<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;

new class extends Component {

    public Wall $wall;

    public function mount(string $slug)
    {
        $this->wall = Wall::where('slug', $slug)->firstOrFail();
    }

public function approvedImages()
{
    if ($this->wall->moderation) {
        return $this->wall->images()
                    ->where('approved', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
    } else {
        return $this->wall->images()
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}

}; ?>

<div class="w-screen h-screen">

<?php
    $approvedImages = $this->approvedImages();
?>

<?php if($approvedImages->isEmpty()): ?>
    <p class="text-center text-gray-500"><?php echo e(__('No image.')); ?></p>
<?php else: ?>
<div
    x-data="{
        currentSlide: 0,
        slides: <?php echo e($approvedImages->count()); ?>,
        init() {
            setInterval(() => {
                this.currentSlide = (this.currentSlide + 1) % this.slides;
            }, 5000);
        }
    }"
    class="relative w-full h-screen flex items-center justify-center"
>
    <?php $__currentLoopData = $approvedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div
            x-show="currentSlide === <?php echo e($index); ?>"
            class="absolute inset-0 flex items-center justify-center"
            wire:key="image-<?php echo e($image->id); ?>"
        >
            <img
                src="<?php echo e(asset('storage/' . $image->thumb)); ?>"
                class="max-w-full max-h-full object-contain"
            />
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php endif; ?>

</div><?php /**PATH C:\laragon\www\FlashWall\resources\views///livewire/walls/display-images.blade.php ENDPATH**/ ?>