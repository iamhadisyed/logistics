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
    <h3 class="page-title">Scan Items Not Found</h3>
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
    <div class="scroller" data-rail-color="blue" data-handle-color="blue">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group date-date-pic">
         
              <input class="form-control" name="date_scanned_from" id="date_scanned_from" type="text" placeholder="Date Form" value="Date From <?php echo @$this->date_scanned_from; ?>" />
           
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group  date-date-pic">
         
              <input class="form-control" name="date_scanned_to" id="date_scanned_to" type="text" placeholder="Date To" value="Date To <?php echo @$this->date_scanned_to; ?>" />
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <input type="hidden" name="form_action" id="form_action"  />
          <button type="button" name="btnSearch" id="btnSearch" class="btn btn-primary">Search</button>
          <button type="button" name="btnExport" id="btnExport" class="btn btn-primary">Export</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption"> <i class="icon-users"></i>Result </div>
    <div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
  </div>
  <div class="portlet-body">
    <div class="scroller" style="min-height:200px;"  data-rail-color="blue" data-handle-color="blue" id="rpt_container">
      <div class="table-bordered" id="rpt_tbl_container">
        <table class="table table-striped table-bordered table-advance table-hover">
          <thead>
            <tr>
              <th>Master Number</th>
              <th>Bag Number</th>
              <th>Tracking Number</th>
              <th>Scanned By</th>
              <th>Scanned Date</th>
            </tr>
          </thead>
          <tbody>
            <?php
                                if (count($this->report_data) > 0) {
                                    echo '<tr class="danger" id="header-grand-total-tr"></tr>';
                                    $totalRecord = 0;
                                    foreach ($this->report_data as $data) {                                        
                                    ?>
            <tr>
              <td><?php echo $data->getMawb(); ?></td>
              <td><?php echo $data->getBagNumber(); ?></td>
              <td><?php echo $data->getTrackingNumber(); ?></td>
              <td><?php echo $data->getScannedBy(); ?></td>
              <td><?php echo $data->getDateCreated(); ?></td>
            </tr>
            <?php
                                        $totalRecord++;
                                    }
                                    ?>
            <tr class="danger" id="footer-grand-total-tr">
              <th colspan="4">Total Not Found</th>
              <th><?php echo $totalRecord; ?></th>
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
<script type="text/javascript">            
            $(document).ready(function() {
                $("#date_scanned_from").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
                $("#date_scanned_to").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
                $("#header-grand-total-tr").html($("#footer-grand-total-tr").html());                
                $("#btnSearch").click(function(){
                    $("#form_action").val("Search");
                    $("#adminForm").submit();
                });
                $("#btnExport").click(function(){
                    $("#form_action").val("Export");
                    $("#adminForm").submit();
                });
            });
        </script>
<?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $date_scanned_fromp = '';
        $date_scanned_top = '';
        
        if (isset($_POST['date_scanned_from'])) {
            $date_scanned_fromp = $_POST['date_scanned_from'];
            $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'] = $date_scanned_fromp;
        } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_FROM'])) {
            $date_scanned_fromp = $_SESSION['USER_REPORTING']['DATE_SCAN_FROM'];
        }
        $this->date_scanned_from = $date_scanned_fromp;

        if (isset($_POST['date_scanned_to'])) {
            $date_scanned_top = $_POST['date_scanned_to'];
            $_SESSION['USER_REPORTING']['DATE_SCAN_TO'] = $bag_numberp;
        } else if (isset($_SESSION['USER_REPORTING']['DATE_SCAN_TO'])) {
            $date_scanned_top = $_SESSION['USER_REPORTING']['DATE_SCAN_TO'];
        }
        $this->date_scanned_to = $date_scanned_top;
        
        if (empty($this->date_scanned_from))
            $this->date_scanned_from = date("Y-m-d");
        if (empty($this->date_scanned_to))
            $this->date_scanned_to = date("Y-m-d");

        
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        
        $this->setTitle("Admin - User Reporting");
        /* ------------------------------------------------------------------------------ */
        // get report
        $NotFoundRecordFilter = new NotFoundRecordFilter();
        $NotFoundRecordFilter->addDateCreatedFilter($this->date_scanned_from, $this->date_scanned_to);
        $this->report_data = $NotFoundRecordFilter->getColumnList('mawb, bag_number, tracking_number, scanned_by, date_created');
        if ($_POST["form_action"] == "Export") {
            if (count($this->report_data) > 0) {
                $csv = "";
                $cr = "\r\n";
                $count = 1;
                $csvHeader = "Sr.,Master Number,Bag Number,Tracking Number,Scanned By,Scanned Date".$cr;
                $NumberofUniqueCode = 0;
                foreach ($this->report_data as $notFound) {                    
                    $csv .= $count.",".$notFound->getMawb().",".$notFound->getBagNumber().",";
                    $csv .= $notFound->getTrackingNumber() . ",".$notFound->getScannedBy().",".$notFound->getDateCreated();
                    $csv .= $cr;
                    $count +=1;
                }
                $data = $csvHeader . $cr . $csv;
                echo $data;
                $uniqueFileName = "not-found-report-".$this->date_scanned_from."-to-".$this->date_scanned_to."-".time();
                header("Content-Type: application/csv");
                header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");
                exit;
            }
        }        
    }
}
/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?> 
