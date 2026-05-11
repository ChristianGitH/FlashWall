<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller responsible for rendering translated static pages and
 * redirecting plain slug requests to the current locale version.
 */
class StaticPageController extends Controller
{
    /**
     * Load the static page translation map from configuration.
     *
     * @return array<string, array<string, string>>
     */
    protected function getPages()
    {
        return config('pages.pages', []);
    }
    /**
     * Load the static pages list of names, and their keys (keys are english names).
     *
     * @return array<string, array<string, string>>
     */
    protected function getSlugIndex()
    {
        return config('pages.slug_index', []);
    }

    /**
     * Detect locale from a URL path string.
     *
     * Extracts locale from URL structure (e.g., /en/legal → 'en')
     * Returns null if no valid locale is found.
     *
     * @param  string  $path
     * @return string|null
     */
    protected function detectLocaleFromPath($path)
    {
        $segments = explode('/', trim($path, '/'));
        if (! empty($segments[0]) && in_array($segments[0], ['en', 'fr'])) {
            return $segments[0];
        }
        return null;
    }

    /**
     * Display a localized home page view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show_home(Request $request)
    {
        $pages = $this->getPages();

        $locale = app()->getLocale() ?? 'en';

        $slug = $pages[$locale]['home'];

        return redirect("/{$locale}/{$slug}", 302);
    }


    /**
     * Display a localized static page view.
     *
     * @param  string  $locale
     * @param  string  $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function show($locale, $slug)
    {
        $pages = $this->getPages();

        $pageKey = null;

        $pageKey = array_search($slug, $pages[$locale] ?? [], true);

        if (! $pageKey) {
            abort(404);
        }

        // Set the application locale so view helpers and translations
        // are rendered consistently for the selected language.
        app()->setLocale($locale);

        return view("static.{$locale}.{$pageKey}", [
            'locale' => $locale,
            'pageKey' => $pageKey,
        ]);
    }

    /**
     * DESACTIVATED FOR NOW, AS IT CAN CAUSE CONFLICTS WITH DYNAMIC WALL ROUTES (/{wall})
     * 
     * Redirect a non-localized slug to the current locale's translated page.
     *
     * Uses HTTP 301 (permanent redirect) to preserve SEO link equity
     * and consolidate search engine rankings on the canonical URL.
     *
     * Locale detection priority:
     * 1. Detected from referer URL structure (ensures crawlers are consistent)
     * 2. Falls back to session locale (better UX for logged-in users)
     * 3. Defaults to 'en' (safe default for new visitors)
     *
     * This keeps URLs like /legal and /mentions-legales functional while
     * preserving the current locale intelligently.
     *
     * @param  string  $page
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /*public function redirect($page, Request $request)
    {
        $pages = $this->getPages();
        $slugIndex = $this->getSlugIndex();


        // Priority 1: Detect locale from referer URL
        $refererPath = parse_url($request->header('referer'), PHP_URL_PATH) ?? '';
        $locale = $this->detectLocaleFromPath($refererPath);

        // Priority 2: Fall back to session locale
        if (! $locale) {
            $locale = session('locale');
        }

        // Priority 3: Default to 'en'
        if (! $locale) {
            $locale = 'en';
        }

        // We get the page key (english name) from the slug index.
        $pageKey = $slugIndex[$page] ?? null;
        if (!$pageKey) {
            abort(404);
        }

        // Then we find the translated slug for the detected locale.
        $translatedSlug = $pages[$locale][$pageKey] ?? null;
        if (!$translatedSlug ) {
            abort(404);
        }

        return redirect("/{$locale}/" . $translatedSlug, 301);

        abort(404);
    }*/
}