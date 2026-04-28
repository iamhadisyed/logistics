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
        Schema::create('partnerservicesroutings', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->integer('country_id');
            $table->decimal('from_weight', 10)->nullable();
            $table->decimal('to_weight', 10)->nullable();
            $table->integer('status')->default(1);
            $table->integer('product_id')->nullable();
            $table->integer('service_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnerservicesroutings');
    }
};
