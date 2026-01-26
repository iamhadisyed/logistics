<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
/* * *
 * Controller logic
 */

protected function init() {
// user must be CLIENT
Sessionmanager::checkUserAccess(USER::PRIVILEGE_USER_LIST);

$this->setTitle("Payment List");
}

/**
 * Page-specific buttons
 */
protected function renderHead() {
?>
<script type="text/javascript">

</script>
<?php
}

/* * *
 * Content View
 */

protected function renderBody() {
?>
<ul class="breadcrumb">
    <li><a href="../main/index.php">Home</a></li>
    <li><a href="../main/ViewPaymentHistory.php">Payment</a></li>
    <li><a href="#">List</a></li>
</ul>
<style>
    .row{
        margin-bottom:15px;
    }
</style>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption"> <i class="icon-users"></i>
            Payment Listings
        </div>
        <div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
    </div>
    <div class="portlet-body">

        <?php
        // transfer form variables into local values (form variables come from parent)
        //	foreach ($this->form_vars as $key=>$val) { $$key = $val; }
        // Get clients
        $payment_filter = new PaymentFilter();
        $payment_filter->addIsCompletedFilter(1);
        $payment_list = $payment_filter->getList();

//                echo "<pre>";
//                print_r($payment_list);
//                echo "</pre>";die;
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
        $rowsperpage = 25;
        }

        //print_r($_SESSION["consignment_filter"]);
        //$this->consignment_filter = $_SESSION["consignment_filter"];
        // get consignment count
        $numrows = $payment_filter->getPagingCount();

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

        $payment_filter->setRowsPerPage($rowsperpage);

        // the offset of the list, based on current page
        $payment_filter->setOffset($offset);

        // get consignment list to display

        $payment_list = $payment_filter->getPagingList();

        // page title
//                echo "<pre>";
//                print_r($payment_list);
//                echo "</pre>";die;
        ?>        

        <?php
        // report any errors
        errorList::getItem()->render();
        ?>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <div class="col-md-10">
            <?php
            if ($numrows > 25) {
            ?>
            <div  style="text-align:center;margin:9px;width:95%">


                <ul class="pagination" style="width:80%">   

                    Set Page Size &nbsp;&nbsp;
                    <select style="min width:0px; display:inline;min-width:10px;width:50px; margin-bottom:1px; padding:1px"  class="select_dropdown" name="ddlPageSize" onchange="SetPageSize();">
                        <option value="25" <?php if ($rowsperpage == 25) echo "selected='selected'" ?> >25</option>
                        <option value="50" <?php if ($rowsperpage == 50) echo "selected='selected'" ?> >50</option>
                        <option value="100" <?php if ($rowsperpage == 100) echo "selected='selected'" ?> >100</option>
                    </select>        




                    <?php
                    /*                     * ****  build the pagination links ***** */

                    // range of num links to show
                    $range = 3;

                    // if not on page 1, don't show back links
                    if ($currentpage > 1) {
                    // show << link to go back to page 1
                    echo " &nbsp;<li><a href='{$_SERVER['PHP_SELF']}?currentpage=1&pagesize=$rowsperpage'><<</a></li> ";
                    // get previous page num
                    $prevpage = $currentpage - 1;
                    // show < link to go back to 1 page
                    echo " &nbsp;<li><a class='prev' href='{$_SERVER['PHP_SELF']}?currentpage=$prevpage&pagesize=$rowsperpage'><</a></li> ";
                    } // end if
                    // loop to show links to range of pages around current page
                    for ($x = ($currentpage - $range);
                    $x < (($currentpage + $range) + 1);
                    $x++) {
                    // if it's a valid page number...
                    if (($x > 0) && ($x <= $totalpages)) {
                    // if we're on current page...
                    if ($x == $currentpage) {
                    // 'highlight' it but don't make a link
                    echo "<li class='active'><a href='#'><b>$x</b></a></li>";
                    // if not current page...
                    } else {
                    // make it a link
                    echo " &nbsp;<li><a  href='{$_SERVER['PHP_SELF']}?currentpage=$x&pagesize=$rowsperpage'>$x</a></li> ";
                    } // end else
                    } // end if
                    } // end for
                    // if not on last page, show forward and last page links
                    if ($currentpage != $totalpages) {
                    // get next page
                    $nextpage = $currentpage + 1;
                    // echo forward link for next page
                    echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?currentpage=$nextpage&pagesize=$rowsperpage'>></a></li> ";
                    // echo forward link for lastpage
                    echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?currentpage=$totalpages&pagesize=$rowsperpage'>>></a></li> ";
                    } // end if

                    echo "<li style='vertical-align:middle'><lable>Total records <b></label>$numrows</b>&nbsp;&nbsp; </li> 	";
                    ?>
                    <br class="clear" />
                </ul>

            </div>
            <div style='float:right;margin-top:10px;text-align:center'>

            </div>

            <? /*                                 * **** end build pagination links ***** */
            }
            ?>
        </div>
        <div class="table-scrollable">
            <table class='table table-striped table-bordered table-advance table-hover'>
                <thead>
                    <tr>
                        <th id="col2" class="bg-red">Date</th>
                        <th id="col2" class="bg-red">Payment Method</th>
                        <th id="col2" class="bg-red">Description</th>
                        <th id="col2" class="bg-red">Transaction</th>
                        <th id="col2" class="bg-red">Debit</th>
                        <th id="col2" class="bg-red">Credit</th>
                        <th id="col2" class="bg-red">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($payment_list as $payment) {
                    $balance +=$payment->getDr();
                    $balance -=$payment->getCr();
                    
                    $payment_method = new PaymentMethod($payment->getPaymentmethodId());
                    
                    
                    $amount_currencyid = $payment->getAmountCurrencyId();
                    $amount_currency = new Currency($amount_currencyid);
                    
                    $trn_currencyid = $payment->getCurrencyId();
                    $trn_currency = new Currency($trn_currencyid);
                    
                    ?>
                    <tr class="">
                        <td><?php echo date("D, M d, Y", strtotime($payment->getPaymentDate())); ?></td>
                        <td><?php echo $payment_method->getTitle(); ?></td>
                        <td><?php echo $payment->getPaymentDetail(); ?></td>
                        <td><?php echo $payment->getAmount().' '.$amount_currency->getRightsymbol(); ?></td>
                        <td><?php echo $payment->getDr().' '.$trn_currency->getRightsymbol(); ?></td>
                        <td><?php echo $payment->getCr().' '.$trn_currency->getRightsymbol(); ?></td>
                        <td><?php echo $balance.' '.$trn_currency->getRightsymbol(); ?></td>

                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}
}
/**
 * Override to show the menu
 *
 */
public function renderMenu() {
$menu = new Adminmenu(Adminmenu::CUSTOMERS);
$menu->render();
}

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?> 