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
        Schema::create('tariffs_details', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->integer('tariffs_id');
            $table->unsignedInteger('from_zone_id');
            $table->unsignedInteger('to_zone_id');
            $table->decimal('weight_from', 7);
            $table->decimal('weight_to', 7);
            $table->decimal('weight_cost', 9);
            $table->decimal('piece_cost', 5);
            $table->string('formula')->nullable()->default('Q  ( ITMCHR + REG ) + W  CHRG')->comment('Formulla for calculation ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs_details');
    }
};
