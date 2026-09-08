<?php

use Livewire\Component;
use App\Models\Newsletter;
use Livewire\Attributes\Rule;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

    #[Rule('required|email|max:150|unique:newsletters')]
    public $email;

    public function saveEmail()
    {
        $data = $this->validate();

        Newsletter::create($data);

        $this->success('Merci pour votre inscription', 'Succès');

        $this->email = '';
    }
}

?>

<x-layouts.app :title="$pageMetadata['title'] ?? 'À propos de Flashwall'">
    <x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

    <div class="mx-auto max-w-6xl px-4 pb-12 sm:px-6 sm:pb-16 lg:px-8">

        {{-- =========================================================
             HERO
        ========================================================== --}}
        <section class="relative overflow-hidden rounded-3xl border border-teal-100 bg-gradient-to-br from-teal-50 via-white to-amber-50 p-6 py-12 shadow-sm dark:border-teal-900/70 dark:from-slate-950 dark:via-slate-900 dark:to-teal-950 sm:p-10 sm:py-16 lg:p-14 lg:py-20">

            <div class="relative max-w-4xl">

                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-600 dark:text-teal-300">
                    À propos de Flashwall
                </p>

                <h1 class="mt-4 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl dark:text-white">
                    Une solution professionnelle pour interagir avec le public,
                    <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                        conçue pour les événements en direct.
                    </span>
                </h1>

                <p class="mt-6 max-w-3xl text-xl leading-8 text-gray-600 dark:text-gray-300">
                    Flashwall est un mur de photos participatif pensé pour faire vivre
                    l'énergie de votre audience sur vos écrans, avec la rapidité, la fiabilité
                    et la personnalisation qu'exigent les événements importants.
                </p>

            </div>

        </section>


        {{-- =========================================================
             THE STORY
        ========================================================== --}}
        <section class="mt-6 rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-[0_20px_60px_-20px_rgba(15,118,110,0.16)] backdrop-blur dark:border-slate-700 dark:bg-slate-900/90 sm:p-8 lg:p-10">

            <div class="grid gap-8 lg:grid-cols-2 lg:gap-12 lg:items-center">

                <div>

                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-600 dark:text-teal-300">
                        Les débuts
                    </p>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                        Né d'une véritable expérience événementielle.
                    </h2>

                    <div class="mt-6 space-y-4 text-lg leading-8 text-gray-600 dark:text-gray-300">

                        <p>
                            Flashwall n'est pas né d'une idée de logiciel générique.
                            Il est né dans les coulisses d'événements live.
                        </p>

                        <p>
                            Après des années de travail sur des événements sportifs internationaux,
                            notamment des productions de Coupes du monde, nous avons utilisé plusieurs
                            solutions de Fan Wall existantes pour afficher le contenu du public sur grand écran.
                        </p>

                        <p>
                            Elles fonctionnaient, mais présentaient souvent les mêmes limites :
                            modération difficile, configuration complexe et, surtout,
                            personnalisation très limitée.
                        </p>

                        <p>
                            Nous pouvions créer quelque chose de mieux.
                        </p>

                    </div>

                </div>


                {{-- Event production card --}}
                <div class="relative">

                    <div class="rounded-3xl bg-linear-to-br from-teal-700 to-amber-500 p-1 shadow-2xl shadow-teal-900/20">

                        <div class="rounded-[1.35rem] bg-gray-950 p-6 text-white sm:p-8 lg:p-10">

                            <div class="flex items-center gap-3">

                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke-width="1.8"
                                         stroke="currentColor"
                                         class="size-6">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M4.5 6.75h15m-15 0A2.25 2.25 0 0 1 6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75m-15 0v10.5A2.25 2.25 0 0 0 6.75 19.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75" />

                                    </svg>

                                </div>

                                <span class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-300">
                                    Événements en direct
                                </span>

                            </div>


                            <p class="mt-8 text-3xl font-bold leading-tight">
                                Créé par des personnes qui comprennent ce qui se passe
                                <span class="text-teal-300">
                                    derrière les écrans.
                                </span>
                            </p>


                            <div class="mt-8 h-px bg-white/10"></div>


                            <p class="mt-6 text-sm leading-6 text-gray-400">
                                De la production LED et vidéo à l'engagement du public,
                                Flashwall est issu de la réalité de la production événementielle en direct.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- =========================================================
             THE PROBLEM
        ========================================================== --}}
        <section class="py-12 sm:py-16 lg:py-20">

            <div class="grid gap-8 lg:grid-cols-2 lg:gap-12 lg:items-center">

                <div>

                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-300">
                        Pourquoi Flashwall ?
                    </p>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                        Votre événement est unique.
                        <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                            Votre Fan Wall devrait l'être aussi.
                        </span>
                    </h2>

                </div>


                <div class="space-y-5 text-lg leading-8 text-gray-600 dark:text-gray-300">

                    <p>
                        Un événement sportif ne ressemble pas à un mariage.
                        Un festival ne ressemble pas à une conférence d'entreprise.
                        Chaque événement possède sa propre identité, son public et son atmosphère.
                    </p>

                    <p>
                        Nous voulions créer un Fan Wall capable de s'adapter à cette identité,
                        plutôt que de faire entrer chaque événement dans le même modèle.
                    </p>

                    <p>
                        C'est pourquoi la personnalisation est au cœur de Flashwall.
                    </p>

                </div>

            </div>

        </section>


        {{-- =========================================================
             CUSTOMIZATION
        ========================================================== --}}
        <section class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-teal-50 p-6 dark:border-slate-700 dark:from-slate-900 dark:to-slate-800 sm:p-8 lg:p-10">

            <div>
                <p
                    x-data="{ eventTypes: ['Mariage',
                    'Conférence',
                    'Concert',
                    'Séminaire',
                    'Festival',
                    'Match de foot'], currentEvent: 0, isVisible: true }"
                    x-init="setInterval(() => { isVisible = false; setTimeout(() => { currentEvent = (currentEvent + 1) % eventTypes.length; isVisible = true }, 250) }, 1000)"
                    class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-700 dark:text-teal-300"
                >
                    Conçu spécialement pour votre <span x-text="eventTypes[currentEvent]" :class="{ 'opacity-0': !isVisible }" class="inline-block transition-opacity duration-300">événement</span>
                </p>
            </div>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

                <div>


                    <h2 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Donnez à Flashwall l'apparence d'une solution créée pour vous.
                    </h2>

                </div>

                <p class="max-w-xl text-lg leading-7 text-gray-600 dark:text-gray-300">
                    Des moindres détails à l'expérience visuelle globale,
                    Flashwall donne aux organisateurs la liberté de créer leur propre univers.
                </p>

            </div>


            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">🎨</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Identité visuelle
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Couleurs, polices, logos et arrière-plans.
                    </p>

                </div>


                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">🖥️</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Expérience à l'écran
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Mises en page, transitions, animations et paramètres d'affichage.
                    </p>

                </div>


                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">📱</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Expérience de l'audience
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Personnalisez la façon dont votre audience découvre et utilise votre wall.
                    </p>

                </div>


                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">✨</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Votre créativité
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Créez une expérience qui ressemble vraiment à votre événement.
                    </p>

                </div>

            </div>


            <div class="mt-8 text-center">

                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Votre événement a sa propre identité.
                </p>

                <p class="mt-2 text-3xl font-bold tracking-tight">
                    <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                        Flashwall est conçu pour s'y adapter.
                    </span>
                </p>

            </div>

        </section>


        {{-- =========================================================
             WORLD CUP CASE STUDY
        ========================================================== --}}
        <section class="py-12 sm:py-16 lg:py-20">

            <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">

                <div>

                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-600 dark:text-teal-300">
                        Éprouvé sur le terrain
                    </p>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                        De la
                        <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                            Coupe du Monde
                        </span>
                        à votre événement.
                    </h2>

                    <div class="mt-6 space-y-5 text-lg leading-8 text-gray-600 dark:text-gray-300">

                        <p>
                            Flashwall a déjà été utilisé dans un véritable contexte
                            sportif international.
                        </p>

                        <p>
                            Lors de la
                            <strong class="text-gray-900 dark:text-white">
                                BMW IBU Biathlon World Cup 2025 à Annecy–Le Grand-Bornand
                            </strong>,
                            Flashwall a permis de recueillir les photos des spectateurs,
                            de modérer les contributions et d'afficher en direct le contenu du public
                            sur les écrans LED du stade.
                        </p>

                        <p>
                            L'expérience a également été personnalisée aux couleurs de l'événement,
                            démontrant exactement ce que Flashwall a été conçu pour accomplir :
                            une expérience professionnelle pour le public, qui s'intègre naturellement
                            à l'événement lui-même.
                        </p>

                    </div>

                </div>


                {{-- Case study visual --}}
                <div>

                    <div class="relative overflow-hidden rounded-3xl bg-gray-950 p-8 shadow-2xl sm:p-10">

                        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-teal-600/20 blur-3xl"></div>

                        <div class="relative">

                            <div class="flex items-center justify-between">

                                <span class="rounded-full bg-teal-400/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-teal-200">
                                    Étude de cas
                                </span>

                                <span class="text-sm text-gray-500">
                                    2025
                                </span>

                            </div>

                            <div class="mt-10">

                                <p class="mt-2 text-3xl font-bold text-white">
                                    BMW IBU World Cup
                                </p>

                                <p class="text-md font-medium tracking-wider text-gray-500">
                                    Annecy–Le Grand-Bornand
                                </p>

                                <p class="mt-2 text-gray-400">
                                    France
                                </p>

                            </div>

                            <div class="mt-10 grid grid-cols-2 gap-3">

                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Contenu du public diffusé en
                                    </p> 
                                    <p class="text-2xl font-bold text-white">
                                        Live
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Diffusion sur les
                                    </p>
                                    <p class="text-2xl font-bold text-white">
                                        Écrans géants
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Modération
                                    </p>
                                    <p class="text-2xl font-bold text-white">
                                        Rapide
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-2xl font-bold text-white">
                                        100 %
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        aux couleurs de l'événement
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- =========================================================
             PROFESSIONAL + ACCESSIBLE
        ========================================================== --}}
        <section class="rounded-3xl bg-gray-950 p-6 text-white sm:p-8 lg:p-10">

            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">

                <div>

                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-300">
                        Le professionnalisme au cœur du produit
                    </p>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                        Assez professionnel pour une production en direct.
                        Assez simple pour tout le monde.
                    </h2>

                </div>


                <div class="space-y-5 text-lg leading-8 text-gray-300">

                    <p>
                        Flashwall est conçu autour des réalités des événements en direct :
                        envoi rapide des images, modération simple, affichage fiable
                        et contrôle total de l'expérience visuelle.
                    </p>

                    <p>
                        Mais professionnel ne veut pas dire compliqué.
                        Flashwall est conçu pour être accessible aux organisateurs
                        d'événements de toutes tailles.
                    </p>

                </div>

            </div>


            <div class="mt-10 flex flex-wrap gap-3">

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Envoi rapide des images
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Modération simple
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Hautement personnalisable
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Affichage en direct
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Configuration simple
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Conçu pour les événements
                </span>

            </div>

        </section>


        {{-- =========================================================
             WHO IT IS FOR
        ========================================================== --}}
        <section class="py-12 sm:py-16 lg:py-20">

            <div class="mx-auto max-w-4xl text-center">

                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-300">
                    Conçu pour tous les types d'événements
                </p>

                <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                    L'engagement professionnel de l'audience
                    <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                        ne devrait pas être réservé aux grands événements.
                    </span>
                </h2>

                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Que vous organisiez un grand événement sportif ou une célébration
                    privée, Flashwall offre à votre audience un moyen simple de participer
                    et vous donne les outils pour intégrer son contenu à l'expérience.
                </p>

            </div>


            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">🏆</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Événements sportifs
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">💍</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Mariages
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">🎤</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Festivals et concerts
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">🏢</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Événements d'entreprise
                    </p>
                </div>

            </div>

        </section>


        {{-- =========================================================
             MADE IN FRANCE
        ========================================================== --}}
        <section class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-teal-50 p-6 dark:border-slate-700 dark:from-slate-900 dark:to-slate-800 sm:p-8 lg:p-10">

            <div class="grid gap-8 lg:grid-cols-[auto_1fr] lg:items-center">

                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-white text-4xl shadow-sm dark:bg-gray-800">
                    🇫🇷
                </div>

                <div>

                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-700 dark:text-teal-300">
                        Créé en France
                    </p>

                    <h2 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Créé dans les Alpes françaises.
                        Conçu pour les événements du monde entier.
                    </h2>

                    <p class="mt-5 max-w-3xl text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Flashwall est créé par un développeur freelance français,
                        expérimenté à la fois en développement web et en production événementielle.
                        La plateforme est hébergée en France sur une infrastructure OVH.
                    </p>

                </div>

            </div>

        </section>


        {{-- =========================================================
             THE FUTURE
        ========================================================== --}}
        <section class="py-12 sm:py-16 lg:py-20">

            <div class="mx-auto max-w-4xl text-center">

                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-600 dark:text-teal-300">
                    Et maintenant ?
                </p>

                <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                    Le Fan Wall n'est que le début.
                </h2>

                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Nous développons Flashwall pour en faire bien plus qu'un endroit où
                    des photos apparaissent à l'écran. L'objectif est de créer de nouvelles façons
                    pour les audiences de participer, d'interagir et de s'amuser.
                </p>

            </div>


            <div class="mt-10 grid gap-4 sm:grid-cols-3">

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">

                    <div class="text-2xl">🎯</div>

                    <p class="mt-4 font-bold text-gray-900 dark:text-white">
                        Plus d'interaction
                    </p>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        De nouvelles façons d'encourager le public à participer pendant tout l'événement.
                    </p>

                </div>


                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">

                    <div class="text-2xl">✨</div>

                    <p class="mt-4 font-bold text-gray-900 dark:text-white">
                        Plus de créativité
                    </p>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        De nouveaux outils pour transformer et partager le contenu créé par le public.
                    </p>

                </div>


                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">

                    <div class="text-2xl">🚀</div>

                    <p class="mt-4 font-bold text-gray-900 dark:text-white">
                        Plus de possibilités
                    </p>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Nous explorons constamment de nouvelles façons de rendre les événements
                        en direct plus captivants.
                    </p>

                </div>

            </div>

        </section>


        {{-- =========================================================
             FINAL CTA
        ========================================================== --}}
        <section class="mb-12 overflow-hidden rounded-3xl bg-gradient-to-r from-teal-700 to-amber-500 p-6 text-white shadow-[0_20px_60px_-20px_rgba(15,118,110,0.3)] sm:p-8 lg:p-10">

            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                <div>

                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-100">
                        Faites vivre l'énergie de votre audience sur vos écrans
                    </p>

                    <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                        Créez votre premier Flashwall.
                    </h2>

                    <p class="mt-3 max-w-2xl text-lg text-purple-50/90">
                        Commencez gratuitement, personnalisez votre expérience et découvrez
                        ce que votre audience peut apporter à votre événement.
                    </p>

                </div>


                <a href="{{ route('register') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-full bg-white px-6 py-3 font-semibold text-teal-800 transition hover:bg-teal-50">
                    Commencer
                </a>

            </div>

        </section>


        {{-- =========================================================
             NEWSLETTER
        ========================================================== --}}
        <section class="mb-16">

            <div class="mx-auto max-w-2xl text-center">

                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-600 dark:text-teal-300">
                    Restez informé
                </p>

                <h2 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Ne manquez pas la suite.
                </h2>

                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Suivez l'évolution de Flashwall tandis que nous ajoutons de nouvelles façons
                    d'engager votre audience.
                </p>

            </div>


            <div class="mt-6 flex justify-center">

                <x-form wire:submit="saveEmail">

                    <div class="join flex flex-col rounded-xl bg-linear-to-r from-teal-600 to-amber-400 p-1 sm:flex-row">

                        <input
                            wire:model="email"
                            maxlength="150"
                            required
                            type="email"
                            class="join-item input border-none bg-white dark:text-gray-800"
                            placeholder="email-address@domain.com"
                        />

                        <x-button
                            icon="o-paper-airplane"
                            spinner="saveEmail"
                            type="submit"
                            class="join-item border-none bg-transparent text-xl text-white hover:text-teal-900"
                        >
                            S'inscrire
                        </x-button>

                    </div>

                    @error('email')
                        <span class="mt-2 block text-sm text-red-500">
                            {{ $message }}
                        </span>
                    @enderror

                </x-form>

            </div>

        </section>

    </div>

</x-layouts.app>