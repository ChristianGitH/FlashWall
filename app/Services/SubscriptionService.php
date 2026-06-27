<?php

namespace App\Services;

use App\Models\User;

/**
 * Subscription/Authorization Service
 * Centralized logic for checking subscriptions and features
 */
class SubscriptionService
{
    /**
     * Check if user can create a new wall
     */
    public static function canCreateWall(User $user): bool
    {
        if (!$user->hasActiveSubscription()) {
            return false;
        }

        return !$user->hasReachedWallLimit();
    }

    /**
     * Check if user has access to a feature
     */
    public static function hasFeature(User $user, string $feature): bool
    {
        return $user->hasFeature($feature);
    }

    /**
     * Get error message for subscription issue
     */
    public static function getSubscriptionErrorMessage(User $user): string
    {
        if (!$user->hasActiveSubscription()) {
            if ($user->subscription_ends_at && $user->subscription_ends_at->isPast()) {
                return 'Your subscription has expired. Please renew to continue.';
            }
            return 'You need an active subscription to access this feature.';
        }

        if ($user->hasReachedWallLimit()) {
            $limit = $user->getFeature('walls');
            return __("You've reached your wall limit of :limit. Please upgrade your plan.", [
                'limit' => $limit,
            ]);
        }

        return __('You do not have permission to access this feature.');
    }

    /**
     * Check if user can perform moderation (advanced feature)
     */
    public static function canModerate(User $user): bool
    {
        return $user->hasFeature('advanced_moderation');
    }

    /**
     * Check if user has API access
     */
    public static function hasApiAccess(User $user): bool
    {
        return $user->hasFeature('api_access');
    }

    /**
     * Get plan upgrade recommendation
     */
    public static function getUpgradeRecommendation(User $user): ?array
    {
        if ($user->plan_level === 0) {
            // Free user trying to create more walls
            $plans = config('plans.plans');
            return $plans[1] ?? null; // Recommend Pro
        }

        if ($user->plan_level === 1) {
            // Pro user trying to do enterprise features
            $plans = config('plans.plans');
            return $plans[2] ?? null; // Recommend Business
        }

        return null;
    }
}
