<?php

namespace App\Helpers;

use App\Models\AdminSetting;

class CurrencyHelper
{
    /**
     * Format an amount with the configured currency symbol.
     * Uses exchange rate to convert from base USD.
     */
    public static function format(mixed $amount, bool $convert = true): string
    {
        $value = $convert ? AdminSetting::convert($amount) : (float) $amount;
        return AdminSetting::currency($value);
    }

    /**
     * Format without exchange rate conversion (raw amount with symbol).
     */
    public static function raw(mixed $amount): string
    {
        return AdminSetting::currency($amount);
    }

    /**
     * Just the numeric converted value.
     */
    public static function value(mixed $amount): float
    {
        return AdminSetting::convert($amount);
    }

    /**
     * Get currency symbol.
     */
    public static function symbol(): string
    {
        return AdminSetting::getValue('currency_symbol', '$');
    }

    /**
     * Get currency code.
     */
    public static function code(): string
    {
        return AdminSetting::getValue('currency_code', 'USD');
    }
}
