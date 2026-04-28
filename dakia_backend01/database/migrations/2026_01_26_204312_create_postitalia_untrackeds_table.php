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
        Schema::create('postitalia_untrackeds', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('postcode')->nullable();
            $table->string('region', 45)->nullable();
            $table->string('provenience', 45)->nullable();
            $table->string('sortation', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postitalia_untrackeds');
    }
};
