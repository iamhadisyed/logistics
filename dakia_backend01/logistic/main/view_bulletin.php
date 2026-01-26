<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'ivisualcomponent'
        ], 'library');
include_classes([
    'country.class',
    'countryfilter.class',
    'bulletins.class',
    'bulletinsfilter.class',
]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $uploadfilelist = "";
    private $user;
    private $selected_user;
    private $errormsg = '';

    protected function init() {
        $this->user = SessionManager::getUser();
         $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'view_bulletin.php' => "Customer & Agent Bulletins"
        );
        if ($_GET['action'] && $_GET['action'] == 'view_bulletin') {
            $bulletInObj = new BulletinsFilter();
            $bulletInObj->addFieldFilter("created_by", $this->user->getId());
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $heading = $this->form_vars['heading'];
                if (!empty($heading))
                    $bulletInObj->addLikeFilter('   heading', $heading);
                
                $createdDate = $this->form_vars['created_date'];
                if (!empty($createdDate))
                    $bulletInObj->addFieldFilter('   DATE(date_submitted)',date("Y-m-d",  strtotime ($createdDate)));
            }
            
            $totalRecords = $bulletInObj->getCount();
            $iTotalRecords = $totalRecords;
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $bulletInObj->setRowsPerPage($iDisplayLength);
            $bulletInObj->setOffset($iDisplayStart);
            $resultOfBulletIn = $bulletInObj->getList();

            if (!empty($resultOfBulletIn)) {
                foreach ($resultOfBulletIn as $key => $viewData) {
                    $currentArr = array();
                    $currentArr['heading'] = $viewData->getHeading();
                    $currentArr['created_date'] = date('Y-m-d', $viewData->getDateCreated());
                    $currentArr['action'] = '<button type="button" class="btn btn-primary btn-sm" id="send_email_all" data-id=' . $viewData->getId() . ' data-toggle="modal" data-target="#myModalSendEmail">Send Email</button>
                                                <button type="button" class="btn btn-primary btn-sm" id="edit_bulletin" data-id=' . $viewData->getId() . ' data-toggle="modal" data-target="#myModalBulletins">Edit Bulletin</button>
';
                    $setDataArr[] = $currentArr;
                }
            }
            $setDataArrJson['data'] = (!empty($setDataArr) ? $setDataArr : 0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            exit;
        }

        if (isset($_POST['action']) && trim($_POST['action']) == "SAVE_BULLETIN") {
            $this->user = SessionManager::getUser();
            $id = $_POST["id"];
            $heading = $_POST["heading"];
            $description = htmlspecialchars($_POST["description"]);
            $date_created = $_POST["date_created"];

            if (isset($heading) && !empty($heading) && isset($description) && !empty($description)) {
                if (!empty($id)) {
                    $Bulletins = new Bulletins($id);
                } else {
                    $Bulletins = new Bulletins();
                }
                $Bulletins->setHeading($heading);
                $Bulletins->setDescription($description);

                if (empty($date_created) || $date_created == '1970-01-01 00:00:00')
                    $date_created = date("Y-m-d");

                $Bulletins->setDateSubmitted(strtotime($date_created));
                $Bulletins->setDateCreated(time());
                $Bulletins->setCreatedBy($this->user->getUserAccount());
                $Bulletins->save();
                exit;
            }else {
                echo "Error";
                exit;
            }
        }
        if (isset($_POST['action']) && trim($_POST['action']) == "EDIT_BULLETIN_DATA") {

            $Bulletins = new Bulletins($_POST["id"]);

            if (!empty($Bulletins)) {
                $responceArray = array();
                $responceArray['ID'] = $Bulletins->getId();
                $responceArray['HEADING'] = $Bulletins->getHeading();
                $responceArray['DESCRIPTION'] = $Bulletins->getDescription();
                $responceArray['DATE_CREATED'] = date("Y-m-d", $Bulletins->getDateSubmitted());
                echo json_encode($responceArray);
                exit;
            }
            exit;
        }
        if (isset($_POST['action']) && $_POST['action'] == "SEND_BULLETIN_EMAIL") {
            $bulletin_id = $_POST["id"];
            $sendto = $_POST["sendto"];
            $search_criteria = $_POST["selective"];
            $detail = $_POST["detail"];
            $from_date = $_POST["from_date"];
            $to_date = $_POST["to_date"];
            
            if (!empty($sendto) && !empty($search_criteria)) {
                if ($search_criteria != "send_to_all") {
                    $consignment_filter = new ConsignmentFilter();
                    if ($search_criteria == "country")
                        $consignment_filter->addCountryFilter($detail);
                    else if ($search_criteria == "city")
                        $consignment_filter->addFilter("city like '%" . $detail . "%'");
                    else if ($search_criteria == "mawb" || $search_criteria == "manifestno") {
                        $ManifestDataFilter = new ManifestFilter();
                        if ($search_criteria == "mawb")
                            $ManifestDataFilter->addFilter(" AND mawb like '%" . $detail . "%'");
                        else if ($search_criteria == "manifestno")
                            //$ManifestDataFilter->addIdFilter($detail);
                            $manifestid = $detail;

                        //$manifestList = $ManifestDataFilter->getColumnList("id");
                       
                        if (!empty($manifestid)) {
                            //$manifestid = $manifestList[0]->getId();
                            $ManifestConsignmentDataFilter =  new ManifestEntityMappingFilter();
                            $manifestidList = $ManifestConsignmentDataFilter->getConsignmentNo($manifestid);
                            //$manifestidList = $ManifestConsignmentDataFilter->getList();
                            if (!empty($manifestidList)) {
                                $consignmentIdArray = array();
                                foreach ($manifestidList as $m) {
                                    $consignmentIdArray[] = $m->getConsignmentId();
                                }
                            }
                        }
                        
                        $consignment_filter->addIdArrayFilter($consignmentIdArray);
                    }

                    if ($search_criteria == "country" || $search_criteria == "city") {
                        if (isset($from_date) && $from_date != '1970-01-01' && isset($to_date) && $to_date != '1970-01-01')
                            $consignment_filter->addDateScannedRangeFilter(strtotime($from_date), strtotime($to_date));
                        else
                            $this->errormsg = "Please select Date Range";
                    }

                    if ($sendto == "customer") {
                        $consignment_filter->addGroupByClause("user_id");
                        $fields = "user_id";
                    } else if ($sendto == "agent") {
                        $consignment_filter->addGroupByClause("agent_id");
                        $fields = "agent_id";
                    }


                    $consignment_list = $consignment_filter->getColumnList($fields);

                    if (!empty($consignment_list)) {
                        $customer_account = array();
                        $agent_account = array();
                        foreach ($consignment_list as $clist) {
                            if ($sendto == "customer")
                                $customer_account[] = $clist->getUserId();
                            else if ($sendto == "agent")
                                $agent_account[] = $clist->getAgentId();
                        }
                    }
                    
                    if (!empty($customer_account)) {
                        $userflr = new UserFilter(); 
                        $userflr->addUserAccountInFilter($customer_account);
                        $userList = $userflr->getColumnList("email");
                        
                        if (!empty($userList)) {
                            $userEmail = array();
                            foreach ($userList as $ulist) {
                                if ($ulist->getEmail() != '')
                                    $userEmail[] = $ulist->getEmail();
//                                else if (trim($ulist->getAlternativeEmail()) != '')
//                                    $userEmail[] = $ulist->getAlternativeEmail();
                            }
                        }
                    }
                    if (!empty($agent_account)) {
                        $agentFilter = new AgentDataFilter();
                        $agentFilter->addAgentIdInFilter($agent_account);
                        $agentList = $agentFilter->getColumnList("id, email, alternative1_email");
                        if (!empty($agentList)) {
                            $agentEmail = array();
                            foreach ($agentList as $alist) {
                                if ($alist->getEmail() != '')
                                    $agentEmail[] = $alist->getEmail();
                                else if (trim($alist->getAlternative1Email()) != '')
                                    $agentEmail[] = $alist->getAlternative1Email();
                            }
                        }
                    }
                }
                else {
                    if ($sendto == "customer") {
                        $userflr = new UserAccountFilter();
                        $userflr->addActiveFlagFilter();
                        $userflr->addUserTypeFilter(User::USER_TYPE_CLIENT);
                        $userList = $userflr->getColumnList("email, alternative_email");
                        if (count($userList) > 0) {
                            $userEmail = array();
                            foreach ($userList as $ulist) {
                                if (trim($ulist->getEmail()) != '')
                                    $userEmail[] = $ulist->getEmail();
                                else if (!empty($ulist->getAlternativeEmail()))
                                    $userEmail[] = $ulist->getAlternativeEmail();
                            }
                        }
                    }else if ($sendto == "agent") {
                        $agentFilter = new AgentDataFilter();
                        $agentFilter->addActiveFilter("1");
                        $agentList = $agentFilter->getColumnList("id, email, alternative1_email");
                        if (count($agentList) > 0) {
                            $agentEmail = array();
                            foreach ($agentList as $alist) {
                                if (trim($alist->getEmail()) != '')
                                    $agentEmail[] = $alist->getEmail();
                                else if (trim($alist->getAlternative1Email()) != '')
                                    $agentEmail[] = $alist->getAlternative1Email();
                            }
                        }
                    }
                }
                if (!empty($agentEmail) || !empty($userEmail)) {

                    $bullent_record = new Bulletins($bulletin_id);

                    if (!empty($bullent_record)) {
                        $email = implode(";", @$userEmail) . ";" . implode("; ", @$agentEmail);
                        //$email = 'alliee.safdar@gmail.com';
                        //$email = "cs@oneworldexpress.com; itsupport@oneworldexpress.com";
                        $subject = $bullent_record->getHeading();
                        $headers = "From: itsupport@oneworldexpress.com \r\n";
                        $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                        //$headers .= 'Cc: itsupport@oneworldexpress.com;' . "\r\n";
                        $headers .= "Cc: cs@oneworldexpress.com;  itsupport@oneworldexpress.com; \r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        $msg = [];
                        $message = "<div>" . nl2br($bullent_record->getDescription()) . "</div>";
                        if (mail($email, $subject, $message, $headers)) {
                            $msg['msg'] = "Email Send Successfully";
                            echo json_encode($msg);
                            die;
                        } else {
                            $msg['msg'] = "Unable to Send Successfully<br/>".$this->errormsg;
                            echo json_encode($msg);
                            die;
                        }
                    }
                }else {
                    $msg['msg'] = "Unable to Send Successfully";
                    echo json_encode($msg);
                    die;
                    
                }
            }

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
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

        <script src="../assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="./../assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
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
                                "url": "view_bulletin.php?action=view_bulletin", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "action", "bSortable": false},
                                {"data": "heading", "bSortable": false},
                                {"data": "created_date", "bSortable": false},
                            ],
                            rowCallback: function (row, data, index) {
                                var cssClass = $('td input', row).val();
                                //$('td',row).removeClass("sorting_1").addClass(cssClass);
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


            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    });
                }
                $(document).on('click', '#add_bulletin', function () {
                    $.blockUI();
                    var id = $("#bulletin_id").val();
                    var heading = $("#bulletin_heading").val();
                    var description = $("#bulletin_description").val();
                    var date_created = $("#date_created").val();
                    if (heading && description && date_created) {
                        $.post(
                            "view_bulletin.php",
                            {action: 'SAVE_BULLETIN', id: id, heading: heading, description: description, date_created: date_created},
                            function (data)
                            {
                                $("#date_created").val('');
                                $("#bulletin_description").val('');
                                $("#bulletin_heading").val('');
                                $('#myModalBulletins').modal('hide');
                                $.unblockUI();
                                grid.getDataTable().ajax.reload();
                            }
                        );
                    }
                });

                $(document).on('click', '#edit_bulletin', function () {
                    $.blockUI();
                    var id = $(this).data('id');
                    $.post(
                            "view_bulletin.php",
                            {action: 'EDIT_BULLETIN_DATA', id: id},
                            function (data)
                            {
                                $.unblockUI();
                                $("#bulletin_heading").val('');
                                $("#bulletin_description").val('');
                                $("#date_created").val('');
                                $("#bulletin_id").val('');
                                if ($.type(data) === 'object') {
                                    $("#bulletin_heading").val(data.HEADING);
                                    $("#bulletin_description").val(data.DESCRIPTION);
                                    $("#date_created").val(data.DATE_CREATED);
                                    $("#bulletin_id").val(data.ID);
                                }
                            }
                    , "json");
                });
                
                $(document).on('click', '#send_bulletin', function () {
                    $.blockUI();
                    var id = $(this).data('id');
                    var sendto = $('input[name=sendto]:checked').val();
                    var selective = $('input[name=selective]:checked').val();
                    var detail = $("#detail_criteria").val();
                    var from_date = $("#from_date").val();
                    var to_date = $("#to_date").val();
                    $.post(
                        "view_bulletin.php",
                        {action: 'SEND_BULLETIN_EMAIL', id: id, sendto: sendto, selective: selective, detail: detail, from_date: from_date, to_date: to_date},
                        function (data)
                        {
                            $.unblockUI();
                           if(data.msg){
                               $('.msg').addClass('alert alert-info');
                               $('.msg').html(data.msg);
                           }
                            //$('#myModalSendEmail').modal('hide')

                        }
                    ,'json');
                });
                $(document).on('click', '#send_email_all', function () {
                    var id = $(this).data('id');
                    $('#send_bulletin').attr('data-id',id);
                    $('.msg').removeClass('alert alert-info');
                    $('.msg').html('');
                    $("#from_date").val('');
                    $("#detail_criteria").val('');
                    $("#to_date").val('');
                });
                
                $('input[name=selective]').on('ifClicked', function(event){
                    var selected = $(this).val();
                    if(selected == 'country'){
                        $('.bs-select').show();
                        $('.detail_criteria').hide();
                    }else{
                        $('.bs-select').hide();
                        $('.detail_criteria').show();
                    }
                });
                
            });
            
        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style type="text/css">

        </style>
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
                    Customer & Agent Bulletins
                </div>
                <div class="actions">
                    <input type="button" class="btn btn-primary pull-right" value="Add Bulletin" title="Add Bulletins" name='btnBulletins'  data-toggle="modal" data-target="#myModalBulletins">
                </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Action</th>
                                <th>Heading</th>
                                <th>Created Date</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                    </div>

                                </td>
                                <td width="650px">
                                    <input type="text" class="form-control form-filter input-xs" name="heading" id ="heading" />
                                </td>
                                <td width="150px">
                                    <input type="text" class="form-control form-filter input-xs date-picker" name="created_date" id ="created_date" />
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>	
            </div>
        </div>	
        <div class="modal fade" id="myModalBulletins" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title bulletin_new">Add New Bulletins</h4>
                    </div>

                    <form action="javasceript:{};" name="bullet_form" id="bullet_form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Heading</label>
                                        <input type="text" name="bulletin_heading" id="bulletin_heading" placeholder="Please enter Heading" value = "<?= $this->form_vars['bulletin_heading']; ?>" class="form-control" required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Date</label>
                                        <div class="date-picker input-daterange" data-date="20-01-2018" data-date-format="mm-dd-yyyy">
                                            <input type="text" class="form-control" name="date_created" id="date_created" value="<?= (!empty($_POST['date_created']) ? $_POST['date_created'] : ''); ?>" style="text-align:left;" required="required">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Description</label>
                                        <textarea data-provide="markdown" class="form-control"  rows="7" cols="150" name="bulletin_description" placeholder="<?php echo "Description"; ?>" id="bulletin_description" rel="tooltip" data-original-title="<?php echo "Description" ?>" required="required"><?= $this->form_vars["bulletin_description"]; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="bulletin_id" id="bulletin_id" value="">
                            <button type="submit" id = "add_bulletin" class="btn btn-info btn_save">Save</button>
                            <button type="button"  class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>  

        <div class="modal fade in" id="myModalSendEmail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">Send Bulletin</h4>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                  <div class="msg"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Send To</label>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group icheck">
                                    <label class="label-control">
                                        <input checked="" type="radio" id="sendto" name="sendto" value="customer" class="icheck-list">Customer
                                    </label>
                                    <label class="label-control">
                                        <input type="radio" id="sendto" name="sendto" value="agent" class="icheck-list">Agent
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">HAWB Selection</label>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group icheck">
                                    <label class="label-control">
                                        <input checked="" type="radio" id="selective_4" name="selective" value="send_to_all" class="icheck-list">ALL
                                    </label>
                                    <label class="label-control">
                                        <input checked="" type="radio" id="selective" name="selective" value="country" class="icheck-list">Destination Country
                                    </label>
                                    <label class="label-control">
                                        <input type="radio" id="selective_1" name="selective" value="mawb" class="icheck-list">MAWB
                                    </label> 
                                    <br>
                                    <label class="label-control">
                                        <input type="radio" id="selective_2" name="selective" value="city" class="icheck-list">Destination City
                                    </label>

                                    <label class="label-control">
                                        <input type="radio" id="selective_3" name="selective" value="manifestno" class="icheck-list">Manifest No.
                                    </label>                                                    
                                    <br>
                                    <input type="text" name="detail_criteria" id="detail_criteria" value="" class="form-control detail_criteria">
                                    <br/>
                                    <?php
                                    echo Ddl::generateCountryDDL('detail_criteria', $country, 'iso', 'class="bs-select form-control" data-live-search="true" data-container="body" data-size="8"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Date Range</label>
                                </div>
                            </div>
<!--                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">From Date</label>
                                    <input class="form-control" name="from_date" data-date-format="yyyy-mm-dd" id="from_date" type="text" placeholder="From Date" value="">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label font-green-soft"><strong>To Date</strong></label>
                                    <input class="form-control" name="to_date" data-date-format="yyyy-mm-dd" id="to_date" type="text" placeholder="To Date" value="">
                                </div>
                            </div>-->
                             <div class="col-md-9">
                                
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from_date" value="<?= (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to_date" value="<?= (!empty($_POST['to_date']) ? $_POST['to_date'] : ''); ?>" data-original-title="" title=""> 
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="send_bulletin" class="btn btn-primary">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
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
