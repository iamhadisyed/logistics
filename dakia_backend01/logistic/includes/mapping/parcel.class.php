<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class Parcel extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
                                'id'    => 'number',
                                'consignment_id' => 'number',
                                'tracking_number' => 'string',
                                'do_tracking_number' => 'string',
                                'length' => 'number',
                                'width' => 'number',
                                'height' => 'number',
                                'weight' => 'number',
                                'description'=>'string',
                                'parcel_message'=>'string',
                                'qty'=>'string',
                                'commoditycode'=>'string',
                                'hscode'=>'string',
                                'grossweight'=>'number',
                                'pweight'=>'string',
                                'itemvalue'=>'string',
                                'number_item'=>'number',
                                'tarrif_no'=>'string',
                                'update_weight'=>'number',
                                'owe_status_code'=>'string',
                                'chute_sorted'=>'number',
                                'parcel_status_code' => 'number',
                                'routing_code' => 'string',
                                'last_tracking_update' => 'datetime',
                                'parcel_label' => 'string',
                                'itemsku' => 'string',
                                'itemurl' => 'string',
                                'sort_type' => 'string',
								'courier_status' => 'undefined',
                                'pieces' => 'undefined',
                                'mawb_number' => 'undefined',
                                'actualweight' => 'undefined',
                                'bagnumber' => 'undefined',
                                'name' => 'undefined',
                                'countryname' => 'undefined',
                                'user_account' => 'undefined',
                                'group_scan_date' => 'undefined',
                                'group_parcel_id' => 'undefined',
                                'dims' => 'undefined',
                                'first_name' => 'undefined',
                                'carrier' => 'undefined',
                                'palletno' => 'undefined',
                                'warehouse_id' => 'undefined',
                                'parcel_type' => 'undefined',
                                'date_added' => 'undefined',
                                'parcel_service_id' => 'undefined',
                                'parcel_service_name' => 'undefined',
                                'parcel_service_code' => 'undefined',
                                'parcel_service_carrier_code' => 'undefined',
                                'consignment_country_id' => 'undefined',
                                'consignment_country_iso' => 'undefined',
                                'consignment_awb' => 'undefined',
                                'consignment_hawb' => 'undefined',
                                'message'   => 'undefined'
                                );

		//
		parent::__construct("parcel", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}


	/**
	 * Get list of Consignmnet Piece objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getParcelListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}

	public function bulkUpdate($setColumns, $where)
	{
		//$awbArrayStr = "'" . implode("','",$awbArray) . "'";
		if(trim($setColumns) != '' && trim($where) != '') {
			$sql = "UPDATE parcel SET $setColumns WHERE $where";
            //echo $sql;
        }
		//t($sql, __METHOD__); 
		
		return DbAccess3::runQuery($sql);				 		 
		//DbAccess3::runQuery($sql);				 
	}

    public function getWeight()
    {
        return $this->valArray["weight"];
    }
    public static function setParcelStatus($parcelId,$statusCode) {
        if((int)$parcelId > 0 && (int)$statusCode > 0){
            $str = "";
            if(isset(Consignment::$database_status_array[$statusCode])){
                $str = ", owe_status_code = '".Consignment::$database_status_array[$statusCode]."'";
            }
            $sql = "UPDATE `parcel` SET parcel_status_code = '".DbAccess3::escape($statusCode)."' ".$str." WHERE id = '".DbAccess3::escape($parcelId)."'";
            DbAccess3::runQuery($sql);
        }
    }
    public static function deleteByConsignmentId($consignmentId) {
        if($consignmentId != "" && $consignmentId > 0)
            self::runQuery("DELETE FROM `parcel` WHERE consignment_id = '".DbAccess3::escape($consignmentId)."'");
    }
    public static function updateParcelStatusByConId($consignmentId,$statusCode) {
        if($consignmentId > 0){
            $sql = "UPDATE `parcel` SET parcel_status_code = '".DbAccess3::escape($statusCode)."' , owe_status_code = '".Consignment::$database_status_array[$statusCode]."' WHERE consignment_id = '".DbAccess3::escape($consignmentId)."'";
            DbAccess3::runQuery($sql);
        }
    }
    public static function emptyParcelTrackingByConId($consignmentId,$statusCode=Consignment::STATUS_READY_TO_PRINT) {
        if($consignmentId > 0){
            $sql = "UPDATE `parcel` SET parcel_status_code = '".DbAccess3::escape($statusCode)."' ,tracking_number = NULL , owe_status_code = '".Consignment::$database_status_array[$statusCode]."' WHERE consignment_id = '".DbAccess3::escape($consignmentId)."'";
            DbAccess3::runQuery($sql);
        }
    }
	public static function getTotalNumberOfParcelFromSql($sql) {
            $rs = DbAccess3::runQuery($sql);
            $data = mysqli_fetch_assoc($rs);
            return $data['total'];
        }
    public static function getParcelWiseItemArray($consignment_id,$parcel_id="") {
        $itemArr = [];
        if($consignment_id > 0){
            $sqlQuery = '';
            if(!empty($parcel_id) && $parcel_id > 0){
                $sqlQuery = "  AND p.id ='".DbAccess3::escape($parcel_id)."'";
            }
            $sql = "SELECT * FROM `parcel` p WHERE p.`consignment_id` =  '".DbAccess3::escape($consignment_id)."' ".$sqlQuery;
            $rtnRuery = DbAccess3::runQuery($sql);
            if ($rtnRuery->num_rows > 0) {
                while($row = $rtnRuery->fetch_assoc()) {
                    if(!empty($row['description'])){
                        $description = json_decode($row['description'],true);
                        $qty = json_decode($row['qty'],true);
                        $commoditycode = json_decode($row['commoditycode'],true);
                        $hscode = json_decode($row['hscode'],true);
                        $pWeight = json_decode($row['pweight'],true);
                        $itemValue = json_decode($row['itemvalue'],true);
                        $tarrifNo = json_decode($row['tarrif_no'],true);
                        $itemsku = json_decode($row['itemsku'],true);
                        $itemurl = json_decode($row['itemurl'],true);
                        foreach ($description as $index => $despArr) {
                            $itemDet[] = [
                                            "description"=>$despArr,
                                            "qty"=>$qty[$index],
                                            "commoditycode"=>$commoditycode[$index],
                                            "hscode"=>$hscode[$index],
                                            "pweight"=>$pWeight[$index],
                                            "itemvalue"=>$itemValue[$index],
                                            "tarrif_no"=>$tarrifNo[$index],
                                            "itemsku"=>$itemsku[$index],
                                            "itemurl"=>$itemurl[$index]
                                        ];
                            $itemArr[$row['id']] = $itemDet;
                        }
                    }
                }
            }
        }
        return $itemArr;
    }
}
