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
        Schema::create('consignment_charges_types', function (Blueprint $table) {
            $table->integer('id');
            $table->string('title');
            $table->string('charges_key');
            $table->enum('charge_type', ['both', 'customer', 'agent'])->default('both');
            $table->boolean('apply_per_kg')->default(false);
            $table->boolean('is_extra_charge')->default(false);
            $table->boolean('is_vat')->default(false);
            $table->boolean('has_account_default_value')->default(false);
            $table->boolean('is_replace_charges')->nullable()->default(false);
            $table->boolean('status')->default(true)->comment('0 for inactive 1 for active 2 for deleted');
            $table->boolean('is_delete')->nullable()->default(false);
            $table->integer('added_by');
            $table->timestamp('added_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_charges_types');
    }
};
