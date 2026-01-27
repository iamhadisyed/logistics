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
        Schema::create('reamus_sites', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('reamus_id', 10)->nullable();
            $table->string('site', 100)->nullable();
            $table->string('reamus_id2', 10)->nullable();
            $table->string('country_code', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reamus_sites');
    }
};
