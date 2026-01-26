<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'userservicescharges.class',
    'userserviceschargesfilter.class',
    'useraccountservicecharges.class',
    'useraccountservicechargesfilter.class',
	'userservicesroutingfilter.class',
    'userservicesrouting.class',
    'services.class',
    'servicesfilter.class',
	
]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = 0;
    
    private $user_account_id = 0;
    private $account_id = 0;
    private $account_id_encode = 0;
    private $service_id = 0;
    private $service_id_encode = 0;

    protected function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("SERVICE_CHARGES")
        );

        $this->user = SessionManager::getUser();
        
        $this->account_id = base64_decode(util_get("account_id"));
        $this->account_id_encode = util_get("account_id");
        
        $this->service_id = base64_decode(util_get("service_id"));
        $this->service_id_encode = util_get("service_id");
        
        $this->user_account_id = $this->account_id;
        
        // Handle user account service
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_account_services') {
            $output = '<option value="">Please Select</option>';
            $userAccountId = DbAccess3::escape($this->form_vars['user_account_id']);
            $serviceId = $this->form_vars['service_d'];
            $getAllAccount = $this->form_vars['get_all_account'];
            if($getAllAccount == "get_all"){
                $userAccountObj = UserAccountFilter::getImmediateChildAccount($userAccountId);
                if(count($userAccountObj) > 0){
                    $loggedInUserId = $userAccountId;
                    $userAccountId = [];
                    $userAccountId[0] = $loggedInUserId;
                    foreach ($userAccountObj as $userAccountOb) {
                        $userAccountId[] = $userAccountOb->getId();    
                    }
                }
            }
            $userServicesRoutingFilter = new UserServicesRoutingFilter();
            $userServicesRoutingFilter->addFilterIn("    user_account_id", $userAccountId);
//            $userServicesRoutingFilter->addFieldsFilter("is_agreed", "1");
            $userServicesRoutingFilter->addFieldsFilter("status", "1");
            $userServicesRoutingFilter->setGroup("service_id");
            $userAccountService = $userServicesRoutingFilter->getColumnList("service_id");

            if(count($userAccountService) > 0){
                foreach ($userAccountService as $service) {
                    $selected = "";
                    if($service->getServiceId() == $serviceId)
                        $selected = "selected";
                    
                    $serviceObj = new Services($service->getServiceId());
                    $output .= '<option '.$selected.' value="'.$service->getServiceId().'">'.$serviceObj->getName().'</option>';
                }
            }
            echo $output;
            exit;
        }
