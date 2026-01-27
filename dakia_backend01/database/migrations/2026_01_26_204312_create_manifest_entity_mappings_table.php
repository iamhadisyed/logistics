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
        Schema::create('manifest_entity_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('entity_id');
            $table->integer('manifest_id');
            $table->enum('manifest_entity_type', ['p', 'b', 'pl'])->default('p')->comment('p for parcel, b for bagging and pl for parcel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifest_entity_mappings');
    }
};
