     <div
        x-data="{
            progress: 0,
            cropper: null,
            justCropped: false,
            fileChanged: false,
            imagePreview: null,
            imageCrop: null,
            originalImageUrl: null,
            cropAfterChange: <?php echo e(json_encode($cropAfterChange)); ?>,
            file: <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?>,
            init () {
                this.imagePreview = this.$refs.preview?.querySelector('img')
                this.imageCrop = this.$refs.crop?.querySelector('img')
                this.originalImageUrl = this.imagePreview?.src

                this.$watch('progress', value => {
                    if (value == 100 && this.cropAfterChange && !this.justCropped) {
                        this.crop()
                    }
                })
            },
            get processing () {
                return this.progress > 0 && this.progress < 100
            },
            close() {
                $refs.maryCrop.close()
                this.cropper?.destroy()
            },
            change() {
                if (this.processing) {
                    return
                }

                this.$refs.file.click()
            },
            refreshImage() {
                this.progress = 1
                this.justCropped = false

                if (this.imagePreview?.src) {
                    this.imagePreview.src = URL.createObjectURL(this.$refs.file.files[0])
                    this.imageCrop.src = this.imagePreview.src
                }
            },
            crop() {
                $refs.maryCrop.showModal()
                this.cropper?.destroy()

                this.cropper = new Cropper(this.imageCrop, <?php echo e($cropSetup()); ?>);
            },
            revert() {
                 $wire.$removeUpload('<?php echo e($attributes->wire('model')->value); ?>', this.file.split('livewire-file:').pop(), () => {
                    this.imagePreview.src = this.originalImageUrl
                 })
            },
            async save() {
                $refs.maryCrop.close();

                this.progress = 1
                this.justCropped = true

                this.imagePreview.src = this.cropper.getCroppedCanvas().toDataURL()
                this.imageCrop.src = this.imagePreview.src

                this.cropper.getCroppedCanvas().toBlob((blob) => {
                    blob.name = $refs.file.files[0].name
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').upload('<?php echo e($attributes->wire('model')->value); ?>', blob,
                        (uploadedFilename) => {  },
                        (error) => {  },
                        (event) => { this.progress = event.detail.progress }
                    )
                }, '<?php echo e($cropMimeType); ?>')
            }
         }"

        x-on:livewire-upload-progress="progress = $event.detail.progress;"

        <?php echo e($attributes->whereStartsWith('class')); ?>

    >
        <!-- STANDARD LABEL -->
        <!--[if BLOCK]><![endif]--><?php if($label): ?>
            <label for="<?php echo e($uuid); ?>" class="pt-0 label label-text font-semibold">
                <span>
                    <?php echo e($label); ?>


                    <!--[if BLOCK]><![endif]--><?php if($attributes->get('required')): ?>
                        <span class="text-error">*</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </span>
            </label>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- PROGRESS BAR  -->
        <!--[if BLOCK]><![endif]--><?php if(! $hideProgress && $slot->isEmpty()): ?>
            <div class="h-1 -mt-5 mb-5">
                <progress
                    x-cloak
                    :class="!processing && 'hidden'"
                    :value="progress"
                    max="100"
                    class="progress progress-success h-1 w-56"></progress>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- FILE INPUT -->
        <input
            id="<?php echo e($uuid); ?>"
            type="file"
            x-ref="file"
            @change="refreshImage()"

            <?php echo e($attributes->whereDoesntStartWith('class')->class([
                    "file-input file-input-bordered file-input-primary",
                    "hidden" => $slot->isNotEmpty()
                ])); ?>

        />

        <!--[if BLOCK]><![endif]--><?php if($slot->isNotEmpty()): ?>
            <!-- PREVIEW AREA -->
            <div x-ref="preview" class="relative flex">
                <div
                    wire:ignore
                    @click="change()"
                    :class="processing && 'opacity-50 pointer-events-none'"
                    class="cursor-pointer hover:scale-105 transition-all tooltip"
                    data-tip="<?php echo e($changeText); ?>"
                >
                    <?php echo e($slot); ?>

                </div>
                <!-- PROGRESS -->
                <div
                    x-cloak
                    :style="`--value:${progress}; --size:1.5rem; --thickness: 4px;`"
                    :class="!processing && 'hidden'"
                    class="radial-progress text-success absolute top-5 start-5 bg-neutral"
                    role="progressbar"
                ></div>
            </div>

            <!-- CROP MODAL -->
            <div @click.prevent="" x-ref="crop" wire:ignore>
                <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['id' => 'maryCrop'.e($uuid).'','title' => $cropTitleText,'separator' => true,'persistent' => true,'withoutTrapFocus' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-ref' => 'maryCrop','class' => 'backdrop-blur-sm','@keydown.window.esc.prevent' => '']); ?>
                    <img src="" />
                     <?php $__env->slot('actions', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => $cropCancelText] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'close()']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => $cropSaveText] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-primary','@click' => 'save()',':disabled' => 'processing']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

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

        <!-- MULTIPLE -->
        <!--[if BLOCK]><![endif]--><?php $__errorArgs = [$modelName().'.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="text-red-500 label-text-alt p-1 pt-2"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

        <!-- HINT -->
        <!--[if BLOCK]><![endif]--><?php if($hint): ?>
            <div class="<?php echo e($hintClass); ?>" x-classes="label-text-alt text-gray-400 py-1 pb-0"><?php echo e($hint); ?></div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div><?php /**PATH C:\laragon\www\FlashWall\storage\framework\views/40a4c35cae544d847a26d9e6305b1a05.blade.php ENDPATH**/ ?>