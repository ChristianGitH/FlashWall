<x-layouts.app title="Weddings">
    <x-seo-head-tags :locale="$locale" :pageKey="$pageKey" />

    <div class="mx-auto mt-2 max-w-6xl">
        <section class="rounded-[2rem] border border-pink-100 bg-gradient-to-br from-white via-fuchsia-50 to-rose-100 p-8 shadow-[0_20px_80px_-20px_rgba(236,72,153,0.35)] dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-950 sm:p-10 lg:p-12">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-pink-500">
                    Weddings
                </p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                    Make every guest part of your wedding story.
                </h1>
                <p class="mt-5 text-xl leading-8 text-gray-700 dark:text-gray-300">
                    Collect photos, messages, and joyful memories from your loved ones in real time and display them beautifully on the big screen.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-pink-500 to-fuchsia-500 px-6 py-3 font-semibold text-white shadow-lg shadow-pink-500/20 transition hover:opacity-90">
                        Create your wedding wall
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-pink-200 px-6 py-3 font-semibold text-pink-700 transition hover:bg-white/70 dark:border-gray-700 dark:text-pink-300 dark:hover:bg-gray-800/70">
                        See how it works
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-gray-200 bg-white/80 p-6 dark:border-gray-800 dark:bg-gray-900/70 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[#b14fff]">
                        A wedding experience that feels alive
                    </p>
                    <h2 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        From the ceremony to the afterparty, your guests can contribute in seconds.
                    </h2>
                </div>
                <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                    It is the perfect way to turn emotions, laughter, and candid moments into a shared visual celebration.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-pink-100 bg-gradient-to-br from-pink-50 to-rose-50 p-5 dark:border-gray-700 dark:from-gray-800 dark:to-gray-800/90">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">1. Guests scan a QR code</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Whether at the table, on the dance floor, or during the cocktail hour, everyone can join instantly.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">2. They share a photo or message</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        A sweet memory, a funny moment, or a heartfelt note can be submitted in just a few taps.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">3. You moderate with ease</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Approve the best submissions, keep the tone right, and ensure the content feels as elegant as the event itself.
                    </p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white/70 p-5 dark:border-gray-700 dark:bg-gray-900/70">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">4. It appears live on screen</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Your wedding wall becomes a living album that grows throughout the celebration.
                    </p>
                </article>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-pink-200 bg-gradient-to-br from-pink-600 to-fuchsia-600 p-6 text-white shadow-[0_20px_60px_-20px_rgba(236,72,153,0.35)] sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-pink-100">Why couples love it</p>
                    <h2 class="mt-2 text-2xl font-bold">
                        A modern and personal way to make your celebration unforgettable.
                    </h2>
                    <p class="mt-3 max-w-2xl text-lg text-pink-50/90">
                        From intimate receptions to large gatherings, Flashwall helps create a warm, interactive atmosphere that feels personal from the first moment.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 font-semibold text-pink-700 transition hover:bg-pink-50">
                        Start for free
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-white/70 px-5 py-3 font-semibold text-white transition hover:bg-white/10">
                        Request a demo
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-8 rounded-3xl border border-white/50 p-6 shadow-[0_20px_60px_-20px_rgba(217,70,239,0.2)] sm:p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Designed to match your theme perfectly.
            </h2>
            <p class="mt-4 max-w-3xl text-lg leading-8 text-gray-700 dark:text-gray-300">
                Customize the visual style, colors, and layout to fit your wedding theme, your names, and the mood of the day. Whether your celebration is elegant, romantic, or vibrant, Flashwall fits naturally into the experience.
            </p>
            <p class="mt-6 text-lg text-gray-700 dark:text-gray-300">
                <a href="{{ route('register') }}" class="font-semibold text-pink-600 dark:text-pink-400">Create an account</a> and prepare a wall that reflects your special day.
            </p>
        </section>
    </div>
</x-layouts.app>
