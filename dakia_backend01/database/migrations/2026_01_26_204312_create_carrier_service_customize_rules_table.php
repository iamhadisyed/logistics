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
        Schema::create('carrier_service_customize_rules', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('serviceid')->nullable();
            $table->integer('agentid')->nullable();
            $table->integer('user_account_id')->nullable();
            $table->decimal('from_weight', 10, 3)->nullable();
            $table->decimal('to_weight', 10, 3)->nullable();
            $table->boolean('status')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_service_customize_rules');
    }
};
