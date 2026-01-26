<?php 
$UserSessionManager	=	SessionManager::getUser();
?><!DOCTYPE html>
<!-- 
Template Name: OneWorld
Version: 1
Author: OneWorldTeam
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8" />
<?php $this->renderMetaTags(); ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport" />
<meta content="" name="description" />
<meta content="" name="author" />
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL STYLES -->
<link href="../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
<link href="../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/select2/css/select2.css"/>
<link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />

<!-- END THEME GLOBAL STYLES -->
<!-- BEGIN THEME LAYOUT STYLES -->
<link href="../assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />

<?php 
//if(trim($UserSessionManager->getCountry()) == 'DE')
{
?>
<link href="../assets/layouts/layout/css/themes/darkblue.germany.min.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="../assets/layouts/layout/css/custom.germany.min.css" rel="stylesheet" type="text/css" />
<?php
}
//else
{
/*?>
<link href="../assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="../assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
<?php*/
}
?>
<!-- END THEME LAYOUT STYLES -->
<link rel="shortcut icon" href="favicon.ico" />
<script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<!-- END THEME STYLES -->
<?php $this->renderHead(); ?>
</head>
<!-- END HEAD -->
<!--page-sidebar-closed-hide-logo -->
<body class="page-header-fixed page-content-white">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top"> 
  <!-- BEGIN HEADER INNER -->
  <div class="page-header-inner"> 
    <!-- BEGIN LOGO -->
    <div class="col-sm-2 col-lg-2 col-xs-8">
      <div class="main-top-logo"> <a href="index.php"> 
        <?php
			$imageLogo	= '';
                	$userImage	=	$UserSessionManager->getLogo();
					if(trim($UserSessionManager->getCountry()) == 'DE')
                    {
                    	$imageLogo	=	'<img src="../images/inner-logo.de.png"  class="img-rounded img-responsive white-back" alt="One World Express" />';
                   	}
                    else if(trim($userImage) != '')
					{
						$imageLogo	=	'<img src="../images/userlogo/'.$userImage.'"  class="img-rounded img-responsive white-back" alt="One World Express" />';
					}
					else
					{
						$imageLogo	= '<img src="../images/inner-logo.png" class="img-rounded img-responsive white-back" alt="One World Express" />';
					}
		            echo $imageLogo;
            ?>
        </a>
        <div class="menu-toggler sidebar-toggler hide"> 
          <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header --> 
        </div>
      </div>
    </div>
    <!-- END LOGO --> 
    <!-- BEGIN RESPONSIVE MENU TOGGLER --> 
    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a> 
    <!-- END RESPONSIVE MENU TOGGLER --> 
    <!-- BEGIN TOP NAVIGATION MENU -->
    <div class="col-lg-7 visible-lg hidden-xs" id="main-top-heading">
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
                        if(trim($UserSessionManager->getUserType()) == User::USER_TYPE_ADMIN)
                        {
                        ?>  ><a href="index.php?menu-option=<?php echo User::USER_TYPE_CLIENT?>"><span class="fa fa-users"></span>&nbsp;&nbsp;<? echo Translation::GetCaption("customer") ?></a>
                        <?php }
                        ?>
                        </li>
                        
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
                        ?>  ><a href="index.php?menu-option=<?php echo User::USER_TYPE_CORPORATECLIENT?>"><span class="fa fa-users"></span>&nbsp;&nbsp; <? echo Translation::GetCaption("cocustomer") ?></a></li>
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
                        ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_CUSTOMER_SERVICE?>"><span class="fa fa-cube"></span>&nbsp;&nbsp;<? echo Translation::GetCaption("coservice") ?></a></li>
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
                        ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_WAREHOUSE?>"><span class="fa fa-truck"></span>&nbsp;&nbsp;<? echo Translation::GetCaption("operations") ?></a></li>
        <!--<li ><a href="#"><span class="fa fa-database"></span>&nbsp;&nbsp;Sales</a></li>-->
        
      </ul>
    </div>
    <div class="col-xs-4 col-lg-3" >
      <div class="hidden-xs">
        <div class="top-menu right-main-nav">
          <ul class="nav navbar-nav pull-right">
            <!-- BEGIN NOTIFICATION DROPDOWN --> 
            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte --> 
            <!--<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-bell"></i>
                                <span class="badge badge-default">
                                7 </span>
                                </a>
                                <ul class="dropdown-menu">
                                        <li class="external">
                                                <h3><span class="bold">12 pending</span> notifications</h3>
                                                <a href="#">view all</a>
                                        </li>
                                        <li>
                                                <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">just now</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-success">
                                                                <i class="fa fa-plus"></i>
                                                                </span>
                                                                New user registered. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">3 mins</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-danger">
                                                                <i class="fa fa-bolt"></i>
                                                                </span>
                                                                Server #12 overloaded. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">10 mins</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-warning">
                                                                <i class="fa fa-bell-o"></i>
                                                                </span>
                                                                Server #2 not responding. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">14 hrs</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-info">
                                                                <i class="fa fa-bullhorn"></i>
                                                                </span>
                                                                Application error. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">2 days</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-danger">
                                                                <i class="fa fa-bolt"></i>
                                                                </span>
                                                                Database overloaded 68%. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">3 days</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-danger">
                                                                <i class="fa fa-bolt"></i>
                                                                </span>
                                                                A user IP blocked. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">4 days</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-warning">
                                                                <i class="fa fa-bell-o"></i>
                                                                </span>
                                                                Storage Server #4 not responding dfdfdfd. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">5 days</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-info">
                                                                <i class="fa fa-bullhorn"></i>
                                                                </span>
                                                                System Error. </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="time">9 days</span>
                                                                <span class="details">
                                                                <span class="label label-sm label-icon label-danger">
                                                                <i class="fa fa-bolt"></i>
                                                                </span>
                                                                Storage server failed. </span>
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
                                </ul>
                        </li>--> 
            <!-- END NOTIFICATION DROPDOWN --> 
            <!-- BEGIN INBOX DROPDOWN --> 
            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte --> 
            <!--<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-envelope-open"></i>
                                <span class="badge badge-default">
                                4 </span>
                                </a>
                                <ul class="dropdown-menu">
                                        <li class="external">
                                                <h3>You have <span class="bold">7 New</span> Messages</h3>
                                                <a href="#">view all</a>
                                        </li>
                                        <li>
                                                <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                                        <li>
                                                                <a href="#">
                                                                <span class="photo">
                                                                <img src="../assets/admin/layout3/img/avatar2.jpg" class="img-circle" alt="">
                                                                </span>
                                                                <span class="subject">
                                                                <span class="from">
                                                                Lisa Wong </span>
                                                                <span class="time">Just Now </span>
                                                                </span>
                                                                <span class="message">
                                                                Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="#w">
                                                                <span class="photo">
                                                                <img src="../assets/admin/layout3/img/avatar3.jpg" class="img-circle" alt="">
                                                                </span>
                                                                <span class="subject">
                                                                <span class="from">
                                                                Richard Doe </span>
                                                                <span class="time">16 mins </span>
                                                                </span>
                                                                <span class="message">
                                                                Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="#">
                                                                <span class="photo">
                                                                <img src="../assets/admin/layout3/img/avatar1.jpg" class="img-circle" alt="">
                                                                </span>
                                                                <span class="subject">
                                                                <span class="from">
                                                                Bob Nilson </span>
                                                                <span class="time">2 hrs </span>
                                                                </span>
                                                                <span class="message">
                                                                Vivamus sed nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="#">
                                                                <span class="photo">
                                                                <img src="../assets/admin/layout3/img/avatar2.jpg" class="img-circle" alt="">
                                                                </span>
                                                                <span class="subject">
                                                                <span class="from">
                                                                Lisa Wong </span>
                                                                <span class="time">40 mins </span>
                                                                </span>
                                                                <span class="message">
                                                                Vivamus sed auctor 40% nibh congue nibh... </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="#">
                                                                <span class="photo">
                                                                <img src="../assets/admin/layout3/img/avatar3.jpg" class="img-circle" alt="">
                                                                </span>
                                                                <span class="subject">
                                                                <span class="from">
                                                                Richard Doe </span>
                                                                <span class="time">46 mins </span>
                                                                </span>
                                                                <span class="message">
                                                                Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
                                </ul>
                        </li>--> 
            <!-- END INBOX DROPDOWN --> 
            <!-- BEGIN TODO DROPDOWN --> 
            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte --> 
            <!--<li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-calendar"></i>
                                <span class="badge badge-default">
                                3 </span>
                                </a>
                                <ul class="dropdown-menu extended tasks">
                                        <li class="external">
                                                <h3>You have <span class="bold">12 pending</span> tasks</h3>
                                                <a href="#">view all</a>
                                        </li>
                                        <li>
                                                <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="task">
                                                                <span class="desc">New release v1.2 </span>
                                                                <span class="percent">30%</span>
                                                                </span>
                                                                <span class="progress">
                                                                <span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">40% Complete</span></span>
                                                                </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="task">
                                                                <span class="desc">Application deployment</span>
                                                                <span class="percent">65%</span>
                                                                </span>
                                                                <span class="progress">
                                                                <span style="width: 65%;" class="progress-bar progress-bar-danger" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">65% Complete</span></span>
                                                                </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="task">
                                                                <span class="desc">Mobile app release</span>
                                                                <span class="percent">98%</span>
                                                                </span>
                                                                <span class="progress">
                                                                <span style="width: 98%;" class="progress-bar progress-bar-success" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">98% Complete</span></span>
                                                                </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="task">
                                                                <span class="desc">Database migration</span>
                                                                <span class="percent">10%</span>
                                                                </span>
                                                                <span class="progress">
                                                                <span style="width: 10%;" class="progress-bar progress-bar-warning" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">10% Complete</span></span>
                                                                </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="task">
                                                                <span class="desc">Web server upgrade</span>
                                                                <span class="percent">58%</span>
                                                                </span>
                                                                <span class="progress">
                                                                <span style="width: 58%;" class="progress-bar progress-bar-info" aria-valuenow="58" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">58% Complete</span></span>
                                                                </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="task">
                                                                <span class="desc">Mobile development</span>
                                                                <span class="percent">85%</span>
                                                                </span>
                                                                <span class="progress">
                                                                <span style="width: 85%;" class="progress-bar progress-bar-success" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">85% Complete</span></span>
                                                                </span>
                                                                </a>
                                                        </li>
                                                        <li>
                                                                <a href="javascript:;">
                                                                <span class="task">
                                                                <span class="desc">New UI release</span>
                                                                <span class="percent">38%</span>
                                                                </span>
                                                                <span class="progress progress-striped">
                                                                <span style="width: 38%;" class="progress-bar progress-bar-important" aria-valuenow="18" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">38% Complete</span></span>
                                                                </span>
                                                                </a>
                                                        </li>
                                                </ul>
                                        </li>
                                </ul>
                        </li>--> 
            <!-- END TODO DROPDOWN --> 
            <!-- BEGIN USER LOGIN DROPDOWN --> 
            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
            <li class="dropdown dropdown-user"> <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
              <?php 
                                if(isset($_SESSION['admin']['profile_image']) && trim($_SESSION['admin']['profile_image']) != '')
                                {
                                echo '<img alt="" class="img-circle" src="../../_assets/profile_images/'.$_SESSION['admin']['profile_image'].'">';
                                }
                                else
                                {
                                echo '<img alt="" class="img-circle" src="../assets/admin/layout/img/avatar3_small.jpg"/>';
                                }

                                ?>
              <span class="username username-hide-on-mobile"> <?php echo @$_SESSION['admin']['firstname'].'( '.@$_SESSION['admin']['user_type'].' ) ';?> </span> <i class="fa fa-angle-down"></i> </a>
              <ul class="dropdown-menu dropdown-menu-default">
                <!--<li>
                                        <a href="#">
                                        <i class="icon-user"></i> My Profile </a>
                                </li>
                                <li>
                                        <a href="#">
                                        <i class="icon-calendar"></i> My Calendar </a>
                                </li>
                                <li>
                                        <a href="#">
                                        <i class="icon-envelope-open"></i> My Inbox <span class="badge badge-danger">
                                        3 </span>
                                        </a>
                                </li>
                                <li>
                                        <a href="#">
                                        <i class="icon-rocket"></i> My Tasks <span class="badge badge-success">
                                        7 </span>
                                        </a>
                                </li>
                                <li class="divider">
                                </li>
                                <li>
                                        <a href="#">
                                        <i class="icon-lock"></i> Lock Screen </a>
                                </li>-->
                <li> <a href="login.php?logout=true"> <i class="icon-key"></i> Log Out </a> </li>
              </ul>
            </li>
            <!-- END USER LOGIN DROPDOWN -->
            
          </ul>
        </div>
      </div>
      <ul class="list-inline flags-top-nav">
        <?php $pageName = $_SERVER['PHP_SELF'] ?>
        <li><a href="<? echo $pageName?>?lang=de-DE"><img src="../images/de.png" /></a></li>
        <li><a href="<? echo $pageName?>?lang=en-GB"><img src="../images/en.png" /></a></li>
      </ul>
    </div>
    <!-- END TOP NAVIGATION MENU --> 
  </div>
  <!-- END HEADER INNER --> 
