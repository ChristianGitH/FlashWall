<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

?>

<div 
    x-data="{
        showImageZoomModal: false,
        modalImageUrl: '',
        showConfirmModal: false,
        modalTitle: '',
        modalMessage: '',
        modalConfirmText: '',
        modalConfirmClass: '',
        confirmAction: null
    }"
    @open-image-modal.window="modalImageUrl = $event.detail.url; showImageZoomModal = true"
    @confirm-action.window="
        modalTitle = $event.detail.title;
        modalMessage = $event.detail.message;
        modalConfirmText = $event.detail.confirmText;
        modalConfirmClass = $event.detail.confirmClass || 'bg-blue-600 hover:bg-blue-700';
        confirmAction = $event.detail.action;
        showConfirmModal = true;"
    x-cloak
>
    <div class="wall_data">
        <h1 class="text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-3xl lg:text-4xl dark:text-white">
            Modération : <?php echo e(__( $wall->name )); ?>

        </h1>
        <p class="text-sm font-normal text-gray-500 lg:text-base xl:text-lg dark:text-gray-400"><?php echo e(__( $wall->description )); ?></p>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($wall->moderation): ?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('walls.unprocessed-images', ['wall' => $wall]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1540227099-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('walls.approved-images', ['wall' => $wall]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1540227099-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('walls.archived-images', ['wall' => $wall]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1540227099-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php else: ?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('walls.all-images', ['wall' => $wall]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1540227099-3', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Zoom Image Modal -->
    <div 
        x-show="showImageZoomModal"
        @click="showImageZoomModal = false"
        x-transition 
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="shadow-lg overflow-auto relative">
            <div class="close-button-wrapper">
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-x-mark'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'showImageZoomModal = false','class' => 'btn btn-sm']); ?>
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
            </div>
            <img :src="modalImageUrl" alt="Image Preview" class="w-full h-auto mt-4" />
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div 
        x-show="showConfirmModal"
        x-transition
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 overflow-auto relative">
            <h2 class="text-lg font-semibold" x-text="modalTitle"></h2>
            <p class="mt-2 text-gray-600" x-text="modalMessage"></p>

            <div class="mt-4 flex justify-end space-x-2">
                <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    <?php echo e(__('Cancel')); ?>

                </button>
                <button @click="confirmAction(); showConfirmModal = false" class="px-4 py-2 text-white rounded" :class="modalConfirmClass">
                    <span x-text="modalConfirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\FlashWall\resources\views\livewire/walls/moderation.blade.php ENDPATH**/ ?>