<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;


new class extends Component {

    public Wall $wall;
    public array $displaySettings;
    public $approvedImages = [];
    public $displayedImageIds = [];
    public int $countImageDisplay = 0;
    public int $countImageDisplayBeforeRefresh = 0;

    /* For dev and testing */
    public $lastDBCheckAt = '';
    public $refreshEvery = '';

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
        ->where('status', '!=', 5)->where('permanent', 1)->orderBy('updated_at', 'asc')->get();
        // For priority : ->orderBy('priority', 'desc') For exemple priority = 1 : push forward
}



/**
 * Called when an image has finished displaying.
 */
public function markImageAsDisplayed($imageId, $nextImageId)
{
    $this->countImageDisplay++;
    
    // We keep the next image data in case it will not be in the next load.
    // If last image, will keep displaying it.
    $nextImage = $this->approvedImages->firstWhere('id', $nextImageId);

    // We add the image id in displayedImageIds, unique id and at the end of array 
        $this->displayedImageIds = [...array_filter($this->displayedImageIds, fn($id) => $id !== $imageId), $imageId];
        // If priority = 1, we change it back to 0
        Image::where('id', $imageId)->where('priority', 1)->update(['priority' => 0]);


    // We check is there's a propelled image
    if ($this->checkForPropelledImage($nextImage)) {
        return;
    }


    // Refresh the image list every totalImages ÷ 4 images displayed, but never more frequently than every 2 images.
    $totalImages = $this->approvedImages->count();
    $refreshEvery = max(2, (int) floor($totalImages / 4));

    // For dev front
    $this->refreshEvery = max(2, (int) floor($totalImages / 4));

    if ($this->countImageDisplay % $refreshEvery == 0 || $this->countImageDisplay >= $totalImages) {
            // For dev front
            $this->lastDBCheckAt = now()->format('H:i:s'); // stocke l'heure pour l'affichage front
        
        $this->checkForChangesInDatabase($nextImage);
    }

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







    public function checkForPropelledImage($nextImage): bool
    {
        $propelled = $this->wall->images()
            ->where('priority', 1)
            ->where('status', 1)
            ->first();

        // We check is the propelled image is not allready the nextImage
        if ($propelled && (!$nextImage || $propelled->id !== $nextImage->id)) {
            $newCollection = collect();

            // Push nextImage
            if ($nextImage) {
                $newCollection->push($nextImage);
            }
            // Push propelled behind nextImage
            $newCollection->push($propelled);

            $remaining = $this->approvedImages
                ->reject(fn($img) => in_array($img->id, [$nextImage?->id, $propelled->id]))
                ->values();

            $this->approvedImages = $newCollection->concat($remaining)->values();
            
            return true;
        }
        return false;
    }


}; ?>

@section('title', 'Display ' . $wall->name)

<div x-data="{
    showDebug: false, // FOR DEV ONLY
    }">

<!-- DEV TOGGLE BUTTON -->
<div class="fixed top-5 left-5" style="z-index: 100;">
    <x-button 
        @click="showDebug = !showDebug"
        class="btn btn-xs"
        x-text="showDebug ? 'Masquer debug' : 'Afficher debug'"
    />
</div>

<!-- For DEV and testing -->
<div x-show="showDebug" style="z-index: 100; opacity: 0.7;" class="absolute bottom-0 left-0 right-0 bg-white text-center text-gray-600 p-2 text-sm shadow">
    <p>Displayed : {{ $countImageDisplay }}. IDs to display ({{ count($approvedImages) }}) : 
    @foreach ($approvedImages as $index => $image)
        <span style="color: {{ $image->permanent ? 'green' : 'blue' }}; background-color: {{ $image->priority == 1 ? 'orange' : 'transparent' }}">
            {{ $image->id }}
        </span>@if (!$loop->last), @endif
    @endforeach
    </p>
    <p>Already displayed IDs : {{ implode(', ', $displayedImageIds) }}</p>
    <p>Refresh every : {{ $refreshEvery }}. Last DB check: {{ $lastDBCheckAt ?? 'Never' }}</p>
</div>
<!-- For dev and testing -->

@if($approvedImages->isEmpty())
    <p class="text-center text-gray-500">{{ __('No image. Reload the page to start the slideshow.') }}</p>
@else

<div
    x-data="{
        currentSlide: 0,
        slides: {{ $approvedImages->count() }},
        isUpdating: false,
        lastTime: performance.now(),
        isFullscreen: false,
        wakeLock: null,

        // WAKE LOCK : Keep screen active !
        async requestWakeLock() {
            try {
                if ('wakeLock' in navigator) {
                    if (this.wakeLock) return; // évite les doublons

                    this.wakeLock = await navigator.wakeLock.request('screen');
                    console.log('✅ Wake Lock activé');

                    this.wakeLock.addEventListener('release', async () => {
                        console.log('⚠️ Wake Lock relâché, tentative de réactivation...');
                        try {
                            this.wakeLock = await navigator.wakeLock.request('screen');
                            console.log('🔁 Wake Lock réactivé');
                        } catch (e) {
                            console.warn('Impossible de relancer le Wake Lock:', e);
                        }
                    });
                } else {
                    console.warn('Wake Lock API non supportée sur ce navigateur.');
                }
            } catch (err) {
                console.error('Erreur activation Wake Lock:', err);
            }
        },

        async nextFrame(now) {
            const duration = {{ $displaySettings['duration'] }};
            if (!this.lastTime) this.lastTime = now;

            if (now - this.lastTime >= duration) {
                if (!this.isUpdating) {
                    this.isUpdating = true;

                    // On récupère l'ID de l'image affichée
                    let imageId = parseInt(this.$refs['image-' + this.currentSlide]?.dataset?.imageId);
                    let nextIndex  = (this.currentSlide + 1) % this.slides;
                    let nextImageId = parseInt(this.$refs['image-' + nextIndex]?.dataset?.imageId);

                    if (imageId) {
                        try {
                            await $wire.markImageAsDisplayed(imageId, nextImageId);
                        } catch (e) {
                            console.error('Erreur markImageAsDisplayed:', e);
                        }
                    }

                    // On passe à la diapo suivante
                    this.currentSlide = nextIndex;
                    this.isUpdating = false;
                }

                this.lastTime = now;
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
            this.requestWakeLock();

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    this.requestWakeLock(); // Réactive dès que la page devient visible
                }
            });

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
    <div class="fixed top-2 right-2" x-show="!isFullscreen">
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
            </div>

                <!-- CAPTION WRAPPER - and avatar and name -->
                <!-- We check for name, email and avatar if they're enabled and not empty.  -->
                @if(($image->caption && $wall->caption_on_wall) || ($image->submitter_name && $wall->submitter_name_on_wall) || ($image->submitter_avatar && $wall->require_avatar_submitter))


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
                @endif
        </div>
    @endforeach

        <!-- Debug panel -->
    <div x-show="showDebug" style="z-index: 150;" class="fixed bottom-2 right-2 bg-white text-xs text-gray-700 shadow p-2 rounded max-w-xs z-50">
        <p><strong>currentSlide:</strong> <span x-text="currentSlide"></span></p>
        <p><strong>slides:</strong> <span x-text="JSON.stringify(slides)"></span></p>
    </div>
</div>

@endif

</div>