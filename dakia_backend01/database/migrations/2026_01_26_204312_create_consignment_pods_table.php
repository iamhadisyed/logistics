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
        Schema::create('consignment_pods', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('consignmentid')->nullable();
            $table->string('signature', 45)->nullable();
            $table->string('pod_date', 45)->nullable();
            $table->string('pod_image', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_pods');
    }
};
