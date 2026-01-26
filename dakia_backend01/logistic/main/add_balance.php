<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../includes/library/vendor/autoload.php");
include_classes([  
                    'currency.class',
                    'currencyfilter.class',
                    'paymentshistory.class',
                    'paymentshistoryfilter.class',
                        ]);
class Page extends BasePage {

    public $user;
    private $msg;
    private $account;
    private $apiContext;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Add Balance"
        );
        $this->user = SessionManager::getUser();
        $this->account = new CustomerAccount($this->user->getUserAccountId());
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'add_balance') {
            $account_id = $this->form_vars['account_id'];
            $amount = $this->form_vars['amount'];
            $currency = $this->form_vars['currency'];
            $pay_by = $this->form_vars['pay_by'];
            $bank_ref_number = $this->form_vars['bank_ref_number'];
            $cheque_number = $this->form_vars['cheque_number'];
            $userAccountObj = new CustomerAccount($account_id);
            if($userAccountObj->getIsPrepaid() == 1) {
                $paymentDetail = "Recharge";
                $paymentStatus = "";
                $isCompleted = "";
                $credit = 0.00;
                $debit = 0.00;
                $userCurrencySymbol = "GBP";
                $userCurrencyId = 2;
                if($amount > 0) {
                    if(!empty($userAccountObj->getBillingCurrency())) {
                        $toCurrencyFilter = new CurrencyFilter();
                        $toCurrencyFilter->addFieldFilter("     rightsymbol", $userAccountObj->getBillingCurrency());
                        $toCurrencyObj = $toCurrencyFilter->getList();
                        if(count($toCurrencyObj)) {
                            $userCurrencySymbol = $toCurrencyObj[0]->getRightsymbol();
                            $userCurrencyId = $toCurrencyObj[0]->getId();
                        }
                    }
                    $fromCurrencyObj = new Currency($currency);
                    $fromCurrency = $fromCurrencyObj->getRightsymbol();
                    $toCurrency = $userCurrencySymbol;
                    $amountToConvert = $amount;
                    $credit = Currency::convertCurrency($fromCurrency, $toCurrency, $amountToConvert);
                }
                if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                    $paymentStatus = "completed";
                    $isCompleted = "yes";
                } else if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
                    $paymentStatus = "pending";
                    $isCompleted = "no";
                } else if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                    $paymentStatus = "pending";
                    $isCompleted = "no";
                }
                $userChildAccountArry = CustomerAccount::accountImmediateChild($this->user->getUserAccountId());
                if(in_array($account_id, $userChildAccountArry)) {
                    $paymentStatus = "completed";
                    $isCompleted = "yes";
                }
                if(($account_id == $this->user->getUserAccountId()) && ($this->user->getUserType() != User::USER_TYPE_ADMIN)) {
                    $paymentStatus = "pending";
                    $isCompleted = "no";
                }
                $date_added = time();
                $added_by = $this->user->getId();
                $date_update = time();
                $update_by = $this->user->getId();
                $paymenthistoryObj = new PaymentsHistory();
                if($pay_by == "bank_transfer") {
                    $paymenthistoryObj->setBillingId($bank_ref_number);
                } else if($pay_by == "cheque") {
                    $paymenthistoryObj->setBillingId($cheque_number);
                }
                $paymenthistoryObj->setAccountId($account_id);
                $paymenthistoryObj->setAmount($amount);
                $paymenthistoryObj->setAmountCurrencyId($currency);
                $paymenthistoryObj->setPaymentMethod($pay_by);
                $paymenthistoryObj->setPaymentDetail($paymentDetail);
                $paymenthistoryObj->setUserCurrencyId($userCurrencyId);
                $paymenthistoryObj->setDebit($debit);
                $paymenthistoryObj->setCredit($credit);
                $paymenthistoryObj->setPaymentStatus($paymentStatus);
                $paymenthistoryObj->setIsCompleted($isCompleted);
                $paymenthistoryObj->setDateAdded($date_added);
                $paymenthistoryObj->setAddedBy($added_by);
                $paymenthistoryObj->setDateUpdated($date_update);
                $paymenthistoryObj->setUpdatedBy($update_by);
                $paymenthistoryObj->save();
				/* update account balance */
				CustomerAccount::updateBalance($account_id);
				/******************************/
                $this->msg = 'Balance has been added successfully.';
                $this->flashMsg->success($this->msg);
            } else {
                $this->msg = 'You can only add balance in prepaid account.';
                $this->flashMsg->error($this->msg);
            }
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_account_detail') {
            $output = [];
            $accountId = $this->form_vars['account_id'];
            $accountObj = new CustomerAccount($accountId);
            if(count($accountObj) > 0) {
                $output['paypalEmail'] = $accountObj->getPaypalEmail();
                $output['paypalCurrency'] = $accountObj->getPaypalCurrency();
            }
            echo json_encode($output);
            die();
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'paypal_create_payment') {
            $output = [];
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $return_url = $actual_link."/paypal_success.php";
            $cancel_url = $actual_link."/paypal_cancel.php";

            $accountId = $this->form_vars['account'];
            $amount_add = $this->form_vars['amount'];
            $currencyId = $this->form_vars['currency'];
            $userAccountObj = new CustomerAccount($accountId);
            if($userAccountObj->getIsPrepaid() == 1) {
                $userParentAccountObj = new CustomerAccount($userAccountObj->getParentid());
                if(!empty($userParentAccountObj->getPaypalClientId()) && !empty($userParentAccountObj->getPaypalClientSecret())) {
                    $currency = new Currency($currencyId);
                    $userCurrencyId = 2;
                    $userCurrencySymbol = "GBP";
                    if(!empty($userAccountObj->getBillingCurrency())) {
                        $toCurrencyFilter = new CurrencyFilter();
                        $toCurrencyFilter->addFieldFilter("     rightsymbol", $userAccountObj->getBillingCurrency());
                        $toCurrencyObj = $toCurrencyFilter->getList();
                        if(count($toCurrencyObj)) {
                            $userCurrencySymbol = $toCurrencyObj[0]->getRightsymbol();
                            $userCurrencyId = $toCurrencyObj[0]->getId();
                        }
                    }
                    $fromCurrency = $currency->getRightsymbol();
                    $toCurrency = $userCurrencySymbol;
                    $amountToConvert = $amount_add;
                    $credit = Currency::convertCurrency($fromCurrency, $toCurrency, $amountToConvert);
                    /* Save payment in DB */
                    $isCompleted = "no";
                    $paymentDetail = "Recharge";
                    $paymentStatus = "pending";
                    $pay_by = "paypal";
                    $date_added = time();
                    $added_by = $this->user->getId();
                    $date_update = time();
                    $update_by = $this->user->getId();
                    $paymentHistory = new PaymentsHistory();
                    $paymentHistory->setAccountId($accountId);
                    $paymentHistory->setAmount($amount_add);
                    $paymentHistory->setAmountCurrencyId($currencyId);
                    $paymentHistory->setUserCurrencyId($userCurrencyId);
                    $paymentHistory->setCredit($credit);
                    $paymentHistory->setDebit('0.00');
                    $paymentHistory->setPaymentMethod($pay_by);
                    $paymentHistory->setPaymentDetail($paymentDetail);
                    $paymentHistory->setPaymentStatus($paymentStatus);
                    $paymentHistory->setIsCompleted($isCompleted);
                    $paymentHistory->setDateAdded($date_added);
                    $paymentHistory->setAddedBy($added_by);
                    $paymentHistory->setDateUpdated($date_update);
                    $paymentHistory->setUpdatedBy($update_by);
                    $paymentHistory->save();
                    /* create payment paypal */
                    $payer = new PayPal\Api\Payer();
                    $payer->setPaymentMethod('paypal');

                    $amount = new PayPal\Api\Amount();
                    $amount->setTotal($amount_add);
                    $amount->setCurrency($currency->getRightsymbol());

                    $transaction = new PayPal\Api\Transaction();
                    $transaction->setAmount($amount);
                    $transaction->setDescription("Add Balance");
                    $transaction->setCustom($paymentHistory->getId());

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
                    $paymentHistoryUpdate = new PaymentsHistory($paymentHistory->getId());
                    $paymentHistoryUpdate->setPaypalPaymentId($paypal_payment_id);
                    $paymentHistoryUpdate->save();

                    echo $paymentData;
                    die;
                } else {
                    $output['status'] = "error";
                    $output['message'] = "Sorry paypal crenditional is not set in account";
                    echo json_encode($output);
                    die;
                }
            } else {
                $output['status'] = "error";
                $output['message'] = "You can only add balance in prepaid account.";
                echo json_encode($output);
                die;
            }
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'paypal_execute_payment') {
            $account_id = $this->form_vars['account_id'];
            $this->getPaypalApiContext($account_id);
            $paymentId = $this->form_vars['payment_id'];
            $payerId = $this->form_vars['payer_id'];
            $payment = PayPal\Api\Payment::get($paymentId, $this->apiContext);
            // Execute payment with payer ID
            $execution = new PayPal\Api\PaymentExecution();
            $execution->setPayerId($payerId);
            $result = $payment->execute($execution, $this->apiContext);
            // Log lAst transaction
            $results = print_r($result, true);
            file_put_contents('paypal.txt', $results);

            /* Update payment success */
            $state = $result->state;
            $status = $result->payer->status;
            $payment_history_id = "";
            if($state == "approved" && ($status == "VERIFIED" ||  $status == "UNVERIFIED")) {
                $paypal_payment_id = $result->id;
                $payment_history_id = $result->transactions[0]->custom;
                $transaction_id = $result->transactions[0]->related_resources[0]->sale->id;
                $payer_email = $result->payer->payer_info->email;
                $payment_status = $result->transactions[0]->related_resources[0]->sale->state;
                $amount = $result->transactions[0]->related_resources[0]->sale->amount->total;
                $paymentHistoryObj = new PaymentsHistory($payment_history_id);
                /* update payment history */
                $paymentHistoryUpdate = new PaymentsHistory($payment_history_id);
                $paymentHistoryUpdate->setTxnId($transaction_id);
                $paymentHistoryUpdate->setSenderEmail($payer_email);
                $paymentHistoryUpdate->setPaymentStatus($payment_status);
                if(($payment_status == "completed") && ($amount == $paymentHistoryObj->getAmount()) && ($paypal_payment_id == $paymentHistoryObj->getPaypalPaymentId())) {
                    $paymentHistoryUpdate->setIsCompleted("yes");
                }
                $paymentHistoryUpdate->save();
				/* update account balance */
				CustomerAccount::updateBalance($paymentHistoryObj->getAccountId());
				/******************************/
            }
            /* Update payment success */
            echo $payment_history_id;
            die;
        }
    }
    
    public function getPaypalApiContext($account_id) {
        $userAccountObj = new CustomerAccount($account_id);
        $userParentAccountObj = new CustomerAccount($userAccountObj->getParentid());
        $this->apiContext = new PayPal\Rest\ApiContext(
            new PayPal\Auth\OAuthTokenCredential(
                $userParentAccountObj->getPaypalClientId(),     // ClientID
                $userParentAccountObj->getPaypalClientSecret()      // ClientSecret
            )
        );
        $mode = 'SANDBOX';
        if(PAYPAL_LIVE) {
            $mode = 'LIVE';
        }
        $this->apiContext->setConfig(
            array(
                'mode' => $mode
            )
        );
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>

        <script src="https://www.paypalobjects.com/api/checkout.js" data-version-4></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                change_pay_by();
                var account = "";
                paypal.Button.render({
                    style: {
                        size: 'medium',
                        color: 'blue',
                        shape: 'rect',
                        label: 'checkout',
                        tagline: false
                    },
                    env: '<?php echo ((PAYPAL_LIVE) ? "production" : "sandbox") ?>', // Or 'production' ,'sandbox'
                    // Set up the payment:
                    // 1. Add a payment callback
                    payment: function(data, actions) {
                      // 2. Make a request to your server
                      account = $('#account_id').val();
                      var amount = $('#amount').val();
                      var currency = $('#currency').val();
                      return actions.request.post('/add_balance.php', { 
                        account: account,
                        amount: amount,
                        currency: currency,
                        action: "paypal_create_payment"
                      }).then(function(res) {
                          // 3. Return res.id from the response
                          if(res.id) {
                            return res.id;
                          } if(res.status == "error") {
                                swal("Sorry!", res.message, "error");
                          } else {
                                swal("Sorry!", "Please refresh you page and try again if error persist please contact to info@oneworldexpress.com  ", "error");
                          }
                        });
                    },
                    // Execute the payment:
                    // 1. Add an onAuthorize callback
                    onAuthorize: function(data, actions) {
                      // 2. Make a request to your server
                      return actions.request.post('/add_balance.php', {
                        payment_id: data.paymentID,
                        payer_id:   data.payerID,
                        account_id:   account,
                        action: "paypal_execute_payment"
                      }).then(function(res) {
                          // 3. Show the buyer a confirmation message.
                          if(res > 0) {
                              $('.success_msg').html("<p>payment added successfully</p>");
                              $('#res_message').show();
                          } else {
                              alert("Please refresh you page and try again if error persist please contact to info@oneworldexpress.com  ");
                          }
                        });
                    }
                }, '#paypal_btn');
            });
            $(document).on('click', '#btnSave', function () {
                var validation = 1;
                if(!$('#account_id').val()) {
                    $('#account_id').parents(".input-group").css('border', '1px solid red');
                    validation = 0;
                } else {
                    $('#account_id').parents(".input-group").css('border', '0px');
                }
                if(!$('#amount').val()) {
                    $('#amount').parents(".input-group").css('border', '1px solid red');
                    validation = 0;
                } else {
                    $('#amount').parents(".input-group").css('border', '0px');
                }
                if (validation) {
                    $('#add_balance').submit();
                } else {
                    swal("Sorry!", "Error is high lighted with red border", "error");
                }
            });
            function change_pay_by() {
                $('#btnSave').show();
                $('#paypal_btn').hide();
                $('.pay_by_link_box').hide();
                $('#billing_currency').val('');
                $('#billing_currency').select2();
                var select_pay_by = $('#pay_by').val();
                if (select_pay_by == "cheque") {
                    $('#cheque_box').show();
                } else if (select_pay_by == "bank_transfer") {
                    $('#bank_ref_no_box').show();
                } else if (select_pay_by == "paypal") {
                    $('#btnSave').hide();
                    $('#paypal_btn').show();
                }
            }
            function get_user_account_details(obj) {
                var login_user_account_id = <?php echo $this->user->getUserAccountId() ?>;
                var form_data = new FormData();
                var account_id = obj.value;
                form_data.append('account_id', account_id);
                form_data.append('action', 'get_account_detail');
                $.ajax({
                        url: "add_balance.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            $('#user_paypal_currency').val(data.paypalCurrency);
                            $('#business').val(data.paypalEmail);
                            if(login_user_account_id == account_id) {
                                $('#pay_by option[value="cash"]').detach();
                                $('#pay_by option[value="bonus"]').detach();
                                // Set the value, creating a new option if necessary
                                if (!$('#pay_by').find("option[value='paypal']").length) { 
                                    // Create a DOM Option and pre-select by default
                                    var newOption = new Option("Paypal", "paypal", true, true);
                                    // Append it to the select
                                    $('#pay_by').append(newOption).trigger('change');
                                }
                            } else {
                                // Set the value, creating a new option if necessary
                                if (!$('#pay_by').find("option[value='cash']").length) { 
                                    // Create a DOM Option and pre-select by default
                                    var newOption = new Option("Cash", "cash", true, true);
                                    // Append it to the select
                                    $('#pay_by').append(newOption).trigger('change');
                                } 
                                // Set the value, creating a new option if necessary
                                if (!$('#pay_by').find("option[value='bonus']").length) { 
                                    // Create a DOM Option and pre-select by default
                                    var newOption = new Option("Bonus", "bonus", true, true);
                                    // Append it to the select
                                    $('#pay_by').append(newOption).trigger('change');
                                }
                                $('#pay_by option[value="paypal"]').detach();
                            }
                            change_pay_by();
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                });
            }
        </script>
        <?php
    }

    protected function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-money"></i>
                    Add Balance
                </div>
                <div class="actions">
        <!--                    <a id="csv_download_btn" class="btn btn-sm blue"><span></span><i class="fa fa-download"></i>&nbsp;<?php //echo Translation::GetCaption("DOWNLOAD_CSV");    ?></a>-->
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                </div>
                <div class="row display-none" id="res_message">
                    <div class="col-md-12">
                        <div class="alert alert-success success_msg"></div>
                    </div>
                </div>
                <form name="add_balance" id="add_balance" action="add_balance.php" method="post">
                    <div class="caption margin-bottom-10 block">
                        Balance Information
                    </div>
                    <div class="row">
                        <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                
                               <div class="has-float-label">
                                   <div class="first_form_col">
                                    <?php
                                        $accountParentId = $this->user->getUserAccountId();
                                        $includeParent = true;
                                        $selectedAccount = "";
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        echo Ddl::showTreeDropdown('account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'","is_prepaid = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control validate_check" data-live-search="true" onchange="get_user_account_details(this)" ', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel);
                                    ?>
                                    <label>User Account</label>

                                </div>
                            </div>
                        </div>
                        </div>
                        <?php } else if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                        <div class="col-md-4">

                             <div class="form-group">

                            <?php
                                $userAccountFilter = new UserAccountFilter();
                                $userAccountFilter->addFieldFilter("        ua.parentid", $this->user->getUserAccountId());
                                $userAccountFilter->addFieldFilter("        ua.is_prepaid", 1);
                                $userAccountFilter->addOrFilter("       (ua.id = '" . $this->user->getUserAccountId() . "' AND ua.is_prepaid = '1')");
                                $userAccountFilterObjs = $userAccountFilter->getList();
                                $accountArray = [];
                                if(count($userAccountFilterObjs) > 0) {
                                    foreach ($userAccountFilterObjs as $userAccountFilterObj) {
                                        $accountArray[$userAccountFilterObj->getId()] = $userAccountFilterObj->getUserAccount();
                                    }
                                }
                            ?>
                           
                               
                               <div class="has-float-label input-icon right">
                                   
                                 <div class="first_form_col">
                                    <?php echo Ddl::generateArrayDDL('account_id', $accountArray, '', '', 'class="form-filter bs-select form-control validate_check" onchange="get_user_account_details(this) ', "", $dd_id = 'account_id'); ?>
                                     <label>User Account</label>


                                </div>
                            </div>
                        </div>
                        </div>
                        <?php } else if ($this->user->getUserType() == User::USER_TYPE_CLIENT) { ?>
                        <input type="hidden" name="account_id" id="account_id" value="<?php echo $this->user->getUserAccountId(); ?>" >
                        <?php } ?>
                         <div class="col-md-4">
                            <div class="form-group">
                               <div class="has-float-label input-icon right">
                                   <i class="fa fa-shopping-cart"></i>
                                    <input type="number" name="amount" id="amount" class="form-control validate_check" placeholder="Enter Amount" value="" />
                                     <label for="amount">Amount</label>


                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                
                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-money"></i>
                                    <?php
                                    echo Ddl::generateDDL('currency', 'CurrencyFilter', ' AND isactive = 1 ', 'rightsymbol', 'id', '', ' class="form-control select2"  data-toggle="tooltip" data-placement="top" title="Billing Currency" data-original-title="Billing Currency" ', 'Select Currency', '', 'currency', 'Billing Currency');
                                    ?>
                                    <label> Currency</label>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                              
                                  <div class="has-float-label input-icon right">
                                     <i class="fa fa-shopping-cart"></i>
                                    <?php
                                    if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
                                        $pay_by_array = array("paypal" => "Paypal", "bank_transfer" => "Bank Transfer", "cheque" => "Cheque");
                                    } else {
                                        $pay_by_array = array("cash" => "Cash", "paypal" => "Paypal", "bank_transfer" => "Bank Transfer", "cheque" => "By Cheque", "bonus" => "Bonus");
                                    }
                                    echo Ddl::generateArrayDDL('pay_by', $pay_by_array, "", "", ' class="form-control select2" onchange="change_pay_by()" ', 'Select Pay by', 'pay_by', 'Select Pay by', '');
                                    ?>
                                      <label>Pay By</label>


                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pay_by_link_box" id="cheque_box">
                            <div class="form-group">
                                
                               <div class="has-float-label input-icon right">
                                   <i class="fa fa-shopping-cart"></i>
                                    <input type="text" name="cheque_number" id="cheque_number" class="form-control" value="" />
                                    <label>Cheque#</label>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pay_by_link_box" id="bank_ref_no_box">
                            <div class="form-group">
                              
                                <div class="has-float-label input-icon right">
                                     <i class="fa fa-shopping-cart"></i>
                                    <input type="text" name="bank_ref_number" id="bank_ref_number" class="form-control" value="" />
                                      <label>Ref No</label>
                                </div>
                            </div>
                        </div>
                    </div>                   
                    <div class="row margin-top-20">
                        <div class="col-md-12 text-right">
                            <button type="button" id="paypal_btn" class="btn btn-primary"></button>
                            <input type="hidden" name="add_balance_id" value="" />
                            <input type="hidden" name="action" value="add_balance" />
                            <button type="button" name="btnSave" id="btnSave" class="btn btn-primary btn_save">Add</button>
                            <a href="customers.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>   
                </form>      
            </div>
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }
    
     public function renderHead() {
        ?>
        <style type="text/css">
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                /* display: none; <- Crashes Chrome on hover */
                -webkit-appearance: none;
                margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
            }
            #paypal_btn{
                padding: 0px;
                display: none;
            }
        </style>
        <?php
    }

}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
