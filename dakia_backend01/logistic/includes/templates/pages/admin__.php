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


<link rel="apple-touch-icon" sizes="57x57" href="../images/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="../images/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="../images/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="../images/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="../images/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="../images/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="../images/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="../images/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="../images/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="../images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="../images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="../images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="../images/favicon-16x16.png">
<link rel="manifest" href="/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="../images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">


<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
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
<!-- <link href="../assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" /> -->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL STYLES -->
<link href="../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
<link href="../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
<!--<link rel="stylesheet" type="text/css" href="../assets/global/plugins/select2/css/select2.css"/>-->
<link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />

<!-- END THEME GLOBAL STYLES -->
<!-- BEGIN THEME LAYOUT STYLES -->
<link href="../assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />

<?php 
if(true)//(trim($UserSessionManager->getCountry()) == 'DE')
{
?>
<link href="../assets/layouts/layout/css/themes/darkblue.germany.min.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="../assets/layouts/layout/css/custom.germany.min.css" rel="stylesheet" type="text/css" />
<?php
}
else
{
?>
<link href="../assets/layouts/layout/css/themes/darkblue.de.min.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="../assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
<?php
}
?>
<!-- END THEME LAYOUT STYLES -->
<link rel="shortcut icon" href="favicon.ico" />
<script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script>
function getReminder()
{
	$.ajax({
        url: "../main/getEmailReminder.php",
        type: "GET",
        dataType:'json',
 	   	data: {
            action: 'GETEMAILREMINDER',
        },
        success: function(data) {
        	// $('#user-new-data').html(data);
        	if (data['count']) {
        		$('#today_reminder').html(data['count']);
        	}
        	if (data['html']){
        		$('#reminder_message').html(data['html']);
        	}
        }
    });
}
function getCsReminder()
{

	$.ajax({
        url: "getEmailReminder.php",
        type: "GET",
        dataType:'json',
 	   	data: {
            action: 'GETCSREMINDER',
        },
        success: function(data) {
        	// $('#user-new-data').html(data);
        	if (data['html']){
        		$('#reminder-message').html(data['html']);
        	}
        }
    });
}
function callnewuser(){
	$.ajax({
        url: "newAjaxUserGet.php",
        type: "GET",
        dataType:'json',
 	   	data: {
            action: 'GETNEWUSERS',
        },
        success: function(data) {
        	// $('#user-new-data').html(data);
        	if (data['count']) {
        		$('#total_user').html(data['count']);
        	}
        	if (data['html']){
        		$('#user-new-data').html(data['html']);
        	}
        }
    });
}

 function updateReminder(id)
{
	$.ajax({
        url: "getEmailReminder.php",
        type: "GET",
 	   	data: {
            action: 'UPDATEREMINDER',
			id:		id
        },
        success: function(data) {
        }
    });
	
}

getCsReminder();
//callnewuser();
getReminder();
</script>
<!-- END THEME STYLES -->
<?php $this->renderHead(); ?>

