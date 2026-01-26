<?php
// get settings
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

class ConsignmentBillingHoldLog extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id'                    => 'number',
            'consignment_id'        => 'number',
            'user_account_id_from'  => 'number',
            'user_account_id_to'    => 'number',
            'status'                => ['enum' => ['hold','unhold']],
            'reason'                => 'string',
            'added_by'              => 'number',
            'date_added'            => 'datetime',
            'updated_by'            => 'number',
            'date_updated'          => 'datetime'
        );
        parent::__construct("consignment_billing_hold_log", 'id', $fieldList, $mixedCreator);
    }
    
    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    
    public static function getTotalNumberOfConsignmentBillingHoldLogFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function deleteConsignmentBillingHoldLogFromSql($sql) {
        self::runQuery($sql);
    }
    
    public static function deleteById($id) {
        if($id != "" && $id > 0)
            $sql = "DELETE FROM consignment_billing_hold_log WHERE id='".DbAccess3::escape($id)."'";
        
        self::runQuery($sql);
    }

}
