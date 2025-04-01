    <div wire:key="<?php echo e($uuid); ?>">
        <div
            <?php echo e($attributes->class([
                    "flex justify-start items-center gap-4 px-3",
                    "hover:bg-base-200/50" => !$noHover,
                    "cursor-pointer" => $link
                ])); ?>

        >

            <!--[if BLOCK]><![endif]--><?php if($link && (data_get($item, $avatar) || !is_string($avatar))): ?>
                <div>
                    <a href="<?php echo e($link); ?>" wire:navigate>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- AVATAR -->
            <!--[if BLOCK]><![endif]--><?php if(data_get($item, $avatar)): ?>
                <div class="py-3">
                    <div class="avatar">
                        <div class="w-11 rounded-full">
                            <img src="<?php echo e(data_get($item, $avatar)); ?>" />
                        </div>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if(!is_string($avatar)): ?>
                <div <?php echo e($avatar->attributes->class(["py-3"])); ?>>
                    <?php echo e($avatar); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


            <!--[if BLOCK]><![endif]--><?php if($link && (data_get($item, $avatar) || !is_string($avatar))): ?>
                    </a>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- CONTENT -->
            <div class="flex-1 overflow-hidden whitespace-nowrap text-ellipsis truncate w-0 mary-hideable">
                <!--[if BLOCK]><![endif]--><?php if($link): ?>
                    <a href="<?php echo e($link); ?>" wire:navigate>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="py-3">
                    <div <?php if(!is_string($value)): ?> <?php echo e($value->attributes->class(["font-semibold truncate"])); ?> <?php else: ?> class="font-semibold truncate" <?php endif; ?>>
                        <?php echo e(is_string($value) ? data_get($item, $value) : $value); ?>

                    </div>

                    <div <?php if(!is_string($subValue)): ?>  <?php echo e($subValue->attributes->class(["text-gray-400 text-sm truncate"])); ?> <?php else: ?> class="text-gray-400 text-sm truncate" <?php endif; ?>>
                        <?php echo e(is_string($subValue) ? data_get($item, $subValue) : $subValue); ?>

                    </div>
                </div>

                <!--[if BLOCK]><![endif]--><?php if($link): ?>
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- ACTION -->
            <!--[if BLOCK]><![endif]--><?php if($actions): ?>
                <!--[if BLOCK]><![endif]--><?php if($link && !Str::of($actions)->contains([':click', '@click' , 'href'])): ?>
                    <a href="<?php echo e($link); ?>" wire:navigate>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div <?php echo e($actions->attributes->class(["py-3 flex items-center gap-3 mary-hideable"])); ?>>
                            <?php echo e($actions); ?>

                    </div>

                <!--[if BLOCK]><![endif]--><?php if($link && !Str::of($actions)->contains([':click', '@click' , 'href'])): ?>
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!--[if BLOCK]><![endif]--><?php if(!$noSeparator): ?>
            <hr class="border-base-300"/>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/e839b1a3fac6260e54f619fd18bfeba3.blade.php ENDPATH**/ ?>