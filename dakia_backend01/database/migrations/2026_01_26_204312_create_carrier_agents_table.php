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
        Schema::create('carrier_agents', function (Blueprint $table) {
            $table->integer('id');
            $table->string('account_number', 45)->nullable();
            $table->string('name', 45)->nullable();
            $table->string('company', 45)->nullable();
            $table->string('address_line_1', 45)->nullable();
            $table->string('address_line_2', 45)->nullable();
            $table->string('address_line_3', 45)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('country_iso_code', 2)->nullable();
            $table->string('api_username', 45)->nullable();
            $table->string('api_password', 45)->nullable();
            $table->string('ftp_host', 45)->nullable();
            $table->string('ftp_username', 45)->nullable();
            $table->string('ftp_password', 45)->nullable();
            $table->string('integration_type', 45)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('created_by', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_agents');
    }
};
