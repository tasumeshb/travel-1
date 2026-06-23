@php
    $homeSearchMapId = 'map-home-' . \Illuminate\Support\Str::random(8);
    $initialCategory = request()->query('search_category', 'tourpackage');
    $validCategories = ['tourpackage', 'touragnt', 'tourvehicle', 'stay', 'event', 'boat', 'touritinerary'];
    if (!in_array($initialCategory, $validCategories, true)) {
        $initialCategory = 'tourpackage';
    }
@endphp
<style>
  #g-form-control-id {
    top: 21px;
  }
  @media (max-width: 767px) {
    #g-form-control-id {
      top: 120px !important;
    }
  }
  @media (max-width: 1023px) {
    #searchForm.is-stay-search.bravo_form {
      display: flex;
      flex-direction: column;
      flex-wrap: nowrap;
    }
    #searchForm.is-stay-search .g-field-search {
      flex: 0 0 100% !important;
      max-width: 100% !important;
      width: 100%;
      padding: 0 15px;
    }
    #searchForm.is-stay-search .g-field-search > .row {
      display: flex;
      flex-direction: column;
      flex-wrap: nowrap;
      margin: 0;
    }
    #searchForm.is-stay-search .g-field-search > .row > [class*="col-"] {
      flex: 0 0 100% !important;
      max-width: 100% !important;
      width: 100%;
      padding: 0;
      border-right: none !important;
    }
    #searchForm.is-stay-search .home-search-category {
      order: 0;
    }
    #searchForm.is-stay-search .home-search-list-name {
      order: 1;
    }
    #searchForm.is-stay-search .stay-field {
      order: 2;
    }
    #searchForm.is-stay-search .form-group {
      position: relative;
      border-bottom: 1px solid #D7DCE3 !important;
    }
    #searchForm.is-stay-search .form-content {
      padding: 16px 10px 12px 44px;
    }
    #searchForm.is-stay-search .field-icon {
      left: 10px;
      font-size: 28px;
      margin-top: -14px;
    }
    #searchForm.is-stay-search label {
      font-size: 13px;
      display: block;
      margin-bottom: 4px;
    }
    #searchForm.is-stay-search .form-control {
      font-size: 15px;
      padding: 0 !important;
      height: auto;
      min-height: 22px;
    }
    #searchForm.is-stay-search .g-button-submit {
      flex: 0 0 100% !important;
      max-width: 100% !important;
      width: 100%;
      text-align: right;
      padding: 0 15px 12px;
    }
    #searchForm.is-stay-search .g-button-submit .btn-search {
      display: inline-block;
      width: auto;
      height: auto;
      margin: 8px 0 0;
      border-radius: 5px;
      padding: 9px 22px;
      font-size: 14px;
    }
  }
  #homeSearchSuggestions.suggestions-list {
    border: 1px solid #ccc;
    max-height: 200px;
    overflow-y: auto;
    position: absolute;
    z-index: 1000;
    background: #fff;
    width: 100%;
    left: 0;
    top: 100%;
  }
  #homeSearchSuggestions .suggestion-item {
    padding: 10px;
    cursor: pointer;
  }
  #homeSearchSuggestions .suggestion-item:hover {
    background-color: #f0f0f0;
  }
