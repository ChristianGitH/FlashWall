<?php

use App\Models\Wall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Title('Create a Wall')]

class extends Component {
 
    #[Rule('required|string|max:255|unique:walls,name')]
    public string $name = '';
    #[Rule('required|string|max:255|unique:walls,slug')]
    public string $slug = '';
    #[Rule('string|max:255')]
    public string $description = '';
    #[Rule('nullable|integer|max:99')]
    public ?int $max_images_user;
    #[Rule('boolean')]
    public string $allow_captions = '';
    #[Rule('boolean')]
    public string $activate_moderation = '';


    public function createWall()
    {
        $data = $this->validate();

        Wall::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'user_id' => Auth::id(),
            'max_images_user' => $data['max_images_user'],
            'captions' => $data['allow_captions'],
            'moderation' => $data['activate_moderation'],
        ]);

    
        session()->flash('message', __('Wall created successfully.'));
        return redirect('/'); 
    }

}; ?>

<div>
    <x-card class="h-screen flex items-center justify-center" title="{{__('Create a Wall')}}" shadow separator>

        @if (session()->has('message'))
            <x-alert color="success">{{ session('message') }}</x-alert>
        @endif

        <x-form wire:submit="createWall">
            <x-input label="{{__('Name')}}" wire:model="name"  inline />
            <x-input label="{{__('Slug')}}" wire:model="slug" icon="o-link" inline />
            <x-input label="{{__('Description')}}" wire:model="description" inline separator />
            <x-menu-separator />
            <x-input label="{{__('Max images per user')}}" wire:model="max_images_user" inline />
            <x-toggle label="{{__('Allow captions?')}}" wire:model="allow_captions" right inline/>
            <x-toggle label="{{__('Activate moderation?')}}" wire:model="activate_moderation" right inline/>

            <x-slot:actions>
                <x-button label="{{__('Créer')}}" type="submit" icon="o-plus" class="btn-primary" spinner="createWall" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>