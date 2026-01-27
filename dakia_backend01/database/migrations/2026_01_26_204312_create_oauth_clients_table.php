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
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->string('client_id', 80);
            $table->string('client_secret', 80)->nullable();
            $table->string('redirect_uri', 2000)->nullable();
            $table->string('grant_types', 80)->nullable();
            $table->string('scope', 100)->nullable();
            $table->unsignedInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }
};
