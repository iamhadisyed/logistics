<?php

require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'iaddress.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'invoices.class',
                    'invoicesfilter.class',]);

$error_list = array();
$msg = "";
$left_to_print = 0;

if (isset($_POST['action']) && trim($_POST['action']) == 'HAWB-DETAIL') {
    $hawb = $_POST['hawb'];
    $consignmentFilter = new ConsignmentFilter();
    $consignmentFilter->addHawbFilter($hawb);
    $consignmetlist = $consignmentFilter->getColumnList('hawb, service_type, date_booked, date_scanned, handling, country, awb, date_received, date_submitted');
    if (count($consignmetlist) > 0) {
        $consignmentData = $consignmetlist[0];
        $datebooked = (trim($consignmentData->getDateBooked()) != '') ? $consignmentData->getDateBooked() : ((trim($consignmentData->getDateReceived()) != '') ? $consignmentData->getDateReceived() : $consignmentData->getDateSubmitted());
        $description = $consignmentData->getCountry() . '; Tracking Number:' . $consignmentData->getAwb() . '; ';
        $ar = array(
            'success',
            $consignmentData->getHawb(),
            $consignmentData->getServiceType(),
            $datebooked,
            $consignmentData->getHandling(),
            $description
        );
        echo json_encode($ar);
        die;
    } else {
        $ar = array('error', 'No consignment found for hawb: ' . $hawb);
        echo json_encode($ar);
        die;
    }
}
if (isset($_POST['action']) && trim($_POST['action']) == 'LOAD-INVOICES') {

    $account = $_POST['account'];
    $invoiceType = $_POST['invoiceType'];
    $invoiceNumber = $_POST['invoiceNumber'];
    echo Ddl::generateDDL('invoice_number', 'InvoiceFilter', array('user_account_id'=>$account, 'invoice_type'=>trim($invoiceType)), 'invoice_no', 'invoice_no', $warehouse_id, 'class="form-control select2" data-toggle="tooltip" data-placement="top" title="Select Hub" data-original-title="Select Hub"', '', '', '', 'Select Invoice');
    die;
}
if (isset($_POST['action']) && trim($_POST['action']) == 'LOAD-INVOICES-DATA') {
    $accountNumber = $_POST['accountNumber'];
    $invoiceNumber = $_POST['invoiceNumber'];
    $incoiveOption = $_POST['incoiveOption'];
    $invoiceType = $_POST['invoiceType'];
    $creditNoteId = $_POST['creditNoteId'];
    $dataConsignment = array();
    if (trim($incoiveOption) == 'PARTIAL') {
        if (isset($creditNoteId) && $creditNoteId > 0) {
            $creditNoteDetailFilterObj = new CreditNoteDetailsFilter();
            $creditNoteDetailFilterObj->addFieldFilter('credit_id', $creditNoteId);
            $creditNoteDetailRes = $creditNoteDetailFilterObj->getList();
            if (count($creditNoteDetailRes) > 0) {
                foreach ($creditNoteDetailRes as $creditNoteDetail) {
                    $dataConsignment[] = array(
                        'hawb' => $creditNoteDetail->getHawb(),
                        'service_type' => $creditNoteDetail->getReference(),
                        'date_scanned' => $creditNoteDetail->getDateBooked(),
                        'invoice_amount' => $creditNoteDetail->getInvoiceAmount(),
                        'credit_amount' => $creditNoteDetail->getCreditAmount(),
                        'description' => $creditNoteDetail->getDescription(),
                        'is_vatable' => $creditNoteDetail->getIsVatable()
                    );
                }
            }
        } else {
            $hawbNumber = preg_split('/\r\n|\r|\n/', $_POST['hawbNumber']);
            $hawbNum = "      c.hawb in ('".implode("','", $hawbNumber)."')";
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addFilter($hawbNum);
            if ($invoiceNumber != '') {
                $user = SessionManager::getUser();
                $consignmentFilter->addConsignmentChargesJoin($user->getUserAccountId(),$user->getUserType());
                $consignmentFilter->addInvoiceJoin();
                $consignmentFilter->addFilter("     cc.cost_type = 'customer'","filter");
                $consignmentFilter->addFilter("     inv.invoice_no = '" . $invoiceNumber . "'","filter");
                $consignmentFilter->addFilter("     inv.invoice_type = '" . $invoiceType . "'","filter");
            }
            $consignmentFilter->addFilter("     cc.account_id = '" . $accountNumber . "'","filter");
            $consignmentFilter->addGroupBy('c.id');
            $consignmentList = $consignmentFilter->getColumnList('c.hawb, c.date_booked, c.country_id, c.reference, inv.id AS invoice_id, inv.net_amount');
            if (count($consignmentList) > 0) {
                foreach ($consignmentList as $consignmentData) {
                    $datebooked = (trim($consignmentData->getDateBooked()) != '' && trim($consignmentData->getDateBooked()) > 0) ? date("Y-m-d", $consignmentData->getDateBooked()) : "";
                    $dataConsignment[] = array(
                        'hawb' => $consignmentData->getHawb(),
                        'date_booked' => $datebooked,
                        'reference' => $consignmentData->getReference(),
                        'invoice_amount' => $consignmentData->getNetAmount()
                    );
                }
            }
        }

        echo json_encode($dataConsignment);
    } else if (trim($incoiveOption) == 'OTHER') {
        if (isset($creditNoteId) && $creditNoteId > 0) {
            $creditNoteDetailFilterObj = new CreditNoteDetailsFilter();
            $creditNoteDetailFilterObj->addFieldFilter('credit_id', $creditNoteId);
            $creditNoteDetailRes = $creditNoteDetailFilterObj->getList();
            if (count($creditNoteDetailRes) > 0) {
                foreach ($creditNoteDetailRes as $creditNoteDetail) {
                    $dataConsignment[] = array(
                        'hawb' => $creditNoteDetail->getHawb(),
                        'service_type' => $creditNoteDetail->getReference(),
                        'date_scanned' => $creditNoteDetail->getDateBooked(),
                        'invoice_amount' => $creditNoteDetail->getInvoiceAmount(),
                        'credit_amount' => $creditNoteDetail->getCreditAmount(),
                        'description' => $creditNoteDetail->getDescription(),
                        'is_vatable' => $creditNoteDetail->getIsVatable()
                    );
                }
            }
        }
        for ($i = count($dataConsignment); $i < 5; $i++) {
            $dataConsignment[] = array(
                'hawb' => '',
                'service_type' => '',
                'date_scanned' => '',
                'invoice_amount' => '',
                'credit_amount' => '',
                'description' => '',
                'is_vatable' => 'NO'
            );
        }
        echo json_encode($dataConsignment);
    } else {
        $dataConsignment = array();
        if (isset($creditNoteId) && $creditNoteId > 0) {
            $creditNoteDetailFilterObj = new CreditNoteDetailsFilter();
            $creditNoteDetailFilterObj->addFieldFilter('credit_id', $creditNoteId);
            $creditNoteDetailRes = $creditNoteDetailFilterObj->getList();
            if (count($creditNoteDetailRes) > 0) {
                foreach ($creditNoteDetailRes as $creditNoteDetail) {
                    $dataConsignment[] = array(
                        'hawb' => $creditNoteDetail->getHawb(),
                        'service_type' => $creditNoteDetail->getReference(),
                        'date_scanned' => $creditNoteDetail->getDateBooked(),
                        'invoice_amount' => $creditNoteDetail->getInvoiceAmount(),
                        'credit_amount' => $creditNoteDetail->getCreditAmount(),
                        'description' => $creditNoteDetail->getDescription(),
                        'is_vatable' => $creditNoteDetail->getIsVatable()
                    );
                }
            }
        } else {
            $consignmentChargesFilter = new InvoiceFilter();
            $consignmentChargesFilter->join('consignment_charges cc', array("inv.id" => "cc.invoice_id", "inv.invoice_no"=>$invoiceNumber) , $type = "INNER") ;
            $consignmentChargesFilter->where ( array( "inv.user_account_id" => $accountNumber));
            $consignmentChargesFilter->where ( array( "cc.cost_type" => "customer"));
            $consignmentCount = $consignmentChargesFilter->getCount(false);
            
            $incoiveDatail = new InvoiceFilter();
            $incoiveDatail->where(['inv.invoice_type' => trim($invoiceType),'inv.invoice_no' => $invoiceNumber,'inv.user_account_id' => $accountNumber]);
            $invoiceDatalist = $incoiveDatail->getList();
            if (count($invoiceDatalist) > 0) {
                foreach ($invoiceDatalist as $rowdata) {
                    $dataConsignment[] = array(
                        'invoice_amount' => $rowdata->getNetAmount(),
                        'description' => 'Account Number: ' . $accountNumber . ' has ' . $consignmentCount . ' consignments for credit note.'
                    );
                }
            } else {
                $dataConsignment[] = array(
                    'error' => 'No Invoice data found for this invoice number',
                );
            }
        }
        echo json_encode($dataConsignment);
    }
}

