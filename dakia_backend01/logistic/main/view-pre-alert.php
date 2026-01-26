<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'flightinfo.class',
    'flightinfofilter.class',
    
    ]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $uploadfilelist = "";
    private $user;

    protected function init() {
        $this->user = SessionManager::getUser();
        
         $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Pre-alert Report'
        );
        
        if($_GET['action'] && $_GET['action'] == 'prealert_datatable'){
            $flightFilterObj=  new FlighInfoFilter();
           
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                if(!empty($this->form_vars['search_mawb'])){
                    $flightFilterObj->addFilter("   fm.mawb = '".$this->form_vars['search_mawb']."'");
                }
                if(!empty($this->form_vars['search_status'])){
                    $flightFilterObj->addFilter("   fi.status = '".$this->form_vars['search_status']."'");
                }
                if(!empty($this->form_vars['search_flight'])){
                    $flightFilterObj->addFilter("   fi.flight_number = '".$this->form_vars['search_flight']."'");
                }
                if(!empty($this->form_vars['search_weight'])){
                    $flightFilterObj->addFilter("   fi.weight = '".$this->form_vars['search_weight']."'");
                }
                if(!empty($this->form_vars['search_pieces'])){
                    $flightFilterObj->addFilter("   fi.pieces = '".$this->form_vars['search_pieces']."'");
                }
                if(!empty($this->form_vars['search_user_account_id'])){
                    $flightFilterObj->addFilter("   fi.account_id = '".$this->form_vars['search_user_account_id']."'");
                }
            }
            $flightFilterObj->addFilter("   fi.is_delete = '0' ");
            $flightFilterCount = $flightFilterObj->getViewPreAlertReportCount();
            $iTotalRecords = $flightFilterCount;
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $flightFilterObj->setRowsPerPage($iDisplayLength);
            $flightFilterObj->setOffset($iDisplayStart);
            $dataGettingFlightInfoAndMapping = $flightFilterObj->flightInfoReport();
            if(!empty($dataGettingFlightInfoAndMapping)){
                foreach($dataGettingFlightInfoAndMapping as $key => $preData){
                    $cssClass = '';
                     if ($preData->getStatus() == "not_assigned" && $preData->getCurrentStatus() == "in_tranist") {
                        $cssClass = 'in-transit';
                    } else
                    if ($preData->getStatus() == "in_warehouse") {
                        $cssClass = 'in-warehouse';
                    } else {
                        $cssClass = 'default-color';
                    }
                    $currentArr = array();
                    $status = (!empty($preData->getStatus())?  strtoupper(str_replace("_"," ",$preData->getStatus())):'<label class="label label-warning">N/A</label>');
                    $currentArr['mawb'] = '<input type="hidden" name="row_class" value="'.$cssClass.'" />'.$preData->getMawb();
                    $currentArr['flight'] = $preData->getFlightNumber();
                    $currentArr['status'] = $status;
                    $currentArr['eta'] = $preData->getEta();
                    $currentArr['etd'] = $preData->getEtd();
                    $currentArr['weight'] = $preData->getWeight();
                    $currentArr['pieces'] = $preData->getPieces();
                    $currentArr['account'] = $preData->getUserAccount();
                    $currentArr['comment'] = $preData->getComments();
                    $file = '<label class="label label-warning">N/A</label>';
                    if(!empty($preData->getFiles())){
                        $fileLink = SETTING_MAIN_ASSETS . "preadvice/";
                        $file = '<a class="ebayButton" href="' . $fileLink . $preData->getFiles() . '" target="_blank"><i class="fa fa-download"></i></a>';
                    }
                    $currentArr['file'] = date('d-m-Y',$preData->getDateCreated());
                    
                    $setDataArr[] = $currentArr;
                }
            }
            $setDataArrJson['data'] = (!empty($setDataArr)?$setDataArr:0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            exit;
        }
        
        

    }

    protected function addPagelavelCss() {
        ?>
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
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>  

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
                                "url": "view-pre-alert.php?action=prealert_datatable", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "account", "bSortable": false},
                                {"data": "mawb", "bSortable": false},
                                {"data": "flight", "bSortable": false},
                                {"data": "pieces", "bSortable": false},
                                {"data": "weight", "bSortable": false},
                                {"data": "eta", "bSortable": false},
                                {"data": "etd", "bSortable": false},
                                {"data": "status", "bSortable": false},
                                {"data": "file", "bSortable": false},
                                {"data": "comment", "bSortable": false}
                            ],
                            rowCallback: function (row, data, index) {
                                var cssClass = $('td input', row).val();
                                //$('td',row).removeClass("sorting_1").addClass(cssClass);
                            }
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
                setInterval(function(){
                  grid.getDataTable().ajax.reload();
                },5000);
            });
            
        </script>
        <?php
    }

    protected function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .in-transit{
                color: #000 !important;
                background-color: #FF3535 !important;
            }
            .in-warehouse{
                color: #000 !important;
                background-color: #A3F46C !important;
            }
            .default-color{
                color: #000 !important;
                background-color: #FFB164 !important;
            }
        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $sessionUser = SessionManager::getUser();
        ?>
        <div class="portlet light">	
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-plane"></i>
                    Flight Arrivals
                </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">	
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Account</th>
                                <th>MAWB#</th>
                                <th>Flight#</th>
                                <th>Pieces</th>
                                <th>Weight (Kg)</th>
                                <th>ETD</th>
                                <th>ETA</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>	
            </div>
        </div>	
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
