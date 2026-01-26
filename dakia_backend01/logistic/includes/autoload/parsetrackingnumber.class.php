<?php

class ParseTrackingNumber {

    public static function Parse($trackingNumber) {
        if(substr( $trackingNumber, 0, 3 ) == "JJD"){
            $trackingNumber = str_replace('JJD', 'JD', strtoupper($trackingNumber));
        }
        $track_arr = str_split($trackingNumber);
        if ($track_arr[0] == '%') { //DPD germany ignore first 8 and last 7 characters
            $isPilot = substr($trackingNumber, 8, 4);
            if ($isPilot == "0621")
                $trackingNumber = substr($trackingNumber, 12, -6);
            else
                $trackingNumber = substr($trackingNumber, 8, 22 - 8);
        }
        $isGls = substr($trackingNumber, 0, 3);
        if ($isGls == "456" && strlen($trackingNumber) == 12) {
            $trackingNumber = substr($trackingNumber, 0, -1);
        } else if ($isGls == "410" && strlen($trackingNumber) == 12) {
            $trackingNumber = substr($trackingNumber, 0, -1);
        }
        
        $isOc = substr($trackingNumber, 0, 3);
        if ($isOc == "420" && strlen($trackingNumber) == 34) {
            $trackingNumber = substr($trackingNumber, 8);
        }
//		 elseif(strlen($trackingNumber) == 23)
//		 {
//			 $isUKMailtrackingNumber =  substr($trackingNumber,6, 14);
//			 $conFilter = new ConsignmentFilter();
//			 $conFilter->addawbFilter_bag($isUKMailtrackingNumber);
//			 $list = $conFilter->getColumnList("awb, handling");
//			 if(count($list) > 0)
//			 {
//				$consignment = $list[0]; 
//				$handling = $consignment->getHandling();
//				
//				if($handling == 12)
//				{
//					$trackingNumber = $isUKMailtrackingNumber;
//				}
//			 }	
//		 }


        return $trackingNumber;
    }

}
