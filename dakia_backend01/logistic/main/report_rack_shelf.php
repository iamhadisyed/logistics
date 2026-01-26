<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

        include_classes([   
                    'warehouse.class',
                    'warehousefilter.class',
                    'rack.class',
                    'rackfilter.class',
                    'rackshelf.class',
                    'rack.class',
                    'rackfilter.class',
					'generalreporting.class'
                ]);
// set up local page class
class Page extends BasePage {

    private $report_filter;
    private $report_data;
    private $type;
    private $user;
    private $date_from;
    private $date_to;
    private $shelf_id;
    private $rack;
    private $rack_shelf;    
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
        
        /*
        $totalSql = "SELECT 
                    COUNT(lrs.id) AS total
                  FROM
                    log_rack_shelf lrs 
                    JOIN `rack_shelf_item` rsi 
                      ON rsi.`id` = lrs.`rack_shelf_item_id`
                      JOIN `user` u1 ON u1.`id` = lrs.`in_by`
                      JOIN `user` u2 ON u2.`id` = lrs.`out_by`
                  WHERE lrs.`rack_shelf_id` = '".DbAccess3::escape($this->shelf_id)."' ".$this->whereStr;        
				  
		*/
		
		$totalSql = "SELECT 
                    COUNT(lrs.id) AS total
                  FROM
                    log_rack_shelf lrs 
                    JOIN `rack_shelf_item` rsi 
                      ON rsi.`id` = lrs.`rack_shelf_item_id`                     
                  WHERE lrs.`rack_shelf_id` = '".DbAccess3::escape($this->shelf_id)."' ".$this->whereStr;     
				  
				  
		//echo $totalSql;
        
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
        //$offset = ($currentpage - 1) * $rowsperpage;
	
		
        
        $SQL = $this->sql." LIMIT ".$currentpage.",".$rowsperpage;
		
		//echo $SQL;
        
        $this->report_data = GeneralReporting::getReportFromSql($SQL);
        
