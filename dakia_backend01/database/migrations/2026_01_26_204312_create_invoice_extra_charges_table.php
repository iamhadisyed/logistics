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
        Schema::create('invoice_extra_charges', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('invoice_detail_id');
            $table->integer('charge_type_id');
            $table->integer('agent_id');
            $table->enum('cost_type', ['customer', 'agent'])->default('customer');
            $table->decimal('cost', 11)->default(0);
            $table->string('description')->nullable();
            $table->integer('added_by');
            $table->timestamp('added_date')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_extra_charges');
    }
};
