<?php
include_classes([
    'manifestentitymappingfilter.class',
    'manifestentitymapping.class',
    'manifestservicemapping.class',
    'manifestservicemappingfilter.class'
]);
class Manifest extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'user_id' => 'number',
            'file_name' => 'string',
            'parcel_file_name' => 'string',
            'label_link' => 'string',
            'date_created' => 'datetime',
            'pieces' => 'number',
            'agent_id' => 'number',
            'weight' => 'number',
            'service_id' => 'number',
            'pdf_file' => 'string',
            'ioss_manifest_file' => 'string',
            'flight_number' => 'string',
            'mawb' => 'string',
            'type' => 'string',
            'collection_date' => 'datetime',
            'collection_date_to' => 'datetime',
            'pickup_date' => 'datetime',
            'collection_comment' => 'string',
            'delivery_note' => 'string',
            'signature' => 'string',
            'pickup_id' => 'number',
            'date_received' => 'datetime',
            'received_by' => 'string',
            'name_of_driver' => 'string',
            'licence_number' => 'string',
            'routing_email_date' => 'datetime',
            'route_warehouse_id' => 'number',
            'account_owner' => 'string',
            'product' => 'string',
            'number_bag' => 'string',
            'carrier_note' => 'string',
            'carrier_pdf' => 'string',
            'carrier_id' => 'number',
            'is_deleted' =>  ['enum' => ['Y','N'],'default' => 'N'],
            'is_dispatched' => ['enum' => ['Y','N'],'default' => 'N'],
            'is_send_email' => ['enum' => ['Y','N'],'default' => 'N'],
            'manifest_by' => ['enum' => ['operation','client'],'default' => 'client'],
            'manifest_id' => 'undefined',
        );
        //
        parent::__construct("manifest", 'id', $fieldList, $mixedCreator);
    }

    public static function getManifestListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function manifestCreate($parcelIds = [], $serviceId = 0, $manifestBy = "client", $cctManifest = 0,$warehouseId=0,$userData="",$typeCase="service",$parcelServiceIdArr=[]){
        $errorArray =   array();
        $sessionUser = SessionManager::getUser();
        if(empty($userData)){
            $userAccountId = $sessionUser->getUserAccountId();
            $userId = $sessionUser->getId();
        }else{
            $userAccountId = $userData->getUserAccountId();
            $userId = $userData->getId();
        }
        
        // Check if parcels are already manifested
        $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
        $manifestEntityMappingFilter->addManifestJoin();
        $manifestEntityMappingFilter->addFilterIn("    mem.entity_id", $parcelIds);
        $manifestEntityMappingFilter->addFieldFilter("  mem.manifest_entity_type", 'p');
        $manifestEntityMappingFilter->addFieldFilter("  m.manifest_by", $manifestBy);
        $manifestEntityMappingFilter->addFilter("  m.user_id in (select id from user where user_account_id = '".$userAccountId."' AND warehouse_id = '".$warehouseId."')", $manifestBy);
        $manifestEntityObj = $manifestEntityMappingFilter->getColumnList("mem.entity_id");
        if(count($manifestEntityObj) > 0){
            $output["STATUS"] = "Error";
            $errorArr = [];
            foreach ($manifestEntityObj as $manifestEntity) {
                $parcel = new Parcel($manifestEntity->getEntityId());
                $errorArr[] = "Parcel [ ".$parcel->getTrackingNumber()." ] is already manifested";
            }
            $output["MESSAGE"] = $errorArr;
            return $output;
        }
        //  Calculate all parcel weight and then papulate the weight feild
//        $parcelFilter = new ParcelFilter();
//        $parcelFilter->addFilterIn("    p.id", $parcelIds);
//        $parcelFilterObjs = $parcelFilter->getColumnList("sum(p.weight) as weight");
//        $totalParcelWeight = 0;
//        if(count($parcelFilterObjs) > 0) {
//            $totalParcelWeight = $parcelFilterObjs[0]->getWeight();
//        }
        $newServiceId = "";
        $newAgentId = "";
        $newCarrierId = "";
        if($typeCase == 'agent'){
            $newAgentId = $serviceId;
        }else if($typeCase == 'carrier'){
            $newCarrierId = $serviceId;
        }
        $manifest = new Manifest([
            'pieces'=>count($parcelIds),
            'date_created' =>date("Y-m-d H:i:s"),
            'user_id'=>$userId,
            'file_name'=> '',
            'manifest_by' =>  $manifestBy ,
            'route_warehouse_id' =>  $warehouseId,
            'is_dispatched' =>  'N',
            'agent_id'=> ($newAgentId != "" ? $newAgentId : "NULL"),
            'carrier_id'=>($newCarrierId != "" ? $newCarrierId : "NULL")
        ]);
        $manifest->save();
        $manifestId =   $manifest->getId();
        if($manifestId <= 0) {
            $errorArray[] = "System could not save manifest, Please try again later";
        }else{
            $output["ID"] = $manifestId;
            $parcelServiceIdArr = array_unique($parcelServiceIdArr);
            $manifestTemplate = "";
            foreach ($parcelServiceIdArr as $parcelServiceId) {
                if($parcelServiceId > 0 && $manifestTemplate ==""){
                    $serviceObj = new Services($parcelServiceId);
                    $manifestTemplate = $serviceObj->getLabelClassName();
                }
                // Save multi service
                $manifestServiceMapping = new ManifestServiceMapping();
                $manifestServiceMapping->setManifestId($manifestId);
                $manifestServiceMapping->setServiceId($parcelServiceId);
                $manifestServiceMapping->save();
            }
                $manifestEntityMapping = "INSERT INTO manifest_entity_mapping
                                            (`id`,
                                            `entity_id`,
                                            `manifest_id`,
                                            `manifest_entity_type`)
                                            ";
                $entityArray    =   array();
                foreach($parcelIds as $parceld) {
                    $entityArray[] =      "(null, $parceld, $manifestId, 'p')";
                }
                $entityFinalQuery   = $manifestEntityMapping. " values ".  implode(",", $entityArray).";";

                
                $entityResult   =   DbAccess3::runQueryWithError($entityFinalQuery);
                if($entityResult === FALSE) {
                    $errorArray[] = "System could not save manifest entities, Please try again later";
                    $manifest->delete();
                } else {
                    //  Update weight feild all parcel which is manifisted
                    $manifestFilter = new ManifestFilter();
                    $manifestFilter->updateManifestWeight($manifestId);
                }
                if($manifestBy == "client"){
                    $consignmentUpdate  =   "UPDATE consignment Set is_customer_manifested = '1' WHERE id IN ( SELECT consignment_id from parcel where id in ('".implode("','", $parcelIds)."'))";
                    $consignmentUpdateResult       =   DbAccess3::runQueryWithError($consignmentUpdate);
                    if($consignmentUpdateResult === FALSE) {
                        $errorArray[] = "System could not update consignment, Please try again later";
                        $entityDeleteQuery   = "DELETE FROM manifest_entity_mapping where manifest_id = '".$manifestId."'";
                        $entityDeleteResult   =   DbAccess3::runQueryWithError($entityDeleteQuery);
                        if($entityDeleteResult === FLASE){
                            $errorArray[] = "System could not remove manfest entity record, Please try again later";
                        }
                        $manifest->delete();
                    }
                }
                $csv_result     =   self::generateManifestCsv($manifestId, $manifestBy,$userData);
                $parcel_csv_result     =   self::generateManifestParcelCsv($manifestId, $manifestBy,$userData);
                if($parcel_csv_result["STATUS"]) {
                    $manifest->setFileName($parcel_csv_result["FILE"]);
                    $manifest->setParcelFileName($parcel_csv_result["FILE"]);
                    $output["CSV_FILE"] = SETTING_URL_ASSETS."manifest/csv/".$csv_result['FILE'];
                    $manifest->save();
                }
                if($csv_result["STATUS"]) {
                    $manifest->setFileName($csv_result["FILE"]);
                    $output["CSV_FILE"] = SETTING_URL_ASSETS."manifest/csv/".$csv_result['FILE'];
                    $manifest->save();
                } else {
                    self::DeleteManifestById($manifestId);
                }

//                require_once("../includes/labels/royalmail.class.php");
//                $serviceObj = new Services($serviceId);
//                $royalMail = new RoyalMail();
//                $pdf_result= $royalMail->manifest($parcelIds);
                $manifestTemplate = "";
                if($newAgentId > 0){
                    $manifestTemplate = "";
                }

                if(!empty($manifestTemplate)) {
                    $consignments = self::getManifestedConsignment($manifestId, $manifestBy);
                    include_classes([
                        strtolower($manifestTemplate.".class")
                    ], 'labels');
                    $manifestTemplateObj = new $manifestTemplate();
                    $manifestTemplatePdf = $manifestTemplateObj->manifest($consignments);
                    //$pdf_result = $cttManifest->AddConsignment($consignmentsItems);
                } 
                $manifestPdf = self::generateManifestPdf($manifestId, $manifestBy,"",$userData);
                if(!empty($manifestTemplatePdf["STATUS"])){
                    $folder_path = "../_assets/manifest/pdf/" ;
                    $pdfFilePath    =    date("Y_m_d")."/".$userAccountId;
                    if (!file_exists($folder_path.$pdfFilePath)) {
                        mkdir($folder_path.$pdfFilePath, 0777, true);
                    }
                    $uniqueFileName = time().".pdf";
                    $file_path = $folder_path.$pdfFilePath ."/".$uniqueFileName;
                    $pdfMerger = new PDFMerger();
                    $pdfMerger->addPDF(SETTING_DIR_ASSETS."manifest/pdf/".$manifestTemplatePdf["FILE"], 'all');
                    if(!empty($manifestPdf["FILE"]))
                        $pdfMerger->addPDF(SETTING_DIR_ASSETS."manifest/pdf/".$manifestPdf["FILE"], 'all');
                    if(!empty($manifestPdf["IOSS_FILE"]))
                        $pdfMerger->addPDF(SETTING_DIR_ASSETS."manifest/pdf/".$manifestPdf["IOSS_FILE"], 'all');
                    $pdfMerger->merge('file', $file_path);
                    $manifestPdf['STATUS'] = true;
                    $manifestPdf['FILE'] = $pdfFilePath ."/".$uniqueFileName;
                    $output["PDF_FILE"] = SETTING_URL_ASSETS."manifest/pdf/".$manifestPdf['FILE'];
                    $manifestPdf['D_FILE'] = $file_path;
                }
                if($manifestPdf["STATUS"]) {
                    $output["PDF_FILE"] = SETTING_URL_ASSETS."manifest/pdf/".$manifestPdf['FILE'];
                    if(!empty($manifestPdf["FILE"]))
                        $manifest->setPdfFile($manifestPdf["FILE"]);
                    if(!empty($manifestPdf["IOSS_FILE"]))
                        $manifest->setIossManifestFile($manifestPdf["IOSS_FILE"]);
                    $manifest->save();
                } else {
                    self::DeleteManifestById($manifestId);
                }
        }
        if(!empty($errorArray))
        {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = implode(",", $errorArray);
        }
        else
        {
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = "Manifest generated successfully";
        }
        return $output;
    }
    public static function saveReturnManifest($parcelIds = [], $serviceIds = [],$warehouseId=0){
        $errorArray =   array();
        $sessionUser = SessionManager::getUser();
        // Check if parcels are already manifested
        $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
        $manifestEntityMappingFilter->addManifestJoin();
        $manifestEntityMappingFilter->addFilterIn("    mem.entity_id", $parcelIds);
        $manifestEntityMappingFilter->addFieldFilter("  mem.manifest_entity_type", 'p');
        $manifestEntityMappingFilter->addFieldFilter("  m.manifest_by", "operation");
        $manifestEntityMappingFilter->addFieldFilter("  m.type", "return");
        $manifestEntityMappingFilter->addFilter("  m.user_id in (select id from user where user_account_id = '".$sessionUser->getUserAccountId()."')", "operation");
        $manifestEntityObj = $manifestEntityMappingFilter->getColumnList("mem.entity_id");
        if(count($manifestEntityObj) > 0){
            $output["STATUS"] = "Error";
            $errorArr = [];
            foreach ($manifestEntityObj as $manifestEntity) {
                $parcel = new Parcel($manifestEntity->getEntityId());
                $errorArr[] = "Parcel [ ".$parcel->getTrackingNumber()." ] is already manifested";
            }
            $output["MESSAGE"] = $errorArr;
            return $output;
        }
        $manifest = new Manifest([
            'pieces'=>count($parcelIds),
            'date_created' =>date("Y-m-d H:i:s"),
            'user_id'=>$sessionUser->getId(),
            'file_name'=> '',
            'manifest_by' =>  "operation" ,
            'route_warehouse_id' =>  $warehouseId,
            'type' =>  "return",
            'is_dispatched' =>  'N'
        ]);
        $manifest->save();
        $manifestId = $manifest->getId();
        if($manifestId > 0){
            foreach ($serviceIds as $serviceId) {
                $manifestServiceMapping = new ManifestServiceMapping();
                $manifestServiceMapping->setManifestId($manifestId);
                $manifestServiceMapping->setServiceId($serviceId);
                $manifestServiceMapping->save();
            }
            $manifestEntityMapping = "INSERT INTO manifest_entity_mapping
                                            (`id`,
                                            `entity_id`,
                                            `manifest_id`,
                                            `manifest_entity_type`)
                                            ";
            $entityArray    =   array();
            foreach($parcelIds as $parceld) {
                $entityArray[] =      "(null, $parceld, $manifestId, 'p')";
            }
            $entityFinalQuery   = $manifestEntityMapping. " values ".  implode(",", $entityArray).";";
            $entityResult   =   DbAccess3::runQueryWithError($entityFinalQuery);
            if($entityResult === FALSE) {
                $errorArray[] = "System could not save manifest entities, Please try again later";
                $manifest->delete();
            } else {
                //  Update weight feild all parcel which is manifisted
                $manifestFilter = new ManifestFilter();
                $manifestFilter->updateManifestWeight($manifestId);
                $manifestPdf = self::generateManifestPdf($manifestId, "operation","return");
                $manifest->setPdfFile($manifestPdf["FILE"]);
                $manifest->save();
                $output['FILE'] = $manifestPdf["FILE"];
            }
        }else{
            $errorArray[] = "System could not save manifest, Please try again later";
        }
        if(!empty($errorArray))
        {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = implode(",", $errorArray);
        }
        else
        {
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = "Manifest generated successfully";
        }
        return $output;
    }
    public static function generateManifestCsv($manifest = 0 , $manifestBy= 'client',$sessionUser="" )
    {
        $output =   array();
        if(empty($sessionUser))
            $sessionUser = SessionManager::getUser();

        $consignmentsItems = self::getManifestedConsignment($manifest, $manifestBy);
        if(count($consignmentsItems)>0)
        {
            $csv = '';
            $sepChar = array("\r\n","\n\r", "\r", "\n", ",");
            foreach($consignmentsItems as $key=> $consignment)
            {
                $dateDispatch = $consignment->getDateBooked();
                if($dateDispatch == "" || $dateDispatch == "01-01-1970" || date("d-m-Y", $dateDispatch) == '01-01-1970'){
                    $dateDispatch = "";
                }else{
                    $dateDispatch = date("d-m-Y", $dateDispatch);
                }
                $csv  .=   (cleanCsvCall($consignment->getUserAccount())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getHawb(),'int')) . ',';
                $csv  .=   (cleanCsvCall($consignment->getReference())) . ',';
                $csv  .=   cleanCsvCall($consignment->getTrackingNumber(),'int'). ",";
                $csv  .=   (cleanCsvCall($consignment->getCompany())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getContact())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getAddressLine1())). ',';
                $csv  .=   (cleanCsvCall($consignment->getAddressLine2())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getAddressLine3())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getCity())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getPostCode())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getCountryName())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getTelephone())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getParcelWeight())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getValue())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getTotalParcel())) . ',';
                $csv  .=   (str_replace($sepChar, "",$consignment->getDescription())) . ',';
                $csv  .=   !empty($consignment->getDateLabelCreated()) ? formatDate(date("d-m-Y", $consignment->getDateLabelCreated())) : ''. ',';
