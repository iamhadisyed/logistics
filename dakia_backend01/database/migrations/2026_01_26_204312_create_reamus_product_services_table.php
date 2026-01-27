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
        Schema::create('reamus_product_services', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('reamus_id', 19)->nullable();
            $table->string('product_code', 2)->nullable();
            $table->string('feature_code', 5)->nullable();
            $table->string('exception', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reamus_product_services');
    }
};
