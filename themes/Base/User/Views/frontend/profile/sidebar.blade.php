<div class="profile-summary mb-2">
    <div class="profile-avatar">
        @if($avatar = $user->getAvatarUrl())
            <div class="avatar-img avatar-cover" style="background-image: url('{{$user->getAvatarUrl()}}')">
            </div>
        @else
            <span class="avatar-text">{{$user->getDisplayName()[0]}}</span>
        @endif
    </div>
    <div class="text-center mb-1"><span class="role-name  badge badge-primary">{{$user->role_name}}</span></div>
    <h3 class="display-name">{{$user->getDisplayName()}}
        @if($user->is_verified)
            <img data-toggle="tooltip" data-placement="top" src="{{asset('icon/ico-vefified-1.svg')}}" title="{{__("Verified")}}" alt="ico-vefified-1">
        @else
            <img data-toggle="tooltip" data-placement="top" src="{{asset('icon/ico-not-vefified-1.svg')}}" title="{{__("Not verified")}}" alt="ico-vefified-1">
        @endif
    </h3>

    <p class="profile-since">{{ __("Member Since :time",["time"=> date("M Y",strtotime($user->created_at))]) }}</p>

    @if($user->hasPermission('dashboard_vendor_access'))<hr>
    <ul class="meta-info style2">
        <!-- <li class="is_vendor">
            <i class="icon ion-ios-ribbon"></i>
            {{__('Vendor')}}
        </li> -->
        <li class="review_count">
            <i class="icon ion-ios-thumbs-up"></i>
            @if($user->review_count <= 1)
                {{__(':count review',['count'=>$user->review_count])}}
            @else
                {{__(':count reviews',['count'=>$user->review_count])}}
            @endif
        </li>
    </ul>
    @endif
    @if(setting_item('vendor_show_email') or setting_item('vendor_show_phone'))
    @php
        $addressLines = array_filter([
            $user->address ?? null,
            $user->address2 ?? null,
            $user->city ?? null,
            $user->state ?? null,
        ], fn ($line) => filled(trim((string) $line)));
        $fullAddress = !empty($addressLines) ? implode(', ', $addressLines) : '';
        $googleLocationUrl = $fullAddress !== ''
            ? 'https://www.google.com/search?q=' . rawurlencode($fullAddress)
            : null;

        $showEmail = setting_item('vendor_show_email') && $user->email;
        $showPhone = setting_item('vendor_show_phone') && $user->phone;
        $showWhatsapp = setting_item('vendor_show_phone') && ($user->whatsapp_code || $user->whatsapp);
        $showAddress = !empty($addressLines) && $googleLocationUrl;
    @endphp
    <hr>
    @if($showEmail)
    <ul class="meta-info style1">
        <li class="user_email">
            <span class="label">{{__('Email:')}}</span>
            <span class="val">
                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
            </span>
        </li>
    </ul>
    @endif

    @if($showPhone)
        @if($showEmail)
        <hr class="profile-contact-divider">
        @endif
    <ul class="meta-info style1">
        <li class="user_phone">
            <span class="label">{{__('Phone:')}}</span>
            <span class="val">
                @include('Layout::parts.phone-display', [
                    'phone' => $user->phone,
                    'href' => 'tel:' . phone_intl_digits(phone_intl_normalize_e164($user->phone)),
                ])
            </span>
        </li>
        @if($showWhatsapp)
        <li class="user_whatsapp">
            <span class="label">{{__('Whatsapp:')}}</span>
            <span class="val">
                @php $waDigits = phone_intl_digits(phone_intl_whatsapp_e164($user->whatsapp_code, $user->whatsapp)); @endphp
                @include('Layout::parts.phone-display', [
                    'whatsapp_code' => $user->whatsapp_code,
                    'whatsapp' => $user->whatsapp,
                    'href' => $waDigits ? 'https://wa.me/' . $waDigits : null,
                ])
            </span>
        </li>
        @endif
    </ul>
    @elseif($showWhatsapp)
        @if($showEmail)
        <hr class="profile-contact-divider">
        @endif
    <ul class="meta-info style1">
        <li class="user_whatsapp">
            <span class="label">{{__('Whatsapp:')}}</span>
            <span class="val">
                @php $waDigits = phone_intl_digits(phone_intl_whatsapp_e164($user->whatsapp_code, $user->whatsapp)); @endphp
                @include('Layout::parts.phone-display', [
                    'whatsapp_code' => $user->whatsapp_code,
                    'whatsapp' => $user->whatsapp,
                    'href' => $waDigits ? 'https://wa.me/' . $waDigits : null,
                ])
            </span>
        </li>
    </ul>
    @endif

    @if($showAddress)
        @if($showEmail || $showPhone || $showWhatsapp)
        <hr class="profile-contact-divider">
        @endif
    <ul class="meta-info style1">
        <li class="user_address">
            <span class="label">{{__('Address:')}}</span>
            <span class="val">
                <a href="{{ $googleLocationUrl }}" class="user-address-google-link" target="_blank" rel="noopener noreferrer" title="{{ __('Search this location on Google') }}">
                    {!! implode('<br>', array_map('e', $addressLines)) !!}
                </a>
            </span>
        </li>
    </ul>
    @endif
    <div class="profile-social-links">
        @if($user->facebook != NULL)
        <a href="{{$user->facebook??'#'}}" class="social-link social-facebook" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook"></i></a>
        @endif
        @if($user->youtube != NULL)
        <a href="{{$user->youtube??'#'}}" class="social-link social-youtube" target="_blank" rel="noopener noreferrer"><i class="fa fa-youtube"></i></a>
        @endif
        @if($user->twitter != NULL)
        <a href="{{$user->twitter??'#'}}" class="social-link social-twitter" target="_blank" rel="noopener noreferrer"><i class="fa fa-twitter"></i></a>
        @endif
        @if($user->linkedin != NULL)
        <a href="{{$user->linkedin??'#'}}" class="social-link social-linkedin" target="_blank" rel="noopener noreferrer"><i class="fa fa-linkedin"></i></a>
        @endif
        @if($user->instagram != NULL)
        <a href="{{$user->instagram??'#'}}" class="social-link social-instagram" target="_blank" rel="noopener noreferrer"><i class="fa fa-instagram"></i></a>
        @endif
    </div>
   
    @endif
</div>
