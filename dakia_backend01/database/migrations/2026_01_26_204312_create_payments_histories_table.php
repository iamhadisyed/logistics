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
        Schema::create('payments_histories', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedBigInteger('account_id');
            $table->string('paypal_payment_id', 100)->nullable();
            $table->string('txn_id', 150)->nullable();
            $table->string('billing_id', 150)->nullable();
            $table->string('sender_email', 150)->nullable();
            $table->double('amount');
            $table->integer('amount_currency_id');
            $table->enum('payment_method', ['cash', 'paypal', 'bank_transfer', 'cheque'])->nullable();
            $table->string('payment_detail', 150)->nullable();
            $table->integer('user_currency_id');
            $table->decimal('debit', 11)->nullable()->default(0);
            $table->decimal('credit', 11)->nullable()->default(0);
            $table->string('module_name', 150)->nullable();
            $table->string('module_id', 50)->nullable();
            $table->integer('invoice_id')->nullable();
            $table->string('payment_status', 50)->nullable();
            $table->enum('is_completed', ['yes', 'no'])->default('no');
            $table->timestamp('date_added')->nullable()->useCurrent();
            $table->integer('added_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_histories');
    }
};
