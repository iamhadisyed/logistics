<?php
/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
 
class Bagging extends DbAccess3 
{	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'id'	=> 'number',
					'bagnumber' => 'string',			
					'date_created'=> 'datetime',			
					'csv' => 'string',			
					'pdf' => 'string',			
					'manifestid'=> 'number',
					'account'	=> 'string',
					'user_id'	=> 'number',			
					'manifest_pdf'	=> 'string',
					'bag_status'	=> 'number',
					'date_updated'  => 'datetime',
					'isdeleted'  => 'number',
					'service' => 'string',
					'carrier_id' => 'number',
					'country' => 'string',
					'country_iso_code' => 'string',					
					'bag_type' => 'string',
					'actual_weight' => 'number',
					'length' => 'number',
					'width' => 'number',
					'height' => 'number',
					'pieces' => 'number',
					'weight' => 'number',
					'bag_label' => 'string',
					'is_closed' => 'bit',
					'bag_source_country_id' => 'number',
                                        'bag_source_warehouse_id' => 'number',
                                        'bag_destination_country_id' => 'number',
                                        'bag_destination_warehouse_id' => 'number',
                                        'closed_by' => 'number',
                                        'closed_date' => 'datetime',
                                        'reopen_by' => 'number',
                                        'reopen_date' => 'datetime',
            				'bag_manifest' => 'string',
                                        'bag_manifest' => 'string',
                                        'bag_value' => ['enum' => ['hv','lv','mv']],
                    'is_country_bagging' => ['enum' => ['y','n'],'default' => 'n'],
                    'is_fixed' => ['enum' => ['y','n'],'default' => 'n'],
                    'bag_filter' => 'string',
                    'oc_bag_label' => 'string',
                    'country_name' => 'undefined'
                    );
		//
		parent::__construct("bagging", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getBaggingListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}		
	public static function getTotalNumberOfBaggingFromSql($sql) {
            $rs = DbAccess3::runQuery($sql);
            $data = mysqli_fetch_assoc($rs);
            return $data['total'];
        }
	public static function CreateBagCsvAndPdf($bagId) {
        if ($bagId > 0) {
            $bagging = new Bagging($bagId);
            $ConsignmentBaggingMappingFilter = new ConsignmentBaggingMappingFilter();
            $ConsignmentBaggingMappingFilter->addFieldEqualFilter("bagid", "=", $bagId);
            $consignmentlist = $ConsignmentBaggingMappingFilter->getList();
            if (count($consignmentlist) > 0) {
                $conIdArray = array();
                foreach ($consignmentlist as $cons) {
                    $conIdArray[] = $cons->getConsignmentid();
                }
                $sessionUser = SessionManager::getUser();
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addIdArrayFilter($conIdArray);

                $consignmentFilter->AddOrderByServiceType();
                $list = $consignmentFilter->getColumnList("handling, account, hawb, reference, awb, company, contact, address_line_1, address_line_2, address_line_3, city, postcode, country, telephone, weight, number_pieces, description, date_submitted, date_booked, service_type, account_owner");
                $csv = "";
                $cr = "\r\n";
                $code = "";
                $codeArray = array();
                $serviceArray = array();
                $serviceTypeArray = array();
                $tweight = 0;
                $tpieces = 0;
                foreach ($list as $consignment) {
                    $code = str_replace("RTN", "", $consignment->getHandling());
                    $account_owner = $consignment->getAccountOwner();
                    $service_type = $consignment->getServiceType();
                    $supplier = '';
                    //echo $code; exit;
                    $serv = new ServiceFilter();
                    $serv->addSCodeFilter($code);
                    $servList = $serv->getColumnList("supplier, name");
                    if (count($servList) > 0) {
                        $supplier = $servList[0]->getSupplier();
                        $servicename = $servList[0]->getName();
                    } else
                        $servicename = $consignment->getServiceType();

                    if (!in_array($code, $codeArray))
                        $codeArray[] = $code;

                    if (!in_array($servicename, $serviceArray))
                        $serviceArray[] = $servicename;

                    if (!in_array($service_type, $serviceTypeArray))
                        $serviceTypeArray[] = $service_type;


                    $userfilterClass = new UserFilter();
                    $userfilterClass->addParentidFilter($sessionUser->getParentId());
                    $ulist = $userfilterClass->getColumnList("user_account");
                    if (count($ulist) > 0)
                        $owner = $ulist[0]->getUserAccount();
                    $csv .= $consignment->getAccount() . ',';
                    $csv .= self::removecommas($consignment->getHawb()) . ',';
                    $csv .= self::removecommas($consignment->getReference()) . ',';
                    $csv .= "=\"" . $consignment->getAwb() . "\"" . ",";
                    $csv .= self::removecommas($consignment->getCompany()) . ',';
                    $csv .= self::removecommas($consignment->getContact()) . ',';
                    $csv .= self::removecommas($consignment->getAddressLine1()) . ',';
                    $csv .= self::removecommas($consignment->getAddressLine2()) . ',';
                    //;
                    $csv .= self::removecommas($consignment->getAddressLine3()) . ',';
                    $csv .= self::removecommas($consignment->getCity()) . ',';
                    $csv .= self::removecommas($consignment->getPostCode()) . ',';
                    $csv .= self::removecommas($consignment->getCountry()) . ',';
                    $csv .= self::removecommas($consignment->getTelephone()) . ',';
                    $csv .= self::removecommas($consignment->getWeight()) . ',';
                    $csv .= self::removecommas($consignment->getNumberPieces()) . ',';
                    $csv .= self::removecommas($consignment->getDescription()) . ',';
                    $csv .= $consignment->getDateSubmitted() . ',';
                    $csv .= $consignment->getDateBooked() . ',';
                    $csv .= self::removecommas($owner) . ',';
                    $csv .= $supplier . ',';
                    $csv .= self::removecommas($consignment->getHandling()) . ',';
                    $csv .= $servicename . ',';
                    $csv .= $cr;
                    $tweight += $consignment->getWeight();
                    $tpieces += $consignment->getNumberPieces();
                }
                $csvHeader = self::getHeader();
                $folder_path = "../_assets/client_bagging_files/" . $sessionUser->getAccount();
                if (!file_exists($folder_path)) {
                    mkdir($folder_path, 0777, true);
                }
                $bagging->setPieces($tpieces);
                $bagging->setWeight($tweight);
                $bagging->save();
                $uniqueFileName = uniqid();
                $file_path = $folder_path . "/" . $uniqueFileName . ".csv";
                $pdf_file_name = BagLabel::buildPDFDocuments($bagId);
                $bagging->setCsv($file_path);
                $bagging->setPdf($pdf_file_name);
                $bagging->save();
                $file_path = fopen($file_path, 'w');
                fwrite($file_path, $csvHeader . $cr . $csv);
                // close file
                fclose($file_path);
                return $pdf_file_name;
            } else {
                return "ERROR||No Shipment Found";
            }
        }
    }
    
    public static function getAlreadyAssignedBag($consignment_ids = array()){
        $consignmentDataForBagnumber = "SELECT 
                            p.tracking_number as awb , c.id as id , b.bagnumber as bag_id
                    FROM 
                            consignment c 
                    INNER JOIN 
                            parcel p ON  c.id = p.`consignment_id` 
                    INNER JOIN 
                            parcel_bagging_mapping pbm ON pbm.parcel_id = p.id 
                    INNER JOIN 
                            bagging b ON pbm.bag_id = b.id
                    WHERE 
                            c.id IN (".implode(",",$consignment_ids).")";
                 $consignmentData = Consignment::getConsignmentListFromSql($consignmentDataForBagnumber);
                 return $consignmentData;
    }
    
    
    
    public static function insertBagData($oba_docket_no, $consignmentid, $userId){
        $bagNumberMapping = "INSERT INTO 
                                    parcel_bagging_mapping (bag_id, parcel_id,added_by,added_date)
                            SELECT 
                                (SELECT id FROM bagging WHERE bagnumber = '".$oba_docket_no."'),p.id, ".$userId.",NOW()  "
                        . " FROM "
                        . "     parcel p "
                        . " WHERE "
                        . "     p.consignment_id = '".$consignmentid."'
                            AND 
                                p.id NOT IN (SELECT parcel_id FROM parcel_bagging_mapping WHERE parcel_id = p.id AND bag_id in  (SELECT id FROM bagging WHERE bagnumber = '".$oba_docket_no."'  ) )"; 

        DbAccess3::runQuery($bagNumberMapping);
    }
    
    
    public static function insertBagInfo($oba_docket_no, $consignmentid, $userId){
        $docketNumberInvocieInsert  = "  INSERT 
                                            INTO bagging 
                                                ( bagnumber,user_id,bag_status,service,country,country_iso_code,bag_type,bag_source_country_id, bag_source_warehouse_id, bag_destination_country_id,bag_destination_warehouse_id)
                                            SELECT '".$oba_docket_no."', ".$userId.",1,c.service_id,c.country_id,'GB','MIX',225, 10, 225,10 "
                                                . " FROM "
                                                . "     consignment c "
                                                . " WHERE "
                                                . "         c.id = '".$consignmentid."' "
                                                . "     AND '".$oba_docket_no."' NOT IN (SELECT bagnumber FROM bagging)";
        DbAccess3::runQuery($docketNumberInvocieInsert);
    }
    
    public static function deleteByBagId($bagId) {
        if($bagId != "" && $bagId > 0)
            self::runQuery("DELETE FROM `bagging` WHERE id = '".DbAccess3::escape($bagId)."'");
    }

    
    
}