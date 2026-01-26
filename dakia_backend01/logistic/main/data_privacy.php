<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    private $report_filter;
    private $date_scanned_from;
    private $date_scanned_to;
    private $where = "";
    private $join = "";

    /*     * *
     * Set the page header
     * @return void
     */

    public function getTitle() {
        return "Admin - Index";
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->

        <div class="row">
            <div class="col-md-8">
                <h3 class="page-title"><?= Translation::GetCaption("DATA_PRIVACY"); ?></h3>
            </div>
        </div>
        <div class="portlet box blue">

            <div class="portlet-body">
                <div class="scroller" data-rail-color="blue" data-handle-color="blue">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo nl2br(Translation::GetCaption("DATA_PRIVACY_TEXT")); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- END PAGE CONTENT-->
            <?php
        }

        /**
         * Override to show the menu
         *
         */
        public function renderMenu() {
            $menu = new Adminmenu(Adminmenu::DASHBOARD);
            $menu->render();
        }

        public function renderHead() {
            ?>
            <?php
        }

        /*         * *
         * Controller logic goes here
         */

        public function init() {
            
        }

    }

    /* ------------------------------------------------------------------------------ */
    // create and render page
    $PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
    $PageObj->show();
    ?>
