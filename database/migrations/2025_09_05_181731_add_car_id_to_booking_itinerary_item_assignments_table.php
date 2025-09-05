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
        Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
            // Add after the morph columns for readability
            if (! Schema::hasColumn('booking_itinerary_item_assignments', 'car_id')) {
                $table->foreignId('car_id')
                    ->nullable()
                    ->after('assignable_id')
                    ->constrained('cars')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('booking_itinerary_item_assignments', 'room_id')) {
                $table->foreignId('room_id')
                    ->nullable()
                    ->after('car_id')
                    ->constrained('rooms')
                    ->restrictOnDelete();
            }

            if (! Schema::hasColumn('booking_itinerary_item_assignments', 'restaurant_meal_id')) {
                $table->foreignId('restaurant_meal_id')
                    ->nullable()
                    ->after('room_id')
                    ->constrained('restaurant_meals')
                    ->restrictOnDelete();
            }
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_itinerary_item_assignments', function (Blueprint $table) {
            //
        });
    }
};
