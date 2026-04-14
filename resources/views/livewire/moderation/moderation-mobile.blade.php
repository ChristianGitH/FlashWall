<?php

use Livewire\Component;
use App\Models\Wall;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Title;


new
#[Title('Moderation Mobile')]

class extends Component {
    use Toast, WithPagination, WithoutUrlPagination, \App\Traits\ModerationImageActions;

    public Wall $wall;

    public int $approvedCount = 0;
    public int $unprocessedCount = 0;
    public int $archivedCount = 0;
    public int $allCount = 0;
    public int $approvedCountTotal = 0;
    public int $unprocessedCountTotal = 0;
    public int $archivedCountTotal = 0;
    public int $allCountTotal = 0;

    public function mount(Wall $wall)
    {
        $this->wall = $wall;
        $this->loadCounts();
    }

    /* -------------------- QUERIES -------------------- */

    public function loadCounts()
    {
        $counts = Image::where('wall_id', $this->wall->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->approvedCountTotal = $counts[1] ?? 0;
        $this->unprocessedCountTotal = $counts[0] ?? 0;
        $this->archivedCountTotal = $counts[2] ?? 0;
        $this->allCountTotal = $counts->sum();
    }

    public function getImagesByStatus(int $status, string $pageName)
    {
        return Image::where('wall_id', $this->wall->id)
            ->where('status', $status)
            ->orderBy('pinned', 'desc')
            ->orderBy('last_status_update', 'asc')
            ->paginate(30, pageName: $pageName);
    }

    public function getApprovedImagesProperty()
    {
        $data = $this->getImagesByStatus(1, 'approved-images');
        $this->approvedCount = $data->count();
        return $data;
    }

    public function getUnprocessedImagesProperty()
    {
        $data = $this->getImagesByStatus(0, 'unprocessed-images');
        $this->unprocessedCount = $data->count();
        return $data;
    }

    public function getArchivedImagesProperty()
    {
        $data = $this->getImagesByStatus(2, 'archived-images');
        $this->archivedCount = $data->count();
        return $data;
    }

    public function getAllImagesProperty()
    {
        $images = Image::where('wall_id', $this->wall->id)
                    ->where('status', '!=', 5)
                    ->orderBy('pinned', 'desc')
                    ->orderBy('created_at', 'asc')
                    ->paginate(30, pageName: 'all-images');

        $this->allCount = $images->count();
        return $images;
    }

    protected $listeners = [
        'approved-images-updated' => '$refresh',
        'unprocessed-images-updated' => '$refresh',
        'archived-images-updated' => '$refresh',
        'all-images-updated' => '$refresh',

        'reset-selection-approved' => '$refresh',
        'reset-selection-unprocessed' => '$refresh',
        'reset-selection-archived' => '$refresh',
        'reset-selection-all' => '$refresh',
    ];

    /* -------------------- ACTIONS -------------------- */

    public function changeStatusFrom(string $context, int|array $ids, int $status)
    {
        $map = [
            'approved' => ['page' => 'approved-images', 'count' => $this->approvedCount],
            'unprocessed' => ['page' => 'unprocessed-images', 'count' => $this->unprocessedCount],
            'archived' => ['page' => 'archived-images', 'count' => $this->archivedCount],
            'all' => ['page' => 'all-images', 'count' => $this->allCount],
        ];

        $conf = $map[$context];

        $this->changeStatus(
            $ids,
            $status,
            $conf['page'],
            $conf['count'],
            $status === 1 ? 'approved' : ($status === 2 ? 'archived' : 'updated'),
            $context.'-images-updated',
            'action-on-'.$context.'-image'
        );

        if (count((array)$ids) > 1) {
            $this->dispatch('reset-selection-'.$context);
        }

        $this->loadCounts();
    }

    public function deleteFrom(string $context, int|array $ids)
    {
        $map = [
            'approved' => ['page' => 'approved-images', 'count' => $this->approvedCount],
            'unprocessed' => ['page' => 'unprocessed-images', 'count' => $this->unprocessedCount],
            'archived' => ['page' => 'archived-images', 'count' => $this->archivedCount],
            'all' => ['page' => 'all-images', 'count' => $this->allCount],
        ];

        $conf = $map[$context];

        $this->removeImages(
            $ids,
            $conf['page'],
            $conf['count'],
            'deleted',
            $context.'-images-updated',
            'action-on-'.$context.'-image'
        );

        if (count((array)$ids) > 1) {
            $this->dispatch('reset-selection-'.$context);
        }

        $this->loadCounts();
    }


    public function togglePinImage(int $id): void
    {
        $image = Image::find($id);
        if (!$image) {
            $this->error(__('Image not found'));
            return;
        }

        if ($image->pinned) {
            $image->update(['pinned' => false]);
            $this->success(__('Image successfully unpinned'));
        }
        else {
            $image->update(['pinned' => true]);
            $this->success(__('Image successfully pinned'));
        }

        $this->dispatch('action-on-all-image', id: $id);
    }

}; ?>

<div 
    x-data="{
        showImageZoomModal: false,
        modalImageZoomId: null,
        modalImageZoomUrl: '',
        modalImageZoomCaption: '',
        modalImageZoomContext: '',
        modalImageZoomCaptionContent: '',
        showImageZoomModal: false,
        showConfirmModal: false,
        modalTitle: '',
        modalMessage: '',
        modalConfirmText: '',
        modalConfirmClass: '',
        confirmAction: null
    }"
    @open-image-modal.window="
        modalImageZoomUrl = $event.detail.url;
        modalImageZoomId = $event.detail.id;
        modalImageZoomCaption = $event.detail.caption;
        modalImageZoomContext = $event.detail.context;
        modalImageZoomCaptionContent = $event.detail.captionContent;
        showImageZoomModal = true;
    "
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
        if (showImageZoomModal) {
            showImageZoomModal = false;
        }
    "
    class="-mt-4"
