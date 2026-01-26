<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger',
], 'labels');
include_classes([
    'tcpdf'
], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
], 'general');

include_classes([
    'carrier.class',
    'carrierfilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'servicefilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'usermarketplacesmappingfilter.class',
    'usermarketplacesmapping.class',

]);

class Page extends BasePage
{

    private $consignment_filter;
    private $table_msg;
    private $num_valid; // number of valid consignments.
    private $column_name;
    private $sortby;
    private $flag;
    private $num_invalid;
    private $num_printed;
    private $num_shipped;
    private $num_delivered;
    private $num_hold;
    private $user;
    private $record;
    private $userAccount;
    private $saccount;
    private $uaccount;
    private $uaccountCompanyName;
    private $orderByDT;
    private $orderFalse;

    /*     * *
     * Controller logic
     */

    protected function init()
    {

        //variable that use to get all shipment related to account.
        $this->record = (($_GET['record']) != '') ? $_GET['record'] : '';
        $this->saccount = (int)(trim(util_get('saccount')) != '') ? base64_decode(util_get('saccount')) : '';
        $this->filtertype = base64_decode(util_get("filtertype"));
        $this->uaccount = (int)(trim(util_get('uaccount')) != '') ? base64_decode(util_get('uaccount')) : '';
        if (!empty($this->uaccount)) {
            $userData = new User($this->uaccount);
//            $userAccountId = $userData->getUserAccountId();

            $userAccount = new CustomerAccount($userData->getUserAccountId());
            $this->uaccountCompanyName = $userAccount->getUserAccount();
        }
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'client_list.php' => Translation::GetCaption("SHIPMENTS")
        );

        // user must be CLIENT
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
        $this->user = $user = SessionManager::getUser();
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
        if ($this->user->getId() <= 0) {
            if (isset($_GET['action']) && $_GET['action'] == "consignment_ajax")
                echo "login-failed";
            else
                util_redirect('login.php');
        }
        $this->consignment_filter = new ConsignmentFilter();

        /*
         *   Get count to active lable button for printing labels.
         */
        $conFilter = new ConsignmentFilter();
        if ($user->getUserType() == User::USER_TYPE_CLIENT)
            $conFilter->addFilter("    c.user_id = '" . $user->getId() . "' and c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'");
        else
            $conFilter->addFilter("    c.user_id in ( select id from user where user_account_id = '" . $user->getUserAccountId() . "') and c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'");
        $shipmentCount = $conFilter->getColumnList('c.id');
        $this->shipmentValid = count($shipmentCount);
        /*
         *  END 
         * Ajax Handling for Consignment table value
         */
        //1 echo "me here";
        //   die;
        if (isset($_GET['action']) && $_GET['action'] == "consignment_ajax") {
            $this->table_msg = "Shipments in total";
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $this->orderFalse = $orderBy;
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                if (trim($dataTableColumnName) == 'account')
                    $dataTableColumnName = 'user_account';
                if (trim($dataTableColumnName) == 'date_created')
                    $dataTableColumnName = 'c.date_created';
                if (trim($dataTableColumnName) == 'service_type')
                    $dataTableColumnName = 'service_name';
                if (trim($dataTableColumnName) == 'shipment_status')
                    $dataTableColumnName = 'consignment_status';
                if (trim($dataTableColumnName) == 'country_iso_code')
                    $dataTableColumnName = 'con.name';
                if (trim($dataTableColumnName) == 'user_id')
                    $dataTableColumnName = 'u.user_name';
                
                $this->orderByDT = $dataTableColumnName;
            }

            /*
             *  Add all form data
             */
            $this->addFilters();

            /*
             * Generating Manifest for selected and non selected shipment
             */
            if (isset($this->form_vars["form_action"])) {
                switch ($this->form_vars["form_action"]) {
                    case "createmanifestall":
                    case "createmanifest":
                        $delete_array = $_POST['deleteConsignments'];

                        $conisgnmentIdArrayWithTrackingNumber = array();
                        $this->consignment_filter->addFilter("      c.is_customer_manifested = '0' AND c.awb <> '' AND c.shipment_status = '" . Consignment::STATUS_LABEL_CREATED . "' ");
                        if ($this->form_vars["form_action"] == 'createmanifest') {
                            if (count($delete_array) <= 0) {
                                $output["STATUS"] = "error";
                                $output["MESSAGE"] = Translation::GetCaption("MANIFEST_HAS_NOT_GENERATED");
                                echo json_encode($output);
                                die;
                            } else
                                $this->consignment_filter->addFilter("      c.id in ('" . implode("','", $delete_array) . "') AND c.awb <> '' ");
                        }
                        $consignmentDataArr = $this->consignment_filter->getColumnList(" pc.id  'parcel_id' ", 5000, false, false);

                        if (count($consignmentDataArr) > 0) {
                            foreach ($consignmentDataArr as $ke => $valConsignment)
                                $conisgnmentIdArrayWithTrackingNumber[] = $valConsignment->getParcelId();

                            $output = Manifest::manifestCreate($conisgnmentIdArrayWithTrackingNumber);
                            echo json_encode($output);
                        } else {
                            $output["STATUS"] = "error";
                            $output["MESSAGE"] = Translation::GetCaption("MANIFEST_HAS_NOT_GENERATED");
                            echo json_encode($output);
                        }
                        die;
                        break;

                }  // switch()
            }
            /*
            * End Generating Manifest for selected and non selected shipment
            */


            /*
             * Set pagination & Encode data into Json form to return to DataTable
             */
//            $iTotalRecords = $this->consignment_filter->getShipmentPagingCountNew(false);
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
//          $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->consignment_filter->setRowsPerPage($iDisplayLength);
//         the offset of the list, based on current page
            $this->consignment_filter->setOffset($iDisplayStart);


            $this->consignment_filter->addGroupBy("c.id");

            // Hadi add limit 5 lac and make hidden input
//            $consignmentObjs = $this->consignment_filter->getShipmentPagingListOpt(false);


            
            $iTotalRecords = $this->consignment_filter->getShipmentPagingListOpt(false, true, false, true);
            
