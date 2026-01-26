<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAccount extends Model
{
    protected $table = 'user_accounts';

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_account',
        'active_flag',
        'company',
        'full_name',
        'return_address',
        'sms_dpd',
        'user_service_type',
        'parentid',
        'phone',
        'logo',
        'instant_label',
        'country',
        'country_id',
        'tracking_api_access',
        'import_data_csv',
        'proforma',
        'add_tracking',
        'collection',
        'default_description',
        'default_notes',
        'default_weight',
        'payment_term',
        'query_term',
        'vat_number',
        'billing_currency',
        'vat_chargable',
        'vat_value',
        'allow_remote_area',
        'telephone',
        'billing_address',
        'date_dispatch',
        'is_product',
        'profile_image',
        'send_courier_data',
        'archive_server',
        'credit_check',
        'tariff_agreed',
        'sales_person',
        'scan_document',
        'data_entry',
        'bank_account_title',
        'bank_sortcode',
        'bank_account_number',
        'bank_branch_address',
        'trade_name_i',
        'trade_address_i',
        'trade_email_i',
        'trade_phone_i',
        'trade_name_ii',
        'trade_address_ii',
        'trade_email_ii',
        'trade_phone_ii',
        'reg_number',
        'reg_address',
        'reg_postcode',
        'reg_country',
        'sale_agent',
        'sale_date',
        'fuel_charges',
        'warehouse_id',
        'user_signature',
        'is_fuelcharges_include',
        'is_prepaid',
        'return_label',
        'finalmile_over_label',
        'request_manifest_collection',
        'create_pre_alert',
        'is_employee',
        'invoice_bank_details_id',
        'check_list_account_form',
        'check_list_credit_check',
        'check_list_t_cs',
        'check_list_tariff_agreed',
        'check_list_sales_pot',
        'sales_pot_time_period',
        'sales_pot_percentage',
        'last_login_date',
        'invalid_login_count',
        'token',
        'token_updated',
        'lock_time',
        'opearation_manifest',
        'own_tariff',
        'user_warehouse',
        'api_key',
        'api_secret',
        'api_date',
        'bagging',
        'retail_customer',
        'show_price',
        'sales_rate',
        'collection_add_line_1',
        'collection_add_line_2',
        'collection_add_line_3',
        'collection_city',
        'collection_postcode',
        'collection_country',
        'theme_id',
        'user_code',
        'website_link',
        'allow_return_email',
        'default_lang',
        'credit_limit',
        'invoice_period',
        'label_price',
        'discount',
        'account_code',
        'paypal_email',
        'paypal_currency',
        'email',
        'paypal_client_secret',
        'alternative_email',
        'billing_email',
        'date_created',
        'allow_oversize',
        'allow_overweight',
        'paypal_client_id',
        'invoice_template_id',
        'send_tracking_data',
        'billing_contact',
        'ftp_shipment_upload',
        'balance_alert_percentage',
        'commission_break_event_account_amount',
        'tracking_order_prefix',
        'return_shipment_allow',
        'account_balance',
    ];

    protected $casts = [
        'active_flag' => 'boolean',
    ];

    /**
     * Scope a query to only include active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('active_flag', 1);
    }

    /**
     * Get the users for this account
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_account_id');
    }
}
