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
        Schema::create('consignment_charges_logs', function (Blueprint $table) {
            $table->comment('Stores activity log.');
            $table->integer('id');
            $table->integer('userid');
            $table->dateTime('logdate');
            $table->string('ipaddress', 40);
            $table->integer('log_id');
            $table->string('log_type', 10)->default('A');
            $table->text('message')->nullable();
            $table->text('previous_data')->nullable();
            $table->text('current_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_charges_logs');
    }
};
