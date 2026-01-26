<?php
// get settings 
require_once("../includes/settings/config.inc.php");
include_classes([
    'marketplaces.class',
    'marketplacesfilter.class',
    ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $error_message = null;
    public $success_message = null;
    public $markiting_place = null;

    protected function init() {
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $sessionUser = SessionManager::getUser();
        if (isset($_GET['id']) && $_GET['id'] != "" && isset($_GET['action'])) {
            $action = "";
            $md5Id = "";
            $action = $_GET['action'];
            $md5Id = $_GET['id'];
            $MarketPlacesFilter = new MarketPlacesFilter();
            $MarketPlacesFilter->addMd5IdByFilter($md5Id);
            $MarketPlacesFilter->addIsDeleteNotFilter();
            $data = $MarketPlacesFilter->getList();
            if (!empty($data) && $action == "Delete") {
                $data = $data[0];
                $MarketPlaces = new MarketPlaces($data->getId());
                $MarketPlaces->setIsDelete("1");
                $MarketPlaces->save();
                $this->success_message = "Your record is deleted successfully";
            } else {
                $this->error_message = "invalid command";
            }
        }

if(isset($_GET['new']) && $_GET['new']=='added'){
    $this->success_message = "New record has been added successfully";
}
        // common initialisation for ths page
        $this->setTitle("Market Places List");
        $MarketPlacesFilter = new MarketPlacesFilter();
        $MarketPlacesFilter->addIsDeleteNotFilter();
        $this->markiting_place = $MarketPlacesFilter->getList();
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
            <li><a href="../main/market_places.php">Market Places</a></li>
            <li><a href="../main/market_places_list.php">List</a></li>
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
                        Market Places List 
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
                                    foreach ($this->markiting_place as $markiting_place) {
                                        ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo $markiting_place->getTitle(); ?></td>
                                            <td><?php echo $markiting_place->getDescription(); ?></td>
                                            <td><?php echo ($markiting_place->getIsActive() == 1 ? "Active" : "Not Active"); ?></td>
                                            <td><?php
                                                $User = new CustomerAccount($markiting_place->getAddedBy());
                                                echo $User->getFirstName() . " [" . $User->getUserName() . "]";
                                                ?></td>
                                            <td><?php
                                                $date = new DateTime();
                                                $date->setTimestamp($markiting_place->getAddedDate());
                                                echo $date->format('Y-m-d H:i:s');
                                                ?></td>
                                            <td><a href="market_places.php?id=<?php echo md5($markiting_place->getId()); ?>&action=Edit" class="btn default btn-xs purple">
                                                    <i class="fa fa-edit"></i> Edit </a> | <a onclick="return confirm('Are you sure you want to delete record?');" href="market_places_list.php?id=<?php echo md5($markiting_place->getId()); ?>&action=Delete" class="btn default btn-xs black">
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
