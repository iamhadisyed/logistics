<?php

/*
 * Parcel Filter
 *
 */

class ParcelFilter {

    private $filter = "";
    private $group_by = "";
    private $join = "";
    private $order_by = "";

    /**
     * Get list of parcels based on filter conditions
     *
     * @return array[parcel]
     */
    public function getList($addLimit = false,$debug=false) {
        // has filter been configured
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        
        $sort = "";
        if ($this->order_by != "") {
            $sort = "ORDER BY " . $this->order_by;
        }
        $limit = "";
        if($addLimit) {
            $limit = "LIMIT $this->pageOffset , $this->rowsPerPage";
        }
        $sql = "SELECT *
                    FROM parcel p
                    $where
                    $sort
                    $limit";
        if($debug){
            echo '<pre>';
            print_r($sql);
            echo '</pre>';
        }
        $parcelObj = Parcel::getParcelListFromSql($sql);
        return $parcelObj;
    }
    /**
     * Get list of parcels based on filter conditions
     *
     * @return array[parcel]
     */
    public function getColumnList($fields, $debug = false) {
        // has filter been configured
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $groupBy = "";
        if ($this->group_by != "")
            $groupBy = "GROUP BY " . $this->group_by;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $sql = "SELECT " . $fields . ", p.id
				FROM parcel p
                                $checkJoin
				$where $groupBy";
        if ($debug) {
            echo '<pre>';
            print_r($sql);
            echo '</pre>';
            die;
        }
        return Parcel::getParcelListFromSql($sql);
    }

    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /**
     * Filter on a consignment Id
     *
     * @param int - consignment Id
     */
    public function addConsignmentIdFilter($id) {
        // List of service types
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "consignment_id=" . DbAccess3::escape($id);
    }

    public function addLicensePlateFilterIn($id) {
        // List of service types
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "p.tracking_number IN($id)";
    }

    public function addUserIdsInFilter($id) {
        // List of service types
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "join_con.user_id IN($id)";
    }

    public function addTrackingNumberFilter($tracking_number) {
        // List of service types
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "p.tracking_number  = '" . DbAccess3::escape($tracking_number) . "'";
    }

    public function addLicensePlateFilter($tracking_number) {
        // List of service types
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " p.tracking_number = '" . DbAccess3::escape($tracking_number) . "'";
    }

    public function addConsignmentIdFilterNew($consignmentId) {
        // List of service types
        if (is_array($consignmentId)) {
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " p.consignment_id IN ( " . implode(', ', $consignmentId) . ")";
        }
        else {
            if ($this->filter != "")
                $this->filter .= " AND ";
            $this->filter .= " p.consignment_id = '" . DbAccess3::escape($consignmentId) . "'";
        }
    }

  

