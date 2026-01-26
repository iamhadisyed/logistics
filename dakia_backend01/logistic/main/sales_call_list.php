<?php
// get settings
require_once("../includes/settings/config.inc.php");


class Page extends BasePage {
/* * *
 * Controller logic
 */

private $sales_call_data;
private $table_msg;
private $total_rec;
private $total_pages;
private $current_page;

protected function init() {
$this->table_msg = "Invalid Command";

// common initialisation for ths page
$this->setTitle("Sales Call List");
if ($this->table_msg == "")
$this->table_msg = "Sales Call List";

Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
$sessionUser = SessionManager::getUser();
// Get deparment For logged in User

if (isset($this->form_vars["form_action"]) && $this->form_vars["form_action"] == "SearchCall")
{

$SalesCallLogFilter = new SalesCallLogFilter();
$SalesCallLogFilter->addFieldFilter("userid", $sessionUser->getId());
if($this->form_vars["newcustomerlist"] != '' && isset($this->form_vars["newcustomerlist"]))
$SalesCallLogFilter->addFieldFilter("customer_code", $this->form_vars["newcustomerlist"]);
if($this->form_vars["date_from"] != '' && isset($this->form_vars["date_from"]) && $this->form_vars["date_to"] != '' && isset($this->form_vars["date_to"]))
$SalesCallLogFilter->addFilter('date_call >= "'.date("Y-m-d", strtotime($this->form_vars["date_from"])).'" AND date_call <= "'. date("Y-m-d", strtotime($this->form_vars["date_to"])).'"');


}
else if (isset($this->form_vars["form_action"]) && $this->form_vars["form_action"] == "export")
{
SalesCallLogReport::buildPDFDocuments($this->form_vars["date_from"], $this->form_vars["date_to"], $this->form_vars["newcustomerlist"]);
exit;
}
else
{
$SalesCallLogFilter = new SalesCallLogFilter();
$SalesCallLogFilter->addFieldFilter("userid", $sessionUser->getId());
}
if (isset($_GET['currentpage']) && $_GET['currentpage'] > 0) {
$offset = ($_GET['currentpage'] - 1) * 25;
$SalesCallLogFilter->setOffset($offset);
$SalesCallLogFilter->setRowsPerPage(25);
$this->sales_call_data = $SalesCallLogFilter->getPagingList();
} else {
$offset = 0;
$SalesCallLogFilter->setOffset($offset);
$SalesCallLogFilter->setRowsPerPage(25);
$this->sales_call_data = $SalesCallLogFilter->getPagingList();
}
$this->total_rec = $SalesCallLogFilter->getCount();

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

/* * *
 * Insert content into HEAD section of html page.
 */

public function renderHead() {
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#date_from').datepicker({dateFormat: "yy M d"});
        $('#date_to').datepicker({dateFormat: "yy M d"});

        $('#btnExport').click(function () {
            $('#form_action').val('export');
            $('#adminForm').submit();
        });

        $('#btnSearch').click(function () {
            $('#form_action').val('SearchCall');
            $('#adminForm').submit();
        });
    });
</script>
<?php
}

/* * *
 * Content View
 */

