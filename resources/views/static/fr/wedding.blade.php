<x-layouts.app title="Mariages">
    <x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

    <div class="mx-auto mt-2 max-w-6xl">
        <section class="rounded-[2rem] border border-pink-100 bg-gradient-to-br from-white via-fuchsia-50 to-rose-100 p-8 shadow-[0_20px_80px_-20px_rgba(236,72,153,0.35)] dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-950 sm:p-10 lg:p-12">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-pink-500">
                    Mariages
                </p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                    Flashwall fera entrer l'amour de vos invités sur vos écrans!
                </h1>
                <p class="mt-5 text-xl leading-8 text-gray-700 dark:text-gray-300">
                    Rassemblez en direct photos, messages et souvenirs de vos proches pour les afficher avec élégance sur grand écran.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-pink-500 to-fuchsia-500 px-6 py-3 font-semibold text-white shadow-lg shadow-pink-500/20 transition hover:opacity-90">
                        Créer votre wall de mariage
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-pink-200 px-6 py-3 font-semibold text-pink-700 transition hover:bg-white/70 dark:border-gray-700 dark:text-pink-300 dark:hover:bg-gray-800/70">
                        Voir comment ça marche
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-gray-200 bg-white/80 p-6 dark:border-gray-800 dark:bg-gray-900/70 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[#b14fff]">
                        Une expérience de mariage qui prend vie
                    </p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        De la cérémonie à la soirée, vos invités peuvent contribuer en quelques secondes.
                    </h2>
                </div>
                <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                    C’est une façon originale de transformer les émotions, les éclats de rire et les moments spontanés en célébration partagée.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-pink-100 bg-gradient-to-br from-pink-50 to-rose-50 p-5 dark:border-gray-700 dark:from-gray-800 dark:to-gray-800/90">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">1. Les invités scannent un QR code</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Autour de la table, sur la piste de danse ou pendant le cocktail, chacun peut participer instantanément.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">2. Ils partagent une photo ou un message</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Un souvenir tendre, un moment drôle ou un message peut être envoyé en quelques clics.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">3. Vous modérez avec simplicité</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Validez les meilleurs contenus, gardez le bon ton et offrez une expérience à votre image.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">4. Le contenu apparaît en direct sur l’écran</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Votre mur de mariage devient un album vivant qui grandit au fil de la journée.
                    </p>
                </article>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-pink-200 bg-gradient-to-br from-pink-600 to-fuchsia-600 p-6 text-white shadow-[0_20px_60px_-20px_rgba(236,72,153,0.35)] sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-pink-100">Pourquoi les couples l’adorent</p>
                    <h2 class="mt-2 text-2xl font-bold">
                        Une façon moderne et personnelle d'ajouter du partage et de l'intéraction à votre événement.
                    </h2>
                    <p class="mt-3 max-w-2xl text-lg text-pink-50/90">
                        Des réceptions intimes aux grands rassemblements, Flashwall crée une ambiance décontractée et interactive, à la hauteur de l'enjeu.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 font-semibold text-pink-700 transition hover:bg-pink-50">
                        Essayer gratuitement
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-white/70 px-5 py-3 font-semibold text-white transition hover:bg-white/10">
                        Demander une démo
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-white/50 p-6 shadow-[0_20px_60px_-20px_rgba(217,70,239,0.2)] sm:p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Pensé pour s'accorder parfaitement avec votre thème !
            </h2>
            <p class="mt-4 max-w-3xl text-lg leading-8 text-gray-700 dark:text-gray-300">
                Personnalisez le style visuel, les couleurs et la mise en page pour qu’ils reflètent votre thème, vos prénoms et l'esprit de la journée. Qu'elle soit élégante, romantique ou festive, Flashwall s'intègre naturellement à l'évènement.
            </p>
            <p class="mt-6 text-lg text-gray-700 dark:text-gray-300">
                <a href="{{ route('register') }}" class="font-semibold text-pink-600 dark:text-pink-400">Créer un compte</a> et préparez un mur qui ressemble à votre journée.
            </p>
        </section>
    </div>
</x-layouts.app>
