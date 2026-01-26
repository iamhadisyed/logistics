<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'include_list',
    ], 'reamus');
include_classes([
    'ups.class'
    ], 'labels');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'currency.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'serviceconstantvaluefilter.class',
    'serviceconstantvalue.class',
    'servicerangemappingfilter.class',
    'servicerangemapping.class',
    'licenceplatefilter.class',
    'licenceplate.class',
    'warehousefilter.class',
    'warehouse.class',
    'trackingfilter.class',
    'tracking.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    'sku.class',
    'skufilter.class',
    'skuorder.class',
    'skuorderfilter.class',
    'skuboxdetail.class',
    'skuboxdetailfilter.class',
    'itemdetail.class',
    'itemdetailfilter.class'
    ]);
class Page extends BasePage {
    public $user;
    public $userAccount;
    public $serviceId = 0;
    public $parcelData = "";
    public $itemData = "";
    public $labelGenreatedCheck = "";
    public $consignmentEditError = "";
    public $consignmentStatus = "";
    public $isInsurance = "";
    public $isPaperlessInvoice = "";
    public $invoiceFile = "";
    public $skuorder = "";
    protected function init() {
        $this->user = SessionManager::getUser();
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Create/Update Shipment"
        );
        /*
         * Add Consignment Data Logic
         */
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "save_data_consignment") {
            
            $output = array();
            $shipmentUserId = "";
            $userParams = (trim(util_get('uaccount')) != '') ? "uaccount=" . util_get('uaccount') : "";
            if(Permissions::checkFilePermission('CREATE_OTHER_USER_SHIPMENT')){
                
                 $shipmentUserId = (int) $this->form_vars["shipmentUserId"];
                 if($shipmentUserId > 0){
                    $userParams = "uaccount=". base64_encode($shipmentUserId);
                 }
            }
            else
            {
                $userId = (int) (trim(util_get('uaccount')) != '') ? base64_decode(util_get('uaccount')) : $this->user->getId();
            }
           
            if(Permissions::checkFilePermission('CREATE_OTHER_USER_SHIPMENT')){
                
                 $shipmentUserId = (int) $this->form_vars["shipmentUserId"];
                 if($shipmentUserId > 0){
                    $userParams = "uaccount=". base64_encode($shipmentUserId);
                 }
            }
            else
            {
                $userId = (int) (trim(util_get('uaccount')) != '') ? base64_decode(util_get('uaccount')) : $this->user->getId();
            }
            $userId = (int) (trim(util_get('uaccount')) != '') ? base64_decode(util_get('uaccount')) : $this->user->getId();
            //$userId = (int) (trim(util_get('user_id')) != '') ? base64_decode(util_get('user_id')) : $this->user->getId();
            
            $output = Consignment::saveShipment($this->form_vars, $userId, false, $userParams, 'web', $shipmentUserId);

            echo json_encode($output);
            die;
        }
        /*
         * save Item details info
         */
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "save_itemdetail") {    
            $pieceNumber = $this->form_vars["piecenumber"];
            $itemArray = $this->form_vars["item"];
            $consignment_id  = $this->form_vars["consignment_id"];
            if(count($itemArray) > 0){
                $errorArray = array();
                $count = 1;
                $itemDetailArray = array();
                foreach($itemArray as $item){
                   if($item['item_qty'] != "" && !is_numeric($item['item_qty'])){
                        $errorArray[] = 'Please enter item '.$count.' valid quantity.';
                   }
                   if($item['item_value'] != "" && !is_numeric($item['item_value'])){
                        $errorArray[] = 'Please enter item '.$count.' valid value.';
                   }
                   if($item['item_weight']!= "" && !is_numeric($item['item_weight'])){
                        $errorArray[] = 'Please enter item '.$count.' valid weight.';
                   }
                   if ($item['item_url']!= "" && !filter_var($item['item_url'], FILTER_VALIDATE_URL))
                        $errorArray[] = 'Please enter valid url.';
                   
                    
                    $itemArr["item_description"] = $item['item_description'];
                    $itemArr["item_url"] = $item['item_url'];
                    $itemArr["item_sku"] = $item['item_sku'];

                    $itemArr["no_of_items"] =$item['item_qty'] ;
                    $itemArr["item_value"] = $item['item_value'];
                    $itemArr["weight"] = $item['item_weight'];
                    $itemArr["tariff_no"] = "";
                    $itemArr["hscode"] = $item['item_hscode'];
                    $itemArr["manufacture_country_iso"] =$item['item_country'] ;
                    $itemDetailArray[] = $itemArr;
                   $count++;
                }
                if(count($errorArray) > 0){
                    $outputArray['STATUS'] = "ERROR";
                    $outputArray['MESSAGE'] = implode( '<br>', $errorArray);
                    echo json_encode($outputArray);
                    die;
                }
            }
            
            $session_id = session_id();
            $itemDetailFilter = new ItemDetailFilter();
            
            if($consignment_id > 0){
                $itemDetailFilter->addFilter(" consignment_id = '". $consignment_id."' and parcel_count='".$pieceNumber."'");
            }
            else
            {
                $itemDetailFilter->addFilter(" session_id = '". $session_id."' and parcel_count='".$pieceNumber."'");
            }
            
            $itemDetailList = $itemDetailFilter->getList();
            if(count($itemDetailList) > 0){
                $itemIdArray = array();
                foreach($itemDetailList as $itemDetail){
                    $itemDetail->setParcelCount($pieceNumber);
                    $itemDetail->setItemDetail(json_encode($itemDetailArray));
                    $itemDetail->setUserId($this->user->getId());
                    $itemDetail->save();
                    //$itemIdArray[] = $item->getId();
                }
                /*if(count($itemIdArray) > 0){
                    $deleteRecordSql = "DELETE FROM item_details where  id IN ('". implode("','", $itemIdArray)."')";
                    $itemDetail = new ItemDetail();
                    $itemDetail->deleteItemDetailFromSql($deleteRecordSql);
                }*/
            }
            else
            {
            
                $itemDetail = new ItemDetail();
                $itemDetail->setConsignmentId('0');
                $itemDetail->setSessionId($session_id);
                $itemDetail->setParcelCount($pieceNumber);
                $itemDetail->setItemDetail(json_encode($itemDetailArray));
                $itemDetail->setUserId($this->user->getId());
                $itemDetail->save();
            }
            $output["STATUS"] = "SUCCESS";
            echo json_encode($output);
            die;
        }
        
        /*
         * edit item detail and re-populate item details
         */
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "edit_itemdetail") {
            $session_id = session_id();
            $parcelCount = $this->form_vars["parcelcount"];
            $consignmentId = $this->form_vars["consignment_id"];
            $skuid = base64_decode($this->form_vars["skuid"]);
            $itemDetailFilter = new ItemDetailFilter();
            if($consignmentId > 0){
                $itemDetailFilter->addFilter(" consignment_id = '". $consignmentId."' and parcel_count='".$parcelCount."'");
            }
            else{
                $itemDetailFilter->addFilter(" session_id = '". $session_id."' and parcel_count='".$parcelCount."'");
            }
            $itemDetailList = $itemDetailFilter->getList();
            $output = array();

            if(count($itemDetailList) > 0){
                $itemArray = array();
                $itemDiv = "";
                $totalItemCount = 0;
                foreach($itemDetailList as $item){
                    $itemDetail =  $array = json_decode($item->getItemDetail(), true);
                    $itemIndex = 0;
                    if(count($itemDetail) > 0){
                        foreach($itemDetail as $it){
                            
                            $itemDiv .= '<div class="form-group">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input type="text"  name="item['.$itemIndex.'][item_description]" value="'. $it['item_description'].'" placeholder="Description"  class="form-control item_description"  rel="tooltip" title="Description"  required />
                                                </div>';
                            if($skuid != '' ){

                                $itemDiv .= '<div class="col-md-1">';
                                $itemDiv .= Ddl::generateDDL('item['.$itemIndex.'][item_sku]', "skuFilter", ' id in (select sku_id from sku_order_mapping where sku_order_id = "'.$skuid.'")', "sku", "id", $it['item_sku'], ' class="bs-select input-sm form-control form-filter item_sku" data-container="body" data-live-search="true"  data-show-subtext="true"', 'Select SKU', '', 'item_sku_'.$itemIndex);
                                $itemDiv .= '</div>';
                            }
                            else
                            {
                                $itemDiv .=     '<div class="col-md-1">
                                                    <input type="text"  name="item['.$itemIndex.'][item_sku]"  value="'. $it['item_sku'] .'"  placeholder="SKU"  class="form-control item_sku"  rel="tooltip" title="SKU"  />
                                                </div>';
                            }

                                $itemDiv  .=    '<div class="col-md-2">
                                                    <input type="text"  name="item['.$itemIndex.'][item_url]"   value="'. $it['item_url'] .'"   placeholder="URL"  class="form-control item_url"  rel="tooltip" title="URL"  />
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"  name="item['.$itemIndex.'][item_qty]" value="'. ($it['item_qty']== "" ? $it['no_of_items'] : $it['item_qty']) .'"  placeholder="Quantity"  class="form-control item_qty"  rel="tooltip" title="Quantity"   />
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"  name="item['.$itemIndex.'][item_value]" value="'.$it['item_value'] .'"  placeholder="Value"  class="form-control item_value"  rel="tooltip" title="Value"   />
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"  name="item['.$itemIndex.'][item_weight]" value="' . ($it['item_weight']== "" ? $it['weight'] : $it['item_weight']) .'"  placeholder="Weight"  class="form-control item_weight"  rel="tooltip" title="Weight"   />
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"  name="item['.$itemIndex.'][item_hscode]" value="'. ($it['item_hscode']== "" ? $it['hscode'] : $it['item_hscode']).'"  placeholder="HS Code"  class="form-control item_hscode"  rel="tooltip" title="HS Code"   />
                                                </div>
                                                <div class="col-md-2">';
                                                    $manufactureCountry = ($it['item_country']== "" ? $it['manufacture_country_iso'] : $it['item_country']);
                                                    if(is_numeric($manufactureCountry)){
                                                        $countryList = new Country($manufactureCountry);
                                                        $manufactureCountry = $countryList->getIso();
                                                    }
                                               $itemDiv .=     Ddl::generateCountryDDL('item['.$itemIndex.'][item_country]', $manufactureCountry, 'iso', ' class="not_clear item_country form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" ', 'item_country_'.$itemIndex) 
                                                .'</div>

                                          <div class="col-md-1">';
                                          if((count($itemDetail) -1 ) == ($itemIndex)){
                                          $itemDiv .= '<button type="button" class="btn btn-success add-more-item-keys"><i class="fa fa-plus"></i></button>
                                                    <button type="button" class="btn btn-danger remove-item-key initialitem-button"><i class="fa fa-minus"></i></button>';
                                          }else
                                          {
                                              $itemDiv .= '<button type="button" class="btn btn-danger remove-item-key initialitem-button"><i class="fa fa-minus"></i></button>';
                                          }
                                           $itemDiv .= '</div>
                                            </div>
                                        </div>';


                            $itemIndex++;
                            $totalItemCount++;
                        }
                    }
                    else
                    {
                        $itemIndex = 0;
                        $itemDiv .= '<div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <input type="text"  name="item['.$itemIndex.'][item_description]" value="" placeholder="Description"  class="form-control item_description"  rel="tooltip" title="Description"  required />
                                </div>';
                                if($skuid != '' ){
                                    $itemDiv .= '<div class="col-md-1">';
                                    $itemDiv .= Ddl::generateDDL('item['.$itemIndex.'][item_sku]', "skuFilter", ' id in (select sku_id from sku_order_mapping where sku_order_id = "'.$skuid.'")', "sku", "id", '', ' class="bs-select input-sm form-control form-filter item_sku" data-container="body" data-live-search="true"  data-show-subtext="true"', 'Select SKU', '', 'item_sku_'.$itemIndex);
                                    $itemDiv .= '</div>';
                                }
                                else
                                {
                                    $itemDiv .=     '<div class="col-md-1">
                                                        <input type="text"  name="item['.$itemIndex.'][item_sku]"  value=""  placeholder="SKU"  class="form-control item_sku"  rel="tooltip" title="SKU"  />
                                                    </div>';
                                }

                                $itemDiv  .=    '<div class="col-md-2">
                                    <input type="text"  name="item['.$itemIndex.'][item_url]"   value=""   placeholder="URL"  class="form-control item_url"  rel="tooltip" title="URL"  />
                                </div>
                                <div class="col-md-1">
                                    <input type="text"  name="item['.$itemIndex.'][item_qty]" value=""  placeholder="Quantity"  class="form-control item_qty"  rel="tooltip" title="Quantity"   />
                                </div>
                                <div class="col-md-1">
                                    <input type="text"  name="item['.$itemIndex.'][item_value]" value=""  placeholder="Value"  class="form-control item_value"  rel="tooltip" title="Value"   />
                                </div>
                                <div class="col-md-1">
                                    <input type="text"  name="item['.$itemIndex.'][item_weight]" value=""  placeholder="Weight"  class="form-control item_weight"  rel="tooltip" title="Weight"   />
                                </div>
                                <div class="col-md-1">
                                    <input type="text"  name="item['.$itemIndex.'][item_hscode]" value=""  placeholder="HS Code"  class="form-control item_hscode"  rel="tooltip" title="HS Code"   />
                                </div>
                                <div class="col-md-2">';
                                $itemDiv .=     Ddl::generateCountryDDL('item['.$itemIndex.'][item_country]', '', 'iso', ' class="not_clear item_country form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" ', 'item_country_'.$itemIndex) 
                                .'</div>

                                <div class="col-md-1">';

                                $itemDiv .= '<button type="button" class="btn btn-success add-more-item-keys"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger remove-item-key initialitem-button"><i class="fa fa-minus"></i></button>';

                                $itemDiv .= '</div>
                            </div>
                        </div>';
                        $itemDiv .= '<input type="hidden" name="consignment_id" value="'.$consignmentId.'" />';
                        $totalItemCount = 1;
                    }
                    $itemDiv .= '<input type="hidden" name="consignment_id" value="'.$consignmentId.'" />';
                }
                $output["STATUS"] = "SUCCESS";
                $output["ITEM_DETAILS"] = $itemDiv;
                $output["ITEM_COUNT"] = $totalItemCount;
            }
            else
            {
                $output["STATUS"] = "ERROR";
            }
            echo json_encode($output);
            die;
        }
        
        
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "get_drop_off_data") {
            $country = new Country($this->form_vars["sender_country_id"]);
            $iso = $country->getIso();
            $output = [];
            $drop_off = [];
            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->form_vars["user_id"]);
            $dropoffFilter->addFieldFilter("country", $iso);
            $dropoffFilter->addFieldFilter("service_id", $this->form_vars["service_id"]);
            $dropoffFilter->orderBySort("added_date desc");
            $dropoffList = $dropoffFilter->getList('1');
            $lat = "";
            $lng = "";
            if(count($dropoffList) > 0) {
                $dropOffData = $dropoffList[0];
                $drop_off['company_name'] = $dropOffData->getCompanyname();
                $drop_off['address_line_1'] = $dropOffData->getAddressLine1();
                $drop_off['address_line_2'] = $dropOffData->getAddressLine2();
                $drop_off['address_line_3'] = $dropOffData->getAddressLine3();
                $drop_off['city'] = $dropOffData->getCity();
                $drop_off['postcode'] = $dropOffData->getPostcode();
                $drop_off['telephone'] = $dropOffData->getTelephone();
                $drop_off['lat'] = $dropOffData->getLat();
                $drop_off['lng'] = $dropOffData->getLng();
            }
            $output['drop_off'] = $drop_off;
             echo json_encode($output);
            die;
        }
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "map_load") {
            $output = [];
            $location = [];
            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->user->getId());
            $dropoffFilter->addFieldFilter("service_id", $this->form_vars["service_id"]);
            $dropoffFilter->orderBySort("added_date desc");
            $dropoffList = $dropoffFilter->getList('1');
            $lat = "";
            $lng = "";
            if (count($dropoffList) > 0) {
                $dlist = $dropoffList[0];
                $lat = $dlist->getLat();
                $lng = $dlist->getLng();
            }
            $postcode = str_replace(" ", "", $this->form_vars["from_post_code"]);
            $city = str_replace(" ", "", $this->form_vars["to_city"]);
            $countryId = str_replace(" ", "", $this->form_vars["from_country_id"]);
            if($countryId > 0){
                $country = new Country($countryId);
                $countryIso = $country->getIso();
            }
            // On call Mruga told me that you can get calls name from service table by just service id not agent id
            $serviceObj = new Services($this->form_vars["service_id"]);
           $className = trim($serviceObj->getLabelClassName());
            $calssFound = false;
            if (trim($className) != '') {
                $file = "../includes/labels/" . strtolower($className) . ".class.php";
                if (is_file($file)) {
                    require_once($file);
                    $calssFound = true;
                }
            }
