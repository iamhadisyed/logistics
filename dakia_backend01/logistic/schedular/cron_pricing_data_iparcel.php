<?php

$localSettingsFile = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : '';
if (substr($localSettingsFile, 0, 4) == "www.")
    $localSettingsFile = substr($localSettingsFile, 4);
$localSettingsFile = str_replace(".", "_", $localSettingsFile);
if (empty($localSettingsFile)) {
    $localSettingsFile = $_SERVER["SCRIPT_NAME"];
    if (strpos($localSettingsFile, "beta") !== false) {
        $localSettingsFile = "beta_smarttrack_co";
    } else if (strpos($localSettingsFile, "staging") !== false) {
        $localSettingsFile = "staging_smarttrack_co";
    } else if (strpos($localSettingsFile, "local") !== false) {
        $localSettingsFile = "local_smarttrack_co";
    } else {
        $localSettingsFile = "smarttrack_co";
    }
}
require_once(__DIR__ . "/../includes/settings/config.inc.php");
include_classes([
    'bagging.class',
    'mawbfilter.class',
    'mawb.class',
    'consignment.class',
    'consignmentfilter.class',
    'services.class',
    'servicefilter.class',
    'parcel.class',
    'parcelfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'serviceagentmappingfilter.class',
    'serviceagentmapping.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'baggingservicesmapping.class',
    'mawbparcelmapping.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
]);

/* error_reporting(E_ALL & ~(E_NOTICE | E_WARNING));
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('memory_limit', '-1');
  set_time_limit(-1);
  ini_set('max_execution_time', -1); */


$fromDateCreated = date("Y-m-d H:i:s", strtotime("-3 hours"));
//$fromDateCreated = date("Y-03-01 00:00:00");
$toDateCreated = date("Y-m-d H:i:s", strtotime("-2 hours"));

//echo $fromDateCreated . " -- " . $toDateCreated;
$mysql_access = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

session_name(CONFIG_COOKIE_NAME . "_session");
session_start();

Sessionmanager::setSessionId(session_id());
$sessionUser = SessionManager::getUser('DEVELOPER');
echo "<pre>";
$allFinalConsignmentData = array();

$allConsignmentData = array();
$selecedtCountry = array();
$selecedtHandling = array();
$selecedtCountryISO = array();
$selecedtHandlingCharges = array();

$errorMessageArray = array();
$jsonData = array();

$arrayHandlingDims = array();
//$arrayHandlingDims['STIPRCL24'] = array(45, 35, 20);
//$arrayHandlingDims['STIPRCLCI'] = array(120, 120, 90);
//$arrayHandlingDims['STIPRCLLL'] = array(120, 120, 90);
//$arrayHandlingDims['STIPRCLMP'] = array(45, 35, 20);
//$arrayHandlingDims['STIPRCL48'] = array(90, 90, 90);
//$arrayHandlingDims['STIPRCLIE'] = array(90, 90, 90);
$arrayHandlingDims = [
    'STIPRCNP24' => array(50, 35, 20),
    'STIPRCL24' => array(50, 35, 20),
    'STIPRCNP48' => array(90, 90, 90),
    'STIPRCL48' => array(90, 90, 90),
    'STIPRCNPCI' => array(120, 120, 90),
    'STIPRCLCI' => array(120, 120, 90),
    'STIPRCNPLL' => array(170, 120, 90),
    'STIPRCLLL' => array(170, 120, 90),
    'STIPRCNPIE' => array(90, 90, 90),
    'STIPRCLIE' => array(90, 90, 90)
];

$arrayHandlingDims['3'] = array(50, 80);
$arrayHandlingDims['17'] = array(90, 150);
$arrayHandlingDims['30'] = array(120, 170);
$arrayHandlingDims['LL'] = array(170, 250);

echo "<pre>";
$limitDaya = 15000;
$chargesAccountId = 4371;
$consignment = new ConsignmentFilter();
$dateCreatedWhere = "       "
        . " (c.date_created >= '" . DbAccess3::escape($fromDateCreated) . "' "
        . " AND c.date_created <= '" . DbAccess3::escape($toDateCreated) . "')"
        . " AND "
        . " u.user_account_id = $chargesAccountId "
        . " AND c.customized_service_id in (549, 550, 551, 552, 652, 653 ) "
        . " AND consignment_id in ( select distinct consignment_id from consignment_charges where account_id = $chargesAccountId and cost_type = 'customer' and (invoice_id is null or invoice_id <= 0 or invoice_id = ''))"
