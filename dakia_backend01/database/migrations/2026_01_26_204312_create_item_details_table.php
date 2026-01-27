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
        Schema::create('item_details', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignment_id');
            $table->string('session_id', 50);
            $table->integer('parcel_count');
            $table->text('item_detail');
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_details');
    }
};
