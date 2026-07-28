<x-layouts.app title="Événements">
    <x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

    <div class="mx-auto mt-2 max-w-6xl">
        <section class="rounded-[2rem] border border-indigo-100 bg-gradient-to-br from-white via-violet-50 to-sky-100 p-8 shadow-[0_20px_80px_-20px_rgba(99,102,241,0.3)] dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-950 sm:p-10 lg:p-12">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-500">
                    Pour les événements
                </p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                    Faites de votre audience un acteur de l’expérience.
                </h1>
                <p class="mt-5 text-xl leading-8 text-gray-700 dark:text-gray-300">
                    Rassemblez en direct photos, réactions et messages des participants pour les faire vivre sur un écran partagé.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:opacity-90">
                        Créer votre wall
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-indigo-200 px-6 py-3 font-semibold text-indigo-700 transition hover:bg-white/70 dark:border-gray-700 dark:text-indigo-300 dark:hover:bg-gray-800/70">
                        Voir comment ça marche
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-gray-200 bg-white/80 p-6 shadow-[0_20px_60px_-20px_rgba(99,102,241,0.2)] backdrop-blur dark:border-gray-800 dark:bg-gray-900/70 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-500">
                        Une expérience vivante et engageante
                    </p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        Du premier invité jusqu’aux derniers applaudissements, chaque moment peut être partagé instantanément.
                    </h2>
                </div>
                <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                    Flashwall vous aide à créer une ambiance spontanée et interactive où chaque participant devient acteur de l’événement.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-violet-50 p-5 dark:border-gray-700 dark:from-gray-800 dark:to-gray-800/90">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">1. Les invités rejoignent en quelques secondes</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Un QR code ou un lien donne immédiatement accès à tous, que ce soit dans l’assistance ou en ligne.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">2. Ils envoient photos ou messages</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Un moment marquant, une réaction spontanée ou un petit message peuvent être partagés en quelques clics.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">3. Vous maîtrisez le flux</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Validez ce qui apparaît, gardez l’énergie en phase avec l’événement et offrez une expérience soignée.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">4. Le contenu apparaît en direct sur l’écran</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Votre wall devient un élément vivant tout au long de la journée ou de la soirée.
                    </p>
                </article>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-indigo-200 bg-gradient-to-br from-indigo-600 to-violet-600 p-6 text-white shadow-[0_20px_60px_-20px_rgba(99,102,241,0.35)] sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-100">Pourquoi les organisateurs l’adoptent</p>
                    <h2 class="mt-2 text-2xl font-bold">
                        Fini les moments d'attentes ou les temps morts, place à l’interaction et à la participation.
                    </h2>
                    <p class="mt-3 max-w-2xl text-lg text-indigo-50/90">
                        Rendez chaque événement plus connecté, dynamique et mémorable. 
                        Qu’il s’agisse d’une conférence, d’un lancement, d’un festival ou d’un rassemblement privé, Flashwall transforme la participation en moment visuel partagé.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="text-center inline-flex items-center justify-center rounded-full bg-white px-5 py-3 font-semibold text-indigo-700 transition hover:bg-indigo-50">
                        Essayer gratuitement
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-white/50 p-6 shadow-[0_20px_60px_-20px_rgba(99,102,241,0.2)] sm:p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Pensé pour s’adapter à l’identité de votre événement.
            </h2>
            <p class="mt-4 max-w-3xl text-lg leading-8 text-gray-700 dark:text-gray-300">
                Personnalisez le design, les couleurs et la mise en page pour refléter votre image, votre thème et l’ambiance du moment. Flashwall s’adapte aussi bien aux événements intimes qu’aux grands rassemblements.
            </p>
            <p class="mt-6 text-lg text-gray-700 dark:text-gray-300">
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 dark:text-indigo-400">Créer un compte</a> et préparez un wall sur mesure pour votre événement.
            </p>
        </section>
    </div>
</x-layouts.app>