/*. " AND c.awb in ('JD0002210164278749')"*/
;


$consignment->addFilter($dateCreatedWhere, 'filter');
$selectedColumns = [];
$selectedColumns[] = "c.id";
$selectedColumns[] = "u.user_account_id";
$selectedColumns[] = "c.hawb";
$selectedColumns[] = "c.awb";
$selectedColumns[] = "c.address_line_1";
$selectedColumns[] = "c.address_line_2";
$selectedColumns[] = "c.address_line_3";
$selectedColumns[] = "c.postcode, c.city";
$selectedColumns[] = "c.service_id";
$selectedColumns[] = "s.name as service_name";
$selectedColumns[] = "s.code as service_code";
$selectedColumns[] = "(select name from services where id = customized_service_id limit 1) as product_name";
$selectedColumns[] = "(select code from services where id = customized_service_id limit 1) as product_code";
$selectedColumns[] = "c.country_id";
$selectedColumns[] = "c.remote_charges";
$selectedColumns[] = "pc.length as parcel_length";
$selectedColumns[] = "pc.height as parcel_height";
$selectedColumns[] = "pc.width as parcel_width";
$selectedColumns[] = "c.weight";
$selectedColumns[] = "c.charge_weight";

$selectedColumns[] = "c.shipment_status";
$colVolDem = "(if(c.vol_demonimator is null, 5000, c.vol_demonimator))";
$selectedColumns[] = "   $colVolDemas vol_demonimator";
$selectedColumns[] = "if(c.vol_weight is null or c.vol_weight<=0,(pc.length*pc.height*pc.width)/ $colVolDem, c.vol_weight ) as vol_weight";
$selectedColumns[] = " ((pc.length*pc.height*pc.width)/ 1000000)  as cubic_meter";

$notPricingDatalist = $consignment->getList(false, implode(", ", $selectedColumns), 5000);

