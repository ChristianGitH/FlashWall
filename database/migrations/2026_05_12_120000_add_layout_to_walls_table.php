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
            $table->integer('layout')->default(0)->after('caption_max_width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->dropColumn('layout');
        });
    }
};
