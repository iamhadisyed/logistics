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
        Schema::create('dpdgroups', function (Blueprint $table) {
            $table->integer('id');
            $table->string('lookup_code', 15)->nullable();
            $table->string('list_of_available_services', 100)->nullable();
            $table->string('Business', 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpdgroups');
    }
};
