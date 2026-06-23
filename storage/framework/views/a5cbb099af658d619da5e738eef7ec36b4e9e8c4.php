<div class="panel">
    <div class="panel-title"><strong><?php echo e(__('Website & Booking Links')); ?></strong></div>
    <div class="panel-body">
        <div class="form-group">
            <label for="service_website"><?php echo e(__('Website URL')); ?></label>
            <input type="text" name="website" id="service_website" class="form-control"
                   value="<?php echo e(old('website', $row->website ?? '')); ?>"
                   placeholder="https://example.com">
            <small class="form-text text-muted"><?php echo e(__('Your business website (include https://)')); ?></small>
        </div>
        <div class="form-group">
            <label for="service_booking_url"><?php echo e(__('Booking URL')); ?></label>
            <input type="text" name="booking_url" id="service_booking_url" class="form-control"
                   value="<?php echo e(old('booking_url', $row->booking_url ?? '')); ?>"
                   placeholder="https://example.com/book-now">
            <small class="form-text text-muted"><?php echo e(__('External page where customers can book')); ?></small>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/travel/modules/Booking/Views/partials/external-links-fields.blade.php ENDPATH**/ ?>