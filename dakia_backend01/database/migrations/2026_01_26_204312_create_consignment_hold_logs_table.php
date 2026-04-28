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
        Schema::create('consignment_hold_logs', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignment_id')->nullable();
            $table->integer('user_account_id_from')->nullable();
            $table->integer('user_account_id_to')->nullable();
            $table->enum('status', ['hold', 'unhold'])->nullable();
            $table->string('reason', 500)->nullable();
            $table->integer('added_by')->nullable();
            $table->timestamp('date_added')->nullable()->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->dateTime('date_updated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_hold_logs');
    }
};
