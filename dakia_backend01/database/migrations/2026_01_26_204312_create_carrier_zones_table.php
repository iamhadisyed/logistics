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
        Schema::create('carrier_zones', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->integer('carrier_id');
            $table->unsignedInteger('service_id')->nullable();
            $table->string('name', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('deleted')->default(false);
            $table->dateTime('date_added')->nullable();
            $table->integer('added_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_zones');
    }
};
