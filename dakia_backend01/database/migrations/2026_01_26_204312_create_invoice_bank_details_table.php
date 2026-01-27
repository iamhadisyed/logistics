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
        Schema::create('invoice_bank_details', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_account_id');
            $table->string('account_title', 45)->nullable();
            $table->string('account_sortcode', 10)->nullable();
            $table->integer('account_number')->nullable();
            $table->string('account_iban', 45)->nullable();
            $table->string('bank_name', 45)->nullable();
            $table->string('bank_branch', 45)->nullable();
            $table->string('bank_address', 100)->nullable();
            $table->timestamp('date_added')->nullable()->useCurrent();
            $table->integer('added_by')->nullable();
            $table->boolean('status')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_bank_details');
    }
};
