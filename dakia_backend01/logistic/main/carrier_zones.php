<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'carrier.class',
                    'carrierfilter.class',
                    'country.class',
                    'countryfilter.class',
                    'carrierzones.class',
                    'carrierzonesfilter.class',
                    'carrierzonescountries.class',
                    'carrierzonescountriesfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'carrierzonespostcode.class' ,
                    'carrierzonespostcodefilter.class'
                ]);
class Page extends BasePage {
    private $csvCheck;
    private $sessionUser;
    private $carrierZoneFilter = '';
    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Carrier Zones"
        );
        $this->sessionUser = SessionManager::getUser();
        if ($this->sessionUser->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }

        if($this->sessionUser->getUserType() == User::USER_TYPE_CORPORATE) {
            $carrierFilter = new CarrierFilter();
            $carrierFilter->addFilter("usr.user_account_id = '" . $this->sessionUser->getUserAccountId() . "'");
            $allowedCarriersObj = $carrierFilter->getCarrierSetupList('DISTINCT cl.id');
            $allowedCarriers = [];
            if (!empty($allowedCarriersObj)) {
                foreach ($allowedCarriersObj as $allowedCarrier) {
                    $allowedCarriers[] = $allowedCarrier->getId();
                }
                $this->carrierZoneFilter = ' cz.carrier_id IN ('.implode(",",$allowedCarriers).')';
            }
            else{
                $this->carrierZoneFilter = " cz.carrier_id = '-50000'";
            }
        }
        /*
         * DataTable handlings
         */
        if(isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_carrier_services'){
            // Auto Select Services on the based of Carrier Id from Carrier List Page
            $output = '';
            $carrierId = $this->form_vars['carrier_id'];
            $serviceId = $this->form_vars['service_id'];
            $serviceObj = new Services($serviceId);
            $csvCheck = $this->form_vars['csv_check'];
            $ServiceFilter = new ServiceFilter();
            if($csvCheck == 2) {
                if($carrierId > 0) {
                    $ServiceFilter->addCarrierFilter($carrierId);
                }
            } else {
                $ServiceFilter->addCarrierFilter($carrierId);
            }
            if($this->sessionUser->getUserType() != USER::USER_TYPE_ADMIN) {
                $carrierFilter = new CarrierFilter();
                $carrierFilter->addFilter("      usr.user_account_id = '" . $this->sessionUser->getUserAccountId() . "'");
                $allowedServicesObj = $carrierFilter->getCarrierSetupList('     DISTINCT s.id');
                $allowedServices = [];
                if (!empty($allowedServicesObj)) {
                    foreach ($allowedServicesObj as $allowedService) {
                        $allowedServices[] = $allowedService->getId();
                    }
                    $ServiceFilter->addFilter('      ser.id IN ('.implode(",",$allowedServices).')');
                }else{
                    $ServiceFilter->addFilter("      ser.id = '-50000'");
                }
            }
            $servicesList = $ServiceFilter->getCarrierServicesList('ser.id, ser.name, ser.code,ser.zone_type');
            // load services against the csv model drop down
            if($csvCheck == 1) {
                $output = '<select data-show_service="'.$carrierZoneBase.'"  name="csv_service_id" id="csv_service_id" class="form-control select2" data-original-title="" data-live-search="true" title=""><option value="">Select Service</option>';
                if(count($servicesList) > 0){
                    foreach($servicesList as $service){
                        $selected = ($service->getId() == $serviceId ? ' selected="selected"' : '');
                        $output .= '<option value="'.$service->getId().'"'.$selected.' data-zone_type="'.$service->getZoneType().'" >'.$service->getName().' ['.$service->getCode().']</option>';
                    }
                }
                $output .= '</select>';
            }
            // load services against the save 
            else if($csvCheck == 0) {
                $output = '<select name="service_id" id="service_id" class="form-control select2" data-original-title="" data-live-search="true" title=""><option value="">Select Service</option>';
                if(count($servicesList) > 0){
                    foreach($servicesList as $service){
                        $selected = ($service->getId() == $serviceId ? ' selected="selected"' : '');
                        $output .= '<option value="'.$service->getId().'"'.$selected.' data-zone_type="'.$service->getZoneType().'" >'.$service->getName().' ['.$service->getCode().']</option>';
                    }
                }
                $output .= '</select>';
            } 
            // load services against search 
            else if($csvCheck == 2) {
                $output = '<select data-show_service="'.$carrierZoneBase.'" name="search_service_id" id="search_service_id" class="select2 input-sm form-control form-filter" data-original-title="" data-live-search="true" title=""><option value="">Select Service</option>';
                if(count($servicesList) > 0){
                    foreach($servicesList as $service){
                        $selected = ($service->getId() == $serviceId ? ' selected="selected"' : '');
                        $output .= '<option value="'.$service->getId().'"'.$selected.' data-zone_type="'.$service->getZoneType().'" >'.$service->getName().' ['.$service->getCode().']</option>';
                    }
                }
                $output .= '</select>';
            }
            $return['services'] = $output;
            $return['zone_type'] = $serviceObj->getZoneType();
            echo json_encode($return);
            exit;
        }     
        if (isset($_GET['action']) && $_GET['action'] == "carrier_zones_ajax") {
            $carrierZonesFilter = new CarrierZonesFilter();
            $carrierZonesFilter->addIsDeletedFilter();
            $carrierZonesFilter->addCarrierJoin();
            if ($this->carrierZoneFilter != '') {
                $carrierZonesFilter->addFilter($this->carrierZoneFilter);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $carrierId = $this->form_vars['search_carrier_id'];
                if (!empty($carrierId))
                    $carrierZonesFilter->addFieldFilter('cz.carrier_id',$carrierId);

                $name = $this->form_vars['name'];
                if (!empty($name))
                    $carrierZonesFilter->addFieldLikeFilter('name', $name);
                
                $serviceId = $this->form_vars['search_service_id'];
                if (!empty($serviceId))
                    $carrierZonesFilter->addFieldFilter('cz.service_id',$serviceId);
               
            }

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
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $carrierZonesFilter->AddOrderBy(strtolower("cz." . $dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iTotalRecords = $carrierZonesFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $carrierZonesFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $carrierZonesFilter->setOffset($iDisplayStart);
            $carrierZonesFilter->AddOrderBy("cz.sort_order");
            $carrierZonesObjs = $carrierZonesFilter->getPagingList('cz.id,cz.carrier_id,cz.service_id,c.carrier AS carrier_name,c.logo AS carrier_logo,cz.name,cz.status,cz.sort_order');
            $setDataArr = array();
            foreach ($carrierZonesObjs as $carrierZonesObj) {
                $currentArr = array();
                $currentArr['name'] = $carrierZonesObj->getName();
                $currentArr['carrier_id'] = '<img src="../images/carrierlogo/thumbnail/owe_16_'.$carrierZonesObj->getCarrierLogo().'" alt="" /> '.$carrierZonesObj->getCarrierName();
                $carrierService = '';
                $zoneType = '';
                if(!empty($carrierZonesObj->getServiceId())) {
                    $carrierServiceObj = new Services($carrierZonesObj->getServiceId());
                    $carrierService = $carrierServiceObj->getName()." [".$carrierServiceObj->getCode()."]";
                    $zoneType = $carrierServiceObj->getZoneType();
                }
                $currentArr['service_id'] = $carrierService;
                if(!empty($carrierZonesObj->getCarrierId())) {
                    $carrierObj = new Carrier($carrierZonesObj->getCarrierId());
                    if($carrierObj->getZoneBase() == 1) {
                        $zoneType = $carrierObj->getZoneType();
                    }
                }
                $currentArr['actions'] = '';
                $currentArr['actions'] = '<div class="btn-group">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">';
                if (Permissions::checkFilePermission('carrier_zone_delete')) {
                    $currentArr['actions'] .= '<li>
                                                    <a title="Delete" href="javascript:;" data-zone_id="' . $carrierZonesObj->getId() . '" class="btndelete" >
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </li>';
                }
                if (Permissions::checkFilePermission('carrier_zone_country_list')) {
                    $currentArr['actions'] .= '<li>
                                                    <a title="'.(($zoneType == "country") ? "Zone Countries" : "Zone Postcode").'" href="javascript:;" data-title="Carrier Zones" data-carrier_zone_name="'.$carrierZonesObj->getName().'" data-carrier_zone_id="'.$carrierZonesObj->getId().'" class="carrier_zone_countries" >
                                                        <i class="fa fa-flag"></i> '.(($zoneType == "country") ? "Zone Countries" : "Zone Postcode").'
                                                    </a>
                                                </li>';
                }
                if (Permissions::checkFilePermission('carrier_zone_edit')) {
                    $currentArr['actions'] .= '<li>
                                                    <a title="Edit" href="javascript:;" class="btnedit"  data-zone_id="' . $carrierZonesObj->getId() . '" >
                                                        <i class="glyphicon glyphicon-pencil"></i> Edit
                                                    </a>
                                                </li>';
                }
                 $currentArr['actions'] .= "<li>"
                                            . "<a href='' title='Audit View'  id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $carrierZonesObj->getId() . "' data-log_name='carrier_zones' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>"
                                            . "</li>";
                $currentArr['actions'] .= '</ul> </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == "save_carrier_zone") {
            $output = [];
            $output['STATUS'] = 'error';
            $output['MESSAGE'] = 'Something went wrong.';
            $user = SessionManager::getUser();
            $id = $this->form_vars['carrier_zone_id'];
            $carrierId = $this->form_vars['carrier_id'];
            $serviceId = $this->form_vars['service_id'];
            $name = $this->form_vars['name'];
            $sortOrder = $this->form_vars['sort_order'];
            $zoneCountries = $this->form_vars['zone_countries'];
            $postcodeData = nl2br($this->form_vars['postcode']);
            $postcodes = $this->multiexplode(['<br />',','],$postcodeData);
            $carrierZoneBase = 0;
            $zoneType = 0; /* 0 => postcodes , 1  => countries */
            if(isset($this->form_vars['zone_type']) && $this->form_vars['zone_type'] == "country") {
                $zoneType = 1;
            }
            $errors = [];
            if (empty($carrierId)) {
                $errors[] = "Select carrier";
            }
            if (empty($name)) {
                $errors[] = "Enter zone name";
            }
            if($zoneType == 1) {
                if (empty($zoneCountries)) {
                    $errors[] = "Select zone countries";
                }
            } else {
                if (empty($postcodes)) {
                    $errors[] = "Select zone postcode";
                }
            }
            if (!empty($carrierId)) {
                $carrierObj = new Carrier($carrierId);
                $carrierZoneBase = $carrierObj->getZoneBase();
                if ($carrierZoneBase != 1 && empty($serviceId)) {
                    $errors[] = "Select carrier service";
                }
            }
            $validateServiceId = '';
            $validateZoneId = '';
            if ($carrierZoneBase != 1) {
                $validateServiceId = $serviceId;
            }
            if (!empty($id) && $id > 0) {
                $validateZoneId = $id;
            }
            if (!empty($zoneCountries)) {
                $validateCountry = CarrierZonesCountries::validateCarrierZonesCountries($zoneCountries, $carrierId, $validateServiceId, $validateZoneId);
                if (!empty($validateCountry)) {
                    $validateError = '';
                    foreach ($validateCountry as $country => $zone) {
                        $validateError .= ($validateError != '' ? '<br />' : '') . $country . " already exists in " . $zone;
                    }
                    $errors[] = $validateError;
                }
            }
            if (!empty($errors)) {
                $errorStr = implode('<br />', $errors);
                $output['STATUS'] = 'error';
                $output['MESSAGE'] = $errorStr;
            } else {
                if (!empty($id) && $id > 0) {
                    $carrierZone = new CarrierZones($id);
                } else {
                    $carrierZone = new CarrierZones();
                }
                $carrierZone->setCarrierId($carrierId);
                if ($carrierZoneBase != 1) {
                    $carrierZone->setServiceId($serviceId);
                } else {
                    $carrierZone->setServiceId(0);
                }
                $carrierZone->setName($name);
                if(trim($sortOrder) == '')
                      $sortOrder = 0;
                $carrierZone->setSortOrder($sortOrder);
                $carrierZone->setStatus(1);
                if ($id > 0) {
                    $carrierZone->setUpdatedBy($user->getId());
                    $carrierZone->setDateUpdated(date('Y-m-d H:i:s'));
                } else {
                    $carrierZone->setAddedBy($user->getId());
                    $carrierZone->setDateAdded(date('Y-m-d H:i:s'));
                    $carrierZone->setDateUpdated(date('Y-m-d H:i:s'));
                }
                $carrierZone->saveLog = false;
                $carrierZone->save();
                $carrierZoneId = $carrierZone->getId();
                if (!empty($id)) {
                    $carrierZoneId = $id;
                }
                $new_data_p = [];
                $old_data_p = [];
                $oldZoneCountryIds = [];
                $oldZonePostcodes = [];
                $allPostcodes = [];
                $allCountryIds = [];
                if ($carrierZoneId > 0) {
                    if($zoneType == 1) {
                        $oldZoneCountries = new CarrierZonesCountries();
                        $oldZoneCountries = $oldZoneCountries->getZoneCountries($carrierZoneId);
                        foreach ($oldZoneCountries as $oldZoneCountry) {
                            $allCountryIds[] = $oldZoneCountry->getCountryId();
                            if (!in_array($oldZoneCountry->getCountryId(), $zoneCountries)) {
                                $country = new Country($oldZoneCountry->getCountryId());
                                $oldZoneCountryIds[] = $country->getName();
                            }
                        }
                        CarrierZonesCountries::deleteCountriesByZoneId($carrierZoneId);
                        foreach ($zoneCountries as $zoneCountryId) {
                            $carrierZonesCountries = new CarrierZonesCountries();
                            $carrierZonesCountries->setCarrierZoneId($carrierZoneId);
                            if (!in_array($zoneCountryId, $allCountryIds)) {
                                $country = new Country($zoneCountryId);
                                $countries[] = $country->getName();
                            }
                            $carrierZonesCountries->setCountryId($zoneCountryId);
                            $carrierZonesCountries->save();
                        }
                        if (!empty($countries)) {
                            $new_data_p['countries'] = $countries;
                        }
                        if (!empty($oldZoneCountryIds)) {
                            $old_data_p['countries'] = $oldZoneCountryIds;
                        }
                    } else {
                        $oldPostcode = new CarrierZonesPostcode();
                        $oldPostcodes = $oldPostcode->getZonePostcodes($carrierZoneId);
                        foreach ($oldPostcodes as $oldZonePostcode) {
                            $allPostcodes[] = $oldZonePostcode->getPostcode();
                            if (!in_array($oldZonePostcode->getPostcode(), $postcodes)) {
                                $oldZonePostcodes[] = $oldZonePostcode->getPostcode();
                            }
                        }
                        CarrierZonesPostcode::deletePostcodesByZoneId($carrierZoneId);
                        $newPostcodes = [];
                        foreach ($postcodes as $postcode) {
                            $carrierZonesPostcode = new CarrierZonesPostcode();
                            $carrierZonesPostcode->setCarrierZoneId($carrierZoneId);
                            if (!in_array(trim($postcode), $allPostcodes)) {
                                $newPostcodes[] = trim($postcode);
                            }
                            $carrierZonesPostcode->setPostcode(trim($postcode));
                            $carrierZonesPostcode->save();
                        }
                        if (!empty($newPostcodes)) {
                            $new_data_p['countries'] = $newPostcodes;
                        }
                        if (!empty($oldZonePostcodes)) {
                            $old_data_p['countries'] = $oldZonePostcodes;
                        }
                    }
                    $carrierZone->logMoreDataOld = $old_data_p;
                    $carrierZone->logMoreDataNew = $new_data_p;
                    $carrierZone->saveAuditData();
                }
                $output['STATUS'] = 'success';
                $output['MESSAGE'] = "Zone saved successfully";
            }
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_countries_list") {
            $output = '';
            $carrierZoneId = $this->form_vars['carrier_zone_id'];
            $carrierZonesCountries = new CarrierZonesCountries();
            $zoneCountries = $carrierZonesCountries->getZoneCountries($carrierZoneId);
            $output .= '<div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>';
            if(!empty($zoneCountries)){
                $i = 0;
                foreach($zoneCountries as $zoneCountry){
                    if ($i % 4 == 0) {
                        $output .= '</tr><tr>';
                    }
                    $output .= '<td>';
                    $output .= '<img src="/assets/global/img/flags/' . strtolower($zoneCountry->getCountryIso()) . '.png" />&nbsp;&nbsp;'.$zoneCountry->getCountryName();
                    $output .= '</td>';
                    $i++;
                }
            }else{
                $carrierZonePostcodes = new CarrierZonesPostcode();
                $zonePostcodes = $carrierZonePostcodes->getZonePostcodes($carrierZoneId);
                if(!empty($zonePostcodes)) {
                    $i = 0;
                    foreach($zonePostcodes as $zonePostcode){
                        if ($i % 4 == 0) {
                            $output .= '</tr><tr>';
                        }
                        $output .= '<td>';
                        $output .= $zonePostcode->getPostcode();
                        $output .= '</td>';
                        $i++;
                    }
                } else {
                    $output .= '<tr><td>No country found.</td></tr>';
                }
            }
            $output .= '                </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>';
            echo $output;
            exit;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "edit") {
            $zoneId = $this->form_vars['zone_id'];
            if ($zoneId > 0) {
                $carrierZone = new CarrierZones($zoneId);
                $returnMsg['STATUS'] = "success";
                $returnMsg['name'] = $carrierZone->getName();
                $returnMsg['carrier_id'] = $carrierZone->getCarrierId();
                $returnMsg['sort_order'] = $carrierZone->getSortOrder();
                $returnMsg['service_id'] = $carrierZone->getServiceId();
                $zoneCountriesObj = CarrierZonesCountries::getZoneCountries($zoneId);
                $zoneCountries = [];
                if(count($zoneCountriesObj) > 0){
                    foreach($zoneCountriesObj as $zoneCountry){
                        $zoneCountries[] = $zoneCountry->getCountryId();
                    }
                }
                $returnMsg['zone_countries'] = $zoneCountries;
                $zonePostcodeObj = CarrierZonesPostcode::getZonePostcodes($zoneId);
                $zonePostcodes = [];
                if(count($zonePostcodeObj) > 0){
                    foreach($zonePostcodeObj as $zonePostcode){
                        $zonePostcodes[] = $zonePostcode->getPostcode();
                    }
                }
                $returnMsg['zone_postcodes'] = implode(',',$zonePostcodes);
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download') {
            $search_Name = $this->form_vars['name_download'];
            $search_Carrier = $this->form_vars['carrier_id_download'];
            $search_Service = $this->form_vars['service_id_download'];
            $carrierZonesFilter = new CarrierZonesFilter();
            if($this->sessionUser->getUserType() == User::USER_TYPE_CORPORATE) {
                $carrierFilter = new CarrierFilter();
                $carrierFilter->addFilter("usr.user_account_id = '" . $this->sessionUser->getUserAccountId() . "'");
                $allowedCarriersObj = $carrierFilter->getCarrierSetupList('DISTINCT cl.id');
                $allowedCarriers = [];
                if (!empty($allowedCarriersObj)) {
                    foreach ($allowedCarriersObj as $allowedCarrier) {
                        $allowedCarriers[] = $allowedCarrier->getId();
                    }
                    if (!empty($search_Carrier)) {
                        $allowedCarriers[] = $search_Carrier;
                    }
                    $carrierZonesFilter->addFilterIn('   cz.carrier_id',$allowedCarriers);
                }
            } else {
                if (!empty($search_Carrier)) {
                    $carrierZonesFilter->addFieldFilter('   cz.carrier_id',$search_Carrier);
                }
            }
            $carrierZonesFilter->addIsDeletedFilter();
            $carrierZonesFilter->addCarrierJoin();
            
            if (!empty($search_Service))
                $carrierZonesFilter->addFieldFilter('   cz.service_id',$search_Service);
            if (!empty($search_Name))
                $carrierZonesFilter->addFieldLikeFilter('   cz.name', $search_Name);
            $returnString = "Name, Carrier, Services";
            $fileName = "carrier_zone".time();
//            $carrierZonesFilter->setRowsPerPage(1000);
            //$carrierZonesFilter->setOffset(1000);
            $carrierZonesFilter->AddOrderBy("   cz.sort_order");
            $res = $carrierZonesFilter->getPagingList('cz.id,cz.carrier_id,cz.service_id,c.carrier AS carrier_name,c.logo AS carrier_logo,cz.name,cz.status,cz.sort_order',false);
           
            if (count($res) > 0) {
                foreach ($res as $resultData) {
                //  $carrierName = new Carrier($resultData->getCarrierId());
                //  $serviceName = new Services($resultData->getServiceId());
                $carrierService = '';
                if(!empty($resultData->getServiceId())) {
                    $carrierServiceObj = new Services($resultData->getServiceId());
                    $carrierService = $carrierServiceObj->getName()." [".$carrierServiceObj->getCode()."]";
                }
                $returnString .= "\r\n";
                $returnString .= cleanCsvCall($resultData->getName()) . ",";
                $returnString .= cleanCsvCall($resultData->getCarrierName()) . ",";
                $returnString .= cleanCsvCall($carrierService);
                  //$returnString .= $carrierName->getCarrierDisplayName().",";
                  //$returnString .= $serviceName->getName();
                }
            }
//                else
//                {
//                    $returnString = "There no data to download";
//                }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
            
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $output = array();
            $uniqueName = array();
            $output['status'] = 'error';
            $output['message'] = 'Something went wrong.';
            $carrierId = $this->form_vars['csv_carrier_id'];
            $serviceId = $this->form_vars['csv_service_id'];
            $carrierZoneBase = 1;
            
            /*
             * Country Iso Loading 
             */
            $countryFilter  = new CountryFilter();
            $countryIsos = $countryFilter->getColumnList("iso");
            $countryIsoArray = []; 
            if(count($countryIsos)>0)
                foreach($countryIsos as $conIso)
                    $countryIsoArray[] =$conIso->getIso() ;
            // validation
             $errors = [];
            if(empty($carrierId)){
                $errors[] = "Select carrier";
                $output['status'] = 'fail';
                $output['message'] = "Select carrier";
                echo json_encode($output);
                exit;
            }
            if(!empty($carrierId)) {
                $carrierObj = new Carrier($carrierId);
                $carrierZoneBase = $carrierObj->getZoneBase();
                if($carrierZoneBase == 0 && empty($serviceId)){
                    $output['status'] = 'fail';
                    $output['message'] = "Select carrier service";
                    echo json_encode($output);
                    exit;
                }
            }
            
            if($carrierZoneBase == 0){
                $serviceId = $this->form_vars['csv_service_id'];
            }else{
                $serviceId = 0;
            }
            $multiZoneCountriesIds = [];
            $zoneCountriesArr = [];
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $relPath = '../_assets/carrierzones_csv/'.$new_file_name;
                    if (!file_exists("../_assets/carrierzones_csv/"))
                        @mkdir("../_assets/carrierzones_csv/", 0775);
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                        $row = 1;
                        $i=0;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = '';
                            $successRecords = 0;
                            $errorRecords = 0;
                            $zones = [];
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                if ($row < 2) {
                                    if (count($data) != 2) {
                                        $output['message'] = "Please check your CSV, it should only have 2 columns, Zone Name, Country Iso";
                                        $output['status'] = 'fail';
                                        echo json_encode($output) ;
                                        die;
                                    }
                                    $row++;
                                    continue;
                                }
                                $zoneName = $data[0];
                                $countryIso = $data[1];
                                if(!in_array($countryIso, $countryIsoArray)){
                                    $output['message'] = $countryIso." Country Iso code is not valid. Please check and try again.";
                                    $output['status'] = 'fail';
                                    echo json_encode($output) ;
                                        die;
                                }
                                
                                $zoneCountriesArr[$zoneName][] = $countryIso;
                                $carrierZoneFilter = new CarrierZonesFilter();
                                $carrierZoneFilter->addFieldFilter("    carrier_id", $carrierId);
                                if($carrierZoneBase == 0)
                                    $carrierZoneFilter->addFieldFilter("    service_id", $serviceId);
                                $carrierZoneFilter->addFieldFilter("name", $zoneName);
                                $carrierZoneFilter->addIsDeletedFilter();
                                if(count($carrierZoneFilter->getList("id")) == 0 && !in_array($zoneName,$zones)) {
                                    $zones[] = $zoneName;
                                    $csvContent .= (!empty($csvContent) ? "\r\n" : "").$zoneName;   
                                    $successRecords++;
                                } else {
                                    $errorRecords++;
                                }
                                $row++;
                                $i++;
                            }
                            
                            $CurFileName = "carrierzones_csv". time() .".csv";
                            //Write File
                            $CurFileContent = "../_assets/carrierzones_csv/".$CurFileName;
                            $message = '';
                            
                            if(!empty($csvContent)){
                                if(file_put_contents($CurFileContent,$csvContent)){
                                    $output['file_name'] = $new_file_name;
                                    $newDate = date('Y-m-d H:i:s');
                                    $load_data_sql = "LOAD DATA LOCAL INFILE '../_assets/carrierzones_csv/".$CurFileName. "' INTO TABLE `carrier_zones` FIELDS ENCLOSED BY '\"'
                                            TERMINATED BY ',' LINES TERMINATED BY '\r\n' (
                                                              `name`
                                                            ) SET carrier_id = '".$carrierId ."', service_id = '".$serviceId ."' , status='1' , deleted = '0' , added_by =  '" . $user->getId() . "' , date_added='".$newDate."'";
                                    $output['sql'] = $load_data_sql;
                                    $res = DbAccess3::runQueryWithError($load_data_sql, true);
                                    if ($res === false) {
                                        $error = DbAccess3::$dbError;
                                        $output['message'] = print_r($error,true);
                                        $output['status'] = 'fail';
                                    }else{
                                        unlink("../_assets/carrierzones_csv/".$CurFileName);
                                        $message .= 'Uploaded Zone successfully with';
                                        if($successRecords>0)
                                            $message .= '<br /> ' . $successRecords . ' Records imported successfully.';
                                        if($errorRecords>0)
                                            $message .= '<br /> ' . $errorRecords . ' Zones already exists.';
                                        $output['status'] = 'success';
                                    }
                                }
                            }else{
                                $message .= 'Uploaded zone successfully with';
                                if($successRecords>0)
                                    $message .= '<br /> ' . $successRecords . ' Records imported successfully.';
                                if($errorRecords>0)
                                    $message .= '<br /> ' . $errorRecords . ' Zones already exists.';
                            }
                            /* for zone country multi array */
                            if(count($zoneCountriesArr)) {
                                foreach($zoneCountriesArr as $zone => $countryIso) {
                                    $carrierZoneFilter = new CarrierZonesFilter();
                                    $carrierZoneFilter->addFieldFilter("    carrier_id", $carrierId);
                                    if($carrierZoneBase == 0) {
                                        $carrierZoneFilter->addFieldFilter("    service_id", $serviceId);
                                    }
                                    $carrierZoneFilter->addFieldFilter("name", $zone);
                                    $carrierZoneFilter->addIsDeletedFilter();
                                    $getZoneId = '';
                                    $carrierZoneFilterObj = $carrierZoneFilter->getList("cz.id,cz.name");
                                    if(count($carrierZoneFilterObj)) {
                                        $getZoneId = $carrierZoneFilterObj[0]->getId();
                                    }
                                    foreach($countryIso as $iso) {
                                        $countryFilter = new CountryFilter();
                                        $countryFilter->addIsoFilter($iso);
                                        $countryResObj = $countryFilter->getColumnList('name');
                                        if(count($countryResObj) > 0 && $getZoneId > 0){
                                            $multiZoneCountriesIds[$getZoneId][] = $countryResObj[0]->getId();
                                        }
                                    }
                                }
                            }
                            if(count($multiZoneCountriesIds)) {
                                $errorArrs = [];
                                foreach($multiZoneCountriesIds as $zone_id => $countryArr) {
                                    $errorCountryS = $this->saveCarrierZone($countryArr, $carrierId, $serviceId, $zone_id);
                                    if(!empty($errorCountryS))
                                        $errorArrs = $errorCountryS;
                                }
                                
                                if(count($errorArrs)>0) {
                                    $message .= '<br /> Uploaded error in countries';
                                    foreach($errorArrs as $errorArr) {
                                        $message .= implode('<br /> ', $errorArr);
                                    }
                                } else {
                                    $message .= '<br />  Uploaded countries successfully';
                                }
                            }
                            /* end for zone country multi array */
                            $output['message'] = $message;
                        }
                    } else {
                        $output['message'] = 'File upload fail.';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = 'Invalid CSV File.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = 'No file found to import data.';
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        }
        /*
         * Import Csv Country
         * Get: zone id
         * Return: Message json encoded array
         */
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file_country') {
            @$csv_file = $_FILES['csv_file_country'];
            $output = $this->uploadZoneCountryCsv($this->form_vars, $csv_file,"single");
            echo json_encode($output);
            exit;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            
            
            $user = SessionManager::getUser();
            $carrierZoneId = $this->form_vars['carrier_zone_id'];
            
            if ($carrierZoneId > 0) {
                $carrierZone = new CarrierZones($carrierZoneId);
                //$oldRemoteareasGroupsData = serialize($remoteareas);
                $returnMsg['STATUS'] = "success";
                $carrierZone->setUpdatedBy($user->getId());
                $carrierZone->setDateUpdated(date('Y-m-d H:i:s'));
                $carrierZone->setStatus("2");
                $carrierZone->save();
                /*
                * Add Remoteareas Log details
                */
                //$remoteareasGroupsLog = new RemoteareasGroupsLog();
                //$newRemoteareasGroupsData = serialize($remoteareas);
                //$remoteareasGroupsLog->createlog($user->getId(),'',$remoteareasId,'REMOTEAREAS_GROUPS',$user->getUserName() . ' has deleted ' . $remoteareasId,$oldRemoteareasGroupsData, $newRemoteareasGroupsData);
                echo json_encode($returnMsg);
            } 
            else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "check_download_carrierzones") {
            $carrierZonesFilter = new CarrierZonesFilter();
            $carrierZonesFilter->addIsDeletedFilter();
            $carrierZonesFilter->addCarrierJoin();
            $search_Name = $this->form_vars['search_name'];
            $search_Carrier_Id = $this->form_vars['search_carrier_id'];
            $search_Service_Id = $this->form_vars['search_service_id'];
            if (!empty($search_Carrier_Id))
                $carrierZonesFilter->addFieldFilter('cz.carrier_id', $search_Carrier_Id);
            if (!empty($search_Service_Id))
                $carrierZonesFilter->addFieldFilter('cz.service_id', $search_Service_Id);
            if (!empty($search_Name))
                $carrierZonesFilter->addFieldLikeFilter('name', $search_Name);
            
            $carrierZonesFilter->AddOrderBy("cz.sort_order");
            $res = $carrierZonesFilter->getList('cz.id,cz.carrier_id,cz.service_id,c.carrier AS carrier_name,c.logo AS carrier_logo,cz.name,cz.status,cz.sort_order');
            if (count($res) > 0) {
                $output["status"] = "success";
                echo json_encode($output);
                die;
            }  else {
                $output["status"] = "error";
                echo json_encode($output);
                die;
            }
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_country_csv') {
            $carrierZone = new CarrierZones($this->form_vars['carrier_zone_id_country']);
            $carrierZoneName = $carrierZone->getName();
            $zoneCountriesObj = CarrierZonesCountries::getZoneCountries($carrierZone->getId());
            $csvContent = "Zone Name,Country ISO,Country Name\r\n";
            if(count($zoneCountriesObj) > 0){
                foreach($zoneCountriesObj as $zoneCountry){
                    $csvContent .= $carrierZoneName.",".$zoneCountry->getCountryIso().",".$zoneCountry->getCountryName()."\r\n";
                }
            }
            $fileName = $carrierZoneName."-countries";
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $csvContent;
            exit;
        }        
    }

    function multiexplode ($delimiters,$data) {
        $MakeReady = str_replace($delimiters, $delimiters[0], $data);
        $Return    = explode($delimiters[0], $MakeReady);
        return  $Return;
    }
    
    public function uploadZoneCountryCsv($formData, $csv_file) {
        //Get Zone id form ajax req
        $carrierZone = [];
        $carrierZoneBase = 1;
        if($importType == "single") {
            $zoneId = (int) trim($formData['zone_id']);
            $carrierZone = new CarrierZones($zoneId);
            $carrierId = $carrierZone->getCarrierId();
            $serviceId = $carrierZone->getServiceId();
        }
        $zoneCountries = [];
        $multiZoneCountries = [];
        $output = [];
        $relPath = "";
        if (!empty($csv_file['name'])) {
            $file_name = $csv_file['name'];
            $path_parts = pathinfo($file_name);
            $ext = strtolower($path_parts['extension']);
            $basename = $path_parts['basename'];
            if ($ext == 'csv') {
                $user = SessionManager::getUser();
                $account = $user->getId();
                $new_file_name = $account . "_" . time() . "_" . $basename;
                $relPath = '../_assets/carrierzones_csv/'.$new_file_name;
                if (!file_exists("../_assets/carrierzones_csv/"))
                    @mkdir("../_assets/carrierzones_csv/", 0775);
                if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                    $row = 1;
                    if (($handle = fopen($relPath, "r")) !== FALSE) {
                        $csvContent = '';
                        $successRecords = 0;
                        $errorRecords = 0;
                        $zones = [];
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if($row < 2){
                                $row++;
                                continue;
                            }
                            $zoneName = $data[0];
                            $countryIso = $data[1];
                            $countryName = $data[2];
                            $countryFilter = new CountryFilter();
                            $countryFilter->addIsoFilter($countryIso);
                            $countryResObj = $countryFilter->getColumnList('id');
                            if($zoneName != $carrierZone->getName()){
                                $output['message'] = 'Invalid Zone. "'.$zoneName.'"';
                                $output['status'] = 'fail';
                                return $output;
                                exit;
                            }
                            if(count($countryResObj) > 0){
                                $zoneCountries[] = $countryResObj[0]->getId();
                            }
                            
                            $row++;
                            $i++;
                        } 
                        $errorArr = $this->saveCarrierZone($zoneCountries, $carrierId, $serviceId, $zoneId);
                        if(count($errorArr)) {
                            $message = 'Uploaded error';
                            $message .= implode('<br />', $errorArr);
                            $output['status'] = 'error';
                            $output['message'] = $message;
                        } else {
                            $message = 'Uploaded successfully';
                            $output['status'] = 'success';
                            $output['message'] = $message;
                        }
                        unlink($relPath);
                        fclose($handle);
                    }
                } else {
                    $output['message'] = 'File upload fail.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = 'Invalid CSV File.';
                $output['status'] = 'fail';
            }
        } else {
            $output['message'] = 'No file found to import data.';
            $output['status'] = 'fail';
        }
        return $output;
    }
    
    function saveCarrierZone($zoneCountries,$carrierId, $serviceId, $zoneId) {
        $errors = [];
        $zoneCountries = array_unique($zoneCountries);
        //Get unique country from zone id
        $errorCountryArr = CarrierZonesCountries::validateCarrierZonesCountries($zoneCountries, $carrierId, $serviceId, $zoneId);

        if(empty($errorCountryArr)){
            CarrierZonesCountries::deleteCountriesByZoneId($zoneId);
            foreach($zoneCountries as $key => $zoneCountryId){
                $carrierZonesCountries = new CarrierZonesCountries();
                $carrierZonesCountries->setCarrierZoneId($zoneId);
                $carrierZonesCountries->setCountryId($zoneCountryId);
                $carrierZonesCountries->save();
            }            
        } else{
            foreach ($errorCountryArr as $CountryName => $ZoneName) {
                $errors[] = $CountryName . " already exists in ".$ZoneName;
            }
        }
        return $errors;
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
    <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
    <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>

        <script type="text/javascript">
        var grid = null;
        var DataTableFun = function () {
            var handleDataTable = function () {
                grid = new Datatable();
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
                            "url": "carrier_zones.php?action=carrier_zones_ajax", // ajax source
                            headers: {

                            },
                        },
                        "bStateSave": true,
                        "columns": [
                            {"data": "actions", "bSortable": false},
                            {"data": "name"},
                            {"data": "carrier_id"},
                            {"data": "service_id"}
                        ]
                    }
                });
            }
            return {
                //main function to initiate the module
                init: function () {
                    handleDataTable();
                }
            };
        }();
        function loadCarrierServices(carrierId, serviceId, csv = 0){
            $.ajax({
                type: "POST",
                url: "carrier_zones.php",
                data: {func: "get_carrier_services", carrier_id: carrierId, service_id:serviceId, csv_check:csv },
                dataType: "json",
                success: function (data) {
                    if(csv > 0) {
                        $("#csv_services_span").html("");
                        $("#csv_services_span").html(data.services);
                        $("#csv_service_id").select2();
                    } else {
                        $("#services_span").html(data.services);
                        $("#service_id").select2();
                    }
                    if(data.zone_type == 'country') {
                        $('#zone_postcode_box').hide();
                        $('#zone_countries_box').show();
                    } else if(data.zone_type == 'postcode') {
                        $('#zone_countries_box').hide();
                        $('#zone_postcode_box').show();
                    }
                },
                error: function () {
                    alert('error handing here');
                }
            });
        }
        $(document).ready(function () {
            
            $('.multiselect_drop_down').multiSelect({
                selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                afterInit: function(ms){
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function(e){
                            if (e.which === 40){
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function(e){
                            if (e.which == 40){
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                },
                afterSelect: function(values){
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function(values){
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });
            $('#carrier_id').change(function(){
                var zoneBase = $("#carrier_id option:selected").data('zone_base');
                var zoneType = $("#carrier_id option:selected").data('zone_type');
                var carrierId = $(this).val();
                var serviceId = '';
                if(zoneBase == 1){
                    var data = '<select name="service_id" id="service_id" class="form-control select2" disabled="disabled">';
                        data += '   <option value="">Select Service</option>';
                        data += '</select>';
                    $("#services_span").html(data);
                    $("#service_id").select2();
                    if(zoneType == 'country') {
                        $('#zone_postcode_box').hide();
                        $('#zone_countries_box').show();
                    } else if(zoneType == 'postcode') {
                        $('#zone_countries_box').hide();
                        $('#zone_postcode_box').show();
                    }
                    $('#zone_type').val(zoneType);
                }else{
                    loadCarrierServices(carrierId, serviceId);
                }
            });
            $('body').on('change', '#service_id', function() {
                var zoneType = $("#service_id option:selected").data('zone_type');
                $('#zone_type').val(zoneType);
                if(zoneType == 'country') {
                    $('#zone_postcode_box').hide();
                    $('#zone_countries_box').show();
                } else if(zoneType == 'postcode') {
                    $('#zone_countries_box').hide();
                    $('#zone_postcode_box').show();
                }
            });
            $('#csv_carrier_id').change(function(){
                var zoneBase = $("#csv_carrier_id option:selected").data('zone_base');
                var carrierId = $(this).val();
                var serviceId = '';
                if(zoneBase == 1){
                    var data = '<select name="csv_service_id" id="csv_service_id" class="form-control select2" data-live-search="true" disabled="disabled">';
                        data += '   <option value="">Select Service</option>';
                        data += '</select>';
                    $("#csv_services_span").html(data);
                    $("#csv_service_id").select2();
                }else{
                    var csvCheck = 1;
                    loadCarrierServices(carrierId, serviceId, csvCheck);
                }
            });
            DataTableFun.init();
            $("#csv_download_btn").click(function () {
                var searchName = $("#search_Name").val();
                var searchCarrierId = $("#search_carrier_id").val();
                var searchServiceId = $("#search_service_id").val();
                $("#name_download").val($("#search_Name").val());
                $("#carrier_id_download").val($("#search_carrier_id").val());
                $("#service_id_download").val($("#search_service_id").val());
                $("#message_download_csv").hide();
                   $.ajax({
                    type: "POST",
                    url: "carrier_zones.php",
                    data: {action: "check_download_carrierzones",search_name: searchName,search_carrier_id:searchCarrierId,search_service_id:searchServiceId},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == "success") {
                            $("#hiddenForm").submit();
                            $('#csv_download').modal('hide');
                        } else if (data.status == "error")  {
                            $("#message_download_csv div.alert").html("There is no record found to download");
                            $("#message_download_csv").show();
                            setTimeout(function(){
                            $('#message_download_csv').html('');
                            }, 4000);
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            });
            $("#btnSubmitImport").click(function () {
                $('#upload_carrier_zone_console_window').hide();
                $('#upload_carrier_zone_console_window').html('');
                $('#upload_carrier_zone_remove_btn').click();
                $("#csv_upload").modal('show');
                $('#csv_carrier_id').val("");
                $("#csv_carrier_id").selectpicker('refresh');
                $("#csv_service_id").html('<option value="">Select Service</option>');
                $("#csv_service_id").select2();
            });
            
            $("#upload_csv_btn").click(function () {
                $('#upload_carrier_zone_console_window').html("Uploading CSV File....<br />");
                var file_data = $('#file_in').prop('files')[0];
               // $('#csv_upload').modal('hide');
                var form_data = new FormData();
                var CsvCarrierId = "";
                CsvCarrierId = $("#csv_carrier_id").val();
                var CsvServiceId = "";
                CsvServiceId = $("#csv_service_id").val();
                form_data.append('csv_file', file_data);
                form_data.append('func', 'upload_csv_file');
                form_data.append('csv_carrier_id', CsvCarrierId);
                form_data.append('csv_service_id', CsvServiceId);
                $.ajax({
                    url: "carrier_zones.php",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if (response.status == 'success') {
                            grid.getDataTable().ajax.reload();
                            $('#upload_carrier_zone_console_window').append(response.message);
                            $('#csv_carrier_id').val("");
                            $("#csv_carrier_id").selectpicker('refresh');
                            $('#upload_carrier_zone_console_window').show();
                           
                        } else {
                            $('#upload_carrier_zone_console_window').append(response.message);
                            $('#upload_carrier_zone_console_window').show();
                        }
                    }
                });
                return false;
            });
            $("#upload_csv_country").click(function () {
                $('#console_window_country').show();
                $('#console_window_country').html('');
                $('#console_window_country').html("Uploading CSV File....<br />");
                var file_data = $('#file_in_country').prop('files')[0];
                var form_data = new FormData();
                var zone_id = '';
                zone_id = $('#carrier_zone_id_country').val();
                form_data.append('csv_file_country', file_data);
                form_data.append('func', 'upload_csv_file_country');
                form_data.append('zone_id', zone_id);
                $.ajax({
                    url: "carrier_zones.php",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#console_window_country').append(response.message);
                        } else {
                            $('#console_window_country').append(response.message);
                        }
                    }
                });
                return false;
            });
            $(document).on('click', '#country_download_btn', function () {
                $('#country_csv_download_form').submit();
            });
            $(document).on('click', '#csv_download_zone_country_btn', function () {
                $('#country_csv_download_form').submit();
            });
            $("#btnCountrySubmitImport").click(function(){
                $("#console_window_country").html(" ");
                $("#console_window_country").hide();
                $("#remove_btn_country").click();                
                $("#csv_upload_country").modal("show");
            });
             <?php 
            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) { ?>
                $('#manage-data-table button.filter-submit').click();
                <?php 
                if(isset($_GET['service_id']) && !empty($_GET['service_id'])) { ?>
                    loadCarrierServices('<?php echo $_GET['carrier_id'];?>','<?php echo $_GET['service_id'];?>');
                    $('#manage-data-table button.filter-submit').click();
            <?php
                } 
            }?>
            $('#search_carrier_id').trigger('change');
        });
        $('#btnSave').click(function () {
            $.post("carrier_zones.php", $("#carrier_zone_add_frm").serialize(), function (response) {
                $("#res_message div.alert").removeClass('alert-success');
                $("#res_message div.alert").removeClass('alert-danger');
                if (response.STATUS == "success") {
                    $("#res_message div.alert").addClass('alert-success');
                    $("#res_message div.alert").html(response.MESSAGE);
                    $("#res_message").show();
                    grid.getDataTable().ajax.reload();
                    $('#carrier_id').val("");
                    $("#carrier_id").selectpicker('refresh');
                    $("#service_id").html('<option value="">Select Service</option>');
                    $("#service_id").select2();
                    $("#name").val("");
                    $("#sort_order").val("");
                    $('#zone_countries').val("");
                    $('#zone_countries').multiSelect('refresh');
                    $('#zone_countries').multiSelect('select', []);
                } else {
                    $("#res_message div.alert").addClass('alert-danger');
                    $("#res_message div.alert").html(response.MESSAGE);
                    $("#res_message").show();
                }
            }, "json");

        });
        $(document).on('click', '.btndelete', function () {
            var zoneId = $(this).attr("data-zone_id");
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
                function(isConfirm) {
                    if (isConfirm) {
                        $("#res_message div.alert").removeClass('alert-success');
                        $("#res_message div.alert").removeClass('alert-danger');
                        $.ajax({
                            type: "POST",
                            url: "carrier_zones.php",
                            data: {action: "delete", carrier_zone_id: zoneId},
                            dataType: "json",
                            success: function (data) {
                                if (data.STATUS == "success") {
                                    $("#res_message div.alert").addClass('alert-success');
                                    $("#res_message div.alert").html("<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>");
                                    $("#res_message").show();
                                    $(".scroll-to-top").click();
                                    grid.getDataTable().ajax.reload();
                                } else {
                                }
                            },
                            error: function () {
                                //alert('error handing here');
                            }
                        });
                    }
                });
        });
        $(document).on('click', '.btnedit', function () {
            var zoneId = $(this).attr("data-zone_id");
            $.ajax({
                type: "POST",
                url: "carrier_zones.php",
                data: {action: "edit", zone_id: zoneId},
                dataType: "json",
                success: function (data) {
                    if (data.STATUS == "success") {
                        $("#carrier_zone_id").val(zoneId);
                        $("#carrier_id").val(data.carrier_id);
                        $("#carrier_id").selectpicker('refresh');
                        loadCarrierServices(data.carrier_id, data.service_id);
                        $("#name").val(data.name);
                        $("#sort_order").val(data.sort_order);
                        $("#postcode").val(data.zone_postcodes);
                        $('#zone_countries').multiSelect("deselect_all").multiSelect('select', data.zone_countries);
                        $(".scroll-to-top").click();
                        $('#carrier_id').trigger('change');
                        $('#carrier_id').trigger('service_id');
                    } else {
                    }
                },
                error: function () {
                    alert('error handing here');
                }
            });
        });
        $(document).on('click', '.carrier_zone_countries', function () {
            var zoneId =  $(this).data("carrier_zone_id");
            var zoneName =  $(this).data("carrier_zone_name");
            $('#carrier_zone_id_country').val(zoneId);
            $("#zone_countries_model .modal-title").html(zoneName+" Countries");
            $("#zone_countries_model .modal-body").html("");
            $.ajax({
                url: 'carrier_zones.php',
                type: 'POST',
                dataType: "html",
                data: {action: 'get_countries_list',carrier_zone_id:zoneId},
                success: function(data){
                    $("#zone_countries_model .modal-body").html(data);
                    $("#zone_countries_model").modal("show");
                },
                error: function(xhr,status,error) {
                }
            });
        });
        // Apply default drop down option after user press the cancel button
        $('#manage-data-table button.filter-cancel').click(function () {
            $("#search_service_id").val("");
            $("#search_carrier_id").val("");
            $("#search_carrier_id").selectpicker('refresh');
            $("#search_service_id").selectpicker('refresh');
        });
        $(document).on('change', '#search_carrier_id', function () {
            var carrierId = $(this).val();
            var serviceId = '';
            var csv = 2;
            $.ajax({
                type: "POST",
                url: "carrier_zones.php",
                data: {func: "get_carrier_services", carrier_id: carrierId, service_id:serviceId, csv_check:csv },
                dataType: "json",
                success: function (data) {
                    $("#search_service_id_span").html("");
                    $("#search_service_id_span").html(data.services);
                    $("#search_service_id").select2();
                },
                error: function () {
                    alert('error handing here');
                }
            });
        });
        </script>
    <?php
    }
    /*     * *
     * Content View
     */
    protected function renderBody() {
        ?>
        <form name="carrier_zone_add_frm" id="carrier_zone_add_frm" action="" method="POST" onsubmit="return false;">
            <input type="hidden" name="zone_type" id="zone_type" />
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-dropbox"></i>
                        Add/Update Carrier Zone
                    </div>
                    <div class="actions">
                        <?php if($this->sessionUser->getUserType() == User::USER_TYPE_ADMIN) { ?>
                            <?php if (Permissions::checkFilePermission('carrier.php')) { ?>
                            <a href="carrier.php" class="btn btn-sm blue"><span></span><i class="fa fa-dropbox"></i>&nbsp; Carrier Listing</a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row display-none" id="res_message">
                        <div class="col-md-12">
                            <div class="alert alert-success"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                   
                                     <div class="has-float-label right">  
                                        <?php
                                        $carrierId = '';
                                        if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])){
                                            $carrierId = $_GET['carrier_id'];
                                        }
                                        echo Ddl::generateCarrierDDLWithImage('carrier_id', $carrierId, 'id', ' class="bs-select  form-control form-filter" data-live-search="true"  data-show-subtext="true"');
                                        ?>
                                     <label>Carrier Name</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                
                                 <div class="has-float-label right">  
                                    <span id="services_span">
                                        <select name="service_id" id="service_id" class="form-control select2" disabled="disabled">
                                            <option value="">Select Service</option>
                                        </select>
                                    </span>
                                  <label>Carrier Service</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                          
                                
                               <div class="has-float-label input-icon right">   <i class="fa fa-globe"></i>
                                   
                                    <input type="text" name="name" id="name" class="form-control" title="Enter Zone Name" placeholder="Enter Zone Name" />
                                <label for="name">Zone Name</label>
                          
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                           
                              
                                <div class="has-float-label input-icon right"><i class="fa fa-sort-amount-asc"></i>
                                
                                    <input type="text" name="sort_order" id="sort_order" class="form-control"  title="Enter Sort Order" placeholder="Sort Order" />
                                  <label for="sort_order">Sort Order</label>
                              </span>
                                </div>
                          
                        </div>
                    </div>
                    <div class="row" id="zone_countries_box">
                        <div class="col-md-12 full-width-multiselect">
                            <label>Zone Countries</label>
                            <?php
                            $selected_countries = array();
                            echo Ddl::generateDDL('zone_countries[]', 'CountryFilter', array('active' => '1', 'deletedq' => 'N'), 'name', 'id', $selected_countries, 'class="multi-select multiselect_drop_down" multiple="multiple"', '', '', 'zone_countries');
                            ?>
                        </div>
                    </div>
                    <div class="row" id="zone_postcode_box">
                        <div class="col-md-12">
                            <label>Zone Postcodes </label>
                            <textarea class="form-control" name="postcode" style="height: 150px;" id="postcode" ></textarea>
                            <p class="help-block">Enter comma separated or perline postcode e.g 12345,123456,45678</p>
                        </div>
                    </div>
                    <div class="row margin-top-20">
                        <div class="col-md-12 text-center">
                            <a id="btnSave"   href="javascript:;" class="btn btn-primary btn_save"><span></span>Save</a>
                            <a href="carrier_zones.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="carrier_zone_id" id="carrier_zone_id" value="" />
            <input type="hidden" name="func" value="save_carrier_zone" />
        </form>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Carrier Zones List
                </div>
               <div class="actions">
                    <?php if (Permissions::checkFilePermission('carrier_zone_download')) { ?>
                        <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i> &nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>
                    <?php } ?>    
                    <?php if (Permissions::checkFilePermission('carrier_zone_import')) { ?>
                        <a id="btnSubmitImport" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i> &nbsp;<?php echo Translation::GetCaption("IMPORT"); ?></a>
                    <?php } ?>    
                    <?php if (Permissions::checkFilePermission('carrier_zone_country_template_download')) { ?>
                        <a id="csv_download_zone_country_btn" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i> &nbsp;<?php echo Translation::GetCaption("Zone Country Template"); ?></a>
                    <?php } ?>    
                </div>
            </div>
            <div class="row" id="message_download_csv" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-danger"></div>
                                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                    <tr role="row" class="heading">
                        <th>Actions</th>
                        <th>Name</th>
                        <th>Carrier</th>
                        <th>Service</th>
                    </tr>
                    <tr role="row" class="filter">
                        <td>
                            <div class="margin-bottom-5">
                                <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control form-filter input-xs" name="name" id ="search_Name" />
                        </td>
                        <td  class="user_acccount_correct_button">
                            <?php 
                            $carrierId = '';
                            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])){
                                $carrierId = $_GET['carrier_id'];
                            }
                            echo Ddl::generateCarrierDDLWithImage('search_carrier_id', $carrierId, 'id', ' class="bs-select form-control form-filter" data-container="body" data-live-search="true"  data-show-subtext="true"');
                            ?>
                        </td>
                        <td  class="user_acccount_correct_button">
                            <?php 
                            $servicerId = '';
                            if(isset($_GET['service_id']) && !empty($_GET['service_id'])){
                                $servicerId = $_GET['service_id'];
                            }
                            ?>
                            <span id="search_service_id_span">
                                <?php
                                echo Ddl::generateServiceDDLWithImage('search_service_id', $servicerId, 'id', ' class="bs-select input-sm form-control form-filter" data-container="body" data-live-search="true"  data-show-subtext="true"', '', '', 'name');
                                //echo Ddl::generateServiceDDLWithImage('service_id', $selected_value, 'id', ' class="bs-select input-sm form-control form-filter" required="" data-live-search="true" data-show-subtext="true" ', '', '', 'name', 'Select Services');
                                ?>
                            </span>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <!--Model for Zone Countries-->
        <div class="modal fade bs-modal-lg" id="zone_countries_model" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="users_weight_limit_model">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Zone Countries</h4>
                        <div class="text-right">
                            <input type="file" name="import_country" id="import_country"  style="display: none;"/>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div  id="console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a id="country_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>
                        <a id="btnCountrySubmitImport" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i>&nbsp;<?php echo Translation::GetCaption("IMPORT"); ?></a>
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Model for CSV download-->
        <div class="modal fade" id="csv_download" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Select Carrier</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="message_download_csv" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-danger"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <?php echo Ddl::generateCarrierDDLWithImage('carrier_csv', '', 'id', ' class="bs-select input-sm form-control form-filter "  data-live-search="true"  data-show-subtext="true"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="download_csv" class="btn green">Download</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Model for CSV Upload-->
        <div class="modal fade" id="csv_upload" role="basic" aria-hidden="true" style="overflow:hidden">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Import Carrier CSV</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="message_import_csv" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-danger"></div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div  id="upload_carrier_zone_console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;">

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="first_form_col">
                                    <div class="form-group">
                                        <label>Carrier Name</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <?php echo Ddl::generateCarrierDDLWithImage('csv_carrier_id', '', 'id', ' class="bs-select input-sm form-control form-filter" data-live-search="true"  data-show-subtext="true"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Carrier Service</label>
                                    <div class="input-group input-group-sm ">
                                        <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <span id="csv_services_span">
                                            <select name="csv_service_id" id="csv_service_id" class="form-control select2" disabled="disabled">
                                                <option value="">Select Service</option>
                                            </select>
                                        </span>
                                    </div>
                                </div>
                            </div>   
                    </div>
                         <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="form-group">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="file_in" id="file_in">
                                                </span>
                                                <a id="upload_carrier_zone_remove_btn" href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id ="close_csv" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <a id="btntemplate" href="../csv/template_carrier_zone.csv" class="btn btn-primary btn_save margin-right-10" data-original-title="" title=""><span></span>Download Template</a>
                        <button type="button" id="upload_csv_btn" class="btn green">Upload</button>
                        
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="carrier_id_hidden" value="" id="carrier_id_hidden"/>
                <input type="hidden" id="name_download" name="name_download" value="" />
                <input type="hidden" id="carrier_id_download" name="carrier_id_download" value="" />
                <input type="hidden" id="service_id_download" name="service_id_download" value="" />
                <input type="hidden" name="action" value="download" />
            </form>
            <form id="country_csv_download_form" name="country_csv_download_form" method="post" >
                <input type="hidden" name="action" value="download_country_csv" />
                <input type="hidden" name="carrier_zone_id_country" id="carrier_zone_id_country" value="" />
            </form>
        </div>
         <!--Model for CSV upload country-->
         <div class="modal fade" id="csv_upload_country" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Import Country CSV</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div  id="console_window_country" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;">

                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="form-group">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="file_in_country" id="file_in_country">
                                                </span>
                                                <a id="remove_btn_country" href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id ="close_csv_country" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv_country" class="btn green">Upload</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    <?php
    }
    
    public function renderHead() {
        ?>
        <style type="text/css">
            #zone_postcode_box, #zone_countries_box {
                display: none;
            }
        </style>
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