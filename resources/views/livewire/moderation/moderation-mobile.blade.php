<?php

use Livewire\Component;
use App\Models\Wall;
use App\Models\Image;
use Livewire\Attributes\Title;

new
#[Title('Moderation Swipe')]

class extends Component {

    public Wall $wall;
    public int $unprocessedImagesCount = 0;

    public function mount(Wall $wall)
    {
        $this->wall = $wall;
        $this->loadImageCount();
    }

    public function loadImageCount()
    {
        $this->unprocessedImagesCount = Image::where('wall_id', $this->wall->id)
            ->where('status', 0)
            ->where('pinned', false)
            ->count();
    }
    
    // Provides a buffer of images
    public function getNextImages()
    {
        $this->loadImageCount();
        return Image::where('wall_id', $this->wall->id)
            ->where('status', 0)
            ->where('pinned', false)
            ->orderBy('created_at')
            ->limit(20)
            ->get()
            ->map(fn($img) => [
                'id' => $img->id,
                'url' => asset('storage/'.$img->webp_full_path),
                'caption' => $img->caption,
                'submitter_name' => $img->submitter_name,
            ])
            ->values();
    }

    // Simple actions
    public function approve(int $id)
    {
        $updated = Image::where('id', $id)
            ->where('status', 0)
            ->where('pinned', false)
            ->update([
                'status' => 1,
                'last_status_update' => now()
            ]);
        if ($updated) {
            $this->unprocessedImagesCount--; // Decrements immediately for better reactivity
        }
    }

    public function archive(int $id)
    {
        $updated = Image::where('id', $id)
            ->where('status', 0)
            ->where('pinned', false)
            ->update([
                'status' => 2,
                'last_status_update' => now()
            ]);
        if ($updated) {
            $this->unprocessedImagesCount--; // Decrements immediately for better reactivity
        }
    }

    public function delete(int $id)
    {
        $updated = Image::where('id', $id)
            ->where('status', 0)
            ->where('pinned', false)
            ->update([
                'status' => 3,
                'last_status_update' => now()
            ]);
        if ($updated) {
            $this->unprocessedImagesCount--; // Decrements immediately for better reactivity
        }
    }

}; ?>

<div class="-mt-4 w-full flex flex-col items-center justify-center overflow-hidden">

    <div class="w-full mb-2">
        <h1 class="text-2xl md:text-3xl lg:text-4xl">
            {{ __('Moderation') }} : {{ __( $wall->name ) }}
        </h1>
        <p class="text-sm text-gray-500">{{ __('Pending images') }} : {{ $unprocessedImagesCount }}
        </p>
    </div>

    <div 
        x-data="{
            images: [],
            currentIndex: 0,
            loading: false,
            startX: 0,
            offsetX: 0,
            isDragging: false,
            isZoomingIn: false,
            isTransitioning: false,
            displayArchiveSVG: false,

            async init() {
                await this.loadImages()
            },

            async loadImages() {
                this.loading = true

                let newImages = await this.$wire.getNextImages()

                // Avoid duplicates
                let existingIds = this.images.map(i => i.id)
                newImages = newImages.filter(img => !existingIds.includes(img.id))

                this.images.push(...newImages)

                this.loading = false
            },

            get current() {
                return this.images[this.currentIndex]
            },

            swipe(direction, isArchiveButtonClicked) {
                let image = this.current
                if (!image) return

                this.displayArchiveSVG = isArchiveButtonClicked

                // Reset animation immediately
                this.offsetX = direction === 'right' ? 500 : -500
                this.isTransitioning = true

                setTimeout(() => {
                    if (direction === 'right') {
                        this.$wire.approve(image.id)
                    }
                    
                    if (direction === 'top') {
                        this.$wire.archive(image.id)
                    }

                    if (direction === 'left') {
                        this.$wire.delete(image.id)
                    }

                    this.currentIndex++
                    this.offsetX = 0
                    this.isZoomingIn = true
                    this.displayArchiveSVG = false

                    setTimeout(() => {
                        this.isTransitioning = false
                        this.isZoomingIn = false
                    }, 50)

                    if (this.images.length - this.currentIndex < 18) {
                        this.loadImages()
                    }
                }, 150)
            }
        }"
        x-init="init()"
        class="w-full flex flex-col items-center"
        :class="{
            'bg-green-100 dark:bg-green-900/30': offsetX > 50 && !displayArchiveSVG,
            'bg-blue-100 dark:bg-blue-900/30': displayArchiveSVG,
            'bg-red-100 dark:bg-red-900/30': offsetX < -50 && !displayArchiveSVG
        }"
    >

    <!-- OVERLAY SWIPE ICON -->
