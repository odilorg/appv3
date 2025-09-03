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
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('address')->after('email');
            $table->string('phone01', 50)->after('name');
            $table->string('phone02', 50)->nullable()->after('phone01');
            $table->string('image')->nullable()->after('phone02');
            $table->string('license_number', 80)->nullable()->unique()->after('image');
            $table->date('license_expires_at')->nullable()->after('license_number');
            $table->string('license_image')->nullable()->after('license_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            //
        });
    }
};
