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
        Schema::create('sorter_postcode_zones', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('area', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('zone', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorter_postcode_zones');
    }
};
