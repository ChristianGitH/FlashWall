    <a href="/" wire:navigate>
        <!-- Hidden when collapsed -->
        <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
            <div class="flex items-center gap-2">
                <x-icon name="o-square-3-stack-3d" class="w-6 -mb-1 text-purple-500" />

    <div class="flex flex-col leading-none">
        <span class="text-[9px] text-warning uppercase tracking-wider mb-0.5">Beta</span>
        <span class="font-bold text-3xl bg-gradient-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
            Flashwall
        </span>
    </div>
            </div>
        </div>

        <!-- Display when collapsed -->
        <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-6 h-[28px]">
            <x-icon name="s-square-3-stack-3d" class="w-6 -mb-1 text-purple-500" />
        </div>
    </a>