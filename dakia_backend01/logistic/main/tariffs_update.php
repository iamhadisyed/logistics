<?php

////////////////////////////////////////////////////
//
// List of tariffs
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $page_vars;

	/***
	 * Controller logic goes here
	 */
	public function init()
	{
		$load_all = false;
		$debug = true;
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		$this->setTitle("Update Charge Prices From Cost");
		$tariff_name	=	(isset($_REQUEST['tariff_name'])			 ? strip_tags($_REQUEST['tariff_name']) : '');

		$this->page_vars["service_id"] 						= util_request_num("service_id");
		$this->page_vars["collection_rateband_id"] 			= util_request_num("collection_rateband_id");
		$this->page_vars["destination_rateband_id"] 		= util_request_num("destination_rateband_id");
		$this->page_vars["tariff_name"] 		= util_request_num("tariff_name");
		$this->page_vars["collection_postcode_group_id"] 	= util_request_num("collection_postcode_group_id");
		/*------------------------------------------------------------------------------*/
		// not post back, read values from request string
		if (!isset($_POST['btn_save']))
		{
			$this->page_vars = array();
			//
			

			// default values
			$this->page_vars["price_variation"] = 0;
		}
		/*------------------------------------------------------------------------------*/
		// Postback - Process form
		else
		{
			$this->page_vars = util_getPostArray();
			//
			$perc = intval($this->page_vars["price_variation"]);
			// vary price

			// Get list of tariffs
			/*$where = "courier_service_id=" . $this->page_vars["service_id"] .
					" AND collection_rateband_id=" . $this->page_vars["collection_rateband_id"] .
					" AND destination_rateband_id=" . $this->page_vars["destination_rateband_id"];

			$coll_postcode_group_id = intval($this->page_vars["collection_postcode_group_id"]);
			if ($coll_postcode_group_id > 0) $where .= " AND collection_postcode_group_id=" . $coll_postcode_group_id;*/
			//
			
			$tarrifFilter = new TariffFilter();//::listFactory($where);
			$tarrifFilter->addFieldFilter('courier_service_id',$this->page_vars["service_id"]);
			$tarrifFilter->addFieldFilter('collection_rateband_id',$this->page_vars["collection_rateband_id"]);
			$tarrifFilter->addFieldFilter('destination_rateband_id',$this->page_vars["destination_rateband_id"]);
			$tarrifFilter->addFieldFilter('customer_id',$this->page_vars["tariff_name"]);
			
			if(trim($tariff_name) != '')
				$tarrifFilter->addFieldFilter('customer_id',$tariff_name);
			
			$tarrifList	=	$tarrifFilter->getList();
			if(count($tarrifList)>0)
			{
				foreach ($tarrifList as $tariff)
				{
					$tariff->setAddUnitCost ($this->calcPrice ($tariff->getExtraAddUnitCost(), $perc));
					$tariff->setTariff($this->calcPrice($tariff->getExtraTariff(), $perc));
					$tariff->save();
				}
			}
			// Send to edit form
			$page = "tariffs_details.php?service_id=" . $this->page_vars["service_id"] .
					"&collection_rateband_id=" . $this->page_vars["collection_rateband_id"] .
					"&destination_rateband_id=" . $this->page_vars["destination_rateband_id"] .
					"&tariff_name=" . $this->page_vars["tariff_name"] .
					"&collection_postcode_group_id=" . $this->page_vars["collection_postcode_group_id"];
			util_redirect($page);
		}
	}

	private function calcPrice ($val, $perc)
	{
		return ($val + (($val * $perc) / 100));
	}


	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		$p = $this->page_vars;
		// get rateband information
		$collRateBand = new RateBand(@$_REQUEST["collection_rateband_id"]);
		$destRateBand = new RateBand(@$_REQUEST["destination_rateband_id"]);
		//$collPostcode = new PostcodeGroup($p["collection_postcode_group_id"]);
		?>
        
        
        <div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Update Tariff With Percentage
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
								<a href="" class="fullscreen">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
							</div>
							
                            
						</div>
						<div class="portlet-body">
							<div class="scroller" style="min-height:350px; max-height:600px" data-always-visible="0" data-rail-visible="1" data-rail-color="blue" data-handle-color="red">
								
                                
                               
                                
                                
               <div>
			<div id="top-button-area">

				<a class="btn btn-primary pull-right" href="tariffs.php?service_id=<?php 
					echo $_GET["service_id"]; 
					echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.$_REQUEST['tariff_name'] :'';
					echo (isset($_REQUEST['collection_rateband_id']))? '&collection_rateband_id='.$_REQUEST['collection_rateband_id'] :'';
					echo (isset($_REQUEST['destination_rateband_id']))? '&destination_rateband_id='.$_REQUEST['destination_rateband_id'] :'';
					echo (isset($_REQUEST['collection_postcode_group_id']))? '&collection_postcode_group_id='.$_REQUEST['collection_postcode_group_id'] :'';
					echo (isset($_REQUEST['destination_postcode_group_id']))? '&destination_postcode_group_id='.$_REQUEST['destination_postcode_group_id'] :'';
					  ?>">Tariffs</a>
			</div>

			<table id="tbl_tariffs" class="table table-striped table-bordered table-hover" >
				<thead>
                	<tr>
						<th>NOTE: Please note that we have disable this service  on temprary basis.</th>
					</tr>
                    <tr>
						<th width="150">From Rateband</th>
						<th width="150">To Rateband</th>
					</tr>
                </thead>
				<tr id="tbl_selections">
					<td width="150">
							<?php echo $collRateBand->getName(); ?>
					</td>
					<td width="150">
							<?php echo $destRateBand->getName();  ?>
					</td>
				</tr>
				<tr class="textb">
					<th>Price Variation:</th>
					<td><input class="txt" id="price_variation" style="width:50px;" name="price_variation" type="text" value="<?php echo @$_REQUEST["price_variation"];?>">%</td>
				</tr>
			</table>

			<input type="hidden" name="action" id="action" value="save" />
			<input type="hidden" name="service_id" value="<?php echo $_REQUEST["service_id"]; ?>" />
			<input type="hidden" name="collection_rateband_id" value="<?php echo $_REQUEST["collection_rateband_id"]; ?>" />
			<input type="hidden" name="destination_rateband_id" value="<?php echo $_REQUEST["destination_rateband_id"]; ?>" />
            <input type="hidden" name="tariff_name" value="<?php echo $_REQUEST["tariff_name"]; ?>" />
			<!--<input class="btn btn-primary" id="btn_save" name="btn_save" type="submit" value="Update" >--><br/>
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
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?> 
