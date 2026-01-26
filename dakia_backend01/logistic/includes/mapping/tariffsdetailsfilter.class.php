<?php

/*
 * Service Log Filter
 *
 */

class TariffsDetailsFilter {

    private $filter = "";
    private $order_by = "";
    private $group_by = "";
    private $limit = 1000;
    private $join = "";
    /**
     * Get list of Service Log based on filter conditions
     *
     * @return array[ServiceLog]
     */
    public function getList($debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if(!empty($this->order_by))
            $sort = $this->order_by;

        $groupBy = "";
        if(!empty($this->group_by))
            $groupBy = $this->group_by;

        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $limit = "";
        if ($this->limit > 0)
            $limit = " LIMIT " . $this->limit;

        $sql = "SELECT *
                FROM tariffs_details td
                $checkJoin
                $where
                $sort
                $groupBy
                $limit
                ";
        if($debug){
            echo $sql;die;
        }
        t($sql, __METHOD__);
        return TariffsDetails::getTariffsDetailsListFromSql($sql);
    }
    public function getColumnList($fields,$debug = false, $limit= true) {
        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if(!empty($this->order_by))
            $sort = $this->order_by;

        $groupBy = "";
        if(!empty($this->group_by))
            $groupBy = $this->group_by;

        if ($this->join != "")
            $checkJoin = $this->join;

        $applyLimit = "";
        if($limit)
            $applyLimit = "LIMIT ".$this->limit;

        $sql = "SELECT td.id, ".$fields."
                FROM tariffs_details td
                $checkJoin
                $where
                $sort
                $groupBy
                $applyLimit
                ";
        if($debug){
            echo $sql;die;
        }
        t($sql, __METHOD__);
        return TariffsDetails::getTariffsDetailsListFromSql($sql);
    }
  
    public function AddOrderById($ascending = true) {
        $this->order_by = "td.id" . ($ascending ? "ASC" : " DESC");
    }
    public function addOrderByToZoneId($ascending = true) {
        $this->order_by = " ORDER BY td.to_zone_id" . ($ascending ? "ASC" : " DESC");
    }
    public function addFilter($code)
    {
        $this->filter .= " AND ";    
        $this->filter .= " ".$code." ";
    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }
    public function join($joinTable, $where, $type = "")
    {
        $_where = [];
        $operand = '=';
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $_where[] = $col . " " . $operand . " " . DbAccess3::escape($val);
            }
            $_where = implode(" AND ", $_where);
        } else if (is_string($where)) {
            $_where = $where;
        }
        $this->Join[] = $type . " JOIN " . $joinTable . " ON " . $_where;
    }
    public function where($where, $operand = "=")
    {
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                if($operand == 'LIKE') {
                    $this->filter[] = $col . " " . $operand . " '%" . DbAccess3::escape($val) . "%'";
                } else {
                    $this->filter[] = $col . " " . $operand . " '" . DbAccess3::escape($val) . "'";
                }
            }
        } else if (is_string($where)) {
            $this->filter[] = $where;
        }
    }
    public function addJoinCarrierZone() {
        $this->join .= " JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2";
    }
    public function addJoinTariffAdditionalCharges() {
        $this->join .= " LEFT JOIN `tariff_additional_charges` tac  ON tac.tariff_id = td.tariffs_id ";
    }
    public function addJoinTariff() {
        $this->join .= " JOIN `tariffs` t  ON td.tariffs_id = t.id";
    }
    public function addOrderBySortOrder($ascending = true) {
        $this->order_by = " ORDER BY cz.sort_order" . ($ascending ? " ASC " : " DESC ");
    } 
    public function addOrderBy($field, $ascending = true) {
        $this->order_by = " ORDER BY " . $field . " " . ($ascending ? " ASC " : " DESC ");
    }
    public function addGroupBy($field) {
        $this->group_by = " GROUP BY " . $field;
    }
    /***
    * Limit number of rows returned
    */
    public function setLimit($lim)
    {
            $this->limit = $lim;
    }
    public function getTariffDetails($userAccount,$allowedServices,$fromCountry, $toCountry, $fromWeight, $toWeight){
        $where = '';
        $serviceIn = implode(",",$allowedServices);
        $sql ="SELECT
                  td.`id`,
                  s.`name` AS tariffs_id,
                  s.`code` AS formula,
                  t.`service_id`,
                  td.`weight_from`,
                  td.`weight_to`,  
                  td.`weight_cost`,
                  td.`piece_cost`,
                  c.`logo` AS from_zone_id,
                  td.`to_zone_id`,
                  cur.`rightsymbol` AS currency,
                  sctt.`transit_time`
                FROM
                  tariffs t
                  JOIN currency cur
                    ON cur.`id` = t.`currency_id`
                  JOIN `tariffs_details` td
                    ON td.`tariffs_id` = t.`id`
                  JOIN services s
                    ON s.`id` = t.`service_id` AND s.is_customized = 0 AND s.`id` IN (".$serviceIn.")
                  JOIN carrier c
                    ON c.`id` = s.`carrier_id`
                  JOIN carrier_zones cz
                    ON 1
                    AND IF (
                      c.`zone_base` = 1,
                      s.`carrier_id` = cz.carrier_id,
                      cz.service_id = t.`service_id`
                    )
                  JOIN carrier_zones_countries ocn
                    ON ocn.carrier_zone_id = td.from_zone_id
                    AND
                    CASE
                      WHEN s.`service_type` = 'C'
                      THEN '".DbAccess3::escape($fromCountry)."' IN
                      (SELECT
                        country_id
                      FROM
                        `service_collection_county`
                      WHERE service_id = t.`service_id`)
                      ELSE 1 = 1
                    END 
                    /*ocn.country_id = '".DbAccess3::escape($fromCountry)."'*/
                  JOIN carrier_zones_countries dcn
                    ON dcn.carrier_zone_id = td.to_zone_id
                    AND dcn.country_id = '".DbAccess3::escape($toCountry)."'
                  JOIN `service_country_ttime` sctt
                    ON sctt.`id_service` = t.`service_id`
                    AND sctt.`id_country` = '".DbAccess3::escape($toCountry)."'  
                WHERE t.`tariff_type` = 'supplier'
                  AND t.`user_account_id` = '".DbAccess3::escape($userAccount)."'
                  AND t.`start_date` <= CURRENT_DATE
                  AND t.`end_date` >= CURRENT_DATE
                  AND t.`status` = 1
                  AND s.`active` = 1
                  AND s.`deletedq` = 0
                  /* AND td.`weight_from` >= '".DbAccess3::escape($fromWeight)."'
                  AND td.`weight_to` <= '".DbAccess3::escape($toWeight)."' */
                  AND td.`weight_cost` > 0
                GROUP BY td.id
                ORDER BY td.`weight_cost` ASC";
        //echo $sql."<br />";

        t($sql, __METHOD__);
        return TariffsDetails::getTariffsDetailsListFromSql($sql);
    }
}
