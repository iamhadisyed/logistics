<?php
include_classes([
	'currency.class',
	'currencyfilter.class',
]);
////////////////////////////////////////////////////
//
// Provides a set of utility functions for use
// throughout site.  These functions should be site
// independent, i.e. this file can be used on other sites
// without alteration.
//
////////////////////////////////////////////////////
function in_array_r($needle, $haystack, $strict = false) {
    //print_r($haystack);
    $found = "notfound";

    foreach ($haystack as $key => $value) {
        if ($value == $needle)
            $found = "found";
    }


    return $found;
}
/**
 * Provides a redirection to the page given.
 *
 * @param string $page
 */
function util_redirect($page) {
    ob_start();
    header("Location: " . $page);
    ob_end_flush();
    die();
}

/**
 * Performs simple formatting on XML
 */
function util_formatXml($xml) {
    $xml = str_replace("<", "\n<", $xml);
    $xml = str_replace("\n</", "</", $xml);
    $xml = str_replace("></", ">\n</", $xml);
    return $xml;
}

/**
 * Performs simple XML to HTML formatting
 */
function util_formatXmlAsHtml($xml) {
    $xml = str_replace("<", "<br />&lt;", $xml);
    $xml = str_replace(">", "&gt;", $xml);
    $xml = str_replace("&", "&amp;", $xml);
    $xml = str_replace("<br />&lt;/", "&lt;/", $xml);
    return $xml;
}

/**
 * Performs simple formatting on XML
 */
function util_showXml($xml, $rows = 20) {
    echo "<p><textarea rows=$rows style=\"width:700px\">" . util_formatXml($xml) . "</textarea></p>";
}

/**
 * Gets a value from the query string, returns blank if not passed
 *
 * @param string $requestKey
 */
function util_request($requestKey) {
    return (isset($_REQUEST[$requestKey]) ? strip_tags($_REQUEST[$requestKey]) : '');
}

/**
 * Gets a value from the get method
 *
 * @param string $requestKey
 */
function util_get($requestKey) {
    return (isset($_GET[$requestKey]) ? strip_tags($_GET[$requestKey]) : '');
}

function util_get_num($requestKey, $default = -1) {
    $val = util_get($requestKey);
    return (is_numeric($val) ? $val : $default);
}

/**
 * Gets a value from query string, and ensures it is a number.
 * Returns -1, if key not set or not a number.
 *
 * @param unknown_type $requestKey
 * @return unknown
 */
function util_request_num($requestKey, $default = -1) {
    $val = util_request($requestKey);
    return (is_numeric($val) ? $val : $default);
}

/**
 * Gets a value that has been posted to page.  Returns
 * blank if the post field does not exist.
 *
 * @param unknown_type $postKey
 */
function util_post($postKey, $default = "") {
    return (isset($_POST[$postKey]) ? trim(strip_tags($_POST[$postKey])) : $default);
}

function util_post_num($postKey, $default = -1) {
    $val = util_post($postKey);
    return (is_numeric($val) ? $val : $default);
}

/**
 * Format a currency - rounds it to 2 dp and formats.
 *
 * @param doubl $val
 * @param string currency symbol
 */
function util_money($val, $symbol = "&pound;") {
    // getting this strange bug: round(17.365, 2)  gives 17.36
    // only way, I've found round this is to force to large int and do calculation
    $val2 = (round(intval($val * 1000), -1)) / 1000;
    return $symbol . " " . number_format($val2, 2, ".", ",");
}

/**
 * Ensures given string is formatted for use in xml
 *
 * @param unknown_type $str
 */
function util_xmlEncode($str) {
    return str_replace("&", "&amp;", $str);
}

/**
 * Get sanatised post value array
 *
 */
function util_getPostArray() {
    $p = array();
    foreach ($_POST as $key => $val) {
        //echo "<br>Got $key = $val";
        if (is_string($val)) {
            $val = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $val);
            $val = preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', "", $val);
            $p[$key] = strip_tags($val);
        } else {
            $p[$key] = $val;
        }
    }
    return $p;
}

/**
 * Encode an array into json format.
 *
 * @param array $a
 * @return string Encoded array
 */
function util_jsonEncode($a = false) {
    if (is_null($a))
        return 'null';
    if ($a === false)
        return 'false';
    if ($a === true)
        return 'true';
    if (is_scalar($a)) {
        if (is_float($a)) {
            // Always use "." for floats.
            return floatval(str_replace(",", ".", strval($a)));
        }

        if (is_string($a)) {
            static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
            return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
        } else
            return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
        if (key($a) !== $i) {
            $isList = false;
            break;
        }
    }
    $result = array();
    if ($isList) {
        foreach ($a as $v)
            $result[] = util_jsonEncode($v);
        return '[' . join(',', $result) . ']';
    } else {
        foreach ($a as $k => $v)
            $result[] = util_jsonEncode($k) . ':' . util_jsonEncode($v);
        return '{' . join(',', $result) . '}';
    }
}

function p($msg) {
    util_echo($msg);
}

function util_echo($msg) {
    echo "<p>$msg</p>";
}

/* * *
 * Converts array values to display string, each value separated by given separator
 */

function util_formatArrayValues($theArray, $theSeparator = "<br />") {
    $result = "";
    foreach ($theArray as $val) {
        if ($result != "")
            $result .= $theSeparator;
        $result .= $val;
    }
    return $result;
}

