<!------ 
    LAYOUT FOR LIVEWIRE PAGES
-------->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') {{ isset($title) ? __($title).' - '.config('app.name') : ' - '.config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/site.webmanifest" />

    {{-- INSERT STACK HERE --}}
    @stack('head')

</head>

@php
    $user = auth()->user();
    $routeName = Route::currentRouteName();

    $isPublicPage = in_array($routeName, [
        'static.page',
        'plans',
        'login',
        'password.forgot',
        'password.reset',
        'home',
        'register'
    ]);

        
    $isBlankLayout =
        Request::is('display/*')
        || Route::is('create-image');
@endphp

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

<!-- HORIZONTAL NAVBAR -->
<!-- If it's not one of these pages we use the normal layout -->
@if (! $isBlankLayout)

<!-- Display horizontal navbar on all public pages -->
@if ($isPublicPage)
    <x-nav sticky full-width>
        <x-slot:brand>
            {{-- Brand --}}
            <x-app-brand/>
        </x-slot:brand>

        <x-slot:actions>
            {{-- Drawer toggle for "main-drawer" --}}
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
            <div class="hidden lg:flex">
                <livewire:static-page-navigation />
            </div>
        </x-slot:actions>
    </x-nav>

<!-- If it's not static page or the home page,
we display the horizontal navbar only on mobile screens -->
@else
    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>
@endif

{{-- MAIN --}}
<x-main full-width :with-nav="$isPublicPage">


<!-- If it's a static page AND user is loged in, we display both menus in sidebar :
    navigation + static-page-navigation -->
@if($user && $isPublicPage)

{{-- SIDEBAR --}}
<x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">
    <div class="lg:hidden">
        {{-- BRAND --}}
        <x-app-brand class="p-5 pt-3" />
        {{-- MENU --}}
        <livewire:static-page-navigation />
    </div>
    {{-- MENU --}}
    <livewire:navigation />
</x-slot:sidebar>

<!-- If user is loged in but it's not a static page, then we display only the sidebar navigation menu -->
@elseif($user)

    {{-- SIDEBAR --}}
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">
        {{-- BRAND --}}
        <x-app-brand class="p-5 pt-3" />
        {{-- MENU --}}
        <livewire:navigation />
    </x-slot:sidebar>

<!-- If it's a guest and static page, we don't display the sidebar navigation menu -->
@elseif($isPublicPage)
    {{-- SIDEBAR --}}
    <div class="lg:hidden">
        {{-- BRAND --}}
        <x-app-brand class="p-5 pt-3" />
        {{-- MENU --}}
        <livewire:static-page-navigation />
    </div>
@endif

    {{-- The `$slot` goes here --}}
    <x-slot:content>
        {{ $slot }}
    </x-slot:content>
</x-main>


@else

<!-- NO MENU : If it's one of the page, we use a blank layout which was created for this page (app/view/components/Blank.php)-->
<x-blank full-width>
    {{-- The `$slot` goes here --}}
    <x-slot:content>
        {{ $slot }}
    </x-slot:content>
</x-blank>

@endif

    {{--  TOAST area --}}
    <x-toast />

</body>
</html>
