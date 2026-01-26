<?php
$UserSessionManager = SessionManager::getUser();
$userAccountID = $UserSessionManager->getUserAccountId();
$userAccountDetail = new CustomerAccount($userAccountID);

$this->auditLogs();
?>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!doctype html>
<html lang="en" data-layout="semibox" data-layout-style="default" data-sidebar="light" data-topbar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-sidebar-visibility="show" data-bs-theme="light" data-layout-width="fluid" data-layout-position="fixed">
<head>

    <meta charset="utf-8"/>
    <?php $this->renderMetaTags(); ?>
    <title>Logistic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?=SETTING_MAIN_ASSETS;?>images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="<?=SETTING_MAIN_ASSETS;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?=SETTING_MAIN_ASSETS;?>css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?=SETTING_MAIN_ASSETS;?>css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?=SETTING_MAIN_ASSETS;?>css/custom.min.css" rel="stylesheet" type="text/css" />
    <?php $this->addPagelavelCss(); ?>
    <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        table td div {
            font-weight: 300;
        }
        table td  {
            font-weight: 300;
        }
    </style>
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
    <?php $this->renderHead(); ?>
    <style type="text/css">
        li {
            list-style-type: none;
            padding-left:
        }
        .page-sidebar-menu{
            padding-left: 0rem !important;
        }
    </style>
</head>
<body>
<!-- Begin page -->
<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?=SETTING_MAIN_ASSETS;?>images/logo-sm.png" alt="" height="22">
                        </span>
                            <span class="logo-lg">
                            <img src="<?=SETTING_MAIN_ASSETS;?>images/logo-dark.png" alt="" height="17">
                        </span>
                        </a>

                        <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?=SETTING_MAIN_ASSETS;?>images/logo-sm.png" alt="" height="22">
                        </span>
                            <span class="logo-lg">
                            <img src="<?=SETTING_MAIN_ASSETS;?>images/logo-light.png" alt="" height="17">
                        </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    </button>
                </div>

                <div class="d-flex align-items-center">

                    <div class="dropdown d-md-none topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-search fs-22"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
<!--                    Language-->
                    <div class="dropdown ms-1 topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                            $pageName = DbAccess3::escape(strip_tags($_SERVER['PHP_SELF']));
                            if (@$_SESSION['lang'] == 'de-DE') {
                                ?>
                                <img src="<?=SETTING_MAIN_ASSETS;?>images/flags/germany.svg" alt="Header Language" height="20" class="rounded" id="header-lang-img"/>
                                <?php
                            } else {
                                ?>
                                <img src="<?=SETTING_MAIN_ASSETS;?>images/flags/uk.svg" alt="Header Language" height="20" class="rounded" id="header-lang-img"/>
                                <?php
                            }
                            ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">

                            <!-- item-->
                            <a href="<?php echo $pageName ?>?lang=en-GB" class="dropdown-item notify-item language py-2" data-lang="en" title="English">
                                <img src="<?=SETTING_MAIN_ASSETS;?>images/flags/uk.svg" alt="user-image" class="me-2 rounded" height="18">
                                <span class="align-middle">English</span>
                            </a>

                            <!-- item-->
                            <a href="<?php echo $pageName ?>?lang=de-DE" class="dropdown-item notify-item language" data-lang="gr" title="German">
                                <img src="<?=SETTING_MAIN_ASSETS;?>images/flags/germany.svg" alt="user-image" class="me-2 rounded" height="18"> <span class="align-middle">Deutsche</span>
                            </a>

                        </div>
                    </div>
<!--Language End-->
<!--Full Screen start-->
                    <div class="ms-1 header-item d-none d-sm-flex">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-toggle="fullscreen">
                            <i class='bx bx-fullscreen fs-22'></i>
                        </button>
                    </div>
<!--Full Screen End-->
<!--Dark Mode-->
                    <div class="ms-1 header-item d-none d-sm-flex">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                            <i class='bx bx-moon fs-22'></i>
                        </button>
                    </div>
