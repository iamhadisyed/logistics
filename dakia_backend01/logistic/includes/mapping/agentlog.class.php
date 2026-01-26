<?php

class AgentLog extends DbAccess3 {

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
        parent::__construct("agent_log", 'id', $fieldList, $mixedCreator);
    }
   
    public static function logArray(){
        $displayArray   =   array(
            'id'      => 'ID',
            'agentcode'      => 'Agent Code',
            'agentname'      => 'Agent Name',
            'vatno'   => 'Vat No',
            'currency' 		=> 'Currency',
            'active' => 'Active',
            'contactname' => 'Contact Name',
            'addressline1' => 'Address Line 1',
            'addressline2' => 'Address Line 2',
            'addressline3' => 'Address Line 3',
            'countryisocode'=>'Country Iso Code',
            'county'=>'Country',
            'city'=>'City',
            'postcode'             => 'Postcode',
            'telephone'           => 'Telephone',
            'mobile'      => 'Mobile',
            'fax'               => 'Fax',
            'email'                => 'Email',
            'alternativecontact_1'               => 'Alternative Contact 1',
            'alternative1telephone'               => 'Alternative Telephone',
            'alternative1mobile'    => 'Alternative Mobile',
            'alternative1fax'         => 'Alternative Fax',
            'alternative1email' => 'Alternative Email',
            'alternativecontact_2'         => 'Alternative Contact 2',
            'alternative2telephone'   => 'Alternative Telephone',
            'alternative2mobile'   => 'Alternative Mobile',
            'alternative2fax'           => 'Alternative Fax',
            'alternative2email'       => 'Alternative Email',
            'remarks'       => 'Remarks',
            'agenttype'      => 'Agent Type',
            'isdeleted'        => 'Deleted'
        );
        return $displayArray;
    }
    
   

    /**
     * Get list of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getAgentLogListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getAgentLogCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
    
    public function createlog($user_id = '', $ipaddress, $Serviceid, $log_type, $message, $previous_data, $current_data) {
        
        if ($user_id == '') {
            $this->setUserId();
        } else {
            $this->setUserId($user_id);
        }
        $ipaddress = str_replace('.', '', $ipaddress);
        $this->setLogdate(time());
        $this->setIpaddress($_SERVER['REMOTE_HOST']);
        $this->setLogId($Serviceid);
        $this->setLogType($log_type);
        $this->setMessage($message);
        $this->setPreviousData($previous_data);
        $this->setCurrentData($current_data);
        $this->save();
        
    }
    
  

}

// class