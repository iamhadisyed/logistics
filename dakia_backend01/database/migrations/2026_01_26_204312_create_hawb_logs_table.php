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
        Schema::create('hawb_logs', function (Blueprint $table) {
            $table->integer('id');
            $table->string('hawb', 45)->nullable();
            $table->string('status', 10)->nullable();
            $table->timestamp('date_created')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hawb_logs');
    }
};
