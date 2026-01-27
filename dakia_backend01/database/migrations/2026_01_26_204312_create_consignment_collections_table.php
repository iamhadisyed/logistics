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
        Schema::create('consignment_collections', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('consignment_id');
            $table->string('sender_company', 50)->nullable();
            $table->string('sender_contact', 50)->nullable();
            $table->string('sender_email', 50)->nullable();
            $table->string('sender_address_line_1', 50)->nullable();
            $table->string('sender_address_line_2', 50)->nullable();
            $table->string('sender_address_line_3', 50)->nullable();
            $table->string('sender_city', 50)->nullable();
            $table->char('sender_country_iso_code', 3)->nullable();
            $table->string('sender_postcode', 10)->nullable();
            $table->string('sender_telephone', 20)->nullable();
            $table->integer('date_collection')->nullable();
            $table->unsignedInteger('earliest_latest_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_collections');
    }
};
