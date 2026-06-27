<?php

if (!function_exists('localized_page')) {
    /**
     * Generates a localized URL for static pages based on a page key.
     */
    function localized_page(string $pageKey, ?string $locale = null): string
    {
        // Use provided locale or fall back to the current application locale
        $locale ??= app()->getLocale();

        // Retrieve the URL slug for this page key from the pages configuration
        $slug = config("pages.pages.$locale.$pageKey");

        // Abort with 404 if the page slug is not found in configuration
        abort_if(!$slug, 404);

        // Generate and return the route URL with the locale and slug
        return route('static.page', [
            'locale' => $locale,
            'slug' => $slug,
        ]);
    }
}