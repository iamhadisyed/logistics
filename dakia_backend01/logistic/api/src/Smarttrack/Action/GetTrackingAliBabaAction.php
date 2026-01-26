<?php

namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use UserServicesRouting;
use SoapClient;
use CountryFilter;


class GetTrackingAliBabaAction {

    protected $logger;
    protected $renderer;
    protected $authorMapper;
    protected $userServiceRoutingMapper;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        //$trackingId = $request->getAttribute('tracking_id');
        $trackingData = $request->getParams();
        $mailNo = $trackingData['mailNo'];
        $sign = $trackingData['sign'];
        $cpCode = $trackingData['cpCode'];
        $lang = $trackingData['lang'];

        $this->logger->info("Getting tracking for", ['tracking_id' => $mailNo]);
        if (!isset($mailNo) || is_null($mailNo) || empty($mailNo)) {
            $problem = new ApiProblem(
                'Could not find user', 'https://www.smarttrack.co', 404
            );
            throw new ProblemException($problem);
        }
        $aliBabaTrackingMap = [
            111 => "GTMS_DELIVERING",
            112 => "SC_INBOUND_FAILURE",
            113 => "SC_SIGN_IN_SUCCESS",
            114 => "PU_SIGN_IN_SUCCESS",
            115 => "SC_INBOUND_FAILURE",
            116 => "SC_INBOUND_FAILURE",
            117 => "CC_EX_SUCCESS",
            118 => "SC_INBOUND_FAILURE",
            119 => "SC_INBOUND_FAILURE",
            120 => "SC_INBOUND_FAILURE",
            121 => "GTMS_SIGNED",
            122 => "GTMS_SIGNED",
            123 => "GTMS_SIGNED",
            124 => "SC_INBOUND_FAILURE",
            125 => "GTMS_SIGN_FAILURE",
            126 => "GTMS_DELIVERING",
            127 => "GMT_DEL_FAILURE",
            128 => "SC_INBOUND_FAILURE",
            129 => "GTMS_ACCEPT",
            130 => "SC_INBOUND_FAILURE",
            131 => "PU_PICKUP_FAILURE",
            133 => "PU_PICKUP_SUCCESS",
            134 => "Partially Collected",
            135 => "SC_INBOUND_FAILURE",
            136 => "SC_INBOUND_FAILURE",
            137 => "GTMS_ACCEPT",
            138 => "RETURNED",
            140 => "SC_SIGN_IN_SUCCESS",
            141 => "SC_SIGN_IN_SUCCESS",
            142 => "SC_INBOUND_FAILURE",
            143 => "RETURNED",
            144 => "SC_SIGN_IN_SUCCESS",
            145 => "GTMS_DO_ARRIVE",
            146 => "SC_SIGN_IN_SUCCESS",
            147 => "GTMS_SIGNED",
            148 => "PU_SIGN_IN_SUCCESS"
        ];
        $trackingData = ApiFunctions::getTracking($mailNo);
        $status = "true";
        $statusCode = "S01";
        $message = "";
        if(isset($trackingData['message']))
        {
            $message = $trackingData['message'];
        }		
        
