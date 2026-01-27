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
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->integer('id');
            $table->string('credit_note_number', 120)->nullable();
            $table->integer('user_account_id');
            $table->enum('invoice_type', ['INV', 'MNI']);
            $table->string('invoice_number', 45)->nullable();
            $table->enum('credit_note_type', ['PARTIAL', 'FULL', 'OTHER'])->nullable();
            $table->text('hawb')->nullable();
            $table->text('credit_note_heading')->nullable();
            $table->dateTime('credit_date')->nullable();
            $table->decimal('net_amount', 10)->nullable();
            $table->decimal('vat_amount', 10)->nullable();
            $table->decimal('credit_total', 10)->nullable();
            $table->integer('credit_note_by')->nullable();
            $table->string('pdf', 200)->nullable();
            $table->boolean('is_email')->nullable()->default(false);
            $table->boolean('is_read')->nullable()->default(false);
            $table->integer('added_by')->nullable();
            $table->timestamp('date_created')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('currency_id')->nullable()->default(2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
