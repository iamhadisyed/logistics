<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "User Manual"
        );
    }

    protected function renderBody() {
        ?>

        <div class="portlet light portlet-fit bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class=" icon-layers font-green"></i>
                    <span class="caption-subject font-green bold uppercase">User Manual</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="mt-element-card mt-card-round mt-element-overlay">
                    <div class="row">
                        <?php
                        if (Permissions::checkFilePermission('admin_user_guide')) {
                            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="mt-card-item">
                                    <div class="mt-card-avatar mt-overlay-1">
                                        <img src="../assets/pages/img/avatars/question-mark.png" />
                                        <div class="mt-overlay">
                                            <ul class="mt-info">

                                                <li>
                                                    <a class="btn default btn-outline" href="administration_user_guide.php">
                                                        <i class="icon-link"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-card-content">
                                        <h3 class="mt-card-name">Admin User Guide</h3>
                                     <!--    <p class="mt-card-desc font-grey-mint">Creative Director</p> -->

                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        if (Permissions::checkFilePermission('client_user_guide')) {
                            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="mt-card-item">
                                    <div class="mt-card-avatar mt-overlay-1">
                                        <img src="../assets/pages/img/avatars/question-mark.png" />
                                        <div class="mt-overlay">
                                            <ul class="mt-info">

                                                <li>
                                                    <a class="btn default btn-outline" href="client_user_guide.php">
                                                        <i class="icon-link"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-card-content">
                                        <h3 class="mt-card-name">Client User Guide</h3>
                                     <!--    <p class="mt-card-desc font-grey-mint">Creative Director</p> -->

                                    </div>
                                </div>
                            </div>

            <?php
        }
        if (Permissions::checkFilePermission('corporate_user_guide')) {
            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="mt-card-item">
                                    <a href="operations_user_guide.php"></a>
                                    <div class="mt-card-avatar mt-overlay-1">
                                        <img src="../assets/pages/img/avatars/question-mark.png" />
                                        <div class="mt-overlay">
                                            <ul class="mt-info">

                                                <li>
                                                    <a class="btn default btn-outline" href="corporate_user_guide.php">
                                                        <i class="icon-link"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-card-content">
                                        <h3 class="mt-card-name">Corporate User Guide</h3>
                                     <!--    <p class="mt-card-desc font-grey-mint">Creative Director</p> -->

                                    </div>
                                </div>
                            </div>
            <?php
        }
        if (Permissions::checkFilePermission('finance_user_guide')) {
            ?>                                        

                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="mt-card-item">
                                    <div class="mt-card-avatar mt-overlay-1">
                                        <img src="../assets/pages/img/avatars/question-mark.png" />
                                        <div class="mt-overlay">
                                            <ul class="mt-info">

                                                <li>
                                                    <a class="btn default btn-outline" href="finance_user_guide.php">
                                                        <i class="icon-link"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-card-content">
                                        <h3 class="mt-card-name">Finance User Guide</h3>
                                     <!--    <p class="mt-card-desc font-grey-mint">Creative Director</p> -->

                                    </div>
                                </div>
                            </div>
            <?php
        }
        if (Permissions::checkFilePermission('operation_user_guide')) {
            ?>                                     

                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="mt-card-item">
                                    <div class="mt-card-avatar mt-overlay-1">


                                        <img src="../assets/pages/img/avatars/question-mark.png" />
                                        <div class="mt-overlay">
                                            <ul class="mt-info">

                                                <li>
                                                    <a class="btn default btn-outline" href="operations_user_guide.php">
                                                        <i class="icon-link"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-card-content">
                                        <h3 class="mt-card-name">Operations User Guide</h3>
                                     <!--    <p class="mt-card-desc font-grey-mint">Creative Director</p> -->

                                    </div>
                                </div>
                            </div>
        <?php } ?>


                    </div>
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