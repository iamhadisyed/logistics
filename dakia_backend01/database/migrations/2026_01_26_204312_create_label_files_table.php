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
        Schema::create('label_files', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('file_name', 100)->nullable();
            $table->string('account_number', 30)->nullable();
            $table->text('hawb_list')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->string('error_list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_files');
    }
};
