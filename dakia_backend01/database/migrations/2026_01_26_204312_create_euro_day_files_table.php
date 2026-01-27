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
        Schema::create('euro_day_files', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('file_name', 50)->nullable();
            $table->dateTime('sent_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('euro_day_files');
    }
};
