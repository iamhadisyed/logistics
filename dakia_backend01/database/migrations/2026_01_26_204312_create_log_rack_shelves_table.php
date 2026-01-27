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
        Schema::create('log_rack_shelves', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('rack_shelf_id');
            $table->bigInteger('rack_shelf_item_id');
            $table->integer('customer_id');
            $table->text('remarks')->nullable();
            $table->dateTime('in_date');
            $table->integer('in_by');
            $table->dateTime('out_date')->nullable();
            $table->integer('out_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_rack_shelves');
    }
};
