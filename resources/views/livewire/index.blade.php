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
            $this->success(__('Thank you for subscribing'), __('Success'));

        // Reset du champ
        $this->email = '';
    }
}

?>

<div class="mx-auto mt-8 max-w-6xl">
    <section class="">
        <h1 class="mt-2 inline-block text-4xl font-bold tracking-tighter sm:text-6xl md:text-6xl dark:from-gray-50 dark:to-gray-300">
            <span class="font-bold">{{ __('home.welcome') }}</span>
            <span class="font-bold bg-linear-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
                {{ __('home.flashwall') }}
            </span>
        </h1>
    
        <p class="mt-3 text-xl text-gray-700 dark:text-gray-300">
            {{ __('home.coming_soon') }}
        </p>

        <h2 class="mt-8 text-2xl font-bold sm:text-4xl md:text-4xl">{{ __('home.bring_energy') }}</h2>
        <p class="mt-1 text-xl text-gray-700 dark:text-gray-300">{{ __('home.tool_description') }}</p>
        <p class="mt-1 text-xl text-gray-700 dark:text-gray-300">{{ __('home.receive_moderate_display') }}</p>

        <p class="mt-5 text-xl text-gray-700 dark:text-gray-300">
            <span class="text-gray-900 font-bold dark:text-gray-200">{{ __('home.experience') }}</span>
            {{ __('home.experience_details') }}
        </p>

        <h2 class="mt-8 text-2xl font-bold sm:text-4xl md:text-4xl">
            <span class="font-bold bg-linear-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
                {{ __('home.flashwall') }}
            </span>
            {{ __('home.flashwall_custom') }}
        </h2>

        <div class="mt-10 w-full flex justify-center">
            <x-form wire:submit="saveEmail">
                <div class="px-2 join flex flex-col lg:flex-row rounded-lg justify-center items-center bg-linear-to-r from-purple-500 to-pink-300">
                    <div>
                        <p style="font-family: Figtree;" class="rounded-l-lg join-item px-2 text-white text-2xl">{{ __('home.newsletter_input') }}</p>
                    </div>
                    <div class="join-item bg-transparent">
                        <input wire:model="email" maxlength="150" required class="m-1 focus:border-white border-none bg-white input rounded-none dark:text-gray-800" placeholder="{{ __('home.email_exemple') }}" />
                    </div>
                    <div>
                        <x-button icon="o-paper-airplane" spinner="saveEmail" type="submit" style="font-family: Figtree;" class="hover:text-[#ad48ff] rounded-r-lg btn border-none join-item bg-transparent text-white text-2xl">Go !</x-button>
                    </div>
                </div>
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-form>
        </div>

    </section>
</div>


