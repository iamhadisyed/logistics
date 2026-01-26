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
    'tariffsdetails.class',
    'tariffsdetailsfilter.class',
    'remoteareachargestariffs.class',
    'remoteareachargestariffsfilter.class',
    'remoteareasgroups.class',
    'remoteareasgroupsfilter.class',
]);

class Page extends BasePage {
    /* * *
     * Controller logic
     */

    private $filerColumn = '*';
    private $params = "";
    private $user = "";

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
        /*
         * DataTable handlings
         */
        if((isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) && (isset($_GET['service_id']) && !empty($_GET['service_id']))) {
            $this->params = "carrier_id=" . $_GET['carrier_id'] . "&service_id=" . $_GET['service_id'];
        } else {
            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                $this->params = "carrier_id=" . $_GET['carrier_id'];
            }
            if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                $this->params = "service_id=" . $_GET['service_id'];
            }
        }
        if (isset($_GET['action']) && $_GET['action'] == "tariffs_ajax") {
            $tariffsFilter = new TariffsFilter();
            //if ($this->user->getUserType() != User::USER_TYPE_ADMIN)
            {
                $tariffsFilter->addFieldFilter('   t.user_account_id', $this->user->getUserAccountId());
                //$tariffsFilter->addFieldFilter('   t.status', 1);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
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
//                    echo $functionName; die;
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
                } else {
                    $typeOfTariff = '<div class="text-center"><span class="label label-sm label-info">Supplier</span></div>';
                }
                // set status of tariff
                if ($tariffsObj->getStatus() == 1) {
                    $statusOfTariff = '<div class="text-center"><span class="label label-sm label-success">Yes</span></div>';
                } else {
                    $statusOfTariff = '<div class="text-center"><span class="label label-sm label-danger">No</span></div>';
                }

                $currentArr = array();
                $currentArr['carrier_id'] = '<img src="../images/carrierlogo/thumbnail/owe_16_' . $carrierLogo . '" alt="" /> ' . $carrierName;
                $currentArr['service_id'] = $service->getName();
                $currentArr['name'] = $tariffsObj->getName();
                $currentArr['currency'] = $currency->getCurrencyname();
                $currentArr['start_date'] = formatDate(date("d-m-Y", strtotime($tariffsObj->getStartDate())));
                $currentArr['end_date'] = formatDate(date("d-m-Y", strtotime($tariffsObj->getEndDate())));
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
                                    </li>'.$returnHtml;
                if($tariffsObj->getTariffType() != "supplier") {
                    $currentArr['actions'] .= '<li>
                                                    <a href="javascript:;" id="download_tariff_excel" data-id="'.$tariffsObj->getId().'" title="Download Excel">
                                                        <i class="fa fa-download"></i> Download Excel
                                                    </a>
                                                </li>';
                }
                $currentArr['actions'] .= '<li>
                                                    <a href="javascript:;" title="Download CSV" id="download_tariff_csv" data-id="'.$tariffsObj->getId().'">
                                                        <i class="fa fa-download"></i> Download CSV
                                                    </a>
                                                </li>';
                $currentArr['actions'] .= '<li>
                                                    <a href="javascript:;" title="Upload CSV" id="show_upload_csv_modal" data-id="'.$tariffsObj->getId().'">
                                                        <i class="fa fa-download"></i> Upload CSV
                                                    </a>
                                                </li>';


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

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $tariffId = $this->form_vars['tariff_id'];
            if ($tariffId > 0) {
                $remoteareas = new Tariffs($tariffId);
                $remoteareas->delete();
                $returnMsg['STATUS'] = "success";
                /*
                 * Add Remoteareas Log details
                 */
//                $remoteareasGroupsLog = new TariffsLog();
//                $newTariffsData = serialize($remoteareas);
//                $remoteareasGroupsLog->createlog($this->user->getId(),'',$tariffId,'REMOTEAREAS_GROUPS',$this->user->getUserName() . ' has deleted ' . $tariffId,$oldTariffsData, $newTariffsData);
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
            $tariff = new Tariffs($tariffId);
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
                    $account = $user->getAccount();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $relPath = '../_assets/carrierzones_csv/'.$new_file_name;
                    if (!file_exists("../_assets/carrierzones_csv/"))
                        @mkdir("../_assets/carrierzones_csv/", 0775);
                    $csvContent = '';
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                        $row = 1;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $successRecords = 0;
                            $errorRecords = 0;
                            $headings = array();
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
                                $weight = explode('-',$value[0]);
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

                                /* Get Zone Id From Name */
                                $carrierZonesNameFilter = new CarrierZonesFilter();
                                $carrierZonesNameFilter->addIsDeletedFilter();
                                $carrierZonesNameFilter->addFieldFilter('TRIM(UPPER(cz.name))', trim(strtoupper($fromZoneName)));
                                $carrierZonesNameFilter->addFieldFilter('cz.status', 1);
                                $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                                if (count($zoneFilterObj) > 0) {
                                    $zoneToId = $zoneFilterObj[0]->getId();
                                }
                                /* Get Zone Id From Name end */
                                /* Get Zone Id From Name */
                                $carrierZonesNameFilter = new CarrierZonesFilter();
                                $carrierZonesNameFilter->addIsDeletedFilter();
                                $carrierZonesNameFilter->addFieldFilter('TRIM(UPPER(cz.name))', trim(strtoupper($toZoneName)));
                                $carrierZonesNameFilter->addFieldFilter('cz.status', 1);
                                $zoneFilterObj = $carrierZonesNameFilter->getColumnList('cz.name');
                                if (count($zoneFilterObj) > 0) {
                                    $zoneFromId = $zoneFilterObj[0]->getId();
                                }

//                                if(in_array($zoneToId, $vaildsZoneIds) && $zoneToId > 0) {
                                $csvContent .= (!empty($csvContent) ? "\n" : '').$tariffId.','.$zoneFromId.','.$zoneToId.','.$w_from.','.$w_to.','.$weightCost.','.$pieceCost.','.$formula;
                                $successRecords++;
//                                } else {
//                                    if($lastKey == $key) {
//                                        if(!$formula) {
//                                            $errorRecords++;
//                                        }
//                                    } else {
//                                        $errorRecords++;
//                                    }
//                                }

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

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_tariff_template') {
            $tariffId = $this->form_vars['tariffId'];
            $zoneIds = $this->getValidZoneFromTariffId($tariffId);
            /*  Download Csv Code  */
            $fileName = "tariff_csv_" . time(). ".csv";
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
                        $carrierFromZone = new CarrierZones($zoneFromId);
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

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_tariff_excel') {
            $tariffId = $this->form_vars['tariffId'];
            $tariff = new tariffs($tariffId);
            $tariffDetails = new TariffsDetailsFilter();
            $tariffDetails->addFieldFilter("    td.tariffs_id", $tariffId);
            $tariffDetails->setLimit("-1");
            $tariffDetailsObjs = $tariffDetails->getList();
            $data = [];
            $zoneIds = [];
            foreach($tariffDetailsObjs as $tariffDetailsObj) {
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
                $objPHPExcel->getActiveSheet()->SetCellValue('B4', "Ykit");
                $objPHPExcel->getActiveSheet()->SetCellValue('B5', date('d-m-Y'));

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
                        $zone_id = $zoneId;
                        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleForReport);
                        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, html_entity_decode($currency->getLeftsymbol()).$cost['weight_cost']);
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
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "The maximum length+girth must be under 120 cm. With no single side greater than 90cm length.");
                $objPHPExcel->getActiveSheet()->mergeCells('E'.$count.':F'.($count+4));
                $objPHPExcel->getActiveSheet()->getStyle('E'.$count.':F'.($count+4))->applyFromArray($styleEndReport);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$count, "up to 30 KG");
                $count = $count + 5;

                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "The above rates are based on pre-labeled shipments as per One World Routings");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "The rates offered are based on an average volume and send profile.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "One World Express reserve the right to revise these rates should this profile change adversely.");
                $count++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, "Rates are valid untill ".date('d-m-Y',strtotime($endDate))." unless revoked earlier with 30 days notice.");
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
            echo "excel download successfully";
            die;
        }
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
        $carrierZonesFilter->addFieldFilter('cz.status', 1);
        $carrierZonesFilter->addFieldFilter('cz.carrier_id', $carrierId);
        if ($carrierZoneBase == 0) {
            $carrierZonesFilter->addFieldFilter('cz.service_id', $tariffs->getServiceId());
        }
        $carrierZones = $carrierZonesFilter->getColumnList('id,name');

        $validZone = [];
        if (count($carrierZones) > 0) {
            foreach ($carrierZones as $carrierZone) {
                $validZone[] = $carrierZone->getId();
            }
        }
        return $validZone;
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
                        if(service_id.length > 0) {
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
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
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
            $(document).on('click', '#download_tariff_template', function () {
                var tariffId = $('#upload_tariff_id').val();
                $('#dounload_csv_tariffId').val(tariffId);
                $('#download_tariff_template_form').submit();
            });
            $(document).on('click', '#show_upload_csv_modal', function () {
                var tariffId = $(this).data('id');
                $('#upload_tariff_id').val(tariffId);
                $('#tariff_upload_csv_modal').modal('show');
            });
            $(document).on('click', '#btnSubmitImport', function () {
                var file_data = $('#tariff_upload_file').prop('files')[0];
                var tariffId = $('#upload_tariff_id').val();
                var form_data = new FormData();
                form_data.append('csv_file', file_data);
                form_data.append('func', 'upload_csv_file');
                form_data.append('tariffId', tariffId);
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

            $(document).on('click', '#download_tariff_excel', function () {
                var tariffId = $(this).data('id');
                $('#tariff_id_excel_download').val(tariffId);
                $('#download_tariff_excel_form').submit();
            });

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
                            <a href="tariff_add.php" class="btn blue"  ><i class="fa fa-plus"></i> Add Tariff</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="portlet-body">
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
                        <td>
                            <?php
                            $slectedCarrierId = '';
                            if(isset($_GET['carrier_id']) && !empty($_GET['carrier_id'])) {
                                $slectedCarrierId = $_GET['carrier_id'];
                            }
                            ?>
                            <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', $slectedCarrierId, 'id', ' class="bs-select input-sm form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body"'); ?>
                        </td>
                        <td>
                            <?php
                            $slectedServiceId = '';
                            if(isset($_GET['service_id']) && !empty($_GET['service_id'])) {
                                $slectedServiceId = $_GET['service_id'];
                            }
                            ?>
                            <?php echo Ddl::generateServiceDDLWithImage('service_id', $slectedServiceId, 'id', ' class="bs-select input-sm form-control form-filter " data-live-search="true"  data-show-subtext="true" data-container="body"'); ?>
                        </td>
                        <td>
                            <input type="text" class="form-control form-filter input-xs" name="name" id ="name" />
                        </td>
                        <td>
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
                        <a href="javascript:;" class="btn btn-info btn-sm" id="download_tariff_template" >Download Template</a>
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
                <input type="hidden" name="carrier_id" id="get_selected_carrier_id" value="<?php echo $slectedCarrierId; ?>" />
                <input type="hidden" name="service_id" id="get_selected_service_id"  value="<?php echo $slectedServiceId; ?>" />

            </form>
            <form name="hiddenForm" id="download_tariff_template_form" action="" method="POST">
                <input type="hidden" name="tariffId" id="dounload_csv_tariffId" value="" />
                <input type="hidden" name="func" value="download_tariff_template" />
            </form>
            <form name="hiddenForm" id="download_tariff_remotearea_template_form" action="tariff_remotearea_charges.php" method="POST">
                <input type="hidden" name="tariffId" id="download_csv_tariffId" value="" />
                <input type="hidden" name="carrierId" id="download_csv_carrierId" value="" />
                <input type="hidden" name="remoteareaType" id="download_csv_remoteareaType" value="" />
                <input type="hidden" name="func" value="download_tariff_remotearea_template" />
            </form>
            <?php $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; ?>
            <form name="hiddenForm" id="download_tariff_detail_form" action="<?php echo $actual_link; ?>" method="POST">
                <input type="hidden" name="tariffId" id="tariff_id_detail_download" value="" />
                <input type="hidden" name="func" value="download_tariff_detail_csv" />
            </form>
            <form name="hiddenForm" id="download_tariff_excel_form" action="" method="POST">
                <input type="hidden" name="tariffId" id="tariff_id_excel_download" value="" />
                <input type="hidden" name="func" value="download_tariff_excel" />
            </form>
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