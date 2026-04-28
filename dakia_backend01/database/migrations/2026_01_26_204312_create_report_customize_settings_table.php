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
        Schema::create('report_customize_settings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('account_id')->nullable();
            $table->string('report_title', 100)->nullable();
            $table->string('report_key', 100)->nullable();
            $table->longText('fields_data')->nullable();
            $table->dateTime('date_added')->nullable();
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
        Schema::dropIfExists('report_customize_settings');
    }
};
