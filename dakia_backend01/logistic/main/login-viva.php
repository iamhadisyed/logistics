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
    

           



     
        <div class="wrapper">
          
            <div class="form-inner">

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


             <form class="form-signin" method="post">

                    <div class="form-header">

<div style="background:#fff; padding:1em; border-radius: 5px !important;">
  <center>
                        <img class="profile-img img-responsive" src="images/viva-main-logo.gif" alt="">
                      </center>
</div>

                        <!--<h3>Sign in to the portal </h3>-->
                    </div>
                   
                    <div class="form-group">
                        <label for="">E-mail:</label>
                        <input type="text" class="form-control" placeholder="user_name" required autofocus name="username" id="username" value="<?php
                                if (isset($cookie["usr"])) {
                                    echo $cookie["usr"];
                                } else {
                                    echo $this->username;
                                }
                                ?>">
                    </div>
                    <div class="form-group" >
                        <label for="">Password:</label>
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


                      <button class="btn btn-lg btn-default btn-block" type="submit">  Login to VIVA Xpress</button>


                <label class="checkbox ml-5">
                    <input type="checkbox" value="remember" <?php echo (isset($_COOKIE['ONEWORLDAUTH']) ? ' checked="checked"' : ''); ?>>
                    Remember me
                </label>
               
              <!-- <a href="#" class="pull-right need-help">Need help? </a> --><span class="clearfix"></span>


                
                </form>


            </div>




              <div class="image-holder">
                <img src="../images/registration-form-8.jpg" alt="" class="img-responsive">
            </div>
            
        </div>



   </form>
        







<style>
    
    @font-face {
  font-family: "ChelseaMarket-Regular";
  src: url("../images/fonts/chelsea_market/ChelseaMarket-Regular.ttf"); }
@font-face {
  font-family: "Muli-Regular";
  src: url("../images//fonts/muli/Muli-Regular.ttf"); }
@font-face {
  font-family: "Muli-SemiBold";
  src: url("../images//fonts/muli/Muli-SemiBold.ttf"); }
* {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box; }

.btn-default:hover  { color: #fff }
.ml-5 { margin-left: 20px; }
.wrapper {
  min-height: 100vh;
  display: flex; }
  .wrapper .image-holder {
    width: 69.9%; }
  .wrapper .form-inner {
    width: 30.1%; }

.image-holder {
  background: url("../images/registration-form-8.jpg") no-repeat;
  background-size: cover; }
  .image-holder img {
    display: none; }

.form-inner {
  background: #1e73be;
  padding-top: 16.36vh;
  padding-left: 4vw;
  padding-right: 4vw; }

form {
  width: 100%; }

.form-header {
  text-align: center;
  margin-bottom: 39px; }

h3 {
  text-transform: uppercase;
  font-size: 40px;
  font-family: "ChelseaMarket-Regular";
  color: #fff }

label {
  margin-bottom: 11px;
  display: block; 
  color: #fff

}

.form-group {
  margin-bottom: 26px;
  position: relative; }

.form-control {
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: 5px !important;
  display: block;
  width: 100%;
  height: 45px;
  background: none;
  padding: 0 19px;
  color: #fff;
  font-size: 17px; 

}
  .form-control.error {
    border-color: #fd677a !important;
    }

.form-error {
  margin-top: 10px;
  display: inline-block; }

button {
  border: none;
  width: 100%;
  height: 46px;
  border-radius: 22.5px !important;
  margin: auto;
  margin-top: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  background: #fff;
  color: #1e73be;
  text-transform: uppercase;
  font-size: 17px;
  overflow: hidden;
  -webkit-transform: perspective(1px) translateZ(0);
  transform: perspective(1px) translateZ(0);
  position: relative;
  -webkit-transition-property: color;
  transition-property: color;
  -webkit-transition-duration: 0.3s;
  transition-duration: 0.3s; }
  button:before {
    content: "";
    position: absolute;
    z-index: -1;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: #000080;
    -webkit-transform: scaleX(0);
    transform: scaleX(0);
    -webkit-transform-origin: 50%;
    transform-origin: 50%;
    -webkit-transition-property: transform;
    transition-property: transform;
    -webkit-transition-duration: 0.3s;
    transition-duration: 0.3s;
    -webkit-transition-timing-function: ease-out;
    transition-timing-function: ease-out; }
  button:hover {
    color: white; }
    button:hover:before {
      -webkit-transform: scaleX(1);
      transform: scaleX(1); }

.socials {
  text-align: center;
  margin-top: 59px; }

.socials-icon {
  display: inline-flex;
  width: 41px;
  height: 41px;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 0.5);
  font-size: 19px;
  color: #fff;
  transition: all 0.5s ease;
  margin-right: 19px; }
  .socials-icon:hover {
    background: #fff;
    border: 1px solid transparent;
    color: #1e73be; }
  .socials-icon:last-child {
    margin-right: 0; }

p {
  font-family: "Muli-SemiBold";
  color: #ffff66;
  margin-bottom: 22px; }

@media (max-width: 1500px) {
  .form-inner {
    display: flex;
    align-items: center;
    padding-top: 0;
    padding-left: 4vw;
    padding-right: 4vw; } }
@media (max-width: 1199px) {
  .wrapper {
    flex-direction: column; }
    .wrapper .image-holder {
      width: 100%;
      height: 45vh; }
    .wrapper .form-inner {
      width: 100%;
      height: 55vh; }

  .form-inner {
    padding-left: 20vw;
    padding-right: 20vw; }

  button {
    width: 50%; } }
@media (max-width: 991px) {
  .wrapper .image-holder {
    height: 37vh; }
  .wrapper .form-inner {
    height: 63vh; }

  .socials {
    margin-top: 40px; }

  .form-header {
    margin-bottom: 30px; } }
@media (max-width: 767px) {
  .wrapper .form-inner {
    height: auto; }
  .wrapper .image-holder {
    height: auto; }

  .image-holder img {
    display: block; }

  .form-inner {
    padding: 30px 20px; }

  button {
    width: 100%; } }
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
            util_redirect("login-ukmail.php");
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
                $this->flashMsg->error("You have provided wrong Username or Password.","login-ukmail.php");

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
