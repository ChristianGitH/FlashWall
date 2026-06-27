<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
 

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'email',
        'password',
        'plan_level',
        'trial_ends_at',
        'subscription_ends_at',
        'stripe_id',
        'is_subscription_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations : One user has many walls.
    public function walls(): HasMany
    {
        return $this->hasMany(Wall::class);
    }

    // Accessor for display name, which prioritizes first_name over name, then null.
    public function getDisplayNameAttribute()
    {
        return $this->first_name 
            ?: ($this->name ?: null);
    }

    /**
     * Get the current plan for this user
     */
    public function currentPlan(): array
    {
        $plans = config('plans.plans');
        return $plans[$this->plan_level] ?? $plans[0];
    }

    /**
     * Check if user is on a trial period
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is active (not expired)
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->plan_level === 0) {
            return true; // Free tier is always "active"
        }

        // Trial is active
        if ($this->isOnTrial()) {
            return true;
        }

        // Paid subscription is active
        return $this->is_subscription_active && 
               ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture());
    }

    /**
     * Start trial period for this user
     */
    public function startTrial(int $days = null): void
    {
        $days = $days ?? config('plans.trial_days');
        $this->trial_ends_at = now()->addDays($days);
        $this->save();
    }

    /**
     * Check if user has access to a specific feature
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->hasActiveSubscription()) {
            return false; // Expired subscription loses access
        }

        $plan = $this->currentPlan();
        return $plan['features'][$feature] ?? false;
    }

    /**
     * Get a feature value (useful for numeric limits)
     * Returns the feature value or 0 if not available
     */
    public function getFeature(string $feature): int|float|bool
    {
        if (!$this->hasActiveSubscription()) {
            return 0; // Expired subscription has no features
        }

        $plan = $this->currentPlan();
        return $plan['features'][$feature] ?? 0;
    }

    /**
     * Check if user has reached their wall limit
     */
    public function hasReachedWallLimit(): bool
    {
        $limit = $this->getFeature('walls');
        if ($limit === PHP_INT_MAX) {
            return false; // Unlimited
        }
        return $this->walls()->count() >= $limit;
    }

    /**
     * Get remaining walls user can create
     */
    public function remainingWalls(): int
    {
        $limit = $this->getFeature('walls');
        if ($limit === PHP_INT_MAX) {
            return PHP_INT_MAX;
        }
        return max(0, $limit - $this->walls()->count());
    }

    /**
     * Upgrade user to a new plan level
     */
    public function upgradePlan(int $planLevel): void
    {
        if (!isset(config('plans.plans')[$planLevel])) {
            throw new \InvalidArgumentException("Plan level {$planLevel} does not exist");
        }

        $this->plan_level = $planLevel;
        $this->trial_ends_at = null; // Clear trial when upgrading
        $this->is_subscription_active = true;
        $this->save();
    }

    /**
     * Cancel subscription and revert to free plan
     */
    public function cancelSubscription(): void
    {
        $this->plan_level = 0; // Revert to free
        $this->is_subscription_active = false;
        $this->subscription_ends_at = now();
        $this->save();
    }
}
