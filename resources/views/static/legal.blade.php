<x-layouts.app :title="'legal.title'">
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header with back button and language switcher -->
    <div class="flex flex-row justify-between items-center flex-nowrap mb-6 gap-4">
      <a href="{{ url()->previous() ?: route('home') }}" onclick="event.preventDefault(); history.back();">← {{ __('Go back') }}</a>
      <noscript>
        <a href="{{ route('home') }}">← {{ __('legal.home') }}</a>
      </noscript>
      <div class="w-48">
        @include('partials.language-switcher')
      </div>
    </div>

    <!-- Main title -->
    <h1 class="text-3xl font-bold mb-6">{{ __('legal.title') }}</h1>

    <!-- Editor section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('legal.editor.title') }}</h2>
    <p class="mb-6 leading-relaxed">
        {{ __('legal.editor.text') }} {{ __('legal.editor.email') }}
    </p>

    <!-- Intellectual property section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('legal.intellectual_property.title') }}</h2>
    <p class="mb-6 leading-relaxed">
        {{ __('legal.intellectual_property.text') }}<br>
        {{ __('legal.intellectual_property.restriction') }}<br>
        {{ __('legal.intellectual_property.third_party') }}
    </p>

    <!-- Hosting section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('legal.hosting.title') }}</h2>
    <p class="mb-6 leading-relaxed">
        {{ __('legal.hosting.host_name') }}<br>
        {{ __('legal.hosting.company') }}<br>
        {{ __('legal.hosting.address') }}<br>
    </p>

    <!-- Liability section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('legal.liability.title') }}</h2>
    <p class="mb-6 leading-relaxed">
        {{ __('legal.liability.text') }}
    </p>

    <!-- Personal data section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('legal.personal_data.title') }}</h2>
    <p class="mb-6 leading-relaxed">
        {{ __('legal.personal_data.text') }}<br>
        {{ __('legal.personal_data.rights') }}<br>
        {{ __('legal.personal_data.contact') }}
    </p>

    <!-- Contact section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('legal.contact.title') }}</h2>
    <p class="mb-6 leading-relaxed">
        {{ __('legal.contact.text') }}
    </p>
  </div>
</x-layouts.app>
