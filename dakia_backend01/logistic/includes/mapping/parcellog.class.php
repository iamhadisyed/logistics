<?php

class ParcelLog extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'message' => 'string',
            'userid' => 'number',
            'logdate' => 'datetime',
            'ipaddress' => 'number',
            'userid' => 'number',
            'log_id' => 'number',
            'log_type' => 'string',
            'previous_data' => 'string',
            'current_data' => 'string'
        );
        //
        parent::__construct("parcel_log", 'id', $fieldList, $mixedCreator);
    }
   
    public static function logArray(){
        $displayArray   =   array(
            'id'      => 'ID',
            'consignment_id' => 'Consignment Id',
            'tracking_number' => 'Tracking Number',
            'do_tracking_number' => 'Do Tracking Number',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'weight' => 'Weight',
            'description' => 'Description',
            'parcel_message' => 'Parcel Message',
            'qty' => 'Qty',
            'commoditycode' => 'Commodity Code',
            'grossweight' => 'Gross Weight',
            'pweight' => 'Pweight',
            'itemvalue' => 'Item Value',
            'number_item' => 'Number Item',
            'tarrif_no' => 'Tarrif No',
            'update_weight' => 'Update Weight',
            'owe_status_code' => 'Owe Status Code',
            'chute_sorted' => 'Chute Sorted',
            'parcel_status_code' => 'Parcel Status Code',
            'routing_code' => 'Routing Code',
            'last_tracking_update' => 'Last Tracking Update'
        );
        return $displayArray;
    }
    
   

    /**
     * Get list of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getParcelLogListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getParcelLogCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
    
 
    public function createlog($user_id = '', $ipaddress, $Serviceid, $log_type, $message, $previous_data, $current_data) {
        $ipaddress = getClientIp();
        if ($user_id == '') {
            $this->setUserId();
        } else {
            $this->setUserId($user_id);
        }
        $ipaddress = str_replace('.', '', $ipaddress);
        $this->setLogdate(time());
        $this->setIpaddress($ipaddress);
        $this->setLogId($Serviceid);
        $this->setLogType($log_type);
        $this->setMessage($message);
        $this->setPreviousData($previous_data);
        $this->setCurrentData($current_data);
        $this->save();
        
    }
    
  

}

// class