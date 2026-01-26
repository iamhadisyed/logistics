<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $group;

    protected function init() {
       $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("401_PAGE")
        );
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        ?>

        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/pages/css/error.min.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>       
            <div class="row">
                <div class="col-md-12 page-500">
                    <div class=" number font-red"> 500 </div>
                    <div class=" details">
                        <h3><?php echo Translation::GetCaption("401_ERROR_MESSAGE"); ?></h3>
                        <p> <?php echo Translation::GetCaption("401_ERROR_MESSAGE_DETAILS"); ?>
                            <br> </p>
                        <p>
                            <a href="index.php" class="btn red btn-outline"> <?php echo Translation::GetCaption("RETURN_HOME"); ?> </a>
                            <br> </p>
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

    public function renderFooter() {
        ?>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
