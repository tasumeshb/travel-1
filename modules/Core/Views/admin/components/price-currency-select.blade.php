@php
    $currency = service_price_currency_code($row);
    $currencyOptions = service_price_currency_options();
@endphp
<div class="col-lg-12">
    <div class="form-group">
        <label class="control-label">{{ __('Price currency') }}</label>
        <select name="price_currency" class="form-control" style="max-width: 220px;">
            @foreach($currencyOptions as $code => $name)
                <option value="{{ $code }}" @if($currency === $code) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
        <small class="text-muted d-block mt-1">
            {{ __('Enter amounts below in this currency. Saved in :currency using your exchange rates.', ['currency' => strtoupper(setting_item('currency_main'))]) }}
        </small>
    </div>
</div>
