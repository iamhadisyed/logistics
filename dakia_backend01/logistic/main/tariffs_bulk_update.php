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
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) 
		{
			util_redirect("login.php");
		}
		
		//Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
		$sessionUser = SessionManager::getUser();	
		
		
		if (trim(util_request("action")) == "BULK_UPLOAD_TARIFF") 
		{
			$id				=	((trim($_POST['id']) != '')? $_POST['id'] : '0');
			$value			=	((trim($_POST['value']) != '')? $_POST['value'] : '0');
			$option			=	((trim($_POST['option']) != '')? $_POST['option'] : '0');
			if($id > 0)
			{
				$tariffData			=	new TariffFilter();
				$tariffData->addFieldFilter('id', $id	);
				
				$recordSet	=	$tariffData->getCollectionZones(' * ');
				
				if(count($recordSet)> 0)
				{
					$dataTariff	=	$recordSet[0];
					
					
					if(trim($option) == 'tariff')
						$dataTariff->setTariff($value);
					else if(trim($option) == 'add_unit_cost')	
						$dataTariff->setAddUnitCost($value);	
					else if(trim($option) == 'extra_tariff')	
						$dataTariff->setExtraTariff($value);	
					else if(trim($option) == 'extra_add_unit_cost')	
						$dataTariff->setExtraAddUnitCost($value);	
					
					$dataTariff->setChangedOn(date('Y-m-d h:i:s', time()));	
					$dataTariff->setChangedBy($sessionUser->getUsername());	
					$dataTariff->save();
						echo "SUCCESS";
				}
				else
						echo "ERROR - 2";
			}
			else
			{
				echo "ERROR - 3";
			}
			
			die;
		}
		
		$tariff_name		=	(isset($_REQUEST['tariff_name']) ? strip_tags(htmlspecialchars($_REQUEST['tariff_name'])) : '');
		$this->page_vars["service_id"] 						= util_request_num("service_id");
		$this->page_vars["option"] 						= util_request("tariff");
		$this->page_vars["tariff_name"] 					= $tariff_name;
	
		$tariffDetailFilter	=	new TariffFilter();
		$tariffDetailFilter->addFieldFilter('courier_service_id', $this->page_vars["service_id"]);
		$tariffDetailFilter->addFieldFilter('customer_id', $this->page_vars["tariff_name"]);
		$collectionZone	=	$tariffDetailFilter->getCollectionZones(' DISTINCT collection_rateband_id');
		$collectionRateBandsArray	=	array();
		$destinationRateBandsArray	=	array();
		$weightArray				=	array();
		if(count($collectionZone) > 0 )
		{
			foreach($collectionZone as $collectionRateBands)
			{
				$collectionRateBandsArray[]	=	$collectionRateBands->getCollectionRatebandId();
			}
		}
		
		if(count($collectionRateBandsArray)>0)
		{
			foreach($collectionRateBandsArray as $colRatebandId	)
			{
				$tariffDeFilter	=	new TariffFilter();
				$tariffDeFilter->addFieldFilter('courier_service_id', $this->page_vars["service_id"]);
				$tariffDeFilter->addFieldFilter('customer_id', $this->page_vars["tariff_name"]);
				$tariffDeFilter->addFieldFilter('collection_rateband_id', $colRatebandId);
				$tariffDeFilter->addFieldOrderBy(' weight_from asc');
				$destinationZone	=	$tariffDeFilter->getCollectionZones(' DISTINCT destination_rateband_id');
				if(count($destinationZone)>0)
				{
					foreach($destinationZone as $destZones)
					{
						$destinationZoneId	=	$destZones->getDestinationRatebandId();
						$destinationRateBandsArray[$colRatebandId][]	=	$destinationZoneId;
					}
				}	
			}
		}
		
		if(count($collectionRateBandsArray)>0)
		{
			foreach($collectionRateBandsArray as $colRatebandId	)
			{
				$tariffWeightFilter	=	new TariffFilter();
				$tariffWeightFilter->addFieldFilter('courier_service_id', $this->page_vars["service_id"]);
				$tariffWeightFilter->addFieldFilter('customer_id', $this->page_vars["tariff_name"]);
				$tariffWeightFilter->addFieldFilter('collection_rateband_id', $colRatebandId);
				$tariffWeightFilter->addFieldOrderBy(' weight_from asc');
				$weightZone	=	$tariffWeightFilter->getCollectionZones(' DISTINCT weight_from, weight_to');
				if(count($weightZone)>0)
				{
					$countVariables	=	0;
					foreach($weightZone as $weightCoZones)
					{
						$fromweight		=	$weightCoZones->getWeightFrom();
						$toWeight		=	$weightCoZones->getWeightTo();
						$weightArray[$colRatebandId][$countVariables]['from']	=	$fromweight;
						$weightArray[$colRatebandId][$countVariables]['to']	=	$toWeight;
						$countVariables++;
					}
				}	
			}
		}
		$this->AllCollections		=	$collectionRateBandsArray;
		$this->AllDestination		=	$destinationRateBandsArray;
		$this->AllWeights			=	$weightArray;
		
