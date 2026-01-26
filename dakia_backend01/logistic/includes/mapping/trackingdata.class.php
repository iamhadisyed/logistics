<?php
/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */

class TrackingData extends DbAccess3
{	/**
 * Construct
 *
 * @param id/array
 */
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id'	=> 'number',
            'entity_id' => 'number',
            'entity_type' => 'string',
            'tracking_number' => 'string',
            'user_id' => 'string',
            'track_point' => 'string',
            'date_created' => 'string',
            'ip_address'=>'string',
            'status_code_id'=>'number',
            'warehouse_id'=>'number',
            'pod_image' => 'string',
            'carrier_code' => 'string',
            'carrier_desc' => 'string',
            'signatory' => 'string',
            'parcel_image' => 'string',
            'latitude' => 'string',
            'longitude' => 'string',
            'date_label_created' => 'undefined',
            'user_account' => 'undefined',
            'hawb' => 'undefined',
            'name' => 'undefined',
            'hawb' => 'undefined',
            'postcode' => 'undefined',
            'weight' => 'undefined',
            'length' => 'undefined',
            'width' => 'undefined',
            'height' => 'undefined',
            'transit_time' => 'undefined',
            'address_line_1' => 'undefined',
            'address_line_2' => 'undefined',
            'address_line_3' => 'undefined',
            'group_date_created' => 'undefined',
            'group_tracking_number' => 'undefined',
            'group_status_code' => 'undefined',
            'group_carrier_desc' => 'undefined',
            'group_last_tracking_date' => 'undefined',
            'r_menifest_id' => 'undefined',
            'r_menifest_weight' => 'undefined',
            'serviceid' => 'undefined',
            'date_create_track' => 'undefined',
            'countryname' =>'undefined',
            'reference' =>'undefined',
            'description' =>'undefined',
            'contact' =>'undefined',
            'city' =>'undefined',
            'number_pieces' =>'undefined',
            'vol_weight' =>'undefined',
            'total' =>'undefined',
            'date_added' =>'undefined',
            'service_name' => 'undefined',
            'service_code' => 'undefined',
            'user_account' => 'undefined',
            'first_name' => 'undefined',
            'last_name' => 'undefined',
            'agent_name' => 'undefined',
            'shipment_user_account' => 'undefined',
            'shipment_user_account_id' => 'undefined',
            'date_booked' => 'undefined',
            'date_label_created' => 'undefined',
            'mawb_number' => 'undefined',
            'date_scanned' => 'undefined',
            'data_send' => 'number'

        );
        parent::__construct("tracking_data", 'id', $fieldList, $mixedCreator);
    }

    public static function getTrackingDataListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getListFromSql($sql, $val_field, $key_field = "")
    {
        $list = array();
        $rs = DbAccess3::runQuery($sql);
        //
        while ($row = mysqli_fetch_assoc($rs))
        {
            if ($key_field == "")
            {
                $list[] = $row[$val_field];
            }
            else
            {
                $list[$row[$key_field]] = $row[$val_field];
            }
        }
        return $list;
    }

    public function getId() {
        return $this->valArray["id"];
    }

    public static function getTotalNumberOfRecordsFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public function gettrackingnumber() {
        return $this->valArray["tracking_number"];
    }

    public function getCreatedData() {
        return $this->valArray["date_created"];
    }

    public static function getTotalNumberOfConsignmentsFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }


    public static function getHeader()
    {
        $record = "Account, HawbNo, Reference, TrackingNumber, Company, Contact, Address1, Address2, Address3, City, Postcode, Country, Telephone, Weight, NumberOfPieces,  Description, DateCreated, DateBooked, Owner, Supplier, ServiceCode, ServiceName, RemoteArea";
        return $record;
    }

    public static function GetDuplicateConList($limit)
    {

        $sql = "select consignment_id from tracking_data where description = 'HAYES SORTING CENTRE' and account = 'OPERA' and ip_address = ''
and consignment_id > 0 and consignment_id != '' group by consignment_id having count(*) > 10  limit $limit";

        echo $sql;

        return DbAccess3::getListFromSql(__CLASS__, $sql);


    }

    public static function DeleteDuplicateConList($conId)
    {

        $sql = "delete from tracking_data where description = 'HAYES SORTING CENTRE'  and account = 'OPERA' and (ip_address = '' or ip_address is null) and
				consignment_id =  $conId";

        echo $sql;

        return DbAccess3::getListFromSql(__CLASS__, $sql);

    }

  
    public static function SendEmail($trackingNumbers, $sessionUser, $emailArray = '', $type = '', $saveHandling = 'Y', $carrier = '')
    {

        $sessionUser = SessionManager::getUser();
        $NumberofUniqueCode = 0;
        $trackingNumberArrayStr = "'" . implode("','", $trackingNumbers) . "'";
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFilter("    c.id in ($trackingNumberArrayStr)");
        $consignmentFilter->addFieldNotFilter("    awb","");
        $consignmentFilter->AddOrderBy("    service_id", "asc");
        $list = $consignmentFilter->getColumnList(" IF( c.customized_service_id > 0,c.customized_service_id,c.service_id) 'service_id', user_id, hawb, c.reference, c.awb, c.company, c.contact, c.address_line_1, c.address_line_2, c.address_line_3, c.city, 
                                    c.postcode, c.country_id,c.telephone, c.weight, c.number_pieces, c.description, c.date_label_created, c.date_booked, c.remote_charges", count($trackingNumbers));

        $csv = "";
        $cr = "\r\n";
        $code = "";
        $codeArray = array();
        $serviceArray = array();
        $serviceTypeArray = array();
        $consignmentIdArray =   array();

        if(count($list) >0)
        {

            foreach($list as $consignment)
            {
                $consignmentIdArray[] = $consignment->getId();
                $service_type =  $consignment->getServiceType();
                $totalWeight += $consignment->getWeight();
                $supplier = '';
                $service = new Services($consignment->getServiceId());
                $servicename = $service->getName();
                $code = $service->getCode();
                if(!in_array($code, $codeArray))
                    $codeArray[] = $code;
                if(!in_array($servicename, $serviceArray))
                    $serviceArray[] = $servicename;
                if(!in_array($service_type, $serviceTypeArray))
                    $serviceTypeArray[] = $service_type;
                $userAccount	= new CustomerAccount($sessionUser->getUserAccountId());
                $parentId = $userAccount->getParentId();
                $parentAccount = "";
                $dateBooked = "";
                if($parentId > 0)
                {
                    $parentId = $userAccount->getParentId();
                    $parentUser = new User($parentId);
                    $parentUserAccount	= new CustomerAccount($parentUser->getUserAccountId());
                    $parentAccount = $parentUserAccount->getUserAccount();
                }
                if($consignment->getDateBooked() != "1970-01-01")
                    $dateBooked = $consignment->getDateBooked();
                $country = new Country($consignment->getCountryId());
                $countryName = $country->getName();
                $csv  .=   $userAccount->getAccount() . ',';
                $csv  .=   ($consignment->getHawb()) . ',';
                $csv  .=   ($consignment->getReference()) . ',';
                $csv  .=  "=\"" . $consignment->getAwb()       . "\"" . ",";
                $csv  .=   ($consignment->getCompany()) . ',';
                $csv  .=   ($consignment->getContact()) . ',';
                $csv  .=   ($consignment->getAddressLine1()). ',';
                $csv  .=   ($consignment->getAddressLine2()) . ',';
                $csv  .=   ($consignment->getAddressLine3()) . ',';
                $csv  .=   ($consignment->getCity()) . ',';
                $csv  .=   ($consignment->getPostCode()) . ',';
                $csv  .=   ($countryName) . ',';
                $csv  .=   ($consignment->getTelephone()) . ',';
                $csv  .=   ($consignment->getWeight()) . ',';
                $csv  .=   ($consignment->getNumberPieces()) . ',';
                $csv  .=   ($consignment->getDescription()) . ',';
                $csv  .=   date("Y-m-d", $consignment->getDateLabelCreated()). ',';
                $csv  .=   $dateBooked. ',';
                $csv  .=   ($parentAccount). ',';
                $csv  .=   $supplier. ',';
                $csv  .=   ($code). ',';
                $csv  .=   $servicename. ',';
                $csv  .=   $consignment->getRemoteCharges().',';
                $csv  .=   $cr;
            }
            $csvHeader = self::getHeader();
            $folder_path = "../_assets/client_tracking_files/" . $sessionUser->getUserAccountId();
            if (!file_exists($folder_path)) {
                mkdir($folder_path, 0777, true);
            }
            $uniqueFileName = uniqid();
            $file_path    = $folder_path ."/" . $uniqueFileName . ".csv";
            /////////////////////////////////// SAVING FILE LINK TO MANIFEST //////////////////////////////////////
            $manifest = new Manifest();
            $manifest->setPieces(count($trackingNumbers));
            $manifest->setDateCreated(time());
            $manifest->setUserId($sessionUser->getId());
            $manifest->setFileName($file_path);
            $manifest->setWeight($totalWeight);
            $manifest->save();
            $manifestid = $manifest->getId();
            $manifestSummaryReport = new ManifestSummaryReport();
            $pdf_file_name = $manifestSummaryReport->SavePDFFile($list, $manifestid);
            $manifest->setPdfFile($pdf_file_name);
            $manifest->save();
            if(count($consignmentIdArray) > 0 )
            {
                $ManifestConsignmentMapping = new ManifestEntityMapping();
                $ManifestConsignmentMapping->bulkDataInsertCustomerMenifest($manifestid,'P', $consignmentIdArray);
            }
            $file_path = fopen($file_path, 'w');
            fwrite($file_path, $csvHeader . $cr . $csv);
            fclose($file_path);
        }
        return $manifestid;
    }


    public static function SendDataToCourier($trackingNumbers)
    {

        $cms_flag = false;



        $scannedNumbers = "'" . implode("','", $trackingNumbers) . "'";

        $con_filter = new ConsignmentFilter();
        $con_filter->addawbFilterList($scannedNumbers);
        $con_filter->addFieldEqualFilter('consignment_status','<>', 'recycled');
        $con_filter->addDateBookedNotSet();

        //print_r($con_filter);

        $list = $con_filter->getColumnList("awb, handling");

        //print_r($list);
        //die;
        //$list = $con_filter->getColumnList("handling, account, hawb, reference, awb, company, contact, address_line_1, address_line_2, address_line_3, city, postcode, country, telephone, weight, number_pieces, description, date_submitted, date_booked, service_type");
        //echo count($list);die;

        $user = SessionManager::getUser();


        if(count($list) > 0)
        {

            foreach ($list as $consignment)
            {
                //$arr_numbers_data_not_sent[] = $consignment->getAwb();
                if($consignment->getHandling() == '19DEDR')
                {
                    if($user->getWarehouseId() == 15)
                    {
                        $ConsignmentLog = new ConsignmentLog();
                        $ConsignmentLog->createlog("DPD GERMANY DIRECT Data Sent by " . $user->getAccount(), $consignment->getId());
                        $arr_numbers_data_not_sent[] = $consignment->getAwb();
                    }
                }
                else
                {
                    $arr_numbers_data_not_sent[] = $consignment->getAwb();
                }

            }

            $str_data_not_sent = implode(",", $arr_numbers_data_not_sent);
            //print_r($arr_numbers_data_not_sent);
            SendDataToCourier::sendCarrierData($arr_numbers_data_not_sent, 1);

            //mail("kazim@oneworldexpress.com", "DATA NOT SENT TO COURIER", $str_data_not_sent);



        }


    }
    /*
        * Scenario 201
        * Get : Consignment AWB, User Account, "status=>Received"
        * Return : 
        */
    public static function AddVirtualTrackingToScanParcels($trackingNumbers, $sessionUser, $status = '144', $date = '', $hub = '',$track_point = '', $pod_image = '', $pod_date = '', $sorter_status = '', $pod_name='', $carrierDesc = '') {
        
        if($date == '')
            $date = date("Y-m-d H:i");
        $NumberofUniqueCode = 0;
        $result = '';
        $userAccount = $sessionUser->getUserAccount();
        /*$warehouseId = $sessionUser->getWarehouseId();
        $userId = $sessionUser->getId();
        if($hub == '')
        {
            $warehouse = new Warehouse($warehouseId);
            $hub = $warehouse->getHub();
        }*/
//		$email = $sessionUser->getEmail();//Not used any where commiting this
       
        
        if($track_point == '' && in_array($status, array(144,146))) {
            $warehouseid = $sessionUser->getWarehouseId();
            $warehouseName = '';
            $countryIso3 = '';
            if ($warehouseid > 0) {
                $warehouseObj = new Warehouse($warehouseid);
                $warehouseName = $warehouseObj->getWarehouseName();
            }
            $countryId = $sessionUser->getCountryId();
            $country = new Country($countryId);
            if (count($country) > 0)
                $countryIso3 = $country->getIso3();
            $track_point = $warehouseName . " - " . $countryIso3;
        }
        $trackingNumberArrayStr = "'" . implode("','", $trackingNumbers) . "'";
        $trackingNumberExist = array();
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addawbFilterList($trackingNumberArrayStr);
        $list = $consignmentFilter->getColumnList("service_id, awb, user_id", count($trackingNumbers));

        foreach ($list as $con) {
            if(!in_array($con->getAwb(), $trackingNumberExist))
            {
                $con_id = $con->getID();
                //$consignment = new Consignment($con_id);
                $consignmentParcels = $con->getParcels();
                if(count($consignmentParcels) > 0){
                    foreach($consignmentParcels as $consignmentParcel) {
                        $parcelId = $consignmentParcel->getId();#
                        $trackingDataFilter = new TrackingDataFilter();
                        $trackingDataFilter->addTrackPointExistFilter($consignmentParcel->getTrackingNumber(), $status, $track_point, '', $carrierDesc, $date);
                        $trackingExitsList = $trackingDataFilter->getColumnList("id");
                        
                        if(count($trackingExitsList) == 0){
                            $trackingData = [
                                'user_id' => $sessionUser->getId(),
                                'entity_id' => $parcelId,
                                'entity_type' => 'parcel',
                                'tracking_number' => $consignmentParcel->getTrackingNumber(),
                                'track_point' => $track_point,
                                'date_created' => $date,
                                'ip_address' => getClientIp(),
                                'status_code_id' => $status,
                                'pod_image' => $pod_image,
                                'carrier_code' => '',
                                'carrier_desc' => ($carrierDesc != '' ? $carrierDesc : Tracking::$oneworld_status_desc[$status]),
                                'signatory' => $pod_name
                            ];
                            $trackingDataObj = new TrackingData($trackingData);
                            $trackingDataObj->save();
                             //change parcel status
                            $consignmentParcel->setParcelStatusCode(Tracking::$oneworld_consignment_code_mapping[$status]);
                            $consignmentParcel->setOweStatusCode(Consignment::$database_status_array[Tracking::$oneworld_consignment_code_mapping[$status]]);
                            $consignmentParcel->save();
                        }
                       

                    }
                }
                /*
                $trackDataArr[$NumberofUniqueCode]['consignment_id'] = $con_id;
                $trackDataArr[$NumberofUniqueCode]['tracking_number'] = $con->getAwb();
                $trackDataArr[$NumberofUniqueCode]['status_code'] = $status;
                $trackDataArr[$NumberofUniqueCode]['description'] = $hub;
                $trackDataArr[$NumberofUniqueCode]['track_point'] = $track_point;
                $trackDataArr[$NumberofUniqueCode]['date_created'] = $date;
                $trackDataArr[$NumberofUniqueCode]['user_id'] = $userId;
                $trackDataArr[$NumberofUniqueCode]['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $trackingNumberExist[] = $con->getAwb();
                $NumberofUniqueCode++;*/
            }
        }
        /*if(count($trackDataArr) > 0) {
            $trackingDataFilter = new TrackingDataFilter();
            $result = $trackingDataFilter->bulkDataInsert($trackDataArr);
        }*/
        
        /*if(array_key_exists($status, Tracking::$oneworld_status_code))
        {
            $trackingNumberArrayStr = "'" . implode("','", $trackingNumbers) . "'";
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addJoin("tracking_data td","td.tracking_number = c.awb");
            $consignmentFilter->addFilter("td.tracking_number in (".$trackingNumberArrayStr.")");
            $resultArray = $consignmentFilter->getListNew('c.id, c.date_booked, td.date_created','',false);
            
            if(count($resultArray)>0)
            {
                foreach($resultArray as $result)
                {
                    $dateBooked = $result->getDateBooked();
                    if($dateBooked == '0' || $dateBooked == '' || $dateBooked == null)
                    {       
                        $consignment = new Consignment($result->getId());
                        $consignment->setDateBooked(strtotime($date));
                        $consignment->save();            
                    }
                }
            }            
        }*/
        return $result;
    }
    public static function putShipmentOnHold($con, $status)
    {

        //$con = $con_list[0];
        $oldStatus = $con->getStatus();
        $message = '';

        $ConsignmentLog = new ConsignmentLog();

        //if($status == Consignment::STATUS_HOLD)
        {
            $con->setStatus(Consignment::STATUS_HOLD);
            $con->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_HOLD]);
            //$message = Translation::GetCaption("MSG_SHIPMENT_ADDED_ON_HOLD");
        }
        //else
