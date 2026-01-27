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
        Schema::create('oauth_public_keys', function (Blueprint $table) {
            $table->string('client_id', 80)->nullable();
            $table->string('public_key', 8000)->nullable();
            $table->string('private_key', 8000)->nullable();
            $table->string('encryption_algorithm', 80)->nullable()->default('RS256');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_public_keys');
    }
};