if (isset($_POST['action']) && trim($_POST['action']) == 'GET-CREDIT-NOTE-INVOICE-DATA') {

    // Getting All Vatable countries form Database Starts Here
    $countryArray = array();
    $countriesVatChargeAble = new CountryFilter();
    $countriesVatChargeAble->addIsVatableFilter('YES');
    $countriesVatChargeAbleData = $countriesVatChargeAble->getColumnList('name, iso, vat_rate');
    foreach ($countriesVatChargeAbleData as $CountryData) {
        $countryArray['country_id'][] = $CountryData->getId();
        $countryArray['name'][] = $CountryData->getName();
        $countryArray['iso'][] = $CountryData->getIso();
        $countryArray['vatrate'][] = $CountryData->getVatRate();
    }
    $_SESSION['VATABLE_COUNTRIES'] = $countryArray;

    $_SESSION['CURRENT_CREDIT_ACCOUNT'] = $_POST['account'];
    $_SESSION['CURRENT_CREDIT_NUMBER'] = $_POST['credit_number'];
    $creditNumber = $_POST['credit_number'];

    // GETTING USER DETAILS FROM DATABASE
    $users = new UserAccountFilter();
    $users->addUserAccountFilter($_POST['account']);
    $userResult = $users->getColumnList("full_name, company, return_address, country, user_account, billing_currency, vat_chargable, billing_address, vat_value");
    $arrarUsers = array();

    if (count($userResult) > 0) {
        $_SESSION['CURRENT_INVOICE_FULLNAME'] = $userResult[0]->getFirstName();
        $_SESSION['CURRENT_INVOICE_COMPANY'] = $userResult[0]->getCompany();
        if (trim($userResult[0]->getBillingAddress()) != '')
            $_SESSION['CURRENT_INVOICE_ADDRESS'] = $userResult[0]->getBillingAddress();
        else
            $_SESSION['CURRENT_INVOICE_ADDRESS'] = $userResult[0]->getReturnAddress();


        $_SESSION['CURRENT_INVOICE_COUNTRY'] = $userResult[0]->getCountry();
        $_SESSION['CURRENT_INVOICE_ACCOUNT'] = $userResult[0]->getUserAccount();
        $_SESSION['CURRENT_INVOICE_CURRENCY'] = $userResult[0]->getBillingCurrency();
        $_SESSION['vatchargable'] = $userResult[0]->getVatChargable();
        if ((int) $userResult[0]->getVatValue() > 0)
            $_SESSION['vatvalue'] = $userResult[0]->getVatValue();
        else
            $_SESSION['vatvalue'] = Country::vatRateCountry($userResult[0]->getCountry());
    }
    $Account = $_POST['account']; //'ORION';
    $creditNumberData = new CreditNote($creditNumber);

    $creditNoteData['C_ACCOUNT'] = $creditNumberData->getUserAccount();
    $creditNoteData['C_NUMBER'] = $creditNumberData->getCnNumber();
    $creditNoteData['C_AMOUNT'] = $creditNumberData->getCnAmount();
    $creditNoteData['C_VAT_AMOUNT'] = $creditNumberData->getVatAmount();
    $creditNoteData['C_TOTAL_AMOUNT'] = $creditNumberData->getTotalAmount();
    $creditNoteData['C_CURRENCY'] = $creditNumberData->getCurrency();
    $creditNoteData['C_DATE'] = date('d M Y ', strtotime($creditNumberData->getCnDate()));
    $creditNoteData['C_DESCRIPTION'] = $creditNumberData->getCreditDescription();
    $creditNoteData['C_REFERENCE'] = $creditNumberData->getCreditReference();
    $creditNoteData['I_AMOUNT'] = $creditNumberData->getInvoiceAmount();
    $creditNoteData['I_NUMBER'] = $creditNumberData->getInvoiceNumber();

    $_SESSION['credit_note_data'] = $creditNoteData;
    echo json_encode(array('creditnote' => @$creditNoteData));
}