//        if (($this->service_id == 0) || ($this->user_account_id == 0)) {
//            util_redirect('index.php');
//        }
        
        /*
         * Handle Data Table 
         */

        if (isset($_GET['action']) && $_GET['action'] == 'user_account_service_charges_ajax') {
            $userAccountServicesChargesFilter = new UserAccountServiceChargesFilter();
            $userAccountServicesChargesFilter->join("consignment_charges_types cct", ['cct.id' => 'uasc.consignment_charges_types_id']);
//            if (isset($this->service_id) && $this->service_id > 0) {
//                $userAccountServicesChargesFilter->where(['uasc.service_id' => $this->service_id]);
//            }
//            if (isset($this->user_account_id) && $this->user_account_id > 0) {
//                $userAccountServicesChargesFilter->where(['uasc.user_account_id' => $this->user_account_id]);
//            }
            
           /*
            * Column filter
            * For search
            */
           if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
               $filterArray = [];

               $consignment_charges_types_id = $this->form_vars['consignment_charges_types_id'];
               if (!empty($consignment_charges_types_id))
                   $filterArray['uasc.consignment_charges_types_id'] = $consignment_charges_types_id;

               $charge = $this->form_vars['charge'];
               if (!empty($charge))
                   $filterArray['uasc.charge'] = $charge;
               
               $charge_type = $this->form_vars['charge_type'];
               if (!empty($charge_type))
                   $filterArray['uasc.charge_type'] = $charge_type;
               
               $serviceId = $this->form_vars['service_id'];
               if (!empty($serviceId))
                   $filterArray['uasc.service_id'] = $serviceId;
              
               $userAccountId = $this->form_vars['user_account'];
               if (!empty($userAccountId))
                   $filterArray['uasc.user_account_id'] = $userAccountId;
               
               
               

               $userAccountServicesChargesFilter->where($filterArray);

               $date_created_from = $this->form_vars['date_created_from'];
               $date_created_to = $this->form_vars['date_created_to'];
               if (!empty($date_created_from) && !empty($date_created_to))
                   $userAccountServicesChargesFilter->whereBetween ('uasc.added_date', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));
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
                $userAccountServicesChargesFilter->orderBy(strtolower("uasc.".$dataTableColumnName), $orderFalse);
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
            $userAccountServicesChargesFilter->setRowsPerPage($iDisplayLength);
            $userAccountServicesChargesFilter->setOffset($iDisplayStart);
            $userAccountServicesChargesFilterObj = $userAccountServicesChargesFilter->getList("uasc.*, cct.title");
            $iTotalRecords = $userAccountServicesChargesFilter->getCount();
            $setDataArr = array();
            foreach ($userAccountServicesChargesFilterObj as $userAccountServicesChargesObj) {
                $chargesListArr = array(); 
                $userAccountObj = new CustomerAccount($userAccountServicesChargesObj->getUserAccountId());
                $chargesListArr['user_account_id'] = $userAccountObj->getUserAccount();
                $service = new Services($userAccountServicesChargesObj->getServiceId());
                $chargesListArr['service_id'] = $service->getName();
                $chargesListArr['consignment_charges_types_id'] = $userAccountServicesChargesObj->getTitle();
                $chargesListArr['charge'] = $userAccountServicesChargesObj->getCharge();
                $chargesListArr['charge_type'] = ucfirst($userAccountServicesChargesObj->getChargeType());
                $chargesListArr['added_date'] = date("d m Y",$userAccountServicesChargesObj->getAddedDate());
                $chargesListArr['actions'] = '';
                $chargesListArr['actions'] .= '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                        <ul class="dropdown-menu" >';
                    $chargesListArr['actions'] .= '<li>
                                                    <a title="Edit" href="javascript:;" class="btnedit" data-id="' . $userAccountServicesChargesObj->getId() . '" >
                                                        <span class="fa fa-edit"></span> Edit
                                                    </a>
                                                </li>';
                    $chargesListArr['actions'] .= '<li>
                                                    <a title="Delete" href="javascript:;" class="btnDelete" data-id="' . $userAccountServicesChargesObj->getId() . '" >
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
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'saveUserAccountServiceCharges') {
            $output = [];
            $serviceId = $this->form_vars['service'];
            $accountId = $this->form_vars['user_account'];
            $chargesTypeId = $this->form_vars['chargesTypeId'];
            $charges = $this->form_vars['charges'];
            $chargesType = $this->form_vars['chargesType'];
            if($accountId <= 0){
                $output['status'] = "error";
                $output['message'] = "User account is not valid";
                echo json_encode($output);
                die();
            }
            if($serviceId <= 0){
                $output['status'] = "error";
                $output['message'] = "Service is not valid";
                echo json_encode($output);
                die();
            }
            if($chargesTypeId <= 0){
                $output['status'] = "error";
                $output['message'] = "Charges type is not valid";
                echo json_encode($output);
                die();
            }
            if($charges <= 0){
                $output['status'] = "error";
                $output['message'] = "Charges must be greater than zero";
                echo json_encode($output);
                die();
            }
            
            $date_added = time();
            $added_by = $this->user->getId();
            $date_update = time();
            $update_by = $this->user->getId();
            if(!empty($this->form_vars['user_service_charges_id'])) {
                $userAccountServicesCharges = new UserAccountServiceCharges($this->form_vars['user_service_charges_id']);
            } else {
                $userAccountServicesCharges = new UserAccountServiceCharges();
            }
            $userAccountServicesCharges->setUserAccountId($accountId);
            $userAccountServicesCharges->setServiceId($serviceId);
            $userAccountServicesCharges->setConsignmentChargesTypesId($chargesTypeId);
            $userAccountServicesCharges->setCharge($charges);
            $userAccountServicesCharges->setChargeType($chargesType);
            $userAccountServicesCharges->setAddedBy($added_by);
            $userAccountServicesCharges->setAddedDate($date_added);
            $userAccountServicesCharges->setUpdatedBy($update_by);
            $userAccountServicesCharges->setUpdatedDate($date_update);
            if(!empty($_GET['account_id'])) {
                $userAccountServicesCharges->tableKey = $_GET['account_id'];
                $userAccountServicesCharges->auditTableName = 'user_account';
                $serviceChargesObj = new Services($serviceId);
                $userAccountServicesCharges->custom_message = $this->user->getFirstName() . ' ' . $this->user->getLastName() . ' has updated service charges of ' . $serviceChargesObj->getName();
            }
            $userAccountServicesCharges->save();

            $output['status'] = "success";
            $output['message'] = "Charges Save successfully";
            echo json_encode($output);
            die();
        }
         
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_user_account_service_charges') {
            $id = $this->form_vars['id'];
            $returnJson = [];
            $chergeObj = new UserAccountServiceCharges($id);
            $returnJson['id'] = $chergeObj->getId();
            $returnJson['user_account_id'] = $chergeObj->getUserAccountId();
            $returnJson['service_id'] = $chergeObj->getServiceId();
            $returnJson['consignment_charges_types_id'] = $chergeObj->getConsignmentChargesTypesId();
            $returnJson['charge'] = $chergeObj->getCharge();
            $returnJson['charge_type'] = $chergeObj->getChargeType();
            echo json_encode($returnJson);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete") {
            $id = $this->form_vars['id'];
            if ($id > 0) {
                $chergeObj = new UserAccountServiceChargesFilter($id);
                $chergeObj->where(['id' => $id]);
                $chergeObj->delete();
                $returnMsg['STATUS'] = "success";
            } else {
                $returnMsg['STATUS'] = "error";
            }
            echo json_encode($returnMsg);
            die;
        }
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
                                "url": "user_account_service_charges.php?action=user_account_service_charges_ajax&service_id=<?php echo $this->service_id; ?>&account_id=<?php echo $this->user_account_id; ?>", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "user_account_id"},
                                {"data": "service_id"},
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
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                // Triger change to get services
                $("#user_account").trigger("change");
                $.ajax({
                    type: "POST",
                    url: "user_account_service_charges.php",
                    data: {func: "get_account_services", user_account_id:"<?php echo $this->user_account_id ?>",service_d:"<?php echo $this->service_id ?>",get_all_account:"get_all"},
                    dataType: "html",
                    success: function (data) {
                        if(data) {
                            $("#service_id").html("");
                            $("#service_id").html(data);
                            $('#service_id').selectpicker("refresh");
                            $(".filter-submit").click();
                        } else {
                            $("#service_id").html("");
                            $('#service_id').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            });
            // Handle get account services
            function loadAccountServices(user_account_id,service_d){
                $.ajax({
                    type: "POST",
                    url: "user_account_service_charges.php",
                    data: {func: "get_account_services", user_account_id: user_account_id,service_d:service_d},
                    dataType: "html",
                    success: function (data) {
                        if(data) {
                            $("#service").html("");
                            $("#service").html(data);
                            $('#service').selectpicker("refresh");
                        } else {
                            $("#service").html("");
                            $('#service').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            }
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
                                    url: "user_account_service_charges.php?service_id=<?php echo $this->service_id; ?>&account_id=<?php echo $this->user_account_id; ?>",
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
                    url: 'user_account_service_charges.php?service_id=<?php echo $this->service_id; ?>&account_id=<?php echo $this->user_account_id; ?>',
                    type: 'POST',
                    data: {action: 'get_user_account_service_charges', id: id},
                    headers: {
                    },
                    dataType: "json",
                    success: function (obj) {
                        $("#user_service_charges_id").val(obj.id);
                        $("#service_id").val(obj.service_id);
                        $("#account_id").val(obj.user_account_id);
                        
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
            $(document).on('click', '#btnSave', function () {
                var form_data = $("#userAccountServiceChargesForm").serializeArray();
                form_data.push({name: "action", value: "saveUserAccountServiceCharges"});
                $.ajax({
                        type: "POST",
                        url: 'user_account_service_charges.php?service_id=<?php echo $this->service_id; ?>&account_id=<?php echo $this->user_account_id; ?>',
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            if(data.status == "success") {
                                $('#userAccountServiceChargesForm').trigger("reset");
                                $("#user_service_charges_id").val("");
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
                <div class="caption"> <i class="icon-docs"></i>Service Charges </div>
            </div>
            <form id="userAccountServiceChargesForm" name="userAccountServiceChargesForm" action="user_account_service_charges.php?service_id=<?php echo $this->service_id_encode; ?>&account_id=<?php echo $this->account_id_encode; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_service_charges_id" id="user_service_charges_id" value="" >
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
                            <label>User Account</label>
                            <div class="input-group">
                                <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                <?php 
                                    $userAccount = $this->user->getUserAccountId(); 
                                    $accountFilterData = ' parentid = '.$userAccount .($this->account_id > 0? " and id = '".$this->account_id  ."' " :'');
                                    echo Ddl::generateDDL('user_account', 'UserAccountFilter', $accountFilterData, 'user_account', 'id', $this->user_account_id, ' class="form-control select2" data-toggle="tooltip" data-placement="top" onchange="loadAccountServices(this.value,'.$this->service_id.')" title="User Account" data-original-title="User Account"', 'Please Select', '', 'user_account', 'User Account'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Service</label>
                                <div class="first_form_col">
                                    <div class="input-group">
                                        <div class="input-group-addon"> <i class="fa fa-user"></i></div>
                                        <!-- User Document -->
                                        <select name="service" id="service" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Service" data-container="body" placeholder="Service">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Charges Type</label>
                            <?php
                                $chargesTypesFilter = new ConsignmentChargesTypesFilter();
                                $tpyeWhere = " cct.charge_type <> 'agent'";
                                $chargesTypesFilter->addFilter("   $tpyeWhere and cct.status='1' and is_delete = '0' AND has_account_default_value = 1 ");
                                $chargesTypesFilterObj = $chargesTypesFilter->getList();
                                $chargesArray = [];
                                $chargesArray[""] = "Select Charges Title";
                                if(count($chargesTypesFilterObj) > 0) {
                                    foreach($chargesTypesFilterObj as $charges) {
                                        $chargesArray[$charges->getId()] = $charges->getTitle();
                                    }
                                }
                                echo Ddl::generateArrayDDL('chargesTypeId', $chargesArray, '', '', 'class="form-filter select2 form-control" ', "", $dd_id = 'chargesTypeId');
                            ?>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-4">
                            <label>&nbsp;</label><br />
                            <button type="button" id="btnSave" class="btn btn-primary btn_save"><span></span>Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-dropbox"></i>
                    Service Charges
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
                                <th>User Account</th>
                                <th>Service</th>
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
                                        $accountFilterData = ' parentid = '.$userAccount .($this->account_id > 0? " and id = '".$this->account_id  ."' " :'');
                                        echo Ddl::generateDDL('user_account', 'UserAccountFilter', $accountFilterData, 'user_account', 'id', $this->user_account_id, ' class="form-control select2 form-filter" data-toggle="tooltip" data-placement="top" onchange="loadAccountServices(this.value,'.$this->service_id.')" title="User Account" data-original-title="User Account"', 'Please Select', '', 'user_account', 'User Account'); ?>
                                </td>
                                <td class="user_acccount_correct_button">
                                    <select name="service_id" id="service_id" class="bs-select form-control form-filter" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Service" data-container="body" placeholder="Service">
                                    </select>
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
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter" readonly name="date_created_from" placeholder="From">
                                        <span class="input-group-btn">
                                            <button class="btn btn-md default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
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