        ?>
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <h3 class="page-title">Rack Shelf Report [<?php echo $this->rack." - ".$this->rack_shelf;?>]</h3>
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
                            <div class="form-group">
                                <select name="type" id="type" class="form-control">
                                    <option value="">-- Type --</option>
                                    <option value="in"<?php echo ($this->type == 'in' ? ' selected="selected"' : ''); ?>>In</option>
                                    <option value="out"<?php echo ($this->type == 'out' ? ' selected="selected"' : ''); ?>>Out</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="user" id="user" value="<?php echo @$this->user; ?>" class="form-control" placeholder="User" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_from" id="date_from" type="text" placeholder="Date Form" value="<?php echo @$this->date_from; ?>" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_to" id="date_to" type="text" placeholder="Date To" value="<?php echo @$this->date_to; ?>" />
                            </div>
                        </div>                        
                        <div class="col-md-2">
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
                <div class="scroller" style="min-height:200px;" data-rail-color="blue" data-handle-color="blue"> 
                    <?php
                    if($this->shelf_id > 0){
                    ?>
                    <div class="table-bordered">                        
                        <table class="table table-striped table-bordered table-advance table-hover">
                            <thead>                                
                                <tr>
                                    <th>In Date</th>
                                    <th>Out Date</th>
                                    <th>Item</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($this->report_data) > 0) {
                                    foreach ($this->report_data as $report_data) {
                                        $scanned_by = ($report_data['scanned_by'] != "" ? $report_data['scanned_by'] : 'Anonymous');
                                        ?>        
                                        <tr>                                            
                                            <td><?php echo $report_data['in_date']."<br /><em><b>By:</b> ".$report_data['in_by_user']."</em>"; ?></td>
                                            <td><?php echo $report_data['out_date']."<br /><em><b>By:</b> ".$report_data['out_by_user']."</em>"; ?></td>
                                            <td><?php echo $report_data['goods_name']."<br /><em><b>Dim: </b> ".$report_data['dimension']." <b>Weight: </b> ".$report_data['weight']."Kg</em>"; ?></td>
                                            <td><?php echo $report_data['remarks']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }else{
                                ?>
                                        <tr><td colspan="4"> No record found</td></tr>       
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
                                        /**                                         * ***  build the pagination links ***** */
                                        // range of num links to show
                                        $range = 3;
                                        // if not on page 1, don't show back links
                                        if ($currentpage > 1) {
                                            // show << link to go back to page 1
                                            echo " &nbsp;<li><a href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=1&pagesize=$rowsperpage&id=".$_GET["id"]."'><<</a></li> ";
                                            // get previous page num
                                            $prevpage = $currentpage - 1;
                                            // show < link to go back to 1 page
                                            echo " &nbsp;<li><a class='prev' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$prevpage&pagesize=$rowsperpage&id=".$_GET["id"]."'><</a></li> ";
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
                                                    echo " &nbsp;<li><a  href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$x&pagesize=$rowsperpage&id=".$_GET["id"]."'>$x</a></li> ";
                                                } // end else
                                            } // end if
                                        } // end for
                                        // if not on last page, show forward and last page links
                                        if ($currentpage != $totalpages) {
                                            // get next page
                                            $nextpage = $currentpage + 1;
                                            // echo forward link for next page
                                            echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$nextpage&pagesize=$rowsperpage&id=".$_GET["id"]."'>>></a></li>";
                                            // echo forward link for lastpage
                                            echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$totalpages&pagesize=$rowsperpage&id=".$_GET["id"]."'>>></a></li> ";
                                        } // end if
                                        //echo "<li style='vertical-align:middle'>Total records <b>$numrows</b>&nbsp;&nbsp;</li>";
                                        ?>
                                    </ul>
                                    <?php
                                    /**                                     * *** end build pagination links ***** */
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
                    <?php
                    }
                    ?>
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
        // get report
        $this->shelf_id = (isset($_GET['id']) && !empty($_GET['id']) ? $_GET['id'] : 0);
        
        
        $typep = '';
        $userp = '';
        $date_fromp = '';
        $date_top = '';

        if (isset($_POST['type'])) {
            $typep = $_POST['type'];
            $_SESSION['REPORT_RACK_SHELF']['TYPE'] = $typep;
        } else if (isset($_SESSION['REPORT_RACK_SHELF']['TYPE'])) {
            $typep = $_SESSION['REPORT_RACK_SHELF']['TYPE'];
        }
        $this->type = $typep;

        if (isset($_POST['user'])) {
            $userp = $_POST['user'];
            $_SESSION['REPORT_RACK_SHELF']['USER'] = $userp;
        } else if (isset($_SESSION['REPORT_RACK_SHELF']['USER'])) {
            $userp = $_SESSION['REPORT_RACK_SHELF']['USER'];
        }
        $this->user = $userp;

        if (isset($_POST['date_from'])) {
            $date_fromp = $_POST['date_from'];
            $_SESSION['REPORT_RACK_SHELF']['DATE_FROM'] = $date_fromp;
        } else if (isset($_SESSION['REPORT_RACK_SHELF']['DATE_FROM'])) {
            $date_fromp = $_SESSION['REPORT_RACK_SHELF']['DATE_FROM'];
        }
        $this->date_from = $date_fromp;

        if (isset($_POST['date_to'])) {
            $date_top = $_POST['date_to'];
            $_SESSION['REPORT_RACK_SHELF']['DATE_TO'] = $date_top;
        } else if (isset($_SESSION['REPORT_RACK_SHELF']['DATE_TO'])) {
            $date_top = $_SESSION['REPORT_RACK_SHELF']['DATE_TO'];
        }
        $this->date_to = $date_top;
        
