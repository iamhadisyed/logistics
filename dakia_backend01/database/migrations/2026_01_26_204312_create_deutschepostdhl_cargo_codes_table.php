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
        Schema::create('deutschepostdhl_cargo_codes', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('start_postcode')->nullable();
            $table->integer('end_postcode')->nullable();
            $table->integer('cargo_code')->nullable();
            $table->string('municipality_name', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deutschepostdhl_cargo_codes');
    }
};
