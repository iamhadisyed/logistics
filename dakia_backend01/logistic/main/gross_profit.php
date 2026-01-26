<?php

////////////////////////////////////////////////////
//
// List of orders
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $id = 0;
	private $booking_array;

	/***
	* This page's content
	* @return void
	*/
	public function renderBody()
	{
		
		
		if (isset($this->form_vars["form_action"]))
		{
			// take appropriate action
			switch ($this->form_vars["form_action"])
			{
				
			}
		}
		
		//echo "kazim " . $this->form_vars['order_date'];
		
		?>
		<div>
			<table border="0" cellspacing="0" cellpadding="0" class="list-table">
				<tr>
					<td>Choose date from: <input name="order_date" id="order_date" type="text" value="<?php echo $this->form_vars['order_date'];?>">
                    </td>                                       
				</tr>
                <tr>
					<td>Choose date to: <input name="order_date_to" id="order_date_to" type="text" value="<?php echo $this->form_vars['order_date_to'];?>">
                    <input name="btn_go" type="submit" value="Go">
                    </td>                    
				</tr>			
                
			</table>

			<p>
				<a href="../controller/booking_list_download.php" class="link-button">Download</a>
			</p>
		</div>
		<?php
	}

	public function renderHead()
	{
		?>
		<script type="text/javascript">
		// Set date picker
		$(document).ready(function() {
			$("#order_date").datepicker({dateFormat: 'dd-mm-yy', showOn: 'button', buttonImage: '../images/calendar.gif', buttonImageOnly: true});
			
			$("#order_date_to").datepicker({dateFormat: 'dd-mm-yy', showOn: 'button', buttonImage: '../images/calendar.gif', buttonImageOnly: true});

		});
		</script>
		<?php
	}


	/**
	* Override to show the menu
	*
	*/
	public function renderMenu()
	{
		$menu = new Adminmenu(Adminmenu::BOOKINGS);
		$menu->render();
	}

	/***
	* Controller logic goes here
	*/
	public function init()
	{
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		$this->setTitle("Gross Profit");

		/*------------------------------------------------------------------------------*/
		// get vars
		$this->order_date = (isset($_POST['order_date']) ? strip_tags($_POST['order_date']) : date("d/m/Y"));

		/*------------------------------------------------------------------------------*/
		// format date
		$this->order_dateDMY = str_replace("/", "-", $this->order_date);
		$this->order_date_value = strtotime($this->order_dateDMY);

		/*------------------------------------------------------------------------------*/
		// get orders

		if ($this->order_date > 0)
		{
        	$filter = new ParcelGroupFilter();
        	$filter->setFilterDate($this->order_date_value);
       		$this->booking_array = $filter->getFullInfoList();
       		//
       		SessionManager::saveParcelGroupFilter($filter);
		}
		else
		{
			$this->booking_array = array();
		}
	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
