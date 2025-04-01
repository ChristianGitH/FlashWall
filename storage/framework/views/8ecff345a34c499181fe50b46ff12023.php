<div>
    <?php
        // Wee need this extra step to support models arrays. Ex: wire:model="emails.0"  , wire:model="emails.1"
        $uuid = $uuid . $modelName()
    ?>

    
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

    
    <!--[if BLOCK]><![endif]--><?php if($prefix || $suffix || $prepend || $append): ?>
        <div class="flex">
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($prefix || $prepend): ?>
        <div
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    "rounded-s-lg flex items-center bg-base-200",
                    "border border-primary border-e-0 px-4" => $prefix,
                    "border-0 bg-base-300" => $attributes->has('disabled') && $attributes->get('disabled') == true,
                    "border-dashed" => $attributes->has('readonly') && $attributes->get('readonly') == true,
                    "!border-error" => $errorFieldName() && $errors->has($errorFieldName()) && !$omitError
                ]); ?>"
        >
            <?php echo e($prepend ?? $prefix); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="flex-1 relative">
        
        <!--[if BLOCK]><![endif]--><?php if($money): ?>
            <div
                wire:key="money-<?php echo e(rand()); ?>"
                x-data="{ amount: $wire.get('<?php echo e($modelName()); ?>') }" x-init="$nextTick(() => new Currency($refs.myInput, <?php echo e($moneySettings()); ?>))"
            >
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <input
            id="<?php echo e($uuid); ?>"
            placeholder = "<?php echo e($attributes->whereStartsWith('placeholder')->first()); ?> "

            <?php if($attributes->has('autofocus') && $attributes->get('autofocus') == true): ?>
                autofocus
            <?php endif; ?>

            <?php if($money): ?>
                x-ref="myInput"
                :value="amount"
                x-on:input="$nextTick(() => $wire.set('<?php echo e($modelName()); ?>', Currency.getUnmasked(), false))"
                inputmode="numeric"
            <?php endif; ?>

            <?php echo e($attributes
                    ->merge(['type' => 'text'])
                    ->except($money ? 'wire:model' : '')
                    ->class([
                        'input input-primary w-full peer',
                        'ps-10' => ($icon),
                        'h-14' => ($inline),
                        'pt-3' => ($inline && $label),
                        'rounded-s-none' => $prefix || $prepend,
                        'rounded-e-none' => $suffix || $append,
                        'border border-dashed' => $attributes->has('readonly') && $attributes->get('readonly') == true,
                        'input-error' => $errorFieldName() && $errors->has($errorFieldName()) && !$omitError
                ])); ?>

        />

        
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
<?php $component->withAttributes(['class' => 'absolute top-1/2 -translate-y-1/2 start-3 text-gray-400 pointer-events-none']); ?>
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
<?php $component->withAttributes(['x-on:click' => '$wire.set(\''.e($modelName()).'\', \'\', '.e(json_encode($attributes->wire('model')->hasModifier('live'))).')','class' => 'absolute top-1/2 end-3 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600']); ?>
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
<?php $component->withAttributes(['class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses(['absolute top-1/2 end-3 -translate-y-1/2 text-gray-400 pointer-events-none', '!end-10' => $clearable]))]); ?>
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

        
        <!--[if BLOCK]><![endif]--><?php if($label && $inline): ?>
            <label for="<?php echo e($uuid); ?>" class="absolute text-gray-400 duration-300 transform -translate-y-1 scale-75 top-2 origin-left rtl:origin-right rounded px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-1 <?php if($inline && $icon): ?> start-9 <?php else: ?> start-3 <?php endif; ?>">
                <?php echo e($label); ?>

            </label>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <!--[if BLOCK]><![endif]--><?php if($money): ?>
                <input type="hidden" <?php echo e($attributes->only('wire:model')); ?> />
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($suffix || $append): ?>
         <div
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    "rounded-e-lg flex items-center bg-base-200",
                    "border border-primary border-s-0 px-4" => $suffix,
                    "border-0 bg-base-300" => $attributes->has('disabled') && $attributes->get('disabled') == true,
                    "border-dashed" => $attributes->has('readonly') && $attributes->get('readonly') == true,
                    "!border-error" => $errorFieldName() && $errors->has($errorFieldName()) && !$omitError
                ]); ?>"
        >
            <?php echo e($append ?? $suffix); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($prefix || $suffix || $prepend || $append): ?>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if(!$omitError && $errors->has($errorFieldName())): ?>
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->get($errorFieldName()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = Arr::wrap($message); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="<?php echo e($errorClass); ?>" x-classes="text-red-500 label-text-alt p-1"><?php echo e($line); ?></div>
                <?php if($firstErrorOnly) break; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            <?php if($firstErrorOnly) break; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($hint): ?>
        <div class="<?php echo e($hintClass); ?>" x-classes="label-text-alt text-gray-400 py-1 pb-0"><?php echo e($hint); ?></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/bb1d900bb7eab8c3d52693f02a423107.blade.php ENDPATH**/ ?>