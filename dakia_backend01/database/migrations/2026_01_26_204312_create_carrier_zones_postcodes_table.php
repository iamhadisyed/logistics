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
        Schema::create('carrier_zones_postcodes', function (Blueprint $table) {
            $table->unsignedInteger('id')->default(0);
            $table->unsignedInteger('carrier_zone_id');
            $table->string('postcode', 15);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_zones_postcodes');
    }
};
