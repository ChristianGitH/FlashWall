<form
    {{ $attributes->whereDoesntStartWith('class') }}
    {{ $attributes->class(['grid grid-flow-row auto-rows-min gap-3']) }}
>

    {{ $slot }}

    @if ($actions)
        @if(!$noSeparator)
            <hr class="my-3" />
        @endif

        <div {{ $actions->attributes->class(["flex justify-end gap-3"]) }}>
            {{ $actions}}
        </div>
    @endif
</form>