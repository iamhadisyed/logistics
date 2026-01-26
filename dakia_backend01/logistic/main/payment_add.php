<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Warehouse Add/Update page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    private $error_msg = "";
    private $payment_methods = "";
    private $currency_list = "";
    private $paymentmethod;
    private $amount;
    private $currencyid;
    private $id = 0;

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <script>

            $(document).ready(function () {
                $('input[title]').tooltip({placement: 'bottom'});
            })
        </script>
        <?php
        //echo "<pre>"; print_r($_SESSION['user_type']); echo "</pre>";
        ?>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-cogs"></i>Add Balance / Top-up Account </div>
                <div class="tools"> <a href="javascript:;" class="collapse"> </a><a href="" class="fullscreen" data-original-title="" title="">
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <?php
                if ($this->error_msg != "") {
                    ?>
                    <div class="note note-success"><?php echo $this->error_msg ?></div>
                    <?php
                }

                $sessionUser = SessionManager::getUser();
                //echo "<pre>"; print_r($sessionUser); echo "</pre>";
                if ($sessionUser->getIsPrepaid() == 'YES') {
                    ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Payment Method</label><br />
                                <?php
                                if (count($this->payment_methods) > 0) {
                                    ?>    
                                    <select name="paymentmethod" id="paymentmethod" class="form-control">
                                        <?php
                                        foreach ($this->payment_methods as $paymentMethod) {
                                            ?>
                                            <option value="<?php echo $paymentMethod->getId(); ?>"<?php echo ( $paymentMethod->getId() == $this->paymentmethod ? ' selected="selected"' : ''); ?>><?php echo $paymentMethod->getTitle(); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>   
                                    <?php
                                }
                                ?>
                            </div>
                        </div>    
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Amount</label><br />
                                <input type="number" class="form-control" name="amount" id="amount" value="<?php echo $this->amount; ?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Currency</label><br />
                                <select name="currencyid" id="currencyid" class="form-control">
                                    <?php
                                    if (count($this->currency_list) > 0) {
                                        foreach ($this->currency_list as $currency) {
                                            $selected = '';
                                            if ($this->currencyid != '' && $currency->getId() == $this->currencyid) {
                                                $selected = ' selected="selected"';
                                            } else if ($currency->getRightsymbol() == $sessionUser->getBillingCurrency()) {
                                                $selected = ' selected="selected"';
                                            }

                                            echo '<option value="' . $currency->getId() . '"' . $selected . '>' . $currency->getCurrencyname() . ' [' . $currency->getRightsymbol() . ']</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" name="btn_add_balance" id="btn_add_balance" class="btn btn-primary">Add Balance</button>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="note note-danger">
                        You do not have access to this page.
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }

    public function renderHead() {
        ?>

        <link rel="stylesheet" type="text/css" href="../_assets/global/plugins/select2/select2.css"/>
        <script type="text/javascript" src="../_assets/global/plugins/select2/select2.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#currencyid").select2();
            });

        </script>
        <?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {

        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }

        $paymentMethodFilter = new PaymentMethodFilter();
        $paymentMethodFilter->addIsActive(1);
        $this->payment_methods = $paymentMethodFilter->getColumnList('title');

        $currencyFilter = new CurrencyFilter();
        $currencyFilter->addIsactiveFilter("1");
        $this->currency_list = $currencyFilter->getList();
//        if($_SESSION['user_type'] == 'warehouse' && $this->id  == 0){
//            util_redirect("racks.php");
//        }

        /* ------------------------------------------------------------------------------ */
        // process form
        if (isset($_POST['btn_add_balance'])) {
            $this->paymentmethod = $_POST['paymentmethod'];
            $this->amount = $_POST['amount'];
            $this->currencyid = $_POST['currencyid'];
            if ($this->validate_form()) {

                $topup_amount = $this->amount;

                $sessionUser = SessionManager::getUser();
                $user_currency = $sessionUser->getBillingCurrency();

                $userCurrencyFilter = new CurrencyFilter();
                $userCurrencyFilter->addFieldFilter('rightsymbol', $user_currency);
                $userCurrencyObj = $userCurrencyFilter->getColumnList('currencyid');
                //echo "<pre>"; print_r($userCurrencyObj); echo "</pre>"; exit;
                $user_currencyid = $userCurrencyObj[0]->getCurrencyid();

                $currencyFilter = new CurrencyFilter();
                $currencyFilter->addFieldFilter('id', $this->currencyid);
                $currencyObj = $currencyFilter->getColumnList('rightsymbol');
                $amount_currency = $currencyObj[0]->getRightsymbol();

                if ($user_currency != $amount_currency)
                    $topup_amount = Currency::convertCurrency($amount_currency, $user_currency, $this->amount);

                $iscompleted = ($this->paymentmethod == '2' ? 1 : 0);

                $user_id = $sessionUser->getId(); //(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');

                $fieldsVal = array('amount' => number_format($this->amount, 2),
                    'amount_currency_id' => $this->currencyid,
                    'dr' => number_format($topup_amount, 2),
                    'cr' => 0,
                    'currency_id' => $user_currencyid,
                    'paymentmethod_id' => $this->paymentmethod,
                    'paymentdate' => date("Y-m-d H:i:s"),
                    'iscompleted' => $iscompleted,
                    'customer_id' => $user_id,
                    'Paymentdetail' => 'Topup Account',
                    'added_by' => $user_id,
                    'added_date' => date("Y-m-d H:i:s")
                );
                $PaymentObj = new Payment($fieldsVal);
                $PaymentObj->save(true);
                $paymentId = $PaymentObj->getId();
                if ($this->paymentmethod == 1) {
                    $domain = (PAYPAL_LIVE) ? "www.paypal.com" : "www.sandbox.paypal.com";
                    echo '<form action="https://' . $domain . '/cgi-bin/webscr" method="post"  id="PPForm" name="PPForm">
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="business" value="' . PAYPAL_EMAIL_ADDRESS . '">
                        <input type="hidden" name="lc" value="GB">
                        <input type="hidden" name="item_name" value="One World Express Payment">
                        <input type="hidden" name="item_number" value="' . $paymentId . '"> 
                        <input type="hidden" name="amount" value="' . $this->amount . '">
                        <input type="hidden" name="currency_code" value="' . $amount_currency . '">
                        <input type="hidden" name="return" value="' . SETTING_MAIN_URL . 'main/payment_success.php">
                        <input type="hidden" name="cancel_return" value="' . SETTING_MAIN_URL . 'main/payment_cancel.php">
                        <input type="hidden" name="notify_url" value="' . SETTING_MAIN_URL . 'main/process_paypal_payment.php">
                        <input type="hidden" name="invoice" value="' . $paymentId . '"/>
                        <input type="hidden" name="custom" value="' . md5($paymentId . $this->amount . $amount_currency . PAYPAL_EMAIL_ADDRESS . ORDER_SALT) . '"/>
                        <input type="hidden" name="button_subtype" value="services">
                        <input type="hidden" name="no_note" value="0">
                        <input type="hidden" name="tax_rate" value="0.000">
                        <input type="hidden" name="shipping" value="0.00">
                    </form>
                    <script type="text/javascript">
                        document.getElementById("PPForm").submit();
                    </script>';
                }
            }
        }

        /* ------------------------------------------------------------------------------ */
        $this->setTitle("Admin - Top Up Account");
    }

    /**
     * Returns boolean to indicate if the form is valid
     *
     */
    private function validate_form() {
        // Check that the warehouse name is not blank
        if (trim($this->paymentmethod) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Select Payment Method.";
        }
        // Check the address line 1
        if (trim($this->amount) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Enter Amount.";
        }
        if (trim($this->currencyid) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Select Currency.";
        }
        return ($this->error_msg == "");
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
