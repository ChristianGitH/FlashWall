<x-layouts.app title="Events">
    <x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

    <div class="mx-auto mt-2 max-w-6xl">
        <section class="rounded-[2rem] border border-indigo-100 bg-gradient-to-br from-white via-violet-50 to-sky-100 p-8 shadow-[0_20px_80px_-20px_rgba(99,102,241,0.3)] dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-950 sm:p-10 lg:p-12">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-500">
                    For events
                </p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                    Turn your audience into an active part of the experience.
                </h1>
                <p class="mt-5 text-xl leading-8 text-gray-700 dark:text-gray-300">
                    Collect photos, reactions, and messages from attendees in real time and bring them to life on a shared screen.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:opacity-90">
                        Create your event wall
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-indigo-200 px-6 py-3 font-semibold text-indigo-700 transition hover:bg-white/70 dark:border-gray-700 dark:text-indigo-300 dark:hover:bg-gray-800/70">
                        See how it works
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-gray-200 bg-white/80 p-6 shadow-[0_20px_60px_-20px_rgba(99,102,241,0.2)] backdrop-blur dark:border-gray-800 dark:bg-gray-900/70 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-500">
                        A live and engaging experience
                    </p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        From the first guest to the final applause, every moment can be shared instantly.
                    </h2>
                </div>
                <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                    Flashwall helps you create a spontaneous, interactive atmosphere where attendees become co-creators of the event.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-violet-50 p-5 dark:border-gray-700 dark:from-gray-800 dark:to-gray-800/90">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">1. Guests join in seconds</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        A QR code or link gives everyone immediate access, whether they are in the audience or online.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">2. They send photos or messages</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        A special moment, a quick reaction, or a short message can be shared in just a few taps.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">3. You shape the flow</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Approve what appears, keep the energy aligned with the event, and create a polished experience.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">4. It appears live on screen</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Your event wall becomes a living visual highlight throughout the day or evening.
                    </p>
                </article>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-indigo-200 bg-gradient-to-br from-indigo-600 to-violet-600 p-6 text-white shadow-[0_20px_60px_-20px_rgba(99,102,241,0.35)] sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-100">Why organizers choose it</p>
                    <h2 class="mt-2 text-2xl font-bold">
                        No more waiting around or downtime – it’s time for interaction and participation.
                    </h2>
                    <p class="mt-3 max-w-2xl text-lg text-indigo-50/90">
                        Make every event more connected, dynamic and memorable. Whether it’s a conference, a launch, a festival or a private gathering, Flashwall changes attendance into a shared visual experience.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 font-semibold text-indigo-700 transition hover:bg-indigo-50">
                        Start for free
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-white/50 p-6 shadow-[0_20px_60px_-20px_rgba(99,102,241,0.2)] sm:p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Built to fit the identity of your event.
            </h2>
            <p class="mt-4 max-w-3xl text-lg leading-8 text-gray-700 dark:text-gray-300">
                Customize the design, colors, and layout to reflect your brand, your theme, and the atmosphere of the moment. Flashwall adapts effortlessly to both intimate and large-scale gatherings.
            </p>
            <p class="mt-6 text-lg text-gray-700 dark:text-gray-300">
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 dark:text-indigo-400">Create an account</a> and set up a custom wall for your event. 
            </p>
        </section>
    </div>
</x-layouts.app>
