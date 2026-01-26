<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
/* * *
 * Controller logic
 */

public $user_parrent_child_list;
public $user_parrent_count;
public $total_pages;
public $current_page;
public $total_rec;

protected function init() {


Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);

// common stuff
$this->setTitle("User Shipments");
//
$user = Sessionmanager::getUser();
if (isset($_GET['parent_id']) && $_GET['parent_id'] != "") {
$UserId = $_GET['parent_id'];

$UserFilter = new UserAccountFilter();
if (isset($_GET['currentpage']) && $_GET['currentpage'] > 0) {
$offset = ($_GET['currentpage'] - 1) * 25;
} else {
$offset = 0;
}
$UserFilter->setOffset($offset);
$this->user_parrent_child_list = $UserFilter->getParentChildAccount($UserId);
$total_record = $UserFilter->getParentChildAccountCount($UserId);
$total_record = $total_record[0];
$this->total_rec = $total_record->getId();
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
} else {
util_redirect("account_summary.php");
}
}

protected function renderHead() {
?>
<script type="text/javascript">

</script>
<?php
}

/* * *
 * Content
 */

protected function renderBody() {
?>	
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption"> <i class="icon-docs"></i>Report List</div>
        <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
    </div>
    <div class="portlet-body">
        <div class="table-scrollable">
            <table   class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <?php if ($this->total_pages > 1) { ?>  
                    <tr>
                        <td  style="text-align:left;">
                            <ul class="pagination" style="width:80%; ">
                                <?php if (isset($_GET['currentpage']) && $_GET['currentpage'] > 1) { ?>

                                <li><a href="user_shipment.php?currentpage=<?php echo $this->current_page - 1; ?>&&<?php echo 'parent_id='.$_GET['parent_id']; ?>">&lt;&lt;</a></li>
                                <li><a class="prev" href="user_shipment.php?currentpage=1&&<?php echo 'parent_id='.$_GET['parent_id']; ?>">&lt;</a></li>
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
                                        ?> user_shipment.php?currentpage=<?php
                                    echo $i + 1;
                                    }
                                    ?>&&<?php echo 'parent_id='.$_GET['parent_id']; ?>"><?php echo $i + 1; ?></a></li>
                                <?php } if ($this->current_page < $this->total_pages) { ?>
                                <li><a class="next" href="user_shipment.php?currentpage=<?php echo $this->current_page + 1; ?>&&<?php echo 'parent_id='.$_GET['parent_id']; ?>">&gt;</a></li> 
                                <li><a class="next" href="user_shipment.php?currentpage=<?php echo $this->total_pages; ?>&&<?php echo 'parent_id='.$_GET['parent_id']; ?>">&gt;&gt;</a></li> 
<?php } ?>                             </ul>
                            <span  style="text-align:right;" >
                                <span> Total records <b><?php echo $this->total_rec; ?></b></span>
                            </span>
                        </td>
                    </tr>
<?php } ?>
                    <tr class="grid_header alert alert-info">
                        <th class="red-back">User Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($this->user_parrent_child_list as $user_parrent_child_list) {
                    ?>
                    <tr>
                        <td><?php echo $user_parrent_child_list->getFirstName(); ?></td>
                    </tr>
                    <?
                    }
                    ?>
                </tbody>
            </table>
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

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
