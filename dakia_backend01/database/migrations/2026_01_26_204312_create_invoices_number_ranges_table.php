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
        Schema::create('invoices_number_ranges', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->tinyInteger('is_default')->default(0);
            $table->unsignedBigInteger('range_start');
            $table->unsignedBigInteger('range_end');
            $table->unsignedBigInteger('next_number');
            $table->dateTime('increment_date')->nullable();
            $table->integer('user_account_id');
            $table->string('prefix', 10)->nullable();
            $table->string('sufix', 10)->nullable();
            $table->enum('range_type', ['AUTO', 'MANUAL', 'CREDIT'])->nullable();
            $table->timestamp('date_created')->nullable()->useCurrent();
            $table->dateTime('date_updated')->nullable();
            $table->integer('addedby')->nullable();
            $table->integer('updatedby')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_number_ranges');
    }
};
