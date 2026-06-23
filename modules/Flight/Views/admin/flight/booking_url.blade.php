<div class="panel">
    <div class="panel-title"><strong>Booking Url</strong></div>
    <div class="panel-body">

        <h3 class="panel-body-title"> </h3>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group" >
                    <label for="">Booking Url</label>
                    <input type="text" name="booking_url" id=" booking_url" class="form-control" value="{{ old('booking_url',!empty($row->booking_url)?$row->booking_url:"")}}" placeholder="Enter booking url">

                </div>
            </div>
             
        </div> 
        
    </div>
</div>
