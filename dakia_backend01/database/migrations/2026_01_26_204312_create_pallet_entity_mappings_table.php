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
        Schema::create('pallet_entity_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('pallet_id');
            $table->integer('entity_id');
            $table->enum('pallet_entity_type', ['p', 'b'])->default('p')->comment('p for parcel and b for bagging');
            $table->enum('pre_sort', ['y', 'n'])->default('n');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallet_entity_mappings');
    }
};
