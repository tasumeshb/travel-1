<div class="effect">
    <div class="owl-carousel">
        @foreach($list_slider as $item)
            @php
                $img = get_file_url($item['bg_image'] ?? null, 'full');
                $sliderLink = \Modules\Template\Blocks\FormSearchAllService::slideLinkFromItem($item);
                $sliderStyle = "background-image: linear-gradient(0deg,rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.2)),url('{$img}') !important";
                $sliderExternal = \Modules\Template\Blocks\FormSearchAllService::isExternalSlideUrl($sliderLink);
            @endphp
            <div class="item owl-home-slide" style="{{ $sliderStyle }}"@if($sliderLink !== '') data-slide-url="{{ e($sliderLink) }}"@endif>
                @if($sliderLink !== '')
                    <a href="{{ e($sliderLink) }}" class="bravo-slider-hit-area"
                       @if($sliderExternal) target="_blank" rel="noopener noreferrer" @endif
                       aria-label="{{ __('Open slide link') }}"></a>
                @endif
                <div class="owl-slide-caption">
                    <h2 class="sub-heading text-center">{{ $item['desc'] ?? "" }}</h2>
                    <h1 class="text-heading text-center">{{ $item['title'] ?? "" }}</h1>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="container bravo-home-search-container">
    <div class="row">
        <div class="col-lg-12">
            @include("Template::frontend.blocks.form-search-all-service.form-search")
        </div>
    </div>
</div>
