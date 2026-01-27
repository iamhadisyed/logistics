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
        Schema::create('login_requests', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('user_id')->nullable();
            $table->string('user_name', 45)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('login_status')->default(false);
            $table->timestamp('login_time')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('logout_time')->nullable();
            $table->string('session_id', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_requests');
    }
};
