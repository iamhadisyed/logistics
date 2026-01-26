<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');

include_classes([   
                    'country.class',
                    'countryfilter.class',
                    'services.class' ,
                    'servicefilter.class',]);
$error_list = array();
$msg = "";
$left_to_print = 0;
if(isset($_POST['action']) && trim($_POST['action']) == 'SELECTEDCOUNTRIES')
{
	$serviceType = $_POST['serviceType'];
	$serviceId = -1;
        if(isset($_POST['service_id']) && !empty($_POST['service_id']))
            $serviceId = $_POST['service_id'];
	$countryFilter = new CountryFilter();
	if(trim($serviceType) != 'ALL')
		$countryFilter->addRegionFilter("'".$serviceType."'");
	if($countryFilter->getCount()>0)
	{
		$CountryAllList = $countryFilter->getList();
		foreach ($CountryAllList as $countryListdata)
		{
                    $selected = "";
                    if(($serviceId == -1) || (Services::checkServiceExists($serviceId,$countryListdata->getId()) > 0))
                        $selected = "selected=selected";    
			echo '<option value="'.strtoupper($countryListdata->getId()).'" '.$selected.' >'.strtoupper($countryListdata->getName()).'</option>';
		}
	}
	else
	{
		echo '<option value="">No Country</option>';
	}
}

	

	