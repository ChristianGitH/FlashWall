<?php

use App\Models\Wall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
#[Title('Create a Wall')]

class extends Component {
    use Toast;
 
    #[Rule('required|string|max:30')]
    public string $name = '';
    #[Rule('string|max:100')]
    public string $description = '';

    public function createWall()
    {
        $data = $this->validate();

        // Generate a base slug from the name
        $slug = Str::slug($data['name']);

        // Ensure uniqueness by appending a random string if the slug already exists
        while (Wall::where('slug', $slug)->exists()) {
            $slug = Str::slug($data['name']) . '-' . Str::random(5);
        }

        Wall::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'],
            'user_id' => Auth::id(),
        ]);

        $link = 'setup-wall/'.$slug;

        $this->success(
            __('Wall successfully created!'),
            redirectTo: '/'.$link
        );
    }

}; ?>

<div>
    <x-card class="lg:h-screen flex items-center justify-center" title="{{__('Create a Wall')}}" shadow separator>
        <x-form wire:submit="createWall">
            <x-input label="{{__('Name')}}" placeholder="{{__('Name')}}" wire:model="name"  inline />
            <x-input label="{{__('Description')}}" placeholder="{{__('Description')}}" wire:model="description" inline separator />

            <x-slot:actions>
                <x-button label="{{__('Create')}}" type="submit" icon="o-plus" class="btn-primary" spinner="createWall" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>