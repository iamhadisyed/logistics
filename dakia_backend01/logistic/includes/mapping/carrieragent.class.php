<?php

/**
 * carrierAgent Object
 *
 */
class carrierAgent extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'lang_key' => 'string',
            'parent_id' => 'number',
            'file_name' => 'string',
            'description' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'query_string' => 'string',
            'icon' => 'string',
            'sort_order' => 'number',
            'is_menu_item' => 'number',
            'is_active' => 'number',
            'is_deleted' => 'number'
        );
        parent::__construct("carrier_agent", 'id', $fieldList, $mixedCreator);
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
    public static function getcarrierAgentListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfcarrierAgentFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM carrier_agent WHERE id = '".DbAccess3::escape($id)."'");
    }
}
