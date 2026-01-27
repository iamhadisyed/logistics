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
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('marketplace_id')->nullable();
            $table->string('marketplace_order_number', 45)->nullable();
            $table->dateTime('create_time')->nullable();
            $table->string('order_status', 45)->nullable();
            $table->string('receiver_name', 50)->nullable();
            $table->string('receiver_phone', 45)->nullable();
            $table->string('receiver_state', 45)->nullable();
            $table->string('receiver_city', 45)->nullable();
            $table->bigInteger('receiver_country_id')->nullable();
            $table->string('receiver_addressline1', 100)->nullable();
            $table->string('receiver_addressline2', 100)->nullable();
            $table->string('receiver_postcode', 45)->nullable();
            $table->string('payment_method', 45)->nullable();
            $table->string('order_total', 45)->nullable();
            $table->string('tracking_number', 45)->nullable();
            $table->string('receiver_email', 45)->nullable();
            $table->string('sender_email', 45)->nullable();
            $table->string('Ack', 45)->nullable();
            $table->string('error_code', 45)->nullable();
            $table->string('error_message', 45)->nullable();
            $table->bigInteger('consignment_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->date('shipped_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_orders');
    }
};
