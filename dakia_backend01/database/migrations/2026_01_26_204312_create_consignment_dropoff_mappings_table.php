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
        Schema::create('consignment_dropoff_mappings', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('dropoff_consignment_id');
            $table->bigInteger('dispatch_consignment_id');
            $table->text('dropoff_consignment_tracking');
            $table->text('dispatch_consignment_tracking');
            $table->text('parcel_tracking')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->dateTime('date_created')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_dropoff_mappings');
    }
};
