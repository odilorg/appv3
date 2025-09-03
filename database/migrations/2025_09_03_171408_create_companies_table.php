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
        Schema::create('companies', function (Blueprint $table) {
           $table->id();
            $table->string('name');                           // Required
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('inn')->nullable()->unique();      // Uzbekistan tax ID
            $table->string('account_number')->nullable()->unique();
            $table->string('bank_name')->nullable();
            $table->string('bank_mfo')->nullable();
            $table->string('director_name')->nullable();
            $table->string('logo')->nullable();               // store path or URL
            $table->boolean('is_operator')->default(false);   // tour operator?
            $table->string('license_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
