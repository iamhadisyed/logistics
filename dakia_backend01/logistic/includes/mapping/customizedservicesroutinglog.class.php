<?php

class CustomizedServicesRoutingLog extends DbAccess3 {

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
            'log_id' => 'number'
        );
        //
        parent::__construct("customized_services_routing_log", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getCustomizedServicesRoutingLogListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getCustomizedServicesRoutingLogCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
    
     public function setUserid($val)
    {
        $this->valArray["userid"]=$val;
    }
     public function setIpaddress($val)
    {
        $this->valArray["ipaddress"]=$val;
    }
     public function setLogdate($val)
    {
        $this->valArray["logdate"]=$val;
    }
    public function setLogId($val)
    {
        $this->valArray["log_id"]=$val;
    }
    public function setMessage($val)
    {
        $this->valArray["message"]=$val;
    }
     
    public function createlog($user_id = '', $ipaddress, $productid, $message) {
       
        if ($user_id == '') {
            $this->setUserId();
        } else {
            $this->setUserId($user_id);
        }
        $ipaddress = str_replace('.', '', $ipaddress);
        $this->SetLogdate(date('Y-m-d H:i:s'));
        $this->SetIpaddress($ipaddress);
        $this->SetLogId($productid);
        $this->SetMessage($message);
        $this->Save();
    }

}

// class