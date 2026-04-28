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
        Schema::create('rack_shelf_items', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('goods_name');
            $table->string('tracking_number', 45)->nullable();
            $table->text('description')->nullable();
            $table->decimal('weight', 11);
            $table->string('dimension', 50);
            $table->dateTime('added_date');
            $table->integer('added_by');
            $table->timestamp('updated_date')->useCurrent();
            $table->integer('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rack_shelf_items');
    }
};
