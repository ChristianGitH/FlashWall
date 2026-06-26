<?php

use App\Models\Wall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;
use App\Services\SubscriptionService;


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
        if (SubscriptionService::canCreateWall(auth()->user())) {
            // User can create a wall

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

        } else {
            $error = SubscriptionService::getSubscriptionErrorMessage(auth()->user());
            // $error will contain reason (expired, reached limit, etc.)
            $this->warning($error);
        }
    }

}; ?>

<div class="lg:min-h-screen flex items-center justify-center">
    <x-card class="flex items-center justify-center p-5 lg:px-10 lg:py-5" title="{{__('Create a Wall')}}" shadow separator>
        <x-form wire:submit="createWall">
            <x-input label="{{__('Name')}}" placeholder="{{__('Name')}}" wire:model="name"  inline />
            <x-input label="{{__('Description')}}" placeholder="{{__('Description')}}" wire:model="description" inline separator />

            @if (auth()->user()->hasReachedWallLimit())
                <x-alert title="{{ __('You have reached your wall limit') }}" 
                description="{{ trans_choice(
                    'With your current plan, you can create :count wall.|With your current plan, you can create :count walls.',
                    auth()->user()->getFeature('walls'),
                    ['count' => auth()->user()->getFeature('walls')]
                ) }}" 
                icon="o-lock-closed" class="alert-info flex flex-wrap">
                    <x-slot:actions>
                        <x-button label="{{ __('Upgrade your plan !') }}" />
                    </x-slot:actions>
                </x-alert>
            @else
                <x-slot:actions>
                    <x-button label="{{__('Create')}}" type="submit" icon="o-plus" class="btn-primary" spinner="createWall" />
                </x-slot:actions>
            @endif

        </x-form>

    </x-card>
</div>