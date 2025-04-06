<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;


new class extends Component {
    use Toast;

    public Wall $wall;
    
    //public $images;
    public array $selectedImages = [];

    /*public function mount(Wall $wall)
    {
        $this->images = Image::where('wall_id', $wall->id)->get();
    }*/

    public function mount(Wall $wall)
    {
        $this->wall = $wall;
    }

    public function archivedImages()
    {
        return Image::where('wall_id', $this->wall->id)
                    ->where('archived', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    protected $listeners = ['reset-selection-archived' => '$refresh', 'archived-images-updated' => '$refresh',];



    // Approving images //
    public function approveImage(int $id): void
    {
        // Récupérer directement les données nécessaires en une seule requête
       Image::where('id', $id)->update(['approved' => true, 'archived' => false]);

        // Réinitialiser la sélection
        $this->dispatch('reset-selection-archived');
        //  Émission d’événement Livewire vers le composant approved-images
        $this->dispatch('approved-images-updated');
        $this->success(__('Photo approved successfully.'));
    }

    public function approveSelected(array $selectedImages)
    {
        
        if (empty($selectedImages)) {
            $this->error(__('No images selected.'));
            return;
        }

        Image::whereIn('id', $selectedImages)->update(['approved' => true, 'archived' => false]);

        // Réinitialiser la sélection
        $this->dispatch('reset-selection-archived');
        //  Émission d’événement Livewire vers le composant approved-images
        $this->dispatch('approved-images-updated');
        $this->success(__('Selected images approved.'));
    }
    



    // Deleting images //    
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

        // Réinitialiser la sélection
        $this->dispatch('reset-selection-archived');
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
        $this->dispatch('reset-selection-archived');    
        $this->success(__('Selected images deleted.'));
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
        modalImageUrl: '' }" @reset-selection-archived.window="selectedArchived = []; allSelected = false,  errorMessage = ''">

<div class="galery_data">
    <h2>{{ __( 'Archived images' ) }}</h2>
</div>

<div class="bulk-actions flex items-center">
    <button class="btn btn-sm" @click="allSelected = !allSelected; selectedArchived = allSelected ? [...document.querySelectorAll('.image-checkbox')].map(cb => cb.value) : []">
        <label for="select-all-checkbox" @click="allSelected = !allSelected; selectedArchived = allSelected ? [...document.querySelectorAll('.image-checkbox')].map(cb => cb.value) : []" class="cursor-pointer">Select All</label>
        <input 
            type="checkbox"
            id="select-all-checkbox"
            class="checkbox"
            x-model="allSelected"
        />
    </button>

    <x-button 
        @click="
                if (selectedArchived.length === 0) { 
                    errorMessage = '{{ __('No images selected.') }}'; 
                    setTimeout(() => errorMessage = '', 1500);
                } else {
                    modalTitle = '{{ __('Approve Images') }}';
                    modalMessage = '{{ __('You are about to approve') }} ' + selectedArchived.length + ' {{ __('images.') }}';
                    modalConfirmText = '{{ __('Yes, approve !') }}';
                    modalConfirmClass = 'bg-green-600 hover:bg-green-700';
                    showConfirmModal = true; 
                    confirmAction = () => { 
                        errorMessage = ''; 
                        $wire.call('approveSelected', selectedArchived);
                        showConfirmModal = false;
                    };
                }
            "
        icon="o-check"
        class="btn btn-sm"
        tooltip="{{ __('Approve Selected') }}"
        aria-label="{{ __('Approve Selected') }}"
    />

    <x-button     
        @click="
                if (selectedArchived.length === 0) { 
                    errorMessage = '{{ __('No images selected.') }}'; 
                    setTimeout(() => errorMessage = '', 1500);
                } else {
                    modalTitle = '{{ __('Delete images') }}';
                    modalMessage = '{{ __('You are about to delete') }} ' + selectedArchived.length + ' {{ __('images.') }}';
                    modalConfirmText = '{{ __('Yes, delete !') }}';
                    modalConfirmClass = 'bg-red-600 hover:bg-red-700';
                    showConfirmModal = true; 
                    confirmAction = () => { 
                        errorMessage = ''; 
                        $wire.call('deleteSelected', selectedArchived);
                        showConfirmModal = false;
                    };
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

    @if($this->archivedImages()->isEmpty())
        <p class="text-center text-gray-500">{{ __('No archived image.') }}</p>
    @else
        @foreach($this->archivedImages() as $image)
        @if ($image->caption)
        <div class="image_wrapper tooltip tooltip-bottom" data-tip="{{ __( $image->caption ) }}" wire:key="image-{{ $image->id }}">
            <div class="uper_image_data justify-between">
                <a role="button" @click="modalImageUrl = '{{ asset('storage/' . $image->name) }}'; showImageZoomModal = true;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                    </svg>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
        @else
        <div class="image_wrapper" wire:key="image-{{ $image->id }}">
            <div class="uper_image_data justify-between">
                <a role="button" @click="modalImageUrl = '{{ asset('storage/' . $image->name) }}'; showImageZoomModal = true;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                    </svg>
                </a>
        @endif
            <input 
                type="checkbox" 
                class="checkbox checkbox-sm image-checkbox"
                :value="{{ $image->id }}"
                x-model="selectedArchived"
                id="checkbox-{{ $image->id }}"
            />
            </div>
                <label for="checkbox-{{ $image->id }}" display="block">
                    <img src="{{ asset('storage/' . $image->thumb) }}" />
                </label>
            <div class="moderation_buttons flex justify-between">
                <x-button 
                    wire:click="approveImage({{ $image->id }})"
                    icon="o-check"
                    class="btn btn-sm"
                    tooltip="{{ __('Approve image') }}"
                    aria-label="{{ __('Approve image') }}"
                    @click="$wire.set('selectedImages', selectedArchived)"
                />
                <x-button 
                    wire:click.prevent=""
                    icon="o-trash"
                    class="btn btn-sm btn-danger"
                    tooltip="{{ __('Delete image') }}"
                    aria-label="{{ __('Delete image') }}"
                    @click="
                    modalTitle = '{{ __('Delete image') }}';
                    modalMessage = '{{ __('Are you sure you want to delete this image?') }}';
                    modalConfirmText = '{{ __('Yes, delete!') }}';
                    modalConfirmClass = 'bg-red-600 hover:bg-red-700';
                    confirmAction = () => { 
                        $wire.call('deleteImage', {{ $image->id }});
                        showConfirmModal = false;
                    };
                    showConfirmModal = true;
                    "
                />
            </div>
        </div>
        @endforeach
    @endif
</div>


    <!-- Fenêtre modale dynamique (Validation & Suppression) -->
    <div x-show="showConfirmModal" x-transition class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 overflow-auto relative">
            <h2 class="text-lg font-semibold" x-text="modalTitle"></h2>
            <p class="mt-2 text-gray-600" x-text="modalMessage"></p>

            <div class="mt-4 flex justify-end space-x-2">
                <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    {{ __('Cancel') }}
                </button>
                <button @click="confirmAction()" class="px-4 py-2 text-white rounded" :class="modalConfirmClass">
                    <span x-text="modalConfirmText"></span>
                </button>
            </div>
        </div>
    </div>

    
    <!-- Fenêtre modale pour afficher l'image en grand -->
    <div x-show="showImageZoomModal" @click="showImageZoomModal = false" x-transition class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="shadow-lg overflow-auto relative">
            <div class="close-button-wrapper">
                <x-button @click="showImageZoomModal = false" class="btn btn-sm" icon="o-x-mark" />
            </div>
            <img :src="modalImageUrl" alt="Image Preview" class="w-full h-auto mt-4" />
        </div>
    </div>

</div>