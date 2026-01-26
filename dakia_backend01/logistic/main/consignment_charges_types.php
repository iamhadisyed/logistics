<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'invoices.class',
    'invoicesfilter.class']);
/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $uploadfilelist = "";
    private $user;
    private $title;
    private $charges_key;
    private $charges_type;
    private $apply_per_kg;
    private $is_extra_charge;
    private $is_vat;
    private $has_account_default_value;
    private $status;

    protected function init() {
        $this->user = SessionManager::getUser();
        
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"), 'Consignment Charges Type');
        
        if($_GET['action'] && $_GET['action'] == 'prealert_datatable'){
            $consignmentObj = new ConsignmentChargesTypesFilter();
            $chargesKeyArr = ['BASIC_CHARGES','LABEL_CHARGES','REMOTE_AREA_CHARGES','VAT','DISCOUNT'];
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                if(!empty($this->form_vars['search_title'])){
                    $consignmentObj->addFilter("   cct.title LIKE   '%".$this->form_vars['search_title']."%'");
                }
                if(!empty($this->form_vars['search_key'])){
                    $consignmentObj->addFilter("   cct.charges_key LIKE   '%".$this->form_vars['search_key']."%'");
                }
                if(isset($this->form_vars['search_status']) && $this->form_vars['search_status'] !=''){
                    $consignmentObj->addFilter("   cct.status =  '".$this->form_vars['search_status']."'");
                }
                if(isset($this->form_vars['search_vat']) && $this->form_vars['search_vat'] != ''){
                    $consignmentObj->addFilter("   cct.is_vat = '".$this->form_vars['search_vat']."'");
                }
                if(isset($this->form_vars['search_extra_charges']) && $this->form_vars['search_extra_charges'] !=''){
                    $consignmentObj->addFilter("   cct.is_extra_charge = '".$this->form_vars['search_extra_charges']."'");
                }
                if(isset($this->form_vars['search_apply_per_kg']) && $this->form_vars['search_apply_per_kg'] !=''){
                    $consignmentObj->addFilter("  cct.apply_per_kg = '".$this->form_vars['search_apply_per_kg']."'");
                }
                if(isset($this->form_vars['is_replace_charges']) && $this->form_vars['is_replace_charges'] !=''){
                    $consignmentObj->addFilter("  cct.is_replace_charges = '".$this->form_vars['is_replace_charges']."'");
                }
                if(isset($this->form_vars['search_default_value']) && $this->form_vars['search_default_value'] !=''){
                    $consignmentObj->addFilter("   cct.has_account_default_value = '".$this->form_vars['search_default_value']."'");
                }
                if(!empty($this->form_vars['search_charge_type'])){
                    $consignmentObj->addFilter("   cct.charge_type = '".$this->form_vars['search_charge_type']."'");
                }
				if(!empty($this->form_vars['search_apply_by'])){
					$consignmentObj->addFilter("   cct.apply_by = '".$this->form_vars['search_apply_by']."'");
				}
            }
            $ConsigmentCount = $consignmentObj->getCount();
            $iTotalRecords = $ConsigmentCount;
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $consignmentObj->setRowsPerPage($iDisplayLength);
            $consignmentObj->setOffset($iDisplayStart);
            $consignmentObj->AddOrderBy("id", false);
            $dataConsignment = $consignmentObj->getList();

            if(!empty($dataConsignment)){
                foreach($dataConsignment as  $preData){
                    $sucess = '<label class="label label-success">';
                    $endSucess = '</label>';
                    $danger = '<label class="label label-danger">';
                    $endDanger = '</label>';
                    $currentArr = array();
                    $actionDelete = '';
                    if(!in_array($preData->getChargesKey(),$chargesKeyArr))
                    $actionDelete = "<a href='javascript:{};' class='btn btn-xs btn-default red btn-outline center types_delete' rel='tooltip' data-toggle='tooltip' placeholder='Delete' title='Delete' data-name='".$preData->getTitle()."' data-id='".$preData->getId()."'> <span class='fa fa-trash'></span> </a>";
                    $actionAudit = "<a href='' class='btn btn-xs blue btn mt-ladda-btn ladda-button btn-outline' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $preData->getId() . "' data-log_name='consignment_charges_types' data-toggle='modal'> <span class='fa fa-list'></span> </a>";
                    $currentArr['edit'] = "<a href='javascript:{};' class='btn btn-xs btn-default blue btn-outline center prealert_edit' rel='tooltip' data-toggle='tooltip' placeholder='Edit' title='Edit'  data-id='".$preData->getId()."'> <span class='fa fa-pencil'></span> </a> $actionDelete $actionAudit";
                             
                    $currentArr['title'] = ucwords($preData->getTitle());
                    $currentArr['charges_types'] = ucwords($preData->getChargeType());
                    $currentArr['key'] = $preData->getChargesKey();
                    $status  = (!empty($preData->getstatus())?$sucess.'Yes'.$endSucess:$danger.'No'.$endDanger);
                    $currentArr['status'] = $status;
					$currentArr['apply_by'] = ucwords($preData->getApplyBy());
                    $vat = (!empty($preData->getIsVat())?$sucess.'Yes'.$endSucess:$danger.'No'.$endDanger);
                    $currentArr['is_vat'] = $vat;
                    $extraCharges = (!empty($preData->getIsExtraCharge())?$sucess.'Yes'.$endSucess:$danger.'No'.$endDanger);
                    $currentArr['is_extra_charge'] = $extraCharges;
                    $applyPerKg = (!empty($preData->getApplyPerKg())?$sucess.'Yes'.$endSucess:$danger.'No'.$endDanger);
                    $currentArr['apply_per_kg'] = $applyPerKg;
                    $isReplaceCharges = (($preData->getIsReplaceCharges() == 1)?$sucess.'Yes'.$endSucess:$danger.'No'.$endDanger);
                    $currentArr['is_replace_charges'] = $isReplaceCharges;
                    
                    $isExpirable = (($preData->getIsExpirable() == 1)?$sucess.'Yes'.$endSucess:$danger.'No'.$endDanger);
                    $currentArr['is_expirable'] = $isExpirable;
                    
                    $defaultValue = (!empty($preData->getHasAccountDefaultValue())?$sucess.'Yes'.$endSucess:$danger.'No'.$endDanger);
                    $currentArr['has_account_default_value'] = $defaultValue;
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
            $title = $this->form_vars["title"];
            $key = '';
            $consignmentObj = new ConsignmentChargesTypes();
            if (!empty($id)) {
                $consignmentObj = new ConsignmentChargesTypes($id);
            }else{
                $key = $this->clean($this->form_vars["title"]);
                $consignmentObj->setChargesKey($key);
            }
            $consignmentObj->setTitle($title);
            if(!empty($this->form_vars["charges_type"]))
                $consignmentObj->setChargeType(1);
            else
                $consignmentObj->setChargeType(0);
            if(!empty($this->form_vars["apply_per_kg"]))
                $consignmentObj->setApplyPerKg(1);
            else
                $consignmentObj->setApplyPerKg(0);
            if(!empty($this->form_vars["is_replace_charges"]))
                $consignmentObj->setIsReplaceCharges(1);
            else
                $consignmentObj->setIsReplaceCharges(0);
            if(!empty($this->form_vars["is_expirable"]))
                $consignmentObj->setIsExpirable(1);
            else
                $consignmentObj->setIsExpirable(0);
            if(!empty($this->form_vars["is_extra_charge"]))
                $consignmentObj->setIsExtraCharge(1);
            else
                $consignmentObj->setIsExtraCharge(0);
            if(!empty($this->form_vars["is_vat"]))
                $consignmentObj->setIsVat(1);
            else
                $consignmentObj->setIsVat(0);
            if(!empty($this->form_vars["has_account_default_value"]))
                $consignmentObj->setHasAccountDefaultValue(1);
            else
                $consignmentObj->setHasAccountDefaultValue(0);
            if(!empty($this->form_vars["status"]))
                $consignmentObj->setStatus(1);
            else
                $consignmentObj->setStatus(0);

			$consignmentObj->setApplyBy($this->form_vars["apply_by"]);
            
            $consignmentObj->getAddedBy($user->getId());
            $consignmentObj->getUpdatedBy($user->getId());
            
            $typesObj = new ConsignmentChargesTypesFilter();
            $typesObj->addFieldFilter('charges_key', $key);
            $returnKeyData = $typesObj->getList();
            
            
            $msg = [];
            if(empty($returnKeyData) && empty($id)){
                $consignmentObj->save();
            }else if(!empty($returnKeyData) && empty($id)){
                $msg['error'] = "<div class='col-md-12 alert alert-danger'>We are unable to save data. The charges key will not duplicate.</div>";
            }else if (!empty($consignmentObj->getId())) {
                $consignmentObj->save();
                $msg['sucess'] = "<div class='col-md-12 alert alert-success'>Consignment charges types data has been saved.</div>";
            } else {
                $msg['error'] = "<div class='col-md-12 alert alert-danger'>Consignment charges types data has not been save.</div>";
            }
            echo json_encode($msg);
            exit;
        }
        
        if (isset($_POST['func']) && $_POST['func'] == 'delete') {
            $id = (!empty($this->form_vars['id'])?$this->form_vars['id']:0);
            $name = (!empty($this->form_vars['name'])?$this->form_vars['name']:'');
            $msg = [];
            if(!empty($id)){
                $consignmentTypesObj = new ConsignmentChargesTypes($id);
                $consignmentTypesObj->setIsDeleteble(1);
                $consignmentTypesObj->save();
                if(!empty($consignmentTypesObj->getId())){
                    $msg['sucess'] = "$name has been deleted";
                }else{
                    $msg['error'] = "We are unable to delete the $name.";
                }
                
            }else{
                $msg['error'] = "We are unable to delete the $name.";
            }
            echo json_encode($msg);
            exit;
        }

        if (isset($_POST['func']) && $_POST['func'] == 'edit') {
            $id = $_POST["id"];
            $consignmentObj = new ConsignmentChargesTypesFilter();
            $consignmentObj->addFieldFilter("id", $id);
            $typesDate = $consignmentObj->getList();
            $consginmentTypes = [];
            if(!empty($typesDate)){
                foreach($typesDate as $data){
                    $consginmentTypes['id'] = $data->getId();
                    $consginmentTypes['vat'] = $data->getIsVat();
                    $consginmentTypes['title'] = $data->getTitle();
                    $consginmentTypes['type'] = $data->getChargeType();
                    $consginmentTypes['perKg'] = $data->getApplyPerKg();
                    $consginmentTypes['isReplaceCharges'] = $data->getIsReplaceCharges();
                    $consginmentTypes['isExpirable'] = $data->getIsExpirable();
                    $consginmentTypes['extraCharge'] = $data->getIsExtraCharge();
					$consginmentTypes['applyBy'] = $data->getApplyBy();
                    $consginmentTypes['status'] = $data->getStatus();
                    $consginmentTypes['defaultValue'] = $data->getHasAccountDefaultValue();
                }
            }
            echo json_encode($consginmentTypes);
            exit;
        }
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />        
        <?php
    }

    public function clean($string) {
        $string = str_replace(' ', '-', $string); 
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $return = preg_replace('/-+/', '_', $string);
        return strtoupper($return);
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
                                "url": "consignment_charges_types.php?action=prealert_datatable", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "edit", "bSortable": false,"className":"text-center"},
                                {"data": "title", "bSortable": false},
                                {"data": "key", "bSortable": false},
                                {"data": "charges_types", "bSortable": false,"className":"text-center"},
								{"data": "apply_by", "bSortable": false,"className":"text-center"},
                                {"data": "is_vat"},
                                {"data": "is_extra_charge", "bSortable": false},
                                {"data": "apply_per_kg", "bSortable": false},
                                {"data": "is_replace_charges", "bSortable": false},
                                {"data": "has_account_default_value", "bSortable": false},
                                {"data": "status"},
                                {"data": "is_expirable"}
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

                $("#btnCancel").click(function () {
                    $("#form_action").val("cancel");
                    $("#adminForm").submit();

                });

                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                $('#btnSaveNew').click(function () {
                    if ($('#title').val() != '') {
                       // $('#consignment_charges_form').trigger('submit');
                    }
                });
                $("#consignment_charges_form").on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'consignment_charges_types.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            var obj = JSON.parse(data);
                            if (obj.sucess) {
                                $('.msg').show();
                                $('.msg').html(obj.sucess);
                            } else if (obj.error) {
                                $('.msg').show();
                                $('.msg').html(obj.error);
                            }
                            setTimeout(function(){ $('.msg').hide(); }, 7000);
                            $('#id').val('');
                            $('#consignment_charges_form').trigger("reset");
                            grid.getDataTable().ajax.reload();
                            <?php if(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])){?>
                                    window.location = 'consignment_charges_types.php';
                            <?php }?>
                        }
                    });
                });

                $(document).on('click','.types_delete',function (){
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    swal({
                        title: "Are you sure you want to delete?",
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
                            $.post('consignment_charges_types.php',{func:'delete',id:id,name:name},function(data){
                                var obj = JSON.parse(data);
                                if (obj.sucess) {
                                    swal("Delete", obj.sucess, "info");
                                    grid.getDataTable().ajax.reload();
                                } else if (obj.error) {
                                    swal("Delete",obj.error, "danger");
                                }
                            });
                        } else {
                            return false;
                        }
                    });
                });

                $(document).on('click','.prealert_edit',function (){
                    <?php if(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])){?>
                        var id = '<?= $_GET['id'];?>';
                    <?php }else {?>
                        var id = $(this).data('id');
                    <?php }?>
                    
                    $.post('consignment_charges_types.php',{func:'edit',id:id},function(data){
                        var obj = JSON.parse(data);
                        if(obj.id){
                            
                            $("#title").val(obj.title);
                            $("#charges_type").val(obj.type).change();
                            if(obj.perKg === '1'){
                                $('input[name=apply_per_kg]').bootstrapSwitch('state', true);

                            }else{
                                $('input[name=apply_per_kg]').bootstrapSwitch('state', false);
                            }
                            if(obj.isExpirable === '1'){
                                $('input[name=is_expirable]').bootstrapSwitch('state', true);

                            }else{
                                $('input[name=is_expirable]').bootstrapSwitch('state', false);
                            }
                            
                            if(obj.isReplaceCharges === '1'){
                                $('input[name=is_replace_charges]').bootstrapSwitch('state', true);

                            }else{
                                $('input[name=is_replace_charges]').bootstrapSwitch('state', false);
                            }
                            if(obj.extraCharge === '1')
                                $('input[name=is_extra_charge]').bootstrapSwitch('state', true);
                            else
                                $('input[name=is_extra_charge]').bootstrapSwitch('state', false);
                            
                            if(obj.status === '1')
                               $('input[name=status]').bootstrapSwitch('state', true);
                            else
                                $('input[name=status]').bootstrapSwitch('state', false);
                            
                            if(obj.defaultValue === '1')
                                $('input[name=has_account_default_value]').bootstrapSwitch('state', true);
                            else
                                $('input[name=has_account_default_value]').bootstrapSwitch('state', false);
                            
                            if(obj.vat === '1')
                                $('input[name=is_vat]').bootstrapSwitch('state', true);
                            else
                               $('input[name=is_vat]').bootstrapSwitch('state', false);

							$('#apply_by').val(obj.applyBy);


                            $('#id').val(obj.id);
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
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
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
                <div class="caption"> <i class="fa fa-money"></i>
                    Consignment Charges	Types			
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <form action="" method="post" enctype="multipart/form-data" id="consignment_charges_form" name="consignment_charges_form">
                    <div class="msg"></div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
								<div class="has-float-label input-icon right">
									<i class="fa fa-ticket "></i>
									<input type="text" class="form-control" name="title" id="title" value="<?php echo $this->title; ?>" size="100" style="" maxlength="35" rel="tooltip" placeholder="Title" data-original-title="title" required />
									<label for="title">Title</label>
								</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
								<div class="has-float-label input-icon right">
									 <select class="form-control" name="charges_type" id="charges_type">
										<!--<option value="">Select Any One</option>-->
										 <option value="both">Both</option>
										 <option value="customer">Customer</option>
										 <option value="agent">Agent</option>
									 </select>
									  <label class="label-account">Charges Type</label>
								 </div>
                              </div>
                        </div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="has-float-label input-icon right">
									<select class="form-control" name="apply_by" id="apply_by">
										<option value="fixed">Fixed</option>
										<option value="country">Zone Wise</option>
									</select>
									<label class="label-account">Apply By</label>
								</div>
							</div>
						</div>
                        <div class="col-md-3">
                            <div class="form-group">
								<div class="has-float-label input-icon right">
									<input id="is_extra_charge" name="is_extra_charge" type="checkbox" class="make-switch" <?php echo ($this->is_extra_charge == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
									<label class="label-account">Extra Charges</label>
								</div>
							</div>
                        </div>
                        <div class="col-md-3">
							<div class="form-group">
								<div class="has-float-label input-icon right">
									<input id="apply_per_kg" name="apply_per_kg" type="checkbox" class="make-switch" <?php echo ($this->apply_per_kg == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
									<label class="label-account">Apply Per Kg</label>
								</div>
							</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
								<div class="has-float-label input-icon right">
									<input id="is_vat" name="is_vat" type="checkbox" class="make-switch" <?php echo ($this->is_vat == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
									<label class="label-account">Vat</label>
								</div>
                           </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
								<div class="has-float-label input-icon right">
									<input id="status" name="status" type="checkbox" class="make-switch" <?php echo ($this->status == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
									<label class="label-account">Status</label>
								</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
								<div class="has-float-label input-icon right">
									<input id="has_account_default_value" name="has_account_default_value" type="checkbox" class="make-switch" <?php echo ($this->has_account_default_value == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
									<label class="label-account">Account Default Value</label>
								</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
							   <div class="has-float-label input-icon right">
									<input id="is_replace_charges" name="is_replace_charges" type="checkbox" class="make-switch" <?php echo ($this->is_replace_charges == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
									<label class="label-account">Is Replace Charges</label>
							   </div>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                            <div class="has-float-label input-icon right">
                                <input id="is_expirable" name="is_expirable" type="checkbox" class="make-switch" <?php echo ($this->is_expirable == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
                                <label class="label-account">Is Expirable</label>                                    
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">      
                        <div class="col-md-12" style="text-align: center;">
                            <button class="btn btn-primary btn_save" name="btnSave" id="btnSaveNew" type="submit">Save</button>
                            <input type="hidden" name="form_action" id="form_action" value="save" />
                            <input type="hidden" name="id" id="id" value="<?php echo (!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])?$_GET['id']:'')?>" />
<!--                            <button id="btnCancel" class="btn btn-danger btn_cancel">Cancel</button>-->
                        </div>
                    </div>
                </form>
            </div> 
        </div>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-money"></i>
                    Consignment Charges Types
                </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th style="width: 105px;">Edit</th>
                                <th>Title</th>
                                <th>Charges Key</th>
                                <th>Charges Types</th>
								<th>Apply By</th>
                                <th>Vat</th>
                                <th>Extra Charges</th>
                                <th>Apply Per Kg</th>
                                <th>Is Replace Charges</th>
                                <th>Account Default Value</th>
                                <th>Status</th>
                                <th>Is Expirable</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_title" id="search_title">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_key">
                                </td>
                                <td>
                                   <select class="form-control form-filter" name="search_charge_type" id="search_charge_type">
                                        <option value="">Select Any</option>
                                        <option value="both">Both</option>
                                        <option value="customer">Customer</option>
                                        <option value="agent">Agent</option>
                                    </select>
                                </td>
								<td>
									<select class="form-control form-filter" name="search_apply_by" id="search_apply_by">
										<option value="">Select Any</option>
										<option value="fixed">Fixed</option>
										<option value="country">Country Wise</option>
										<option value="postcode">Postcode Wise</option>
										<option value="state">State Wise</option>
									</select>
								</td>
                                <td>
                                    <select class="form-control form-filter" name="search_vat" id="search_vat">
                                        <option value="">Select Any</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-filter" name="search_extra_charges" id="search_extra_charges">
                                        <option value="">Select Any</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-filter" name="search_apply_per_kg" id="search_apply_per_kg">
                                        <option value="">Select Any</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-filter" name="is_replace_charges" id="is_replace_charges">
                                        <option value="">Select Any</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-filter" name="search_default_value" id="search_default_value">
                                        <option value="">Select Any</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-filter" name="search_status" id="search_status">
                                        <option value="">Select Any</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-filter" name="search_is_expirable" id="search_is_expirable">
                                        <option value="">Select Any</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
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
