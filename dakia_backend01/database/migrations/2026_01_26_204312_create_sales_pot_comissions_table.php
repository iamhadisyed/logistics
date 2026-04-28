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
        Schema::create('sales_pot_comissions', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->dateTime('date_added');
            $table->integer('no_of_shipments')->default(0);
            $table->decimal('comission', 10)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->integer('paid_by')->nullable();
            $table->dateTime('paid_date')->nullable();
            $table->decimal('company_comission', 10)->nullable();
            $table->decimal('sales_comission', 10)->nullable();
            $table->text('salepot_table_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_pot_comissions');
    }
};
