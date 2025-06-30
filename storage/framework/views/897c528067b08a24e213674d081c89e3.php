<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

?>

<div x-data="{ selectedApproved: [], allSelectedApproved: false, errorMessage : '',
        showConfirmModal: false, 
        modalTitle: '', 
        modalMessage: '', 
        modalConfirmText: '', 
        modalConfirmClass: 'bg-blue-600 hover:bg-blue-700', 
        confirmAction: null,
        showImageZoomModal: false,
        modalImageUrl: '' }" @reset-selection-approved.window="selectedApproved = []; allSelectedApproved = false">

<div class="galery_data">
    <h2><?php echo e(__( 'Approved images' )); ?></h2>
</div>

<div class="bulk-actions flex items-center">
    <button class="btn btn-sm" @click="allSelectedApproved = !allSelectedApproved; selectedApproved = allSelectedApproved ? [...document.querySelectorAll('.approved-image-checkbox')].map(cb => cb.value) : []">
        <label for="approved-select-all-checkbox" @click="allSelectedApproved = !allSelectedApproved; selectedApproved = allSelectedApproved ? [...document.querySelectorAll('.approved-image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">Select All</label>
        <input 
            type="checkbox"
            id="approved-select-all-checkbox"
            class="checkbox"
            x-model="allSelectedApproved"
        />
    </button>

    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-archive-box','tooltip' => ''.e(__('Archive selected')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'if (selectedApproved.length === 0) { 
                    errorMessage = \''.e(__('No images selected.')).'\'; 
                    setTimeout(() => errorMessage = \'\', 1500);
                } else {
                    let textPlural = selectedApproved.length === 1 ? \''.e(__('image')).'\' : \''.e(__('images')).'\';
                    $dispatch(\'confirm-action\', {
                        title: \''.e(__('Archive images')).'\',
                        message: \''.e(__('You are about to archive')).' \' + selectedApproved.length + \' \' + textPlural + \'.\',
                        confirmText: \''.e(__('Yes, archive!')).'\',
                        confirmClass: \'bg-blue-600 hover:bg-blue-700\',
                        action: () => $wire.call(\'archiveSelected\', selectedApproved)
                    })
                }
            ','class' => 'btn btn-sm','aria-label' => ''.e(__('Archive selected')).'']); ?>
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
                if (selectedApproved.length === 0) { 
                    errorMessage = \''.e(__('No images selected.')).'\'; 
                    setTimeout(() => errorMessage = \'\', 1500);
                } else {
                    let textPlural = selectedApproved.length === 1 ? \''.e(__('image')).'\' : \''.e(__('images')).'\';
                    $dispatch(\'confirm-action\', {
                        title: \''.e(__('Delete images')).'\',
                        message: \''.e(__('You are about to delete')).' \' + selectedApproved.length + \' \' + textPlural + \'.\',
                        confirmText: \''.e(__('Yes, delete !')).'\',
                        confirmClass: \'bg-red-600 hover:bg-red-700\',
                        action: () => $wire.call(\'deleteSelected\', selectedApproved)
                    })
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

    <!--[if BLOCK]><![endif]--><?php if($this->approvedImages()->isEmpty()): ?>
        <p class="text-center text-gray-500"><?php echo e(__('No approved image.')); ?></p>
    <?php else: ?>
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->approvedImages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        if ($image->caption) {
            $data1 = "tooltip tooltip-bottom";
            $data2 = "$image->caption";
            $data3 = "";
        } else {
            $data1 = "";
            $data2 = "";
            $data3 = "hidden";
        }
    ?>
        <div class="image_wrapper <?php echo e(( $data1 )); ?>" data-tip="<?php echo $data2; ?>" wire:key="image-<?php echo e($image->id); ?>">
            <div class="uper_image_data justify-between">
                <a role="button" @click="$dispatch('open-image-modal', { url: '<?php echo e(asset('storage/' . $image->name)); ?>' })">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                    </svg>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 <?php echo e(( $data3 )); ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
            <input 
                type="checkbox" 
                class="checkbox checkbox-sm approved-image-checkbox"
                :value="<?php echo e($image->id); ?>"
                x-model="selectedApproved"
                id="checkbox-<?php echo e($image->id); ?>"
            />
            </div>
                <label for="checkbox-<?php echo e($image->id); ?>" display="block">
                    <img src="<?php echo e(asset('storage/' . $image->thumb)); ?>" />
                </label>
            <div class="moderation_buttons flex justify-between">
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-archive-box','tooltip' => ''.e(__('Archive Selected')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'archiveImage('.e($image->id).')','class' => 'btn btn-sm','aria-label' => ''.e(__('Archive Selected')).'','@click' => '$wire.set(\'selectedImages\', selectedApproved)']); ?>
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
                        $dispatch(\'confirm-action\', {
                            title: \''.e(__('Delete image')).'\',
                            message: \''.e(__('Are you sure you want to delete this image?')).'\',
                            confirmText: \''.e(__('Yes, delete!')).'\',
                            confirmClass: \'bg-red-600 hover:bg-red-700\',
                            action: () => $wire.call(\'deleteImage\', '.e($image->id).')
                        })
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
<div class="galerie-navigation flex justify-evenly">
    <?php echo e($this->approvedImages()->links(data: ['scrollTo' => false])); ?>

</div>

</div><?php /**PATH C:\laragon\www\FlashWall\resources\views\livewire/walls/approved-images.blade.php ENDPATH**/ ?>