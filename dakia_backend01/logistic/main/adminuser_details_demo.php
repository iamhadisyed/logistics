<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Admin users details page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    public $id = NULL;
    public $action = NULL;
    public $_error_array = array();
    public $firstname = NULL;
    public $companyname = NULL;
    public $username = NULL;
    public $password = NULL;

    /*     * *
     * This page's content
     * @return void
     */

    public function renderHead() {
        ?>

        <?php
    }

    public function renderBody() {
        ?>
        <div class="note note-success">
            <p>	</p>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-users"></i><?php echo $this->User->getUserAccount(); ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                    <a href="" class="fullscreen">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                </div>

            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname">Full name:</label>
                                <input id="firstname" name="firstname" type="text"  class="form-control" value="<?php echo $this->User->getFullname(); ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="companyname">Company name:</label>
                                <input id="companyname" name="companyname" type="text"  class="form-control" value="<?php echo $this->User->getCompany(); ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="surname">Username:</label>
                                <input id="username" name="username" type="text"  class="form-control" value="<?php echo $this->User->getUserName(); ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input autocomplete="off" id="password" name="password" type="password"  class="form-control" value="" />
                                <p class="help-block"><small>Leave it blabk if you do not want to change your password</small></p>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="text-align:center;">
                        <button type="submit" class="btn btn-primary" id="btn_save" name="btn_save">
                            Save changes
                        </button>
                    </div>
                    <input name="id" type="hidden" value="<?php echo $this->User->getId(); ?>">
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
        $menu = new Adminmenu(Adminmenu::ADMINISTRATORS);
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        // check admin user is authenticated
       	$user = SessionManager::getUser();
		if ($user->getUserType() != "finance" && $user->getUserType() != "admin")
		{
			util_redirect("index.php");
		}

        /* ------------------------------------------------------------------------------ */
        // get vars
        $this->action = (isset($_REQUEST['action']) ? strip_tags($_REQUEST['action']) : '');
        $this->id = (isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : '');
        $this->firstname = (isset($_POST['firstname']) ? strip_tags($_POST['firstname']) : '');
        $this->companyname = (isset($_POST['companyname']) ? strip_tags($_POST['companyname']) : '');
        $this->username = (isset($_POST['username']) ? strip_tags($_POST['username']) : '');
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $password_hash = "";
            $password_hash = strip_tags($_POST['password']);
            $password_hash = password_hash($password_hash, PASSWORD_DEFAULT);
            $this->password = $password_hash;
        } else {
            $this->password = "";
        }
//        $this->passwordold = (isset($_POST['passwordold']) ? strip_tags($_POST['passwordold']) : '');




        /* ------------------------------------------------------------------------------ */
        // process form
        if (isset($_POST['btn_save'])) {


            // get vars
            $AurSavObj = new CustomerAccount($this->id);
            $AurSavObj->setFullName($this->firstname);
            $AurSavObj->setCompany($this->companyname);
            $AurSavObj->setUsername($this->username);

            //If password is given then save
            if ($this->password != "")
                $AurSavObj->setPassword($this->password);

            //$AurSavObj->setActive(1);
            //$AurSavObj->setDeletedq('N');
            $AurSavObj->save();
            // go to product list
            util_redirect("adminusers.php");
        }

        /* ------------------------------------------------------------------------------ */
        // process delete
        if ($this->action == "confirmed_delete") {
            // delete
            $AurDelObj = new CustomerAccount($this->id);
            $AurDelObj->setActive('0');
            $AurDelObj->setDeletedq('Y');
            $AurDelObj->save();
            // go to admin user list
            util_redirect("adminusers.php");
        }

        if ($this->action == "confirmed_active") {
            // delete
            $AurDelObj = new CustomerAccount($this->id);
            $AurDelObj->setActive('1');
            $AurDelObj->setDeletedq('N');
            $AurDelObj->save();
            // go to admin user list
            util_redirect("adminusers.php");
        }



        /* ------------------------------------------------------------------------------ */

        $userData = new CustomerAccount($this->id);
        $this->User = $userData;

        //
        $this->setTitle("Admin - Administrators - " . $this->User->getFirstName() . "");
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
