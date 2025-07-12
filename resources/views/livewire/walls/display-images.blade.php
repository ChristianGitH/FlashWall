<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;


new class extends Component {

    public Wall $wall;


    public function mount(string $slug)
    {
        $this->wall = Wall::where('slug', $slug)->firstOrFail();
    }

public function wallSettingsMount(): array
{
    $image_max_height = 100 - $this->wall->horizontal_borders_width * 2;
    $image_max_width = 100 - $this->wall->vertical_borders_width * 2;
    $caption_max_width = $this->wall->caption_max_width;
    $caption_font_size = $this->wall->caption_font_size;
    $duration = $this->wall->duration*1000;
    $caption_font_color = $this->wall->caption_font_color;
    $caption_background_color = $this->wall->caption_background_color;
    $caption_background_opacity = $this->wall->caption_background_opacity;

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
        'image_max_height' => $image_max_height,
        'image_max_width' => $image_max_width,
        'caption_max_width' => $caption_max_width,
        'caption_font_size' => $caption_font_size,
        'background' => $background,
        'duration' => $duration,
        'caption_font_color' => $caption_font_color,
        'caption_background' => $caption_background,
    ];
}

public function approvedImages()
{
    if ($this->wall->moderation) {
        return $this->wall->images()
                    ->where('approved', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
    } else {
        return $this->wall->images()
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}

}; ?>

<div class="w-screen h-screen">

@php
    $approvedImages = $this->approvedImages();
    $displaySettings = $this->wallSettingsMount();
@endphp

@if($approvedImages->isEmpty())
    <p class="text-center text-gray-500">{{ __('No image.') }}</p>
@else

<div
    x-data="{
        currentSlide: 0,
        slides: {{ $approvedImages->count() }},
        init() {
            setInterval(() => {
                this.currentSlide = (this.currentSlide + 1) % this.slides;
            }, {{ $displaySettings['duration'] }});
        }
    }"
    class="relative w-full h-screen flex items-center justify-center" style="{{ $displaySettings['background'] }}"
>
    @foreach($approvedImages as $index => $image)
        <div
            x-show="currentSlide === {{ $index }}"
            class="absolute inset-0 flex items-center flex-col justify-center text-center"
            wire:key="image-{{ $image->id }}"
        >
            <div class="relative" style="max-height: {{ $displaySettings['image_max_height'] }}%; max-width: {{ $displaySettings['image_max_width'] }}%;">
                <img
                    src="{{ asset('storage/' . $image->name) }}"
                    class="object-contain w-full h-full"
                    style="max-height: 100%; max-width: 100%;"
                />
                <!-- CAPTION POSITION = If 1, caption is on the image. If 0, caption is bellow the image. -->
                @if($image->caption && $this->wall->caption_position == 1)
                    <div class="absolute bottom-[20px] text-center w-full">
                        <span class="leading-none p-[0.5em] font-semibold rounded-md "
                        style="font-size: {{ $displaySettings['caption_font_size'] }}px;
                        color: {{ $displaySettings['caption_font_color'] }};
                        background-color: {{ $displaySettings['caption_background'] }};
                        max-width: {{ $displaySettings['caption_max_width'] }}%;
                        display: inline-block;">
                            {{ $image->caption }}
                        </span>
                    </div>
                @endif
            </div>

                <!-- CAPTION POSITION = If 1, caption is on the image. If 0, caption is bellow the image. -->
                @if($image->caption && $this->wall->caption_position == 0)
                    <div class="absolute bottom-[20px] text-center w-full">
                        <span class="leading-none p-[0.5em] font-semibold rounded-md "
                        style="font-size: {{ $displaySettings['caption_font_size'] }}px;
                        color: {{ $displaySettings['caption_font_color'] }};
                        background-color: {{ $displaySettings['caption_background'] }};
                        max-width: {{ $displaySettings['caption_max_width'] }}%;
                        display: inline-block;">
                            {{ $image->caption }}
                        </span>
                    </div>
                @endif
        </div>
    @endforeach
</div>

@endif

</div>