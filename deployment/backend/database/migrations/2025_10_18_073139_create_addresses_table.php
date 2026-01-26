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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 45)->nullable();
            $table->string('company', 45)->nullable();
            $table->string('contact', 45)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('address_line_1', 45)->nullable();
            $table->string('address_line_2', 45)->nullable();
            $table->string('address_line_3', 45)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('country', 3)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('state', 45)->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
