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
        Schema::create('bagging_manifest_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('entity_id');
            $table->integer('manifest_id');
            $table->enum('manifest_entity_type', ['p', 'b'])->default('p')->comment('p for parcel and b for bagging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bagging_manifest_mappings');
    }
};
