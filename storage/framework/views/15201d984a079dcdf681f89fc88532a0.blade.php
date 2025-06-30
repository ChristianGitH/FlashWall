    <a href="/" wire:navigate>
        <!-- Hidden when collapsed -->
        <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
            <div class="flex items-start gap-2">
                {{-- Icône alignée verticalement avec "Flashwall" --}}
                <x-icon name="o-square-3-stack-3d" class="w-6 text-purple-500 mt-1" />

                {{-- Bloc texte avec Flashwall et beta --}}
                <div class="relative leading-tight">
                    <span class="block font-bold text-3xl bg-gradient-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent">
                        Flashwall
                    </span>
                    <span class="absolute right-0 text-[10px] text-warning mt-0.5">beta</span>
                </div>
            </div>
        </div>

        <!-- Display when collapsed -->
        <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-6 h-[28px]">
            <x-icon name="s-square-3-stack-3d" class="w-6 -mb-1 text-purple-500" />
        </div>
    </a>