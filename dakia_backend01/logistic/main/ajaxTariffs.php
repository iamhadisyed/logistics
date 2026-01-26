<?php
require_once("../includes/settings/config.inc.php");
if(isset($_POST['action']) && trim($_POST['action']) == 'SHOW_ALL_TARIFF')
{
	$courierName	=	$_POST['courier_name'];
	$serviceId		=	$_POST['service_id'];
	$tariff_type		=	$_POST['tariff_type'];
	if(trim($tariff_type) == 'chargeable')
	{
		$tarFilter		= new TariffFilter();
		$tarFilter->addFieldFilter('courier_service_id',$serviceId);
		$tariffsData	= $tarFilter->getTarifDistinctList(' customer_id ');
		$htmlEn	=	 '<div class="row">';
		
		if(count($tariffsData)>0)
		{	
			$countService	=	1;
			foreach($tariffsData as $tariffItem)
			{
				$htmlEn	.=  '<div class="col-sm-3 download-tariff-popup" >
								<span class="pull-left" title="Open Tariff">
									<a href="tariffs.php?service_id='.$serviceId	.'&courier_id='.$courierName.'&tariff_name='.$tariffItem->getCustomerId().'">
										'.$tariffItem->getCustomerId().'
									</a>
								</span>
								<span class="glyphicon glyphicon-paperclip pull-right tariff-validitaty"   data-action="SHOW-TARIFF-VALIDITY"  data-tariff-type="CHARGE" data-tariff-name="'.$tariffItem->getCustomerId().'" title="Tariff Validity"></span>
							</div>';
				$countService++;
			}
		}
		else
		{
			$htmlEn	.=  '<div class="col-sm-12 cente text-danger">
							<center>
								Currently there is no tariff.
							</center>
						</div>';
		}
		$htmlEn	.=  '
				<div class="col-sm-12 center">
					<a class="btn btn-xs default blue-stripe " href="tariffs_details.php?service_id='.$serviceId	.'">ADD NEW TARIFF</a>
					<a class="btn btn-xs default blue-stripe " href="tariffs_full_upload.php?service_id='.$serviceId	.'">UPLOAD TARIFF LIST</a>
				</div>
			</div>';
	}
	else
	{
		$tarFilter		= new CostTariffsFilter();
		$tarFilter->addFieldFilter('courier_service_id',$serviceId);
		$tariffsData	= $tarFilter->getTarifDistinctList(' tariff_name ');
		$htmlEn	=	 '<div class="row">';
		if(count($tariffsData)>0)
		{	
			$countService	=	1;
			foreach($tariffsData as $tariffItem)
			{
				$htmlEn	.=  '<div class="col-sm-3 download-tariff-popup">
								<span class="pull-left"  title="Open Tariff">
									<a href="tariffs.php?service_id='.$serviceId	.'&courier_id='.$courierName.'&tariff_name='.$tariffItem->getTariffName().'">
										'.$tariffItem->getTariffName().'
									</a>
								</span>
								<span class="glyphicon glyphicon-paperclip pull-right tariff-validitaty"  data-action="SHOW-TARIFF-VALIDITY" data-tariff-type="COST" data-tariff-name="'.$tariffItem->getTariffName().'" title="Tariff Validity"></span>
							</div>';
				
				$countService++;
			}
		}
		else
		{
			$htmlEn	.=  '<div class="col-sm-12 cente text-danger">
							<center>
								Currently there is no tariff.
							</center>
						</div>';
		}
		$htmlEn	.=  '<div class="col-sm-12 center">
				<a class="btn btn-xs default blue-stripe " href="tariffs_cost_details.php?service_id='.$serviceId	.'">ADD NEW TARIFF</a>
				<a class="btn btn-xs default blue-stripe " href="tariffs_cost_full_upload.php?service_id='.$serviceId	.'">UPLOAD TARIFF LIST</a>
				</div>
			</div>';
		
	}
	
			echo $htmlEn;
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'GET_SERVICES')
{
	$tariff_name	=	$_POST['tariff_name'];
	
 	$tarFilter		= new TariffFilter();
	$tariffsData	= $tarFilter->getTarifServicesList($tariff_name);
	if(count($tariffsData	)>0)
	{
		foreach($tariffsData as $tariffsDataItems)
		{
			echo '<div class="col-md-4">'.$tariffsDataItems->getChangedBy().' 
						<a href="customers.php?tariff_name='.$tariff_name.'&serviceid='.$tariffsDataItems->getId().'&carrier='.$tariffsDataItems->getFormula().'&service_name='.$tariffsDataItems->getChangedBy().'&user_account=YPS&action=DOWNLOAD_ALL_CUSTOMER_TARIFF" >
							<span class="glyphicon glyphicon-download pull-right"></span>
						</a>
						<span class="pull-right">&nbsp;&nbsp;&nbsp;</span>
						<a href="tariffs.php?tariff_name='.$tariff_name.'&service_id='.$tariffsDataItems->getId().' &courier_id='.$tariffsDataItems->getFormula().'" >
							<span class="glyphicon glyphicon-eye-open pull-right"></span>
						</a>
				</div>';
		}
	}
	else
	{
		echo "Not Service found against this tariff";
	}
	die;
 
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'SHOW_ALL_CUSTOMER_TARIFF')
{
	$customer_account	=	$_POST['customer_account'];
	$tarFilter		= new TariffUserMappingFilter();
	$tarFilter->addFieldFilter('user_account',$customer_account);
	$tariffsData	= $tarFilter->getColumnList(' tariff_name, user_account ');
	$htmlEn	=	 "<div class='row'>";
	$htmlEn	.=	 "<div class='col-sm-12'>";

	if(count($tariffsData)>0)
	{	
		$countService	=	1;
		foreach($tariffsData as $tariffItem)
		{
			//customers.php?tariff_name='.$tariffItem->getTariffName().'&user_account='.$customer_account.'&action=DOWNLOAD_ALL_CUSTOMER_TARIFF
			$htmlEn	.=  '<div class="col-sm-3 download-tariff-popup" title="Download" data-action= "GET_SERVICES" data-tariff-name= "'.$tariffItem->getTariffName().'">
							<span class="pull-left">'.$tariffItem->getTariffName().'</span>
							<span class="glyphicon glyphicon-download pull-right"></span>
							</div>';
			$countService++;
		}
	}
	else
	{
		$htmlEn	.=  '<div class="col-sm-4">Currently, there is no tariff.</div>';
	}
	$htmlEn	.=  '</div>';
			echo $htmlEn;
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'SHOW_URL_CUSTOMER_TARIFF')
{
	$customer_tariff	=	$_POST['customer_tariff'];
	$tarFilter			=	new Tariff();
	echo $tariffsData		=	$tarFilter->getCustomerTariffUrl($customer_tariff);
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'SHOW-TARIFF-VALIDITY')
{
	$tariff_name			=	$_POST['tariff_name'];
	$tariff_type			=	$_POST['tariff_type'];
	$tariffExpDetail		=	array();
	$tariffValidityFilter	=	new TariffValidityFilter();
	$tariffValidityFilter->addFieldFilter('tv.tariff_name',$tariff_name);
	$tariffValidityFilterList =	$tariffValidityFilter->getList();
	if(count($tariffValidityFilterList)>0)
	{
		$tariffValidityFilterListNew		=	$tariffValidityFilterList[0];
		$tariffExpDetail['id']				=	$tariffValidityFilterListNew->getId();
		$tariffExpDetail['tariff_name']		=	$tariffValidityFilterListNew->getTariffName();
		$tariffExpDetail['tariff_type']		=	$tariffValidityFilterListNew->getTariffType();
		$tariffExpDetail['start_date']		=	date('d-m-Y',$tariffValidityFilterListNew->getStartDate());
		$tariffExpDetail['end_date']		=	date('d-m-Y',$tariffValidityFilterListNew->getEndDate());
		$tariffExpDetail['date_created']	=	date('d-m-Y',$tariffValidityFilterListNew->getDateCreated());
		$tariffExpDetail['added_by']		=	$tariffValidityFilterListNew->getAddedBy();
	}
	else
	{
		$tariffExpDetail['id']				=	0;
		$tariffExpDetail['tariff_name']		=	$tariff_name;
		$tariffExpDetail['tariff_type']		=	$tariff_type;
		$tariffExpDetail['start_date']		=	'';
		$tariffExpDetail['end_date']		=	'';
		$tariffExpDetail['date_created']	=	'';
		$tariffExpDetail['added_by']		=	'';
	}
	echo json_encode($tariffExpDetail);
	die;
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'SAVE_TARIFF_EXPIRE')
{
	$tariffExpDetail	=	array();
	
	
	$sessionManager = Sessionmanager::getUser();
	
	$id				=	(int)$_POST['id'];
	
	$date_start		=	$_POST['date_start'];
	if(trim($date_start) == '')
		$date_start		=	date('d-m-Y');
	$date_end		=	$_POST['date_end'];
	if(trim($date_end) == '')
		$date_end		=	date('d-m-Y');
	$tariff_type	=	$_POST['tariff_type'];
	$tariff_name	=	$_POST['tariff_name'];
	
	$tariffValidityRecord				=	new TariffValidity($id);
	$tariffValidityRecord->setTariffName($tariff_name);
	$tariffValidityRecord->setTariffType($tariff_type);
	$tariffValidityRecord->setStartDate(strtotime($date_start));
	$tariffValidityRecord->setEndDate(strtotime($date_end));
	$tariffValidityRecord->setDateCreated(time());
	$tariffValidityRecord->setAddedBy($sessionManager->getUsername());
	$tariffValidityRecord->save();
	
	$tariffExpDetail['id']				=	$tariffValidityRecord->getId();
	$tariffExpDetail['tariff_name']		=	$tariff_name;
	$tariffExpDetail['tariff_type']		=	$tariff_type;
	$tariffExpDetail['start_date']		=	$date_start;
	$tariffExpDetail['end_date']		=	$date_end;

		
	$tariffExpDetail['status']	=	'SUCCESS';
	$tariffExpDetail['message']	=	'You have successfully save tariff expiry date.';
	echo json_encode($tariffExpDetail);
}
else
{
	echo 'ERROR|| NO REQUEST FOUND';
}


?>
