<?php
////////////////////////////////////////////////////
//
// Controller for Admin - login page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");
// set up local page class

include_classes([
	'country.class',
	'countryfilter.class',
	'useraccount.class',
	'forgetpasswordrequest.class'
]);

class Page extends BasePage
{

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

	public function renderBody()
	{
		?>
		<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
		<!--<div class="menu-toggler sidebar-toggler">
		</div>-->
		<!-- END SIDEBAR TOGGLER BUTTON -->
		<!-- BEGIN LOGO -->
		<!--<div class="logo">
         <?php
		$imageLogo = '';
		if (isset($_GET['register'])) {
			$showRegister = true;
		}
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
		//echo $imageLogo;
		?>
        </div>
         END LOGO -->
		<!-- BEGIN LOGIN -->
		<?php
		if (strtoupper(trim($this->ipDetailCountry)) == 'DE' || $accountNumber == "DEMOTEST") {
			?>
			<!--   <div class="page-header navbar" style="background:#fff;">
			</div> -->
		<?php }
		?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php $this->flashMsg->display(); ?>
                </div>
                <div class="col-lg-12">
                    <div class="card overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4 auth-one-bg h-100">
                                    <div class="bg-overlay"></div>
                                    <div class="position-relative h-100 d-flex flex-column">
                                        <div class="mb-4">
                                            <a href="index.html" class="d-block">
                                                <img src="<?= SETTING_MAIN_ASSETS;?>images/logo-light.png" alt="" height="18">
                                            </a>
                                        </div>
                                        <div class="mt-auto">
                                            <div class="mb-3">
                                                <i class="ri-double-quotes-l display-4 text-success"></i>
                                            </div>

                                            <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                <div class="carousel-indicators">
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                </div>
                                                <div class="carousel-inner text-center text-white-50 pb-5">
                                                    <div class="carousel-item active">
                                                        <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                    </div>
                                                    <div class="carousel-item">
                                                        <p class="fs-15 fst-italic">" The theme is really great with an amazing customer support."</p>
                                                    </div>
                                                    <div class="carousel-item">
                                                        <p class="fs-15 fst-italic">" Great! Clean code, clean design, easy for customization. Thanks very much! "</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end carousel -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->

                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4">
                                    <div>
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p class="text-muted">Sign in to continue to Velzon.</p>
                                    </div>

                                    <div class="mt-4">
                                        <form method="post" action="<?= SETTING_MAIN_URL;?>login">

                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
                                            </div>

                                            <div class="mb-3">
                                                <div class="float-end">
                                                    <a href="auth-pass-reset-cover.html" class="text-muted">Forgot password?</a>
                                                </div>
                                                <label class="form-label" for="password-input">Password</label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input name="password" type="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input">
                                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                                <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-info w-100" name="btn_login" type="submit">Sign In</button>
                                            </div>

                                            <div class="mt-4 text-center">
                                                <div class="signin-other-title">
                                                    <h5 class="fs-13 mb-4 title">Sign In with</h5>
                                                </div>

                                                <div>
                                                    <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                                                    <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                                                    <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                                                    <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>

                                    <div class="mt-5 text-center">
                                        <p class="mb-0">Don't have an account ? <a href="auth-signup-cover.html" class="fw-semibold text-primary text-decoration-underline"> Signup</a> </p>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->

            </div>
            <!-- end row -->
        </div>
		<?php
	}

