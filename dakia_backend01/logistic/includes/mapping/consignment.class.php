<?php

/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
include_classes([
    'iaddress.class',
    'parcel.class',
    'parcelfilter.class',
    'consignmentdropoffmapping.class',
    'consignmentdropoffmappingfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'consignmentdropoffmapping.class',
    'consignmentdropoffmappingfilter.class',
    'itemdetail.class',
    'itemdetailfilter.class',
    'skuboxmapping.class',
    'skuboxmappingfilter.class',
    'presortdestinationwarehouse.class',
    'presortdestinationwarehousefilter.class'
]);
class Consignment extends DbAccess3 implements iAddress
{

    // consignment service type
    const SERVICE_DOMESTIC = "DBP";
    const SERVICE_INTERNATIONAL = "INT";
    const SERVICE_EUROPE_ROAD = "R1";
    const SERVICE_RETURN = "RTN";
    const SERVICE_ROYALMAIL_48 = "RM48";
    const SERVICE_ROYALMAIL_48_SIGN = "RM48S";
    const SERVICE_ROYALMAIL_24 = "RM24";
    const SERVICE_ROYALMAIL_24_SIGN = "RM24S";
    const ROUTING_SERVICE_ONEWORLD = "OR";
    const ROUTING_SERVICE_PARTNER = "PR";
    const ROUTING_SPECIAL_SERVICE = "S";
    const SERVICE_WEIGHT_TYPE_PARCEL = "PARCEL";
    //
    const VALUE_HIGH = "HV";
    const VALUE_LOW = "LV";
    const VALUE_MEDIUM = "MV";

    //Consignment status constant
    const STATUS_NEW = 10;
    const STATUS_INVALID = 11;
    const STATUS_READY_TO_PRINT = 12;
    const STATUS_LABEL_CREATED = 13;
    const STATUS_RECEIVED = 14;
    const STATUS_PARTIAL_RECEIVED = 15;
    const STATUS_DISPATCHED = 16;
    const STATUS_PARTIAL_DISPATCHED = 17;
    const STATUS_INTRANSIT = 18;
    const STATUS_DELIVERED = 19;
    const STATUS_PARTIAL_DELIVERED = 20;
    const STATUS_CLOSE = 21;
    const STATUS_RECYCLED = 22;
    const STATUS_CANCELLED = 23;
    const STATUS_HOLD = 24;
    const STATUS_PROBLEM = 25;
    const STATUS_RELABLED = 26;
    const STATUS_RETURNED = 27;
    const STATUS_DISCREPANCY = 28;
    const STATUS_AWATING_CLAIM = 29;
    const STATUS_PARTIAL_RETURNED = 30;
    const STATUS_NOT_DELIVERED = 31;
  

