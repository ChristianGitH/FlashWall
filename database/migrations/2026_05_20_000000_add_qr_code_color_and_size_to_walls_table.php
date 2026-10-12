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
            $table->string('qr_code_color')->default('#000000')->after('qr_code_position');
            $table->unsignedTinyInteger('qr_code_size')->default(12)->after('qr_code_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->dropColumn(['qr_code_color', 'qr_code_size']);
        });
    }
};
