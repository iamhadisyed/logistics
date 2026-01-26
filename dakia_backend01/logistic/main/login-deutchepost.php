<?php
////////////////////////////////////////////////////
//
// Controller for Admin - login page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    public $username = NULL;
    public $password = NULL;
    public $user_name = NULL;
    public $msg = NULL;
    public $error_msg = NULL;
    public $show_forget = NULL;
    public $show_captcha = NULL;

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
?>        <style>

.mt-100 {
    margin-top: 100px; 
}
.mb-100 {
    margin-bottom: 100px;
}

.icon {
    width: 32px;
    height: 32px;
    text-align: center;
    padding: 7px 8px;
    border: 2px solid;
    border-radius: 50%;
}

.btn-circle {
    border-radius: 20px;
}

.input-group input {
    border: 0;
    box-shadow: none;
    padding-right: 30px;
}
.input-group input:focus,
.input-group input:active {
    outline: 0;
    box-shadow: none;
}
.input-group-btn:last-child>.btn {
    z-index: 2;
    margin-left: -18px;   
    border-radius: 20px;
}


/* Header */
.large-header {
  position: relative;
  width: 100%;
  background: #fff;
  overflow: hidden;
  background-size: cover;
  background-position: center center;
  z-index: 1;
   /* background-image: url("76.jpg");*/
   background: #004E87
}

.main-title {
  position: absolute;
  margin: 0;
  padding: 0;
  color: #4C4B53;
  top: 0%;
  left: 50%;
  -webkit-transform: translate3d(-50%, 5%, 0);
  transform: translate3d(-50%, 5%, 0);
}

.btn-success {
    color: #fff;
    background-color: #B9004D;
    border-color: #B9004D;
}

.btn-success:hover {
    color: #fff;
    background-color:#4C4B53;
    border-color: #4C4B53;
}

.demo-1 .main-title {
  text-transform: uppercase;
  font-size: 4.2em;
  letter-spacing: 0.1em;
}
.main-title .thin {
  font-weight: 200;
}
@media only screen and (max-width: 768px) {
  .demo-1 .main-title {
    font-size: 3em;
  }
}



.login-container{
    margin-top: 5%;
    margin-bottom: 5%;
}
.login-form-1{
    padding: 5%;
    box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.2), 0 9px 26px 0 rgba(0, 0, 0, 0.19);
}
.login-form-1 h3{
    text-align: center;
    color: #333;
}
.login-form-2{
    padding: 5%;
    background: #0062cc;
    box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.2), 0 9px 26px 0 rgba(0, 0, 0, 0.19);
}
.login-form-2 h3{
    text-align: center;
    color: #fff;
}
.login-container form{
    padding: 10%;
}
.btnSubmit
{
    width: 50%;
    border-radius: 1rem;
    padding: 1.5%;
    border: none;
    cursor: pointer;
}
.login-form-1 .btnSubmit{
    font-weight: 600;
    color: #fff;
    background-color: #0062cc;
}
.login-form-2 .btnSubmit{
    font-weight: 600;
    color: #0062cc;
    background-color: #fff;
}
.login-form-2 .ForgetPwd{
    color: #fff;
    font-weight: 600;
    text-decoration: none;
}
.login-form-1 .ForgetPwd{
    color: #0062cc;
    font-weight: 600;
    text-decoration: none;
}

.login .content .form-actions {
    clear: both;
    border: 0px;
    border-bottom: 1px solid #eee;
    padding: 0px 30px 25px 30px;
    margin-left: -30px;
    margin-right: -30px;
}

.login .content .rememberme {
    margin-left: 8px;
    margin-top: 10px;
}

.login .content .check {
    color: #8290a3;
}



