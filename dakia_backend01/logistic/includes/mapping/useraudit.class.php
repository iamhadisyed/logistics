<?php

class UserAudit extends DbAccess3
{
    protected $Country;
    protected $County;
    protected $Weight;
    protected $Currency;
    protected $DateCreated;
    protected $UserId;
    protected $Vat;
    protected $Account;
    public $dataTitle;
    public $dataType;
    public $saveLog = true;
    public $common_messages = [
        'update' => '{user_name} has updated {table_name}',
        'insert' => '{user_name} has generated new {table_name}',
        'delete' => '{user_name} has deleted data from {table_name}',
        'loggedIn' => '{user_name} has logged In',
        'Label' => '{user_name} has generated label',
        'scanDate' => 'Scan Date is set via tracking'
        // 'detailUpdate' => '{user_name} has updated {data_type} for {data_title}',
        ////  'detailInsert' => '{user_name} has added new {data_type} {data_title}',
        // 'detailImport' => '{user_name} has imported {data_type} via file upload for {data_title}',
    ];

    public $custom_message = [
        'services' => [
            'insert' => '{user_name} has added new Service',
            'update' => '{user_name} has updated service',
            'deleted' => '{user_name} has delete service',
            'skip_table' => [
                'parcel',
                'consignment'
            ]
        ]
    ];

