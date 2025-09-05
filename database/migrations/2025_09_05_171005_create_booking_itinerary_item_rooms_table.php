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
        Schema::create('booking_itinerary_item_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_itinerary_item_id')
                ->constrained('booking_itinerary_items')
                ->cascadeOnDelete();
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('rate', 10, 2)->nullable(); // optional: per-room nightly rate or agreed price
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['booking_itinerary_item_id', 'room_id'], 'biir_item_room_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_itinerary_item_rooms');
    }
};
