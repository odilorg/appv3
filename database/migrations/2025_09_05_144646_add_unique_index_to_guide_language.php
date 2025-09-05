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
        Schema::table('guide_language', function (Blueprint $table) {
            // avoid duplicate pairs
            $table->unique(['guide_id', 'language_id'], 'guide_language_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_language', function (Blueprint $table) {
            //
        });
    }
};
