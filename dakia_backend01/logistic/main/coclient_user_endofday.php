<?php
// get settings
require_once("../includes/settings/config.inc.php");

@session_start;
 
          include_classes([   
                    'manifestdatatfilter.class']);
class Page extends BasePage {

    private $manifestid = "";
    private $list_manifest = "";
    private $user = NULL;
    private $manifestFilter = NULL;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            '' => Translation::GetCaption("MANIFEST_REPORT"),
        );
        // user must be CLIENT
        SessionManager::checkUserAccess(User::PRIVILEGE_IMPORT);
        $this->user = Sessionmanager::getUser();

        $this->manifestid = $_GET["ManifestId"];
        if ($this->user == NULL) {
            util_redirect("../main/index.php");
        }
        t_on();
        // turn on trace for this page  
        //echo $_GET['action'];
        //die;

        if (trim($this->form_vars["func"]) == 'delete_tracking_manifest') {
            $manifestid = $this->form_vars['manifestid'];

            $delete = 'Y';
            $manifest = new Manifest($manifestid);
            $manifest->setIsdeleted($delete);
            $manifest->setAccount($this->user->getAccount());
            $manifest->save();
            $con = new ConsignmentFilter();
            $con->addStatusFilter(Consignment::STATUS_DATAREADY);
//            $con->addServiceCodeArrayFilter($handlingid);
            $con->addWarehouseIdFilter($this->user->getWarehouseId());
            $Consignmentlist = $con->getColumnListLimit('id, awb');

            if (count($Consignmentlist) > 0) {
                $NumberofUniqueCode = 0;
                $manifestArray = array();
                $CurrentConsignment_idArray = array();
                $trackingNumberArray = array();
                foreach ($Consignmentlist as $clist) {
                    $trackingNumberArray[] = $clist->getAwb();
                    $consignmentIdArray[] = $clist->getId();
                    $CurrentConsignment_idArray[] = $clist->getId();
                    $manifestArray[$NumberofUniqueCode]['manifestid'] = $manifestid;
                    $manifestArray[$NumberofUniqueCode]['consignmentid'] = $clist->getId();
                    $NumberofUniqueCode++;
                }
                if (count($manifestArray) > 0) {
                    $ManifestConsignmentMapping = new ManifestConsignmentMapping();
                    $ManifestConsignmentMapping->bulkDataInsert($manifestArray);

                    if (sizeof($consignmentIdArray) > 0) {
                        $ConsignmentFilter = new ConsignmentFilter();
                        $ConsignmentFilter->addIdArrayFilter($consignmentIdArray);
                        $con_list = $ConsignmentFilter->getColumnListLimit("awb, hawb, reference, contact, company, address_line_1, 
									    address_line_2, city,postcode, telephone, number_pieces,
									    weight, description, value, currency, notes, date_booked,
									    date_printed, service_type");

//          $con_list = $ConsignmentFilter->getColumnListLimit("awb, hawb, reference, contact, company, address_line_1, 
//					address_line_2, city, country, postcode, telephone, number_pieces,
//					weight, description, value, currency, notes, date_booked, date_submitted, date_scanned,
//					date_printed, service_type, handling, user_code");
                        if (count($con_list) > 0) {

                            $count = 1;
                            $csv = '';
                            $cr = "\r\n";
                            $uniqueFileName = uniqid();

                            $csvheader = $this->getDispatchHeader();
                            foreach ($con_list as $consignment) {

                                $csv .= $this->getDispatchCSV($consignment, $count);
                                $count++;
                            }

                            $folder_path = "../_assets/manifest_ops";

                            if (!file_exists($folder_path)) {
                                mkdir($folder_path, 0777, true);
                            }

                            $file_path = $folder_path . "/" . $uniqueFileName;
                            $file_path = $file_path . ".csv";

                            $manifest->setFileName($file_path);

//                            $manifest->save();
                            $file_path = fopen($file_path, 'w');
                            fwrite($file_path, $csvheader . $cr . $csv);
                            fclose($file_path);

                            //echo "test" . $dpdlabellink;
                            //print_r($list);

                            $pdf_file_name = ManifestSummaryReport::SavePDFFile($con_list, $manifestid);

                            $manifest->setPdfFile($pdf_file_name);
                            $manifest->save();
                        }
                    }
                }
            }
        }

        if (trim($this->form_vars["func"]) == 'GET_MANIFEST_REPORT') {
            $output = "";
            $manifestid = $this->form_vars['manifestid'];
            $TrackingFilterData = new ManifestDataFilter();
            $TrackingData = $TrackingFilterData->GetTrackingNumberByManifestId($manifestid);
            if (count($TrackingData) > 0) {
                $output .= '<table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Tracking Number</th>
                                            <th>Service Type</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                <tbody>';
                foreach ($TrackingData as $trackinglog) {
                    $output .= '<tr>';
                    $output .= "<td><a href='javascript:;' id='delete_tracking' data-manifestid =" . $manifestid . " class='btn btn-sm btndelete red mt-ladda-btn ladda-button btn-outline'><span class='fa fa-times'></a></td>";
                    $output .= '<td>' . $trackinglog->getawb() . '</td>';
                    $output .= '<td>' . $trackinglog->getServiceType() . '</td>';
                    $output .= '<td>' . date("d-m-Y", strtotime($trackinglog->getCreatedData())) . '</td>';
                    $output .= '</tr>';
                }
                $output .= '</tbody>
                                </table>';
            } else {
                $output .= '';
                $output .= '<div class="alert alert-danger">No Data Found.</div>';
            }
            echo $output;
            exit;
        }

        if (isset($_GET['action']) && $_GET['action'] == "manifestlist_ajax") {
            //$sessionUser = SessionManager::getUser();
            //$this->listAllManifest();
            //SORTING CODE
            $this->manifestFilter = new ManifestDataFilter();
            $this->manifestFilter->addFieldFilter("user_id", $this->user->getId());
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter')
            {
                $searchDateFrom = $this->form_vars['search_Date_from'];
                $searchDateTo = $this->form_vars['search_Date_to'];
                $searchManifestIdcsv = $this->form_vars['manifestidcsv'];
                $searchManifestIdpdf = $this->form_vars['manifestidpdf'];
                if (!empty($searchDateFrom) || !empty($searchDateTo)) {
                    $this->manifestFilter->addFromAndToDateFilter($searchDateFrom, $searchDateTo);
                }
                if (!empty($searchManifestIdcsv)) {
                    $this->manifestFilter->addIdFilter($searchManifestIdcsv);
                }
                if (!empty($searchManifestIdpdf)) {
                    $this->manifestFilter->addIdFilter($searchManifestIdpdf);
                }
                //$this->manifestFilter->addFieldFilter("user_id", $this->user->getId());	
                //$manifestFilter->addAccountFilter($user->getAccount());
                if (isset($_GET['collection_id']) && $_GET['collection_id'] > 0) {
                    $this->manifestFilter->addFilter(" and pickup_id = '" . $_GET['collection_id'] . "'");
                }

                //print_r($this->manifestFilter);
            //    $this->list_manifest = $this->manifestFilter->getColumnList("id, file_name, pdf_file, date_created, handling, pieces, product");
            }
            /*
             * Set pagination & Encode data into Json form to return to DataTable
             */
            $this->manifestFilter->addOrderById();
            $iTotalRecordsArray = $this->manifestFilter->getPagingCount();
            $iTotalRecords = (!empty($iTotalRecordsArray[0])?$iTotalRecordsArray[0]->getId():'0');
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->manifestFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->manifestFilter->setOffset($iDisplayStart);
            $manifest_List = $this->manifestFilter->getPagingList();
            $manifestDataArr = array();
            foreach ($manifest_List as $manifest) {
                $manifestData['actions'] = '';
                $manifestData['actions'] = "";
                //$manifestData['actions'] .= "<div class='margin-bottom-5'><a data-id =" . $manifest->getId() . " class='btnedit btn-sm blue btn mt-ladda-btn ladda-button btn-outline'><span class='fa fa-pencil'></span> </a></div>";
                //$manifestData['actions'] .= "<a href='#' data-id =" . $manifest->getId() . " onclick='return confirm('Are you sure you want to delete?')' class='btn btn-sm btndelete red mt-ladda-btn ladda-button  btn-outline'><span class='fa fa-times'></a>";
                //                $warehouseArr['option'] = '<input type=checkbox id= "delete55" name="deletewarehouse[]" value="' . $warehouse->getId() . '" />';
                $manifest_created = formatDate(date("d-m-Y", $manifest->getDateCreated()));
                if ($manifest_created == '01.01.1970' || $manifest_created == '')
                    $manifestData['date'] = formatDate(date("d-m-Y", strtotime($manifest->getDateCreated())));
                else
                    $manifestData['date'] = $manifest_created;
                //$manifestData['date'] = date("d.m.Y", strtotime($manifest->getDateCreated())); 
                if ($this->user->getIsProduct() == "YES")
                    $manifestData['product'] = "";//trim($manifest->getProduct());
                else
                    $manifestData['product'] = "";//trim($manifest->getHandling());
                $manifestData['noofshipments'] = $manifest->getPieces();
                $manifestData['manifestno'] = $manifest->getId();
                $manifestData['viewshipments'] = "<a class='btn blue btn-outline btn-xs' href='client_list.php?ManifestId=" . $manifest->getId() . "'>"
                        . "                             View Shipments</a>"
                        . "
                                <a target='_blank'  class='btn blue btn-outline btn-xs' href='../_assets/manifest/csv/" . $manifest->getFileName() . "'>CSV</a>
                                <a target='_blank'  class='btn blue btn-outline btn-xs' href='../_assets/manifest/pdf/" . $manifest->getPdfFile() . "'>PDF</a>";
                $manifestDataArr[] = $manifestData;
            }
            
            $manifestDataArr['data'] = $manifestDataArr;
            $manifestDataArr['draw'] = $sEcho;
            $manifestDataArr['recordsTotal'] = $iTotalRecords;
            $manifestDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($manifestDataArr);
            die;
            //Foreach
            //ECHO json DATA
        }
    }

    private function getDispatchHeader() {
        $header_row = array(
            "Sr.",
            "HAWB",
            "REF",
            "Date Dispatched",
            "Company",
            "Contact",
            "Address Line 1",
            "Address Line 2",
            "City",
            "Postcode",
            "Telephone",
            "Number of Pieces",
            "Weight",
            "Description",
            "Value",
            "Currency",
            "Notes",
            "Tracking Number"
        );

        foreach ($header_row as $field) {
            $record .= $field . ",";
        }
        return $record;
    }

    private function getDispatchCSV($consignment, $count) {

        $csv .= $count . ",";
        $csv .= cleanCsvCall($consignment->getHawb()) . ",";
        $csv .= $this->cleanData($consignment->getReference()) . ",";
        $csv .= date("d-m-Y", strtotime($consignment->getDateBooked())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getCompany())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getContact())) . ",";
        $csv .= cleanCsvCall($consignment->getAddressLine1()) . ",";
        $csv .= cleanCsvCall(trim($consignment->getAddressLine2())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getCity())) . ",";
