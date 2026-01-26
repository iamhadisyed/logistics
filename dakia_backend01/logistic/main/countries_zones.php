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
	private $service_id = 0;
	private $courier_id = 0;
	private $_services = array();
	private $_czones;

	/**
	 * Get the html for the zone selection drop down list.
	 * Name is set to ### and is replaced where used.
	 */
	private function getZoneDropDown ($service_id, $name, $selectedRateId	= array())
	{
		$ZoneObj = new RatebandFilter();
		$ZoneObj->addFieldFilter('courier_service_id',$service_id);
		$zones = $ZoneObj->getColumnList('name');
		$html = "<select name=\"".$name."[]\" class='form-control' multiple=\"multiple\" >";
		$html .= "<option value=\"-1\"></option>";
		//
		if(count($zones)>0)
		{
			foreach ($zones as $zoneData) {
				if(in_array($zoneData->getId(), $selectedRateId))
					$html .= "<option value=\"". $zoneData->getId() ."\" selected >" . $zoneData->getName() . "</option>";
				else
					$html .= "<option value=\"". $zoneData->getId() ."\">" . $zoneData->getName() . "</option>";
			}
		}
		$html .= "</select>";
		return $html;
	}

	/***
 	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		// Get zone drop down (will reuse for each list)
		//$zone_dd = $this->getZoneDropDown($this->service_id);
		?>
		 <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-search"></i>Country Panel
                </div>
                <div class="tools">
                    <a href="services.php?courier_id=<?php echo $this->courier_id ?>" style="color:#fff;">Services</a>
                    <a href="javascript:;" class="collapse"></a>
                    
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px;"  data-rail-color="blue" data-handle-color="blue">
                  <table class="table table-striped table-bordered table-advance table-hover">
				<thead>
				<tr>
					<th width="380">Country</th>
					<th width="40" align="center">Ratebands</th>
				</tr>
                <tr>
					<th colspan="2"  >
                    <div class="alert alert-danger">
  <strong>Note!</strong> To assign Multiple Rate band Hold Ctrl
</div>
</th>

				</tr>
				</thead>
				<tbody>
				<?php
				//echo "<pre>";
				//print_r($this->_czones);
				
				//foreach ($this->_czones as $czone)
				foreach($this->countryList as $countryListData)
				{
					$name = "zone" . $countryListData->getId();
					$czone_obj = new Countryrateband();
					$_czones = $czone_obj->getFullCountryRatebandsList($this->service_id, $countryListData->getId());
					?>
					<tr >
						<td><?php echo $countryListData->getName(); // echo $czone->getCountryName();    changed_on?></td>
						<td align="center">
							<?php
							$selectedRateBands	=	array();
							if(count($_czones)>0)
							{
								foreach ($_czones  as $czone)
								$selectedRateBands[]	=	$czone->getRatebandId();
							}
							
							echo $this->getZoneDropDown($this->service_id, $name, $selectedRateBands);
							?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			 <button type="submit" class="btn btn-primary" id="btn_save" name="btn_save" value="Save Changes">
                Save changes
            </button>
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
		$sessionUser = SessionManager::getUser();
		/*------------------------------------------------------------------------------*/
		// service id passed to page
		$this->courier_id = util_request("courier_id");
		if ($this->courier_id < 0) util_redirect("couriers.php");

		$this->service_id = util_request_num("service_id");
		if ($this->service_id < 0) util_redirect("services.php?courier_id=" . $this->courier_id);

		$countryForZone	=	new CountryFilter();
		$this->countryList	=	$countryList	=	$countryForZone->getColumnList('id, name, iso');
		
		/*------------------------------------------------------------------------------*/
		// process form
		if (isset($_POST['btn_save']))
		{
			$czone_obj 	= new Countryrateband();
			$czone_obj->getExpnge($this->service_id);
			
			if(count($countryList) >0)
			{
				foreach($countryList as $countryData)
				{
					$countryId			=	$countryData->getId();
					$name				= "zone" .$countryData->getId();//$czone->getParentCountryId();
					$zoneListArray 		= $_POST[$name];
					if(count($zoneListArray) > 0 )
					{
						foreach($zoneListArray as $zoneId)
						{
							$czone_additional = new Countryrateband();
							$czone_additional->setRateBandId($zoneId);
							$czone_additional->setCountryId($countryId); // country Id
							$czone_additional->setActive(1); // country Id
							$czone_additional->setDeletedq(0); // country Id
							$czone_additional->setOrderq(0); // country Id
							$czone_additional->setAddedBy($sessionUser->getUserName()); // country Id
							$czone_additional->setAddedOn(date('Y-m-d h:i:s')); // country Id
							$czone_additional->setChangedOn(date('Y-m-d h:i:s')); // country Id
							$czone_additional->setChangedBy($sessionUser->getUserName()); // country Id
							$czone_additional->save();
						}
					}
				}
			}
			

			// go to service list
			util_redirect("services.php?courier_id=".$this->courier_id);
		}

		

		/*------------------------------------------------------------------------------*/
		// get the courier service details
		$ServObj = new Services($this->service_id);

		// set the title
		$this->setTitle("Country-Zones for \"" . $ServObj->getName() . "\"");
	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
