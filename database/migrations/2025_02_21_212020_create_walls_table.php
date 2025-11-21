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
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->boolean('allow_captions')->default(false);
            $table->boolean('moderation')->default(false);
            $table->integer('max_images_submitter')->default(2);
            $table->integer('capture_mode')->default(0);
            $table->boolean('ask_name_submitter')->default(false);
            $table->boolean('require_name_submitter')->default(false);
            $table->boolean('ask_email_submitter')->default(false);
            $table->boolean('require_email_submitter')->default(false);
            $table->boolean('require_avatar_submitter')->default(false);
            $table->boolean('submitter_name_on_wall')->default(false);
            $table->boolean('caption_on_wall')->default(false);
            $table->integer('background_choice')->default(0); // 0=color, 1=image
            $table->string('background_color')->default('#f7a6d5');
            $table->string('background_image')->default('default_background.jpg');
            $table->integer('caption_max_width')->default(50);
            $table->integer('caption_position')->default(1); // 0=bellow image, 1=on image
            $table->string('caption_font')->nullable();
            $table->integer('caption_font_size')->default(16);
            $table->integer('margin_top')->default(20);
            $table->integer('margin_bottom')->default(10);
            $table->integer('margin_left')->default(10);
            $table->integer('margin_right')->default(10);
            $table->integer('duration')->default(2);
            $table->string('transition')->default('fade');
            $table->string('caption_font_color')->default('#000000');
            $table->string('caption_background_color')->default('#ffffff');
            $table->integer('caption_background_opacity')->default(70);
            $table->integer('caption_max_characters')->default(255);
            $table->string('posting_page_text')->default('Share your selfie');
            $table->boolean('posting_page_text_visibility')->default(true);
            $table->string('posting_page_font')->nullable();
            $table->string('posting_page_buttons_color')->nullable();
            $table->string('posting_page_buttons_font_color')->nullable();
            $table->string('posting_page_logo')->default("posting_page_default_logo.png");
            $table->integer('posting_page_logo_visibility')->default(0);
            $table->string('posting_page_background_color')->default("#f8f8f8");
            $table->string('posting_page_background_image')->default("posting_page_default_background.png");
            $table->integer('posting_page_background_choice')->default(0); // 0=color, 1=image
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
