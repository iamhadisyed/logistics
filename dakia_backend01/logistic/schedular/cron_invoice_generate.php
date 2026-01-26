<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'invoicetemplate.class'
],'invoices');
include_classes([
    'tcpdf'
],'3rdparty/tcpdf');

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'licenceplate.class',
    'licenceplatefilter.class'
]);
@session_start();
DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$consignmentData = new ConsignmentFilter();
$consignmentData->addFilter("invd.invoice_no IS NULL        OR invd.invoice_no = ''", 'invoicedetailfilter');

$consignmentData->addGroupBy('ua.id');
$consignmentDataSet = $consignmentData->getColumnList("ua.id 'user_account_id', ua.user_account , ua.company user_company , ua.country_id 'user_country_id', ua.billing_email, ua.billing_currency, ua.billing_address, ua.invoice_period, ua.vat_chargable, ua.vat_value");
$userAccountData = array();
$count = count($consignmentDataSet);
if (count($consignmentDataSet) > 0) {
    foreach ($consignmentDataSet as $userData) {
        $userAccountData[$userData->getUserAccountId()]['COUNTRY'] = $userData->getUserCountryId();
        $userAccountData[$userData->getUserAccountId()]['COMPANY'] = $userData->getUserCompany();
        $userAccountData[$userData->getUserAccountId()]['ACCOUNT_ID'] = $userData->getUserAccountId();
        $userAccountData[$userData->getUserAccountId()]['ACCOUNT'] = $userData->getUserAccount();
        $userAccountData[$userData->getUserAccountId()]['BILLING_EMAIL'] = $userData->getBillingEmail();
        $userAccountData[$userData->getUserAccountId()]['BILLING_CURRENCY'] = $userData->getBillingCurrency();
        $userAccountData[$userData->getUserAccountId()]['BILLING_ADDRESS'] = $userData->getBillingAddress();
        $userAccountData[$userData->getUserAccountId()]['INVOICE_PERIOD'] = $userData->getInvoicePeriod();
        $userAccountData[$userData->getUserAccountId()]['VAT_CHARGABLE'] = $userData->getVatChargable();
        $userAccountData[$userData->getUserAccountId()]['VAT_PERCENT'] = $userData->getVatValue();
    }
}

$selectVatableCountries =   new CountryFilter();
$selectVatableCountries->addFilter(" is_vatable = 'YES'" );
$vatableCountry =   $selectVatableCountries->getColumnList("id, iso, name");
$vatableCountriesIso   =   array();
if(count($vatableCountry)>0)
{
    foreach($vatableCountry as $vatAblecountry)
    {
        $vatableCountriesIso[] =  $vatAblecountry->getIso();
    }
}

