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
       Schema::create('guides', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('base_city')->nullable()->index();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->json('languages')->nullable();   // ["English","Italian","Russian"]
            $t->boolean('is_active')->default(true)->index();
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
        Schema::dropIfExists('guides');
    }
};
