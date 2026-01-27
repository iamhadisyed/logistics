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
        Schema::create('warehouse_processing_times', function (Blueprint $table) {
            $table->comment('This table is used to get information of the parcel processing time in any warehouse with respect to the service or carrier.');
            $table->integer('id');
            $table->integer('warehouse_id')->nullable()->comment('This field is acting as foreign key in order to get the warehouse ID. This will help to find out which service in which warehouse is taking what time to process the parcel.');
            $table->integer('parcel_processing_time')->nullable()->comment('This service is used to find out the parcel process time with respect to the particular warehouse.');
            $table->integer('service_id')->nullable()->comment('Th service_id field acts as a forign key of the service table in order to find out the service.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_processing_times');
    }
};
