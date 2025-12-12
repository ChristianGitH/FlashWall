<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

new class extends Component 
{
    public $walls = [];

    public function mount()
    {
        if (Auth::check()) {
            $this->walls = Wall::where('user_id', Auth::id())->get();
        }
    }

    
    public function logout(): void
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $this->redirect('/');
    }


    // Refresh navigation when a wall name is updated.
    protected $listeners = ['refreshNavigation' => '$refresh'];

}; ?>


<div class="flex flex-col h-[90%]">
    <x-menu activate-by-route>

        {{-- Vérifie si l'utilisateur est connecté --}}
        @if($user = auth()->user())
                <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 -my-2! rounded">
                    <x-slot:actions>
                        <x-button icon="o-power" wire:click="logout" class="btn-circle btn-ghost btn-xs" tooltip-left="{{__('Logout')}}" no-wire-navigate spinner="logout" />
                    </x-slot:actions>
                </x-list-item>
            <x-menu-separator />

        <x-menu-item title="{{__('Home')}}" icon="o-home" link="{{ route('home') }}" />

        <x-menu-item title="{{__('Create Wall')}}" icon="o-plus" link="{{ route('create-wall') }}" />

        {{-- Affichage des Walls de l'utilisateur --}}
        @if(count($walls) > 0)
                @foreach($walls as $index => $wall)
                    <x-menu-sub :title="$wall->name" icon="o-sparkles" :open="request()->is($wall->slug . '/*')">
                        <x-menu-item title="{{__('Settings')}}" icon="o-cog-6-tooth" link="{{ route('setup-wall', ['wall' => $wall->slug]) }}"  />
                        <x-menu-item title="{{__('Moderation')}}" icon="o-magnifying-glass-circle" link="{{ route('moderation', ['wall' => $wall->slug]) }}"  />
                        <x-menu-item title="{{__('Post image')}}" icon="o-plus" link="{{ route('create-image', ['wall' => $wall->slug]) }}"  />
                        <x-menu-item title="Display" icon="o-tv" link="{{ route('slideshow', ['wall' => $wall->slug]) }}" external />
                        <x-menu-item title="Display Dev" icon="o-tv" link="{{ route('slideshow.mode', ['wall' => $wall->slug, 'mode' => 'dev']) }}" external />
                        <x-menu-item title="Display Slow" icon="o-tv" link="{{ route('slideshow.mode', ['wall' => $wall->slug, 'mode' => 'slow']) }}" external />
                        <x-menu-item title="Display Old Caption" icon="o-tv" link="{{ route('slideshow.mode', ['wall' => $wall->slug, 'mode' => 'oldcaption']) }}" external />
                    </x-menu-sub>
                @endforeach

        @endif

        @else
            <x-menu-item title="{{__('Home')}}" icon="o-home" link="{{ route('home') }}" />
            <x-menu-item title="{{__('Login')}}" icon="o-user" link="{{ route('login') }}" />                   
        @endif

        <x-menu-separator />

        <x-menu class="flex flex-row my-0.5 mt-3 py-1.5 px-4 whitespace-nowrap flex flex-row items-center">
            <x-theme-toggle />
            @include('partials/language-switcher')
        </x-menu>
    </x-menu>



    <x-menu class="justify-end grow ml-3">
        <span>Version : {{ 'v' . trim(shell_exec('git rev-list --count HEAD')) }}</span> 
    </x-menu>
</div>