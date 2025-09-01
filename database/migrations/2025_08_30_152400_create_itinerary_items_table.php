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
        // 2025_08_30_000002_create_itinerary_items_table.php
Schema::create('itinerary_items', function (Blueprint $t) {
    $t->id();
    $t->foreignId('tour_id')->constrained()->cascadeOnDelete();
    $t->foreignId('parent_id')->nullable()->constrained('itinerary_items')->nullOnDelete();
    $t->enum('type', ['day','stop'])->default('day');
    $t->unsignedInteger('sort_order')->default(0);
    $t->string('title');
    $t->longText('description')->nullable();
    $t->time('default_start_time')->nullable();
    $t->unsignedInteger('duration_minutes')->nullable();
    $t->json('meta')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_items');
    }
};
