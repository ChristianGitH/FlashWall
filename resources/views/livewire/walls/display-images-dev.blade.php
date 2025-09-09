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


    public function mount(Wall $wall)
    {
        $this->wall = $wall;
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
    // We keep in cache the next image data in case it will not be in the next load.
    // If last image, will keep displaying it.
    $nextImage = $this->approvedImages->firstWhere('id', $nextImageId);

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
                ->reject(fn($image) => in_array($image->id, $recentlyDisplayedIds) && $image->priority !== 1)
                ->values();

        }


        // If exist, push nextImageId in first position
        $remaining = $filtered->reject(fn($image) => $image->id === $nextImageId)->values();

        $newCollection = collect();
        // Push nextImage in first place
        if ($nextImage) {
            $newCollection->push($nextImage);
        }

        $this->approvedImages = $newCollection->concat($remaining)->values();
    }

}


protected $listeners = ['imageDisplayed' => 'markImageAsDisplayed',];


}; ?>

<div class="w-screen h-screen">


<div style="z-index: 100; opacity: 0.7;" class="absolute bottom-0 left-0 right-0 bg-white text-center text-gray-600 p-2 text-sm shadow">
    <p>IDs to display ({{ count($approvedImages) }}) : 
    @foreach ($approvedImages as $index => $image)
        <span style="color: {{ $image->permanent ? 'green' : 'blue' }}; background-color: {{ $image->priority == 1 ? 'orange' : 'transparent' }}">
            {{ $image->id }}
        </span>@if (!$loop->last), @endif
    @endforeach
    </p>
    <p>Already displayed IDs : {{ implode(', ', $displayedImageIds) }}</p>
</div>

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
            style="{{ $displaySettings['image_container_style'] }}"
        >
            <div class="relative" style="max-height: 100%; max-width: 100%;">
                <img
                    src="{{ asset('storage/' . $image->name) }}"
                    class="object-contain"
                    style="max-height: 100%; max-width: 100%;"
                />
                
                @if($image->caption)
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
                                {{ $image->caption }}
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
</div>

@endif

</div>