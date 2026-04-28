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
        Schema::create('tracking_data', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('entity_id');
            $table->enum('entity_type', ['parcel', 'shipment'])->default('parcel');
            $table->string('tracking_number', 45)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('track_point', 100)->nullable();
            $table->timestamp('date_created')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->integer('status_code_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->string('pod_image', 200)->nullable();
            $table->string('carrier_code', 100)->nullable();
            $table->string('carrier_desc')->nullable();
            $table->string('signatory')->nullable();
            $table->timestamp('date_added')->useCurrent();
            $table->string('parcel_image')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_data');
    }
};
