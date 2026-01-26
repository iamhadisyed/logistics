<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'flightinfo.class',
    'flightinfofilter.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    'mawb.class',
    'mawbfilter.class',

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
            'Add Pre-Alert'
        );
        
        if($_GET['action'] && $_GET['action'] == 'prealert_datatable'){
            $flightFilterObj=  new FlighInfoFilter();
           
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                if(!empty($this->form_vars['search_mawb'])){
                    $flightFilterObj->addFilter("   ma.mawb_number = '".$this->form_vars['search_mawb']."'");
                }
                if(!empty($this->form_vars['search_status'])){
                    $flightFilterObj->addFilter("   fi.status =  '".$this->form_vars['search_status']."'");
                }
                if(!empty($this->form_vars['search_flight'])){
                    $flightFilterObj->addFilter("   fi.flight_number LIKE   '%".$this->form_vars['search_flight']."%'");
                }
                if(!empty($this->form_vars['search_weight'])){
                    $flightFilterObj->addFilter("   fi.weight = '".$this->form_vars['search_weight']."'");
                }
                if(!empty($this->form_vars['search_pieces'])){
                    $flightFilterObj->addFilter("   fi.pieces = '".$this->form_vars['search_pieces']."'");
                }
                if(!empty($this->form_vars['search_user_account_id'])){
                    $flightFilterObj->addFilter("   fi.account_id = '".$this->form_vars['search_user_account_id']."'");
                }
            }
            if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
                $flightFilterObj->addFilter("   fi.account_id = '".$this->user->getUserAccountId()."'");
            }
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
                    $status = (!empty($preData->getStatus())? ucwords(str_replace("_"," ",$preData->getStatus())) :'<label class="label label-warning">N/A</label>');
                    $currentArr['edit'] = "<a href='javascript:{};' class='btn btn-xs btn-default blue btn-outline center prealert_edit' rel='tooltip' data-toggle='tooltip' placeholder='Edit' title='Edit' data-id='".$preData->getId()."'> <span class='fa fa-pencil'></span> </a>";
                    $currentArr['mawb'] = $preData->getMawb();
                    $currentArr['flight'] = $preData->getFlightNumber();
                    $currentArr['status'] = $status;
                    $currentArr['eta'] = $preData->getEta();
                    $currentArr['etd'] = $preData->getEtd();
                    $currentArr['weight'] = $preData->getWeight();
                    $currentArr['pieces'] = $preData->getPieces();
                    if ($this->user->getUserType() != User::USER_TYPE_CLIENT) {
                        $currentArr['account'] = $preData->getUserAccount();
                    }
                    $file = '<label class="label label-warning">N/A</label>';
                    if(!empty($preData->getFiles())){
                        $fileLink = SETTING_MAIN_ASSETS . "preadvice/";
                        $file = '<a class="ebayButton" href="' . $fileLink . $preData->getFiles() . '" target="_blank"><i class="fa fa-download"></i></a>';
                    }
                    $currentArr['file'] = $file;
                    
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
            
            $user = Sessionmanager::getUser();
            $id = !empty($this->form_vars["id"]) ? $this->form_vars["id"] : '';
            $flightInfoObj = new FlightInfo();
            if (!empty($id)) {
                $flightInfoObj = new FlightInfo($id);
            }
            $flightInfoObj->setFlightNumber($this->form_vars["flight"]);
            $flightInfoObj->setPieces($this->form_vars["pieces"]);
            $flightInfoObj->setCarriageValue(0.0);
            $flightInfoObj->setCustomValue(0.0);
            $flightInfoObj->setInsuranceAmount(0.0);
            $flightInfoObj->setWeight($this->form_vars["weight"]);
            if(empty($id))
                $flightInfoObj->setDateCreated(date("Y-m-d H:i:s"));
            if (!empty($this->form_vars['user_account_id'])) {
                $flightInfoObj->setAccountId($this->form_vars["user_account_id"]);
            } else {
                $flightInfoObj->setAccountId($this->user->getUserAccountId());
            }
            $flightInfoObj->setEtd($this->form_vars["etd"]);
            $flightInfoObj->setEta($this->form_vars["eta"]);
            $flightInfoObj->setStatus("in_transit");

            $flightInfoObj->setDateCreated(time());
            $flightInfoObj->setCleared('NO');
            $flightInfoObj->setIsDelete(0);
            $flightInfoObj->setStatus('not_assigned');
            if (isset($_FILES['upload_file']) && $_FILES['upload_file']['name']) {
                if (!file_exists('path/to/directory')) {
                    mkdir('../_assets/preadvice', 0777, true);
                }
                $errors = array();
                $file_name = $_FILES['upload_file']['name'];
                $file_size = $_FILES['upload_file']['size'];
                $file_tmp = $_FILES['upload_file']['tmp_name'];
                $file_type = $_FILES['upload_file']['type'];
                $file_ext = strtolower(end(explode('.', $_FILES['upload_file']['name'])));
                $file_name = time().'-'.$file_name;
                if (empty($errors)) {
                    move_uploaded_file($file_tmp, "../_assets/preadvice/" . $file_name);
                }
                if(!empty($_POST['file_update'])){
                    unlink("../_assets/preadvice/" . $_POST['file_update']);
                }
               
                $flightInfoObj->setFiles($file_name);
              
               
            }            
            $flightInfoObj->setCreatedBy($user->getId());
            $userAccount = (!empty($this->form_vars["user_account_id"]) ? $this->form_vars["user_account_id"] : '');
	    $flightInfoObj->setIsClosed(0);
            $flightInfoObj->save();
            $mawbId = 0;
