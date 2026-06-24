<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'number' => (float) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function setValue(string $key, $value, string $type = 'string', string $group = 'general', ?string $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    /**
     * Format a number with the current currency symbol.
     */
    public static function currency(mixed $amount): string
    {
        $symbol = static::getValue('currency_symbol', '₦');
        return $symbol . number_format((float) $amount, 2);
    }

    /**
     * Convert an amount from base currency to the configured currency using exchange rate.
     */
    public static function convert(mixed $amount): float
    {
        $rate = (float) static::getValue('exchange_rate', 1);
        return (float) $amount * $rate;
    }

    /**
     * Format amount in the local currency after exchange rate conversion.
     */
    public static function format(mixed $amount): string
    {
        return static::currency(static::convert($amount));
    }
}
