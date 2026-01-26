<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Country list page
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
    <div class="caption"> <i class="glyphicon glyphicon-List"></i>Countries List </div>
    <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
  </div>
  <div class="portlet-body">
    <div class="scroller" style="min-height:200px; max-height:450px"  data-rail-color="blue" data-handle-color="blue">
      <table class="table table-striped table-bordered table-advance table-hover">
        <thead>
          <tr>
            <td colspan="3"><a href="country_details.php?id=0" class="btn btn-primary">Add new country</a></td>
          </tr>
          <tr>
            <th class="bg-red">Country</th>
            <th class="bg-red">Edit</th>
            <?php /*?><th width="40" align="center">Delete</th><?php */?>
          </tr>
        </thead>
        <tbody>
          <?php
			if (count($this->countries) > 0)
			{
				foreach ($this->countries as $country)
				{
					?>
          <tr>
            <td><?php echo $country->getName();?></td>
            <td class="clsButton"><a href="country_details.php?country_id=<?php echo $country->getId();?>" class="btn default blue-stripe btn-sm">Edit</a></td>
            <?php /*?><td class="clsButton"><a href="country_details.php?country_id=<?php echo $country->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Country?')">Delete</a></td><?php */?>
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
	 	$menu = new Adminmenu(Adminmenu::COUNTRIES);
	 	$menu->render();
	 }


	/***
	* Controller logic goes here
	*/
	public function init()
	{
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		//
		$this->setTitle("Admin Countries");

		/*------------------------------------------------------------------------------*/
		// get countries
		$CouObj = new CountryFilter;
		$this->countries = $CouObj->getList();
 	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
