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
        Schema::create('reamus_destination_stations', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('country_code', 2)->nullable();
            $table->string('postcode_from', 10)->nullable();
            $table->string('postcode_to', 10)->nullable();
            $table->string('product_code', 2)->nullable();
            $table->string('station_id', 10)->nullable();
            $table->string('hub_id', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reamus_destination_stations');
    }
};
