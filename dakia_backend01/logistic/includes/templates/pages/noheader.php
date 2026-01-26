<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]> -->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8"/>
        <!--<title>OWE-Internal System</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <meta content="" name="description"/>
        <meta content="" name="author"/>-->
        <?php $this->renderMetaTags(); ?>
        
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="../_assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="../_assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="../_assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../_assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <link href="../_assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME STYLES -->
        <link href="../_assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
        <link href="../_assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
        <link href="../_assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
        <link id="style_color" href="../_assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>
        <link href="../_assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
        <!-- END THEME STYLES -->
        <link rel="shortcut icon" href="templates/admin/favicon.ico"/>
        <link href="../_assets/css/smoothness/jquery-ui-1.7.1.custom.css" rel="Stylesheet" type="text/css" />
        <script src="../_assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js/common.js"></script>
        <!-- BEGIN THEME STYLES -->
        <link href="../_assets/css/custom.css" rel="stylesheet" type="text/css"/>
        <!-- END THEME STYLES -->
        <?php //$this->renderHead(); ?>
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
    <!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
    <!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
    <!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
    <!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
    <!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
    <!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
    <!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
    <!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
    <body class=" ">
        <!-- BEGIN HEADER -->
        
        <!-- END HEADER -->
        <div class="clearfix">
        </div>
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            
         
                <div class="page-content">
                    <form method="post" enctype="multipart/form-data" id="adminForm" name="adminForm"  role="form">
                        <?php $this->renderBody(); ?>
                    </form>

                </div>
            <!-- END CONTENT -->


        </div>
        <!-- BEGIN FOOTER -->
        <div class="page-footer">
            <div class="page-footer-inner">
                2015 &copy; One World Express - OWE ACCOUNTS.
            </div>
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
            </div>
        </div>
        <!-- END FOOTER -->

        <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- BEGIN CORE PLUGINS -->
        <!--[if lt IE 9]>
        <script src="../../assets/global/plugins/respond.min.js"></script>
        <script src="../../assets/global/plugins/excanvas.min.js"></script> 
        <![endif]-->

        <!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
        <script src="../_assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="../_assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <script src="../_assets/global/scripts/metronic.js" type="text/javascript"></script>
        <script src="../_assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
        <script src="../_assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
        <script src="../_assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
        <script src="../js/validator.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script>
            jQuery(document).ready(function() {
                Metronic.init(); // init core components
                Layout.init(); // init current layout
                QuickSidebar.init(); // init quick sidebar
                Demo.init(); // init demo features
            });

        </script>
        <?php $this->renderFooter(); ?>
        <!-- END JAVASCRIPTS -->
    </body>
    <!-- END BODY -->
</html>