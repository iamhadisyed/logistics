<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'manifest.class','manifestfilter.class'
],'autoload');
include_classes([
    'manifestservicemapping.class',
    'manifestservicemappingfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'services.class'
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $user;
    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Return Manifest List"
        );
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "manifest_ajax") {
            $manifestFilter = new ManifestFilter();
            $manifestFilter->addFieldFilter("    m.type","return");
            if($this->user->getUserType() !="admin")
                $manifestFilter->addFieldFilter("u.account_id",$this->user->getUserAccountId());

            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $manifestId = $this->form_vars['manifest_id'];
                if (!empty($manifestId))
                    $manifestFilter->addFieldFilter('   m.id', $manifestId);

                $manifestWeight = $this->form_vars['manifest_weight'];
                if (!empty($manifestWeight))
                    $manifestFilter->addFieldLikeFilter('weight', $manifestWeight);
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
                $manifestFilter->AddOrderBy(strtolower("rg.".$dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $manifestFilter->getRypPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $manifestFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $manifestFilter->setOffset($iDisplayStart);
            $manifestObjs = $manifestFilter->getPagingRypList();
            $setDataArr = array();
            foreach ($manifestObjs as $manifestObj) {
                $currentArr = array();
                $manifestServices = "";
                $manifestServiceMappingFilter = new ManifestServiceMappingFilter();
                $manifestServiceMappingFilter->addFieldFilter("    manifest_id",$manifestObj->getId());
                $manifestServiceMappingObj = $manifestServiceMappingFilter->getColumnList("service_id");
                if(count($manifestServiceMappingObj) > 0){
                    foreach ($manifestServiceMappingObj as $manifestServiceMappingArr) {
                        $service = new Services($manifestServiceMappingArr->getServiceId);
                        $manifestServices .= $service->getName()." ";
                    }
                }
                $manifestparcels = 0;
                $manifestEntityMappingFilter = new ManifestEntityMappingFilter();
                $manifestEntityMappingFilter->addFieldFilter("    manifest_id",$manifestObj->getId());
                $manifestEntityMappingFilter->addFieldFilter("    manifest_entity_type","p");
                $manifestEntityMappingObj = $manifestEntityMappingFilter->getColumnList("entity_id");
                $manifestparcels = count($manifestEntityMappingObj);
                $currentArr['manifest_number'] = $manifestObj->getId();
                $currentArr['total_parcel'] = $manifestparcels;
                $currentArr['services'] = $manifestServices;
                $pdfFileLine = SETTING_MAIN_ASSETS."manifest/pdf/".$manifestObj->getPdfFile();
                $currentArr['manifest_file'] = "<a target='_blank' href='".$pdfFileLine."' class='btn btn-xs blue btn-outline'><span class='fa fa-eye'></span> </a>";
                $currentArr['manifest_weight'] = $manifestObj->getWeight();
                $currentArr['actions'] = "";
//                        "<a href='JavaScript:Void(0);' data-group_id='".$remoteareasGroupsObj->getId()."' class='btnedit btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>      <a href='services_view.php?id=" . $remoteareasGroupsObj->getId() . "' class='btn btn-xs blue btn-outline'><span class='fa fa-eye'></span> </a>"
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
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
                                "url": "return_manifest.php?action=manifest_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                        {"data": "actions", "bSortable": false},
                                        {"data": "manifest_number"},
                                        {"data": "total_parcel"},
                                        {"data": "services"},
                                        {"data": "manifest_file"},
                                        {"data": "manifest_weight"}
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

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Return Manifest List
                </div>
                <div class="actions">
                    <!--<a href="#" class="btn blue"  ><i class="fa fa-plus"></i> Add New</a>-->
                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Manifest</th>
                            <th>Total Parcel</th>
                            <th>Services</th>
                            <th>Manifest File</th>
                            <th>Manifest Weight</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>

                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="manifest_id" id ="manifest_id" />
                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-xs" name="manifest_weight" id ="manifest_weight" />
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

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>