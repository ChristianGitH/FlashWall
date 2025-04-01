<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;


new class extends Component {
    use Toast;
    use WithPagination, WithoutUrlPagination;

    public Wall $wall;
        
}; ?>

<div>

<div class="wall_data">
    <h1>{{ __( $wall->name ) }}</h1>
    <h3>{{ __( $wall->description ) }}</h3>
</div>


<livewire:walls.unprocessed-images :wall="$wall" />

<livewire:walls.approved-images :wall="$wall" />

<livewire:walls.archived-images :wall="$wall" />

</div>