if (isset($_POST['action']) && trim($_POST['action']) == 'GET-CREDIT-NOTE-CONSIGNMENT') {

    // Getting All Vatable countries form Database Starts Here
    $countryArray = array();
    $countriesVatChargeAble = new CountryFilter();
    $countriesVatChargeAble->addIsVatableFilter('YES');
    $countriesVatChargeAbleData = $countriesVatChargeAble->getColumnList('name, iso, vat_rate');
    foreach ($countriesVatChargeAbleData as $CountryData) {
        $countryArray['country_id'][] = $CountryData->getId();
        $countryArray['name'][] = $CountryData->getName();
        $countryArray['iso'][] = $CountryData->getIso();
        $countryArray['vatrate'][] = $CountryData->getVatRate();
    }
    $_SESSION['VATABLE_COUNTRIES'] = $countryArray;

    $_SESSION['CURRENT_CREDIT_ACCOUNT'] = $_POST['account'];
    $_SESSION['CURRENT_CREDIT_NUMBER'] = $_POST['credit_number'];
    $creditNumber = $_POST['credit_number'];

    // GETTING USER DETAILS FROM DATABASE
    $users = new UserAccountFilter();
    $users->addUserAccountFilter($_POST['account']);
    $userResult = $users->getColumnList("full_name, company, return_address, country, user_account, billing_currency, vat_chargable, billing_address, vat_value");
    $arrarUsers = array();

    if (count($userResult) > 0) {
        $_SESSION['CURRENT_INVOICE_FULLNAME'] = $userResult[0]->getFirstName();
        $_SESSION['CURRENT_INVOICE_COMPANY'] = $userResult[0]->getCompany();
        if (trim($userResult[0]->getBillingAddress()) != '')
            $_SESSION['CURRENT_INVOICE_ADDRESS'] = $userResult[0]->getBillingAddress();
        else
            $_SESSION['CURRENT_INVOICE_ADDRESS'] = $userResult[0]->getReturnAddress();


        $_SESSION['CURRENT_INVOICE_COUNTRY'] = $userResult[0]->getCountry();
        $_SESSION['CURRENT_INVOICE_ACCOUNT'] = $userResult[0]->getUserAccount();
        $_SESSION['CURRENT_INVOICE_CURRENCY'] = $userResult[0]->getBillingCurrency();
        $_SESSION['vatchargable'] = $userResult[0]->getVatChargable();
        if ((int) $userResult[0]->getVatValue() > 0)
            $_SESSION['vatvalue'] = $userResult[0]->getVatValue();
        else
            $_SESSION['vatvalue'] = Country::vatRateCountry($userResult[0]->getCountry());

        $serviceArray = array();
        $userServicesCharges = new UserServicesChargesFilter();
        $userServicesCharges->addUserAccountIdFilter($userResult[0]->getId());
//			$userServicesCharges->addServiceIdFilter();
        $userServicesChargesList = $userServicesCharges->getColumnList('user_account_id, service_id, sur_charge, extra_charge');
        if (count($userServicesChargesList) > 0) {
            foreach ($userServicesChargesList as $userServicesChargesData) {
                $serviceArray[$userServicesChargesData->getServiceId()][] = $userServicesChargesData->getSurCharge();
                $serviceArray[$userServicesChargesData->getServiceId()][] = $userServicesChargesData->getExtraCharge();
            }
        }
        $_SESSION['charges'] = $serviceArray;
    }
    $Account = $_POST['account']; //'ORION';

    /*
     * ***********************************
     */
    $consign = new ConsignmentFilter();
    $consign->addAccountFilter($Account);
    $consign->addFieldFilter('credit_id', $creditNumber);
    $consign->addBillingHoldFilter('NO');
    $consign->AddOrderByCountry();
    $consignment_array = $consign->getColumnList('id');
    // Resetting the invoice Fields
    $_SESSION['invoice_weight_total'] = 0;
    $_SESSION['invoice_sub_total'] = 0;
    $_SESSION['invoice_vatable_total'] = 0;
    $_SESSION['invoice_country_sub_total'] = 0;
    $_SESSION['invoice_fuel_surcharge'] = 0;
    $_SESSION['invoice_consignment_hawb'] = array();
    $_SESSION['invoice_tarrif_invalid'] = array();
    $_SESSION['invoice_current_country'] = '';
    $_SESSION['invoice_current_country_array'] = array();
    $_SESSION['invoice_current_country_counter'] = 0;
    $_SESSION['invoice_page_sub_total'] = 0;

    foreach ($consignment_array as $id) {
        $arr[] = array('id' => $id->getId());
    }

    if (sizeof($consignment_array) > 0) {
        $InvoiceSavObj = new CreditNote($_SESSION['CURRENT_CREDIT_NUMBER']);
        $_SESSION['CREDIT-NOTE-DATA'] = $InvoiceSavObj;
    }

    $_SESSION['sizeOfArr'] = sizeof(@$arr);
    echo json_encode(array('shipmentIds' => @$arr));
}




