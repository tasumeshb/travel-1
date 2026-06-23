@php
    $websiteUrl = bookable_external_url($row, 'website');
    $bookingUrl = bookable_external_url($row, 'booking_url');
@endphp
@if($websiteUrl || $bookingUrl)
<div class="bravo-detail-external-links">
    <div class="profile-summary mb-2">
        <h4 class="summary-title">{{ __('Links') }}</h4>
        <div class="bravo-detail-external-links__buttons">
            @if($websiteUrl)
                <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-block mb-2">
                    {{ __('Website Link') }}
                </a>
            @endif
            @if($bookingUrl)
                <a href="{{ $bookingUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-block">
                    {{ __('Booking Link') }}
                </a>
            @endif
        </div>
    </div>
</div>
@endif
