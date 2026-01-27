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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->integer('id');
            $table->string('heading', 300)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->dateTime('date_submitted')->nullable();
            $table->string('created_by', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
