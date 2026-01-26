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
        // Main Shipments Table
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('service_type');
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->string('reference')->index(); // HAWB
            $table->text('notes')->nullable();
            
            // Receiver Details
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
            
            // Sender Details
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

        // Parcels Table
        Schema::create('shipment_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->decimal('weight', 8, 3);
            $table->decimal('length', 8, 2);
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Items Table
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->constrained('shipment_parcels')->onDelete('cascade');
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('weight', 8, 3);
            $table->decimal('value', 10, 2);
            $table->timestamps();
        });

        // History Table
        Schema::create('shipment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->string('action');
            $table->text('details')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_history');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipment_parcels');
        Schema::dropIfExists('shipments');
    }
};
