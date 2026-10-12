<?php

use Livewire\Component;

new class extends Component
{
    public bool $as_sub_menu = false;
};

?>

@if ($as_sub_menu)
    <x-menu-sub title="Menu" icon="o-bars-3">
        @include('partials.static-menu-items')
    </x-menu-sub>
@else
    <x-menu class="menu-vertical lg:menu-horizontal items-start lg:items-center">
        @include('partials.static-menu-items')

        @guest
            <x-menu-item title="{{ __('Login') }}" icon="o-user" link="{{ route('login') }}" />

            @include('partials.language-switcher')

            <x-theme-toggle class="btn btn-circle btn-ghost btn-sm" />
        @endguest

    </x-menu>
@endif