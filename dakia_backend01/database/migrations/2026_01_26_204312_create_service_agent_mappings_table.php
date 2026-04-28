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
        Schema::create('service_agent_mappings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('serviceid')->nullable();
            $table->integer('agentid')->nullable();
            $table->integer('linehaul_agent')->nullable()->default(0);
            $table->string('account_number', 45)->nullable();
            $table->string('api_url', 200)->nullable();
            $table->string('api_username', 45)->nullable();
            $table->string('api_password', 45)->nullable();
            $table->string('ftp_host', 45)->nullable();
            $table->string('ftp_username', 45)->nullable();
            $table->string('ftp_password', 45)->nullable();
            $table->string('integration_type', 5)->nullable();
            $table->string('class_file_name', 45)->nullable();
            $table->decimal('insurance_charges')->nullable()->default(0);
            $table->decimal('insurance_cover')->nullable()->default(0);
            $table->decimal('reroute_charges')->nullable()->default(0);
            $table->decimal('oversize_charges')->nullable()->default(0);
            $table->decimal('address_change_charges')->nullable()->default(0);
            $table->decimal('other_surcharges')->nullable()->default(0);
            $table->decimal('return_charges')->nullable()->default(0);
            $table->decimal('relabel_charges')->nullable()->default(0);
            $table->decimal('wrong_address_charges')->nullable()->default(0);
            $table->decimal('from_weight')->nullable()->default(0);
            $table->decimal('to_weight')->nullable()->default(0);
            $table->text('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_agent_mappings');
    }
};