function getClientIp() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function extractNumber($string) {
    preg_match_all('/([\d]+)/', $string, $match);

    return $match[0];
}
/*
function cleanData($str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    $str = preg_replace('/[\$,]/', '', $str);
    return $str;
}
*/
function xml2array($contents, $get_attributes = 1, $priority = 'tag') {

    if (!$contents)
        return array();

    if (!function_exists('xml_parser_create')) {

        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, @trim($contents), $xml_values);
    xml_parser_free($parser);

    if (!$xml_values)
        return; //Hmm...


//Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference
    //Go through the tags.
    $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
    foreach ($xml_values as $data) {
        unset($attributes, $value); //Remove existing values, or there will be trouble
        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data); //We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();

        if (isset($value)) {
            if ($priority == 'tag')
                $result = $value;
            else
                $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if (isset($attributes) and $get_attributes) {
            foreach ($attributes as $attr => $val) {
                if ($priority == 'tag')
                    $attributes_data[$attr] = $val;
                else
                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if ($type == "open") {//The starting of the tag '<tag>'
            $parent[$level - 1] = &$current;
            if (!is_array($current) or ( !in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if ($attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;

                $current = &$current[$tag];
            } else { //There was another element with the same tag name
                if (isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level] ++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag . '_' . $level] = 2;

                    if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = &$current[$tag][$last_item_index];
            }
        } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if (!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority == 'tag' and $attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
            } else { //If taken, put all things inside a list(array)
                if (isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                    if ($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level] ++;
                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $get_attributes) {
                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }

                        if ($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level] ++; //0 and 1 index is already taken
                }
            }
        } elseif ($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level - 1];
        }
    }
    return($xml_array);
}
/*
function htmlToArray($vartables) {


    $html = new simple_html_dom($vartables);
    $theData = array();

    if (trim($vartables) != '') {
        foreach ($html->find('table') as $onetable) {
            $countRows = 0;
            foreach ($onetable->find('tr') as $row) {
                if ($countRows == 0) {
                    $countRows++;
                    continue;
                }
                $rowData = array();

                foreach ($row->find('td') as $cell) {

                    $rowData[] = $cell->innertext;
                }

                $theData[] = $rowData;
            }
        }
    }
    return $theData;
}

function makeUTF8($str, $encoding = "") {
    $str = preg_replace('/[^(\x20-\x7F)]* /', '', $str);
    //$str = str_replace('&','&amp;', $str);
    $str = str_replace('<', '&lt;', $str);
    $str = str_replace('>', '&gt;', $str);
    $str = str_replace("'", "", $str);
    $str = str_replace('&', '', $str);

    if ($str !== "") {
        if (empty($encoding) && isUTF8($str))
            $encoding = "UTF-8";
        if (empty($encoding))
            $encoding = mb_detect_encoding($str, 'UTF-8, ISO-8859-1');
        if (empty($encoding))
            $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
        return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str, "UTF-8", $encoding);
    }
}

function isUTF8($str) {
    return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $str);
}
*/
function cleanTrackingNo($string) {
    return $string;
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
function changeConStatusByParcelStatus($parcelId, $conOtherCode = "") {
//    Consignment::STATUS_P
    if ($parcelId > 0) {
        $parcelObj = new Parcel($parcelId);
        $consignmentId = $parcelObj->getConsignmentId();
        $consignmentObj = new Consignment($consignmentId);
        //If status is passed by parameters
        if(!empty($conOtherCode)){
            if(isset(Consignment::$database_status_array[$conOtherCode])){
                $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[$conOtherCode]);
            }
            $consignmentObj->setShipmentStatus($conOtherCode);
        }else{
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addFieldFilter("consignment_id", $consignmentId);
            $parcelFilter->addGroupBy("parcel_status_code");
            $parcelResObj = $parcelFilter->getColumnList("parcel_status_code");
            if (count($parcelResObj) == 1) {
                $consignmentObj->setShipmentStatus($parcelResObj[0]->getParcelStatusCode());
                if(isset(Consignment::$database_status_array[$parcelResObj[0]->getParcelStatusCode()])){
                    $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[$parcelResObj[0]->getParcelStatusCode()]);
                }
            } else if(count($parcelResObj) > 0) {
                $parcelStauts = [];
                foreach ($parcelResObj as $parcelResArr) {
                    $parcelStauts[]= $parcelResArr->getParcelStatusCode();
                }
                $parcelStauts = array_unique($parcelStauts);
                //Handle partial Data
                if(in_array(Consignment::STATUS_DELIVERED, $parcelStauts)){
                   $consignmentObj->setShipmentStatus(Consignment::STATUS_PARTIAL_DELIVERED);
                   if(isset(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_DELIVERED])){
                        $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_DELIVERED]);
                    }
                }
