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
        Schema::create('domestics', function (Blueprint $table) {
            $table->integer('id');
            $table->string('postcode_sector', 45)->nullable();
            $table->string('dpd_depot', 45)->nullable();
            $table->string('dpd_services_group', 45)->nullable();
            $table->string('dpd_offshore_zone', 45)->nullable();
            $table->string('timeslots_code', 45)->nullable();
            $table->string('cluster', 45)->nullable();
            $table->string('ilk_depot', 45)->nullable();
            $table->string('ilk_services_group', 45)->nullable();
            $table->string('ilk_offshore_zone', 45)->nullable();
            $table->string('ilk_alternate_service', 45)->nullable();
            $table->string('new_postcode', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domestics');
    }
};
