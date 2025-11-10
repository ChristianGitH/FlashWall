<?php

// This is a component for walls>moderation.blade.php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;


/* For fillForDev functionality */
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Intervention\Image\Drivers\GD\Driver;

new class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    public Wall $wall;   
    public array $selectedImages = [];
    public int $unprocessedImagesPageCount = 0;


    public function mount(Wall $wall)
    {
        $this->wall = $wall;
    }

    public function getImagesProperty()
    {
        $images = Image::where('wall_id', $this->wall->id)
                    ->where('status', 0) // 0 = unprocessed. 1 = approved. 2 = archived.
                    ->where('permanent', 1)
                    ->orderBy('created_at', 'desc')
                    ->paginate(30, pageName: 'unprocessed-images');

        $this->unprocessedImagesPageCount = $images->count();
        return $images;
    }


    /* For testing and dev only */
    public function fillForDev(): void
    {
        $sourceImage = base_path('public/storage/N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg'); // ton image source

        for ($i = 0; $i < 30; $i++) {
            // Générer un nom unique pour l'image originale
            $ext = pathinfo($sourceImage, PATHINFO_EXTENSION); // jpg
            $filename = 'image_' . $i . '_' . Str::random(8) . '.' . $ext;

            // Copier l'image originale
            Storage::disk('public')->put('walls_images/images_submitters/' . $filename, file_get_contents($sourceImage));

            // Generate file names
            $thumbFilename = 'thumb_' . $i . '_' . Str::random(8) . '.webp';
            $webpFilename = 'webp_' . $i . '_' . Str::random(8) . '.webp';

            // Generate and save WebP version
            $webpImage = InterventionImage::read($sourceImage)->encodeByExtension('webp', 80); // 80 : quality (0 to 100)
            Storage::disk('public')->put('walls_images/webp_images_submitters/' . $webpFilename, $webpImage);

            // Generate and save thumbnail
            $thumb = InterventionImage::read($sourceImage)
                ->scale(width: 500)
                ->encodeByExtension('webp', 80);

            Storage::disk('public')->put('walls_images/thumbs_submitters/' . $thumbFilename, $thumb);

            // Créer l'entrée en base
            Image::create([
                'wall_id' => $this->wall->id,
                'parent_id' => null,
                'name' => $filename,       // original
                'webp_name' => $webpFilename,       // webp
                'thumb' => $thumbFilename, // miniature WebP
                'caption' => 'Image : '. $i,
                'visitor_token' => '1458-afgd',
                'permanent' => true,
            ]);
        }
    }



    // Deletes all images with status = 5.
    public function cleanDeletedImages()
    {
        $images = Image::where('status', 5)
            ->get();

        $deletedCount = 0;

        foreach ($images as $image) {
            // If it's a permanent image, we delete the files and the line from the DB
            if ($image->permanent) {
                if (Storage::disk('public')->exists($image->name)) {
                    Storage::disk('public')->delete($image->name);
                }
                if (Storage::disk('public')->exists($image->thumb)) {
                    Storage::disk('public')->delete($image->thumb);
                }

                $image->delete();
                $deletedCount++;
            }
            // If non permanent image, we only delete the line from the DB
            else {
                $image->delete();
                $deletedCount++;
            }
        }

        // Envoie un message de confirmation côté front
        $this->success(__('Images successfully deleted'));
    }


    // This updates the parent image and creates copies if needed
    private function approveAndCopy(Image $parent, int $copiesToCreate, array &$copies): void
    {
        // Updating image satus
        $parent->status = 1;
        $parent->save();

        if ($parent->permanent && $copiesToCreate > 0) {
            $now = now();
            for ($k = 0; $k < $copiesToCreate; $k++) {
                $copies[] = [
                    'wall_id'    => $parent->wall_id,
                    'parent_id'  => $parent->id,
                    'name'       => $parent->name,
                    'webp_name'       => $parent->webp_name,
                    'thumb'      => $parent->thumb,
                    'caption'    => $parent->caption,
                    'status'     => 1,
                    'permanent'  => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
    }



    /* Approving images */
    // The called function by bellow approveImage and approveSelected functions
    protected function approve(array $imageIds, string $successMessage): void
    {
        // Pagination reset if all images were approved
        if ($this->unprocessedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'unprocessed-images');
        }

        //  Livewire event to approved-images so it gets refreshed
        $this->dispatch('approved-images-updated');
        $this->success(__($successMessage));
    }

    // Single image approve
    public function approveImage(int $id): void
    {
        $parent = Image::find($id);
        if (!$parent) return; 

        // Creating copies for image display functionality
        if ($parent && $parent->permanent) {
            // Get all parent images (permanent = true) which are not approved (status != 1)
            $parentsCount = Image::where('wall_id', $parent->wall_id)
                ->where('permanent', true)
                ->where('status', 1)
                ->count();

            $copiesToCreate = (int) round($parentsCount * 0.2);
            $copies = [];
            $this->approveAndCopy($parent, $copiesToCreate, $copies);

            // Insert in database, instead of create to limit requests
            if (!empty($copies)) {
                Image::insert($copies);
            }
        }

        // Removing image from selection browser side
        $this->dispatch('action-on-unprocessed-image', id: $id);

        $this->approve([$id], 'Image successfully approved');
    }

    // Multiple images approve
    public function approveSelected(array $selectedImages)
    {
        if (empty($selectedImages)) {
            $this->error(__('No item selected'));
            return;
        }

        // Get all selected images
        $images = Image::whereIn('id', $selectedImages)->get();

        // Get all parent images (permanent = true) which are not approved (status != 1)
        $parentsCount = Image::where('wall_id', $this->wall->id)
            ->where('permanent', true)
            ->where('status', 1)
            ->count();

        $copiesToCreate = (int) round($parentsCount * 0.2);
        $copies = [];

        foreach ($images as $parent) {
            $this->approveAndCopy($parent, $copiesToCreate, $copies);
        }

        // Insert in database, instead of create to limit requests
        if (!empty($copies)) {
            Image::insert($copies);
        }

        // Reset selection browser side
        $this->dispatch('reset-selection');

        $this->approve([$selectedImages], 'Images successfully approved');
    }
    

    /* Archiving images */
    // The called function by bellow archiveImage and archiveSelected functions
    protected function archive(array $imageIds, string $successMessage): void
    {
        if (empty($imageIds)) {
            $this->error(__('No item selected'));
            return;
        }

        Image::whereIn('id', $imageIds)->update(['status' => 2]);

        // Pagination reset if all images were archived
        if ($this->unprocessedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'unprocessed-images');
        }

        //  Livewire event to archived-images so it gets refreshed
        $this->dispatch('archived-images-updated');
        $this->success(__($successMessage));
    }

    // Single image archive
    public function archiveImage(int $id): void
    {
        // Delete image from selection browser side
        $this->dispatch('action-on-unprocessed-image', id: $id);
    
        $this->archive([$id], 'Image successfully archived');
    }

    // Multiple images archive
    public function archiveSelected(array $selectedImages)
    {
        // Reset selection browser side
        $this->dispatch('reset-selection');
        $this->archive($selectedImages, 'Images successfully archived');
    }



    /* Deleting images */
    // The called function by bellow deleteImage and deleteSelected functions
    protected function delete(array $imageIds, string $successMessage): void
    {
        if (empty($imageIds)) {
            $this->error(__('No item selected'));
            return;
        }

        // Pagination reset if all images were deleted
        if ($this->unprocessedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'unprocessed-images');
        }

        $this->success(__($successMessage));
    }
    public function deleteImage(int $id): void
    {
        $image = Image::where('id', $id)->first(['id', 'name', 'webp_name', 'thumb']);
    
        if (!$image) {
            $this->error(__('Image not found.'));
            return;
        }

        // Delete files
        Storage::disk('public')->delete([$image->original_full_path, $image->thumb_full_path, $image->webp_full_path]);

        // Delete from database
        $image->delete();

        $this->delete([$id], 'Image successfully deleted');

        // Remove from selection browser side
        $this->dispatch('action-on-unprocessed-image', id: $id);
    }

    public function deleteSelected(array $selectedImages)
    {

        // Retrieve all concerned images
        $images = Image::whereIn('id', $selectedImages)->get(['id', 'name', 'webp_name', 'thumb']);

        if ($images->isEmpty()) {
            $this->error(__('No valid images found.'));
            return;
        }

        // Build all file paths using accessors
        $paths = $images->flatMap(fn($image) => [
            $image->original_full_path,
            $image->webp_full_path,
            $image->thumb_full_path,
        ])->toArray();
    
        // Delete files
        Storage::disk('public')->delete($paths);
    
        // Delete from database
        Image::whereIn('id', $selectedImages)->delete();

        $this->delete($selectedImages, 'Images successfully deleted');

        // Reset selection browser side
        $this->dispatch('reset-selection');
    }


    // Deleting caption of an image //
    public function deleteCaption(int $id): void
    {
        $image = Image::find($id);
        if (!$image) {
            $this->error(__('Image not found'));
            return;
        }

        // Removing caption of the image
        $image->update(['caption' => null]);

        $this->success(__('Caption successfully deleted'));
    }
        
}; ?>

