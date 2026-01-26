<?php
/**
 * ConsignmentLog class
 * - deals with Logs of everything that happens with consignments.
 
 
 * Last modified Alex 22/05/2014
 */
class ConsignmentChargesLog extends DbAccess3 {

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
            'ipaddress' => 'string',
            'log_id' => 'number',
            'log_type' => 'string',
            'previous_data' => 'string',
            'current_data' => 'string',            
            'user_name' => 'undefined'
        );
        parent::__construct("consignment_charges_log", 'id', $fieldList, $mixedCreator);
    }

    public static function getConsignmentChargesLogListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getConsignmentChargesLogCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }

    //set functions of each column
    public function setLogId($val) {
        $this->valArray["log_id"] = $val;
    }

    //get userid id number
    public function setUserId($val = '') {
        if ($val == '') {
            $user = SessionManager::getUser();
            if ($user->getId() > 0)
                $this->valArray["userid"] = $user->getId();
            else
                $this->valArray["userid"] = $val;
        }
        else {
            $this->valArray["userid"] = $val;
        }
    }

    //set the action they performed.
    public function setMessage($val) {
        $this->valArray["message"] = $val;
    }

    //ip address
    public function setIpAddress() {
        $this->valArray["ipaddress"] = ip2long($_SERVER['REMOTE_ADDR']);
    }

    //set datetime
    public function setDateTime() {
        $this->valArray["logdate"] = date('Y-m-d H:i:s');
    }

    //creates a log message @param $message,$consignmentid
    public function createlog($message, $logid, $logType, $user_id = '', $oldData = '', $newData = '') {
        $this->setLogId($logid);
        $this->setLogType($logType);
        if ($user_id == '') {
            $this->setUserId();
        } else {
            $this->setUserId($user_id);
        }
        $this->SetMessage($message);
        $this->SetDateTime();
        $this->SetIpAddress();
        $this->SetIpAddress();
        $this->setPreviousData($oldData);
        $this->setCurrentData($newData);

        $this->Save();
    }

}
