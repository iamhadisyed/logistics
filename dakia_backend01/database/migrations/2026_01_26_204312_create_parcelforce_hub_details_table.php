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
        Schema::create('parcelforce_hub_details', function (Blueprint $table) {
            $table->integer('id');
            $table->string('depo_name', 300)->nullable();
            $table->string('depo_number', 45)->nullable();
            $table->string('mon_hub_24', 45)->nullable();
            $table->string('mon_chute_24', 45)->nullable();
            $table->string('mon_hub_48', 45)->nullable();
            $table->string('mon_chute_48', 45)->nullable();
            $table->string('tue_hub_24', 45)->nullable();
            $table->string('tue_chute_24', 45)->nullable();
            $table->string('tue_hub_48', 45)->nullable();
            $table->string('tue_chute_48', 45)->nullable();
            $table->string('wed_hub_24', 45)->nullable();
            $table->string('wed_chute_24', 45)->nullable();
            $table->string('wed_hub_48', 45)->nullable();
            $table->string('wed_chute_48', 45)->nullable();
            $table->string('thu_hub_24', 45)->nullable();
            $table->string('thu_chute_24', 45)->nullable();
            $table->string('thu_hub_48', 45)->nullable();
            $table->string('thu_chute_48', 45)->nullable();
            $table->string('fri_hub_24', 45)->nullable();
            $table->string('fri_chute_24', 45)->nullable();
            $table->string('fri_hub_48', 45)->nullable();
            $table->string('fri_chute_48', 45)->nullable();
            $table->string('sat_hub_24', 45)->nullable();
            $table->string('sat_chute_24', 45)->nullable();
            $table->string('sat_hub_48', 45)->nullable();
            $table->string('sat_chute_48', 45)->nullable();
            $table->string('sun_hub_24', 45)->nullable();
            $table->string('sun_chute_24', 45)->nullable();
            $table->string('sun_hub_48', 45)->nullable();
            $table->string('sun_chute_48', 45)->nullable();
            $table->string('sat_delivery_hub', 45)->nullable();
            $table->string('sat_delivery_chute', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelforce_hub_details');
    }
};
