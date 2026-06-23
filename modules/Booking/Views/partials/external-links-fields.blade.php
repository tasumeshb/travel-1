<div class="panel">
    <div class="panel-title"><strong>{{ __('Website & Booking Links') }}</strong></div>
    <div class="panel-body">
        <div class="form-group">
            <label for="service_website">{{ __('Website URL') }}</label>
            <input type="text" name="website" id="service_website" class="form-control"
                   value="{{ old('website', $row->website ?? '') }}"
                   placeholder="https://example.com">
            <small class="form-text text-muted">{{ __('Your business website (include https://)') }}</small>
        </div>
        <div class="form-group">
            <label for="service_booking_url">{{ __('Booking URL') }}</label>
            <input type="text" name="booking_url" id="service_booking_url" class="form-control"
                   value="{{ old('booking_url', $row->booking_url ?? '') }}"
                   placeholder="https://example.com/book-now">
            <small class="form-text text-muted">{{ __('External page where customers can book') }}</small>
        </div>
    </div>
</div>
