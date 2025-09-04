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
         Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();           // Air Condition, Desk, Kettle, Pool...
            $table->enum('scope', ['hotel','room']);    // where it applies
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('amenity_hotel', function (Blueprint $table) {
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->primary(['amenity_id','hotel_id']);
        });

        Schema::create('amenity_room', function (Blueprint $table) {
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->primary(['amenity_id','room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
Schema::dropIfExists('amenity_room');
        Schema::dropIfExists('amenity_hotel');
        Schema::dropIfExists('amenities');    }
};
