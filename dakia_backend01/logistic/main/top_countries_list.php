<?php
// get settings
require_once("../includes/settings/config.inc.php");



class Page extends BasePage {

    // consignment filter
    private $countries_data;
    // table description
    private $table_msg;
    private $return_data;

    /*     * *
     * Controller logic
     */

    protected function init() {

        $this->table_msg = "Invalid Command";

        // common initialisation for ths page
        $this->setTitle("Top Countries List");
        if ($this->table_msg == "")
            $this->table_msg = "Top Countries List";


        $userSection = SessionManager::getUser();
        $consignmentFilteter = new ConsignmentFilter();
        $consignmentFilteter->addAccountFilter($userSection->getUserAccount());
        $status_not_include = array('0', '', 'new', 'invalid', 'recycled', 'cancelled');
        $consignmentFilteter->addStatusFilterNotIn($status_not_include);

        $this->countries_data = $consignmentFilteter->getDashboardTopCoutry("50000");
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
            <li><a href="../main/top_countries_list.php">Top Countries</a></li>
            <li><a href="#">List</a></li>
        </ul>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Top Countries List 
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
                    <!-- describe table filter -->
                    <!-- CONSIGNMENT TABLE -->
                    <div id='table_container'  class="main_grid2">
                        <div class="table-scrollable">
                            <table id='consignment_list' class="table table-striped table-bordered table-advance table-hover" >
                                <!-- table head -->
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>Shipments</th>
                                    </tr>
                                </thead>
                                <!-- table head-->
                                <!-- table body -->
                                <tbody>
                                    <?php
                                    foreach ($this->countries_data as $countries_list) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $countries_list->getCountry(); ?>
                                            </td>
                                            <td>
                                                <?php echo $countries_list->getId(); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
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
