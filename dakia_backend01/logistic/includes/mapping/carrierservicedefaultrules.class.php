<?php

/**
 * carrierServiceDefaultRules Object
 *
 */
class carrierServiceDefaultRules extends DbAccess3 {

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
            'from_weight' => 'string',
            'to_weight' => 'string',
            'is_default' => 'bit',
            'agent_type' => ['enum' => ['outbound','dispatch'],'default' => 'outbound'],
            'agent_name' => 'undefined'
        );
        parent::__construct("carrier_service_default_rules", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }
    
    public function getAgentId() {
        return $this->valArray["agentid"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getcarrierServiceDefaultRulesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfcarrierServiceDefaultRulesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM carrier_service_default_rules WHERE id = '".DbAccess3::escape($id)."'");
    }
    public static function deleteByServiceId($serviceId) {
        if($serviceId != "" && $serviceId > 0)
            self::runQuery("DELETE FROM carrier_service_default_rules WHERE serviceid = '".DbAccess3::escape($serviceId)."'");
    }
}
