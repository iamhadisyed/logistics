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
        Schema::create('whistl_depo_details', function (Blueprint $table) {
            $table->integer('id');
            $table->string('depo_id', 45)->nullable();
            $table->string('depo_detail', 200)->nullable();
            $table->string('depo_address', 200)->nullable();
            $table->string('from_postcode', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whistl_depo_details');
    }
};
