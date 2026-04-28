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
        Schema::create('cacesa_routines', function (Blueprint $table) {
            $table->integer('id');
            $table->string('type', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('agency', 45)->nullable();
            $table->string('route_description', 45)->nullable();
            $table->string('route_id', 45)->nullable();
            $table->string('courier', 45)->nullable();
            $table->string('routing', 100)->nullable();
            $table->string('country', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cacesa_routines');
    }
};
