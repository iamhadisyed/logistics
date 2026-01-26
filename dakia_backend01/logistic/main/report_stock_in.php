<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");
include_classes([   
                    'generalreporting.class',
                    'warehouse.class',
                    'warehousefilter.class']);
// set up local page class
class Page extends BasePage {

    private $report_filter;
    private $report_data;
    private $warehouse_list;
    private $warehouse_id;
    private $rack_id;
    private $rack_shelf_id;
    private $user;
    private $customer;
    private $goods_name;
    private $tracking_number;
    private $date_from;
    private $date_to;
    private $whereStr;
    private $sql;

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

        $totalSql = "SELECT 
                        COUNT(lrs.id) AS total
                    FROM
                        log_rack_shelf lrs 
                        JOIN `rack_shelf` rs 
                          ON rs.id = lrs.`rack_shelf_id` 
                        JOIN rack r 
                          ON r.`id` = rs.`rack_id`
                        JOIN warehouse w 
                          ON w.`id` = r.`warehouse_id`  
                        JOIN `rack_shelf_item` rsi
                          ON rsi.`id` = lrs.`rack_shelf_item_id`
                        JOIN `user` u1
                          ON u1.`id` = lrs.`in_by`                        
                        JOIN `user` cust
                          ON cust.`id` = lrs.`customer_id`
                      WHERE lrs.`out_date` IS NULL " . $this->whereStr;

        ///////////////////////////////////////////

        $numrows = GeneralReporting::getTotalRecordsFromSql($totalSql);