//            $upslabel = new UPS();
            if($calssFound){
                $classObject = new $className();
                $dropofflocationresponse = $classObject->getDropOffLocation($postcode, $countryIso, $city);
                if (sizeof($dropofflocationresponse) > 0) {
                    $index = 1;
                    foreach ($dropofflocationresponse as $dropoff) {
                        $selected = 0;
                        if ($lat == $dropoff['lat'] && $lng == $dropoff['lng']) {
                            $selected = 1;
                        }
                        $address = $dropoff['addressline1'] ." ". $dropoff['addressline2'];
                        $addressLen = strlen($address );
                        $addressline1 = "";
                        $addressline2 = "";
                        if($addressLen > 25){
                            $addsplit = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $address, -1, PREG_SPLIT_NO_EMPTY);
                            $addline1Len = 0;
                            foreach($addsplit as $add){
                                $addline1Len = strlen($addressline1) + strlen($add);
                                if($addline1Len < 25)
                                    $addressline1 .= $add . " ";
                                else {
                                    $addressline2 .= $add . " ";
                                }
                                
                            }
                        }
                        else
                        {
                            $addressline1 = $dropoff['addressline1'];
                        }
                        
                        $location[] = array('companyname' => $dropoff['companyname'],
                            'addressline1' => $addressline1,
                            'addressline2' => $addressline2,
                            'addressline3' => $dropoff['addressline3'],
                            'city' => $dropoff['city'],
                            'postcode' => $dropoff['postcode'],
                            'country' => $dropoff['country'],
                            'telephone' => $dropoff['telephone'],
                            'lat' => $dropoff['lat'],
                            'lng' => $dropoff['lng'],
                            'Mon' => $dropoff['Mon'],
                            'Tue' => $dropoff['Tue'],
                            'Wed' => $dropoff['Wed'],
                            'Thu' => $dropoff['Thu'],
                            'Fri' => $dropoff['Fri'],
                            'Sat' => $dropoff['Sat'],
                            'Sun' => $dropoff['Sun'],
                            'branchId' => $dropoff['branchId'],
                            'selected' => $selected,
                            'index' => $index);
                        $index++;
                    }
                }
            }
           
