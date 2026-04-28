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
        Schema::create('locations', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 200)->nullable();
            $table->timestamp('date_created')->nullable();
            $table->integer('createdby')->nullable();
            $table->string('date_updated', 45)->nullable();
            $table->timestamp('updatedby')->nullable();
            $table->string('active', 1)->nullable();
            $table->string('type', 1)->nullable();
            $table->integer('warehouseid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
