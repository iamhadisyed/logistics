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
        Schema::create('customized_services_routing_logs', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('userid')->nullable();
            $table->dateTime('logdate')->nullable();
            $table->string('ipaddress', 100)->nullable();
            $table->integer('log_id')->nullable();
            $table->text('message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customized_services_routing_logs');
    }
};
