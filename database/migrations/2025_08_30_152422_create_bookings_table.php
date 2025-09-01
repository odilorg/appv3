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
       Schema::create('bookings', function (Blueprint $t) {
    $t->id();
    $t->foreignId('customer_id')->constrained(); // your existing table
    $t->foreignId('tour_id')->constrained()->cascadeOnDelete();
    $t->date('start_date');
    $t->date('end_date');
    $t->unsignedSmallInteger('pax_total')->default(1);
    $t->enum('status', ['draft','pending','confirmed','in_progress','completed','cancelled'])->default('draft');
    $t->string('currency', 3)->default('USD');
    $t->decimal('total_price', 12, 2)->default(0);
    $t->text('notes')->nullable();
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
