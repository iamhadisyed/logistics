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
        Schema::create('service_range_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('service_id')->nullable();
            $table->integer('agent_id')->nullable();
            $table->integer('licence_plate_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_range_mappings');
    }
};
