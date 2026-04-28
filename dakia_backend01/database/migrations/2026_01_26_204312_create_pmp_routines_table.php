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
        Schema::create('pmp_routines', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('storeid')->nullable();
            $table->string('store_name', 200)->nullable();
            $table->boolean('is_active')->nullable()->default(false);
            $table->string('country', 3)->nullable();
            $table->string('address_line_1', 45)->nullable();
            $table->string('address_line_2', 45)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->integer('depot_no')->nullable();
            $table->string('depot_description', 45)->nullable();
            $table->integer('round1')->nullable();
            $table->integer('drop1')->nullable();
            $table->integer('round2')->nullable();
            $table->integer('drop2')->nullable();
            $table->dateTime('date_created')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmp_routines');
    }
};