<div x-data="{ selected: [], allSelected: false,  errorMessage : '',
        showConfirmModal: false, 
        modalTitle: '', 
        modalMessage: '', 
        modalConfirmText: '', 
        modalConfirmClass: 'bg-blue-600 hover:bg-blue-700', 
        confirmAction: null,        
        showImageZoomModal: false,
        modalImageUrl: '' }"
        @reset-selection.window="selected = []; allSelected = false,  errorMessage = ''"
        @action-on-unprocessed-image.window="selected = selected.filter(id => id != $event.detail.id)"
        tabindex="0"
        @keydown.prevent.stop="if ($event.key === 'Delete' || $event.key === 'Backspace') { 
            if(selected.length > 0) {
                let textPlural = selected.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                $dispatch('confirm-action', {
                    title: '{{ __('Delete') }}',
                    message: `{{ __('You are about to') }} {{ __('delete')  }} ${selected.length} ${textPlural}.`,
                    confirmText: '{{ __('Yes') }}, {{ __('delete')  }} !',
                    confirmClass: 'bg-red-600 hover:bg-red-700',
                    action: () => $wire.call('deleteSelected', selected)
                })
            }
        }"
>

<x-card title="{{ __( 'Pending images' ) }}" class="mt-[15px] mb-[15px]" shadow separator>


