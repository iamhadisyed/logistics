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
            $table->id();
            $table->string('carrier', 45)->nullable();
            $table->string('logo', 45)->nullable();
            $table->string('cut_off_time', 45)->nullable();
            $table->string('carrier_display_name', 45)->nullable();
            $table->integer('status')->default(1)->comment('0 for deactive, 1 for active, 2 for delete');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->string('currency_code', 3)->default('GBP');
            $table->enum('remotearea_check', ['c', 's'])->default('c');
            $table->boolean('zone_base')->default(false)->comment('1 for carrier and 0 for service');
            $table->enum('zone_type', ['country', 'postcode'])->default('country');
            $table->boolean('on_contract')->default(false);
            $table->boolean('is_gazetteer')->default(false);
            $table->integer('is_reconcile')->default(0);
            $table->timestamps();
            
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
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
