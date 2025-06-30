    <a href="/" wire:navigate>
        <!-- Hidden when collapsed -->
        <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
<div class="flex items-center gap-2">
    <x-icon name="o-square-3-stack-3d" class="w-6 text-purple-500" />

    <div class="relative leading-tight" style="min-width: max-content;">
        <div class="font-bold text-3xl bg-gradient-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
            Flashwall
        </div>
        <div class="absolute top-3 text-[10px] text-warning mt-0.5">
            beta
        </div>
    </div>
</div>
        </div>

        <!-- Display when collapsed -->
        <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-6 h-[28px]">
            <x-icon name="s-square-3-stack-3d" class="w-6 -mb-1 text-purple-500" />
        </div>
    </a>