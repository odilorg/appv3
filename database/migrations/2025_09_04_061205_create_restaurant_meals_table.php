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
        Schema::create('restaurant_meals', function (Blueprint $table) {
             $table->id();
            $table->foreignId('restaurant_id')
                ->constrained('restaurants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('name');                       // e.g. Coffee, Lunch, Dinner Set
            $table->string('type')->nullable();           // breakfast|lunch|dinner|snack|drink|other
            $table->decimal('price', 12, 2)->default(0);  // monetary value
            $table->string('currency', 3)->default('USD');// USD|UZS|EUR...
            $table->boolean('per_person')->default(true); // true = per person; false = per group
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['restaurant_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_meals');
    }
};
