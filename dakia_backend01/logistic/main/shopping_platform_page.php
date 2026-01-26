<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $shopping_data;

    protected function init() {
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
        // common initialisation for ths page
        $this->setTitle("Shipping Integration");
        $Page_key = $_GET['key'];

        $ShoppingPlatformFilter = new ShoppingPlatformFilter();
        $ShoppingPlatformFilter->addPageKeyFilter($Page_key);
        $this->shopping_data = $ShoppingPlatformFilter->getList();
//        echo "<pre>";print_r($this->shopping_data);echo "</pre>"; die;
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $shopping_data = $this->shopping_data;
        $shopping_data = $shopping_data[0];
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/shipping_integration_page.php">Shipping Integration</a></li>
        </ul>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-cogs"></i>
                        One World Express <?php echo $shopping_data->getTitle(); ?> Integration
                    </div>                    
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">                            
                            <?php echo html_entity_decode($shopping_data->getDescription()); ?>                              
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
