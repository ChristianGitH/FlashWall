    <!--[if BLOCK]><![endif]--><?php if($link): ?>
        <a href="<?php echo $link; ?>"
    <?php else: ?>
        <button
    <?php endif; ?>

        wire:key="<?php echo e($uuid); ?>"
        <?php echo e($attributes->whereDoesntStartWith('class')->merge(['type' => 'button'])); ?>

        <?php echo e($attributes->class(['btn normal-case', "!inline-flex lg:tooltip $tooltipPosition" => $tooltip])); ?>


        <?php if($link && $external): ?>
            target="_blank"
        <?php endif; ?>

        <?php if($link && !$external && !$noWireNavigate): ?>
            wire:navigate
        <?php endif; ?>

        <?php if($tooltip): ?>
            data-tip="<?php echo e($tooltip); ?>"
        <?php endif; ?>

        <?php if($spinner): ?>
            wire:target="<?php echo e($spinnerTarget()); ?>"
            wire:loading.attr="disabled"
        <?php endif; ?>
    >

        <!-- SPINNER LEFT -->
        <!--[if BLOCK]><![endif]--><?php if($spinner && !$iconRight): ?>
            <span wire:loading wire:target="<?php echo e($spinnerTarget()); ?>" class="loading loading-spinner w-5 h-5"></span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- ICON -->
        <!--[if BLOCK]><![endif]--><?php if($icon): ?>
            <span class="block" <?php if($spinner): ?> wire:loading.class="hidden" wire:target="<?php echo e($spinnerTarget()); ?>" <?php endif; ?>>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- LABEL / SLOT -->
        <!--[if BLOCK]><![endif]--><?php if($label): ?>
            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(["hidden lg:block" => $responsive ]); ?>">
                <?php echo e($label); ?>

            </span>
            <!--[if BLOCK]><![endif]--><?php if(strlen($badge ?? '') > 0): ?>
                <span class="badge badge-primary <?php echo e($badgeClasses); ?>"><?php echo e($badge); ?></span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php else: ?>
            <?php echo e($slot); ?>

        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- ICON RIGHT -->
        <!--[if BLOCK]><![endif]--><?php if($iconRight): ?>
            <span class="block" <?php if($spinner): ?> wire:loading.class="hidden" wire:target="<?php echo e($spinnerTarget()); ?>" <?php endif; ?>>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $iconRight] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- SPINNER RIGHT -->
        <!--[if BLOCK]><![endif]--><?php if($spinner && $iconRight): ?>
            <span wire:loading wire:target="<?php echo e($spinnerTarget()); ?>" class="loading loading-spinner w-5 h-5"></span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if(!$link): ?>
        </button>
    <?php else: ?>
        </a>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]--><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/4c9d4fa7134d0e147159af5d1c028bbc.blade.php ENDPATH**/ ?>