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

<div>

<div class="wall_data">
    <h1><?php echo e(__( $wall->name )); ?></h1>
    <h3><?php echo e(__( $wall->description )); ?></h3>
</div>


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

</div><?php /**PATH C:\laragon\www\FlashWall\resources\views\livewire/walls/moderation.blade.php ENDPATH**/ ?>