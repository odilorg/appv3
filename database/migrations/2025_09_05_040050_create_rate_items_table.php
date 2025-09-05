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
        Schema::create('rate_items', function (Blueprint $table) {
             $table->id();

            // aligns with your transport modes
            $table->enum('mode', ['road', 'air', 'rail'])->index();

            // machine-stable identifier (unique, uppercase snake style recommended)
            $table->string('code', 80)->unique();

            // human label
            $table->string('name', 150);

            // default charging unit for this service
            $table->enum('default_unit', ['flat', 'per_hour', 'per_km', 'per_ticket'])->index();

            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            // helpful composite index for lookups
            $table->index(['mode', 'default_unit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_items');
    }
};
