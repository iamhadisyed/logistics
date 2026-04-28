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
        Schema::create('tariffs_logs', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('old_id')->nullable();
            $table->unsignedInteger('courier_service_id');
            $table->unsignedInteger('collection_rateband_id');
            $table->unsignedInteger('destination_rateband_id');
            $table->integer('collection_postcode_group_id');
            $table->integer('destination_postcode_group_id')->nullable();
            $table->decimal('weight_from', 7);
            $table->decimal('weight_to', 7);
            $table->decimal('tariff', 9);
            $table->decimal('add_unit_cost', 5);
            $table->decimal('unit_size', 5)->default(0);
            $table->decimal('extra_tariff', 7)->nullable();
            $table->decimal('extra_add_unit_cost', 7)->nullable();
            $table->integer('orderq')->default(0);
            $table->tinyInteger('active')->nullable()->default(1);
            $table->char('deletedq', 1)->nullable()->default('N');
            $table->dateTime('added_on')->nullable();
            $table->string('added_by', 100)->nullable();
            $table->dateTime('changed_on')->nullable();
            $table->string('changed_by', 100)->nullable();
            $table->string('customer_id');
            $table->string('formula', 100)->nullable()->default('Q * ( ITMCHR + REG ) + W * CHRG')->comment('Formulla for calculation ');
            $table->date('log_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs_logs');
    }
};
