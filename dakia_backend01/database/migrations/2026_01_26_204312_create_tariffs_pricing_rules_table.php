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
        Schema::create('tariffs_pricing_rules', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('tariff_id')->nullable()->default(0);
            $table->string('name', 100)->nullable();
            $table->timestamp('date_added')->useCurrentOnUpdate()->useCurrent();
            $table->integer('added_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs_pricing_rules');
    }
};
