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
        Schema::create('services', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 45);
            $table->string('code', 20);
            $table->integer('carrier_id');
            $table->string('account_number', 20)->nullable();
            $table->string('type', 5)->nullable();
            $table->decimal('from_weight', 10, 3)->nullable();
            $table->decimal('to_weight', 10, 3)->nullable();
            $table->integer('wieght_type')->nullable()->default(1)->comment('1 for parcel 
2 for shipment');
            $table->string('supplier', 100)->nullable();
            $table->enum('service_type', ['D', 'C', 'B', 'DO'])->default('D')->comment('D dispatch, B both, C collection, DO for drop off');
            $table->bigInteger('drop_off_service_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('fuel_surcharge_cost', 9)->nullable();
            $table->decimal('fuel_surcharge', 9);
            $table->char('fuel_surcharge_type', 1);
            $table->decimal('max_length', 9);
            $table->decimal('max_width', 9);
            $table->decimal('max_height', 9);
            $table->decimal('max_volumetric_weight', 9)->nullable();
            $table->integer('volumetric_denominator')->nullable()->default(5000);
            $table->tinyInteger('send_data_courier')->nullable()->default(0);
            $table->boolean('is_document')->nullable()->default(false);
            $table->boolean('friday_only_flag')->nullable();
            $table->boolean('saturday_only_flag')->nullable()->default(false);
            $table->boolean('sunday_only_flag')->nullable()->default(false);
            $table->integer('product_owner')->nullable();
            $table->boolean('active')->nullable()->default(true);
            $table->boolean('deletedq')->nullable()->default(false);
            $table->dateTime('added_on')->nullable();
            $table->string('added_by', 100)->nullable();
            $table->dateTime('changed_on')->nullable();
            $table->string('changed_by', 100)->nullable();
            $table->string('uploaded_currency', 3)->nullable();
            $table->decimal('uploaded_currency_value', 10)->nullable();
            $table->decimal('registration_fee', 10)->nullable()->default(0);
            $table->decimal('weight_after', 10)->nullable()->default(0);
            $table->decimal('aditional_charge', 10)->nullable()->default(0);
            $table->integer('origin_country')->nullable()->default(255);
            $table->boolean('is_untrack')->nullable()->default(false);
            $table->integer('account_owner')->nullable();
            $table->enum('remotearea', ['ON_WEIGHT', 'ON_PIECE'])->nullable()->default('ON_PIECE');
            $table->integer('carrier_address_limit')->nullable()->default(30);
            $table->string('label_class_name', 100)->nullable();
            $table->integer('transit_time')->nullable();
            $table->boolean('required_email')->nullable()->default(false);
            $table->boolean('required_telephone')->nullable()->default(false);
            $table->enum('shipment_type', ['LETTER', 'PARCEL'])->nullable()->default('PARCEL');
            $table->enum('pre_sort', ['YES', 'NO'])->nullable()->default('NO');
            $table->boolean('proforma_invoice')->nullable()->default(false);
            $table->enum('agent_dispatch', ['Y', 'N'])->nullable()->default('N');
            $table->enum('brief_manifest', ['Y', 'N'])->nullable()->default('N');
            $table->boolean('delivery_mode')->nullable()->comment('1 - Door to Door Delivery
2 - Parcel Shops
3 - Door to Door Delivery (POD)
');
            $table->boolean('insurance_available')->nullable()->default(false);
            $table->string('vol_wgt_formula', 50)->nullable()->comment('L* W * H / 5000');
            $table->enum('is_remotearea', ['Y', 'N'])->nullable()->default('N');
            $table->boolean('is_customized')->nullable()->default(false);
            $table->enum('pre_advise', ['Y', 'N'])->default('N');
            $table->enum('pre_alert', ['Y', 'N'])->default('N');
            $table->text('pre_alert_email')->nullable();
            $table->string('cut_off_time', 5)->nullable();
            $table->decimal('label_charges', 10)->nullable()->default(0);
            $table->tinyInteger('allow_oversize')->default(0);
            $table->tinyInteger('allow_overweight')->default(0);
            $table->integer('maximum_allowed_dimension')->default(0);
            $table->string('maximum_dim_formula', 100)->nullable();
            $table->enum('validation_type', ['mail', 'courier'])->default('mail');
            $table->enum('zone_type', ['country', 'postcode'])->nullable()->default('country');
            $table->enum('tariff_type', ['multi', 'single'])->nullable()->default('single');
            $table->decimal('girth', 10)->nullable();
            $table->string('girth_formula')->nullable();
            $table->enum('mail_type', ['letter', 'boxable', 'non-boxable'])->nullable();
            $table->enum('mail_option', ['commercial', 'freight_to_post'])->nullable();
            $table->integer('is_reschedulable')->default(0);
            $table->string('carrier_service_code', 50)->nullable();
            $table->integer('is_eori_required')->default(0);
            $table->enum('delivery_type', ['all', 'economy', 'priority', ''])->default('all');
            $table->enum('is_commercials', ['required', 'not required'])->default('not required');
            $table->enum('is_cn', ['required', 'not required'])->default('not required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
