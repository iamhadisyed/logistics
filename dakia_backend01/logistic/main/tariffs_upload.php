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

		$this->setTitle("Upload Prices");

		/*------------------------------------------------------------------------------*/
		// not post back, read values from request string
		if (!isset($_POST['btn_save']))
		{
			$this->page_vars = array();
			//
			$this->page_vars["service_id"] = util_request_num("service_id");
			$this->page_vars["collection_rateband_id"] = util_request_num("collection_rateband_id");
			$this->page_vars["destination_rateband_id"] = util_request_num("destination_rateband_id");
			$this->page_vars["collection_postcode_group_id"] = util_request_num("collection_postcode_group_id");

			// default values
			$this->page_vars["price_variation"] = 0;
		}
		/*------------------------------------------------------------------------------*/
		// Postback - Process form
		else
		{
			$this->page_vars = util_getPostArray();

			 $FilObj = new Fileupload($_FILES["price_file"]);

			 $fileName = "";
			 if ($FilObj->uploaded)
			 {
			 	$file = "../_assets/pricelists/";
			 	$FilObj->process($file);

			 	//$file = $FilObj->filename_dst_name;

			 	if ($FilObj->processed)
			 	{
		 			$fileName = $FilObj->file_dst_pathname;
			 	}
			 }
			 //
			 $reload = ($this->page_vars["mode"] == "reload");

			 $tariffUpdate = new TariffUpload($fileName);
			 $tariffUpdate->setDebugMode($debug);
			 $tariffUpdate->setPriceVariation(intval($this->page_vars["price_variation"]));
			 //
			 $updated = $tariffUpdate->update(
			 	$this->page_vars["service_id"],
			 	$this->page_vars["collection_rateband_id"],
			 	$this->page_vars["destination_rateband_id"],
			 	$reload,
			 	$this->page_vars["collection_postcode_group_id"]
			 	);
			// Send to edit form
			$page = "tariffs_details.php?service_id=" . $this->page_vars["service_id"] .
					"&collection_rateband_id=" . $this->page_vars["collection_rateband_id"] .
					"&destination_rateband_id=" . $this->page_vars["destination_rateband_id"] .
					"&collection_postcode_group_id=" . $this->page_vars["collection_postcode_group_id"];

			if ($updated && $debug) { echo "<br><a href=\"" . $page . "\">Prices</a>"; die; }
			util_redirect($page);
		}
	}


	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		$p = $this->page_vars;

		// get rateband information
		$collRateBand = new RateBand($p["collection_rateband_id"]);
		$destRateBand = new RateBand($p["destination_rateband_id"]);
		$collPostcode = new PostcodeGroup($p["collection_postcode_group_id"]);
		?>
        
        
        
        <div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Tariffs
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
								
                                
                                
                                
                                
                                
                                
               
			<div id="top-button-area">
				<a class="btn btn-primary" href="tariffs.php?service_id=<?php echo $p["service_id"] ?>">Tariffs</a>
			</div>

			<table id="tbl_tariffs" class="table table-striped table-bordered table-hover">
				<tr class="textb" id="tbl_header">
					<th width="150">From Rateband</th>
					<th width="150">To Rateband</th>
					<th width="150">Postcode group</th>
					<th width="150">&nbsp;</th>
				</tr>
				<tr id="tbl_selections">
					<td width="150">
							<?php echo $collRateBand->getName(); ?>
					</td>
					<td width="150">
							<?php echo $destRateBand->getName();  ?>
					</td>
					<td width="150">
							<?php echo $collPostcode->getName(); ?>
					</td>
					<td width="150">&nbsp;</td>
				</tr>
			</table >

			<p>&nbsp;</p>


			<table class="table table-striped table-bordered table-hover">
				<tr class="textb">
					<td>Mode:</td>
					<td id="mode_row">
						<input type="radio" value="update" checked id="update" name="mode" /><label for="update">Update existing tariffs</label>
						<br>
						<input type="radio" value="reload" id="reload" name="mode" /><label for="reload">Reload from file</label>
					</td>
				</tr>
				<tr class="textb">
					<td>Price Variation:</td>
					<td><input class="txt" id="price_variation" style="width:50px;" name="price_variation" type="text" value="<?php echo @$p["price_variation"];?>">%</td>
				</tr>
				<tr class="textb">
					<td>Price File:</td>
					<td><input class="txt" id="price_file" width="400px" name="price_file" type="file" value=""></td>
			</table>

			<input type="hidden" name="action" id="action" value="save" />
			<input type="hidden" name="service_id" value="<?php echo $p["service_id"]; ?>" />
			<input type="hidden" name="collection_rateband_id" value="<?php echo $p["collection_rateband_id"]; ?>" />
			<input type="hidden" name="destination_rateband_id" value="<?php echo $p["destination_rateband_id"]; ?>" />
			<input type="hidden" name="collection_postcode_group_id" value="<?php echo $p["collection_postcode_group_id"]; ?>" />

			<input class="btn btn-primary" id="btn_save" name="btn_save" type="submit" value="Save changes" ><br/>
		                          
		
			

			
                                
                                
                                
                                
                                
                                
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
