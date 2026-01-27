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
        Schema::create('pallet_locations', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('locationid')->nullable();
            $table->integer('palletid')->nullable();
            $table->integer('consignmentid')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->integer('createdby')->nullable();
            $table->string('comments', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallet_locations');
    }
};
