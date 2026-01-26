<?php 
/**
 * UserAccountServiceCharges class
 * @package News Releases
 */
class UserAccountServiceCharges extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'user_account_id' => 'number',
            'service_id' => 'number',
            'consignment_charges_types_id' => 'number',
            'charge' => 'number',
            'charge_type' => ['enum' => ['fixed', 'percentage'],'fixed'],
//            'discount' => 'number',
//            'discount_type' => ['enum' => ['fixed', 'percentage']],
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            
            'user_account' => 'undefined',
            'title' => 'undefined',
            'name' => 'undefined'
        );

        parent::__construct("user_account_service_charges", 'id', $fieldList, $mixedCreator);
    }

    public static function getUserAccountServiceChargesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfUserAccountServiceChargesFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteUserAccountServiceChargesFromSql($sql) {
        self::runQuery($sql);
    }

}

?>
