<!doctype html>
<html lang="en" data-layout="vertical" data-layout-style="detached" data-sidebar="light" data-topbar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
<head>
    <meta charset="utf-8" />
    <title>Logistic Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Logistic " name="description" />
    <meta content="Myth Solutions" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= SETTING_MAIN_ASSETS;?>images/favicon.ico">
    <!-- Layout config Js -->
    <script src="<?= SETTING_MAIN_ASSETS;?>js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="<?= SETTING_MAIN_ASSETS;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= SETTING_MAIN_ASSETS;?>css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?= SETTING_MAIN_ASSETS;?>css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?= SETTING_MAIN_ASSETS;?>css/custom.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- auth-page wrapper -->
<div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-overlay"></div>
    <!-- auth-page content -->
    <div class="auth-page-content overflow-hidden pt-lg-5">
        <?php $this->renderBody(); ?>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="mb-0">&copy;
                            <script>document.write(new Date().getFullYear())</script> Logistic. Crafted with <i class="mdi mdi-heart text-danger"></i> by Myth Solutions
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->
</div>
<!-- end auth-page-wrapper -->

<!-- JAVASCRIPT -->
<script src="<?= SETTING_MAIN_ASSETS;?>libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= SETTING_MAIN_ASSETS;?>libs/simplebar/simplebar.min.js"></script>
<script src="<?= SETTING_MAIN_ASSETS;?>libs/node-waves/waves.min.js"></script>
<script src="<?= SETTING_MAIN_ASSETS;?>libs/feather-icons/feather.min.js"></script>
<script src="<?= SETTING_MAIN_ASSETS;?>js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="<?= SETTING_MAIN_ASSETS;?>js/plugins.js"></script>
</body>
</html>