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
        Schema::create('user_services_routings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_account_id');
            $table->integer('country_id');
            $table->decimal('from_weight', 10)->nullable();
            $table->decimal('to_weight', 10)->nullable();
            $table->boolean('status')->default(false);
            $table->integer('service_id')->nullable();
            $table->boolean('is_remotearea')->default(false);
            $table->boolean('is_over_label')->nullable()->default(false);
            $table->integer('added_by');
            $table->boolean('is_agreed')->nullable()->default(false);
            $table->decimal('label_charges', 10)->nullable()->default(0);
            $table->boolean('is_dead_weight')->nullable()->default(false);
            $table->integer('is_over_size')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_services_routings');
    }
};
