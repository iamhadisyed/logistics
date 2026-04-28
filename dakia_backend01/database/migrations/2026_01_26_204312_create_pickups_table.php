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
        Schema::create('pickups', function (Blueprint $table) {
            $table->integer('id');
            $table->string('pickup_number', 100)->nullable();
            $table->timestamp('pickup_date')->nullable();
            $table->string('delivery_note', 100)->nullable();
            $table->string('pick_up_pdf', 100)->nullable();
            $table->string('collection_pdf', 100)->nullable();
            $table->string('collection_address', 300)->nullable();
            $table->string('address_line_1', 100)->nullable();
            $table->string('address_line_2', 100)->nullable();
            $table->string('address_line_3', 100)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('country', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickups');
    }
};
