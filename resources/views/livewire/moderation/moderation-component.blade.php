<?php

use Livewire\Component;
use App\Models\Wall;
use App\Models\Image;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Storage;



/* For fillForDev functionality */
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Intervention\Image\Drivers\GD\Driver;

new class extends Component {
    use Toast, WithPagination, WithoutUrlPagination;

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



    protected function ensureImageIds(array $imageIds): bool
    {
        if (empty($imageIds)) {
            $this->error(__('No item selected'));
            return false;
        }

        return true;
    }

    protected function normalizeImageIds(int|array $imageIds): array
    {
        return is_array($imageIds) ? $imageIds : [$imageIds];
    }

    protected function changeStatus(int|array $imageIds, int $status, string $pageName, int $pageCount, string $actionType, ?string $dispatchEvent = null, ?string $singleRemoveEvent = null): void
    {
        $ids = $this->normalizeImageIds($imageIds);
        
        if (!$this->ensureImageIds($ids)) {
            return;
        }

        Image::whereIn('id', $ids)->update(['status' => $status]);

        $message = match($actionType) {
            'archived' => count($ids) === 1 ? __('Image successfully archived') : __('Images successfully archived'),
            'approved' => count($ids) === 1 ? __('Image successfully approved') : __('Images successfully approved'),
            'deleted' => count($ids) === 1 ? __('Image successfully deleted') : __('Images successfully deleted'),
            default => count($ids) === 1 ? __('Image successfully updated') : __('Images successfully updated'),
        };

        $this->success($message);

        if ($pageCount <= count($ids)) {
            $this->resetPage(pageName: $pageName);
        }

        if ($dispatchEvent !== null) {
            $this->dispatch($dispatchEvent);
        }

        if (count($ids) === 1 && $singleRemoveEvent !== null) {
            $this->dispatch($singleRemoveEvent, id: $ids[0]);
        } elseif (count($ids) > 1) {
            // For multiple items, dispatch a bulk reset event (expected in blade)
            // This will be handled by component-specific reset event
        }

    }

    protected function removeImages(int|array $imageIds, string $pageName, int $pageCount, string $actionType, ?string $dispatchEvent = null, ?string $singleRemoveEvent = null): void
    {
        $ids = $this->normalizeImageIds($imageIds);
        
        if (!$this->ensureImageIds($ids)) {
            return;
        }

        $images = Image::whereIn('id', $ids)->get(['id', 'webp_name', 'thumb']);
        if ($images->isEmpty()) {
            $this->error(__('No valid images found.'));
            return;
        }

        $paths = $images->flatMap(fn($image) => [
            $image->webp_full_path,
            $image->thumb_full_path,
        ])->toArray();

        Storage::disk('public')->delete($paths);
        Image::whereIn('id', $ids)->delete();

        $message = match($actionType) {
            'deleted' => count($ids) === 1 ? __('Image successfully deleted') : __('Images successfully deleted'),
            default => count($ids) === 1 ? __('Image updated') : __('Images updated'),
        };

        $this->success($message);

        if ($pageCount <= count($ids)) {
            $this->resetPage(pageName: $pageName);
        }

        if ($dispatchEvent !== null) {
            $this->dispatch($dispatchEvent);
        }

        if (count($ids) === 1 && $singleRemoveEvent !== null) {
            $this->dispatch($singleRemoveEvent, id: $ids[0]);
        }

    }

    public function deleteCaption(int $id): void
    {
        $image = Image::find($id);
        if (!$image) {
            $this->error(__('Image not found'));
            return;
        }

        $image->update(['caption' => null]);
        $this->success(__('Caption successfully deleted'));
    }


    public function propelImage(int $id, ?int $pageCount = null, ?string $pageName = null): void
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

        Image::where('wall_id', $this->wall->id)->where('priority', 1)->update(['priority' => 0]);
        $image->update(['priority' => 1]);

        $this->success(__('Image successfully propelled : will be displayed asap'));

        $this->dispatch('action-on-approved-image', id: $id);

        if ($pageCount !== null && $pageName !== null && $pageCount <= 1) {
            $this->resetPage(pageName: $pageName);
        }

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


    
    /* For testing and dev only */
    public function fillForDev(): void
    {
        $sourceImage = base_path('public/storage/N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg'); // ton image source

        for ($i = 0; $i < 30; $i++) {
            // Générer un nom unique pour l'image originale
            $ext = pathinfo($sourceImage, PATHINFO_EXTENSION); // jpg
            $filename = 'image_' . $i . '_' . Str::random(8) . '.' . $ext;

            // Copier l'image originale
            Storage::disk('public')->put('walls_images/images_submitters/' . $filename, file_get_contents($sourceImage));

            // Generate file names
            $thumbFilename = 'thumb_' . $i . '_' . Str::random(8) . '.webp';
            $webpFilename = 'webp_' . $i . '_' . Str::random(8) . '.webp';

            // Generate and save WebP version
            $webpImage = InterventionImage::read($sourceImage)->encodeByExtension('webp', 80); // 80 : quality (0 to 100)
            Storage::disk('public')->put('walls_images/webp_images_submitters/' . $webpFilename, $webpImage);

            // Generate and save thumbnail
            $thumb = InterventionImage::read($sourceImage)
                ->scale(width: 500)
                ->encodeByExtension('webp', 80);

            Storage::disk('public')->put('walls_images/thumbs_submitters/' . $thumbFilename, $thumb);

            // Créer l'entrée en base
            Image::create([
                'wall_id' => $this->wall->id,
                'name' => $filename,       // original
                'webp_name' => $webpFilename,       // webp
                'thumb' => $thumbFilename, // miniature WebP
                'caption' => 'Image : '. $i,
                'avatar' => 'Image : '. $i,
                'visitor_token' => '1458-afgd',
                'submitter_name' => 'Username',
                'submitter_avatar' => '😊',
            ]);
        }
    }

}; ?>

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
    <div class="flex space-x-2 mb-4 border-b text-gray-600 border-gray-500">
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

        <!-- BULK ACTIONS -->
        <div class="bulk-actions flex items-center space-x-2"
            x-data="{
                handleSelection(action, status = null, actionTitle, confirmClass) {
                    if (selected['{{ $context }}'].length === 0) {
                        errorMessage = '{{ __('No item selected') }}';
                        setTimeout(() => errorMessage = '', 1500);
                        return;
                    }

                    let textPlural = selected['{{ $context }}'].length === 1 ? '{{ __('image') }}' : '{{ __('images') }}';
                    $dispatch('confirm-action', {
                        title: actionTitle,
                        message: `{{ __('You are about to') }} ${action} ${selected['{{ $context }}'].length} ${textPlural}.`,
                        confirmText: `{{ __('Yes') }}, ${action} !`,
                        confirmClass: confirmClass,
                        action: () => {
                            if (action === 'delete') {
                                $wire.deleteFrom('{{ $context }}', selected['{{ $context }}']);
                            } else {
                                $wire.changeStatusFrom('{{ $context }}', selected['{{ $context }}'], status);
                            }
                        }
                    });
                }
            }">

            <button class="btn btn-sm"
                @click="allSelected['{{ $context }}'] = !allSelected['{{ $context }}']; selected['{{ $context }}'] = allSelected['{{ $context }}'] ? [...document.querySelectorAll('.{{ $context }}-checkbox')].filter(cb => !cb.disabled).map(cb => cb.value) : []">
                Select all
                <input type="checkbox" class="checkbox" x-model="allSelected['{{ $context }}']" wire:loading.attr="disabled"/>
            </button>

            @if($context === 'approved')
                <x-button @click="handleSelection('{{ __('archive') }}', 2, '{{ __('Archive') }}', 'bg-blue-600 hover:bg-blue-700')"
                icon="o-archive-box" class="btn btn-sm" 
                wire:loading.attr="disabled"
                tooltip="{{ __('Archive') }}"
                aria-label="{{ __('Archive') }}"/>
            @endif

            @if($context === 'unprocessed')
                <x-button @click="handleSelection('{{ __('approve') }}', 1, '{{ __('Approve') }}', 'bg-green-600 hover:bg-green-700')"
                icon="o-check"
                class="btn btn-sm"
                wire:loading.attr="disabled"
                tooltip="{{ __('Approve') }}"
                aria-label="{{ __('Approve') }}"/>

                <x-button @click="handleSelection('{{ __('archive') }}', 2, '{{ __('Archive') }}', 'bg-blue-600 hover:bg-blue-700')"
                icon="o-archive-box"
                class="btn btn-sm"
                wire:loading.attr="disabled"
                tooltip="{{ __('Archive') }}"
                aria-label="{{ __('Archive') }}"/>

                <x-button @click="$wire.$refresh()" icon="o-arrow-path" class="btn btn-sm" wire:click.prevent=""
                    tooltip="{{ __('Refresh') }}"
                    aria-label="{{ __('Refresh') }}"
                    wire:loading.attr="disabled"
                />
            @endif

            @if($context === 'archived')
                <x-button @click="handleSelection('{{ __('approve') }}', 1, '{{ __('Approve') }}', 'bg-green-600 hover:bg-green-700')"
                icon="o-check" 
                class="btn btn-sm" 
                wire:loading.attr="disabled"
                tooltip="{{ __('Approve') }}"
                aria-label="{{ __('Approve') }}"/>
            @endif

            <x-button @click="handleSelection('{{ __('delete') }}', 5, '{{ __('Delete') }}', 'bg-red-600 hover:bg-red-700')"
                icon="o-trash"
                class="btn btn-xs btn-danger"
                tooltip="{{ __('Delete') }}"
                aria-label="{{ __('Delete') }}"
                wire:loading.attr="disabled"
            />

            <x-button
                wire:click="fillForDev()"
                icon="o-photo"
                class="btn btn-sm bg-green-600 hover:bg-green-700"
                tooltip="{{ __('Fill with dev images') }}"
                aria-label="{{ __('Fill with dev images') }}"
                wire:loading.attr="disabled"
            />

            <p x-show="errorMessage" x-text="errorMessage" class="text-red-500"></p>
        </div>

        <!-- GALLERY -->
        <div class="gallery_wrapper">

            @if($images->isEmpty())
                <p class="text-center">No images</p>
            @else

            @foreach($images as $image)

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

            <div class="image_wrapper {{ ( $caption_tooltip_classes ) }}" data-tip="{!! $caption_tooltip_content !!}" wire:key="image-{{ $context }}-{{ $image->id }}">
                <div class="uper_image_data justify-between">
                    <a role="button" @click="$dispatch('open-image-modal', { url: '{{ asset('storage/' . $image->webp_full_path) }}' })" class="tooltip tooltip-top" data-tip="Zoom">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                        </svg>
                    </a>
                    
                    <a role="button" class="{{ ( $caption_tooltip_icon_visibility ) }} tooltip tooltip-top"
                        data-tip="{{ __('Delete caption') }}"
                        @click="$dispatch('confirm-action', {
                                title: '{{ __('Delete caption') }}',
                                message: '{{ __('Are you sure you want to delete the caption attached to this image?') }}',
                                confirmText: '{{ __('Yes') }}',
                                confirmClass: 'bg-orange-600 hover:bg-orange-700',
                                action: () => $wire.call('deleteCaption', {{ $image->id }})
                            })"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    </a>

                    @if ($image->pinned)
                        <a role="button" wire:click="togglePinImage({{ $image->id }})" class="tooltip tooltip-top" data-tip="{{ __('Unpin') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" class="size-6">
                                <path fill-rule="evenodd" d="M6.32 2.577a49.255 49.255 0 0 1 11.36 0c1.497.174 2.57 1.46 2.57 2.93V21a.75.75 0 0 1-1.085.67L12 18.089l-7.165 3.583A.75.75 0 0 1 3.75 21V5.507c0-1.47 1.073-2.756 2.57-2.93Z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <a role="button" wire:click="togglePinImage({{ $image->id }})" class="tooltip tooltip-top" data-tip="{{ __('Pin') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 1.664 1.664M21 21l-1.5-1.5m-5.485-1.242L12 17.25 4.5 21V8.742m.164-4.078a2.15 2.15 0 0 1 1.743-1.342 48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185V19.5M4.664 4.664 19.5 19.5" />
                            </svg>
                        </a>
                    @endif

                    <input 
                        type="checkbox"
                        class="checkbox checkbox-sm {{ $context }}-checkbox"
                        :value="{{ $image->id }}"
                        x-model="selected['{{ $context }}']"
                        id="checkbox-{{ $context }}-{{ $image->id }}"
                        @if($image->pinned) disabled @endif
                    />

                </div>

                <label for="checkbox-{{ $context }}-{{ $image->id }}">
                    <img src="{{ asset('storage/' . $image->thumb_full_path) }}" />
                </label>

                <div class="moderation_buttons flex justify-between">

                    @if($context === 'approved')
                        <x-button wire:click="changeStatusFrom('{{ $context }}', {{ $image->id }}, 2)" 
                        icon="o-archive-box" 
                        class="btn btn-xs"
                        tooltip="{{ __('Archive') }}"
                        aria-label="{{ __('Archive') }}"
                        wire:loading.attr="disabled"/>

                        <x-button 
                            id="propel-btn-{{ $image->id }}"
                            icon="o-rocket-launch"
                            class="btn btn-xs {{ $image->priority == 1 ? 'bg-orange-600 hover:bg-orange-700' : '' }}"
                            tooltip="{{ __('Propel') }}"
                            aria-label="{{ __('Propel') }}"
                            @click="
                                $dispatch('confirm-action', {
                                    title: '{{ __('Propel') }}',
                                    message: '{{ __('Are you sure you want to propel to first place?') }}',
                                    confirmText: '{{ __('Yes') }}',
                                    confirmClass: 'bg-orange-600 hover:bg-orange-700',
                                    action: () => $wire.call('propelImage',{{ $image->id }},{{ $this->approvedCount  }},'approved-images')
                                })
                            "
                            wire:target="propelImage"
                            wire:loading.attr="disabled"
                            x-init="{{ $image->priority == 1 ? 'removeHighlight(' . $image->id . ')' : '' }}"
                        />
                    @endif

                    @if($context === 'unprocessed')
                        <x-button wire:click="changeStatusFrom('{{ $context }}', {{ $image->id }}, 1)" 
                        icon="o-check" 
                        class="btn btn-xs" 
                        tooltip="{{ __('Approve') }}"
                        aria-label="{{ __('Approve') }}"
                        wire:loading.attr="disabled"/>
                        <x-button wire:click="changeStatusFrom('{{ $context }}', {{ $image->id }}, 2)" 
                        icon="o-archive-box" 
                        class="btn btn-xs" 
                        tooltip="{{ __('Archive') }}"
                        aria-label="{{ __('Archive') }}"
                        wire:loading.attr="disabled"/>
                    @endif

                    @if($context === 'archived')
                        <x-button wire:click="changeStatusFrom('{{ $context }}', {{ $image->id }}, 1)" 
                        icon="o-check" 
                        class="btn btn-xs" 
                        tooltip="{{ __('Approve') }}"
                        aria-label="{{ __('Approve') }}"
                        wire:loading.attr="disabled"/>
                    @endif

                    <x-button icon="o-trash"
                        class="btn btn-xs btn-danger"
                        tooltip="{{ __('Delete') }}"
                        aria-label="{{ __('Delete') }}"
                        @click="
                            $dispatch('confirm-action', {
                                title: '{{ __('Delete') }}',
                                message: '{{ __('Are you sure you want to delete this image?') }}',
                                confirmText: '{{ __('Yes, delete!') }}',
                                confirmClass: 'bg-red-600 hover:bg-red-700',
                                action: () => $wire.call('deleteFrom', '{{ $context }}', {{ $image->id }})
                            })
                        "
                        wire:loading.attr="disabled"
                    />

                </div>

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
