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
        Schema::create('flight_infos', function (Blueprint $table) {
            $table->integer('id');
            $table->string('flight_number', 45)->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('destination_country_id')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->enum('current_status', ['in_tranist', 'arrived_lhr', 'clearance_in_process', 'collection_in_process'])->nullable();
            $table->enum('status', ['not_assigned', 'assigned', 'in_warehouse'])->nullable();
            $table->string('address_line_1', 45)->nullable();
            $table->string('address_line_2', 100)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('signature', 45)->nullable();
            $table->string('carrier', 45)->nullable();
            $table->decimal('carriage_value', 18)->nullable();
            $table->decimal('custom_value', 18)->nullable();
            $table->decimal('insurance_amount', 18)->nullable();
            $table->string('currency', 45)->nullable();
            $table->string('connecting_flight_number', 45)->nullable();
            $table->string('weight_type', 2)->nullable();
            $table->string('rate_charge', 45)->nullable();
            $table->string('iata_code', 45)->nullable();
            $table->string('departure_airport', 45)->nullable();
            $table->string('phone_number', 45)->nullable();
            $table->string('shipper_co', 100)->nullable();
            $table->string('consignee_co', 100)->nullable();
            $table->string('arrival_airport', 45)->nullable();
            $table->integer('account_id')->nullable();
            $table->string('shippers_name', 45)->nullable();
            $table->string('shippers_addressline1', 100)->nullable();
            $table->string('shippers_addressline2', 100)->nullable();
            $table->string('accounting_reference', 100)->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('rate_change', 50)->nullable();
            $table->string('low_value_manifest')->nullable();
            $table->string('high_value_manifest')->nullable();
            $table->string('invoice')->nullable();
            $table->string('files_hv')->nullable();
            $table->string('hscodes', 5000)->nullable();
            $table->string('etd', 20)->nullable();
            $table->string('eta', 20)->nullable();
            $table->string('shed', 45)->nullable();
            $table->string('files_lv')->nullable();
            $table->string('cleared', 45)->nullable();
            $table->string('comments', 45)->nullable();
            $table->string('weight', 45)->nullable();
            $table->string('pieces', 45)->nullable();
            $table->bigInteger('created_by');
            $table->boolean('is_delete')->default(false);
            $table->boolean('is_closed')->nullable()->default(false);
            $table->string('account_number', 50)->nullable();
            $table->string('airway_bill', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('currancy', 50)->nullable();
            $table->string('files')->nullable();
            $table->string('destination_company')->nullable();
            $table->string('destination_phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_infos');
    }
};
