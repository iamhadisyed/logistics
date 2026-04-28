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
        Schema::create('service_constants', function (Blueprint $table) {
            $table->integer('id');
            $table->string('constant', 200)->nullable();
            $table->integer('carrier_id')->nullable();
            $table->string('caption', 200)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('design_control', 45)->nullable();
            $table->boolean('mandatory')->nullable()->default(false);
            $table->integer('sort_order')->nullable();
            $table->enum('integration_type', ['API', 'EDI', 'SOFTWARE'])->nullable();
            $table->text('default_values')->nullable();
            $table->integer('field_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_constants');
    }
};
