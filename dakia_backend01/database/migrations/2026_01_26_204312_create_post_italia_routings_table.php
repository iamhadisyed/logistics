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
        Schema::create('post_italia_routings', function (Blueprint $table) {
            $table->integer('id');
            $table->string('zip_code', 45);
            $table->string('routing_file', 45);
            $table->string('province', 45);
            $table->string('province_iso_code', 45);
            $table->string('sortation_name', 45)->nullable();
            $table->string('sortation_id', 45)->nullable();
            $table->string('sortation_name_on_bag', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_italia_routings');
    }
};
