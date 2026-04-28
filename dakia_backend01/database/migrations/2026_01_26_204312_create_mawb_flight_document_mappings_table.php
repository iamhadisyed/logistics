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
        Schema::create('mawb_flight_document_mappings', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('country_id');
            $table->bigInteger('document_id');
            $table->bigInteger('template_id');
            $table->bigInteger('added_by');
            $table->dateTime('added_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mawb_flight_document_mappings');
    }
};
