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
        Schema::create('ch_shipments', function (Blueprint $table) {
            $table->integer('id');
            $table->string('hawb', 45)->nullable();
            $table->string('reference', 45)->nullable();
            $table->string('awb', 45)->nullable();
            $table->string('account', 45)->nullable();
            $table->string('country_iso_code', 3)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('product', 45)->nullable();
            $table->string('service', 45)->nullable();
            $table->string('bagnumber', 45)->nullable();
            $table->string('mawb', 45)->nullable();
            $table->decimal('charge_able_weight', 10, 3)->nullable();
            $table->decimal('total_charge', 10, 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ch_shipments');
    }
};
