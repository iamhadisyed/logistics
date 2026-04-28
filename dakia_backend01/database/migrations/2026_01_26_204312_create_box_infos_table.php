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
        Schema::create('box_infos', function (Blueprint $table) {
            $table->integer('id');
            $table->string('box_number', 50)->nullable();
            $table->string('box_size', 20)->nullable();
            $table->decimal('box_weight', 6)->nullable();
            $table->text('tracking_numbers')->nullable();
            $table->string('manifest_number', 25)->nullable();
            $table->string('mawb_number', 25)->nullable();
            $table->dateTime('date_submitted')->nullable();
            $table->dateTime('date_scanned')->nullable();
            $table->text('api_data')->nullable();
            $table->string('status', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_infos');
    }
};
