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

        $this->success('Thank you for subscribing', 'Success');

        $this->email = '';
    }
}

?>

<x-layouts.app :title="$pageMetadata['title'] ?? 'About Flashwall'">
    <x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

    <div class="mx-auto max-w-6xl px-4 pb-12 sm:px-6 sm:pb-16 lg:px-8">

        {{-- =========================================================
             HERO
        ========================================================== --}}
        <section class="relative overflow-hidden rounded-3xl border border-teal-100 bg-gradient-to-br from-teal-50 via-white to-amber-50 p-6 py-12 shadow-sm dark:border-teal-900/70 dark:from-slate-950 dark:via-slate-900 dark:to-teal-950 sm:p-10 sm:py-16 lg:p-14 lg:py-20">

            <div class="relative max-w-4xl">

                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-600 dark:text-teal-300">
                    About Flashwall
                </p>

                <h1 class="mt-4 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl dark:text-white">
                    Professional audience engagement,
                    <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                        built for live events.
                    </span>
                </h1>

                <p class="mt-6 max-w-3xl text-xl leading-8 text-gray-600 dark:text-gray-300">
                    Flashwall is a live audience photo wall designed to bring the energy
                    of your audience to your screens — with the speed, reliability and
                    customization that real events demand.
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
                        Where it started
                    </p>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                        Born from real event experience.
                    </h2>

                    <div class="mt-6 space-y-4 text-lg leading-8 text-gray-600 dark:text-gray-300">

                        <p>
                            Flashwall didn't start as a generic software idea.
                            It started behind the screens of live events.
                        </p>

                        <p>
                            After years of working on international sporting events,
                            including World Cup productions, we used a number of existing
                            Fan Wall solutions to bring audience content onto the big screen.
                        </p>

                        <p>
                            They worked — but they often came with the same limitations:
                            difficult moderation, complicated configuration and, above all,
                            very limited customization.
                        </p>

                        <p>
                            We knew we could build something better.
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
                                    Live events
                                </span>

                            </div>


                            <p class="mt-8 text-3xl font-bold leading-tight">
                                Built by people who understand what happens
                                <span class="text-teal-300">
                                    behind the screens.
                                </span>
                            </p>


                            <div class="mt-8 h-px bg-white/10"></div>


                            <p class="mt-6 text-sm leading-6 text-gray-400">
                                From LED and video production to audience engagement,
                                Flashwall comes from the reality of live event production.
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
                        Why Flashwall?
                    </p>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                        Your event is unique.
                        <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                            Your Fan Wall should be too.
                        </span>
                    </h2>

                </div>


                <div class="space-y-5 text-lg leading-8 text-gray-600 dark:text-gray-300">

                    <p>
                        A sports event doesn't look like a wedding.
                        A festival doesn't look like a corporate conference.
                        Every event has its own identity, audience and atmosphere.
                    </p>

                    <p>
                        We wanted to create a Fan Wall that could adapt to that identity,
                        rather than forcing every event into the same template.
                    </p>

                    <p>
                        That's why customization is at the heart of Flashwall.
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
                    x-data="{ eventTypes: ['Wedding',
                    'Conference',
                    'Concert',
                    'Seminar',
                    'Festival',
                    'Football game'], currentEvent: 0, isVisible: true }"
                    x-init="setInterval(() => { isVisible = false; setTimeout(() => { currentEvent = (currentEvent + 1) % eventTypes.length; isVisible = true }, 250) }, 1000)"
                    class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-700 dark:text-teal-300"
                >
                    Designed specifically for your <span x-text="eventTypes[currentEvent]" :class="{ 'opacity-0': !isVisible }" class="inline-block transition-opacity duration-300">event</span>
                </p>
            </div>


            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

                <div>
                    <h2 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Make Flashwall look like it was made just for you.
                    </h2>

                </div>

                <p class="max-w-xl text-lg leading-7 text-gray-600 dark:text-gray-300">
                    From the smallest details to the overall visual experience,
                    Flashwall gives organizers the freedom to create their own look.
                </p>

            </div>


            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">🎨</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Visual identity
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Colors, fonts, logos and backgrounds.
                    </p>

                </div>


                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">🖥️</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Screen experience
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Layouts, transitions, animations and display settings.
                    </p>

                </div>


                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">📱</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Audience experience
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Customize the way your audience discovers and uses your wall.
                    </p>

                </div>


                <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-700 dark:bg-slate-800/90">

                    <div class="text-2xl">✨</div>

                    <h3 class="mt-3 font-bold text-gray-900 dark:text-white">
                        Your creativity
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Create an experience that feels unique to your event.
                    </p>

                </div>

            </div>


            <div class="mt-8 text-center">

                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Your event has its own identity.
                </p>

                <p class="mt-2 text-3xl font-bold tracking-tight">
                    <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                        Flashwall is designed to match it.
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
                        Built in the field
                    </p>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                        From the
                        <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                            World Cup
                        </span>
                        to your event.
                    </h2>

                    <div class="mt-6 space-y-5 text-lg leading-8 text-gray-600 dark:text-gray-300">

                        <p>
                            Flashwall has already been put to work in a real international
                            sporting environment.
                        </p>

                        <p>
                            During the
                            <strong class="text-gray-900 dark:text-white">
                                2025 BMW IBU Biathlon World Cup in Annecy–Le Grand-Bornand
                            </strong>,
                            Flashwall was used to collect photos from spectators,
                            moderate submissions and display audience content live
                            on the stadium's LED screens.
                        </p>

                        <p>
                            The experience was also branded specifically for the event,
                            demonstrating exactly what Flashwall was created to achieve:
                            a professional audience experience that feels like a natural
                            part of the event itself.
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
                                    Case study
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
                                    <p class="text-2xl font-bold text-white">
                                        Live
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Audience content
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-2xl font-bold text-white">
                                        LED
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Stadium screens
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-2xl font-bold text-white">
                                        Fast
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Moderation
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-2xl font-bold text-white">
                                        Custom
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Event branding
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
                        Professional at its core
                    </p>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                        Serious enough for a live production.
                        Simple enough for everyone.
                    </h2>

                </div>


                <div class="space-y-5 text-lg leading-8 text-gray-300">

                    <p>
                        Flashwall is designed around the realities of live events:
                        fast image submission, easy moderation, reliable display
                        and complete control over the visual experience.
                    </p>

                    <p>
                        But professional doesn't have to mean complicated.
                        Flashwall is built to be accessible to event organizers
                        of all sizes.
                    </p>

                </div>

            </div>


            <div class="mt-10 flex flex-wrap gap-3">

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Fast image submission
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Easy moderation
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Highly customizable
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Live display
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Easy setup
                </span>

                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium">
                    Built for events
                </span>

            </div>

        </section>


        {{-- =========================================================
             WHO IT IS FOR
        ========================================================== --}}
        <section class="py-12 sm:py-16 lg:py-20">

            <div class="mx-auto max-w-4xl text-center">

                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-300">
                    Made for every kind of event
                </p>

                <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                    Professional audience engagement
                    <span class="bg-linear-to-r from-teal-500 to-amber-400 bg-clip-text text-transparent">
                        shouldn't be reserved for major events.
                    </span>
                </h2>

                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Whether you're organizing a major sporting event or a private
                    celebration, Flashwall gives your audience a simple way to
                    participate and gives you the tools to make their content part
                    of the experience.
                </p>

            </div>


            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">🏆</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Sports events
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">💍</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Weddings
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">🎤</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Festivals & concerts
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-3xl">🏢</div>
                    <p class="mt-3 font-bold text-gray-900 dark:text-white">
                        Corporate events
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
                        Built in France
                    </p>

                    <h2 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Created in the French Alps.
                        Built for events everywhere.
                    </h2>

                    <p class="mt-5 max-w-3xl text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Flashwall is created by a French freelance developer with
                        experience in both web development and live event production.
                        The platform is hosted in France on OVH infrastructure.
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
                    What's next?
                </p>

                <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                    The Fan Wall is just the beginning.
                </h2>

                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    We're building Flashwall to become more than a place where
                    photos appear on a screen. The goal is to create new ways
                    for audiences to participate, interact and have fun.
                </p>

            </div>


            <div class="mt-10 grid gap-4 sm:grid-cols-3">

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">

                    <div class="text-2xl">🎯</div>

                    <p class="mt-4 font-bold text-gray-900 dark:text-white">
                        More interaction
                    </p>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        New ways to encourage audiences to participate throughout an event.
                    </p>

                </div>


                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">

                    <div class="text-2xl">✨</div>

                    <p class="mt-4 font-bold text-gray-900 dark:text-white">
                        More creativity
                    </p>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        New tools to transform and share the content audiences create.
                    </p>

                </div>


                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">

                    <div class="text-2xl">🚀</div>

                    <p class="mt-4 font-bold text-gray-900 dark:text-white">
                        More possibilities
                    </p>

                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        We're constantly exploring new ways to make live events more engaging.
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
                        Bring the energy of your audience to your screens
                    </p>

                    <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                        Create your first Flashwall.
                    </h2>

                    <p class="mt-3 max-w-2xl text-lg text-purple-50/90">
                        Start for free, customize your experience and discover
                        what your audience can bring to your event.
                    </p>

                </div>


                <a href="{{ route('register') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-full bg-white px-6 py-3 font-semibold text-teal-800 transition hover:bg-teal-50">
                    Get started
                </a>

            </div>

        </section>


        {{-- =========================================================
             NEWSLETTER
        ========================================================== --}}
        <section class="mb-16">

            <div class="mx-auto max-w-2xl text-center">

                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-600 dark:text-teal-300">
                    Stay in the loop
                </p>

                <h2 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Don't miss what comes next.
                </h2>

                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Follow Flashwall as we add new ways to engage your audience.
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
                            Subscribe
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