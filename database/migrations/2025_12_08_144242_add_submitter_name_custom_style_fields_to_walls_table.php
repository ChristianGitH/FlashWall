<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->string('submitter_name_font_color')
                ->default('#000000')
                ->after('caption_font_color');

            $table->integer('submitter_name_font_size')
                ->default(16)
                ->after('submitter_name_font_color');
                
            $table->string('caption_font_unit')
                ->default('px')
                ->after('caption_font_size');
        });
    }

    public function down(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->dropColumn(['posting_page_end_title', 'posting_page_end_text']);
        });
    }
};
