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
        Schema::create('remoteareas', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('remoteareas_groups_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('from_postcode', 8)->nullable();
            $table->string('to_postcode', 8)->nullable();
            $table->string('city', 45)->nullable();
            $table->enum('is_deleted', ['Y', 'N'])->nullable()->default('N');
            $table->integer('added_by')->nullable();
            $table->dateTime('added_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remoteareas');
    }
};
