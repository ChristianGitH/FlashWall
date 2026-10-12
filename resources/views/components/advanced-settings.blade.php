@props([
    'hasAdvancedSettings' => auth()->user()->hasFeature('advanced_settings'),
])

<x-collapse separator>
    <x-slot:heading>
        {{ __('Advanced settings') }}
    </x-slot:heading>

    <x-slot:content>
        @if(!$hasAdvancedSettings)
            <x-alert
                title="{{ __('Advanced settings') }}"
                description="{{ __('These settings are not available with your current subscription.') }}"
                icon="o-lock-closed"
                class="mb-6 alert-info flex flex-wrap md:max-w-[25vw] justify-center"
            >
                <x-slot:actions>
                    <x-button
                        label="{{ __('Upgrade your plan !') }}"
                        class="btn-sm"
                        link="{{ route('plans') }}"
                    />
                </x-slot:actions>
            </x-alert>
        @endif

        {{ $slot }}
    </x-slot:content>
</x-collapse>