<style>
.page-header.navbar .top-menu .navbar-nav>li.dropdown .dropdown-toggle:hover {background-color: #004E87 !important; color:#fff !important}

.dropdown-user * span:hover   { color:#fff !important}
.dropdown-language * span:hover   { color:#fff !important}

#reminder-message {
    position: fixed;
    bottom: 50px;
    right: 0;
    width: 200px;
}
#reminder-message-inner {
    margin: 0 auto;
}

</style>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white"> 
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top"> 

  

  
  
  
  <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                
                
                <a href="index.php?menu-option=<?php echo $UserSessionManager->getUserType(); ?>"> 
                <?php
                    $imageLogo	= '';
                            $userImage	=	$UserSessionManager->getLogo();
                            if(trim($UserSessionManager->getThemeId()) == '1')
                            {
                                $imageLogo	=	'<img src="../images/inner-logo.de.png"  class="img-rounded img-responsive white-back" alt="One World Express" style="max-height:93px;"/>';
                            }
                            else if(trim($userImage) != '')
                            {
                                $imageLogo	=	'<img src="../images/userlogo/'.$userImage.'"  class="img-rounded img-responsive white-back" alt="One World Express" style="max-height:93px;" />';
                            }
                            else
                            {
                                $imageLogo	= '<img src="../images/inner-logo.png" class="img-rounded img-responsive white-back" alt="One World Express"  style="max-height:93px;"/>';
                            }
                            echo $imageLogo;
                    ?>
                </a>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                              Menu  <i class="glyphicon glyphicon-menu-hamburger"></i> <i class="fa fa-angle-down"></i> 
                             
                            </a>
                            
                            
                            
                          <?php 
                 $menuOption	=	@$_SESSION['menu-option'];
                 $menuToShow	=	$UserSessionManager->getUserType();
               
                ?>
    			  <ul class="dropdown-menu">
        <li><a href="index.php?menu-option=<?php echo $UserSessionManager->getUserType()?>"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Home</a></li>
        <li           <?php
                        if(trim($menuOption) == "") 
                        {
                            if($UserSessionManager->getUserType() == User::USER_TYPE_ADMIN) 
                            	echo 'class="active"';
                            else 
                            	echo 'class="disabled hide"';
                        }
                        else  if(trim($menuOption) == User::USER_TYPE_ADMIN)
                        {
                        	echo 'class="active"';
                        }
						else 
                        {
                        	echo 'class="disabled hide"';
                        }
						
                      ?>
                        ><a href="index.php?menu-option=<?php echo User::USER_TYPE_ADMIN?>"><span class="fa fa-street-view"></span>&nbsp;&nbsp;Admin</a></li>
        <?php
        if($UserSessionManager->getUserType() != User::USER_TYPE_CLIENT)
        {
        ?>
        <li 
                      	<?php 
                        if(trim($menuOption) == "") 
                        {
                            if(isset($_SESSION['admin']) &&  in_array(trim($UserSessionManager->getUserType()), array(User::USER_TYPE_CLIENT, User::USER_TYPE_CORPORATECLIENT, User::USER_TYPE_WAREHOUSE,User::USER_TYPE_FINANCE, User::USER_TYPE_CUSTOMER_SERVICE, User::USER_TYPE_SALES))) 
                            	echo 'class="active"';
                            else if(isset($_SESSION['admin']) && $UserSessionManager->getUserType() == User::USER_TYPE_ADMIN)
                            	echo 'class="active"';
                            else 
                            	echo 'class="disabled hide"';
                        }
                        else  if(in_array(trim($menuOption), array(User::USER_TYPE_ADMIN,User::USER_TYPE_CLIENT, User::USER_TYPE_CORPORATECLIENT, User::USER_TYPE_WAREHOUSE,User::USER_TYPE_FINANCE,User::USER_TYPE_CUSTOMER_SERVICE, User::USER_TYPE_SALES)))
                        {
                        	echo 'class="active"';
                        }
						else 
                        {
                        	echo 'class="disabled hide"';
                        }
						?> ><?
                        if(in_array(trim($UserSessionManager->getUserType()), array(User::USER_TYPE_ADMIN,User::USER_TYPE_CLIENT, User::USER_TYPE_CORPORATECLIENT, User::USER_TYPE_WAREHOUSE, User::USER_TYPE_FINANCE,User::USER_TYPE_CUSTOMER_SERVICE, User::USER_TYPE_SALES)))  
                      
                        {
                        ?>  <a href="index.php?menu-option=<?php echo User::USER_TYPE_CLIENT?>">
                        <span class="fa fa-users"></span>&nbsp;&nbsp;<? echo Translation::GetCaption("customer") ?></a>
                        <?php }
                        ?>
                        </li>
         <?
         }
         ?>               
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
						else if(in_array(trim($UserSessionManager->getUserType()), array(User::USER_TYPE_ADMIN, User::USER_TYPE_CORPORATECLIENT)))  
						{
						}
						else 
                        {
                        	echo 'class="disabled hide"';
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
						else if(in_array(trim($UserSessionManager->getUserType()), array(User::USER_TYPE_ADMIN, User::USER_TYPE_FINANCE)))  
						{
						}
						else 
                        {
                        	echo 'class="disabled hide"';
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
						else if(in_array(trim($UserSessionManager->getUserType()), array(User::USER_TYPE_ADMIN, User::USER_TYPE_CUSTOMER_SERVICE)))  
						{
						}
						else 
                        {
                        	echo 'class="disabled hide"';
                        }
                        ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_CUSTOMER_SERVICE?>"><span class="fa fa-cube"></span>&nbsp;&nbsp;<? echo Translation::GetCaption("coservice") ?></a></li>
				 <li 
                      	<?php 
                        if(trim($menuOption) == "") 
                        {
                            if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_SALES) 
                            	echo 'class="active"';
                            else if(isset($_SESSION['admin']) && $_SESSION['admin']['user_type'] == User::USER_TYPE_ADMIN)
                            	echo '';
                            else 
                            	echo 'class="disabled hide"';
                        } 
                        else  if(trim($menuOption) == User::USER_TYPE_SALES)
                        {
                        	echo 'class="active"';
                        }
						else if(in_array(trim($UserSessionManager->getUserType()), array(User::USER_TYPE_ADMIN, User::USER_TYPE_SALES)))  
						{
						}
						else 
                        {
                        	echo 'class="disabled hide"';
                        }
                        ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_SALES?>"><span class="fa fa-cube"></span>&nbsp;&nbsp;<? echo Translation::GetCaption("SALES") ?></a></li>                        
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
						else if(in_array(trim($UserSessionManager->getUserType()), array(User::USER_TYPE_ADMIN, User::USER_TYPE_WAREHOUSE)))  
						{
						}
						else 
                        {
                        	echo 'class="disabled hide"';
                        }
                        ?> ><a href="index.php?menu-option=<?php echo User::USER_TYPE_WAREHOUSE?>"><span class="fa fa-truck"></span>&nbsp;&nbsp;<? echo Translation::GetCaption("operations") ?></a></li>
        <!--<li ><a href="#"><span class="fa fa-database"></span>&nbsp;&nbsp;Sales</a></li>-->
        
      </ul>   
                            
                            
                        </li>
                      
                        
                    
                        <li class="dropdown dropdown-user">
                  
                          
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            
                            
                            
                  <?php 
                               if( trim($UserSessionManager->getProfileImage()) != '')
                                {
                                echo '<img alt="" class="img-circle" src="../_assets/profile_images/'.$UserSessionManager->getProfileImage().'">';
                                }
                                else
                                {
                                echo '<img alt="" class="img-circle" src="../assets/layouts/layout/img/avatar3_small.jpg"/>';
                                }
                      

                                ?>
              <span class="username username-hide-on-mobile"> <?php echo @$_SESSION['admin']['firstname'].'( '.@$_SESSION['admin']['user_type'].' ) ';?> </span> <i class="fa fa-angle-down"></i>           
                            
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-default">
                             <li> <a href="view_profile.php">
                             <i class="icon-user"></i> My Profile</a>
                              </li>
                <li> <a href="login.php?logout=true"> 
                <i class="icon-key"></i> Log Out </a> 
                </li>
                            
                            
                            </ul>
                        </li>
                     
                     
                     
                        <li class="dropdown dropdown-language">
                         <?php $pageName = $_SERVER['PHP_SELF'] ;
                         if(@$_SESSION['lang'] == 'de-DE')
                        {
                        ?>   <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img src="../images/de.png" />
                                <span class="langname"> Germany </span>
                                <i class="fa fa-angle-down"></i>
                            </a><?php
                        }
                        else
                        {
                        ?>   <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">    <img src="../images/en.png" />
                                <span class="langname"> UK </span>
                                <i class="fa fa-angle-down"></i>
                            </a><?php
                        }
                         
                         
                         ?>
                         
                         
                         
                            
                            
                            
                            
             
                            
                            
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="<? echo $pageName?>?lang=de-DE"><img src="../images/de.png" /> Germany </a>
                                </li>
                                <li>
                                    <a href="<? echo $pageName?>?lang=en-GB"><img src="../images/en.png" /> Uk </a>
                                </li>
                                
                                
                            </ul>
                        </li>
                        
                        <?php 
    		
			if(isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
				$displayOption 	=	@$_SESSION['menu-option'];	
			else
				$displayOption 	=		$UserSessionManager->getUserType() ;	

			switch ($displayOption) {
			
				case User::USER_TYPE_SALES :
                 ?>
                 
				<li  class="dropdown dropdown-registeruser" >
				    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"><i class="fa fa-bell"></i>Reminder
				       <span class="label label-danger" id="today_reminder"></span><i class="fa fa-angle-down"></i>
				    </a>
				    
				     <ul class="dropdown-menu dropdown-menu-default " id="reminder_message">
				    </ul>
				</li>
				<?php 
				break;
					
			}

     ?>
                       
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
  
  
  
  
  
  
  
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
<?php 
if(trim($UserSessionManager->getThemeId()) == '1')
{
?>
<style>




</style>
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
<!-- BEGIN FOOTER -->
<div class="page-footer">
  <div class="row">
  <div class="container">
    
      <div class="col-sm-2">
        <div class="foot-header"> HÄNDLERBUND<br /><br /></div>
        <div class="foot-links">
          <a href="https://www.haendlerbund.de/en" target="_blank">About Us</a>
          <a href="https://www.haendlerbund.de/en/haendlerbund/about-us/our-team" target="_blank">Our Team</a>
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
		<img src="../images/onlinehaendler-news-logo-white.png" class="img-responsive img-thumbnail">
      
      
    </div>
    </div>
  	</div><!--/row-->
   
	 
    
    <div class="scroll-to-top"> <i class="icon-arrow-up"></i> </div>
    
</div>
<?php 
}
else
{
?>
<!-- BEGIN FOOTER -->
<div class="page-footer">
<?
	if(isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
				$displayOption 	=	@$_SESSION['menu-option'];	
			else
				$displayOption 	=		$UserSessionManager->getUserType() ;	
			if( $displayOption ==  User::USER_TYPE_CUSTOMER_SERVICE )
            { 
?>
  <div id="reminder-message" class="right-bottom" >
  
  </div>
<?
	}
?>
  <div style="clear:both;"></div>
  <div class="page-footer-inner"> 1998 - 2016 &copy; One World Express</div>
  <div class="scroll-to-top"> <i class="icon-arrow-up"></i> </div>
</div>
<?php
}
?>

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
<!--
<script src="../assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js" type="text/javascript"></script> 
<script src="../assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" type="text/javascript"></script> 
-->
<script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script> 
<!--<script type="text/javascript" src="../assets/global/plugins/select2/js/select2.min.js"></script> -->

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
