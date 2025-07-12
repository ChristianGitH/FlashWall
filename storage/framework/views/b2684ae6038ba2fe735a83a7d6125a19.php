        <div>
            <label for="<?php echo e($uuid); ?>" class="flex items-center gap-3 cursor-pointer font-semibold">

                <!--[if BLOCK]><![endif]--><?php if($right): ?>
                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(["flex-1" => !$tight]); ?>">
                        <?php echo e($label); ?>


                        <!--[if BLOCK]><![endif]--><?php if($attributes->get('required')): ?>
                            <span class="text-error">*</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <input id="<?php echo e($uuid); ?>" type="checkbox" <?php echo e($attributes->whereDoesntStartWith('class')); ?> <?php echo e($attributes->class(['toggle toggle-primary'])); ?>  />

                <!--[if BLOCK]><![endif]--><?php if(!$right): ?>
                    <?php echo e($label); ?>


                    <!--[if BLOCK]><![endif]--><?php if($attributes->get('required')): ?>
                        <span class="text-error">*</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </label>

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
        </div><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/d20a7f28617cbae399488a9a9128ddba.blade.php ENDPATH**/ ?>