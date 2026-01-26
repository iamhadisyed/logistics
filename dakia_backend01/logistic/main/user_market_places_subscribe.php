<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'usermarketplacessubscribe.class',
    'usermarketplacessubscribefilter.class',
    'usermarketplacesmapping.class',
    'usermarketplacesmappingfilter.class',
    'useraccount.class',
    'marketplaces.class',
    'marketplaceslog.class',
]);

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $user = null;
    private $user_filter;
    private $account_id;
    private $account_id_encoded;
    public $pending_req = 0;


    protected function init() {
        $this->user = SessionManager::getUser();
        if (isset($this->account_id) && $this->account_id > 0) {
            $this->account = $this->account_id;
        }
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("Subscribers")
        );
        $this->pending_req = UserMarketPlacesMappingFilter::pendingRequests($this->user->getUserAccountId());
        /*
         * DataTable handlings
         */
        //Handle subscriber Ajax
        if (isset($_GET['getsubscriberAjax']) && $_GET['getsubscriberAjax'] == 'subscribers_ajax') {
            /*
             * Set columns orders for sorting
             */
            $userMarketPlacesMappingFilter = New UserMarketPlacesMappingFilter();
            $userMarketPlacesMappingFilter->addFieldFilter("    user_parent_id",$this->user->getUserAccountId());

              if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                    $dataTableColumnId = $this->form_vars['order'][0]['column'];
                    $orderBy = $this->form_vars['order'][0]['dir'];
                    $orderFalse = TRUE;
                    if ($orderBy == 'desc') {
                        $orderFalse = FALSE;
                    }
                    $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                    if ($dataTableColumnName == "market_place")
                        $dataTableColumnName = "title";
                        $userMarketPlacesMappingFilter->AddOrderBy($dataTableColumnName, $orderFalse);
                }
                /*
                 * Column filter
                 * For search
                 */
                if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $searchAccount = $this->form_vars['search_UserAccount'];
                if (!empty($searchAccount))
                    $userMarketPlacesMappingFilter->addFieldLikeFilter('user_account', $searchAccount);

                $searchMarketPlace = $this->form_vars['search_Title'];
                if (!empty($searchMarketPlace))
                    $userMarketPlacesMappingFilter->addFieldLikeFilter('Title', $searchMarketPlace);

                $search_requestdate = $this->form_vars['search_requestdate'];
                if (!empty($search_requestdate))
                    $userMarketPlacesMappingFilter->addFieldFilter(" DATE_FORMAT(umps.date_time, '%d-%m-%Y') ", date('d-m-Y', strtotime($search_requestdate)));

                $search_activedate = $this->form_vars['search_activedate'];
                if (!empty($search_activedate))
                    $userMarketPlacesMappingFilter->addFieldFilter(" DATE_FORMAT(umps.active_date, '%d-%m-%Y') ", date('d-m-Y', strtotime($search_activedate)));

                $search_expirydate = $this->form_vars['search_expirydate'];
                if (!empty($search_expirydate))
                    $userMarketPlacesMappingFilter->addFieldFilter(" DATE_FORMAT(umps.expiry_date, '%d-%m-%Y') ", date('d-m-Y', strtotime($search_expirydate)));
                $searchMarketPlace = $this->form_vars['search_status'];
                if (!empty($searchMarketPlace))
                    $userMarketPlacesMappingFilter->addFieldLikeFilter('status', $searchMarketPlace);
            }
                /*
                 * Pagination Logic Implemented
                 *
                 */
            $iTotalRecords = $userMarketPlacesMappingFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $userMarketPlacesMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $userMarketPlacesMappingFilter->setOffset($iDisplayStart);
            $subscriberObjs = $userMarketPlacesMappingFilter->getPagingList();
            $subscriberDataArr = array();
            foreach ($subscriberObjs as $subscriberObj) {
                $subscriberArr = array();

                $subscriberArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
            <ul class="dropdown-menu" >';
            //active
            if($subscriberObj->getStatus() == "pending"){
                $subscriberArr['actions'] .= '<li>
                    <a href="?id='.$subscriberObj->getId().'&status='.$subscriberObj->getStatus().'" class="a_update" id="'.$subscriberObj->getId().'" data-log_key="' . $subscriberObj->getId() . '" mkt_place_id="' . $subscriberObj->getMarketPlacesId() . '"  mkt_place_mapping_id="' . $subscriberObj->getMarketPlacesId() . '"  user_account_id="' . $subscriberObj->getUserAccountId() . '" status_id="' . $subscriberObj->getStatus() . '" data-log_name="subscriber"> <i class="glyphicon glyphicon-stats"></i> Accept </a> </li>';
            }
            //inactive
            if($subscriberObj->getStatus() == "approved" && $subscriberObj->getActive() != "0"){
                $subscriberArr['actions'] .= '<li>
                <a href="?id='.$subscriberObj->getId().'&status='.$subscriberObj->getStatus().'"  
                class="a_inactive" 
                id="'.$subscriberObj->getId().'" 
                data-log_key="' . $subscriberObj->getId() . '" 
                mkt_place_id="' . $subscriberObj->getMarketPlacesId() . '" 
                mkt_place_mapping_id="' . $subscriberObj->getMarketPlacesId() . '"
                user_account_id="' . $subscriberObj->getUserAccountId() . '"
                status_id="' . $subscriberObj->getStatus() . '"
                data-log_name="subscriber"> <i class="glyphicon glyphicon-stats"></i> Inactive
                                     </a>
                                     </li>';
            }
                if($subscriberObj->getActive() == "0"){
                    $subscriberArr['actions'] .= '<li>
                        <a href="?id='.$subscriberObj->getId().'&status='.$subscriberObj->getStatus().'"  
                        class="a_active" 
                        id="'.$subscriberObj->getId().'" 
                        data-log_key="' . $subscriberObj->getId() . '" 
                        mkt_place_id="' . $subscriberObj->getMarketPlacesId() . '" 
                        mkt_place_mapping_id="' . $subscriberObj->getMarketPlacesId() . '"
                        user_account_id="' . $subscriberObj->getUserAccountId() . '"
                        status_id="' . $subscriberObj->getStatus() . '"
                        data-log_name="subscriber"> <i class="glyphicon glyphicon-ok"></i> Active
                                             </a>
                                             </li>';
                }
            //forward
//            if($check_authrozie > 0 && $subscriberObj->getStatus() == 2 && $subscriberObj->getPendingReq() == 2){
//            $subscriberArr['actions'] .= '<li>
//            <a href="?id='.$subscriberObj->getId().'&status='.$subscriberObj->getStatus().'"
//            class="a_forward" 
//            id="'.$subscriberObj->getId().'" 
//            data-log_key="' . $subscriberObj->getId() . '"
//            parent_id="' . $subscriberObj->getParentId() . '"  
//            mkt_place_id="' . $subscriberObj->getMarketPlacesId() . '" 
//            mkt_place_mapping_id="' . $subscriberObj->getMarketPlacesId() . '"
//            user_account_id="' . $subscriberObj->getUserAccountId() . '" 
//            status_id="' . $subscriberObj->getStatus() . '"
//            data-log_name="subscriber"> <i class="glyphicon glyphicon-stats"></i> Forward
//                                 </a>
//                                 </li>';
//            }

           $subscriberArr['actions'] .= '<li>
           <a href="?id='.$subscriberObj->getId().'&reject_row=" 
           class="a_reject" 
           id=" '.$subscriberObj->getId().'" 
           mkt_place_id="' . $subscriberObj->getMarketPlacesId() . '" 
           mkt_place_mapping_id="' . $subscriberObj->getMarketPlacesId() . '"
           user_account_id="' . $subscriberObj->getUserAccountId() . '"
           data-log_name="Reject"> <i class="glyphicon glyphicon-remove"></i> Reject
                         </a></li>';

           $subscriberArr['actions'] .= '<li>
           <a href="?id='.$subscriberObj->getId().'&delete_row=" 
           class="a_trash" 
           id=" '.$subscriberObj->getId().'" 
           mkt_place_id="' . $subscriberObj->getMarketPlacesId() . '" 
           mkt_place_mapping_id="' . $subscriberObj->getMarketPlacesId() . '"
           user_account_id="' . $subscriberObj->getUserAccountId() . '"
           data-log_name="delete"> <i class="glyphicon glyphicon-trash"></i> Delete
                         </a></li>';
           $userAccount = New CustomerAccount($subscriberObj->getUserAccountId());
           $subscriberArr['user_account'] = $userAccount->getUserAccount();
           $userMarketPlaces = New MarketPlaces($subscriberObj->getMarketPlacesId());
           $subscriberArr['market_place'] = $userMarketPlaces->getTitle();
           $subscriberArr['added_date'] = date("d-m-Y" , $subscriberObj->getDateCreated());
               if(!empty($subscriberObj->getActiveDate())){
                    $ActiveDate = date("d-m-Y" , $subscriberObj->getActiveDate());
                }else{
                    $ActiveDate = "N / A";
                }
                $subscriberArr['active_date'] = '<ActiveDatePicker class="cls_activedate" contenteditable="true"  id="'. $subscriberObj->getId() .'">'. $ActiveDate .'</ActiveDatePicker>';
/*                '<input type="text" class="cls_activedate" contenteditable="false" id="'. $subscriberObj->getId() .'" value="' .$ActiveDate . '" style="background: rgba(0, 0, 0, 0);
    border: none; outline: none;" placeholder="dd-mm-yyyy">';*/
            if(!empty($subscriberObj->getExpiryDate())){
                    $ExpiryDate = date("d-m-Y" , $subscriberObj->getExpiryDate());
                }else{
                    $ExpiryDate = "N / A";
                }
            $subscriberArr['expiry_date'] =
            '<ExpiryDatePicker class="cls_expirydate" contenteditable="true" id="'.     $subscriberObj->getId() .'">'. $ExpiryDate . '</ExpiryDatePicker>';
                if($subscriberObj->getStatus() == "approved"){
                     $status = '<span class="label label-sm label-success">Active</span>';
                }elseif($subscriberObj->getStatus() == "pending"){
                    $status = '<span class="label label-sm label-primary">Pending</span>';
                }elseif($subscriberObj->getStatus() == "rejected"){
                    $status = '<span class="label label-sm label-danger">Rejected</span>';
                }else{
                    $status = '<span class="label label-sm label-warning">Expired</span>';
                }
                if($subscriberObj->getActive() == "0"){
                    $status = '<span class="label label-sm label-warning">Inactive</span>';
                }
            $subscriberArr['status'] = '<div class="text-center">' . $status . '</div>';
            $subscriberDataArr [] = $subscriberArr;
            }

            $subscriberDataArrJson['data'] = $subscriberDataArr;
            $subscriberDataArrJson['draw'] = $sEcho;
            $subscriberDataArrJson['recordsTotal'] = $iTotalRecords;
            $subscriberDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($subscriberDataArrJson);
            die;
        }

            if(isset($this->form_vars['action']) && $this->form_vars['action'] == "approve_account"){
            /*
             * Update account for approve
             */
                $expiryDate = strtotime(date('Y-m-d H:i:s', strtotime('+5 years')));
                $activeDate = strtotime(date('Y-m-d H:i:s'));
                if($this->form_vars['market_places_mapping_id'] > 0){
                    $lastStatus = 'rq';
                    $userMarketPlacesMapping = new UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                    $lastStatus = (!empty($userMarketPlacesMapping->getStatus()) ? $userMarketPlacesMapping->getStatus() : "rq");
                    $statusArr = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','expired'=>'expired','rq'=>'requested'];
                    $userMarketPlacesMapping = New UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                    $userMarketPlacesMapping->setActiveDate($activeDate);
                    $userMarketPlacesMapping->setExpiryDate($expiryDate);
                    $userMarketPlacesMapping->setStatus("approved");
                    $userMarketPlacesMapping->save();
                    /*
                     * save log
                     */
                    $marketPlacesLog = New MarketPlacesLog();
                    $marketPlacesLog->setMarketPlaceId($this->form_vars['market_places_mapping_id']);
                    $marketPlacesLog->setUserAccountId($this->user->getUserAccountId());
                    $marketPlacesLog->setLastStatus($statusArr[$lastStatus]);
                    $marketPlacesLog->setCurrentStatus("approved");
                    $marketPlacesLog->setDateRequest(time());
                    $marketPlacesLog->save();
                    die;
                }
            }
            if(isset($this->form_vars['action']) && $this->form_vars['action'] == "reject_account"){
            /*
             * Update account for approve
             */
            $expiryDate = strtotime(date('Y-m-d H:i:s', strtotime('+5 years')));
            $activeDate = strtotime(date('Y-m-d H:i:s'));
            $rejectReason = $this->form_vars['reject_reason'];
            if($this->form_vars['market_places_mapping_id'] > 0){
                $lastStatus = 'rq';
                $userMarketPlacesMapping = new UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                $lastStatus = (!empty($userMarketPlacesMapping->getStatus()) ? $userMarketPlacesMapping->getStatus() : "rq");
                $statusArr = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','expired'=>'expired','rq'=>'requested'];
                $userMarketPlacesMapping = New UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                $userMarketPlacesMapping->setStatus("rejected");
                $userMarketPlacesMapping->setReason($rejectReason);
                $userMarketPlacesMapping->save();
                /*
                 * save log
                 */
                $marketPlacesLog = New MarketPlacesLog();
                $marketPlacesLog->setMarketPlaceId($this->form_vars['market_places_mapping_id']);
                $marketPlacesLog->setUserAccountId($this->user->getUserAccountId());
                $marketPlacesLog->setLastStatus($statusArr[$lastStatus]);
                $marketPlacesLog->setCurrentStatus("rejected");
                $marketPlacesLog->setDateRequest(time());
                $marketPlacesLog->save();
                die;
            }
        }
            if(isset($this->form_vars['action']) && $this->form_vars['action'] =="inactive_account"){
            /*
             * Update account for inactive
             */
            $expiryDate = strtotime(date('Y-m-d H:i:s', strtotime('+5 years')));
            $activeDate = strtotime(date('Y-m-d H:i:s'));
            if($this->form_vars['market_places_mapping_id'] > 0){
                $lastStatus = 'rq';
                $userMarketPlacesMapping = new UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                $lastStatus = (!empty($userMarketPlacesMapping->getStatus()) ? $userMarketPlacesMapping->getStatus() : "rq");
                $statusArr = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','expired'=>'expired','rq'=>'requested'];
                $userMarketPlacesMapping = New UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                $userMarketPlacesMapping->setActive("0");
                $userMarketPlacesMapping->save();
                /*
                 * save log
                 */
                $marketPlacesLog = New MarketPlacesLog();
                $marketPlacesLog->setMarketPlaceId($this->form_vars['market_places_mapping_id']);
                $marketPlacesLog->setUserAccountId($this->user->getUserAccountId());
                $marketPlacesLog->setLastStatus($statusArr[$lastStatus]);
                $marketPlacesLog->setCurrentStatus("inactive");
                $marketPlacesLog->setDateRequest(time());
                $marketPlacesLog->save();
                die;
            }
        }
            if(isset($this->form_vars['action']) && $this->form_vars['action'] =="active_account"){
            /*
             * Update account for active
             */
            $expiryDate = strtotime(date('Y-m-d H:i:s', strtotime('+5 years')));
            $activeDate = strtotime(date('Y-m-d H:i:s'));
            if($this->form_vars['market_places_mapping_id'] > 0){
                $lastStatus = 'rq';
                $userMarketPlacesMapping = new UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                $lastStatus = (!empty($userMarketPlacesMapping->getStatus()) ? $userMarketPlacesMapping->getStatus() : "rq");
                $statusArr = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','expired'=>'expired','rq'=>'requested'];
                $userMarketPlacesMapping = New UserMarketPlacesMapping($this->form_vars['market_places_mapping_id']);
                $userMarketPlacesMapping->setActive("1");
                $userMarketPlacesMapping->save();
                /*
                 * save log
                 */
                $marketPlacesLog = New MarketPlacesLog();
                $marketPlacesLog->setMarketPlaceId($this->form_vars['market_places_mapping_id']);
                $marketPlacesLog->setUserAccountId($this->user->getUserAccountId());
                $marketPlacesLog->setLastStatus("inactive");
                $marketPlacesLog->setCurrentStatus("active");
                $marketPlacesLog->setDateRequest(time());
                $marketPlacesLog->save();
                die;
            }
        }

            if(isset($_POST['rowid'] , $_POST['is_delete'], $_POST['market_place_id'],
                     $_POST['user_account_id'], $_POST['market_place_mapping_id'])){
                $s_status->is_detele($_POST['market_place_id'], $_POST['market_place_mapping_id']);
            }

            if(isset($_POST['rowid'] , $_POST['is_reject'], $_POST['market_place_id'],
                $_POST['user_account_id'], $_POST['market_place_mapping_id'])){
                $s_status->is_reject($_POST['market_place_id'], $_POST['market_place_mapping_id']);
            }

            if(isset($_POST['ActiveDatePicker_Id'], $_POST['ActiveDate'])){
                $ActiveDate = date('Y-m-d', strtotime($_POST['ActiveDate']));
                $s_status->set_ActiveDate($_POST['ActiveDatePicker_Id'], $ActiveDate);
            }

            if(isset($_POST['ExpiryDatePicker_Id'], $_POST['ExpiryDate'])){
                $ExpiryDate = date('Y-m-d', strtotime($_POST['ExpiryDate']));
                $s_status->set_ExpiryDate($_POST['ExpiryDatePicker_Id'], $ExpiryDate);
            }

            if(isset($_POST['ExpiryDatePicker_Id'], $_POST['ExpiryDate'])){
                $ExpiryDate = date('Y-m-d', strtotime($_POST['ExpiryDate']));
                $s_status->set_ExpiryDate($_POST['ExpiryDatePicker_Id'], $ExpiryDate);
            }

