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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_account_id');
            $table->integer('carrier_id');
            $table->integer('service_id')->nullable();
            $table->string('name', 45)->nullable();
            $table->boolean('status')->nullable()->default(false);
            $table->integer('currency_id')->nullable()->default(2);
            $table->enum('tariff_type', ['customer', 'supplier'])->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('tariffs_pricing_rule_id')->nullable()->default(0);
            $table->timestamp('date_added')->nullable()->useCurrent();
            $table->integer('added_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
