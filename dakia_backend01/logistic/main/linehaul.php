 <?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
        'country.class',
        'countryfilter.class',
        'linehaul.class',
        'linehaulfilter.class',
        'agentdata.class',
        'agentdatafilter.class'
        ]);
?>

<?php

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $error = array();
    public $message;
    private $user = null;
    private $agents = [];

    protected function init() {
         $this->user = SessionManager::getUser();
         $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Linehaul'
         );

        $agentFilter = new AgentDataFilter();
        $agentFilter->addFilter("     AND (a.agent_type = 'both' || a.agent_type = 'dispatch')");
        $this->agents = $agentFilter->getColumnList('*');

        if(isset($this->form_vars["action"]) && $this->form_vars["action"] == "save"){
            //New logic
            $agentId = $this->form_vars["agent_id"];
            $countryId = $this->form_vars["country_id"];
            $price = $this->form_vars["price"];
            $economyPriority = $this->form_vars["delivery_type"];
            $id = $this->form_vars['id'];
            $linehaul = new Linehaul();
            if($id != "" && $id > 0) {
                $linehaul = new Linehaul($id);
            }
            $linehaul->setAccountId($this->user->getUserAccountId());
            $linehaul->setAgentId($agentId);
            $linehaul->setCountryId($countryId);
            $linehaul->setDeliveryType($economyPriority);
            $linehaul->setPrice($price);
            $linehaul->setDateCreated(time());
            $linehaul->save();
            $output['status'] = "success";
            $output['message'] = "linehaul saved successfully";
            echo json_encode($output);
            die;
        }
        if (isset($_GET['action']) && $_GET['action'] == "rtn_list") {
            $linehaulFilter = new LinehaulFilter();
            $linehaulFilter->where(['account_id' => $this->user->getUserAccountId()]);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $agentId = $this->form_vars['agent_id'];
                if (!empty($agentId)) {
                    $linehaulFilter->where(['agent_id' => $agentId]);
                }
                $countryId = $this->form_vars['country_id'];
                if (!empty($countryId)) {
                    $linehaulFilter->where(['country_id' => $countryId]);
                }
                $deliveryType = $this->form_vars['delivery_type'];
                if (!empty($deliveryType)) {
                    $linehaulFilter->where(['delivery_type' => $deliveryType]);
                }
                $price = $this->form_vars['price'];
                if (!empty($price)) {
                    $linehaulFilter->where(['price' => $price]);
                }
                $searchDateFrom = $this->form_vars['search_date_from'];
                $searchDateTo = $this->form_vars['search_date_to'];
                if (!empty($searchDateFrom) && !empty($searchDateTo)) {
                    $linehaulFilter->whereBetween('date_created',$searchDateFrom,$searchDateTo);
                }
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
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if ($dataTableColumnName != "action") {
                    $linehaulFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $linehaulFilter->orderBy(strtolower("l.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $linehaulFilter->setRowsPerPage($iDisplayLength);
            $linehaulFilter->setOffset($iDisplayStart);
            $linehaulFilterObjs = $linehaulFilter->getList("l.*");
            $iTotalRecords = $linehaulFilter->getCount();
            $setDataArr = array();
            foreach ($linehaulFilterObjs as $linehaulListObj) {
                $agent = new AgentData($linehaulListObj->getAgentId());
                $agentName = "";
                if(!empty($agent)) {
                    $agentName = $agent->getAgentName();
                }
                $country = new Country($linehaulListObj->getCountryId());
                $currentArr['agent_id'] = $agentName;
                $currentArr['country_id'] = $country->getName();
                $currentArr['delivery_type'] = ucwords($linehaulListObj->getDeliveryType());
                $currentArr['price'] = $linehaulListObj->getPrice();
                $action = '<div class="btn-group" data-container="body" >
                                <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" >';
                    $action .= '<li>
                                    <a title="Edit" href="javascript:;" data-id="'.$linehaulListObj->getId().'" class="edit">
                                        <span class="glyphicon glyphicon-eye-open"></span> Edit
                                    </a>
                                    <a title="Delete" href="javascript:;" data-id="'.$linehaulListObj->getId().'" class="delete">
                                        <span class="glyphicon glyphicon-trash"></span> Delete
                                    </a>
                                </li>';
                    $action .= '</ul>';
                $action .= '</div>';
                $currentArr['actions'] = $action;
                $setDataArr[] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if(isset($this->form_vars["action"]) && $this->form_vars["action"] == "edit"){
            $id = $this->form_vars['id'];
            $linehaulObj = new Linehaul($id);
            $dt = [
                    'id' => $linehaulObj->getId(),
                    'agent_id' => $linehaulObj->getAgentId(),
                    'country_id' => $linehaulObj->getCountryId(),
                    'delivery_type' => $linehaulObj->getDeliveryType(),
                    'price' => $linehaulObj->getPrice()
            ];
            $return = [
                'status' => 'success',
                'linehaul' => $dt
            ];
            echo json_encode($return);
            die;
        }

        if(isset($this->form_vars["action"]) && $this->form_vars["action"] == "delete"){
            $id = $this->form_vars['id'];
            $linehaulFilter = new LinehaulFilter();
            $linehaulFilter->where(['id' => $id]);
            $linehaulFilter->delete();
            $return = [
                    'status' => 'success',
                    'message' => 'Data delete successfully',
            ];
            echo json_encode($return);
            die;
        }
    }
    protected function renderHead() {
        ?>

        <?php
    }
    
    protected function addPagelavelCss() {
    ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
    <?php
    }
    public function addPagelavelJs() {
    ?>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"
                type="text/javascript"></script>
         <script type="text/javascript">
             var grid = null;
             var DataTableFun = function () {
                 var handleDataTable = function () {
                     var datatableurl = "linehaul.php?action=rtn_list";
                     grid = new Datatable();
                     grid.init({
                         src: $("#manage-data-table"),
                         onSuccess: function (grid, response) {
                             $(".table-container .custom-alerts").hide();
                             if (response.recordsTotal > 0) {
                                 $("#bluk_actions").show();
                                 $(".button-download-records").show();
                             } else {
                                 $("#bluk_actions").hide();
                                 $(".button-download-records").hide();
                             }
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
                                 headers: {}
                             },
                             "bStateSave": true,
                             "columns": [
                                 {"data": "actions", "bSortable": false},
                                 {"data": "agent_id", "bSortable": false},
                                 {"data": "country_id", "bSortable": false},
                                 {"data": "delivery_type", "bSortable": false},
                                 {"data": "price", "bSortable": false}
                             ],
                             rowCallback: function (row, data, index) {
                             }
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
            $(document).ready(function (e) {
                $("#btnSave").click(function(){
                    var id = $("#id").val();
                    var agent_id = $("#agent_id").val();
                    var country_id = $("#country_id").val();
                    var delivery_type = $("#delivery_type").val();
                    var price = $("#price").val();
                    var validate = true;
                    if(agent_id == ""){
                        validate = false;
                        swal("","please select agent", "info");
                    }
                    if(country_id == ""){
                        validate = false;
                        swal("","please select country", "info");
                    }
                    if(delivery_type == null) {
                        validate = false;
                        swal("", "please select Delivery Type", "info");
                    }
                    if(price == "") {
                        validate = false;
                        swal("", "please enter price", "info");
                    }
                    if(validate){
                        $.blockUI();
                        $.ajax({
                            type: "POST",
                            url: "linehaul.php",
                            data: {action: "save", id: id,agent_id: agent_id,country_id:country_id,delivery_type:delivery_type,price:price},
                            dataType: "json",
                            success: function (data) {
                                $.unblockUI();
                                if (data.status == "success") {
                                    $("#res_message").show();
                                    $(".success_msg").html(data.message);
                                    grid.getDataTable().ajax.reload();
                                    $('#linehaul_form')[0].reset();
                                    $('#id').val('');
                                    $('#country_id').val('');
                                    $('#country_id').selectpicker('refresh');
                                }
                            },
                            error: function () {
                                $.unblockUI();
                                alert('error handing here');
                            }
                        });   
                    }
                });
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                DataTableFun.init();
                $(document).on('click','.edit',function(){
                    var id = $(this).data('id');
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "linehaul.php",
                        data: {action: "edit", id: id},
                        dataType: "json",
                        success: function (data) {
                            $.unblockUI();
                            if (data.status == "success") {
                                var id = data.linehaul.id;
                                var agent_id = data.linehaul.agent_id;
                                var country_id = data.linehaul.country_id;
                                var delivery_type = data.linehaul.delivery_type;
                                var price = data.linehaul.price;
                                $('#id').val(id);
                                $('#agent_id').val(agent_id).trigger('change');
                                $('#country_id').val(country_id);
                                $('#country_id').selectpicker('refresh');
                                $('#delivery_type').val(delivery_type).trigger('change');
                                $('#price').val(price);
                            }
                        },
                        error: function () {
                            $.unblockUI();
                            alert('error handing here');
                        }
                    });
                });
                $(document).on('click','.delete',function(){
                    var id = $(this).data('id');
                    $.blockUI();
                    $.ajax({
                        type: "POST",
                        url: "linehaul.php",
                        data: {action: "delete", id: id},
                        dataType: "json",
                        success: function (data) {
                            $.unblockUI();
                            if (data.status == "success") {
                                $("#res_message").show();
                                $(".success_msg").html(data.message);
                                grid.getDataTable().ajax.reload();
                            } else {
                            }
                        },
                        error: function () {
                            $.unblockUI();
                            alert('error handing here');
                        }
                    });
                });
            });
        </script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
    <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-users"></i>
                    Linehaul
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <form action="" method="post" enctype="multipart/form-data" id="linehaul_form" name="linehaul_form">
                    <input type="hidden" name="id" id="id" />
                    <div class="row display-none" id="res_message">
                        <div class="col-md-12">
                            <div class="alert alert-success success_msg"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="label-account">Agent</label>
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ship"></i> </span>
                                    <select class="select2" name="agent_id" id="agent_id">
                                        <option value="">Select Agent</option>
                                        <?php if(count($this->agents)) { ?>
                                            <?php foreach($this->agents as $agentObj) { ?>
                                                <option value="<?php echo $agentObj->getId() ?>"><?php echo $agentObj->getAgentName() ?> [<?php echo $agentObj->getContactName() ?>]</option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Country</label>
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                                    <?php echo Ddl::generateCountryDDL('country_id', '', 'id'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Economy/Priority</label>
                            <div class="form-group">
                                <select name="delivery_type" id="delivery_type" class="form-control select2" required="required">
                                    <option value="economy">Economy</option>
                                    <option value="priority">Priority</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Price</label>
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-dollar"></i> </span>
                                    <input type="text" step="any" class="form-control" name="price" id="price" value="" size="100" style=""  maxlength="35" rel="tooltip" placeholder="price" data-original-title="price" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <button class="btn btn-primary btn_save" name="btnSave" id="btnSave" type="button">Save</button>
                            <input type="hidden" name="form_action" id="form_action" value="save" />
                            <input type="hidden" name="id" id="id" value="" />
                            <a href="linehaul.php" class="btn btn-danger btn_cancel">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-bar-chart"></i>
                    Linehaul List
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper"></div>
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Action</th>
                                <th>Agent</th>
                                <th>Country</th>
                                <th>Delivery Type</th>
                                <th>Price</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                    </div>
                                </td>
                                <td>
                                    <select class="select2 form-control form-filter" name="agent_id">
                                        <option value="">Select Agent</option>
                                        <?php if(count($this->agents)) { ?>
                                            <?php foreach($this->agents as $agentObj) { ?>
                                                <option value="<?php echo $agentObj->getId() ?>"><?php echo $agentObj->getAgentName() ?> [<?php echo $agentObj->getContactName() ?>]</option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <div>
                                        <?php echo Ddl::generateCountryDDL('country_id', '', 'id'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <select name="delivery_type" class="form-control form-filter select2">
                                            <option value="">Select Type</option>
                                            <option value="all">All</option>
                                            <option value="economy">Economy</option>
                                            <option value="priority">Priority</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter" name="price" />
                                </td>
                                <td> </td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
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
