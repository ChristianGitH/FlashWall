<?php

use Livewire\Component;
use App\Models\Wall;
use App\Models\Image;


new class extends Component {

    public Wall $wall;
    public array $displaySettings;
    public $approvedImages;

    public function mount(Wall $wall, array $displaySettings)
    {
        $this->wall = $wall;
        $this->displaySettings = $displaySettings;
        $this->approvedImages = $this->loadDefaultImages();
    }


    

    /**
     * Load default images from public/slideshow-preview-default-images folder
     */
    private function loadDefaultImages()
    {
        $defaultImagesPath = public_path('storage/slideshow-preview-default-images');
        $defaultImages = collect();
        $default_caption_2 = "Bienvenue sur Flashwall\u{00A0}!";
        $default_caption_3 = "Bonjour depuis les Alpes\u{00A0}❤️";
        $default_caption_4 = "Vive la montagne\u{00A0}!";
        $default_caption_5 = "Ma photo préférée de la collection";
        $default_submitter_name_2 = "Donna";
        $default_submitter_name_3 = "Shelly";
        $default_submitter_name_4 = "Norma";
        $default_submitter_name_5 = "Josie";
        $default_submitter_avatar_2 = "😀";
        $default_submitter_avatar_3 = "😊";
        $default_submitter_avatar_4 = "⛰️";
        $default_submitter_avatar_5 = "🥰";

        if (is_dir($defaultImagesPath)) {
            $files = array_diff(scandir($defaultImagesPath), ['.', '..']);
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            foreach ($files as $index => $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                
                if (in_array($ext, $imageExtensions)) {
                    $image = new \stdClass();
                    $image->id = 'default-' . $index;
                    $image->webp_full_path = 'slideshow-preview-default-images/' . $file;
                    $image->caption = ${"default_caption_$index"};
                    $image->submitter_name = ${"default_submitter_name_$index"};
                    $image->submitter_avatar = ${"default_submitter_avatar_$index"};
                    
                    $defaultImages->push($image);
                }
            }
        }

        return $defaultImages;
    }


}; ?>

@section('title', 'Display ' . $wall->name)

<div>


@if($approvedImages->isEmpty())
    <p class="text-center text-gray-500">{{ __('No image. Reload the page to start the slideshow.') }}</p>
@else

<div
    x-data="{
        currentSlide: 0,
        slides: {{ $approvedImages->count() }},
        lastTime: performance.now(),
        isFullscreen: !!document.fullscreenElement,

        nextFrame(now) {
            const duration = {{ $displaySettings['duration'] }};

            if (!Number.isFinite(this.lastTime)) this.lastTime = now;

            const elapsed = now - this.lastTime;
            if (elapsed >= duration) {
                this.currentSlide = (this.currentSlide + 1) % this.slides;
                this.lastTime = performance.now();
            }

            requestAnimationFrame(this.nextFrame.bind(this));
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
                this.isFullscreen = true;
                document.body.style.cursor = 'none';
            } else {
                document.exitFullscreen();
                this.isFullscreen = false;
                document.body.style.cursor = 'default';
            }
        },

        init() {
            requestAnimationFrame(this.nextFrame.bind(this));
                
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    this.isFullscreen = false;
                    document.body.style.cursor = 'default';
                }
            });
        }
    }"
    wire:key="slideshow-{{ implode(',', $approvedImages->pluck('id')->toArray()) }}"
    class="relative w-full h-screen flex items-center justify-center" style="{{ $displaySettings['background'] }}"
>


    <!-- REFRESH BUTTON -->
    <div class="fixed top-2 left-1/2 transform -translate-x-1/2 z-50" x-show="!isFullscreen">
        <x-button 
            @click="window.location.reload()"
            icon="o-arrow-path"
            class="btn btn-s"
            label="{{ __('Refresh') }}"
        />
    </div>

    <!-- FULLSCREEN BUTTON -->
    <div class="fixed top-2 right-2 z-50" x-show="!isFullscreen">
        <x-button 
            @click="toggleFullscreen"
            icon="o-arrows-pointing-out"
            class="btn btn-s"
            label="{{ __('Full screen') }}"
        />
    </div>