if (isset($_POST['action']) && trim($_POST['action']) == 'PUT-CREDIT-DATA-FULL-ON-FILE') {
    $id = $_POST['id'];

    $credit_array[$id]['C_ACCOUNT'] = $_SESSION['credit_note_data']['C_ACCOUNT'];
    $credit_array[$id]['C_NUMBER'] = $_SESSION['credit_note_data']['C_NUMBER'];
    $credit_array[$id]['C_AMOUNT'] = $_SESSION['credit_note_data']['C_AMOUNT'];
    $credit_array[$id]['C_VAT_AMOUNT'] = $_SESSION['credit_note_data']['C_VAT_AMOUNT'];
    $credit_array[$id]['C_TOTAL_AMOUNT'] = $_SESSION['credit_note_data']['C_TOTAL_AMOUNT'];
    $credit_array[$id]['C_CURRENCY'] = $_SESSION['credit_note_data']['C_CURRENCY'];
    $credit_array[$id]['C_DATE'] = $_SESSION['credit_note_data']['C_DATE'];
    $credit_array[$id]['C_DESCRIPTION'] = $_SESSION['credit_note_data']['C_DESCRIPTION'];
    $credit_array[$id]['id'] = $id;
    $_SESSION["array_invoice"][$id] = $credit_array[$id];

    $arrarUsers['userName'] = $_SESSION['CURRENT_INVOICE_FULLNAME'];
    $arrarUsers['userCompany'] = $_SESSION['CURRENT_INVOICE_COMPANY'];
    $arrarUsers['userAddress'] = $_SESSION['CURRENT_INVOICE_ADDRESS'];
    $arrarUsers['userCountry'] = $_SESSION['CURRENT_INVOICE_COUNTRY'];
    $arrarUsers['userAccount'] = $_SESSION['CURRENT_INVOICE_ACCOUNT'];
    $arrarUsers['userCurrency'] = $_SESSION['CURRENT_INVOICE_CURRENCY'];
    $arrarUsers['vatchargable'] = $_SESSION['vatchargable'];
    $arrarUsers['vatvalue'] = $_SESSION['vatvalue'];
    //$arrarUsers['charges']			=   $_SESSION['charges'];
    //$arrarUsers['VATABLE_COUNTRIES']		=   $_SESSION['VATABLE_COUNTRIES'];
    //echo (int)$_POST["i"]+count($_SESSION['invoice_current_country_array']) + $_SESSION['pieces']; 
    //Reset the country subtotal array
    $glOrderPdf = new creditNoteFullTemplate();
    $arr_invoice = $_SESSION["array_invoice"];
    $invoice_file_link = $glOrderPdf->AddHTML(true, $_SESSION['CURRENT_CREDIT_ACCOUNT'], $credit_array[$id], '1', $arrarUsers);


    $_SESSION["array_invoice"] = array();
    $_SESSION["invoice_files"][] = $invoice_file_link;
    $_SESSION['CREDIT-NOTE-DATA'] = new CreditNote($id);
}

