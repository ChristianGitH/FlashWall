<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

}; ?>


<div>
    <x-menu activate-by-route>

        
        {{-- Vérifie si l'utilisateur est connecté --}}
        @if($user = auth()->user())
            <x-menu-separator />
                <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                    <x-slot:actions>
                        <x-button icon="o-power" wire:click="logout" class="btn-circle btn-ghost btn-xs" tooltip-left="{{__('Logout')}}" no-wire-navigate />
                    </x-slot:actions>
                </x-list-item>
            <x-menu-separator />

        {{-- Affichage des Walls de l'utilisateur --}}
        @if(count($walls) > 0)
            <x-menu-sub title="Mes Walls" icon="o-folder">            </x-menu-sub>
                @foreach($walls as $wall)
                    <x-menu-item title="{{ $wall->name }}" icon="o-cog-6-tooth" link="{{ route('setup-wall', ['wall' => $wall->id]) }}"  />
                    <x-menu-item title="{{ $wall->name }}" icon="o-tv" link="{{ route('moderation', ['wall' => $wall->id]) }}"  />
                @endforeach

        @endif

        @else
            <x-menu-item title="Hello" icon="o-sparkles" link="/" />
            <x-menu-item title="{{__('Login')}}" icon="o-user" link="{{ route('login') }}" />                   
        @endif


        <x-menu-item title="Create Wall" icon="o-plus" link="{{ route('create-wall') }}" />                   

    </x-menu>
</div>