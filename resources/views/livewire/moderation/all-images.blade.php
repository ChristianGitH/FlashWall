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
    public int $unprocessedImagesPageCount = 0;


    public function mount(Wall $wall)
    {
        $this->wall = $wall;
    }

    public function getImagesProperty()
    {
        $images = Image::where('wall_id', $this->wall->id)
                    ->where('permanent', 1)
                    ->where('status', '!=', 5)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20, pageName: 'unprocessed-images');

        $this->unprocessedImagesPageCount = $images->count();
        return $images;
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
        if ($this->unprocessedImagesPageCount <= count($imageIds)) {
            $this->resetPage(pageName: 'unprocessed-images');
        }

        $this->success(__($successMessage));
    }
    // Single image delete
    public function deleteImage(int $id): void
    {
        $this->delete([$id], 'Image successfully deleted');

        // Supprimer l'image de la sélection côté navigateur
        $this->dispatch('image-deleted', id: $id);
    }
    // Multiple image delete
    public function deleteSelected(array $selectedImages)
    {
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
        @image-deleted.window="selected = selected.filter(id => id != $event.detail.id)"
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


<x-card title="{{ __( 'All images' ) }}" class="mt-[15px] mb-[15px]" shadow separator>           
    <p>{{__('Moderation is desactivated!') }} {!! __('All images sent are displayed') !!}.
        <a class="link" href="../setup-wall/{{ $wall->slug }}">{{__('Check Settings page to activate')}}</a>
    </p>
    <x-menu-separator />

<div class="bulk-actions flex items-center space-x-2">
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
        @click="
                if (selected.length === 0) { 
                    errorMessage = '{!! __('No item selected') !!}'; 
                    setTimeout(() => errorMessage = '', 1500);
                } else {
                    let textPlural = selected.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                    $dispatch('confirm-action', {
                        title: '{{ __('Delete images') }}',
                        message: '{{ __('You are about to delete') }} ' + selected.length + ' ' + textPlural + '.',
                        confirmText: '{{ __('Yes, delete!') }}',
                        confirmClass: 'bg-red-600 hover:bg-red-700',
                        action: () => $wire.call('deleteSelected', selected)
                    })
                }
            "
        icon="o-trash"
        class="btn btn-sm"
        tooltip="{{ __('Delete selection') }}"
        aria-label="{{ __('Delete selection') }}"
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
            
        <div class="image_wrapper {{ ( $caption_tooltip_classes ) }}" data-tip="{!! $caption_tooltip_content !!}" wire:key="image-{{ $image->id }}">
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