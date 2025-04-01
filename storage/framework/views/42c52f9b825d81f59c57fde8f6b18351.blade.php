    <hr class="my-3"/>

    @if($title)
        <li {{ $attributes->class(["menu-title text-inherit uppercase"]) }}>
            <div class="flex items-center gap-2">

                @if($icon)
                    <x-mary-icon :name="$icon"  />
                @endif

                {{ $title }}
            </div>
        </li>
    @endif