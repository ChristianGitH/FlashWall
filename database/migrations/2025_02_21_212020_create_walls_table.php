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
            $table->integer('background_choice')->default(0);
            $table->string('background_color')->default('#f7a6d5');
            $table->string('background_image')->default('N3uG8CaldrDo2jTEv1Foe9GZsPOb9WwglQ3dDR9M.jpg');
            $table->integer('caption_max_width')->default(50);
            $table->integer('caption_position')->default(1);
            $table->integer('caption_font_size')->default(16);;
            $table->integer('vertical_borders_width')->default(20);
            $table->integer('horizontal_borders_width')->default(10);
            $table->integer('duration')->default(2);
            $table->string('caption_font_color')->default('#000000');
            $table->string('caption_background_color')->default('#ffffff');
            $table->integer('caption_background_opacity')->default(70);
            $table->string('caption_max_characters')->default(255);
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
