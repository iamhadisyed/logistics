<?php

class ConsignmentLog extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'userid' => 'number',
            'logdate' => 'datetime',
            'ipaddress' => 'number',
            'log_id' => 'number',
            'log_type' => 'string',
            'message' => 'string',
            'previous_data' => 'string',
            'current_data' => 'string'
        );
        
        
    
    
        //
        parent::__construct("consignment_log", 'id', $fieldList, $mixedCreator);
    }

    
    public static function logArray(){
        $displayArray = array
        (
            'id' => 'Id',
            'agent_id' => 'agent id',
            'user_id' => 'user id',
            'service_id' => 'service id',
            'customized_service_id' => 'customized service id',
            'warehouse_user_id' => 'warehouse user id',
            'warehouse_id' => 'warehouse id',
            'sales_pot_id' => 'sales pot id',
            'invoice_id' => 'invoice id',
            'credit_id' => 'credit id',
            'is_invoiced' => 'is invoiced',
            'invoice_type' => 'invoice type',
            'shipment_status' => 'shipment status',
            'shipment_type' => 'shipment type',
            'awb' => 'awb',
            'consignment_status' => 'consignment status',
            'return_awb' => 'return awb',
            'hawb' => 'hawb',
            'mawb' => 'mawb',
            'service_name' => 'service name',
            'reference' => 'reference',
            'date_created' => 'date created',
            'date_label_created' => 'date_label created',
            'date_booked' => 'date booked',
            'date_delivered' => 'date delivered',
            'is_customer_manifested' => 'is customer manifested',
            'booked_file_id' => 'booked file id',
            'company' => 'company',
            'contact' => 'contact',
            'address_line_1' => 'address line 1',
            'address_line_2' => 'address line 2',
            'address_line_3' => 'address line 3',
            'city' => 'city',
            'state' => 'state',
            'postcode' => 'postcode',
            'country_id' => 'country id',
            'telephone' => 'telephone',
            'number_pieces' => 'number pieces',
            'weight_type' => 'weight type',
            'weight' => 'weight',
            'update_weight' => 'update weight',
            'fake_weight' => 'fake weight',
            'charge_weight' => 'charge weight',
            'vol_weight' => 'vol weight',
            'vol_demonimator' => 'vol demonimator',
            'hv_lv' => 'hv lv',
            'description' => 'description',
            'notes' => 'notes',
            'value' => 'value',
            'currency' => 'currency',
            'sender_name' => 'sender name',
            'username' => 'username',
            'sender_checked' => 'sender checked',
            'message' => 'message',
            'sorter_image' => 'sorter image',
            'label_file' => 'label file',
            'is_doc' => 'is doc',
            'email' => 'email',
            'itemtype' => 'itemtype',
            'routing_code' => 'routing code',
            'routing_code_eur' => 'routing code eur',
            'other_routing_code' => 'other routing code',
            'billing_hold' => 'billing hold',
            'send_courier_data' => 'send courier data',
            'remote_charges' => 'remote charges',
            'reinvoices' => 'reinvoices',
            'optimus_sorter' => 'optimus sorter',
            'full_pallet' => 'full pallet',
            'half_pallet' => 'half pallet',
            'quarter_pallet' => 'quarter pallet',
            'date_scanned' => 'date scanned',
            'consignment_type' => 'consignment_type',
            'api_uuid' => 'api uuid',
            'sender_company' => 'sender company',
            'sender_email' => 'sender email',
            'sender_telephone' => 'sender telephone',
            'sender_address_line_1' => 'sender address line 1',
            'sender_address_line_2' => 'sender address line 2',
            'sender_address_line_3' => 'sender address line 3',
            'sender_city' => 'sender city',
            'sender_postcode' => 'sender postcode',
            'sender_country_id' => 'sender country id',
            'sender_state' => 'sender state',
            'collection_date' => 'collection date',
            'collection_start_time' => 'collection start time',
            'collection_end_time' => 'collection end time',
            'collection_confirmation_no' => 'collection confirmation no',
            'is_white_label' => 'is white label'
        );

        return $displayArray;
    }
    /**
     * Get list of Permissions Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getConsignmentLogListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of Permissions Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getConsignmentLogCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
    
     public function setUserid($val)
    {
        $this->valArray["userid"]=$val;
    }
     public function setIpaddress($val)
    {
        $this->valArray["ipaddress"]= ip2long($_SERVER['REMOTE_ADDR']);
    }
     public function setLogdate($val)
    {
        $this->valArray["logdate"]=$val;
    }
     public function setLogId($val)
    {
        $this->valArray["log_id"]=$val;
    }
     public function setLogType($val)
    {
        $this->valArray["log_type"]=$val;
    }
     public function setMessage($val)
    {
        $this->valArray["message"]=$val;
    }
     public function setPreviousData($val)
    {
        $this->valArray["previous_data"]=$val;
    }
     public function setCurrentData($val)
    {
        $this->valArray["current_data"]=$val;
    }
    
    public function createlog($message,$logid, $logType='l', $user_id='',$oldData='', $newData='') {
        $ipaddress = getClientIp();
        $ipaddress = str_replace('.', '', $ipaddress);
        $this->setLogId($logid);
        $this->setLogType($logType);
        if ($user_id == '') {
            $this->setUserId("");
        } else {
            $this->setUserId($user_id);
        }
        $this->SetLogdate(date('Y-m-d H:i:s'));
        $this->SetMessage($message);
        $this->SetIpAddress($ipaddress);
        $this->setPreviousData($oldData);
        $this->setCurrentData($newData);
        $this->Save();   
    }

}

// class