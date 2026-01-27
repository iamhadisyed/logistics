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
        Schema::create('market_places_authenticate_fields', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('field_name');
            $table->string('field_value');
            $table->bigInteger('market_places_id');
            $table->bigInteger('added_by');
            $table->dateTime('added_date')->nullable();
            $table->boolean('is_delete')->nullable()->default(false);
            $table->boolean('auto_generate_value')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_places_authenticate_fields');
    }
};
