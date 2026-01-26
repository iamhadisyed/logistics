<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'services.class',
    'servicefilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'country.class',
    'countryfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    
    
    ]);


ini_set('max_execution_time', '-1'); 

$carrierIdArray =  array(66, 187, 198, 154,  206, 121, 193,147, 164, 222, 159,199, 174, 186);//array(188); 

if (count($carrierIdArray) > 0) {
    foreach ($carrierIdArray as $carrierId) {
        
        $sqlquery = 
            " SELECT c.service_id, c.agent_id
        FROM
            consignment c
                LEFT JOIN
            services s ON c.service_id = s.id
        WHERE
            s.carrier_id = '" . $carrierId . "'
            AND s.pre_advise = 'Y'
                AND c.send_courier_data = '0'
                AND awb != ''
               GROUP BY c.service_id, c.agent_id LIMIT 50";

echo "<br />";
        $agentServiceArray = array();
        $consignmentServiceAgent = Consignment::getConsignmentListFromSql($sqlquery);
        
        
        if (count($consignmentServiceAgent) > 0) {
            foreach ($consignmentServiceAgent as $agentData) {
                $agentServiceArray['services'][] = $agentData->getServiceId();
                $agentServiceArray['agent'][] = $agentData->getAgentId();
            }
            if (!empty($agentServiceArray['services'])) {
                $serviceid = "";
                foreach ($agentServiceArray['services'] as $key => $service_id) {
                    
                    $serviceid = trim($service_id);
                    $agentid = trim($agentServiceArray['agent'][$key]);

                    $className = '';
                    $serivceAgentMapping = new ServiceAgentMappingDataFilter();
                    $serivceAgentMapping->addFilter(" serviceid = '" . $serviceid . "' and agentid = '" . $agentid . "'");
                    $serviceAgentMappintRecordSet = $serivceAgentMapping->getList();
                    if (count($serviceAgentMappintRecordSet) > 0) {
                        foreach ($serviceAgentMappintRecordSet as $agentData) {
                            $className = $agentData->getClassFileName();
                        }
                    }
                    
                    if (trim($className) == '') {
                        $serivces = new Services($serviceid);
                        $className = trim($serivces->getLabelClassName());
                    }
                    echo $className;
                    $file = strtolower(__DIR__ . "/../../includes/labels/$className.class.php");
                    if (file_exists($file)) {
                        try {
                            require_once $file;
                            $sql = "SELECT 
                            c.awb
                        FROM
                            consignment c
                        WHERE
                                c.service_id = '" . $serviceid . "'
                                AND c.agent_id = '" . $agentid . "'
                                AND c.send_courier_data = '0'
                                AND awb != ''
                                AND date_created >= '2019-05-09'
                                AND c.shipment_status not in ('" . Consignment::STATUS_INVALID . "', '" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_CANCELLED . "') limit 1000";
                            $consignmentObj = Consignment::getConsignmentListFromSql($sql);
                            
							
                            $trackingNumberArray = array();
                            if (count($consignmentObj) > 0) {
                                foreach ($consignmentObj as $consignment) {
                                    $trackingNumberArray[] = $consignment->getAwb();
                                }
                                print_r($trackingNumberArray);
                                $sendData = new $className();
                                $results = $sendData->sendData($trackingNumberArray);
                            }
                        } catch (Exception $e) {
                            $result["STATUS"] = "ERROR";
                            $result["MESSAGE"] = $e->getMessage();
                        }
                    } else {
                        $results['STATUS'] = 'ERROR';
                        $results['MESSAGE'] = "Service provider[ " . $className . " ] is not defined. Please contact system administrator";
                    }
                    if ($results['STATUS'] == "ERROR") {
                        // print_r($results);
                        mail("itsupport@oneworldexpress.com", "ERROR IN SENDING DATA FOR " . $className, $results['MESSAGE']);
                    } else {
                        // mail("mruga@oneworldexpress.com","SEND DATA SUCCESSFULLY FOR" . $className, count($consignmentObj));
                    }
                }
            }
        }
    }
}

