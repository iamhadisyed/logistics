<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Courier rateband list page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $service_id = 0;
	private $courier_id = 0;
	private $ratebands;
	private $CarierName	=	'';

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
                    <i class="glyphicon glyphicon-gift"></i>Rate Bands
                </div>
                <div class="tools">
		             <a href="ratebands_details.php?rateband_id=0&service_id=<?php echo $this->service_id ?>" style="color:#fff;" title="Add New Rate Band"><span class="glyphicon glyphicon-plus"></span></a>
                     <a href="services.php?courier_id=<?php echo $this->CarierName ?>" style="color:#fff;">Services</a>
                    <a href="javascript:;" class="collapse"></a>
                    
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px; "  data-rail-color="blue" data-handle-color="blue">
                  <table class="table table-striped table-bordered table-advance table-hover">
            	<thead>
			    <tr>
				    <th width="440">Rate Band</th>
					<th width="40" align="center">Action</th>
				</tr>
				</thead>
				<tbody>
              <?php
    	      foreach ($this->ratebands as $rateband)
			  {
				?>
				<tr>
					<td><?php echo $rateband->getName();?></td>
					<td align="center"><a class="link-button" href="ratebands_details.php?rateband_id=<?php echo $rateband->getId();?>"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;&nbsp; <a class="link-button" href="ratebands_details.php?rateband_id=<?php echo $rateband->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Rateband?')"><span class="glyphicon glyphicon-remove"></span></a></td>
				</tr>
			  	<?php
		      }
			  ?>
			  </tbody>
			</table>

		</div>
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
       $user = SessionManager::getUser();
        if ($user->getUserType() != "finance" && $user->getUserType() != "admin")
		{
			util_redirect("index.php");
		}

		/*------------------------------------------------------------------------------*/
		// service id passed to page
		$this->service_id = util_request_num("service_id");
		if ($this->service_id < 0) util_redirect("couriers.php");

		/*------------------------------------------------------------------------------*/
		// Get the service details
		$ServObj = new Services($this->service_id);
		$this->CarierName	=	$ServObj->getCarrier();
		//$this->CarierServiceName	=	$ServObj->getName();
		$this->setTitle ("Rate Bands for \"". $ServObj->getName() . "\".");
		$this->courier_id = $ServObj->getCourierId();

	    /*------------------------------------------------------------------------------*/
		// get ratebands
		$RbnObj         = new RatebandFilter;
         $RbnObj->addFieldFilter("courier_service_id",  $this->service_id );
		 $this->ratebands = $RbnObj->getColumnList("name");
		
	
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
  