//            check if mawb is already there and opend
            $mawbFilter = new MawbFilter();
            $mawbFilter->addFieldFilter("    mawb_number",$this->form_vars["mawb"]);
            $mawbData = $mawbFilter->getList('id,mawb_status');
            if(count($mawbData) > 0){
                $mawbStatus = $mawbData[0]->getMawbStatus();
                if($mawbStatus == "pd" || $mawbStatus == "o"){
                    $mawbId = $mawbData[0]->getId();
                }
            }
            if($mawbId == 0){
                // Save MAWB first
                $mawb = new Mawb();
                $mawb->setMawbNumber($this->form_vars["mawb"]);
                $mawb->setmawbSourceCountryId($this->user->getCountryId());
                $mawb->setmawbdestinationCountryId($this->user->getCountryId());
                $mawb->setIsActive(1);
                $mawb->setAddedBy($this->user->getId());
                $mawb->setAddedDate(date("Y-m-d H:i:s", time()));
                $mawb->setUpdatedDate(date("Y-m-d H:i:s", time()));
                $mawb->save();
                $mawbId = $mawb->getId();
            }

            $msg = [];
            $flightMapppingObj = new FlightMapping();
            $flightMappDelObj = new FlightMappingFilter();
            $flightMappDelObj->deleteOnFlightId($flightInfoObj->getId());
            $flightMapppingObj->setFlightInfoId($flightInfoObj->getId());
            $flightMapppingObj->setFlightNumber($this->form_vars["flight"]);
            $flightMapppingObj->setMawbId($mawbId);
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


        if (isset($_POST['func']) && $_POST['func'] == 'edit') {
            $id = DbAccess3::escape($_POST["id"]);
            $prealerfilter = new FlighInfoFilter();
            $preList = $prealerfilter->addIdFilter($id);
            $preAlerttData = [];
            $preAlerttData['mawb'] = $preList[0]->getMawb();
            $preAlerttData['flight'] = $preList[0]->getFlightNumber();
            $preAlerttData['pieces'] = $preList[0]->getPieces();
            $preAlerttData['weight'] = $preList[0]->getWeight();
            $preAlerttData['eta'] = $preList[0]->getEta();
            $preAlerttData['etd'] = $preList[0]->getEtd();
            $preAlerttData['file'] = $preList[0]->getFiles();
            $preAlerttData['comments'] = $preList[0]->getComments();
            $preAlerttData['shed'] = $preList[0]->getShed();
            $preAlerttData['status'] = $preList[0]->getStatus();
            $preAlerttData['currenct_status'] = $preList[0]->getCurrentStatus();
//            $preAlerttData['arrived_date'] = date('m/d/Y',  strtotime($preList[0]->getArrivedDate()));
            $preAlerttData['id'] = $preList[0]->getId();
            $preAlerttData['account'] = $preList[0]->getAccountId();
            echo json_encode($preAlerttData);
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

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
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
                                "url": "pre-alert.php?action=prealert_datatable", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "edit", "bSortable": false},
                                {"data": "mawb", "bSortable": false},
                                {"data": "flight", "bSortable": false},
                                {"data": "pieces"},
                                {"data": "weight"},
                                <?php if ($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
                                {"data": "account"},
                                <?php } ?>
                                {"data": "status", "bSortable": false},
                                {"data": "file", "bSortable": false}
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
                $('#etd').datetimepicker({format: 'dd-mm-yyyy hh:ii:ss', step: 10});
                $('#eta').datetimepicker({format: 'dd-mm-yyyy hh:ii:ss', step: 10});

                $("#btnCancel").click(function () {
                    $("#form_action").val("cancel");
                    $("#adminForm").submit();

                });

                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                $('#btnSaveNew').click(function () {
                    if ($('#mawb').val() != '' && $('#weight').val() != '' && $('#pieces').val() != '' && $('#eta').val() != '' && $('#etn').val() && $('#flight').val()) {
                        $('#pre_alert_form').trigger('submit');
                    }
                });
                $("#pre_alert_form").on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'pre-alert.php',
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
                            $('#file_update').val('');
                            $('#pre_alert_form').trigger("reset");
                            grid.getDataTable().ajax.reload();
                            <?php if(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])){?>
                                    window.location = 'pre-alert.php';
                            <?php }?>
                        }
                    });
                });


                $(document).on('click','.prealert_edit',function (){
                    <?php if(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])){?>
                        var id = '<?php echo (int) $_GET['id'];?>';
                    <?php }else {?>
                        var id = $(this).data('id');
                    <?php }?>
                    
                    $.post('pre-alert.php',{func:'edit',id:id},function(data){
                        var obj = JSON.parse(data);
                        if(obj.id){
                            $('#weight').val(obj.weight);
                            $('#pieces').val(obj.pieces);
                            $('#eta').val(obj.eta);
                            $('#etd').val(obj.etd);
                            $('#mawb').val(obj.mawb);
                            $('#flight').val(obj.flight);
                            $('#id').val(obj.id);
                            $("#user_account_id").val(obj.account).change();
                        }
                    });
                });
                <?php if(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])){?>
                        setTimeout(function(){ $('.prealert_edit').trigger('click'); }, 2000);
                        
                <?php }?>
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
                    Pre Alert Advice				
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <form action="" method="post" enctype="multipart/form-data" id="pre_alert_form" name="pre_alert_form">
                    <div class="msg"></div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <div class="form-group col-md-12">
                                    <label class="label-account">MAWB Number</label>
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket "></i> </span>
                                        <input type="text" class="form-control" name="mawb" id="mawb" value="<?php echo @$mawb; ?>" size="100" style="" maxlength="35" rel="tooltip" placeholder="MAWB Number" data-original-title="MAWB Number" required />
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="label-account">Weight</label>
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                        <input type="number" step="any" class="form-control" name="weight" id="weight" value="<?php echo @$weight; ?>" size="100" style=""  maxlength="35" rel="tooltip" placeholder="Weight" data-original-title="Weight" required />
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group col-md-12">
                                    <label class="label-account">Flight Number</label>
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-plane"></i> </span>
                                        <input type="text" class="form-control" name="flight" id="flight" value="<?php echo @$flight; ?>" size="100" style=""  maxlength="35" rel="tooltip" placeholder="Flight Number" data-original-title="Flight Number" required /> 
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="label-account">Estimated Delivery Time</label>
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input class="form-control" name="etd" id="etd" type="text" placeholder="Estimated Delivery Time" value="<?php echo @formatDateTime($this->etd); ?>" rel="tooltip" placeholder="ETD" data-original-title="ETD" required/>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group col-md-12">
                                    <label class="label-account">Pieces</label>
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                        <input type="number" min="0" step="1" class="form-control" name="pieces" id="pieces" value="<?php echo @$pieces; ?>" size="100" style=""  maxlength="35" rel="tooltip" placeholder="Pieces" data-original-title="Pieces" required/>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="label-account">Estimated Arrival Time</label>
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input class="form-control" name="eta" id="eta" type="text" placeholder="Estimated Arrival Time" value="<?php echo @formatDateTime($this->eta); ?>" rel="tooltip" placeholder="ETA" data-original-title="ETA" required/>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group col-md-12">
                                    <label class="label-account">Upload File</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" name="upload_file" id="upload_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" rel="tooltip" placeholder="Upload File" data-original-title="Upload File"/>
                                            <?php
                                            if (!empty($id)) {
                                                if (!empty($this->uploadfilelist)) {
                                                    ?>
                                                <div style="border-style:solid; border-width:1px; border-color:black; padding:5px; width:280px;">
                                                <?php
                                                echo $this->uploadfilelist;
                                                ?> 

                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php if ($sessionUser->getUserType() != User::USER_TYPE_CLIENT) {   ?> 
                                <div class="form-group col-md-12">
                                    <label class="label-account">Account</label>
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                                    </div>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">      
                        <div class="col-md-12" style="text-align: center;">
                            <button class="btn btn-primary btn_save" name="btnSave" id="btnSaveNew" type="submit">Save</button>
                            <input type="hidden" name="form_action" id="form_action" value="save" />
                            <input type="hidden" name="id" id="id" value="<?php echo (!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])? (int) $_GET['id']:'')?>" />
                            <input type="hidden" name="file_update" id="file_update" value="" />
                            <button id="btnCancel" class="btn btn-danger btn_cancel">Cancel</button>
                        </div>
                    </div>
                </form>
            </div> 
        </div>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-users"></i>
                    Pre Alert List
                </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Edit</th>
                                <th>MAWB#</th>
                                <th>Flight#</th>
                                <th>Pieces</th>
                                <th>Weight</th>
                                <?php if ($sessionUser->getUserType() != User::USER_TYPE_CLIENT) { ?>
                                <th >Account</th>
                                <?php } ?>
                                <th>Status</th>
                                <th>Files</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_mawb" id="search_mawb">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_flight">
                                </td>
                                <td>
                                    <input type="number"  min="0" step="1" class="form-control form-filter input-sm" name="search_pieces">
                                </td>
                                <td class="user_acccount_correct_button">
                                    <input type="number" step="any" class="form-control form-filter input-sm" name="search_weight">
                                </td>
                                <?php if ($sessionUser->getUserType() != User::USER_TYPE_CLIENT) { ?>
                                <td class="user_acccount_correct_button">
                                <?php                                     
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                            $accountParentId = $this->user->getUserAccountId();
                                            $includeParent = false;
                                        }
                                        $selectedAccount = (!empty($this->userAccountId) ? $this->userAccountId : '');
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        echo Ddl::showTreeDropdown('search_user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel);
                                        
                                ?>
                                </td>
                                <?php } ?>
                               <td class="user_acccount_correct_button">
                                    <?php
                                    $dateTypes = array(
                                        'not_assigned' => "Not Assigned",
                                        'assigned' => "Assigned",
                                        'in_warehouse' => "In Warehouse"
                                    );
                                    echo Ddl::generateArrayDDL('search_status', $dateTypes, '', '', 'class="form-filter bs-select form-control" ', "", $dd_id = 'search_status');
                                    ?>
                                </td>
                                <td>
                                    
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
