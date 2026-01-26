<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'carrierservice.class',
], 'general');

include_classes([
    'yodel.class',
    'yodeltrackingstatus.class'
], 'labels');
include_classes([
    'tracking.class',
    'iaddress.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'carrier.class',
    'carrierfilter.class',
    'services.class',
    'servicesfilter.class',
    'servicecountrytime.class',
    'consignment.class',
    'servicecountrytimefilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'serviceconstant.class',
    'serviceconstantfilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'consignmentrelabelfilter.class',
    'consignmentrelabel.class',

]);

class Page extends BasePage
{
    private $trackingResults;
    private $shipmentDetail;
    private $trackingEvents;
    private $trackingStatus;
    private $trackingStatusDescription;
    private $imageLink;
    private $sorterImage;
    private $trackingData;

    /*     * *
     * Controller logic
     */
    protected function init()
    {
//        $trackingObj = new Tracking(); //  Calling the constructor
        //$this->trackingResults = $trackingObj->GetTracking($trackingNumber);
        // print_r($this->trackingResults); die;
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'delete_tracking_event') {
            $html = '';
            if ($this->form_vars['tracking_data_id']) {
                $trackingDataObj = new TrackingData($this->form_vars['tracking_data_id']);
                $trackingDataObj->delete();
                if (!empty($this->form_vars['tracking_numbers'])) {
                    $i = 0;
                    foreach ($this->form_vars['tracking_numbers'] as $datum) {
                        $this->trackingData[$i]['tracking_number'] = $datum;
                        $trackingNumber = cleanTrackingNo($datum);
                        $trackingObj = new Tracking(); //  Calling the constructor
                        $this->trackingResults = $trackingObj->trackingDataEvents($trackingNumber);
                        if (!empty($this->trackingResults)) {
                            $this->trackingData[$i]['tracking_events'] = $this->trackingResults;
                        } else {
                            $this->trackingData[$i]['tracking_events'] = [];
                        }
                        $i++;
                    }
                    if (!empty($this->trackingData)) {
                        foreach ($this->trackingData as $trackingDatum) {
                            $html .= '<table class="table table-striped table-bordered table-advance table-hover">
                        <thead>
                        <tr>
                            <th colspan="5">
                                <div class="caption">
                                    <i class=" icon-layers"></i>
                                    <span class="caption-subject bold uppercase">Tracking Events (' . $trackingDatum['tracking_number'] . ')</span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>';
                            if (count($trackingDatum['tracking_events']) > 0) {
                                $heading = true;
                                foreach ($trackingDatum['tracking_events'] as $eventDate => $trackingEvent) {
                                    $html .= '<tr>
                                    <th colspan="' . ($heading === true ? 2 : 5) . '">' . date('l, F d, Y', strtotime($eventDate)) . '</th>';
                                    if ($heading === true) {
                                        $html .= '<th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>';
                                    }
                                    $html .= '</tr>';
                                    foreach ($trackingEvent as $eventDetail) {
                                        $html .= '<tr>
                                        <td style="width: 10px;">' . $eventDetail['time_12_hr'] . '</td>
                                        <td style="width: 25%;">' . $eventDetail['track_point'] . '</td>
                                        <td style="width: 55%;"> ' . $eventDetail['carrier_desc'] . ($eventDetail['signatory'] != '' ? ' ' . $eventDetail['signatory'] : '') . ($eventDetail['parcel_image'] != '' ? ' ' . "<a href='" . $eventDetail['parcel_image'] . "' class='btn btn-xs blue' target='_blank' > Parcel Image</a>" : '') . ($eventDetail['pod_image'] != '' ? '  ' . "<a href='" . $eventDetail['pod_image'] . "' class='btn btn-xs blue' target='_blank' > Signature Image </a>" : '') . '</td>
                                        <td style="width: 10%;">' . $eventDetail['event_content'] . '</td>';
                                        if (!empty($eventDetail['user_id'])) {
                                            $html .= '<td width="30"><a href="javascript:;" data-tracking_data_id="' . $eventDetail['tracking_data_id'] . '" class="btn btn-danger delete-tracking-event btn-xs"><i class="fa fa-trash"></i></a></td>';
                                        } else {
                                            $html .= '<td width="30"></td>';
                                        }
                                        $html .= '</tr>';
                                        $heading = false;
                                    }
                                }
                            } else {
                                $html .= '<tr>
                                <td colspan="4">
                                    No Tracking data available.
                                </td>
                            </tr>';
                            }
                            $html .= '</tbody></table>';
                        }
                    }
                    $data = ['status' => true, 'message' => 'Tracking event deleted successfully.', 'html' => $html];
                } else {
                    $html .= '<p>No Tracking available.</p>';
                    $data = ['status' => true, 'message' => 'Tracking event deleted successfully.', 'html' => $html];
                }
            } else {
                $html .= '<p>No Tracking available.</p>';
                $data = ['status' => false, 'message' => 'Tracking Event not found.', 'html' => $html];
            }
            echo json_encode($data);
            exit;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_tacking_data') {
            $html = '';
            if (!empty($this->form_vars['tracking_numbers'])) {
                $i = 0;
                foreach ($this->form_vars['tracking_numbers'] as $datum) {
                    $this->trackingData[$i]['tracking_number'] = $datum;
                    $trackingNumber = cleanTrackingNo($datum);
                    $trackingObj = new Tracking(); //  Calling the constructor
                    $this->trackingResults = $trackingObj->trackingDataEvents($trackingNumber);
                    if (!empty($this->trackingResults)) {
                        $this->trackingData[$i]['tracking_events'] = $this->trackingResults;
                    } else {
                        $this->trackingData[$i]['tracking_events'] = [];
                    }
                    $i++;
                }
                if (!empty($this->trackingData)) {
                    foreach ($this->trackingData as $trackingDatum) {
                        $html .= '<table class="table table-striped table-bordered table-advance table-hover">
                        <thead>
                        <tr>
                            <th colspan="5">
                                <div class="caption">
                                    <i class=" icon-layers"></i>
                                    <span class="caption-subject bold uppercase">Tracking Events (' . $trackingDatum['tracking_number'] . ')</span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>';
                        if (count($trackingDatum['tracking_events']) > 0) {
                            $heading = true;
                            foreach ($trackingDatum['tracking_events'] as $eventDate => $trackingEvent) {
                                $html .= '<tr>
                                    <th colspan="' . ($heading === true ? 2 : 5) . '">' . date('l, F d, Y', strtotime($eventDate)) . '</th>';
                                if ($heading === true) {
                                    $html .= '<th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>';
                                }
                                $html .= '</tr>';
                                foreach ($trackingEvent as $eventDetail) {
                                    $html .= '<tr>
                                        <td style="width: 10px;">' . $eventDetail['time_12_hr'] . '</td>
                                        <td style="width: 20%;">' . $eventDetail['track_point'] . '</td>
                                        <td style="width: 55%;"> ' . $eventDetail['carrier_desc'] . ($eventDetail['signatory'] != '' ? ' ' . $eventDetail['signatory'] : '') . ($eventDetail['parcel_image'] != '' ? ' ' . "<a href='" . $eventDetail['parcel_image'] . "' class='btn btn-xs blue' target='_blank' > Parcel Image</a>" : '') . ($eventDetail['pod_image'] != '' ? '  ' . "<a href='" . $eventDetail['pod_image'] . "' class='btn btn-xs blue' target='_blank' > Signature Image </a>" : '') . '</td>
                                        <td style="width: 10%;">' . $eventDetail['event_content'] . '</td>';
                                    if (!empty($eventDetail['user_id'])) {
                                        $html .= '<td width="30"><a href="javascript:;" data-tracking_data_id="' . $eventDetail['tracking_data_id'] . '" class="btn btn-danger delete-tracking-event btn-xs"><i class="fa fa-trash"></i></a></td>';
                                    } else {
                                        $html .= '<td width="30"></td>';
                                    }
                                    $html .= '</tr>';
                                    $heading = false;
                                }
                            }
                        } else {
                            $html .= '<tr>
                                <td colspan="4">
                                    No Tracking data available.
                                </td>
                            </tr>';
                        }
                        $html .= '</tbody></table>';
                    }
                }
                echo json_encode($html);
                exit;
            } else {
                $html .= '<p>No Tracking available.</p>';
//                $data = ['status' => false, 'msg' => '<p>No Tracking available.</p>'];
                echo json_encode($html);
                exit;
            }
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected
    function renderHead()
    {

    }

    /*     * *
     * Content View
     */

    protected
    function renderBody()
    {
        //echo "<pre>"; print_r($this->trackingResults); echo "</pre>";
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-upload"></i>
                   Parcel tracking
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="label-account"><?php echo Translation::GetCaption("MUTLTITRACKING_ENTER"); ?></label>
                        <div class="form-group tracking_type_field">
                            <div class="input-group"><span class="input-group-addon"> <i
                                            class="fa fa-road"></i> </span>
                                <textarea id="resnums" name="nums"
                                          placeholder="Tracking Number" rel="tooltip"
                                          data-original-title="Tracking" class="form-control form-filter"
                                          style="resize: none;height: 80px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="text-align:center;">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary filter-submit start-tracking" name="btn_go"
                                    id="btn_go" value="Search"> <?php echo Translation::GetCaption("TRACK"); ?>
                            </button>&nbsp;
                            <button type="button" class="btn btn-default filter-cancel" name="btn_rest"
                                    value="Reset" onclick="resetForm()"> Reset
                            </button>
                        </div>
                    </div>
                </div>
                <div class="alert alert-danger" style="display: none;"></div>
                <div class="alert alert-success" style="display: none;"></div>
                <div class="tracking-data-show">
                    <p>
                        No Tracking available.
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param none
     */

    /**
     * Return to source page
     * @param $filter_set
     */
    public
    function renderMenu()
    {
        if (isset($_SESSION['admin'])) {
            $menu = new Adminmenu(Adminmenu::COURIERS);
            $menu->render();
        }
    }

    protected
    function addPagelavelCss()
    {
        ?>
        <link href="../assets/layouts/layout4/css/multitrack.css" rel="stylesheet">
        <style type="text/css">
            .page-breadcrumb {
                display: none
            }

            .icon-status-box {
                text-align: center;
                padding: 3em 0 0 0;
                width: 100%
            }

            .icon-status-box .fa {
                font-size: 3em;
                text-align: center;
                color: #26C281;
            }

            @media (min-width: 992px) {
                .page-content-wrapper .page-content {

                    padding-top: 0px !important;
                }
            }

            .page-header.navbar .page-logo .logo-default {
                margin: 17px 10px 0 !important;
                max-width: 180px !important;
                max-height: 43px !important;
            }

            .page-sidebar-hide {
                margin-left: 0px !important;;
                padding-left: 0px !important;
            }

            .dashboard-stat2 h3 {
                font-size: 24px !important;
            }

        </style>
        <?php
    }

    protected
    function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-knob-dials.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                var record_limit = parseInt('<?= (isset($_SESSION['menu-option']) && ($_SESSION['menu-option'] == User::USER_TYPE_CUSTOMER_SERVICE)) ? 500 : 500 ?>');
                /*                    $(".page-content-wrapper > .page-content").addClass('page-sidebar-hide');
                                    $(".sidebar-toggler").hide();*/
                $(".start-tracking").click(function () {
                    if ($.trim($("#resnums").val()) == '') {
                        alert("<?= Translation::GetCaption("MULTITRACKING_ERROR"); ?>");
                    } else {
                        var tracking_numbers = $("#resnums").val().split("\n");
                        var html = '';
                        $.ajax({
                            url: 'tracking_data.php',
                            data: {
                                tracking_numbers,
                                action: 'get_tacking_data'
                            },
                            type: 'POST',
                            dataType: 'json',
                            success: function (response) {
                                $('.tracking-data-show').html(response);
                            }
                        });
                        //alert(totalNumberValue.length);
                        /*if (totalNumberValue.length > record_limit)
                            alert("You are not allowed to track more than " + record_limit + " number at one go.");
                        else
                            $("#resTrackfrm").submit();*/
                    }
                });
                $('body').on('click', '.delete-tracking-event', function () {
                    var tracking_data_id = $(this).data("tracking_data_id");
                    var tracking_numbers = $("#resnums").val().split("\n");
                    swal({
                            title: "Are you sure you want to delete tracking event ?",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $.ajax({
                                    url: 'tracking_data.php',
                                    data: {
                                        tracking_data_id: tracking_data_id,
                                        tracking_numbers,
                                        action: 'delete_tracking_event'
                                    },
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function (response) {
                                        $(window).scrollTop(0);
                                        if (response.status == true) {
                                            $('.alert-success').show().html(response.message);
                                            $('.alert-danger').hide()
                                        } else if (response == false) {
                                            $('.alert-success').hide();
                                            $('.alert-danger').show().html(response.message);
                                        }
                                        $('.tracking-data-show').html(response.html);
                                    }
                                });
                            }
                        });

                });
            });

            function resetForm() {
                $("#resnums").val('');
            }
        </script>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
//$page = new Page("noheader");
//$page->show();
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>