>
    <div class="w-full mb-2">
        <h1 class="text-2xl md:text-3xl lg:text-4xl">
            {{ __('Moderation') }} : {{ __( $wall->name ) }}
        </h1>
    </div>


    <div x-data="{
        tab: @js($wall->moderation ? 'unprocessed' : 'all'),
        selected: { approved: [], unprocessed: [], archived: [], all: [] },
        allSelected: { approved: false, unprocessed: false, archived: false, all: false },
        errorMessage: ''
    }"

    @reset-selection-approved.window="selected.approved = []; allSelected.approved = false"
    @reset-selection-unprocessed.window="selected.unprocessed = []; allSelected.unprocessed = false"
    @reset-selection-archived.window="selected.archived = []; allSelected.archived = false"
    @reset-selection-all.window="selected.all = []; allSelected.all = false"

    @action-on-approved-image.window="selected.approved = selected.approved.filter(id => id != $event.detail.id)"
    @action-on-unprocessed-image.window="selected.unprocessed = selected.unprocessed.filter(id => id != $event.detail.id)"
    @action-on-archived-image.window="selected.archived = selected.archived.filter(id => id != $event.detail.id)"
    @action-on-all-image.window="selected.all = selected.all.filter(id => id != $event.detail.id)"
    >

    @php
        $tabs = $wall->moderation
            ? [
                'approved' => $this->approvedImages,
                'unprocessed' => $this->unprocessedImages,
                'archived' => $this->archivedImages,
            ]
            : ['all' => $this->allImages];
    @endphp

    @if ($wall->moderation)
    <!-- TABS -->
    <div class="flex flex-wrap space-x-2 mb-4 border-b text-gray-600 border-gray-500">
        <button class="p-1 border-b-1 hover:text-black hover:border-gray-500" :class="tab === 'unprocessed' ? 'text-black border-gray-500' : ''" 
            @click="tab='unprocessed'">{{ __('Pending images') }} ({{ $unprocessedCountTotal }})
        </button>
        <button class="p-1 border-b-1 hover:text-black hover:border-gray-500" :class="tab === 'approved' ? 'text-black border-gray-500' : ''" 
            @click="tab='approved'">{{ __('Approved images') }} ({{ $approvedCountTotal }})
        </button>
        <button class="p-1 border-b-1 hover:text-black hover:border-gray-500" :class="tab === 'archived' ? 'text-black border-gray-500' : ''" 
            @click="tab='archived'">{{ __('Archived images') }} ({{ $archivedCountTotal }})
        </button>
    </div>
    @else
        <p class="p-1 mb-4 border-b-1 hover:text-black hover:border-gray-500">{{ __('All images') }} ({{ $allCountTotal }})</p>
    @endif

    @foreach($tabs as $context => $images)

    <div x-show="tab === '{{ $context }}'">

        <!-- GALLERY -->
        <div class="w-full flex relative justify-start flex-wrap gap-x-4 gap-y-4 py-5">

            @if($images->isEmpty())
                <p class="text-center">No images</p>
            @else

            @foreach($images as $image)


                <!-- Building caption tooltip with Submitter Name and Caption -->
                @php
                    $parts = [];

                    if ($wall->submitter_name_on_wall && $image->submitter_name) {
                        $parts[] = $image->submitter_name;
                    }

                    if ($wall->caption_on_wall && $image->caption) {
                        $parts[] = $image->caption;
                    }

                    $caption_content = implode(' : ', $parts);
                @endphp

            <!-- IMAGE WRAPPER -->
            <div wire:key="image-{{ $context }}-{{ $image->id }}">

                <!-- UPPER BUTTONS -->
                <div class="flex absolute justify-between text-right mt-2 px-2 z-10">
                    @if($image->caption)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    @endif
                </div>
                
                <img @click="$dispatch('open-image-modal', { 
                        id: {{ $image->id }},
                        url: '{{ asset('storage/' . $image->webp_full_path) }}',
                        captionContent: '{{ $caption_content }}',
                        context: '{{ $context }}',
                        caption: @json(!empty($image->caption))
                    })"
                    class="relative object-cover w-[10em] h-[10em]"
                    src="{{ asset('storage/' . $image->thumb_full_path) }}"/>

            </div>

            @endforeach

            @endif

        </div>

        <div class="galerie-navigation flex justify-evenly">
            {{ $images->links(data: ['scrollTo' => false]) }}
        </div>

    </div>

    @endforeach

