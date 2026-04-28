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
        Schema::create('products', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('country_id')->nullable();
            $table->string('product_name', 45);
            $table->decimal('insurance', 5)->nullable();
            $table->string('description')->nullable();
            $table->integer('status')->nullable()->default(1)->comment('0 - in-active
1-active
2 -deleted');
            $table->decimal('from_weight', 10, 3)->nullable();
            $table->decimal('to_weight', 10, 3)->nullable();
            $table->string('logo', 45)->nullable();
            $table->integer('length')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->decimal('vol_weight', 10, 3)->nullable();
            $table->integer('vol_denominator')->nullable();
            $table->integer('is_untrack')->nullable();
            $table->decimal('remotearea_charges', 10, 3)->nullable();
            $table->decimal('fuel_charges', 10, 3)->nullable();
            $table->timestamp('added_date')->nullable()->useCurrent();
            $table->integer('added_by')->nullable();
            $table->string('transit_time', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
