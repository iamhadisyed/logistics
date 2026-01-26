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
		
		$tariff_name						=	(isset($_REQUEST['tariff_name']) ? strip_tags($_REQUEST['tariff_name']) : '');
		$this->page_vars["service_id"] 		= util_request_num("service_id");
		$this->page_vars["option"] 			= util_request("tariff");
		$this->page_vars["tariff_name"] 	= $tariff_name;
	
		$tariffDetailFilter			=	new CostTariffsFilter();
		$tariffDetailFilter->addFieldFilter('courier_service_id', $this->page_vars["service_id"]);
		$tariffDetailFilter->addFieldFilter('tariff_name', $this->page_vars["tariff_name"]);
		$collectionZone				=	$tariffDetailFilter->getCollectionZones(' DISTINCT collection_rateband_id');
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
				$tariffDeFilter	=	new CostTariffsFilter();
				$tariffDeFilter->addFieldFilter('courier_service_id', $this->page_vars["service_id"]);
				$tariffDeFilter->addFieldFilter('tariff_name', $this->page_vars["tariff_name"]);
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
				$tariffWeightFilter	=	new CostTariffsFilter();
				$tariffWeightFilter->addFieldFilter('courier_service_id', $this->page_vars["service_id"]);
				$tariffWeightFilter->addFieldFilter('tariff_name', $this->page_vars["tariff_name"]);
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

		(int)$this->service_id		= 	(isset($_REQUEST['service_id']) ? strip_tags($_REQUEST['service_id']) : '');
		$serviceObject 				= 	new Services($this->service_id);
		$this->courier_id			= 	$serviceObject->getCarrier();
		$this->serviceName			= 	$serviceObject->getName();
		
		
		
		
		/***************/
		$csvSting		=	'';
		$p 				= $this->page_vars;
		$tariff_name	=	$_REQUEST['tariff_name'];
		$service_id		=	$this->service_id;
		$optionRaw		=	$this->page_vars["option"];
		foreach($this->AllCollections as $collectionRateBandd)
		{
			$collRateBand = new RateBand($collectionRateBandd);
			$returnDestArray	=	array();
			
			$csvSting	.=	"Kgs,";
			foreach($this->AllDestination[$collectionRateBandd] as $destinationRatebands )
			{
				$DestRateBand = new RateBand($destinationRatebands);
				$returnDestArray[$destinationRatebands]	=	$this->getAllWeightsColDes($service_id, $tariff_name, $collectionRateBandd, $destinationRatebands );
				$csvSting	.= $DestRateBand->getName().",";
			}
			$csvSting	.= "\n";
			foreach($this->AllWeights[$collectionRateBandd] as $weigthLo )
			{
				$csvSting	.=	$weigthLo['to'].",";
				foreach($this->AllDestination[$collectionRateBandd] as $destinationRatebands )
				{
					$id				=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['id'];
					if(trim($optionRaw == 'tariff_cost'))
					{
						$tariff		=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['tariff_cost'];
						$option			=	'tariff_cost';
					}
					elseif(trim($optionRaw == 'unit_cost'))
					{
						$tariff	=	$returnDestArray[$destinationRatebands][$weigthLo['from']]['unit_cost'];
						$option			=	'unit_cost';
					}
					$updateOption	=	'tariff';
					$csvSting	.=  $tariff.",";
				}
				$csvSting	.= "\n";
			}
		}
		
		
		header('Expires: 0');
		header('Cache-control: private');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-Type: application/csv');
		header('Content-disposition: attachment; filename='.$this->page_vars["option"] .'-tariff-of-'. $tariff_name.'-'. str_replace(' ', '-', $this->serviceName).'-'.date('YMdhis').'.csv');
		echo $csvSting;
		die;
		
		/*******************/
		
		
	}
	
	
	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		


	}
	
	public function getAllWeightsColDes($service, $tariff, $collection, $destination )
	{
		$arrayDestination		=	array();
		$tariffWeightFilter		=	new CostTariffsFilter();
		$tariffWeightFilter->addFieldFilter('courier_service_id', $service);
		$tariffWeightFilter->addFieldFilter('tariff_name', $tariff );
		$tariffWeightFilter->addFieldFilter('collection_rateband_id', $collection);
		$tariffWeightFilter->addFieldFilter('destination_rateband_id', $destination );
		$tariffWeightFilter->addFieldOrderBy(' weight_from asc');
		//$tariffWeightFilter->addFieldFilter('active',  '1');
		$tariffDestinationZone	=	$tariffWeightFilter->getCollectionZones(' id, weight_from, weight_to, tariff_cost, unit_cost');
		if(count($tariffDestinationZone)>0)
		{
			foreach($tariffDestinationZone as $tariffs)
			{
				$arrayDestination[$tariffs->getWeightFrom()]['id']	=	$tariffs->getId();
				$arrayDestination[$tariffs->getWeightFrom()]['tariff_cost']	=	$tariffs->getTariffCost();
				$arrayDestination[$tariffs->getWeightFrom()]['unit_cost']	=	$tariffs->getUnitCost();
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
 