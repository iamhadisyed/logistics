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
        Schema::create('brazil_postcodes', function (Blueprint $table) {
            $table->integer('id');
            $table->string('state', 45)->nullable();
            $table->string('locality', 200)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('zone', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brazil_postcodes');
    }
};
