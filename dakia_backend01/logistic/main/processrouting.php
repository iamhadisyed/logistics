<?php

//
// get settings

require_once("../includes/settings/config.inc.php");

$sessionUser = SessionManager::getUser();
$sessionUserId = $sessionUser->getId();
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ROUTING_COUNTRIES') {
    $routing_name = $_REQUEST['routing_name'];
    $psrupdate = new PartnerServicesRoutingFilter();
    $psrupdate->addRoutingNameFilter($routing_name);
    $psrupdate->addORPRServicesFilter();
    $psrupdate->addOrderBy(' country ASC ');

    $partnersRoutingCountry = $psrupdate->getCustomColumnList(' DISTINCT country ');
    $countCountry = 1;
    if (count($partnersRoutingCountry) > 0) {
        $display_stringDemo = "<table class='table table-striped table-bordered table-advance table-hover'>";
        $display_stringDemo .= "<thead><tr>";
        $display_stringDemo .= "<th>Country</th>";
        $display_stringDemo .= "<th>Option</th>";
        $display_stringDemo .= "</tr></thead><tbody>";

        foreach ($partnersRoutingCountry as $country) {
            $display_stringDemo .= "<tr>";
            $display_stringDemo .= '<td>' . $country->getCountry() . '</td>';
            $display_stringDemo .= '<td><span  onclick="return getCountryServiceWeight(\'' . $country->getCountry() . '\',\'' . $routing_name . '\')" title="view ' . $country->getCountry() . '"><img src="http://oneworldexpress.co.uk/remote/images/view-r.png" alt="View"></span>&nbsp;&nbsp;
						<span  onclick="return getCountryServiceWeightEdit(\'' . $country->getCountry() . '\',\'' . $routing_name . '\')" title="edit ' . $country->getCountry() . '"><img src="http://oneworldexpress.co.uk/remote/images/edit-r.png"  alt="Edit"></span>&nbsp;&nbsp;
						<span  onclick="return getCountryServiceWeightDelete(\'' . $country->getCountry() . '\',\'' . $routing_name . '\')" title="delete ' . $country->getCountry() . '"><img src="http://oneworldexpress.co.uk/remote/images/delete-r.png"  alt="Delete"></span></td>';
            $display_stringDemo .= "</tr>";
            $countCountry++;
        }
        $display_stringDemo .= "</tbody></table>";

        echo $display_stringDemo;
    }
    die;
}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'COUNTRIES_DETAIL_EDIT') {

    $country_name = $_REQUEST['country_name'];
    $routing_name = $_REQUEST['routing_name'];
    $serviceArray = array();
    $service = new ServiceFilter();
    $resultServices = $service->getColumnList('code');
    if (count($resultServices) > 0) {
        foreach ($resultServices as $serviceCode) {
            $serviceArray[] = $serviceCode->getCode();
        }
    }
    $weightCount = 0.25;
    $display_stringDemo = "<table class='table table-striped table-bordered table-advance table-hover'>";
    $display_stringDemo .= "<thead><tr>";
    $display_stringDemo .= "<th>Routing Name</th>";
    $display_stringDemo .= "<th>Country Name</th>";
    $display_stringDemo .= "</tr></thead><tbody>";
    $display_stringDemo .= "<tr>";
    $display_stringDemo .= '<td>' . $routing_name . '</td>';
    $display_stringDemo .= '<td>' . $country_name . '</td>';
    $display_stringDemo .= "</tr>";
    $display_stringDemo .= "</tbody></table>";

    $display_stringDemo .= "<table class='table table-striped table-bordered table-advance table-hover'>";
    $display_stringDemo .= "<thead><tr>";
    $display_stringDemo .= "<th colspan='2'>Bulk Routing Update</th>";
    $display_stringDemo .= "</tr></thead><tbody>";
    $display_stringDemo .= "<tr>";
    $display_stringDemo .= '<td><strong>Weight:</strong>0.00 to 30.00 Kg</td>';
    $display_stringDemo .= '<td><input type="checkbox" name="bulk_upload" value="bulk_upload"> Please check for bulk upload</td>';
    $display_stringDemo .= "</tr>";
    $display_stringDemo .= "<tr>";
    $display_stringDemo .= '<td>' . serviceBulkDropDown($serviceArray, '') . '</td>';
    $display_stringDemo .= '<td><button type="submit" class="btn btn-primary btn_save" id="btnSave" >Save</button></td>';
    $display_stringDemo .= "</tr>";
    $display_stringDemo .= "</tbody></table>";

    //echo $display_stringDemo;
    $psrupdate = new PartnerServicesRoutingFilter();
    $psrupdate->addCountryFilter($country_name);
    $psrupdate->addRoutingNameFilter($routing_name);
    $psrupdate->addOrderBy(' from_weight ASC ');
    //$psrupdate->addToWeightFilter($weightCount);
    $psrupdate->addORPRServicesFilter();
    $partnersRoutingCountry = $psrupdate->getCustomColumnList(' id, from_weight,  to_weight, service_name,country, carrier');
    $partnerServiceRouting = array();
    //$weightToLimit			=	array();
    if (count($partnersRoutingCountry) > 0) {
        $countRoutingAvailable = 0;
        foreach ($partnersRoutingCountry as $partnerRouting) {
            //$weightToLimit[$countRoutingAvailable]	=	$partnerRouting->getToWeight();

            $partnerServiceRouting["'" . $partnerRouting->getToWeight() . "'"]['id'] = $partnerRouting->getId();
            $partnerServiceRouting["'" . $partnerRouting->getToWeight() . "'"]['from_weight'] = $partnerRouting->getFromWeight();
            $partnerServiceRouting["'" . $partnerRouting->getToWeight() . "'"]['to_weight'] = $partnerRouting->getToWeight();
            $partnerServiceRouting["'" . $partnerRouting->getToWeight() . "'"]['service_name'] = $partnerRouting->getServiceName();
            $partnerServiceRouting["'" . $partnerRouting->getToWeight() . "'"]['country'] = $partnerRouting->getCountry();
            $partnerServiceRouting["'" . $partnerRouting->getToWeight() . "'"]['carrier'] = $partnerRouting->getCarrier();

            $countRoutingAvailable++;
        }
    }
    $display_stringDemo .= "<table class='table table-striped table-bordered table-advance table-hover'>";
    $display_stringDemo .= "<thead><tr>";
    $display_stringDemo .= "<th colspan='2'>Individual Routing Update</th>";
    $display_stringDemo .= "</tr></thead><tbody>";
    $display_stringDemo .= "<tr>";
    $display_stringDemo .= '<td colspan="2">';



    while ($weightCount <= 30) {
        //print_r($partnerServiceRouting["'".$weightCount."'"]);
        if (count($partnerServiceRouting) > 0 && isset($partnerServiceRouting["'" . number_format($weightCount, 2) . "'"])) {
            //echo $partnerServiceRouting["'".$weightCount."'"]['service_name'];
            $display_stringDemo .= '<div class="col-sm-6" style=" background:#ccc; text-align:center; border:1px solid #fff; padding:5px;" > <strong>Weight:</strong>' . $partnerServiceRouting["'" . number_format($weightCount, 2) . "'"]['to_weight'] . 'Kg <br><br>' . serviceDropDown($serviceArray, $partnerServiceRouting["'" . number_format($weightCount, 2) . "'"]['service_name']) . '<div style="clear:both;"></div>' . '</div>';
        } else {
            $display_stringDemo .= '<div class="col-sm-6" style=" background:#ccc; text-align:center;  border:1px solid #fff;  padding:5px;" > <strong>Weight:</strong>' . number_format($weightCount, 2) . 'Kg <br><br>' . serviceDropDown($serviceArray, '') . '<div style="clear:both;"></div>' . '</div>';
        }


        if ($weightCount <= 2) {
            $display_stringDemo .= '<input type="hidden" name="from_weight[]" value="' . ($weightCount - 0.25) . '">';
            $display_stringDemo .= '<input type="hidden" name="to_weight[]" value="' . $weightCount . '">';
        } else {
            $display_stringDemo .= '<input type="hidden" name="from_weight[]" value="' . ($weightCount - 0.5) . '">';
            $display_stringDemo .= '<input type="hidden" name="to_weight[]" value="' . $weightCount . '">';
        }

        if ($weightCount < 2) {
            $weightCount += 0.25;
        } else {

            $weightCount += 0.5;
        }
    }

    $display_stringDemo .= '<input type="hidden" name="routing_name" value="' . $routing_name . '">';
    $display_stringDemo .= '<input type="hidden" name="status" value="active">';
    $display_stringDemo .= '<input type="hidden" name="country" value="' . $country_name . '">';
    $display_stringDemo .= '<input type="hidden" name="service_type" value="PR">';
    $display_stringDemo .= '<input type="hidden" name="UPDATE_ROUTING" value="UPDATE_ROUTING">';

    $display_stringDemo .= '</td>';
    $display_stringDemo .= "</tr>";
    $display_stringDemo .= "<tr>";
    $display_stringDemo .= '<td colspan="2" align="center"> <button type="submit" class="btn btn-primary btn_save" id="btnSave" ><span></span>Save</button></td>';
    $display_stringDemo .= "</tr>";
    $display_stringDemo .= "</tbody></table>";
    echo $display_stringDemo;
    /* 	$psrupdate			=	new PartnerServicesRoutingFilter();
      $psrupdate->addCountryFilter($country_name);
      $psrupdate->addRoutingNameFilter($routing_name);
      $psrupdate->addOrderBy(' from_weight ASC ');
      $psrupdate->addORPRServicesFilter();

      $partnersRoutingCountry	=	$psrupdate->getCustomColumnList(' id, from_weight,  to_weight, service_name,country, carrier');
      $countCountry	=	1;
      if(count($partnersRoutingCountry)>0)
      {
      foreach($partnersRoutingCountry as $country)
      {
      echo '<div style="float:left; min-width:100px; padding:10px; margin:5px; background:#ccc;" >'.$country->getFromWeight().' - '.$country->getToWeight().'  : '.serviceDropDown($serviceArray	,	$country->getServiceName()).'</div>';
      echo '<div style="clear:both;"></div>';
      $countCountry++;
      }

      } */
    die;
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'COUNTRIES_DETAIL_DELETE') {

    $country_name = $_REQUEST['country_name'];
    $routing_name = $_REQUEST['routing_name'];
    $psrupdate = new PartnerServicesRoutingFilter();
    $psrupdate->addCountryFilter($country_name);
    $psrupdate->addRoutingNameFilter($routing_name);
    $psrupdate->addOrderBy(' from_weight ASC ');
    $psrupdate->addORPRServicesFilter();
    $psrupdate->deleteList();

    echo "<span style='color:red;'>Routing <strong>" . $routing_name . "</strong> for country <strong>" . $country_name . "</strong> has been successfully removed</span>";
    die;
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'COUNTRIES_DETAIL') {
    $country_name = $_REQUEST['country_name'];
    $routing_name = $_REQUEST['routing_name'];
    $psrupdate = new PartnerServicesRoutingFilter();
    $psrupdate->addCountryFilter($country_name);
    $psrupdate->addRoutingNameFilter($routing_name);
    $psrupdate->addOrderBy(' from_weight ASC ');
    $psrupdate->addORPRServicesFilter();

    $partnersRoutingCountry = $psrupdate->getServiceColumnList(' psr.id, psr.from_weight,  psr.to_weight, psr.service_name,psr.country, psr.carrier, ser.name');
    $countCountry = 1;
    if (count($partnersRoutingCountry) > 0) {
        $display_stringDemo = "<table class='table table-striped table-bordered table-advance table-hover'>";
        $display_stringDemo .= "<thead><tr>";
        $display_stringDemo .= "<th>Weight</th>";
        $display_stringDemo .= "<th>Carrier</th>";
        $display_stringDemo .= "<th>Service Code</th>";
        $display_stringDemo .= "</tr></thead><tbody>";

        foreach ($partnersRoutingCountry as $country) {
            $display_stringDemo .= "<tr>";
            $display_stringDemo .= '<td>' . $country->getFromWeight() . ' - ' . $country->getToWeight() . '</td>';
            $display_stringDemo .= '<td>' . $country->getCarrier() . '</td>';
            $display_stringDemo .= '<td>' . $country->getName() . '</td>';
            $display_stringDemo .= "</tr>";
            $countCountry++;
        }
        $display_stringDemo .= "</tbody></table>";
        echo $display_stringDemo;
    }
    die;
}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'GET_SERVICE_DROPDOWN') {
    $service = new ServiceFilter();
    $service->addCarrierFilter($_REQUEST['carrier']);
    $resultServices = $service->getColumnList('code, name');
    echo '<label>Service</label>';
    echo "<select name='services' id='services' class='form-control' >";
    echo "<option value=''>Select Service</option>";
    if (count($resultServices) > 0) {
        foreach ($resultServices as $serviceCode) {
            echo "<option value='" . $serviceCode->getCode() . "' >" . $serviceCode->getName() . "</option>";
        }
    }
    echo "</select>";
    die;
}

function serviceBulkDropDown($serviceArray, $selectedValue) {
    $dropDown = '<select id="handling" name="handlingBulk" class="form-control">';
    $dropDown .= '<option value=""> Select Service</option>';
    foreach ($serviceArray as $key => $serviceCode) {
        if (trim($selectedValue) == trim($serviceCode)) {
            $selectee = 'selected';
        } else {
            $selectee = '';
        }
        $dropDown .= '<option value="' . $serviceCode . '"  ' . $selectee . ' >' . $serviceCode . '</option>';
    }
    $dropDown .= '</select>';

    return $dropDown;
}

function serviceDropDown($serviceArray, $selectedValue) {
    $dropDown = '<select id="handling" name="handling[]" class="form-control">';
    $dropDown .= '<option value=""> Select Service</option>';
    foreach ($serviceArray as $key => $serviceCode) {
        if (trim($selectedValue) == trim($serviceCode)) {
            $selectee = 'selected';
        } else {
            $selectee = '';
        }
        $dropDown .= '<option value="' . $serviceCode . '"  ' . $selectee . ' >' . $serviceCode . '</option>';
    }
    $dropDown .= '</select>';

    return $dropDown;
}

?> 