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

        // Optional: confirmation message
        $this->success('Thank you for subscribing', 'Success');

        // Reset field
        $this->email = '';
    }
}

?>
<x-layouts.app :title="'Accueil'">
<x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />
<div class="mx-auto mt-2 max-w-6xl">
    <section class="">
        <h1 class="mt-2 inline-block text-4xl font-bold tracking-tighter sm:text-6xl md:text-6xl dark:from-gray-50 dark:to-gray-300">
            <span class="font-bold bg-linear-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
                Flashwall
            </span>
            <span class="font-bold"> - Faites entrer l’énergie du public sur vos écrans !</span>
        </h1>

        <p class="mt-1 text-xl text-gray-700 dark:text-gray-300">
            Un outil évolutif, simple et rapide, conçu par et pour les organisateurs d’événements.
        </p>

        <p class="mt-1 text-xl text-gray-700 dark:text-gray-300">
            Recevez, modérez et affichez le contenu de votre public sans effort.
        </p>

        <section class="mt-8 rounded-3xl border border-purple-100 bg-white/80 p-6 shadow-[0_20px_60px_-20px_rgba(168,85,247,0.25)] backdrop-blur dark:border-gray-800 dark:bg-gray-900/70 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-purple-500">
                        Comment ça marche
                    </p>
                    <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        Des téléphones du public au grand écran en quelques étapes simples
                    </h3>
                </div>
                <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                    Une expérience fluide pour les invités et un contrôle total pour les organisateurs.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-gray-200 bg-gradient-to-br from-purple-50 to-pink-50 p-5 transition-transform duration-300 hover:-translate-y-1 dark:border-gray-700 dark:from-gray-800 dark:to-gray-800/90">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-600 text-white shadow-lg shadow-purple-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">1. Scanner le QR code</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Les invités scannent un QR code ou cliquent sur un lien et accèdent instantanément à l’expérience.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 transition-transform duration-300 hover:-translate-y-1 dark:border-gray-700 dark:bg-gray-900/70">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-500 text-white shadow-lg shadow-pink-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">2. Envoyer une photo</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        En quelques secondes, le public partage un souvenir, un moment ou une émotion.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 transition-transform duration-300 hover:-translate-y-1 dark:border-gray-700 dark:bg-gray-900/70">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500 text-white shadow-lg shadow-indigo-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">3. L’organisateur modère - ou pas</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Chaque envoi peut être approuvé ou refusé en un clic pour préserver l’ambiance.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 transition-transform duration-300 hover:-translate-y-1 dark:border-gray-700 dark:bg-gray-900/70">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">4. Les photos apparaissent sur l’écran</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Le contenu approuvé s’affiche en direct sur le grand écran.
                    </p>
                </article>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-purple-200 bg-gradient-to-br from-purple-600 to-pink-500 p-6 text-white shadow-[0_20px_60px_-20px_rgba(168,85,247,0.35)] sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-purple-100">
                        Essayez gratuitement
                    </p>
                    <h3 class="mt-2 text-2xl font-bold">
                        Créez un compte gratuit et configurez votre premier wall en quelques minutes
                    </h3>
                    <p class="mt-3 max-w-2xl text-lg text-purple-50/90">
                        Découvrez Flashwall en pratique, personnalisez votre expérience et préparez votre événement sans engagement.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 font-semibold text-purple-700 transition hover:bg-purple-50">
                        Créer un compte
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-white/70 px-5 py-3 font-semibold text-white transition hover:bg-white/10">
                        Essayer gratuitement
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-white-200 p-6 shadow-[0_20px_60px_-20px_rgba(217,70,239,0.25)] sm:p-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-fuchsia-600 dark:text-fuchsia-400">
                        Personnalisation avancée
                    </p>
                    <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        Adaptez chaque détail à l’identité de votre événement
                    </h3>
                </div>
                <div class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm dark:bg-gray-800/80 dark:text-gray-200">
                    Couleurs • Mise en page • Animations • Arrière-plans
                </div>
            </div>
            <p class="mt-5 text-lg leading-8 text-gray-700 dark:text-gray-300">
                Une expérience parfaitement intégrée à votre événement. Grâce aux options de personnalisation les plus avancées, vous pouvez façonner l’identité visuelle de votre mur, de l’affichage à l’accueil des utilisateurs, et obtenir exactement ce que vous souhaitez.
            </p>

            <p class="mt-5">
                <a href="{{ route('register') }}" class="btn btn-primary text-white">
                    Se connecter
                </a>
                <span class="text-lg leading-8 text-gray-700 dark:text-gray-300"> pour découvrir tous les réglages disponibles.</span>
            </p>
        </section>

        <h2 class="mt-8 text-2xl font-bold sm:text-4xl md:text-4xl">
            <span class="font-bold bg-linear-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
                Flashwall
            </span>
            donnera l’impression d’avoir été conçu spécialement pour votre événement !
        </h2>

        <div class="mt-10 w-full flex justify-center">
            <x-form wire:submit="saveEmail">
                <div class="px-2 join flex flex-col lg:flex-row rounded-lg justify-center items-center bg-linear-to-r from-purple-500 to-pink-300">
                    <div>
                        <p style="font-family: Figtree;" class="rounded-l-lg join-item px-2 text-white text-2xl">
                            Ne manquez pas la suite :
                        </p>
                    </div>

                    <div class="join-item bg-transparent">
                        <input
                            wire:model="email"
                            maxlength="150"
                            required
                            class="m-1 focus:border-white border-none bg-white input rounded-none dark:text-gray-800"
                            placeholder="adresse-email@domaine.com"
                        />
                    </div>

                    <div>
                        <x-button
                            icon="o-paper-airplane"
                            spinner="saveEmail"
                            type="submit"
                            style="font-family: Figtree;"
                            class="hover:text-[#ad48ff] rounded-r-lg btn border-none join-item bg-transparent text-white text-2xl"
                        >
                            C’est parti !
                        </x-button>
                    </div>
                </div>

                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-form>
        </div>

    </section>
</div>
</x-layouts.app>