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
        Schema::create('deutschepost_dhl_streetcodes', function (Blueprint $table) {
            $table->integer('id');
            $table->string('street', 45)->nullable();
            $table->string('zipcode', 45)->nullable();
            $table->integer('street_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deutschepost_dhl_streetcodes');
    }
};
