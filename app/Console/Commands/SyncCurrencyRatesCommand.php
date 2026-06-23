<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateApiService;
use Illuminate\Console\Command;

class SyncCurrencyRatesCommand extends Command
{
    protected $signature = 'currency:sync-rates {--dry-run : Show changes without saving}';

    protected $description = 'Sync extra currency exchange rates from ExchangeRate-API';

    public function handle(): int
    {
        if (!is_installed()) {
            $this->error('Application is not installed yet.');

            return self::FAILURE;
        }

        $main = strtoupper((string) setting_item('currency_main', 'USD'));
        $extraCurrencies = setting_item_array('extra_currency', []);

        if (empty($extraCurrencies)) {
            $this->warn('No extra currencies configured in Admin → Settings → Payment.');

            return self::SUCCESS;
        }

        $service = new ExchangeRateApiService($main);
        $fetch = $service->fetchRates();
        $apiOk = $fetch['success'];

        if ($apiOk) {
            $this->info(sprintf(
                'Fetched rates (API base: %s, main currency: %s).',
                $fetch['api_base'] ?? '?',
                $main
            ));
        } else {
            $this->warn($fetch['message']);
            if (!config('currency.exchangerate_api.inr_per_usd')) {
                $this->line('Existing rates were left unchanged.');

                return self::FAILURE;
            }
            $this->line('Applying configured EXCHANGERATE_INR_PER_USD override(s) only.');
        }

        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;
        $skipped = 0;

        foreach ($extraCurrencies as $index => $item) {
            $code = strtoupper((string) ($item['currency_main'] ?? ''));
            if ($code === '' || $code === $main) {
                $skipped++;
                continue;
            }

            $newRate = $apiOk ? $service->adminRateFor($code) : null;
            if ($newRate === null) {
                $newRate = ExchangeRateApiService::configuredOverrideRate($main, $code);
            }
            if ($newRate === null) {
                $this->warn("  [skip] {$code}: not available from API or overrides.");
                $skipped++;
                continue;
            }

            $newRate = round($newRate, 11);
            $oldRate = isset($item['rate']) ? (float) $item['rate'] : 0;

            $hint = '';
            $units = $service->displayUnitsPerMain($code, $newRate);
            if ($units !== null && $code !== $main) {
                $hint = sprintf(' (1 %s ≈ %s %s)', $main, number_format($units, 4, '.', ''), $code);
            }

            $this->line(sprintf(
                '  %s: %s → %s%s',
                $code,
                $oldRate > 0 ? (string) $oldRate : '(empty)',
                $newRate,
                $hint
            ));

            if (!$dryRun) {
                $extraCurrencies[$index]['rate'] = $newRate;
            }

            $updated++;
        }

        if ($dryRun) {
            $this->info("Dry run: {$updated} rate(s) would be updated, {$skipped} skipped.");

            return self::SUCCESS;
        }

        if ($updated === 0) {
            $this->warn('No rates were updated.');

            return self::SUCCESS;
        }

        setting_update_item('extra_currency', $extraCurrencies);
        setting_update_item('currency_rates_last_sync', now()->toIso8601String());
        if ($apiOk) {
            setting_update_item('currency_rates_api_base', $fetch['api_base'] ?? '');
        }

        $this->info("Updated {$updated} exchange rate(s).");

        return self::SUCCESS;
    }
}
