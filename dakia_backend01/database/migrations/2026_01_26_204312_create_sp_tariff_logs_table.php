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
        Schema::create('sp_tariff_logs', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('account_id')->nullable();
            $table->string('charges_type', 45)->nullable();
            $table->decimal('charges', 10)->nullable()->default(0);
            $table->string('formulla', 100)->nullable();
            $table->timestamp('date_added')->nullable()->useCurrent();
            $table->integer('consignment_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp_tariff_logs');
    }
};