    public $tableNames = [
        'account_data' => 'Account Data',
        'address' => 'Address',
        'agent_data' => 'Agent Data',
        'agent_document' => 'Agent Document',
        'agent_log' => 'Agent Log',
        'agent_restricted_postcode' => 'Agent Restricted Postcode',
        'api_data' => 'Api Data',
        'auto_tracking' => 'Auto Tracking',
        'bagging' => 'Bagging',
        'bagging_manifest_mapping' => 'Bagging Manifest Mapping',
        'bagging_services_mapping' => 'Bagging Services Mapping',
        'bagnumbers' => 'Bag Numbers',
        'bag_country_weight_limit' => 'Country Weight Limit for Bag',
        'bag_scan_log' => 'Bag Scan Log',
        'box_info' => 'Box information',
        'brazil_postcode' => 'Brazil Postcode',
        'brazil_state' => 'Brazil State',
        'bulletins' => 'Bulletins',
        'cacesa_routine' => 'Access Routine',
        'carrier' => 'Carrier',
        'carrier_agent' => 'Carrier Agent',
        'carrier_data_file_log' => 'Carrier Data File Log',
        'carrier_document' => 'Carrier Document',
        'carrier_hubs' => 'Carrier Hubs',
        'carrier_log' => 'Carrier Log',
        'carrier_service_customize_rules' => 'Carrier Service Customize Rules',
        'carrier_service_default_rules' => 'Carrier Service Default Rules',
        'carrier_zones' => 'Carrier Zones',
        'carrier_zones_countries' => 'Carrier Zones Countries',
        'carton_pallet_number' => 'Carton Pallet Number',
        'ch_shipments' => 'CH Shipments',
        'consignment' => 'Consignment',
        'consignment_bagging_mapping' => 'Consignment Bagging Mapping',
        'consignment_billing_hold' => 'Consignment Billing Hold',
        'consignment_billing_hold_log' => 'Consignment Billing Hold Log',
        'consignment_charges' => 'Consignment Charges',
        'consignment_charges_log' => 'Consignment Charges Log',
        'consignment_charges_types' => 'Consignment Charges Types',
        'consignment_collection' => 'Consignment collection',
        'consignment_details' => 'Consignment Details',
        'consignment_hold' => 'Consignment Hold',
        'consignment_hold_log' => 'Consignment Hold Log',
        'consignment_hscode' => 'Consignment HS Code',
        'consignment_log' => 'Consignment Log',
        'consignment_pod' => 'Consignment POD',
        'consignment_relabel' => 'Consignment Relabel',
        'consignment_status_log' => 'Consignment Status Log',
        'correos_brazil_datafile' => 'Brazil Data File',
        'cost_tariffs' => 'Cost Tariffs',
        'countries_link_ratebands' => 'Countries Link Rate Bands',
        'country' => 'Country',
        'courier' => 'Courier',
        'cpost_manifest' => 'Cpost Manifest',
        'credit_note' => 'Credit Note',
        'credit_note_details' => 'Credit Note Details',
        'csv_import_template' => 'CSV Import Template',
        'cs_log' => 'CS Log',
        'cs_notes' => 'CS Notes',
        'ctt_datafile_id' => 'CTT Data File ID',
        'currency' => 'Currency',
        'customized_services_routing' => 'Customized Services Routing',
        'customized_services_routing_log' => 'Customized Services Routing Log',
        'customized_user_services_routing' => 'Customized User Services Routing',
        'custom_clearance_agent' => 'Custom Clearance Agent',
        'custom_clearance_agent_oauth' => 'Custom Clearance Agent Oauth',
        'custom_clearance_agent_value' => 'Custom Clearance Agent Value',
        'czint_datafile_id' => 'Czint Data file ID',
        'cz_datafile_id' => 'CZ Data File ID',
        'department' => 'Department',
        'deutschepostdhl_cargo_code' => 'Deutschepost DHL Cargo Code',
        'deutschepost_dhl_streetcode' => 'deutschepost DHL Street Code',
        'dispatch_manifest' => 'Dispatch Manifest',
        'document_type' => 'Document Type',
        'domestic' => 'Domestic',
        'domestic_day_file' => 'Domestic Day File',
        'dpdgroups' => 'DPD Groups',
        'dpd_datafile_id' => 'DPD Data File ID',
        'dropoff_user_location' => 'Dropoff User Location',
        'dx_routing' => 'DX Routing',
        'emailtemplate' => 'Email Template',
        'estimate_delivery_timing' => 'Estimate Delivery Timing',
        'euro_day_file' => 'Euro Day File',
        'fftin_file' => 'fftin File',
        'flight_info' => 'Flight Information',
        'flight_info_bk' => 'Flight Info BK',
        'flight_mapping' => 'Flight Mapping',
        'forget_password_request' => 'Forget Password Request',
        'grouphaspermissions' => 'Group Permissions',
        'groups' => 'Groups',
        'groups_log' => 'Groups Log',
        'hawb_log' => 'HAWB Log',
        'helpdesk_ticket' => 'Help Desk Ticket',
        'helpdesk_ticket_message' => 'help Desk Ticket Message',
        'hermes_datafile_id' => 'Hermes Datafile ID',
        'hermes_postcode_record' => 'Hermes Postcode record',
        'imcp' => 'IMCP',
        'import_csv_consignment_temp' => 'Import CSV Consignment Temp',
        'import_csv_tmp' => 'Import CSV Temporary',
        'international' => 'International',
        'invoices' => 'Invoices',
        'invoices_manual' => 'Invoices Manual',
        'invoices_manual_details' => 'Invoices Manual Details',
        'invoices_number_range' => 'Invoices Number Range',
        'invoice_bank_details' => 'Invoice Bank Details',
        'invoice_detail' => 'Invoice Detail',
        'invoice_detail_backup' => 'Invoice Detail Backup',
        'invoice_detail_log' => 'Invoice Detail Log',
        'invoice_extra_charges' => 'Invoice Extra Charges',
        'invoice_extra_charges_types' => 'Invoice Extra Charges Types',
        'label_file' => 'Label File',
        'language' => 'Language',
        'language_keys' => 'Language Keys',
        'language_keys_najam' => 'Language Keys',
        'licence_plate' => 'Licence Plate',
        'licence_plate_country' => 'Licence Plate Country',
        'location' => 'Location',
        'login_request' => 'Login Request',
        'log_rack_shelf' => 'Log RACK Shelf',
        'manifest' => 'Manifest',
        'manifest_consignment_mapping' => 'Manifest Consignment Mapping',
        'manifest_entity_mapping' => 'Manifest Entity Mapping',
        'marketplace_order' => 'Marketplace Order',
        'marketplace_order_details' => 'Marketplace Order Details',
        'market_places' => 'Market Places',
        'market_places_authenticate_field' => 'Marketplaces Authentication Field',
        'mawb' => 'MAWB',
        'mawb_parcel_mapping' => 'MAWB Parcel Mapping',
        'not_found_record' => 'Not Found Record',
        'oauth_access_tokens' => 'Oauth Access Token',
        'oauth_authorization_codes' => 'Oauth Authorization Code',
        'oauth_clients' => 'Oauth clients',
        'oauth_jwt' => 'Oauth JWT',
        'oauth_public_keys' => 'Oauth Public Keys',
        'oauth_refresh_tokens' => 'Oauth Refresh Tokens',
        'oauth_scopes' => 'Oauth Scopes',
        'ops_summary' => 'Ops Summary',
        'optimus_file_name' => 'Optimus File Name',
        'owe_southafrica_postcode' => 'OWE South Africa Postcode',
        'owe_southafrica_routine' => 'OWE South Africa Routine',
        'pallet' => 'Pallet',
        'pallet_bag_mapping' => 'Pallet Bag Mapping',
        'pallet_bag_remove_reason' => 'Pallet Bag Remove Reason',
        'pallet_carier_group' => 'Pallet Carrier Group',
        'pallet_carrier' => 'Pallet carrier',
        'pallet_carrier_service' => 'Pallet Carrier Service',
        'pallet_entity_mapping' => 'Pallet Entity Mapping',
        'pallet_location' => 'Pallet Location',
        'pallet_name' => 'Pallet Name',
        'parcel' => 'Parcel',
        'parcelforce_datafile_id' => 'Parcel Force Datafile ID',
        'parcelforce_depo_detail' => 'Parcel Force Depo Detail',
        'parcelforce_hub_details' => 'Parcel Force HUB Details',
        'parcelforu_pickup_point' => 'Parcel Force Pickup Point',
        'parcel_bagging_mapping' => 'Parcel Bagging Mapping',
        'parcel_iteam' => 'Parcel Item',
        'parcel_log' => 'Parcel Log',
        'partnerservicesrouting' => 'Parcel Services Routing',
        'payments_history' => 'Payment History',
        'payment_gateways' => 'Payment Gateways',
        'pbt_datafile_id' => 'PBT Datafile ID',
        'pbt_routine' => 'PBT Routine',
        'permissions' => 'Permissions',
        'permissions_log' => 'Permissions Log',
        'pickup' => 'Pickup',
        'pmp_routine' => 'PMP Routine',
        'postcode_user_service_charges' => 'Postcode User Service Charges',
        'postitalia_untracked' => 'Post Italia Untracked',
        'postnl_datafile_id' => 'Postnl Datafile ID',
        'post_italia_routing' => 'Post italia Routing',
        'pre_alert' => 'Flight Pre Alert',
        'pricing_bulk_data_1542977106' => 'Parcel Bulk Data',
        'pricing_bulk_data_1561140807' => 'Pricing Bulk Data',
        'pricing_bulk_data_1561141630' => 'Pricing Bulk Data',
        'pricing_bulk_data_1561141636' => 'Pricing Bulk Data',
        'products' => 'Products',
        'product_log' => 'Product Log',
        'product_routine_log' => 'Product routine Log',
        'proforma_invoice_biiling' => 'Proforma invoice Billing',
        'quotation_details' => 'Quotation Details',
        'rack' => 'Rack',
        'rack_shelf' => 'Rack Shelf',
        'rack_shelf_item' => 'Rack Shelf Item',
        'ratebands' => 'Rate Bands',
        'reamus_destination_station' => 'Reamus Destination Station',
        'reamus_exception' => 'Reamus Exception',
        'reamus_product_service' => 'Reamus Product Service',
        'reamus_service' => 'Reamus Service',
        'reamus_site' => 'Reamus Site',
        'remoteareas' => 'Remote Areas',
        'remoteareas_groups' => 'Remote Areas Groups',
        'remoteareas_groups_log' => 'Remote Areas Groups Log',
        'remoteareas_log' => 'Remote Areas Log',
        'remotearea_charges_carrier' => 'Remote Area Charges Carrier',
        'remotearea_charges_carrier_user' => 'Remote Area Charges Carrier User',
        'remotearea_charges_services' => 'Remote Area Charges Services',
        'remotearea_charges_services_user' => 'Remote Charges Services User',
        'remotearea_user_mapping' => 'Remote Area User Mapping',
        'remotearea_weight_charge' => 'Remote Area Weight Charge',
        'report_customize_settings' => 'Report Customize Settings',
        'routing_user_mapping' => 'Routing User Mapping',
        'royalmail_sortcode' => 'Royal Mail Sort code',
        'sales_call_log' => 'Sales Call Log',
        'sales_pot_comission' => 'Sales Pot commission',
        'services' => 'Services',
        'services_dpd' => 'Service DPD',
        'service_agent_mapping' => 'Service Agent mapping',
        'service_collection_county' => 'Service collection country',
        'service_constant' => 'Service Constant',
        'service_constant_value' => 'Service Constant Values',
        'service_country_ttime' => 'Services Country Ttime',
        'service_document' => 'Service Document',
        'service_log' => 'Service Log',
        'service_range_mapping' => 'Service Range Mapping',
        'shopping_platform' => 'Shopping Platform',
        'sorter_postcode_zone' => 'Sorter Postcode Zone',
        'sort_key_record' => 'Sort Key Record',
        'sp_tariff_log' => 'SP Tarrif Log',
        'status_reason' => 'Status Reason',
        'tagnumber_range' => 'Tag Number Range',
        'tariffs' => 'Tariffs',
        'tariffs_account_mapping' => 'Tariffs Account Mapping',
        'tariffs_details' => 'Tariffs Details',
        'tariffs_log' => 'Tariffs Log',
        'tariffs_pricing' => 'Tariffs Pricing',
        'tariffs_pricing_rules' => 'Tariffs Pricing Rules',
        'tariffs_pricing_rules_details' => 'Tariffs Pricing Rules Details',
        'tariff_additional_charges' => 'Tariff Additional Charges',
        'tariff_details' => 'Tariff Details',
        'tariff_service_charges' => 'Tariff Service Charges',
        'tariff_user_mapping' => 'Tariff User Mapping',
        'tourline_routine' => 'Tour Line Routine',
        'tracking_data' => 'Tracking Data',
        'tracking_data_history' => 'Tracking Data History',
        'tracking_estimated_time' => 'Tracking Estimated Time',
        'tracking_status_codes' => 'Tracking Status Code',
        'ukmail_authentication' => 'UKmail Authentication',
        'ukpostcodelatlng' => 'UK Postcode Latlng',
        'user' => 'Users',
        'userhasgroups' => 'User has Group',
        'user_account' => 'User Account',
        'user_account_log' => 'User Account Log',
        'user_account_service_charges' => 'User Account Service Charges',
        'user_backup' => 'User Backup',
        'user_department' => 'User Department',
        'user_document' => 'User Document',
        'user_log' => 'User Log',
        'user_market_places_mapping' => 'User Market Places Mapping',
        'user_services_charges' => 'User Services Charges',
        'user_services_charges_log' => 'User Services Charges Log',
        'user_services_routing' => 'User Services Routing',
        'user_shopping_platforms' => 'User Shopping Platforms',
        'vehicle' => 'Vehicle',
        'vehicle_driver' => 'Vehicle Driver',
        'vehicle_parcel_mapping' => 'Vehicle Parcel Mapping',
        'warehouse' => 'Warehouse',
        'warehouse_processing_time' => 'Warehouse Processing Time',
        'warehouse_warehouse_ttime' => 'Warehouse Ttime',
        'whistl_depo_details' => 'Whistl Depo Details',
        'yodel_hubs' => 'Yodel hubs',
        'user_audit' => 'User Audit',
    ];
    public $allowAdd = false;
    public $ignoreTables = [
        'grouphaspermissions',
        'userhasgroups',
        'vehicle_parcel_mapping',
        'pallet_carrier_service',
        'service_country_ttime',
        'carrier_zones_countries',
        'user_services_routing',
        'carrier_service_default_rules', //service agent
        'carrier_service_customize_rules',
        'remotearea_charges_services_user',
        'consignment_log',
        'tariffs_account_mapping',
        'user_account_log',
        'remotearea_charges_tariffs',
        'consignment_charges',
        'user_log',
        'service_log',
        'groups_log',
        'vehicle_driver',
        'tracking_data',
        'tariffs_details',
        'consignment_charges_log',
        'remoteareas_groups_log',
    ];