<!--Dark Mode End-->
<!--Notification start-->
                    <?php
                    $invoiceQueryFilter = new InvoiceFilter();
                    $invoiceQueryFilter->where(['inv.is_email' => "1", 'inv.is_read' => "0", 'inv.user_account_id' => $UserSessionManager->getUserAccountId()]);
                    $invoiceQueryFilter->groupBy("inv.invoice_type");
                    $invoiceMaxIdObj = $invoiceQueryFilter->getList('count(inv.id) as id, invoice_type');
                    $numberOfInvoices = 0;
                    if (count($invoiceMaxIdObj) > 0) {
                        $numberOfInvoices += $invoiceMaxIdObj[0]->getId();
                    }
                    if ($numberOfInvoices > 0) :
                    ?>
                    <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                            <i class='bx bx-bell fs-22'></i>
                            <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><?php echo $numberOfInvoices ?><span class="visually-hidden">unread messages</span></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                            <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                        </div>
                                        <div class="col-auto dropdown-tabs">
                                            <span class="badge bg-light-subtle text-body fs-13"> <?php echo $numberOfInvoices ?> New</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-2 pt-2">
                                    <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab" aria-selected="true">
                                                All (<?php echo $numberOfInvoices ?>)
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>

                            <div class="tab-content position-relative" id="notificationItemsTabContent">
                                <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                    <div data-simplebar style="max-height: 300px;" class="pe-2">
                                        <?php
                                            foreach ($invoiceMaxIdObj as $invoiceItems) {
                                        ?>

                                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                                            <div class="d-flex">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                    <i class="bx bx-badge-check"></i>
                                                </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="#!" class="stretched-link">
                                                        <?php
                                                        $invoiceType = $invoiceItems->getInvoiceType();
                                                        switch ($invoiceType) {
                                                            case "INV":
                                                                echo '<h6 class="mt-0 mb-2 lh-base" onclick="window.location=\'invoice_list.php\'"> View </h6>';
                                                                break;
                                                            case "MNI":
                                                                echo '<h6 class="mt-0 mb-2 lh-base" onclick="window.location=\'invoice_list.php?itype=mni\'"> View </h6>';
                                                                break;
                                                            case "CRN":
                                                                echo '<h6 class="mt-0 mb-2 lh-base" onclick="window.location=\'credit_note.php\'"> View </h6>';
                                                                break;

                                                        }
                                                        ?>
                                                    </a><?php
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
                                                    ?>
<!--                                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">-->
<!--                                                        <span><i class="mdi mdi-clock-outline"></i> Just 30 sec ago</span>-->
<!--                                                    </p>-->
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="my-3 text-center view-all">
                                            <a href="invoice_list.php" class="btn btn-soft-success waves-effect waves-light">View
                                                All Notifications <i class="ri-arrow-right-line align-middle"></i></a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
<!--Notification end-->
<!--                    Profile start-->
                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <?php
                            $profileImagePath = realpath('../_assets/profile_images/');
                            if (trim($UserSessionManager->getProfileImage()) != '' && file_exists($profileImagePath . "/" . $UserSessionManager->getProfileImage())) {
                                echo '<img alt="Profile Picture" class="rounded-circle header-profile-user" src="../_assets/profile_images/' . $UserSessionManager->getProfileImage() . '">';
                            } else {
                                echo '<img alt="Profile Picture" class="rounded-circle header-profile-user" src="../_assets/images/profile/default_profile.png"/>';
                            }
                            ?>
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-semibold user-name-text"><?php echo ((strlen($_SESSION['admin']['firstname']) > 15) ? substr(@$_SESSION['admin']['firstname'], 0, 15) . "..." : @$_SESSION['admin']['firstname']); ?></span>
                                <span class="d-none d-xl-block ms-1 fs-13 user-name-sub-text"><?php echo @$_SESSION['admin']['user_type'] ?></span>
                            </span>
                        </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <h6 class="dropdown-header">Welcome <?php echo ((strlen($_SESSION['admin']['firstname']) > 15) ? substr(@$_SESSION['admin']['firstname'], 0, 15) . "..." : @$_SESSION['admin']['firstname']); ?>!</h6>
                            <a class="dropdown-item" href="<?= BASE_URL;?>view-profile"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>

                            <a class="dropdown-item" href="<?= BASE_URL;?>logout"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                        </div>
                    </div>
<!--                    profile end-->
                </div>
            </div>
        </div>
    </header>

    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <div class="navbar-brand-box">
            <a href="<?= BASE_URL;?>dashboard" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="<?=SETTING_MAIN_ASSETS;?>images/logo-sm.png" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="<?=SETTING_MAIN_ASSETS;?>images/logo-dark.png" alt="" height="17">
                </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        <div id="scrollbar">
            <div class="container-fluid">
                <div id="two-column-menu"> </div>
                <ul class="navbar-nav" id="navbar-nav">
                    <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                    <?php $this->renderMenu(); ?>
                </ul>
            </div>
            <!-- Sidebar -->
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">&nbsp;</h4>
                            <div class="page-title-right">
                                <?php $this->renderBreadcrumb(); ?>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->
                <?php $this->renderBody(); ?>
            </div>
        </div>

    </div>
    </div>

<!--start back-to-top-->
<button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>
<!--end back-to-top-->

<!--preloader-->
<div id="preloader">
    <div id="status">
        <div class="spinner-border text-primary avatar-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>


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
<!-- JAVASCRIPT -->
<script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- Layout config Js -->
<script src="<?=SETTING_MAIN_ASSETS;?>js/layout.js"></script>
<script src="<?=SETTING_MAIN_ASSETS;?>libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?=SETTING_MAIN_ASSETS;?>libs/simplebar/simplebar.min.js"></script>
<script src="<?=SETTING_MAIN_ASSETS;?>libs/node-waves/waves.min.js"></script>
<script src="<?=SETTING_MAIN_ASSETS;?>libs/feather-icons/feather.min.js"></script>
<script src="<?=SETTING_MAIN_ASSETS;?>js/pages/plugins/lord-icon-2.1.0.js"></script>

<?php $this->addPagelavelJs(); ?>
<script src="<?=SETTING_MAIN_ASSETS;?>js/plugins.js"></script>
<!-- apexcharts -->
<script src="<?=SETTING_MAIN_ASSETS;?>libs/apexcharts/apexcharts.min.js"></script>
<!-- projects js -->
<script src="<?=SETTING_MAIN_ASSETS;?>js/pages/dashboard-projects.init.js"></script>
<!-- App js -->
<script src="<?=SETTING_MAIN_ASSETS;?>js/app.js"></script>
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
        $("img").on("error", function() {
            console.log("Error");
        });
        //IdleTimeoutPlus.start({
        //    multiWindowSupport: true,
        //    bootstrap: true,
        //    keepAliveInterval: false,
        //    //keepAliveUrl: '<?php ////echo BASE_URL;?>////keepalive.php',
        //    redirectUrl: '<?php //echo BASE_URL;?>//login.php?logout=true',
        //    logoutAutoUrl: '<?php //echo BASE_URL;?>//login.php?logout=true',
        //    logoutUrl: '<?php //echo BASE_URL;?>//login.php?logout=true',
        //    warnTimeLimit: <?php //echo WARN_TIME_LIMIT;?>//,
        //    idleTimeLimit: <?php //echo IDLE_TIME_LIMIT; ?>
        //});
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
<!--<script type="text/javascript">function add_chatinline() {-->
<!--        var hccid = 58015497;-->
<!--        var nt = document.createElement("script");-->
<!--        nt.async = true;-->
<!--        nt.src = "https://mylivechat.com/chatinline.aspx?hccid=" + hccid;-->
<!--        var ct = document.getElementsByTagName("script")[0];-->
<!--        ct.parentNode.insertBefore(nt, ct);-->
<!--    }-->
<!---->
<!--    add_chatinline(); </script>-->
<script src="<?=BASE_URL ?>assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js"
        type="text/javascript"></script>
<?php
$this->renderFooter();
DbAccess3::closeConnection();
echo "<!--close connection-->";
?>
</body>
</html>