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
        Schema::create('parcel_iteams', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('parcel_id');
            $table->string('iteam_name')->nullable();
            $table->decimal('iteam_weight', 10)->nullable();
            $table->enum('iteam_weight_unit', ['kg', 'pound'])->nullable()->default('kg');
            $table->integer('iteam_value')->nullable();
            $table->integer('iteam_quantity')->nullable();
            $table->integer('iteam_country_id')->nullable();
            $table->string('iteam_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_iteams');
    }
};
