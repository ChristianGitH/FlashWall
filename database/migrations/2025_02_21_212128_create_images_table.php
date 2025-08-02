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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wall_id')->constrained('walls')->onDelete('cascade');
            $table->string('name');
            $table->string('thumb');
            $table->string('caption')->nullable();
            $table->integer('status')->default(0); // 0 = unprocessed. 1 = approved. 2 = archived.
            $table->string('visitor_token');
            $table->integer('display_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
