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
       Schema::create('booking_item_suppliers', function (Blueprint $t) {
    $t->id();
    $t->foreignId('booking_itinerary_item_id')->constrained()->cascadeOnDelete();
    $t->morphs('supplier'); // supplier_type, supplier_id
    $t->string('role')->index(); // driver, guide, hotel, vehicle, restaurant
    $t->unsignedInteger('qty')->nullable();
    $t->decimal('unit_price', 12, 2)->nullable();
    $t->string('currency', 3)->nullable();
    $t->enum('status', ['requested','confirmed','completed','cancelled'])->default('requested');
    $t->text('notes')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_item_suppliers');
    }
};