//                $csv  .=   $dateDispatch. ',';
                $csv  .=   ',';
                $csv  .=   ',';
                $csv  .=   ',';
                $csv  .=   (cleanCsvCall($consignment->getServiceCode())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getServiceName())) . ',';
                $csv  .=   (cleanCsvCall($consignment->getRemoteCharges())).',';
                $csv  .=   "\r\n";
            }
            $csvHeader = self::getHeader();
            $folder_path = SETTING_DIR_ASSETS."manifest/csv/" ;
            $csvFilePath    =    date("Y_m_d")."/".$sessionUser->getUserAccountId();
            if (!file_exists($folder_path.$csvFilePath)) {
                mkdir($folder_path.$csvFilePath, 0777, true);
            }
            $uniqueFileName = time() . ".csv";
            $file_path    = $folder_path.$csvFilePath ."/".$uniqueFileName;
            $file_path_open = fopen($file_path, "w");
            fwrite($file_path_open, $csvHeader . "\r\n" . $csv);
            fclose($file_path_open);
            $output['STATUS'] = true;
            $output['FILE'] = $csvFilePath ."/".$uniqueFileName;
            $output['D_FILE'] = $file_path;
        }
        else{
            $output['STATUS'] = false;
            $output['MESSAGE'] = "No consignment found for csv";

        }
        return $output;
    }
    public static function generateManifestParcelCsv($manifest = 0 , $manifestBy= 'client',$sessionUser="" )
    {
        $output =   array();
        if(empty($sessionUser))
            $sessionUser = SessionManager::getUser();

        $consignmentsItems = self::getManifestedConsignment($manifest, $manifestBy, "p.id");
        if(count($consignmentsItems)>0)
        {
            $csv = '';
            $sepChar = array("\r\n","\n\r", "\r", "\n", ",");
            
            foreach($consignmentsItems as $key=> $consignment)
            {
                $consignmentId = $consignment->getId();
                $ItemDetailFilter = new ItemDetailFilter();
                $ItemDetailFilter->addFilter('         consignment_id = '.$consignmentId);
                $ItemDetailFilterObjs = $ItemDetailFilter->getList();
                $dateDispatch = $consignment->getDateBooked();
                if($dateDispatch == "" || $dateDispatch == "01-01-1970" || date("d-m-Y", $dateDispatch) == '01-01-1970'){
                    $dateDispatch = "";
                }else{
                    $dateDispatch = date("d-m-Y", $dateDispatch);
                }
//                $csv  .=   (cleanCsvCall($consignment->getUserAccount())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getHawb(),'int')) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getReference())) . ',';
//                $csv  .=   cleanCsvCall($consignment->getTrackingNumber(),'int'). ",";
//                $csv  .=   (cleanCsvCall($consignment->getCompany())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getContact())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getAddressLine1())). ',';
//                $csv  .=   (cleanCsvCall($consignment->getAddressLine2())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getAddressLine3())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getCity())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getPostCode())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getCountryName())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getTelephone())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getParcelWeight())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getValue())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getTotalParcel())) . ',';
//                $csv  .=   (str_replace($sepChar, "",$consignment->getDescription())) . ',';
//                $csv  .=   !empty($consignment->getDateLabelCreated()) ? formatDate(date("d-m-Y", $consignment->getDateLabelCreated())) : ''. ',';
////                $csv  .=   $dateDispatch. ',';
//                $csv  .=   ',';
//                $csv  .=   ',';
//                $csv  .=   ',';
//                $csv  .=   (cleanCsvCall($consignment->getServiceCode())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getServiceName())) . ',';
//                $csv  .=   (cleanCsvCall($consignment->getRemoteCharges())).',';
//                $csv  .=   "\r\n";
                if(count($ItemDetailFilterObjs) > 0) {
                    foreach($ItemDetailFilterObjs as $ItemDetailFilterObj) {
                        $detaiils = $ItemDetailFilterObj->getItemDetail();
                        if($detaiils != "") {
                            $detaiils = json_decode($detaiils);
                            if(count($detaiils) > 0) {
                                foreach($detaiils as $detaiil) {
                                    /* repeat */
                                    $csv  .=   (cleanCsvCall($consignment->getUserAccount())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getHawb(),'int')) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getReference())) . ',';
                                    $csv  .=   cleanCsvCall($consignment->getTrackingNumber(),'int'). ",";
                                    $csv  .=   (cleanCsvCall($consignment->getCompany())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getContact())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getAddressLine1())). ',';
                                    $csv  .=   (cleanCsvCall($consignment->getAddressLine2())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getAddressLine3())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getCity())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getPostCode())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getCountryName())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getTelephone())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getParcelWeight())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getValue())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getTotalParcel())) . ',';
                                    $csv  .=   (str_replace($sepChar, "",$consignment->getDescription())) . ',';
                                    $csv  .=   !empty($consignment->getDateLabelCreated()) ? formatDate(date("d-m-Y", $consignment->getDateLabelCreated())) : ''. ',';
                                    //                $csv  .=   $dateDispatch. ',';
                                    $csv  .=   ',';
                                    $csv  .=   ',';
                                    $csv  .=   ',';
                                    $csv  .=   (cleanCsvCall($consignment->getServiceCode())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getServiceName())) . ',';
                                    $csv  .=   (cleanCsvCall($consignment->getRemoteCharges())).',';
                                    $csv  .=   (cleanCsvCall($consignment->getIossNumber())).',';
                                    $csv  .=   (cleanCsvCall($consignment->getVatNumber())).',';
                                    /* repeat */
                                    $csv  .=   (cleanCsvCall($detaiil->item_description)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->item_url)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->item_sku)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->no_of_items)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->item_value)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->weight)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->tariff_no)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->hscode)) . ',';
                                    $csv  .=   (cleanCsvCall($detaiil->manufacture_country_iso)) . ',';
                                    $csv  .=   "\r\n";
                                }
                            }
                            else
                            {
                                $csv  .=   (cleanCsvCall($consignment->getUserAccount())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getHawb(),'int')) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getReference())) . ',';
                                $csv  .=   cleanCsvCall($consignment->getTrackingNumber(),'int'). ",";
                                $csv  .=   (cleanCsvCall($consignment->getCompany())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getContact())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getAddressLine1())). ',';
                                $csv  .=   (cleanCsvCall($consignment->getAddressLine2())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getAddressLine3())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getCity())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getPostCode())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getCountryName())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getTelephone())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getParcelWeight())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getValue())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getTotalParcel())) . ',';
                                $csv  .=   (str_replace($sepChar, "",$consignment->getDescription())) . ',';
                                $csv  .=   !empty($consignment->getDateLabelCreated()) ? formatDate(date("d-m-Y", $consignment->getDateLabelCreated())) : ''. ',';
                //                $csv  .=   $dateDispatch. ',';
                                $csv  .=   ',';
                                $csv  .=   ',';
                                $csv  .=   ',';
                                $csv  .=   (cleanCsvCall($consignment->getServiceCode())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getServiceName())) . ',';
                                $csv  .=   (cleanCsvCall($consignment->getRemoteCharges())).',';
                                $csv  .=   (cleanCsvCall($consignment->getIossNumber())).',';
                                $csv  .=   (cleanCsvCall($consignment->getVatNumber())).',';
                                $csv  .=   "\r\n";

                            }
                        }
                        else
                        {
                            $csv  .=   (cleanCsvCall($consignment->getUserAccount())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getHawb(),'int')) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getReference())) . ',';
                            $csv  .=   cleanCsvCall($consignment->getTrackingNumber(),'int'). ",";
                            $csv  .=   (cleanCsvCall($consignment->getCompany())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getContact())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getAddressLine1())). ',';
                            $csv  .=   (cleanCsvCall($consignment->getAddressLine2())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getAddressLine3())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getCity())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getPostCode())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getCountryName())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getTelephone())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getParcelWeight())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getValue())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getTotalParcel())) . ',';
                            $csv  .=   (str_replace($sepChar, "",$consignment->getDescription())) . ',';
                            $csv  .=   !empty($consignment->getDateLabelCreated()) ? formatDate(date("d-m-Y", $consignment->getDateLabelCreated())) : ''. ',';
            //                $csv  .=   $dateDispatch. ',';
                            $csv  .=   ',';
                            $csv  .=   ',';
                            $csv  .=   ',';
                            $csv  .=   (cleanCsvCall($consignment->getServiceCode())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getServiceName())) . ',';
                            $csv  .=   (cleanCsvCall($consignment->getRemoteCharges())).',';
                            $csv  .=   (cleanCsvCall($consignment->getIossNumber())).',';
                            $csv  .=   (cleanCsvCall($consignment->getVatNumber())).',';
                            $csv  .=   "\r\n";

                        }
                    }
                }
                else
                {
                    $csv  .=   (cleanCsvCall($consignment->getUserAccount())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getHawb(),'int')) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getReference())) . ',';
                    $csv  .=   cleanCsvCall($consignment->getTrackingNumber(),'int'). ",";
                    $csv  .=   (cleanCsvCall($consignment->getCompany())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getContact())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getAddressLine1())). ',';
                    $csv  .=   (cleanCsvCall($consignment->getAddressLine2())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getAddressLine3())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getCity())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getPostCode())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getCountryName())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getTelephone())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getParcelWeight())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getValue())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getTotalParcel())) . ',';
                    $csv  .=   (str_replace($sepChar, "",$consignment->getDescription())) . ',';
                    $csv  .=   !empty($consignment->getDateLabelCreated()) ? formatDate(date("d-m-Y", $consignment->getDateLabelCreated())) : ''. ',';
    //                $csv  .=   $dateDispatch. ',';
                    $csv  .=   ',';
                    $csv  .=   ',';
                    $csv  .=   ',';
                    $csv  .=   (cleanCsvCall($consignment->getServiceCode())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getServiceName())) . ',';
                    $csv  .=   (cleanCsvCall($consignment->getRemoteCharges())).',';
                    $csv  .=   (cleanCsvCall($consignment->getIossNumber())).',';
                    $csv  .=   (cleanCsvCall($consignment->getVatNumber())).',';
                    $csv  .=   "\r\n";

                }
            }
            $csvHeader = self::getHeader();
            $csvHeader .= ',Item Description,Item Url, Item SKU, No Of Item, Item Value, Weight,Traiff No, HS Code, Manufacture Country Iso';
            $folder_path = SETTING_DIR_ASSETS."manifest/csv/" ;
            $csvFilePath    =    date("Y_m_d")."/".$sessionUser->getUserAccountId();
            if (!file_exists($folder_path.$csvFilePath)) {
                mkdir($folder_path.$csvFilePath, 0777, true);
            }
            $uniqueFileName =  "item_" .time() . ".csv";
            $file_path    = $folder_path.$csvFilePath ."/".$uniqueFileName;
            $file_path_open = fopen($file_path, "w");
            fwrite($file_path_open, $csvHeader . "\r\n" . $csv);
            fclose($file_path_open);
            $output['STATUS'] = true;
            $output['FILE'] = $csvFilePath ."/".$uniqueFileName;
            $output['D_FILE'] = $file_path;
        }
        else{
            $output['STATUS'] = false;
            $output['MESSAGE'] = "No consignment found for csv";

        }
        return $output;
    }
    public static function generateManifestPdf($manifest=0, $manifestBy= 'client',$type="",$sessionUser="", $iossManifest = false )
    {
        $output =   array();
        if(empty($sessionUser))
            $sessionUser = SessionManager::getUser();

        $consignmentsItems = self::getManifestedConsignment($manifest, $manifestBy);
        /*
         * Check if the consignment has europe (R1) region and ioss field populated
         * if there then check consignment currency if its un euro then okay other vise convert it
         *  If its less than 150 euro (EUR) & ioss is there make separate pdf, and other ones in 2nd pdf
         */
        if(count($consignmentsItems)>0) {
            $iossNumberConData = [];
            $conData = [];
            $notIossConData = [];
            $iossConData = [];
            foreach ($consignmentsItems as $consignmentsItem) {
                if($consignmentsItem->getRegion() == "R1" && !empty($consignmentsItem->getIossNumber()) && $type !="return"){
                    // Check if currency is in euro otherwise convert it currency
                    if(strtolower($consignmentsItem->getCurrency()) == 'eur'){
                        $conValue = $consignmentsItem->getValue();
                    }else{
                        $conValue = Currency::convertCurrency($consignmentsItem->getCurrency(),"EUR",$consignmentsItem->getValue());
                    }
                    if($conValue < 150){
                        $iossNumberConData[] = $consignmentsItem;
                    }else{
                        $conData[] = $consignmentsItem;
                    }
                }else{
                    $conData[] = $consignmentsItem;
                }
            }
            $notIossConData = $conData;
            $iossConData = $iossNumberConData;
            $pdfFilePathIoss = "";
            $uniqueFileNameIoss = "";
            $file_pathIoss = "";
            $pdfFileIoss = "";
            $IossPdfFileLink = "";
            $pdfFilePath = "";
            $uniqueFileName = "";
            $file_path = "";
            $path = "";
            if(count($iossNumberConData) > 0){
                $folder_path = "../_assets/manifest/pdf/" ;
                $pdfFilePathIoss    =    date("Y_m_d")."/".$sessionUser->getUserAccountId();
                if (!file_exists($folder_path.$pdfFilePathIoss)) {
                    mkdir($folder_path.$pdfFilePathIoss, 0777, true);
                }
                $uniqueFileNameIoss = rand().time().".pdf";
                $file_pathIoss    = $folder_path.$pdfFilePathIoss ."/".$uniqueFileNameIoss;
                $manifestSummaryReport = new ManifestSummaryReport();
                $pdfFileIoss = $manifestSummaryReport->SavePDFFile($iossConData, $manifest,$file_pathIoss,$type,$sessionUser, true);
                $IossPdfFileLink = $pdfFilePathIoss ."/".$uniqueFileNameIoss;
            }
            if(!empty($notIossConData)){
                $folder_path = "../_assets/manifest/pdf/" ;
                $pdfFilePath    =    date("Y_m_d")."/".$sessionUser->getUserAccountId();
                if (!file_exists($folder_path.$pdfFilePath)) {
                    mkdir($folder_path.$pdfFilePath, 0777, true);
                }
                $uniqueFileName = time().".pdf";
                $path = $pdfFilePath ."/".$uniqueFileName;
                $file_path    = $folder_path.$pdfFilePath ."/".$uniqueFileName;
                $manifestSummaryReport = new ManifestSummaryReport();
                $pdf_file_name = $manifestSummaryReport->SavePDFFile($notIossConData, $manifest,$file_path,$type,$sessionUser, $iossManifest);
            }
                $output['STATUS'] = true;
                $output['FILE'] = $path;
                $output['D_FILE'] = $file_path;
                $output['IOSS_FILE'] = $IossPdfFileLink;
                $output['IOSS_D_FILE'] = $file_pathIoss;
        } else {
            $output['STATUS'] = false;
            $output['MESSAGE'] = "No consignment found for csv";

        }
        return $output;
    }
    public static function getHeader()
    {
        $record = "Account, Hawb No, Reference, Tracking Number, Company, Contact, Address1, Address2, Address3, City, Postcode, Country, Telephone, Weight, Value, Number Of Pieces,  Description, Date Created, Owner, Supplier, Service Code, Service Name, Remote Area, ioss_number, vat_number";
        return $record;
    }
    
    
    
    

    public static function AssignManifestNumberToList($list) {
        $manifestid = 0;

        $sessionUser = SessionManager::getUser();
        $manifest = new Manifest();
        $manifest->setPieces(count($list));
        //$manifest->setAgent($this->form_vars["agent"]);
        //$manifest->setWeight($this->form_vars["weight"]);			
        $manifest->setDateCreated(time());
        $manifest->setAccount($sessionUser->getAccount());
        //$manifest->setFileName($file_path);	

        $manifest->setType($type);
        //$pdf_file_name = ManifestSummaryReport::SavePDFFile($list, $manifestid);		
        //$manifest->setPdfFile($pdf_file_name);	
        $manifest->save();
        $manifestid = $manifest->getId();

        $conIdArray = array();
        $servArray = array();


        foreach ($list as $consignment) {

            $manifestArray[$NumberofUniqueCode]['manifestid'] = $manifestid;
            $manifestArray[$NumberofUniqueCode]['consignmentid'] = $consignment->getId();
            $conIdArray[] = $consignment->getId();
            $NumberofUniqueCode++;
        }
        if (count($conIdArray) > 0) {
            $strConsignmentId = implode(",", $conIdArray);

            if ($sessionUser->getIsProduct() == 'YES') {

                $result = Consignment::GetDistinctHandlingFromConsignment($strConsignmentId, "service_type");
                foreach ($result as $res) {
                    $servArray[] = $res['service_type'];
                }
            } else {
                $result = Consignment::GetDistinctHandlingFromConsignment($strConsignmentId, "handling");
                foreach ($result as $res) {
                    $servArray[] = $res['handling'];
                }
            }

            $manifest->setHandling(implode(",", $servArray));
            $manifest->save();
        }

        if (count($manifestArray) > 0) {
            $ManifestConsignmentMapping = new ManifestConsignmentMapping();
            $ManifestConsignmentMapping->bulkDataInsert($manifestArray);
        }

        return $manifestid;
    }

    public static function ManifestSelected() {
        $user = SessionManager::getUser();

        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addAccountFilter($user->getId());
        $fromDate = date("Y-m-d", strtotime("-1 month"));
        $dateTo = date("Y-m-d");
        $consignmentFilter->addDateFilter($fromDate, date("Y-m-d"), 'printed');
        $consignmentFilter->addIsCustomerManifested();
        //$consignmentFilter->addEndOfDayIsNull();
        //$consignmentFilter->addDateBookedNotSet();
        $consignmentFilter->addStatusFilter(Consignment::STATUS_RECEIVED);
        $_SESSION["consignment_filter"] = $consignmentFilter;

        util_redirect("../main/client_list.php?show=manifest_selected");
    }

    /*
      public function ManifestIndividualShipment($ConIdArray)
      {
      $user = SessionManager::getUser();

      if(count($ConIdArray) > 0)
      {
      $strConsignmentId = implode(",", $ConIdArray);
      $consignmentFilter = new ConsignmentFilter();
      $consignmentFilter->addIdFilter($strConsignmentId);
      $consignmentFilter->getColumnList("id, awb");
      }


      /*
      $consignmentFilter->addAccountFilter($user->getAccount());
      $fromDate = date("Y-m-d", strtotime("-1 month"));
      $dateTo = date("Y-m-d");
      $consignmentFilter->addDateFilter($fromDate, date("Y-m-d"), 'printed');
      $consignmentFilter->addEndOfDayIsNull();
      //$consignmentFilter->addDateBookedNotSet();
      $consignmentFilter->addStatusFilter(Consignment::STATUS_RECEIVED);
      $_SESSION["consignment_filter"] = $consignmentFilter;
      //util_redirect("../main/client_list.php?show=manifes_selected");

      }
     */

    public static function CreateBoxes($manifestId, $numberOfBoxes) {

        $user = SessionManager::getUser();
        $manifestDataFilter = new ManifestConsignmentDataFilter();
        $manifestDataFilter->addManifestIDFilter($manifestId);
        $manifestConList = $manifestDataFilter->getList();

        $manifest = new Manifest($manifestId);
        $manifest->setNumberBag(Manifest::GetNumberOfBoxes($numberOfBoxes));
        $manifest->save();

        $totalWeight = 0;
        $totalPieces = 0;
        $maxAllowedWeight = 30; // Weight in Kg.
        $codeArray = array();
        $serviceArray = array();
        $country = "";


        foreach ($manifestConList as $manifest) {
            $consignment = new Consignment($manifest->getConsignmentId());
            $totalWeight += $consignment->getWeight();
            $servicename = $consignment->getServiceType();
            $code = $consignment->getHandling();


            if (!in_array($code, $codeArray))
                $codeArray[] = $code;

            if (!in_array($servicename, $serviceArray))
                $serviceArray[] = $servicename;
        }

        if (count($manifestConList) > 0) {
            for ($i = 0; $i < count($numberOfBoxes); $i++) {
                //echo "Pieces " . $numberOfBoxes[$i]['pieces'];

                $totalPieces = $numberOfBoxes[$i]['number_boxes'];

                for ($j = 1; $j <= $totalPieces; $j++) {
                    $mixbag = "MIX";
                    $bagging = new Bagging();
                    $bagging->setDateCreated(time());
                    $bagging->setDateUpdated(time());
                    $bagging->setAccount($user->getUserAccount());
                    $bagging->setService(implode(",", $serviceArray));
                    $bagging->setLength($numberOfBoxes[$i]['length']);
                    $bagging->setWidth($numberOfBoxes[$i]['width']);
                    $bagging->setHeight($numberOfBoxes[$i]['height']);
                    $bagging->setBagStatus(1);
                    $bagging->setBagType($mixbag);
                    $bagging->setManifestId($manifestId);
                    $bagging->save();
                }
            }
        }

        //$this->open_bag_list = $this->openBagList();		
        return $manifestId;
    }

    public static function ManifestAll($userid="") {
        $manifestId = 0;
        $user = SessionManager::getUser();
        $consignmentFilter = new ConsignmentFilter();
        if($userid!= '' && $userid > 0)
            $consignmentFilter->addAccountFilter($userid);
        else
            $consignmentFilter->addAccountFilter($user->getId());
        $fromDate = date("Y-m-d H:i:s", strtotime("-1 month"));
        $dateTo = date("Y-m-d H:i:s");
        $consignmentFilter->addDateFilter($fromDate, $dateTo, 'printed');
        $consignmentFilter->addIsCustomerManifested();
        $consignmentFilter->addStatusFilterIn(Consignment::STATUS_LABEL_CREATED);
        $list = $consignmentFilter->getColumnList("awb");
        $arr_update_scan_numbers = array();
        if(count($list) > 0)
        {
            foreach ($list as $consignment) {
                if ($consignment->getId() > 0)
                    $arr_update_scan_numbers[] = $consignment->getId();
            }
        }
        if (count($arr_update_scan_numbers) > 0) {
            $strTrackingNumbers = implode("','", $arr_update_scan_numbers);
            $set_update_columns = " is_customer_manifested = 1";
            $where = "id IN ('$strTrackingNumbers')";
            $manifestId = TrackingData::SendEmail($arr_update_scan_numbers, $user);
            Consignment::bulkUpdate($set_update_columns, $where);
        }
        return $manifestId;
    }
    public function getScannedManifestByDate($userAccountId,$date="",$op="") {
            $sql = "SELECT 
                        count(m.`id`) AS id ,m.date_created
                      FROM
                        `manifest` m 
                        JOIN `user` u 
                          ON u.`id` = m.`user_id` 
                      WHERE m.`is_dispatched` = 'y' 
                        AND u.`user_account_id` = '".DbAccess3::escape($userAccountId)."' AND DATE(m.date_created) ".$op." '".$date."' GROUP BY DATE(m.date_created) ";
		return Manifest::getManifestListFromSql($sql);
	}
    public static function getTotalNumberOfManifestFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function getManifestedConsignment($manifestId,$manifestBy, $groupBy = "c.id") {
        if($manifestBy == 'client') {
            $serviceid  =   'IF( c.customized_service_id > 0, customized_service_id, c.service_id) as service_id';
        } else {
            $serviceid  =   'c.service_id';
        }
        $sql = "SELECT
                      SUM(p.`weight`) AS parcel_weight,
                      COUNT(mem.id) AS total_parcel,
                      c.id,
                      c.hawb,
                      c.reference,
                      c.company,
                      c.contact,
                      c.address_line_1,
                      c.address_line_2,
                      c.address_line_3,
                      c.city,
                      c.postcode,
                      c.country_id,
                      c.telephone,
                      c.weight,
                      c.number_pieces,
                      c.description,
                      c.currency,
                      c.user_id,
                      c.ioss_number,
                      ".$serviceid.",
                      c.awb,
                      c.value,
                      c.date_label_created,
                      c.date_booked,
                      c.remote_charges,
                      s.name AS 'service_name',
                      s.code AS 'service_code',  
                      cnt.name AS 'country_name',
                      cnt.region AS 'region',
                      ua.user_account,
                      p.`tracking_number`,
                      p.`id` AS parcel_id,
                      c.ioss_number,
                      c.eori_number,
                      c.vat_number
                    FROM
                      `manifest_entity_mapping` mem
                      JOIN parcel p
                        ON p.id = mem.`entity_id`
                      JOIN consignment c
                        ON c.id = p.`consignment_id`
                      JOIN `user` u
                        ON u.id = c.`user_id`
                      JOIN customer_account ua
                        ON ua.`id` = u.`user_account_id`
                      JOIN services s
                        ON s.`id` = c.`service_id`
                      JOIN country cnt
                        ON cnt.`id` = c.`country_id`
                    WHERE mem.`manifest_id` = '".DbAccess3::escape($manifestId)."'
                      AND mem.`manifest_entity_type` = 'p'
                    GROUP BY " . $groupBy;

        $consignmentsItems = Consignment::getConsignmentListFromSql($sql);

        /*$consignmentData    =   new ConsignmentFilter();
        $consignmentData->addFilter(" AND pc.id in  (SELECT entity_id  from manifest_entity_mapping WHERE  manifest_entity_type = 'p' and manifest_id = '".$manifestId."')", "parcelJoinFilter");
        $consignmentsItems  =   $consignmentData->getColumnList(
            "ua.user_account,
                    c.hawb, c.reference, pc.tracking_number,sum(pc.weight) as parcel_weight,count(pc.id) as total_parcel, c.company, c.contact, c.address_line_1, c.address_line_2
                    , c.address_line_3, c.city, c.postcode,c.country_id, c.telephone, c.weight,  c.number_pieces,  c.description,c.currency, 
                    c.user_id, ".$serviceid.",c.awb,c.value, c.date_label_created, c.date_booked,s.name 'service_name', s.code 'service_code',  c.remote_charges, con.name 'country_name' ",500,true);*/

        return $consignmentsItems;
    }
    
    public static function DeleteManifestById($manifestId) {
        $sqlManifest =  "DELETE
                            FROM `manifest`
                         WHERE id = '".DbAccess3::escape($manifestId)."'";
        DbAccess3::runQuery($sqlManifest);
        $sqlManifestEntityMapping =  "DELETE
                                        FROM `manifest_consignment_mapping`
                                      WHERE manifest_id = '".DbAccess3::escape($manifestId)."'";
        DbAccess3::runQuery($sqlManifestEntityMapping);
        return;
    }

}
