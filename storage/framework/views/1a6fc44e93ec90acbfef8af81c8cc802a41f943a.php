<div class="<?php echo e($viewClass['form-group'], false); ?> <?php echo !$errors->has($errorKey) ? '' : 'has-error'; ?>">

    <label for="<?php echo e($id, false); ?>" class="<?php echo e($viewClass['label'], false); ?> control-label"><?php echo e($label, false); ?></label>

    <div class="<?php echo e($viewClass['field'], false); ?>">

        <?php echo $__env->make('admin::form.error', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <div id="<?php echo e($id, false); ?>" style="width: 100%; height: 100%;">
            <p><?php echo old($column, $value); ?></p>
        </div>

        <input id="input-<?php echo e($id, false); ?>" type="hidden" name="<?php echo e($name, false); ?>" value="<?php echo e(old($column, $value), false); ?>" />

        <?php echo $__env->make('admin::form.help-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>
</div>