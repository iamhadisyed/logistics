<?php 
require_once("../includes/settings/config.inc.php");
 if(isset($_POST['Funcajax']) && $_POST['Funcajax'] == 'get_vinculum_extClientId') {
	 $companyname = $_POST["companyname"];
	 $telephone = $_POST["telephone"];
	 $email = $_POST["email"];
	 $password = $_POST["password"];
	 $country = $_POST["country"];
	 $return_address = $_POST["return_address"];
	 
	$userInformation = array();
	$userInformation["emailId"] = $email;
	$userInformation["phoneNo"] = $telephone;
	$userInformation["companyName"] = $companyname;
	$userInformation["password"] = $password;
	$userInformation["address"] = $return_address;
	$userInformation["city"] = $country;
	$userInformation["state"] = "1";
	$userInformation["country"] = "107";
	$userInformation["currency"] = "1";
	$userInformation["pinNo"] = "72072";
	
	//print_r($userInformation);
        
	$handle   = curl_init();
	curl_setopt($handle, CURLOPT_URL, "https://myaccount.vinculumgroup.com/api/customercreate/index.php");
	curl_setopt($handle, CURLOPT_HTTPHEADER, array('tokenid:A0IfmF57%2BDbI1Xuio41Q1d0Na7r8fJ2R'));
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_POSTFIELDS, $userInformation);
	 $response = curl_exec($handle);
	 if (empty($response)) {
		throw new SoapFault('CURL error: '.curl_error($handle),curl_errno($handle));
	 }
	 curl_close($handle);
		 //Get the new array
	 $end_array = text_to_array($response);
	
//	print_r($end_array);
//	echo $end_array['Status'];
//	exit;
	 //Output the array!
	if(trim(strtolower($end_array['Status'])) == "success")
	{
		echo "SUCCESS||".trim($end_array['extClientId']);
	}
	else
	{
		echo "ERROR||".trim($end_array['Message']);
	}
	exit;	 
 }
 
  function text_to_array($str) {

        //Initialize arrays
        $keys = array();
        $values = array();
        $output = array();

        //Is it an array?
        if( substr($str, 0, 5) == 'Array' ) {

            //Let's parse it (hopefully it won't clash)
            $array_contents = substr($str, 7, -2);
            $array_contents = str_replace(array('[', ']', '=>'), array('#!#', '#?#', ''), $array_contents);
            $array_fields = explode("#!#", $array_contents);

            //For each array-field, we need to explode on the delimiters I've set and make it look funny.
            for($i = 0; $i < count($array_fields); $i++ ) {

                //First run is glitched, so let's pass on that one.
                if( $i != 0 ) {

                    $bits = explode('#?#', $array_fields[$i]);
                    if( $bits[0] != '' ) $output[$bits[0]] = $bits[1];

                }
            }

            //Return the output.
            return $output;

        } else {

            //Duh, not an array.
            echo 'The given parameter is not an array.';
            return null;
        }

    }

?>