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
        Schema::create('tariffs_pricings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('tariff_id');
            $table->enum('tarif_pricing_type', ['single_tariff', 'whole_tariff'])->nullable();
            $table->integer('to_zone_id')->nullable();
            $table->decimal('weight_from', 7)->nullable();
            $table->decimal('weight_to', 7)->nullable();
            $table->enum('margin_type', ['percentage', 'price'])->nullable();
            $table->decimal('margin', 9)->nullable();
            $table->timestamp('date_added')->useCurrent();
            $table->integer('added_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs_pricings');
    }
};
