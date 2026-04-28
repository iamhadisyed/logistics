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
        Schema::create('cs_logs', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignment_id')->nullable();
            $table->text('internal_message')->nullable();
            $table->text('customer_message')->nullable();
            $table->string('cust_mail', 45)->nullable();
            $table->string('agent_mail', 45)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('reminder', 45)->nullable();
            $table->dateTime('reminder_expiry')->nullable();
            $table->integer('userid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_logs');
    }
};
