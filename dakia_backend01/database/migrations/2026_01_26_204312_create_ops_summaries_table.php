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
        Schema::create('ops_summaries', function (Blueprint $table) {
            $table->integer('id');
            $table->string('account', 45)->nullable();
            $table->string('services', 45)->nullable();
            $table->string('country', 45)->nullable();
            $table->string('quantity', 40)->nullable();
            $table->string('weight', 45)->nullable();
            $table->string('carrier', 45)->nullable();
            $table->dateTime('date_submitted')->nullable();
            $table->string('reference', 45)->nullable();
            $table->string('mawb', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ops_summaries');
    }
};
