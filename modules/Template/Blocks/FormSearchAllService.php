<?php
namespace Modules\Template\Blocks;

use Modules\Flight\Models\SeatType;
use Modules\Template\Blocks\BaseBlock;
use Modules\Location\Models\Location;
use Modules\Media\Helpers\FileHelper;

class FormSearchAllService extends BaseBlock
{
    public function getName()
    {
        return __('Form Search All Service');
    }

    public function getOptions()
    {
        $list_service = [];
        foreach (get_bookable_services() as $key => $service) {
            $list_service[] = ['value'   => $key,
                'name' => ucwords($key)
            ];
            $arg[] = [
                'id'        => 'title_for_'.$key,
                'type'      => 'input',
                'inputType' => 'text',
                'label'     => __('Title for :service',['service'=>ucwords($key)])
            ];
        }
        $arg[] = [
            'id'            => 'service_types',
            'type'          => 'checklist',
            'listBox'          => 'true',
            'label'         => "<strong>".__('Service Type')."</strong>",
            'values'        => $list_service,
        ];

        $arg[] = [
            'id'        => 'title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Title')
        ];
        $arg[] = [
            'id'        => 'sub_title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Sub Title')
        ];

        $arg[] =  [
            'id'            => 'style',
            'type'          => 'radios',
            'label'         => __('Style Background'),
            'values'        => [
                [
                    'value'   => '',
                    'name' => __("Normal")
                ],
                [
                    'value'   => 'carousel',
                    'name' => __("Slider Carousel")
                ],
                [
                    'value'   => 'carousel_v2',
                    'name' => __("Slider Carousel Ver 2")
                ],
                [
                    'value'   => 'bg_video',
                    'name' => __("Background Video")
                ],
            ]
        ];

        $arg[] = [
            'id'    => 'bg_image',
            'type'  => 'uploader',
            'label' => __('- Layout Normal: Background Image Uploader')
        ];

        $arg[] = [
            'id'        => 'video_url',
            'type'      => 'input',
            'inputType' => 'text',
            'label' => __('- Layout Video: Youtube Url')
        ];

        $arg[] = [
            'id'        => 'slider_links',
            'type'      => 'textArea',
            'inputType' => 'textArea',
            'label'     => __('Bulk slide URLs (optional, one per line)'),
            'help'      => __('One URL per line (slide 1, 2, 3…). Saving this overwrites per-slide URLs for those lines. Clear this box to use only “Client website URL” on each slide.'),
        ];

        $arg[] = [
            'id'          => 'list_slider',
            'type'        => 'listItem',
            'label'       => __('- Layout Slider: Client / partner banners'),
            'title_field' => 'title',
            'settings'    => [
                [
                    'id'        => 'title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Title (optional, slider ver 2)')
                ],
                [
                    'id'        => 'desc',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Description (optional, slider ver 2)')
                ],
                [
                    'id'    => 'bg_image',
                    'type'  => 'uploader',
                    'label' => __('Client banner image')
                ],
                [
                    'id'        => 'link_url',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Client website URL'),
                    'help'      => __('Full URL, e.g. https://www.client.com — opens in a new tab when clicked.'),
                ]
            ]
        ];

        $arg[] = [
            'type'=> "checkbox",
            'label'=>__("Hide form search service?"),
            'id'=> "hide_form_search",
            'default'=>false
        ];

        return [
            'settings' => $arg,
            'category'=>__("Other Block")
        ];
    }

    public function content($model = [])
    {
        $model['bg_image_url'] = FileHelper::url($model['bg_image'] ?? "", 'full') ?? "";
        $model['list_location'] = $model['tour_location'] =  Location::where("status","publish")->limit(1000)->orderBy('name', 'asc')->with(['translation'])->get()->toTree();
        $model['style'] = $model['style'] ?? "";
        $model = self::syncSliderLinksInModel($model);
        $model['modelBlock'] = $model;
        $model['seatType'] =  SeatType::get();
        return $this->view('Template::frontend.blocks.form-search-all-service.index', $model);
    }

    public function contentAPI($model = []){
        if (!empty($model['bg_image'])) {
            $model['bg_image_url'] = FileHelper::url($model['bg_image'], 'full');
        }
        return $model;
    }

    /**
     * Decode list_slider from JSON string / objects into a plain array of slides.
     */
    public static function normalizeListSlider($listSlider): array
    {
        if (is_string($listSlider)) {
            $decoded = json_decode($listSlider, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            $listSlider = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($listSlider)) {
            return [];
        }

        $slides = [];
        foreach ($listSlider as $slide) {
            if (is_object($slide)) {
                $slide = (array) $slide;
            }
            if (is_array($slide)) {
                $slides[] = $slide;
            }
        }

        return $slides;
    }

    /**
     * Read redirect URL from a slide item (admin may use link_url, url, or link).
     */
    public static function slideLinkFromItem($slide): string
    {
        if (is_object($slide)) {
            $slide = (array) $slide;
        }
        if (!is_array($slide)) {
            return '';
        }

        foreach (['link_url', 'url', 'link', 'client_website_url', 'website_url'] as $key) {
            $val = trim((string) ($slide[$key] ?? ''));
            if ($val !== '') {
                return self::normalizeSlideUrl($val);
            }
        }

        return '';
    }

    /**
     * Merge bulk slider_links + per-slide link_url (used on frontend and when saving template).
     */
    public static function syncSliderLinksInModel(array $model): array
    {
        $slides = self::normalizeListSlider($model['list_slider'] ?? []);
        $lines = preg_split('/\r\n|\r|\n/', (string) ($model['slider_links'] ?? ''));

        foreach ($slides as $index => &$slide) {
            if (is_object($slide)) {
                $slide = (array) $slide;
            }
            if (!is_array($slide)) {
                continue;
            }

            $lineUrl = trim($lines[$index] ?? '');
            $perSlide = self::slideLinkFromItem($slide);

            // Bulk line wins when set (admin "Bulk slide URLs" replaces old per-slide values)
            if ($lineUrl !== '') {
                $slide['link_url'] = self::normalizeSlideUrl($lineUrl);
            } elseif ($perSlide !== '') {
                $slide['link_url'] = $perSlide;
            } else {
                unset($slide['link_url']);
            }
        }
        unset($slide);

        $model['list_slider'] = $slides;
        $model['slider_links'] = implode("\n", array_map(function ($slide) {
            return trim((string) ($slide['link_url'] ?? ''));
        }, $slides));

        return $model;
    }

    /**
     * @deprecated Use syncSliderLinksInModel()
     */
    protected static function applySliderLinks(array $slides, $sliderLinksText): array
    {
        return self::syncSliderLinksInModel([
            'list_slider'  => $slides,
            'slider_links' => $sliderLinksText,
        ])['list_slider'];
    }

    /**
     * Normalize slide link: keep internal paths, add https:// for client domains.
     */
    public static function normalizeSlideUrl($url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }
        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }
        if ($url[0] === '/') {
            return $url;
        }
        if (preg_match('#^(mailto:|tel:)#i', $url)) {
            return $url;
        }
        // domain.com/path → external; tour or tour/foo → internal page on this site
        if (preg_match('#^[a-z0-9][a-z0-9.-]*\.[a-z]{2,}(/|$)#i', $url)) {
            return 'https://' . $url;
        }

        return '/' . ltrim($url, '/');
    }

    public static function isExternalSlideUrl($url): bool
    {
        $url = trim((string) $url);
        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        if ($host && $appHost && strcasecmp($host, $appHost) === 0) {
            return false;
        }

        return true;
    }
}
