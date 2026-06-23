<?php
$types = get_bookable_services();
if (empty($types)) return;
$list_service = [];
?>
<div class="profile-service-tabs">
    <div class="service-nav-tabs">
        <ul class="nav nav-tabs">
            @php $i = 0; @endphp
            @foreach($types as $type=>$moduleClass)
                @php
                    if($type == "flight")  continue;
                    if(!$moduleClass::isEnable()) continue;
                    if(!$user->hasPermission($type.'_create')) continue;
                    $services = $moduleClass::getVendorServicesQuery($user->id)->orderBy('id','desc')->paginate(6);
                    if(empty($services->total())) continue;
                    $list_service[$type] = $services;
                

                $module_name = $moduleClass::getModelName();
                if( $module_name == 'car' || $module_name == 'Car'|| $module_name == 'CAR') {
                    $name = 'Premium Member';
                } else if(  $module_name == 'boat'  || $module_name == 'Boat'|| $module_name == 'BOAT') {
                    $name = 'Community Member';
                } else if(  $module_name == 'flight'  || $module_name == 'Flight'|| $module_name == 'FLIGHT') {
                    $name = 'Travel Insurance';
                } else if(  $module_name == 'space'  || $module_name == 'Space'|| $module_name == 'SPACE') {
                    $name = 'Travel Agent';
                } else {
                    $name = $module_name;
                }
                @endphp
                    <li class="nav-item">
                        <a href="#" class="nav-link @if(!$i) active @endif" data-toggle="tab" data-target="#type_{{$type}}">{{$name }}</a>
                    </li>
                @php $i++; @endphp
            @endforeach
        </ul>
    </div>
    <div class="tab-content">
        @php $i = 0; @endphp
        @foreach($types as $type=>$moduleClass)
            @php
                if($type == "flight")  continue;
                if(!$moduleClass::isEnable()) continue;
                if(empty($list_service[$type])) continue;
            @endphp
                @if(view()->exists(ucfirst($type).'::frontend.profile.service') && $user->hasPermission($type.'_create'))
                    <div class="tab-pane fade @if(!$i) show active @endif" id="type_{{$type}}" role="tabpanel" aria-labelledby="pills-home-tab">
                        @include(ucfirst($type).'::frontend.profile.service',['services'=>$list_service[$type]])
                    </div>
                    @php $i++; @endphp
                @endif
        @endforeach
    </div>
</div>
