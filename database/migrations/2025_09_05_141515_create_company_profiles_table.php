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
        Schema::create('company_profiles', function (Blueprint $table) {
           // Strict 1–1 with customers: PK = customer_id
            $table->unsignedBigInteger('customer_id')->primary();

            // Link to existing companies table (REUSED entity)
            $table->unsignedBigInteger('company_id');

            $table->boolean('is_agency')->default(false)->index();
            $table->decimal('commission_rate', 5, 2)->nullable(); // e.g. 12.50
            $table->string('account_manager', 255)->nullable();

            $table->timestamps();

            // FKs
            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('cascade');

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->onDelete('cascade'); // if a company is deleted, drop this profile
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
