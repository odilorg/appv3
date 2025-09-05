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
        Schema::create('individual_profiles', function (Blueprint $table) {
             // Strict 1–1 with customers: PK = customer_id
            $table->unsignedBigInteger('customer_id')->primary();

            $table->date('dob')->nullable();
            $table->string('nationality', 120)->nullable();
            $table->string('passport_number', 120)->nullable();
            $table->date('passport_expiry')->nullable();

            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('cascade'); // delete profile with customer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_profiles');
    }
};
