<x-layouts.app :title="'terms.title'">
  <div class="container mx-auto p-5 lg:py-5 px-2 lg:px-10 max-w-3xl">
    <!-- Header with back button and language switcher -->
    <div class="flex flex-row justify-between items-center flex-nowrap mb-6 gap-4">
      <a href="{{ url()->previous() ?: route('home') }}" onclick="event.preventDefault(); history.back();">← {{ __('Go back') }}</a>
      <noscript>
        <a href="{{ route('home') }}">← {{ __('terms.home') }}</a>
      </noscript>
      <div class="justify-end">
        @if (app()->getLocale() === 'fr')
          <span>Français</span>
          <a href="{{ route('terms', ['locale' => 'en']) }}" class="underline ml-2">English</a>
        @elseif (app()->getLocale() === 'en')
          <a href="{{ route('terms', ['locale' => 'fr']) }} " class="underline mr-2">Français</a>
          <span>English</span>
        @endif
      </div>
    </div>

    <h1 class="text-3xl font-bold mb-6">{{ __('terms.title') }}</h1>
    <p class="mb-8 leading-relaxed">{{ __('terms.intro') }}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.service_purpose_title') }}</h2>
    <p class="mb-6 leading-relaxed">{{ __('terms.service_purpose_content') }}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.responsibility_title') }}</h2>
    <p class="mb-6 leading-relaxed">{!! nl2br(e(__('terms.responsibility_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.moderation_title') }}</h2>
    <p class="mb-6 leading-relaxed">{!! nl2br(e(__('terms.moderation_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.usage_rights_title') }}</h2>
    <p class="mb-6 leading-relaxed">{!! nl2br(e(__('terms.usage_rights_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.data_title') }}</h2>
    <p class="mb-6 leading-relaxed">{!! nl2br(e(__('terms.data_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.prohibited_title') }}</h2>
    <p class="mb-6 leading-relaxed">{!! nl2br(e(__('terms.prohibited_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.liability_title') }}</h2>
    <p class="mb-6 leading-relaxed">{!! nl2br(e(__('terms.liability_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.changes_title') }}</h2>
    <p class="mb-6 leading-relaxed">{!! nl2br(e(__('terms.changes_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.accept_title') }}</h2>
    <p class="mb-8 leading-relaxed">{!! nl2br(e(__('terms.accept_content'))) !!}</p>

  </div>
</x-layouts.app>
