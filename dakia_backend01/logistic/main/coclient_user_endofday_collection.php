<?php
// get settings
require_once("../includes/settings/config.inc.php");
@session_start;

class Page extends BasePage {

    private $prealerfilter = NULL;
    private $prealert_list = NULL;
    private $mawbno = "";
    private $flightno = "";
    private $etd = "";
    private $eta = "";

    protected function init() {


        // user must be CLIENT
        SessionManager::checkUserAccess(User::PRIVILEGE_IMPORT);
        t_on(); // turn on trace for this page



        if (isset($this->form_vars["funcAction"]) &&
                (trim($this->form_vars["funcAction"]) == 'GET_COLLECTION' ||
                trim($this->form_vars["funcAction"]) == 'GET_PICKUP')) {


            $id = $this->form_vars['id'];



            //$manifestIdArray = json_decode($id, true);
            $manifestIdArray = explode(",", $id);

            //print_r($manifestIdArray);

            if (!is_array($manifestIdArray)) {
                //echo "Id : " . $id;
                $manifest = new Manifest(trim($id));
            } else {

                $strManifestId = implode(", ", $manifestIdArray);
                $manifestFilter = new ManifestDataFilter();
                $manifestFilter->addIdFilterIn($strManifestId);
                $list = $manifestFilter->getList("id");
                $manifest = $list[0];
            }

            //echo "<pre>";
            //print_r($list);
            //mail("kazim@oneworldexpress.com", "manifestid", implode(", ", $someArray));			
            //echo "<fkjsdlfjalksdjfl;sdjfsdfkjasl;dfjkl;sadjflksdjfl;sjdpre>";
            //print_r($id);
            //die;
            //echo json_decode($id);


            $output = array();

            if ($manifest->getId() > 0) {
                $output['response'] = 'SUCCESS';

                if (date("Y-m-d G:i", $manifest->getCollectionDate()) != '1970-01-01 0:00')
                    $collectionDate = date("Y-m-d G:i", $manifest->getCollectionDate());
                if (date("Y-m-d G:i", $manifest->getCollectionDateTo()) != '1970-01-01 0:00')
                    $collectionDateTo = date("Y-m-d G:i", $manifest->getCollectionDateTo());
                if (date("Y-m-d G:i", $manifest->getPickupDate()) != '1970-01-01 0:00')
                    $pickupDate = date("Y-m-d G:i", $manifest->getPickupDate());

                $driverName = $manifest->getNameOfDriver();
                $licenceNumber = $manifest->getLicenceNumber();


                ////////////////////////// COLLECTION - PARAMETERS /////////////////////////
                //echo $collectionDate;



                if ($collectionDate != '1970-01-01 1:00')
                    $output['collection_date'] = $collectionDate;
                else
                    $output['collection_date'] = '';

                if ($collectionDateTo != '1970-01-01 1:00')
                    $output['collection_date_to'] = $collectionDateTo;
                else
                    $output['collection_date_to'] = "";

                $output['comments'] = $manifest->getCollectionComment();
                //echo 
                $output['collection_address'] = SessionManager::getUser()->getReturnAddress();

                /*
                  $output['address_line_1'] = SessionManager::getUser()->getAddressLine1();
                  $output['address_line_2'] = SessionManager::getUser()->getAddressLine2();
                  $output['address_line_3'] = SessionManager::getUser()->getAddressLine3();

                  $output['city'] = SessionManager::getUser()->getCity();
                  $output['country'] = SessionManager::getUser()->getCity();

                 */



                ////////////////////////////// PICKUP - PARAMETERS //////////////////////////


                if ($pickupDate != '1970-01-01 1:00')
                    $output['pickup_date'] = $pickupDate;
                else
                    $output['pickup_date'] = "";

                $output['name_of_driver'] = $driverName;
                $output['licence_number'] = $licenceNumber;
            }
            else {
                $output['response'] = 'ERROR';
            }

            echo json_encode($output);

            exit;
        }


        if (isset($this->form_vars["funcAction"]) && trim($this->form_vars["funcAction"]) == 'UPDATE_PICKUP') {
            $output = array();

            $id = $this->form_vars['id'];
            $pickupDate = $this->form_vars['pickup_date'];
            $nameOfDriver = $this->form_vars['name_of_driver'];
            $licenceNumber = $this->form_vars['licence_number'];


            //$manifestFilter =	new Manifest(trim($id));				
            //$manifestIdArray = json_decode($id, true);
//            $manifestIdArray = explode(",",$id);
//            $strManifestId = '';
//            if (!is_array($manifestIdArray))
//            //$manifest		=	new Manifest(trim($id));
//                $strManifestId = $id;
//            else {
//                $strManifestId = implode(", ", $manifestIdArray);
//            }
            //mail("mkazim4u@gmail.com", "manifest id", $strManifestId);
            $strManifestId = $id;
            $manifestFilter = new ManifestDataFilter();
            $manifestFilter->addIdFilterIn($strManifestId);
            $list = $manifestFilter->getList();
            if (count($list) > 0) {
                //$pickupFileName = $pickNoteReport->SavePDFFile($pickUpId, $manifestIdArray);
                //$pickup->setPickUpPDF($pickupFileName);
                //$pickup->save();
                $pickUpId = 0;
                foreach ($list as $manifest) {
                    $pickUpId = $manifest->getPickUpId();
                    $manifest->setPickupDate(strtotime($pickupDate));
                    $manifest->setNameOfDriver($nameOfDriver);
                    $manifest->setLicenceNumber($licenceNumber);
                    //$manifest->setDeliveryNote($deliveryNote);
                    //$manifest->setSignature($signature);
                    //$manifest->setPickupId($pickup->getId());
                    $manifest->save();
                }
                if ($pickUpId > 0) {

                    //print_r(array($id));

                    if (strpos($id, ",") !== false) {
                        $manifestIdArray = explode(",", $id);
                    } else {
                        $manifestIdArray = array($id);
                    }



                    $pickup = new PickupSmart($pickUpId);
                    $pickNoteReport = new PickNoteReport();
                    $pickupFileName = $pickNoteReport->SavePDFFile($pickUpId, $manifestIdArray, "Pickup");
                    //$pickup->setPickupNumber($);
                    $pickup->setPickupDate(strtotime($pickupDate));
                    //$pickup->setDeliveryNote($deliveryNote);
                    $pickup->setPickUpPDF($pickupFileName);
                    $pickup->save();
                }
                //$pickUpId = $pickup->getId();

                $subject = " Pickup up for Manifest Number " . $id . " .";
                $headers = "From: itsupport@oneworldexpress.com \r\n";
                $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $headers .= 'Cc: itsupport@oneworldexpress.com; ' . "\r\n";
                $message = 'Dear Sir, <br><br>A pickup has been booked for manifest number ' . $strManifestId . ' on  ' . $pickupDate;

                //if(mail($customerEmail.',itsupport@oneworldexpress.com', $subject, $message, $headers))
                // mail("kathrin@oneworldexpress.com", $subject, $message, $headers);
                //mail("Martin Fuell<martin.fuell@postconsulting.at>", 'Pickup for manifest number '.$id, 'Dear Sir, <br><br>A pickup has been booked for manifest number '.$strManifestId.' on  '. $pickupDate, $headers);
                $output['manifest_id'] = $strManifestId;
                $output['delivery_note'] = $pickupFileName;
                $output['response'] = 'SUCCESS';
            } else {
                $output['response'] = 'ERROR';
            }
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars["funcAction"]) && trim($this->form_vars["funcAction"]) == 'UPDATE_COLLECTION') {
            $output = array();
            //print_r($this->form_vars);
            $id = $this->form_vars['id'];
            $dateColletion = $this->form_vars['collection_date'];
            $dateColletionTo = $this->form_vars['collection_date_to'];
            $commentColletion = $this->form_vars['collection_comment'];

            $address_line_1 = $this->form_vars['address_line_1'];
            $address_line_2 = $this->form_vars['address_line_2'];
            $address_line_3 = $this->form_vars['address_line_3'];
            $city = $this->form_vars['city'];
            $country = $this->form_vars['country'];
            $postcode = $this->form_vars['postcode'];

            //$collectionAddress = $this->form_vars['collection_address'];		


            $manifestIdArray = json_decode($id, true);
//            $manifestIdArray = explode(",", $id);
//
            if (!is_array($manifestIdArray)) {
                $manifest = new Manifest(trim($id));
                $strManifestId = $id;
            } else {
                $strManifestId = implode(", ", $manifestIdArray);
            }

            //mail("mkazim4u@gmail.com", "manifest id", $strManifestId);

            $strManifestId = $id;

            $manifestFilter = new ManifestDataFilter();
            $manifestFilter->addIdFilterIn($strManifestId);
            $list = $manifestFilter->getList();

            $pickup = new PickupSmart();
            //$pickup->setPickupNumber($);
            //$pickup->setPickupDate(strtotime($pickupDate));
            //$pickup->setDeliveryNote($deliveryNote);			
            $pickup->save();
            $pickUpId = $pickup->getId();

            //print_r($manifestIdArray);

            $pickNoteReport = new PickNoteReport();
            $pickupFileName = $pickNoteReport->SavePDFFile($pickUpId, $manifestIdArray, "Collection");
            $pickup->setCollectionPDF($pickupFileName);
            //$pickup->setCollectionAddress($collectionAddress);

            $pickup->setAddressLine1($address_line_1);
            $pickup->setAddressLine2($address_line_2);
            $pickup->setAddressLine3($address_line_3);
            $pickup->setCity($city);
            $pickup->setCountry($country);
            $pickup->setPostCode($postcode);

            $pickup->save();

            //echo "collection";
            //die;
            //$manifestFilter =	new Manifest(trim($id));

            if (count($list) > 0) {
                foreach ($list as $manifest) {
                    $manifest->setCollectionComment($commentColletion);
                    $manifest->setCollectionDate(strtotime($dateColletion));
                    $manifest->setCollectionDateTo(strtotime($dateColletionTo));
                    $manifest->setPickUpId($pickUpId);
                    $manifest->save();
                }
                $api_response = $this->SendCollectionRequest($pickUpId, $list);

                if ($api_response == "SUCCESS") {
                    //echo "<pre>";
                    //print_r($manifestFilter);
                    //die;
                    $subject = " Collection for Manifest Number " . $id . " .";
                    $headers = "From: itsupport@oneworldexpress.com \r\n";
                    $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $headers .= 'Cc: itsupport@oneworldexpress.com; ' . "\r\n";
                    $message = 'Dear Sir, <br><br>A collection has been booked for manifest number ' . $strManifestId . ' on  ' . $dateColletion;
                    //mail("kathrin@oneworldexpress.com", $subject, $message, $headers);
                    //mail("Martin Fuell<martin.fuell@postconsulting.at>", 'Collection for manifest number '.$id, 'Dear Sir, <br><br>A collection has been booked got manifest number '.$strManifestId.' on  '. $dateColletion, $headers);
                    $output['manifest_id'] = $strManifestId;
                    $output['delivery_note'] = $pickupFileName;
                    $output['response'] = 'SUCCESS';
                } else {
                    $output['response'] = $api_response;
                }
            } else {
                $output['response'] = 'ERROR';
            }

            //print_r($output);

            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars["form_action"])) {
            // take appropriate action  btnSave
            if ($this->form_vars["btnBook"] == 'Book') {
                

                $user = SessionManager::getUser();

                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addAccountFilter($user->getAccount());


                $fromDate = date("Y-m-d", strtotime("-1 month"));

                $dateTo = date("Y-m-d");


                //$dateFrom = date("Y-m-d") - 30;
                //$dateFrom = date("Y-m-d", strtotime("-1 months"));
                $consignmentFilter->addDateFilter($fromDate, date("Y-m-d"), 'printed');
                $consignmentFilter->addEndOfDayIsNull();
                $consignmentFilter->addStatusFilter(Consignment::STATUS_LABEL_CREATED);

                $list = $consignmentFilter->getColumnlist("awb");

                //echo count($list);
                //die;

                $arr_update_scan_numbers = array();

                foreach ($list as $consignent) {
                    if ($consignent->getAwb() != '')
                        $arr_update_scan_numbers[] = $consignent->getAwb();
                }

                //echo "<pre>";
                //print_r($arr_update_scan_numbers);
                //die;

                if (count($arr_update_scan_numbers) > 0) {

                    $strTrackingNumbers = implode("','", $arr_update_scan_numbers);

                    $set_update_columns = " date_endofday = '" . date("Y-m-d G:i:s") . "'";

                    $where = "awb IN ('$strTrackingNumbers')";

                    //echo $set_update_columns . " " . $where;
                    //die;

                    TrackingData::SendEmail($arr_update_scan_numbers, $user);

                    Consignment::bulkUpdate($set_update_columns, $where);
                }
            }
        }
    }

    public function SendCollectionRequest($pickupId, $manifestList) {

        $shipmentArray = array();

        $user = SessionManager::getUser();
        $customerInfoArray = array(
            "customer-id" => $user->getAccount(),
            "mail" => $user->getEmail(),
            "contact-name" => $user->getFirstName(),
            "return-address" => $user->getReturnAddress()
        );

        $pickup = new PickupSmart($pickupId);

        $pickupPdf = $pickup->getCollectionPdf();

        $addressLine1 = $pickup->getAddressLine1();
        $addressLine2 = $pickup->getAddressLine2();
        $addressLine3 = $pickup->getAddressLine3();
        $city = $pickup->getCity();
        $country = $pickup->getCountry();
        $postCode = $pickup->getPostCode();


        $totalPieces = 0;
        $totalWeight = 0;

        $totalManifestWeight = 0;

        foreach ($manifestList as $manifest) {
            $totalManifestWeight = 0;

            $ManifestConsignmentDataFilter = new ManifestConsignmentDataFilter();
            $ManifestConsignmentDataFilter->addManifestIDFilter($manifest->getId());
            $manifestList = $ManifestConsignmentDataFilter->getList();
            if (count($manifestList) > 0) {

                foreach ($manifestList as $m) {
                    $consignmentIdArray[] = $m->getConsignmentId();
                }

                //$strConsignmentId = "'" . implode("','", $consignmentIdArray) . "'";  

                $consignentFilter = new ConsignmentFilter();
                $consignentFilter->addIdArrayFilter($consignmentIdArray);
                $list = $consignentFilter->getColumnList("id, account, hawb, reference, awb, company, 
					contact, address_line_1,address_line_2, address_line_3, city, postcode, country,                    
					telephone, weight, telephone, number_pieces, 
					description, date_submitted, date_booked, handling, service_type ");

                foreach ($list as $consignment) {

                    $totalManifestWeight += $consignment->getWeight();
                    $totalWeight += $consignment->getWeight();

                    $shipmentArray[] = array("Account" => $consignment->getAccount(),
                        "HawbNo" => $consignment->getHawb(),
                        "SSCC" => $consignment->getAwb(),
                        "Reference" => $consignment->getReference(),
                        "TrackingNumber" => $consignment->getAwb(),
                        "Company" => $consignment->getCompany(),
                        "Contact" => $consignment->getContact(),
                        "Address1" => $consignment->getAddressLine1(),
                        "Address2" => $consignment->getAddressLine2(),
                        "Address3" => $consignment->getAddressLine3(),
                        "City" => $consignment->getCity(),
                        "Postcode" => $consignment->getPostCode(),
                        "Country" => $consignment->getCountry(),
                        "Telephone" => $consignment->getTelephone(),
                        "Weight" => $consignment->getWeight(),
                        "NumberOfPieces" => $consignment->getNumberPieces(),
                        "Description" => $consignment->getDescription(),
                        "DateCreated" => date_format(date_create($consignment->getDateSubmitted()), 'c'),
                        "DateBooked" => $consignment->getDateBooked(),
                        "ServiceType" => $consignment->getServiceType()
                            //"ServiceCode" => $consignment->getHandling(),
                            //"ServiceName" => $consignment->getServiceType()
                    );
                }

                $totalPieces += $manifest->getPieces();
                $collectionDateFrom = date("Y-m-d", $manifest->getCollectionDate());
                $collectionDateTo = date("Y-m-d", $manifest->getCollectionDateTo());

                //$totalWeight += $manifest->getWeight();  
                //$collectionDateFrom = date_format(date_create(date("Y-m-d", $manifest->getCollectionDate())), 'c');
                //$collectionDateTo = date_format(date_create(date("Y-m-d",  $manifest->getCollectionDateTo())), 'c');

                $manifestArray[] = array(
                    "date" => date_format(date_create(date("Y-m-d", $manifest->getDateCreated())), 'c'),
                    "id" => $manifest->getId(),
                    "number-of-shipments" => $manifest->getPieces(),
                    "total-weight" => $totalManifestWeight,
                    "shipments" => $shipmentArray,
                );
            }
        }

        $arrayCollection = array(
            "_description" => "OWE pickup dataset",
            "id" => $pickupId,
            "customer-info" => $customerInfoArray,
            "delivery-note-pdf" => "https://oneworldexpress.co.uk/remote/" . str_replace("../", "", $pickupPdf),
            "requested-pickup-date" => $collectionDateFrom,
            //"requested-pickup-date-to" => $collectionDateTo, //date_format(date_create($collectionDateTo))), 'c'),
            "number-of-pieces" => $totalPieces,
            "total-weight" => $totalWeight,
            "manifests" => $manifestArray,
            "collection_address_line_1" => $addressLine1,
            "collection_address_line_2" => $addressLine2,
            "collection_address_line_3" => $addressLine3,
            "collection_address_city" => $city,
            "collection_address_country" => $country,
            "collection_address_postcode" => $postCode,
        );



        //$service_url='https://api-ops-test.azurewebsites.net/api/hub/data/pickup/'.$pickupId;
        //$service_url='https://api-ops-test.azurewebsites.net/api/backend/owe/pickup/'.$pickupId;
        $service_url = 'https://cloud.open-postal-systems.net/api/backend/owe/pickup/' . $pickupId;



        $username = "oweBackend";
        $password = "WMAmc9hoTCdz2jvIoDCp";
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        $curl = curl_init($service_url);
        $curl_post_data = $json;
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // don't knpow how to use get - let's hope it's the default :)
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($arrayCollection));
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $curl_response = curl_exec($curl);

        //print_r($curl_response);
        //echo "API RESULT\n";
        //echo "\n";
        $messageCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($messageCode == "200") {
            mail("kazim@oneworldexpress.com", "OPS COLLECTION SUCCESS", json_encode($arrayCollection));
            return "SUCCESS";
        } else if ($messageCode == "409") {
            mail("itsupport@oneworldexpress.com", "OPS COLLECTION FAILED", "FOR" . $pickupId . "unable to make collection on OPS.");
            return "There is some issue while making collection. Please try again later.";
        }
    }

    /**
     * Force page refresh if importing
     */
    protected function renderHead() {
        ?>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
        <script>
            var count = 0;

            function checkedAll() {

                //alert(groupcheck[].length);

                var group = document.getElementsByName("selectManifest[]");
                if (count == 0)
                {
                    for (var i = 0, len = group.length; i < len; i++)
                    {
                        if (group[i].checked == false)
                            group[i].click();//checked = true;
                        count = 1;
                    }
                } else
                {
                    for (var i = 0, len = group.length; i < len; i++)
                    {
                        if (group[i].checked == true)
                            group[i].click();// = false;
                        count = 0;
                    }
                }


                /*
                 var selectedManifest = [];
                 
                 
                 $('input[name="selectManifest[]"]:checked').each(function() {
                 selectedManifest.push(this.value);//alert(this.value);
                 alert(this.length);				
                 });
                 
                 
                 */
            }

            $(document).ready(function () {
                //alert($(".manifest_chk").length);
                if ($(".manifest_chk").length == 0) {
                    $("#selectAll").attr('disabled', 'disabled');
                    $("#bulk_update_container").remove();
                }
                $("#btnSearch").click(function () {
                    $("#form_action").val("search");
                    $("#adminForm").submit();
                });
                //$('#collection_date').datetimepicker({dateFormat: 'dd/MM/yyyy hh:mm:ss'});
                //$('#collection_date_to').datetimepicker({dateFormat: 'dd/MM/yyyy hh:mm:ss'});
                $('#collection_date').datepicker({dateFormat: 'yyyy-mm-dd'});
                $('#collection_date_to').datepicker({dateFormat: 'yyyy-mm-dd'});
                $('#pickup_date').datepicker({dateFormat: 'yyyy-mm-dd'});
                //$('#collection_date').datetimepicker({dateFormat: 'yyyy-mm-dd', use24hours: true});
        //                $('#collection_date_to').datetimepicker({dateFormat: 'yyyy-mm-dd', use24hours: true});
        //                $('#pickup_date').datetimepicker({dateFormat: 'yyyy-mm-dd', use24hours: true});

                //$('#etd').datepicker({dateFormat: "d M yy"});
                //$('#eta').datepicker({dateFormat: "d M yy"});

            });
            function noSpeciatCharacter(e)
            {
                var unicode = e.charCode ? e.charCode : e.keyCode
                //alert(unicode);
                if (unicode != 8)
                {
                    if ((unicode == 31) || (unicode >= 33 && unicode <= 35) || (unicode >= 39 && unicode <= 43) || (unicode >= 36 && unicode <= 38) || unicode == 163 || unicode == 94 || unicode == 64 || unicode == 126) //if not a number
                        return false //disable key press
                }
            }

            function funcPickup(id)
            {
                $("#lblPickupManifestId").text(id);
                $("#update_pickup_msg").css('display', 'none');
                $("#manifested_id").val(id);




                var manifestid;
                var selectedManifest = [];
                if (id == 0)
                {
                    $('input[name="selectManifest[]"]:checked').each(function () {
                        selectedManifest.push(this.value);//alert(this.value);				
                    });

                    if (selectedManifest.length == 0)
                    {
                        alert("Please select manifest for pick-up.");
                        return;
                    }

                    $("#pickup-request").modal("show");

                    $("#lblPickupManifestId").text(selectedManifest.join(","));

                    //manifestid = JSON.stringify(selectedManifest);
                    manifestid = selectedManifest.join(",");
                    $("#manifested_id").val(manifestid);

                } else
                {
                    manifestid = id;
                    $("#manifested_id").val(id);
                    $("#lblPickupManifestId").text(manifestid);
                }

                //alert(manifestid);

                var form_data = new FormData();
                form_data.append('funcAction', 'GET_PICKUP');
                form_data.append('id', manifestid);
                $.ajax({
                    url: 'coclient_user_endofday_collection.php',
                    dataType: 'text',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        //alert(response);
                        var obj = JSON.parse(response);
                        if (obj.response == 'SUCCESS')
                        {
                            $("#pickup_date").val(obj.pickup_date);
                            $("#name_of_driver").val(obj.name_of_driver);
                            $("#licence_number").val(obj.licence_number);

                        }
                    }
                });
            }
            function funcManifest(manifeastId)
            {
                var selectedManifest = [];
                if (manifeastId == 0)
                {
                    $('input[name="selectManifest[]"]:checked').each(function () {
                        selectedManifest.push(this.value);//alert(this.value);				
                    });

                    if (selectedManifest.length == 0)
                    {
                        alert("Please select manifest for collection.");
                        return;
                    }
                    $("#collection-request").modal("show");

                    $("#lblCollectionManifestId").text(selectedManifest.join(","));

                    //manifeastId = JSON.stringify(selectedManifest);
                    manifeastId = selectedManifest.join(",");
                    $("#manifested_id").val(manifeastId);
                } else
                {
                    $("#manifested_id").val(manifeastId);
                    $("#lblCollectionManifestId").text(manifeastId);
                }
                $("#update_collection_msg").addClass('hidden');
                //JSON.parse(manifeastId);
                var form_data = new FormData();
                form_data.append('funcAction', 'GET_COLLECTION');

                //alert(manifeastId);

                form_data.append('id', manifeastId);

                $.ajax({
                    url: 'coclient_user_endofday_collection.php',
                    dataType: 'text',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        //alert(response);
                        var obj = JSON.parse(response);
                        if (obj.response == 'SUCCESS')
                        {
                            $("#collection_date").val(obj.collection_date);
                            //$("#collection_date").datetimepicker( {'setDate' : obj.collection_date});	
                            $("#collection_date_to").val(obj.collection_date_to);
                            $("#collection_comment").val(obj.comments);
                            $("#collection_address").val(obj.comments);
                        }
                    }
                });
            }

            function updatePickupData()
            {

                var id = $("#manifested_id").val();


                var pickupDate = $("#pickup_date").val();
                var name_of_driver = $("#name_of_driver").val();
                var licence_number = $("#licence_number").val();

                var form_data = new FormData();
                form_data.append('funcAction', 'UPDATE_PICKUP');
                form_data.append('pickup_date', pickupDate);
                form_data.append('id', id);
                form_data.append('name_of_driver', name_of_driver);
                form_data.append('licence_number', licence_number);


                $.ajax({
                    url: 'coclient_user_endofday_collection.php',
                    dataType: 'text',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        //alert("--"+response+"--");
                        var obj = JSON.parse(response);
                        if (obj.response == 'SUCCESS')
                        {
                            var manifest_tmp = obj.manifest_id;
                            var manifest = manifest_tmp.split(",");
                            if (manifest.length > 0) {
                                $.each(manifest, function (index, manifest_id) {
                                    manifest_id = $.trim(manifest_id);
                                    $("#collection_td_" + manifest_id).attr('colspan', '2');
                                    var c_html = '<a href="javascript:;" title="Collected" class="btn btn-success btn-block margin-bottom-10">Collected</a>';
                                    c_html += '<a class="btn btn-primary btn-block" title="Collection" href="' + obj.delivery_note + '" target="_blank">Delivery Note</a>';
                                    $("#collection_td_" + manifest_id).html(c_html);
                                    $("#pickup_td_" + manifest_id).remove();
                                });
                            }
                            $("#update_pickup_msg").html('Pickup has been made.');
                            $("#update_pickup_msg").css('display', 'block');
                        }
                    }
                });
            }
            function updateCollectionData()
            {
                var id = $("#manifested_id").val();
                var colelctionDate = $("#collection_date").val();
                var colelctionDateTo = $("#collection_date_to").val();
                var collectionComment = $("#collection_comment").val();
                var collectionAddress = $("#collection_address").val();
                var addressLine1 = $("#address_line_1").val();
                var addressLine2 = $("#address_line_2").val();
                var addressLine3 = $("#address_line_3").val();
                var city = $("#city").val();
                var country = $("#country").val();
                var postcode = $("#postcode").val();

                var selectedManifest = [];


                $('input[name="selectManifest[]"]:checked').each(function () {
                    selectedManifest.push(this.value);//alert(this.value);				
                });


                if (selectedManifest.length > 0)
                {
                    $("#lblCollectionManifestId").text(selectedManifest.join(","));
                    //var manifestId = JSON.stringify(selectedManifest);
                    var manifestId = selectedManifest.join(",");
                    $("#manifested_id").val(manifestId);
                } else
                {
                    var manifestId = id;
                    $("#manifested_id").val(id);
                }
                var form_data = new FormData();
                form_data.append('funcAction', 'UPDATE_COLLECTION');
                form_data.append('collection_date', colelctionDate);
                form_data.append('collection_date_to', colelctionDateTo);
                form_data.append('id', manifestId);
                form_data.append('collection_comment', collectionComment);
                form_data.append('collection_address', collectionAddress);

                form_data.append('address_line_1', addressLine1);
                form_data.append('address_line_2', addressLine2);
                form_data.append('address_line_3', addressLine3);
                form_data.append('city', city);
                form_data.append('country', country);
                form_data.append('postcode', postcode);



                $.ajax({
                    url: 'coclient_user_endofday_collection.php',
                    dataType: 'text',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        // alert(response);
                        var obj = JSON.parse(response);
                        if (obj.response == 'SUCCESS')
                        {
                            var manifest = obj.manifest_id;
                            manifest = manifest.split(",");
                            if (manifest.length > 0) {
                                $.each(manifest, function (index, manifest_id) {
                                    manifest_id = $.trim(manifest_id);
        //                                    var c_html = '<a href="#" title="Collection Request" data-target="#collection-request" data-toggle="modal" class="btn btn-success btn-block margin-bottom-10"  onclick="funcManifest(\'' + manifest_id + '\');">Request Sent</a>';
                                    var c_html = '<a href="#"  class="btn btn-success btn-block margin-bottom-10">Request Sent</a>';

                                    //c_html += '<a class="btn btn-primary btn-block" title="Collection" href="' + obj.delivery_note + '" target="_blank">Delivery Note</a>';

                                    $("#collection_td_" + manifest_id).html(c_html);
                                    $("#collection_td_" + manifest_id).removeAttr('colspan');
                                    if ($("#pickup_td_" + manifest_id).length > 0) {
                                        var p_html = '<a onclick="funcPickup("' + manifest_id + '");" class="btn btn-primary btn-block" data-toggle="modal" data-target="#pickup-request" title="Pickup" href="#">Pickup</a>';
                                        $("#pickup_td_" + manifest_id).html(p_html);
                                    } else {
                                        var p_html = '<td id="pickup_td_' + manifest_id + '"><a onclick="funcPickup(\'' + manifest_id + '\');" class="btn btn-primary btn-block" data-toggle="modal" data-target="#pickup-request" title="Pickup" href="#">Pickup</a></td>';
                                        $("#collection_td_" + manifest_id).after(p_html);
                                    }
                                });
                            }
                            //alert(obj.delivery_note);
                            //alert(obj.manifest_id);
                            $("#update_collection_msg").html('You have successfully update collection time.');
                            $("#update_collection_msg").removeClass('hidden');
                        } else
                        {
                            $("#update_collection_msg").html('Collection not booked.');
                        }

                    }
                });
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {

        $collection = "N";

        if (isset($_REQUEST['collection']) && $_REQUEST['collection'] == "Y") {
            $collection = $_REQUEST['collection'];
        }
        ?>


        <ul class="breadcrumb">
            <li><a href="../main/index.php"><?php echo Translation::GetCaption("HOME"); ?></a></li>
            <li><a href="../main/client_list.php"><?php echo Translation::GetCaption("SHIPMENTS"); ?></a></li>
            <li><a href="../main/coclient_user_endofday_collection.php"><?php echo Translation::GetCaption("COLLECTION"); ?></a></li>
            <!--<li>	<div class="row">
                    <div class="col-md-12">  
                        <img src="../images/5.png" alt="create shipment" style="padding-left:15px; width:900px;"  />
                    </div> 
                </div></li>-->
        </ul>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-table"></i>
                        <?php echo Translation::GetCaption("REQUEST_COLLECTION"); ?></div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <?php errorList::getItem()->render(); ?>
                    <div class="row">   

                        <div class="col-md-12">

                            <?php
                            
                            $user = SessionManager::getUser();
                            $dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
                            $dateTo = date("Y-m-d");
                            $manifestFilter = new ManifestDataFilter();
                            $manifestFilter->addAccountFilter($user->getAccount());
                            $manifestFilter->addDateRangeFilter($dateFrom, $dateTo);
                            $manifestFilter->addOrderById();
                            $list_manifest = $manifestFilter->getColumnList("id, file_name, pdf_file, date_created, handling, collection_date, pickup_date, pickup_id, pieces");

                            //include("endofday_booking.php");

                            /* if($collection != 'Y')
                              {


                              /////////////////////////// showing last month of ///////////////////////////////

                              $fromDate = date("Y-m-d", strtotime("-1 month"));

                              //$dateFrom = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
                              $dateTo = date("Y-m-d");


                              $consignmentFilter = new ConsignmentFilter();
                              $consignmentFilter->addAccountFilter($user->getAccount());
                              $consignmentFilter->addDateFilter($fromDate, date("Y-m-d"), 'submitted');
                              $consignmentFilter->addStatusFilter(Consignment::STATUS_LABEL_CREATED);
                              $consignmentFilter->addEndOfDayIsNull();
                              $list = $consignmentFilter->getColumnlist("id");

                              if(count($list) > 0 )
                              {
                              $total =  count($list);
                              $html = '<table>
                              <tr>
                              <td> Showing <b>non-manifested</b> Shipments for booking less than 30 days old.</td>
                              </tr>
                              <tr>
                              <td>
                              <br/>
                              <b>'.$total .'</b> Total ready to be booked
                              <input id="btnBook" type="submit"
                              name="btnBook" value="Book" class="btn_save btn btn-primary"  />
                              </td>
                              </tr>


                              </table> <br />';

                              echo $html;
                              }
                              else
                              {
                              $html = '<table>
                              <tr>
                              <td>
                              <b>NO RECORD FOUND</b>
                              </td>
                              </tr>


                              </table>';

                              //echo $html;
                              }

                              } */
                            ?>

                            <div class="row" id="bulk_update_container">
                                <div class="col-md-2">
                                    <a id="btnCollectionSelected" href="#" title="Collection Request" data-toggle="modal" class="btn btn-primary" style="" onclick="funcManifest(0);"><?php echo Translation::GetCaption("REQUEST_COLLECTION_FOR_SELECTED"); ?></a>              
                                </div>
                                <div class="col-md-1">&nbsp;
                                </div>
                                <div class="col-md-2">
                                    <a id="btnPickupSelected" href="#" title="Pickup Request" data-toggle="modal" class="btn btn-primary"  style="" onclick="funcPickup(0);"><?php echo Translation::GetCaption("CONFIRM_PICK_UP_FOR_SELECTED"); ?></a>
                                </div>
                            </div>


                            <div class="table-scrollable">
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="red-back">
                                                <input id="selectAll" name="selectAll" onclick='javascript:checkedAll();'  type="checkbox" />
                                            </th>
                                            <th width="10%" class="red-back">
                                                <?php echo Translation::GetCaption("DATE"); ?>
                                            </th>
                                            <th class="red-back">
                                                <?php echo Translation::GetCaption("MANIFEST_ID"); ?> #
                                            </th>
                                            <th class="red-back">
                                                <?php echo Translation::GetCaption("SERVICE"); ?>
                                            </th>
                                            <th class="red-back">
                                                <?php echo Translation::GetCaption("NUMBEROFSHIPMENTS"); ?>
                                            </th>
                                            <th class="red-back">
                                                <?php echo Translation::GetCaption("VIEWSHIPMENTCSVPDF"); ?>
                                            </th>
                                            <th class="red-back">
                                                <?php echo Translation::GetCaption("REQUEST_COLLECTION"); ?>

                                            </th>
                                            <th class="red-back">
                                                <?php echo Translation::GetCaption("CONFIRMPICKUP"); ?>
                                            </th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        <?php
                                        $table = '';
                                        $count = 0;

                                        foreach ($list_manifest as $manifest) {

                                            $csvlink = $manifest->getFileName();
                                            $pdflink = $manifest->getPdfFile();
                                            $manifestid = $manifest->getId();
                                            $date = $manifest->getDateCreated();
                                            $handling = trim($manifest->getHandling());
                                            $manifestid = $manifest->getId();
                                            $collectionDateFrom = date("Y-m-d", $manifest->getCollectionDate());
                                            $pickupDate = date("Y-m-d", $manifest->getPickupDate());
                                            $number_pieces = $manifest->getPieces();
                                            $pickUpId = $manifest->getPickUpId();


                                            //echo "pickupid : " . $pickUpId;

                                            $serviceHtml = "";
                                            $return = "";


                                            if ($handling !== "") {

                                                if (strpos($handling, "RTN") !== false) {
                                                    $return = "Return";
                                                    $handling = str_replace("RTN", "", $handling);
                                                }


                                                $serviceFilter = new ServiceFilter();
                                                $serviceFilter->addCodeExactFilter($handling);
                                                $serv_list = $serviceFilter->getColumnList("name");

                                                if (count($serv_list) > 0) {
                                                    $serviceObj = $serv_list[0];
                                                    $service = $serviceObj->getName();
                                                    $serviceHtml = "$service";
                                                }
                                            }

                                            //echo $serviceHtml . $handling . $manifestid;
                                            //die;


                                            if ($pickupDate != '1970-01-01') {
                                                $table .= '<tr style="background-color: #D6EDCB;">';
                                            } else {
                                                $table .= "<tr>";
                                            }

                                            $table .= "<td>";
                                            $table .= '<input style="width:5%;" id="selectManifest[]" value="' . $manifestid . '" name="selectManifest[]" class="form-control manifest_chk" type="checkbox"' . (($pickupDate != '1970-01-01') ? 'disabled="disabled"' : '') . ' />';
                                            $table .= "</td>";
                                            $table .= "<td style='width:10%;'>";
                                            $table .= date('d-m-y G:i', $date);
                                            $table .= "</td>";
                                            $table .= "<td>";
                                            $table .= $manifestid;
                                            $table .= "</td>";
                                            $table .= "<td style='width:30%;' >";
                                            $table .= $handling;
                                            $table .= "</td>";
                                            $table .= "<td style='text-align:center;'>";
                                            $table .= $number_pieces;
                                            $table .= "</td>";
                                            $table .= "<td style='text-align:center;'>";
                                            $table .= "<a target='_blank' href='" . $csvlink . "'>" . Translation::GetCaption("CSV") . "</a>";
                                            $table .= " /  <a target='_blank' href='" . $pdflink . "'>" . Translation::GetCaption("PDF") . "</a>";
                                            $table .= "</td>";
                                            //if($collection == 'Y')
                                            //{
                                            //echo $collectionDateFrom;
                                            //die;
                                            if ($collectionDateFrom != '1970-01-01') {
                                                if ($pickupDate == '1970-01-01') {
                                                    $table .= '<td style="text-align:center" id="collection_td_' . $manifestid . '">';
                                                    $pickUp = new PickupSmart($pickUpId);
//                                                $table .= '<a href="#" title="Collection Request" data-target="#collection-request" data-toggle="modal" class="btn btn-success btn-block margin-bottom-10" onclick="funcManifest(\'' . $manifestid . '\');">'.Translation::GetCaption("REQUESTSENT").'</a>';
                                                    $table .= '<a href="#" onclick="return false;" class="btn btn-success disabled btn-block margin-bottom-10">Request Sent</a>';
                                                    if ($pickUpId > 0 && $pickUp->getCollectionPDF() != '') {
                                                        //$table .= '<a target="_blank" href="' . $pickUp->getCollectionPDF() . '" title="Collection" class="btn btn-primary btn-block">'.Translation::GetCaption("DELIVERY_NOTE").'</a>';
                                                    }
                                                    $table .= '</td>';
                                                    $table .= '<td style="text-align:center" id="pickup_td_' . $manifestid . '">';
                                                    $table .= '<a href="#" title="Pickup" data-target="#pickup-request" data-toggle="modal" class="btn btn-primary btn-block" onclick="funcPickup(\'' . $manifestid . '\');">' . Translation::GetCaption("PICKUP") . '</a>';
                                                    $table .= "</td>";
                                                } else {
                                                    $pickUp = new PickupSmart($pickUpId);
                                                    $table .= '<td style="text-align:center" id="collection_td_' . $manifestid . '" colspan="2">';
                                                    $table .= '<a href="javascript:;" title="Collected" class="btn btn-success btn-block margin-bottom-10">' . Translation::GetCaption("COLLECTED") . '</a>';
                                                    if ($pickUpId > 0 && $pickUp->getCollectionPDF() != '') {
                                                        $table .= '<a target="_blank" href="' . $pickUp->getCollectionPDF() . '" title="Collection" class="btn btn-primary btn-block">' . Translation::GetCaption("DELIVERY_NOTE") . '</a>';
                                                    }
                                                    $table .= "</td>";
                                                }
                                            } else {
                                                $table .= '<td style="text-align:center" id="collection_td_' . $manifestid . '" colspan="2">';
                                                $table .= '<a href="#" title="Collection Request" data-target="#collection-request" data-toggle="modal" class="btn btn-primary btn-block" onclick="funcManifest(\'' . $manifestid . '\');">' . Translation::GetCaption("SIMPLECOLLECTION") . '</a>';
                                                $table .= '</td>';
                                            }

                                            if ($pickupDate != '1970-01-01' && $collectionDateFrom != '1970-01-01') {
//                                            $table .= "</td>";
//                                            $table .= '<td style="text-align:center" id="pickup_td_' . $manifestid . '">&nbsp;';
//                                            $pickUp = new PickUp($pickUpId);
//                                            $table .= '<a href="#" title="Pickup" data-target="#pickup-request" data-toggle="modal" class="btn btn-primary btn-block" onclick="funcPickup(\'' . $manifestid . '\');">Picked-up</a>';
//
//                                            if ($pickUp->getPickUpPDF() != '')
//                                                $table .= '<a target="_blank" href="' . $pickUp->getPickUpPDF() . '" title="Pickup" class="btn btn-primary btn-block">Delivery Note</a>';
                                            }

                                            //}
                                            //$table .= "<br>";			
                                            //$table .= "$serviceHtml Date : " . date("d-m-y G:i", $date) . "  : CSV : <a target='_blank' href='".$csvlink."'>".$manifestid."</a> PDF : <a target='_blank' href='".$pdflink."'>".$manifestid."</a>";
                                            //$table .= "<br>";						
                                            //$table .= "</td>";
                                            $table .= "</tr>";
                                            $count++;
                                        }
                                        echo $table;
                                        ?>
                                    </tbody>
                                </table>
                            </div>  
                        </div>

                    </div>   

                </div>
            </div>
        </div>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        </form>
        <div class="modal fade" tabindex="-1" role="dialog" id="collection-request" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo Translation::GetCaption("COLLECTIONREQUEST"); ?> - <label id="lblCollectionManifestId"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="collection_request_frm" name="collection_request_frm" method="post">
                            <div class="alert alert-success" id="update_collection_msg"></div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12 date-date-pic">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("COLLECTIONDATEFROM"); ?>:</label> 
                                    <div class="input-group date form_datetime">
                                        <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input id="collection_date" name="collection_date" type="text" size="16" data-date-format="yyyy-mm-dd" readonly class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 date-date-pic">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("COLLECTIONDATETO"); ?>:</label> 
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input type="text" data-date-format="yyyy-mm-dd" name="collection_date_to" id="collection_date_to" value="" class="form-control" readonly />
                                    </div>
                                </div>
                            </div>
                            <?
                            $address_line_1 = $user->getCollectionAddLine1();
                            $address_line_2 = $user->getCollectionAddLine2();
                            $address_line_3 = $user->getCollectionAddLine3();
                            $city = $user->getCollectionCity();
                            $country = $user->getCollectionCountry();
                            $postcode = $user->getCollectionPostcode();

                            ?>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("ADRESS_LINE_1_(STREET_&_NO)"); ?>:</label>
                                    <input type="text" name="address_line_1" id="address_line_1" value="<?php echo $address_line_1; ?>" class="form-control" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("ADDRESS_LINE_2"); ?>:</label>
                                    <input type="text" name="address_line_2" id="address_line_2" value="<?php echo $address_line_2; ?>" class="form-control" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("ADDRESS_LINE_3_COUNTRY_DIVISION_STATE"); ?>:</label>
                                    <input type="text" name="address_line_3" id="address_line_3" value="<?php echo $address_line_3; ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("CITY"); ?>:</label>
                                    <input type="text" name="city" id="city" value="<?php echo $city; ?>" class="form-control" />
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("COUNTRY"); ?>:</label>
                                    <div class="form-group col-md-12">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                                            <select  name="country"  size="1" id="country" class="form-control" data-live-search="true" rel="tooltip" data-original-title="Country" placeholder="Country">
                                                <option value="" selected="selected">Select Country</option>
                                                <?php
                                                $countriesList = new CountryFilter();
                                                $countryListData = $countriesList->getList();
                                                foreach ($countryListData as $countryItem) {
                                                    $countryName = strtoupper($countryItem->getName());

                                                    if (strtolower(trim($country)) == strtolower(trim($countryItem->getName())))
                                                        echo '<option value="' . $countryName . '" selected="selected">' . $countryName . '</option>';
                                                    else
                                                        echo '<option value="' . $countryName . '" >' . $countryName . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>                            
                                <div class="form-group col-md-4">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("POSTCODE"); ?>:</label>
                                    <input type="text" name="postcode" id="postcode" value="<?php echo $postcode; ?>" class="form-control" />
                                </div>                            
                            </div>                        
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("COLLECTIONCOMMENT"); ?>:</label>
                                    <textarea name="collection_comment" id="collection_comment" value="" class="form-control" ></textarea>
                                </div>
                            </div>
                            <!--<div class="row">
                                <div class="form-group col-md-12">
                                  <? // $collection_address = SessionManager::getUser()->getReturnAddress(); ?>
                                    <label class="control-label font-green-soft"><?php //echo Translation::GetCaption("COLLECTIONADDRESS");   ?>:</label>
                                    <textarea name="collection_address" id="collection_address" value="<? //echo  SessionManager::getUser()->getReturnAddress();  ?>" class="form-control" ><? //echo  SessionManager::getUser()->getReturnAddress();  ?></textarea>
                                </div>
                            </div>-->
                            <div class="modal-footer">
                                <input type="hidden" name="manifested_id" id="manifested_id" value="" />
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Translation::GetCaption("CLOSE"); ?></button>
                                <button type="button" class="btn btn-primary" id="update_hawb_btn" onclick="updateCollectionData();"><?php echo Translation::GetCaption("SENDREQUEST"); ?></button>
                            </div>	
                        </form>    
                    </div>

                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <form id="pickup_request_frm" name="pickup_request_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="pickup-request" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><?php echo Translation::GetCaption("PICKUP"); ?> - <label id="lblPickupManifestId"></label></h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" style="display:none" id="update_pickup_msg"></div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12 date-date-pic">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("PICKUP_DATE"); ?>:</label> 
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                        <input type="text" name="pickup_date" id="pickup_date" data-date-format="yyyy-mm-dd"
                                               value="" class="form-control" readonly />
                                    </div>
                                </div>                            
                                <div class="form-group col-md-6 col-sm-12 date-date-pic">                                               
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("NAMEOFDRIVER"); ?>:</label> 
                                    <div class="input-group"> <span class="input-group-addon"> 
                                            <i class="glyphicon glyphicon-user"></i> </span>
                                        <input type="text" name="name_of_driver" id="name_of_driver" value="" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="control-label font-green-soft"><?php echo Translation::GetCaption("LICENCE_NUMBER"); ?>:</label>
                                    <input type="text" name="licence_number" id="licence_number" value="" class="form-control" />
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="manifested_id" id="manifested_id" value="" />
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Translation::GetCaption("CLOSE"); ?></button>
                            <button type="button" class="btn btn-primary" id="update_hawb_btn" onclick="updatePickupData();"><?php echo Translation::GetCaption("SAVECHANGES"); ?></button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>
        <?php
        // report any errors
        // has file been chosen yet?		
    }

    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
