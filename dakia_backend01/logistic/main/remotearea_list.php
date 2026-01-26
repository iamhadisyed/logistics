<?php

////////////////////////////////////////////////////
//
// List of courier services
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $_courier_id = 0;
	private $_services = array();

    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>

<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption"> <i class="glyphicon glyphicon-search"></i>Remote Area for <?php echo $this->_courier_id;?></div>
    <div class="tools"> <a href="couriers.php" style="color:#fff;">Couriers</a> <a href="javascript:;" class="collapse"></a> </div>
  </div>
  <div class="portlet-body">
    <div class="scroller" style="min-height:300px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
      <div class="row">
      	<div class="col-md-4">
        	<h3>Country</h3>
        </div>
        <div class="col-md-4">
	        <h3>Remote Areas Name</h3>
        </div>
        <div class="col-md-4">
	        <h3>Remote Areas Postcodes</h3>
        </div>
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
    public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::COURIERS);
    	$menu->render();
    }


    /**
     * Override to show the menu
     *
     */
    public function renderHead()
    {
    	?>
<script>
		function showTariffList(dataId, serviceId, courier_name)
		{
			 $.post( 
			 	"ajaxTariffs.php",
				{action:'SHOW_ALL_TARIFF',service_id:serviceId, courier_name:courier_name},
				function( data ) 
				{
					$("#"+dataId).html(data);
				});
//			$('#'+dataId).html(servicename + 'this is test ');
			$('#'+dataId).toggle();
		}
        </script>
<?php
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
		// courier id passed to page
		$this->_courier_id = util_request("courier_id");
		if ($this->_courier_id < 0) util_redirect("couriers.php");

        // get courier details
		//$CorObj         = new Courier($this->_courier_id);
        //if ($CorObj->getId() != $this->_courier_id) util_redirect("couriers.php"); // check got correct courier

        // set the title
        $this->setTitle("Courier Services for " .$this->_courier_id );//$CorObj->getName());

        // get the services for this courier
        $CorObj         = new Courier();
		//$CorObj->setId('1');
		$CorObj->setName($this->_courier_id );
		//$this->_services = $CorObj->getServices("", false);
		
		$service			=	new ServiceFilter();
		$service->addCarrierFilter($this->_courier_id);
		$this->_services	=	$service->getList();
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 