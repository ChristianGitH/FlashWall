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
        
}; ?>

<div 
    x-data="{
        showImageZoomModal: false,
        modalImageUrl: '',
        showConfirmModal: false,
        modalTitle: '',
        modalMessage: '',
        modalConfirmText: '',
        modalConfirmClass: '',
        confirmAction: null
    }"
    @open-image-modal.window="modalImageUrl = $event.detail.url; showImageZoomModal = true"
    @confirm-action.window="
        modalTitle = $event.detail.title;
        modalMessage = $event.detail.message;
        modalConfirmText = $event.detail.confirmText;
        modalConfirmClass = $event.detail.confirmClass || 'bg-blue-600 hover:bg-blue-700';
        confirmAction = $event.detail.action;
        showConfirmModal = true;"
    x-cloak
>
    <div class="wall_data">
        <h1>{{ __( $wall->name ) }}</h1>
        <h3>{{ __( $wall->description ) }}</h3>
    </div>

    <livewire:walls.unprocessed-images :wall="$wall" />
    <livewire:walls.approved-images :wall="$wall" />
    <livewire:walls.archived-images :wall="$wall" />

    <!-- Zoom Image Modal -->
    <div 
        x-show="showImageZoomModal"
        @click="showImageZoomModal = false"
        x-transition 
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="shadow-lg overflow-auto relative">
            <div class="close-button-wrapper">
                <x-button @click="showImageZoomModal = false" class="btn btn-sm" icon="o-x-mark" />
            </div>
            <img :src="modalImageUrl" alt="Image Preview" class="w-full h-auto mt-4" />
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div 
        x-show="showConfirmModal"
        x-transition
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 overflow-auto relative">
            <h2 class="text-lg font-semibold" x-text="modalTitle"></h2>
            <p class="mt-2 text-gray-600" x-text="modalMessage"></p>

            <div class="mt-4 flex justify-end space-x-2">
                <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    {{ __('Cancel') }}
                </button>
                <button @click="confirmAction(); showConfirmModal = false" class="px-4 py-2 text-white rounded" :class="modalConfirmClass">
                    <span x-text="modalConfirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>