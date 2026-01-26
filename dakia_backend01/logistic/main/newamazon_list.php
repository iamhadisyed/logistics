<?php
// get settings

require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

// consignment filter
    private $consignment_filter;
// table description
    private $table_msg;
    private $num_valid; // number of valid consignments.
    private $column_name;
    private $sortby;
    private $flag;
    private $errorCodes = '';
    private $notification;
    public $auth_data = false;
    public $platform_id = false;

    /*     * *
     * Controller logic
     */

    protected function init() {
// user must be CLIENT
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
        $user = SessionManager::getUser();
        if (!isset($_GET['id']) || $_GET['id'] == "") {
            util_redirect("../main/index.php");
        }
        $this->platform_id = $_GET['id'];
        if (isset($_POST['authBtn']) && $_POST['authBtn'] != "") {
            $sessionUser = SessionManager::getUser();
            $newUserId = $sessionUser->getId();
            // Set User Shopping Platforms
            if (!empty($newUserId) && $newUserId > 0) {
                UserMarketPlacesMapping::deleteSpecificUserMarketPlacesMappingList($newUserId, $this->platform_id);
            }
            $shopping_plateform = $this->form_vars["shopping_plateform"];
            if (!empty($shopping_plateform)) {
                foreach ($shopping_plateform as $platform_id => $plateform) {
                    $userPlatform = new UserMarketPlacesMapping();
                    $userPlatform->setMarketPlacesId($platform_id);
                    $userPlatform->setUserId($sessionUser->getId());
                    $userPlatform->setAuthData(json_encode($plateform));
                    $userPlatform->save();
                }
            }
        }


        $UserShoppingPlatformMappingFilter = new UserMarketPlacesMappingFilter();
        $UserShoppingPlatformMappingFilter->addUserIdFilter($user->getId());
        $UserShoppingPlatformMappingFilter->addMarketPlacesMappingIdMd5Filter($this->platform_id);
        $UserData = $UserShoppingPlatformMappingFilter->getList();
        if (count($UserData) > 0) {
            $UserData = $UserData[0];
            $authDataArray = (array) json_decode($UserData->getAuthData());
            foreach ($authDataArray as $key => $value) {
                if (empty($value)) {
                    $this->auth_data = true;
                    break;
                }
            }
        } else {
            util_redirect("../main/index.php");
            exit();
        }
        if (isset($_SESSION["orders"])) {
            $consignment_array = $_SESSION["orders"];

            $arr = array();
        }

// FORM POSTED BACK?

        if (isset($this->form_vars["form_action"])) {
// take appropriate action
//$this->form_vars["form_action"];



            switch ($this->form_vars["form_action"]) {
// SHOW INVALID
// - filter to [NEW &] INVALID entries only
// - entries should be validated on import - shouldn't really see any NEW entries
                case "GETAMAZONORDERS":
                    unset($_SESSION['detail']);
                    $platform_id = trim($this->platform_id);
//    $platform_id = md5($platform);
                    //GET user platforms
                    $UserShoppingPlatformMappingFilter = new UserMarketPlacesMappingFilter();
                    $UserShoppingPlatformMappingFilter->addMarketPlacesMappingIdMd5Filter($platform_id);
                    $UserShoppingPlatformMappingFilter->addUserIdFilter($user->getId());
                    $UserData = $UserShoppingPlatformMappingFilter->getList();
                    $UserData = $UserData[0];
                    $authDataArray = (array) json_decode($UserData->getAuthData());
                    foreach ($authDataArray as $key => $value) {
                        if (empty($value)) {
                            $this->auth_data = true;
                            break;
                        } else {
                            $_SESSION['detail'][$key] = $value;
                        }
                    }
                    require_once("amazon.php");

//echo "orders found";
                    $_SESSION['filter'] = $_SESSION["orders"];
//echo "<pre>"; print_r($_SESSION["orders"]); echo "</pre>";
//die;
                    util_redirect("../main/newamazon_list.php?id=" . $this->platform_id);
                    break;

                case "search":
                    unset($_SESSION["filter"]);
                    /* Getting values from drop down */
                    $status_dropdown = $_POST['pend_ship_unship']; //values Shipped, unshipped, pending
                    $country_dropdown = $_POST['sortCountry'];    // values GB and non_GB
                    $value_dropdown = $_POST['lessOrGreater'];    //values LessFifteen and GreaterFifteen					


                    foreach ($consignment_array as $ct_idx => $consignment) {
                        $status = @$consignment["OrderStatus"];
                        $country = @$consignment["CountryCode"];
                        $amount = @$consignment["Amount"];
                        /*
                          if country_dropdown value is non Uk then change $country value to non_GB
                          $country == $country_dropdown
                         */
                        if ($country != 'GB') {
                            $country = 'non_GB';
                        }

                        if ($value_dropdown == 'LessFifteen') {
                            if (($status == $status_dropdown || $status_dropdown == 'Select a Status') && ($country == $country_dropdown || $country_dropdown == 'Select a Country') && $amount < 15) {
                                $arr[] = $consignment;
                            }
                        } else
                        if ($value_dropdown == 'GreaterFifteen') {
                            if (($status == $status_dropdown || $status_dropdown == 'Select a Status') && ($country == $country_dropdown || $country_dropdown == 'Select a Country') && $amount >= 15) {
                                $arr[] = $consignment;
                            }
                        } else
                        if ($value_dropdown == 'Select a Value') {
                            if (($status == $status_dropdown || $status_dropdown == 'Select a Status') && ($country == $country_dropdown || $country_dropdown == 'Select a Country')) {
                                $arr[] = $consignment;
                            }
                        }
                    }
                    $_SESSION["filter"] = $arr;

                    if (count($arr) <= 0) {
                        $this->notification = "No Shipment is shipped";
                    }
                    break;

                /* Order ID search */
                case "btnAmazonOrdersSearch":
                    unset($_SESSION["filter"]);
//echo "alpan";
//die;

                    $_SESSION["order_id"] = $_POST["order_id_textbox"];
                    require_once("amazon_search.php");
                    util_redirect("../main/newamazon_list.php?id=" . $this->platform_id);

//$_SESSION["filter"] = $_SESSION["orders"];
//$consignment_array =  $_SESSION["filter"];
                    break;

                /* case "Unshipped":
                  unset($_SESSION["filter"]);
                  foreach ($consignment_array as $ct_idx => $consignment)
                  {
                  $status			= @$consignment["OrderStatus"];
                  if($status=='Unshipped')
                  {
                  $arr[] = $consignment;
                  }
                  }
                  $_SESSION["filter"] = $arr;
                  $_SESSION["orders"] = $arr;

                  if(count($arr)<=0)
                  {
                  $this->notification = "All Shipments Have been uploaded";
                  }
                  break;

                  case "btncancelled":
                  unset($_SESSION["filter"]);
                  foreach ($consignment_array as $ct_idx => $consignment)
                  {
                  $status			= @$consignment["OrderStatus"];
                  if($status=='Canceled')
                  {
                  $arr[] = $consignment;
                  }
                  }
                  $_SESSION["filter"] = $arr;
                  $_SESSION["orders"] = $arr;
                  break;

                  case "Pending":
                  unset($_SESSION["filter"]);
                  foreach ($consignment_array as $ct_idx => $consignment)
                  {
                  $status			= @$consignment["OrderStatus"];
                  if($status=='Pending')
                  {
                  $arr[] = $consignment;
                  }
                  }
                  $_SESSION["filter"] = $arr;
                  $_SESSION["orders"] = $arr;
                  break;


                  case "LessFifteen":
                  unset($_SESSION["filter"]);

                  foreach ($consignment_array as $ct_idx => $consignment)
                  {

                  $amount			= @$consignment["Amount"];
                  if($amount<=15)
                  {
                  $arr[] = $consignment;
                  }
                  }
                  $_SESSION["filter"] = $arr;
                  $_SESSION["orders"] = $arr;

                  if(count($arr)<=0)
                  {
                  $this->notification = "All Shipment's value is greater than 15";
                  }
                  break;

                  case "GreaterFifteen":
                  unset($_SESSION["filter"]);
                  foreach ($consignment_array as $ct_idx => $consignment)
                  {
                  $amount			= @$consignment["Amount"];
                  if($amount>15)
                  {
                  $arr[] = $consignment;
                  }
                  }
                  $_SESSION["filter"] = $arr;
                  $_SESSION["orders"] = $arr;

                  if(count($arr)<=0)
                  {
                  $this->notification = "All Shipment's value is less than 15";
                  }
                  break;

                  case "uk":
                  unset($_SESSION["filter"]);
                  foreach ($consignment_array as $ct_idx => $consignment)
                  {
                  $country_code	= 	@$consignment["CountryCode"];

                  if($country_code=='GB')
                  {
                  $arr[] = $consignment;
                  }
                  }
                  $_SESSION["filter"] = $arr;
                  $_SESSION["orders"] = $arr;

                  if(count($arr)<=0)
                  {
                  $this->notification = "There is no UK Shipment";
                  }
                  break;

                  case "non_uk":
                  unset($_SESSION["filter"]);
                  foreach ($consignment_array as $ct_idx => $consignment)
                  {
                  $country_code	= 	@$consignment["CountryCode"];
                  if($country_code!='GB')
                  {
                  $arr[] = $consignment;
                  }
                  }
                  $_SESSION["filter"] = $arr;
                  $_SESSION["orders"] = $arr;

                  if(count($arr)<=0)
                  {
                  $this->notification = "No shipment";
                  }
                  break; */

                default:
                    break;
            }  // switch()
        }

// Tool bar
        /* $toolbar = Toolbar::getItem();	
          $toolbar->showPrintOption($this->num_valid > 0);
          $toolbar->showSearchOption();
          $toolbar->showImportOption();
          $toolbar->showLabelList();
          $toolbar->showWarehousePage();
          $toolbar->showAddConsignment();
          $toolbar->showCSVOption();
          $toolbar->showReleaseList();
          $toolbar->showConsignmentList(true);
          $toolbar->showSaveAddress();
          $toolbar->showSearchOption();
          $toolbar->showAddUser();
          $toolbar->showExportOption();
          $toolbar->showEndOfDayOption();
          $toolbar->showManifestList();
          $toolbar->showLabelCreation();
          $toolbar->showUserList();
         */
// common initialisation for ths page
        $this->setTitle("Amazon List");

// Check message
        if ($this->table_msg == "")
            $this->table_msg = "Consignments Listed";
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script type="text/javascript">

            var color_on = '#EEEEEE';
            var color_txt_on = '#000000';
            var color_off = '#FFFFFF';
            var color_txt_off = '#FFFFFF';

            var count = 0;

            function popupwindow(url, title, w, h)
            {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }

            /*
             Generate Labels of all checked orders. Also verify either the user selects the service, weight and quantity.
             After the successful completion of label generation, it merge all the labels
             */

            function BulkLabels()
            {
                var $checkboxes = jQuery('input[type="checkbox"]');
                var temp = new Array();

                jQuery.each($checkboxes, function (i) {
                    if ($(this).is(':checked')) {
                        var value = $(this).attr('value');
                        temp[i] = value;
                    }
                });

                if (temp.length <= 0)
                {
                    alert("Please Check atleast One Checkbox");
                    return false;
                }
                checkboxArray = temp.toString();
                var myarr = checkboxArray.split(",");

                var servicename;
                servicename = document.getElementById("service_name").value;
                bulkWeight = document.getElementById("weight_bulk").value;
                bulkQuantity = document.getElementById("quantity_bulk").value;

                if (servicename == 'Select a Service')
                {
                    alert('Please select a service');
                    return false;
                }

                if (bulkWeight == '')
                {
                    alert('Please Enter a Weight');
                    return false;
                }

                if (bulkQuantity == '')
                {
                    alert('Please Enter a Quantity');
                    return false;
                }


                for (i = 1; i < myarr.length; i++)
                {
                    if (myarr[i] != '')
                    {
                        amazonOrderId = myarr[i];
                        for (var j = 0; j < jasonArrayConsignment.length; j++)
                        {
                            // if check box value (amazon order id) is equal to jason array order id then call bulkLabelsGenerate
                            if (jasonArrayConsignment[j].amazon_order_id == amazonOrderId)
                            {
                                bulkLabelsGenerate(amazonOrderId, amazonOrderId, jasonArrayConsignment[j].name,
                                        jasonArrayConsignment[j].address1, jasonArrayConsignment[j].address2, jasonArrayConsignment[j].address3
                                        , jasonArrayConsignment[j].city, jasonArrayConsignment[j].countryCode, jasonArrayConsignment[j].postcode
                                        , jasonArrayConsignment[j].phone
                                        , jasonArrayConsignment[j].currency, jasonArrayConsignment[j].useraccount, jasonArrayConsignment[j].username
                                        , jasonArrayConsignment[j].userpassword, jasonArrayConsignment[j].status, servicename, bulkWeight, 0, 'title'
                                        , bulkQuantity, jasonArrayConsignment[j].amount)
                            }
                        }
                    }
                }
                mergeLabel();
            }


            //BULK INVOICE
            function BulkInvoice()
            {
                var $checkboxes = jQuery('input[type="checkbox"]');
                var temp = new Array();

                jQuery.each($checkboxes, function (i)
                {
                    if ($(this).is(':checked'))
                    {
                        var value = $(this).attr('value');
                        temp[i] = value;
                    }
                });

                if (temp.length <= 0)
                {
                    alert("Please Check atleast One Checkbox");
                    return false;
                }
                checkboxArray = temp.toString();
                var myarr = checkboxArray.split(",");

                /// loop on checkbox values
                for (i = 0; i < myarr.length; i++)
                {
                    if (myarr[i] != '')
                    {
                        hawb = myarr[i];
                        $.post("../main/ajaxlabel_amazon.php", {hawb: hawb, action: 'GenerateInvoice'}, function (data)
                        {

                        });
                    }
                }
                mergeInvoice();
            }


            // BULK LABEL DISPATCH//
            function dispatchBulkOrders()
            {
                $("#errordiv").empty();
                var $checkboxes = jQuery('input[type="checkbox"]');
                var temp = new Array();
                var hawbNotInOurSystem = new Array();
                i = 0;
                jQuery.each($checkboxes, function (i) {
                    if ($(this).is(':checked')) {
                        var value = $(this).attr('value');
                        temp[i] = value;
                    }
                });

                checkboxArray = temp.toString();
                var myarr = checkboxArray.split(",");
                var index = 0;
                for (i = 1; i < myarr.length; i++)
                {
                    if (myarr[i] != '')
                    {
                        amazonOrderId = myarr[i];
                        dispatchLabel(amazonOrderId, '');
                    }
                }
                //uncheck all checked checkboxes 
                jQuery.each($checkboxes, function (i) {
                    if ($(this).is(':checked')) {
                        $(this).attr('checked', false);
                    }
                });
            }






            function showlogs(consignmentid)
            {
                popupwindow("logs.php?cid=" + consignmentid, 'Logs View', 550, 400);
            }







            // function to create label and save data into smart system . AJAX call to ajaxlabel_amazon

            function showlabels(index, hawb, name, address1, address2, address3, city, country, postcode, phone, currency, useraccount, username, password, status, serviceId, weightId, routingId, title, noOfPiecesId, amount)
            {

                if (typeof ($('#' + routingId)) != "undefined")
                {
                    if ($('#' + routingId).prop('checked'))
                        var routing = '1';
                    else
                    {
                        var routing = '0';
                    }
                } else
                {
                    var routing = '0';
                }

                if (routing == '0')
                {
                    if (typeof ($('#' + serviceId)) != "undefined")
                    {
                        var servicename = $('#' + serviceId).val();
                        if (servicename == '' && routing == '0')
                        {
                            alert("please select service or routing checkbox");
                            return false;
                        }
                    } else
                    {
                        var servicename = '';
                    }
                } else
                {
                    var servicename = '';
                }

                var weightValue = $('#' + weightId).val();
                if (parseInt(weightValue) < 0 || weightValue == '')
                {
                    alert('please enter weight for the order');
                    return false;
                }

                var quantity = $('#' + noOfPiecesId).val();
                if (parseInt(quantity) < 0 || quantity == '')
                {
                    alert('please enter No. of Pieces for the order');
                    return false;
                }

                $.post("../main/ajaxlabel_amazon.php", {index: index, hawb: hawb, name: name, address1: address1, address2: address2,
                    address3: address3, city: city, country: country, postcode: postcode, phone: phone, currency: currency, useraccount: useraccount,
                    username: username, password: password, status: status, weight: weightValue, routing: routing,
                    servicename: servicename, title: title, quantity: quantity, amountpaid: amount, action: 'GENERATEAMAZONLABEL'}, function (data) {
                    var myArr = data.split('||');
                    if (data == 'SUCCESS' || data.indexOf("SUCCESS") >= 0)
                    {
                        $('#labelLinks-' + hawb).html('<a href="#" class="ebayButton" onclick="return displayLabel(\'' + hawb + '\')">View Label</a> <a href="../main/amazon_invoice.php?hawb=' + hawb + '&description=' + title + '" class="ebayButton" target="_blank">Print Invoice</a>  <a href="#" class="ebayButton" onclick="return dispatchLabel(\'' + hawb + '\',\'' + myArr[2] + '\')">Dispatch</a><br>' + myArr[2]);
                        $('#row-' + hawb).attr('style', 'background:#ccc; color:#000;');
                    } else
                    {
                        alert(data);
                    }
                });
                return false;
            }





            /*
             Generate Label one by one. This function is called from BulkLabels() function in a loop.
             */
            function bulkLabelsGenerate(index, hawb, name, address1, address2, address3, city, country, postcode, phone, currency,
                    useraccount, username, password, status, servicename, weight, routing, title, quantity, amount)
            {


                $.post("../main/ajaxlabel_amazon.php", {index: index, hawb: hawb, name: name, address1: address1, address2: address2,
                    address3: address3, city: city, country: country, postcode: postcode, phone: phone, currency: currency,
                    useraccount: useraccount, username: username, password: password, status: status, weight: weight, routing: routing,
                    servicename: servicename, title: title, quantity: quantity, amount: amount, action: 'GENERATEAMAZONLABEL'},
                        function (data)
                        {
                            var myArr = data.split('||');

                            //alert(myArr);

                            if (data.indexOf("SUCCESS") >= 0)
                            {
                            } else
                            {
                                $('#errordiv').append(hawb + ' ' + myArr[1] + '<br>');
                                $('#errordiv').show();
                            }
                        });
                return false;
            }





            /*
             This function is called from BulkLabels() function after label creation
             */
            function mergeLabel()
            {
                $.post("../main/ajaxlabel_amazon.php", {action: 'GENERATEBULKAMAZONLABEL'}, function (data)
                {
                    $('#BulkLabelLink').show();
                    $('#BulkLabelLink').html(data);
                });
            }




            function mergeInvoice()
            {
                $.post("../main/ajaxlabel_amazon.php", {action: 'GenerateBulkInvoice'}, function (data)
                {
                    $('#BulkInvoiceLink').show();
                    $('#BulkInvoiceLink').html(data);
                });
            }




            $(window).scroll(function ()
            {
                if ($(this).scrollTop() > 380)
                {
                    if ($('.loading-service').height() < 350)
                        $('.loading-service').attr('style', 'position:fixed; right: 350px; top: 80px; display:none;');
                    else
                    {
                        $('.loading-service').attr('style', 'position:fixed; right: 350px; top: 80px; display:none;');
                        $('.loading-service').hide();
                    }
        //            if ($('#detail_advance').height() < 350)
        //                $('#detail_advance').attr('style', 'float:left; margin:10px; padding:20px;  position: fixed; right:95px; top: 30px;');
        //            else
        //                $('#detail_advance').attr('style', '');
                } else
                {
                    $('.loading-service').attr('style', '');
                    $('.loading-service').hide();
                    $('#detail_advance').attr('style', '');
                }
            });





            /// Function to View Label
            function displayLabel(consignmentid)
            {
                $.post("../main/ajaxlabel_amazon.php", {consignmentid: consignmentid, action: 'SHOWLABEL'}, function (data) {
                    if (data.indexOf('ERROR') < 0)
                    {
                        popupwindow(data, 'Label View', 550, 400);
                    } else
                    {
                        alert(data);
                    }
                });
                return false;
            }





            ///Function Dispatch Label. Ajax call to fullfilment.php it's submit feed API
            function dispatchLabel(hawb, trackingNumber)
            {
                var check_var = 0;
                var errHawb = new Array();
                var count = 0;

                var request = $.ajax({
                    type: "POST",
                    url: "../main/fullfilment.php", // your php file name
                    data: {hawb: hawb, action: 'DISPATCHLABELAMAZON'},
                    success: function (data)
                    {
                        if (data.indexOf('ERROR') < 0)
                        {
                            $('#labelLinks-' + hawb).html('<a href="#" class="ebayButton" onclick="return displayLabel(\'' + hawb + '\')">View Label</a>  <a class="ebayButton">Dispatched</a> <br>' + trackingNumber);
                            $('#status-' + hawb).html('Shipped');
                            $('#row-' + hawb).attr('style', ' background:#393; color:#FFF;');
                            check_var = 1;
                        } else
                        {
                            $('#errordiv').append(hawb + ' not exist in our system' + '<br>');
                            $('#errordiv').show();
                        }
                    }
                });
            }




            function view_detail(amazonOrderId, status)
            {
                $('.loading-service').show();
                $.post("../main/amazon_detail.php", {amazonOrderId: amazonOrderId, status: status}, function (data)
                {
                    $('#detail_modal').modal('show');
                    $('#detail_advance').html(data);
                    $('#detail_advance').show();
                    $('.loading-service').hide();

                });
            }






            /*
             When user click show more, this function is called
             */

            function nextToken()
            {
                //alert('hhh'); exit;
                $.post("../main/ajaxlabel_amazon.php", {action: 'NextToken'}, function (data)
                {
                    window.location = "../main/newamazon_list.php";
                });
                return false;
            }





            function order_id_search()
            {
                $('#order_id_textbox').show();
            }





            function checkedAll(group)
            {
                if (count == 0)
                {
                    for (var i = 0, len = group.length; i < len; i++)
                    {
                        group[i].checked = true;
                        count = 1;
                    }
                } else
                {
                    for (var i = 0, len = group.length; i < len; i++)
                    {
                        group[i].checked = false;
                        count = 0;
                    }
                }
            }



            $(document).ready(function ()
            {
        <?php if ($this->auth_data == true) { ?>
                    $('#auth-log-popup').modal("show");
        <?php } ?>
                $('#errordiv').hide();
                // SHOW UNSHIPPED
                $("#btnunshipped").click(function () {
                    $("#form_action").val("btnunshipped");
                    $("#adminForm").submit();
                });

                // SHOW Pendind
                $("#btnPending").click(function () {
                    $("#form_action").val("btnpending");
                    $("#adminForm").submit();
                });
                $('.selection').hide();
                $(document).on('change', '#checkall', function () {
                    if ($(".checkAmazon:checkbox:checked").length > 0) {
                        var checkAmazon = $(".checkAmazon").attr("checked");
                        $.uniform.update(checkAmazon);
                        $('.selection').show();
                    } else {
                        $('.selection').hide();
                    }
                    if (!$('#checkall').is(':checked')) {
                        var AmazonUncheck = $(".checkAmazon").removeAttr("checked");
                        $.uniform.update(AmazonUncheck);
                    }
                });
                $(document).on('change', '.checkAmazon', function () {
                    if ($(".checkAmazon:checkbox:checked").length > 0) {
                        $('.selection').show();
                    } else {
                        $('.selection').hide();
                    }
                });
                // SHOW Cancelled
                $("#btnCan").click(function () {
                    $("#form_action").val("btncancelled");
                    $("#adminForm").submit();
                });

                // Drop Down for status
                $("#pend_ship_unship").change(function () {
                    value = $('#pend_ship_unship').val();
                    $("#form_action").val("search");
                    $("#adminForm").submit();
                });

                $("#lessOrGreater").change(function () {
                    value = $('#lessOrGreater').val();
                    $("#form_action").val("search");
                    $("#adminForm").submit();
                });

                $("#sortCountry").change(function () {
                    value = $('#sortCountry').val();
                    $("#form_action").val("search");
                    $("#adminForm").submit();
                });

                // Fetch Orders
                $('#btnFetchOrders').click(function () {
                    $("#form_action").val("GETAMAZONORDERS");
                    $("#adminForm").submit();
                });

                $('#btnAmazonOrdersSearch').click(function () {
                    $("#form_action").val("btnAmazonOrdersSearch");
                    $("#adminForm").submit();
                });

                jQuery(".tooltip").tooltip();

                $("#fromOrderDate").datepicker({dateFormat: "d M yy"});
                $("#toOrderDate").datepicker({dateFormat: "d M yy"});
                $("#ui-datepicker-div").attr('style', 'display:none');

            });
        //    $(document).on( "click", "authBtn", function() {
        //                   
        //                });
        </script>
        <style>
            .tooltip
            {
                text-decoration:underline;
                cursor:pointer;
                position: relative;
                display: inline;
                overflow: auto;			
            }

            .tooltip:hover
            {
                text-decoration:none;			
            }

            .tooltip:hover:after
            {
                background		: #333;
                background		: rgba(0,0,0,.8);
                border-radius	: 5px;
                bottom			: 26px;
                color			: #fff;
                content			: attr(title);
                right			:0%;
                padding: 5px 15px;
                position: absolute;
                z-index: 98;
                width: 220px;
            }

            .tooltip:hover:before
            {
                border		: solid;
                border-color: #333 transparent;
                border-width: 6px 6px 0 6px;
                bottom		: 20px;
                content		: "";
                left		: 20%;
                position	: absolute;
                z-index		: 99;
            }			
        </style>        
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        foreach ($this->form_vars as $key => $val) {
            $key = $val;
        }

        $user = SessionManager::getUser();
//////////Get Service Name List from Services Table and store it in an array //////////////
        $services = new ServiceFilter();
        $services->addSpecialServicesFilter();
        $services->addAccountNumberFilter($user->getUserAccount());

        $service_name = $services->getRecordFromServiceAndName();
        $service_list = array();
        if (count($service_name) > 0) {
            foreach ($service_name as $ser_name) {
                $service_list[] = $ser_name->getName();
            }
        } else {
            $service_list = array();
        }
///////////////////////////////////////////////////////////////////////////////////////////
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/client_list.php">Consignments</a></li>
            <li><a href="#">List</a></li>
        </ul>
        <?php
        /* // transfer form variables into local values (form variables come from parent)
          foreach ($this->form_vars as $key=>$val) {$key = $val; } */
        ?>


        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Amazon Order List</div>
                <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""></a> </div>
            </div>
            <div class="portlet-body">

                <div class="row">
                    <div class="col-md-12">



                        <div class="alert alert-warning">
                            <?php echo sizeof(@$_SESSION["filter"]) . " " . $this->table_msg ?></div>

                        <div class="main_container">

                            <!-- Fetch Order Bar -->
                            <div class="portlet box blue-hoki">
                                <div class="portlet-title">
                                    <div class="caption"><i class="fa fa-search"></i>Fetch Amazon orders:</div>
                                    <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">      

                                        <div class="col-md-2">
                                            <label class="label-control">&nbsp;</label>
                                            <div class="form-group">   
                                                <div class="input-group">   <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>

                                                    <input id="fromOrderDate" type="text"  placeholder="From Date"
                                                           name="createdAfter" value="<?php
                                                           if (isset($_SESSION['ordersdate']['createdAfter']))
                                                               echo date('d-m-Y', strtotime($_SESSION['ordersdate']['createdAfter']));
                                                           else
                                                               echo date('d-m-Y');
                                                           ?>" class="form-control">  


                                                </div>

                                            </div>
                                        </div>      


                                        <div class="col-md-2">


                                            <label class="label-control">&nbsp;</label>
                                            <div class="form-group">   
                                                <div class="input-group">   <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>


                                                    <input id="toOrderDate" type="text" placeholder="To Date"
                                                           name="createdBefore" value="<?php
                                                           if (isset($_SESSION['ordersdate']['createdBefore']))
                                                               echo date('d-m-Y', strtotime($_SESSION['ordersdate']['createdBefore']));
                                                           else
                                                               echo date('d-m-Y');
                                                           ?>" class="form-control">

                                                </div>

                                            </div>

                                        </div>
                                        <div class="col-md-2">
                                            <label>Status:</label>
                                            <div class="form-group">




                                                <select id="pend_ship_unship" name="pend_ship_unship" class="form-control">
                                                    <option  <?php
                                                    if ($this->form_vars['pend_ship_unship'] == 'Select a Status')
                                                        echo 'selected="selected"';
                                                    ?> value="Select a Status">Select a Status</option>

                                                    <option  <?php
                                                    if (trim($this->form_vars['pend_ship_unship']) == 'Shipped')
                                                        echo 'selected="selected"';
                                                    ?> value="Shipped">Shipped</option>

                                                    <option <?php
                                                    if (trim($this->form_vars['pend_ship_unship']) == 'Unshipped')
                                                        echo 'selected="selected"';
                                                    ?>value="Unshipped">Unshipped</option>

                                                    <option <?php
                                                    if (trim($this->form_vars['pend_ship_unship']) == 'Pending')
                                                        echo 'selected="selected"';
                                                    ?>value="Pending">Pending</option>                      
                                                </select>     





                                            </div>
                                        </div>

                                        <div class="col-md-2">

                                            <label>Value:</label>

                                            <div class="form-group">





                                                <select id="lessOrGreater" name="lessOrGreater" class="form-control">

                                                    <option  <?php
                                                    if ($this->form_vars['lessOrGreater'] == 'Select a Value')
                                                        echo 'selected="selected"';
                                                    ?> value="Select a Value">Select a Value</option>

                                                    <option <?php
                                                    if ($this->form_vars['lessOrGreater'] == 'LessFifteen')
                                                        echo 'selected="selected"';
                                                    ?> value="LessFifteen">Less Than 15</option>

                                                    <option <?php
                                                    if ($this->form_vars['lessOrGreater'] == 'GreaterFifteen')
                                                        echo 'selected="selected"';
                                                    ?> value="GreaterFifteen">Greater Than 15</option>
                                                </select>




                                            </div>

                                        </div>

                                        <div class="col-md-2">
                                            <label>Country:</label>
                                            <div class="form-group">




                                                <select id="sortCountry" name="sortCountry"  class="form-control">
                                                    <option <?php
                                                    if ($this->form_vars['sortCountry'] == 'Select a Country')
                                                        echo 'selected="selected"';
                                                    ?> value="Select a Country">Select a Country</option>

                                                    <option <?php
                                                    if ($this->form_vars['sortCountry'] == 'GB')
                                                        echo 'selected="selected"';
                                                    ?> value="GB">UK</option>

                                                    <option <?php
                                                    if ($this->form_vars['sortCountry'] == 'non_GB')
                                                        echo 'selected="selected"';
                                                    ?> value="non_GB">NON UK</option>
                                                </select>     



                                            </div>
                                        </div>                    

                                        <div class="col-md-2">
                                            <label class="label-control">&nbsp;</label>
                                            <div class="form-group">
                                                <input class="held btn btn-primary" id="btnFetchOrders" name="btnFetchOrders" type="button" value="Fetch Orders">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Filter Order Bar -->
                            <hr />     
                            <div class="portlet box blue-hoki">
                                <div class="portlet-title">
                                    <div class="caption"><i class="fa fa-search"></i>Search by Order Id:</div>
                                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""></a> </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>OrderId:</label>
                                            <div class="form-group">
                                                <select class="form-control" id="search_amazon" name="search_amazon" onchange="order_id_search()"
                                                        >
                                                    <option value="order_id">Select a value</option>
                                                    <option value="order_id">Order ID</option>
                                                </select> 
                                            </div>

                                        </div>                         

                                        <div class="col-md-2">  
                                            <label>&nbsp;</label>
                                            <div class="form-group">
                                                <input type="text" id="order_id_textbox" name="order_id_textbox" style="display:none" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <div class="form-group">
                                                <input class="search btn btn-primary"  
                                                       id="btnAmazonOrdersSearch" name="btnAmazonOrdersSearch" 
                                                       type="button" value="Search" >   
                                            </div>
                                        </div>                           </div>
                                </div>
                            </div>

                <!--<li><input class="show" id="btnShipped" name="btnShipped" type="button" value="Shipped" /></li>
                <li><input class="held" id="btnunshipped" name="btnunshipped" type="button" value="Unshipped" /></li>    
                <li><input class="held" id="btnPending" name="btnPending" type="button" value="Pending"></li>-->                








                            <!--Generate bulk Label and Dispatch Bulk Bar -->

                            <hr />    
                            <div class="portlet box blue-hoki selection">
                                <div class="portlet-title">
                                    <div class="caption"><i class="fa fa-cog"></i>Bulk </div>
                                    <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">   

                                        <div class="col-md-3">    

                                            <label>
                                                Select a Service :</label>

                                            <div class="form-group">   
                                                <select id="service_name" name="service_name" class="form-control">
                                                    <option value="Select a Service">Select a Service</option>
                                                    <?php
                                                    foreach ($service_list as $serviceName) {
                                                        ?>                        	
                                                        <option value='<?php echo $serviceName ?>'><?php echo $serviceName ?></option>
                                                    <?php }
                                                    ?>                        
                                                </select> </div>
                                        </div>

                                        <div class="col-md-2">         

                                            <label>Weight:</label>
                                            <input type='text' name="weight_bulk" id="weight_bulk" class="form-control" />
                                        </div>

                                        <div class="col-md-2">    

                                            <label>Quantity:</label>
                                            <input type='text' name="quantity_bulk" id="quantity_bulk" class="form-control"/>
                                        </div>


                                        <div class="col-md-5">  

                                            <div style="margin-top:20px">           
                                                <input class="held btn-primary btn" id="btnBulkLabel" name="btnBulkLabel" 
                                                       type="button" value="Bulk Label"  onclick="BulkLabels();" >


                                                <input class="held  btn-primary btn" id="btnDispatchOrders" name="btnDispatchOrders" 
                                                       type="button" value="Bulk Dispatch"  onclick="dispatchBulkOrders();">

                                                <input class="held  btn-primary btn" id="btnBulkInvoice" name="btnBulkInvoice" 
                                                       type="button" value="Bulk Invoice"  onclick="BulkInvoice();">

                                                <div id="BulkLabelLink" style="float:right"></div>
                                                <div id="BulkInvoiceLink" style="float:right"></div>
                                            </div>


                                        </div>

                                    </div></div>

                            </div>




                            <div class="portlet box blue-hoki">
                                <div class="portlet-title">
                                    <div class="caption">Order List </div>
                                    <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
                                </div>
                                <div class="portlet-body">
                                    <!-- AMAZON ORDER TABLE -->
                                    <div id='table_container'  class="main_grid2">





                                        <div class="alert alert-warning"  id="errordiv">
                                        </div>




                                        <table id='consignment_list' class="consignment_list_tbl table table-striped table-bordered table-advance table-hover">
                                            <colgroup width='20'> </colgroup>
                                            <?php
                                            if ($user->getUserAccount() == "")
                                                echo "<colgroup width='30'> </colgroup>";
                                            ?>
                                            <!-- table head -->
                                            <thead>
                                                <tr>	
                                                    <th scope="col">
                                                        <input id="checkall" type='checkbox' name='checkall' onclick='checkedAll(dispatchOrder);'></th>							
                                                    <th scope="col" align="center">Date</th>
                                                    <th scope="col">Amazon Order ID</a></th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">View Details</th>
                                                </tr>
                                            </thead>

                                            <!-- table body -->

                                            <tbody>

                                                <?php
                                                if (!isset($_SESSION["filter"])) {
                                                    $_SESSION["filter"] = @$_SESSION["orders"];
                                                }
                                                $consignment_array = @$_SESSION["filter"];

                                                if (count($consignment_array) > 0) {
                                                    foreach ($consignment_array as $ct_idx => $consignment) {
                                                        $amazon_order_id = @$consignment["AmazonOrderId"];
                                                        $purchase_date = @$consignment["PurchaseDate"];
                                                        $status = @$consignment["OrderStatus"];
                                                        $title = @$consignment["Title"];
                                                        $name = @$consignment["Name"];
                                                        $address1 = @$consignment["AddressLine1"];
                                                        $address2 = @$consignment["AddressLine2"];
                                                        $address3 = @$consignment["AddressLine3"];
                                                        $city = @$consignment["City"];
                                                        $county = @$consignment["County"];
                                                        $district = @$consignment["District"];
                                                        $state = @$consignment["StateOrRegion"];
                                                        $postCode = @$consignment["PostalCode"];
                                                        $countryCode = @$consignment["CountryCode"];
                                                        $phone = @$consignment["Phone"];
                                                        $currency = @$consignment["CurrencyCode"];
                                                        $amount = @$consignment["Amount"];

                                                        $useraccount = @$user->getUserAccount();
                                                        $username = @$user->getUserName();
                                                        $userpass = @$user->getUserPass();
                                                        //$serviceType	= $user->getUserServiceType();

                                                        /*
                                                          Create jasonArr and access it in javascript
                                                         */
                                                        $jsonArr[] = array('amazon_order_id' => $amazon_order_id, 'purchase_date' => $purchase_date,
                                                            'status' => $status, 'title' => $title, 'name' => $name, 'address1' => $address1, 'address2' => $address2,
                                                            'address3' => $address3, 'city' => $city, 'county' => $county, 'district' => $district, 'state' => $state,
                                                            'postcode' => $postCode, 'countryCode' => $countryCode, 'phone' => $phone, 'currency' => $currency,
                                                            'amount' => $amount, 'useraccount' => $useraccount, 'username' => $username, 'userpassword' => $userpass);
                                                        $jason = json_encode($jsonArr);
                                                        ?>
                                                        <tr id="row-<?php echo $amazon_order_id; ?>">
                                                            <td style="width:3%;">
                                                                <input class="checkAmazon" type="checkbox" name="dispatchOrder[]" id="dispatchOrder" value="<?php echo $amazon_order_id; ?>"
                                                                       size="2" style="color:#999;"/>
                                                            </td>
                                                            <td style="width:10%;"><?php echo date('d M Y', strtotime($purchase_date)); ?></td>
                                                            <td style="width:20%;"><?php
                                                                echo "<b><font color='#103181'>" . $amazon_order_id . "<br></font>
				<font weight='normal' size='2px'><br>Shipping Address:<br>"
                                                                . $address1 . "<br>" . $address2 . $address3 . $postCode . "<br>" . $city . "<br>" . $countryCode;
                                                                ?></td>

                                                            <td style="width:10%;" id="status-<?php echo $amazon_order_id; ?>"><?php echo $status; ?></td>

                                                            <td style="width:10%; cursor: pointer;" id="detail-<?php echo $amazon_order_id; ?>"><span 
                                                                    onclick="view_detail('<?php echo $amazon_order_id; ?>', '<?php echo $status; ?>')">View Detail</span></td>

                                                        </tr>

                                                    <?php }
                                                    ?>
                                                <script>
                                                    var jasonArrayConsignment = <?php echo $jason; ?>;
                                                </script>

                                                <?php
                                            } else {
                                                ?> <tr><td colspan=8><?php if ($this->notification != '') echo $this->notification; ?></td></tr>
                                            <?php }
                                            ?>
                                            </tbody>

                                        </table>






                                        <img class="loading-service" width="52" style="display:none;" src="loading.gif">






                                    </div>  <!-- table_container -->

                                    <!-- Show More Div-->
                                    <div class="alert alert-warning">
                                        <?php
                                        if (isset($_SESSION["nextToken"])) {
                                            ?>
                                            <a href='' onclick="return nextToken()">Show More</a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <!-- End of Show More Div-->
                                </div></div>
                        </div>   </div>   </div> </div> 

            <input type="hidden" name="form_action" id="form_action" />
            <div id="detail_modal" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 id="modal-title_dispatch" class="modal-title"><strong>View Detail</strong></h4><br/>
                        </div>
                        <div class="modal-body clearfix clear_both">  
                            <form class="form-horizontal" data-toggle="validator" role="form" id="viewdetaillfrm" name="viewdetaillfrm" method="post" novalidate="true">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="detail_advance">
                                            </div>                                             
                                        </div>
                                    </div>                    
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn default" id="clear">Close</button>
                        </div>
                    </div>
                </div>
            </div>


            <?php
        }

        public function renderFooter() {
            $sessionUser = SessionManager::getUser();
            ?>
            <div class="modal fade" tabindex="-1" role="dialog" id="auth-log-popup" >
                <div class="modal-dialog">
                    <form name="authentication_frm" id="authentication_frm" action="" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Authentication Details</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row" >
                                    <?php
                                    //GET user platforms
                                    $UserShoppingPlatformMappingFilter = new UserShoppingPlatformMappingFilter();
                                    $UserShoppingPlatformMappingFilter->addUserIdFilter($sessionUser->getId());
                                    $UserShoppingPlatformMappingFilter->addShoppingPlatformMappingIdMd5Filter($this->platform_id);
                                    $UserData = $UserShoppingPlatformMappingFilter->getList();
                                    if (count($UserData) > 0) {
                                        foreach ($UserData as $U) {
                                            $this->selectedShoppingPlatfrom[$U->getMarketPlacesId()] = $U->getAuthData();
                                        }
                                    }
                                    //GET Allowed shopping platform
                                    // GET All Shopping Platform List
                                    $ShoppingPlatformFilter = new ShoppingPlatformFilter();
                                    $ShoppingData = $ShoppingPlatformFilter->getAllowedShoppingPlatformData($sessionUser->getId(), $this->platform_id);
                                    if (!empty($ShoppingData)) {
                                        $PlatformArray = array();
                                        foreach ($ShoppingData as $Shopping) {
                                            $PlatformArray[$Shopping->getId()][] = array("PlatformId" => $Shopping->getId(), "PlatformTitle" => $Shopping->getTitle(), "AuthenticateId" => $Shopping->getDescription(), "AuthenticateTitle" => $Shopping->getIsActive(), "AuthenticateValue" => $Shopping->getAddedBy());
                                        }
                                        foreach ($PlatformArray as $ShoppingArray) {
                                            ?>

                                            <?php
                                            $authDataArray = array();
                                            if (isset($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']])) {
                                                $auth_data = $this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']];
                                                $authDataArray = (array) json_decode($auth_data);
                                            }
                                            foreach ($ShoppingArray as $value) {
                                                $tmpVal = $value['AuthenticateValue'];
                                                $name_index = "shopping_plateform[" . $value['PlatformId'] . "][" . $tmpVal . "]";
                                                $auth_value = isset($authDataArray[$tmpVal]) ? $authDataArray[$tmpVal] : '';
                                                ?>
                                                <div class="col-md-6">
                                                    <label><?php echo $value['AuthenticateTitle']; ?></label> 
                                                    <input name="<?php echo $name_index; ?>" type="text" value="<?php echo $auth_value; ?>" placeholder="<?php echo $value['AuthenticateTitle']; ?>" class="form-control shopping_plateform_input shopping_plateform_<?php echo $value['PlatformId']; ?>" <?php echo ((!isset($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']])) ? 'disabled="true"' : ''); ?> />
                                                </div>  
                                                <?php
                                            }
                                            ?>

                                            <?php
                                            $count++;
                                        }
                                    } else {
                                        echo "No data found";
                                    }
                                    ?> 

                                </div>       
                            </div>
                            <div class="modal-footer">
                                <!--                        <button type="button" name="authBtn" id="authBtn" value="Save" class="btn btn-default">Close</button>-->
                                <input type="submit" name="authBtn" id="authBtn" value="Save" class="btn btn-default" />
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>    
            <?php
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
    