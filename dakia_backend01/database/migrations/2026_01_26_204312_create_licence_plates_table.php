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
        Schema::create('licence_plates', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('range_name', 45)->nullable();
            $table->unsignedBigInteger('range_start');
            $table->unsignedBigInteger('range_end');
            $table->unsignedBigInteger('next_number');
            $table->dateTime('increment_date')->nullable();
            $table->string('delivery_network', 45);
            $table->string('prefix', 20)->nullable();
            $table->string('sufix', 20)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('addedby')->nullable();
            $table->integer('updatedby')->nullable();
            $table->boolean('country_range')->nullable()->default(false);
            $table->text('country_list')->nullable();
            $table->integer('range_reminder_limit')->nullable()->default(1000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licence_plates');
    }
};
