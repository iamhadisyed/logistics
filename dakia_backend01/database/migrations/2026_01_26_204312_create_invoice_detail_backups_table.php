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
        Schema::create('invoice_detail_backups', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignment_id')->nullable();
            $table->string('invoice_no', 50)->nullable();
            $table->string('hawb', 25)->nullable();
            $table->decimal('basic_charges', 10)->nullable();
            $table->decimal('fuel_charges', 10)->nullable();
            $table->decimal('additional_charges', 10)->nullable();
            $table->decimal('remote_area_charge', 10)->nullable();
            $table->decimal('on_farword_charges', 10)->nullable();
            $table->decimal('ndx', 10)->nullable();
            $table->decimal('ddp', 10)->nullable();
            $table->decimal('extra', 10)->nullable();
            $table->decimal('hv', 11)->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->decimal('agent_basic_charges', 10)->nullable();
            $table->decimal('agent_fuel_charges', 10)->nullable();
            $table->decimal('agent_additional_charges', 10)->nullable();
            $table->decimal('agent_remote_area_charge', 10)->nullable();
            $table->decimal('agent_on_farword_charges', 10)->nullable();
            $table->decimal('agent_ndx', 10)->nullable();
            $table->decimal('agent_ddp', 10)->nullable();
            $table->decimal('agent_extra', 10)->nullable();
            $table->decimal('agent_amount', 10)->nullable();
            $table->decimal('agent_linehaul_cost', 10)->nullable();
            $table->decimal('agent_handling_charges', 10)->nullable();
            $table->string('reference', 350)->nullable();
            $table->integer('quotation_id')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('added_by', 15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_detail_backups');
    }
};