    public $servicesColumns = [
        'service_id',
        'serviceId',
        'service',
    ];

    //because of duplication of dolumns
    public $servicesidColumns = [
        'serviceid',
    ];

    public $accountColumns = [
        'account_id',
        'user_account_id',
    ];

    public $shippmentStatusColumn = [
        'shipment_status',
    ];

    public $customizedServicesColumns = [
        'customized_service_id',
    ];

    public $mawbColumns = [
        'mawb_id',
        'mawb',
    ];

    public $flightColumns = [
        'flight_info_id',
    ];

    public $userColumns = [
        'user_id',
        'userid'
    ];

    public $userCommonColumn = [
        'added_by',
        'updated_by',
        'created_by',
        'warehouse_user_id',
        'driver_id',
        'reopen_by',
        'closed_by'
    ];

    public $warehouseColumns = [
        'wharehouse_id',
        'warehouse_id'
    ];

    public $destinationWarehouseColumns = [
        'destination_warehouse_id',
    ];

    public $agentColumns = [
        'agent_id',
        'agentid'
    ];

    public $parcelColumns = [
        'parcel_id',
    ];

    public $bagColumns = [
        'bag_id',
    ];

    public $countryColumns = [
        'country_id',
        'mawb_source_country_id',
        'bag_source_country_id'
    ];