if (isset($_POST['action']) && trim($_POST['action']) == 'PUT-CREDIT-DATA-ON-FILE') {
    if ($_POST["i"] == 0) {
        $_SESSION["invoice_files"] = array();
        $_SESSION["array_invoice"] = array();
        $_SESSION['invoice_current_country_array'] = array();
        $_SESSION['pieces'] = 0;
        $_SESSION['counter'] = 0;
    }

    $id = $_POST['id'];
    $invoice_array = array();
    $consignment = new ConsignmentFilter();
    $consignment->addIdFilter($id);
    $invoice_row = $consignment->getColumnList(' id, hawb, date_booked, reference, city, country, country_iso_code, is_doc, weight, number_pieces, hv_lv, handling,update_weight, weight_comment, remote_charges, postcode, account, credit_amount, invoice_amount, credit_description  ');
    $id = $invoice_row[0]->getId();
    $hawb = $invoice_row[0]->getHAwb();
    $datebooked = $invoice_row[0]->getDateBooked();
    $reference = $invoice_row[0]->getReference();
    $destination = substr($invoice_row[0]->getCity(), 0, 14) . '..., ' . $invoice_row[0]->getCountryIsoCode();
    $doctype = $invoice_row[0]->getIsDoc();
    $weight = $invoice_row[0]->getWeight();
    $pieces = $invoice_row[0]->getNumberPieces();
    $hvlv = $invoice_row[0]->getHvLv();
    $remoteCharges = $invoice_row[0]->getRemoteCharges();
    $postcode = $invoice_row[0]->getPostcode();
    $account = $invoice_row[0]->getAccount();
    $creditAmount = $invoice_row[0]->getCreditAmount();
    $invoiceAmount = $invoice_row[0]->getInvoiceAmount();
    $creditDescription = $invoice_row[0]->getCreditDescription();

    if ($pieces > 1)
        $_SESSION['pieces'] = $pieces + $_SESSION['pieces'];

    $isoname = $invoice_row[0]->getCountryIsoCode();
    $countryOption = '';
    if ($isoname == '') {
        $isoname = $invoice_row[0]->getCountry();
        $countryOption = 'name';
    }
    $country = getCountryId($isoname, $countryOption);
    $handling = $invoice_row[0]->getHandling();
    $updateWeight = $invoice_row[0]->getUpdateWeight();
    $weightComment = $invoice_row[0]->getWeightComment();


    if (count($_SESSION['invoice_current_country_array']) == 0) {
        $_SESSION['invoice_current_country_array'][] = $country;
    } elseif (!in_array($country, $_SESSION['invoice_current_country_array'])) {
        $_SESSION['invoice_current_country_array'][] = $country;
    }

    $invoice_array[$id]['id'] = $id;
    $invoice_array[$id]['hawb'] = $hawb;
    $invoice_array[$id]['datebooked'] = $datebooked;
    $invoice_array[$id]['reference'] = $reference;
    $invoice_array[$id]['destination'] = $destination;
    $invoice_array[$id]['doctype'] = $doctype;
    $invoice_array[$id]['weight'] = $weight;
    $invoice_array[$id]['pieces'] = $pieces;
    $invoice_array[$id]['hvlv'] = $hvlv;
    $invoice_array[$id]['country'] = $country;
    $invoice_array[$id]['handling'] = $handling;
    $invoice_array[$id]['updateWeight'] = $updateWeight;
    $invoice_array[$id]['weightComment'] = $weightComment;
    $invoice_array[$id]['remote_area'] = $remoteCharges;
    $invoice_array[$id]['postcode'] = $postcode;
    $invoice_array[$id]['account'] = $account;
    $invoice_array[$id]['creditAmount'] = $creditAmount;
    $invoice_array[$id]['invoiceAmount'] = $invoiceAmount;
    $invoice_array[$id]['creditDescription'] = $creditDescription;


    $_SESSION["array_invoice"][$id] = $invoice_array[$id];
    $arrarUsers['userName'] = $_SESSION['CURRENT_INVOICE_FULLNAME'];
    $arrarUsers['userCompany'] = $_SESSION['CURRENT_INVOICE_COMPANY'];
    $arrarUsers['userAddress'] = $_SESSION['CURRENT_INVOICE_ADDRESS'];
    $arrarUsers['userCountry'] = $_SESSION['CURRENT_INVOICE_COUNTRY'];
    $arrarUsers['userAccount'] = $_SESSION['CURRENT_INVOICE_ACCOUNT'];
    $arrarUsers['userCurrency'] = $_SESSION['CURRENT_INVOICE_CURRENCY'];
    $arrarUsers['vatchargable'] = $_SESSION['vatchargable'];
    $arrarUsers['vatvalue'] = $_SESSION['vatvalue'];
    $arrarUsers['charges'] = $_SESSION['charges'];
    $arrarUsers['VATABLE_COUNTRIES'] = $_SESSION['VATABLE_COUNTRIES'];

    if (((int) $_POST["i"] + count($_SESSION['invoice_current_country_array']) + $_SESSION['pieces']) % INVOICE_PER_PAGE_LIMIT == 0 && $_POST["i"] > 0) {
        //echo (int)$_POST["i"]+count($_SESSION['invoice_current_country_array']) + $_SESSION['pieces']; 
        //Reset the country subtotal array
        $glOrderPdf = new creditNoteTemplate();
        $arr_invoice = $_SESSION["array_invoice"];
        $invoice_file_link = $glOrderPdf->AddHTML(true, $_SESSION['CURRENT_INVOICE_ACCOUNT'], $arr_invoice, ((int) $_POST["i"] + count($_SESSION['invoice_current_country_array'])), $arrarUsers);
        $_SESSION["array_invoice"] = array();
        $_SESSION["invoice_files"][] = $invoice_file_link;

        //$lastCountryId	=	$_SESSION['invoice_current_country_array'][count($_SESSION['invoice_current_country_array'])-1];
        //$_SESSION['invoice_current_country_array'] =	array();
        //$_SESSION['invoice_current_country_array'][] =	$lastCountryId;
    }
    if ($_POST["i"] == ($_SESSION['sizeOfArr'] - 1)) {
        $glOrderPdf = new creditNoteTemplate();
        $arr_invoice = $_SESSION["array_invoice"];
        $invoice_file_link = $glOrderPdf->AddHTML(true, $_SESSION['CURRENT_INVOICE_ACCOUNT'], $arr_invoice, $_POST["i"], $arrarUsers);
        $_SESSION["array_invoice"] = array();
        $_SESSION["invoice_files"][] = $invoice_file_link;
    }
}

