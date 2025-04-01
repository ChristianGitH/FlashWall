    <div
        <?php echo e($attributes
                ->merge(['wire:key' => $uuid ])
                ->class(['card bg-base-100 rounded-lg p-5', 'shadow-sm' => $shadow])); ?>

    >
        <!--[if BLOCK]><![endif]--><?php if($figure): ?>
            <figure <?php echo e($figure->attributes->class(["mb-5 -m-5"])); ?>>
                <?php echo e($figure); ?>

            </figure>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($title || $subtitle): ?>
            <div class="pb-5">
                <div class="flex justify-between items-center">
                    <div>
                        <!--[if BLOCK]><![endif]--><?php if($title): ?>
                            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["text-2xl font-bold", is_string($title) ? '' : $title?->attributes->get('class') ]); ?>" >
                                <?php echo e($title); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($subtitle): ?>
                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["text-gray-500 text-sm mt-1", is_string($subtitle) ? '' : $subtitle?->attributes->get('class') ]); ?>" >
                                <?php echo e($subtitle); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($menu): ?>
                        <div <?php echo e($menu->attributes->class(["flex items-center gap-2"])); ?>> <?php echo e($menu); ?> </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!--[if BLOCK]><![endif]--><?php if($separator): ?>
                    <hr class="mt-3" />

                    <!--[if BLOCK]><![endif]--><?php if($progressIndicator): ?>
                        <div class="h-0.5 -mt-4 mb-4">
                            <progress
                                class="progress progress-primary w-full h-0.5 dark:h-1"
                                wire:loading

                                <?php if($progressTarget()): ?>
                                    wire:target="<?php echo e($progressTarget()); ?>"
                                 <?php endif; ?>></progress>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div>
            <?php echo e($slot); ?>

        </div>

        <!--[if BLOCK]><![endif]--><?php if($actions): ?>
            <!--[if BLOCK]><![endif]--><?php if($separator): ?>
                <hr class="mt-5" />
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="flex justify-end gap-3 p-3">
                <?php echo e($actions); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/632348c7e8933d3b67e4b1aa73098be8.blade.php ENDPATH**/ ?>