if (!empty($userAccountData)) {
    foreach ($userAccountData as $userAccountId => $userAccountDetail) {
        $invoicePeriod = $userAccountDetail['INVOICE_PERIOD'];
        $consignmentLabelData = new ConsignmentFilter();

        switch ($invoicePeriod) {
            case 'daily':
                $today = strtotime('00:00:00');
                $yesterday = strtotime('-1 day', $today);
                //AND  c.date_label_created >= $yesterday
                $consignmentLabelData->addFilter("c.date_label_created > 0 AND  c.date_label_created < $today   ", 'consignmentfilter');
                break;
            case 'weekly':
                $previous_week = strtotime("-1 week " . "00:00:00");
                $start_week = strtotime("last monday midnight", $previous_week);
                $end_week = strtotime("next monday", $start_week);
                //AND  c.date_label_created >= $start_week
                $consignmentLabelData->addFilter("c.date_label_created > 0 AND  c.date_label_created < $end_week  ", 'consignmentfilter');
                break;
            case 'monthly':
                $first_day_month = strtotime("first day of previous month");
                $last_day_month = strtotime("last day of previous month" . "23:59:59");
                //AND  c.date_label_created >= $first_day_month
                $consignmentLabelData->addFilter("c.date_label_created > 0 AND  c.date_label_created < $last_day_month   ", 'consignmentfilter');
                break;
            case 'bi-monthly':
                $first_day_month = strtotime("first day of previous month");
                $last_day_month = strtotime("last day of previous month" . "23:59:59");
                //AND  c.date_label_created >= $first_day_month
                $consignmentLabelData->addFilter("c.date_label_created > 0 AND  c.date_label_created < $last_day_month ", 'consignmentfilter');
                break;
        }

        $consignmentLabelData->addFilter("invd.invoice_no IS NULL        OR invd.invoice_no = ''", 'invoicedetailfilter');
        $consignmentLabelData->AddOrderBy("con.name", "order_by", true);
        $selectedColumns = "c.id, c.hawb, c.consignment_status, c.reference, c.date_created, c.date_booked, c.date_label_created, c.date_delivered, c.awb ,
         c.city, c.is_doc,  c.weight, c.number_pieces, c.hv_lv,  c.vol_weight, c.remote_charges, c.postcode, con.name 'country_name',con.iso 'country_iso_code',
					s.code 'service_code',ca.carrier 'carrier_name', s.name 'service_name', ua.user_account, 
					invd.label_price, invd.label_discount, invd.basic_charges, invd.fuel_charges, invd.additional_charges, invd.remote_area_charge, 
					invd.on_farword_charges, invd.ndx, invd.ddp, invd.extra, invd.hv, invd.discount,  invd.ancillary_charges, invd.ancillary_charges_details,
					 invd.amount, c.invoice_id, c.invoice_type, c.vol_demonimator";

        $consignmentDataSet = $consignmentLabelData->getColumnList($selectedColumns);

        /*
        *       GetParcelData   =
        */
        $consignmentDataArrayId =   array();
        $allParcelConsignmentData   =   array();
        $consignmentParcelDataSet = $consignmentLabelData->getColumnList(" c.id ");
        if(count($consignmentParcelDataSet)>0)
        {
            foreach($consignmentParcelDataSet as $consignmentIdData)
                $consignmentDataArrayId[] =   $consignmentIdData->getId();

            $parcelFilterdata = new ParcelFilter();
            $parcelFilterdata->addConsignmentIdFilterNew($consignmentDataArrayId);
            $consignmentParcelData = $parcelFilterdata->getColumnList('length, width, height, consignment_id, licence_plate');
            if (count($consignmentParcelData) > 0) {
                foreach ($consignmentParcelData as $consignmentParcelItemData) {
                    $allParcelConsignmentData[$consignmentParcelItemData->getConsignmentId()][] = array(
                        'lenght' => $consignmentParcelItemData->getLength(),
                        'width' => $consignmentParcelItemData->getWidth(),
                        'awb' => $consignmentParcelItemData->getTrackingNumber(),
                        'height' => $consignmentParcelItemData->getHeight()
                    );
                }
            }
        }


        $responceData = downloadPDF($consignmentDataSet, $allParcelConsignmentData, $userAccountDetail, $vatableCountriesIso);
        if (trim($responceData['STATUS']) == 'SUCCESS') {
            $consignmentIdArray = $responceData['DATA']['CONSIGNMENT_ID'];
            $successMessage = $responceData['MESSAGE'];
            $invoiceFile = $responceData['DATA']['FILENAME'];
            $invoiceNumber = $responceData['DATA']['INVOICE'];
            $invoiceIdDb = $responceData['DATA']['INVOICE_DB'];
            $sqlInvoiceDetailQuery   =   " UPDATE invoice_detail SET invoice_id ='".$invoiceIdDb."' , invoice_no = '".$invoiceNumber."' WHERE consignment_id > 0 AND consignment_id <> '' AND consignment_id <> 0  AND consignment_id IN ('".implode("','", $consignmentIdArray)."') ";
                DbAccess3::runQueryWithError($sqlInvoiceDetailQuery);
            $sqlConsignmentQuery   =   " UPDATE consignment SET invoice_id ='".$invoiceIdDb."', invoice_type = 'INV' , is_invoiced = 1 WHERE id > 0 AND id <> '' AND id <> 0  AND id IN ('".implode("','", $consignmentIdArray)."') ";
                DbAccess3::runQueryWithError($sqlConsignmentQuery);
            downloadCSV($consignmentDataSet, $allParcelConsignmentData, $userAccountDetail, $invoiceIdDb);

        }


    }
}
function downloadPDF($dataConsignment, $parcelConsignmentData = array() , $userData, $vatableCountriesIso)
{

    $sessionArrayValues = array();
    $sessionArrayValues['invoice_weight_total'] = 0;
    $sessionArrayValues['invoice_sub_total'] = 0;
    $sessionArrayValues['invoice_vatable_total'] = 0;
    $sessionArrayValues['invoice_country_sub_total'] = 0;
    $sessionArrayValues['invoice_fuel_surcharge'] = 0;
    $sessionArrayValues['invoice_consignment_hawb'] = array();
    $sessionArrayValues['invoice_tarrif_invalid'] = array();
    $sessionArrayValues['invoice_current_country'] = '';
    $sessionArrayValues['invoice_page_number'] = 1;
    $sessionArrayValues['invoice_current_country_array'] = array();
    $sessionArrayValues['invoice_current_country_counter'] = 0;
    $sessionArrayValues['invoice_page_sub_total'] = 0;

    if (sizeof($dataConsignment) > 0) {
        $invoice_id = LicencePlate::getLicencePlateNumber(122); // AUTO INVOICES ID IS 122
        if ($invoice_id > 0) {
            $_SESSION['INVOICE'][$invoice_id] = $sessionArrayValues;
            $glOrderPdf = new invoicetemplate($invoice_id, $userData);
            $invoice_data = $glOrderPdf->addConsignmetToInvoice($dataConsignment, 1, $parcelConsignmentData, $vatableCountriesIso);

            $responceArray['MESSAGE'] = "You have successfully generated invoice.";
            $responceArray['STATUS'] = "SUCCESS";
            $responceArray['DATA'] = $invoice_data; //"Please download invoice by <a href='".$urlParts[1]."' target='_blank' >Click Here</a>";
            $responceArray['ACCOUNT'] = $userData['ACCOUNT']; //"Please download invoice by <a href='".$urlParts[1]."' target='_blank' >Click Here</a>";
        } else {
            $responceArray['STATUS'] = "ERROR";
            $responceArray['MESSAGE'] = "Invoice Number ranges has been finish. Please contact to administrator";
        }
    } else {
        $responceArray['STATUS'] = "ERROR";
        $responceArray['MESSAGE'] = "Shippment data not found";
    }
    return $responceArray;
}

function downloadCSV($consignmentDataSet, $allParcelConsignmentData, $userAccountDetail, $invoiceIdDb){
    invoicetemplate::generateCsv($consignmentDataSet, $userAccountDetail, $invoiceIdDb);
}

?>

	
	


