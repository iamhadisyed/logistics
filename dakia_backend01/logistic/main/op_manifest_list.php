<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([  
                    'invoices.class',
                    'invoicesfilter.class',
                    'iaddress.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'parcel.class' ,
                    'parcelfilter.class',
                    'manifestentitymapping.class',
                    'manifestentitymappingfilter.class'
                        ]);

class Page extends BasePage {
    /* 
     * Controller logic
     */
    
    private $manifestFilter = array();
    private $user = '';
    private $heading = '';

    protected function init() {
        $this->heading = "Operation Manifest List";
        if(!empty($_GET) && $_GET['manifest'] == 'upcomming'){
            $this->heading = "Upcoming Manifest";
        }
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            $this->heading
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        
        $this->manifestFilter = new ManifestFilter();
        if(!isset($_GET['manifest'])){
            $this->manifestFilter->addFieldFilter("    m.manifest_by", "operation");
            $this->manifestFilter->addFieldFilter("    u.user_account_id", $this->user->getUserAccountId());
        }
            
        if (isset($_GET['action']) && $_GET['action'] == "manifest_ajax") {
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                if(isset($_GET['manifest']) && $_GET['manifest']=="upcomming"){
                    $this->applyFilter($this->form_vars,true);
                } else {
                    $this->applyFilter($this->form_vars);
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
                $this->manifestFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            } else {
                if(isset($_GET['manifest']) && $_GET['manifest']=="upcomming"){
                    $this->manifestFilter->AddOrderBy(strtolower('manifest_id'), false);
                } else {
                    $this->manifestFilter->AddOrderBy(strtolower('m.id'), false);
                }
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $manifest_id = $_GET['manifest_id'];
            if (!empty($manifest_id)){
                if(isset($_GET['manifest']) && $_GET['manifest']=="upcomming"){
                  $this->manifestFilter->addFieldFilter('   id', $manifest_id);  
                }else{
                    $this->manifestFilter->addFieldFilter('   m.id', $manifest_id);
                }
            }
            if(isset($_GET['manifest']) && $_GET['manifest']=="upcomming"){
                $iTotalRecords = count($this->manifestFilter->getUpCommingPagingCount($this->user->getUserAccountId()));
            }else{
                $iTotalRecords = $this->manifestFilter->getPagingCount();
            }
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->manifestFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->manifestFilter->setOffset($iDisplayStart);
            if(isset($_GET['manifest']) && $_GET['manifest']=="upcomming"){
                $manifestObjs = $this->manifestFilter->getUpCommingPagingList($this->user->getUserAccountId());
            }else{
                $manifestObjs = $this->manifestFilter->getOpPagingList();
            }
            $setDataArr = array();
            foreach ($manifestObjs as $manifestObj) {
                $currentArr = array();
                $service = new Services($manifestObj->getServiceId());
                if (!empty($manifestObj->getDateCreated())) {
                    $date_created = formatDate(date("d-m-Y", $manifestObj->getDateCreated()));
                } else {
                    $date_created = "";
                }
                
                $currentArr['date_created'] = $date_created;
                $manifestTracking = explode(",",$manifestObj->getPieces());
                if(count($manifestTracking) > 0){
                    $count = 0;
                    $manifesthtml = '<tr>';
                    foreach ($manifestTracking as $manifestTrackingNumber) {
                        if($count % 3 == 0){
                            $manifesthtml .= '</tr><tr>';
                        }
                        $manifesthtml .= '<td>';
                        $manifesthtml .= $manifestTrackingNumber;
                        $manifesthtml .= '</td>';
                        $count++;
                    }
                    $manifesthtml .= '</tr>';
                }
                $currentArr['op_manifest_list'] = '<a class="show_tracking_modal" href="javascript:;" data-tracking="'.sprintf('%010d', $manifestObj->getId()).'" data-tracking_number="'.$manifesthtml.'">'.$manifestTracking[0].'...</a>';
//                $currentArr['manifest_id'] = sprintf('%010d', $manifestObj->getId());
                $currentArr['manifest_id'] = sprintf('%010d', $manifestObj->getId());
                $currentArr['service_id'] = $service->getName();
                $currentArr['weight'] = $manifestObj->getWeight();
                if($manifestObj->getIsDispatched() == 'N'){
                    $currentArr['is_dispatched'] = '<span class="label label-sm label-danger"> <strong>No</strong> </span>';
                }
                else{
                    $currentArr['is_dispatched'] = '<span class="label label-sm label-success"> <strong>Yes</strong> </span>';
                }
                
                if($manifestObj->getIsSendEmail() == 'N'){
                    $currentArr['is_send_email'] = '<span class="label label-sm label-danger"> <strong>No</strong> </span>';             }  
                else{
                    $currentArr['is_send_email'] = '<span class="label label-sm label-success"> <strong>Yes</strong> </span>';
                }
                $currentArr['actions'] = '<div class="btn-group" data-container="body">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                                <ul class="dropdown-menu" >
                                                    <li>
                                                        <a data-manifest_id="'.sprintf('%010d', $manifestObj->getId()).'" data-service_name="'.$service->getName().'" class="view_detail" href="JavaScript:Void(0);" title="View Details">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="../_assets/manifest/csv/'.$manifestObj->getFileName().'" title="View CSV" target="_blank" >
                                                            <i class="fa fa-download"></i> Download CSV
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="../_assets/manifest/pdf/'.$manifestObj->getPdfFile().'" title="View PDF" target="_blank" >
                                                            <i class="fa fa-download"></i> Download PDF
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            ';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_manifest_detail"){
            $manifestId = $this->form_vars['manifest_id'];
            $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
            $manifestEntityMappingFilter->addFieldFilter("     mem.manifest_id",$manifestId);
            $manifestEntityMappingFilter->addFieldFilter("     mem.manifest_entity_type","p");
            $manifestEntityList = $manifestEntityMappingFilter->getList();
            $html = "";
            if(count($manifestEntityList) > 0) {
                foreach($manifestEntityList as $key => $parcelId) {
                    $parcel = new Parcel($parcelId->getEntityId());
                    $html .= "<tr>";
                            $html .= "<td> " . ($key + 1) . "</td>";
                            $html .= "<td>" . $parcel->getTrackingNumber() . "</td>";
                            $html .= "<td>" . $parcel->getLength()." x ".$parcel->getWidth()." x ".$parcel->getHeight() . "</td>";
                            $html .= "<td>" . $parcel->getWeight() . "</td>";
                            $html .= "<td><span class='label label-sm label-info'>" . ucwords(Consignment::$database_status_array[$parcel->getParcelStatusCode()]) . "</span></td>";
                    $html .= "</tr>";
                }
            } else {
                $html .= "<tr>";
                    $html .= "<td> NO Parcel Found </td>";
                $html .= "</tr>";
            }
            echo $html;
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "download_operation_manifest_list_csv"){
            $output = array();
            
            if(isset($_GET['manifest']) && $_GET['manifest']=="upcomming"){
                $this->applyFilter($this->form_vars,true);
                $manifestObjs = $this->manifestFilter->getUpCommingPagingList($this->user->getUserAccountId());
            }else{
                $this->applyFilter($this->form_vars);
                $manifestObjs = $this->manifestFilter->getOpPagingList();
            }
            
            if(count($manifestObjs) > 0){
                $csvData = $this->operationManifestCsv($manifestObjs);
                $output['path'][] = $csvData;
                die;
            }
            else{
                 $this->flashMsg->error("No manifest to download."); 
            }
                
        }
        
    }
    
    protected function applyFilter($data,$upcomming=false) {
        $this->form_vars = $data;
        $date_created_from = $this->form_vars['date_created_from'];
        $date_created_to = $this->form_vars['date_created_to'];
        if (!empty($date_created_from) && !empty($date_created_to)){
            $this->manifestFilter->addDateFilter($date_created_from, $date_created_to,$upcomming);
        }
        $manifestId = $this->form_vars['manifest_id'];
        if (!empty($manifestId)){
            if($upcomming){
              $this->manifestFilter->addFieldFilter('   id', $manifestId);  
            }else{
                $this->manifestFilter->addFieldFilter('   m.id', $manifestId);
            }
        }
        $serviceId = $this->form_vars['service_id'];
        if (!empty($serviceId))
            $this->manifestFilter->addFieldFilter('    service_id', $serviceId);

        $manifestWeight = $this->form_vars['manifest_weight'];
        if (!empty($manifestWeight))
            $this->manifestFilter->addFieldFilter('    weight', $manifestWeight);

        $idDispacthed = $this->form_vars['id_dispacthed'];
        if (!empty($idDispacthed))
            $this->manifestFilter->addFieldFilter('is_dispatched', $idDispacthed);

        $idEmail = $this->form_vars['id_email'];
        if (!empty($idEmail))
            $this->manifestFilter->addFieldFilter('is_send_email', $idEmail);

        $trackingNumber = $this->form_vars['tracking_number'];
        $trackingNumber = array_map('trim',explode("\n", str_replace("\r", "", $trackingNumber)));
        if (isset($trackingNumber[0]) && !empty($trackingNumber[0]))
            $this->manifestFilter->addFilterIn('    p.tracking_number', $trackingNumber);
    }
    
    protected function operationManifestCsv($manifestObjs) {
        $returnData = array();
        $chargesTotal = array();
        $heading = ["Date Created", "Manifest Id", "Service", "Weight", "Is Dispatched", "Is Email Send"];

        $fileName = "manifest_" . time(). ".csv";
        if(count($manifestObjs) > 0) {
            $returnString = "";
            foreach ($heading as $h) {
                $returnString .= $h . ",";
            }       
            foreach($manifestObjs as $manifestObj) {
                $returnString .=  "\r\n";
                 if (!empty($manifestObj->getDateCreated())) {
                    $date_created = date("d-m-Y", $manifestObj->getDateCreated());
                } else {
                    $date_created = "";
                }
                $returnString .=  $date_created . ",";
                $returnString .=  cleanCsvCall(sprintf('%010d', $manifestObj->getId())) . ",";
                $service = new Services($manifestObj->getServiceId());
                $returnString .=  cleanCsvCall($service->getName()) . ",";
                $returnString .=  cleanCsvCall($manifestObj->getWeight()) . ",";
                if($manifestObj->getIsDispatched() == 'N'){
                    $is_dispatched = 'No';
                }
                else{
                    $is_dispatched = 'Yes';
                }
                $returnString .=  cleanCsvCall($is_dispatched) . ",";
                if($manifestObj->getIsSendEmail() == 'N'){
                    $is_send_email = 'No';             }  
                else{
                    $is_send_email = 'Yes';
                }
                $returnString .=  cleanCsvCall($is_send_email) . ",";

            }
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $fileName);
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $returnString;
        $returnData['FILENAME'] = $fileName;
        return $returnData;
        die;
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
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
                                "url": "op_manifest_list.php?action=manifest_ajax<?php if(isset($_GET['manifest']) && $_GET['manifest'] == "upcomming") echo "&manifest=upcomming"; ?><?php if(isset($_GET['manifest_id']) && $_GET['manifest_id'] != "") echo "&manifest_id=".$_GET['manifest_id']; ?>", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                        {"data": "actions", "bSortable": false},
                                        {"data": "date_created"},
                                        {"data": "manifest_id"},
                                        {"data": "op_manifest_list"},
                                        {"data": "service_id"},
                                        {"data": "weight"},
                                        {"data": "is_dispatched"},
                                        {"data": "is_send_email"}
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
                <?php if(!empty($_GET['dispatch']) && $_GET['dispatch']=="yes"){ ?>
                    $(".filter-submit").click();
                <?php } ?>
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
            //functin to handle view details
            $(document).on('click', '.view_detail', function () {
                var manifestId = "";
                var serviceName = "";
                manifestId = $(this).data("manifest_id");
                serviceName = $(this).data("service_name");
                $("#modal_mawb_number").html(manifestId);
                $("#modal_mawb_service").html(serviceName);
                $.ajax({
                    type: "POST",
                    url: "op_manifest_list.php",
                    data: {action:'get_manifest_detail',manifest_id:manifestId},
                    success: function (data) {
                        $('#manifest_list_data').html(data);
                        $('#mawb_detail_modal').modal('show');
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            });
            $('#btnSave').click(function(){
                if($("#add_carrier_id").val() == ""){
                   swal("","Please select Carrier name", "info");
                }
                else if($("#carrier").val() == ""){
                   swal("","Please enter group Name", "info");
                }else{
                    $("#form_action").val("save");
                     $.post("remoteareas_groups.php",$("#adminForm").serialize(),function(response){
                        if(response.STATUS == "success"){
                            $("#res_message").html("Remoteareas Group added successfully");
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                            $("#group_name").val("");
                            $('#add_carrier_id').val("");
                        }else{
//                            $("#abc").html(response.message);
                        }
                    },"json");
                }
            });
            $(document).on('click','.btnedit',function(){
                var groupId = $(this).attr("data-group_id");
                $.ajax({
                    type: "POST",
                    url: "remoteareas_groups.php",
                    data: {action:"edit",group_id:groupId},
                    dataType: "json",
                    success: function(data) {
                        if(data.STATUS=="success"){
                            $("#add_carrier_id").val(data.carrier_id);
                            $("#group_name").val(data.group_name);
                            $("#id").val(data.group_id);
                        }else{
                        }
                    },
                    error: function() {
                        alert('error handing here');
                    }
                });
            });
            //functin to handle view details
            $(document).on('click', '.show_tracking_modal', function () {
                var trackingNumbers = $(this).data('tracking_number');
                var trackingNumber = $(this).data('tracking');
                $("#tracking_number_data").html(" ");
                $("#tracking_number_modal").modal("show");
                $("#tracking_number_data").html(trackingNumbers);
                $("#tracking_head").html("");
                $("#tracking_head").html(trackingNumber);
            });
            function get_csv_operation_manifest_list() {
                $('#operation_manifest_list_form').submit();
            }
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
                   <?= $this->heading;?>
                </div>
                <div class="actions">
                    <!--<a href="#" class="btn blue"  ><i class="fa fa-plus"></i> Add New</a>-->
                    <button class="btn btn-sm btn-default table-group-action-submit" id="csv_download_operation_manifest_list" onclick="get_csv_operation_manifest_list()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php $this->flashMsg->display(); ?>
                </div>
                <!--Hadi Code-->
                <?php 
                 if($_GET['manifest'] == "upcomming"){
                     $querystring = "?manifest=upcomming";
                 }
                ?>
                <form method="post" action="op_manifest_list.php<?=$querystring;?>" id="operation_manifest_list_form">
                    <input type="hidden" name="action" value="download_operation_manifest_list_csv" />
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Date Created</th>
                            <th>Manifest Id</th>
                            <th>Tracking No.</th>
                            <th>Service Id</th>
                            <th>Weight</th>
                            <th>Is Dispatched</th>
                            <th>Is Email Sent</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td  class="user_acccount_correct_button">
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td class="user_acccount_correct_button">
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td class="user_acccount_correct_button">
                                <input type="text" class="form-control form-filter input-xs" name="manifest_id" id ="manifest_id" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <textarea name="tracking_number" id="tracking_number" class="form-control form-filter input-xs" ></textarea>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                $serviceid = "";
                                echo Ddl::generateServiceDDLWithImage('service_id', $selected_value, 'id', ' class="bs-select input-sm form-control form-filter" required="" data-live-search="true" data-show-subtext="true" ', '', '', 'name', 'Select Services');
                                ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <input type="text" class="form-control form-filter input-xs" name="manifest_weight" id ="manifest_weight" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                    $IsDispatchArr = array('' => 'Please Select','Y' => 'Yes', 'N' => 'No');
                                    $default = "";
                                    if(!empty($_GET['dispatch']) && $_GET['dispatch']=="yes"){
                                        $default = "Y";
                                    }
                                    echo Ddl::generateArrayDDL('id_dispacthed', $IsDispatchArr, $default, '', ' class="form-filter form-control select2 select" rel="tooltip" data-original-title="Is Dispatched" placeholder="Is Dispatched"');
                                ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php
                                    $IsEmailArr = array('' => 'Please Select','Y' => 'Yes', 'N' => 'No');
                                    $default = "";
                                    echo Ddl::generateArrayDDL('id_email', $IsEmailArr, $default, '', ' class="form-filter form-control select2 select" rel="tooltip" data-original-title="Is Dispatched" placeholder="Is Dispatched"');
                                ?>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </form>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="mawb_detail_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Manifest Detail [ <span id="modal_mawb_number"></span> - <span id="modal_mawb_service"></span> ]</h4>
                    </div>
                    <div class="modal-body">
                         <div class="table-scrollable">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Tracking Number </th>
                                        <th> Dims </th>
                                        <th> Weight </th>
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <tbody id="manifest_list_data"> 
                                    
                                </tbody>
                            </table>
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
        <!-- Show Tracking Modal -->
            <div class="modal fade" tabindex="-1" role="dialog" id="tracking_number_modal" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Manifest Tracking Number [ <span id="tracking_head"></span> ]</h4>
                        </div>
                        <div class="modal-body">
                             <div class="table-scrollable">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th> # </th>
                                            <th> Tracking Number </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tracking_number_data">
                                    </tbody>
                                </table>
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
        <!-- End Show Tracking Modal -->
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