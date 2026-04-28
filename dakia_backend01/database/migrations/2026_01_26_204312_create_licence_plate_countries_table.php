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
        Schema::create('licence_plate_countries', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('licence_plate_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('range_name', 45)->nullable();
            $table->bigInteger('range_start')->nullable();
            $table->bigInteger('range_end')->nullable();
            $table->bigInteger('next_number')->nullable();
            $table->dateTime('increment_date')->nullable();
            $table->string('prefix', 10)->nullable();
            $table->string('sufix', 10)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('addedby')->nullable();
            $table->integer('updatedby')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licence_plate_countries');
    }
};
