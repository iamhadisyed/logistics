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
        Schema::create('ukmail_authentications', function (Blueprint $table) {
            $table->integer('id');
            $table->string('authentication_token')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('user_account', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ukmail_authentications');
    }
};
