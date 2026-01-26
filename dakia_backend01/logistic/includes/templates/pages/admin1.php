<?php
$UserSessionManager = SessionManager::getUser();
$userAccountID = $UserSessionManager->getUserAccountId();
$userAccountDetail = new CustomerAccount($userAccountID);

$this->auditLogs();
?>
<!DOCTYPE html>
<!--
=========================================================
* Material Dashboard 2 - v3.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2023 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8"/>
    <?php $this->renderMetaTags(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="Preview page of Metronic Admin Theme #4 for manage products" name="description"/>
    <meta content="One World Express - Irshad Ali" name="author"/>

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/images/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link rel="apple-touch-icon" sizes="57x57" href="/images/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/images/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/images/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/images/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/images/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/images/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <!--<link rel="manifest" href="/manifest.json">-->

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= BASE_URL ?>assets/common/css/common.css" rel="stylesheet" type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/css/bootstrap-float-label.min.css" rel="stylesheet" type="text/css"/>

    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <?php $this->addPagelavelCss(); ?>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="<?= BASE_URL ?>assets/global/css/components-rounded.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="<?= BASE_URL ?>assets/layouts/layout4/css/layout.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= BASE_URL ?>assets/layouts/layout4/css/themes/default.min.css" rel="stylesheet" type="text/css"
          id="style_color"/>
    <link href="<?= BASE_URL ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= BASE_URL ?>assets/global/plugins/jquery-idle-timeout-plus/jquery-idle-timeout-plus.css"
          rel="stylesheet" type="text/css"/>

    <?php
    $userAccountId = $UserSessionManager->getUserAccountId();
    if ($userAccountId > 0) {
        $useraccountData = new CustomerAccount($userAccountId);
        if($useraccountData->getThemeId() > 0) {
            $theme = new Themes($useraccountData->getThemeId());
            if(!empty($theme->getStyleSheet()) && file_exists(BASE_PATH.'assets/layouts/layout4/css/'.$theme->getStyleSheet())){
                echo '<link href="' . BASE_URL . 'assets/layouts/layout4/css/' . $theme->getStyleSheet().'" rel="stylesheet" type="text/css" />';
            }
            if(file_exists(BASE_PATH.'assets/layouts/layout4/css/custom.'.$theme->getSlug().'.min.css')) {
                echo '<link href="' . BASE_URL . 'assets/layouts/layout4/css/custom.' . $theme->getSlug() . '.min.css" rel="stylesheet" type="text/css" />';
            }
        } else {
            echo '<link href="' . BASE_URL . 'assets/layouts/layout4/css/custom.css" rel="stylesheet" type="text/css" />';
        }
    }
    ?>
    <!--        >-->
    <!-- END THEME LAYOUT STYLES -->
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
    <!-- END THEME STYLES -->
    <?php $this->renderHead(); ?>
    <style type="text/css">
        #idle-timeout-dialog {
            z-index: 99999999 !important;
        }
    </style>
