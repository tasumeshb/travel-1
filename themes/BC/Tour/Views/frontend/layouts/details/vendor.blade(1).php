<?php
//if(!setting_item('tour_enable_inbox')) return;
$vendor = $row->author;
?>
@if(!empty($vendor->id))
<div class="owner-info widget-box">
    <div class="media">
        <div class="media-left">
            <a href="{{route('user.profile',['id'=>$vendor->user_name ?? $vendor->id])}}" class="avatar-cover" style="background-image: url('{{$vendor->getAvatarUrl()}}')" >
            </a>
            <!--<a href="{{$vendor->website??'#'}}" class="avatar-cover" style="background-image: url('{{$vendor->getAvatarUrl()}}')"  target="_blank">-->
            <!--</a>-->
        </div>
        <div class="media-body">
            <h4 class="media-heading"><a class="author-link" href="{{route('user.profile',['id'=>$vendor->user_name ?? $vendor->id])}}">{{$vendor->getDisplayName()}}</a>
                @if($vendor->is_verified)
                    <img data-toggle="tooltip" data-placement="top" src="{{asset('icon/ico-vefified-1.svg')}}" title="{{__("Verified")}}" alt="{{__("Verified")}}">
                @else
                    <img data-toggle="tooltip" data-placement="top" src="{{asset('icon/ico-not-vefified-1.svg')}}" title="{{__("Not verified")}}" alt="{{__("Verified")}}">
                @endif
            </h4>
            <p>{{ __("Member Since :time",["time"=> date("M Y",strtotime($vendor->created_at))]) }}</p>
            @if((!Auth::check() or Auth::id() != $row->author_id ) and setting_item('inbox_enable'))
                <a class="btn bc_start_chat" href="{{route('user.chat',['user_id'=>$row->author_id])}}" ><i class="icon ion-ios-chatboxes"></i> {{__('Message host')}}</a>
            @endif
        </div>  
    </div>
  
    <div style="text-align:center">
        <a class="btn btn-sm btn-primary"  target="_blank" href="{{$vendor->website??'#'}}" :class="{'disabled':onSubmit,'btn-success':(step != 2),'btn-success':step == 1}" name="submit">
        <span>PROFILE</span></a> 
    </div>

    <div class=" " style="text-align:center;margin-top:8px;font-size:24px">
        @if($vendor->facebook != NULL)
        <a href="{{$vendor->facebook??'#'}}" class="p-1" target="_blank"><i class="fa fa-facebook"></i></a>
        @endif
        @if($vendor->youtube != NULL)
        <a href="{{$vendor->youtube??'#'}}" class="p-1"target="_blank"><i class="fa fa-youtube"></i></a>
        @endif
        @if($vendor->twitter != NULL)
        <a href="{{$vendor->twitter??'#'}}" class="p-1"target="_blank"><i class="fa fa-twitter"></i></a>
        @endif
        @if($vendor->linkedin != NULL)
        <a href="{{$vendor->linkedin??'#'}}" class="p-1"target="_blank"><i class="fa fa-linkedin"></i></a>
        @endif
        @if($vendor->instagram != NULL)
        <a href="{{$vendor->instagram??'#'}}" class="p-1"target="_blank"><i class="fa fa-instagram"></i></a>
        @endif

    </div>
</div>
@endif
