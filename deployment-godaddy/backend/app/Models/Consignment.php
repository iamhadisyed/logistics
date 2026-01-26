<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consignment extends Model
{
    protected $fillable = [
        'agent_id',
        'user_id',
        'service_id',
        'customized_service_id',
        'warehouse_user_id',
        'warehouse_id',
        'sales_pot_id',
        'invoice_id',
        'credit_id',
        'is_invoiced',
        'invoice_type',
        'shipment_status',
        'shipment_type',
        'awb',
        'consignment_status',
        'return_awb',
        'hawb',
        'mawb',
        'service_name',
        'reference',
        'date_created',
        'date_label_created',
        'date_booked',
        'date_delivered',
        'is_customer_manifested',
        'booked_file_id',
        'company',
        'contact',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'state',
        'postcode',
        'country_id',
        'telephone',
        'email',
        'number_pieces',
        'weight_type',
        'weight',
        'update_weight',
        'fake_weight',
        'charge_weight',
        'vol_weight',
        'vol_denominator',
        'hv_lv',
        'description',
        'notes',
        'value',
        'currency',
        'itemtype',
        'sender_name',
        'username',
        'sender_checked',
        'sender_company',
        'sender_email',
        'sender_telephone',
        'sender_address_line_1',
        'sender_address_line_2',
        'sender_address_line_3',
        'sender_city',
        'sender_postcode',
        'sender_country_id',
        'sender_state',
        'message',
        'sorter_image',
        'label_file',
        'is_doc',
        'routing_code',
        'routing_code_eur',
        'other_routing_code',
        'billing_hold',
        'send_courier_data',
        'remote_charges',
        'reinvoices',
        'optimus_sorter',
        'full_pallet',
        'half_pallet',
        'quarter_pallet',
        'date_scanned',
        'consignment_type',
        'api_uuid',
        'collection_date',
        'collection_start_time',
        'collection_end_time',
        'collection_confirmation_no',
        'created_from',
        'is_white_label',
        'is_dead_weight_chargable',
        'is_customer_billable',
        'ioss_number',
        'eori_number',
        'vat_number',
        'is_over_size_chargable',
        'is_insured',
        'destination_warehouse_id',
        'consignment_seller',
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'date_scanned' => 'datetime',
        'collection_date' => 'date',
        'weight' => 'decimal:3',
        'update_weight' => 'decimal:3',
        'fake_weight' => 'decimal:3',
        'charge_weight' => 'decimal:3',
        'vol_weight' => 'decimal:3',
        'value' => 'decimal:2',
        'is_invoiced' => 'boolean',
        'is_customer_manifested' => 'boolean',
        'sender_checked' => 'boolean',
        'is_doc' => 'boolean',
        'billing_hold' => 'boolean',
        'send_courier_data' => 'boolean',
        'remote_charges' => 'boolean',
        'reinvoices' => 'boolean',
        'optimus_sorter' => 'boolean',
        'is_white_label' => 'boolean',
        'is_dead_weight_chargable' => 'boolean',
        'is_customer_billable' => 'boolean',
        'is_over_size_chargable' => 'boolean',
        'is_insured' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function senderCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'sender_country_id');
    }
}
