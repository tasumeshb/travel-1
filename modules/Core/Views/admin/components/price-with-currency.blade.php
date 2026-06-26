@php
    $field = $field ?? 'price';
    $label = $label ?? __('Price');
    $placeholder = $placeholder ?? $label;
    $colClass = $colClass ?? 'col-lg-6';
    $showCurrencySelect = $showCurrencySelect ?? false;
    $priceData = service_price_for_admin($row, $field);
    $currencyOptions = service_price_currency_options();
    if ($showCurrencySelect && empty($row->price_currency)) {
        $preferredCurrency = strtolower((string) setting_item('service_price_input_currency', 'inr'));
        if (isset($currencyOptions[$preferredCurrency])) {
            $priceData['currency'] = $preferredCurrency;
        }
    }
@endphp
<div class="{{ $colClass }}">
    <div class="form-group">
        <label class="control-label">{{ $label }}</label>
        <div class="input-group">
            <input type="number" step="any" min="0" name="{{ $field }}" class="form-control"
                   value="{{ $priceData['amount'] !== '' ? $priceData['amount'] : '' }}"
                   placeholder="{{ $placeholder }}">
            @if($showCurrencySelect)
                <div class="input-group-append">
                    <select name="price_currency" class="form-control" style="min-width: 110px;">
                        @foreach($currencyOptions as $code => $name)
                            <option value="{{ $code }}" @if($priceData['currency'] === $code) selected @endif>{{ strtoupper($code) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        @if($showCurrencySelect)
            <small class="text-muted d-block mt-1">
                {{ __('Stored in :currency using exchange rates from Settings → Payment.', ['currency' => strtoupper(setting_item('currency_main'))]) }}
            </small>
        @endif
    </div>
</div>
