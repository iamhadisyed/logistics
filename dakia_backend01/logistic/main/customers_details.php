<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'currency.class',
    'currencyfilter.class',
    'invoicebankdetails.class',
    'invoicebankdetailsfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'usermarketplacesmapping.class',
    'usermarketplacesmappingfilter.class',
    'marketplaces.class',
    'marketplacesfilter.class',
    'tariffsaccountmapping.class',
    'tariffsaccountmappingfilter.class',
    'remoteareausermapping.class',
    'remoteareausermappingfilter.class',
    'documenttype.class',
    'documenttypefilter.class',
    'userdocument.class',
    'userdocumentfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'useraccountlog.class',
    'useraccountlogfilter.class',
    'api2cart.class',
]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage
{

    private $userServiceTypeNew;
    private $scan_doc;
    private $user_account;
    private $department_data;
//    private $selected_dep;
    private $selectedTariff = array();
//    private $selectedRouting = array();
    private $selectedPostcodeGroup = array();
    private $selectedShoppingPlatfrom = array();
    private $msg = '';
    private $counting_error;
    private $parents_Userid = '';
//    private $user_group = '';
    public $UserSalesRate = 0;
    private $agentList;
    private $serviceList;
    private $serviceCustomizeRulesList;
    private $sessionUser;
    private $commission_break_event_account_amount = 0;
    private $return_shipment_allow = 0;

    /*     * *
     * Controller logic
     */

    protected function init()
    {
        
        $sessionUser = $this->sessionUser = SessionManager::getUser();

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'customers.php' => 'Accounts List',
            'Add/Update Account'
        );
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);

        $logMessage = array();
//Get Sales Rate Total
//        $userAccountFilter = new UserAccountFilter();
//        $UserSalesRate = $userAccountFilter->getColumnDistinctList("SUM(`u`.`sales_rate`) AS sales_rate");
//        if (isset($UserSalesRate[0]) && $UserSalesRate[0]->getSalesRate() <= 100) {
//            $this->UserSalesRate = 100 - $UserSalesRate[0]->getSalesRate();
//        } else {
//        }
        $this->UserSalesRate = 100;
        CustomerAccount::updateBalance(util_get_num('id'));
        if (!empty($_POST['func']) && $_POST['func'] == 'chkaccount') {
            $accountStatus = $_POST['accountStatus'];
            $userAccountId = $_POST['id'];
            $paymentArr = [];
            if ($userAccountId > 0) {
                $userAccount = new CustomerAccount($userAccountId);
                if ($accountStatus == 'postpaid' && $userAccount->getIsPrepaid() == 1) {
                    $return = checkBalance($userAccountId, '', 1);
                    //$paymentArr = [];
                    if (!empty($return)) {
                        $paymentArr['payment'] = $return[0];
                        $paymentArr['payments'] = str_replace("-", "", $return[0]);
                        $paymentArr['postpaid'] = 'postpaid';
                    }
                } else if ($accountStatus == 'prepaid') {
                    $return = InvoiceFilter::clearAllInvoices($userAccountId);
                    //$paymentArr = [];
                    if (!empty($return)) {
                        $paymentArr['postpaid'] = 'postpaid';
                        $negative = '';
                        if ($return[0]->getId() > 0) {
                            $paymentArr['payment'] = $return[0]->getId();
                        }
                    }
                }
            }
            echo json_encode($paymentArr);
            exit;
        }
        /* Company Details */
