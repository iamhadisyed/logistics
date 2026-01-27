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
        Schema::create('ukpostcodelatlngs', function (Blueprint $table) {
            $table->integer('id');
            $table->string('postcode', 8);
            $table->decimal('latitude', 18, 15);
            $table->decimal('longitude', 18, 15);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ukpostcodelatlngs');
    }
};
