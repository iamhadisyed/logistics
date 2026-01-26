<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'tariffs.class',
    'tariffsfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'tariffadditionalchargesfilter.class',
    'tariffadditionalcharges.class',
    'userservicescharges.class',
    'userserviceschargesfilter.class',
    'country.class',
    'countryfilter.class',
    'tariffsdetails.class',
    'tariffsdetailsfilter.class',
    'carrierzones.class',
    'carrierzonesfilter.class'
    ]);

///require_once("../includes/mapping/userservicescharges.class.php");
/* * *
 * Page for editing a user
 */

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = 0;
    private $tariff_id = 0;

    protected function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("SERVICE_CHARGES")
        );

        $this->user = SessionManager::getUser();
        if (!empty($_GET['tariff_id'])) {
            $this->tariff_id = trim($_GET['tariff_id']);
            $this->tariffsObj = new Tariffs($this->tariff_id);
          //  print_r($this->tariffsObj);
            //die;
            
        }
        if((int)$this->tariff_id <=0)
        {
            util_redirect("tariffs_list.php");
        }
        else
        {
            $tarriffNameClass = new Tariffs($this->tariff_id);
            if($tarriffNameClass->getUserAccountId() != $this->user->getUserAccountId())
            {
                util_redirect("tariffs_list.php");
            }
        }
        
        
        /*
         * Handle Data Table 
         */

        if (isset($_GET['action']) && $_GET['action'] == 'tariff_charges_ajax') {
            $tariffAdditionalChargesFilter = new TariffAdditionalChargesFilter();
            $tariffAdditionalChargesFilter->join("consignment_charges_types cct", ['cct.id' => 'tac.consignment_charges_types_id']);
            $tariffAdditionalChargesFilter->join("tariffs t", ['t.id' => 'tac.tariff_id']);
            
               $filterArray['tac.tariff_id'] = $this->tariff_id;
               $tariffAdditionalChargesFilter->where($filterArray);

            
            if(trim($this->tariffsObj->getTariffType()) == 'customer')
                $tariffAdditionalChargesFilter->whereNotIn('cct.charge_type', "'agent'");
            else
                $tariffAdditionalChargesFilter->whereNotIn('cct.charge_type', "'customer'");
           /*
            * Column filter
            * For search
            */
           if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
               $filterArray = [];

               $consignment_charges_types_id = $this->form_vars['consignment_charges_types_id'];
               if (!empty($consignment_charges_types_id))
                   $filterArray['tac.consignment_charges_types_id'] = $consignment_charges_types_id;

               $charge = $this->form_vars['charge'];
               if (!empty($charge))
                   $filterArray['tac.charge'] = $charge;
               
               $charge_type = $this->form_vars['charge_type'];
               if (!empty($charge_type))
                   $filterArray['tac.charge_type'] = $charge_type;

               $tariff_id = $this->form_vars['tariff_id'];
               if (!empty($tariff_id))
                   $filterArray['tac.tariff_id'] = $tariff_id;
               
               
               

               $tariffAdditionalChargesFilter->where($filterArray);

               $date_created_from = $this->form_vars['date_created_from'];
               $date_created_to = $this->form_vars['date_created_to'];
               if (!empty($date_created_from) && !empty($date_created_to))
                   $tariffAdditionalChargesFilter->whereBetween ('tac.added_date', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));
            }
            
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = "ASC";
                if ($orderBy == 'desc') {
                    $orderFalse = 'DESC';
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                $tariffAdditionalChargesFilter->orderBy(strtolower("tac.".$dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $tariffAdditionalChargesFilter->setRowsPerPage($iDisplayLength);
            $tariffAdditionalChargesFilter->setOffset($iDisplayStart);
            $tariffAdditionalChargesFilter->groupBy('tac.consignment_charges_types_id');
            $tariffAdditionalChargesFilterObj = $tariffAdditionalChargesFilter->getList("tac.*, t.name, cct.title");
            $iTotalRecords = $tariffAdditionalChargesFilter->getCount();
            $setDataArr = array();
            foreach ($tariffAdditionalChargesFilterObj as $tariffAdditionalChargesObj) {
                $chargesListArr = array(); 
                $chargesListArr['tariff_id'] = $tariffAdditionalChargesObj->getName();
                $chargesListArr['consignment_charges_types_id'] = $tariffAdditionalChargesObj->getTitle();
                $chargesListArr['charge'] = $tariffAdditionalChargesObj->getCharge();
                $chargesListArr['charge_type'] = ucfirst($tariffAdditionalChargesObj->getChargeType());
                $chargesListArr['added_date'] = formatDate(date("Y-m-d",$tariffAdditionalChargesObj->getAddedDate()));
                $chargesListArr['actions'] = '';
                $chargesListArr['actions'] .= '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                        <ul class="dropdown-menu" >';
                    $chargesListArr['actions'] .= '<li>
                                                    <a title="Edit" href="javascript:;" class="btnedit" data-id="' . $tariffAdditionalChargesObj->getId() . '" >
                                                        <span class="fa fa-edit"></span> Edit
                                                    </a>
                                                </li>';
                    $chargesListArr['actions'] .= '<li>
                                                    <a title="Delete" href="javascript:;" class="btnDelete" data-id="' . $tariffAdditionalChargesObj->getId() . '" >
                                                        <span class="fa fa-trash"></span> Delete
                                                    </a>
                                                </li>';
                $chargesListArr['actions'] .= '</ul> </div>';
                $setDataArr [] = $chargesListArr;
            }
            $serviceDataArrJson['data'] = $setDataArr;
            $serviceDataArrJson['draw'] = $sEcho;
            $serviceDataArrJson['recordsTotal'] = $iTotalRecords;
            $serviceDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($serviceDataArrJson);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'saveTariffAdditionalCharges') {
            $output = [];
            $tariffId = $this->form_vars['tariff_id'];
            $chargesTypeId = $this->form_vars['chargesTypeId'];
            $charges = $this->form_vars['charges'];
            $chargesType = $this->form_vars['chargesType'];
            if ($tariffId <= 0) {
                $output['status'] = "error";
                $output['message'] = "Tariff is invalid, Please select tariff from dropdown and try again.";
                echo json_encode($output);
                die();
            }
            if ($chargesTypeId <= 0) {
                $output['status'] = "error";
                $output['message'] = "Charges type is not valid";
                echo json_encode($output);
                die();
            }
            if(is_array($charges)) {
                $zone_ids = $this->form_vars['zone_ids'];
                $formulas = $this->form_vars['formula'];
                $tariffAdditionalChargesIds = $this->form_vars['tariff_additional_charges_id'];
                foreach($charges as $key => $charge) {
                    $chargesType = $chargesType[$key];
                    $zoneId = $zone_ids[$key];
                    $formula = $formulas[$key];
                    $tariffAdditionalChargesId = isset($tariffAdditionalChargesIds[$key]) ? $tariffAdditionalChargesIds[$key] : "";
                    $this->saveTariffAdditionalCharges($tariffId,$chargesTypeId,$charge,$chargesType,$formula,$zoneId,$tariffAdditionalChargesId);
                }
            } else {
                if ($charges <= 0) {
                    $output['status'] = "error";
                    $output['message'] = "Charges must be greater than zero";
                    echo json_encode($output);
                    die();
                }
                $tariffAdditionalChargesId = $this->form_vars['tariff_additional_charges_id'];
                $this->saveTariffAdditionalCharges($tariffId,$chargesTypeId,$charges,$chargesType,'','',$tariffAdditionalChargesId);

            }
            $output['status'] = "success";
            $output['message'] = "Charges Save successfully";
            echo json_encode($output);
            die();
        }
         
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_tariff_additional_charges') {
            $id = $this->form_vars['id'];
            $returnJson = [];
            $chergeObj = new TariffAdditionalCharges($id);
            $returnJson['id'] = $chergeObj->getId();
            $returnJson['tariff_id'] = $chergeObj->getTariffId();
            $returnJson['consignment_charges_types_id'] = $chergeObj->getConsignmentChargesTypesId();
            $returnJson['charge'] = $chergeObj->getCharge();
            $returnJson['charge_type'] = $chergeObj->getChargeType();
            echo json_encode($returnJson);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $id = $this->form_vars['id'];
            if ($id > 0) {
                $chergeObj = new TariffAdditionalChargesFilter($id);
                $chergeObj->where(['id' => $id]);
                $chergeObj->delete();
                $returnMsg['STATUS'] = "success";
            } else {
                $returnMsg['STATUS'] = "error";
            }
            echo json_encode($returnMsg);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'getTariffZoneForCharge') {
            $tariffId = $this->form_vars['tariff_id'];
            $tariffDetailFilter =  new TariffsDetailsFilter();
            $tariffDetailFilter->addJoinCarrierZone();
            $tariffDetailFilter->addFieldFilter('     td.tariffs_id',$tariffId);
            $tariffDetailFilter->addGroupBy('      cz.id');
            $tariffDetailFilters = $tariffDetailFilter->getColumnList('cz.id,cz.name as zone_name');
            $html = '';
            if(count($tariffDetailFilters)) {
                foreach($tariffDetailFilters as $tariffDetailFilterObj) {
                    $zoneId = $tariffDetailFilterObj->getId();
                    $zoneName = $tariffDetailFilterObj->getZoneName();

                    $tariffAdditionalChargesFilter = new TariffAdditionalChargesFilter();
                    $tariffAdditionalChargesFilter->where(['tac.tariff_id' => $tariffId]);
                    $tariffAdditionalChargesFilter->where(['tac.zone_id' => $zoneId]);
                    $tariffAdditionalChargesFilterObjs = $tariffAdditionalChargesFilter->getList('tac.*');
                    $chargeTypeId = '';
                    $charge = '';
                    $chargeType = '';
                    $formula = '';
                    if(count($tariffAdditionalChargesFilterObjs)) {
                        $chargeTypeId = $tariffAdditionalChargesFilterObjs[0]->getId();
                        $charge = $tariffAdditionalChargesFilterObjs[0]->getCharge();
                        $chargeType = $tariffAdditionalChargesFilterObjs[0]->getChargeType();
                        $formula = $tariffAdditionalChargesFilterObjs[0]->getFormula();
                    }
                    $typePercentageSelected = "";
                    $typeFixedSelected = "";
                    if($chargeType == "percentage") {
                        $typePercentageSelected = "selected='selected'";
                    }
                    if($chargeType == "fixed") {
                        $typeFixedSelected = "selected='selected'";
                    }
                    $html .= $this->countryChargesHtml($zoneId, $zoneName, $chargeTypeId, $chargeType, $charge, $formula);
                }
            }
            $return = [
                'status' => 'success',
                'html' => $html
            ];
            echo json_encode($return);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_country_form') {
            $tariffId = $this->tariff_id;
            $tariffDetailFilter =  new TariffsDetailsFilter();
            $tariffDetailFilter->addJoinCarrierZone();
            $tariffDetailFilter->addFieldFilter('     td.tariffs_id',$tariffId);
            $tariffDetailFilter->addGroupBy('      cz.id');
            $tariffDetailFilters = $tariffDetailFilter->getColumnList('td.id,td.tariffs_id,cz.id as zone_id,cz.name as zone_name');
            $csvStr = 'Zone Name,Charges,Charges Type,Formula';
            if(count($tariffDetailFilters)) {
                foreach($tariffDetailFilters as $key => $tariffDetailFilter) {
                    $tariffAdditionalChargesFilter = new TariffAdditionalChargesFilter();
                    $tariffAdditionalChargesFilter->where(['tac.tariff_id' => $tariffDetailFilter->getTariffsId()]);
                    $tariffAdditionalChargesFilter->where(['tac.zone_id' => $tariffDetailFilter->getZoneId()]);
                    $tariffAdditionalChargesFilterObjs = $tariffAdditionalChargesFilter->getList('tac.*');
                    $charge = '';
                    $chargeType = '';
                    $formula = '';
                    if(count($tariffAdditionalChargesFilterObjs)) {
                        $charge = $tariffAdditionalChargesFilterObjs[0]->getCharge();
                        $chargeType = $tariffAdditionalChargesFilterObjs[0]->getChargeType();
                        $formula = $tariffAdditionalChargesFilterObjs[0]->getFormula();
                    }
                    /* make csv string*/
                    $csvStr .= "\r\n";
                    $csvStr .= $tariffDetailFilter->getZoneName().',';
                    $csvStr .= $charge.',';
                    $csvStr .= $chargeType.',';
                    $csvStr .= $formula.',';
                }
            }
            $fileName = "countryCharges_" . time();
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $csvStr;
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_country_charges') {
            $tariffId = $this->tariff_id;
            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            $zoneCharges = [];
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $new_file_name = "tariff_additional_country_charges_" . time() . "_" . $basename;
                    $relPath = '../_assets/tariff_additional_charges_csv/' . $new_file_name;
                    if (!file_exists("../_assets/tariff_additional_charges_csv/")) {
                        @mkdir("../_assets/tariff_additional_charges_csv/", 0775);
                    }
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                        $row = 0;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle)) !== FALSE) {
                                if($row > 0) {
                                    $zoneCharges[] = [
                                            'zone_name' => $data[0],
                                            'charge_type' => $data[2],
                                            'charge' => $data[1],
                                            'formula' => $data[3]
                                    ];
                                }
                                $row++;
                            }
                        } else {
                            $output['message'] = 'Fail to open file.';
                            $output['status'] = 'fail';
                        }
                    } else {
                        $output['message'] = 'Fail to upload file.';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = 'Invalid CSV file.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = 'No file found to import data.';
                $output['status'] = 'fail';
            }
            $html = '';
            if(count($zoneCharges)) {
                foreach($zoneCharges as $zoneChargeArr) {
                    $tariffDetailFilter =  new TariffsDetailsFilter();
                    $tariffDetailFilter->addJoinCarrierZone();
                    $tariffDetailFilter->addFieldFilter('      td.tariffs_id',$tariffId);
                    $tariffDetailFilter->addFieldLikeFilter('        cz.name',$zoneChargeArr['zone_name']);
                    $tariffDetailFilter->addGroupBy('      cz.id');
                    $tariffDetailFilters = $tariffDetailFilter->getColumnList('cz.id as zone_id,cz.name as zone_name');
                    $zoneId = "";
                    $zoneName = "";
                    $chargeTypeId = "";
                    if(count($tariffDetailFilters)) {
                        $tariffDetailFilterObj = $tariffDetailFilters[0];
                        $zoneId = $tariffDetailFilterObj->getZoneId();
                        $zoneName = $tariffDetailFilterObj->getZoneName();

                        $tariffAdditionalChargesFilter = new TariffAdditionalChargesFilter();
                        $tariffAdditionalChargesFilter->where(['tac.tariff_id' => $tariffId]);
                        $tariffAdditionalChargesFilter->where(['tac.zone_id' => $zoneId]);
                        $tariffAdditionalChargesFilterObjs = $tariffAdditionalChargesFilter->getList('tac.*');

                        if(count($tariffAdditionalChargesFilterObjs)) {
                            $chargeTypeId = $tariffAdditionalChargesFilterObjs[0]->getId();
                        }
                    }
                    $chargeType = $zoneChargeArr['charge_type'];
                    $charge = $zoneChargeArr['charge'];
                    $formula = $zoneChargeArr['formula'];
                    if($zoneId != "") {
                        $html .= $this->countryChargesHtml($zoneId, $zoneName, $chargeTypeId, $chargeType, $charge, $formula);
                    }
                }
            }
            $output['html'] = $html;
            echo json_encode($output);
            die;
        }

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

    protected function countryChargesHtml($zoneId, $zoneName, $chargeTypeId, $chargeType, $charge, $formula) {
        $html = "";
        $html .= '<tr id="country_' . $zoneId . '" data-country_id="' . $zoneId . '">';
        $html .= '<td><label>' . $zoneName . '</label><input type="hidden" name="zone_ids[]" id="zone_id_' . $zoneId . '" value="' . $zoneId . '"><input type="hidden" name="zone_names[]" id="zone_name_' . $zoneId . '" value="' . $zoneName . '"><input type="hidden" name="tariff_additional_charges_id[]" id="tariff_additional_charges_id_'.$zoneId.'" value="'.$chargeTypeId.'" ></td>';
        $html .= '<td>';
        $html .= '<div class="input-group">';
        $html .= '<input type="text" name="charges[]" id="charges_' . $zoneId . '" class="form-control" value="'.$charge.'" >';
        $html .= '<div class="input-group-btn">';
        $html .= '<select class="selectpicker form-control" name="chargesType[]" id="chargesType_' . $zoneId . '" >';
        $typePercentageSelected = "";
        $typeFixedSelected = "";
        if($chargeType == "percentage") {
            $typePercentageSelected = "selected='selected'";
        }
        if($chargeType == "fixed") {
            $typeFixedSelected = "selected='selected'";
        }
        $html .= '<option value="percentage" '.$typePercentageSelected.' >%</option>';
        $html .= '<option value="fixed" '.$typeFixedSelected.' >Fixed</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<select name="formula[]" id="formula_' . $zoneId . '" class="form-control selectpicker formula" >';
        $tariffFormulas = get_tariff_formulas();
        if (!empty($tariffFormulas)) {
            foreach ($tariffFormulas as $tariffFormula => $tariffFormulaText) {
                $selected = "";
                if($formula == $tariffFormula) {
                    $selected = "selected='selected'";
                }
                $html .= '<option value="' . $tariffFormula . '" ' . $selected . ' >' . $tariffFormulaText . '</option>';
            }
        }
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    }

    protected function addPagelavelCss() {
        ?>
		<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .btn-secelct-option {
                width: 100%;
                margin: 5px auto;
            }
            .ms-container {
                width: 100%;
            }
            .country_container {
                display: none;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
		<script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-bootstrap-multiselect.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/serializeForm.js" type="text/javascript"></script>
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
                                "url": "tariff_additional_charges.php?action=tariff_charges_ajax&tariff_id=<?php echo $this->tariff_id; ?>", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "tariff_id"},
                                {"data": "consignment_charges_types_id"},
                                {"data": "charge"},
                                {"data": "charge_type"},
                                {"data": "added_date"}
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
            	$("#tariff_csv_box").hide();
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                var TariffId = $("#tariff_id").select2();                
                TariffId.val('<?php echo $this->tariff_id;?>').trigger('change');

                $(document).on('click','.remove_country_charges', function() {
                    $(this).parent().parent().remove();
                });

                $("#upload_csv").click(function () {
                    var file_data = $('#csv_file').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('csv_file', file_data);
                    form_data.append('action', 'upload_country_charges');
                    $.ajax({
                        url: "tariff_additional_charges.php?tariff_id=<?php echo $this->tariff_id; ?>",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                            var status = response.status;
                            if (status == 'success') {
                                $('#add_country_charges_row').html(response.html);
                                $('.bs-select').selectpicker({
                                    'liveSearch':true,
                                    container: 'body'
                                });
                                $('.selectpicker').selectpicker({
                                    'liveSearch':true,
                                    container: 'body'
                                });
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                        }
                    });
                    return false;
                });

            });
            // Handle get account services
            $(document).on('click', '.btnDelete', function () {
                var id = $(this).attr('data-id');
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
                                    url: "tariff_additional_charges.php?tariff_id=<?php echo $this->tariff_id; ?>",
                                    data: {action: "delete", id: id},
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
            $(document).on('click', '.btnedit', function () {
                $(".scroll-to-top").click();
                var id = $(this).attr('data-id');
                $.ajax({
                    url: 'tariff_additional_charges.php?tariff_id=<?php echo $this->tariff_id; ?>',
                    type: 'POST',
                    data: {action: 'get_tariff_additional_charges', id: id},
                    headers: {
                    },
                    dataType: "json",
                    success: function (obj) {
                        $("#tariff_additional_charges_id").val(obj.id);
                        var TariffId = $("#tariff_id").select2();                
                        TariffId.val(obj.tariff_id).trigger('change');
                        $("#chargesTypeId").val(obj.consignment_charges_types_id);
                        var chargesTypeId = $("#chargesTypeId").select2();
                        chargesTypeId.val(obj.consignment_charges_types_id).trigger('change');
                        
                        $("#charges").val(obj.charge);
                        $('#chargesType').val(obj.charge_type);
                        $('#chargesType').selectpicker('refresh');
                    },
                    error: function (xhr, status, error) {

                    }
                });
            });

            $(document).on('change','#chargesTypeId',function(){
				var applyBy = $("#chargesTypeId option:selected").data("apply_by");
				$("#charges_container").show();
                $(".country_container").hide();
				if(applyBy == 'country'){
                    $('#add_country_charges_row').html('');
					$(".country_container").show();
				    $("#charges_container").hide();
                    addCountryChargesRow();
				}
			});

            $(document).on('click','#download_country_template',function(){
                var form_data = $("#tariffAdditionalChargesForm").serialize();
                var jsonStr = JSON.stringify(form_data);
                $('#download_country_form_input').val(form_data);
                $('#download_country_form').submit();
            });

            $(document).on('click', '#btnSave', function () {
                var form_data = $("#tariffAdditionalChargesForm").serializeArray();
                form_data.push({name: "action", value: "saveTariffAdditionalCharges"});
                $.ajax({
                        type: "POST",
                        url: 'tariff_additional_charges.php?tariff_id=<?php echo $this->tariff_id; ?>',
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            if(data.status == "success") {
                                // $('#tariffAdditionalChargesForm').trigger("reset");
                                // $("#tariff_additional_charges_id").val("");
                                grid.getDataTable().ajax.reload();
                                swal("Success!", data.message, "success");
                            } else {
                                swal("Sorry!", data.message, "error");
                            }
                        },
                        error: function () {
                                //alert('error handing here');
                        }
                });
            });

            function addCountryChargesRow() {
                var form_data = $("#tariffAdditionalChargesForm").serializeArray();
                form_data.push({name: "action", value: "getTariffZoneForCharge"});
                $.ajax({
                    type: "POST",
                    url: 'tariff_additional_charges.php?tariff_id=<?php echo $this->tariff_id; ?>',
                    data: form_data,
                    dataType: "json",
                    success: function (data) {
                        if(data.status == "success") {
                            $('#add_country_charges_row').html(data.html);
                            $('.bs-select').selectpicker({
                                'liveSearch':true,
                                container: 'body'
                            });
                            $('.selectpicker').selectpicker({
                                'liveSearch':true,
                                container: 'body'
                            });
                        } else {
                            swal("Sorry!", data.message, "error");
                        }
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
        </script>
        <!--End Hadi Code-->
        <?php
    }

    protected function renderHead() {
        ?>	
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-docs"></i>Tariff Additional Charges </div>
                <div class="actions">
                    <a href="javascript:;" class="btn blue tariff_csv_box country_container" id="download_country_template" ><i class="fa fa-download"></i> Template</a>
                </div>
            </div>
            <form id="tariffAdditionalChargesForm" name="tariffAdditionalChargesForm" action="tariff_additional_charges.php?tariff_id=<?php echo $this->tariff_id; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tariff_additional_charges_id" id="tariff_additional_charges_id" value="" >
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="col-md-12 alert alert-success" id="success_message" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" >
                            <?php
                                $this->flashMsg->display();
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tariff Name </label>
                            <div class="input-group">
                                <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                <?php 
                                $userAccount = $this->user->getUserAccountId();
                              
                                $tariffIdType = " user_account_id = '".$userAccount."' ". ($this->tariff_id > 0 ? " and id = ". $this->tariff_id :'' );
                                echo Ddl::generateDDL('tariff_id', 'TariffsFilter',$tariffIdType  , 'name', 'id', $this->tariff_id, ' class="form-control select2 " data-toggle="tooltip" data-placement="top" title="User Account" data-original-title="User Account"', 'Please Select', '', 'tariff_id', 'Tariff'); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label>Charges Type</label>
							<select name="chargesTypeId" id="chargesTypeId" class="form-filter select2 form-control">
                            <?php
                                $chargesTypesFilter = new ConsignmentChargesTypesFilter();
                                $tpyeWhere = " cct.charge_type <> 'customer'";
                                if(trim($this->tariffsObj->getTariffType()) == 'customer')
                                    $tpyeWhere = " cct.charge_type <> 'agent'";
                                
                                $chargesTypesFilter->addFilter("   $tpyeWhere and cct.status='1' and is_delete = '0' ");
                                $chargesTypesFilterObj = $chargesTypesFilter->getList('*');
                                $chargesArray = [];
                                $chargesArray[""] = "Select Charges Title";
                                if(count($chargesTypesFilterObj) > 0) {
                                    foreach($chargesTypesFilterObj as $charges) {
                                        //$chargesArray[$charges->getId()] = $charges->getTitle();
								?>
										<option value="<?php echo $charges->getId(); ?>" data-apply_by="<?php echo $charges->getApplyBy(); ?>"><?php echo $charges->getTitle(); ?></option>
								<?php
                                    }
                                }
                                //echo Ddl::generateArrayDDL('chargesTypeId', $chargesArray, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'chargesTypeId');
                            ?>
							</select>
                        </div>
                        <div class="col-md-3" id="charges_container">
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
						<div class="col-md-6 country_container">
							<div class="fileinput fileinput-new" data-provides="fileinput">
								<div class="form-group">
									<label> Upload Your Document</label>
									<div class="input-group input-large">
										<div class="form-control uneditable-input input-fixed input-medium"
											 data-trigger="fileinput">
											<i class="fa fa-file fileinput-exists"></i>&nbsp;
											<span class="fileinput-filename"> </span>
										</div>
										<span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new"> Select file </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="csv_file" id="csv_file">
                                        </span>
										<a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
										<a href="javascript:;" class="input-group-addon btn blue" id="upload_csv">Upload</a>
									</div>
								</div>
							</div>
						</div>
                        <div class="col-md-12 country_container">
                            <div class="row" style="margin-top: 15px;">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Charges</th>
                                                    <th>Formula</th>
                                                </tr>
                                            </thead>
                                            <tbody id="add_country_charges_row"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="row">
						<div class="col-md-4">
							<label>&nbsp;</label><br />
							<button type="button" id="btnSave" class="btn btn-primary btn_save"><span></span>Save</button>
						</div>
					</div>

                </div>
            </form>
        </div>
        <form method="post" id="download_country_form" action="tariff_additional_charges.php?tariff_id=<?php echo $this->tariff_id; ?>">
            <input type="hidden" name="action" value="download_country_form">
            <input type="hidden" name="download_country_form" id="download_country_form_input">
        </form>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-dropbox"></i>
                    List
                </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="10%">Actions</th>
                                <th>Tariff</th>
                                <th>Charges Title</th>
                                <th>Charges</th>
                                <th>Charges Type</th>
                                <th>Created Date</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs btn-default blue btn-outline pull-left filter-submit"><i class="fa fa-search"></i> </button>
                                        <button class="btn btn-xs btn-default red btn-outline pull-left filter-cancel"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <?php   
                                            $userAccount = $this->user->getUserAccountId();
                                            $tariffIdType = " user_account_id = '".$userAccount."' ". ($this->tariff_id > 0 ? " and id = ". $this->tariff_id :'' );
                                            echo Ddl::generateDDL('search_tariff_id', 'TariffsFilter', $tariffIdType , 'name', 'id', ($this->tariff_id > 0 ? $this->tariff_id :'' ), ' class="form-control select2" data-toggle="tooltip" data-placement="top" title="User Account" data-original-title="User Account"', 'Please Select', '', 'search_tariff_id', 'Tariff'); 
                                            ?>
                                </td>
                                <td>
                                    <?php echo Ddl::generateArrayDDL('consignment_charges_types_id', $chargesArray, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'consignment_charges_types_id'); ?>
                                </td>
                                <td><input type="text" class="form-control form-filter form-control" name="charge"></td>
                                <td>
                                    <select class="form-filter select2" name="charge_type" id="charge_type" >
                                        <option value="">select Charges Type</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter" readonly name="date_created_from" placeholder="From">
                                        <span class="input-group-btn">
                                            <button class="btn btn-md default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter" readonly name="date_created_to" placeholder="To">
                                        <span class="input-group-btn">
                                            <button class="btn btn-md default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>	
            </div>
        </div>
        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="more_option" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Pricing Break Down</h4>
                    </div>
                    <div class="modal-body">
                        <form id="update_more_option_frm" name="update_more_option_frm" method="post">
                            <div class="alert alert-success hidden" id="update_remote_msg">Enter New Account</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_REMOTE" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <?php
                                        $userExtraColumn = UserServicesCharges::extraDetailsCharges();
                                        if (count($userExtraColumn) > 0) {
                                            $countForLoop = 0;
                                            ?><div class="row"><?php
                                            foreach ($userExtraColumn as $key => $value) {
                                                ?>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label font-green-soft"><?php echo $value ?>（GBP/KG):</label> 
                                                            <div class="input-group">
                                                                <div class="input-group-addon"> <i class="fa fa-money"></i> </div>
                                                                <input type="text" name="extraPric[<?php echo $key; ?>]" id="<?php echo $key ?>" data-key="<?php echo $key; ?>" value="0.00" class="form-control input-sm customer_charges" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if ($countForLoop % 3 == 0)
                                                        echo '<div stype="clear:both;"></div>';
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>	
                                    </fieldset>
                                </div>

                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateAdditionalPricingData" name="updateAdditionalPricingData">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
