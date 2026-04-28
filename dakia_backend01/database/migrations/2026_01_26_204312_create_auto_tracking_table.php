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
        Schema::create('auto_tracking', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->integer('next_number')->nullable();
            $table->integer('range_end')->nullable();
            $table->dateTime('increment_date')->nullable();
            $table->string('service_name', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_tracking');
    }
};
