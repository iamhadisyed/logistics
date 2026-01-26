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
    'useraccountadditionalcontactsfilter.class',
    'useraccountadditionalcontacts.class',
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


        if (!Permissions::checkFilePermission('additional_contacts.php'))
            util_redirect("index.php");
        
        if (isset($_GET['id']) && trim($_GET['id']) == '')
            util_redirect("index.php");
        if (!isset($_GET['id']))
            util_redirect("index.php");
        $userAccountAdditionalContactid = base64_decode($_GET['id']);
        if($userAccountAdditionalContactid<= 0)
            util_redirect("index.php");
            
        $this->userAccountData    =   new CustomerAccount($userAccountAdditionalContactid);
        
        $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'additional_contacts.php' => 'Additional Contact Details'
        );
        //$this->constantlist = new ServiceConstantFilter();
        // common initialisation for ths page
        $this->setTitle("Constant List");
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {
            extract($this->form_vars);

            $userAccountAdditionalContactsDataobj = new UserAccountAdditionalContacts($id);
            $userAccountAdditionalContactsDataobj->setUserAccountId($userAccountAdditionalContactid);
            $userAccountAdditionalContactsDataobj->setTitle($title);
            $userAccountAdditionalContactsDataobj->setName($name);
            $userAccountAdditionalContactsDataobj->setEmail($email);
            $userAccountAdditionalContactsDataobj->setPhone($phone);
            $userAccountAdditionalContactsDataobj->setContactType($contact_type);
            if ((int) $id <= 0) {
                $userAccountAdditionalContactsDataobj->setAddedBy($user->getId());
                //$invoiceDataobj->setDateAdded(date("Y-m-d h:i:s"));
            }
            $userAccountAdditionalContactsDataobj->save();

            die;
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $constantId = $_POST['constantid'];

            $this->id = $constantId;
            $edit_array = array();
            $serviceConstantValue = new UserAccountAdditionalContacts($constantId);

            $edit_array['id'] = (int) $constantId;
            $edit_array['user_account_id'] = $serviceConstantValue->getUserAccountId();
            $edit_array['title'] = $serviceConstantValue->getTitle();
            $edit_array['name'] = $serviceConstantValue->getName();
            $edit_array['email'] = $serviceConstantValue->getEmail();
            $edit_array['phone'] = $serviceConstantValue->getPhone();
            $edit_array['contact_type'] = $serviceConstantValue->getContactType();
            echo json_encode($edit_array);
            die;
        }


        if (isset($_GET['action']) && $_GET['action'] == "user_account_additional_contacts_ajax") {
            // Get current user
            $this->userAccountAdditionalContactsFilter = new UserAccountAdditionalContactsFilter();


            $this->userAccountAdditionalContactsFilter->addFieldFilter('uaac.user_account_id', $userAccountAdditionalContactid);


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
                $search_title = $this->form_vars['search_title'];
                if (!empty($search_title)) {
                    $this->userAccountAdditionalContactsFilter->addFieldLikeFilter('uaac.title', $search_title);
                }
                $search_contact_type = $this->form_vars['search_contact_type'];
                if (!empty($search_contact_type)) {
                    $this->userAccountAdditionalContactsFilter->addFieldLikeFilter('uaac.contact_type', $search_contact_type);
                }
                $search_name = $this->form_vars['search_name'];
                if (!empty($search_name)) {
                    $this->userAccountAdditionalContactsFilter->addFieldLikeFilter('uaac.name', $search_name);
                }
                $search_phone = $this->form_vars['search_phone'];
                if (!empty($search_phone)) {
                    $this->userAccountAdditionalContactsFilter->addFieldLikeFilter('uaac.phone', $search_phone);
                }
                $search_email = $this->form_vars['search_email'];
                if (!empty($search_email)) {
                    $this->userAccountAdditionalContactsFilter->addFieldLikeFilter('uaac.email', $search_email);
                }
                
            }

