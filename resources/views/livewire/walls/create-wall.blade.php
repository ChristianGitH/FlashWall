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
 
    #[Rule('required|string|max:255')]
    public string $name = '';
    #[Rule('string|max:255')]
    public string $description = '';
    #[Rule('boolean')]
    public bool  $allow_captions = false;
    #[Rule('boolean')]
    public bool $activate_moderation = false;


    public function createWall()
    {
        $data = $this->validate();

        Wall::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'user_id' => Auth::id(),
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
            <x-input label="{{__('Description')}}" wire:model="description" inline separator />
            <x-menu-separator />
            <x-toggle label="{{__('Allow captions?')}}" wire:model="allow_captions" right inline/>
            <x-toggle label="{{__('Activate moderation?')}}" wire:model="activate_moderation" right inline/>

            <x-slot:actions>
                <x-button label="{{__('Créer')}}" type="submit" icon="o-plus" class="btn-primary" spinner="createWall" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>