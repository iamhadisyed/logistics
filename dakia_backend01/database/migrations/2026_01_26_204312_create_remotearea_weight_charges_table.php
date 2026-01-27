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
        Schema::create('remotearea_weight_charges', function (Blueprint $table) {
            $table->integer('id');
            $table->decimal('weight_from', 10)->nullable();
            $table->decimal('weight_to', 10)->nullable();
            $table->string('service_code', 45)->nullable();
            $table->string('country_iso', 10)->nullable();
            $table->string('postcode_name', 45)->nullable();
            $table->string('formulla')->nullable();
            $table->decimal('charges', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remotearea_weight_charges');
    }
};
