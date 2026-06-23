@php
    // _active is an admin UI flag (expand/collapse), not a publish toggle.
    $activeItems = collect($list_item ?? [])->filter(function ($item) {
        return !empty($item['title']) || !empty($item['background_image']);
    })->values();
@endphp
@if($activeItems->isNotEmpty())
    <div class="bravo-offer">
        <div class="container">
            <div class="row">
                @foreach($activeItems as $key => $item)
                    @php $size = ($key === 0) ? 6 : 3; @endphp
                    <div class="col-lg-{{ $size }}">
                        <div class="item">
                            @if(!empty($item['featured_text']))
                                <div class="featured-text">{{ $item['featured_text'] }}</div>
                            @endif
                            @if(!empty($item['featured_icon']))
                                <div class="featured-icon"><i class="{{ $item['featured_icon'] }}"></i></div>
                            @endif
                            <h2 class="item-title">{{ $item['title'] ?? '' }}</h2>
                            <p class="item-sub-title">{!! $item['desc'] ?? '' !!}</p>
                            @if(!empty($item['link_more']) && !empty($item['link_title']))
                                <a href="{{ $item['link_more'] }}" class="btn btn-default">{{ $item['link_title'] }}</a>
                            @endif
                            <div class="img-cover" style="background: url('{{ get_file_url($item['background_image'] ?? null, 'full') ?? '' }}')"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif