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
        Schema::create('document_types', function (Blueprint $table) {
            $table->integer('id');
            $table->string('document_name')->nullable();
            $table->string('description')->nullable();
            $table->enum('document_type', ['company_contract', 'service_contract', 'agent_contract', 'service_agent'])->nullable();
            $table->enum('is_active', ['0', '1'])->nullable()->default('1');
            $table->bigInteger('added_by')->nullable();
            $table->dateTime('added_date');
            $table->bigInteger('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
            $table->enum('is_delete', ['0', '1'])->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
