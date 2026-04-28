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
        Schema::create('estimate_delivery_timings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('service_id')->nullable();
            $table->integer('from_rateband')->nullable();
            $table->integer('to_rateband')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->nullable()->default('INACTIVE');
            $table->string('delivery_timing', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_delivery_timings');
    }
};
