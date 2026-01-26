<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    public $displayname = '';
    public $account = '';

    /*
     * Controller logic
     */

    protected function init() {

        $user = SessionManager::getUser();
        if ($user->getUserType() != "finance" && $user->getUserType() != "admin") {
            util_redirect("index.php");
        }


        if (isset($_POST['action']) && trim($_POST['action']) == 'DELETE-POSTCODE-BY-NAME') {
            $responseArray = array();
            $userId = $_POST['userid'];
            if (trim($userId) != 'DEFAULT') {
                $filteruser = new UserAccountFilter();
                $filteruser->addIdFilter($userId);
                $routinguser = $filteruser->getList();
                if (!empty($routinguser)) {
                    $this->displayname = $routinguser[0]->getUserName();
                    $this->account = $routinguser[0]->getUserAccount();
                } else {
                    $responseArray['STATUS'] = 'ERROR';
                    $responseArray['MESSAGE'] = 'User is not selected.';
                    echo json_encode($responseArray);
                    exit;
                }
            } else {
                $this->displayname = 'DEFAULT';
                $this->account = 'ALL';
            }
            $service_code = $_POST['service_code'];
            $postcode_name = $_POST['postcode_name'];

            $serviceFileter = new RemoteareaUserMappingFilter();
            $serviceFileter->addFieldFilter('user_account', $this->account);
            $serviceFileter->addFieldFilter('service_code', $service_code);
            $serviceFileter->addFieldFilter('postcode_name', $postcode_name);
            $service_list = $serviceFileter->expunge(); //, postcode_name, charges 

            $responseArray['STATUS'] = 'SUCCESS';
            $responseArray['MESSAGE'] = 'You have successfully remove the remote areas named ' . $postcode_name . '.' . $service_list;
            echo json_encode($responseArray);
            exit;

            die;
        }

        if (isset($_POST['action']) && trim($_POST['action']) == 'DELETE-POSTCODE-BY-NAME-WEIGHT') {
            $responseArray = array();
            $userId = $_POST['userid'];
            if (trim($userId) != 'DEFAULT') {
                $filteruser = new UserAccountFilter();
                $filteruser->addIdFilter($userId);
                $routinguser = $filteruser->getList();
                if (!empty($routinguser)) {
                    $this->displayname = $routinguser[0]->getUserName();
                    $this->account = $routinguser[0]->getUserAccount();
                } else {
                    $responseArray['STATUS'] = 'ERROR';
                    $responseArray['MESSAGE'] = 'User is not selected.';
                    echo json_encode($responseArray);
                    exit;
                }
            } else {
                $this->displayname = 'DEFAULT';
                $this->account = 'ALL';
            }
            $service_code = $_POST['service_code'];
            $postcode_name = $_POST['postcode_name'];

            $serviceFileter = new RemoteareaUserMappingFilter();
            $serviceFileter->addFieldFilter('user_account', $this->account);
            $serviceFileter->addFieldFilter('service_code', $service_code);
            $serviceFileter->addFieldFilter('postcode_name', $postcode_name);
            $service_list = $serviceFileter->expunge(); //, postcode_name, charges 

            DbAccess3::runQuery("DELETE FROM remotearea_weight_charge WHERE service_code = '" . $service_code . "' AND postcode_name = '" . $postcode_name . "' ");

            $responseArray['STATUS'] = 'SUCCESS';
            $responseArray['MESSAGE'] = 'You have successfully remove the remote areas named ' . $postcode_name . '.' . $service_list;
            echo json_encode($responseArray);
            exit;

            die;
        }


        if (isset($_POST['action']) && trim($_POST['action']) == 'SAVE-POSTCODE-BY-NAME') {
            $responseArray = array();
            $userid = $_POST['userid'];
            $charges = $_POST['charges'];
            if (trim($userid) != 'DEFAULT') {
                $filteruser = new UserAccountFilter();
                $filteruser->addIdFilter($userid);
                $routinguser = $filteruser->getList();
                if (!empty($routinguser)) {
                    $this->displayname = $routinguser[0]->getUserName();
                    $this->account = $routinguser[0]->getUserAccount();
                } else {
                    $responseArray['STATUS'] = 'ERROR';
                    $responseArray['MESSAGE'] = 'User is not selected.';
                    echo json_encode($responseArray);
                    exit;
                }
            } else {
                $this->displayname = 'DEFAULT';
                $this->account = 'ALL';
            }
            $service_code = $_POST['service_code'];
            $postcode_name = $_POST['postcode_name'];

            DbAccess3::runQuery("UPDATE 
									remotearea_user_mapping SET charges = '" . $charges . "' 
								WHERE 
									postcode_name = '" . $postcode_name . "' AND 
									service_code = '" . $service_code . "'  AND
									user_account = '" . $this->account . "'");


            $responseArray['STATUS'] = 'SUCCESS';
            $responseArray['MESSAGE'] = 'You have successfully updated the remote areas named ' . $postcode_name . '.' . $service_code;
            echo json_encode($responseArray);
            exit;

            die;
        }



        if (isset($_POST['action']) && trim($_POST['action']) == 'DETAIL-POSTCODE-BY-NAME-WEIGHT') {
            $responseArray = array();
            $userId = $_POST['userid'];
            if (trim($userId) != 'DEFAULT') {
                $filteruser = new UserAccountFilter();
                $filteruser->addIdFilter($userId);
                $routinguser = $filteruser->getList();
                if (!empty($routinguser)) {
                    $this->displayname = $routinguser[0]->getUserName();
                    $this->account = $routinguser[0]->getUserAccount();
                } else {
                    $responseArray['STATUS'] = 'ERROR';
                    $responseArray['MESSAGE'] = 'User is not selected.';
                    echo json_encode($responseArray);
                    exit;
                }
            } else {
                $this->displayname = 'DEFAULT';
                $this->account = 'ALL';
            }
            $postcode_name = $_POST['postcode_name'];
            $service_code = $_POST['service_code'];

            //$service_code	=	$_POST['service_name'];
            //$remotetype		=	$_POST['remotetype'];



            $serviceFileter = new RemoteareaWeightChargeFilter();
            $serviceFileter->addFieldFilter('postcode_name', $postcode_name);
            $serviceFileter->addFieldFilter('service_code', $service_code);
            $service_list = $serviceFileter->getColumnList(" id, weight_from, weight_to, (select name from services where code = service_code) 'service_code', (select name from country where iso = country_iso) 'country_iso', postcode_name, formulla, charges "); //, postcode_name, charges 
            if (count($service_list) > 0) {
                $htmlResponse = '<div class="col-sm-12"><strong>
						<div class="col-sm-4">
							Weight (Kgs) 
						</div>
						<div class="col-sm-4">
							Information 
						</div>
						<div class="col-sm-4">
							Charges
						</div>
						</strong>
						</div>';
                $htmlResponse .= '<div class="col-sm-12">';
                foreach ($service_list as $service) {
                    $htmlResponse .= '<div class="row">
						<div class="col-sm-2">
							<div class="col-sm-6">' . $service->getWeightFrom() . '</div>	
							<div class="col-sm-6">' . $service->getWeightTo() . '</div>	
						</div>
						<div class="col-sm-6">
							<div class="col-sm-4">' . $service->getServiceCode() . '</div>	
							<div class="col-sm-4">' . $service->getPostcodeName() . '</div>	
							<div class="col-sm-4">' . $service->getCountryIso() . '</div>	
						</div>
						<div class="col-sm-4">
							<div class="col-sm-4">' . $service->getFormulla() . '</div>
							<div class="col-sm-4">' . $service->getCharges() . '</div>
							<div class="col-sm-4"></div>
							
						</div>

						</div>';
                }
                $htmlResponse .= '</div>';
                $responseArray['STATUS'] = 'SUCCESS';
                $responseArray['MESSAGE'] = 'Please check the remote areas rates below.';
                $responseArray['DATA'] = $htmlResponse;
                echo json_encode($responseArray);
                exit;
            } else {
                $responseArray['STATUS'] = 'ERROR';
                $responseArray['MESSAGE'] = 'No remote area price available.';
                echo json_encode($responseArray);
                exit;
            }


            die;
        }


        if (isset($_POST['action']) && trim($_POST['action']) == 'GET_REMOTEAREA_CHARGES_DETAIL') {
            $responseArray = array();
            $userId = $_POST['userid'];
            if (trim($userId) != 'DEFAULT') {
                $filteruser = new UserAccountFilter();
                $filteruser->addIdFilter($userId);
                $routinguser = $filteruser->getList();
                if (!empty($routinguser)) {
                    $this->displayname = $routinguser[0]->getUserName();
                    $this->account = $routinguser[0]->getUserAccount();
                } else {
                    $responseArray['STATUS'] = 'ERROR';
                    $responseArray['MESSAGE'] = 'User is not selected.';
                    echo json_encode($responseArray);
                    exit;
                }
            } else {
                $this->displayname = 'DEFAULT';
                $this->account = 'ALL';
            }
            $service_code = $_POST['service_name'];
            $remotetype = $_POST['remotetype'];



            $serviceFileter = new RemoteareaUserMappingFilter();
            $serviceFileter->addFieldFilter('user_account', $this->account);
            $serviceFileter->addFieldFilter('service_code', $service_code);
            $service_list = $serviceFileter->getColumnDistinctList(" distinct postcode_name, charges "); //, postcode_name, charges 
            if (count($service_list) > 0) {
                if (trim($remotetype) == 'ON_WEIGHT')
                    $headinds = '<div class="col-sm-9">Name</div>
							<div class="col-sm-3">Action</div>';
                else
                    $headinds = '<div class="col-sm-6">Name</div>
							<div class="col-sm-3">Charges</div>
							<div class="col-sm-3">Action</div>';

                $htmlResponse = '<div class="row"><strong>
						<div class="col-sm-6">
							<div class="form-control" title="View Remote Areas Details">
								' . $headinds . '
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-control" title="View Remote Areas Details">
								' . $headinds . '
							</div>
						</div>
						</strong>
						</div>';
                $htmlResponse .= '<div class="row">';
                foreach ($service_list as $service) {
                    if (trim($remotetype) != 'ON_WEIGHT') {
                        $htmlsDFaya = '<div class="col-sm-6">' . $service->getPostcodeName() . '</div>
										<div class="col-sm-3" style="text-align:right;">' . number_format($service->getCharges(), 2) . '</div>
										<div class="col-sm-3" style="text-align:right;">
										<a href="javascript:;" title="UPDATE ' . $service->getPostcodeName() . ' Charges " 
											class="update-postcode-by-name" data-postcodename="' . $service->getPostcodeName() . '"
											data-charges="' . $service->getCharges() . '"
											data-servicecode="' . $service_code . '"
											data-userid="' . $userId . '"
											><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;<a href="../main/remoteareas.php?form_action=VIEW_REMOTE_AREAS&post_code_name=' . $service->getPostcodeName() . '" target="_blank" title="View remoteareas related to ' . $service->getPostcodeName() . ' Charges" 
											
											data-postcodename="' . $service->getPostcodeName() . '"
										data-servicecode="' . $service_code . '"
										data-userid="' . $userId . '"
										><i class="fa fa-eye" aria-hidden="true"></i></a>&nbsp;<a href="javascript:;" title="Remove ' . $service->getPostcodeName() . ' Charges" 
										onclick="return confirm(\'Are you sure, you want to remove ' . $service->getPostcodeName() . '  ? \');"
										class="delete-postcode-by-name" data-postcodename="' . $service->getPostcodeName() . '"
										data-servicecode="' . $service_code . '"
										data-userid="' . $userId . '"
										><i class="fa fa-times" aria-hidden="true"></i></a>
										</div>';
                    } else {
                        $htmlsDFaya = '<div class="col-sm-9">' . $service->getPostcodeName() . '</div>
										<div class="col-sm-3" style="text-align:right;">
										<a href="javascript:;" title="UPDATE ' . $service->getPostcodeName() . ' Charges " 
											class="update-postcode-by-name-weight" data-postcodename="' . $service->getPostcodeName() . '"
											data-charges="' . $service->getCharges() . '"
											data-servicecode="' . $service_code . '"
											data-userid="' . $userId . '"
											><i class="fa fa-pencil" aria-hidden="true"></i></a>
											<a href="javascript:;" title="Remove ' . $service->getPostcodeName() . ' Charges" 
										onclick="return confirm(\'Are you sure, you want to remove ' . $service->getPostcodeName() . '  ? \');"
										class="delete-postcode-by-name" data-postcodename="' . $service->getPostcodeName() . '"
										data-servicecode="' . $service_code . '"
										data-userid="' . $userId . '"
										><i class="fa fa-times" aria-hidden="true"></i></a>
										</div>';
                    }

                    $htmlResponse .= '<div class="col-sm-6" id="pcode-' . $service->getPostcodeName() . '">
							<div class="form-control" title="View Remote Areas Details">
								' . $htmlsDFaya . '
							</div>
						</div>';
                }
                $htmlResponse .= '</div>';
                $responseArray['STATUS'] = 'SUCCESS';
                $responseArray['MESSAGE'] = 'Please check the remote areas rates below.';
                $responseArray['DATA'] = $htmlResponse;
                echo json_encode($responseArray);
                exit;
            } else {
                $responseArray['STATUS'] = 'ERROR';
                $responseArray['MESSAGE'] = 'No remote area price available.';
                echo json_encode($responseArray);
                exit;
            }


            die;
        }

        $userId = util_get_num("id");

        if ($userId != "" && $_GET['id'] != 'DEFAULT') {
            $filteruser = new UserAccountFilter();
            $filteruser->addIdFilter($userId);
            $routinguser = $filteruser->getList();
            if (!empty($routinguser)) {
                $this->displayname = $routinguser[0]->getUserName();
                $this->account = $routinguser[0]->getUserAccount();
            } else {
                $this->displayname = '';
                $this->account = '';
            }
        } else if ($_GET['id'] == 'DEFAULT') {
            $this->displayname = 'DEFAULT';
            $this->account = 'ALL';
        }


        t_on(); // turn on trace for this page
        // common initialisation for ths page
        $this->setTitle("User Edit");
        if (isset($this->form_vars["form_action"])) {


            if (count($this->form_vars["postcode_group"]) > 0) {
                print_r($this->form_vars["postcode_group"]);
                $postcodesGroup = explode(',', $this->form_vars["postcode_group"]);
                if (count($postcodesGroup) > 0) {
                    foreach ($postcodesGroup as $postcodeName) {
                        if (trim($postcodeName) != '') {
                            $remoteAreaChargesFileter = new RemoteareaUserMappingFilter();
                            $remoteAreaChargesFileter->addFieldFilter('user_account', $this->account);
                            $remoteAreaChargesFileter->addFieldFilter('service_code', $this->form_vars["service_id"]);
                            $remoteAreaChargesFileter->addFieldFilter('postcode_name', $postcodeName);
                            $remoteAreaChargesList = $remoteAreaChargesFileter->getColumnList(' service_id, sur_charge, extra_charge ');
                            if (count($remoteAreaChargesList) > 0) {
                                $remoteAreaChargesData = $remoteAreaChargesList[0];
                            } else {
                                $remoteAreaChargesData = new RemoteareaUserMapping();
                            }
                            $remoteAreaChargesData->setUserAccount($this->account);
                            $remoteAreaChargesData->setServiceCode(trim($this->form_vars["service_id"]));
                            $remoteAreaChargesData->setCharges(trim($this->form_vars["sur_charge"]));
                            $remoteAreaChargesData->setPostcodeName($postcodeName);
                            $remoteAreaChargesData->setRemoteareaAddedDate(date("Y-m-d h:i:s"));

                            $remoteAreaChargesData->save();
                        }
                    }
                }
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'DELETE') {
            $remoteAreaChargesFileter = new RemoteareaUserMappingFilter;
            $remoteAreaChargesFileter->addFieldFilter('id', $_GET['record']);
            $remoteAreaChargesFileter->expunge();
        }
//		$toolbar = Toolbar::getItem();
//		$toolbar->showUserList();
    }

    protected function renderHead() {
        ?>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <link rel="stylesheet" href="/resources/demos/style.css">
        <style>
            .ui-autocomplete-loading {
                background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
            }
        </style>
        <script>
            $(function () {
                function split(val) {
                    return val.split(/,\s*/);
                }
                function extractLast(term) {
                    return split(term).pop();
                }
                $("#postcode_group")
        // don't navigate away from the field on tab when selecting an item
                        .bind("keydown", function (event) {
                            if (event.keyCode === $.ui.keyCode.TAB &&
                                    $(this).autocomplete("instance").menu.active) {
                                event.preventDefault();
                            }
                        })
                        .autocomplete({
                            source: function (request, response) {
                                $.getJSON("remotearea_ajax.php", {
                                    postcode: extractLast(request.term),
                                    action: 'getRemoteAreaData',
                                }, response);
                            },
                            search: function () {
        // custom minLength
                                var term = extractLast(this.value);
                                if (term.length < 2) {
                                    return false;
                                }
                            },
                            focus: function () {
        // prevent value inserted on focus
                                return false;
                            },
                            select: function (event, ui) {
                                var terms = split(this.value);
        // remove the current input
                                terms.pop();
        // add the selected item
                                terms.push(ui.item.value);
        // add placeholder to get the comma-and-space at the end
                                terms.push("");
                                this.value = terms.join(", ");
                                return false;
                            }
                        });
            });


            $(document).ready(function () {
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();

                $('.download-tariff-popup').click(function (e) {
                    $("#download_tariff_msg").hide();
                    $("#download_tariff").modal("show");
                    $(".download-tariff-service-area").html("Please wait, we are dealing your request.");

                    var elm = $(this);
                    var service_name = elm.data('service-name');
                    var userid = elm.data('userid');
                    var action = elm.data('action');
                    var remotetype = elm.data('remotetype');
                    var service_detail = elm.data('service-detail');

                    $.post(
                            "list_remotearea_charges.php",
                            {action: action, service_name: service_name, userid: userid, remotetype: remotetype},
                            function (data) {
                                var obj = JSON.parse(data);
                                $("#service_detail").html(service_detail);
                                if (obj.STATUS == 'ERROR')
                                {
                                    $('#download_tariff_msg').show()
                                    $('#download_tariff_msg').html(obj.MESSAGE);
                                    setTimeout(function ()
                                    {
                                        $('#download_tariff_msg').css('background-color', '#EDEDE7');
                                    }, 3000);
                                } else
                                {

                                    $('#download_tariff_msg').show()
                                    $(".download-remote-area").html(obj.DATA);
                                    $('#download_tariff_msg').html(obj.MESSAGE);




                                    if (remotetype == 'ON_WEIGHT')
                                    {
                                        $('.update-postcode-by-name-weight').click(function (e) {
                                            $('#update_remotearea_weight_msg').hide()
                                            $("#update_remote_weight_charges").modal("show");
                                            var elm = $(this);
                                            var postcode_name = elm.data('postcodename');
                                            var service_code = elm.data('servicecode');
                                            var userid = elm.data('userid');
                                            var charges = elm.data('charges');
                                            $('#remote_area_name').val(postcode_name);
                                            $('#remote_area_service').val(service_code);
                                            $('#remote_area_user').val(userid);
                                            $('#remote_area_charges').val(charges);
                                            $.post(
                                                    "list_remotearea_charges.php",
                                                    {
                                                        action: 'DETAIL-POSTCODE-BY-NAME-WEIGHT',
                                                        postcode_name: postcode_name,
                                                        service_code: service_code,
                                                        userid: userid,
                                                    },
                                                    function (data) {
                                                        var obj = JSON.parse(data);
                                                        $('#update_remotearea_weight_msg').show()
                                                        $('#update_remotearea_weight_msg').html(obj.MESSAGE);

                                                        if (obj.STATUS == 'ERROR')
                                                        {

                                                        } else
                                                        {
                                                            $('#weight_data_display').html(obj.DATA);
                                                            setTimeout(function ()
                                                            {
                                                                $('#download_tariff_msg').css('background-color', '#EDEDE7');

                                                            }, 3000);
                                                        }




                                                    });

                                        });

                                        $('.delete-postcode-by-name').click(function (e) {

                                            var elm = $(this);
                                            var postcode_name = elm.data('postcodename');
                                            var service_code = elm.data('servicecode');
                                            var userid = elm.data('userid');
                                            var action = elm.data('action');
                                            $.post(
                                                    "list_remotearea_charges.php",
                                                    {
                                                        action: 'DELETE-POSTCODE-BY-NAME',
                                                        postcode_name: postcode_name,
                                                        service_code: service_code,
                                                        userid: userid,

                                                    },
                                                    function (data) {

                                                        var obj = JSON.parse(data);
                                                        $('#pcode-' + postcode_name).remove();
                                                        $('#download_tariff_msg').show()
                                                        $('#download_tariff_msg').html(obj.MESSAGE);

                                                        setTimeout(function ()
                                                        {
                                                            $('#download_tariff_msg').css('background-color', '#EDEDE7');

                                                        }, 3000);

                                                    });

                                        });
                                    } else
                                    {
                                        $('.update-postcode-by-name').click(function (e) {
                                            $('#update_remotearea_msg').hide()
                                            $("#update_remote_charges").modal("show");
                                            var elm = $(this);
                                            var postcode_name = elm.data('postcodename');
                                            var service_code = elm.data('servicecode');
                                            var userid = elm.data('userid');
                                            var charges = elm.data('charges');
                                            $('#remote_area_name').val(postcode_name);
                                            $('#remote_area_service').val(service_code);
                                            $('#remote_area_user').val(userid);
                                            $('#remote_area_charges').val(charges);

                                        });

                                        $('.delete-postcode-by-name').click(function (e) {

                                            var elm = $(this);
                                            var postcode_name = elm.data('postcodename');
                                            var service_code = elm.data('servicecode');
                                            var userid = elm.data('userid');
                                            var action = elm.data('action');
                                            $.post(
                                                    "list_remotearea_charges.php",
                                                    {
                                                        action: 'DELETE-POSTCODE-BY-NAME-WEIGHT',
                                                        postcode_name: postcode_name,
                                                        service_code: service_code,
                                                        userid: userid,
                                                    },
                                                    function (data) {
                                                        var obj = JSON.parse(data);
                                                        $('#pcode-' + postcode_name).remove();
                                                        $('#download_tariff_msg').show()
                                                        $('#download_tariff_msg').html(obj.MESSAGE);

                                                        setTimeout(function ()
                                                        {
                                                            $('#download_tariff_msg').css('background-color', '#EDEDE7');

                                                        }, 3000);

                                                    });

                                        });
                                    }






                                }



                            });

                });
                $("#saveCharges").click(function () {

                    $.post(
                            "list_remotearea_charges.php",
                            {
                                action: 'SAVE-POSTCODE-BY-NAME',
                                postcode_name: $('#remote_area_name').val(),
                                service_code: $('#remote_area_service').val(),
                                userid: $('#remote_area_user').val(),
                                charges: $('#remote_area_charges').val(),
                            },
                            function (data) {

                                var obj = JSON.parse(data);

                                $('#update_remotearea_msg').show()
                                $('#update_remotearea_msg').html(obj.MESSAGE);

                                setTimeout(function ()
                                {
                                    $('#update_remotearea_msg').css('background-color', '#EDEDE7');

                                }, 3000);

                            });
                })


                $("#btnSave").click(function () {
                    $("#form_action").val("save");
                    $("#adminForm").submit();
                })
                $('#postcode_group').keypress(function (e)
                {
                    var keyCode = e.which;

                });
            });
        </script>
        <style type="text/css">

            #postcode_group 
            { 

            } 


            input[type=text]:focus, textarea:focus, #postcode_group:focus
            {
                background-color: #FFC;
                border: 1px solid #21286F;
            }

            .tableA
            {


                margin-right : 200px;
                display: inline-block;
            }

            .error
            {
                font-size: 18px;
                color: red;   
            }

            .success
            {
                font-size: 18px;
                color: green;   
            }

            .web_dialog_overlay
            {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                height: 100%;
                width: 100%;
                margin: 0;
                padding: 0;
                background: #000000;
                opacity: .5;
                filter: alpha(opacity=15);
                -moz-opacity: .15;
                z-index: 101;
                display: none;
            }
            .web_dialog_alert
            {
                position: fixed;
                top: 50%;
                left: 50%;

                padding: 0px;
                z-index: 102;
                font-family: Verdana;
                font-size: 10pt;
            }




        </style> 
        <?php
    }

    private function getCellData($countryId, $lowerLimit, $upperLimit) {
        
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    private function showMessage() {
        if (isset($this->form_vars['hawb'])) {

            $str = '';
            if (count($this->error) > 0) {
                foreach ($this->error as $err) {
                    $str .= $err . '<br>';
                }

                echo '<script>$("#msg").addClass("error");</script>' . $str;
            } else {
                $str = 'Changes saved successfully';
                echo '<script>$("#msg").addClass("success");</script>' . $str;
            }
        }
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $userId = util_get_num("id");
        ?>
        <input id="form_action" type="hidden" name="form_action" value="" />
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/customers.php?action=all">Users</a></li>
            <li><a href="../main/customers_details.php?id=<?php echo util_get_num("id"); ?>"><?php if (util_get_num("id") > 0) echo 'Edit';
        else echo 'Add'; ?></a></li>
            <li><a href="#">Remote Area Charges</a></li>
        </ul>
        <?php
        $sessionUser = SessionManager::getUser();
        //print_r($sessionUser);
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="main_formpage"> 
            <h1 class="heading">&nbsp;

            </h1>
            <div class="form_container">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption"> <i class="icon-docs"></i><?php
        echo "Remote Area Charges ";
        if ($this->displayname != '')
            print('for ' . $this->displayname);
        ?></div>
                        <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                    </div>

                    <div class="portlet-body">
                        <div id='msg'>
        <?php $this->showMessage(); ?>
                        </div>	
        <?php errorList::getItem()->render(); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-md-3">
                                    <div class="form-group col-md-12">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-qrcode"></i> </span>
                                            <input id="postcode_group" type="text" name="postcode_group" value="" class="form-control" maxlength="30" rel="tooltip" placeholder="Assign Remote Area" data-original-title="Assign Remote Area"/>
                                        </div>
                                    </div>
                                </div>



                                <div class="form-group col-md-3">
                                    <div class="form-group col-md-12">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                            <select id="service_id" name="service_id" class="form-control" rel="tooltip" placeholder="Service" data-original-title="Service">
        <?php
        $serviceid = new ServiceFilter();
        $servicelist = $serviceid->getColumnList(' id, code, name ');
        foreach ($servicelist as $serviceData) {
            $selected = "";
            ?>
                                                    <option value="<?php echo $serviceData->getCode(); ?>" ><?php echo $serviceData->getName(); ?></option>
            <?php
        }
        ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group col-md-3">
                                    <div class="form-group col-md-12">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                            <input id="surcharge" name="sur_charge" type="text" value="<?php echo @$sur_charge; ?>" class="form-control" maxlength="30" rel="tooltip" placeholder="Remote Area Charge" data-original-title="Remote Area Charge"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="form-group col-md-12">
                                        <div class="input-group"> 
                                            <label> User <span style="color: red;"> * </span> :</label> <?php if (isset($this->displayname)) echo $this->displayname; ?> 
                                        </div>
                                    </div> 
                                </div>


                            </div>
                        </div>


                        <div class="row" style="text-align:center;">    
                            <br/> 
                            <a id="btnCncl" href="../main/user_edit.php?id=<?php echo $_GET["id"]; ?>" class="btn btn-danger btn_cancel"><span></span>Cancel</a>
                            <a id="btnSave"   href="#" class="btn btn-primary btn_save"><span></span>Save</a> 
                            <br/><br>
                            <input id="user_id" type="hidden" name="user_id" value="<?php echo @$this->account; ?>" placeholder="User Id" class="form_field_col1" maxlength="30" />
                        </div>
        <?php
        $serviceFileter = new RemoteareaUserMappingFilter();
        $serviceFileter->addFieldFilter('user_account', $this->account);
        $service_list = $serviceFileter->getColumnDistinctList(" distinct service_code, 
							(select name from services where TRIM(code) =  TRIM(service_code)) 'postcode_name', 
							(select remotearea from services where TRIM(code) =  TRIM(service_code)) 'user_account'
							  "); //, postcode_name, charges 
        ?>
                        <div class="row" > 
                            <div class="col-md-12">
                            <?php
                            if ($_GET['id'] != 'DEFAULT') {
                                ?>
                                    <div class="col-md-12 well" style="background-color: #bce8f1; text-align: center;">
                                        If remote area charges is not specified for this user then it will be charged on default rate. <a class="btn btn-primary btn_save" href="../main/list_remotearea_charges.php?id=DEFAULT">Click Here</a> 
                                    </div>
        <?php } ?>
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                        <tr >
                                            <th id="col1">Service Name</th>
                                        </tr>
                                    </thead>
                                </table><div class="row">
                                    <style>
                                        .form-control:hover{
                                            background:#eee;
                                            color:#000;
                                            cursor:pointer;
                                        }
                                    </style>
        <?php
        if (count($service_list) > 0) {
            foreach ($service_list as $service) {
                ?>
                                            <div class="col-sm-4">
                                                <div class="form-control  download-tariff-popup" title="View Remote Areas Details" data-action= "GET_REMOTEAREA_CHARGES_DETAIL" 
                                                     data-userid="<?php echo $_GET["id"]; ?>" 
                                                     data-service-name="<?php echo $service->getServiceCode(); ?>"
                                                     data-service-detail="<?php echo $service->getPostcodeName(); ?>"
                                                     data-remotetype="<?php echo $service->getUserAccount(); ?>">
                                                    <div  class="col-sm-10"><?php echo $service->getPostcodeName() . " (" . $service->getServiceCode() . ") "; ?></div>
                                                    <div  class="col-sm-2"><i class="fa fa-eye-slash" aria-hidden="true"></i></div>


                                                </div>
                                            </div>
                                                        <?php /* ?>                        <td><?php echo  $service->getPostcodeName() ." (".$service->getServiceCode().") " ; ?></td>
                                                          <td><?php //echo  $service->getPostcodeName() ; ?></td>
                                                          <td><?php //echo  $service->getCharges() ; ?></td>
                                                          <td>( <a href="../main/list_remotearea_charges.php?id=<?php echo $_GET["id"];?>&action=DELETE&record=<?php echo  $service->getId() ; ?>" title="DELETE ENTRY" onclick="return confirm('Are you sure, you want to delete ? ');">X</a> )</td>
                                                          </tr>
                                                          <?php */ ?>                    <?php
                                                    }
                                                }
                                                ?></div>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
            </div>
        </form>
        <form id="download_tariff_frm" name="download_tariff_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="download_tariff" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Remote Areas Datails</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="download_tariff_msg"></div>
                            <input type="hidden" name="action" id="action" value="DOWNLOAD_TARIFF" />                            
                            <div class="row">

                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Postcode Group Name for <span id="service_detail"></span></legend>
                                        <div class="row">
                                            <div class="col-md-12 download-remote-area">

                                            </div>
                                        </div>

                                    </fieldset>
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
        </form>
        <form id="update_remote_charges_frm" name="update_remote_charges_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="update_remote_charges" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Remote Areas Datails</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="update_remotearea_msg"></div>
                            <input type="hidden" name="action" id="action" value="UPDATE_REMOTECHARGES_BY_NAME" />                            
                            <div class="row">

                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Remote Area Charges</legend>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="remote_area_name" id="remote_area_name" value="" title="Postcode Name"  placeholder="Postcode Name" readonly="readonly" />      
                                                </div>    
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="remote_area_user" id="remote_area_user" value=""  placeholder="User" title="User" readonly="readonly"/>      
                                                </div>    
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="remote_area_service" id="remote_area_service" value=""  placeholder="Service code" title="Service code" readonly="readonly"/>      
                                                </div>    
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" name="remote_area_charges" id="remote_area_charges" value=""  placeholder="Postcode Charges" title="Postcode Charges"  />      
                                                </div>    
                                            </div>
                                        </div>


                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" id="saveCharges" >Save</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
            <form id="update_remote_weight_charges_frm" name="update_remote_weight_charges_frm" method="post">
                <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="update_remote_weight_charges" >
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Remote Areas Datails</h4>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-success" id="update_remotearea_weight_msg"></div>
                                <input type="hidden" name="action" id="action" value="UPDATE_REMOTECHARGES_BY_NAME" />                            
                                <div class="row">

                                    <div class="col-md-12">
                                        <fieldset class="fsStyle">
                                            <legend class="legendStyle">Remote Area Charges</legend>
                                            <div class="row" id="weight_data_display">
                                            </div>


                                        </fieldset>
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
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
