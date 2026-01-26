<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'errorlist.class'
                ],'visualcomponents');
include_classes([
    'invoices.class',
    'invoicesfilter.class',
    'invoicebankdetailsfilter.class',
    'invoicebankdetails.class',
    'currency.class',
    'currencyfilter.class']);

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

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript"
                src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
        type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript"
                src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
        type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>


        <?php
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    protected function init() {


        if (!Permissions::checkFilePermission('invoice_bank_details.php'))
            util_redirect("index.php");
        $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'invoice_bank_details.php' => 'Invoice Bank Details'
        );
        //$this->constantlist = new ServiceConstantFilter();
        // common initialisation for ths page
        $this->setTitle("Constant List");
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {

            $constant_id            = $this->form_vars["id"];
            $userAccountId          = $user->getUserAccountId();
            $account_title          = $this->form_vars["account_title"];
            $account_sortcode       = $this->form_vars["account_sortcode"];
            $account_swift_code     = $this->form_vars["account_swift_code"];
            $account_number         = $this->form_vars["account_number"];
            $account_iban           = $this->form_vars["account_iban"];
            $bank_name              = $this->form_vars["bank_name"];
            $bank_branch            = $this->form_vars["bank_branch"];
            $bank_address           = $this->form_vars["bank_address"];
            $currency               = $this->form_vars["currency"];
            $status                 = (isset($_POST["status"]) ? '1' : '0');

            $invoiceDataobj = new InvoiceBankDetails($constant_id);
            $invoiceDataobj->setUserAccountId($userAccountId);
            $invoiceDataobj->setAccountTitle($account_title);
            $invoiceDataobj->setAccountSortcode($account_sortcode);
            $invoiceDataobj->setAccountSwiftCode($account_swift_code);
            $invoiceDataobj->setAccountNumber($account_number);
            $invoiceDataobj->setAccountIban($account_iban);
            $invoiceDataobj->setBankName($bank_name);
            $invoiceDataobj->setBankBranch($bank_branch);
            $invoiceDataobj->setBankAddress($bank_address);
            $invoiceDataobj->setStatus($status);
            $invoiceDataobj->setCurrency($currency);
            if ((int) $constant_id <= 0) {
                $invoiceDataobj->setAddedBy($user->getId());
                $invoiceDataobj->setDateAdded(date("Y-m-d h:i:s"));
            }
            $invoiceDataobj->save(false,true);

            die;
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $constantId = $_POST['constantid'];

            $this->id = $constantId;
            $edit_array = array();
            $serviceConstantValue = new InvoiceBankDetails($constantId);

            $edit_array['id'] = (int) $constantId;
            $edit_array['user_account_id'] = $serviceConstantValue->getUserAccountId();
            $edit_array['account_title'] = $serviceConstantValue->getAccountTitle();
            $edit_array['account_sortcode'] = $serviceConstantValue->getAccountSortcode();
            $edit_array['account_swift_code'] = $serviceConstantValue->getAccountSwiftcode();
            $edit_array['account_number'] = $serviceConstantValue->getAccountNumber();
            $edit_array['account_iban'] = $serviceConstantValue->getAccountIban();
            $edit_array['bank_name'] = $serviceConstantValue->getBankName();
            $edit_array['bank_branch'] = $serviceConstantValue->getBankBranch();
            $edit_array['bank_address'] = $serviceConstantValue->getBankAddress();
            $edit_array['status'] = $serviceConstantValue->getStatus();
            $edit_array['currency'] = $serviceConstantValue->getCurrency();
            echo json_encode($edit_array);
            die;
        }


        if (isset($_GET['action']) && $_GET['action'] == "invoice_bank_details_ajax") {
            // Get current user
            $this->invoiceBankDetailFilter = new InvoiceBankDetailsFilter();


            $this->invoiceBankDetailFilter->addFieldFilter('ibd.user_account_id', $user->getUserAccountId());


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
                $search_account_title = $this->form_vars['search_account_title'];
                if (!empty($search_account_title)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.account_title', $search_account_title);
                }
                $search_currency = $this->form_vars['search_currency'];
                if (!empty($search_currency)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.currency', $search_currency);
                }
                $search_account_sortcode = $this->form_vars['search_account_sortcode'];
                if (!empty($search_account_sortcode)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.account_sortcode', $search_account_sortcode);
                }
                $search_account_number = $this->form_vars['search_account_number'];
                if (!empty($search_account_sortcode)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.account_number', $search_account_number);
                }
                $search_iban = $this->form_vars['search_iban'];
                if (!empty($search_iban)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.account_iban', $search_iban);
                }

                $search_bank_name = $this->form_vars['search_bank_name'];
                if (!empty($search_bank_name)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.bank_name', $search_bank_name);
                }

                $search_bank_branch = $this->form_vars['search_bank_branch'];
                if (!empty($search_bank_branch)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.bank_branch', $search_bank_branch);
                }
                $search_bank_address = $this->form_vars['search_bank_address'];
                if (!empty($search_bank_address)) {
                    $this->invoiceBankDetailFilter->addFieldLikeFilter('ibd.bank_address', $search_bank_address);
                }
                $search_status = $this->form_vars['search_status'];

                if ($search_status != '') {
                    $this->invoiceBankDetailFilter->addFieldFilter('ibd.status', $search_status);
                }
            }

            $iTotalRecords = $this->invoiceBankDetailFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->invoiceBankDetailFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->invoiceBankDetailFilter->setOffset($iDisplayStart);

            if ($dataTableColumnName != '') {
                $this->invoiceBankDetailFilter->AddOrderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->invoiceBankDetailFilter->AddOrderBy('ibd.id', false);
            }
            $invoiceBankDetailsList = $this->invoiceBankDetailFilter->getPagingList("  ibd.*, ua.user_account, u.user_name");

            $invoiceBankDetailsDataArr = array();
            foreach ($invoiceBankDetailsList as $item) {
                $invoiceBankDetailsArr['actionss'] = '';
                $invoiceBankDetailsArr['actionss'] .= "<a data-id =" . $item->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-pencil'></span> </a>";

                $invoiceBankDetailsArr['account_title'] = $item->getAccountTitle();
                $invoiceBankDetailsArr['account_sortcode'] = $item->getAccountSortcode();
                $invoiceBankDetailsArr['account_number'] = $item->getAccountNumber();
                $invoiceBankDetailsArr['iban'] = $item->getAccountIban();
                $invoiceBankDetailsArr['bank_name'] = $item->getBankName();
                $invoiceBankDetailsArr['bank_branch'] = $item->getBankBranch();
                $invoiceBankDetailsArr['bank_address'] = $item->getBankAddress();
                $invoiceBankDetailsArr['currency'] = $item->getCurrency();
                $invoiceBankDetailsArr['status'] = (trim($item->getStatus()) == '1' ? '<center><span class="label label-sm label-success">' . 'Yes' . '</span></center>' : '<center><span class="label label-sm label-danger">' . 'No' . '</span></center>');
                $invoiceBankDetailsArr['added_by'] = $item->getUserName();
                $invoiceBankDetailsArr['date_added'] = date("d-m-Y", strtotime($item->getDateAdded()));

                $invoiceBankDetailsDataArr[] = $invoiceBankDetailsArr;
            }
            $invoiceBankDetailsDataArr['data'] = $invoiceBankDetailsDataArr;
            $invoiceBankDetailsDataArr['draw'] = $sEcho;
            $invoiceBankDetailsDataArr['recordsTotal'] = $iTotalRecords;
            $invoiceBankDetailsDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($invoiceBankDetailsDataArr);
            die;
        }
    }

    protected function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>

        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css"/>


        <?php
    }

    /*     * *
     * Content View
     */

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
                        url: "invoice_bank_details.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $("#bankDetailForm")[0].reset();
                    });
                });
                $(document).on('click', '#btn_Save', function () {

                    $('#bankDetailForm').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented()) {
                            return false;
                        } else {
                            var id = $('#id').val();
                            $.ajax({
                                method: "POST",
                                url: "invoice_bank_details.php",
                                data: $('#bankDetailForm').serialize()
                            }).done(function (data) {

                                $('#btn_Save').val("Save");
                                $('#success_msg').html(" ");
                                if (id == "") {
                                    $('#success_msg').html("Record has been Added Successfully");
                                } else {
                                    $('#success_msg').html("Record has been Updated Successfully");
                                }
                                $('#user_account_id').val('');
                                $('#account_title').val('');
                                $('#account_sortcode').val('');
                                $('#account_swift_code').val('');
                                $('#account_number').val('');                                
                                $('#account_iban').val('');
                                $('#currency').val('GBP');
                                $('#bank_name').val('');
                                $('#bank_branch').val('');
                                $('#bank_address').val('');
                                $('#status').val('');


                                $("div").removeClass("hidden");
                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                $('#id').val("");
                                $('#manage-data-table').DataTable().ajax.reload();
                            });
                            return false;
                        }
                    });
                    $("#bankDetailForm").submit();
                });

                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var constantid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "invoice_bank_details.php",
                        data: {constantid: constantid, func: "editrecord"}
                    }).done(function (data) {

                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");
                        $('#user_account_id').val(t.user_account_id).trigger('change');
                        $('#account_title').val(t.account_title);
                        $('#account_sortcode').val(t.account_sortcode);
                        $('#account_swift_code').val(t.account_swift_code);
                        $('#account_number').val(t.account_number);
                        $('#account_iban').val(t.account_iban);
                        $('#bank_name').val(t.bank_name);
                        $('#currency').val(t.currency);
                        $('#bank_branch').val(t.bank_branch);
                        $('#bank_address').val(t.bank_address);
                        $('#status').val(t.status);
                        $('#id').val(t.id);
                        if (t.status == "1") {
                            $('.make-switch-status').attr('checked', true);
                            $('.make-switch-status').bootstrapSwitch('state', true);
                        } else {
                            $('.make-switch-status').attr('checked', false);
                            $('.make-switch-status').bootstrapSwitch('state', false);
                        }

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
                                "url": "invoice_bank_details.php?action=invoice_bank_details_ajax", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "account_title"},
                                {"data": "account_sortcode"},
                                {"data": "account_number"},
                                {"data": "iban"},
                                {"data": "bank_name"},
                                {"data": "bank_branch"},
                                {"data": "currency"},
                                {"data": "bank_address"},
                                {"data": "status"},
                                {"data": "added_by"},
                                {"data": "date_added"}
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
                            
                                Invoices Bank Details
                            
                        </div>
                        <div class="actions">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <form name="bankDetailForm" id="bankDetailForm" action="" method="POST">
                            <div class="row">
                                <div class="col-md-12 hidden" id="successmsg">
                                    <div class="alert alert-success" id="success_msg"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Account Title</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="account_title" id="account_title" value="" size="50"
                                                   class="form-control" title="Account Title" maxlength="35"
                                                   placeholder="Account Title" rel="tooltip"
                                                   data-original-title="Account Title" type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Account Sortcode</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="account_sortcode" id="account_sortcode" value="" size="50"
                                                   class="form-control" title="Account Sortcode" maxlength="35"
                                                   placeholder="Account Sortcode" rel="tooltip"
                                                   data-original-title="Account Sortcode" type="text" >
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Account SWIFT</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="account_swift_code" id="account_swift_code" value="" size="50"
                                                   class="form-control" title="Account SWIFT" maxlength="35"
                                                   placeholder="Account SWIFT" rel="tooltip"
                                                   data-original-title="Account SWIFT" type="text" >
                                            <span class="input-group-addon red-18"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Account Number</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="account_number" id="account_number" value="" size="50"
                                                   class="form-control" title="Account Number" maxlength="35"
                                                   placeholder="Account Number" rel="tooltip"
                                                   data-original-title="Account Number" type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>IBAN</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="account_iban" id="account_iban" value="" size="50"
                                                   class="form-control" title="IBAN" maxlength="35" placeholder="IBAN"
                                                   rel="tooltip" data-original-title="IBAN" type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Bank Name</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="bank_name" id="bank_name" value="" size="50"
                                                   class="form-control" title="Bank Name" maxlength="35"
                                                   placeholder="Bank Name" rel="tooltip" data-original-title="Bank Name"
                                                   type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Bank Branch</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="bank_branch" id="bank_branch" value="" size="50"
                                                   class="form-control" title="Bank Branch" maxlength="35"
                                                   placeholder="Bank Branch" rel="tooltip"
                                                   data-original-title="Bank Branch" type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Account Currency</label>
                                        <div class="input-group select2-bootstrap-append select2-bootstrap-prepend">
                                            <div class="input-group-addon"><i class="fa fa-building"></i></div>
                                            <?php
                                            echo Ddl::generateDDL('currency', 'CurrencyFilter', '      isactive = 1 ', ['rightsymbol'], 'rightsymbol', 'GBP', ' class="form-control select2 full-width-box"  data-toggle="tooltip" data-placement="top" title="Account Currency" data-original-title="Account Currency"', 'Select Account Currency', '', 'Account Currency', 'Account Currency');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Bank Address </label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="bank_address" id="bank_address" value="" size="250"
                                                   class="form-control" title="Bank Address" maxlength="250"
                                                   placeholder="Bank Address" rel="tooltip"
                                                   data-original-title="Bank Address" type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 ">
                                    <div class="form-group">
                                        <label>Status</label><br/>
                                        <input name="status" id="status" type="checkbox" class="make-switch make-switch-status" data-size="small"
                                               data-on-text="Active" check data-off-text="Inactive" checked
                                               data-on-color="primary" data-off-color="danger">
                                    </div>
                                </div>
                            </div>
                            <div style="clear:both"></div>
                            <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"
                                   class="form-control"/>
                            <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                            <input type="hidden" name="form_action" id="form_action" value="saverecord"/>
                            <div class="row " style="text-align:centre;" align="center">
                                <div class="col-md-12">
                                    <input id="btn_Save" type="button" class="btn btn-primary"
                                           value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                                    <input id="btn_Cancel" type="button" class="btn btn-default"
                                           value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        <?php } ?>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>
                        <span class="caption-subject bold uppercase">
                            Invoices Bank Details
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>Account Title</th>
                                    <th>Account Sortcode</th>
                                    <th>Account Number</th>
                                    <th>IBAN</th>
                                    <th>Bank Name</th>
                                    <th>Bank Branch</th>
                                    <th>Account Currency</th>
                                    <th>Bank Address</th>
                                    <th>Status</th>
                                    <th>Added By</th>
                                    <th>Date Created</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline">
                                                <i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline">
                                                <i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm"
                                               name="search_account_title" id="search_account_title"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm"
                                               name="search_account_sortcode" id="search_account_sortcode"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm"
                                               name="search_account_number" id="search_account_number"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_iban"
                                               id="search_iban"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_bank_name"
                                               id="search_bank_name"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm"
                                               name="search_bank_branch" id="search_bank_branch"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm"
                                               name="search_currency" id="search_currency"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm"
                                               name="search_bank_address" id="search_bank_address"/>
                                    </td>
                                    <td>
        <?php
        $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
        echo Ddl::generateArrayDDL('search_status', $arrayTypeValues, "", "Select Status", ' class="form-control input-sm form-filter select2"', "", 'search_status', 'Select Status', '');
        ?>
                                    </td>
                                    <td></td>
                                    <td></td>

                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>"/>
                </div>
            </div>
        </div>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
