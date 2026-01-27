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
        Schema::create('consignments', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->integer('agent_id')->nullable()->default(51);
            $table->integer('user_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('customized_service_id')->nullable()->default(0);
            $table->integer('warehouse_user_id')->nullable()->comment('User Id for those shipment which created by warehouse  perosn');
            $table->integer('warehouse_id')->nullable();
            $table->bigInteger('sales_pot_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->integer('credit_id')->nullable();
            $table->integer('is_invoiced')->nullable()->default(0);
            $table->enum('invoice_type', ['INV', 'MNI'])->nullable();
            $table->integer('shipment_status')->nullable()->default(0)->comment('4 digit code for shipment status which also belong');
            $table->enum('shipment_type', ['C', 'D', 'P', 'DO'])->nullable()->default('D')->comment('C for collection 
D for dispatched
P product, DO dropoff');
            $table->string('awb', 30)->nullable();
            $table->string('consignment_status', 20)->nullable();
            $table->string('return_awb', 30)->nullable();
            $table->string('hawb', 40);
            $table->string('mawb', 40)->nullable();
            $table->string('service_name', 50)->nullable();
            $table->string('reference', 20)->nullable();
            $table->timestamp('date_created')->nullable()->useCurrent();
            $table->integer('date_label_created')->nullable();
            $table->integer('date_booked')->nullable();
            $table->integer('date_delivered')->nullable();
            $table->integer('is_customer_manifested')->nullable()->default(0);
            $table->string('booked_file_id', 50)->default('0');
            $table->string('company', 100)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('address_line_1', 50)->nullable();
            $table->string('address_line_2', 50)->nullable();
            $table->string('address_line_3', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 45)->nullable();
            $table->string('postcode', 15)->nullable();
            $table->integer('country_id')->nullable();
            $table->string('telephone', 17)->nullable();
            $table->unsignedInteger('number_pieces')->nullable();
            $table->string('weight_type', 2)->nullable()->default('PP');
            $table->decimal('weight', 8, 3)->unsigned()->nullable();
            $table->decimal('update_weight', 8, 3)->nullable();
            $table->decimal('fake_weight', 8, 3)->nullable()->comment('sending 20-30% decrease weight to the carrier(Hungary Post) as compared to the actual weight.');
            $table->decimal('charge_weight', 8, 3)->nullable();
            $table->decimal('vol_weight', 8, 3)->nullable()->comment('vol weight will updated by scanning system ');
            $table->integer('vol_demonimator')->nullable()->comment('Denominator will be updated from service table on consignment add');
            $table->enum('hv_lv', ['L', 'H', 'M'])->nullable();
            $table->string('description')->nullable();
            $table->string('notes')->nullable();
            $table->decimal('value', 10)->unsigned()->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('sender_name', 45);
            $table->string('username', 45)->nullable();
            $table->integer('sender_checked')->nullable()->default(0);
            $table->string('message', 400)->nullable();
            $table->string('sorter_image', 200)->nullable();
            $table->string('label_file')->nullable();
            $table->integer('is_doc')->nullable()->default(0);
            $table->string('email', 45)->nullable();
            $table->string('itemtype', 60)->nullable();
            $table->string('routing_code', 3)->nullable();
            $table->string('routing_code_eur', 45)->nullable();
            $table->string('other_routing_code', 250)->nullable();
            $table->integer('billing_hold')->nullable()->default(0)->comment('Account use this field for making billing hold as YES and NO');
            $table->integer('send_courier_data')->nullable()->default(0)->comment('Set 1 when data send to all courier');
            $table->integer('remote_charges')->nullable()->default(0);
            $table->integer('reinvoices')->nullable()->default(0);
            $table->integer('optimus_sorter')->nullable()->default(0);
            $table->integer('full_pallet')->nullable()->default(0);
            $table->integer('half_pallet')->nullable()->default(0);
            $table->integer('quarter_pallet')->nullable()->default(0);
            $table->dateTime('date_scanned')->nullable();
            $table->enum('consignment_type', ['return', 'outbound'])->default('outbound');
            $table->string('api_uuid', 200)->nullable();
            $table->string('sender_company', 100)->nullable();
            $table->string('sender_email', 45)->nullable();
            $table->string('sender_telephone', 17)->nullable();
            $table->string('sender_address_line_1')->nullable();
            $table->string('sender_address_line_2')->nullable();
            $table->string('sender_address_line_3')->nullable();
            $table->string('sender_city', 50)->nullable();
            $table->string('sender_postcode', 15)->nullable();
            $table->integer('sender_country_id')->nullable();
            $table->string('sender_state', 45)->nullable();
            $table->date('collection_date')->nullable();
            $table->string('collection_start_time', 5)->nullable();
            $table->string('collection_end_time', 5)->nullable();
            $table->string('collection_confirmation_no', 45)->nullable();
            $table->enum('created_from', ['web', 'api', 'csv'])->nullable()->default('web');
            $table->tinyInteger('is_white_label')->default(0);
            $table->tinyInteger('is_dead_weight_chargable')->nullable()->default(0);
            $table->integer('is_customer_billable')->default(0);
            $table->string('ioss_number', 50)->default('0');
            $table->string('eori_number', 50)->default('0');
            $table->string('vat_number', 50)->default('0');
            $table->integer('is_over_size_chargable')->default(0);
            $table->integer('is_insured')->default(0);
            $table->integer('destination_warehouse_id')->nullable()->default(0);
            $table->text('consignment_seller')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignments');
    }
};
