<?php
// get settings
require_once("../includes/settings/config.inc.php");
ini_set('max_input_vars', 50000);
include_classes([
    'useraccount.class',
    'useraccountfilter.class',
    'invoicetemplates.class',
    'invoicetemplatesfilter.class'
]);

class Page extends BasePage
{

    public $user;
    public $invoiceTemplateObjs;
    public $accountInvoiceTemplate;
    public $account_id;

    protected function init()
    {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Invoice Templates"
        );

        $this->user = SessionManager::getUser();
        $this->account_id = $_GET['id'];
        $accountObj = new CustomerAccount($this->account_id);
        $this->accountInvoiceTemplate = $accountObj->getInvoiceTemplateId();
        if($this->accountInvoiceTemplate == "") {
            $this->accountInvoiceTemplate = 1;
        }
        $invoiceTemplateObj = new InvoiceTemplatesFilter();
        $this->invoiceTemplateObjs = $invoiceTemplateObj->getList();

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'update_template') {
            $return = [];
            $template_id = $this->form_vars['template_id'];
            $account_id = $this->form_vars['account_id'];
            $accountObj = new CustomerAccount($account_id);
            $accountObj->setInvoiceTemplateId($template_id);
            $accountObj->save();
            $return['status'] = "success";
            $return['message'] = "Template update successfully";
            echo json_encode($return);
            die;
        }
    }

    protected function numberFormat($number, $decimalPoints = 2)
    {
        return number_format($number, $decimalPoints, '.', '');
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
        <link href="../assets/pages/css/portfolio.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>

        <script src="../assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/portfolio-1.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function () {

            });
            function activate(template_id) {
                var account_id = <?php echo $this->account_id ?>;
                var form_data = new FormData();
                form_data.append('action', 'update_template');
                form_data.append('template_id', template_id);
                form_data.append('account_id', account_id);
                $.ajax({
                    url: 'invoice_templates.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        swal({
                            title: "success",
                            text: response.message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Ok",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                location.reload();
                            }
                        });
                    }
                });
            }
        </script>
        <?php
    }

    protected function renderBody()
    {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-money"></i>
                    Templates
                </div>
                <div class="actions"></div>
                <div class="tools"></div>
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
                <div class="row">
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="portfolio-content portfolio-1">
                        <div id="js-grid-juicy-projects" class="cbp">
                            <?php if(count($this->invoiceTemplateObjs)) { ?>
                            <?php foreach($this->invoiceTemplateObjs as $invoiceTemplateObj) { ?>
                            <div class="cbp-item graphic">
                                <div class="cbp-caption">
                                    <div class="cbp-caption-defaultWrap">
                                        <img src="../assets/pages/img/invoice_template/<?php echo $invoiceTemplateObj->getImage(); ?>" alt="">
                                    </div>
                                    <div class="cbp-caption-activeWrap">
                                        <div class="cbp-l-caption-alignCenter">
                                            <div class="cbp-l-caption-body">
                                                <?php if($this->accountInvoiceTemplate  != $invoiceTemplateObj->getId()) { ?>
                                                    <a href="javascript:;" class="btn green uppercase" rel="nofollow" onclick="activate(<?php echo $invoiceTemplateObj->getId() ?>)" >Activate</a>
                                                <?php } else { ?>
                                                    <h4 style="color: #fff;">Activated</h4>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cbp-l-grid-projects-title uppercase text-center"><?php echo $invoiceTemplateObj->getName(); ?></div>
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                                <div class="col-sm-12">
                                    <div class="alert alert-danger">
                                        No Template Found
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }

    protected function renderHead()
    {
        ?>
        <style>

        </style>
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

}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>