//            if(isset($_POST['rowid'], $_POST['parent_id'], $_POST['user_account_id'], $_POST['mkt_place_id'],                                                   $_POST['mkt_place_mapping_id'])){
//                $s_status->request_forward($_POST['rowid'], $_POST['parent_id'], $_POST['user_account_id'],$_POST['mkt_place_id'],                                                    $_POST['mkt_place_mapping_id']);
//            }
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
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet"  type="text/css" />
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>

        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script>
            $(document).on('focus', 'ActiveDatePicker', function (e) {
                if($(this).hasClass('cls_activedate')) {
                    $(this).addClass("width=200");
                    $(this).focus(function(){
                        $(this).css("background-color", "#e6f2ff");

                    });
                    $(this).blur(function(){
                        $(this).css("background-color", "#ffffff");
                    });

                    var ActiveDatePicker_Id = $(this).attr('id');
                    if ($(this).length > 0) {
                        //init date pickers
                        var today = new Date();
                        $(".cls_activedate").datepicker({
                            format: 'dd-mm-yyyy',
                            autoclose:true,
                            minDate: 0,
                        }).on('changeDate', function (ev) {
                            var dateTime = new Date($(this).datepicker("getDate"));
                            var strDateTime = dateTime.getDate() + "-" + (dateTime.getMonth() + 1) + "-" +
                                dateTime.getFullYear();
                            $(this).html(strDateTime);
                            $(this).datepicker('hide');
                            $.ajax({
                                type: "POST",
                                data: {ActiveDatePicker_Id: ActiveDatePicker_Id, ActiveDate:strDateTime},
                                success: function (success) {
                                    $("#res_message").html("<?php echo Translation::GetCaption
                                    ("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                    $("#res_message").addClass("alert alert-success");
                                    $("#res_message").show();
                                    $(".scroll-to-top").click();
                                    grid.getDataTable().ajax.reload();
                                }
                            });
                        });
                    }
                }
            })
            $(document).on('focus', 'ExpiryDatePicker', function (e) {
                if($(this).hasClass('cls_expirydate')) {
                    var ExpiryDatePicker_Id = $(this).attr('id');
                    if ($(this).length > 0) {
                        //init date pickers
                        var today = new Date();
                        $(this).datepicker({
                            format: 'dd-mm-yyyy',
                            autoclose:true,
                            minDate: 0,
                        }).on('changeDate', function (ev) {
                            var dateTime = new Date($(this).datepicker("getDate"));
                            var strDateTime = dateTime.getDate() + "-" + (dateTime.getMonth() + 1) + "-" +
                                dateTime.getFullYear();
                            $(this).html(strDateTime);
                            $(this).datepicker('hide');
                            $.ajax({
                                type: "POST",
                                data: {ExpiryDatePicker_Id: ExpiryDatePicker_Id, ExpiryDate:strDateTime},
                                success: function (success) {
                                    $("#res_message").html("<?php echo Translation::GetCaption
                                    ("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                    $("#res_message").addClass("alert alert-success");
                                    $("#res_message").show();
                                    $(".scroll-to-top").click();
                                    grid.getDataTable().ajax.reload();
//                                    $("#res_message").fadeOut(5000);
                                }
                            });
                        });
                    }
                }
            })
        </script>

        <script type="text/javascript">
            var account = '';
        <?php if (trim($this->account_id_encoded)!='') { ?>
                account = '<?php echo $this->account_id_encoded; ?>';
        <?php } ?>
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
                        "url": "user_market_places_subscribe.php?getsubscriberAjax=subscribers_ajax", // ajax source
                        headers: {},
                        },
                        "bStateSave": true,
                        "columns": [
                        {"data": "actions"},
                        {"data": "user_account"},
                        {"data": "market_place"},
                        {"data": "added_date"},
                        {"data": "active_date"},
                        {"data": "expiry_date"},
                        {"data": "status"},
                        //{"data": "userServiceType", "bSortable": true}
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
                    var today = new Date();
                    $('.date-picker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose:true
                        // endDate: "today",
                        // maxDate: today
                    }).on('changeDate', function (ev) {
                        $(this).datepicker('hide');
                    });
                    $('.date-picker').keyup(function () {
                        if (this.value.match(/[^0-9]/g)) {
                            this.value = this.value.replace(/[^0-9^-]/g, '');
                        }
                    });
                }

        <?php if ($this->account_id > 0) { ?>
                $(".filter-submit").click();
        <?php } ?>
            });
                $(document).on('click', 'a', function (e) {
                    if($(this).hasClass('a_trash')){
                    e.preventDefault();
                    var market_place_id = $(this).attr('mkt_place_id');
                    var market_place_mapping_id = $(this).attr('mkt_place_mapping_id');
                    var user_account_id = $(this).attr('user_account_id');
                    var is_delete = $(this).attr('is_delete');
                    var rowid = $(this).attr('id');

                swal({
                title: "Are you sure you want to delete this record?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                },
                        function(isConfirm) {
                        if (isConfirm) {
                        $.ajax({
                        type: "POST",
                                url: "user_market_places_subscribe.php",
                                data: {market_place_id: market_place_id, user_account_id:
                                   user_account_id, rowid:rowid, is_delete:is_delete,
                                   market_place_mapping_id:market_place_mapping_id},
                                success: function (data) {
                                $("#res_message").addClass("alert alert-danger");
                                $("#res_message").html("<?php echo Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY") ?>");
                                $("#res_message").show();
                                $(".scroll-to-top").click();
                                grid.getDataTable().ajax.reload();
//                                    $("#res_message").fadeOut(5000);
                                },
                                error: function () {
                                alert('error handing here');
                                }
                        });
                        }else{}
                        });
                    }
                });
                $(document).on('click', 'a', function (e) {
                    if($(this).hasClass('a_update')){
                    e.preventDefault();
                    var market_places_mapping_id = $(this).attr('id');

                swal({
                title: "Are you sure you want to approve this record?",
                        text: "",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                },
                        function(isConfirm) {
                        if (isConfirm) {
                        $.ajax({
                        type: "POST",
                        data: {action:"approve_account",market_places_mapping_id: market_places_mapping_id},
                                success: function (response) {
                                $("#res_message").html("<?php echo Translation::GetCaption
                                ("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                $("#res_message").removeClass("alert alert-danger");
                                $("#res_message").addClass("alert alert-success");
                                $("#res_message").show();
                                $(".scroll-to-top").click();
                                grid.getDataTable().ajax.reload();
                                //$("#res_message").fadeOut(5000);
                                },
                                error: function () {
                                alert('error handing here');
                                }
                        });
                        }
                        });
                    }
                });
                $(document).on('click', 'a', function (e) {
                if($(this).hasClass('a_inactive')){
                    e.preventDefault();
                    var market_places_mapping_id = $(this).attr('id');

                    swal({
                            title: "Are you sure you want to inactive this record?",
                            text: "",
                            type: "info",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    type: "POST",
                                    data: {action:"inactive_account",market_places_mapping_id: market_places_mapping_id},
                                    success: function (response) {
                                        $("#res_message").html("<?php echo Translation::GetCaption
                                        ("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                        $("#res_message").removeClass("alert alert-danger");
                                        $("#res_message").addClass("alert alert-success");
                                        $("#res_message").show();
                                        $(".scroll-to-top").click();
                                        grid.getDataTable().ajax.reload();
                                        //$("#res_message").fadeOut(5000);
                                    },
                                    error: function () {
                                        alert('error handing here');
                                    }
                                });
                            }
                        });
                }
            });
                $(document).on('click', 'a', function (e) {
                if($(this).hasClass('a_active')){
                    e.preventDefault();
                    var market_places_mapping_id = $(this).attr('id');

                    swal({
                            title: "Are you sure you want to active this record?",
                            text: "",
                            type: "info",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    type: "POST",
                                    data: {action:"active_account",market_places_mapping_id: market_places_mapping_id},
                                    success: function (response) {
                                        $("#res_message").html("<?php echo Translation::GetCaption
                                        ("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                        $("#res_message").removeClass("alert alert-danger");
                                        $("#res_message").addClass("alert alert-success");
                                        $("#res_message").show();
                                        $(".scroll-to-top").click();
                                        grid.getDataTable().ajax.reload();
                                        //$("#res_message").fadeOut(5000);
                                    },
                                    error: function () {
                                        alert('error handing here');
                                    }
                                });
                            }
                        });
                }
            });
                $(document).on('click', 'a', function (e) {
                    if($(this).hasClass('a_reject')){
                e.preventDefault();
                var market_places_mapping_id = $(this).attr('id');

                swal({
                        title: "Are you sure you want to reject this record?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true,
                        type: "input",
                        inputPlaceholder: "Reason for reject this record"
                    },
                    function(inputValue){
                        if (inputValue == "") {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html("Please add reason for reject this record");
                            $("#show_general_msg").show();
                            return false;
                        }else{
                            $.ajax({
                                type: "POST",
                                url: "user_market_places_subscribe.php",
                                data: {action:"reject_account",market_places_mapping_id: market_places_mapping_id,"reject_reason":inputValue},
                                success: function (data) {
                                    $("#res_message").addClass("alert alert-danger");
                                    $("#res_message").html("<?php echo Translation::GetCaption("RECORD REJECTED SUCCESSFULLY") ?>");
                                    $("#res_message").show();
                                    $(".scroll-to-top").click();
                                    grid.getDataTable().ajax.reload();
//                                    $("#res_message").fadeOut(5000);
                                },
                                error: function () {
                                    alert('error handing here');
                                }
                            });
                        }
//                    function(isConfirm) {
//                        if (isConfirm) {
//                            $.ajax({
//                                type: "POST",
//                                url: "user_market_places_subscribe.php",
//                                data: {action:"reject_account",market_places_mapping_id: market_places_mapping_id},
//                                success: function (data) {
//                                    $("#res_message").addClass("alert alert-danger");
//                                    $("#res_message").html("<?php //echo Translation::GetCaption("RECORD REJECTED SUCCESSFULLY") ?>//");
//                                    $("#res_message").show();
//                                    $(".scroll-to-top").click();
//                                    grid.getDataTable().ajax.reload();
////                                    $("#res_message").fadeOut(5000);
//                                },
//                                error: function () {
//                                    alert('error handing here');
//                                }
//                            });
//                        }else{}
                    });
                    }
                 });
                $(document).on('click', 'a', function (e) {
                    if($(this).hasClass('a_forward')){
                    e.preventDefault();
                    var rowid = $(this).attr('id');
                    var status_id = $(this).attr('status_id');
                    var mkt_place_mapping_id = $(this).attr('mkt_place_mapping_id');
                    var mkt_place_id = $(this).attr('mkt_place_id');
                    var user_account_id = $(this).attr('user_account_id');
                    var parent_id = $(this).attr('parent_id');

                    if(status_id == "4")
                    {
                        return false;
                    }

                    swal({
                            title: "Are you sure you want to forward this record ?",
                            text: "",
                            type: "info",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    type: "POST",
                                    data: {parent_id: parent_id, rowid:rowid, mkt_place_mapping_id:mkt_place_mapping_id,
                                            mkt_place_id:mkt_place_id, user_account_id:user_account_id},
                                    success: function (response) {
                                        $("#res_message").html("<?php echo Translation::GetCaption
                                        ("RECORD_UPDATED_SUCCESSFULLY") ?>");
                                        $("#res_message").removeClass("alert alert-danger");
                                        $("#res_message").addClass("alert alert-success");
                                        $("#res_message").show();
                                        $(".scroll-to-top").click();
                                        grid.getDataTable().ajax.reload();
                                        //$("#res_message").fadeOut(5000);
                                    },
                                    error: function () {
                                        alert('error handing here');
                                    }
                                });
                            }
                        });
                }
                });

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
                    <?php
                    if ( $this->account_id > 0) {
                        $userAccount = new CustomerAccount($this->account_id);
                        echo $userAccount->getUserAccount();
                        //die;
                    }
                    ?>
                    Subscriber List
                    <?php
