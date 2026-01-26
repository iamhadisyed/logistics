<?php
// get settings
require_once("../includes/settings/config.inc.php");
class Page extends BasePage {

    // consignment filter
    private $consignment_filter;
    // table description
    private $table_msg;
    private $num_valid; // number of valid consignments.
    private $column_name;
    private $sortby;
    private $flag;

    /*     * *
     * Controller logic
     */

    protected function init() {
        $user = SessionManager::getUser();
        if ($user->getUserType() != "admin") {
            util_redirect("index.php");
        }
        $this->setTitle("Admin - View Language");
    }

    public function renderBody() {


        $this->language = trim($_POST["language"]);

        $user = Sessionmanager::getUser();

        $languageFilter = new LanguageFilter();

        if ($this->language != '') {
            $languageFilter->addLanguageFilter($this->language);
        }

        $languageFilter->AddOrderByLanguage();

        $numrows = $languageFilter->getPagingCount();

        //echo $numrows;
        //die;
        //$numrows = GeneralReporting::getTotalRecordsFromSql($totalSql);

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

            //echo "currrrent page " . $_GET['currentpage'];
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
        //echo "current page final value " . $currentpage;
        //$_GET['currentpage']


        $offset = ($currentpage - 1) * $rowsperpage;

        //echo  $offset . " " . $rowsperpage;
        //$SQL = $this->sql . " LIMIT " . $offset . "," . $rowsperpage;
        //$this->report_data = GeneralReporting::getReportFromSql($SQL);
        //$languageFilter = new LanguageKeysFilter();		
        $languageFilter->setOffset($offset);
        $languageFilter->setRowsPerPage($rowsperpage);



        /* $locationFilter->AddActiveFilter("Y");
          $locationFilter->AddTypeFilter("L"); */

        /* if(isset($_POST["name"]) && $_POST["name"] != '')
          {
          $name = trim($_POST["name"]);
          $locationFilter->addLocationNameLikeFilter($name);
          } */

        //$locationFilter->AddWarehouseIdFilter($user->getWarehouseId());

        $this->report_data = $languageFilter->getPagingColumnList("id, language, date_created, created_by");

        //print_r($this->report_data);
        //die;		
        ?>
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <h3 class="page-title">Languages</h3>
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
                        <div class="col-md-12">                       

                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="language" id="language" size="100" value="<?php echo htmlspecialchars($this->language); ?>" class="form-control" placeholder="Language" />
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <input id="form_action" type="hidden" name="form_action">
                                    <button type="submit" name="btnSearch" class="btn btn-primary">Search</button>

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
                                        <th class="red-back">Language</th>                                    
                                        <th class="red-back">Date Created</th>
                                        <th class="red-back">Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
        <?php
        if (count($this->report_data) > 0) {
            foreach ($this->report_data as $report_data) {
                ?>        
                                            <tr>                           

                                                <td><?php /* $warehouse = new Warehouse($report_data->getWarehouseId()); */
                echo $report_data->getLanguage();
                ?></td>

                                                <td><?php echo formatDateTime($report_data->getDateCreated()); ?></td>
                                                <td><?php echo "<a target='_blank' href='add_language.php?id=" . $report_data->getId() .
                "'>Edit</a>";
                ?></td>
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
                                                /**                                                 * *** end build pagination links ***** */
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

    public function renderHead() {
        ?>
            <script type="text/javascript">
                function SetPageSize() {
                    document.getElementById('adminForm').submit();
                }

            </script>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function populateLanguageDropDown() {
        
    }

    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
