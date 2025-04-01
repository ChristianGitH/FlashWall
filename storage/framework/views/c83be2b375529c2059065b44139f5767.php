<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

?>

<div x-data="{ selected: [], allSelected: false,  errorMessage : '',
        showConfirmModal: false, 
        modalTitle: '', 
        modalMessage: '', 
        modalConfirmText: '', 
        modalConfirmClass: 'bg-blue-600 hover:bg-blue-700', 
        confirmAction: null }" @reset-selection.window="selected = []; allSelected = false,  errorMessage = ''">

<div class="galery_data">
    <h2><?php echo e(__( 'Pending images' )); ?></h2>
</div>

<div class="bulk-actions flex items-center">
    <button class="btn btn-sm" @click="allSelected = !allSelected; selected = allSelected ? [...document.querySelectorAll('.image-checkbox')].map(cb => cb.value) : []">
        <label for="select-all-checkbox" @click="allSelected = !allSelected; selected = allSelected ? [...document.querySelectorAll('.image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">Select All</label>
        <input 
            type="checkbox"
            id="select-all-checkbox"
            class="checkbox"
            x-model="allSelected"
        />
    </button>

    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-check','tooltip' => ''.e(__('Approve Selected')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => '
                if (selected.length === 0) { 
                    errorMessage = \''.e(__('No images selected.')).'\'; 
                    setTimeout(() => errorMessage = \'\', 1500);
                } else {
                    modalTitle = \''.e(__('Approve Images')).'\';
                    modalMessage = \''.e(__('You are about to approve')).' \' + selected.length + \' '.e(__('images.')).'\';
                    modalConfirmText = \''.e(__('Yes, approve !')).'\';
                    modalConfirmClass = \'bg-green-600 hover:bg-green-700\';
                    showConfirmModal = true; 
                    confirmAction = () => { 
                        errorMessage = \'\'; 
                        $wire.call(\'approveSelected\', selected);
                        showConfirmModal = false;
                    };
                }
            ','class' => 'btn btn-sm','aria-label' => ''.e(__('Approve Selected')).'']); ?>
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
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-archive-box','tooltip' => ''.e(__('Archive Selected')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => '','class' => 'btn btn-sm','aria-label' => ''.e(__('Archive Selected')).'']); ?>
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
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-trash','tooltip' => ''.e(__('Delete Selected')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => '
                if (selected.length === 0) { 
                    errorMessage = \''.e(__('No images selected.')).'\'; 
                    setTimeout(() => errorMessage = \'\', 1500);
                } else {
                    modalTitle = \''.e(__('Delete images')).'\';
                    modalMessage = \''.e(__('You are about to delete')).' \' + selected.length + \' '.e(__('images.')).'\';
                    modalConfirmText = \''.e(__('Yes, delete !')).'\';
                    modalConfirmClass = \'bg-red-600 hover:bg-red-700\';
                    showConfirmModal = true; 
                    confirmAction = () => { 
                        errorMessage = \'\'; 
                        $wire.call(\'deleteSelected\', selected);
                        showConfirmModal = false;
                    };
                }
            ','class' => 'btn btn-sm btn-danger','wire:click.prevent' => '','aria-label' => ''.e(__('Delete Selected')).'']); ?>
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
    <!-- Message d'erreur affiché dynamiquement -->
    <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 mt-2 transition-opacity duration-500"></p>
    <p wire:loading>Please wait...</p>
    
</div>


<div class="gallery_wrapper">

    <!--[if BLOCK]><![endif]--><?php if($this->images()->isEmpty()): ?>
        <p class="text-center text-gray-500"><?php echo e(__('No image pending.')); ?></p>
    <?php else: ?>
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->images(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <!--[if BLOCK]><![endif]--><?php if($image->caption): ?>
        <div class="image_wrapper tooltip tooltip-bottom" data-tip="<?php echo e(__( $image->caption )); ?>" wire:key="image-<?php echo e($image->id); ?>">
            <div class="uper_image_data justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
        <?php else: ?>
        <div class="image_wrapper" wire:key="image-<?php echo e($image->id); ?>">
            <div class="uper_image_data justify-end">
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <input 
                type="checkbox" 
                class="checkbox checkbox-sm image-checkbox"
                :value="<?php echo e($image->id); ?>"
                x-model="selected"
            />
            </div>
                <a href="<?php echo e(asset('storage/' . $image->name)); ?>" class="block">
                    <img src="<?php echo e(asset('storage/' . $image->thumb)); ?>" />
                </a>
            <div class="moderation_buttons flex justify-between">
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-check','tooltip' => ''.e(__('Approve image')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'approveImage('.e($image->id).')','class' => 'btn btn-sm','aria-label' => ''.e(__('Approve image')).'','@click' => '$wire.set(\'selectedImages\', selected)']); ?>
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
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-archive-box','tooltip' => ''.e(__('Archive Selected')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'archiveSelected','class' => 'btn btn-sm','aria-label' => ''.e(__('Archive Selected')).'','@click' => '$wire.set(\'selectedImages\', selected)']); ?>
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
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-trash','tooltip' => ''.e(__('Delete image')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click.prevent' => '','class' => 'btn btn-sm btn-danger','aria-label' => ''.e(__('Delete image')).'','@click' => '
                    modalTitle = \''.e(__('Delete image')).'\';
                    modalMessage = \''.e(__('Are you sure you want to delete this image?')).'\';
                    modalConfirmText = \''.e(__('Yes, delete!')).'\';
                    modalConfirmClass = \'bg-red-600 hover:bg-red-700\';
                    confirmAction = () => { 
                        $wire.call(\'deleteImage\', '.e($image->id).');
                        showConfirmModal = false;
                    };
                    showConfirmModal = true;
                    ']); ?>
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
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>


    <!-- Fenêtre modale dynamique (Validation & Suppression) -->
    <div x-show="showConfirmModal" x-transition class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 overflow-auto relative">
            <h2 class="text-lg font-semibold" x-text="modalTitle"></h2>
            <p class="mt-2 text-gray-600" x-text="modalMessage"></p>

            <div class="mt-4 flex justify-end space-x-2">
                <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    <?php echo e(__('Cancel')); ?>

                </button>
                <button @click="confirmAction()" class="px-4 py-2 text-white rounded" :class="modalConfirmClass">
                    <span x-text="modalConfirmText"></span>
                </button>
            </div>
        </div>
    </div>

</div><?php /**PATH C:\laragon\www\FlashWall\resources\views\livewire/walls/unprocessed-images.blade.php ENDPATH**/ ?>