//                    $this->user_filter = new UserMarketPlacesSubscribeFilter();
//                    $this->pending_req = $this->user_filter->pendingRequests($this->user->getUserAccountId());
                    ?>
                    <p>Pending Requests &nbsp;
                       <span class="badge badge-primary"><?php echo $this->pending_req; ?></span>
                    </p>
                    <p></p>
                </div>
            </div>
            <div class="portlet-body">
                <div id="res_message"></div>
                <div class="row" style="display: none;" id="show_general_msg">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th width="6%">Actions</th>
                            <th>Subscriber</th>
                            <th>Market Place</th>
                            <th>Request Date</th>
                            <th>Active Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs btn-default blue btn-outline pull-left filter-submit"><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs btn-default red btn-outline pull-left filter-cancel"><i class="fa fa-times"></i></button>
                                </div>

                            </td>
                            <td><input type="text" id="search_UserAccount" class="form-control form-filter input-sm" name="search_UserAccount"></td>
                            <td><input type="text" id="search_Title" class="form-control form-filter input-sm" name="search_Title"></td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly placeholder="Date Created" data-date-format="dd-mm-yyyy" id="search_requestdate" name="search_requestdate">
                                    <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                </div>

                            </td>
                             <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly  placeholder="Date Created" data-date-format="dd-mm-yyyy" id="search_activedate" name="search_activedate">
                                    <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                </div>

                            </td>
                            <td>
                          <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="search_expirydate" placeholder="Date Created" data-date-format="dd-mm-yyyy" id="search_expirydate">
                                    <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                </div>

                            </td>
                            <td>
   <?php
             $arrayTypeValues = array('1' => 'Active', '2' => 'Pending', '3' => 'Reject', '4' => 'Forward');
                                echo Ddl::generateArrayDDL('search_status', $arrayTypeValues, "", "Select Status", ' class="form-control form-filter select2"', "", 'search_status', 'Select Status', '');
                                ?>
                            </td>

                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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

    public function renderHead() {
        ?>
        <style type="text/css">
            .table-responsive {
                overflow-x: visible !important;
                overflow-y: visible !important;
            }
        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
