<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class'
], 'general');
include_classes([
    'pdfmerger'
], 'labels');
include_classes([
    'ups.class'
], 'labels');
include_classes([
    'tcpdf'
], '3rdparty/tcpdf');
include_classes([
    'include_list',
], 'reamus');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'csvimporttemplate.class',
    'csvimporttemplatefilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'mawb.class',
    'mawbfilter.class',
    'baggingfilter.class',
    'bagging.class',
    'currency.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',

]);

class Page extends BasePage {

    //how many bookings to import/validate at once?
    const IMPORT_BATCH_MAX = 10;
    // page states
    const STATUS_INITIAL = 0;

    private $user = null;
    private $csvColumn = [];
    private $csvTemplate = [];

    /*     * *
     * Controller logic
     */

    protected function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'client_list.php' => Translation::GetCaption("SHIPMENTS"),
            Translation::GetCaption("IMPORT")
        );
        $this->user = SessionManager::getUser();
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_shipmnet_csv') {
            $output = [
                    'status' => 'error',
                    'message' => 'File not upload successfully please contact to admin',
            ];
            $accountId = $this->form_vars['user_account_id'];
            $accountObj = new CustomerAccount($accountId);
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $account = strtolower($accountObj->getUserAccount());
                    if (!file_exists( SETTING_DIR_ASSETS . "user_data_csv/" . $account)) {
                        mkdir( SETTING_DIR_ASSETS . "user_data_csv/" . $account, 0777, true);
                    }
                    if (!file_exists( SETTING_DIR_ASSETS . "user_data_csv/" . $account."/data_in")) {
                        mkdir( SETTING_DIR_ASSETS . "user_data_csv/" . $account."/data_in", 0777, true);
                    }
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $destinationPath = SETTING_DIR_ASSETS . "user_data_csv/" . $account . "/data_in/";
                    if (move_uploaded_file($csv_file['tmp_name'], $destinationPath . $new_file_name)) {
                        $output = [
                            'status' => 'success',
                            'message' => 'File uploaded successfully'
                        ];
                    }
                } else {
                    $output['message'] = "Please select only valid csv file";
                }
            } else {
                $output['message'] = "No file found to upload";
            }
            echo json_encode($output);
            die;
        }
        // common initialisation for ths page
        $this->setTitle("Upload Shipnment Via FTP");
    }

    protected function renderHead() {

    }

    protected function addPagelavelCss() {
        ?>

        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-nestable/jquery.nestable.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .help-block-error{
                display: none !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>

        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-nestable/jquery.nestable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <?php
    }

    protected function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $(document).on('click', '#btnSubmitUpload', function() {
                    var form = $('#shipment_csv_upload_form')[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);
                    formData.append('func', 'upload_shipmnet_csv')
                    $.blockUI();
                    $.ajax({
                        url: 'upload_shipment_via_ftp.php',
                        data: formData,
                        type: 'POST',
                        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                        processData: false, // NEEDED, DON'T OMIT THIS
                        dataType: "json",
                        success: function (response) {
                            if(response.status == "success") {
                                swal("Success!", response.message, "success");
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                            $.unblockUI();
                        },
                        error: function () {
                            $.unblockUI();
                        }
                    });
                });
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>

        <div class="main_formpage">
            <!--NEW Block Start-->
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-upload"></i>
                        <?php echo "Upload Shipnment CSV"; ?>
                    </div>
                    <div class="actions">
                    </div>
                </div>

                <div class="portlet-body" >
                    <form method="post" id="shipment_csv_upload_form">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="label-account">Select Account</label>
                                <div class="form-group">
                                    <div id="user_account_content">
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                                            $accountParentId = $this->user->getUserAccountId();
                                        }
                                        $selectedAccount = $this->user->getUserAccountId();
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        ?>
                                        <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true" onclick="get_account_users()" ', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="form-group">
                                        <label> <?php echo Translation::GetCaption("SELECT_FILE_TO_IMPORT"); ?></label>
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                <span class="fileinput-filename"> </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput-new"> Select file </span>
                                                <span class="fileinput-exists"> Change </span>
                                                <input type="file" name="csv_file" id="csv_file">
                                            </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitUpload" data-original-title="" title=""><?php echo Translation::GetCaption("UPLOAD"); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div style="clear: both;">&nbsp;</div>
                </div>
            </div>
        </div>
        <?php
    }
    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {

        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
