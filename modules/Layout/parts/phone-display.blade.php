@php
    $phoneValue = $phone ?? '';
    if ($phoneValue === '' && !empty($whatsapp_code)) {
        $phoneValue = phone_intl_whatsapp_e164($whatsapp_code ?? '', $whatsapp ?? '');
    }
    $phoneValue = phone_intl_normalize_e164($phoneValue);
    $displayText = $display ?? $phoneValue;
    $iso2 = !empty($phoneValue) ? phone_intl_country_iso2($phoneValue) : '';
    $linkHref = $href ?? null;
@endphp
@if(!empty($phoneValue))
<span class="bravo-phone-display" data-phone="{{ $phoneValue }}">
    @if($iso2)
        <span class="iti__flag iti__{{ $iso2 }}" role="img" aria-hidden="true"></span>
    @endif
    @if($linkHref)
        <a href="{{ $linkHref }}">{{ $displayText }}</a>
    @else
        <span class="bravo-phone-display-number">{{ $displayText }}</span>
    @endif
</span>
@endif
