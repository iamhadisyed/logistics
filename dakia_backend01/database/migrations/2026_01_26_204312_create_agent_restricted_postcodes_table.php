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
        Schema::create('agent_restricted_postcodes', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('agent_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->string('postcode_city', 100)->nullable();
            $table->boolean('is_city')->nullable()->default(false)->comment('city = 1
postcode = 0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_restricted_postcodes');
    }
};
