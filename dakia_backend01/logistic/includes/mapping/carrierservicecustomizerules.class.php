<?php

/**
 * carrierServiceCustomizeRules Object
 *
 */
class carrierServiceCustomizeRules extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'serviceid' => 'number',
            'agentid' => 'number',
            'user_account_id' => 'number',
            'from_weight' => 'string',
            'to_weight' => 'string',
            'status'    => 'bit'
        );
        parent::__construct("carrier_service_customize_rules", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getcarrierServiceCustomizeRulesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfcarrierServiceCustomizeRulesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM carrier_service_customize_rules WHERE id = '".DbAccess3::escape($id)."'");
    }
    public static function deleteByServiceId($serviceId) {        
        if($serviceId != "" && $serviceId > 0)
            self::runQuery("DELETE FROM carrier_service_customize_rules WHERE serviceid = '".DbAccess3::escape($serviceId)."'");
    }
    
    public static function deleteByUserId($userId) {        
        if($userId != "" && $userId > 0)
            self::runQuery("DELETE FROM carrier_service_customize_rules WHERE user_account_id = '".DbAccess3::escape($userId)."'");
    }

    public static function deleteByUserAccountIdAndServiceId($userAccountId, $serviceId) {
        if($userAccountId != "" && $userAccountId > 0 && $serviceId != "" && $serviceId > 0)
            self::runQuery("DELETE FROM carrier_service_customize_rules WHERE user_account_id = '".DbAccess3::escape($userAccountId)."' AND serviceid = '" .DbAccess3::escape($serviceId). "'" );
    }
}
