<x-layouts.app :title="'Mentions légales'">
<x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

<div class="container mx-auto px-4 max-w-3xl">
    <!-- Header with back button and language switcher -->
    <div class="flex flex-row justify-between items-center flex-nowrap mb-6 gap-4">
      <a href="{{ url()->previous() ?: route('home') }}" onclick="event.preventDefault(); history.back();">
        ← Retour
      </a>

      <noscript>
        <a href="{{ route('home') }}">
          ← Accueil
        </a>
      </noscript>

      <div class="w-48">
        @include('partials.language-switcher')
      </div>
    </div>

    <!-- Main title -->
    <h1 class="text-3xl font-bold mb-6">Mentions légales</h1>

    <!-- Editor section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Éditeur</h2>
    <p class="mb-6 leading-relaxed">
        L’éditeur du site est : contact@flashwall.app
    </p>

    <!-- Intellectual property section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Propriété intellectuelle</h2>
    <p class="mb-6 leading-relaxed">
        L’ensemble des contenus présents sur le site (textes, images, illustrations, photographies, vidéos, logos, icônes…) est la propriété de l’éditeur ou utilisé avec l’autorisation des auteurs.<br>
        Toute reproduction, distribution, modification, adaptation, retransmission ou publication de ces éléments est strictement interdite sans accord écrit de l’éditeur.<br>
        Pour les textes et images provenant de tiers, les sources sont citées et les autorisations obtenues lorsque nécessaire.
    </p>

    <!-- Hosting section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Hébergeur</h2>
    <p class="mb-6 leading-relaxed">
        OVH<br>
        OVH SAS<br>
        2 rue Kellermann - 59100 Roubaix - France<br>
    </p>

    <!-- Liability section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Responsabilité</h2>
    <p class="mb-6 leading-relaxed">
        L’éditeur ne saurait être tenu responsable des dommages directs ou indirects, matériels ou immatériels, résultant de l’accès ou de l’utilisation du site, ainsi que de l’impossibilité d’accéder au site ou d’utiliser ses services.
    </p>

    <!-- Personal data section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Données personnelles</h2>
    <p class="mb-6 leading-relaxed">
        D’une façon générale, vous pouvez visiter notre site sur Internet sans avoir à décliner votre identité et à fournir des informations personnelles vous concernant. Cependant, nous pouvons parfois vous demander des informations. Par exemple si vous souhaitez créer un compte, ou partager des fichiers. Dans ce cas vous devrez accepter des conditions d’utilisations liées aux opérations que vous souhaitez effectuer.<br>
        Conformément à la loi « Informatique et Libertés » du 6 janvier 1978, vous disposez d’un droit d’accès, de modification, de rectification et de suppression des données vous concernant.<br>
        Pour exercer ce droit, vous pouvez contacter l’éditeur à l’adresse e-mail indiquée ci-dessus.
    </p>

    <!-- Contact section -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Contact</h2>
    <p class="mb-6 leading-relaxed">
        Pour toute question relative au site ou aux services proposés, vous pouvez contacter l’éditeur aux coordonnées indiquées ci-dessus.
    </p>
</div>
</x-layouts.app>