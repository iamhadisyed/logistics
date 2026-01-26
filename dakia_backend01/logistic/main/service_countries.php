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
	private $_service_id = 0;
	private $_title = "";
	private $_services = array();
	private $_czones;

	/***
	 * Set the page header
	 * @return void
	 */
	public function getTitle ()
	{
		echo "Service Countries" . $this->_title;
	}

	/***
 	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		//
		?>
		<div>
			<table width="560" border="0" cellspacing="0" cellpadding="0" class="list-table">
				<tr>
					<td colspan="3"><a href="service_details.php?id=0&courierid=<?php echo $this->_service_id; ?>">Add new service</a></td>
				</tr>
				<tr>
					<th width="480">Country</th>
					<th width="40" align="center">Zone</th>
				</tr>
				<tbody>
				<?php
				foreach ($this->_czones as $czone)
				{
					$name = "zone" . $czone->getId();
					?>
					<tr>
						<td><?php echo $czone->getName();?></td>
						<td align="center">
							<input type="text" name="<?php echo $name ?>" value="<?php $czone->getZoneName() ?>" />
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<input class="submit" id="btn_save" name="btn_save" type="submit" value="Save changes"><br/>
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
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		/*------------------------------------------------------------------------------*/
		// service id passed to page
		$this->_service_id = util_request_num("serviceid");
		if ($this->_service_id < 0) util_redirect("couriers.php");

		// get list of countries and zones
		$czone_obj = new Countryzone();
		//
		$this->_czones = $czone_obj->getCountryZones($this->_service_id);

        /*------------------------------------------------------------------------------*/
        // process form
        if (isset($_POST['btn_save']))
        {
			foreach ($this->_czones as $czone)
			{
				$input = "zone" . $czone->getId();

				// read zone name
				$zone_name = util_post($input);
			}


        	// get vars
        	$ServObj = new Courierservice($id);
	        $ServObj->id  = $id;
	        $ServObj->setName(util_request("name"));

	        // validate page
	        if ($ServObj->getName() == "") array_push($this->_error_array, "Service name cannot be blank");

            // save update
            if (sizeof($this->_error_array) == 0)
            {
            	$ServObj->setCourierId($courier_id); // in case adding new service
            	$ServObj->setActive(true);
            	$ServObj->save();
                // go to service list
                util_redirect("services.php?id=$courier_id");
            }

            // set the service object pointing to updated service object
            $this->_service = &$ServObj;
        }

	    /*------------------------------------------------------------------------------*/
		// get the courier service details
		$ServObj = new CourierService($this->_service_id);

		// set the title
		$this->_title = "Countries for \"" . $ServObj->getName() . "\"";
	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?> 
