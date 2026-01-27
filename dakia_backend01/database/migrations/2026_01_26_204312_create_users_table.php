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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_type', ['corporate', 'client', 'admin', 'driver'])->default('client');
            $table->string('user_name', 30)->nullable()->comment('Legacy username field');
            $table->boolean('active_flag')->default(false);
            $table->string('first_name', 100)->nullable();
            $table->string('last_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('country_id')->nullable();
            $table->string('api_key', 100)->nullable();
            $table->string('api_secret', 100)->nullable()->comment('Fixed typo from api_secert');
            $table->dateTime('api_date')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_employee')->default(false);
            $table->integer('warehouse_id')->nullable();
            $table->enum('dashboard', ['corporate', 'operation', 'customer_service', 'account', 'driver'])->default('corporate');
            $table->integer('invalid_login_count')->nullable();
            $table->integer('user_account_id')->nullable();
            $table->dateTime('last_login_date')->nullable();
            $table->integer('added_by')->nullable();
            $table->dateTime('added_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('archive_server')->default(false);
            $table->boolean('carrier_setup_agreement')->default(false);
            $table->enum('receive_email', ['y', 'n'])->default('n');
            $table->date('tc_agreed_date')->nullable();
            $table->enum('is_tc_agreed', ['y', 'n', 'i'])->default('n');
            $table->string('address_2', 50)->nullable();
            $table->string('address_3', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('postcode', 15)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('commission_break_event_amount', 50)->default('');
            $table->boolean('is_sale_pot_eligible')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
