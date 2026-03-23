<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->string('posting_page_end_title')
                ->default('Thank you!')
                ->after('posting_page_text_visibility');

            $table->string('posting_page_end_text')
                ->default('The submission was successful.')
                ->after('posting_page_end_title');
        });
    }

    public function down(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->dropColumn(['posting_page_end_title', 'posting_page_end_text']);
        });
    }
};