</head>
<!-- END HEAD -->
<body class="page-container-bg-solid page-header-fixed ">
    <?php  if (isset($_SESSION['admin'])) { ?>
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="index.php?menu-option=<?php echo $UserSessionManager->getUserType(); ?>">
                <img src="<?php echo User::getUserCompanyImages(); ?>"
                     class="img-rounded img-responsive white-back logo-default" alt="Smarttrack"/>
            </a>
            <div class="menu-toggler sidebar-toggler">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
           data-target=".navbar-collapse"> </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE ACTIONS -->
        <!-- DOC: Remove "hide" class to enable the page header actions -->

        <!-- END PAGE ACTIONS -->
        <!-- BEGIN PAGE TOP -->
        <div class="page-top">

            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <!-- DOC: Apply "dropdown-hoverable" class after "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                    <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->

                    <!-- END NOTIFICATION DROPDOWN -->
                    <li class="separator hide"></li>
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <!-- DOC: Apply "dropdown-hoverable" class after "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                    <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->
                    <?php
                    $invoiceQueryFilter = new InvoiceFilter();
                    $invoiceQueryFilter->where(['inv.is_email' => "1", 'inv.is_read' => "0", 'inv.user_account_id' => $UserSessionManager->getUserAccountId()]);
                    $invoiceQueryFilter->groupBy("inv.invoice_type");
                    $invoiceMaxIdObj = $invoiceQueryFilter->getList('count(inv.id) as id, invoice_type');
                    $numberOfInvoices = 0;
                    if (count($invoiceMaxIdObj) > 0) {
                        $numberOfInvoices += $invoiceMaxIdObj[0]->getId();
                    }
                    /*  foreach ($invoiceFilterObj as $key => $obj) {
                          $account = new CustomerAccount($obj->getUserAccountId());
                      }*/

                    if ($numberOfInvoices > 0)        :

                        ?>
                        <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">

                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               data-close-others="true">
                                <i class="icon-bell"></i>
                                <span class="badge badge-danger"> <?php echo $numberOfInvoices ?> <pending></pending></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">

                                <li class="external">
                                    <h3>
                                        <span class="bold"><?php echo $numberOfInvoices ?> pending</span> notifications
                                    </h3>
                                    <a href="invoice_list.php">view all</a>
                                </li>
                                <?php
                                foreach ($invoiceMaxIdObj as $invoiceItems) {
                                    ?>
                                    <li>
                                        <ul class="dropdown-menu-list scroller" style="height: 250px;"
                                            data-handle-color="#637283">
                                            <li>
                                                <a href="javascript:;">

                                                    <?php
                                                    $invoiceType = $invoiceItems->getInvoiceType();
                                                    switch ($invoiceType) {
                                                        case "INV":
                                                            echo '<span class="time" onclick="window.location=\'invoice_list.php\'"> View </span>';
                                                            break;
                                                        case "MNI":
                                                            echo '<span class="time" onclick="window.location=\'invoice_list.php?itype=mni\'"> View </span>';
                                                            break;
                                                        case "CRN":
                                                            echo '<span class="time" onclick="window.location=\'credit_note.php\'"> View </span>';
                                                            break;

                                                    }
                                                    ?>

                                                    <span class="details">
                                <span class="label label-sm label-icon label-danger">
                                    <i class="fa fa-bell-o"></i>
                                </span> <?php
                                                        $invoiceType = $invoiceItems->getInvoiceType();
                                                        switch ($invoiceType) {
                                                            case "INV":
                                                                echo $invoiceItems->getId() . " Invoice(s) received. ";
                                                                break;
                                                            case "MNI":
                                                                echo $invoiceItems->getId() . " Manual Invoice(s) received. ";
                                                                break;
                                                            case "CRN":
                                                                echo $invoiceItems->getId() . " Credit Note(s) received. ";
                                                                break;

                                                        }
                                                        ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php
                                }
                                ?>

                            </ul>
                        </li>

                    <?php endif; ?>
                    <!-- END NOTIFICATION DROPDOWN -->
                    <li class="separator hide"></li>

                    <li class="separator hide"></li>
                    <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                    <li class="separator hide"></li>
                    <?php
                    if (isset($_SESSION['admin'])) {
                        ?>
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               data-close-others="true">
            <span
                    class="username username-hide-on-mobile"> <?php echo ((strlen($_SESSION['admin']['firstname']) > 15) ? substr(@$_SESSION['admin']['firstname'], 0, 15) . "..." : @$_SESSION['admin']['firstname']) . ' ( ' . @$_SESSION['admin']['user_type'] . ' ) '; ?> </span>
                                <?php
                                $profileImagePath = realpath('../_assets/profile_images/');
                                if (trim($UserSessionManager->getProfileImage()) != '' && file_exists($profileImagePath . "/" . $UserSessionManager->getProfileImage())) {
                                    echo '<img alt="" class="img-circle" src="../_assets/profile_images/' . $UserSessionManager->getProfileImage() . '">';
                                } else {
                                    echo '<img alt="" class="img-circle" src="../_assets/images/profile/default_profile.png"/>';
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li><a href="view_profile.php">
                                        <i class="icon-user" style="color:#666 !important"></i> My Profile</a>
                                </li>
                                <li><a href="login.php?logout=true">
                                        <i class="icon-key"></i> Log Out </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    if (isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
                        $displayOption = @$_SESSION['menu-option'];
                    else
                        $displayOption = $UserSessionManager->getUserType();

                    switch ($displayOption) {

                        case User::USER_TYPE_SALES :
                            ?>
                            <li class="dropdown dropdown-registeruser">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"
                                   data-hover="dropdown" data-close-others="true"><i class="fa fa-bell"></i>Reminder
                                    <span class="label label-danger" id="today_reminder"></span><i
                                            class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default " id="reminder_message"></ul>
                            </li>
                            <?php
                            break;
                    }
                    ?>


                    <li class="dropdown dropdown-language">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                           data-close-others="true">    <?php
                            $pageName = DbAccess3::escape(strip_tags($_SERVER['PHP_SELF']));
                            if (@$_SESSION['lang'] == 'de-DE') {
                                ?>
                                <img src="/images/de.png" class="img-lang"/> <span class="langname"> Germany </span>
                                <?php
                            } else {
                                ?>
                                <img src="/images/en.png" class="img-lang"/> <span class="langname"> UK </span>
                                <?php
                            }
                            ?>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="<?php echo $pageName ?>?lang=de-DE"><img src="/images/de.png"
                                                                                  class="img-lang"/> Germany </a>
                            </li>
                            <li>
                                <a href="<?php echo $pageName ?>?lang=en-GB"><img src="/images/en.png"
                                                                                  class="img-lang"/> Uk </a>
                            </li>


                        </ul>
                    </li>
                    <?php
                    if (!isset($_SESSION['admin'])) {
                        ?>
                        <li class="margin-top-10">
                            <button class="btn red-haze btn-sm margin-top-10 wol-btn-login" type="button"><i
                                        class="fa fa-key"> </i> Login
                            </button>
                        </li>
                        <?php
                    }
                    ?>


                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
    <?php }?>
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
     <?php if (isset($_SESSION['admin'])) { ?>
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapsed">

            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <?php $this->renderMenu(); ?>
            <!-- END SIDEBAR MENU -->
        </div>
    </div>
     <?php } ?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper" >
        <div class="<?php echo isset($_SESSION['admin'])?"page-content":'' ?>" >
            <?php $this->renderBreadcrumb(); ?>
            <?php $this->renderBody(); ?>
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<?php
if (trim($userAccountDetail->getThemeId()) == '1') {
    ?>
    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <div class="row">
            <div class="container">

                <div class="col-sm-2">
                    <div class="foot-header"> H�NDLERBUND<br/><br/></div>
                    <div class="foot-links">
                        <a href="https://www.haendlerbund.de/en" target="_blank">About Us</a>
                        <a href="https://www.haendlerbund.de/en/haendlerbund/about-us/our-team" target="_blank">Our
                            Team</a>
                        <a href="https://www.haendlerbund.de/en/services" target="_blank">Services</a>
                    </div>
                </div>
                <div class="col-sm-2 border-left">
                    <div class="foot-header"> ONE WORLD EXPRESS</div>
                    <div class="foot-links">
                        <a href="http://www.oneworldexpress.com/about-us" target="_blank">About Us</a>
                        <a href="http://www.oneworldexpress.com/team" target="_blank">Our Team</a>
                        <a href="http://www.oneworldexpress.com/services/international-delivery/" target="_blank">Services</a>
                    </div>
                </div>
                <!--/col-sm-3-->
                <div class="col-sm-2 border-left">
                    <div class="foot-header"> SERVICE</div>
                    <div class="foot-links">
                        <a href="">Membership in detail</a>
                        <a href="">Legal texts</a>
                        <a href="">Assistance with written warnings</a>
                        <a href="">Legal advice</a>
                        <a href="">Shop inspection </a>
                        <a href="">International legal texts</a>
                    </div>
                </div>
                <div class="col-sm-3 border-left">
                    <div class="foot-header">
                        CONTACT
                    </div>
                    <div class="foot-links">
                        <a href="">Contact form</a>
                        <a href="">Legal references</a>
                        <a href="">Data protection Statement</a>


                    </div>
                </div><!--/col-sm-3-->
                <div class="col-sm-3 icon">
                    <i class="icon-twitter"></i>&nbsp;&nbsp;
                    <i class="icon-facebook"></i>&nbsp;&nbsp;
                    <i class="icon-xing"></i>&nbsp;&nbsp;
                    <i class="icon-youtube-play"></i>&nbsp;&nbsp;
                    <i class="icon-google-plus"></i>
                    <br>
                    <img src="/images/onlinehaendler-news-logo-white.png" class="img-responsive img-thumbnail">


                </div>
            </div>
        </div><!--/row-->


        <div class="scroll-to-top"><i class="icon-arrow-up"></i></div>

    </div>
    <?php
} else {
    ?>
    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <?php
        if (isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
            $displayOption = @$_SESSION['menu-option'];
        else
            $displayOption = $UserSessionManager->getUserType();
        if ($displayOption == User::USER_TYPE_ADMIN) {
            ?>
            <div id="reminder-message" class="right-bottom">

            </div>
            <?php
        }
        ?>
        <div style="clear:both;"></div>
        <div class="page-footer-inner"> 2011-2018 &copy; SmartTrack</div>
        <div class="scroll-to-top"><i class="icon-arrow-up"></i></div>
    </div>
    <?php
}
?>


<!-- AUDIT MODEL -->
<div class="modal fade" tabindex="-1" role="dialog" id="audit-log">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><span></span> Audit Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>Date Time</th>
                            </tr>
                            </thead>
                            <tbody id="audit_content">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!--modal to view user audit on every page-->
<div class="modal fade" tabindex="-1" role="dialog" id="user-audit-view-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Audit Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="user-audit-no-record"></div>
                        <!--                        <table class="table table-bordered table-hover user-audit-table">
                                                    <thead>
                                                    <tr>
                                                        <th>User Name</th>
                                                        <th>Message</th>
                                                        <th>Dated</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="audit_detail_view_html">
                                                    </tbody>
                                                </table>-->
                        <table class="table table-striped table-bordered table-hover user-audit-table table-condensed"
                               id="manage-data-table-show-audit">
                            <thead>
                            <tr role="row" class="heading">
                                <th>User Name</th>
                                <th>Message</th>
                                <th>Dated</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="audit_detail_view_html">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!--audit signle view modal-->
<div class="modal fade" tabindex="-1" role="dialog" id="user_audit_single_view">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Audit Details</h4>
            </div>
            <div class="modal-body">
                <div class="no-data-found"></div>
                <div class="row user_audit_data_view">
                    <div class="col-md-6">
                        <label>Old Data</label>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody id="old_data">
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <label>New Data</label>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody id="new_data">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="bs-modal-lg modal fade" tabindex="-1" role="dialog" id="detail-log-popup">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Log Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="model-content-display"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- END QUICK NAV -->


<!-- START LOG OUT POP UP -->
<div class="modal fade" id="jitp-warn-display" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Your session is about to expire.</h4></div>
            <div class="modal-body">
                <p>You session will be sign out in <span class="jitp-countdown-holder"></span></p>
                <!--<p>Time remaining: <span class="jitp-countdown-holder"></span></p>-->
                <p>Do you want to continue your session?</p>
                <div class="progress">
                    <div id="jitp-warn-bar" class="progress-bar progress-bar-striped active" role="progressbar"
                         style="min-width: 15px; width: 100%;">
                        <span class="jitp-countdown-holder"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="jitp-warn-logout" type="button" class="btn btn-default">No, Logout</button>
                <button id="jitp-warn-alive" type="button" class="btn btn-primary">Yes, Keep Working</button>
            </div>
        </div>
    </div>
</div>

<!--[if lt IE 9]>
<script src="/assets/global/plugins/respond.min.js"></script>
<script src="/assets/global/plugins/excanvas.min.js"></script>
<script src="/assets/global/plugins/ie8.fix.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="<?= BASE_URL ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
        type="text/javascript"></script>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="<?php echo BASE_URL; ?>assets/global/plugins/jquery-idle-timeout-plus/jquery-idle-timeout-plus.js"
        type="text/javascript"></script>
<script src="<?php echo BASE_URL; ?>assets/global/plugins/jquery-idle-timeout-plus/jquery-storage-api.js"
        type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="<?= BASE_URL ?>assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<!--<script src="../assets/pages/scripts/ui-idletimeout.min.js" type="text/javascript"></script>-->
<!-- END PAGE LEVEL SCRIPTS -->
<?php $this->addPagelavelJs(); ?>
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="<?= BASE_URL ?>assets/layouts/layout4/scripts/layout.min.js" type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/layouts/layout4/scripts/demo.min.js" type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->
<script src="<?= BASE_URL ?>assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
        type="text/javascript"></script>
<script src="<?= BASE_URL ?>assets/pages/scripts/components-bootstrap-select.min.js" type="text/javascript"></script>

<script type="text/javascript">
    var gridUserAudit = null;
    var DataTableFunUserAudit = function () {
        var handleDataTableParcel = function () {
            var datatableparcelurl = "user_audit_list.php?action=get_user_audit_data_dynamic";
            gridUserAudit = new Datatable();
            gridUserAudit.init({
                src: $("#manage-data-table-show-audit"),
                onSuccess: function (grid, response) {
                    // execute some code after table records loaded
                },

                onError: function (grid) {
                    // execute some code on network or other general error
                },
                dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                    "lengthMenu": [
                        [10, 20, 50, 100, 150],
                        [10, 20, 50, 100, 150] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": datatableparcelurl, // ajax source
                        headers: {}
                    },
                    "bServerSide": false,
                    "deferLoading": false,
                    "bStateSave": true,
					"ordering": false,
                    "columns": [
                        {"data": "user_name", "bSortable": false},
                        {"data": "message", "bSortable": false},
                        {"data": "action_date", "bSortable": false},
                        {"data": "actions", "bSortable": false},
                    ],
                    rowCallback: function (row, data, index) {

                    }
                }
            });
        };
        return {
            //main function to initiate the module
            init: function () {
                handleDataTableParcel();
            }
        };
    }();
	<?php if(basename($_SERVER['REQUEST_URI']) != 'get_pricing.php'){ ?>
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
	<?php } ?>

    $(document).ready(function () {
        //DataTableFunUserAudit.init();
        if ($(".select2").length > 0) {
            $(".select2").select2();
            $(".select2-container").tooltip({
                title: function () {
                    return $(this).prev().attr("title");
                },
                placement: "top"
            });
        }
        $('input,select,a,textarea').tooltip();


        $(document).on("click", ".show_audit", function () {

            var ajax_url = $(this).data('ajax_url');
            if ($.trim(ajax_url) == '')
                ajax_url = 'index.php';
            var id = $(this).data('id');
            var title = $(this).data('title');
            var table = $(this).data('table');
            var ajaxUrl = $(this).data('ajax_url');
            var audit_continer = $(this).data('container');
            $("#audit-log h4.modal-title span").html(title);
            $.post(ajax_url, {func: 'get_audit_log', id: id, table: table, ajaxUrl: ajaxUrl}, function (data) {
                $("#" + audit_continer).html(data);

            });
        });

        $(document).on('click', '.log_detail_link', function () {
            var e = $(this);
            var cid = e.data('cid');
            var url = e.data('ajax_url');
            if ($.trim(url) == '')
                url = 'index.php';

            var title = $(this).data('title');
            var table = $(this).data('table');
            $.post(url, {func: 'get_log_details', cid: cid, table: table}, function (d) {
                $("#model-content-display").html(d);
                $("#detail-log-popup").modal('show');
            });
        });
        $('body').on('click', '#user-audit-detail-view', function () {
			$('#audit_detail_view_html').html('<p>Loading Please wait...</p>');
			if(gridUserAudit == null)
				DataTableFunUserAudit.init();
            var log_key = $(this).data('log_key');
            var log_name = $(this).data('log_name');
            if (log_name != '' && log_name != undefined && log_key != '' && log_key != undefined) {
                gridUserAudit.setAjaxParam('table_key', log_key);
                gridUserAudit.setAjaxParam('table_name', log_name);
                gridUserAudit.setAjaxParam('onload', 1);
                gridUserAudit.submitFilter();
                $('.custom-alerts').hide();
            } else {
                $('#audit_detail_view_html').html('<p>No record found.</p>');
            }
        });
        $('body').on('click', '#user_audit_single_view_btn', function () {
            var that = $(this);
            var user_audit_id = that.data('audit_id');
            $.ajax({
                type: "POST",
                url: "user_audit_list.php",
                data: {action: 'get_user_audit_data', user_audit_id: user_audit_id},
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result == 'error') {
                        $('.no-data-found').text(response.message);
                        $('.no-data-found').show();
                        $('.user_audit_data_view').hide();
                    } else {
                        $('.user_audit_data_view').show();
                        $('.no-data-found').hide();
                        $('#old_data').html(response.old_html);
                        $('#new_data').html(response.new_html);
                    }
                },
                error: function () {
                    //alert('error handing here');
                }
            });
        });

        //jQuery(document).ready(function () {
		<?php if(basename($_SERVER['REQUEST_URI']) != 'get_pricing.php'){ ?>
            IdleTimeoutPlus.start({
                multiWindowSupport: true,
                bootstrap: true,
                keepAliveInterval: false,
                //keepAliveUrl: '<?php //echo BASE_URL;?>//keepalive.php',
                redirectUrl: '<?php echo BASE_URL;?>login.php?logout=true',
                logoutAutoUrl: '<?php echo BASE_URL;?>login.php?logout=true',
                logoutUrl: '<?php echo BASE_URL;?>login.php?logout=true',
                warnTimeLimit: <?php echo WARN_TIME_LIMIT;?>,
                idleTimeLimit: <?php echo IDLE_TIME_LIMIT; ?>
            });
        <?php } ?>
        //});

        if ($('.wol-btn-login').length > 0) {
            $('.wol-btn-login').click(function () {
                window.location = '/login.php';
            })
        }


//                $('#clickmewow').click(function(){
//                    $('#radio1003').attr('checked', 'checked');
//                });
    });

    function callnewuser() {
        $.ajax({
            url: "newAjaxUserGet.php",
            type: "GET",
            dataType: 'json',
            data: {
                action: 'GETNEWUSERS',
            },
            success: function (data) {
                // $('#user-new-data').html(data);
                if (data['count']) {
                    $('#total_user').html(data['count']);
                }
                if (data['html']) {
                    $('#user-new-data').html(data['html']);
                }
            }
        });
    }

    function updateReminder(id) {
        $.ajax({
            url: "getEmailReminder.php",
            type: "GET",
            data: {
                action: 'UPDATEREMINDER',
                id: id
            },
            success: function (data) {
            }
        });

    }

</script>
<script type="text/javascript">function add_chatinline() {
        var hccid = 58015497;
        var nt = document.createElement("script");
        nt.async = true;
        nt.src = "https://mylivechat.com/chatinline.aspx?hccid=" + hccid;
        var ct = document.getElementsByTagName("script")[0];
        ct.parentNode.insertBefore(nt, ct);
    }

    add_chatinline(); </script>
<script src="<?= BASE_URL ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js"
        type="text/javascript"></script>

<?php
$this->renderFooter();
DbAccess3::closeConnection();
echo "<!--close connection-->";
?>
<!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>
