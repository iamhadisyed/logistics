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
		
        ?>



<!--    <?php
        $imageLogo = '';



        if (isset($_GET['data'])) {
            $accountNumber = ($_GET['data']);
            $userfilterClass = new UserFilter();
            $userfilterClass->addUserAccountFilter($accountNumber);
            if ($userfilterClass->getCount() > 0) {
                $userData = $userfilterClass->getList();
                $userImage = $userData[0]->getLogo();
                if (trim($userImage) != '') {
                    $imageLogo = '<img src="../images/userlogo/' . $userImage . '"  height="100"/>';
                } else {
                    $imageLogo = '<img src="../images/inner-logo.png" height="100"/>';
                }
            }
        } else {
            $imageLogo = '<img src="../images//inner-logo.png" class="img-responsive"/>';
        }
        echo $imageLogo;
        ?>
        </div> -->
    

        <div class="limiter">


		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="images/rt-main-images.png" alt="IMG">
				</div>

				<form class="login100-form form-signin"  method="post">

					<center><img class="img-responsive" src="images/rt-logo.png" alt="RT"></center>

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
         
                  <div class="col-md-12">
                  <?php $this->flashMsg->display();
                  ?>
                      </div>
           





					<span class="login100-form-title">
						Sign in to the portal

					</span>

					<div class="wrap-input100 validate-input">
						

						 <input type="text" class="input100 form-control" placeholder="User Name" required autofocus name="username" id="username" value="<?php
                              if (isset($cookie["usr"])) {
                                  echo $cookie["usr"];
                              } else {
                                  echo $this->username;
                              }
                              ?>">


						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
					

  <input type="password" class="form-control input100" placeholder="Password" required name="password" id="password" value="<?php
                              if (isset($cookie["hash"])) {
                                  echo base64_decode($cookie["hash"]);
                              }
                              ?>">


						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					



                  


					<div class="container-login100-form-btn">
					

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

                        <button class="login100-form-btn" type="submit">  Login to RT</button>

                        

					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							  <label class="checkbox ml-5">
                  <input type="checkbox" value="remember" <?php echo (isset($_COOKIE['ONEWORLDAUTH']) ? ' checked="checked"' : ''); ?>>
                  Remember me
              </label>
						</span>
					
					</div>

					<div class="text-center p-t-136">
					
					</div>
				</form>
			</div>
		</div>
	</div>
	
           


             
                








   <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">


<style>
    





body, html {
	height: 100%;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
	background-size: 400% 400%;
	animation: gradientBG 5s ease infinite;

}


@keyframes gradientBG {
	0% {
		background-position: 0% 50%;
	}
	50% {
		background-position: 100% 50%;
	}
	100% {
		background-position: 0% 50%;
	}
}

/*---------------------------------------------*/
a {
  font-family: 'Poppins', sans-serif;
	font-size: 14px;
	line-height: 1.7;
	color: #666666;
	margin: 0px;
	transition: all 0.4s;
	-webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
}

a:focus {
	outline: none !important;
}

a:hover {
	text-decoration: none;
  color: #E4312A;
}

/*---------------------------------------------*/
h1,h2,h3,h4,h5,h6 {
	margin: 0px;
}

p {
  font-family: 'Poppins', sans-serif;
	font-size: 14px;
	line-height: 1.7;
	color: #666666;
	margin: 0px;
}

ul, li {
	margin: 0px;
	list-style-type: none;
}


/*---------------------------------------------*/
input {
	outline: none;
	border: none;
}

textarea {
  outline: none;
  border: none;
}

textarea:focus, input:focus {
  border-color: transparent !important;
}

input:focus::-webkit-input-placeholder { color:transparent; }
input:focus:-moz-placeholder { color:transparent; }
input:focus::-moz-placeholder { color:transparent; }
input:focus:-ms-input-placeholder { color:transparent; }

textarea:focus::-webkit-input-placeholder { color:transparent; }
textarea:focus:-moz-placeholder { color:transparent; }
textarea:focus::-moz-placeholder { color:transparent; }
textarea:focus:-ms-input-placeholder { color:transparent; }

