<?php

use App\Models\Wall;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new
#[Title('Setup Wall')]

class extends Component {
    
    public Wall $wall;

    public string $name = '';
    public string $slug = '';
    public string $description = '';

    public function mount(Wall $wall)
    {
        $this->wall = $wall;
        $this->name = $wall->name;
        $this->slug = $wall->slug;
        if($wall->description) {
            $this->description = $wall->description;
        }
    }

    public function updateWall()
    {
        $data = $this->validate([
            'name' => 'required|string|max:255|unique:walls,name,' . $this->wall->id,
            'slug' => 'required|string|max:255|unique:walls,slug,' . $this->wall->id,
            'description' => 'nullable|string|max:255',
        ]);
    
        // Filtrer uniquement les champs modifiés
        $changes = array_filter($data, fn ($value, $key) => $this->wall->$key !== $value, ARRAY_FILTER_USE_BOTH);
    
        // Exécute la mise à jour uniquement si des changements existent
        if (!empty($changes)) {
            $this->wall->update($changes);
            session()->flash('message', 'Changes saved!');
        } else {
            session()->flash('message', 'No change detected');
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

<div class="flex items-center justify-center h-screen">
    <x-card title="{{ __('Update Wall') }}" class="w-96" shadow separator>
        @if (session()->has('message'))
            <x-alert color="success">{{ session('message') }}</x-alert>
        @endif

        <x-form wire:submit="updateWall">
            <x-input label="{{ __('Name') }}" wire:model="name" />
            <x-input label="{{ __('Slug') }}" wire:model="slug" />
            <x-input label="{{ __('Description') }}" wire:model="description" />

            <x-slot:actions>
                <x-button label="{{ __('Create image') }}" title="{{ $wall->name }}" icon="o-tv" link="{{ route('create-image', [$wall->slug]) }}"  />
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWall" />
            </x-slot:actions>
        </x-form>

        <x-button label="Delete" icon="o-trash" class="btn-danger"
            wire:click="deleteWall" 
            wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE" />
    </x-card>
</div>
