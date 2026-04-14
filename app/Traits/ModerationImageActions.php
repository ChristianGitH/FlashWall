<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

trait ModerationImageActions
{
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

        // Selecting message depending the action and the number of images
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

        // Dispatch refresh event if provided
        if ($dispatchEvent !== null) {
            $this->dispatch($dispatchEvent);
        }

        // Dispatch single item removal or bulk reset event
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

        // Selecting the toast message
        $message = match($actionType) {
            'deleted' => count($ids) === 1 ? __('Image successfully deleted') : __('Images successfully deleted'),
            default => count($ids) === 1 ? __('Image updated') : __('Images updated'),
        };

        $this->success($message);

        if ($pageCount <= count($ids)) {
            $this->resetPage(pageName: $pageName);
        }

        // Dispatch refresh event if provided
        if ($dispatchEvent !== null) {
            $this->dispatch($dispatchEvent);
        }

        // Dispatch single item removal event
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

        $this->success(__('Image successfully propelled : will be displayed asap'));

        // Supprimer l'image de la sélection côté navigateur
        $this->dispatch('action-on-approved-image', id: $id);

    }



}
