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

    #[Rule('required|image|max:5120')]
    public $photo;

    #[Rule('nullable|string|max:255')]
    public string $caption = '';

    public function mount(string $slug)
    {
        $this->wall = Wall::where('slug', $slug)->firstOrFail();
    }

    public function save()
    {
        $data = $this->validate();

        // Sauvegarde de l'image principale
        $path = $this->photo->store('images', 'public');

        // Génération de la miniature
        
        // create new manager instance with desired driver
        $manager = new ImageManager(new Driver());

        $image = $manager->read($this->photo->getRealPath())->scale(width: 500)->encode();
        Storage::disk('public')->put('thumbs/' . basename($path), $image);

        // Enregistrement en base de données
        Image::create([
            'wall_id' => $this->wall->id,
            'name' => $path,
            'thumb' => 'thumbs/' . basename($path),
            'caption' => $this->caption,
        ]);

        $this->success(__('Image added successfully!'));

        $this->reset('photo', 'caption');
    }

}; 
?>   

<div class="h-screen flex items-center justify-center">
    <x-card class="h-screen flex items-center justify-center" title="{{ __('Add Image to') }} {{ $wall->name }}">
        <x-form wire:submit="save"> 
            <x-file wire:model="photo" label="{{__('Image')}}" hint="{{__('Only image formats allowed')}}" accept="image/png, image/jpeg"/>
            
            @if($photo)
                <img src="{{ $photo->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover " />
            @endif

            <x-input label="{{__('Caption')}}" wire:model="caption" hint="{{__('Caption')}}" />
            
            <x-slot:actions>
                <x-button label="{{__('Save')}}" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
