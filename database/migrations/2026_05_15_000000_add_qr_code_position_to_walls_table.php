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
            if (Schema::hasColumn('walls', 'caption_position')) {
                $table->dropColumn('caption_position');
            }

            $table->string('qr_code_position')->default('bottom-right')->after('caption_max_width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('walls', function (Blueprint $table) {
            $table->integer('caption_position')->default(1)->after('caption_max_width');
            $table->dropColumn('qr_code_position');
        });
    }
};
