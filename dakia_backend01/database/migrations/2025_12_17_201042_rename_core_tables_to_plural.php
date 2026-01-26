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
        // Drop the legacy singular user table (now empty, data migrated to users)
        Schema::dropIfExists('user');

        // Rename core tables to plural
        Schema::rename('address', 'addresses');
        Schema::rename('carrier', 'carriers');
        Schema::rename('consignment', 'consignments');
        Schema::rename('country', 'countries');
        Schema::rename('parcel', 'parcels');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('addresses', 'address');
        Schema::rename('carriers', 'carrier');
        Schema::rename('consignments', 'consignment');
        Schema::rename('countries', 'country');
        Schema::rename('parcels', 'parcel');
    }
};
