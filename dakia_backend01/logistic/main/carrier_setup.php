<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'errorlist.class'
                ],'visualcomponents');
include_classes([  
                    'agentdata.class',
                    'agentdatafilter.class',
                    'agentlog.class',
                    'agentlogfilter.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'country.class',
                    'countryfilter.class',
                    'invoices.class',
                    'invoicesfilter.class',
                    'servicecountrytime.class',
                    'servicecountrytimefilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'userservicesrouting.class',
                    'userservicesroutingfilter.class',
                    'serviceconstantfilter.class',
                    'serviceconstant.class',
                    'serviceconstantfilter.class.php',
                    'serviceagentmapping.class',
                    'serviceagentmappingfilter.class', 
                    'licenceplate.class',
                    'licenceplatefilter.class',
                    'carrierservicecustomizerules.class',
                    'carrierservicecustomizerulesfilter.class',
                    'serviceconstantvalue.class',
                    'serviceconstantvaluefilter.class',
                    'servicerangemapping.class',
                    'servicerangemappingfilter.class',
    
    ]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-multi-select.min.js" type="text/javascript"></script>

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->



        <script>
            $(document).ready(function () {
                $('.button-next').click(function () {

                    var tab_number = $.trim($(".nav-pills > li.active").find('a').find('span.number').html());
                    var next_tab_number = ( parseInt(tab_number) + 1 );
                    var get_carrier_setupid = $("#get_carrier_setupid").val();
                    $('.nav-pills > li.active').next('li').find('a').trigger('click');
                    if ($.trim(next_tab_number) == '3') {
                        $("#get_carrier_option").val("fields");
                        $('.shipping_carrier [data-id="' + get_carrier_setupid + '"]').click();
                    }
                    else if ($.trim(next_tab_number) == '2') {
                        $("#get_carrier_option").val("services");
                        $('.shipping_carrier [data-id="' + get_carrier_setupid + '"]').click();
                    }

                    var listArray = [];
                    $("#agent_services > option:selected").each(function () {
                        listArray.push('<li>' + this.text + '</li>');
                    });

                    if ($.trim(next_tab_number) == '3' && listArray.length <= 0) {
                        $(".display-service-error").show();
                        $("#display-service-message").html("Please select services then click continue.");
                        $('.btnPrevious').click();
                    }
                    else
                        $(".display-service-error").hide();
                    if ($.trim(next_tab_number) == '4') {
                       var listArrayHtml = "<ul>";
                        $("#agent_services > option:selected").each(function () {
                            listArrayHtml += '<li>' + this.text + '</li>';
                        });
                        listArrayHtml += "</ul>";
                        $(".confirm_carrier_details").html(listArrayHtml);
                        var listArrayFieldsHtml = '<div class="row">';
                        $( 'input[type="text"]' ).each(function( index ) {
                            var fieldName = $(this).attr('name');//.charAt(0).toUpperCase() + this.substr(1);
                            var fieldValue = $(this).val();
                            if(fieldName != 'get_carrier_setupid' &&  fieldName != 'get_carrier_option')
                            listArrayFieldsHtml += '<div  class="col-md-4"><strong>' + fieldName.toUpperCase().replace(/\_/g," ") +" </strong>: " + fieldValue + '</div>';
                        });
                        listArrayFieldsHtml += "</div>";

                        $(".confirm_contact_details").html(listArrayFieldsHtml);

                    }

                });

                $('.button-previous').click(function () {
                    var next_tab_number = $('.nav-pills > .active').prev('li').find('a').find('span.number').html();

                    var get_carrier_setupid = $("#get_carrier_setupid").val();
                    $('.nav-pills > .active').prev('li').find('a').trigger('click');

                    if ($.trim(next_tab_number) == '1') {
                        $("#get_carrier_option").val('');
                        $(".button-next").addClass("disabled");
                    }

                });
            });
        </script>

        <script src="../assets/pages/scripts/form-wizard.js" type="text/javascript"></script>
        <?php
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderFooter()
    {
        ?>
        <script type="text/javascript">
            var eventCarrierId = 0;

            $(document).ready(function () {




                getAllServicesList();
                getAllCarrierList();

                $('input').tooltip();
                $('select').tooltip();
                $('a').tooltip();


                //Save setup agreement
                $(document).on('click', '#setup_agree', function () {
                    $.ajax({
                        type: "POST",
                        url: "carrier_setup.php",
                        data: {func: "save_setup_agreement"},
                        success: function () {
                            $('#service-modal-popup').modal('toggle');
                            getCarrierList();
                        },
                        error: function () {
                            $('#res_message').removeClass('alert-success').addClass('alert-danger');
                            $('#res_message').html("Some error occurred");
                            $('#res_message').show();
                        }
                    });
                });

                //Carrier agreement Popup

                $(document).on('click', '#serviceagreement_popup', function () {
                    var e = $(this);
                    var carrier_id = e.data('courierid');
                    $('#carrier_id').val(carrier_id);
                   // $('#carrier-modal-popup').modal('show');
                });

                //Save carrier usage agreement
                $(document).on('click', '#carrier_agree', function () {
                    var carrier_id = $('#carrier_id').val();
                    $.ajax({
                        type: "POST",
                        url: "carrier_setup.php",
                        data: {func: "save_carrier_agreement", carrier_id: carrier_id},
                        success: function () {
                            //$('#carrier-modal-popup').modal('toggle');
                            $('#service-modal-popup').modal('toggle');

                            getAllServicesList();
                        },
                        error: function () {
                            $('#res_message').removeClass('alert-success').addClass('alert-danger');
                            $('#res_message').html("Some error occurred");
                            $('#res_message').show();
                        }
                    });
                });

                //LIST OF ALL CARRIER FOR MY CONTRACT
                $(document).on('click', '#save_bulk', function () {
                    //handleTitle($("#tab1"), $(".nav-pills"), 0);
                    $('.nav-pills a:first').tab('show');
                    $('#get_carrier_option').val('');
                    //$('#form_wizard_1').step({});
                    $("#selected-carrier-here").html("Please select carrier and then press continue button.");
                    $("#own-carrier-popup").modal('show');
                    getCarrierList();
                });

                //DESIGN OF SET UP MY CONTRACT
                $(document).on('click', '.shipping_carrier', function () {

                    var e = $(this);
                    var carrier_id = e.data('id');
                    var carrier_name = e.data('name');
                    $("#selected-carrier-here").html("You have selected <b>"+carrier_name+"</b> as carrier.");
                    $("#get_carrier_setupid").val(carrier_id);
                    //var get_carrier_option = $("#get_carrier_option").val("services");

                    var get_carrier_option = $("#get_carrier_option").val();
                    $.ajax({
                        type: "POST",
                        url: "carrier_setup.php",
                        data: {get_all: "get_carrier_service_setupform", carrier_id: carrier_id, carrier_name: carrier_name, get_carrier_option:get_carrier_option},
                        success: function (data) {
                            if($.trim(get_carrier_option) == 'services') {
                                $('#services-detail-setup').html("");
                                $('#services-detail-setup').html(data);
                            }
                            else if($.trim(get_carrier_option) == 'fields') {
                                $('#contract-detail-setup').html("");
                                $('#contract-detail-setup').html(data);
                            }
                            $('.multiselect_drop_down').multiSelect();
                            $(".EDI").hide();
                            $(".API").hide();
                            $(".SOFTWARE").hide();

                            $("#INTEGRATION_TYPE").change(function (){
                                //DELETE MY CONTRACT FUNCTIONALITY
                                $(".EDI").hide();
                                $(".API").hide();
                                $(".SOFTWARE").hide();

                                var e = $(this);
                                if(e.val() == 'API')
                                {
                                    $(".API").show();
                                }
                                else if(e.val() == 'EDI')
                                {
                                    $(".EDI").show();
                                }
                                else if(e.val() == 'SOFTWARE')
                                {
                                    $(".SOFTWARE").show();
                                }


                            });
                            if($.trim(get_carrier_option) == '')
                                $(".button-next").click();
                        },
                        error: function () {
                            $('#res_message').removeClass('alert-success').addClass('alert-danger');
                            $('#res_message').html("Some error occurred");
                            $('#res_message').show();
                        }
                    });
                });




                //SAVE CARRIER CONTRACT
               /* $(document).on('click', '.button-submit', function () {
                    $('#error_message').hide();
                    $('#success_message').html("").hide();
                    $('#submit_form').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented()) {
                            return false;
                        } else {
                            $.ajax({
                                method: "POST",
                                url: "carrier_setup.php",
                                data: $('#submit_form').serialize(),
                                dataType: 'json'
                            }).done(function (data) {
                                $('#error_message').removeClass('alert-danger').addClass('alert-danger');
                                if (data.status == "ERROR") {
                                    $('#error_message').html(data.message);
                                    $('#error_message').show();
                                } else {
                                    getAllCarrierList( true );
                                    $('#success_message').removeClass('alert-danger').addClass('alert-success');
                                    $('#success_message').html("You have successfully added your own carrer contract");
                                    $('#success_message').show();
                                }

                            });
                            return false;
                        }
                    });
                  //  $("#submit_form").submit();
                });
*/
                // LOAD SERVICES BASED ON CARRIER
                $(document).on('click', '.event-carrierload', function () {
                    var e = $(this);
                    //alert(window.eventCarrierId);
                    var carrier_name = e.data('carrier_name');
                    var courier = e.data('courierid');
                    if (window.eventCarrierId != courier && window.eventCarrierId != 0) {
                        return;
                    }
                    var url = e.data('carrierload');
                    var action = e.data('action');
                    var courierimage = e.data('courierimage');

                    $("#message_download_csv_service").hide();
                    var carrierId = $(this).attr("data-courierid");
                    var serviceDisplayName = $(this).data("carrier_display_name");
                    $("#carrier-content-display-wait").html('Please wait, we are dealing with your request.');


                    var cut_of_time = e.data('cut_of_time');
                    var carrier_display_name = e.data('carrier_display_name');
                    var carrier_status = e.data('carrier_status');
                    var carrier_country = e.data('carrier_country');
                    var carrier_country_iso = e.data('carrier_country_iso');
                    var carrier_remotearea_check = e.data('carrier_remotearea_check');
                    var user_service_status = e.data('user_service_status');

                    $("#carrier_name").html(carrier_name);
                    $.post(url, {
                        func: action,
                        carrier_name: carrier_name,
                        courier: courier,
                        courierimage: courierimage,
                        carrier_display_name: carrier_display_name,
                        cut_of_time: cut_of_time,
                        carrier_status: carrier_status,
                        carrier_country_iso: carrier_country_iso,
                        carrier_country: carrier_country,
                        carrier_remotearea_check: carrier_remotearea_check,
                        user_service_status: user_service_status
                    }, function (d) {
                        $("#carrier-content-display").html(d);
                        $("#export_remotearea_service").data("carrier_id", carrierId);
                        $("#console_window_service").hide();
                    });
                });

                //CHANGE ALL SERVICES STATUS
                $(document).on('click', '.carrier_status', function () {

                    var e = $(this);
                    changeAllServiceStatus(e);
                });
                //EDIT MY CONTRACT
                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var carrier_id = e.data('carrier_id');
                    var agent_id = e.data('agent_id');
                    var service_id = e.data('service_id');

                    $('#edit-form-content #carrier_id').val(carrier_id);
                    $('#agent_id').val(agent_id);
                    $('#edit-contract-success-messages').hide();

                    $.ajax({
                        type: "POST",
                        url: "carrier_setup.php",
                        data: {get_all: "get_carrier_setupform", carrier_id: carrier_id, agent_id: agent_id, service_id: service_id },
                        success: function (data) {
                            
                            $('#edit-carrier-contents').html("");
                            $('#edit-carrier-contents').html(data);
                            $('.multiselect_drop_down').multiSelect();

                            $('#edit-carrier-popup').modal("show");
                            $("#INTEGRATION_TYPE").change(function (){
                                //DELETE MY CONTRACT FUNCTIONALITY
                                $(".EDI").hide();
                                $(".API").hide();
                                $(".SOFTWARE").hide();

                                var e = $(this);
                                if(e.val() == 'API')
                                {
                                    $(".API").show();
                                }
                                else if(e.val() == 'EDI')
                                {
                                    $(".EDI").show();
                                }
                                else if(e.val() == 'SOFTWARE')
                                {
                                    $(".SOFTWARE").show();
                                }


                            });
                            $("#INTEGRATION_TYPE").change();

                             $(".bs-select").selectpicker();
                        },
                        error: function () {
                            $('#res_message').removeClass('alert-success').addClass('alert-danger');
                            $('#res_message').html("Some error occurred");
                            $('#res_message').show();
                        }
                    });
                });
                $(document).on('click', '#btn_edit_save', function () {
                    $.ajax({
                        type: "POST",
                        url: "carrier_setup.php",
                        data: $('#edit-form-content').serialize(),
                        dataType: 'json',
                        success: function (data) {
                            $('#edit-contract-success-messages').removeClass('alert-danger').addClass('alert-danger');
                            if (data.status == "ERROR") {
                                $('#edit-contract-success-messages').html(data.message);
                                $('#edit-contract-success-messages').show();
                            } else {
                                $('#edit-contract-success-messages').removeClass('alert-danger').addClass('alert-success');
                                $('#edit-contract-success-messages').html("You have successfully added your own carrer contract");
                                $('#edit-contract-success-messages').show();
                            }
                        },
                        error: function () {
                            $('#edit-contract-success-messages').removeClass('alert-success').addClass('alert-danger');
                            $('#edit-contract-success-messages').html("Some error occurred");
                            $('#edit-contract-success-messages').show();
                        }
                    });
                });



                //DELETE MY CONTRACT FUNCTIONALITY
                $(document).on('click', '.btndelete', function () {
                    var e = $(this);
                    var agent_id = e.data('agent_id');
                    var statusdata = e.data('status');

                    var contract_name = e.data('contract_name');
                    if(statusdata == '0')
                        var displaymes  =" Are you sure you want to active contract?";
                    else
                        var displaymes  =" Are you sure you want to inactive contract?";
                    swal({
                            title: displaymes,
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
                                $.ajax({
                                    type: "POST",
                                    url: "carrier_setup.php",
                                    data: {func: "DELETE_CONTRACT", agent_id: agent_id, contract_name: contract_name, status:statusdata},
                                    dataType: "json",

                                    success: function (data) {
                                        swal(
                                            data.status,
                                            data.message,
                                            data.status.toLowerCase()
                                        )
                                        getAllCarrierList();

                                    },
                                    error: function () {
                                    }

                                });

                            }

                        });
                });

            });

            function getCarrierList() {
                $.ajax({
                    type: "POST",
                    url: "carrier_setup.php",
                    data: {get_all: "carrier_list"},

                    success: function (data) {
                        $('#show_carrier_to_select').html("");
                        $('#show_carrier_to_select').html(data);
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }


            function changeAllServiceStatus(e) {

                var status = e.data('status');
                var carrier_name = e.data('carrier_name');
                var carrier_id = e.data('carrier_id');
                var showservice = e.data('showservice');

                if (status == 'active') {
                    var message = "activate";
                } else {
                    var message = "deactivate";
                }
                var confirmationMessage = "Are you sure you want to " + message + " all the services related to this Courier?";
                confirmationMessage = confirmationMessage.replace("#name#", carrier_name);
                confirmationMessage = confirmationMessage.replace("#status#", status);
                confirmationMessage = confirmationMessage.replace("#status#", status);
                swal({
                        title: confirmationMessage,
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
                            $.ajax({
                                type: "POST",
                                url: "carrier_setup.php",
                                data: {
                                    func: "CHANGE_STATUS_SERVICES",
                                    carrier_name: carrier_name,
                                    carrier_id: carrier_id,
                                    status: status
                                },
                                dataType: "json",

                                success: function (data) {
                                    if (status == 'active') {
                                        $('.modal-body table').find('.is_status').bootstrapSwitch('state', true, true);
                                        e.removeClass('ribbon-color-danger').addClass('ribbon-color-success');
                                        e.data('status', 'inactive')
                                        e.html('Active');
                                    } else if (status == 'inactive') {
                                        $('.modal-body table').find('.is_status').bootstrapSwitch('state', false, true);
                                        e.removeClass('ribbon-color-success').addClass('ribbon-color-danger');
                                        e.data('status', 'active')
                                        e.html('Inactive');
                                    }

                                    if (showservice != "displaynon") {

                                        //window.eventCarrierId = carrier_id;

                                        //$(".event-carrierload").click();
                                        getAllServicesList();
                                    }

                                },
                                error: function () {
                                    $('#res_message').removeClass('alert-success').addClass('alert-danger');

                                }

                            });
                            //                   if(showservice != "displaynon")
                            //                        $(".event-carrierload").click();

                        }

                    });
            }

            //ACTIVATE INDIVIDUAL SERVICES
            function serviceActivation(state, serviceId, serviceName) {
                if (state) {
                    var message = "activate";
                } else {
                    var message = "deactivate";
                }
                swal({
                        title: "Are you sure you want to " + message + " " + serviceName + " service?",
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

                            $.ajax({
                                type: "POST",
                                url: "carrier_setup.php",
                                data: {action: "deactive_individual_service", serviceId: serviceId, status: state},
                                dataType: "json",
                                success: function (data) {
                                    swal(
                                        data.STATUS,
                                        data.MESSAGE,
                                        data.STATUS.toLowerCase()
                                    )
                                },
                                error: function () {
                                    $('#res_message').removeClass('alert-success').addClass('alert-danger');

                                }
                            });

                        }
                    });
            }

            //GET ALLOWED COUNTRY FOR SERVICES
            $(document).on('click', '.event-servicecountry', function () {
                $("#service-country-content-display").html('Please wait, we are dealing with your request.');
                var e = $(this);

                var serviceid = e.data('serviceid');
                var servicename = e.data('servicename');
                var url = e.data('carrierload');
                var action = e.data('action');

                $("#serviceCountryName").html(servicename);
                $.post(url, {func: action, serviceid: serviceid, servicename: servicename}, function (d) {
                    $("#service-country-content-display").html(d);
                    $("#service-country-popup").modal('show');
                });
            });

            function displayAllContract() {
                getAllCarrierList();
            }

            //Get All ajax based services list
            //Get All ajax based carrier List
            function getAllServicesList() {
                $.ajax({
                    type: "POST",
                    url: "carrier_setup.php",
                    data: {get_all: "services_list"},
                    success: function (data) {
                        $('#append_all_service_list').html("");
                        $('#append_all_service_list').html(data);
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }

            //Get All ajax based carrier List
            function getAllCarrierList(displaymessag = false) {
                $.ajax({
                    type: "POST",
                    url: "carrier_setup.php?display="+displaymessag,
                    data: {get_all: "shipping_contract_list"},
                    success: function (data) {
                        $('#append_all_carrier_list').html("");
                        $('#append_all_carrier_list').html(data);
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }

            function numbersonly(e) {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8) {
                    if (unicode == 46) {
                    } else if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
                }
            }
        </script>
        <?php
    }

    protected function init()
    {

        $user = SessionManager::getUser();
//            if ($user->getUserType() != "admin")
//                    util_redirect("index.php");
         $this->breadCrumb['data'] = array( 
                    'index.php'=>Translation::GetCaption("HOME"),
                    'carrier_setup.php'=>'Carrier Setup'
            );
        
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_WAREHOUSE_LIST);
        t_on(); // turn on trace for this page

        if (isset($this->form_vars['carrier_id'])) {
            $carrier_id = $this->form_vars['carrier_id'];
        } else if (isset($this->form_vars['id'])) {
            $carrier_id = $this->form_vars['id'];
        }
        // CARRIER SELECTION TAB DISPALY

        if (isset($this->form_vars["form_action"]) && $this->form_vars["form_action"] == "edit_record_contract")
        {
            $output = array();
            $carrier_id = $this->form_vars["carrier_id"];
            $agentId = $this->form_vars["agent_id"];
            $carrier_name = $this->form_vars["carrier_name"];
            $services = $this->form_vars["agent_services"];
            $fromWeight = $this->form_vars["from_weight"];
            $toWeight = $this->form_vars["to_weight"];
            $rangeStart = $this->form_vars["range_start"];
            $rangeEnd = $this->form_vars["range_end"];
            $prefix = $this->form_vars["prefix"];
            $suffix = $this->form_vars["suffix"];


        if (sizeof($services) > 0) {
            if ($agentId <= 0) {
                // CREATE NEW AGENT
                $agentcode      = $carrier_name . "_" . $user->getUserAccountId();
                $agentName      = $carrier_name . "_" . $user->getUserAccountId();
                $contactName    = $user->getFirstName();
                $address        = explode(",", $user->getReturnAddress());
                $country        = $user->getCountryId();
                $telephone      = $user->getTelephone();
                $email      = $user->getEmail();

                $agentFilterObj = new AgentDataFilter($agentId);
                $agentFilterObj->addFieldFilter("agent_code", $agentcode);
                $agentList = $agentFilterObj->getList();
                if (count($agentList) > 0) {
                    $agentcode = $carrier_name . "_" . time();
                }
                $agentObj = new AgentData($agentId);
                $agentObj->setAgentCode($agentcode);
                $agentObj->setAgentName($agentName);
                $agentObj->setActive(1);
                $agentObj->setContactName($contactName);
                $agentObj->setAddressLine1(@$address[0]);
                $agentObj->setAddressLine2(@$address[1]);
                $agentObj->setCity(@$address[2]);
                $agentObj->setCountryId($country);
                $agentObj->setTelephone($telephone);
                $agentObj->setEmail($email);
                $agentObj->setUserId($user->getUserAccountId());
                $agentObj->setDateCreated(date("Y-m-d H:i:s"));
                $agentObj->setAgentType("carrier");
                $agentObj->save();

                $agentId = $agentObj->getId();
                $newAgentObj = $agentObj;
                $agentLog = new AgentLog();
                $agentLog->createlog($user->getId(), '', $agentId, 'AGENT', $user->getUserName() . ' has added new agent by adding new contract ' . $agentcode, '', $newAgentObj);
            }


            //FETCH CONSTANT FOR THAT CARRIER
            $serviceConstantObj = new ServiceConstantFilter();
            $serviceConstantObj->addFilter("carrier_id = '" . $carrier_id . "' or carrier_id = 0");
            $serviceConstantObj->AddOrderBy("sort_order");
            $serviceConstantList = $serviceConstantObj->getList(false);

            if ($this->form_vars["agent_id"] > 0) {
                $agentDataObj = new AgentData($this->form_vars["agent_id"]);
                $agentcode = $agentDataObj->getAgentCode();
                $agentDataObj->setActive(1);
                $agentDataObj->setDateCreated(date("Y-m-d H:i:s"));
                $agentDataObj->save();
                $serAgeFilter = new ServiceAgentMappingDataFilter();
                $serAgeFilter->addFilter(" agentid = '" . $agentId . "'");
                $serAgeList = $serAgeFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");
                if (count($serAgeList) > 0) {
                    $currentServices = array();
                    foreach ($serAgeList as $serv) {
                        $currentServices[] = $serv->getServiceid();
                    }
                    $differentServices = array_diff($currentServices, $services);
                    if (sizeof($differentServices) > 0) {
                        foreach ($differentServices as $diffServiceId) {
                            $safilter = new ServiceAgentMappingDataFilter();
                            $safilter->addFilter(" agentid = '" . $agentId . "' and serviceid = '" . $diffServiceId . "'");
                            $differenceAgentList = $safilter->getColumnList(" id");
                            if (count($differenceAgentList) > 0) {
                                foreach ($differenceAgentList as $agentid) {
                                    //DELETE FROM AGENT SERVICE MAPPING
                                    ServiceAgentMapping::deleteById($agentid->getId());

                                    //DEACTIVE SERVICE IN USER SERVICE ROUTING
                                    $diffuserServiceRouting = new UserServicesRoutingFilter();
                                    $diffuserServiceRouting->addFilter(" user_account_id = '" . $user->getUserAccountId() . "' and service_id = '" . $diffServiceId . "'");
                                    $diffuserServiceList = $diffuserServiceRouting->getList();
                                    if (count($diffuserServiceList) > 0) {
                                        foreach ($diffuserServiceList as $diffulist) {
                                            $diffulist->setStatus(0);
                                            $diffulist->save();
                                        }
                                    }

                                    //DEACTIVE FROM CUSTOMIZE RULE
                                    $diffcarrierServiceCustomizeRulesFilterObj = new carrierServiceCustomizeRulesFilter();
                                    $diffcarrierServiceCustomizeRulesFilterObj->addFilter(" serviceid = '" . $diffServiceId . "' and agentid = '" . $agentId . "' and user_account_id = '" . $user->getUserAccountId() . "'");
                                    $diffcarrierSerCutList = $diffcarrierServiceCustomizeRulesFilterObj->getList();

                                    if (count($diffcarrierSerCutList) > 0) {
                                        foreach ($diffcarrierSerCutList as $diffcarrList) {
                                            $diffcarrList->setStatus(0);
                                            $diffcarrList->save();
                                        }
                                    }

                                    //DELETE CONSTANT
                                    $diffserviceConstantValueFilter = new ServiceConstantValueFilter();
                                    $diffserviceConstantValueFilter->addFilter(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                    $diffserviceConsList = $diffserviceConstantValueFilter->getList();
                                    if (count($diffserviceConsList) > 0) {
                                        ServiceConstantValue::deleteConstant(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                    }

                                    //DELETE RANGES
                                    $diffserviceRangeMappingFilter = new ServiceRangeMappingFilter();
                                    $diffserviceRangeMappingFilter->addFilter(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                    $diffserviceRangeList = $diffserviceRangeMappingFilter->getList();
                                    if (count($diffserviceRangeList) > 0) {
                                        ServiceRangeMapping::deleteRange(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //ADD RANGES
            if ($rangeStart > 0 && $rangeEnd > 0) {
                $licencePlateFilterObj = new LicencePlateFilter();
                $licencePlateFilterObj->addFilter(" range_name = '" . $agentcode . "' and addedby = '" . $user->getId() . "'");
                $licencePlateList = $licencePlateFilterObj->getList();

                if (count($licencePlateList) > 0) {
                    $licencePlate = $licencePlateList[0];
                } else {
                    $licencePlate = new LicencePlate();
                    $licencePlate->setDateCreated(date("Y-m-d H:i:s"));
                    $licencePlate->setAddedby($user->getId());
                }

                $licencePlate->setRangeName($agentcode);
                $licencePlate->setRangeStart($rangeStart);
                $licencePlate->setRangeEnd($rangeEnd);
                $licencePlate->setNextNumber($rangeStart);
                $licencePlate->setPrefix($prefix);
                $licencePlate->setSufix($suffix);
                $licencePlate->setUpdatedBy($user->getId());
                $licencePlate->setDateUpdated(time());
                $licencePlate->save();
                $licencePlateId = $licencePlate->getId();
            }
            
            
            foreach ($services as $ser) {
                //SAVE EACH SERVICE WITH AGENT
                $serviceAgentFilter = new ServiceAgentMappingDataFilter();
                $serviceAgentFilter->addFilter(" agentid = '" . $agentId . "' and serviceid = '" . $ser . "'");
                $SerAgentList = $serviceAgentFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");

                if (count($SerAgentList) > 0) {
                    $serviceAgentObj = $SerAgentList[0];
                    $old_fromweight = $serviceAgentObj->getFromWeight();
                    $old_toweight = $serviceAgentObj->getToWeight();
                } else {
                    $serviceAgentObj = new ServiceAgentMapping();
                }
                $serviceAgentObj->setServiceid($ser);
                $serviceAgentObj->setagentid($agentId);
                $serviceAgentObj->setFromWeight($fromWeight);
                $serviceAgentObj->setToWeight($toWeight);
                $serviceAgentObj->save();


                //CHECK SERVICE IS UNABLE TO THE USER

                $serviceCountryTimeObj = new ServiceCountryTimeFilter();
                $serviceCountryTimeObj->addFilter("id_service = '" . $ser . "'");
                $serviceCountryTimeList = $serviceCountryTimeObj->getList();

                if (count($serviceCountryTimeList) > 0) {
                    foreach ($serviceCountryTimeList as $serviceCountryList) {
                        $userServiceRouting = new UserServicesRoutingFilter();
                        $userServiceRouting->addFilter(" user_account_id = '" . $user->getUserAccountId() . "' and service_id = '" . $ser . "' and country_id = '" . $serviceCountryList->getIdCountry() . "'");
                        $userServiceList = $userServiceRouting->getList();
                        if (count($userServiceList) > 0) {
                            foreach ($userServiceList as $ulist) {
                                $usrStatus = $ulist->getStatus();
                                if ($usrStatus == 0) {
                                    $ulist->setStatus(1);
                                    $ulist->save();
                                }
                            }
                        } else {

                            // ADD RECORD FOR EACH SERVICE TO ACTIVATE FOR USER
                            $userServiceRoutingObj = new UserServicesRouting();
                            $userServiceRoutingObj->setUserAccountId($user->getUserAccountId());
                            $userServiceRoutingObj->setCountryId($serviceCountryList->getIdCountry());
                            $userServiceRoutingObj->setFromWeight($fromWeight);
                            $userServiceRoutingObj->setToWeight($toWeight);
                            $userServiceRoutingObj->setStatus(1);
                            $userServiceRoutingObj->setServiceId($ser);
                            $userServiceRoutingObj->setIsRemoteArea(0);
                            $userServiceRoutingObj->setAddedBy($user->getId());
                            $userServiceRoutingObj->save();
                        }
                    }
                }


                //ADD RULE IN CUSTOMIZE RULES
                if ($this->form_vars["agent_id"] > 0) {

                    $carrierServiceCustomizeRulesFilterObj = new carrierServiceCustomizeRulesFilter();
                    $carrierServiceCustomizeRulesFilterObj->addFilter(" serviceid = '" . $ser . "' and agentid = '" . $agentId . "' and user_account_id = '" . $user->getUserAccountId() . "'");
                    $carrierSerCutList = $carrierServiceCustomizeRulesFilterObj->getList();

                    if (count($carrierSerCutList) > 0) {
                        $carrierServiceCustomizeRulesObj = $carrierSerCutList[0];
                    } else {
                        $carrierServiceCustomizeRulesObj = new carrierServiceCustomizeRules();
                    }
                } else {
                    $carrierServiceCustomizeRulesObj = new carrierServiceCustomizeRules();
                }

                $carrierServiceCustomizeRulesObj->setServiceId($ser);
                $carrierServiceCustomizeRulesObj->setAgentId($agentId);
                $carrierServiceCustomizeRulesObj->setUserAccountId($user->getUserAccountId());
                $carrierServiceCustomizeRulesObj->setFromWeight($fromWeight);
                $carrierServiceCustomizeRulesObj->setToWeight($toWeight);
                $carrierServiceCustomizeRulesObj->setStatus(1);
                $carrierServiceCustomizeRulesObj->save();
               
                //ADD CONSTANT VALUES
                if (count($serviceConstantList) > 0) {
                    $constant = array();
                    foreach ($serviceConstantList as $serviceConstant) {


                        if (trim($this->form_vars[$serviceConstant->getConstant()]) != '') {
                            $constantId = $serviceConstant->getId();
                            $serviceConstantValueFilter = new ServiceConstantValueFilter();
                            $serviceConstantValueFilter->addFilter(" service_id = '" . $ser . "' and agent_id = '" . $agentId . "' and constant_id = '" . $constantId . "'");
                            $serviceConsList = $serviceConstantValueFilter->getList();

                            if (count($serviceConsList) > 0) {
                                $serviceConstantValueObj = $serviceConsList[0];
                            } else {
                                $serviceConstantValueObj = new ServiceConstantValue();
                            }

                            $serviceConstantValueObj->setConstantValue($this->form_vars[$serviceConstant->getConstant()]);
                            $serviceConstantValueObj->setServiceId($ser);
                            $serviceConstantValueObj->setAgentId($agentId);
                            $serviceConstantValueObj->setConstantId($constantId);
                            $serviceConstantValueObj->setDateCreated(date("Y-m-d H:i:s"));
                            $serviceConstantValueObj->setAddedBy($user->getId());
                            $serviceConstantValueObj->save();
                        }
                    }
                }

                //MAP RANGES WITH SERVICES
                $serviceRangeMappingFilterObj = new ServiceRangeMappingFilter();
                $serviceRangeMappingFilterObj->addFilter(" service_id = '" . $ser . "' and agent_id = '" . $agentId . "'");
                $serviceRangeMappingList = $serviceRangeMappingFilterObj->getList();
                if (count($serviceRangeMappingList) > 0) {
                    $serviceRangeMappingObj = $serviceRangeMappingList[0];
                } else {
                    $serviceRangeMappingObj = new ServiceRangeMapping();
                }
                $serviceRangeMappingObj->setServiceId($ser);
                $serviceRangeMappingObj->setAgentId($agentId);
                $serviceRangeMappingObj->setLicencePlateId($licencePlateId);
                $serviceRangeMappingObj->save();


                $output["status"] = "SUCCESS";
                $output["message"] = formatMessages(SUCCESS_CONTRACT_ADDED);
            }
        } else {
            $output["status"] = "ERROR";
            $output["message"] = formatMessages(ERROR_SELECT_SERVICE);
        }
        echo json_encode($output);
        exit;
    }
        else if (isset($this->form_vars["get_all"]) && $this->form_vars["get_all"] == "services_list") {
            $carrierFilter = new CarrierFilter();
            $carrierFilter->addFilter(" usr.user_account_id = '" . $user->getUserAccountId() . "' and cl.status = 1");
          /*  $carrierFilter->addFilter(" service_id NOT IN (SELECT
                serviceid
            FROM
                agent_data a
                    INNER JOIN
                service_agent_mapping sam ON sam.agentid = a.id AND a.user_id = '148')");*/
            //$carrierFilter->AddOrderBy('carrier', true);
            $carrierFilter->setGroup(" carrier");
            $carrierResult = $carrierFilter->getCarrierSetupList("cl.*,cn.name 'country_name',cn.iso 'country_iso', s.name as service_name, s.from_weight, s.to_weight, s.id as service_id, usr.status as user_service_status, usr.is_agreed as is_agreed ");
            $carrierFilterCount = 0;
            $html = "";
            $count = 1;

            if (count($carrierResult) > 0) {
                foreach ($carrierResult as $rowImage) {

                    $statusButton = 0;
                    $carrierStatusFilter = new CarrierFilter();
                    $carrierStatusFilter->addFilter(" usr.user_account_id = '" . $user->getUserAccountId() . "' and usr.status = 1 and cl.id = '" . $rowImage->getId() . "'");
                    $carrierStatusFilter->AddOrderBy('carrier', true);
                    $carrierStatusResult = $carrierStatusFilter->getCarrierSetupList("cl.*");
                    if (count($carrierStatusResult) > 0) {
                        $statusButton = 1;
                    }

                    $carrierName = $rowImage->getCarrier();
                    $carrierId = $rowImage->getId();
                    $cutOffTime = 'Cut Off - ' . $rowImage->getCutOfftime();
                    $displayName = $rowImage->getCarrierDisplayName();
                    $status = $rowImage->getUserServiceStatus();

                    $countryIso = $rowImage->getCountryIso();
                    $countryName = $rowImage->getCountryName();
                    $currencyCode = $rowImage->getCurrencyCode();
                    $serviceid = $rowImage->getServiceId();
                    $serviceName = $rowImage->getServiceName();
                    $fromWeight = $rowImage->getFromWeight();
                    $toWeight = $rowImage->getToWeight();

                    $serviceCountryTimeObj = new ServiceCountryTimeFilter();
                    $serviceCountryTimeObj->addFieldFilter("id_service", $serviceid);
                    $countryList = $serviceCountryTimeObj->getCountryList('c.name as country_name');
                    if (count($countryList) > 0) {
                        $countryMessage = "";
                        if (count($countryList) < 2) {
                            $countryArray = array();
                            foreach ($countryList as $clist) {
                                $countryArray[] = $clist->getCountryName();
                            }
                            $countryMessage = "Delivery in " . implode(" and ", $countryArray);
                        } else {
                            $countryMessage = "Network in " . count($countryList) . "+ countries";
                        }
                    }
                    $html .= '<div data-name="' . $displayName . '" class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                                        <div class="panel panel-default mt-element-ribbon">';
                    $class = "center-block";
                    if ($rowImage->getIsAgreed() == 1) {

                        $html .= (trim($statusButton) == '1' ? '<span class="btn btn-xs ribbon ribbon-right ribbon-right ribbon-color-success uppercase carrier_status" data-status="inactive"  data-showservice = "displaynon" data-carrier_id="' . $carrierId . '"   data-carrier_name="' . $displayName . '">  ACTIVE </span>' : '<span class="btn btn-xs ribbon ribbon-right ribbon-right ribbon-color-danger uppercase carrier_status"  data-status="active" data-showservice = "displaynon"  data-carrier_id="' . $carrierId . '" data-carrier_name="' . $displayName . '">  INACTIVE </span>');
                        //$class = "pull-right";
                    }
                    $html .= '<div class="panel-body" title="' . $displayName . '-' . $serviceid . '">
                                            <div class="img-wrapper clearfix">
                                                <img class="' . $class . '" src="../images/carrierlogo/thumbnail/owe_100_' . $rowImage->getLogo() . '" alt="' . $rowImage->getCarrier() . '" uib-popover="' . $rowImage->getCarrier() . '" popover-trigger="mouseenter" >
                                            </div> 
                                        </div>
                                        <div class="text-center" >
                                            <ul class="list-unstyled task-list">
                                                <li class="clearfix"><img src="../assets/global/img/flags/' . strtolower($countryIso) . '.png"> ' . $countryName . '</li>
                                                <li class="clearfix">' . $cutOffTime . '</li>
                                                <li class="clearfix"> Currency: ' . $currencyCode . '</li>      
                                                <li class="clearfix">' . $countryMessage . '</li>
                                            </ul>
                                        </div>        
                                        <div class="panel-footer bg-primary clearfix text-center"> ';

                    if ($rowImage->getIsAgreed() == 1) {
                        $html .= '<a class="btn btn-xs btn-default blue btn-outline margin-right-5 event-carrierload ' . $rowImage->getCarrier() . '" 
                                                rel="tooltip" title="Services"
                                                data-toggle="modal" 
                                                data-target="#carrier-service-popup" 
                                                role="dialog" 
                                                tabindex="-1"
                                                href="javascript:;"
                                                data-carrier_name="' . $rowImage->getCarrier() . '" 
                                                data-action="GET_ALL_SERVICES"
                                                data-courierimage="' . $rowImage->getLogo() . '" 
                                                data-courierid="' . $rowImage->getId() . '" 
                                                data-carrierload="carrier_setup.php"
                                                data-cut_of_time="' . $cutOffTime . '" 
                                                data-carrier_display_name="' . $displayName . '" 
                                                data-carrier_status="' . $status . '" 
                                                data-carrier_country="' . $countryName . '" 
                                                data-carrier_country_iso="' . $rowImage->getCountryIso() . '"
                                                data-carrier_remotearea_check="' . $rowImage->getRemoteareaCheck() . '"
                                                data-user_service_status="' . $statusButton . '"
                                                >
                                                <span class="fa fa-plug"></span>
                                            </a>';
                    } else {
                        $class = ' ';
                        $html .= ' <a id="serviceagreement_popup"   href="javascript:;" class="btn btn-primary btn-xs"  
                        data-courierid="' . $rowImage->getId() . '"  data-toggle="modal" data-target="#service-modal-popup" role="dialog"  >'
                            . '<span class="fa fa-check"></span> Agree</a>';
                    }
                    $html .= '</div>
                                        </div>
                                    </div>';
                }
            }
            $html .= '<div class="clearfix"></div>';
            echo $html;
            die;
        } //SAVE CARRIER AGREEMENT
        else if (trim($this->form_vars["func"]) == 'save_carrier_agreement') {
            $carrierId = (int)$this->form_vars["carrier_id"];
            if ($carrierId > 0) {
                UserServicesRouting::updateUserAgreementForServices($carrierId, $user->getUserAccountId());
            }
            exit;
        } //CHANGE STATUS OF SERVICES ACTIVE OR DEACTIVE
        else if (trim($this->form_vars["func"]) == 'CHANGE_STATUS_SERVICES') {

            $output = array();
            $output["STATUS"] = 'SUCCESS';
            $output["MESSAGE"] = '';
            $carrierId = (int)$this->form_vars["carrier_id"];
            $carrierName = $this->form_vars["carrier_name"];
            $status = (trim($this->form_vars["status"]) == 'active' ? '1' : ((trim($this->form_vars["status"]) == 'inactive' ? '0' : '')));

            if (trim($status) != '') {
                $userServiceRoutingObj = new UserServicesRoutingFilter();
                $userServiceRoutingObj->addFieldFilter("carrier_id", $carrierId, "ser.");
                $userServiceRoutingObj->addFieldFilter("user_account_id", $user->getUserAccountId(), "psr.");
                $userServiceRoutingList = $userServiceRoutingObj->getServiceColumnList('psr.*');
                if (count($userServiceRoutingList) > 0) {
                    foreach ($userServiceRoutingList as $userserviceList) {
                        $userserviceList->setStatus($status);
                        $userserviceList->save();
                    }
                }


                $output["STATUS"] = 'SUCCESS';
                $output["MESSAGE"] = formatMessages(SUCCESS_STATUS_UPDATED);//'You have successfully change the services status';
            } else {
                $output["STATUS"] = 'ERROR';
                $output["MESSAGE"] = formatMessages(ERROR_INVALID_DATA);
            }
            echo json_encode($output);
            die;
        } //CHANGE STATE OF INDIVIDUAL SERVICE ACTIVE OR DEACTIVE
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == "deactive_individual_service") {
            $serviceId = $this->form_vars['serviceId'];
            $status = $this->form_vars["status"];
            if (trim($status) != '') {
                $saveStatus = 0;
                if(trim($status) == "true") {
                    $saveStatus = 1;
                }
                $userServiceRoutingObj = new UserServicesRoutingFilter();
                $userServiceRoutingObj->addFieldFilter("service_id", $serviceId, "psr.");
                $userServiceRoutingObj->addFieldFilter("user_account_id", $user->getUserAccountId(), "psr.");
                $userServiceRoutingList = $userServiceRoutingObj->getServiceColumnList('psr.*');
                if (count($userServiceRoutingList) > 0) {
                    foreach ($userServiceRoutingList as $userserviceList) {
                        $userserviceList->setStatus($saveStatus);
                        $userserviceList->save();
                    }
                    $output["STATUS"] = 'SUCCESS';
                    $output["MESSAGE"] = formatMessages(SUCCESS_STATUS_UPDATED);//'You have successfully change the service status';
                } else {
                    $output["STATUS"] = 'ERROR';
                    $output["MESSAGE"] = formatMessages(ERROR_INVALID_DATA);//'You have pass incorrect data to modify';
                }
            }


            echo json_encode($output);
            die;
        } //GET ALL SERVICES BASED ON CARRIER
        else if (trim($this->form_vars["func"]) == "GET_ALL_SERVICES") {
            $htmlReturn = "";
            $courier = (int)$this->form_vars["courier"];
            $carrier_name = $this->form_vars["carrier_name"];
            $cut_of_time = $this->form_vars["cut_of_time"];
            $carrier_display_name = $this->form_vars["carrier_display_name"];
            $carrier_status = $this->form_vars["carrier_status"];
            $carrier_country = $this->form_vars["carrier_country"];
            $carrier_country_iso = $this->form_vars["carrier_country_iso"];
            if(preg_match('/^[a-zA-Z1-9\d]+$/', $this->form_vars["courierimage"])) {
                $courierimage = $this->form_vars["courierimage"];
            } else {
                $courierimage = '';
            }
//            $courierimage = $this->form_vars["courierimage"];
            $carrier_remotearea_check = $this->form_vars["carrier_remotearea_check"];
            $user_service_status = $this->form_vars["user_service_status"];
            if ($user_service_status == 1)
                $status = 'inactive';
            else
                $status = 'active';
            $statusButton = 0;
            $carrierStatusFilter = new CarrierFilter();
            $carrierStatusFilter->addFilter(" usr.user_account_id = '" . $user->getUserAccountId() . "' and usr.status = 1 and cl.id = '" . $courier . "'");
            $carrierStatusFilter->AddOrderBy('carrier', true);
            $carrierStatusResult = $carrierStatusFilter->getCarrierSetupList("cl.*");

            if (count($carrierStatusResult) > 0) {
                $statusButton = 1;
            }
            $serviceTable = '';
            $serviceTable .= '<div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><img class="margin-right-5" src="../images/carrierlogo/thumbnail/owe_50_' . $courierimage . '" alt="' . $carrier_name . '" uib-popover="' . $carrier_name . '" popover-trigger="mouseenter">' . $carrier_name . ' Services</h4>
                    </div>
                    <!-- /.modal-content --> 
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div  id="console_window_service" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px; margin-bottom:15px;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <strong>Origin:</strong> ' . (trim($carrier_country) == '' ? '-' : ' <img src="../assets/global/img/flags/' . trim(strtolower($carrier_country_iso)) . '.png">  ' . trim($carrier_country)) . '
                                    </div>
                                    <div class="col-sm-4">
                                            <div class="md-radio-inline"><strong>Active:</strong>
                                                <input data-carrier_id="' . $courier . '" data-carrier_name="' . $carrier_name . '" name="service_carrier_status"  type="checkbox" class="make-switch service_carrier_status" data-status = "' . $status . '"  data-on-text="YES"  ' . (($statusButton == '1') ? 'checked=checked' : '') . '  data-off-text="NO"  data-on-color="primary" data-off-color="danger" data-size="mini">
                                            </div>
                                        
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Cut Off:</strong> ' . (trim($cut_of_time) == '' ? '-' : trim($cut_of_time)) . '
                                    </div>
                                </div>
                            </div>';

            $serviceTable .= '</div>
                        <div class="row">
                            <hr class="" />
                        </div>
                        <div class="row" id="message_download_csv_service" style="display: none;">
                            <div class="col-md-12">
                                    <div class="alert alert-danger"></div>
                            </div>
                        </div>';

            if ($courier > 0) {
                $serviceFilter = new ServiceFilter();
                $serviceFilter->addFieldFilter('ser.carrier_id', $courier);
                $serviceFilter->addFieldFilter('usr.user_account_id', $user->getUserAccountId());
                $columnListArray = array('ser.id', 'uploaded_currency', 'carrier_id', 'name', 'code', 'origin_country', 'type', 'wieght_type', 'ser.is_remotearea', 'usr.status as user_service_status,  ser.max_length, ser.max_width, ser.max_height, ser.to_weight, ser.from_weight');
                $serviceData = $serviceFilter->getInnerJoinList($columnListArray, 'services ser', 'user_services_routing usr', 'usr.service_id = ser.id', 'group by ser.id');

                if (count($serviceData) > 0) {
                    $serviceTable .= '<div class="row">
                        
                        <div class="col-md-12"><table class="table table-striped table-bordered table-advance table-hover">';
                    $serviceTable .= '<thead><tr>' .
                        '<th>Service (Code)</th>' .
                        '<th>Weight (Kg) </th>' .
                        '<th>Dims (CM) </th>' .
                        '<th>Countries</th>' .
                        '<th>Package</th>';
                    $serviceTable .= '<th class="red-back">Status</th>' .
                        '</tr></thead><tbody>';
                    foreach ($serviceData as $servicesItem) {

                        $serviceId = $servicesItem->getId();
                        $carrierId = $servicesItem->getCarrierId();
                        $name = $servicesItem->getName();
                        $code = $servicesItem->getCode();
                        $serviceCountry = str_replace(',', ', ', $servicesItem->getServiceCountry());
                        $originCountry = $servicesItem->getOriginCountry();
                        $uploadedCurrency = $servicesItem->getUploadedCurrency();
                        $TypeRegion = $servicesItem->getType();
                        $weightType = $servicesItem->getWieghtType();
                        $isRemotearea = $servicesItem->getIsRemotearea();
                        $userSericeStatus = $servicesItem->getUserServiceStatus();
                        $serviceTable .= '<tr>' .
                            '<td>' . $name ."(". $code  . ')</td>' .
                            '<td>' . number_format($servicesItem->getFromWeight(), 2) ."-". number_format($servicesItem->getToWeight(),2) . '</td>' .
                            '<td>' . $servicesItem->getMaxLength() ." X ". $servicesItem->getMaxWidth() . ' X ' . $servicesItem->getMaxHeight() . '</td>' .
                            '<td>
                                        <a class="btn btn-xs btn-default blue btn-outline pull-left margin-right-5 event-servicecountry" rel="tooltip" title="Services Country"
                                                data-target="#service-country-popup" role="dialog" tabindex="-1" 
                                                data-servicename="' . $name . '" data-action="GET_SERVICES_COUNTRY"
                                                data-serviceid="' . $serviceId . '" data-carrierload="carrier_setup.php" href="javascript:;">
                                                Show Countries
                                            </a>' .
                            '<td>' . (($weightType == 1) ? 'Parcel' : 'Shipment') . '</td>';

                        $serviceTable .= '<td>
                                                  <div class="form-group">
                                                              <div class="md-radio-inline">
                                                                  <input data-current-service="' . $serviceId . '" name="is_status[' . $serviceId . ']" data-servicename="' . $name . '" type="checkbox" class="make-switch is_status"  data-on-text="YES"  ' . (($userSericeStatus == '1') ? 'checked=checked' : '') . '  data-off-text="NO"  data-on-color="primary" data-off-color="danger" data-size="mini">
                                                              </div>
                                                  </div>
                                              </td>';

                        $serviceTable .= '</td>' .
                            '</tr>';
                    }
                    $serviceTable .= '</tbody></table></div></div>';
                } else {
                    $serviceTable .= '<div class="alert alert-danger text-center">' . formatMessages(ERROR_SERVICE_CARRIER) . $carrier_name . '.</div>';
                }
            } else {
                $serviceTable .= '<div class="alert alert-danger text-center">' . formatMessages(ERROR_INVALID_CARRIER) . '</div>';
            }
            echo $serviceTable . '<div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div><script>
                            $(".service_carrier_status").bootstrapSwitch();
                            $(".is_status").bootstrapSwitch();
                    $(".service_carrier_status").on("switchChange.bootstrapSwitch", function () {
                      var e = $(this);
                    changeAllServiceStatus(e);
                    
                    });
                   $(".is_status").on("switchChange.bootstrapSwitch", function (event, state) {
                    var serviceId = $(this).attr("data-current-service");
                    var serviceName = $(this).attr("data-servicename");
                    serviceActivation(state,serviceId,serviceName);
                    });</script>';
            die;
        } //GET ALLOW COUNTRY FOR SERVICES
        else if (trim($this->form_vars["func"]) == 'GET_SERVICES_COUNTRY') {
            $serviceId = (int)$this->form_vars["serviceid"];
            $servicename = $this->form_vars["servicename"];
            $selectCountryClass = new ServiceCountryTimeFilter();
            $selectCountryClass->addFilter(" id_service ='" . DbAccess3::escape($serviceId) . "'");
            $selectCountryList = $selectCountryClass->getCountryList("c.id, c.iso as iso, c.name as country_name");
            if (count($selectCountryList) > 0) {
                foreach ($selectCountryList as $countryData) {
                    echo '<div class="col-sm-6"><img src="../assets/global/img/flags/' . strtolower($countryData->getIso()) . '.png"> ' . $countryData->getCountryName() . '</div>';
                }
            } else {
                echo '<div class="col-sm-12"> ' . $servicename . ':' . formatMessages(ERROR_COUNTRY_NOT_AVAILABLE) . '.</div>';
            }
            echo '<div style="clear:both;"></div>';
            die;
        } // MY CONTRACT TAB DISPLAY
        else if (isset($this->form_vars["get_all"]) && $this->form_vars["get_all"] == "shipping_contract_list") {

            $display    =    $_REQUEST['display'];

            $html = "";
            $agentFilter = new AgentDataFilter();
            $contractList = $agentFilter->myContractList($user->getUserAccountId(), false);

            if (count($contractList) > 0) {
                $html .= '<div class="portlet light">
                                    <div class="portlet-body">
                                        <div class="row"><div class="col-md-12 alert alert-danger display-none"  id="response_message" ></div>';
                if($display == 'true')
                        $html .= '                        
                                        <div class="col-md-12 alert alert-success"  id="success_message" >You have successfully add/updated contract. </div>';
                $html .= '
                                        </div>
                                        <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th>' . Translation::GetCaption("ACTION") . '</th>
                                                        <th>Date Created</th>
                                                        <th>Contract</th>
                                                        <th>Carrier</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                 <tbody>';
                foreach ($contractList as $contract) {
                    $html .= '<tr>';
                    $html .= '<td><a href="javascript:;" class="btnedit btn btn-xs blue btn-outline" data-agent_id="' . $contract->getId() . '" data-carrier_id="' . $contract->getCarrierId() . '" data-contract_name = "' . $contract->getContractName() . '" data-service_id = "'.$contract->getServiceId().'"><span class="fa fa-pencil"></span> </a> '
                        /*    .(($contract->getStatus() == 1 )?
                            '<span class="fa fa-trash"></span> </a>'
                            : '')*/
                        . '</td>';
                    $html .= '<td>' . formatDate(date("Y-m-d", strtotime($contract->getDateCreated()))) . '</td>';
                        $html .= '<td>' . $contract->getContractName() . '</td>';
                    $html .= '<td><img src="../images/carrierlogo/thumbnail/owe_16_' . $contract->getLogo() . '" title="' . $contract->getCarrier() . '" alt="' . $contract->getCarrier() . '"> ' . $contract->getCarrier() . '</td>';
                    $html .= '<td>' . ($contract->getStatus() == 1 ? '<center><a href="javascript:;" class="btndelete " data-status="1" data-agent_id="' . $contract->getId() . '" data-contract_name = "' . $contract->getContractName() . '"><span class="label label-sm label-success">Active</span></a></center>' : '<center><a href="javascript:;" class="btndelete" data-status="0" data-agent_id="' . $contract->getId() . '" data-contract_name = "' . $contract->getContractName() . '"><span class="label label-sm label-danger">Inactive</span></a></center>') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</tbody>
                                            </table>
                                        </div>
                                    </div>';

                $html .= '</div>';
            } else {
                $html .= '<div class="alert alert-info"><span class="fa fa-info-circle"></span> No contracts to display.</div>';
            }
            $class = "";
            //if ($user->getCarrierSetupAgreement() != "1") {

             //   $class = 'data-toggle="modal" data-target="#service-modal-popup" role="dialog" ';
           //     $html .= ' <a id="agreement_popup"   href="javascript:;" class="btn btn-primary" ' . $class . ' ><span class="fa fa-plus"></span> Add your own contract</a>';
            //} else {
                $html .= ' <a id="save_bulk"   href="javascript:;" class="btn btn-primary"><span class="fa fa-plus"></span> Add your own contract</a>';
            //}

            echo $html;
            die;
        } //SAVE CARRIER SETUP AGREEMENT
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "save_setup_agreement") {
            $user->setCarrierSetupAgreement(1);
            $user->save();

            $userLog = new UserLog();
            $userLog->createlog('Carrier Setup agreement accepted.', $user->getId(), 'USER');
            die;
        } //SET UP CONTRACT FOR ALL AVAILBEL CARRIER LIST
        else if (isset($this->form_vars["get_all"]) && $this->form_vars["get_all"] == "carrier_list") {
            $carrierFilter = new CarrierFilter();
            $carrierFilter->addFilter(" cl.status = '1' and on_contract = 1");
            $carrierFilter->AddOrderBy('carrier', true);
            $carrierFilter->setGroup(" carrier");

            $carrierResult = $carrierFilter->getCarrierList("cl.*,cn.name 'country_name',cn.iso 'country_iso'");
            $carrierFilterCount = 0;
            $html = "";
            $count = 1;
            foreach ($carrierResult as $rowImage) {
                $carrierName = $rowImage->getCarrier();
                $carrierId = $rowImage->getId();
                $cutOffTime = 'Cut Off - ' . $rowImage->getCutOfftime();
                $displayName = $rowImage->getCarrierDisplayName();
                $status = $rowImage->getStatus();
                $countryIso = $rowImage->getCountryIso();
                $countryName = $rowImage->getCountryName();
                $currencyCode = $rowImage->getCurrencyCode();

                $html .= '<div data-name="' . $displayName . '" class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope search_carrier_custom">
                            <div class="panel panel-default mt-element-ribbon shipping_carrier" data-id="' . $carrierId . '" data-name="' . $displayName . '" >
                                <div class="panel-body" title="' . $displayName . '">
                                    <div class="img-wrapper clearfix">
                                        <img class=" center-block" src="../images/carrierlogo/thumbnail/owe_100_' . $rowImage->getLogo() . '" alt="' . $rowImage->getCarrier() . '" uib-popover="' . $rowImage->getCarrier() . '" popover-trigger="mouseenter" >
                                    </div> 
                                </div>
                                <div class="text-center" >
                                    <ul class="list-unstyled task-list">
                                        <li class="clearfix"><img src="../assets/global/img/flags/' . strtolower($countryIso) . '.png"> ' . $countryName . '</li>
                                    </ul>
                                
                                    <a class=" btn btn-primary btn-xs margin-right-5" title="Carrier Setup"
                                    data-id="' . $carrierId . '" data-name="' . $displayName . '" href="javascript:;">
                                    Select Carrier
                                    </a>
                                    
                                </div> 
                                <br>
                                
                            </div>
                          
                            </div>
                       ';
            }
            $html .= '<div class="clearfix"></div>';
            echo $html;
            die;
        } //CREATE DYNAMIC FORM FOR CARRIER SETUP
        else if (isset($this->form_vars["get_all"]) && $this->form_vars["get_all"] == "get_carrier_service_setupform") {
            $carrier_id = $this->form_vars["carrier_id"];
            $carrier_name = $this->form_vars["carrier_name"];
            $get_carrier_option = $this->form_vars["get_carrier_option"];

            $agent_id = $this->form_vars["agent_id"];
            $serviceConstantObj = new ServiceConstantFilter();
            $serviceConstantObj->addFilter("carrier_id = '" . DbAccess3::escape($carrier_id) . "' or carrier_id = 0");
            $serviceConstantObj->AddOrderBy("sort_order");
            $serviceConstantList = $serviceConstantObj->getList();

            if ($agent_id > 0) {
                $serviceConstantFilterObj = new ServiceConstantFilter();
                $servConstantList = $serviceConstantFilterObj->getConstantValue($agent_id, $carrier_id);

                if (count($servConstantList) > 0) {
                    $editValueArray = array();
                    foreach ($servConstantList as $scList) {
                        $editValueArray[$scList->getId()] = $scList->getConstantValue();
                    }

                    $serviceAgentMappingFilter = new ServiceAgentMappingDataFilter();
                    $serviceAgentMappingFilter->addFilter(" agentid = '" . DbAccess3::escape($agent_id) . "'");
                    $serviceAgentList = $serviceAgentMappingFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");
                    if (count($serviceAgentList) > 0) {
                        $serviceArray = array();
                        foreach ($serviceAgentList as $saList) {
                            $serviceArray[] = $saList->getServiceId();
                            $from_weight = $saList->getFromWeight();
                            $to_weight = $saList->getToWeight();
                        }
                    }
                }
            }
            $col = 4;
            if (count($serviceConstantList) > 0) {
                $html = "";
                if(trim($get_carrier_option) == 'services') {
                    $html .= ''
                        . '<div class="portlet light">'
                        . '<div class="portlet-body">'
                        . '<div class="row">'
                        . ' <div class="col-md-12 alert alert-danger display-none"  id="error_message" ></div>';
                    $html .= '<div class="form-group">
                                <!--<div class="caption margin-bottom-10 block"> <span class="caption-subject bold uppercase">Services</span> </div>-->
                                <div class="row">
                                    <div class="col-md-12 accessibility-container">
                                        <select name="agent_services[]" id="agent_services" multiple="multiple"  class="multi-select multiselect_drop_down" title="Please select services" placeholder="Please select services" data-original-title="Please select services" required >';
                    $servicesObj = new ServiceFilter();
                    $servicesObj->addFilter("and active = 1 and carrier_id = '" . $carrier_id . "'");
                    $serviceList = $servicesObj->getColumnList("id, name");

                    if (count($serviceList) > 0) {
                        $selectedServicesArr = array();
                        foreach ($serviceList as $services) {
                            $serviceSelected = "";
                            if (in_array($services->getId(), $serviceArray)) {
                                $serviceSelected = "selected=selected";
                                $selectedServicesArr[] = $services->getId();
                            }
                            $html .= '<option ' . $serviceSelected . ' value="' . $services->getId() . '">' . $services->getName() . '</option>';
                        }
                    }
                    $html .= '</select>
                                        <input  type="hidden" id="selected_hidden_services"  value="' . implode(',', $selectedServicesArr) . '"/>
                                    </div>
                                </div>
                            </div>';

                }
                else if(trim($get_carrier_option) == 'fields') {


                    $html .= '<div class="row">';
                $ranges = 0;
                foreach ($serviceConstantList as $serviceObj) {
                    $textInputSize  =   '';
                    if(trim($serviceObj->getFieldSize()) != '' && $serviceObj->getFieldSize() > 0)
                        $textInputSize = ' size="'.$serviceObj->getFieldSize().'" maxlength="'.$serviceObj->getFieldSize().'"';
                    else
                        $textInputSize = ' size="35" maxlength="35"';

                    $mandatory = $serviceObj->getMandatory();
                    $markRequiredField = "";
                    $required = "";
                    if ($mandatory == 1) {
                        $markRequiredField .= '<i class="fa tooltips font-red">*</i>';
                        $required = "required";
                    }
                    if ($serviceObj->getConstant() == "RANGES") {
                        $ranges = 1;
                    }
                    if ($serviceObj->getDesignControl() == "TEXTBOX") {

                        $html .= '<div class="col-md-' . $col . ' '.$serviceObj->getIntegrationType().'">
                                        <div class="form-group1"> 
                                            <label>' . $serviceObj->getCaption() . '</label>
                                            <div class="input-group input-group-sm input-icon right" > 
                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';
                        $html .= $markRequiredField;
                        $html .= '<input name="' . $serviceObj->getConstant() . '" '.$textInputSize.' id="' . $serviceObj->getConstant() . '" value="' . @$editValueArray[$serviceObj->getId()] . '"  class="form-control"   title="" placeholder="' . $serviceObj->getCaption() . '" rel="tooltip" data-original-title="' . $serviceObj->getCaption() . '" type="text" ' . $required . '>

                                            </div>
                                        </div>
                                    </div>

                    ';
                    } else if ($serviceObj->getDesignControl() == "DROPDOWN") {

                        $html .= '<div class="col-md-' . $col . ' '.$serviceObj->getIntegrationType().'">
                                        <div class="form-group1"> 
                                            <label>' . $serviceObj->getCaption() . '</label>
                                            <div class="input-group input-group-sm input-icon right" > 
                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';
                        if(trim($serviceObj->getDefaultValues()) != ''){
                            $value_array    = json_decode($serviceObj->getDefaultValues(), true);
                            //, $default_select = "", $attr = "", $default_select_value = "", $dd_id = '' ,$title='',$use_key_value = ''
                            $html .= Ddl::generateArrayDDL($serviceObj->getConstant(), $value_array, @$editValueArray[$serviceObj->getId()], "Please Select", ' class="form-control form-filter select2" '.$required, "", $serviceObj->getConstant(),'Select Select','');
                        }
                        else{
                            $html .= Ddl::generateCountryDDL($serviceObj->getConstant(), @$editValueArray[$serviceObj->getId()], 'id');
                        }

                        $html .= '  </div>
                                        </div>
                                    </div>';
                    }
                }

                $html .= '<div style="clear:both"></div><hr><div class="col-md-' . $col . '">
                                <div class="form-group1">
                                    <label >From Weight</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                            <input class="form-control" name="from_weight" id="from_weight" type="text" onkeypress="return numbersonly(event)" value="' . $from_weight . '" placeholder="From Weight" title="From Weight" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-' . $col . '">
                                <div class="form-group1">
                                    <label >To Weight</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="to_weight" id="to_weight" type="text" onkeypress="return numbersonly(event)" value="' . $to_weight . '" placeholder="To Weight" title="To Weight" required>
                                    </div>
                                </div>
                            </div>';
                if ($ranges == 1) {
                    $agentDataObj = new AgentData($this->form_vars["agent_id"]);
                    $agentcode = $agentDataObj->getAgentCode();
                    $licencePlateFilterObj = new LicencePlateFilter();
                    $licencePlateFilterObj->addFilter(" range_name = '" . $agentcode . "' and addedby = '" . $user->getId() . "'");
                    $licenceplateList = $licencePlateFilterObj->getList();
                    if (count($licenceplateList) > 0) {
                        $licenceplate = $licenceplateList[0];
                        $rangeStart = $licenceplate->getRangeStart();
                        $rangeEnd = $licenceplate->getRangeEnd();
                        $prefix = $licenceplate->getPrefix();
                        $sufix = $licenceplate->getSufix();
                    }
                    $html .= '<div class="col-md-' . $col . '">
                                <div class="form-group1">
                                    <label >Range Start</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="range_start" id="range_start" type="text" onkeypress="return numbersonly(event)" value="' . $rangeStart . '" placeholder="Range Start" title="Range Start" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-' . $col . '">
                                <div class="form-group1">
                                    <label >Range End</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="range_end" id="range_end" type="text" onkeypress="return numbersonly(event)" value="' . $rangeEnd . '" placeholder="Range End" title="Range End" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-' . $col . '">
                                <div class="form-group1">
                                    <label >Prefix</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <input class="form-control" name="prefix" id="prefix" type="text" value="' . $prefix . '" placeholder="Prefix" title="Prefix" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-' . $col . '">
                                <div class="form-group1">
                                    <label >Suffix</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <input class="form-control" name="suffix" id="suffix" type="text" value="' . $sufix . '" placeholder="Suffix" title="Suffix" >
                                    </div>
                                </div>';
                }

                $html .= '</div>';
                $html .= ' <div style="clear:both"></div> 
                        <div class="row " style="text-align:centre;" align="center">
                            <div class="col-md-12">
                                <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                                <input type="hidden" name="carrier_id" id="carrier_id" value="' . $carrier_id . '" />
                                <input type="hidden" name="carrier_name" id="carrier_name" value="' . $carrier_name . '" />
                                <input type="hidden" name="agent_id" id="agent_id" value="' . $agent_id . '" />
                                <!--<input id="btn_Save" type="button"  class="btn btn-primary" value="' . Translation::GetCaption("SAVE") . '"/>
                                <input id="btn_Cancel" type="button"  class="btn btn-default" value="' . Translation::GetCaption("CANCEL") . '"/> -->
                            </div>
                        </div></div></div> </form>';
                }

                echo $html;
                die;
            }
        } // SAVE CONTRACT
        else if (isset($this->form_vars["get_all"]) && $this->form_vars["get_all"] == "get_carrier_contract_setupform") {
            $carrier_id = $this->form_vars["carrier_id"];
            $carrier_name = $this->form_vars["carrier_name"];
            $agent_id = $this->form_vars["agent_id"];
            $serviceConstantObj = new ServiceConstantFilter();
            $serviceConstantObj->addFilter("carrier_id = '" . DbAccess3::escape($carrier_id) . "' or carrier_id = 0");
            $serviceConstantObj->AddOrderBy("sort_order");
            $serviceConstantList = $serviceConstantObj->getList();

            if ($agent_id > 0) {
                $serviceConstantFilterObj = new ServiceConstantFilter();
                $servConstantList = $serviceConstantFilterObj->getConstantValue($agent_id, $carrier_id);

                if (count($servConstantList) > 0) {
                    $editValueArray = array();
                    foreach ($servConstantList as $scList) {
                        $editValueArray[$scList->getId()] = $scList->getConstantValue();
                    }

                    $serviceAgentMappingFilter = new ServiceAgentMappingDataFilter();
                    $serviceAgentMappingFilter->addFilter(" agentid = '" . $agent_id . "'");
                    $serviceAgentList = $serviceAgentMappingFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");
                    if (count($serviceAgentList) > 0) {
                        $serviceArray = array();
                        foreach ($serviceAgentList as $saList) {
                            $serviceArray[] = $saList->getServiceId();
                            $from_weight = $saList->getFromWeight();
                            $to_weight = $saList->getToWeight();
                        }
                    }
                }
            }

            if (count($serviceConstantList) > 0) {


                $html = "";
                $html .= ''
                    . '<div class="portlet light">'
                    . '<div class="portlet-body">'
                    . '<div class="row">'
                    . ' <div class="col-md-12 alert alert-danger display-none"  id="error_message" ></div>';
                $col = 3;
                $html .= '<div class="form-group">
                                <div class="caption margin-bottom-10 block"> Services</div>
                                <div class="row">
                                    <div class="col-md-12 accessibility-container">
                                        <select name="agent_services[]" id="agent_services" multiple="multiple"  class="multi-select multiselect_drop_down" title="Group" placeholder="Group" data-original-title="Group" >';
                $servicesObj = new ServiceFilter();
                $servicesObj->addFilter("and active = 1 and carrier_id = '" . $carrier_id . "'");
                $serviceList = $servicesObj->getColumnList("id, name");

                if (count($serviceList) > 0) {
                    $selectedServicesArr = array();
                    foreach ($serviceList as $services) {
                        $serviceSelected = "";
                        if (in_array($services->getId(), $serviceArray)) {
                            $serviceSelected = "selected=selected";
                            $selectedServicesArr[] = $services->getId();
                        }
                        $html .= '<option ' . $serviceSelected . ' value="' . $services->getId() . '">' . $services->getName() . '</option>';
                    }
                }
                $html .= '</select>
                                        <input  type="hidden" id="selected_hidden_services"  value="' . implode(',', $selectedServicesArr) . '"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                           ';
                $ranges = 0;
                foreach ($serviceConstantList as $serviceObj) {
                    $textInputSize  =   '';
                    if(trim($serviceObj->getFieldSize()) != '' && $serviceObj->getFieldSize() > 0)
                        $textInputSize = ' size="'.$serviceObj->getFieldSize().'" maxlength="'.$serviceObj->getFieldSize().'"';
                    else
                        $textInputSize = ' size="35" maxlength="35"';

                    $mandatory = $serviceObj->getMandatory();
                    $markRequiredField = "";
                    $required = "";
                    if ($mandatory == 1) {
                        $markRequiredField .= '<i class="fa tooltips font-red">*</i>';
                        $required = "required";
                    }
                    if ($serviceObj->getConstant() == "RANGES") {
                        $ranges = 1;
                    }
                    if ($serviceObj->getDesignControl() == "TEXTBOX") {

                        $html .= '<div class="col-md-' . $col . ' '.$serviceObj->getIntegrationType().'">
                                        <div class="form-group"> 
                                            <label>' . $serviceObj->getCaption() . '</label>
                                            <div class="input-group input-group-sm input-icon right" > 
                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';
                        $html .= $markRequiredField;
                        $html .= '<input name="' . $serviceObj->getConstant() . '" id="' . $serviceObj->getConstant() . '"  '.$textInputSize.' value="' . @$editValueArray[$serviceObj->getId()] . '"  class="form-control"   title="" placeholder="' . $serviceObj->getCaption() . '" rel="tooltip" data-original-title="' . $serviceObj->getCaption() . '" type="text" ' . $required . '>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    ';
                    } else if ($serviceObj->getDesignControl() == "DROPDOWN") {

                        $html .= '<div class="col-md-' . $col . ' '.$serviceObj->getIntegrationType().'">
                                        <div class="form-group"> 
                                            <label>' . $serviceObj->getCaption() . '</label>
                                            <div class="input-group input-group-sm input-icon right" > 
                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';
                        if(trim($serviceObj->getDefaultValues()) != ''){
                            $value_array    = json_decode($serviceObj->getDefaultValues(), true);
                            //, $default_select = "", $attr = "", $default_select_value = "", $dd_id = '' ,$title='',$use_key_value = ''
                            $html .= Ddl::generateArrayDDL($serviceObj->getConstant(), $value_array, @$editValueArray[$serviceObj->getId()], "Please Select", ' class="form-control form-filter select2"', "", $serviceObj->getConstant(),'Select Select','');
                        }
                        else{
                            $html .= Ddl::generateCountryDDL($serviceObj->getConstant(), @$editValueArray[$serviceObj->getId()], 'id');
                        }

                        $html .= '  </div>
                                        </div>
                                    </div>';
                    }
                }

                $html .= '<div class="col-md-3">
                                <div class="form-group">
                                    <label >From Weight</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                            <input class="form-control" name="from_weight" id="from_weight" type="text" onkeypress="return numbersonly(event)" value="' . $from_weight . '" placeholder="From Weight" title="From Weight" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >To Weight</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="to_weight" id="to_weight" type="text" onkeypress="return numbersonly(event)" value="' . $to_weight . '" placeholder="To Weight" title="To Weight" required>
                                    </div>
                                </div>
                            </div>';
                if ($ranges == 1) {
                    $agentDataObj = new AgentData($this->form_vars["agent_id"]);
                    $agentcode = $agentDataObj->getAgentCode();
                    $licencePlateFilterObj = new LicencePlateFilter();
                    $licencePlateFilterObj->addFilter(" range_name = '" . $agentcode . "' and addedby = '" . $user->getId() . "'");
                    $licenceplateList = $licencePlateFilterObj->getList();
                    if (count($licenceplateList) > 0) {
                        $licenceplate = $licenceplateList[0];
                        $rangeStart = $licenceplate->getRangeStart();
                        $rangeEnd = $licenceplate->getRangeEnd();
                        $prefix = $licenceplate->getPrefix();
                        $sufix = $licenceplate->getSufix();
                    }
                    $html .= '<div class="col-md-3">
                                <div class="form-group">
                                    <label >Range Start</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="range_start" id="range_start" type="text" onkeypress="return numbersonly(event)" value="' . $rangeStart . '" placeholder="Range Start" title="Range Start" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Range End</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="range_end" id="range_end" type="text" onkeypress="return numbersonly(event)" value="' . $rangeEnd . '" placeholder="Range End" title="Range End" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Prefix</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <input class="form-control" name="prefix" id="prefix" type="text" value="' . $prefix . '" placeholder="Prefix" title="Prefix" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Suffix</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <input class="form-control" name="suffix" id="suffix" type="text" value="' . $sufix . '" placeholder="Suffix" title="Suffix" >
                                    </div>
                                </div>';
                }

                $html .= '</div>';
                $html .= ' <div style="clear:both"></div> 
                        <div class="row " style="text-align:centre;" align="center">
                            <div class="col-md-12">
                                <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                                <input type="hidden" name="carrier_id" id="carrier_id" value="' . $carrier_id . '" />
                                <input type="hidden" name="carrier_name" id="carrier_name" value="' . $carrier_name . '" />
                                <input type="hidden" name="agent_id" id="agent_id" value="' . $agent_id . '" />
                                <!--<input id="btn_Save" type="button"  class="btn btn-primary" value="' . Translation::GetCaption("SAVE") . '"/>
                                <input id="btn_Cancel" type="button"  class="btn btn-default" value="' . Translation::GetCaption("CANCEL") . '"/> -->
                            </div>
                        </div></div></div> ';
                echo $html;
                die;
            }
        } // SAVE CONTRACT


        else if (isset($this->form_vars["get_all"]) && $this->form_vars["get_all"] == "get_carrier_setupform") {
            $carrier_id = $this->form_vars["carrier_id"];
            $carrier_name = $this->form_vars["carrier_name"];
            $agent_id = $this->form_vars["agent_id"];
            $service_id = $this->form_vars["service_id"];
            $serviceConstantObj = new ServiceConstantFilter();
            $serviceConstantObj->addFilter("carrier_id = '" . DbAccess3::escape($carrier_id) . "' or carrier_id = 0");
            $serviceConstantObj->AddOrderBy("sort_order");
            $serviceConstantList = $serviceConstantObj->getList();

            if ($agent_id > 0) {
                $serviceConstantFilterObj = new ServiceConstantFilter();
                $servConstantList = $serviceConstantFilterObj->getConstantValue($agent_id, DbAccess3::escape($carrier_id), $service_id);

                if (count($servConstantList) > 0) {
                    $editValueArray = array();
                    foreach ($servConstantList as $scList) {
                        $editValueArray[$scList->getId()] = $scList->getConstantValue();
                    }

                    $serviceAgentMappingFilter = new ServiceAgentMappingDataFilter();
                    $serviceAgentMappingFilter->addFilter(" agentid = '" . DbAccess3::escape($agent_id) . "'");
                    $serviceAgentList = $serviceAgentMappingFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");
                    if (count($serviceAgentList) > 0) {
                        $serviceArray = array();
                        foreach ($serviceAgentList as $saList) {
                            $serviceArray[] = $saList->getServiceId();
                            $from_weight = $saList->getFromWeight();
                            $to_weight = $saList->getToWeight();
                        }
                    }
                }
            }

            if (count($serviceConstantList) > 0) {


                $html = "";
                $html .= ''
                    . ' <div class="col-md-12 alert alert-danger display-none"  id="error_message" ></div>';
                $col = 3;
                $html .= '<div class="form-group">
                                <div class="caption margin-bottom-10 block"> '. $carrier_name .' Services</div>
                                <div class="row">
                                    <div class="col-md-12 accessibility-container">
                                        <select name="agent_services[]" id="agent_services" multiple="multiple"  class="multi-select multiselect_drop_down" title="Group" placeholder="Group" data-original-title="Group" >';
                $servicesObj = new ServiceFilter();
                $servicesObj->addFilter("and active = 1 and carrier_id = '" .  DbAccess3::escape($carrier_id) . "'");
                $serviceList = $servicesObj->getColumnList("id, name");

                if (count($serviceList) > 0) {
                    $selectedServicesArr = array();
                    foreach ($serviceList as $services) {
                        $serviceSelected = "";
                        if (in_array($services->getId(), $serviceArray)) {
                            $serviceSelected = "selected=selected";
                            $selectedServicesArr[] = $services->getId();
                        }
                        $html .= '<option ' . $serviceSelected . ' value="' . $services->getId() . '">' . $services->getName() . '</option>';
                    }
                }
                $html .= '</select>
                                        <input  type="hidden" id="selected_hidden_services"  value="' . implode(',', $selectedServicesArr) . '"/>
                                    </div>
                                </div>
                            </div>
                            <div class="caption margin-bottom-10 block"> Contract Details </div>
                            <div class="row">
                            
                           ';
                $ranges = 0;
                foreach ($serviceConstantList as $serviceObj) {

                    $textInputSize  =   '';
                    if(trim($serviceObj->getFieldSize()) != '' && $serviceObj->getFieldSize() > 0)
                        $textInputSize = ' size="'.$serviceObj->getFieldSize().'" maxlength="'.$serviceObj->getFieldSize().'"';
                    else
                        $textInputSize = ' size="35" maxlength="35"';
                    $mandatory = $serviceObj->getMandatory();
                    $markRequiredField = "";
                    $required = "";
                    if ($mandatory == 1) {
                        $markRequiredField .= '<i class="fa tooltips font-red">*</i>';
                        $required = "required";
                    }
                    if ($serviceObj->getConstant() == "RANGES") {
                        $ranges = 1;
                    }
                    if ($serviceObj->getDesignControl() == "TEXTBOX") {

                        $html .= '<div class="col-md-' . $col . ' '.$serviceObj->getIntegrationType().'">
                                        <div class="form-group"> 
                                            <label>' . $serviceObj->getCaption() . '</label>
                                            <div class="input-group input-group-sm input-icon right" > 
                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';

                        $html .= $markRequiredField;
                        $html .= '<input name="' . $serviceObj->getConstant() . '" '.$textInputSize.' id="' . $serviceObj->getConstant() . '" value="' . @$editValueArray[$serviceObj->getId()] . '" class="form-control" title="" placeholder="' . $serviceObj->getCaption() . '" rel="tooltip" data-original-title="' . $serviceObj->getCaption() . '" type="text" ' . $required . '>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    ';
                    } else if ($serviceObj->getDesignControl() == "DROPDOWN") {

                        $html .= '<div class="col-md-' . $col . ' '.$serviceObj->getIntegrationType().'">
                                        <div class="form-group"> 
                                            <label>' . $serviceObj->getCaption() . '</label>
                                            <div class="input-group input-group-sm input-icon right" > 
                                                <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>';
                        if(trim($serviceObj->getDefaultValues()) != ''){
                            $value_array    = json_decode($serviceObj->getDefaultValues(), true);
                            //, $default_select = "", $attr = "", $default_select_value = "", $dd_id = '' ,$title='',$use_key_value = ''
                            $html .= Ddl::generateArrayDDL($serviceObj->getConstant(), $value_array, @$editValueArray[$serviceObj->getId()], "Please Select", ' class="form-control form-filter select2"', "", $serviceObj->getConstant(),'Select Select','');
                        }
                        else{
                            $html .= Ddl::generateCountryDDL($serviceObj->getConstant(), @$editValueArray[$serviceObj->getId()], 'id');
                        }

                        $html .= '  </div>
                                        </div>
                                    </div>';
                    }
                }

                $html .= '<div class="col-md-3">
                                <div class="form-group">
                                    <label >From Weight</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                            <input class="form-control" name="from_weight" id="from_weight" type="text" onkeypress="return numbersonly(event)" value="' . $from_weight . '" placeholder="From Weight" title="From Weight" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >To Weight</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="to_weight" id="to_weight" type="text" onkeypress="return numbersonly(event)" value="' . $to_weight . '" placeholder="To Weight" title="To Weight" required>
                                    </div>
                                </div>
                            </div>';
                if ($ranges == 1) {
                    $agentDataObj = new AgentData($this->form_vars["agent_id"]);
                    $agentcode = $agentDataObj->getAgentCode();
                    $licencePlateFilterObj = new LicencePlateFilter();
                    $licencePlateFilterObj->addFilter(" range_name = '" . $agentcode . "' and addedby = '" . $user->getId() . "'");
                    $licenceplateList = $licencePlateFilterObj->getList();
                    if (count($licenceplateList) > 0) {
                        $licenceplate = $licenceplateList[0];
                        $rangeStart = $licenceplate->getRangeStart();
                        $rangeEnd = $licenceplate->getRangeEnd();
                        $prefix = $licenceplate->getPrefix();
                        $sufix = $licenceplate->getSufix();
                    }
                    $html .= '<div class="col-md-3">
                                <div class="form-group">
                                    <label >Range Start</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="range_start" id="range_start" type="text" onkeypress="return numbersonly(event)" value="' . $rangeStart . '" placeholder="Range Start" title="Range Start" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Range End</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <i class="fa tooltips font-red">*</i>
                                        <input class="form-control" name="range_end" id="range_end" type="text" onkeypress="return numbersonly(event)" value="' . $rangeEnd . '" placeholder="Range End" title="Range End" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Prefix</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <input class="form-control" name="prefix" id="prefix" type="text" value="' . $prefix . '" placeholder="Prefix" title="Prefix" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Suffix</label>
                                    <div class="input-group input-group-sm input-icon right">
                                        <span class="input-group-addon"> <i class="fa fa-cogs"></i></span>
                                        <input class="form-control" name="suffix" id="suffix" type="text" value="' . $sufix . '" placeholder="Suffix" title="Suffix" >
                                    </div>
                                </div>';
                }

                $html .= '</div>';
                $html .= ' ';
                echo $html;
                die;
            }
        } // EDIT CONTRACT




        else if (isset($this->form_vars["form_action"]) && $this->form_vars["form_action"] == "saverecord") {
            $output = array();
            $carrier_id = $this->form_vars["carrier_id"];
            $agentId = $this->form_vars["agent_id"];
            $carrier_name = $this->form_vars["carrier_name"];
            $services = $this->form_vars["agent_services"];
            $fromWeight = $this->form_vars["from_weight"];
            $toWeight = $this->form_vars["to_weight"];
            $rangeStart = $this->form_vars["range_start"];
            $rangeEnd = $this->form_vars["range_end"];
            $prefix = $this->form_vars["prefix"];
            $suffix = $this->form_vars["suffix"];


            if (sizeof($services) > 0) {
                if ($agentId <= 0) {
                    // CREATE NEW AGENT
                    $agentcode = $carrier_name . "_" . $user->getUserAccountId();
                    $agentName = $carrier_name . "_" . $user->getUserAccountId();
                    $contactName = $user->getFirstName();
                    $address = explode(",", $user->getReturnAddress());
                    $country = $user->getCountryId();
                    $telephone = $user->getTelephone();
                    $email = $user->getEmail();


                    $agentFilterObj = new AgentDataFilter();
                    $agentFilterObj->addFieldFilter("agent_code", $agentcode);
                    $agentList = $agentFilterObj->getList();
                    if (count($agentList) > 0) {
                        $agentcode = $carrier_name . "_" . time();
                    }
                    $agentObj = new AgentData($agentId);
                    $agentObj->setAgentCode($agentcode);
                    $agentObj->setAgentName($agentName);
                    $agentObj->setActive(1);
                    $agentObj->setContactName($contactName);
                    $agentObj->setAddressLine1(@$address[0]);
                    $agentObj->setAddressLine2(@$address[1]);
                    $agentObj->setCity(@$address[2]);
                    $agentObj->setCountryId($country);
                    $agentObj->setTelephone($telephone);
                    $agentObj->setEmail($email);
                    $agentObj->setUserId($user->getUserAccountId());
                    $agentObj->setDateCreated(date("Y-m-d H:i:s"));
                    $agentObj->setAgentType("carrier");
                    $agentObj->save();
                    $agentId = $agentObj->getId();
                    $newAgentObj = $agentObj;
                    $agentLog = new AgentLog();
                    $agentLog->createlog($user->getId(), '', $agentId, 'AGENT', $user->getUserName() . ' has added new agent by adding new contract ' . $agentcode, '', $newAgentObj);
                }


                //FETCH CONSTANT FOR THAT CARRIER
                $serviceConstantObj = new ServiceConstantFilter();
                $serviceConstantObj->addFilter("carrier_id = '" . $carrier_id . "' or carrier_id = 0");
                $serviceConstantObj->AddOrderBy("sort_order");
                $serviceConstantList = $serviceConstantObj->getList();

                if ($this->form_vars["agent_id"] > 0) {
                    $agentDataObj = new AgentData($this->form_vars["agent_id"]);
                    $agentcode = $agentDataObj->getAgentCode();
                    $agentDataObj->setActive(1);
                    $agentDataObj->save();
                    $serAgeFilter = new ServiceAgentMappingDataFilter();
                    $serAgeFilter->addFilter(" agentid = '" . $agentId . "'");
                    $serAgeList = $serAgeFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");
                    if (count($serAgeList) > 0) {
                        $currentServices = array();
                        foreach ($serAgeList as $serv) {
                            $currentServices[] = $serv->getServiceid();
                        }
                        $differentServices = array_diff($currentServices, $services);
                        if (sizeof($differentServices) > 0) {
                            foreach ($differentServices as $diffServiceId) {
                                $safilter = new ServiceAgentMappingDataFilter();
                                $safilter->addFilter(" agentid = '" . $agentId . "' and serviceid = '" . $diffServiceId . "'");
                                $differenceAgentList = $safilter->getColumnList(" id");
                                if (count($differenceAgentList) > 0) {
                                    foreach ($differenceAgentList as $agentid) {
                                        //DELETE FROM AGENT SERVICE MAPPING
                                        ServiceAgentMapping::deleteById($agentid->getId());

                                        //DEACTIVE SERVICE IN USER SERVICE ROUTING
                                        $diffuserServiceRouting = new UserServicesRoutingFilter();
                                        $diffuserServiceRouting->addFilter(" user_account_id = '" . $user->getUserAccountId() . "' and service_id = '" . $diffServiceId . "'");
                                        $diffuserServiceList = $diffuserServiceRouting->getList();
                                        if (count($diffuserServiceList) > 0) {
                                            foreach ($diffuserServiceList as $diffulist) {
                                                $diffulist->setStatus(0);
                                                $diffulist->save();
                                            }
                                        }

                                        //DEACTIVE FROM CUSTOMIZE RULE
                                        $diffcarrierServiceCustomizeRulesFilterObj = new carrierServiceCustomizeRulesFilter();
                                        $diffcarrierServiceCustomizeRulesFilterObj->addFilter(" serviceid = '" . $diffServiceId . "' and agentid = '" . $agentId . "' and user_account_id = '" . $user->getUserAccountId() . "'");
                                        $diffcarrierSerCutList = $diffcarrierServiceCustomizeRulesFilterObj->getList();

                                        if (count($diffcarrierSerCutList) > 0) {
                                            foreach ($diffcarrierSerCutList as $diffcarrList) {
                                                $diffcarrList->setStatus(0);
                                                $diffcarrList->save();
                                            }
                                        }

                                        //DELETE CONSTANT
                                        $diffserviceConstantValueFilter = new ServiceConstantValueFilter();
                                        $diffserviceConstantValueFilter->addFilter(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                        $diffserviceConsList = $diffserviceConstantValueFilter->getList();
                                        if (count($diffserviceConsList) > 0) {
                                            ServiceConstantValue::deleteConstant(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                        }

                                        //DELETE RANGES
                                        $diffserviceRangeMappingFilter = new ServiceRangeMappingFilter();
                                        $diffserviceRangeMappingFilter->addFilter(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                        $diffserviceRangeList = $diffserviceRangeMappingFilter->getList();
                                        if (count($diffserviceRangeList) > 0) {
                                            ServiceRangeMapping::deleteRange(" service_id = '" . $diffServiceId . "' and agent_id = '" . $agentId . "'");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                //ADD RANGES
                if ($rangeStart > 0 && $rangeEnd > 0) {
                    $licencePlateFilterObj = new LicencePlateFilter();
                    $licencePlateFilterObj->addFilter(" range_name = '" . $agentcode . "' and addedby = '" . $user->getId() . "'");
                    $licencePlateList = $licencePlateFilterObj->getList();

                    if (count($licencePlateList) > 0) {
                        $licencePlate = $licencePlateList[0];
                    } else {
                        $licencePlate = new LicencePlate();
                        $licencePlate->setDateCreated(date("Y-m-d H:i:s"));
                        $licencePlate->setAddedby($user->getId());
                    }

                    $licencePlate->setRangeName($agentcode);
                    $licencePlate->setRangeStart($rangeStart);
                    $licencePlate->setRangeEnd($rangeEnd);
                    $licencePlate->setNextNumber($rangeStart);
                    $licencePlate->setPrefix($prefix);
                    $licencePlate->setSufix($suffix);
                    $licencePlate->setUpdatedBy($user->getId());
                    $licencePlate->setDateUpdated(time());
                    $licencePlate->save();
                    $licencePlateId = $licencePlate->getId();
                }


                foreach ($services as $ser) {
                    //SAVE EACH SERVICE WITH AGENT
                    $serviceAgentFilter = new ServiceAgentMappingDataFilter();
                    $serviceAgentFilter->addFilter(" agentid = '" . $agentId . "' and serviceid = '" . $ser . "'");
                    $SerAgentList = $serviceAgentFilter->getColumnList(" id, serviceid, agentid, from_weight, to_weight");

                    if (count($SerAgentList) > 0) {
                        $serviceAgentObj = $SerAgentList[0];
                        $old_fromweight = $serviceAgentObj->getFromWeight();
                        $old_toweight = $serviceAgentObj->getToWeight();
                    } else {
                        $serviceAgentObj = new ServiceAgentMapping();
                    }
                    $serviceAgentObj->setServiceid($ser);
                    $serviceAgentObj->setagentid($agentId);
                    $serviceAgentObj->setFromWeight($fromWeight);
                    $serviceAgentObj->setToWeight($toWeight);
                    $serviceAgentObj->save();


                    //CHECK SERVICE IS UNABLE TO THE USER

                    $serviceCountryTimeObj = new ServiceCountryTimeFilter();
                    $serviceCountryTimeObj->addFilter("id_service = '" . $ser . "'");
                    $serviceCountryTimeObj->setRowsPerPage("1000");
                    $serviceCountryTimeList = $serviceCountryTimeObj->getList();
                    if (count($serviceCountryTimeList) > 0) {
                        foreach ($serviceCountryTimeList as $serviceCountryList) {
                            $userServiceRouting = new UserServicesRoutingFilter();
                            $userServiceRouting->addFilter(" user_account_id = '" . $user->getUserAccountId() . "' and service_id = '" . $ser . "' and country_id = '" . $serviceCountryList->getIdCountry() . "'");
                            $userServiceList = $userServiceRouting->getList();
                            if (count($userServiceList) > 0) {
                                foreach ($userServiceList as $ulist) {
                                    $usrStatus = $ulist->getStatus();
                                    if ($usrStatus == 0) {
                                        $ulist->setStatus(1);
                                        $ulist->save();
                                    }
                                }
                            } else {
                                // ADD RECORD FOR EACH SERVICE TO ACTIVATE FOR USER
                                $userServiceRoutingObj = new UserServicesRouting();
                                $userServiceRoutingObj->setUserAccountId($user->getUserAccountId());
                                $userServiceRoutingObj->setCountryId($serviceCountryList->getIdCountry());
                                $userServiceRoutingObj->setFromWeight($fromWeight);
                                $userServiceRoutingObj->setToWeight($toWeight);
                                $userServiceRoutingObj->setStatus(1);
                                $userServiceRoutingObj->setServiceId($ser);
                                $userServiceRoutingObj->setIsRemoteArea(0);
                                $userServiceRoutingObj->setAddedBy($user->getId());
                                $userServiceRoutingObj->save();
                            }
                        }
                    }

                    //ADD RULE IN CUSTOMIZE RULES
                    if ($this->form_vars["agent_id"] > 0) {

                        $carrierServiceCustomizeRulesFilterObj = new carrierServiceCustomizeRulesFilter();
                        $carrierServiceCustomizeRulesFilterObj->addFilter(" serviceid = '" . $ser . "' and agentid = '" . $agentId . "' and user_account_id = '" . $user->getUserAccountId() . "'");
                        $carrierSerCutList = $carrierServiceCustomizeRulesFilterObj->getList();

                        if (count($carrierSerCutList) > 0) {
                            $carrierServiceCustomizeRulesObj = $carrierSerCutList[0];
                        } else {
                            $carrierServiceCustomizeRulesObj = new carrierServiceCustomizeRules();
                        }
                    } else {
                        $carrierServiceCustomizeRulesObj = new carrierServiceCustomizeRules();
                    }

                    $carrierServiceCustomizeRulesObj->setServiceId($ser);
                    $carrierServiceCustomizeRulesObj->setAgentId($agentId);
                    $carrierServiceCustomizeRulesObj->setUserAccountId($user->getUserAccountId());
                    $carrierServiceCustomizeRulesObj->setFromWeight($fromWeight);
                    $carrierServiceCustomizeRulesObj->setToWeight($toWeight);
                    $carrierServiceCustomizeRulesObj->setStatus(1);
                    $carrierServiceCustomizeRulesObj->save();

                    //ADD CONSTANT VALUES
                    if (count($serviceConstantList) > 0) {
                        $constant = array();
                        foreach ($serviceConstantList as $serviceConstant) {
                            if (trim($this->form_vars[$serviceConstant->getConstant()]) != '') {
                                $constantId = $serviceConstant->getId();
                                $serviceConstantValueFilter = new ServiceConstantValueFilter();
                                $serviceConstantValueFilter->addFilter(" service_id = '" . $ser . "' and agent_id = '" . $agentId . "' and constant_id = '" . $constantId . "'");
                                $serviceConsList = $serviceConstantValueFilter->getList();

                                if (count($serviceConsList) > 0) {
                                    $serviceConstantValueObj = $serviceConsList[0];
                                } else {
                                    $serviceConstantValueObj = new ServiceConstantValue();
                                }
                                $serviceConstantValueObj->setConstantValue($this->form_vars[$serviceConstant->getConstant()]);
                                $serviceConstantValueObj->setServiceId($ser);
                                $serviceConstantValueObj->setAgentId($agentId);
                                $serviceConstantValueObj->setConstantId($constantId);
                                $serviceConstantValueObj->setDateCreated(strtotime(date("Y-m-d")));
                                $serviceConstantValueObj->setAddedBy($user->getId());
                                $serviceConstantValueObj->save();
                            }
                        }
                    }

                    //MAP RANGES WITH SERVICES
                    $serviceRangeMappingFilterObj = new ServiceRangeMappingFilter();
                    $serviceRangeMappingFilterObj->addFilter(" service_id = '" . $ser . "' and agent_id = '" . $agentId . "'");
                    $serviceRangeMappingList = $serviceRangeMappingFilterObj->getList();
                    if (count($serviceRangeMappingList) > 0) {
                        $serviceRangeMappingObj = $serviceRangeMappingList[0];
                    } else {
                        $serviceRangeMappingObj = new ServiceRangeMapping();
                    }
                    $serviceRangeMappingObj->setServiceId($ser);
                    $serviceRangeMappingObj->setAgentId($agentId);
                    $serviceRangeMappingObj->setLicencePlateId($licencePlateId);
                    $serviceRangeMappingObj->save();


                    $output["status"] = "SUCCESS";
                    $output["message"] = formatMessages(SUCCESS_CONTRACT_ADDED);
                }
            } else {
                $output["status"] = "ERROR";
                $output["message"] = formatMessages(ERROR_SELECT_SERVICE);
            }
            echo json_encode($output);
            exit;
        }
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "DELETE_CONTRACT") {
            $agent_id = $this->form_vars["agent_id"];
            $agent_id = $this->form_vars["agent_id"];
            $status = $this->form_vars["status"];
            $contract_name = $this->form_vars["contract_name"];
            $output = array();

            if ($agent_id > 0) {
                $carrierServiceCustomizeRulesObj = new carrierServiceCustomizeRulesFilter();
                $carrierServiceCustomizeRulesObj->addFilter(" agentid = '" . $agent_id . "' and user_account_id = '" . $user->getUserAccountId() . "' and status = ".$status." ");
                $carrierCustomizeList = $carrierServiceCustomizeRulesObj->getList("*", false);
                $userServiceIds =   array(0);
                if (count($carrierCustomizeList) > 0) {
                    foreach ($carrierCustomizeList as $carrierCusList) {
                        $carrierCusList->setStatus((($status == '1')?0:1) );
                        $carrierCusList->save();
                        $userServiceIds[] =  $carrierCusList->getServiceid();
                    }
                    if(count($userServiceIds)>0) {
                        $userServiceRouting = new UserServicesRoutingFilter();
                        $userServiceRouting->addFilter(" user_account_id = '" . $user->getUserAccountId() . "' and service_id in ('" . implode("','", $userServiceIds) . "')");
                        $userServiceRoutingList = $userServiceRouting->getList(false);
                        if (count($userServiceRoutingList) > 0) {
                            foreach ($userServiceRoutingList as $usrList) {
                                $usrList->setStatus((($status == '1')?0:1) );
                                    $usrList->save();
                            }
                        }
                    }

                    $agentDataFilter = new AgentDataFilter();
                    $agentDataFilter->addFilter(" id = '" . $agent_id . "'");
                    $agentList = $agentDataFilter->getList(false);
                    if (count($agentList) > 0) {
                        foreach ($agentList as $agent) {
                            $agent->setActive((($status == '1')?0:1) );
                            $agent->save();
                        }
                    }
                    $output["status"] = "SUCCESS";
                    $output["message"] = formatMessages(SUCCESS_DELETE_CONTRACT);
                }
            } else {
                $output["status"] = "ERROR";
                $output["message"] = formatMessages(ERROR_DELETE_CONTRACT);
            }
            echo json_encode($output);
            exit;
        }

        // common initialisation for ths page
        $this->setTitle("Carriers List");
    }

    /*     * *
     * Content View
     */

    protected function renderHead()
    {
        ?>

        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <style>
            img {
                position: relative;
            }
            img:after { 
                content: "\f1c5" " " attr(alt);
                font-size: 16px;
                font-family: FontAwesome;
                color: rgb(100, 100, 100);
                display: block;
                position: absolute;
                z-index: 2;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #fff;
            }
        </style>

        <?php
    }

    protected function renderBody()
    {
        ?>

        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        if (errorList::getItem()->getErrorCount() > 0) {
            ?>
            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>
        <!-- BEGIN TAB PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-dropbox"></i>
                    <span class="caption-subject font-dark bold uppercase">
                    Available Carrier</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#portlet_tab3" data-toggle="tab"> Carrier Selection</a>
                    </li>
                    <li>
                        <a href="#portlet_tab2" data-toggle="tab">Contracts </a>
                    </li>

                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="portlet_tab3">

                        <div class="portlet-body" id="append_all_service_list">
                            <center>Please wait<br>Data loading...</center>
                        </div>


                    </div>
                    <div class="tab-pane" id="portlet_tab2">


                        <div class="portlet-body" id="append_all_carrier_list">
                            <center>Please wait<br>Data loading...</center>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- END TAB PORTLET-->


        <!--
                    <div class="portlet">
                        <div class="portlet-body">
                            <ul class="nav nav-pills">
                                <li class="active">
                                    <a href="#tab_4_1" data-toggle="tab"> Carrier Selection </a>
                                </li>
                                <li>
                                    <a href="#tab_4_2" data-toggle="tab" onclick="displayAllContract();"> My Contracts </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_4_1">
                                    <div class="portlet-body" id="append_all_service_list"><center>Please wait<br>Data loading...</center></div>
                                </div>
                                <div class="tab-pane fade" id="tab_4_2">
                                    <div class="portlet-body" id="append_all_carrier_list"><center>Please wait<br>Data loading...</center></div>

                                </div>
                            </div>

                        </div>
                    </div> -->

        <div class="modal fade" tabindex="-1" role="dialog" id="carrier-service-popup">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="carrier-content-display">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="serviceCountryName"></span> Services</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12" id="carrier-content-display-wait">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="service-country-popup">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="serviceCountryName"></span> Countries</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12" id="service-country-content-display">

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

        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="service-modal-popup" data-backdrop="static"
             data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="show-service-info-name"></span> Carrier Setup Agreement</h4>
                    </div>
                    <div class="modal-body">
                        <div id="show-service-info-detail">
                            <p> All carrier services Labels are approved by the relevant authorities of the respective
                                integrated carriers. But these labels are approved on the basis of the given account,
                                credentials and other requirements of the particular service. These all labels are
                                currently working and usable without any problems. </p>

                            <p>It is recommended by us if any SmartTrack user would like to create their own carrier
                                setup in order to use any service, please first approve all the relevant services labels
                                from respective carriers after putting all the designated fields of requirements during
                                carrier setup.</p>

                            <p>Please note that SmartTrack is not responsible for any shipping label if labels is
                                created by user own carrier setup and is not approved by the relevant carrier. It is
                                strongly advised that please send at least five trials for each service of any carrier
                                before starting live shipments. </p>

                            <p>Each carrier service requirements can be easily found during carrier services Setup. </p>

                            <p><b>Steps of Carrier Setup.</b><br/>

                                1. Connect the relevant Carrier.<br/>
                                2. Select the relevant services would like to use.<br/>
                                3. Enter the credentials given.<br/>
                                4. Click Save. <br/>
                            </p>
                            After submission of the carrier, your service will be available for the chosen weights.

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="carrier_id" value="189" data-original-title="" title="">
                        <a href="javascript:;" id="carrier_agree" class="btn btn-primary" data-original-title="" title="">Agree</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    <!--
                    <div class="modal-footer">
                        <a href="javascript:;" id="setup_agree" class="btn btn-primary">Agree</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>-->
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="carrier-modal-popup" data-backdrop="static"
             data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="show-service-info-name"></span> Carrier Agreement</h4>
                    </div>
                    <div class="modal-body">
                        <div id="show-service-info-detail">
                            <div class="row1">


                                <div class="col-sm-6">
                                    <h3 class="text-primary">Service Details</h3>
                                    <span ng-bind-html="quote.Service.Description | html" class="ng-binding">

                                        Parcel delivery within 3 working day throughout the most of UK's mainland. Deliveries are
                                        not guaranteed but enjoy over 98% success. Delivery Service time are between 8am – 6pm.

                                        <br>
                                        <br>
                                        <b>About Correct Weight and Dimension at all times:</b><br>

                                        Failure to do so administrative penalty as well as additional charges will be applicable.
                                        Larger and heavier parcels and subject to surcharge.    Please be advised that surcharge apply for these postcode areas. Please check the postcode listings &amp; your rate card surcharges applicable. Please click here to find postcodes for Highlands, Irelands, Island Surcharges, Notes  Please note that if the correct weight has not been set by the client then the default weight will be set as 0.5 by the sytem itself when saving the consignment information.
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <h3 class="text-primary">Key Features</h3>
                                    <ul class="fa-ul margin-none">
                                        <li ng-show="quote.Service.TimedDeliveryTime" class="ng-hide"><i class="fa-li fa fa-check palette-

                                                                                                         primary"></i>
                                            Delivery by 8:00 AM to 7:00 PM
                                        </li>
                                        <li ng-show="quote.Service.IsDropOff" class="ng-hide"><i
                                                    class="fa-li fa fa-check palette-primary"></i>

                                            Drop Off service
                                        </li>
                                        <li><i class="fa-li fa fa-check palette-primary"></i>£20.00 inclusive cover</li>
                                        <li><i class="fa-li fa fa-check palette-primary"></i>Fully tracked service</li>
                                        <li><i class="fa-li fa fa-check palette-primary"></i>From UK to UK</li>
                                        <li><i class="fa-li fa fa-check palette-primary"></i>Door to Door Delivery</li>
                                        <li><i class="fa-li fa fa-check palette-primary"></i>Signed Delivery</li>
                                        <li ng-show="quote.Service.DeliveryAlert" class="ng-hide"><i class="fa-li fa fa-check palette-
                                                                                                     primary"></i> SMS
                                            alert optional
                                        </li>
                                        <li class="ng-binding"><i class="fa-li fa fa-check palette-primary"></i> Protect
                                            your parcel up to the

                                            value of £2,500.00
                                        </li>
                                    </ul>
                                    <hr>
                                    <h3 class="text-primary">Restrictions</h3>
                                    <ul class="fa-ul margin-none">
                                        <!-- ngIf: !quote.Service.MoreInfo -->
                                        <li ng-if="!quote.Service.MoreInfo" class="ng-scope"><i class="fa-li

                                                                                                                                      fa fa-ban palette-primary"></i>
                                            Check
                                                the prohibited items list</li>
                                        <li class="ng-binding"><i class="fa-li fa fa-balance-scale palette-primary"></i>
                                            Maximum Weight 30 kg
                                        </li>
                                        <li class="ng-binding"><i class="fa-li fa fa-expand palette-primary"></i>
                                            Maximum Length 60cm and overall size not bigger than 0-112 cubic
                                        </li>

                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="carrier_id" value=""/>
                        <a href="javascript:;" id="carrier_agree" class="btn btn-primary">Agree</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <?php /*
        <div class="modal fade" tabindex="-1" role="dialog" id="own-carrier-popup">
            <div class="modal-dialog modal-lg">

                <div class="modal-content">
                   <div class="modal-body" >
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet light" id="form_wizard_1">
                                    <form class="form-horizontal" action="#" id="test" method="POST">
                                        <div class="form-wizard">
                                            <div class="form-body">
                                                <ul class="nav nav-pills nav-justified steps">
                                                    <li>
                                                        <a href="#select_carrier" data-toggle="tab" class="step">
                                                            <span class="number"> 1 </span>
                                                            <span class="desc">
                                                                <i class="fa fa-check"></i>Select A Carrier</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#select_services" data-toggle="tab" class="step">
                                                            <span class="number"> 2 </span>
                                                            <span class="desc">
                                                                <i class="fa fa-check"></i> Select Services</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#carrier_details" data-toggle="tab" class="step">
                                                            <span class="number"> 3 </span>
                                                            <span class="desc">
                                                                <i class="fa fa-check"></i>Contract Details</span>
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#confirmation" data-toggle="tab" class="step active">
                                                            <span class="number"> 4 </span>
                                                            <span class="desc">
                                                                <i class="fa fa-check"></i> Confirmation </span>
                                                        </a>
                                                    </li>

                                                </ul>
                                                <div id="bar" class="progress progress-striped" role="progressbar">
                                                    <div class="progress-bar progress-bar-success"> </div>
                                                </div>
                                                <div class="tab-content">
                                                    <div class="alert alert-danger display-none">
                                                        <button class="close" data-dismiss="alert"></button> You have some form errors. Please check below. </div>
                                                    <div class="alert alert-success display-none">
                                                        <button class="close" data-dismiss="alert"></button> Your form validation is successful! </div>
                                                    <div class="tab-pane active" id="select_carrier">
                                                        <div id="show_carrier_to_select"> </div>
                                                    </div>
                                                    <div class="tab-pane active" id="select_services">
                                                        <div class="alert alert-danger display-none display-service-error">
                                                            <button class="close" data-dismiss="alert"></button> <span id="display-service-message"></span></div>

                                                        <div id = "services-detail-setup"> </div>
                                                    </div>
                                                    <div class="tab-pane" id="carrier_details">

                                                        <div class="alert alert-danger display-none display-contract-field-error">
                                                            <button class="close" data-dismiss="alert"></button> <span id="display-contract-field-message"></span></div>
                                                        <div id = "contract-detail-setup"> </div>
                                                    </div>
                                                    <div class="tab-pane" id="confirmation">
                                                        <h3 class="block ">Confirm Account Details</h3>
                                                        <div id = "account-number-confirm">
                                                            <h4 class="block ">Account Number</h4>
                                                        </div>
                                                        <div id = "carrier-setup-confirm">
                                                            <h4 class="block ">Selected Services</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- form body -->
                                            <div class="form-actions">
                                                <div class="row">
                                                    <div class="col-md-offset-3 col-md-9">

                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- form wizard -->
                                    </form>

                                </div> <!-- wizard -->
                            </div> <!-- col-md-12 -->
                        </div>    <!-- row -->

                    </div> <!--modal-body -->
                    <div class="modal-footer" >
                        <input type="hidden" id="get_carrier_option" name="get_carrier_option">
                        <input type="hidden" id="get_carrier_setupid" name="get_carrier_setupid">
                        <a href="javascript:;" class="btn default btnPrevious">
                            <i class="fa fa-angle-left "></i> Back </a>
                        <a href="javascript:;" class="btn btn-outline btn-primary button-next display-contract-values-on-confirmation btnNext" > Continue
                            <i class="fa fa-angle-right"></i>
                        </a>

                        <a href="javascript:;" id="btn_Save" class="btn btn-outline  btn-primary button-submit"> Submit
                            <i class="fa fa-check"></i>
                        </a>
                </div> <!--modal-footer -->


                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        */ ?>

        <!--------------------------------------------------->
        <div class="modal fade" tabindex="-1" role="dialog" id="own-carrier-popup">
            <div class="modal-dialog modal-lg">
        <div class="portlet light bordered" id="form_wizard_1">
            <div class="portlet-title">
                <div class="caption">
                    <i class=" icon-layers font-red"></i>
                    <span class="caption-subject font-red bold uppercase"> Setup Carrier -
                                            <span class="step-title"> Step 1 of 4 </span>
                                        </span>
                </div>
                <!--<div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
                        <i class="icon-cloud-upload"></i>
                    </a>
                    <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
                        <i class="icon-wrench"></i>
                    </a>
                    <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
                        <i class="icon-trash"></i>
                    </a>
                </div>-->
            </div>
            <div class="portlet-body form">
                <form class="form-horizontal" action="#" id="submit_form" method="POST">
                    <div class="form-wizard">
                        <div class="form-body">
                            <ul class="nav nav-pills nav-justified steps">
                                <li class="active">
                                    <a href="#tab1" data-toggle="tab" class="step active">
                                        <span class="number">1</span>
                                        <span class="desc">
                                        <i class="fa fa-check"></i> Select Carrier </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab2" data-toggle="tab" class="step">
                                        <span class="number">2</span>
                                        <span class="desc">
                                        <i class="fa fa-check"></i> Select Services </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab3" data-toggle="tab" class="step">
                                        <span class="number">3</span>
                                        <span class="desc">
                                        <i class="fa fa-check"></i> Contract Details </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab4" data-toggle="tab" class="step">
                                        <span class="number">4</span>
                                        <span class="desc">
                                        <i class="fa fa-check"></i> Confirmation </span>
                                    </a>
                                </li>
                            </ul>

                            <div id="bar" class="progress progress-striped" role="progressbar">
                                <div class="progress-bar progress-bar-success"> </div>
                            </div>
                            <div class="tab-content">
                                <div class="alert alert-info" id="selected-carrier-here"> Please select a carrier and press continue</div>
                                <div class="alert alert-danger display-none">
                                    <button class="close" data-dismiss="alert"></button> You have some form errors. Please check below. </div>
                                <div class="alert alert-success display-none">
                                    <button class="close" data-dismiss="alert"></button> Your form validation is successful! </div>
                                <div class="tab-pane active" id="tab1">
                                    <div style="opacity: 0;">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm input-icon right">
                                            <input name="get_carrier_setupid" id="get_carrier_setupid" value="" size="50" class="form-control" maxlength="35" title="" placeholder="Carrier" rel="tooltip" data-original-title="Carrier" type="text" required>

                                        </div>
                                    </div>
                                    </div>


                                    <div id="show_carrier_to_select"> </div>
                                </div>
                                <div class="tab-pane" id="tab2">
                                    <div id = "services-detail-setup"> </div>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <div id = "contract-detail-setup"> </div>
                                </div>
                                <div class="tab-pane" id="tab4">
                                    <h4 class="form-section">Carrier & Services</h4>
                                    <div class="confirm_carrier_details">
                                    </div>
                                    <h4 class="form-section">Contract Details</h4>
                                    <div class="confirm_contact_details">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div >
                            <div class="row">
                                <input type="hidden" id="get_carrier_option" name="get_carrier_option">
                                <div class="col-md-12">
                                    <a href="javascript:;" class="btn btn-primary button-submit btn-sm pull-right" id="btn_Save">  Submit
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-primary button-next disabled btn-sm pull-right" > Continue
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                    <a href="javascript:;" class="btn default button-previous btn-sm pull-right">
                                        <i class="fa fa-angle-left"></i> Back
                                    </a>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
                </div></div>

        <!--------------------------------------------------->
        <div class="modal fade" tabindex="-1" role="dialog" id="edit-carrier-popup">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="carrier-content-display">
                    <form action="#" id="edit-form-content" method="POST">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="serviceCountryName"></span> Own Contract Update</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" id="edit-contract-success-messages"></div>
                        <div class="row">

                            <div class="col-md-12" id="edit-carrier-contents">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="form_action" id="form_action" value="edit_record_contract" />
                        <input type="hidden" name="carrier_id" id="carrier_id" value="" />
                        <input type="hidden" name="agent_id" id="agent_id" value="" />
                        <input id="btn_edit_save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
