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
        Schema::create('tariffs_account_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('tariff_id');
            $table->integer('user_account_id');
            $table->dateTime('added_date')->nullable();
            $table->integer('added_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs_account_mappings');
    }
};
