    <div {{ $attributes->class(["bg-base-100 border-base-300 border-b", "sticky top-0 z-10" => $sticky]) }}>
        <div @class(["flex items-center px-6 py-5",  "max-w-screen-2xl mx-auto" => !$fullWidth])>
            <div {{ $brand?->attributes->class(["flex-1 flex items-center"]) }}>
                {{ $brand }}
            </div>
            <div {{ $actions?->attributes->class(["flex items-center gap-4"]) }}>
                {{ $actions }}
            </div>
        </div>
    </div>