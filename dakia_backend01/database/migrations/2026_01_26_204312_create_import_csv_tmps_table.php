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
        Schema::create('import_csv_tmps', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('account')->nullable();
            $table->string('hawb')->nullable();
            $table->string('service')->nullable();
            $table->string('service_code')->nullable();
            $table->string('reference')->nullable();
            $table->string('date_submitted')->nullable();
            $table->string('company')->nullable();
            $table->string('contact')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('post_code')->nullable();
            $table->string('telephone')->nullable();
            $table->string('number_of_pieces')->nullable();
            $table->string('weight')->nullable();
            $table->string('description')->nullable();
            $table->string('value')->nullable();
            $table->string('currency')->nullable();
            $table->string('notes')->nullable();
            $table->string('routing_non_routing')->nullable();
            $table->string('full_pallet')->nullable();
            $table->string('half_pallet')->nullable();
            $table->string('quarter_pallet')->nullable();
            $table->string('all_weight')->nullable();
            $table->string('width')->nullable();
            $table->string('heigh')->nullable();
            $table->string('length')->nullable();
            $table->string('item_type')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('email')->nullable();
            $table->string('flight_number')->nullable();
            $table->string('bag_number')->nullable();
            $table->string('mawb')->nullable();
            $table->boolean('is_complete')->default(false);
            $table->boolean('status')->default(false);
            $table->text('message')->nullable();
            $table->string('batch_number', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_csv_tmps');
    }
};
