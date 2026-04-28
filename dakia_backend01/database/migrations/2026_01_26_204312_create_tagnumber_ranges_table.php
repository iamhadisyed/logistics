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
        Schema::create('tagnumber_ranges', function (Blueprint $table) {
            $table->integer('id');
            $table->bigInteger('range_start')->nullable();
            $table->bigInteger('range_end')->nullable();
            $table->bigInteger('next_number')->nullable();
            $table->dateTime('increment_date')->nullable();
            $table->string('service', 45)->nullable();
            $table->string('country', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagnumber_ranges');
    }
};
