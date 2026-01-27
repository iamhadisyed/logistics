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
        Schema::create('language_keys', function (Blueprint $table) {
            $table->integer('id');
            $table->string('keyword', 100)->nullable();
            $table->string('language', 10)->nullable();
            $table->text('caption')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->integer('createdby')->nullable();
            $table->timestamp('date_updated')->nullable();
            $table->integer('updatedby')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_keys');
    }
};
