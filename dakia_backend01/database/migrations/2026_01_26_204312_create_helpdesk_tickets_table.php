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
        Schema::create('helpdesk_tickets', function (Blueprint $table) {
            $table->integer('id');
            $table->string('ticket_code');
            $table->integer('department_id')->default(0);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent', 'critical'])->default('low');
            $table->string('subject');
            $table->enum('status', ['open', 'in_progress', 'cancel', 'fixed', 'close'])->default('open');
            $table->integer('addedby')->default(0);
            $table->dateTime('added_date');
            $table->integer('updatedby')->nullable()->default(0);
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_tickets');
    }
};
