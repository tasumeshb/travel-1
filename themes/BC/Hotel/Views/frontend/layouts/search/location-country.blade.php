@if(!empty($row->location->name))
    @php
        $locationParts = get_location_display_parts($row->location);
    @endphp
    @if(!empty($show_location_icon))
        <i class="icofont-paper-plane"></i>
    @endif
    @if(!empty($locationParts['city']))
        <span class="location-city">{{ $locationParts['city'] }}</span>
    @endif
    @if(!empty($locationParts['country']))
        @if(!empty($locationParts['city']))
            <span class="location-separator">,</span>
        @endif
        <span class="location-country">
            @if(!empty($locationParts['country']['code']))
                <span class="flag-icon flag-icon-{{ $locationParts['country']['code'] }}" title="{{ $locationParts['country']['name'] }}"></span>
            @endif
            <span class="location-country-name">{{ $locationParts['country']['name'] }}</span>
        </span>
    @elseif(empty($locationParts['city']))
        @php $location = $row->location->translate(); @endphp
        <span class="location-city">{{ $location->name ?? '' }}</span>
    @endif
@endif
