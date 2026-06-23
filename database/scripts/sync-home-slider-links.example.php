<?php
/**
 * Run on server after deploy if slider URLs were only set on local DB:
 *   php database/scripts/sync-home-slider-links.example.php
 *
 * Edit $urls below to match your client links (one per slide, same order as images).
 */

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$urls = [
    'https://www.example-client-1.com',
    'https://www.example-client-2.com',
    'https://www.example-client-3.com',
    'https://www.example-client-4.com',
];

$templateId = 1;
$locales = ['en'];

$update = function (string $content) use ($urls): ?string {
    $blocks = json_decode($content, true);
    if (!is_array($blocks)) {
        return null;
    }
    foreach ($blocks as &$row) {
        if (($row['type'] ?? '') !== 'form_search_all_service') {
            continue;
        }
        $row['model']['slider_links'] = implode("\n", $urls);
        $slides = $row['model']['list_slider'] ?? [];
        if (is_string($slides)) {
            $slides = json_decode($slides, true) ?: [];
        }
        foreach ($slides as $i => &$slide) {
            if (isset($urls[$i])) {
                $slide['link_url'] = $urls[$i];
            }
        }
        $row['model']['list_slider'] = $slides;
        break;
    }
    unset($row, $slide);

    return json_encode($blocks);
};

$template = \Modules\Template\Models\Template::find($templateId);
if (!$template) {
    fwrite(STDERR, "Template {$templateId} not found.\n");
    exit(1);
}

$newContent = $update($template->content);
if ($newContent) {
    $template->content = $newContent;
    $template->save();
    echo "Updated core_templates id={$templateId}\n";
}

foreach ($locales as $locale) {
    $tr = $template->translate($locale);
    if (!$tr) {
        echo "Skip locale {$locale} (no translation)\n";
        continue;
    }
    $new = $update($tr->content);
    if ($new) {
        $tr->content = $new;
        $tr->save();
        echo "Updated core_template_translations locale={$locale}\n";
    }
}

echo "Done.\n";
