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
        Schema::table('shipments', function (Blueprint $table) {
            // Real FKs so pricing/tariff lookups, tracking, and reporting can
            // join properly, instead of matching the free-text service_type
            // string. Nullable: existing rows (booked before this migration)
            // won't have them, and legacy carrier/service ids aren't declared
            // as strict foreign keys elsewhere in this schema either.
            $table->unsignedInteger('carrier_id')->nullable()->after('service_type');
            $table->unsignedInteger('service_id')->nullable()->after('carrier_id');
            $table->index('carrier_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['carrier_id', 'service_id']);
        });
    }
};