/*		{
			$this->page_vars = util_getPostArray();
			$page = "tariffs_details.php?service_id=" . $this->page_vars["service_id"] .
					"&collection_rateband_id=" . $this->page_vars["collection_rateband_id"] .
					"&destination_rateband_id=" . $this->page_vars["destination_rateband_id"] .
					"&tariff_name=" . $this->page_vars["tariff_name"] .
					"&collection_postcode_group_id=" . $this->page_vars["collection_postcode_group_id"];
			util_redirect($page);
		}*/

		(int)$this->service_id		= 	(isset($_REQUEST['service_id']) ? strip_tags($_REQUEST['service_id']) : '');
		$serviceObject 				= 	new Services($this->service_id);
		$this->courier_id			= 	$serviceObject->getCarrier();
	}
	
	public function renderHead()
	{
	?>
    <script>
	function updatetariffvalue(tariffId, tariffValue, tariffOption)
	{
		//alert(tariffId+' - '+tariffValue+' - '+tariffOption);
		if ($("#pricing_details_msg").hasClass("alert-success"))
			$("#pricing_details_msg").removeClass("alert-success");
		$("#pricing_details_msg").addClass("alert-info");
		$("#pricing_details_msg").html("please wait we are saving...");
		$("#pricing_details_msg").show();
		
		
		$.post("tariffs_bulk_update.php", {action:'BULK_UPLOAD_TARIFF',id:tariffId,  value:tariffValue, option:tariffOption}, function(data) {
			if ($("#pricing_details_msg").hasClass("alert-info"))
				$("#pricing_details_msg").removeClass("alert-info");
			$("#pricing_details_msg").addClass("alert-success");
			$("#pricing_details_msg").html("Updated successfully.");
			$("#pricing_details_msg").show();
			
		});
		
	}
    </script>
	<?	
	}
	
	
	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		$p 				= $this->page_vars;
		$tariff_name	=	htmlspecialchars($_REQUEST['tariff_name']);
		$service_id		=	$this->service_id;
		$optionRaw		=	$this->page_vars["option"];
		
	?>
    
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i>UPDATE BULK TARIFF
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
            <div class="alert alert-success" id="pricing_details_msg" style="display:none;">Updated successfully</div>
            <div>
			<div id="top-button-area">
				<a class="btn btn-primary pull-right" href="tariffs.php?service_id=<?php 
					echo $_GET["service_id"]; 
					echo (isset($this->page_vars["tariff_name"]))? '&tariff_name='.$this->page_vars["tariff_name"] :''; ?>">Tariffs</a>
			</div>
			<table id="tbl_tariffs" class="table table-striped table-bordered table-hover" >
				<thead>
                	<tr>
						<th width="150" class="red-back">Service Name</th>
                        <th width="150" class="red-back">Tariff Name</th>
					</tr>
                </thead>
				<tr id="tbl_selections">
					<td width="150"><?php echo $this->courier_id; ?></td>
                    <td width="150"><?php echo htmlspecialchars($_REQUEST['tariff_name']); ?></td>
				</tr>
			</table>
            <table id="tbl_tariffs" class="table table-striped table-bordered table-hover" >
                
                <?php 
				
				foreach($this->AllCollections as $collectionRateBandd)
				{
					$collRateBand = new RateBand($collectionRateBandd);
					$returnDestArray	=	array();
					?>
                        <tr>
                            <th colspan="<?= (count($this->AllDestination[$collectionRateBandd]) +1 )?>" class="red-back">Tariff Details From <?php echo $collRateBand->getName();?>:</th>
                        </tr>
                        <tr>
	                        <th  class="bg-blue">Kgs / Zones</th>
                            <?php 
								foreach($this->AllDestination[$collectionRateBandd] as $destinationRatebands )
								{
									$DestRateBand = new RateBand($destinationRatebands);
									$returnDestArray[$destinationRatebands]	=	$this->getAllWeightsColDes($service_id, $tariff_name, $collectionRateBandd, $destinationRatebands )
							?>
								<th  class="bg-blue"><?php echo $DestRateBand->getName();?></th>
							<?php 	
								
								}
							?>
                            
                        </tr>
                        <?php 
						foreach($this->AllWeights[$collectionRateBandd] as $weigthLo )
						{
						?>
                        <tr>
	                        <th  class="red-back"><?=$weigthLo['to']?></th>
                            <?php
							foreach($this->AllDestination[$collectionRateBandd] as $destinationRatebands )
								{
									$id				=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['id'];
									if(trim($optionRaw == 'cost'))
									{
										$tariff			=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['tariff'];
										$option			=	'tariff';
									}
									elseif(trim($optionRaw == 'unit'))
									{
										$tariff		=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['unit_cost'];
										$option			=	'add_unit_cost';
									}
									elseif(trim($optionRaw == 'excost'))
									{
										$tariff		=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['ex_tariff'];
										$option			=	'extra_tariff';
									}
									elseif(trim($optionRaw == 'exunit'))
									{
										$tariff	=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['ex_unit_cost'];
										$option			=	'extra_add_unit_cost';
									}
									$updateOption	=	'tariff';
									echo '<td><input type="text" value="'.$tariff.'" class="form-control" onblur="updatetariffvalue(\''.$id.'\',this.value, \''.$option. '\')" ></td>';
								}
                            	
							?>
                        </tr>
						<?php	
						}
						?>
                        
					<?php
				}
				?>
                <tr>
                	<td colspan="3"><?php
					/*echo "<pre>";
                    print_r($this->AllCollections);
					print_r($this->AllDestination);
					print_r($this->AllWeights);
					echo "</pre>";*/
					?></td>
                </tr>
			</table>
		</div>                 
        </div>
    </div>
