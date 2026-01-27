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
        Schema::create('pre_alerts', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('mawb_id')->nullable();
            $table->string('flight_number', 45)->nullable();
            $table->string('pieces', 45)->nullable();
            $table->string('weight', 45)->nullable();
            $table->string('etd', 45)->nullable();
            $table->string('eta', 45)->nullable();
            $table->string('current_status', 45)->nullable();
            $table->string('date_time', 45)->nullable();
            $table->string('cleared', 45)->nullable();
            $table->string('status', 45)->nullable();
            $table->string('comments', 45)->nullable();
            $table->string('account', 45)->nullable();
            $table->text('files')->nullable();
            $table->string('uploadby', 45)->nullable();
            $table->string('shed', 45)->nullable();
            $table->string('date_entry', 45)->nullable();
            $table->integer('created_by')->nullable();
            $table->string('date_updated', 45)->nullable();
            $table->integer('updated_by')->nullable();
            $table->string('type', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_alerts');
    }
};
