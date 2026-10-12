    @php($current_locale_code = strtoupper($current_locale))
    <x-dropdown label="{{ $current_locale_code }}" class="btn-ghost" right>
            @foreach($available_locales as $locale_name => $available_locale)
            <a
                    href="{{ url('language/' . $available_locale) }}"
                    class="block px-3 py-2 text-sm {{ $available_locale === $current_locale ? 'bg-slate-100 font-semibold text-slate-900 dark:bg-slate-800 dark:text-white' : 'font-normal text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}"
                >
                    {{ $locale_name }}
                </a>
            @endforeach
    </x-dropdown>
