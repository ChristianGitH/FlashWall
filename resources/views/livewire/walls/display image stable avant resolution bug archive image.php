<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;


new class extends Component {

    public Wall $wall;
    
    public $approvedImages = [];
    public array $displaySettings;
    public $displayedImageIds = [];
    protected $actualImageId = null;
    public int $countImageDisplay = 0;


    public function mount(string $slug)
    {
        $this->wall = Wall::where('slug', $slug)->firstOrFail();
        $this->approvedImages = $this->loadApprovedImages();
        $this->displaySettings = $this->computeWallSettings();
    }

public function loadApprovedImages()
{
    $query = $this->wall->images();

    if ($this->wall->moderation) {
        $query->where('status', 1);  // 0 = unprocessed. 1 = approved. 2 = archived.
    }

    return $query
        ->orderBy('priority', 'desc')->inRandomOrder()->get();
}

public function computeWallSettings(): array
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
        $background = 'background:  no-repeat center url(\''. asset('storage/background_images/' . $this->wall->background_image) .'\'); background-size: 100% 100%;';
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
    $query = $this->wall->images();

    if ($this->wall->moderation) {
        $query->where('status', 1);  // 0 = unprocessed. 1 = approved. 2 = archived.
    }

    return $query
        ->orderBy('priority', 'desc')->inRandomOrder()->get();
        // For priority : ->orderBy('priority', 'desc') For exemple priority = 1 : push forward
}


public function markImageAsDisplayed($imageId, $nextImageId)
{

    $deletedCount = Image::where('id', $imageId)->where('permanent', 0)->delete();

    if ($deletedCount === 0) {
        // Image not deleted so we keep it in displayedImageIds
        $this->displayedImageIds[] = $imageId;
        // If priority = 1, we change it back to 0
        Image::where('id', $imageId)->where('priority', 1)->update(['priority' => 0]);
    } else {
        // Image deleted, so we take it out of approvedImages collection
        $this->approvedImages = $this->approvedImages->reject(
            fn($image) => $image->id == $imageId
        )->values();
    }


    $this->countImageDisplay++;
    if ($this->countImageDisplay==2) {
        $this->countImageDisplay = 0;

        // We get the updated approved Images
        $currentImagesFromDB = $this->approvedImages();

        $filtered = $currentImagesFromDB;

        if (count($currentImagesFromDB) >= 3 && !empty($this->displayedImageIds)) {
            $numberOfImages = max(1, (int) round(count($currentImagesFromDB) / 3));

            // We get the last IDs added to displayedImageIds
            $recentlyDisplayedIds  = array_slice($this->displayedImageIds, -$numberOfImages);
            $this->displayedImageIds = $recentlyDisplayedIds;

            // We filter the images
            $filtered = $currentImagesFromDB
                ->reject(fn($image) => in_array($image->id, $recentlyDisplayedIds))
                ->values();
        }


        // If exist, push nextImageId in first position
        $nextImage = $filtered->firstWhere('id', $nextImageId);
        $remaining = $filtered->reject(fn($image) => $image->id === $nextImageId)->values();

        $newCollection = collect();
        // Push nextImage in first place
        if ($nextImage) {
            $newCollection->push($nextImage);
        }

        // We check if ther is an Image with priority = 1.
        $priorityImage = Image::where('priority', 1)
            ->when($this->wall->moderation, fn($query) => $query->where('status', 1))
            ->first();

        // If founded, we push it juste after nextImage
        if ($priorityImage) {
            $newCollection->push($priorityImage);
        }

        $this->approvedImages = $newCollection->concat($remaining)->values();
    }

}


protected $listeners = ['imageDisplayed' => 'markImageAsDisplayed',];


}; ?>

<div class="w-screen h-screen">


<div style="z-index: 100;" class="absolute bottom-0 left-0 right-0 bg-white text-center text-gray-600 p-2 text-sm shadow">
    IDs à afficher ({{ count($approvedImages) }}) : 
    @foreach ($approvedImages as $index => $image)
        <span style="color: {{ $image->permanent ? 'green' : 'blue' }}">
            {{ $image->id }}
        </span>@if (!$loop->last), @endif
    @endforeach
</div>

@if($approvedImages->isEmpty())
    <p class="text-center text-gray-500">{{ __('No image.') }}</p>
@else

<div
    x-data="{
        currentSlide: 0,
        slides: {{ $approvedImages->count() }},
        displayedIds: [],
        init() {
            setInterval(() => {                
                // On récupère l'ID de l'image affichée
                let imageId = parseInt(this.$refs['image-' + this.currentSlide]?.dataset?.imageId);
                this.currentSlide = (this.currentSlide + 1) % this.slides;
                let nextImageId = parseInt(this.$refs['image-' + this.currentSlide]?.dataset?.imageId);
                if (imageId) {
                    //Livewire.dispatch('imageDisplayed', { imageId: parseInt(imageId) });
                    $wire.markImageAsDisplayed(imageId, nextImageId)
                    this.displayedIds.push(parseInt(imageId));
                }
            }, {{ $displaySettings['duration'] }});
        }
    }"
    wire:key="slideshow-{{ implode(',', $approvedImages->pluck('id')->toArray()) }}"
    class="relative w-full h-screen flex items-center justify-center" style="{{ $displaySettings['background'] }}"
>
    @foreach($approvedImages as $index => $image)
        <div
            x-show="currentSlide === {{ $index }}"
            x-ref="image-{{ $index }}"
            data-image-id="{{ $image->id }}"
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
                            {{ $image->id }}
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

    <div style="z-index: 100;" class="absolute bottom-[45px] left-0 right-0 bg-white text-center text-gray-600 p-2 text-sm shadow">
        <p>
            Id déjà affichées :
            <span x-text="displayedIds.join(', ')"></span>
        </p>
    </div>
</div>

@endif

</div>