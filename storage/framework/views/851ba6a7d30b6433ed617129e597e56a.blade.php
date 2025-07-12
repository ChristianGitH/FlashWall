    <li {{ $attributes->class(["menu-title"]) }}>
        <div class="flex items-center gap-2">

            @if($icon)
                <x-mary-icon :name="$icon"  />
            @endif

            {{ $title }}
        </div>
    </li>