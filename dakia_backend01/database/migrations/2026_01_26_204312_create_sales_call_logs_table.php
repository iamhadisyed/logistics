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
        Schema::create('sales_call_logs', function (Blueprint $table) {
            $table->integer('id');
            $table->date('date_call')->nullable();
            $table->timestamp('meeting_date')->nullable();
            $table->string('customer_code', 45)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->text('detail_discussed')->nullable();
            $table->string('document_link', 200)->nullable();
            $table->timestamp('follow_meeting_date')->nullable();
            $table->integer('userid')->nullable();
            $table->char('email_send', 1)->nullable()->default('N');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_call_logs');
    }
};
