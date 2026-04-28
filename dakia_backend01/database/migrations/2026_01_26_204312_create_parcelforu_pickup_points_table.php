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
        Schema::create('parcelforu_pickup_points', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('name', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('address_line_1', 250)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('country_iso', 2)->nullable();
            $table->string('statuscode', 2)->nullable();
            $table->string('status_description', 100)->nullable();
            $table->string('latitude', 45)->nullable();
            $table->string('longitude', 45)->nullable();
            $table->string('mon', 45)->nullable();
            $table->string('tue', 45)->nullable();
            $table->string('wed', 45)->nullable();
            $table->string('thu', 45)->nullable();
            $table->string('fri', 45)->nullable();
            $table->string('sat', 45)->nullable();
            $table->string('sun', 45)->nullable();
            $table->string('label_routing', 45)->nullable();
            $table->string('branch_id', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelforu_pickup_points');
    }
};
