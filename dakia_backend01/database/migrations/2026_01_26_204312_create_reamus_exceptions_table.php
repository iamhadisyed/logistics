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
        Schema::create('reamus_exceptions', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('country_code', 5)->nullable();
            $table->string('postcode_from', 10)->nullable();
            $table->string('postcode_to', 10)->nullable();
            $table->string('product_code', 5)->nullable();
            $table->string('feature_code', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reamus_exceptions');
    }
};
