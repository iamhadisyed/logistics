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
        Schema::create('parcels', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('consignment_id');
            $table->string('tracking_number', 32)->nullable()->comment('tracking number for parcel');
            $table->string('do_tracking_number', 32)->nullable();
            $table->decimal('length', 10)->unsigned()->default(0);
            $table->decimal('width', 10)->unsigned()->default(0);
            $table->decimal('height', 10)->unsigned()->default(0);
            $table->decimal('weight', 4);
            $table->text('description')->nullable();
            $table->text('parcel_message')->nullable();
            $table->string('qty', 45)->nullable()->comment('Number of items in parcel');
            $table->string('commoditycode', 300)->nullable();
            $table->string('hscode', 300)->nullable();
            $table->decimal('grossweight', 10)->nullable();
            $table->string('pweight', 45)->nullable();
            $table->string('itemvalue', 300)->nullable();
            $table->integer('number_item')->nullable();
            $table->string('tarrif_no', 45)->nullable();
            $table->decimal('update_weight', 10)->nullable();
            $table->string('owe_status_code', 200)->nullable()->comment('For tracking purpose');
            $table->integer('chute_sorted')->nullable();
            $table->integer('parcel_status_code')->nullable();
            $table->string('routing_code', 45)->nullable();
            $table->dateTime('last_tracking_update')->nullable();
            $table->text('parcel_item_desc')->nullable();
            $table->string('parcel_label')->nullable();
            $table->string('itemsku')->nullable();
            $table->string('itemurl')->nullable();
            $table->string('sort_type', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
