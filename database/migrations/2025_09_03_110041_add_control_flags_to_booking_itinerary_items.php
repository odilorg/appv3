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
        Schema::table('booking_itinerary_items', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false)->after('tour_itinerary_item_id');
            $table->boolean('is_locked')->default(false)->after('is_custom');
            $table->string('status')->nullable()->after('is_locked'); // planned|confirmed|cancelled
            $table->softDeletes();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_itinerary_items', function (Blueprint $table) {
            //
        });
    }
};
