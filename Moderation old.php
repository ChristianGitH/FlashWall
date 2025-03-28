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
    
    public $images;
    public array $selectedImages = [];

    public function mount(Wall $wall)
    {
        $this->images = Image::where('wall_id', $wall->id)->get();
    }


    public function approveSelected()
    {
        Image::whereIn('id', $this->selectedImages)->update(['approved' => true]);
        $this->resetSelection();
    }

    public function archiveSelected()
    {
        Image::whereIn('id', $this->selectedImages)->update(['archived' => true]);
        $this->resetSelection();
    }

    public function deleteSelected(): void
    {

        dd($this->selectedImages);
        // Vérification des IDs reçus
        $images = Image::whereIn('id', $this->selectedImages)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete([$image->name, $image->thumb]);
            $image->delete();
        }

        $this->resetSelection();
        $this->success(__('Photos deleted successfully.'));
    }


    private function resetSelection()
    {
        $this->selectedImages = [];
    }

}; ?>

<div x-data="{
    @entangle('selectedImages').defer,
        selectedImages: [], 
        selectAll: false, 
        init() {
            this.selectedImages = @js($selectedImages);
        },
        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            this.selectedImages = this.selectAll ? 
                [...document.querySelectorAll('.image-checkbox')].map(cb => cb.value) : [];
        },
        toggleImageSelection(id) {
            id = id.toString(); // Convertir en string pour éviter les erreurs de type
            if (this.selectedImages.includes(id)) {
                this.selectedImages = this.selectedImages.filter(imgId => imgId !== id);
            } else {
                this.selectedImages.push(id);
            }
        }
    }"
    x-init="init()"
>



<div class="wall_data">
    <h1>{{ __( $wall->name ) }}</h1>
    <h3>{{ __( $wall->description ) }}</h3>
</div>

<div class="bulk-actions flex items-center">
    <button class="btn btn-sm">
    <label for="select-all-checkbox" class="cursor-pointer">Select All</label>
        <input 
            type="checkbox"
            id="select-all-checkbox"
            class="checkbox"
            @change="toggleSelectAll"
            :checked="selectAll"
        />
    </button>
    <x-button 
        wire:click="approveSelected"
        icon="o-check"
        class="btn btn-sm"
        tooltip="{{ __('Approve Selected') }}"
        aria-label="{{ __('Approve Selected') }}"
    />
    <x-button 
        wire:click="archiveSelected"
        icon="o-archive-box"
        class="btn btn-sm"
        tooltip="{{ __('Archive Selected') }}"
        aria-label="{{ __('Archive Selected') }}"
    />
    <x-button 
        wire:click.prevent="deleteSelected"
        icon="o-trash"
        class="btn btn-sm btn-danger"
        wire:confirm='{{ __("Are you sure you want to delete") }}'
        tooltip="{{ __('Delete Selected') }}"
        aria-label="{{ __('Delete Selected') }}"
    />
</div>

<div class="gallery_wrapper">

        @foreach($images as $image)
        @if ($image->caption)
        <div class="image_wrapper tooltip tooltip-bottom" data-tip="{{ __( $image->caption ) }}">
            <div class="uper_image_data justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
        @else
        <div class="image_wrapper">
            <div class="uper_image_data justify-end">
        @endif
            <input 
                type="checkbox" 
                class="checkbox image-checkbox"
                :value="{{ $image->id }}" 
                :checked="selectedImages.includes('{{ $image->id }}')"
                @change="toggleImageSelection('{{ $image->id }}')"
            />
            </div>
                <a href="{{ asset('storage/' . $image->name) }}" class="block">
                    <img src="{{ asset('storage/' . $image->thumb) }}" />
                </a>
            <div class="moderation_buttons flex justify-between">
                <x-button 
                    wire:click="approve({{ $image->id }})"
                    icon="o-check"
                    class="btn btn-sm btn-outline"
                    tooltip="{{ __('Appprove') }}"
                    aria-label="{{ __('Appprove') }}"
                />
                <x-button 
                    wire:click="archive({{ $image->id }})"
                    icon="o-archive-box"
                    class="btn btn-sm btn-outline"
                    tooltip="{{ __('Archive') }}"
                    aria-label="{{ __('Archive') }}"
                />
                <x-button 
                    wire:click.prevent="deleteSelected({{ $image->id }})"
                    icon="o-trash"
                    class="btn btn-sm btn-outline"
                    wire:confirm="{{ __('Are you sure you want to delete this photo?') }}"
                    tooltip="{{ __('Delete') }}"
                    aria-label="{{ __('Delete') }}"
                />
            </div>
        </div>
        @endforeach
</div>

</div>