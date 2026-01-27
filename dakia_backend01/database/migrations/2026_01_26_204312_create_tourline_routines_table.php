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
        Schema::create('tourline_routines', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('agency_name', 60)->nullable();
            $table->string('postal_code', 45)->nullable();
            $table->string('agency_code', 45)->nullable();
            $table->string('zone', 45)->nullable();
            $table->string('province', 45)->nullable();
            $table->string('route_code', 45)->nullable();
            $table->string('km', 45)->nullable();
            $table->string('town_name', 100)->nullable();
            $table->string('kilometer', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourline_routines');
    }
};
