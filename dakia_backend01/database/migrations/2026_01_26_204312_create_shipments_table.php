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
        Schema::create('shipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 36)->unique();
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('service_type');
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->string('reference')->index();
            $table->text('notes')->nullable();
            $table->string('company')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postcode');
            $table->unsignedInteger('country_id');
            $table->string('sender_company')->nullable();
            $table->string('sender_contact')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_telephone')->nullable();
            $table->string('sender_address_line_1')->nullable();
            $table->string('sender_address_line_2')->nullable();
            $table->string('sender_address_line_3')->nullable();
            $table->string('sender_city')->nullable();
            $table->string('sender_state')->nullable();
            $table->string('sender_postcode')->nullable();
            $table->unsignedInteger('sender_country_id')->nullable();
            $table->enum('status', ['draft', 'booked', 'label_generated'])->default('draft');
            $table->boolean('label_generated')->default(false);
            $table->dateTime('label_generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
