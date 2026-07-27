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
            'name' => 'Demo',
            'stripe_price_id' => null, // No Stripe product for free tier
            'features' => [
                'walls' => 1,
                'images_per_wall' => 20,
                'advanced_settings' => false,
                'advanced_moderation' => false,
                'live_usage_duration' => 0,
            ],
            'description' => 'Create your free account and try Flashwall : build a wall in just a few clicks and see the result.',
            'price' => 'Free',
        ],

        // Standard Plan
        1 => [
            'name' => 'Standard',
            'stripe_price_id' => env('STRIPE_STANDARD_PRICE_ID', 'price_standard'), // Set in .env
            'features' => [
                'walls' => 2,
                'images_per_wall' => 1000,
                'advanced_settings' => false,
                'advanced_moderation' => true,
                'live_usage_duration' => 48,
            ],
            'description' => 'For growing events and projects',
            'price' => '95 €',
        ],

        // Business Plan
        2 => [
            'name' => 'Pro',
            'stripe_price_id' => env('STRIPE_BUSINESS_PRICE_ID', 'price_business'),
            'features' => [
                'walls' => 5,
                'images_per_wall' => 5000,
                'advanced_settings' => true,
                'advanced_moderation' => true,
                'live_usage_duration' => 96,
            ],
            'description' => 'For teams and enterprises',
            'price' => '149 €',
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
