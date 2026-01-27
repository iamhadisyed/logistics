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
        Schema::create('forget_password_requests', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('user_name', 45);
            $table->string('ip_address', 45);
            $table->string('user_agent', 45)->nullable();
            $table->string('token', 100);
            $table->timestamp('date_created')->useCurrent();
            $table->dateTime('date_expire')->nullable();
            $table->boolean('is_expire')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forget_password_requests');
    }
};