    public $destinationCountryColumns = [
        'destination_country_id',
        'mawb_destination_country_id',
        'bag_destination_country_id'
//        'userid'
    ];

    public $vehicleColumns = [
        'vehicle_id'
    ];

    public $searchTableNames = [
        'select_search' => 'Select search table',
        'user_account' => 'User account',
        'services' => 'Services',
        'consignment' => 'Consignment',
        'agent_data' => 'Agents',
        'services_by_code' => 'Service By Code',
        'bagging' => 'Bagging',
        'carrier' => 'Carrier',
        'flight_info' => 'Flight Info',
        'invoices_manual' => 'Invoices Manual',
        'user' => 'Search User by username',
        'user_by_name' => 'Search User by name',
        'mawb' => 'MAWB',
        'pallet' => 'Pallet',
        'tariffs' => 'Tariffs',
        'parcel' => 'Parcels',
    ];

    public $userSearchTable = [
        'select_search' => [
            'search_column' => '',
            'table_name' => '',
            'primary_key' => '',
        ],
        'user_account' => [
            'search_column' => 'user_account',
            'table_name' => 'user_account',
            'primary_key' => 'id',
        ],
        'services' => [
            'table_name' => 'services',
            'search_column' => 'name',
            'primary_key' => 'id',
        ],
        'consignment' => [
            'table_name' => 'consignment',
            'search_column' => 'awb',
            'primary_key' => 'id',
        ],
        'agent_data' => [
            'table_name' => 'agent_data',
            'search_column' => 'agent_name',
            'primary_key' => 'id',
        ],
        'services_by_code' => [
            'table_name' => 'services',
            'search_column' => 'code',
            'primary_key' => 'id',
        ],
        'bagging' => [
            'table_name' => 'bagging',
            'search_column' => 'bagnumber',
            'primary_key' => 'id',
        ],
        'carrier' => [
            'table_name' => 'carrier',
            'search_column' => 'carrier',
            'primary_key' => 'id',
        ],
        'flight_info' => [
            'table_name' => 'flight_info',
            'search_column' => 'flight_number',
            'primary_key' => 'id',
        ],
        'groups' => [
            'table_name' => 'groups',
            'search_column' => 'group_name',
            'primary_key' => 'group_id',
        ],
        'invoice_bank_details' => [
            'table_name' => 'invoice_bank_details',
            'search_column' => 'account_title',
            'primary_key' => 'id',
        ],
        'invoices_manual' => [
            'table_name' => 'invoices_manual',
            'search_column' => 'invoice_no',
            'primary_key' => 'id',
        ],
        'user' => [
            'table_name' => 'user',
            'search_column' => 'user_name',
            'primary_key' => 'id',
        ],
        'user_by_name' => [
            'table_name' => 'user',
            'search_column' => "CONCAT(user.first_name, '',user.last_name)",
            'primary_key' => 'id',
        ],
        'mawb' => [
            'table_name' => 'mawb',
            'search_column' => "mawb_number",
            'primary_key' => 'id',
        ],
        'pallet' => [
            'table_name' => 'pallet',
            'search_column' => "palletno",
            'primary_key' => 'id',
        ],
        'parcel' => [
            'table_name' => 'parcel',
            'search_column' => "tracking_number",
            'primary_key' => 'id',
        ],
        'tariffs' => [
            'table_name' => 'tariffs',
            'search_column' => "name",
            'primary_key' => 'id',
        ],

    ];

    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'table_name' => 'string',
            'message' => 'string',
            'is_active' => 'bit',
            'is_deleted' => 'bit',
            'added_by' => 'number',
            'old_data' => 'text',
            'new_data' => 'text',
            'ip_address' => 'string',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',