if (isset($_POST['action']) && trim($_POST['action']) == 'MERGE-CREDIT-FILE') {
    $pdf2 = new PDFMerger();
    $arr = $_SESSION["invoice_files"];
    $creditNumber = $_POST['creditNumber'];
//print_r($arr);
    $prefix = date("Ymd");
    $mergeFileName = 'CRN-' . $creditNumber . ".pdf"; //uniqid($prefix) . ".pdf";

    foreach ($arr as $labelArr) {
        $pdf2->addPDF($labelArr, 'all');
    }

    $PDFfileLocationNew = dirname(__FILE__) . '/credit_notes/pdf/' . $mergeFileName;
    $PDFfileLocationNew = str_replace('main/', '', $PDFfileLocationNew);


    $pdf2->merge('file', $PDFfileLocationNew);
    $_SESSION['CREDIT-NOTE-DATA']->setPdfFile($mergeFileName);
    $_SESSION['CREDIT-NOTE-DATA']->save();


//	SERVER_IP_INTERNAL
//	$ftp_object1 = new FTPfile("invoices_ftp", "OweInv123@", '213.246.110.102' );
    $ftp_object1 = new FTPfile(SERVER_USERNAME_INVOICE_INTERNAL, SERVER_PASSWORD_INVOICE_INTERNAL, SERVER_IP_INTERNAL);
    $ftp_object1->cd('smartsystem_invoices');
    $remote_file_path1 = $_SESSION['CREDIT-NOTE-DATA']->getUserAccount() . '_' . "" . $mergeFileName;
    $yearDirectory = date('Y', strtotime($_SESSION['CREDIT-NOTE-DATA']->getCnDate()));
    $monthDirectory = date('F', strtotime($_SESSION['CREDIT-NOTE-DATA']->getCnDate()));
    $dayDirectory = date('d', strtotime($_SESSION['CREDIT-NOTE-DATA']->getCnDate()));
    $ftp_object1->cd('credit_notes');
    $ftp_object1->ftp_is_dir($yearDirectory);
    $ftp_object1->ftp_is_dir($monthDirectory);
    $ftp_object1->ftp_is_dir($dayDirectory);
    $ftp_object1->passive();
    $isCMSUpload = $ftp_object1->put($remote_file_path1, $PDFfileLocationNew, FTP_ASCII);
    @$ftp_object1->chmod($remote_file_path1, 0777);
    if ($isCMSUpload == true)
        echo "SUCCESS||You have successfully generated credit note. Please <a href='.." . '/credit_notes/pdf/' . $mergeFileName . "' target='_blank'>click here</a> to download";
    else
        echo "SUCCESS||You have successfully generated  credit note but file is not transfered to the server. Please contact to administrator. . <a href='credit_note.php'>Click Here</a> to view all credit notes or <a href='.." . '/credit_notes/pdf/' . $mergeFileName . "' target='_blank'>click here</a> to download ";





    unset($_SESSION["credit_note_data"]);
    unset($_SESSION["invoice_files"]);
    unset($_SESSION["invoice_filter"]);
    unset($_SESSION["VATABLE_COUNTRIES"]);
    unset($_SESSION["CURRENT_CREDIT_ACCOUNT"]);
    unset($_SESSION["CURRENT_CREDIT_NUMBER"]);
    unset($_SESSION["CURRENT_INVOICE_FULLNAME"]);
    unset($_SESSION["CURRENT_INVOICE_COMPANY"]);
    unset($_SESSION["CURRENT_INVOICE_ADDRESS"]);
    unset($_SESSION["CURRENT_INVOICE_COUNTRY"]);
    unset($_SESSION["CURRENT_INVOICE_ACCOUNT"]);
    unset($_SESSION["CURRENT_INVOICE_CURRENCY"]);
    unset($_SESSION["vatchargable"]);
    unset($_SESSION["vatvalue"]);
    unset($_SESSION["array_invoice"]);
    unset($_SESSION["invoice_page_number"]);
    unset($_SESSION["invoice_files"]);
    unset($_SESSION["CREDIT-NOTE-DATA"]);

//  echo "<pre>";
//  print_r($_SESSION);
}

function getCountryId($country, $option = '') {
    if ($option == 'name')
        return Country::getIdFromName($country);
    else
        return Country::getIdFromIso($country);
}