//            $serviceObj = new services($this->form_vars["service_id"]);
            $carrierId = $serviceObj->getCarrierId();
            $carrierObj = new Carrier($carrierId);
            $carrierLogo = "one-world.png";
            if(file_exists('../images/carrierlogo/thumbnail/owe_16_'.$carrierObj->getLogo())){
				$carrierLogo = $carrierObj->getLogo();
			}else{
				$carrierLogo = "one-world.png";
			}
            $output['location_data'] = $location;
            $output['carrier_logo'] = $carrierLogo; //$carrierObj->getLogo();
            $output['carrier_name'] = $carrierObj->getCarrier();
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "DROPOFF_SELECTED_SERVICE") {
             $companyname = utf8_decode($this->form_vars["companyname"]);
             $addressline1 = utf8_decode($this->form_vars["addressline1"]);
             $addressline2 = utf8_decode($this->form_vars["addressline2"]);
             $addressline3 = utf8_decode($this->form_vars["addressline3"]);
             $city = utf8_decode($this->form_vars["city"]);
             $postcode = $this->form_vars["postcode"];
             $country = $this->form_vars["country"];
             $telephone = $this->form_vars["telephone"];
             $mon = $this->form_vars["mon"];
             $tue = $this->form_vars["tue"];
             $wed = $this->form_vars["wed"];
             $thu = $this->form_vars["thu"];
             $fri = $this->form_vars["fri"];
             $sat = $this->form_vars["sat"];
             $sun = $this->form_vars["sun"];
             $carrierlogo = $this->form_vars["carrierLogo"];
             $carriername = $this->form_vars["carrierName"];
             $lat = $this->form_vars["lat"];
             $lng = $this->form_vars["lng"];
             $serviceId = $this->form_vars["serviceId"];
             $location = $this->form_vars["location"];
             $branchId = $this->form_vars["branchId"];                     
            // Save Drop Off Data
            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->user->getId());
            $dropoffFilter->addFieldFilter("service_id", $serviceId);
            $dropoffFilter->addFieldFilter("country", $country);
            $dropoffFilter->addFieldFilter("companyname", $companyname);
            $dropoffList = $dropoffFilter->getList();
            if(count($dropoffList) == 0)
            {
                $dropUserLocation = new DropoffUserLocation();
                $dropUserLocation->setUserId($this->user->getId());
                $dropUserLocation->setServiceId($serviceId);
                $dropUserLocation->setCompanyname($companyname);
                $dropUserLocation->setAddressLine1($addressline1);
                $dropUserLocation->setAddressLine2($addressline2);
                $dropUserLocation->setAddressLine3($addressline3);
                $dropUserLocation->setCity($city);
                $dropUserLocation->setPostcode($postcode);
                $dropUserLocation->setCountry($country);
                $dropUserLocation->setTelephone($telephone);
                $dropUserLocation->setMon($mon);
                $dropUserLocation->setTue($tue);
                $dropUserLocation->setWed($wed);
                $dropUserLocation->setThu($thu);
                $dropUserLocation->setFri($fri);
                $dropUserLocation->setSat($sat);
                $dropUserLocation->setSun($sun);
                $dropUserLocation->setAddedDate(strtotime(date('Y-m-d H:i:s')));
                $dropUserLocation->setAddedBy($this->user->getId());
                $dropUserLocation->setLat($lat);
                $dropUserLocation->setLng($lng);
                $dropUserLocation->save();
            }
              $output .= '<div class="row">
                            <h4><span id="rangeName"></span><img src="../images/carrierlogo/thumbnail/owe_100_'.$carrierlogo.'" /> '.$carriername  .'</h4>';
              $output .= '<div class="col-md-12">';
              $output .= '<label><h3 class="drop_off_data" id="drop_off_company">'.$companyname.'</h3></label><br />';
              $output .= '<label class="drop_off_data" id="drop_off_address_line_1" data-branchId="'.$branchId.'" data-city="'.$city.'">'.$addressline1.'</label><br />';
              $output .= '<label class="drop_off_data" '.($addressline2 == "" ? ' style="display: none;" ' : '').' id="drop_off_address_line_2">'.$addressline2.'</label><br />';
              $output .= '<label class="drop_off_data" '.($addressline3 == "" ? ' style="display: none;" ' : '').' id="drop_off_address_line_3">'.$addressline3.'</label>';
              $output .= '<label class="drop_off_data" '.($city == "" ? ' style="display: none;" ' : '').' id="drop_off_city">'.$city.'</label>';
              $output .= '<label class="drop_off_data" id="drop_off_postcode_select">'.$postcode.'</label>,';
              $output .= '<label class="drop_off_data" id="drop_off_country">'.$country.'</label>';
              $output .= '<label class="drop_off_data" '.($country == "" ? '' : 'style="display: block;" ').' id="drop_off_telephone">'.$telephone.'</label> <label class="drop_off_data" id="drop_off_location">'.$location.'</label>';
              $output .= '</div><div class="col-md-12">';
              if($mon != '' || $tue != '' || $wed != '' || $thu != '' || $fri != '' || $sat != '' || $sun != '')
                $output .= '<label><h3><b>Opening Hours</b></h3></label></div>';
              if($mon != '')
                  $output .= '<div  class="col-md-12">Mon</div><div  class="col-md-10">'.$mon.'</div>';
              if($tue != '')
                $output .= '<div  class="col-md-12">Tue</div><div  class="col-md-10">'.$tue.'</div>';
              if($wed != '')
                $output .= '<div  class="col-md-12">Wed</div><div  class="col-md-10">'.$wed.'</div>';
              if($thu != '')
                $output .= '<div  class="col-md-12">Thu</div><div  class="col-md-10">'.$thu.'</div>';
              if($fri != '')
                $output .= '<div  class="col-md-12">Fri</div><div  class="col-md-10">'.$fri.'</div>';
              if($sat != '')
                $output .= '<div  class="col-md-12">Sat</div><div  class="col-md-10">'.$sat.'</div>';
              if($sun != '')
                $output .= '<div  class="col-md-12">Sun</div><div  class="col-md-10">'.$sun.'</div>';
              $storedetails = "companyname:".$companyname.",addressline1:".$addressline1.",addressline2:".$addressline2.",addressline3:".$addressline3.",city:".$city.",postcode:".$postcode.",country:".$country.",telephone:".$telephone;
              $base64storedetails = base64_encode($storedetails);
              $output .= '</div><div style="clear:both"><br /></div><div class="col-md-6">'
                      . '<a  data-store-detail="' . $base64storedetails . '" id="btn_select_location" class="btn btn-sm blue">'
                      . '<span></span>&nbsp;Select Location</a></div>';
              $output .=      '</div>';
            echo $output;
            exit;
         }
        // Get country services
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "get_country_services") {
            /*
             * Get variable from POST
             */
            $userId = $this->form_vars["user_id"];
            $fromCountry = $this->form_vars["from_country"];
            $toCountry = $this->form_vars["to_country"];
            $selected = $this->form_vars["selected"];
            $serviceType = $this->form_vars["service_type"];
            if(empty($serviceType))
                $serviceType = "D";
            $output = '<option value="">Please Select</option>';
            /*
             *   Standard base customer
             */
            if (trim($userId) != '') {
                $userId = (int) base64_decode($userId);
                $userObj = new User($userId);
                $userAccountId = $userObj->getUserAccountId();
            } else
                $userAccountId = $this->user->getUserAccountId();
            $servicesData = ServiceFilter::getUserAccountServices($userAccountId, $fromCountry, $toCountry,$serviceType);
            $selectedStr = "";
            if (count($servicesData) > 0) {
                foreach ($servicesData as $userServiceData) {
                    $logoPath = '../images/carrierlogo/thumbnail/owe_16_' . $userServiceData->getCarrierLogo();
                    $selectedStr = ($userServiceData->getId() == $selected ? ' selected="selected"' : '');
                    $output .= '<option ' . $selectedStr .  ' data-id="' . $userServiceData->getId() . '" data-proformainvoice="' . $userServiceData->getProformaInvoice() . '" data-iscustomized="' . $userServiceData->getIsCustomized() . '" data-insurance="'.$userServiceData->getInsuranceAvailable(). '" data-deliverymode="'.$userServiceData->getDeliveryMode(). '" value="' . $userServiceData->getId() . '" data-content="<img src=\'' . $logoPath . '\' /> ' . ucfirst($userServiceData->getName()) . ' ">' . $userServiceData->getName() . '</option>';
                }
            }
            echo $output;
            die;
        }
        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "GET_SERVICE_DESCRIPTION") {
            $service_id = (int) trim($this->form_vars['service_id']);
            $serviceDetail = new Services($service_id);
            if ($serviceDetail->getId() > 0) {
                $description = $serviceDetail->getDescription();
                $output['STATUS'] = 'SUCCESS';
                if (trim($description) == '')
                    $description = formatMessages(ERROR_NO_SERVICE_DETAIL); //'Service discription is not available.';
                $output['MESSAGE'] = $description;
                $output['NAME'] = $serviceDetail->getName() . ' Service';
            }
            else {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = formatMessages(ERROR_NO_SERVICE_DETAIL);
            }
            //}
            echo json_encode($output);
            die;
        }
        //Address popup code for listing search start
        if (isset($_GET['addressFunction']) && $_GET['addressFunction'] == 'handle_address_ajax') {
            $addressFilter = new AddressFilter();
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if ($dataTableColumnName == "Phone_number") {
                    $dataTableColumnName = "Phone";
                }
                $functionName = 'AddOrderBy' . $dataTableColumnName;
                $addressFilter->$functionName($orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchPhoneNumber = $this->form_vars['phone_number'];
                if (!empty($searchPhoneNumber))
                    $addressFilter->addFieldLikeFilter('phone_number', $searchPhoneNumber);
                $searchCompany = $this->form_vars['search_company'];
                if (!empty($searchCompany))
                    $addressFilter->addFieldLikeFilter('company', $searchCompany);
                $searchContact = $this->form_vars['search_contact'];
                if (!empty($searchContact))
                    $addressFilter->addFieldLikeFilter('contact', $searchContact);
                $searchAddressLine1 = $this->form_vars['search_address_line_1'];
                if (!empty($searchAddressLine1))
                    $addressFilter->addFieldLikeFilter('address_line_1', $searchAddressLine1);
                $searchAddressLine2 = $this->form_vars['search_address_line_2'];
                if (!empty($searchAddressLine2))
                    $addressFilter->addFieldLikeFilter('address_line_2', $searchAddressLine2);
                $searchAddressLine3 = $this->form_vars['search_address_line_3'];
                if (!empty($searchAddressLine3))
                    $addressFilter->addFieldLikeFilter('address_line_3', $searchAddressLine3);
                $searchCity = $this->form_vars['search_city'];
                if (!empty($searchCity))
                    $addressFilter->addFieldLikeFilter('city', $searchCity);
                $searchCountry = $this->form_vars['search_country'];
                if (!empty($searchCountry))
                    $addressFilter->addFieldLikeFilter('c.name', $searchCountry);
                $searchPostcode = $this->form_vars['search_postcode'];
                if (!empty($searchPostcode))
                    $addressFilter->addFieldLikeFilter('postcode', $searchPostcode);
                $searchState = $this->form_vars['search_state'];
                if (!empty($searchState))
                    $addressFilter->addFieldLikeFilter('state', $searchState);
                $searchEmail = $this->form_vars['search_email'];
                if (!empty($searchEmail))
                    $addressFilter->addFieldLikeFilter('email', $searchEmail);
            }
            $addressDataArr = array();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $addressFilter->setRowsPerPage($iDisplayLength);
            $addressFilter->setOffset($iDisplayStart);
            $userId = (int) (trim(util_get('uaccount')) != '') ? base64_decode(util_get('uaccount')) : $this->user->getId();
            $addressFilter->addFieldFilter("user_id", $userId);
            $iTotalRecords = $addressFilter->getPagingCount();
            $addressObjs = $addressFilter->getPagingList(" a.phone_number, a.id, a.company, a.contact, a.address_line_1, a.address_line_2, a.address_line_3,a.city,a.country,c.name 'country_name',a.postcode,a.state,a.email");
            foreach ($addressObjs as $addressObj) {
                $addressArr = array();
                $addressArr['phone_number'] = $addressObj->getPhoneNumber();
                $addressArr['company'] = $addressObj->getCompany();
                $addressArr['contact'] = $addressObj->getContact();
                $addressArr['address_line_1'] = $addressObj->getAddressLine1();
                $addressArr['address_line_2'] = $addressObj->getAddressLine2();
                $addressArr['address_line_3'] = $addressObj->getAddressLine3();
                $addressArr['city'] = $addressObj->getCity();
                $addressArr['country'] = $addressObj->getCountryName();
                $addressArr['country_id'] = $addressObj->getCountry();
                $addressArr['postcode'] = $addressObj->getPostcode();
                $addressArr['state'] = $addressObj->getState();
                $addressArr['email'] = $addressObj->getEmail();
                $addressCurrArr = base64_encode(json_encode($addressArr));
//                unset($addressArr['country_iso']);
                $addressArr['actions'] = '<a data-address_obj=' . $addressCurrArr . ' href="javascript:void(0)" class="btn btn-sm grey-cascade choose_address"><i class="fa fa-link"></i></a>';
                $addressDataArr [] = $addressArr;
            }
            $addressDataArrJson['data'] = $addressDataArr;
            $addressDataArrJson['draw'] = $sEcho;
            $addressDataArrJson['recordsTotal'] = $iTotalRecords;
            $addressDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($addressDataArrJson);
            die;
        }
        //Address popup code for listing search end
        /*
         * Post Code
         */
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'FIND_REMOTE_AREA_POSTCODE') {
//            $postcode = str_replace(' ', '', $this->form_vars['postcode']);
//            if (isset($this->form_vars['postcode']) && (int) $postcode == $postcode) {
//                $filterRemote = new RemoteareaUserMappingFilter();
//                $remotearesPostCode = $filterRemote->getRemoteAreaPostCodeFilter($postcode, $this->user->getUserAccount());
//                if (count($remotearesPostCode) > 0) {
//                    if ($remotearesPostCode[0]->getId() > 0)
//                        echo 'SUCCESS';
//                    else
//                        echo 'FAILED - 2';
//                }
//            }
//            else {
//                echo 'FAILED - 1 ';
//            }
            die;
        }
        
        /*
         * Handle Upload invoice documents
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_user_doc') {
            $html = "";
            
            $path = "../_assets/paperless_invoice/temp/";
            if (!file_exists($path))
                @mkdir($path, 0775, true);
            if (isset($_FILES["user_doc_file"]) && trim($_FILES["user_doc_file"]["name"]) != '') {
                $allowedExts = array("pdf");
                $temp = explode(".", $_FILES["user_doc_file"]["name"]);
                
                $extension = end($temp);
                if ((($_FILES["user_doc_file"]["type"] == "application/pdf" )) && in_array($extension, $allowedExts)) {
                    if ($_FILES["user_doc_file"]["error"] > 0) {
                        $return_msg = "Return Code: " . $_FILES["user_doc_file"]["error"] . "<br>";
                    } else {
                        $uploadUserDoc = str_replace(' ', '_', time() . $_FILES["user_doc_file"]["name"]);
                        $documentName = explode(".", $uploadUserDoc);
                        $docName = $documentName[0];
                        move_uploaded_file($_FILES["user_doc_file"]["tmp_name"], $path . $uploadUserDoc);
                        $fileFullPath = $path . $uploadUserDoc;
                        if ($extension == "pdf") {
                            $fileFullPath = "../images/pdf.png";
                        }
                        
                        $html .= '<div class="col-md-3 doc_upload_view" id="usr_doc_' . $docName . '">';
                        $html .= '<div class="thumbnail">';
                        $html .= '<img src="' . $fileFullPath . '" style="max-width: 100%; max-height: 100px; display: block;" data-src="' . $path . $uploadUserDoc . '">';
                        $html .= '<div class="caption text-center" style="height: auto">';
                        $html .= '<a target="_blank" href="' . $path . $uploadUserDoc . '" class="btn blue btn-xs"> View </a>&nbsp&nbsp';
                        $html .= '<a href="javascript:;" class="btn btn-xs red remove_doc" data-doc_id="' . $uploadUserDoc . '"> Remove </a>';
                        $html .= '<input type="hidden" name="temp_invoice_name" id="temp_invoice_name" value="' . $uploadUserDoc . '" />';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        echo $html;
                    }
                } else {
                    echo $return_msg = "0";
                }
            }

            die;
        }
        /*
         *         Handle remove Invoice Document
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'remove_user_doc') {
            $docId = $this->form_vars['doc_id'];
            @unlink("../_assets/paperless_invoice/temp/". $docId);
            
            echo "1";
            die;
        }
        
        /*
         * SKU Case
         */
        $skuid = base64_decode(util_get("skuid"));
        $skuType = util_get("type");
        
        
        if($skuid > 0 && $skuType == 'sku'){
            
            $this->skuorder = new SkuOrder($skuid);
            $this->form_vars["order_reference"] = $this->skuorder->getShipmentReference();
            $this->form_vars["skuid"] = $skuid;
            $this->form_vars["shipment_type"] = "C";
            $this->form_vars["sender_country"] = $this->user->getCountryId();
            $this->form_vars["sender_company"] = $this->userAccount->getCompany();
            $this->form_vars["sender_contact"] = $this->user->getFirstName() . ' ' . $this->user->getLastName();
            $this->form_vars["sender_email"] = $this->user->getEmail();
            $this->form_vars["sender_telephone"] = $this->user->getPhone();
            $this->form_vars["sender_address_line_1"] =$this->user->getAddress();
            $this->form_vars["sender_address_line_2"] =  $this->user->getAddress2();
            $this->form_vars["sender_address_line_3"] = $this->user->getAddress3();
            $this->form_vars["sender_city"] = $this->user->getCity();
            $this->form_vars["sender_state"] = $this->user->getState();
            $this->form_vars["sender_postcode"] = $this->user->getPostcode();
            
            if($this->skuorder->getWarehouseId() > 0){
                $warehouse = new Warehouse($this->skuorder->getWarehouseId());
                $this->form_vars["receiver_company"] = $warehouse->getWarehouseName();
                $this->form_vars["receiver_contact"] = $warehouse->getWarehouseName();
                $this->form_vars["receiver_telephone"] = $warehouse->getPhone();
                $this->form_vars["receiver_address_line_1"] = $warehouse->getAddressLine1();
                $this->form_vars["receiver_address_line_2"] = $warehouse->getAddressLine2();
                $this->form_vars["receiver_city"] = $warehouse->getCitytown();
                $this->form_vars["receiver_postcode"] = $warehouse->getPostzipcode();
                $this->form_vars["receiver_country"] = $warehouse->getCountryId();
                $this->form_vars["receiver_state"] = $warehouse->getStateregion();
            }
            
        }
        else{
            if($skuid > 0){
                $this->form_vars["skuid"] = $skuid;
                $this->skuorder = new SkuOrder($skuid);
            }
            // Edit consignment case
            $id = util_get_num("id");
            $this->form_vars["id"] = $id;
            $this->labelGenreatedCheck = 0;
            $this->isInsurance = 0;
            $this->isPaperlessInvoice = 0;
            $consignment = new Consignment();
            if ($this->form_vars["id"] > 0) {
                $consignment = new Consignment($id);
                $this->labelGenreatedCheck = $consignment->getDateLabelCreated();
                $this->consignmentEditError = $consignment->getMessage();
                $this->consignmentStatus = $consignment->getShipmentStatus();
            }
            $this->form_vars["sender_country"] = (!empty($consignment->getSenderCountryId()) ? $consignment->getSenderCountryId() : $this->user->getCountryId());
            if($consignment->getShipmentType() == "C"){
                $this->form_vars["sender_company"] = $consignment->getSenderCompany() ;
                $this->form_vars["sender_contact"] = $consignment->getSenderName() ;
                $this->form_vars["sender_email"] = $consignment->getSenderEmail() ;
                $this->form_vars["sender_telephone"] = $consignment->getSenderTelephone() ;
                $this->form_vars["sender_address_line_1"] = $consignment->getSenderAddressLine1();
                $this->form_vars["sender_address_line_2"] = $consignment->getSenderAddressLine2();
                $this->form_vars["sender_address_line_3"] = $consignment->getSenderAddressLine3();
                $this->form_vars["sender_city"] = $consignment->getSenderCity();
                $this->form_vars["sender_state"] = $consignment->getSenderState();
                $this->form_vars["sender_postcode"] = $consignment->getSenderPostcode();
                $this->form_vars["packageLocation"] = $consignment->getRoutingCodeEur();
            }else
            {
                $this->form_vars["sender_company"] = (!empty($consignment->getSenderCompany()) ? $consignment->getSenderCompany() : $this->userAccount->getCompany());
                $this->form_vars["sender_contact"] = (!empty($consignment->getSenderName()) ? $consignment->getSenderName() : $this->user->getFirstName() . ' ' . $this->user->getLastName());
                $this->form_vars["sender_email"] = (!empty($consignment->getSenderEmail()) ? $consignment->getSenderEmail() : $this->user->getEmail());
                $this->form_vars["sender_telephone"] = (!empty($consignment->getSenderTelephone()) ? $consignment->getSenderTelephone() : $this->user->getPhone());
                $this->form_vars["sender_address_line_1"] = (!empty($consignment->getSenderAddressLine1()) ? $consignment->getSenderAddressLine1() : $this->user->getAddress());
                $this->form_vars["sender_address_line_2"] = (!empty($consignment->getSenderAddressLine2()) ? $consignment->getSenderAddressLine2() : $this->user->getAddress2());
                $this->form_vars["sender_address_line_3"] = (!empty($consignment->getSenderAddressLine3()) ? $consignment->getSenderAddressLine3() : $this->user->getAddress3());
                $this->form_vars["sender_city"] = (!empty($consignment->getSenderCity()) ? $consignment->getSenderCity() : $this->user->getCity());
                $this->form_vars["sender_state"] = (!empty($consignment->getSenderState()) ? $consignment->getSenderState() : $this->user->getState());
                $this->form_vars["sender_postcode"] = (!empty($consignment->getSenderPostcode()) ? $consignment->getSenderPostcode() : $this->user->getPostcode());
            }
            $this->form_vars["sender_country_id"] = (!empty($consignment->getSenderCountryId()) ? $consignment->getSenderCountryId() : $this->user->getCountryId());

            $this->form_vars["receiver_company"] = $consignment->getCompany();
            $this->form_vars["receiver_contact"] = $consignment->getContact();
            $this->form_vars["receiver_email"] = $consignment->getEmail();
            $this->form_vars["receiver_telephone"] = $consignment->getTelephone();
            $this->form_vars["receiver_address_line_1"] = $consignment->getAddressLine1();
            $this->form_vars["receiver_address_line_2"] = $consignment->getAddressLine2();
            $this->form_vars["receiver_address_line_3"] = $consignment->getAddressLine3();
            $this->form_vars["receiver_city"] = $consignment->getCity();
            $this->form_vars["receiver_postcode"] = $consignment->getPostcode();
            $this->form_vars["receiver_country"] = $consignment->getCountryId();
            $this->form_vars["receiver_state"] = $consignment->getState();
            $this->form_vars["service"] = ($consignment->getCustomizedServiceId() > 0 ? $consignment->getCustomizedServiceId() : $consignment->getServiceId());
            $this->form_vars["reference"] = $consignment->getReference();
            $this->form_vars["order_reference"] = $consignment->getHawb();
            $this->form_vars["eori_number"] = $consignment->getEoriNumber();
            $this->form_vars["ioss_number"] = $consignment->getIossNumber();
            $this->form_vars["vat_number"] = $consignment->getVatNumber();
            $this->form_vars["tracking_number"] = $consignment->getAwb();

            $this->form_vars["item_currency"] = $consignment->getCurrency();
            $this->form_vars["item_type"] = $consignment->getItemtype();
            $this->form_vars["item_weight"] = $consignment->getWeight();
            $this->form_vars["item_value"] = $consignment->getValue();
            $this->form_vars["notes"] = $consignment->getNotes();
            $this->form_vars["description"] = $consignment->getDescription();
            $this->form_vars["collection_end_time"] = $consignment->getCollectionEndTime();
            $this->form_vars["collection_start_time"] = $consignment->getCollectionStartTime();
            $this->form_vars["collection_date"] = (!empty($consignment->getCollectionDate()) ? $consignment->getCollectionDate() : "");
            $this->form_vars["shipment_type"] = $consignment->getShipmentType();
            $this->form_vars["shipmentUserId"] = $consignment->getUserId();
            $this->form_vars["insurance_agree"] = $consignment->getIsInsured();
            $this->serviceId = $this->form_vars["service"];
            $serviceObj = new Services($this->serviceId);
            $this->isInsurance = $serviceObj->getInsuranceAvailable();
            $this->isPaperlessInvoice = $serviceObj->getProformaInvoice();
            $invoicePath = "../_assets/paperless_invoice/";
            $fileName = md5($consignment->getId()) . ".pdf";
            if(file_exists($invoicePath . $fileName)){
               $this->invoiceFile =  $fileName;
            }

            // Get consignment Parcel Data
            if ($id > 0) {
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addFieldFilter("consignment_id", $id);
                $this->parcelData = $parcelFilter->getList();
            }
        }
        /*
         * delete any existing items in current session
         */
        $session_id = session_id();
        $itemDetailDeleteSql = "DELETE FROM item_details where session_id = '".$session_id."' and user_id = '".$this->user->getId()."'";
        $itemDetail = new ItemDetail();
        $itemDetail->deleteItemDetailFromSql($itemDetailDeleteSql);
    }
    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            #map{
                width:100%;
                height:600px;
            }
			.add-more-parcel-keys, .remove-parcel-key{
				padding: 4px 10px;
			}
        </style>
        <?php
    }
    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('span').tooltip();
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                }
                
                $("#apply_all").on("ifChanged", function () {
                    if ($(this).is(":checked")) {
                        applyAllPieces();
                        return;
                    
                    } else {
                        $('.parcel_quantity').not(':first').val('');
                        $('.parcel_weight').not(':first').val('');
                        $('.parcel_value').not(':first').val('');
                        $('.parcel_length').not(':first').val('');
                        $('.parcel_width').not(':first').val('');
                        $('.parcel_height').not(':first').val('');
                       
                        calculateTotalParcelWeightNValue();
                        calculateTotalParcelWeightNValue("value");
                        
                    }
                });             
                $(document).on('click', '.add-more-parcel-keys', function () {
                    elindex++;
                    var clone = $(this).parent().parent().parent().clone();
                    var parcelQuantity = $(clone).find('.parcel_quantity').attr('name');
                    var parcelWeight = $(clone).find('.parcel_weight').attr('name');
                    var parcelLength = $(clone).find('.parcel_length').attr('name');
                    var parcelWidth = $(clone).find('.parcel_width').attr('name');
                    var parcelHeight = $(clone).find('.parcel_height').attr('name');
                    var parcelValue = $(clone).find('.parcel_value').attr('name');
                    var parcelItemdetails = $(clone).find('.parcel_itemdetails').attr('name');
                    var id = $(clone).find('.parcel_id').attr('name');
                    $(this).remove();
                    //$(clone).find('input').val('0');
                    //$(clone).find('.parcel_weight').val('');
                    $(clone).find('.parcel_value').val('');
                    $(clone).find('.parcel_length').val('');
                    $(clone).find('.parcel_height').val('');
                    $(clone).find('.parcel_width').val('');
                    $(clone).find('.parcel_itemdetails').val('');
                    

                    $(clone).find('.parcel_quantity').attr('name', parcelQuantity.replace(/\d+/, elindex));
                    $(clone).find('.parcel_weight').attr('name', parcelWeight.replace(/\d+/, elindex));
                    $(clone).find('.parcel_length').attr('name', parcelLength.replace(/\d+/, elindex));
                    $(clone).find('.parcel_width').attr('name', parcelWidth.replace(/\d+/, elindex));
                    $(clone).find('.parcel_height').attr('name', parcelHeight.replace(/\d+/, elindex));
                    $(clone).find('.parcel_value').attr('name', parcelValue.replace(/\d+/, elindex));
                    $(clone).find('.parcel_itemdetails').attr('name', parcelItemdetails.replace(/\d+/, elindex));
                    $(clone).find('.parcel_itemdetails').attr('data-parcelcount', elindex);
                    $(clone).find('.parcel_id').attr('name', id.replace(/\d+/, elindex));
                    
                    $(clone).find('button.remove-parcel-key').show();
                    $(clone).find('button.remove-parcel-key').removeClass('initial-button');
                    var numItems = $('.parcel_weight').length
                    $('#total_pieces').val(numItems+1).select();
                    $(clone).appendTo($('.parcel_container'));
                    $(".parcel_itemdetails").click(function () {
                        getItemDetails($(this));
                    });
                });
                $(document).on('click', '.remove-parcel-key', function () {
                    var el = $(this);
                    var totalPieceFlag = $(el).data('totalpieces');
                    if(totalPieceFlag != 'yes'){
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                            function (isConfirm) {
                                if (isConfirm) {
                                    
                                   if ($('.parcel_container .remove-parcel-key').length > 1) {
                                    $(el).parent().parent().remove();
                                    if ($('.add-more-parcel-keys').length == 0) {
                                        var addMore = $(el).parent().find('.add-more-parcel-keys').clone();
                                        $('.parcel_container .remove-parcel-key').last().parent().prepend(addMore);
                                    }
                                    if ($(".parcel_weight").length == 1) {
                                        $(".parcel_container .remove-parcel-key").hide();
                                    }
                                    var total_pieces =$(".parcel_weight").length;
                                    $('#total_pieces').val(total_pieces).select();
                                } else {
                                    $(el).parent().parent().find('input').val('');
                                }
                                calculateTotalParcelWeightNValue();
                                calculateTotalParcelWeightNValue("value");
                                }
                            });
                    }
                    else
                    {
                        if ($('.parcel_container .remove-parcel-key').length > 1) {
                        $(el).parent().parent().remove();
                        if ($('.add-more-parcel-keys').length == 0) {
                            var addMore = $(el).parent().find('.add-more-parcel-keys').clone();
                            $('.parcel_container .remove-parcel-key').last().parent().prepend(addMore);
                        }
                        if ($(".parcel_weight").length == 1) {
                            $(".parcel_container .remove-parcel-key").hide();
                        }
                        var total_pieces =$(".parcel_weight").length;
                        $('#total_pieces').val(total_pieces).select();
                        } else {
                            $(el).parent().parent().find('input').val('');
                        }
                        calculateTotalParcelWeightNValue();
                        calculateTotalParcelWeightNValue("value");
                    }
                  
                });
                $("#portlet_address").click(function () {
                    $('#portlet-address').modal('show');
                    if (!$.fn.DataTable.isDataTable("#manage-data-table"))
                        DataTableFun.init();
                });
                
                $(".parcel_itemdetails").click(function () { 
                   getItemDetails($(this));
                });
                
                // Add multiple Items of Parcel
                $(".initialitem-button").hide();
                var elindex_item = 0;
                $(document).on('click', '.add-more-item-keys', function () {
                    elindex_item++;
                    var clone = $(this).parent().parent().parent().clone();
                    var itemDescription = $(clone).find('.item_description').attr('name');
                    <?php if(util_get("skuid") != ''){  ?>
                        var item_sku = $(clone).find('.item_sku .bs-select').attr('name');
                        var item_sku_id = $(clone).find('.item_sku .bs-select').attr('id');
                    <?php } else{ ?>        
                        var itemSku = $(clone).find('.item_sku').attr('name');
                    <?php } ?>
                    var itemUrl = $(clone).find('.item_url').attr('name');
                    var itemQty = $(clone).find('.item_qty').attr('name');
                    var itemValue = $(clone).find('.item_value').attr('name');
                    var itemWeight = $(clone).find('.item_weight').attr('name');
                    var itemHscode = $(clone).find('.item_hscode').attr('name');
                    var item_country = $(clone).find('.item_country .bs-select').attr('name');
                    var item_country_id = $(clone).find('.item_country .bs-select').attr('id');
                    
                    $(this).remove();
                    $(clone).find('.item_description').val('');
                    $(clone).find('.item_description').attr('name', itemDescription.replace(/\d+/, elindex_item));
                    <?php if(util_get("skuid") != ''){  ?>
                        $(clone).find('.item_sku .bs-select').attr('name', item_sku.replace(/\d+/, elindex_item));
                        $(clone).find('.item_sku .bs-select').attr('id', item_sku_id.replace(/\d+/, elindex_item));
                        $(clone).find('.bootstrap-select.item_sku').replaceWith(function () {
                            return $('#item_sku_' + elindex_item, this);
                        });
                        $(clone).find('#item_sku_' + elindex_item).selectpicker('refresh');
                    <?php } else {?>
                        $(clone).find('.item_sku').attr('name', itemSku.replace(/\d+/, elindex_item));
                    <?php } ?>
                    $(clone).find('.item_url').attr('name', itemUrl.replace(/\d+/, elindex_item));
                    $(clone).find('.item_qty').attr('name', itemQty.replace(/\d+/, elindex_item));
                    $(clone).find('.item_value').attr('name', itemValue.replace(/\d+/, elindex_item));
                    $(clone).find('.item_weight').attr('name', itemWeight.replace(/\d+/, elindex_item));
                    $(clone).find('.item_hscode').attr('name', itemHscode.replace(/\d+/, elindex_item));
                    $(clone).find('.item_country .bs-select').attr('name', item_country.replace(/\d+/, elindex_item));
                    $(clone).find('.item_country .bs-select').attr('id', item_country_id.replace(/\d+/, elindex_item));

                    $(clone).find('.bootstrap-select.item_country').replaceWith(function () {
                        return $('#item_country_' + elindex_item, this);
                    });
                    $(clone).find('#item_country_' + elindex_item).selectpicker('refresh');

                    $(clone).find('button.remove-item-key').show();
                    $(clone).find('button.remove-item-key').removeClass('initialitem-button');
                    $(clone).appendTo($('.ItemInformation'));
                });

                //Remove Multiple Items of Parcel
                $(document).on('click', '.remove-item-key', function () {
                    var el = $(this);
                    var removeItemsFlag = $(el).data('totalremoveitems');
                    if(removeItemsFlag != 'yes'){
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                            function (isConfirm) {
                                if (isConfirm) {
                                    if ($('.ItemInformation .remove-item-key').length > 1) {
                                        $(el).parent().parent().remove();
                                        if ($('.add-more-item-keys').length == 0) {
                                            var addMore = $(el).parent().find('.add-more-item-keys').clone();
                                            $('.ItemInformation .remove-item-key').last().parent().prepend(addMore);
                                        }
                                        if ($(".item_description").length == 1) {
                                            $(".ItemInformation .remove-item-key").hide();
                                        }
                                    } else {
                                        $(el).parent().parent().find('input').val('');
                                    }
                                }
                            });
                    }
                    else
                    {
                        if ($('.ItemInformation .remove-item-key').length > 1) {
                            $(el).parent().parent().remove();
                            if ($('.add-more-item-keys').length == 0) {
                                var addMore = $(el).parent().find('.add-more-item-keys').clone();
                                $('.ItemInformation .remove-item-key').last().parent().prepend(addMore);
                            }
                            if ($(".item_description").length == 1) {
                                $(".ItemInformation .remove-item-key").hide();
                            }
                        } else {
                            $(el).parent().parent().find('input').val('');
                        }
                    }
                });
                
                // Save Item Details btnSaveItemDetails
                var itemArray = [];
                $("#btnSaveItemDetails").click(function (){
                    var frmData = $("#itemdetail_save").serialize();
                    $.ajax({
                        type: "POST",
                        url: "consignment_add.php",
                        data: frmData,
                        dataType: "json",
                        success: function (json) {
                            $("#shw_itm_message div.alert").html(" ");
                            $("#shw_itm_message").show();
                            if (json.STATUS == 'SUCCESS')
                            {  
                                $('.remove-item-key').attr('data-totalremoveitems', 'yes');
                                var totalItems = $('.ItemInformation .remove-item-key').length;
                                for(i = 1; i <= totalItems; i++){
                                    $(".remove-item-key").last().click();
                                 }
                                $("#itemdetail_save").find('input[type=text]:not(".not_clear"), textarea, select').val("");
                                $("#item_country_0").val("").selectpicker('refresh');
                                $("#item_sku_0").val("").selectpicker('refresh');
                                
                                $("#shw_itm_message div.alert").addClass('alert-success').removeClass('alert-danger');
                                $("#shw_itm_message div.alert").html("Item added Successfully.");
                                 $('#add-item-details').modal('hide');
                            } else if (json.STATUS == 'ERROR') {
                                $("#shw_itm_message div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#shw_itm_message div.alert").html(json.MESSAGE);
                            } else {
                                $("#shw_itm_message div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#shw_itm_message div.alert").html("We have encounter technical error, Please try again later or contact to administrator.");
                            } 
                        },
                        error: function () {
                            $("#shw_itm_message div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#shw_itm_message div.alert").html("We have encounter technical error, Please try again later or contact to administrator.");
                        }
                        
                    });
                });

                function getItemDetails(el){
                    $("#shw_itm_message div.alert").html(" ");
                    $("#shw_itm_message").hide();
                    var parcelcount = el.data('parcelcount');
                    var cosignment_id = $('.consignment_id').val();
                    var skuid = "<?php echo util_get('skuid');?>";
                    $.ajax({
                        type: "POST",
                        url: "consignment_add.php",
                        data: {
                            parcelcount: parcelcount,
                            consignment_id: cosignment_id,
                            skuid:skuid,
                            func: 'edit_itemdetail'
                        },
                        dataType: "json",
                        success: function (data) {
                            elindex_item = 0;
                            if (data.STATUS == 'SUCCESS'){
                                var itemDetailData = data.ITEM_DETAILS;
                                $(".ItemInformation").html(data.ITEM_DETAILS);
                                //$("#sender_country").val("225").selectpicker('refresh');
                                $('.item_country').selectpicker('refresh');
                                $('.item_sku').selectpicker('refresh');
                                elindex_item = data.ITEM_COUNT;
                            } else {
                                $('.remove-item-key').attr('data-totalremoveitems', 'yes');
                                var totalItems = $('.ItemInformation .remove-item-key').length;
                                for(i = 1; i <= totalItems; i++){
                                    $(".remove-item-key").last().click();
                                }
                            }
                        },
                        error: function () {
                            $("#shw_itm_message div.alert").html(" ");
                            $("#shw_itm_message").show();
                            $("#shw_itm_message div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#shw_itm_message div.alert").html("We have encounter technical error, Please try again later or contact to administrator.");
                        }

                    });
                    $('#add-item-details').modal('show');
                    $('#piecenumber').val(parcelcount);
                }
                
                
                // Save consignment Logic
                function saveShipmentData() {
                    var externalUserParam = '';
                    var externalUser    =   '<?php echo util_get("uaccount");?>';
                    if($.trim(externalUser) != '')
                    {
                        externalUserParam    =   "?uaccount="+externalUser;
                    }
                    var frmData = $("#consignment_save").serialize();
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "consignment_add.php"+externalUserParam,
                        data: frmData,
                        dataType: "json",
                        success: function (json) {
                            $('#id').val(json.CONSIGNMENT_ID);
                            if (json.STATUS == 'SUCCESS' && json.CONSIGNMENT_ID > 0)
                            {
                                $("#consignment_save").find('input[type=text]:not(".not_clear"), textarea, select').val("");
                                $("#item_type").val("Normal");
                                $("#sender_country").val("225").selectpicker('refresh');
                                $("#receiver_country").val("225").selectpicker('refresh');
                                $("#service").val("").selectpicker('refresh');
                                $("#item_currency").val("GBP");
                                $(".parcel_length").val(0);
                                $(".parcel_width").val(0);
                                $(".parcel_height").val(0);
                                $(".parcel_value").val(0);
                                //  $("select").selectpicker('refresh');
                                $('.address_book_container').hide();
                                $("#id").val("0");
                                $("#temp_invoice_name").val("");
                                if (typeof (json.INSTANT_LABEL) == "undefined") {
                                    var msg = "You have successfully added shipment data, Please " +
                                            "<a class='btn btn-primary btn-xs' href='" + json.URL + "' target='_blank'>click here</a> to view list.";
                                    show_res_msg("success", msg);
                                } else {
                                    var msg = "You have successfully created label, please <a class='btn btn-primary btn-xs'  href='" + json.INSTANT_LABEL + "' target='_blank'>click here</a> to view label or " +
                                            "<a class='btn btn-primary btn-xs' href='" + json.URL + "' target='_blank'>click here</a> to view list.";
                                    show_res_msg("success", msg);
                                    window.open(json.INSTANT_LABEL, '', 'width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');
                                }
                            } else if (json.STATUS == 'ERROR') {
                                show_res_msg("error", json.MESSAGE);
                            } else {
                                show_res_msg("error", "We have encounter technical error, Please try again later or contact to administrator.");
                            }
                            window.scrollTo(0, 0);
                            $.unblockUI();
                        },
                        error: function () {
                            $.unblockUI();
                            show_res_msg('error', 'We have encounter technical error, Please try again later or contact to administrator.');
                        }
                    });
                }
                
                
                
                $(".btn_save").click(function () {
                    if ($(this).hasClass("btn_save_invalid")) {
                        swal({
                            title: "Are you sure you want to generate label ?",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#save_invalid").val(1);
                                saveShipmentData();
                            }
                        });
                    } else {
                        $("#save_invalid").val(0);
                        saveShipmentData();
                    }
                });
                // Save consignment Logic End
        <?php
        if ($this->form_vars["id"] > 0) {
            echo 'getAccountServices();';
        }
        if($this->isInsurance > 0) { ?>
            $("#insurance-div").show();
        <?php } 
        if($this->isPaperlessInvoice > 0) { ?>
            $("#plt-div").show();
        <?php } ?> 
            
            
        <?php if ($this->labelGenreatedCheck > 0) { ?>
                    $("input").attr("disabled", "disabled");
                    $("select").attr("disabled", "disabled");
                    $(".add-more-parcel-keys").remove();
                    $(".remove-parcel-key").remove();
                    $("#portlet_address").remove();
                    $(".address_book_container").hide();
        <?php } ?>
                $("#btnCancel").click(function () {
        <?php if ($this->labelGenreatedCheck == 0) { ?>
                        swal({
                            title: "You are about to leave the page, Please confirm.",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                                function (isConfirm) {
                                    if (isConfirm) {
                                        window.location = "client_list.php";
                                    }
                                });
        <?php } else { ?>
                        window.location = "client_list.php";
        <?php } ?>
                });
                $(".get_service_detail").click(function () {
                    var service_id = $(".get_service_detail").data("service_id");
                    var iscustomized = $(".get_service_detail").data("iscustomized");
                    if ($.isNumeric(service_id)) {
                        $.ajax({
                            url: "consignment_add.php",
                            data: {
                                service_id: service_id,
                                shipment_type: iscustomized,
                                action: 'GET_SERVICE_DESCRIPTION'
                            },
                            type: "POST",
                            dataType: "json",
                            async: false,
                        })
                                // Code to run if the request succeeds (is done);
                                // The response is passed to the function
                                .done(function (json) {
                                    if (json.STATUS == 'SUCCESS') {
                                        $("#show-service-info-detail").html(json.MESSAGE);
                                        $("#show-service-info-name").html(json.NAME);
                                        $("#show-service-info").modal("show");
                                    } else {
                                        show_res_msg("error", "We are unable to find service.");
                                    }
                                })
                                // Code to run if the request fails; the raw request and
                                // status codes are passed to the function
                                .fail(function (xhr, status, errorThrown) {
                                    show_res_msg("error", "Sorry, there was a problem!");
                                })
                                // Code to run regardless of success or failure;
                                .always(function (xhr, status) {
                                    // alert( "The request is complete!" );
                                });
                    }
                });
                if ($('.date_picker').length > 0) {
                    var dateToday = new Date();
                    //init date pickers
                    $('.date_picker').datepicker({
                        autoclose: true,
                        startDate: dateToday,
                    });
                }
                if (jQuery().timepicker) {
                    $('.timepicker-default1').timepicker({
                        autoclose: true,
                        minuteStep: 5,
                        showSeconds: false,
                        showMeridian: false
                    });
                    $('.timepicker-default2').timepicker({
                        autoclose: true,
                        minuteStep: 5,
                        showSeconds: false,
                        showMeridian: false
                    });
                }
                // Collection type change
                $(document).on('click', '.shipment_type', function (event, state) {
                    var shipment_type = $(this).val();
                    var shipper_permission = '<?php echo (Permissions::checkFilePermission('consignment_add_show_shipper_detail') === true ? '1' : '0') ?>';
                    $("#drop_off_link").hide();
                    $("#pick_up_link").hide();
                    
                    $("#shipper_detail_div").html("Shipper Details");
                    if(shipper_permission == 0)
                        $("#shipper_detail_container").hide();
                        $("#shipper_details_content").hide();
                    if(shipment_type == 'D' && shipper_permission == 1)
                    {
                        //alert(shipment_type);
                        //$("#shipper_detail_div").html("COLLECTION DETAILS");
                        $("#shipper_detail_container").show();
                        $("#shipper_details_content").hide();
                        // Assign default data
                        addDefaultValue();
                         $(".pick_up_cls").prop('readonly', false);
                    }
                    else if (shipment_type == 'C') {
                        $("#shipper_detail_div").html("Collection Details");
                        $("#shipper_detail_container").show();
                        $("#shipper_details_content").show();
                        <?php if(!isset($_GET["id"]) && !isset($_GET["skuid"])){ ?>
                        $("#sender_company").val('');
                        $("#sender_contact").val('');
                        $("#sender_email").val('');
                        $("#sender_telephone").val('');
                        $("#sender_address_line_1").val('');
                        $("#sender_address_line_2").val('');
                        $("#sender_address_line_3").val('');
                        $("#sender_city").val('');
                        $("#sender_state").val('');
                        $("#sender_postcode").val('');
                        $(".pick_up_cls").prop('readonly', false);                        
                        <?php  } ?>
                        // Assign default data
                        //addDefaultValue();
                        // End assign default data
                        $(".drop_off_cls").prop('readonly', false);
                    } else if (shipment_type == 'DO'){
                        $("#shipper_detail_div").html("Drop-off Details");
                        $("#drop_off_link").show();
                        $("#shipper_detail_container").show();
                        $("#shipper_details_content").hide();
                        //$(".drop_off_cls").val('');
                        // Assign default data
                        //addDropOffValue();
                        // End assign default data
                        $(".drop_off_cls").prop('readonly', true);
                        $(".pick_up_cls").prop('readonly', false);
                    }else{
                        // Assign default data
                        addDefaultValue();
                    }
                    getAccountServices();
                });
                 <?php if ($this->form_vars["id"] > 0 || $this->form_vars["skuid"] > 0) { ?>
                    $('input[name=shipment_type]:checked').trigger('click');
                <?php  }  ?>
                calculateTotalParcelWeightNValue();
                calculateTotalParcelWeightNValue("value");
                calculateTotalParcelWeightNValue("quantity");
            });
            function getAccountServices() {
                var selected = "<?php echo $this->serviceId; ?>";
                var from_country = $("#sender_country").val();
                var to_country = $("#receiver_country").val();
                var user_id = '<?php echo util_get("uaccount"); ?>';
                var serviceType = $("input[name='shipment_type']:checked"). val();
                $.blockUI();
                $.ajax({
                    url: "consignment_add.php",
                    data: {
                        from_country: from_country,
                        to_country: to_country,
                        user_id: user_id,
                        selected: selected,
                        service_type: serviceType,
                        func: 'get_country_services'
                    },
                    type: "POST",
                    dataType: "html",
                    async: false,
                })
                        .done(function (data) {
                            $.unblockUI();
                            if (data != "") {
                                $('#service').html(data);
                                $('#service').selectpicker("refresh");
                            }
                        });
            }
            function getDropOffData() {
                var service_id = $("#service").val();
                var sender_country_id = $("#sender_country").val();
                if(service_id == "" || sender_country_id == ""){
                    swal("","Please select Shipper country and service");
                }
                var user_id = '<?php echo $this->user->getId(); ?>';
                $.blockUI();
                $.ajax({
                    url: "consignment_add.php",
                    data: {
                        service_id: service_id,
                        sender_country_id: sender_country_id,
                        user_id: user_id,
                        func: 'get_drop_off_data'
                    },
                    type: "POST",
                    dataType: "json",
                    async: false,
                }).done(function (data) {
                            $.unblockUI();
                            if (data != "") {
                                $("#sender_company").val(data.drop_off.company_name);
                                $("#sender_address_line_1").val(data.drop_off.address_line_1);
                                $("#sender_address_line_2").val(data.drop_off.address_line_2);
                                $("#sender_address_line_3").val(data.drop_off.address_line_3);
                                $("#sender_city").val(data.drop_off.city);
                                $("#sender_postcode").val(data.drop_off.postcode);
                                $("#sender_telephone").val(data.drop_off.telephone);
                                $("#sender_company").data("drop_off_company",data.drop_off.company_name);
                                $("#sender_address_line_1").data("drop_off_address_line_1",data.drop_off.address_line_1);
                                $("#sender_address_line_2").data("drop_off_address_line_2",data.drop_off.address_line_2);
                                $("#sender_address_line_3").data("drop_off_address_line_3",data.drop_off.address_line_3);
                                $("#sender_city").data("drop_off_city",data.drop_off.city);
                                $("#map_lat").val(data.drop_off.lat);
                                $("#map_long").val(data.drop_off.lng);
                                if(data.drop_off.postcode == undefined  || data.drop_off.postcode == ""){
                                    $("#sender_postcode").val($("#sender_postcode").data("sender_postcode"));
                                }else{
                                    $("#sender_postcode").data("drop_off_postcode",data.drop_off.postcode);
                                }
                                $("#sender_telephone").data("drop_off_telephone",data.drop_off.telephone);
                            }
                        });
            }
            $(document).on('focusout', '.parcel_weight', function () {
                calculateTotalParcelWeightNValue();
            });
            $(document).on('focusout', '.parcel_value', function () {
                calculateTotalParcelWeightNValue("value");
            });
            $(document).on('focusout', '.parcel_quantity', function () {
                calculateTotalParcelWeightNValue("quantity");
            });
            function calculateTotalParcelWeightNValue(type) {
                if(!type){
                    type = "weight";
                }
                // Add sum of parcels weight to the total weight field
                var sum = 0;
                $('.parcel_'+type).each(function () {
                    if ($(this).val() > 0) {
                        var tmpWeight = $.trim($(this).val());
                        tmpWeight = (tmpWeight != "" ? tmpWeight : 0);
                        sum += parseFloat(tmpWeight);  // Or this.innerHTML, this.innerText
                    } else {
                        $(this).val("");
                    }
                });
                if(type == "weight"){
                    sum = sum.toFixed(3);
                }else{
                    sum = sum.toFixed(2);
                }
                $("#item_"+type).val(sum);
            }
            $(document).on('change', '#sender_country,#receiver_country', function () {
                getAccountServices();
            });
            $(document).on('change', '#service', function () {
                 $("#plt-div").hide();
                 $("#insurance-div").hide();
                 $("#pick_up_link").hide();
                var proformainvoice = $(this).find(':selected').attr('data-proformainvoice');
                var insurance = $(this).find(':selected').attr('data-insurance');
                var service_id = $(this).find(':selected').attr('data-id');
                var iscustomized = $(this).find(':selected').attr('data-iscustomized');
                var deliverymode = $(this).find(':selected').attr('data-deliverymode');
                
                $("#is_product").val(iscustomized);
                $(".get_service_detail").attr("data-service_id", service_id);
                $(".get_service_detail").attr("data-iscustomized", iscustomized);
                var shipment_type = $('input[name=shipment_type]:checked').val();
                if(proformainvoice == 1){
                     $("#plt-div").show();
                }
                if(insurance == 1){
                     $("#insurance-div").show();
                }
                if(deliverymode == 2){
                    $("#pick_up_link").show();
                    swal("","Please select Pick Up Location.","info");
                }
                if(shipment_type == 'DO') {
                    getDropOffData();
                } 
                else if(shipment_type == 'C'){
                    <?php if(!isset($_GET["id"])  && !isset($_GET["skuid"])){ ?>
                    $("#sender_company").val('');
                    $("#sender_contact").val('');
                    $("#sender_email").val('');
                    $("#sender_telephone").val('');
                    $("#sender_address_line_1").val('');
                    $("#sender_address_line_2").val('');
                    $("#sender_address_line_3").val('');
                    $("#sender_city").val('');
                    $("#sender_state").val('');
                    $("#sender_postcode").val('');
                    <?php } ?>
                }
                else {
                    // Assign default data
                    addDefaultValue();
                }
            });
            
             /*
             *         Handle Invoice  File upload
             */
                $("#upload_file").click(function () {
                    
                    var exitingDocCheck = $('.doc_upload_view')[0];
                    if(exitingDocCheck){
                        swal("", "You can only upload one file. Please remove file before uploading new one.", "error");
                        return false;
                    }
                    var filename = $("#file_name").val();
                    if (filename == "") {
                        swal("", "Please Select File", "info");
                        return false;
                    } else {
                        var file_data = $('#file_name').prop('files')[0];
                        var form_data = new FormData();
                        form_data.append('file_name', filename);
                        form_data.append('action', "upload_user_doc");
                        form_data.append('user_doc_file', file_data);
                        $.ajax({
                            url: "consignment_add.php", // point to server-side PHP script
                            dataType: 'html', // what to expect back from the PHP script, if anything
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (php_script_response) {
                                if (php_script_response == "0") {
                                    swal("Invalid file type", "You can only upload gif,jpeg,jpg,png and pdf file", "error");
                                } else {
                                    $("#append_user_doc").append(php_script_response);
                                    $("a.fileinput-exists").click();
                                }
                            }
                        });
                    }
                });
                
                
                //Handle Document Remove functioanlity
                $(document).on('click', '.remove_doc', function () {
                    var el = $(this);
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                            function (isConfirm) {
                                if (isConfirm) {
                                    var userDocId = el.data("doc_id");
                                    $.ajax({
                                        url: 'consignment_add.php',
                                        type: 'POST',
                                        data: {action: 'remove_user_doc', doc_id: userDocId},
                                        headers: {
                                        },
                                        success: function () {
                                            var divname = userDocId.split('.');
                                            $("#usr_doc_" + divname[0]).remove();
                                        },
                                        error: function (xhr, status, error) {

                                        }
                                    });

                                }
                            });

                });
                
            function addDefaultValue(){
                // Assign default data
                
                if($('#sender_company').val() == '')
                    $("#sender_company").val($("#sender_company").data("sender_company"));
                if($('#sender_contact').val() == '')
                    $("#sender_contact").val($("#sender_contact").data("sender_contact"));
                if($('#sender_email').val() == '')
                    $("#sender_email").val($("#sender_email").data("sender_email"));
                if($('#sender_telephone').val() == '')
                    $("#sender_telephone").val($("#sender_telephone").data("sender_telephone"));
                if($('#sender_address_line_1').val() == '')
                    $("#sender_address_line_1").val($("#sender_address_line_1").data("sender_address_line_1"));
                if($('#sender_address_line_2').val() == '')
                    $("#sender_address_line_2").val($("#sender_address_line_2").data("sender_address_line_2"));
                if($('#sender_address_line_3').val() == '')
                    $("#sender_address_line_3").val($("#sender_address_line_3").data("sender_address_line_3"));
                if($('#sender_city').val() == '')
                    $("#sender_city").val($("#sender_city").data("sender_city"));
                if($('#sender_state').val() == '')
                    $("#sender_state").val($("#sender_state").data("sender_state"));
                if($('#sender_postcode').val() == '')
                    $("#sender_postcode").val($("#sender_postcode").data("sender_postcode"));
            }
            function addDropOffValue(){
                // Assign default data
                $("#sender_company").val($("#sender_company").data("drop_off_sender_company"));
                $("#sender_contact").val($("#sender_contact").data("drop_off_sender_contact"));
                $("#sender_email").val($("#sender_email").data("drop_off_sender_email"));
                $("#sender_telephone").val($("#sender_telephone").data("drop_off_sender_telephone"));
                $("#sender_address_line_1").val($("#sender_address_line_1").data("drop_off_sender_address_line_1"));
                $("#sender_address_line_2").val($("#sender_address_line_2").data("drop_off_sender_address_line_2"));
                $("#sender_address_line_3").val($("#sender_address_line_3").data("drop_off_sender_address_line_3"));
                $("#sender_city").val($("#sender_city").data("drop_off_sender_city"));
                $("#sender_state").val($("#sender_state").data("drop_off_sender_state"));
                if($("#sender_postcode").data("drop_off_sender_postcode") != undefined){
                    $("#sender_postcode").val($("#sender_postcode").data("drop_off_sender_postcode"));
                }else{
                    $("#sender_postcode").val($("#sender_postcode").data("sender_postcode"));
                }
            }
            function showErrorMessageSA(message, type) {
                var swMsg = message;
                swal({
                    title: swMsg,
                    text: "",
                    type: type,
                    html: true,
                    customClass: "swal-large",
                    //                        showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Ok",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true,
                    allowOutsideClick: true
                },
                        function (isConfirm) {
                            if (isConfirm) {
                            }
                        });
            }
            function show_res_msg(type, msg) {
                $("html, body").animate({scrollTop: 0}, "slow");
                $("#show_general_msg div.alert").html(" ");
                if (type == "success") {
                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                } else if (type == "error") {
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                } else {
                    $("#show_general_msg div.alert").addClass('alert-info').removeClass('alert-success');
                }
                $("#show_general_msg div.alert").html(msg);
                $("#show_general_msg").show();
            }
            //Hadi Code
            $(document).on('click', '.choose_address', function () {
                var address_data = window.atob($(this).data('address_obj'));
                var address_json = JSON.parse(address_data);
//                console.log(address_json);
                $("#receiver_telephone").val(address_json.phone_number);
                $("#receiver_company").val(address_json.company);
                $("#receiver_contact").val(address_json.contact);
                $("#receiver_address_line_1").val(address_json.address_line_1);
                $("#receiver_address_line_2").val(address_json.address_line_2);
                $("#receiver_address_line_3").val(address_json.address_line_3);
                $("#receiver_city").val(address_json.city);
                $("#receiver_postcode").val(address_json.postcode);
                $("#receiver_country").val(address_json.country_id);
                $("#receiver_country").selectpicker("refresh");
                $('#portlet-address').modal('hide');
                getAccountServices();
            });
            var DataTableFun = function () {
                var externalUserParam = '';
                var externalUser = '<?php echo util_get("uaccount"); ?>';
                if ($.trim(externalUser) != '')
                    externalUserParam = "&uaccount=" + externalUser;
                var handleDataTable = function () {
                    var grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "consignment_add.php?addressFunction=handle_address_ajax" + externalUserParam, // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "contact"},
                                {"data": "company"},
                                {"data": "address_line_1", "bSortable": false},
                                {"data": "address_line_2", "bSortable": false},
                                {"data": "address_line_3", "bSortable": false},
                                {"data": "city"},
                                {"data": "postcode"},
                                {"data": "state"},
                                {"data": "country"},
                                {"data": "email"}
                            ]
                        }
                    });
                }
                return {
                    init: function () {
                        handleDataTable();
                    }
                };
            }();
            function PostcodeAnywhere_Interactive_FindByParts_v1_00Begin(Key, Organisation, Building, Street, Locality, Postcode, UserName, postcodesearchid) {
                var scriptTag = document.getElementById("PCAf84f3b53776c481f8a0d00ba6c342422");
                var headTag = document.getElementsByTagName("head").item(0);
                var strUrl = "";
                //Build the url
                Key = "KK47-GX49-RY18-WB49";
                UserName = "INDIV67408";
                strUrl = "https://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/FindByParts/v1.00/json.ws?";
                strUrl += "&Key=" + encodeURI(Key);
                strUrl += "&Postcode=" + encodeURI(Postcode);
                strUrl += "&UserName=" + encodeURI(UserName);
                if(postcodesearchid == "returncollection"){
                    strUrl += "&CallbackFunction=PostcodeAnywhere_Interactive_FindByParts_v1_00EndSender";
                }
                else
                {
                    strUrl += "&CallbackFunction=PostcodeAnywhere_Interactive_FindByParts_v1_00End";
                }
                //Make the request
                if (scriptTag)
                {
                    try
                    {
                        headTag.removeChild(scriptTag);
                    } catch (e)
                    {
                        //Ignore
                    }
                }
                scriptTag = document.createElement("script");
                scriptTag.src = strUrl
                scriptTag.type = "text/javascript";
                scriptTag.id = "PCAf84f3b53776c481f8a0d00ba6c342422";
                headTag.appendChild(scriptTag);
            }
            function PostcodeAnywhere_Interactive_FindByParts_v1_00End(response) {
                document.getElementById('return').options.length = 0;
                //Test for an error
                if (response.length == 1 && typeof (response[0].Error) != 'undefined') {
                    //Show the error message
                    alert("You are not allowed to find address, " + response[0].Description)
                    //  alert(response[0].Description);
                } else {
                    //Check if there were any items found
                    if (response.length == 0) {
                        alert("Sorry, no matching items found");
                    } else {
                        if (response.length == 1) {
                            PostcodeAnywhere_Interactive_RetrieveById_v1_10Begin('AA11-AA11-AA11-AA11', response[0].Id, '', '');
                        } else {
                            document.getElementById('return').style.display = '';
                            for (var i = 0; i < response.length; i++)
                                document.getElementById('return').options.add(new Option(response[i].StreetAddress + ", " + response[i].Place, response[i].Id));
                        }
                    }
                }
            }// END FUNCTION
            function PostcodeAnywhere_Interactive_FindByParts_v1_00EndSender(response) {
                document.getElementById('returncollection').options.length = 0;
                //Test for an error
                if (response.length == 1 && typeof (response[0].Error) != 'undefined') {
                    //Show the error message
                    alert("You are not allowed to find address, " + response[0].Description)
                    //  alert(response[0].Description);
                } else {
                    //Check if there were any items found
                    if (response.length == 0) {
                        alert("Sorry, no matching items found");
                    } else {
                        if (response.length == 1) {
                            PostcodeAnywhere_Interactive_RetrieveById_v1_10Begin('AA11-AA11-AA11-AA11', response[0].Id, '', '');
                        } else {
                            document.getElementById('returncollection').style.display = '';
                            for (var i = 0; i < response.length; i++)
                                document.getElementById('returncollection').options.add(new Option(response[i].StreetAddress + ", " + response[i].Place, response[i].Id));
                        }
                    }
                }
            }// END FUNCTION
            
            function PostcodeAnywhere_Interactive_RetrieveById_v1_10Begin(Key, Id, PreferredLanguage, UserName, postcodesearch) {
                var scriptTag = document.getElementById("PCAa73f9bc2b60d4e4cbd595512478a3291");
                var headTag = document.getElementsByTagName("head").item(0);
                var strUrl = "";
                Key = "KK47-GX49-RY18-WB49";
                UserName = "INDIV67408";
                //Build the url
                strUrl = "https://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/RetrieveById/v1.10/json.ws?";
                strUrl += "&Key=" + encodeURI(Key);
                strUrl += "&Id=" + encodeURI(Id);
                strUrl += "&PreferredLanguage=" + encodeURI(PreferredLanguage);
                strUrl += "&UserName=" + encodeURI(UserName);
                if(postcodesearch == "senderPostcode"){
                    strUrl += "&CallbackFunction=PostcodeAnywhere_Interactive_RetrieveById_v1_10EndSender";
                }
                else
                {
                    strUrl += "&CallbackFunction=PostcodeAnywhere_Interactive_RetrieveById_v1_10End";
                }
                //Make the request
                if (scriptTag)
                {
                    try
                    {
                        headTag.removeChild(scriptTag);
                    } catch (e)
                    {
                        //Ignore
                    }
                }
                scriptTag = document.createElement("script");
                scriptTag.src = strUrl
                scriptTag.type = "text/javascript";
                scriptTag.id = "PCAa73f9bc2b60d4e4cbd595512478a3291";
                headTag.appendChild(scriptTag);
            }
            function PostcodeAnywhere_Interactive_RetrieveById_v1_10End(response) {
//                console.log(response);
                if (response.length == 1 && typeof (response[0].Error) != 'undefined')
                    alert(response[0].Description);
                else
                {
                    //Check if there were any items found
                    if (response.length == 0)
                    {
                        alert("Sorry, no matching items found");
                    } else
                    {
                        $('#receiver_company').val(response[0].Company);
                        $('#receiver_address_line_1').val(response[0].Line1);
                        $('#receiver_address_line_2').val(response[0].Line2);
                        if ($.trim(response[0].County) != '')
                        {
                            $('#receiver_address_line_3').val(response[0].PostTown);
                            $('#receiver_city').val(response[0].County);
                        } else
                        {
                            $('#receiver_address_line_3').val(response[0].Line3);
                            $('#receiver_city').val(response[0].PostTown);
                        }
                        $('#receiver_postcode').val(response[0].Postcode);
                        document.getElementById('return').style.display = 'none';
                    }
                }
            }
            function PostcodeAnywhere_Interactive_RetrieveById_v1_10EndSender(response) {
//                console.log(response);
                if (response.length == 1 && typeof (response[0].Error) != 'undefined')
                    alert(response[0].Description);
                else
                {
                    //Check if there were any items found
                    if (response.length == 0)
                    {
                        alert("Sorry, no matching items found");
                    } else
                    {
                        $('#sender_company').val(response[0].Company);
                        $('#sender_address_line_1').val(response[0].Line1);
                        $('#sender_address_line_2').val(response[0].Line2);
                        if ($.trim(response[0].County) != '')
                        {
                            $('#sender_address_line_3').val(response[0].PostTown);
                            $('#sender_city').val(response[0].County);
                        } else
                        {
                            $('#sender_address_line_3').val(response[0].Line3);
                            $('#sender_city').val(response[0].PostTown);
                        }
                        $('#sender_postcode').val(response[0].Postcode);
                        document.getElementById('returncollection').style.display = 'none';
                    }
                }
            }
            function check_remote_area(domObject, isRemoteArea) {
                var postcode = domObject.value;
                $.post("consignment_add.php", {action: 'FIND_REMOTE_AREA_POSTCODE', postcode: postcode}, function (data)
                {
                    if (data == "SUCCESS" && isRemoteArea == "YES")
                    {
                        show_res_msg("info", postcode + " is a remote area and may conatin additional");
                    } else if (data == "SUCCESS" && (isRemoteArea == "NO" || isRemoteArea == ""))
                    {
                        show_res_msg("error", postcode + " is remote area. Remote area postcode is not allowed for your account. Please contact to administrator and activate it.");
                        $("#btnSave").hide();
                    } else
                    {
                        $("#show_general_msg").hide();
                        $("#show_general_msg div.alert").html("");
                        $("#btnSave").show();
                    }
                });
                return false;
            }
            
            function addMultiPiecesTextbox(){
                
                   
                    var total_pieces = $("#total_pieces").val();
                    var total_piececount = $(".parcel_weight").length;
                    $('.remove-parcel-key').attr('data-totalpieces', 'yes');
                    if(total_pieces < total_piececount){
                        var difference = total_piececount - total_pieces;
                        //alert(difference +"-"+ total_piececount +"-"+ total_pieces);
                        for(i = 1; i <= difference; i++){
                           $(".remove-parcel-key").last().click();
                        }
                        
                    }
                    else{
                        var difference = total_pieces - total_piececount ;
                        for(i = 1; i <= difference; i++){
                            $(".add-more-parcel-keys").click();
                        }
                    }
                    $('.remove-parcel-key').attr('data-totalpieces', 'no');
                    if($("#apply_all").is(":checked")){
                           applyAllPieces();
                    }
                }
            function applyAllPieces(){
                var total_pieces = $("#total_pieces").val();
                var weight = $('.parcel_weight').first().val();
                var pquantity = $('.parcel_quantity').first().val();
                var pvalue = $('.parcel_value').first().val();
                var length = $('.parcel_length').first().val();
                var width = $('.parcel_width').first().val();
                var height = $('.parcel_height').first().val();


                jQuery('.parcel_weight').each(function(key,value) {
                    var currentElement = $(this);
                    currentElement.val(weight); // if it is an input/select/textarea field
                    jQuery('.parcel_quantity:nth-child('+key+ ')').val(pquantity); 
                    jQuery('.parcel_value:nth-child('+key+ ')').val(pvalue);
                    jQuery('.parcel_length:nth-child('+key+ ')').val(length);
                    jQuery('.parcel_width:nth-child('+key+ ')').val(width);
                    jQuery('.parcel_height:nth-child('+key+ ')').val(height);


                });
                calculateTotalParcelWeightNValue();
                calculateTotalParcelWeightNValue("value");
            }
            
        </script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDM4nwr48gdWBG0QTTiosoLkQMKAdzZrk&region=LV"></script>