            'first_name' => 'undefined',
            'last_name' => 'undefined',
        );
        parent::__construct("user_audit", 'id', $fieldList, $mixedCreator);
    }

    public static function getUserAuditFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function insertAuditData($table_name, $type, $user_name, $user_id, $new_data = '', $table_key = 0, $old_data = '', $message = '')
    {

        $type = self::escape($type);
        $user_name = self::escape($user_name);
        $message = self::escape($message);
        if (!in_array($table_name, $this->ignoreTables) || $this->allowAdd == true) {
            if (empty($message)) {
                $custom_messages_keys = array_keys ($this->custom_message);
                if(in_array($table_name, $custom_messages_keys)) {
                    $message = str_replace('{user_name}', $user_name, $this->custom_message[$table_name][$type]);
                } else {
                    $message = str_replace('{table_name}', $this->tableNames[$table_name], $this->common_messages[$type]);
                    $message = str_replace('{user_name}', $user_name, $message);
                    if (!empty($this->dataTitle)) {
                        $message = str_replace('{data_title}', $this->dataTitle, $message);
                    }

                    if (!empty($this->dataType)) {
                        $message = str_replace('{data_type}', $this->dataType, $message);
                    }
                }

            }

            $oldDataN = json_decode($old_data, true);
            $newDataN = json_decode($new_data, true);
            $oldDataLog = [];
            $newDataLog = [];
            if (!empty($oldDataN) && !empty($newDataN)) {
                foreach ($oldDataN as $old_datum_key => $old_datum_value) {
                    foreach ($newDataN as $new_datum_key => $new_datum_value) {
                        if ($new_datum_key == $old_datum_key) {
                            if (is_array($new_datum_value) && is_array($old_datum_value)) {
                                if (!empty($new_datum_value) || !empty($old_datum_value)) {
                                    $oldDataLog[$old_datum_key] = $old_datum_value;
                                    $newDataLog[$new_datum_key] = $new_datum_value;
                                }
                            } else {
                                if ($new_datum_value != $old_datum_value) {
                                    if ((!empty($old_datum_value) && $old_datum_value != 'NULL') || (!empty($new_datum_value) && $new_datum_value != 'NULL')) {
                                        $oldDataLog[$old_datum_key] = $old_datum_value;
                                        $newDataLog[$new_datum_key] = $new_datum_value;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $oldDataLog = $oldDataN;
                $newDataLog = $newDataN;
            }
            if(!empty($oldDataLog) || !empty($newDataLog)) {
                $oldDataLog = json_encode($oldDataLog);
                $newDataLog = json_encode($newDataLog);
                $userIp = getClientIp();
                $query = "INSERT INTO user_audit (message, added_by, table_name, old_data, new_data, table_key, ip_address)
                    VALUES ('$message', $user_id, '$table_name', '$oldDataLog', '$newDataLog', $table_key, '$userIp')";
                DbAccess3::runQuery($query);
            }
        }
    }

    public static function getTotalNumberOfUserAuditFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
