<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([ 'errorlist.class'
                ],'visualcomponents');
include_classes([
    'invoices.class',
    'invoicesfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'services.class',
    'servicefilter.class',
    'serviceconstant.class',
    'serviceconstantfilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'carrierfilter.class',
    'carrier.class',]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $constantlist;
    private $id = NULL;
    private $breadcrumb = '';

    
    /*     * *
     * Controller logic
     */

    protected function init() {
        
        
//        if(!Permissions::checkFilePermission('add_ranges.php')) 
//                    util_redirect ("index.php");
        $user = SessionManager::getUser();
       $this->breadCrumb['data'] = array( 
                                        'index.php'=>Translation::GetCaption("HOME"),
                                        'add_constant.php'=>'Add Constant'
            );
        //$this->constantlist = new ServiceConstantFilter();
        // common initialisation for ths page
        $this->setTitle("Constant List");
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {
            
            $constant_id             = $this->form_vars["id"];
            $constant_value            = $this->form_vars["constant_value"];
            $service_id = $this->form_vars["service_id"];
            $agent_id            = $this->form_vars["agent_id"];
            $cid              = $this->form_vars["constant_id"];
        
            $error_array = array();
            $headerMessage = '';
            foreach($service_id as $sid)
            {
                $constantobj = new ServiceConstantValue($constant_id);
                $constantobj->setServiceId($sid);
                $constantobj->setAgentId($agent_id);
                $constantobj->setConstantValue($constant_value);
                $constantobj->setConstantId($cid);
             
                if ((int) $constant_id <= 0)
                {
                    $constantobj->setAddedBy($user->getId());
                    $constantobj->setDateCreated(time());
                }

                $constantobj->setUpdatedBy($user->getId());
                $constantobj->setDateUpdate(time());

                $constantobj->save();
               
                //$constntId = $constantobj->getId();
            }
            
            
            die;
            
        }
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $constantId = (int) $_POST['constantid'];
            
            $this->id = $constantId;
            $edit_array = array();
            $serviceConstantValue = new ServiceConstantValue($constantId);
            
            $edit_array['id']              = $constantId;
            $edit_array['serviceid']     = $serviceConstantValue->getServiceId();
            $edit_array['agentid']       = $serviceConstantValue->getAgentId();
            $edit_array['constantid']          = $serviceConstantValue->getConstantId();
            $edit_array['constantvalue']          = $serviceConstantValue->getConstantValue();

           
            
            echo json_encode($edit_array);
            die;
        }
      
       
        if (isset($_GET['action']) && $_GET['action'] == "constant_ajax") {
            // Get current user
            $this->constantlist = new ServiceConstantValueFilter();
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
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchservice = $this->form_vars['search_service'];
                if (!empty($searchservice)) {
                    $this->constantlist->addFieldFilter('scv.service_id', $searchservice);
                }
                $searchAgent = $this->form_vars['search_agent'];
                if (!empty($searchAgent)) {
                    $this->constantlist->addFieldFilter('scv.agent_id', $searchAgent);
                }
                $searchConstant = $this->form_vars['search_constant'];
                if (!empty($searchConstant)) {
                    $this->constantlist->addFieldFilter('scv.constant_id', $searchConstant);
                }
                $searchConstantValue = $this->form_vars['search_constantvalue'];
                if (!empty($searchConstantValue)) {
                    $this->constantlist->addFieldLikeFilter('scv.constant_value', $searchConstantValue);
                }
            }
            
            $iTotalRecords = $this->constantlist->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->constantlist->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->constantlist->setOffset($iDisplayStart);
            
            if($dataTableColumnName != '')
            {
                 $this->constantlist->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            else
            {
                $this->constantlist->AddOrderBy('scv.id', false);
            }
            $constant_list = $this->constantlist->getPagingList("  s.name AS service_name, a.agent_name, c.constant, ca.logo,  scv.*");
            $constantDataArr = array();
            foreach ($constant_list as $constant) {
                $constantArr['actionss'] = '';
                $constantArr['actionss'] .= "<a data-id =" . $constant->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-pencil'></span> </a>";
                $constantArr['actionss'] .= "<a href='' class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $constant->getId() . "' data-log_name='service_constant_value' data-toggle='modal'> <span class='fa fa-list'></span> </a>";
                $constantArr['service_name']         = '<img src=\'../images/carrierlogo/thumbnail/owe_16_' . $constant->getLogo() . '\' />'. $constant->getServiceName();
                $constantArr['agent_name']           = $constant->getAgentName();
                $constantArr['constant_name']        = $constant->getConstant();
                $constantArr['constant_value']       = $constant->getConstantValue();
                $constantDataArr[] = $constantArr;
            }
            $constantDataArr['data'] = $constantDataArr;
            $constantDataArr['draw'] = $sEcho;
            $constantDataArr['recordsTotal'] = $iTotalRecords;
            $constantDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($constantDataArr);
            die;
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>

        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
            
            
            
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        

        <?php
    }

    protected function renderFooter() {
        ?>
        <script>
            $(document).ready(function () {
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "add_constant.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $("#rangeForm")[0].reset();
                    });
                });
                $(document).on('click', '#btn_Save', function () {
                   
                    $('#rangeForm').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else
                        {
                            var id = $('#id').val();
                            $.ajax({
                                method: "POST",
                                url: "add_constant.php",
                                data: $('#rangeForm').serialize()
                            }).done(function (data) {
                                
                                $('#btn_Save').val("Save");
                                $('#success_msg').html(" ");
                                if (id == "") {
                                    $('#success_msg').html("Record has been Added Successfully");
                                } else {
                                    $('#success_msg').html("Record has been Updated Successfully");
                                }
                                $('#service_id').val('').trigger('change');
                                $('#agent_id').val('').trigger('change');
                                $('#constant_id').val('').trigger('change');
                                $('#constant_value').val('');
                                $("div").removeClass("hidden");
                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                $('#id').val("");
                                $('#manage-data-table').DataTable().ajax.reload();
                            });
                            return false;
                        }
                    });
                    $("#rangeForm").submit();
                });

                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var constantid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "add_constant.php",
                        data: {constantid: constantid, func: "editrecord"}
                    }).done(function (data) {
                       
                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");                        
                        $('#service_id').val(t.serviceid).trigger('change');
                        $('#agent_id').val(t.agentid).trigger('change');
                        $('#constant_id').val(t.constantid).trigger('change');
                        $('#constant_value').val(t.constantvalue);
                        
                        //$('#active').val(t.status);
                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
                    });
                });

            });

            var DataTableFun = function () {
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
                                [10, 20, 50, 100],
                                [10, 20, 50, 100] // change per page values here 
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": "add_constant.php?action=constant_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss","bSortable": false},
                                {"data": "service_name"},
                                {"data": "agent_name"},
                                {"data": "constant_name"},
                                {"data": "constant_value"}
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
           
            });
           
        </script>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        if (errorList::getItem()->getErrorCount() > 0) {
            ?>
            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>
        <div class="main_formpage">
            <?php
            //if(Permissions::checkFilePermission('range_add'))
            {
            ?>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-plus"></i>
                        
                           Add Constant
                    </div>
                    <div class="actions">
                        
                    </div>
                </div>
                <div class="portlet-body">
                    <form name="rangeForm" id="rangeForm" action="" method="POST">           
                        <div class="row">
                            <div class="col-md-12 hidden" id="successmsg">
                                <div class="alert alert-success" id="success_msg"> </div>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-sm-3">
                            <div class="form-group">
                                <div class="has-float-label">
                                                     
                                       <?php
                                    
                                    echo Ddl::generateServiceDDLWithImage('service_id',$service_id,'id', ' class="bs-select input-sm form-control form-filter " required="" data-show-subtext="true"','service_id','','name','Select Service'); 
                                    //  echo Ddl::generateDDL('service_id[]', 'ServiceFilter', "AND active = '1'", 'name', 'id', $selected_value, ' class="form-control select2 " multiple required', "Please select Services", "", "service_id", "service_id");
                                //    echo Ddl::generateDDL($serviceName, 'ServiceFilter', "AND active = '1'", 'name', 'id', $selected_value, ' class="form-control select2 service_select"', "Please select service", "", "service", "service");
                                    ?>  
                                    <label >Services <span class=" red-18">*</span> </label>
                                    
                               </div>
                            </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group"> 
                                      <div class="has-float-label">
                                   
                             
                                       
                                            <?php 
                                            echo Ddl::generateDDL('agent_id', 'AgentDataFilter', "( agent_type = 'carrier' OR agent_type = 'both')", 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select" required', "Please select agent", "", "agent_id", "Agent");
                                            ?> <label>Agent <span class="red-18">*</span> </label>
                                        
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group"> 
                                     <div class="has-float-label input-icon right">
                                 
                                      
                                            <?php 
                                            echo Ddl::generateDDL('constant_id', 'ServiceConstantFilter', "", 'constant', 'id', $constant_id, ' class="form-control select2" required', "Please select Constant", "", "constant_id", "Constant");
                                            ?> <label>Constant <span class="red-18">*</span> </label>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group"> 
                                   
                                    <div class="has-float-label input-icon right">
                                      
                                        <input name="constant_value" id="constant_value" value="" size="50" class="form-control" title="Constant Value" maxlength="35" placeholder="Constant Value" rel="tooltip" data-original-title="Constant Value" type="text" required> 
                                        <label for="constant_value">Constant Value <span class="red-18">*</span> </label>
                                        
                                    </div>
                                </div>
                            </div> 
                        </div> 
                        <div style="clear:both"></div> 
                        <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                        <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                        <div class="row " style="text-align:centre;" align="center">
                            <div class="col-md-12">
                                <input id="btn_Save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                                <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                            </div>
                        </div>   
                    </form>
                </div>
            </div>
            <?php } ?>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>
                      
                           Constant List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>Service Name</th>
                                    <th>Agent Name</th>
                                    <th>Constant</th>
                                    <th>Constant Value</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td class="user_acccount_correct_button">
                                       <?php
                                       echo Ddl::generateServiceDDLWithImage('search_service',$search_service,'id', ' class="bs-select input-sm form-control form-filter " data-show-subtext="true"','search_service','search_service','name','Select Services'); 
                                       
                                       ?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo Ddl::generateDDL('search_agent', 'AgentDataFilter', "agent_type = 'carrier'", 'agent_name', 'id', $selected_value, ' class="form-control select2 form-filter" ', "Please select agent", "", "search_agent", "Agent");
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo Ddl::generateDDL('search_constant', 'ServiceConstantFilter', "", 'constant', 'id', $constant_id, ' class="form-control select2 form-filter" ', "Please select Constant", "", "search_constant", "Constant");
                                        ?>
                                    </td>
                                    <td>
                                       <input type="text" class="form-control form-filter input-xs" name="search_constantvalue" id ="search_constantvalue" />
                                    </td>
                                   
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                </div>
            </div>
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

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
