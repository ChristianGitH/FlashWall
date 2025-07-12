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
        Schema::create('walls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->boolean('captions')->default(false);
            $table->boolean('moderation')->default(false);
            $table->string('max_images_user')->nullable();
            $table->integer('background_choice');
            $table->string('background_color');
            $table->string('background_image');
            $table->integer('caption_max_width');
            $table->integer('caption_position');
            $table->integer('vertical_borders_width');
            $table->integer('horizontal_borders_width');
            $table->integer('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walls');
    }
};