</style>
<div class="g-form-control" id="g-form-control-id" style="position:relative !important;">
    <ul class="nav nav-tabs" role="tablist"></ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active">
            <form id="searchForm" action="{{ route('tour.search') }}" class="form bravo_form" method="get">
                <div class="g-field-search">
                    <div class="row">
                        <div class="col-md-3 border-right home-search-category">
                            <div class="form-group">
                                <i class="field-icon icofont-travelling"></i>
                                <div class="form-content">
                                    <label>{{ __('Category List') }}</label>
                                    <div class="input-search">
                                        <select class="form-control" style="border:none;" id="selectcategoryid">
                                            <option value="tourpackage" @if($initialCategory === 'tourpackage') selected @endif>{{ __('Tour Packages') }}</option>
                                            <option value="touragnt" @if($initialCategory === 'touragnt') selected @endif>{{ __('Travel Agent') }}</option>
                                            <option value="tourvehicle" @if($initialCategory === 'tourvehicle') selected @endif>{{ __('Tourist Vehicle') }}</option>
                                            <option value="stay" @if($initialCategory === 'stay') selected @endif>{{ __('Stay List') }}</option>
                                            <option value="event" @if($initialCategory === 'event') selected @endif>{{ __('Event & Park') }}</option>
                                            <option value="boat" @if($initialCategory === 'boat') selected @endif>{{ __('Boat & Cruise') }}</option>
                                            <option value="touritinerary" @if($initialCategory === 'touritinerary') selected @endif>{{ __('Tour Itinerary') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 border-right tour-field">
                            <div class="form-group">
                                <i class="field-icon fa icofont-map"></i>
                                <div class="form-content">
                                    <label>{{ __('Destination') }}</label>
                                    <div class="input-search">
                                        <input type="text" name="destination" class="form-control border-0 tour-only-input"
                                               placeholder="{{ __('Your destination?') }}" value="{{ request()->input('destination') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 border-right tour-field">
                            <div class="form-group">
                                <i class="field-icon fa icofont-map"></i>
                                <div class="form-content">
                                    <label>{{ __('Departure') }}</label>
                                    <div class="input-search">
                                        <input type="text" name="departure" class="form-control border-0 tour-only-input"
                                               placeholder="{{ __('Your departure?') }}" value="{{ request()->input('departure') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 border-right stay-field" style="display:none;">
                            <div class="form-group">
                                <i class="field-icon fa icofont-map"></i>
                                <div class="form-content">
                                    <label>{{ __('Location') }}</label>
                                    <div class="input-search g-map-place">
                                        <input type="text" name="map_place" class="form-control border-0 stay-only-input pac-target-input"
                                               placeholder="{{ __('Where are you going?') }}" value="{{ request()->input('map_place') }}" autocomplete="off">
                                        <div class="map d-none" id="{{ $homeSearchMapId }}"></div>
                                        <input type="hidden" name="map_lat" class="stay-only-input" value="{{ request()->input('map_lat') }}">
                                        <input type="hidden" name="map_lgn" class="stay-only-input" value="{{ request()->input('map_lgn') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 border-right home-search-list-name">
                            <div class="form-group">
                                <i class="field-icon fa icofont-search"></i>
                                <div class="form-content">
                                    <label id="homeServiceNameLabel">{{ __('List Name') }}</label>
                                    <div class="input-search" style="position:relative;">
                                        <input type="text" id="homeServiceName" name="service_name" class="form-control"
                                               placeholder="{{ __('Search for...') }}" value="{{ request()->input('service_name') }}" autocomplete="off">
                                        <div id="homeSearchSuggestions" class="suggestions-list"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="g-button-submit">
                    <button class="btn btn-primary btn-search" type="submit">{{ __('Search') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
jQuery(function ($) {
    var searchRoutes = {
        tourpackage: '{{ route("tour.search") }}',
        touragnt: '{{ route("space.search") }}',
        tourvehicle: '{{ route("car.search") }}',
        stay: '{{ route("hotel.search") }}',
        event: '{{ route("event.search") }}',
        boat: '{{ route("boat.search") }}',
        touritinerary: '{{ route("tour.search") }}'
    };

    var suggestRoutes = {
        tourpackage: '{{ route("tourservices.search") }}',
        touragnt: '{{ route("spaceservices.search") }}',
        tourvehicle: '{{ route("carservices.search") }}',
        stay: '{{ route("hotelservices.search") }}',
        event: '{{ route("eventservices.search") }}',
        boat: '{{ route("boatservices.search") }}',
        touritinerary: '{{ route("tourservices.search") }}'
    };

    function applyCategory(category) {
        var $form = $('#searchForm');
        $form.attr('action', searchRoutes[category] || searchRoutes.tourpackage);

        var isStay = category === 'stay';
        $('.stay-field').toggle(isStay);
        $('.tour-field').toggle(!isStay);
        $form.toggleClass('is-stay-search', isStay);
        $('.tour-only-input').prop('disabled', isStay);
        $('.stay-only-input').prop('disabled', !isStay);

        $('#homeServiceNameLabel').text(isStay ? '{{ __("Hotel / Stay Name") }}' : '{{ __("List Name") }}');
        $('#homeSearchSuggestions').empty();
    }

    $('#selectcategoryid').on('change', function () {
        applyCategory(this.value);
    });

    applyCategory($('#selectcategoryid').val());

    var suggestXhr = null;

    $('#homeServiceName').on('keyup', function () {
        var query = $.trim($(this).val());
        var category = $('#selectcategoryid').val();
        var url = suggestRoutes[category];
        var $box = $('#homeSearchSuggestions');

        if (!url || query.length < 1) {
            $box.empty();
            return;
        }

        if (suggestXhr) {
            suggestXhr.abort();
        }

        suggestXhr = $.get(url, { query: query })
            .done(function (data) {
                $box.empty();
                if (data && data.length) {
                    data.forEach(function (service) {
                        if (service && service.title) {
                            $box.append($('<div class="suggestion-item"></div>').text(service.title));
                        }
                    });
                }
            })
            .fail(function () {
                $box.empty();
            });
    });

    $(document).on('click', '#homeSearchSuggestions .suggestion-item', function () {
        $('#homeServiceName').val($(this).text());
        $('#homeSearchSuggestions').empty();
    });
});
</script>
@endpush
