<?php
// get settings
require_once("../includes/settings/config.inc.php");
class Page extends BasePage {
    /* * *
     * Controller logic
     */
    
    private $filerColumn = '*';
    private $user = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Pending Transactions"
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "pending_transaction") {
            $paymentshistoryfilterObj = new PaymentsHistoryFilter();
            $paymentshistoryfilterObj->addFieldNotFilter('   ph.payment_status', "completed"); 
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $date = $this->form_vars['date_added'];
                if (!empty($date))
                    $paymentshistoryfilterObj->addDateFilter('   ph.date_added', $date);
                
                $payment_method = $this->form_vars['payment_method'];
                if (!empty($payment_method))
                    $paymentshistoryfilterObj->addFieldFilter('   ph.payment_method', $payment_method);
                
                $payment_details = $this->form_vars['payment_detail'];
                if (!empty($payment_details))
                    $paymentshistoryfilterObj->addFieldLikeFilter('   ph.payment_detail', $payment_details);
                
                $amount = $this->form_vars['amount'];
                if (!empty($amount))
                    $paymentshistoryfilterObj->addFieldFilter('   ph.amount', $amount);
                 
                $credit = $this->form_vars['credit'];
                if (!empty($credit))
                    $paymentshistoryfilterObj->addFieldFilter('   ph.credit', $credit);
                
                $debit = $this->form_vars['debit'];
                if (!empty($debit))
                    $paymentshistoryfilterObj->addFieldFilter('   ph.debit', $debit);

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
                $paymentshistoryfilterObj->AddOrderBy(strtolower("ph." . $dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $paymentshistoryfilterObj->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $paymentshistoryfilterObj->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $paymentshistoryfilterObj->setOffset($iDisplayStart);
            $paymentshistoryfilterObjs = $paymentshistoryfilterObj->getPagingList($this->filerColumn);
            $setDataArr = array();

            foreach ($paymentshistoryfilterObjs as $obj) {
                $customer = new CustomerAccount($obj->getUserId());
                $currentArr = array();
                $currentArr['customer'] = $customer->getFullName();
                $currentArr['date_added'] = date("d F Y", $obj->getDateAdded());
                $currentArr['payment_method'] = $obj->getPaymentMethod();
                $currentArr['payment_detail'] = $obj->getPaymentDetail();
                $currentArr['credit'] = $obj->getCredit();
                $currentArr['debit'] = $obj->getDebit();
                $currentArr['amount'] = $obj->getAmount();
                $currentArr['status'] = $obj->getPaymentStatus();
                if($obj->getPaymentMethod() != "paypal") {
                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                $currentArr['actions'] .=        '<li>
                                                    <a title="Approve" href="javascript:;" onclick="approve_payment(' . $obj->getId() . ')">
                                                        <span class="glyphicon glyphicon-pencil"></span> Approve
                                                    </a>
                                                </li>';
                $currentArr['actions'] .= '</ul>
                                        </div>';
                } else {
                    $currentArr['actions'] .= '';
                }
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "approve_payment_action") {
            $paymenthistoryObj = new PaymentsHistory($this->form_vars['transaction_id']);
            $paymenthistoryObj->setPaymentStatus("completed");
            $paymenthistoryObj->setIsCompleted("yes");
            $paymenthistoryObj->save();
			/* update account balance */
			CustomerAccount::updateBalance($paymenthistoryObj->getAccountId());
			/******************************/
            $this->msg = 'Transaction has been approved successfully.';
            $this->flashMsg->success($this->msg);
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
                    var datatableurl = "pending_transactions.php?action=pending_transaction";                    
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
                                {"data": "actions", "bSortable": false},
                                {"data": "customer"},
                                {"data": "date_added"},
                                {"data": "payment_method"},
                                {"data": "payment_detail"},
                                {"data": "credit"},
                                {"data": "debit"},
                                {"data": "amount"},
                                {"data": "status"}
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
            function approve_payment(id) {
                $('#transaction_id').val(id);
                $('#transaction_payment_approve_form').submit();
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
                    Pending Transactions
                </div>
                <div class="actions">
<!--                    <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php //echo Translation::GetCaption("DOWNLOAD_CSV"); ?></a>-->
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                </div>
                <div class="row display-none" id="res_message">
                    <div class="col-md-12">
                        <div class="alert alert-success"></div>
                    </div>
                </div>
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Payment Details</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td>

                            </td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control input-sm form-filter" readonly name="date_added" id="date_added" placeholder="" >
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php
                                if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                                    $payment_method_array = array("cash" => "Cash", "paypal" => "Paypal", "bank_transfer" => "Bank Transfer", "check" => "By Check");
                                } else {
                                    $payment_method_array = array("paypal" => "Paypal", "bank_transfer" => "Bank Transfer", "check" => "By Check");
                                }
                                echo Ddl::generateArrayDDL('payment_method', $payment_method_array, "", "Select Method", ' class="form-control select2 form-filter" ', '', '', 'Select Payment Method', '');
                                ?>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="payment_detail" id="payment_detail" placeholder="" >
                            </td>                            
                            <td>
                                <input type="text" class="form-control form-filter" name="credit" id="credit" placeholder="" >
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="debit" id="payment_method" placeholder="" >
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter" name="amount" id="amount" placeholder="" >
                            </td>
                             <td>
                                <?php
                                    $status_array = array("completed" => "Completed", "not_completed" => "Not Complete");
                                    echo Ddl::generateArrayDDL('status', $status_array, "", "Select status", ' class="form-control select2 form-filter" ', '', 'status', 'Select status', '');
                                ?>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">
            <form method="post" action="pending_transactions.php" id="transaction_payment_approve_form">
                <input type="hidden" name="transaction_id" id="transaction_id" >
                <input type="hidden" name="action" id="action" value="approve_payment_action" >
            </form>
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
