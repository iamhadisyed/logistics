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
        Schema::create('tariffs_pricing_rules_details', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('tariff_pricing_rule_id')->nullable();
            $table->integer('to_zone_id')->nullable();
            $table->decimal('weight_from', 7)->nullable();
            $table->decimal('weight_to', 7)->nullable();
            $table->decimal('margin', 9)->nullable();
            $table->enum('margin_type', ['percentage', 'price'])->nullable();
            $table->string('margin_weight_cost', 20)->nullable();
            $table->string('margin_piece_cost', 20)->nullable();
            $table->decimal('linehaul', 7)->nullable();
            $table->enum('linehaul_type', ['kilogram', 'flat'])->nullable();
            $table->enum('tariff_pricing_type', ['whole', 'multiple'])->nullable();
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
        Schema::dropIfExists('tariffs_pricing_rules_details');
    }
};
