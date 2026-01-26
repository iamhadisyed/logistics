<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    private $report_filter;
    private $services_list;
    private $service;
    private $date_scanned_from;
    private $date_scanned_to;
    private $report_data;
    
    /*     * *
     * Set the page header
     * @return void
     */

    public function getTitle() {
        return "Admin - Index";
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-8"> 
                <h3 class="page-title">Service Scan Report by Account</h3>
            </div>
            <div class="col-md-4"> 
                <!--<a href="reporting_user_fullscreen.php" target="_blank" class="btn btn-success pull-right">Show Full Screen</a>-->
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Panel </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Service</label>
                                <select name="service" id="service" class="form-control">
                                    <option value="">Service Name</option>
                                    <?php
                                    if (count($this->services_list) > 0) {
                                        foreach ($this->services_list as $serviceObj) {
                                            $serviceCode = trim($serviceObj->getCode());
                                            $serviceName = trim($serviceObj->getName());
                                            $carrier = trim($serviceObj->getCarrier());
                                            echo '<option value="' . $serviceCode . '" class="' . $carrier . '"' . ($serviceCode == $this->service ? ' selected="selected"' : '') . '>' . $serviceName . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>                                             
                        <div class="col-md-3">
                            <label class="control-label">Date From</label>
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_scanned_from" id="date_scanned_from" type="text" placeholder="Date Form" value="<?php echo @$this->date_scanned_from; ?>" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label">Date To</label>
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_scanned_to" id="date_scanned_to" type="text" placeholder="Date To" value="<?php echo @$this->date_scanned_to; ?>" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">&nbsp;</label><br />
                            <button type="submit" name="btnSearch" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-users"></i>Result
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                    <a href="" class="fullscreen">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px;"  data-rail-color="blue" data-handle-color="blue" id="rpt_container">
                    <div class="table-bordered" id="rpt_tbl_container">
                        <table class="table table-striped table-bordered table-advance table-hover">
                            <thead>
                                <tr>
                                    <th class="red-back">Service</th>
                                    <th class="red-back">Total Tracking Numbers</th>
                                    <th class="red-back">Unique Tracking Numbers</th>
                                    <th class="red-back">Duplicate Tracking Numbers</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($this->report_data) > 0) {
                                    echo '<tr class="danger" id="header-grand-total-tr"></tr>';
                                    $oldService = '';
                                    $serviceTotal = 0;
                                    $serviceUnique = 0;
                                    $serviceDuplicate = 0;
                                    
                                    $grandTotal = 0;
                                    $grandUnique = 0;
                                    $grandDuplicate = 0;
                                    $code = '';                                   
                                    foreach ($this->report_data as $data) {
                                        $scanned_by = $data['scanned_by'];
                                        $account = $data['account'];
                                        $handling = $data['handling'];                                        
                                        $serviceName = $data['serviceName'];                                        
                                        $tracking_numbers = $data['scanedTotal'];
                                        $distinct_tracking_numbers = $data['scanedUnique'];
                                        $duplicate = $tracking_numbers - $distinct_tracking_numbers;
                                        
                                        $grandTotal += $tracking_numbers;
                                        $grandUnique += $distinct_tracking_numbers;
                                        $grandDuplicate += $duplicate;
                                        
                                        $find = array("!","@","#","$","%","^","&","*","(",")","+","=","|"," ");
                                        $replace = array("","","","","","","","","","","","","","_");
                                        $code = str_replace($find, $replace, $handling);
                                        
                                        if ($handling != $oldService) {
                                            if ($oldService != '') {
                                                $code1 = str_replace($find, $replace, $oldService);
                                        ?>        
                                            <input type="hidden" data-bind="total_<?php echo $code1; ?>" class="total_vals" value="<?php echo $serviceTotal; ?>" />
                                            <input type="hidden" data-bind="unique_<?php echo $code1; ?>" class="total_vals" value="<?php echo $serviceUnique; ?>" />
                                            <input type="hidden" data-bind="duplicate_<?php echo $code1; ?>" class="total_vals" value="<?php echo $serviceDuplicate; ?>" />
                                        <?php  
                                            }
                                        ?>
                                            <tr class="active service_tr" data-bind="service_account_<?php echo $code;?>">
                                                <td><?php echo $serviceName; ?></td>
                                                <td id="total_<?php echo $code; ?>"></td>
                                                <td id="unique_<?php echo $code; ?>"></td>
                                                <td id="duplicate_<?php echo $code; ?>"></td>
                                            </tr>
                                        <?php
                                            $oldService = $handling;
                                            $serviceTotal = 0;
                                            $serviceUnique = 0;
                                            $serviceDuplicate = 0;
                                        }
                                        ?>
                                        <tr class="service_account_tr service_account_<?php echo $code;?>">
                                            <td><?php echo $account; ?></td>
                                            <td><?php echo $tracking_numbers; ?></td>
                                            <td><?php echo $distinct_tracking_numbers; ?></td>
                                            <td><?php echo $duplicate; ?></td>
                                        </tr>
                                        <?php
                                        $serviceTotal += $tracking_numbers;
                                        $serviceUnique += $distinct_tracking_numbers;
                                        $serviceDuplicate += $duplicate;
                                    }
                                    ?>
                                    <input type="hidden" data-bind="total_<?php echo $code; ?>" class="total_vals" value="<?php echo $serviceTotal; ?>" />
                                    <input type="hidden" data-bind="unique_<?php echo $code; ?>" class="total_vals" value="<?php echo $serviceUnique; ?>" />
                                    <input type="hidden" data-bind="duplicate_<?php echo $code; ?>" class="total_vals" value="<?php echo $serviceDuplicate; ?>" />
                                    <tr class="danger" id="footer-grand-total-tr">
                                        <th>Grand Total</th>
                                        <th><?php echo $grandTotal; ?></th>
                                        <th><?php echo $grandUnique; ?></th>
                                        <th><?php echo $grandDuplicate; ?></th>
                                    </tr>
                                    <?php
                                }else{
                                ?>
                                    <tr>
                                        <td colspan="4">No Record Found.</td>
                                    </tr>   
                                <?php 
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
        <?php
    }
    /**
     * Override to show the menu
     *
    */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }
    public function renderHead() {
        ?>
        <style type="text/css">
            #counterWiseRptNoModal{z-index: 99999999}
        </style>
        <script type="text/javascript">
            function setupRefresh() {
                setTimeout("refreshPage();", (5000 * 60)); // milliseconds
            }
            function refreshPage() {
                window.location = location.href;
            }
            $(document).ready(function() {                
                setupRefresh();
                $("#date_scanned_from").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
                $("#date_scanned_to").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});                
                $("#header-grand-total-tr").html($("#footer-grand-total-tr").html());                
                $(".total_vals").each(function(){
                    var bind = $(this).data('bind');
                    var value = $(this).val();
                    $("#"+bind).html(value);
                });
                
                $(".service_account_tr").hide();                
                $(".service_tr").click(function(){
                    var bind = $(this).data('bind');
                    if($(this).hasClass('shown')){
                        $(this).removeClass('shown');
                        $("."+bind).hide();
                    }else{
                        $(this).addClass('shown');
                        $("."+bind).show('slow');
                    }
                    var tbl_height = $("#rpt_tbl_container").height();
                    $("#rpt_container").parent().height(tbl_height+"px");
                    $("#rpt_container").height(tbl_height+"px");
                });                
            });
        </script>
        <?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {        
        $servicep = '';
        $date_scanned_fromp = '';
        $date_scanned_top = '';
        
        $servicesFilterObj1 = new ServiceFilter();
        $this->services_list = $servicesFilterObj1->getColumnList('DISTINCT `code`, `name`, `carrier`');
        
                
        if (isset($_POST['service'])) {
            $servicep = $_POST['service'];
            $_SESSION['SERVICE_ACCOUNT_REPORTING']['SERVICE'] = $servicep;
        } else if (isset($_SESSION['SERVICE_ACCOUNT_REPORTING']['SERVICE'])) {
            $servicep = $_SESSION['SERVICE_ACCOUNT_REPORTING']['SERVICE'];
        }
        $this->service = $servicep;

        if (isset($_POST['date_scanned_from'])) {
            $date_scanned_fromp = $_POST['date_scanned_from'];
            $_SESSION['SERVICE_ACCOUNT_REPORTING']['DATE_SCAN_FROM'] = $date_scanned_fromp;
        } else if (isset($_SESSION['SERVICE_ACCOUNT_REPORTING']['DATE_SCAN_FROM'])) {
            $date_scanned_fromp = $_SESSION['SERVICE_ACCOUNT_REPORTING']['DATE_SCAN_FROM'];
        }
        $this->date_scanned_from = $date_scanned_fromp;

        if (isset($_POST['date_scanned_to'])) {
            $date_scanned_top = $_POST['date_scanned_to'];
            $_SESSION['SERVICE_ACCOUNT_REPORTING']['DATE_SCAN_TO'] = $date_scanned_top;
        } else if (isset($_SESSION['SERVICE_ACCOUNT_REPORTING']['DATE_SCAN_TO'])) {
            $date_scanned_top = $_SESSION['SERVICE_ACCOUNT_REPORTING']['DATE_SCAN_TO'];
        }
        $this->date_scanned_to = $date_scanned_top;
        
        if (empty($this->date_scanned_from))
            $this->date_scanned_from = date("Y-m-d");
        if (empty($this->date_scanned_to))
            $this->date_scanned_to = date("Y-m-d");

        
        
        $whereArr = array();
        $whereStr = "";
        
        $whereArr[] = "c.consignment_status NOT IN ('poland received', 'poland booked', 'recycled')";
        if (!empty($this->service))
            $whereArr[] = "c.handling = '" .DbAccess3::escape( $this->service ). "'";
        if (!empty($this->date_scanned_from))
            $whereArr[] = "DATE(c.date_scanned) >= '" . $this->date_scanned_from . "'";
        if (!empty($this->date_scanned_to))
            $whereArr[] = "DATE(c.date_scanned) <= '" . $this->date_scanned_to . "'";

        if (!empty($whereArr))
            $whereStr = implode(" AND ", $whereArr);
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        
        $this->setTitle("Admin - Country Reporting");
        /* ------------------------------------------------------------------------------ */
        // get report
                
        $this->report_filter = new reportingfilter();
        $this->report_filter->setFilter($whereStr);
        
        $this->report_data = $this->report_filter->getServicesAccountReport();
    }
}
/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>