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
        Schema::create('agent_data', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('agent_code', 45)->nullable();
            $table->string('agent_name', 45)->nullable();
            $table->tinyInteger('active')->nullable();
            $table->string('contact_name', 45)->nullable();
            $table->string('address_line_1', 45)->nullable();
            $table->string('address_line_2', 45)->nullable();
            $table->string('address_line_3', 45)->nullable();
            $table->integer('country_id')->nullable();
            $table->string('county', 45)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('postcode', 45)->nullable();
            $table->string('telephone', 45)->nullable();
            $table->string('mobile', 45)->nullable();
            $table->string('fax', 45)->nullable();
            $table->string('email', 1000)->nullable();
            $table->string('alternative_contact_1', 45)->nullable();
            $table->string('alternative1_telephone', 45)->nullable();
            $table->string('alternative1_mobile', 45)->nullable();
            $table->string('alternative1_fax', 45)->nullable();
            $table->string('alternative1_email', 45)->nullable();
            $table->string('alternative_contact_2', 45)->nullable();
            $table->string('alternative2_telephone', 45)->nullable();
            $table->string('alternative2_mobile', 45)->nullable();
            $table->string('alternative2_fax', 45)->nullable();
            $table->string('alternative2_email', 45)->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('is_deleted')->nullable()->default(false);
            $table->string('logo', 45)->nullable();
            $table->enum('agent_type', ['carrier', 'dispatch', 'both'])->default('carrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_data');
    }
};
