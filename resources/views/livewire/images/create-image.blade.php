<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Intervention\Image\Drivers\GD\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use App\Models\Wall;
use App\Models\Image;

new class extends Component {

    use WithFileUploads, Toast;

    public Wall $wall; // Stocke le Wall correspondant au token

    public $image;
    public string $caption = '';

    public function mount(string $slug)
    {
        $this->wall = Wall::where('slug', $slug)->firstOrFail();
    }

    public function save()
    {
        $data = $this->validate([
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:' . $this->wall->caption_max_characters,
        ]);
    
        // Sauvegarde de l'image principale
        $path = $this->image->store('images', 'public');

        // Génération de la miniature
        
        // create new manager instance with desired driver
        $manager = new ImageManager(new Driver());

        $image = $manager->read($this->image->getRealPath())->scale(width: 500)->encode();
        Storage::disk('public')->put('thumbs/' . basename($path), $image);

        // Enregistrement en base de données
        Image::create([
            'wall_id' => $this->wall->id,
            'name' => $path,
            'thumb' => 'thumbs/' . basename($path),
            'caption' => $this->caption,
        ]);

        $this->success(__('Image added successfully!'));

        $this->reset('image', 'caption');
    }

}; 
?>   

<div class="h-screen flex items-center justify-center">
    <x-card class="flex items-center justify-center" title="{{ __('Post an image') }}">
        <x-form wire:submit="save"> 
            <x-file wire:model="image" label="{{__('Image')}}" hint="{{__('Only image formats allowed')}}" accept="image/png, image/jpeg"/>
            
            <x-progress wire:loading wire:target="image" class="progress-primary h-0.5" indeterminate />

            @if($image)
                <img src="{{ $image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover " />
            @endif

            @if ($wall->captions)
            <x-input label="{{__('Caption')}}" wire:model="caption" hint="Max : {{ $wall->caption_max_characters }}" maxlength="{{ $wall->caption_max_characters }}" />
            @endif

            <x-slot:actions>
                <x-button label="{{__('Send')}}" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
