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

            $table->foreignId('parent_id')->nullable()->constrained('images')->onDelete('cascade');
            
            $table->string('name');
            $table->string('webp_name');
            $table->string('thumb');
            $table->string('caption')->nullable();
            $table->integer('status')->default(0); // 0 = unprocessed. 1 = approved. 2 = archived.
            $table->string('visitor_token')->nullable();
            $table->foreignId('submitter_id')
                ->nullable()
                ->constrained('submitters')
                ->nullOnDelete(); // If the submitter is deleted, image stays with null id.
            $table->string('submitter_name')->nullable();
            $table->boolean('priority')->default(0);
            $table->boolean('permanent');
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
