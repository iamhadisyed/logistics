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
        Schema::create('parcel_bagging_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('parcel_id');
            $table->integer('bag_id');
            $table->bigInteger('added_by');
            $table->dateTime('added_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_bagging_mappings');
    }
};