if (count($notPricingDatalist) > 0) {
    foreach ($notPricingDatalist as $consignmentData) {

        $consignmentId = $consignmentData->getId();
        $userAccountId = $consignmentData->getUserAccountId();
        $addressLine1 = $consignmentData->getAddressLine1();
        $addressLine2 = $consignmentData->getAddressLine2();
        $addressLine3 = $consignmentData->getAddressLine3();
        $postcode = $consignmentData->getPostcode();
        $city = $consignmentData->getCity();
        $serviceId = $consignmentData->getServiceId();
        $serviceName = $consignmentData->getServiceName();
        $serviceCode = $consignmentData->getServiceCode();
        $productName = $consignmentData->getProductName();
        $productCode = $consignmentData->getProductCode();
        //$countryIsoCode = $consignmentData->getCountryIsoCode();
        $remoteCharges = $consignmentData->getRemoteCharges();
        $parcelLength = $consignmentData->getParcelLength();
        $parcelHeight = $consignmentData->getParcelHeight();
        $parcelWidth = $consignmentData->getParcelWidth();
        $weight = $consignmentData->getWeight();
        $charge_weight = $consignmentData->getChargeWeight();
        $volWeight = $consignmentData->getVolWeight();
        $volDemonimator = $consignmentData->getVolDemonimator();
        $cubicMeter = $consignmentData->getCubicMeter();
        $awb = $consignmentData->getAwb();
        $hawb = $consignmentData->getHawb();

        $extraCharges = 0;
        $extraChargesVol = 0;
        $extraChargesLength = 0;
        $extraChargesWeight = 0;
        $extraRemotePostCodeCharges = 0;
        $remoteBasicCharges = 0;

        $parcelDimsData = array($parcelLength, $parcelHeight, $parcelWidth);
        rsort($parcelDimsData);
        $maxValueFromArrayDim = max($parcelDimsData);
        $lengthWigthSum = $maxValueFromArrayDim + $parcelDimsData[1];
        $finalChargeableWeight = $weight;
        if ($charge_weight > $weight)
            $finalChargeableWeight = $charge_weight;
        //else if ($volWeight > $charge_weight)
        //    $finalChargeableWeight = $volWeight;

        $arrayDims = $arrayHandlingDims[$productCode];
        if ($finalChargeableWeight <= 3)
            $arrayDims = $arrayHandlingDims['3'];
        else if ($finalChargeableWeight <= 17)
            $arrayDims = $arrayHandlingDims['17'];
        else if ($finalChargeableWeight <= 30)
            $arrayDims = $arrayHandlingDims['30'];


        switch ($productCode) {
            case "STIPRCLMP":
                if ($finalChargeableWeight > 3)
                    $extraChargesWeight = ($extraChargesWeight <= 5 ? 5 : $extraChargesWeight);
                if ($parcelDimsData[0] > 50 || $lengthWigthSum > 80)
                    $extraChargesLength = ($extraChargesLength <= 5 ? 5 : $extraChargesLength);
                if ($cubicMeter > 0.0315)
                    $extraChargesVol = ($extraChargesVol <= 5 ? 5 : $extraChargesVol);
            case "STIPRCL24":
            case "STIPRCNP24":
            case "STIPRCL48":
            case "STIPRCNP48":
            case "STIPRCLIE":
            case "STIPRCNPIE":
            case "STIPRCLCI":
            case "STIPRCNPCI":

                if ($finalChargeableWeight <= 17 && !in_array($productCode, ["STIPRCLIE", "STIPRCNPIE", "STIPRCLCI", "STIPRCNPCI"])) {
                    if ($parcelDimsData[0] > 90 || $lengthWigthSum > 150)
                        $extraChargesLength = ($extraChargesLength <= 5 ? 5 : $extraChargesLength);
                    if ($cubicMeter > 0.113)
                        $extraChargesVol = ($extraChargesVol <= 5 ? 5 : $extraChargesVol);
                } else if ($finalChargeableWeight <= 30) {
                    if ($parcelDimsData[0] > 120 || $lengthWigthSum > 170)
                        $extraChargesLength = ($extraChargesLength <= 30 ? 30 : $extraChargesLength);
                    if ($cubicMeter > 0.23)
                        $extraChargesVol = ($extraChargesVol <= 30 ? 30 : $extraChargesVol);
                } else {
                    $extraChargesWeight = ($extraChargesWeight <= 30 ? 30 : $extraChargesWeight);
                }

                break;
            case "STIPRCLLL":
            case "STIPRCNPLL":
                $arrayDims = $arrayHandlingDims['LL'];
                if ($parcelDimsData[0] > 170 || $lengthWigthSum > 250)
                    $extraChargesLength = ($extraChargesLength <= 90 ? 90 : $extraChargesLength);
                if ($cubicMeter > 0.28)
                    $extraChargesVol = ($extraChargesVol <= 90 ? 90 : $extraChargesVol);
                if ($finalChargeableWeight > 30)
                    $extraChargesWeight = ($extraChargesWeight <= 90 ? 90 : $extraChargesWeight);
                break;
        }



///////////////////////////// BFPO Charges 
        $extraRemotePostCodeCharges = 0;

        $posCity = strpos($city, 'BFPO');
        $posAddress1 = strpos($addressLine1, 'BFPO');
        $posAddress2 = strpos($addressLine2, 'BFPO');
        $posAddress3 = strpos($addressLine3, 'BFPO');
        if ($posCity !== false || $posAddress1 !== false || $posAddress2 !== false || $posAddress3 !== false)
            $extraRemotePostCodeCharges = 12.00;

        //$remoteBasicCharges = 0.00;

        if (trim($remoteCharges) != '' && $remoteCharges > 0 && $extraRemotePostCodeCharges <= 0.00) {
            $queryBfpoResult = DbAccess3::runQuery(" select * from postcode_user_service_charges WHERE postcode_name = 'YODEL-BFPO' and replace(from_postcode, ' ', '') = '" . str_replace(' ', '', $postcode) . "'");
            if (mysqli_num_rows($queryBfpoResult) > 0)
                $extraRemotePostCodeCharges = 12.00;
            else {
                $extraRemotePostCodeCharges = 2.50;
            }
        }
///////////// Weight Check 
//        if ($finalChargeableWeight > 50)
//            $extraChargesWeight = ($extraChargesWeight <= 110 ? 110 : $extraChargesWeight);
//        else if ($finalChargeableWeight > 30)
//            $extraChargesWeight = ($extraChargesWeight <= 60 ? 60 : $extraChargesWeight);
///////////// Cubic Meter Check
//        if ($cubicMeter > 0.34)
//            $extraChargesVol = ($extraChargesVol <= 65 ? 65 : $extraChargesVol);
//        else if ($cubicMeter > 0.28)
//            $extraChargesVol = ($extraChargesVol <= 18 ? 18 : $extraChargesVol);
//        else if ($cubicMeter > 0.23)
//            $extraChargesVol = ($extraChargesVol <= 3 ? 3 : $extraChargesVol);
///////////// Dimss Check 
//        if (
//                $parcelDimsData[0] > $arrayDims[0] ||
//                $parcelDimsData[1] > $arrayDims[1] ||
//                $parcelDimsData[2] > $arrayDims[2]) {
//            $extraChargesLength = ($extraChargesLength <= 5 ? 5 : $extraChargesLength);
//        }
//
//        if ($maxValueFromArrayDim > $arrayDims[0] && $maxValueFromArrayDim <= 120) {
//            $extraChargesLength = ($extraChargesLength > 5) ? $extraChargesLength : 5;
//        } else if ($maxValueFromArrayDim > 120 && $maxValueFromArrayDim <= 184) {
//            $extraChargesLength = ($extraChargesLength > 7) ? $extraChargesLength : 7;
//        } else if ($maxValueFromArrayDim > 184 && $maxValueFromArrayDim <= 260) {
//            $extraChargesLength = ($extraChargesLength > 20) ? $extraChargesLength : 20;
//        } else if ($maxValueFromArrayDim > 260) {
//            $extraChargesLength = ($extraChargesLength > 110) ? $extraChargesLength : 110;
//        }
//        print_r($parcelDimsData);
        // REMOVE LABEL CHARGES 
        $deleteLabelCharges = "DELETE FROM `consignment_charges`
                                    WHERE 
                                            `account_id`>0 
                                        AND `account_id` = $userAccountId
                                        AND `consignment_id`> 0 
                                        AND `consignment_id` = $consignmentId
                                        AND `charge_type_id` = 27
                                        AND `cost_type` = 'customer'
                                        ";

        DbAccess3::runQuery($deleteLabelCharges);
        $charge_type_id = 3;
        if ($extraChargesVol > $extraChargesLength && $extraChargesVol > $extraChargesWeight) {
            $charge_type_id = 39;
            $extraCharges = $extraChargesVol;
        } else if ($extraChargesLength >= $extraChargesWeight && $extraChargesLength >= $extraChargesVol) {
            $charge_type_id = 38;
            $extraCharges = $extraChargesLength;
        } else if ($extraChargesWeight >= $extraChargesLength && $extraChargesWeight >= $extraChargesVol) {
            $charge_type_id = 40;
            $extraCharges = $extraChargesWeight;
        }

        if ($extraCharges <= 0 && $extraRemotePostCodeCharges <= 0 && $remoteBasicCharges <= 0)
            continue;
        echo $awb
        . "--"
        . $hawb
        . "--"
        . $serviceCode
        . "--"
        . $productCode
        . "--"
        . $extraCharges
        . "--"
        . $extraRemotePostCodeCharges
        . "--"
        . $remoteBasicCharges
        . "--"
        . "<br>";
        if ($remoteBasicCharges > 0) {
            $consignmentBasicRemoteChargesQuery = " SELECT * FROM 
                            consignment_charges 
                        WHERE
                            `account_id` = $userAccountId
                            AND charge_type_id  = 1    
                            AND `consignment_id` = $consignmentId    
                            AND cost_type = 'customer' ";
            $consignmentBasicRemoteChargesResult = DbAccess3::runQuery($consignmentBasicRemoteChargesQuery);
            if (mysqli_num_rows($consignmentBasicRemoteChargesResult) > 0) {
                while ($rowBRC = mysqli_fetch_array($consignmentBasicRemoteChargesResult)) {
                    $updateBRC = "UPDATE  `consignment_charges`
                                    SET 
                                        `cost` = $remoteBasicCharges,
                                        `cost_supplier_currency`= $remoteBasicCharges,
                                        `cost_company_currency`= $remoteBasicCharges,
                                        `description` = 'schedule charge'
                                    WHERE 
                                        id = " . $rowBRC['id'] . "";
                    DbAccess3::runQuery($updateBRC);
                }
            }
        }


        if ($extraCharges > 0) {
            $consignmentExChargesQuery = " SELECT * FROM 
                            consignment_charges 
                        WHERE
                            `account_id` = $userAccountId
                            AND charge_type_id  = $charge_type_id    
                            AND `consignment_id` = $consignmentId    
                            AND cost_type = 'customer' AND `description` = 'schedule charge' ";


            $consignmentExChargesResult = DbAccess3::runQuery($consignmentExChargesQuery);

            if (mysqli_num_rows($consignmentExChargesResult) > 0) {
                while ($rowEx = mysqli_fetch_array($consignmentExChargesResult)) {
                    if ($rowEx['cost'] < $extraCharges) {
                        $updateExtraCharges = "UPDATE  `consignment_charges`
                                    SET 
                                        `cost` = $extraCharges,
                                        `cost_supplier_currency`= $extraCharges,
                                        `cost_company_currency`= $extraCharges,
                                        `description` = 'schedule charge'
                                    WHERE 
                                        id = " . $rowEx['id'] . "";
                        DbAccess3::runQuery($updateExtraCharges);
                    }
                }
            } else {


                $insertExtraCharges = "INSERT INTO `consignment_charges`
                                    SET 
                                        `account_id` = $userAccountId,
                                        `consignment_id` = $consignmentId,
                                        `charge_type_id` = $charge_type_id,
                                        `cost_type` = 'customer',
                                        `cost` = $extraCharges,
                                        `cost_currency` = 'GBP',
                                        `cost_supplier_currency`= $extraCharges,
                                        `supplier_currency`= 'GBP',
                                        `cost_company_currency`= $extraCharges,
                                        `company_currency`= 'GBP',
                                        `description` = 'schedule charge',
                                        `added_by` = '2301' ";
                DbAccess3::runQuery($insertExtraCharges);
            }
        }


        // Check BFPO CHARGES 
        if ($extraRemotePostCodeCharges > 0) {
            $consignmentChargesQuery = " SELECT * FROM 
                            consignment_charges 
                        WHERE
                            `account_id` = $userAccountId
                            AND charge_type_id  = 8    
                            AND `consignment_id` = $consignmentId    
                            AND cost_type = 'customer' ";

            $consignmentChargesResult = DbAccess3::runQuery($consignmentChargesQuery);
            if (mysqli_num_rows($consignmentChargesResult) > 0) {
                $count = 1;
                while ($row = mysqli_fetch_array($consignmentChargesResult)) {
                    if ($row['cost'] <= 14) {
                        $updateExtraCharges = "UPDATE  `consignment_charges`
                                    SET 
                                        `cost` = $extraRemotePostCodeCharges,
                                        `cost_supplier_currency`= $extraRemotePostCodeCharges,
                                        `cost_company_currency`= $extraRemotePostCodeCharges,
                                        `description` = 'schedule charge'
                                    WHERE 
                                        id = " . $row['id'] . "";
                        DbAccess3::runQuery($updateExtraCharges);
                    }
                }
            } else {
                $insertExtraCharges = "INSERT INTO `consignment_charges`
                                    SET 
                                        `account_id` = $userAccountId,
                                        `consignment_id` = $consignmentId,
                                        `charge_type_id` = 8,
                                        `cost_type` = 'customer',
                                        `cost` = $extraRemotePostCodeCharges,
                                        `cost_currency` = 'GBP',
                                        `cost_supplier_currency`= $extraRemotePostCodeCharges,
                                        `supplier_currency`= 'GBP',
                                        `cost_company_currency`= $extraRemotePostCodeCharges,
                                        `company_currency`= 'GBP',
                                        `description` = 'schedule charge',
                                        `added_by` = '2301' ";
                DbAccess3::runQuery($insertExtraCharges);
            }
        }
        $fuelChargesValue = 0;
        $fuelChargesValueType = 'fixed';
        $consignmentFuelChargesTariffWiseQuery = "SELECT 
                            charge,charge_type
                        FROM
                            consignment_charges
                                INNER JOIN
                            tariffs ON consignment_charges.tariff_id = tariffs.id
                                INNER JOIN
                            tariff_additional_charges ON tariff_additional_charges.tariff_id = tariffs.id
                        WHERE
     
                            `account_id` = $userAccountId
                            AND charge_type_id  = 1    
                            AND `consignment_id` = $consignmentId    
                            AND cost_type = 'customer' ";

        $consignmentFuelChargesTariffWiseResult = DbAccess3::runQuery($consignmentFuelChargesTariffWiseQuery);
        
        if (mysqli_num_rows($consignmentFuelChargesTariffWiseResult) > 0) {
            $consignmentFuelChargesTariffWiseRow = mysqli_fetch_array($consignmentFuelChargesTariffWiseResult);
            
            if (trim($consignmentFuelChargesTariffWiseRow["charge_type"]) == 'percentage')
                $fuelChargesValue = $consignmentFuelChargesTariffWiseRow["charge"] / 100;
            else
                $fuelChargesValue = $consignmentFuelChargesTariffWiseRow["charge"];

            $fuelChargesValueType = $consignmentFuelChargesTariffWiseRow["charge_type"];
           
        } else {
            $consignmentFuelChargesTariffWiseQuery = "select charge, charge_type 
                    from user_account_service_charges where 
                            `user_account_id` = $userAccountId
                            AND consignment_charges_types_id  = 2    
                            AND `service_id` = $serviceId    
                             ";

            $consignmentFuelChargesTariffWiseResult = DbAccess3::runQuery($consignmentFuelChargesTariffWiseQuery);
            if (mysqli_num_rows($consignmentFuelChargesTariffWiseResult) > 0) {
                $consignmentFuelChargesTariffWiseRow = mysqli_fetch_array($consignmentFuelChargesTariffWiseResult);
                if (trim($consignmentFuelChargesTariffWiseRow["charge_type"]) == 'percentage')
                    $fuelChargesValue = $consignmentFuelChargesTariffWiseRow["charge"] / 100;
                else
                    $fuelChargesValue = $consignmentFuelChargesTariffWiseRow["charge"];

                $fuelChargesValueType = $consignmentFuelChargesTariffWiseRow["charge_type"];
            }
        }
//echo $fuelChargesValue.$fuelChargesValueType;
        if ($fuelChargesValue > 0) {
            if ($fuelChargesValueType == 'percentage')
                $consignmentFuelChargesQuery = " SELECT sum(cost)* $fuelChargesValue as fuel_charges FROM 
                            consignment_charges 
                        WHERE
                            `account_id` = $userAccountId
                            AND charge_type_id  <> 2    
                            AND `consignment_id` = $consignmentId    
                            AND cost_type = 'customer' ";
            else
                $consignmentFuelChargesQuery = " SELECT  $fuelChargesValue as fuel_charges FROM 
                            consignment_charges 
                        WHERE
                            `account_id` = $userAccountId
                            AND charge_type_id  <> 2    
                            AND `consignment_id` = $consignmentId    
                            AND cost_type = 'customer' ";
            $consignmentFuelChargesResult = DbAccess3::runQuery($consignmentFuelChargesQuery);
            $fuelChargesCalculate = 0;
            if (mysqli_num_rows($consignmentFuelChargesResult) > 0) {
                $fuelChargesCalculateRow = mysqli_fetch_array($consignmentFuelChargesResult);
                $fuelChargesCalculate = $fuelChargesCalculateRow["fuel_charges"];
            }
            $updateFuelCharges = "UPDATE  `consignment_charges`
                                    SET 
                                        `cost` = $fuelChargesCalculate,
                                        `cost_supplier_currency`= $fuelChargesCalculate,
                                        `cost_company_currency`= $fuelChargesCalculate,
                                        `description` = 'schedule charge'
                                    WHERE 
                                    `account_id` = $userAccountId
                                    AND charge_type_id  = 2    
                                    AND `consignment_id` = $consignmentId    
                                    AND cost_type = 'customer' ";
            DbAccess3::runQuery($updateFuelCharges);
        }
    }
}
mysqli_close($mysql_access);
?>