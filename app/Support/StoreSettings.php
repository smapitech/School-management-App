<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class StoreSettings
{
    public static function all(): array
    {
        try {
            if (Schema::hasTable('settings')) {
                return Cache::remember('store_settings', 300, fn () => Setting::pluck('value', 'key')->all());
            }
        } catch (Throwable) {
            return [];
        }

        return [];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::all();
        $value = $settings[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default ? '1' : '0');

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function payment(string $provider, string $key, mixed $default = null): mixed
    {
        // Payment credentials must be read directly from the database. Keeping them in the
        // shared five-minute settings cache can make checkout hide a gateway immediately
        // after an administrator saves or replaces a key.
        $value = self::freshValue("{$provider}_{$key}");
        $value = self::normalizePaymentValue($value);
        $default = self::normalizePaymentValue($default);

        // Never allow dummy/test placeholder values from the settings table to override real .env keys.
        if (! self::looksLikePlaceholderPaymentValue($provider, $key, $value)) {
            return $value;
        }

        // The .env fallback must pass the same checks; otherwise a placeholder key can expose a
        // gateway at checkout and fail only after the customer has already created an order.
        return self::looksLikePlaceholderPaymentValue($provider, $key, $default)
            ? null
            : $default;
    }

    public static function paymentEnabled(string $provider): bool
    {
        if (! in_array($provider, ['paystack', 'stripe'], true)) {
            return false;
        }

        if (! self::freshBool('online_payment_enabled', true)) {
            return false;
        }

        $configuredByDefault = self::paymentConfigured($provider);

        return self::freshBool("{$provider}_enabled", $configuredByDefault);
    }

    public static function paymentConfigured(string $provider): bool
    {
        if (! in_array($provider, ['paystack', 'stripe'], true)) {
            return false;
        }

        $publicKey = self::payment(
            $provider,
            'public_key',
            config("commerce.payments.{$provider}.public_key")
        );
        $secretKey = self::payment(
            $provider,
            'secret_key',
            config("commerce.payments.{$provider}.secret_key")
        );

        if (! self::paymentKeyValid($provider, 'public_key', $publicKey)
            || ! self::paymentKeyValid($provider, 'secret_key', $secretKey)) {
            return false;
        }

        return self::paymentMode($publicKey) === self::paymentMode($secretKey);
    }

    public static function enabledPaymentMethods(): array
    {
        $methods = [];

        if (self::paymentEnabled('paystack') && self::paymentConfigured('paystack')) {
            $methods['paystack'] = 'Paystack';
        }

        if (self::paymentEnabled('stripe') && self::paymentConfigured('stripe')) {
            $methods['stripe'] = 'Stripe';
        }

        return $methods;
    }

    public static function paymentKeyValid(string $provider, string $key, mixed $value): bool
    {
        return ! self::looksLikePlaceholderPaymentValue($provider, $key, $value);
    }

    private static function looksLikePlaceholderPaymentValue(string $provider, string $key, mixed $value): bool
    {
        if (! filled($value) || ! is_string($value)) {
            return true;
        }

        $trimmed = trim($value);
        $lower = strtolower($trimmed);

        if (in_array($lower, ['password', 'password123', 'password123!', 'secret', 'test', 'null', 'none', 'your_key_here', 'your-secret-key'], true)) {
            return true;
        }

        if (strlen($trimmed) < 20
            || preg_match('/\s/u', $trimmed) === 1
            || str_contains($trimmed, '...')
            || str_contains($trimmed, '*')
            || str_contains($trimmed, '•')) {
            return true;
        }

        if ($provider === 'paystack') {
            $prefixes = $key === 'secret_key'
                ? ['sk_test_', 'sk_live_']
                : ['pk_test_', 'pk_live_'];

            return ! self::startsWithAny($trimmed, $prefixes);
        }

        if ($provider === 'stripe') {
            $prefixes = $key === 'secret_key'
                ? ['sk_test_', 'sk_live_', 'rk_test_', 'rk_live_']
                : ['pk_test_', 'pk_live_'];

            return ! self::startsWithAny($trimmed, $prefixes);
        }

        return false;
    }

    private static function freshValue(string $key): mixed
    {
        try {
            if (Schema::hasTable('settings')) {
                return Setting::query()->where('key', $key)->value('value');
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }

    private static function freshBool(string $key, bool $default = false): bool
    {
        $value = self::freshValue($key);

        if (! filled($value)) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private static function normalizePaymentValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];

            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = trim(substr($value, 1, -1));
            }
        }

        return $value;
    }

    private static function startsWithAny(string $value, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($value, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private static function paymentMode(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        if (str_contains($value, '_live_')) {
            return 'live';
        }

        if (str_contains($value, '_test_')) {
            return 'test';
        }

        return null;
    }
}
