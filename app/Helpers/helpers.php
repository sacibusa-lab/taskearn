<?php

use App\Models\AdminSetting;

if (!function_exists('currency')) {
    /**
     * Format an amount with the configured currency symbol and exchange rate.
     */
    function currency(mixed $amount, bool $convert = true): string
    {
        $value = $convert ? AdminSetting::convert($amount) : (float) $amount;
        return AdminSetting::currency($value);
    }
}

if (!function_exists('currency_raw')) {
    /**
     * Format a raw amount with just the currency symbol (no conversion).
     */
    function currency_raw(mixed $amount): string
    {
        return AdminSetting::currency($amount);
    }
}

if (!function_exists('currency_value')) {
    /**
     * Get the numeric converted value.
     */
    function currency_value(mixed $amount): float
    {
        return AdminSetting::convert($amount);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get the current currency symbol.
     */
    function currency_symbol(): string
    {
        return AdminSetting::getValue('currency_symbol', '₦');
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get the current currency code.
     */
    function currency_code(): string
    {
        return AdminSetting::getValue('currency_code', 'NGN');
    }
}