@php
    $transition = $displaySettings['transition'] ?? 'fade';

    $transitions = [
        'fade' => [
            'enter' => 'transition linear duration-700',
            'enterStart' => 'opacity-0',
            'enterEnd' => 'opacity-100',
            'leave' => 'transition linear duration-700',
            'leaveStart' => 'opacity-100',
            'leaveEnd' => 'opacity-0',
        ],
        'zoom' => [
            'enter' => 'transition ease-out duration-100',
            'enterStart' => 'transform opacity-0 scale-75',
            'enterEnd' => 'transform opacity-100 scale-100',
            'leave' => 'transition ease-in duration-75',
            'leaveStart' => 'transform opacity-100 scale-100',
            'leaveEnd' => 'transform opacity-0 scale-75',
        ],
        'none' => [
            'enter' => 'transition duration-[1ms]',
            'enterStart' => 'opacity-0',
            'enterEnd' => 'opacity-100',
            'leave' => 'transition duration-[1ms]',
            'leaveStart' => 'opacity-100',
            'leaveEnd' => 'opacity-0',
        ],
    ];

    $t = $transitions[$transition] ?? $transitions['fade'];
@endphp

    @foreach($approvedImages as $index => $image)
    <div
        x-show="currentSlide === {{ $index }}"
        x-transition:enter="{{ $t['enter'] }}"
        x-transition:enter-start="{{ $t['enterStart'] }}"
        x-transition:enter-end="{{ $t['enterEnd'] }}"
        x-transition:leave="{{ $t['leave'] }}"
        x-transition:leave-start="{{ $t['leaveStart'] }}"
        x-transition:leave-end="{{ $t['leaveEnd'] }}"
        class="absolute inset-0 flex items-center justify-center text-center"
        wire:key="image-{{ $image->id }}"
        style="{{ $displaySettings['image_container_style'] }}"
    >
            <div class="relative flex items-center justify-center h-full w-full" style="max-height: 100%; max-width: 100%;">
                <img
                    src="{{ asset('storage/' . $image->webp_full_path) }}"
                    class="object-contain"
                    style="max-height: 100%; max-width: 100%;"
                />
            
                <!-- CAPTION WRAPPER - and avatar and name -->
                <!-- We check for name, email and avatar if they're enabled and not empty.  -->
                @if(($image->caption && $wall->caption_on_wall) || ($image->submitter_name && $wall->submitter_name_on_wall) || ($image->submitter_avatar && $wall->require_avatar_submitter))

                    @if($displaySettings['layout'] === 0)
                    
                        <!-- Closes the image wrapper div for caption position bellow -->
                        <!-- or on the side of image. -->
                        </div>

                        <div x-data="{ showCaption: false, showCaptionContent: false }"
                            x-init="
                            const duration = {{ $displaySettings['duration'] }};
                            showCaptionDelay = duration/2;
                            showCaptionContentDelay = showCaptionDelay+750
                            if ({{ $index }} === 0) {
                                setTimeout(() => showCaption = true, showCaptionDelay);
                                setTimeout(() => showCaptionContent = true, showCaptionContentDelay);
                            }
                            $watch('currentSlide', value => {
                                if (value === {{ $index }}) {
                                    showCaption = false;
                                    showCaptionContent = false;
                                    setTimeout(() => showCaption = true, showCaptionDelay);
                                    setTimeout(() => showCaptionContent = true, showCaptionContentDelay);
                                }
                            })"
                        class="text-center overflow-hidden transition-all duration-1000"
                        :class="{ 'w-0': !showCaption, 'w-full': showCaption }">
                            <span class="leading-none p-[0.5em] font-semibold rounded-md transition-all duration-700"
                            :class="{ 'opacity-0': !showCaptionContent, 'opacity-100': showCaptionContent }"
                            style="background-color: {{ $displaySettings['caption_background'] }};
                            max-width: {{ $displaySettings['caption_max_width'] }}%;
                            display: inline-block;
                            font-size: {{ $displaySettings['submitter_name_font_size'] . $displaySettings['caption_font_unit'] }};">

                            <div class="flex justify-center items-center gap-2">
                                @if($wall->require_avatar_submitter && $image->submitter_avatar)
                                <span class="emoji_font bg-white rounded-full inline-flex items-center justify-center"
                                    style="font-size: 1em; width: 1.6em; height: 1.6em;">{{ $image->submitter_avatar }}</span>
                                @endif
                                @if($wall->submitter_name_on_wall && $image->submitter_name)
                                    <span class="ml-3 uppercase" style="font-size: {{ $displaySettings['submitter_name_font_size'] . $displaySettings['caption_font_unit'] }};
                                    color: {{ $displaySettings['submitter_name_font_color'] }};
                                    ">
                                        {{ $image->submitter_name }}
                                    </span>
                                @endif

                            </div>

                            @if($wall->caption_on_wall && $image->caption)
                                <div class="p-[0.5em]" style="
                                color: {{ $displaySettings['caption_font_color'] }};
                                font-size: {{ $displaySettings['caption_font_size'] . $displaySettings['caption_font_unit'] }};">
                                    {{ $image->caption }}
                                </div>
                            @endif
                            
                        </div>

                    @elseif($displaySettings['layout'] === 1)
                        <!-- Caption below image -->
                        </div>

                        <div class="absolute text-center w-full"
                        style="bottom : {{ $displaySettings['caption_bellow_image_bottom_margin'] }};">
                            <span class="leading-none p-[0.5em] font-semibold rounded-md "
                            style="font-size: {{ $displaySettings['caption_font_size'] }}px;
                            color: {{ $displaySettings['caption_font_color'] }};
                            background-color: {{ $displaySettings['caption_background'] }};
                            max-width: {{ $displaySettings['caption_max_width'] }}%;
                            display: inline-block;">

                                <div class="flex justify-center items-center gap-2">
                                    @if($wall->require_avatar_submitter && $image->submitter_avatar)
                                    <span
                                        class="emoji_font bg-base-300 rounded-full inline-flex items-center justify-center"
                                        style="
                                            font-size: {{ $displaySettings['caption_font_size'] }}px;
                                            width: {{ $displaySettings['caption_font_size'] * 2 }}px;
                                            height: {{ $displaySettings['caption_font_size'] * 2 }}px;
                                        "
                                    >
                                        {{ $image->submitter_avatar }}
                                    </span>
                                    @endif

                                    @if($wall->submitter_name_on_wall && $image->submitter_name)
                                        <span>{{ $image->submitter_name }}</span>
                                    @endif

                                    @if($wall->caption_on_wall && $image->caption && $wall->submitter_name_on_wall && $image->submitter_name)
                                        :
                                    @endif
                                </div>

                                @if($wall->caption_on_wall && $image->caption)
                                    <div class="p-[0.5em]">{{ $image->caption }}</div>
                                @endif
                            </span>
                        </div>
                    @elseif($displaySettings['layout'] === 2)
                        <!-- Caption on image -->
                        <div class="absolute bottom-[20px] text-center w-full">
                            <span class="leading-none p-[0.5em] font-semibold rounded-md"
                            style="font-size: {{ $displaySettings['caption_font_size'] }}px;
                            color: {{ $displaySettings['caption_font_color'] }};
                            background-color: {{ $displaySettings['caption_background'] }};
                            max-width: {{ $displaySettings['caption_max_width'] }}%;
                            display: inline-block;">

                                <div class="flex justify-center items-center gap-2">
                                    @if($wall->require_avatar_submitter && $image->submitter_avatar)
                                    <span
                                        class="emoji_font bg-base-300 rounded-full inline-flex items-center justify-center"
                                        style="
                                            font-size: {{ $displaySettings['caption_font_size'] }}px;
                                            width: {{ $displaySettings['caption_font_size'] * 2 }}px;
                                            height: {{ $displaySettings['caption_font_size'] * 2 }}px;
                                        "
                                    >
                                        {{ $image->submitter_avatar }}
                                    </span>
                                    @endif

                                    @if($wall->submitter_name_on_wall && $image->submitter_name)
                                        <span>{{ $image->submitter_name }}</span>
                                    @endif

                                    @if($wall->caption_on_wall && $image->caption && $wall->submitter_name_on_wall && $image->submitter_name)
                                        :
                                    @endif
                                </div>

                                @if($wall->caption_on_wall && $image->caption)
                                    <div class="p-[0.5em]">{{ $image->caption }}</div>
                                @endif
                            </span>
                        </div>
                        </div>
                    @endif
                @else
                    <!-- If no caption, name or avatar, we just close the image wrapper div -->
                    </div>
                @endif
        </div>
    @endforeach

</div>

@endif

</div>