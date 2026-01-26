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
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'services.class',
    'servicefilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmentrelabel.class',
    'consignmentrelabelfilter.class',
    'carrier.class',
    'carrierfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    
    
]);
class Page extends BasePage {

    private $user;

    /*     * *
     * Controller logic
     */

    protected function init() {

        // Session check
        $this->user = SessionManager::getUser();
//        if ($this->user->getUserType() != User::USER_TYPE_CORPORATE && $this->user->getUserType() != User::USER_TYPE_ADMIN ) {
//            util_redirect("index.php");
//        }
        //BreadCrum
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            '#.php' => 'Parent Page',
            '1.php' => "Page"
        );

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_carrier_services") {
            if (isset($this->form_vars['user_account_id']) && $this->form_vars['user_account_id'] > 0) {
                $userAccountId = $this->form_vars['user_account_id'];
                $consignmentList = array();
                if (!empty($userAccountId)) {
                    $return = array();
                    $userAccountArry = CustomerAccount::accountSubAccount($userAccountId, 0, true);
                    $userServicesRoutingFilter = new UserServicesRoutingFilter();
                    $userServicesRoutingFilter->addFilterIn("    user_account_id", $userAccountArry);
                    $userServiceObj = $userServicesRoutingFilter->getColumnList(" service_id", false, true);
                    $serviceIds = array();
                    foreach ($userServiceObj as $service) {
                        $serviceIds[] = $service->getServiceId();
                    }
                }
                $serviceIds = array_unique($serviceIds);
                $serviceFilterObj = new ServiceFilter();
                if (!empty($serviceIds)) {
                    $serviceFilterObj->addFilterIn("ser.id", $serviceIds);
                }
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
                $carrierIds = array();
                foreach ($services as $ser) {
                    if (!in_array($ser->getCarrierId(), $carrierIds)) {
                        $carrierIds[] = $ser->getCarrierId();
                    }
                }
                $carrierFilterObj = new CarrierFilter();
                $carrierFilterObj->addFilterIn("c1.id", $carrierIds);
                $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
                $service_option = "<option value=''>Select Service</option>";
                foreach ($services as $sr) {
                    $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
                }
                $carrier_option = "<option value=''>Select Carrier</option>";
                foreach ($carriers as $cr) {
                    $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
                }
                $parcelFilterForAgentIds = new ConsignmentFilter();
                $parcelFilterForAgentIds->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
                $consignmentAgentList = $parcelFilterForAgentIds->getColumnList("DISTINCT(c.agent_id)");
                $agentIds = array();
                foreach ($consignmentAgentList as $obj) {
                    $agentIds[] = $obj->getAgentId();
                }
                $agentFilter = new AgentDataFilter();
                $agentFilter->addAgentIdInFilter($agentIds);
                $agentFilterObj = $agentFilter->getColumnList('   a.agent_name,a.agent_code');
                $agent_option = "<option value=''>Select Agent</option>";
                foreach ($agentFilterObj as $obj) {
                    $agent_option .= "<option value='" . $obj->getId() . "'>" . $obj->getAgentName() . "</option>";
                }
                $return['services_option'] = $service_option;
                $return['carrier_option'] = $carrier_option;
                $return['agent_option'] = $agent_option;
                echo json_encode($return);
            }
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "add_manifest"){
            $conData = [];
            $parcelId = "";
            $conId = "";
            $conData = $this->form_vars['con_data'];
            if(count($conData) > 0){
                foreach ($conData as $arr) {
                    $parcelId = $arr['parcelId'];
                    $conId = $arr['conId'];
                    //Check if parcels are already in manifest
                    $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
                    $manifestEntityMappingFilter->addFieldFilter($colm, $value);
                }
            }
        }
        if (isset($_GET['action']) && $_GET['action'] == "data_table_ajax") {
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addConsignmentTableJoin("LEFT");
            $parcelFilter->addNotEqualEmptyFilter("p.tracking_number");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                if(!empty($this->form_vars['service'])){
                    $service = $this->form_vars['service'];
                }else{
                    if($this->user->getUserType() == USER::USER_TYPE_CORPORATE){
                        $user_account_id = $this->user->getUserAccountId();
                        $data = Carrier::getCarriersServersFromUserAccount($user_account_id,'',true);
                        $service = $data['serviceIds'];
                    }
                }
                if (!empty($service)){
                    $parcelFilter->addTrackingNumberFilterIn('join_con.service_id', $service);
                }

                $mawb = $this->form_vars['mawb'];
                if (!empty($mawb))
                    $parcelFilter->addFieldFilter('join_con.mawb', $mawb);

                $dateType = $this->form_vars['date_type'];
                if (!empty($dateType)) {
                    if (!empty($this->form_vars["from_date"])) {
                        $dateFrom = date("Y-m-d", strtotime($this->form_vars["from_date"]));
                    }
                    if (!empty($this->form_vars["to_date"])) {
                        $dateTo = date("Y-m-d", strtotime($this->form_vars["to_date"]));
                    } else {
                        $dateTo = date("Y-m-d");
                    }
                    if (!empty($this->form_vars["from_date"]) || !empty($this->form_vars["to_date"])) {
                        $parcelFilter->addDateFilter($dateFrom, $dateTo, $dateType);
                    }
                }
                $trackingNumberForStatus = $this->form_vars['tracking_number_for_status'];
                if (!empty($trackingNumberForStatus)){
                    $trackingNumbers = array_map('trim',explode("\n", str_replace("\r", "", $trackingNumberForStatus)));
                    $parcelFilter->addTrackingNumberFilterIn('p.tracking_number', $trackingNumbers);
                }else if(!empty($this->form_vars['search_Tracking'])){
                    $trackingNumbers = $this->form_vars['search_Tracking'];
                    $parcelFilter->addTrackingNumberFilter($trackingNumbers);
                }
//                if(!empty($this->form_vars['search_Weight'])){
//                    $weight = $this->form_vars['search_Weight'];
//                    $parcelFilter->addFieldFilter('join_con.weight', $weight);
//                }
//                if(!empty($this->form_vars['search_service'])){
//                    
//                }
                if(!empty($this->form_vars['search_Status'])){
                    $status = $this->form_vars['search_Status'];
                    $parcelFilter->addFieldLikeFilter('p.owe_status_code',$status);
                }
                
            }
            if(!empty($this->form_vars['action']) && $this->form_vars['action'][0] == 'filter_cancel'){
                if($this->user->getUserType() == USER::USER_TYPE_CORPORATE){
                    $user_account_id = $this->user->getUserAccountId();
                    $data = Carrier::getCarriersServersFromUserAccount($user_account_id,'',true);
                    $service = $data['serviceIds'];
                    $parcelFilter->addTrackingNumberFilterIn('join_con.service_id', $service);
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
                if ($dataTableColumnName == "Logo") {
                    $dataTableColumnName = "Name";
                } else if ($dataTableColumnName == "Tracked") {
                    $dataTableColumnName = "is_untrack";
                }
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $parcelFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $parcelFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $parcelFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $parcelFilter->setOffset($iDisplayStart);
            $parcelObjs = $parcelFilter->getPagingList('p.id,p.tracking_number,p.length,p.width,p.height,p.weight,p.description,p.parcel_message,p.qty,p.commoditycode,p.grossweight,p.pweight,p.itemvalue,p.number_item,p.tarrif_no,p.update_weight,p.owe_status_code,p.chute_sorted,p.parcel_status_code,p.routing_code,p.last_tracking_update,p.consignment_id');
            $parcelDatatArr = array();

            foreach ($parcelObjs as $parcelObj) {
                $parcelArr = array();
                $serviceObj = new Services($parcelObj->getNumberItem());
                $parcelArr['tracking'] = $parcelObj->getTrackingNumber();
                $parcelArr['service'] = ucfirst(strtolower($serviceObj->getName()));
                $parcelArr['dims'] = $parcelObj->getLength() . " X " . $parcelObj->getWidth() . " X " . $parcelObj->getHeight();
                $parcelArr['weight'] = $parcelObj->getWeight();
                $parcelArr['status'] = ucfirst(strtolower($parcelObj->getOweStatusCode()));

                $parcelArr['option'] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type=checkbox id= "delete55" name="add_manifest[]"  value="' . $parcelObj->getConsignmentId() . '" data-parcel_id="' . $parcelObj->getId() . '"  class="manifest_chk group-checkable" /><span></span></label>';
                $parcelDatatArr [] = $parcelArr;
            }
            if (!isset($this->form_vars['action']) && $this->form_vars['action'] != 'filter') {
                $parcelDatatArr = array();
                $sEcho = 0;
                $iTotalRecords = 0;
            }
            $parcelDatatArrJson['data'] = $parcelDatatArr;
            $parcelDatatArrJson['draw'] = $sEcho;
            $parcelDatatArrJson['recordsTotal'] = $iTotalRecords;
            $parcelDatatArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($parcelDatatArrJson);
            die;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            #select2-service-results .select2-results__option[aria-disabled=true] {
                display: none;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>

                                                        <!--        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
                                                                <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        -->        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
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
                                "url": "op_generate_manifest.php?action=data_table_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "tracking"},
                                {"data": "service"},
                                {"data": "dims", "bSortable": false},
                                {"data": "weight"},
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
            // Reload data table command
            //                grid.getDataTable().ajax.reload();
        </script>
        <!--End Hadi Code-->
        <script type="text/javascript">
            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                    /* Custom filtering function which will search data in column four between two values */
                    $('#btn_go').click(function () {
                        $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                            grid.setAjaxParam($(this).attr("name"), $(this).val());
                        });
                        // get all checkboxes
                        $('input.form-filter[type="checkbox"]:checked').each(function () {
                            grid.addAjaxParam($(this).attr("name"), $(this).val());
                        });
                        // get all radio buttons
                        $('input.form-filter[type="radio"]:checked').each(function () {
                            grid.setAjaxParam($(this).attr("name"), $(this).val());
                        });
                        grid.submitFilter();
                    });
                }
                get_carriers("<?php echo $this->user->getUserAccountId(); ?>");
                
                $("#down_summary").click(function(){
                    var dataCon = [];
                    var conId = "";
                    var parcelId = "";
                    $(".manifest_chk:checked").each(function(){
                        conId = $(this).val();
                        parcelId = $(this).data("parcel_id");
                        dataCon.push({parcelId:parcelId,conId:conId});
                     });
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "op_generate_manifest.php",
                        data: {action: "add_manifest", con_data: dataCon},
                        success: function (data) {
                            
                            $.unblockUI();
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                });
                
                
            });
            function get_carriers(userAccountId) {
                $.blockUI();
                $.ajax({
                    type: "POST",
                    url: "op_generate_manifest.php",
                    data: {action: "get_carrier_services", user_account_id: userAccountId},
                    dataType: "json",
                    success: function (data) {
                        $('#carriers').html(data.carrier_option);
                        $('#service').html(data.services_option);
                        $('#carriers').select2();
                        $('#service').select2();
                        $('#carriers').trigger('change');
                        $.unblockUI();
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            function change_carriers(carrierId) {
                $('.serviceOption').attr("disabled", "disabled");
                $('.carrier_' + carrierId).removeAttr("disabled");
                $('#service').select2();
            }
            $('#carriers').change(function () {
                var select2service = $("#service").select2();
                select2service.val($('#service option:first').val()).trigger('change');
                var carrierId = $(this).val();
                change_carriers(carrierId);
            });
            var count = 0;
            function checkedAll(group) {
                if (count == 0) {
                    for (var i = 0, len = group.length; i < len; i++) {
                        if (group[i].checked == false)
                            group[i].click(); //checked = true;
                        count = 1;
                    }
                } else {
                    for (var i = 0, len = group.length; i < len; i++) {
                        if (group[i].checked == true)
                            group[i].click(); // = false;
                        count = 0;
                    }
                }
            }
        </script>
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-bar-chart"></i>
                    Manifest Search                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label> Select Carrier</label>
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-building"></i> </span>
                                <div class="input-icon right" id="carriers_div">
                                    <?php echo Ddl::generateArrayDDL('carriers', array("" => "Select Carrier"), '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'carriers'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label> Select Service</label>
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-building"></i> </span>
                                <div class="input-icon right" id="service_div">
                                    <?php echo Ddl::generateArrayDDL('service', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> MAWB</label>
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-building"></i> </span>
                                <div class="input-icon right">
                                    <input id="mawb" name="mawb" type="text" value="" required="" placeholder="MAWB" class="form-control form-filter tooltipbutton" data-toggle="tooltip" data-placement="top" title="MAWB" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Date Type</label>
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-building"></i> </span>
                                <div class="input-icon right">
                                    <?php
                                    $IsStatusArr = array('submitted' => 'Submitted', 'printed' => 'Printed', 'shipped' => 'Shipped', 'delivered' => 'Delivered');
                                    echo Ddl::generateArrayDDL('date_type', $IsStatusArr, "", '', ' class="form-filter form-control select2 select" rel="tooltip" data-original-title="Status" placeholder="Date Type"');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label"> Date Ranges </label>
                        <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="dd-mm-yyyy">
                            <input type="text" class="form-filter form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? formatDate($_POST['from_date']) : ''); ?>" data-original-title="" title="">
                            <span class="input-group-addon"> to </span>
                            <input type="text" class="form-filter form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) ? formatDate($_POST['to_date']) : ''); ?>" data-original-title="" title="">
                        </div>
                    </div> 
                </div>
                <div class="row margin-top-10">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Please enter Tracking Number</label>
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-building"></i> </span>
                                <textarea rows="4" cols="50" style="resize:none;" name="tracking_number_for_status" id = "tracking_number_for_status"  value= "" class="form-filter form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="text-align:center;">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" name="search" id="btn_go" value="Search"> Search </button>
                        </div>
                    </div>
                </div>
            </div><!--portlet-body-->
        </div>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-dropbox"></i>
                    Shipment List
                </div>
                <div class="actions">
                    <a href="javascript:;" class="btn btn-default" id="down_csv" data-original-title="" title="">
                        Export CSV
                    </a>
                    <a href="javascript:;" class="btn btn-default" id="down_summary" data-original-title="" title="">
                        Summary
                    </a>
                    <a href="javascript:;" class="btn btn-default" id="down_pdf" data-original-title="" title="">
                        Export PDF
                    </a>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type='checkbox' name='checkall' onclick='checkedAll(delete55);' class="group-checkable"/>
                                        <span></span>
                                    </label>
                                </th>
                                <th>Tracking Number</th>
                                <th>Service</th>
                                <th>Dims</th>
                                <th>Weight</th>
                                <th>Status</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_Tracking">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_service">
                                </td>
                                <td></td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_Weight">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_Status">
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>	
            </div>
        </div>
        <?php
    }

    public function renderFooter() {
        ?>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
