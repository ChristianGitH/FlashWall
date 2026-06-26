<?php

namespace App\Livewire\Traits;

use App\Services\SubscriptionService;

/**
 * Trait for Livewire components to easily check subscription/feature access
 */
trait HasSubscriptionCheck
{
    /**
     * Check if current user has a specific feature
     */
    public function userHasFeature(string $feature): bool
    {
        return SubscriptionService::hasFeature(auth()->user(), $feature);
    }

    /**
     * Check if user can create a wall
     */
    public function canCreateWall(): bool
    {
        return SubscriptionService::canCreateWall(auth()->user());
    }

    /**
     * Get subscription error message
     */
    public function getSubscriptionError(): string
    {
        return SubscriptionService::getSubscriptionErrorMessage(auth()->user());
    }

    /**
     * Get current user's plan
     */
    public function getUserPlan(): array
    {
        return auth()->user()->currentPlan();
    }

    /**
     * Check if subscription is active
     */
    public function hasActiveSubscription(): bool
    {
        return auth()->user()->hasActiveSubscription();
    }

    /**
     * Check if user is on trial
     */
    public function isOnTrial(): bool
    {
        return auth()->user()->isOnTrial();
    }

    /**
     * Get remaining walls user can create
     */
    public function getRemainingWalls(): int
    {
        return auth()->user()->remainingWalls();
    }
}
