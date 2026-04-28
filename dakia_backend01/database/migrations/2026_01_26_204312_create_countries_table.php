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
        Schema::create('countries', function (Blueprint $table) {
            $table->integer('id', true);
            $table->char('iso', 3);
            $table->string('name', 80)->nullable();
            $table->string('region', 45)->nullable();
            $table->enum('postcode_required', ['YES', 'NO'])->nullable()->default('YES');
            $table->string('type', 1)->nullable();
            $table->string('region_collection', 45)->nullable();
            $table->unsignedInteger('numcode')->nullable()->comment('This is 3 digit country iso code');
            $table->char('allow_express', 1)->nullable();
            $table->char('allow_classic', 1)->nullable();
            $table->char('eu_country', 1)->nullable();
            $table->string('shipping_advice')->nullable();
            $table->enum('is_vatable', ['YES', 'NO'])->nullable()->default('NO');
            $table->decimal('vat_rate', 2)->nullable()->comment('VAT is always in percentage');
            $table->string('printable_name', 80);
            $table->char('iso3', 3)->nullable();
            $table->integer('export_flag')->nullable();
            $table->integer('timezone_difference')->nullable();
            $table->char('has_postcodeq', 1);
            $table->char('has_subzonesq', 1);
            $table->integer('orderq');
            $table->tinyInteger('active')->default(1);
            $table->char('deletedq', 1)->default('N');
            $table->dateTime('added_on');
            $table->string('added_by', 100);
            $table->dateTime('changed_on');
            $table->string('changed_by', 100);
            $table->integer('vat_charged_flag')->nullable();
            $table->integer('customs_flag')->nullable();
            $table->text('description')->nullable();
            $table->string('country_image', 75)->nullable();
            $table->string('metakeywords', 300)->nullable();
            $table->string('metadescription', 300)->nullable();
            $table->string('pagetitle', 100)->nullable();
            $table->string('countrybanner', 75)->nullable();
            $table->string('opcode', 5)->nullable();
            $table->string('iso_three', 3)->nullable();
            $table->string('german_name', 80)->nullable();
            $table->string('manifest_template', 80)->nullable();
            $table->string('bag_template', 80)->nullable();
            $table->integer('bag_weight_limit')->nullable();
            $table->integer('bag_low_value')->nullable();
            $table->integer('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
