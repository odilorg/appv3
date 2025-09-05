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
        Schema::create('booking_itinerary_items', function (Blueprint $t) {
    $t->id();
    $t->foreignId('booking_id')->constrained()->cascadeOnDelete();
    $t->foreignId('tour_itinerary_item_id')->nullable()->constrained('itinerary_items')->cascadeOnDelete();
    $t->date('date'); // concrete calendar day
    $t->enum('type', ['day','stop']);
    $t->unsignedInteger('sort_order')->default(0);
    $t->string('title');
    $t->longText('description')->nullable();
    $t->time('planned_start_time')->nullable();
    $t->unsignedInteger('planned_duration_minutes')->nullable();
    $t->json('meta')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_itinerary_items');
    }
};
