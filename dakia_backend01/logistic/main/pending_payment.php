<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([  
	'paymentshistory.class',
	'paymentshistoryfilter.class',
	'consignmentcharges.class',
	'consignmentchargesfilter.class'
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $accountId = 0;
    private $checkChild = 0;
    private $childAccountArr = [];

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Pending Payment"
        );
        $this->user = SessionManager::getUser();
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $this->accountId = $_GET['id'];
        }
        
        $userAccountFilter = new UserAccountFilter();
        $userAccountFilter->addFieldFilter("        ua.parentid", $this->user->getUserAccountId());
        $userAccountFilterObjs = $userAccountFilter->getList();
        if(count($userAccountFilterObjs) > 0) {
            foreach ($userAccountFilterObjs as $userAccountFilterObj) {
                $this->childAccountArr[] = $userAccountFilterObj->getId();
            }
        }
        if (isset($this->accountId) && $this->accountId > 0) {
            if(in_array($this->accountId, $this->childAccountArr)) {
                $this->checkChild = 1;
            } else {
                $this->accountId = 0;
            }
        }
        if(isset($this->form_vars['action']) && $this->form_vars['action'] == "save_payment"){
            $paymentId = trim($this->form_vars['payment_id']);
            
            $paymentsHistory = new PaymentsHistory($paymentId);
            $paymentsHistory->setIsCompleted("yes");
            $paymentsHistory->setPaymentStatus("completed");
            $paymentsHistory->setDateUpdated(time());
            $paymentsHistory->setUpdatedBy($this->user->getId());
            $paymentsHistory->save();
			/* update account balance */
			CustomerAccount::updateBalance($paymentsHistory->getAccountId());
			/******************************/
            $invoiceId = $paymentsHistory->getInvoiceId();
            if($invoiceId != "") {
                $invoiceObj = new Invoices($invoiceId);
                $invoiceObj->setIsPaid(1);
                $invoiceObj->save();
            }
            
            $output["status"] = "success";
            $output["message"] = "Payment approved successfully";
            echo json_encode($output);
            die;
        }
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "payment_history_ajax") {
            // Get User account imididate child
            $userAccount = new UserAccountFilter();
            $paymentHistoryFilter = new PaymentsHistoryFilter();
            $paymentHistoryFilter->join("customer_account ua", ['ph.account_id' => 'ua.id']);
            $userAccountId = 0;
            if($this->accountId > 0) {
                $userAccountId = $this->accountId;
            } else {
                $userAccountId = $this->user->getUserAccountId();
            }
            $paymentHistoryFilter->whereIn('ua.id',$this->childAccountArr);
            $paymentHistoryFilter->where(["ph.is_completed" => 'no']);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $date_created_from = $this->form_vars['date_from'];
                $date_created_to = $this->form_vars['date_to'];
                if (!empty($date_created_from) && !empty($date_created_to)) {
                    $paymentHistoryFilter->whereBetween ('ph.date_added', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));
                }
                $payment_detail = $this->form_vars['payment_detail'];
                if (!empty($payment_detail)) {
                    $paymentHistoryFilter->where("(ph.payment_detail LIKE '%" . $payment_detail . "%')");
                }
                $accountId = $this->form_vars['account_id'];
                if (!empty($accountId)) {
                    $paymentHistoryFilter->where("(ph.account_id = '" . $accountId . "')");
                }
                
                $filterArray = [];
                $payment_method = $this->form_vars['payment_method'];
                if (!empty($payment_method)) {
                    $filterArray['ph.payment_method'] = $payment_method;
                }
                $amount = $this->form_vars['amount'];
                if (!empty($amount)) {
                    $filterArray['ph.amount'] = $amount;
                }
                $paymentHistoryFilter->where($filterArray);
                
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
                $paymentHistoryFilter->orderBy("ph.".$dataTableColumnName, $orderFalse);
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
            $paymentHistoryFilter->setRowsPerPage($iDisplayLength);
            $paymentHistoryFilter->setOffset($iDisplayStart);
            $paymentHistoryFilterObjs = $paymentHistoryFilter->getList("ph.*");
            $iTotalRecords = $paymentHistoryFilter->getCount();
            $setDataArr = array();
            foreach ($paymentHistoryFilterObjs as $paymentHistoryFilterObj) {
                if (!empty($paymentHistoryFilterObj->getDateAdded())) {
                    $date_created = formatDate(date("d-m-Y", $paymentHistoryFilterObj->getDateAdded()));
                } else {
                    $date_created = "";
                }
                // Check if method is bank transfer or through cheque then show billing id
                $billingId = "";
                if($paymentHistoryFilterObj->getPaymentMethod() == "bank_transfer" || $paymentHistoryFilterObj->getPaymentMethod() == "cheque"){
                    $billingId = $paymentHistoryFilterObj->getBillingId();
                }
                $currentArr = array();
                $currentArr['added_date'] = $date_created;
                $userAccount = new CustomerAccount($paymentHistoryFilterObj->getAccountId());
                $currentArr['account_id'] = $userAccount->getUserAccount();
                $currentArr['payment_method'] = ucwords(str_replace("_"," ",$paymentHistoryFilterObj->getPaymentMethod())).'<br />'.$billingId;
                $currentArr['payment_detail'] = $paymentHistoryFilterObj->getPaymentDetail();
                $currentArr['amount'] = $paymentHistoryFilterObj->getAmount();
                $currentArr['action'] = '<div class="btn-group btn-group-circle">
                                                                    <button type="button" onclick="approve_payment('.$paymentHistoryFilterObj->getId().')" class="btn btn-outline green btn-sm">Appove</button>
                                                                    <button type="button" class="btn btn-outline red btn-sm">Reject</button>
                                                                </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
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
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

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
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>


        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var accountId = <?php echo $this->accountId ?>;
                    var datatableurl = "pending_payment.php?action=payment_history_ajax";
                    if(accountId > 0) {
                        datatableurl = "pending_payment.php?action=payment_history_ajax&id="+accountId;
                    } else {
                        datatableurl = "pending_payment.php?action=payment_history_ajax";
                    }
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
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "added_date"},
                                {"data": "account_id"},
                                {"data": "payment_method"},
                                {"data": "payment_detail"},
                                {"data": "amount"},
                                {"data": "action", "bSortable": false}
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
            });
            function approve_payment(payment_id){
                swal({
                        title: "Are you sure you want to approve payment",
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
                            url: "pending_payment.php",
                            data: {action: "save_payment", payment_id: payment_id},
                            dataType: "json",
                            success: function (data) {
                                if (data.status == "success") {
                                    $("#show_general_msg div.alert").html(" ");
                                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                    $("#show_general_msg div.alert").html(data.message);
                                    $("#show_general_msg").show();
                                    grid.getDataTable().ajax.reload();
                                } else {
                                }
                            },
                            error: function () {
                                alert('error handing here');
                            }
                        });
                    }
                });
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
                    Pending Payment
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
                <div class="row" id="show_general_msg" style="display: none;">
                    <div class="col-md-12">
                            <div class="alert alert-danger"></div>
                    </div>
		</div>
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Date</th>
                            <th>Account</th>
                            <th>Method</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th width="140">Action</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td width="15%">
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php
                                    $accountParentId = $this->user->getUserAccountId();    
                                    echo Ddl::generateDDL('account_id', 'UserAccountFilter', ' active_flag = "1" AND parentid = '.$accountParentId, 'user_account', 'id', "", ' class="form-control  form-filter select2" data-toggle="tooltip" data-placement="top" title="Department" data-original-title="Department"', 'Please select', '', 'account_id', 'Accounts');
                                ?>
                            </td>
                            <td>
                                <?php
                                    $pay_by_array = array("" => "Please Select", "cash" => "Cash", "paypal" => "Paypal", "bank_transfer" => "Bank Transfer", "cheque" => "Cheque", "bonus" => "Bonus");
                                    echo Ddl::generateArrayDDL('payment_method', $pay_by_array, "", "", ' class="form-control form-filter input-xs select2" ', 'Select Payment Method', 'payment_method', 'Select Payment Method', '');
                                ?>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="payment_detail" id ="payment_detail" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="amount" id ="amount" />
                            </td>
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">

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

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
