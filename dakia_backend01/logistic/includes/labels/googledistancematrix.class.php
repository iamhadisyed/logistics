<?php
class GoogleDistanceMatrix
{
	
	public static function getGoogleDistanceMatrix($origin, $destination=array(), $debug = 'false')
	{
            $destinationstring = implode("|", $destination);
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin."&destinations=".$destinationstring."&key=AIzaSyCDM4nwr48gdWBG0QTTiosoLkQMKAdzZrk"; // AIzaSyCDM4nwr48gdWBG0QTTiosoLkQMKAdzZrk
            $result = json_decode(file_get_contents(($url)));
            $status = $result->status;
            if($status == "OK")
            {
                $destinationAddress = $result->destination_addresses;
                $distanceData = array();
                $elements = $result->rows[0]->elements;
                foreach($elements as $key => $r){
                    $distanceData[$destinationAddress[$key]] = $r;
                }
                return $distanceData;
            }
            else
            {
                return $status;
            }
            
	}
        
         public static function getLatitudeLongitude($address="", $city="", $postcode="", $countryiso="")
        {
            $output = array();
            if($countryiso == ""){
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Please Provide Country ISO.";
                return $output;
            }
            $searchAddress = "";
            if($address != "")
                $searchAddress = $address. "+";
            if($city != "")
                $searchAddress .= $city. "+";
            if($postcode != "")
                $searchAddress .= $postcode. "+";
            
            
            $laturl = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($searchAddress.$countryiso)."&key=AIzaSyCDM4nwr48gdWBG0QTTiosoLkQMKAdzZrk";
            $result = json_decode(file_get_contents(($laturl)));
            $status = $result->status;
            if($status == "OK")
            {
                foreach($result as $r){
                    $lat = $r[0]->geometry->location->lat;
                    $lng = $r[0]->geometry->location->lng;
                    $output["STATUS"] = "SUCCESS";
                    $output["LAT"] = $lat;
                    $output["LNG"] = $lng;
                    return $output;
                }
                
            }
            else
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Unable to get Latitude and Longitude for address provided.";
            }
            return $output;
        }

}
?>