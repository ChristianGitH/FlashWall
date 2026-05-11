<?php

$pages = [
    'en' => [
        'home' => 'home',
        'wedding' => 'wedding',
        'legal' => 'legal',
        'submitters_terms' => 'submitters-terms',
    ],
    'fr' => [
        'home' => 'accueil',
        'wedding' => 'mariage',
        'legal' => 'mentions-legales',
        'submitters_terms' => 'conditions-d-utilisation',
    ],
];

$slugIndex = [];

foreach ($pages as $locale => $translations) {
    foreach ($translations as $key => $slug) {
        $slugIndex[$slug] = $key;
    }
}

return [
    'pages' => $pages,
    'slug_index' => $slugIndex,
];