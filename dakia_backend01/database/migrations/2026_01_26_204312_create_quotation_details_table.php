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
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->bigInteger('shipping_from')->nullable();
            $table->bigInteger('shipping_to')->nullable();
            $table->bigInteger('carrier_id')->nullable();
            $table->bigInteger('service_id')->nullable();
            $table->bigInteger('account_id')->nullable();
            $table->enum('price_type', ['user', 'agent', 'manual'])->nullable()->default('user');
            $table->integer('pieces')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->longText('dimensions')->nullable();
            $table->decimal('volumn_weight', 10, 3)->nullable();
            $table->decimal('basic_charge', 10)->nullable()->default(0);
            $table->decimal('vat_charge', 10)->nullable()->default(0);
            $table->decimal('extra_charge', 10)->nullable()->default(0);
            $table->decimal('sub_total', 10)->nullable()->default(0);
            $table->decimal('discount', 10)->nullable()->default(0);
            $table->string('user_email', 45)->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('total_charge', 10)->nullable()->default(0);
            $table->text('remark')->nullable();
            $table->enum('status', ['active', 'inactive', 'remove'])->nullable()->default('active');
            $table->string('city', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->float('conversionrate')->nullable();
            $table->string('pdf', 99)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->bigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_details');
    }
};
