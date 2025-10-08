<?php

use Livewire\Volt\Component;
use App\Models\Wall;

new class extends Component {

    public Wall $wall;
    public array $displaySettings;


    public function mount(Wall $wall)
    {
        $this->wall = $wall;
        $this->displaySettings = $this->computeWallSettings();
    }

public function computeWallSettings(): array
{
    $caption_max_width = $this->wall->caption_max_width;
    $caption_font_size = $this->wall->caption_font_size;
    $duration = $this->wall->duration*1000;
    $caption_font_color = $this->wall->caption_font_color;
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
        position: absolute;
        top: {$marginTop}%;
        bottom: {$marginBottom}%;
        left: {$marginLeft}%;
        right: {$marginRight}%;
        display: flex;
        align-items: center;
        justify-content: center;
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
        $background = 'background:  no-repeat center url(\''. asset('storage/' . $this->wall->background_image) .'\'); background-size: 100% 100%;';
    }

    return [
        'caption_max_width' => $caption_max_width,
        'caption_font_size' => $caption_font_size,
        'background' => $background,
        'duration' => $duration,
        'caption_font_color' => $caption_font_color,
        'caption_background' => $caption_background,
        'image_container_style' => $image_container_style,
        'image_height' => $image_height,
        'image_width'  => $image_width,
    ];
}

}; ?>

<div x-data="{ ready: false }" x-init="ready = true" class="w-screen h-screen">
    <!-- Loading spinner -->
    <div x-show="!ready" class="absolute inset-0 flex items-center justify-center bg-black z-50">
        <x-loading class="loading-ring" />
    </div>

    <!-- Slideshow appears only when ready = true -->
    <div x-show="ready" x-cloak>
        <!-- Slideshow bellow -->
        <livewire:displaywalls.slideshow :wall="$wall" :displaySettings="$displaySettings" />
    </div>

</div>