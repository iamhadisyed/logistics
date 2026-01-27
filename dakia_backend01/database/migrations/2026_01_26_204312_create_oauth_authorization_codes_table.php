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
        Schema::create('oauth_authorization_codes', function (Blueprint $table) {
            $table->string('authorization_code', 40);
            $table->string('client_id', 80);
            $table->unsignedInteger('user_id')->nullable();
            $table->string('redirect_uri', 2000)->nullable();
            $table->timestamp('expires')->useCurrentOnUpdate()->useCurrent();
            $table->string('scope', 2000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_authorization_codes');
    }
};
