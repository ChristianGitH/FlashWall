<x-layouts.app :title="'Conditions d’utilisation'">
<x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

<div class="container mx-auto p-5 lg:py-5 px-2 lg:px-10 max-w-3xl">
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

    <h1 class="text-3xl font-bold mb-6">Conditions d’utilisation</h1>
    <p class="mb-8 leading-relaxed">
        En envoyant une image via ce service, vous acceptez les présentes Conditions d’utilisation. Ces conditions s’appliquent uniquement aux visiteurs qui envoient des images sur un Wall.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">1. Objet du service</h2>
    <p class="mb-6 leading-relaxed">
        Ce service permet aux visiteurs d’envoyer une image afin qu’elle soit affichée sur un Wall, c’est-à-dire un mur d’images diffusé lors d’un événement, en ligne ou sur un écran.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">2. Responsabilité du visiteur</h2>
    <p class="mb-6 leading-relaxed">
        En envoyant une image, vous garantissez que :<br>
        - vous détenez les droits nécessaires pour la partager ;<br>
        - l’image ne porte pas atteinte aux droits d’un tiers ;<br>
        - l’image est conforme à la loi et ne contient aucun contenu illicite, discriminatoire, violent, pornographique, diffamatoire ou portant atteinte à la vie privée d’autrui.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">3. Modération et affichage</h2>
    <p class="mb-6 leading-relaxed">
        Les organisateurs de l’événement et/ou l’équipe du service peuvent modérer, refuser ou supprimer toute image envoyée, sans notification et pour n’importe quel motif (contenu inapproprié, mauvaise qualité, non-respect des règles, etc.).<br>
        L’envoi d’une image ne garantit pas son affichage sur le Wall.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">4. Droits d’utilisation accordés</h2>
    <p class="mb-6 leading-relaxed">
        En envoyant une image, vous autorisez l’organisateur de l’événement à :<br>
        - afficher l’image sur le Wall concerné ;<br>
        - la diffuser publiquement dans le cadre de l’événement (écran, streaming, projection).<br>
        Cette autorisation est non exclusive et limitée à la durée de l’événement. Aucun autre usage ne sera fait sans votre accord.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">5. Données personnelles et conservation</h2>
    <p class="mb-6 leading-relaxed">
        Votre image peut être stockée temporairement afin de permettre son affichage et la gestion du Wall. Elle peut être supprimée automatiquement ou manuellement après l’événement. Aucune exploitation commerciale n’est effectuée.<br>
        Un identifiant anonyme (cookie de visiteur) peut être utilisé pour éviter les abus et limiter le nombre d'envoi par appareil.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">6. Contenus interdits</h2>
    <p class="mb-6 leading-relaxed">
        Il est strictement interdit d’envoyer des images comprenant :<br>
        - nudité ou contenu sexuel ;<br>
        - violence ou scènes choquantes ;<br>
        - propos haineux ou discriminatoires ;<br>
        - logos ou contenus protégés dont vous ne détenez pas les droits ;<br>
        - données personnelles d’autrui sans consentement ;<br>
        - contenu illégal selon les lois applicables.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">7. Limitation de responsabilité</h2>
    <p class="mb-6 leading-relaxed">
        Le service décline toute responsabilité concernant :<br>
        - l’utilisation faite des images par les organisateurs ;<br>
        - les retards, suppressions ou erreurs de diffusion ;<br>
        - tout dommage résultant de l’envoi d’un contenu non conforme.<br>
        Vous restez seul responsable des images que vous envoyez.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">8. Modification des conditions</h2>
    <p class="mb-6 leading-relaxed">
        Le service se réserve le droit de modifier ces Conditions d’utilisation à tout moment. La version en vigueur est celle affichée au moment de l’envoi de l’image.
    </p>

    <h2 class="text-2xl font-bold mt-8 mb-4">9. Acceptation</h2>
    <p class="mb-8 leading-relaxed">
        En cliquant sur « Envoyer » et en transmettant votre image, vous confirmez avoir lu et accepté ces Conditions d’utilisation.
    </p>

</div>
</x-layouts.app>