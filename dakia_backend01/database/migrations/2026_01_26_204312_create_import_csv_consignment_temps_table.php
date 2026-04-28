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
        Schema::create('import_csv_consignment_temps', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->date('date_added')->nullable();
            $table->string('shipper_country_iso', 50)->nullable();
            $table->string('receiver_country_iso', 50)->nullable();
            $table->string('service_code', 50)->nullable();
            $table->string('order_reference', 50)->nullable();
            $table->string('shipper_company', 50)->nullable();
            $table->string('shipper_contact', 50)->nullable();
            $table->string('shipper_email', 50)->nullable();
            $table->string('shipper_telephone', 50)->nullable();
            $table->string('shipper_address_line_1', 50)->nullable();
            $table->string('shipper_address_line_2', 50)->nullable();
            $table->string('shipper_address_line_3', 50)->nullable();
            $table->string('shipper_city', 50)->nullable();
            $table->string('shipper_state', 50)->nullable();
            $table->string('shipper_postcode', 50)->nullable();
            $table->string('receiver_company', 50)->nullable();
            $table->string('receiver_contact', 50)->nullable();
            $table->string('receiver_email', 50)->nullable();
            $table->string('receiver_telephone', 50)->nullable();
            $table->string('receiver_address_line_1', 50)->nullable();
            $table->string('receiver_address_line_2', 50)->nullable();
            $table->string('receiver_address_line_3', 50)->nullable();
            $table->string('receiver_city', 50)->nullable();
            $table->string('receiver_state', 50)->nullable();
            $table->string('receiver_postcode', 50)->nullable();
            $table->string('reference', 50)->nullable();
            $table->decimal('items_value', 10)->nullable();
            $table->string('items_currency', 5)->nullable();
            $table->string('item_type', 50)->nullable();
            $table->text('note')->nullable();
            $table->text('description')->nullable();
            $table->string('bag_number', 50)->nullable();
            $table->string('tracking_number', 20)->nullable();
            $table->string('mawb_number', 50)->nullable();
            $table->string('flight_number', 50)->nullable();
            $table->enum('status', ['0', '1'])->nullable()->default('0');
            $table->enum('is_complete', ['0', '1'])->nullable()->default('0');
            $table->string('batch_number', 50)->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->text('message')->nullable();
            $table->text('weight')->nullable();
            $table->text('length')->nullable();
            $table->text('height')->nullable();
            $table->text('width')->nullable();
            $table->text('itemvalue')->nullable();
            $table->string('parcel_item_desc')->nullable();
            $table->string('parcel_item_sku')->nullable();
            $table->string('parcel_item_url')->nullable();
            $table->integer('parcel_item_quantity')->nullable();
            $table->decimal('parcel_item_value', 10)->nullable();
            $table->decimal('parcel_item_weight', 10)->nullable();
            $table->string('parcel_item_hs_code', 50)->nullable();
            $table->string('parcel_item_manufacture_country', 50)->nullable();
            $table->string('eori_number', 50)->nullable();
            $table->string('vat_number', 50)->nullable();
            $table->string('ioss_number', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_csv_consignment_temps');
    }
};