<div 
    x-show="offsetX > 50 || offsetX < -50"
    x-transition
    class="absolute inset-0 flex items-center justify-center pointer-events-none"
>
    
    <!-- APPROVE -->
    <template x-if="offsetX > 50 && !displayArchiveSVG">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 13l4 4L19 7" />
        </svg>
    </template>

    <!-- ARCHIVE -->
    <template x-if="displayArchiveSVG">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
        </svg>
    </template>

    <!-- REJECT -->
    <template x-if="offsetX < -50 && !displayArchiveSVG">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
        </svg>
    </template>

</div>

        <!-- IMAGE CARD -->
        <div 
            class="relative w-full max-w-md h-[60vh] bg-white dark:bg-black rounded-xl shadow flex items-center justify-center overflow-hidden"
            style="touch-action: pan-y;"
            
            @touchstart="
                startX = $event.touches[0].clientX;
                isDragging = true;
            "

            @touchmove="
                if (!isDragging) return;
                offsetX = $event.touches[0].clientX - startX;
            "

            @touchend="
                isDragging = false;

                if (offsetX > 80 || offsetX < -80) {

                    // 1. Send card off screen
                    offsetX = offsetX > 0 ? 500 : -500;

                    // 2. Wait for animation to finish
                    setTimeout(() => {

                        if (offsetX > 0) {
                            swipe('right', false);
                        } else {
                            swipe('left', false);
                        }

                        // 3. Reset after image change
                        offsetX = 0;

                    }, 150);

                } else {
                    offsetX = 0;
                }
            "

            :style="`
                transform: ${isZoomingIn ? 'scale(0.7)' : 'scale(1)'} translateX(${offsetX}px) rotate(${offsetX * 0.05}deg);
                opacity: ${isTransitioning ? '0' : isZoomingIn ? '0' : '1'};
                transition: ${isDragging || isTransitioning ? 'none' : 'transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.6s ease-out'};
            `"
        >

            <!-- IMAGE -->
            <template x-if="current">
                <div class="relative w-full h-full z-20">

                    <img :src="current.url" class="w-full h-full object-contain">

                    <!-- CAPTION WRAPPER -->    
                    <div class="absolute bottom-0 left-0 w-full px-2 py-2 bg-white/80 dark:bg-black/80 text-sm">
                        <p><span x-text="current.submitter_name"></span> : <span x-text="current.caption"></span></p>
                        <p x-text="current.id"></p>
                    </div>

                </div>
            </template>

            <!-- LOADING -->
            <template x-if="loading">
                <x-loading class="loading-ring absolute z-10"/>
            </template>

            <!-- EMPTY -->
            <template x-if="!current && !loading">
                <div class="text-center">
                    <p class="text-gray-500">{{ __('No pending images') }}</p>
                    <x-button
                        @click="
                            loading = true;
                            currentIndex = 0;
                            images = [];
                            loadImages();
                        " icon="o-arrow-path" class="btn btn-sm" label="{{ __('Refresh') }}"
                    />
                </div>
            </template>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="w-full mt-6 px-2">
            <div class="flex flex-nowrap gap-2 justify-between">
                <button 
                    @click="swipe('left', false)" 
                    class="flex-1 min-w-0 px-2 py-2 bg-red-500 hover:bg-red-600 active:scale-95 transition text-white rounded-lg shadow text-[10px] sm:text-xs md:text-sm cursor-pointer truncate"
                >
                    {{ __('Delete') }}
                </button>

                <button 
                    @click="swipe('top', true)"
                    class="flex-1 min-w-0 px-2 py-2 bg-blue-500 hover:bg-blue-600 active:scale-95 transition text-white rounded-lg shadow text-[10px] sm:text-xs md:text-sm cursor-pointer truncate"
                >
                    {{ __('Archive') }}
                </button>

                <button 
                    @click="swipe('right', false)"
                    class="flex-1 min-w-0 px-2 py-2 bg-green-500 hover:bg-green-600 active:scale-95 transition text-white rounded-lg shadow text-[10px] sm:text-xs md:text-sm cursor-pointer truncate"
                >
                    {{ __('Approve') }}
                </button>
            </div>
        </div>

    </div>

</div>