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
            $table->id();
            $table->integer('agent_id')->default(51);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->integer('customized_service_id')->default(0);
            $table->unsignedBigInteger('warehouse_user_id')->nullable()->comment('User Id for those shipment which created by warehouse person');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->bigInteger('sales_pot_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('credit_id')->nullable();
            $table->boolean('is_invoiced')->default(false);
            $table->enum('invoice_type', ['INV', 'MNI'])->nullable();
            $table->integer('shipment_status')->default(0)->comment('4 digit code for shipment status');
            $table->enum('shipment_type', ['C', 'D', 'P', 'DO'])->default('D')->comment('C for collection, D for dispatched, P product, DO dropoff');
            $table->string('awb', 30)->nullable();
            $table->string('consignment_status', 20)->nullable();
            $table->string('return_awb', 30)->nullable();
            $table->string('hawb', 40);
            $table->string('mawb', 40)->nullable();
            $table->string('service_name', 50)->nullable();
            $table->string('reference', 20)->nullable();
            $table->timestamp('date_created')->useCurrent();
            $table->integer('date_label_created')->nullable();
            $table->integer('date_booked')->nullable();
            $table->integer('date_delivered')->nullable();
            $table->boolean('is_customer_manifested')->default(false);
            $table->string('booked_file_id', 50)->default('0');
            
            // Receiver details
            $table->string('company', 100)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('address_line_1', 50)->nullable();
            $table->string('address_line_2', 50)->nullable();
            $table->string('address_line_3', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 45)->nullable();
            $table->string('postcode', 15)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('telephone', 17)->nullable();
            $table->string('email', 45)->nullable();
            
            // Package details
            $table->unsignedTinyInteger('number_pieces')->nullable();
            $table->string('weight_type', 2)->default('PP');
            $table->decimal('weight', 8, 3)->unsigned()->nullable();
            $table->decimal('update_weight', 8, 3)->nullable();
            $table->decimal('fake_weight', 8, 3)->nullable()->comment('sending 20-30% decrease weight to the carrier');
            $table->decimal('charge_weight', 8, 3)->nullable();
            $table->decimal('vol_weight', 8, 3)->nullable()->comment('vol weight will updated by scanning system');
            $table->integer('vol_denominator')->nullable()->comment('Denominator will be updated from service table');
            $table->enum('hv_lv', ['L', 'H', 'M'])->nullable();
            $table->string('description', 255)->nullable();
            $table->string('notes', 255)->nullable();
            $table->decimal('value', 10, 2)->unsigned()->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('itemtype', 60)->nullable();
            
            // Sender details
            $table->string('sender_name', 45);
            $table->string('username', 45)->nullable();
            $table->boolean('sender_checked')->default(false);
            $table->string('sender_company', 100)->nullable();
            $table->string('sender_email', 45)->nullable();
            $table->string('sender_telephone', 17)->nullable();
            $table->string('sender_address_line_1', 255)->nullable();
            $table->string('sender_address_line_2', 255)->nullable();
            $table->string('sender_address_line_3', 255)->nullable();
            $table->string('sender_city', 50)->nullable();
            $table->string('sender_postcode', 15)->nullable();
            $table->unsignedBigInteger('sender_country_id')->nullable();
            $table->string('sender_state', 45)->nullable();
            
            // Additional fields
            $table->string('message', 400)->nullable();
            $table->string('sorter_image', 200)->nullable();
            $table->string('label_file', 255)->nullable();
            $table->boolean('is_doc')->default(false);
            $table->string('routing_code', 3)->nullable();
            $table->string('routing_code_eur', 45)->nullable();
            $table->string('other_routing_code', 250)->nullable();
            $table->boolean('billing_hold')->default(false)->comment('Account use this field for making billing hold');
            $table->boolean('send_courier_data')->default(false)->comment('Set 1 when data send to all courier');
            $table->boolean('remote_charges')->default(false);
            $table->boolean('reinvoices')->default(false);
            $table->boolean('optimus_sorter')->default(false);
            $table->tinyInteger('full_pallet')->default(0);
            $table->tinyInteger('half_pallet')->default(0);
            $table->tinyInteger('quarter_pallet')->default(0);
            $table->datetime('date_scanned')->nullable();
            $table->enum('consignment_type', ['return', 'outbound'])->default('outbound');
            $table->string('api_uuid', 200)->nullable();
            
            // Collection details
            $table->date('collection_date')->nullable();
            $table->string('collection_start_time', 5)->nullable();
            $table->string('collection_end_time', 5)->nullable();
            $table->string('collection_confirmation_no', 45)->nullable();
            $table->enum('created_from', ['web', 'api', 'csv'])->default('web');
            $table->boolean('is_white_label')->default(false);
            $table->boolean('is_dead_weight_chargable')->default(false);
            $table->boolean('is_customer_billable')->default(false);
            $table->string('ioss_number', 50)->default('0');
            $table->string('eori_number', 50)->default('0');
            $table->string('vat_number', 50)->default('0');
            $table->boolean('is_over_size_chargable')->default(false);
            $table->boolean('is_insured')->default(false);
            $table->integer('destination_warehouse_id')->default(0);
            $table->text('consignment_seller')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('sender_country_id')->references('id')->on('countries')->onDelete('set null');
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
