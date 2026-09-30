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
        Schema::table('carriers', function (Blueprint $table) {
            // Real legacy column (see logistic/main/carrier.php, carrier_list.php)
            // missing from the original restore migration. Model, controller
            // validation, and factory already expect it — this fixes the gap.
            $table->boolean('is_pallet')->nullable()->default(false)->after('is_reconcile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn('is_pallet');
        });
    }
};
