<?php
/*
 * Consignment Filter
 *
 */
class TrackingDataFilter
{
	private $filter = "";
	private $order_by = "";
	private $group_by = "";
	private $join = "";
	private $pageOffset = 0;
	private $limit = "";

	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;
                
		$sql = "SELECT *
				FROM tracking_data t
				$this->join
				$where
				$sort 
				";
		t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
	}
	
	
	public function getColumnList($fields, $sort = "", $limit = 5000, $debug = false)
	{
		// has filter been configured?
        if($sort != "")
            $this->order_by = $sort;
        
            $where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
                
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

                 $sql = "SELECT SQL_CALC_FOUND_ROWS ".$fields.", t.id FROM tracking_data t " . $this->join . ' ' .$where." ".$sort." limit ". $this->pageOffset . ", " . $limit;
                if ($debug)
                {
                    echo $sql;        die;           
                }
//        echo $sql; die;
        return TrackingData::getTrackingDataListFromSql($sql,$debug);
	}

	public function getColumnListCount() {
	    $sql = "SELECT FOUND_ROWS() as total";
        return TrackingData::getTotalNumberOfRecordsFromSql($sql);
    }
	
	
	public function getColumnListLimit($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT  ".$fields.", id
            FROM tracking_data t
            $where
            $sort limit 100";
				
		//mail("kazim@oneworldexpress.com", 'sql tracking data history', $sql);
		//t($sql, __METHOD__);
		
	  	//echo $sql;

        return TrackingData::getTrackingDataListFromSql($sql);
	}
	
	public function bulkDataUpdate($trackingNumbersArr, $date)
	{
		$trackingNumbersArr = "'" . implode("','",$trackingNumbersArr) . "'";
		$sql = "update consignment set date_scanned = '" . $date . "', date_booked = '" . date("Y-m-d", strtotime($date)) . "' where awb IN(". $trackingNumbersArr .") and date_booked is null and date_scanned = '0000-00-00 00:00:00'";
		
		//echo $sql;
		//die;
	
		t($sql, __METHOD__); 
						 
		return TrackingData::runQuery($sql);				 
				
	}	
	
	public function addFieldNotNull($fieldName) 
	{
        $this->filter .= " AND " . $fieldName . " is not null and $fieldName != ''";
    }
	
	public function bulkDataInsert($rows)
	{
		$sql = array(); 
		
		foreach($rows as $row) 
		{
            $carrier_desc = isset($row['carrier_desc']) ? $row['carrier_desc'] : '';
            $entity_type = isset($row['entity_type']) ? $row['entity_type'] : 'parcel';
            $entity_id = isset($row['entity_id']) ? $row['entity_id'] : $row['parcel_id'];
            $sql[] = "(".DbAccess3::escape($entity_id).",'"
							.DbAccess3::escape($entity_type)."','"
							.DbAccess3::escape(ParseTrackingNumber::Parse($row['tracking_number']))."','"
							.DbAccess3::escape($row['user_id'])."','"
							.DbAccess3::escape($row['track_point'])."','"
							.DbAccess3::escape($row['status_code_id'])."','"
							.DbAccess3::escape($row['date_created'])."','"							
							.DbAccess3::escape($row['ip_address'])."','"
							.DbAccess3::escape($carrier_desc)."','"
							.DbAccess3::escape($row['warehouse_id'])."')" ;

		}
		$sqlQuery =     'INSERT INTO tracking_data (
						 entity_id,
						 entity_type,
						 tracking_number,
						 user_id,
						 track_point, 
						 status_code_id,
						 date_created,
						 ip_address,
						 carrier_desc,
						 warehouse_id
						 ) 
						 VALUES '.implode(',', $sql);
