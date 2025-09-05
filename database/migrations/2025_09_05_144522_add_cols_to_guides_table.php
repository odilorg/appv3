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
        Schema::table('guides', function (Blueprint $table) {
             // if columns already exist, adjust accordingly
            if (! Schema::hasColumn('guides', 'type')) {
                $table->string('type', 20)->default('individual')->index()->after('name');
            }
            if (! Schema::hasColumn('guides', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guides', function (Blueprint $table) {
            //
        });
    }
};
