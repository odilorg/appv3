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
        Schema::create('guide_languages', function (Blueprint $table) {
            $table->id(); // important for Repeater tracking
    $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
    $table->foreignId('language_id')->constrained()->cascadeOnDelete();
    $table->string('level'); // A1..C2
    $table->timestamps();

    $table->unique(['guide_id', 'language_id']); // prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_languages');
    }
};
