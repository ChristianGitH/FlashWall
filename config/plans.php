<?php

/**
 * Subscription Plans Configuration
 * 
 * Define all available subscription tiers and their features.
 * The plan_level corresponds to the user's plan_level column in the database.
 */

return [
    'plans' => [
        // Free Plan
        0 => [
            'name' => 'plans.plans.0.name',
            'stripe_price_id' => null, // No Stripe product for free tier
            'features' => [
                'walls' => 1,
                'images_per_wall' => 20,
                'advanced_settings' => false,
                'advanced_moderation' => false,
                'live_usage_duration' => 0,
                'support' => null,
            ],
            'description' => 'plans.plans.0.description',
            'price' => 'plans.plans.0.price',
        ],

        // Standard Plan
        1 => [
            'name' => 'plans.plans.1.name',
            'stripe_price_id' => env('STRIPE_STANDARD_PRICE_ID', 'price_standard'), // Set in .env
            'features' => [
                'walls' => 1,
                'images_per_wall' => 250,
                'advanced_settings' => false,
                'advanced_moderation' => true,
                'live_usage_duration' => 24,
                'support' => 'plans.plans.1.support',
            ],
            'description' => 'plans.plans.1.description',
            'price' => 'plans.plans.1.price',
        ],

        // Premium Plan
        2 => [
            'name' => 'plans.plans.2.name',
            'stripe_price_id' => env('STRIPE_PREMIUM_PRICE_ID', 'price_premium'),
            'features' => [
                'walls' => 2,
                'images_per_wall' => 500,
                'advanced_settings' => true,
                'advanced_moderation' => true,
                'live_usage_duration' => 48,
                'support' => 'plans.plans.2.support',
            ],
            'description' => 'plans.plans.2.description',
            'price' => 'plans.plans.2.price',
        ],
        // Pro Plan
        3 => [
            'name' => 'plans.plans.3.name',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID', 'price_pro'),
            'features' => [
                'walls' => '∞',
                'images_per_wall' => '∞',
                'advanced_settings' => true,
                'advanced_moderation' => true,
                'live_usage_duration' => '∞',
                'support' => 'plans.plans.3.support',
            ],
            'description' => 'plans.plans.3.description',
            'price' => 'plans.plans.3.price',
        ],
    ],

    /**
     * Trial Configuration
     */
    'trial_days' => (int) env('SUBSCRIPTION_TRIAL_DAYS', 14),

    /**
     * Default plan level for new users
     */
    'default_plan_level' => 0, // Free tier
];
