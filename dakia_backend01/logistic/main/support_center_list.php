<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $ticket_data;
    private $table_msg;
    private $total_rec;
    private $total_pages;
    private $current_page;

    protected function init() {
        $this->table_msg = "Invalid Command";

        // common initialisation for ths page
        $this->setTitle("Tickets List");
        if ($this->table_msg == "")
            $this->table_msg = "Tickets List";

        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $sessionUser = SessionManager::getUser();
        // Get deparment For logged in User
        if ($sessionUser->getUserType() == "admin") {


            $UserDepartmentFilter = new UserDepartmentFilter();
            $UserDepartmentFilter->addByUserId($sessionUser->getId());
            $department_list = $UserDepartmentFilter->getList();
            $department_str = "";
            foreach ($department_list as $value) {
                $department_str .= $value->getDepartmentId() . ",";
            }
            $department_str = rtrim($department_str, ',');
            $Hfilter = new HelpDeskTicketFilter(); // $sessionUser->getId()
            $Hfilter->addDepartmentInByFilter($department_str);
            $Hfilter->AddOrderByAddedDate($ascending = false);
            if (isset($_GET['currentpage']) && $_GET['currentpage'] > 0) {
                $offset = ($_GET['currentpage'] - 1) * 25;
                $Hfilter->setOffset($offset);
                $this->ticket_data = $Hfilter->getPagingList();
            } else {
                $offset = 0;
                $Hfilter->setOffset($offset);
                $this->ticket_data = $Hfilter->getPagingList();
            }
            $this->total_rec = $Hfilter->getCount();
        } else {
            $Hfilter = new HelpDeskTicketFilter(); // $sessionUser->getId()
            $Hfilter->addAddedByFilter($sessionUser->getId());
            $Hfilter->AddOrderByAddedDate($ascending = false);
            if (isset($_GET['currentpage']) && $_GET['currentpage'] > 0) {
                $offset = ($_GET['currentpage'] - 1) * 25;
                $Hfilter->setOffset($offset);
                $this->ticket_data = $Hfilter->getPagingList();
            } else {
                $offset = 0;
                $Hfilter->setOffset($offset);
                $this->ticket_data = $Hfilter->getPagingList();
            }
            $this->total_rec = $Hfilter->getCount();
        }
        //Pagenation 
        $rowsperpage = 25;
        $numrows = $this->total_rec;
        $this->total_pages = ceil($numrows / $rowsperpage);

        // get the current page or set a default
        if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
            // cast var as int
            $currentpage = (int) $_GET['currentpage'];
            $this->current_page = $currentpage;
        } else {
            // default page num
            $currentpage = 1;
            $this->current_page = $currentpage;
        } // end if
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                //                $('input').tooltip();
                //                $('select').tooltip();
                //                $('textarea').tooltip();
                //                $('a').tooltip();
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="../main/index.php"><?php echo Translation::GetCaption("HOME"); ?></a>
                <i class="fa fa-circle"></i>
            </li>
            <li><a href="../main/support_center.php"><?php echo Translation::GetCaption("SUPPORT_CENTER"); ?></a>
                <i class="fa fa-circle"></i>
            </li>
            <li><a href="../main/support_center_list.php"><?php echo Translation::GetCaption("TICKET_LIST"); ?></a></li>
        </ul>
        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                         <?php echo Translation::GetCaption("TICKET_LIST"); ?>

                    </div>  
                    <!--<div class="actions">
                        <a href="support_center.php" class="btn btn-success">
                    <?php // echo Translation::GetCaption("ADD_NEW_TICKET"); ?>
                        </a>
                    </div>-->
                </div>
                <div class="portlet-body">
                    <?php
                    if (count(ErrorList::getItem()->getErrorCount()) > 1) {

                        echo '<div class="alert alert-danger">';
                        ErrorList::getItem()->render();
                        echo '</div>';
                    }
                    ?>
                    <!--                    <div class="row" > 
                                            <div class="col-md-3" style="text-align:center; float: right;"><br />
                                                <strong class="cnls"><?php echo sizeof($this->ticket_data) . " Tickets Listed" ?></strong>
                                                <br>
                                                <input name="searcfield" type="text" id="searcfield" placeholder="Search Tracking/ID#"  class="form-control"  />
                                            </div>
                                        </div>-->
                    <div class="row">
                        <div class="col-md-12" >

                            <?php if (!empty($this->ticket_data)) { ?>

                                <!-- describe table filter -->
                                <!-- CONSIGNMENT TABLE -->
                                <div id='table_container'  class="main_grid2">
                                    <div class="table-scrollable">
                                        <table class='table table-striped table-bordered table-advance table-hover'>
                                            <thead>
                                                <?php if ($this->total_pages > 1) { ?>  
                                                    <tr>
                                                        <td colspan="3" style="text-align:left;">
                                                            <ul class="pagination" style="width:80%; ">
                                                                <?php if (isset($_GET['currentpage']) && $_GET['currentpage'] > 1) { ?>

                                                                    <li><a href="support_center_list.php?currentpage=<?php echo $this->current_page - 1; ?>">&lt;&lt;</a></li>
                                                                    <li><a class="prev" href="support_center_list.php?currentpage=1">&lt;</a></li>
                                                                <?php } ?>
                                                                <?php for ($i = 0; $i < $this->total_pages; $i++) { ?>
                                                                    <li <?php
                                                                    if ($this->current_page == $i + 1) {
                                                                        echo "class='active'";
                                                                    }
                                                                    ?> ><a href="<?php
                                                                            if ($this->current_page == $i + 1) {
                                                                                echo "#";
                                                                            } else {
                                                                                ?> support_center_list.php?currentpage=<?php
                                                                            echo $i + 1;
                                                                        }
                                                                        ?>"><?php echo $i + 1; ?></a></li>
                                                                    <?php } if ($this->current_page < $this->total_pages) { ?>
                                                                    <li><a class="next" href="support_center_list.php?currentpage=<?php echo $this->current_page + 1; ?>">&gt;</a></li> 
                                                                    <li><a class="next" href="support_center_list.php?currentpage=<?php echo $this->total_pages; ?>">&gt;&gt;</a></li> 
                                                                <?php } ?>                             </ul>
                                                        </td>
                                                        <td colspan="3" style="text-align:right;" >
                                                            <span> Total records <b><?php echo $this->total_rec; ?></b></span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <th id="col1" class="bg-red"><?php echo Translation::GetCaption("TICKET_ID"); ?></th>
                                                    <th id="col1" class="bg-red"><?php echo Translation::GetCaption("SUBJECT"); ?></th>
                                                    <th id="col1" class="bg-red"><?php echo Translation::GetCaption("LAST_REPLIER"); ?></th>
                                                    <th id="col3" class="bg-red"><?php echo Translation::GetCaption("DEPARTMENT"); ?></th>
                                                    <th id="col3" class="bg-red"><?php echo Translation::GetCaption("STATUS"); ?></th>
                                                    <th id="col2" class="bg-red"><?php echo Translation::GetCaption("DATE"); ?></th>
                                                    <!--<th id="col3" class="bg-red">Priority</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $helpDeskDataObj = "";
                                                Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
                                                $sessionUser = SessionManager::getUser();
                                                $count = 1;
                                                foreach ($this->ticket_data as $value) {
                                                    //Get Client ID
                                                    $ClientId = $value->getAddedBy();

                                                    //Get logged IN User Data From session Class
                                                    $HelpDeskTicketFilter = new HelpDeskTicketMessageFilter();
                                                    $HelpDeskTicketFilter->addTicketIdFilter($value->getId());
                                                    $HelpDeskTicketFilter->AddOrderByAddedDate($ascending = false);
                                                    $helpDeskDataObj = $HelpDeskTicketFilter->getColumnList('addedby', '1');
                                                    ?>

                                                    <tr class="<?php
                                                    echo $count;
                                                    $count++;
                                                    ?>" <?php
                                                    if (is_array($helpDeskDataObj) && !empty($helpDeskDataObj)) {
                                                        //For Admin
                                                        if ($sessionUser->getUserType() == "admin" && $helpDeskDataObj[0]->getAddedBy() == $ClientId && $value->getStatus() != "close") {
                                                            echo 'style="background-color: rgba(230, 173, 176, 0.5)"';
                                                        }

                                                        //For Client
                                                        elseif ($sessionUser->getUserType() != "admin" && $helpDeskDataObj[0]->getAddedBy() != $ClientId && $value->getStatus() != "close") {
                                                            echo 'style="background-color: rgba(230, 173, 176, 0.5)"';
                                                        }
                                                    }
                                                    ?> >
                                                        <td><a href="ticket_details.php?id=<?php echo md5($value->getId()); ?>" ><?php echo $value->getTicketCode(); ?></a></td>
                                                        <td <?php if ($value->getStatus() == "close") { ?> style="text-decoration: line-through;" <?php } ?> ><?php echo $value->getSubject(); ?></td>
                                                        <td>
                                                            <?php
                                                            if (is_array($helpDeskDataObj) && !empty($helpDeskDataObj)) {
                                                                $User = new CustomerAccount($helpDeskDataObj[0]->getAddedBy());
                                                                echo $User->getFirstName() . " [" . $User->getUserName() . "]";
                                                            }
                                                            ?>  
                                                        </td>
                                                        <td><?php
                                                            $department = new Department($value->getDepartmentId());
                                                            echo ucwords($department->getTitle());
                                                            ?></td>
                                                        <td><?php echo ucwords(str_replace("_", " ", $value->getStatus())); ?></td>
                                                        <td><?php
                                                            $date = new DateTime();
                                                            $date->setTimestamp($value->getAddedDate());
                                                            echo $date->format('Y-m-d H:i:s');
                                                            ?></td>
                                                    </tr>

            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>  <!-- table_container -->
                                <?php
                            } else {
                                echo "<label>No data Found</label>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
            </div>  <!-- table_container -->
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
