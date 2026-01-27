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
        Schema::create('api_data', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignment_id')->nullable();
            $table->text('api_request')->nullable();
            $table->text('api_response')->nullable();
            $table->string('added_by', 45)->nullable();
            $table->dateTime('date_created')->nullable()->useCurrent();
            $table->string('api_reason', 45)->nullable();
            $table->string('type', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_data');
    }
};
