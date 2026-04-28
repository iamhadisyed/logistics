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
        Schema::create('consignment_holds', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('userid')->nullable();
            $table->string('comments', 500)->nullable();
            $table->string('tracking_number', 45)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('action', 45)->nullable();
            $table->string('reason_tag', 45)->nullable();
            $table->string('weight', 45)->nullable();
            $table->string('width', 45)->nullable();
            $table->string('height', 45)->nullable();
            $table->string('length', 45)->nullable();
            $table->string('volume', 45)->nullable();
            $table->string('image', 200)->nullable();
            $table->string('account', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_holds');
    }
};
