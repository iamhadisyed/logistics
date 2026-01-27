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
        Schema::create('vehicle_parcel_mappings', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->bigInteger('parcel_id');
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('driver_id')->nullable();
            $table->date('pickup_date');
            $table->timestamp('date_added')->useCurrent();
            $table->bigInteger('added_by')->nullable();
            $table->boolean('is_active')->nullable()->default(false);
            $table->dateTime('date_updated')->useCurrentOnUpdate()->nullable();
            $table->bigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_parcel_mappings');
    }
};
