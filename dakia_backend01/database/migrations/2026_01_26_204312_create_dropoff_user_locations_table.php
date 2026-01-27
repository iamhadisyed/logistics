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
        Schema::create('dropoff_user_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->integer('service_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('companyname', 100)->nullable();
            $table->string('address_line_1', 50)->nullable();
            $table->string('address_line_2', 50)->nullable();
            $table->string('address_line_3', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('country', 45)->nullable();
            $table->string('telephone', 17)->nullable();
            $table->string('mon', 20)->nullable();
            $table->string('tue', 20)->nullable();
            $table->string('wed', 20)->nullable();
            $table->string('thu', 20)->nullable();
            $table->string('fri', 20)->nullable();
            $table->string('sat', 20)->nullable();
            $table->string('sun', 20)->nullable();
            $table->string('lat', 20)->nullable();
            $table->string('lng', 20)->nullable();
            $table->bigInteger('added_by');
            $table->dateTime('added_date')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dropoff_user_locations');
    }
};
