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
        Schema::create('owe_southafrica_postcodes', function (Blueprint $table) {
            $table->integer('id');
            $table->string('zone', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('main_outlying', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owe_southafrica_postcodes');
    }
};
