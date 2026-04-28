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
        Schema::create('user_services_charges', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_account_id')->nullable();
            $table->integer('service_id');
            $table->decimal('sur_charge', 10)->nullable();
            $table->enum('sur_charge_type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('extra_charge', 10)->nullable();
            $table->enum('extra_charge_type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('discount', 10)->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('percentage');
            $table->enum('additional_charges_type', ['fixed', 'percentage', 'per_kg'])->nullable()->default('per_kg');
            $table->decimal('additional_charges', 10)->nullable();
            $table->text('additional_charges_details')->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->decimal('over_weight', 10)->nullable()->comment('Per peice charges');
            $table->decimal('over_size', 10)->nullable()->comment('Per peice charges');
            $table->enum('over_weight_type', ['fixed', 'percentage', 'per_pcs'])->nullable()->default('per_pcs');
            $table->enum('over_size_type', ['fixed', 'percentage', 'per_pcs'])->nullable()->default('per_pcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_services_charges');
    }
};
