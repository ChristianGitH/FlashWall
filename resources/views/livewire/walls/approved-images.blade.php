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


    /*public function mount(Wall $wall)
    {
        $this->images = Image::where('wall_id', $wall->id)->get();
    }*/

    public function mount(Wall $wall)
    {
        $this->wall = $wall;
    }

    public function approvedImages()
    {
        $images = Image::where('wall_id', $this->wall->id)
                    ->where('approved', true)
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, pageName: 'approved-images');
        
        $this->approvedImagesPageCount = $images->count();
        return $images;
    }

    protected $listeners = ['reset-selection-approved' => '$refresh', 'approved-images-updated' => '$refresh'];



    // Archiving images //
    public function archiveImage(int $id): void
    {
        // Récupérer directement les données nécessaires en une seule requête
       Image::where('id', $id)->update(['archived' => true, 'approved' => false]);

        // Reset la pagination uniquement si c'était la dernière image de la page
        if ($this->approvedImagesPageCount <= 1) {
            $this->resetPage(pageName: 'approved-images');
        }
        //  Émission d’événement Livewire vers le composant archived-images
        $this->dispatch('archived-images-updated');
        $this->success(__('Image archived successfully.'));
    }

    public function archiveSelected(array $selectedImages)
    {
        
        if (empty($selectedImages)) {
            $this->error(__('No image selected.'));
            return;
        }

        Image::whereIn('id', $selectedImages)->update(['archived' => true, 'approved' => false]);

        // Réinitialiser la sélection
        $this->dispatch('reset-selection-approved');
        // Reset la pagination uniquement si c'était la dernière image de la page
        if ($this->approvedImagesPageCount <= 1) {
            $this->resetPage(pageName: 'approved-images');
        }
        //  Émission d’événement Livewire vers le composant archived-images
        $this->dispatch('archived-images-updated');
        $this->success(__('Selected images archived.'));
    }
   



    // Delete images //
    public function deleteImage(int $id): void
    {
        // Récupérer directement les données nécessaires en une seule requête
        $image = Image::where('id', $id)->first(['id', 'name', 'thumb']);
    
        if (!$image) {
            $this->error(__('Image not found.'));
            return;
        }
    
        // Supprimer les fichiers
        Storage::disk('public')->delete([$image->name, $image->thumb]);
    
        // Supprimer l'image de la base de données
        $image->delete();

        // Reset la pagination uniquement si c'était la dernière image de la page
        if ($this->approvedImagesPageCount <= 1) {
            $this->resetPage(pageName: 'approved-images');
        }
        $this->success(__('Photo deleted successfully.'));
    }

    public function deleteSelected(array $selectedImages)
    {
       
        if (empty($selectedImages)) {
            $this->error(__('No images selected.'));
            return;
        }
    
        // Récupérer directement les chemins sous forme de liste plate
        $paths = Image::whereIn('id', $selectedImages)->pluck('name')->merge(
            Image::whereIn('id', $selectedImages)->pluck('thumb')
        )->toArray();
    
        if (empty($paths)) {
            $this->error(__('No valid images found.'));
            return;
        }
    
        // Supprimer les fichiers en une seule requête
        Storage::disk('public')->delete($paths);
    
        // Supprimer les entrées de la base de données
        Image::whereIn('id', $selectedImages)->delete();
    
        // Réinitialiser la sélection
        $this->dispatch('reset-selection-approved');
        // Reset la pagination uniquement si c'était la dernière image de la page
        if ($this->approvedImagesPageCount <= 1) {
            $this->resetPage(pageName: 'approved-images');
        }
        $this->success(__('Selected images deleted.'));
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
        modalImageUrl: '' }" @reset-selection-approved.window="selectedApproved = []; allSelectedApproved = false">

<div class="galery_data">
    <h2>{{ __( 'Approved images' ) }}</h2>
</div>

<div class="bulk-actions flex items-center">
    <button class="btn btn-sm" @click="allSelectedApproved = !allSelectedApproved; selectedApproved = allSelectedApproved ? [...document.querySelectorAll('.approved-image-checkbox')].map(cb => cb.value) : []">
        <label for="approved-select-all-checkbox" @click="allSelectedApproved = !allSelectedApproved; selectedApproved = allSelectedApproved ? [...document.querySelectorAll('.approved-image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">Select All</label>
        <input 
            type="checkbox"
            id="approved-select-all-checkbox"
            class="checkbox"
            x-model="allSelectedApproved"
        />
    </button>

    <x-button 
        @click="if (selectedApproved.length === 0) { 
                    errorMessage = '{{ __('No images selected.') }}'; 
                    setTimeout(() => errorMessage = '', 1500);
                } else {
                    let textPlural = selectedApproved.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                    $dispatch('confirm-action', {
                        title: '{{ __('Archive images') }}',
                        message: '{{ __('You are about to archive') }} ' + selectedApproved.length + ' ' + textPlural + '.',
                        confirmText: '{{ __('Yes, archive!') }}',
                        confirmClass: 'bg-blue-600 hover:bg-blue-700',
                        action: () => $wire.call('archiveSelected', selectedApproved)
                    })
                }
            "
        icon="o-archive-box"
        class="btn btn-sm"
        tooltip="{{ __('Archive selected') }}"
        aria-label="{{ __('Archive selected') }}"
    />
    <x-button     
        @click="
                if (selectedApproved.length === 0) { 
                    errorMessage = '{{ __('No images selected.') }}'; 
                    setTimeout(() => errorMessage = '', 1500);
                } else {
                    let textPlural = selectedApproved.length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                    $dispatch('confirm-action', {
                        title: '{{ __('Delete images') }}',
                        message: '{{ __('You are about to delete') }} ' + selectedApproved.length + ' ' + textPlural + '.',
                        confirmText: '{{ __('Yes, delete !') }}',
                        confirmClass: 'bg-red-600 hover:bg-red-700',
                        action: () => $wire.call('deleteSelected', selectedApproved)
                    })
                }
            "
        icon="o-trash"
        class="btn btn-sm btn-danger"
        wire:click.prevent=""
        tooltip="{{ __('Delete Selected') }}"
        aria-label="{{ __('Delete Selected') }}"
    />
    <!-- Message d'erreur affiché dynamiquement -->
    <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 mt-2 transition-opacity duration-500"></p>
    <p wire:loading>Please wait...</p>
</div>


<div class="gallery_wrapper">

    @if($this->approvedImages()->isEmpty())
        <p class="text-center text-gray-500">{{ __('No approved image.') }}</p>
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                    </svg>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 {{ ( $data3 ) }}">
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
                    tooltip="{{ __('Archive Selected') }}"
                    aria-label="{{ __('Archive Selected') }}"
                    @click="$wire.set('selectedImages', selectedApproved)"
                />
                <x-button 
                    wire:click.prevent=""
                    icon="o-trash"
                    class="btn btn-sm btn-danger"
                    tooltip="{{ __('Delete image') }}"
                    aria-label="{{ __('Delete image') }}"
                    @click="
                        $dispatch('confirm-action', {
                            title: '{{ __('Delete image') }}',
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
    {{ $this->approvedImages()->links(data: ['scrollTo' => false]) }}
</div>

</div>
