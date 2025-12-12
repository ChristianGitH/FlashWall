<?php

use Livewire\Volt\Component;
use App\Models\Wall;

new class extends Component {

    public Wall $wall;
    public string $mode = 'prod';
    public array $displaySettings;


    public function mount(Wall $wall, string $mode = 'prod')
    {
        $this->wall = $wall;
        $this->mode = $mode;
        $this->displaySettings = $this->computeWallSettings();
    }

public function computeWallSettings(): array
{
    $caption_max_width = $this->wall->caption_max_width;
    $caption_font_unit = $this->wall->caption_font_unit;
    $caption_font_size = $this->wall->caption_font_size;
    $submitter_name_font_size = $this->wall->submitter_name_font_size;
    $duration = $this->wall->duration*1000;
    $transition = $this->wall->transition;
    $caption_font_color = $this->wall->caption_font_color;
    $submitter_name_font_color = $this->wall->submitter_name_font_color;
    $caption_background_color = $this->wall->caption_background_color;
    $caption_background_opacity = $this->wall->caption_background_opacity;

    // Marges en pourcentage (à stocker en BDD comme int ou float)
    $marginTop = $this->wall->margin_top;
    $marginBottom = $this->wall->margin_bottom;
    $marginLeft = $this->wall->margin_left;
    $marginRight = $this->wall->margin_right;

    // Hauteur/largeur disponibles après marges
    $image_height = 100 - $marginTop - $marginBottom;
    $image_width  = 100 - $marginLeft - $marginRight;

    $image_container_style = "
        top: {$marginTop}%;
        bottom: {$marginBottom}%;
        left: {$marginLeft}%;
        right: {$marginRight}%;
    ";

    // BACKGROUND COLOR AND OPACITY. Change the pourcentage of opacity from 0-100 to 0-255
    $caption_background_opacity = (int) round(($caption_background_opacity / 100) * 255);
    // Change from hexadecimal 2 caracters (ex: 0 => "00", 255 => "FF")
    $caption_background_opacity = strtoupper(str_pad(dechex($caption_background_opacity), 2, '0', STR_PAD_LEFT));   
    // Ad opcity after hex string.
    $caption_background = $caption_background_color . $caption_background_opacity;

    // BACKGROUND = If background_choice=0 then we set the color. If background_choice=1 we set the url.
    if ($this->wall->background_choice == 0) {    
        $background = 'background: ' . $this->wall->background_color . ';';
    } else {
        $background = 'background:  no-repeat center url(\''. asset('storage/walls_images/background_images/' . $this->wall->background_image) .'\'); background-size: 100% 100%;';
    }

    return [
        'caption_max_width' => $caption_max_width,
        'caption_font_unit' => $caption_font_unit,
        'caption_font_size' => $caption_font_size,
        'submitter_name_font_size' => $submitter_name_font_size,
        'background' => $background,
        'duration' => $duration,
        'transition' => $transition,
        'caption_font_color' => $caption_font_color,
        'submitter_name_font_color' => $submitter_name_font_color,
        'caption_background' => $caption_background,
        'image_container_style' => $image_container_style,
        'image_height' => $image_height,
        'image_width'  => $image_width,
    ];
}

}; ?>

@push('head')
<script>
(function(){
    let wakeLock = null;

    async function requestWakeLock(){
        try {
            if (!('wakeLock' in navigator)) return;
            if (document.visibilityState !== 'visible') return; // avoid DOMException
            wakeLock = await navigator.wakeLock.request('screen');
            wakeLock.addEventListener('release', () => { wakeLock = null; console.log('Wake Lock released'); });
            console.log('Wake Lock acquired');
        } catch (err) {
            // DOMException: The requesting document is hidden.
            console.warn('Unable to acquire Wake Lock:', err);
            wakeLock = null;
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            requestWakeLock();
        } else {
            // when hidden, wake lock is released automatically
            wakeLock = null;
        }
    });

    // Try once on load; many browsers require a user gesture — also attempt on first interaction
    window.addEventListener('load', () => requestWakeLock());
    ['click', 'keydown', 'touchstart'].forEach(ev => {
        const handler = () => { requestWakeLock(); window.removeEventListener(ev, handler); };
        window.addEventListener(ev, handler, { once: true });
    });
})();
</script>
@endpush

<div x-data="{ ready: false }" x-init="ready = true" class="w-screen h-screen">
    <!-- Loading spinner -->
    <div x-show="!ready" class="absolute inset-0 flex items-center justify-center z-50">
        <x-loading class="loading-ring" />
    </div>

    <!-- Slideshow appears only when ready = true -->
    <div x-show="ready" x-cloak>
        <!-- Slideshow bellow -->
        @if($mode === 'dev')
            <livewire:displaywalls.slideshow-dev :wall="$wall" :displaySettings="$displaySettings" />
        @elseif($mode === 'slow')
            <livewire:displaywalls.slideshow-slow :wall="$wall" :displaySettings="$displaySettings" />
        @elseif($mode === 'oldcaption')
            <livewire:displaywalls.slideshow-stable-classic-caption :wall="$wall" :displaySettings="$displaySettings" />
        @else
            <livewire:displaywalls.slideshow :wall="$wall" :displaySettings="$displaySettings" />
        @endif
    </div>

</div>