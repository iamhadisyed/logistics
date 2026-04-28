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
        Schema::create('consignment_relabels', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignment_id')->nullable();
            $table->string('old_tracking_no', 45)->nullable();
            $table->string('new_tracking_no', 45)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->integer('userid')->nullable();
            $table->text('old_consignment_data')->nullable();
            $table->text('old_parcel_tracking_no')->nullable();
            $table->text('old_new_tracking_mapping')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_relabels');
    }
};
