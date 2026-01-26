<?php
// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    public function renderBody() {
        ?>        
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-cogs"></i>Add Balance / Top-up Account </div>
                <div class="tools"> <a href="javascript:;" class="collapse"> </a><a href="" class="fullscreen" data-original-title="" title="">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="note note-info">
                            Account topup payment canceled.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
     public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }

    public function renderHead() {
        
    }
    public function init() {
        $this->setTitle("Admin - Top Up Account Cancel");
    }
}
/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>