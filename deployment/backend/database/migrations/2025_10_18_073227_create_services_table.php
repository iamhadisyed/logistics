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
            $table->id();
            $table->string('name', 45);
            $table->string('code', 20);
            $table->unsignedBigInteger('carrier_id');
            $table->string('account_number', 20)->nullable();
            $table->string('type', 5)->nullable();
            $table->decimal('from_weight', 10, 3)->nullable();
            $table->decimal('to_weight', 10, 3)->nullable();
            $table->integer('weight_type')->default(1)->comment('1 for parcel, 2 for shipment');
            $table->string('supplier', 100)->nullable();
            $table->enum('service_type', ['D', 'C', 'B', 'DO'])->default('D')->comment('D dispatch, B both, C collection, DO for drop off');
            $table->unsignedBigInteger('drop_off_service_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('fuel_surcharge_cost', 9, 2)->nullable();
            $table->decimal('fuel_surcharge', 9, 2);
            $table->char('fuel_surcharge_type', 1);
            $table->decimal('max_length', 9, 2);
            $table->decimal('max_width', 9, 2);
            $table->decimal('max_height', 9, 2);
            $table->decimal('max_volumetric_weight', 9, 2)->nullable();
            $table->integer('volumetric_denominator')->default(5000);
            $table->tinyInteger('send_data_courier')->default(0);
            $table->boolean('is_document')->default(false);
            $table->boolean('friday_only_flag')->nullable();
            $table->boolean('saturday_only_flag')->default(false);
            $table->boolean('sunday_only_flag')->default(false);
            $table->integer('product_owner')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('deletedq')->default(false);
            $table->datetime('added_on')->nullable();
            $table->string('added_by', 100)->nullable();
            $table->datetime('changed_on')->nullable();
            $table->string('changed_by', 100)->nullable();
            $table->string('uploaded_currency', 3)->nullable();
            $table->decimal('uploaded_currency_value', 10, 2)->nullable();
            $table->decimal('registration_fee', 10, 2)->default(0.00);
            $table->decimal('weight_after', 10, 2)->default(0.00);
            $table->decimal('additional_charge', 10, 2)->default(0.00);
            $table->integer('origin_country')->default(255);
            $table->boolean('is_untrack')->default(false);
            $table->integer('account_owner')->nullable();
            $table->enum('remotearea', ['ON_WEIGHT', 'ON_PIECE'])->default('ON_PIECE');
            $table->integer('carrier_address_limit')->default(30);
            $table->string('label_class_name', 100)->nullable();
            $table->integer('transit_time')->nullable();
            $table->boolean('required_email')->default(false);
            $table->boolean('required_telephone')->default(false);
            $table->enum('shipment_type', ['LETTER', 'PARCEL'])->default('PARCEL');
            $table->enum('pre_sort', ['YES', 'NO'])->default('NO');
            $table->boolean('proforma_invoice')->default(false);
            $table->timestamps();
            
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
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
