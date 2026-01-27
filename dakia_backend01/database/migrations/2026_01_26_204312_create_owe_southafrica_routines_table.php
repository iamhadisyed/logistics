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
        Schema::create('owe_southafrica_routines', function (Blueprint $table) {
            $table->integer('id');
            $table->string('state', 45)->nullable();
            $table->string('zone', 45)->nullable();
            $table->string('route', 45)->nullable();
            $table->integer('delivery_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owe_southafrica_routines');
    }
};