//            $this->userAccountAdditionalContactsFilter->addFieldLikeFilter('uaac.user_account_id', $userAccountAdditionalContactid);

            $iTotalRecords = $this->userAccountAdditionalContactsFilter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->userAccountAdditionalContactsFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->userAccountAdditionalContactsFilter->setOffset($iDisplayStart);

            if ($dataTableColumnName != '') {
                $this->userAccountAdditionalContactsFilter->AddOrderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->userAccountAdditionalContactsFilter->AddOrderBy('uaac.id', false);
            }
            $invoiceBankDetailsList = $this->userAccountAdditionalContactsFilter->getPagingList("  uaac.*, ua.user_account, u.user_name");

            $invoiceBankDetailsDataArr = array();
            foreach ($invoiceBankDetailsList as $item) {
                $invoiceBankDetailsArr['actionss'] = '';
                $invoiceBankDetailsArr['actionss'] .= "<a data-id =" . $item->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-pencil'></span> </a>";

                $invoiceBankDetailsArr['title'] = $item->getTitle();
                $invoiceBankDetailsArr['contact_type'] = $item->getContactType();
                $invoiceBankDetailsArr['name'] = $item->getName();
                $invoiceBankDetailsArr['email'] = $item->getEmail();
                $invoiceBankDetailsArr['phone'] = $item->getPhone();$invoiceBankDetailsArr['added_by'] = $item->getUserName();
                $invoiceBankDetailsArr['date_added'] = date("d-m-Y", strtotime($item->getDateCreated()));

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
                        url: "additional_contacts.php",
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
                                url: "additional_contacts.php?id=<?php echo $_GET['id'];?>",
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
                        url: "additional_contacts.php?id=<?php echo $_GET['id'];?>",
                        data: {constantid: constantid, func: "editrecord"}
                    }).done(function (data) {

                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");
                        $('#user_account_id').val(t.user_account_id).trigger('change');
                        $('#title').val(t.title).trigger('change');
                        $('#name').val(t.name);
                        $('#email').val(t.email);
                        $('#phone').val(t.phone);
                        $('#contact_type').val(t.contact_type).trigger('change');
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
                                "url": "additional_contacts.php?id=<?php echo $_GET['id'];?>&action=user_account_additional_contacts_ajax", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "contact_type"},
                                {"data": "title"},
                                {"data": "name"},
                                {"data": "email"},
                                {"data": "phone"},
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
                            
                                Additional Contacts for <?php echo $this->userAccountData->getUserAccount();?>
                            
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
                                                        <label>Department</label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="fa fa-building"></i>
                                                            </div>
                                                            <?php
                                                            $departmentArray = ['Accounts'=>'Accounts', 
                                                                'Customer Services'=>'Customer Services',
                                                                'IT'=>'IT',
                                                                'Operation'=>'Operation',
                                                                'Sales'=>'Sales' 
                                                                ];
                                                            echo Ddl::generateArrayDDL('contact_type', $departmentArray, $contact_type, '', ' required class="form-control select2 select full-width-box" rel="tooltip" data-original-title="Department" placeholder="Department"');
                                                            ?>
                                                        </div>
                                                    </div>
                                        </div>
                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Title</label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="fa fa-user"></i>
                                                            </div>
                                                            <?php
                                                            $titlesArray = [
                                                                'Mr'=>'Mr', 
                                                                'Mrs'=>'Mrs', 
                                                                'Miss'=>'Miss', 
                                                                'Ms'=>'Ms', 
                                                                'Mx'=>'Mx', 
                                                                'Sir'=>'Sir', 
                                                                'Dr'=>'Dr', 
                                                                'Lady'=>'Lady',
                                                                'Lord'=>'Lord'];
                                                            echo Ddl::generateArrayDDL('title', $titlesArray, $title, '', ' required class="form-control select2 select full-width-box" rel="tooltip" data-original-title="Title" placeholder="Title"');
                                                            ?>
                                                        </div>
                                                    </div>
                                        </div>

                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="name" id="name" value="" size="50"
                                                   class="form-control" title="Name" maxlength="35"
                                                   placeholder="Name" rel="tooltip"
                                                   data-original-title="Name" type="text" required >
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="email" id="email" value="" size="50"
                                                   class="form-control" title="Email" maxlength="35"
                                                   placeholder="Email" rel="tooltip"
                                                   data-original-title="Email" type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <div class="input-group input-group-sm input-icon right">
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="phone" id="phone" value="" size="50"
                                                   class="form-control" title="Account Number" maxlength="35"
                                                   placeholder="Phone Number" rel="tooltip"
                                                   data-original-title="Phone Number" type="text" required>
                                            <span class="input-group-addon red-18">*</span>
                                        </div>
                                    </div>
                                </div>
 <!--                               <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Status</label><br/>
                                        <input name="status" id="status" type="checkbox" class="make-switch make-switch-status" data-size="small" data-on-text="Active" check data-off-text="Inactive" checked data-on-color="primary" data-off-color="danger">
                                    </div>
                                </div>-->
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
                            Listing
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>Department</th>
                                    <th>Title</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
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
                                        <?php 
                                               echo Ddl::generateArrayDDL('search_contact_type', $departmentArray, $contact_type, '', ' class="form-control select2 select full-width-box" rel="tooltip" data-original-title="Department" placeholder="Department"');
                                               ?>
                                    </td>
                                    <td>
                                        <?php 
                                            echo Ddl::generateArrayDDL('search_title', $titlesArray, $title, '', ' class="form-control select2 select full-width-box" rel="tooltip" data-original-title="Title" placeholder="Title"');
                                         ?>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm"
                                               name="search_name" id="search_name"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_email"
                                               id="search_email"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_phone"
                                               id="search_phone"/>
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
