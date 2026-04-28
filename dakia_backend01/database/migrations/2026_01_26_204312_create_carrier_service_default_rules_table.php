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
        Schema::create('carrier_service_default_rules', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('serviceid')->nullable();
            $table->integer('agentid')->nullable();
            $table->decimal('from_weight', 10, 3)->nullable();
            $table->decimal('to_weight', 10, 3)->nullable();
            $table->boolean('is_default')->nullable();
            $table->enum('agent_type', ['outbound', 'dispatch'])->default('outbound');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_service_default_rules');
    }
};
