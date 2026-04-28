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
        Schema::create('consignment_charges', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('tariff_id')->nullable()->default(0);
            $table->integer('account_id');
            $table->integer('consignment_id');
            $table->integer('invoice_id')->nullable();
            $table->integer('charge_type_id');
            $table->integer('agent_id')->nullable();
            $table->enum('cost_type', ['customer', 'agent', 'purchase_invoice'])->default('customer');
            $table->decimal('cost', 11)->nullable()->default(0);
            $table->string('cost_currency', 3)->nullable();
            $table->decimal('cost_supplier_currency', 11)->nullable();
            $table->string('supplier_currency', 3)->nullable();
            $table->decimal('cost_company_currency', 11)->nullable();
            $table->string('company_currency', 3)->nullable();
            $table->string('description')->nullable();
            $table->string('changes_reference', 110)->nullable();
            $table->integer('added_by');
            $table->timestamp('added_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_charges');
    }
};
