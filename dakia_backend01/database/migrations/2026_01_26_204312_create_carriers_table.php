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
        Schema::create('carriers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('carrier', 45)->nullable();
            $table->string('logo', 45)->nullable();
            $table->string('cut_off_time', 45)->nullable();
            $table->string('carrier_display_name', 45)->nullable();
            $table->integer('status')->nullable()->comment('0 for deactive
1 for  active
2 for delete');
            $table->integer('country_id')->nullable();
            $table->integer('carrier_id')->nullable();
            $table->string('currency_code', 3)->nullable()->default('GBP');
            $table->enum('remotearea_check', ['c', 's'])->default('c');
            $table->boolean('zone_base')->nullable()->default(false)->comment('1 for carrier and 0 for service');
            $table->enum('zone_type', ['country', 'postcode'])->nullable()->default('country');
            $table->boolean('on_contract')->nullable()->default(false);
            $table->boolean('is_gazetteer')->default(false);
            $table->integer('is_reconcile')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