//                else if(in_array(Consignment::STATUS_PARTIAL_RECEIVED, $parcelStauts)){
//                    $consignmentObj->setShipmentStatus(Consignment::STATUS_PARTIAL_RECEIVED);
//                    if(isset(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_RECEIVED])){
//                        $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_RECEIVED]);
//                    }
//                }
                else if(in_array(Consignment::STATUS_RECEIVED, $parcelStauts)){
                    if(count($parcelStauts) > 1){
                        $consignmentObj->setShipmentStatus(Consignment::STATUS_PARTIAL_RECEIVED);
                        if(isset(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_RECEIVED])){
                            $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_RECEIVED]);
                        }
                    }else{
                        $consignmentObj->setShipmentStatus(Consignment::STATUS_RECEIVED);
                        if(isset(Consignment::$database_status_array[Consignment::STATUS_RECEIVED])){
                            $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_RECEIVED]);
                        }
                    }
                    
                }
                else if(in_array(Consignment::STATUS_DISPATCHED, $parcelStauts)){
                    if(count($parcelStauts) > 1){
                        $consignmentObj->setShipmentStatus(Consignment::STATUS_PARTIAL_DISPATCHED);
                        if(isset(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_DISPATCHED])){
                            $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_PARTIAL_DISPATCHED]);
                        }
                    }else{
                        $consignmentObj->setShipmentStatus(Consignment::STATUS_DISPATCHED);
                        if(isset(Consignment::$database_status_array[Consignment::STATUS_DISPATCHED])){
                            $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_DISPATCHED]);
                        }
                    }
                    
                }
                else{
                    $consignmentObj->setShipmentStatus(Consignment::STATUS_LABEL_CREATED);
                    if(isset(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED])){
                        $consignmentObj->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);
                    }
                }
            }
        }
        if ($consignmentObj->getDateScanned() == '' || $consignmentObj->getDateScanned() == '0000-00-00 00:00:00')
            $consignmentObj->setDateScanned(time());
        $consignmentObj->save();
    }
}
function addDataIntoConsignmentRelabel($oldJson,$newJson,$oldTracking,$newTracking,$conId,$oldParcelsTrackingNoStr,$oldNewTrackingMapping){
        $sessionUser = SessionManager::getUser();
        $consignmentRelabel = new ConsignmentRelabel();
        //Compare old json with new data
        $saveJsonChangedColumn = [];
        //Decode json
        if(!empty($oldJson)){
            $jsonOldDecode = json_decode($oldJson,true);
            if(empty($newJson)){
                $consignmentRelabel->setOldConsignmentData($oldJson);
            }else{
                $jsonNewDecode = json_decode($newJson,true);
                $saveJsonChangedColumn = array_diff($jsonOldDecode,$jsonNewDecode);
                $saveJsonChangedColumn = json_encode($saveJsonChangedColumn);
                $consignmentRelabel->setOldConsignmentData($saveJsonChangedColumn);
            }
        }
        
        $consignmentRelabel->setConsignmentId($conId);
        $consignmentRelabel->setOldTrackingNo($oldTracking);
        $consignmentRelabel->setNewTrackingNo($newTracking);
        $consignmentRelabel->setDateCreated(time());
        $consignmentRelabel->setUserid($sessionUser->getId());
        $consignmentRelabel->setOldParcelTrackingNo($oldParcelsTrackingNoStr);
        $consignmentRelabel->setOldNewTrackingMapping(json_encode($oldNewTrackingMapping));
        $consignmentRelabel->save();
    }
	/*
	 * Generates a slug from string
	 * @return string: slug
	 * @author: Abdul Rahman
	*/
	function util_slugify($text, $toLower = true)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);

		// trim
		$text = trim($text, '-');

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// lowercase
		if($toLower){
			$text = strtolower($text);
		}

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
		{
			return 'n-a';
		}

		return $text;
	}

function getLoggedInUserChildWarehouse() {
    $sessionUser = SessionManager::getUser();
    $userAccountArry = CustomerAccount::accountSubAccount($sessionUser->getUserAccountId(), 0, true);
    return WarehouseFilter::getWarehouseFromUserAccount($userAccountArry);
}
function getMawbIdFromMawbNumber($mawbNumber){
        $mawbId = "";
        if(!empty($mawbNumber)){
            $mawbFilter = new MawbFilter();
            $mawbFilter->addFieldFilter("mawb_number", $mawbNumber);
            $mawbObj = $mawbFilter->getList("id");
            if(count($mawbObj) > 0)
                $mawbId = $mawbObj[0]->getId();
        }
        return $mawbId;
}     
function checkBalance($userAccountId,$returnUserAccountId=false,$singleAccount = ''){
    $accountPaymentArray = [];
    $paymentArray = [];
    $paymentObj = "";
    $unPaidInvoicesSum = 0;
    $totalNotInvoicedConsignmentCharges = 0;
    if(empty($singleAccount)){
        //  Check if parrent has zero balance
        $userAccountArrTmp = [];
        $userAccountArrTmp = CustomerAccount::accountParentAccount($userAccountId,false);
        if(count($userAccountArrTmp) > 0){
            foreach($userAccountArrTmp as $userAccountObj){
                if($userAccountObj->getParentid() > 0)
                    $userAccountArr[] = $userAccountObj->getId();
            }
        }
    }else {
        $userAccountObj = new UserAccountFilter();
        $userAccountObj->addFieldFilter('id', $userAccountId);
        $dataReturn =$userAccountObj->getList();
        $userAccountArr = [];
        $userAccountArr[] = $dataReturn[0]->getId();
    }
    foreach ($userAccountArr as $userAccountId) {
        $userAccount = new CustomerAccount($userAccountId);
		$paymentArray[] = $userAccount->getAccountBalance();
		$accountPaymentArray[$userAccountId] = $userAccount->getAccountBalance();
        /*$paymentObj = PaymentsHistory::getAccountBalance($userAccountId);

        $totalNotInvoicedConsignmentChargesObj = ConsignmentCharges::getTotalNotInvoicedConsignmentChargesByAccountId($userAccountId);
        if($totalNotInvoicedConsignmentChargesObj > 0)
            $totalNotInvoicedConsignmentCharges = $totalNotInvoicedConsignmentChargesObj[0]->getCost();
        else
            $totalNotInvoicedConsignmentCharges = 0;
        
//        $unPaidInvoicesSumObj = Invoices::getTotalUnPaidAmountByAccountId($userAccountId);
//        if(count($unPaidInvoicesSumObj) > 0)
//            $unPaidInvoicesSum = $unPaidInvoicesSumObj[0]->getTotalAmount();
//        else
//            $unPaidInvoicesSum = 0;
        
        if(count($paymentObj) > 0){
            //      Check if user account is prepaid
            if($userAccount->getIsPrepaid() == 1){
                $paymentArray[] = $paymentObj[0]->getCredit();
                $accountPaymentArray[$paymentObj[0]->getAccountId()] = $paymentObj[0]->getCredit();
            }else{
//                $paymentArray[] = (floatval($userAccount->getCreditLimit()) + floatval($paymentObj[0]->getCredit())) - (floatval($totalNotInvoicedConsignmentCharges)+ floatval($unPaidInvoicesSum));
//                
//                $accountPaymentArray[$paymentObj[0]->getAccountId()] = (floatval($userAccount->getCreditLimit()) + floatval($paymentObj[0]->getCredit())) - (floatval($totalNotInvoicedConsignmentCharges)+ floatval($unPaidInvoicesSum));
                $paymentArray[] = (floatval($userAccount->getCreditLimit()) + floatval($paymentObj[0]->getCredit())) - (floatval($totalNotInvoicedConsignmentCharges));
                
                $accountPaymentArray[$paymentObj[0]->getAccountId()] = (floatval($userAccount->getCreditLimit()) + floatval($paymentObj[0]->getCredit())) - (floatval($totalNotInvoicedConsignmentCharges));
            }
        }else{
            if($userAccount->getIsPrepaid() == 0){
//                $paymentArray[] = floatval($userAccount->getCreditLimit()) - (floatval($totalNotInvoicedConsignmentCharges)+ floatval($unPaidInvoicesSum));
//                $accountPaymentArray[$userAccountId] = floatval($userAccount->getCreditLimit()) - (floatval($totalNotInvoicedConsignmentCharges)+ floatval($unPaidInvoicesSum));
                $paymentArray[] = floatval($userAccount->getCreditLimit()) - (floatval($totalNotInvoicedConsignmentCharges));
                $accountPaymentArray[$userAccountId] = floatval($userAccount->getCreditLimit()) - (floatval($totalNotInvoicedConsignmentCharges));
            }else{
                $paymentArray[] = 0;
                $accountPaymentArray[$userAccountId] = 0;
            }
        }*/
    }
    if($returnUserAccountId)
        return $accountPaymentArray;
    else
        return $paymentArray;
}

