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

        // Optionnel : message de confirmation
        $this->success('Thank you for subscribing', 'Success');

        // Reset du champ
        $this->email = '';
    }
}

?>

<x-layouts.app :title="'Home'">
<x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />
<div class="mx-auto mt-8 max-w-6xl">
    <section class="">
        <h1 class="mt-2 inline-block text-4xl font-bold tracking-tighter sm:text-6xl md:text-6xl dark:from-gray-50 dark:to-gray-300">
            <span class="font-bold">Bienvenue sur</span>
            <span class="font-bold bg-linear-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
                Flashwall
            </span>
        </h1>

        <p class="mt-3 text-xl text-gray-700 dark:text-gray-300">
            Flashwall sera bientôt officiellement lancé et accessible à tous. Restez connectés.
        </p>

        <h2 class="mt-8 text-2xl font-bold sm:text-4xl md:text-4xl">
            Faites entrer l'énergie du public sur vos écrans !
        </h2>

        <p class="mt-1 text-xl text-gray-700 dark:text-gray-300">
            Un outil évolutif, rapide et facile à utiliser, conçu par et pour les organisateurs d’événements.
        </p>

        <p class="mt-1 text-xl text-gray-700 dark:text-gray-300">
            Recevez, modérez et affichez le contenu de votre public sans effort.
        </p>

        <p class="mt-5 text-xl text-gray-700 dark:text-gray-300">
            <span class="text-gray-900 font-bold dark:text-gray-200">
                Une expérience qui s’intègre parfaitement à votre événement.
            </span>
            Avec les options de personnalisation les plus avancées : couleur, mise en page, animations ; de l’affichage à l’écran, à l’accueil des utilisateurs, vous obtenez exactement ce que vous souhaitez.
        </p>

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
                            Pour ne pas manquer la suite :
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
                            Go !
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