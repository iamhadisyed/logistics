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
        Schema::create('proforma_invoice_biilings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignment_id');
            $table->string('billing_company', 100)->nullable();
            $table->string('billing_contact', 100)->nullable();
            $table->string('billing_address_line_1', 50)->nullable();
            $table->string('billing_address_line_2', 50)->nullable();
            $table->string('billing_address_line_3', 50)->nullable();
            $table->string('billing_city', 50)->nullable();
            $table->string('billing_country', 50)->nullable();
            $table->string('billing_postcode', 10)->nullable();
            $table->string('billing_telephone', 20)->nullable();
            $table->string('payment_terms', 45)->nullable();
            $table->string('export_type', 45)->nullable();
            $table->string('comments', 200)->nullable();
            $table->string('delivery_terms', 45)->nullable();
            $table->string('link_file', 200)->nullable();
            $table->string('payer_vat', 45)->nullable();
            $table->string('harm_comm_code', 45)->nullable();
            $table->string('export', 45)->nullable();
            $table->string('invoice_type', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_invoice_biilings');
    }
};
