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
        Schema::create('mawbs', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('mawb_number', 50);
            $table->integer('mawb_source_country_id');
            $table->integer('mawb_source_warehouse_id')->nullable();
            $table->integer('mawb_destination_country_id');
            $table->integer('mawb_destination_warehouse_id')->nullable();
            $table->enum('is_active', ['y', 'n'])->default('n');
            $table->integer('added_by')->nullable();
            $table->dateTime('added_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
            $table->enum('mawb_status', ['d', 'pd', 'o'])->default('o')->comment('o for open, d for disptach, pd for partial dispatch');
            $table->string('manifest_label')->nullable();
            $table->string('mawb_lv_manifest')->nullable();
            $table->string('mawb_hv_manifest')->nullable();
            $table->enum('bagging_type', ['p', 'f'])->nullable()->comment('p for postal and f for freight');
            $table->enum('bag_is_hv_lv', ['y', 'n'])->nullable()->default('y');
            $table->string('mawb_class')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mawbs');
    }
};