        if (isset($_POST['ddlPageSize'])) {
            $_SESSION['ddlPageSize'] = $_POST['ddlPageSize'];
            $rowsperpage = $_SESSION['ddlPageSize'];
            $_GET['pagesize'] = $rowsperpage;
        } else if (isset($_GET['pagesize'])) {
            $_SESSION['ddlPageSize'] = $_GET['pagesize'];
            $rowsperpage = $_SESSION['ddlPageSize'];
        } else if (isset($_SESSION['ddlPageSize'])) {
            $rowsperpage = $_SESSION['ddlPageSize'];
        } else {
            $rowsperpage = 10;
        }
        //$rowsperpage = 1;
        // get consignment count
        $totalpages = ceil($numrows / $rowsperpage);
        // get the current page or set a default
        if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
            // cast var as int
            $currentpage = (int) $_GET['currentpage'];
        } else {
            // default page num
            $currentpage = 1;
        } // end if
        // if current page is greater than total pages...
        if ($currentpage > $totalpages) {
            // set current page to last page
            $currentpage = $totalpages;
        } // end if
        // if current page is less than first page...
        if ($currentpage < 1) {
            // set current page to first page
            $currentpage = 1;
        } // end if
        // the offset of the list, based on current page
        $offset = ($currentpage - 1) * $rowsperpage;

        $SQL = $this->sql . " LIMIT " . $offset . "," . $rowsperpage;
		
		


        $this->report_data = GeneralReporting::getReportFromSql($SQL);
        ?>
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <h3 class="page-title">Stock In Report</h3>
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="warehouse_id" id="warehouse_id" class="form-control">
                                    <option value="">-- Warehouse --</option>
                                    <?php
                                    if (count($this->warehouse_list) > 0) {
                                        foreach ($this->warehouse_list as $warehouse) {
                                            echo '<option value="' . $warehouse->getId() . '"' . ($warehouse->getId() == $this->warehouse_id ? ' selected="selected"' : '') . '>' . $warehouse->getWarehouseName() . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="rack_id" id="rack_id" class="form-control">
                                    <option value="">-- Rack --</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="rack_shelf_id" id="rack_shelf_id" class="form-control">
                                    <option value="">-- Rack Shelf--</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="user" id="user" value="<?php echo @$this->user; ?>" class="form-control" placeholder="User" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="customer" id="customer" value="<?php echo @$this->customer; ?>" class="form-control" placeholder="Customer" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <input type="text" name="tracking_number" id="tracking_number" 
                                value="<?php echo @$this->tracking_number; ?>" class="form-control" 
                                placeholder="Tracking Number" />
                            </div>
                        </div> 
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" name="goods_name" id="goods_name" value="<?php echo @$this->goods_name; ?>" class="form-control" placeholder="Item Name" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_from" id="date_from" type="text" placeholder="Date Form" value="<?php echo @$this->date_from; ?>" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_to" id="date_to" type="text" placeholder="Date To" value="<?php echo @$this->date_to; ?>" />
                            </div>
                        </div>                                          
                        
                    </div>
                    <div class="row">
                    	 <div class="col-md-12">
                            <div class="form-group">
                                <input id="form_action" type="hidden" name="form_action">
                                <button type="submit" name="btnSearch" class="btn btn-primary">Search</button>
                                <button id="btnExport" class="btn btn-primary" name="btnExport" type="button">Export</button>
                            </div>
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
           <div class="table-scrollable">
                    <div class="table-bordered">                        
                        <table class="table table-striped table-bordered table-advance table-hover">
                            <thead>                                
                                <tr>
                                    <th class="red-back">Warehouse</th>
                                    <th class="red-back">Rack</th>
                                    <th class="red-back">Shelf</th>
                                    <th class="red-back">Customer</th>
                                    <th class="red-back">Tracking Number</th>
                                    <th class="red-back">In Date</th>
                                    <th class="red-back">Item</th>
                                    <th class="red-back">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($this->report_data) > 0) {
                                    foreach ($this->report_data as $report_data) {
                                        ?>        
                                        <tr>                                            
                                            <td><?php echo $report_data['warehouse_name']; ?></td>
                                            <td><?php echo $report_data['title']; ?></td>
                                            <td><?php echo $report_data['short_title'] ." ". ($report_data['shelf_no']+1); ?></td>
                                            <td><?php echo $report_data['customer_name']; ?></td>
                                            <td><?php echo $report_data['tracking_number']; ?></td>
                                            <td><?php echo $report_data['in_date'] . "<br /><em><b>By:</b> " . $report_data['in_by_user'] . "</em>"; ?></td>                                            
                                            <td><?php echo $report_data['goods_name'] . "<br /><em><b>Dim: </b> " . $report_data['dimension'] . " <b>Weight: </b> " . $report_data['weight'] . "Kg</em>"; ?></td>
                                            <td><?php echo $report_data['description']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr><td colspan="5"> No record found</td></tr>      
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if (count($this->report_data) > 0) {
                            ?>
                            <div class="">
                                <div class="col-md-8">
                                    <?php
                                    if ($numrows > 10) {
                                        ?>
                                        <ul class="pagination">
                                            <?php
                                            /**                                             * ***  build the pagination links ***** */
                                            // range of num links to show
                                            $range = 3;
                                            // if not on page 1, don't show back links
                                            if ($currentpage > 1) {
                                                // show << link to go back to page 1
                                                echo " &nbsp;<li><a href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=1&pagesize=$rowsperpage'><<</a></li> ";
                                                // get previous page num
                                                $prevpage = $currentpage - 1;
                                                // show < link to go back to 1 page
                                                echo " &nbsp;<li><a class='prev' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$prevpage&pagesize=$rowsperpage'><</a></li> ";
                                            } // end if
                                            // loop to show links to range of pages around current page
                                            for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                                                // if it's a valid page number...
                                                if (($x > 0) && ($x <= $totalpages)) {
                                                    // if we're on current page...
                                                    if ($x == $currentpage) {
                                                        // 'highlight' it but don't make a link
                                                        echo "<li class='active'><a href='#'><b>$x</b></a></li>";
                                                        // if not current page...
                                                    } else {
                                                        // make it a link
                                                        echo " &nbsp;<li><a  href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$x&pagesize=$rowsperpage'>$x</a></li> ";
                                                    } // end else
                                                } // end if
                                            } // end for
                                            // if not on last page, show forward and last page links
                                            if ($currentpage != $totalpages) {
                                                // get next page
                                                $nextpage = $currentpage + 1;
                                                // echo forward link for next page
                                                echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$nextpage&pagesize=$rowsperpage'>></a></li> ";
                                                // echo forward link for lastpage
                                                echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$totalpages&pagesize=$rowsperpage'>>></a></li> ";
                                            } // end if
                                            //echo "<li style='vertical-align:middle'>Total records <b>$numrows</b>&nbsp;&nbsp;</li>";
                                            ?>
                                        </ul>
                                        <?php
                                        /**                                         * *** end build pagination links ***** */
                                    }
                                    ?>
                                </div>
                                <div class="col-md-4" style="text-align: right;margin-top: 10px;">
                                    View&nbsp;<select class="select_dropdown form-control input-sm" style="display: inline-block; width: 65px;" name="ddlPageSize" onchange="SetPageSize();">
                                        <option value="10" <?php if ($rowsperpage == 10) echo "selected='selected'" ?> >10</option>
                                        <option value="25" <?php if ($rowsperpage == 25) echo "selected='selected'" ?> >25</option>
                                        <option value="50" <?php if ($rowsperpage == 50) echo "selected='selected'" ?> >50</option>
                                        <option value="100" <?php if ($rowsperpage == 100) echo "selected='selected'" ?> >100</option>
                                    </select>&nbsp;records | Found Total <?php echo $numrows; ?> records 
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="clearfix"></div>
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
            function SetPageSize() {
                document.getElementById('adminForm').submit();
            }
            $(document).ready(function() {
                $("#date_from").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
                $("#date_to").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
                
                $("#btnSearch").click(function(){
                    $("#form_action").val("Search");
                    $("#adminForm").submit();
                });
                $("#btnExport").click(function(){
                    $("#form_action").val("Export");
                    $("#adminForm").submit();
                });
                
                $("#rack_id").change(function(){
                    var rack_id = $(this).val();                    
                    $.post("report_stock_in.php",{func:'getrack_shelfs',rack_id:rack_id},function(data){
                        $("#rack_shelf_id").html(data); 
                        $("#rack_shelf_id").val('<?php echo $this->rack_shelf_id;?>');
                    });
                });                
                $("#warehouse_id").change(function(){
                    var warehouse_id = $(this).val();                    
                    $.post("report_stock_in.php",{func:'getwarehouse_racks',warehouse_id:warehouse_id},function(data){
                        $("#rack_id").html(data); 
                        $("#rack_id").val('<?php echo $this->rack_id;?>');
                        $("#rack_id").trigger("change");
                    });
                });
                $("#warehouse_id").trigger("change");
            });
        </script>
        <?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        $this->setTitle("Admin - Rack Shelf Report");
        /* ------------------------------------------------------------------------------ */
        if(isset($_POST['func']) && $_POST['func'] == 'getwarehouse_racks'){
            $output = '<option value="">-- Rack--</option>';
            $warehouse_id = $_POST['warehouse_id'];
            
            $RFobj = new RackFilter();
            $RFobj->addFilter("r.warehouse_id='".$warehouse_id."'");
            $racks = $RFobj->getColumnList("r.title, r.short_title");
            if(count($racks) > 0){
                foreach($racks as $rack){
                    $output .= '<option value="'.$rack->getId().'">'.$rack->getTitle().' ['.$rack->getShortTitle().']</option>';
                }
            }
            echo $output;
            exit;
        }
        if(isset($_POST['func']) && $_POST['func'] == 'getrack_shelfs'){
            $output = '<option value="">-- Rack Shelf--</option>';
            $rack_id = $_POST['rack_id'];
            $RObj = new Rack($rack_id);
            
            $RSObj = new RackShelfFilter();
            $RSObj->addFilter("rs.rack_id='".$rack_id."'");
            $rackShelfs = $RSObj->getColumnList("rs.shelf_no");
            if(count($rackShelfs) > 0){
                foreach($rackShelfs as $shelf){
                    $output .= '<option value="'.$shelf->getId().'">'.$RObj->getShortTitle().($shelf->getShelfNo()+1).'</option>';
                }
            }
            echo $output;
            exit;
        }
        
        $WObj = new WarehouseFilter();
        $this->warehouse_list = $WObj->getColumnList("w.warehouse_name");
               
        // get report
        $rack_idp = '';
        $warehouse_idp = '';
        $rack_shelf_idp = '';
        $goods_namep = '';
        $userp = '';
        $customerp = '';
        $date_fromp = '';
        $date_top = '';

        if (isset($_POST['warehouse_id'])) {
            $warehouse_idp = htmlspecialchars($_POST['warehouse_id']);
            $_SESSION['REPORT_STOCK_IN']['WAREHOUSE_ID'] = $warehouse_idp;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['WAREHOUSE_ID'])) {
            $warehouse_idp = $_SESSION['REPORT_STOCK_IN']['WAREHOUSE_ID'];
        }
        $this->warehouse_id = $warehouse_idp;
        
        if (isset($_POST['rack_id'])) {
            $rack_idp = htmlspecialchars($_POST['rack_id']);
            $_SESSION['REPORT_STOCK_IN']['RACK_ID'] = $rack_idp;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['RACK_ID'])) {
            $rack_idp = $_SESSION['REPORT_STOCK_IN']['RACK_ID'];
        }
        $this->rack_id = $rack_idp;

        if (isset($_POST['rack_shelf_id'])) {
            $rack_shelf_idp = htmlspecialchars($_POST['rack_shelf_id']);
            $_SESSION['REPORT_STOCK_IN']['RACK_SHELF_ID'] = $rack_shelf_idp;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['RACK_SHELF_ID'])) {
            $rack_shelf_idp = $_SESSION['REPORT_STOCK_IN']['RACK_SHELF_ID'];
        }
        $this->rack_shelf_id = $rack_shelf_idp;

        if (isset($_POST['goods_name'])) {
            $goods_namep = htmlspecialchars($_POST['goods_name']);
            $_SESSION['REPORT_STOCK_IN']['GOODS_NAME'] = $goods_namep;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['GOODS_NAME'])) {
            $goods_namep = $_SESSION['REPORT_STOCK_IN']['GOODS_NAME'];
        }
        $this->goods_name = $goods_namep;
		
		if (isset($_POST['tracking_number'])) {
            $tracking_number = htmlspecialchars($_POST['tracking_number']);
            $_SESSION['REPORT_STOCK_IN']['TRACKING_NUMBER'] = $tracking_number;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['TRACKING_NUMBER'])) {
            $tracking_number = $_SESSION['REPORT_STOCK_IN']['TRACKING_NUMBER'];
        }
        $this->tracking_number = $tracking_number;
		
		
		

        if (isset($_POST['user'])) {
            $userp = htmlspecialchars($_POST['user']);
            $_SESSION['REPORT_STOCK_IN']['USER'] = $userp;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['USER'])) {
            $userp = $_SESSION['REPORT_STOCK_IN']['USER'];
        }
        $this->user = $userp;
        
        if (isset($_POST['customer'])) {
            $customerp = htmlspecialchars($_POST['customer']);
            $_SESSION['REPORT_STOCK_IN']['CUSTOMER'] = $customerp;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['CUSTOMER'])) {
            $customerp = $_SESSION['REPORT_STOCK_IN']['CUSTOMER'];
        }
        $this->customer = $customerp;

        if (isset($_POST['date_from'])) {
            $date_fromp = htmlspecialchars($_POST['date_from']);
            $_SESSION['REPORT_STOCK_IN']['DATE_FROM'] = $date_fromp;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['DATE_FROM'])) {
            $date_fromp = $_SESSION['REPORT_STOCK_IN']['DATE_FROM'];
        }
        $this->date_from = $date_fromp;

        if (isset($_POST['date_to'])) {
            $date_top = htmlspecialchars($_POST['date_to']);
            $_SESSION['REPORT_STOCK_IN']['DATE_TO'] = $date_top;
        } else if (isset($_SESSION['REPORT_STOCK_IN']['DATE_TO'])) {
            $date_top = $_SESSION['REPORT_STOCK_IN']['DATE_TO'];
        }
        $this->date_to = $date_top;
        
        $whereArr = array();
        $whereStr = "";

        if (!empty($this->warehouse_id) && is_numeric($this->warehouse_id)) {
            $whereArr[] = "r.warehouse_id = '" . DbAccess3::escape($this->warehouse_id ). "'";
        }
        if (!empty($this->rack_id) && is_numeric($this->rack_id)) {
            $whereArr[] = "rs.rack_id = '" .DbAccess3::escape( $this->rack_id ). "'";
        }
        if (!empty($this->rack_shelf_id) && is_numeric($this->rack_shelf_id)) {
            $whereArr[] = "lrs.rack_shelf_id = '" . DbAccess3::escape($this->rack_shelf_id) . "'";
        }
        if (!empty($this->user)) {
            $whereArr[] = "u1.full_name LIKE '%" . DbAccess3::escape($this->user) . "%' OR u1.user_account  LIKE '%" . DbAccess3::escape($this->user) . "%'";
        }
        if (!empty($this->customer)) {
            $whereArr[] = "(cust.full_name LIKE '%" . DbAccess3::escape($this->customer) . "%' OR cust.user_account LIKE '%" . DbAccess3::escape($this->customer) . "%')";
        }
		if (!empty($this->tracking_number)) {
            $whereArr[] = "rsi.tracking_number = '" . DbAccess3::escape($this->tracking_number) . "'";
        }
        if (!empty($this->goods_name)) {
            $whereArr[] = "rsi.goods_name LIKE '%" . DbAccess3::escape($this->goods_name) . "%'";
        }
        if (!empty($this->user)) {
            $whereArr[] = "u1.full_name LIKE '%" . DbAccess3::escape($this->user) . "%'";
        }
        if (!empty($this->date_from)) {
            $whereArr[] = "DATE(lrs.in_date) >= '" . $this->date_from . "'";
        }
        if (!empty($this->date_to)) {
            $whereArr[] = "DATE(lrs.in_date) <= '" . $this->date_to . "'";
        }
        if (!empty($whereArr))
            $whereStr = implode(" AND ", $whereArr);
        if (!empty($whereStr))
            $whereStr = " AND " . $whereStr;

        $this->whereStr = $whereStr;
		
		/*

        $sql = "SELECT
                    w.`warehouse_name`,
                    r.`id`,
                    r.`title`,
                    r.`short_title`,
                    rs.`shelf_no`,
                    lrs.`in_date`,
                    lrs.`in_by`,
                    u1.`full_name` AS in_by_user,
                    lrs.`out_date`,
                    lrs.`out_by`,
                    cust.`full_name` AS customer_name,
                    cust.`id` AS customer_id,
                    lrs.`remarks`,
                    rsi.`goods_name`,
                    rsi.`dimension`,
                    rsi.`weight`,
                    rsi.`description`,
					rsi.`tracking_number`
                  FROM
                    log_rack_shelf lrs
                    JOIN `rack_shelf` rs
                      ON rs.id = lrs.`rack_shelf_id`
                    JOIN rack r
                      ON r.`id` = rs.`rack_id`
                    JOIN warehouse w
                      ON w.`id` = r.`warehouse_id`
                    JOIN `rack_shelf_item` rsi
                      ON rsi.`id` = lrs.`rack_shelf_item_id`
                    JOIN `user` u1
                      ON u1.`id` = lrs.`in_by`
                    JOIN `user` cust
                      ON cust.`id` = lrs.`customer_id`
                  WHERE lrs.`out_date` IS NULL " . $whereStr . "
                  ORDER BY lrs.`in_date` DESC, w.id, r.id, rs.id";
				  
		*/
		
		 $sql = "SELECT
                    w.`warehouse_name`,
                    r.`id`,
                    r.`title`,
                    r.`short_title`,
                    rs.`shelf_no`,
                    lrs.`in_date`,
                    lrs.`in_by`,
                    (select user_account from user where id = `in_by`) AS in_by_user,
					(select account from consignment where awb = '" .  $this->tracking_number . "' limit 1) AS customer_name,
                    lrs.`out_date`,
                    lrs.`out_by`,                   
                    lrs.`remarks`,
                    rsi.`goods_name`,
                    rsi.`dimension`,
                    rsi.`weight`,
                    rsi.`description`,
					rsi.`tracking_number`
                  FROM
                    log_rack_shelf lrs
                    JOIN `rack_shelf` rs
                      ON rs.id = lrs.`rack_shelf_id`
                    JOIN rack r
                      ON r.`id` = rs.`rack_id`
                    JOIN warehouse w
                      ON w.`id` = r.`warehouse_id`
                    JOIN `rack_shelf_item` rsi
                      ON rsi.`id` = lrs.`rack_shelf_item_id`                    
                  WHERE lrs.`out_date` IS NULL " . $whereStr . "
                  ORDER BY lrs.`in_date` DESC, w.id, r.id, rs.id";
				  
				  
		
		
		
		
		$this->sql = $sql;

      
        
        if(isset($_POST['form_action']) && $_POST['form_action'] == 'Export'){
            $reportData = GeneralReporting::getReportFromSql($this->sql);
            $csvContent = "Warehouse,Rack,Shelf,In Date,In By,Item Name,Tracking No, Dimension,Weight,Description"."\r\n";
            if(count($reportData) > 0){
                foreach ($reportData as $rpt){
                    $csvContent .= $rpt['warehouse_name'].",".$rpt['title'].",".$rpt['short_title'] ." ". ($rpt['shelf_no']+1).",".$rpt['in_date'].",".$rpt['in_by_user'].",".$rpt['goods_name'].",".$rpt['tracking_number'].",".$rpt['dimension'].",".$rpt['weight']."Kg".",".$rpt['description']."\r\n";
                }
            }
            echo $csvContent;
            $uniqueFileName = "report-stock-in"."-".time();
            header("Content-Type: application/csv");
            header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");
            exit;
        }
        
        $this->report_filter = new GeneralReporting();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>