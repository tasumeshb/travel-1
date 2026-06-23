<form action="{{ route("hotel.search") }}" class="form bravo_form hotel-search-form" method="get">
    <div class="g-field-search">
        <div class="row no-gutters hotel-search-row">
            @php $hotel_search_fields = setting_item_array('hotel_search_fields');
            $hotel_search_fields = array_values(\Illuminate\Support\Arr::sort($hotel_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
            @endphp
            @if(!empty($hotel_search_fields))
                @foreach($hotel_search_fields as $field)
                    @php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" @endphp
                    <div class="col-md-{{ $field['size'] ?? "6" }} border-right hotel-search-field hotel-search-field-{{ $field['field'] }}">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Hotel::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Hotel::frontend.layouts.search.fields.location')
                            @break
                            @case ('date')
                                @include('Hotel::frontend.layouts.search.fields.date')
                            @break
                            @case ('attr')
                                @include('Hotel::frontend.layouts.search.fields.attr')
                            @break
                            @case ('guests')
                                @include('Hotel::frontend.layouts.search.fields.guests')
                            @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{__("Search")}}</button>
    </div>
</form>

@push('css')
<style>
    .bravo_search_hotel .hotel-service-suggestions {
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
    .bravo_search_hotel .hotel-service-suggestions .suggestion-item {
        padding: 10px;
        cursor: pointer;
    }
    .bravo_search_hotel .hotel-service-suggestions .suggestion-item:hover {
        background-color: #f0f0f0;
    }
    @media (max-width: 1023px) {
        .bravo_search_hotel .hotel-search-form.bravo_form {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
        }
        .bravo_search_hotel .hotel-search-form .hotel-search-field-date,
        .bravo_search_hotel .hotel-search-form .hotel-search-field-guests {
            display: none !important;
        }
        .bravo_search_hotel .hotel-search-form .g-field-search {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            width: 100%;
            padding: 0 15px;
        }
        .bravo_search_hotel .hotel-search-form .hotel-search-row {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            margin: 0;
        }
        .bravo_search_hotel .hotel-search-form .hotel-search-row > [class*="col-"] {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            width: 100%;
            padding: 0;
            border-right: none !important;
        }
        .bravo_search_hotel .hotel-search-form .hotel-search-field-service_name {
            order: 1;
        }
        .bravo_search_hotel .hotel-search-form .hotel-search-field-location {
            order: 2;
        }
        .bravo_search_hotel .hotel-search-form .form-group {
            position: relative;
            border-bottom: 1px solid #D7DCE3 !important;
        }
        .bravo_search_hotel .hotel-search-form .form-content {
            padding: 16px 10px 12px 44px;
        }
        .bravo_search_hotel .hotel-search-form .field-icon {
            left: 10px;
            font-size: 28px;
            margin-top: -14px;
        }
        .bravo_search_hotel .hotel-search-form label {
            font-size: 13px;
            display: block;
            margin-bottom: 4px;
        }
        .bravo_search_hotel .hotel-search-form .g-map-place .form-control,
        .bravo_search_hotel .hotel-search-form .smart-search .form-control,
        .bravo_search_hotel .hotel-search-form .hotel-service-name-input {
            font-size: 15px;
            padding: 0 !important;
            height: auto;
            min-height: 22px;
            width: 100%;
            border: none;
            box-shadow: none;
            background: transparent;
        }
        .bravo_search_hotel .hotel-search-form .g-button-submit {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            width: 100%;
            text-align: right;
            padding: 0 15px 12px;
        }
        .bravo_search_hotel .hotel-search-form .g-button-submit .btn-search {
            display: inline-block;
            width: auto;
            height: auto;
            margin: 8px 0 0;
            border-radius: 5px;
            padding: 9px 22px;
            font-size: 14px;
        }
    }
</style>
@endpush

@push('js')
<script>
jQuery(function ($) {
    $(document).on('keyup', '.hotel-service-name-input', function () {
        var query = $.trim($(this).val());
        var $box = $(this).closest('.input-search').find('.hotel-service-suggestions');

        if (query.length < 1) {
            $box.empty();
            return;
        }

        $.get('{{ route("hotelservices.search") }}', { query: query })
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

    $(document).on('click', '.hotel-service-suggestions .suggestion-item', function () {
        var $input = $(this).closest('.input-search').find('.hotel-service-name-input');
        $input.val($(this).text());
        $(this).closest('.hotel-service-suggestions').empty();
    });
});
</script>
@endpush
