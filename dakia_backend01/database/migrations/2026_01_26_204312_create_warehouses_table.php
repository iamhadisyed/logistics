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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->integer('id');
            $table->string('warehouse_name', 50);
            $table->string('addressline1', 100)->nullable();
            $table->string('addressline2', 100);
            $table->string('stateregion', 50);
            $table->string('citytown', 50)->nullable();
            $table->string('postzipcode', 10)->nullable();
            $table->integer('countryid');
            $table->string('phone', 15)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('is_deleted')->default(0);
            $table->dateTime('added_date');
            $table->integer('added_by');
            $table->timestamp('updated_date')->nullable()->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->string('hub', 200)->nullable();
            $table->text('email')->nullable();
            $table->string('warehouse_code', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
