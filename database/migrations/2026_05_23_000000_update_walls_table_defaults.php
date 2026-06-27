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
        Schema::table('walls', function (Blueprint $table) {
            $table->boolean('allow_captions')->default(true)->change();
            $table->boolean('ask_name_submitter')->default(true)->change();
            $table->boolean('require_name_submitter')->default(true)->change();
            $table->boolean('require_avatar_submitter')->default(true)->change();
            $table->boolean('submitter_name_on_wall')->default(true)->change();
            $table->boolean('caption_on_wall')->default(true)->change();
            $table->string('background_color')->default('#f8f8f8')->change();
            $table->integer('caption_max_width')->default(90)->change();
            $table->integer('qr_code_size')->default(17)->change();
            $table->integer('caption_font_size')->default(25)->change();
            $table->integer('margin_top')->default(5)->change();
            $table->integer('margin_bottom')->default(5)->change();
            $table->integer('margin_left')->default(5)->change();
            $table->integer('margin_right')->default(5)->change();
            $table->integer('duration')->default(3)->change();
            $table->integer('layout')->default(2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->boolean('allow_captions')->default(false);
            $table->boolean('ask_name_submitter')->default(false);
            $table->boolean('require_name_submitter')->default(false);
            $table->boolean('require_avatar_submitter')->default(false);
            $table->boolean('submitter_name_on_wall')->default(false);
            $table->boolean('caption_on_wall')->default(false);
            $table->string('background_color')->default('#f8f8f8');
            $table->integer('caption_max_width')->default(50);
            $table->integer('qr_code_size')->default(12)->change();
            $table->integer('caption_font_size')->default(16);
            $table->integer('margin_top')->default(20);
            $table->integer('margin_bottom')->default(10);
            $table->integer('margin_left')->default(10);
            $table->integer('margin_right')->default(10);
            $table->integer('duration')->default(2);
            $table->integer('layout')->default(0);
        });
    }
};
