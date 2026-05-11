<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller responsible for switching the current application locale
 * and translating the current static page URL when applicable.
 */
class LanguageController extends Controller
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
     * Update the user's locale and redirect back to the translated page.
     *
     * Detects current locale from URL structure first (for crawler consistency),
     * then translates a localized static page URL if applicable.
     * Uses URL-based detection to ensure predictable behavior across all contexts.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($locale)
    {
        $pages = $this->getPages();

        if (! isset($pages[$locale])) {
            abort(404);
        }

        session()->put('locale', $locale);
        app()->setLocale($locale);

        $previousUrl = url()->previous();

        $previousPath = parse_url($previousUrl, PHP_URL_PATH) ?: '/';

        $queryString = parse_url($previousUrl, PHP_URL_QUERY);

        // Detect current locale from URL structure
        $currentLocale = $this->detectLocaleFromPath($previousPath);

        // If locale was detected from URL and path has at least 2 segments, translate
        if (
            $currentLocale &&
            count(explode('/', trim($previousPath, '/'))) >= 2
        ) {

            // Extract the slug segment from the current URL path
            $segments = explode('/', trim($previousPath, '/'));
            $currentSlug = $segments[1];

            $pageKey = null;

            // We get the page key (english name) from the slug index.
            $pageKey = array_search($currentSlug, $pages[$currentLocale] ?? [], true);

            if ($pageKey) {

                // Then we find the translated slug for the new selected locale.
                $translatedSlug = $pages[$locale][$pageKey] ?? null;
                if (!$translatedSlug ) {
                    abort(404);
                }

                $targetPath = "/{$locale}/{$translatedSlug}";

                if ($queryString) {
                    $targetPath .= '?' . $queryString;
                }

                // Use 301 permanent redirect to preserve SEO link equity
                return redirect($targetPath, 301);
            }
        }

        return redirect()->back();
    }
}