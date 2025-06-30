    <div
        wire:key="{{ $uuid }}"
        {{ $attributes->whereDoesntStartWith('class') }}
        {{ $attributes->class(['alert rounded-md', 'shadow-md' => $shadow])}}
        x-data="{ show: true }" x-show="show"
    >
        @if($icon)
            <x-mary-icon :name="$icon" />
        @endif

        @if($title)
            <div>
                <div class="font-bold">{{ $title }}</div>
                <div class="text-xs">{{ $description }}</div>
            </div>
        @else
            <span>{{ $slot }}</span>
        @endif

        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>

        @if($dismissible)
            <x-mary-button icon="o-x-mark" @click="show = false" class="btn-xs btn-ghost self-start justify-self-end p-0 absolute sm:static -order-1 sm:order-none" />
        @endif
    </div>