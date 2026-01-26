<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $user = null;
    private $user_filter;

    protected function init() {

    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {

    }

    public function addPagelavelJs() {

    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
    ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-commenting"></i>
                        Chat Agent
                </div>
                <div class="actions"></div>
            </div>
            <div class="portlet-body">
                <iframe src="https://s2.mylivechat.com/webconsole/" frameborder="0" border="0" width="100%" height="800"></iframe>
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

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>