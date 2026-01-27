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
        Schema::create('dx_routings', function (Blueprint $table) {
            $table->integer('id');
            $table->string('district', 45)->nullable();
            $table->string('sector', 45)->nullable();
            $table->string('depot', 45)->nullable();
            $table->string('depotid', 45)->nullable();
            $table->string('region_id', 45)->nullable();
            $table->string('delivery_method', 45)->nullable();
            $table->string('delivery_method_id', 45)->nullable();
            $table->string('delivery_method_description', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dx_routings');
    }
};
