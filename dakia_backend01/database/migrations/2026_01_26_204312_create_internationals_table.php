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
        Schema::create('internationals', function (Blueprint $table) {
            $table->integer('id');
            $table->char('iata_country_code', 2);
            $table->string('zipcode_from', 10)->nullable();
            $table->string('zipcode_to', 10)->nullable();
            $table->string('air_express_depot', 10)->nullable();
            $table->string('air_express_osort', 10)->nullable();
            $table->string('air_express_dsort', 10)->nullable();
            $table->string('dpd_classic_deport', 10)->nullable();
            $table->string('dpd_classic_osort', 10)->nullable();
            $table->string('dpd_classic_dsort', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internationals');
    }
};
