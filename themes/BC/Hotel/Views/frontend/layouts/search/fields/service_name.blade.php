@php
    $serviceNameInputId = $service_name_input_id ?? 'hotelservice_name';
    $suggestionsBoxId = $service_name_suggestions_id ?? 'hotelsuggestions';
@endphp
<div class="form-group">
    <i class="field-icon fa icofont-search"></i>
    <div class="form-content">
        <label>{{ $field['title'] ?? "" }}</label>
        <div class="input-search">
            <input type="text" id="{{ $serviceNameInputId }}" name="service_name" class="form-control hotel-service-name-input" placeholder="{{__("Search for...")}}" value="{{ request()->input("service_name") }}">
            <div id="{{ $suggestionsBoxId }}" class="suggestions-list hotel-service-suggestions"></div>
        </div>
    </div>
</div>
