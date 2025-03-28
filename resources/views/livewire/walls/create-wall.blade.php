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

    public function createWall()
    {
        $data = $this->validate();

        Wall::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'user_id' => Auth::id(),
        ]);

    
        $this->success(__('Wall created successfully.'), redirectTo: "/setup-wall/{$this->wall->id}");
    }

}; ?>

<div>
    <x-card class="h-screen flex items-center justify-center" title="{{__('Create a Wall')}}" shadow separator>

        @if (session()->has('message'))
            <x-alert color="success">{{ session('message') }}</x-alert>
        @endif

        <x-form wire:submit="createWall">
            <x-input label="{{__('Name')}}" wire:model="name" icon="o-folder" inline />
            <x-input label="{{__('Slug')}}" wire:model="slug" icon="o-folder" inline />
            <x-input label="{{__('Description')}}" wire:model="description" icon="o-folder" inline />


            <x-slot:actions>
                <x-button label="{{__('Créer')}}" type="submit" icon="o-plus" class="btn-primary" spinner="createWall" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>