function getBalance($userAccountId){
    $balance = [];
    $totalAmount = 0;
    
    $userAccount = new CustomerAccount($userAccountId);
    $currency = trim($userAccount->getBillingCurrency()) != ''?$userAccount->getBillingCurrency(): 'GBP';
    $paymentHistory = new PaymentsHistoryFilter();
    $paymentHistory->where(['account_id' => $userAccountId]);
    $paymentHistoryObj = $paymentHistory->getList();
    if(count($paymentHistoryObj) > 0) {
        foreach($paymentHistoryObj as $paymentHistory) {
            if($paymentHistory->getIsCompleted() == "yes") {
                $totalAmount = $totalAmount + $paymentHistory->getCredit();
                $totalAmount = $totalAmount - $paymentHistory->getDebit();
            }
            if($paymentHistory->getAmountCurrencyId() > 0) {
                $currencyObj = new Currency($paymentHistory->getUserCurrencyId());
                $currency = $currencyObj->getRightsymbol();
            }
        }
    }
    //Check if user account is prepaid
    if($userAccount->getIsPrepaid() == 1){
        $balance['balance'] = $totalAmount . " " . $currency;
		$balance["_balance"] = $totalAmount;
		$balance["_balance_currency"] = $currency;
    }else{
        $totalNotInvoicedConsignmentChargesObj = ConsignmentCharges::getTotalNotInvoicedConsignmentChargesByAccountId($userAccountId);
        if($totalNotInvoicedConsignmentChargesObj > 0)
            $totalNotInvoicedConsignmentCharges = $totalNotInvoicedConsignmentChargesObj[0]->getCost();
        else
            $totalNotInvoicedConsignmentCharges = 0;
        
//        $unPaidInvoicesSumObj = Invoices::getTotalUnPaidAmountByAccountId($userAccountId);
//        if(count($unPaidInvoicesSumObj) > 0)
//            $unPaidInvoicesSum = $unPaidInvoicesSumObj[0]->getTotalAmount();
//        else
//            $unPaidInvoicesSum = 0;
        
//        $balance["balance"] = (floatval($userAccount->getCreditLimit()) + floatval($totalAmount)) - (floatval($totalNotInvoicedConsignmentCharges)+ floatval($unPaidInvoicesSum));
//        $balance["outstanding"] = (floatval($totalNotInvoicedConsignmentCharges)+ floatval($unPaidInvoicesSum));
        $balance["balance"] = ((floatval($userAccount->getCreditLimit()) + floatval($totalAmount)) - (floatval($totalNotInvoicedConsignmentCharges))) . " " . $currency;
		$balance["_balance"] = ((floatval($userAccount->getCreditLimit()) + floatval($totalAmount)) - (floatval($totalNotInvoicedConsignmentCharges)));
		$balance["_balance_currency"] = $currency;
        $balance["outstanding"] = (floatval($totalNotInvoicedConsignmentCharges)) . " " . $currency;
    }
    return $balance;
}
/*
 * Pass consignment id
 * Return Maximum column value from that consignment's parcel (lenght, widht , height)
 */
function getMaxDim($consignmentId) {
    $res = array();
    $maxDim = 0;
    if(count($consignmentId) > 0){
        $sql = "SELECT MAX(GREATEST(p.`length`,p.`width`,p.`height`)) AS max_dim FROM parcel p WHERE p.`consignment_id` = '".$consignmentId."'";
        $retunObj = DbAccess3::runQuery($sql);
        while($obj = mysqli_fetch_object($retunObj)) {
            $res[] = $obj;
        }
        if(count($res) > 0){
            $maxDim = $res[0]->max_dim;
        }
    }
    return $maxDim;
}
function checkOwnContract($userAccountId,$serviceId){
    $return = false;
    $userAccountArr = [];
    if($userAccountId > 0 && $serviceId > 0){
        $userFilter = new UserFilter();
        $userFilter->addAccountIdFilter($userAccountId);
        $accountUserObj = $userFilter->getColumnList("id");
        if(count($accountUserObj) > 0){
            foreach ($accountUserObj as $accountUser) {
                $userAccountArr[] = $accountUser->getId();
            }
        }
        $userServicesRoutingFilter = new UserServicesRoutingFilter();
        $userServicesRoutingFilter->addFieldFilter("user_account_id", $userAccountId);
        $userServicesRoutingFilter->addFieldFilter("is_agreed", "1");
        $userServicesRoutingFilter->addFieldFilter("status", "1");
        $userServicesRoutingFilter->addFilterIn("added_by", $userAccountArr);
        $userServicesRoutingObj = $userServicesRoutingFilter->getList();
        if(count($userServicesRoutingObj) > 0){
            $return = true;
        }
        
    }
    return $return;
}
function cleanCsvCall($column,$type="text"){
    $returnData = $column;
    $sepChar = array("\r\n","\n\r", "\r", "\n", ",");
    if(!empty($column)){
        $returnData = str_replace($sepChar, " ",$column);
        if($type == "int"){
            $returnData = "=\"".$returnData."\"";
        }
    }
    return $returnData;
}

