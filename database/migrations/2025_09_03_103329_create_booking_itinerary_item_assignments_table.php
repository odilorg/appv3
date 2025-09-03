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
        Schema::create('booking_itinerary_item_assignments', function (Blueprint $table) {
           $table->id();

            $table->foreignId('booking_itinerary_item_id')
                ->constrained()
                ->cascadeOnDelete();

            // Polymorphic target: Guide, Driver, Vehicle, Hotel, etc.
            $table->morphs('assignable'); // assignable_type, assignable_id

            // Optional metadata
            $table->string('role')->nullable();          // e.g. guide|driver|vehicle|hotel|other
            $table->unsignedInteger('quantity')->nullable(); // seats/hours/units
            $table->decimal('cost', 12, 2)->nullable();  // internal cost
            $table->string('currency', 3)->default('USD');
            $table->string('status')->nullable();        // planned|confirmed|completed|cancelled
            $table->text('notes')->nullable();

            // Optional time window for that assignment on the item date
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->timestamps();

            $table->index(['booking_itinerary_item_id', 'assignable_type', 'assignable_id'], 'bii_assignable_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_itinerary_item_assignments');
    }
};
