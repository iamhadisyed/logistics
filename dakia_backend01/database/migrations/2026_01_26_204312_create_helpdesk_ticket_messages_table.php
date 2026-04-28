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
        Schema::create('helpdesk_ticket_messages', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('ticketid')->default(0);
            $table->string('message');
            $table->string('attachment')->nullable();
            $table->integer('addedby')->default(0);
            $table->dateTime('added_date');
            $table->integer('updatedby')->nullable();
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_ticket_messages');
    }
};
