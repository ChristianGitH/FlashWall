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
    $duration = $this->wall->duration*1000;
    
    // CAPTION POSITION = If 1, caption is on the image. If 0, caption is bellow the image.
    if ($this->wall->caption_position == 1) {    
        $caption_margin_bottom = (100 - $image_max_height) / 2 + 1;
    } else {
        $caption_margin_bottom = 0;
    }
    // BACKGROUND = If background_choice=0 then we set the color. If background_choice=1 we set the url.
    if ($this->wall->background_choice == 0) {    
        $background = 'background: ' . $this->wall->background_color . ';';
    } else {
        $background = 'background:  no-repeat center url(\''. asset('storage/' . $this->wall->background_image) .'\'); background-size: 100% 100%;';
    }

    return [
        'image_max_height' => $image_max_height,
        'image_max_width' => $image_max_width,
        'caption_margin_bottom' => $caption_margin_bottom,
        'caption_max_width' => $caption_max_width,
        'background' => $background,
        'duration' => $duration,
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
            class="absolute inset-0 flex items-center justify-center text-center"
            wire:key="image-{{ $image->id }}"
            style="margin: 1% 1%;"
        >
            <img
                src="{{ asset('storage/' . $image->name) }}"
                class="object-contain" style="max-height: {{ $displaySettings['image_max_height'] }}%; max-width: {{ $displaySettings['image_max_width'] }}%;"
            />
            @if($image->caption)
                <span class="absolute p-[3px] bg-white/70 font-semibold rounded-md" style="bottom: {{ $displaySettings['caption_margin_bottom'] }}%; max-width: {{ $displaySettings['caption_max_width'] }}%;">{{ $image->caption }}</span>
            @else
            @endif
        </div>
    @endforeach
</div>

@endif

</div>