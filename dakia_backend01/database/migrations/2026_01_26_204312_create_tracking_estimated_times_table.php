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
        Schema::create('tracking_estimated_times', function (Blueprint $table) {
            $table->integer('id');
            $table->string('handeling_code', 45)->nullable();
            $table->string('country_iso', 45)->nullable();
            $table->string('estimated_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_estimated_times');
    }
};
