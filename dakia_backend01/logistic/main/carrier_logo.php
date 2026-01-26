<?php
// get settings
require_once("../includes/settings/config.inc.php");
/***
 * Page for editing a user
 */
class Page extends BasePage
{
	/***
	* Controller logic
	*/
	protected function init()
	{
            $user = SessionManager::getUser();
            if ($user->getUserType() != "admin")
            {
                    util_redirect("index.php");
            }
		
            Sessionmanager::checkUserAccess(USER::PRIVILEGE_WAREHOUSE_LIST);
            t_on(); // turn on trace for this page

            // is this form being posted back?
            if (isset($this->form_vars["form_action"]))
            {
                // take appropriate action
                switch ($this->form_vars["form_action"])
                {
                    // SAVE
                    // - save new address detailsvalidate new address details - if OK, save and return to booking list
                    case "save":
                        // save new address details3
                        $error_array = array();
                        if(isset($_FILES["logo"]) && trim($_FILES["logo"]["name"]) != '' && isset($_POST["carrier"]) && trim($_POST["carrier"]) != '')
                        {
                                $allowedExts = array("gif", "jpeg", "jpg", "png");
                                $temp = explode(".", $_FILES["logo"]["name"]);
                                $extension = end($temp);

                                if ((($_FILES["logo"]["type"] == "image/gif") || ($_FILES["logo"]["type"] == "image/jpeg") || ($_FILES["logo"]["type"] == "image/jpg") || ($_FILES["logo"]["type"] == "image/pjpeg") || ($_FILES["logo"]["type"] == "image/x-png") || ($_FILES["logo"]["type"] == "image/png"))  && in_array($extension, $allowedExts)) 
                                {
                                  if ($_FILES["logo"]["error"] > 0) 
                                  {
                                                $error_array[]	=	"Return Code: " . $_FILES["logo"]["error"] . "<br>";
                                  } 
                                  else 
                                  {
                                          $uploadName	=	time().$_FILES["logo"]["name"];
                                          move_uploaded_file( $_FILES["logo"]["tmp_name"],  "../images/carrierlogo/" . $uploadName);
                                  }
                                } 
                                else 
                                {
                                        $error_array[]	=	"Invalid file";
                                }

                                $carrierFilter	=	new CarrierFilter();
                                $carrierFilter->addCarrierFilter($this->form_vars['carrier']);
                                $carrierResult	=	$carrierFilter->getList();

                                if($carrierFilter->getCount()>0)
                                {
                                    $error_array[]	=	"The carrier name (".$this->form_vars['carrier']."), you are using is already added.";
                                    $headerMessage	=	'Error';
                                        //$carrierLogo = new Carrier(intval($carrierResult[0]->getId()));
                                        //$carrierLogo->setCarrier ($this->form_vars["carrier"]);
                                        //$carrierLogo->setLogo ($uploadName);
                                        //$carrierLogo->save();
                                }
                                else
                                {
                                    $carrierLogo = new Carrier();
                                    $carrierLogo->setCarrier ($this->form_vars["carrier"]);
                                    $carrierLogo->setLogo ($uploadName);
                                    $carrierLogo->save();
                                    $error_array[]	=	"Carrier logo has been uploaded.";
                                    $headerMessage	=	'Success';
                                }
                                
                        }
                        else{
                                $error_array[]	=	"Please select carrier and upload logo.";
                                $headerMessage	=	'Error';
                        }
                        // add list of errors to error list
                        $error_list = ErrorList::getItem();
                        $error_list->setPrelistMessage($headerMessage);
                        $error_list->addErrorList($error_array);
                        break;

                    case "delete":
                            $services = new Services(intval($this->form_vars["id"]));
                            $services->delete();
                            util_redirect ("../main/services_list.php");
                            break;

                    // CANCEL
                    // - return to booking list
                    case "cancel":
                    default:
                            util_redirect ("../main/services_list.php");
                            break;
                }
            }

		// not post back - first time this form is shown
		else
		{
			// get consignment id passed
			$id = util_get_num("id");
			$this->form_vars["id"] = $id;

			// get address values
			$services = new Services($id);
			//
			$this->form_vars["name"] = $services->getName();
			$this->form_vars["code"] = $services->getCode();
			$this->form_vars["carrier"] = $services->getCarrier();
			$this->form_vars["service_type"] = $services->getType();
			$this->form_vars["service_country"] = $services->getServiceCountry();

		}
		// common initialisation for ths page
		$this->setTitle("Service");
	}
/***
	 * Insert content in to HTML Head section
	 */
	protected function renderHead()
	{
	?>
	<script>
    $(document).ready(function(){
		$("#btnSave").click(function(){
		  $("#form_action").val("save");
		  $("#adminForm").submit();
		});	
		
		$("#btnCancel").click(function(){
			$("#form_action").val("cancel");
			$("#adminForm").submit();
		});
	
	});
    
    </script>
        <style>
        .carrier .panel-body {
            padding: 50px 0;
        }	

                .panel {
                        background-color: #fff;
                        /*border: 1px solid transparent;**/
                        border-radius: 4px !important;
                        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) !important;
                        margin-bottom: 20px !important;
                }
            </style> 
        <?php
	}
	

	/***
	* Content View
	*/
	protected function renderBody()
	{
	?>
            <ul class="breadcrumb">
                <li><a href="../main/index.php"><?=Translation::GetCaption("HOME");?></a></li>
                <li><a href="../main/services_list.php"><?=Translation::GetCaption("CARRIERS");?></a></li>
		<li><a href="#"><?=Translation::GetCaption("LIST");?></a></li>
            </ul>
            <?php
            // transfer form variables into local values (form variables come from parent)
            foreach ($this->form_vars as $key=>$val) {$$key = $val; }
            if(errorList::getItem()->getErrorCount() > 0)
            {
                ?>
                <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
                <?php
            }
            ?>
            
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                <?=Translation::GetCaption("ADD_UPDATE_CARRIER");?>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                
                <div class="row">
                    <div class="col-sm-12">

                    <div class="col-md-3">
                        <div class="form-group"> 
                            <div class="input-group"> 
                            <input type="text" name="carrier" id="carrier" required="required" title="<?= Translation::GetCaption("PLEASE_ENTER_CARRIER_NAME"); ?>" value="" class="form-control" placeholder='<?=Translation::GetCaption("PLEASE_ENTER_CARRIER_NAME"); ?>' />
                            <span class="input-group-addon red-18">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group"> 
                            <div class="input-group"> 
                            <input type="file" name="logo" id="logo" accept="image/*"  required="required" title="<?= Translation::GetCaption("PLEASE_SELECT_CARRIER_LOGO"); ?>" class="form-control" placeholder='<?=Translation::GetCaption("PLEASE_SELECT_CARRIER_LOGO"); ?>'/>
                            <span class="input-group-addon red-18">*</span>
                        </div>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group"> 
                            <div class="input-group"> 
                            <input type="text" name="cut_off_time" id="cut_off_time" required="required" title="<?= Translation::GetCaption("PLEASE_ENTER_CUT_OFF_TIME"); ?>" value="" class="form-control" placeholder='<?=Translation::GetCaption("PLEASE_ENTER_CUT_OFF_TIME"); ?>' />
                            <span class="input-group-addon red-18">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">       
                    </div>		   
                    </div>		   
                </div>  
                <div class="row" style="text-align:center;">      
                    <div class="col-sm-12">
                        <a href="../main/services_list.php" id="btnCancel" class="btn btn-danger  btn_cancel"><span></span>Cancel</a>
                        <a id="btnSave"   href="#" class="btn btn-primary btn_save"><span></span>Save</a>
                        </div>
                </div>
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                <?= Translation::GetCaption("CARRIER_DETAILS"); ?>
                </div>
                <div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
            </div>
                
            <div class="portlet-body">
                <?php 
                    echo '<table class="table table-striped table-bordered table-advance table-hover">';
                    $carrierFilter	=	new CarrierFilter();
                    $carrierResult	=	$carrierFilter->AddOrderBy('carrier', true);
                    $carrierResult	=	$carrierFilter->getList();
                    echo '<tbody><tr style="border:1px solid #ccc; margin:5px; padding:5px;">';
                    $carrierFilterCount	=	0;
                    foreach($carrierResult as $rowImage)
                    {
                        ?>
                
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                    <div class="panel panel-default">
                        <div class="panel-body" >
                            <div class="img-wrapper">
                                <img style="height:80px" class="img-responsive center-block" src="../images/carrierlogo/<?=$rowImage->getLogo()?>" alt="<?=$rowImage->getCarrier()?>" uib-popover="<?=$rowImage->getCarrier()?>" popover-trigger="mouseenter" >
                            </div>
                        </div>
                        <div class="panel-footer clearfix">
                            <a class="btn btn-sm btn-default pull-left" data-pdeload="<?=$rowImage->getId();?>" data-target="#manual-shopping" data-toggle="modal">
                                 <span class="fa fa-pencil"></span> 
                            </a>
                            <a class="btn btn-sm btn-default pull-left" data-pdeload="<?=$rowImage->getId();?>" data-target="#manual-shopping" data-toggle="modal">
                                 <strong>Cut Off <?=$rowImage->getCutOfftime()?>
                            </a>
                            
                            <button class="btn btn-sm btn-success pull-right ng-scope"  data-pload="<?=$rowImage->getId()?>" data-target="#email-invoice" data-toggle="modal">
                                <span class="fa fa-plug"></span> Services
                            </button>
                        </div>
                    </div>
                </div>
                <?php
                    }
                ?>
                <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                <input type="hidden" name="form_action" id="form_action" value="" /> 
            </div>
	</div>
		<?php
	}
	
	/**
	* Override to show the menu
	*
	*/
	public function renderMenu()
	{
		$menu = new Adminmenu(Adminmenu::CUSTOMERS);
		$menu->render();
	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();