</div>



    <div 
        x-show="showImageZoomModal"
        x-transition 
        class="fixed inset-0 bg-gray-900/70 flex items-center justify-center z-50"
    >
        <div class="shadow-lg overflow-auto relative">

            <div class="flex absolute justify-between w-full flex-row-reverse text-right mt-2 px-2">
                <x-button @click="showImageZoomModal = false" class="btn btn-sm" icon="o-x-mark" />
 
                <template x-if="modalImageZoomCaption">   
                    <a role="button"  aria-label="{{ __('Delete caption') }}"
                            @click="$dispatch('confirm-action', {
                                title: '{{ __('Delete caption') }}',
                                message: '{{ __('Are you sure you want to delete the caption attached to this image?') }}',
                                confirmText: '{{ __('Yes') }}',
                                confirmClass: 'bg-orange-600 hover:bg-orange-700',
                                action: () => $wire.call('deleteCaption', modalImageZoomId)
                            })"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    </a>
                </template>
            
            </div>

                <img :src="modalImageZoomUrl" class="w-full h-auto max-w-[80vw] et max-h-[80vh]" />

            <!-- MODERATION BUTTONS -->
            <div class="flex justify-between w-full absolute -mt-8 px-2 ">

                <template x-if="modalImageZoomContext === 'approved'">
                    <x-button @click="$wire.call('changeStatusFrom', modalImageZoomContext, modalImageZoomId, 2)"
                    icon="o-archive-box" 
                    class="btn btn-xs"
                    aria-label="{{ __('Archive') }}"
                    wire:loading.attr="disabled"/>

                    <x-button 
                        icon="o-rocket-launch"
                        aria-label="{{ __('Propel') }}"
                        class="btn btn-xs"
                        @click="
                            $dispatch('confirm-action', {
                                title: '{{ __('Propel') }}',
                                message: '{{ __('Are you sure you want to propel to first place?') }}',
                                confirmText: '{{ __('Yes') }}',
                                confirmClass: 'bg-orange-600 hover:bg-orange-700',
                                action: () => $wire.call('propelImage',modalImageZoomId)
                            })
                        "
                        wire:target="propelImage"
                        wire:loading.attr="disabled"
                    />
                </template>

                <template x-if="modalImageZoomContext === 'unprocessed'">
                    <x-button @click="$wire.call('changeStatusFrom', modalImageZoomContext, modalImageZoomId, 1)"
                    icon="o-check" 
                    class="btn btn-xs" 
                    aria-label="{{ __('Approve') }}"
                    wire:loading.attr="disabled"/>
                    <x-button @click="$wire.call('changeStatusFrom', modalImageZoomContext, modalImageZoomId, 2)" 
                    icon="o-archive-box" 
                    class="btn btn-xs"
                    aria-label="{{ __('Archive') }}"
                    wire:loading.attr="disabled"/>
                </template>

                <template x-if="modalImageZoomContext === 'archived'">
                    <x-button @click="$wire.call('changeStatusFrom', modalImageZoomContext, modalImageZoomId, 1)" 
                    icon="o-check" 
                    class="btn btn-xs"
                    aria-label="{{ __('Approve') }}"
                    wire:loading.attr="disabled"/>
                </template>

                <x-button icon="o-trash"
                    class="btn btn-xs btn-danger"
                    aria-label="{{ __('Delete') }}"
                    @click="
                        $dispatch('confirm-action', {
                            title: '{{ __('Delete') }}',
                            message: '{{ __('Are you sure you want to delete this image?') }}',
                            confirmText: '{{ __('Yes, delete!') }}',
                            confirmClass: 'bg-red-600 hover:bg-red-700',
                            action: () => $wire.call('deleteFrom', modalImageZoomContext, modalImageZoomId)
                        })
                    "
                    wire:loading.attr="disabled"
                />

            </div>
            
            <!-- CAPTION WRAPPER -->    
            <div class="flex justify-between w-full px-2 bg-white/80 dark:bg-black/80 py-2">
                <p x-text="modalImageZoomCaptionContent"></p><br>
                <p x-text="modalImageZoomId"></p><br>   
                <p x-text="modalImageZoomContext"></p>
            </div>
        </div>
    </div>




    <!-- Confirmation Modal -->
    <div 
        x-show="showConfirmModal"
        x-transition
        x-init="$watch('showConfirmModal', value => { if(value) $nextTick(() => $refs.confirmButton.focus()) })"
        class="fixed inset-0 bg-gray-900/70 flex items-center justify-center z-50"
    >
        <div class="bg-white dark:bg-black p-6 rounded-lg shadow-lg w-96 overflow-auto relative">
            <h2 class="text-lg font-semibold" x-text="modalTitle"></h2>
            <p class="mt-2" x-text="modalMessage"></p>
            <p class="mt-3" ><x-kbd class="text-gray-500 kbd-sm">Esc</x-kbd> : {{ __('Cancel') }}. <x-kbd class="kbd-sm">↵</x-kbd> : {{ __('Confirm') }}.</p>

            <div class="mt-4 flex justify-end space-x-2">
                <button @click="showConfirmModal = false" class="px-4 py-2 rounded btn">
                    {{ __('Cancel') }}
                </button>
                <button x-ref="confirmButton" @click="confirmAction(); showConfirmModal = false" class="px-4 py-2 text-white rounded" :class="modalConfirmClass">
                    <span x-text="modalConfirmText"></span>
                </button>
            </div>
        </div>
    </div>

</div>