</div>
		<?php
	}
	
	public function getAllWeightsColDes($service, $tariff, $collection, $destination )
	{
		$arrayDestination		=	array();
		$tariffWeightFilter		=	new TariffFilter();
		$tariffWeightFilter->addFieldFilter('courier_service_id', $service);
		$tariffWeightFilter->addFieldFilter('customer_id', $tariff );
		$tariffWeightFilter->addFieldFilter('collection_rateband_id', $collection);
		$tariffWeightFilter->addFieldFilter('destination_rateband_id', $destination );
		$tariffWeightFilter->addFieldOrderBy(' weight_from asc');
		//$tariffWeightFilter->addFieldFilter('active',  '1');
		$tariffDestinationZone	=	$tariffWeightFilter->getCollectionZones(' id, weight_from, weight_to, tariff, add_unit_cost, extra_tariff, extra_add_unit_cost');
		if(count($tariffDestinationZone)>0)
		{
			foreach($tariffDestinationZone as $tariffs)
			{
				$arrayDestination[$tariffs->getWeightFrom()]['id']	=	$tariffs->getId();
				$arrayDestination[$tariffs->getWeightFrom()]['tariff']	=	$tariffs->getTariff();
				$arrayDestination[$tariffs->getWeightFrom()]['unit_cost']	=	$tariffs->getAddUnitCost();
				$arrayDestination[$tariffs->getWeightFrom()]['ex_tariff']	=	$tariffs->getExtraTariff();
				$arrayDestination[$tariffs->getWeightFrom()]['ex_unit_cost']	=	$tariffs->getExtraAddUnitCost();
			}
		}
		//print_r($arrayDestination);
		//die;
		return $arrayDestination;
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