protected function renderBody() {
foreach ($this->form_vars as $key => $val) {
$$key = $val;
}
?>
<ul class="breadcrumb">
    <li><a href="../main/index.php"><?php echo Translation::GetCaption("HOME"); ?></a></li>
    <li><a href="../main/sales_call_list.php">SALES CALL LIST</a></li>
</ul>
<div class="main_formpage">
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
                                <?
                                $salescalllog = new SalesCallLogFilter();
                                $salescalllog->addgGroupBy("customer_code");
                                $customerlist = $salescalllog->getColumnList("customer_code, id");
                                $customercode = array();
                                if(count($customerlist) > 0)
                                {
                                foreach($customerlist as $customer)
                                {
                                $customercode[$customer->getCustomerCode()] = $customer->getCustomerCode();
                                }
                                }
                                $userFilter = new UserAccountFilter();
                                $userFilter->addActiveFlagFilter();
                                $userFilter->AddOrderByAccount();									
                                $userlist = $userFilter->getDistinctColumnList("user_account, id");

                                if(count($userlist) > 0)
                                {
                                $customercode[""] = "----ACTIVE CUSTOMER----";
                                foreach($userlist as $ulist)
                                {
                                $customercode[$ulist->getUserAccount()] = $ulist->getUserAccount();
                                }
                                }


                                ?>
                                <select id="newcustomerlist" name="newcustomerlist" class="form-control">
                                    <option value="">Select Customer</option>
                                    <option value="">----NOT ACTIVE CUSTOMER----</option>
                                    <?
                                    if(sizeof($customercode) > 0)
                                    {
                                    foreach($customercode as $key=>$cusList) 
                                    {
                                    $selected = ($this->old_customer == $key) ? " selected" : "";
                                    echo "<option ". $selected ." value='".$key."'>". $cusList . " </option>" ;
                                    }
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input  type="text" name="date_from" id="date_from" value="<?php echo @$date_from; ?>"  class="form-control"  placeholder="FROM DATE" rel="tooltip"/>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input  type="text" name="date_to" id="date_to" value="<?php echo @$date_to; ?>"  class="form-control"  placeholder="TO DATE" rel="tooltip"/>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <a id="btnSearch" href="#" class="btn btn-primary" ><span></span>Search</a>
                                <a id="btnExport" href="#" class="btn btn-primary" ><span></span>Export</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"> <i class="icon-list"></i>
                SALES CALL LIST
            </div>  
            <div class="actions">
                <a href="add_sales_call.php" class="btn btn-success">
                    ADD NEW SALES CALL
                </a>
            </div>
        </div>
        <div class="portlet-body">
            <?php
            if (count(ErrorList::getItem()->getErrorCount()) > 1) {

            echo '<div class="alert alert-danger">';
            ErrorList::getItem()->render();
            echo '</div>';
            }
            ?>
            <strong><?php echo sizeof($this->sales_call_data) . " Calls Listed" ?></strong>
            <div class="row">
                <div class="col-md-12" >

                    <?php if (!empty($this->sales_call_data)) { ?>

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

                                                <li><a href="sales_call_list.php?currentpage=<?php echo $this->current_page - 1; ?>">&lt;&lt;</a></li>
                                                <li><a class="prev" href="sales_call_list.php?currentpage=1">&lt;</a></li>
                                                <?php } ?>
                                                <?php for ($i = 0;
                                                $i < $this->total_pages;
                                                $i++) { ?>
                                                <li <?php
                                                if ($this->current_page == $i + 1) {
                                                echo "class='active'";
                                                }
                                                ?> ><a href="<?php
                                                        if ($this->current_page == $i + 1) {
                                                        echo "#";
                                                        } else {
                                                        ?> sales_call_list.php?currentpage=<?php
                                                    echo $i + 1;
                                                    }
                                                    ?>"><?php echo $i + 1; ?></a></li>
                                                <?php } if ($this->current_page < $this->total_pages) { ?>
                                                <li><a class="next" href="sales_call_list.php?currentpage=<?php echo $this->current_page + 1; ?>">&gt;</a></li> 
                                                <li><a class="next" href="sales_call_list.php?currentpage=<?php echo $this->total_pages; ?>">&gt;&gt;</a></li> 
<?php } ?>                             </ul>
                                        </td>
                                        <td colspan="3" style="text-align:right;" >
                                            <span> Total records <b><?php echo $this->total_rec; ?></b></span>
                                        </td>
                                    </tr>
<?php } ?>
                                    <tr>
                                        <th id="col1" class="bg-red">CALL DATE</th>
                                        <th id="col1" class="bg-red">CUSTOMER</th>
                                        <th id="col1" class="bg-red">COMPANY</th>
                                        <th id="col3" class="bg-red">TELEPHONE</th>
                                        <th id="col3" class="bg-red">EMAIL</th>
                                        <th id="col2" class="bg-red">RATES OFFERED</th>
                                        <th id="col3" class="bg-red">FOLLOWING MEETING DATE</th>
                                        <th id="col3" class="bg-red">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $helpDeskDataObj = "";
                                    Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
                                    $sessionUser = SessionManager::getUser();
                                    $count = 1;

                                    foreach ($this->sales_call_data as $value) {
                                    ?>
                                    <tr>
                                        <td><? echo date("Y-m-d",$value->getDateCall()); ?></td>
                                        <td><? echo $value->getCustomerCode(); ?></td>
                                        <td><? echo $value->getCompany(); ?></td>
                                        <td><? echo $value->getTelephone(); ?></td>
                                        <td><? echo $value->getEmail(); ?></td>


                                        <? if($value->getDocumentLink() != "") { ?>
                                        <td>	<a href="<? echo $value->getDocumentLink(); ?>">Download</a></td>
                                        <? }	else{ ?>
                                        <td>	No Rates Offered </td>
                                        <? } ?>

                                        <td><? 
                                            if($value->getFollowMeetingDate()!= '' && date("Y-m-d H:i:s",$value->getFollowMeetingDate()) != '0000-00-00 00:00:00')
                                            echo date("Y-m-d H:i:s", $value->getFollowMeetingDate());
                                            else
                                            echo "";
                                            ?></td>
                                        <td><a href="add_sales_call.php?id=<? echo $value->getId(); ?>">EDIT</a></td>
                                    </tr>
                                    <?

                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>  <!-- table_container -->
                    <?php
                    } else {
                    echo "<label>". Translation::GetCaption("NO_DATA_FOUND")." </label>";
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
