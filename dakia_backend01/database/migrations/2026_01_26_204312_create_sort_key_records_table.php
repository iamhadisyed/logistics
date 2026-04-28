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
        Schema::create('sort_key_records', function (Blueprint $table) {
            $table->integer('id');
            $table->string('pos_sld_sort_level_key', 8);
            $table->string('pos_sld_level_1_type', 8);
            $table->string('pos_sld_level_1_name', 8);
            $table->string('pos_sld_level_1_code', 8);
            $table->string('pos_sld_level_2_type', 8);
            $table->string('pos_sld_level_2_name', 8);
            $table->string('pos_sld_level_2_code', 8);
            $table->string('pos_sld_level_3_type', 8);
            $table->string('pos_sld_level_3_name', 8);
            $table->string('pos_sld_level_3_code', 8);
            $table->string('pos_sld_level_4_type', 8);
            $table->string('pos_sld_level_4_name', 8);
            $table->string('pos_sld_level_4_code', 8);
            $table->string('pos_sld_level_5_type', 8);
            $table->string('pos_sld_level_5_name', 8);
            $table->string('pos_sld_level_5_code', 8);
            $table->string('pos_sld_hermes_barcode_1_to_7', 7);
            $table->string('pos_sld_hermes_barcode_seq_key', 7);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sort_key_records');
    }
};
