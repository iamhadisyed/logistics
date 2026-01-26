<?php
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Operations User Guide"
        );       
    }

    protected function addPagelavelCss() {
        
    }

    public function addPagelavelJs() {
        
    }

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-money"></i>
                    Operations User Guide
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                <iframe src="csv/SmartTrack_user_manual_for_operations.pdf" width="100%" height="768"></iframe>
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
    
     public function renderHead() {
        ?>
        <style type="text/css">
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                /* display: none; <- Crashes Chrome on hover */
                -webkit-appearance: none;
                margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
            }
            #paypal_btn{
                padding: 0px;
                display: none;
            }
        </style>
        <?php
    }

}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>