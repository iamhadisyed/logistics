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
        Schema::create('carton_pallet_numbers', function (Blueprint $table) {
            $table->integer('id');
            $table->string('type', 3)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('start_number', 45)->nullable();
            $table->string('end_number', 45)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->integer('userid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carton_pallet_numbers');
    }
};
