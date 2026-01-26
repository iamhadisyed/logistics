<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $error_message = null;
    public $success_message = null;
    public $shopping_platform = null;

    protected function init() {
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $sessionUser = SessionManager::getUser();
        if (isset($_GET['id']) && $_GET['id'] != "" && isset($_GET['action'])) {
            $action = "";
            $md5Id = "";
            $action = $_GET['action'];
            $md5Id = $_GET['id'];
            $ShoppingPlatformFilter = new ShoppingPlatformFilter();
            $ShoppingPlatformFilter->addMd5IdByFilter($md5Id);
            $ShoppingPlatformFilter->addIsDeleteNotFilter();
            $data = $ShoppingPlatformFilter->getList();
            if (!empty($data) && $action == "Delete") {
                $data = $data[0];
                $ShoppingPlatform = new ShoppingPlatform($data->getId());
                $ShoppingPlatform->setIsDelete("1");
                $ShoppingPlatform->save();
                $this->success_message = "Your record is deleted successfully";
            } else {
                $this->error_message = "invalid command";
            }
        }

if(isset($_GET['new']) && $_GET['new']=='added'){
    $this->success_message = "New record has been added successfully";
}
        // common initialisation for ths page
        $this->setTitle("Shopping Platform List");
        $ShoppingPlatformFilter = new ShoppingPlatformFilter();
        $ShoppingPlatformFilter->addIsDeleteNotFilter();
        $this->shopping_platform = $ShoppingPlatformFilter->getList();
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/shopping_platform.php">Shopping Platform</a></li>
            <li><a href="../main/shopping_platform_list.php">List</a></li>
        </ul>
        <?php if (!empty($this->error_message)) { ?>
            <div class="alert alert-danger">
                <strong>Warning!</strong> <?php echo $this->error_message; ?>
            </div>
        <?php
        }
        else if (!empty($this->success_message)) {
            ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo $this->success_message; ?>
            </div>
        <?php } ?>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Shopping Platform List 
                    </div>                    
                </div>
                <div class="portlet-body">
                    <!-- describe table filter -->
                    <!-- CONSIGNMENT TABLE -->
                    <div id='table_container'  class="main_grid2">
                        <div class="table-scrollable">
                            <table id='consignment_list' class="table table-striped table-bordered table-advance table-hover" >
                                <!-- table head -->
                                <thead>
                                    <tr>
                                        <th>SR#</th> 
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Active</th>
                                        <th>Added By</th>
                                        <th>Added Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!-- table head-->
                                <!-- table body -->
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($this->shopping_platform as $shopping_platform) {
                                        ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo $shopping_platform->getTitle(); ?></td>
                                            <td><?php echo $shopping_platform->getDescription(); ?></td>
                                            <td><?php echo ($shopping_platform->getIsActive() == 1 ? "Active" : "Not Active"); ?></td>
                                            <td><?php
                                                $User = new CustomerAccount($shopping_platform->getAddedBy());
                                                echo $User->getFirstName() . " [" . $User->getUserName() . "]";
                                                ?></td>
                                            <td><?php
                                                $date = new DateTime();
                                                $date->setTimestamp($shopping_platform->getAddedDate());
                                                echo $date->format('Y-m-d H:i:s');
                                                ?></td>
                                            <td><a href="shopping_platform.php?id=<?php echo md5($shopping_platform->getId()); ?>&action=Edit" class="btn default btn-xs purple">
                                                    <i class="fa fa-edit"></i> Edit </a> | <a onclick="return confirm('Are you sure you want to delete record?');" href="shopping_platform_list.php?id=<?php echo md5($shopping_platform->getId()); ?>&action=Delete" class="btn default btn-xs black">
                                                    <i class="fa fa-trash-o"></i> Delete </a></td>
                                        </tr>
                                        <?php
                                        $count ++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>  <!-- table_container -->
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
