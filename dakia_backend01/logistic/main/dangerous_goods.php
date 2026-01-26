<?php
// get settings
require_once("../includes/settings/config.inc.php");
     
class Page extends BasePage {

    public $shopping_data;
    private $user = null;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            '' => 'Dangerous Goods'        );

        $this->user = SessionManager::getUser();
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        
    }

    public function renderFooter() {
        ?>
      
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
   ?>

        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-plug"></i>
                         Dangerous Goods
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="scroller" data-rail-color="blue" data-handle-color="blue">
                        <div class="row">
                          <div class="col-md-12">
                            <?= Translation::GetCaption("DANGEROUS_GOODS_TEXT"); ?>
                          </div>
                        </div>
                      </div>
                </div>
            </div>
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
