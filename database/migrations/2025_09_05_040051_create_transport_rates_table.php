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
        Schema::create('transport_rates', function (Blueprint $table) {
           $table->id();

            $table->foreignId('rate_item_id')
                ->constrained('rate_items')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('transport_type_id')
                ->constrained('transport_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Optional unit override; by default use RateItem->default_unit
            $table->enum('unit', ['flat','per_hour','per_km','per_ticket'])->nullable();

            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('USD'); // ISO 4217, e.g. USD/EUR/UZS

            // Optional validity window
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            // Prevent accidental duplicates for same combo + start date
            $table->unique(['rate_item_id','transport_type_id','currency','valid_from'], 'transport_rates_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_rates');
    }
};
