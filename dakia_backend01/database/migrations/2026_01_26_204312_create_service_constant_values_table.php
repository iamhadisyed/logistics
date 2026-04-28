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
        Schema::create('service_constant_values', function (Blueprint $table) {
            $table->integer('id');
            $table->string('constant_value', 250)->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('agent_id')->nullable();
            $table->integer('constant_id')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->integer('added_by')->nullable();
            $table->dateTime('date_update')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_constant_values');
    }
};
