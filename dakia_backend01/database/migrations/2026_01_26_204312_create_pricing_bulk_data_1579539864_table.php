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
        Schema::create('pricing_bulk_data_1579539864', function (Blueprint $table) {
            $table->integer('id');
            $table->string('tracking_number', 45)->nullable();
            $table->string('hawb', 45)->nullable();
            $table->string('charges_reference', 45)->nullable();
            $table->decimal('basic_charges', 10)->nullable();
            $table->decimal('fuel_charges', 10)->nullable();
            $table->decimal('additional_charges', 10)->nullable();
            $table->decimal('ndx', 10)->nullable();
            $table->decimal('ddp', 10)->nullable();
            $table->decimal('remote_area_charges', 10)->nullable();
            $table->decimal('handling_charges', 10)->nullable();
            $table->decimal('reference', 10)->nullable();
            $table->decimal('linehaul', 10)->nullable();
            $table->decimal('address_correction', 10)->nullable();
            $table->decimal('airline_handling', 10)->nullable();
            $table->decimal('clearance', 10)->nullable();
            $table->decimal('collections', 10)->nullable();
            $table->decimal('ddp_admin_fee', 10)->nullable();
            $table->decimal('ddp_charge', 10)->nullable();
            $table->decimal('delivery', 10)->nullable();
            $table->decimal('dispatch', 10)->nullable();
            $table->decimal('labour', 10)->nullable();
            $table->decimal('other', 10)->nullable();
            $table->decimal('out_of_gauge', 10)->nullable();
            $table->decimal('over_weight', 10)->nullable();
            $table->decimal('ras', 10)->nullable();
            $table->decimal('redelivery', 10)->nullable();
            $table->decimal('label_charges', 10)->nullable();
            $table->decimal('vat', 10)->nullable();
            $table->decimal('discount', 10)->nullable();
            $table->decimal('mobile_tracking', 10)->nullable();
            $table->string('user_account', 45)->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('is_complete')->nullable();
            $table->string('message')->nullable();
            $table->string('batch_number', 45)->nullable();
            $table->string('currency', 3)->nullable();
            $table->text('data_result')->nullable();
            $table->tinyInteger('data_result_count')->nullable();
            $table->timestamp('date_created')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_bulk_data_1579539864');
    }
};
