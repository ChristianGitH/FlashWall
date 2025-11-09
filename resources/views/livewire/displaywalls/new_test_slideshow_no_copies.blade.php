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


    public function mount(Wall $wall, array $displaySettings)
    {
        $this->wall = $wall;
        $this->approvedImages = $this->loadApprovedImages();
        $this->displaySettings = $displaySettings;
    }


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


public function markImageAsDisplayed($imageId, $nextImageId)
{
    $this->countImageDisplay++;
    
    // We keep in cache the next image data in case it will not be in the next load.
    // If last image, will keep displaying it.
    $nextImage = $this->approvedImages->firstWhere('id', $nextImageId);

    // We keep the image id in displayedImageIds, unique id and at the end of array 
    $this->displayedImageIds = [...array_filter($this->displayedImageIds, fn($id) => $id !== $imageId), $imageId];
    // If priority = 1, we change it back to 0
    Image::where('id', $imageId)->where('priority', 1)->update(['priority' => 0]);


    // We check is there's a propelled image
    if ($this->checkForPropelledImage($nextImage)) {
        return;
    }

    // IF we reached the end of the slideshow :
    if ($this->countImageDisplay >= $this->approvedImages->count()) {

        // Check if there's some updates in the DB
        $hasUpdates = $this->checkForDatabaseUpdate($nextImage);

        if ($hasUpdates) {
            return;
        }
        else { // If there's no update in the DB, we push the allreadyDisplayedIds back in the slideshow
            $recentIds = collect($this->displayedImageIds); // Convert array to collection
            $approvedIds = $this->approvedImages->pluck('id');
            if ($recentIds->diff($approvedIds)->isNotEmpty()) {
                // If array not empty, we remove the images from the list
                $remaining = $this->approvedImages
                    ->reject(fn($image) => in_array($image->id, $this->displayedImageIds))
                    ->values();

                // And we get the images
                
                $currentImagesFromDB = $this->loadApprovedImages();
                $recentlyDisplayed = $currentImagesFromDB
                    ->whereIn('id', $this->displayedImageIds)
                    ->values();

                // And push them at the back of the list again
                $this->approvedImages = $recentlyDisplayed->concat($remaining)->values();
            }
            $this->countImageDisplay = 0;
        }
    }
    // Every 5 images displayed, we compare the slideshow with the database
    elseif ($this->countImageDisplay % 3 === 0) {
        $this->checkForDatabaseUpdate($nextImage);
    }

}


public function checkForDatabaseUpdate($nextImage) {

    // We get the updated approved Images from database
    $currentImagesFromDB = $this->loadApprovedImages();
    $currentIdsFromDB = $currentImagesFromDB->pluck('id');
    $oldIds = $this->approvedImages->pluck('id');
    // We ignore recentlyDisplayedIds when we compare
    $ignoredIds = $this->displayedImageIds ?? [];

    // And if there's deleted images
    $removedImages = $oldIds->diff($currentIdsFromDB);
    //We compare with the database if there's new images
    $newImages = $currentIdsFromDB->diff($oldIds->merge($ignoredIds));


    // No changes : we stop
    if ($removedImages->isEmpty() && $newImages->isEmpty()) {
        return false;
    }

    // If there's some removed images in the DB :
    if ($removedImages->isNotEmpty()) {
        // We remove the image from the collection
        $this->approvedImages = $this->approvedImages
            ->reject(fn($image) => $removedImages->contains($image->id))
            ->values();

        // We delete the removed image from displayedImageIds
        $this->displayedImageIds = array_values(
            array_filter(
                $this->displayedImageIds,
                fn($id) => !$removedImages->contains($id)
            )
        );
    }

    // If there's somes new images in the DB :
    if ($newImages->isNotEmpty()) {

        $filtered = $currentImagesFromDB
                ->reject(fn($image) => in_array($image->id, $this->displayedImageIds) && $image->priority !== 1)
                ->values();


        // If exist, push nextImageId in first position
        // Remove nextImageId from the list
        $remaining = $filtered->reject(fn($image) => $image->id === $nextImage->id)->values();
        $newCollection = collect();
        // Push nextImage in first place
        if ($nextImage) {
            $newCollection->push($nextImage);
        }

        $this->approvedImages = $newCollection->concat($remaining)->values();
    }
    $this->countImageDisplay = 0;
    return true;
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

protected $listeners = ['imageDisplayed' => 'markImageAsDisplayed',];


}; ?>

