<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../includes/library/vendor/autoload.php");
include_classes([
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',

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

    'consignment.class',
    'carrier.class',
    'carrierfilter.class',
    'useraccount.class',
    'useraccountfilter.class',
    'user.class',
    'userfilter.class',
    'services.class',
    'servicefilter.class',
    'country.class',
    'countryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'quotationdetails.class',
    'quotationdetailsfilter.class',
    'currency.class',
    'currencyfilter.class',
    'quotationpaymenthistory.class',
    'quotationpaymenthistoryfilter.class',
    'consignmentvalidator.class',
    'tariffs.class']);

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

    protected function init()
    {
        $data = $_POST;
        $string = '<pre>'.print_r($data, true).'</pre>';
        $file = 'barclays.html';
        file_put_contents($file,"update log in barclay inp request \n".$string.date('Y-m-d H:i:s')."update log in barclay inp request \n".PHP_EOL, FILE_APPEND);
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

            $emailOrder =   Order::with(['user', 'consignments' => function($query) {
                $query->withCount('consignmentItems');
            }])->withCount('orderItems')->orderby('id', 'DESC')->findOrFail($order_id);
            $passData = [
                'order' => $emailOrder
            ];
            $user = User::where('id',$order->user_id)->toSql();
            file_put_contents($file,"update log in barclay inp request \n".$user.date('Y-m-d H:i:s')."sql print \n".PHP_EOL, FILE_APPEND);
            Mail('irshadali18@gmail.com,najam@stellartech.co','Test Barclay Ipn', 'Step 1');
            $userAddress = UserAddress::where('is_default',1)->where('user_id',$order->user_id)->first();
            Mail('irshadali18@gmail.com,najam@stellartech.co','Test Barclay Ipn', 'Step 2');
            if(empty($userAddress)) {
                $userAddress = UserAddress::where('user_id',$order->user_id)->first();
                Mail('irshadali18@gmail.com,najam@stellartech.co','Test Barclay Ipn', 'Step 4');
            }
            Mail('irshadali18@gmail.com,najam@stellartech.co','Test Barclay Ipn', 'Step 5');
            $userEmail = !empty($user->email) ? $user->email : (!empty($userAddress->email) ? $userAddress->email : "");
            Mail('irshadali18@gmail.com,najam@stellartech.co','Test Barclay Ipn', 'Step 6');
            $email_body = (string) View::make('email.orderTemplate', $passData);
            $this->email->setToEmail($userEmail);
            $this->email->setCCEmail("itsupport@oneworldexpress.com");
            $this->email->sendEmail("New Order Placed", $email_body);
            Mail('irshadali18@gmail.com,najam@stellartech.co','Test Barclay Ipn', 'Step 7');
            /* End Send Order Email */
            $message = "success";
        }
        Mail('irshadali18@gmail.com,najam@stellartech.co','Test Barclay Ipn', 'Step 8');
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

        <script type="text/javascript">
            $(document).ready(function () {

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

        <?php
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

        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_SIMPLE_TEMPLATE);
$page->show();
?>