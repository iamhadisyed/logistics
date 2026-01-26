<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");
if(isset($_GET['csv']) && isset($_GET['year']) && isset($_GET['carrier']) && !isset($_GET['month'])){
    $year = $_GET['year'];
    $carrier = $_GET['carrier'];
    $whereArr = array();
    $whereStr = "";

    $whereArr[] = "c.consignment_status NOT IN ('poland received', 'poland booked', 'recycled') ";
    if (!empty($carrier))
        $whereArr[] = " s.carrier = '" . DbAccess3::escape($carrier) . "'";        

    if (!empty($year))
        $whereArr[] = " YEAR(c.`date_scanned`) = '" . $year . "'";        

    if (!empty($whereArr))
        $whereStr = implode(" AND ", $whereArr);

        $report_filter = new reportingfilter();
        $report_filter->setFilter($whereStr);
            if(trim($year) !='' && trim($month) == ''){
                $report_filter->setGroupBy('MONTH(c.`date_scanned`)');
                $report_filter->setOrderBy('MONTH(c.`date_scanned`) DESC');
            }
            $report_data = $report_filter->getServicesTransactionGraphicsReport();
            $arry_months = array(
                            '1'=>array('Month'=>1,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '2'=>array('Month'=>2,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '3'=>array('Month'=>3,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '4'=>array('Month'=>4,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '5'=>array('Month'=>5,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '6'=>array('Month'=>6,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '7'=>array('Month'=>7,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '8'=>array('Month'=>8,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '9'=>array('Month'=>9,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '10'=>array('Month'=>10,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '11'=>array('Month'=>11,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                            '12'=>array('Month'=>12,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0)
                          );
                    foreach($report_data as $data){
                        $arry_months[$data['monthly']] = array('Month'=>$data['monthly'],'Total' => $data['scanedTotal'], 'Unique' => $data['scanedUnique'], 'Duplicate' => $data['scanedTotal'] - $data['scanedUnique']);                          
                    }
    header('Content-Type: application/excel');
    header('Content-Disposition: attachment; filename=ServiceTransactionTotalscannedYearly-' . date('Y-m-d'). '.csv');
    header('Pragma: no-cache');
    $csvContent = 'Month,TotalScanned,Unique,Duplicate' . "\n";
    if(count($report_data) > 0){
        $sr = 1;
        foreach($arry_months as $data){
            $monthOfFormat = $year.'-'.$data['Month'];
           
            $csvContent .= date('F Y',  strtotime($monthOfFormat)).",".$data['Total'].",".$data['Unique'].",".$data['Duplicate']."\n";
            $sr++;
        }
        echo $csvContent;
        exit;
    }
}else if(isset($_GET['csv']) && isset($_GET['year']) && isset($_GET['month']) && isset($_GET['carrier'])){
    $year = $_GET['year'];
    $carrier = $_GET['carrier'];
    $month = $_GET['month'];
    $whereArr = array();
    $whereStr = "";

    $whereArr[] = "c.consignment_status NOT IN ('poland received', 'poland booked', 'recycled') ";
    if (!empty($carrier))
        $whereArr[] = " s.`carrier` = '" .DbAccess3::escape( $carrier ). "'";        

    if (!empty($year))
        $whereArr[] = " YEAR(c.`date_scanned`) = '" . $year . "'";        

    if (!empty($month))
        $whereArr[] = " MONTH(c.`date_scanned`) = '" . $month . "'";        
   
    if (!empty($whereArr))
        $whereStr = implode(" AND ", $whereArr);

    $report_filter = new reportingfilter();
    $report_filter->setFilter($whereStr);
    if(trim($year) !='' && trim($month) != ''){
        $report_filter->setGroupBy('DAY(c.`date_scanned`)');
        $report_filter->setOrderBy('DAY(c.`date_scanned`) ');
    }
    $report_data = $report_filter->getServicesTransactionGraphicsReport();
    $daysInMonth =array();
    $daysInMon = date('t', mktime(0, 0, 0, $month));
    for($i = 1 ; $i <= $daysInMon; $i++){
        $array_days[$i]  = array(
                                'Day'=>$i,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0
                                );
    }
    foreach($report_data as $data){
        $array_days[$data['day']] = array('Day'=>$data['day'],'Total' => $data['scanedTotal'], 'Unique' => $data['scanedUnique'], 'Duplicate' => $data['scanedTotal'] - $data['scanedUnique']);
    }
    header('Content-Type: application/excel');
    header('Content-Disposition: attachment; filename=ServiceTransactionTotalscannedMonthly-' . date('Y-m-d'). '.csv');
    header('Pragma: no-cache');
    $csvContent = 'Date,TotalScanned,Unique,Duplicate' . "\n";
    if(count($report_data) > 0){
        $sr = 1;
        foreach($array_days as $data){
            $monthOFDays = $year.'-'.$month.'-'.$data['Day'];
            $format_date = date('F j- Y',  strtotime($monthOFDays));
            $csvContent .=  $format_date.",".$data['Total'].",".$data['Unique'].",".$data['Duplicate']."\n";
            $sr++;
        }
        echo $csvContent;
        exit;
    }
}
// set up local page class
class Page extends BasePage {

    private $report_filter;
    private $carrier_list;
    private $carrier;
    private $years;
    private $months;
    private $arry_months = array();
    private $array_days = array();
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
            <h3 class="page-title">Service Transaction Graphicaly Report</h3>
          </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Panel </div>
              <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
            <div class="scroller" data-rail-color="blue" data-handle-color="blue">
                <form action='reporting_service_graphics.php' method="post" name="transaction_form" id="transaction_form">
                    <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <select name="carrier" id="carrier" class="form-control">
                              <option value="">Carrier Name</option>
                              <?php
                                    if (count($this->carrier_list) > 0) {
                                        foreach ($this->carrier_list as $carrierObj) {
                                            $carrier = trim($carrierObj->getCarrier());
                                            echo '<option value="' . $carrier . '"' . ($carrier == $this->carrier ? ' selected="selected"' : '') . '>' . $carrier . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <select name="years" id="years" class="form-control">
                                <option value="">Year</option>
                                  <?php

                                  $years = range ( date( 'Y' ), date( 'Y') - 5 );

                                  foreach ( $years as $year )
                                  {
                                      echo '<option value="' . $year . '"' . ($year == $this->years ? 'selected="selected"' : '') . '>' . $year . '</option>';                            
                                  }
                                  ?>        

                            </select>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <select name="months" id="months" class="form-control">
                                <option value="">All Months</option>
                                  <?php
                                  for ($m=1; $m<=12; $m++) {
                                      $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                                      echo '<option value="' . $m . '"' . ($m == $this->months ? 'selected="selected"' : '') . '>' . $month . '</option>';                            
                                  }
                                  ?>        
                            </select>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <button type="submit" name="btnSearch" class="btn btn-primary">Search</button>
                          <button type="button" name="btnreset" id="btnreset" class="btn btn-default">Clear</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
<!-- body -->   
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>
                Report
                <?php 
                    if(count($this->report_data)>0){
                        if($this->months != '' && $this->years !=''){
                            $fmted_date = $this->years.'-'.$this->months;
                            echo 'For '.date('F Y',  strtotime($fmted_date));
                        }else if($this->months == '' && $this->years !=''){
                            echo $this->years .' For All Months';
                        }
                    }else{
                        if($this->months != '' && $this->years !=''){
                            $fmted_date = $this->years.'-'.$this->months;
                            echo 'For '.date('F Y',  strtotime($fmted_date));
                        }else if($this->months == '' && $this->years !=''){
                            echo $this->years .' For All Months';
                        }
                        
                    }
                ?>
                </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">            
                <?php 
                if ($this->years !='' && $this->months =='') {
                    if(count($this->report_data)>0){?>                                    
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bar-chart font-green-haze"></i>
                            <span class="caption-subject bold uppercase font-green-haze">Yearly Scanned Bar Chart</span>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse"></a>
                            <a href="javascript:;" class="fullscreen"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="barchart_yearly_scanned" class="chart" style="height: 400px;"></div>   
                    </div>
                </div>
                <?php }else{?>
                <div class='alert alert-info'>No Record Found</div>
                <?php } 
                }?>
                <?php if ($this->years !='' && $this->months =='') { 
                        if(count($this->report_data) > 0){
                    ?>

                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <button class="btn btn-primary btn-md pull-right"  id="exportcsv" name="exportcsv" onclick="window.location.href='reporting_service_graphics.php?csv=1&&year=<?php echo $this->years;?>&&carrier=<?php echo $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH'];?>'"><i class="fa fa-save margin-right-10"></i>Export CSV</button>
                        <div class="caption">
                            <i class="fa fa-table font-green-haze"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Tablular View</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Scanned </th>
                                        <th>Unique Scanned </th>
                                        <th>Duplicate Scanned </th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(count($this->report_data)>0){
                                    foreach($this->arry_months as $data){
                                        echo '<tr>';
                                            echo '<td>';
                                            $formatted_date = $this->years.'-'.$data['Month'];
                                            echo date('F Y ',  strtotime($formatted_date));
                                            echo '</td>';
                                            echo '<td>';
                                                echo $data['Total'];
                                            echo '</td>';
                                            echo '<td>';
                                                echo $data['Unique'];
                                            echo '</td>';
                                            echo '<td>';
                                                echo $data['Total'] - $data['Unique'];
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                 </div>
                    <?php }?>
                <?php }?>
                <?php 
                if ($this->years !='' && $this->months !='') {
                    if(count($this->report_data)>0){
                ?>
                <div class="portlet light bordered">
                    <?php ?>
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bar-chart font-green-haze"></i>
                            <span class="caption-subject bold uppercase font-green-haze">Monthly Scanned Bar Chart</span>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse"></a>
                            <a href="javascript:;" class="fullscreen"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="barchart_monthly_scanned" class="chart" style="height: 400px;"></div>   
                    </div>
                </div>
                <?php }else{?>
                    <div class='alert alert-info'>No Record Found</div>
                <?php }
                }
                ?>
                <?php if ($this->years !='' && $this->months !='') { 
                        if(count($this->report_data) > 0){
                    ?>
                <div class="portlet light bordered">
                    <button class="btn btn-primary btn-md pull-right"  id="exportcsv" name="exportcsv" onclick="window.location.href='reporting_service_graphics.php?csv=1&&year=<?php echo $this->years;?>&&month=<?php echo $this->months;?>&&carrier=<?php echo $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH'];?>'"><i class="fa fa-save margin-right-10"></i>Export CSV</button>
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-table font-green-haze"></i>
                            <span class="caption-subject bold uppercase font-green-haze"> Tablular View</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Days</th>
                                        <th>Total Scanned </th>
                                        <th>Unique Scanned </th>
                                        <th>Duplicate Scanned </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(count($this->report_data)>0){
                                    foreach($this->array_days as $data){
                                        echo '<tr>';
                                            echo '<td>';
                                            $formatted_date = $this->years.'-'.$this->months.'-'.$data['Day'];
                                            echo date('F j, Y ',  strtotime($formatted_date));
                                            echo '</td>';
                                            echo '<td>';
                                                echo $data['Total'];
                                            echo '</td>';
                                            echo '<td>';
                                                echo $data['Unique'];
                                            echo '</td>';
                                            echo '<td>';
                                                echo $data['Total'] - $data['Unique'];
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                 </div>
                <?php } ?>
            <?php }?>
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
    
    <?php if(count($this->report_data)>0){?>
    <script src="../_assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
    <script src="../_assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
    <?php }?>
    <script type="text/javascript">
        <?php if($this->years !='' && $this->months !='' && count($this->report_data)>0){?>
    var initbarChartMonthly = function() {
        var chartmonth = AmCharts.makeChart("barchart_monthly_scanned", {
            "type": "serial",
            "theme": "light",
           // "pathToImages": Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/",
            "autoMargins": false,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',            
            "color":    '#888',
            
            "dataProvider": <?php echo json_encode(array_values($this->array_days));?>,
            "valueAxes": [{
                "axisAlpha": 0,
                "position": "left"
            }],
            "legend": {
                "equalWidths": false,
                "useGraphSettings": true,
                "valueAlign": "left",
                "valueWidth": 120
            },                
            "startDuration": 1,
            "graphs": [{
                "alphaField": "alpha",
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "dashLengthField": "dashLengthColumn",
                "fillAlphas": 1,
                "title": "Total",
                "type": "column",
                "valueField": "Total"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "dashLengthField": "dashLengthLine",
                "lineThickness": 3,
                "bulletSize": 7,
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "useLineColorForBulletBorder": true,
                "bulletBorderThickness": 3,
                "fillAlphas": 0,
                "lineAlpha": 1,
                "title": "Unique",
                "valueField": "Unique"
            },{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "dashLengthField": "dashLengthLine",
                "lineThickness": 3,
                "bulletSize": 7,
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "useLineColorForBulletBorder": true,
                "bulletBorderThickness": 3,
                "fillAlphas": 0,
                "lineAlpha": 1,
                "title": "Duplicate",
                "valueField": "Duplicate"
            }],
            "categoryField": "Day",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0
            }
        });
        $('#barchart_monthly_scanned').closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });
        $('.amcharts-chart-div').find('a').hide();          
    }
        <?php }?>
        <?php if($this->years !='' && $this->months =='' && count($this->report_data)>0){?>
    var initbarChartYearly = function() {        
        var ChartYearly = AmCharts.makeChart("barchart_yearly_scanned", {
            "type": "serial",
            "theme": "light",
           // "pathToImages": Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/",
            "autoMargins": false,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',            
            "color":    '#888',
            
            "dataProvider": <?php echo json_encode(array_values($this->arry_months));?>,
            "valueAxes": [{
                "axisAlpha": 0,
                "position": "left"
            }],
            "legend": {
                "equalWidths": false,
                "useGraphSettings": true,
                "valueAlign": "left",
                "valueWidth": 120
            },                
            "startDuration": 1,
            "graphs": [{
                "alphaField": "alpha",
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "dashLengthField": "dashLengthColumn",
                "fillAlphas": 1,
                "title": "Total",
                "type": "column",
                "valueField": "Total"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "dashLengthField": "dashLengthLine",
                "lineThickness": 3,
                "bulletSize": 7,
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "useLineColorForBulletBorder": true,
                "bulletBorderThickness": 3,
                "fillAlphas": 0,
                "lineAlpha": 1,
                "title": "Unique",
                "valueField": "Unique"
            },{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "dashLengthField": "dashLengthLine",
                "lineThickness": 3,
                "bulletSize": 7,
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "useLineColorForBulletBorder": true,
                "bulletBorderThickness": 3,
                "fillAlphas": 0,
                "lineAlpha": 1,
                "title": "Duplicate",
                "valueField": "Duplicate"
            }],
            "categoryField": "Month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0
            }
        });
        $('#barchart_yearly_scanned').closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });
        $('.amcharts-chart-div').find('a').hide();         
      }
        <?php } ?>
        $(document).ready(function(){
        <?php if($this->years !='' && $this->months !='' && count($this->report_data)>0){?>
                 initbarChartMonthly();
        <?php }?>
        <?php if($this->years !='' && $this->months =='' && count($this->report_data)>0){?>
                 initbarChartYearly();
        <?php }?>
            $('.amcharts-chart-div').find('a').hide();               
            $("#btnreset").click(function() {
                $(this).closest('form').find("input[type=text], select").val("");
            });
        });

    </script>
<?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $carrier = '';
        $year = '';
        $month = '';
         // Geting a accounts from consgnment table
        $servicesFilterObj = new ServiceFilter();
        $servicesFilterObj->getUniqueCarrierfilter();
        $this->carrier_list = $servicesFilterObj->getColumnList('carrier');
        
        if (isset($_POST['carrier'])) {
            //echo $_POST['account'];exit;
            $carrier = $_POST['carrier'];
            $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH'] = $carrier;
        } else if (isset($_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH'])) {
            $carrier = $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH'];
        }
        $this->carrier = $carrier;

        if(isset($_POST['years'])){
            $year = $_POST['years'];
            $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH_YEAR'] = $year;
            
        }else if(isset($_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH_YEAR'])){
            $year = $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH_YEAR'];
        }
        $this->years = $year;

        if(isset($_POST['months'])){
            $month = $_POST['months'];
            $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH_MONTH'] = $month;
            
        }else if(isset($_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH_MONTH'])){
            $month = $_SESSION['ACCOUNT_REPORTING_GRAPH']['SERVICE_GRAPH_MONTH'];
        }
        $this->months = $month;
        
        $whereArr = array();
        $whereStr = "";
        
        $whereArr[] = "c.consignment_status NOT IN ('poland received', 'poland booked', 'recycled')";
        if (!empty($this->carrier))
            $whereArr[] = "s.carrier = '" . $this->carrier . "'";

        if (!empty($this->years))
            $whereArr[] = " YEAR(c.`date_scanned`) = '" . $year . "'";        

        if (!empty($this->months))
            $whereArr[] = " MONTH(c.`date_scanned`) = '" . $month . "'";        

        if (!empty($whereArr))
            $whereStr = implode(" AND ", $whereArr);
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        
        if(trim($carrier) != ''){
            $this->report_filter = new reportingfilter();
            $this->report_filter->setFilter($whereStr);
            if(trim($year) !='' && trim($month) == ''){
                $this->report_filter->setGroupBy('MONTH(c.`date_scanned`)');
                $this->report_filter->setOrderBy('MONTH(c.`date_scanned`) DESC');
            }else if(trim($year) !='' && trim($month) != ''){
                $this->report_filter->setGroupBy('DAY(c.`date_scanned`)');
                $this->report_filter->setOrderBy('DAY(c.`date_scanned`) ');
            }
            $this->report_data = $this->report_filter->getServicesTransactionGraphicsReport();
        
            $this->arry_months = array(
                                        '1'=>array('Month'=>1,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '2'=>array('Month'=>2,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '3'=>array('Month'=>3,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '4'=>array('Month'=>4,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '5'=>array('Month'=>5,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '6'=>array('Month'=>6,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '7'=>array('Month'=>7,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '8'=>array('Month'=>8,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '9'=>array('Month'=>9,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '10'=>array('Month'=>10,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '11'=>array('Month'=>11,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0),
                                        '12'=>array('Month'=>12,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0)
                                      );
            
            
            if($this->years !='' && $this->months ==''){
                ///print_r($this->report_data);
                if(count($this->report_data) > 0){
                    foreach($this->report_data as $data){
                        $this->arry_months[$data['monthly']] = array('Month'=>$data['monthly'],'Total' => $data['scanedTotal'], 'Unique' => $data['scanedUnique'], 'Duplicate' => $data['scanedTotal'] - $data['scanedUnique']);                          
                    }
                }
            }else if($this->years !='' && $this->months !=''){
                if(count($this->report_data) > 0){            
                    $daysInMonth =array();
                    $daysInMon = date('t', mktime(0, 0, 0, $this->months));
                    for($i = 1 ; $i <= $daysInMon; $i++){
                        $this->array_days[$i]  = array(
                                                'Day'=>$i,'Total' => 0, 'Unique' => 0, 'Duplicate' => 0
                                                );
                    }

                    foreach($this->report_data as $data){
                        $this->array_days[$data['day']] = array('Day'=>$data['day'],'Total' => $data['scanedTotal'], 'Unique' => $data['scanedUnique'], 'Duplicate' => $data['scanedTotal'] - $data['scanedUnique']);
                    }
                }
            }
        }
        
    }
}
/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
  