<div>
<!-- For dev and testing -->
<div style="z-index: 100; opacity: 0.7;" class="absolute bottom-0 left-0 right-0 bg-white text-center text-gray-600 p-2 text-sm shadow">
    <p>Displayed : {{ $countImageDisplay }}. IDs to display ({{ count($approvedImages) }}) : 
    @foreach ($approvedImages as $index => $image)
        <span style="color: {{ $image->permanent ? 'green' : 'blue' }}; background-color: {{ $image->priority == 1 ? 'orange' : 'transparent' }}">
            {{ $image->id }}
        </span>@if (!$loop->last), @endif
    @endforeach
    </p>
    <p>Already displayed IDs : {{ implode(', ', $displayedImageIds) }}</p>
</div>
<!-- For dev and testing -->

@if($approvedImages->isEmpty())
    <p class="text-center text-gray-500">{{ __('No image. Reload the page to start the slideshow.') }}</p>
@else

<div
    x-data="{
        currentSlide: 0,
        slides: {{ $approvedImages->count() }},
        init() {
            setInterval(() => {                
                // On récupère l'ID de l'image affichée
                let imageId = parseInt(this.$refs['image-' + this.currentSlide]?.dataset?.imageId);
                this.currentSlide = (this.currentSlide + 1) % this.slides;
                let nextImageId = parseInt(this.$refs['image-' + this.currentSlide]?.dataset?.imageId);
                if (imageId) {
                    //Livewire.dispatch('imageDisplayed', { imageId: parseInt(imageId) });
                    $wire.markImageAsDisplayed(imageId, nextImageId)
                }
            }, {{ $displaySettings['duration'] }});

            // Écoute de l'événement Livewire pour retirer une slide
            Livewire.on('removeSlide', (data) => {
                this.slides = this.slides.filter(id => id !== data.id);

                // Si l'image supprimée était la currentSlide → avancer
                if (!this.slides.includes(this.slides[this.currentSlide])) {
                    this.currentSlide = 0;
                }
            });
        }
    }"
    wire:key="slideshow-{{ implode(',', $approvedImages->pluck('id')->toArray()) }}"
    class="relative w-full h-screen flex items-center justify-center" style="{{ $displaySettings['background'] }}"
>
    @foreach($approvedImages as $index => $image)
        <div
        x-transition:leave="transition duration-[1ms]"
        x-show="currentSlide === {{ $index }}"
            x-ref="image-{{ $index }}"
            data-image-id="{{ $image->id }}"
            class="absolute inset-0 flex items-center flex-col justify-center text-center"
            wire:key="image-{{ $image->id }}"
            style="{{ $displaySettings['image_container_style'] }}"
        >
            <div class="relative" style="max-height: 100%; max-width: 100%;">
                <img
                    src="{{ asset('storage/' . $image->webp_full_path) }}"
                    class="object-contain"
                    style="max-height: 100%; max-width: 100%;"
                />
                
                @if(($image->caption && $wall->caption_on_wall) || ($image->submitter_name && $wall->submitter_name_on_wall))
                    <!-- CAPTION POSITION = If 1, caption is on the image. If 0, caption is bellow the image. 
                        Bellow the image then outside div class="relative", image wrapper -->
                    @if($this->wall->caption_position == 0)
                        </div>
                    @endif

                        <div class="absolute bottom-[20px] text-center w-full">
                            <span class="leading-none p-[0.5em] font-semibold rounded-md "
                            style="font-size: {{ $displaySettings['caption_font_size'] }}px;
                            color: {{ $displaySettings['caption_font_color'] }};
                            background-color: {{ $displaySettings['caption_background'] }};
                            max-width: {{ $displaySettings['caption_max_width'] }}%;
                            display: inline-block;">
                                @if($wall->submitter_name_on_wall && $image->submitter_name)
                                    {{ $image->submitter_name }}
                                @endif

                                @if($wall->caption_on_wall && $image->caption && $wall->submitter_name_on_wall && $image->submitter_name)
                                    :
                                @endif

                                @if($wall->caption_on_wall && $image->caption)
                                    {{ $image->id }}
                                @endif
                            </span>
                        </div>

                    <!-- CAPTION POSITION = If 1, caption is on the image. If 0, caption is bellow the image.
                        On the image then inside div class="relative", image wrapper -->
                    @if($this->wall->caption_position == 1)
                        </div>
                    @endif
                @else
                    </div>
                @endif
        </div>
    @endforeach

        <!-- Debug panel -->
    <div style="z-index: 100;" class="fixed bottom-2 right-2 bg-white text-xs text-gray-700 shadow p-2 rounded max-w-xs z-50">
        <p><strong>currentSlide:</strong> <span x-text="currentSlide"></span></p>
        <p><strong>slides:</strong> <span x-text="JSON.stringify(slides)"></span></p>
    </div>
</div>

@endif

</div>