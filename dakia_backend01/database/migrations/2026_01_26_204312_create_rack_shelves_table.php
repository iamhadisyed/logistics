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
        Schema::create('rack_shelves', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('rack_id');
            $table->integer('shelf_no');
            $table->boolean('is_filled')->default(false);
            $table->timestamp('updated_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rack_shelves');
    }
};
