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
        Schema::create('customized_user_services_routings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('service_id');
            $table->integer('user_account_id');
            $table->dateTime('routing_added_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customized_user_services_routings');
    }
};
