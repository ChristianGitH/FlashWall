<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;

?>

<div class="w-screen h-screen">

<?php
    $approvedImages = $this->approvedImages();
    $displaySettings = $this->wallSettingsMount();
?>

<!--[if BLOCK]><![endif]--><?php if($approvedImages->isEmpty()): ?>
    <p class="text-center text-gray-500"><?php echo e(__('No image.')); ?></p>
<?php else: ?>

<div
    x-data="{
        currentSlide: 0,
        slides: <?php echo e($approvedImages->count()); ?>,
        init() {
            setInterval(() => {
                this.currentSlide = (this.currentSlide + 1) % this.slides;
            }, <?php echo e($displaySettings['duration']); ?>);
        }
    }"
    class="relative w-full h-screen flex items-center justify-center" style="<?php echo e($displaySettings['background']); ?>"
>
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $approvedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div
            x-show="currentSlide === <?php echo e($index); ?>"
            class="absolute inset-0 flex items-center justify-center text-center"
            wire:key="image-<?php echo e($image->id); ?>"
            style="margin: 1% 1%;"
        >
            <img
                src="<?php echo e(asset('storage/' . $image->name)); ?>"
                class="object-contain" style="max-height: <?php echo e($displaySettings['image_max_height']); ?>%; max-width: <?php echo e($displaySettings['image_max_width']); ?>%;"
            />
            <!--[if BLOCK]><![endif]--><?php if($image->caption): ?>
                <span class="absolute p-[3px] bg-white/70 font-semibold rounded-md" style="bottom: <?php echo e($displaySettings['caption_margin_bottom']); ?>%; max-width: <?php echo e($displaySettings['caption_max_width']); ?>%;"><?php echo e($image->caption); ?></span>
            <?php else: ?>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
</div>

<?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div><?php /**PATH C:\laragon\www\FlashWall\resources\views\livewire/walls/display-images.blade.php ENDPATH**/ ?>