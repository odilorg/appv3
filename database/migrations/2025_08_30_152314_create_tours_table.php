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
        Schema::create('tours', function (Blueprint $t) {
    $t->id();
    $t->string('title');
    $t->unsignedTinyInteger('duration_days');
    $t->string('short_description')->nullable();
    $t->longText('long_description')->nullable();
    $t->boolean('is_active')->default(true);
    $t->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
