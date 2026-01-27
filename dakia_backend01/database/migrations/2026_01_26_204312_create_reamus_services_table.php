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
        Schema::create('reamus_services', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('service_id')->nullable();
            $table->string('service_description', 50)->nullable();
            $table->string('product_line1', 15)->nullable();
            $table->string('product_line2', 35)->nullable();
            $table->string('product_code', 2)->nullable();
            $table->string('date_code', 2)->nullable();
            $table->string('day_text', 1)->nullable();
            $table->string('time_code', 1)->nullable();
            $table->string('time_text', 1)->nullable();
            $table->string('handling', 15)->nullable();
            $table->string('feature_id', 3)->nullable();
            $table->string('feature_code', 2)->nullable();
            $table->string('file_type', 3)->nullable();
            $table->string('consignment_flag', 3)->nullable();
            $table->string('ds_flag', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reamus_services');
    }
};
