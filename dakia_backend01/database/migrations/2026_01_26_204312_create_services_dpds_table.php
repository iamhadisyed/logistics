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
        Schema::create('services_dpds', function (Blueprint $table) {
            $table->integer('id');
            $table->string('2_digit_service_code', 2)->nullable();
            $table->string('3_digit_service_code', 3)->nullable();
            $table->string('dpd_product_desc', 45)->nullable();
            $table->string('dpd_label_service', 45)->nullable();
            $table->string('ilk_product_desc', 45)->nullable();
            $table->string('ilk_alternative_service_desc', 45)->nullable();
            $table->string('premium', 45)->nullable();
            $table->string('sec_dpd', 45)->nullable();
            $table->string('sec_ilk', 45)->nullable();
            $table->integer('ilk_max_parcels_per_con')->nullable();
            $table->integer('ilk_max_weight_per_parcel')->nullable();
            $table->integer('dpd_max_parcels_per_con')->nullable();
            $table->integer('dpd_max_weight_per_parcel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_dpds');
    }
};
