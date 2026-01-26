<?php
////////////////////////////////////////////////////
//
// Controller for Admin - login page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'useraccount.class',
    'forgetpasswordrequest.class',
    'forgetpasswordrequestfilter.class'
    
    ]);

// set up local page class
class Page extends BasePage {

    public $username = NULL;
    public $password = NULL;
    public $user_name = NULL;
    public $msg = NULL;
    public $error_msg = NULL;
    public $show_forget = NULL;

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        <!--<div class="menu-toggler sidebar-toggler">
        </div>-->
        <!-- END SIDEBAR TOGGLER BUTTON -->
        <!-- BEGIN LOGO -->
        <!--<div class="logo">

        <!-- <?php
        $imageLogo = '';
        if (isset($_GET['data'])) {
            $accountNumber = ($_GET['data']);
            $userfilterClass = new UserAccountFilter();
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



        </div>-->
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->


        <div class="user-login-5">
            <div class="row bs-reset">
                <div class="col-md-6 login-container bs-reset">
                    <a href="index.php">
                        <img src="../images/smart-track-logo.png" class="login-logo login-6" alt="One World Express" >
                    </a>
                    <div class="login-content">
                        <?php
                        if (empty($this->error_msg) || $this->error_msg != "Your token key has been expired.") { ?>
                            <div class="row">
                                <?php if (!empty($this->error_msg)) { ?>
                                    <div class="alert  alert-danger" ><?php echo $this->error_msg; ?></div>
                                <?php } ?>
                            </div>

                            <form class="login-form" method="post">
                                <h3 class="">Reset Password</h3>
                                <p >Please enter your new password to reset.</p>
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                                    <div class="input-group">
                                            <span class="input-group-addon">
                                               <i class="fa fa-lock"></i>
                                            </span>
                                            <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password_forget" id="password_forget" value="" required="required" oninvalid="this.setCustomValidity('Please Enter Password')"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">Re-Password</label>
                                    <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-lock"></i>
                                            </span>	<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Re-Password" name="repassword_forget" id="repassword_forget" value="" required="required" oninvalid="this.setCustomValidity('Please Enter Re Password')"/>

                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success uppercase " id="btn_save" name="btn_save">Save</button>
                                    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>" />
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        <?php } else {
                            ?>
                            <?php if (!empty($this->error_msg)) { ?>
                                <div class="alert  alert-danger" ><?php echo $this->error_msg; ?></div>
                            <?php } ?>
                            <div class="form-actions text-center">
                                <a href="login.php" class="btn btn-success uppercase" >Login</a>
                                <div class="clearfix"></div>
                            </div>
                        <?php }
                        ?>
                    </div>
                    <div class="login-footer">
                        <div class="row bs-reset">
                            <div class="col-xs-5 bs-reset">
                            </div>
                            <div class="col-xs-7 bs-reset">
                                <div class="login-copyright text-right">
                                    <p>Copyright &copy; One World Express</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6 bs-reset" style="background:url(../assets/pages/img/login/bg1.jpg) no-repeat; background-size: cover;    height: 100vh; "> -->
                <div class="col-md-6 bs-reset">
                    <div class="login-bg"> </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {

        $this->setTitle("Verify Email Token");
        t_on(); // turn on trace for this page
        if (isset($_GET['token']) && !empty($_GET['token'])) {
            $Token = $_GET['token'];
            $DateExpire = date('Y-m-d H:i:s');
            $ForgetPasswordRequestFilter = new ForgetPasswordRequestFilter();
            $ForgetPasswordRequestFilter->addTokenFilter($Token);
            $ForgetPasswordRequestFilter->addIsDateExpireFilter();
            $ForgetPasswordRequestFilter->addIsNotExpireFilter();
            $result = $ForgetPasswordRequestFilter->getList();

            if (count($result) > 0) {
                $this->show_forget = "show_forget";
            } else {
                $this->error_msg = "Your token key has been expired.";
            }
        }
        if (isset($_POST['password_forget']) && isset($_POST['repassword_forget']) && !empty($_POST['password_forget']) && !empty($_POST['repassword_forget'])) {
            if ($_POST['password_forget'] != $_POST['repassword_forget']) {
                $this->error_msg = "Your password and re-password does not match.";
            } else {
                $Token = $_POST['token'];
                $ForgetPasswordRequestFilter = new ForgetPasswordRequestFilter();
                $ForgetPasswordRequestFilter->addTokenFilter($Token);
                $ForgetPasswordRequestFilter->addIsDateExpireFilter();
                $ForgetPasswordRequestFilter->addIsNotExpireFilter();
                $user_result = $ForgetPasswordRequestFilter->getList();
                if (trim($this->form_vars['password_forget']) != '' && !preg_match("/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/", $this->form_vars['password_forget']) ) {
                    $this->error_msg = formatMessages(ERROR_PASSWORD_VERIFY) . "<br>";
                } else  if ($user_result > 0) {
                    $user_result = $user_result[0];
                    $UserName = $user_result->getUserName();
                    $TokenId = $user_result->getId();
                    $password_hash = password_hash($_POST['password_forget'], PASSWORD_DEFAULT);
                    $userFilter = new UserFilter();
                    $userFilter->addUserNameFilter($UserName);
                    $UserData = $userFilter->getList(false);

                    $UserData = $UserData[0];
                    $UserData->setUserPass($password_hash);
                    $UserData->save();
                    $UserId =  $UserData->getId();
                  //  $userAccount = new User($UserId);
                    //$userAccount->setUserPass($password_hash);
                   // $userAccount->save();

                    $ForgetPasswordRequest = new ForgetPasswordRequest($TokenId);
                    $ForgetPasswordRequest->setIsExpire('1');
                    $ForgetPasswordRequest->save();
                    $this->flashMsg->success("You have successfully reset your password. Please login.");
                    util_redirect("login.php?password=reset");
                } else {
                    $this->error_msg = "Your token key has been expired.";

                }
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
