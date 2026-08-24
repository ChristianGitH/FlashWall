    @php($current_locale_code = strtoupper($current_locale))
    <x-menu-sub title="{{ $current_locale_code }}" icon="o-language">
            @foreach($available_locales as $locale_name => $available_locale)
                <x-menu-item title="{{ $locale_name }}" link="{{ url('language/' . $available_locale) }}" class="{{ $available_locale === $current_locale ? 'bg-slate-100 font-semibold text-slate-900 dark:bg-slate-800 dark:text-white' : 'font-normal text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}" />
            @endforeach
    </x-menu-sub>