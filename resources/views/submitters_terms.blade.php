<x-layouts.app :title="'terms.title'">
  <div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header with back button and language switcher -->
    <div class="flex flex-row justify-between items-center flex-nowrap mb-6 gap-4">
      <a href="{{ url()->previous() ?: route('home') }}" onclick="event.preventDefault(); history.back();">← {{ __('Go back') }}</a>
      <noscript>
        <a href="{{ route('home') }}">← {{ __('terms.home') }}</a>
      </noscript>
      <div class="w-48">
        @include('partials.language-switcher')
      </div>
    </div>

    <h1 class="text-3xl font-bold mb-6">{{ __('terms.title') }}</h1>
    <p class="text-gray-700 mb-8 leading-relaxed">{{ __('terms.intro') }}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.service_purpose_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{{ __('terms.service_purpose_content') }}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.responsibility_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{!! nl2br(e(__('terms.responsibility_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.moderation_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{!! nl2br(e(__('terms.moderation_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.usage_rights_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{!! nl2br(e(__('terms.usage_rights_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.data_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{!! nl2br(e(__('terms.data_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.prohibited_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{!! nl2br(e(__('terms.prohibited_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.liability_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{!! nl2br(e(__('terms.liability_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.changes_title') }}</h2>
    <p class="text-gray-700 mb-6 leading-relaxed">{!! nl2br(e(__('terms.changes_content'))) !!}</p>

    <h2 class="text-2xl font-bold mt-8 mb-4">{{ __('terms.accept_title') }}</h2>
    <p class="text-gray-700 mb-8 leading-relaxed">{!! nl2br(e(__('terms.accept_content'))) !!}</p>

    @php $suggestions = trans('terms.suggestions'); @endphp
    @if(is_array($suggestions) && count($suggestions))
      <h2 class="text-xl font-bold mt-8 mb-4">{{ __('terms.suggestions_title') }}</h2>
      <ul class="list-disc list-inside text-gray-700 space-y-2">
        @foreach($suggestions as $s)
          <li>{{ $s }}</li>
        @endforeach
      </ul>
    @endif
  </div>
</x-layouts.app>
