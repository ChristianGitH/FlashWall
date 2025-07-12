<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Blank extends Component
{
    public string $url;

    public function __construct(

        // Slots
        public mixed $sidebar = null,
        public mixed $content = null,
        public mixed $footer = null,
        public ?bool $fullWidth = false,
        public ?bool $withNav = false,
        public ?string $collapseText = 'Collapse',
        public ?string $collapseIcon = 'o-bars-3-bottom-right',
        public ?bool $collapsible = false,
    ) {
        $this->url = route('mary.toogle-sidebar', absolute: false);
    }

    public function render(): View|Closure|string
    {
        return <<<'HTML'
                 <main>
                        <div {{ $content->attributes->class(["w-full mx-auto"]) }}>
                            <!-- MAIN CONTENT -->
                            {{ $content }}
                        </div>
                </main>
                HTML;
    }
}
?>