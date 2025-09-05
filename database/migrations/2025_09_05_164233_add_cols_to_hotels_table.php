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
        Schema::table('hotels', function (Blueprint $table) {
             // category: 1..5 stars (nullable allowed if unknown)
            if (Schema::hasColumn('hotels', 'category')) {
                $table->unsignedTinyInteger('category')->nullable()->change();
            } else {
                $table->unsignedTinyInteger('category')->nullable()->after('address');
            }

            // type: controlled list (string + CHECK)
            if (Schema::hasColumn('hotels', 'type')) {
                $table->string('type', 32)->nullable()->index()->change();
            } else {
                $table->string('type', 32)->nullable()->index()->after('category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            //
        });
    }
};
