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
        Schema::create('transport_types', function (Blueprint $table) {
            $table->id();
            // keep mode aligned with your existing enums
            $table->enum('mode', ['road','air','rail'])->index();
            $table->string('code')->unique();     // e.g. SEDAN, MINIBUS, BUS, AIR, RAIL
            $table->string('name');               // e.g. Sedan, Minibus, Bus, Air, Rail
            $table->unsignedSmallInteger('capacity')->nullable(); // optional helper
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_types');
    }
};