function removeBomUtf8($s){
  if(substr($s,0,3)==chr(hexdec('EF')).chr(hexdec('BB')).chr(hexdec('BF'))){
       return substr($s,3);
   }else{
       return $s;
   }
}

function formatNumber($number,$afterDecimalNumber = 2) {
    return number_format((float)$number,$afterDecimalNumber,'.','');
}

function formatDate($date, $format="d-m-Y"){
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

function formatDateTime($date, $format="d-m-Y H:i:s"){
    $datetime = new DateTime($date);
    return $datetime->format($format);
}
function saveConsignmentChargeableWeight($consignmentId){
    $sessionUser = SessionManager::getUser();
    $parcelWeight = 0;
    $volWeight = 0;
    if($consignmentId > 0){
        $consignmentObj = new Consignment($consignmentId);
        $oldConData = $consignmentObj;
        $serviceId = $consignmentObj->getServiceId();
        $service = new Services($serviceId);
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("    consignment_id", $consignmentId);
        $parcelListObj = $parcelFilter->getList();
        foreach ($parcelListObj as $parcelListArr) {
            $parcelWeight += $parcelListArr->getWeight();
            if ($parcelListArr->getWidth() > 0 && $parcelListArr->getLength() > 0 && $parcelListArr->getHeight() > 0) {
                $parcelWidht = $parcelListArr->getWidth();
                $parcelLength = $parcelListArr->getLength();
                $parcelHeight = $parcelListArr->getHeight();
                if(!empty($vol_denominator) && $vol_denominator > 0){
                    $volWeight += ($parcelLength * $parcelWidht * $parcelHeight) / $vol_denominator;
                }
            }
        }
        if($consignmentObj->getIsDeadWeightChargable() || $service->getValidationType() == "mail"){
            $consignmentObj->setChargeWeight($parcelWeight);
        }else if($service->getValidationType() == "courier"){
            // Check if carrier is courier then calculate volumetric weight and dead weight keep greater into chargable
            if(!Consignment::checkConsignmentInvoiced($consignmentId)){
                $vol_denominator = $consignmentObj->getVolDemonimator();
                $chargeableWeight = $parcelWeight;
                if($volWeight > $parcelWeight){
                    $chargeableWeight = $volWeight;
                }
                $consignmentObj->setChargeWeight($chargeableWeight);
            }
        }
        //Update the consignment log data
        $oldConData = serialize($oldConData);
        $consignmentObj->save();
        $newConData = serialize($consignmentObj);
        $consignmentLog = new ConsignmentLog();
        $consignmentLog->createlog($sessionUser->getUserName() . ' has updated consignemnt chargeable', $sessionUser->getId(), 'USER',  $sessionUser->getId(), $oldConData, $newConData);
    }
}
function saveConsignmentChargeableWeightApi($parcelId, $weight){
    $sessionUser = SessionManager::getUser();
    // Update parcel weight
    $parcel = new Parcel($parcelId);
    $parcel->setWeight($weight);
    $parcelOldData = $parcel;
    $oldParcelData = serialize($parcelOldData);
    $parcel->save();
    $newParcelData = serialize($parcel);
    $parcelLog = new ParcelLog();
    $parcelLog->createlog($sessionUser->getId(), '', $parcelId, 'Parcel', "Parcel weight is changed", $parcelOldData, $newParcelData);
    $parcelObj = new Parcel($parcelId);
    $consignmentId = $parcel->getConsignmentId();

    $parcelWeight = 0;
    $volWeight = 0;
    if($consignmentId > 0){
        $consignment = new Consignment($consignmentId);
        $consignmentObj = new Consignment($consignmentId);
        $oldConData = $consignmentObj;
        $serviceId = $consignmentObj->getServiceId();
        $service = new Services($serviceId);
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("    consignment_id", $consignmentId);
        $parcelListObj = $parcelFilter->getList();
        foreach ($parcelListObj as $parcelListArr) {
            $parcelWeight += $parcelListArr->getWeight();
            if ($parcelListArr->getWidth() > 0 && $parcelListArr->getLength() > 0 && $parcelListArr->getHeight() > 0) {
                $parcelWidht = $parcelListArr->getWidth();
                $parcelLength = $parcelListArr->getLength();
                $parcelHeight = $parcelListArr->getHeight();
                if(!empty($vol_denominator) && $vol_denominator > 0){
                    $volWeight += ($parcelLength * $parcelWidht * $parcelHeight) / $vol_denominator;
                }
            }
        }
        if($consignmentObj->getIsDeadWeightChargable() || $service->getValidationType() == "mail"){
            $consignmentObj->setChargeWeight($parcelWeight);
        }else if($service->getValidationType() == "courier"){
            // Check if carrier is courier then calculate volumetric weight and dead weight keep greater into chargable
            if(!Consignment::checkConsignmentInvoiced($consignmentId)){
                $vol_denominator = $consignmentObj->getVolDemonimator();
                $chargeableWeight = $parcelWeight;
                if($volWeight > $parcelWeight){
                    $chargeableWeight = $volWeight;
                }
                $consignmentObj->setChargeWeight($chargeableWeight);
            }
        }
        //Update the consignment log data
        $oldConData = serialize($oldConData);
        $consignmentObj->save();
        $newConData = serialize($consignment);
        $consignmentLog = new ConsignmentLog();
        $consignmentLog->createlog($sessionUser->getUserName() . ' has updated consignemnt chargeable', $sessionUser->getId(), 'USER',  $sessionUser->getId(), $oldConData, $newConData);
        // consignment
        $allParcelWeight = 0;
        $allParcelWeight = $weight;
        //Check if other parcels have the same consignment id and user already added other weights
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("    consignment_id", $consignmentId);
//        $parcelFilter->addFieldNotFilter("id", $parcelId);
        $parcelListObj = $parcelFilter->getList();
        foreach ($parcelListObj as $parcelListArr) {
            $allParcelWeight += $parcelListArr->getWeight();
        }
        //Update the update weight and vol weight of consignment
        $oldWeight = $consignment->getWeight();
        $oldConData = $consignment;
        $consignment->setWeight($allParcelWeight);
        $consignment->setUpdateWeight($oldWeight);
        $oldConData = serialize($oldConData);
        $consignment->save();
        // Cal tariff
        $outTariff = Consignment::consignment_label_pricing($consignment);

        $newConData = serialize($consignment);
        $consignmentLog = new ConsignmentLog();
        $consignmentLog->createlog($sessionUser->getUserName() . ' has updated consignemnt weight', $sessionUser->getId(), 'USER',  $sessionUser->getId(), $oldConData, $newConData);
    }
}
function array_key_exists_r($needle, $haystack){
    $result = array_key_exists($needle, $haystack);
    if ($result) return $haystack[$needle];
    foreach ($haystack as $v) {
        if (is_array($v)) {
            $result = array_key_exists_r($needle, $v);
        }
        if ($result) return $v[$needle];
    }
    return $haystack[$needle];
}
function mail_type_options() {
    $optionArr = [
        'all' => "ALL",
        'letter' => "Letter Format (P)",
        'boxable' => "Large Letters/Flats Format/Boxable (G)",
        'non-boxable' => "Packet/Non Boxable (E)"
    ];
    return $optionArr;
}

function mail_options() {
    $optionArr = [
        'all' => "ALL",
        'commercial' => "COMMERCIAL",
        'freight_to_post' => "FREIGHT TO POST"
    ];
    return $optionArr;
}

function validatePostCode($country, $postcode){
	$regex = "";
	$format = "";
	$error = "";
	$country = strtoupper($country);
	switch ($country) {
		case "CANADA":
		case "CAN":
		case "CA":
		case "38":
			$regex = "/^(?!.*[DFIOQUdfioqu])[A-VXYa-vxy][0-9][A-Za-z] ?[0-9][A-Za-z][0-9]$/";
			$format = "ANA NAN";
			$error = "Postcode should be 6 or 7 characters with English letters and digits, such A1B2C3 or A1B 2C3";
			break;
		case "UNITED KINGDOM":
		case "GBR":
		case "GB":
		case "225":
			$regex = "/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9][A-Za-z]?))))\s?[0-9][A-Za-z]{2})$/";
			$format = "A(A)N(A/N)NAA (A[A]N[A/N] NAA)";
			$error = "Postcode should be 5 or 6 characters with English letters and digits, such UB33NB or UB3 3NB";
			break;
		case "UNITED STATES":
		case "USA":
		case "US":
		case "226":
			$regex = "/^\d{5}(-\d{4})?$/";
			$format = "NNNNN (optionally NNNNN-NNNN)";
			$error = "Postcode should be 5 digits or 5-4 digits, such 99999 or 99999-9999";
			break;
		case "GERMANY":
		case "DEU":
		case "DE":
		case "80":
			$regex = "/^[0-9]{5}$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "RUSSIA":
		case "RUS":
		case "RU":
		case "177":
		case "271":
			$regex = "/^(\d{6})$/";
			$format = "NNNNNN";
			$error = "Postcode should be 6 digits, such 999999";
			break;
		case "FRANCE":
		case "FRA":
		case "FR":
		case "73":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "ITALY":
		case "ITA":
		case "IT":
		case "105":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "SPAIN":
		case "ESP":
		case "ES":
		case "199":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "UKRAINE":
		case "UKR":
		case "UA":
		case "223":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "POLAND":
		case "POL":
		case "PL":
		case "171":
			$regex = "/^\d{2}[- ]{0,1}\d{3}$/";
			$format = "NNNNN (NN-NNN)";
			$error = "Postcode should be 5 digits or 2-3 digits, such 99999 or 99-999";
			break;
		case "ROMANIA":
		case "ROU":
		case "RO":
		case "176":
			$regex = "/^(\d{6})$/";
			$format = "NNNNN";
			$error = "Postcode should be 6 digits, such 999999";
			break;
		case "NETHERLANDS":
		case "NLD":
		case "NL":
		case "150":
			$regex = "/^(?:[Nn][Ll]-)?(\d{4})\s*([A-Za-z]{2})$/";
			$format = "NNNN AA";
			$error = "Postcode should be 6 or 7 characters with English letters and digits, such 9999AB or 9999 AB";
			break;
		case "BELGIUM":
		case "BEL":
		case "BE":
		case "21":
			$regex = "/^(?:(?:[1-9])(?:\d{3}))$/";
			$format = "NNNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "CZECH REPUBLIC":
		case "CZECHIA":
		case "CZE":
		case "CZ":
		case "57":
			$regex = "/^([Cc][Zz]){0,1}[- ]{0,1}\d{3}([ ]){0,1}\d{1}(\d{0,1})$/";
			$format = "NNNNN (CZ NNN NN)";
			$error = "Postcode should be 5 or 4 digits, such 99999 or 9999 or CZ 999 99 or CZ-999 99";
			break;
		case "GREECE":
		case "GRC":
		case "GR":
		case "83":
			$regex = "/^([Gg][Rr]){0,1}[- ]{0,1}\d{3}([ ]){0,1}\d{2}$/";
			$format = "NNN NN (GR NNN NN)";
			$error = "Postcode should be 5 digits, such 99999 or 999 99 or GR 999 99 or GR-999 99";
			break;
		case "PORTUGAL":
		case "PRT":
		case "PT":
		case "172":
			$regex = "/^\d{4}[- ]{0,1}\d{3}$/";
			$format = "NNNN-NNN (NNNN NNN) (NNNNNNN)";
			$error = "Postcode should be 7 digits, such 9999-999 or 9999 999 or 9999999";
			break;
		case "SWEDEN":
		case "SWE":
		case "SE":
		case "205":
			$regex = "/^([Ss]){0,1}[- ]{0,1}\d{3}([ ]){0,1}\d{2}$/";
			$format = "NNN NNN (NNNNN)";
			$error = "Postcode should be 5 digits, such 999 99 or 99999 or S 99 99 or S-999 99";
			break;
		case "HUNGARY":
		case "HUN":
		case "HU":
		case "97":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "BELARUS":
		case "BLR":
		case "BY":
		case "20":
			$regex = "/^(\d{6})$/";
			$format = "NNNNNN";
			$error = "Postcode should be 6 digits, such 999999";
			break;
		case "AUSTRIA":
		case "AUT":
		case "AT":
		case "14":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "SERBIA":
		case "SRB":
		case "RS":
		case "189":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "SWITZERLAND":
		case "CHE":
		case "CH":
		case "206":
			$regex = "/^(\d{4})$/";
			$format = "NNNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "BULGARIA":
		case "BGR":
		case "BG":
		case "33":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "DENMARK":
		case "DNK":
		case "DK":
		case "58":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "FINLAND":
		case "FIN":
		case "FI":
		case "72":
			$regex = "/^([Ff][Ii][- ]{0,1}){0,1}\d{5}$/";
			$format = "NNNNN (FI NNNNN)";
			$error = "Postcode should be 5 digits, such 99999 or FI 99999 or FI-99999 or FI99999";
			break;
		case "SLOVAKIA":
		case "SVK":
		case "SK":
		case "193":
			$regex = "/^\d{3}([ ]){0,1}\d{2}$/";
			$format = "NNNNN (NNN NN)";
			$error = "Postcode should be 5 digits, such 99999 or 999-99";
			break;
		case "NORWAY":
		case "NOR":
		case "NO":
		case "160":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "IRELAND":
		case "IRL":
		case "IE":
		case "103":
			$regex = "";
			$format = "";
			$error = "";
			break;
		case "CROATIA":
		case "HRV":
		case "HR":
		case "54":
			$regex = "/^([Hh][Rr][- ]{0,1}){0,1}\d{5}$/";
			$format = "NNNNN (HR 99999)";
			$error = "Postcode should be 5 digits, such 99999 or HR 99999 or HR-99999 or HR99999";
			break;
		case "MOLDOVA":
		case "MDA":
		case "MD":
		case "140":
			$regex = "/^([Mm][Dd][- ]{0,1}){0,1}\d{4}$/";
			$format = "MDNNNN (NNNN)";
			$error = "Postcode should be MD+4 digits or only 4 digits, such MD9999 or MD-9999 or MD 9999 or 9999";
			break;
		case "BOSNIA AND HERZEGOVINA":
		case "BOSNIA-HERZEGOVINA":
		case "BIH":
		case "BA":
		case "27":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "ALBANIA":
		case "ALB":
		case "AL":
		case "2":
			$regex = "/^\d{4}$/";
			$format = "NNNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "LITHUANIA":
		case "LTU":
		case "LT":
		case "123":
			$regex = "/^([Ll][Tt][- ]{0,1}){0,1}\d{5}$/";
			$format = "LT-NNNNN (NNNNN)";
			$error = "Postcode should be 5 dogits or LT+5 digits seperated by \"-\" or space, such 99999 or LT99999 or LT-99999 or LT 99999";
			break;
		case "NORTH MACEDONIA":
		case "MACEDONIA":
		case "MKD":
		case "MK":
		case "126":
			$regex = "/^(\d{4})$/";
			$format = "NNNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "SLOVENIA":
		case "SVN":
		case "SI":
		case "194":
			$regex = "/^([Ss][Ii][- ]{0,1}){0,1}\d{4}$/";
			$format = "SI NNNN (SI-NNNN)(SINNNN)(NNNN)";
			$error = "Postcode should be SI+4 digits seperated by \"-\" or space or only 4 digits, such SI9999or SI-9999 or SI 9999 or 9999";
			break;
		case "LATVIA":
		case "LVA":
		case "LV":
		case "117":
			$regex = "/^([Ll][Vv][- ]{0,1}){0,1}\d{4}$/";
			$format = "LV NNNN (LV-NNNN)(LVNNNN)";
			$error = "Postcode should be 4 gigits or LV+4 digits seperated by \"-\" or space, such 9999 or LV9999 or LV 9999 or LV-9999";
			break;
		case "ESTONIA":
		case "EST":
		case "EE":
		case "67":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "MONTENEGRO":
		case "MNE":
		case "ME":
		case "268":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "LUXEMBOURG":
		case "LUX":
		case "LU":
		case "124":
			$regex = "/^([Ll][- ]{0,1}){0,1}\d{4}$/";
			$format = "NNNN (L-NNNN)";
			$error = "Postcode should be 4 digits or L+4 digits, such 9999 or L-9999 or L 9999";
			break;
		case "MALTA":
		case "MLT":
		case "MT":
		case "132":
			$regex = "/^[A-Za-z]{3}\s{0,1}\d{4}$/";
			$format = "AAANNNN (AAA NNNN)";
			$error = "Postcode should be 7 or 8 characters with English letters and digits, such ABC1234 or ABC1234";
			break;
		case "ICELAND":
		case "ISL":
		case "IS":
		case "98":
			$regex = "/^(\d{3})$/";
			$format = "NNN";
			$error = "Postcode should be 3 digits, such 999";
			break;
		case "ANDORRA":
		case "AND":
		case "AD":
		case "5":
			$regex = "/^([Aa][Dd][- ]{0,1}){0,1}\d{3}$/";
			$format = "NNN (AD NNN)";
			$error = "Postcode should be 3 digits or AD+3digits, such 999 or AD 999 or AD-999";
			break;
		case "SAN MARINO":
		case "SMR":
		case "SM":
		case "185":
			$regex = "/^(4789\d)$/";
			$format = "4789N";
			$error = "Postcode should be 4789+1 digit, such 47896";
			break;
		case "VATICAN CITY STATE":
		case "HOLY SEE":
		case "VAT":
		case "VA":
		case "94":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "ISRAEL":
		case "ISR":
		case "IL":
		case "104":
			$regex = "/^\b\d{5}(\d{2})?$/";
			$format = "NNNNNNN, NNNNN";
			$error = "Postcode should be 5 digits or 7 digits, such 99999 or 9999999";
			break;
		case "AUSTRALIA":
		case "AUS":
		case "AU":
		case "13":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "CYPRUS":
		case "CYP":
		case "CY":
		case "56":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "FAROE ISLANDS":
		case "FRO":
		case "FO":
		case "70":
			$regex = "/^([Ff][Oo][- ]{0,1}){0,1}\d{3}$/";
			$format = "NNN (FO NNN)";
			$error = "Postcode should be 3 digits or FO+3 digits, such 999 or FO999 or FO 999 or FO-999";
			break;
		case "TURKEY":
		case "TUR":
		case "TR":
		case "218":
			$regex = "/^(\d{5})$/";
			$format = "NNNNN";
			$error = "Postcode should be 5 digits, such 99999";
			break;
		case "GEORGIA":
		case "GEO":
		case "GE":
		case "79":
			$regex = "/^(\d{4})$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits, such 9999";
			break;
		case "ARGENTINA":
		case "ARG":
		case "AR":
		case "10":
			$regex = "/^\d{4}|[A-Za-z]\d{4}[a-zA-Z]{3}$/";
			$format = "1974-1998 NNNN; From 1999 ANNNNAAA";
			$error = "Postcode should be 4 digits such 9999 or  8 characters such A4321BCD";
			break;
		case "LIECHTENSTEIN":
		case "LIE":
		case "LI":
		case "122":
			$regex = "/^\d{4}$/";
			$format = "NNNN";
			$error = "Postcode should be 4 digits such 9999";
			break;
		case "MOROCCO":
		case "MCO":
		case "MC":
		case "144":
			//$regex = "980NN";
                        $regex = "/^\A[1-9]\d{4,4}$/";
			//$format = "/^980\d{2}$/";
                        $format = "NNNNN";
			$error = "Postcode should be 5 digits such 98012";
			break;
		default:
			$regex = "";
			$format = "";
			$error = "";
			break;
	}
	if(!empty($postcode) && $regex != ""){
		if (!preg_match($regex, $postcode)) {
			return $error;
		}
	}
	return true;
}

function get_zones() {
    $zones = [
        'all' => 'All',
        'asia' => 'Asia',
        'europe' => 'Europe',
        'africa' => 'Africa',
        'oceania' => 'Oceania',
        'north_america' => 'North America',
        'south_america' => 'South America',
        'antarctica' => 'Antarctica',
        'eu27' => 'EU28',
        'middle_east' => 'Middle East',
        'rest_of_the_world' => 'Rest of the World'
    ];
    return $zones;
}

function get_tariff_formulas(){
	$tariffFormulas = [
		'' => 'Only Consider KG Cost',
		'Q * ITMCHR' => 'No of Items * Item Cost',
		'Q * CHRG' => 'No of Items * KG Cost',
		'Q * ( ITMCHR + REG ) + W * CHRG' => '(No. of Items * (Item Cost + Register Cost ) )+ (Items Weight * KG Cost) (Register Post)',
		'( Q * ITMCHR ) + ( W * CHRG )' => '(No. of Items * Item Cost ) + ( Items Weight * KG Cost ) (UnTrack Register Post)',
		'Q * ( ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG' => 'No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items	Weight - Current range From Weight) / 0.45 )) + KG Cost (DHL ECO)',
		'Q * ( ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG' => 'No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items	Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL EXP)',
		'( ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG' => '(Item Cost * ROUND_NEXT_NUMER(( Items Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL EXP)',
		'( ITMCHR * ceil ( W - FRMW )/0.5) + CHRG' => '(Item Cost * ROUND_NEXT_NUMER( Items	Weight - Current range From Weight)/0.5) + KG Cost (DHL	EXP)',
		'( ITMCHR * ceil (( W - FRMW ) / 0.45 )) + CHRG' => 'No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items	Weight - Current range From Weight ) / 0.45 )) + KG Cost (DHL ECO SHIPMENT)',
		'( ITMCHR * ceil (( W - FRMW ) / 0.5 )) + CHRG' => 'No. of Items * ( Item Cost * ROUND_NEXT_NUMER(( Items	Weight - Current range From Weight ) / 0.5 )) + KG Cost (DHL EXP SHIPMENT))',
		'Q * ( ITMCHR * ceil ( W - FRMW )) + CHRG' => 'No. of Items * ( Item Cost * ROUND_NEXT_NUMER( Items	Weight - Current range From Weight ) ) + KG Cost (STANDARD)',
		'ceil ( W ) * CHRG' => 'ROUND_NEXT_NUMER(Items Weight) * KG Cost',
		' W  * CHRG' => 'Items Weight * KG Cost'
	];
	//'TOW * CHRG' => 'Current range To Weight * KG Cost'
	return $tariffFormulas;
}


function generateRandomString($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}