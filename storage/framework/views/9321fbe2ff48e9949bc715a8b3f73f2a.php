    <div <?php echo e($attributes->class(["bg-base-100 border-base-300 border-b", "sticky top-0 z-10" => $sticky])); ?>>
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["flex items-center px-6 py-5",  "max-w-screen-2xl mx-auto" => !$fullWidth]); ?>">
            <div <?php echo e($brand?->attributes->class(["flex-1 flex items-center"])); ?>>
                <?php echo e($brand); ?>

            </div>
            <div <?php echo e($actions?->attributes->class(["flex items-center gap-4"])); ?>>
                <?php echo e($actions); ?>

            </div>
        </div>
    </div><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/32041f3466a5a1c8bf19db455afe2cda.blade.php ENDPATH**/ ?>