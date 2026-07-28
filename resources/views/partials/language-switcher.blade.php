<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    @php($current_locale_code = strtoupper($current_locale))

    <details class="group relative" x-bind:open="open" @toggle="open = $event.target.open">
        <summary class="flex cursor-pointer list-none items-center gap-1 btn btn-ghost btn-sm">
            <span class="tracking-wide">{{ $current_locale_code }}</span>
            <svg class="h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
            </svg>
        </summary>

        <div class="absolute left-0 z-20 mt-1 min-w-20 rounded-lg border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-700 dark:bg-zinc-900">
            @foreach($available_locales as $locale_name => $available_locale)
                <a
                    href="{{ url('language/' . $available_locale) }}"
                    class="block px-3 py-2 text-sm {{ $available_locale === $current_locale ? 'bg-slate-100 font-semibold text-slate-900 dark:bg-slate-800 dark:text-white' : 'font-normal text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}"
                >
                    {{ $locale_name }}
                </a>
            @endforeach
        </div>
    </details>
</div>