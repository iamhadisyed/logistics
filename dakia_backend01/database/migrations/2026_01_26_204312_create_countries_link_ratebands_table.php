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
        Schema::create('countries_link_ratebands', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('rateband_id');
            $table->integer('orderq')->default(0);
            $table->tinyInteger('active')->nullable()->default(1);
            $table->char('deletedq', 1)->nullable()->default('N');
            $table->dateTime('added_on')->nullable();
            $table->string('added_by', 100)->nullable();
            $table->dateTime('changed_on')->nullable();
            $table->string('changed_by', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries_link_ratebands');
    }
};
