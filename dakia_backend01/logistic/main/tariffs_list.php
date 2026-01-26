<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'PHPExcel'
], '3rdparty/phpexcel');

require_once("../includes/library/vendor/autoload.php");

include_classes([
    'carrier.class',
    'carrierfilter.class',
    'currency.class',
    'currencyfilter.class',
    'carrierzones.class',
    'carrierzonesfilter.class',
    'services.class' ,
    'servicefilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'tariffsaccountmapping.class',
    'tariffsaccountmappingfilter.class',
    'tariffsdetails.class',
    'tariffsdetailsfilter.class',
    'remoteareachargestariffs.class',
    'remoteareachargestariffsfilter.class',
    'remoteareasgroups.class',
    'remoteareasgroupsfilter.class',
    'comparepricing.class',
    'comparepricingfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'tariffadditionalchargesfilter.class',
    'tariffadditionalcharges.class',
]);

class Page extends BasePage {
    /* * *
     * Controller logic
     */

    private $filerColumn = '*';
    private $params = "";
    private $user = "";
    private $user_filter = [];

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Tarrif List"
        );
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }
        //        ini_set('display_errors', 1);
        //        ini_set('display_startup_errors', 1);
        //        error_reporting(E_ALL);
        /*
         * DataTable handlings
         */
        if((isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) && (isset($_GET['service_id']) && !empty($_GET['service_id']))) {
            $this->params = "carrier_id=" . (int) $_GET['carrier_id'] . "&service_id=" . $_GET['service_id'];
        } else {
            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                $this->params = "carrier_id=" . (int) $_GET['carrier_id'];
            }
            if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                $this->params = "service_id=" . $_GET['service_id'];
            }
        }
        if (isset($_GET['action']) && $_GET['action'] == "tariffs_ajax") {
            $tariffsFilter = new TariffsFilter();
            //if ($this->user->getUserType() != User::USER_TYPE_ADMIN)
            //{
                $tariffsFilter->addFieldFilter('   t.user_account_id', $this->user->getUserAccountId());
                //$tariffsFilter->addFieldFilter('   t.status', 1);
            //}
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->user_filter = [
                    'carrier_id'=>$this->form_vars['carrier_id'],
                    'service_id'=>$this->form_vars['service_id'],
                    'name'=>$this->form_vars['name'],
                    'start_date'=>$this->form_vars['start_date'],
                    'end_date'=>$this->form_vars['end_date'],
                    'from_zone'=>$this->form_vars['from_zone'],
                    'status'=>$this->form_vars['status'],
                    'type'=>$this->form_vars['type']
                ];
                SessionManager::saveAdditionalChargesFilter($this->user_filter);
                $carrierId = $this->form_vars['carrier_id'];
                if (!empty($carrierId))
                    $tariffsFilter->addFieldFilter('   t.carrier_id', $carrierId);

                $serviceId = $this->form_vars['service_id'];
                if (!empty($serviceId))
                    $tariffsFilter->addFieldFilter('  t.service_id', $serviceId);

                $tariffName = $this->form_vars['name'];
                if (!empty($tariffName))
                    $tariffsFilter->addFieldLikeFilter('  t.name', $tariffName);

                $startDate = $this->form_vars['start_date'];
                if (!empty($startDate))
                    $tariffsFilter->addStartDateFilter($startDate);

                $endDate = $this->form_vars['end_date'];
                if (!empty($endDate))
                    $tariffsFilter->addEndDateFilter($endDate);

                $fromZone = $this->form_vars['from_zone'];
                if (!empty($fromZone)) {
                    $tariffsFilter->addTariffsDetailsJoin();
                    $tariffsFilter->addFieldFilter('    td.from_zone_id', $fromZone);
                    $this->filerColumn = 'DISTINCT t.*, td.`from_zone_id`';
                }

                $isActive = $this->form_vars['status'];
                if ($isActive == 1) {
                    $tariffsFilter->addFieldFilter('   t.status', $isActive);
                }
                if ($isActive == '0') {
                    $tariffsFilter->addFieldFilter('   t.status', $isActive);
                }
                $isType = $this->form_vars['type'];
                if (!empty($isType)) {
                    $tariffsFilter->addFieldFilter('   t.tariff_type', $isType);
                }

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
                //echo $functionName; die;
                $tariffsFilter->AddOrderBy(strtolower("t." . $dataTableColumnName), $orderFalse);
            } else {
                $tariffsFilter->AddOrderBy(strtolower("t.id"), false);
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iTotalRecords = $tariffsFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $tariffsFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $tariffsFilter->setOffset($iDisplayStart);
            $tariffsObjs = $tariffsFilter->getPagingList($this->filerColumn,false);
            $setDataArr = array();
            foreach ($tariffsObjs as $tariffsObj) {
                //Get Carrier details from carrier class
                $carrierFilter = new CarrierFilter($tariffsObj->getCarrierId());
                $carrierFilter->addIdFilter($tariffsObj->getCarrierId());
                $carrierDeials = $carrierFilter->getColumnList('id,logo,carrier');
                $carrierLogo = "";
                $carrierId = "";
                $carrierName = "";
                if (count($carrierDeials) > 0) {
                    $carrierLogo = $carrierDeials[0]->getLogo();
                    $carrierId = $carrierDeials[0]->getId();
                    $carrierName = $carrierDeials[0]->getCarrier();
                }
                //Get Carrier service details from service class
                $service = new Services($tariffsObj->getServiceId());
                $carrierObj = new Carrier($service->getCarrierId());
                //Get Currency from currency class
                $currency = new Currency($service->getCarrierId());
                //Get Tariff Zones from mapping table
                $tariffsDetailsFilter = new TariffsDetailsFilter();
                $tariffsDetailsFilter->addFieldFilter('   tariffs_id', $tariffsObj->getId());
                $tariffsDetailsFilter->setLimit('1');
                $tariffsDetailsObj = $tariffsDetailsFilter->getList();
                $fromZoneId = '';
                $toZoneId = '';
                if (count($tariffsDetailsObj) > 0) {
                    $fromZoneId = $tariffsDetailsObj[0]->getFromZoneId();
                    $toZoneId = $tariffsDetailsObj[0]->getToZoneId();
                }
                $fromZoneObj = new CarrierZones($fromZoneId);
                $toZoneObj = new CarrierZones($toZoneId);
                // set type of tariff
                if ($tariffsObj->getTariffType() == "customer") {
                    $typeOfTariff = '<div class="text-center"><span class="label label-sm label-success">Customer</span></div>';
                } else if ($tariffsObj->getTariffType() == "supplier") {
                    $typeOfTariff = '<div class="text-center"><span class="label label-sm label-info">Supplier</span></div>';
                } else {
                    $typeOfTariff = '<div class="text-center"><span class="label label-sm label-primary">Agent</span></div>';
                }
                // set status of tariff
                if ($tariffsObj->getStatus() == 1) {
                    $statusOfTariff = '<div class="text-center"><span class="label label-sm label-success">Yes</span></div>';
                } else {
                    $statusOfTariff = '<div class="text-center"><span class="label label-sm label-danger">No</span></div>';
                }
                $tariffZoneType = $service->getTariffType();
                $startDate = formatDate(date("d-m-Y", strtotime($tariffsObj->getStartDate())));
                $endDate = formatDate(date("d-m-Y", strtotime($tariffsObj->getEndDate())));
                $currentArr = array();
                $currentArr['carrier_id'] = '<img src="../images/carrierlogo/thumbnail/owe_16_' . $carrierLogo . '" alt="" /> ' . $carrierName;
                $currentArr['service_id'] = $service->getName();
                $currentArr['name'] = $tariffsObj->getName();
                $currentArr['currency'] = $currency->getCurrencyname();
                $currentArr['start_date'] = $startDate;
                $currentArr['end_date'] = $endDate;
                $currentArr['from_zone'] = $fromZoneObj->getName();
                $currentArr['tariff_type'] = $typeOfTariff;
                $currentArr['status'] = $statusOfTariff;
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                if(Permissions::checkFilePermission('tariff_edit_permission')) {
                    if(!empty($this->params)) {
                        $currentArr['actions'] .=       '<li>
                                                        <a title="Edit" href="tariff_add.php?id=' . $tariffsObj->getId() . '&' . $this->params . '">
                                                            <span class="glyphicon glyphicon-pencil"></span> Edit
                                                        </a>
                                                    </li>';
                    } else {
                        $currentArr['actions'] .=       '<li>
                                                        <a title="Edit" href="tariff_add.php?id=' . $tariffsObj->getId() . '">
                                                            <span class="glyphicon glyphicon-pencil"></span> Edit
                                                        </a>
                                                    </li>';
                    }
                }
                if(Permissions::checkFilePermission('tariff_delete_permission')) {
                    $currentArr['actions'] .=        '<li>
                                                    <a href="JavaScript:void(0);" data-tariff_id="' . $tariffsObj->getId() . '" class="btndelete" title="Delete">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </li>';
                }
                if($tariffsObj->getTariffType() == "supplier") {
                    if(!empty($this->params)) {
                        $currentArr['actions'] .= '<li>
                                                    <a href="tariffs_pricing.php?id=' . $tariffsObj->getId() . '&' . $this->params . '" data-tariff_id="' . $tariffsObj->getId() . '" title="Delete">
                                                        <i class="fa fa-money"></i> Pricing
                                                    </a>
                                                </li>';
                    } else {
                        $currentArr['actions'] .= '<li>
                                                    <a href="tariffs_pricing.php?id=' . $tariffsObj->getId() . '" data-tariff_id="' . $tariffsObj->getId() . '" title="Delete">
                                                        <i class="fa fa-money"></i> Pricing
                                                    </a>
                                                </li>';
                    }
                }
                $currentArr['actions'] .= '<li>
                                                    <a href="tariff_additional_charges.php?tariff_id=' . $tariffsObj->getId() . '" title="Tariff Additional pricing">
                                                        <i class="fa fa-money"></i> Tariff Additional Charges
                                                    </a>
                                                </li>';
                if($service->getIsCustomized() == 1) {
                    $currentArr['actions'] .= '<li>
                                                    <a href="javascript:;" title="Tariff Routing Download" data-tariff_id="'.$tariffsObj->getId() .'" data-product_id="'.$service->getId() .'" class="tariff_routing_download"  >
                                                        <i class="fa fa-download"></i> Tariff Routing download
                                                    </a>
                                                </li>';
                }
                if ( !empty($service->getRemotearea())) {
                    $serviceRemoteArea = $service->getRemotearea();
                } else {
                    $serviceRemoteArea ='ON_WEIGHT';
                }
                $funcRemoteArea =  "getRemoteAreaTariffCharges('".$tariffsObj->getId()."',"."'".$carrierObj->getId()."','".($serviceRemoteArea)."' )";
                $currentArr['actions'] .= '<li>
                                            <a data-toggle="modal" id="span_user_service_remotearea_'.$tariffsObj->getId().'" data-target="#model_remoterea_supplier" onclick="return '.$funcRemoteArea.';" title="Tariff Remoteareas Charges">
                                                <i class="fa fa-money"></i> Tariff Remoteareas Charges
                                            </a>
                                    </li>';
                if($tariffsObj->getTariffType() == "customer") {
                    $currentArr['actions'] .= '<li>'
                       // . "<a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $tariffsObj->getId() . "' data-log_name='tariffs' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>"
                                                    .'<a href="javascript:;" class="assign-tariff-account" id="assign-tariff-account-'.$tariffsObj->getId().'" data-target="assign_tariff_account_modal" data-toggle="modal" data-id="'.$tariffsObj->getId().'" data-start_date="'.$startDate.'" data-end_date="'.$endDate.'" data-description="'.$tariffsObj->getDescription().'" title="Assign Tariff">
                                                        <i class="fa fa-sign-in"></i> Assign Tariff
                                                    </a>
                                                </li>';
                }
                if($tariffsObj->getTariffType() != "supplier") {
                    $currentArr['actions'] .= '<li>
                                                    <a href="javascript:;" id="download_tariff_excel" data-id="'.$tariffsObj->getId().'" data-start_date="'.$startDate.'" data-end_date="'.$endDate.'" data-description="'.$tariffsObj->getDescription().'" title="Download Excel">
                                                        <i class="fa fa-download"></i> Download Excel
                                                    </a>
                                                </li>';
                }

                if($tariffsObj->getTariffFile() <> "")
                {
                $tariffFile ="../_assets/tariff_files/".$tariffsObj->getTariffFile();
                $currentArr['actions'] .= '<li>
                <a href="'.$tariffFile.'" title="Download Tariff" id="download_tariff" data-id="'.$tariffsObj->getId().'">
                <i class="fa fa-download"></i> Download Tariff</a></li>';
                }
                
                $currentArr['actions'] .= '<li>
                                                    <a href="javascript:;" title="Upload CSV" id="show_upload_csv_modal" data-id="'.$tariffsObj->getId().'" data-tariff_zone_type="'.$tariffZoneType.'" >
                                                        <i class="fa fa-download"></i> Upload CSV
                                                    </a>
                                                </li>';
                if($tariffZoneType == "single") {
                    $currentArr['actions'] .= '<li>
                                                    <a href="javascript:;" title="Compare Pricing" id="compare_pricing_modal" data-id="' . $tariffsObj->getId() . '" data-tariff_zone_type="' . $tariffZoneType . '"  onclick="getTariffsOption('.$tariffsObj->getId().'); ">
                                                        <i class="fa fa-download"></i> Compare Pricing
                                                    </a>
                                                </li>';
                }

                $currentArr['actions'] .= "<li>"
                    . "<a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $tariffsObj->getId() . "' data-log_name='tariffs' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>"
                    . "</li>";
                $currentArr['actions'] .= '</ul>
                                        </div>' ;
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == "user_account_for_tariff_list") {
            $tariffId = $this->form_vars['tariffId'];
            //tariffs_account_mapping
            $tariffAccountMapping = new TariffsAccountMappingFilter();
            $tariffAccountMapping->addFieldFilter('tariff_id', $tariffId);
            $tariffUATD = $tariffAccountMapping->getUserAccountTariffDistinctList(" tam.*, ua.user_account");
            $useraccountDetails = [];
            
            if(count($tariffUATD)>0){
                $useraccountDetails['status'] = 'success';
                $useraccountDetails['message'] = 'success';
                $useraccountDetails['data'] = [];
                foreach($tariffUATD as $keyUATD=>$valueUATD){
                    $useraccountDetails['data'][] = [
                                    'id'=>$valueUATD->getId(),
                                    'tariff_id'=>$valueUATD->getTariffId(),
                                    'user_account_id'=>$valueUATD->getUserAccountId(),
                                    'user_account'=>$valueUATD->getUserAccount()                                    
                                ];
                }
            } else {
                $useraccountDetails['status'] = 'error';
                $useraccountDetails['message'] = 'Tariff not assigned to any account.';
            }
            echo json_encode($useraccountDetails);
                    die;
        }else if (isset($this->form_vars['func']) && $this->form_vars['func'] == "user_account_for_tariff_add") {
            extract( $this->form_vars);
            if ($tariff_id > 0 && $user_account_id> 0) {
                $remoteareas = new TariffsAccountMapping();
                $remoteareas->setTariffId($tariff_id);
                $remoteareas->setUserAccountId($user_account_id);
                $remoteareas->save();
               
                $returnMsg['STATUS'] = "success";
                $returnMsg['MESSAGE'] = "You have successfully unassigned tariff from account.";
            } else {
                $returnMsg['STATUS'] = "error";
                $returnMsg['MESSAGE'] = "Please check tariff and account. We have not found any record.";
            }
            echo json_encode($returnMsg);
            die;
        }else if (isset($this->form_vars['action']) && $this->form_vars['action'] == "remove_assign_tariffs_from_account") {
            extract( $this->form_vars);
            if ($tariff_id > 0) {
                $remoteareas = new TariffsAccountMapping($tariff_id);
                $remoteareas->delete();
                $returnMsg['STATUS'] = "success";
                $returnMsg['MESSAGE'] = "You have successfully unassigned tariff from account.";
            } else {
                $returnMsg['STATUS'] = "error";
                $returnMsg['MESSAGE'] = "Please check tariff and account. We have not found any record.";
            }
            echo json_encode($returnMsg);
            die;
        }else if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $tariffId = $this->form_vars['tariff_id'];
            if ($tariffId > 0) {
                $remoteareas = new Tariffs($tariffId);
                $remoteareas->delete();
                $returnMsg['STATUS'] = "success";
                /*
                 * Add Remoteareas Log details
                 */
                //  $remoteareasGroupsLog = new TariffsLog();
                //  $newTariffsData = serialize($remoteareas);
                //  $remoteareasGroupsLog->createlog($this->user->getId(),'',$tariffId,'REMOTEAREAS_GROUPS',$this->user->getUserName() . ' has deleted ' . $tariffId,$oldTariffsData, $newTariffsData);
                echo json_encode($returnMsg);
            } else {
                $returnMsg['STATUS'] = "error";
                echo json_encode($returnMsg);
            }

            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_tariff_csv') {
            //Get Tariff Detail from mapping table
            $tariffsFilter = new TariffsFilter();
            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                $tariffsFilter->addFieldFilter(' t.carrier_id ',$_GET['carrier_id']);
            }
            if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                $tariffsFilter->addFieldFilter(' t.service_id ', $_GET['service_id']);
            }
            //if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
            $tariffsFilter->addFieldFilter('   t.user_account_id', $this->user->getUserAccountId());
            // $tariffsFilter->addFieldFilter('   t.status', 1);
            //}
            $tariffFilterCsvObj = $tariffsFilter->getList();
            if(!empty($tariffFilterCsvObj)) {
                $returnString = "Tariff Name,Carrier Name,Carrier Service,Start Date,End Date,";
                $fileName = "tariff_".time();
                foreach($tariffFilterCsvObj as $tariffObj) {
                    $returnString .= "\r\n";
                    $returnString .= cleanCsvCall($tariffObj->getName()) . ",";
                    $carrierFilter = new CarrierFilter($tariffObj->getCarrierId());
                    $carrierFilter->addIdFilter($tariffObj->getCarrierId());
                    $carrierDeials = $carrierFilter->getColumnList('carrier');
                    $carrierName = "";
                    if (count($carrierDeials) > 0) {
                        $carrierName = $carrierDeials[0]->getCarrier();
                    }
                    $returnString .= cleanCsvCall($carrierName) . ",";
                    //Get Carrier service details from service class
                    $service = new Services($tariffObj->getServiceId());
                    $returnString .= cleanCsvCall($service->getName()) . ",";
                    $returnString .= date("d-m-Y", strtotime($tariffObj->getStartDate())) . ",";
                    $returnString .= date("d-m-Y", strtotime($tariffObj->getEndDate())) . ",";
                }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
                header("Pragma: no-cache");
                header("Expires: 0");
                echo $returnString;
                exit();
            } else {
                echo "no record found";
                exit();
            }

        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $output = array();
            $tariffId = $this->form_vars['tariffId'];
            $tariffZoneType = $this->form_vars['tariffZoneType'];
            $tariff = new Tariffs($tariffId);
            $carrierId = $tariff->getCarrierId();
            $from_zone_id = '';
            $tariffDetailFilter = new TariffsDetailsFilter();
            $tariffDetailFilter->addFieldFilter('     td.tariffs_id', $tariffId);
            $tariffDetails = $tariffDetailFilter->getList();
            if(count($tariffDetails)) {
                $from_zone_id = $tariffDetails[0]->getFromZoneId();
            }
            $vaildsZoneIds = $this->getValidZoneFromTariffId($tariffId);
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getUserAccountId();
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
                            $toZoneIds = [];
                            $formula = 0;
                            if($tariffZoneType == "single") {
                                while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                    $lastKey = (count($data) - 1);
                                    if ($row < 2) {
                                        foreach ($data as $key => $value) {
                                            if ($key > 0) {
                                                if ($lastKey == $key && (trim(strtolower($value)) == "formula")) {
                                                    $formula = 1;
                                                }
                                                /* Get Zone Id From Name */
                                                if ($key > 0 && !$formula) {
                                                    $carrierObj = new Carrier($carrierId);
                                                    $carrierZoneBase = $carrierObj->getZoneBase();
                                                    $carrierZonesNameFilter = new CarrierZonesFilter();
                                                    $carrierZonesNameFilter->addFieldFilter('    TRIM(UPPER(cz.name))', trim(strtoupper($value)));
                                                    $carrierZonesNameFilter->addFieldFilter('    cz.carrier_id', $tariff->getCarrierId());
                                                    $carrierZonesNameFilter->addFieldFilter('    cz.status', 1);
                                                    if ($carrierZoneBase == 0) {
                                                        $carrierZonesNameFilter->addFieldFilter('    cz.service_id', $tariff->getServiceId());
                                                    }
                                                    $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                                                    if (count($zoneFilterObj) > 0) {
                                                        $toZoneIds[$key] = $zoneFilterObj[0]->getId();
                                                    } else {
                                                        $toZoneIds[$key] = '';
                                                    }
                                                }
                                            }
                                        }
                                        $row++;
                                        continue;
                                    }
                                    $to_zone_id = '';
                                    $weight_from = '';
                                    $weight_to = '';
                                    $csvWeightCost = '';
                                    $csvPeiceCost = '';
                                    $formulaValue = '';
                                    foreach ($data as $key => $value) {
                                        if ($formula) {
                                            $formulaValue = $data[$lastKey];
                                        }
                                        if ($key == 0) {
                                            $weight = explode('/', $value);
                                            $weight_from = $weight[0];
                                            $weight_to = $weight[1];
                                        }
                                        if ($key > 0) {
                                            $to_zone_id = $toZoneIds[$key];
                                            $zones = explode('/', $value);
                                            if (count($zones) > 0) {
                                                $csvWeightCost = $zones[0];
                                                $csvPeiceCost = (isset($zones[1]) ? $zones[1] : 0.00);
                                            }
                                            if (in_array($to_zone_id, $vaildsZoneIds) && $to_zone_id > 0) {
                                                $csvContent .= (!empty($csvContent) ? "\n" : '') . $tariffId . ',' . $from_zone_id . ',' . $to_zone_id . ',' . $weight_from . ',' . $weight_to . ',' . $csvWeightCost . ',' . $csvPeiceCost . ',' . $formulaValue;
                                                $successRecords++;
                                            } else {
                                                if ($lastKey == $key) {
                                                    if (!$formula) {
                                                        $errorRecords++;
                                                    }
                                                } else {
                                                    $errorRecords++;
                                                }
                                            }
                                        }
                                    }
                                    $row++;
                                }
                            } else {
                                $values = array();
                                while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                    $num = count($data);
                                    for ($c = 0; $c < $num; $c++) {
                                        if ($row == 1) {
                                            $headings[] = $data[$c];
                                        } else {
                                            $values[$row][] = $data[$c];
                                        }
                                    }
                                    $row++;
                                }
                                foreach ($values as $key => $value) {
                                    $lastKey = (count($data) - 1);
                                    $weight = explode('/', $value[0]);
                                    $w_from = '';
                                    $w_to = '';
                                    if (count($weight) > 0) {
                                        $w_from = $weight[0];
                                        $w_to = $weight[1];
                                    }

                                    $fromZoneName = trim($value[1]);
                                    $toZoneName = trim($value[2]);
                                    $weightCost = $value[3];
                                    $pieceCost = $value[4];
                                    $formula = $value[5];

                                    $zoneToId = "";
                                    $zoneFromId = "";

                                    $carrierObj = new Carrier($carrierId);
                                    $carrierZoneBase = $carrierObj->getZoneBase();

                                    /* Get Zone Id From Name */
                                    $carrierZonesNameFilter = new CarrierZonesFilter();
                                    $carrierZonesNameFilter->addIsDeletedFilter();
                                    $carrierZonesNameFilter->addFieldFilter('TRIM(UPPER(cz.name))', trim(strtoupper($fromZoneName)));
                                    $carrierZonesNameFilter->addFieldFilter('cz.status', 1);
                                    $carrierZonesNameFilter->addFieldFilter('    cz.carrier_id', $tariff->getCarrierId());
                                    if ($carrierZoneBase == 0) {
                                        $carrierZonesNameFilter->addFieldFilter('    cz.service_id', $tariff->getServiceId());
                                    }
                                    $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                                    if (count($zoneFilterObj) > 0) {
                                        $zoneFromId = $zoneFilterObj[0]->getId();
                                    }
                                    /* Get Zone Id From Name end */
                                    /* Get Zone Id From Name */
                                    $carrierZonesNameFilter = new CarrierZonesFilter();
                                    $carrierZonesNameFilter->addIsDeletedFilter();
                                    $carrierZonesNameFilter->addFieldFilter('TRIM(UPPER(cz.name))', trim(strtoupper($toZoneName)));
                                    $carrierZonesNameFilter->addFieldFilter('cz.status', 1);
                                    $carrierZonesNameFilter->addFieldFilter('    cz.carrier_id', $tariff->getCarrierId());
                                    if ($carrierZoneBase == 0) {
                                        $carrierZonesNameFilter->addFieldFilter('    cz.service_id', $tariff->getServiceId());
                                    }
                                    $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                                    if (count($zoneFilterObj) > 0) {
                                        $zoneToId = $zoneFilterObj[0]->getId();
                                    }
                                    $csvContent .= (!empty($csvContent) ? "\n" : '').$tariffId.','.$zoneFromId.','.$zoneToId.','.$w_from.','.$w_to.','.$weightCost.','.$pieceCost.','.$formula;
                                    $successRecords++;
                                }
                            }
                            //Write File
                            $message = '';
                            if(!empty($csvContent)){
                                TariffsDetails::deleteTarifsDetailByTarifId($tariffId);
                                $tariffDetailCSVFile = '../_assets/csv/tariff_detail_csv_'.time().".csv";
                                if(file_put_contents($tariffDetailCSVFile,$csvContent)){
                                    $tariffDetailSql = "LOAD DATA LOCAL INFILE '".$tariffDetailCSVFile."' INTO TABLE `tariffs_details` CHARACTER SET 'utf8' FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n' (
                                                          `tariffs_id`,
                                                          `from_zone_id`,
                                                          `to_zone_id`,
                                                          `weight_from`,
                                                          `weight_to`,
                                                          `weight_cost`,
                                                          `piece_cost`,
                                                          `formula`
                                                        );";
                                    DbAccess3::runQueryWithError($tariffDetailSql);
                                    $dbError = DbAccess3::$dbError;
                                    if(count($dbError) > 0) {
                                        $message .= 'Sorry tariff in not insert into table';
                                        $message .= implode("<br /> ", $dbError);
                                    } else {
                                        @unlink($tariffDetailCSVFile);
                                        $message .= 'Tariff is upload successfully';
                                        $message .= '<br /> ' . $successRecords . ' tariff zone imported successfully.';
                                        $message .= '<br /> ' . $errorRecords . ' tariff zone not found.';
                                    }
                                }
                            } else {
                                $message .= 'Tariff not upload successfully';
                                $message .= '<br /> ' . $successRecords . ' tariff zone imported successfully.';
                                $message .= '<br /> ' . $errorRecords . ' tariff zone not found.';
                            }
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

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'compare_csv_file_upload') {
            $output = [];
            $user = SessionManager::getUser();
            $account = $user->getUserAccountId();
            @$csv_files = $_FILES['csv_file'];
            $tariffId = $this->form_vars['tariffId'];
            $user_id = $this->user->getId();
            if (!empty($csv_files)){
                $tariffsdetailsfilterObj = new tariffsdetailsfilter();
                $tariffsdetailsfilterObj->addJoinCarrierZone();
                $tariffsdetailsfilterObj->addFieldFilter("td.tariffs_id", $tariffId);
                $tariffsdetailsfilterObjs = $tariffsdetailsfilterObj->getColumnList("td.to_zone_id, cz.name");
                $zones_arr  = $data_arr = [];
                $data_zones = [];
                $data_files = ['Base Tariff'];
                foreach ($tariffsdetailsfilterObjs as $tariffsdetailsfilterOb){
                    $zones_arr[strtolower($tariffsdetailsfilterOb->getName())] = $tariffsdetailsfilterOb->getToZoneId();
                    if(!in_array($tariffsdetailsfilterOb->getName(), $data_zones)){
                        $data_zones[]= $tariffsdetailsfilterOb->getName();
                    }
                }

                $fileData = $fullData = '';
                for($f=0; $f< count($csv_files['name']); $f++) {
                    if ($csv_files['name'][$f] !=='') {
                        $file_no = $data_files[] = 'File'.($f+1);
                        $data_files[] = 'Diff';
                        $file_name = $csv_files['name'][$f];
                        $path_parts = pathinfo($file_name);
                        $ext = strtolower($path_parts['extension']);
                        $basename = $path_parts['basename'];
                        if ($ext === 'csv') {
                            $new_file_name = $account . "_" . time() . "_" . $basename;
                            $relPath = '../_assets/compare_tariff_csv/' . $new_file_name;
                            if (!file_exists("../_assets/compare_tariff_csv/")) {
                                @mkdir("../_assets/compare_tariff_csv/", 0775);
                            }
                            if (move_uploaded_file($csv_files['tmp_name'][$f], $relPath)) {
                                $liness = 0;
                                $file = fopen($relPath,"r");
                                if(!$file) {
                                    echo "Error opening data file.\n";
                                    exit;
                                }
                                $liness = 0;
                                $zones = [];
                                while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                                    $rowData = "";
                                    for($i = 0; $i <= count($column) -1; $i++) {
                                        $colData = '';
                                        if ($liness == 0){
                                            if ($i >0 && $i < (count($column) -1)){
                                                $zones[$i] = $column[$i];
                                            }
                                        }else {
                                            if ($i> 0 && !empty($column[0]) && $i !== count($column)){
                                                $colData .= $file_no.',';
                                                $weight = $column[0];
                                                $weights = explode("-",$weight);
                                                $colData .= $weights[0];
                                                $colData .= ','.$weights[1];
                                                $colData .= ','.$user_id;
                                                $colData .= ','.$tariffId;
                                            }
                                            if($i> 0 && $i < count($column) -1){
                                                $formula  = (isset($column[count($column) -1]) ? $column[count($column) -1] : '');
                                                $tariffValue = $column[$i];
                                                $tariffValues = explode('/', $tariffValue);
                                                if (count($tariffValues) > 0) {
                                                    $csvPeiceCost = (isset($tariffValues[0]) ? $tariffValues[0] : 0);
                                                    $csvWeightCost = (isset($tariffValues[1]) ? $tariffValues[1] : 0);
                                                }

                                                $zone_id = empty($zones_arr[strtolower($zones[$i])])? 0:$zones_arr[strtolower($zones[$i])];
                                                $colData .= ','.$zone_id;
                                                $colData .= ','.$csvWeightCost;
                                                $colData .= ','.$csvPeiceCost;
                                                $totalCost = $csvWeightCost;
                                                if(!empty($formula)){
                                                    $findArr = ['Q','ITMCHR','REG','W','CHRG'];
                                                    $replaceArr = ['1',$csvPeiceCost,'0',$weights[1],$csvWeightCost];
                                                    $frmla = str_replace($findArr,$replaceArr,$formula);
                                                    eval('$totalCost = '.$frmla.';');
                                                }
                                                $colData .= ','.$totalCost;
                                                $colData .= ','.$formula;
                                            }else if($i> 0 && $i == count($column)){
                                                $colData .= ','.$column[$i];
                                                //$colData .= "\n";
                                            }else {
                                                continue;
                                            }
                                        }
                                        if ($liness == 0 ){
                                            //$rowData = "weight_from, weight_to, user_id, tariff_id, zone_id, weight_cost, piece_cost, formula \n";
                                        }else{
                                            $rowData .= $colData."\n";
                                        }
                                    }
                                    $fullData .= $rowData;
                                    $liness ++;
                                }
                            } else {
                                $output['message'] = "File ".$file_name." not upload successfully.";
                                $output['status'] = "fail";
                            }
                        } else {
                            $output['message'] = "Only CSV file is allowed.";
                            $output['status'] = "fail";
                        }
                    } else {
                        $output['message'] = "CSV file not found.";
                        $output['status'] = "fail";
                    }
                }  //end main for loop of files
                $fileData .= $fullData;
                $fileName = _ASSETS_PATH.$account . "_" . time().".csv";
                $res = $this->deleteUserData($user_id, $tariffId);
                if ($res == NULL){
                    if(file_put_contents($fileName, $fileData)){
                        $sql = "LOAD DATA LOCAL INFILE '".$fileName. "' INTO TABLE `compare_pricings` FIELDS  
                                        TERMINATED BY ',' LINES TERMINATED BY '\n' (
                                        `files`, `weight_from`, `weight_to`, `user_id`, `tariff_id`, `zone_id`, `weight_cost`, `piece_cost`, `total_value`, `formula`                 
                                    )";
                        $rs = DbAccess3::runQuery($sql);
                        $data_arr = ['zones'=>$data_zones, 'headings'=>$data_files];
                        $output['data'] = $data_arr;
                        $output['tariffId'] = $tariffId;
                        $output['status'] = "success";
                        $output['message'] = "File/s uploaded successfully.";
                    }else{
                        $output['message'] = "Error in file ".$file_name." uploading.";
                        $output['status'] = "fail";
                    }
                }else {
                    $output['message'] = "Error in dumping file ".$file_name;
                    $output['status'] = "fail";
                }
            }
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'compare_tariff_to_tariff') {
            $output = [];
            $user = SessionManager::getUser();
            $account = $user->getUserAccountId();
            $tariffNames = $this->form_vars['tariffNames'];
            $tariffId = $this->form_vars['tariffId'];
            $user_id = $this->user->getId();
            if (!empty($tariffNames)){
                $tariffsdetailsfilterObj = new tariffsdetailsfilter();
                $tariffsdetailsfilterObj->addJoinCarrierZone();
                $tariffsdetailsfilterObj->addFieldFilter("td.tariffs_id", $tariffId);
                $tariffsdetailsfilterObjs = $tariffsdetailsfilterObj->getColumnList("td.to_zone_id, cz.name");
                $zones_arr  = $data_arr = [];
                $data_zones = [];
                foreach ($tariffsdetailsfilterObjs as $tariffsdetailsfilterOb){
                    $zones_arr[strtolower($tariffsdetailsfilterOb->getName())] = $tariffsdetailsfilterOb->getToZoneId();
                    if(!in_array($tariffsdetailsfilterOb->getName(), $data_zones)){
                        $data_zones[]= $tariffsdetailsfilterOb->getName();
                    }
                }

                $data_arr = ['zones'=>$data_zones, 'headings'=>$tariffNames];
                $output['data'] = $data_arr;
                $output['tariffId'] = $tariffId;
                $output['status'] = "success";
                $output['message'] = "File/s uploaded successfully.";
            }else {
                $output['message'] = "Error in comparing.";
                $output['status'] = "fail";
            }
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'compare_csv_tariffs_save_excel') {
            $data = $zoneIds = $heads = [];
            $comp_files = ['Zones'];
            $user = SessionManager::getUser();
            $user_id = $this->user->getId();
            $zones                  = $this->form_vars['zones'];
            $files                  = $this->form_vars['files'];
            $collNm =0;
            if (!empty($files[0])){
                $files_arr = explode(",", $files[0]);
                array_push($files_arr, "Cheap");
                $collNm = count($files_arr);
            }
            foreach ($files_arr as $arr)
            {
                array_push($comp_files, $arr);
            }
            $tariffId               = $this->form_vars['tariff_id'];
            $tariff = new tariffs($tariffId);
            $ComparePricingFilterObj =  new ComparePricingFilter();
            $ComparePricingFilterObj->where(['c.tariff_id'=>$tariffId, 'c.user_id'=>$user_id]);
            $ComparePricingFilterObj->orderBy('c.zone_id, c.weight_from, c.weight_to');
            $ComparePricingFilterObjs = $ComparePricingFilterObj->getList();
            foreach($ComparePricingFilterObjs as $ComparePricingFilterObj) {
                if(!in_array($ComparePricingFilterObj->getZoneId(),$zoneIds)) {
                    $zoneIds[] =  $ComparePricingFilterObj->getZoneId();
                }
                sort($zoneIds);
            }
            $heading= ['Weights'];
            foreach($ComparePricingFilterObjs as $ComparePricingFilterObj) {
                $totalCost = 0;
                $zone_val = $ComparePricingFilterObj->getWeightFrom().'-'.$ComparePricingFilterObj->getWeightTo();
                if(!in_array($zone_val, $heading)){
                    $heading[] = $zone_val;
                }
                $zone_name = '';
                $carrierZone = new CarrierZones($ComparePricingFilterObj->getZoneId());
                $zone_name = cleanCsvCall($carrierZone->getName());
                $rowValue=  new ComparePricingFilter();
                $rowValue->where(['c.tariff_id'=>$tariffId, 'c.user_id'=>$user_id, 'c.zone_id'=>$ComparePricingFilterObj->getZoneId()]);
                $rowValue->where(['c.weight_from'=>$ComparePricingFilterObj->getWeightFrom()]);
                $rowValue->where(['c.weight_to'=>$ComparePricingFilterObj->getWeightTo()]);
                $rowValue->orderBy('c.files, c.zone_id, c.weight_from, c.weight_to');
                $rowValues = $rowValue->getList();

                $tariffDetailsFilter =  new TariffsDetailsFilter();
                $tariffDetailsFilter->addFieldFilter('td.tariffs_id',$tariffId);
                $tariffDetailsFilter->addFieldFilter('td.to_zone_id',$ComparePricingFilterObj->getZoneId());
                $tariffDetailsFilter->addFieldFilter('td.weight_from',$ComparePricingFilterObj->getWeightFrom());
                $tariffDetailsFilter->addFieldFilter('td.weight_to',$ComparePricingFilterObj->getWeightTo());
                $tariffDetailsFilter->addOrderBy('td.weight_from');
                $tariffDetailsFilterObjs = $tariffDetailsFilter->getList();
                if (!empty($tariffDetailsFilterObjs[0])){
                    $tariff_detail = $tariffDetailsFilterObjs[0];
                    $formula = $tariff_detail->getFormula();
                    $toWeight = $tariff_detail->getWeightTo();
                    $weightCost = $tariff_detail->getWeightCost();
                    $pieceCost = $tariff_detail->getPieceCost();
                    $totalCost = $weightCost;
                    if(!empty($formula)){
                        $findArr = ['Q','ITMCHR','REG','W','CHRG'];
                        $replaceArr = ['1',$pieceCost,'0',$toWeight,$weightCost];
                        $frmla = str_replace($findArr,$replaceArr,$formula);
                        eval('$totalCost = '.$frmla.';');
                    }
                }

                $first_arr = ['file_name'=>'Base Tariff', 'tariff_value'=>$totalCost];
                $min = $totalCost;
                $minFile = "Base Tariff";
                $i=0;
                foreach ($rowValues as $val){
                    $cheap_arr = [];
                    $tariff_val = sprintf("%.2f", $val->getTotalValue());
                    if ($tariff_val < $min){
                        $min = $tariff_val;
                        $minFile = $val->getFiles();
                    }
                    $diff = $totalCost - $tariff_val;
                    $diffPercent = round(($diff/$tariff_val)*100, 2);
                    $currArr = [];
                    if ($i == 0) {
                        $zone_arr = [];
                        $i++;
                        $second_arr = [
                            'file_name' => $val->getFiles(),
                            'tariff_value' => $tariff_val
                        ];
                        array_push($zone_arr, $first_arr);
                        array_push($zone_arr, $second_arr);
                    }else {
                        $third_arr = [
                            'file_name' => $val->getFiles(),
                            'tariff_value' => $tariff_val
                        ];
                        array_push($zone_arr, $third_arr);
                    }
                    $i++;
                    $diff_arr = ['file_name'=>'Diff', 'tariff_value'=>$diffPercent.'%'];
                    array_push($zone_arr, $diff_arr);
                }
                $cheap_arr['cheap'][] = ['file_name'=>'Cheap File', 'tariff_value'=>$minFile];
                $cheap_arr['cheap'][] = ['file_name'=>'Cheap', 'tariff_value'=>$min];
                array_push($zone_arr, $cheap_arr);
                if (!empty($zone_name)){
                    $data[$zone_name][$ComparePricingFilterObj->getWeightFrom()."-".$ComparePricingFilterObj->getWeightTo()] = $zone_arr;

                }
            }
            sort($zoneIds);
            $colNum = 'B';
            if(count($zoneIds)) {
                foreach($zoneIds as $zoneId) {
                    $carrierZone = new CarrierZones($zoneId);
                    if(!in_array(cleanCsvCall($carrierZone->getName()),$heading)) {
                        $heads[] = cleanCsvCall($carrierZone->getName());
                    }
                    $colNum++;
                }
            }
            $bold = array(
                'font' => array(
                    'bold' => true,
                )
            );
            $styleWeightColoumn = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'color' => array('rgb' => 'FFFFFF'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '665f5f')
                ),
            );
            $styleZoneReport = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 12,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5f5f7d')
                ),
            );
            $styleForPositiveDiff = array(
                'font' => array(
                    'size' => 10,
                    'color' => array('rgb' => '0b6e04'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            );
            $styleForNegativeDiff = array(
                'font' => array(
                    'size' => 10,
                    'color' => array('rgb' => 'a31903'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            );
            $styleHeadingForReport2 = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'color' => array('rgb' => 'FFFFFF'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '3d3636')
                ),
            );
            $styleForReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $styleEndReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );

            if (!empty($data)) {
                $currency = new Currency($tariff->getCurrencyId());
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);
                $k =0;
                $colNum ='A';
                $start = 'A';

                $rowNm = 2;
                $colNm = 'A';
                foreach ($heading as $h) {
                    if ($k ==0){
                        $objPHPExcel->getActiveSheet()->mergeCells($start.'1:'.$colNum.'1');
                        if ($k%2 == 0){
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleHeadingForReport2);
                        }else {
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleWeightColoumn);

                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue($start.'1', $h);
                    }else {
                        $objPHPExcel->getActiveSheet()->mergeCells($start.'1:'.$colNum.'1');
                        if ($k%2 == 0){
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleHeadingForReport2);
                        }else {
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleWeightColoumn);

                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue($start.'1', $h);

                    }
                    $start = $colNum;
                    foreach ($files_arr as $zone_id){
                        $colNum++;
                    }
                    $colNum++;
                    $start++;
                    if ($k < count($heading) -1){
                        if ($k==0){
                            foreach ($comp_files as $h) {
                                if ($h == 'Cheap'){
                                    $first = $colNm;
                                    $colNm++;
                                    $objPHPExcel->getActiveSheet()->mergeCells($first.'2:'.$colNm.'2');
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleHeadingForReport2);

                                    }
                                    $objPHPExcel->getActiveSheet()->SetCellValue($first.'2', $h);
                                }else {
                                    $cell_name = $colNm.$rowNm;
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleHeadingForReport2);

                                    }
                                    if ($h == "Base Tariff" || $h == 'Cheap'|| $h == 'Zones')
                                        $objPHPExcel->getActiveSheet()->getColumnDimension($colNm)->setWidth(18);

                                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                                    $colNm++;
                                }
                            }
                        }else {
                            $colNm++;
                            foreach ($files_arr as $h) {
                                if ($h == 'Cheap'){
                                    $first = $colNm;
                                    $colNm++;
                                    $objPHPExcel->getActiveSheet()->mergeCells($first.'2:'.$colNm.'2');
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleHeadingForReport2);

                                    }
                                    $objPHPExcel->getActiveSheet()->SetCellValue($first.'2', $h);
                                }else {
                                    $cell_name = $colNm.$rowNm;
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleHeadingForReport2);

                                    }
                                    if ($h == "Base Tariff" || $h == 'Cheap'|| $h == 'Zones')
                                        $objPHPExcel->getActiveSheet()->getColumnDimension($colNm)->setWidth(18);

                                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                                    $colNm++;
                                }
                            }
                        }
                        $k++;
                    }

                }
                $rowNum = 3;
                foreach($data as $country => $weights) {
                    $colNum = 'A';
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleWeightColoumn);
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $country);
                    $colNum++;
                    foreach($weights as $toWeight => $files) {
                        foreach($files as $cost) {
                            if (isset($cost['cheap'])){
                                foreach ($cost['cheap'] as $cheap){
                                    $file_name = $cheap['file_name'];
                                    $tariffValue = $cheap['tariff_value'];
                                    $totalCost = $tariffValue;
                                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$cheap['weight_cost']
                                    if ($file_name !== 'Cheap File'){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                                    }
                                    $colNum++;
                                }
                            }else {
                                $file_name = $cost['file_name'];
                                $tariffValue = $cost['tariff_value'];
                                $totalCost = $tariffValue;
                                if ($file_name == 'Diff') {
                                    if ($tariffValue > 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForPositiveDiff);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForNegativeDiff);
                                    }
                                }else {
                                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                                }

                                $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$cost['weight_cost']
                                if ($file_name !== 'Diff'){
                                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                                }
                                $colNum++;
                            }

                        }
                    }
                    $rowNum++;
                }

                $fileName = "tariff_excel_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
            }
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'compare_tariff_to_tariffs_save_excelssss') {
            $data = $zoneIds = $heads = $zones_arr = [];
            $weights = [0];
            $main_array = [];
            $user = SessionManager::getUser();
            $user_id = $this->user->getId();
            $collNm = $step = 0;
            $compare_tariffs                    = $this->form_vars['compare_tariffs'];
            $tariffs_arr = explode(",", $compare_tariffs);
            $tariffId                           = $this->form_vars['tariff_to_tariff_id'];
            $tariff = new tariffs($tariffId);
            $collNm = count($files_arr);
            $tariffsdetailsfilterObj = new tariffsdetailsfilter();
            $tariffsdetailsfilterObj->addJoinCarrierZone();
            $tariffsdetailsfilterObj->addFieldFilter("td.tariffs_id", $tariffId);
            $tariffsdetailsfilterObjs = $tariffsdetailsfilterObj->getColumnList("td.to_zone_id, td.weight_from, td.weight_to, td.weight_cost, td.piece_cost, cz.name");
            $data_zones = $wightsToArray = $weight_ranges = [];
            $heading= ['Weights'];
            $n = 0;
            $zones_tring = "";
            foreach ($tariffsdetailsfilterObjs as $tariffsdetailsfilterOb){
                $zones_arr[strtolower($tariffsdetailsfilterOb->getName())] = $tariffsdetailsfilterOb->getToZoneId();
                if(!in_array($tariffsdetailsfilterOb->getToZoneId(), $zoneIds)){
                    $zoneIds[]= $tariffsdetailsfilterOb->getToZoneId();
                    if ($n == 0) {
                        $zones_tring .= "'".strtolower($tariffsdetailsfilterOb->getName())."'";
                    }else {
                        $zones_tring .= ", '".strtolower($tariffsdetailsfilterOb->getName())."'";
                    }
                }
                if(!in_array($tariffsdetailsfilterOb->getWeightFrom(), $wightsToArray)){
                    $wightsToArray[] = $tariffsdetailsfilterOb->getWeightFrom();
                }
                if(!in_array($tariffsdetailsfilterOb->getWeightTo(), $wightsToArray)){
                    $wightsToArray[] = $tariffsdetailsfilterOb->getWeightTo();
                }
                //$colNum++;
                $n++;
            }
            $sql = "SELECT 
                    min(td.`weight_from`) as weight_from,
                    max(td.`weight_to`) as weight_to,
                    min(td.`weight_to` - td.`weight_from`) as step 
                    FROM
                    `tariffs_details` td
                    WHERE td.`tariffs_id` IN(".$compare_tariffs.", ".$tariffId.")";
            $steps = TariffsDetails::getTariffsDetailsListFromSql($sql);
            if (!empty($steps)){
               $step =  $steps[0]->getStep();
            }
            $max_weight_to = max($wightsToArray);
            $weight = 0;
            $i = 0;
            while ($weight< $max_weight_to){
                $weight += $step;
                $weights[] = $weight;
                $i++;
            }

            $sql = "SELECT td.*, t.`name`, cz.name as zone_name
                    FROM
                    `tariffs_details` td
                    LEFT JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                    JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2
                    WHERE td.`tariffs_id` IN(".$compare_tariffs.") AND LOWER(cz.`name`) IN(".$zones_tring.") ORDER BY td.weight_from, td.weight_to, zone_name  ASC";
            $comparePricingFilterObjs = TariffsDetails::getTariffsDetailsListFromSql($sql);
            //$mainArray = $this->compareTariffToTariffs($tariffId, $compare_tariffs, $wightsToArray, $zones_tring, $tariffsdetailsfilterObjs);
            //$ComparePricingFilterObjs = $mainArray['main_array'];
            $l = 0;
            $m = 1;
            while($l < $max_weight_to) {
                if (!empty($weights[$m])){
                    $range  = $weights[$l]."-".$weights[$m];
                    $weight_ranges[] = $range;
                }
                $l++;
                $m++;
            }

            foreach($weight_ranges as $zone_val) {
                if(!in_array($zone_val, $heading)){
                $heading[] = $zone_val;
                }
            }
            $comp_files = ['Zones', 'Base Tariff'];
            $files_arr = ['Base Tariff'];
            $k = 0;
            foreach ($zoneIds as $zone_id){
                $carrierZone = new CarrierZones($zone_id);
                $zone_name = cleanCsvCall($carrierZone->getName());
                foreach ($weight_ranges as $weight_range){
                    $ranges = explode("-", $weight_range);
                    foreach($tariffs_arr as $compare_tariff_id) {
                        if (count($wightsToArray) < count($weights)){
                            $sql = "SELECT td.*, t.`name`, cz.name as zone_name
                            FROM
                            `tariffs_details` td
                            LEFT JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                            JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2
                            WHERE td.`tariffs_id` = ".$compare_tariff_id." AND LOWER(cz.`name`) = '".strtolower($zone_name)."' ORDER BY zone_name, td.weight_from, td.weight_to ASC";

                        }else {
                            $sql = "SELECT td.*, t.`name`, cz.name as zone_name
                            FROM
                            `tariffs_details` td
                            LEFT JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                            JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2
                            WHERE td.`tariffs_id` = ".$compare_tariff_id." AND LOWER(cz.`name`) = '".strtolower($zone_name)."' AND td.`weight_from` <=".$ranges[0]." AND td.`weight_to` >=".$ranges[1]." ORDER BY zone_name, td.weight_from, td.weight_to ASC";
                        }
                        $comparePricingFilterObjs = TariffsDetails::getTariffsDetailsListFromSql($sql);
                        $tariffObj = new Tariffs($compare_tariff_id);
                        $tariff_name = $tariffObj->getName();
                        if(!in_array($tariff_name."-".$compare_tariff_id, $comp_files)){
                            $comp_files[] = $files_arr[] = $tariff_name."-".$compare_tariff_id;
                            $comp_files[] = $files_arr[] = 'Diff';
                            $comp_files[] = $files_arr[] = 'Cheap';
                        }
                        $i=0;
                        foreach ($comparePricingFilterObjs as $val){
                            $tariff_name = $val->getName()."-".$compare_tariff_id;
                            $zone_arr = [];
                            $totalCost = $pieceCost = $weightCost = $toWeight = 0;
                            if (count($wightsToArray) < count($weights)) {
                                $sql = "SELECT td.*
                                    FROM
                                    `tariffs_details` td
                                    WHERE td.`tariffs_id` = " . $tariffId . " AND td.`to_zone_id` = " . $zone_id . " AND td.`weight_from` <=" . $val->getWeightFrom() . " AND td.`weight_to` >=" . $val->getWeightTo() . " ORDER BY td.`weight_from`, td.`weight_to` ASC LIMIT 1";
                            }else {
                                $sql = "SELECT td.*
                                    FROM
                                    `tariffs_details` td
                                    WHERE td.`tariffs_id` = " . $tariffId . " AND td.`to_zone_id` = " . $zone_id . " AND td.`weight_from` =" .$ranges[0] . " AND td.`weight_to` =" . $ranges[1] . " ORDER BY td.`weight_from`, td.`weight_to` ASC LIMIT 1";
                            }
                            $tariffDetails = TariffsDetails::getTariffsDetailsListFromSql($sql);
                            if (!empty($tariffDetails[0])){
                                $formula = $tariffDetails[0]->getFormula();
                                $toWeight = $tariffDetails[0]->getWeightTo();
                                $weightCost = $tariffDetails[0]->getWeightCost();
                                $pieceCost = $tariffDetails[0]->getPieceCost();
                                $totalCost = ($weightCost * $toWeight) + $pieceCost;
                                if(!empty($formula)){
                                    $findArr = ['Q','ITMCHR','REG','W','CHRG'];
                                    $replaceArr = ['1',$pieceCost,'0',$toWeight,$weightCost];
                                    $frmla = str_replace($findArr,$replaceArr,$formula);
                                    eval('$totalCost = '.$frmla.';');
                                }
                                $first_arr = ['file_name'=>'Base Tariff', 'tariff_value'=>$totalCost];
                                $min = $totalCost;
                                $minFile = "Base Tariff";
                            }


                            $innerToWeight = $val->getWeightTo();
                            $carrierZoneName = new CarrierZones($val->getToZoneId());
                            $carrier_zone_name = cleanCsvCall($carrierZoneName->getName());
                            if ($carrier_zone_name == $zone_name){
                                $cheap_arr = [];
                                $tariff_val = sprintf("%.2f", $val->getWeightCost());
                                if ($tariff_val < $min){
                                    $min = $tariff_val;
                                    $minFile = $val->getName();
                                }
                                $diff = $totalCost - $tariff_val;
                                $diffPercent = round(($diff/$tariff_val)*100, 2);
                                $currArr = [];
                                if ($i == 0) {
                                    $second_arr = [
                                        'file_name' => $val->getName(),
                                        'tariff_value' => $tariff_val
                                    ];
                                    array_push($zone_arr, $first_arr);
                                    array_push($zone_arr, $second_arr);
                                }else {
                                    $third_arr = [
                                        'file_name' => $val->getName(),
                                        'tariff_value' => $tariff_val
                                    ];
                                    //array_push($zone_arr, $first_arr);
                                    array_push($zone_arr, $third_arr);
                                }
                                $file_name = $third_arr['tariff_name'];
                                $diff_arr = ['file_name'=>'Diff', 'tariff_value'=>$diffPercent.'%'];
                                array_push($zone_arr, $diff_arr);
                                $cheap_arr['cheap'][] = ['file_name'=>'Cheap File', 'tariff_value'=>$minFile];
                                $cheap_arr['cheap'][] = ['file_name'=>'Cheap', 'tariff_value'=>$min];
                                array_push($zone_arr, $cheap_arr);
                                if(empty($data[$zone_name][$weight_range][$tariff_name])){
                                    $data[$zone_name][$weight_range][$tariff_name] = $zone_arr;
                                }

                            }
                            $i++;
                        }
                    }
                }
                $k++;
            }
            $colNum = 'B';
            if(count($zoneIds)) {
                foreach($zoneIds as $zoneId) {
                    $carrierZone = new CarrierZones($zoneId);
                    if(!in_array(cleanCsvCall($carrierZone->getName()),$heading)) {
                        $heads[] = cleanCsvCall($carrierZone->getName());
                    }
                    $colNum++;
                }
            }
            $bold = array(
                'font' => array(
                    'bold' => true,
                )
            );
            $styleWeightColoumn = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'color' => array('rgb' => 'FFFFFF'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '665f5f')
                ),
            );
            $styleZoneReport = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 12,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5f5f7d')
                ),
            );
            $styleForPositiveDiff = array(
                'font' => array(
                    'size' => 10,
                    'color' => array('rgb' => '0b6e04'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            );
            $styleForNegativeDiff = array(
                'font' => array(
                    'size' => 10,
                    'color' => array('rgb' => 'a31903'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            );
            $styleHeadingForReport2 = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'color' => array('rgb' => 'FFFFFF'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '3d3636')
                ),
            );
            $styleForReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $styleEndReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );

            if (!empty($data)) {
                $currency = new Currency($tariff->getCurrencyId());
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);
                $k =0;
                $colNum ='A';
                $start = 'A';

                $rowNm = 2;
                $colNm = 'A';
                foreach ($heading as $h) {
                    if ($k ==0){
                        $objPHPExcel->getActiveSheet()->mergeCells($start.'1:'.$colNum.'1');
                        if ($k%2 == 0){
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleHeadingForReport2);
                        }else {
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleWeightColoumn);

                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue($start.'1', $h);
                    }else {
                        $objPHPExcel->getActiveSheet()->mergeCells($start.'1:'.$colNum.'1');
                        if ($k%2 == 0){
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleHeadingForReport2);
                        }else {
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleWeightColoumn);

                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue($start.'1', $h);

                    }
                    $start = $colNum;
                    if (count($files_arr) > 4){
                        foreach ($comp_files as $zone_id){
                            $colNum++;
                        }
                       $quotion =  count($files_arr)/4;
                        for ($i =1; $i <= (int)$quotion; $i++){
                            $colNum++;
                        }
                    }else {
                        foreach ($files_arr as $zone_id){
                            $colNum++;
                        }
                        $colNum++;
                    }


                    $start++;
                    if ($k < count($heading) -1){
                        if ($k==0){
                            foreach ($comp_files as $h) {
                                if ($h == 'Cheap'){
                                    $first = $colNm;
                                    $colNm++;
                                    $objPHPExcel->getActiveSheet()->mergeCells($first.'2:'.$colNm.'2');
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleHeadingForReport2);
                                    }
                                    $objPHPExcel->getActiveSheet()->SetCellValue($first.'2', $h);
                                    $colNm++;
                                }else {
                                    $cell_name = $colNm.$rowNm;
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleHeadingForReport2);
                                    }
                                    if ($h == "Base Tariff" || $h == 'Cheap'|| $h == 'Zones')
                                        $objPHPExcel->getActiveSheet()->getColumnDimension($colNm)->setWidth(18);


                                    $hs = explode("-", $h);
                                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $hs[0]);
                                    $colNm++;
                                }
                            }
                        }else {
                            foreach ($files_arr as $h) {
                                if ($h == 'Cheap'){
                                    $first = $colNm;
                                    $colNm++;
                                    $objPHPExcel->getActiveSheet()->mergeCells($first.'2:'.$colNm.'2');
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleHeadingForReport2);

                                    }
                                    $objPHPExcel->getActiveSheet()->SetCellValue($first.'2', $h);
                                    $colNm++;
                                }else {
                                    $cell_name = $colNm.$rowNm;
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleHeadingForReport2);

                                    }
                                    if ($h == "Base Tariff" || $h == 'Cheap'|| $h == 'Zones')
                                        $objPHPExcel->getActiveSheet()->getColumnDimension($colNm)->setWidth(18);

                                    $hs = explode("-", $h);
                                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $hs[0]);
                                    $colNm++;
                                }
                            }
                        }
                        $k++;
                    }
                }
                //goto endline;
                $rowNum = 3;
                foreach($data as $country => $weights) {
                    $colNum = 'A';
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleWeightColoumn);
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $country);
                    $colNum++;
                    foreach($weights as $toWeight => $tariffs) {
                        $p = 0;
                        foreach($tariffs as $tariff_name=>$tariffArr) {
                            foreach ($tariffArr as $tariff){
                                if (isset($tariff['cheap'])){
                                    foreach ($tariff['cheap'] as $cheap){
                                        $file_name = $cheap['file_name'];
                                        $tariffValue = $cheap['tariff_value'];
                                        $totalCost = $tariffValue;
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$cheap['weight_cost']
                                        if ($file_name !== 'Cheap File'){
                                            $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                                        }
                                        $colNum++;
                                    }
                                }else {
                                    $file_name = $tariff['file_name'];
                                    $tariffValue = $tariff['tariff_value'];
                                    if ($p !=0 && $file_name == 'Base Tariff'){
                                        continue;
                                    }
                                    $totalCost = $tariffValue;
                                    if ($file_name == 'Diff') {
                                        if ($tariffValue > 0){
                                            $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForPositiveDiff);
                                        }else {
                                            $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForNegativeDiff);
                                        }
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                                    }

                                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$tariff['weight_cost']
                                    if ($file_name !== 'Diff'){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                                    }
                                    $colNum++;
                                }
                            }
                            $p++;
                        }
                    }
                    $rowNum++;
                }
                //endline:
                $fileName = "tariff_to_tariffs_excel_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
            }
        }



        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'compare_tariff_to_tariffs_save_excel') {
            $data = $zoneIds = $heads = $zones_arr = [];
            $weights = [0];
            $main_array = [];
            $user = SessionManager::getUser();
            $user_id = $this->user->getId();
            $collNm = $step = 0;
            $compare_tariffs                    = $this->form_vars['compare_tariffs'];
            $tariffs_arr = explode(",", $compare_tariffs);
            $tariffId                           = $this->form_vars['tariff_to_tariff_id'];
            $tariff = new tariffs($tariffId);
            $collNm = count($files_arr);
            $tariffsdetailsfilterObj = new tariffsdetailsfilter();
            $tariffsdetailsfilterObj->addJoinCarrierZone();
            $tariffsdetailsfilterObj->addFieldFilter("td.tariffs_id", $tariffId);
            $tariffsdetailsfilterObjs = $tariffsdetailsfilterObj->getColumnList("td.to_zone_id, td.weight_from, td.weight_to, td.weight_cost, td.piece_cost, cz.name");
            $data_zones = $wightsToArray = $weight_ranges = [];
            $heading = ['Weights'];
            $n = 0;
            $zones_tring = "";
            foreach ($tariffsdetailsfilterObjs as $tariffsdetailsfilterOb){
                $zones_arr[strtolower($tariffsdetailsfilterOb->getName())] = $tariffsdetailsfilterOb->getToZoneId();
                if(!in_array($tariffsdetailsfilterOb->getToZoneId(), $zoneIds)){
                    $zoneIds[]= $tariffsdetailsfilterOb->getToZoneId();
                    if ($n == 0) {
                        $zones_tring .= "'".strtolower($tariffsdetailsfilterOb->getName())."'";
                    }else {
                        $zones_tring .= ", '".strtolower($tariffsdetailsfilterOb->getName())."'";
                    }
                }
                if(!in_array($tariffsdetailsfilterOb->getWeightFrom(), $wightsToArray)){
                    $wightsToArray[] = $tariffsdetailsfilterOb->getWeightFrom();
                }
                if(!in_array($tariffsdetailsfilterOb->getWeightTo(), $wightsToArray)){
                    $wightsToArray[] = $tariffsdetailsfilterOb->getWeightTo();
                }
                //$colNum++;
                $n++;
            }

            $sql = "SELECT 
                    min(td.`weight_from`) as weight_from,
                    max(td.`weight_to`) as weight_to,
                    min(td.`weight_to` - td.`weight_from`) as step 
                    FROM
                    `tariffs_details` td
                    WHERE td.`tariffs_id` IN(".$compare_tariffs.", ".$tariffId.")";
            $steps = TariffsDetails::getTariffsDetailsListFromSql($sql);
            if (!empty($steps)){
                $step =  $steps[0]->getStep();
            }
            $max_weight_to = max($wightsToArray);
            $weight = 0;
            $i = 0;
            while ($weight< $max_weight_to){
                $weight += $step;
                $weights[] = $weight;
                $i++;
            }
            $sql = "SELECT td.*, t.`name`, cz.name as zone_name
                    FROM
                    `tariffs_details` td
                    LEFT JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                    JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2
                    WHERE td.`tariffs_id` IN(".$compare_tariffs.") AND LOWER(cz.`name`) IN(".$zones_tring.") ORDER BY td.weight_from, td.weight_to, zone_name  ASC";
            $comparePricingFilterObjs = TariffsDetails::getTariffsDetailsListFromSql($sql);
            //$mainArray = $this->compareTariffToTariffs($tariffId, $compare_tariffs, $wightsToArray, $zones_tring, $tariffsdetailsfilterObjs);
            //$ComparePricingFilterObjs = $mainArray['main_array'];
            $l = 0;
            $m = 1;
            while($l < $max_weight_to) {
                if (!empty($weights[$m])){
                    $range  = $weights[$l]."-".$weights[$m];
                    $weight_ranges[] = $range;
                }
                $l++;
                $m++;
            }
            foreach($weight_ranges as $zone_val) {
                if(!in_array($zone_val, $heading)){
                    $heading[] = $zone_val;
                }
            }
            $comp_files = ['Zones', 'Base Tariff'];
            $files_arr = ['Base Tariff'];
            $k = 0;
            foreach ($zoneIds as $zone_id){
                $carrierZone = new CarrierZones($zone_id);
                $zone_name = cleanCsvCall($carrierZone->getName());
                foreach ($weight_ranges as $weight_range){
                    $ranges = explode("-", $weight_range);
                    foreach($tariffs_arr as $compare_tariff_id) {
                        if (count($wightsToArray) < count($weights)){
                            $sql = "SELECT td.*, t.`name`, cz.name as zone_name
                            FROM
                            `tariffs_details` td
                            LEFT JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                            JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2
                            WHERE td.`tariffs_id` = ".$compare_tariff_id." AND LOWER(cz.`name`) = '".strtolower($zone_name)."' ORDER BY zone_name, td.weight_from, td.weight_to ASC";

                        }else {
                            $sql = "SELECT td.*, t.`name`, cz.name as zone_name
                            FROM
                            `tariffs_details` td
                            LEFT JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                            JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2
                            WHERE td.`tariffs_id` = ".$compare_tariff_id." AND LOWER(cz.`name`) = '".strtolower($zone_name)."' AND td.`weight_from` <=".$ranges[0]." AND td.`weight_to` >=".$ranges[1]." ORDER BY zone_name, td.weight_from, td.weight_to ASC";
                        }
                        $comparePricingFilterObjs = TariffsDetails::getTariffsDetailsListFromSql($sql);
                        $tariffObj = new Tariffs($compare_tariff_id);
                        $tariff_name = $tariffObj->getName();
                        if(!in_array($tariff_name."-".$compare_tariff_id, $comp_files)){
                            $comp_files[] = $files_arr[] = $tariff_name."-".$compare_tariff_id;
                            $comp_files[] = $files_arr[] = 'Diff';
                            //$comp_files[] = $files_arr[] = 'Cheap';
                        }
                        $i=0;
                        foreach ($comparePricingFilterObjs as $val){
                            $tariff_name = $val->getName()."-".$compare_tariff_id;
                            $zone_arr = [];
                            $totalCost = $pieceCost = $weightCost = $toWeight = 0;
                            if (count($wightsToArray) < count($weights)) {
                                $sql = "SELECT td.*
                                    FROM
                                    `tariffs_details` td
                                    WHERE td.`tariffs_id` = " . $tariffId . " AND td.`to_zone_id` = " . $zone_id . " AND td.`weight_from` <=" . $val->getWeightFrom() . " AND td.`weight_to` >=" . $val->getWeightTo() . " ORDER BY td.`weight_from`, td.`weight_to` ASC LIMIT 1";
                                $tariffDetails = TariffsDetails::getTariffsDetailsListFromSql($sql);
                                if (empty($tariffDetails)){
                                    $sql = "SELECT td.*
                                    FROM
                                    `tariffs_details` td
                                    WHERE td.`tariffs_id` = " . $tariffId . " AND td.`to_zone_id` = " . $zone_id . " AND td.`weight_from` >=" . $val->getWeightFrom() . " AND td.`weight_to` <=" . $val->getWeightTo() . " ORDER BY td.`weight_from`, td.`weight_to` ASC LIMIT 1";
                                    $tariffDetails = TariffsDetails::getTariffsDetailsListFromSql($sql);
                                }
                            }else {
                                $sql = "SELECT td.*
                                    FROM
                                    `tariffs_details` td
                                    WHERE td.`tariffs_id` = " . $tariffId . " AND td.`to_zone_id` = " . $zone_id . " AND td.`weight_from` =" .$ranges[0] . " AND td.`weight_to` =" . $ranges[1] . " ORDER BY td.`weight_from`, td.`weight_to` ASC LIMIT 1";
                                $tariffDetails = TariffsDetails::getTariffsDetailsListFromSql($sql);
                            }
                            if (!empty($tariffDetails[0])){
                                $formula = $tariffDetails[0]->getFormula();
                                $toWeight = $tariffDetails[0]->getWeightTo();
                                $weightCost = $tariffDetails[0]->getWeightCost();
                                $pieceCost = $tariffDetails[0]->getPieceCost();
                                $totalCost = ($weightCost * $toWeight) + $pieceCost;
                                if(!empty($formula)){
                                    $findArr = ['Q','ITMCHR','REG','W','CHRG'];
                                    $replaceArr = ['1',$pieceCost,'0',$toWeight,$weightCost];
                                    $frmla = str_replace($findArr,$replaceArr,$formula);
                                    eval('$totalCost = '.$frmla.';');
                                }
                                $first_arr = ['file_name'=>'Base Tariff', 'tariff_value'=>$totalCost];
                                $min = $totalCost;
                                $minFile = "Base Tariff";
                            }


                            $innerToWeight = $val->getWeightTo();
                            $carrierZoneName = new CarrierZones($val->getToZoneId());
                            $carrier_zone_name = cleanCsvCall($carrierZoneName->getName());
                            if ($carrier_zone_name == $zone_name){
                                $cheap_arr = [];
                                $tariff_val = sprintf("%.2f", $val->getWeightCost());
                                if ($tariff_val < $min){
                                    $min = $tariff_val;
                                    $minFile = $val->getName();
                                }
                                $diff = $totalCost - $tariff_val;
                                $diffPercent = round(($diff/$tariff_val)*100, 2);
                                $currArr = [];
                                if ($i == 0) {
                                    $second_arr = [
                                        'file_name' => $val->getName(),
                                        'tariff_value' => $tariff_val
                                    ];
                                    array_push($zone_arr, $first_arr);
                                    array_push($zone_arr, $second_arr);
                                }else {
                                    $third_arr = [
                                        'file_name' => $val->getName(),
                                        'tariff_value' => $tariff_val
                                    ];
                                    //array_push($zone_arr, $first_arr);
                                    array_push($zone_arr, $third_arr);
                                }
                                $file_name = $third_arr['tariff_name'];
                                $diff_arr = ['file_name'=>'Diff', 'tariff_value'=>$diffPercent.'%'];
                                array_push($zone_arr, $diff_arr);
                                //$cheap_arr['cheap'][] = ['file_name'=>'Cheap File', 'tariff_value'=>$minFile];
                                //$cheap_arr['cheap'][] = ['file_name'=>'Cheap', 'tariff_value'=>$min];
                                //array_push($zone_arr, $cheap_arr);
                                if(empty($data[$zone_name][$weight_range][$tariff_name])){
                                    $data[$zone_name][$weight_range][$tariff_name] = $zone_arr;
                                }
                            }
                            $i++;
                        }
                    }

                    $cheapest = 0;
                    $cheapest_file = '';
                    foreach($data[$zone_name] as  $tariffsArr) {
                        $c = 0;
                        foreach ($tariffsArr as $tariffs){
                            if ($c == 0){
                                $cheapest = $tariffs[0]['tariff_value'];
                            }

                            if ($tariffs[1]['tariff_value'] < $cheapest ){
                                $cheapest           = $tariffs[1]['tariff_value'];
                                $cheapest_file      = $tariffs[1]['file_name'];
                            }
                            $c++;
                        }
                    }
                    $cheap_arr[] = ['file_name'=>'Cheap File', 'tariff_value'=>$cheapest_file];
                    $cheap_arr[] = ['file_name'=>'Cheap', 'tariff_value'=>$cheapest];
                    $data[$zone_name][$weight_range]['Cheap'] = $cheap_arr;
                }
                $k++;

            }

            $comp_files[] = $files_arr[] = 'Cheap';
            $colNum = 'B';
            if(count($zoneIds)) {
                foreach($zoneIds as $zoneId) {
                    $carrierZone = new CarrierZones($zoneId);
                    if(!in_array(cleanCsvCall($carrierZone->getName()),$heading)) {
                        $heads[] = cleanCsvCall($carrierZone->getName());
                    }
                    $colNum++;
                }
            }
            $bold = array(
                'font' => array(
                    'bold' => true,
                )
            );
            $styleWeightColoumn = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'color' => array('rgb' => 'FFFFFF'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '665f5f')
                ),
            );
            $styleZoneReport = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 12,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5f5f7d')
                ),
            );
            $styleForPositiveDiff = array(
                'font' => array(
                    'size' => 10,
                    'color' => array('rgb' => '0b6e04'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            );
            $styleForNegativeDiff = array(
                'font' => array(
                    'size' => 10,
                    'color' => array('rgb' => 'a31903'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFFFF')
                ),
            );
            $styleHeadingForReport2 = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'color' => array('rgb' => 'FFFFFF'),
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '3d3636')
                ),
            );
            $styleForReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $styleEndReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );

            if (!empty($data)) {
                $currency = new Currency($tariff->getCurrencyId());
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);
                $k =0;
                $colNum ='A';
                $start = 'A';

                $rowNm = 2;
                $colNm = 'A';
                foreach ($heading as $h) {
                    if ($k ==0){
                        $objPHPExcel->getActiveSheet()->mergeCells($start.'1:'.$colNum.'1');
                        if ($k%2 == 0){
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleHeadingForReport2);
                        }else {
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleWeightColoumn);

                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue($start.'1', $h);
                    }else {
                        $objPHPExcel->getActiveSheet()->mergeCells($start.'1:'.$colNum.'1');
                        if ($k%2 == 0){
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleHeadingForReport2);
                        }else {
                            $objPHPExcel->getActiveSheet()->getStyle($start.'1:'.$colNum.'1')->applyFromArray($styleWeightColoumn);

                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue($start.'1', $h);

                    }
                    $start = $colNum;
                    if (count($files_arr) > 4){
                        foreach ($comp_files as $zone_id){
                            $colNum++;
                        }
                        $quotion =  count($files_arr)/4;
                        for ($i =1; $i <= (int)$quotion; $i++){
                            //$colNum++;
                        }
                    }else {
                        foreach ($files_arr as $zone_id){
                            $colNum++;
                        }
                        $colNum++;
                    }


                    $start++;
                    if ($k < count($heading) -1){
                        if ($k==0){
                            foreach ($comp_files as $h) {
                                if ($h == 'Cheap'){
                                    $first = $colNm;
                                    $colNm++;
                                    $objPHPExcel->getActiveSheet()->mergeCells($first.'2:'.$colNm.'2');
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleHeadingForReport2);
                                    }
                                    $objPHPExcel->getActiveSheet()->SetCellValue($first.'2', $h);
                                    $colNm++;
                                }else {
                                    $cell_name = $colNm.$rowNm;
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleHeadingForReport2);
                                    }
                                    if ($h == "Base Tariff" || $h == 'Cheap'|| $h == 'Zones')
                                        $objPHPExcel->getActiveSheet()->getColumnDimension($colNm)->setWidth(18);


                                    $hs = explode("-", $h);
                                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $hs[0]);
                                    $colNm++;
                                }
                            }
                        }else {
                            foreach ($files_arr as $h) {
                                if ($h == 'Cheap'){
                                    $first = $colNm;
                                    $colNm++;
                                    $objPHPExcel->getActiveSheet()->mergeCells($first.'2:'.$colNm.'2');
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($first.'2:'.$colNm.'2')->applyFromArray($styleHeadingForReport2);

                                    }
                                    $objPHPExcel->getActiveSheet()->SetCellValue($first.'2', $h);
                                    $colNm++;
                                }else {
                                    $cell_name = $colNm.$rowNm;
                                    if ($k%2 == 0){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleWeightColoumn);
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNm.$rowNm)->applyFromArray($styleHeadingForReport2);

                                    }
                                    if ($h == "Base Tariff" || $h == 'Cheap'|| $h == 'Zones')
                                        $objPHPExcel->getActiveSheet()->getColumnDimension($colNm)->setWidth(18);

                                    $hs = explode("-", $h);
                                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $hs[0]);
                                    $colNm++;
                                }
                            }
                        }
                        $k++;
                    }
                }
                //goto endline;
                $rowNum = 3;
                foreach($data as $country => $weights) {
                    $colNum = 'A';
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleWeightColoumn);
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $country);
                    $colNum++;
                    foreach($weights as $toWeight => $tariffs) {
                        $p = 0;
                        foreach($tariffs as $tariff_name=>$tariffArr) {
                            foreach ($tariffArr as $tariff){
                                if (isset($tariff['cheap'])){
                                    foreach ($tariff['cheap'] as $cheap){
                                        $file_name = $cheap['file_name'];
                                        $tariffValue = $cheap['tariff_value'];
                                        $totalCost = $tariffValue;
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$cheap['weight_cost']
                                        if ($file_name !== 'Cheap File'){
                                            $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                                        }
                                        $colNum++;
                                    }
                                }else {
                                    $file_name = $tariff['file_name'];
                                    $tariffValue = $tariff['tariff_value'];
                                    if ($p !=0 && $file_name == 'Base Tariff'){
                                        continue;
                                    }
                                    $totalCost = $tariffValue;
                                    if ($file_name == 'Diff') {
                                        if ($tariffValue > 0){
                                            $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForPositiveDiff);
                                        }else {
                                            $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForNegativeDiff);
                                        }
                                    }else {
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                                    }

                                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$tariff['weight_cost']
                                    if ($file_name !== 'Diff'){
                                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                                    }
                                    $colNum++;
                                }
                            }
                            $p++;
                        }
                    }
                    $rowNum++;
                }
                //endline:
                $fileName = "tariff_to_tariffs_excel_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
            }
        }



        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_tariff_excel') {
            $tariffId = $this->form_vars['tariffId'];
            $clientName = $this->form_vars['client_name'];
            $issueDate = $this->form_vars['issue_date'];
            $expireDate = $this->form_vars['expire_date'];
            $dimDiscription = $this->form_vars['dim_discription'];

            $tariff = new tariffs($tariffId);

            $tariffsdetailsfilterObj = new tariffsdetailsfilter();
            $tariffsdetailsfilterObj->addJoinCarrierZone();
            $tariffsdetailsfilterObj->addFieldFilter("td.tariffs_id", $tariffId);
            $tariffsdetailsfilterObj->addOrderBy("cz.name, cz.name, td.weight_from, td.weight_to", true);
            $tariffsdetailsfilterObjs = $tariffsdetailsfilterObj->getColumnList("td.to_zone_id, td.weight_from, td.weight_to, td.weight_cost, td.piece_cost, cz.name");
            $data = [];
            $zoneIds = [];
            $heading[] = "Weights/Zones";
            $data_zones = $wightsToArray = [];
            $n = 0;
            $zone_ids_tring = '';
            foreach ($tariffsdetailsfilterObjs as $tariffsdetailsfilterOb){
                $wightsToArray[] = $tariffsdetailsfilterOb->getWeightTo();
                $zones_arr[strtolower($tariffsdetailsfilterOb->getName())] = $tariffsdetailsfilterOb->getToZoneId();
                if(!in_array($tariffsdetailsfilterOb->getToZoneId(), $zoneIds)){
                    $zoneIds[]= $tariffsdetailsfilterOb->getToZoneId();
                    if ($n == 0) {
                        $zone_ids_tring .= "".$tariffsdetailsfilterOb->getToZoneId();
                    }else {
                        $zone_ids_tring .= ", ".$tariffsdetailsfilterOb->getToZoneId();
                    }
                }
                $zone_val = $tariffsdetailsfilterOb->getWeightTo();
                if(!in_array($zone_val, $heading)){
                    $heading[] = $zone_val;
                }
                $colNum++;
                $n++;
            }
            sort($zoneIds);


            foreach ($tariffsdetailsfilterObjs as $tariffsdetailsfilterOb){
                $totalCost = $pieceCost = $weightCost = $toWeight = 0;
                $zone_name = $formula = $sql = "";
                $carrierZone = new CarrierZones($tariffsdetailsfilterOb->getToZoneId());
                $zone_name = cleanCsvCall($carrierZone->getName());
                 $sql = "SELECT td.*, t.`name`
                         FROM
                            `tariffs_details` td
                            INNER JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                        WHERE 
                            td.`tariffs_id` = ".$tariffId." "
                        . "AND td.`to_zone_id` =".$tariffsdetailsfilterOb->getToZoneId()." ORDER BY td.weight_from ASC";
                $ComparePricingFilterObjs = TariffsDetails::getTariffsDetailsListFromSql($sql);
                $i=0;
                $zone_arr = [];
                foreach ($ComparePricingFilterObjs as $val){
                    $tariff_val = $val->getWeightCost();
                    $currArr = [];
                    $currArr = [
                            'weight_cost'=>$val->getWeightCost(),
                            'piece_cost'=>$val->getPieceCost(),
                            'formula'=>$val->getFormula(),
                    ];
                    array_push($zone_arr, $currArr);
                    $i++;
                }

                $data[$zone_name][$tariffsdetailsfilterOb->getWeightTo()] = $zone_arr;
                
            }
            $bold = array(
                'font' => array(
                    'bold' => true,
                )
            );
            $styleWeightColoumn = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'eaeaea')
                ),
            );
            $styleZoneReport = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 12,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5f5f7d')
                ),
            );
            $styleServiceForReport = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 16,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '1D2555')
                ),
            );
            $styleForReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $styleEndReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            if (!empty($data)) {
                $currency = new Currency($tariff->getCurrencyId());
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);
                // IMAGES
                $logo = User::getUserCompanyImages(true);
                if(!empty($logo) && file_exists($logo)){
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('Company Logo');
                    $objDrawing->setDescription('Company Logo');
                    $objDrawing->setPath($logo);
                    $objDrawing->setCoordinates('D1');
                    //setOffsetX works properly
                    $objDrawing->setOffsetX(5);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(1000);
                    $objDrawing->setHeight(100);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }

                $objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleZoneReport);
                $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($styleZoneReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A4', "Client Name");
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', "Date Issued");
                $objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($styleForReport);
                $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($styleForReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('B4', $clientName);
                $objPHPExcel->getActiveSheet()->SetCellValue('B5', $issueDate);

                
                $rowNum = 9;
                $colNum = 'A';
                $mergCellFrom = $colNum.$rowNum;
                foreach ($heading as $h) {
                    $cell_name = $colNum.$rowNum;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleZoneReport);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth(16);
                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                    $colNum++;
                }
                $ascii = $this->getColNo($colNum);
                $colNum = $this->getLetter($ascii);
                $objPHPExcel->getActiveSheet()->mergeCells('A7:'.$colNum.'8');
                $objPHPExcel->getActiveSheet()->getStyle('A7:'.$colNum.'8')->applyFromArray($styleServiceForReport);
                $service = new Services($tariff->getServiceId());
                $objPHPExcel->getActiveSheet()->SetCellValue('A7', $service->getName());
                $rowNum=10;
                
                foreach($data as $zoneName => $zones) {
                    $colNum = 'A';
                    $zone_id = '';
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleZoneReport);
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $zoneName);
                    $colNum++;
                    $i = 0;
                    foreach($zones as $toWeight => $cost) {
                        $formula = $cost[$i]['formula'];
                        $weightCost = $cost[$i]['weight_cost'];
                        $pieceCost = $cost[$i]['piece_cost'];
                        $totalCost = $weightCost;
                        if(!empty($formula)){
                            $findArr = ['Q','ITMCHR','REG','W','CHRG'];
                            $replaceArr = ['1',$pieceCost,'0',$toWeight,$weightCost];
                            $frmla = str_replace($findArr,$replaceArr,$formula);
                            eval('$totalCost = '.$frmla.';');
                        }
                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$cost[$i]['weight_cost']
                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                        $colNum++;
                        $i++;
                    }
                    $rowNum++;
                }

                $count = $rowNum;
                $userAccount = new CustomerAccount($this->user->getUserAccountId());
                $companyName = (!empty($userAccount)?$userAccount->getCompany():'');
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, '© '.$companyName);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $count.":F" . $count)->applyFromArray($bold);
                $count = $count + 2;
                $serviceDesc = "Tracked Solution EX NJ (provides full end to end tracking and online delivery confirmation)";
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $serviceDesc);
                $count = $count + 2;

                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':F'.$count);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$count.':F'.$count)->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Maximum  Dimensions & Weight Europe");
                $count++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':D'.$count);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$count.':D'.$count)->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Dimensions");
                $objPHPExcel->getActiveSheet()->mergeCells('E'.$count.':F'.$count);
                $objPHPExcel->getActiveSheet()->getStyle('E'.$count.':F'.$count)->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$count, "Weight");
                $count++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':D'.($count+4));
                $objPHPExcel->getActiveSheet()->getStyle('A'.$count.':D'.($count+4))->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, $dimDiscription);
                $objPHPExcel->getActiveSheet()->mergeCells('E'.$count.':F'.($count+4));
                $objPHPExcel->getActiveSheet()->getStyle('E'.$count.':F'.($count+4))->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$count, "up to ".$endWeight." KG");
                $count = $count + 5;

                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "The above rates are based on pre-labeled shipments as per One World Routings");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "The rates offered are based on an average volume and send profile.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "One World Express reserve the right to revise these rates should this profile change adversely.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Rates are valid untill ".$expireDate." unless revoked earlier with 30 days notice.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Rates are subject to VAT if applicable.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Rates needs to be accepted within a maximum 30 days from date of issued.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Subject to the One World Express Terms and Conditions.");
                $objPHPExcel->getActiveSheet()->getStyle('A'. $count)->applyFromArray($bold);
                $count = $count+2;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Tariff Acceptance");
                $objPHPExcel->getActiveSheet()->getStyle('A'. $count)->applyFromArray($bold);
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Date");
                $objPHPExcel->getActiveSheet()->getStyle('A'. $count)->applyFromArray($bold);
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Name of the Company: ");
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$count, "One World Representative:  ");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Position:");
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$count, "Position: ");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Name:");


                $fileName = "tariff_excel_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
            }
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'compare_tariff') {
            $output = array();
            $tariffId = $this->form_vars['tariffId'];
            $sql = "select
                            min(td.`weight_from`) as weight_from,
                            max(td.`weight_to`) as weight_to,
                            min(td.`weight_to` - td.`weight_from`) as step 
                        from
                            `tariffs_details` td
                        where td.`tariffs_id` = ".$tariffId;
            $tariffDetailWeight = TariffsDetails::getTariffsDetailsListFromSql($sql);
            $minWeight = "";
            $maxWeight = "";
            $step = "";
            if(count($tariffDetailWeight)) {
                $minWeight = $tariffDetailWeight[0]->getWeightFrom();
                $maxWeight = $tariffDetailWeight[0]->getWeightTo();
                $step = $tariffDetailWeight[0]->getStep();
                if((($step > 0) && ($maxWeight > 0)) && (fmod($maxWeight,$step) != 0)) {
                    $step = fmod($maxWeight,$step);
                }
            }
            $tariffZoneType = $this->form_vars['tariffZoneType'];
            $vaildsZoneIds = $this->getValidZoneFromTariffId($tariffId);
            sort($vaildsZoneIds);
            $tariffObj = new Tariffs($tariffId);
            $carrierId = $tariffObj->getCarrierId();
            $tariffDetailsFilter =  new TariffsDetailsFilter();
            $tariffDetailsFilter->addFieldFilter('td.tariffs_id',$tariffId);
            $tariffDetailsFilter->addOrderBy('td.weight_from');
            $tariffDetailsFilterObjs = $tariffDetailsFilter->getList();
            $from_zone_id = "";
            $tariffsDetailArray = [];
            $tariffsCsvArray = [];
            $tariffsToZoneCsvArray = [];
            if(count($tariffDetailsFilterObjs)) {
                $from_zone_id = $tariffDetailsFilterObjs[0]->getFromZoneId();
                foreach($tariffDetailsFilterObjs as $tariffDetailsFilterObj) {
                    $zone = $tariffDetailsFilterObj->getToZoneId();
                    $weight = $tariffDetailsFilterObj->getWeightFrom()."/".$tariffDetailsFilterObj->getWeightTo();
                    $tariffsDetailArray[$zone][$weight] = [
                        'weight_cost' => $tariffDetailsFilterObj->getWeightCost(),
                        'piece_cost' => $tariffDetailsFilterObj->getPieceCost()
                    ];
                }
                ksort($tariffsDetailArray);
            }
            $row = 1;
            $csvMinWeight = 0;
            $csvMaxWeight = 0;
            $csvStep = '';
            $relPath = $this->form_vars['csvFilePath'];
            if (($handle = fopen($relPath, "r")) !== FALSE) {
                if($tariffZoneType == "single") {
                    $toZoneIds = [];
                    $formulaValue = '';
                    $to_zone_id = '';
                    $weight_from = '';
                    $weight_to = '';
                    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                        $lastKey = (count($data) - 1);
                        foreach ($data as $key => $value) {
                            if($key > 0 && $row == 1) {
                                /* Get Zone Id From Name */
                                if (($key > 0) && ($key != $lastKey)) {
                                    $carrierObj = new Carrier($carrierId);
                                    $carrierZoneBase = $carrierObj->getZoneBase();
                                    $carrierZonesNameFilter = new CarrierZonesFilter();
                                    $carrierZonesNameFilter->addFieldFilter('    TRIM(UPPER(cz.name))', trim(strtoupper($value)));
                                    $carrierZonesNameFilter->addFieldFilter('    cz.carrier_id', $tariffObj->getCarrierId());
                                    $carrierZonesNameFilter->addFieldFilter('    cz.status', 1);
                                    if ($carrierZoneBase == 0) {
                                        $carrierZonesNameFilter->addFieldFilter('    cz.service_id', $tariffObj->getServiceId());
                                    }
                                    $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                                    if (count($zoneFilterObj) > 0) {
                                        $toZoneIds[$key] = $zoneFilterObj[0]->getId();
                                        $tariffsToZoneCsvArray[] = $zoneFilterObj[0]->getId();
                                    } else {
                                        $toZoneIds[$key] = '';
                                    }
                                }
                            }
                            if($key == 0 && $row > 1) {
                                $weight = explode('/', $value);
                                $weight_from = $weight[0];
                                $weight_to = $weight[1];
                                if($csvMinWeight == 0 && $row == 2) {
                                    $csvMinWeight = $weight_from;
                                }
                                if($csvMaxWeight == 0 && $row == 2) {
                                    $csvMaxWeight = $weight_to;
                                }
                                if($weight_from < $csvMinWeight) {
                                    $csvMinWeight = $weight_from;
                                }
                                if($weight_to > $csvMaxWeight) {
                                    $csvMaxWeight = $weight_to;
                                }
                                $currentStep = $weight_to - $weight_from;
                                if($csvStep == 0) {
                                    $csvStep = $currentStep;
                                }
                                if($currentStep < $csvStep) {
                                    $csvStep = $currentStep;
                                }
                            }
                            if ($key > 0 && $row > 1) {
                                if ($lastKey == $key) {
                                    $formulaValue = $data[$key];
                                }
                                $to_zone_id = isset($toZoneIds[$key]) ? $toZoneIds[$key] : 0;
                                $csvWeightCost = '';
                                $csvPeiceCost = '';
                                $arr = explode('/', $value);
                                if (count($arr) > 0) {
                                    $csvWeightCost = $arr[0];
                                    $csvPeiceCost = (isset($arr[1]) ? $arr[1] : 0.00);
                                }
                                if (in_array($to_zone_id, $vaildsZoneIds) && $to_zone_id > 0) {
                                    $zone = $to_zone_id;
                                    $weight = $weight_from.'/'.$weight_to;
                                    $tariffsCsvArray[$zone][$weight] = [
                                        'weight_cost' => $csvWeightCost,
                                        'piece_cost' => $csvPeiceCost,
                                        'formula' => $formulaValue
                                    ];
                                    ksort($tariffsCsvArray);
                                    ksort($tariffsCsvArray[$zone]);
                                }
                            }
                        }
                        $row++;
                    }
                }
                $tariffArray = [];
                $finalStep = '';
                $finalMinWeight = $csvMinWeight;
                $finalMaxWeight = $csvMaxWeight;
                if($csvStep < $step) {
                    $tariffArray = $tariffsCsvArray;
                    if((($csvStep > 0) && ($csvMaxWeight > 0)) && (fmod($csvMaxWeight,$csvStep) != 0)) {
                        $finalStep = fmod($csvMaxWeight,$csvStep);
                    } else {
                        $finalStep = $csvStep;
                    }
                } else {
                    $tariffArray = $tariffsDetailArray;
                    if ((($step > 0) && ($maxWeight > 0)) && (fmod($maxWeight,$step) != 0)) {
                        $finalStep = fmod($maxWeight,$step);
                    } else {
                        $finalStep = $step;
                    }
                }
                $tariffsToZoneCsvArray = array_unique($tariffsToZoneCsvArray);
                sort($tariffsToZoneCsvArray);
                $tariffArray = $this->allow_keys( $tariffArray, $tariffsToZoneCsvArray);
                $compareToZone = !empty(array_intersect($tariffsToZoneCsvArray, $vaildsZoneIds));
                if($finalMaxWeight > $maxWeight) {
                    $finalMaxWeight = $maxWeight;
                }
                if($compareToZone) {
                    $tariffDetailDataForExcel = [];
                    if(!empty($tariffArray) && $finalStep > 0) {
                        foreach($tariffArray as $zoneTo => $weights) {
                            if(isset($tariffsDetailArray[$zoneTo])) {
                                for ($weight = $finalMinWeight; $weight <= $finalMaxWeight;) {
                                    if ($this->numberFormat(($weight + $finalStep)) <= $finalMaxWeight) {
                                        $startLimit = $this->numberFormat($weight);
                                        $endLimit = $this->numberFormat($weight + $finalStep);
                                        foreach ($weights as $weightsArr => $arr) {
                                            $limits = explode("/", $weightsArr);
                                            if ($limits[0] <= $startLimit && $endLimit <= $limits[1]) {
                                                $tariffDetailDataForExcel[$zoneTo][$startLimit . "/" . $endLimit] = [
                                                    'weight_cost' => $arr['weight_cost'],
                                                    'piece_cost' => $arr['piece_cost'],
                                                    'formula' => isset($arr['formula']) ? $arr['formula'] : ''
                                                ];
                                                break;
                                            }
                                        }
                                    }
                                    if ($finalStep != 0) {
                                        $weight += $finalStep;
                                    } else {
                                        $weight += 1;
                                    }
                                }
                            }
                        }
                        $returnJson['tariffsCsvArray'] = $tariffsCsvArray;
                        $returnJson['tariffsDetailArray'] = $tariffsDetailArray;
                        $returnJson['excelData'] = $tariffDetailDataForExcel;
                        $returnJson['finalMaxWeight'] = $finalMaxWeight;
                        $returnJson['finalMinWeight'] = $finalMinWeight;
                        $returnJson['finalStep'] = $finalStep;
                        $returnJson['csvStep'] = $csvStep;
                        $returnJson['step'] = $step;
                        $output['data'] = json_encode($returnJson);
                        $output['status'] = "success";
                    } else {
                        $output['message'] = "Sorry compare tariff is invalid";
                        $output['status'] = "fail";
                    }
                } else {
                    $output['message'] = "Sorry this csv zones is not valid to compare tariff";
                    $output['status'] = "fail";
                }
            } else {
                $output['message'] = "CSV file not found";
                $output['status'] = "fail";
            }
            echo json_encode($output);
            die;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'compare_tariff_save_excel') {
            $output = [];
            $data = json_decode($this->form_vars['data']);
            $tariffsCsvArray = $data->tariffsCsvArray;
            $tariffsDetailArray = $data->tariffsDetailArray;
            $tariffDetailDataForExcel = $data->excelData;
            $finalMaxWeight = $data->finalMaxWeight;
            $finalMinWeight = $data->finalMinWeight;
            $finalStep = $data->finalStep;
            $csvStep = $data->csvStep;
            $step = $data->step;

            $objPHPExcel = new PHPExcel();
            $zoneHeadingBox =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 9,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'CCE5FF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $zoneHeadingBoxTwo =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 9,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FF9933')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $zoneDetailHeadingBox =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E0E0E0')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $weightBox =  array(
                'font' => array(
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $profitBox =  array(
                'font' => array(
                    'color' => array('rgb' => '006600'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $lossBox =  array(
                'font' => array(
                    'color' => array('rgb' => '660000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FF9999')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $NoProfitNoLossBox =  array(
                'font' => array(
                    'color' => array('rgb' => '994C00'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $valuesBox =  array(
                'font' => array(
                    'color' => array('rgb' => '000000'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $profitBox =  array(
                'font' => array(
                    'color' => array('rgb' => '006600'),
                    'size' => 8,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $borderBox =  array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $legentHeadingBox =  array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 9,
                    'name' => 'Arial',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'CCE5FF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            if(!empty($tariffDetailDataForExcel)) {
                $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($legentHeadingBox);
                $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Legend");
                $objPHPExcel->getActiveSheet()->getStyle('B1:B1')->applyFromArray($legentHeadingBox);
                $objPHPExcel->getActiveSheet()->SetCellValue('A2', "W");
                $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Weight Cost");
                $objPHPExcel->getActiveSheet()->SetCellValue('A3', "P");
                $objPHPExcel->getActiveSheet()->SetCellValue('B3', "Piece Cost");
                $stColNum = 'A';
                $colNum = 'A';
                $checkZoneNumber = 1;
                foreach ($tariffDetailDataForExcel as $zoneToId => $weights) {
                    $carrierZoneObj = new CarrierZones($zoneToId);
                    $rowNum = 4;
                    $colHead = $stColNum;
                    $colHead++;
                    $colHead++;
                    $colHead++;
                    $colHead++;
                    $colHead++;
                    if (fmod($checkZoneNumber,2) == 0) {
                        $objPHPExcel->getActiveSheet()->getStyle($stColNum . '4:' . $colHead . $rowNum)->applyFromArray($zoneHeadingBoxTwo);
                    } else {
                        $objPHPExcel->getActiveSheet()->getStyle($stColNum . '4:' . $colHead . $rowNum)->applyFromArray($zoneHeadingBox);
                    }
                    $objPHPExcel->getActiveSheet()->mergeCells($stColNum . '4:' . $colHead . $rowNum);
                    $objPHPExcel->getActiveSheet()->SetCellValue($stColNum . '4', trim($carrierZoneObj->getName()));
                    foreach ($weights as $fromToWeight => $cost) {
                        $weightArr = explode('/',$fromToWeight);
                        $fromWeight = $weightArr[0];
                        $toWeight = $weightArr[1];
                        $colNum = $stColNum;
                        if ($rowNum == 4) {
                            $rowNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('8');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Weight");
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('12');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Customer Tariff\nW/P");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "System Tariff\nW/P");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Discount");
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('10');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Variance Amount\nW/P");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($zoneDetailHeadingBox);
                            $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth('16');
                            $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "Variance %\nW/P");
                            $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->getAlignment()->setWrapText(true);
                            $colNum++;
                        }
                        $colNum = $stColNum;
                        $weightCost = 0.00;
                        $pieceCost = 0.00;
                        $tariffWeightCost = 0.00;
                        $tariffPieceCost = 0.00;
                        if($csvStep < $step) {
                            $weightCost = $cost->weight_cost;
                            $pieceCost = $cost->piece_cost;
                            for ($weight = $finalMinWeight; $weight <= $toWeight;) {
                                if ($this->numberFormat(($weight + $finalStep)) <= $toWeight) {
                                    $startLimit = $this->numberFormat($weight);
                                    $endLimit = $this->numberFormat($weight + $finalStep);
                                    $findFromWeights = $tariffsDetailArray->$zoneToId;
                                    foreach ($findFromWeights as $weightsArr => $obj) {
                                        $limits = explode("/", $weightsArr);
                                        if ($limits[0] <= $startLimit && $endLimit <= $limits[1]) {
                                            $tariffWeightCost = $obj->weight_cost;
                                            $tariffPieceCost = $obj->piece_cost;
                                            break;
                                        }
                                    }
                                }
                                if($finalStep != 0) {
                                    $weight += $step;
                                } else {
                                    $weight += 1;
                                }
                            }
                        } else {
                            $tariffWeightCost = $cost->weight_cost;
                            $tariffPieceCost = $cost->piece_cost;
                            for ($weight = $finalMinWeight; $weight <= $toWeight;) {
                                if ($this->numberFormat(($weight + $finalStep)) <= $toWeight) {
                                    $startLimit = $this->numberFormat($weight);
                                    $endLimit = $this->numberFormat($weight + $finalStep);
                                    $findFromWeights = $tariffsCsvArray->$zoneToId;
                                    foreach ($findFromWeights as $weightsArr => $obj) {
                                        $limits = explode("/", $weightsArr);
                                        if ($limits[0] <= $startLimit && $endLimit <= $limits[1]) {
                                            $weightCost = $obj->weight_cost;
                                            $pieceCost = $obj->piece_cost;
                                            break;
                                        }
                                    }
                                }
                                if($finalStep != 0) {
                                    $weight += $step;
                                } else {
                                    $weight += 1;
                                }
                            }
                        }
                        $objRichText = new PHPExcel_RichText();
                        $objRichTextValue = new PHPExcel_RichText();
                        $profitLossPercentage = "0.00";
                        $profitLossPercentagePieces = "0.00";
                        $profitLossWeightCostAmount = '0.00';
                        $profitLossPieceCostAmount = '0.00';
                        if(!is_numeric($tariffWeightCost)) {
                            $tariffWeightCost = 0.00;
                        }
                        if(!is_numeric($weightCost)) {
                            $weightCost = 0.00;
                        }
                        if ($tariffWeightCost <  $weightCost) {
							$profit = 0;
                            $profit =  $weightCost - $tariffWeightCost;
                            $profitLossWeightCostAmount = trim($this->numberFormat($profit));
                            $tariffProftValueObj = $objRichTextValue->createTextRun($profitLossWeightCostAmount);
                            $tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
                            $objRichTextValue->createTextRun('/');
                            if($tariffWeightCost > 0)
                            	$profitLossPercentage = $this->numberFormat((($profit * 100) / $tariffWeightCost));
                            $tariffProftLossObj = $objRichText->createTextRun($profitLossPercentage."%");
                            $tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
                            $objRichText->createTextRun('/');
                        } else if ($tariffWeightCost > $weightCost){
                        	$loss = 0;
                            $loss = $tariffWeightCost - $weightCost;
                            $profitLossWeightCostAmount = trim($this->numberFormat($loss));
                            $tariffProftValueObj = $objRichTextValue->createTextRun($profitLossWeightCostAmount);
                            $tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
                            $objRichTextValue->createTextRun('/');
                            if($tariffWeightCost > 0)
                            	$profitLossPercentage = $this->numberFormat((($loss * 100) / $tariffWeightCost));
                            $tariffProftLossObj = $objRichText->createTextRun($profitLossPercentage."%");
                            $tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
                            $objRichText->createTextRun('/');
                        } else {
                            $tariffProftValueObj = $objRichTextValue->createTextRun($profitLossWeightCostAmount);
                            $tariffProftValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
                            $objRichTextValue->createTextRun('/');
                            $tariffProftLossObj = $objRichText->createTextRun($profitLossPercentage."%");
                            $tariffProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
                            $objRichText->createTextRun('/');
                        }
                        if(!is_numeric($tariffPieceCost)) {
                            $tariffPieceCost = 0.00;
                        }
                        if(!is_numeric($pieceCost)) {
                            $pieceCost = 0.00;
                        }
                        if ($tariffPieceCost < $pieceCost) {
							$profit = 0;
                            $profit = $pieceCost - $tariffPieceCost;
                            $profitLossPieceCostAmount = trim($this->numberFormat($profit));
                            $tariffLossValueObj = $objRichTextValue->createTextRun($profitLossPieceCostAmount);
                            $tariffLossValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
                            if($tariffPieceCost > 0)
                            	$profitLossPercentagePieces = $this->numberFormat((($profit * 100) / $tariffPieceCost));
                            $pieceProftLossObj = $objRichText->createTextRun($profitLossPercentagePieces."%");
                            $pieceProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "006600")));
                        } else if ($tariffPieceCost > $pieceCost){
                        	$loss = 0;
                            $loss = $tariffPieceCost - $pieceCost;
                            $profitLossPieceCostAmount = trim($this->numberFormat($loss));
                            $tariffLossValueObj = $objRichTextValue->createTextRun($profitLossPieceCostAmount);
                            $tariffLossValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
                            if($tariffPieceCost > 0)
                            	$profitLossPercentagePieces = $this->numberFormat((($loss * 100) / $tariffPieceCost));
                            $pieceProftLossObj = $objRichText->createTextRun($profitLossPercentagePieces."%");
                            $pieceProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "660000")));
                        } else {
                            $tariffLossValueObj = $objRichTextValue->createTextRun($profitLossPieceCostAmount);
                            $tariffLossValueObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
                            $pieceProftLossObj = $objRichText->createTextRun($profitLossPercentagePieces."%");
                            $pieceProftLossObj->getFont()->applyFromArray(array( "bold" => true, "color" => array("rgb" => "FF8000")));
                        }
                        $rowNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($weightBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($toWeight));
                        $colNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($weightCost) . '/' . $this->numberFormat($pieceCost));
                        $colNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat($tariffWeightCost) . '/' . $this->numberFormat($tariffPieceCost));
                        $colNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $this->numberFormat(0.00));
                        $colNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($valuesBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $objRichTextValue);
                        $colNum++;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum . $rowNum)->applyFromArray($borderBox);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $objRichText);
                        $colNum++;
                    }
                    $stColNum = $colNum;
                    $checkZoneNumber++;
                }
                $fileName = "compare_tariff_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                if (!file_exists("../_assets/compare_tariff_excel/")) {
                    @mkdir("../_assets/compare_tariff_excel/", 0775);
                }
                $excelFilePath = "../_assets/compare_tariff_excel/".$fileName.'.xls';
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save($excelFilePath);
                $excelFile = SETTING_MAIN_ASSETS.'compare_tariff_excel/' . $fileName.'.xls';
                $output['compare_excel_file'] = $excelFile;
                $output['status'] = "success";
            } else {
                $output['message'] = "Sorry no data found to compare";
                $output['status'] = "fail";
            }
            echo json_encode($output);
            die;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_tariff_template') {
            $tariffId = $this->form_vars['tariffId'];
            $tariffZoneType = $this->form_vars['tariffZoneType'];
            $zoneIds = $this->getValidZonesFromTariffId($tariffId);

            $zoneIds = array_unique($zoneIds);
            ////sort($zoneIds);
            /*  Download Csv Code  */
            $fileName = "tariff_csv_" . time(). ".csv";
            if($tariffZoneType == "single") {
                if (count($zoneIds)) {
                    $returnString = "";
                    $returnString .= "weight/zone,";
                    foreach ($zoneIds as $zoneId=>$zoneName) {
                        $zone_id = $zoneId;
                        $returnString .= cleanCsvCall($zoneName) . ",";
                    }
                    $returnString .= "formula,";
                    $returnString .= "\r\n";

                    $tariffsDetailsFilterObj = new TariffsDetailsFilter();
                    $tariffsDetailsFilterObj->addFieldFilter('   td.tariffs_id', $tariffId);
                    $tariffsDetailsFilterObj->addFieldFilter('   td.to_zone_id', $zone_id);
                    $tariffsDetailsFilterObj->addOrderBy('     td.weight_from');
                    $tariffsDetailsFilterObjs = $tariffsDetailsFilterObj->getList();
                    foreach ($tariffsDetailsFilterObjs as $tariffsDetailsFilterOb){
                        $returnString .= (int)$tariffsDetailsFilterOb->getWeightFrom()."-".(int)$tariffsDetailsFilterOb->getWeightTo().",";
                        $returnString .= "\r\n";
                    }
                }
            } else {
                if(count($zoneIds)) {
                    $returnString = "";
                    $returnString .= "weight,";
                    $returnString .= "from zone,";
                    $returnString .= "to zone,";
                    $returnString .= "weight cost,";
                    $returnString .= "item cost,";
                    $returnString .= "formula,";
                    $returnString .=  "\r\n";
                    foreach($zoneIds as $zoneFromId) {
                        foreach($zoneIds as $zoneToId) {
                            $returnString .= ",";
                            $carrierFromZone = '0--1';
                            $returnString .= cleanCsvCall($carrierFromZone->getName()) . ",";
                            $carrierToZone = new CarrierZones($zoneToId);
                            $returnString .= cleanCsvCall($carrierToZone->getName()) . ",";
                            $returnString .= ",";
                            $returnString .= ",";
                            $returnString .= ",";
                            $returnString .=  "\r\n";
                        }
                    }
                    $returnString .=  "\r\n";
                }
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_tariff_detail_csv') {
            $tariffId = $this->form_vars['tariffId'];
            $tariffObj = new Tariffs($tariffId);
            $zoneIds = [];
            if($tariffObj->getId() > 0){
                $tariffDetails = new TariffsDetailsFilter();
                $tariffDetails->addFieldFilter("    td.tariffs_id", $tariffId);
                $tariffDetails->setLimit("-1");
                $tariffDetailsObjs = $tariffDetails->getList();
                foreach($tariffDetailsObjs as $tariffDetailsObj) {
                    if(!in_array($tariffDetailsObj->getToZoneId(),$zoneIds)) {
                        $zoneIds[] =  $tariffDetailsObj->getToZoneId();
                    }
                }
                sort($zoneIds);
                $sql = "select
                            min(td.`weight_from`) as weight_from,
                            max(td.`weight_to`) as weight_to,
                            min(td.`weight_to` - td.`weight_from`) as step 
                        from
                            `tariffs_details` td
                        where td.`tariffs_id` = ".$tariffId;
                $tariffDetailWeight = TariffsDetails::getTariffsDetailsListFromSql($sql);
                $csvData = [];
                if(count($tariffDetailWeight)) {
                    $startWeight = $tariffDetailWeight[0]->getWeightFrom();
                    $endWeight = $tariffDetailWeight[0]->getWeightTo();
                    $step = $tariffDetailWeight[0]->getStep();
                    while ($startWeight < $endWeight) {
                        $from = $startWeight;
                        $startWeight += $step;
                        $to = $startWeight;
                        $weight = $from . "/" . $to;
//                        if($startWeight < $endWeight) {
                        foreach($zoneIds as $zoneId) {
                            $tariffDetails = new TariffsDetailsFilter();
                            $tariffDetails->addFieldFilter("    td.tariffs_id", $tariffId);
                            $tariffDetails->addFieldFilter("    td.to_zone_id", $zoneId);
                            $tariffDetails->addFilter("    td.weight_from <= " .$from. " AND ".$to." <= td.weight_to");
                            $tariffDetails->setLimit("-1");
                            $tariffDetailsObjs = $tariffDetails->getList();
                            if(count($tariffDetailsObjs)) {
                                $tariffDetailsObj = $tariffDetailsObjs[0];
                                $csvData[$weight][$zoneId] = [
                                    'weight_cost' => $tariffDetailsObj->getWeightCost(),
                                    'piece_cost' => $tariffDetailsObj->getPieceCost(),
                                    'formula' => $tariffDetailsObj->getFormula()
                                ];
                            } else {
                                $csvData[$weight][$zoneId] = [
                                    'weight_cost' => "",
                                    'piece_cost' => "",
                                    'formula' => ""
                                ];
                            }
                        }
//                        }
                    }
                }
                /*  Download Csv Code  */
                $fileName = "tariff_csv_" . time(). ".csv";
                if(count($csvData)) {
                    $returnString = "";
                    $returnString .= "weight/zone,";
                    foreach($zoneIds as $zoneId) {
                        $carrierZone = new CarrierZones($zoneId);
                        $returnString .= cleanCsvCall($carrierZone->getName()) . ",";
                    }
                    $returnString .= "formula,";
                    $returnString .=  "\r\n";

                    foreach($csvData as $weight => $zones) {
                        $returnString .= cleanCsvCall($weight) . ",";
                        $zone_id = '';
                        foreach($zones as $zoneId => $cost) {
                            $zone_id = $zoneId;
                            if($tariffObj->getTariffType() == 'customer')
                                $returnString .= cleanCsvCall($cost['weight_cost']) . "/" . cleanCsvCall($cost['piece_cost']) . ",";
                            else
                                $returnString .= cleanCsvCall($cost['weight_cost']).",";
                        }
                        $returnString .= cleanCsvCall($zones[$zone_id]['formula']) . ",";
                        $returnString .=  "\r\n";
                    }
                }
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=" . $fileName);
                header("Pragma: no-cache");
                header("Expires: 0");
                echo $returnString;
            }
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_tariffs_options') {
            $zones = [];
            $user = SessionManager::getUser();
            $account_id = $user->getUserAccountId();
            $tariffs_id = $this->form_vars['tariffs_id'];
            $options = '';

            $tariffsdetailsfilterObj = new tariffsdetailsfilter();
            $tariffsdetailsfilterObj->addJoinCarrierZone();
            $tariffsdetailsfilterObj->addFieldFilter("td.tariffs_id", $tariffs_id);
            $tariffsdetailsfilterObjs = $tariffsdetailsfilterObj->getColumnList("td.to_zone_id, td.weight_from, td.weight_to, td.weight_cost, td.piece_cost, cz.name");

            foreach ($tariffsdetailsfilterObjs as $tariffsdetailsfilterOb){
                if(!in_array($tariffsdetailsfilterOb->getName(), $zones)){
                    $zones[]= $tariffsdetailsfilterOb->getName();
                }
            }

            $tariffsFilterObj = new TariffsFilter;
            $tariffsFilterObj->addTariffsDetailsJoin();
            $tariffsFilterObj->addJoinCarrierZone();
            $tariffsFilterObj->addFilter('t.user_account_id' . "='" . DbAccess3::escape($account_id) . "'");
            $tariffsFilterObj->addFilter('t.id' . " <> '".$tariffs_id."'");
            $tariffsFilterObj->addFilterIn('cz.name', $zones);
            $tariffsFilterObj->addGroupBy('t.id');
            $tariffsFilterObjs = $tariffsFilterObj->getList(" DISTINCT t.id, t.name");
            $doneIds = [];
            if(count($tariffsFilterObjs)) {
                foreach($tariffsFilterObjs as $tariffsFilterObj) {
                    $id = $tariffsFilterObj->getId();
                    $name = ucfirst(strtolower($tariffsFilterObj->getName()));
                        if ($name == "")
                            continue;

                        $options .= '<option value="' . $id . '" >' . $name . '</option>';

                }
            }
            $return = [
                'count' => count($tariffsFilterObjs),
                'status' => 'success',
                'options' => $options,
            ];
            echo json_encode($return);
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_tariffs_groups') {

            $options = '<option value="">Please select Group</option>';
            $carrier_id = $this->form_vars['carrier_id'];
            //$options = Ddl::generateDDL('group[]', 'RemoteareasGroupsFilter','AND carrier_id = '.DbAccess3::escape($carrier_id).' ', 'group_name', 'id', '', ' class="validate_checks  form-control select2 remotearea_group"', "Please select Group", "", "group", "Group");

            $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
            $remoteareasGroupsFilter->addFieldFilter('   carrier_id', $carrier_id);
            $remoteareasGroupsFilterObj = $remoteareasGroupsFilter->getColumnList('*');
            if(count($remoteareasGroupsFilterObj) > 0) {
                foreach($remoteareasGroupsFilterObj as $groups) {
                    if (!empty($groups->getGroupName()))
                    $options .= '<option value="'.$groups->getId().'">'.$groups->getGroupName().'</option>';
                }
            }

            $return = [
                'status' => 'success',
                'options' => $options,
            ];
            echo json_encode($return);
            die;

        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_tariff_excelssss') {
            $tariffId = $this->form_vars['tariffId'];
            $clientName = $this->form_vars['client_name'];
            $issueDate = $this->form_vars['issue_date'];
            $expireDate = $this->form_vars['expire_date'];
            $dimDiscription = $this->form_vars['dim_discription'];

            $tariff = new tariffs($tariffId);
            $tariffDetails = new TariffsDetailsFilter();
            $tariffDetails->addFieldFilter("    td.tariffs_id", $tariffId);
            $tariffDetails->setLimit("-1");
            $tariffDetailsObjs = $tariffDetails->getList();
            $data = [];
            $zoneIds = [];
            $endWeight = 0.000;
            foreach($tariffDetailsObjs as $tariffDetailsObj) {
                $toWeight = $tariffDetailsObj->getWeightTo();
                if($toWeight > $endWeight) {
                    $endWeight = $toWeight;
                }
                $data[$tariffDetailsObj->getWeightTo()][$tariffDetailsObj->getToZoneId()] = [
                    'weight_cost' => $tariffDetailsObj->getWeightCost(),
                    'piece_cost' => $tariffDetailsObj->getPieceCost(),
                    'formula' => $tariffDetailsObj->getFormula()
                ];
                if(!in_array($tariffDetailsObj->getToZoneId(),$zoneIds)) {
                    $zoneIds[] =  $tariffDetailsObj->getToZoneId();
                }
                ksort($data[$tariffDetailsObj->getWeightTo()]);
            }
            sort($zoneIds);
            $heading = [];
            if(count($zoneIds)) {
                $heading[] = "weight";
                foreach($zoneIds as $zoneId) {
                    $carrierZone = new CarrierZones($zoneId);
                    $heading[] = cleanCsvCall($carrierZone->getName());
                }
            }

            $bold = array(
                'font' => array(
                    'bold' => true,
                )
            );
            $styleWeightColoumn = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'eaeaea')
                ),
            );
            $styleZoneReport = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 12,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '5f5f7d')
                ),
            );
            $styleServiceForReport = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 16,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '1D2555')
                ),
            );
            $styleForReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            $styleEndReport = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Calibri',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
            );
            if (!empty($data)) {
                $currency = new Currency($tariff->getCurrencyId());
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);
                // IMAGES
                $logo = User::getUserCompanyImages(true);
                if(!empty($logo) && file_exists($logo)){
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('Company Logo');
                    $objDrawing->setDescription('Company Logo');
                    $objDrawing->setPath($logo);
                    $objDrawing->setCoordinates('D1');
                    //setOffsetX works properly
                    $objDrawing->setOffsetX(5);
                    $objDrawing->setOffsetY(5);
                    //set width, height
                    $objDrawing->setWidth(1000);
                    $objDrawing->setHeight(100);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }

                $objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleZoneReport);
                $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($styleZoneReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A4', "Client Name");
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', "Date Issued");
                $objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($styleForReport);
                $objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($styleForReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('B4', $clientName);
                $objPHPExcel->getActiveSheet()->SetCellValue('B5', $issueDate);

                $rowNum = 9;
                $colNum = 'A';
                $mergCellFrom = $colNum.$rowNum;
                foreach ($heading as $h) {
                    $cell_name = $colNum.$rowNum;
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleZoneReport);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($colNum)->setWidth(16);
                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                    $colNum++;
                }
                $ascii = $this->getColNo($colNum);
                $colNum = $this->getLetter($ascii);
                $objPHPExcel->getActiveSheet()->mergeCells('A7:'.$colNum.'8');
                $objPHPExcel->getActiveSheet()->getStyle('A7:'.$colNum.'8')->applyFromArray($styleServiceForReport);
                $service = new Services($tariff->getServiceId());
                $objPHPExcel->getActiveSheet()->SetCellValue('A7', $service->getName());
                $rowNum=10;
                foreach($data as $toWeight => $zones) {
                    $colNum = 'A';
                    $zone_id = '';
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleWeightColoumn);
                    $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $toWeight);
                    $colNum++;
                    foreach($zones as $zoneId => $cost) {
                        $formula = $cost['formula'];
                        $weightCost = $cost['weight_cost'];
                        $pieceCost = $cost['piece_cost'];
                        $totalCost = $weightCost;
                        if(!empty($formula)){
                            $findArr = ['Q','ITMCHR','REG','W','CHRG'];
                            $replaceArr = ['1',$pieceCost,'0',$toWeight,$weightCost];
                            $frmla = str_replace($findArr,$replaceArr,$formula);
                            eval('$totalCost = '.$frmla.';');
                        }
                        $zone_id = $zoneId;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $totalCost); //$cost['weight_cost']
                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getNumberFormat()->setFormatCode('"'.html_entity_decode($currency->getLeftsymbolcode()).'" #,##0.00');
                        $colNum++;
                    }
                    $rowNum++;
                }

                $count = $rowNum;
                $userAccount = new CustomerAccount($this->user->getUserAccountId());
                $companyName = (!empty($userAccount)?$userAccount->getCompany():'');
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, '© '.$companyName);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $count.":F" . $count)->applyFromArray($bold);
                $count = $count + 2;
                $serviceDesc = "Tracked Solution EX NJ (provides full end to end tracking and online delivery confirmation)";
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $serviceDesc);
                $count = $count + 2;

                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':F'.$count);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$count.':F'.$count)->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Maximum  Dimensions & Weight Europe");
                $count++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':D'.$count);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$count.':D'.$count)->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Dimensions");
                $objPHPExcel->getActiveSheet()->mergeCells('E'.$count.':F'.$count);
                $objPHPExcel->getActiveSheet()->getStyle('E'.$count.':F'.$count)->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$count, "Weight");
                $count++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':D'.($count+4));
                $objPHPExcel->getActiveSheet()->getStyle('A'.$count.':D'.($count+4))->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, $dimDiscription);
                $objPHPExcel->getActiveSheet()->mergeCells('E'.$count.':F'.($count+4));
                $objPHPExcel->getActiveSheet()->getStyle('E'.$count.':F'.($count+4))->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$count, "up to ".$endWeight." KG");
                $count = $count + 5;

                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "The above rates are based on pre-labeled shipments as per One World Routings");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "The rates offered are based on an average volume and send profile.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "One World Express reserve the right to revise these rates should this profile change adversely.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Rates are valid untill ".$expireDate." unless revoked earlier with 30 days notice.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Rates are subject to VAT if applicable.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Rates needs to be accepted within a maximum 30 days from date of issued.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Subject to the One World Express Terms and Conditions.");
                $objPHPExcel->getActiveSheet()->getStyle('A'. $count)->applyFromArray($bold);
                $count = $count+2;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Tariff Acceptance");
                $objPHPExcel->getActiveSheet()->getStyle('A'. $count)->applyFromArray($bold);
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Date");
                $objPHPExcel->getActiveSheet()->getStyle('A'. $count)->applyFromArray($bold);
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Name of the Company: ");
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$count, "One World Representative:  ");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Position:");
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$count, "Position: ");
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $count++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$count.':C'.$count);
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$count.':F'.$count);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Name:");


                $fileName = "tariff_excel_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
            }
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'saveTariffAdditionalCharges') {
            $output = [];
            //$tariffId = $this->form_vars['tariff_id'];
            $chargesTypeId = $this->form_vars['chargesTypeId'];
            $charges = $this->form_vars['charges'];
            $chargesType = $this->form_vars['chargesType'];
            $zone_ids = $this->form_vars['zone_ids'];
            $formulas = $this->form_vars['formula'];
            $tariffAdditionalChargesIds = $this->form_vars['tariff_additional_charges_id'];

            $filterData = SessionManager::getAdditionalChargesFilter();
            $tariffName = $filterData['name'];
            if (empty($tariffName)){
                $output["status"] = "error";
                $output["message"] = "Tariff name can not be empty.";
                echo json_encode($output);
                die;
            }
            if ($chargesTypeId <= 0) {
                $output['status'] = "error";
                $output['message'] = "Charges type is not valid";
                echo json_encode($output);
                die();
            }
            if ($charges <= 0) {
                $output['status'] = "error";
                $output['message'] = "Charges must be greater than zero";
                echo json_encode($output);
                die();
            }
            $tariffsFilter = new TariffsFilter();
            $carrierId = $filterData['carrier_id'];
            if (!empty($carrierId))
                $tariffsFilter->addFieldFilter('   t.carrier_id', $carrierId);

            $serviceId = $filterData['service_id'];
            if (!empty($serviceId))
                $tariffsFilter->addFieldFilter('  t.service_id', $serviceId);

            $tariffName = $filterData['name'];
            if (!empty($tariffName))
                $tariffsFilter->addFieldLikeFilter('  t.name', $tariffName);

            $startDate = $filterData['start_date'];
            if (!empty($startDate))
                $tariffsFilter->addStartDateFilter($startDate);

            $endDate = $filterData['end_date'];
            if (!empty($endDate))
                $tariffsFilter->addEndDateFilter($endDate);

            $fromZone = $filterData['from_zone'];
            if (!empty($fromZone)) {
                $tariffsFilter->addTariffsDetailsJoin();
                $tariffsFilter->addFieldFilter('    td.from_zone_id', $fromZone);
                $this->filerColumn = 'DISTINCT t.*, td.`from_zone_id`';
            }

            $isActive = $filterData['status'];
            if ($isActive == 1) {
                $tariffsFilter->addFieldFilter('   t.status', $isActive);
            }
            if ($isActive == '0') {
                $tariffsFilter->addFieldFilter('   t.status', $isActive);
            }
            $isType = $filterData['type'];
            if (!empty($isType)) {
                $tariffsFilter->addFieldFilter('   t.tariff_type', $isType);
            }
            $tariffData = $tariffsFilter->getList(' id');
            if (!empty($tariffData)) {
                foreach ($tariffData as $tariff) {
                    $tariffId = $tariff->getId();
                    if ($tariffId <= 0) {
                        $output['status'] = "error";
                        $output['message'] = "Tariff is invalid, Please select tariff from dropdown and try again.";
                        echo json_encode($output);
                        die();
                    }

                    if(is_array($charges)) {
                        foreach($charges as $key => $charge) {
                            $chargesType = $chargesType[$key];
                            $zoneId = '';
                            $formula = '';
                            $tariffAdditionalChargesId = isset($tariffAdditionalChargesIds[$key]) ? $tariffAdditionalChargesIds[$key] : "";
                            $this->saveTariffAdditionalCharges($tariffId,$chargesTypeId,$charge,$chargesType,$formula,$zoneId,$tariffAdditionalChargesId);
                        }
                    } else {
                        $tariffAdditionalChargesId = $this->form_vars['tariff_additional_charges_id'];
                        $this->saveTariffAdditionalCharges($tariffId,$chargesTypeId,$charges,$chargesType,'','',$tariffAdditionalChargesId);
                    }
                }
            }else {
                $output['status'] = "error";
                $output['message'] = "No data found.";
                echo json_encode($output);
                die();
            }

            $output['status'] = "success";
            $output['message'] = "Charges Save successfully";
            echo json_encode($output);
            die();
        }
    }


    function allow_keys($arr, $keys)
    {
        $saved = [];
        foreach ($keys as $key => $value) {
            if (is_int($key) || is_int($value)) {
                $keysKey = $value;
            } else {
                $keysKey = $key;
            }
            if (isset($arr[$keysKey])) {
                $saved[$keysKey] = $arr[$keysKey];
                if (is_array($value)) {
                    $saved[$keysKey] = allow_keys($saved[$keysKey], $keys[$keysKey]);
                }
            }
        }
        return $saved;
    }

    function numberFormat($number,$decimalPoints = 2){
        return number_format($number,$decimalPoints,'.','');
    }

    function getColNo($colLetters){
        $limit = 5; //apply max no. of characters
        $colLetters = strtoupper($colLetters); //change to uppercase for easy char to integer conversion
        $strlen = strlen($colLetters); //get length of col string
        if($strlen > $limit)return "Column too long!"; //may catch out multibyte chars in first pass
        preg_match("/^[A-Z]+$/",$colLetters,$matches); //check valid chars
        if(!$matches)return "Invalid characters!"; //should catch any remaining multibyte chars or empty string, numbers, symbols
        $it = 0; $vals = 0; //just start off the vars
        for($i=$strlen-1;$i>-1;$i--){ //countdown - add values from righthand side
            $vals += (ord($colLetters[$i]) - 64 ) * pow(26,$it); //cumulate letter value
            $it++; //simple counter
        }
        return $vals - 1; //this is the answer
    }

    function getLetter($c){
        $c = intval($c);
        if ($c <= 0)  {
            return '';
        }
        $letter = '';
        while($c != 0){
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }
        return $letter;
    }

    protected function getValidZoneFromTariffId($tariffId) {
        $tariffs = new Tariffs($tariffId);
        /* Get zone base */
        $carrierId = $tariffs->getCarrierId();
        $carrierObj = new Carrier($carrierId);
        $carrierZoneBase = $carrierObj->getZoneBase();
        /* Get valid zone */
        $carrierZonesFilter = new CarrierZonesFilter();
        $carrierZonesFilter->addFieldFilter('     cz.status', 1);
        $carrierZonesFilter->addFieldFilter('    cz.carrier_id', $carrierId);
        $carrierZonesFilter->addJoin('tariffs_details td', "cz.id = td.to_zone_id");
        if ($carrierZoneBase == 0) {
            $carrierZonesFilter->addFieldFilter('cz.service_id', $tariffs->getServiceId());
        }
        $carrierZonesFilter->AddOrderBy('cz.sort_order, cz.name+0');
        $carrierZones = $carrierZonesFilter->getColumnList('cz.id, cz.name');
        $validZone = [];
        if (count($carrierZones) > 0) {
            foreach ($carrierZones as $carrierZone) {
                if(!in_array($carrierZone->getId(),$validZone)) {
                    $validZone[] = $carrierZone->getId();
                }
            }
        }
        return $validZone;
    }
    protected function getValidZonesFromTariffId($tariffId) {
        $tariffsDetailsFilter = new TariffsDetailsFilter();
        $tariffsDetailsFilter->addFieldFilter("    td.tariffs_id", $tariffId);
        $tariffsDetailsFilter->addJoinCarrierZone();
        $tariffsDetailsFilter->addOrderBy("      cz.sort_order");
        $tariffsDetailsFilter->setLimit("-1");
         $carrierZones = $tariffsDetailsFilter->getList();
        //$carrierZones = $tariffsDetailsFilter->getColumnList('cz.id, cz.name');
        $validZone = [];
        if (count($carrierZones) > 0) {
            foreach ($carrierZones as $carrierZone) {
                if(!in_array($carrierZone->getId(),$validZone)) {
                    $validZone[$carrierZone->getId()] = $carrierZone->getName();
                }
            }
        }
        return $validZone;
    }
    protected function deleteUserData($user_id, $tariff_id)
    {
        $ComparePricingFilterObj =  new ComparePricingFilter();
        $ComparePricingFilterObj->where(['tariff_id'=>$tariff_id, 'user_id'=>$user_id]);
        return $ComparePricingFilterObjs = $ComparePricingFilterObj->delete();
    }
    protected  function compareTariffToTariffs($tariffId, $tariffIds, $weightsToArray, $zoneIds, $tariffsdetailsfilterObjs) {
            $mainArray = $currentArr = $mainWeights = $ranges = $returnArr = $weightsArr = [];
            $maxWeightTo = max($weightsToArray);
            $sql = "SELECT td.*, t.`name`, cz.name as zone_name
                    FROM
                    `tariffs_details` td
                    LEFT JOIN `tariffs` t ON td.`tariffs_id` = t.`id`
                    JOIN `carrier_zones` cz  ON td.to_zone_id = cz.id  AND cz.`status` != 2
                    WHERE td.`tariffs_id` IN(".$tariffIds.") AND LOWER(cz.`name`) IN(".$zoneIds.") ORDER BY zone_name, td.weight_from, td.weight_to ASC";
            $comparePricingFilterObjs = TariffsDetails::getTariffsDetailsListFromSql($sql);

            foreach ($comparePricingFilterObjs as $comparePricingFilterObj) {
                $weightFrom  = $comparePricingFilterObj->getWeightFrom();
                $weightTo  = $comparePricingFilterObj->getWeightTo();
                if(!in_array($weightFrom, $mainWeights)){
                    $mainWeights[] = $weightFrom;
                }
                if(!in_array($weightTo, $mainWeights)){
                    $mainWeights[] = $weightTo;
                }
            }

            if (count($weightsToArray) == 2 && count($mainWeights) >= 2) {
                $mainMaxWeight = max($mainWeights);
                $baseWeightDiff = $weightsToArray[1]-$weightsToArray[0];
                $mainWeightDiff = $mainWeights[1]-$mainWeights[0];
                if ($baseWeightDiff < $mainWeightDiff) {
                    $weightsArr = $weightsToArray;
                    $i = $weightsToArray[0];
                    $j = 0;
                    while ($i < $maxWeightTo) {
                        $j = $i + $baseWeightDiff;
                        $ranges[] = $i."-".$j;
                        $i = $i + $baseWeightDiff;
                    }
                }else {
                    $weightsArr = $mainWeights;
                    $i = $mainWeights[0];
                    while ($i < $maxWeightTo) {
                        $j = $i + $mainWeightDiff;
                        $ranges[] = $i."-".$j;
                        $i = $i + $mainWeightDiff;
                    }
                }
            }else {
                $i = 0;
                $j = 1;
                foreach ($weightsToArray as $weighToArr){
                    if (!empty($weightsToArray[$j])){
                        $ranges[] = $weightsToArray[$i]."-".$weightsToArray[$j];
                        $i++;
                        $j++;
                    }
                }
            }
            foreach ($comparePricingFilterObjs as $ComparePricingFilterObj) {
                $currentWeightTo = $ComparePricingFilterObj->getWeightTo();
                if (count($weightsArr) >= 2 && $currentWeightTo <= $maxWeightTo) {
                    foreach ($mainWeights as $mainWeightTo) {
                        if ($currentWeightTo >= $mainWeightTo) {
                            $currentArr = $ComparePricingFilterObj;
                        }else {
                            $currentArr = $ComparePricingFilterObj;
                        }
                    }
                    array_push($mainArray, $currentArr);
                }else {
                    foreach ($mainWeights as $mainWeightTo) {
                        if ($currentWeightTo >= $mainWeightTo) {
                            $currentArr = $ComparePricingFilterObj;
                        }else {
                            $currentArr = $ComparePricingFilterObj;
                        }
                    }
                    array_push($mainArray, $currentArr);
                }
            }

        $returnArr['weights_'] = $ranges;
        $returnArr['weight_ranges'] = $ranges;
        $returnArr['main_array'] = $mainArray;
        return $returnArr;
    }
    protected function saveTariffAdditionalCharges($tariffId,$chargesTypeId,$charges,$chargesType,$formula = '',$zoneId = '',$tariffAdditionalChargesId = "") {
        $date_added = time();
        $added_by = $this->user->getId();
        $date_update = time();
        $update_by = $this->user->getId();
        if (!empty($tariffAdditionalChargesId)) {
            $userAccountServicesCharges = new TariffAdditionalCharges($tariffAdditionalChargesId);
        } else {
            $userAccountServicesCharges = new TariffAdditionalCharges();
        }
        $userAccountServicesCharges->setTariffId($tariffId);
        $userAccountServicesCharges->setConsignmentChargesTypesId($chargesTypeId);
        $userAccountServicesCharges->setCharge($charges);
        $userAccountServicesCharges->setChargeType($chargesType);
        if($formula != "") {
            $userAccountServicesCharges->setFormula($formula);
        }
        if($zoneId != "") {
            $userAccountServicesCharges->setZoneId($zoneId);
        }
        $userAccountServicesCharges->setAddedBy($added_by);
        $userAccountServicesCharges->setAddedDate($date_added);
        $userAccountServicesCharges->setUpdatedBy($update_by);
        $userAccountServicesCharges->setUpdatedDate($date_update);
        $userAccountServicesCharges->save();
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

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/css/components-rounded.min.css" rel="stylesheet" type="text/css" />

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
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>


        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var carrier_id = $("#get_selected_carrier_id").val();
                    var service_id = $("#get_selected_service_id").val();
                    var datatableurl = "tariffs_list.php?action=tariffs_ajax";
                    if((carrier_id.length > 0) && (service_id.length > 0)) {
                        datatableurl = "tariffs_list.php?action=tariffs_ajax&carrier_id=" + carrier_id + "&service_id=" + service_id;
                    } else {
                        if(carrier_id.length > 0) {
                            datatableurl = "tariffs_list.php?action=tariffs_ajax&carrier_id=" + carrier_id;
                        }
                        else if(service_id.length > 0) {
                            datatableurl = "tariffs_list.php?action=tariffs_ajax&service_id=" + service_id;
                        }
                    }
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {
                            // here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "carrier_id"},
                                {"data": "service_id"},
                                {"data": "name"},
                                {"data": "from_zone"},
                                {"data": "start_date"},
                                {"data": "end_date"},
                                {"data": "tariff_type"},
                                {"data": "status"}
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
            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $('#manage-data-table button.filter-submit').click();
                $('.multiselect_drop_down').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    afterInit: function(ms) {
                        var that = this,
                            $selectableSearch = that.$selectableUl.prev(),
                            $selectionSearch = that.$selectionUl.prev(),
                            selectableSearchString = '#' + that.$container.attr('id') +
                                ' .ms-elem-selectable:not(.ms-selected)',
                            selectionSearchString = '#' + that.$container.attr('id') +
                                ' .ms-elem-selection.ms-selected';
                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                            .on('keydown', function(e) {
                                if (e.which === 40) {
                                    that.$selectableUl.focus();
                                    return false;
                                }
                            });

                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                            .on('keydown', function(e) {
                                if (e.which == 40) {
                                    that.$selectionUl.focus();
                                    return false;
                                }
                            });
                    },
                    afterSelect: function(values) {
                        this.qs1.cache();
                        this.qs2.cache();
                    },
                    afterDeselect: function(values) {
                        this.qs1.cache();
                        this.qs2.cache();
                    }
                });
            });
            $(document).on('click', ".filter-cancel", function(){
                $('#manage-data-table button.filter-submit').click();
            });
            $(document).on('click', '.btndelete', function () {
                var tariffId = $(this).attr("data-tariff_id");
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
                            $.ajax({
                                type: "POST",
                                url: "tariffs_list.php",
                                data: {action: "delete", tariff_id: tariffId},
                                dataType: "json",
                                success: function (data) {
                                    if (data.STATUS == "success") {
                                        swal("Success!", "<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>", "success");
                                        $(".scroll-to-top").click();
                                        grid.getDataTable().ajax.reload();
                                    } else {
                                        swal("Sorry!", "something went wrong", "error");
                                    }
                                },
                                error: function () {
                                    swal("Sorry!", "something went wrong", "error");
                                }
                            });
                        }
                    });
            });
            $(document).on('click', '#csv_download_btn', function () {
                $('#csv_download_form').submit();
            });
            $(document).on('click', '#download_tariff_csv', function () {
                var tariffId = $(this).data('id');
                $('#tariff_id_detail_download').val(tariffId);
                $('#download_tariff_detail_form').submit();
            });
            $(document).on('click', '.download_tariff_template', function () {
                var type = $(this).data('type');
                var tariffId = '';
                var tariffZoneType = '';
                if(type == "compareTariff") {
                    tariffId = $('#compare_pricing_tariff_id').val();
                    tariffZoneType = $('#compare_pricing_tariff_zone_type').val();
                } else {
                    tariffId = $('#upload_tariff_id').val();
                    tariffZoneType = $('#upload_tariff_zone_type').val();
                }
                $('#dounload_csv_tariffId').val(tariffId);
                $('#dounload_csv_tariffZoneType').val(tariffZoneType);
                $('#download_tariff_template_form').submit();
            });
            $(document).on('click', '#show_upload_csv_modal', function () {
                var tariffId = $(this).data('id');
                var tariffZoneType = $(this).data('tariff_zone_type');
                $('#upload_tariff_id').val(tariffId);
                $('#upload_tariff_zone_type').val(tariffZoneType);
                $('#tariff_upload_csv_modal').modal('show');
            });
            $(document).on('click', '#compare_pricing_modal', function () {
                clear_compare_pricing_tariff_console();
                $("#compare_with_csv").attr('checked', 'checked');
                $("#compare_pricing_row").show();
                $("#compare_tariffs_row").hide();
                $("#btnSubmitCompareTariffs").hide();
                $("#btnSubmitCompare").show();
                $("#download_compare_tariff_excel_btn").hide();
                $("#download_compare_tariff_to_tariff_excel_btn").hide();
                $("#compareTariffPricingExcelForm")[0].reset();
                var tariffId = $(this).data('id');
                var tariffZoneType = $(this).data('tariff_zone_type');
                $('#compare_pricing_tariff_id').val(tariffId);
                $('#compare_pricing_tariff_zone_type').val(tariffZoneType);
                $('#compare_pricing_csv_modal').modal('show');
            });
            $(document).on('click', 'input[type="radio"][name="compare_checkbox"]', function () {
                var selected = $('input[type="radio"][name="compare_checkbox"]:checked').val();
                if (selected != undefined && selected != ''){
                    if (selected == 't'){
                        $("#compare_pricing_row").hide();
                        $("#compare_tariffs_row").show();
                        $("#btnSubmitCompareTariffs").show();
                        $("#btnSubmitCompare").hide();
                        //$("#download_compare_tariff_excel_btn").hide();
                        //$("#download_compare_tariff_to_tariff_excel_btn").show();
                    }else{
                        $("#compare_pricing_row").show();
                        $("#compare_tariffs_row").hide();
                        $("#btnSubmitCompareTariffs").hide();
                        $("#btnSubmitCompare").show();
                        //$("#download_compare_tariff_to_tariff_excel_btn").hide();
                        //$("#download_compare_tariff_excel_btn").show();

                    }
                }

            });
            $(document).on('click', '#btnSubmitCompareTariffs', function (event){
                event.preventDefault();
                var form_data = new FormData();
                var radio_type = $('input[type="radio"][name="compare_checkbox"]:checked').val();
                form_data.append('radioType', radio_type);
                if (radio_type != undefined && radio_type != ''){
                    if (radio_type == 't'){
                        form_data.append('func', 'compare_tariff_to_tariff');
                        var tariff_names = $("#tariff_name").val();
                        if (tariff_names == null) {
                            swal("error", "Please select atleast one tariff!","error");
                            return false;
                        }
                        form_data.append('tariffNames', tariff_names);
                    }
                }
                var tariffId = $('#compare_pricing_tariff_id').val();
                var tariffZoneType = $('#compare_pricing_tariff_zone_type').val();

                form_data.append('tariffId', tariffId);
                form_data.append('tariffZoneType', tariffZoneType);
                if (tariffId == "" || tariffId == null) {
                    swal("error", "Your tariff id not found!","error");
                } else {
                    if (tariffId > 0) {
                        $('#compare_tariff_console_window').append("<br /><span style='color:#4bfbca;'>Uploading CSV file ....</span>");
                        $('#compare_tariff_console_window').show();
                        $.ajax({
                            url: 'tariffs_list.php',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                if(response.status == "success") {
                                    $("#btnSubmitCompareTariffs").hide();
                                    $("#download_compare_tariff_to_tariff_excel_btn").show();
                                    $('#compare_tariff_console_window').append("<br /><span style='color:#4bfbca;'>File upload successfully</span>");
                                    $('#compare_tariff_console_window').append("<br /><span style='color:#4bfbca;'>Comparing CSV with tariff ....</span>");
                                    var tariff_id = response.tariffId;
                                    var files = response.data.headings;
                                    var zones = response.data.zones;
                                    $("#tariff_to_tariff_id").val(tariff_id);
                                    $("#tariff_zones").val(zones);
                                    $("#compare_tariffs").val(files);
                                } else {
                                    $('#compare_tariff_console_window').append("<br /><span style='color:#f97878;'>"+response.message+"</span>");
                                }
                            }
                        });
                    } else {
                        $("#btnSubmitCompare").show();
                        swal("Sorry!", "Please select the tariff first", "error");
                    }
                }
            });
            $(document).on('click', '#btnSubmitImport', function () {
                var file_data = $('#tariff_upload_file').prop('files')[0];
                var tariffId = $('#upload_tariff_id').val();
                var tariffZoneType = $('#upload_tariff_zone_type').val();
                var form_data = new FormData();
                form_data.append('csv_file', file_data);
                form_data.append('func', 'upload_csv_file');
                form_data.append('tariffId', tariffId);
                form_data.append('tariffZoneType', tariffZoneType);
                if (tariffId == "" || tariffId == null) {
                    swal("error", "Your tariff id not found!","error");
                } else {
                    if (tariffId > 0) {
                        $.ajax({
                            url: 'tariffs_list.php',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                $('#upload_carrier_zone_console_window').append(response.message);
                                $('#upload_carrier_zone_console_window').show();
                            }
                        });
                    } else {
                        $("#btnSubmitImport").show();
                        swal("Sorry!", "Please select the user first", "error");
                    }
                }
            });

            $(document).on('click', '#tariff_compare_upload_file', function () {
                clear_compare_pricing_tariff_console();
            });

            $(document).on('click', '#btnSubmitCompare', function (event){
                event.preventDefault();
                var form_data = new FormData();
                var radio_type = $('input[type="radio"][name="compare_checkbox"]:checked').val();
                form_data.append('radioType', radio_type);

                if (radio_type != undefined && radio_type != ''){
                    if (radio_type == 't'){
                        form_data.append('func', 'compare_csv_file_upload');
                        var tariff_names = $("#tariff_name").val();
                        if (tariff_names == null) {
                            swal("error", "Please select atleast on tariff!","error");
                            return false;
                        }
                        form_data.append('tariffNames', tariff_names);
                    }else{
                        form_data.append('func', 'compare_csv_file_upload');
                        var file_data = $('#tariff_compare_upload_file').prop('files');
                        if(file_data.length == 0) {
                            swal("error", "File/s not selected!","error");
                            return;
                        }
                        for (var x = 0; x < file_data.length; x++) {
                            form_data.append("csv_file[]", file_data[x]);
                        }
                    }
                }
                var tariffId = $('#compare_pricing_tariff_id').val();
                var tariffZoneType = $('#compare_pricing_tariff_zone_type').val();

                form_data.append('tariffId', tariffId);
                form_data.append('tariffZoneType', tariffZoneType);
                if (tariffId == "" || tariffId == null) {
                    swal("error", "Your tariff id not found!","error");
                } else {
                    if (tariffId > 0) {
                        $('#compare_tariff_console_window').append("<br /><span style='color:#4bfbca;'>Uploading CSV file ....</span>");
                        $('#compare_tariff_console_window').show();
                        $.ajax({
                            url: 'tariffs_list.php',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                if(response.status == "success") {
                                    //$("#btnSubmitCompare").hide();
                                    $("#download_compare_tariff_excel_btn").show();
                                    $('#compare_tariff_console_window').append("<br /><span style='color:#4bfbca;'>File upload successfully</span>");
                                    $('#compare_tariff_console_window').append("<br /><span style='color:#4bfbca;'>Comparing CSV with tariff ....</span>");
                                    var tariff_id = response.tariffId;
                                    var files = response.data.headings;
                                    var zones = response.data.zones;
                                    $("#tariff_id").val(tariff_id);
                                    $("#compare_zones").val(zones);
                                    $("#compare_files").val(files);
                                } else {
                                    $('#compare_tariff_console_window').append("<br /><span style='color:#f97878;'>"+response.message+"</span>");
                                }
                            }
                        });
                    } else {
                        $("#btnSubmitCompare").show();
                        swal("Sorry!", "Please select the tariff first", "error");
                    }
                }
            });
            
            
            
            $(document).on('click', '#assign_tariff_account_btn', function () {
                var tariff_id = $('#selected_tariff').val();
                var form_data = new FormData();
                form_data.append('func', 'user_account_for_tariff_add');
                form_data.append('tariff_id', tariff_id);
                form_data.append('user_account_id', $('#user_account_id').val());
                $.ajax({
                    url: 'tariffs_list.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $('#assign-tariff-account-'+tariff_id).click();
                    }
                });
                
            });
            $(document).on('click', '.assign-tariff-account', function () {
                
                var tariffId = $(this).data('id');
                var form_data = new FormData();
                form_data.append('func', 'user_account_for_tariff_list');
                form_data.append('tariffId', tariffId);
                $('#selected_tariff').val(tariffId);
                $('#show-user-account-for-tariff').html('');
                $.ajax({
                    url: 'tariffs_list.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if(response.status == "success") {
                            //$("#btnSubmitCompare").hide();
                            var accountsdata = response.data
                            $("#show-user-account-for-tariff").show();
                            $.each(accountsdata, function() {
                                $('#show-user-account-for-tariff').append(
                                
                                    '<div class="col-md-3 light bordered">'
                                        +'<a href="javascript:;" class="icon-btn unassigned_tariff_'+this.id+'">'
                                            +'<i class="fa fa-group"></i>'
                                            +'<div> '+this.user_account+'   </div>'
                                            +'<span class="badge badge-danger unassigned-tariff" data-id="'+this.id+'"> x </span>'
                                        +'</a>'
                                 
                                    +'</div>'
                                );
                            });

                           // $('#show-user-account-for-tariff').append("<br /><span style='color:#4bfbca;'>File upload successfully</span>");
                          
                        } else {
                            $('#show-user-account-for-tariff').append("<div class='alert alert-danger'>"+response.message+"</div>");
                        }
                    }
                });
                $('#assign_tariff_account_modal').modal('show');
                
                // $('#assign_tariff_account_form').submit();
            });
            
            $(document).on('click', '.unassigned-tariff', function () {
                var tariffId = $(this).data('id');
                swal({
                        title: "<?php echo Translation::GetCaption("Are you sure to unassigned tariff?") ?>",
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
                                     
                                    $.ajax({
                                        url: 'tariffs_list.php',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {action: 'remove_assign_tariffs_from_account', tariff_id: tariffId},
                                        headers: {
                                        },
                                        success: function (response) {
                                            $(".unassigned_tariff_"+tariffId).remove();
                                            if(response.STATUS == 'success'){
                                                swal("Success!", response.MESSAGE, "success");
                                            } else {
                                                swal("Error!", response.MESSAGE, "error");
                                            }
                                        },
                                        error: function (xhr, status, error) {
                                            swal("Error", error, "error");
                                        }
                                    });

                                }
                            });
             
            });
            $(document).on('click', '#download_tariff_excel', function () {
                $('#download_tariffs_excel_file')[0].reset();
                $('#download_tariff_excel_modal').modal('show');
                var tariffId = $(this).data('id');
                var startDate = $(this).data('start_date');
                $('#issue_date').val(startDate);
                var endDate = $(this).data('end_date');
                $('#expire_date').val(endDate);
                $('#tariff_id_excel_download').val(tariffId);
            });

            $(document).on('click', '#download_tariff_excel_btn', function () {
                var clientName = $('#client_name').val();
                var issueDate = $('#issue_date').val();
                var expireDate = $('#expire_date').val();
                var dimDescription = $('#dim_description').val();
                $('#client_name_excel_download').val(clientName);
                $('#issue_date_excel_download').val(issueDate);
                $('#expire_date_excel_download').val(expireDate);
                $('#dim_description_excel_download').val(dimDescription);
                $('#download_tariff_excel_form').submit();
            });
            $(document).on('click', '#download_compare_tariff_excel_btn', function () {
                var clientName = $('#client_name').val();
                var issueDate = $('#issue_date').val();
                var expireDate = $('#expire_date').val();
                var dimDescription = $('#dim_description').val();
                $('#client_name_excel_download').val(clientName);
                $('#issue_date_excel_download').val(issueDate);
                $('#expire_date_excel_download').val(expireDate);
                $('#dim_description_excel_download').val(dimDescription);
                $('#download_compare_tariff_excel_form').submit();
            });
            $(document).on('click', '#download_compare_tariff_to_tariff_excel_btn', function () {
                $('#download_compare_tariff_to_tariff_excel_form').submit();
            });

            $(document).on('click', '.tariff_routing_download', function () {
                var tariff_id = $(this).data('tariff_id');
                var product_id = $(this).data('product_id');
                $('#routing_tariff_id').val(tariff_id);
                $('#cutomized_service_id_for_routing').val(product_id);
                $('#export_routine_tariff_frm').submit();
            });
            $(document).on('change', '#carrier_id', function () {
                var carrier_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "tariffs_list.php",
                    data: {func: "get_tariffs_groups", carrier_id: carrier_id},
                    dataType: "json",
                    success: function (data) {
                        if(data.status == "success") {
                            $('#groups').html(data.options);
                            $('#save_remoteareas_charges_for_suppliers').show();
                        }
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            });

            function apply_bluk_addtional_charges() {
                var form_data = [];
                var chargesTypeId = $("#chargesTypeId").val();
                var charges = $("#charges").val();
                var chargesType = $("#chargesType").val();
                form_data.push({name: "chargesTypeId", value: chargesTypeId});
                form_data.push({name: "charges", value: charges});
                form_data.push({name: "chargesType", value: chargesType});
                form_data.push({name: "action", value: "saveTariffAdditionalCharges"});
                $.ajax({
                    type: "POST",
                    url: "tariffs_list.php",
                    data: form_data,
                    dataType: "json",
                    success: function (data) {
                        if (data.status == "success"){
                            //$("#chargesTypeId").val('');
                            $("#charges").val('');
                            $("#oba_docket_modal").modal('hide');
                            grid.getDataTable().ajax.reload();
                            swal("Success!", data.message, "success");
                        }else{
                            //$("#oba_docket_modal").modal('hide');
                            swal("Error!", data.message, "error");
                        }

                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            function clear_compare_pricing_tariff_console() {
                $('#compare_tariff_console_window').html("");
                $('#compare_tariff_console_window').hide();
                $('#download_compare_excel_link').html("");
            }
            function getTariffsOption(tariffs_id) {
                $.blockUI();
                $.ajax({
                    type: "POST",
                    url: "tariffs_list.php",
                    data: {func: "get_tariffs_options", tariffs_id: tariffs_id},
                    dataType: "json",
                    success: function (data) {
                        $.unblockUI();
                        if(data.status == "success") {
                            if (data.count == 0)
                                $("#btnSubmitCompareTariffs").hide();

                            $('#tariff_name').html(data.options);
                            $('#tariff_name').multiSelect('refresh');
                        }
                    },
                    error: function () {
                        $.unblockUI();
                        //alert('error handing here');
                    }
                });
            }

            <?php require_once("../js/tariff-remotearea-charges.js"); ?>
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Tariff List
                </div>
                <div class="actions">
                    <?php if(Permissions::checkFilePermission('download_tariff_listing')) { ?>
                        <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>
                    <?php } ?>
                    <?php if(Permissions::checkFilePermission('add_new_tariff')) { ?>
                        <?php if(!empty($this->params)) { ?>
                            <a href="tariff_add.php?<?php echo $this->params; ?>" class="btn blue"  ><i class="fa fa-plus"></i> Add Tariff</a>
                        <?php } else { ?>
                            <a href="tariff_add.php" class="btn blue"><i class="fa fa-plus"></i> Add Tariff</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        $this->flashMsg->display();
                        ?>
                    </div>
                </div>
                <div class="actions" id="bluk_actions" style="">
                    <?php //if (Permissions::checkFilePermission('assign_oba_docket_number')) { ?>
                    <a href="javascript:;" data-toggle="modal" data-target="#oba_docket_modal" class="btn btn-default">
                        Tariff Additional Charges
                    </a>
                    <?php //} ?>

                    <?php //if (Permissions::checkFilePermission('bluk_billing_hold')) { ?>
                    <a href="javascript:;" data-toggle="modal" data-target="#tariff_remoteareas_modal" class="btn btn-default">
                        Tariff Remoteareas Charges
                    </a>
                    <?php //} ?>
                </div>
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                    <tr role="row" class="heading">
                        <th>Actions</th>
                        <th>Carrier Name</th>
                        <th>Carrier Service</th>
                        <th>Tariff Name</th>
                        <th>From Zone</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Type</th>
                        <th>Is Active</th>
                    </tr>
                    <tr role="row" class="filter">
                        <td>
                            <div class="margin-bottom-5">
                                <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                            </div>

                        </td>
                        <td class="user_acccount_correct_button">
                            <?php
                            $slectedCarrierId = '';
                            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                                $slectedCarrierId = $_GET['carrier_id'];
                            }
                            ?>
                            <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', $slectedCarrierId, 'id', ' class="bs-select input-sm form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body"'); ?>
                        </td>
                        <td class="user_acccount_correct_button">
                            <?php
                            $slectedServiceId = '';
                            if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                                $slectedServiceId = $_GET['service_id'];
                            }
                            ?>
                            <?php echo Ddl::generateServiceDDLWithImage('service_id', $slectedServiceId, 'id', ' class="bs-select input-sm form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body"', '','', 'name'); ?>
                        </td>
                        <td class="user_acccount_correct_button">
                            <input type="text" class="form-control form-filter input-sm " name="name" id ="name" />
                        </td>
                        <td class="user_acccount_correct_button">
                            <?php
                            echo Ddl::generateDDL('from_zone', 'CarrierZonesFilter', "status='1' ", 'name', 'id', '', ' class="bs-select input-sm form-control form-filter"', 'Select From Zone', '');
                            ?>
                        </td>
                        <td>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control input-sm form-filter" readonly name="start_date" id="start_date" placeholder="" >
                                <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control input-sm form-filter" readonly name="end_date" id="end_date" placeholder="" >
                                <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                            </div>
                        </td>
                        <td>
                            <?php
                            $arrayTypeValues = array('customer' => 'Customer', 'supplier' => 'Supplier');
                            echo Ddl::generateArrayDDL('type', $arrayTypeValues, "", "Select Type", ' class="form-control form-filter select2"', "", 'search_type', 'Select Type', '');
                            ?>
                        </td>
                        <td>
                            <?php
                            $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
                            echo Ddl::generateArrayDDL('status', $arrayTypeValues, "", "Select Active", ' class="form-control form-filter select2"', "", 'search_Active', 'Select Status', '');
                            ?>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!--Model for services remotearea-->
        <div class="modal fade bs-modal-lg" id="model_remoterea_supplier" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="remotearea_apend_remoterea_supplier">

                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
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
        <div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Select Carreir</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <?php echo Ddl::generateCarrierDDLWithImage('carrier_csv_upload', '', 'id', ' class="bs-select input-sm form-control form-filter "  data-live-search="true"  data-show-subtext="true"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
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
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv" class="btn green">Upload</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Model for CSV Upload-->
        <div class="modal fade" id="tariff_upload_csv_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Tariff Upload</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="upload_tariff_id" id="upload_tariff_id" value="" />
                            <input type="hidden" name="upload_tariff_zone_type" id="upload_tariff_zone_type" value="" />
                            <div class="col-md-12">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="form-group">
                                        <label> <?php echo Translation::GetCaption("SELECT_FILE_TO_IMPORT"); ?></label>
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                <span class="fileinput-filename"> </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput-new"> Select file </span>
                                                <span class="fileinput-exists"> Change </span>
                                                <input type="file" name="file" id="tariff_upload_file"> </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImport" data-original-title="" title=""><?php echo Translation::GetCaption("IMPORT"); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div  id="upload_carrier_zone_console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <a href="javascript:;" class="btn btn-info btn-sm download_tariff_template" id="download_tariff_template" data-type="uploadTariff" >Download Template</a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Model for Compare Pricing-->
        <div class="modal fade" id="compare_pricing_csv_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Tariff Compare Pricing</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="compareTariffPricingExcelForm" enctype="multipart/form-data">
                            <input type="hidden" name="tariffId" id="compare_pricing_tariff_id" value="" />
                            <input type="hidden" name="tariffZoneType" id="compare_pricing_tariff_zone_type" value="" />
                            <input type="hidden" name="func" id="func_compdownload_tariff_excel_btnaring_excel_download" value="compare_csv_file" />
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12 text-left">
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" class="shipment_type" id="compare_with_csv" name="compare_checkbox" value="p"> Compare with csv
                                                <span></span>
                                            </label>

                                            <label class="mt-radio">
                                                <input type="radio" class="shipment_type" id="compare_with_tariff" name="compare_checkbox" value="t" > Compare with Tariffs
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="compare_pricing_row">
                                <div class="col-md-12">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="form-group">
                                            <label> Select File to compare</label>
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="csv_file[]" id="tariff_compare_upload_file" multiple> </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="compare_tariffs_row" style="display: none;">
                                <div class="col-sm-12 full-width-multiselect">
                                    <div class="form-group">
                                        <label class="col-sm-12">Tariff Name
                                            <?php if (util_get_num("id") != "" && util_get_num("id") > 0) {?>
                                                <a href="tariffs_list.php" title="View"  class="btn btn-primary btn-sm pull-right" >
                                                    <i class="fa fa-plus"></i> Add New Tariff
                                                </a><?php } ?></label>
                                        <?php
                                        $user = SessionManager::getUser();
                                        $account = $user->getUserAccountId();
                                        //$useraccount = CustomerAccount::accountImmediateParent($account);
                                        $wereclauseData = "  status = 1 AND user_account_id = '" . $account . "'  AND tariff_type = 'supplier'";
                                        echo Ddl::generateDDL('tariff_name[]', 'TariffsFilter', $wereclauseData, 'name', 'id', $this->selectedTariff, ' class="multi-select multiselect_drop_down" multiple="multiple"', '', '', 'tariff_name', 'Tariff', "", "", "");
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12">
                                <div  id="compare_tariff_console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;"></div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-12">
                                <div  id="download_compare_excel_link"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <a href="javascript:;" class="btn btn-info btn-sm blue" id="btnSubmitCompare" data-original-title="" title="">Compare</a>
                        <a href="javascript:;" class="btn btn-info btn-sm blue" id="btnSubmitCompareTariffs" data-original-title="" title="" style="display: none;">Compare</a>
                        <a href="javascript:;" class="btn btn-info btn-sm download_tariff_template" id="download_tariff_compare_template" data-type="compareTariff" >Download Template</a>
                        <a href="javascript:;" style="display: none;" class="btn btn-success btn-sm" id="download_compare_tariff_excel_btn" data-original-title="" title="" style="display: none;">Download File</a>
                        <a href="javascript:;" style="display: none;" class="btn btn-success btn-sm" id="download_compare_tariff_to_tariff_excel_btn" data-original-title="" title="" style="display: none;">Download File</a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="modal fade" id="download_tariff_excel_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Download Customer Tariff </h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="download_tariffs_excel_file" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Client Name</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-user"></i></span>
                                            <input type="text" name="client_name" id="client_name" class="form-control" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date Issue</label>
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                            <input type="text" class="form-control input-sm" readonly name="issue_date" id="issue_date" placeholder="" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date Expire</label>
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                            <input type="text" class="form-control input-sm" readonly name="expire_date" id="expire_date" placeholder="" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Dims Description</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"> <i class="fa fa-user"></i></span>
                                            <textarea class="form-control" name="dim_description" id="dim_description">The maximum length+girth must be under 120 cm. With no single side greater than 90cm length.</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <a href="javascript:;" class="btn btn-info btn-sm" id="download_tariff_excel_btn" >Download Tariff</a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="assign_tariff_account_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Assign Customer Tariff </h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="assign_user_account_tariff" enctype="multipart/form-data">
                            <input type="hidden" name="selected_tariff" id="selected_tariff" value="">
                            <div class="row">
                                <div class="col-md-3">
                                        <label>User Account</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = false;


                                        if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                            $accountParentId = $this->user->getUserAccountId();
                                        if($this->account > 0 )
                                           $accountParentId = $this->account;
                                        $allowedLevel = 0;
                                        //echo  $accountParentId;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $user_account_id, "", 'class="form-filter bs-select form-control" data-live-search="true"', "", "user_account asc", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                        <a href="javascript:;" class="btn btn-info btn-sm" id="assign_tariff_account_btn" >Assign Tariff</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="show-user-account-for-tariff">
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="carrier_id_hidden" value="" id="carrier_id_hidden"/>
                <input type="hidden" name="action" value="download" />
            </form>
            <form id="csv_download_form" name="csv_download_form" method="post" >
                <input type="hidden" name="action" value="download_tariff_csv" />
                <?php
                $slectedCarrierId = '';
                if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                    $slectedCarrierId = $_GET['carrier_id'];
                }

                $slectedServiceId = '';
                if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                    $slectedServiceId = $_GET['service_id'];
                }
                ?>
                <input type="hidden" name="carrier_id" id="get_selected_carrier_id" value="<?php echo (int) $slectedCarrierId; ?>" />
                <input type="hidden" name="service_id" id="get_selected_service_id"  value="<?php echo $slectedServiceId; ?>" />

            </form>
            <form name="hiddenForm" id="download_tariff_template_form" action="" method="POST">
                <input type="hidden" name="tariffId" id="dounload_csv_tariffId" value="" />
                <input type="hidden" name="tariffZoneType" id="dounload_csv_tariffZoneType" value="" />
                <input type="hidden" name="func" value="download_tariff_template" />
            </form>
            <form name="hiddenForm" id="download_tariff_remotearea_template_form" action="tariff_remotearea_charges.php" method="POST">
                <input type="hidden" name="tariffId" id="download_csv_tariffId" value="" />
                <input type="hidden" name="carrierId" id="download_csv_carrierId" value="" />
                <input type="hidden" name="remoteareaType" id="download_csv_remoteareaType" value="" />
                <input type="hidden" name="func" value="download_tariff_remotearea_template" />
            </form>
            <?php $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . strip_tags($_SERVER['HTTP_HOST']) . strip_tags(urldecode($_SERVER['REQUEST_URI'])); ?>
            <form name="hiddenForm" id="download_tariff_detail_form" action="<?php echo $actual_link; ?>" method="POST">
                <input type="hidden" name="tariffId" id="tariff_id_detail_download" value="" />
                <input type="hidden" name="func" value="download_tariff_detail_csv" />
            </form>
            <form name="hiddenForm" id="download_tariff_excel_form" action="" method="POST">
                <input type="hidden" name="tariffId" id="tariff_id_excel_download" value="" />
                <input type="hidden" name="client_name" id="client_name_excel_download" value="" />
                <input type="hidden" name="issue_date" id="issue_date_excel_download" value="" />
                <input type="hidden" name="expire_date" id="expire_date_excel_download" value="" />
                <input type="hidden" name="dim_discription" id="dim_description_excel_download" value="" />
                <input type="hidden" name="func" value="download_tariff_excel" />
            </form>
            <form name="hiddenForm" id="download_compare_tariff_excel_form" action="" method="POST">
                <input type="hidden" name="tariff_id" id="tariff_id" value="" />
                <input type="hidden" name="zones[]" id="compare_zones" value="" />
                <input type="hidden" name="files[]" id="compare_files" value="" />
                <input type="hidden" name="func" value="compare_csv_tariffs_save_excel" />
            </form>
            <form name="hiddenForm" id="download_compare_tariff_to_tariff_excel_form" action="" method="POST">
                <input type="hidden" name="tariff_to_tariff_id" id="tariff_to_tariff_id" value="" />
                <input type="hidden" name="tariff_zones[]" id="tariff_zones" value="" />
                <input type="hidden" name="compare_tariffs" id="compare_tariffs" value="" />
                <input type="hidden" name="func" value="compare_tariff_to_tariffs_save_excel" />
            </form>
            <form id="export_routine_tariff_frm" action="get_pricing.php" method="post">
                <input type="hidden" name="func" value="download_routing_excel" />
                <input type="hidden" name="cutomized_service_id_for_routing" id="cutomized_service_id_for_routing" value="" />
                <input type="hidden" name="routing_tariff_id" id="routing_tariff_id" value="routing_tariff_id" />
                <input type="hidden" name="routing_pricing_download" value="1" />
            </form>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="oba_docket_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Tariff Additional Charges</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" id="res_oba_docket_message"></div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <label>Charges Type</label>
                                    <select name="chargesTypeId" id="chargesTypeId" class="form-filter select2 form-control">
                                        <?php
                                        $chargesTypesFilter = new ConsignmentChargesTypesFilter();
                                        $tpyeWhere = " cct.charge_type <> 'customer'";
                                        $chargesTypesFilter->addFilter("   $tpyeWhere and cct.status='1' and is_delete = '0' ");
                                        $chargesTypesFilterObj = $chargesTypesFilter->getList('*');
                                        $chargesArray = [];
                                        $chargesArray[""] = "Select Charges Title";
                                        if(count($chargesTypesFilterObj) > 0) {
                                            foreach($chargesTypesFilterObj as $charges) {
                                                ?>
                                                <option value="<?php echo $charges->getId(); ?>" data-apply_by="<?php echo $charges->getApplyBy(); ?>"><?php echo $charges->getTitle(); ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-5" id="charges_container">
                                    <label>Charges</label>
                                    <div class="input-group">
                                        <input type="text" name="charges" id="charges" class="form-control">
                                        <div class="input-group-btn">
                                            <select class="selectpicker form-control" name="chargesType" id="chargesType" >
                                                <option value="percentage">%</option>
                                                <option value="fixed">Fixed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="apply_bluk_addtional_charges();">Assign</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bs-modal-lg" id="tariff_remoteareas_modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="remotearea_apend_remoterea_suppliers">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">RemoteAreas Charges</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger display-none"  id="res_message_remoterea_suppliers" ></div>
                            </div>
                        </div>
                        <div class="parent_clone_div_remotearea">
                            <div class="clone_div_remotearea">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Group</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                                <select name="group[]" id="groups" class="validate_checks  form-control select2 remotearea_group"  title = "Group" data-container="body" placeholder = "Group"  >
                                                    <option value="">Please select Group</option>
                                                </select>
                                                <span class="input-group-addon red-18">*</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label>Charges</label>
                                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-key"></i> </span>
                                                <div class="input-icon right">
                                                    <i class="fa tooltips font-red" data-original-title="Charges is mandatory">*</i>
                                                    <input type="text" name="charges[]" value="" placeholder="Charges" class="validate_checks form-control remotearea_charges"  data-toggle="tooltip" data-placement="top" title="Charges" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 show_remotearea_remove_btn" data-index_of_remotearea_remove="0" style="display: none;">
                                        <label class="control-label">&nbsp;</label>
                                        <a href="javascript:;"  class="btn btn-danger repeater-delete"><i class="fa fa-close"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="append_here_remotearea"></div>
                            <a href="javascript:;" data-tariff_id="1470" class="btn btn-info repeater_add_mores"><i class="fa fa-plus"></i> Add more</a>
                            <br><br>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-md-offset-9 col-sm-4 col-sm-offset-6">
                                <div class="form-group">
                                    <button   id="save_remoteareas_charges_for_suppliers" data-tariff_id="1470" class="btn btn-primary green save_remoteareas_tariff_data">Save changes</button>&nbsp;
                                    <button  class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"><hr/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="form-group">
                                        <label>SELECT_FILE_TO_IMPORT</label>
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                <span class="fileinput-filename"> </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput-new"> Select file </span>
                                                <span class="fileinput-exists"> Change </span>
                                                <input type="file" name="file" id="tariff_upload_remote_file"> </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImportRemoteArea" data-original-title="" title="">IMPORT</a>
                                            <a href="javascript:;" class="input-group-addon btn danger" id="download_tariff_remotarea_template" data-original-title="" title=""><i class="fa fa-download"></i> TEMPLATE</a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">       </div>
                        </div>
                    </div>


                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>


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
