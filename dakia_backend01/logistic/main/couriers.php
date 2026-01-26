<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Courier list page
//
////////////////////////////////////////////////////

// get settings

require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>

<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption"> <i class="glyphicon glyphicon-List"></i>Carrier List </div>
    <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
  </div>
  <div class="portlet-body">
    <div class="scroller" style="min-height:200px; "  data-rail-color="blue" data-handle-color="blue">
      <table class="table table-striped table-bordered table-advance table-hover">
        <thead>
          <tr>
            <th class="bg-red">Courier</th>
            <th class="bg-red">Services</th>
            <!--<th align="center">Edit</th>
                                <th align="center">Delete</th>--> 
          </tr>
        </thead>
        <tbody>
          <?php
                        if (count($this->couriers) > 0)
                        {
                            foreach ($this->couriers as $courier)
                            {
                            ?>
          <tr >
            <td><?php echo $courier->getCarrier();//$courier->getName();?></td>
            <td><a class="btn default blue-stripe" href="services.php?courier_id=<?php echo $courier->getCarrier();?>" >Services </a></td>
            <!--<td align="center"><a href="courier_details.php?courier_id=<?php //echo $courier//$courier->getId();?>" class="link-button">Edit</a></td>
                                <td align="center"><a href="courier_details.php?courier_id=<?php //echo $courier//$courier->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Courier?')"  class="link-button">Delete</a></td>--> 
          </tr>
          <?php
                            }
                        }
                        ?>
        </tbody>
      </table>
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


    /***
     * Controller logic goes here
     */
    public function init()
    {
        // check admin user is authenticated
		$user = SessionManager::getUser();
		if ($user->getUserType() != "finance" && $user->getUserType() != "admin")
		{
			util_redirect("index.php");
		}

		$this->setTitle("Admin - Couriers");

	    /*------------------------------------------------------------------------------*/
		// get couriers
		$CorObj         = new ServiceFilter();
        $this->couriers = $CorObj->getDistinctService();
	//	print_r($this->couriers);
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
