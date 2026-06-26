<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Plan level for feature access (0=free, 1=pro, 2=enterprise, etc)
            $table->unsignedTinyInteger('plan_level')->default(0)->after('email_verified_at');
            
            // Trial ends at timestamp
            $table->timestamp('trial_ends_at')->nullable()->after('plan_level');
            
            // Subscription ends at timestamp (for tracking expiry)
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
            
            // Stripe subscription ID (managed by Cashier)
            $table->string('stripe_id')->nullable()->unique()->after('subscription_ends_at');
            
            // To track if subscription is active
            $table->boolean('is_subscription_active')->default(false)->after('stripe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan_level', 'trial_ends_at', 'subscription_ends_at', 'stripe_id', 'is_subscription_active']);
        });
    }
};
