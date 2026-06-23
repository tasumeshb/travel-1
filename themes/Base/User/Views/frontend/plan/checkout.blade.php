@extends('layouts.app')
@push('css')
    <link href="{{ asset('module/booking/css/checkout.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
@endpush
@section('content')
    @php
        $translate = $plan->translate();
        if(request()->query('annual')!=1){
            $price = $plan->price;
            $duration_text = $plan->duration_type_text;
        }else{
            $price = $plan->annual_price;
            $duration_text = __('Year');
        }
            $term_conditions = setting_item('booking_term_conditions');

    @endphp
    <section class="pricing-section bravo-booking-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @include('admin.message')
                    <div class="sec-title text-center mb-5">
                        <h2>{{ setting_item_with_lang('user_plans_page_title', app()->getLocale()) ?? __("Pricing Packages")}}</h2>
                    </div>
                    <div class="pricing-tabs tabs-box">
                        <form method="post" action="{{route('user.plan.buyProcess',['id'=>$plan->id])}}" class="row">
                            @csrf
                            <input type="hidden" name="annual" value="{{request()->query('annual')}}">
                            <div class="pricing-table col-12">
                                <div class="inner-box">
                                    <div class="title">{{$translate->title}}</div>
                                    <div class="price">{{ format_money($price)}}
                                        @if($price)
                                            <span class="duration">/ {{$duration_text}}</span>
                                        @endif
                                    </div>
                                    <div class="table-content">
                                        {!! clean($translate->content) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-section col-12">
                                @include('Booking::frontend.booking.checkout-payment')
                                
                                

                            </div>
                            <div class="form-actions col-12">
                                <div class="form-group">
                                    <label class="term-conditions-checkbox">
                                        <input type="checkbox" id="term_conditions_id" checked  name="term_conditions"> {{__('I have read and accept the')}} <a target="_blank" href="{{get_page_url($term_conditions)}}">{{__('terms and conditions')}}</a>
                                    </label>
                                </div>
                                @if(setting_item("booking_enable_recaptcha"))
                                    <div class="form-group">
                                        {{recaptcha_field('booking')}}
                                    </div>
                                @endif
                                
                                
                                <?php

// -----------------------------
// 1. GET USER IP (FIXED)
// -----------------------------
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// -----------------------------
// 2. GET CURRENCY (ipwho.is)
// -----------------------------
$response = @file_get_contents("https://ipwho.is/{$ip}");
$data = $response ? json_decode($response) : null;

$currencycode = $data->currency->code ?? 'USD';
$currencysymbol = $data->currency->symbol ?? '$';

// -----------------------------
// 3. GET INR RATE (EXCHANGE API)
// -----------------------------
$url = "https://open.er-api.com/v6/latest/USD";
$response = @file_get_contents($url);
$rates = $response ? json_decode($response, true) : null;

$usd_to_inr_rate = $rates['rates']['INR'] ?? null;

// -----------------------------
// 4. PRICE CALCULATION (FIXED ORDER)
// -----------------------------
$package_price = $price;

if ($usd_to_inr_rate) {
    $package_price = $price * $usd_to_inr_rate;
}

// convert to paise/cents BEFORE formatting
$package_price_100 = $package_price * 100;

// format only for display use (NOT for math)
$package_price = number_format($package_price, 2, '.', '');

?>

<input type="hidden" name="bank_transaction_info" id="bank_transaction_info"/>     
                                
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        var submitButton = document.getElementById('submit_id');
        var rzpbutton = document.getElementById('rzp-button');
        var loadgif_id = document.getElementById('loadgif_id'); 
        var currency = "<?php echo $currencycode; ?>"; 
        
        var options = {
            "key": "rzp_live_tIEq2PniYEm1UW", // Replace with your Razorpay API key
            "amount": <?php echo $package_price_100;?>, // Example: Amount in paise (50000 paise = ₹500)
            "currency": currency, 
            "name": "Travelkey",
            "description": "Packages Upgrade",
            "handler": function(response) {
                console.log('Payment success:', response); 
                var paymentId = response.razorpay_payment_id;  
                document.getElementById('bank_transaction_info').value = paymentId; 
                loadgif_id.style.display = 'block';
                rzpbutton.style.display = 'none';
                submitButton.click();
            },
            "prefill": {
                "name": "Mr Kissan Weblinks Private Limited",
                "email": "travelkeyai@gmail.com"
            },
            "theme": {
                "color": "#1AF0EB" // Razorpay button color
            },
            "modal": {
                "ondismiss": function() {
                    console.log('Payment canceled');
                    // Handle payment cancellation here 
                }
            }
        };

        var rzp = new Razorpay(options);

        document.getElementById('rzp-button').addEventListener('click', function(e) {
            rzp.open();
            e.preventDefault();
        });
    });
</script>
<?php  if($package_price > 0){ ?>
    <button id="rzp-button" class="btn btn-primary">Pay Now <?php echo $currencysymbol; ?> <?php echo $package_price;?></button>
    <?php }else{?>
          <button type="submit" style="visibility: hidden1;" id="submit_id" class="btn btn-danger">{{__('Submit')}}</button>
             <?php } ?>                   
      <img src="https://travelkey.io/load.gif" style="width: 120px; display: none;" id="loadgif_id"/>
      
                            </div>
                        </form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkBox = document.getElementById('term_conditions_id');
    var payButton = document.getElementById('rzp-button');

    // Update button display based on checkbox
    checkBox.addEventListener('change', function() {
        payButton.style.display = checkBox.checked ? 'block' : 'none';
    });
});
</script>


                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('footer')
@endsection
