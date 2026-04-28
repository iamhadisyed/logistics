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
        Schema::create('user_market_places_mappings', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('market_places_id');
            $table->bigInteger('user_account_id');
            $table->text('auth_data')->nullable();
            $table->string('store_key', 200)->nullable();
            $table->integer('active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_market_places_mappings');
    }
};
