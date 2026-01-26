<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Courier details page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $rateband			= NULL;

	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		?>
		<div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-gift"></i>Rate Bands Add/Edit
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                <div class="row">
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>Name: </label>
                                <input class="form-control" name="name" id="name" type="text" value="<?php echo $this->rateband->getName();?>">
                              </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                             <div class="form-group">
                              <a class="btn btn-primary" href="services.php?courier_id=<?=  util_request('courier_id')?>">
                                	Back
                                </a>
                            	<button type="submit" class="btn btn-primary" id="btn_save" name="btn_save" value="Save Changes">
                                	Save changes
                                </button>
                              </div>
                        </div>
                    </div>
         
         		</div>
	        </div>
        </div>
		<?php
	}

	/**
	 * Add content to the head section
	 *
	 */
	public function renderHead()
	{
		?>
		<script type="text/javascript">
		<!--
		$(document).ready(function(){
			// set focus
			$("#name").focus();
		});
		//-->
		</script>
		<?php
	}

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::COURIERS);
    	$menu->render();
    }

	/***
	 * Controller logic goes here
	 */
	public function init()
	{
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR  is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		/*------------------------------------------------------------------------------*/
		// get the rateband, service id passed and action
		$id					= util_request_num ("rateband_id");
		$this->service_id	= util_request_num ("service_id");
		$this->action		= util_request ("action");

		/*------------------------------------------------------------------------------*/
		// get the rateband details
		$this->rateband = new Rateband($id);

		// use service id from rateband
		if ($this->rateband->getCourierServiceId() > 0) $this->service_id = $this->rateband->getCourierServiceId();

		$Serv = new Services($this->service_id);
		$this->setTitle("Edit Rate Band for Courier Service: " . $Serv->getName());

		/*------------------------------------------------------------------------------*/
		// process form
		if (isset($_POST['btn_save']))
		{
			// save rateband details
			$this->rateband->setName (util_request("name"));
			$this->rateband->setCourierServiceId($this->service_id);
			$this->rateband->setActive(1);
			$this->rateband->setDeletedq('N');
			$this->rateband->setAddedOn(strtotime(time('Y-m-d H:i:s')));
			
			$this->rateband->setAddedBy($_SESSION["admin"]["user_name"]);
			$this->rateband->setChangedOn(strtotime(date('Y-m-d H:i:s')));
			$this->rateband->setChangedBy($_SESSION["admin"]["user_name"]);
			
			
			

			$this->rateband->save();

			// go to rateband list
			util_redirect("ratebands.php?service_id=".$this->service_id);
		}

		/*------------------------------------------------------------------------------*/
		// process delete
		if ($this->action == "confirmed_delete")
		{
			// save times and details
			$this->rateband->delete();

			// go to rateband list
			util_redirect("ratebands.php?service_id=".$this->service_id);
		}
	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 