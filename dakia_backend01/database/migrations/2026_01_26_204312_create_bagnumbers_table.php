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
        Schema::create('bagnumbers', function (Blueprint $table) {
            $table->integer('id');
            $table->string('hawb', 45)->nullable();
            $table->string('consignment_id', 45)->nullable();
            $table->integer('parcel_id')->nullable();
            $table->string('tag_number', 45)->nullable();
            $table->integer('bag_number')->nullable();
            $table->string('service', 45)->nullable();
            $table->string('value', 45)->nullable();
            $table->string('weight', 45)->nullable();
            $table->string('number_pieces', 45)->nullable();
            $table->string('label_file', 45)->nullable();
            $table->string('manifest_file', 45)->nullable();
            $table->string('status', 45)->nullable();
            $table->string('date_created', 45)->nullable();
            $table->string('date_printed', 45)->nullable();
            $table->string('flightnumber', 45)->nullable();
            $table->integer('flight_id')->nullable();
            $table->integer('mawb')->nullable();
            $table->string('accountnumber', 45)->nullable();
            $table->string('destination_addr', 45)->nullable();
            $table->string('country', 45)->nullable();
            $table->string('dispatchdate', 45)->nullable();
            $table->string('mail_number', 45)->nullable();
            $table->string('last_bag', 45)->nullable();
            $table->string('flight_datetime', 45)->nullable();
            $table->string('bag_type', 10)->nullable();
            $table->string('is_track', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bagnumbers');
    }
};
