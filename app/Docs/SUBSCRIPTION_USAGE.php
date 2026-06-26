<?php

/**
 * SUBSCRIPTION SYSTEM - USAGE GUIDE
 * 
 * Quick reference for common subscription checks and operations
 */

// ============================================================================
// 1. USER MODEL - Direct access from User instances
// ============================================================================

$user = auth()->user();

// Get user's current plan
$plan = $user->currentPlan();
echo $plan['name']; // "Pro"
echo $plan['features']['walls']; // 5

// Check subscription status
$user->hasActiveSubscription(); // true/false
$user->isOnTrial(); // true/false

// Check specific features
$user->hasFeature('custom_branding'); // true/false
$user->getFeature('walls'); // 5 (or PHP_INT_MAX for unlimited)

// Wall-specific checks
$user->hasReachedWallLimit(); // true/false
$user->remainingWalls(); // 3 remaining

// ============================================================================
// 2. SUBSCRIPTION SERVICE - Static utility methods
// ============================================================================

use App\Services\SubscriptionService;

// Check wall creation permission
if (SubscriptionService::canCreateWall($user)) {
    // Allow wall creation
}

// Check feature access
if (SubscriptionService::hasFeature($user, 'advanced_moderation')) {
    // Show moderation features
}

// Get user-friendly error messages
$error = SubscriptionService::getSubscriptionErrorMessage($user);
// "You've reached your wall limit of 5. Please upgrade your plan."

// Get upgrade recommendation
$upgradeTo = SubscriptionService::getUpgradeRecommendation($user);
// ['name' => 'Business', 'features' => [...]]

// ============================================================================
// 3. IN LIVEWIRE COMPONENTS - Using the trait
// ============================================================================

class CreateWall extends Component
{
    use \App\Livewire\Traits\HasSubscriptionCheck;

    public function createWall()
    {
        if (!$this->canCreateWall()) {
            $this->error($this->getSubscriptionError());
            return;
        }

        // Create wall...
    }

    public function render()
    {
        return view('livewire.create-wall', [
            'plan' => $this->getUserPlan(),
            'remaining' => $this->getRemainingWalls(),
            'onTrial' => $this->isOnTrial(),
        ]);
    }
}

// ============================================================================
// 4. IN VIEWS - Blade template checks
// ============================================================================

@if (auth()->user()->hasFeature('custom_branding'))
    <!-- Show branding options -->
@endif

@if (auth()->user()->hasReachedWallLimit())
    <p>You've reached your limit. <a href="">Upgrade now</a></p>
@else
    <button>Create new wall</button>
@endif

<!-- Show current plan info -->
<p>You're on the {{ auth()->user()->currentPlan()['name'] }} plan</p>
<p>Remaining walls: {{ auth()->user()->remainingWalls() }}</p>

// ============================================================================
// 5. INITIALIZING USER SUBSCRIPTION
// ============================================================================

// When user registers (typically in RegisterController or Livewire component)
$user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => Hash::make($data['password']),
    'plan_level' => config('plans.default_plan_level'), // Usually 0 (free)
]);

// Start a trial period
$user->startTrial(); // Uses config('plans.trial_days'), default 14 days

// Or start with a specific number of days
$user->startTrial(30);

// ============================================================================
// 6. UPGRADING USER PLAN (After Stripe payment via Cashier)
// ============================================================================

// When Stripe webhook confirms payment
$user->upgradePlan(1); // Upgrade to Pro (plan level 1)

// Set subscription expiry (usually 1 year from Stripe)
$user->update([
    'subscription_ends_at' => now()->addYear(),
    'is_subscription_active' => true,
]);

// ============================================================================
// 7. ROUTE MIDDLEWARE - Protect routes
// ============================================================================

// In routes/web.php
Route::middleware('check.wall.creation')->group(function () {
    Route::post('/walls', [WallController::class, 'store']);
});

// Register middleware in app/Http/Kernel.php
protected $routeMiddleware = [
    // ...
    'check.wall.creation' => \App\Http\Middleware\CheckWallCreationLimit::class,
];

// ============================================================================
// 8. CASHIER INTEGRATION - Webhook listener example
// ============================================================================

// When Stripe confirms subscription update:
public function handleSubscriptionUpdated($payload)
{
    $user = User::where('stripe_id', $payload['data']['object']['customer'])->first();
    
    if (!$user) return;

    $planLevel = $this->mapStripePriceToLevel($payload['data']['object']['items']['data'][0]['price']['id']);
    
    $user->update([
        'plan_level' => $planLevel,
        'is_subscription_active' => true,
        'subscription_ends_at' => Carbon::createFromTimestamp($payload['data']['object']['current_period_end']),
    ]);
}

// ============================================================================
// 9. CONFIG FILE REFERENCE - Plans configuration
// ============================================================================

// config/plans.php defines:
// - Plan name and features for each level (0, 1, 2, 3...)
// - Stripe price IDs for each paid plan
// - Trial period duration
// - Default plan level for new users

// Access via:
config('plans.plans.0') // Free plan
config('plans.plans.1') // Pro plan
config('plans.trial_days') // 14 days default