    public function bulkupdateCourierStatus($tracking_number, $shipnmentStatus) {
        $tracking_number = "'" . implode("','", $tracking_number) . "'";
        $sql = "UPDATE parcel SET parcel_status_code = '" . DbAccess3::escape($shipnmentStatus) . "', owe_status_code = '" . DbAccess3::escape(Consignment::$database_status_array[$shipnmentStatus]) . "'
				WHERE tracking_number IN (" . $tracking_number . ")";
        DbAccess3::runQuery($sql);
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '%" . DbAccess3::escape($value) . "%'";
    }

    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }
    
    public function addFieldNotEqualFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " != '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addGroupBy($fieldName) {
        $this->group_by = $fieldName;
    }
    
    public function AddOrderBy($fieldName, $ascending = true) {
        $this->order_by = " ".$fieldName. ($ascending ? "ASC" : " DESC");
    }

    public function addFieldNotFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " != '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addTrackingNumberFilterIn($field, $values) {
        $finalArr = [];
        if (trim($this->filter) != "") {
            $this->filter .= " AND ";
        }
        if (is_array($values)) {
            foreach ($values as $trackinNumber) {
                $finalArr[] = ParseTrackingNumber::Parse($trackinNumber);
            }
            $this->filter .= $field . " IN ('" . implode("','", $finalArr) . "')";
        } else {
            $this->filter .= $field . " IN (" . $values . ")";
        }
    }

    public function addConsignmentTableJoin($joinType="") {
        $this->join .= $joinType." JOIN consignment join_con ON join_con.id = p.consignment_id";
    }

    public function addTrackingDataTableJoin($joinType="") {
        $this->join .= $joinType." JOIN tracking_data track ON track.tracking_number = p.tracking_number";
    }
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT count(p.id) as total FROM parcel p $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return Parcel::getTotalNumberOfParcelFromSql($sql);
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
        if ($this->filter != "") {
            $where = "WHERE " .$this->filter;
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $joinColumnName = "";
        if (strpos($checkJoin, 'join_con') !== false) {
            $joinColumnName = ",join_con.service_id AS number_item";
        }


        $sql = "SELECT " . $columns . " $joinColumnName FROM `parcel` p  $checkJoin  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return Parcel::getParcelListFromSql($sql);
    }
    public function addDateFilter($date_value1, $date_value2, $filterDate) {
        if ($filterDate == "submitted") {
            $this->filter .= " AND ";
            $this->filter .= "(join_con.date_created>='" . date('Y-m-d 00:00:00',strtotime($date_value1)) . "'";

            $this->filter .= " AND ";
            $this->filter .= "join_con.date_created<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";
        } elseif ($filterDate == "shipped") {
            $this->filter .= " AND ";
            $this->filter .= "join_con.date_booked>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "join_con.date_booked<='" . strtotime($date_value2) . "'";
        } elseif ($filterDate == "delivered") {
            $this->filter .= " AND ";
            $this->filter .= "(join_con.date_delivered>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "join_con.date_delivered<='" . strtotime($date_value2) . "')";
        } elseif ($filterDate == "printed") {
            $this->filter .= " AND ";
            $this->filter .= "(join_con.date_label_created>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "join_con.date_label_created<='" . strtotime($date_value2) . "')";
        } else {
            $this->filter .= " AND ";
            $this->filter .= "(join_con.date_printed>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "join_con.date_printed<='" . strtotime($date_value2) . "')";
        }
    }
    public function addNotEqualEmptyFilter($colm) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " != '' ";
    }
    
    public function scannedParcelReport($userAccountId= 0, $wareHouseId = 0, $userId = 0,$trackingNumber=0,$mawb=0,$searchDateCreated){
        $where = '';
        $limit = '';
        if(!empty($userId)){
          $where .= "    AND u.`id` = '$userId' ";  
        }
        if(!empty($userAccountId)){
            $userAccountId = implode(',',$userAccountId);
            $where .="  AND ua.`id` IN ($userAccountId)";
        }
        
        if(!empty($trackingNumber)){
             $where .= "    AND p.`tracking_number` =  '$trackingNumber' ";  
        }
        if(!empty($mawb)){
             $where .= "    AND c.`hawb` = '$mawb' ";  
        }
        if(!empty($searchDateCreated)){
            $searchDateCreated = date("Y-m-d",strtotime($searchDateCreated));
            $where .= "    AND date_format(td.`date_created`, '%Y-%m-%d') =  ".DbAccess3::escape($searchDateCreated);
        }
        if(!empty($this->rowsPerPage)){
            $limit = 'LIMIT '. $this->pageOffset . ',' .$this->rowsPerPage;
        }
        $sql = "SELECT 
                   DISTINCT(p.`id`) AS id,
                    p.`tracking_number`,
                    p.`consignment_id`,
                    ua.`user_account`,
                    u.`user_name` AS grossweight,
                    CONCAT(p.`length`,'*',p.`width`,'*',p.`height`) AS dims,
                    c.hawb AS qty,
                    td.date_created AS description
                  FROM
                    `tracking_data` td 
                    JOIN parcel p 
                      ON p.`id` = td.`entity_id`
                    JOIN consignment c ON c.id = p.consignment_id
                    JOIN `user` u 
                      ON u.id = td.`user_id` 
                    JOIN customer_account ua 
                      ON ua.`id` = u.`user_account_id` 
                  WHERE  td.`status_code_id` = '146' AND td.`warehouse_id` = '".DbAccess3::escape($wareHouseId)."' $where  $limit ";
//        echo $sql;exit;
        return Parcel::getParcelListFromSql($sql);
    }
    
    public function getViewScannedParcelReportCount($userAccountId= 0, $wareHouseId = 0, $userId = 0,$trackingNumber=0,$mawb=0,$searchDateCreated){
        $where = '';
        if(!empty($userId)){
          $where .= "    AND td.`user_id` =  ".DbAccess3::escape($userId);  
        }
        if(!empty($searchDateCreated)){
            $searchDateCreated = date("Y-m-d",strtotime($searchDateCreated));
            $where .= "    AND date_format(td.`date_created`, '%Y-%m-%d') =  ".DbAccess3::escape($searchDateCreated);
        }
        if(!empty($trackingNumber)){
             $where .= "    AND p.`tracking_number` =  '$trackingNumber' ";  
        }
        if(!empty($mawb)){
             $where .= "    AND c.`hawb` = '$mawb' ";  
        }
        if(!empty($userAccountId)){
            $userAccountId = implode(',',$userAccountId);
            $where .="  AND ua.`id` IN ($userAccountId)";
        }
        if(!empty($this->rowsPerPage)){
            $limit = 'LIMIT '. $this->pageOffset . ',' .$this->rowsPerPage;
        }
        $sql = "SELECT count(id) AS id
                    FROM
                    (SELECT 
                        DISTINCT(p.`id`) AS id
                      FROM
                        `tracking_data` td 
                        JOIN parcel p 
                          ON p.`id` = td.`entity_id` 
                        JOIN `user` u 
                          ON u.id = td.`user_id` 
                        JOIN consignment c ON c.id = p.consignment_id
                        JOIN customer_account ua 
                          ON ua.`id` = u.`user_account_id` 
                      WHERE  td.`status_code_id` = '146' AND td.`warehouse_id` = '".DbAccess3::escape($wareHouseId)."' $where) as asd ";
        //echo $sql;exit;
        return Parcel::getParcelListFromSql($sql);
    }
    
    public function getCarrierPalletReport($startDate = '',$endDate = '',$palletID ='',$carrierId='',$serviceId='',$userAccountId=''){
        $where = [];
        $where[] = "  
          pem.`pallet_entity_type` = 'b' ";
        if(!empty($startDate) && !empty($endDate)){
            $where[] = "
                c.`date_label_created` != '' AND c.`date_label_created` > 0
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_label_created),
                  '%Y-%m-%d'
                ) >= '".date('Y-m-d',  strtotime($startDate))."'
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_label_created),
                  '%Y-%m-%d'
                ) <= '".date('Y-m-d',  strtotime($endDate))."' ";
        }
        
        if(!empty($palletID)){
            $where[] = "    
                    pa.`palletno`= '".$palletID."'";
        }
        if(!empty($serviceId)){
            $where[] = "    
                c.`service_id`= '".$serviceId."'";
            
        }
        if(!empty($carrierId)){
            $where[] = "    
                ca.`id`= '".$carrierId."'";
            
        }
        if(!empty($userAccountId)){
            $where[] = "
                u.`user_account_id` IN (".implode(',',$userAccountId).") ";
            
        }

                
        $whereImplode = '';
        if(!empty($where)){
            $where[] = 'WHERE ';
            $where = array_reverse($where);
            $whereImplode = implode(' AND ', $where);
            $whereImplode = substr_replace($whereImplode,' ',7,10);
        }
        
        $sql = "SELECT 
                COUNT(DISTINCT bag.id) AS pweight,            
                c.`service_id`,
                cn.`name`,
                COUNT(p.id) AS id,
                SUM(c.`weight`) AS weight,             
                GROUP_CONCAT(DISTINCT pa.`palletno`) AS qty,
                ca.`carrier` 
              FROM
                `pallet_entity_mapping` pem 
                JOIN `bagging` bag 
                  ON bag.`id` = pem.`entity_id` 
                JOIN `parcel_bagging_mapping` pbm 
                  ON pbm.`bag_id` = bag.id 
                JOIN parcel p 
                  ON p.`id` = pbm.parcel_id 
                JOIN `pallet` pa 
                  ON pa.`id` = pem.`pallet_id` 
                JOIN `consignment` c 
                  ON c.id = p.`consignment_id` 
                JOIN `services` s 
                  ON s.`id` = c.`service_id` 
                JOIN `country` cn 
                  ON cn.`id` = c.`country_id` 
                JOIN `user` u 
                  ON u.`id` = c.`user_id` 
                JOIN `carrier` ca 
                  ON ca.`id` = s.`carrier_id`  
                $whereImplode
                GROUP BY cn.id,
                  pa.id "; 
        //echo $sql;exit;
        return Parcel::getParcelListFromSql($sql);
    }
    
    public function serivceScanReport($startDate = '',$endDate = '',$carrierId ='',$serviceId='',$wareHouseId=array(),$userAccountId=''){
        $where = [];
        
        if(!empty($startDate) && !empty($endDate)){
            $where[] = "
                c.date_created >= '".date('Y-m-d',  strtotime($startDate))."'  AND  c.date_created <= '".date('Y-m-d',  strtotime($endDate))."'  ";
        }
        
        if(!empty($carrierId)){
            if(is_array($carrierId)){
                $carrierId = implode(',',$carrierId);
            }
            $where[] = "    
                    ca.`id` IN (".$carrierId.")";
        }
        if(!empty($wareHouseId)){
            $where[] = "    
                w.`id` IN (".implode(',',$wareHouseId).")";
            
        }
        if(!empty($serviceId)){
             if(is_array($serviceId)){
                $serviceId = implode(',',$serviceId);
            }
            $where[] = "    
                s.`id` IN (".$serviceId.")";
            
        }
        if(!empty($userAccountId)){
            $where[] = "
                us.`user_account_id` IN (".implode(',',$userAccountId).") ";
            
        }
        $whereImplode = '';
        if(!empty($where)){
            $where[] = 'WHERE ';
            $where = array_reverse($where);
            $whereImplode = implode(' AND ', $where);
            $whereImplode = substr_replace($whereImplode,' ',7,10);

        }
        $sql = "SELECT 
                    COUNT(p.id) AS id,
                    SUM(c.`weight`) AS weight,
                    td.`warehouse_id`,
                    w.`warehouse_name` AS tarrif_no,
                    ca.carrier,
                    s.`name`
                  FROM
                    parcel p 
                    JOIN `consignment` c 
                      ON c.`id` = p.`consignment_id` 
                    JOIN `tracking_data` td 
                      ON td.`entity_id` = p.`id` 
                      AND td.`status_code_id` = '146' 
                    JOIN `services` s 
                      ON s.`id` = c.`service_id` 
                    JOIN `carrier` ca 
                      ON ca.`id` = s.`carrier_id` 
                    JOIN `warehouse` w 
                      ON w.`id` = td.`warehouse_id` 
                    JOIN `user` us 
                      ON us.`id` = td.`user_id`
                $whereImplode
              GROUP BY td.`warehouse_id`,s.`id` 
              ORDER BY ca.`id`  ";
//            echo $sql;exit;
        return Parcel::getParcelListFromSql($sql);
    }
    public static function updateParcelColumnByParcelId($parcelId,$col,$colVal) {
        if($parcelId > 0){
            $sql = "UPDATE `parcel` p SET $col = '".DbAccess3::escape($colVal)."' WHERE p.`id` = '".DbAccess3::escape($parcelId)."' ";
            DbAccess3::runQuery($sql);
        }
    }

    public static function updateParcelStatusOnConsignmentStatusChange($consignmentId, $status, $status_text) {
        if ($consignmentId > 0) {
            $sql = "UPDATE `parcel` SET parcel_status_code = '" . DbAccess3::escape($status) . "', owe_status_code = '" . DbAccess3::escape($status_text) . "' WHERE `consignment_id` = '" . DbAccess3::escape($consignmentId) . "'";
            DbAccess3::runQuery($sql);
        }
    }
    public function getParcelSerivce($tracking_number) {
        $return = "";
        if(!empty($tracking_number)){
            $sql = "SELECT
                        p.`tracking_number`,
                        p.`consignment_id`,
                        s.id AS qty,
                        s.`name` AS commoditycode,
                        p.id,
                        p.weight,
                        p.itemvalue,
                        c.currency AS chute_sorted
                      FROM
                        `parcel` p
                        JOIN consignment c
                          ON p.`consignment_id` = c.`id`
                        JOIN services s
                          ON c.`service_id` = s.`id`
                      WHERE p.`tracking_number` = '" . DbAccess3::escape($tracking_number) . "'";
            $return = Parcel::getParcelListFromSql($sql);
        }
        return $return;
    }
    public function getParcelSerivces($trackingNumberArr) {
        $return = "";
        if(!empty($trackingNumberArr)){
            $trackingNumber = "";
            foreach ($trackingNumberArr as $trackingNumberItem) {
                $trackingNumber .= "'".$trackingNumberItem."',";
            }
            $trackingNumber = rtrim($trackingNumber,',');
            $sql = "SELECT
                        s.`name` AS commoditycode,
                        s.id AS id,
                        p.weight,
                        p.tracking_number
                      FROM
                        `parcel` p
                        JOIN consignment c
                          ON p.`consignment_id` = c.`id`
                        JOIN services s
                          ON c.`service_id` = s.`id` 
                      WHERE p.`tracking_number` IN ($trackingNumber)";
            $return = Parcel::getParcelListFromSql($sql);
        }
        return $return;
    }
    
    public function getSorterHoldShipments($date_from, $date_to, $mawb){
        $filter = "";
        if(trim($mawb) != '')
        {
            $filter = "   and p.consignment_id in (select id from consignment where mawb = '".$mawb."')";
        }
        $limit = "LIMIT $this->pageOffset , $this->rowsPerPage";
        $sql = "select p.tracking_number, p.width, p.height, p.length, c.date_added, c.message from parcel p 
                inner join consignment_status_log c ON p.id = c.parcel_id
                where date_format(c.date_added, '%Y-%m-%d') >= '".$date_from."' 
                and date_format(c.date_added, '%Y-%m-%d') <= '".$date_to."'"
                . " and p.id in (select entity_id from tracking_data t where t.track_point = 'Birmingham Sorting Centre - GBR' )" . $limit;
         return Parcel::getParcelListFromSql($sql);
    }
     public function getSorterHoldShipmentsCount($date_from, $date_to, $mawb){
        $filter = "";
        if(trim($mawb) != '')
        {
            $filter = "   and p.consignment_id in (select id from consignment where mawb = '".$mawb."')";
        }
        $sql = "select count(p.id) as total from parcel p 
                inner join consignment_status_log c ON p.id = c.parcel_id
                where date_format(c.date_added, '%Y-%m-%d') >= '".$date_from."' 
                and date_format(c.date_added, '%Y-%m-%d') <= '".$date_to."'"
                . " and p.id in (select entity_id from tracking_data t where t.track_point = 'Birmingham Sorting Centre - GBR' )";
         return Parcel::getTotalNumberOfParcelFromSql($sql);
    }
    public function getParcelWeightNValue($parcelId) {
        $return = "";
        if($parcelId > 0){
            $sql="SELECT
                    p.`weight`,
                    c.`value` AS itemvalue
                  FROM
                    parcel p
                    JOIN consignment c
                      ON p.`consignment_id` = c.`id`
                  WHERE p.`id` = '" . DbAccess3::escape($parcelId) . "'";
            $return = Parcel::getParcelListFromSql($sql);
        }
        return $return;
    }
    public function addFilterIn($field, $values, $filter='filter') {
        $finalArr = [];
        if (trim($this->$filter) != "") {
            $this->$filter .= " AND ";
        }
        if (is_array($values)) {
            foreach ($values as $trackinNumber) {
                $finalArr[] = trim(trim(ParseTrackingNumber::Parse($trackinNumber),"\n"),"\r");
            }
            $this->$filter .= $field ." IN ('" . implode("','", $finalArr) . "')";
        } else {
            $this->$filter .= $field . " IN (" . $values . ")";
        }
    }
	
	public static function getParcelIdFromTrackingNumber($tackingNumber) {
        $tackingNumber = ParseTrackingNumber::Parse($tackingNumber);
        $parcelObj = "";
        $returnMsg = "";
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("    tracking_number", $tackingNumber);
        $parcelObj = $parcelFilter->getColumnList("id");
        if (count($parcelObj) > 0) {
            $returnMsg = $parcelObj[0]->getId();
        }
        return $returnMsg;
    }
    public function getParcelSerivcesDataById($pid) {
        // From parcel weight, and consignemnt id for join
        // From consignment getCountryId, getAwb , getHawb
        $return = "";
        if(!empty($pid)){
            $sql = "SELECT
                        s.`name` AS parcel_service_name,
                        s.`code` AS parcel_service_code,
                        s.`carrier_service_code` AS parcel_service_carrier_code,
                        s.id AS parcel_service_id,
                        p.weight,
                        c.country_id as consignment_country_id,
                        cou.iso as consignment_country_iso,
                        c.awb as consignment_awb,
                        c.hawb as consignment_hawb
                      FROM
                        `parcel` p
                        JOIN consignment c
                          ON p.`consignment_id` = c.`id`
                        JOIN services s
                          ON c.`service_id` = s.`id` 
                          JOIN country cou
                          ON cou.`id` = c.country_id
                      WHERE p.`id` = '" . DbAccess3::escape($pid) . "'";
            $return = Parcel::getParcelListFromSql($sql);
        }
        return $return;
    }
}