	/**
	 * Override to show the menu
	 *
	 */
	public function renderMenu()
	{

	}

//Get an array with geoip-infodata
	public function geoCheckIP($ip)
	{
		//check, if the provided ip is valid
		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			throw new InvalidArgumentException("IP is not valid");
		}
		//contact ip-server
		$response = @file_get_contents('http://www.netip.de/search?query=' . $ip);
		if (empty($response)) {
			throw new InvalidArgumentException("Error contacting Geo-IP-Server");
		}
		//Array containing all regex-patterns necessary to extract ip-geoinfo from page
		$patterns = array();
		$patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
		$patterns["country"] = '#Country: (.*?)&nbsp;#i';
		$patterns["state"] = '#State/Region: (.*?)<br#i';
		$patterns["town"] = '#City: (.*?)<br#i';
		//Array where results will be stored
		$ipInfo = array();
		//check response from ipserver for above patterns
		foreach ($patterns as $key => $pattern) {
			//store the result in array
			$ipInfo[$key] = preg_match($pattern, $response, $value) && !empty($value[1]) ? $value[1] : 'not found';
		}
		return $ipInfo;
	}

	/*     * *
	 * Controller logic goes here
	 */

	public function addPagelavelJs()
	{
		?>
        <script src="<?= SETTING_MAIN_ASSETS;?>js/pages/password-addon.init.js"></script>
		<?php
	}

	public function addPagelavelCss()
	{
		?>

		<?php
	}

	public function init()
	{
		$ip = '213.246.110.102';
		$ipCountry = 'DE'; //$IpDeatils['country'];
		if (trim($ipCountry) != '') {
			$countryIpDara = explode('-', $ipCountry);
			$this->ipDetailCountry = trim($countryIpDara[0]);
		}

		$userSection = SessionManager::getUser();
		if (isset($_POST['action']) && $_POST['action'] === "update_user") {
			$rtnMsg = "";
			$userId = trim($_POST['user_id']);
			$user = new User($userId);
			if (isset($_POST['set_ignore']) && $_POST['set_ignore'] == "ignore") {
				$user->setIsTcAgreed("i");
				$_SESSION["is_tc_agreed"] = "i";   // Set user t&c check
				$rtnMsg = "ignore";
			} else {
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
		//t_on(); // turn on trace for this page
		// Log user out?
		if (util_request("logout") == "true") {
			$data = $_SESSION["user_account"];
			User::logout();
		}

		if (SessionManager::getUser()->getId() > 0 && !empty ($_SESSION["is_tc_agreed"]) && $_SESSION["is_tc_agreed"] == 'y') {
			util_redirect("index.php");
		}
		/* ------------------------------------------------------------------------------ */
		// get vars
		$this->username = DbAccess3::escape(((isset($_POST['username']) ? strip_tags($_POST['username']) : '')));
		$this->password = DbAccess3::escape((isset($_POST['password']) ? strip_tags($_POST['password']) : ''));
		$this->user_name = DbAccess3::escape((isset($_POST['user_name']) ? strip_tags($_POST['user_name']) : ''));
		/* ------------------------------------------------------------------------------ */
		// process form
		if (isset($_POST['btn_login']) || (isset($_POST['username']) && isset($_POST['password']))) {
			$userLoginCheck = User::getUser($this->username, $this->password);
			if ($userLoginCheck && !empty ($_SESSION["is_tc_agreed"]) && $_SESSION["is_tc_agreed"] == 'n') {
				$userId = $_SESSION["user_id"];
				if ($userId > !0)
					$userId = 0;
				?>
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
				<script type="text/javascript">
					$(document).ready(function () {
						var userId = "<?php echo $userId; ?>";
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
							function (isConfirm) {
								if (isConfirm) {
									$.ajax({
										url: 'login.php',
										type: 'POST',
										data: {action: "update_user", user_id: userId},
										success: function (data) {
											if (data == 'yes') {
												location.href = "index.php"
											}
										},
										error: function () {

										}
									});
								}
							});
						<?php   $now = time();
						$date = '2018/05/25';
						if (strtotime($date) >= $now) { ?>
						$(".btn_red").before('<button class="btn btn-lg btn_blue" tabindex="2" style="display: inline-block;">Ignore</button>&nbsp;');
						<?php   } ?>
						$(".btn_blue").click(function () {
							$.ajax({
								url: 'login.php',
								type: 'POST',
								data: {action: "update_user", user_id: userId, set_ignore: 'ignore'},
								success: function (data) {
									if (data == 'ignore') {
										// location.href = "index.php"
									}
								},
								error: function () {

								}
							});
							swal.close();
						});
					});
				</script>
				<?php
			} else if ($userLoginCheck) {
				$_SESSION["archive_server"] = $_POST["archive"];
				if (isset($_POST['remember']) && $_POST['remember'] == 1) {
					$cookie_time = (3600 * 24 * 90);
					setcookie('ONEWORLDAUTH', 'usr=' . $this->username . '&hash=' . base64_encode($this->password), time() + $cookie_time);
				} else if (isset($_COOKIE['ONEWORLDAUTH'])) {
					$past = time() - 100;
					setcookie('ONEWORLDAUTH', 'gone', $past);
				}
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
				$this->flashMsg->error("You have provided wrong Username or Password.", "login.php");
			}
		} else if (isset($_POST['email']) && !empty($_POST['email'])) {
			$email = DbAccess3::escape($_POST['email']);
			$userFilter = new UserFilter();
			$userFilter->addFieldFilter("email", $email);
			$userFilter->addFieldFilter("active_flag", 1);
			$userData = $userFilter->getColumnList('id, user_type, user_name, first_name, last_name,email');
			if (count($userData) > 0) {
				$userData = $userData[0];
				$UserName = $userData->getUserName();
				$FullName = $userData->getFirstName() . " " . $userData->getLastName();
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
				$msg = 'Dear ' . $FullName . ", <br><br><br>The following link leads you to One World Smart Track where you can set a new password for access to your account.<br><br><a href='" . SETTING_URL . "verify_email_token.php?token=" . $Token . "'>" . SETTING_URL . "verify_email_token.php?token=" . $Token . "</a><br>This unique link will only remain valid for a period of 24 hours. However, you can request a new password link at any time.<br><br>If you cannot open the link by clicking on it, copy and paste this URL into your browser's address bar.<br>If you've received this message in error or did not request to reset your password, please do not click on the link. You do not need to take any further action and can safely discard this message.<br><br>  <strong>Best Regards</strong>,<br><br>   <strong>Customer Service Team</strong><br> <strong>One World Express Ltd</strong>";
				$headers = "From: SmartTrack<smart@smarttrack.co>\r\n";
				// $headers .= "Reply-To: One World Express<itsupport@oneworldexpress.com> \r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				mail($Email, 'Forget Password', $msg, $headers);
				$this->msg = "Please check your e-mail associated with this " . trim(strip_tags(DbAccess3::escape($_POST['email'])), "()'><");//$FullName;
				$this->flashMsg->success($this->msg, "login.php");
			} else {
				$this->error_msg = "There is no account associated with this " . trim(strip_tags(DbAccess3::escape($_POST['email'])), "()'><");// $FullName;
				$this->flashMsg->error($this->error_msg, "login.php");
			}
		}
	}

	public function getClientIp()
	{
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
