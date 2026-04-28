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
        Schema::create('pbt_routines', function (Blueprint $table) {
            $table->integer('id');
            $table->string('file_code', 45)->nullable();
            $table->string('courier_file_label_code', 45)->nullable();
            $table->string('transport_label_code', 45)->nullable();
            $table->string('courier_charges_code', 45)->nullable();
            $table->string('area_desc', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pbt_routines');
    }
};
