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
        Schema::create('warehouse_warehouse_ttimes', function (Blueprint $table) {
            $table->comment('This is the table we are using in order to get the parcel transit time from one warehouse to other warehouse.');
            $table->integer('id');
            $table->integer('from_warehouse_id')->nullable()->comment('This is the id of the warehouse table from the parcel is sending.');
            $table->integer('to_warehouse_id')->nullable()->comment('This is the id of the warehouse table to where parcel is dispatching. ');
            $table->integer('transit_time')->nullable()->comment('This field we are using in order to find out the time of the parcel delivery betwee two warehouses . ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_warehouse_ttimes');
    }
};
