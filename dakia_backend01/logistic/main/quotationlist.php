<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Country list page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {
    /*     * *
     * This page's content
     * @return void
     */

    private $quotation = NULL;
    private $quotation_filter = NULL;

    public function renderHead() {
        ?>
        <script type="text/javascript" async="true">
            jQuery(document).ready(function () {

                $("#searchall").keypress(function (e) {
                    if (e.keyCode == 13) {
                        $("#form_action").val("searchall");

                        $("#adminForm").submit();
                    }

                });


                $("input[name=price_type]:radio").change(function () {

                    var pricetype = $('input[name=price_type]:checked').val();
                    if (pricetype == 'manual')
                    {
                        $("#calc_basic").removeAttr('readonly');
                        $("#calc_fuel").removeAttr('readonly');
                        $("#calc_extra").removeAttr('readonly');
                    } else
                    {
                        $("#calc_basic").prop('readonly', 'readonly');
                    }

                });
                $("input[name=price_type]:radio").change();


                $("#calc_loading").hide();
                $("#calc_total_weight_error_container").hide();
                $('#radioBtn2 a').on('click', function () {
                    var sel = $(this).data('title');
                    var tog = $(this).data('toggle');
                    $('#' + tog).prop('value', sel);
                    $('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
                    $('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
                    calculatePrice();
                });
                $("#calc_weight").keyup(function () {
                    validateWeight($(this).val(), 'calc_');
                });
                $(".calculate_price").change(function () {
                    //  calculatePrice();
                });
                /* $(".calculate_price_input").keyup(function() {
                 var id = $(this).attr('id');
                 if($("#_"+id).length > 0)
                 $("#_"+id).val($(this).val());                    
                 if (window.myajax) {
                 clearTimeout(window.myajax);
                 }
                 window.myajax = setTimeout(function() {
                 calculatePrice();
                 window.myajax = null;
                 }, 500);
                 });*/

                $('#calc_shipping_to').change(function (e) {
                    //alert($( this ).val());
                })

            });




            function addNewDimmBoxes(domElement)
            {
                var dimBoxes = $("#item-dimm-html").html();
                var numberPiece = domElement.value;
                var existingElements = $("#dimention-section").find("div.item-dimenssion");
                var existingElementCount = existingElements.length;
                //$("#dimention-section").html('');
                //	alert(numberPiece	+' '+existingElementCount);
                if (numberPiece > existingElementCount)
                {
                    for (i = existingElementCount; i < numberPiece; i++)
                    {
                        //alert(dimBoxes)
                        HtmlToTight = dimBoxes.replace(/\#\#\#\#+/g, '_' + i);
                        $("#dimention-section").append(HtmlToTight);
                    }
                } else if (numberPiece < existingElementCount)
                {
                    for (i = numberPiece; i <= existingElementCount; i++)
                    {
                        existingElements[i].remove();
                    }
                }

                if (numberPiece > 0)
                    $("#calculationVolumn").show();
            }

            function calclulateweight(keyIdNumber)
            {
                //alert(keyIdNumber);
                var conversion = $("#conversion").val();
                if (conversion == "" || conversion == "0")
                {
                    alert('Please select conversion rate.');
                }
                var calcLength = $("#calc_length" + keyIdNumber).val();
                var calcWidth = $("#calc_width" + keyIdNumber).val();
                var calcHeight = $("#calc_height" + keyIdNumber).val();
                if (parseFloat(calcLength) <= 0)
                    return false;
                if (parseFloat(calcWidth) <= 0)
                    return false;
                if (parseFloat(calcHeight) <= 0)
                    return false;

                var volumnWeight = (calcLength * calcWidth * calcHeight) / conversion;

                $("#vol_wgt" + keyIdNumber).val(volumnWeight.toFixed(2));
                calclulateweightTotal();


            }

            function calclulateweightTotal()
            {
                var tot_pieces = $('#pieces').val();
                var totalWeight = 0;
                for (i = 0; i < tot_pieces; i++)
                {
                    var individualWeight = $('#vol_wgt_' + i).val()

                    totalWeight = parseFloat(totalWeight) + parseFloat(individualWeight);
                }

                $('#volumn_weight_total').val(parseFloat(totalWeight).toFixed(2));
                chargeAbleWeight();

            }

            function chargeAbleWeight()
            {
                var volumn_weight_total = $('#volumn_weight_total').val();
                var calc_weight = $('#calc_weight').val();
                if (volumn_weight_total != "") {
                    weight = volumn_weight_total > calc_weight ? volumn_weight_total : calc_weight;
                    $('#calc_chargeable_weight').val(weight);
                }
            }

            function saveQuotation()
            {


                var calc_currency = $("#currency").val();
                var calc_scale = $("#calc_scale").val();
                var calc_shipping_from = $("#calc_shipping_from").val();
                var calc_shipping_to = $("#calc_shipping_to").val();
                var service_type = $("#service_type").val();
                var account = $("#account").val();
                var pieces = $("#pieces").val();
                var calc_remark = $("#calc_remark").val();
                var calc_weight = $("#calc_weight").val();
                var quotationid = $("#quotationid").val();
                var calc_basic = $("#calc_basic").val();
                var calc_fuel = $("#calc_fuel").val();
                var calc_extra = $("#calc_extra").val();
                var useremail = $("#useremail").val();
                var discount = $("#discount").val();
                var totalamount = $("#screen_price").html()
                var city = $("#city").html()
                var postcode = $("#postcode").html()
                var pricetype = $('input[name=price_type]:checked').val();
                if ($('#discount_type').is(":checked"))
                    var discount_type = 'PERCENTAGE';
                else
                    var discount_type = 'NORMAL';


                var calc_width = 0;
                var calc_length = '';
                var calc_height = '';
                var calc_VolWidth = '';
                var weight = calc_weight;

                for (i = 0; i < pieces; i++)
                {
                    //alert(i+1);
                    if ((i + 1) == pieces)
                        var additionalConnector = '';
                    else
                        var additionalConnector = '&&';
                    calc_width += $('#calc_width_' + i).val() + additionalConnector;
                    calc_length += $('#calc_length_' + i).val() + additionalConnector;
                    calc_height += $('#calc_height_' + i).val() + additionalConnector;
                    calc_VolWidth += $('#vol_wgt_' + i).val() + additionalConnector;
                }
                var volumetric_weight = $('#volumn_weight_total').val();
                var conversion = $('#conversion').val();

                if (volumetric_weight != "") {
                    weight = volumetric_weight > calc_weight ? volumetric_weight : calc_weight;
                }
                $("#calc_chargeable_weight").val(weight);
                var quotationid = $("#quotation_id").val();


                if (validateWeight(weight, 'calc_')) {
                    $.post(
                            "quotation.php",
                            {action: 'SAVE_QUOTATION', quotationid: quotationid, calc_currency: calc_currency, calc_scale: calc_scale, calc_shipping_from: calc_shipping_from, calc_shipping_to: calc_shipping_to, service_type: service_type, account: account, pieces: pieces, calc_remark: calc_remark, calc_weight: weight, calc_width: calc_width, calc_length: calc_length, calc_height: calc_height, calc_basic: calc_basic, calc_fuel: calc_fuel, calc_extra: calc_extra, useremail: useremail, discount: discount, discount_type: discount_type, totalamount: totalamount, city: city, postcode: postcode, conversion: conversion, calc_width: calc_width, calc_length: calc_length, calc_height: calc_height, calc_VolWidth: calc_VolWidth, pricetype: pricetype},
                            function (data)
                            {
                                var obj = jQuery.parseJSON(data);
                                if (obj.STATUS == 'SUCCESS')
                                {
                                    $("#quotation_id").val(obj.id);
                                    alert(obj.message);
                                }
                                //window.location = "../main/quotationlist.php";
                            });
                }
            }

            function changeTheRatesValues()
            {

                var calcBasic = $("#calc_basic").val();
                var calcFuel = $("#calc_fuel").val();
                var calcExtra = $("#calc_extra").val();
                var discount = $("#discount").val();
                var sumAll = parseFloat(calcBasic) + parseFloat(calcFuel) + parseFloat(calcExtra);
                var discountAmount = 0;
                if ($('#discount_type').is(":checked") && discount != '' && parseFloat(discount) > 0)
                {
                    discountAmount = parseFloat(discount / 100 * sumAll).toFixed(2);
                } else if (discount == '' || parseFloat(discount) == 0)
                {
                    discountAmount = parseFloat(0).toFixed(2);
                } else
                {
                    discountAmount = parseFloat(discount).toFixed(2);
                }
                if (discountAmount >= sumAll)
                {
                    $("#screen_price").html("Invalid discount amount.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                } else
                {
                    sumAll = sumAll - discountAmount;

                }

                $("#screen_price").html(sumAll.toFixed(2));
            }
            function validateWeight(TotalWeight, prefix) {
                if (!isNaN(TotalWeight) && TotalWeight > 0) {
                    if ($("#" + prefix + "total_weight_container").length > 0) {
                        $("#" + prefix + "total_weight_container").removeClass('has-error');
                        $("#" + prefix + "total_weight_container").addClass('has-success');
                        $("#" + prefix + "total_weight_error_container").hide();
                    }
                    return true;
                } else {
                    if ($("#" + prefix + "total_weight_container").length > 0) {
                        $("#" + prefix + "total_weight_container").removeClass('has-success');
                        $("#" + prefix + "total_weight_container").addClass(' has-error');
                        $("#" + prefix + "total_weight_error_container").show();
                        $("#" + prefix + "total_weight_container").find('input').focus();
                        $('html,body').animate({
                            scrollTop: ($("#" + prefix + "total_weight_container").offset().top - 100)
                        }, 'slow');
                    }
                    return false;
                }
            }
            function calculatePrice() {
                var calc_currency = $("#currency").val();
                var calc_scale = $("#calc_scale").val();
                var calc_shipping_from = $("#calc_shipping_from").val();
                var calc_shipping_to = $("#calc_shipping_to").val();
                var service_type = $("#carrier").val();
                var service_type = $("#service_type").val();
                var account = $("#account").val();
                var pieces = $("#pieces").val();
                var calc_remark = $("#calc_remark").val();
                var calc_weight = $("#calc_weight").val();

                var discount = $("#discount").val();
                var postcode = $("#postcode").val();
                var city = $("#city").val();
                var discountAmount = 0;

                var calc_width = '';
                var calc_length = '';
                var calc_height = '';
                var calc_VolWidth = '';
                var weight = calc_weight;
                var calc_basic = $("#calc_basic").val();
                var calc_fuel = $("#calc_fuel").val();
                var calc_extra = $("#calc_extra").val();


                for (i = 0; i < pieces; i++)
                {
                    //alert(i+1);
                    if ((i + 1) == pieces)
                        var additionalConnector = '';
                    else
                        var additionalConnector = '&&';

                    calc_width += $('#vol_wgt_' + i).val() + additionalConnector;
                    calc_length += $('#vol_wgt_' + i).val() + additionalConnector;
                    calc_height += $('#vol_wgt_' + i).val() + additionalConnector;
                    calc_VolWidth += $('#vol_wgt_' + i).val() + additionalConnector;
                }
                var volumetric_weight = $('#volumn_weight_total').val();

                if (volumetric_weight != "") {
                    weight = volumetric_weight > calc_weight ? volumetric_weight : calc_weight;
                }


                if ($.trim(account) == '')
                {
                    $("#screen_price").html("Please select account number.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }
                if ($.trim(calc_shipping_from) == '' || $.trim(calc_shipping_to) == '')
                {
                    $("#screen_price").html("Please select country.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }
                if (parseFloat(calc_weight).toFixed(2) <= 0.00)
                {
                    $("#screen_price").html("Please enter weight.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }
                if ($.trim(carrier) == '')
                {
                    $("#screen_price").html("Please select carrier.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }
                if ($.trim(service_type) == '')
                {
                    $("#screen_price").html("Please select service.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }
                if ($.trim(calc_currency) == '')
                {
                    $("#screen_price").html("Please select currency.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }
                if ($.trim(calc_currency) == '')
                {
                    $("#screen_price").html("Please select currency.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }

                if (pieces <= 0)
                {
                    $("#screen_price").html("Please select number of pieces/items.");
                    $("#save").hide();
                    $("#sendemail").hide();
                    $("#print_quote").hide();
                    return false;
                }

                //if (calc_weight != "" && calc_width != "" && calc_length != "" && calc_height != "") {
                //   var volumetric_weight = (calc_width * calc_length * calc_height) / 5000;
                // weight = volumetric_weight > calc_weight ? volumetric_weight : calc_weight;
                ///}


                $("#calc_chargeable_weight").val(weight);

                var pricetype = $('input[name=price_type]:checked').val();
                if (pricetype == 'manual')
                {
                    var totalCharges = parseFloat(calc_basic) + parseFloat(calc_fuel) + parseFloat(calc_extra);
                    if ($('#discount_type').is(":checked") && discount != '' && parseFloat(discount) > 0)
                    {
                        discountAmount = parseFloat(discount / 100 * totalCharges).toFixed(2);
                    } else if (discount == '' || parseFloat(discount) == 0)
                    {
                        discountAmount = parseFloat(0).toFixed(2);
                    } else
                    {
                        discountAmount = parseFloat(discount).toFixed(2);
                    }
                    if (discountAmount >= totalCharges)
                    {
                        $("#screen_price").html("Invalid discount amount.");
                        $("#save").hide();
                        $("#sendemail").hide();
                        $("#print_quote").hide();
                    } else
                    {
                        totalCharges = totalCharges - discountAmount;
                        $("#screen_price").html((totalCharges).toFixed(2));
                        $("#save").show();
                        $("#sendemail").show();
                        $("#print_quote").show();
                    }
                } else
                {
                    if (validateWeight(weight, 'calc_')) {
                        $.post(
                                "quotation.php",
                                {action: 'CALCULATE_QUOTATION', calc_currency: calc_currency, calc_scale: calc_scale, calc_shipping_from: calc_shipping_from, calc_shipping_to: calc_shipping_to, service_type: service_type, account: account, pieces: pieces, calc_remark: calc_remark, calc_weight: weight, calc_width: calc_width, calc_length: calc_length, calc_height: calc_height, postcode: postcode, city: city, pricetype: pricetype},
                                function (data)
                                {
                                    var obj = jQuery.parseJSON(data);
                                    if (obj.status == 'ERROR')
                                    {
                                        $("#screen_price").html(obj.tariff);
                                        $("#calc_basic").val(0.00);
                                        $("#calc_fuel").val(0.00);
                                        $("#calc_extra").val(0.00);

                                        $("#save").hide();
                                        $("#sendemail").hide();
                                        $("#print_quote").hide();
                                    } else
                                    {
                                        var totalCharges = parseFloat(obj.tariff) + parseFloat(obj.registration_charges) + parseFloat(obj.fuel_tariff) + parseFloat(obj.extra_tariff);
                                        var basicCharge = parseFloat(obj.tariff) + parseFloat(obj.registration_charges);
                                        if ($('#discount_type').is(":checked"))
                                        {
                                            if (discount != '' && parseFloat(discount) > 0)
                                            {
                                                discountAmount = discount / 100 * totalCharges;
                                            }
                                        } else
                                        {
                                            if (discount >= totalCharges)
                                            {
                                                $("#screen_price").html("Invalid discount amount.");
                                                $("#save").hide();
                                                $("#sendemail").hide();
                                                $("#print_quote").hide();
                                                return false;
                                            } else if (discount != '')
                                            {
                                                discountAmount = parseFloat(discount);
                                            }
                                        }
                                        totalCharges = totalCharges - discountAmount;
                                        $("#screen_price").html((totalCharges).toFixed(2));
                                        $("#calc_basic").val(parseFloat(basicCharge).toFixed(2));
                                        $("#calc_fuel").val(parseFloat(obj.fuel_tariff).toFixed(2));
                                        $("#calc_extra").val(parseFloat(obj.extra_tariff).toFixed(2));
                                        $("#save").show();
                                        $("#sendemail").show();
                                        $("#print_quote").show();
                                    }
                                });
                    }
                }
            }

            function getCarrierNames(callingMethod, countryRequest)
            {
                if (countryRequest == 'FROM')
                    var countryIso = $("#calc_shipping_from").val();
                else
                    var countryIso = $("#calc_shipping_to").val();
                // alert(countryIso);
                $.ajax({
                    url: "quotation.php",
                    type: "POST",
                    async: false,
                    data: {
                        action: 'GETCARRIERNAMES',
                        countryIso: countryIso,
                        countryRequest: countryRequest
                    },
                    success: function (data) {
                        $('#carrierdrop').html(data);
                    }
                });

            }

            function getServiceType(callingMethod)
            {
                var carrierName = $("#carrier").val();
                var fromCountry = $("#calc_shipping_from").val();
                var account = $("#account").val();
                if (fromCountry == '')
                {
                    alert('Please select from country');
                    return false;
                }
                var toCountry = $("#calc_shipping_to").val();
                if (toCountry == '')
                {
                    alert('Please select destination country');
                    return false;
                }

                $.ajax({
                    url: "quotation.php",
                    type: "POST",
                    async: false,
                    data: {
                        action: 'GETCARRIERSERVICETYPES',
                        carrierName: carrierName,
                        fromCountry: fromCountry,
                        toCountry: toCountry,
                        account: account
                    },
                    success: function (data) {
                        $('#servicetype').html(data);
                        if (callingMethod != 'onload')
                        {
                            $("#screen_price").html('0.00');
                            $("#calc_basic").val('0.00');
                            $("#calc_fuel").val('0.00');
                            $("#calc_extra").val('0.00');
                            $("#save").hide();
                            $("#sendemail").hide();
                            $("#print_quote").hide();

                        }

                    }
                });

            }
            function printquote()
            {
                var quotationid = $("#quotation_id").val();
                //alert(quotationid);
                var calc_currency = $("#currency").val();
                var calc_scale = $("#calc_scale").val();
                var calc_shipping_from = $("#calc_shipping_from").val();
                var calc_shipping_to = $("#calc_shipping_to").val();
                var service_type = $("#service_type").val();
                var account = $("#account").val();
                var pieces = $("#pieces").val();
                var calc_remark = $("#calc_remark").val();
                var calc_weight = $("#calc_weight").val();
                var calc_width = $("#calc_width").val();
                var calc_length = $("#calc_length").val();
                var calc_height = $("#calc_height").val();
                //var quotationid 			= $("#quotationid").val();
                var userEmail = $("#useremail").val();
                var weight = calc_weight;
                if (calc_weight != "" && calc_width != "" && calc_length != "" && calc_height != "") {
                    var volumetric_weight = (calc_width * calc_length * calc_height) / 5000;
                    weight = volumetric_weight > calc_weight ? volumetric_weight : calc_weight;
                }
                $.post(
                        "quotation.php",
                        {action: 'PRINT_QUOTE',
                            quotationid: quotationid,
                            calc_currency: calc_currency,
                            calc_scale: calc_scale,
                            calc_shipping_from: calc_shipping_from,
                            calc_shipping_to: calc_shipping_to,
                            service_type: service_type,
                            account: account, pieces: pieces,
                            calc_remark: calc_remark,
                            calc_weight: weight,
                            calc_width: calc_width,
                            calc_length: calc_length,
                            calc_height: calc_height,
                            userEmail: userEmail,

                        },
                        function (data)
                        {
                            //alert(data);
                            window.open(data, '', 'width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');
                        });

            }
            function sendEmail()
            {

                //alert(quotationid);
                var calc_currency = $("#currency").val();
                var calc_scale = $("#calc_scale").val();
                var calc_shipping_from = $("#calc_shipping_from").val();
                var calc_shipping_to = $("#calc_shipping_to").val();
                var service_type = $("#service_type").val();
                var account = $("#account").val();
                var pieces = $("#pieces").val();
                var calc_remark = $("#calc_remark").val();
                var calc_weight = $("#calc_weight").val();
                var calc_width = $("#calc_width").val();
                var calc_length = $("#calc_length").val();
                var calc_height = $("#calc_height").val();
                //var quotationid 			= $("#quotationid").val();
                var userEmail = $("#useremail").val();
                var weight = calc_weight;
                if (calc_weight != "" && calc_width != "" && calc_length != "" && calc_height != "") {
                    var volumetric_weight = (calc_width * calc_length * calc_height) / 5000;
                    weight = volumetric_weight > calc_weight ? volumetric_weight : calc_weight;
                }
                var quotationid = $("#quotation_id").val();
                $.post(
                        "quotation.php",
                        {action: 'SEND_EMAIL',
                            quotationid: quotationid,
                            calc_currency: calc_currency,
                            calc_scale: calc_scale,
                            calc_shipping_from: calc_shipping_from,
                            calc_shipping_to: calc_shipping_to,
                            service_type: service_type,
                            account: account, pieces: pieces,
                            calc_remark: calc_remark,
                            calc_weight: weight,
                            calc_width: calc_width,
                            calc_length: calc_length,
                            calc_height: calc_height,
                            userEmail: userEmail,

                        },
                        function (data)
                        {
                            alert(data);
                        });
            }



            function deleterecord(id)
            {
                if (id != "")
                {
                    if (confirm("Are you sure you want to delete this record?"))
                    {
                        $.post(
                                "quotation.php",
                                {action: 'CONFIRMED_DELETE', id: id},
                                function (data)
                                {
                                    alert(data);
                                    window.location = "../main/quotationlist.php";

                                });

                    }
                }

            }

            function editRecord(id)
            {
                if (id != "" && id != '0')
                {
                    $.ajax({
                        url: "quotation.php",
                        type: "POST",
                        async: false,
                        data: {
                            action: 'EDIT_RECORD',
                            id: id
                        },
                        success: function (data) {
                            var obj = JSON.parse(data);

                            $("#quotation_id").val(id);
                            $("#currency").val(obj[0].currency);
                            $("#calc_shipping_from").val(obj[0].shipping_from);
                            $("#calc_shipping_from").val(obj[0].shipping_from);

                            $("#calc_shipping_to").val(obj[0].shipping_to);
                            getCarrierNames($("#calc_shipping_to"), 'TO');




                            /*if(obj[0].carrier !=  "")
                             getServiceType('onload');*/


                            $("#service_type").val(obj[0].service_type);
                            $("#account").val(obj[0].account);
                            $("#calc_weight").val(obj[0].weight);
                            var piecesAll = parseInt(obj[0].pieces);
                            $("#pieces").val(obj[0].pieces);
                            $("#pieces").change();
                            $("#city").val(obj[0].city);
                            $("#postcode").val(obj[0].postcode);
                            $("#carrier").val(obj[0].carrierName);
                            $("#carrier").change();
                            $("#service_type").val(obj[0].service_type);
                            var lengthAll = (obj[0].length).split("&&");
                            var widthAll = (obj[0].width).split("&&");
                            var heightAll = (obj[0].height).split("&&");
                            var vilWeightAll = (obj[0].VolWeight).split("&&");

                            for (i = 0; i < piecesAll; i++)
                            {
                                $("#calc_length_" + i).val(lengthAll[i]);
                                $("#calc_width_" + i).val(widthAll[i]);
                                $("#calc_height_" + i).val(heightAll[i]);
                                $("#vol_wgt_" + i).val(vilWeightAll[i]);
                            }
                            calclulateweightTotal();

                            /*$("#calc_width").val(obj[0].width);
                             $("#calc_height").val(obj[0].height);
                             $("#volumn_weight").val(obj[0].VolWeight);
                             */

                            $("#calc_remark").val(obj[0].remark);
                            $("#screen_price").html(obj[0].tariff);
                            $("#calc_basic").val(obj[0].basic_charge);
                            $("#calc_fuel").val(obj[0].fuel_charge);
                            $("#calc_extra").val(obj[0].extra_charge);
                            $("#discount").val(obj[0].discount);
                            $("#useremail").val(obj[0].useremail);
                            $("#conversion").val(obj[0].conversion);
                            if (obj[0].price_type == 'user')
                                $("#price_type_user").click();
                            else if (obj[0].price_type == 'your')
                                $("#price_type_your").click();
                            else
                                $("#price_type_maual").click();
                            if (obj[0].discount_type == 'PERCENTAGE')
                            {
                                $('#discount_type').click();
                            }
                            $("#save").show();
                            if ($("#useremail").val != '')
                                $("#sendemail").show();
                            else
                                $("#sendemail").hide();

                            $("#print_quote").show();

                        }
                    });
                } else
                {
                    $("#carrier").val('');
                    $("#currency").val('');
                    $("#calc_shipping_from").val('');
                    $("#calc_shipping_from").val('');
                    $("#calc_shipping_to").val('');
                    $("#service_type").val('');
                    $("#account").val('');
                    $("#calc_weight").val('');
                    $("#pieces").val('');
                    $("#city").val('');
                    $("#postcode").val('');
                    $("#calc_length").val('');
                    $("#calc_width").val('');
                    $("#calc_height").val('');
                    $("#calc_remark").val('');
                    $("#screen_price").html('0.00');
                    $("#calc_basic").val('');
                    $("#calc_fuel").val('0.00');
                    $("#calc_extra").val('0.00');
                }
            }

        <?php
        if (@$_SESSION['menu-option'] == 'finance')
            echo 'jQuery(document).ready(function() { $("#add_new_quotation").click(); });';
        ?>
        </script>
        <?php
    }

    public function renderBody() {
        ?>

        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-List"></i>Quotation List </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller"   data-rail-color="blue" data-handle-color="blue">
                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-advance table-hover">
                            <thead>
                                <tr>
                                    <td colspan="6" style="text-align:center;">
                                        <a data-toggle="modal" title="Add new qoutation." data-target="#calc_modal" class="btn btn-warning" onClick="return editRecord('0');" >Add new Quotation</a>
                                        <a data-toggle="tooltip" title="View active qoutations." class="btn btn-primary" href="../main/quotationlist.php">ACTIVE</a>
                                        <a data-toggle="tooltip" title="View deleted qoutations." class="btn btn-danger" href="../main/quotationlist.php?status=deleted" >DELETED</a></td>
                                    <td colspan="2"  style="text-align:center;">
                                        <div class="form-group">
                                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-search "></i> </span>
                                                <input type='text' name='searchall' id='searchall' value='<?php if (isset($_POST['searchall'])) echo $_POST['searchall']; ?>' placeholder='Search Here' class="form-control">
                                            </div></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-red">Action</th>
                                    <th class="bg-red">Quotation No</th>
                                    <th class="bg-red">Account</th>
                                    <th class="bg-red">Service</th>
                                    <th class="bg-red">Weight</th>
                                    <th class="bg-red">Pieces</th>
                                    <th class="bg-red">Country</th>
                                    <th class="bg-red">Date Updated</th>
                                </tr>
                            </thead>
                            <tbody>
        <?php
        if (count($this->quotation) > 0) {


            foreach ($this->quotation as $quote) {

                //echo $curr->getCurrencyId().'gjgjh'; exit;
                ?>
                                        <tr>
                                            <td class="clsButton">
                                                <a  title="Edit" data-toggle="modal" data-target="#calc_modal"onClick="return editRecord(<?php echo $quote->getId(); ?>)" ><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp; 
                                        <?php
                                        if ($quote->getStatus() != 'DELETED') {
                                            ?>

                                                    <a  title="Create Consignment" href="../main/quotationlist.php?action=CREATE_SHIPPMENT&id=<?php echo $quote->getId(); ?>" target="_blank"><span class="glyphicon glyphicon-fullscreen"></span></a>&nbsp;&nbsp;&nbsp; 
                                                    <a href="javascript:;" onclick="return deleterecord(<?php echo $quote->getId(); ?>)" title="Remove"> <span class="glyphicon glyphicon-remove"></span></a>
                                            <?php
                                        }
                                        ?>


                                            </td>
                                            <td>QT-<?php echo $quote->getId(); ?></td>
                                            <td><?php echo $quote->getAccount(); ?></td>
                                            <td><?php echo $quote->getCarrier(); ?></td>
                                            <td><?php echo $quote->getWeight(); ?></td>
                                            <td><?php echo $quote->getPieces(); ?></td>
                                            <td><?php echo $quote->getShippingTo(); ?></td>
                                            <td><?php echo date("Y-m-d", $quote->getDateCreated()); ?></td>
                                        </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CALCULATOR DIV -->
        <div class="modal fade modal-transparent bs-modal-lg" id="calc_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-box">
                            <button type="button" class="close"  data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div class="cal-header">
                              <h3 class="heading-cal text-center"><!--<img src="../images/logo-calc.png">--> QUOTATION</h3>
                                <div class="cal-screen">
                                    <div class="col-md-2 col-sm-2 col-xs-2 text-center" id="screen_currency"></div>
                                    <div class="col-md-2 col-sm-2 col-xs-2 text-center" id="screen_scale"></div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 text-center" id="screen_dim"></div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 text-center" id="screen_from_to"></div>
                                    <div class="col-md-12"> <span class="cal-result">
                                            <h2 id="screen_price"  style="background:#000; color:#fff; padding:10px; width:100% ">0</h2>
                                        </span> </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="cal-body">

                                <div class="row">   
                                    <div class="col-sm-8">
                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <input id="quotation_id" name="quotation_id"  value="" type="hidden">
                                                <!-- <label class="control-label font-green-soft" for="calc_chargeable_weight"><strong>Chargeable Weight</strong></label>-->
                                                <input id="calc_chargeable_weight" name="calc_chargeable_weight" placeholder="Weight Charge" class="form-control chargeable_weight" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <!-- <label class="control-label font-green-soft" for="calc_chargeable_weight"><strong>Chargeable Weight</strong></label>-->
                                                <input id="useremail" name="useremail" placeholder="Email" class="form-control calculate_price_input" type="text"  >
                                            </div>
                                        </div>

                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <div id="accountdrop"> 
                                                    <script type="text/javascript">
                                                        $.post(
                                                                "quotation.php",
                                                                {action: 'ACCOUNTDROPDOWN'},
                                                                function (data)
                                                                {
                                                                    $('#accountdrop').html(data);
                                                                });
                                                    </script> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <div id="fromCountry"> 
                                                    <script type="text/javascript">
                                                        $.post(
                                                                "quotation.php",
                                                                {action: 'FROM_COUNTRY'},
                                                                function (data)
                                                                {
                                                                    $('#fromCountry').html(data);
                                                                });
                                                    </script> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <div id="toCountry"> 
                                                    <script type="text/javascript">
                                                        $.post(
                                                                "quotation.php",
                                                                {action: 'TO_COUNTRY'},
                                                                function (data)
                                                                {
                                                                    $('#toCountry').html(data);
                                                                });
                                                    </script> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <input id="city" name="city" placeholder="City" title="City" class="form-control calculate_price_input" type="text" >
                                            </div>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <input id="postcode" name="postcode" title="Postcode" placeholder="PostCode" class="form-control calculate_price_input" type="text" >
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="calc_total_weight_container"> 
                                                <input id="calc_weight" name="calc_weight" placeholder="Weight" class="form-control calculate_price_input"  type="text" title="Weight">
                                                <div id="calc_total_weight_error_container" class="help-block with-errors">Please enter valid weight</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <div id="radioBtn2" class="btn-group"> <a class="btn btn-warning  btn-md active" data-toggle="calc_scale" data-title="kg">KGS</a> <a class="btn btn-info  btn-md notActive" data-toggle="calc_scale" data-title="lb">LBS</a> </div>
                                                <input name="calc_scale" class="calculate_price" value="kg" id="calc_scale" type="hidden">
                                            </div>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <div id="carrierdrop"> 
                                                    <script type="text/javascript">
                                                        $.post(
                                                                "quotation.php",
                                                                {action: 'CARRIER'},
                                                                function (data)
                                                                {
                                                                    $('#carrierdrop').html(data);
                                                                });
                                                    </script> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <div class="form-group">
                                                <div id="servicetype"> </div>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <select id="pieces" name="pieces" class="form-control"  title="Pieces" onchange="addNewDimmBoxes(this);">
                                                    <option value="">Pieces</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>

                                                </select>
                                                <!--<input id="pieces" name="pieces" placeholder="Pieces" class="form-control calculate_price_input"  title="Pieces" type="text">-->
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <div id="loadCurrency"> 
                                                    <script type="text/javascript">
                                                        $.post(
                                                                "quotation.php",
                                                                {action: 'LOAD_CURRENCY'},
                                                                function (data)
                                                                {
                                                                    $('#loadCurrency').html(data);
                                                                });
                                                    </script> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <select id="conversion" name="conversion" class="form-control" title="Conversion">
                                                    <option value="">Conversion Rate</option>
                                                    <option value="2000">2000</option>
                                                    <option value="3000">3000</option>
                                                    <option value="4000">4000</option>
                                                    <option value="5000">5000</option>
                                                </select>
                                            </div>
                                        </div>



                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <!--<label class="control-label" for="calc_height">Height (cm)</label>-->
                                                <input title="discount" id="discount" name="discount" placeholder="Discount" class="form-control calculate_price_input" type="text">
                                            </div>   

                                        </div>
                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <!--<label class="control-label" for="calc_length">Length(cm)</label>-->


                                                <input id="calc_basic" title="Basic Charge"  name="calc_basic" placeholder="Basic Charge"  class="form-control chargeable_weight" type="text" readonly onkeyup="changeTheRatesValues();">
                                            </div>

                                        </div>
                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <!--<label class="control-label" for="calc_width">Width(cm)</label>-->
                                                <input id="calc_fuel" title="Fuel Charges"  name="calc_fuel" placeholder="Fuel Charges" class="form-control calculate_price_input"  type="text"  onkeyup="changeTheRatesValues();">
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <!--<label class="control-label" for="calc_height">Height (cm)</label>-->
                                                <input id="calc_extra" title="Extra Charges"  name="calc_extra" placeholder="Extra Charges" class="form-control calculate_price_input"  type="text"  onkeyup="changeTheRatesValues();">
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <input id="calc_vat" title="VAT"  name="calc_vat" placeholder="VAT Charges" class="form-control calculate_price_input"  type="hidden"  onkeyup="changeTheRatesValues();">
                                            </div>
                                        </div>

                                        <div class="col-sm-3"> 
                                            <div class="form-group">
                                                <input type="checkbox" name="discount_type" value="percentage" id="discount_type" ><label >PERCENT</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">

                                            <div class="md-radio-inline">
                                                <div class="md-radio">
                                                    <input type="radio" name="price_type" <?php
                                if (@$this->price_type == "all")
                                    echo "checked";
                                else if (@$this->price_type == "")
                                    echo "checked";
                                ?>  value="manual" id="price_type_maual" class="md-radiobtn" checked>
                                                    <label for="price_type_maual"> <span></span> <span class="check"></span> <span class="box"></span> Manual Price</label>
                                                </div>
                                                <div class="md-radio has-error">
                                                    <input type="radio" id="price_type_user" name="price_type" <?php if (@$this->price_type == "YES") echo "checked"; ?>  value="user" class="md-radiobtn" >
                                                    <label for="price_type_user"> <span></span> <span class="check"></span> <span class="box"></span> Member Price </label>
                                                </div>
                                                <div class="md-radio has-warning">
                                                    <input type="radio" id="price_type_your" name="price_type" <?php if (@$this->price_type == "NO") echo "checked"; ?> value="your" class="md-radiobtn">
                                                    <label for="price_type_your"> <span></span> <span class="check"></span> <span class="box"></span> Agent Price</label>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <textarea id="calc_remark"  title="Remark" name="calc_remark" rows="5" cols="60" class="form-control calculate_price_input" > </textarea>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input type="hidden" id="quotationid"  name="quotationid" value="0"  />
                                                <input id="save" name="save" value="Save" type="button" onClick="saveQuotation();" class="btn btn-primary" style="display:none;"/>
                                                <input id="calculate" name="calculate" value="Calculate" type="button" onClick="calculatePrice();"  class="btn btn-primary"/>
                                                <input id="sendemail" name="sendemail" value="EMAIL" type="button" onClick="sendEmail();"  class="btn btn-success" style="display:none;"/>
                                                <input id="print_quote" name="print_quote" value="VIEW QUOTE" type="button" onClick="printquote();"  class="btn btn-success" style="display:none;"/>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4" >
                                        <div class="form-group">
                                            <div  class="col-sm-12" id="dimention-section"></div>  
                                            <div id="calculationVolumn" style="display:none;">
                                                <div  class="col-sm-6">
                                                    <input id="volumn_weight_total" title="volumn_weight_total"  name="volumn_weight_total" placeholder="Vol Wght Total" class="form-control calculate_price_input"  type="text"  >
                                                </div>


                                                <div class="clearfix"></div>

                                                <br clear="all"/>
                                                <div class="form-group">
                                                    <div  class="col-sm-12">
                                                        <input id="calculate_volumn" name="calculate_volumn" value="Calculate Volumn" type="button" onClick="calclulateweightTotal();"  class="btn btn-primary"/></div>
                                                </div>
                                            </div>




                                        </div>

                                    </div>
                                </div>

                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="item-dimm-html" style="display:none;">
            <div class="item-dimenssion">
                <div class="col-sm-3 form-group" style="padding-left:3px !important; padding-right:2px !important;"> 
                    <!--<label class="control-label" for="calc_length">Length(cm)</label>-->
                    <input title="Length(cm)" id="calc_length####" name="calc_length[]" placeholder="Length(cm)" class="form-control calculate_price_input" onblur="calclulateweight('####');" type="text">
                </div>
                <div class="col-sm-3 form-group" style="padding-left:3px !important; padding-right:2px !important;"> 
                    <!--<label class="control-label" for="calc_width">Width(cm)</label>-->
                    <input title="Width(cm)" id="calc_width####" name="calc_width[]" placeholder="Width(cm)" class="form-control calculate_price_input"  type="text"  onblur="calclulateweight('####');" >
                </div>
                <div class="col-sm-3 form-group" style="padding-left:3px !important; padding-right:2px !important;">
                    <!--<label class="control-label" for="calc_height">Height (cm)</label>-->
                    <input title="Height(cm)" id="calc_height####" name="calc_height[]" placeholder="Height(cm)" class="form-control calculate_price_input"  type="text"  onblur="calclulateweight('####');" >
                </div>
                <div class="col-sm-3 form-group" style="padding-left:3px !important; padding-right:2px !important;"> 
                    <!--<label class="control-label" for="calc_height">Height (cm)</label>-->
                    <input title="Vol Wgt" id="vol_wgt####" name="vol_wgt[]" placeholder="Vol Wgt(cm)" class="form-control calculate_price_input" style="color:#000 !important;" type="text" readonly="readonly">
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
//		 Adminmenu::CURRENCY
        $menu = new Adminmenu();
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $user = SessionManager::getUser();
        // check admin user is authenticated
        if ($user->getUserType() != "customerservice" && $user->getUserType() != "admin") {
            util_redirect("index.php");
        }

        if (isset($_GET["action"]) && trim($_GET["action"]) == 'CREATE_SHIPPMENT') {
            $_SESSION['TARIFF_QOUTE_ID'] = $_GET["id"];
            util_redirect("../../main/consignment_edit.php?option=new");
            die;
        }

        if (isset($_GET["action"]) && trim($_GET["action"]) == 'REMOVE_QOUTATION') {
            $_SESSION['TARIFF_QOUTE_ID'] = $_GET["id"];
            util_redirect("../../main/consignment_edit.php?option=new");
            die;
        }


        //
        $this->setTitle("Admin Agent");


        /* ----------------------------------------------------------------------------- */
        if (isset($_POST["searchall"]) && trim($_POST["searchall"]) != '') {
            $searchApp = trim($_POST["searchall"]);

            $this->quotation_filter = new QuotationDetailsFilter();
            $this->quotation_filter->addFieldFilter("account", $searchApp);
            if (count($this->quotation_filter->getColumnList('account')) <= 0) {
                $this->quotation_filter = new QuotationDetailsFilter();
                $this->quotation_filter->addFieldFilter("carrier", $searchApp);
            }
            if (count($this->quotation_filter->getColumnList('carrier')) <= 0) {
                $this->quotation_filter = new QuotationDetailsFilter();
                $this->quotation_filter->addFieldFilter("shipping_to", $searchApp);
            }
        } else {
            $this->quotation_filter = new QuotationDetailsFilter();
        }
        if (isset($_GET["status"]) && trim($_GET["status"]) == 'deleted')
            $this->quotation_filter->addFieldFilter("status", 'DELETED');
        else
            $this->quotation_filter->addFieldFilter("status", 'ACTIVE');
        $this->quotation = $this->quotation_filter->getList();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
 