//		echo $sqlQuery;
//		die;				
//		t($sql, __METHOD__); 
		//mysqli_query(self::$connection, $sql);			 
		return TrackingData::runQuery($sqlQuery);				 
	}
	
    public function addTrackPointExistFilter($tracking_numbers, $status_code, $track_point, $carrier_code, $carrier_desc, $date_created='')
	{
        
        $date_filter = "";
        if(trim($date_created) != '')
        {
            $date_filter = " AND date_created = '".$date_created."'";
        }
		$this->filter .= " AND ";		
        $this->filter .= " tracking_number = '" . $tracking_numbers . "' AND status_code_id = '" . DbAccess3::escape($status_code) . "' AND
						   track_point = '" . DbAccess3::escape($track_point) . "' AND carrier_desc = '" . DbAccess3::escape($carrier_desc) . "' AND carrier_code = '" . DbAccess3::escape($carrier_code) . "' " . $date_filter;
						   
		
						   
	}
	
	
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}	
	
	public function addConsignmentIDFilter($consignmentID)
	{
			$this->filter .= " AND ";
        $this->filter .= "t.entity_id='" . DbAccess3::escape($consignmentID). "' AND entity_type = 'shipment'";

	}
	public function addIdFilter ($id)
    {
        $this->filter .= " AND t.id ='" . DbAccess3::escape($id) . "'";
    }
	
	public function addStatusFilter ($status)
    {
        $this->filter .= " AND t.status_code_id ='" . DbAccess3::escape($status) . "'";
    }
	public function addDescriptionFilter ($description)
    {
        $this->filter .= " AND t.description ='" . DbAccess3::escape($description) . "'";
    }
	public function addSorterStatusFilter ($status)
    {
        $this->filter .= " AND t.sorter_status ='" . DbAccess3::escape($status). "'";
    }
	public function addAccountFilter ($account)
    {
        $this->filter .= " AND t.account ='" . DbAccess3::escape($account) . "'";
    }
	
	public function addAccountFilterNotIn ($account)
    {
        $this->filter .= " AND t.account Not In ('" . $account . "')";
    }
	
	public function addAccountFilterIn ($account)
    {
        $this->filter .= " AND t.account In (" . $account . ")";
    }
	
	public function addCartonNumberFilter ($carton_number)
    {
        $this->filter .= " AND t.carton_number ='" . DbAccess3::escape($carton_number) . "'";
    }
	
	public function addTrackPointFilter ($trackPoint)
    {
        $this->filter .= " AND t.track_point = '" . DbAccess3::escape($trackPoint) . "'";
    }
	
	public function addTrackingNumberFilter ($trackingNumber)
    {
        //$this->filter .= " AND t.tracking_number like '%" . $trackingNumber . "%'";
	$this->filter .= " AND t.tracking_number = '" . DbAccess3::escape($trackingNumber) . "'";
    }
	
	public function addParcelIDFilter ($parcel_id)
    {
        //$this->filter .= " AND t.tracking_number like '%" . $trackingNumber . "%'";
        $this->filter .= " AND t.entity_id = '" . DbAccess3::escape($parcel_id) . "' AND entity_type = 'parcel'";
    }
	
	public function addTrackingNumberOrConsignmentIDFilter($trackingNumber)
    {
	$this->filter .= " AND ";
	$this->filter .= "t.tracking_number='" . DbAccess3::escape($trackingNumber). "'";
	/*if(is_numeric($trackingNumber))
	$this->filter .= " or t.consignment_id = '".DbAccess3::escape($trackingNumber)."'";*/
    }
	
	public function DateCreatedFilter($date1)
	{
	    $this->filter .= " AND date_format(t.date_created,'%Y-%m-%d') = '" . $date1. "'";
	}
	public function addDateCreatedFilter($date1, $date2)
	{
	    $this->filter .= " AND t.date_created >= '" . $date1. "' AND t.date_created <= '" .$date2 ."'";
	}
	public function addPodDateFilter($date = NULL)
	{
	    $this->filter .= " AND t.pod_date >= '" . $date. "'";
	}
	
	public function AddOrderByTracking ($ascending = true)
	{
	    $this->order_by = " id " . ($ascending ? "" : " DESC");
	}
	
	public function AddOrderByDate ($ascending = true)
	{
		$this->order_by = " date_created " . ($ascending ? "" : " DESC");
	}
	
	public function AddOrderByID ($ascending = true)
	{
		$this->order_by = " id " . ($ascending ? "" : " DESC");
	}
	
	public function addMawbNumberFilter($mawb)
    {
        $this->filter .= " AND t.mawb = '" . DbAccess3::escape($mawb) . "'";
		
    }
	
	public function addWarehouseIdFilter($warehouseid)
    {
        $this->filter .= " AND t.warehouse_id IN('" .$warehouseid . "')";		
    }		
	
	public function addGroupByMawb()
	{
		$this->group_by = "mawb";
	}
	public function addFilterIn($field, $values) {
            if (trim($this->filter) != "") {
              $this->filter .= " AND ";
            }
            if (is_array($values)) { 
             $this->filter .= $field ." IN ('" . implode("','", $values) . "')";
            } else {
             $this->filter .= $field . " IN ('" . $values . "')";
            }
        }
	public function getAllAccountExcludeOWE()
	{
		$sql = "SELECT distinct account from tracking_data where account not in ('ONEWORLD') ";
        return TrackingData::getTrackingDataListFromSql($sql);
	}

    public function addFilterString($fieldName) {
        $this->filter .= " AND " . $fieldName . " ";
    }
	
	public function getTrackingList($trackingnumber)
	{
		
		//$sql = "SELECT t.tracking_number FROM rumba19.tracking_data t inner join rumba19.consignment c on t.consignment_id = c.id where c.value>=15 and t.mawb = '" . $mawb . "'";
		$sql = "SELECT awb FROM consignment c where value >= 15 and awb in (".$trackingnumber.") ORDER BY awb";

		t($sql, __METHOD__);
		//echo $sql;
		return Consignment::getListFromSql($sql,"awb");
	}
	
	public function getMawbList()
	{
		// has filter been configured?
		$where = "WHERE mawb <> '' ";
		$where .= $this->filter;
		//
		$sql = "SELECT DISTINCT t.mawb FROM rumba19.tracking_data t inner join rumba19.consignment c on t.consignment_id = c.id where c.value>=15";
		//$sql = "SELECT DISTINCT mawb FROM tracking_data t $where ORDER BY mawb";

		t($sql, __METHOD__);
		return TrackingData::getListFromSql($sql, "mawb");
	}
	 public function setFilter($filter) {
        $this->filter = $filter;
    }
	
	public function getProcessedShipment($awbarray, $fromdate, $todate)
	{
		$where = "";
		if(sizeof($awbarray) > 0)
		{
			$awb_array = "'".implode("','",$awbarray) . "'";
			$where = " and t.tracking_number in (".$awb_array.") ";
		}
		if(!empty($fromdate) && !empty($todate))
		{
			$where .= " and date_format(t.date_created, '%Y-%m-%d') >= '".$fromdate."' 
						and date_format(t.date_created, '%Y-%m-%d') <= '".$todate."' ";
		}
		//echo $where;
        $sql = "SELECT  t.tracking_number, t.date_created, t.account, t.description, t.status_code_id, t.track_point
					FROM tracking_data_history t where  t.description = 'Birmingham Sorting Centre' 
					". $where . " group by tracking_number order by t.date_created desc";
	
        return TrackingData::getTrackingDataListFromSql($sql);
	}	
	public function getTotalSorterScannedToday($datefrom, $dateto, $mawb = "",$debug=false)
	{
            $filter = "";
		if($mawb != '')
                    $filter = "   and  c.mawb = '".$mawb."'";
		$sql = "select count(distinct t.tracking_number) as total, s.name as service_name, t.date_created  from tracking_data t
                        inner join parcel p on p.id = t.entity_id 
                        inner join consignment c on c.id = p.consignment_id 
                        inner join services s on s.id = c.service_id
                        where t.track_point = 'Birmingham Sorting Centre - GBR' and 
                        date_format(t.date_created,'%Y-%m-%d') >= '".$datefrom."' "
                        . "and date_format(t.date_created,'%Y-%m-%d') <= '".$dateto."'" . $filter .  " group by c.service_id";
	if($debug)
        {
            echo $sql ;
            die;
        }
        return TrackingData::getTrackingDataListFromSql($sql);
	}	
	
	public function getSuccessScanAtBirmingham()
	{
		
		$sql = "SELECT  distinct t.tracking_number, t.date_created,t.description, t.account FROM tracking_data_history t 
				 WHERE t.tracking_number not in ( select  c.tracking_number from consignment_hold c ) 
				 and t.description ='Birmingham Sorting Centre'  AND date_format(t.date_created,'%Y-%m-%d') = '".date('Y-m-d')."'";
	
        return TrackingData::getTrackingDataListFromSql($sql);
	}	
	public function getSuccessScanCountAtBirmingham()
	{
		
		  $sql = "SELECT  count(distinct t.tracking_number) as tracking_number,date_created FROM tracking_data_history t 
				 WHERE t.tracking_number not in ( select  c.tracking_number from consignment_hold c ) 
				 and t.description ='Birmingham Sorting Centre' AND  date_format(t.date_created,'%Y-%m-%d') = '".date('Y-m-d')."'";
	
        return TrackingData::getTrackingDataListFromSql($sql);
	}	
	public function getIntransportationCheckPoint($track_pointArray, $trackingArray)
	{
		$traArray = "'" . implode("','", $trackingArray) . "'";
		$track_pointArr = "'" . implode("','", $track_pointArray) . "'";
		 $sql = "SELECT tracking_number,track_point
				FROM 
					tracking_data_history
				WHERE
					track_point IN (". $track_pointArr . ") and tracking_number IN (".$traArray.")";
		
		t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
	}
	public function getCountTrackingHub($trackingArray, $description)
	{
		$traArray = "'" . implode("','", $trackingArray) . "'";
		
		$sql = "SELECT tracking_number
				FROM 
					tracking_data_history
				WHERE
					tracking_number IN (".$traArray.") and description = '". DbAccess3::escape($description) ."' limit " . sizeof($trackingArray);
		
		t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
	}
	public function getCountAtHub($trackingArr)
	{
		$traArray = "'" . implode("','", $trackingArr) . "'";
		//echo $traArray;
		$sql = "Select count(*) as parcel_id, t.id, t.description
				from tracking_data_history t
				inner join 
				(
					Select distinct tracking_number, max(id) as id, description
					from tracking_data_history where 
					tracking_number in (
			". $traArray ."
			) group by tracking_number order by id desc
				   
				) t1 
				on t.id = t1.id
				where t.tracking_number in (
			". $traArray ."
			) 
				Group by t.description order by t.id asc
				
			";
//			echo $sql;
			//and t.description != ''
		t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
	}	
	public function getTrackingNumberAtHub($trackingArr, $Hub)
	{
		$traArray = "'" . implode("','", $trackingArr) . "'";
		//echo $traArray;
		$sql = "Select t.tracking_number, t.id, t.description
				from tracking_data_history t
				inner join 
				(
					Select distinct tracking_number, max(id) as id, description
					from tracking_data_history where 
					tracking_number in (
			". $traArray ."
			) group by tracking_number order by id desc
				   
				) t1 
				on t.id = t1.id
				where t.tracking_number in (
			". $traArray ."
			) 
				 and t.description = '".DbAccess3::escape($Hub)."'
				 order by t.id asc
				
			";
			//and t.description != ''
		//echo $sql;
		t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
	}
    public function addJoin($table,$where, $type = "INNER") {
        $this->join .= $type . " JOIN ".$table."  ON " . $where . " ";
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
    public function addFieldFilterColn($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = " . DbAccess3::escape($value) . "";
    }
        public function getParcelListByServiceEndOfDay($warehouseId,$serviceId=0,$userAccountId=0,$groupBy=" s.id",$typeCase='service',$debug = false) {
        $whereService = "";
        $whereAgent = "";
        $whereCarrier = "";
        if($serviceId > 0) {
            if ($typeCase == 'service') {
                $whereService .= " AND s.id = '" . DbAccess3::escape($serviceId) . "' ";
            }else if($typeCase == 'agent'){
                $whereAgent =  " AND ag.id = '" . DbAccess3::escape($serviceId) . "' ";
            }else if($typeCase == 'carrier'){
                $whereCarrier =  " AND car.id = '" . DbAccess3::escape($serviceId) . "' ";
            }
            $groupBy = " td.`entity_id`";
        }
        $sql ="SELECT 
td.entity_id,
    td.tracking_number,
    td.entity_type,
    s.`service_type` AS ip_address,
    s.`name` AS track_point,
    s.`code` AS status_code_id,
    c.`shipment_status` AS warehouse_id,
    s.`id` AS pod_image,
    ag.agent_name AS longitude,
    car.carrier AS latitude,
    ag.`id` AS address_line_1,
    car.`id` AS address_line_2,
    COUNT(s.id) AS signatory
FROM
		consignment c 
    INNER JOIN
		parcel p ON c.id = p.`consignment_id`
    INNER JOIN 
		services s ON s.`id` = c.`service_id` $whereService
    INNER JOIN `carrier` car
        ON car.`id` = s.`carrier_id` $whereCarrier
    INNER JOIN `agent_data` ag
        ON ag.`id` = c.`agent_id`  $whereAgent
	INNER JOIN 
		tracking_data td    ON p.id = td.`entity_id` AND td.entity_type = 'parcel' AND  td.`status_code_id` = '146' AND td.`warehouse_id` = '".DbAccess3::escape($warehouseId)."' 
        AND td.`user_id` IN (SELECT 
            id
        FROM
            `user`
        WHERE
            user_account_id = '$userAccountId'
                AND `active_flag` = 1
                AND `warehouse_id` = '".DbAccess3::escape($warehouseId)."'
                AND `is_deleted` = 0)
		AND p.id not in (
SELECT 
    entity_id
FROM
    manifest m
        INNER JOIN
    user u ON m.user_id = u.id
        AND u.user_account_id = '$userAccountId'
        AND u.`warehouse_id` = '".DbAccess3::escape($warehouseId)."'
        INNER JOIN
    manifest_entity_mapping me ON m.id = me.manifest_id
GROUP BY entity_id)
WHERE s.`service_type` != 'DO'
GROUP BY $groupBy";
            //echo $sql; die;
        if($debug)
        {
            echo $sql;
            die;
        }
        return TrackingData::getTrackingDataListFromSql($sql);
    }
    public function getParcelListByService($warehouseId,$serviceId=0,$userAccountId=0,$debug = false) {
        $where = "";
        $groupBy = " s.id";        
        if($serviceId > 0){
            $where .= " AND s.id = '".DbAccess3::escape($serviceId)."' ";
            $groupBy = " td.`entity_id`";
        }
        
        
        $sql = "SELECT
                td.entity_id,
                td.tracking_number,
                td.entity_type,
                s.`service_type` AS ip_address,
                s.`name` AS track_point,
                s.`code` AS status_code_id,
                c.`shipment_status` AS warehouse_id,
                s.`id` AS pod_image,
                COUNT(s.id) AS signatory
              FROM
                tracking_data td 
                JOIN parcel p 
                  ON p.id = td.`entity_id`   
                JOIN consignment c 
                  ON c.id = p.`consignment_id` 
                JOIN services s 
                  ON s.`id` = c.`service_id`
                LEFT JOIN (SELECT mem.* FROM `manifest` mn 
                            JOIN `manifest_entity_mapping` mem 
                              ON mem.`manifest_id` = mn.`id` 
                            WHERE mn.`manifest_by` = 'operation' AND mn.user_id not in (select id from user where user_account_id = '$userAccountId')) AS mem
                  ON mem.`entity_id` = td.`entity_id`
              WHERE 
                td.`status_code_id` = '146'
                AND td.`warehouse_id` = '".DbAccess3::escape($warehouseId)."'
                AND mem.id IS NULL
                AND td.`user_id` IN (SELECT id FROM `user` WHERE user_account_id = '".DbAccess3::escape($userAccountId)."' AND `active_flag` = 1 AND `is_deleted` = 0)
                AND td.entity_type = 'parcel' " .$where ."
              GROUP BY ".$groupBy;
        if($debug)
        {
            echo $sql;
            die;
        }
        return TrackingData::getTrackingDataListFromSql($sql);
    }
//    public function getParcelListByService($warehouseId, $serviceId) {
//        $sql = "SELECT
//                td.entity_id,
//                td.entity_type,
//                td.tracking_number,
//                s.`service_type` AS ip_address,
//                s.`name` AS track_point,
//                s.`id` AS pod_image
//              FROM
//                tracking_data td
//                JOIN parcel p
//                  ON p.id = td.`entity_id`
//                LEFT OUTER JOIN `manifest_entity_mapping` mem
//                ON mem.`entity_id` = p.`id`
//                JOIN consignment c
//                  ON c.id = p.`consignment_id`
//                JOIN services s
//                  ON s.`id` = c.`service_id`
//              #WHERE DATE(td.date_created) >= ADDDATE(CURRENT_DATE, INTERVAL -1 MONTH) AND
//              WHERE  td.`status_code_id` = 146 AND td.`warehouse_id` = '".DbAccess3::escape($warehouseId)."' AND s.id = '".DbAccess3::escape($serviceId)."' AND mem.id IS NULL
//              GROUP BY td.`entity_id`, td.`entity_type`";
//        t($sql, __METHOD__);
//        return TrackingData::getTrackingDataListFromSql($sql);
//    }
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
    public function getDailyTrackingDataReport($fromDate='',$toDate='',$allouedAcccounts,$search_Carrier_id = 0,$search_Code = 0,$country = 0,$userAccountId='',$trackingNum = '',$showReport='',$onlyShowFilter = "",$report_type="") {
        $limit = '';
        $where = '';
        $having = '';
        //var_dump($onlyCarrierReceived); exit;
        if($onlyShowFilter == "carrier_received"){
            $having = " HAVING FIND_IN_SET('148', group_status_code)";
        }else if($onlyShowFilter == "hub_received"){
            $having = " HAVING FIND_IN_SET('146', group_status_code)";
        }
        $whereTo = '';
//        if(!empty($fromDate) && !empty($toDate)){
//            $where = ' DATE(t.date_created) >= "'.$fromDate.'" AND DATE(t.date_created) <= "'.$toDate.'"';
//        }
        if(!empty($fromDate) && !empty($toDate)){
            $where = ' DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.$fromDate.'" AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") <= "'.$toDate.'"';
        }else if(!empty($fromDate)){
            $where = ' DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.$fromDate.'"';
        }else{
            $where = ' DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.date("Y-m-d").'"';
        }
        $joinService = ' s.`id` = c.`service_id`';
        if(!empty($search_Code)){
            $serviceObj = new Services($search_Code);
            if($serviceObj->getIsCustomized() == 1){
                $joinService = ' s.`id` = c.`customized_service_id`';
                if(!empty($where))
                    $where .=" AND c.`customized_service_id` = '".$search_Code."'";
                else
                    $where .=" c.`customized_service_id` = '".$search_Code."'";
            }else{
                if(!empty($where))
                    $where .=" AND c.`service_id` = '".$search_Code."'";
                else
                    $where .=" c.`service_id` = '".$search_Code."'";
            }

        }
        if(!empty($where)){
            $where .=" AND u.`user_account_id` IN ('".implode("','", $allouedAcccounts)."')";
        }else{
            $where .="  u.`user_account_id` IN ('".implode("','", $allouedAcccounts)."')";
        }

        if(!empty($country)){
            if(!empty($where))
                $where .=" AND c.`country_id` = '".$country."'";
            else
                $where .="  c.`country_id` = '".$country."'";

        }
        if(!empty($search_Carrier_id)){
            if(!empty($where))
                $where .=" AND s.carrier_id = '".$search_Carrier_id."'";
            else
                $where .=" s.carrier_id = '".$search_Carrier_id."'";

        }

        if(!empty($trackingNum)){
            $trackingNumberArr = explode(",", $trackingNum);
            $strTrackingNumber = "'".implode("','",$trackingNumberArr)."'";
            if(!empty($where))
                $where .=" AND t.tracking_number IN (".$strTrackingNumber.")";
            else
                $where .="  t.tracking_number IN (".$strTrackingNumber.")";
        }

        if(!empty($this->rowsPerPage)){
            $limit = 'LIMIT '. $this->pageOffset . ',' .$this->rowsPerPage;
        }
        
        if($report_type!="shipment_status"){
        if(empty($toDate) && empty($fromDate) && empty($userAccountId) && empty($search_Carrier_id) && empty($country) && empty($search_Code) && empty($trackingNum)){
            if(!empty($where))
                $where .= ' AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") = "'.date("Y-m-d").'" ';
            else
                $where .= '  DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") = "'.date("Y-m-d").'"';
        }
        }

        $sql = "SELECT
               SQL_CALC_FOUND_ROWS  
               p.`id`,
               p.`consignment_id`,
               p.`weight`,
               c.`hawb`,
               t.`tracking_number`,
               c.date_scanned,
               c.date_booked,
               GROUP_CONCAT(t.`tracking_number` ORDER BY t.`date_created` DESC) AS group_tracking_number,
               GROUP_CONCAT(DATE(t.`date_created`) ORDER BY t.`date_created` DESC) AS group_date_created,
               GROUP_CONCAT(t.`status_code_id` ORDER BY t.`date_created` DESC) AS group_status_code,
               GROUP_CONCAT(
                t.`date_created` 
                ORDER BY t.`date_created` DESC
                ) AS group_last_tracking_date,
                GROUP_CONCAT(
                    t.`carrier_desc`
                    ORDER BY t.`date_created` DESC
                  ) AS group_carrier_desc,               
               t.`date_created`,
               p.`length`,
               p.`width`,
               p.`height`,
               s.`name`,
               c.`date_label_created`,
               c.`postcode`,
               coun.name AS warehouse_id,
               c.city AS ip_address,
               a.`user_account`,
               tt.`transit_time`,
               t.carrier_desc,
               t.`carrier_code`,
               t.`status_code_id`,
               DATE(t.`date_created`),
               m.mawb_number
             FROM
               tracking_data t 
               INNER JOIN parcel p 
                 ON p.`id` = t.`entity_id` 
               INNER JOIN `consignment` c 
                 ON c.`id` = p.`consignment_id` 
                 AND c.shipment_status NOT IN ('12', '11', '22') 
               INNER JOIN `services` s 
                 ON $joinService
               INNER JOIN `user` u 
                 ON u.`id` = c.`user_id` 
               INNER JOIN `user_account` a 
                 ON a.id = u.`user_account_id` 
               INNER JOIN `service_country_ttime` tt 
                 ON tt.`id_country` = c.`country_id`
                 AND tt.`id_service` = c.`service_id` 
               INNER JOIN country coun ON coun.id = c.country_id
               LEFT JOIN `mawb_parcel_mapping` mpm
                ON p.id = mpm.`parcel_id`
               LEFT JOIN `mawb` m
                ON m.`id` = mpm.`mawb_id`
                WHERE   
                 $where
             GROUP BY t.`tracking_number` $having $limit";
      
        return TrackingData::getTrackingDataListFromSql($sql);
    }
    public function getDailyTrackingDataCountReport2(){
        $sql = "SELECT FOUND_ROWS() AS id";
        return TrackingData::getTrackingDataListFromSql($sql);
    }
    
    public function getDailyTrackingDataCountReport($fromDate='',$toDate='',$allouedAcccounts,$search_Carrier_id = 0,$search_Code = 0,$country = 0,$userAccountId='',$trackingNum = '',$showReport='') {
        
        $where = '';
        $whereTo = '';
        if(!empty($fromDate) && !empty($toDate)){            
            $where = ' DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.$fromDate.'" AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") <= "'.$toDate.'"';
        }
        if(!empty($search_Code)){
            $serviceObj = new Services($search_Code);
            if($serviceObj->getIsCustomized() == 1){
                 $joinService = ' s.`id` = c.`customized_service_id`';
                if(!empty($where))
                 $where .=" AND c.`customized_service_id` = '".$search_Code."'";
                else
                 $where .=" c.`customized_service_id` = '".$search_Code."'";
            }else{
                if(!empty($where))
                 $where .=" AND c.`service_id` = '".$search_Code."'";
                else
                 $where .=" c.`service_id` = '".$search_Code."'";
            }

        }        
        if(!empty($where)){
            $where .=" AND u.`user_account_id` IN ('".implode("','", $allouedAcccounts)."')";
        }else{
            $where .="  u.`user_account_id` IN ('".implode("','", $allouedAcccounts)."')";
        }
        if(!empty($country)){
            if(!empty($where))
                 $where .=" AND c.`country_id` = '".$country."'";
            else
                 $where .="  c.`country_id` = '".$country."'";

        }
        if(!empty($search_Carrier_id)){
            if(!empty($where))
                 $where .=" AND s.carrier_id = '".$search_Carrier_id."'";
            else
                 $where .=" s.carrier_id = '".$search_Carrier_id."'";

        }
       
        if(!empty($trackingNum)){
             $trackingNumberArr = explode(",", $trackingNum);
             $strTrackingNumber = "'".implode("','",$trackingNumberArr)."'";
             if(!empty($where))
                  $where .=" AND t.tracking_number IN (".$strTrackingNumber.")";
             else
                  $where .="  t.tracking_number IN (".$strTrackingNumber.")";
        }

        if(empty($toDate) && empty($fromDate) && empty($userAccountId) && empty($search_Carrier_id) && empty($country) && empty($search_Code) && empty($trackingNum)){
            if(!empty($where))
                $where .= ' AND  DATE(t.`date_created`) = "'.date('Y-m-d').'"';
            else
                $where .= '   DATE(t.`date_created`) = "'.date('Y-m-d').'"'; 
        }
         $sql = "SELECT 
                    COUNT(id) AS id 
                FROM(SELECT 
                    p.`id`
                  FROM
                    tracking_data t 
                    INNER JOIN parcel p 
                      ON p.`id` = t.`entity_id` 
                    INNER JOIN `consignment` c 
                      ON c.`id` = p.`consignment_id` 
                      AND c.shipment_status NOT IN ('12', '11', '22') 
                    INNER JOIN `services` s 
                      ON $joinService
                    INNER JOIN `user` u 
                      ON u.`id` = c.`user_id` 
                    INNER JOIN `user_account` a 
                      ON a.id = u.`user_account_id` 
                    INNER JOIN `service_country_ttime` tt 
                      ON tt.`id_country` = c.`country_id`
                      AND tt.`id_service` = c.`service_id` 
                     INNER JOIN country coun ON coun.id = c.country_id
                     WHERE   
                      $where
                  GROUP BY t.`tracking_number`) as asd" ;
        // echo $sql;exit;
       return TrackingData::getTrackingDataListFromSql($sql); 

    }

    public function getCarrierServicePerformanceReport($fromDate = '',$toDate = '',$search_Carrier_id = 0,$service_id = [],$country = 0,$user_account_id = '',$totalCount = '',$no = '') {
        $limit = '';
        $where = '';
        $innerJoinUser = '';
        if(!empty($search_Carrier_id)){
            $where .="  s.carrier_id IN (".$search_Carrier_id.")";
        }
        if(!empty($user_account_id)){
            $innerJoinUser = '  INNER JOIN `user` u 
                 ON u.`id` = c.`user_id` 
               INNER JOIN `user_account` a 
                 ON a.id = u.`user_account_id` ';
            if(!empty($where))
                $where .="  AND u.`user_account_id` IN (".implode(',',$user_account_id).")";
            else
                $where .="    u.`user_account_id` IN (".implode(',',$user_account_id).")";
        }
        if(!empty($fromDate) && !empty($toDate)){
            if(!empty($where)) {
                $where .= 'AND  DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.$fromDate.'" AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") <= "'.$toDate.'"';
            } else {
                $where .= ' DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.$fromDate.'" AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") <= "'.$toDate.'"';
            }
        }
        if(!empty($service_id)){
            if(!empty($where)) {
                $where .=" AND c.`service_id` IN (".implode(',',$service_id).")";
            } else {
                $where .="  c.`service_id` IN (".implode(',',$service_id).")";
            }
        }
        if(!empty($this->rowsPerPage)){
           $limit = 'LIMIT '. $this->pageOffset . ',' .$this->rowsPerPage;
        }
        if(!empty($country)){
            if(!empty($where)) {
                $where .=" AND co.`id` = '".$country."'";
            } else {
               $where .=" co.`id` = '".$country."'"; 
            }
        }
        if(empty($country) && empty($fromDate) && empty($toDate)){
            if(!empty($where)) {
                $where .= ' AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.date('Y-m-d', strtotime("-1 month", strtotime(date('Y-m-d')))).'" AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") <= "'.date("Y-m-d").'"';
            } else {
                $where .= '  DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") >= "'.date('Y-m-d', strtotime("-1 month", strtotime(date('Y-m-d')))).'" AND DATE_FORMAT(FROM_UNIXTIME(c.date_label_created),"%Y-%m-%d") <= "'.date("Y-m-d").'"';
            }
        }
        $wheres = ' WHERE ';
        if(empty($where)){
            $wheres = '';
        }

            $sql = "SELECT 
                        * 
                      FROM
                        (
                    (SELECT 
                    co.`name` AS countryname,
                    s.`name`,
                    tt.`transit_time`,
                    s.`id` AS serviceid,
                    p.`id`,
                    GROUP_CONCAT(
                      t.`tracking_number` 
                      ORDER BY t.`date_created` DESC
                    ) AS group_tracking_number,
                    GROUP_CONCAT(
                      DATE(t.`date_created`) 
                      ORDER BY t.`date_created` DESC
                    ) AS group_date_created,
                    GROUP_CONCAT(
                      t.`status_code_id` 
                      ORDER BY t.`date_created` DESC
                    ) AS group_status_code,
                    DATE(t.`date_created`) AS date_created
                  FROM
                    tracking_data t 
                    INNER JOIN parcel p 
                      ON p.`id` = t.`entity_id` 
                    INNER JOIN `consignment` c 
                      ON c.`id` = p.`consignment_id` 
                    INNER JOIN `services` s 
                      ON s.`id` = c.`service_id`
                    $innerJoinUser
                    INNER JOIN `service_country_ttime` tt 
                      ON tt.`id_country` = c.`country_id` 
                      AND tt.`id_service` = c.`service_id` 
                    INNER JOIN country co 
                        ON co.`id` = tt.`id_country` 
                $wheres 
                $where
            GROUP BY t.`tracking_number`,tt.`id_country` )
            UNION
            (
            SELECT 
                    co.`name` AS countryname,
                    s.`name`,
                    tt.`transit_time`,
                    s.`id` AS serviceid,
                    p.`id`,
                    GROUP_CONCAT(
                      t.`tracking_number` 
                      ORDER BY t.`date_created` DESC
                    ) AS group_tracking_number,
                    GROUP_CONCAT(
                      DATE(t.`date_created`) 
                      ORDER BY t.`date_created` DESC
                    ) AS group_date_created,
                    GROUP_CONCAT(
                      t.`status_code_id` 
                      ORDER BY t.`date_created` DESC
                    ) AS group_status_code,
                    DATE(t.`date_created`) AS date_created
                FROM
                    tracking_data t 
                    INNER JOIN parcel p 
                      ON p.`id` = t.`entity_id` 
                    INNER JOIN `consignment` c 
                      ON c.`id` = p.`consignment_id` 
                    INNER JOIN `services` s 
                      ON s.`id` = c.`customized_service_id`
                    $innerJoinUser
                    INNER JOIN `service_country_ttime` tt 
                      ON tt.`id_country` = c.`country_id` 
                      AND tt.`id_service` = c.`customized_service_id` 
                    INNER JOIN country co 
                        ON co.`id` = tt.`id_country` 
                 $wheres". 
                 str_replace("c.`service_id` IN","c.`customized_service_id` IN",$where)."
            GROUP BY t.`tracking_number`,tt.`id_country`
            ) ) AS temp
            $limit";
