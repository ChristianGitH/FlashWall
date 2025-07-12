<?php

use App\Models\Wall;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Mary\Traits\Toast;


new
#[Title('Setup Wall')]

class extends Component {
    use Toast, WithFileUploads;
    
    public Wall $wall;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $max_images_user;
    public bool $captions = false;
    public bool $moderation = false;
    public string $color;
    public int $background_choice;
    public string $background_image;
    public $new_background_image;
    


    public function mount(Wall $wall)
    {
        $this->wall = $wall;
        $this->name = $wall->name;
        $this->slug = $wall->slug;
        if($wall->description) {
            $this->description = $wall->description;
        }
        if($wall->max_images_user) {
            $this->max_images_user = $wall->max_images_user;
        }
        $this->captions = $wall->captions;
        $this->moderation = $wall->moderation;
        $this->color = $wall->background_color;
        $this->background_choice = $wall->background_choice;
        $this->background_image = $wall->background_image;
    }

    public function updateWall()
    {
        $data = $this->validate([
            'name' => 'required|string|max:255|unique:walls,name,' . $this->wall->id,
            'slug' => 'required|string|max:255|unique:walls,slug,' . $this->wall->id,
            'description' => 'nullable|string|max:255',
            'max_images_user' => 'nullable|integer|max:99',
            'captions' => 'boolean',
            'moderation' => 'boolean',
        ]);
    
        // Filtrer uniquement les champs modifiés
        // Old code before using fill : $changes = array_filter($data, fn ($value, $key) => $this->wall->$key !== $value, ARRAY_FILTER_USE_BOTH);
        // Remplit le modèle avec les données validées
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));

        } else {
            $this->warning(__('No change detected!'));
        }
    }


    public function updateWallDisplayBackground()
    {
        $data = $this->validate([
            'color' => 'required|string|max:255',
            'new_background_image' => 'nullable|image|max:5120',
            'background_choice' => 'required|integer|max:2',
        ]);

        if ($this->new_background_image) {
            // Suppression de l'ancienne image puis sauvegarde de la nouevlle image
            \Storage::disk('public')->delete($this->wall->background_image);
            $background_image_path = $this->new_background_image->store('background_images', 'public');
            $this->wall->background_image = $background_image_path;
        }
        
        // On prépare les changements sur le modèle (sauf l'image)
        // Remplit le modèle avec les données validées
        $this->wall->background_color = $this->color;
        $this->wall->background_choice = $this->background_choice;
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
    }



    public function deleteWall()
    {
        if ($this->wall->user_id !== Auth::id()) {
            abort(403, 'Forbidden action!');
        }

        $this->wall->delete();

        session()->flash('message', 'Wall deleted!');
        return redirect()->route('create-wall'); 
    }

};

?>
<div>
    <h1 class="font-bold text-2xl mb-3 me-3 text-gray-700">
            Update wall : {{ __( $wall->name ) }}
    </h1>

<div class="flex items-start justify-center gap-7 flex-wrap">
    <x-card title="{{ __('Update Wall') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateWall">
            <x-input label="{{ __('Name') }}" wire:model="name" />
            <x-input label="{{ __('Slug') }}" wire:model="slug" icon="o-link"/>
            <x-input label="{{ __('Description') }}" wire:model="description" />
            <x-menu-separator />
            <x-input label="{{__('Max images per user')}}" wire:model="max_images_user" inline />
            <x-toggle label="{{__('Allow captions?')}}" wire:model="captions" right inline/>
            <x-toggle label="{{__('Activate moderation?')}}" wire:model="moderation" right inline/>

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWall" />
            </x-slot:actions>
        </x-form>

        <x-button label="Delete" icon="o-trash" class="btn-danger"
            wire:click="deleteWall" 
            wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE" />
    </x-card>
    
    <x-card title="{{ __('Background') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateWallDisplayBackground">
            <div x-data="{ color: @entangle('color') }"class="flex flex-row items-end justify-evenly">
                <x-input class="w-full" label="Background color" x-model="color" />

                <input
                    type="color"
                    x-model="color"
                    wire:model="color"
                    class="p-1 h-10 w-14 bg-white border border-gray-200 cursor-pointer rounded-lg"
                    title="Choose a color"
                >
            </div>

            <div class="max-w-full overflow-hidden">
                <x-file style="max-width: 100% !important" wire:model="new_background_image" label="{{ __('Background-image') }}" 
                    hint="{{ __('Only image formats allowed') }}"
                    accept="image/png, image/jpeg"
                />
            </div>
            <x-progress wire:loading wire:target="new_background_image" class="progress-primary h-0.5" indeterminate />
            @if($new_background_image)
                <img src="{{ $new_background_image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover " />
            @elseif($background_image)
                <img src="{{ asset('storage/' . $wall->background_image) }}" class="max-w-xs mx-auto shadow-md object-cover " />
            @endif

            @php
                $options = [
                    ['custom_key' => 1 , 'name' => 'Image'],
                    ['custom_key' => 0 , 'name' => 'Color'],
                ];
            @endphp
            <div class="flex justify-center">
                <x-radio label="Use as background:" wire:model="background_choice" :options="$options" option-value="custom_key" inline center />
            </div>

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWallDisplayBackground" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
</div>
