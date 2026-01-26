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
        <?php $this->renderHead(); ?>
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
    <body class="page-header-fixed page-quick-sidebar-over-content ">
        <!-- BEGIN HEADER -->
        <div class="page-header -i navbar navbar-fixed-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner">
                <!-- BEGIN LOGO -->
                <div class="col-sm-3 col-lg-3 col-xl-3 page-logo">
                    <a href="index.php">
                        <img src="../images/inner-logo.png" class="img-rounded" alt="One World Express" style="background-color: #fff;"/>
                    </a>
                    <div class="menu-toggler sidebar-toggler hide">
                        <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                    </div>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN TOP NAVIGATION MENU -->
                <style>
				.nav-pills >  li > a {
						background-color: #eee  !important;
					}
				
					
				.nav-pills > .active > a, .nav-pills > .active > a:hover {
						background-color: #960707  !important;
					}
					
                </style>
                <div class="col-lg-8 visible-lg" style="padding-top:20px;" >
                <?php 
                $menuOption	=	@$_SESSION['menu-option'];
                $menuToShow	=	@$_SESSION['admin']['user_type'];
               
                ?>
                    <ul class="nav nav-pills red">
                      <li 
                      <?php
                        if(trim($menuOption) == "") 
                        {
                            if($_SESSION['admin']['user_type'] == User::USER_TYPE_ADMIN) 
                            	echo 'class="active"';
                            else 
                            	echo 'class="disabled hide"';
                        }
                        else  if(trim($menuOption) == User::USER_TYPE_ADMIN)
                        {
                        	echo 'class="active"';
                        }
                      
                      	
                      ?>
                        ><a href="index.php?menu-option=<?php echo User::USER_TYPE_ADMIN?>"><span class="fa fa-street-view"></span>&nbsp;&nbsp;Admin</a></li>
                      <li 
                      	<?php 
                        if(trim($menuOption) == "") 
                        {
                            if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_CLIENT) 
                            	echo 'class="active"';
                            else if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_ADMIN)
                            	echo '';
                            else 
                            	echo 'class="disabled hide"';
                        }
                        else  if(trim($menuOption) == User::USER_TYPE_CLIENT)
                        {
                        	echo 'class="active"';
                        }
                        ?>  ><a href="index.php?menu-option=<?php echo User::USER_TYPE_CLIENT?>"><span class="fa fa-users"></span>&nbsp;&nbsp;Customer</a></li>
                      <li 
                      	<?php 
                        if(trim($menuOption) == "") 
                        {
                            if(isset($_SESSION['admin']) && ($_SESSION['admin']['user_type'] == User::USER_TYPE_CORPORATECLIENT || $_SESSION['admin']['user_type'] == User::USER_TYPE_CORPORATE_CLIENT )) 
                            	echo 'class="active"';
                            else if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_ADMIN)
	                            echo '';
                            else 
    	                        echo 'class="disabled hidden"';
                        } 
                        else  if(trim($menuOption) == User::USER_TYPE_CORPORATECLIENT)
                        {
                        echo 'class="active"';
                        }
                        ?>  ><a href="index.php?menu-option=<?php echo User::USER_TYPE_CORPORATECLIENT?>"><span class="fa fa-users"></span>&nbsp;&nbsp;Corporate Customer</a></li>
                      <li 
                      <?php 
                        if(trim($menuOption) == "") 
                        {
                            if(isset($_SESSION['admin']) && ( $_SESSION['admin']['user_type'] == User::USER_TYPE_ACCOUNT || $_SESSION['admin']['user_type'] == User::USER_TYPE_FINANCE))									
                            	echo 'class="active"';
                            else if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_ADMIN)
                            	echo ''; 
                            else 
                            	echo 'class="disabled hide"';
                        } 
                        else  if(trim($menuOption) == User::USER_TYPE_FINANCE)
                        {
                        	echo 'class="active"';
                        }
                     ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_FINANCE?>"><span class="fa fa-gears"></span>&nbsp;&nbsp;Accounts</a></li>
                      <li 
                      	<?php 
                        if(trim($menuOption) == "") 
                        {
                            if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_CUSTOMER_SERVICE) 
                            	echo 'class="active"';
                            else if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_ADMIN)
                            	echo '';
                            else 
                            	echo 'class="disabled hide"';
                        } 
                        else  if(trim($menuOption) == User::USER_TYPE_CUSTOMER_SERVICE)
                        {
                        	echo 'class="active"';
                        }
                        ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_CUSTOMER_SERVICE?>"><span class="fa fa-cube"></span>&nbsp;&nbsp;Customer service</a></li>
                      <li 
                      <?php 
                        if(trim($menuOption) == "") 
                        {
                            if(isset($_SESSION['admin']) && ($_SESSION['admin']['user_type'] == User::USER_TYPE_WAREHOUSE_ALPHA || $_SESSION['admin']['user_type'] == User::USER_TYPE_WAREHOUSE)) 
                            	echo 'class="active"';
                            else if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_ADMIN)
                            	echo '';
                            else 
                            	echo 'class="disabled hide"';
                        } 
                        else  if(trim($menuOption) == User::USER_TYPE_WAREHOUSE)
                        {
                        echo 'class="active"';
                        }
                        ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_WAREHOUSE?>"><span class="fa fa-truck"></span>&nbsp;&nbsp;Operations</a></li>
                      <!--<li ><a href="#"><span class="fa fa-database"></span>&nbsp;&nbsp;Sales</a></li>-->
                      
                    </ul>
				</div>
                
                <div class="top-menu visible-lg" >
                    <ul class="nav navbar-nav pull-right">
                       
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <?php 
                                if(isset($_SESSION['admin']['profile_image']) && trim($_SESSION['admin']['profile_image']) != '')
                                {
                                echo '<img alt="" class="img-circle" src="../../_assets/profile_images/'.$_SESSION['admin']['profile_image'].'">';
                                }
                                else
                                {
                                echo '<img alt="" class="img-circle" src="../_assets/admin/layout/img/avatar3_small.jpg"/>';
                                }

                                ?>

                                <span class="username username-hide-on-mobile">
                                    <?php echo @$_SESSION['admin']['firstname'].'( '.@$_SESSION['admin']['user_type'].' ) ';?> </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="login.php?logout=true">
                                    <i class="icon-key"></i> Log Out </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN CONTENT -->
            <div class="container ">
                <div class="page-content">
                    <form method="post" enctype="multipart/form-data" id="adminForm" name="adminForm"  role="form">
                        <?php $this->renderBody(); ?>
                    </form>

                </div>
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