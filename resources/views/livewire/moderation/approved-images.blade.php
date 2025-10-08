<?php

// This is a component for walls>moderation.blade.php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;


new class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

    public Wall $wall;
    public array $selectedImages = [];
    public int $approvedImagesPageCount = 0;


    public function mount(Wall $wall)
    {
        $this->wall = $wall;
    }

    public function approvedImages()
    {
        $images = Image::where('wall_id', $this->wall->id)
                    ->where('status', 1) // 0 = unprocessed. 1 = approved. 2 = archived.
                    ->where('permanent', 1)
                    ->orderBy('created_at', 'desc')
                    ->paginate(30, pageName: 'approved-images');
        
        $this->approvedImagesPageCount = $images->count();
        return $images;
    }

    protected $listeners = ['reset-selection-approved' => '$refresh', 'approved-images-updated' => '$refresh'];




    /* Archiving images */
    // The called function by bellow archiveImage and archiveSelected functions
    protected function archive(array $imageIds, string $successMessage): void
    {
        if (empty($imageIds)) {
            $this->error(__('No item selected'));
            return;
        }

        Image::whereIn('id', $imageIds)->update(['status' => 2]); // 0 = unprocessed. 1 = approved. 2 = archived.

        // Updating the copies
        Image::where('wall_id', $this->wall->id)
            ->where('permanent', false)
            ->whereIn('parent_id', $imageIds)
            ->update(['status' => 5]);

        // Pagination reset if all images were archived
        if ($this->approvedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'approved-images');
        }

        //  Livewire event to archived-images so it gets refreshed
        $this->dispatch('archived-images-updated');
        $this->success(__($successMessage));
    }
    // Archiving images //
    public function archiveImage(int $id): void
    {
        // REmove the image from the selection browser side 
        $this->dispatch('action-on-approved-image', id: $id);

        $this->archive([$id], 'Image successfully archived');
    }

    public function archiveSelected(array $selectedImages)
    {
        // Reset the selection browser side
        $this->dispatch('reset-selection-approved');

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


        Image::whereIn('id', $imageIds)->update(['status' => 5]); // 0 = unprocessed. 1 = approved. 2 = archived.

        // Updating the copies
        Image::where('wall_id', $this->wall->id)
            ->where('permanent', false)
            ->whereIn('parent_id', $imageIds)
            ->update(['status' => 5]);

        // Pagination reset if all images were deleted
        if ($this->approvedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'approved-images');
        }

        $this->success(__($successMessage));
    }
    // Single image delete
    public function deleteImage(int $id): void
    {
        $this->delete([$id], 'Image successfully deleted');

        // Supprimer l'image de la sélection côté navigateur
        $this->dispatch('action-on-approved-image', id: $id);
    }
    // Multiple image delete
    public function deleteSelected(array $selectedImages)
    {
        $this->delete($selectedImages, 'Images successfully deleted');

        // Reset selection browser side
        $this->dispatch('reset-selection-approved');
    }




    // Propelling images //
    public function propelImage(int $id): void
    {
        $image = Image::find($id);
        if (!$image) {
            $this->error(__('Image not found'));
            return;
        }

        if ($image->priority === 1) {
            $this->info(__('Image already being propelled'));
            return;
        }

        // All priorities back to 0
        Image::where('wall_id', $this->wall->id)->where('priority', 1)->update(['priority' => 0]);
        // Updating selected image priority
        $image->update(['priority' => 1]);

        // Supprimer l'image de la sélection côté navigateur
        $this->dispatch('action-on-approved-image', id: $id);

        // Reset la pagination uniquement si c'était la dernière image de la page
        if ($this->approvedImagesPageCount <= 1) {
            $this->resetPage(pageName: 'approved-images');
        }
        $this->success(__('Image successfully propelled : will be displayed asap'));
    }
    
}; ?>

<div x-data="{ selectedApproved: [], allSelectedApproved: false, errorMessage : '',
        showConfirmModal: false, 
        modalTitle: '', 
        modalMessage: '', 
        modalConfirmText: '', 
        modalConfirmClass: 'bg-blue-600 hover:bg-blue-700', 
        confirmAction: null,
        showImageZoomModal: false,
        modalImageUrl: '' }" @reset-selection-approved.window="selectedApproved = []; allSelectedApproved = false"
        @action-on-approved-image.window="selectedApproved = selectedApproved.filter(id => id != $event.detail.id)"
        tabindex="0"
        @keydown.prevent.stop="if ($event.key === 'Delete' || $event.key === 'Backspace') { 
            if(selectedApproved.length > 0) {
                let textPlural = selectedApproved.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                $dispatch('confirm-action', {
                    title: '{{ __('Delete') }}',
                    message: `{{ __('You are about to') }} {{ __('delete')  }} ${selectedApproved.length} ${textPlural}.`,
                    confirmText: '{{ __('Yes') }}, {{ __('delete')  }} !',
                    confirmClass: 'bg-red-600 hover:bg-red-700',
                    action: () => $wire.call('deleteSelected', selectedApproved)
                })
            }
        }"
    >

<x-card title="{{ __('Approved images') }}" subtitle="{{ __('These images are displayed')}}" class="mt-[15px] mb-[15px]" shadow separator>