//        Handle file upload for users documents user_id
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_user_doc') {
            $html = "";
            $userAccountId = $this->form_vars["user_account_id"];
            $documentId = $this->form_vars["file_type"];
            $fileTypeText = $this->form_vars["file_type_text"];
            $addedBy = $sessionUser->getId();
            $path = "../_assets/user_documents/" . $userAccountId . "/";
            if (!file_exists($path))
                @mkdir($path, 0775);
            if (isset($_FILES["user_doc_file"]) && trim($_FILES["user_doc_file"]["name"]) != '') {
                $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xls", "xlsx");
                $temp = explode(".", $_FILES["user_doc_file"]["name"]);
                $extension = end($temp);
                if ((
                        ($_FILES["user_doc_file"]["type"] == "image/gif") || 
                        ($_FILES["user_doc_file"]["type"] == "image/jpeg") || 
                        ($_FILES["user_doc_file"]["type"] == "image/jpg") || 
                        ($_FILES["user_doc_file"]["type"] == "image/pjpeg") || 
                        ($_FILES["user_doc_file"]["type"] == "image/x-png") || 
                        ($_FILES["user_doc_file"]["type"] == "image/png") || 
                        ($_FILES["user_doc_file"]["type"] == "application/pdf")|| 
                        ($_FILES["user_doc_file"]["type"] == "application/doc")|| 
                        ($_FILES["user_doc_file"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document")|| 
                        ($_FILES["user_doc_file"]["type"] == "application/xls")||
                        ($_FILES["user_doc_file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
                        ) && in_array($extension, $allowedExts)) {
                    if ($_FILES["user_doc_file"]["error"] > 0) {
                        $return_msg = "Return Code: " . $_FILES["user_doc_file"]["error"] . "<br>";
                    } else {
                        $uploadUserDoc = str_replace(' ', '_', time() . $_FILES["user_doc_file"]["name"]);
                        move_uploaded_file($_FILES["user_doc_file"]["tmp_name"], $path . $uploadUserDoc);
                        $fileFullPath = $path . $uploadUserDoc;
                        if ($extension == "pdf") {
                            $fileFullPath = "../images/pdf.png";
                        }
                        if ($extension == "xls"||$extension == "xlsx") {
                            $fileFullPath = "../images/xls.png";
                        }
                        if ($extension == "doc"||$extension == "docx") {
                            $fileFullPath = "../images/doc.png";
                        }
                        //Save User document Data
                        $userDocument = new userDocument();
                        $userDocument->setUserAccountId($userAccountId);
                        $userDocument->setDocumentId($documentId);
                        $userDocument->setDocumentName($uploadUserDoc);
                        $userDocument->setAddedBy($addedBy);
                        $userDocument->setAddedDate(time());
                        $userDocument->save();
                        $html .= '<div class="col-md-3" id="usr_doc_' . $userDocument->getId() . '">';
                        $html .= '<div class="thumbnail">';
                        $html .= '<img src="' . $fileFullPath . '" alt="100%x200" style="max-width: 100%; max-height: 200px; display: block;" data-src="' . $path . $uploadUserDoc . '">';
                        $html .= '<div class="caption">';
                        $html .= '<h3>' . $fileTypeText . '</h3>';
                        $html .= '<a target="_blank" href="' . $path . $uploadUserDoc . '" class="btn blue"> View </a>&nbsp&nbsp';
                        $html .= '<a href="javascript:;" class="btn red remove_doc" data-doc_id="' . $userDocument->getId() . '"> Remove </a>';
                        $html .= '</p>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        echo $html;
                    }
                } else {
                    echo $return_msg = "0";
                }
            }
            die;
        }
        /* End Company Details */
//        Handle remove User Document
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'remove_user_doc') {
            $docId = $this->form_vars['doc_id'];
            $userAccountId = $this->form_vars['user_account_id'];
            if ($userAccountId > 0) {
                $userDocument = new userDocument($docId);
                @unlink("../_assets/user_documents/" . $userAccountId . "/" . $userDocument->getDocumentName());
                $userDocument->deleteById($docId);
            }
            echo "1";
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'users_weight_limit') {
            $fromWeight = $this->form_vars['from_weight'];
            $toWeight = $this->form_vars['to_weight'];
            $serviceId = $this->form_vars['service_id'];
            $userAccountId = $this->form_vars['user_id'];
            UserServicesRouting::runQuery("UPDATE user_services_routing SET  from_weight = '" . DbAccess3::escape($fromWeight) . "' , to_weight = '" . DbAccess3::escape($toWeight) . "' WHERE user_account_id = '" . DbAccess3::escape($userAccountId) . "' AND service_id = '" . DbAccess3::escape($serviceId) . "' ");
            echo "1";
            die;
        }
// is this form being posted back?

        if (isset($this->form_vars["form_action"])) {
// take appropriate action

            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - save new address detailsvalidate new address details - if OK, save and return to booking list

                case "save":
                    $sessionUser = $this->sessionUser;
                    $sessionUserAccount = new CustomerAccount($sessionUser->getUserAccountId());
                    // address valid? set VALID and return to booking list
                    $error_array = array();
                    $oldLogo = '';
                    // save new address details
                    intval($this->form_vars["id"]);
                    $userAccount = new CustomerAccount(intval($this->form_vars["id"]));
                    $userLogSystem = new CustomerAccount(intval($this->form_vars["id"]));
                    $userLogSystem->getTariffValues();

//                $userLogSystem->getProductNames();
                    /* Accounts Tabs */
                    $default_weight = $userAccount->getDefaultWeight() == '' ? 0.00 : $userAccount->getDefaultWeight();
                    $default_weight = (isset($this->form_vars["weight"]) && !empty($this->form_vars["weight"])) ? $this->form_vars["weight"] : $default_weight;
                    if (Permissions::checkFilePermission('customer_detail_account')) {
                        $userAccountNumber = trim($this->form_vars["account_number"]);
                        $userAccount->setUserAccount($userAccountNumber);
                        if ((int)$userAccount->getId() <= 0) {
                            $accountCheck = new UserAccountFilter();
                            $accountCheck->addFilter("    user_account = '" . $userAccountNumber . "'");
                            $accountCheck->addFilter("    id != '" . $this->form_vars["id"] . "'");
                            $accountCheckList = $accountCheck->getColumnList("user_account, id");
                            if (count($accountCheckList) > 0)
                                $error_array[] = formatMessages(ERROR_DUPLICATE_ACCOUNT);
                        }

                        if (strpos($userAccountNumber, ' ') !== false) {
                            $error_array[] = formatMessages(ERROR_ACCOUNT_SPACES);
                        }

                        if ($this->form_vars["company"] == '' || $this->form_vars["contact"] == '' || $this->form_vars["telephone"] == '' || $this->form_vars["country_id"] == '' || $this->form_vars["email"] == '' || $this->form_vars["return_address"] == '')
                            $error_array[] = formatMessages(ERROR_REQUIRED_FILEDS_EMPTY);

                        $userAccount->setParentid($this->form_vars["parentAccountId"]);
                        $chkActive = 0;
                        if (isset($this->form_vars["chkActive"]))
                            $chkActive = 1;
                        $userAccount->setActive($chkActive);

                        $returnShipmentAllow = 0;
                        if (isset($this->form_vars["return_shipment_allow"]))
                            $returnShipmentAllow = 1;
                        $userAccount->setReturnShipmentAllow($returnShipmentAllow);

                        $userAccount->setCompany($this->form_vars["company"]);
                        $userAccount->setFullName($this->form_vars["contact"]);
                        $userAccount->setTelephone($this->form_vars["telephone"]);
                        $userAccount->setCountryId($this->form_vars["country_id"]);
                        $userAccount->setEmail($this->form_vars["email"]);
                        $userAccount->setAlternativeEmail($this->form_vars["alter_email"]);
                        $userAccount->setWebsiteLink($this->form_vars["website_link"]);
                        $userAccount->setReturnAddress($this->form_vars["return_address"]);
                        if ($userAccount->getDateCreated() <= '0' || trim($userAccount->getDateCreated()) == '' || trim($userAccount->getDateCreated()) == '1970-01-01 00:00:00')
                            $userAccount->setDateCreated(time());

                        $userAccount->setDefaultWeight($default_weight);
                        $userAccount->setDefaultNotes($this->form_vars["notes"]);
                        $userAccount->setDefaultDescription($this->form_vars["description"]);
                        $userAccount->setTrackingOrderPrefix($this->form_vars["tracking_order_prefix"]);
                        if ($sessionUser->getUserType() == User::USER_TYPE_ADMIN && isset($this->form_vars["theme"])) {
                            $userAccount->setThemeId($this->form_vars["theme"]);
                        } else {
                            $userAccount->setThemeId($sessionUserAccount->getThemeId());
                        }
                        $uploadLogoName = '';
                        if (isset($_FILES["your_logo"]) && trim($_FILES["your_logo"]["name"]) != '') {
                            $newPath = "../images/userlogo/";
                            if (!file_exists($newPath))
                                @mkdir($newPath, 0775);
                            $newPathThumb = "../images/userlogo/thumbnail/";
                            if (!file_exists($newPathThumb))
                                @mkdir($newPathThumb, 0775);

                            $allowedExts = array("gif", "jpeg", "jpg", "png");
                            $temp = explode(".", $_FILES["your_logo"]["name"]);
                            $extension = end($temp);
                            if ((($_FILES["your_logo"]["type"] == "image/gif") || ($_FILES["your_logo"]["type"] == "image/jpeg") || ($_FILES["your_logo"]["type"] == "image/jpg") || ($_FILES["your_logo"]["type"] == "image/pjpeg") || ($_FILES["your_logo"]["type"] == "image/x-png") || ($_FILES["your_logo"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                                if ($_FILES["your_logo"]["error"] > 0) {
                                    $error_array[] = "Return Code: " . $_FILES["your_logo"]["error"] . "<br>";
                                } else {
                                    $uploadLogoName = str_replace(' ', '_', time() . $_FILES["your_logo"]["name"]);
                                    move_uploaded_file($_FILES["your_logo"]["tmp_name"], "../images/userlogo/" . $uploadLogoName);

                                    $thumb = new easyphpthumbnail;
                                    $thumb->Thumblocation = '../images/userlogo/thumbnail/';
                                    $thumb->Thumbprefix = 'owe_';
                                    $thumb->Thumbsaveas = $extension;
                                    $thumb->Thumbfilename = $uploadLogoName;
                                    $thumb->Clipcorner = array(2, 15, 0, 0, 1, 1, 0);

                                    $thumb->Thumbsize = 16;
                                    $thumb->Thumbprefix = 'owe_16_';
                                    $thumb->Createthumb("../images/userlogo/" . $uploadLogoName, 'file');

                                    $thumb->Thumbsize = 100;
                                    $thumb->Thumbprefix = 'owe_100_';
                                    $thumb->Createthumb("../images/userlogo/" . $uploadLogoName, 'file');
                                }
                            } else {
                                $error_array[] = formatMessages(ERROR_INVALID_FILE);
                            }
                        }
                    }
                    /* End Accounts Tabs */
                    /* Company Details */
                    if (Permissions::checkFilePermission('customer_detail_company') && $this->form_vars["id"] > 0) {
                        $userAccount->setBankAccountTitle($this->form_vars["bank_account_title"]);
                        $userAccount->setBankSortcode($this->form_vars["bank_sortcode"]);
                        $userAccount->setBankAccountNumber($this->form_vars["bank_account_number"]);
                        $userAccount->setBankBranchAddress($this->form_vars["bank_branch_address"]);
                        $userAccount->setTradeNameI($this->form_vars["trade_name_i"]);
                        $userAccount->setTradeAddressI($this->form_vars["trade_address_i"]);
                        $userAccount->setTradeNameIi($this->form_vars["trade_name_ii"]);
                        $userAccount->setTradeAddressIi($this->form_vars["trade_address_ii"]);
                        $userAccount->setTradeEmailI($this->form_vars["trade_email_i"]);
                        $userAccount->setTradePhoneI($this->form_vars["trade_phone_i"]);
                        $userAccount->setTradeEmailIi($this->form_vars["trade_email_ii"]);
                        $userAccount->setTradePhoneIi($this->form_vars["trade_phone_ii"]);
                        $userAccount->setRegNumber($this->form_vars["reg_number"]);
                        $userAccount->setRegAddress($this->form_vars["reg_address"]);
                        $userAccount->setRegPostcode($this->form_vars["reg_postcode"]);
                        $userAccount->setRegCountry($this->form_vars["reg_country"]);
                        $userAccount->setUserSignature($this->form_vars["email_signature"]);
                        /* End Company Details */
                        /* Billing Detail */
                    }

                    $accountDisplayInvoices = $userAccount->getInvoiceBankDetailsId() == '' ? "0" : $userAccount->getInvoiceBankDetailsId();
                    $userAccount->setInvoiceBankDetailsId($accountDisplayInvoices);
                    $chkVatChargable = $userAccount->getVatChargable() == 0 ? 0 : $userAccount->getVatChargable();
                    $userAccount->setVatChargable($chkVatChargable);
                    $vatNumber = $userAccount->getVatNumber() == '' ? '' : $userAccount->getVatNumber();
                    $userAccount->setVatNumber($vatNumber);
                    $queryTerm = $userAccount->getQueryTerm() == '' ? '' : $userAccount->getQueryTerm();
                    $userAccount->setQueryTerm($queryTerm);
                    $paymentTerm = $userAccount->getPaymentTerm() == '' ? '' : $userAccount->getPaymentTerm();
                    $userAccount->setPaymentTerm($paymentTerm);
                    $vatValue = $userAccount->getVatValue() == '' ? 0.00 : $userAccount->getVatValue();
                    $userAccount->setVatValue($vatValue);
                    $chkAccountType = $userAccount->getIsPrepaid() == 0 ? 0 : $userAccount->getIsPrepaid();
                    $userAccount->setIsPrepaid($chkAccountType);
                    $ftpShipmentUpload = $userAccount->getFtpShipmentUpload() == 0 ? 0 : $userAccount->getFtpShipmentUpload();
                    $userAccount->setFtpShipmentUpload($ftpShipmentUpload);
                    /*$credit_check = $userAccount->getCreditCheck() == 0 ? 0 : $userAccount->getCreditCheck();
                    $userAccount->setCreditCheck($credit_check);
                    $tariff_agreed = $userAccount->getTariffAgreed() == 0 ? 0 : $userAccount->getTariffAgreed();
                    $userAccount->setTariffAgreed($tariff_agreed);
                    $is_fulecharges_include = $userAccount->getIsFuelchargesInclude() == 0 ? 0 : $userAccount->getIsFuelchargesInclude();
                    $userAccount->setIsFuelchargesInclude($is_fulecharges_include);
                    $own_tariff = $userAccount->getOwnTariff() == 0 ? 0 : $userAccount->getOwnTariff();
                    $userAccount->setOwnTariff($own_tariff);*/
                    $fuelCharges = $userAccount->getFuelCharges() == '' ? 0.00 : $userAccount->getFuelCharges();
                    $userAccount->setFuelCharges($fuelCharges);
                    $labelCharges = $userAccount->getLabelPrice() == '' ? 0.00 : $userAccount->getLabelPrice();
                    $userAccount->setLabelPrice($labelCharges);
                    $discountCharges = $userAccount->getDiscount() == '' ? 0.00 : $userAccount->getDiscount();
                    $userAccount->setDiscount($discountCharges);
                    $allow_over_size = $userAccount->getAllowOversize() == 0 ? 0 : $userAccount->getAllowOversize();
                    $userAccount->setAllowOversize($allow_over_size);
                    $send_return_email = $userAccount->getAllowReturnEmail() == 0 ? 0 : $userAccount->getAllowReturnEmail();
                    $userAccount->setAllowReturnEmail($send_return_email);

                    $credit_limit = $userAccount->getCreditLimit() == '' ? 0.00 : $userAccount->getCreditLimit();
                    $userAccount->setCreditLimit($credit_limit);
                    
                    $balance_alert_percentage = $userAccount->getBalanceAlertPercentage() == '' ? 0 : $userAccount->getBalanceAlertPercentage();
                    $userAccount->setBalanceAlertPercentage($balance_alert_percentage);
                    $invoicePeriod = $userAccount->getInvoicePeriod() == '' ? 'daily' : $userAccount->getInvoicePeriod();
                    $userAccount->setInvoicePeriod($invoicePeriod);

                    if (Permissions::checkFilePermission('customer_detail_billing') && $this->form_vars["id"] > 0) {
                        $userAccount->setBillingCurrency($this->form_vars["billing_currency"]);
                        $invoicePeriod = "daily";
                        if (isset($this->form_vars["invoice_period"]))
                            $invoicePeriod = $this->form_vars["invoice_period"];
                        $userAccount->setInvoicePeriod($invoicePeriod);

                        if (!empty($this->form_vars['invoice_bank_details_id']))
                            $accountDisplayInvoices = $this->form_vars["invoice_bank_details_id"];
                        $userAccount->setInvoiceBankDetailsId($accountDisplayInvoices);
                        $userAccount->setBillingAddress($this->form_vars["billing_address"]);
                        $userAccount->setBillingEmail($this->form_vars["billing_email"]);
                        $userAccount->setBillingContact($this->form_vars["billing_contact"]);

                        if (isset($this->form_vars["vat_number"]) && !empty($this->form_vars["vat_number"])) {
                            $vatNumber = $this->form_vars["vat_number"];
                            $userAccount->setVatNumber($vatNumber);
                        }

                        if (isset($this->form_vars["query_term"]) && !empty($this->form_vars["query_term"])) {
                            $queryTerm = $this->form_vars["query_term"];
                            $userAccount->setQueryTerm($queryTerm);
                        }

                        if (isset($this->form_vars["payment_term"]) && !empty($this->form_vars["payment_term"])) {
                            $paymentTerm = $this->form_vars["payment_term"];
                            $userAccount->setPaymentTerm($paymentTerm);
                        }

                        if (isset($this->form_vars["chkVatChargable"])) {
                            $chkVatChargable = 1;
                        } else {
                            $chkVatChargable = 0;
                        }
                        $userAccount->setVatChargable($chkVatChargable);

                        if (isset($this->form_vars["vat_value"]) && !empty($this->form_vars["vat_value"]))
                            $vatValue = $this->form_vars["vat_value"];

                        $userAccount->setVatValue($vatValue);
                        $credit_limit = trim($this->form_vars["credit_limit"]) == '' ? 0 : $this->form_vars["credit_limit"];
                        $balance_alert_percentage = trim($this->form_vars["balance_alert_percentage"]) == '' ? 0 : $this->form_vars["balance_alert_percentage"];

                        $parent_Id_account = !empty($this->form_vars["id"]) ? $this->form_vars["id"] : $this->form_vars["parentAccountId"];
                        $chkAccountTypeNew = 0;
                        if ($this->form_vars["chkAccountType"] == "on") {
                            $chkAccountTypeNew = 1;
                        }
                        if ($chkAccountTypeNew != $chkAccountType) {
                            if (isset($this->form_vars["chkAccountType"])) {
                                $return = InvoiceFilter::clearAllInvoices(intval($parent_Id_account));
                                if (!empty($return)) {
                                    if ($return[0]->getId() > 0) {
                                        $error_array[] = formatMessages(ERROR_CLEAR_INVOICE, false);
                                        $chkAccountType = 0;

                                    } else {
                                        $chkAccountType = 1;
                                        $credit_limit = 0.00;
                                        $balance_alert_percentage = 0;

                                    }
                                } else {
                                    $chkAccountType = 1;
                                    $credit_limit = 0.00;
                                    $balance_alert_percentage = 0;
                                }
                            } else {
                                $return = checkBalance(intval($parent_Id_account), '', 1);
                                if (!empty($return)) {
                                    if ($return[0] < 0) {
                                        $error_array[] = formatMessages(ERROR_PAY_BALANCE, false);
                                        $chkAccountType = 1;
                                    } else {
                                        $chkAccountType = 0;
                                    }
                                } else {
                                    $chkAccountType = 0;
                                }
                            }
                        }
                        $ftpShipmentUpload = 0;
                        if ($this->form_vars["ftp_shipment_upload"] == "on") {
                            $ftpShipmentUpload = 1;
                        }
                        $userAccount->setIsPrepaid($chkAccountType); //is_prepaid                        
                        $userAccount->setFtpShipmentUpload($ftpShipmentUpload); //is_prepaid
                        $userAccount->setCreditLimit($credit_limit);
                        $userAccount->setBalanceAlertPercentage($balance_alert_percentage);

                       /* if (isset($this->form_vars["credit_check"])) {
                            $credit_check = 1;
                        } else {
                            $credit_check = 0;
                        }
                        $userAccount->setCreditCheck($credit_check);

                        if (isset($this->form_vars["tariff_agreed"])) {
                            $tariff_agreed = 1;
                        } else {
                            $tariff_agreed = 0;
                        }
                        $userAccount->setTariffAgreed($tariff_agreed);

                        if (isset($this->form_vars["is_fulecharges_include"])) {
                            $is_fulecharges_include = 1;
                        } else {
                            $is_fulecharges_include = 0;
                        }
                        $userAccount->setIsFuelchargesInclude($is_fulecharges_include);

                        if (isset($this->form_vars["own_tariff"])) {
                            $own_tariff = 1;
                        } else {
                            $own_tariff = 0;
                        }
                        $userAccount->setOwnTariff($own_tariff);
                        */
                        if (!empty($this->form_vars["fuel_charges"]))
                            $fuelCharges = $this->form_vars["fuel_charges"];
                        $userAccount->setFuelCharges($fuelCharges);

                        if (!empty($this->form_vars["label_price"]))
                            $labelCharges = $this->form_vars["label_price"];
                        $userAccount->setLabelPrice($labelCharges);


                        if (!empty($this->form_vars["discount"]))
                            $discountCharges = $this->form_vars["discount"];
                        $userAccount->setDiscount($discountCharges);

                        if (isset($this->form_vars["allow_over_size"])) {
                            $allow_over_size = 1;
                        } else {
                            $allow_over_size = 0;
                        }
                        $userAccount->setAllowOversize($allow_over_size);

                        if (isset($this->form_vars["send_return_email"])) {
                            $send_return_email = 1;
                        } else {
                            $send_return_email = 0;
                        }
                        $userAccount->setAllowReturnEmail($send_return_email);


                        if (!empty($this->form_vars["paypal_merchant_account_email"]))
                            $paypalEmail = $this->form_vars["paypal_merchant_account_email"];
                        $userAccount->setPaypalEmail($paypalEmail);

                        if (!empty($this->form_vars["paypal_merchant_account_email_currency"]))
                            $paypalCurrency = $this->form_vars["paypal_merchant_account_email_currency"];
                        $userAccount->setPaypalCurrency($paypalCurrency);


                        /* End Billing Detail */
                    }
                    if (Permissions::checkFilePermission('customer_detail_service') && $this->form_vars["id"] > 0) {
                        /* Services */
//                        $userAccount->setIsProduct($this->form_vars["is_product"]);
                        /* End Services */
                        /* Sales Details */
                    }

                    $check_list_sales_pot = $userAccount->getCheckListSalesPot() == '' ? 0 : $userAccount->getCheckListSalesPot();
                    $salesPotTimePeriod = $userAccount->getSalesPotTimePeriod() == '' ? 0 : $userAccount->getSalesPotTimePeriod();
                    $salesPotPercentage = $userAccount->getSalesPotPercentage() == '' ? 0.00 : $userAccount->getSalesPotPercentage();

                    $userAccount->setSalesPotPercentage($salesPotPercentage);
                    $userAccount->setSalesPotTimePeriod($salesPotTimePeriod);
                    if (isset($this->form_vars["check_list_sales_pot"])){
                        $check_list_sales_pot = 1;
                    }else{
                        $check_list_sales_pot = 0;
                    }
                    $userAccount->setCheckListSalesPot($check_list_sales_pot);

                    $saleDate = $userAccount->getSaleDate() == '' ? '0000-00-00 00:00:00' : $userAccount->getSaleDate();
                    $userAccount->setSaleDate($saleDate);
                    if (Permissions::checkFilePermission('customer_detail_sales')) {
                        $userAccount->setSalesPerson($this->form_vars["sales_person"]);
                        if (isset($this->form_vars["sale_date"]) && !empty($this->form_vars["sale_date"]))
                            $saleDate = date('Y-m-d 00:00:00', strtotime($this->form_vars["sale_date"]));
                        $userAccount->setSaleDate($saleDate);


                        if (isset($this->form_vars["sales_pot_time_period"]))
                            $salesPotTimePeriod = $this->form_vars["sales_pot_time_period"];
                        $userAccount->setSalesPotTimePeriod($salesPotTimePeriod);

                        if (isset($this->form_vars["sales_pot_percentage"]))
                            $salesPotPercentage = $this->form_vars["sales_pot_percentage"];
                        $userAccount->setSalesPotPercentage($salesPotPercentage);

                        if ($this->form_vars["sales_value"] > $this->form_vars["max_sales_rate"]) {
                            $error_array[] = formatMessages(ERROR_SALE_RATE) . $this->form_vars["max_sales_rate"] . " %";
                        } else {
                            $userAccount->setSalesRate($this->form_vars["sales_value"]);
                        }
                        if(!empty($this->form_vars["commission_break_event_account_amount"])){
                            $commisionBreakEventAccountAmount = $this->form_vars["commission_break_event_account_amount"];
                            if($check_list_sales_pot != 1){
                                $commisionBreakEventAccountAmount = 0.00;
                            }
                            $userAccount->setCommissionBreakEventAccountAmount($commisionBreakEventAccountAmount);
                        }
                        /* End Sales Details */
                        /* Check List */
                    }

                    $check_list_account_form = $userAccount->getCheckListAccountForm() == '' ? 0 : $userAccount->getCheckListAccountForm();
                    $check_list_credit_check = $userAccount->getCheckListCreditCheck() == '' ? 0 : $userAccount->getCheckListCreditCheck();
                    $check_list_t_cs = $userAccount->getCheckListTCs() == '' ? 0 : $userAccount->getCheckListTCs();
                    $check_list_tariff_agreed = $userAccount->getCheckListTariffAgreed() == '' ? 0 : $userAccount->getCheckListTariffAgreed();

                    if (Permissions::checkFilePermission('customer_detail_checklist') && $this->form_vars["id"] > 0) {
                        //Check List Post

                        if (isset($this->form_vars["check_list_account_form"])) {
                            $check_list_account_form = 1;
                        } else {
                            $check_list_account_form = 0;
                        }
                        $userAccount->setCheckListAccountForm($check_list_account_form);

                        if (isset($this->form_vars["check_list_credit_check"])) {
                            $check_list_credit_check = 1;
                        } else {
                            $check_list_credit_check = 0;
                        }
                        $userAccount->setCheckListCreditCheck($check_list_credit_check);

                        if (isset($this->form_vars["check_list_t_cs"])) {
                            $check_list_t_cs = 1;
                        } else {
                            $check_list_t_cs = 0;
                        }
                        $userAccount->setCheckListTCs($check_list_t_cs);

                        if (isset($this->form_vars["check_list_tariff_agreed"])) {
                            $check_list_tariff_agreed = 1;
                        } else {
                            $check_list_tariff_agreed = 0;
                        }
                        $userAccount->setCheckListTariffAgreed($check_list_tariff_agreed);
                    }

                    /* End Check List */
//                    }/* End Permissions Check */
                    /* Unknown Set Default */
                    $userAccount->setUserServiceType("CHOICE");
                    $userAccount->setUserWarehouse("NON"); //which warehouse user belongs to?
                    $userAccount->setApiDate(time());


                    /* End Unknown Set Default */
                    $this->selectedTariff = $this->form_vars["tariff_name"];
                    $oldLogo = $userAccount->getLogo();
                    if (trim(@$uploadLogoName) != '') {
                        $userAccount->setLogo($uploadLogoName);
                        @unlink('../images/userlogo/' . $oldLogo);
                    }

                    if (empty($this->form_vars['parentAccountId']) && $sessionUser->getUserType() != User::USER_TYPE_ADMIN) {
                        if ($sessionUser->getUserType() == User::USER_TYPE_CORPORATE) {
                            $userAccount->setParentId($sessionUser->getUserAccountId());
                        } else {
                            $error_array[] = formatMessages(ERROR_PARENT_USER);
                        }
                    }

                    if (count($error_array) <= 0) {
                        if ($userAccount->isValid($error_array) || $this->form_vars["id"] > 0) {
                            $tarifMappingFilter = new TariffsAccountMappingFilter();
                            $tarifMappingFilter->addUserAccountFilter($this->form_vars["id"]);
                            $userTariffs = $tarifMappingFilter->getList();
                            $existUserTariff = [];
                            if (count($userTariffs) > 0) {
                                foreach ($userTariffs as $userTarifObj) {
                                    $existUserTariff[] = $userTarifObj->getTariffId();
                                }
                            }
                            $newTarrifs = [];
                            $oldTarrifs = [];
                            $tariff = $this->form_vars["tariff_name"];
                            if (isset($_POST["tariff_name"]) && (count($_POST["tariff_name"]) > 0)) {
                                // Add all tariffs for this User
                                foreach ($tariff as $t) {
                                    if (!in_array($t, $existUserTariff)) {
                                        $tariffObj = new Tariffs($t);
                                        $newTarrifs[] = $tariffObj->getName();
                                    }
                                }
                            }
                            if (!empty($existUserTariff)) {
                                foreach ($existUserTariff as $t) {
                                    if (!in_array($t, $tariff)) {
                                        $tariffObj = new Tariffs($t);
                                        $oldTarrifs[] = $tariffObj->getName();
                                    }
                                }
                            }
                            if (!empty($oldTarrifs)) {
                                $userAccount->logMoreDataOld['tariff_names'] = $oldTarrifs;
                            } else {
                                $userAccount->logMoreDataOld['tariff_names'] = [];
                            }
                            if (!empty($newTarrifs)) {
                                $userAccount->logMoreDataNew['tariff_names'] = $newTarrifs;
                            } else {
                                $userAccount->logMoreDataNew['tariff_names'] = [];
                            }
                            $userAccount->save();

//                          Add aduit log logic
//                          add agent data
//                          Get lastest inserted id
                            $latestId = $userAccount->getId();
                            //Add account code
                            UserAccountFilter::updateAccountCode($latestId);
                            $ConsignmentLog = new ConsignmentLog();
                            $ConsignmentLog->createlog("USER CREATED/ EIDTED " . $this->form_vars["account_number"], $userAccount->getId());
                            $insertIntoTariff = array();
                            /* Billing Detail */
                            $tarifMappingFilter = new TariffsAccountMappingFilter();
                            $tarifMappingFilter->addUserAccountFilter($latestId);
                            $userTariffs = $tarifMappingFilter->getList();
                            $existUserTariff = [];
                            if (count($userTariffs) > 0) {
                                foreach ($userTariffs as $userTarifObj) {
                                    $existUserTariff[] = $userTarifObj->getTariffId();
                                }
                            }
                            $insertIntoTariffString = '';
                            $tariff = $this->form_vars["tariff_name"];
                            // delete all tariffs for this User
                            if (count($existUserTariff) > 0) {
                                foreach ($existUserTariff as $tariff_id) {
                                    if (!in_array($tariff_id, $tariff)) {
                                        $TarDelObj = new TariffsAccountMappingFilter();
                                        $TarDelObj->addUserAccountFilter($latestId);
                                        $TarDelObj->addFieldFilter("    tam.tariff_id", $tariff_id);
                                        $TarDelObj->expunge();
                                    }
                                }
                            }
                            if (isset($_POST["tariff_name"]) && (count($_POST["tariff_name"]) > 0)) {
                                // Add all tariffs for this User
                                foreach ($tariff as $t) {
                                    if (!in_array($t, $existUserTariff)) {
                                        $tmapping = new TariffsAccountMapping();
                                        $tmapping->setTariffId($t);
                                        $tmapping->setUserAccountId($userAccount->getId());
                                        $tmapping->setAddedDate(time());
                                        $tmapping->setAddedBy(time());
                                        $tmapping->save();
                                        $insertIntoTariff[] = "('" . $t . "','XXXXX','" . date('Y-m-d h:i:s') . "')";
                                    }
                                }
                                if (count($insertIntoTariff) > 0) {
                                    $insertIntoTariffString = implode(',', $insertIntoTariff);
                                }
                            }
                            /* End Billing Detail */
                            $newAccountId = $userAccount->getId();
                            /* Third Party Details */

                            if (!empty($newAccountId) && $newAccountId > 0) {
                                UserMarketPlacesMapping::deleteUserMarketPlacesMappingList($newAccountId);
                            }

                            if (isset($this->form_vars['checkbox_auth'])) {
                                // Set User Shopping Platforms

                                foreach ($_POST['checkbox_auth'] as $platform_id) {
                                    $marketPlacesData = new MarketPlacesFilter();
                                    $marketPlacesData = $marketPlacesData->getMarketPlacesListWithAuthFields($platform_id);
                                    $plateformData = $this->form_vars['shopping_plateform'][$platform_id];
                                    $storeKey = [];
                                    if (!empty($marketPlacesData)) {
                                        if ($marketPlacesData[0]->getIsApi2cart() == 1) {
                                            $api2CartObj = new Api2cart();
                                            $apiData = $plateformData;
                                            $apiData['cart_id'] = $marketPlacesData[0]->getPluginKey();
                                            $storeKey = $api2CartObj->addCartInApi2Cart($apiData);
                                        }
                                    }
                                    if ($storeKey['status'] == true) {
                                        $storeKey = $storeKey['store_key'];
                                    } else {
                                        $storeKey = '';
                                    }
                                    $userPlatform = new UserMarketPlacesMapping();
                                    $userPlatform->setMarketPlacesId($platform_id);
                                    $userPlatform->setUserAccountId($userAccount->getId());
                                    $userPlatform->setAuthData(json_encode($plateformData));
                                    $userPlatform->setStoreKey($storeKey);
                                    $userPlatform->setActive(1);
                                    $userPlatform->save();
                                }
                            }
                            /* End Third Party Details */
                            
                            /* Start Add balace for pre-paid account */
                            $add_balance_id = $this->form_vars["add_balance_id"];
                            $pay_by = $this->form_vars['pay_by'];
                            $bank_ref_number = $this->form_vars['bank_ref_number'];
                            $cheque_number = $this->form_vars['cheque_number'];
                            if($add_balance_id > 0){
                                
                                if(!empty($this->form_vars["billingcurrency"])) {
                                    $toCurrencyFilter = new CurrencyFilter();
                                    $toCurrencyFilter->addFieldFilter("     rightsymbol", $this->form_vars["billingcurrency"]);
                                    $toCurrencyObj = $toCurrencyFilter->getList();
                                    if(count($toCurrencyObj)) {
                                        $userCurrencySymbol = $toCurrencyObj[0]->getRightsymbol();
                                        $userCurrencyId = $toCurrencyObj[0]->getId();
                                    }
                                }
                                
                                $paymenthistoryObj = new PaymentsHistory();
                                if($pay_by == "bank_transfer") {
                                    $paymenthistoryObj->setBillingId($bank_ref_number);
                                } else if($pay_by == "cheque") {
                                    $paymenthistoryObj->setBillingId($cheque_number);
                                }
                                $paymenthistoryObj->setAccountId($userAccount->getId());
                                $paymenthistoryObj->setAmount($add_balance_id);
                                $paymenthistoryObj->setAmountCurrencyId($userCurrencyId);
                                $paymenthistoryObj->setPaymentMethod($pay_by);
                                $paymenthistoryObj->setPaymentDetail("Recharge");
                                $paymenthistoryObj->setUserCurrencyId($userCurrencyId);
                                $paymenthistoryObj->setDebit('0.00');
                                $paymenthistoryObj->setCredit($add_balance_id);
                                $paymenthistoryObj->setPaymentStatus("completed");
                                $paymenthistoryObj->setIsCompleted("yes");
                                $paymenthistoryObj->setDateAdded(time());
                                $paymenthistoryObj->setAddedBy($this->sessionUser->getId());
                                $paymenthistoryObj->setDateUpdated(time());
                                $paymenthistoryObj->setUpdatedBy($this->sessionUser->getId());
                                $paymenthistoryObj->save();
                            }
                            /*End Add balace for pre-paid account  */
							/* update account balance */
								CustomerAccount::updateBalance($userAccount->getId());
							/******************************/
                            $newUserId = $this->sessionUser->getId();
                            $sucess = '';
                            $userAccountLog = new UserAccountLog();
                            $ipAddress = getClientIp();
                            if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
                                $logId = $this->form_vars["id"];
                            } else {
                                $logId = $newAccountId;
                            }
                            if ((int)(intval($this->form_vars["id"])) <= 0) {
                                $userAccountLog->createlog($newUserId, $ipAddress, $logId, 'USER', 'Created new account', "", "");
                                if (empty($error_array)) {
                                    $this->msg = formatMessages(SUCCESS_ACCOUNT_CREATED);
                                }
                                //$sucess = 1;
                                util_redirect("customers.php?save=success");
                            } else {
                                $userAccount->setTariffvalues(json_encode($this->form_vars["tariff_name"]));
                                $oldUserData = serialize($userLogSystem);
                                $newUserData = serialize($userAccount);
                                $userAccountLog->createlog($newUserId, $ipAddress, $logId, 'USER', $sessionUser->getUserName() . ' has updated ' . $userAccount->getUserAccount(), $oldUserData, $newUserData);
                                if (empty($error_array)) {
                                    $this->msg = formatMessages(SUCCESS_DATA_UPDATED);
                                }
                                util_redirect("customers.php?save=updated");
                                // $sucess = 2;
                            }

                            //$this->flashMsg->success($this->msg, "../main/customers_details.php?id=" . $newAccountId);
                        }
                    }
                    /*echo '<pre>';
                    print_r($error_array);
                    echo '</pre>';
                    die;*/
                    // add list of errors to error list
                    if (!empty($error_array)) {
                        foreach ($error_array as $error) {
                            $this->flashMsg->error($error);
                        }
                    }

                    break;
                case "delete":
                    $userAccount = new CustomerAccount(intval($this->form_vars["id"]));
                    $userAccount->delete();
                    $this->flashMsg->success("Accout deleted successfully.", "../main/customers_details.php?id=" . $newAccountId);
                    break;
                // CANCEL
                // - return to booking list
                case "cancel":
                default:
                    util_redirect("../main/customers.php");
                    break;
            }
        } // not post back - first time this form is shown
        else {
// get consignment id passed

            $id = util_get_num("id");
            $this->form_vars["id"] = $id;
            $this->form_vars["user_account_id"] = $id;
// get address values
            $userAccountFilter = new UserAccountFilter();
            $userAccountFilter->addIdFilter($id);
            if ($sessionUser->getUserType() == User::USER_TYPE_CORPORATE) {
                $userAccountFilter->addFieldFilter('parentid', $sessionUser->getUserAccountId());
            }
            $userList = $userAccountFilter->getList();
            if (count($userList) > 0) {
                foreach ($userList as $user) {
                    /* Accounts */

                    $this->form_vars["account_number"] = $user->getUserAccount();
                    $this->user_account = $user->getUserAccount();
                    $this->form_vars["parentAccountId"] = $user->getParentid();
                    $this->form_vars["chkActive"] = ($user->getActive() ? 1 : 0);
                    $this->form_vars["return_shipment_allow"] = ($user->getReturnShipmentAllow() ? 1 : 0);
                    $this->return_shipment_allow = $this->form_vars["return_shipment_allow"];

                    $this->form_vars["company"] = $user->getCompany();
                    $this->form_vars["contact"] = $user->getFullName();
                    $this->form_vars["telephone"] = $user->getTelephone();
                    $this->form_vars["country_id"] = $user->getCountryId();
                    $this->form_vars["email"] = $user->getEmail();
                    $this->form_vars["alter_email"] = $user->getAlternativeEmail();
                    $this->form_vars["website_link"] = $user->getWebsiteLink();
                    $this->form_vars["return_address"] = $user->getReturnAddress();
                    $this->form_vars["weight"] = $user->getDefaultWeight();
                    $this->form_vars["notes"] = $user->getDefaultNotes();
                    $this->form_vars["description"] = $user->getDefaultDescription();
                    $this->form_vars["tracking_order_prefix"] = $user->getTrackingOrderPrefix();
                    $this->form_vars["theme"] = $user->getThemeId();
                    /* End Accounts */
                    /* Company Details */
                    $this->form_vars["bank_account_title"] = $user->getBankAccountTitle();
                    $this->form_vars["bank_sortcode"] = $user->getBankSortcode();
                    $this->form_vars["bank_account_number"] = $user->getBankAccountNumber();
                    $this->form_vars["bank_branch_address"] = $user->getBankBranchAddress();
                    $this->form_vars["trade_name_i"] = $user->getTradeNameI();
                    $this->form_vars["trade_address_i"] = $user->getTradeAddressI();
                    $this->form_vars["trade_name_ii"] = $user->getTradeNameIi();
                    $this->form_vars["trade_address_ii"] = $user->getTradeAddressIi();
                    $this->form_vars["trade_email_i"] = $user->getTradeEmailI();
                    $this->form_vars["trade_phone_i"] = $user->getTradePhoneI();
                    $this->form_vars["trade_email_ii"] = $user->getTradeEmailIi();
                    $this->form_vars["trade_phone_ii"] = $user->getTradePhoneIi();
                    $this->form_vars["reg_number"] = $user->getRegNumber();
                    $this->form_vars["reg_address"] = $user->getRegAddress();
                    $this->form_vars["reg_postcode"] = $user->getRegPostcode();
                    $this->form_vars["reg_country"] = $user->getRegCountry();
                    $this->form_vars["email_signature"] = $user->getUserSignature();
                    /* End Company Details */
                    /* Billing Detail */
                    $this->form_vars["billing_currency"] = $user->getBillingCurrency();
                    $this->form_vars["billingcurrency"] = $user->getBillingCurrency();
                    $this->form_vars["invoice_period"] = $user->getInvoicePeriod();
                    $this->form_vars["invoice_bank_details_id"] = $user->getInvoiceBankDetailsId();
                    $this->form_vars["billing_address"] = $user->getBillingAddress();
                    $this->form_vars["billing_email"] = $user->getBillingEmail();
                    $this->form_vars["billing_contact"] = $user->getBillingContact();
                    $this->form_vars["chkVatChargable"] = $user->getVatChargable();
                    $this->form_vars["payment_term"] = $user->getPaymentTerm();
                    $this->form_vars["query_term"] = $user->getQueryTerm();
                    $this->form_vars["vat_number"] = $user->getVatNumber();
                    $this->form_vars["vat_value"] = $user->getVatValue();
                    $this->form_vars["chkAccountType"] = $user->getIsPrepaid(); //is_prepaid
                    $this->form_vars["ftp_shipment_upload"] = $user->getFtpShipmentUpload(); //is_prepaid
                    if($user->getIsPrepaid()){
						$this->form_vars["add_balance_id"] = '';
                    }
                    $this->form_vars["credit_limit"] = $user->getCreditLimit();
                    $this->form_vars["balance_alert_percentage"] = $user->getBalanceAlertPercentage();
                    $this->form_vars["credit_check"] = $user->getCreditCheck();
                    $this->form_vars["tariff_agreed"] = $user->getTariffAgreed();
                    $this->form_vars["is_fulecharges_include"] = $user->getIsFuelchargesInclude();
                    $this->form_vars["own_tariff"] = $user->getOwnTariff();
                    $this->form_vars["fuel_charges"] = $user->getFuelCharges();
                    $this->form_vars["label_price"] = $user->getLabelPrice();
                    $this->form_vars["discount"] = $user->getDiscount();
                    $this->form_vars["paypalEmail"] = $user->getPaypalEmail();
                    $this->form_vars["paypalCurrency"] = $user->getPaypalCurrency();
                    $this->form_vars["allow_over_size"] = $user->getAllowOversize();
                    $this->form_vars["send_return_email"] = $user->getAllowReturnEmail();
                    $this->form_vars["account_balance"] = $user->getAccountBalance();
                    $this->form_vars["commission_break_event_account_amount
"] = $user->getCommissionBreakEventAccountAmount();
                    $this->commission_break_event_account_amount = $user->getCommissionBreakEventAccountAmount();
                    // Get user tariff
                    $tfilter = new TariffsAccountMappingFilter();
                    $tfilter->addUserAccountFilter($user->getId());
                    $tlist = $tfilter->getTariffNameDistinctList("tariff_id");
                    if (count($tlist) > 0) {
                        foreach ($tlist as $t) {
                            $this->selectedTariff[] = $t->getTariffId();
                        }
                    }
                    /* End Billing Detail */
                    //Check List Get Data
                    $remoteAreaUserMappingfilter = new RemoteareaUserMappingFilter();
                    $remoteAreaUserMappingfilter->addFieldFilter('user_account', $user->getUserAccount());
                    $remoteAreaUserMappingList = $remoteAreaUserMappingfilter->getColumnDistinctList('postcode_name');
                    if (count($remoteAreaUserMappingfilter) > 0) {
                        foreach ($remoteAreaUserMappingfilter as $remoteAreaUserMapping) {
                            $this->selectedPostcodeGroup[] = $remoteAreaUserMapping->getPostcodeName();
                        }
                    }
                    //Check List Get Data End
                    /* End Services */
                    /* Sales Details */
                    $this->form_vars["sales_person"] = $user->getSalesPerson();
                    if (trim($user->getSaleDate()) != '0000-00-00 00:00:00')
                        $this->form_vars["sale_date"] = date('d-m-Y', strtotime($user->getSaleDate()));
                    else
                        $this->form_vars["sale_date"] = '';
                    $this->form_vars["check_list_sales_pot"] = $user->getCheckListSalesPot();
                    $this->form_vars["sales_pot_time_period"] = $user->getSalesPotTimePeriod();
                    $this->form_vars["sales_pot_percentage"] = $user->getSalesPotPercentage();
                    $this->form_vars["sales_value"] = $user->getSalesRate();
                    /* End Sales Details */
                    /* Check List */
                    $this->form_vars["check_list_account_form"] = $user->getCheckListAccountForm();
                    $this->form_vars["check_list_credit_check"] = $user->getCheckListCreditCheck();
                    $this->form_vars["check_list_t_cs"] = $user->getCheckListTCs();
                    $this->form_vars["check_list_tariff_agreed"] = $user->getCheckListTariffAgreed();
                    /* End Check List */
                    $this->form_vars["logo"] = $user->getLogo();
                    $this->userServiceTypeNew = $user->getUserServiceType();
                    $this->scan_doc = $user->getScanDocument();


                    break;
                }
            } else {
                if ($id > 0) {
                    util_redirect("customers.php");
                }
            }
            $this->serviceCustomizeRulesList = "";
            $carrierServiceCustomizeRulesFilter = new carrierServiceCustomizeRulesFilter();
            $carrierServiceCustomizeRulesFilter->addFilter("user_account_id = " . $id);
            $carrierServiceCustomizeRulesFilter->AddOrderBy('serviceid');
            $this->serviceCustomizeRulesList = $carrierServiceCustomizeRulesFilter->getList();
        }
// common initialisation for ths page
        $this->setTitle("User Edit");
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    protected function renderHead()
    {

    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
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
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript">
        </script>
        <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-wizard.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <?php
    }

    protected function renderFooter()
    {
        ?>
        <style>
            .full-width-box {
                width: 100% !important
            }

        </style>
        <script type="text/javascript">
            //PASSWORD STREANGH CHECKTER
            var handlePasswordStrengthChecker = function() {
                var initialized = false;
                var input = $("#password");
                input.keydown(function() {
                    if (initialized === false) {
                        // set base options
                        input.pwstrength({
                            raisePower: 1.4,
                            minChar: 8,
                            showVerdictsInsideProgressBar: true,
                            verdicts: ["Weak", "Normal", "Medium", "Strong", "Very Strong"],
                            scores: [17, 26, 40, 50, 60]
                        });
                        // add your own rule to calculate the password strength
                        input.pwstrength("addRule", "demoRule", function(options, word, score) {
                            return word.match(
                                /^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/
                            ) && score;
                        }, 10, true);
                        // set as initialized
                        initialized = true;
                    }
                });
            }
            $(document).ready(function() {
                change_pay_by();
               //$('#payby_id').hide();
                $(".button-submit").click(function() {
                    // if( !validateEmail($("#email").val()))
                    // {
                    //     $("#email").blur();
                    //     return false;
                    // }
                    swal({
                            title: "Are you sure you wish to continue?",
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
                                $("#form_action").val("save");
                                document.getElementById("submit_form").submit();
                            } else {
                                return false;
                            }
                        });
                });
                //                $('#tariff_name option').mousedown(function (e) {
                //                    e.preventDefault();
                //                    $(this).prop('selected', !$(this).prop('selected'));
                //                    return false;
                //                });
                handlePasswordStrengthChecker();
                $('#country').change(function() {
                    if ($('#country').val() == 'GB') {
                        var chk = $('#chkVatChargable').prop('checked', true);
                        // $.uniform.update(chk);
                        $('#vat_value').attr('readonly', 'readonly');
                        $('#vat_value').val('20.00');
                    } else {
                        var false_chk = $('#chkVatChargable').prop('checked', false);
                        // $.uniform.update(false_chk);
                        $('#vat_value').attr('readonly', false);
                        $('#vat_value').val('0.00');
                    }
                });
                //$('.multiselect_drop_down').multiSelect();
                //                $("#tariff_name").select2({
                //                    placeholder: "Assign Tariff"
                //                });
                //        Handle is prepaid switch
                $('#is_prepaid').on('switchChange.bootstrapSwitch', function(event, state) {
                    
                    if (state) {
                        $('#credit_limit_id').hide();
                        $('#add_balance').show();
                        $('#payby_id').show();
                    } else {
                        $('#payby_id').hide();
                        $('#bank_ref_no_box').hide();
                        $('#cheque_box').hide();
                        $('#add_balance').hide();
                        $('#credit_limit_id').show();
                        $("#credit_limit").val("<?php echo(!empty($this->form_vars["
                credit_limit "]) ? $this->form_vars["
                credit_limit "] : ''); ?>");
                    }
                    var isPaymentAccountStatus = (state == true ? 'prepaid' : 'postpaid');
                    var id = "";
                    <?php
                    if (!empty($_GET['id'])) {
                    ?>
                    id = '<?php echo DbAccess3::escape($_GET['id']);?>'; <?php
                    } else {
                    ?>
                    id = $('#parentAccountId option:selected').val(); <?php
                    } ?>
                    if (id != "") {
                        $.post('customers_details.php', {
                            func: 'chkaccount',
                            accountStatus: isPaymentAccountStatus,
                            id: id
                        }, function(data) {
                            var obj = JSON.parse(data);
                            if (obj.postpaid) {
                                if (obj.payment < 0) {
                                    swal({
                                        title: "Sorry we are unable to change in postpaid.Please pay this " +
                                            obj.payments + " payment",
                                        text: "",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonClass: "btn-danger",
                                        confirmButtonText: "Ok",
                                        cancelButtonText: "No",
                                        closeOnConfirm: true,
                                        closeOnCancel: false
                                    });
                                    $('input[name=chkAccountType]').bootstrapSwitch('state', true);
                                    return false;
                                }
                            } else if (obj.prepaid) {
                                if (obj.payment > 0) {
                                    swal({
                                        title: "Sorry we are unable to change in prepaid.Please clear the all invoices",
                                        text: "",
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonClass: "btn-danger",
                                        confirmButtonText: "Ok",
                                        cancelButtonText: "No",
                                        closeOnConfirm: true,
                                        closeOnCancel: false
                                    });
                                    $('input[name=chkAccountType]').bootstrapSwitch('state', false);
                                    return false;
                                }
                            }
                        });
                    }
                });
                //        Handle User File upload
                $("#upload_file").click(function() {
                    var fileType = $("#document_name").val();
                    var fileTypeText = $("#document_name option:selected").text();
                    if ($.trim(fileType) == "") {
                        swal("", "Please Select File Type", "info");
                        return false;
                    }
                    var filename = $("#file_name").val();
                    if (filename == "") {
                        swal("", "Please Select File", "info");
                        return false;
                    } else {
                        var file_data = $('#file_name').prop('files')[0];
                        var form_data = new FormData();
                        var userAccountId = $("#id").val();
                        form_data.append('file_type', fileType);
                        form_data.append('file_name', filename);
                        form_data.append('user_account_id', userAccountId);
                        form_data.append('file_type_text', fileTypeText);
                        form_data.append('action', "upload_user_doc");
                        form_data.append('user_doc_file', file_data);
                        $.ajax({
                            url: "customers_details.php", // point to server-side PHP script
                            dataType: 'html', // what to expect back from the PHP script, if anything
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function(php_script_response) {
                                if (php_script_response == "0") {
                                    swal("Invalid file type",
                                        "You can only upload gif,jpeg,jpg,png,exl, and pdf file", "error"
                                    );
                                } else {
                                    $("#append_user_doc").append(php_script_response);
                                    $("#document_name").val($("#document_name option:first").val());
                                    $("#document_name").selectpicker('refresh');
                                    $("a.fileinput-exists").click();
                                }
                            }
                        });
                    }
                });
                $('.user_service_remotearea').on('switchChange.bootstrapSwitch', function(event, state) {
                    var service_id = $(this).data("service_id");
                    if (state == true) {
                        $("#span_user_service_remotearea_" + service_id).show();
                    } else {
                        $("#span_user_service_remotearea_" + service_id).hide();
                    }
                });
            });
            
            function change_pay_by() {
                $('.pay_by_link_box').hide();
                var select_pay_by = $('#pay_by').val();
                if (select_pay_by == "cheque") {
                    $('#cheque_box').show();
                } else if (select_pay_by == "bank_transfer") {
                    $('#bank_ref_no_box').show();
                    
                } 
            }
            function numbersonly(e) {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8) {
                    if (unicode == 46) {} else if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
                }
            }

            function fuleCharges() {
                if ($('#is_fulecharges_include_yes').is(':checked')) {
                    $('#fuel_charges').attr('readonly', 'readonly');
                    $('#fuel_charges').val('0.00');
                } else {
                    $('#fuel_charges').attr('readonly', false);
                    $('#fuel_charges').val(' ');
                }
            }

            function AutoGenerateKey(textboxname) {
                var companyname = $('#company').val();
                var telephone = $('#telephone').val();
                var email = $('#email').val();
                var password = AutoGenerateVinculumPassword();
                var return_address = $("#return_address").val();
                var country = $("#country option:selected").text();
                $.post('api_vinculum.php', {
                    Funcajax: 'get_vinculum_extClientId',
                    companyname: companyname,
                    telephone: telephone,
                    email: email,
                    password: password,
                    return_address: return_address,
                    country: country
                }, function(data) {
                    var result = data.split("||");
                    if (result[0] == "SUCCESS") {
                        $("#error_message").hide();
                        $("input[name='" + textboxname + "']").val(result[1]);
                    } else {
                        $("#error_message").show();
                        $("#error_message").html(result[1].replace(".", "") + " on Vinculum.");
                    }
                });
            }

            function AutoGenerateVinculumPassword() {
                var length = 8,
                    charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                    retVal = "";
                for (var i = 0, n = charset.length; i < length; ++i) {
                    retVal += charset.charAt(Math.floor(Math.random() * n));
                }
                return retVal;
            }

            function validateUrl() {
                var websitelink = $('#website_link').val();
                if (websitelink != '') {
                    var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
                    if (!re.test(websitelink)) {
                        swal("", "url error", "error");
                        return false;
                    }
                }
                return true;
            }

            function validateEmail(sEmail) {
                var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                if (filter.test(sEmail)) {
                    return true;
                } else if (sEmail != '') {
                    swal("", "Please enter valid email address", "info");
                    //   $("#email").focus();
                    return false;
                }
            }

            $(document).ready(function() {
                addCurrencyTitle();
                $('#sale_pot').on('switchChange.bootstrapSwitch', function(event, state) {
                    if (state) {
                        addCurrencyTitle();
                        $('#sale_pot_percentage_div').show();
                    } else {
                        $('#sale_pot_percentage_div').hide();
                        $("#sales_pot_time_period").val("");
                        $("#sales_pot_percentage").val("1");
                    }
                });
                $(".plateform_checkbox").on("ifChanged", function() {
                    var platform_id = $(this).data("id");
                    if ($(this).is(":checked")) {
                        $(".shopping_plateform_" + platform_id).removeAttr("disabled");
                    } else {
                        $(".shopping_plateform_" + platform_id).attr("disabled", "disabled");
                        $(".shopping_plateform_" + platform_id).val("");
                    }
                });
                // Set date picker
                $('#sale_date').datepicker({
                    format: 'dd-mm-yyyy'
                });
                //Handle Document Remove functioanlity
                $(document).on('click', '.remove_doc', function() {
                    var el = $(this);
                    swal({
                            title: "<?php echo Translation::GetCaption("
                ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD ") ?>",
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
                                var userDocId = el.data("doc_id");
                                var userAccountId = $("#id").val();
                                $.ajax({
                                    url: 'customers_details.php',
                                    type: 'POST',
                                    data: {
                                        action: 'remove_user_doc',
                                        doc_id: userDocId,
                                        user_account_id: userAccountId
                                    },
                                    headers: {},
                                    success: function() {
                                        $("#usr_doc_" + userDocId).remove();
                                    },
                                    error: function(xhr, status, error) {

                                    }
                                });

                            }
                        });

                });
                $('.multiselect_drop_down').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    afterInit: function(ms) {
                        var that = this,
                            $selectableSearch = that.$selectableUl.prev(),
                            $selectionSearch = that.$selectionUl.prev(),
                            selectableSearchString = '#' + that.$container.attr('id') +
                                ' .ms-elem-selectable:not(.ms-selected)',
                            selectionSearchString = '#' + that.$container.attr('id') +
                                ' .ms-elem-selection.ms-selected';
                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                            .on('keydown', function(e) {
                                if (e.which === 40) {
                                    that.$selectableUl.focus();
                                    return false;
                                }
                            });

                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                            .on('keydown', function(e) {
                                if (e.which == 40) {
                                    that.$selectionUl.focus();
                                    return false;
                                }
                            });
                    },
                    afterSelect: function(values) {
                        this.qs1.cache();
                        this.qs2.cache();
                    },
                    afterDeselect: function(values) {
                        this.qs1.cache();
                        this.qs2.cache();
                    }
                });
            });
        </script>
        <script type="text/javascript">
            function showSelectedService(str, showfield, vale, fieldName, fieldId) {
                $.get("getservices.php?q=" + str + '&n=' + fieldName + '&i=' + fieldId + '&sel=' + vale, function(data) {
                    $("#" + showfield).html(data);
                });
            }

            function showCountries(showfield, str, fieldName, fieldId) {
                $.get("getcountries.php?q=" + str + "&n=" + fieldName + "&i=" + fieldId, function(data) {
                    $("#" + showfield).html(data);
                });
            }

            function showCarrier(showfield, str, fieldName, fieldId) {
                $.get("getcarrier.php?q=" + str + "&n=" + fieldName + "&i=" + fieldId, function(data) {
                    $("#" + showfield).html(data);
                });
            }

            function showServiceType(showfield, str, fieldName, fieldId) {
                $.get("getservicetype.php?q=" + str + "&n=" + fieldName + "&i=" + fieldId, function(data) {
                    $("#" + showfield).html(data);
                });
            }

            function calcUpperWeight(pweight) {
                var toweight = parseFloat(pweight);
                if (toweight < 2)
                    toweight += 0.25;
                else if (toweight < 10)
                    toweight += 0.5;
                else
                    toweight += 5;
                $('#toweight').val(toweight);
                return false;
            }

            function calcLowerWeight(pweight) {
                var fromweight = parseFloat(pweight);
                if (fromweight < 2)
                    fromweight -= 0.25;
                else if (fromweight < 10)
                    fromweight -= 0.5;
                else
                    fromweight -= 5;
                $('#fromweight').val(fromweight);
                return false;

            }

            function addDropdown(spaid, recid, crcode, userid) {
                $.get("getallservices.php?sel=" + crcode + "&re=" + recid + "&user_id=" + userid, function(data) {
                    $("#" + spaid).html(data);
                    $("#" + spaid).attr("onclick", '');
                });
            }

            function save_service_change(mval, rid, userid) {
                $.get("updaterule.php?services=" + mval + "&reid=" + rid + "&user_id=" + userid, function(data) {
                    $("#ser-" + rid).html(data);
                    $("#ser-" + rid).attr("onclick", 'addDropdown(\'ser-\'' + rid + ',' + rid + ',' + mval + ',' + userid +
                        ')');

                });
            }

            function remove_service(rid, act, inact) {
                if (confirm("Are you sure, you want to inactive this option")) {
                    $.get("updaterule.php?reid=" + rid + "&action=delete", function(data) {
                        $("#" + rid).attr("class", 'inactive');
                        $("#" + act).show();
                        $("#" + inact).hide();
                    });
                }
            }

            function add_service(rid, act, inact) {
                if (confirm("Are you sure, you want to active this option")) {
                    $.get("updaterule.php?reid=" + rid + "&action=active", function(data) {
                        $("#" + rid).attr("class", 'active');
                        $("#" + act).show();
                        $("#" + inact).hide();
                    });
                }
            }

            function routing_service(rid, act, inact) {
                if (confirm("Are you sure, you want to change service type")) {
                    $.get("updaterule.php?reid=" + rid + "&action=routing", function(data) {
                        $("#" + rid).attr("class", 'active');
                        $("#" + act).show();
                        $("#" + inact).hide();
                    });
                }
            }

            function oneworld_service(rid, act, inact) {
                if (confirm("Are you sure, you want to change service type")) {
                    $.get("updaterule.php?reid=" + rid + "&action=oneworld", function(data) {
                        $("#" + rid).attr("class", 'active');
                        $("#" + act).show();
                        $("#" + inact).hide();
                    });
                }
            }

            var count = 0;

            function selcetAllServicesCheck(group) {
                if (count == 0) {
                    $('.serviceList').each(function() {
                        if (this.checked == false) {
                            this.click();
                            trackUncheck(this, $(this).data('servicecode'));
                        }
                    });
                    count = 1;
                } else {
                    $('.serviceList').each(function() {
                        if (this.checked == true) {
                            this.click();
                            trackUncheck(this, $(this).data('servicecode'));
                        }
                    });
                    count = 0;
                }
            }

            function selcetAllCountryCheck(valCheck) {
                if (valCheck.checked === true) {
                    $('.countryList').attr('checked', 'checked');
                    $(this).attr('checked', 'checked');
                } else {
                    $('.countryList').removeAttr('checked');
                    //$(this).val('check all');
                    $(this).attr('checked', 'unchecked');
                }
            }

            function submitCountry() {
                $("#form_action").val("saveCountry");
                $("#adminForm").submit();
                return false;
            }

            function checkWeightValid() {
                if (parseFloat(document.getElementById('fromweight').value) < parseFloat(document.getElementById('toweightmore')
                    .value)) {
                    submitWeight();
                } else if (parseFloat(document.getElementById('fromweight').value) < parseFloat(document.getElementById('toweight')
                    .value)) {
                    submitWeight();
                } else {
                    alert("Please select appropriate weight range");
                    return false;
                }
            }

            function submitWeight() {
                $("#form_action").val("saveWeight");
                $("#adminForm").submit();
            }

            $(window).scroll(function() {
                if ($(this).scrollTop() > 380) {
                    if ($('#advance-service').height() < 350)
                        $('#advance-service').attr('style', 'position:fixed; top:30px; width:40%');
                    else
                        $('#advance-service').attr('style', '');
                } else {
                    $('#advance-service').attr('style', '');
                }
            });

            function submit_Country() {
                $("#form_action").val("search");
                $("#adminForm").submit();
                return false;
            }

            function saveCountry() {
                $("#form_action").val("saveCountry");
                $("#adminForm").submit();
            }

            function morethan() {
                $('#more-than-30').toggle();
            }

            function changeVatValue() {
                if ($('#country_id').val() == '225') {
                    $('#chkVatChargable').attr('checked', true);
                    $('#chkVatChargable').bootstrapSwitch('state', true);
                    $("#chkVatChargable").prop('checked', true);
                    $('#vat_value').attr('readonly', 'readonly');
                    $('#vat_value').val('20.00');
                } else {
                    $('#chkVatChargable').attr('checked', false);
                    $('#chkVatChargable').bootstrapSwitch('state', false);
                    $('#vat_value').attr('readonly', false);
                    $('#vat_value').val('0.00');
                }
            }

            var deleteserviceArray = Array();

            function trackUncheck(elementDom, serviceCode) {
                var index = deleteserviceArray.indexOf(serviceCode);
                if (elementDom.checked === true) {
                    if (index > -1) {
                        deleteserviceArray.splice(index, 1);
                    }
                } else {
                    deleteserviceArray.push(serviceCode);
                }
                $('#deleteserviceids').val(deleteserviceArray.join(','));
            }

            function numbersonly(e) {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8) {
                    if (unicode == 46) {} else if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
                }
            }

            $(document).ready(function() {
                //    $("#is_product").change();
                $("#btnSaveServices").click(function() {
                    if (confirm(
                        "Are you sure you want to apply these services changes?, If you have disabled any service, It will disabled for all its sub accounts. Please click Yes to continue."
                    )) {
                        $("#form_action").val("save");
                        $("#adminForm").submit();
                    } else {
                        return false;
                    }
                });
                //Sreach functionality for Carrier Name
                $("#search_company_document").on('keyup keypress', function(e) {
                    $('.user_company_documents').each(function(e) {
                        var current = $.trim($(this).data('doc_type')).toLowerCase();
                        var search_str = $.trim($("#search_company_document").val()).toLowerCase();;
                        if (current.indexOf(search_str) >= 0) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });


                });

            <?php
                if (!Permissions::checkFilePermission('customer_detail_company') && !Permissions::checkFilePermission(
                    'customer_detail_billing') && !Permissions::checkFilePermission('customer_detail_service') && !
                    Permissions::checkFilePermission('customer_detail_sales') && !Permissions::checkFilePermission(
                    'customer_detail_thirdparty') && !Permissions::checkFilePermission('customer_detail_checklist')) {
			?>
                    //            Uncomment when you will implement permissions on tab
                    //        $("#submit_btn").show();
                <?php
                }
                ?>
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    public function renderBody()
    {
        $sessionUser = $this->sessionUser;
        $displayname = '';
        if ($_GET['id'] != "") {
            $filteruser = new UserAccountFilter();
            $filteruser->addIdFilter(util_get_num("id"));
            $routinguser = $filteruser->getList();
            if (!empty($routinguser)) {
                $displayname = $routinguser[0]->getUserAccount();
            } else {
                $displayname = '';
            }
        }
// transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="row">
            <div class="col-xxl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1"><?php
                            if (util_get_num("id") == "" || util_get_num("id") < 0)
                                echo "Add Member ";
                            else
                                echo "Edit Member ";
                            if ($displayname != '')
                                print('for ' . $displayname);
                            ?></h4>
                        <div class="flex-shrink-0">
                            <div class="form-check form-switch form-switch-right form-switch-md">

                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <?php if(!empty($this->counting_error)){ ?>
                            <p class="text-muted"><code><?php errorList::getItem()->render(); ?></code> </p>
                        <?php } ?>
                        <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
                            <p class="text-muted">You have created new account.</p>
                        <?php } else if (isset($_GET['success']) && $_GET['success'] == 2) { ?>
                            <p class="text-muted">Your data has been updated.</p>
                        <?php }  ?>
                        <?php if(!empty($this->flashMsg->display())){ ?>
                            <p class="text-muted"><code><?php $this->flashMsg->display(); ?></code> </p>
                        <?php } ?>
                        <form class="form-horizontal1" action="customers_details.php" id="submit_form" method="POST" name="admin_form"  enctype="multipart/form-data">
                            <div class="live-preview">
                            <div class="accordion" id="default-accordion-example">
                                <?php if (Permissions::checkFilePermission('customer_detail_account')) { ?>
                                    <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Basic Member Details
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#default-accordion-example">
                                        <div class="accordion-body">
                                            <div class="row gy-4">
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="account_number" class="form-label">Account Number</label>
                                                        <?php
                                                        if (count($routinguser) > 0 && trim($routinguser[0]->getUserAccount()) != '' && strtoupper($sessionUser->getUserType()) != 'ADMIN') {
                                                            echo '<input id="account_number" type="text" name="account_number" value="' . @$account_number . '"   class="form-control rounded-pill" maxlength="15" readonly="readonly" required="required" onkeyup="this.value = this.value.toUpperCase();" />';
                                                        } else {
                                                            ?>
                                                            <input id="account_number" type="text" name="account_number"
                                                                   value="<?php echo @$account_number; ?>"
                                                                    class="form-control rounded-pill"
                                                                   required="required" maxlength="15"
                                                                   onkeyup="this.value = this.value.toUpperCase();">
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <?php if ($sessionUser->getUserType() == User::USER_TYPE_ADMIN) { ?>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="parentAccountId" class="form-label">Parent Account</label>
                                                        <?php
                                                        $accountParentId = 0;
                                                        if ($sessionUser->getUserType() == User::USER_TYPE_CORPORATE)
                                                            $accountParentId = $this->sessionUser->getUserAccountId();
                                                        $allowedLevel = 0;
                                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                                            $allowedLevel = 1;
                                                        }
                                                        echo Ddl::showTreeDropdown('parentAccountId', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $parentAccountId, "Please Select Account", 'class="form-control  rounded-pill"', "parentAccountId", "", "", "", "", true, $allowedLevel);
                                                        ?>
                                                        <input type="hidden" id="parentuserid" name="parentuserid"
                                                               value="<?php echo $parentAccountId; ?>">
                                                    </div>
                                                </div>
                                                    <?php  } ?>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="company" class="form-label">Name of Company</label>
                                                            <input id="company" type="text" name="company"
                                                                   value="<?php echo @$company; ?>"
                                                                   class="form-control rounded-pill"
                                                                   required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="contact" class="form-label">Contact Name</label>
                                                        <input id="contact" type="text" name="contact"
                                                               value="<?php echo @$contact; ?>"
                                                               class="form-control rounded-pill"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="telephone" class="form-label">Contact Telephone Number</label>
                                                        <input id="telephone" type="text" name="telephone"
                                                               value="<?php echo @$telephone; ?>"
                                                               class="form-control rounded-pill"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="Country" class="form-label">Country</label>
                                                        <?php
                                                        echo Ddl::generateCountryDDL('country_id', $country_id, 'id', ' class=" form-control rounded-pill" required=""  onChange=changeVatValue();');
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="email" class="form-label">Account Owner Contact Email</label>
                                                        <input id="email" type="text" name="email"
                                                               value="<?php echo @$email; ?>"
                                                               class="form-control rounded-pill"
                                                               onblur="validateEmail(this.value);"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="alter_email" class="form-label">Email (CS, Warehouse)</label>
                                                        <input id="alter_email" type="text" name="alter_email"
                                                               value="<?php echo @$alter_email; ?>"
                                                               class="form-control rounded-pill"
                                                               onblur="return validateEmail(this.value);"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="alter_email" class="form-label">Website Link</label>
                                                        <input id="website_link" type="text" name="website_link"
                                                               value="<?php echo @$website_link; ?>"
                                                               class="form-control rounded-pill"
                                                               onblur="return validateEmail(this.value);"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="return_address" class="form-label">Return Address</label>
                                                        <textarea id="return_address" name="return_address" type="text"
                                                                   required=""
                                                                  class="form-control rounded-pill" ><?php echo(!empty($return_address) ? $return_address : 'Logistic Canal Garden Lahore'); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="alter_email" class="form-label">Weight</label>
                                                        <input id="weight" type="text" name="weight"
                                                               value="<?php echo @$weight; ?>"
                                                               class="form-control rounded-pill"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="weight" class="form-label">Weight</label>
                                                        <input id="weight" type="text" name="weight"
                                                               value="<?php echo @$weight; ?>"
                                                               class="form-control rounded-pill"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="notes" class="form-label">Notes</label>
                                                        <input id="notes" type="text" name="notes"
                                                               value="<?php echo @$notes; ?>"
                                                               class="form-control rounded-pill"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="notes" class="form-label">Description</label>
                                                        <input id="notes" type="text" name="notes"
                                                               value="<?php echo @$notes; ?>"
                                                               class="form-control rounded-pill"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="tracking_order_prefix" class="form-label">Order Reference / Tracking Number Prefix</label>
                                                        <input id="tracking_order_prefix" type="text" name="tracking_order_prefix"
                                                               value="<?php echo @$tracking_order_prefix; ?>"
                                                               class="form-control rounded-pill"
                                                               required="required" >
                                                    </div>
                                                </div>
                                                <?php
                                                if ($sessionUser->getUserType() == User::USER_TYPE_ADMIN && 1 == 2) {
                                                ?>
                                                    <div class="col-xxl-4 col-md-4">
                                                        <div>
                                                            <label for="theme_id" class="form-label">Select Theme</label>
                                                            <?php
                                                            echo Ddl::generateDDL("theme", "ThemesFilter", ['is_active' => 1], "name", "id", $theme, ' class="form-control rounded-pill"  ', "Default", "", "theme_id");

                                                            ?>
                                                        </div>
                                                    </div>

                                                    <?php
                                                }
                                                ?>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="theme_id" class="form-label">Upload Your Company Logo</label>
                                                        <input type="hidden" id="logo" name="logo" value="<?php echo $logo; ?>">
                                                        <br clear="all">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-new thumbnail"
                                                                 style="width: 200px; height: 150px;">
                                                                <?php
                                                                if (trim($logo) != '' && file_exists('../images/userlogo/' . $logo)) {
                                                                    echo '<img src="../images/userlogo/' . $logo . '" >';
                                                                } else {
                                                                    echo '<img src = "../images/No-image-found.jpg">';
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="fileinput-preview fileinput-exists thumbnail"
                                                                 style="max-width: 200px; max-height: 150px;"></div>
                                                            <div>
                                                        <span class="btn default btn-file"> <span class="fileinput-new">
                                                                Select image </span> <span class="fileinput-exists">
                                                                Change </span>
                                                            <input id="your_logo" type="file" name="your_logo">
                                                        </span> <a href="javascript:;" class="btn red fileinput-exists"
                                                                   data-dismiss="fileinput"> Remove </a>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix margin-top-10"><span
                                                                    class="label label-primary"><small>NOTE!</span>
                                                            Recommended logo dimensions (254 x 62) </small></div>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                        <input checked="checked" id="user_name" type="checkbox" class="form-check-input" name="chkActive" <?php echo ($chkActive == '1' ? 'checked="checked"' : ''); ?>>
                                                        <label class="form-check-label" for="user_name">Active</label>
                                                    </div>
                                                </div>
<!--                                                <div class="col-xxl-4 col-md-4">-->
<!--                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">-->
<!--                                                        <input checked="checked" id="return_shipment_allow" type="checkbox" class="form-check-input" name="return_shipment_allow" --><?php //echo ($this->return_shipment_allow == '1' ? 'checked="checked"' : ''); ?><!-- >-->
<!--                                                        <label class="form-check-label" for="return_shipment_allow">Allow Return Shipment</label>-->
<!--                                                    </div>-->
<!--                                                </div>-->


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('customer_detail_company')) { ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Company Information
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#default-accordion-example">
                                        <div class="accordion-body">
                                            <div class="row gy-4">

                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="bank_account_title" class="form-label">Bank Account Title</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="bank_account_title"
                                                                id="bank_account_title" value="<?php echo @$bank_account_title; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="bank_sortcode" class="form-label">Bank Sort Code</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="bank_sortcode"
                                                                id="bank_sortcode" value="<?php echo @$bank_sortcode; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="bank_account_number" class="form-label">Bank Account Number</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="bank_account_number"
                                                                id="bank_account_number" value="<?php echo @$bank_account_number; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="bank_brance_address" class="form-label">Branch Address</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="bank_brance_address"
                                                                id="bank_brance_address" value="<?php echo @$bank_brance_address; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_name_i" class="form-label">Trade Reference Name (1)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_name_i"
                                                                id="trade_name_i" value="<?php echo @$trade_name_i; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_address_i" class="form-label">Trade Reference Address (1)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_address_i"
                                                                id="trade_address_i" value="<?php echo @$trade_address_i; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_email_i" class="form-label">Trade Reference Email (1)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_email_i"
                                                                id="trade_email_i" value="<?php echo @$trade_email_i; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_phone_i" class="form-label">Trade Reference Phone Number (1)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_phone_i"
                                                                id="trade_phone_i" value="<?php echo @$trade_phone_i; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_name_ii" class="form-label">Trade Reference Name (2)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_name_ii"
                                                                id="trade_name_ii" value="<?php echo @$trade_name_ii; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_address_ii" class="form-label">Trade Reference Address (2)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_address_ii"
                                                                id="trade_address_ii" value="<?php echo @$trade_address_ii; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_email_ii" class="form-label">Trade Reference Email (2)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_email_ii"
                                                                id="trade_email_ii" value="<?php echo @$trade_email_ii; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="trade_phone_ii" class="form-label">Trade Reference Phone Number (2)</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="trade_phone_ii"
                                                                id="trade_phone_ii" value="<?php echo @$trade_phone_ii; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="vat_number" class="form-label">Customer VAT Number</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="vat_number"
                                                                id="vat_number" value="<?php echo @$vat_number; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="reg_number" class="form-label">Company Registration Number</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="reg_number"
                                                                id="reg_number" value="<?php echo @$reg_number; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="reg_address" class="form-label">Company Registered Address</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="reg_address"
                                                                id="reg_address" value="<?php echo @$reg_address; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="reg_postcode" class="form-label">Company Registered Postcode</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="reg_postcode"
                                                                id="reg_postcode" value="<?php echo @$reg_postcode; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="country" class="form-label">Registered Country</label>
                                                        <?php
                                                        echo Ddl::generateCountryDDL('country_id', $country_id, 'id', ' class=" form-control rounded-pill" required=""  onChange=changeVatValue();');
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="email_signature" class="form-label">Signature</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="email_signature"
                                                                id="email_signature" value="<?php echo @$email_signature; ?>" required="required">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('customer_detail_billing')) { ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Billing Information
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#default-accordion-example">
                                        <div class="accordion-body">
                                            <div class="row gy-4">

                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="invoiceperiod" class="form-label">Invoice Period</label>
                                                        <?php
                                                        $IsPeArr = array('daily' => 'Daily', 'weekly' => 'Weekly', 'bi-monthly' => 'Bi-monthly', 'monthly' => 'Monthly');
                                                        echo Ddl::generateArrayDDL('invoice_period', $IsPeArr, $invoice_period, '', ' class=" form-control rounded-pill"');
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="bankdisplay" class="form-label">Bank Account to Display On Invoices</label>
                                                        <?php
                                                        echo Ddl::generateDDL('invoice_bank_details_id', 'InvoiceBankDetailsFilter', ' status = 1 AND user_account_id = "' . $sessionUser->getUserAccountId() . '"', ['account_title', 'bank_name','currency'], 'id', $invoice_bank_details_id, ' class=" form-control rounded-pill" data-toggle="tooltip" data-placement="top" title="Billing Currency" data-original-title="Attach Bank detail to invoices"', 'Select Bank Detail', '', 'Attach Bank detail to invoices', 'Attach Bank detail to invoices');
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="payment_term" class="form-label">Payment Term</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="payment_term"
                                                         id="payment_term" value="<?php echo @$payment_term; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="query_term" class="form-label">Query Term</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="query_term"
                                                                id="query_term" value="<?php echo @$query_term; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="billing_contact" class="form-label">Contact/Name For Accounts/Billing</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="billing_contact"
                                                                id="billing_contact" value="<?php echo @$billing_contact; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="billing_email" class="form-label">Email For Accounts/Billing</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="billing_email"
                                                                id="billing_email" value="<?php echo @$billing_email; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="billing_address" class="form-label">Billing Company & Address</label>
                                                        <input  autocomplete="off"  <?php echo ((int)($this->user_id) > 0) ? 'readonly="readonly" ' : ''?> type="text" class="form-control rounded-pill" name="billing_address"
                                                                id="billing_address" value="<?php echo @$billing_address; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                        <input checked="checked" id="chkVatChargable" type="checkbox" class="form-check-input" name="chkVatChargable" <?php echo ($chkActive == '1' ? 'checked="checked"' : ''); ?>>
                                                        <label for="chkVatChargable" class="form-label">VAT Chargable</label>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                        <input checked="checked" id="chkAccountType" type="checkbox" class="form-check-input" name="chkAccountType" <?php echo ($chkActive == '1' ? 'checked="checked"' : ''); ?>>
                                                        <label for="chkAccountType" class="form-label">Is Prepaid</label>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                        <input checked="checked" id="ftp_shipment_upload" type="checkbox" class="form-check-input" name="ftp_shipment_upload" <?php echo ($chkActive == '1' ? 'checked="checked"' : ''); ?>>
                                                        <label for="ftp_shipment_upload" class="form-label">Is Upload Shipment</label>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="invoiceperiod" class="form-label">Billing Currency</label>
                                                        <?php
                                                        if (!empty($_GET['id']) && $_GET['id'] > 0) {
                                                            $account_id_for_currency_debit = DbAccess3::escape($_GET['id']);
                                                            if (!empty($chkAccountType)) {
                                                                // prepaid
                                                                $returnResult = PaymentsHistoryFilter::checkAccountTransaction($account_id_for_currency_debit);
                                                            } else {
                                                                //postpaid
                                                                $returnResult = ConsignmentChargesFilter::checkAccountPostPaidCurrency($account_id_for_currency_debit);
                                                            }
                                                        }
                                                        $DisbaleAttr = '';
                                                        if ($returnResult == 1) {
                                                            $DisbaleAttr = "disabled='disabled'";
                                                        }
                                                        if (empty($billing_currency))
                                                            $billing_currency = 'GBP';
                                                        echo Ddl::generateDDL('billing_currency', 'CurrencyFilter', ' AND isactive = 1 ', 'rightsymbol', 'rightsymbol', $billing_currency, ' class="form-control rounded-pill" ' . $DisbaleAttr . ' data-toggle="tooltip" data-placement="top" title="Billing Currency" data-original-title="Billing Currency"', 'Select Currency', '', 'billing_currency', 'Billing Currency');
                                                        ?>
                                                    </div>
                                                </div>

                                                        <?php
                                                        if (!empty($_GET['id']) && $_GET['id'] > 0) {
                                                        $accountBalance = !empty($account_balance) ? number_format($account_balance, 2) : 0.00;
                                                        $availableAccountBalance = "Available Credit: ".$accountBalance." ".$billing_currency;
                                                        if (!empty($chkAccountType)) {
                                                            // prepaid
                                                            $availableAccountBalance = "Available Balance: ".$accountBalance." ".$billing_currency;
                                                        } else {
                                                            //postpaid
                                                            $availableAccountBalance = "Available Credit: ".$accountBalance." ".$billing_currency;
                                                        }
                                                        ?>
                                                            <div class="col-md-3">
                                                                <div class="form-group pull-right">
                                                                    <label>&nbsp;</label><br />
                                                                    <label class="btn green btn-outline btn-sm active" style="padding: 7px;">
                                                                        <strong><?php echo $availableAccountBalance; ?></strong>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                <?php } ?>

                                                <div class="col-xxl-4 col-md-4" id="add_balance"
                                                     style="display: <?php echo($chkAccountType == '1' ? '' : 'none'); ?>;">
                                                    <div>
                                                        <label for="add_balance_id" class="form-label">Add Balance</label>
                                                        <input  type="text" class="form-control rounded-pill" name="add_balance_id" id="add_balance_id" value="<?php echo @$add_balance_id; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" id="payby_id" style="display: <?php echo($chkAccountType == '1' ? '' : 'none'); ?>;">
                                                    <div>
                                                        <label for="email_signature" class="form-label">Pay By</label>
                                                        <?php
                                                        $pay_by_array = array("cash" => "Cash","bank_transfer" => "Bank Transfer", "cheque" => "By Cheque", "bonus" => "Bonus");
                                                        echo Ddl::generateArrayDDL('pay_by', $pay_by_array, "", "", ' class="form-control rounded-pill" onchange="change_pay_by()" ', 'Select Pay by', 'pay_by', 'Select Pay by', '');
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="col-xxl-4 col-md-4 pay_by_link_box" id="cheque_box" style="display: <?php echo($chkAccountType == '1' ? '' : 'none'); ?>;">
                                                    <div>
                                                        <label for="cheque_number" class="form-label">Cheque Number</label>
                                                        <input  type="text" class="form-control rounded-pill" name="cheque_number" id="cheque_number" value="<?php echo @$cheque_number; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4 pay_by_link_box" id="bank_ref_no_box" style="display: <?php echo($chkAccountType == '1' ? '' : 'none'); ?>;">
                                                    <div>
                                                        <label for="bank_ref_number" class="form-label">Ref No</label>
                                                        <input  type="text" class="form-control rounded-pill" name="bank_ref_number" id="bank_ref_number" value="<?php echo @$bank_ref_number; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" id="credit_limit_id"
                                                     style="display: <?php echo($chkAccountType == '1' ? 'none' : ''); ?>;">
                                                    <div>
                                                        <label for="credit_limit" class="form-label">Credit Limit</label>
                                                        <input  type="text" class="form-control rounded-pill" name="credit_limit" id="credit_limit" value="<?php echo @$credit_limit; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" >
                                                    <div>
                                                        <label for="balance_alert_percentage" class="form-label">Balance Alert Percentage (%)</label>
                                                        <input  type="text" class="form-control rounded-pill" name="balance_alert_percentage" id="balance_alert_percentage" value="<?php echo @$balance_alert_percentage; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" >
                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                        <input checked="checked" id="credit_check" type="checkbox" class="form-check-input" name="credit_check" >
                                                        <label for="credit_check" class="form-label">Credit Checks</label>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" >
                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                        <input  id="credit_check" type="checkbox" class="form-check-input" name="tariff_agreed" >
                                                        <label for="tariff_agreed" class="form-label">Tariff Agreed</label>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" >
                                                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                        <input  id="is_fulecharges_include" type="checkbox" class="form-check-input" name="is_fulecharges_include" >
                                                        <label for="is_fulecharges_include" class="form-label">Included Fuel Charges</label>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" style=" <?php echo $customerHideStyle; ?>">
                                                    <div>
                                                        <label for="balance_alert_percentage" class="form-label">Label Charges</label>
                                                        <input  type="text" class="form-control rounded-pill" name="label_price" id="label_price" value="<?php echo @$label_price; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" style=" <?php echo $customerHideStyle; ?>">
                                                    <div>
                                                        <label for="discount" class="form-label">Discount On Label Charges (%)</label>
                                                        <input  type="text" class="form-control rounded-pill" name="discount" id="discount" value="<?php echo @$discount; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4">
                                                    <div>
                                                        <label for="allow_over_size" class="form-label">Allow Over Size</label>
                                                        <input  type="text" class="form-control rounded-pill" name="allow_over_size" id="allow_over_size" <?php echo($allow_over_size == '1' ? 'checked="checked"' : ''); ?> required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" style=" <?php echo $customerHideStyle; ?>">
                                                    <div>
                                                        <label for="tariff_name" class="form-label">Tariff Name
                                                            <?php if (util_get_num("id") != "" && util_get_num("id") > 0) {?>
                                                            <a href="tariffs_list.php" title="View"  class="btn btn-primary btn-sm pull-right" >
                                                                <i class="fa fa-plus"></i> Add New Tariff
                                                            </a><?php } ?></label>
                                                        <?php
                                                        $useraccount = CustomerAccount::accountImmediateParent($this->form_vars["id"]);
                                                        $wereclauseData = "  status = 1 AND user_account_id = '" . $useraccount . "'  AND tariff_type = 'customer'";
                                                        echo Ddl::generateDDL('tariff_name[]', 'TariffsFilter', $wereclauseData, 'name', 'id', $this->selectedTariff, ' class="form-control" multiple="multiple"', '', '', 'tariff_name', 'Tariff', "", "", "");
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" >
                                                    <div>
                                                        <label for="paypal_merchant_account_email" class="form-label">Paypal Email</label>
                                                        <input  type="text" class="form-control rounded-pill" name="paypal_merchant_account_email" id="paypal_merchant_account_email" value="<?php echo @$paypal_merchant_account_email; ?>" required="required">
                                                    </div>
                                                </div>
                                                <div class="col-xxl-4 col-md-4" >
                                                    <div>
                                                        <label for="paypal_merchant_account_email" class="form-label">Currency</label>
                                                        <?php
                                                        echo Ddl::generateDDL('paypal_merchant_account_email_currency', 'CurrencyFilter', ' AND isactive = 1 ', 'rightsymbol', 'rightsymbol', $this->form_vars['paypalCurrency'], ' class="form-control rounded-pill"  data-toggle="tooltip" data-placement="top" title="Billing Currency" data-original-title="Billing Currency"', 'Select Currency', '', 'paypal_merchant_account_email_currency', 'Payapl Billing Currency');
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('customer_detail_sales') && 1==2) { ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                Sales Information
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#default-accordion-example">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-xxl-4 col-md-4">
                                                        <div>
                                                            <label for="sales_person" class="form-label">Sales Person</label>
                                                            <?php
                                                            echo Ddl::generateDDL("sales_person", "UserFilter", ['user_type' => 'sales_agent','active_flag' => 1, 'user_account_id'=>$this->sessionUser->getUserAccountId()], "first_name", "id", $sales_person, ' class="form-control rounded-pill"  ', "Select Sales Person", "", "sales_person");
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-4 col-md-4">
                                                        <div>
                                                            <label for="sales_person" class="form-label">Sale Date</label>
                                                            <input id="sale_date" name="sale_date" type="calender"
                                                                   value="<?php echo @formatDate($sale_date); ?>"
                                                                   class="form-control rounded-pill" />
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-4 col-md-4">
                                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                            <input id="check_list_sales_pot" type="checkbox" class="form-check-input" name="check_list_sales_pot" <?php echo($check_list_sales_pot == '1' ? 'checked' : ''); ?> >
                                                            <label for="check_list_sales_pot" class="form-label">Sales POT</label>
                                                            <?php
                                                            if (!isset($check_list_sales_pot))
                                                                $check_list_sales_pot = "";
                                                            $selected = ((strtoupper($check_list_sales_pot) == "1") ? "checked" : "");
                                                            $sal_percentage = 1;
                                                            if (!empty($sales_pot_percentage) && $sales_pot_percentage > 1) {
                                                                $sal_percentage = $sales_pot_percentage;
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div id="sale_pot_percentage_div" <?php if ($selected != "checked") { ?> style="display: none;" <?php } ?> >
                                                        <div class="col-xxl-4 col-md-4">
                                                            <div>
                                                                <label for="sales_pot_time_period" class="form-label">Sales Pot Time Period</label>
                                                                <?php
                                                                $IsPeArr = array('3' => '3 Months', '6' => '6 Months', '9' => '9 Month', '12' => '1 Year', '24' => '2 Year', '36' => '3 Year', '48' => '4 Year', '60' => '5 Year', '-1' => 'Un-conditional');
                                                                echo Ddl::generateArrayDDL('sales_pot_time_period', $IsPeArr, $sales_pot_time_period, '', ' class="orm-control rounded-pill" rel="tooltip" data-original-title="Invoice Period" placeholder="Invoice Period"', '', 'sales_pot_time_period');
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-xxl-4 col-md-4">
                                                            <div>
                                                                <label for="sales_pot_percentage" class="form-label">Sale Pot Percentage</label>
                                                                <input id="sales_pot_percentage" name="sales_pot_percentage" type="text"
                                                                       value="<?php echo @$sal_percentage; ?>"
                                                                       onkeypress='return numbersonly(event)'
                                                                       class="form-control rounded-pill" />
                                                            </div>
                                                        </div>
                                                        <div class="col-xxl-4 col-md-4">
                                                            <div>
                                                                <label for="commission_break_event_account_amount" class="form-label">Commission Break Event Amount ( <span id="show_currency_id"></span> ) </label>
                                                                <input id="commission_break_event_account_amount" name="commission_break_event_account_amount" type="text"
                                                                       value="<?php echo @$commission_break_event_account_amount; ?>"
                                                                       class="form-control rounded-pill" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-4 col-md-4 <?php echo $user_type; ?>">
                                                        <div>
                                                            <label for="sales_value" class="form-label">Company Pot Percentage </label>
                                                            <input id="sales_value" name="sales_value" type="text"
                                                                   value="<?php echo (isset($sales_value)? $sales_value: '3'); ?>"
                                                                   class="form-control rounded-pill" />
                                                            <input type="hidden" id="max_sales_rate" name="max_sales_rate"
                                                                   value="<?php echo $this->UserSalesRate; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('customer_detail_thirdparty') && 1==2) { ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFive">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                                Carrier Information
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#default-accordion-example">
                                            <div class="accordion-body">
                                                <div class="row gy-4">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('customer_detail_checklist') && 1==2) { ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSix">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                                Final Verification
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#default-accordion-example">
                                            <div class="accordion-body">
                                                <div class="row gy-4">
                                                    <div class="col-xxl-4 col-md-4" >
                                                        <?php
                                                        if (!isset($check_list_account_form)) {
                                                            $check_list_account_form = "";
                                                        }
                                                        $selected = (($check_list_account_form == "1") ? "checked" : "");
                                                        ?>
                                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                            <input <?php echo $selected; ?> name="check_list_account_form"  id="check_list_account_form" type="checkbox" class="form-check-input"  >
                                                            <label for="check_list_account_form" class="form-label">Account Form</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-4 col-md-4" >
                                                        <?php
                                                        if (!isset($check_list_credit_check))
                                                            $check_list_credit_check = "";
                                                        $selected = ((strtoupper($check_list_credit_check) == "1") ? "checked" : "");
                                                        ?>
                                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                            <input <?php echo $selected; ?> name="check_list_credit_check"  id="check_list_credit_check" type="checkbox" class="form-check-input"  >
                                                            <label for="check_list_credit_check" class="form-label">Credit Check</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-4 col-md-4" >
                                                        <?php
                                                        if (!isset($check_list_t_cs))
                                                            $check_list_t_cs = "";
                                                        $selected = ((strtoupper($check_list_t_cs) == "1") ? "checked" : "");
                                                        ?>
                                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                            <input <?php echo $selected; ?> name="check_list_t_cs"  id="check_list_t_cs" type="checkbox" class="form-check-input"  >
                                                            <label for="check_list_t_cs" class="form-label">Terms & Conditions</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-4 col-md-4" >
                                                        <?php
                                                        if (!isset($check_list_tariff_agreed))
                                                            $check_list_tariff_agreed = "";
                                                        $selected = ((strtoupper($check_list_tariff_agreed) == "1") ? "checked" : "");
                                                        ?>
                                                        <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                            <input <?php echo $selected; ?> name="check_list_tariff_agreed"  id="check_list_tariff_agreed" type="checkbox" class="form-check-input"  >
                                                            <label for="check_list_tariff_agreed" class="form-label">Tariff Agreed</label>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row" style="margin-top: 5%">
                                    <div class="col-xxl-12 col-md-12 text-right pull-right">
                                        <a id="btnCancel" href="customers.php" class="btn_cancel btn btn-danger">Cancel </a>
<!--                                        <a href="javascript:;" class="btn default button-previous">-->
<!--                                            Back </a> -->
<!--                                        <a href="javascript:;" class="btn btn-primary button-next">-->
<!--                                            Continue </a>-->
                                        <a href="javascript:;" id="submit_btn" class="btn btn-primary button-submit"> Submit</a>
                                        <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                                        <input type="hidden" name="form_action" id="form_action" value="" />
                                        <input type="hidden" name="billingcurrency" id="billingcurrency" value="<?=$billingcurrency;?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
        </div>
        <!--end row-->
        
        <div class="modal fade" tabindex="-1" role="dialog" id="view-country-popup">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="service_name"></span> Countries List</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12" id="carrier-logs-display">

                            </div>
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
        <script>
            $(document).ready(function() {
                $("#search_carrier").on('keyup keypress', function(e) {
                    $('.search_carrier_custom').each(function(e) {
                        var current = $.trim($(this).data('name')).toLowerCase();;
                        var search_str = $.trim($("#search_carrier").val()).toLowerCase();;
                        if (current.indexOf(search_str) >= 0) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });

                var zindex = 10;

                $(".toggle-info").click(function(e) {
                    e.preventDefault();

                    var isShowing = false;

                    if ($(this).closest('.card').hasClass("show")) {
                        isShowing = true
                    }

                    if ($("div.cards").hasClass("showing")) {
                        // a card is already in view
                        $("div.card.show")
                            .removeClass("show");

                        if (isShowing) {
                            // this card was showing - reset the grid
                            $("div.cards")
                                .removeClass("showing");
                        } else {
                            // this card isn't showing - get in with it
                            $(this).closest('.card')
                                .css({
                                    zIndex: zindex
                                })
                                .addClass("show");

                        }

                        zindex++;

                    } else {
                        // no cards in view
                        $("div.cards")
                            .addClass("showing");
                        $(this).closest('.card')
                            .css({
                                zIndex: zindex
                            })
                            .addClass("show");

                        zindex++;
                    }

                });
                $('#billing_currency').on('select2:select', function (e) {
                    // Do something
                    addCurrencyTitle();
                });
            });
            function  addCurrencyTitle() {
                var billing_currency = $('#billing_currency').val();
                console.log(billing_currency);
                $('#show_currency_id').html("");
                $('#show_currency_id').html(billing_currency);
            }
        </script>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
