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
        Schema::create('marketplace_order_details', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('marketplace_order_id')->nullable();
            $table->string('marketplace_item_id', 45)->nullable();
            $table->string('sku', 45)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('quantity_purchased', 45)->nullable();
            $table->string('asin', 45)->nullable();
            $table->string('item_price', 45)->nullable();
            $table->string('currency', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_order_details');
    }
};