//            echo $sql; exit;
        return TrackingData::getTrackingDataListFromSql($sql); 
    }
   
    public function getReportScannedGroupByService($warehouseId,$userAccountId,$date="",$op=""){
        $where = "";
	    if(!empty($date) && !empty($op)){
		    $where = " AND DATE(td.date_created) ".$op." '".$date."'";
	    }
	    $sql = "SELECT
                        s.`name` AS carrier_desc,COUNT(s.id) AS entity_id
                      FROM
                        tracking_data td 
                        JOIN parcel p 
                            ON p.id = td.`entity_id` 
                        JOIN consignment c 
                            ON c.id = p.`consignment_id` 
                        JOIN services s 
                            ON s.`id` = c.`service_id`
                        JOIN `user` u 
                          ON u.`id` = td.`user_id`
                      WHERE  td.warehouse_id = '".DbAccess3::escape($warehouseId)."' AND td.`status_code_id` = '146' AND  u.`user_account_id` = '".DbAccess3::escape($userAccountId)."' $where
                        GROUP BY s.id ";
        t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
        
    }
    public function getParcelIdCountMawbReport($parcelId = ''){
        
        $sql = " SELECT COUNT(DISTINCT entity_id) as id FROM tracking_data WHERE entity_id IN ($parcelId)";
        //echo $sql;exit;
        t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
        
    }
    public function getPrcelDataFromTracking($tracingNumber) {
        $sql = '';
    }
    
    public function getDashboardCSDelivered($userAccountId=0,$wareHouseId=0,$userId=0,$to='',$from='',$join='',$column='',$groupBy=''){
        $where = [];
        if(!empty($userAccountId)){
            $where[] = "    
                        u.`user_account_id` IN (".implode(',',$userAccountId).")";
        }
        if(!empty($wareHouseId)){
            $where[] = "    
                        td.`warehouse_id` = '$wareHouseId' ";
        }
        if(!empty($userId)){
            $where[] = "    
                        u.`id` = '$userId' ";
        }
        if(!empty($from)){
            $where[] = "    
                       DATE(td.date_created) <=  '$from'  ";
           
        }
        if(!empty($to)){
            if(empty($from)){
               $where[] = "    
                            DATE(td.date_created) = '$to'  "; 
            }else{
                $where[] = "    
                            DATE(td.date_created) >= '$to'  ";
            }
        }
        
        $whereImplode = '';
        if(!empty($where)){
            $where[] = 'WHERE ';
            $where = array_reverse($where);
            $whereImplode = implode(' AND ', $where);
            $whereImplode = substr_replace($whereImplode,' ',7,10);
        }
        $sql = "SELECT 
                    p.id,  
                    tt.`id_country`,
                    tt.`id_service`,
                    tt.`transit_time`,
                    GROUP_CONCAT(DATE(td.`date_created`) ORDER BY td.`date_created` DESC) AS group_date_created,
                    GROUP_CONCAT(td.`status_code_id` ORDER BY td.`date_created` DESC) AS group_status_code
                    $column
                FROM
                    `tracking_data` td 
                    JOIN parcel p 
                      ON p.`id` = td.`entity_id` 
                    INNER JOIN `consignment` c 
                      ON c.`id` = p.`consignment_id` 
                    INNER JOIN `user` u 
                      ON u.`id` = c.`user_id` 
                    INNER JOIN `service_country_ttime` tt
                      ON tt.`id_country` = c.`country_id` 
                      AND tt.`id_service` = c.`service_id`
                      $join
                    $whereImplode   
                    GROUP BY td.`tracking_number`  $groupBy ";
            //echo $sql;exit;
        t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
        
    }
    public function deleteFromTrackingDataIn($parcelIdArr) {
        $sql = "DELETE FROM `tracking_data` WHERE `entity_id` IN ('" . implode("','", $parcelIdArr) . "') AND entity_type ='parcel'";
        DbAccess3::runQuery($sql);
    }
    
    public function getCSTopService($userAccountId=0,$wareHouseId=0,$userId=0,$date=''){
        $where = [];
        if(!empty($userAccountId)){
            $where[] = "    
                        u.`user_account_id` IN ($userAccountId)";
        }
        if(!empty($wareHouseId)){
            $where[] = "    
                        td.`warehouse_id` = '$wareHouseId' ";
        }
        if(!empty($userId)){
            $where[] = "    
                        u.`id` = '$userId' ";
        }
        if(!empty($date)){
            $where[] = "    
                       DATE(td.date_created)  $date  ";
        }
        $whereImplode = '';
        if(!empty($where)){
            $where[] = 'WHERE ';
            $where = array_reverse($where);
            $whereImplode = implode(' AND ', $where);
            $whereImplode = substr_replace($whereImplode,' ',7,10);
        }
        $sql = "SELECT 
                    p.id,
                    tt.`id_country`,
                    tt.`id_service`,
                    tt.`transit_time`,
                    GROUP_CONCAT(
                      DATE(td.`date_created`) 
                      ORDER BY td.`date_created` DESC
                    ) AS group_date_created,
                    GROUP_CONCAT(ser.`name`) AS signatory,
                    GROUP_CONCAT(td.`tracking_number`) AS tracking_number,
                    GROUP_CONCAT(
                      td.`status_code_id` 
                      ORDER BY td.`date_created` DESC
                    ) AS group_status_code 
                  FROM
                    `tracking_data` td 
                    JOIN parcel p 
                      ON p.`id` = td.`entity_id` 
                    INNER JOIN `consignment` c 
                      ON c.`id` = p.`consignment_id` 
                    INNER JOIN `services` ser 
                      ON ser.id = c.`service_id` 
                    INNER JOIN `user` u 
                      ON u.`id` = c.`user_id` 
                    INNER JOIN `service_country_ttime` tt 
                      ON tt.`id_country` = c.`country_id` 
                      AND tt.`id_service` = c.`service_id` 
                      $whereImplode
                  GROUP BY ser.id,
                    td.`tracking_number`";
        //  echo $sql;exit;
        t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
        
    }
    
    public static function getCSStatusCOde($parcelId=0){
        $where = [];
        $sql = "SELECT 
                DATE(td.`date_created`) AS date_create_track
              FROM
                tracking_data td 
              WHERE td.`entity_id` = '$parcelId' 
                AND td.`status_code_id` = '146' ";
        t($sql, __METHOD__);
        return TrackingData::getTrackingDataListFromSql($sql);
        
    }
    public static function updateParcelColumnByParcelId($parcelId,$oldTrackingNumber,$newTrackingNumber,$entityType="parcel") {
        if($parcelId > 0 && !empty($newTrackingNumber)){
            $sql = "UPDATE `tracking_data` td SET td.tracking_number = '".DbAccess3::escape($newTrackingNumber)."' WHERE td.`tracking_number` = '".DbAccess3::escape($oldTrackingNumber)."' AND entity_type ='".DbAccess3::escape($entityType)."' AND entity_id='".DbAccess3::escape($parcelId)."' ";
            DbAccess3::runQuery($sql);
        }
    }

    public static function getTrackingDataForApi($trackingNumber, $limit = 0) {
        if(!empty($trackingNumber)){
            $limitClouse = '';
            if(!empty($limit)) {
                $limitClouse = 'LIMIT ' . $limit;
            }
            $sql = "SELECT * FROM `tracking_data` AS td WHERE td.tracking_number = '".DbAccess3::escape($trackingNumber)."' ORDER BY id DESC " . $limitClouse;
            return TrackingData::getTrackingDataListFromSql($sql);
        } else {
            return false;
        }
    }
    
     public function setLimit($limit)
    {
            $this->limit = $limit;
    }
    
    
    public static function getTrackingPointsDescriptonByServiceId($service_id, $debug = false){
        $sql = "SELECT 
                    tracking_data.*,     CASE 
                                WHEN carrier_desc LIKE '%delivered%' THEN 'delivered'  
                        WHEN carrier_desc LIKE '%collected%' THEN 'delivered' 
                        WHEN carrier_desc LIKE '%collect%' THEN 'delivered' 
                        ELSE carrier_desc 
                        END   as 'carrier_desc'
                FROM
                    tracking_data
                        INNER JOIN
                    parcel ON tracking_data.entity_id = parcel.id AND trim(tracking_data.carrier_desc) <> ''
                                INNER JOIN 
                        consignment ON consignment.id = parcel.consignment_id and consignment.service_id = '".$service_id."'
                GROUP BY CASE 
                        WHEN carrier_desc LIKE '%delivered%' THEN 'delivered'  
                        WHEN carrier_desc LIKE '%collected%' THEN 'delivered' 
                         WHEN carrier_desc LIKE '%collect%' THEN 'delivered' 
                        ELSE carrier_desc 
                        END 
                ORDER BY id ASC ";
        if($debug){
            echo $sql ; 
            die;
        }
        return TrackingData::getTrackingDataListFromSql($sql);
    }
    
}