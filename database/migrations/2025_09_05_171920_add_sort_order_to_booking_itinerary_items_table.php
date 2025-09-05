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
           // add after any column that exists in your schema
            if (! Schema::hasColumn('booking_itinerary_items', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('date');
            }
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
