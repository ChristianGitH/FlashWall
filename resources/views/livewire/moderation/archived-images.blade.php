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

new class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    public Wall $wall;
    public array $selectedImages = [];
    public int $archivedImagesPageCount = 0;

    public function mount(Wall $wall)
    {
        $this->wall = $wall;
    }

    public function getArchivedImagesProperty()
    {
        $ArchivedImages = Image::where('wall_id', $this->wall->id)
                    ->where('status', 2) // 0 = unprocessed. 1 = approved. 2 = archived.
                    ->where('permanent', 1)
                    ->orderBy('created_at', 'desc')
                    ->paginate(30, pageName: 'archived-images');

        $this->archivedImagesPageCount = $ArchivedImages->count();
        return $ArchivedImages;
    }

    protected $listeners = ['reset-selection-archived' => '$refresh', 'archived-images-updated' => '$refresh',];


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
        if ($this->archivedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'unprocessed-images');
        }

        //  Livewire event to approved-images so it gets refreshed
        $this->dispatch('approved-images-updated');
        $this->success(__($successMessage));
    }
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
        $this->dispatch('action-on-archived-image', id: $id);

        $this->approve([$id], 'Image successfully approved');
    }

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

        // Reset sélection browser side
        $this->dispatch('reset-selection-archived');

        $this->approve([$selectedImages], 'Images successfully approved');
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
        if ($this->archivedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'archived-images');
        }

        $this->success(__($successMessage));
    }
    // Single image delete
    public function deleteImage(int $id): void
    {
        $image = Image::where('id', $id)->first(['id', 'name', 'webp_name', 'thumb']);
    
        if (!$image) {
            $this->error(__('Image not found.'));
            return;
        }

        // Delete files using accessors
        Storage::disk('public')->delete([
            $image->original_full_path,
            $image->webp_full_path,
            $image->thumb_full_path,
        ]);

        // Delete from database
        $image->delete();

        $this->delete([$id], 'Image successfully deleted');

        // Supprimer l'image de la sélection côté navigateur
        $this->dispatch('action-on-archived-image', id: $id);
    }
    // Multiple image delete
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
        $this->dispatch('reset-selection-archived');
    }

        
}; ?>

<div x-data="{ selectedArchived: [], allSelected: false,  errorMessage : '',
        showConfirmModal: false, 
        modalTitle: '', 
        modalMessage: '', 
        modalConfirmText: '', 
        modalConfirmClass: 'bg-blue-600 hover:bg-blue-700', 
        confirmAction: null,
        showImageZoomModal: false,
        modalImageUrl: '' }" @reset-selection-archived.window="selectedArchived = []; allSelected = false,  errorMessage = ''"
        @action-on-archived-image.window="selectedArchived = selectedArchived.filter(id => id != $event.detail.id)"
        tabindex="0"
        @keydown.prevent.stop="if ($event.key === 'Delete' || $event.key === 'Backspace') { 
            if(selectedArchived.length > 0) {
                let textPlural = selectedArchived.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                $dispatch('confirm-action', {
                    title: '{{ __('Delete') }}',
                    message: `{{ __('You are about to') }} {{ __('delete')  }} ${selectedArchived.length} ${textPlural}.`,
                    confirmText: '{{ __('Yes') }}, {{ __('delete')  }} !',
                    confirmClass: 'bg-red-600 hover:bg-red-700',
                    action: () => $wire.call('deleteSelected', selectedArchived)
                })
            }
        }"
>

<x-collapse separator>
    <x-slot:heading>{{ __( 'Archived images' ) . ' (' . $this->ArchivedImages->total() }})</x-slot:heading>

    <x-slot:content>

        <div class="bulk-actions flex items-center space-x-2"
        x-data="{
                handleSelectionArchivedImages(actionType, actionMethod, actionTitle, confirmClass) {
                    if (selectedArchived.length === 0) {
                        errorMessage = '{{ __('No item selected') }}';
                        setTimeout(() => errorMessage = '', 1500);
                        return;
                    }

                    let textPlural = selectedArchived.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                    $dispatch('confirm-action', {
                        title: actionTitle,
                        message: `{{ __('You are about to') }} ${actionType} ${selectedArchived.length} ${textPlural}.`,
                        confirmText: `{{ __('Yes') }}, ${actionType} !`,
                        confirmClass: confirmClass,
                        action: () => $wire.call(actionMethod, selectedArchived)
                    });
                }
            }"
        >

            <button class="btn btn-sm" @click="allSelected = !allSelected; selectedArchived = allSelected ? [...document.querySelectorAll('.archived-image-checkbox')].map(cb => cb.value) : []">
                <label for="archived-select-all-checkbox" @click="allSelected = !allSelected; selectedArchived = allSelected ? [...document.querySelectorAll('.archived-image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">{{__('Select all')}}</label>
                <input 
                    type="checkbox"
                    id="archived-select-all-checkbox"
                    class="checkbox"
                    x-model="allSelected"
                />
            </button>

            <x-button 
                @click="handleSelectionArchivedImages('{{ __('approve') }}', 'approveSelected', '{{ __('Approve') }}', 'bg-green-600 hover:bg-green-700')"
                icon="o-check"
                class="btn btn-sm"
                tooltip="{{ __('Approve selection') }}"
                aria-label="{{ __('Approve selection') }}"
                wire:loading.attr="disabled"
                wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
            />

            <x-button     
                @click="handleSelectionArchivedImages('{{ __('delete') }}', 'deleteSelected', '{{ __('Delete') }}', 'bg-red-600 hover:bg-red-700')"
                icon="o-trash"
                class="btn btn-sm"
                tooltip="{{ __('Delete selection') }}"
                aria-label="{{ __('Delete selection') }}"
                wire:loading.attr="disabled"
                wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
            />
            <!-- Message d'erreur affiché dynamiquement -->
            <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 mt-2 transition-opacity duration-500"></p>
            <p wire:loading class="text-primary font-bold">Please wait <x-loading class="-bottom-0.5 loading-dots relative text-primary" /></p>
            
        </div>


        <div class="gallery_wrapper">

            @if($this->ArchivedImages->isEmpty())
                    <p class="text-center text-gray-500">{{ __('No archived image.') }}</p>
                @else
                    @foreach($this->ArchivedImages as $image)
                    @php
                    if ($image->caption) {
                        $data1 = "tooltip tooltip-bottom";
                        $data2 = "$image->caption";
                        $data3 = "";
                    } else {
                        $data1 = "";
                        $data2 = "";
                        $data3 = "hidden";
                    }
                @endphp
                <div class="image_wrapper {{ ( $data1 ) }}" data-tip="{!! $data2 !!}" wire:key="image-{{ $image->id }}">
                    <div class="uper_image_data justify-between">
                        <a role="button" @click="$dispatch('open-image-modal', { url: '{{ asset('storage/' . $image->webp_full_path) }}' })">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                            </svg>
                        </a>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6 {{ ( $data3 ) }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    <input 
                        type="checkbox" 
                        class="checkbox checkbox-sm archived-image-checkbox"
                        :value="{{ $image->id }}"
                        x-model="selectedArchived"
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
                            class="btn btn-xs"
                            tooltip="{{ __('Approve') }}"
                            aria-label="{{ __('Approve') }}"
                            @click="$wire.set('selectedImages', selectedArchived)"
                            wire:loading.attr="disabled"
                            wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected"
                        />
                        <x-button 
                            icon="o-trash"
                            class="btn btn-xs btn-danger"
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
            {{ $this->archivedImages->links(data: ['scrollTo' => false]) }}
        </div>
   
    </x-slot:content>
</x-collapse>

</div>
