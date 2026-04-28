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
        Schema::create('brazil_states', function (Blueprint $table) {
            $table->integer('id');
            $table->string('state_code', 45)->nullable();
            $table->string('state_name', 45)->nullable();
            $table->string('city_code', 45)->nullable();
            $table->string('city_name', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brazil_states');
    }
};
