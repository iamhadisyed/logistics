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
        // Corrects 2026_09_29_200118_add_is_pallet_to_carriers_table: that
        // migration was wrong. It added is_pallet based on seeing the field
        // name in a legacy PHP form (carrier.php), without checking the
        // authoritative schema dump (db_full_schema.json) first. That dump
        // shows the real legacy `carriers` table has 15 columns and none of
        // them is is_pallet — the real "pallet carrier" concept lives in a
        // separate `pallet_carriers` lookup table instead. Never rewriting
        // an already-pushed migration's history — this is the correction,
        // shipped as its own migration.
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn('is_pallet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->boolean('is_pallet')->nullable()->default(false);
        });
    }
};
