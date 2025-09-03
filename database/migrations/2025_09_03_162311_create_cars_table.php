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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('make');               // e.g., Chevrolet
            $table->string('model');              // e.g., Cobalt
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('plate_number')->unique(); // e.g., 30 A 123 AB
            $table->string('color')->nullable();
            $table->string('vin')->nullable()->unique();
            $table->unsignedTinyInteger('seats')->default(4);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
