<?php

$pages = [
    'en' => [
        'home' => 'home',
        'about' => 'about',
        'wedding' => 'wedding',
        'event' => 'event',
        'legal' => 'legal',
        'submitters_terms' => 'submitters-terms',
    ],
    'fr' => [
        'home' => 'accueil',
        'about' => 'a-propos',
        'wedding' => 'mariage',
        'event' => 'evenement',
        'legal' => 'mentions-legales',
        'submitters_terms' => 'conditions-d-utilisation',
    ],
];

$metadata = [
    'en' => [
        'home' => [
            'title' => 'Bring the energy of your audience to your screens',
            'description' => 'Receive, moderate and display your audience’s content effortlessly. Make your audience part of the experience.',
        ],
        'about' => [
            'title' => 'About Flashwall',
            'description' => 'Discover Flashwall, the simple way to collect, moderate, and display audience photos and messages live.',
        ],
        'wedding' => [
            'title' => 'Live photo sharing for weddings',
            'description' => 'Collect photos and messages from your guests in real time and display them live on the big screen.',
        ],
        'event' => [
            'title' => 'Live photo sharing for events',
            'description' => 'Collect photos and reactions in real time, turning every participant into an actor of the event.',
        ],
        'legal' => [
            'title' => 'Legal Notice',
            'description' => 'View the legal notice and information about the publisher of Flashwall.',
        ],
        'submitters_terms' => [
            'title' => 'Terms of Use',
            'description' => 'View the terms that apply to submitting and sharing images on Flashwall.',
        ],
    ],
    'fr' => [
        'home' => [
            'title' => 'Faites entrer l’énergie du public sur vos écrans',
            'description' => 'Recevez, modérez et affichez le contenu de votre public sans effort. Faites de votre audience un acteur de l’expérience.',
        ],
        'about' => [
            'title' => 'A propos de Flashwall',
            'description' => 'Découvrez Flashwall, la solution simple pour collecter, modérer et afficher en direct les photos et messages de votre public.',
        ],
        'wedding' => [
            'title' => 'Partage de photos lors de mariage',
            'description' => 'Rassemblez en direct photos et messages de vos proches pour les afficher en live sur grand écran.',
        ],
        'event' => [
            'title' => 'Partage de photos lors d’événements',
            'description' => 'Rassemblez en direct photos et réactions afin que chaque participant devienne acteur de l’événement. ',
        ],
        'legal' => [
            'title' => 'Mentions légales',
            'description' => 'Consultez les mentions légales et les informations sur l’éditeur de Flashwall.',
        ],
        'submitters_terms' => [
            'title' => 'Conditions d’utilisation',
            'description' => 'Consultez les conditions applicables à l’envoi et au partage d’images sur Flashwall.',
        ],
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
    'metadata' => $metadata,
    'slug_index' => $slugIndex,
];