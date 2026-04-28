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
        Schema::create('user_account_service_charges', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('user_account_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('consignment_charges_types_id');
            $table->decimal('charge', 10)->unsigned()->default(0);
            $table->enum('charge_type', ['fixed', 'percentage'])->nullable()->default('fixed');
            $table->bigInteger('added_by')->nullable();
            $table->dateTime('added_date')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_account_service_charges');
    }
};
