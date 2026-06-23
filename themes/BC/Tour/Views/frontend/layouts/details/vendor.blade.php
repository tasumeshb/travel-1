@php
    $vendor = $row->author;
    $user = $vendor;
@endphp
@if(!empty($vendor->id))
@push('css')
    <link href="{{ asset('dist/frontend/module/user/css/profile.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
    <link href="{{ asset('css/vendor-profile-sidebar.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
@endpush
<div class="bravo-detail-profile-aside">
    @include('User::frontend.profile.sidebar')

    <div class="profile-summary mb-2 bravo-detail-profile-actions">
        @if((!Auth::check() or Auth::id() != $row->author_id) and setting_item('inbox_enable'))
            <a class="btn btn-primary btn-block bc_start_chat mb-2" href="{{ route('user.chat', ['user_id' => $row->author_id]) }}">
                <i class="icon ion-ios-chatboxes"></i> {{ __('Message host') }}
            </a>
        @endif
        <a class="btn btn-primary btn-block" href="{{ route('user.profile', ['id' => $vendor->user_name ?? $vendor->id]) }}">
            {{ __('View Profile') }}
        </a>
    </div>
</div>
@endif
