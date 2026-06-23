@push('css')
    <style>
        .hotel_price_tariffs .hotel-info {
            padding: 12px 15px;
        }
        .hotel_price_tariffs .hotel-info .room-name {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .hotel_price_tariffs .hotel-info .room-meta {
            margin-bottom: 0;
        }
        .hotel_price_tariffs .col-price {
            padding: 12px 15px;
        }
        .hotel_price_tariffs .col-price .price {
            font-size: 18px;
        }
    </style>
@endpush
@if(!empty($price_tariffs))
    <div class="hotel_price_tariffs hotel_rooms_form">
        <h3 class="heading-section">{{__('Price Tariffs')}}</h3>
        <div class="hotel_list_rooms">
            <div class="row">
                <div class="col-md-12">
                    @foreach($price_tariffs as $tariff)
                        <div class="room-item">
                            <div class="row">
                                <div class="col-xs-12 col-md-3">
                                    @if(!empty($tariff['image']))
                                        <div class="image" data-toggle="modal" data-target="#modal_room_{{$tariff['id']}}">
                                            <img src="{{$tariff['image']}}" alt="{{$tariff['title']}}">
                                            @if(!empty($tariff['gallery']) && count($tariff['gallery']) > 1)
                                                <div class="count-gallery">
                                                    <i class="fa fa-picture-o"></i>
                                                    {{count($tariff['gallery'])}}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @if(!empty($tariff['gallery']))
                                        <div class="modal" id="modal_room_{{$tariff['id']}}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{$tariff['title']}}</h5>
                                                        <span class="c-pointer" data-dismiss="modal" aria-label="Close">
                                                            <i class="input-icon field-icon fa">
                                                                <img src="{{asset('images/ico_close.svg')}}" alt="close">
                                                            </i>
                                                        </span>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="fotorama" data-nav="thumbs" data-width="100%" data-auto="false" data-allowfullscreen="true">
                                                            @foreach($tariff['gallery'] as $g)
                                                                <a href="{{$g['large']}}"></a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="hotel-info">
                                        <h3 class="room-name">{{$tariff['title']}}</h3>
                                        <ul class="room-meta">
                                            @if(!empty($tariff['size_html']))
                                                <li>
                                                    <div class="item" data-toggle="tooltip" data-placement="top" title="{{__('Room Footage')}}">
                                                        <i class="input-icon field-icon icofont-ruler-compass-alt"></i>
                                                        <span>{!! $tariff['size_html'] !!}</span>
                                                    </div>
                                                </li>
                                            @endif
                                            @if(!empty($tariff['beds_html']))
                                                <li>
                                                    <div class="item" data-toggle="tooltip" data-placement="top" title="{{__('No. Beds')}}">
                                                        <i class="input-icon field-icon icofont-hotel"></i>
                                                        <span>{!! $tariff['beds_html'] !!}</span>
                                                    </div>
                                                </li>
                                            @endif
                                            @if(!empty($tariff['adults_html']))
                                                <li>
                                                    <div class="item" data-toggle="tooltip" data-placement="top" title="{{__('No. Adults')}}">
                                                        <i class="input-icon field-icon icofont-users-alt-4"></i>
                                                        <span>{!! $tariff['adults_html'] !!}</span>
                                                    </div>
                                                </li>
                                            @endif
                                            @if(!empty($tariff['children_html']))
                                                <li>
                                                    <div class="item" data-toggle="tooltip" data-placement="top" title="{{__('No. Children')}}">
                                                        <i class="input-icon field-icon fa-child fa"></i>
                                                        <span>{!! $tariff['children_html'] !!}</span>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                        @if(!empty($tariff['term_features']))
                                            <div class="room-attribute-item">
                                                <ul>
                                                    @foreach($tariff['term_features'] as $feature)
                                                        <li>
                                                            <i class="input-icon field-icon {{$feature['icon']}}" data-toggle="tooltip" data-placement="top" title="{{$feature['title']}}"></i>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="col-price clear">
                                        <div class="text-center">
                                            <span class="price">{!! $tariff['price_html'] !!}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
@include("Booking::frontend.global.enquiry-form",['service_type'=>'hotel'])
