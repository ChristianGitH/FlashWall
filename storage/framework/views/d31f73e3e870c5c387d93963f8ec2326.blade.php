    <a href="/" wire:navigate>
        <!-- Hidden when collapsed -->
        <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
<div class="flex items-start content-start gap-2">
    <x-icon name="o-square-3-stack-3d" class="w-6 text-purple-500" />

    <div class="relative leading-tight" style="min-width: max-content;">
        <p class="text-right">
            <span class="font-bold text-3xl bg-gradient-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">Flashwall</span>
            <br />
            <span class="right-4 absolute mb-3 text-gray-500 dark:text-gray-400">beta</span>
        </p>

    </div>
</div>
        </div>

        <!-- Display when collapsed -->
        <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-6 h-[28px]">
            <x-icon name="s-square-3-stack-3d" class="w-6 -mb-1 text-purple-500" />
        </div>
    </a>