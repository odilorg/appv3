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
        Schema::table('rooms', function (Blueprint $table) {
             $table->foreignId('hotel_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

            $table->string('name');                // e.g. Deluxe Room, Twin Room
            $table->string('code')->nullable();    // internal code, optional
            $table->string('layout')->nullable();  // single|twin|double|triple|suite...
            $table->unsignedSmallInteger('size_m2')->nullable();
            $table->unsignedTinyInteger('max_adults')->default(2);
            $table->unsignedTinyInteger('max_children')->default(0);

            $table->decimal('base_price_usd', 10, 2)->nullable(); // price per night (USD)
            $table->text('description')->nullable();
            $table->json('images')->nullable();    // multiple images

            $table->boolean('is_active')->default(true);


            $table->unique(['hotel_id','name']); // avoid duplicate room names in a hotel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
};
