<?php

namespace App\Services;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = AdminSetting::getValue('paystack_secret_key', '');
        $this->publicKey = AdminSetting::getValue('paystack_public_key', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && !empty($this->publicKey);
    }

    /**
     * Create a Dedicated Virtual Account (DVA) for a user.
     */
    public function createDedicatedAccount(string $email, string $phone, string $name): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('Paystack not configured. Cannot create DVA.');
            return null;
        }

        try {
            $response = Http::withToken($this->secretKey)
                ->post($this->baseUrl . '/dedicated_account', [
                    'email' => $email,
                    'phone' => $phone,
                    'first_name' => $name,
                    'customer' => [
                        'email' => $email,
                        'phone' => $phone,
                        'first_name' => $name,
                    ],
                    'preferred_bank' => 'wema-bank',
                ]);

            if ($response->successful()) {
                $body = $response->json();
                if ($body['status']) {
                    return $body['data'];
                }
            }

            Log::error('Paystack DVA creation failed', [
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Paystack DVA exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify a transaction reference.
     */
    public function verifyTransaction(string $reference): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withToken($this->secretKey)
                ->get($this->baseUrl . "/transaction/verify/{$reference}");

            if ($response->successful()) {
                $body = $response->json();
                return $body['status'] ? $body['data'] : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Paystack verification exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get list of banks from Paystack.
     */
    public function listBanks(): array
    {
        if (!$this->isConfigured()) return [];
        try {
            $response = Http::withToken($this->secretKey)->get($this->baseUrl . '/bank', ['country' => 'nigeria', 'perPage' => 100]);
            if ($response->successful()) {
                $body = $response->json();
                return $body['status'] ? $body['data'] : [];
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Paystack list banks: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Resolve bank account number.
     */
    public function resolveAccount(string $accountNumber, string $bankCode): ?array
    {
        if (!$this->isConfigured()) return null;
        try {
            $response = Http::withToken($this->secretKey)
                ->get($this->baseUrl . "/bank/resolve", [
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                ]);
            if ($response->successful()) {
                $body = $response->json();
                return $body['status'] ? $body['data'] : null;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Paystack resolve account: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initialize a transaction (for deposits).
     */
    public function initializeTransaction(string $email, float $amount, string $reference, ?string $callbackUrl = null): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $currency = AdminSetting::getValue('paystack_currency', 'NGN');

        try {
            $payload = [
                'email' => $email,
                'amount' => (int) ($amount * 100), // Paystack uses kobo/cents
                'reference' => $reference,
                'currency' => $currency,
            ];

            if ($callbackUrl) {
                $payload['callback_url'] = $callbackUrl;
            }

            $response = Http::withToken($this->secretKey)
                ->post($this->baseUrl . '/transaction/initialize', $payload);

            if ($response->successful()) {
                $body = $response->json();
                return $body['status'] ? $body['data'] : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Paystack init exception: ' . $e->getMessage());
            return null;
        }
    }
}
