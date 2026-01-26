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
        // Separate call for index drop to handle existence check
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        } catch (\Exception $e) {
            // Ignore if it doesn't exist
        }

        Schema::table('users', function (Blueprint $table) {
            // Legacy user table columns - adding after existing columns
            $table->enum('user_type', ['corporate', 'client', 'admin', 'driver'])->default('client')->after('password');
            $table->string('user_name', 30)->nullable()->after('user_type')->comment('Legacy username field');
            $table->boolean('active_flag')->default(false)->after('user_name');
            $table->string('first_name', 100)->nullable()->after('active_flag');
            $table->string('last_name', 255)->nullable()->after('first_name');
            $table->string('address', 255)->nullable()->after('last_name');
            $table->string('phone', 20)->nullable()->after('address');
            $table->integer('country_id')->nullable()->after('phone');
            $table->string('api_key', 100)->nullable()->after('country_id');
            $table->string('api_secret', 100)->nullable()->after('api_key')->comment('Fixed typo from api_secert');
            $table->datetime('api_date')->nullable()->after('api_secret');
            $table->string('profile_image')->nullable()->after('api_date');
            $table->boolean('is_employee')->default(false)->after('profile_image');
            $table->integer('warehouse_id')->nullable()->after('is_employee');
            $table->enum('dashboard', ['corporate', 'operation', 'customer_service', 'account', 'driver'])->default('corporate')->after('warehouse_id');
            $table->integer('invalid_login_count')->nullable()->after('dashboard');
            $table->integer('user_account_id')->nullable()->after('invalid_login_count');
            $table->datetime('last_login_date')->nullable()->after('user_account_id');
            $table->integer('added_by')->nullable()->after('last_login_date');
            $table->datetime('added_date')->nullable()->after('added_by');
            $table->integer('updated_by')->nullable()->after('added_date');
            $table->datetime('updated_date')->nullable()->after('updated_by');
            $table->boolean('is_deleted')->default(false)->after('updated_date');
            $table->boolean('archive_server')->default(false)->after('is_deleted');
            $table->boolean('carrier_setup_agreement')->default(false)->after('archive_server');
            $table->enum('receive_email', ['y', 'n'])->default('n')->after('carrier_setup_agreement');
            $table->date('tc_agreed_date')->nullable()->after('receive_email');
            $table->enum('is_tc_agreed', ['y', 'n', 'i'])->default('n')->after('tc_agreed_date');
            $table->string('address_2', 50)->nullable()->after('is_tc_agreed');
            $table->string('address_3', 50)->nullable()->after('address_2');
            $table->string('city', 50)->nullable()->after('address_3');
            $table->string('postcode', 15)->nullable()->after('city');
            $table->string('state', 50)->nullable()->after('postcode');
            $table->string('commission_break_event_amount', 50)->default('')->after('state');
            $table->boolean('is_sale_pot_eligible')->default(false)->after('commission_break_event_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_type', 'user_name', 'active_flag', 'first_name', 'last_name',
                'address', 'phone', 'country_id', 'api_key', 'api_secret',
                'api_date', 'profile_image', 'is_employee', 'warehouse_id',
                'dashboard', 'invalid_login_count', 'user_account_id', 'last_login_date',
                'added_by', 'added_date', 'updated_by', 'updated_date',
                'is_deleted', 'archive_server', 'carrier_setup_agreement',
                'receive_email', 'tc_agreed_date', 'is_tc_agreed', 'address_2',
                'address_3', 'city', 'postcode', 'state',
                'commission_break_event_amount', 'is_sale_pot_eligible'
            ]);
            
            $table->unique('email');
        });
    }
};