            $consignmentObjs = $this->consignment_filter->getShipmentPagingListOpt(false);
//$iTotalRecords = $this->consignment_filter->getShipmentPagingCountNew(false);
            if (Permissions::checkFilePermission('hide_subaccount')) {
                $accountObj = new CustomerAccount();
                $searchConsignmentAccountId = $this->form_vars['search_Account'];
                $accountSubAccountArr = $accountObj->getSubAccountsArrayShowConsignmentAccount($this->user->getUserAccountId(),$searchConsignmentAccountId);
            }
            $consignmentDataArr = array();
            foreach ($consignmentObjs as $consignmentObj) {

                /*if (in_array($consignmentObj->getStatus(), [
							Consignment::STATUS_HOLD, 
							Consignment::STATUS_RECYCLED, 
							Consignment::STATUS_READY_TO_PRINT, 
							Consignment::STATUS_INVALID, 
							Consignment::STATUS_LABEL_CREATED ,
							Consignment::STATUS_RECEIVED ,
							Consignment::STATUS_PARTIAL_RECEIVED, 
							Consignment::STATUS_DISPATCHED ,
							Consignment::STATUS_PARTIAL_DISPATCHED
							]) && $consignmentObj->getIsCustomerManifested() != '1') {*/
                    $consignmentArr['option'] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type=checkbox id= "delete55" name="deleteConsignments[]"  value="' . $consignmentObj->getId() . '"  class="group-checkable" /><span></span></label>';
                /*} else {
                    $consignmentArr['option'] = "&nbsp;";
                }*/
                $returnIcon = '';
                if ($consignmentObj->getConsignmentType() == 'return') {
                    $returnIcon = '<i class="font-red-mint fa fa-reply"></i>&nbsp;';
                }
                $consignmentArr['date_created'] = $returnIcon . formatDate($consignmentObj->getDateCreated());

                if ($user->getUserType() == User::USER_TYPE_ADMIN || $user->getUserType() == User::USER_TYPE_CORPORATE) {
                    $consignmentArr['Account'] = $consignmentObj->getUserAccount();
                    if (Permissions::checkFilePermission('hide_subaccount')) {
                        $consignmentAccountId = $consignmentObj->getUserAccountId();
                        $accountObj = new CustomerAccount();
                        $consignmentArr['Account'] = $accountObj->showAccount($consignmentAccountId,$accountSubAccountArr);
                    }
                }
                $externalUserData = ($this->uaccount > 0) ? "&uaccount=" . base64_encode($this->uaccount) : '';
                $consignmentArr['HAWB'] = "";
                $createdFrom = ucfirst($consignmentObj->getCreatedFrom());
                if(file_exists("../images/thirdparty/round/icon-".strtolower($createdFrom)."png"))
                    $logo = "../images/thirdparty/round/icon-".strtolower($createdFrom)."png";
                else
                    $logo = "../images/thirdparty/round/icon-smarttrack.png";
                $consignmentArr['HAWB'] .= '<img src=\'' . $logo . '\' / width=16>&nbsp;&nbsp;&nbsp;' . ucfirst($consignmentObj->getCreatedFrom()) . '<br />' . '<a href="consignment_add.php?from=client&id=' . $consignmentObj->getId() . $externalUserData . '" id="hawb-' . $consignmentObj->getId() . '">';
            
                $consignmentArr['HAWB'] .= $consignmentObj->getHawb();
                $consignmentArr['HAWB'] .= '</a>';

                $consignmentArr['contact'] = ucwords(strtolower($consignmentObj->getContact()));


                $consignmentArr['country_iso_code'] = (($consignmentObj->getCity() == "") ? "" : trim(ucwords(strtolower(($consignmentObj->getCity())))) . " ") . "<br>" . (($consignmentObj->getCountryName() == "") ? "" : trim($consignmentObj->getCountryName()) . " ");

                $consignmentArr['Weight'] = (($consignmentObj->getWeight() == "") ? "" : trim($consignmentObj->getWeight()) . " ") ;
                $status = $consignmentObj->getShipmentStatus();

                $c_status = Consignment::stateText(strtolower(trim($status)));

                if (strtolower($c_status) == "label created") {
                    $shipment_status = '<span class="label label-sm bg-blue-chambray bg-font-blue-chambray line-height-2"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_LABEL_CREATED]) . '</span>';
                }

                if (strtolower($c_status) == "label created")
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-blue-chambray bg-font-blue-chambray"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_LABEL_CREATED]) . '</span>';
                else if (strtolower($c_status) == "booked")
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-blue-dark bg-font-blue-dark"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_DISPATCHED]) . '</span>'; //= Translation::GetCaption("SHIPPED");
                else if (Consignment::STATUS_RECYCLED == trim($status))
                    $consignmentArr['shipment_status'] = '<span class="label label-sm label-danger"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_RECYCLED]) . '</span>';
                else if (Consignment::STATUS_DELIVERED == trim($status))
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-green-jungle bg-font-green-jungle"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_DELIVERED]) . '</span>';
                else if (Consignment::STATUS_INVALID == trim($status))
                    $consignmentArr['shipment_status'] = '<span class="label label-sm label-warning"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) . '</span>';
                else if (Consignment::STATUS_READY_TO_PRINT == trim($status))
                    $consignmentArr['shipment_status'] = '<span class="label label-sm label-info"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_READY_TO_PRINT]) . '</span>';
                else
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-default bg-font-default"> ' . Translation::GetCaption($c_status) . '</span>';

                if (trim($consignmentObj->getProductName()) != "") {
                    $consignmentArr['service_type'] = ucwords(strtolower($consignmentObj->getProductName()));
                } else {
                    $consignmentArr['service_type'] = ucwords(strtolower($consignmentObj->getServiceName()));
                }
                /*if (trim($consignmentObj->getShipmentType()) == 'D')
                    $consignmentArr['service_type'] = ucwords(strtolower($consignmentObj->getServiceName()));
                else
                    $consignmentArr['service_type'] = ucwords(strtolower($consignmentObj->getProductName()));
                */

                $trackingLink = ($consignmentObj->getReturnAwb() != "") ? $consignmentObj->getAwb() . ' / ' . $consignmentObj->getReturnAwb() : $consignmentObj->getAwb();
                //if ($consignmentObj->getServiceCode() == "REGPOST") {
                //    $consignmentArr['awb'] = '<a href="https://tracking.directlink.com/multipletrack-client2.php?lang=en&postal_ref_mode=0&order_no=' . trim($consignmentObj->getAwb()) . ' "target="_blank">' . $trackingLink . '</a>';
                //} else {
                $consignmentArr['awb'] = '<a href="tracking.php?tracking_number=' . trim($consignmentObj->getAwb()) . ' "target="_blank">' . $trackingLink . '</a><br /> <a href="javascript:;" onclick="get_parcel_list(\'' . $consignmentObj->getId() . '\',\'' . $consignmentObj->getAwb() . '\')" class="parcel_lists" data-tid="' . $consignmentObj->getAwb() . '">Parcel List</a>';
                //}

                $errorString = $consignmentObj->getMessage();
                if (strlen($errorString) > 3) {
                    $errorStringMsg = '<p class="tooltipbutton" data-toggle="tooltip" data-placement="top" title=" ' . $errorString . '">' . substr($errorString, 0, 5) . "..." . "</p>";
                } else {
                    $errorStringMsg = $errorString;
                }
                $consignmentArr['Reason'] = $errorStringMsg;

                $consignmentArr['label'] = '';

                if (trim($consignmentObj->getlabelFile()) != '' && file_exists($consignmentObj->getlabelFile())) {

                    if ($user->getFinalMileOverLabel() == "YES") {
                        $label = str_replace("/pdf", "/relabel", $consignmentObj->getlabelFile());
                        $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" href="' . $label . '" target="_blank" ><i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("VIEW_LABEL") . '"> </i> </a>';
                        //$consignmentArr['label'] .= '<br><a href="javascript:{};" title="Details" data-poload="client_list.php" data-cid="' . $consignmentObj->getId() . '">' . Translation::GetCaption("DETAILS") . '</a>';
                    } else {

                        if ($consignmentObj->getIsWhiteLabel() > 0)
                            $label = str_replace(".pdf", "_overlabel.pdf", $consignmentObj->getlabelFile());
                        else
                            $label = $consignmentObj->getlabelFile();
                        $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" href="' . $label . '" target="_blank" ><i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("VIEW_LABEL") . '"> </i></a>';
                        //  $consignmentArr['label'] .= '<br><a href="javascript:{};" title="Details" data-poload="client_list.php" data-cid="' . $consignmentObj->getId() . '">' . Translation::GetCaption("DETAILS") . '</a>';
                    }
                    
                    if($consignmentObj->getIsReschedulable() == 1){
                        $consignmentArr['label'] .= '&nbsp;&nbsp;<a class=" margin-right-5 reschedule_collection" data-consignment_id="' . $consignmentObj->getId() . '" data-action="RESCHEDULECOLLECTION"   rel="tooltip" title="Reschedule Collection"> <i class="fa fa-calendar" data-toggle="tooltip" data-placement="top" title="Reschedule Collection"> </i> </a>';
                    }
                    
                } else if (in_array($consignmentObj->getStatus(), array(Consignment::STATUS_RECEIVED, Consignment::STATUS_PARTIAL_RECEIVED, Consignment::STATUS_DISPATCHED, Consignment::STATUS_PARTIAL_DISPATCHED, Consignment::STATUS_LABEL_CREATED, Consignment::STATUS_INTRANSIT, Consignment::STATUS_DELIVERED, Consignment::STATUS_PARTIAL_DELIVERED))) {

                    $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" onclick="return displayLabel(\'' . $consignmentObj->getId() . '\', \'' . $consignmentObj->getId() . '\')" id="labelDisplayLink-' . $consignmentObj->getId() . '" href="javascript:{};" ><i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("VIEW_LABEL") . '"> </i></a>';
                    if($consignmentObj->getIsReschedulable() == 1){
                        $consignmentArr['label'] .= '&nbsp;&nbsp;<a class="margin-right-5 reschedule_collection" data-consignment_id="' . $consignmentObj->getId() . '" data-action="RESCHEDULECOLLECTION"   rel="tooltip" title="Reschedule Collection"> <i class="fa fa-calendar" data-toggle="tooltip" data-placement="top" title="Reschedule Collection"> </i> </a>';
                    }
                    //    $consignmentArr['label'] .= '<br><a href="javascript:{};" title="Details" data-poload="client_list.php" data-cid="' . $consignmentObj->getId() . '">' . Translation::GetCaption("DETAILS") . '</a>';
                } else if ($consignmentObj->getStatus() == Consignment::STATUS_READY_TO_PRINT) {

                    $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" onclick="javascript:showlabels(\'' . $consignmentObj->getId() . '\')" id="labelDisplayLink-' . $consignmentObj->getId() . '"  ><i class="fa fa-file-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("GENERATE_LABEL") . '"> </i></a>';
                }
                $consignmentDataArr[] = $consignmentArr;
            }

            $consignmentDataarr['data'] = $consignmentDataArr;
            $consignmentDataarr['draw'] = $sEcho;
            $consignmentDataarr['recordsTotal'] = $iTotalRecords;
            $consignmentDataarr['recordsFiltered'] = $iTotalRecords;
            //  echo "<pre>";
            // print_r($consignmentDataarr);
            echo json_encode($consignmentDataarr, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }
//	End Hadi Code
        /*
                if (!isset($_POST['deleteConsignments'])) {
                    if (isset($_REQUEST['show'])) {

                        if ($_REQUEST['show'] != 'collection_selected') {
                            $this->form_vars["form_action"] = $_REQUEST['show'];
                        }
                    }
                }
        */
        // Bring detail fo thte shipment such as manifest id, invoice  no etc
        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'getShipmentDetails') {
            $consignment_id = $_REQUEST['cid'];
            $conData = new Consignment($consignment_id);

            $ManifestConsignmentDataFilter = new ManifestConsignmentDataFilter();
            $ManifestConsignmentDataFilter->addConsignmentIDFilter($consignment_id);
            $manifestList = $ManifestConsignmentDataFilter->getList();
            $manifestIdarray = array();
            if (count($manifestList) > 0) {
                foreach ($manifestList as $manifest) {
                    $manifestIdarray[] = $manifest->getManifestId();
                    $manifestIdarray[] = $manifest->getManifestId();
                }
            }

            $linkManifest = 'N/A';
            $linkpickup = 'N/A';


            if (sizeof($manifestIdarray) > 0) {
                $manifestData = new ManifestDataFilter();
                $manifestData->addIdFilterIn("'" . implode("','", $manifestIdarray) . "'");
                $manifestData->addAccountFilter(SessionManager::getUser()->getUserAccount());
                $manifestlist = $manifestData->getColumnList('account,pdf_file,pickup_id');

                if (count($manifestlist) > 0) {
                    $linkManifest = '<a href="' . $manifestlist[0]->getPdfFile() . '">' . $manifestlist[0]->getId() . '</a>';
                    $pickupid = $manifestlist[0]->getPickupId();
                    if ($pickupid > 0) {
                        $pickupData = new PickupSmart($pickupid);
                        if ($pickupData->getPickUpPdf() == "")
                            $linkpickup = formatMessages(ERROR_PICKUP);
                        else
                            $linkpickup = '<a href="' . $pickupData->getPickUpPdf() . '">' . $pickupid . '</a>';
                    }
                }
            }

            $html = '<table>
				<tr>
					<th>Manifest</th>
					<td>' . $linkManifest . '</td>
				</tr>
				<tr>
					<th>Delivery Note</th>
					<td>' . $linkpickup . '</td>
				</tr>
				<tr>
					<th>Invoice</th>
					<td>N/A</td>
				</tr>
			</table>
			';
            echo $html;
            die;
        }
        // FORM POSTED BACK?
        if (isset($this->form_vars["form_action"])) {
// load session copies of class variables
            $this->table_msg = $_SESSION["client_table_msg"];

            switch ($this->form_vars["form_action"]) {
// SHOW INVALID
// - filter to [NEW &] INVALID entries only
// - entries should be validated on import - shouldn't really see any NEW entries

                case "createmanifestall":

                    $conisgnmentIdArrayWithTrackingNumber = array();
                    $consignmentDataFilter = new ConsignmentFilter();
                    $consignmentDataFilter->addFilter("      c.is_customer_manifested = '0' AND c.awb <> '' AND c.shipment_status = '" . Consignment::STATUS_LABEL_CREATED . "' ");
                    $consignmentDataArr = $consignmentDataFilter->getColumnList(" pc.id  'parcel_id' ");
                    if (count($consignmentDataArr) > 0) {
                        foreach ($consignmentDataArr as $ke => $valConsignment)
                            $conisgnmentIdArrayWithTrackingNumber[] = $valConsignment->getParcelId();

                        $output = Manifest::manifestCreate($conisgnmentIdArrayWithTrackingNumber);
                        echo json_encode($output);
                    } else {
                        $output["STATUS"] = "error";
                        $output["MESSAGE"] = Translation::GetCaption("MANIFEST_HAS_NOT_GENERATED");
                        echo json_encode($output);

                    }

                    die;
                    break;
                case "createmanifest":
                    $delete_array = $_POST['deleteConsignments'];
                    $conisgnmentIdArrayWithTrackingNumber = array();
                    $consignmentDataFilter = new ConsignmentFilter();
                    $consignmentDataFilter->addFilter("      c.id in ('" . implode("','", $delete_array) . "') AND c.awb <> '' ");
                    $consignmentDataArr = $consignmentDataFilter->getColumnList(" pc.id  'parcel_id' ");
                    if (count($consignmentDataArr) > 0) {
                        foreach ($consignmentDataArr as $valConsignment)
                            $conisgnmentIdArrayWithTrackingNumber[] = $valConsignment->getParcelId();

                        $output = Manifest::manifestCreate($conisgnmentIdArrayWithTrackingNumber);
                        echo json_encode($output);
                    } else {
                        $output["STATUS"] = "error";
                        $output["MESSAGE"] = Translation::GetCaption("MANIFEST_HAS_NOT_GENERATED");
                        echo json_encode($output);

                    }
                    die;
                    break;

                case "export_data":


                    $delete_array = $_POST['deleteConsignments'];
                    $arr_update_scan_numbers = array();
                    $count = 0;
                    $this->consignment_filter = new ConsignmentFilter();

                    $this->consignment_filter->setRowsPerPage(5000000);
// the offset of the list, based on current page
                    $this->consignment_filter->setOffset(0);

                    if (isset($_GET['ManifestId']) && $_GET['ManifestId'] > 0) {

                        $this->consignment_filter->addManifestTableJoin();
                        //$this->consignment_filter->addFilter(" c.id = mcm.consignmentid");
                        $this->consignment_filter->addFilter("     c.user_id = '" . $user->getId() . "'", 'consignmentfilter');
                        $this->consignment_filter->addFilter("     mcm.manifest_id = '" . $_GET['ManifestId'] . "'", 'manifestfilter');
                    }


                    if (sizeof($delete_array) > 0) {

                        $this->consignment_filter->addFilter("      c.id in ('" . implode("','", $delete_array) . "') AND c.id > 0 and c.id <> '' ");
                        $this->consignment_filter->addGroupBy("c.id");
                        $consignment_array = $this->consignment_filter->getShipmentPagingListOpt();
                        $count = count($consignment_array);
                    } else {

                        /* echo "<pre>";
                        print_r($this->form_vars);
                            die;*/
                        $this->addFilters();
                        $this->consignment_filter->addGroupBy("c.id");
                        // the offset of the list, based on current page
                        $this->consignment_filter->setRowsPerPage(5000000);
                        $this->consignment_filter->setOffset(0);
                        $consignment_array = $this->consignment_filter->getShipmentPagingListOpt();

                        $count = count($consignment_array);

                    }


                    if ($count > 0) {
                        header("Content-Type: application/csv");
                        header("Content-Disposition: attachment; filename=exported_consignments.csv");
                        $csv = "";
                        $csv .= Consignment::exportHeader();
                        foreach ($consignment_array as $consignment) {
                            $csv .= $consignment->exportRow();
                        }
                        $this->table_msg = formatMessages(SUCCESS_EXPORTED_FILE);
                        echo $csv;
                        die;
                    } else {
                        $this->table_msg = formatMessages(ERROR_DATA_EXPORT);
                    }
                    break;
                case "delete":
                    $delete_array = $_POST['deleteConsignments'];
                    for ($start = 0; $start <= count($delete_array) - 1; $start++) {
                        $deleteId = $delete_array[$start];
                        $consignment = new Consignment($deleteId);
                        $consignment->delete();
                    }
                    $output["status"] = "success";
                    $output["message"] = Translation::GetCaption("MSG_SHIPMENT_DELETE");
                    echo json_encode($output);
                    die;
                    break;

                case "Hold":
                    $consignmentArray = $_POST['deleteConsignments'];
                    $consignmentRecycledResponse = Consignment::HoldShipment($consignmentArray);
                    echo json_encode($consignmentRecycledResponse);
                    die;
                    break;
                case "Unhold":
                    $consignmentArray = $_POST['deleteConsignments'];
                    $consignment = new Consignment();
                    $consignmentRecycledResponse = $consignment->UnHoldShipment($consignmentArray);
                    echo json_encode($consignmentRecycledResponse);
                    die;
                    break;
                case "btnRestore":
                    $consignmentArray = $_POST['deleteConsignments'];
                    $consignmentFilter = new ConsignmentFilter();
                    $consignmentFilter->addFilterIn('   id',$consignmentArray,"filter");
                    $consignmentFilterObjs = $consignmentFilter->getListNew('hawb');
                    $hawb = [];
                    if(count($consignmentFilterObjs)) {
                        foreach($consignmentFilterObjs as $consignmentFilterObj) {
                            $hawb[] = $consignmentFilterObj->getHawb();
                        }
                    }
                    $consignmentRecycledResponse = Consignment::RestoreShipment($hawb);
                    if(isset($consignmentRecycledResponse['MESSAGE'])) {
                        $consignmentRecycledResponse['MESSAGE'] = implode("<br />",$consignmentRecycledResponse['MESSAGE']);
                    }
                    if(isset($consignmentRecycledResponse['ERRORS']) && count($consignmentRecycledResponse['ERRORS'])) {
                        $consignmentRecycledResponse['MESSAGE'] = implode("<br />",$consignmentRecycledResponse['ERRORS']);
                    }
                    echo json_encode($consignmentRecycledResponse);
                    die;


                    break;


                case "Recycle":

                    $consignmentArray = $_POST['deleteConsignments'];
                    $consignment = new Consignment();
                    $consignmentRecycledResponse = $consignment->RecycledShipment($consignmentArray);
                    echo json_encode($consignmentRecycledResponse);
                    die;
                    break;
// unrecognised command
                default:
                    break;
            }  // switch()
        }

        if (isset($_GET['action']) && ($_GET['action'] == "get_parcel_detail")) {
            $consignmentId = $this->form_vars['consignment_id'];
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addFieldFilter("consignment_id", $consignmentId);
            $parcelList = $parcelFilter->getList();
            $html = "";
            if (count($parcelList) > 0) {
                foreach ($parcelList as $key => $parcel) {
                    $html .= "<tr>";
                    $html .= "<td> " . ($key + 1) . "</td>";
                    $html .= '<td><a href="tracking.php?tracking_number=' . $parcel->getTrackingNumber() . ' "target="_blank">' . $parcel->getTrackingNumber() . "</a></td>";
                    $html .= "<td>" . $parcel->getLength() . " x " . $parcel->getWidth() . " x " . $parcel->getHeight() . "</td>";
                    $html .= "<td>" . $parcel->getWeight() . "</td>";
                    $html .= "<td>" . ucwords(Consignment::$database_status_array[$parcel->getParcelStatusCode()]) . "</td>";
                    $html .= "</tr>";
                }
            } else {
                $html .= "<tr>";
                $html .= "<td> NO Parcel Found </td>";
                $html .= "</tr>";
            }
            echo $html;
            die;
        }
        
        
        if (isset($_POST['action']) && ($_POST['action'] == "GET_RESCHEDULE_COLLECTION")) {
            $consignment_id = $this->form_vars['consignment_id'];
            $collectionDate = $this->form_vars['collection_date'];
            $startTime = $this->form_vars['starttime'];
            $endTime = $this->form_vars['endtime'];
            $result["STATUS"] = "SUCCESS";
            if($consignment_id > 0 && $collectionDate != ''){
                $consignment  = new Consignment($consignment_id);
                $result = $consignment::getCollectionReschedule($consignment, $collectionDate, $startTime, $endTime);
            }
            else {
                $result["STATUS"] = "ERROR";
                $result["MESSAGE"] = "Please select reschedule date and time.";
            }
            echo json_encode($result);
            die;
            
            
            
            
        }

// common initialisation for ths page
        $this->setTitle("Client Shipements List");

// Check message
        if ($this->table_msg == "")
            $this->table_msg = Translation::GetCaption("SHIPMENT_LISTED");
    }


