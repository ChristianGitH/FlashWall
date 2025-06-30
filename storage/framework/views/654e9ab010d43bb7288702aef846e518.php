<div>
    <?php
        // Wee need this extra step to support models arrays. Ex: wire:model="emails.0"  , wire:model="emails.1"
        $uuid = $uuid . $modelName()
    ?>

    <!-- STANDARD LABEL -->
    <!--[if BLOCK]><![endif]--><?php if($label && !$inline): ?>
        <label for="<?php echo e($uuid); ?>" class="pt-0 label label-text font-semibold">
            <span>
                <?php echo e($label); ?>


                <!--[if BLOCK]><![endif]--><?php if($attributes->get('required')): ?>
                    <span class="text-error">*</span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </span>
        </label>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="flex" x-data>
        <div
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    "rounded-s-lg flex items-center",
                    "border border-primary border-e-0 px-4 cursor-pointer",
                    "focus-within:outline focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-primary",
                    "border-0 bg-base-300" => $attributes->has('disabled') && $attributes->get('disabled') == true,
                    "border-dashed" => $attributes->has('readonly') && $attributes->get('readonly') == true,
                    "!border-error" => $errorFieldName() && $errors->has($errorFieldName()) && !$omitError
                ]); ?>"

                x-on:click="$refs.colorpicker.click()"
                :style="{ backgroundColor: $wire.<?php echo e($modelName()); ?> }"
        >
            <input
                type="color"
                class="cursor-pointer opacity-0 w-4"
                x-ref="colorpicker"
                x-on:click.stop=""
                <?php echo e($attributes->wire('model')); ?>

                :style="{ backgroundColor: $wire.<?php echo e($modelName()); ?> }"  />
        </div>

        <div class="flex-1 relative">
            <!-- INPUT -->
            <input
                id="<?php echo e($uuid); ?>"
                placeholder = "<?php echo e($attributes->whereStartsWith('placeholder')->first()); ?> "
                <?php echo e($attributes
                        ->merge(['type' => 'text'])
                        ->class([
                            'input input-primary w-full peer',
                            'ps-10' => ($icon),
                            'h-14' => ($inline),
                            'pt-3' => ($inline && $label),
                            'rounded-s-none',
                            'border border-dashed' => $attributes->has('readonly') && $attributes->get('readonly') == true,
                            'input-error' => $errorFieldName() && $errors->has($errorFieldName()) && !$omitError
                    ])); ?>

            />

            <!-- ICON  -->
            <!--[if BLOCK]><![endif]--><?php if($icon): ?>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'absolute top-1/2 -translate-y-1/2 start-3 text-gray-400 cursor-pointer','x-on:click' => '$refs.colorpicker.click()']); ?>
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
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- CLEAR ICON  -->
            <!--[if BLOCK]><![endif]--><?php if($clearable): ?>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-x-mark'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => '$wire.set(\''.e($modelName()).'\', \'\', '.e(json_encode($attributes->wire('model')->hasModifier('live'))).')','class' => 'absolute top-1/2 end-3 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600']); ?>
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
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- RIGHT ICON  -->
            <!--[if BLOCK]><![endif]--><?php if($iconRight): ?>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $iconRight] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['absolute top-1/2 end-3 -translate-y-1/2 text-gray-400 cursor-pointer', '!end-10' => $clearable])),'x-on:click' => '$refs.colorpicker.click()']); ?>
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
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- INLINE LABEL -->
            <!--[if BLOCK]><![endif]--><?php if($label && $inline): ?>
                <label for="<?php echo e($uuid); ?>" class="absolute text-gray-400 duration-300 transform -translate-y-1 scale-75 top-2 origin-[0] rounded px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-1 <?php if($inline && $icon): ?> start-9 <?php else: ?> start-3 <?php endif; ?>">
                    <?php echo e($label); ?>

                </label>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- ERROR -->
    <!--[if BLOCK]><![endif]--><?php if(!$omitError && $errors->has($errorFieldName())): ?>
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->get($errorFieldName()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = Arr::wrap($message); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="<?php echo e($errorClass); ?>" x-classes="text-red-500 label-text-alt p-1"><?php echo e($line); ?></div>
                <?php if($firstErrorOnly) break; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            <?php if($firstErrorOnly) break; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- HINT -->
    <!--[if BLOCK]><![endif]--><?php if($hint): ?>
        <div class="<?php echo e($hintClass); ?>" x-classes="label-text-alt text-gray-400 py-1 pb-0"><?php echo e($hint); ?></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/c458640fdd15ccdf5a95a984f6d37033.blade.php ENDPATH**/ ?>