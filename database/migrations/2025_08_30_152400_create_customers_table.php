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
        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('name');                           // person or company name
            $t->string('type')->default('individual');    // individual|company|agency
            $t->string('email')->nullable()->index();
            $t->string('phone')->nullable()->index();
            $t->string('country_code', 2)->nullable();    // ISO-2 (UZ, IT, US…)
            $t->string('city')->nullable();
            $t->string('preferred_language')->nullable(); // en, ru, it, uz …
            $t->string('source')->nullable();             // direct|website|instagram|referral|agency|gyg|viator
            $t->boolean('marketing_opt_in')->default(false);
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
