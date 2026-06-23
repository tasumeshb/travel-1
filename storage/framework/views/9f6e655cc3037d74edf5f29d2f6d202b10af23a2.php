<?php
    $field = $field ?? 'price';
    $label = $label ?? __('Price');
    $placeholder = $placeholder ?? $label;
    $colClass = $colClass ?? 'col-lg-6';
    $showCurrencySelect = $showCurrencySelect ?? false;
    $priceData = service_price_for_admin($row, $field);
    $currencyOptions = service_price_currency_options();
?>
<div class="<?php echo e($colClass); ?>">
    <div class="form-group">
        <label class="control-label"><?php echo e($label); ?></label>
        <div class="input-group">
            <input type="number" step="any" min="0" name="<?php echo e($field); ?>" class="form-control"
                   value="<?php echo e($priceData['amount'] !== '' ? $priceData['amount'] : ''); ?>"
                   placeholder="<?php echo e($placeholder); ?>">
            <?php if($showCurrencySelect): ?>
                <div class="input-group-append">
                    <select name="price_currency" class="form-control" style="min-width: 110px;">
                        <?php $__currentLoopData = $currencyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($code); ?>" <?php if($priceData['currency'] === $code): ?> selected <?php endif; ?>><?php echo e(strtoupper($code)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <?php if($showCurrencySelect): ?>
            <small class="text-muted d-block mt-1">
                <?php echo e(__('Stored in :currency using exchange rates from Settings → Payment.', ['currency' => strtoupper(setting_item('currency_main'))])); ?>

            </small>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/html/travel/modules/Core/Views/admin/components/price-with-currency.blade.php ENDPATH**/ ?>