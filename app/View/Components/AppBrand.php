<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="/" wire:navigate>
                    <!-- Hidden when collapsed -->
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="flex items-center gap-2">
                            <x-icon name="o-square-3-stack-3d" class="w-6 -mb-1 text-purple-500 -mt-3" />
                            <div class="flex items-end flex-col">
                                <span class="font-bold text-3xl me-3 bg-gradient-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent ">
                                    Flashwall
                                </span>
                                <span class="text-gray-400 me-3 -mt-3">beta</span>
                            </div>
                        </div>
                    </div>

                    <!-- Display when collapsed -->
                    <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-6 h-[28px]">
                        <x-icon name="s-square-3-stack-3d" class="w-6 -mb-1 text-purple-500" />
                    </div>
                </a>
            HTML;
    }
}
