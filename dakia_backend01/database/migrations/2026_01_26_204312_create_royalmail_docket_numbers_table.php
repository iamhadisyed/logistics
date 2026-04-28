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
        Schema::create('royalmail_docket_numbers', function (Blueprint $table) {
            $table->integer('id');
            $table->string('tracking_number', 45)->nullable();
            $table->string('docket_number', 45)->nullable();
            $table->string('file_name', 45)->nullable();
            $table->timestamp('date_created')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('royalmail_docket_numbers');
    }
};
