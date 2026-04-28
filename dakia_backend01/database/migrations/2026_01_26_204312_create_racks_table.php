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
        Schema::create('racks', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('warehouse_id');
            $table->string('title');
            $table->string('short_title', 100);
            $table->integer('rack_rows');
            $table->integer('rack_cols')->nullable();
            $table->string('shelf_dimension', 50);
            $table->decimal('shelf_max_weight', 11);
            $table->boolean('is_york')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('added_date');
            $table->integer('added_by');
            $table->timestamp('updated_date')->useCurrent();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('racks');
    }
};
