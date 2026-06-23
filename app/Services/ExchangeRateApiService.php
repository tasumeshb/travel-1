<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateApiService
{
    protected string $mainCurrency;

    /** @var array<string, float> */
    protected array $conversionRates = [];

    protected string $apiBase;

    public function __construct(?string $mainCurrency = null)
    {
        $this->mainCurrency = strtoupper($mainCurrency ?: (string) setting_item('currency_main', 'USD'));
    }

    /**
     * @return array{success: bool, message: string, rates?: array<string, float>, api_base?: string}
     */
    public function fetchRates(): array
    {
        if (!config('currency.exchangerate_api.enabled')) {
            return ['success' => false, 'message' => 'Exchange rate sync is disabled (EXCHANGERATE_SYNC_ENABLED).'];
        }

        $response = $this->requestLatest($this->mainCurrency);
        if ($response['success']) {
            return $response;
        }

        $fallback = strtoupper((string) config('currency.exchangerate_api.fallback_base', 'USD'));
        if ($fallback !== $this->mainCurrency) {
            $response = $this->requestLatest($fallback);
            if ($response['success']) {
                return $response;
            }
        }

        return $response;
    }

    /**
     * Rate stored in admin extra_currency (display = price_main / rate).
     */
    public function adminRateFor(string $extraCurrency): ?float
    {
        $extra = strtoupper($extraCurrency);
        $main = $this->mainCurrency;

        if ($extra === $main || empty($this->conversionRates)) {
            return null;
        }

        if (!isset($this->conversionRates[$extra])) {
            return null;
        }

        $rate = null;

        if ($this->apiBase === $main) {
            $apiRate = (float) $this->conversionRates[$extra];
            if ($apiRate <= 0) {
                return null;
            }
            $rate = 1 / $apiRate;
        } else {
            if (!isset($this->conversionRates[$main])) {
                return null;
            }

            $mainPerBase = (float) $this->conversionRates[$main];
            $extraPerBase = (float) $this->conversionRates[$extra];

            if ($mainPerBase <= 0 || $extraPerBase <= 0) {
                return null;
            }

            $rate = $mainPerBase / $extraPerBase;
        }

        return $this->applyInrPerUsdOverride($main, $extra, $rate);
    }

    /**
     * Human-readable units of extra currency per 1 unit of main (for logging).
     */
    public function displayUnitsPerMain(string $extraCurrency, float $adminRate): ?float
    {
        if ($adminRate <= 0) {
            return null;
        }

        return $adminRate >= 1
            ? $adminRate
            : (1 / $adminRate);
    }

    /**
     * Admin rate from EXCHANGERATE_INR_PER_USD only (no API).
     */
    public static function configuredOverrideRate(string $mainCurrency, string $extraCurrency): ?float
    {
        $main = strtoupper($mainCurrency);
        $extra = strtoupper($extraCurrency);
        $inrPerUsd = config('currency.exchangerate_api.inr_per_usd');

        if (empty($inrPerUsd) || $inrPerUsd <= 0) {
            return null;
        }

        if ($main === 'USD' && $extra === 'INR') {
            return 1 / (float) $inrPerUsd;
        }

        if ($main === 'INR' && $extra === 'USD') {
            return (float) $inrPerUsd;
        }

        return null;
    }

    protected function applyInrPerUsdOverride(string $main, string $extra, float $rate): float
    {
        $override = self::configuredOverrideRate($main, $extra);

        return $override ?? $rate;
    }

    public function getMainCurrency(): string
    {
        return $this->mainCurrency;
    }

    /**
     * @return array{success: bool, message: string, rates?: array<string, float>, api_base?: string}
     */
    protected function requestLatest(string $baseCurrency): array
    {
        $base = strtoupper($baseCurrency);

        try {
            $url = $this->buildUrl($base);
        } catch (\RuntimeException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        try {
            $response = Http::timeout((int) config('currency.exchangerate_api.timeout', 15))
                ->acceptJson()
                ->get($url);
        } catch (\Throwable $e) {
            Log::warning('ExchangeRate-API request failed: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Could not reach ExchangeRate-API: ' . $e->getMessage()];
        }

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => 'ExchangeRate-API HTTP ' . $response->status(),
            ];
        }

        $data = $response->json();
        $result = $data['result'] ?? null;

        if ($result !== 'success') {
            $error = $data['error-type'] ?? 'unknown';

            return [
                'success' => false,
                'message' => 'ExchangeRate-API error: ' . $error,
            ];
        }

        $rates = $data['conversion_rates'] ?? $data['rates'] ?? null;
        if (!is_array($rates) || empty($rates)) {
            return ['success' => false, 'message' => 'ExchangeRate-API returned no conversion rates.'];
        }

        $this->apiBase = (string) ($data['base_code'] ?? $base);
        $this->conversionRates = [];
        foreach ($rates as $code => $value) {
            $this->conversionRates[strtoupper((string) $code)] = (float) $value;
        }

        return [
            'success' => true,
            'message' => 'OK',
            'rates' => $this->conversionRates,
            'api_base' => $this->apiBase,
        ];
    }

    protected function buildUrl(string $baseCurrency): string
    {
        $key = config('currency.exchangerate_api.key');
        $base = strtoupper($baseCurrency);

        if (!empty($key)) {
            $root = rtrim((string) config('currency.exchangerate_api.base_url'), '/');

            return $root . '/' . $key . '/latest/' . $base;
        }

        if (config('currency.exchangerate_api.use_open_when_no_key')) {
            $root = rtrim((string) config('currency.exchangerate_api.open_url'), '/');

            return $root . '/latest/' . $base;
        }

        throw new \RuntimeException('EXCHANGERATE_API_KEY is not set. Add a key or set EXCHANGERATE_USE_OPEN_API=true.');
    }
}
