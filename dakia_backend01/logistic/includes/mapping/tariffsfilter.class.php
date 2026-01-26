<?php

////////////////////////////////////////////////////
//
// Class for dealing with courier tariffs
//
////////////////////////////////////////////////////
/**
 * Tariff - Tariff class
 * @package Courier
 */
class TariffsFilter{
    private $filter = "";
    private $order_by = "";
    private $group_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    /**
     * Get list of CarrierZones items based on filter conditions
     *
     * @return array[CarrierZones]
     */
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        $sql = "SELECT count(t.id) as total FROM tariffs t $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return Tariffs::getTotalNumberOfTariffsFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*',$debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $columns . " FROM tariffs t $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if($debug)
        {
            echo $sql;
            die;
        }
        return Tariffs::getTariffsListFromSql($sql);
    }

    /**
     * Get list of CarrierZones items based on filter conditions
     *
     * @return array[CarrierZones]
     */
    public function getList($columnName = "*",$debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = $groupBy = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($this->group_by != "")
            $groupBy = " ".$this->group_by;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

         $sql = "SELECT $columnName
                FROM tariffs t
                $checkJoin
                $where
                $sort
                $groupBy
                ";
         
         if($debug){
             echo $sql;
         }
        t($sql, __METHOD__);
        return Tariffs::getTariffsListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $fields . ", id
				FROM tariffs t
				$checkJoin
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return Tariffs::getTariffsListFromSql($sql);
    }

  
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /*     * *
     * Oder by Column Name
     */

    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $filterVal . " ";
    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addStartDateFilter($filterDate) {
        $this->filter .= " AND ";
        $this->filter .= "t.start_date>='" . DbAccess3::escape(date("Y-m-d",strtotime($filterDate))) . "'";
    }
    public function addEndDateFilter($filterDate) {
        $this->filter .= " AND ";
        $this->filter .= "t.end_date<='" . DbAccess3::escape(date("Y-m-d",strtotime($filterDate))) . "'";
    }

    public function addDateFilter($col, $date, $operator = '>=') {
        $this->filter .= " AND ";
        $this->filter .= $col." ".$operator." '" . DbAccess3::escape(date("Y-m-d",strtotime($date))) . "'";
    }

    public function addFilterIn($field, $values, $not = false)
    {
        if (is_array($values)) {
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " " . $field . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "') ";
        } else {
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " " . $field . ($not === true ? ' NOT' : '') . " IN (" . $values . ") ";
        }
    }
    
   
    public function addTariffsDetailsJoin() {
        $this->join.= " LEFT JOIN `tariffs_details` td  ON t.id = td.tariffs_id ";
    }
    public function addJoinCarrierZone() {
        $this->join .= " JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2";
    }
    public function addCarrierZonesCountriesJoin() {
        $this->join.= " LEFT JOIN `carrier_zones_countries` czc  ON t.id = czc.carrier_zone_id ";
    }
    
    public function addJoin() {
	$this->join .= " JOIN `carrier` c  ON t.carrier_id = c.id ";
    }
    
     public function addCustomJoin($join) {
	$this->join .= $join;
    }
    
    public static function getTariffExcelData($tariffId = 0) {


        $sql = "SELECT 
                    cz.`name` AS name,
                    cnt.`name` AS tariff_type,
                    c.`rightsymbol`  AS currency_id,
                    td.`piece_cost` AS carrier_id,
                    td.`weight_cost` AS description,
                    sctt.`transit_time` AS service_id,
                    t.`start_date` AS start_date ,
                    t.`end_date` AS end_date,
                    s.`to_weight` AS user_account_id,
                    s.`name`  AS status,
                    s.`max_height` AS tariffs_pricing_rule_id,
                    s.`max_length` AS id,
                    s.`max_width` AS updated_by
                  FROM
                    tariffs t 
                    JOIN tariffs_details td 
                      ON td.`tariffs_id` = t.id 
                    JOIN services s 
                      ON s.`id` = t.`service_id` 
                    JOIN currency c 
                      ON c.id = t.`currency_id` 
                    JOIN carrier_zones cz 
                      ON cz.`id` = td.`to_zone_id` 
                    JOIN carrier_zones_countries czc 
                      ON czc.`carrier_zone_id` = cz.`id` 
                    JOIN country cnt 
                      ON cnt.`id` = czc.`country_id` 
                    JOIN `service_country_ttime` sctt 
                      ON sctt.`id_service` = t.`service_id` 
                      AND sctt.`id_country` = czc.`country_id` 
                  WHERE t.id = '".  DbAccess3::escape($tariffId)."' ";
//        echo $sql; die;
        return Tariffs::getTariffsListFromSql($sql);
    }
    public function addGroupBy($field) {
        $this->group_by = " GROUP BY " . $field;
    }
}
?>
