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
        Schema::create('baggings', function (Blueprint $table) {
            $table->integer('id');
            $table->string('bagnumber', 45)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('csv', 200)->nullable();
            $table->string('pdf', 200)->nullable();
            $table->integer('manifestid')->nullable();
            $table->string('account', 45)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('manifest_pdf', 200)->nullable();
            $table->tinyInteger('bag_status')->nullable()->default(0)->comment('0 for Open,1 for Hold,2 for Close');
            $table->dateTime('date_updated')->nullable();
            $table->tinyInteger('isdeleted')->nullable()->default(0);
            $table->string('service', 200)->nullable();
            $table->integer('serviceid')->nullable()->comment('Assigned service id to bag');
            $table->string('country', 45)->nullable()->comment('Assigned country id to bag');
            $table->string('country_iso_code', 2)->nullable();
            $table->string('bag_type', 45)->nullable()->comment('0 for Mixed and 1 for normal');
            $table->decimal('actual_weight', 10)->nullable()->comment('after scaning Calculated weight');
            $table->decimal('length', 10)->nullable();
            $table->decimal('width', 10)->nullable();
            $table->decimal('height', 10)->nullable();
            $table->integer('pieces')->nullable();
            $table->decimal('weight', 10, 3)->nullable()->comment('Customers assigned weight');
            $table->string('bag_label')->nullable();
            $table->integer('bag_source_country_id')->nullable();
            $table->integer('bag_source_warehouse_id')->nullable();
            $table->integer('bag_destination_country_id')->nullable();
            $table->integer('bag_destination_warehouse_id')->nullable();
            $table->boolean('is_closed')->nullable()->default(false);
            $table->bigInteger('closed_by')->nullable();
            $table->dateTime('closed_date')->nullable();
            $table->bigInteger('reopen_by')->nullable();
            $table->dateTime('reopen_date')->nullable();
            $table->string('bag_manifest')->nullable();
            $table->enum('bag_value', ['hv', 'lv', 'mv'])->default('lv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baggings');
    }
};
