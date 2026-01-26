<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Country details page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
// set up local page class
class Page extends BasePage {

    private $error_msg = "";
    private $id = NULL;
    private $language_key = '';
	private $language = '';
	private $caption = '';
	
	

    public function init() {

        // check admin user is authenticated
		$user = SessionManager::getUser();
        if ($user->getUserType() != "admin")
		{
			util_redirect("index.php");
		}
        if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
            $languageKeyId = $_GET['id'];
            $languageKeyObj = new Languages($languageKeyId);
            $this->id = $languageKeyObj->getId();
			$this->language = $languageKeyObj->getLanguage();

        }

        /* ------------------------------------------------------------------------------ */
        // process form
        if (isset($_POST['btn_update'])) {
			
            echo "<pre>"; print_r($_POST); echo "</pre>";
            $id = $_POST['id'];
            $language = $_POST['language'];
			
			$languageKeyObj = new Languages($languageKeyId);
			$languageKeyObj->setLanguage($language);
			$languageKeyObj->save(); 
            
       
            util_redirect("language_list.php");
        }
        if (isset($_POST['btn_save'])) {
            
			$this->language = $_POST['language'];
			$dateCreated = date("Y-m-d G:i:s");			
			$user = Sessionmanager::getUser();
			$createdBy = $user->getId();
			
			$languageFilter = new LanguageFilter();
			$languageFilter->addLanguageFilter(DbAccess3::escape($this->language));
			
			
			//print_r($languageFilter);
			$list = $languageFilter->getList();	
			
//			print_r($list); die;  		
			
			//echo count($list);
			
			//die;
			if(count($list) == 0)
			{
				$languageKeyObj = new Languages();
				$languageKeyObj->setLanguage(DbAccess3::escape($this->language));
				$languageKeyObj->setDateCreated($dateCreated);
				$languageKeyObj->setCreatedBy($createdBy);
				$languageKeyObj->setIsActive("Y");
				$languageKeyObj->save();
				util_redirect("language_list.php");
			}
			else
			{
				$this->error_msg = "Language already exists.";
			}
			//print_r($languageKeyObj);
			
			//die;
			//$languageKeyObj->setIsActive('N');
            
            // go to courier list
            //util_redirect("manual_invoices.php");
        }
        /* ------------------------------------------------------------------------------ */
        // process delete
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == "confirmed_delete") {

            // save times and details
            /*$CouDelObj = new AgentData($this->id);
            $CouDelObj->delete();

            // go to courier list
            util_redirect("agent.php");*/
        }


        /* ------------------------------------------------------------------------------ */
        $this->setTitle("Admin - Agent Detail - " . $this->agent_name);
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <div class="portlet box blue" >
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-briefcase"></i>Language Keys</div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <form name="adminForm" id="adminForm" action="" method="POST"> 
                    <div class="note note-default">
                        <p><b>Add Language Key</b></p>
                    </div>
                    <div style="min-height:200px; max-height:1200px; height:auto !important;"  data-rail-color="blue" data-handle-color="blue">

                        <div class="note note-error">
                            <?php if ($this->error_msg != "") { ?>
                                <p class="alert alert-danger"> <?php echo $this->error_msg; ?></p>
                            </div>
                        <?php } ?>
                        <input type="hidden" name="id" id="id" value="<?php echo $this->id;?>" />
                        <div class="row">                            
                            <div class="col-md-6">
                                <div class="form-group" id="invoceData">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-magic"></i> </span>
                                        <input type="text" name="language" id="language" class="form-control" placeholder="Enter Language" value="<?php echo $this->language; ?>" required>
                                    </div>
                                </div>
                            </div>
                         </div>
                        <div class="col-md-12">
                            <div class="form-group"> <a  class="btn btn-primary" href="language_list.php">List</a>&nbsp;&nbsp;&nbsp;
                                <button type="submit" class="btn btn-primary" id="btn_save" name="<?php echo ($this->id != "" || $this->id > 0) ? 'btn_update' : 'btn_save'; ?>" value="Save Changes" > Save changes </button>
                            </div>
                        </div>
                    </div>
                </form>
                    </div>
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
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }

    private function languageDropdown() {
        /* if(isset($_SESSION['BOOKING']['ACCOUNT']) && $_SESSION['BOOKING']['ACCOUNT'] != '--- Please Select ---')
          {
          $userAccount =	$_SESSION['BOOKING']['ACCOUNT'];
          }
          else
          {
          $userAccount  = @$_POST["account"];
          $_SESSION['BOOKING']['ACCOUNT'] = $userAccount;
          } */
        /*$userAccount = $this->account_number;

        $filter = new UserAccountFilter();
        $filter->AddOrderByAccount();
        $filter->addActiveFlagFilter();
        $userList = $filter->getColumnList("id, user_account");*/
	$languageArray = array("en", "es", "de", "fr");
        echo '<select id="language" name="language"  class="form-control" required>';
        echo '<option value="">Language Dropdown</option>';
        foreach ($languageArray as $language) {
            $selected = ($this->language == $language) ? " selected" : "";
            echo '<option' . $selected . ' value="' . $language . '">' . $language . '</option>';
        }

        echo '</select>';
    }

    /*     * *
     * Controller logic goes here
     */

    

    public function renderHead() {
        ?>
        <link href="../includes/3rdparty/calendar/calendar.css" rel="stylesheet" type="text/css" />
        <?php
    }
     protected function addPagelavelCss() {
        ?>
        <?php
     }
     
     public function addPagelavelJs() {
        ?>
        <script type="text/javascript">
            function SetPageSize()
            {
                document.getElementById('adminForm').submit();
            }
            $(document).ready(function() {
                
//                $('#adminForm').validator().on('submit',function (e) {
//                        
//                        if (e.isDefaultPrevented()) {
//                            $('.btn-primary').attr("disabled");
//                            return true;
//                        }
//                        else
//                        {
//                           $('.btn-primary').removeAttr("disabled");
//                           return true;
//                        }
//                });

                // Set the date pickers
                $("#btnSubmit").click(function()
                {
                    $("#form_action").val("Submit");
                    $("#adminForm").submit();
                });
            });
        </script> 
        <script language="javascript" src="../includes/3rdparty/calendar/calendar.js"></script>
        <?php
     }
    /**
     * Returns boolean to indicate if the form is valid
     *
     */
    private function validate_form() {
        // Check that the country name is not blank
      

      
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
