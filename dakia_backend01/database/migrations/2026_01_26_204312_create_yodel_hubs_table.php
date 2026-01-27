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
        Schema::create('yodel_hubs', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('pre_sort_carrier_id')->nullable();
            $table->string('hub', 100)->nullable();
            $table->string('routing_code', 45)->nullable();
            $table->string('company', 45)->nullable();
            $table->string('contact', 45)->nullable();
            $table->string('address_line_1', 45)->nullable();
            $table->string('address_line_2', 45)->nullable();
            $table->string('address_line_3', 45)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('country_iso_code', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yodel_hubs');
    }
};
