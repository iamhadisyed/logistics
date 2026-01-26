<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
], '3rdparty/tcpdf');
include_classes([
    'ivisualcomponent', 'ddl.inc'
], 'library');
include_classes(['iaddress.class',
    'consignment.class',
    'consignmentfilter.class',]);

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    protected function init()
    {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "GP PDF Report"
        );
        $this->user = SessionManager::getUser();
        if (!empty($_POST['user_account_id'])) {
            $user_account_id = $_POST['user_account_id'];
        } else {
            $user_account_id = $this->user->getUserAccountId();
        }

        $userFilterSubObj = new UserAccountFilter();
        $userFilterSubObj->addFieldFilter('parentid', $user_account_id);
        $dataSubAccount = $userFilterSubObj->getList();

        if (isset($this->form_vars['submitFilter'])) {
            $consignmentFilter = new ConsignmentFilter();
            $accountReport = false;
            $searchFilter = true;

            ////////////////Status Filter/////////////
            $consignmentFilter->addFilter(" c.`shipment_status` NOT IN ('11', '12', '22', '23') ", 'filter');


            ////////////////Date Filter/////////////
            if (isset($this->form_vars['from_date']) && !empty($this->form_vars['from_date']) && isset($this->form_vars['to_date']) && !empty($this->form_vars['to_date'])) {

                $dateFrom = $this->form_vars['from_date'];
                $dateTo = $this->form_vars['to_date'];
                $dateFrom = date('Y-m-d 00:00:00', strtotime($dateFrom));
                $dateTo = date('Y-m-d 23:59:59', strtotime($dateTo));
                $dateWhere = " ((c.date_label_created >= '" . strtotime(DbAccess3::escape($dateFrom)) . "') AND (c.date_label_created <= '" . strtotime(DbAccess3::escape($dateTo)) . "'))";
                $consignmentFilter->addFilter($dateWhere, 'filter');

                ////////////////Account Filter/////////////
                $this->user = SessionManager::getUser();
                $loggedInUserAccount = $this->user->getUserAccountId();
                if ($this->form_vars['user_account_id'] != '') {
                    $userAccountId = $this->form_vars['user_account_id'];
                    $consignmentFilter->addGroupBy('c.service_id');
                    $accountReport = true;
                } else {
                    $userAccountId = $loggedInUserAccount;
                    $consignmentFilter->addGroupBy(' u.user_account_id');

                }

                /////////////// USER FILTER/////////////
                $userAccountArry = CustomerAccount::accountSubAccount($userAccountId, 0, true);
                $ids = implode(",", $userAccountArry);
                $consignmentFilter->addFilter(" c.user_id IN (SELECT id FROM user WHERE u.user_account_id IN (" . $ids . ") )", 'filter');

                ////////////////Mawb Filter/////////////
                if (isset($this->form_vars['mawb']) && $this->form_vars['mawb'] != '') {
                    $mawb = trim($this->form_vars['mawb']);
                    $mawbWhere = " c.mawb = '" . DbAccess3::escape($mawb) . "'";
                    $consignmentFilter->addFilter($mawbWhere, 'filter');
                }

                ////////////////Data Selection/////////////
                $columns = " ( SELECT 
                                    user_account
                                FROM
                                    customer_account ua
                                WHERE
                                    ua.id = u.user_account_id
                                LIMIT 1) AS account,
                            c.user_id,
                            s.name service_name,
                            COUNT(c.id) AS shipment_count,
                            SUM(c.weight) weight,
                            SUM(c.number_pieces) number_pieces,
                            sum(basic_charges) as basic_charges,
                            sum(fuel_charges) as fuel_charges,
                            sum(agent_basic_charges) as agent_basic_charges,
                            sum(agent_fuel_charges) as agent_fuel_charges";


                ////////////////joins/////////////

                $consignmentFilter->addJoin("user u", "u.id = c.user_id", "INNER");
                $consignmentFilter->addJoin("services s", "s.id =  c.service_id", "LEFT");
                $consignmentFilter->addJoin(" (SELECT SUM(cost) as basic_charges, consignment_id
                                                FROM
                                                consignment_charges
                                                WHERE
                                                cost_type = 'customer'
                                                AND charge_type_id NOT IN (2 , 28)
                                                AND account_id = '" . $userAccountId . "' group by consignment_id ) basic", "basic.consignment_id = c.id", "LEFT");
                $consignmentFilter->addJoin(" (SELECT 
                                                SUM(cost) as fuel_charges, consignment_id
                                                FROM
                                                consignment_charges
                                                WHERE
                                                cost_type = 'customer'
                                                AND charge_type_id =2 
                                                AND account_id = '" . $userAccountId . "' group by consignment_id ) customer_fuel ", " customer_fuel.consignment_id = c.id ", "LEFT");

                $consignmentFilter->addJoin(" (SELECT 
                                                SUM(cost) as agent_basic_charges, consignment_id
                                                FROM
                                                consignment_charges
                                                WHERE
                                                cost_type = 'agent'
                                                AND charge_type_id NOT IN (2 , 28)
                                                AND account_id = '" . $userAccountId . "' group by consignment_id ) abasic", " abasic.consignment_id = c.id  ", "LEFT");

                $consignmentFilter->addJoin(" (SELECT 
                                                   SUM(cost) as agent_fuel_charges, consignment_id
                                                   FROM
                                                   consignment_charges
                                                   WHERE
                                                   cost_type = 'agent'
                                                   AND charge_type_id =2 
                                                   AND account_id = '" . $userAccountId . "' group by consignment_id ) agent_fuel", " abasic.consignment_id = c.id  ", "LEFT");

////////////////Execution/////////////
                $consignmentObjs = $consignmentFilter->getListNew($columns);


                $countData = count($consignmentObjs);
                if ($countData > 0) {
                    $GPReportPDFObj = new GPReportPDF();
                    $result = $GPReportPDFObj->generateReportPdf($consignmentObjs, $countData, $accountReport, $userAccountId, $dateFrom, $dateTo);
                } else {
                    $this->msg = 'No records found for selected filter.';
                    $this->flashMsg->error($this->msg);
                }

            } else {

                $this->msg = 'Error! Select date ranges, start and end date.';
                $this->flashMsg->error($this->msg);

            }


        }
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter()
    {
        ?>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>

        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                if ($('.date-picker').length > 0) {
                    $('#from_date').datepicker({
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    }).on('changeDate', function () {
                        orignalDate = new Date($(this).val());
                        oneMonthExtented = new Date(new Date(orignalDate).setMonth(orignalDate.getMonth() + 2));
                        $('#to_date').datepicker('setStartDate', orignalDate);
                        $('#to_date').datepicker('setEndDate', oneMonthExtented);
                    });

                    $('#to_date').datepicker({
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    }).on('changeDate', function () {
                        // set the "fromDate" end to not be later than "toDate" starts:
                        orignalDate = new Date($(this).val());
                        oneMonthBefore = new Date(new Date(orignalDate).setMonth(orignalDate.getMonth() - 2));
                        $('#from_date').datepicker('setStartDate', oneMonthBefore);
                        $('#from_date').datepicker('setEndDate', orignalDate);
                        //  $('#to_date').datepicker('setEndDate', orignalDate);
                    });
                }

                $("#resetBtn").click(function () {
                    $('#from_date').datepicker();
                    $('#to_date').datepicker();

                    $('.input-daterange input').each(function () {
                        //$(this).datepicker('clearDates');
                        $(this).datepicker('setStartDate', null);
                        $(this).datepicker('setEndDate', null);

                    });

                });
            });

        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-list"></i>
                    GP PDF Report
                </div>
                <div class="actions">
                </div>
            </div>


            <?php if (!empty($this->error_msg)) { ?>
                <div class="alert  alert-danger"><?php echo $this->error_msg; ?></div>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <?php $this->flashMsg->display();
                    ?>
                </div>
            </div>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                       <span id="account_name"></span>Report Filters
                    </div>
                </div>

                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form"
                          enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="control-label">Date Ranges </label>
                                <div class="input-group date-picker input-daterange"
                                     data-date="20-01-2018" data-date-format="dd-mm-yyyy">
                                    <input type="text" autocomplete="off" class="form-control" name="from_date"
                                           id="from_date"
                                           value="<?= (!empty($_POST['from_date']) ? formatDate($_POST['from_date']) : ''); ?>"
                                           data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" autocomplete="off" class="form-control" name="to_date"
                                           id="to_date"
                                           value="<?= (!empty($_POST['to_date']) ? formatDate($_POST['to_date']) : ''); ?>"
                                           data-original-title="" title="">
                                    <input type="hidden" class="" name="download_file" id="download_file" value="">

                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="label-account">MAWB</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="mawb" name="mawb" type="text"
                                               placeholder="Master Number" value="" rel="tooltip"
                                               data-original-title="MAWB">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="label-account">Select Account</label>
                                <div class="form-group">
                                    <div id="user_content">
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                            $accountParentId = $this->user->getUserAccountId();
                                            $includeParent = false;
                                        }
                                        $selectedAccount = (!empty($_POST['user_account_id']) ? $_POST['user_account_id'] : '');
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        ?>
                                        <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1' and parentid = '" . $accountParentId . "'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent, $allowedLevel); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <button class="btn btn-default" name="resetBtn" id="resetBtn" type="button">
                                        <i class="fa fa-eraser"></i> Reset Date Filters
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button class="btn btn-primary" name="submitFilter" type="submit">
                                        <i class="fa fa-download"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead()
    {
        ?>

        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>