//		{
//			$con->setStatus(Consignment::STATUS_RECEIVED);	
//                        $con->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_RECEIVED]);
//			//$message = Translation::GetCaption("MSG_SHIPMENT_REMOVED_FROM_HOLD");
//		}

        $con->save();

        $ConsignmentLog->createlog("Consignment Status Changed from " . Consignment::$database_status_array[$oldStatus] . " to " . Consignment::$database_status_array[$con->getStatus()]. " for collection.", $con->getId());

    }

    public static function getUserOnHoldRecords()
    {

        $user = SessionManager::getUser();
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addStatusFilter(array(Consignment::STATUS_HOLD));
        $consignmentFilter->addAccountFilter($user->getId());
        $list = $consignmentFilter->getColumnList("id, awb");
        $totalRecords = count($list);

        return $totalRecords;



    }

    public static function getTotalParcelsCountByAccountId($userAccountId,$warehouseId,$userId=0) {
            $whare = "";
            if($userId > 0)
                $whare = " AND td.`user_id` =  ".DbAccess3::escape($userId);
		$sql = "SELECT 
                            COUNT(p.`id`) AS total
                          FROM
                            tracking_data td 
                             JOIN parcel p 
                              ON p.`id` = td.`entity_id`  
                             JOIN `user` u 
                              ON u.`id` = td.`user_id` 
                          WHERE u.`user_account_id` = '".DbAccess3::escape($userAccountId)."' AND td.`status_code_id` = '146' AND td.`warehouse_id` = '".DbAccess3::escape($warehouseId)."' ".$whare;
		return TrackingData::getTotalNumberOfConsignmentsFromSql($sql);
	}
	public static function getTotalScannedManifestCountByAccountId($userAccountId,$date="",$op="") {
            $sql = "SELECT 
                        Count(m.`id`) AS total
                      FROM
                        `manifest` m 
                        JOIN `user` u 
                          ON u.`id` = m.`user_id` 
                      WHERE m.`is_dispatched` = 'y' AND m.manifest_by = 'operation' 
                        AND u.`user_account_id` = '".DbAccess3::escape($userAccountId)."' ";
		return TrackingData::getTotalNumberOfConsignmentsFromSql($sql);
	}
	public function upcommingManifests($userAccountId,$date="",$op="") {
		$where = "";
		if(!empty($date) && !empty($op)){
			$where = "AND DATE(m.date_created) ".$op." '".$date."'";
		}
		$sql = "SELECT 
                            * 
                          FROM
                            (SELECT 
                              mem.`manifest_id` as r_menifest_id,
                              SUM(c.`weight`) as r_menifest_weight,
                              t.`status_code_id`,
                              t.`tracking_number` 
                            FROM
                              tracking_data t 
                              INNER JOIN parcel p 
                                ON p.`id` = t.`entity_id` 
                              INNER JOIN consignment c 
                                ON c.`id` = p.`consignment_id` 
                              INNER JOIN manifest_entity_mapping mem 
                                ON p.`id` = mem.`entity_id` 
                              INNER JOIN `manifest` m 
                                ON m.`id` = mem.`manifest_id` 
                              INNER JOIN `user` u 
                                ON u.`id` = t.`user_id` 
                            WHERE u.`user_account_id` = '".DbAccess3::escape($userAccountId)."' 
                            $where 
                            GROUP BY t.`tracking_number` 
                            HAVING COUNT(t.id) = 1) AS upcommingMenifest 
                          WHERE `status_code_id` = 144 
                          GROUP BY r_menifest_id";
//                echo $sql; exit;
		return TrackingData::getTrackingDataListFromSql($sql);
	}
	public static function upcommingManifestTotal($userAccountId) {
		$sql = "SELECT 
                            r_menifest_id
                            FROM
                              (SELECT 
                                mem.`manifest_id` AS r_menifest_id,
                                td.`tracking_number`,
                                td.`status_code_id` 
                              FROM
                                tracking_data td 
                                INNER JOIN parcel p 
                                  ON p.`id` = td.`entity_id` 
                                INNER JOIN manifest_entity_mapping mem 
                                  ON p.`id` = mem.`entity_id` 
                                INNER JOIN `user` u 
                                  ON u.`id` = td.`user_id` AND u.`user_account_id` = '".DbAccess3::escape($userAccountId)."'
                              GROUP BY td.`tracking_number` 
                              HAVING COUNT(td.id) = 1) AS upcommingMenifest 
                            WHERE `status_code_id` = '144' GROUP BY r_menifest_id";
		return TrackingData::getTrackingDataListFromSql($sql);
	}
        
        public static function getCSDashboardTotalShipment($userAccountId,$warehouseId=0,$userId=0,$query='') {
            $where = "";
            if(!empty($query))
                $where .= $query;
		
            $sql = "SELECT 
                        COUNT(c.`id`) AS total 
                      FROM
                        parcel p 
                        JOIN consignment c 
                          ON c.`id` = p.`consignment_id` 
                        JOIN `user` u 
                          ON u.`id` = c.`user_id`  
                      WHERE u.`user_account_id` IN (".implode(',',$userAccountId).")  ".$where;
            //echo $sql;exit;
            return TrackingData::getTrackingDataListFromSql($sql);
	}
        public static function getCSDashboardTotalLabelCreated($userAccountId,$warehouseId=0,$userId=0,$query='') {
            $where = "";
            if(!empty($query))
                $where .= $query;
		
            $sql = "SELECT 
                        COUNT(c.`id`) AS total 
                      FROM
                        parcel p 
                        JOIN consignment c 
                          ON c.`id` = p.`consignment_id` 
                        JOIN `user` u 
                          ON u.`id` = c.`user_id`  
                      WHERE u.`user_account_id` IN (".implode(',',$userAccountId).")  ".$where;
            //echo $sql;exit;
            return TrackingData::getTrackingDataListFromSql($sql);
	}
        
        public static function getAccountDashboardTotalBilledAmount($userAccountId=0) {
            $sql = "SELECT 
                        SUM(IFNULL(cc.`cost`, 0)) AS id 
                      FROM
                        `consignment_charges` cc 
                        JOIN `invoices` i 
                          ON i.id = cc.`invoice_id` 
                          AND i.`is_paid` = '1' 
                      WHERE cc.`account_id` IN 
                        (SELECT 
                          id 
                        FROM
                          user_account 
                        WHERE parentid IN ($userAccountId))";
            //echo $sql;exit;
            return TrackingData::getTrackingDataListFromSql($sql);
	}
        
       
        
        public static function getAccountDashboardBillableAmount($userAccountId) {
            $sql = "SELECT 
                    SUM(IFNULL(cc.`cost`,0)) AS id 
                  FROM
                    `consignment_charges` cc 
                  WHERE (
                      cc.`invoice_id` = 0 
                      OR cc.`invoice_id` IS  NULL
                    ) 
                    AND cc.`account_id` IN 
                    (SELECT 
                      id 
                    FROM
                      user_account 
                    WHERE parentid = '$userAccountId')";
            //echo $sql;exit;
            return TrackingData::getTrackingDataListFromSql($sql);
	}
        public static function getAccountBillableShipment($userAccountId) {
            $where = "";
            if(!empty($query))
                $where .= $query;
		
            $sql = "SELECT 
                      COUNT(`consignment_id`) AS id
                    FROM(
                        SELECT 
                        cc.`consignment_id`
                      FROM
                        `consignment_charges` cc 
                      WHERE (
                          cc.`invoice_id` > 0 
                            OR cc.`invoice_id` IS NOT NULL
                        ) 
                        AND cc.`account_id` IN (SELECT id FROM user_account WHERE parentid = '$userAccountId') GROUP BY cc.`consignment_id`) AS tmp";
            return TrackingData::getTrackingDataListFromSql($sql);
	}
        
        public static function getAccountShipmentAmount($userAccountId) {
            $where = "";
            if(!empty($query))
                $where .= $query;
		
            $sql = "SELECT 
                    SUM(IFNULL(cc.`cost`,0)) AS id 
                  FROM
                    `consignment_charges` cc 
                  WHERE
                  cc.`account_id` IN 
                    (SELECT 
                      id 
                    FROM
                      user_account 
                    WHERE parentid = $userAccountId)";
            //echo $sql;exit;
            return TrackingData::getTrackingDataListFromSql($sql);
	}
}  // class




