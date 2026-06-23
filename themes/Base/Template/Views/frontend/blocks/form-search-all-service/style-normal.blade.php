@if(!empty($style) and $style == "carousel" and !empty($list_slider))
    <div class="effect">
        <div class="owl-carousel">
            @foreach($list_slider as $item)
                @php
                    $img = get_file_url($item['bg_image'] ?? null, 'full');
                    $sliderLink = \Modules\Template\Blocks\FormSearchAllService::slideLinkFromItem($item);
                    $sliderExternal = \Modules\Template\Blocks\FormSearchAllService::isExternalSlideUrl($sliderLink);
                @endphp
                <div class="item owl-home-slide"@if($sliderLink !== '') data-slide-url="{{ e($sliderLink) }}"@endif>
                    @if($sliderLink !== '')
                        <a href="{{ e($sliderLink) }}" class="bravo-slider-hit-area"
                           @if($sliderExternal) target="_blank" rel="noopener noreferrer" @endif
                           aria-label="{{ __('Open slide link') }}"></a>
                    @endif
                    <div class="item-bg" style="background-image: linear-gradient(0deg,rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.2)),url('{{ $img }}') !important"></div>
                </div>
            @endforeach
        </div>
    </div>
@endif
<div class="container bravo-home-search-container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="text-heading">{{$title}}</h1>
            <div class="sub-heading">{{$sub_title}}</div>
            @include("Template::frontend.blocks.form-search-all-service.form-search")
        </div>
    </div>
</div>
