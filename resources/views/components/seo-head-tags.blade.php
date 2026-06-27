@props(['locale' => null, 'pageKey' => null])

@php
    $locale = $locale ?? app()->getLocale();
    $pages = config('pages.pages', []);
    
    // Get the current page translations
    $pageTranslations = $pages[$pageKey] ?? null;
    
    if ($pageTranslations) {
        // Canonical URL for the current page
        $canonicalUrl = route('static.page', [$locale, $pageTranslations[$locale]]);
        
        // All hreflang URLs for all language versions
        $hreflangs = [];
        foreach ($pageTranslations as $lang => $slug) {
            $hreflangs[] = [
                'lang' => $lang,
                'url' => route('static.page', [$lang, $slug])
            ];
        }
    }
@endphp

@push('head')
    @if ($pageTranslations)
        <!-- Canonical URL for SEO -->
        <link rel="canonical" href="{{ $canonicalUrl }}" />
        
        <!-- Alternate language versions (hreflang) for international SEO -->
        @foreach ($hreflangs as $hreflang)
            <link rel="alternate" hreflang="{{ $hreflang['lang'] }}" href="{{ $hreflang['url'] }}" />
        @endforeach
    @endif
@endpush
