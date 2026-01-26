<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'invoices.class',
    'invoicesfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'mawb.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    'parcel.class',
    'consignment.class',
    'services.class',
]);

class Page extends BasePage
{

    private $user;
    private $wareHouseId;
    private $user_id;

    /*     * *
     * Controller logic
     */

    protected function init()
    {

        // Session check
        $this->user = SessionManager::getUser();
//        if ($this->user->getUserType() != User::USER_TYPE_CORPORATE && $this->user->getUserType() != User::USER_TYPE_ADMIN ) {
//            util_redirect("index.php");
//        }
        //BreadCrum
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            '#' => 'Flight and Scanning Status',
            'mawb_scan_report.php' => "MAWB SCANNING"
        );
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_current_mawb") {
            $html = "";
            //Get todays scanned MAWB
            if (isset($this->form_vars['date']) && !empty($this->form_vars['date'])) {
                $curDate = DateTime::createFromFormat('d-m-Y', $this->form_vars['date']);
                $curDate = $curDate->format("Y-m-d");
            } else {
                $curDate = date('Y-m-d');
            }
            $dataWareHouse = getLoggedInUserChildWarehouse();
            if (!empty($dataWareHouse)) {
                foreach ($dataWareHouse as $data) {
                    $this->wareHouseId[] = $data->getId();
                }
            }
            $this->user_id = $this->user->getId();
//            $CurDate = "2018-08-02";
            //$consignmentFilter = new ConsignmentFilter();
            $consignmentFilter = new MawbParcelMappingFilter();
            $curMawbObj = $consignmentFilter->getTodayMawbNo($curDate, $this->wareHouseId, $this->user->getId());
            $count = 1;
            //$totalbags = [];
            $bagIdCount = 0;
            if (count($curMawbObj) > 0) {
                foreach ($curMawbObj as $curMawb) {
                    if ($count % 2 == 0) {
                        $class = "info";
                    } else if ($count % 3 == 0) {
                        $class = "warning";
                    } else {
                        $class = "success";
                    }
                    $mawb = new Mawb($curMawb->getMawbId());
                    $count++;
                    $html .= '<tr>
                            <td class="highlight">
                                <div class="' . $class . '"></div>
                                <a class="mawb_details" href="javascript:;" id="' . $curMawb->getMawbId() . '"> ' . $mawb->getMawbNumber() . ' </a>
                            </td>
                            <td class="hidden-xs"> ' . date("d-m-y") . ' </td>
                        </tr>';
                }
            } else {
                $html .= '<tr>
                            <td class="highlight" colspan="2">
                                <div class="warning"></div> 
                               <a href="javascript:;"> No record found </a>
                            </td>
                        </tr>';
            }
            echo $html;
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_parcel_bag_details") {
            $mawbNo = $this->form_vars['mawb'];
            if (!empty($mawbNo)) {
                //       Get Total Bags from MAWB number
                $this->wareHouseId = $this->user->getWarehouseId();
                $totalBag = [];
                $totalScannedBag = 0;
                $totalParcel = 0;
                $totalScannedParcel = [];
                $totalScannedParcelCount = 0;
                $totalUnsignedParcel = 0;
                $totalScannedUnsignedParcel = 0;
                $bagId = "";
                $assignedBagArr = [];
                $unassignedParcelArr = [];
                $allScannedParcelArr = [];
                $allConsignmentServiceArr = [];
                $html = "";
                $userServiceHtml = "";

                $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                $mawbObj = $mawbParcelMappingFilter->getAllDataOfMawbParcel($mawbNo, $this->wareHouseId);
                if (count($mawbObj) > 0) {
                    foreach ($mawbObj as $mawbData) {
                        $bagId = $mawbData->getBagId();

                        // Get scanned parcel From parcel
                        $trackingDataFilter = new TrackingDataFilter();
                        $trackingDataFilter->addFieldFilter('   entity_id', $mawbData->getParcelId());
                        $trackingDataFilter->addFieldFilter('   entity_type', 'parcel');
                        $trackingDataFilter->addFilterIn('   warehouse_id', $this->wareHouseId);
                        $trackingDataFilter->addFieldFilter('   status_code_id', '146');
                        $trackingDataobj = $trackingDataFilter->getColumnList('*');
                        if (count($trackingDataobj) > 0) {
                            $allScannedParcelObjArr[] = $trackingDataobj[0]->getEntityId();
                            // Get all consignment ids from parcel list
                            $parcel = new Parcel($trackingDataobj[0]->getEntityId());
                            $consignment = new Consignment($parcel->getConsignmentId());
                            $allConsignmentServiceArr[$consignment->getServiceId()][$trackingDataobj[0]->getUserId()][] = ['scanned' => strtotime($trackingDataobj[0]->getDateCreated()), "parcel_id" => $trackingDataobj[0]->getEntityId()];

                            usort($allConsignmentServiceArr[$consignment->getServiceId()][$trackingDataobj[0]->getUserId()], function ($a, $b) {
                                return $a['scanned'] - $b['scanned'];
                            });
                        }
                        //Check if bag is not null or empty that means parcel is assigned otherwise bag is unsigned
                        if (!empty($bagId)) {
                            $totalBag[] = $bagId;
                            $assignedBagArr[$bagId][] = $mawbData->getParcelId();
                            $totalParcel++;
                            if (count($trackingDataobj) > 0) {
                                $totalScannedParcel[$bagId][] = $mawbData->getParcelId();
                                $totalScannedParcelCount++;
                            }
                        } else {
                            // make array of parcel that was not assigned
                            $unassignedParcelArr[] = $mawbData->getParcelId();
                            if (count($trackingDataobj) > 0) {
                                $totalScannedUnsignedParcel++;
                            }
                        }
                    }
                }
                // Remove duplication from bag arr and parcel arr
                $totalBag = count(array_unique($totalBag));
                $totalUnsignedParcel = count(array_unique($unassignedParcelArr));
                // Get scanned parcel unique array
                $allScannedParcelObjArr = array_unique($allScannedParcelObjArr);

                // Check if page is completed scanned
                foreach ($totalScannedParcel as $bagId => $unassignedParcel) {
                    $bagParcelScannedCount = count($totalScannedParcel[$bagId]);
                    $parcelScannedCount = count($assignedBagArr[$bagId]);
                    if ($bagParcelScannedCount == $parcelScannedCount) {
                        $totalScannedBag++;
                    }
                }
                // Assign values to variable
                $scanbagTotal = $totalScannedBag;
                $remaningBag = $totalBag - $scanbagTotal;

                $remaningParcel = ($totalParcel + $totalUnsignedParcel) - ($totalScannedParcelCount + $totalScannedUnsignedParcel);
                $html .= '<tr>
                    <td class="active"> DUE IN </td>
                    <td class="success"> ' . $totalBag . ' </td>
                    <td class="active"> DUE IN BAG </td>
                    <td class="danger"> ' . $totalParcel . ' </td>
                </tr>
                <tr>
                    <td class="active"> SCANNED </td>
                    <td class="success"> ' . $scanbagTotal . ' </td>
                    <td class="active"> SCANNED IN BAG </td>
                    <td class="danger"> ' . $totalScannedParcelCount . ' </td>
                </tr>
                <tr>
                    <td  class="active">  </td>
                    <td  class="success">  </td>
                    <td  class="active"> UNASSIGNED DUE IN </td>
                    <td  class="danger"> ' . $totalUnsignedParcel . '  </td>
                </tr>
                <tr>
                    <td  class="active">  </td>
                    <td  class="success">  </td>
                    <td  class="active"> SCANNED UNASSIGNED </td>
                    <td  class="danger"> ' . $totalScannedUnsignedParcel . '  </td>
                </tr>
                <tr>
                    <td class="active"> REMAIN </td>
                    <td id="bag_rem" class="success"> ' . $remaningBag . ' </td>
                    <td class="active"> REMAIN </td>
                    <td id="parcel_rem" class="danger"> ' . $remaningParcel . '  </td>
                </tr>
                ';
                // Get parcel objct array
                foreach ($allConsignmentServiceArr as $serviceId => $userData) {
                    $service = new Services($serviceId);
                    $serviceName = $service->getName();
                    foreach ($userData as $userId => $userDetail) {
                        $countArrScanned = count($userDetail);
                        $user = new User($userId);
                        $userName = $user->getUserName();
                        if ($countArrScanned > 1) {
                            $firstScan = $userDetail[0]['scanned'];
                            $lastScan = $userDetail[$countArrScanned - 1]['scanned'];
                        } else {
                            $firstScan = $userDetail[0]['scanned'];
                            $lastScan = $userDetail[0]['scanned'];
                        }

                    }

                    $datetime1 = new DateTime(date('d-m-Y H:i:s', $firstScan));//start time
                    $datetime2 = new DateTime(date('d-m-Y H:i:s', $lastScan));//end time
                    $interval = $datetime1->diff($datetime2);
                    $userServiceHtml .= "<tr>
                                            <td>" . $userName . "</td>
                                            <td>" . $serviceName . "</td>
                                            <td>" . formatDateTime(date('d-m-Y H:i:s', $firstScan)) . "</td>
                                            <td>" . formatDate(date('d-m-Y H:i:s', $lastScan)) . "</td>
                                            <td>" . $interval->format('%Y years %m months %d days %H hours %i minutes %s seconds') . "</td>
                                            <td>" . $countArrScanned . "</td>
                                            <td>" . $service->getCutOffTime() . "</td>
                                            
                                    </tr>";
                }

                echo $html . ":::" . $userServiceHtml;
            } else {
                $html = "";
            }
            die;
        }
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
                getMawbByDate("");
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                setTimeout(function () {
                    location.reload();
                }, 30000);
            });
            $(document).on('click', '#search_from_date', function () {
                var from_date = $("#from_date").val();
                getMawbByDate(from_date);
            });
            $(document).on('click', '.mawb_details', function () {
                var mawb_no = $(this).attr('id');
                getParcelBagDetails(mawb_no);
            });

            function getParcelBagDetails(mawb_number) {
                $(".mawb_number_show").html(mawb_number);
                $.ajax({
                    type: "POST",
                    url: "mawb_scan_report.php",
                    data: {action: "get_parcel_bag_details", mawb: mawb_number},
                    dataType: "html",
                    success: function (data) {
                        $("#bag_detail").html(" ");
                        var returnData = data.split(":::");
                        $("#bag_detail").append(returnData[0]);
                        $("#mawb_table_detail").html(" ");
                        $("#mawb_table_detail").append(returnData[1]);
                        var bag_rem = parseInt($.trim($("#bag_rem").text()));
                        var parcel_rem = parseInt($.trim($("#parcel_rem").text()));
                        if (bag_rem > 0 || parcel_rem > 0) {
                            $("#scanned_in_process").show();
                        } else if (bag_rem == 0 || parcel_rem == 0) {
                            $("#scanned_comleted").show();
                        } else {
                            $("#scanned_comleted").hide();
                            $("#scanned_in_process").hide();
                        }
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }

            function getMawbByDate(date) {
                //Send Ajac request to fetch currently scanned MAWB
                if (date == "")
                    date = "";
                $.ajax({
                    type: "POST",
                    url: "mawb_scan_report.php",
                    data: {action: "get_current_mawb", date: date},
                    dataType: "html",
                    success: function (data) {
                        $("#cur_mawb").html("");
                        $("#cur_mawb").append(data);
                        var mawb_number = $("#cur_mawb").find("td:first a").attr('id');
                        getParcelBagDetails(mawb_number);
                    },
                    error: function () {
                        //                        alert('error handing here');
                    }
                });
            }
        </script>
        <?php
    }

    protected function renderHead()
    {
        ?>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
//        $id = util_get_num("id");
        // transfer form variables into local values (form variables come from parent)
//        foreach ($this->form_vars as $key => $val) {
//            $$key = $val;
//        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="input-group col-md-2 pull-right">
                    <input title="select date" placeholder="select date" data-date-format="dd-mm-yyyy"
                           data-original-title="select date" class="form-control date-picker" size="16" type="text"
                           id="from_date" name="from_date" value="">
                    <span class="input-group-btn">
                        <button id="search_from_date" type="button" class="btn  green"> <i
                                    class="icon-magnifier"></i></button>
                    </span>
                </div>
                <div class="caption"><i class="icon-bar-chart"></i>
                    MAWB Scanning
                </div>
                <div class="actions">

                </div>
                <div class="tools">

                </div>
            </div>
            <div class="portlet-body">
                <div class="row" id="show_general_msg" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="caption margin-bottom-10 block">
                            <span class="caption-subject bold uppercase">Mawb Currently being Scanned</span>
                        </div>
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-advance table-hover">
                                <thead>
                                <tr>
                                    <th>
                                        <i class="fa fa-briefcase"></i> MAWB
                                    </th>
                                    <th class="hidden-xs">
                                        <i class="fa fa-user"></i> DATE
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="cur_mawb">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="caption margin-bottom-10 block">
                            <span class="caption-subject bold uppercase">MAWB Number: <span
                                        class="mawb_number_show"></span> &nbsp;&nbsp;<span style="display: none;"
                                                                                           id="scanned_comleted"
                                                                                           class="label label-sm label-success"> <strong>Completed</strong> </span>&nbsp;&nbsp;&nbsp;<span
                                        style="display: none;" id="scanned_in_process"
                                        class="label label-sm label-warning"> <strong>In Process</strong> </span></span>
                        </div>
                        <div class="table-scrollable">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th colspan="2"> CARTONS SCAN</th>
                                    <th colspan="2"> ITEM SCANNED</th>
                                </tr>
                                </thead>
                                <tbody id="bag_detail">
                                <!--if all scanned then add success class if remaning add warning class-->
                                <!--                                    <tr>
                                                                <td> 1 </td>
                                                                <td class="active"> active </td>
                                                                <td class="success"> success </td>
                                                                <td class="warning"> warning </td>
                                                                <td class="danger"> danger </td>
                                                            </tr>-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="caption margin-bottom-10 block">
                            <span class="caption-subject bold uppercase">MAWB Number: <span
                                        class="mawb_number_show"></span> Detail</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th> User Scanned List</th>
                                    <th> Service</th>
                                    <th> First Scan</th>
                                    <th> Last Scan</th>
                                    <th> Time</th>
                                    <th> Total Scanned</th>
                                    <th> Target Time</th>
                                </tr>
                                </thead>
                                <tbody id="mawb_table_detail">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!--portlet-body-->
        </div>
        <?php
    }

    public function renderFooter()
    {
        ?>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

function getFirstLastRec($date_arr)
{
    $htmlRet = "";
    usort($date_arr, function ($a, $b) {
        $dateTimestamp1 = $a;
        $dateTimestamp2 = $b;
        return $dateTimestamp1 < $dateTimestamp2 ? -1 : 1;
    });

    $htmlRet .= '<td>';
    $htmlRet .= formatDateTime(date('d-m-Y H:i:s', $date_arr[0]));
    $htmlRet .= '</td>';

    $htmlRet .= '<td>';
    $htmlRet .= formatDateTime(date('d-m-Y H:i:s', $date_arr[count($date_arr) - 1]));
    $htmlRet .= '</td>';

    $start = new DateTime(date('Y-m-d H:i:s', $date_arr[0]));
    $date2 = $start->diff(new DateTime(date('Y-m-d H:i:s', $date_arr[count($date_arr) - 1])));
    $htmlRet .= '<td>';
    if (!empty($date2->y))
        $htmlRet .= $date2->y . ' years ';
    if (!empty($date2->m))
        $htmlRet .= $date2->m . ' months ';
    if (!empty($date2->d))
        $htmlRet .= $date2->d . ' days ';
    if (!empty($date2->h))
        $htmlRet .= $date2->h . ' hours ';
    if (!empty($date2->i))
        $htmlRet .= $date2->i . ' minutes ';
    if (!empty($date2->s))
        $htmlRet .= $date2->s . ' seconds ';
    $htmlRet .= '</td>';

    return $htmlRet;
}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