<div class="bulk-actions flex items-center space-x-2"
    x-data="{
        handleSelectionApprovedImages(actionType, actionMethod, actionTitle, confirmClass) {
            if (selectedApproved.length === 0) {
                errorMessage = '{{ __('No item selected') }}';
                setTimeout(() => errorMessage = '', 1500);
                return;
            }

            let textPlural = selectedApproved.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
            $dispatch('confirm-action', {
                title: actionTitle,
                message: `{{ __('You are about to') }} ${actionType} ${selectedApproved.length} ${textPlural}.`,
                confirmText: `{{ __('Yes') }}, ${actionType} !`,
                confirmClass: confirmClass,
                action: () => $wire.call(actionMethod, selectedApproved)
            });
        }
    }"
>
    <button class="btn btn-sm" @click="allSelectedApproved = !allSelectedApproved; selectedApproved = allSelectedApproved ? [...document.querySelectorAll('.approved-image-checkbox')].map(cb => cb.value) : []">
        <label for="approved-select-all-checkbox" @click="allSelectedApproved = !allSelectedApproved; selectedApproved = allSelectedApproved ? [...document.querySelectorAll('.approved-image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">{{__('Select all')}}</label>
        <input 
            type="checkbox"
            id="approved-select-all-checkbox"
            class="checkbox"
            x-model="allSelectedApproved"
        />
    </button>

    <x-button 
        @click="handleSelectionApprovedImages('{{ __('archive') }}', 'archiveSelected', '{{ __('Archive') }}', 'bg-blue-600 hover:bg-blue-700')"
        icon="o-archive-box"
        class="btn btn-sm"
        tooltip="{{ __('Archive selection') }}"
        aria-label="{{ __('Archive selection') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected, propelImage"
    />
    <x-button     
        @click="handleSelectionApprovedImages('{{ __('delete') }}', 'deleteSelected', '{{ __('Delete') }}', 'bg-red-600 hover:bg-red-700')"
        icon="o-trash"
        class="btn btn-sm"
        tooltip="{{ __('Delete selection') }}"
        aria-label="{{ __('Delete selection') }}"
        wire:loading.attr="disabled"
        wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected, propelImage"
    />
    <!-- Message d'erreur affiché dynamiquement -->
    <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 mt-2 transition-opacity duration-500"></p>
    <p wire:loading>Please wait...</p>
</div>


<div class="gallery_wrapper" x-data="{ removeHighlight(id) {
        let btn = document.getElementById('propel-btn-' + id);
        if (btn) {
            setTimeout(() => {
                btn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
            }, 2000);
        }
    }}">

    @if($this->approvedImages()->isEmpty())
        <p class="text-center">{{ __('No approved image.') }}</p>
    @else
    @foreach($this->approvedImages() as $image)
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
                <a role="button" @click="$dispatch('open-image-modal', { url: '{{ asset('storage/' . $image->name) }}' })">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                    </svg>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6 {{ ( $data3 ) }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
            <input 
                type="checkbox" 
                class="checkbox checkbox-sm approved-image-checkbox"
                :value="{{ $image->id }}"
                x-model="selectedApproved"
                id="checkbox-{{ $image->id }}"
            />
            </div>
                <label for="checkbox-{{ $image->id }}" display="block">
                    <img src="{{ asset('storage/' . $image->thumb) }}" />
                </label>
            <div class="moderation_buttons flex justify-between">
                <x-button 
                    wire:click="archiveImage({{ $image->id }})"
                    icon="o-archive-box"
                    class="btn btn-sm"
                    tooltip="{{ __('Archive') }}"
                    aria-label="{{ __('Archive') }}"
                    @click="$wire.set('selectedImages', selectedApproved)"
                    wire:loading.attr="disabled"
                    wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected, propelImage"
                />
                <x-button 
                    id="propel-btn-{{ $image->id }}"
                    icon="o-rocket-launch"
                    class="btn btn-sm {{ $image->priority == 1 ? 'bg-orange-600 hover:bg-orange-700' : '' }}"
                    tooltip="{{ __('Propel') }}"
                    aria-label="{{ __('Propel') }}"
                    @click="
                        $dispatch('confirm-action', {
                            title: '{{ __('Propel') }}',
                            message: '{{ __('Are you sure you want to propel to first place?') }}',
                            confirmText: '{{ __('Yes') }}',
                            confirmClass: 'bg-orange-600 hover:bg-orange-700',
                            action: () => $wire.call('propelImage', {{ $image->id }})
                        })
                    "
                    wire:loading.attr="disabled"
                    wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected, propelImage"
                    x-init="{{ $image->priority == 1 ? 'removeHighlight(' . $image->id . ')' : '' }}"
                />
                <x-button 
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
                    wire:target="approveImage, archiveImage, deleteImage, approveSelected, archiveSelected, deleteSelected, propelImage"
                />
            </div>
        </div>
        @endforeach
    @endif
</div>
<div class="galerie-navigation flex justify-evenly">
    {{ $this->approvedImages()->links(data: ['scrollTo' => false]) }}
</div>

</x-card>
</div>
