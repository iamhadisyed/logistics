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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_make')->nullable();
            $table->integer('model_year')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('vehicle_color', 50)->nullable();
            $table->string('vehicle_capacity')->nullable();
            $table->boolean('is_deleted')->nullable()->default(false);
            $table->timestamp('added_date')->useCurrent();
            $table->integer('added_by')->nullable();
            $table->string('vehicle_number', 15)->nullable();
            $table->timestamp('updated_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