    public static $status_array = array(
        10 => "New",
        11 => "Invalid",
        12 => "Ready To Print",
        13 => "Label Created",
        14 => "Received",
        15 => "Partial Received",
        16 => "Dispatched",
        17 => "Partial Dispatched",
        18 => "Intransit",
        19 => "Delivered",
        20 => "Partial Delivered",
        21 => "Closed",

        22 => "Deleted",
        23 => "Cancelled",
        24 => "Hold",
        25 => "Problem",
        26 => "Relabel",
        27 => "Returned",
        28 => "Discrepancy",
        29 => "Awaiting Claim",
        30 => "Partial Returned",
        31 => "not delivered"
     
    );
    public static $database_status_array = array(
        10 => "new",
        11 => "invalid",
        12 => "ready to print",
        13 => "label created",
        14 => "received",
        15 => "partial received",
        16 => "dispatched",
        17 => "partial dispatched",
        18 => "intransit",
        19 => "delivered",
        20 => "partial delivered",
        21 => "closed",

        22 => "recycled",
        23 => "cancelled",
        24 => "hold",
        25 => "problem",
        26 => "relabel",
        27 => "returned",
        28 => "discrepancy",
        29 => "awaiting claim",
        30 => "partial returned",
        31 => "not delivered"

    );
    public static $ecomerece_status_array = array(
        0 => "Unknown",
        1001 => "New",
        1002 => "Invalid",
        1003 => "Ready To Print",
        1004 => "Printed",
        1005 => "Printing",
        1006 => "Warehouse Reveived",
        1007 => "Held",
        1008 => "Shipped",
        1009 => "Exporting",
        1010 => "Delivered",
    );
    //
    private $parcel_list = null;
    private $iso_look_up_flag = true;
    //
    private $debug_flag = false;
    private $delivery_network = null;
    private $error_list = array();

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null,$debug=false)
    {
        $fieldList = array(
            'id' => 'number',
            'consignment_status' => 'string',
            'shipment_status' => 'number',
            'user_id' => 'number',
            'warehouse_user_id' => 'number',
            'service_id' => 'number',
            'customized_service_id' => 'number',
            'awb' => 'string',
            'return_awb' => 'string',
            'hawb' => 'string',
            'shipment_type' => 'string',
            'reference' => 'string',
            'date_created' => 'string',
            'date_label_created' => 'number',
            'date_booked' => 'number',
            'date_delivered' => 'number',
            'is_customer_manifested' => 'number',
            'booked_file_id' => 'string',
            'company' => 'string',
            'contact' => 'string',
            'address_line_1' => 'string',
            'address_line_2' => 'string',
            'address_line_3' => 'string',
            'city' => 'string',
            'state' => 'string',
            'postcode' => 'string',
            'country_id' => 'number',
            'telephone' => 'string',
            'number_pieces' => 'number',
            'weight_type' => 'string',
            'weight' => 'number',
            'update_weight' => 'number',
            'fake_weight' => 'number',
            'charge_weight' => 'number',
            'vol_weight' => 'number',
            'vol_demonimator' => 'number',
            'hv_lv' => ['enum' => ['L','M','H']],
            'description' => 'string',
            'notes' => 'string',
            'value' => 'number',
            'currency' => 'string',
            'sender_name' => 'string',
            'username' => 'string',
            'sender_checked' => 'string',
            'message' => 'string',
            'sorter_image' => 'string',
            'label_file' => 'string',
            'mawb' => 'string',
            'is_doc' => 'number',
            'email' => 'string',
            'itemtype' => 'string',
            'routing_code' => 'string',
            'routing_code_eur' => 'string',
            'other_routing_code' => 'string',
            'is_invoiced' => 'number',
            'invoice_type' => ['enum' => ['INV','MNI']],
            'invoice_id' => 'number',
            'credit_id' => 'number',
            'billing_hold' => 'number',
            'send_courier_data' => 'number',
            'remote_charges' => 'number',
            'reinvoices' => 'number',
            'optimus_sorter' => 'number',
            'agent_id' => 'number',
            'warehouse_id' => 'number',
            'sales_pot_id' => 'number',
            'full_pallet' => 'number',
            'half_pallet' => 'number',
            'quarter_pallet' => 'number',
            'date_scanned' => 'datetime',
            'consignment_type' => ['enum' => ['return','outbound'],'default' => 'outbound'],
            'sender_company' => 'string',
            'sender_email' => 'string',
            'sender_address_line_1' => 'string',
            'sender_address_line_2' => 'string',
            'sender_address_line_3' => 'string',
            'sender_city' => 'string',
            'sender_state' => 'string',
            'sender_postcode' => 'string',
            'sender_country_id' => 'number',
            'sender_telephone' => 'string',
            'collection_start_time' => 'string',
            'collection_end_time' => 'string',
            'collection_date' => 'date',
            'collection_confirmation_no' => 'string',
            'created_from' => 'string',
            'is_white_label' => 'number',
            'is_dead_weight_chargable' => 'number',
            'is_over_size_chargable' => 'number',
            'is_insured' => 'bit',
            'eori_number' => 'string',
            'vat_number' => 'string',
            'ioss_number' => 'string',
            'is_customer_billable' => 'number',
            'destination_warehouse_id' => 'number',
            'consignment_seller' => 'string',
//            'return_label' => 'string',
            // NEW FIELDS AFTER OPTIMIZATION
            
            'length' => 'undefined',
            'parcel_weight' => 'undefined',
            'parcel_height' => 'undefined',
            'parcel_length' => 'undefined',
            'parcel_width' => 'undefined',
            'cubic_meter' => 'undefined',
            'agent_handling_charges' => 'undefined',
            'total_item_in_bag' => 'undefined',
            'ddp' => 'undefined',
            'bag_id' => 'undefined',
            'consiignmentid' => 'undefined',
            'on_farword_charges' => 'undefined',
            'hv' => 'undefined',
            'label_price' => 'undefined',
            'label_discount' => 'undefined',
            'basic_charges' => 'undefined',
            'ndx' => 'undefined',
            'ddp' => 'undefined',
            'fuel_charges' => 'undefined',
            'additional_charges' => 'undefined',
            'remote_area_charge' => 'undefined',
            'extra' => 'undefined',
            'discount' => 'undefined',
            'hv' => 'undefined',
            'ancillary_charges' => 'undefined',
            'amount' => 'undefined',
            'account' => 'undefined',
            'user_account' => 'undefined',
            'country_name' => 'undefined',
            'service_code' => 'undefined',
            'service_name' => 'undefined',
            'product_code' => 'undefined',
            'product_name' => 'undefined',
            'shipment_count' => 'undefined',
            'courier_status' => 'undefined',
            'user_account' => 'undefined',
            'billing_email' => 'undefined',
            'billing_currency' => 'undefined',
            'billing_address' => 'undefined',
            'invoice_period' => 'undefined',
            'vat_chargable' => 'undefined',
            'vat_value' => 'undefined',
            'user_account_id' => 'undefined',
            'user_country_id' => 'undefined',
            'user_company' => 'undefined',
            'carrier_name' => 'undefined',
            'service_type' => 'undefined',
            'type' => 'undefined',
            'charges_invoice_id' => 'undefined',
            'consignment_billing_hold_id' => 'undefined',
            'total_parcel' => 'undefined',
            'parcel_id' => 'undefined',
            'tracking_number' => 'undefined',
            'net_amount' => 'undefined',
            'code' => 'undefined',
            '_date_label_created' => 'undefined',
            'month_dates' => 'undefined',
            'length' => 'undefined',
            'width' => 'undefined',
            'height' => 'undefined',
            'consignment_id' => 'undefined',
            'carrier_id' => 'undefined',
            'country_region' => 'undefined',
            'service_code' => 'undefined',
            'country_iso_code' => 'undefined',
            'country' => 'undefined',
            'consignment_charges_id' => 'undefined',
            'api_uuid' => 'string',
            'first_name' => 'undefined',
            'last_name' => 'undefined',
            'c_id' => 'undefined',
            'service_name' => 'undefined',
            'service_code' => 'undefined',
            'chute_sorted' => 'undefined',
            'service_using' => 'undefined',
            'bag_low_value' => 'undefined',
            'agent_basic_charges' => 'undefined',
            'agent_fuel_charges' => 'undefined',
            'margin' => 'undefined',
            'profit' => 'undefined',
            'customer_cost' => 'undefined',
            'customer_cost_company' => 'undefined',
            'agent_cost_supplier' => 'undefined',
            'agent_cost_company' => 'undefined',
            'purchase_invoice_cost_supplier' => 'undefined',
            'purchase_invoice_cost_company' => 'undefined',
            'customer_cost_currency' => 'undefined',
            'customer_company_currency' => 'undefined',
            'agent_supplier_currency' => 'undefined',
            'agent_company_currency' => 'undefined',
            'purchase_invoice_supplier_currency'=>'undefined',
            'purchase_invoice_company_currency'=>'undefined' ,
            'agent_additional_charges'=>'undefined' ,
            'agent_basic_charges'=>'undefined' ,
            'agent_total'=>'undefined' ,
            'agent_currency'=>'undefined' ,
            'invoiced_additional_charges'=>'undefined' ,
            'invoiced_basic_charges'=>'undefined' ,
            'invoiced_total'=>'undefined' ,
            'invoiced_currency'=>'undefined' ,
            'do_tracking_number' => 'undefined',
            'invoice_no'=>'undefined',
            'owe_status_code'=>'undefined',
            'last_tracking_update'=>'undefined',
            'service_display_id'=>'undefined',
            'is_reschedulable' => 'undefined',
            'bagnumber'=>'undefined', 
            'title'=>'undefined',
            'marketplaceorder_id' =>'undefined',
            'items' => 'undefined',
            'consignment_parcels' => 'undefined',
            'vat_rate' => 'undefined',
            'region' => 'undefined'

        );
        //
        parent::__construct("consignment", 'id', $fieldList, $mixedCreator,$debug);
    }


    public static function getTotalNumberOfConsignmentsFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }



    public function getService()
    {
        return $this->valArray["service"];
    }
     public function getFullName()
    {
        return $this->getContact();
    }
    public function getAddressLine1()
    {
        return htmlspecialchars_decode($this->valArray["address_line_1"]);
    }

    public function setAddressLine1($val)
    {
        $this->valArray["address_line_1"] = $val;
        $this->modifyArray["address_line_1"] = $val;
    }

    public function getAddressLine2()
    {
        return htmlspecialchars_decode($this->valArray["address_line_2"]);
    }


    public function getAddressLine3()
    {
        return htmlspecialchars_decode($this->valArray["address_line_3"]);
    }

    public function setAddressLine2($val)
    {
        $this->valArray["address_line_2"] = $val;
        $this->modifyArray["address_line_2"] = $val;
    }

    public function getTelephone()
    {
        return $this->valArray["telephone"];
    }

    public function setTelephone($val)
    {
        $this->valArray["telephone"] = $val;
        $this->modifyArray["telephone"] = $val;
    }

    public function getCompany()
    {
        return htmlspecialchars_decode($this->valArray["company"]);
    }

    public function setCompany($val)
    {
        $this->valArray["company"] = $val;
        $this->modifyArray["company"] = $val;
    }

    public function getCity()
    {
        return $this->valArray["city"];
    }

    public function setCity($val)
    {
        $this->valArray["city"] = $val;
        $this->modifyArray["city"] = $val;
    }

    public function getPostcode()
    {
        return $this->valArray["postcode"];
    }

    public function setPostcode($val)
    {

        $postcode = strtoupper($val);
        //$this->valArray["postcode"] = $postcode;
        $this->valArray["postcode"] = $val;
        $this->modifyArray["postcode"] = $val;
    }
    public function getDatePrinted()
    {
        return date("Y-m-d", strtotime($this->valArray["date_printed"]));
    }
    public function setApiData($requestString = '', $responseString = '', $apireason = '')
    {
        $consignment_id = $this->getId();
        $added_by = Sessionmanager::getUser()->getUserName();
        if ($consignment_id > 0) {
            $querySql = "INSERT INTO api_data "
                . "(consignment_id, api_request, api_response, added_by, api_reason)"
                . "VALUES"
                . "('" . DbAccess3::escape($consignment_id) . "', '" . DbAccess3::escape($requestString) . "', "
                . "'" . DbAccess3::escape($responseString) . "', '" . DbAccess3::escape($added_by) . "', '" . DbAccess3::escape($apireason) . "' )";

            DbAccess3::runQuery($querySql);
        }
    }

    /**
     * A consignment is delivered via a delivery network!!
     * The delivery network is determine by the consignment type,
     * the appropriate network is looked by the factory method,
     * and reference to network stored within the consignment it self!
     *
     * return DeliveryNetwork
     *
     */
    public function getDeliveryNetwork()
    {

        if ($this->delivery_network == null) {
            $this->delivery_network = DeliveryNetwork::deliveryNetworkFactory($this);
        }

        return $this->delivery_network;
    }

    public function bulkUpdate($setColumns, $where, $debug = false)
    {
        //$awbArrayStr = "'" . implode("','",$awbArray) . "'";
        if (trim($setColumns) != '' && trim($where) != '')
            $sql = "update consignment c set $setColumns where $where";

        if ($debug) {
            echo $sql;
            die;
        }
        //t($sql, __METHOD__);

        return DbAccess3::runQuery($sql);
        //DbAccess3::runQuery($sql);
    }


    public static function getServiceTypeFromWeight($user, $weight, $country, $type, $routing = '')
    {
        if (trim($routing) != '')
            $routingQuery = " AND p.routing_name like '" . DbAccess3::escape($routing) . "' ";
        else
            $routingQuery = "";

        echo $sql = "SELECT  s.code, s.name, s.account_owner, s.volumetric_denominator, s.type, p.service_name , p.service_type, rum.is_agreed 'is_agreed'  
					FROM 
						( services as s 
					INNER JOIN 
						customizedservicesrouting as p 
					ON 
							s.code = p.service_name
						)
					INNER JOIN 
						routing_user_mapping as rum	
					ON 
							rum.routing_name = p.routing_name
							
					WHERE 		
						p.service_name = s.code 
						and s.type = '" . DbAccess3::escape($type) . "' 
						and rum.user_account = '" . DbAccess3::escape($user) . "'
						and p.from_weight<= '" . DbAccess3::escape($weight) . "' 
						and p.to_weight >= '" . DbAccess3::escape($weight) . "' 
						and country = '" . DbAccess3::escape($country) . "' 
						and p.service_type != 's' 
						and p.status = '" . DbAccess3::escape('active') . "'" . $routingQuery . " LIMIT 0,1";


        $rs = DbAccess3::runQuery($sql);

        $responceArray = array();
        if ($rs != "") {
            $row = mysqli_fetch_assoc($rs);
            $responceArray['code'] = $row['code'];
            $responceArray['name'] = $row['name'];
            $responceArray['type'] = $row['type'];
            $responceArray['service_type'] = $row['service_type'];
            if (trim($row['account_owner']) != '') {
                $rsUsers = DbAccess3::runQuery("SELECT user_account FROM user where id = '" . $row['account_owner'] . "'");
                if ($rsUsers != "") {
                    $rowUser = mysqli_fetch_assoc($rsUsers);
                    $responceArray['account_owner'] = $rowUser['user_account'];
                } else
                    $responceArray['account_owner'] = '';
            } else
                $responceArray['account_owner'] = '';
            $responceArray['denominator'] = $row['volumetric_denominator'];
        }
        return $responceArray; // $row['service_name'];
    }


    public static function getErrorMessage($id)
    {
        $sql = "SELECT message FROM consignment where hawb = '" . DbAccess3::escape($id) . "' LIMIT 0,1";


        $rs = DbAccess3::runQuery($sql);


        if ($rs != "") {
            $row = mysqli_fetch_assoc($rs);

            return $row['message'];
        }
    }

    public static function getStandardUserRoutingCode($userId, $countryIso, $serviceId, $weight, $parentId = 0)
    {
        $output = 0;
        $sql = "SELECT p.user_account_id,  addedby.user_account_id 'parent_user_account_id', p.added_by, p.is_agreed 'is_agreed' FROM 
                services as s 
                    INNER JOIN user_services_routing as p 
                ON 
                    s.id = p.service_id
                
                INNER JOIN user as u
                ON
                    u.user_account_id = p.user_account_id
                    
                INNER JOIN user as addedby
                    ON
                        addedby.id = p.added_by
                    

                WHERE 		
                        u.id = '" . DbAccess3::escape($userId) . "'  
                AND	p.from_weight <= '" . DbAccess3::escape($weight) . "' 
                AND 	p.to_weight >='" . DbAccess3::escape($weight) . "' 
                AND 	p.country_id='" . DbAccess3::escape($countryIso) . "' 
                AND 	p.service_id = '" . DbAccess3::escape($serviceId) . "' 
                AND 	p.country_id in (
                            select id_country from service_country_ttime where id_service = '" . DbAccess3::escape($serviceId) . "' "
            . "                 )
                AND 	p.status=1 LIMIT 0,1";

        $rs = DbAccess3::runQuery($sql);
        if (mysqli_num_rows($rs) > 0) {
            $row = mysqli_fetch_assoc($rs);
            $userId = $row['user_account_id'];
            $parentId = $row['parent_user_account_id'];
            $parentUserId = $row['added_by'];
            $is_agreed = $row['is_agreed'];
            if ($is_agreed <> 1)
                return 'NON_AGREED';
            $output = self::CustomizeAgentRule($userId, $countryIso, $serviceId, $weight, $parentId, $parentUserId);
        }

        return $output;
    }


    public static function CustomizeAgentRule($userAccountId, $countryIso, $serviceId, $weight, $parentAccountId = 0, $parentUserId = 0)
    {
        $output = 0;
        $checkDefault = false;
        $sql = "SELECT agentid FROM 
                    carrier_service_customize_rules
                WHERE 		
                        user_account_id = '" . DbAccess3::escape($userAccountId) . "'  
                AND	from_weight <= '" . DbAccess3::escape($weight) . "' 
                AND 	to_weight >='" . DbAccess3::escape($weight) . "' 
                AND 	serviceid = '" . DbAccess3::escape($serviceId) . "'"
            . "AND status='" . DbAccess3::escape('1') . "' LIMIT 0,1";


        $rs = DbAccess3::runQuery($sql);
        if (mysqli_num_rows($rs) > 0) {
            $row = mysqli_fetch_assoc($rs);
            $output = $row['agentid'];
        } else {
            $checkDefault = true;
            if ($parentAccountId > 0 && $userAccountId != $parentAccountId) {
                $userAccountId = $parentAccountId;

                $output = self::CustomizeAgentRule($userAccountId, $countryIso, $serviceId, $weight, 0, $parentUserId);
                if ($output <= 0) {
                    $output = self::getStandardUserRoutingCode($parentUserId, $countryIso, $serviceId, $weight);
                    if ($output == 'NON_AGREED')
                        return $output;
                    else if ($output <= 0) {
                        $userFilter = new UserFilter();
                        $userFilter->addFilter(" id = '" . $parentUserId . "' AND  user_type = 'admin' ");
                        $userList = $userFilter->getColumnList("id");
                        if (count($userList) > 0) {
                            $checkDefault = true;
                        }
                    }
                }
            }

            if ($checkDefault)
                $output = self::defaultAgentRule($countryIso, $serviceId, $weight);
        }
        return $output;
    }

    public static function defaultAgentRule($countryIso, $serviceId, $weight)
    {
        $output = 0;
        $sql = "SELECT agentid FROM 
                    carrier_service_default_rules
                WHERE 		
                        from_weight <= '" . DbAccess3::escape($weight) . "' 
                AND 	to_weight >='" . DbAccess3::escape($weight) . "' 
                AND 	serviceid = '" . DbAccess3::escape($serviceId) . "' LIMIT 0,1";

        $rs = DbAccess3::runQuery($sql);
        if (mysqli_num_rows($rs) > 0) {
            $row = mysqli_fetch_assoc($rs);
            $output = $row['agentid'];
        }
        return $output;
    }

    /*
    * Scenario 201
    * Get user_account, country_id,Consignment_service,consignment_handling, weight (But in Scenario 201 weight should be zero)
    * Return : customizedservicesrouting.service_type
    */
    public static function getSpecialServiceRoutingCode($user, $country, $service_type, $handling, $weight)
    {
        $sql = "SELECT p.service_type  
					FROM 	services as s 
					INNER JOIN customizedservicesrouting as p 
					ON 
							s.code = p.service_name
					WHERE 		
								p.service_name = s.code 
						AND 	p.account_number = '" . DbAccess3::escape($user) . "'  
						AND		p.from_weight <= '" . DbAccess3::escape($weight) . "' 
						AND 	p.to_weight >='" . DbAccess3::escape($weight) . "' 
						AND 	country='" . DbAccess3::escape($country) . "' 
						AND 	s.type='" . DbAccess3::escape($service_type) . "' 
						AND 	p.service_type = '" . DbAccess3::escape('S') . "' 
						AND 	p.service_name = '" . DbAccess3::escape($handling) . "' 
						AND 	p.status='" . DbAccess3::escape('active') . "' LIMIT 0,1";
        $rs = DbAccess3::runQuery($sql);
        if (mysqli_num_rows($rs) > 0) {
            $row = mysqli_fetch_assoc($rs);
            return $row['service_type'];
        } else {
            return '';
        }
        mail("mruga@oneworldexpress.com", "asdf", $sql);
    }

   /*
        * Scenario 201
        * Get user_account, country_id,Consignment_service,consignment_handling,routing_code, weight (But in Scenario 201 weight should be zero)
        * Return : without any thing it will return wtih error
        */
    public static function getRoutingErrors($user, $weight, $country, $service_type, $routingType, $handling = '', $both = '')
    {
        $errorForRouting = "The ($handling), weight ($weight) ,  country ($country) is not allowed for services you have selected, please the required details.";
        return $errorForRouting;
        /*
         * 	we make this change for optimise the code. we will consider it later on.
         */


        $sql = "SELECT 
		 			psr.carrier  
				FROM 
					services as ser 
				INNER JOIN 
					customizedservicesrouting psr 
				ON 
					psr.service_name = ser.code 
				WHERE  
					psr.account_number = '" . DbAccess3::escape($user) . "' ";
        if ($handling != '')
            $handlingSql = "	AND psr.service_name = '" . DbAccess3::escape($handling) . "' ";
        else
            $handlingSql = "	 ";
        $weightSql = "	AND psr.from_weight <= '" . DbAccess3::escape($weight) . "'  AND psr.to_weight >= '" . DbAccess3::escape($weight) . "'  ";
        $serviceSql = "	AND ser.type='" . DbAccess3::escape($service_type) . "'  ";
        $countrySql = "	AND	(psr.country) = upper('" . DbAccess3::escape($country) . "') ";
        $staticSql = "	AND	psr.service_type != '" . DbAccess3::escape($routingType) . "' AND	psr.status='" . DbAccess3::escape('active') . "' LIMIT 0,1 ";
        $NotRoutingstaticSql = "	AND	psr.service_type = '" . DbAccess3::escape($routingType) . "' AND	psr.status='" . DbAccess3::escape('active') . "' LIMIT 0,1 ";

        if (trim($both) == '') {
            $query = $sql . $handlingSql . $staticSql;
            $recordSet = DbAccess3::runQuery($query);
            if (mysqli_num_rows($recordSet) > 0) {
                $query = $sql . $handlingSql . $countrySql . $staticSql;
                $recordSet = DbAccess3::runQuery($query);
                if (mysqli_num_rows($recordSet) > 0) {
                    $errorForRouting = $handling . " Selected weight ($weight) for country ($country) is not allowed for services. Please check parcel weight. ";
                } else {
                    $errorForRouting = "Country (" . $country . ") is not allowed for services. Please check your destination country.";
                }
            }
        } else if (trim($both) == 'PRODUCT') {
            if (trim($handling) != '')
                $query = $sql . $handlingSql . $staticSql;
            else
                $query = $sql . $staticSql;

            $recordSet = DbAccess3::runQuery($query);
            if (mysqli_num_rows($recordSet) > 0) {
                $query = $sql . $handlingSql . $countrySql . $staticSql;
                $recordSet = DbAccess3::runQuery($query);
                if (mysqli_num_rows($recordSet) > 0) {

                    $errorForRouting = $handling . " Selected weight ($weight) for country ($country) is not allowed for services. Please check parcel weight. ";
                } else {
                    $errorForRouting = "Country (" . $country . ") is not allowed for services. Please check your destination country.";
                }
            } else {
                $errorForRouting = "The service ($handling) you have selected is not allowed.";
            }
        } else {
            $errorForRouting = "The service ($handling) you have selected is not allowed.";
        }

        return $errorForRouting;
    }


    public static function find3DigitServiceCode($handling)
    {
        $sql = "SELECT 3_digit_service_code, premium, dpd_label_service FROM services_dpd where 2_digit_service_code = '" . DbAccess3::escape($handling) . "' ";
        $rs = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($rs);
        return $row['3_digit_service_code'] . "|" . $row['premium'] . "|" . $row['dpd_label_service'];
    }

    /**
     * Get parcel objects associated with this consignment.
     * The "number of pieces" indicates how many parcels there are for
     * a consignment.  But there may not be a corresponding number of actual
     * parcel objects created; either returns real list of parcels or
     * can be used to create new parcel objects (but not commit to db).
     *
     * @param bool - Create if missing.
     */
    public function getParcels($create_if_missing = true)
    {
        // Get list of parcels
        if ($this->parcel_list == null) {
            // look for parcels if this file has been saved.
            if ($this->getId() > 0) {
                $filter = new ParcelFilter();
                $filter->addConsignmentIdFilter($this->getId());
                $this->parcel_list = $filter->getList();
            } else {
                $this->parcel_list = array();
            }
           $start = sizeof($this->parcel_list);
            // Check the number of parcels
            if ($create_if_missing && $start <= 0) {

//                $bagNumbers = $this->getBagNumber();
//                $bagNumberArray = $array = explode(',', $bagNumbers);

                for ($i = $start; $i < $this->getNumberPieces(); $i++) {
                    $parcel = new Parcel();
//                    $currentBagNumber = 0;
//                    if (isset($bagNumberArray[$i]))
//                        $currentBagNumber = $bagNumberArray[$i];
                    $parcel->setConsignmentId($this->getId());
                    //$parcel->setBagNumber($currentBagNumber);
                    //$parcel->setTrackingNumber(LicencePlate::getLicencePlateNumber($this->getService()));
                    $parcel->save();
                    $this->parcel_list[] = $parcel;
                }
                // remove any extra
                for ($i = $start - 1; $i >= $this->getNumberPieces(); $i--) {
                    $this->parcel_list[$i]->delete();
                    unset($this->parcel_list[$i]);
                }
            }
        }
        return $this->parcel_list;
    }




    /**
     * Get the Country iso code
     *
     * @return string
     */
    public function getCountryIsoCode()
    {
        // lookup if not set (once only for each class instance)
        if ($this->iso_look_up_flag && ($this->valArray["country_iso_code"] == "")) {
            // Lookup country.
            //echo $this->getCountry();

            $country = Country::getCountryFromName($this->getCountry());
            if ($country != null) {
                $this->valArray["country_iso_code"] = $country->getIso();
            }
        }
        return $this->valArray["country_iso_code"];
    }

    /**
     * Set the consignment delivery country.
     *
     * @param string $country
     */
    public function setCountry($country)
    {


        $this->valArray["country"] = $country;
        $this->modifyArray["country"] = $country;
        // clear the iso code, ensures it is looked up when accessed
        // lookup if not set (once only for each class instance)

        $countryiso = Country::getCountryFromName($country);
        if ($countryiso != null) {
            $this->valArray["country_iso_code"] = $countryiso->getIso();
            $this->modifyArray["country_iso_code"] = $countryiso->getIso();


            if ($this->getService() == '') {
                $this->valArray["service"] = $countryiso->getRegion();
                $this->modifyArray["service"] = $countryiso->getRegion();
            }
        }
        $this->iso_look_up_flag = true;
    }


    /**
     * Get array of possible statues,
     * the key is state value and the value is the description.
     *
     * @return array
     */
    public static function getStatusList()
    {
        return self::$status_array;
    }

    /**
     * Get a status' description
     *
     * @param string $theState
     * @return string
     */
    public static function stateText($theState)
    {

        if (!isset(self::$status_array[$theState]))
            return "Unknown";

        return self::$status_array[$theState];
    }

    /**
     * Set status
     *
     * @param int - consignment status
     */
    public function setStatus($val)
    {
        // overridden default to ensure valid consignment status is set.
        if (!isset(self::$status_array[$val]))
            $val = self::STATUS_NEW;
        // default
        $this->valArray["shipment_status"] = $val;
        $this->modifyArray["shipment_status"] = $val;
    }

    /**
     * Get status
     *
     * @return consignment status
     */
    public function getStatus()
    {
        return $this->getShipmentStatus();
    }

    public function getConsignmentStatus()
    {
        if ($this->valArray["consignment_status"] == "")
            $this->valArray["consignment_status"] = Consignment::STATUS_NEW;
        return $this->valArray["consignment_status"];
    }

    /**
     * Get list of consignment objects, using sql given
     *
     * @param string $sql
     */
    public static function getConsignmentListFromSql($sql)
    {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /*     * *
     * Gets array with simple key value pair for given sql
     */

    public static function getListFromSql($sql, $val_field, $key_field = "")
    {
        $list = array();
        $rs = DbAccess3::runQuery($sql);
        //
        while ($row = mysqli_fetch_assoc($rs)) {
            if ($key_field == "") {
                $list[] = $row[$val_field];
                echo '<br>=' . $row[$val_field];
            } else {
                $list[$row[$key_field]] = $row[$val_field];
            }
        }
        return $list;
    }

    /**
     * Get count of consignment objects, using sql given
     *
     * @param string $sql
     */
    public static function getConsignmentCountFromSql($sql)
    {
        return DbAccess3::getSql(__CLASS__, $sql);
    }

    /* Save Logs from consignment edit page */

    public function SaveLog($message = NULL, $type = 'A')
    {
        $log = new ConsignmentLog();
        $this->save();
        if ($message != NULL)
            $log->createlog($message, $this->getId(), $type);
    }


    public function isValid()
    {
        $ProductTyep = trim(Sessionmanager::getUser()->getIsProduct());
        $this->error_list = array(); // reset error list.
        // account set by system according to client id
        // AWB set by system at print time
        // HAWB must be present and unique before consignment accepted
        // reference, date_created optional
        $user = SessionManager::getUser();

        if ($this->getSenderChecked() == '1') {
            /* 	// company - not empty
              if ($this->getSenderCompany() == "")
              {
              $this->error_list[] = "Sender Company name cannot be blank";
              } */
            // address line 1 - not empty

            if ($this->getSenderAddressLine1() == "") {
                $this->error_list[] = "Sender Address Line 1 cannot be blank";
            }
            // city
            if ($this->getSenderCity() == "") {
                $this->error_list[] = "Sender City cannot be blank";
            }
            // postcode (some countries don't have postcodes).
            if ($this->getSenderPostcode() == "") {
                $this->error_list[] = "Sender Postcode cannot be blank";
            }
        }
        //addressline 3 state validation for TMART
        if ($this->getAddressLine3() != "") {

            $addline3 = explode("¬", $this->getAddressLine3());
            if (sizeof($addline3) > 0) {
                $this->setAddressLine3($addline3[0]);
                if ($addline3[1] != "") {
                    $this->setState($addline3[1]);
                }
            }
        }

        if ($this->getWeight() == '0.000') {

            $this->error_list[] = "Please enter valid weight, Weight should be greater than 0.000Kg .";
        }


        //if(trim($this->getAccount()) == 'ITTEAM')
        /* {
          if(strlen($this->getAddressLine1())>35)
          {
          $addressLine1			=	$this->getAddressLine1();
          $newAddressLine1		=	substr($addressLine1,0,35);
          $this->setAddressLine1($newAddressLine1);
          $newAddressLinePart2	=	substr($addressLine1,35);
          $this->setAddressLine2($newAddressLinePart2 . $this->getAddressLine2());
          }
          if(strlen($this->getAddressLine2())>35)
          {
          $addressLine2			=	$this->getAddressLine2();
          $newAddressLine2		=	substr($addressLine2,0,35);
          $this->setAddressLine2($newAddressLine2);
          $newAddressLinePart3	=	substr($addressLine2,35);
          $this->setAddressLine3($newAddressLinePart3 . $this->getAddressLine3());
          }
          } */

        //addressline 3 state validation for TMART
        if ($this->getNumberPieces() >= 100) {

            $this->error_list[] = "Validation Failed: Number of pieces must be less than 100 to process shipment";
            return (sizeof($this->error_list) == 0);
        }


        if ($this->getService() == Consignment::SERVICE_RETURN && in_array($this->getHandling(), array('PREPAID', 'PAID', 'RTN'))) {
            return (sizeof($this->error_list) == 0);
        }

        // Check Account is not blank
        if ($this->getAccount() == "") {
            $this->error_list[] = "Please enter a valid account number";
        }

        if (Sessionmanager::getUser()->getUserType() == 'warehouse') {
            if ($this->getWarehouseAccountRef() == "") {
                $this->error_list[] = "Please select a valid account number";
            }
        }
        /* --------- Check that user is selected special service code ------------- */
        if ($this->getService() == '') {
            $this->error_list[] = "Please select service";
        }

        
        if ($this->getBothChecked() == 0 && trim($ProductTyep) != 'YES') {
            if ($this->getServiceType() == '') {
                $this->error_list[] = "Please select service type";
            }
        }
        if ($this->getAddressLine1() == "") {
            $this->error_list[] = "Please enter address line 1";
        }
        if ($this->getCity() == "") {
            $this->error_list[] = "Please enter a city";
        }

        if (trim($this->getContact()) == '') {
            $this->error_list[] = "Please enter contact ";
        }

        if ($len = strlen($this->getContact()) > 35) {
            $this->error_list[] = "Contact must be less than 35 characters";
        }
        if ($len = strlen($this->getCompany()) > 35) {
            $this->error_list[] = "Company must be less than 35 characters";
        }
        /* if($len=strlen($this->getDescription())>30)
          {
          $this->error_list[]		=		"Description must be less than 30 characters";
          } */

        if ($this->getDescription() == "") {
            $this->error_list[] = "Please enter a description";
        }


        if (trim(strtolower($this->getWeight())) == 'null') {
            $this->setWeight(0.5);
        } else if (!is_numeric($this->getWeight())) {
            $this->error_list[] = "Weight error only use numbers.";
        } else if ((float)$this->getWeight() <= 0) {
            //$this->error_list[]		=		"Please enter weight greater than 0 Kg.";
        }

        if (!is_numeric($this->getValue())) {
            $this->error_list[] = "Value error only use numbers.";
        }
        // Consignment Value is greater 15

        if (trim($this->getCountry()) == "") {
            $this->error_list[] = "Please select country.";
        }


        // postcode (some countries don't have postcodes).
        if ($this->getPostcode() == "") {
            $countryFilterPostCode = new CountryFilter();
            $countryFilterPostCode->addPostcodeRequiredFilter('NO');
            $countryFilterPostCode->addNameFilter(strtoupper($this->getCountry()));

            if ($countryFilterPostCode->getCount() > 0) {

            } else if ($this->getService() == Consignment::SERVICE_RETURN) {

            } else {
                $this->error_list[] = "Please enter postcode";
            }
        } //Code shifted form consignment edit by Tahir
        else if (strtoupper($this->getCountry()) == "BRAZIL") {
            $postcode = $this->getPostCode();
            $postcode = trim($postcode);
            $position = strpos($postcode, "-");
            if ($position != 5) {
                $firstfive = substr($postcode, 0, 5);
                $lastthree = substr($postcode, 5, 7);
                $actualpostcode = $firstfive . "-" . $lastthree;
                @$this->setPostCode($actualpostcode);
            } else {
                @$this->setPostCode($postcode);
            }
        }

        return (sizeof($this->error_list) == 0);
    }


    /**
     * Export Header
     * - echo CSV-format header row
     * @return void
     */
    public static function exportHeader()
    {
        // output header row
        $header = "";
        $header_row = array(
            "Account",
            "Tracking Number",
            "HAWB",
            "Service",
            "ServiceCode",
            "REF",
            "Date Submitted",
            "Date Label Created",
            "Company",
            "Contact",
            "Address Line 1",
            "Address Line 2",
            "City",
            "Country",
            "Postcode",
            "Telephone",
            "Number of Pieces",
            "Weight",
            "Description",
            "Value",
            "Currency",
            "Notes",
            "Parcel Number",
            "Status",
            "Courier Status",
            "Label",
            "Dimensions(L*W*H)");
        foreach ($header_row as $field) {
            $header .= $field . ",";
        }
        $header .= "\r";
        return $header;
    }

    /**
     * Export Consignment
     * - echo CSV-format consignment row
     * @return void
     */
    public function exportRow()
    {
        $csv = "";
      /*  $parcel_list = $this->getParcels();
        $parcel_count = sizeof($parcel_list);
        //echo $parcel_count; die;

        if ($parcel_count > 0) {
            $trackingNumberStart = $parcel_list[0]->getTrackingNumber();
            foreach ($parcel_list as $parcel) {
                $trackingNumberEnd = $parcel->getTrackingNumber();
            }
        }
        if ($parcel_count > 1)
            $trackingNumber = $trackingNumberStart . "-" . $trackingNumberEnd;
        else*/
       //     $trackingNumber = $trackingNumberStart;
        //if(trim($trackingNumber)== '')
            $trackingNumber = $this->getAwb();

        //  print_r($this); die;
        $descripton = $this->removeChar($this->getDescription());
        $descripton = $this->removeChar($descripton);
        $csv .= cleanCsvCall($this->getUserAccount()) . ",";

        $date_label_created = date("Y-m-d", $this->getDateLabelCreated());
        if ($date_label_created == '1970-01-01')
            $date_label_created = "";


        $csv .= "=\"" . cleanCsvCall($trackingNumber) . "\"". ",";
        $csv .= "=\"" . cleanCsvCall($this->getHawb()) . "\"". ",";
        if($this->getProductName()!= '')
        {
            $csv .= cleanCsvCall($this->getProductName()) . ",";
            $csv .= cleanCsvCall($this->getProductCode()) . ",";
        }
        else {
            $csv .= cleanCsvCall($this->getServiceName()) . ",";
            $csv .= cleanCsvCall($this->getServiceCode()) . ",";
        }
        $csv .= preg_replace('/[\$,]/', '', trim(cleanCsvCall($this->getReference()))) . ",";
        $csv .= date("d-m-Y H:i:s",strtotime($this->getDateCreated())) . ",";
        $csv .= $date_label_created . ",";
        $csv .= cleanCsvCall($this->getCompany()) . ",";
        $csv .= cleanCsvCall($this->getContact()) . ",";
        $csv .= cleanCsvCall($this->getAddressLine1()) . ",";
        $csv .= cleanCsvCall($this->getAddressLine2()) . ",";
        $csv .= cleanCsvCall($this->getCity()) . ",";
        $csv .= cleanCsvCall($this->getCountryName()) . ",";
        $csv .= cleanCsvCall($this->getPostcode()) . ",";
        $csv .= cleanCsvCall($this->getTelephone()) . ",";
        $csv .= cleanCsvCall($this->getNumberPieces()) . ",";
        $csv .= cleanCsvCall($this->getWeight()) . ",";
        $csv .= cleanCsvCall($descripton) . ",";
        $csv .= cleanCsvCall($this->getValue()) . ",";
        $csv .= cleanCsvCall($this->getCurrency()) . ",";
        $csv .= cleanCsvCall($this->getNotes()) . ",";
        $csv .= cleanCsvCall($this->getAwb(),'int') . ",";
        $c_status = $this->getShipmentStatus();
        $csv .=  Translation::GetCaption(Consignment::stateText(strtolower(trim(cleanCsvCall($c_status))))) . ",";
        if ($this->getCourierStatus() != '')
            $csv .= cleanCsvCall($this->getCourierStatus()) . ",";
        else
            $csv .= '' . ",";
        if (trim($this->getLabelFile()) != '')
            $csv .= SETTING_URL_LABEL . str_replace("../", "", $this->getLabelFile()) . ",";
        else
            $csv .= '' . ",";
        $csv .= cleanCsvCall($this->getparcellength()) . '*' . cleanCsvCall($this->getparcelwidth()) . '*' . cleanCsvCall($this->getparcelheight());
        $csv .= "\r";
        return $csv;
    }

    private function removeChar($text)
    {
        $textNew = $text;
        $textNew = str_replace("=", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("'", "\'", preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("|", '\|', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\r\n", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\r", '', preg_replace('/[\$,]/', '', $textNew));
        $textNew = str_replace("\n", '', preg_replace('/[\$,]/', '', $textNew));

        $textNew = preg_replace('/[\n,]/', '', $textNew);

        return $textNew;
    }


    public function getErrorList()
    {
        return $this->error_list;
    }

    public static function getServiceAvailibility($postcode, $handling)
    {

        $postcode = substr($postcode, 0, -2);
        $sql = "select SUBSTR(list_of_available_services,$handling,1) as available from dpdgroups where lookup_code  in (select dpd_services_group from domestic   where postcode_sector = '" . DbAccess3::escape($postcode) . "' )";
        // echo  $sql;
        $rs = DbAccess3::runQuery($sql);

        $row = mysqli_fetch_assoc($rs);
        return $row['available'];
    }

    public function GetDistinctHandlingFromConsignment($id, $col)
    {
        //$sql = "select distinct bag_number, date_scanned, scanned_by from consignment where pallet_no = '$palletno'";

        $sql = "select distinct $col from consignment where Id In ($id)";

        ///mail("mkazim4u@gmail.com", "query", $sql);

        $rs = DbAccess3::runQuery($sql);

        while ($row = mysqli_fetch_assoc($rs)) {
            $r[] = $row;
        }

        return $r;
    }

    public static function GetBagCountInPallet($palletno)
    {
        //$sql = "select distinct bag_number, date_scanned, scanned_by from consignment where pallet_no = '$palletno'";

        $sql = "select distinct bag_number from consignment where pallet_no = '" . DbAccess3::escape($palletno) . "'";

        //echo $sql;

        $rs = DbAccess3::runQuery($sql);

        while ($row = mysqli_fetch_assoc($rs)) {
            $r[] = $row;
        }

        return $r;
    }


    public static function GetTotalWeightAndPieces($flight)
    {
        $totalWeight = 0;
        $totalPieces = 0;
        $consignment_filter = new ConsignmentFilter();
        $list = $consignment_filter->GetAllBagsManifestByFlight($flight);

        foreach ($list as $con) {
            $totalWeight += $con->getWeight();
            $totalPieces += $con->getNumberPieces();
        }

        return $totalWeight . "||" . $totalPieces;
    }

    public function GetVolWeightOfConsignment($conid, $denominator)
    {
        //$date = date("Y-m-d");
        //$date = "2016-05-04";
        $sql = "select sum((length * width * height) / $denominator) 'vol_weight' from parcel 
				where consignment_id IN($conid)";

        $rs = DbAccess3::runQuery($sql);

        while ($row = mysqli_fetch_assoc($rs)) {
            $r[] = $row;
        }

        return $r;
    }


    public static function GetCarrierFromBagNumber($bagNumber)
    {
//		$sql = "select distinct carrier_name from consignment c, services s
//				where c.handling = s.code and bag_number = '".DbAccess3::escape($bagNumber)."'";
        $sql = "SELECT distinct car.carrier AS carrier_name
                        FROM consignment c
                          INNER JOIN consignment_bagging_mapping cgm
                            ON cgm.consignmentid = c.id
                          INNER JOIN bagging bag
                            ON bag.id = cgm.bagid
                          INNER JOIN services s
                            ON s.id = c.service_id
                          INNER JOIN carrier car
                            ON car.id = s.carrier_id
                        WHERE bag.bagnumber = '" . DbAccess3::escape($bagNumber) . "'";

        $rs = DbAccess3::runQuery($sql);

        $data = mysqli_fetch_assoc($rs);

        //echo $sql . $data['carrier'];

        return $data['carrier_name'];
    }



    public static function GetListOfSupplierScannedReturnShipments($parentid, $status, $account, $col = '', $handling = '')
    {

        if (($status == 'relabel' || $status == 'return hold' || $status == 'ready to dispatch') && $parentid == 0)
            $arg = " and c.scanned_by = '" . DbAccess3::escape($account) . "' ";
        else
            $arg = " and c.account in (select user_account from user where parentid =  $parentid or id = $parentid) ";

        if ($handling != '')
            $arg .= " and handling = '" . DbAccess3::escape($handling) . "'";

        if ($col == '')
            $col = "c.id, awb, c.account 'account', c.scanned_by 'processedby', c.date_created";


        /* $sql = "select distinct $col from
          tracking_data_history tdh, consignment c
          where c.id =  tdh.consignment_id
          and tdh.status_code = 'Returned' and c.consignment_status = '$status' $arg";
         */


        $sql = "select distinct $col from consignment c
				where c.consignment_status = '" . DbAccess3::escape($status) . "' $arg";


        //echo $sql;

        $rs = DbAccess3::runQuery($sql);

        while ($row = mysqli_fetch_assoc($rs)) {
            $r[] = $row;
        }

        return $r;
    }


    public static function getServiceIntAvailibility($countrycode, $postcode, $servicetype, $serviceName = '')
    {
        if ($serviceName == 'STDPDNL19') {
            if ($countrycode == 'NL')

                $routing_postcode = substr($postcode, 0, 4);
            else
                $routing_postcode = $postcode;
        } else if (strpos($postcode, "-") > 0) {
            $split_postcode = explode("-", $postcode);
            $routing_postcode = $split_postcode[1];
        } else {
            $routing_postcode = $postcode;
        }
        $sql = "select  $servicetype from international where  iata_country_code = '" . DbAccess3::escape($countrycode) . "' and zipcode_from = 0 and zipcode_to = 'Z'";
        $rs = DbAccess3::runQuery($sql);
        //mail("kazim@oneworldexpress.com", "dpd-nl", $sql);

        $rows = mysqli_num_rows($rs);

        if ($rows > 0) {
            $row = mysqli_fetch_assoc($rs);
            return $row[$servicetype];
        } else {
            $user = SessionManager::getUser();

            $sql = "select $servicetype from international where iata_country_code = '$countrycode'
                            and zipcode_to >=  CAST('" . $routing_postcode . "' AS UNSIGNED) and zipcode_from <=  CAST('" . $routing_postcode . "' AS UNSIGNED)";


            /* $sql = "select $servicetype from international where iata_country_code = '$countrycode'
              and zipcode_to >=  '".DbAccess3::escape($routing_postcode)."' and zipcode_from <= '".DbAccess3::escape($routing_postcode)."'"; */
            //exit;
            //	mail("mruga@oneworldexpress.com", "dpd-nl", $sql . $servicetype);
            $rs = DbAccess3::runQuery($sql);

            $rows = mysqli_num_rows($rs);

            if ($rows > 0) {
                $row = mysqli_fetch_assoc($rs);
                return $row[$servicetype];
            }
        }
        return "";
    }

    public static function findRoutingCode($countrycode, $postcode)
    {
        if ($countrycode == 'NL') {
            $routing_postcode = substr($postcode, 0, 4);
        } else if (strpos($postcode, "-") > 0) {
            $split_postcode = explode("-", $postcode);
            $routing_postcode = $split_postcode[1];
        } else {
            $routing_postcode = $postcode;
        }


        $sql = "select zipcode_from , zipcode_to, air_express_depot, air_express_osort, air_express_dsort, dpd_classic_deport, dpd_classic_osort, dpd_classic_dsort from international where  iata_country_code = '" . DbAccess3::escape($countrycode) . "' and zipcode_from = 0 and zipcode_to = 'Z'";
        $rs = DbAccess3::runQuery($sql);
        $rows = mysqli_num_rows($rs);
        if ($rows > 0) {
            $row = mysqli_fetch_assoc($rs);
            return $row['zipcode_from'] . "|" . $row['zipcode_to'] . "|" . $row['air_express_depot'] . "|" . $row['air_express_osort'] . "|" . $row['air_express_dsort'] . "|" . $row['dpd_classic_deport'] . "|" . $row['dpd_classic_osort'] . "|" . $row['dpd_classic_dsort'];
        } else {
            $sql = "select zipcode_from , zipcode_to, air_express_depot, air_express_osort, air_express_dsort, dpd_classic_deport,
                           dpd_classic_osort, dpd_classic_dsort  from international where iata_country_code = '" . DbAccess3::escape($countrycode) . "'
                            and zipcode_to >=  CAST('" . $routing_postcode . "' AS UNSIGNED) and zipcode_from <= CAST('" . $routing_postcode . "' AS UNSIGNED)";

            $rs = DbAccess3::runQuery($sql);
            $rows = mysqli_num_rows($rs);
            if ($rows > 0) {
                $row = mysqli_fetch_assoc($rs);
                return $row['zipcode_from'] . "|" . $row['zipcode_to'] . "|" . $row['air_express_depot'] . "|" . $row['air_express_osort'] . "|" . $row['air_express_dsort'] . "|" . $row['dpd_classic_deport'] . "|" . $row['dpd_classic_osort'] . "|" . $row['dpd_classic_dsort'];
            } else
                return "";
        }
    }

    public static function findCountrycodesRouting($country)
    {
        $sql = "select numcode, allow_express,allow_classic,eu_country,shipping_advice from country where name = '" . DbAccess3::escape($country) . "'";

        $rs = DbAccess3::runQuery($sql);
        $rows = mysqli_num_rows($rs);
        if ($rows > 0) {
            $row = mysqli_fetch_assoc($rs);
            return $row['iso_number'] . "|" . $row['allow_express'] . "|" . $row['allow_classic'] . "|" . $row['eu_country'] . "|" . $row['shipping_advice'];
        } else
            return "";
    }

    /*     * *
     * Controller logic goes here
     */

    public static function getInstantLabel(Consignment $consignment,$labelType = 'pdf',$size = '100x150',$returnMsg = false,$dropOffLabel=false,$overlabel=true,$dataEnty=false)
    {
        if(!empty($consignment->getAwb()) && !empty($consignment->getLabelFile())){
            if (file_exists(_ASSETS_PATH . "pdf/" . $consignment->getLabelFile()) || $dataEnty == true) {
                $results["STATUS"] = "SUCCESS";
                $results["LABEL"] = SETTING_URL . "_assets/pdf/" . $consignment->getLabelFile();
                $results["LABEL_BIN_STR"] = base64_encode(file_get_contents(_ASSETS_PATH . "pdf/" . $consignment->getLabelFile()));
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addConsignmentIdFilter($consignment->getId());
                $parcelFilter->AddOrderBy("id",true);
                $parcelData = $parcelFilter->getColumnList('tracking_number, parcel_label');
                $trackingNummber = [];
                $parcelLabels = [];
                if(count($parcelData) > 0){
                    foreach ($parcelData as $parcelDataArr) {
                        $trackingNummber[] =$parcelDataArr->getTrackingNumber();
                        if(!empty($parcelDataArr->getParcelLabel()) && file_exists(_ASSETS_PATH . "pdf/" . $parcelDataArr->getParcelLabel())) {
                            $parcelLabels[] = [
                                'tracking_number' => $parcelDataArr->getTrackingNumber(),
                                'url' => SETTING_URL . "_assets/pdf/" . $parcelDataArr->getParcelLabel(),
                                'label_bin_string' => base64_encode(file_get_contents(_ASSETS_PATH . "pdf/" . $parcelDataArr->getParcelLabel())),
                            ];
                        }
                    }
                }
                $results["TRACKING_NUMBER"] = $trackingNummber;
                $results["PARCEL_LABEL"] = $parcelLabels;
                $results["AWB"] = [$consignment->getAwb()];
                $results["CONSIGNMENT_ID"] = $consignment->getId();
                $results["ORDER_REFERENCE"] = $consignment->getHawb();
                $results["HAWB"] = $consignment->getHawb();
                $results["ID"] = $consignment->getId();
                return $results; die;
                $labelNotFound = false;
            }else{
                $results['STATUS'] = 'ERROR';
                $results['ERROR'][] = "label is archived. Please contact system administrator";
                $results['MESSAGE'] = "label is archived. Please contact system administrator";
                return $results;
                die;
            }
        }
        $labelCreated = "not_created";
        $sessionUser = Sessionmanager::getUser();
        $userAccountArr = [];
        $accountPaymentArr = [];
        // Label Charges Variable
        $userId = $consignment->getUserId();
        $user = new User($userId);
        $userAccountId = $user->getUserAccountId();
        $userAccouont = new CustomerAccount($userAccountId);
        $isPrepaid = $userAccouont->getIsPrepaid();
        $fromCountryId = $user->getCountryId();
        $toCountryId = $consignment->getCountryId();
        $serviceId = ($consignment->getCustomizedServiceId() > 0 ? $consignment->getCustomizedServiceId():$consignment->getServiceId());
        $servicesDetails = new Services($consignment->getServiceId());
        
        $toPostcode = $consignment->getPostcode();
        $fromPostcode = "";
        $toCity = $consignment->getCity();
        $fromCity = "";
        $tariffChargesArr = [];
        $tariffCharges = [];
        $consignemntLabelCharges = [];
        $weight = $consignment->getWeight();
        $numberPieces = $consignment->getNumberPieces();
        
        
        if($dropOffLabel == false){
            // Check Balance Add function here
             if(!Permissions::checkFilePermission('CREATE_OTHER_USER_SHIPMENT')){
                $results = Consignment::getBalanceCheck($consignment);
            }
            if(isset($results['STATUS']) && $results['STATUS'] == "ERROR"){
                return $results;
                die;
            }
        }

        $className = '';
        $className = Consignment::IncludeCarrierClass($consignment);
        if($className === false){
            $results['STATUS'] = 'ERROR';
            $results['ERROR'][] = "Service provider[ ".$className." ] is not defined. Please contact system administrator";
            $results['MESSAGE'] = "Service provider[ ".$className." ] is not defined. Please contact system administrator";
            return $results;
            die;
        }
        //echo "me here 1".$className; die;
        /*
         * Initilize the variables
         */
        ///////////////////////////////////////////////////
        // Create directory if not exist
        chdir('..');
        chdir('_assets/pdf/');
        $currentDirecotryPath = str_replace('\\', '/', getcwd()) . "/";
        $path = $currentDirecotryPath . date("Y_m_d", time()) . "/";
        if (!file_exists($path))
            @mkdir($path, 0777);
        chdir('../../main');
        // end Create directory if not exist

        $apiValue = new $className(); //  Calling the constructor
        $results = $apiValue->label($consignment,$labelType,$size);
        if($dropOffLabel && method_exists($apiValue, "setLinkService")){
             
            $apiValue->setLinkService(true);
             
        }
        $labelDataLink = '';
        $parcelLabels = [];
        if (trim($results['STATUS']) == 'SUCCESS') {
            
            if($consignment->getIsWhiteLabel() > 0 && $overlabel)
            {

                 $whitelabel = strtolower("../includes/labels/whitelabel.class.php");
                if (file_exists($whitelabel))
                    require_once $whitelabel;
                $overLabel          =   new WhiteLabel(); //  Calling the constructor
                $resultsOverLabel   =   $overLabel->label($consignment,$labelType,$size);
                $labelData          =   $currentDirecotryPath.$resultsOverLabel['LABEL'];
                $labelDataLink      = SETTING_URL . "_assets/pdf/" . $resultsOverLabel['LABEL'];
                if(!empty($resultsOverLabel['LABEL'])){
                    $labelCreated = "created";
                }
            }
            else
            {
               
                if(strtolower($labelType) == "png"){
                    
                    //check directory and make folder
                    $path = "../_assets/png/" . date("Y_m_d", time()) . "/";
                    if (!file_exists($path))
                        @mkdir($path, 0777, true);
                    
                    $pdflabel =  SETTING_DIR_ASSETS . "pdf/" . $results['LABEL'];
                    $pngLabel = SETTING_DIR_ASSETS ."png/" . date('Y_m_d') . '/'  . $consignment->getId() . ".png";
                    $convertCommand = "convert -flatten -density 300  ".$pdflabel." -quality 100 ".$pngLabel;
                    exec($convertCommand);
                    if(file_exists($pngLabel)){
                        $labelData          =   $pngLabel;
                        $labelDataLink = SETTING_URL . "_assets/png/"  . date('Y_m_d') . '/'  . $consignment->getId() . ".png";
                    }
                }
                else{
                    $labelData          =   $currentDirecotryPath.$results['LABEL'];
                    $labelDataLink      =   SETTING_URL . "_assets/pdf/" . $results['LABEL'];
                    
                    if(strtoupper($consignment->getCreatedFrom()) == 'SKU')
                    {
                        $labelDataLinkFBO      =   SETTING_DIR_ASSETS . "pdf/" . $results['LABEL'];
                        require_once("../includes/labels/wmsfbo.class.php");
                        $wmsfbo = New WMSFBO();
                        $fboResult = $wmsfbo->label($consignment,'pdf',$size); 
                        if($fboResult['STATUS'] == 'SUCCESS')
                        {
                            $FBOLabelLink = SETTING_DIR_ASSETS . "pdf/" . $fboResult['LABEL'];
                            $PDFMerger = new PDFMerger();
                            $PDFMerger->addPDF($labelDataLinkFBO);
                            $PDFMerger->addPDF($FBOLabelLink);
                            try {
                                $PDFMerger->merge('file',$labelDataLinkFBO);
                            } catch (Exception $e) {
                                echo 'Caught exception: ', $e->getMessage(), "\n";
                            }
                        }
                    }
                    
                    if(($servicesDetails->getIsCommercials()== 'required' || $servicesDetails->getIsCn()== 'required') && $consignment->getCountryId() != $consignment->getSenderCountryId()){
                        require_once("../includes/labels/cn22.class.php");
                        require_once("../includes/labels/commercialinvoice.class.php");
                        if($labelType == 'zpl'){
                           
                            $labelLink = file_get_contents($labelDataLink);
                            if($servicesDetails->getIsCn()== 'required'){
                                $cn22Obj = new Cn22();
                                $cn22 = $cn22Obj->getCn22($consignment, $results['TRACKING_NUMBER'][0], $labelType, null, null); 
                            } 
                            else 
                                $cn22 = "";
                            if($servicesDetails->getIsCommercials()== 'required'){
                            $commercialinvoiceObj = new Commercialinvoice();
                            $commercialinvoice = $commercialinvoiceObj->getCommercialinvoice($consignment, $results['TRACKING_NUMBER'][0], $labelType, null, null);
                            } 
                            else 
                                $commercialinvoice = "";
                            $allLabelData = $labelLink."\r\n".$cn22."\r\n".$commercialinvoice;

                          file_put_contents($currentDirecotryPath. $results['LABEL'], $allLabelData);
                        } else if($labelType == 'pdf'){
                            $page_size = array(100, 150);
                            $pdfCnD = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            if($servicesDetails->getIsCn()== 'required'){
                                $cn22Obj = new Cn22();
                                $cn22 = $cn22Obj->getCn22($consignment, $results['TRACKING_NUMBER'][0], $labelType, $pdfCnD, $page_size); 
                            }
                            if($servicesDetails->getIsCommercials()== 'required'){
                                $commercialinvoiceObj = new Commercialinvoice();
                                $commercialinvoice = $commercialinvoiceObj->getCommercialinvoice($consignment, $results['TRACKING_NUMBER'][0], $labelType, $pdfCnD, $page_size);
                            }
                            $cnDocs = "../_assets/pdf/" . date('Y_m_d') . '/cn_doc_' . $consignment->getId() . ".pdf";
                            $pdfCnD->Output("../_assets/pdf/" . date('Y_m_d') . '/cn_doc_' . $consignment->getId() . ".pdf", "F");
                            $PDFMerger = new PDFMerger();
                            $PDFMerger->addPDF($labelData);
                            $PDFMerger->addPDF($cnDocs);
                            try {
                                $PDFMerger->merge('file',$labelData);
                            } catch (Exception $e) {
                                echo 'Caught exception: ', $e->getMessage(), "\n";
                            }
                        }
                    }
                }
                if(!empty($results['LABEL'])){
                    $labelCreated = "created";
                }
            }

            if(file_exists($labelData)){
                $results['LABEL_BIN_STR'] = base64_encode(file_get_contents($labelData));
            }
			$consignment->setAwb($results['TRACKING_NUMBER'][0]);
			$consignment->setLabelFile($results['LABEL']);
            
            $consignment->setConsignmentStatus(self::$database_status_array[self::STATUS_LABEL_CREATED]);
            $consignment->setShipmentStatus(self::STATUS_LABEL_CREATED);
            $consignment->setDateLabelCreated(time());
            $consignment->setMessage('');

            $warehouseid = $sessionUser->getWarehouseId();
            $warehouseName = '';
            $countryIso3 = '';
            $parcelLabels = [];
            if ($warehouseid > 0) {
                $warehouseObj = new Warehouse($warehouseid);
                $warehouseName = $warehouseObj->getWarehouseName();
            }

            $countryId = $sessionUser->getCountryId();
            $country = new Country($countryId);
            if (count($country) > 0)
                $countryIso3 = $country->getIso3();
            $trackpoint = $warehouseName . " - " . $countryIso3;
            $parcelTrackingNo = [];
            $consignmentParcels = $consignment->getParcels();
                if (count($consignmentParcels) > 0) {
                    foreach ($consignmentParcels as $consignmentParcel) {
                        $parcelTrackingNo[] = $consignmentParcel->getTrackingNumber();
                        /*if (($key = array_search($consignmentParcel->getTrackingNumber(), $results['TRACKING_NUMBER'])) !== false)
                        {
                            unset($results['TRACKING_NUMBER'][$key]);
                        }*/
                        $parcelId = $consignmentParcel->getId();
                        if(!empty($consignmentParcel->getParcelLabel()) && file_exists(_ASSETS_PATH . "pdf/" . $consignmentParcel->getParcelLabel())) {
                            $parcelLabels[] = [
                                'tracking_number' => $consignmentParcel->getTrackingNumber(),
                                'url' => SETTING_URL . "_assets/pdf/" . $consignmentParcel->getParcelLabel(),
                                'label_bin_string' => base64_encode(file_get_contents(_ASSETS_PATH . "pdf/" . $consignmentParcel->getParcelLabel())),
                            ];
                        }
                        $trackingData = [
                            'user_id' => $sessionUser->getId(),
                            'entity_id' => $parcelId,
                            'entity_type' => 'parcel',
                            'tracking_number' => $consignmentParcel->getTrackingNumber(),
                            'track_point' => $trackpoint,
                            'date_created' => date("Y-m-d H:i:s"),
                            'ip_address' => getClientIp(),
                            'status_code_id' => 144,
                            'warehouse_id' => $warehouseid,
                            'carrier_code' => '',
                            'carrier_desc' => Tracking::$oneworld_status_code[144],
                            'signatory' => ''
                        ];
                        $trackingDataObj = new TrackingData($trackingData);
                        $trackingDataObj->save();
                    }
                }
                $awbArr = array_diff($results['TRACKING_NUMBER'], $parcelTrackingNo);
                if(count($awbArr) > 0){
                    $trackingData = [
                            'user_id' => $sessionUser->getId(),
                            'entity_id' => $consignment->getId(),
                            'entity_type' => 'shipment',
                            'tracking_number' => $awbArr[0],
                            'track_point' => $trackpoint,
                            'date_created' => date("Y-m-d H:i:s"),
                            'ip_address' => getClientIp(),
                            'status_code_id' => 144,
                            'warehouse_id' => $warehouseid,
                            'carrier_code' => '',
                            'carrier_desc' => Tracking::$oneworld_status_code[144],
                            'signatory' => ''
                        ];
                        $trackingDataObj = new TrackingData($trackingData);
                        $trackingDataObj->save();
                }
                
                if($numberPieces < count($results['TRACKING_NUMBER'])){
                array_shift($results['TRACKING_NUMBER']);
            }
                /*
                *  Assign Label Pricing
                */
                if(!Permissions::checkFilePermission('CREATE_OTHER_USER_SHIPMENT')){
                    self::consignment_label_pricing($consignment); // Why we are using this should we remove this? @tahir
                }
//                if($isPrepaid == 1){
//                    /*
//                    * Assign Label Charges
//                    */
//                    $paymentHistory = new PaymentsHistory();
//                    $paymentHistory->setAccountId($userAccountId);
//                    $paymentHistory->setAmount($consignemntLabelCharges);
//                    $paymentHistory->setAmountCurrencyId($userAccouont->getBillingCurrency());
//                    $paymentHistory->setPaymentDetail('Consigenment Label Charges');
//                    $paymentHistory->setDebit($consignemntLabelCharges);
//                    $paymentHistory->setIsCompleted('yes');
//                    $paymentHistory->setDateAdded(time());
//                    $paymentHistory->setAddedBy($sessionUser->getId());
//                    $paymentHistory->save();
//                }
        } else {
            if (empty($results['MESSAGE'])){
                $results['MESSAGE'] = 'Carrier /Supplier API is not responding, Please contact ITSupport Team';
            }
            $consignment->setConsignmentStatus(self::$database_status_array[self::STATUS_INVALID]);
            $consignment->setShipmentStatus(self::STATUS_INVALID);
            $consignment->setMessage($results['MESSAGE']);
            $results['STATUS'] = "ERROR";
            $results['ERROR'][] = $results['MESSAGE'];
            $results['MESSAGE'] = $results['MESSAGE'];
        }

        $consignment->eventKey = "Label";
       // print_r($consignment); die;
        $consignment->save();
        DbAccess3::runQuery("UPDATE parcel set parcel_status_code = '" . $consignment->getShipmentStatus() . "' , owe_status_code = '" .self::$database_status_array[$consignment->getShipmentStatus()] . "' where consignment_id = '" . $consignment->getId() . "' AND consignment_id > 0;");
	    if ($returnMsg && $results['STATUS'] != "ERROR") {
                $results['LABEL'] = $labelDataLink;//SETTING_URL . "_assets/pdf/" . $results['LABEL'];
                $results['AWB'] = $consignment->getAwb();
                $results['ORDER_REFERENCE'] = $consignment->getHawb();
                $results['AWB'] = $consignment->getAwb();
                $results['PARCEL_LABEL'] = $parcelLabels;
                $results['LABEL_CREATED'] = $labelCreated;
            }
        return $results;
    }
    public static function getBalanceCheck($consignment){
        $sessionUser = Sessionmanager::getUser();
        $userAccountArr = [];
        $accountPaymentArr = [];
        // Label Charges Variable
        $userId = $consignment->getUserId();
        $user = new User($userId);
        $userAccountId = $user->getUserAccountId();
        $userAccouont = new CustomerAccount($userAccountId);
        $isPrepaid = $userAccouont->getIsPrepaid();
        $fromCountryId = $user->getCountryId();
        $toCountryId = $consignment->getCountryId();
        $serviceId = ($consignment->getCustomizedServiceId() > 0 ? $consignment->getCustomizedServiceId():$consignment->getServiceId());
        $toPostcode = $consignment->getPostcode();
        $fromPostcode = $consignment->getSenderPostcode();
        $toCity = $consignment->getCity();
        $fromCity = $consignment->getSenderCity();
        $tariffChargesArr = [];
        $tariffCharges = [];
        $consignemntLabelCharges = [];
        $weight = $consignment->getWeight();
        if($consignment->getIsDeadWeightChargable() == '1')
        {
            $weight = $consignment->getWeight();
        }
        else
        {
            //$weight = $consignment->getVolWeight();
            //by irshad
            $weight = $consignment->getChargeWeight();
        }
        $numberPieces = $consignment->getNumberPieces();
            // Check if account have balance if not then return from here with error message
            $accountPaymentArr = checkBalance($userAccountId);
            $userAccountArr = CustomerAccount::accountParentAccount($userAccountId);
            /*
            * Pass consignment id
            * Return Maximum column value from that consignment's parcel (lenght, widht , height)
            */
            $maxDim = getMaxDim($consignment->getId());
            if(count($userAccountArr) > 0){
                $accountOwnContract = 0;
                foreach ($userAccountArr as $userAccountIdArr) {
                    $curUserAccountId = $userAccountIdArr->getId();
                    $curUserAccountParrentId = $userAccountIdArr->getParentid();
                    if($curUserAccountParrentId > 0){
                        $ownContractCheck = UserServicesRoutingFilter::isOwnUserContract($serviceId,$curUserAccountId);
                        if($ownContractCheck == true && $accountOwnContract == 0)
                            $accountOwnContract = 1;
                        $tariffCharges = Tariffs::getUserQuotationsByAssignedServices($curUserAccountId, $fromCountryId, $toCountryId, $fromPostcode, $toPostcode, $fromCity, $toCity, $weight, $numberPieces, $serviceId,0,$maxDim, $accountOwnContract,$consignment->getShipmentType());
                        
                        if($curUserAccountId == $userAccountId){
                           $consignemntLabelCharges = $tariffCharges;
                        }
                        $tariffChargesArr[] = isset($tariffCharges['QUOTATIONS'][0]['TOTAL']) ? $tariffCharges['QUOTATIONS'][0]['TOTAL'] : 0;
                    }
                }
            }
            if(in_array(0, $tariffChargesArr)){
                $results['STATUS'] = 'ERROR';
                $results['ERROR'][] = "Label can not be generated. Tariff not found";
                $results['MESSAGE'] = "Label can not be generated. Tariff not found";
                return $results;
                die;
            }
            if (!is_array($accountPaymentArr)) {
                $accountPaymentArr[] = 0;
            }
            if (isset($consignemntLabelCharges['STATUS']) && $consignemntLabelCharges['STATUS'] == "SUCCESS") {
                if (isset($consignemntLabelCharges['QUOTATIONS'][0]['TOTAL']) && min($accountPaymentArr) >= $consignemntLabelCharges['QUOTATIONS'][0]['TOTAL'] && min($accountPaymentArr) > 0) {
                } else if (isset($consignemntLabelCharges['QUOTATIONS'][0]['TOTAL']) && $consignemntLabelCharges['QUOTATIONS'][0]['TOTAL'] == 0) {
                    $results['STATUS'] = 'ERROR';
                    $results['ERROR'][] = "Label can not be generated. Tariff not found";
                    $results['MESSAGE'] = "Label can not be generated. Tariff not found";
                    return $results;
                    die;
                } else {
                    $results['STATUS'] = 'ERROR';
                    $results['ERROR'][] = "Credit is not available. Please top up your account or contact Admin";
                    $results['MESSAGE'] = "Credit is not available. Please top up your account or contact Admin";
                    return $results;
                    die;
                }
            } else if (isset($consignemntLabelCharges['STATUS']) && $consignemntLabelCharges['STATUS'] == "ERROR") {
                $results['STATUS'] = 'ERROR';
                $results['ERROR'][] = $consignemntLabelCharges['ERROR'];
                $results['MESSAGE'] = $consignemntLabelCharges['MESSAGE'];
                return $results;
                die;
            } else {
                $results['STATUS'] = 'ERROR';
                $results['ERROR'][] = "Label can not be generated. Please contact to administrator at info@smarttrack.co";
                $results['MESSAGE'] = "Label can not be generated. Please contact to administrator at info@smarttrack.co";
                return $results;
                die;
            }
    }
    public static function consignment_label_pricing(Consignment $consignment)
    {
        $userId = (int)$consignment->getUserId();
        $consignmentId = (int)$consignment->getId();
        $serviceId = (int)$consignment->getServiceId();
        $countryId = (int)$consignment->getCountryId();
        $numberPieces = (int)$consignment->getNumberPieces();
        $weight = $consignment->getWeight();
        $postcode = $consignment->getPostcode();

        $DiscountAmount = 0;
        $labelPrice = 0;

        if ($userId > 0) {
            $userData = new User($userId);
            if ($userData->getid() > 0) {
                $userAccountData = new CustomerAccount($userData->getUserAccountId());
                if ($userAccountData->getId() > 0) {
                    $labelPrice = $userAccountData->getLabelPrice();
                    $DiscountPercent = $userAccountData->getDiscount();
                    if (is_numeric($DiscountPercent) && $DiscountPercent > 0) {
                        $DiscountAmount = (($labelPrice * $DiscountPercent) / 100);
                    }
                }
            }
        }

///////////////////////////////////// PLEASE ROLLBACK THE BELOW CODE IF ANY PROBLEM ///////////////////////////////////////


        $host = SETTING_DB_SERVER;
        $user = SETTING_DB_USER;
        $password = SETTING_DB_PASSWORD;
        $db = SETTING_DB_DATABASE;
        $userAccountId = $userData->getUserAccountId();
        //$origincountryId = $userAccountData->getCountryId();
        $mysqli = new mysqli($host, $user, $password, $db);
        if ($mysqli->connect_errno) {
            return "ERROR||Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        $herarchialSql = "CALL tariff_hierarchical_pricing($consignmentId,'customer', '" . $userAccountId . "')";
        if (!($res = $mysqli->query($herarchialSql))) {
            echo "ERROR||CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
		/* update account balance */
		CustomerAccount::updateBalance($userAccountId);
		/******************************/
		
        /*        $invoiceDetail = new InvoiceDetailFilter();
                $invoiceDetail->addQueryFilter(" consingment_id = '" . $consignment->getId() . "' and ( invoice_id <= 0 OR invoice_id is null ) ");
                $invoiceDetailList = $invoiceDetail->getColumnList('id');

                $invoiceDetailId = (count($invoiceDetailList)>0)? (int) $invoiceDetailList[0]->getId() : 0 ;

                $invoiceDetail = new InvoiceDetail($invoiceDetailId);
                $invoiceDetail->setConsignmentId($consignmentId);
                $invoiceDetail->setDateCreated(date("Y-m-d h:i:s"));
                $invoiceDetail->setLabelPrice($labelPrice);
                $invoiceDetail->setLabelDiscount($DiscountAmount);
                $invoiceDetail->setAmount();
                $invoiceDetail->save();*/
    }

    public function _RecycledShipment($consignemntId)
    {
        $rtn = [];
        $output = [];
        $apiReturn = [];
        // TODO: Implement save() method.
        $consignment = new Consignment((int)$consignemntId);

        if ($consignment->getId() > 0) {
            if($consignment->getIsCustomerManifested() == 1)
            {
                $apiReturn[] = $consignment->getHawb() . " cannot removed because it is already been manifested.";
                $output["message"] .= $consignment->getHawb() . " cannot removed because it is already been manifested.";
                $output["error"][] = $consignment->getHawb() . " cannot removed because it is already been manifested.";
                $output["status"] = "ERROR";
                return $output;

            } else if (in_array($consignment->getShipmentStatus(), array(self::STATUS_INVALID, self::STATUS_READY_TO_PRINT,self::STATUS_NEW, self::STATUS_LABEL_CREATED))  ) {
                $className = '';
                $className = self::IncludeCarrierClass($consignment);
                if ($className !== false) {
                    $apiValue = new $className(); //  Calling the constructor
                    $resultsResponse = $apiValue->recycledShipment($consignment);
                    if (trim($resultsResponse["STATUS"]) == 'SUCCESS') {
                        $oldStatus = $consignment->getConsignmentStatus();
                        $message = '';
                        $ConsignmentLog = new ConsignmentLog();
                        $consignment->setConsignmentStatus(self::$database_status_array[self::STATUS_RECYCLED]);
                        $consignment->setShipmentStatus(self::STATUS_RECYCLED);
                        $parcels = new ParcelFilter();
                        $parcels->updateParcelStatusOnConsignmentStatusChange($consignemntId, self::STATUS_RECYCLED, self::$database_status_array[self::STATUS_RECYCLED]);
                        $consignment->save();
                        //
                        $ConsignmentLog->createlog("Consignment Status Changed from " . Consignment::$database_status_array[$oldStatus] . " to " . Consignment::$database_status_array[$consignment->getShipmentStatus()] . ".", $consignment->getId());

                        $output["message"] .= $consignment->getHawb() . " has been deleted successfully.<br />";
                        $apiReturn[] = $consignment->getHawb() . " has been deleted successfully.";
                        $output["status"] = "SUCCESS";
                    } else {
                        $output["message"] .= $consignment->getHawb() . " " . ((trim(@$resultsResponse["MESSAGE"]) != '') ? @$resultsResponse["MESSAGE"] : "got error form api call.");
                        $output["error"][] = $consignment->getHawb() . " " . ((trim(@$resultsResponse["MESSAGE"]) != '') ? @$resultsResponse["MESSAGE"] : "got error form api call.");
                        $apiReturn[] = $consignment->getHawb() . " " . ((trim(@$resultsResponse["MESSAGE"]) != '') ? @$resultsResponse["MESSAGE"] : "got error form api call.");
                        $output["status"] = "ERROR";
                    }
                } else {
                    $output["message"] .= "Carrier integration not found.";
                    $output["error"][] = "Carrier integration not found.";
                    $apiReturn[] = "Carrier integration not found.";
                    $output["status"] = "ERROR";
                }
            } else {
                if (in_array($consignment->getShipmentStatus(), array(self::STATUS_RECYCLED))){
                    $output["message"] .= $consignment->getHawb() . " has been ".Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_RECYCLED])."<br>";
                    $apiReturn[] = $consignment->getHawb() . " has been ".Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_RECYCLED]);
                    $output["status"] = "SUCCESS";
                }
                else{
                    $output["message"] .= $consignment->getHawb() . " cannot removed. Because shipment status is ".Translation::GetCaption(Consignment::$status_array[$consignment->getShipmentStatus()]).".<br>";
                    $apiReturn[] = $consignment->getHawb() . " cannot removed. Because shipment status is ".Translation::GetCaption(Consignment::$status_array[$consignment->getShipmentStatus()]) . ".";
                    $output["status"] = "ERROR";
                }
            }
        } else {
            $output["message"] .= "Can not find shipment to ".Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_HOLD])."<br>";
            $output["error"][] = "Can not find shipment to ".Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_HOLD])."<br>";
            $apiReturn[] = "Can not find shipment to ".Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_HOLD]);
            $output["status"] = "ERROR";
        }
        $rtn['output'] = $output;
        $rtn['api_return'] = $apiReturn;
        return $rtn;
    }

    public function RecycledShipment($consignmentArray,$apiReq = false,$recycleLinkedShipment=false)
    {
        $apiReturn = [];
        $output = array();
        if (count($consignmentArray) > 0) {
            foreach ($consignmentArray as $conIndex => $conId) {
                $funRtn = $this->_RecycledShipment($conId);
                $output = $funRtn['output'];
                $apiReturn[] = (is_array($funRtn['api_return'])?$funRtn['api_return'][0]:$funRtn['api_return']);
                if($recycleLinkedShipment && isset($output['status']) && $output['status'] == "SUCCESS"){
                    // Remove linked shipment
                    $consignmentDropoffMappingFilter = New ConsignmentDropoffMappingFilter();
                    $consignmentDropoffMappingFilter->addFieldFilter("   dropoff_consignment_id",$conId);
                    $consignmentDropoffMappingObj = $consignmentDropoffMappingFilter->getColumnList('dispatch_consignment_id');
                    if(count($consignmentDropoffMappingObj) > 0){
                        $dispatchConsignmentId = $consignmentDropoffMappingObj[0]->getDispatchConsignmentId();                             $disFunRtn = $this->_RecycledShipment($dispatchConsignmentId);
                        $output = $disFunRtn['output'];
                        $apiReturn = $disFunRtn['api_return'];
                        $apiReturn[] = (is_array($disFunRtn['api_return'])?$disFunRtn['api_return'][0]:$disFunRtn['api_return']);
                    }
                }
            }
        } else {
            $output["status"] = "ERROR";
            $output["message"] = "No shipment has been selected.";
            $output["error"][] = "No shipment has been selected.";
        }
        if($apiReq){
            $output["message"] = $apiReturn;
            return $output;
        }
        else{
            return $output;
        }
    }

    public static function IncludeCarrierClass(Consignment $consignment)
    {
        $serivceAgentMapping = new ServiceAgentMappingDataFilter();
        $serivceAgentMapping->addFilter(" serviceid = '" . $consignment->getServiceId() . "' and agentid = '" . $consignment->getAgentId() . "'");
        $serviceAgentMappintRecordSet = $serivceAgentMapping->getList();
        if (count($serviceAgentMappintRecordSet) > 0) {
            foreach ($serviceAgentMappintRecordSet as $agentData) {
                $className = $agentData->getClassFileName();
            }
        }

        if (trim($className) == '') {
            $serivces = new Services($consignment->getServiceId());
            $className = trim($serivces->getLabelClassName());
        }

        if (trim($className) != '') {

            $file = "../includes/labels/" . strtolower($className) . ".class.php";
            if (is_file($file)) {
                require_once($file);
                return $className;
            } else
                return false;

        } else
            return false;
    }
    
    /*
     * Collection Reschedule
     */
    
    public static function getCollectionReschedule($consignment, $rescheduleDate="", $rescheduleStartTime="", $rescheduleEndTime=""){
        $className = Consignment::IncludeCarrierClass($consignment);
        if($className === false){
            $results['STATUS'] = 'ERROR';
            $results['MESSAGE'] = "Service provider[ ".$className." ] is not defined. Please contact system administrator";
            return $results;
        }
        
        $apiValue = new $className(); //  Calling the constructor
        
        if(method_exists($apiValue, "reschedulePickup")){
            $results = $apiValue->reschedulePickup($consignment,$rescheduleDate,$rescheduleStartTime, $rescheduleEndTime);
            return $results;
        }
        else
        {
            $results['STATUS'] = 'ERROR';
            $results['MESSAGE'] = "Reschedule is not available for this shipment.";
            return $results;
        }
    }

    public function UnHoldShipment($consignmentArray){
        $output = array();
        if (count($consignmentArray) > 0) {
            foreach ($consignmentArray as $conIndex => $conId) {
                $consignmentId = (int)$conId;
                $consignment = new Consignment($consignmentId);
                if ($consignment->getId() > 0) {
                    $oldStatus = $consignment->getConsignmentStatus();
                    $message = '';
                    $ConsignmentLog = new ConsignmentLog();
                    if (trim($consignment->getAwb()) != '') {
                        $consignment->setShipmentStatus(Consignment::STATUS_LABEL_CREATED);
                        $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);
                    } else {
                        $consignment->setShipmentStatus(Consignment::STATUS_INVALID);
                        $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_INVALID]);
                    }
                    $consignment->save();
                    $ConsignmentLog->createlog("Consignment Status Changed from " . Consignment::$database_status_array[$oldStatus] . " to " . Consignment::$database_status_array[$consignment->getShipmentStatus()] . ".", $consignment->getId());
                    $output["message"] .= $consignment->getHawb() . " " . Translation::GetCaption("MSG_SHIPMENT_ADDED_ON_RECEIVED") . "<br>";
                } else {
                    $output["message"] .= "Can not find shipment for restore.<br>";
                }
            }
            $output["status"] = "success";
        } else {
            $output["status"] = "ERROR";
            $output["message"] = "No shipment has been selected.";
        }
        return $output;
    }
    public static  function HoldShipment($consignmentArray)
    {
        $output = array();
        if (count($consignmentArray) > 0) {
            foreach ($consignmentArray as $conIndex => $conId) {
                $consignmentId = (int)$conId;
                $consignment = new Consignment($consignmentId);
                if ($consignment->getId() > 0) {
                    $oldStatus = $consignment->getConsignmentStatus();
                    $message = '';
                    $ConsignmentLog = new ConsignmentLog();
                    $consignment->setConsignmentStatus(self::$database_status_array[self::STATUS_HOLD]);
                    $consignment->setShipmentStatus(self::STATUS_HOLD);
                    $consignment->save();
                    $status = "";
                    TrackingData::putShipmentOnHold($consignment, $status);
                    $ConsignmentLog->createlog("Consignment Status Changed from " . Consignment::$database_status_array[$oldStatus] . " to " . Consignment::$database_status_array[$consignment->getShipmentStatus()] . ".", $consignment->getId());
                    $output["message"] .= $consignment->getHawb() . " " . Translation::GetCaption("MSG_SHIPMENT_ADDED_ON_HOLD") . "<br>";
                } else {
                    $output["message"] .= "Can not find shipment to hold.<br>";
                }
            }
            $output["status"] = "success";
        } else {
            $output["status"] = "ERROR";
            $output["message"] = "No shipment has been selected.";
        }
        return $output;
    }

    public static function RestoreShipment($orderReferenceArr)
    {
        $output = [];
        $consignmentArray = [];
        $notFound = [];
        $notValidstatus = [];
        $multiFound = [];
        if(count($orderReferenceArr)) {
            foreach($orderReferenceArr as $orderReference) {
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addFieldEqualFilter("hawb", "=", $orderReference);
                $consignmentObj = $consignmentFilter->getListNew("id,shipment_status");
                if (count($consignmentObj) > 0) {
                    if (count($consignmentObj) > 1) {
                        $multiFound[] = $orderReference;
                    }
                    if (count($consignmentObj) == 1 && $consignmentObj[0]->getShipmentStatus() != Consignment::STATUS_RECYCLED) {
                        $notValidstatus[] = $orderReference;
                    }
                    if (count($consignmentObj) == 1 && ($consignmentObj[0]->getShipmentStatus() == Consignment::STATUS_RECYCLED)) {
                        $consignmentArray[] = $consignmentObj[0]->getId();
                    }
                } else {
                    $notFound[] = $orderReference;
                }
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"][] = "No shipment has been selected.";
        }

        if (count($consignmentArray) > 0) {
            $output["STATUS"] = "SUCCESS";
            foreach ($consignmentArray as $conIndex => $conId) {
                $consignmentId = (int)$conId;
                $consignment = new Consignment($consignmentId);
                $hawb = self::getuniqueHawb($consignment->getHawb(),$consignmentId);
                $consignment->setHawb($hawb);
                $oldStatus = $consignment->getConsignmentStatus();
                $ConsignmentLog = new ConsignmentLog();
                if (trim($consignment->getAwb()) != '') {
                    $consignment->setShipmentStatus(Consignment::STATUS_LABEL_CREATED);
                    $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);
                    $parcels = new ParcelFilter();
                    $parcels->updateParcelStatusOnConsignmentStatusChange($conId, Consignment::STATUS_LABEL_CREATED, Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);
                } else {
                    $consignment->setShipmentStatus(Consignment::STATUS_READY_TO_PRINT);
                    $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_READY_TO_PRINT]);
                    $parcels = new ParcelFilter();
                    $parcels->updateParcelStatusOnConsignmentStatusChange($conId, Consignment::STATUS_READY_TO_PRINT, Consignment::$database_status_array[Consignment::STATUS_READY_TO_PRINT]);
                }
                $consignment->save();
                $ConsignmentLog->createlog("Consignment Status Changed from " . Consignment::$database_status_array[$oldStatus] . " to " . Consignment::$database_status_array[$consignment->getShipmentStatus()] . ".", $consignment->getId());
//                $output["MESSAGE"][] = $consignment->getHawb() . " " . Translation::GetCaption("MSG_SHIPMENT_ADDED_ON_RECEIVED");
                $output["MESSAGE"][] = 'Shipment restored successfully.';
            }
        }

        if(count($notFound) || count($notValidstatus) || count($multiFound)) {
            if (count($consignmentArray) > 0)
                $output['STATUS'] = "SUCCESS";
            else
                $output['STATUS'] = "ERROR";
        }

        foreach ($notFound as $hawbNumber) {
            $output['ERRORS'][] = "Invalid data, Please enter valid consignment order reference " . $hawbNumber;
        }

        foreach ($notValidstatus as $hawbNumber) {
            $output['ERRORS'][] = "Consignment with order reference " . $hawbNumber . " is not valid for restored";
        }

        foreach ($multiFound as $hawbNumber) {
            $output['ERRORS'][] = "Consignment with order reference " . $hawbNumber . " is already in active status";
        }
        return $output;
    }


    public static function bulkUpdateWithJoin($setColumns, $where)
    {
        //$awbArrayStr = "'" . implode("','",$awbArray) . "'";
        if (trim($setColumns) != '' && trim($where) != '')
            $sql = "update consignment c $setColumns where $where";

        //t($sql, __METHOD__);

        return DbAccess3::runQuery($sql);
        //DbAccess3::runQuery($sql);
    }

  /*
     * Get consignemnt object from array
     */
    public static function getConsignmentObjectFromArray($formPostArray) {
        if(is_array($formPostArray) && count($formPostArray) > 0){
            $consignmentSeller = [
                                    'seller_name' => isset($formPostArray['seller_name']) ? $formPostArray['seller_name'] : '',
                                    'seller_first_name' => isset($formPostArray['seller_first_name']) ? $formPostArray['seller_first_name'] : '',
                                    'seller_last_name' => isset($formPostArray['seller_last_name']) ? $formPostArray['seller_last_name'] : '',
                                    'seller_email' => isset($formPostArray['seller_email']) ? $formPostArray['seller_email'] : '',
                                    'seller_phone' => isset($formPostArray['seller_phone']) ? $formPostArray['seller_phone'] : '',
                                    'seller_address_line_1' => isset($formPostArray['seller_address_line_1']) ? $formPostArray['seller_address_line_1'] : '',
                                    'seller_address_line_2' => isset($formPostArray['seller_address_line_2']) ? $formPostArray['seller_address_line_2'] : '',
                                    'seller_address_line_3' => isset($formPostArray['seller_address_line_3']) ? $formPostArray['seller_address_line_3'] : '',
                                    'seller_city' =>  isset($formPostArray['seller_city']) ? $formPostArray['seller_city'] : '',
                                    'seller_state' => isset($formPostArray['seller_state']) ? $formPostArray['seller_state'] : '',
                                    'seller_postcode' => isset($formPostArray['seller_postcode']) ? $formPostArray['seller_postcode'] : '',
                                    'seller_country_iso' => isset($formPostArray['seller_country_iso']) ? $formPostArray['seller_country_iso'] : '',
                                    'seller_gst_tax_id' => isset($formPostArray['seller_gst_tax_id']) ? $formPostArray['seller_gst_tax_id'] : ''
                                ];
            $consignmentSeller = json_encode($consignmentSeller);
            $consignmentId  = isset($formPostArray["id"]) ? intval($formPostArray["id"]) : 0;
            if($consignmentId > 0)
                $consignment = new Consignment($consignmentId);
            else
                $consignment = new Consignment();
            ///////////////////////////////////////////
            if(isset($formPostArray['aget_id']))
                $consignment->setAgentId($formPostArray['aget_id']);
            if(isset($formPostArray['user_id']))
                $consignment->setUserId($formPostArray['user_id']);
            if(isset($formPostArray['service']))
                $consignment->setServiceId($formPostArray['service']);
            if(isset($formPostArray['service_id']))
                $consignment->setServiceId($formPostArray['service_id']);
            if(isset($formPostArray['customized_service_id']))
                $consignment->setCustomizedServiceId($formPostArray['customized_service_id']);
            if(isset($formPostArray['warehouse_user_id']))
                $consignment->setWarehouseUserId($formPostArray['warehouse_user_id']);
            if(isset($formPostArray['warehouse_id']))
                $consignment->setWarehouseId($formPostArray['warehouse_id']);
            if(isset($formPostArray['sales_pot_id']))
                $consignment->setSalesPotId($formPostArray['sales_pot_id']);
            if(isset($formPostArray['invoice_id']))
                $consignment->setInvoiceId($formPostArray['invoice_id']);
            if(isset($formPostArray['credit_id']))
                $consignment->setCreditId($formPostArray['credit_id']);
            if(isset($formPostArray['is_invoiced']))
                $consignment->setIsInvoiced($formPostArray['is_invoiced']);
            if(isset($formPostArray['invoice_type']))
                $consignment->setInvoiceType($formPostArray['invoice_type']);
            if(isset($formPostArray['shipment_status']))
                $consignment->setShipmentStatus($formPostArray['shipment_status']);
            $shipmentType = "D";
            if(!empty($formPostArray['shipment_type']))
                $shipmentType = $formPostArray['shipment_type'];

            if(!empty($formPostArray['created_from']) &&  $formPostArray['skuid'] > 0){
                $consignment->setCreatedFrom("sku");
            }
            else if(!empty($formPostArray['created_from']))
                $consignment->setCreatedFrom($formPostArray['created_from']);
            else
                $consignment->setCreatedFrom("web");
            $consignment->setShipmentType($shipmentType);
            if($shipmentType == "C") {
                if(isset($formPostArray['collection_end_time']) && !empty($formPostArray['collection_end_time']))
                    $consignment->setCollectionEndTime(date("H:i",strtotime($formPostArray['collection_end_time'])));
                if(isset($formPostArray['collection_start_time']) && !empty($formPostArray['collection_start_time']))
                    $consignment->setCollectionStartTime(date("H:i",strtotime($formPostArray['collection_start_time'])));
                if(isset($formPostArray['collection_date']) && !empty($formPostArray['collection_date'])) 
                    $consignment->setCollectionDate(date('Y-m-d', strtotime($formPostArray['collection_date'])));
                
            }
            if(isset($formPostArray['awb']))
                $consignment->setAwb($formPostArray['awb']);
            if(isset($formPostArray['tracking_number']))
                $consignment->setAwb($formPostArray['tracking_number']);
            if(isset($formPostArray['consignment_status']))
                $consignment->setConsignmentStatus($formPostArray['consignment_status']);
            if(isset($formPostArray['return_awb']))
                $consignment->setReturnAwb($formPostArray['return_awb']);
            if(isset($formPostArray['order_reference']))
                $consignment->setHawb(trim($formPostArray['order_reference']));
            if(isset($formPostArray['mawb']))
                $consignment->setMawb($formPostArray['mawb']);
            if(isset($formPostArray['service_name']))
                $consignment->setServiceName($formPostArray['service_name']);
            if(isset($formPostArray['reference']))
                $consignment->setReference($formPostArray['reference']);
            if(isset($formPostArray['date_dispatch']))
                $consignment->setDateLabelCreated($formPostArray['date_dispatch']);
            if(isset($formPostArray['is_customer_manifested']))
                $consignment->setIsCustomerManifested($formPostArray['is_customer_manifested']);
            if(isset($formPostArray['booked_file_id']))
                $consignment->setBookedFileId($formPostArray['']);
            if(isset($formPostArray['receiver_company']))
                $consignment->setCompany($formPostArray['receiver_company']);
            if(isset($formPostArray['receiver_contact']))
                $consignment->setContact($formPostArray['receiver_contact']);
            if(isset($formPostArray['receiver_address_line_1']))
                $consignment->setAddressLine1($formPostArray['receiver_address_line_1']);
            if(isset($formPostArray['receiver_address_line_2']))
                $consignment->setAddressLine2($formPostArray['receiver_address_line_2']);
            if(isset($formPostArray['receiver_address_line_3']))
                $consignment->setAddressLine3($formPostArray['receiver_address_line_3']);
            if(isset($formPostArray['receiver_city']))
                $consignment->setCity($formPostArray['receiver_city']);
            if(isset($formPostArray['receiver_state']))
                $consignment->setState($formPostArray['receiver_state']);
            if(isset($formPostArray['receiver_postcode']))
                $consignment->setPostcode(cleanCsvCall ($formPostArray['receiver_postcode']));
            if(isset($formPostArray['receiver_country']))
                $consignment->setCountryId($formPostArray['receiver_country']);
            if(isset($formPostArray['receiver_telephone']))
                $consignment->setTelephone($formPostArray['receiver_telephone']);
            if(isset($formPostArray['parcel']))
                $consignment->setNumberPieces(count($formPostArray['parcel']));
            if(isset($formPostArray['weight_type']))
                $consignment->setWeightType($formPostArray['weight_type']);
            if(isset($formPostArray['item_weight']))
                $consignment->setWeight($formPostArray['item_weight']);
            if(isset($formPostArray['item_weight']))
                $consignment->setUpdateWeight($formPostArray['item_weight']);
            if(isset($formPostArray['item_weight']))
                $consignment->setFakeWeight($formPostArray['item_weight']);
            if(isset($formPostArray['charge_weight']))
                $consignment->setChargeWeight($formPostArray['charge_weight']);
            if(isset($formPostArray['vol_weight']))
                $consignment->setVolWeight($formPostArray['vol_weight']);
            if(isset($formPostArray['vol_demonimator']))
                $consignment->setVolDemonimator($formPostArray['vol_demonimator']);
            if(isset($formPostArray['hv_lv']))
                $consignment->setHvLv($formPostArray['hv_lv']);
            if(isset($formPostArray['description']))
                $consignment->setDescription($formPostArray['description']);
            if(isset($formPostArray['notes']))
                $consignment->setNotes($formPostArray['notes']);
            if(isset($formPostArray['item_value']))
                $consignment->setValue($formPostArray['item_value']);
            if(isset($formPostArray['value']))
                $consignment->setValue($formPostArray['value']);
            if(isset($formPostArray['item_currency']) && !empty(trim($formPostArray['item_currency'])))
                $consignment->setCurrency($formPostArray['item_currency']);
            else
                $consignment->setCurrency("GBP");
            if(isset($formPostArray['sender_contact']))
                $consignment->setSenderName($formPostArray['sender_contact']);
            if(isset($formPostArray['username']))
                $consignment->setUsername($formPostArray['username']);
            if(isset($formPostArray['sender_checked']))
                $consignment->setSenderChecked($formPostArray['sender_checked']);
            else
                $consignment->setSenderChecked(0);
            if(isset($formPostArray['message']))
                $consignment->setMessage($formPostArray['message']);
            if(isset($formPostArray['sorter_image']))
                $consignment->setSorterImage($formPostArray['sorter_image']);
            if(isset($formPostArray['label_file']))
                $consignment->setLabelFile($formPostArray['label_file']);
            if(isset($formPostArray['is_doc']))
                $consignment->setIsDoc($formPostArray['is_doc']);
            if(isset($formPostArray['receiver_email']))
                $consignment->setEmail($formPostArray['receiver_email']);
            if(isset($formPostArray['item_type']))
                $consignment->setItemtype($formPostArray['item_type']);
            if(isset($formPostArray['routing_code']))
                $consignment->setRoutingCode($formPostArray['routing_code']);
            if($shipmentType == "C"){
                $consignment->setRoutingCodeEur($formPostArray['packageLocation']);
            }else if(isset($formPostArray['pickup_branchId']))
                $consignment->setRoutingCodeEur($formPostArray['pickup_branchId']);
            else if(isset($formPostArray['routing_code_eur']))
                $consignment->setRoutingCodeEur($formPostArray['routing_code_eur']);
            if(isset($formPostArray['other_routing_code']))
                $consignment->setOtherRoutingCode($formPostArray['other_routing_code']);
            if(isset($formPostArray['billing_hold']))
                $consignment->setBillingHold($formPostArray['billing_hold']);
            /*if(isset($formPostArray['send_courier_data']))
                $consignment->setSendCourierData($formPostArray['send_courier_data']);*/
            if(isset($formPostArray['remote_charges']))
                $consignment->setRemoteCharges($formPostArray['remote_charges']);
            if(isset($formPostArray['reinvoices']))
                $consignment->setReinvoices($formPostArray['reinvoices']);
            if(isset($formPostArray['optimus_sorter']))
                $consignment->setOptimusSorter($formPostArray['optimus_sorter']);
            if(isset($formPostArray['fullpallets']))
                $consignment->setFullPallet($formPostArray['fullpallets']);
            if(isset($formPostArray['halfpallets']))
                $consignment->setHalfPallet($formPostArray['halfpallets']);
            if(isset($formPostArray['qtrpallets']))
                $consignment->setQuarterPallet($formPostArray['qtrpallets']);
            if(isset($formPostArray['date_scanned']))
                $consignment->setDateScanned($formPostArray['date_scanned']);
            if(isset($formPostArray['consignment_type']))
                $consignment->setConsignmentType($formPostArray['consignment_type']);
            if(isset($formPostArray['api_uuid']))
                $consignment->setApiUuid($formPostArray['api_uuid']);
            if(isset($formPostArray['sender_company']))
                $consignment->setSenderCompany($formPostArray['sender_company']);
            if(isset($formPostArray['sender_email']))
                $consignment->setSenderEmail($formPostArray['sender_email']);
            if(isset($formPostArray['sender_telephone']))
                $consignment->setSenderTelephone($formPostArray['sender_telephone']);
            if(isset($formPostArray['sender_address_line_1']))
                $consignment->setSenderAddressLine1($formPostArray['sender_address_line_1']);
            if(isset($formPostArray['sender_address_line_2']))
                $consignment->setSenderAddressLine2($formPostArray['sender_address_line_2']);
            if(isset($formPostArray['sender_address_line_3']))
                $consignment->setSenderAddressLine3($formPostArray['sender_address_line_3']);
            if(isset($formPostArray['sender_city']))
                $consignment->setSenderCity($formPostArray['sender_city']);
            if(isset($formPostArray['sender_postcode']))
                $consignment->setSenderPostcode($formPostArray['sender_postcode']);
            if(isset($formPostArray['sender_country']))
                $consignment->setSenderCountryId($formPostArray['sender_country']);
            if(isset($formPostArray['sender_state']))
                $consignment->setSenderState($formPostArray['sender_state']);
            if(isset($formPostArray['eori_number']))
                $consignment->setEoriNumber($formPostArray['eori_number']);
            if(isset($formPostArray['vat_number']))
                $consignment->setVatNumber($formPostArray['vat_number']);
            if(isset($formPostArray['ioss_number']))
                $consignment->setIossNumber($formPostArray['ioss_number']);
            if(isset($formPostArray['insurance_agree']))
                $consignment->setIsInsured($formPostArray['insurance_agree']);
            else
                $consignment->setIsInsured(0);

            $consignment->setConsignmentSeller($consignmentSeller);
            
            $destinationHub = self::getDestinationHub($formPostArray["receiver_postcode"], $formPostArray["receiver_state"], $formPostArray["receiver_country"]);
            $consignment->setDestinationWarehouseId($destinationHub);
            $consignmentVal = 0;
            if(count($formPostArray['parcel']) > 0){
                foreach ($formPostArray['parcel'] as $parcelArr){
                    $consignmentVal += $parcelArr['itemvalue'];
                }
            }
            if($consignmentVal > 0)
                $consignment->setValue($consignmentVal);
            
            /*
             * Item Detail Array
             */
            $session_id = session_id();
            if(count($formPostArray['parcel']) > 0){
                $itemDetailArray = array();
                $parcelCount = 0;
                foreach($formPostArray['parcel'] as $parcel){
                    if(count($parcel["items"]) > 0){
                        foreach($parcel["items"] as $i)
                            $itemArr = array();
                            $itemArr["item_description"] = $i["item_description"];
                            $itemArr["item_url"] = $i["item_url"];
                            $itemArr["item_sku_id"] = $i["item_sku"];
                            $itemArr["item_sku"] = $i["item_sku"];
                            $itemArr["no_of_items"] =$i["no_of_items"] ;
                            $itemArr["item_value"] = $i["item_value"];
                            $itemArr["weight"] = $i["weight"];
                            $itemArr["tariff_no"] = $i["tariff_no"];
                            $itemArr["hscode"] = $i["hscode"];
                            $itemArr["manufacture_country_iso"] =$i["manufacture_country_iso"] ;
                            $itemDetailArray[] = $itemArr;
                            
                    }
                    else{
                        $itemDetailFilter = new ItemDetailFilter();
                        $itemDetailFilter->addFilter(" session_id = '".$session_id."' and user_id = '".$consignment->getUserId()."' and parcel_count = '".$parcelCount."'");
                        $itemList = $itemDetailFilter->getList();
                        if(count($itemList) == 0 && $consignmentId > 0){
                            $itemDetailFilter = new ItemDetailFilter();
                            $itemDetailFilter->addFilter(" consignment_id = '".$consignmentId."' and parcel_count = '".$parcelCount."'");
                            $itemList = $itemDetailFilter->getList();
                        }
                        if(count($itemList) > 0){
                            foreach($itemList as $item){
                                $itemdetail = json_decode($item->getItemDetail());
                                foreach($itemdetail as $i){
                                    $itemArr = array();
                                    $itemArr["item_description"] = $i->item_description;
                                    $itemArr["item_url"] = $i->item_url;
                                    $itemArr["item_sku_id"] = $i->item_sku;
                                    if(strtoupper($consignment->getCreatedFrom()) == "SKU"){
                                        $sku = new sku($i->item_sku);
                                        $itemArr["item_sku"] = $sku->getSku();
                                    }
                                    else{
                                        $itemArr["item_sku"] = $i->item_sku;
                                    }
                                    $itemArr["no_of_items"] =$i->no_of_items ;
                                    $itemArr["item_value"] = $i->item_value;
                                    $itemArr["weight"] = $i->weight;
                                    $itemArr["tariff_no"] = "";
                                    $itemArr["hscode"] = $i->hscode;
                                    $itemArr["manufacture_country_iso"] =$i->manufacture_country_iso ;
                                    $itemDetailArray[] = $itemArr;

                                }
                            }
                        }
                    }
                }
            }
			$consignment->setConsignmentParcels($formPostArray['parcel']);
            $consignment->setItems($itemDetailArray);

            return $consignment;
        }else{
            return false;
        }
    }
    /*
    * Save consignment funtion
    */
    public static  function saveShipment($formPostArray, $userId, $apiCall = false, $userParams="", $createdFrom = '', $shipmentUserId=""){
        
        $outputArray = [];
        $outputArray['STATUS'] = "SUCCESS";
        $error_array = [];
        $information_array = [];
        $oldConData = "";
        $newConData = "";
        $sessionUser = SessionManager::getUser();
        $userObj = new User($userId);
        $userAccountId = $userObj->getUserAccountId();
        $userAccountObj = new CustomerAccount($userAccountId);
        $consignmentId  =   intval($formPostArray["id"]);
        if(isset($formPostArray['awb']) && !isset($formPostArray['tracking_number'])){
            $formPostArray['tracking_number'] = $formPostArray['awb'];
        }

        if(array_key_exists('order_reference',$formPostArray) && empty(trim($formPostArray['order_reference']))){
            $timeStampHawb = time(); 
            $objSenderCountry = new Country(trim($formPostArray['sender_country'])>0?$formPostArray['sender_country']:$userObj->getCountryId());
            $objReceiverCountry = new Country($formPostArray['receiver_country']);
            $countryIsoCodeSender   = substr($objSenderCountry->getIso(),0,2);
            $countryIsoCodeReceiver = substr($objReceiverCountry->getIso(),0,2);
           // $formPostArray['order_reference'] = strtoupper("ST". substr($userAccountObj->getUserAccount(), 0,3).generateRandomString(3).time());
            $formPostArray['order_reference'] = strtoupper($userAccountObj->getTrackingOrderPrefix().$countryIsoCodeSender.$timeStampHawb.$countryIsoCodeReceiver.LicencePlate::commoditycode($timeStampHawb).LicencePlate::mod11($timeStampHawb));
            /*if(isset($formPostArray['tracking_number']) && trim($formPostArray['tracking_number']) ==""){
                $formPostArray['order_reference'] = "";
            }*/
        }
        // in edit case
        $oldConsignment = NULL;
        if(!empty($consignmentId) && $consignmentId > 0){
            $oldConsignment = new Consignment($consignmentId);
        }
        $formPostArray['user_id'] = $userId;
        $formPostArray['warehouse_id'] = $userObj->getWarehouseId();
        $formPostArray['created_from'] = $createdFrom;
        $consignment = self::getConsignmentObjectFromArray($formPostArray);

        //check if shipment is high or low value
        $country = new Country($consignment->getCountryId());
        $shipmentLowValue = $country->getBagLowValue();
        $countryCurrency = new Currency($country->getCurrencyId());
        $valueLvHv = $consignment->getValue();
        if(strtoupper($consignment->getCurrency()) != $countryCurrency->getRightSymbol()){
            $valueLvHv = Currency::convertCurrency($consignment->getCurrency(), $countryCurrency->getRightSymbol(), $consignment->getValue());
        }
        if($valueLvHv <= $shipmentLowValue){
            $consignment->setHvLv('L');
        }
        else
        {
            $consignment->setHvLv('H');
        }

        // Check if serice is drop off
        $services = new Services($consignment->getServiceId());
        $dropOffService = false;
        $dropOffServiceId = "";
        $dropOffData = [];
        /* Check if submitted type and submitted service type are same
        * Then give only single label
        */
        $consignment->setIsCustomerBillable(1);
        if($consignment->getServiceId() > 0) {
            $conType = array("D" => "Dispatch", "C" => "Collection", "DO" => "Drop-Off");
            if ($consignment->getShipmentType() == $services->getServiceType()) {

            } else if ($services->getDropOffServiceId() > 0) { // && $consignment->getShipmentType() == "DO"
                $dropOffService = true;
                $consignment->setIsCustomerBillable(0);
                $dropOffServiceId = $services->getDropOffServiceId();
                /*
                 * Check which type of service is
                 * that type would be consignment shipment type
                 * Check
                 */
                $serviceNewObj = new Services($dropOffServiceId);
                $backServiceType = $serviceNewObj->getServiceType();
                if ($consignment->getShipmentType() == $backServiceType) {
                    $consignment->setShipmentType($backServiceType);
                } else {
                    $outputArray['ERROR'] = ['submitted service does not support ' . $consignment->getShipmentType() . ' type '];
                    $outputArray['STATUS'] = "ERROR";
                    $outputArray['MESSAGE'] = 'submitted service does not support ' . $consignment->getShipmentType() . ' type';
                    return $outputArray;
                }
            } else {
                $outputArray['ERROR'] = ['submitted service does not support ' . $conType[$consignment->getShipmentType()] . ' type '];
                $outputArray['STATUS'] = "ERROR";
                $outputArray['MESSAGE'] = 'submitted service does not support ' . $conType[$consignment->getShipmentType()] . ' type';
                return $outputArray;
            }
        }
        if ($consignment->getDateCreated() == '' || $consignment->getDateCreated() == '0000-00-00 00:00:00')
            $consignment->setDateCreated(date("Y-m-d H:i:s"));

        $consignmentValidator = new ConsignmentValidator($consignment, $formPostArray['parcel'],$userObj,$userAccountObj);
        $IsValidConsignment = $consignmentValidator->validateConsignment();
        $consignment = $consignmentValidator->getConsignment();
        $isBasicValid = $consignmentValidator->isBasicValidationFailed();
        if($isBasicValid){
            $basicValidationError = $consignmentValidator->getErrorList();
            $outputArray['ERROR'] = $basicValidationError;
            $outputArray['STATUS'] = "ERROR";
            $outputArray['MESSAGE'] = implode( '<br>', $basicValidationError);
            return $outputArray;
        }
        // Valid consinment check
      /*  if($formPostArray["save_invalid"]) {
            $consignment->save();
            $newConData = serialize($consignment);
            $consignmentLog = new ConsignmentLog();
            $consignmentLog->createlog($sessionUser->getUserName() . ' has added new consignemnt ', $userObj->getId(), 'USER',  $userObj->getId(), $oldConData, $newConData);
            if ($consignment->getId() > 0) {
                self::saveConsigmentParcel($consignment, $formPostArray['parcel']);
            }
        }*/
        // Delete Old Parcel and add new parcel in edit case
        if(isset($_POST['id']) && $_POST['id'] > 0){
            self::saveConsigmentParcel($consignment, $formPostArray['parcel'], $formPostArray['skuid']);
            
        }
        if($IsValidConsignment){
//            $tempCustomizedServiceId = "";
//            $tempServiceId = "";
//            if($dropOffService){
//                // Drop off service
//                $tempCustomizedServiceId = $consignment->getCustomizedServiceId();
//                $tempServiceId = $consignment->getServiceId();
//                $consignment->setCustomizedServiceId("");
//                $consignment->setServiceId($dropOffServiceId);
//            }
            if($shipmentUserId > 0){
                
                $consignment->setUserId($shipmentUserId);
            }
            $consStatus = Consignment::STATUS_READY_TO_PRINT;
            if(!empty($consignment->getAwb())){
                $consStatus = Consignment::STATUS_LABEL_CREATED;
            }
            $consignment->setShipmentStatus($consStatus);
            $consignment->setConsignmentStatus(Consignment::$database_status_array[$consStatus]);
            if($oldConsignment != NULL){
                $message = self::buildstring($oldConsignment, $formPostArray);
                $logtype = self::logtype($formPostArray);
                //saves the log and message.
                $consignment->savelog($message, $logtype);
            }
            if($consignment->getId() <= 0) {
//                $conOldObj = new Consignment($consignment->getId());
//                $oldConData = serialize($conOldObj);
                $consignment->save();
//                $newConData = serialize($consignment);
//                $consignmentLog = new ConsignmentLog();
//                $consignmentLog->createlog($sessionUser->getUserName() . ' has updated consignemnt ', $userObj->getId(), 'USER',  $userObj->getId(), $oldConData, $newConData);
                self::saveConsigmentParcel($consignment, $formPostArray['parcel'], $formPostArray['skuid']);
                
            }
            $newConObj = null;
            if($consignment->getId() > 0){
                if(trim($formPostArray["temp_invoice_name"]) != ''){
                $currentFileLocation = "../_assets/paperless_invoice/temp/" . $formPostArray["temp_invoice_name"];
                    if(file_exists($currentFileLocation)){
                        
                        $fileName = $formPostArray["temp_invoice_name"];
                        $temp = explode(".", $fileName);
                        $extension = end($temp);
                        $uploadName = md5($consignment->getId()) . "." . $extension;
                        $newFileLocation = "../_assets/paperless_invoice/" . $uploadName;
                        @rename($currentFileLocation, $newFileLocation);
                    }
                }
                ////////////////// Commercial Invoices from API
                //
                    if(isset($formPostArray["commercial_invoice"]) && trim($formPostArray["commercial_invoice"])!=''){
                        $uploadName = md5($consignment->getId()) . ".pdf";
                        $newFileLocation = "../_assets/paperless_invoice/" . $uploadName;
                        file_put_contents($newFileLocation, base64_decode($formPostArray["commercial_invoice"]));
                    }
                //self::saveConsigmentParcel($consignment,$formPostArray['parcel']);
                if(($userObj->getInstantLabel() == '1' && $consignment->getShipmentStatus() == Consignment::STATUS_READY_TO_PRINT) || $dropOffService == true) {
                    $labelType = (!empty($formPostArray["label_type"]) ? $formPostArray["label_type"] : 'pdf');
                    $labelSize = (!empty($formPostArray["label_size"]) ? $formPostArray["label_size"] : '100x150');
                    $dropOffLabel = $dropOffService;
                    //
                    $tempCustomizedServiceId = "";
                    $tempServiceId = "";
                    $tempAgentId = "";
                    $errorTemp = [];
                    if($dropOffService){
                        $tempCustomizedServiceId = $consignment->getCustomizedServiceId();
                        $tempServiceId = $consignment->getServiceId();
                        $tempAgentId = $consignment->getAgentId();
                        $weightToSend = $consignment->getWeight();
//                        $consignment->setAgentId("");
                        $consignment->setServiceId($dropOffServiceId);
                        $consignment->setCustomizedServiceId("");
                        $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
                        $carrierServiceDefaultRulesFilter->addFilter("serviceid = ".$dropOffServiceId);
                        $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'outbound'");
                        $carrierServiceDefaultRules = $carrierServiceDefaultRulesFilter->getColumnList("agentid,from_weight,to_weight");
                        if(count($carrierServiceDefaultRules) > 0){
                            $weightFound = false;
                            foreach($carrierServiceDefaultRules as $carrierServiceDefaultRule){
                                if($carrierServiceDefaultRule->getFromWeight() < $weightToSend && $carrierServiceDefaultRule->getToWeight() >= $weightToSend ){
                                    $consignment->setAgentId($carrierServiceDefaultRule->getAgentid());
                                    $weightFound = true;
                                    break;
                                }
                            }
                            if($weightFound === false){
                                $errorTemp[] = "We are unable to generate drop-off label Weight ( ".$weightToSend." Kg ) is not allowed)";
                                $consignment->setMessage(implode("<br>", $errorTemp));
                                $outputArray['ERROR'] = $errorTemp;
                                $outputArray['STATUS'] = "ERROR";
                                $outputArray['MESSAGE'] = "We are unable to generate drop-off label Weight ( ".$weightToSend." Kg ) is not allowed)";
                                return $outputArray;
                            }
                        }else{
                            $errorTemp[] = "We are unable to generate drop-off label Weight ( ".$weightToSend." Kg ) is not allowed)";
                            $consignment->setMessage(implode("<br>", $errorTemp));
                            $outputArray['ERROR'] = $errorTemp;
                            $outputArray['STATUS'] = "ERROR";
                            $outputArray['MESSAGE'] = "We are unable to generate drop-off label Weight ( ".$weightToSend." Kg ) is not allowed)";
                            return $outputArray;
                        }
                        $consignment->save();
                    }
                    $dataEntry = false;
                    if(!empty($formPostArray['label_file'])){
                        $dataEntry = true;
                    }
                    $labelReturn = Consignment::getInstantLabel($consignment,$labelType,$labelSize,true,$dropOffService,true,$dataEntry);
                    if(isset($labelReturn['STATUS']) && $labelReturn['STATUS'] == "ERROR"){
                        return $labelReturn;
                    }
                    if ($consignment->getShipmentStatus() == Consignment::STATUS_INVALID) {
                        $outputArray['URL'] = "../main/consignment_add.php?id=" . $consignment->getId();
                    } else {
                        $outputArray['URL'] = "../main/client_list.php?show=printed" . (trim($userParams) != '' ? "&" . $userParams : '');
                        $outputArray['INSTANT_LABEL'] = $labelReturn['LABEL'];
                        //$consignment->getLabelFile();
                        $outputArray['TRACKING_NUMBER'] = $labelReturn['TRACKING_NUMBER'];
                        if(!empty($outputArray['TRACKING_NUMBER']) && $dropOffLabel){
                            // Make new consignment
                            $oldConsignment = new Consignment($consignment->getId());
                            $newConId = self::AddReturnShipment($consignment->getId(),true);
                            $newConObj = new Consignment($newConId);
                            $newConObj->setCustomizedServiceId($tempCustomizedServiceId);
                            $newConObj->setServiceId($tempServiceId);
                            $newConObj->setShipmentType("D");
                            $newConObj->setAwb("");
                            $newConObj->setLabelFile("");
                            $newConObj->setDestinationWarehouseId($oldConsignment->getDestinationWarehouseId());
                            $newConObj->setAgentId($tempAgentId);
                            $newConObj->setHawb(trim($oldConsignment->getAwb()));
                            $newConObj->setShipmentStatus(Consignment::STATUS_READY_TO_PRINT);
                            $newConObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_READY_TO_PRINT]);
                            $newConObj->save();
                            // Get all tracking number with parcel id before empty it
                            $parcelFilterObj = new ParcelFilter();
                            $parcelFilterObj->addFieldFilter("     consignment_id",$newConId);
                            $parcelNewData = $parcelFilterObj->getColumnList("id,tracking_number");
                            $newParcelTrackingArr = [];
                            if(count($parcelNewData) > 0){
                                foreach ($parcelNewData as $parcelData){
                                    $newParcelTrackingArr[$parcelData->getTrackingNumber()] = $parcelData->getId();
                                }
                            }
                            Parcel::emptyParcelTrackingByConId($newConId, Consignment::STATUS_READY_TO_PRINT);
                            $labelReturnNew = Consignment::getInstantLabel($newConObj,$labelType,$labelSize,true);
                            if(isset($labelReturnNew['STATUS']) && $labelReturnNew['STATUS'] == "ERROR"){
                                // Remove old consignment
                                $oldConsignment->setShipmentStatus(Consignment::STATUS_RECYCLED);
                                $oldConsignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_RECYCLED]);
                                $oldConsignment->save();
                                return $labelReturnNew;
                            }
                            // Map old and new consignment parcels tracking
                            $mapParcelOldTraciong = [];
                            $mapParcelOldTraciongJsonStr = "{";
                            foreach ($newParcelTrackingArr as $parcelTrackingNumber => $parcelId) {
                                $parcelObj = new Parcel($parcelId);
                                $mapParcelOldTraciong[$parcelTrackingNumber] = $parcelObj->getTrackingNumber();
                                // Make json for mapping
                                $mapParcelOldTraciongJsonStr .= '"'.$parcelTrackingNumber.'":"'.$parcelObj->getTrackingNumber().'",';
                            }
                            $mapParcelOldTraciongJsonStr = rtrim($mapParcelOldTraciongJsonStr,",");
                            $mapParcelOldTraciongJsonStr .= "}";
                            $dropOffData['drop_off_con_id'] = $consignment->getId();
                            $dropOffData['drop_off_con_tracking'] = $consignment->getAwb();
                            $dropOffData['dispatch_con_id'] = $newConObj->getId();
                            $dropOffData['dispatch_off_con_tracking'] = $newConObj->getAwb();
                            $dropOffData['parcel_tracking_mapping'] = $mapParcelOldTraciongJsonStr;
                            $consignmentDropoffMapping = new ConsignmentDropoffMapping();
                            $consignmentDropoffMapping->setDropoffConsignmentId($dropOffData['drop_off_con_id']);
                            $consignmentDropoffMapping->setDispatchConsignmentId($dropOffData['dispatch_con_id']);
                            $consignmentDropoffMapping->setDropoffConsignmentTracking($dropOffData['drop_off_con_tracking']);
                            $consignmentDropoffMapping->setDispatchConsignmentTracking($dropOffData['dispatch_off_con_tracking']);
                            $consignmentDropoffMapping->setParcelTracking($dropOffData['parcel_tracking_mapping']);
                            $consignmentDropoffMapping->setAddedBy($userId);
                            $consignmentDropoffMapping->setDateCreated(time());
                            $consignmentDropoffMapping->save();
                        }
                    }
                } else {
                    $outputArray['URL'] = "../client_list.php?show=show_valid" . (trim($userParams) != '' ? "&" . $userParams : '');
                }
                if ($consignment->getRemoteCharges() == '1')
                    $information_array[] = formatMessages(MESSAGE_REMOTE_AREA_CHARGE);
            }
        }else{
            $consignment->setShipmentStatus(Consignment::STATUS_INVALID);
            $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_INVALID]);
        }
        if (isset($formPostArray["dataEntry"]) && $formPostArray["dataEntry"] == "Yes") {
            $consignment->setDateBooked(time());
            $consignment->setWarehouseId(Sessionmanager::getUser()->getWarehouseId());
            if ($formPostArray["awb"] == '')
                $consignment->setAwb($consignment->getHawb());
            else
                $consignment->setAwb($formPostArray["awb"]);
            $consignment->setShipmentStatus(Consignment::STATUS_LABEL_CREATED);
            $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);
            $outputArray['URL'] = "../client_list.php?show=printed" . (trim($userParams) != '' ? "&" . $userParams : '');
        }
        // Save consignemnt to changed the status
      /*  if($IsValidConsignment || $formPostArray["save_invalid"]) {
            $conOldObj = new Consignment($consignment->getId());
            $oldConData = serialize($conOldObj);
            $consignment->save();
            $newConData = serialize($consignment);
            $consignmentLog = new ConsignmentLog();
            $consignmentLog->createlog($sessionUser->getUserName() . ' has updated consignemnt ', $userObj->getId(), 'USER',  $userObj->getId(), $oldConData, $newConData);
        }*/
        if ($consignment->getId() > 0 && isset($formPostArray["saveaddress"]) && trim($formPostArray["saveaddress"]) == 'Y') {
            $address2 = new Address();
            $address2->setAddressLine1($consignment->getAddressLine1());
            $address2->setAddressLine2($consignment->getAddressLine2());
            $address2->setAddressLine3($consignment->getAddressLine3());
            $address2->setCity($consignment->getCity());
            $address2->setCompany($consignment->getCompany());
            $address2->setCountry($consignment->getCountryId());
            $address2->setContact($formPostArray["receiver_contact"]);
            $address2->setPhoneNumber($consignment->getTelephone());
            $address2->setPostCode($consignment->getPostcode());
            $address2->setState($consignment->getState());
            $address2->setEmail($consignment->getEmail());
            $address2->setUserId($userObj->getId());
            $filter = Sessionmanager::getAddressFilter();
            if (trim($address2->getAddressLine1()) != "")
                $filter->addFieldFilter("address_line_1", $address2->getAddressLine1());
            if (trim($address2->getCity()) != "")
                $filter->addFieldFilter("city", $address2->getCity());
            if (trim($address2->getPostCode()) != "")
                $filter->addFieldFilter("postcode", $address2->getPostcode());
            if (trim($address2->getCountry()) != "")
                $filter->addFieldFilter("country", $address2->getCountry());
            if (trim($address2->getEmail()) != "")
                $filter->addFieldFilter("email", $address2->getEmail());
            if (count($filter->getList()) == 0) {
                $address2->save();
            }
        }
        // Add MAWB into mapping table
        if($consignment->getId() > 0) {
            $parcelFilterObj = null;
            if((isset($formPostArray['mawb']) && !empty($formPostArray['mawb'])) || (isset($formPostArray['bag_number']) && !empty($formPostArray['bag_number']))){
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addFieldFilter("     consignment_id", $consignment->getId());
                $parcelFilterObj = $parcelFilter->getList();
            }
            //Check if mawb exsist
            if(isset($formPostArray['mawb']) && !empty($formPostArray['mawb'])){
                $mawbFilter = new MawbFilter();
                $mawbFilter->addFieldFilter("       mawb_number", $formPostArray['mawb']);
                $mawbFilterObj = $mawbFilter->getList();
                if(count($mawbFilterObj) > 0) {
                    $mawbId = $mawbFilterObj[0]->getId();
                    if(count($parcelFilterObj) > 0) {
                        foreach ($parcelFilterObj as $parcelObj) {                                
                            $parcelId = $parcelObj->getId();
                            $date_added = time();
                            $added_by = $userObj->getId();
                            $date_update = time();
                            $update_by = $userObj->getId();
                            $mawbParcelMapping = new MawbParcelMapping();
                            $mawbParcelMapping->setMawbId($mawbId);
                            $mawbParcelMapping->setParcelId($parcelId);
                            $mawbParcelMapping->setWharehouseId(0);
                            $mawbParcelMapping->setBagId(0);
                            $mawbParcelMapping->setDateAdded($date_added);
                            $mawbParcelMapping->setAddedBy($added_by);
                            $mawbParcelMapping->setDateUpdated($date_update);
                            $mawbParcelMapping->setUpdatedBy($update_by);
                            $mawbParcelMapping->save();
                        }
                    }
                }                    
            }
            //Check if bag number exsist
            if(isset($formPostArray['bag_number']) && !empty($formPostArray['bag_number'])){
                $baggingFilter = new BaggingFilter();
                $baggingFilter->addFieldFilter("        bagnumber", $formPostArray['bag_number']);
                $baggingFilterObj = $baggingFilter->getList();
                if(count($baggingFilterObj) > 0) {
                    $bagId = $baggingFilterObj[0]->getId();
                    if(count($parcelFilterObj) > 0) {
                        foreach ($parcelFilterObj as $parcelObj) {
                            $parcelId = $parcelObj->getId();
                            $parcelBaggingMapping = new ParcelBaggingMapping();
                            $parcelBaggingMapping->setParcelId($parcelId);
                            $parcelBaggingMapping->setBagId($bagId);
                            $parcelBaggingMapping->save();
                        }
                    }
                }
            }        
        }
        if(!$IsValidConsignment){
            $error_array = $consignmentValidator->getErrorList();
        }
        if (sizeof($error_array) > 0) {
            if ($consignment->getId() > 0) {
                $consignment->setMessage( implode( '<br>', $error_array ) );
               /* if($formPostArray["save_invalid"]) {
                    $conOldObj = new Consignment($consignment->getId());
                    $oldConData = serialize($conOldObj);
                    $consignment->save();
                    $newConData = serialize($consignment);
                    $consignmentLog = new ConsignmentLog();
                    $consignmentLog->createlog($sessionUser->getUserName() . ' has updated consignemnt ', $userObj->getId(), 'USER',  $userObj->getId(), $oldConData, $newConData);
                }*/
                $outputArray['ERROR'] = $error_array;
                $outputArray['URL'] = "../main/consignment_add.php?id=" . $consignment->getId();
            }else{
                $outputArray['ERROR'] = $error_array;
            }
        }
        
        if (count($outputArray['ERROR']) > 0) {
            $outputArray['STATUS'] = "ERROR";
            foreach ($outputArray['ERROR'] as $loopError)
                $outputArray['MESSAGE'] .= $loopError . '<br>';
        }
        $outputArray["CONSIGNMENT_ID"] = $consignment->getId();
        $outputArray["ORDER_REFERENCE"] = $consignment->getHawb();
        $outputArray['ID'] = $consignment->getId();
        $outputArray['HAWB'] = $consignment->getHawb();
        /*if($dropOffLabel){
//            $outputArray["CONSIGNMENT_ID"] = $newConObj->getId();
//            $outputArray["ORDER_REFERENCE"] = $newConObj->getHawb();
        }*/
        $outputArray['MESSAGE'] .= implode("<br />",$information_array);
        
        if(trim($outputArray['STATUS']) == 'SUCCESS' && $formPostArray["save_invalid"]){
            $outputArray['status']      =   'SUCCESS';
            $labelArray                 =    array();
            $labelArray    =   Consignment::getInstantLabel($consignment,'pdf','100x150', false,false,true);
            if($labelArray['STATUS'] == "ERROR"){
                $outputArray = $labelArray;
            } else {
                if($consignment->getIsWhiteLabel()>0)
                        $singleLabel = str_replace (".pdf","_overlabel.pdf",$consignment->getlabelFile());
                    else
                        $singleLabel = $consignment->getlabelFile();

                $outputArray['STATUS']   =   'SUCCESS';
                $outputArray['URL'] = "../client_list.php?show=printed" . (trim($userParams) != '' ? "&" . $userParams : '');
                $outputArray['INSTANT_LABEL'] = SETTING_URL_LABEL.$singleLabel;
                $outputArray['AWB'] = $consignment->getAwb();
                $outputArray['ID'] = $consignment->getId();
                $outputArray['HAWB'] = $consignment->getHawb();
                if( $consignment->getRemoteCharges() == '1')
                    $outputArray['MESSAGE'] = $consignment->getHawb()." is a remote area. It will be charged as remote area.";
                
            }
        }
        if(($outputArray["CONSIGNMENT_ID"]  < 1 || $outputArray['ID'] < 1) && empty($outputArray['MESSAGE']) && empty($outputArray['ERROR'])){
            $outputArray['STATUS'] = 'ERROR';
            $outputArray['MESSAGE'] = "something went wrong please try again";
            $outputArray['ERROR'][] = "something went wrong please try again";
        }
        return $outputArray;
    }
    /*
    * End Save consignment funtion
    */
    /*
    * Save consignment parcel
    * 
    */
    public static function saveConsigmentParcel(Consignment $consignment,$parcelArr, $skuid) {
        
        $consignmentId = $consignment->getId();
        if($consignmentId > 0 && is_array($parcelArr) && count($parcelArr) > 0){
            // Delete old parcels from parcel table
            Parcel::deleteByConsignmentId($consignmentId);
            // Check if only one parcel then add the value of consignment into parcel
            $totalParcel = count($parcelArr);
            $itemCounter = 0;
            $itemArray = array();
            // Save new parcel
            foreach ($parcelArr as $parcelcount => $parcelData) {
                
                $parcel = new Parcel();
                $parcel->setConsignmentId($consignmentId);
                $parcel->setWeight((trim($parcelData['weight'])==''?0:$parcelData['weight'])); // weight
                $parcel->setWidth((trim($parcelData['width'])==''?0:$parcelData['width'])); // width
                $parcel->setHeight((trim($parcelData['height'])==''?0:$parcelData['height'])); // Height
                $parcel->setLength((trim($parcelData['length'])==''?0:$parcelData['length'])); // Length
                $parcel->setNumberItem((trim($parcelData['itemquantity'])==''?1:$parcelData['itemquantity'])); // itemquantity
                $parcel->setDescription(""); // description
                $parcel->setCommoditycode(""); // Manufecture country
                $parcel->setQty(1); // quantity
                if(!empty(trim($parcelData['tracking_number'])))
                    $parcel->setTrackingNumber(trim($parcelData['tracking_number'])); // tracking_number

                $parcel->setItemvalue($parcelData['itemvalue']); // value
                $parcel->setTarrifNo(""); // tarrif number
                $parcel->setPweight(0); // weight number
                if($totalParcel == 1){
                    $parcel->setItemvalue((float)$consignment->getValue());
                }
                $parcel->setRoutingCode($consignment->getRoutingCode()); // routing_code
                $parcel->setParcelStatusCode($consignment->getShipmentStatus()); // 4 digit code
                $parcel->setOweStatusCode($consignment->getConsignmentStatus()); // OWE str code
                // set performa invoices data
                $session_id = session_id();
                $itemDetailFilter = new ItemDetailFilter();
                $itemDetailFilter->addFilter(" session_id = '".$session_id."' and user_id = '".$consignment->getUserId()."' and parcel_count = '".$parcelcount."'");
                $itemList = $itemDetailFilter->getList();
                if(count($itemList) == 0){
                    $itemDetailFilter = new ItemDetailFilter();
                    $itemDetailFilter->addFilter(" consignment_id = '".$consignmentId."' and  parcel_count='".$itemCounter."'");
                    $itemList = $itemDetailFilter->getList();
                }
                if(count($itemList) > 0){
                  
                    foreach($itemList as $item){
                        $itemDetailArray = array();
                        $itemdetail = json_decode($item->getItemDetail());
                        $item->setSessionId('');
                        $item->setConsignmentId($consignmentId);
                        $item->setParcelCount($itemCounter);
                        $item->save();
                         
                        foreach($itemdetail as $i){
                            $itemArr = array();
                            $itemArr["item_description"] = $i->item_description;
                            $itemArr["item_url"] = $i->item_url;
                            $itemArr["item_sku_id"] = $i->item_sku;
                            if(strtoupper($consignment->getCreatedFrom()) == "SKU"){
                                $sku = new sku($i->item_sku);
                                $itemArr["item_sku"] = $sku->getSku();
                            }
                            else{
                                $itemArr["item_sku"] = $i->item_sku;
                            }
                            $itemArr["no_of_items"] =$i->no_of_items ;
                            $itemArr["item_value"] = $i->item_value;
                            $itemArr["weight"] = $i->weight;
                            $itemArr["tariff_no"] = "";
                            $itemArr["hscode"] = $i->hscode;
                            $itemArr["manufacture_country_iso"] =$i->manufacture_country_iso ;
                            $itemDetailArray[] = $itemArr;
                        }
                    }
                    $itemArray[]= $itemDetailArray;
                    
                    $parcelData['items'] = $itemDetailArray;
                }
                else{
                    $itemDetail = new ItemDetail();
                    $itemDetail->setConsignmentId($consignmentId);
                    $itemDetail->setParcelCount($itemCounter);
                    $itemDetail->setItemDetail(json_encode($parcelData['items']));
                    $itemDetail->setUserId($consignment->getUserId());
                    $itemDetail->save();
                }
                $itemContentsDetail =   @$parcelData['items'];
                if(count($itemContentsDetail)>0)
                {
                    /*
description
qty
commoditycode
hscode
pweight
itemvalue
tarrif_no
itemsku
itemurl                     */
                    $itemContentPS  =   array();
                    foreach($itemContentsDetail as $contentsDetails)
                    {
                        $itemContentPS['desc'][]          = @$contentsDetails['item_description'];
                        $itemContentPS['qnty'][]          = @$contentsDetails['no_of_items'];
                        $itemContentPS['value'][]         = @$contentsDetails['item_value'];
                        $itemContentPS['itemsku'][]       = @$contentsDetails['item_sku'];
                        $itemContentPS['itemurl'][]       = @$contentsDetails['item_url'];
                        $itemContentPS['weight'][]        = @$contentsDetails['weight'];
                        $itemContentPS['tariff'][]        = @$contentsDetails['tariff_no'];
                        $itemContentPS['hscode'][]        = @$contentsDetails['hscode'];                                                
                        $itemContentPS['CodeCountry'][]   = @$contentsDetails['manufacture_country_iso'];
                    }
                    $parcel->setDescription(json_encode($itemContentPS['desc']));
                    $parcel->setQty(json_encode($itemContentPS['qnty']));
                    $parcel->setItemValue(json_encode($itemContentPS['value']));
                    $parcel->setItemSku(json_encode($itemContentPS['itemsku']));
                    $parcel->setItemUrl(json_encode($itemContentPS['itemurl']));
                    $parcel->setPweight(json_encode($itemContentPS['weight']));
                    $parcel->setTarrifNo(json_encode($itemContentPS['tariff']));
                    $parcel->setHscode(json_encode($itemContentPS['hscode']));
                    $parcel->setCommoditycode(json_encode($itemContentPS['CodeCountry'])); 
                   // $parcel->setNumberItem(count($itemContentPS['desc']));  
                }
                $parcel->save();
                $itemCounter++;
                
            }
            
            /*
                * Add Order details in SKU_Order Details Table for WMS orders
                */
            if($skuid > 0)
            {
                    self::saveSKUOrderDetails($consignment, $skuid, $parcelArr, $itemArray);
            }
        }
    }
    
    
    /*
     * Save SKU Box Details
     */
    public  static function saveSKUOrderDetails(Consignment $consignment, $skuid, $parcelArr, $itemArr){
      
      if($skuid > 0)
      {
          $skuorder = new SkuOrder($skuid);		  
          $skuorder->setConsignmentId($consignment->getId());
          $skuorder->save();
        $shipmentReference = $skuorder->getShipmentReference();
      }
	  
	  $count = 1;
	  
      $skuBoxDetailFilter = new SkuBoxDetailFilter();
      $skuBoxDetailFilter->addFilter(" sku_order_id = '".$skuid."'");
      $skuBoxDetailList = $skuBoxDetailFilter->getList();
      if(count($skuBoxDetailList) == 0){
        foreach ($parcelArr as $key => $parcelData) {
            $skuBoxDetail = new skuBoxDetail();
            $skuBoxDetail->setSkuOrderId($skuid);
            $skuBoxDetail->setBoxQuantity(1);
            $skuBoxDetail->setBagNumber($shipmentReference . str_pad($count++, 4, 0, STR_PAD_LEFT));
            $skuBoxDetail->setNumberPieces((trim($parcelData['itemquantity'])==''?1:$parcelData['itemquantity']));
            $skuBoxDetail->setBoxWeight((trim($parcelData['weight'])==''?0:$parcelData['weight'])); // weight
            $skuBoxDetail->setBoxWidth((trim($parcelData['width'])==''?0:$parcelData['width'])); // width
            $skuBoxDetail->setBoxHeight((trim($parcelData['height'])==''?0:$parcelData['height'])); // Height
            $skuBoxDetail->setBoxLength((trim($parcelData['length'])==''?0:$parcelData['length'])); // Length
            $skuBoxDetail->setUserId($consignment->getUserId());
            $skuBoxDetail->setDateCreated(strtotime(date("Y-m-d H:i:s")));
            $skuBoxDetail->setSendWms(1);
            $skuBoxDetail->save();
            $skuboxid = $skuBoxDetail->getId();
            
            $itemArray = $itemArr[$key];
            foreach($itemArray as $item){
                    $skuboxMapping = new SkuBoxMapping();
                    $skuboxMapping->setSkuBoxDetailId($skuboxid);
                    $skuboxMapping->setSkuId($item["item_sku_id"]);
                    $skuboxMapping->setSkuQuantity($item["no_of_items"]);
                    $skuboxMapping->save();
            }
        }        
      } 
      else
      {
          
          foreach($skuBoxDetailList as $key => $skuBoxList){
             
            $parcel = isset($parcelArr["'".$key."'"]) ? $parcelArr["'".$key."'"] : $parcelArr[$key];
            $skuBoxList->setSkuOrderId($skuid);
            $skuBoxList->setBoxQuantity(1);
            $skuBoxList->setNumberPieces((trim($parcel['itemquantity'])==''?1:$parcel['itemquantity']));
            $skuBoxList->setBoxWeight((trim($parcel['weight'])==''?0:$parcel['weight'])); // weight
            $skuBoxList->setBoxWidth((trim($parcel['width'])==''?0:$parcel['width'])); // width
            $skuBoxList->setBoxHeight((trim($parcel['height'])==''?0:$parcel['height'])); // Height
            $skuBoxList->setBoxLength((trim($parcel['length'])==''?0:$parcel['length'])); // Length
            $skuBoxList->setUserId($consignment->getUserId());
            //$skuBoxDetail->setDateCreated(date('Y-m-d H:i:s'));
            $skuBoxList->save();
            $skuboxid = $skuBoxList->getId();
            
            $skuboxMapping = new SkuBoxMapping();
            $sql = "delete from sku_box_mapping where sku_box_detail_id = '".$skuboxid."'";
            $skuboxMapping->deleteSkuBoxMappingFromSql($sql);
            
            $itemArray = $itemArr[$key];
            foreach($itemArray as $item){
                $skuboxMapping = new SkuBoxMapping();
                $skuboxMapping->setSkuBoxDetailId($skuboxid);
                $skuboxMapping->setSkuId($item["item_sku_id"]);
                $skuboxMapping->setSkuQuantity($item["no_of_items"]);
                $skuboxMapping->save(); 
            }
          }
      }
	   $output = self::SendBoxDataToWMS($skuid);
           return $output;
    }
    /*
     * End Save SKU Box Details
     */
	 
	 /*
     * Send SKU Box Data to WMS
     */
	 
    private  static function SendBoxDataToWMS($skuOrderId) 
    {
        if($skuOrderId > 0)
        {
            try
                {
                    $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
                    $skuorder = new SkuOrder($skuOrderId);
                    $warehouseId = $skuorder->getWarehouseId();

                    $userObj = new User($skuorder->getUserId());
                    $userAccountId = $userObj->getUserAccountId();				
                    $userAccountObj = new CustomerAccount($userAccountId);
                    $userAccount = $userAccountObj->getUserAccount();

                    $warehouseObj = new Warehouse($warehouseId);
                    $warehouseCode = $warehouseObj->getWarehouseCode();

                    $warehouseCode2Digit = "";
                    if($warehouseCode == 'BHX')
                    $warehouseCode2Digit = "BM";
                    $request = new stdClass();
                    $request->ProcessCode = $skuorder->getShipmentReference();
                    $request->CustomerCode = $userAccountObj->getUserAccount();
                    $request->WarehouseCode = $warehouseCode2Digit;
                    $request->Weight = '1';
                    $request->Length = '1';
                    $request->Width = '1';
                    $request->Height = '1';
                    $request->ExptectedArrivalTime = date('Y-m-d');

                    $skuBoxDetailFilter = new SkuBoxDetailFilter();
                    $skuBoxDetailFilter->addFilter(" sku_order_id = '".$skuOrderId."'");
                    $skuBoxDetailList = $skuBoxDetailFilter->getList();
                    
                    if(count($skuBoxDetailList) > 0)
                    {
                        $FbaContainerDetailDataArr = array();
                        $i = 0;
                        
                        foreach ($skuBoxDetailList as $skuBox) 
                        {
                                $skuId = $skuorder->getSkuId();
                                $skuObj = new Sku($skuId);
                                $FbaContainerDetailDataClass = new stdClass();
                                $FbaContainerDetailDataClass->FbaId = $skuBox->getBagNumber();
                                $FbaContainerDetailDataClass->Sku = $skuObj->getSku();
                                $FbaContainerDetailDataClass->ExpectedQty = $skuBox->getNumberPieces();
                                $FbaContainerDetailDataClass->Length = $skuBox->getBoxLength();
                                $FbaContainerDetailDataClass->Width = $skuBox->getBoxWidth();
                                $FbaContainerDetailDataClass->Height = $skuBox->getBoxHeight();
                                $FbaContainerDetailDataClass->Weight = $skuBox->getBoxWeight();
                                $FbaContainerDetailDataArr['FbaContainerDetailDataModel'][$i++] =  $FbaContainerDetailDataClass;
                        }
                        $request->Quantity = count($skuBoxDetailList);
                        $request->FbaContainerDetailDataModelList = $FbaContainerDetailDataArr;
                        $response = $client->CreateFbaCarton( array("request" => $request));
                        if($response->CreateFbaCartonResult->ErrorMsg != "")
                        {
                            $output['STATUS'] = 'ERROR';
                            $output['ERROR'] = $response->CreateFbaCartonResult->ErrorMsg;
                            $output['MESSAGE'] = $response->CreateFbaCartonResult->ErrorMsg;
                        }
                        else
                        {
                            $output['STATUS'] = 'SUCCESS';
                        }
                    }
                }
                catch(Exception $e)
                {
                        $output['STATUS'] = 'ERROR';
                        $output['ERROR'][] = $e;
                        $output['MESSAGE'] = $e;
                }
                return $output; 
            }
    }

	/*
     * End Send SKU Box Data to WMS
     */
    //sets the type of log.
    private  static function logtype($formPostArray = array()) {
        if ($formPostArray["new"] == "false")
            $logtype = 'A';
        else
            $logtype = 'L';
        return $logtype;
    }

    //build string for log.
    private static function buildstring($consignmentold,$formPostArray = array()) {
        $message = NULL;
        //only compare if there its not a new consignment.
        if ($formPostArray["new"] == "false") {
            if ($formPostArray["hawb"] != $consignmentold->getHawb())
                $message .= ' HAWB was changed from ' . $consignmentold->getHawb() . ' to ' . $formPostArray["hawb"];
            if ($formPostArray["service"] != $consignmentold->getService())
                $message .= ' Service was changed from ' . $consignmentold->getService() . ' to ' . $formPostArray["service"];
            if ($formPostArray["service_type"] != $consignmentold->getServiceType() && $formPostArray["service_type"] != '')
                $message .= ' ServiceType was changed from ' . $consignmentold->getServiceType() . ' to ' . $formPostArray["service_type"];
            if ($formPostArray["handling"] != $consignmentold->getHandling())
                $message .= ' Handling was changed from ' . $consignmentold->getHandling() . ' to ' . $formPostArray["handling"];
            if ($formPostArray["reference"] != $consignmentold->getReference())
                $message .= ' Reference was changed from ' . $consignmentold->getReference() . ' to ' . $formPostArray["reference"];
            if ($formPostArray["company"] != $consignmentold->getCompany() && $formPostArray["company"] != '')
                $message .= ' Company was changed from ' . $consignmentold->getCompany() . ' to ' . $formPostArray["company"];
            if ($formPostArray["contact"] != $consignmentold->getContact() && $formPostArray["contact"] != '')
                $message .= ' Contact was changed from ' . $consignmentold->getContact() . ' to ' . $formPostArray["contact"];
            if ($formPostArray["address_line_1"] != $consignmentold->getAddressLine1() && $formPostArray["address_line_1"] != '')
                $message .= ' Address1 was changed from ' . $consignmentold->getAddressLine1() . ' to ' . $formPostArray["address_line_1"];
            if ($formPostArray["address_line_2"] != $consignmentold->getAddressLine2() && $formPostArray["address_line_2"] != '')
                $message .= ' Address2 was changed from ' . $consignmentold->getAddressLine2() . ' to ' . $formPostArray["address_line_2"];
            if ($formPostArray["address_line_3"] != $consignmentold->getAddressLine3() && $formPostArray["address_line_3"] != '')
                $message .= ' Address3 was changed from ' . $consignmentold->getAddressLine3() . ' to ' . $formPostArray["address_line_3"];
            if ($formPostArray["city"] != $consignmentold->getCity() && $formPostArray["city"] != '')
                $message .= ' City was changed from ' . $consignmentold->getCity() . ' to ' . $formPostArray["city"];
            if ($formPostArray["postcode"] != $consignmentold->getPostCode() && $formPostArray["postcode"] != '')
                $message .= ' Postcode was changed from ' . $consignmentold->getPostCode() . ' to ' . $formPostArray["postcode"];
            if (strtoupper($formPostArray["country"]) != strtoupper($consignmentold->getCountry()))
                $message .= ' Country was changed from ' . $consignmentold->getCountry() . ' to ' . $formPostArray["country"];
            if ($formPostArray["sender_company"] != $consignmentold->getSenderCompany())
                $message .= ' Sender Company was changed from ' . $consignmentold->getSenderCompany() . ' to ' . $formPostArray["service_type"];
            if ($formPostArray["sender_contact"] != $consignmentold->getSenderContact())
                $message .= ' Sender Contact was changed from ' . $consignmentold->getSenderContact() . ' to ' . $formPostArray["service_type"];
            if ($formPostArray["sender_address_line_1"] != $consignmentold->getSenderAddressLine1())
                $message .= ' Sender AddressLine1 was changed from ' . $consignmentold->getSenderAddressLine1() . ' to ' . $formPostArray["sender_address_line_1"];
            if ($formPostArray["sender_address_line_2"] != $consignmentold->getSenderAddressLine2())
                $message .= ' Sender AddressLine2 was changed from ' . $consignmentold->getSenderAddressLine2() . ' to ' . $formPostArray["sender_address_line_2"];
            if ($formPostArray["sender_address_line_3"] != $consignmentold->getSenderAddressLine3())
                $message .= ' Sender AddressLine3 was changed from ' . $consignmentold->getSenderAddressLine3() . ' to ' . $formPostArray["sender_address_line_3"];
            if ($formPostArray["sender_city"] != $consignmentold->getSenderCity())
                $message .= ' Sender City was changed from ' . $consignmentold->getSenderCity() . ' to ' . $formPostArray["sender_city"];
            if ($formPostArray["sender_postcode"] != $consignmentold->getSenderPostCode())
                $message .= ' Sender PostCode was changed from ' . $consignmentold->getSenderPostCode() . ' to ' . $formPostArray["sender_postcode"];
            if ($formPostArray["sender_country"] != $consignmentold->getSenderCountry())
                $message .= ' Sender Country was changed from ' . $consignmentold->getSenderCountry() . ' to ' . $formPostArray["sender_country"];
            if ($formPostArray["sender_telephone"] != $consignmentold->getSenderTelephone())
                $message .= ' Sender Telephone was changed from ' . $consignmentold->getSenderTelephone() . ' to ' . $formPostArray["sender_telephone"];
            // CAN'T READ/EDIT COUNTRY ISO CODE // !!! regenerate from country?
            if ($formPostArray["telephone"] != $consignmentold->getTelephone() && $formPostArray["telephone"] != '')
                $message .= ' Telephone was changed from ' . $consignmentold->getTelephone() . ' to ' . $formPostArray["telephone"];
            if ($formPostArray["number_pieces"] != $consignmentold->getNumberPieces())
                $message .= ' Pieces was changed from ' . $consignmentold->getNumberPieces() . ' to ' . $formPostArray["number_pieces"];
            if ($formPostArray["weight"] != $consignmentold->getWeight())
                $message .= ' Weight was changed from ' . $consignmentold->getWeight() . ' to ' . $formPostArray["weight"];
            //if($formPostArray["hv_lv"]          != $consignmentold->getHvlv())$message .='HVLV was changed ';
            if ($formPostArray["description"] != $consignmentold->getDescription())
                $message .= ' Description was changed from ' . $consignmentold->getDescription() . ' to ' . $formPostArray["description"];
            if ($formPostArray["value"] != $consignmentold->getValue())
                $message .= ' Value was changed from ' . $consignmentold->getValue() . ' to ' . $formPostArray["value"];
            if ($formPostArray["currency"] != $consignmentold->getCurrency())
                $message .= ' Currency was changed from ' . $consignmentold->getCurrency() . ' to ' . $formPostArray["currency"];
            if ($formPostArray["notes"] != $consignmentold->getNotes())
                $message .= ' notes was changed from ' . $consignmentold->getNotes() . ' to ' . $formPostArray["notes"];
        }
        //$formPostArray["bag_number"]     = $consignment->getBagNumber();
        //if($formPostArray["sender_name"]      != $consignment->getSenderName());
        //if($formPostArray["sender_checked"]      != $consignment->getSenderChecked());
        //if($formPostArray["both_checked"]      != $consignment->getBothChecked());
        // check for edited or first created.
        if ($formPostArray["new"] == "false") {
            $mainmessage = 'Consignment Edited: ';
        } else {
            $mainmessage = 'Consignment Created';
        }
        if (($message == NULL) && ($formPostArray["new"] == "false")) {
            $mainmessage .= 'No Changes';
        } else {
            $mainmessage = $mainmessage . $message;
        }
        return trim($mainmessage);
    }
  
    public static function AddReturnShipment($conId,$duplicateParcel=false) {
        $returnId = "";
        if($conId > 0){
            // For consignment
            $columnConSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".SETTING_DB_DATABASE."' AND TABLE_NAME = 'consignment'";
            $returnConRes = DbAccess3::runQuery($columnConSql);
            $columnConName = '';
            if ($returnConRes->num_rows > 0) {
                while($row = $returnConRes->fetch_array()) {
                    if(!in_array($row['COLUMN_NAME'], [ 'id', 'is_customer_billable']))
                        $columnConName .= $row['COLUMN_NAME'].',';
                }
            }
            if (substr($columnConName, -1, 1) == ',')
            {
                $columnConName = substr($columnConName, 0, -1);
            }
        // For parcel
            $columnParcelSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".SETTING_DB_DATABASE."' AND TABLE_NAME = 'parcel'";
            $returnParcelRes = DbAccess3::runQuery($columnParcelSql);
            $columnParcelName = '';
            if ($returnParcelRes->num_rows > 0) {
                while($row = $returnParcelRes->fetch_array()) {
                    if($row['COLUMN_NAME'] != 'id')
                        $columnParcelName .= $row['COLUMN_NAME'].',';
                }
            }
            if (substr($columnParcelName, -1, 1) == ',')
            {
                $columnParcelName = substr($columnParcelName, 0, -1);
            }

            // For item
            // For parcel
            $columnItemSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".SETTING_DB_DATABASE."' AND TABLE_NAME = 'item_details'";
            $returnItemRes = DbAccess3::runQuery($columnItemSql);
            $columnItemName = '';
            if ($returnItemRes->num_rows > 0) {
                while($row = $returnItemRes->fetch_array()) {
                    if($row['COLUMN_NAME'] != 'id')
                        $columnItemName .= $row['COLUMN_NAME'].',';
                }
            }
            if (substr($columnItemName, -1, 1) == ',')
            {
                $columnItemName = substr($columnItemName, 0, -1);
            }
            $sql = "INSERT INTO `consignment` ($columnConName)
                        SELECT $columnConName FROM `consignment`
                    WHERE id = ".DbAccess3::escape($conId);
            $returnId = DbAccess3::runQueryReturnId($sql);
            if($duplicateParcel && $returnId > 0){
                $sqlParcel = "INSERT INTO `parcel` ($columnParcelName)
                        SELECT $returnId,".str_replace('consignment_id,','',$columnParcelName)." FROM `parcel`
                    WHERE consignment_id = ".DbAccess3::escape($conId);
                DbAccess3::runQueryReturnId($sqlParcel);
                $sqlItemDetails = "INSERT INTO `item_details` ($columnItemName) 
                    SELECT $returnId, '', ".str_replace('consignment_id,session_id,','',$columnItemName) ." FROM `item_details` WHERE consignment_id = " . DbAccess3::escape($conId);
                DbAccess3::runQueryReturnId($sqlItemDetails);
            }
        }
        return $returnId;
    }
    public static function AddReturnShipmentParcel($parcelId,$newConId) {
        $returnId = "";
        if($parcelId > 0){
            $sql = "INSERT INTO `parcel` (consignment_id,tracking_number,length,width,height,weight,description,parcel_message,qty,commoditycode,grossweight,pweight,itemvalue,number_item,tarrif_no,update_weight,owe_status_code,chute_sorted,parcel_status_code,routing_code,last_tracking_update)
                        SELECT $newConId,tracking_number,length,width,height,weight,description,parcel_message,qty,commoditycode,grossweight,pweight,itemvalue,number_item,tarrif_no,update_weight,owe_status_code,chute_sorted,parcel_status_code,routing_code,last_tracking_update FROM `parcel`
                    WHERE id = ".DbAccess3::escape($parcelId);
            $returnId = DbAccess3::runQueryReturnId($sql);
        }
        return $returnId;
    }
    
    public static function checkConsignmentInvoiced($consignmentId, $returnData=false) {
        $output = [];
        $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($consignmentId);
        $consignmentChargesFilter = new ConsignmentChargesFilter();
        $consignmentChargesFilter->addFilter("     cc.account_id = '" . $user_account_id . "' AND cc.consignment_id = '" . $consignmentId . "' AND cc.cost_type = 'customer'");
        $consignmentCharges = $consignmentChargesFilter->getColumnList("cc.consignment_id,cc.charge_type_id,cc.invoice_id");
        $output['invoiced'] = 0;
        if (count($consignmentCharges) > 0) {
            if ($consignmentCharges[0]->getInvoiceId() > 0 && !empty($consignmentCharges[0]->getInvoiceId())) {
                $output['invoiced'] = 1;
                $invoice = new Invoices($consignmentCharges[0]->getInvoiceId());
                $output['invoice_type'] = $invoice->getInvoiceType();
            }
        }
        if($returnData) {
            return $output;
        } else {
            return $output['invoiced'];
        }
    }

    public static function getConsignmentUserAccountIdForPricing($consignment_id,$consignmentUserId=NULL) {
        
        $sub_account_array = [];
        $user = SessionManager::getUser();
        $loginSubaccount = new UserAccountFilter();
        $loginSubaccount->addFilter(' parentid = ' . $user->getUserAccountId());
        $loginSubaccountFilter = $loginSubaccount->getColumnList("id");
        if (count($loginSubaccountFilter) > 0) {
            foreach ($loginSubaccountFilter as $loginSubaccountData)
                $sub_account_array[] = $loginSubaccountData->getId();
        }
        
        if(!empty($consignmentUserId)){
            $userObj = new User($consignmentUserId);
        }else{
            $consignmentObj = new Consignment($consignment_id);
            $userObj = new User($consignmentObj->getUserId());
            
        }
        $ShipmentParentAccount = CustomerAccount::accountParentAccount($userObj->getUserAccountId(), true);
        $ShipmentAccountPricingId = array_intersect($ShipmentParentAccount, $sub_account_array);
        if (empty($ShipmentAccountPricingId) || $ShipmentAccountPricingId == 0) {
            $ShipmentAccountPricingId = $ShipmentParentAccount[0];
        }
        $user_account_id = 0;
        if (is_array($ShipmentAccountPricingId)) {
            foreach ($ShipmentAccountPricingId as $valueData) {
                $user_account_id = $valueData;
            }
        } else if (is_numeric($ShipmentAccountPricingId)) {
            $user_account_id = $ShipmentAccountPricingId;
        }
        return $user_account_id;
    }
    
    public static function getShipnmentStatus($status) {
        $c_status = Consignment::stateText(strtolower(trim($status)));
        if (strtolower($c_status) == "label created") {
            $shipment_status = '<span class="label label-sm bg-blue-chambray bg-font-blue-chambray line-height-2"> ' . Translation::GetCaption("PRINTED") . '</span>';
        } else if (strtolower($c_status) == "booked") {
            $shipment_status = '<span class="label label-sm bg-blue-dark bg-font-blue-dark line-height-2"> ' . Translation::GetCaption("SHIPPED") . '</span>'; //= Translation::GetCaption("SHIPPED");
        } else if (Consignment::STATUS_RECYCLED == trim($status)) {
            $shipment_status = '<span class="label label-sm label-danger line-height-2"> ' . Translation::GetCaption("RECYCLED") . '</span>';
        } else if (Consignment::STATUS_DELIVERED == trim($status)) {
            $shipment_status = '<span class="label label-sm bg-green-jungle bg-font-green-jungle line-height-2"> ' . Translation::GetCaption("DELIVERED") . '</span>';
        } else if (Consignment::STATUS_INVALID == trim($status)) {
            $shipment_status = '<span class="label label-sm label-warning line-height-2"> ' . Translation::GetCaption("INVALID") . '</span>';
        } else if (Consignment::STATUS_READY_TO_PRINT == trim($status)) {
            $shipment_status = '<span class="label label-sm label-info line-height-2"> ' . Translation::GetCaption("READY_TO_PRINT") . '</span>';
        } else if (Consignment::STATUS_READY_TO_PRINT == trim($status)) {
            $shipment_status = '<span class="label label-sm label-info line-height-2"> ' . Translation::GetCaption("VALID") . '</span>';
        } else {
            if ($c_status == "Unknown") {
                $shipment_status = "";
            } else {
                $shipment_status = '<span class="label label-sm bg-default bg-font-default line-height-2"> ' . Translation::GetCaption(strtoupper($c_status)) . '</span>';
            }
        }
        return $shipment_status;
    }
    
    public static function getConsignmentReturnStatus($consignmentId) {
        $consignmentObj = new Consignment($consignmentId);
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("     p.consignment_id", $consignmentId);
        $parcelFilter->addGroupBy("p.`parcel_status_code`");
        $parcelFilters = $parcelFilter->getColumnList("COUNT(p.`id`) AS id,p.`parcel_status_code`");
        $totalParcel = 0;
        $totalReturnedParcel = 0;
        if(count($parcelFilters) > 0) {
            foreach($parcelFilters as $parcel) {
                if($parcel->getParcelStatusCode() == Consignment::STATUS_RETURNED) {
                    $totalReturnedParcel = $parcel->getId();
                }
                $totalParcel += $parcel->getId();
            }
            if($totalReturnedParcel > 0) {
                if($totalReturnedParcel == $totalParcel) {
                    return Consignment::STATUS_RETURNED;
                } else {
                    return Consignment::STATUS_PARTIAL_RETURNED;
                }
            } 
        }
        return $consignmentObj->getShipnmentStatus();
    }
   
    
    /*
     *  Check for duplicate hawb and if its duplicate then check recurively and append -1, -2
     */
    public static function checkDuplicateHawb($hawb, $i=0){
       
        $dowhileflag = false;
        $newHawbNumber = $hawb ;
        do{
            $hawb_filter = new ConsignmentFilter();
            $hawb_filter->addDeletedHawbFilter(trim($newHawbNumber));
            $hawblist = $hawb_filter->getColumnList("c.id");
            if(count($hawblist) > 0)
            {
                $i++;
                $newHawbNumber = $hawb . "-". $i;
                $dowhileflag = true;
                //self::checkDuplicateHawb($findHawb, $i);
            }
            else
            {
                $dowhileflag = false;
            }
            
            
        }
        while($dowhileflag);
            return $newHawbNumber;
    }
    public function getuniqueHawb($hawb,$consignmentId) {
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldEqualFilter("hawb", "=", $hawb);
        $consignmentFilter->addFieldEqualFilter("shipment_status", "!=", Consignment::STATUS_RECYCLED);
        $consignmentFilter->addFieldEqualFilter("id", "!=", $consignmentId);
        $consignmentHawbObj = $consignmentFilter->getListNew();
        if(count($consignmentHawbObj) > 0){
            $hawb = $hawb."-rec";
            self::getuniqueHawb($hawb,$consignmentId);
        }
        return $hawb;       
    }
    public static function updateTariffUsingConsignmentId($consignmentId){
        $userLogin = $user = SessionManager::getUser();
        if ($consignmentId > 0) {
            $consignmentObj = new Consignment($consignmentId);
            if ($consignmentObj->getid() > 0) {
                $userAccountId = Consignment::getConsignmentUserAccountIdForPricing($consignmentObj->getId());
                
                $isCheck = UserServicesRoutingFilter::isOwnUserContract($consignmentObj->getServiceId(), $userAccountId);
                $parameter = 0;
                if ($isCheck)
                    $parameter = 1;
                
                $mysqli = new mysqli(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                if ($mysqli->connect_errno) {
                    $message['status'] = 'error';
                    $message['message'] = "ERROR||Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                }
                 $sql = "CALL tariff($consignmentId,'customer', '" . $userAccountId . "', '" . $parameter . "','".$userLogin->getId()."',@S_STATUS,@S_MESSAGE)";
                if (!($res = $mysqli->query($sql))) {
                    $message['status'] = 'error';
                    $message['message'] = "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
                } else {
					/* update account balance */
					CustomerAccount::updateBalance($userAccountId);
					/******************************/
                    $message['status'] = 'success';
                    $message['message'] = "tariff successfully updated";
                }
            } else {
                $message['status'] = 'error';
                $message['message'] = "invalid consignment id";
            }
        } else {
            $message['status'] = 'error';
            $message['message'] = "invalid consignment id";
        }
    }

    public static function getShipmentInfo($hawbNumber) {
        $data = [];
        $shipment = new ConsignmentFilter();
        $shipment->addHawbFilter($hawbNumber);
        $shipment->addJoin('services s', 's.id = c.service_id', 'INNER');
        $shipmentObj = $shipment->getColumnList('c.*, s.name as service_name');
        if(!empty($shipmentObj)) {
            $originCountry = new Country($shipmentObj[0]->getCountryId());
            $destinationCountry = new Country($shipmentObj[0]->getSenderCountryId());
            $data['STATUS'] = 'SUCCESS';
            $data['MESSAGE'] = 'Shipment Details Found';
            $data['data'] = [
                'service' => $shipmentObj[0]->getServiceName(),
                'shipment_type'  => $shipmentObj[0]->getShipmentType(),
                'awb' => $shipmentObj[0]->getAwb(),
                'consignment_status' => $shipmentObj[0]->getConsignmentStatus(),
                'consignment_type' => $shipmentObj[0]->getConsignmentType(),
                'reference' => $shipmentObj[0]->getReference(),
                'company' => $shipmentObj[0]->getCompany(),
                'contact' => $shipmentObj[0]->getContact(),
                'telephone' => $shipmentObj[0]->getTelephone(),
                'number_pieces' => $shipmentObj[0]->getNumberPieces(),
                'address_line_1' => $shipmentObj[0]->getAddressLine1(),
                'address_line_2' => $shipmentObj[0]->getAddressLine2(),
                'address_line_3' => $shipmentObj[0]->getAddressLine3(),
                'origin_country' => $originCountry->getName(),
                'sender_company' => $shipmentObj[0]->getSenderCompany(),
                'sender_email' => $shipmentObj[0]->getSenderEmail(),
                'sender_telephone' => $shipmentObj[0]->getSenderTelephone(),
                'sender_address_line_1' => $shipmentObj[0]->getSenderAddressLine1(),
                'sender_address_line_2' => $shipmentObj[0]->getSenderAddressLine2(),
                'sender_address_line_3' => $shipmentObj[0]->getSenderAddressLine3(),
                'destination _country' => $destinationCountry->getName(),
                'weight_type' => $shipmentObj[0]->getWeightType(),
                'weight' => $shipmentObj[0]->getWeight(),
                'description' => $shipmentObj[0]->getDescription(),
                'notes' => $shipmentObj[0]->getNotes(),
                'value' => $shipmentObj[0]->getValue(),
                'currency' => $shipmentObj[0]->getCurrency(),
                'message' => $shipmentObj[0]->getMessage(),
            ];
        } else {
            $data['STATUS'] = 'ERROR';
            $data['MESSAGE'] = 'No Shipment Data Found.';
            $data['ERROR'] = ['No Shipment data found.'];
        }
        return $data;
    }

    public static function consignmentStatusUpdate($consignmentIds,$shipnmentStatus,$shipnmentStatusComment) {
        $user = SessionManager::getUser();
        if (count($consignmentIds) > 0) {
            foreach ($consignmentIds as $consignmentId) {
                $consignment = new Consignment($consignmentId);
                $consignment->setConsignmentStatus(Consignment::$database_status_array[$shipnmentStatus]);
                $consignment->setShipmentStatus($shipnmentStatus);
                $consignment->save();
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addFieldFilter("    consignment_id", $consignmentId);
                $parcelFilterObjs = $parcelFilter->getColumnList("      p.consignment_id");
                if (count($parcelFilterObjs) > 0) {
                    foreach ($parcelFilterObjs as $parcelFilterObj) {
                        $parcel = new Parcel($parcelFilterObj->getId());
                        $oldStatus = $parcel->getParcelStatusCode();
                        $parcelTracking = $parcel->getTrackingNumber();
                        $parcel->setParcelStatusCode($shipnmentStatus);
                        $parcel->save();

                        $date_added = time();
                        $added_by = $user->getId();
                        $consignmentStatusLog = new ConsignmentStatusLog();
                        $consignmentStatusLog->setParcelId($parcelFilterObj->getId());
                        $consignmentStatusLog->setOldStatus($oldStatus);
                        $consignmentStatusLog->setNewStatus($shipnmentStatus);
                        $consignmentStatusLog->setMessage($shipnmentStatusComment);
                        $consignmentStatusLog->setAddedBy($added_by);
                        $consignmentStatusLog->setDateAdded($date_added);
                        $consignmentStatusLog->save();

                        $ip = getClientIp();
                        $dateCreated = date('Y-m-d H:i:s');
                        $trackingData = new TrackingData();
                        $trackingData->setEntityId($parcelFilterObj->getId());
                        $trackingData->setEntityType('parcel');
                        $trackingData->setTrackingNumber($parcelTracking);
                        $trackingData->setUserId($added_by);
                        $trackingData->setCarrierDesc($shipnmentStatusComment);
                        $trackingData->setDateCreated($dateCreated);
                        $trackingData->setIpAddress($ip);
                        $trackingData->setStatusCodeId($shipnmentStatus);
                        $trackingData->save(false,true);

                    }
                }
            }
        }
    }
    public function getEbayMasterReport($dateFrom, $dateTo, $mawb, $usercode, $handlingcode, $user_template_select, $debug = false) {
        $output =   [];
        $user_template_select = new CsvTrackingTemplate($user_template_select);
        if ($user_template_select->getId() <= 0) {
            $output["status"] = false;
            $output["message"] = "Please select template.";
        } else {
            $templateJson = $user_template_select->getTemplate();
            $templateJson = json_decode($templateJson);
            $queryExecute = false;
            $where = "";
            $twhere = "";
            if ($dateFrom != '' && $dateFrom != '1970-01-01' && $dateTo != '' && $dateTo != '1970-01-01') {
                $twhere .= " and date_format(c.date_received,'%Y-%m-%d') >= '" . DbAccess3::escape($dateFrom) . "' and c.date_format(date_received,'%Y-%m-%d') <= '" . DbAccess3::escape($dateTo) . "'";
                $queryExecute = true;
            }
            if ($mawb != '') {
                //$where .= " and c.mawb = '".$mawb."'";
                $where .= "AND p.id IN (SELECT parcel_id FROM mawb_parcel_mapping inner join mawb ON mawb.id = mawb_parcel_mapping.mawb_id AND mawb.mawb_number =  '" . $mawb . "')";
                $queryExecute = true;
            }
            if ($usercode != '') {
                $where .= " AND c.user_id in (select id from user where user_account_id = '" . $usercode . "') ";
                $queryExecute = true;
            }
            if ($handlingcode != '') {
                $where .= " and c.service_id = '" . $handlingcode . "'";
                $queryExecute = true;
            }

            if (!$queryExecute) {
                return;
            }
            $csv = "";
            $cr = "\r\n";
            $totalWeight = '';
            $totalPieces = '';
            $csvHeader = "";
            $csvHeader = ["Sr.",
                "Mawb",
                "Parcel Tracking #",
                "Service Name",
                "Service Code",
                "Label Created Date",
                "Postcode",
            ];
            $sql = "  SELECT 
                    (@cnt:=@cnt + 1) AS rownumber,
                    c.mawb,
                    c.awb,
                   # c.id,
                   s.name, 
                    s.code , #c.handling,
                    #c.consignment_status,
                    
                    #c.hawb,
                    #c.date_created,
                    FROM_UNIXTIME(c.date_label_created, '%Y-%m-%d %H:%i:%s'),
                    UPPER(REPLACE(postcode, ' ', '')) 'postcode',";
            foreach ($templateJson as $key => $value) {
                $csvHeader[] = $value;
                $sql .= "    (SELECT date_created FROM tracking_data tdh WHERE tdh.carrier_desc = '" . $key . "'  AND tdh.entity_id = p.id LIMIT 1) '" . $value . "',";
            }
            $sql .= "  ''
                        FROM (
                                consignment c 
                            INNER JOIN 
                                parcel p ON c.id = p.consignment_id
                            INNER JOIN 
                            
                                services s ON s.id = c.service_id
                            ) 
                        CROSS JOIN (SELECT @cnt:=0) AS dummy 
                            WHERE
                        consignment_status <> 'recycled'
                        " . $where . " order by c.service_id ASC";
            if ($debug) {
                echo $sql;
                die;
            }
            $result = DbAccess3::runQuery($sql);
            if (mysqli_num_rows($result) > 0) {
                $csvRowField[] = $csvHeader;

                while ($row = mysqli_fetch_assoc($result)) {
                    $csvRowField[] = $row;
                }
                $output["status"] = true;
                $output["data"] = $csvRowField;
            } else {
                $output["status"] = false;
                $output["message"] = "No data found";
            }
        }
        return $output;
    }

    public static function checkVatChargable($fromCountry,$toCountry)
    {
        $sql = "SELECT COUNT(*) as total FROM country WHERE id IN (".$fromCountry.",".$toCountry.") AND region IN ('DBP', 'R1')";
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        if($fromCountry == '225' && $toCountry == '225') {
            $data['total'] = 2;
        }
        return $data['total'];
    }
    public static function checkHawbSameAccountExsit($hawb,$accountId){
        $return = '';
        if(!empty($hawb) && !empty($accountId)){
            $sql = "SELECT
                      c.*
                    FROM
                      `consignment` c
                      JOIN `user` u
                        ON u.id = c.`user_id`
                      JOIN customer_account ua
                        ON u.`user_account_id` = ua.id
                    WHERE c.`hawb` = '" . DbAccess3::escape($hawb) . "' AND ua.`id` = '" . DbAccess3::escape($accountId) . "' AND c.`shipment_status` = '11'  ";
            $result = DbAccess3::runQuery($sql);
            if (mysqli_num_rows($result) > 0) {
                $return = mysqli_fetch_assoc($result);
            }
        }
        return $return;
    }
    
    public static function getDestinationHub($postcode, $state, $countryId){
        if($countryId > 0){
            $country = new Country($countryId);
            $destinationhub = "";
            $postcode = strtoupper(trim(str_replace(" ", "", $postcode)));
            if(strtoupper($country->getIso()) == "US"){
                $postcodeThreeChar = substr($postcode, 0, 3);
                $presortDestinationWarehouseFilter = new presortDestinationWarehouseFilter();
                $presortDestinationWarehouseFilter->addFieldFilter("postcode", $postcodeThreeChar);
                $presortDestinationWarehouseFilter->addFieldFilter("countryid", $countryId);
                $presortList = $presortDestinationWarehouseFilter->getList();
                if(count($presortList) > 0){
                    $destinationhub = $presortList[0]->getWarehouseId();
                }
            }
            else if(strtoupper($country->getIso() == "CA")){
                $postcodeFirstChar = substr($postcode, 0, 1);
                if($postcodeFirstChar == "V" || $postcodeFirstChar == "X"){
                    $postcodeFirstChar = substr($postcode, 0, 3);
                }
                $presortDestinationWarehouseFilter = new presortDestinationWarehouseFilter();
                $presortDestinationWarehouseFilter->addFieldFilter("postcode", $postcodeFirstChar);
                $presortDestinationWarehouseFilter->addFieldFilter("countryid", $countryId);
                $presortList = $presortDestinationWarehouseFilter->getList();
                if(count($presortList) > 0){
                    $destinationhub = $presortList[0]->getWarehouseId();
                }
                else
                {
                    $presortDestinationWarehouseFilter = new presortDestinationWarehouseFilter();
                    $presortDestinationWarehouseFilter->addFieldFilter("countryid", $countryId);
                    $presortDestinationWarehouseFilter->addFieldFilter("is_default", '1');
                    $presortList = $presortDestinationWarehouseFilter->getList();
                    if(count($presortList) > 0){
                        $destinationhub = $presortList[0]->getWarehouseId();
                    }
                }
            }
            else if(strtoupper($country->getIso() == "AU")){
                if($state != ""){
                    $presortDestinationWarehouseFilter = new presortDestinationWarehouseFilter();
                    $presortDestinationWarehouseFilter->addFieldFilter("state", trim($state));
                    $presortDestinationWarehouseFilter->addFieldFilter("countryid", $countryId);
                    $presortList = $presortDestinationWarehouseFilter->getList();
                    if(count($presortList) > 0){
                        $destinationhub = $presortList[0]->getWarehouseId();
                    }
                    else
                    {
                        $presortDestinationWarehouseFilter = new presortDestinationWarehouseFilter();
                        $presortDestinationWarehouseFilter->addFieldFilter("is_default", '1');
                        $presortDestinationWarehouseFilter->addFieldFilter("countryid", $countryId);
                        $presortList = $presortDestinationWarehouseFilter->getList();
                        if(count($presortList) > 0){
                            $destinationhub = $presortList[0]->getWarehouseId();
                        }   
                    }
                }
                else
                {
                    $presortDestinationWarehouseFilter = new presortDestinationWarehouseFilter();
                    $presortDestinationWarehouseFilter->addFieldFilter("is_default", '1');
                    $presortDestinationWarehouseFilter->addFieldFilter("countryid", $countryId);
                    $presortList = $presortDestinationWarehouseFilter->getList();
                    if(count($presortList) > 0){
                        $destinationhub = $presortList[0]->getWarehouseId();
                    }   
                }
            }
            else if(strtoupper($country->getIso() == "DE")){
                $presortDestinationWarehouseFilter = new presortDestinationWarehouseFilter();
                    $presortDestinationWarehouseFilter->addFieldFilter("countryid", $countryId);
                    $presortDestinationWarehouseFilter->addFieldFilter("is_default", '1');
                    $presortList = $presortDestinationWarehouseFilter->getList();
                    if(count($presortList) > 0){
                        $destinationhub = $presortList[0]->getWarehouseId();
                    }
            }
            return $destinationhub;
        }
    }
    
}

// class
