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
    private $department_data = NULL;
    private $selected_dep = NULL;

    /*     * *
     * This page's content
     * @return void
     */
public function renderHead() {
 ?>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/jquery-multi-select/css/multi-select.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/select2/select2.css"/>

<script type="text/javascript" src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="../assets/global/plugins/select2/select2.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    $('#deparment').multiSelect();
     $('input').tooltip();
     $("#btn_save").click(function(){
         $("#adminForm").submit();
     });
     
});
</script>
<?php
}
    public function renderBody() {
        ?>
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
                                <input id="firstname" name="firstname" type="text"  class="form-control" value="<?php echo $this->User->getFullname(); ?>" rel="tooltip"  title="Full name"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="companyname">Company name:</label>
                                <input id="companyname" name="companyname" type="text"  class="form-control" value="<?php echo $this->User->getCompany(); ?>" rel="tooltip"  title="Company name"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="surname">Username:</label>
                                <input id="username" name="username" type="text"  class="form-control" value="<?php echo $this->User->getUserName(); ?>" rel="tooltip"  title="Username"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input  title="The Pasword must be at least one upper case english letter,at least one lower case english letter,at least one special character and minimum 8 in length." id="password" name="password" rel="tooltip" type="password"  class="form-control" value="" autocomplete="off"/>
                                 <p class="help-block"><small>Leave it blank if you do not want to change your password</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Department">Department:</label>
                                <select  multiple="multiple" required="required" id="deparment" name="deparment[]" class="form-control" rel="tooltip" title="" data-original-title="Deparment" aria-describedby="Deparment">
                                    <option value="">Select Department</option>
                                    <?php foreach ($this->department_data as $value) { ?>
                                        <option <?php if(in_array($value->getId(),$this->selected_dep)){echo 'selected="selected"';}?>   value="<?php echo $value->getId(); ?>"><?php echo $value->getTitle(); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="text-align:center;">
                        <button type="submit" class="btn btn-primary" id="btn_save" name="btn_save">
                            Save changes
                        </button>
                    </div>
                    <input name="id" type="hidden" value="<?php echo $this->User->getId(); ?>">
                    <input name="passwordold" type="hidden" value="<?php echo $this->User->getPassword(); ?>">

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
        $this->password = (isset($_POST['password']) ? strip_tags($_POST['password']) : '');
        $this->passwordold = (isset($_POST['passwordold']) ? strip_tags($_POST['passwordold']) : '');
        
        
        $DepartmentFilter = new DepartmentFilter();
        $DepartmentFilter->addIsNotDeletedFilter();
        $DepartmentFilter->addIsActiveFilter();
        $this->department_data = $DepartmentFilter->getList();
        
        $UserDepartment = new UserDepartmentFilter();
        $UserDepartment->addByUserId($this->id);
        $UserDepartmentObj = $UserDepartment->getList();
        $department_array = array();
        foreach ($UserDepartmentObj as $value) {
          $department_array[] = $value->getDepartmentId();
        }
        if(count($department_array) > 0)
            $this->selected_dep = $department_array;

        /* ------------------------------------------------------------------------------ */
        // process form

        if (isset($_POST['btn_save'])) {
            $UserDepartment = new UserDepartment();
            $UserDepartment->deleteByUser($this->id);
            $my_arr = array();
            foreach ($_POST['deparment'] as $value) {    
                $UserDepartmentSave = new UserDepartment();
                $UserDepartmentSave->setUserId($this->id);
                $UserDepartmentSave->setDepartmentId($value);
                $UserDepartmentSave->save(); 
            }
            // get vars
            $AurSavObj = new CustomerAccount($this->id);
            $AurSavObj->setFullName($this->firstname);
            $AurSavObj->setCompany($this->companyname);
            $AurSavObj->setUsername($this->username);

            //If password is given then save
            if ($this->password != "")
                $AurSavObj->setPassword(password_hash($this->password, PASSWORD_DEFAULT));

            //$AurSavObj->setActive(1);
            //$AurSavObj->setDeletedq('N');
            $AurSavObj->save();
            // go to product list
            util_redirect("employees.php");
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
            util_redirect("employees.php");
        }

        if ($this->action == "confirmed_active") {
            // delete
            $AurDelObj = new CustomerAccount($this->id);
            $AurDelObj->setActive('1');
            $AurDelObj->setDeletedq('N');
            $AurDelObj->save();
            // go to admin user list
            util_redirect("employees.php");
        }



        /* ------------------------------------------------------------------------------ */

        $userData = new CustomerAccount($this->id);
        $this->User = $userData;

        //
        $this->setTitle("Employees - " . $this->User->getFirstName() . "");
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
