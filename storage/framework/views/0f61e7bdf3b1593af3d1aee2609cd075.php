<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

?>

<div x-data="{ selected: [], allSelected: false,  errorMessage : '',
        showConfirmModal: false, 
        modalTitle: '', 
        modalMessage: '', 
        modalConfirmText: '', 
        modalConfirmClass: 'bg-blue-600 hover:bg-blue-700', 
        confirmAction: null,        
        showImageZoomModal: false,
        modalImageUrl: '' }" @reset-selection.window="selected = []; allSelected = false,  errorMessage = ''">

<div class="flex items-center justify-center">
<?php if (isset($component)) { $__componentOriginal7f194736b6f6432dc38786f292496c34 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7f194736b6f6432dc38786f292496c34 = $attributes; } ?>
<?php $component = Mary\View\Components\Card::resolve(['title' => 'Moderation is desactivated !','subtitle' => 'All images are beeing displayed.','separator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Card::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-96 shadow-md lg:-mt-7']); ?>
            <a href="../setup-wall/<?php echo e($wall->id); ?>"><?php if (isset($component)) { $__componentOriginal91586e22c1998368a30f831eea05043a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91586e22c1998368a30f831eea05043a = $attributes; } ?>
<?php $component = Mary\View\Components\Toggle::resolve(['label' => ''.e(__('Check Settings page to activate')).'','right' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Toggle::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'activate_moderation','disabled' => true,'inline' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91586e22c1998368a30f831eea05043a)): ?>
<?php $attributes = $__attributesOriginal91586e22c1998368a30f831eea05043a; ?>
<?php unset($__attributesOriginal91586e22c1998368a30f831eea05043a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91586e22c1998368a30f831eea05043a)): ?>
<?php $component = $__componentOriginal91586e22c1998368a30f831eea05043a; ?>
<?php unset($__componentOriginal91586e22c1998368a30f831eea05043a); ?>
<?php endif; ?></a>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7f194736b6f6432dc38786f292496c34)): ?>
<?php $attributes = $__attributesOriginal7f194736b6f6432dc38786f292496c34; ?>
<?php unset($__attributesOriginal7f194736b6f6432dc38786f292496c34); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7f194736b6f6432dc38786f292496c34)): ?>
<?php $component = $__componentOriginal7f194736b6f6432dc38786f292496c34; ?>
<?php unset($__componentOriginal7f194736b6f6432dc38786f292496c34); ?>
<?php endif; ?>
</div>

<div class="galery_data">
    <h2><?php echo e(__( 'All images' )); ?></h2>
</div>

<div class="bulk-actions flex items-center">
    <button class="btn btn-sm" @click="allSelected = !allSelected; selected = allSelected ? [...document.querySelectorAll('.unprocessed-image-checkbox')].map(cb => cb.value) : []">
        <label for="select-all-checkbox" @click="allSelected = !allSelected; selected = allSelected ? [...document.querySelectorAll('.unprocessed-image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">Select All</label>
        <input 
            type="checkbox"
            id="select-all-checkbox"
            class="checkbox"
            x-model="allSelected"
        />
    </button>

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
                    let textPlural = selected.length === 1 ? \''.e(__('image')).'\' : \''.e(__('images')).'\';
                    $dispatch(\'confirm-action\', {
                        title: \''.e(__('Delete images')).'\',
                        message: \''.e(__('You are about to delete')).' \' + selected.length + \' \' + textPlural + \'.\',
                        confirmText: \''.e(__('Yes, delete !')).'\',
                        confirmClass: \'bg-red-600 hover:bg-red-700\',
                        action: () => $wire.call(\'deleteSelected\', selected)
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
    <!--[if BLOCK]><![endif]--><?php if($this->images->isEmpty()): ?>
        <p class="text-center text-gray-500"><?php echo e(__('No image pending.')); ?></p>
    <?php else: ?>
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                class="checkbox checkbox-sm unprocessed-image-checkbox"
                :value="<?php echo e($image->id); ?>"
                x-model="selected"
                id="checkbox-<?php echo e($image->id); ?>"
            />
            </div>
                <label for="checkbox-<?php echo e($image->id); ?>" display="block">
                    <img src="<?php echo e(asset('storage/' . $image->thumb)); ?>" />
                </label>
            <div class="moderation_buttons flex justify-between">
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
    <?php echo e($this->images->links(data: ['scrollTo' => false])); ?>

</div>

</div><?php /**PATH C:\laragon\www\FlashWall\resources\views\livewire/walls/all-images.blade.php ENDPATH**/ ?>