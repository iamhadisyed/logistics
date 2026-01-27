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
        Schema::create('flight_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('flight_info_id')->nullable();
            $table->string('flight_number', 45)->nullable();
            $table->string('mawb', 45)->nullable();
            $table->integer('mawb_id')->nullable();
            $table->boolean('is_delete')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_mappings');
    }
};
