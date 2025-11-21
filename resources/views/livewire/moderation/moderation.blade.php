<?php

use Livewire\Volt\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;


new
#[Title('Moderation')]

class extends Component {
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
    @keydown.escape.window="
        if (showConfirmModal) {
            showConfirmModal = false;
        }
    "
>
    <div class="wall_data">
        <h1 class="text-2xl md:text-3xl lg:text-4xl">
            {{ __('Moderation') }} : {{ __( $wall->name ) }}
        </h1>
        <div class="flex items-center pb-1.5">
            <p class="text-sm font-normal lg:text-base xl:text-lg">{{ $wall->description }}</p>
            
            <!-- Wall settings quick view popover -->
            <x-popover>
                <x-slot:trigger>
                    <x-icon name="o-information-circle" class="text-gray-700 ml-2" title="Wall settings quick view" />
                </x-slot:trigger>
                <x-slot:content>
                    <!-- This code bellow is using Daisy UI, not Mary UI. -->
                    <fieldset class="fieldset">
                        
                        <legend class="fieldset-legend">{{ __('Settings quick view') }}</legend>
                        <label class="label">{{ __('Caption enabled') }} :
                            <input type="checkbox" class="toggle toggle-success" disabled
                                {{ $wall->caption ? 'checked' : '' }}
                            />
                        </label>
                        <label class="label">{{ __('Caption displayed on wall') }} :
                            <input type="checkbox" class="toggle toggle-success" disabled
                                {{ $wall->caption_on_wall ? 'checked' : '' }}
                            />
                        </label>

                        <legend class="fieldset-legend">{{ __('Requested user information') }}</legend>
                        <label class="label">{{ __('Name') }} :
                            <input type="checkbox" class="toggle toggle-success" disabled
                                {{ $wall->ask_name_submitter ? 'checked' : '' }}
                            />
                            {{ $wall->require_name_submitter ? __('and required') : '' }}
                        </label>
                        <label class="label">{{ __('Email') }} :
                            <input type="checkbox" class="toggle toggle-success" disabled
                                {{ $wall->ask_email_submitter ? 'checked' : '' }}
                            />
                            {{ $wall->require_email_submitter ? __('and required') : '' }}
                        </label>
                        <label class="label">{{ __('Name displayed on wall') }} :
                            <input type="checkbox" class="toggle toggle-success" disabled
                                {{ $wall->submitter_name_on_wall ? 'checked' : '' }}
                            />
                        </label>
                    </fieldset>
                    
                </x-slot:content>
            </x-popover>

        </div>
    </div>

    @if ($wall->moderation)
        <livewire:moderation.unprocessed-images :wall="$wall" />
        <livewire:moderation.approved-images :wall="$wall" />
        <livewire:moderation.archived-images :wall="$wall" />
    @else
        <livewire:moderation.all-images :wall="$wall" />
    @endif



    <!-- Zoom Image Modal -->
    <div 
        x-show="showImageZoomModal"
        @click="showImageZoomModal = false"
        x-transition 
        class="fixed inset-0 bg-gray-900/70 flex items-center justify-center z-50"
    >
        <div class="shadow-lg overflow-auto relative">
                <div class="close-button-wrapper">
                    <x-button @click="showImageZoomModal = false" class="btn btn-sm" icon="o-x-mark" />
                </div>
                <img :src="modalImageUrl" alt="Image Preview" class="w-full h-auto mt-4 max-w-[80vw] et max-h-[80vh]" />
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div 
        x-show="showConfirmModal"
        x-transition
        x-init="$watch('showConfirmModal', value => { if(value) $nextTick(() => $refs.confirmButton.focus()) })"
        class="fixed inset-0 bg-gray-900/70 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 overflow-auto relative">
            <h2 class="text-lg font-semibold" x-text="modalTitle"></h2>
            <p class="mt-2 text-gray-600" x-text="modalMessage"></p>
            <p class="mt-3 text-gray-500" ><x-kbd class="text-gray-500 kbd-sm">Esc</x-kbd> : {{ __('Cancel') }}. <x-kbd class="kbd-sm">↵</x-kbd> : {{ __('Confirm') }}.</p>

            <div class="mt-4 flex justify-end space-x-2">
                <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    {{ __('Cancel') }}
                </button>
                <button x-ref="confirmButton" @click="confirmAction(); showConfirmModal = false" class="px-4 py-2 text-white rounded" :class="modalConfirmClass">
                    <span x-text="modalConfirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>