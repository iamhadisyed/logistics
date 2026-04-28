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
        Schema::create('tariff_user_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('tariff_name');
            $table->integer('user_id');
            $table->dateTime('tariff_added_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariff_user_mappings');
    }
};
