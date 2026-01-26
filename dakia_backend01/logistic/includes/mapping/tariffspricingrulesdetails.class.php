<?php

class TariffsPricingRulesDetails extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'tariff_pricing_rule_id' => 'number',
            'to_zone_id' => 'number',
            'weight_from' => 'number',
            'weight_to' => 'number',
            'margin' => 'number',
            'margin_type' => 'string',
            'margin_weight_cost' => 'string',
            'margin_piece_cost' => 'string',
            'linehaul' => 'string',
            'linehaul_type' => 'string',
            'tariff_pricing_type' => 'string',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number'
        );

        parent::__construct("tariffs_pricing_rules_details", 'id', $fieldList, $mixedCreator);
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

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
    public static function getTariffsPricingRulesDetailsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfTariffsPricingRulesDetailsFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

   
    public function delete() {
        if ($this->getId() > 0) {
            $sql = "UPDATE tariffs_pricing_rules_details SET status = '2' WHERE id=''" . DbAccess3::escape($this->getId()) . "'";
            $result = DbAccess3::runQuery($sql);
        }
        return;
    }

}

?>
