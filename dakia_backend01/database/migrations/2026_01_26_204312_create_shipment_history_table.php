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
        Schema::create('shipment_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shipment_id')->index('shipment_history_shipment_id_foreign');
            $table->string('action');
            $table->text('details')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_history');
    }
};
