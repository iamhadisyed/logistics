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
        Schema::create('not_found_records', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('mawb', 45)->nullable();
            $table->string('bag_number', 45)->nullable();
            $table->string('tracking_number', 45)->nullable();
            $table->bigInteger('scanned_by')->nullable();
            $table->decimal('length')->nullable();
            $table->decimal('width')->nullable();
            $table->decimal('height')->nullable();
            $table->decimal('weight')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('reason', 45)->nullable();
            $table->string('image', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('not_found_records');
    }
};
