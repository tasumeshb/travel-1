<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Template\Blocks\FormSearchAllService;
use Modules\Template\Models\Template;

class SyncHomeSliderLinksCommand extends Command
{
    protected $signature = 'slider:sync-links
        {urls?* : One URL per slide, in the same order as slider images}
        {--template=1 : Homepage template ID}
        {--file= : Text file with one URL per line}
        {--locale=* : Translation locales to update (default: en)}';

    protected $description = 'Save homepage slider redirect URLs into the template (run on live after setting links locally)';

    public function handle(): int
    {
        $urls = $this->argument('urls');
        if ($file = $this->option('file')) {
            if (!is_readable($file)) {
                $this->error("Cannot read file: {$file}");

                return self::FAILURE;
            }
            $lines = preg_split('/\r\n|\r|\n/', (string) file_get_contents($file));
            $urls = array_merge($urls, array_map('trim', $lines));
        }

        $urls = array_values(array_filter(array_map(function ($url) {
            return FormSearchAllService::normalizeSlideUrl(trim((string) $url));
        }, $urls)));

        if (empty($urls)) {
            $this->error('Provide URLs as arguments or --file=paths.txt (one URL per line).');
            $this->line('Examples:');
            $this->line('  php artisan slider:sync-links https://client1.com /tour tour');
            $this->line('  php artisan slider:sync-links --file=slider-urls.txt');

            return self::FAILURE;
        }

        $templateId = (int) $this->option('template');
        $template = Template::find($templateId);
        if (!$template) {
            $this->error("Template id={$templateId} not found.");

            return self::FAILURE;
        }

        $locales = $this->option('locale') ?: ['en'];

        $update = function (string $content) use ($urls): ?string {
            $blocks = json_decode($content, true);
            if (!is_array($blocks)) {
                return null;
            }
            $found = false;
            foreach ($blocks as &$row) {
                if (($row['type'] ?? '') !== 'form_search_all_service') {
                    continue;
                }
                $found = true;
                $row['model']['slider_links'] = implode("\n", $urls);
                $row['model'] = FormSearchAllService::syncSliderLinksInModel($row['model']);
                break;
            }
            unset($row);

            if (!$found) {
                return null;
            }

            return json_encode($blocks);
        };

        $newContent = $update($template->content);
        if (!$newContent) {
            $this->error('No form_search_all_service block found in template.');

            return self::FAILURE;
        }

        $template->content = $newContent;
        $template->save();
        $this->info("Updated core_templates id={$templateId}");

        foreach ($locales as $locale) {
            $tr = $template->translate($locale);
            if (!$tr) {
                $this->warn("Skip locale {$locale} (no translation row).");
                continue;
            }
            $new = $update($tr->content);
            if ($new) {
                $tr->content = $new;
                $tr->save();
                $this->info("Updated translation locale={$locale}");
            }
        }

        foreach ($urls as $i => $url) {
            $this->line(sprintf('  Slide %d → %s', $i + 1, $url));
        }

        $this->newLine();
        $this->comment('Run on live: php artisan cache:clear && php artisan view:clear');

        return self::SUCCESS;
    }
}
