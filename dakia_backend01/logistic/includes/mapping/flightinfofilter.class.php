<?php

// get settings
class FlighInfoFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";
    private $group_by = "";
    /**
     * Get list of RemoteareasGroups items based on filter conditions
     *
     * @return array[RemoteareasGroups]
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
        
        $sql = "SELECT count(f.id) as total FROM flight_info f $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return FlightInfo::getFlightInfoListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*') {
        // has filter been configured?
        $where = "";
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if (!empty($this->order_by))
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if (!empty($this->join))
            $checkJoin = $this->join;
        
        $joinColumnName = "";
//        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
//            $joinColumnName = ",c.carrier AS carrier_id";
//        }
        
        
       $sql = "SELECT " . $columns . " $joinColumnName FROM flight_info f  $checkJoin  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return FlightInfo::getFlightInfoListFromSql($sql);
    }

    /**
     * Get list of RemoteareasGroups items based on filter conditions
     *
     * @return array[RemoteareasGroups]
     */
    public function getList($join = FALSE,$debug =FALSE) {
        // has filter been configured?
       $jointFlightMapping = '';
       $columnJointFlightMapping = '';
       $where = "";
       $limit = '';
        if (!empty($this->filter)) {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        else
            $sort = "ORDER BY fi.`id` DESC";

        if(!empty($this->rowsPerPage)){
            $limit = 'LIMIT '. $this->pageOffset . ',' .$this->rowsPerPage;
        }

       if($join === TRUE){
            $jointFlightMapping =  "  INNER JOIN `flight_mapping` fm  ON fm.`flight_info_id` = fi.`id` AND fm.is_delete = '0' INNER JOIN  `user_account` u  ON u.`id` = fi.`account_id` INNER JOIN `mawb` ma ON ma.`id` = fm.`mawb_id` ";
            $columnJointFlightMapping = " ,ma.mawb_number AS mawb, u.user_account ";
       }
       $sql = "SELECT 
                fi.*
               $columnJointFlightMapping
              FROM
                `flight_info` fi 
               $jointFlightMapping   $where $sort   $limit";
if($debug){
    echo "<pre>";
    print_r($sql);
    echo "</pre>";
    die;
}
        t($sql, __METHOD__);
        return FlightInfo::getFlightInfoListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000,$debug = false) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
        if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $fields . ", f.id 
				FROM flight_info f
				$checkJoin
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        if($debug){
            print_r($sql);
            die;
        }
        return FlightInfo::getFlightInfoListFromSql($sql);
    }

    /**
     * Get count of RemoteareasGroups items based on filter conditions
     *
     * @return int
     */
    public function getCount($join = TRUE) {
        $result = $this->getList($join);
        return sizeof($result);
    }

    public function addGroupBy($colm) {
        if ($this->group_by != "")
            $this->group_by .= " , ";
        $this->group_by .= "  " . $colm . " ";
    }
    /*     * *
     * Oder by Column Name
     */

    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }

    public function addFilter($filterVal) {
        if (!empty($this->filter))
            $this->filter .= " AND ";

        $this->filter .= "    " . $filterVal . " ";
    }
//    public function addCarrierJoin() {
//        $this->join.= " JOIN `carrier` c  ON rg.carrier_id = c.id ";
//    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFromFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " >= '" .$value. "'";
    }
    public function addToFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " <= '" .$value. "'";
    }
    public function addInFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " IN( " .$value. ")";
    }
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    public function flightInfoReport(){
        $sql = "SELECT 
                    m.mawb,
                    m.flight_number,
                    p.pieces,
                    p.weight,
                    p.etd,
                    p.eta,
                    p.current_status,
                    p.cleared,
                    p.status,
                    p.comments,
                    p.`account_id`,
                    p.shed,
                    p.`date_created`,
                    u.user_account
                  FROM
                    flight_info p 
                    INNER JOIN flight_mapping m ON m.`flight_info_id` = p.`id`
                    INNER JOIN  `user_account` u  ON u.`id` = p.`account_id` 
                  WHERE (
                      DATE(p.`date_created`) = '".date('Y-m-d')."' 
                      AND STATUS = 'in warehouse'
                    ) 
                    OR (
                      DATE_SUB(p.eta, INTERVAL 7 DAY) 
                      AND STATUS IN ('not_assigned', 'M')
                    ) 
                  ORDER BY STATUS DESC,
                    current_status DESC,
                    eta DESC";
        //echo $sql;exit;
        return FlightInfo::getFlightInfoListFromSql($sql);
    }
    
     public function getViewPreAlertReportCount() {
        $result = $this->flightInfoReport();
        return sizeof($result);
    }
    public function addFieldNotEqualFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " != '" . DbAccess3::escape($fieldValue) . "'";
    }
    public function getFlightData($flightId) {
        $returnData = "";
        if($flightId > 0){
            $sql = "SELECT
                          fl.`flight_number`,
                          mawb.`mawb_number` AS mawb,
                          SUM(p.`weight`) AS weight,
                          SUM(p.`itemvalue`) AS foreign_value,
                          COUNT(p.id) AS pieces,
                          c.awb AS hawb,
                          c.hawb AS awb,
                          c.`country_id` AS country_id,
                          c.`sender_country_id` AS origin_country,
                          c.sender_name AS sender_name,
                          c.contact AS contact,
                          c.`description` AS description,
                          c.awb AS tracking_number,
                          c.`currency` AS currency,
                          c.`id` AS consignment_id,
                          c.address_line_1,
                          c.address_line_2,
                          c.address_line_3,
                          c.city,
                          c.state,
                          c.postcode,
                          c.sender_address_line_1,
                          c.sender_address_line_2,
                          c.sender_address_line_3,
                          c.sender_city,
                          c.sender_state,
                          c.sender_postcode
                        FROM
                          `flight_info` fi
                          JOIN `flight` fl
                            ON fl.`id` = fi.`transport_id`
                          JOIN `flight_mapping` fm
                            ON fi.`id` = fm.`flight_info_id`
                          JOIN `mawb`
                            ON mawb.id = fm.`mawb_id`
                          JOIN `mawb_parcel_mapping` mpm
                            ON mpm.`mawb_id` = mawb.`id`
                          JOIN parcel p
                            ON p.`id` = mpm.`parcel_id`
                          JOIN consignment c
                            ON c.`id` = p.`consignment_id`
                        WHERE fi.`id` = '" . DbAccess3::escape($flightId) . "'
                        GROUP BY c.id";
            //echo $sql;die;
            $returnData =  FlightInfo::getFlightInfoListFromSql($sql);
        }
        return $returnData;
    }
}

// class
?>