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
        Schema::create('fftin_files', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('file_name', 35)->nullable();
            $table->dateTime('sent_date')->nullable();
            $table->string('label_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fftin_files');
    }
};