        if(isset($trackingData['status']) && $trackingData['status'] == "error")
        {
            /*$client = new SoapClient(null, 
            array(
                    'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
                    'uri'      => "https://oneworldexpress.co.uk/remote/main/index.php"));   

            $results =  $client->__soapCall('getTracking', array('consignmentinformation' => $mailNo.'||json'));
            $trackingData = json_decode($results);
            $message = $trackingData->Tracking->Status;
            */
            /*$client = new SoapClient(null, 
            array(
                    'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
                    'uri'      => "https://oneworldexpress.co.uk/remote/main/index.php"));   

            $results =  $client->__soapCall('getTracking', array('consignmentinformation' => $mailNo.'||json'));
            $trackingData = json_decode($results);*/
            //echo '<pre>';
           //print_r($trackingData); die;
            $message = "not found" ; //$trackingData->Tracking->Status;
            

            if(trim($message) == "not found")
            {
                $status = "false";
                $statusCode = "C01";
                $message = "Mailno doesn’t exist";

                $returnData = '<response>
                               <mailNo>'.$mailNo.'</mailNo>
                               <cpcode>'.$cpCode.'</cpcode >
                               <success>'.$status.'</success>
                               <code>'.$statusCode.'</code>
                               <message>'.$message.'</message>';
                               $returnData .= '</response>';
                return $returnData;
            }
            else
            {
                $returnData = '<response>
                               <mailNo>'.$mailNo.'</mailNo>
                               <cpcode>'.$cpCode.'</cpcode >
                               <success>'.$status.'</success>
                               <code>'.$statusCode.'</code>
                               <message>Tracking Found</message>';
                if(!empty($trackingData->Tracking) && count($trackingData->Tracking) > 0)
                {
                    $destinationCountry = "";
                    $originCountry = "";

                    if(!empty($trackingData->Tracking))
                    {
                        $originCountry = $trackingData->Tracking->OriginCountry;
                        $destinationCountry = $trackingData->Tracking->DestinationCountry;

                        $countryFilter = new CountryFilter();
                        $countryFilter->addIsoFilter($destinationCountry);
                        $destCountryList = $countryFilter->getColumnList("name");

                        if(count($destCountryList) > 0)
                        {
                            $destCountry = $destCountryList[0];
                            $destCountryName = $destCountry->getName();
                        }

                        $countryFilter = new CountryFilter();
                        $countryFilter->addIsoFilter($originCountry);
                        $originCountryList = $countryFilter->getColumnList("name");

                        if(count($originCountryList) > 0)
                        {
                            $originCountry = $originCountryList[0];
                            $originCountryName = $originCountry->getName();
                        }
                    }
                    $trackingDataEvent = "";
                    if(!empty($trackingData->Tracking->History))
                    {
                        $ocData = [];
                        $dcData = [];
                       
                        foreach ($trackingData->Tracking->History as $dateEventData)
                        {
                            if($dateEventData->TrackPoint == 'Manifest Generate')
                            {
                                $dateEventData->TrackPoint = $originCountryName;
                                $code = 'PU_PICKUP_SUCCESS';
                            }
                            
                            if($dateEventData->TrackPoint == $originCountryName)
                            {
                                $ocData[] = $dateEventData;
                            }
                            else
                            {
                                $dcData[] = $dateEventData;
                            }
                        }
                        
                        //total 16 
                        $codeArray = array('SC_SIGN_IN_SUCCESS','SC_OUTBOUND_SUCCESS',
                                           'SC_HO_OUT_SUCCESS','LH_POST_COLLECTION','CC_EX_SUCCESS','GTMS_ACCEPT','GTMS_SC_ARRIVE','LH_DEPART',
                                           'LH_ARRIVE','GTMS_DELIVERING','GTMS_DO_ARRIVE',
                                           'GTMS_RE_DELIVERING','PU_SIGN_IN_SUCCESS','SC_INBOUND_SUCCESS');
                        $index = 0;
                        if(!empty($ocData))
                        {
                            $trackingDataEvent .= '<group>
                                                    <type>OC</type>';
                            foreach($ocData as $ocTrack)
                            {
                                $eventContent = "";
                                if($ocTrack->EventContent == '')
                                {
                                    $eventContent = $ocTrack->Other;
                                }
                                else
                                {
                                    $eventContent = $ocTrack->EventContent;
                                }
                                
				if (strpos(strtolower($eventContent), 'delivered') !== false) 
                                {
                                    if($eventContent != 'Parcel to be delivered by courier')
                                    {
                                        $code = "GTMS_SIGNED";
                                    }
                                }
                                if ($code == '')
                                {
                                    $code = $codeArray[$index];
                                    $index++;
                                }
                                
                                if($ocTrack->Other == '')
                                {
                                    $description = $ocTrack->TrackPoint .', '.$eventContent;                                 
                                }
                                else
                                {
                                    $description =  $ocTrack->TrackPoint .', '. $eventContent.', '.$ocTrack->Other;
                                }
                                
                                
                                $trackingDataEvent .= '<track>
                                                        <time>'.date("Y-m-d H:i:s", strtotime($ocTrack->DateTime)).'</time>
                                                        <country>'.$originCountryName.'</country> 
                                                        <city>'.$originCountryName.'</city>
                                                        <facilityName>'.$ocTrack->Other.'</facilityName>
                                                        <timeZone>+8</timeZone>
                                                        <desc>'.$description.'</desc>
                                                        <actionCode>'.$code.'</actionCode>
                                                     </track>';
                                $code = '';
                            }
                            $trackingDataEvent .= '</group>';
                        }
                        if(!empty($dcData))
                        {
                            $trackingDataEvent .= '<group>
                                                    <type>DC</type>';
                            foreach($dcData as $dcTrack)
                            {
                                $eventContent = "";
                                $statusoneworld = $trackingData->Tracking->Status;
                                
                                if ($code == '')
                                {
                                    $code = $codeArray[$index];
                                    $index++;
                                }
                                
                                if($dcTrack->EventContent == '')
                                {
                                    $eventContent = $dcTrack->Other;
                                }
                                else
                                {
                                    $eventContent = $dcTrack->EventContent;
                                }
                                
                                if (strpos(strtolower($eventContent), 'delivered') !== false) 
                                {
                                    if($eventContent != 'Parcel to be delivered by courier')
                                    {
                                        $code = "GTMS_SIGNED";
                                    }
                                }
                                
                                if($ocTrack->Other == '')
                                {
                                    $description = $dcTrack->TrackPoint .', '.$eventContent;                                 
                                }
                                else
                                {
                                    $description =  $dcTrack->TrackPoint .', '. $eventContent.', '.$dcTrack->Other;
                                }
                                
                                $trackingDataEvent .= '<track>
                                                    <time>'.date("Y-m-d H:i:s", strtotime($dcTrack->DateTime)).'</time>
                                                    <country>'.$destCountryName.'</country> 
                                                    <city>'.$destCountryName.'</city>
                                                    <facilityName>'.$dcTrack->Other.'</facilityName>
                                                    <timeZone>+8</timeZone>
                                                    <desc>'.$description.'</desc>
                                                    <actionCode>'.$code.'</actionCode>
                                                 </track>';
                            $code = '';
                            }
                            $trackingDataEvent .= '</group>';
                        }
                    }
                    $returnData .=    '<tracesElement>
                                       <destinationCountry>'.$destCountryName.'</destinationCountry>
                                       '.$trackingDataEvent.'
                                     </tracesElement>';
                }
                $returnData .= '</response>';
                echo $returnData;
                die;
            }
        }
		else
		{
                    $codeArray = array('PU_PICKUP_SUCCESS','PU_SIGN_IN_SUCCESS','SC_INBOUND_SUCCESS','SC_SIGN_IN_SUCCESS','SC_OUTBOUND_SUCCESS',
                                     'SC_HO_OUT_SUCCESS','LH_POST_COLLECTION','CC_EX_SUCCESS','GTMS_ACCEPT','GTMS_SC_ARRIVE','LH_DEPART',
                                     'LH_ARRIVE','GTMS_DO_ARRIVE','GTMS_DELIVERING','GTMS_RE_DELIVERING', 'CC_EX_START', 'RT_INBOUND');
                        
			$returnData = '<response>
                        <mailNo>'.$mailNo.'</mailNo>
                        <cpcode>'.$cpCode.'</cpcode >
                        <success>'.$status.'</success>
                        <code>'.$statusCode.'</code>
                        <message>'.$message.'</message>';
        
			if(!empty($trackingData['tracking']) && count($trackingData['tracking']) > 0){
				$destinationCountry = "";
				$originCountry = "";
				if(!empty($trackingData['tracking']['shipment_detail'])){
					$destinationCountry = $trackingData['tracking']['shipment_detail']['destination_country'];
					$originCountry = $trackingData['tracking']['shipment_detail']['origin_country'];
				}
				$trackingDataEvent = "";
				if(!empty($trackingData['tracking']['tracking_events'])){
					$ocData = [];
					$dcData = [];
					foreach ($trackingData['tracking']['tracking_events'] as $dateEvent => $dateEventData) {
						foreach ($dateEventData as $eventData) {
							if($eventData['tracking_country'] == $originCountry){
								$ocData[] = $eventData;
							}else{
								$dcData[] = $eventData;
							}
						}
					}
                                        
                                        $ocData = array_reverse($ocData);
                                        $dcData = array_reverse($dcData);
                                        
					if(!empty($ocData)){
						$trackingDataEvent .= '<group>
                                                                       <type>OC</type>';
                                                $index = 0 ;
                                                foreach($ocData as $ocTrack)
                                                {
                                                        $code = $codeArray[$index];
                                                        $index++;
                                                        $trackingDataEvent .= '<track>
                                                                                <time>'.$ocTrack['date_time'].'</time>
                                                                                <country>'.$originCountry.'</country>
                                                                                <city>'.$originCountry.'</city>
                                                                                <facilityName>'.$ocTrack['track_point'].'</facilityName>
                                                                                <timeZone>+8</timeZone>
                                                                                <desc>'.$ocTrack['carrier_desc'].'</desc>';
                                                        $trackingDataEvent .= '<actionCode>'.$code.'</actionCode>
													</track>';                                                        
						}
						$trackingDataEvent .= '</group>';
					}
					if(!empty($dcData)){
						$trackingDataEvent .= '<group>
												<type>DC</type>';
						foreach($dcData as $dcTrack){
                                                        if($dcTrack['status_code_id'] == 121)
                                                        {
                                                            $code = 'GTMS_SIGNED';
                                                        }                                                        
                                                        
                                                        else
                                                        {
                                                            $code = $codeArray[$index];
                                                            $index++;
                                                        }
							$trackingDataEvent .= '<track>
                                                                                <time>'.$dcTrack['date_time'].'</time>
                                                                                <country>'.$destinationCountry.'</country>
                                                                                <city>'.$destinationCountry.'</city>
                                                                                <facilityName>'.$dcTrack['track_point'].'</facilityName>
                                                                                <timeZone>+8</timeZone>
                                                                                <desc>'.$dcTrack['carrier_desc'].'</desc>
                                                                                <actionCode>'.$code.'</actionCode>
                                                                             </track>';
						}
						$trackingDataEvent .= '</group>';
					}
				}
				$returnData .=    '<tracesElement>
								   <destinationCountry>'.$destinationCountry.'</destinationCountry>
								   '.$trackingDataEvent.'
								 </tracesElement>';
			}
			$returnData .= '</response>';
			echo $returnData;
			die;
			
		}
    }
}
