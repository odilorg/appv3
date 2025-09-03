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

    // FK (short name)
    $table->unsignedBigInteger('booking_itinerary_item_id');
    $table->foreign('booking_itinerary_item_id', 'bii_assign_bii_fk')
        ->references('id')->on('booking_itinerary_items')
        ->cascadeOnDelete();

    // Polymorphic columns (manual, to control index name)
    $table->string('assignable_type');
    $table->unsignedBigInteger('assignable_id');
    $table->index(['assignable_type', 'assignable_id'], 'bii_assign_idx'); // short index name ✅

    // Optional fields
    $table->string('role')->nullable();
    $table->unsignedInteger('quantity')->nullable();
    $table->decimal('cost', 12, 2)->nullable();
    $table->string('currency', 3)->default('USD');
    $table->string('status')->nullable();
    $table->text('notes')->nullable();
    $table->time('start_time')->nullable();
    $table->time('end_time')->nullable();

    $table->timestamps();
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
