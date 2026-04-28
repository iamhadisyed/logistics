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
        Schema::create('credit_note_details', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('credit_note_id');
            $table->string('hawb', 50)->nullable();
            $table->dateTime('date_booked')->nullable();
            $table->string('reference', 45)->nullable();
            $table->decimal('invoice_amount', 10)->nullable()->default(0);
            $table->decimal('chargeable_amount', 10)->nullable()->default(0);
            $table->decimal('credit_amount', 10)->nullable()->default(0);
            $table->string('description')->nullable();
            $table->enum('is_vatable', ['YES', 'NO'])->nullable()->default('NO');
            $table->timestamp('date_created')->nullable()->useCurrent();
            $table->integer('updated_by');
            $table->dateTime('date_updated');
            $table->decimal('vat_amount', 10)->nullable()->default(0);
            $table->integer('added_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_note_details');
    }
};
