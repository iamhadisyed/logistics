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
        Schema::create('market_place_documentation_mappings', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->integer('marketplace_id')->nullable();
            $table->string('step_title', 250)->nullable();
            $table->text('step_description')->nullable();
            $table->string('step_image')->nullable();
            $table->integer('step_order')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->integer('added_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_place_documentation_mappings');
    }
};
