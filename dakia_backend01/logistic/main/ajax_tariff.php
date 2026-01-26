<?php
require_once("../includes/settings/config.inc.php");
if($_POST['ACTION'] == 'UPDATE')
{
	$tariffId		=	$_POST['tariffId'];
	$weight			=	$_POST['weight'];
	$exTariff		=	$_POST['exTariff'];
	$exUnitPrice	=	$_POST['exUnitPrice'];
	$tariff			=	$_POST['tariff'];
	$ubitPrice		=	$_POST['ubitPrice'];
	$unitSize		=	$_POST['unitSize'];
	$customerId		=	$_POST['customerId'];
	$formula		=	$_POST['formula'];
	$TarSavObj 	= new Tariff($tariffId);
	
	$TarSavObj->setTariff($tariff);
	$TarSavObj->setAddUnitCost($ubitPrice);
	$TarSavObj->setUnitSize($unitSize);
	$TarSavObj->setExtraTariff($exTariff);
	$TarSavObj->setExtraAddUnitCost($exUnitPrice);
	$TarSavObj->setCustomerId($customerId	);
	$TarSavObj->setFormula($formula);
	$TarSavObj->setChangedOn(date('Y-m-d h:i:s',time()));
	$TarSavObj->setChangedBy($_SESSION['admin']["user_account"]);
	$TarSavObj->setAddedOn(date('Y-m-d h:i:s',time()));
	$TarSavObj->setAddedBy($_SESSION['admin']["user_account"]);					
	$TarSavObj->save();
	//if($TarSavObj->save())
	//{
		echo "Changes has been successfuly saved";
	//}
	//else
	//{
	//	echo "Oops! There is some error, Please Try that again in a few moments.";
	//}
	
}
else if(trim($_POST['ACTION']) == 'ADD')
{
	$service_id						=	$_POST['service_id'];
	$collection_rateband_id			=	$_POST['collection_rateband_id'];
	$destination_rateband_id		=	$_POST['destination_rateband_id'];
	$collection_postcode_group_id	=	$_POST['collection_postcode_group_id'];
	$destination_postcode_group_id	=	$_POST['destination_postcode_group_id'];
	$weight							=	$_POST['weight'];
	$exTariff						=	$_POST['exTariff'];
	$exUnitPrice					=	$_POST['exUnitPrice'];
	$tariff							=	$_POST['tariff'];
	$ubitPrice						=	$_POST['ubitPrice'];
	$unitSize						=	$_POST['unitSize'];
	$customerId						=	$_POST['customerId'];
	$fr_weight						=	$_POST['fr_weight'];
	$formula						=	$_POST['formula'];
	
	
	$TarSavObj 	= new Tariff;
	$TarSavObj->setCourierServiceId($service_id);
	$TarSavObj->setCollectionRatebandId($collection_rateband_id);
	$TarSavObj->setDestinationRatebandId($destination_rateband_id);
	$TarSavObj->setCollectionPostcodeGroupId($collection_postcode_group_id);
	$TarSavObj->setDestinationPostcodeGroupId($destination_postcode_group_id);
	$TarSavObj->setTariff($tariff);
	$TarSavObj->setAddUnitCost($ubitPrice);
	$TarSavObj->setUnitSize($unitSize);
	$TarSavObj->setExtraTariff($exTariff);
	$TarSavObj->setExtraAddUnitCost($exUnitPrice);
	$TarSavObj->setCustomerId($customerId	);
	$TarSavObj->setWeightFrom( $fr_weight);
	$TarSavObj->setWeightTo($weight);
	$TarSavObj->setFormula($formula);
	$TarSavObj->setOrderq(0);
	$TarSavObj->setActive(1);
	$TarSavObj->setDeletedq('N');
	$TarSavObj->setChangedOn(date('Y-m-d h:i:s',time()));
	$TarSavObj->setChangedBy($_SESSION['admin']["user_account"]);
	$TarSavObj->setAddedOn(date('Y-m-d h:i:s',time()));
	$TarSavObj->setAddedBy($_SESSION['admin']["user_account"]);					
	$TarSavObj->save();
	//if($TarSavObj->save())
	//{
		echo "SUCCESS";
	//}
	//else
	//{
	//	echo "Oops! There is some error, Please Try that again in a few moments.";
	//}
	
}
else
{
	echo "Your are not allowed to do this action.";
}
?>