//        $csv .= $this->cleanData(trim($consignment->getCountry())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getPostCode())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getTelephone())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getNumberPieces())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getWeight())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getDescription())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getValue())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getCurrency())) . ",";
        $csv .= cleanCsvCall(trim($consignment->getNotes())) . ",";
        $csv .= cleanCsvCall($consignment->getAwb(),'int'). ",";
        //$csv  .=  $consignment->getAwb() . ",";
        $csv .= "\r\n";
        return $csv;
    }

    private function cleanData($str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        $str = preg_replace('/[\$,]/', '', $str);
        return $str;
    }

    /**
     * Force page refresh if importing
     */
    public function renderHead() {
        ?>	
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="portlet light">   
            <div class="portlet-title">                
                <div class="caption"> <i class="icon-list"></i>
                    <?php echo Translation::GetCaption("MANIFEST_REPORT") ?>
                </div>
                <div class="actions">
                    <a href="client_list.php<?php echo (util_get('uaccount') != '' ? "?uaccount=" . util_get('uaccount') : '') ?>" class="btn blue ">
                        <i class="fa fa-list"></i> List </a>


                    <a href="javascript:;" class="collapse btn btn-circle btn-icon-only btn-default hidden" data-original-title="" title=""> </a>
                    <a href="" class="btn btn-circle btn-icon-only btn-default fullscreen hidden" data-original-title="" title=""> </a>
                    <a href="#portlet-config" data-toggle="modal" class="btn btn-circle btn-icon-only btn-default hidden"><i class="icon-wrench"></i></a>

                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="5%"></th>
                                <th width="15%"><?php echo Translation::GetCaption("DATE"); ?></th>
                                <th width="15%"><?php echo Translation::GetCaption("Number of Parcels"); ?></th>
                                <th width="15%">Manifest No</th>
                                <th width="15%"><?php echo Translation::GetCaption("VIEW_SHIPMENTS"); ?></th>

                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn-sm filter-submit margin-bottom btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                    </div>
                                    <button class="btn-sm red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                </td>                           
                                <td rowspan="1" colspan="1">
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter input-sm" readonly="" name="search_Date_from" placeholder="From" data-original-title="" title="">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter input-sm" readonly="" name="search_Date_to" placeholder="To" data-original-title="" title="">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="edit-manifest-report-popup" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close modal-close" data-dismiss="modal"  aria-label="Close" aria-hidden="true"></button>
                        <h4 class="modal-title"><span id="product_name_logs"></span>Edit Manifest</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 hidden" id="successmsg">
                                <div class="alert alert-success" id="success_msg"> Record has been Deleted Successfully .</div>
                            </div>
                            <div class="col-md-12" id="edit-manifest-report-display">

                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default modal-close" id="close_manifest_report_popup" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>

        <?php
    }

    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

    public function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $(document).on('click', '#delete_tracking', function () {
                    var e = $(this);
                    var manifestid = e.data('manifestid');
                    var action = 'delete_tracking_manifest';
                    var url = 'coclient_user_endofday.php';
                    $.post(url, {func: action, manifestid: manifestid}, function (d) {
                        $("div").removeClass("hidden");
                        $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                        $('#manage-data-table').DataTable().ajax.reload();
                    });
                });

                $(document).on('click', '.edit-manifest-report', function () {
                    var e = $(this);
                    var manifestid = e.data('manifestid');
                    var url = e.data('manifestload');
                    var action = e.data('action');
                    $.post(url, {func: action, manifestid: manifestid}, function (d) {
                        $("#edit-manifest-report-display").html(d);
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
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here 
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "coclient_user_endofday.php?action=manifestlist_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "date"},
                                {"data": "noofshipments"},
                                {"data": "manifestno"},
                                {"data": "viewshipments"}
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
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $("#btnSearch").click(function ()
                {
                    $("#form_action").val("Search");
                    $("#adminForm").submit();
                });
                $("#btnBook").click(function () {
                    if (confirm('<?= Translation::GetCaption("BULK_PROCESSING_MESSAGE"); ?>') == true)
                    {
                        $("#form_action").val("BookAll");
                        $("#adminForm").submit();
                    } else
                    {
                        return false;
                    }
                });
                $("#btnView").click(function () {
                    $("#form_action").val("BookSelected");
                    $("#adminForm").submit();
                });
                $("#btnCancel").click(function () {
                    $("#form_action").val("Cancel");
                    $("#adminForm").submit();
                });
            });
            function noSpeciatCharacter(e)
            {
                var unicode = e.charCode ? e.charCode : e.keyCode
                //alert(unicode);
                if (unicode != 8)
                {
                    if ((unicode == 31) || (unicode >= 33 && unicode <= 35) || (unicode >= 39 && unicode <= 43) || (unicode >= 36 && unicode <= 38) || unicode == 163 || unicode == 94 || unicode == 64 || unicode == 126) //if not a number
                        return false //disable key press
                }
            }



        </script>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
