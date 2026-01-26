<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([  
                    'invoices.class',
                    'invoicesfilter.class',
                    'notfoundrecord.class',
                    'notfoundrecordfilter.class',
    ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $notFoundRecordFilter = array();
    private $user = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Not Found Record List"
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "not_found_record_ajax") {
            $this->notFoundRecordFilter = new NotFoundRecordFilter();
            $this->notFoundRecordFilter->join("user u", "u.id=nfr.scanned_by");
            
            if($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                $this->notFoundRecordFilter->where(['u.user_account_id' => $this->user->getUserAccountId()]);
            }

            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $filterArray = [];

                $mawb = $this->form_vars['mawb'];
                if (!empty($mawb))
                    $filterArray['nfr.mawb'] = $mawb;

                $tracking_number = $this->form_vars['tracking_number'];
                if (!empty($tracking_number))
                    $filterArray['nfr.tracking_number'] = $tracking_number;

                $bag_number = $this->form_vars['bag_number'];
                if (!empty($bag_number))
                    $filterArray['nfr.bag_number'] = $bag_number;

                $length = $this->form_vars['length_filter'];
                if (!empty($length))
                    $filterArray['nfr.length'] = $length;
                
                $width = $this->form_vars['width'];
                if (!empty($width))
                    $filterArray['nfr.width'] = $width;
                
                $height = $this->form_vars['height'];
                if (!empty($height))
                    $filterArray['nfr.height'] = $height;
                
                $weight = $this->form_vars['weight'];
                if (!empty($weight))
                    $filterArray['nfr.weight'] = $weight;
                
                $scanned_by = $this->form_vars['user_id'];
                if (!empty($scanned_by))
                    $filterArray['nfr.scanned_by'] = $scanned_by;

                $this->notFoundRecordFilter->where($filterArray);

                $date_created_from = $this->form_vars['date_created_from'];
                $date_created_to = $this->form_vars['date_created_to'];
                if (!empty($date_created_from) && !empty($date_created_to))
                    $this->notFoundRecordFilter->whereBetween('nfr.date_created', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));

            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = "ASC";
                if ($orderBy == 'desc') {
                    $orderFalse = 'DESC';
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if($dataTableColumnName == "user_id") {
                    $dataTableColumnName = "scanned_by";
                }
                if($dataTableColumnName == "length_filter") {
                    $dataTableColumnName = "length";
                }
                $this->notFoundRecordFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
            } else {
                $this->notFoundRecordFilter->orderBy(strtolower("nfr.id"), "DESC");
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
            $this->notFoundRecordFilter->setRowsPerPage($iDisplayLength);
            $this->notFoundRecordFilter->setOffset($iDisplayStart);
            $notFoundRecordFilterObjs = $this->notFoundRecordFilter->getList("nfr.*");
            $iTotalRecords = $this->notFoundRecordFilter->getCount();

            $setDataArr = array();
            foreach ($notFoundRecordFilterObjs as $notFoundRecordFilterObj) {
                $userObj = new User($notFoundRecordFilterObj->getScannedBy());
                if (!empty($notFoundRecordFilterObj->getDateCreated())) {
                    $date_created = date("d-m-Y", strtotime($notFoundRecordFilterObj->getDateCreated()));
                } else {
                    $date_created = "";
                }
                $currentArr = array();
                $currentArr['date_created'] = $date_created;
                $currentArr['mawb'] = $notFoundRecordFilterObj->getMawb();
                $currentArr['tracking_number'] = $notFoundRecordFilterObj->getTrackingNumber();
                $currentArr['bag_number'] = $notFoundRecordFilterObj->getBagNumber();
                $currentArr['length'] = $notFoundRecordFilterObj->getLength();
                $currentArr['width'] = $notFoundRecordFilterObj->getWidth();
                $currentArr['height'] = $notFoundRecordFilterObj->getHeight();
                $currentArr['weight'] = $notFoundRecordFilterObj->getWeight();
                if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                    $currentArr['scanned_by'] = $userObj->getUserName();
                }
                $currentArr['actions'] = "";
//                $currentArr['actions'] = '<div class="btn-group" data-container="body" >
//                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
//                                                <i class="fa fa-angle-down"></i>
//                                            </button>
//                                            <ul class="dropdown-menu" >';
//                    $currentArr['actions'] .= '<li>
//                                                    <a href="JavaScript:void(0);" data-tariff_id="' . $tariffsObj->getId() . '" class="btndelete" title="Delete">
//                                                        <i class="fa fa-trash"></i> Delete
//                                                    </a>
//                                                </li>';
//                $currentArr['actions'] .= '</ul>
//                                        </div>';
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
                    var datatableurl = "not_found_record.php?action=not_found_record_ajax";
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
                            $(".table-container .custom-alerts").hide();
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
                                {"data": "date_created", "bSortable": false},
                                {"data": "mawb"},
                                {"data": "tracking_number"},
                                {"data": "bag_number"},
                                {"data": "length"},
                                {"data": "width"},
                                {"data": "height"},
                                {"data": "weight"},
                                <?php if($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                                {"data": "scanned_by"}
                                <?php } ?>
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
                    Not Found Record List
                </div>
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <!--Najam Code-->
                <div class="table-container">
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Scanned Date</th>
                            <th>MAWB</th>
                            <th>Tracking Number</th>
                            <th>Bag Number</th>
                            <th>Length</th>
                            <th>Width</th>
                            <th>Height</th>
                            <th>Weight</th>
                            <?php if($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                            <th>Scanned By</th>
                            <?php } ?>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                    <span class="input-group-btn">
                                        <button class="btn btn-sm default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="mawb" id ="mawb" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="tracking_number" id ="tracking_number" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="bag_number" id ="bag_number" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="length_filter" id ="length" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="width" id ="width" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="height" id ="height" />
                            </td>
                            <td>
                               <input type="text" class="form-control form-filter input-xs" name="weight" id ="weight" />
                            </td>
                            <?php if($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                            <td>
                                <?php
                                echo Ddl::generateDDL('user_id', 'UserFilter', ' user_account_id = "'.$this->user->getUserAccountId().'" ', 'user_name', 'id', "", ' class="form-filter select2 form-control" data-toggle="tooltip" data-placement="top" title="Scan by" data-original-title="Scan by"', 'Please Select', '', 'user_id', 'Scan by');
                                ?>
                            </td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">

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