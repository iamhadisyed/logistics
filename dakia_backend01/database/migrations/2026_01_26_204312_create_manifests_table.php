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
        Schema::create('manifests', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_id')->nullable();
            $table->string('file_name', 500)->nullable();
            $table->string('label_link', 100)->nullable();
            $table->dateTime('date_created')->nullable();
            $table->string('pieces', 45)->nullable();
            $table->bigInteger('agent_id')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->integer('service_id')->nullable();
            $table->string('handling', 200)->nullable();
            $table->string('pdf_file', 500)->nullable();
            $table->string('flight_number', 45)->nullable();
            $table->string('mawb', 45)->nullable();
            $table->string('type', 45)->nullable();
            $table->text('collection_comment')->nullable();
            $table->dateTime('collection_date')->nullable();
            $table->dateTime('collection_date_to')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->text('delivery_note')->nullable();
            $table->string('signature', 100)->nullable();
            $table->integer('pickup_id')->nullable();
            $table->integer('route_warehouse_id')->nullable();
            $table->dateTime('routing_email_date')->nullable();
            $table->dateTime('date_received')->nullable();
            $table->string('received_by', 45)->nullable();
            $table->string('name_of_driver', 100)->nullable();
            $table->string('licence_number', 100)->nullable();
            $table->string('account_owner', 45)->nullable();
            $table->string('number_bag', 45)->nullable();
            $table->string('product', 500)->nullable();
            $table->string('carrier_note', 5000)->nullable();
            $table->string('carrier_pdf', 500)->nullable();
            $table->integer('carrier_id')->nullable();
            $table->enum('is_deleted', ['Y', 'N'])->nullable()->default('N');
            $table->enum('is_dispatched', ['Y', 'N'])->default('N');
            $table->enum('is_send_email', ['Y', 'N'])->default('N');
            $table->enum('manifest_by', ['operation', 'client'])->default('client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifests');
    }
};
