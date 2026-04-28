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
        Schema::create('hermes_postcode_records', function (Blueprint $table) {
            $table->integer('id');
            $table->string('fullpostcode', 8);
            $table->char('pos_pcd_postcode_excluded_indicator', 1)->default('N');
            $table->string('sort_level_key', 8);
            $table->char('next_day_service', 1)->default('N');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hermes_postcode_records');
    }
};
