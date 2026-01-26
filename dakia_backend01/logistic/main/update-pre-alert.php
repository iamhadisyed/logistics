<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'flightinfo.class',
    'flightinfofilter.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    ]);
/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $uploadfilelist = "";
    private $user;

    protected function init() {
        $this->user = SessionManager::getUser();
         $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Update Pre-alert'
        );
        //die();
        
        if($_GET['action'] && $_GET['action'] == 'prealert_datatable'){
            $flightFilterObj=  new FlighInfoFilter();
           
//            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
//                
//                if(!empty($this->form_vars['search_mawb'])){
//                    $flightFilterObj->addFilter("   fm.mawb = '".$this->form_vars['search_mawb']."'");
//                }
//                if(!empty($this->form_vars['search_status'])){
//                    $flightFilterObj->addFilter("   fi.status = '".$this->form_vars['search_status']."'");
//                }
//                if(!empty($this->form_vars['search_flight'])){
//                    $flightFilterObj->addFilter("   fi.flight_number = '".$this->form_vars['search_flight']."'");
//                }
//                if(!empty($this->form_vars['search_weight'])){
//                    $flightFilterObj->addFilter("   fi.weight = '".$this->form_vars['search_weight']."'");
//                }
//                if(!empty($this->form_vars['search_pieces'])){
//                    $flightFilterObj->addFilter("   fi.pieces = '".$this->form_vars['search_pieces']."'");
//                }
//                if(!empty($this->form_vars['search_user_account_id'])){
//                    $flightFilterObj->addFilter("   fi.account_id = '".$this->form_vars['search_user_account_id']."'");
//                }
//            }
            $flightFilterObj->AddOrderBy('fi.id', ' DESC');
            $flightFilterObj->addFilter("   fi.is_delete = '0' ");
            $flightFilterCount = $flightFilterObj->getCount();
            $iTotalRecords = $flightFilterCount;
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $flightFilterObj->setRowsPerPage($iDisplayLength);
            $flightFilterObj->setOffset($iDisplayStart);
            $dataGettingFlightInfoAndMapping = $flightFilterObj->getList(TRUE);
            if(!empty($dataGettingFlightInfoAndMapping)){
                foreach($dataGettingFlightInfoAndMapping as $key => $preData){
                    $currentArr = array();

                    $currentArr['action'] = '<a class="btn btn-xs btn-default blue btn-outline center prealert_edit" href="javascript:;" data-id="'.$preData->getId().'"> Edit </a><a class="margin-top-10 btn btn-xs btn-default red btn-outline center prealert_delete" href="javascript:;" data-id="'.$preData->getId().'" data-mawb="'.$preData->getMawb().'"> Delete </a>';
                    $currentArr['mawb'] = $preData->getMawb();;
                    $currentArr['flight'] = $preData->getFlightNumber();
                    $status = $preData->getCurrentStatus();
                    $owe_status = $preData->getStatus();
                    $current_status = '';
                    if($status == 'collection_in_process'){
                        $current_status = 'COLLECTION IN PROCESS';
                    }else if($status == 'in_tranist'){
                        $current_status = 'IN TRANSIT';
                    
                    }else if($status == 'clearance_in_process'){
                        $current_status = 'CLEARANCE IN PROCESS';
                    }
                    
                    $status_owe = '';
                    if($owe_status == 'in_warehouse'){
                        $status_owe = 'IN Warehouse';
                    }else if($owe_status == 'assigned'){
                        $status_owe = 'ASSIGNED';
                    }else if($owe_status == 'not_assigned'){
                        $status_owe = 'NOT ASSIGNED';
                    }
                    $currentArr['owestatus'] = $current_status;
                    $currentArr['eta'] = $preData->getEta();
                    $currentArr['etd'] = $preData->getEtd();
                    $currentArr['weight'] = $preData->getWeight();
                    $currentArr['pieces'] = $preData->getPieces();
                    
                    $currentArr['status'] = $status_owe;
                    $currentArr['arrived_date'] = date('Y-m-d',  strtotime($preData->getArrivedDate()));
                    //$currentArr['account'] = $preData->getUserAccount();
                    $currentArr['shed'] = $preData->getShed();
                    $currentArr['comments'] = $preData->getComments();
                    $currentArr['id'] = $preData->getId();
                    $setDataArr[] = $currentArr;
                }
            }
            $setDataArrJson['data'] = (!empty($setDataArr)?$setDataArr:0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            exit;
        }
        
        
        if (isset($_POST['form_action']) && $_POST['form_action'] == 'save') {
            $fileString = (!empty($file_name) ? $file_name : '');
            $user = Sessionmanager::getUser();
            $id = !empty($this->form_vars["id"]) ? $this->form_vars["id"] : '';   
            $flightInfoObj = new FlightInfo($id);
                  
            $flightInfoObj->setFlightNumber($this->form_vars["flight"]);
            $flightInfoObj->setPieces($this->form_vars["pieces"]);
            $flightInfoObj->setCarriageValue(0.0);
            $flightInfoObj->setCustomValue(0.0);
            $flightInfoObj->setInsuranceAmount(0.0);
            $flightInfoObj->setWeight($this->form_vars["weight"]);
            $flightInfoObj->setCreatedBy($user->getId());
            $flightInfoObj->setShed($this->form_vars['shed']);
            $flightInfoObj->setComments($this->form_vars['comments']);
            $flightInfoObj->setEta(date('Y-m-d h:i:s',  strtotime($this->form_vars['arrived_date'])));
            $flightInfoObj->setCurrentStatus($this->form_vars['status']);
            $flightInfoObj->setStatus($this->form_vars['owestatus']);
            $flightInfoObj->setIsDelete(0);
            $flightInfoObj->save();   
            
            $msg = [];
            $flightMapppingObj = new FlightMapping();
            $flightMappDelObj = new FlightMappingFilter();
            $flightMappDelObj->deleteOnFlightId($flightInfoObj->getId());
            $flightMapppingObj->setFlightInfoId($flightInfoObj->getId());
            $flightMapppingObj->setFlightNumber($this->form_vars["flight"]);
            $flightMapppingObj->setMawb($this->form_vars["mawb"]);
            $flightMapppingObj->setIsDelete(0);
            $flightMapppingObj->Save();
            if (!empty($flightInfoObj->getId())) {
                $msg['sucess'] = "<div class='col-md-12 alert alert-success'>Pre Alert Advice data has been saved.</div>";
            } else {
                $msg['error'] = "<div class='col-md-12 alert alert-danger'>Pre Alert Advice data has not been save.</div>";
            }
            echo json_encode($msg);
            exit;
        }

       
        if(isset($_POST['func']) && $_POST['func'] == 'delete'){
            $id = $_POST["id"];
            $preFlightObj = new FlightInfo($id);
            $preFlightObj->setIsDelete(1);
            $preList = $preFlightObj->save();
            $preFlightMapping = new FlightMappingFilter();
            $preFlightMapping->addFilter("     m.flight_info_id = '".$id."'");
            $preAlertMappingObj = $preFlightMapping->getColumnList('*');
            if(!empty($preAlertMappingObj)){
               foreach($preAlertMappingObj as $data){
                   $prealertMappingObj = new FlightMapping($data->getId());
                   $prealertMappingObj->setIsDelete(1);
                   $prealertMappingObj->save();
               }
               if (!empty($prealertMappingObj->getId())) {
                    $msg['sucess'] = "<div class='col-md-12 alert alert-success'>Pre Alert Advice data has been deleted.</div>";
                } else {
                    $msg['error'] = "<div class='col-md-12 alert alert-danger'>Pre Alert Advice data has not been delete.</div>";
                }
            }
            echo json_encode($msg);
            exit;
        }
    }

    protected function addPagelavelCss() {
        ?>
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
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>  

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
                                "url": "update-pre-alert.php?action=prealert_datatable", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "action"},
                                {"data": "mawb", "bSortable": false},
                                {"data": "flight", "bSortable": false},
                                {"data": "pieces"},
                                {"data": "weight"},
                                {"data": "eta"},
                                {"data": "etd"},
                                {"data": "arrived_date"},
                                {"data": "owestatus"},
                                {"data": "status", "bSortable": false},
                                {"data": "shed", "bSortable": false},
                                {"data": "comments", "bSortable": false},
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
                $('#btnSaveNew').attr('disabled','disabled');
                DataTableFun.init();
                //TableDatatablesEditable.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                
                 $(document).on('click','.prealert_edit',function (){
                    $('#btnSaveNew').removeAttr('disabled');
                    var id = $(this).data('id');
                    $.blockUI();
                    $.post('pre-alert.php',{func:'edit',id:id},function(data){
                        var obj = JSON.parse(data);
                        
                        if(obj.id){
                            $('#weight').val(obj.weight);
                            $('#pieces').val(obj.pieces);
                            $('#shed').val(obj.shed);
                            $('#comments').val(obj.comments);
                            $("#status option[value="+obj.currenct_status+"]").attr('selected', 'selected');
                            $("#owestatus option[value="+obj.status+"]").attr('selected', 'selected');
                            $('#mawb').val(obj.mawb);
                            $('#flight').val(obj.flight);
                            $('#arrived_date').val(obj.arrived_date);
                            $('#id').val(obj.id);
                            $.unblockUI();
                        }
                    });
                });
                $('#btnSaveNew').click(function () {
                    if ($('#mawb').val() != '' && $('#weight').val() != '' && $('#pieces').val() != '' && $('#flight').val()) {
                        $('#pre_alert_form').trigger('submit');
                        //
                        setTimeout(function(){ $('#btnSaveNew').attr('disabled','disabled'); grid.getDataTable().ajax.reload(); }, 2000);
                        
                    }
                });
                
                $(document).on('click','.yes',function (){
                    var id = $('#modal_delete_id').val();
                    $.blockUI();
                    if(id){
                        $.post('update-pre-alert.php',{func:'delete',id:id},function(data){
                            var obj = JSON.parse(data);
                                $('.msg_modal').html('');
                            if (obj.sucess) {
                                $('#model_delete').modal('hide');
                                $.unblockUI();
                            } else if (obj.error) {
                                $('#model_delete').modal('hide');
                                $.unblockUI();
                            }
                            grid.getDataTable().ajax.reload();
                        });
                    }
                });
                $(document).on('click','.prealert_delete',function (){
                    $('#model_delete').modal('show'); 
                    var id = $(this).data('id');
                    var mawb = $(this).data('mawb');
                    $('#modal_delete_id').val(id);
                    $('.mawb_delete').html(mawb);
                });
                
                $("#pre_alert_form").on('submit', function (e) {
                    $.blockUI();    
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'update-pre-alert.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            var obj = JSON.parse(data);
                            if (obj.sucess) {
                                $('.msg').html(obj.sucess);
                            } else if (obj.error) {
                                $('.msg').html(obj.error);
                            }
                            setTimeout(function(){ $('.msg').hide(); }, 3000);
                            $('#id').val('');
                            $('#pre_alert_form').trigger("reset");
                            $.unblockUI();

                        }
                    });
                });
            });
        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $sessionUser = SessionManager::getUser();
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-users"></i>
                    Update Pre Alert Advice				
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <form action="javascript:{};" method="post" enctype="multipart/form-data" id="pre_alert_form" name="pre_alert_form">
                    <div class="msg"></div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="label-account">MAWB Number</label>
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket "></i> </span>
                                    <input type="text" class="form-control" name="mawb" id="mawb" value="<?php echo @$mawb; ?>" size="100" style="" maxlength="35" rel="tooltip" placeholder="MAWB Number" data-original-title="MAWB Number" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Weight</label>
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input type="number" step="any" class="form-control" name="weight" id="weight" value="<?php echo @$weight; ?>" size="100" style=""  maxlength="35" rel="tooltip" placeholder="Weight" data-original-title="Weight" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Flight Number</label>
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-plane"></i> </span>
                                    <input type="text" class="form-control" name="flight" id="flight" value="<?php echo @$flight; ?>" size="100" style=""  maxlength="35" rel="tooltip" placeholder="Flight Number" data-original-title="Flight Number" required /> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">Owe Status</label>
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ship"></i> </span>
                                    <select class="form-control" name="owestatus" id="owestatus">
                                        <option value="not_assigned">NOT ASSIGNED</option>
                                        <option value="assigned">ASSIGNED</option>
                                        <option value="in_warehouse">IN WAREHOUSE</option>
                                    </select>                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                           <label class="label-account">Number of bags</label>
                           <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                    <input type="number" min="0" step="1" class="form-control" name="pieces" id="pieces" value="<?php echo @$pieces; ?>" size="100" style=""  maxlength="35" rel="tooltip" placeholder="Pieces" data-original-title="Pieces" required/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                           <label class="label-account">Current Status</label>
                           <div class="form-group"> 
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa fa-ship"></i> </span>
                                     <select class="form-control" name="status" id="status">
                                         <option value="in_transit">IN TRANSIT</option>
                                         <option value="clearance_in_process">CLEARANCE IN PROCESS</option>
                                         <option value="collection_in_process">COLLECTION IN PROCESS</option>
                                     </select>                                    
                                 </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                           <label class="label-account">Shed</label>
                           <div class="form-group">
                                <div class="input-group"><span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input type="text" class="form-control" id="shed" name="shed" value="" placeholder="Shed">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                          <label class="label-account">Arrival Date</label>
                           <div class="form-group">
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control" name="arrived_date" id="arrived_date" value="" placeholder="Arrival Date" style="text-align: left;"> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">      
                        <div class="col-md-3">
                             <label class="label-account">Comments</label>
                           <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-comment"></i> </span>
                                    <textarea class="form-control" row="5" cols="5" id="comments" name="comments" placeholder="Comments"></textarea>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-3">
                            <label class="label-account">&nbsp;</label>
                            <div class="form-group">
                                <button class="btn btn-primary btn_save" name="btnSave" id="btnSaveNew" type="submit">Save</button>
                                <input type="hidden" name="form_action" id="form_action" value="save" />
                                <input type="hidden" name="id" id="id" value="" />
                            </div>
                        </div>
                    </div>
                </form>
            </div> 
        </div>
        
        <div class="row">
            <div class="col-lg-12 col-xs-12 col-sm-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption"> <i class="fa fa-users"></i>
                            Pre Alert Advice List			
                        </div>

                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-hover table-bordered" id="manage-data-table">
                            <thead>
                                <tr>
                                    <th style="width:100px;">Action</th>
                                    <th>MAWB#</th>
                                    <th>Flight#</th>
                                    <th>Numnber of bags</th>
                                    <th>Weight</th>
                                    <th>ETD</th>
                                    <th>ETA</th>
                                    <th>Arrival Date</th>
                                    <th>Current Status</th>
                                    <th>OWE Status</th>
                                    <th>Shed</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="model_delete" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Delete Pre Alert Advice</h4>
                    </div>
                    <div class="msg_modal"></div>
                    <div class="modal-body">
                        <div class="note note-info">Do you want to delete this pre alert advice with MAWB (<span class="mawb_delete"></span>) ?</div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="modal_delete_id" value="" />
                        <button type="button" class="btn red " data-dismiss="modal">No</button>
                        <button type="button" class="btn green yes">Yes</button>
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
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
