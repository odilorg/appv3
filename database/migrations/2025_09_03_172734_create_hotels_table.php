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
        Schema::create('hotels', function (Blueprint $table) {
             $table->id();

            $table->foreignId('city_id')
                ->constrained('cities')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('name');
            $table->string('address')->nullable();

            // Star category (1–5). Keep nullable to allow “unrated”.
            $table->unsignedTinyInteger('category')->nullable()->comment('Stars 1–5');

            // E.g. hotel, guesthouse, hostel, bnb, boutique, etc.
            $table->string('type')->nullable();

            $table->longText('description')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Store multiple image paths as JSON
            $table->json('images')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
