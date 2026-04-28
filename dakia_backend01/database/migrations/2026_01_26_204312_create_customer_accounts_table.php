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
        Schema::create('customer_accounts', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('user_account', 30)->nullable();
            $table->boolean('active_flag')->default(false);
            $table->string('company', 100)->nullable();
            $table->string('full_name', 100)->nullable();
            $table->string('return_address')->nullable();
            $table->boolean('sms_dpd')->nullable()->default(false);
            $table->enum('user_service_type', ['CHOICE', 'ROUTING', 'BOTH'])->nullable();
            $table->integer('parentid')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('logo')->nullable();
            $table->boolean('instant_label')->nullable()->default(false);
            $table->string('country', 3)->nullable();
            $table->integer('country_id')->nullable();
            $table->boolean('tracking_api_access')->nullable()->default(false);
            $table->boolean('import_data_csv')->nullable()->default(false);
            $table->boolean('proforma')->nullable()->default(false);
            $table->boolean('add_tracking')->nullable()->default(false);
            $table->boolean('collection')->nullable()->default(false);
            $table->string('default_description')->nullable();
            $table->string('default_notes')->nullable();
            $table->decimal('default_weight', 5)->nullable();
            $table->text('payment_term')->nullable();
            $table->text('query_term')->nullable();
            $table->string('vat_number', 40)->nullable();
            $table->string('billing_currency', 3)->nullable()->default('GBP');
            $table->boolean('vat_chargable')->nullable()->default(false);
            $table->decimal('vat_value', 10)->nullable()->comment('this is percentage field');
            $table->boolean('allow_remote_area')->nullable()->default(false);
            $table->string('telephone', 20)->nullable();
            $table->string('billing_address')->nullable();
            $table->boolean('date_dispatch')->nullable()->default(false);
            $table->string('is_product', 2)->nullable()->default('0');
            $table->string('profile_image')->nullable();
            $table->boolean('send_courier_data')->nullable()->default(false)->comment('Send data on label Creation');
            $table->boolean('archive_server')->nullable()->default(false);
            $table->boolean('credit_check')->nullable()->default(false);
            $table->boolean('tariff_agreed')->nullable()->default(false);
            $table->string('sales_person', 45)->nullable();
            $table->text('scan_document')->nullable();
            $table->boolean('data_entry')->nullable()->default(false);
            $table->string('bank_account_title', 45)->nullable();
            $table->string('bank_sortcode', 10)->nullable();
            $table->string('bank_account_number', 20)->nullable();
            $table->string('bank_branch_address')->nullable();
            $table->string('trade_name_i', 50)->nullable();
            $table->string('trade_address_i')->nullable();
            $table->string('trade_email_i')->nullable();
            $table->string('trade_phone_i', 50)->nullable();
            $table->string('trade_name_ii', 50)->nullable();
            $table->string('trade_address_ii')->nullable();
            $table->string('trade_email_ii')->nullable();
            $table->string('trade_phone_ii', 50)->nullable();
            $table->string('reg_number', 20)->nullable();
            $table->string('reg_address')->nullable();
            $table->string('reg_postcode', 10)->nullable();
            $table->string('reg_country', 5)->nullable();
            $table->string('sale_agent', 10)->nullable();
            $table->dateTime('sale_date')->nullable();
            $table->decimal('fuel_charges', 10)->nullable()->default(0);
            $table->integer('warehouse_id')->nullable();
            $table->text('user_signature')->nullable();
            $table->boolean('is_fuelcharges_include')->nullable()->default(false);
            $table->boolean('is_prepaid')->nullable()->default(false);
            $table->boolean('return_label')->nullable()->default(false);
            $table->boolean('finalmile_over_label')->nullable()->default(false);
            $table->boolean('request_manifest_collection')->nullable()->default(false);
            $table->boolean('create_pre_alert')->nullable()->default(false);
            $table->boolean('is_employee')->default(false);
            $table->integer('invoice_bank_details_id')->nullable()->default(0);
            $table->boolean('check_list_account_form')->nullable()->default(false);
            $table->boolean('check_list_credit_check')->nullable()->default(false);
            $table->boolean('check_list_t_cs')->nullable()->default(false);
            $table->boolean('check_list_tariff_agreed')->nullable()->default(false);
            $table->boolean('check_list_sales_pot')->nullable()->default(false);
            $table->integer('sales_pot_time_period')->nullable()->default(0);
            $table->decimal('sales_pot_percentage', 5)->nullable()->default(0);
            $table->timestamp('last_login_date')->nullable();
            $table->integer('invalid_login_count')->nullable();
            $table->string('token')->nullable();
            $table->timestamp('token_updated')->nullable();
            $table->timestamp('lock_time')->nullable();
            $table->boolean('opearation_manifest')->nullable()->default(false);
            $table->boolean('own_tariff')->nullable()->default(false);
            $table->enum('user_warehouse', ['NON', 'BIRMINGHAM', 'HAYES'])->nullable()->default('NON');
            $table->string('api_key', 100)->nullable();
            $table->string('api_secert', 100)->nullable();
            $table->dateTime('api_date')->nullable();
            $table->boolean('bagging')->nullable()->default(false);
            $table->boolean('retail_customer')->nullable()->default(false);
            $table->boolean('show_price')->nullable()->default(false);
            $table->decimal('sales_rate', 10)->nullable();
            $table->string('collection_add_line_1', 50)->nullable();
            $table->string('collection_add_line_2', 50)->nullable();
            $table->string('collection_add_line_3', 50)->nullable();
            $table->string('collection_city', 45)->nullable();
            $table->string('collection_postcode', 45)->nullable();
            $table->string('collection_country', 45)->nullable();
            $table->integer('theme_id')->nullable();
            $table->integer('user_code')->nullable();
            $table->string('website_link', 200)->nullable();
            $table->boolean('allow_return_email')->nullable()->default(false);
            $table->string('default_lang', 10)->nullable()->default('en-GB');
            $table->decimal('credit_limit', 10)->nullable()->default(0);
            $table->enum('invoice_period', ['daily', 'weekly', 'bi-monthly', 'monthly'])->nullable()->default('daily');
            $table->decimal('label_price', 10)->nullable()->default(0);
            $table->decimal('discount', 10)->nullable()->default(0);
            $table->string('account_code', 4)->nullable();
            $table->string('paypal_email', 70)->nullable();
            $table->string('paypal_currency', 20)->nullable();
            $table->string('email', 500)->nullable();
            $table->string('paypal_client_secret')->nullable();
            $table->string('alternative_email', 500)->nullable();
            $table->string('billing_email', 500)->nullable();
            $table->timestamp('date_created')->nullable()->useCurrent();
            $table->tinyInteger('allow_oversize')->default(0);
            $table->tinyInteger('allow_overweight')->default(0);
            $table->string('paypal_client_id')->nullable();
            $table->unsignedBigInteger('invoice_template_id')->nullable();
            $table->boolean('send_tracking_data')->nullable();
            $table->string('billing_contact', 50);
            $table->integer('ftp_shipment_upload')->default(0);
            $table->integer('balance_alert_percentage')->default(0);
            $table->integer('commission_break_event_account_amount')->default(0);
            $table->string('tracking_order_prefix', 50);
            $table->integer('return_shipment_allow')->default(0);
            $table->decimal('account_balance', 10)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_accounts');
    }
};
