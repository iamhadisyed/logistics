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
        Schema::create('pallets', function (Blueprint $table) {
            $table->integer('id');
            $table->string('palletno', 45)->nullable();
            $table->timestamp('date_created')->nullable();
            $table->boolean('close')->nullable()->default(false)->comment('1 for Yes 0 for No');
            $table->integer('userid')->nullable();
            $table->integer('pallet_carrier_id')->nullable();
            $table->timestamp('date_dispatch')->nullable();
            $table->integer('dispatch_userid')->nullable();
            $table->string('type', 1)->nullable();
            $table->integer('manifestid')->nullable()->default(0);
            $table->string('hub', 45)->nullable();
            $table->boolean('is_active')->nullable()->default(false)->comment('O for No and 1 for Yes');
            $table->string('comments', 100)->nullable();
            $table->string('label', 100)->nullable();
            $table->integer('pallet_source_country_id')->nullable();
            $table->integer('pallet_source_warehouse_id')->nullable();
            $table->integer('pallet_destination_country_id')->nullable();
            $table->integer('pallet_destination_warehouse_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallets');
    }
};
