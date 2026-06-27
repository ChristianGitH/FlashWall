<?php

use Livewire\Component;

new class extends Component 
{
}
?>
<x-menu class="menu-vertical lg:menu-horizontal items-center">
    <x-menu-item title="{{ __('Home') }}" link="{{ route('home') }}"/>
    <x-menu-item title="{{ __('Plans') }}" link="{{ route('plans') }}"/>

        {{-- Vérifie si l'utilisateur est connecté --}}
        @if($user = auth()->user())

        @else
            <x-menu-item title="{{ __('Login') }}" icon="o-user" link="{{ route('login') }}" />

            <x-theme-toggle />
            @include('partials/language-switcher')
        @endif
</x-menu>
