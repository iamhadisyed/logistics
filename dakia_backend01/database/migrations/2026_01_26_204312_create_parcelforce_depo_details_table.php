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
        Schema::create('parcelforce_depo_details', function (Blueprint $table) {
            $table->integer('id');
            $table->string('depo_name', 200)->nullable();
            $table->string('depo_short_name', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('route_number', 45)->nullable();
            $table->string('pfw_ect', 45)->nullable();
            $table->string('pfw_lat', 45)->nullable();
            $table->string('pfw_lct', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelforce_depo_details');
    }
};