    public function addFilters()
    {

        $this->user = $user = SessionManager::getUser();
        $dataTableColumnName = $this->orderByDT;
        /*
         * Column filter
         * For search
         */
        if ((isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') OR ($this->form_vars['form_action'] == 'export_data')) 
        {
            $orderFalse = $this->orderFalse;

            $this->consignment_filter = new ConsignmentFilter();

            $searchDateFrom = $this->form_vars['search_Date_from'];
            $searchDateTo = $this->form_vars['search_Date_to'];

            if (!empty($searchDateFrom) || !empty($searchDateTo))
                $this->consignment_filter->addDateFilter($searchDateFrom, $searchDateTo, 'submitted');

            $searchAccount = $this->form_vars['search_Account'];
            if (!empty($searchAccount)) {
                $this->consignment_filter->addFilter("    ua.id ='" . trim($searchAccount) . "'", "userAccountFilter");
            }
            $searchHAWB = $this->form_vars['search_HAWB'];
            if (!empty($searchHAWB))
                $this->consignment_filter->addFieldLikeFilter('c.hawb', trim($searchHAWB));
            
            $searchSource = $this->form_vars['search_Source'];
            if (!empty($searchSource))
                $this->consignment_filter->addFieldLikeFilter('c.created_from', trim($searchSource));


            $searchName = $this->form_vars['search_Name'];
            if (!empty($searchName))
                $this->consignment_filter->addFieldLikeFilter('c.contact', trim($searchName));


            $searchCountry = $this->form_vars['search_Country'];
            if (!empty($searchCountry))

                $this->consignment_filter->addFieldEqualFilter('     c.country_id', '=', trim($searchCountry));

            
            
            $searchCity = $this->form_vars['search_City'];
            if (!empty($searchCity))
                $this->consignment_filter->addFieldLikeFilter('	  c.city', trim($searchCity));
            
            $searchWeight = $this->form_vars['search_Weight'];
            if (!empty($searchWeight))
                $this->consignment_filter->addFieldLikeFilter('	  c.weight', trim($searchWeight));

            $searchStatus = $this->form_vars['search_Status'];
            if (!empty($searchStatus)) {
                if (trim($searchStatus) == Consignment::STATUS_INTRANSIT)
                    $this->consignment_filter->addFilter("      c.shipment_status in ( '" . Consignment::STATUS_INTRANSIT . "','" . Consignment::STATUS_PARTIAL_RECEIVED . "','" . Consignment::STATUS_RECEIVED . "','" . Consignment::STATUS_PARTIAL_DISPATCHED . "','" . Consignment::STATUS_DISPATCHED . "' )");
                else if (trim($searchStatus) == Consignment::STATUS_DELIVERED)
                    $this->consignment_filter->addFilter("      c.shipment_status in ( '" . Consignment::STATUS_PARTIAL_DELIVERED . "','" . Consignment::STATUS_DELIVERED . "','" . Consignment::STATUS_CLOSE . "' )");
                else
                    $this->consignment_filter->addFilter("      c.shipment_status='" . trim($searchStatus) . "'");
            }

            $search_service = $this->form_vars['search_ServiceType'];
            if (!empty($search_service)) {
                $service = new Services($search_service);
                $isCustomized = $service->getIsCustomized();
                if ($isCustomized) {
                    $this->consignment_filter->addFilter("    c.customized_service_id ='" . trim($search_service) . "'", "consignmentfilter");
                } else {
                    $this->consignment_filter->addFilter("    c.service_id ='" . trim($search_service) . "'", "consignmentfilter");
                }
            }

            $searchTracking = $this->form_vars['search_Tracking'];
            if (!empty($searchTracking)) {
                $consignmentIds = array();
                $ParcelFilter = new ParcelFilter();
                $ParcelFilter->addFieldFilter("    p.tracking_number", trim($searchTracking));
                $ParcelData = $ParcelFilter->getColumnList("consignment_id");
                foreach ($ParcelData as $con_id) {
                    $consignmentIds[] = $con_id->getConsignmentId();
                }
                $queryStr = "";
                if (count($consignmentIds) > 0) {
                    $queryStr = " OR c.id IN ('" . implode("','", $consignmentIds) . "')";
                }
                $this->consignment_filter->addFilter("      (c.awb = '" . trim($searchTracking) . "'" . $queryStr . ")");
            }
            $searchManifestdata = $this->form_vars['search_manifestdata'];
            if (trim($searchManifestdata) == '1')
                $this->consignment_filter->addFilter('    c.is_customer_manifested <> 1');

            $searchlabel = $this->form_vars['search_label'];
            if (!empty($searchlabel))
                $this->consignment_filter->addFieldLikeFilter('      label', trim($searchlabel));

            if (trim($dataTableColumnName) != '') {
                if (trim($dataTableColumnName) == 'user_account' || trim($dataTableColumnName) == 'Account')
                    $this->consignment_filter->AddOrderBy('user_account', 'order_by', $orderFalse);
                else
                    $this->consignment_filter->AddOrderBy($dataTableColumnName, 'order_by', $orderFalse);
            } else
                $this->consignment_filter->AddOrderBy('c.date_created', 'order_by', false);
        } else {

            $this->consignment_filter = new ConsignmentFilter();
            if (!empty($_GET['show']) && $_GET['show'] == 'cs_ready_print_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(Consignment::STATUS_READY_TO_PRINT));
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_deleted_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(
                    Consignment::STATUS_RECYCLED));
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_intransit_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(
                    Consignment::STATUS_RETURNED, Consignment::STATUS_CANCELLED, Consignment::STATUS_CLOSE,
                    Consignment::STATUS_INTRANSIT, Consignment::STATUS_PARTIAL_DISPATCHED, Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_RECEIVED, Consignment::STATUS_PARTIAL_RECEIVED));
               // $this->consignment_filter->addOrFilter('c.date_booked !=""');
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_hold_problem_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(Consignment::STATUS_HOLD, Consignment::STATUS_PROBLEM));
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_not_delivered_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(Consignment::STATUS_INTRANSIT));
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_delivered_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(Consignment::STATUS_DELIVERED, Consignment::STATUS_PARTIAL_DELIVERED));
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_label_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(Consignment::STATUS_LABEL_CREATED));
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_total_shipment') {
                $this->consignment_filter->addStatusFilterIn(array(
                    Consignment::STATUS_LABEL_CREATED, Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_RECEIVED, Consignment::STATUS_PARTIAL_RECEIVED, Consignment::STATUS_DISPATCHED, Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT, Consignment::STATUS_DELIVERED, Consignment::STATUS_PARTIAL_DELIVERED, Consignment::STATUS_CLOSE,
                    Consignment::STATUS_CANCELLED, Consignment::STATUS_HOLD, Consignment::STATUS_PROBLEM, Consignment::STATUS_RELABLED, Consignment::STATUS_RETURNED,
                    Consignment::STATUS_DISCREPANCY, Consignment::STATUS_AWATING_CLAIM));
            } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_order_shipment') {
                $this->consignment_filter->addStatusFilterIn(array('10', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'));
            } else {
                $this->consignment_filter->addStatusFilterIn(array(
                    Consignment::STATUS_NEW, Consignment::STATUS_INVALID, Consignment::STATUS_READY_TO_PRINT, Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_RECEIVED, Consignment::STATUS_PARTIAL_RECEIVED, Consignment::STATUS_DISPATCHED, Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT, Consignment::STATUS_DELIVERED, Consignment::STATUS_PARTIAL_DELIVERED, Consignment::STATUS_CLOSE, Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_CANCELLED, Consignment::STATUS_HOLD, Consignment::STATUS_PROBLEM, Consignment::STATUS_RELABLED, Consignment::STATUS_RETURNED,
                    Consignment::STATUS_DISCREPANCY, Consignment::STATUS_AWATING_CLAIM));
            }
            // Include all account when the login user is admin=
            if (trim($dataTableColumnName) != '') {
                if (trim($dataTableColumnName) == 'user_account' || trim($dataTableColumnName) == 'Account')
                    $this->consignment_filter->AddOrderBy('user_account', 'order_by', $orderFalse);
                else
                    $this->consignment_filter->AddOrderBy($dataTableColumnName, 'order_by', $orderFalse);
            } else
                $this->consignment_filter->AddOrderBy('c.date_created', 'order_by', false);
        }

        if (isset($_REQUEST['show']) && trim($_REQUEST['show']) == 'show_lsd') {

            $this->consignment_filter->addFilter("      c.shipment_status in ('" .
                Consignment::STATUS_LABEL_CREATED . "', '" .
                Consignment::STATUS_DISPATCHED . "', '" .
                Consignment::STATUS_PARTIAL_DISPATCHED . "', '" .
                Consignment::STATUS_RECEIVED . "', '" .
                Consignment::STATUS_PARTIAL_RECEIVED . "', '" .
                Consignment::STATUS_PARTIAL_DELIVERED . "', '" .
                Consignment::STATUS_INTRANSIT . "', '" .
                Consignment::STATUS_CLOSE . "', '" .
                Consignment::STATUS_DELIVERED . "')", "consignmentfilter");
        }

        if ($this->saccount > 0) {
            if (trim($this->filtertype) != '') {
                $conArrayStatus = array(
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_RECEIVED, Consignment::STATUS_PARTIAL_RECEIVED,
                    Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_PARTIAL_DELIVERED,
                    Consignment::STATUS_CLOSE,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_PROBLEM,
                    Consignment::STATUS_RELABLED,
                    Consignment::STATUS_DISCREPANCY,
                    Consignment::STATUS_AWATING_CLAIM);
                $this->consignment_filter->addFilter("      c.shipment_status in ('" . implode("','", $conArrayStatus) . "')", "consignmentfilter");
            }


            switch (trim($this->filtertype)) {
                case 'sub':
                    $this->consignment_filter->addFilter(" c.date_label_created > 0 ", 'consignmentfilter');
                    $userAccountArry = CustomerAccount::accountSubAccount($this->saccount);
                    $this->consignment_filter->addFilter("     u.user_account_id in ('" . implode("','", $userAccountArry) . "')", 'userfilter');
                    break;
                case 'all':
                    $this->consignment_filter->addFilter("    c.date_label_created > 0 ", 'consignmentfilter');
                    $userAccountArry = CustomerAccount::accountSubAccount($this->saccount, 0, true);
                    $this->consignment_filter->addFilter("     u.user_account_id in ('" . implode("','", $userAccountArry) . "')", 'userfilter');
                    break;
                case 'own':
                    $this->consignment_filter->addFilter("    c.date_label_created > 0 ", 'consignmentfilter');
                    $this->consignment_filter->addFilter('     u.user_account_id ="' . $this->saccount . '"', 'userfilter');
                    break;
                default:
                    $this->consignment_filter->addFilter('    u.user_account_id ="' . $this->saccount . '"', 'userfilter');
                    break;
            }
        } else if ($this->uaccount > 0) {
            $this->consignment_filter->addFilter('     c.user_id ="' . $this->uaccount . '"', 'consignmentfilter');
        } else if ($user->getUserType() == User::USER_TYPE_ADMIN) {

        } else if ($user->getUserType() == User::USER_TYPE_CORPORATE) {
            if ($this->saccount > 0) {

            } else {
//                    $allUserClients = $this->userClients();
                $allouedAcccounts = [];
                if (!empty($_GET['show']) && $_GET['show'] == 'cs_order_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_ready_print_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_deleted_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_intransit_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_label_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_total_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_hold_problem_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_not_delivered_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else if (!empty($_GET['show']) && $_GET['show'] == 'cs_delivered_shipment') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user->getUserAccountId(), 0, true);
                } else {
                    $allouedAcccounts[] = $user->getUserAccountId();
                }
                //$this->consignment_filter->addFilter('     u.user_account_id ="' . $user->getUserAccountId() . '"', 'userfilter');
                $this->consignment_filter->addFilter('     u.user_account_id IN (' . implode(",", $allouedAcccounts) . ')', 'userfilter');
            }
        } else {
            // only in clude individual account
            $this->consignment_filter->addAccountFilter($user->getId());
        }

        if ($this->record == "today") {
            $toDayDate = date("Y-m-d");
            $this->consignment_filter->addDateFilter($toDayDate, $toDayDate, 'submitted');
        }

        if ($this->record == "week") {
            $fromDate = date("Y-m-d");
            $toDate = date('Y-m-d', strtotime("+1 week"));
            $this->consignment_filter->addDateFilter($fromDate, $toDate, 'submitted');
        }

        // set fresh filter for PRINTED, RECEIVED, HELD
        if (isset($this->form_vars['selected_manifest']) && $this->form_vars['selected_manifest'] == "show") {
            //$this->consignment_filter = new ConsignmentFilter();
            if ($this->uaccount > 0)
                $this->consignment_filter->addFilter("     c.user_id = '" . $this->uaccount . "'", 'consignmentfilter');
            else
                $this->consignment_filter->addFilter("      c.user_id = '" . $user->getId() . "'", 'consignmentfilter');
            $fromDate = date("Y-m-d", strtotime("-2 month"));
            $dateTo = date("Y-m-d 23:59:00");
            $this->consignment_filter->addDateFilter($fromDate, $dateTo, 'printed');
            $this->consignment_filter->addIsCustomerManifested();
            $this->consignment_filter->addFieldNotFilter("    awb", "");
            $this->consignment_filter->addStatusFilter(Consignment::STATUS_LABEL_CREATED);
            $this->consignment_filter->AddOrderBy('id', 'order_by_consignment', false);
            //print_r($this->consignment_filter);
        } else
            if (isset($_GET['ManifestId']) && $_GET['ManifestId'] > 0) {
                $this->consignment_filter = new ConsignmentFilter();
                $this->consignment_filter->addManifestTableJoin();
                //$this->consignment_filter->addFilter(" c.id = mcm.consignmentid");
                $this->consignment_filter->addFilter("     c.user_id = '" . $user->getId() . "'", 'consignmentfilter');
                $this->consignment_filter->addFilter("     mcm.manifest_id = '" . $_GET['ManifestId'] . "'", 'manifestfilter');
            }

    }

    /*
     * Getting list for user that are under particular parent account
     * * */

    public function userClients()
    {

        $childUserId = array();
        $userParentFilter = new UserAccountFilter();
        $userParentFilter->addParentidFilter($this->user->getId());
        $straccoutnArray = '';

        $userParentResult = $userParentFilter->getList();

        if (count($userParentResult) > 0) {

            $childUserId = array();
            $childUserId[] = $user->getUserAccount();
            foreach ($userParentResult as $userData)
                $childUserId[] = $userData->getUserAccount();
            $straccoutnArray = "'" . implode("','", $childUserId) . "'";
        }
        return $straccoutnArray;
    }

    public function getShipmentListById($id = array())
    {
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFilter(" c.id in (" . implode(",", $id) . ")");
        $consignmentList = $consignmentFilter->getColumnList("c.id,c.country_id, s.service_id, hawb, awb, sender_checked, c.type, c.shipment_status, c.consignment_status, c.user_id , c.message");
        return $consignmentList;
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead()
    {

    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/js-custom-forms/css/theme-minimal/jcf.css" rel="stylesheet"
              type="text/css"/>
        <style type="text/css">
            .help-block-error {
                display: none !important;
            }


        </style>


        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript"
                src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/plugins/js-custom-forms/js/jcf.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/js-custom-forms/js/jcf.scrollable.js" type="text/javascript"></script>
        <script src="../js/generate-bulk-labels.js" type="text/javascript"></script>
        <?php

    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        $user = $this->user;
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>


        <div class="main_formpage">
            <?php
            if ($user->getRetailCustomer() != 1) {
                $consignmentFile = 'consignment_add.php';
            } else {
                $consignmentFile = 'booking_quote.php';
            }
            ?>
            <div class="portlet light">
                <div class="portlet-title">

                    <div class="caption"><i class="icon-list"></i>
                      
            <?php
            if ($_REQUEST['show'] != 'collection_selected' && $_REQUEST['show'] != 'hold_parcels') {
                echo Translation::GetCaption("SHIPMENT_LIST");
                if (!empty($this->uaccountCompanyName)) {
                    echo " of " . $this->uaccountCompanyName;
                }
            } else {
                echo Translation::GetCaption("LIST_OF_ALL_COLLECTIONS");
            }
            ?>
                        
                    </div>
                    <div class="actions">
                        <?php if($this->userAccount->getFtpShipmentUpload()) { ?>
                        <a href="upload_shipment_via_ftp.php" class="btn blue margin-bottom-5">
                            <i class="fa fa-upload"></i>
                            <?= Translation::GetCaption("Upload Shipment Via ftp") ?>
                        </a>
                        <?php } ?>
                        <?php if (Permissions::checkFilePermission('RETAIL_CUSTOMER')): ?>
                            <a href="consignment_quote.php"
                               class="btn blue margin-bottom-5">
                                <i class="fa fa-plus"></i> Quote & Ship</a>
                        <?php endif;
                         if (Permissions::checkFilePermission('consignment_add.php')): ?>
                            <a id="add_consignment" href="<?= $consignmentFile ?><?php echo(util_get('uaccount') != '' ? "?uaccount=" . util_get('uaccount') : '') ?>"
                               class="btn blue margin-bottom-5">
                                <i class="fa fa-plus"></i> <?= Translation::GetCaption("ADD_NEW_SHIPMENT") ?> </a>
                        <?php endif;
                        if (Permissions::checkFilePermission('import.php')): ?>
                            <a href="client_file_con.php" class="btn blue margin-bottom-5">
                                <i class="fa fa-upload"></i> <?= Translation::GetCaption("IMPORT") ?> </a>
                        <?php endif;
                        if (Permissions::checkFilePermission('show_address.php')): ?>
                            <a href="show_address.php" class="btn blue margin-bottom-5">
                                <i class="fa fa-building-o"></i> Manage Address </a>
                        <?php endif; ?>
                        <a href="javascript:;" class="collapse btn btn-circle btn-icon-only btn-default hidden"
                           data-original-title="" title=""> </a>
                        <a href="" class="btn btn-circle btn-icon-only btn-default fullscreen hidden"
                           data-original-title="" title=""> </a>
                        <a href="#portlet-config" data-toggle="modal"
                           class="btn btn-circle btn-icon-only btn-default hidden"><i class="icon-wrench"></i></a>

                    </div>
                </div>


                <div class="portlet-body">
                    <!-- Testing Export Excel bar  -->
                    <div class="export-excel-msg" id="export-terminal">
                        <div class="cancel" title="Close" onclick="cancelTerminal()">Close</div>
                        <div class="jcf-scrollable">
                            <ul id="export-terminal-msgs"></ul>
                            <span id="wait">.</span>
                        </div>
                    </div>
                    <?php
                    if (count(ErrorList::getItem()->getErrorCount()) > 1) {

                        echo '<div class="alert alert-danger">';
                        ErrorList::getItem()->render();
                        echo '</div>';
                    }
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <fieldset>
                                <ul class="nav nav-pills">
                                    <?php if (Permissions::checkFilePermission('manifest')): ?>
                                        <li>
                                            <input class="btn blue btn-outline margin-bottom-5" id="btnManifestShow"
                                                   name="btnManifestShow" type="button"
                                                   value="<?php echo Translation::GetCaption("MANIFEST"); ?>"/>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($_GET["show"] == "hold_parcels") { ?>
                                        <li>
                                            <input class="btn blue btn-outline margin-bottom-5" id="btnUnHold"
                                                   name="btnUnHold" type="button"
                                                   value="<?php echo Translation::GetCaption("UNHOLD"); ?>"/>
                                        </li>
                                    <?php }
                                    if ($_GET["show"] != "collection_selected") {
                                        ?>
                                        <?php if (Permissions::checkFilePermission('label')): ?>
                                            <li><input class="btn blue btn-outline margin-bottom-5" id="btnLabelShow"
                                                       name="btnLabelShow" type="button"
                                                       value="<?php echo Translation::GetCaption("LABELS"); ?>" <?= ($this->shipmentValid <= 0) ? 'disabled' : '' ?>/>
                                            </li>
                                        <?php endif; ?>
                                    <?php } else { ?>
                                        <input class="btn blue btn-outline margin-bottom-5" id="btnCollectionSelected"
                                               name="btnCollectionSelected"
                                               type="button"
                                               value="<?php echo Translation::GetCaption('SELECT_SHIPMENT'); ?>"/>
                                        <input class="btn blue btn-outline margin-bottom-5" id="btnCollectionAll"
                                               name="btnCollectionAll"
                                               type="button"
                                               value="<?php echo Translation::GetCaption("ALL_SHIPMENTS"); ?>"/>

                                        <?php
                                    }
                                    ?>

                                    <?php if (Permissions::checkFilePermission('merge_existing_label')): ?>
                                        <li class="<?php if ($_SESSION["status"] == array(Consignment::STATUS_LABEL_CREATED, Consignment::STATUS_HOLD)) echo "active"; ?>">
                                            <input class="btn blue btn-outline margin-bottom-5" id="btnLabelMerge"
                                                   name="btnLabelMerge" type="button"
                                                   value="<?php echo Translation::GetCaption("MERGE_EXISTING_LABEL"); ?>"/>
                                        </li>
                                    <?php endif; ?>
                                    <li><input href="fetch_label.php" class="btn blue btn-outline margin-bottom-5"
                                               id="btnLabelFetch" name="btnLabelFetch" type="button" value="Fetch Label"
                                               onclick=" window.location='fetch_label.php'"/></li>
                                </ul>
                            </fieldset>
                        </div>
                        <div class="col-md-6" id="ShowLabel" style="display:none;">
                            <fieldset>
                                <ul class="nav nav-pills pull-right">
                                    <?php if (Permissions::checkFilePermission('m_s_printed_selected')): ?>
                                        <li class="">
                                            <input class="btn blue btn-outline margin-bottom-5" id="btnLabelCreate"
                                                   name="btnLabelCreate" type="button"
                                                   value="<?php echo Translation::GetCaption("PRINT_SELECTED"); ?>"/>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (Permissions::checkFilePermission('m_s_printed_all')): ?>
                                        <li class=""><input id="btnBatchSingleLabel" name="btnBatchSingleLabel"
                                                            class="btn blue btn-outline margin-bottom-5" type="button"
                                                            value="<?php echo Translation::GetCaption("PRINT_ALL"); ?>"/>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </fieldset>
                        </div>

                        <div class="col-md-6" id="ShowManifest" style="display: none;">
                            <fieldset>
                                <ul class="nav nav-pills pull-right">
                                    <?php if (Permissions::checkFilePermission('m_s_manife_stselected')): ?>

                                        <li>

                                            <input class="btn blue btn-outline margin-bottom-5" id="btnManifestSelected"
                                                   name="btnManifestSelected" type="button"
                                                   value="<?php echo Translation::GetCaption("MANIFEST_SELECTEDS"); ?>"/>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (Permissions::checkFilePermission('m_s_manife_all')): ?>
                                        <li>

                                            <input class="btn blue btn-outline margin-bottom-5" id="btnManifestAll"
                                                   name="btnManifestAll" type="button"
                                                   value="<?php echo Translation::GetCaption("MANIFEST_ALL"); ?>"/>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (Permissions::checkFilePermission('coclient_user_endofday.php')): ?>
                                        <li>

                                            <input class="btn blue btn-outline margin-bottom-5" id="btnManifestList"
                                                   name="btnManifestList" type="button"
                                                   onclick="window.location = 'coclient_user_endofday.php';"
                                                   value="<?php echo Translation::GetCaption("LIST_OF_ALL_MANIFESTS"); ?>"/>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </fieldset>
                        </div>

                        <div class="col-md-6" id="ShowPickup" style="<?php
                        if (isset($_GET['show']) && $_GET['show'] == 'collection_selected')
                            echo '';
                        else
                            echo 'display:none;'
                        ?>">
                            <fieldset>
                                <ul class="nav nav-pills">
                                    <li>


                                        <input class="btn blue btn-outline margin-bottom-5" id="btnCollectionHistory"
                                               name="btnCollectionHistory"
                                               type="button"
                                               value="<?php echo Translation::GetCaption("COLLECTION_HISTORY"); ?>"/>
                                        <input class="btn blue btn-outline margin-bottom-5" id="btnPutOnHold"
                                               name="btnPutOnHold"
                                               type="button" value="<?php echo Translation::GetCaption("HOLD"); ?>"/>
                                    </li>
                                </ul>
                            </fieldset>
                        </div>
                    </div>
                    <hr/>

                    <div class="row">
                        <div class="col-md-12 alert alert-danger display-none" id="res_message"></div>
                    </div>

                    <input type="hidden" id="total_valid_shipment" name="total_valid_shipment"
                           value="<?php echo $this->shipmentValid; ?>"/>
                    <!--Hadi Work-->
                    <form name="adminForm" action="" method="POST" id="adminForm">
                        <div class="table-responsive table-container">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="manage-client">


                                    <div class="table-group-actions pull-right">
                                        <span></span>
                                        <?php
                                        $otherOption = array();
                                        if (Permissions::checkFilePermission('export') && $_REQUEST['show'] != 'collection_selected')
                                            $otherOption['Export'] = Translation::GetCaption("EXPORT");
                                        if (Permissions::checkFilePermission('delete')) {
                                            $otherOption['Delete'] = Translation::GetCaption("DELETE");
                                        }
                                        if (Permissions::checkFilePermission('unhold'))
                                            $otherOption['Unhold'] = Translation::GetCaption("UNHOLD");
                                        if (Permissions::checkFilePermission('hold'))
                                            $otherOption['Hold'] = Translation::GetCaption("HOLD");
                                        if (Permissions::checkFilePermission('restore') && $user->getThemeId() != 1)
                                            $otherOption['Restore'] = Translation::GetCaption("RESTORE");
                                        if (!isset($_GET['ManifestId']))
                                            echo Ddl::generateArrayDDL('other_options', $otherOption, $other_options, 'ACTION', 'class="table-group-action-input form-control input-inline input-small input-sm" onchange="other_option_action(this.value)"');
                                        ?>
                                    </div>
                                    <thead>
                                    <tr class="heading">


                                        <th width="4%">
                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type='checkbox' name='checkall' onclick='checkedAll(delete55);'
                                                       class="group-checkable"/>
                                                <span></span>
                                            </label>
                                        </th>


                                        <th width="10%">
                                            Created
                                        </th>

                                        <?php if ($user->getUserType() == User::USER_TYPE_ADMIN || $user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                                            <th>
                                                Accounts
                                            </th>

                                        <?php } ?>
                                        
                                        <th>
                                            Source<br />
                                            HAWB
                                        </th>

                                        <th>
                                            Contact
                                        </th>

                                        <th>
                                            City <br /> Country
                                        </th>

                                        <th>
                                            Weight (Kg)
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                        <th>
                                            Service
                                        </th>

                                        <th>
                                            Tracking No
                                        </th>

                                        <th>
                                            Reason
                                        </th>

                                        <th>
                                            Actions
                                        </th>

                                    </tr>

                                    <tr role="row" class="filter">
                                        <td>
                                            <div class="margin-bottom-5">
                                                <button class="btn btn-sm yellow filter-submit margin-bottom"><i
                                                            class="fa fa-search"></i></button>
                                                <input type="hidden" class="form-filter" name="selected_manifest"
                                                       id="selected_manifest" value=""/>
                                            </div>
                                            <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i>
                                            </button>
                                        </td>

                                        <td>
                                            <div class="input-group date date-picker margin-bottom-5"
                                                 data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly
                                                       name="search_Date_from" placeholder="From"
                                                       data-date-format="yyyy-mm-dd">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                            </div>
                                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly
                                                       name="search_Date_to" placeholder="To"
                                                       data-date-format="yyyy-mm-dd">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                            </div>
                                        </td>

                                        <?php
                                        if ($user->getUserType() == User::USER_TYPE_ADMIN || $user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                                            <td class="user_acccount_correct_button">
                                                <?php
                                                $accountParentId = 0;
                                                if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                                    $accountParentId = $this->user->getUserAccountId();
                                                $selectedAccount = "";
                                                if (!empty($_GET['account']) && (int)trim($_GET['account']) > 0)
                                                    $selectedAccount = (int)trim($_GET['account']);
                                                $allowedLevel = 0;
                                                if (Permissions::checkFilePermission('hide_subaccount')) {
                                                    $allowedLevel = 1;
                                                }
                                                echo Ddl::showTreeDropdown('search_Account', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel);
                                                ?>
                                            </td>

                                        <?php } ?>
                                        
                                        <td>
                                            <div class="input-group margin-bottom-5">
                                            <input type="text" placeholder="Source" class="form-control form-filter input-sm"
                                                   name="search_Source"></div>
                                                
                                            </div>
                                            <div class="input-group margin-bottom-5">
                                            <input type="text" placeholder="HAWB" class="form-control form-filter input-sm"
                                                   name="search_HAWB"></div>
                                        </td>

                                        <td>
                                            <input type="text" placeholder="Contact" class="form-control form-filter input-sm"
                                                   name="search_Name">
                                        </td>
                                        <td class="user_acccount_correct_button">
                                            <div class="input-group margin-bottom-5">
                                                <input type="text" placeholder="City" class="form-control form-filter input-sm"
                                                   name="search_City">
                                                
                                            </div>
                                            <div class="input-group margin-bottom-5"><?php echo Ddl::generateCountryDDL('search_Country', '', '', ' class="form-filter bs-select form-control input-sm" data-live-search="true"'); ?></div>

                                        </td>
                                        <td>
                                            <input type="text" placeholder="Weight" class="form-control form-filter input-sm"
                                                   name="search_Weight">
                                        </td>
                                        <td>

                                            <?php $this->statusToUser(); ?>
                                            <input type="hidden" class="form-control form-filter input-sm"
                                                   name="search_manifestdata" id="search_manifestdata" value="0">

                                        </td>
                                        <td>
                                            <input type="hidden" class="form-control form-filter input-sm"
                                                   name="search_product" id="search_product">
                                            <input type="hidden" class="form-control form-filter input-sm"
                                                   name="search_service" id="search_service">
                                            <!-- We have skipped this because its comming by inner join-->

                                            <?php
                                            $sql = "SELECT
                                                            service_id 'id', name 'name', IF(is_customized = 0, 'service','product' ) AS 'service_type'
                                                        FROM
                                                            user_services_routing usr
                                                                INNER JOIN
                                                            services s ON usr.service_id = s.id
                                                                AND user_account_id = '" . $user->getUserAccountId() . "'
                                                        GROUP BY service_id ";


                                           
                                            echo Ddl::generateDDLFromSql         ($sql, 'search_ServiceType', 'name', 'id', '', ' class="form-filter form-control input-sm select2 searchbox"', 'Service', '', '', '', array('service_type' => 'service_type'));
                                           // echo Ddl::generateServiceDDLWithImage('service', $selected_value, 'id', ' class="bs-select input-sm form-control form-filter" required="" data-live-search="true" data-show-subtext="true" ', '', '', 'name', 'Select Services');
                                            ?>

                                        </td>
                                        <td>
                                            <input type="text" placeholder="Tracking No" class="form-control form-filter input-sm"
                                                   name="search_Tracking">
                                        </td>
                                        <td>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="form_action" id="form_action"
                                   value="<?php echo !empty($form_action) && preg_match('/^[a-zA-Z0-9_ \d]+$/', $form_action) ? $form_action : ''; ?>"/>
                            <input type="hidden" id='total_weight_sum' name='total_weight_sum' value=""/>
                            <input type="hidden" name="collection_type" id="collection_type" value=""/>
                    </form>
                    <!--End Hadi Work-->
                </div>
            </div>  <!-- table_container -->
        </div>
        </form>

        <div class="modal fade  bs-modal-lg" id="print-labels-popup" data-backdrop="static" data-keyboard="false"
             tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close modal-close" data-dismiss="modal"
                                aria-hidden="true"></button>
                        <h4 class="modal-title">Label Confirmation</h4>
                    </div>
                    <form class="form-horizontal" role="form" action="" method="POST" name="frm_layout">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" id="print-labels-message">

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="button" id="generate-labels-new" type="button" class="btn blue"
                                   value="<?= Translation::GetCaption("GENERATE_LABEL") ?>"/>
                            <button type="button" class="btn default modal-close" id="btncloselabel"
                                    data-dismiss="modal">Close
                            </button>
                            <input type="hidden" name="save_layout" value="layout_editor"/>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="parcel_list_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Parcel List [ <span class="tracking_number_heading"></span> ]</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th> #</th>
                                    <th> Tracking Number</th>
                                    <th> Dims</th>
                                    <th> Weight</th>
                                    <th> Status</th>
                                </tr>
                                </thead>
                                <tbody id="parcel_list_data">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        
        <!----------------------- Collection reschedule Modal ---------------------------->
        <!--Model for carrier details-->
        <div class="modal fade" role="dialog" id="collection-reschedule-popup" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Reschedule Collection</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="consignment_id" name="consignment_id">
                        <div class="row" >
                            <div class="col-md-12"  id="model-cd-content-display">
                                <div class="row">
                                      <div class="col-md-12 alert alert-danger display-none"  id="collection_mess" ></div>
                                </div>
                                <div class="row">
                                    
                                    <div class="col-md-3">
                                        <div class="input-icon has-float-label right">
                                            <i class="fa fa-calendar"></i>
                                             <input data-date-format="dd-mm-yyyy" type="text" size="16" class="form-control collectiondatepicker" name="collection_date" value="<?php echo formatDate($collection_date); ?>" id="collection_date"  placeholder="Collection Date"/>
                                                                                     <label for="collection_date">Date</label>
                                         </div>
                                     </div>
                                     <div class="col-md-3">
                                         <div class="input-icon has-float-label right">
                                             <i class="fa fa-clock-o"></i>
                                             <input type="text" class="form-control timepicker timepicker-default1" name="collection_start_time" value="<?php echo $collection_start_time; ?>" id="collection_start_time"  placeholder="Collection Start Time"/>
                                             <label for="collection_start_time">Start Time</label>
                                         </div>
                                     </div>
                                     <div class="col-md-3">
                                         <div class="input-icon has-float-label right">
                                             <i class="fa fa-clock-o"></i>
                                             <input type="text" class="form-control timepicker timepicker-default2" name="collection_end_time" value="<?php echo $collection_end_time; ?>" id="collection_end_time"  placeholder="Collection End Time"/>
                                             <label for="collection_end_time">End Time</label>
                                         </div>
                                     </div>
                                </div>
                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <a  id="btnreschedule" class="btn btn-sm blue"><span></span><i class="fa fa-calendar"></i>&nbsp;Reschedule</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>

        <?php
}

    public function renderFooter()
    {
        $user = $this->user;
        ?>
        <style type="text/css">
            .label_td {
                text-align: center !important;
            }

            }
        </style>
        <!--Hadi Code-->
        <script type="text/javascript">
            //CUSTOM DATA TABLE
            var grid = null;
            var clients = function () {
                var handleClients = function () {
                    grid = new Datatable();
                    var queryStr = "";
                    var conIdArr = [];
                    <?php
                    if (isset($_GET['ManifestId']) && $_GET['ManifestId'] > 0) {
                    ?>
                    queryStr = "&ManifestId=<?php echo $_GET['ManifestId']; ?>";
                    <?php
                    }
                    if (isset($_GET['saccount'])) {
                    ?>
                    queryStr += "&saccount=<?php echo $_GET['saccount']; ?>";
                    <?php
                    if (isset($_GET['filtertype'])) {
                    ?>
                    queryStr += "&filtertype=<?php echo $_GET['filtertype']; ?>";
                    <?php
                    }
                    }
                    if (isset($_GET['uaccount'])) {
                    ?>
                    queryStr += "&uaccount=<?php echo $_GET['uaccount']; ?>";
                    <?php
                    }
                    if (isset($_GET['show'])) {
                    ?>
                    queryStr += "&show=<?php echo $_GET['show']; ?>";
                    <?php
                    }

                    if (isset($_GET['record'])) {
                    ?>
                    queryStr += "&record=<?php echo $_GET['record']; ?>";
                    <?php
                    }

                    ?>
                    grid.init({
                        src: $("#manage-client"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // window.location.href = 'login.php';// execute some code on network or other general error
                        },

                        dataTable: {
                            "fnDrawCallback": function () {
                                var rows = this.fnGetData();
                                if (rows.length === 0) {
                                    if ($("#search_manifestdata").val() == '1') {
                                        $("#btnManifestSelected").hide();
                                        $("#btnManifestAll").hide();
                                    }
                                } else {
                                    if ($("#search_manifestdata").val() == '1') {
                                        $("#btnManifestSelected").show();
                                        $("#btnManifestAll").show();
                                    }
                                }
                            },

                            // here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "client_list.php?action=consignment_ajax" + queryStr, // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [{
                                "data": "option", "bSortable": false
                            },
                                {
                                    "data": "date_created", 'sClass': 'date-created'
                                },
                                <?php if ($user->getUserType() == User::USER_TYPE_ADMIN || $user->getUserType() == User::USER_TYPE_CORPORATE) {
                                ?>
                                {
                                    "data": "Account", 'sClass': 'account'
                                },
                                <?php } ?>
                                {
                                     "data": "Source", 'sClass': 'Source',
                                    "data": "HAWB", 'sClass': 'hawb'
                                },
                                {
                                    "data": "contact", 'sClass': 'name'
                                },
                                {
                                    "data": "city", 'sClass': 'city',
                                    "data": "country_iso_code", 'sClass': 'country'
                                },
                                {
                                    "data": "Weight", 'sClass': 'weight'
                                },
                                {
                                    "data": "shipment_status", 'sClass': 'status'
                                },
                                {
                                    "data": "service_type", 'sClass': 'serviceType'
                                },
                                {
                                    "data": "awb", 'sClass': 'tracking'
                                },
                                {
                                    "data": "Reason", 'sClass': 'reason', "bSortable": false
                                },
                                {
                                    "data": "label", 'sClass': 'label_td', "bSortable": false
                                },
                            ],
                            "initComplete": function (settings, json) {
                                $('p').tooltip();
                                $("[data-toggle='tooltip']").tooltip();
                            },
                            "fnDrawCallback": function (oSettings) {
                                $('p').tooltip();
                                $("[data-toggle='tooltip']").tooltip();
                                if (oSettings._iRecordsTotal == 0) {
                                    $("#ShowLabel").hide();
                                }
                            }
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleClients();
                    }
                };
            }();
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
            $(document).ready(function () {
                clients.init();
                if ($('.date-picker').length > 0) {
                     $('.collectiondatepicker').datepicker({
                        autoclose: true,
                        format: 'dd-mm-yyyy',
                        startDate: "today",
                    });
                    //init date pickers
                    var today = new Date();
                    $('.date-picker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true,
                        endDate: "today",
                        maxDate: today
                    }).on('changeDate', function (ev) {
                        $(this).datepicker('hide');
                    });


                    $('.date-picker').keyup(function () {
                        if (this.value.match(/[^0-9]/g)) {
                            this.value = this.value.replace(/[^0-9^-]/g, '');
                        }
                    });
                    
                    
                    if (jQuery().timepicker) {
                    $('.timepicker-default1').timepicker({
                        autoclose: true,
                        minuteStep: 5,
                        showSeconds: false,
                        showMeridian: false
                    });
                    $('.timepicker-default2').timepicker({
                        autoclose: true,
                        minuteStep: 5,
                        showSeconds: false,
                        showMeridian: false
                    });
                }
                }
            });
            //End Hadi Code
            var count = 0;

            function showlabels(consignmentid) {
                $('.loading-' + consignmentid).show();
                $.ajax({
                    url: "ajaxlabel.php",
                    data: {
                        consignmentid: consignmentid,
                        action: 'GENERATELABEL'
                    },
                    type: "POST",
                    dataType: "json",
                    async: true,
                })
                // Code to run if the request succeeds (is done);
                // The response is passed to the function
                    .done(function (json) {
                        if (json.status == 'SUCCESS') {
                            var dataJson = json.DATA;
                            $.each(dataJson, function (arrayID, dataArray) {
                                if (dataArray.STATUS == 'SUCCESS') {
                                    awbnumber = dataArray.AWB;
                                    hawbnumber = dataArray.HAWB;
                                    $('#labelDisplayLink-' + consignmentid).attr('onclick', 'displayLabel(\'' + dataArray.ID + '\')');

                                    $('#labelDisplayLink-' + consignmentid).html('<i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="<?php echo Translation::GetCaption("VIEW_LABEL");?>"> </i>');
                                    $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm bg-blue-chambray bg-font-blue-chambray"><?= Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_LABEL_CREATED]) ?></span>');
                                    $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.tracking').html('<a href="tracking.php?tracking_number=' + awbnumber + ' "target="_blank">' + awbnumber + '</a>');
                                    $("[data-toggle='tooltip']").tooltip();
                                } else {
                                    $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.reason').html(dataArray.MESSAGE);
                                    $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm label-warning"><?= Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) ?></span>');
                                }

                            });
                        } else {
                            $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.reason').html(dataArray.MESSAGE);
                            $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm label-warning"><?= Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) ?></span>');
                        }
                        $('.loading-' + consignmentid).hide();
                    })
                    // Code to run if the request fails; the raw request and
                    // status codes are passed to the function
                    .fail(function (xhr, status, errorThrown) {
                        $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.reason').html("Error: " + errorThrown);
                        $('#labelDisplayLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm label-warning"><?= Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) ?></span>');
                        console.log("Error: " + errorThrown);
                        console.log("Status: " + status);
                        console.dir(xhr);
                        $('.loading-' + consignmentid).hide();
                    })
                    // Code to run regardless of success or failure;
                    .always(function (xhr, status) {
                        // alert( "The request is complete!" );
                    });
            }

            function popupwindow(url, title, w, h) {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }

            function displayLabel(consignmentid, recordid) {

                $.ajax({
                    url: "ajaxlabel.php",
                    data: {
                        consignmentid: consignmentid,
                        action: 'SHOWLABEL'
                    },
                    type: "POST",
                    dataType: "json",
                    async: false,
                })
                // Code to run if the request succeeds (is done);
                // The response is passed to the function
                    .done(function (json) {
                        if (json.STATUS != "ERROR") {
                            var labelLink = json.LABEL;
                            var labels = labelLink.replace("\/", "/");
                            popupwindow(labels, 'Label View', 550, 400);
                        } else {
                            swal("", json.MESSAGE, "info");
                        }
                    })
                    .fail(function (xhr, status, errorThrown) {
                        swal("", "Please try again later", "info");
                        console.log("Error: " + errorThrown);
                        console.log("Status: " + status);
                        console.dir(xhr);
                    });
            }

            function checkedAll(group) {
                if (count == 0) {
                    for (var i = 0, len = group.length; i < len; i++) {
                        if (group[i].checked == false)
                            group[i].click(); //checked = true;
                        count = 1;
                    }
                } else {
                    for (var i = 0, len = group.length; i < len; i++) {
                        if (group[i].checked == true)
                            group[i].click(); // = false;
                        count = 0;
                    }
                }
            }

            var conIdArr = [];
            $(document).ready(function () {
                jQuery(".tooltip").tooltip();
                $("#searc_multiple").click(function () {
                    $("#form_action").val("searc_multiple");
                    $("#adminForm").submit();
                });
                $("#reset_multiple").click(function () {
                    $("#awb").val("");
                    $("#country").val("");
                    $("#carrier").val("");
                    $("#service_type").val("");
                    $("#date_printed").val("");
                    $("#date_printed1").val("");
                    $("#hawb").val("");
                    $("#status_list").val("");
                    $("#contact_name").val("");
                    $("#company").val("");
                    $("#city").val("");
                    $("#postcode").val("");
                    $("#mawb").val("");
                    $("#manifest").val("");
                    $("#return_awb").val("");
                    $("#form_action").val("reset_multiple");
                    $("#adminForm").submit();
                });
                $('#divSearchPortlet').on('click', function (event) {
                    $('#searchShipmentbox').toggle('block');
                });
                // IMPORT
                $("#btnImport").click(function () {
                    $("#form_action").val("import");
                    handleActionAjax("import");
                });
                // SHOW EXPORT
                $("#btnExportData").click(function () {
                    $("#form_action").val("export_data");
                    $("#adminForm").prop('action', 'client_list.php');
                    $("#adminForm").submit();
                });
                $("#btnBatchSingleLabel").click(function () {
                    window.conIdArr = [];
                    var valid_shipment = '<?= $this->shipmentValid; ?>';
                    $("#generate-labels").hide();
                    if (valid_shipment > 0) {
                        $("#generate-labels").show();
                        $("#generate-labels-new").show();
                        $('#print-labels-popup').modal('show');
                        var message = '<?= Translation::GetCaption("BULK_LABEL_MESSAGE_1") ?> ' + valid_shipment + " <?= Translation::GetCaption("BULK_LABEL_MESSAGE_3") ?> <?= Translation::GetCaption("BULK_LABEL_MESSAGE_2") ?>";
                        $('#print-labels-message').html(message);
                    } else {
                        $('#print-labels-message').html('<?php echo Translation::GetCaption("LABEL_ERROR_MESSAGE") ?>');
                    }
                });
                $("#btnLabelCreate").click(function () {
                    conIdArr = [];
                    $.each($('input[name="deleteConsignments[]"]:checkbox:checked'), function () {
                        conIdArr.push($(this).val());
                    });
                    if (conIdArr.length <= 0) {
                        $('#generate-labels').hide();
                        swal("", "<?php echo Translation::GetCaption("NO_SHIPMENT_LABEL_MESSAGE") ?>", "info");
                        return false;
                    } else {
                        $.ajax({
                            url: "ajaxlabel.php",
                            data: {
                                action: 'GET_SHIPMENT_WITHID_JSON',
                                shipment_id: conIdArr,
                            },
                            type: "POST",
                            dataType: "json",
                            //async: false,
                        })
                            .done(function (json) {
                                if (json.STATUS == 'SUCCESS') {
                                    var shipmentsArray = json.DATA;
                                    var valid_shipment = shipmentsArray.length;
                                    if (valid_shipment > 0) {
                                        $("#generate-labels").show();
                                        $('#print-labels-popup').modal('show');
                                        // $('.filter-submit').click();
                                        var message = '<?= Translation::GetCaption("BULK_LABEL_MESSAGE_1") ?> ' + valid_shipment + " <?= Translation::GetCaption("BULK_LABEL_MESSAGE_3") ?> <?= Translation::GetCaption("BULK_LABEL_MESSAGE_2") ?>";
                                        $('#print-labels-message').html(message);
                                    } else {

                                        $('#print-labels-message').html('<?php echo Translation::GetCaption("LABEL_ERROR_MESSAGE") ?>');
//                            $('#generate-labels').hide();
                                    }
//                        grid.getDataTable().ajax.reload();
                                }

                            })
                            .fail(function (xhr, status, errorThrown) {
                                // alert( "The request is complete!" );
                                $(".modal-close").show();
                            })
                            // Code to run regardless of success or failure;
                            .always(function (xhr, status) {
//                     alert( "The request is complete!" );
                            });
                        return false;
                    }
                });
                $("#generate-labels").click(function () {
                    $("#generate-labels").hide();
                    $(".modal-close").hide();
                    var conIdArr = window.conIdArr
                    if (conIdArr.length > 0) {
                        var action = 'GET_SHIPMENT_WITHID_JSON';
                        var valid_shipment = parseInt(conIdArr.length);
                    } else {
                        var action = 'GET_SHIPMENT_JSON';
                        var valid_shipment = parseInt('<?= $this->shipmentValid; ?>');
                    }

                    var externalUserParam = '';
                    var externalUser = '<?php echo util_get("uaccount"); ?>';
                    if ($.trim(externalUser) != '')
                        externalUserParam = "?uaccount=" + externalUser;
                    var successLabels = 0;
                    var errorLabels = 0;
                    var labelsLinkArray = [];
                    var sadlfjaslfjasdf = $.ajax({
                        url: "ajaxlabel.php" + externalUserParam,
                        data: {
                            action: action,
                            shipment_id: conIdArr
                        },
                        type: "POST",
                        dataType: "json",
                        //async: false,
                    })
                    // Code to run if the request succeeds (is done);
                    // The response is passed to the function
                        .done(function (json) {

                            if (json.STATUS == 'SUCCESS') {
                                var successLabels = 0;
                                var errorLabels = 0;
                                var totalLableGenerated = 0;
                                $.each(json.DATA, function (arrayIndex, consignmentid) {
                                    //alert('Please wait, we are process labels...'+arrayIndex +" / "+ json.DATA.length);
                                    /////////--------------------------------------
                                    $(".modal-close").hide();
                                    $.ajax({
                                        url: "ajaxlabel.php" + externalUserParam,
                                        data: {
                                            consignmentid: consignmentid,
                                            action: 'GENERATELABEL'
                                        },
                                        type: "POST",
                                        dataType: "json",
                                        async: false,
                                    })
                                    // Code to run if the request succeeds (is done);
                                    // The response is passed to the function
                                        .done(function (json) {
                                            if (json.status == 'SUCCESS') {
                                                var dataJson = json.DATA;
                                                var successcount = true;
                                                $.each(dataJson, function (arrayID, dataArray) {
                                                    if (dataArray.STATUS == 'SUCCESS') {
                                                        if (successcount) {
                                                            successLabels = successLabels + 1;
                                                            successcount = false;
                                                        }
                                                        labelsLinkArray.push(dataArray.INSTANT_LABEL);
                                                    } else {
                                                        errorLabels = errorLabels + 1;
                                                    }
                                                    totalLableGenerated = (successLabels + errorLabels);
                                                    totalProgressBar = (totalLableGenerated * 100) / valid_shipment;
                                                    $('#print-labels-message').html('Label Generating ' + totalLableGenerated + ' / ' + valid_shipment + '<br>' +
                                                        '<div class="progress progress-striped active">' +
                                                        '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' + totalProgressBar + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + totalProgressBar + '%">' +
                                                        '<span class="sr-only"> ' + totalProgressBar + '% Complete </span>' +
                                                        '</div>' +
                                                        '</div>');
                                                });
                                            } else {
                                                errorLabels = errorLabels + 1;
                                                totalLableGenerated = (successLabels + errorLabels);
                                                totalProgressBar = (totalLableGenerated * 100) / valid_shipment;
                                                $('#print-labels-message').html('Label Generating ' + totalLableGenerated + ' / ' + valid_shipment + '<br>' +
                                                    '<div class="progress progress-striped active">' +
                                                    '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' + totalProgressBar + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + totalProgressBar + '%">' +
                                                    '<span class="sr-only"> ' + totalProgressBar + '% Complete </span>' +
                                                    '</div>' +
                                                    '</div>');
                                            }

                                        })
                                        // Code to run if the request fails; the raw request and
                                        // status codes are passed to the function
                                        .fail(function (xhr, status, errorThrown) {
                                            // alert( "The request is complete!" );
                                            $(".modal-close").show();
                                        })
                                        // Code to run regardless of success or failure;
                                        .always(function (xhr, status) {
                                            // alert( "The request is complete!" );
                                        });
                                    ///////----------------------------------------

                                });
                                $("<p>Please Wait, we are merging your lables.</p>").appendTo("#print-labels-message");
                                if (labelsLinkArray.length > 0) {
                                    $(".modal-close").hide();
                                    $.ajax({
                                        url: "ajaxlabel.php",
                                        data: {
                                            labels: labelsLinkArray,
                                            action: 'GENERATELABELMERGE'
                                        },
                                        type: "POST",
                                        dataType: "json",
                                        //   async: false,
                                    })
                                    // Code to run if the request succeeds (is done);
                                    // The response is passed to the function
                                        .done(function (json) {
                                            $('#print-labels-message').html(json.MESSAGE);
                                            $('.filter-submit').click();
                                            $(".modal-close").show();
                                        })
                                        // Code to run if the request fails; the raw request and
                                        // status codes are passed to the function
                                        .fail(function (xhr, status, errorThrown) {
                                            // alert( "The request is complete!" );
                                            $(".modal-close").show();
                                        })
                                        // Code to run regardless of success or failure;
                                        .always(function (xhr, status) {
                                            // alert( "The request is complete!" );
                                            $(".modal-close").show();
                                        });
                                } else {
                                    $('#print-labels-message').html('No label has been generated');
                                }

                            } else {
                                $('#print-labels-message').html('<?php echo Translation::GetCaption("LABEL_ERROR_MESSAGE") ?>');
                            }
                        })
                        // Code to run if the request fails; the raw request and
                        // status codes are passed to the function
                        .fail(function (xhr, status, errorThrown) {
                            alert("Sorry, there was a problem!");
                            console.log("Error: " + errorThrown);
                            console.log("Status: " + status);
                            console.dir(xhr);
                            $("#modal-close").show();
                        })
                        // Code to run regardless of success or failure;
                        .always(function (xhr, status) {
                            // alert( "The request is complete!" );
                        });
                    $('#print-labels-message').html();
                });
                $("#generate-labels-new").click(function () {
//	        $("#generate-labels-new").hide();
//	        $(".modal-close").hide();
                    $('#print-labels-popup').modal('hide');
                    var conIdArr = window.conIdArr
//                console.log(conIdArr);
                    if (conIdArr.length > 0) {
                        var action = 'GET_SHIPMENT_WITHID_JSON_NEW';
                        var valid_shipment = parseInt(conIdArr.length);
                    } else {
                        var action = 'GET_SHIPMENT_JSON';
                        var valid_shipment = parseInt('<?= $this->shipmentValid; ?>');
                    }

                    var externalUserParam = '';
                    var externalUser = '<?php echo util_get("uaccount"); ?>';
                    if ($.trim(externalUser) != '')
                        externalUserParam = "?uaccount=" + externalUser;
                    var successLabels = 0;
                    var errorLabels = 0;
                    var labelsLinkArray = [];
                    //send request to generate labels and merge together //all in one request
                    var exporting = true, xhr;
                    $("#export-terminal").css({"z-index": 9999, "visibility": "visible"}).fadeIn();
                    $("#export-terminal-msgs").append("<li>Processing request...</li>");
                    jcf.replaceAll();
                    var SM_AJAX_URL = "ajaxgeneratelabels.php" + externalUserParam;
//		    var SM_AJAX_DATA   = {action:action,shipment_id : conIdArr};
                    var SM_AJAX_DATA = 'action=' + action + '&shipment_id=' + conIdArr;
                    return callXHRRequest(SM_AJAX_URL, SM_AJAX_DATA);
                });

                function callXHRRequest(sm_ajaxUrl, sm_ajaxData) {
                    $("body").css("overflow-y", "hidden");
                    var dots = window.setInterval(function () {
                        var wait = document.getElementById("wait");
                        if (wait.innerHTML.length > 3)
                            wait.innerHTML = "";
                        else
                            wait.innerHTML += ".";
                    }, 250);
                    try {
                        if (window.XMLHttpRequest) {
                            // code for modern browsers
                            xhr = new XMLHttpRequest();
                        } else {
                            // code for old IE browsers
                            xhr = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        xhr.previous_text = '';
                        //xhr.responseType = 'text/html';
                        xhr.onload = function () {
                        };
                        xhr.onerror = function () {
                            //console.log(xhr.status,"xhr.status");
                            exporting = false;
                        };
                        xhr.onreadystatechange = function (response) {
                            try {
                                if (xhr.readyState > 2) {
                                    var new_response = xhr.responseText.substring(xhr.previous_text.length);
                                    xhr.previous_text = xhr.responseText;
                                    if (new_response.indexOf("http") !== -1) {
                                        if (new_response.indexOf("taskCompleted") !== -1) {
                                            var errorRes = new_response.replace("abortConsoleExecution", "");
                                            var errorRes = new_response.replace("taskCompleted", "");
                                            $("#export-terminal-msgs").append('<br><li style="list-style: none" class="completed display-block">' + errorRes + "</li>");
                                            $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                            clearInterval(dots);
                                            document.getElementById("wait").innerHTML = "";
//                                                        setTimeout(function(){
//                                                                exporting = false;
//                                                                $("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
//                                                                $("#export-terminal-msgs").html("");
//                                                                $("body").css("overflow-y","auto");
//                                                        },5000);
                                            grid.getDataTable().ajax.reload();
                                        } else {
                                            exporting = false;
                                            //					            window.location.href = new_response;
                                            grid.getDataTable().ajax.reload();
                                            clearInterval(dots);
                                            $("#export-terminal").css({"z-index": -9999, "visibility": "hidden"});
                                            $("#export-terminal-msgs").html("");
                                            $("body").css("overflow-y", "auto");
                                        }
                                    } else if (new_response.indexOf("abortConsoleExecution") !== -1) {
                                        var errorRes = new_response.replace("abortConsoleExecution", "");
                                        $("#export-terminal-msgs").append('<li class="errorconsole">' + errorRes + "</li>");
                                        $("#export-terminal-msgs").append('<li class="errorconsole">' + "Please fix all errors above and try again! " + "</li>");

                                        $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                        clearInterval(dots);
                                        document.getElementById("wait").innerHTML = "";
//					            setTimeout(function(){
//						            exporting = false;
//						            $("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
//						            $("#export-terminal-msgs").html("");
//						            $("body").css("overflow-y","auto");
//					            },5000);
                                        grid.getDataTable().ajax.reload();
                                    } else if (new_response.indexOf("consoleWarning") !== -1) {
                                        var errorRes = new_response.replace("consoleWarning", "");
                                        $("#export-terminal-msgs").append('<li class="errorconsole">' + errorRes + "</li>");
                                    } else if (new_response.indexOf("taskCompleted") !== -1) {
                                        var errorRes = new_response.replace("abortConsoleExecution", "");
                                        var errorRes = new_response.replace("taskCompleted", "");
                                        $("#export-terminal-msgs").append('<br><li style="list-style: none" class="completed display-block">' + errorRes + "</li>");
                                        $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                        clearInterval(dots);
                                        document.getElementById("wait").innerHTML = "";
//					            setTimeout(function(){
//						            exporting = false;
//						            $("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
//						            $("#export-terminal-msgs").html("");
//						            $("body").css("overflow-y","auto");
//					            },5000);
                                        grid.getDataTable().ajax.reload();
                                    } else {
                                        if (new_response != "false" && new_response != "" && new_response.indexOf("http") === -1)
                                            var respstring = new_response.trim();
//                                                console.log('inside else');
//                                                console.log(respstring);
                                        if (respstring) {
                                            if (respstring.length > 4) {
                                                $("#export-terminal-msgs").append("<li class='display-block'>" + new_response + "</li>");
                                                $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                            }
                                        }
                                    }
                                }
                                $(".errorconsole").css({"background-color": "red"});
                                $(".completed").css({"background-color": "green"});
                            } catch (e) {
                                console.log("<b>[XHR] Exception: " + e + "</b>");
                            }
                        };
                        xhr.open("POST", sm_ajaxUrl, true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
                        xhr.send(sm_ajaxData);
                    } catch (e) {
                        console.log(("<b>[XHR] Exception: " + e + "</b>"));
                    }
                }

                $("#btnLabelMerge").click(function () {
                    window.conIdArr = [];
                    var externalUserParam = '';
                    var externalUser = '<?php echo util_get("uaccount"); ?>';
                    if ($.trim(externalUser) != '')
                        externalUserParam = "?uaccount=" + externalUser;
                    $.each($("input[name='deleteConsignments[]']:checked"), function () {
                        window.conIdArr.push($(this).val());
                    });
                    $("#generate-labels-new").hide();
                    if (window.conIdArr.length <= 0) {
                        $('#generate-labels').hide();
                        $('#print-labels-message').html('<?php echo Translation::GetCaption("MERGE_LABEL_MESSAGE_1") ?>');
                        $('#print-labels-popup').modal('show');
                        return false;
                    } else {

                        $.ajax({
                            url: "ajaxlabel.php" + externalUserParam,
                            data: {
                                action: 'GET_LABEL_WITHID_JSON',
                                shipment_id: window.conIdArr,
                            },
                            type: "POST",
                            dataType: "json",
                            //async: false,
                        })
                            .done(function (json) {

                                if (json.STATUS == 'SUCCESS') {
                                    var shipmentsArray = json.DATA;
                                    var labelsLinkArray = shipmentsArray;
                                    var valid_shipment = shipmentsArray.length;
                                    if (labelsLinkArray.length > 0) {
                                        $(".modal-close").hide();
                                        $.ajax({
                                            url: "ajaxlabel.php" + externalUserParam,
                                            data: {
                                                labels: labelsLinkArray,
                                                action: 'GENERATELABELMERGE'
                                            },
                                            type: "POST",
                                            dataType: "json",
                                            async: false,
                                        })
                                        // Code to run if the request succeeds (is done);
                                        // The response is passed to the function
                                            .done(function (json) {
                                                $('#print-labels-popup').modal('show');
                                                $('#generate-labels').hide();
                                                $('#print-labels-message').html(json.MESSAGE);
                                                $(".modal-close").show();
                                            })
                                            // Code to run if the request fails; the raw request and
                                            // status codes are passed to the function
                                            .fail(function (xhr, status, errorThrown) {
                                                // alert( "The request is complete!" );
                                                $(".modal-close").show();
                                            })
                                            // Code to run regardless of success or failure;
                                            .always(function (xhr, status) {
                                                // alert( "The request is complete!" );
                                                $(".modal-close").show();
                                            });
                                    } else {
                                        $('#print-labels-message').html('No label is available to merge');
                                    }
                                } else {
                                    swal("", json.DATA, "info");
                                }

                            })
                            .fail(function (xhr, status, errorThrown) {
                                // alert( "The request is complete!" );
                                $(".modal-close").show();
                            })
                            // Code to run regardless of success or failure;
                            .always(function (xhr, status) {
                                // alert( "The request is complete!" );
                            });
                    }
                });
                // Collection
                $("#btnShowCollection").click(function () {
                    $("#form_action").val("btnShowCollection");
                    handleActionAjax("btnShowCollection");
                });
                $("#btnManifestSelected").click(function () {
                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("NO_MANIFEST_SELECTED ") ?>", "")) {
                        return false;
                    } else {
                        swal({
                                title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_CREATE_A_MANIFEST?") ?>",
                                text: "",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Yes",
                                cancelButtonText: "No",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    $("#form_action").val("createmanifest");
                                    handleActionAjax("createmanifest");
                                }
                            });
                    }
                });
                $("#btnCollectionHistory").click(function () {
                    window.location = "find_collection.php";
                });
                $("#btnCollectionSelected").click(function () {

                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("LLECTION ") ?>", "")) {
                        return false;
                    } else {
                        var conIdArr = [];
                        $.each($("input[name='deleteConsignments[]']:checked"), function () {
                            conIdArr.push($(this).val());
                        });
                        $.ajax({
                            type: "POST",
                            url: "../main/find_collection_ajax.php", // your php file name
                            data: {
                                action: 'getTotalWeight',
                                conIdArr: conIdArr
                            },
                            success: function (data) {
                                $("#total_weight_sum").val(data);
                            }

                        });
                        $("#collection_type").val("collection_selected");
                        $("#collection-request").modal("show");
                    }
                });
                $("#btnCollectionAll").click(function () {
                    var conIdArr = [];
                    $.ajax({
                        type: "POST",
                        url: "../main/find_collection_ajax.php", // your php file name
                        data: {
                            action: 'getTotalWeight',
                            conIdArr: conIdArr
                        },
                        success: function (data) {
                            $("#total_weight_sum").val(data);
                        }

                    });
                    $("#collection_type").val("collection_all");
                    $("#collection-request").modal("show");
                });
                $("#btnPutOnHold").click(function () {
                    $("#HOLD_LIST_SCAN").modal("show");
                    $("#hidUnHold").val("hold");
                    $("#hold_title").text('<?php echo Translation::GetCaption("MSG_HOLD_POPUP_TITLE") ?>');
                });
                $("#btnUnHold").click(function () {

                    $("#HOLD_LIST_SCAN").modal("show");
                    $("#hidUnHold").val("unhold");
                    $("#hold_title").text('<?php echo Translation::GetCaption("MSG_UNHOLD_POPUP_TITLE") ?>');
                });
                $("#btnManifestAll").click(function () {

                    swal({
                            title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_CREATE_A_MANIFEST?") ?>",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#form_action").val("createmanifestall");
                                handleActionAjax("createmanifestall");
                            }
                        });
                    /*
                     swal({
                     title: "<?php //echo Translation::GetCaption("BULK_MANIFEST_POP_MESSAGE_1")  ?>",
             text: "",
             type: "warning",
             showCancelButton: true,
             confirmButtonClass: "btn-danger",
             confirmButtonText: "Yes",
             cancelButtonText: "No",
             closeOnConfirm: true,
             closeOnCancel: true
             },
             function(isConfirm) {
             if (isConfirm) {
             location.href = "manifest_type.php?manifest=manifestall";
             }
             });
             */

                });
                $("#search_Status").change(function () {
                    $(".filter-submit").click();
                });
                $("#search_Country").change(function () {
                    $(".filter-submit").click();
                });
                $("#search_ServiceType").change(function () {
                    if ($(this).find(':selected').data('service_type') == 'service') {
                        $("#search_service").val($("#search_ServiceType").val());
                        $("#search_product").val('');
                    } else if ($(this).find(':selected').data('service_type') == 'product') {
                        $("#search_service").val('');
                        $("#search_product").val($("#search_ServiceType").val());
                    }
                    $(".filter-submit").click();
                });
                $("#btnLabelShow").click(function () {
                    $("#search_manifestdata").val(0);
                    $("#search_Status").val('<?= Consignment::STATUS_READY_TO_PRINT; ?>');
                    $("#search_Status").change();
                    $('#ShowLabel').show();
                    $('#ShowManifest').hide();
                    $('#ShowPickup').hide();
                });
                $("#btnManifestShow").click(function () {
                    $("#selected_manifest").val("show");
                    //window.location = "client_list.php?show=manifest_selected";
                    $("#search_Status").val('<?= Consignment::STATUS_LABEL_CREATED; ?>');
                    $("#search_manifestdata").val('1');
                    //   alert($("#search_manifestdata").val());
                    $("#search_Status").change();

                    $("#ShowLabel").hide();
                    $("#ShowManifest").show();
                    // $(".filter-submit").trigger("click");
                });
                $("#btnCollectionShow").click(function () {
                    $("#search_manifestdata").val(0);
                    window.location = "client_list.php?show=collection_selected";
                });
                $("#btnAdvanceSearch").click(function () {
                    $("#search_manifestdata").val(0);
                    $("#form_action").val("btnAdvanceSearch");
                    $("#adminForm").prop('action', 'client_list.php');
                    $("#adminForm").submit();
                });
                // Create Labels


                $('*[data-poload]').click(function () {
                    var e = $(this);
                    var cid = e.data('cid');
                    var url = e.data('poload');
                    e.off('click');
                    $.post(url, {
                        func: 'getShipmentDetails',
                        cid: cid
                    }, function (d) {
                        e.popover({
                            content: d,
                            placement: 'left',
                            html: true,
                            container: 'body'
                        }).popover('show');
                    });
                });
                
                ///*********************** Collection reschedule ********************** /
                $(document).on('click', '.reschedule_collection', function () {

                    var consignment_id = $(this).attr('data-consignment_id');
                    $('#consignment_id').val(consignment_id);
                    $('#collection-reschedule-popup').modal('show');
                });
                $(document).on('click', '#btnreschedule', function () {
                    var consignment_id = $('#consignment_id').val();
                    var collectiondate = $('#collection_date').val();
                    var starttime = $('#collection_start_time').val();
                    var endtime = $('#collection_end_time').val();
                    
                    $('#res_message').hide();
                    $.ajax({
                        type: "POST",
                        url: "client_list.php",
                        data: {action: "GET_RESCHEDULE_COLLECTION", consignment_id: consignment_id, collection_date:collectiondate, starttime:starttime, endtime:endtime },
                        dataType: "json",
                        success: function (data) {
                            if(data.STATUS == "SUCCESS"){
                                $('#collection_mess').removeClass('alert-danger').addClass('alert-success');
                                $('#collection_mess').html("Collection reschedule successfully between " +starttime + " to " + endtime + " on " + collectiondate + ".");
                                $('#collection_mess').show();
                            }
                            else
                            {
                                $('#collection_mess').removeClass('alert-success').addClass('alert-danger');
                                $('#collection_mess').html(data.MESSAGE);
                                $('#collection_mess').show();
                            }
                        },
                        error: function () {
                            $('#collection_mess').removeClass('alert-success').addClass('alert-danger');
                            $('#collection_mess').html("Some error occurred");
                            $('#collection_mess').show();
                        }
                    });
                });
            });

            function other_option_action(option_value) {
                if (option_value == "Export") {
                    $("#form_action").val("export_data");
                    $("#adminForm").prop('action', '');
                    $("#adminForm").submit();

                } else if (option_value == "Restore") {
                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>", "")) {
                        $("#other_options").val('').change();
                        return false;
                    }
                    swal({
                            title: "<?php echo Translation::GetCaption("RESTORE_CONFIRM_MESSAGE") ?>",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#form_action").val("btnRestore");
                                handleActionAjax("btnRestore");
                            }
                        });
                } else if (option_value == "Delete") {

                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>")) {
                        $("#other_options").val('').change();
                        return false;
                    }
                    swal({
                            title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THESE_SHIPMENT?") ?>",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#form_action").val("Recycle");
                                handleActionAjax("Recycle");
                            }
                        });
                } else if (option_value == "Hold") {
                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>")) {
                        $("#other_options").val('').change();
                        return false;
                    }
                    swal({
                            title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_HOLD_THESE_SHIPMENT") ?>",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#form_action").val("Hold");
                                handleActionAjax("Hold");
                            }
                        });
                } else if (option_value == "Unhold") {
                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment. ") ?>")) {
                        $("#other_options").val('').change();
                        return false;
                    }

                    if (confirm('<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_UNHOLD_THESE_SHIPMENT") ?>')) {
                        $("#form_action").val("Unhold");
                        handleActionAjax("Unhold");
                    } else {
                        $("#other_options").val("<?php echo Translation::GetCaption("ACTION ") ?>");
                    }
                }
                $("#other_options").val('').change();

            }

            function handleActionAjax(action) {
                var externalUserParam = '';
                var externalUser = '<?php echo util_get("uaccount"); ?>';
                if ($.trim(externalUser) != '')
                    externalUserParam = "?uaccount=" + externalUser;
                var url_data = "client_list.php";
                if (action == "createmanifest" || action == "createmanifestall") {
                    $('<input />').attr('type', 'hidden')
                        .attr('name', "action")
                        .attr('value', "filter")
                        .appendTo('#adminForm');

                    if ($.trim(externalUserParam) == '')
                        externalUserParam = '?action=consignment_ajax';
                    else
                        externalUserParam = externalUserParam + '&action=consignment_ajax';
                    $("#adminForm").append()

                }

                var dataString = $("#adminForm").serialize();

                $.ajax({
                    type: "POST",
                    url: url_data + externalUserParam,
                    data: dataString,
                    dataType: "json",
                    success: function (data) {
                        $('#res_message').removeClass('alert-danger');
                        $('#res_message').removeClass('alert-success');
                        $('#res_message').removeClass('alert-info');
                        var returnStatus = '';

                        if ($.type(data.STATUS) != 'undefined')
                            returnStatus = data.STATUS;
                        else if ($.type(data.status) != 'undefined')
                            returnStatus = data.status;
                        var returnmessage = '';
                        if ($.type(data.MESSAGE) != 'undefined')
                            returnmessage = data.MESSAGE;
                        else if ($.type(data.message) != 'undefined')
                            returnmessage = data.message;

                        if (returnStatus.toLowerCase() == 'success') {
                            $('#res_message').addClass('alert-success').html(returnmessage);
                            if (action == "createmanifest" || action == "createmanifestall") {
                                window.location = 'coclient_user_endofday.php' + externalUserParam;
                            } else {
                                grid.getDataTable().ajax.reload();
                            }
                        } else if (returnStatus.toLowerCase() == 'info') {
                            $('#res_message').addClass('alert-info').html(returnmessage);
                            grid.getDataTable().ajax.reload();
                        } else {
                            grid.getDataTable().ajax.reload();
                            $('#res_message').addClass('alert-danger').html(returnmessage);

                        }
                        // $('#res_message').removeClass('alert-info').removeClass('alert-success').addClass('alert-danger').html(returnmessage);
                        $('#res_message').show();
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }

            function displayInvoice(consignmentid) {
                $.post("../main/performainvoicepdf_multi.php", {
                    jobno: consignmentid
                }, function (data) {
                    outFile = data;
                    var stringparts = outFile.split('||');
                    if (stringparts[0] == 'ERROR') {
                        alert(stringparts[1])
                    } else {
                        window.open(stringparts[1], '', 'width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');
                        document.location.href = "../main/client_list.php";
                    }
                    return;
                });
            }

            function consignmentSelectionConfirmation(errorMessage, errorMessagenew) {
                if (typeof (document.getElementsByName("deleteConsignments[]")) == 'undefined') {
                    swal("", errorMessage, "info");
                    return false;
                }
                var deleteConsign = document.getElementsByName("deleteConsignments[]");
                var txt = "";
                var i;
                for (i = 0; i < deleteConsign.length; i++) {
                    if (deleteConsign[i].checked) {
                        txt = txt + deleteConsign[i].value + "";
                    }
                }

                if (txt == "") {
                    swal("", errorMessage, "info");
                    return false;
                }
                return true;
            }

            function SendEmail(label_link, id, hawb) {
                $.post("../main/ajaxlabel.php", {
                    action: 'CollectionEmail',
                    label_link: label_link,
                    id: id
                }, function (data) {
                    $("#email_check" + hawb).attr('value', 'Resend');
                });
            }

            function get_parcel_list(connsignmentId, trackingNumber) {
                $.ajax({
                    type: "POST",
                    url: "client_list.php?action=get_parcel_detail",
                    data: {consignment_id: connsignmentId},
                    success: function (data) {
                        $('#parcel_list_data').html("");
                        $('.tracking_number_heading').html("");
                        $('#parcel_list_data').html(data);
                        $('.tracking_number_heading').html(trackingNumber);
                        $('#parcel_list_modal').modal('show');
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            <?php
            if (isset($_REQUEST['show']) && in_array($_REQUEST['show'], array('show_valid')))
                echo ' $(document).ready(function() {'
                    . '$("#btnLabelShow").click();});';
            if (isset($_REQUEST['show']) && in_array($_REQUEST['show'], array('label_created')))
                echo ' $(document).ready(function() {'
                    . '$("#search_Status").val("' . Consignment::STATUS_LABEL_CREATED . '");'
                    . '$("#search_Status").change();'
                    . '});';
            if (isset($_REQUEST['show']) && in_array($_REQUEST['show'], array('shipped')))
                echo ' $(document).ready(function() {'
                    . '$("#search_Status").val("' . Consignment::STATUS_INTRANSIT . '");'
                    . '$("#search_Status").change();'
                    . ' });';
            if (isset($_REQUEST['show']) && in_array($_REQUEST['show'], array('delivered')))
                echo ' $(document).ready(function() {'
                    . '$("#search_Status").val("' . Consignment::STATUS_DELIVERED . '");'
                    . '$("#search_Status").change();'
                    . ' });';
            ?>
        </script>

        <?php
        if (isset($_GET["create_manifest"])) {
            echo '<script>
            $(document).ready(function() {
                $("#btnManifestShow").click();
            });
        </script>';
        }
        include("find_collection_popup.php");
    }

    /*
     * Return Status According to the user type
     */

    private function statusToUser()
    {
        $user = $this->user;
        $userType = $user->getUserType();
        $status_array = array();

        switch ($userType) {
            case "corporateclient":
                $status_array = array(
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_READY_TO_PRINT,
                    Consignment::STATUS_INVALID,

                    Consignment::STATUS_RECEIVED,
                    Consignment::STATUS_PARTIAL_RECEIVED,
                    Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_PARTIAL_DISPATCHED,

                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_CLOSED
                );
                break;
            case "client":
            default:
                $status_array = array(
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_READY_TO_PRINT,
                    Consignment::STATUS_RECEIVED,
                    Consignment::STATUS_INTRANSIT,

                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_INVALID,
                    Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_PROBLEM,
                    Consignment::STATUS_RETURNED
                );
                break;
            case "warehouse":
            case "admin":
            case "finance":
            case "customerservice":
            case "sales":
                $status_array = array(
                    Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_INVALID,
                    Consignment::STATUS_READY_TO_PRINT,
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_RECEIVED,
                    Consignment::STATUS_PARTIAL_RECEIVED,
                    Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_PARTIAL_DELIVERED,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_RETURNED,
                    Consignment::STATUS_CLOSE
                );
                break;
        }
        $this->form_vars["status_list"] = ''; // this line clears the current selected status
        $arrayForUsers = array();
        foreach ($status_array as $key => $status) {
            $c_status = Consignment::stateText($status);
            $arrayForUsers[$status] = Translation::GetCaption($c_status);

        }

        echo Ddl::generateArrayDDL('search_Status', $arrayForUsers, $this->form_vars["status_list"], 'Status', 'class="form-control form-filter input-sm select2 searchbox"', "", 'search_Status', 'Status');
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
