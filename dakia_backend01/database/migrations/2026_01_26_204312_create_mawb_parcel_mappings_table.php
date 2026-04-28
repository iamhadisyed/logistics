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
        Schema::create('mawb_parcel_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('mawb_id');
            $table->integer('parcel_id');
            $table->integer('wharehouse_id');
            $table->integer('bag_id')->nullable();
            $table->dateTime('date_added')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->bigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mawb_parcel_mappings');
    }
};