<div class="bulk-actions flex items-center flex-wrap space-x-2"
    x-data="{
        handleSelection(actionType, actionMethod, actionTitle, confirmClass) {
            if (selected.length === 0) {
                errorMessage = '{{ __('No item selected') }}';
                setTimeout(() => errorMessage = '', 1500);
                return;
            }

            let textPlural = selected.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
            $dispatch('confirm-action', {
                title: actionTitle,
                message: `{{ __('You are about to') }} ${actionType} ${selected.length} ${textPlural}.`,
                confirmText: `{{ __('Yes') }}, ${actionType} !`,
                confirmClass: confirmClass,
                action: () => $wire.call(actionMethod, selected)
            });
        }
    }"
>
    <button class="btn btn-sm" @click="allSelected = !allSelected; selected = allSelected ? [...document.querySelectorAll('.unprocessed-image-checkbox')].map(cb => cb.value) : []">
        <label for="select-all-checkbox" @click="allSelected = !allSelected; selected = allSelected ? [...document.querySelectorAll('.unprocessed-image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">{{__('Select all')}}</label>
        <input 
            type="checkbox"
            id="select-all-checkbox"
            class="checkbox"
            x-model="allSelected"
        />
    </button>

    <x-button 
        @click="handleSelection('{{ __('approve') }}', 'approveSelected', '{{ __('Approve') }}', 'bg-green-600 hover:bg-green-700')"
        icon="o-check"
        class="btn btn-sm"
        tooltip="{{ __('Approve selection') }}"
        aria-label="{{ __('Approve selection') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
    />

    <x-button 
        @click="handleSelection('{{ __('archive') }}', 'archiveSelected', '{{ __('Archive') }}', 'bg-blue-600 hover:bg-blue-700')"
        icon="o-archive-box"
        class="btn btn-sm"
        tooltip="{{ __('Archive selection') }}"
        aria-label="{{ __('Archive selection') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
    />
    <x-button
        @click="handleSelection('{{ __('delete') }}', 'deleteSelected', '{{ __('Delete') }}', 'bg-red-600 hover:bg-red-700')"
        icon="o-trash"
        class="btn btn-sm"
        tooltip="{{ __('Delete selection') }}"
        aria-label="{{ __('Delete selection') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
    />
    <x-button
        @click="$wire.$refresh()"
        icon="o-arrow-path"
        class="btn btn-sm"
        wire:click.prevent=""
        tooltip="{{ __('Refresh') }}"
        aria-label="{{ __('Refresh') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
    />
    <x-button
        wire:click="fillForDev()"
        icon="o-photo"
        class="btn btn-sm bg-green-600 hover:bg-green-700"
        wire:click.prevent=""
        tooltip="{{ __('Fill with dev images') }}"
        aria-label="{{ __('Fill with dev images') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
    />
    <x-button
        wire:click="cleanDeletedImages()"
        icon="o-trash"
        class="btn btn-sm bg-red-600 hover:bg-red-700"
        wire:click.prevent=""
        tooltip="{{ __('Empty the bin') }}"
        aria-label="{{ __('Empty the bin') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
    />
    <!-- Message d'erreur affiché dynamiquement -->
    <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 mt-2 transition-opacity duration-500"></p>
    <p wire:loading class="text-primary font-bold">Please wait <x-loading class="loading-dots relative -bottom-2.5 text-primary" /></p>
    
