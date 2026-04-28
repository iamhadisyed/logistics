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
        Schema::create('czint_datafile_ids', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('file_name', 45)->nullable();
            $table->dateTime('sent_date')->nullable();
            $table->integer('file_name_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('czint_datafile_ids');
    }
};
