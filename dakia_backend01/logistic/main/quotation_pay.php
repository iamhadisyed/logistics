<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../includes/library/vendor/autoload.php");
include_classes([
	'pdfmerger'
], 'labels');
include_classes([
	'tcpdf'
], '3rdparty/tcpdf');
include_classes([
	'carrierservice.class'
], 'general');
include_classes([
	'include_list',
], 'reamus');
include_classes([
	'ups.class'
], 'labels');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'currency.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'serviceconstantvaluefilter.class',
    'serviceconstantvalue.class',
    'servicerangemappingfilter.class',
    'servicerangemapping.class',
    'licenceplatefilter.class',
    'licenceplate.class',
    'warehousefilter.class',
    'warehouse.class',
    'trackingfilter.class',
    'tracking.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    'quotationdetails.class',
	'quotationdetailsfilter.class',
    'quotationpaymenthistory.class',
    'quotationpaymenthistoryfilter.class'
]);

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    private $quotationDetailsFilter = array();
    private $user = '';
    private $quotationId = 0;
    private $apiContext = [];
    private $quotationObj = [];
    private $consignmentObj = [];
    private $fieldsData = [];

    protected function init()
    {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Quotation List"
        );

        if (isset($_GET['key']) && ($_GET['key'] != '')) {
            $key = $_GET['key'];
            $filter = new QuotationDetailsFilter();
            $filter->where(["MD5(id)" => DbAccess3::escape($key)]);
            $filterObj = $filter->getList();
            if(count($filterObj)) {
                $this->quotationObj =  $filterObj[0];
                $this->quotationId = $this->quotationObj->getId();
                if(count($this->quotationObj) && $this->quotationObj->getQuotationData() != "") {
                    $this->fieldsData = unserialize($this->quotationObj->getQuotationData());
                }
            }
        }


        if (isset($_GET['skey']) && ($_GET['skey'] != '')) {
            $skey = $_GET['skey'];
            $filter = new ConsignmentFilter();
            $filter->addFieldFilter("   MD5(c.id)" , $skey);
            $filterObj = $filter->getListNew();
            if(count($filterObj)) {
                $this->consignmentObj = $filterObj[0];
            }
        }
        $data = $_POST;
        if(isset($data['PAYID']) && $data['PAYID'] != "") {
            $string = '<pre>' . print_r($data, true) . '</pre>';
            $file = BASE_PATH.'main/barclays.html';
            file_put_contents($file, "update log in barclay inp request \n" . $string . date('Y-m-d H:i:s') . "update log in barclay inp request \n" . PHP_EOL, FILE_APPEND);
            $status = $data['STATUS'];
            $order_id = $data['orderID'];
            $message = "";
            if ($status == 9) {
                $update = [];
                $transaction_id = $data['PAYID'];
                $payer_email = "";
                $payment_status = $status;
                $amount = $data['amount'];

                $quotationPaymentsHistoryUpdate = new QuotationPaymentsHistory($order_id);
                $originalAmount = $quotationPaymentsHistoryUpdate->getAmount();
                $quotationPaymentsHistoryUpdate->setTxnId($transaction_id);
                $quotationPaymentsHistoryUpdate->setSenderEmail($payer_email);
                $quotationPaymentsHistoryUpdate->setPaymentStatus($payment_status);
                if ($payment_status == 9 && $amount == $originalAmount) {
                    $quotationPaymentsHistoryUpdate->setIsCompleted('yes');
                }
                $quotationPaymentsHistoryUpdate->save();
                // save order to smarttrack
                /* Send Order Email */
                $quoteObj = new QuotationDetails($quotationPaymentsHistoryUpdate->getQuotationId());
                $formDataArray = unserialize($quotationPaymentsHistoryUpdate->getQuoteData());
                $res = $this->add_consignment($formDataArray);
                $this->paymentEmailTemplate($quoteObj->getAccountId(),$quoteObj,$quotationPaymentsHistoryUpdate);
                if(isset($res['STATUS']) && $res['STATUS'] == "SUCCESS") {
                    $quotationPaymentsHistoryUpdateConsignmentId = new QuotationPaymentsHistory($order_id);
                    $quotationPaymentsHistoryUpdate->setConsignmentId($res['CONSIGNMENT_ID']);
                    $quotationPaymentsHistoryUpdateConsignmentId->save();
                    $this->emailTemplate($quoteObj->getAccountId(),$res);
                }
            }
        }
        // Barclaycard payment accepted
        if (isset($_GET['action']) && $_GET['action'] == 'accepted') {
            $dt = $_GET;
            $paymentHistoryId = $dt['orderID'];
            $quotationPymentHistoryObj = new QuotationPaymentsHistory($paymentHistoryId);
            $skey = md5($quotationPymentHistoryObj->getConsignmentId());
            header("Location: ".BASE_URL."quotation_pay.php?skey=".$skey."&message=success");
            die;
        }
        // Barclaycard payment declined
        if (isset($_GET['action']) && $_GET['action'] == 'declined') {
            header("Location: ".BASE_URL."quotation_pay.php?skey=".$skey."&message=declined");
            die;
        }
        // Barclaycard payment exception
        if (isset($_GET['action']) && $_GET['action'] == 'exception') {
            header("Location: ".BASE_URL."quotation_pay.php?skey=".$skey."&message=exception");
            die;
        }
        // Barclaycard payment cancel
        if (isset($_GET['action']) && $_GET['action'] == 'cancel') {
            header("Location: ".BASE_URL."quotation_pay.php?skey=".$skey."&message=cancel");
            die;
        }
        // Barclaycard payment save-payment
        if (isset($_GET['action']) && $_GET['action'] == 'save-payment') {
            $file = BASE_PATH.'main/barclays.html';
            file_put_contents($file, "Yes save walay main" . PHP_EOL, FILE_APPEND);
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'paypal_create_payment') {
            $output = [];
            $quoteData = serialize($this->form_vars);
            $formDataArray = $this->form_data();
            $quoteId = $this->quotationId;
            $quoteObj = new Quotationdetails($quoteId);
            $accountId = $quoteObj->getAccountId();
            $userAccountObj = new CustomerAccount($accountId);
            $consignmentData = $this->consigmentData($formDataArray);
            $consignment = Consignment::getConsignmentObjectFromArray($consignmentData);
            $parcel = $formDataArray['parcel'];
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter(' user_account_id',$accountId);
            $userObj = $userFilter->getList();
            if(count($userObj)) {
                $userObj = $userObj[0];
            }
            /* validate paypal setting in account */
            $paypalValid = $this->getPaypalApiContext($accountId);
            if($paypalValid['status'] == "error") {
                $htmlError = '<li>Paypal Client Id or Secret is invalid</li>';
                $return['status'] = 'error';
                $return['error'] = $htmlError;
                echo json_encode($return);
                die;
            }
            $consignmentValidator = new ConsignmentValidator($consignment, $parcel,$userObj,$userAccountObj);
            $IsValidConsignment = $consignmentValidator->validateConsignment();
            if(!$IsValidConsignment) {
                $error = $consignmentValidator->getErrorList();
                $htmlError = '';
                if(count($error)) {
                    foreach($error as $e) {
                        $htmlError .= '<li>'.$e.'</li>';
                    }
                }
                $return['status'] = 'error';
                $return['error'] = $htmlError;
                echo json_encode($return);
                die;
            } else {
                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $return_url = $actual_link . "/quote_paypal_success.php";
                $cancel_url = $actual_link . "/quote_paypal_cancel.php";

                $amountAdd = $formDataArray['amount'];
                $currencyId = $quoteObj->getCurrencyId();
                $isCompleted = "no";
                $paymentDetail = "Quotation payment of consignment";
                $paymentStatus = "pending";
                $date_added = time();
                $date_update = time();
                $quotationPaymentsHistory = new QuotationPaymentsHistory();
                $quotationPaymentsHistory->setQuotationId($quoteId);
                $quotationPaymentsHistory->setAmount($amountAdd);
                $quotationPaymentsHistory->setAmountCurrencyId($currencyId);
                $quotationPaymentsHistory->setPaymentMethod('paypal');
                $quotationPaymentsHistory->setPaymentDetail($paymentDetail);
                $quotationPaymentsHistory->setPaymentStatus($paymentStatus);
                $quotationPaymentsHistory->setIsCompleted($isCompleted);
                $quotationPaymentsHistory->setQuoteData($quoteData);
                $quotationPaymentsHistory->setDateAdded($date_added);
                $quotationPaymentsHistory->setDateUpdated($date_update);
                $quotationPaymentsHistory->save();
                /* create payment paypal */
                $payer = new PayPal\Api\Payer();
                $payer->setPaymentMethod('paypal');

                $currency = new Currency($currencyId);
                $amount = new PayPal\Api\Amount();
                $amount->setTotal($amountAdd);
                $amount->setCurrency($currency->getRightsymbol());

                $transaction = new PayPal\Api\Transaction();
                $transaction->setAmount($amount);
                $transaction->setDescription("Pay Quote Consignment");
                $transaction->setCustom($quotationPaymentsHistory->getId());

                $redirectUrls = new PayPal\Api\RedirectUrls();
                $redirectUrls->setReturnUrl($return_url)->setCancelUrl($cancel_url);

                $payment = new PayPal\Api\Payment();
                $payment->setIntent('sale');
                $payment->setPayer($payer);
                $payment->setTransactions(array($transaction));
                $payment->setRedirectUrls($redirectUrls);

                $this->getPaypalApiContext($accountId);

                $paymentData = $payment->create($this->apiContext);
                $paypal_payment_id = $paymentData->id;
                /* update payment historey */
                $quotationPaymentsHistoryUpdate = new QuotationPaymentsHistory($quotationPaymentsHistory->getId());
                $quotationPaymentsHistoryUpdate->setPaypalPaymentId($paypal_payment_id);
                $quotationPaymentsHistoryUpdate->save();
                echo $paymentData;
                die;
            }
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'paypal_execute_payment') {
            $quoteId = $this->quotationId;
            $formDataArray = $this->form_data();
            $quoteObj = new Quotationdetails($quoteId);
            $accountId = $quoteObj->getAccountId();
            $this->getPaypalApiContext($accountId);
            $paymentId = $this->form_vars['payment_id'];
            $payerId = $this->form_vars['payer_id'];
            $payment = PayPal\Api\Payment::get($paymentId, $this->apiContext);
            // Execute payment with payer ID
            $execution = new PayPal\Api\PaymentExecution();
            $execution->setPayerId($payerId);
            $result = $payment->execute($execution, $this->apiContext);
            /* Update payment success */
            $state = $result->state;
            $status = $result->payer->status;
            $quotation_payment_history_id = "";
            $quotationPaymentHistoryObj = [];
            if($state == "approved" && ($status == "VERIFIED" ||  $status == "UNVERIFIED")) {
                $paypal_payment_id = $result->id;
                $quotation_payment_history_id = $result->transactions[0]->custom;
                $transaction_id = $result->transactions[0]->related_resources[0]->sale->id;
                $payer_email = $result->payer->payer_info->email;
                $payment_status = $result->transactions[0]->related_resources[0]->sale->state;
                $amount = $result->transactions[0]->related_resources[0]->sale->amount->total;
                /* Save Consignment */
                $res = $this->add_consignment($formDataArray);
                /* quotation payment history obj for compare */
                $quotationPaymentHistoryObj = new QuotationPaymentsHistory($quotation_payment_history_id);
                /* update payment history */
                $quotationPaymentHistoryUpdate = new QuotationPaymentsHistory($quotation_payment_history_id);
                if(isset($res['STATUS']) && $res['STATUS'] == "SUCCESS") {
                    $quotationPaymentHistoryUpdate->setConsignmentId($res['CONSIGNMENT_ID']);
                }
                $quotationPaymentHistoryUpdate->setTxnId($transaction_id);
                $quotationPaymentHistoryUpdate->setSenderEmail($payer_email);
                $quotationPaymentHistoryUpdate->setPaymentStatus($payment_status);
                if(($payment_status == "completed") && ($amount == $quotationPaymentHistoryObj->getAmount()) && ($paypal_payment_id == $quotationPaymentHistoryObj->getPaypalPaymentId())) {
                    $quotationPaymentHistoryUpdate->setIsCompleted("yes");
                }
                $quotationPaymentHistoryUpdate->save();
                $quotationPaymentHistoryObjNew = new QuotationPaymentsHistory($quotation_payment_history_id);
                $this->paymentEmailTemplate($accountId,$quoteObj,$quotationPaymentHistoryObjNew);
                if(isset($res['STATUS']) && $res['STATUS'] == "SUCCESS") {
                    $this->emailTemplate($accountId,$res);
					$res['key'] = md5($res['CONSIGNMENT_ID']);
                }
				echo json_encode($res);
            }
            /* Update payment success */
            //echo $quotation_payment_history_id;
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'barclay_create_payment') {
            $output = [];
            $quoteId = $this->quotationId;
            $formDataArray = $this->form_data();
            $quoteData = serialize($formDataArray);
            $quoteObj = new Quotationdetails($quoteId);
            $accountId = $quoteObj->getAccountId();
            $userAccountObj = new CustomerAccount($accountId);
            $consignmentData = $this->consigmentData($formDataArray);
            $consignment = Consignment::getConsignmentObjectFromArray($consignmentData);
            $parcel = $formDataArray['parcel'];
            $userFilter = new UserFilter();
            $userFilter->addFieldFilter(' user_account_id',$accountId);
            $userObj = $userFilter->getList();
            if(count($userObj)) {
                $userObj = $userObj[0];
            }
            $consignmentValidator = new ConsignmentValidator($consignment, $parcel,$userObj,$userAccountObj);
            $IsValidConsignment = $consignmentValidator->validateConsignment();
            if(!$IsValidConsignment) {
                $error = $consignmentValidator->getErrorList();
                $htmlError = '';
                if(count($error)) {
                    foreach($error as $e) {
                        $htmlError .= '<li>'.$e.'</li>';
                    }
                }
                $return['status'] = 'error';
                $return['error'] = $htmlError;
                echo json_encode($return);
                die;
            } else {
                $amountAdd = $this->form_vars['amount'];
                $currencyId = $quoteObj->getCurrencyId();
                /* Convert currency to GBP */
                if(!empty($currencyId) && $currencyId != 2) {
                    $currency = new Currency($currencyId);
                    $amountAdd = Currency::convertCurrency($currency->getRightsymbol(),'GBP',$amountAdd);
                    $currencyId = 2;
                }
                $isCompleted = "no";
                $paymentDetail = "Quotation payment of consignment";
                $paymentStatus = "pending";
                $date_added = time();
                $date_update = time();
                $quotationPaymentsHistory = new QuotationPaymentsHistory();
                $quotationPaymentsHistory->setQuotationId($quoteId);
                $quotationPaymentsHistory->setAmount($amountAdd);
                /* GBP currency hardcode */
                $quotationPaymentsHistory->setAmountCurrencyId($currencyId);
                $quotationPaymentsHistory->setPaymentMethod('barclays');
                $quotationPaymentsHistory->setPaymentDetail($paymentDetail);
                $quotationPaymentsHistory->setPaymentStatus($paymentStatus);
                $quotationPaymentsHistory->setIsCompleted($isCompleted);
                $quotationPaymentsHistory->setQuoteData($quoteData);
                $quotationPaymentsHistory->setDateAdded($date_added);
                $quotationPaymentsHistory->setDateUpdated($date_update);
                $quotationPaymentsHistory->save();
                $quotationPaymentsHistoryId = $quotationPaymentsHistory->getId();
                /* Barclays payment */
                $ShopperLocale = "en_GB";
                $CurrencyCode = "GBP";
                $PaymentAmount = ($amountAdd * 100);            // this is 1 pound (100p)
                $OrderID = $quotationPaymentsHistoryId;    // Order Id - needs to be unique
                //- integration user details - //
                $PW = 'Stellar123456789';
                $PSPID = 'epdq1490589';
                /* = create string to hash (digest) using values of options/details above */
                $DigestivePlain = "AMOUNT=" . $PaymentAmount . $PW .
                            "CURRENCY=" . $CurrencyCode . $PW .
                            "LANGUAGE=" . $ShopperLocale . $PW .
                            "ORDERID=" . $OrderID . $PW .
                            "PSPID=" . $PSPID . $PW .
                            "";
                $strHashedString_plain = SHA1($DigestivePlain);

                $return['amount'] = $PaymentAmount;
                $return['currency'] = $CurrencyCode;
                $return['language'] = $ShopperLocale;
                $return['orderid'] = $OrderID;
                $return['pspid'] = $PSPID;
                $return['shasign'] = $strHashedString_plain;
                $return['status'] = 'success';
                $return['quotation_payment_id'] = $quotationPaymentsHistoryId;
                echo json_encode($return);
                die;
            }
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'new_quotation_save') {
            $output = [];
            $quoteId = $this->quotationId;
            $quoteObj = new QuotationDetails($quoteId);
            $parcels = $this->form_vars['parcel'];
            $totalWeight = 0.00;
            $pieces = count($parcels);
            $conversionRate = $quoteObj->getConversionrate();
            if(count($parcels)) {
                foreach($parcels as $ke => $parcel) {
                    $totalWeight += $parcel['weight'];
                    $parcels[$ke]['weight_type'] = 'kg';
                    $parcels[$ke]['vol_weight'] = ((($parcel['length'] * $parcel['width']) * $parcel['height']) /  $conversionRate);
                }
            }
            $userAccountId = $quoteObj->getAccountId();
            $fromCountry = $this->form_vars['sender_country'];
            $toCountry = $this->form_vars['receiver_country'];
            $fromPostcode = $this->form_vars['sender_postcode'];
            $toPostcode = $this->form_vars['receiver_postcode'];
            $fromCity = $this->form_vars['sender_city'];
            $toCity = $this->form_vars['receiver_city'];
            $weight = $totalWeight;
            $pieces = $pieces;
            $serviceId = $quoteObj->getServiceId();
            $result = Tariffs::getUserQuotationsByAssignedServices($userAccountId, $fromCountry, $toCountry,$fromPostcode,$toPostcode,$fromCity,$toCity,$weight,$pieces,$serviceId);
            $quotation = [];
            if(isset($result['QUOTATIONS'][0])) {
                $quotation = $result['QUOTATIONS'][0];
            }
            $quote = [];
            if(!empty($quotation)) {
                $quote['price_type'] = $quoteObj->getPriceType();
                $quote['from_country'] = $fromCountry;
                $quote['from_postcode'] = $fromPostcode;
                $quote['from_city'] = $fromCity;
                $quote['user_account_id'] = $userAccountId;
                $quote['to_country'] = $toCountry;
                $quote['to_postcode'] = $toPostcode;
                $quote['to_city'] = $toCity;
                $quote['calculate'] = $parcels;
                $quote['currency_id'] = $quoteObj->getCurrencyId();
                $quote['conversion_rate'] = $conversionRate;
                $quote['subTotalCharges'] = $quotation['SUBTOTAL'];
                $quote['carrier_id'] = $quoteObj->getCarrierId();
                $quote['service_id'] = $serviceId;
                $quote['discount'] = $quotation['DISCOUNT_AMOUNT'];
                $quote['discount_type'] = ($quotation['DISCOUNT_TYPE'] != "") ? $quotation['DISCOUNT_TYPE']: 'fixed';
                $quote['basic_charges'] = $quotation['BASIC_CHARGE'];
                $quote['vat_charges'] = $quotation['VAT_CHARGE'];
                $quote['extra_charges'] = $quotation['TOTAL_EXTRAS'];
                $quote['total_charges'] = $quotation['TOTAL'];
                $quote['description'] = $this->form_vars['description'];
                $quote['email'] = $quoteObj->getUserEmail();
                $quote['quote_data'] = serialize($this->form_vars);
                $quote['login_check'] = 1;
                $quote['action'] = 'save_quotation';

                $output['status'] = 'success';
                $output['quote'] = $quote;
            } else {
                $output['status'] = 'error';
                $output['message'] = 'No quotation found against these credentials. please contact to customer service for details';
            }
            echo json_encode($output);
            die;
        }

    }

    public function emailTemplate($accountId,$res) {
        $userFilter = new UserFilter();
        $userFilter->addFieldFilter(' user_account_id',$accountId);
        $userObj = $userFilter->getList();
        if(count($userObj)) {
            $userObj = $userObj[0];
        }
        $userAccountObj = new CustomerAccount($accountId);
        $logoImage = $userAccountObj->getLogo();
        $imageLogo = "";
        if (trim($logoImage) != '') {
            $imageLogo = BASE_URL.'images/userlogo/' . $logoImage;
            if(!file_exists($imageLogo)) {
                $imageLogo = BASE_URL.'images/inner-logo.png';
            }
        } else {
            $imageLogo = BASE_URL.'images/inner-logo.png';
        }
        $company = $userAccountObj->getCompany();
        $toEmail = $userAccountObj->getEmail();
        if($userAccountObj->getAlternativeEmail() != "") {
            $toEmail .= ",".$userAccountObj->getAlternativeEmail();
        }
        $consigmentObj = new Consignment($res['CONSIGNMENT_ID']);
        $mailHeaders = 'From: One World Express <smart@smarttrack.co>' . "\r\n";
        $mailHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
        $subject = "New Shipnment Created Successfully";
        $message = 'Your order is submitted with following details <br /><br />';
        $message .= '<div style="width:50%;float:left">';
        $message .= "<b>Sender Address</b><br />";
        $country = '';
        if(count($consigmentObj)) {
            $countryId = $consigmentObj->getSenderCountryId();
            $countryObj = new Country($countryId);
            $country = count($countryObj) ? $countryObj->getName() : '';
        }
        $message .= (count($consigmentObj) && $consigmentObj->getSenderAddressLine1() != "") ? $consigmentObj->getSenderAddressLine1().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getSenderAddressLine2() != "") ? $consigmentObj->getSenderAddressLine2().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getSenderAddressLine3() != "")  ? $consigmentObj->getSenderAddressLine3().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getSenderCity() != "")  ? $consigmentObj->getSenderCity().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getSenderState() != "") ? $consigmentObj->getSenderState().'<br />' : '';
        $message .= ($country != "") ? $country.'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getSenderPostcode() != "") ? $consigmentObj->getSenderPostcode().'<br />' : '';
        $message .= '</div>';
        $message .= '<div style="width:50%;float:left;">';
        $message .= "<b>Receiver Address</b><br />";
        $country = '';
        if(count($consigmentObj)) {
            $countryId = $consigmentObj->getCountryId();
            $countryObj = new Country($countryId);
            $country = count($countryObj) ? $countryObj->getName() : '';
        }
        $message .= (count($consigmentObj) && $consigmentObj->getAddressLine1() != "") ? $consigmentObj->getAddressLine1().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getAddressLine2() != "") ? $consigmentObj->getAddressLine2().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getAddressLine3() != "")  ? $consigmentObj->getAddressLine3().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getCity() != "")  ? $consigmentObj->getCity().'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getState() != "") ? $consigmentObj->getState().'<br />' : '';
        $message .= ($country != "") ? $country.'<br />' : '';
        $message .= (count($consigmentObj) && $consigmentObj->getPostcode() != "") ? $consigmentObj->getPostcode().'<br />' : '';
        $message .= '</div>';
        $message .= '<div style="width: 100%; clear: both; padding-top: 20px;">';
        $message .= '<b>ORDER REFERENCE: </b> '.$res['ORDER_REFERENCE'].' <br />';
        $message .= '<b>TRACKING NUMBER: </b> '.$res['AWB'].' <br />';
        $message .= '<b>LABEL LINK: </b> <a href="'.$res['INSTANT_LABEL'].'" target="_blank" >'.$res['INSTANT_LABEL'].' </a> <br />';
        $message .= '<br />You must print your label by clicking the above \'Label Link\' and attach it to your parcel before your collection or drop it off process. A4 Printer will be suitable for all label printing. Alternatively, you can choose one of the following options for your printing needs:<br /> 
                    4x6 label printer<br />
                    A4 printer (4x 4x6 labels)<br />
                    Address labels (A4 sheet)<br />';
        $message .= '</div>';
        $dt = [
                'logo' => $imageLogo,
                'name' => $userObj->getFirstName() . ' ' . $userObj->getLastName(),
                'message' => $message,
                'company' => $company
        ];
        $emailBody = file_get_contents ('quotation_new_shipment_email_template.php');// read in the template file from above
        foreach ($dt as $key => $value){
            $emailBody = str_replace ("[$key]", $value, $emailBody);
        }
        if($toEmail != "") {
            @mail($toEmail, $subject, $emailBody, $mailHeaders);
        }
    }

    public function paymentEmailTemplate($accountId,$quoteObj,$quotationPaymentHistoryObj) {
        $userFilter = new UserFilter();
        $userFilter->addFieldFilter(' user_account_id',$accountId);
        $userObj = $userFilter->getList();
        if(count($userObj)) {
            $userObj = $userObj[0];
        }
        $userAccountObj = new CustomerAccount($accountId);
        $logoImage = $userAccountObj->getLogo();
        $imageLogo = "";
        if (trim($logoImage) != '') {
            $imageLogo = BASE_URL.'images/userlogo/' . $logoImage;
            if(!file_exists($imageLogo)) {
                $imageLogo = BASE_URL.'images/inner-logo.png';
            }
        } else {
            $imageLogo = BASE_URL.'images/inner-logo.png';
        }
        $company = $userAccountObj->getCompany();
        $toEmail = $userAccountObj->getEmail();
        if($userAccountObj->getAlternativeEmail() != "") {
            $toEmail .= ",".$userAccountObj->getAlternativeEmail();
        }
        $mailHeaders = 'From: One World Express <smart@smarttrack.co>' . "\r\n";
        $mailHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
        $subject = "Payment Received Successfully";
        $message = 'We have received your payment in the amount of '.$quoteObj->getTotalCharge().' GBP, <br />';
        $message .= 'Please keep this email as a receipt of your records. <br /><br />';
        $message .= 'Total Amount : '.$quoteObj->getTotalCharge().' <br />';
        $message .= 'Transaction Number: '.$quotationPaymentHistoryObj->getTxnId().' <br /> <br />';
        $message .= 'Please note that, if you do not receive the email for labels for the above quoation due to any reason, Our Customer Service or Operation team will contact you shortly. <br /><br />';
        $message .= 'Thanks you for your business!';

        $dt = [
            'logo' => $imageLogo,
            'name' => $userObj->getFirstName() . ' ' . $userObj->getLastName(),
            'message' => $message,
            'company' => $company
        ];
        $emailBody = file_get_contents ('quotation_new_shipment_email_template.php');// read in the template file from above
        foreach ($dt as $key => $value){
            $emailBody = str_replace ("[$key]", $value, $emailBody);
        }
        if($toEmail != "") {
            @mail($toEmail, $subject, $emailBody, $mailHeaders);
        }
    }

    public function getPaypalApiContext($account_id) {
        $userAccountObj = new CustomerAccount($account_id);
        $output = [];
        if($userAccountObj->getPaypalClientId() != "" && $userAccountObj->getPaypalClientSecret() != "") {
            $this->apiContext = new PayPal\Rest\ApiContext(
                new PayPal\Auth\OAuthTokenCredential(
                    $userAccountObj->getPaypalClientId(),     // ClientID
                    $userAccountObj->getPaypalClientSecret()      // ClientSecret
                )
            );
            $mode = 'SANDBOX'; // 'LIVE', 'SANDBOX'
            $this->apiContext->setConfig(
                array(
                    'mode' => $mode
                )
            );
            $output['status'] = 'success';
        } else {
            $output['status'] = 'error';
        }
        return $output;
    }

    public function form_data() {
        $quoteId = $this->quotationId;
        $quoteObj = new Quotationdetails($quoteId);
        $serviceObj = new Services($quoteObj->getServiceId());
        $parcels = unserialize($quoteObj->getDimensions());
        $totalWeight = 0.00;
        foreach($parcels as $k => $parcel) {
            unset($parcels[$k]['weight_type']);
            unset($parcels[$k]['vol_weight']);
            $totalWeight += $parcel['weight'];
        }
        $id = count($this->quotationObj) ? $this->quotationObj->getId() : '';
        $dataArray = [
            'quote_id' => $quoteId,
            'quote_reference' => 'OWE'.str_pad($id, 6, '0', STR_PAD_LEFT),
            'amount' => $quoteObj->getTotalCharge(),
            'service' => $quoteObj->getServiceId(),
            'shipment_type' => $serviceObj->getServiceType(),
            'service_name' => $serviceObj->getName(),
            'item_value' => $this->form_vars['item_value'],
            'item_weight' => $totalWeight,
            'parcel' => $parcels,
            'pieces' => count($parcels),
            'description' => $this->form_vars['description'],
            'sender_company' => $this->form_vars['sender_company'],
            'sender_contact' => $this->form_vars['sender_contact'],
            'sender_address_line_1' => $this->form_vars['sender_address_line_1'],
            'sender_address_line_2' => $this->form_vars['sender_address_line_2'],
            'sender_city' => $quoteObj->getFromCity(),
            'sender_postcode' => $quoteObj->getFromPostcode(),
            'sender_country' => $quoteObj->getShippingFrom(),
            'receiver_company' => $this->form_vars['receiver_company'],
            'receiver_contact' => $this->form_vars['receiver_contact'],
            'receiver_address_line_1' => $this->form_vars['receiver_address_line_1'],
            'receiver_address_line_2' => $this->form_vars['receiver_address_line_2'],
            'receiver_city' => $quoteObj->getToCity(),
            'receiver_postcode' => $quoteObj->getToPostcode(),
            'receiver_country' => $quoteObj->getShippingTo(),
            'action' => 'barclay_create_payment'
        ];
        return $dataArray;
    }

    public function consigmentData($postData) {
        $defaultConsignmentData['shipment_type'] = '';
        $defaultConsignmentData['sender_country'] = '';
        $defaultConsignmentData['receiver_country'] = '';
        $defaultConsignmentData['service'] = '';
        $defaultConsignmentData['order_reference'] = 'OWEQ'.str_pad($postData['quote_id'],'5','0',0);
        //$defaultConsignmentData['shipmentUserId'] = '';
        $defaultConsignmentData['sender_company'] = '';
        $defaultConsignmentData['sender_contact'] = '';
        $defaultConsignmentData['sender_email'] = '';
        $defaultConsignmentData['sender_telephone'] = '';
        $defaultConsignmentData['sender_address_line_1'] = '';
        $defaultConsignmentData['sender_address_line_2'] = '';
        $defaultConsignmentData['sender_address_line_3'] = '';
        $defaultConsignmentData['sender_city'] = '';
        $defaultConsignmentData['sender_state'] = '';
        $defaultConsignmentData['sender_postcode'] = '';
        $defaultConsignmentData['packageLocation'] = '';
        $defaultConsignmentData['collection_date'] = '';
        $defaultConsignmentData['collection_start_time'] = '';
        $defaultConsignmentData['collection_end_time'] = '';
        $defaultConsignmentData['receiver_company'] = '';
        $defaultConsignmentData['receiver_contact'] = '';
        $defaultConsignmentData['receiver_email'] = '';
        $defaultConsignmentData['receiver_telephone'] = '';
        $defaultConsignmentData['receiver_address_line_1'] = '';
        $defaultConsignmentData['receiver_address_line_2'] = '';
        $defaultConsignmentData['receiver_address_line_3'] = '';
        $defaultConsignmentData['receiver_city'] = '';
        $defaultConsignmentData['receiver_state'] = '';
        $defaultConsignmentData['receiver_postcode'] = '';
        $defaultConsignmentData['parcel'] = '';
        $defaultConsignmentData['item_weight'] = '';
        $defaultConsignmentData['item_value'] = '';
        $defaultConsignmentData['reference'] = '';
        $defaultConsignmentData['item_currency'] = '';
        $defaultConsignmentData['item_type'] = '';
        $defaultConsignmentData['description'] = '';
        $defaultConsignmentData['notes'] = '';
        $defaultConsignmentData['save_invalid'] = '1';
        $consignmentData = array_merge($defaultConsignmentData,$postData);
        return $consignmentData;
    }

    public function add_consignment($form_vars) {
        $quoteId = $form_vars['quote_id'];
        $quoteObj = new Quotationdetails($quoteId);
        $accountId = $quoteObj->getAccountId();
        $userFilter = new UserFilter();
        $userFilter->addFieldFilter(' user_account_id',$accountId);
        $userObj = $userFilter->getList();
        $userId = 0;
        if(count($userObj)) {
            $userObj = $userObj[0];
            $userId = $userObj->getId();
        }
        $consignmentData = $this->consigmentData($form_vars);
        $output = Consignment::saveShipment($consignmentData, $userId, false, '', 'web');
        return $output;
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter()
    {
        ?>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/vendor/bootstrap/css/bootstrap.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css"
              href="../assets/quot_payment/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css"
              href="../assets/quot_payment/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/vendor/animate/animate.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/vendor/css-hamburgers/hamburgers.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/vendor/animsition/css/animsition.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/vendor/select2/select2.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/vendor/daterangepicker/daterangepicker.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/css/util.css">
        <link rel="stylesheet" type="text/css" href="../assets/quot_payment/css/main.css">

        <link rel="stylesheet" href="https://cdn.rawgit.com/tonystar/bootstrap-float-label/v3.0.1/dist/bootstrap-float-label.min.css"/>

        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <style>
            .input-group-prepend {
                padding: 6px 8px 0px 8px !important;
                background-color: darkgrey;
                border-top-left-radius: 50%;
                border-bottom-left-radius: 50%;
                color: #fff;
            }
            .input-group-append {
                padding: 5px 6px 0px 9px;
                background-color: darkgray;
                border-top-right-radius: 50%;
                border-bottom-right-radius: 50%;
                margin: 0px 0px 0px 8px;
                color: #fff;
            }
            .select2-selection {
                border-radius: 50px !important;
                height: 35px !important;
            }
            #show_error {
                padding: 5px 0px;
            }
            #show_error li {
                color: #ff0000;
                font-size: 12px;
                text-align: left;
                text-transform: initial;
            }
            .message_box .row {
                margin-top: 20px;
            }
             .message_box .col-md-12{
                 border: 1px solid #ED1C24;
                 box-shadow: 0px 0px 20px 10px rgba(199,199,199,1);
                 border-radius: 35px;
                 padding: 30px;
             }
            .message_box .logo-div{
                text-align: center;
            }
            #alert_msg {
                text-align: center;
                color: green;
                font-size: 20px;
                font-weight: bold;
            }
            #alert_msg_error {
                text-align: center;
                color: red;
                font-size: 20px;
                font-weight: bold;
            }
            .address_tbl {
                min-height: 235px;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <!--===============================================================================================-->
        <script src="../assets/quot_payment/vendor/jquery/jquery-3.2.1.min.js"></script>
        <!--===============================================================================================-->
        <script src="../assets/quot_payment/vendor/animsition/js/animsition.min.js"></script>
        <!--===============================================================================================-->
        <script src="../assets/quot_payment/vendor/bootstrap/js/popper.js"></script>
        <script src="../assets/quot_payment/vendor/bootstrap/js/bootstrap.min.js"></script>
        <!--===============================================================================================-->
        <script src="../assets/quot_payment/vendor/select2/select2.min.js"></script>
        <script>
            $(".selection-2").select2({
                minimumResultsForSearch: 20,
                dropdownParent: $('#dropDownSelect1')
            });
        </script>
        <!--===============================================================================================-->
        <script src="../assets/quot_payment/vendor/daterangepicker/moment.min.js"></script>
        <script src="../assets/quot_payment/vendor/daterangepicker/daterangepicker.js"></script>
        <!--===============================================================================================-->
        <script src="../assets/quot_payment/vendor/countdowntime/countdowntime.js"></script>
        <!--===============================================================================================-->
        <script src="../assets/quot_payment/js/main.js"></script>

        <script src="https://www.paypalobjects.com/api/checkout.js"></script>

        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $('.select2').select2();
                $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click', '#btn_quotation_edit', function () {
                    $('.edit_field').removeAttr('readonly');
                    $('.edit_field_select').removeAttr('disabled');
                    $('#payment_btn_box').hide();
                    $(this).hide();
                    $('#btn_quotation_save').show();
                });
                $(document).on('click', '#btn_quotation_save', function () {
                    var data = $('#quotation_form').serializeArray();
                    data.push({name: 'action', value: 'new_quotation_save'});
                    $.ajax({
                        url: "quotation_pay.php?key=<?php echo $_GET['key'] ?>",
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                            var status = response.status;
                            if (status == 'success') {
                                $.ajax({
                                    url: "quotation_add.php",
                                    data: response.quote,
                                    type: 'post',
                                    dataType: 'json',
                                    success: function (res) {
                                        var status = res.status;
                                        if (status == 'success') {
                                            if(res.id > 0) {
                                                window.location = res.link;
                                            }
                                        } else {
                                            swal("Sorry!", "Quotation not save please contact to admin", "error");
                                        }
                                    }
                                });
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                        }
                    });
                });
                if ($('#paypal_btn').length > 0) {
                    paypal.Button.render({
                        style: {
                            size: 'medium',
                            color: '',
                            shape: 'pill',
                            label: 'checkout',
                            tagline: false
                        },
                        env: 'sandbox', // Or 'production', 'sandbox'
                        // Set up the payment:
                        // 1. Add a payment callback
                        payment: function (data, actions) {
                            var unindexed_array = $('#quotation_form').serializeArray();
                            var indexed_array = {};
                            $.map(unindexed_array, function(n, i){
                                indexed_array[n['name']] = n['value'];
                            });
                            indexed_array['action'] = 'paypal_create_payment';
                            // 2. Make a request to your server
                            return actions.request.post("quotation_pay.php?key=<?php echo $_GET['key'] ?>",indexed_array).then(function (res) {
                                    // 3. Return res.id from the response
                                    if(res.status == "error" ) {
                                        $('#show_error').html(res.error);
                                    }else if(res.id != "") {
                                        return res.id;
                                    }
                                });
                        },
                        // Execute the payment:
                        // 1. Add an onAuthorize callback
                        onAuthorize: function (data, actions) {
                            var unindexed_array = $('#quotation_form').serializeArray();
                            var indexed_array = {};
                            $.map(unindexed_array, function(n, i){
                                indexed_array[n['name']] = n['value'];
                            });
                            indexed_array['payment_id'] = data.paymentID;
                            indexed_array['payer_id'] = data.payerID;
                            indexed_array['action'] = 'paypal_execute_payment';
                            // 2. Make a request to your server
                            return actions.request.post("quotation_pay.php?key=<?php echo $_GET['key'] ?>", indexed_array).then(function (res) {
                                // 3. Show the buyer a confirmation message.
                                if (res.STATUS == "SUCCESS") {
                                    swal("Good!", "Your have successful complete your transaction", "success");
                                    window.location = "<?php echo BASE_URL; ?>quotation_pay.php?skey="+res.key+"&message=success";
                                } else {
                                    swal("Sorry!", "Please refresh you page and try again if error persist please contact to info@oneworldexpress.com", "error");
                                }
                            });
                        },
                        // If an error prevents buyer checkout, define an error page using the onError callback:
                        onError: function (err) {
                            if(err.error) {
                                $('#show_error').html("<li>You have an error in your consignment. Please contact to info@oneworldexpress.com</li>");
                            }
                        }
                    }, '#paypal_btn');
                }
                $(document).on('click','#barclay_btn',function() {
                    var data = $('#quotation_form').serializeArray();
                    data.push({name: 'action', value: 'barclay_create_payment'});
                    $.ajax({
                        url: "quotation_pay.php?key=<?php echo $_GET['key'] ?>",
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                            var status = response.status;
                            if (status == 'success') {
                                $('#amount_barclay').val(response.amount);
                                $('#currency').val(response.currency);
                                $('#language').val(response.language);
                                $('#orderid').val(response.orderid);
                                $('#pspid').val(response.pspid);
                                $('#shasign').val(response.shasign);
                                $('#barclays_form').submit();
                            } else {
                                $('#show_error').html(response.error);
                            }
                        }
                    });
                });
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <?php if($_GET['message'] == 'success') { ?>
        <div class="container-fluid message_box">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="logo-div">
                            <img src="../assets/quot_payment/images/smart-track-log.png" />
                        </div>
                        <?php if(count($this->consignmentObj) && $this->consignmentObj->getAwb() != "") { ?>
                            <div id="alert_msg">
                                You have successfully complete you quotation payments.
                                <p class="mt-4 mb-4">
                                    You must print your label by clicking the below 'Label Link' and attach it to your parcel before your collection or drop it off process. A4 Printer will be suitable for all label printing. Alternatively, you can choose one of the following options for your printing needs: <br />
                                    4x6 label printer <br />
                                    A4 printer (4x 4x6 labels) <br />
                                    Address labels (A4 sheet) <br />
                                </p>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <table class="table table-bordered address_tbl">
                                        <tr>
                                            <th>Sender Address</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php
                                                $country = '';
                                                    if(count($this->consignmentObj)) {
                                                        $countryId = $this->consignmentObj->getSenderCountryId();
                                                        $countryObj = new Country($countryId);
                                                        $country = count($countryObj) ? $countryObj->getName() : '';
                                                    }
                                                ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getSenderAddressLine1() != "") ? $this->consignmentObj->getSenderAddressLine1().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getSenderAddressLine2() != "") ? $this->consignmentObj->getSenderAddressLine2().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getSenderAddressLine3() != "")  ? $this->consignmentObj->getSenderAddressLine3().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) &&  $this->consignmentObj->getSenderCity() != "")  ? $this->consignmentObj->getSenderCity().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) &&  $this->consignmentObj->getSenderState() != "") ? $this->consignmentObj->getSenderState().'<br />' : '' ?>
                                                <?php echo ($country != "") ? $country.'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getSenderPostcode() != "") ? $this->consignmentObj->getSenderPostcode() : '' ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-bordered address_tbl">
                                        <tr>
                                            <th>Receiver Address</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php
                                                $country = '';
                                                if(count($this->consignmentObj)) {
                                                    $countryId = $this->consignmentObj->getCountryId();
                                                    $countryObj = new Country($countryId);
                                                    $country = count($countryObj) ? $countryObj->getName() : '';
                                                }
                                                ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getAddressLine1() != "") ? $this->consignmentObj->getAddressLine1().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getAddressLine2() != "") ? $this->consignmentObj->getAddressLine2().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getAddressLine3() != "")  ? $this->consignmentObj->getAddressLine3().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) &&  $this->consignmentObj->getCity() != "")  ? $this->consignmentObj->getCity().'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) &&  $this->consignmentObj->getState() != "") ? $this->consignmentObj->getState().'<br />' : '' ?>
                                                <?php echo ($country != "") ? $country.'<br />' : '' ?>
                                                <?php echo (count($this->consignmentObj) && $this->consignmentObj->getPostcode() != "") ? $this->consignmentObj->getPostcode() : '' ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Order Reference</th>
                                        <td><?php echo count($this->consignmentObj) ? $this->consignmentObj->getHawb() : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tracking Number</th>
                                        <td><?php echo count($this->consignmentObj) ? $this->consignmentObj->getAwb() : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Label Link</th>
                                        <?php
                                            $link = "";
                                            if(count($this->consignmentObj) && $this->consignmentObj->getLabelFile() != "") {
                                                $link = SETTING_URL . "_assets/pdf/" . (count($this->consignmentObj) ? $this->consignmentObj->getLabelFile() : '#');
                                            }
                                        ?>
                                        <td><a href="<?php echo $link; ?>" target="_blank" ><?php echo $link; ?></a></td>
                                    </tr>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div id="alert_msg">
                                You have successfully complete you quotation payments. Our team will be contact you soon.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } else if($_GET['message'] == 'cancel') { ?>
        <div class="container-fluid message_box">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="logo-div">
                            <img src="../assets/quot_payment/images/smart-track-log.png" />
                        </div>
                        <div id="alert_msg_error">
                            You have cancel your payment.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else if($_GET['message'] == 'exception') { ?>
        <div class="container-fluid message_box">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="logo-div">
                            <img src="../assets/quot_payment/images/smart-track-log.png" />
                        </div>
                        <div id="alert_msg_error">
                            You payment is not successfully done please try again.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else if($_GET['message'] == 'declined') { ?>
        <div class="container-fluid message_box">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="logo-div">
                            <img src="../assets/quot_payment/images/smart-track-log.png" />
                        </div>
                        <div id="alert_msg_error">
                            You payment is declined please try again.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <p class="text-danger important-notic-text">
                        <small> *IMPORTANT – Editing the details may change the cost of this Shipment and a new Unique quote ref number will be created</small>
                    </p>
                    <form class="login100-form validate-form" method="post" name="quotation_form" action="" id="quotation_form">
                        <span class="logo-div">
                            <center>
                                <img src="../assets/quot_payment/images/smart-track-log.png" style="max-width: 200px"/> Proceed to the Request for Quotation (RFQ)
                            </center>
                        </span>
                        <div class="contianer">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="wrap-input100 validate-input m-b-20 input-group" data-validate="Enter Unique Quote Ref #">
                                        <span class="has-float-label">
                                            <input type="hidden" name="quote_id" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getId() : ""); ?>">
                                            <input id="quote_reference" class="input100 font-weight-bold" type="text" name="quote_reference" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? 'OWE'.str_pad($this->quotationObj->getId(), 6, '0', STR_PAD_LEFT) : ""); ?>" readonly="readonly">
                                            <label for="quote_reference">Enter Unique Quote Ref #</label>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="wrap-input100 validate-input m-b-20 input-group" data-validate="Amount Quoted">
                                        <div class="input-group-prepend">
                                            <?php
                                                $currencyId = count($this->quotationObj) ? $this->quotationObj->getCurrencyId() : '';
                                                $currencyObj = new currency($currencyId);
                                            ?>
                                            <span class="input-group-text"><?php echo $currencyObj->getLeftsymbol() ?></span>
                                        </div>
                                        <span class="has-float-label ">
                                            <input id="amount" class="input100 font-weight-bold" type="text" name="amount" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getTotalCharge() : ""); ?>" readonly="readonly">
                                            <label for="amount">Amount Quoted</label>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?php
                                        $serviceId = count($this->quotationObj) ? $this->quotationObj->getServiceId() : '';
                                        $serviceObj = new Services($serviceId);
                                    ?>
                                    <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Country">
                                        <span class="has-float-label">
                                            <input type="hidden" name="service" value="<?php echo $serviceId ?>" >
                                            <input type="hidden" name="shipment_type" value="<?php echo $serviceObj->getServiceType() ?>" >
                                            <input id="service_name" class="input100 font-weight-bold" type="text" name="service_name" placeholder="" value="<?php echo $serviceObj->getName() ?>" readonly="readonly">
                                            <label for="service_name">Service</label>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="wrap-input100 validate-input m-b-20 input-group" data-validate="Enter Value Declared">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo $currencyObj->getLeftsymbol() ?></span>
                                        </div>
                                        <span class="has-float-label">
                                            <input id="item_value" class="input100 font-weight-bold" type="text" name="item_value" placeholder="" value="" >
                                            <label for="item_value">Value Declared</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $pieceArr = count($this->quotationObj) ? $this->quotationObj->getDimensions() : '';
                                $pieces = unserialize($pieceArr);
                            ?>
                            <?php $totalWeight = "0.00"; ?>
                            <?php if (count($pieces) > 0) { ?>
                                <?php foreach ($pieces as $k =>  $piece) { ?>
                                    <?php $totalWeight = $totalWeight + $piece['weight']; ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="wrap-input100 validate-input m-b-20 input-group" data-validate="Enter Weight/Dims/Pieces">
                                                <span class="has-float-label">
                                                    <input class="input100 edit_field" type="text" name="parcel[<?php echo $k ?>][weight]" placeholder="" value="<?php echo isset($piece['weight']) ? $piece['weight'] : ''; ?>" readonly="readonly">
                                                    <label for="first">Weight</label>
                                                </span>
                                                <div class="input-group-append ">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="wrap-input100 validate-input m-b-20 input-group" data-validate="Enter Value Declared">
                                                <span class="has-float-label">
                                                    <input class="input100 edit_field" type="text" name="parcel[<?php echo $k ?>][length]" placeholder="" value="<?php echo $piece['length']; ?>" readonly="readonly">
                                                    <label for="first">Length</label>
                                                </span>
                                                <div class="input-group-append ">
                                                    <span class="input-group-text">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="wrap-input100 validate-input m-b-20 input-group" data-validate="Enter Weight/Dims/Pieces">
                                                <span class="has-float-label">
                                                    <input class="input100 edit_field" type="text" name="parcel[<?php echo $k ?>][width]" placeholder="" value="<?php echo $piece['width']; ?>" readonly="readonly">
                                                    <label for="first">Width</label>
                                                </span>
                                                <div class="input-group-append ">
                                                    <span class="input-group-text">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="wrap-input100 validate-input m-b-20 input-group" data-validate="Enter Value Declared">
                                                <span class="has-float-label">
                                                    <input class="input100 edit_field" type="text" name="parcel[<?php echo $k ?>][height]" placeholder="" value="<?php echo $piece['height']; ?>" readonly="readonly">
                                                    <label for="first">Height</label>
                                                </span>
                                                <div class="input-group-append ">
                                                    <span class="input-group-text">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <input type="hidden" name="item_weight" value="<?php echo $totalWeight; ?>" >
                            <div class="row">
                                <div class="col-md-3">
                                <div class="wrap-input100 validate-input m-b-20 " data-validate="Enter Weight/Dims/Pieces">
                                    <span class="has-float-label">
                                        <input class="input100 edit_field" type="text" name="pieces" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getPieces() : ""); ?>" readonly="readonly">
                                        <label for="first">Pieces</label>
                                    </span>
                                </div>
                            </div>
                                <div class="col-md-9">
                                <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Weight/Dims/Pieces">
                                    <span class="has-float-label">
                                        <input id="description" class="input100" type="text" name="description" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getRemark() : ""); ?>">
                                        <label for="description">Description</label>
                                    </span>
                                </div>
                            </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="login100-form-title mb-2">
                                        <img src="../assets/quot_payment/images/icon-billing.png"/> Billing address (Shipper)
                                    </span>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="sender_company" class="input100" type="text" name="sender_company" placeholder="" value="<?php echo (isset($this->fieldsData['sender_company']) ? $this->fieldsData['sender_company'] : '') ?>" >
                                                    <label for="sender_company">Company</label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="sender_contact" class="input100" type="text" name="sender_contact" placeholder="" value="<?php echo (isset($this->fieldsData['sender_contact']) ? $this->fieldsData['sender_contact'] : '') ?>" >
                                                    <label for="sender_contact">Name</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="sender_address_line_1" class="input100" type="text" name="sender_address_line_1" placeholder="" value="<?php echo (isset($this->fieldsData['sender_address_line_1']) ? $this->fieldsData['sender_address_line_1'] : '') ?>" >
                                                    <label for="sender_address_line_1">Address Line 1</label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="sender_address_line_2" class="input100" type="text" name="sender_address_line_2" placeholder="" value="<?php echo (isset($this->fieldsData['sender_address_line_2']) ? $this->fieldsData['sender_address_line_2'] : '') ?>" >
                                                    <label for="fisender_address_line_2rst">Address Line 2</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter City Name">
                                                <span class="has-float-label">
                                                <input id="sender_city" class="input100 edit_field" type="text" name="sender_city" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getFromCity() : ''); ?>" readonly="readonly" >
                                                    <label for="sender_city">City</label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20 " data-validate="Enter Zip/Postal">
                                                <span class="has-float-label">
                                                    <input id="sender_postcode" class="input100 edit_field" type="text" name="sender_postcode" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getFromPostcode() : ''); ?>" readonly="readonly" >
                                                    <label for="sender_postcode">ZIP/Postal Code</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Country">
                                        <span class="has-float-label">
                                            <?php
                                                $shipmentFrom = count($this->quotationObj) ? $this->quotationObj->getShippingFrom() : '';
                                                echo Ddl::generateCountryDDL('sender_country', $shipmentFrom, 'id', ' class="input100 edit_field_select" required="" disabled="disabled" ');
                                            ?>
                                            <label for="sender_country">Country</label>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <span class="login100-form-title mb-2">
                                        <img src="../assets/quot_payment/images/icon-shipping.png"/> Shipping address (Receiver)
                                    </span>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="receiver_company" class="input100" type="text" name="receiver_company" placeholder="" value="<?php echo (isset($this->fieldsData['receiver_company']) ? $this->fieldsData['receiver_company'] : '') ?>" >
                                                    <label for="receiver_company">Company</label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="receiver_contact" class="input100" type="text" name="receiver_contact" placeholder="" value="<?php echo (isset($this->fieldsData['receiver_contact']) ? $this->fieldsData['receiver_contact'] : '') ?>" >
                                                    <label for="receiver_contact">Name</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="receiver_address_line_1" class="input100" type="text" name="receiver_address_line_1" placeholder="" value="<?php echo (isset($this->fieldsData['receiver_address_line_1']) ? $this->fieldsData['receiver_address_line_1'] : '') ?>" >
                                                    <label for="receiver_address_line_1">Address Line 1</label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Street Address">
                                                <span class="has-float-label">
                                                    <input id="receiver_address_line_2" class="input100" type="text" name="receiver_address_line_2" placeholder="" value="<?php echo (isset($this->fieldsData['receiver_address_line_2']) ? $this->fieldsData['receiver_address_line_2'] : '') ?>" >
                                                    <label for="receiver_address_line_2">Address Line 2</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter City Name">
                                                <span class="has-float-label">
                                                    <input id="receiver_city" class="input100 edit_field" type="text" name="receiver_city" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getToCity() : ''); ?>" readonly="readonly" >
                                                    <label for="receiver_city">City</label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Zip/Postal">
                                                <span class="has-float-label">
                                                    <input id="receiver_postcode" class="input100 edit_field" type="text" name="receiver_postcode" placeholder="" value="<?php echo((count($this->quotationObj) > 0) ? $this->quotationObj->getToPostcode() : ''); ?>" readonly="readonly" >
                                                    <label for="receiver_postcode">ZIP/Postal Code</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wrap-input100 validate-input m-b-20" data-validate="Enter Country">
                                        <span class="has-float-label">
                                            <?php
                                                $shippmentTo = count($this->quotationObj) ? $this->quotationObj->getShippingTo() : '';
                                                echo Ddl::generateCountryDDL('receiver_country', $shippmentTo, 'id', ' class="input100 edit_field_select" required="" disabled="disabled" ');
                                            ?>
                                            <label for="receiver_country">Country</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="pb-2" style="font-size: 12px">
                                        Please confirm the above details are correct, if not please click On the
                                        <b>
                                            <a href="#" class="txt2"> “EDIT”</a>
                                        </b> button below to make any corrections* if yes please proceed and click on
                                        “confirm” Below to checkout.
                                    </p>
                                    <div class="container-login100-form-btn pb-1 plr-6" id="edit_btn_box">
                                        <button class="login100-form-btn" type="button" id="btn_quotation_edit">
                                            <!--<img src="../assets/quot_payment/images/icon-edit.png" style="margin-right: 10px"/>--> Edit
                                        </button>
                                        <button class="login100-form-btn" type="button" id="btn_quotation_save" style="display:none;">
                                            <!--<img src="../assets/quot_payment/images/icon-save.png" style="margin-right: 10px"/>--> Save
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6" id="payment_btn_box">
                                    <span class="login100-form-title mb-1 mt-1">
                                        <span class="login100-form-title mb-2">Confirm & Pay</span>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="javascript:;" class="txt2" id="paypal_btn">
    <!--                                                <img src="../assets/quot_payment/images/paypal-smart-payment-button-for-simple-membership.jpg" class="img-fluid mb-1">-->
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="javascript:;" class="txt2" id="barclay_btn">
                                                    <img src="../assets/quot_payment/images/btn-barclasy.png" class="img-fluid mb-1">
                                                </a>
                                            </div>
                                            <div class="error">
                                                <ul id="show_error"></ul>
                                            </div>
                                            <span class="login100-form-title">
                                                <img src="../assets/quot_payment/images/visa-master-logo.png" style="max-width: 40%" class="img-fluid">
                                            </span>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <form name="barclays_form" id="barclays_form" action="https://payments.epdq.co.uk/ncol/prod/orderstandard.asp" method="POST">
            <input type="hidden" name="AMOUNT" id="amount_barclay" value=""/>
            <input type="hidden" name="CURRENCY" id="currency" value=""/>
            <input type="hidden" name="LANGUAGE" id="language" value="">
            <input type="hidden" name="ORDERID" id="orderid" value=""/>
            <input type="hidden" name="PSPID" id="pspid" value=""/>
            <input type="hidden" name="SHASign" id="shasign" value="">
<!--            <input type="hidden" name="ACCEPTURL" value="--><?php //echo BASE_URL; ?><!--quotation_pay.php?key=--><?php //echo $_GET['key'] ?><!--">-->
<!--            <input type="hidden" name="DECLINEURL" value="--><?php //echo BASE_URL; ?><!--quotation_pay.php?key=--><?php //echo $_GET['key'] ?><!--">-->
<!--            <input type="hidden" name="EXCEPTIONURL" value="--><?php //echo BASE_URL; ?><!--quotation_pay.php?key=--><?php //echo $_GET['key'] ?><!--">-->
<!--            <input type="hidden" name="CANCELURL" value="--><?php //echo BASE_URL; ?><!--quotation_pay.php?key=--><?php //echo $_GET['key'] ?><!--">-->
        </form>
        <?php
        }
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {

    }

    public function renderHead()
    {
        ?>
        <style type="text/css">
            .input-group-prepend {
                padding: 5px 0px 0px 10px !important;
            }
        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_SIMPLE_TEMPLATE);
$page->show();
?>