input::-webkit-input-placeholder { color: #999999; }
input:-moz-placeholder { color: #999999; }
input::-moz-placeholder { color: #999999; }
input:-ms-input-placeholder { color: #999999; }

textarea::-webkit-input-placeholder { color: #999999; }
textarea:-moz-placeholder { color: #999999; }
textarea::-moz-placeholder { color: #999999; }
textarea:-ms-input-placeholder { color: #999999; }

/*---------------------------------------------*/
button {
	outline: none !important;
	border: none;
	background: transparent;
}

button:hover {
	cursor: pointer;
}

iframe {
	border: none !important;
}


/*//////////////////////////////////////////////////////////////////
[ Utility ]*/
.txt1 {
    font-family: 'Poppins', sans-serif;
  font-size: 13px;
  line-height: 1.5;
  color: #999999;
}

.txt2 {
    font-family: 'Poppins', sans-serif;
  font-size: 13px;
  line-height: 1.5;
  color: #666666;
}


/*//////////////////////////////////////////////////////////////////
[ login ]*/

.limiter {
  width: 100%;
  margin: 0 auto;
}

.container-login100 {
  width: 100%;  
  min-height: 100vh;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  padding: 15px;
/* Permalink - use to edit and share this gradient: https://colorzilla.com/gradient-editor/#ff1a00+0,3a8aca+100 */
/*background: rgb(255,26,0);
background: -moz-linear-gradient(-135deg,  rgba(255,26,0,1) 0%, rgba(58,138,202,1) 100%);
background: -webkit-linear-gradient(-135deg,  rgba(255,26,0,1) 0%,rgba(58,138,202,1) 100%);
background: linear-gradient(-135deg,  rgba(255,26,0,1) 0%,rgba(58,138,202,1) 100%); 
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff1a00', endColorstr='#3a8aca',GradientType=1 ); */

}

.wrap-login100 {
  width: 960px;
  background: #fff;
  border-radius: 10px !important;
  overflow: hidden;

  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding: 100px 130px 96px 95px;
  border: 10px solid #3A8ACA;
}

/*------------------------------------------------------------------
[  ]*/
.login100-pic {
  width: 316px;
  padding-top:72px;
}

.login100-pic img {
  max-width: 100%;
}


/*------------------------------------------------------------------
[  ]*/
.login100-form {
  width: 290px;
}

.login100-form-title {
  font-family: 'Poppins', sans-serif;
  font-size: 24px;
  color: #333333;
  line-height: 1.2;
  text-align: center;

  width: 100%;
  display: block;
  padding-bottom: 14px;
  padding-top: 34px;
  font-weight: 700
}


/*---------------------------------------------*/
.wrap-input100 {
  position: relative;
  width: 100%;
  z-index: 1;
  margin-bottom: 10px;
}

.input100 {
  font-family: 'Poppins', sans-serif;
  font-size: 15px;
  line-height: 1.5;
  color: #666666;

  display: block;
  width: 100%;
  background: #e6e6e6;
  height: 50px;
  border-radius: 25px !important;
  padding: 0 30px 0 68px;
}


/*------------------------------------------------------------------
[ Focus ]*/
.focus-input100 {
  display: block;
  position: absolute;
  border-radius: 25px;
  bottom: 0;
  left: 0;
  z-index: -1;
  width: 100%;
  height: 100%;
  box-shadow: 0px 0px 0px 0px;
  color: rgba(38,147,255, 0.8);
}

.input100:focus + .focus-input100 {
  -webkit-animation: anim-shadow 0.5s ease-in-out forwards;
  animation: anim-shadow 0.5s ease-in-out forwards;
}

@-webkit-keyframes anim-shadow {
  to {
    box-shadow: 0px 0px 70px 25px;
    opacity: 0;
  }
}

@keyframes anim-shadow {
  to {
    box-shadow: 0px 0px 70px 25px;
    opacity: 0;
  }
}

.symbol-input100 {
  font-size: 15px;

  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  align-items: center;
  position: absolute;
  border-radius: 25px;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  padding-left: 35px;
  pointer-events: none;
  color: #666666;

  -webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
  transition: all 0.4s;
}

.input100:focus + .focus-input100 + .symbol-input100 {
  color: #E4312A;
  padding-left: 28px;
}

/*------------------------------------------------------------------
[ Button ]*/
.container-login100-form-btn {
  width: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  padding-top: 0px;
}

.login100-form-btn {
  
  font-size: 15px;
  line-height: 1.5;
  color: #fff;
  text-transform: uppercase;

  width: 100%;
  height: 50px;
  border-radius: 25px !important;
  background: #E4312A;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0 25px;

  -webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
  transition: all 0.4s;
}

.login100-form-btn:hover {
  background: #333333;
}



/*------------------------------------------------------------------
[ Responsive ]*/



@media (max-width: 992px) {
  .wrap-login100 {
    padding: 177px 90px 33px 85px;
  }

  .login100-pic {
    width: 35%;
  }

  .login100-form {
    width: 50%;
  }
}

@media (max-width: 768px) {
  .wrap-login100 {
    padding: 100px 80px 33px 80px;
  }

  .login100-pic {
    display: none;
  }

  .login100-form {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .wrap-login100 {
    padding: 100px 15px 33px 15px;
  }
}


/*------------------------------------------------------------------
[ Alert validate ]*/

.validate-input {
  position: relative;
}

.alert-validate::before {
  content: attr(data-validate);
  position: absolute;
  max-width: 70%;
  background-color: white;
  border: 1px solid #c80000;
  border-radius: 13px;
  padding: 4px 25px 4px 10px;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -moz-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  -o-transform: translateY(-50%);
  transform: translateY(-50%);
  right: 8px;
  pointer-events: none;

  font-family: Poppins-Medium;
  color: #c80000;
  font-size: 13px;
  line-height: 1.4;
  text-align: left;

  visibility: hidden;
  opacity: 0;

  -webkit-transition: opacity 0.4s;
  -o-transition: opacity 0.4s;
  -moz-transition: opacity 0.4s;
  transition: opacity 0.4s;
}

.alert-validate::after {
  content: "\f06a";
  font-family: FontAwesome;
  display: block;
  position: absolute;
  color: #c80000;
  font-size: 15px;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -moz-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  -o-transform: translateY(-50%);
  transform: translateY(-50%);
  right: 13px;
}

.alert-validate:hover:before {
  visibility: visible;
  opacity: 1;
}

@media (max-width: 992px) {
  .alert-validate::before {
    visibility: visible;
    opacity: 1;
  }
}
</style>









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
            util_redirect("login-rt.php");
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
                $this->flashMsg->error("You have provided wrong Username or Password.","login-rt.php");

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
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN_LOGIN);
$PageObj->show();
?>

    <script src="../assets/global/plugins/tilt.jquery.min.js" type="text/javascript"></script>

  
	
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>