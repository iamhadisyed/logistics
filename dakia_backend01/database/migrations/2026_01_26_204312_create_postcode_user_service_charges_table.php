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
        Schema::create('postcode_user_service_charges', function (Blueprint $table) {
            $table->integer('id');
            $table->string('from_postcode', 45)->nullable()->comment('-');
            $table->string('to_postcode', 45)->nullable();
            $table->string('postcode_name', 45)->nullable();
            $table->string('city_name', 45)->nullable();
            $table->string('country_iso', 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postcode_user_service_charges');
    }
};