/*$file_object_dpd = new AutoBookDpdGermany();
			$booking_file_id_dpd = $file_object_dpd->getBookingFileId();
			
			
			$file_object_correos = new AutoBookEuroBToC();
			$booking_file_id_correos = $file_object_correos->getBookingFileId();
		
			foreach ($list as $consignment)
			{
					if($consignment->getHandling() == "19EURDPD" || $consignment->getHandling() == "19EURDPDDE")
					{	
					
						if ($file_object_dpd->addConsignment($consignment))
						{		
							$consignment->setStatus(Consignment::STATUS_DISPATCHED);
							$consignment->setBookedFileId($booking_file_id_dpd);
							$consignment->save();
							$bookings_made_dpd++;	
						}						
			
					}					
					elseif($consignment->getHandling() == "KB" || $consignment->getHandling() == "CORREOS" || $consignment->getHandling() == "CORRNPO" || $consignment->getHandling() == "DAC")
					{
						if ($file_object_correos->addConsignment($consignment))
						{
							$consignment->setStatus(Consignment::STATUS_DISPATCHED);
							$consignment->setBookedFileId($booking_file_id_correos);
							$consignment->save();
							$bookings_made_euro++;	
						}
					}			
					
			}
			
			if($bookings_made_dpd > 0)
			{
			
				if(!$file_object_dpd->sendBookings($cms_flag))
				{
					mail("kazim@oneworldexpress.com", "DPD DATA NOT SENT", "BOOKING FILE ID : " . $booking_file_id_dpd);
				}
			}
			
			if($bookings_made_euro > 0 )
			{			
				if (!$file_object_correos->sendBookings($cms_flag1))
				{
					mail("kazim@oneworldexpress.com", "KB / CORREOS / CORRNPO / DAC DATA NOT SENT", "BOOKING FILE ID : " . $booking_file_id_correos);
				}
			}*/