//        if (empty($this->date_from))
//            $this->date_from = date("Y-m-d");
//        if (empty($this->date_to))
//            $this->date_to = date("Y-m-d");

        $whereArr = array();
        $whereStr = "";        
           
        if (!empty($this->user)){
            if(!empty($this->type) && $this->type == 'in')
                $whereArr[] = "u1.full_name LIKE '%" . DbAccess3::escape($this->user) . "%'";
            else if(!empty($this->type) && $this->type == 'out')
                $whereArr[] = "u2.full_name LIKE '%" . DbAccess3::escape($this->user) . "%'";
            else
                $whereArr[] = "(u1.full_name LIKE '%" . DbAccess3::escape($this->user) . "%' OR u2.full_name LIKE '%" . DbAccess3::escape($this->user) . "%')";
        }        
        if (!empty($this->date_from)){
            if(!empty($this->type) && $this->type == 'in')
                $whereArr[] = "DATE(lrs.in_date) >= '" . $this->date_from . "'";
            else if(!empty($this->type) && $this->type == 'out')
                $whereArr[] = "DATE(lrs.out_date) >= '" . $this->date_from . "'";
            else
                $whereArr[] = "(DATE(lrs.in_date) >= '" . $this->date_from . "' OR DATE(lrs.out_date) >= '" . $this->date_from . "')";
        }
        if (!empty($this->date_to)){
            if(!empty($this->type) && $this->type == 'in')
                $whereArr[] = "DATE(lrs.in_date) <= '" . $this->date_to . "'";
            else if(!empty($this->type) && $this->type == 'out')
                $whereArr[] = "DATE(lrs.out_date) <= '" . $this->date_to . "'";
            else
                $whereArr[] = "(DATE(lrs.in_date) <= '" . $this->date_to . "' OR DATE(lrs.out_date) <= '" . $this->date_to . "')";            
        }
        if (!empty($whereArr))
            $whereStr = implode(" AND ", $whereArr);
        if (!empty($whereStr))
            $whereStr = " AND ".$whereStr;
        
        $this->whereStr = $whereStr;
        
		/*
        $sql = "SELECT 
                    lrs.`in_date`,
                    lrs.`in_by`,
                    u1.`full_name` AS in_by_user,
                    lrs.`out_date`,
                    lrs.`out_by`,
                    u2.`full_name` AS out_by_user,
                    lrs.`remarks`,
                    rsi.`goods_name`,
                    rsi.`dimension`,
                    rsi.`weight`,
                    rsi.`description` 
                  FROM
                    log_rack_shelf lrs 
                    JOIN `rack_shelf_item` rsi 
                      ON rsi.`id` = lrs.`rack_shelf_item_id`
                      JOIN `user` u1 ON u1.`id` = lrs.`in_by`
                      JOIN `user` u2 ON u2.`id` = lrs.`out_by`
                  WHERE lrs.`rack_shelf_id` = '".DbAccess3::escape($this->shelf_id)."' ".$whereStr."
                  ORDER BY lrs.id DESC";
				  
		*/
		
		$sql = "SELECT 
                    lrs.`in_date`,
                    lrs.`in_by`,
                    (select user_account from user where id = `in_by`) AS in_by_user,
                    lrs.`out_date`,
                    lrs.`out_by`,
                    (select user_account from user where id = `out_by`) AS out_by_user,
					(select account from consignment where awb = `tracking_number` limit 1) AS account,
                    lrs.`remarks`,
                    rsi.`goods_name`,
                    rsi.`dimension`,
                    rsi.`weight`,
                    rsi.`description`,
					rsi.`tracking_number` 
                  FROM
                    log_rack_shelf lrs 
                    JOIN `rack_shelf_item` rsi 
                      ON rsi.`id` = lrs.`rack_shelf_item_id`                
                  WHERE lrs.`rack_shelf_id` = '".DbAccess3::escape($this->shelf_id)."' ".$whereStr."
                  ORDER BY lrs.id DESC";
        
        $this->sql = $sql;
        
        $SHObj = new RackShelf($this->shelf_id);
        $shelf_no = $SHObj->getShelfNo();
        $rack_id = $SHObj->getRackId();
        
        $RObj = new Rack($rack_id);
        $rack_title = $RObj->getTitle();
        $rack_short_title = $RObj->getShortTitle();
        
        $this->rack = $rack_title;
        $this->rack_shelf = $rack_short_title." ".$shelf_no;
        
        $this->report_filter = new GeneralReporting();     
        if(isset($_POST['form_action']) && $_POST['form_action'] == 'Export'){
            $reportData = GeneralReporting::getReportFromSql($this->sql);
            $csvContent = "In Date, Out Date, In By, Out By, Customer, Tracking Number, Item Name,Dimension,Weight,Remarks"."\r\n";
            if(count($reportData) > 0){
                foreach ($reportData as $rpt){
                    $csvContent .= $rpt['in_date'].",".$rpt['out_date'].",".$rpt['in_by_user'].",".$rpt['out_by_user'].",".$rpt['account'].",".$rpt['tracking_number'].",".$rpt['goods_name'].",".$rpt['dimension'].",".$rpt['weight']."Kg".",".$rpt['remarks']."\r\n";
                }
            }
            echo $csvContent;
            $uniqueFileName = "report-rack-shelf"."-".time();
            header("Content-Type: application/csv");
            header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");
            exit;
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
