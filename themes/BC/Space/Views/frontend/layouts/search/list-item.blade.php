<div class="row">
    <div class="col-lg-3 col-md-12">
        @include('Space::frontend.layouts.search.filter-search')
    </div>
    <div class="col-lg-9 col-md-12">
        <div class="bravo-list-item">
            <div class="topbar-search">
                <h2 class="text">
                    @if($rows->total() > 1)
                        {{ __(":count agents found",['count'=>$rows->total()]) }}
                    @else
                        {{ __(":count agent found",['count'=>$rows->total()]) }}
                    @endif
                </h2>
                <div class="control bc-form-order">
                    @include('Layout::global.search.orderby',['routeName'=>'space.search'])
                </div>
            </div>

            <div class="ajax-search-result">
                @include('Space::frontend.ajax.search-result')
            </div>
        </div>
    </div>
</div>