</div>


<div class="gallery_wrapper">
    @if($this->images->isEmpty())
        <p class="text-center text-gray-500">{{ __('No image pending.') }}</p>
    @else
        @foreach($this->images as $image)
    
            <!-- Building caption tooltip with Submitter Name and Caption -->
            @php
                $caption_tooltip_classes = "tooltip tooltip-bottom"; // tooltip classes
                $caption_tooltip_icon_visibility = "hidden"; // default = hidden

                // Building tooltip content
                $caption_tooltip_content = '';
                if ($wall->submitter_name_on_wall && $image->submitter_name) {
                    $caption_tooltip_content .= $image->submitter_name;
                }

                if ($wall->caption_on_wall && $image->caption && $wall->submitter_name_on_wall && $image->submitter_name) {
                    $caption_tooltip_content .= ' : ';
                }

                if ($wall->caption_on_wall && $image->caption) {
                    $caption_tooltip_content .= $image->caption;
                    $caption_tooltip_icon_visibility = '';
                }

                // Si rien à afficher, cacher le tooltip
                if (empty($caption_tooltip_content)) {
                    $caption_tooltip_classes = '';
                    $caption_tooltip_icon_visibility = 'hidden';
                }
            @endphp
            
        <div class="image_wrapper {{ ( $caption_tooltip_classes ) }}" data-tip="{{ $caption_tooltip_content }}" wire:key="image-{{ $image->id }}">
            <div class="uper_image_data justify-between">
                <a role="button" @click="$dispatch('open-image-modal', { url: '{{ asset('storage/' . $image->webp_full_path) }}' })">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                    </svg>
                </a>

                <a role="button" 
                    @click="$dispatch('confirm-action', {
                            title: '{{ __('Delete caption') }}',
                            message: '{{ __('Are you sure you want to delete the caption attached to this image?') }}',
                            confirmText: '{{ __('Yes') }}',
                            confirmClass: 'bg-orange-600 hover:bg-orange-700',
                            action: () => $wire.call('deleteCaption', {{ $image->id }})
                        })"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6 {{ ( $caption_tooltip_icon_visibility ) }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                </a>
            <input 
                type="checkbox" 
                class="checkbox checkbox-sm unprocessed-image-checkbox"
                :value="{{ $image->id }}"
                x-model="selected"
                id="checkbox-{{ $image->id }}"
            />
            </div>
                <label for="checkbox-{{ $image->id }}" display="block">
                    <img src="{{ asset('storage/' . $image->thumb_full_path) }}" />
                </label>
            <div class="moderation_buttons flex justify-between">
                <x-button 
                    wire:click="approveImage({{ $image->id }})"
                    icon="o-check"
                    class="btn btn-sm"
                    tooltip="{{ __('Approve') }}"
                    aria-label="{{ __('Approve') }}"
                    @click="$wire.set('selectedImages', selected)"
                    wire:loading.attr="disabled"
                    wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
                />
                <x-button 
                    wire:click="archiveImage({{ $image->id }})"
                    icon="o-archive-box"
                    class="btn btn-sm"
                    tooltip="{{ __('Archive') }}"
                    aria-label="{{ __('Archive') }}"
                    @click="$wire.set('selectedImages', selected)"
                    wire:loading.attr="disabled"
                    wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
                />
                <x-button 
                    wire:click.prevent=""
                    icon="o-trash"
                    class="btn btn-sm btn-danger"
                    tooltip="{{ __('Delete') }}"
                    aria-label="{{ __('Delete') }}"
                    @click="
                        $dispatch('confirm-action', {
                            title: '{{ __('Delete') }}',
                            message: '{{ __('Are you sure you want to delete this image?') }}',
                            confirmText: '{{ __('Yes, delete!') }}',
                            confirmClass: 'bg-red-600 hover:bg-red-700',
                            action: () => $wire.call('deleteImage', {{ $image->id }})
                        })
                    "
                    wire:loading.attr="disabled"
                    wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
                />
            </div>
        </div>
        @endforeach
    @endif
</div>
<div class="galerie-navigation flex justify-evenly">
    {{ $this->images->links(data: ['scrollTo' => false]) }}
</div>

</x-card>

</div>