.form-heading { font-size:23px;}
.panel h2{ color:#333; font-size:18px; margin:10px 0 8px 0;}
.panel p { color:#333; font-size:14px; margin-bottom:30px; line-height:12px;}
.login-form .form-control {
  background: #f7f7f7 none repeat scroll 0 0;
  border: 1px solid #d4d4d4;
  border-radius: 4px;
  font-size: 14px;
  height: 50px;
  line-height: 50px;
}
.main-div {
  background: #ffffff none repeat scroll 0 0;
  border-radius: 2px;
  padding: 50px 70px 70px 71px;
}

.login-form .form-group {
  margin-bottom:10px;
}
.login-form{ text-align:center;}
.forgot a {
  color: #777777;
  font-size: 14px;
  text-decoration: underline;
}

.forgot {
  text-align: left; margin-bottom:10px;
}
.botto-text {
  color: #ffffff;
  font-size: 14px;
  margin: auto;
}
.login-form .btn.btn-primary.reset {
  background: #ff9900 none repeat scroll 0 0;
}
.back { text-align: left; margin-top:10px;}
.back a {color: #444444; font-size: 13px;text-decoration: none;}


.login-form  .btn.btn-primary {
   color: #ffffff;
  font-size: 14px;
  width: 100%;
  height: 50px;
  line-height: 50px;
  padding: 0;
  background: #004E87!important;
  border-color: #004E87!important;

}

.login-form  .btn.btn-primary:hover {
  background: orange!important;
  border-color: orange!important;

}


.login-form  .btn.btn-info {
  background: orange!important;
  border-color: orange!important;
  color: #ffffff;
  font-size: 14px;
  width: 100%;
  height: 50px;
  line-height: 50px;
  padding: 0;
}

.login-form  .btn.btn-info:hover {
  background: #004E87!important;
  border-color: #004E87!important;
}

.text-white { color: #fff }
</style>
        
        <div class="bg-body">

	<div id="large-header" class="large-header">
		<canvas id="demo-canvas"></canvas>
		<div class="main-title"><div class="container">
			<div class="row">
				<!-- Button trigger modal -->
				<div class="col-12 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3">
					<div class="content">
						<h1 class="text-center text-white">WE DELIVER <span style="color:#D00322">TRUST.</span>
						</h1>
						<p class="lead text-center text-white">By delivering confidential letters we help protect people's and support democracy. </p>
						<div class="login-form card">
							<div class="main-div">
								<div class="panell">
									<center><img src="../images/deutsche-post-logo.svg" class="img-responsive"></center>
									<h2>Sign in to the portal</h2>
									<p>Please enter your username and password to log in.</p>
								</div>
                                <?php
                        $cookie = array();
                        if (isset($_COOKIE["ONEWORLDAUTH"]))
                            parse_str($_COOKIE["ONEWORLDAUTH"], $cookie);
                
                if(isset($_GET['password'])&& $_GET['password']=="reset") {$this->msg = "Your password has been reset successfully. Please login with your new details."; }
                if (!empty($this->msg)) { ?>
                    <div class="alert  alert-success" ><?php echo $this->msg; ?></div>
                <?php } ?>
                    
                <?php if (!empty($this->error_msg)) { ?>
                    <div class="alert  alert-danger" ><?php echo $this->error_msg; ?></div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12">
                    <?php $this->flashMsg->display();
                    ?>
                        </div>
                </div>
                <form class="form-signin" method="post" id="Login">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="user_name" required autofocus name="username" id="username" value="<?php
                                if (isset($cookie["usr"])) {
                                    echo $cookie["usr"];
                                } else {
                                    echo $this->username;
                                }
                                ?>">
									</div>
									<div class="form-group">
										<input type="password" class="form-control" placeholder="Password" required name="password" id="password" value="<?php
                                if (isset($cookie["hash"])) {
                                    echo base64_decode($cookie["hash"]);
                                }
                                ?>">
										
									</div>
                                    <?php
                        if ($_SESSION['login_attemp'] >=3){ ?>
                        <div id="imgdiv">
                            <img id="img" src="capcha.php">
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9">Captcha</label>
                            <div class="">
                                <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Captcha" name="captcha"  id="captcha" value=""/>
                               
                            </div>
                        </div>
                        <?php } ?>
									<!--<div class="forgot">
										<a href="reset.html">Forgot password?</a>
									</div>-->
									<div class="row">
										<div class="checkbox col-md-12 text-left">
											<label class="forgot">
												<input type="checkbox" value="remember" <?php echo (isset($_COOKIE['ONEWORLDAUTH']) ? ' checked="checked"' : ''); ?>>  Remember me
											</label>
										</div>
										<!--<div class="checkbox col-md-6 text-right">
											<label class="forgot">
												<input name="remember" type="checkbox" value="Archive Server"> Archive Server
											</label>
										</div>-->
									</div>
									<div class="row">
										<div class="col-md-12 text-left">
											<button type="submit" class="btn btn-primary">Login</button>
										</div>
										<!--<div class="col-md-6 text-left">
											<button type="submit" class="btn btn-info">Register</button>
										</div>-->
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
        <?php
    }
    public function renderHead() {
        ?>
        
            <?php
    }
    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        
    }

//Get an array with geoip-infodata
       public function geoCheckIP($ip)
       {
               //check, if the provided ip is valid
               if(!filter_var($ip, FILTER_VALIDATE_IP))
               {
                       throw new InvalidArgumentException("IP is not valid");
               }

               //contact ip-server
               $response=@file_get_contents('http://www.netip.de/search?query='.$ip);
               if (empty($response))
               {
                       throw new InvalidArgumentException("Error contacting Geo-IP-Server");
               }

               //Array containing all regex-patterns necessary to extract ip-geoinfo from page
               $patterns=array();
               $patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
               $patterns["country"] = '#Country: (.*?)&nbsp;#i';
               $patterns["state"] = '#State/Region: (.*?)<br#i';
               $patterns["town"] = '#City: (.*?)<br#i';

               //Array where results will be stored
               $ipInfo=array();

               //check response from ipserver for above patterns
               foreach ($patterns as $key => $pattern)
               {
                       //store the result in array
                       $ipInfo[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : 'not found';
               }

               return $ipInfo;
       }
	   
	   
    /*     * *
     * Controller logic goes here
     */

    public function init() {
        
        $ip = '213.246.110.102';
//     $ip='94.219.40.96';
        //      $IpDeatils  =   $this->geoCheckIP($ip);
        $ipCountry = 'DE'; //$IpDeatils['country'];
        if (trim($ipCountry) != '') {
            $countryIpDara = explode('-', $ipCountry);
            $this->ipDetailCountry = trim($countryIpDara[0]);
        }
        $userSection = SessionManager::getUser();
        if(isset($_POST['action']) && $_POST['action'] === "update_user"){
            $rtnMsg = "";
            $userId = trim($_POST['user_id']);
            $user = new User($userId);
            if(isset($_POST['set_ignore']) && $_POST['set_ignore'] == "ignore"){
                $user->setIsTcAgreed("i");
                $_SESSION["is_tc_agreed"] = "i";   // Set user t&c check
                $rtnMsg = "ignore";
            }else{
                $_SESSION["is_tc_agreed"] = "y";   // Set user t&c check
                $user->setIsTcAgreed("y");
                $user->setTcAgreedDate(time());
                $rtnMsg = "yes";
            }
            $user->save();
            echo $rtnMsg;
            die;
        }
        $this->setTitle("Please Login");
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        Translation::PopulateKeywordsArray($language);
        t_on(); // turn on trace for this page
        // Log user out?
        if (util_request("logout") == "true") {
            $data = $_SESSION["user_account"];
            User::logUserOut();
            //util_redirect("../main/index.php");
            //  echo "../main/index.php?data=".$data;
            //  die;
            util_redirect("login-deutchepost.php");
            die;
        }
        /* ------------------------------------------------------------------------------ */
        // get vars
        $this->username = DbAccess3::escape(((isset($_POST['username']) ? strip_tags($_POST['username']) : '')));
        $this->password = DbAccess3::escape((isset($_POST['password']) ? strip_tags($_POST['password']) : ''));
        $this->user_name = DbAccess3::escape((isset($_POST['user_name']) ? strip_tags($_POST['user_name']) : ''));
        /* ------------------------------------------------------------------------------ */
//      print_r($_POST);
        // process form
        if (isset($_POST['btn_login']) || (isset($_POST['username']) && isset($_POST['password']))) {
            // login
            $userLoginCheck = User::getUser($this->username, $this->password);
            
            if($userLoginCheck && !empty ($_SESSION["is_tc_agreed"]) && $_SESSION["is_tc_agreed"] ==  'n'){
                $userId = $_SESSION["user_id"];
                if($userId >! 0)
                    $userId = 0;
            ?>
    <style type="text/css">
        .btn_red{
            background-color: #e04a49 !important;
            border-color: #e04a49 !important;
            color: #fff !important;
        }
        .btn_green{
            background-color: #006600 !important;
            border-color: #006600 !important;
            color: #fff !important;
        }
        .btn_blue{
            background-color: #004E87 !important;
            border-color: #004E87 !important;
            color: #fff !important;
        }
    </style>
    <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
             var  userId = "<?php echo $userId; ?>";
        swal({
            title: "Data Policy Update",
                text: 'As you may be aware, new rules are coming into place this month regarding data protection. We are committed to ensuring that any personal data we hold is protected in accordance with the data protections laws taking effect on the 25th May 2018. The policy requires that you opt in to continue receiving emails from us including product updates, promotion and referral opportunities. Please read our updated policy and Opt in&trade; to continue.				</br><a target="_blank" href="https://www.oneworldexpress.com/terms-conditions/" >Terms & Conditions</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="https://www.oneworldexpress.com/impressum/" >Privacy Policy</a>',
            html: true,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn_green btn btn-lg",
            confirmButtonText: "Opt-in",
            cancelButtonText: "Opt-out",
            cancelButtonClass: "btn_red btn btn-lg",
            closeOnConfirm: true,
            closeOnCancel: true
            },
            function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    data: {action: "update_user", user_id: userId},
                    success: function(data){ 
                        if(data == 'yes'){
                            location.href = "index.php"
                        }
                    },
                    error: function() {

                    }
                });
            }
        });
        <?php   $now = time();
                $date = '2018/05/25';
                if (strtotime($date) >= $now) { ?>
                    $(".btn_red").before('<button class="btn btn-lg btn_blue" tabindex="2" style="display: inline-block;">Ignore</button>&nbsp;');
        <?php   } ?>
            $(".btn_blue").click(function(){
                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    data: {action: "update_user", user_id: userId,set_ignore:'ignore'},
                    success: function(data){ 
                        if(data == 'ignore'){
                            location.href = "index.php"
                        }
                    },
                    error: function() {

                    }
                });
                swal.close();
            });
        });
    </script>
                <?php
            } else if ($userLoginCheck) {
                $_SESSION["archive_server"] = $_POST["archive"];
                //if (isset($_POST["remember"]) && $_POST["remember"] == '1') {
//                    setcookie("ONEWORLDEXPRESS[USERNAME]", sha1($this->username), time() + (7 * 24 * 3600), '/');
//                    setcookie("ONEWORLDEXPRESS[PASSWORD]", sha1($this->password), time() + (7 * 24 * 3600), '/');
                //$success = $this->goToUserPage();
                if (isset($_POST['remember']) && $_POST['remember'] == 1) {
                    $cookie_time = (3600 * 24 * 90);
                    setcookie('ONEWORLDAUTH', 'usr=' . $this->username . '&hash=' . base64_encode($this->password), time() + $cookie_time);
                } else if (isset($_COOKIE['ONEWORLDAUTH'])) {
                    $past = time() - 100;
                    setcookie('ONEWORLDAUTH', 'gone', $past);
                }
                //}
                /*                
                 * Get all permissions for a login user
                 * Check if session is already exsist
                 * return session array
                 */
                if(empty($_SESSION['allPermissions'])){
                    $user = SessionManager::getUser();
                    $userId = $user->getId();
                    $_SESSION['allPermissions'] = Permissions::getAllPermissions($userId);
                }
                util_redirect("index.php");
                
            } else {
                $this->flashMsg->error("You have provided wrong Username or Password.","login-deutchepost.php");

            }
        }
        if (isset($_POST['user_name']) && !empty($_POST['user_name'])) {
            $userName = DbAccess3::escape($_POST['user_name']);
            $userFilter = new UserAccountFilter();
            $userFilter->addFilter("user_name = '" . $userName . "' AND active_flag = '1'");
            $userData = $userFilter->getColumnList('id, user_type, user_name,user_account, full_name,email');
            if (count($userData) > 0) {
                $userData = $userData[0];
                $UserName = $userData->getUserName();
                $FullName = $userData->getFirstName();
                $Email = $userData->getEmail();
                $UserAgent = $_SERVER['HTTP_USER_AGENT'];
                $time = time();
                $Token = md5($time);
                $DateExpire = date('Y-m-d H:i:s', strtotime('+1 day'));
                $DateCreated = date('Y-m-d H:i:s');
                $IsExpire = 0;
                $IpAddress = $this->getClientIp();
                $ForgetPasswordRequest = new ForgetPasswordRequest();
                $ForgetPasswordRequest->setUserName($UserName);
                $ForgetPasswordRequest->setIpAddress($IpAddress);
                $ForgetPasswordRequest->setUserAgent($UserAgent);
                $ForgetPasswordRequest->setToken($Token);
                $ForgetPasswordRequest->setDateExpire($DateExpire);
                $ForgetPasswordRequest->setDateCreated($DateCreated);
                $ForgetPasswordRequest->setIsExpire($IsExpire);
                $ForgetPasswordRequest->save();
                $msg = 'Dear ' . $FullName . ", <br><br><br>The following link leads you to One World Smart Track where you can set a new password for access to your account.<br><br><a href='http://oneworldexpress.co.uk/remote/main/verify_email_token.php?token=" . $Token . "'>http://oneworldexpress.co.uk/remote/main/verify_email_token.php?token=" . $Token . "</a><br>This unique link will only remain valid for a period of 24 hours. However, you can request a new password link at any time.<br><br>If you cannot open the link by clicking on it, copy and paste this URL into your browser's address bar.<br>If you've received this message in error or did not request to reset your password, please do not click on the link. You do not need to take any further action and can safely discard this message.<br><br>  <strong>Best Regards</strong>,<br><br>   <strong>Customer Service Team</strong><br> <strong>One World Express Ltd</strong>";
                $headers = "From: One World Express<itsupport@oneworldexpress.com> \r\n";
                $headers .= "Reply-To: One World Express<itsupport@oneworldexpress.com> \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                mail($Email, 'Forget Password', $msg, $headers);
                $this->msg = "Please check your e-mail associated wtih this user name.";
                $this->flashMsg->success($this->msg);
            } else {
                $this->error_msg = "There is no account associated with this user name.";
                echo '<pre>';
                print_r($this->error_msg);
                echo '</pre>';
                die;
                $this->flashMsg->error($this->error_msg);
            }
        }
    }

    public function getClientIp() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN_LOGIN_DEUCHE);
$PageObj->show();
?>
