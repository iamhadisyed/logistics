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

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>

<div class="col-md-6"> 
  
  <!-- BEGIN LOGIN FORM -->
  
  <form class="login-form" method="post">
    <h3 class="">Sign in to the portal </h3>
    <p>Please enter your username and password to log in.</p>
    <?php if (!empty($this->msg)) { ?>
    <div class="alert  alert-success" ><?php echo $this->msg; ?></div>
    <?php } ?>
    <?php if (!empty($this->error_msg)) { ?>
    <div class="alert  alert-danger" ><?php echo $this->error_msg; ?></div>
    <?php } ?>
    <div class="alert alert-danger display-hide">
      <button class="close" data-close="alert"></button>
      <span> Enter any username and password. </span> </div>
    <?php
                $cookie = array();
                if (isset($_COOKIE["ONEWORLDAUTH"]))
                    parse_str($_COOKIE["ONEWORLDAUTH"], $cookie);
                ?>
    <div class="form-group"> 
      <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
      <label class="control-label visible-ie8 visible-ie9">Username</label>
      <div class="input-icon">
        <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" id="username" value="<?php if (isset($cookie["usr"])) {
                                                                echo $cookie["usr"];
                                                            }else{echo $this->username;} ?>" />
        <i class="fa fa-user"></i></div>
    </div>
    <div class="form-group">
      <label class="control-label visible-ie8 visible-ie9">Password</label>
      <div class="input-icon">
        <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"  id="password" value="<?php if (isset($cookie["hash"])) {
                                                                echo base64_decode($cookie["hash"]);
                                                            }?>"/>
        <i class="fa fa-lock"></i> </div>
    </div>
    <div class="form-actions">
      <label class="rememberme check">
        <input type="checkbox" name="remember" value="1" <?php echo (isset($_COOKIE['ONEWORLDAUTH']) ? ' checked="checked"' : ''); ?>/>
        Remember </label>
      <button type="submit" class="btn btn-success uppercase pull-right" id="btn_login" name="btn_login">Login</button>
      <div class="clearfix"></div>
      <label class="rememberme check">
        <input type="checkbox" name="archive" id="archive" value="TRUE" />
        Archive Server </label>
      <a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>
      <button type="submit" class="btn btn-success uppercase" id="" name="btn_login">Register</button>
    </div>
  </form>
  <!-- END LOGIN FORM --> 
  <!-- BEGIN FORGOT PASSWORD FORM -->
  <form class="forget-form" action="" method="post">
    <h3>Forget Password ?</h3>
    <p> Enter your User Name below to reset your password. </p>
    <div class="form-group">
      <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="User Name" name="user_name"/>
    </div>
    <div class="form-actions">
      <button type="button" id="back-btn" class="btn btn-default">Back</button>
      <button type="submit" class="btn btn-success uppercase pull-right">Submit</button>
    </div>
  </form>
  <!-- END FORGOT PASSWORD FORM --> 
  
</div>
<div class="col-md-6"> <br />
  <br />
  <br />
  <br />
  <img src="login-laptop.jpg" class="img-responsive" /> </div>
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
        $this->setTitle("Please Login");

        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        Translation::PopulateKeywordsArray($language);

        t_on(); // turn on trace for this page
        // Log user out?
        if (util_request("logout") == "true") {
            $data = $_SESSION["user_account"];
            User::logUserOut();
            //util_redirect("../main/index.php");
            //	echo "../main/index.php?data=".$data;
            //	die;
            util_redirect("../main/index.php?data=" . $data);
            die;
        }


        /* ------------------------------------------------------------------------------ */
        // get vars
        $this->username = (isset($_POST['username']) ? strip_tags($_POST['username']) : '');
        $this->password = (isset($_POST['password']) ? strip_tags($_POST['password']) : '');
        $this->user_name = (isset($_POST['user_name']) ? strip_tags($_POST['user_name']) : '');

        /* ------------------------------------------------------------------------------ */
//		print_r($_POST);
        // process form
        if (isset($_POST['btn_login']) || (isset($_POST['username']) && isset($_POST['password']))) {


            // login
            if (User::getUser($this->username, $this->password)) {

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



                util_redirect("index.php");
            } else {
                util_redirect("login.php");
            }
        }
        if (isset($_POST['user_name']) && !empty($_POST['user_name'])) {
            $UserData = User::getUserByUsername($this->user_name);
            if (!empty($UserData) && is_array($UserData)) {
                $msg = 'Dear ' . $UserData['firstname'] . ", \n \n Here is your account Details: \n \n Email: " . $UserData['email'] . "\n User Name: " . $this->user_name . "\n Password: " . $UserData['user_pass'] . "\n \n Best Regards, \n oneworldexpress.co.uk";
                mail($UserData['email'], 'Forget Password', $msg);
                $this->msg = "Please Check Your E-mail account associated wtih this user name and get account details.";
            } else {
                $this->error_msg = "There is no account associated with this username.";
            }
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN_LOGIN);
$PageObj->show();
?>