</div>
<!-- END HEADER -->
<div class="clearfix"> </div>
<!-- BEGIN CONTAINER -->
<div class="page-container"> 
  <!-- BEGIN SIDEBAR -->
  <div class="page-sidebar-wrapper"> 
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing --> 
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse"> 
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
  <!-- END SIDEBAR --> 
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
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
  <div class="page-footer-inner"> 2015 &copy; One World Express - OWE ACCOUNTS. </div>
  <div class="scroll-to-top"> <i class="icon-arrow-up"></i> </div>
</div>

<!-- END FOOTER --> 
<!-- END FOOTER --> 
<!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]--> 
<!-- BEGIN CORE PLUGINS --> 

<script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script> 
<!-- END CORE PLUGINS --> 
<!-- BEGIN PAGE LEVEL PLUGINS --> 
<script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/morris/morris.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="../assets/global/plugins/select2/js/select2.min.js"></script> 

<!-- END PAGE LEVEL PLUGINS --> 
<!-- BEGIN THEME GLOBAL SCRIPTS --> 
<script src="../assets/global/scripts/app.min.js" type="text/javascript"></script> 
<!-- END THEME GLOBAL SCRIPTS --> 
<!-- BEGIN PAGE LEVEL SCRIPTS --> 
<script src="../assets/pages/scripts/dashboard.min.js" type="text/javascript"></script> 
<!-- END PAGE LEVEL SCRIPTS --> 
<!-- BEGIN THEME LAYOUT SCRIPTS --> 
<script src="../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script> 
<script src="../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script> 
<script src="../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script> 
<script src="../js/validator.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="../js/common.js"></script> 
<script>
            jQuery(document).ready(function() {
               // Metronic.init(); // init core components
              //  Layout.init(); // init current layout
               // QuickSidebar.init(); // init quick sidebar
                //Demo.init(); // init demo features
            });
$.fn.datepicker.defaults.format = "dd-mm-yyyy";
$.fn.datepicker.defaults.autoclose	= true;
$.fn.datepicker.defaults.daysOfWeekHighlighted	=	[0,6];
$.fn.datepicker.defaults.container	=	'body';
$.fn.datepicker.defaults.defaultDate	=	new Date();



        </script>
<?php $this->renderFooter(); 
        	   DbAccess3::closeConnection();	
               echo "<!--close connection-->";
        ?>
<!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>
