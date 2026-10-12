<?php

use Livewire\Component;
use App\Models\Wall;
use App\Models\Image;


new class extends Component {

    public Wall $wall;
    public array $displaySettings;
    public $approvedImages = [];
    public $displayedImageIds = [];
    public int $countImageDisplay = 0;
    public int $countImageDisplayBeforeRefresh = 0;


    public function mount(Wall $wall, array $displaySettings)
    {
        $this->wall = $wall;
        $this->approvedImages = $this->loadApprovedImages();
        $this->displaySettings = $displaySettings;
    }


/**
 * Get approved images from database 
 */
public function loadApprovedImages()
{
    $query = $this->wall->images();

    if ($this->wall->moderation) {
        $query->where('status', 1);  // 0 = unprocessed. 1 = approved. 2 = archived.
    }

    return $query
        ->where('status', '!=', 5)->orderBy('updated_at', 'asc')->get();
}



/**
 * Called when an image has finished displaying.
 */
public function markImageAsDisplayed($imageId, $nextImageId)
{
    // Si un seul ID est passé, le transformer en tableau
    if (!is_array($imageId)) {
        $imageId = [$imageId];
    }

    // We push each id
    foreach ($imageId as $imageId) {
        // We remove the ID from the array and put it back at the end
        $this->displayedImageIds = [...array_filter($this->displayedImageIds, fn($id) => $id !== $imageId), $imageId];
    }
    // We keep the next image data in case it will not be in the next load.
    // If last image, will keep displaying it.
    $nextImage = $this->approvedImages->firstWhere('id', $nextImageId);
    $this->lastDBCheckAt = now()->format('H:i:s'); // stocke l'heure pour l'affichage front

    $this->checkForChangesInDatabase($nextImage);

}


    private function checkForChangesInDatabase($nextImage)
    {
        // We get the updated approved Images from database
        $currentImagesFromDB = $this->loadApprovedImages();
        $currentIdsFromDB = $currentImagesFromDB->pluck('id');
        $oldIds = $this->approvedImages->pluck('id');
        // We ignore recentlyDisplayedIds when we compare
        $ignoredIds = $this->displayedImageIds ?? [];

        //We compare with the database if there's new images
        $newImages = $currentIdsFromDB->diff($oldIds->merge($ignoredIds));
        // And if there's deleted images
        $removedImages = $oldIds->diff($currentIdsFromDB);


        // If there's somes changes in the DB :
        if ($newImages->isNotEmpty() || $removedImages->isNotEmpty()) {

            // If there's some deleted image we take them out of the array displayedImageIds
            if ($removedImages->isNotEmpty()) {
                $this->displayedImageIds = array_values(
                    array_filter($this->displayedImageIds, fn($id) => !in_array($id, $removedImages->toArray()))
                );
            }

            // We limit the number of new image to add to slideshow
            $maxNewImagesPerCycle = 20;
            if ($newImages->count() > $maxNewImagesPerCycle) {
                $newImages = $newImages->take($maxNewImagesPerCycle);
            }

            // We filter, new image without nextImage, to avoid having nextImage twice
            $filtered_items = $currentImagesFromDB
                ->whereIn('id', $newImages)
                ->reject(fn($image) => $image->id === $nextImage?->id)
                ->values();

            // If there less than X images, we push back in the allready displayed images.
           if ($filtered_items->count() < 20) {
                $this->updateApprovedImagesWithNextImageAndDisplayedImages($nextImage, $filtered_items, $currentImagesFromDB);
            }
            else {
                // If exist, push nextImageId in first position
                $this->approvedImages = collect($nextImage ? [$nextImage] : [])
                ->concat($filtered_items)
                ->values();
            }
            $this->countImageDisplay = 0;
        }
        /*  AND IF there's no change in the DB but we reached the end of the slideshow :
            then we push the allready displayed images back in the slideshow. */
        elseif ($this->countImageDisplay >= $this->approvedImages->count()) {
            $this->countImageDisplay = 0;

            $recentIds = collect($this->displayedImageIds); // Convert array to collection
            $approvedIds = $this->approvedImages->pluck('id');

            if ($recentIds->diff($approvedIds)->isNotEmpty()) {
                // If array not empty, we remove the images from the list
                $filtered_items = $this->approvedImages
                ->reject(fn($image) => in_array($image->id, $this->displayedImageIds))
                ->reject(fn($image) => $image->id === $nextImage?->id)->values()
                ->values();

                $this->updateApprovedImagesWithNextImageAndDisplayedImages($nextImage, $filtered_items, $currentImagesFromDB);

            }        
        }
    }



    private function updateApprovedImagesWithNextImageAndDisplayedImages($nextImage, $filtered_items, $currentImagesFromDB)
    {
        // Remove nextImage from displayed IDs
        $this->displayedImageIds = array_values(
            array_filter($this->displayedImageIds, fn($id) => $id !== $nextImage?->id)
        );

        // Get recently displayed images content
        $recentlyDisplayed = $currentImagesFromDB
            ->whereIn('id', $this->displayedImageIds)
            ->values();

        // Add nextImage, images from DB and push recentlyDisplayed image and the end
        $this->approvedImages = collect($nextImage ? [$nextImage] : [])
            ->concat($filtered_items)
            ->concat($recentlyDisplayed)
            ->values();
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
        isUpdating: false,
        lastTime: performance.now(),
        isFullscreen: !!document.fullscreenElement,
        displayedCount: 0,
        refreshEvery: {{ max(3, floor($approvedImages->count() / 4)) }},
        displayedIds: [],
        elapsed: 0,

        async nextFrame(now) {
            const duration = {{ $displaySettings['duration'] }};

            if (!Number.isFinite(this.lastTime)) this.lastTime = now;

            if (!this.slides || this.slides <= 0) {
                requestAnimationFrame(this.nextFrame.bind(this));
                return;
            }

            this.elapsed = now - this.lastTime;
            if (this.elapsed >= duration) {
                if (!this.isUpdating) {
                    this.isUpdating = true;
                    try {
                        const currentRef = this.$refs['image-' + this.currentSlide];
                        const imageId = currentRef ? parseInt(currentRef.dataset.imageId, 10) : NaN;
                        const nextIndex = (this.currentSlide + 1) % this.slides;
                        const nextRef = this.$refs['image-' + nextIndex];
                        const nextImageId = nextRef ? parseInt(nextRef.dataset.imageId, 10) : NaN;

                        if (Number.isFinite(imageId)) {
                            this.displayedCount++;

                            this.displayedIds.push(imageId);

                            // Call markImageAsDisplayed only if we reach refreshEvery
                            if (this.displayedCount % this.refreshEvery === 0 || this.displayedCount >= this.slides) {
                                await $wire.markImageAsDisplayed(this.displayedIds, Number.isFinite(nextImageId) ? nextImageId : null);
                                this.displayedIds = [];
                                this.displayedCount = 0;
                            }
                        }

                        this.currentSlide = nextIndex;
                        this.lastTime = performance.now();
                    } catch (e) {
                        console.error('Erreur markImageAsDisplayed:', e);
                    } finally {
                        this.isUpdating = false;
                    }
                }
                
            }

            requestAnimationFrame(this.nextFrame.bind(this));
        },

        // FULLSCREEN BUTTON
        async toggleFullscreen() {
            if (!document.fullscreenElement) {
                await document.documentElement.requestFullscreen();
                this.isFullscreen = true;
                document.body.style.cursor = 'none';
            } else {
                await document.exitFullscreen();
                this.isFullscreen = false;
                document.body.style.cursor = 'default';
            }
        },

        init() {
            requestAnimationFrame(this.nextFrame.bind(this));
                
            document.addEventListener('fullscreenchange', () => {
                // Quand on quitte le fullscreen (ex: touche Échap)
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
        x-ref="image-{{ $index }}"
        data-image-id="{{ $image->id }}"
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
                            showCaptionDelay = duration/3;
                            showCaptionContentDelay = showCaptionDelay+700
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
                            display: inline-block;">

                            <div class="flex justify-center items-center gap-2">
                                @if($wall->require_avatar_submitter && $image->submitter_avatar)
                                <span
                                    class="emoji_font bg-base-300 rounded-full inline-flex items-center justify-center"
                                    style="
                                        font-size: {{ $displaySettings['submitter_name_font_size'] . $displaySettings['caption_font_unit'] }};
                                        width: 2em;
                                        height: 2em;
                                    "
                                >
                                    {{ $image->submitter_avatar }}
                                </span>
                                @endif

                                @if($wall->submitter_name_on_wall && $image->submitter_name)
                                    <span style="font-size: {{ $displaySettings['submitter_name_font_size'] . $displaySettings['caption_font_unit'] }};
                                    color: {{ $displaySettings['submitter_name_font_color'] }};
                                    ">
                                        {{ $image->submitter_name }}
                                    </span>
                                @endif

                            </div>

                                @if($wall->caption_on_wall && $image->caption)
                                    <div class="p-[0.5em]" style="
                                    color: {{ $displaySettings['caption_font_color'] }};
                                    font-size: {{ $displaySettings['caption_font_size'] . $displaySettings['caption_font_unit'] }};">{{ $image->caption }}</div>
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