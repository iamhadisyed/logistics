<?php

////////////////////////////////////////////////////
//
// Class for dealing with Invoices
//
////////////////////////////////////////////////////

/**
 * Invoices class
 * @package News Releases
 */
class AgentRestrictedPostcode extends DbAccess3 {


    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'string',
            'agent_id' => 'number',
            'service_id' => 'number',
            'postcode_city' => 'string',
            'is_city' => 'number'
        );
        parent::__construct("agent_restricted_postcode", 'id', $fieldList, $mixedCreator);
    }
    public static function getListSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}

?>
