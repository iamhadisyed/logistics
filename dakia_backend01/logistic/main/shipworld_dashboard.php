<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'iaddress.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'country.class',
                    'warehouse.class',
                    'warehousefilter.class'
                 ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $carrierId = '';
    private $serviceId = '';
    private $serAccountId = '';
    private $noRecordFound = '';

    protected function init() {
        //if(!Permissions::checkFilePermission('shipworld_dashboard.php'))
         //       util_redirect ("index.php");
         $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'shipworld_dashboard.php' => "Ship2World Dashboard"
        );
        

        
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "getDashboardData") {
            
            $date = date("Y-m-d");
            $outputArray  = array();
            /*
             * get Label Created
             */
           $consignmentFilter = new ConsignmentFilter();
           $consignmentFilter->addJoin("user u", "u.id = c.user_id");
           $consignmentFilter->addFilterNew("c.`shipment_status` NOT IN ('11','22') and  u.user_account_id in ('4889', '2311') and date_created >= '".$date."' and shipment_type = 'D'");
           $cList = $consignmentFilter->getListNew(" count(c.id) as total_parcel");
           $totalLabelCreated = 0;
           if(count($cList) > 0){
               foreach($cList as $consignment){
                   $totalLabelCreated = $consignment->getTotalParcel();
               }
           }
           $outputArray["label_created"] = $totalLabelCreated;
           
           /*
            * Drop Off
            */
           $consignmentFilter = new ConsignmentFilter();
           $consignmentFilter->addJoin("parcel p", "p.consignment_id = c.id");
           $consignmentFilter->addJoin("user u", "u.id = c.user_id");
           $consignmentFilter->addJoin("tracking_data t", "t.entity_id= p.id");
           $consignmentFilter->addFilterNew("c.`shipment_status` NOT IN ('11','22') and  u.user_account_id in ('4889', '2311') and t.date_created >= '".$date."' and shipment_type = 'DO' and t.carrier_desc = 'Drop-Off'");
           $cList = $consignmentFilter->getListNew(" count(c.id) as total_parcel"); 
           $totalDropOff = 0;
           if(count($cList) > 0){
               foreach($cList as $consignment){
                   $totalDropOff = $consignment->getTotalParcel();
               }
           }
           $outputArray["drop_off"] = $totalDropOff;
           
            $user = SessionManager::getUser();
            $countryId = $user->getCountryId();
            $country = new Country($countryId);
            $trackpoint = '';
            $countryIso3 = '';
            $warehouseName = '';
            $carrierDesc = '';
            if (count($country) > 0)
                $countryIso3 = $country->getIso3();

            $warehouseid = $user->getWarehouseId();
            if ($warehouseid > 0) {
                $warehouseObj = new Warehouse($warehouseid);
                $warehouseName = $warehouseObj->getWarehouseName();
            }
            $trackpoint = $warehouseName." - ".$countryIso3;

            if(!empty($warehouseName)) {
                $arrivedDesc = 'Arrived at Sort Facility ' . $trackpoint;
                $departedDesc = 'Departed Facility in '.$trackpoint;
            }
           
           /*
            * Warehouse Received
            */
           $consignmentFilter = new ConsignmentFilter();
           $consignmentFilter->addJoin("parcel p", "p.consignment_id = c.id");
           $consignmentFilter->addJoin("user u", "u.id = c.user_id");
           $consignmentFilter->addJoin("tracking_data t", "t.entity_id= p.id");
           $consignmentFilter->addFilterNew("c.`shipment_status` NOT IN ('11','22') and  u.user_account_id in ('4889', '2311') and t.date_created >= '".$date."' and shipment_type = 'DO' and t.status_code_id = '146' and t.carrier_desc='".$arrivedDesc."' and shipment_status = '14' AND c.awb IN (SELECT hawb FROM consignment cc WHERE cc.hawb = c.awb AND cc.shipment_status  IN ('13','14'))");
           $cList = $consignmentFilter->getListNew(" count(c.id) as total_parcel"); 
           $totalWarehouseReceived = 0;
           if(count($cList) > 0){
               foreach($cList as $consignment){
                   $totalWarehouseReceived = $consignment->getTotalParcel();
               }
           }
           $outputArray["warehouse_received"] = $totalWarehouseReceived;
           
           /*
            * Warehouse Dispatch
            */
           $consignmentFilter = new ConsignmentFilter();
           $consignmentFilter->addJoin("parcel p", "p.consignment_id = c.id");
           $consignmentFilter->addJoin("user u", "u.id = c.user_id");
           $consignmentFilter->addJoin("tracking_data t", "t.entity_id= p.id");
           $consignmentFilter->addFilterNew("c.`shipment_status` IN ('16') and  u.user_account_id in ('4889', '2311') and t.date_created >= '".$date."' and shipment_type = 'D' and t.status_code_id = '126' and t.carrier_desc='".$departedDesc."'");
           $cList = $consignmentFilter->getListNew(" count(c.id) as total_parcel"); 
           $totalWarehouseDispatch = 0;
           if(count($cList) > 0){
               foreach($cList as $consignment){
                   $totalWarehouseDispatch = $consignment->getTotalParcel();
               }
           }
           $outputArray["warehouse_dispatch"] = $totalWarehouseDispatch;
           
           $outputArray["ready_to_dispatch"] = $totalWarehouseReceived;
           
           /*
            * Shipment Transit
            */
           $consignmentFilter = new ConsignmentFilter();
           $consignmentFilter->addJoin("parcel p", "p.consignment_id = c.id");
           $consignmentFilter->addJoin("user u", "u.id = c.user_id");
           $consignmentFilter->addJoin("tracking_data t", "t.entity_id= p.id");
           $consignmentFilter->addFilterNew("c.`shipment_status` NOT IN ('11','22') and  u.user_account_id in ('4889', '2311') and t.date_created >= '2021-07-15' and shipment_type = 'D' and t.status_code_id = '146' and c.shipment_status != '19'");
           $cList = $consignmentFilter->getListNew(" count(c.id) as total_parcel"); 
           $totalIntransit = 0;
           if(count($cList) > 0){
               foreach($cList as $consignment){
                   $totalIntransit = $consignment->getTotalParcel();
               }
           }
           $outputArray["shipment_intransit"] = $totalIntransit;
           echo json_encode($outputArray);
           exit;
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
        <?php
    }

    public function addPagelavelJs() {
        ?>
        

        <script type="text/javascript">
             
            $(document).ready(function () {
                $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
                getDashboardData();
            });

            function getDashboardData() {
                    $.ajax({
                        type: "POST",
                        url: "shipworld_dashboard.php",
                        data: {action: "getDashboardData"}, 
                        dataType: "json",
                        success: function (data) {
                            $('.label_created').html(data.label_created);
                            $('.dropoff_scans').html(data.drop_off);
                            $('.received_warehouse').html(data.warehouse_received);
                            $('.dispatch_warehouse').html(data.warehouse_dispatch);
                            $('.ready_dispatch').html(data.ready_to_dispatch);
                            $('.shipment_transit').html(data.shipment_intransit);
                            
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
            }
            
            var intervalId = window.setInterval(function(){
                getDashboardData();
              }, 300000);
            
            
            
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
                    Ship2World Dashboard
                </div>
                <div class="tools">
                    <a href="" class="fullscreen" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row widget-row">
                    <div class="col-md-4">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">Label Created</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-green fa fa-shopping-cart"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"></span>
                                    <span class="widget-thumb-body-stat label_created" data-counter="counterup" data-value="0">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                    <div class="col-md-4">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">Drop-Off Scan</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-green fa fa-cubes"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"></span>
                                    <span class="widget-thumb-body-stat dropoff_scans" data-counter="counterup" data-value="0">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                    <div class="col-md-4">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">Received Warehouse</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-green fa fa-truck"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"></span>
                                    <span class="widget-thumb-body-stat received_warehouse" data-counter="counterup" data-value="0">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                </div>
                
                <div class="row widget-row">
                    <div class="col-md-4">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">Dispatch Warehouse</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-green fa fa-truck"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"></span>
                                    <span class="widget-thumb-body-stat dispatch_warehouse" data-counter="counterup" data-value="0">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                    <div class="col-md-4">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">Ready to Dispatch</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-green fa fa-cubes"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"></span>
                                    <span class="widget-thumb-body-stat ready_dispatch" data-counter="counterup" data-value="0">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                    <div class="col-md-4">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">Shipment Transit</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-green fa fa-road"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-subtitle"></span>
                                    <span class="widget-thumb-body-stat shipment_transit" data-counter="counterup" data-value="0">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
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
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead() {
        ?>
        <style>
            #select2-service_id-results .select2-results__option[aria-disabled=true] {
                display: none;
            }
        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>