<?
require_once("../includes/settings/config.inc.php");

if (isset($_POST['action']) && trim($_POST['action']) == 'GET_API_LABEL') {
	
	$servicecode = $_POST['servicecode'];
	$contact = $_POST['contact'];
	$company = $_POST['company'];
	$txt_des_add_line_1 = $_POST['txt_des_add_line_1'];
	$txt_des_add_line_2 = $_POST['txt_des_add_line_2'];
	$txt_des_add_line_3 = $_POST['txt_des_add_line_3'];
	$txt_des_city = $_POST['txt_des_city'];
	$txt_des_telephone = $_POST['txt_des_telephone'];
	$txt_des_postcode = $_POST['txt_des_postcode'];
	$dest_country = $_POST['dest_country'];
	$txt_des_value = $_POST['txt_des_value'];
	$txt_des_descr = $_POST['txt_des_descr'];
	$txt_des_notes = $_POST['txt_des_notes'];
	$weight = $_POST['weight'];
	$length = $_POST['length'];
	$width = $_POST['width'];
	$height = $_POST['height'];

	$userSession	=	SessionManager::getUser();
	$user_account = $userSession->getUserAccount();
	$user_name = $userSession->getUserName();
	$password = $userSession->getUserPass();
	$number_pieces = 1;
	$currency = "GBP";
	
	 
	 
	$consignmentinformation = "||".$company."||".$contact."||".$txt_des_add_line_1."||".$txt_des_add_line_2."||".$txt_des_add_line_3."||".$txt_des_city."||" .$dest_country . "||" . $txt_des_postcode . 
	"||". $txt_des_telephone. "||".$number_pieces."||".$weight."||".$txt_des_descr."||".$txt_des_value."||".$currency."||||||".$servicecode."||".$user_account."||".$user_name. "||" .$password."||0||||||||||||".$weight."||||||||||||||";
	
	
	$client = new SoapClient(null, 
							 array(
							'location' => "http://accounts.oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
                            'uri'      => "http://accounts.oneworldexpress.co.uk/remote/main/index.php"));	

	$results =  $client->__soapCall('getLabels', array('consignmentinformation' => $consignmentinformation));
	
	print_r($results);
	exit;
}
?>