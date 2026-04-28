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
        Schema::create('tariff_details', function (Blueprint $table) {
            $table->integer('id');
            $table->string('tariff_name', 45)->nullable();
            $table->boolean('status')->nullable()->default(false);
            $table->string('tariff_type', 10)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamp('date_created')->nullable()->useCurrent();
            $table->integer('added_by')->nullable();
            $table->string('currency', 3)->nullable()->default('GBP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariff_details');
    }
};
