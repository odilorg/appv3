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
       Schema::create('travelers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $t->string('first_name');
            $t->string('last_name')->nullable();
            $t->date('date_of_birth')->nullable();
            $t->string('nationality')->nullable();            // e.g., UZ, IT
            $t->string('passport_number')->nullable();
            $t->date('passport_expires_at')->nullable();
            $t->string('gender')->nullable();                 // optional
            $t->text('notes')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travelers');
    }
};
