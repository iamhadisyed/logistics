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
    'consignment.class',
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
    'optimussorter.class'

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

    /*     * *
     * Controller logic
     */
    protected function init()
    {
        t_on(); // turn on trace for this page
        if (trim($_GET['tracking_number']) == '' || trim($_GET['tracking_number']) == '-') {
            util_redirect("../main/404.php");
            die;
        }
        $trackingNumber = cleanTrackingNo($_GET['tracking_number']);
        $trackingObj = new Tracking(); //  Calling the constructor
        $this->trackingResults = $trackingObj->GetTracking($trackingNumber);
      
        if ($this->trackingResults['status'] == 'success') {
            $this->shipmentDetail = $this->trackingResults['tracking']['shipment_detail'];
            $this->trackingEvents = $this->trackingResults['tracking']['tracking_events'];
            $this->hawb = $this->shipmentDetail['hawb'];
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addHawbFilter($this->hawb);
            $result = $consignmentFilter->getConList();
            if(count($result)>0 && trim($result[0]->getSorterImage()) != ''){
                $this->sorterImage = $result[0]->getSorterImage();
            }
            $this->trackingStatus = 'In Transit';
            $trackingStatusDescription = '';
            if (!empty($this->trackingEvents)) {
                foreach ($this->trackingEvents as $tmpTrackingEvent) {
                    $this->trackingStatus = $tmpTrackingEvent[0]['event_content'];
                    $this->trackingStatusDescription = $tmpTrackingEvent[0]['carrier_desc'];
                    if ($tmpTrackingEvent[0]['signatory'] != '') {
                        $this->trackingStatusDescription .= " " . $tmpTrackingEvent[0]['signatory'];
                    }
                    break;
                }
               
            }
        }
        
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_sorter_image') {
            $sorterImage = $this->form_vars['sorterImage'];
            $imagePath = SETTING_DIR_ASSETS.'optimus_sorter/Image/'.$sorterImage;
            $baseimagePath = SETTING_URL_ASSETS.'optimus_sorter/Image/'.$sorterImage;
            if(!file_exists($imagePath)){
                $optimusSorter = new OptimusSorter();
                $downloadImage = $optimusSorter->getSorterImage($sorterImage);
                echo $baseimagePath ;
            }
            else
            {
                echo $baseimagePath;
            }
            die;
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead()
    {
        ?>
       <script type="text/javascript">
            function showsorterImage(sorterImage, trackingNumber){
                $.ajax({
                    url: "tracking.php?tracking_number="+trackingNumber,
                    data: {func: "get_sorter_image", sorterImage: sorterImage},
                    type: 'post',
                    async: false,
                    success: function (response) {
                        if(response != ''){
                             $('#show-sorter-image').modal('show');
                             $('#tracking_image_container').html("<div><img src='"+response+"' alt='"+trackingNumber+"' height='500' width = '800'></div>");
                        }
                    }
                });
            }
       </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
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
    

                <?php
                if ($this->trackingResults['status'] == 'success') {
                    ?>
                    <div class="row">

                      



                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <a class="dashboard-stat dashboard-stat-v2 <?php echo(strtolower($this->trackingStatus) == 'delivered' ? 'green' : 'blue'); ?>"
                               href="javascript:{};">
                                <div class="visual">
                                    <i class="fa fa-<?php echo(strtolower($this->trackingStatus) == 'delivered' ? 'home' : 'truck'); ?>"></i>
                                </div>
                                <div class="details">
                                    <div class="number">
                                <span data-counter="counterup"
                                      data-value="12,5"><?php echo $this->trackingStatus; ?></span></div>
                                    <div class="desc">
                                        <small><?php echo trim(str_replace($this->trackingStatus . " - ", "", $this->trackingStatusDescription)); ?></small>
                                        <br/>
                                        <?php echo $this->shipmentDetail['transit_time']; ?> transit time
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="c-content-title-1">
                                    <h4 class="uppercase" style="margin-bottom: 3px;margin-top: 4px;">Have another shipment?</h4>
                                    <div class="c-line-left bg-dark"></div>
                                    <form action="tracking.php" method="get">
                                        <div class="input-group input-group-lg c-square">
                                            <input type="text" class="form-control c-square" name="tracking_number" placeholder="Tracking No." value="<?php echo $_GET['tracking_number']?>">
                                            <span class="input-group-btn">
                                        <button class="btn uppercase" type="submit">Track</button>
                                         <?php
                                         if(trim($this->sorterImage) != ''){ ?>
                                             <a   id="btnSorterImage" href="#" onclick="showsorterImage('<?=$this->sorterImage?>', '<?php echo $_GET['tracking_number']?>');" class="btn blue btnedit  show_sorter_image"   >
                                                        Parcel Image
                                                    </a>
                                            <?php //echo  '<a target="_blank" href="http://164.39.218.212/Optimus/image/'.$this->sorterImage.'" class="btn blue"> <i class="fa fa-picture-o"></i>Parcel Image</a>'; 
                                         }
                                        ?> 
                                       
                                    </span>                                            
                                        </div>
                                                                       
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="color: #3f444a;;">
                                <strong> <i class="fa fa-info-circle"></i> SHIPMENT INFORMATION</strong>
                            </h5>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-green-sharp">
                                            <span><?php echo $this->shipmentDetail['tracking_number']; ?></span>
                                        </h3>
                                        <small> Tracking Number</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-pie-chart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-red-haze">
                                            <span><?php echo $this->shipmentDetail['hawb']; ?></span>
                                        </h3>
                                        <small>Order # (Hawb)</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-basket"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-blue-sharp">
                                            <span><?php echo $this->shipmentDetail['origin_country']; ?></span>
                                        </h3>
                                        <small>Origin Country</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-flag"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="dashboard-stat2 bordered">
                                <div class="display">
                                    <div class="number">
                                        <h3 class="font-purple-soft">
                                            <span><?php echo $this->shipmentDetail['destination_country']; ?></span>
                                        </h3>
                                        <small>Destination Country</small>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-flag"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-element-step">
                        <div class="row step-line">
                            <div class="mt-step-desc">
                                <!-- <div class="font-dark bold uppercase"><?php echo $this->trackingStatus; ?></div>
                        <div class="caption-desc font-grey-cascade"><?php echo $this->trackingStatusDescription; ?></div> -->
                                <br>
                            </div>

                            <div class="col-md-4 mt-step-col first active">
                                <div class="mt-step-number bg-white">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                                <div class="mt-step-title uppercase font-grey-cascade">Picked Up</div>
                                <!-- <div class="mt-step-content font-grey-cascade">Purchasing the item</div> -->
                            </div>
                            <div
                                    class="col-md-4 mt-step-col <?php echo($this->trackingStatus != 'Delivered' ? ' done' : ($this->trackingStatus == 'Delivered' ? ' active' : '')); ?>">
                                <div class="mt-step-number bg-white">
                                    <i class="fa fa fa-truck"></i>
                                </div>
                                <div class="mt-step-title uppercase font-grey-cascade">On the Way</div>
                                <!-- <div class="mt-step-content font-grey-cascade">Complete your payment</div> -->
                            </div>
                            <div
                                    class="col-md-4 mt-step-col last<?php echo($this->trackingStatus == 'Delivered' ? ' done' : ''); ?>">
                                <div class="mt-step-number bg-white">
                                    <i class="fa fa-home"></i>
                                </div>
                                <div class="mt-step-title uppercase font-grey-cascade">Delivered</div>
                                <!-- <div class="mt-step-content font-grey-cascade">Receive item integration</div> -->
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-advance table-hover">
                        <thead>
                        <tr>
                            <th colspan="4">
                                <div class="caption">
                                    <i class=" icon-layers"></i>
                                    <span class="caption-subject bold uppercase">Tracking Events</span>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($this->trackingEvents) > 0) {
                            $heading = true;
                            foreach ($this->trackingEvents as $eventDate => $trackingEvent) {                                
                                 
                                ?>
                                <tr>
                                    <th colspan="<?php echo ($heading===true ? 2 : 4)?>"><?php echo date('l, F d, Y', strtotime($eventDate)); ?></th>
                                    <?php if($heading===true){ ?>
                                        <th>Description</th>
                                        <th>Status</th>
                                    <?php } ?>
                                </tr>
                                <?php
                                foreach($trackingEvent as $key => $row)
                                {
                                    $time[$key]  = $row['time_12_hr'];
                                }
                                array_multisort($time, SORT_DESC, $trackingEvent);
                                   
                                foreach ($trackingEvent as $eventDetail) {
                                  
                                    ?>
                                    <tr>
                                        <td width="90"><?php echo $eventDetail['time_12_hr']; ?></td>
                                        <td><?php echo $eventDetail['track_point']; ?></td>
                                        <td><?php echo $eventDetail['carrier_desc'] . ($eventDetail['signatory'] != '' ? ' ' . $eventDetail['signatory'] : ''). ($eventDetail['parcel_image'] != '' ? ' ' . "<a href='".$eventDetail['parcel_image']."' class='btn btn-xs blue' target='_blank' > Parcel Image</a>" : ''). ($eventDetail['pod_image'] != '' ? '  ' . "<a href='".$eventDetail['pod_image']."' class='btn btn-xs blue' target='_blank' > Signature Image </a>" : ''); ?></td>
                                        <td><?php echo $eventDetail['event_content']; ?></td>
                                    </tr>
                                    <?php
                                    $heading = false;
                                }
                            }
                        }else{
                            ?>
                            <tr>
                                <td colspan="4">
                                    No Tracking data available.
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    echo '<p>No Tracking available.</p>';
                }
                ?>
            </div>
        </div>
            <div class="modal fade" tabindex="-1" role="dialog" id="show-sorter-image" aria-labelledby="myLargeModalLabel">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><span></span> Sorter Scan Image</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12 display-none" id="display_modal_message"></div>
                                        <div id="tracking_image_container"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
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
    public function renderMenu()
    {
        if (isset($_SESSION['admin'])) {
            $menu = new Adminmenu(Adminmenu::COURIERS);
            $menu->render();
        }
    }

    protected function addPagelavelCss()
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

    protected function addPagelavelJs()
    {
      
        ?>
        <script src="../assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-knob-dials.min.js" type="text/javascript"></script>
        <?php
        if (!isset($_SESSION['admin'])) {
            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $(".page-content-wrapper > .page-content").addClass('page-sidebar-hide');
                    $(".sidebar-toggler").hide();
                });
      
           
        </script>
            <?php
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
//$page = new Page("noheader");
//$page->show();
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>