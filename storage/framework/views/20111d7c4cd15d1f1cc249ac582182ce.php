<form
    <?php echo e($attributes->whereDoesntStartWith('class')); ?>

    <?php echo e($attributes->class(['grid grid-flow-row auto-rows-min gap-3'])); ?>

>

    <?php echo e($slot); ?>


    <!--[if BLOCK]><![endif]--><?php if($actions): ?>
        <!--[if BLOCK]><![endif]--><?php if(!$noSeparator): ?>
            <hr class="my-3" />
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div <?php echo e($actions->attributes->class(["flex justify-end gap-3"])); ?>>
            <?php echo e($actions); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</form><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/49b90db44d79d1e5621478b26c29a2e2.blade.php ENDPATH**/ ?>