<script type="text/javascript">
            var InforObj = [];
            var locations = '';
            var map;
            var previousmarker = null;
            var currentmarker = null;
            function initMap(countryIso='', postcode='', iso='', city='')
            {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: {lat: 51.5, lng: -0.41}
                });
                var geocoder = new google.maps.Geocoder();
                geocodeAddress(geocoder, map, countryIso, postcode, iso, city);


            }
            function geocodeAddress(geocoder, resultsMap, countryIso, postcode, iso, city) {
                if(postcode != '')
                    var address = city + "," + postcode + "," + iso;
                else
                    var address = city + "," + iso;
                geocoder.geocode({'address': address}, function(results, status) {
                  if (status === 'OK') {
                    resultsMap.setCenter(results[0].geometry.location);
//                    var marker = new google.maps.Marker({
//                      map: resultsMap,
//                      position: results[0].geometry.location
//                    });
                  } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                  }
                });
              }
            //Add marker on map
            function initMarker(locations,carrierName,carrierLogo, location=""){
                $("#dropoff-content-display").html('');
                var iconBase = '../images/carrierlogo/thumbnail/owe_16_'+carrierLogo;
                var hoverIconBase = '../images/carrierlogo/thumbnail/owe_50_'+carrierLogo;
                var contentString = '<div id="content"><h5>'+locations.companyname+'</h5><br /> '+locations.addressline1+'<br /> '+locations.addressline2+'<br /> '+locations.city+'<br /> '+locations.postcode+'</div>';
                //Add Marker on map
                
               const marker = new google.maps.Marker({
                   position: {lat: parseFloat(locations.lat), lng: parseFloat(locations.lng)},
                   icon: iconBase,
                   map: map,
                   id:'test_'+locations.lat,
                   animation: google.maps.Animation.DROP
                   //zIndex: parseFloat(locations[i].index)
               });
               const infowindow = new google.maps.InfoWindow({
                   content: contentString,
                   maxWidth: 200
               });
                marker.addListener('mouseover', function () {
                   closeOtherInfo();
                   infowindow.open(marker.get('map'), marker);
                   InforObj[0] = infowindow;
               });
               marker.addListener('mouseout', function () {
                   closeOtherInfo();
                   infowindow.close();
                   InforObj[0] = infowindow;
               });
                // Add info window to marker
               google.maps.event.addListener(marker, 'click', (function() {
//                   console.log(marker.id);
                   return function() {
                       if(previousmarker !== null){
                           previousmarker.setAnimation(null);
                           previousmarker.setIcon(iconBase);
                       }
                       previousmarker = marker;
                       map.setZoom(12);
                       //map.setCenter(marker.getPosition());
                       marker.setIcon(null);
                       marker.setIcon(hoverIconBase);
                        toggleBounce(marker);
                       var url = "consignment_add.php";
                       var action = "DROPOFF_SELECTED_SERVICE";
                       var companyname  = locations.companyname;
                       var addressline1  = locations.addressline1;
                       var addressline2  = locations.addressline2;
                       var addressline3  = locations.addressline3;
                       var city  = locations.city;
                       var postcode  = locations.postcode;
                       var country  = locations.country;
                       var telephone  = locations.telephone;
                       var mon  = locations.Mon;
                       var tue  = locations.Tue;
                       var wed  = locations.Wed;
                       var thu  = locations.Thu;
                       var fri  = locations.Fri;
                       var sat  = locations.Sat;
                       var sun  = locations.Sun;
                       var lat  = locations.lat;
                       var lng  = locations.lng;
                       var branchId = locations.branchId;
                       var serviceId = $("#service").val();
                       var carrierlogo = carrierLogo;
                       var carriername = carrierName;
                       $.post(url, {func: action, companyname: companyname,
                           addressline1: addressline1, addressline2: addressline2,
                           addressline3: addressline3, city: city, postcode: postcode, country: country, telephone: telephone,
                           mon:mon, tue:tue, wed:wed, thu:thu, fri:fri, sat:sat, sun:sun, carrierLogo: carrierlogo, carrierName: carriername, lat: lat, lng: lng, serviceId: serviceId, branchId: branchId, location: location
                           }, function (d) {
                               //$('#add-dropoffform-popup').modal('show');
                                $("#dropoff-content-display").html(d);
                                $("#dropoff-content-display").parent().animate({ width: 'show' });
                           });
                   }
               })(marker));
               return marker;
            }
            function loadMap(fromPostCode){
                var serviceId = $("#service").val();
                var fromCountryId = $("#sender_country").val();
                
                if(serviceId == ""){
                     swal("","Please select reciever country and service","info");
                 }else{
                     $.ajax({
                         type: "POST",
                         url: "consignment_add.php",
                         data: {form_action: "map_load", from_country_id: fromCountryId,service_id:serviceId,from_post_code:fromPostCode},
                         dataType:"json",
                         success: function (data) {
                         	console.log(data);
                             $("#close_drop_off_detail").trigger("click");
                             initMap(fromCountryId,fromPostCode);
                             locations = data.location_data;
                             var len = locations.length;
                             var i;
                             // Add multiple markers to map
                             for (i = 0; i < len; i++)
                             {
                                var retMarker = initMarker(locations[i],data.carrier_name,data.carrier_logo);
                                if(locations[i].selected == '1'){
                                    google.maps.event.trigger(retMarker, 'click');
                                }
                             }
                         },
                         error: function () {
                             swal("","No data found", "info");
                         }
                     });
                     $("#pickup").val("");
                     $("#drop_off_modal").modal("show");
                 }
            }
            
            
            $(document).on('click', '#drop_off_link', function (event, state) {
                var fromPostCode = $("#sender_postcode").val();
                $("#drop_off_postcode").val(fromPostCode);
                loadMap(fromPostCode);
            });
            function loadPickMap(toPostCode='', toCity = ''){
                var serviceId = $("#service").val();
                var toCountryId = $("#receiver_country").val();
                var toCountryIso = $("#receiver_country").find(':selected').attr('data-countryiso');
                var dropoffCity = $("#drop_off_city").val();
                if(serviceId == ""){
                     swal("","Please select reciever country and service","info");
                 }else{
                     $.ajax({
                         type: "POST",
                         url: "consignment_add.php",
                         data: {form_action: "map_load", from_country_id: toCountryId,service_id:serviceId,from_post_code:toPostCode,to_city:toCity, pickup:"Pick"},
                         dataType:"json",
                         success: function (data) {
                         	console.log(data);
                             $("#close_drop_off_detail").trigger("click");
                             initMap(toCountryId,'', toCountryIso, dropoffCity);
                             locations = data.location_data;
                             var len = locations.length;
                             var i;
                             // Add multiple markers to map
                             for (i = 0; i < len; i++)
                             {
                                var retMarker = initMarker(locations[i],data.carrier_name,data.carrier_logo,"PICKUP");
                                if(locations[i].selected == '1'){
                                    google.maps.event.trigger(retMarker, 'click');
                                }
                             }
                         },
                         error: function () {
                             swal("","No data found", "info");
                         }
                     });
                     $("#pickup").val("PICKUP");
                     $("#drop_off_modal").modal("show");
                 }
            }
           $(document).on('click', '#btn_reload_map', function (event, state) {
                var isPickUp = $("#pickup").val();
                var fromPostCode = $("#drop_off_postcode").val();
                var toCity = $("#drop_off_city").val();
                if(isPickUp == "PICKUP")
                    loadPickMap(fromPostCode, toCity);
                else
                    loadMap(fromPostCode);
                
            });
            $(document).on('click', '#close_drop_off_detail', function (event, state) {
                $("#dropoff-content-display").parent().animate({ width: 'hide' });
            });
            $(document).on('click', '#pick_up_link', function (event, state) {
                loadPickMap();
            });
            $(document).on('click', '#btn_select_location', function () {
                var location = $("#drop_off_location").html();
                if(location == "PICKUP"){
                    $("#receiver_company").val($("#drop_off_company").html());
                    $("#receiver_address_line_1").val($("#drop_off_address_line_1").html());
                    $("#receiver_address_line_2").val($("#drop_off_address_line_2").html());
                    $("#receiver_address_line_3").val($("#drop_off_address_line_3").html());
                    $("#receiver_city").val($("#drop_off_address_line_1").attr("data-city"));
                    $("#receiver_postcode").val($("#drop_off_postcode_select").html());
                    $("#receiver_telephone").val($("#drop_off_telephone").html());
                    $("#pickup_branchId").val($("#drop_off_address_line_1").attr("data-branchId"));
                    $(".pick_up_cls").prop('readonly', true);
                }
                else
                {
                    $("#sender_company").val($("#drop_off_company").html());
                    $("#sender_address_line_1").val($("#drop_off_address_line_1").html());
                    $("#sender_address_line_2").val($("#drop_off_address_line_2").html());
                    $("#sender_address_line_3").val($("#drop_off_address_line_3").html());
                    $("#sender_city").val($("#drop_off_address_line_1").attr("data-city"));
                    $("#sender_postcode").val($("#drop_off_postcode_select").html());
                    $("#sender_telephone").val($("#drop_off_telephone").html());
                }
                    $("#drop_off_modal").modal("hide");
            });
            function toggleBounce(marker) {
                if (marker.getAnimation() !== null) {
                  marker.setAnimation(null);
                } else {
                  marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            }
            function closeOtherInfo() {
            if (InforObj.length > 0) {
                /* detach the info-window from the marker ... undocumented in the API docs */
                InforObj[0].set("marker", null);
                /* and close it */
                InforObj[0].close();
                /* blank the array */
                InforObj.length = 0;
            }
        }
</script>
        <?php
    }
    protected function renderBody() {
        extract($this->form_vars);
        if ($this->form_vars["id"] > 0) {
            if (!empty($this->consignmentEditError) && $this->consignmentStatus == consignment::STATUS_INVALID) {
                $this->flashMsg->error($this->consignmentEditError);
            }
        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-money"></i>
                    Create/Update Shipment
                </div>
                <div class="actions">
                </div>
                <div class="tools">
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                </div>
                <div class="row" id="show_general_msg" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <form name="consignment_save" id="consignment_save" action="" method="post">
					<?php if(Permissions::checkFilePermission('add_consignment_chk_service_type')){ ?>
					<div class="row">
						<div class="form-group">
							<div class="col-md-12 text-right">
								<div class="mt-radio-inline">
									<label class="mt-radio">
										<input type="radio" class="shipment_type" name="shipment_type" id="dispatch" value="D" <?php if(isset($shipment_type) && ($shipment_type == "D")) { echo "checked='checked'";} if(empty($shipment_type)) { echo "checked='checked'";} ?> > Dispatch
										<span></span>
									</label>
									<label class="mt-radio">
										<input type="radio" class="shipment_type" name="shipment_type" id="collection" value="C" <?php if(isset($shipment_type) && ($shipment_type == "C")) { echo "checked='checked'";} ?> > Collection
										<span></span>
									</label>
									<label class="mt-radio">
										<input type="radio" class="shipment_type" name="shipment_type" id="dropoff" value="DO" <?php if(isset($shipment_type) && ($shipment_type == "DO")) { echo "checked='checked'";} ?> > Drop-Off
										<span></span>
									</label>
									<!--<a class="btn blue btn-sm" id="drop_off_link" style="display: none;" href="#"><i class="fa fa-globe"></i>&nbsp;Drop of location</a>
									<input type="hidden" id="map_lat" value="" name="map_lat" />
									<input type="hidden" id="map_long" value="" name="map_long" />-->
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
							  <div class="has-float-label">
								<?php echo Ddl::generateCountryDDL('sender_country', $sender_country, 'id', ' class="not_clear form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" '); ?>
								<label>Shipper Country <span class="required" aria-required="true"> * </span></label>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-3">
						  <div class="form-group">
							<div class="has-float-label">
							<?php echo Ddl::generateCountryDDL('receiver_country', $receiver_country, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" '); ?>
							<label>Receiver Country <span class="required" aria-required="true"> * </span></label>
						  </div>
						</div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label">
                                    <select name="service" id="service" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Service" data-container="body" placeholder="Service">
                                    </select>
                                     <label>Service <span class="required" aria-required="true"> * </span><span class="get_service_detail" style="cursor: pointer;"><i class="fa fa-info-circle"></i> More Info</span></label>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                              <div class="form-group">
                                 <div class="has-float-label input-icon right">
                                <i class="fa fa-building-o"></i>
                                    <input type="text" name="order_reference" id="order_reference" class="form-control" value="<?php echo $order_reference; ?>" placeholder="Order Reference" />
                                    <label for="order_reference">Order Reference</label>
                                </div>
							  </div>
                        </div>
                    </div>

					<div class="row">
					<?php
					if(Permissions::checkFilePermission('CREATE_OTHER_USER_SHIPMENT')){
					?>
						<div class="col-md-3">
							<div class="form-group">
								<div class="has-float-label">
									 <?php
									//if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
									  // $accountParentId = $this->user->getUserAccountId();
									 $sql = "SELECT MIN(u.id) as id, ua.user_account FROM user u inner join customer_account ua on ua.id = u.user_account_id WHERE ua.parentid = '".$this->user->getUserAccountId()."' group by ua.user_account";
										echo Ddl::generateDDLFromSql($sql, 'shipmentUserId', 'user_account', 'id', $shipmentUserId, 'class="form-filter bs-select form-control" data-live-search="true"', 'Please select account', '', 'shipmentUserId');
									   //echo Ddl::generateDDL('shipmentUserId', 'userAccountFilter', ' parentid = "'.$this->user->getUserAccountId().'"', 'user_account', 'id', $shipmentUserId, 'class="form-filter bs-select form-control" data-live-search="true"', 'Please select account', '', 'shipmentUserId',$title='','logo','../images/userlogo/thumbnail/','owe_16_');
									//}
								  // echo Ddl::showTreeDropdownuser('shipmentUserId', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $shipmentUserId, "Please Select User", 'class="form-filter bs-select form-control" data-live-search="true"', 'shipmentUserId', "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', false);
									?>
							   <label>Account <span class="get_service_detail" rel="tooltip" title="Select account for which you would like to create shipment." style="cursor: pointer;"><i class="fa fa-info-circle"></i> More Info</span></label>

								</div>
							</div>
						</div>
					<?php
					}
                                        if(Permissions::checkFilePermission('DATA_ENTRY_SHIPMENT') || $tracking_number != ''){
					?>
						<div class="col-md-3">
							<div class="form-group">
								 <div class="has-float-label input-icon right">
									<i class="fa fa-building-o"></i>
									<input type="text" name="tracking_number" id="tracking_number" class="not_clear drop_off_cls form-control" data-tracking_number="<?php echo $tracking_number; ?>" value="<?php echo $tracking_number; ?>" placeholder="Tracking Number" />
									 <label for="tracking_number">Tracking Number  <span class="get_service_detail" rel="tooltip" title="For data entry, please enter tracking number." style="cursor: pointer;"><i class="fa fa-info-circle"></i> More Info</span></label>
								</div>
							</div>
						</div>
					<?php }?>
                                        <div class="col-md-2">
                                                <div class="form-group">
                                                         <div class="has-float-label input-icon right">
                                                                <i class="fa fa-building-o"></i>
                                                                <input type="text" name="eori_number" id="eori_number" class="not_clear drop_off_cls form-control" data-eori_number="<?php echo $eori_number; ?>" value="<?php echo $eori_number; ?>" placeholder="EORI Number" />
                                                                 <label for="eori_number">EORI Number  <span class="get_service_detail" rel="tooltip" title="EORI number for non EU goods." style="cursor: pointer;"><i class="fa fa-info-circle"></i> More Info</span></label>
                                                        </div>
                                                </div>
                                        </div>
                                         <div class="col-md-2">
                                                <div class="form-group">
                                                         <div class="has-float-label input-icon right">
                                                                <i class="fa fa-building-o"></i>
                                                                <input type="text" name="ioss_number" id="ioss_number" class="not_clear drop_off_cls form-control" data-ioss_number="<?php echo $ioss_number; ?>" value="<?php echo $ioss_number; ?>" placeholder="IOSS Number" />
                                                                 <label for="ioss_number">IOSS Number  <span class="get_service_detail" rel="tooltip" title="IOSS number for EU goods  value less then 150 EUR." style="cursor: pointer;"><i class="fa fa-info-circle"></i> More Info</span></label>
                                                        </div>
                                                </div>
                                        </div>

                                        <div class="col-md-2">
                                                <div class="form-group">
                                                         <div class="has-float-label input-icon right">
                                                                <i class="fa fa-building-o"></i>
                                                                <input type="text" name="vat_number" id="vat_number" class="not_clear drop_off_cls form-control" data-vat_number="<?php echo $vat_number; ?>" value="<?php echo $vat_number; ?>" placeholder="VAT Number" />
                                                                 <label for="vat_number">VAT Number  <span class="get_service_detail" rel="tooltip" title="VAT number for non EU goods." style="cursor: pointer;"><i class="fa fa-info-circle"></i> More Info</span></label>
                                                        </div>
                                                </div>
                                        </div>
                                            
                    </div>
                    <div id="shipper_detail_container" <?php if(!Permissions::checkFilePermission('consignment_add_show_shipper_detail')){ ?> style="display: none;"  <?php } ?> >
                        <div class="caption margin-bottom-10 block">
                            <span id="shipper_detail_div">Shipper Details</span>
							<div class="pull-right">
                                                                <a class="btn blue btn-xs" id="pick_up_link" style="display: none;" href="#"><i class="fa fa-map-marker margin-right-10"></i>Pick Up location</a>
								<a class="btn blue btn-xs" id="drop_off_link" style="display: none;" href="#"><i class="fa fa-map-marker margin-right-10"></i>Drop off location</a>
								<input type="hidden" id="map_lat" value="" name="map_lat" />
								<input type="hidden" id="map_long" value="" name="map_long" />
							</div>
							<!--<button class="btn btn-xs blue"><i class="fa fa-map-marker margin-right-10"></i>Drop off location</button></div>-->
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                        <i class="fa fa-building-o"></i>
                                        <input type="text" name="sender_company" id="sender_company" class="not_clear drop_off_cls form-control" data-drop_off_company="" data-sender_company="<?php echo $sender_company; ?>" value="<?php echo $sender_company; ?>" placeholder="Company" />
                                        <label for="sender_company">Company</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="has-float-label input-icon right">
                                       <i class="fa fa-user"></i>
                                        <input type="text" name="sender_contact" id="sender_contact" class="not_clear form-control" data-drop_off_contact="" data-sender_contact="<?php echo $sender_contact; ?>" value="<?php echo $sender_contact; ?>" placeholder="Contact Person" />
                                        <label for="sender_contact">Contact Person <span class="required" aria-required="true"> * </span></label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                   <div class="has-float-label input-icon right">
                                     <i class="fa fa-envelope"></i>
                                        <input type="text" name="sender_email" id="sender_email" class="not_clear drop_off_cls form-control" data-drop_off_email=""  data-sender_email="<?php echo $sender_email; ?>" value="<?php echo $sender_email; ?>" placeholder="Email"/>
                                        <label for="sender_email">Email</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                   <div class="has-float-label input-icon right">
                                     <i class="fa fa-phone"></i>

                                        <input type="text" name="sender_telephone" id="sender_telephone" class="not_clear drop_off_cls form-control" data-drop_off_telephone="" data-sender_telephone="<?php echo $sender_telephone; ?>"  value="<?php echo $sender_telephone; ?>"  placeholder="Telephone"/>
                                        <label for="sender_telephone">Telephone</label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                       <i class="fa fa-map-marker"></i>
                                        <input type="text" name="sender_address_line_1" id="sender_address_line_1" class="not_clear drop_off_cls form-control" data-drop_off_address_line_1=""  data-sender_address_line_1="<?php echo $sender_address_line_1; ?>" value="<?php echo $sender_address_line_1; ?>"  placeholder="Address Line 1"/>
                                        <label for="sender_address_line_1">Address Line 1 <span class="required" aria-required="true"> * </span></label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                     <i class="fa fa-map-marker"></i>
                                        <input type="text" name="sender_address_line_2" id="sender_address_line_2" class="not_clear drop_off_cls form-control" data-drop_off_address_line_2=""  data-sender_address_line_2="<?php echo $sender_address_line_2; ?>" value="<?php echo $sender_address_line_2; ?>"  placeholder="Address Line 2" />
                                        <label for="sender_address_line_2">Address Line 2</label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                      <div class="has-float-label input-icon right">
                                <i class="fa fa-map-marker"></i>

                                        <input type="text" name="sender_address_line_3" id="sender_address_line_3" class="not_clear drop_off_cls form-control" data-drop_off_address_line_3="" data-sender_address_line_3="<?php echo $sender_address_line_3; ?>" value="<?php echo $sender_address_line_3; ?>"  placeholder="Address Line 3"/>
                                        <label for="sender_address_line_3">Address Line 3</label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                       <div class="has-float-label input-icon right">
                                        <i class="fa fa-map-marker"></i>

                                        <input type="text" name="sender_city" id="sender_city" class="not_clear form-control" data-drop_off_city=""  data-sender_city="<?php echo $sender_city; ?>"  value="<?php echo $sender_city; ?>"  placeholder="City"/>
                                        <label for="sender_city">City <span class="required" aria-required="true"> * </span></label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                     <i class="fa fa-map-marker"></i>

                                        <input type="text" name="sender_state" id="sender_state" class="not_clear drop_off_cls form-control" data-drop_off_state=""  data-sender_state="<?php echo $sender_state; ?>" value="<?php echo $sender_state; ?>"  placeholder="State" />
                                        <label for="sender_state">State</label>

                                     </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-icon has-float-label input-group">
                                         <i class="fa fa-qrcode"></i>

                                        <input type="text" name="sender_postcode" id="sender_postcode" class="not_clear drop_off_cls form-control" data-drop_off_postcode="" data-sender_postcode="<?php echo $sender_postcode; ?>" value="<?php echo $sender_postcode; ?>"  placeholder="Postcode"  />
                                        <label for="sender_postcode">Postcode <span class="required" aria-required="true"> * </span></label>
                                        <span class="input-group-btn">
                                            <button onclick="PostcodeAnywhere_Interactive_FindByParts_v1_00Begin('AA11-AA11-AA11-AA11', $('#sender_company').val(), $('#sender_address_line_1').val(), $('#sender_address_line_2').val(), $('#sender_city').val(), $('#sender_postcode').val(), '','returncollection')" value="Click to Find" type="button" class="btn btn-info">Find</button>
                                        </span>
                                    </div>
                                    <select class="form-control" id="returncollection" style="display:none;" size="4" onchange="PostcodeAnywhere_Interactive_RetrieveById_v1_10Begin('AA11-AA11-AA11-AA11', document.getElementById('returncollection').value, '', '','senderPostcode')"></select>
                                </div>
                            </div>
                            
                            <div id="shipper_details_content" style="display: none;">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="has-float-label input-icon right">
                                                    <i class="fa fa-file"></i>
                                                    <input  type="text" name="packageLocation" id="packageLocation" class="form-control" value="<?php echo $packageLocation; ?>"  placeholder="P.Location"/>
                                                    <label>P.Location</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-icon has-float-label right">
                                                <i class="fa fa-calendar"></i>
                                                 <input data-date-format="dd-mm-yyyy" type="text" size="16" class="form-control date_picker" name="collection_date" value="<?php echo formatDate($collection_date); ?>" id="collection_date"  placeholder="Collection Date"/>
                                                                                         <label for="collection_date">Date</label>
                                             </div>
                                         </div>
                                         <div class="col-md-3">
                                             <div class="input-icon has-float-label right">
                                                 <i class="fa fa-clock-o"></i>
                                                 <input type="text" class="form-control timepicker timepicker-default1" name="collection_start_time" value="<?php echo $collection_start_time; ?>" id="collection_start_time"  placeholder="Collection Start Time"/>
                                                 <label for="collection_start_time">Start Time</label>
                                             </div>
                                         </div>
                                         <div class="col-md-3">
                                             <div class="input-icon has-float-label right">
                                                 <i class="fa fa-clock-o"></i>
                                                 <input type="text" class="form-control timepicker timepicker-default2" name="collection_end_time" value="<?php echo $collection_end_time; ?>" id="collection_end_time"  placeholder="Collection End Time"/>
                                                 <label for="collection_end_time">End Time</label>
                                             </div>
                                         </div>
                                
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="caption margin-bottom-10 block">
                        Receiver Details
                        <div class="pull-right">
                            <button type="button" class="btn btn-default btn-block btn-xs" id="portlet_address"><i class="icon-home"></i> Address List</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                  <i class="fa fa-building-o"></i>
                                    <input type="text" name="receiver_company" id="receiver_company" class="form-control pick_up_cls" value="<?php echo $receiver_company; ?>" placeholder="Company"  placeholder="Company" />
                                    <label for="receiver_company">Company</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                   <i class="fa fa-user"></i>
                                    <input type="text" name="receiver_contact" id="receiver_contact" class="form-control" value="<?php echo $receiver_contact; ?>"  placeholder="Contact Person"/>
                                    <label for="receiver_contact">Contact Person <span class="required" aria-required="true"> * </span></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                   <i class="fa fa-envelope"></i>
                                    <input type="text" name="receiver_email" id="receiver_email" class="form-control" value="<?php echo $receiver_email; ?>"  placeholder="Email"/>
                                    <label for="receiver_email">Email</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                   <i class="fa fa-phone"></i>
                                    <input type="text" name="receiver_telephone" id="receiver_telephone" class="form-control" value="<?php echo $receiver_telephone; ?>"  placeholder="Telephone" />
                                    <label for="receiver_telephone">Telephone</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                   <i class="fa fa-map-marker"></i>
                                    <input type="text" name="receiver_address_line_1" id="receiver_address_line_1" class="form-control pick_up_cls" value="<?php echo $receiver_address_line_1; ?>"  placeholder="Address Line 1" />
                                    <label for="receiver_address_line_1">Address Line 1 <span class="required" aria-required="true"> * </span></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" name="receiver_address_line_2" id="receiver_address_line_2" class="form-control pick_up_cls" value="<?php echo $receiver_address_line_2; ?>"  placeholder="Addres Line 2"/>
                                    <label for="receiver_address_line_2">Address Line 2</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" name="receiver_address_line_3" id="receiver_address_line_3" class="form-control pick_up_cls" value="<?php echo $receiver_address_line_3; ?>"  placeholder="Address Line 3"/>
                                    <label for="receiver_address_line_3">Address Line 3</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" name="receiver_city" id="receiver_city" class="form-control pick_up_cls" value="<?php echo $receiver_city; ?>"  placeholder="City"/>
                                    <label for="receiver_city">City <span class="required" aria-required="true"> * </span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" name="receiver_state" id="receiver_state" class="form-control" value="<?php echo $receiver_state; ?>"  placeholder="State" />
                                    <label for="receiver_state">State</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group retrunNotRequired">
                                <div class="has-float-label  input-group">  <!-- <i class="fa fa-qrcode"></i> -->
									<input type='text'  name='receiver_postcode' id='receiver_postcode' value='<?php echo $receiver_postcode; ?>' size='10' maxlength="9"  placeholder="Postcode"  class="form-control pick_up_cls"  rel="tooltip" title="Postcode"  required <?php echo $readonly_str; ?>/>
								    <label for="receiver_postcode">Postcode <span class="required" aria-required="true"> * </span></label>
                                    <span class="input-group-btn">
                                        <button onclick="PostcodeAnywhere_Interactive_FindByParts_v1_00Begin('AA11-AA11-AA11-AA11', $('#receiver_company').val(), $('#receiver_address_line_1').val(), $('#receiver_address_line_2').val(), $('#receiver_city').val(), $('#receiver_postcode').val(), '','return')" value="Click to Find" type="button" class="btn btn-info">Find</button>
                                    </span>
                                </div>
                                <select class="form-control" id="return" style="display:none;" size="4" onchange="PostcodeAnywhere_Interactive_RetrieveById_v1_10Begin('AA11-AA11-AA11-AA11', document.getElementById('return').value, '', '','')"></select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                        </div>
                        <div class="row address_book_container">
                            <div class="col-sm-3">
                                <div class="form-group text-right margin-right-5">
                                    <span class="margin-right-5">Add to Address Book</span>
                                    <span><input  name="saveaddress" value="Y" type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="caption margin-bottom-10 block">
                      Parcel Details
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="parcel_box">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="has-float-label input-icon right">
                                        <select name='total_pieces' id='total_pieces' title="Items" rel="tooltip" class="form-control" placeholder="Items" onchange="addMultiPiecesTextbox()" >
                                                <?php
                                                for ($i=1; $i<100; $i++)
                                                {
                                                        ?>
                                                        <option value="<?php echo $i; ?>" <?php echo (@$total_pieces == $i) ? "selected":"" ?>><?php echo $i; ?></option>
                                                        <?php
                                                }
                                                ?>
                                            </select>
                                            <label>Total No of Pieces</label>
                                    </div>
                                </div>
                                <?php if(util_get("skuid") != ''){ ?>
                                <div class="col-md-2">
                                        <div class="has-float-label input-icon right">
                                                <i class="fa fa-cube"></i>
                                                <input type="text" name="sku_quantity" id="sku_quantity" class="form-control" readonly="" value="<?php echo $this->skuorder->getTotalSkuQuantity(); ?>"  placeholder="Items Weight kg"/>
                                                <label>SKU Quantity</label>
                                        </div>
                                </div>
                                    
                               <?php }?>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <div class="icheck-inline">
                                            <label class="label-account">
                                                <input id="apply_all" name="apply_all" type="checkbox" class="form-filter icheck"   /> Apply to all pieces
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-md-2">
                                    <label>Weight (kg)<span class="required" aria-required="true"> * </span></label>
                                </div>
                                <div class="col-md-2">
                                    <label>Qty</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Value<!--<span class="required" aria-required="true"> * </span>--></label>
                                </div>
                                <div class="col-md-1">
                                    <label>Length (cm)<!--<span class="required" aria-required="true"> * </span>--></label>
                                </div>
                                <div class="col-md-1">
                                    <label>Height (cm)<!--<span class="required" aria-required="true"> * </span>--></label>
                                </div>
                                <div class="col-md-1">
                                    <label>Width (cm)<!--<span class="required" aria-required="true"> * </span>--></label>
                                </div>
                                <div class="col-md-1">
                                    <label>Item Details<!--<span class="required" aria-required="true"> * </span>--></label>
                                </div>
                                <div class="col-md-2">
                                </div>
                            </div>
                            <div class="parcel_container">
                                <?php
                                $totalParcel = count($this->parcelData);
                                if ($totalParcel > 0 && $id > 0) {
                                    $countParcel = 0;
                                 //   echo "<pre>";
                                  //  print_r($this->parcelData);
                                  //  die;
                                    foreach ($this->parcelData as $parcelData) {
                                        ?>
                                        <div class="form-group">
                                            <div class="row">
                                                
                                                <div class="col-md-2">
                                                    <input type="text"  min="0" name="parcel['<?php echo $countParcel; ?>'][weight]" class="form-control parcel_weight input-sm" placeholder="Weight" value="<?php echo $parcelData->getWeight(); ?>" >
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text"  min="0" name="parcel['<?php echo $countParcel; ?>'][itemquantity]" class="form-control parcel_quantity input-sm" placeholder="Item Quantity" value="<?php echo $parcelData->getNumberItem(); ?>" >
                                                </div>
                                                <div class="col-md-2">
                                                    <?php $getItemvalue = array_sum(json_decode($parcelData->getItemvalue())); ?>
                                                    <input type="text"  min="0" name="parcel['<?php echo $countParcel; ?>'][itemvalue]" class="form-control parcel_value input-sm" placeholder="Value" value="<?php echo $getItemvalue; ?>" >
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"  min="0" name="parcel['<?php echo $countParcel; ?>'][length]" class="form-control parcel_length input-sm" placeholder="Length" value="<?php echo $parcelData->getLength(); ?>" >
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"  min="0" name="parcel['<?php echo $countParcel; ?>'][height]" class="form-control parcel_height input-sm" placeholder="height" value="<?php echo $parcelData->getHeight(); ?>" >
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text"  min="0" name="parcel['<?php echo $countParcel; ?>'][width]" class="form-control parcel_width input-sm" placeholder="width" value="<?php echo $parcelData->getWidth(); ?>" >
                                                </div>
                                                <div class="col-md-1">
                                                    <a here="#"  name="parcel['<?php echo $countParcel; ?>'][itemdetails]" class="btn btn-sm blue parcel_itemdetails" data-parcelcount ="<?php echo $countParcel?>"  data-original-title="" title="">Item Details</a>
                                                </div>
                                                <div class="col-md-2">
                                                    <?php
                                                    $initialbtn = ($totalParcel == 1) ? "initial-button" : "";
                                                    if ($totalParcel == ($countParcel + 1)) {
                                                        ?>
                                                        <button type="button" class="btn btn-success add-more-parcel-keys"><i class="fa fa-plus"></i></button>
                                                        <button type="button" class="btn btn-danger remove-parcel-key <?php echo $initialbtn; ?>"><i class="fa fa-minus"></i></button>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <button type="button" class="btn btn-danger remove-parcel-key"><i class="fa fa-minus"></i></button>
                                                        <?php
                                                    }
                                                    ?>
                                                    <input type="hidden" class="parcel_id" name="parcel['<?php echo $countParcel; ?>'][id]"  value="">
                                                    <input type="hidden" class="consignment_id" name="consignment_id"  value="<?php echo $parcelData->getConsignmentId(); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $countParcel++;
                                    }
                                } else {
                                    ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="text"  min="0" name="parcel[0][weight]" class="form-control parcel_weight input-sm" placeholder="Weight" value="" >
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text"  min="0" name="parcel[0][itemquantity]" class="form-control parcel_quantity input-sm" placeholder="Item Quantity" value="" >
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text"  min="0" name="parcel[0][itemvalue]" class="form-control parcel_value input-sm" placeholder="Value" value="" >
                                            </div>
                                            <div class="col-md-1">
                                                <input type="text"  min="0" name="parcel[0][length]" class="form-control parcel_length input-sm" placeholder="Length" value="0" >
                                            </div>
                                            <div class="col-md-1">
                                                <input type="text"  min="0" name="parcel[0][height]" class="form-control parcel_height input-sm" placeholder="height" value="0" >
                                            </div>
                                            <div class="col-md-1">
                                                <input type="text"  min="0" name="parcel[0][width]" class="form-control parcel_width input-sm" placeholder="width" value="0" >
                                            </div>
                                            <div class="col-md-1">
                                                <a here="#"  name="parcel[0][itemdetails]" class="btn btn-sm blue parcel_itemdetails" data-original-title="" title="" data-parcelcount ="0">Item Details</a>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-success add-more-parcel-keys"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-danger remove-parcel-key initial-button"><i class="fa fa-minus"></i></button>
                                                <input type="hidden" class="parcel_id" name="parcel[0][id]" value="" >
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="<?php echo $totalParcel; ?>" />
                            </div>
							<div class="row margin-bottom-10">
								<div class="col-md-2">
									<div class="has-float-label input-icon right">
										<i class="fa fa-cube"></i>
										<input type="text" name="item_weight" id="item_weight" class="form-control" readonly="" value="<?php echo $item_weight; ?>"  placeholder="Weight kg"/>
										<label>Weight kg<span class="required" aria-required="true"> * </span></label>
									</div>
								</div>
                                                                <div class="col-md-2">
                                                                    <div class="has-float-label input-icon right">
                                                                            <i class="fa fa-money"></i>
                                                                            <input readonly="readonly" type="text" name="item_quantity" id="item_quantity" class="form-control" value="<?php echo $item_quantity; ?>"  placeholder="Quantity"/>
                                                                            <label>Quantity</label>
                                                                    </div>
                                                                </div>
								<div class="col-md-2">
									<div class="has-float-label input-icon right">
										<i class="fa fa-money"></i>
										<input readonly="readonly" type="text" name="item_value" id="item_value" class="form-control" value="<?php echo $item_value; ?>"  placeholder="Value"/>
										<label>Value</label>
									</div>
								</div>
                                                                
							</div>
                        </div>
                    </div>
                    <div class="caption margin-bottom-10 block">
                        Consignment Details
                    </div>
                    <div class="row">
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                     <i class="fa fa-thumbs-up"></i>
                                    <input type="text" name="reference" id="reference" class="form-control" value="<?php echo $reference; ?>"  placeholder="Reference" />
                                    <label for="reference">Reference</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                    <?php
                                    // Currency array
                                    $arrayCurrency = array('GBP' => 'GBP','USD' => 'USD','CAN' => 'CAN','DFL' => 'DFL','DKR' => 'DKR','EUR' => 'EUR','FFR' => 'FFR','HKG' => 'HKG','INR' => 'INR','JPY' => 'JPY','NKR' => 'NKR','NLG' => 'NLG','SGD' => 'SGD','SFR' => 'SFR','SKR' => 'SKR','YEN' => 'YEN','PLN' => 'PLN');
                                    echo Ddl::generateArrayDDL('item_currency', $arrayCurrency, $item_currency, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Currency" placeholder="Currency"');?>
                                    <label for="item_currency">Currency</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                    <?php
                                    $itemType = array("Normal", "Letter", "Packets", "Lithium Battery", "Perfume", "Fire Extinguisher");
                                    echo Ddl::generateArrayDDL('item_type', $itemType, $item_type, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Item Type" placeholder="Item Type"');
                                    ?>
                                    <label for="item_type">Type</label>
                                </div>
                            </div>
                        </div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="has-float-label input-icon right">
									<i class="fa fa-file"></i>
									<input type="text" name="description" id="description" class="form-control" value="<?php echo $description; ?>"  placeholder="Description"/>
									<label for="description">Description <span class="required" aria-required="true"> * </span></label>
								</div>
								<!--<div class="has-float-label input-icon right">
                                     <i class="fa fa-money"></i>
                                    <input readonly="readonly" type="text" name="item_value" id="item_value" class="form-control" value="<?php /*echo $item_value; */?>"  placeholder="Items Value"/>
                                    <label>Items Value</label>
                                </div>-->
							</div>
						</div>
                    </div>
                    <div class="row">
                        <!--<div class="col-md-3">
                            <div class="form-group">
                                <div class="has-float-label input-icon  right">
                                     <i class="fa fa-cube"></i>
                                    <input type="text" name="item_weight" id="item_weight" class="form-control" readonly="" value="<?php /*echo $item_weight; */?>"  placeholder="Items Weight kg"/>
                                    <label>Items Weight kg<span class="required" aria-required="true"> * </span></label>
                                </div>
                            </div>
                        </div>-->
                        <!--<div class="col-md-6">
                            <div class="form-group">
                            </div>
                        </div>-->
                        <div class="col-md-12">
                            <div class="form-group">
								<div class="has-float-label input-icon right">
									<i class="fa fa-file"></i>
									<input type="text" name="notes" id="notes" class="form-control" value="<?php echo $notes; ?>"  placeholder="Notes"/>
									<label for="notes">Notes</label>
								</div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="row"  >
                        <!-- PAPER LESS TRADE PANEL -->      
                        <div class="col-md-6" id="plt-div" style="display:none;" >

                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Paperless Trade</label></legend>
                                
                                <div id="plt-details">
                                    <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Document</label> <br style="clear:both;"/>

                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                        <span class="fileinput-filename"> </span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="file_name" id="file_name" accept="application/pdf"> </span>
                                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                    <a href="javascript:;" class="input-group-addon btn blue" id="upload_file" data-original-title="" title="">Upload</a>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    
                                    
                                    <div class="row" id="append_user_doc">
                                         <?php
                                         if($id > 0 && $this->invoiceFile != ''){
                                            $documentName = explode(".", $this->invoiceFile);
                                            $docName = $documentName[0];
                                            $fileFullPath = "../_assets/paperless_invoice/" . $this->invoiceFile;
                                         ?>
                                        
                                        <div class="col-md-3 doc_upload_view" id="usr_doc_<?=$docName;?>">
                                        <div class="thumbnail">
                                        <img src="../images/pdf.png" style="max-width: 100%; max-height: 100px; display: block;" data-src="<?=$fileFullPath;?>">
                                        <div class="caption text-center" style="height: auto">
                                        <a target="_blank" href="<?=$fileFullPath;?>" class="btn blue btn-xs"> View </a> &nbsp;&nbsp;
                                        <a href="javascript:;" class="btn btn-xs red remove_doc" data-doc_id="<?=$this->invoiceFile;?>"> Remove </a>
                                        <input type="hidden" name="temp_invoice_name" id="temp_invoice_name" value="<?=$this->invoiceFile;?>" />
                                        </div>
                                        </div>
                                        </div>
                                        
                                        <?php
                                         } ?>
                                    </div>
                                    
                                </div>      
                                
                            </fieldset>
                        </div>
                        <!-- END OF PAPERLESS TRADE -->
                        
                        <!-- INSURANCE PANEL -->      
                        <div class="col-md-6" id="insurance-div" style="display: none;" >

                            <fieldset class="fsStyle">
                                <legend class="legendStyle"><label class="label-account">Protect Your Shipment</label></legend>
                                <div id="general">
                                    <div class="col-md-12" id="dangerous-details" style=" padding:0px; margin:0px;">
                                        <div class="col-md-12">
                                            <div class="form-group"> 
                                                <div class="input-group">
                                                    <h5>
                                                        The provision, at individual shipment level, 
                                                        of declared value coverage above Standard Liability for the amount necessary to repair or replace a shipment in the event of physical loss or damage. 
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group"> 
                                                <div class="input-group">
                                                    <?php
                                                        $selected = (($insurance_agree == "1") ? "checked" : "");
                                                        ?>
                                                        <label class="label-control">
                                                            <input <?php echo $selected; ?> id="insurance_agree"
                                                                                            name="insurance_agree" type="checkbox" class="icheck"
                                                                                            data-checkbox="icheckbox_flat-blue" value="1" /><b>  I would like to insure my shipment. </b>&nbsp;
                                                        </label>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>      
                                </div>
                            </fieldset>
                        </div>
                        <!-- END OF INSURANCE -->
                    </div>
                    
                    
                    
                    
                    <div class="row margin-top-20">
                        <div class="col-md-12 text-center">
                            <?php if ($this->labelGenreatedCheck == 0) { ?>
                            <button type="button" name="btnSave" id="btnSave" class="btn btn-primary btn_save"><?php echo (isset($_GET['id']) && $_GET['id'] > 0 ? "Update" : "Create") ?> Shipment</button>
                            <?php// if(!isset($_GET['id'])){ ?>
                                    <button type="button" name="btnSaveInvalid" id="btnSaveInvalid" class="btn btn-primary btn_save btn_save_invalid">Generate Label</button>
                            <?php //} ?>
                            <?php } ?>
                            <a href="javascript:{};" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                            <?php if(isset($_GET['id'])){ ?>
                            <a href='' class=" btn btn btn-default" title='Audit View'  id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='<?php echo $_GET['id'];?>' data-log_name='consignment' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>
                            <?php }?>
                            <input type="hidden" name="func" value="save_data_consignment" />
                            <input type="hidden" name="id" value="<?php echo $id; ?>" />
                            <input type="hidden" name="skuid" value="<?php echo $skuid; ?>" />
                            <input type="hidden" name="pickup_branchId" id="pickup_branchId" value="<?php echo $pickup_branchId; ?>" />
                            
                            
<!--                            <input type="hidden" name="user_id" value="<?php //echo (int) (trim(util_get('uaccount')) != '') ? base64_decode(util_get('uaccount')) : $this->user->getId(); ?>" />-->
                            <input type="hidden" id="save_invalid" name="save_invalid" value="0" />
                        </div>
                    </div>
                    <input type='hidden' name='is_product' id='is_product' value='<?php echo @$is_product; ?>' />
                    
                </form>
            </div>
        </div>
        <!-- Address pop up Hadi-->
        <div class="modal fade bs-modal-full in" id="portlet-address" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <div class="caption"> <i class="fa fa-maps"></i>
                             Address List
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-body">
                            <form class="form-horizontal" role="form" action="" method="POST" id="frm_address" name="frm_address">
                                <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="5%"> Actions </th>
                                            <th> Contact </th>
                                            <th> Company </th>
                                            <th> Address Line 1</th>
                                            <th> Address Line 2 </th>
                                            <th> Address Line 3 </th>
                                            <th> City </th>
                                            <th> Postcode </th>
                                            <th> State </th>
                                            <th> Country </th>
                                            <th> Email </th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td>
                                                <div class="margin-bottom-5">
                                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                </div>
                                            </td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_contact"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_company"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_address_line_1"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_address_line_2"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_address_line_3"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_city"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_postcode"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_state"> </td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_country"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="search_email"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <!--<button type="button" class="btn blue">Save changes</button>-->
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Service Modal Start -->
        <!-- /.modal-dialog -->
        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="show-service-info" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="show-service-info-name"></span> Details </h4>
                    </div>
                    <div class="modal-body">
                        <div id="show-service-info-detail">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Service Modal End -->
        <!-- Drop Off Modal End -->
        <div class="modal fade" id="drop_off_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
<!--                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Modal Title</h4>
                    </div>-->
                    <div class="modal-body">
                        <button type="button" class="close pull-right" data-dismiss="modal" aria-hidden="true"></button>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City</label>
                                    <div class="input-group has-float-label input-icon">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="drop_off_city" id="drop_off_city" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Postcode</label>
                                    <div class="input-group has-float-label input-icon">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="drop_off_postcode" id="drop_off_postcode" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                        <button type="button" name="btn_reload_map" id="btn_reload_map" class="btn btn-primary">Load Map</button>
                                        <input type="hidden" id="pickup" name="pickup" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="position:relative;">
                                <div id="map"></div>
                            </div>
                            <div class="col-md-3 portlet light pull-right"  style="display: none; right: 0px; position:absolute; height: 600px; overflow: auto;">
                                <button type="button" id="close_drop_off_detail" class="close pull-right"></button>
                                <div class="col-md-12" id="dropoff-content-display"></div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" class="btn green">Save changes</button>
                    </div>-->
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Drop Off Modal End -->
       <!-- Item Details Modal Start -->
        <!-- /.modal-dialog -->
        <div class="modal fade bs-modal-full" tabindex="-1" role="dialog" id="add-item-details" >
            <div class="modal-dialog modal-full">
                <form name="itemdetail_save" id="itemdetail_save" action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"> Item Details </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="shw_itm_message" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-danger"></div>
                            </div>
                        </div>
                        <div class="row">        
                            <div class="col-md-2">
                                <label>Description<span class="required" aria-required="true"> * </span></label>
                            </div>
                            <div class="col-md-1">
                                <label>SKU</label>
                            </div>
                            <div class="col-md-2">
                                <label>URL<!--<span class="required" aria-required="true"> * </span>--></label>
                            </div>
                            <div class="col-md-1">
                                <label>Quantity<!--<span class="required" aria-required="true"> * </span>--></label>
                            </div>
                            <div class="col-md-1">
                                <label>Value<!--<span class="required" aria-required="true"> * </span>--></label>
                            </div>
                            <div class="col-md-1">
                                <label>Weight<!--<span class="required" aria-required="true"> * </span>--></label>
                            </div>
                            <div class="col-md-1">
                                <label>HS Code</label>
                            </div>
                            <div class="col-md-2">
                                <label>Manufacture Country</label>
                            </div>
                            <div class="col-md-1">
                            </div>
                        </div>
                        <div class="ItemInformation">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type='text'  name='item[0][item_description]' value='<?php echo $item_description; ?>'  placeholder="Description"  class="form-control item_description"  rel="tooltip" title="Description"  required />
                                    </div>
                                    <div class="col-md-1">
                                        <?php if(util_get("skuid") != ''){ 
                                            $skuid = base64_decode(util_get("skuid"));
                                             echo Ddl::generateDDL('item[0][item_sku]', "skuFilter", ' id in (select sku_id from sku_order_mapping where sku_order_id = "'.$skuid.'")', "sku", "id", $item_sku, ' class="bs-select input-sm form-control form-filter item_sku" data-container="body" data-live-search="true"  data-show-subtext="true"', 'Select SKU', '', 'item_sku_0');
                                         } else { ?>
                                        <input type='text'  name='item[0][item_sku]'  value='<?php echo $item_sku; ?>'  placeholder="SKU"  class="form-control item_sku"  rel="tooltip" title="SKU"  />
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-2">
                                        <input type='text'  name='item[0][item_url]'   value='<?php echo $item_url; ?>'   placeholder="URL"  class="form-control item_url"  rel="tooltip" title="URL"  />
                                    </div>
                                    <div class="col-md-1">
                                        <input type='text'  name='item[0][item_qty]' value='<?php echo $item_qty; ?>'  placeholder="Quantity"  class="form-control item_qty"  rel="tooltip" title="Quantity"   />
                                    </div>
                                    <div class="col-md-1">
                                        <input type='text'  name='item[0][item_value]' value='<?php echo $item_value; ?>'  placeholder="Value"  class="form-control item_value"  rel="tooltip" title="Value"   />
                                    </div>
                                    <div class="col-md-1">
                                        <input type='text'  name='item[0][item_weight]' value='<?php echo $item_weight; ?>'  placeholder="Weight"  class="form-control item_weight"  rel="tooltip" title="Weight"   />
                                    </div>
                                    <div class="col-md-1">
                                        <input type='text'  name='item[0][item_hscode]' value='<?php echo $item_hscode; ?>'  placeholder="HS Code"  class="form-control item_hscode"  rel="tooltip" title="HS Code"   />
                                    </div>
                                    <div class="col-md-2">
<!--                                        <input type='text'  name='item[0][item_country]' value='<?php echo $item_country; ?>'  placeholder="Country"  class="form-control item_country"  rel="tooltip" title="Country"   />-->
                                       <?php echo Ddl::generateCountryDDL('item[0][item_country]', $item_country, 'iso', ' class="not_clear item_country form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" ', 'item_country_0'); ?>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-success add-more-item-keys"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger remove-item-key initialitem-button"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="btnSaveItemDetails" class="btn blue">Save</button>
                        <input type="hidden" name="func" value="save_itemdetail" />
                        <input type="hidden" name="piecenumber" id="piecenumber" value="" />
                    </div>
                </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Item Details Modal End -->
        
        
        <?php
    }
    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }
}
// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
