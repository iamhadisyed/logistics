<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Customer details page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
// set up local page class
class Page extends BasePage {

    private $id = 0;
    private $tracking_number = 0;
    private $consignment_status = 0;
    private $_error_array = array();
    private $customerName = "";
    private $active = "";
	private $servicename = "";
	private $carrier = "";

    /*     * *
     * This page's content
     * @return void
     */
    
  public function  makeHtmlAncillaryChargesDetails($oldData,$newData){

        $output	=	'';
			$output .= '<tr><td>Clearance</td><td>' . $oldData->clearance . '</td><td> to </td><td>' .  $newData->clearance . '</td></tr>';

			$output .= '<tr><td>Import Handing Charge</td><td>' . $oldData->import_handing_charge  . '</td><td> to </td><td>' .  $newData->import_handing_charge . '</td></tr>';

			$output .= '<tr><td>UK Local Truck</td><td>' . $oldData->uk_local_truck  . '</td><td> to </td><td>' .  $newData->uk_local_truck . '</td></tr>';

			$output .= '<tr><td>UK Opt</td><td>' . $oldData->uk_opt  . '</td><td> to </td><td>' .  $newData->uk_opt . '</td></tr>';

			$output .= '<tr><td>Truck to Destination</td><td>' . $oldData->truck_to_destination  . '</td><td> to </td><td>' .  $newData->truck_to_destination . '</td></tr>';
        return $output;	
    }
    public function makeHtml($oldData, $newData)
	{
		$output	=	'';
		if($oldData->getConsignmentId() != $newData->getConsignmentId())
			$output .= '<tr><td>Consignment Id</td><td>' . $oldData->getConsignmentId()  . '</td><td> to </td><td>' .  $newData->getConsignmentId() . '</td></tr>';
		if($oldData->getInvoiceNo() != $newData->getInvoiceNo())
			$output .= '<tr><td>Invoice No</td><td>' . $oldData->getInvoiceNo()  . '</td><td> to </td><td>' .  $newData->getInvoiceNo() . '</td></tr>';
		if($oldData->getHawb() != $newData->getHawb())
			$output .= '<tr><td>Hawb</td><td>' . $oldData->getHawb()  . '</td><td> to </td><td>' .  $newData->getHawb() . '</td></tr>';
		if($oldData->getBasicCharges() != $newData->getBasicCharges())
			$output .= '<tr><td>Basic Charges</td><td>' . $oldData->getBasicCharges()  . '</td><td> to </td><td>' .  $newData->getBasicCharges() . '</td></tr>';
		if($oldData->getFuelCharges() != $newData->getFuelCharges())
			$output .= '<tr><td>Fuel Charges</td><td>' . $oldData->getFuelCharges()  . '</td><td> to </td><td>' .  $newData->getFuelCharges() . '</td></tr>';
		if($oldData->getAdditionalCharges() != $newData->getAdditionalCharges())
			$output .= '<tr><td>Additional Charges</td><td>' . $oldData->getAdditionalCharges()  . '</td><td> to </td><td>' .  $newData->getAdditionalCharges() . '</td></tr>';
		if($oldData->getRemoteAreaCharge() != $newData->getRemoteAreaCharge())
			$output .= '<tr><td>Remote Area Charge</td><td>' . $oldData->getRemoteAreaCharge()  . '</td><td> to </td><td>' .  $newData->getRemoteAreaCharge() . '</td></tr>';
		if($oldData->getOnFarwordCharges() != $newData->getOnFarwordCharges())
			$output .= '<tr><td>On Farword Charges</td><td>' . $oldData->getOnFarwordCharges()  . '</td><td> to </td><td>' .  $newData->getOnFarwordCharges() . '</td></tr>';
		if($oldData->getNdx() != $newData->getNdx())
			$output .= '<tr><td>Ndx</td><td>' . $oldData->getNdx()  . '</td><td> to </td><td>' .  $newData->getNdx() . '</td></tr>';
		if($oldData->getDdp() != $newData->getDdp())
			$output .= '<tr><td>Ddp</td><td>' . $oldData->getDdp()  . '</td><td> to </td><td>' .  $newData->getDdp() . '</td></tr>';
		if($oldData->getExtra() != $newData->getExtra())
			$output .= '<tr><td>Extra</td><td>' . $oldData->getExtra()  . '</td><td> to </td><td>' .  $newData->getExtra() . '</td></tr>';
		if($oldData->getHv() != $newData->getHv())
			$output .= '<tr><td>Hv</td><td>' . $oldData->getHv()  . '</td><td> to </td><td>' .  $newData->getHv() . '</td></tr>';
		if($oldData->getDiscount() != $newData->getDiscount())
			$output .= '<tr><td>Discount</td><td>' . $oldData->getDiscount()  . '</td><td> to </td><td>' .  $newData->getDiscount() . '</td></tr>';
		if($oldData->getAncillaryCharges() != $newData->getAncillaryCharges())
			$output .= '<tr><td>Ancillary Charges</td><td>' . $oldData->getAncillaryCharges()  . '</td><td> to </td><td>' .  $newData->getAncillaryCharges() . '</td></tr>';
		if($oldData->getAncillaryChargesDetails() != $newData->getAncillaryChargesDetails())
			$output .= '<tr data-detail-id = "" style="cursor: pointer;" id="AncillaryChargesDetailsId" ><td>Ancillary Charges Details</td><td>&nbsp;</td><td>&nbsp;</td><td>Details</td></tr>';
		if($oldData->getAmount() != $newData->getAmount())
			$output .= '<tr><td>Amount</td><td>' . $oldData->getAmount()  . '</td><td> to </td><td>' .  $newData->getAmount() . '</td></tr>';
		if($oldData->getAgentBasicCharges() != $newData->getAgentBasicCharges())
			$output .= '<tr><td>Agent Basic Charges</td><td>' . $oldData->getAgentBasicCharges()  . '</td><td> to </td><td>' .  $newData->getAgentBasicCharges() . '</td></tr>';
		if($oldData->getAgentFuelCharges() != $newData->getAgentFuelCharges())
			$output .= '<tr><td>Agent Fuel Charges</td><td>' . $oldData->getAgentFuelCharges()  . '</td><td> to </td><td>' .  $newData->getAgentFuelCharges() . '</td></tr>';
		if($oldData->getAgentAdditionalCharges() != $newData->getAgentAdditionalCharges())
			$output .= '<tr><td>Agent Additional Charges</td><td>' . $oldData->getAgentAdditionalCharges()  . '</td><td> to </td><td>' .  $newData->getAgentAdditionalCharges() . '</td></tr>';
		if($oldData->getAgentRemoteAreaCharge() != $newData->getAgentRemoteAreaCharge())
			$output .= '<tr><td>Agent Remote Area Charge</td><td>' . $oldData->getAgentRemoteAreaCharge()  . '</td><td> to </td><td>' .  $newData->getAgentRemoteAreaCharge() . '</td></tr>';
		if($oldData->getAgentOnFarwordCharges() != $newData->getAgentOnFarwordCharges())
			$output .= '<tr><td>Agent On Farword Charges</td><td>' . $oldData->getAgentOnFarwordCharges()  . '</td><td> to </td><td>' .  $newData->getAgentOnFarwordCharges() . '</td></tr>';
		if($oldData->getAgentNdx() != $newData->getAgentNdx())
			$output .= '<tr><td>Agent Ndx</td><td>' . $oldData->getAgentNdx()  . '</td><td> to </td><td>' .  $newData->getAgentNdx() . '</td></tr>';
		if($oldData->getAgentDdp() != $newData->getAgentDdp())
			$output .= '<tr><td>Agent Ddp</td><td>' . $oldData->getAgentDdp()  . '</td><td> to </td><td>' .  $newData->getAgentDdp() . '</td></tr>';
		if($oldData->getAgentExtra() != $newData->getAgentExtra())
			$output .= '<tr><td>Agent Extra</td><td>' . $oldData->getAgentExtra()  . '</td><td> to </td><td>' .  $newData->getAgentExtra() . '</td></tr>';
		if($oldData->getAgentAmount() != $newData->getAgentAmount())
			$output .= '<tr><td>Agent Amount</td><td>' . $oldData->getAgentAmount()  . '</td><td> to </td><td>' .  $newData->getAgentAmount() . '</td></tr>';
		if($oldData->getAgentLinehaulCost() != $newData->getAgentLinehaulCost())
			$output .= '<tr><td>Agent Linehaul Cost</td><td>' . $oldData->getAgentLinehaulCost()  . '</td><td> to </td><td>' .  $newData->getAgentLinehaulCost() . '</td></tr>';
		if($oldData->getAgentHandlingCharges() != $newData->getAgentHandlingCharges())
			$output .= '<tr><td>Agent Handling Charges</td><td>' . $oldData->getAgentHandlingCharges()  . '</td><td> to </td><td>' .  $newData->getAgentHandlingCharges() . '</td></tr>';
		if($oldData->getReference() != $newData->getReference())
			$output .= '<tr><td>Reference</td><td>' . $oldData->getReference()  . '</td><td> to </td><td>' .  $newData->getReference() . '</td></tr>';
		if($oldData->getQuotationId() != $newData->getQuotationId())
			$output .= '<tr><td>QuotationId</td><td>' . $oldData->getQuotationId()  . '</td><td> to </td><td>' .  $newData->getQuotationId() . '</td></tr>';
		if($oldData->getDateCreated() != $newData->getDateCreated())
			$output .= '<tr><td>Date Created</td><td>' . $oldData->getDateCreated()  . '</td><td> to </td><td>' .  $newData->getDateCreated() . '</td></tr>';
		if($oldData->getAddedBy() != $newData->getAddedBy())
			$output .= '<tr><td>Added By</td><td>' . $oldData->getAddedBy()  . '</td><td> to </td><td>' .  $newData->getAddedBy() . '</td></tr>';
		if($oldData->getTariffName() != $newData->getTariffName())
			$output .= '<tr><td>Tariff Name</td><td>' . $oldData->getTariffName()  . '</td><td> to </td><td>' .  $newData->getTariffName() . '</td></tr>';
	return $output;	
	}
        
        
private function userDropdown() {
       
        $filter = new UserAccountFilter();
        $filter->AddOrderByAccount();
        $filter->addActiveFlagFilter();
        $userList = $filter->getColumnList("id, user_account");
       
        echo '<select id="new_account" name="new_account"  class="form-control"  rel="tooltip" title="User Account">';
        echo '<option value="">Select Account</option>';
        foreach ($userList as $userData) {
            $selected = ($new_account == $userData->getUserAccount()) ? " selected" : "";
            echo '<option' . $selected . ' value="' . $userData->getUserAccount() . '">' . $userData->getUserAccount() . '</option>';
        }

        echo '</select>';
    }

    public function renderHead() {
        ?>

        <style>
            legend a {
                color: inherit;
            }
            legend.legendStyle {
                padding-left: 5px;
                padding-right: 5px;
            }
            fieldset.fsStyle {
                font-size: small;
                font-weight: normal;
                border: 1px solid #000065;
                padding: 4px;
                margin-bottom: 10px
            }
            legend.legendStyle {
                font-size: 90%;
                color: #888888;
                background-color: transparent;
                font-weight: bold;
            }
            legend {
                width: auto;
                border-bottom: 0px;
                margin-bottom: 0
            }
          
            .fsStyle .table-scrollable {
                border: none !important;
                margin: 0 !important
            }
            table {
                font-size: 12px
            }
            .table .btn {
                font-size: 12px;
                padding: 3px;
            }
            .form-control {
                font-size: 12px;
                height: 28px;
                padding: 2px 12px;
            }
        </style>
        <script src="../_assets/admin/pages/scripts/portlet-draggable.js"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
        <script >
            //Weight Discrepancy Button
            function weightDiscrepancy(weight, id)
            {
                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php",
                        {action: 'WEIGHT_DISCREPANCY', weight: weight, id: id},
                function(data)
                {
                    $('#loadcontent').html(data);
                });
            }
			function EditValueType()
			{
				document.getElementById('highlowvalueedit').style.display = '';
				document.getElementById('highlowvalueshow').style.display = 'none';
				
			}
			function SaveHighValue(id)
			{
				var highlowvalue = document.getElementById('highlowvalue').value;
				  $.post(
                        "ajaxbooking.php",
                        {action: 'SAVEHIGHVALUE', highlowvalue: highlowvalue, id: id},
                function(data)
                {
					if(data == "Success")
					{
						var vtype = "";
						if(highlowvalue == 'HV')
							vtype = 'High';
						else if(highlowvalue == 'MV')
							vtype = 'Medium';
						else
							vtype = 'Low';
						document.getElementById('valueType').innerHTML = vtype;
						document.getElementById('highlowvalueedit').style.display = 'none';
						document.getElementById('highlowvalueshow').style.display = '';
					}
                    
                });
			}
			function SaveExportCustome(id)
			{
				var custom_export_number = document.getElementById("custom_export_number").value;
				 $.post(
                        "ajaxbooking.php",
                        {action: 'CUSTOM_EXPORT', custom_export_number: custom_export_number, id: id},
                function(data)
                {
					alert(data);
                });
			}
            //get Difference between custom weight and original weight
            function differenceweight(weight)
            {
                var cweight = document.getElementById("customerweight").value;
                var differenceWt = cweight - weight;
                document.getElementById("diffweight").value = differenceWt;
            }
            //if there is difference in weight than save new weight 
            function saveWeight(id, weight)
            {
                $('#loadcontent').show();
                var cweight = document.getElementById("customerweight").value;
                $.post(
                        "ajaxbooking.php",
                        {action: 'SAVE_WEIGHT', cweight: cweight, weight: weight, id: id},
                function(data)
                {
                    document.getElementById("weight-update-section").innerHTML = cweight;
                    alert(data);
                    pricingUpdateCalculation(id);
                });
            }


            function dispalyDetailMessage(logId, messageFor)
            {
                $.post(
                        "ajaxbooking.php",
                        {action: 'FETCH_LOG_MESSAGE', logId: logId, messageFor: messageFor},
                function(data)
                {
                    objLogData = JSON.parse(data);
                    if (objLogData.length > 0)
                    {
                        var id = objLogData[0];
                        var internalmessage = objLogData[1];
                        var customermessage = objLogData[2];
						var reminderdate 	= objLogData[3];
                        $('#internallog').val(internalmessage).attr('readonly', 'readonly');
                        $('#customerlog').val(customermessage).attr('readonly', 'readonly');
						$('#followup_meeting_date').val(reminderdate).attr('readonly', 'readonly');
                        $('#forwardEmail').show();
                    }
                });
            }


            //Load Email Window
            function forwardLogEmail(id)
            {
                var id = id;
                var internalmessage = $('#internallog').val();
                var customermessage = $('#customerlog').val();

                $.post(
                        "ajaxbooking.php", {action: 'SEND_FORWARDEMAIL', id: id, internalmessage: internalmessage, customermessage: customermessage},
                function(data)
                {
                    $('#loadcontent').html(data);
					
                });

            }
            //volume calculation window.
            function volumecalculation(id, isInvoiced)
            {
                $('#loadcontent').show();
                var volbase = document.getElementById("volbase").value;
                $.post(
                        "ajaxbooking.php",
                        {
                            action: 'VOLUME_DISPLAY',
                            id: id,
                            isInvoiced: isInvoiced,
                            volbase: volbase
                        },
                function(data)
                {
                    $('#loadcontent').html(data);
                });
            }
            //calculate volumatric weight based on dimensions
            function calcuVolWeight(i)
            {
                var pcs = document.getElementById("pcs" + i).value;
                var ptracking = document.getElementById("ptracking" + i).value;
                var length = document.getElementById("length" + i).value;
                var width = document.getElementById("width" + i).value;
                var height = document.getElementById("height" + i).value;
                var volbase = document.getElementById("volbase").value;
                var volweight = (pcs * length * width * height) / volbase;

                document.getElementById("kg" + i).value = volweight;
            }

            //get total of all volumetric weight
            function totalVolWeight(i)
            {
                var totalWeight = "";
                if (i == 0)
                {
                    totalWeight = 0;
                }
                else
                {
                    totalWeight = document.getElementById("totalvolweight").value;
                }
                if (totalWeight == "")
                {
                    totalWeight = 0;
                }
                var volweight = document.getElementById("kg" + i).value;
                var weight = parseFloat(volweight) + parseFloat(totalWeight);
                document.getElementById("totalvolweight").value = weight;
            }
            //Send Weight and Volume Email Popup
            function sendWeightVolumeEmail(id, oldweight, newweight)
            {
                alert(id);
                alert(oldweight);
                alert(newweight);

            }

            function changeBillingHoldStatus(id, billingOnholdValue)
            {
                $.post(
                        "booking_view.php",
                        {action: 'SAVE_BILLING_HOLD_STATUS', id: id, billing_hold: billingOnholdValue},
                function(data)
                {
                    alert('You have successfully saved the billing status.');
                });
            }
            //save the volumetric weight
            function saveVolume(id, numberPieces)
            {
                var totalpieces = "";
                var totallength = "";
                var totalwidth = "";
                var totalheight = "";
                for (i = 0; i < numberPieces; i++)
                {
                    var pcs = document.getElementById("pcs" + i).value;
                    var length = document.getElementById("length" + i).value;
                    var width = document.getElementById("width" + i).value;
                    var height = document.getElementById("height" + i).value;
                    var totalWeight = document.getElementById("totalvolweight").value;
                    var ptracking = document.getElementById("ptracking" + i).value;

                    var volbase = document.getElementById("volbase").value;
                    var volweight = (pcs * length * width * height) / volbase;

                    totalpieces += String(pcs) + "||";
                    totallength += String(length) + "||";
                    totalwidth += String(width) + "||";
                    totalheight += String(height) + "||";
                    //var totalvolweight = volweight + "||";

                }

                if (totalpieces != "" && totallength != "" && totalwidth != "" && totalheight != "")
                {
                    $.post(
                            "ajaxbooking.php",
                            {action: 'SAVE_VOLUME', id: id, pcs: totalpieces, ptracking: ptracking, length: totallength, width: totalwidth, height: totalheight, totalWeight: totalWeight, volweight: volweight},
                    function(data)
                    {
                        document.getElementById("updateweight").value = totalWeight;
                        
						//$('#loadcontent').hide();


                        pricingUpdateCalculation(id);
                    });
                }
            }
            //Give contact details of agent and customer
            function contactdetails(agentid, usertelephone)
            {
                if (agentid != "")
                {
                    $('#loadcontent').show();
                    $.post(
                            "ajaxbooking.php",
                            {action: 'CUSTOMER_DISPLAY', agentid: agentid, usertelephone: usertelephone},
                    function(data)
                    {
                        $('#loadcontent').html(data);
                    });
                }
            }
            //Cancel Button 
            function Cancelbutton()
            {
                $('#loadcontent').hide();
            }
            //Update POD Status of Shipments
            function updateStatus(id)
            {

                var status = document.getElementById("status").value;
                $.post(
                        "ajaxbooking.php",
                        {action: 'UPDATE_STATUS', id: id, status: status},
                function(data)
                {
                    alert(data);
                });
            }
            //Load Log Window
            function displayLog(id)
            {
                $('#loadcontent').show();
				
                $.post(
                        "ajaxbooking.php",
                        {action: 'UPDATE_LOG', id: id},
                function(data)
                {
                    $('#loadcontent').html(data);

                });
            }
            //Save log to the database
            function AddLogbutton(id)
            {
                $('#loadcontent').show();
                var internallog = document.getElementById("internallog").value;
                var customerlog = document.getElementById("customerlog").value;
				var reminder = "";
				
                $.post(
                        "ajaxbooking.php",
                        {action: 'ADD_LOG', id: id, internallog: internallog, customerlog: customerlog, reminder: reminder},
                function(data)
                {
                    alert(data);
                    $('#loadcontent').show();
					displayLog(id);
                });
            }




            //Send email to customer and agent from Log
            function SendLogFarwordEmail(id)
            {
                var cmail = document.getElementById("cemail").checked;
                var amail = document.getElementById("aemail").checked;
				
                if (cmail == true)
                    cmail = "customer";
                if (amail == true)
                    amail = "agent";
                var cmailcc = document.getElementById("cmailcc").value;
                var amailcc = document.getElementById("amailcc").value;
                var message = document.getElementById("emailmessage").value;

                $.post(
                        "ajaxbooking.php", {action: 'SEND_FARWORD_EMAIL', id: id, cmail: cmail, amail: amail, cmailcc: cmailcc, amailcc: amailcc, message: message},
                function(data)
                {
                    alert(data);
                });

            }

            function updateConsignmentRemote()
            {
                var conId = $("#consignment_id_remote").val();
                var remote_area_value = $("#remote_area_value").val();

                if ($("#remote_area_value").is(":checked") == true)
                    var remotearea = 'YES';
                else
                    var remotearea = 'NO';
                var action = "UPDATE_REMOTE";

                $.post("booking_view.php", {action: action, id: conId, remotearea: remotearea}, function(data) {
                    if ($.type(data) === 'object') {

                        if (data.STATUS == "SUCCESS") {
                            $('#remote-area-div').html(remotearea);
                            if ($("#update_remote_msg").hasClass("alert-danger"))
                                $("#update_remote_msg").removeClass("alert-danger");                            
                            $("#update_remote_msg").addClass("alert-success").html(data.MESSAGE).removeClass("hidden");

                        } else {
                            if ($("#update_remote_msg").hasClass("alert-success"))
                                $("#update_remote_msg").removeClass("alert-success");
                            $("#update_remote_msg").addClass("alert-danger").html(data.MESSAGE).removeClass("hidden");
                        }
                    }

                }, "json");


            }
            function updateConsignmentAgent()
            {
                var conId = $("#consignment_id_agent").val();
                var agent_area_value = document.getElementById('agent_area_id').value;


                var action = "UPDATE_AGENT";

                $.post("booking_view.php", {action: action, id: conId, agentcode: agent_area_value}, function(data) {
                    if ($.type(data) === 'object') {

                        if (data.STATUS == "SUCCESS") {

                            if ($("#update_agent_msg").hasClass("alert-danger"))
                                $("#update_agent_msg").removeClass("alert-danger");
                            $("#update_agent_msg").addClass("alert-success").html("Your information has been successfully changed...").removeClass("hidden");

                        } else {
                            if ($("#update_agent_msg").hasClass("alert-success"))
                                $("#update_agent_msg").removeClass("alert-success");
                            $("#update_agent_msg").addClass("alert-danger").html("Please enter account code to change...").removeClass("hidden");
                        }
                    }

                }, "json");


            }
            function updateConsignmentTracking()
            {
                var conId = $("#consignment_id_tracking").val();
                var tracking_new = document.getElementById('tracking_new').value;
                var action = "UPDATE_TRACKING";
                $.post("booking_view.php", {action: action, id: conId, tracking_new: tracking_new}, function(data) {
                    if ($.type(data) === 'object') {

                        if (data.STATUS == "SUCCESS") {

                            if ($("#update_tracking_msg").hasClass("alert-danger"))
                                $("#update_tracking_msg").removeClass("alert-danger");
                            $("#update_tracking_msg").addClass("alert-success").html("Your information has been successfully changed...").removeClass("hidden");

                        } else {
                            if ($("#update_tracking_msg").hasClass("alert-success"))
                                $("#update_tracking_msg").removeClass("alert-success");
                            $("#update_tracking_msg").addClass("alert-danger").html("Please enter account code to change...").removeClass("hidden");
                        }
                    }

                }, "json");


            }


            function updateConsignmentHawb()
            {
                var conId = $("#consignment_id_hawb").val();
                var hawb_new = document.getElementById('hawb_new').value;


                var action = "UPDATE_HAWB";

                $.post("booking_view.php", {action: action, id: conId, hawb_new: hawb_new}, function(data) {
                    if ($.type(data) === 'object') {

                        if (data.STATUS == "SUCCESS") {

                            if ($("#update_hawb_msg").hasClass("alert-danger"))
                                $("#update_hawb_msg").removeClass("alert-danger");
                            $("#update_hawb_msg").addClass("alert-success").html("Your information has been successfully changed...").removeClass("hidden");

                        } else {
                            if ($("#update_hawb_msg").hasClass("alert-success"))
                                $("#update_hawb_msg").removeClass("alert-success");
                            $("#update_hawb_msg").addClass("alert-danger").html("Please enter account code to change...").removeClass("hidden");
                        }
                    }

                }, "json");


            }

            function updateConsignmentAwb()
            {
                var conId = $("#consignment_id_awb").val();
                var awb_new = document.getElementById('awb_new').value;


                var action = "UPDATE_AWB";

                $.post("booking_view.php", {action: action, id: conId, awb_new: awb_new}, function(data) {
                    if ($.type(data) === 'object') {

                        if (data.STATUS == "SUCCESS") {

                            if ($("#update_awb_msg").hasClass("alert-danger"))
                                $("#update_awb_msg").removeClass("alert-danger");
                            $("#update_awb_msg").addClass("alert-success").html("Your information has been successfully changed...").removeClass("hidden");

                        } else {
                            if ($("#update_awb_msg").hasClass("alert-success"))
                                $("#update_awb_msg").removeClass("alert-success");
                            $("#update_awb_msg").addClass("alert-danger").html("Please enter account code to change...").removeClass("hidden");
                        }
                    }

                }, "json");


            }
            function updateConsignmentAccount()
            {
                var conId = $("#consignment_id_account").val();
                var oldAccount = $("#old_account").val();
                var newAccount = $("#new_account").val();
                var hawbAccount = $("#hawb_account").val();
                var action = "UPDATE_UPDATE_ACCOUNT";

                if (newAccount == '')
                {
                    if ($("#update_change_account_msg").hasClass("alert-success"))
                        $("#update_change_account_msg").removeClass("alert-success");
                    $("#update_change_account_msg").addClass("alert-danger").html("Please enter account code to change...").removeClass("hidden");

                    return false;
                }



                $.post("booking_view.php", {action: action, id: conId, oldAccount: oldAccount, newAccount: newAccount}, function(data) {
                    if ($.type(data) === 'object') {

                        if (data.STATUS == "SUCCESS") {
                            if ($("#update_change_account_msg").hasClass("alert-danger"))
                                $("#update_change_account_msg").removeClass("alert-danger");
                            $("#update_change_account_msg").addClass("alert-success").html("Your account has been successfully changed...").removeClass("hidden");

                        } else {
                            if ($("#update_change_account_msg").hasClass("alert-success"))
                                $("#update_change_account_msg").removeClass("alert-success");
                            $("#update_change_account_msg").addClass("alert-danger").html("Please enter account code to change...").removeClass("hidden");
                        }
                    }

                }, "json");


            }
            function updateInvoiceTotal() {
                var customer_total = 0;
                var agent_total = 0;
                var margin = 0;
                var discount = 0;
                $(".customer_charges").each(function() {
                    var val = $.trim($(this).val());
                    val = (val == '' ? 0 : val);
                    customer_total += parseFloat(val);
                });
                discount = $.trim($("#pricing-invoice #discount").val());
                if (discount != '' && !isNaN(discount)) {
                    customer_total -= parseFloat(discount);
                } else {
                    $("#pricing-invoice #discount").val('');
                }
                $(".agent_charges").each(function() {
                    if ($(this).attr('name') != 'reference') {
                        var val = $.trim($(this).val());
                        val = (val == '' ? 0 : val);
                        agent_total += parseFloat(val);
                    }
                });
                margin = parseFloat(customer_total - agent_total);
                $("#total").val(customer_total);
                $("#agent_total").val(agent_total);
                $("#margin").val(margin);
            }
            function loadExtras(invoice_detail_id) {
                if (invoice_detail_id != '') {
                    $.post('ajaxbooking.php', {action: 'get_extras', invoice_detail_id: invoice_detail_id}, function(data) {
                        $("#customer_extras_body").html(data.customerData);
                        $("#agent_extras_body").html(data.agentData);
                        $("#customer_totals_extras").val(data.customerCount);
                        $("#agent_totals_extras").val(data.agentCount);
                        $("#extra").val(data.customerTotalExtras);
                        // $("#agent_extra").val(data.agentTotalExtras);
                        updateInvoiceTotal();
                    }, "json");
                } else {
                    updateInvoiceTotal();
                }
            }

            function pricingUpdateCalculation(consignmentid) {

                $("#pricing_details_msg").hide();
                $("#update_pricing_details_frm").find("input[type=text],input[type=hidden]").val("");
                $("#update_pricing_details_frm").find("input[name=action]").val("UPDATE_PRICING_DETAILS");
                //$("#update_pricing_details_btn").prop('disabled', true);
                $("#update_pricing_details_frm").find("input[type=text]").prop("disabled", true);
               
			   
			    $.post("pricing-data-cron.php", {request: 'ajax', action: 'UPDATE_PRICING_DETAILS', id: consignmentid}, function(data) {

                   
                        var obj = data;

                        if (obj[0].STATUS == 'ERROR')
                        {
                            alert(obj[0].MESSAGE)
                        }
                        else
                        {
                            alert(obj[0].MESSAGE)
                        }

                    

                }, "json");


            }
			
			function ShowLabel(labelLink)
			{
					
				if(labelLink != '')
				{
					var label = labelLink.replace("../","");
					window.open("<?= SETTING_MAIN_URL; ?>" + label);
				}
				else
				{
					alert("Label does not exits.")
				}
			}

            function pricingCalculation(consignmentid) {
                $("#pricing_details_msg").hide();
                $("#update_pricing_details_frm").find("input[type=text],input[type=hidden]").val("");
                $("#update_pricing_details_frm").find("input[name=action]").val("UPDATE_PRICING_DETAILS");
                //$("#update_pricing_details_btn").prop('disabled', true);
                $("#update_pricing_details_frm").find("input[type=text]").prop("disabled", true);
                $.post("ajaxbooking.php", {action: 'PRICING_DETAILS', id: consignmentid}, function(data) {

                    if ($.type(data) === 'object') {
                        $.each(data, function(key, value) {
                            $("#pricing-invoice #" + key).val(value);
							if(key=='invoice_no')
								$("#invoice_no_show").html(value);
                        });
						var		dataancellarycharges	=	'<table  class="table table-striped table-bordered table-advance table-hover"><tbody>';
						var 	ancellaryChae	=	data.ancillary_charges_details
						
						var dataancellarychargesTmp = '';
						if(data.ancillary_charges_details != null)
						{
							$.each(data.ancillary_charges_details, function(key, value) {
								var nm = key.split('_').join('&nbsp;');
								dataancellarychargesTmp	+=	'<tr><td>'+nm+'</td><td>'+value+'</td></tr>';	
							});
						}
						dataancellarycharges += dataancellarychargesTmp;
						if(dataancellarychargesTmp == '')
						{
							dataancellarycharges	+=	'<tr><td>No Data Found</td></tr>';	
						}
						dataancellarycharges	+=	'</tbody></table>';
                        if (data.invoice_no == "") {
                            $("#update_pricing_details_frm").find("input[type=text]").prop("disabled", false);
                            $("#update_pricing_details_btn").prop('disabled', false);
							
							 $("#ancillary-charges").popover({content: dataancellarycharges, placement: 'left', html: true}).popover();
							 
                        } else {
                            $("#update_pricing_details_frm").find("input[type=text].agent_charges").prop("disabled", false);
                        }
                    }
                    $("#update_pricing_details_frm").find("#consignment_id").val('<?php echo $this->id; ?>');
                    //updateInvoiceTotal();
                    loadExtras(data.invoice_detail_id);
                }, "json");

            }
            function updatePricingDetails() {
                $("#update_pricing_details_btn").prop('disabled','disabled');
				$("#update_pricing_details_btn").off('click');
				var form_data = $("#update_pricing_details_frm").serialize();
				
                if ($("#pricing_details_msg").hasClass("alert-success"))
                    $("#pricing_details_msg").removeClass("alert-success");
                $("#pricing_details_msg").addClass("alert-info");
                $("#pricing_details_msg").html("please wait we are saving...");
                $("#pricing_details_msg").show();
                $.post("ajaxbooking.php", form_data, function(data) {
                    if ($("#pricing_details_msg").hasClass("alert-info"))
                        $("#pricing_details_msg").removeClass("alert-info");
                    $("#pricing_details_msg").addClass("alert-success");
                    $("#pricing_details_msg").html("Updated successfully.");
                    $("#pricing_details_msg").show();
				});
            }

            //LOAD AUDIT
            function ShowAudit(consignment_id)
            {
                $.post('booking_view.php', {func: 'get_audit_log', consignment_id: consignment_id}, function(data) {
                    $("#audit_content").html(data);
                });
            }
            //Send email to customer and agent from Log
            function SendLogEmail(email, user, count)
            {
                var sendemail = email;

                if (user == "customer")
                {
                    var customermail = "";
                    var clogid = "";
                    for (i = 0; i < count; i++) {
                        customermail = document.getElementById("csend" + i).checked;
                        if (customermail == true)
                        {
                            if (clogid == "")
                                clogid = document.getElementById("csend" + i).value;
                            else
                                clogid = clogid + "," + document.getElementById("csend" + i).value;
                        }
                    }
                }
                else
                {
                    var agentmail = "";
                    var clogid = "";
                    for (i = 0; i < count; i++) {
                        agentmail = document.getElementById("asend" + i).checked;
                        if (agentmail == true)
                        {
                            if (clogid == "")
                                clogid = document.getElementById("asend" + i).value;
                            else
                                clogid = clogid + "," + document.getElementById("asend" + i).value;
                        }
                    }
                }
                $.post(
                        "ajaxbooking.php",
                        {action: 'SEND_EMAIL', email: sendemail, user: user, clogid: clogid},
                function(data)
                {
                    alert(data);
                });

            }
            //Show History of Updated POD Status
            function showHistory(id)
            {

                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php", {action: 'HISTORY_STATUS', id: id},
                function(data)
                {
                    $('#loadcontent').html(data);
                });

            }
            //Load Email Window
            function SendEmail(id)
            {
                $('#loadcontent').show();
				$('#followup_meeting_date').datetimepicker({dateFormat: 'yyyy-mm-dd hh:ii', use24hours: true,autoclose: true});
				
                $.post(
                        "ajaxbooking.php", {action: 'SEND_PODEMAIL', id: id},
                function(data)
                {
                    $('#loadcontent').html(data);
					$('#loadcontent').find("#followup_meeting_date").datetimepicker({showOn: 'click', dateFormat: 'yyyy-mm-dd hh:ii', use24hours: true, autoclose: true});
                });

            }
            //Send Email to Customer or Agent
            function SendPodEmail(id)
            {
                var cmail = document.getElementById("cemail").checked;
                var amail = document.getElementById("aemail").checked;

                if (cmail == true)
                    cmail = "customer";
                if (amail == true)
                    amail = "agent";
                var cmailcc = document.getElementById("cmailcc").value;
                var amailcc = document.getElementById("amailcc").value;
                var message = document.getElementById("emailmessage").value;
				var reminder = document.getElementById("followup_meeting_date").value;

                $.post(
                        "ajaxbooking.php", {action: 'SENDEMAILPOD', id: id, cmail: cmail, amail: amail, cmailcc: cmailcc, amailcc: amailcc, message: message, reminder: reminder},
                function(data)
                {
                    alert(data);
                });
            }
            //Resend Email to Customer if There is discrepancy in Weight
            function resendWeightEmail(id, oldweight, newweight)
            {
                if (oldweight == "")
                    oldweight = 0;
                var differenceWt = newweight - oldweight;
                if (differenceWt == 0)
                {
                    alert("There is no discrepancy in Weight!!!");
                }
                else
                {
                    $('#loadcontent').show();
                    $.post(
                            "ajaxbooking.php", {action: 'ResendWeightEmail', id: id},
                    function(data)
                    {
                        alert(data);
                    });
                }
            }
            //Resend Email to Customer if there is discrepancy in weight due to volume
            function resendVolumeEmail(id)
            {
                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php", {action: 'ResendVolumeEmail', id: id},
                function(data)
                {
                    alert(data);
                });
            }

            //Show quotation
            function showQuotation(id)
            {
                var quotationid = document.getElementById("quotationreference").value;
                $('#loadcontent').show();
                if (quotationid == '')
                {
                    alert('Please enter Quotation Reference.');
                    return false;
                }

                $.post(
                        "ajaxbooking.php", {action: 'ShowQuotation', quotationid: quotationid, id: id},
                function(data)
                {
                    //alert(data);
                    $('#loadcontent').html(data);
                });
            }

            //Assign Quotation 
            function assignQuotation(id, quotationid)
            {

                $('#loadcontent').show();
                $.post(
                        "ajaxbooking.php", {action: 'AssignQuotation', id: id, quotationid: quotationid},
                function(data)
                {
                    var message = data.split("||");
                    if (message[0] == "ERROR")
                    {
                        alert(data);
                    }
                    else
                    {
                        document.getElementById("quotationreference").value = message[1];
                        alert("Quotation Added.");
                    }
                    $('#loadcontent').hide();


                });
            }
            function getCustomerDetais(accountDetail)
            {
                var accountDetail = accountDetail;

                $.ajax({
                    url: "booking_view.php",
                    type: "POST",
                    async: false,
                    data: {
                        action: 'GET_USER_DETAILS',
                        account: accountDetail,
                    },
                    success: function(data) {
                        var obj = jQuery.parseJSON(data);//$('#service').html('<option value="">Select Service</option>' + data);
                        //alert(obj.fullname);
                        $("#ad-account").html(obj.useraccount);
                        $("#ad-fullname").html(obj.fullname);
                        $("#ad-company").html(obj.company);
                        $("#ad-returnaddress").html(obj.returnaddress);
                        $("#ad-email").html(obj.email);

                        $("#ad-telephone").html(obj.telephone);
                        $("#ad-billingaddress").html(obj.billingaddress);
                        $("#ad-alternativeemail").html(obj.alternativeemail);
                        $("#ad-country").html(obj.country);



                        /*								$.each(obj, function( index, value ) {
                         optionsAccount += "<option value='"+index+"'>"+value+"</option>";
                         });
                         $("#service").html(optionsAccount);*/
                    }
                });
            }

			$(document).ready(function (){
				
				var trackingNumber	=	$( "#duplicate-shipment-section" ).data( "tracking" );
				var action	=	$( "#duplicate-shipment-section" ).data( "action" );

				$.ajax({
                    url: 'booking_view.php',
                    data: {action:action, tracking:trackingNumber },
                    type: 'post',
                    success: function (response) {
                        
						if($.trim(response)!='')
						{
							$( "#duplicate-shipment-section").removeClass('hide');
							$( "#duplicate-tracking" ).html(response);
						}
                    }
                });
				
				});

        </script>
        <?php
    }

    public function renderBody() {

//$consignment = new Consignment($this->id);

        $consignment = new ConsignmentFilter();
        $consignment->addFieldFilter('id', $this->id);
        $consignmentFilter = $consignment->getList();
        $consignment = $consignmentFilter[0];



        $user = new UserAccountFilter();
        $consignment->getAccount();
        $user->addAccountNumberFilter($consignment->getAccount());
        $userlist = $user->getColumnList('user_name,user_account,company,full_name,return_address,email,country,telephone');
        if (count($userlist) > 0)
            $userlist = $userlist[0];

        $invoiceDetails = new InvoiceDetailFilter();
        $invoiceDetails->addFieldFilter('consignment_id', $this->id);
        $invoice_list = $invoiceDetails->getColumnList('quotation_id');
        $quotationid = "";
        if (count($invoice_list) > 0) {
            $invoice_list = $invoice_list[0];
            $quotationid = $invoice_list->getQuotationId();
        }
        ?>

        <div class="row" id="sortable_portlets">
            <div class="col-md-8  sortable">
                <div class="portlet box blue portlet-sortable">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gift"></i>Shipment Details
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse">
                            </a>
                            <a href="#portlet-config" data-toggle="modal" class="config">
                            </a>
                            <a href="" class="fullscreen">
                            </a>

                        </div>
                    </div>
                    <div class="portlet-body">

                        <fieldset class="fsStyle">
                            <legend class="legendStyle"> <a data-toggle="collapse" data-target="#general" href="#">Details</a> </legend>
                            <div class="row collapse in" id="general">
                                <div class="col-md-12">
                                    <div class="table-scrollable">
                                        <div id="fld_times_details">
                                            <table class="table table-bordered table-hover table-condensed ">
                                                <tr>
                                                    <td class="success"><?php
                                                        if ($consignment->getDateBooked() != '1970-01-01' && $consignment->getDateBooked() != '' && $consignment->getDateBooked() != NULL)
                                                            echo 'Date Dispatched :';
                                                        else
                                                            echo 'Date Label Created:';
                                                        ?></td>
                                                    <td class="success"><?php
                                                        if ($consignment->getDateBooked() != '1970-01-01' && $consignment->getDateBooked() != '' && $consignment->getDateBooked() != NULL)
                                                            echo $consignment->getDateBooked();
                                                        else
                                                            echo ($consignment->getDateSubmitted());
                                                        ?></td>
                                                    <td class="danger">Order No (HAWB):</td>
                                                    <td class="danger"><?php echo $consignment->getHawb(); ?>  <?php /* ?><a href="#" title="Hawb Edit" data-target="#hawb-edit" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" title="Hawb Edit"></span></a><?php */ ?></td>
                                                    <td class="info">Invoice No:</td>
                                                    <td class="info"><?php echo ($consignment->getInvoiceId() > 0) ? $consignment->getInvoiceId() : ''; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Service:</td>
                                                    <td><?php 
													if($consignment->getType() != '' && $consignment->getType() != 'D' && $consignment->getType() != 'C')
													{
														$ServiceFilter = new ServiceFilter();
														$ServiceFilter->addFieldFilter("code", $consignment->getHandling());
														$serviceList = $ServiceFilter->getColumnList("name, carrier");
														if(count($serviceList) > 0 )
														{
															$this->servicename = $serviceList[0]->getName();
															$this->carrier = $serviceList[0]->getCarrier();
															echo  $this->servicename ; 
														}
														else
														{
															echo $consignment->getServiceType() ;
														}
													}
													else
													{
														echo $consignment->getServiceType() . ' ( ' . $consignment->getHandling() . ' )'; 
                                                    }?>
                                                    </td>
                                                    <td> Claim Submitted: </td>
                                                    <td><select id="claim" class="form-control">
                                                            <option value = "No">No</option>
                                                            <option value = "Yes">Yes</option>
                                                        </select></td>
                                                    <td>Type:</td>
                                                    <td><?php
                                                        If ($consignment->getType() == '' || $consignment->getType() == 'D') {
                                                            echo 'Dispatch';
                                                        } else If ($consignment->getType() == 'C') {
                                                            echo 'Collection';
                                                        } else {
															if($this->servicename != "")
																echo $this->servicename;
															else
                                                            	echo $consignment->getType() . ' ( ' . $consignment->getHandling() . ' )';
                                                        }
                                                        ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row">
                          <?php 
							if ($consignment->getType() == 'C')
							{
								?>
                                <div class="col-sm-12">
                                <fieldset class="fsStyle" >
                                    <legend class="legendStyle"> <a data-toggle="collapse" data-target="#general2" href="#">Collection Details</a> </legend>
                                    <div class="row collapse in" id="general2">
                                        <div class="col-md-12">
                                            <div class="table-scrollable">
                                                <div id="fld_times_details">
                                                    <table class="table table-bordered table-hover table-condensed ">
                                                        <tr>
                                                            <td>Contact Name</td>
                                                            <td><?php
                                                               
                                                                    echo $consignment->getSenderContact();
                                                                ?></td>
                                                            <td>Company Name</td>
                                                            <td><?php
                                                               
                                                                    echo html_entity_decode($consignment->getSenderCompany());
                                                                ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address: </td>
                                                            <td><?php
                                                                    echo html_entity_decode($consignment->getSenderAddressLine1()) . ' ' . html_entity_decode($consignment->getSenderAddressLine2()) . ' ' . html_entity_decode($consignment->getSenderAddressLine3());
                                                                ?></td>
                                                            <td>City:</td>
                                                            <td><?php
                                                                    echo html_entity_decode($consignment->getSenderCity());
                                                                ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td> Postcode: </td>
                                                            <td><?php
                                                                        echo $consignment->getSenderPostCode();
                                                                    ?>
</td>
                                                            <td>Country:</td>
                                                            <td><?php
                                                                    echo html_entity_decode($consignment->getSenderCountry());
                                                                ?></td>
                                                        </tr>                    
                                                        <tr>
                                                            <td> Telephone: </td>
                                                            <td><?php
                                                                    echo $consignment->getSenderTelephone();
                                                                ?></td>

                                                        </tr>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
								<?php
							}
						?>
                        
                            <div class="col-sm-6">
                                <fieldset class="fsStyle" style="height:300px;">
                                    <legend class="legendStyle" > <a data-toggle="collapse" data-target="#general2" href="#">Account Details</a> </legend>
                                    <div class="row collapse in" id="general2">
                                        <div class="col-md-12">
                                            <div class="table-scrollable">
                                                <div id="fld_times_details">
                                                    <table class="table table-bordered table-hover table-condensed ">
                                                        <tr>
                                                            <td width="30%">Account Code: </td>
                                                            <td>
                                                                <a href="#" title="Account Details" data-target="#account-details" data-toggle="modal"><?php echo $userlist->getUserAccount(); ?> <span class="glyphicon glyphicon-eye-open" title="Account Detail"></span></a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="30%">Contact Name: </td>
                                                            <td><?php echo $userlist->getFirstName(); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Company:</td>
                                                            <td><?php echo $userlist->getCompany(); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Sender Address</td>
                                                            <td><?php echo $userlist->getReturnAddress(); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Country</td>
                                                            <td><?php echo $userlist->getCountry(); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Email: </td>
                                                            <td><?php echo $userlist->getEmail(); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td> Contact No</td>
                                                            <td><?php echo $userlist->getPhone(); ?></td>
                                                        </tr>

                                                    </table><br /><br />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset> 
                            </div>
                            
                            <div class="col-sm-6">
                                <fieldset class="fsStyle"  style="min-height:300px;">
                                    <legend class="legendStyle"> <a data-toggle="collapse" data-target="#general2" href="#">Delievery Details</a> </legend>
                                    <div class="row collapse in" id="general2">
                                        <div class="col-md-12">
                                            <div class="table-scrollable">
                                                <div id="fld_times_details">
                                                    <table class="table table-bordered table-hover table-condensed ">
                                                        <tr>
                                                            <td>Type of Shipment:</td>
                                                            <td>
                                                                <?php
                                                                if (trim($consignment->getType()) == 'D' || trim($consignment->getType()) == '')
                                                                    echo 'Dispatch service';
                                                                else if (trim($consignment->getType()) == 'C')
                                                                    echo "Collection Service";
                                                                else
                                                                    echo "Product (" . $consignment->getType() . ")";
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Reference No: </td>
                                                            <td><?php echo $consignment->getReference(); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Contact Name</td>
                                                            <td><?php
                                                                    echo $consignment->getContact();
                                                                ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Company Name</td>
                                                            <td><?php
                                                                    echo html_entity_decode($consignment->getCompany());
                                                                ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Address: </td>
                                                            <td><?php
                                                                    echo html_entity_decode($consignment->getAddressLine1()) . ' ' . html_entity_decode($consignment->getAddressLine2()) . ' ' . html_entity_decode($consignment->getAddressLine3());
                                                                ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>City:</td>
                                                            <td><?php
                                                                    echo html_entity_decode($consignment->getCity());
                                                                ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td> Postcode: </td>
                                                            <td><div class="col-sm-3"><?php
                                                                        echo $consignment->getPostcode();
                                                                    ?>
                                                                </div><div class="col-sm-7"><strong>Remote Area:</strong><span id="remote-area-div"><?php echo ($consignment->getRemoteCharges() == 'YES') ? 'YES' : 'NO'; ?></span></div><div class="col-sm-2"><a href="#" title="Remote Area" data-target="#remotearea_change" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" title="Remote Area"></span></a></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Country:</td>
                                                            <td><?php
                                                                    echo html_entity_decode($consignment->getCountry()) . '(' . $consignment->getCountryIsoCode() . ')';
                                                                ?></td>
                                                        </tr>                    
                                                        <tr>
                                                            <td> Telephone: </td>
                                                            <td><?php
                                                               
                                                                    echo $consignment->getTelephone();
                                                                ?></td>

                                                        </tr>
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            
                          
                        
                        </div>

                        <div class="table-scrollable">
                            <fieldset class="fsStyle">
                                <legend class="legendStyle"> <a data-toggle="collapse" data-target="#prodcut" href="#">Shipment Details</a> </legend>
                                <div class="row collapse in" id="prodcut">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover table-condensed ">
                                            <tr>
                                                <td>Weight: </td>
                                                <td><span class="ribbon-content" id="weight-update-section"> <?php echo $consignment->getWeight() ?> </span>KG</td> 
                                                <td>Pieces:</td>
                                                <td><?php echo $consignment->getNumberPieces() ?></td>
                                                <td> Value:</td>
                                                <td><?php echo $consignment->getCurrency(); ?> <?php echo $consignment->getValue(); ?></td>
                                                <td><div class="ribbon-content">
                                                        <input type="button" class="btn btn-primary btn-circle" value="Weight" title="Modify Weight" name='btnweight' <?php
                                                        if (trim($consignment->getIsInvoiced()) == 'Y') {
                                                            echo 'disabled="disabled"';
                                                        }
                                                        ?> onclick="weightDiscrepancy('<?php echo $consignment->getWeight(); ?>', <?php echo $this->id; ?>)" data-toggle="modal" data-target="#myModalvolume"/>
                                                        <!--<input type="button" value="Email" class="btn btn-danger btn-block btn-circle"  title="Resend Email"  id="btnresendweight" name="btnresendweight" onclick="resendWeightEmail(< ?php echo $this->id; ?>, < ?php echo $consignment->getUpdateWeight(); ?>, < ?php echo $consignment->getWeight(); ?>)" />-->
                                                    </div>
                                                </td>
                                            </tr>
                                            </tr>
                                            <td> Vol/Wt: </td>
                                            <td><input type="text" id="updateweight" name="updateweight" style="border:none !important;width:50px; float: left!important; " readonly value="<?php echo $consignment->getVolWeight(); ?>" /><?php
                                                $ConversionFaction = 5000;
                                                $serviceConversion = new ServiceFilter();
                                                $serviceConversion->addFieldFilter('code', $consignment->getHandling());
                                                $serviceConversionList = $serviceConversion->getColumnList(' volumetric_denominator');
                                                if (count($serviceConversionList) > 0)
                                                    $ConversionFaction = $serviceConversionList[0]->getVolumetricDenominator();
                                                ?>
                                                <input type="text" name="volbase" id="volbase" class="form-control" step=" width:50px; float: right!important; "  value="<?php echo $ConversionFaction; ?>" /></td>
                                            <td>Description:</td>
                                            <td><?php echo $consignment->getDescription(); ?></td>

                                            <td>Product: </td>
                                            <td><?php echo "NDX" ?></td>
                                            <td>
                                                <div class="ribbon-content">
                                                    <input type="button" class="btn btn-primary btn-circle" value="Volume" name="btnvolume" id="btnvolume" onclick="volumecalculation(<?php echo $this->id; ?>, '<?php echo $consignment->getIsInvoiced(); ?>')"   data-toggle="modal" data-target="#myModalvolume"/>
                                                    <!--<input type="button"  class="btn btn-primary btn-circle hidd" value="Email" id="btnresendvolume" name="btnresendvolume" onclick="resendVolumeEmail(< ?php echo $this->id; ?>)" />-->
                                                </div>
                                            </td>
                                            </tr>
                                            <?php
                                            $arrayService = array('A|A', 'A|C', 'A|E', 'A|F', 'A|H', 'A|Y', 'B|B', 'B|J', 'B|K', 'B|Z', 'YSWSPALLET');
                                            if (in_array($consignment->getHandling(), $arrayService)):
                                                ?>
                                                <tr>
                                                    <td>Pallet Information:</td>
                                                    <td>Quater Pallet &nbsp; &nbsp; &nbsp;<?php echo $consignment->getQuarterPallet() ?></td>
                                                    <td>Half Pallet &nbsp; &nbsp; &nbsp;<?php echo $consignment->getHalfPallet() ?></td>
                                                    <td>Full Pallet &nbsp; &nbsp; &nbsp;<?php echo $consignment->getFullPallet() ?></td>
                                                    <td>Pallets Lifts &nbsp; &nbsp; &nbsp;<?php echo $consignment->getPalletLifts() ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td> Chargeable Wt: </td>
                                                <td><?php echo ($consignment->getVolWeight() > $consignment->getWeight()) ? $consignment->getVolWeight() : $consignment->getWeight(); ?></td>

                                                <td>Q/Ref:</td>
                                                <td><input type="text" class="form-control" id="quotationreference" name="quotationreference" value="<?php echo $quotationid; ?>" /></td>
                                                <td>


                                                <input type="button" class="btn btn-primary btn-circle" value="Assign Quotation" <?php
                                                    if (trim($consignment->getIsInvoiced()) == 'Y') {
                                                        echo 'disabled="disabled"';
                                                    }
		                                          if($this->userSession->getUserType() ==  User::USER_TYPE_WAREHOUSE ) {
														echo 'disabled="disabled"';
													}
													
                                                    ?> onclick="showQuotation(<?php echo $this->id; ?>)" />
                                                    
                                                </td>
                                                <td><input type="button" class="btn btn-primary  btn-circle" value="Proforma Invoice" onClick="window.open('cs_proformaInvoice.php?id=<?php echo $this->id; ?>', 'windowname', ' height=600')" /></td>
                                                <td><input type="button" class="btn btn-primary btn-circle" data-toggle="modal" data-target="#email-log-popup" title="Emails option"       value="Send Email" 
                                                <?
												  if($this->userSession->getUserType() ==  User::USER_TYPE_WAREHOUSE ) {
														echo 'disabled="disabled"';
													}
                                                
                                                ?> /></td>
                                                <td><?php
                                                    $agentid = $consignment->getAgentid();
                                                    $AgentData = new AgentDataFilter();
                                                    $AgentData->addFieldFilter("id", $agentid);
                                                    $agentList = $AgentData->getList();

                                                    if (count($agentList) > 0) {
                                                        $agentList = $agentList[0];
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>


                                            </table>
                                        </div>
                                    </div>
                                </fieldset>

                            </div>

                            <div class="table-scrollable">
                                <fieldset class="fsStyle">
                                    <legend class="legendStyle"> <a data-toggle="collapse" data-target="#agent" href="#">Agent Details</a> </legend>
                                    <div class="row collapse in" id="agent">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover table-condensed ">
                                                <tr>
                                                    <td><input type="button" class="btn btn-primary btn-circle" value="Agent Details" onclick="window.open('agent_details.php?agent_id=<?php echo $agentid; ?> ', '_blank');" /><a href="#" title="Agent" data-target="#agent_change" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" title="Agent"></span></a></td>
                                                    <td></td>
                                                    <td>Agent: </td>
                                                    <td><?php echo $agentList->getAgentCode(); ?> <?php echo $agentList->getAgentName(); ?></td>
                                                    <td> Transit Time(hrs): </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td> Del:</td>
                                                    <td><?php echo $agentList->getAgentCode(); ?> <?php echo $agentList->getAgentName(); ?></td>
                                                    <td>Mawb No: </td>
                                                    <td><?php echo $consignment->getMawb(); ?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <?php
                                            } else {
                                                ?>
                                                </td>
                                                </tr>
                                                <tr>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                            </div> 

                            <div class="table-scrollable">
                                <fieldset class="fsStyle">
                                    <legend class="legendStyle"> <a data-toggle="collapse" data-target="#agent" href="#">Agent Details</a> </legend>
                                    <div class="row collapse in" id="agent">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover table-condensed ">
                                                <tr>
                                                    <td><input type="button" class="btn btn-primary btn-circle" value="Agent Details" onclick="window.open('agent_details.php', '_blank');" /><a href="#" title="Agent" data-target="#agent_change" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" title="Agent"></span></a></td>
                                                    <td></td>
                                                    <td>Agent: </td>
                                                    <td>No Agent</td>
                                                    <td> Transit Time(hrs): </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td> Del:</td>
                                                    <td></td>
                                                    <td>Mawb No: </td>
                                                    <td><?php echo $consignment->getMawb(); ?></td>
                                                    <td>Item Type</td>
                                                    <td><?php echo $consignment->getItemType(); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td>Delivery Instruction:</td>
                                                <td><?php echo $consignment->getNotes(); ?></td>
                                                <td> Manifest No: </td>
                                                <td><?php
														$manifestFilterCal	=	new ManifestConsignmentDataFilter();
														$manifestFilterCal->addConsignmentIDFilter($consignment->getId());
														$manifestFilterData	=	$manifestFilterCal->getList();
														if(count($manifestFilterData)>0 )
														{
															foreach($manifestFilterData as $mData)
															{
																echo $mData->getManifestId().', ';
															}
														}
														else
															echo $consignment->getNotes(); 
														
												?></td>
                                                <td> Remarks:</td>
                                                <td><?php echo $consignment->getNotes(); ?></td>
                                            </tr>
                                            <tr>
                                                <td> Flight No:</td>
                                                <td></td>
                                                <td>Carrier Name:</td>
                                                <td>
                                                <?php 
													if($consignment->getType() != '' && $consignment->getType() != 'D' && $consignment->getType() != 'C')
													{
														if($this->carrier  != "")
														{
															echo $this->carrier ;
														}
														else
															echo $consignment->getServiceType() ;
													}
													else
													{
														echo $consignment->getServiceType(); 
                                                    }?>
                                                </td>
                                                <td> Tracking Number (AWB):</td>
                                                <td> <a href="tracking.php?tracking_number=<?php echo $consignment->getAwb(); ?>" target="_blank"><?php echo $consignment->getAwb(); ?></a><?php /* ?><a href="#" title="awb_change" data-target="#awb_change" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" title="awb_change"></span></a><?php */ ?></td>
                                            </tr>
                                            <tr>
                                            	<td> Custom Export Number: </td>
                                               
                                               		<?
														$ConsignmentDetailsFilter = new ConsignmentDetailsFilter();
														$ConsignmentDetailsFilter->addconsignmentFilter($consignment->getId());
														$custome_list = $ConsignmentDetailsFilter->getList();
														if(count($custome_list) > 0)
														{
													?>
                                                    	 <td id="custom_exp_no_db"> 
                                                         	<? echo $custome_list[0]->getCustomExportNumber(); ?>
                                                		</td>
                                                   <?
														}
														else
														{
													?>
															<td id="custom_exp_no_save">
                                                            	<input type="text" class="form-control" id="custom_export_number" name="custom_export_number" value="<?php echo $custom_export_number; ?>" />
                                                                <input type="button" class="btn btn-primary btn-circle" value="custom_export" onclick="SaveExportCustome(<?php echo $consignment->getId(); ?>)" />
                                                            	
                                                            </td>
													<?
														}
													?> 
                                                    <td> Value Type: </td>
                                                    <td> 
                                                    <div id="highlowvalueshow" style="display:block;">
                                                    <label id="valueType"> <? echo $consignment->getHvLvDescription(); ?> </label>
                                                    <input type="button" class="btn btn-primary btn-circle" value="Edit" onclick="EditValueType()" />
                                                    </div>
                                                    <div id="highlowvalueedit" style="display:none;">
                                                    <select id="highlowvalue" name = "highlowvalue" class="form-control" >
                                                        <option value="">Select Type of Value</option>
                                                        <option <?php if ($this->form_vars['highlowvalue'] == "HV")  echo 'selected'; ?> value="HV">High Value</option>
                                                        <option <?php if ($this->form_vars['highlowvalue'] == "MV")  echo 'selected'; ?> value="MV">Medium Value</option>
                                                        <option <?php if ($this->form_vars['highlowvalue'] == "LV")  echo 'selected'; ?> value="LV">Low Value</option>
                                                    </select>
                                                    <input type="button" class="btn btn-primary btn-circle" value="Save" onclick="SaveHighValue(<?php echo $consignment->getId(); ?>)" />
                                                    </div>
                                                    
                                                    </td>
                                                    <td colspan="2">&nbsp;</td>
                                                    
                                                  
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </fieldset>
                        </div> 
                       	<?php if($this->userSession->getUserType() == User::USER_TYPE_CUSTOMER_SERVICE){?>
                        	<input class="btn btn-danger pull-right btn-circle" id="btn_back_l" name="btn_back_l" type="button" value="back" onClick="window.location = 'cs_bookings.php'" >
                         <?php }else { ?>
						
                         <input class="btn btn-danger pull-right btn-circle" id="btn_back_l" name="btn_back_l" type="button" value="back" onClick="window.location = 'bookings.php'" >
                        <? } ?>
                        <input class="btn btn-primary pull-right btn-circle" id="btn_save" name="btn_save" type="submit" value="save"  <?php
                        if (trim($consignment->getIsInvoiced()) == '1') {
                            echo 'disabled="disabled"';
                        }
                        ?> onClick="document.location.href = 'bookings.php'">
                        <!--target="_blank"-->  

						
                        <a href="#" onclick="ShowLabel('<?php echo $consignment->getSingleLabel(); ?>')">
                       
                            <input class="btn btn-primary pull-right btn-circle" id="btn_label" name="btn_label" type="button" value="Label"  >
                        </a>
                        
                        &nbsp;
                        &nbsp;
                        <?php
						 if($this->userSession->getUserType() ==  User::USER_TYPE_FINANCE || $this->userSession->getUserType() == User::USER_TYPE_ADMIN || $this->userSession->getUserType() == User::USER_TYPE_CUSTOMER_SERVICE) {
						?>
                       &nbsp;
                       	<?php if($this->userSession->getUserType() != User::USER_TYPE_CUSTOMER_SERVICE):?>
                        <a href="#" title="Update Pricing" >
                            <input class="btn btn-primary pull-right btn-circle" id="btn_update_pricing" name="btn_update_pricing" type="button" value="Update Pricing" onClick="pricingUpdateCalculation('<?php echo $consignment->getId(); ?>');">
                        </a>
                        <?php endif;?>
                        &nbsp;
                        <a href="#" title="Pricing" data-target="#pricing-invoice" data-toggle="modal">
                            <input class="btn btn-primary pull-right btn-circle" id="btn_pricing" name="btn_pricing" type="button" value="Pricing" onClick="pricingCalculation('<?php echo $consignment->getId(); ?>');">
                        </a>&nbsp;
                       
                        <a href="#" title="audit" data-target="#audit-log" data-toggle="modal">
                            <input class="btn btn-primary pull-right btn-circle" id="btn_audit" name="btn_audit" type="button" value="Audit" onClick="ShowAudit('<?php echo $consignment->getId(); ?>');" >
                        </a>
                        <?php if($this->userSession->getUserType() != User::USER_TYPE_CUSTOMER_SERVICE):?>
                        <a href="#" title="Price audit" data-target="#audit-log" data-toggle="modal" id="price_audit_id">
                            <input class="btn btn-primary pull-right btn-circle" id="btn_price_audit" name="btn_price_audit" type="button" value="Price Audit" data-con_id="<?php echo $consignment->getId(); ?>" />
                        </a>
                        <?php endif;?>
                        <?
						if ( $consignment->getIsInvoiced() != 'Y'):
						 ?>
                            <a href="#" title="Change Account" data-target="#change_account" data-toggle="modal">
                                <input class="btn btn-primary pull-right btn-circle" id="btn_ChangeAccount" name="btn_ChangeAccount" type="button" value="ChangeAccount">
                            </a>&nbsp;
                                                 <?php endif; 
                         if ( $consignment->getIsInvoiced() != 'Y'): ?>
                            <a href="#" title="Tracking Edit" data-target="#tracking-edit" data-toggle="modal">
                                <input class="btn btn-primary pull-right btn-circle" id="btn_TrackingEdit" name="btn_TrackingEdit" type="button" value="TrackingEdit">
                            </a>
                        <?php endif;
						
						 } 
						 ?>
                        &nbsp;
                        <div class="modal fade" id="myModalvolume" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">


                                    <div class="modal-body">


                                        <div id='loadcontent'>

                                        </div> 

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                                    </div>
                                </div>
                            </div>
                        </div>        
                        <br clear="all"/>
                    </div>
                </div>
                <div class="portlet portlet-sortable-empty"></div>
            </div>
            <div class="col-md-4  sortable">
                <div class="portlet box blue portlet-sortable">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gift"></i>POD Status
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse"></a>
                            <a href="#portlet-config" data-toggle="modal" class="config"></a>
                            <a href="" class="fullscreen"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <fieldset class="fsStyle">
                            <legend class="legendStyle"> <a data-toggle="collapse" data-target="#booking" href="#">Booking Details</a> </legend>
                            <div class="">
                                <div class="row collapse in" id="booking">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover table-condensed ">
                                            <tr>
                                                <td>HawbNo:</td>
                                                <td><?php echo $consignment->getHawb(); ?></td>
                                            </tr>
                                            <tr>
                                                <td> Tracking Ref (Pieces):</td>
                                                <td> <?php /* ?><a href="../main/tracking.php?tracking_number=<?php echo $consignment->getAwb(); ?>" target="_blank"><?php echo $consignment->getAwb(); ?></a><?php */ ?><?php
                                                    if (count($consignment->getParcels()) > 0) {
                                                        $parcel_list = $consignment->getParcels();
                                                        $parcel_count = sizeof($parcel_list);
                                                        foreach ($parcel_list as $parcel) {
                                                            echo '<a href="tracking.php?tracking_number=' . $parcel->getTrackingNumber() . '" target="_blank">' . $parcel->getTrackingNumber() . "</a><br>";
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> Return Tracking Number:</td>
                                                
                                                <td><!-- <?php print_r($consignment);?>--><?php echo $consignment->getReturnAwb(); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Expected DOD:</td>
                                                <td><?php echo $consignment->getDateBooked(); ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"> Not to Pay Agent
                                                    <input type="checkbox" value=""   /></td>
                                            </tr>
                                            <tr>
                                                <td>Invoice OnHold:</td>
                                                <td>
                                                    <select id="billing_hold" name="billing_hold" onchange="changeBillingHoldStatus('<?php echo $consignment->getId() ?>', this.value);" class="form-control">
                                                        <option <?php echo ($consignment->getBillingHold() == 'NO' || trim($consignment->getBillingHold()) == '') ? 'selected="selected"' : ''; ?> value="NO">No</option>
                                                        <option <?php echo ($consignment->getBillingHold() == 'YES') ? 'selected="selected"' : ''; ?> value="YES">Yes</option>
                                                    </select>

                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fsStyle">
                            <legend class="legendStyle"> <a data-toggle="collapse" data-target="#agent-com" href="#">Agent Internal Coment</a> </legend>
                            <div class="">
                                <div class="row collapse in" id="agent-com">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover table-condensed ">
                                            <tr>
                                                <td>Shipment Status </td>
                                                <td><?php
                                                    $consignment_status = $consignment->getConsignmentStatus();
                                                    $consignment_status = $consignment_status == 'received' ? 'label created' : $consignment_status;
                                                    $consignment_status = $consignment_status == 'booked' ? 'shipped' : $consignment_status;
                                                    echo ucwords(strtolower($consignment_status));
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> POD Status</td>
                                                <td><?php
                                                    $tracking = new TrackingDataFilter();
                                                    $tracking->addConsignmentIDFilter($this->id);
                                                    $tracking->AddOrderByID(false);
                                                    $trackingList = $tracking->getColumnList('status_code,pod_image, date_created');
                                                    if (count($trackingList) > 0)
                                                        echo ucwords(strtolower($trackingList[0]->getStatusCode()));
													if($this->userSession->getUserType() !=  User::USER_TYPE_WAREHOUSE ) {
													
                                                    if ($this->consignment_status != 'invalid' || $this->consignment_status != 'valid') {
                                                        ?>   <a href="#" title="UPDATE TRACKING POD" data-target="#tracking-pod" data-toggle="modal"><span class="glyphicon glyphicon-eye-open"></span></a>
                                                        <?php
                                                    }
													}
                                                    ?></td>
                                            </tr>
                                            <tr>
                                                <td> Date</td>
                                                <td><?php
                                                    if (count($trackingList) > 0) {
                                                        echo '' . date('d-m-Y', strtotime($trackingList[0]->getDateCreated())) . '';
                                                    } else {
                                                        echo ($consignment->getDateDelivered() != '') ? $consignment->getDateDelivered() : $consignment->getDateBooked();
                                                    }
                                                    ?></td>
                                            </tr>
                                            <tr>  
                                                <td>POD File</td>
                                                <td><?php
                                                    if (count($trackingList) > 0 && $trackingList[0]->getPodImage() != '') {
                                                        echo '<a href="../pod_images/' . $trackingList[0]->getPodImage() . '">' . $trackingList[0]->getPodImage() . '</a>';
                                                    }
                                                    ?></td>

                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="col-sm-12">
                                                        <div class="col-sm-3">
                                                            <input type="button" class="btn btn-primary btn-circle" value="Contact" id='btncontact' name='btncontact' onclick="contactdetails(<?php echo $agentid; ?>, '<?php echo $userlist->getTelephone(); ?>');"  data-toggle="modal" data-target="#myModalvolume"/>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="button" class="btn btn-primary btn-circle" value="Log" onclick="displayLog(<?php echo $this->id; ?>)" data-toggle="modal" data-target="#myModalvolume"
                                                              <?
																  if($this->userSession->getUserType() ==  User::USER_TYPE_WAREHOUSE ){
																	echo 'disabled="disabled"';
																}
                                                
                                             				   ?>
                                                            />
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a href="#" title="audit" data-target="#audit-log" data-toggle="modal">
                                                                <input class="btn btn-primary pull-right btn-circle" id="btn_audit" name="btn_audit" type="button" value=" History " onClick="ShowAudit('<?php echo $consignment->getId(); ?>');">
                                                            </a>
                                                               <!-- <input type="button" class="btn btn-primary btn-block btn-circle" value="History" id="historylog" name="historylog"
                                                                       onclick="showHistory('<?php // echo $consignment->getHawb();       ?>');" />-->
                                                        </div>
                                                        <div class="col-sm-3"><input type="button" class="btn btn-primary btn-circle" value="Mail" onclick="SendEmail(<?php echo $this->id; ?>)"   data-toggle="modal" data-target="#myModalvolume"
                                                            <?
																  if($this->userSession->getUserType() ==  User::USER_TYPE_WAREHOUSE ){
																	echo 'disabled="disabled"';
																}
                                                
                                             				   ?>
                                                        />
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fsStyle hide" id="duplicate-shipment-section" data-action="GET_DUPLICATE_TRACKING_SHIPMENTS" data-tracking="<?php echo $consignment->getAwb() ?>">
                            <legend class="legendStyle"> <a data-toggle="collapse" data-target="#agent-com" href="#">Duplicate Shipments</a> </legend>
                            <div class="">
                                <div class="row collapse in">
                                    <div class="col-md-12" id="duplicate-tracking">
                                        
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="portlet portlet-sortable-empty"></div>
            </div>
        </div>
        <input name="id" type="hidden" value="<?php echo $this->id; ?>">
        <!-- CALCULATOR DIV -->
        <?php /*?><div class="modal fade modal-transparent modal-fullscreen " id="calc_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-box">
                            <button type="button" class="close"  data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div class="cal-header">
                              <h3 class="heading-cal"><!--<img src="../images/logo-calc.png">--> QUOTATION</h3>
                                <div class="cal-screen">
                                    <div class="col-md-2 col-sm-2 col-xs-2 text-center" id="screen_currency"></div>
                                    <div class="col-md-2 col-sm-2 col-xs-2 text-center" id="screen_scale"></div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 text-center" id="screen_dim"></div>
                                    <div class="col-md-4 col-sm-4 col-xs-4 text-center" id="screen_from_to"></div>
                                    <div class="col-md-12"> <span class="cal-result">
                                            <h2 id="screen_price">0</h2>
                                        </span> </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="cal-body">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <input id="quotation_id" name="quotation_id"  value="" type="hidden">
                                            <!-- <label class="control-label font-green-soft" for="calc_chargeable_weight"><strong>Chargeable Weight</strong></label>-->
                                            <input id="calc_chargeable_weight" name="calc_chargeable_weight" placeholder="Weight Charge" class="form-control chargeable_weight" type="text" readonly>
                                        </div>
                                        <div class="col-sm-8"> 
                                          <!-- <label class="control-label font-green-soft" for="calc_chargeable_weight"><strong>Chargeable Weight</strong></label>-->
                                            <input id="useremail" name="useremail" placeholder="Email" class="form-control calculate_price_input" type="text"  style="color:#fff">
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3"> 
                                            <!--<label class="control-label" for="textinput">Currency</label>-->
                                            <div id="loadCurrency"> 
                                                <script type="text/javascript">
                                                    $.post(
                                                            "quotation.php",
                                                            {action: 'LOAD_CURRENCY'},
                                                    function(data)
                                                    {
                                                        $('#loadCurrency').html(data);
                                                    });
                                                </script> 
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!--   <label class="control-label" for="textinput">Account</label>-->
                                            <div id="accountdrop"> 
                                                <script type="text/javascript">
                                                    $.post(
                                                            "quotation.php",
                                                            {action: 'ACCOUNTDROPDOWN'},
                                                    function(data)
                                                    {
                                                        $('#accountdrop').html(data);
                                                    });
                                                </script> 
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!-- <label class="control-label" for="textinput">Shopping from?</label>-->
                                            <div id="fromCountry"> 
                                                <script type="text/javascript">
                                                    $.post(
                                                            "quotation.php",
                                                            {action: 'FROM_COUNTRY'},
                                                    function(data)
                                                    {
                                                        $('#fromCountry').html(data);
                                                    });
                                                </script> 
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!-- <label class="control-label" for="textinput">Ship to?</label>-->
                                            <div id="toCountry"> 
                                                <script type="text/javascript">
                                                    $.post(
                                                            "quotation.php",
                                                            {action: 'TO_COUNTRY'},
                                                    function(data)
                                                    {
                                                        $('#toCountry').html(data);
                                                    });
                                                </script> 
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <input id="city" name="city" placeholder="City" title="City" class="form-control calculate_price_input" type="text" style="color:#fff" >
                                        </div>
                                        <div class="col-sm-3">
                                            <input id="postcode" name="postcode" title="Postcode" placeholder="PostCode" class="form-control calculate_price_input" type="text"style="color:#fff" >
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!--  <label class="control-label" for="textinput">Carrier</label>-->
                                            <div id="carrierdrop"> 
                                                <script type="text/javascript">
                                                    $.post(
                                                            "quotation.php",
                                                            {action: 'CARRIER'},
                                                    function(data)
                                                    {
                                                        $('#carrierdrop').html(data);
                                                    });
                                                </script> 
                                            </div>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!--  <label class="control-label" for="textinput">Service Type</label>-->
                                            <div id="servicetype"> </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="form-group" id="calc_total_weight_container"> 
                                        <!-- <div class="col-sm-4">
                                                                                        <label class="control-label" for="calc_weight">Weight</label>
                                                                                    </div>
                                                                                    <div class="col-sm-4">
                                                                                        <label class="control-label" for="calc_weight">Pieces</label>
                                                                                    </div>
                                                                                    <div class="clearfix"></div>-->
                                        <div class="col-sm-3">
                                            <input id="calc_weight" name="calc_weight" placeholder="Weight" class="form-control calculate_price_input" style="color:#fff" type="text" title="Weight">
                                            <div id="calc_total_weight_error_container" class="help-block with-errors">Please enter valid weight</div>
                                        </div>
                                        <div class="col-sm-3">
                                            <select id="pieces" name="pieces" class="form-control" style="color:#fff" title="Pieces" onchange="addNewDimmBoxes(this);">
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
                                            <!--<input id="pieces" name="pieces" placeholder="Pieces" class="form-control calculate_price_input" style="color:#fff" title="Pieces" type="text">--> 
                                        </div>
                                        <div class="col-sm-3">
                                            <select id="conversion" name="conversion" class="form-control" style="color:#fff" title="Conversion">
                                                <option value="">Conversion Rate</option>
                                                <option value="2000">2000</option>
                                                <option value="3000">3000</option>
                                                <option value="4000">4000</option>
                                                <option value="5000">5000</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <div id="radioBtn2" class="btn-group"> <a class="btn btn-warning  btn-md active" data-toggle="calc_scale" data-title="kg">KGS</a> <a class="btn btn-info  btn-md notActive" data-toggle="calc_scale" data-title="lb">LBS</a> </div>
                                            <input name="calc_scale" class="calculate_price" value="kg" id="calc_scale" type="hidden">
                                        </div>

                                                    <!--  <div class="col-sm-2"><i class="fa fa-refresh fa-lg fa-spin" id="calc_loading"></i></div>-->
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3"> 
                                            <!--<label class="control-label" for="calc_height">Height (cm)</label>-->
                                            <input title="discount" id="discount" name="discount" placeholder="Discount" class="form-control calculate_price_input" style="color:#fff" type="text">
                                            <input type="checkbox" name="discount_type" value="percentage" id="discount_type" >
                                            <label >PERCENT</label>
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!--<label class="control-label" for="calc_length">Length(cm)</label>-->
                                            <input id="calc_basic" title="Basic Charge"  name="calc_basic" placeholder="Basic Charge"  class="form-control chargeable_weight" type="text" readonly tyle="color:#fff" type="text" onkeyup="changeTheRatesValues();">
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!--<label class="control-label" for="calc_width">Width(cm)</label>-->
                                            <input id="calc_fuel" title="Fuel Charges"  name="calc_fuel" placeholder="Fuel Charges" class="form-control calculate_price_input" style="color:#fff" type="text"  onkeyup="changeTheRatesValues();">
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!--<label class="control-label" for="calc_height">Height (cm)</label>-->
                                            <input id="calc_extra" title="Extra Charges"  name="calc_extra" placeholder="Extra Charges" class="form-control calculate_price_input" style="color:#fff" type="text"  onkeyup="changeTheRatesValues();">
                                        </div>
                                        <div class="col-sm-3"> 
                                            <!--<label class="control-label" for="calc_height">Height (cm)</label>-->
                                            <input id="calc_vat" title="VAT"  name="calc_vat" placeholder="VAT Charges" class="form-control calculate_price_input" style="color:#fff" type="hidden"  onkeyup="changeTheRatesValues();">
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <textarea id="calc_remark"  title="Remark" name="calc_remark" rows="5" cols="60" class="form-control calculate_price_input" style="color:#fff; background-color:#555"></textarea>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <input type="hidden" id="quotationid"  name="quotationid" value="0"  />
                                            <input id="save" name="save" value="Save" type="button" onClick="saveQuotation();" class="btn btn-primary" style="display:none;"/>
                                            <input id="calculate" name="calculate" value="Calculate" type="button" onClick="calculatePrice();"  class="btn btn-primary"/>
                                            <input id="sendemail" name="sendemail" value="EMAIL" type="button" onClick="sendEmail();"  class="btn btn-success" style="display:none;"/>
                                            <input id="print_quote" name="print_quote" value="PRINT QUOTE" type="button" onClick="printquote();"  class="btn btn-success" style="display:none;"/>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <div  class="col-sm-12" id="dimention-section"></div>
                                        <div id="calculationVolumn" style="display:none;">
                                            <div  class="col-sm-6">
                                                <input id="volumn_weight_total" title="volumn_weight_total"  name="volumn_weight_total" placeholder="Vol Wght Total" class="form-control calculate_price_input" style="color:#fff" type="text"  >
                                            </div>
                                            <div  class="col-sm-6">
                                                <input id="calculate_volumn" name="calculate_volumn" value="Calculate Volumn" type="button" onClick="calclulateweightTotal();"  class="btn btn-primary"/>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><?php */?>
        <div class="modal fade" tabindex="-1" role="dialog" id="tracking-pod" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Update POD Status</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" id="pod_status_msg">Updated successfully</div>
                        <form id="update_pod_status_frm" name="update_pod_status_frm" method="post">
                            <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                            <input type="hidden" name="tracking_number" id="tracking_number" value="<?php echo $this->tracking_number; ?>" />
                            <input type="hidden" name="tracking_id" id="tracking_id" value="<?php echo $this->tracking_id; ?>" />
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft"><strong>POD Status</strong></label>
                                        <select id="pod_status" name="pod_status" class="form-control">
                                            <option value="">Please Select</option>
                                            <option value="booked"<?php echo ($this->consignment_status == 'booked' ? ' selected="selected"' : ''); ?>>Shipped</option>
                                            <option value="returned"<?php echo ($this->consignment_status == 'returned' ? ' selected="selected"' : ''); ?>>Returned</option>
                                            <option value="hold"<?php echo ($this->consignment_status == 'hold' ? ' selected="selected"' : ''); ?>>Hold</option>
                                            <option value="closed"<?php echo ($this->consignment_status == 'closed' ? ' selected="selected"' : ''); ?>>Closed</option>
                                            <option value="delivered"<?php echo ($this->consignment_status == 'delivered' ? ' selected="selected"' : ''); ?>>Point of Delivery (POD)</option>
                                            <option value="nopod"<?php echo ($this->consignment_status == 'nopod' ? ' selected="selected"' : ''); ?>>NO POD AVAILABLE</option>
                                            <option value="lost"<?php echo ($this->consignment_status == 'lost' ? ' selected="selected"' : ''); ?>>Lost</option>
                                            <option value="intransit"<?php echo ($this->consignment_status == 'intransit' ? ' selected="selected"' : ''); ?>>In Transit</option>
                                            <option value="remote area"<?php echo ($this->consignment_status == 'remote area' ? ' selected="selected"' : ''); ?>>Remote Area</option>
                                            <option value="out for delivery"<?php echo ($this->consignment_status == 'out for delivery' ? ' selected="selected"' : ''); ?>>OUT FOR DELIVERY</option>
                                            <option value="held - awaiting duty payment"<?php echo ($this->consignment_status == 'held - awaiting duty payment' ? ' selected="selected"' : ''); ?>>HELD -AWAITING DUTY PAYMENT</option>
                                            <option value="bad address need better details"<?php echo ($this->consignment_status == 'bad address need better details' ? ' selected="selected"' : ''); ?>>BAD ADDRESS NEED BETTER DETAILS</option>
                                            <option value="refused"<?php echo ($this->consignment_status == 'refused' ? ' selected="selected"' : ''); ?>>REFUSED</option>
                                            <option value="dangerous goods"<?php echo ($this->consignment_status == 'dangerous goods' ? ' selected="selected"' : ''); ?>>DANGEROUS GOODS</option>
                                            <option value="damage goods"<?php echo ($this->consignment_status == 'damage goods' ? ' selected="selected"' : ''); ?>>DAMAGE GOODS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row pod_component_container">
                                <div class="col-sm-6">
                                    <div class="form-group" id="pod_name_container">
                                        <label class="control-label font-green-soft"><strong>Name</strong></label>
                                        <input type="text" name="pod_name" id="pod_name" value="" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft"><strong>POD Date</strong></label>
                                        <div class="input-group date form_datetime" data-date-format="yyyy-mm-dd HH:ii">
                                            <input type="text" size="16"  class="form-control" name="pod_date" id="pod_date"  />
                                            <span class="input-group-btn">
                                                <button class="btn default date-reset" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                            </span> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row pod_component_container">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft"><strong>Image</strong></label>
                                        <br />
                                        <div class="fileinput fileinput-new" data-provides="fileinput"> <span class="btn default btn-file"> <span class="fileinput-new"> Select file </span> <span class="fileinput-exists"> Change </span>
                                                <input type="file" name="pod_image" id="pod_image">
                                            </span> <span class="fileinput-filename"></span> &nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a> </div>
                                    </div>
                                </div>
                                <div class="col-md-6"> <br />
                                    <br />
                                    <label>
                                        <input type="checkbox" name="send_pod_status_mail" id="send_pod_status_mail" value="1">
                                        Send POD Status Email</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft" for="calc_chargeable_weight"><strong>Description</strong></label>
                                        <input type="text" name="pod_description" id="pod_description" value="" placeholder="Please enter description here" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr style="margin: 0px;" />
                        <div class="row">
                            <div class="col-md-12">
                                <h4>History</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Edit</th>
                                            <th>Date Time</th>
                                            <th>Track Point</th>
                                            <th>Event Content</th>
                                            <th>Other</th>
                                        </tr>
                                    </thead>
                                    <tbody id="history_content">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="update_pod_status_btn">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <!-- /.modal --> 

        <!-- Account Details -->
        <script>
            $(document).ready(function() {

                getCustomerDetais('<?php echo $consignment->getAccount(); ?>');
                
                
                $("#price_audit_id").on( "click", "input", function() {
                    $("#audit_content").html("<td colspan='4'>Please Wait...</td>");
                    var price_audit_con_id = "";
                   price_audit_con_id = $("#btn_price_audit").data("con_id");
                   
                   $.post('booking_view.php', {func: 'get_price_audit_log', consignment_id: price_audit_con_id}, function(data) {
                   $("#audit_content").html(data);
                });
                
                });
                
               $("body").on( "click", ".details_view", function() { 
                   $("#audit_content_details").html("<p>Please Wait...</p>");
                   $('#audit-log-details').modal('show');
                   var log_id = "";
                   log_id = $(this).data("log-id");
                   $.post('booking_view.php', {func: 'get_price_audit_log_detail', log_id: log_id}, function(data) {
                       $("#AncillaryChargesDetailsTxt").val(log_id);
                        $("#audit_content_details").html(data);
                   });
                });
                
            });
        </script>
        <div class="modal fade" tabindex="-1" role="dialog" id="account-details" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Customer Details</h4>
                    </div>
                    <div class="modal-body">
                        <!--<div class="alert alert-success" id="pod_status_msg">Updated successfully</div>-->
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">

                                    <tr>
                                        <th width="25%">Account Number</th>
                                        <td width="25%" id="ad-account"></td>
                                        <th width="25%">Company </th>
                                        <td  width="25%" id="ad-company"></td>
                                    </tr>
                                    <tr>
                                        <th>Full Name</th>
                                        <td id="ad-fullname"></td>
                                        <th>Owner </th>
                                        <td  id="ad-owner"></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td  id="ad-email"></td>
                                        <th>Alternative Email </th>
                                        <td  id="ad-alternativeemial"></td>
                                    </tr>

                                    <tr>
                                        <th>Return Address</th>
                                        <td id="ad-returnaddress"></td>
                                        <th>Billing Address </th>
                                        <td id="ad-billingaddress"></td>
                                    </tr>
                                    <tr>
                                        <th>Telephone</th>
                                        <td id="ad-telephone"></td>
                                        <th>Country </th>
                                        <td id="ad-country"></td>
                                    </tr>

                                    <tbody id="history_content">
                                    </tbody>
                                </table>
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
        <!-- /.modal --> 
        <form id="update_pricing_details_frm" name="update_pricing_details_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="pricing-invoice" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Pricing Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="pricing_details_msg">Updated successfully</div>
                            <input type="hidden" name="action" id="action" value="UPDATE_PRICING_DETAILS" />
                            <input type="hidden" name="invoice_detail_id" id="invoice_detail_id" value="" />
                            <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                            <input type="hidden" name="invoice_no" id="invoice_no" value="" />

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Customers Details</legend>

                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label class="control-label font-green-soft">Account:</label><strong><?php echo $consignment->getAccount(); ?></strong><br />
                                                <label class="control-label font-green-soft">Service:</label><strong><?php echo $consignment->getHandling(); ?></strong><br /> 
                                                <label class="control-label font-green-soft">Invoice Number:</label><strong id="invoice_no_show"><?php echo $consignment->getHandling(); ?></strong>      
                                            </div>

                                            <div class="col-md-4 form-group"><label class="control-label font-green-soft">Tracking No:</label><strong><?php echo $consignment->getAwb(); ?></strong><br /><label class="control-label font-green-soft">Order No. (Hawb):</label><strong><?php echo $consignment->getHawb(); ?></strong></div>
                                            <div class="col-md-3 form-group"><label class="control-label font-green-soft">Country:</label><strong><?php echo $consignment->getCountry(); ?></strong><br /><label class="control-label font-green-soft">City:</label><strong><?php echo $consignment->getCity(); ?></strong></div>
                                            <div class="col-md-2 form-group"><label class="control-label font-green-soft">Weight:</label><strong><?php
                                                    if ($consignment->getWeight() > $consignment->getVolWeight()) {
                                                        echo $consignment->getWeight();
                                                    } else {
                                                        echo $consignment->getVolWeight();
                                                    }
                                                    ?></strong><br /><label class="control-label font-green-soft">Pieces</label><strong><?php echo $consignment->getNumberPieces(); ?></strong></div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                            <?php 
									if($this->userSession->getUserType() == User::USER_TYPE_CUSTOMER_SERVICE)
									{
										$Col6to12	=	'12';
										$Col4to6	=	'6';
										$displayAreadCs	=	'style="display:none;"';
									}
									else
									{
										$Col6to12	=	'6';
										$Col4to6	=	'4';
										$displayAreadCs	=	'';
									}
									
								?>
                                <div class="col-md-<?=$Col6to12;?>">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Customer Charges</legend>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Basic Charges</label>
                                                <input type="text" name="basic_charges" id="basic_charges" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Fuel Charges</label>
                                                <input type="text" name="fuel_charges" id="fuel_charges" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Additional Charges</label>
                                                <input type="text" name="additional_charges" id="additional_charges" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Remote Area Charges</label>
                                                <input type="text" name="remote_area_charge" id="remote_area_charge" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                        </div>    
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">On Farword Charges</label>
                                                <input type="text" name="on_farword_charges" id="on_farword_charges" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">NDX</label>
                                                <input type="text" name="ndx" id="ndx" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                        </div>
                                        <div class="row">                                            
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">DDP</label>
                                                <input type="text" name="ddp" id="ddp" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">H/V</label>
                                                <input type="text" name="hv" id="hv" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Ancillary Charges  <a href="#" title="Ancillary Charges" id="ancillary-charges" data-toggle="modal"><span class="glyphicon glyphicon-eye-open" PRICING_DETAILS></span></a></label>
                                                <input type="text" readonly="readonly" name="ancillary_charges" id="ancillary_charges" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                        </div>
                                        <div class="row">                                        
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Discount</label>
                                                <input type="text" name="discount" id="discount" value="" class="form-control input-sm" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Extra</label>
                                                <input type="text" name="extra" id="extra" value="" class="form-control input-sm customer_charges" readonly="readonly" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Total</label>
                                                <input type="text" name="total" id="total" value="" class="form-control input-sm" readonly="readonly" />
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                
                                
                                <div class="col-md-<?=$Col6to12;?>" <?=$displayAreadCs;?>>
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Agent Cost</legend>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Basic Charges</label>
                                                <input type="text" name="agent_basic_charges" id="agent_basic_charges" value="" class="form-control input-sm agent_charges" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Fuel Charges</label>
                                                <input type="text" name="agent_fuel_charges" id="agent_fuel_charges" value="" class="form-control input-sm agent_charges" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Additional Charges</label>
                                                <input type="text" name="agent_additional_charges" id="agent_additional_charges" value="" class="form-control input-sm agent_charges" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Remote Area Charges</label>
                                                <input type="text" name="agent_remote_area_charge" id="agent_remote_area_charge" value="" class="form-control input-sm agent_charges" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">On Farword Charges</label>
                                                <input type="text" name="agent_on_farword_charges" id="agent_on_farword_charges" value="" class="form-control input-sm agent_charges" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Handling Charges</label>
                                                <input type="text" name="agent_handling_charges" id="agent_handling_charges" value="" class="form-control input-sm agent_charges" />
                                            </div>                                            
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">NDX</label>
                                                <input type="text" name="agent_ndx" id="agent_ndx" value="" class="form-control input-sm agent_charges" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">DDP</label>
                                                <input type="text" name="agent_ddp" id="agent_ddp" value="" class="form-control input-sm agent_charges" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Reference</label>
                                                <input type="text" name="reference" id="reference" value="" class="form-control agent_charges"  />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Linehaul</label>
                                                <input type="text" name="agent_linehaul_cost" id="agent_linehaul_cost" value="" class="form-control input-sm agent_charges" />
                                            </div>                                            
                                        </div>    
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Extra</label>
                                                <input type="text" name="agent_extra" id="agent_extra" value="0.00" class="form-control input-sm agent_charges" readonly="readonly" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Total</label>
                                                <input type="text" name="agent_total" id="agent_total" value="0.00" class="form-control input-sm" readonly="readonly" />
                                            </div>
                                        </div>                                        
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-<?=$Col4to6;?>"  <?=$displayAreadCs;?>>
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Margin</legend>
                                        <div class="form-group">
                                            <input type="text" class="form-control input-sm" name="margin" id="margin" value="" readonly="readonly" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-md-<?=$Col4to6;?>">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Tariff Name</legend>
                                        <div class="form-group" >
                                            <input type="text" class="form-control" name="tariff_name" id="tariff_name" value="" readonly="readonly" />
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-md-<?=$Col4to6;?>">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Last Modified</legend>
                                        <div class="form-group" >
                                            <input type="text" class="form-control" name="added_by" id="added_by" value="" readonly="readonly" />
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-warning extras" data-type="agent"  <?=$displayAreadCs;?>>Agent Extras</button>
                            <button type="button" class="btn btn-success extras" data-type="customer">Customer Extras</button>
                            <button type="button" class="btn btn-primary" id="update_pricing_details_btn" onclick="updatePricingDetails();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

            <!-- /.modal --> 
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="pricing-extra">
                <input type="hidden" id="hidden_extra_type" value="" />
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">Agent</label>
                                        <select name="agent_id" id="agent_id" class="form-control">
                                            <option value="">Select Agent</option>
                                            <?php
                                            $agentDataFilter = new AgentDataFilter();
                                            $agentDataFilter->addFieldFilter('active', '1');
											$agentDataFilter->AddOrderBy('agent_code', 'asc');
                                            $AgentData = $agentDataFilter->getColumnList('agent_code,agent_name,currency');
											
                                            if (count($AgentData) > 0) {
                                                foreach ($AgentData as $agent) {
                                                    echo '<option value="' . $agent->getId() . '" data-currency="' . $agent->getCurrency() . '">' . $agent->getAgentCode() . '[' . $agent->getAgentName() . ']</option>';
                                                }
                                            }
										
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">Charge Type</label>
                                        <select name="charge_type" id="charge_type" class="form-control">
                                            <option value="">Select Type</option>
                                            <?php
                                            $chargeTypeFilter = new InvoiceExtraChargesTypesFilter();
                                            $chargeTypeFilter->addFieldFilter('isactive', '1');
											$chargeTypeFilter->AddOrderBy('title', 'asc');
                                            $chargeTypeData = $chargeTypeFilter->getColumnList('title');
                                            if (count($chargeTypeData) > 0) {
                                                foreach ($chargeTypeData as $chargeType) {
                                                    echo '<option value="' . $chargeType->getId() . '">' . $chargeType->getTitle() . '</option>';
                                                }
                                            }
												
                                            ?>
                                        </select>
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">Description</label>
                                        <input type="text" name="description" id="description" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">Total</label>
                                        <input type="text" name="extra_total" id="extra_total" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="control-label font-green-soft">&nbsp;</label><br />
                                        <button type="button" id="btn_add_extra_charge" class="btn btn-success btn-block btn-sm"> Add </button>
                                        <!--<button type="button" id="btn_edit_extra_charge" class="btn btn-success btn-block btn-sm">Edit</button>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 extras_container" id="customer_extras_container">                                    
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Charge Type</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody id="customer_extras_body"></tbody>
                                    </table>
                                    <input type="hidden" id="customer_totals_extras" value="0" />
                                </div>
                                <div class="col-md-12 extras_container" id="agent_extras_container">
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Charge Type</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody id="agent_extras_body"></tbody>
                                        <input type="hidden" id="agent_totals_extras" value="0" />
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form id="update_change_account_frm" name="update_change_account_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="change_account" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Change Account</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_change_account_msg">Enter New Account</div>
                            <input type="hidden" name="action_account" id="action_account" value="UPDATE_CHANGE_ACCOUNT" />
                            <input type="hidden" name="consignment_id_account" id="consignment_id_account" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Accounts Detail</legend>

                                        <div class="row">
                                            <div class="form-group col-md-4">

                                                <tr>
                                                <label class="control-label font-green-soft">HawbNo:</label> 
                                                <td>
                                                    <input type="text" name="hawb_account" id="hawb_account" value="<?php echo $consignment->getHawb(); ?>" class="form-control" readonly disabled="disabled" /></td>

                                                </tr>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <tr>
                                                <label class="control-label font-green-soft">Current Account:</label> 
                                                <td> <input type="text" name="old_account" id="old_account" value="<?php echo $consignment->getAccount(); ?>" class="form-control" readonly disabled="disabled" />
                                                </td>
                                                </tr>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">New Account</label>
                                             			<? $this->userDropdown(); ?>
                                               <!-- <input type="text" name="new_account" id="new_account" value="" class="form-control " />-->
                                            </div>
                                        </div>   
                                    </fieldset>
                                </div>

                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_change_account_btn" onclick="updateConsignmentAccount();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

        </form>
        
        <form id="update_remotearea_frm" name="update_remotearea_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="remotearea_change" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Remote Area Change</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_remote_msg">Enter New Account</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_REMOTE" />
                            <input type="hidden" name="consignment_id_remote" id="consignment_id_remote" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Consignment Detail</legend>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">HawbNo:</label> 
                                                <input type="text" name="hawb_remote" id="hawb_remote" value="<?php echo $consignment->getHawb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Account:</label> 
                                                <input type="text" name="c_account" id="c_account" value="<?php echo $consignment->getAccount(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">City:</label>
                                                <input type="text" name="c_city" id="c_city" value="<?php echo $consignment->getCity(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Postcode:</label>
                                                <input type="text" name="Postcode" id="Postcode" value="<?php echo $consignment->getPostCode(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Postcode:</label>
                                                <input type="text" name="c_country" id="c_country" value="<?php echo $consignment->getCountry(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Remote Area</label><br />
                                                <?php
                                                if ($consignment->getRemoteCharges() == 'YES')
                                                    $remoteCheckBox = 'checked="checked"';
                                                else
                                                    $remoteCheckBox = '';
                                                ?>
                                                <input type="checkbox" name="remote_area_value" id="remote_area_value" value="YES" <?php echo $remoteCheckBox; ?> class="form-control " /> YES/NO
                                            </div>
                                        </div>   
                                    </fieldset>
                                </div>

                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_remote_btn" onclick="updateConsignmentRemote();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

        </form>

        <form id="update_tracking_frm" name="update_tracking_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="tracking-edit" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Tracking Edit</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_hawb_msg">Enter New Tracking</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_TRACKING" />
                            <input type="hidden" name="consignment_id_tracking" id="consignment_id_tracking" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Consignment Detail</legend>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Current Tracking Number:</label> 
                                                <input type="text" name="tracking_remote" id="tracking_remote" value="<?php echo $consignment->getAwb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">New Tracking Number:</label>
                                                <input type="text" name="tracking_new" id="tracking_new" value="" class="form-control" />
                                            </div>

                                        </div>   
                                    </fieldset>
                                </div>

                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_tracking_btn" onclick="updateConsignmentTracking();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

        </form>

        <form id="update_hawb_frm" name="update_hawb_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="hawb-edit" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">HAWB Edit</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_hawb_msg">Enter New HAWB</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_HAWB" />
                            <input type="hidden" name="consignment_id_hawb" id="consignment_id_hawb" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Consignment Detail</legend>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Current HawbNo:</label> 
                                                <input type="text" name="hawb_remote" id="hawb_remote" value="<?php echo $consignment->getHawb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">New HawbNo:</label>
                                                <input type="text" name="hawb_new" id="hawb_new" value="" class="form-control" />
                                            </div>

                                        </div>   
                                    </fieldset>
                                </div>

                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_hawb_btn" onclick="updateConsignmentHawb();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

        </form>

        <form id="update_awb_frm" name="update_awb_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="awb_change" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">AWB Edit</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_awb_msg">Enter New AWB</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_AWB" />
                            <input type="hidden" name="consignment_id_awb" id="consignment_id_awb" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Consignment Detail</legend>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">Current Tracking NO:</label> 
                                                <input type="text" name="awb_remote" id="awb_remote" value="<?php echo $consignment->getAwb() ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="control-label font-green-soft">New Tracking No:</label>
                                                <input type="text" name="awb_new" id="awb_new" value="" class="form-control" />
                                            </div>

                                        </div>   
                                    </fieldset>
                                </div>

                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_hawb_btn" onclick="updateConsignmentAwb();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

        </form>

        <form id="update_agent_frm" name="update_agent_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="agent_change" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Agent Change</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success hidden" id="update_agent_msg">Enter New Account</div>
                            <input type="hidden" name="action_remote" id="action_remote" value="UPDATE_AGENT" />
                            <input type="hidden" name="consignment_id_agent" id="consignment_id_agent" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Agent Details</legend>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">HawbNo:</label> 
                                                <input type="text" name="hawb_remote" id="hawb_remote" value="<?php echo $consignment->getHawb(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Account:</label> 
                                                <input type="text" name="c_account" id="c_account" value="<?php echo $consignment->getAccount(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Service:</label>
                                                <input type="text" name="service" id="service" value="<?php echo $consignment->getServiceType(); ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Agent Assigned:</label>
                                                <?php
                                                if ($consignment->getAgentid() != '' || $consignment->getAgentid() > 0) {
                                                    $AgentDataFilter = new AgentDataFilter();
                                                    $AgentDataFilter->addFieldFilter("id", $agentid);
                                                    $agentList = $AgentDataFilter->getColumnList('agent_code, agent_name');
                                                    if (count($agentList) > 0) {
                                                        $agentCode = $agentList[0]->getAgentCode();
                                                    }
                                                }
                                                ?>
                                                <input type="text" name="agent" id="agent" value="<?php echo $agentCode; ?>" class="form-control" readonly disabled="disabled" />
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="control-label font-green-soft">Agent</label><br />
                                                <select name="agent_area_id" id="agent_area_id" class="form-control">
                                                    <option value="">Select Agent</option>
                                                    <?php
                                                    $agentDataFilter = new AgentDataFilter();
                                                    $agentDataFilter->addFieldFilter('active', '1');
                                                    $AgentData = $agentDataFilter->getColumnList('agent_code,agent_name,currency');
                                                    if (count($AgentData) > 0) {
                                                        foreach ($AgentData as $agent) {
                                                            echo '<option value="' . $agent->getId() . '" data-currency="' . $agent->getCurrency() . '">' . $agent->getAgentName() . '[' . $agent->getAgentCode() . ']</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>   
                                    </fieldset>
                                </div>

                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_agent_btn" onclick="updateConsignmentAgent();">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

        </form>



        <!-- AUDIT MODEL -->
        <div class="modal fade" tabindex="-1" role="dialog" id="audit-log" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Audit Details</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>User</th>
                                            <th>Date Time</th>
                                        </tr>
                                    </thead>
                                    <tbody id="audit_content">
                                    </tbody>
                                </table>
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
        <!-- AUDIT MODEL Details -->
        <div class="modal fade" tabindex="-1" role="dialog" id="audit-log-details" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Audit Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="AncillaryChargesDetailsTxt" id="AncillaryChargesDetailsTxt" value="" />
                            <div class="col-md-12" id="audit_content_details">
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
        <!-- AUDIT MODEL Details -->
        <div class="modal fade" tabindex="-1" role="dialog" id="audit-log-details-ancillary" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Ancillary Charges Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" id="audit_content_details_ancillary">
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

        <div class="modal fade" tabindex="-1" role="dialog" id="email-log-popup" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Send Emails</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table  table-hover">
                                    <tbody>
                                        <tr>
                                            <td class="col-md-2">
                                            </td>
                                            <td class="col-md-4">
                                                <input id="btnresendweight" class="btn btn-danger btn-block btn-circle" type="button" onclick="resendWeightEmail(<?php echo $consignment->getId(); ?>, <?php echo $consignment->getUpdateWeight(); ?>, <?php echo $consignment->getWeight(); ?>)" name="btnresendweight" title="Resend Email" value="Weight Email">
                                            </td>

                                            <td class="col-md-4">
                                                <input id="btnresendvolume" class="btn btn-primary btn-block btn-circle" type="button" onclick="resendVolumeEmail(<?php echo $consignment->getId(); ?>)" name="btnresendvolume" value="Volumn Weight Email">
                                            </td>
                                            <td class="col-md-2">

                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
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
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
// check admin user is authenticated
       
        $user = SessionManager::getUser();
        $this->userSession = $user;
		if ($this->userSession->getUserType() != User::USER_TYPE_FINANCE && $this->userSession->getUserType() != User::USER_TYPE_ADMIN && $this->userSession->getUserType() != User::USER_TYPE_CUSTOMER_SERVICE && $this->userSession->getUserType() != User::USER_TYPE_WAREHOUSE)
		{
			util_redirect("index.php");
		}
		
		
		if (isset($_POST['action']) && $_POST['action'] == 'GET_DUPLICATE_TRACKING_SHIPMENTS') {
			$tracking	=	$_POST['tracking'];
			if(trim($tracking) != '')
			{
				$consignmentFilterDuplicate = new ConsignmentFilter();
				$consignmentFilterDuplicate->setFilter( "AND c.consignment_status not in ('recycled','invalid', 'valid') and c.awb = '".trim($tracking)."'");
				
				$consignmentFilterDuplicateData	=	$consignmentFilterDuplicate->getColumnList("awb, hawb,id, consignment_status");
				$htmlData	=	'';
				if(count($consignmentFilterDuplicateData)>1)
				{
					$htmlData	.=	'<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert-danger">
										<br>Please note below order reference numbers has same tracking numbers. <br><br>
									</div>
										<strong>
											<div class="row">
												<div  class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div  class="col-xs-12 col-sm-12 col-md-12 col-lg-12 alert-info">
														<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Order Reference <br>Tracking Number</div>
														<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Status</div>
														<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></div>
													</div>
												</div>
											</div>
										</strong>';
										$Rowbscount	= 0;
					foreach($consignmentFilterDuplicateData as $conData	)
					{
						$c_status = Consignment::stateText(strtolower(trim($conData->getConsignmentStatus())));
														if(strtolower($c_status) == "label created")
															 $c_status = "Printed";
														else if(strtolower($c_status) == "booked")
															 $c_status = "Shipped";
														else if(strtolower($c_status) == "valid")
															 $c_status = "Ready To Print";
														else
															 $c_status;
															if($Rowbscount%2 ==0)
							$rowClass	=	'';
						else
							$rowClass	=	'alert-info';
						$htmlData	.=	'
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div  class="col-xs-12 col-sm-12 col-md-12 col-lg-12 '.$rowClass.'">
											<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">'.$conData->getHawb().' <br> '.$conData->getAwb().'</div>
												<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">'.$c_status.'</div>
												<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
												<a href="booking_view.php?id='.$conData->getId().'" target="_blank">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</a>
											</div>
										</div>
									</div>
								</div>';
								$Rowbscount++;
					}
				}
			}
			echo $htmlData;
			die;				

		}
        if (isset($_POST['action']) && $_POST['action'] == 'UPDATE_REMOTE') {
            $responceArray = array();
            $consignmentId = $_POST['id'];
            $remotearea = $_POST['remotearea'];

            $consignmentCon = new Consignment($consignmentId);
            if (count($consignmentCon) > 0) {
                
                $account = $consignmentCon->getAccount();
                $handling = $consignmentCon->getHandling();
                $postCode = $consignmentCon->getPostcode();
                $country = $consignmentCon->getCountryIsoCode();
                
                $consignmentCon->setRemoteCharges($remotearea);
                $invoiced_shipment = false;
                $invoiceDetailUpdate = NULL;
                $invoiceDetailFilter = new InvoiceDetailFilter();
                $invoiceDetailFilter->addFieldFilter('consignment_id', $consignmentId);
                $invoiceDetailObj = $invoiceDetailFilter->getColumnList('invoice_no, remote_area_charge');
                if(!empty($invoiceDetailObj)){
                    foreach ($invoiceDetailObj as $invoiceDetail){
                        $invoice_no = $invoiceDetail->getInvoiceNo();
                        if(!empty($invoice_no)){
                            $invoiced_shipment = true;
                        }else{
                            $invoiceDetailUpdate = $invoiceDetail;
                        }
                    }
                }
                if($invoiced_shipment){
                    if (trim($remotearea) == 'YES') {
                        $consignmentCon->savelog($user->getFullname() . ' tried to change shipment as remote area, but its invoiced so not changed.', '');
                    }else{
                        $consignmentCon->savelog($user->getFullname() . ' tried to change shipment as non remote area, but its invoiced so not changed.', '');
                    }
                    $responceArray['STATUS'] = 'ERROR';
                    $responceArray['MESSAGE'] = 'This shipment is invoiced, you can not change it.';
                }else{
                    if (trim($remotearea) == 'YES') {
                        $remoteAreaUserMappingFilter = new RemoteareaUserMappingFilter();
                        $remoteAreaUserMapping = $remoteAreaUserMappingFilter->getRemoteCharges($account, $handling, $postCode, $country);
                        if (!empty($remoteAreaUserMapping)) {
                            $remoteAreaUserMapping = $remoteAreaUserMapping[0];
                            if($invoiceDetailUpdate != NULL) {
                                $invoiceDetailUpdate->setRemoteAreaCharge($remoteAreaUserMapping->getCharges());
                                $invoiceDetailUpdate->save();
                            }else{
                                $invoiceDetail = new InvoiceDetail();
                                $invoiceDetail->setConsignmentId($consignmentId);
                                $invoiceDetail->setRemoteAreaCharge($remoteAreaUserMapping->getCharges());
                                $invoiceDetail->save();
                            }
                            $consignmentCon->savelog($user->getFullname() . ' has changed shipment as remote area', '');
                            $responceArray['STATUS'] = 'SUCCESS';
                            $responceArray['MESSAGE'] = 'Your shipment has been successfully changed';
                        }else{
                            $consignmentCon->savelog($user->getFullname() . ' marked shipment as remote area but system could not found its remote area charges.', '');
                            $responceArray['STATUS'] = 'ERROR';
                            $responceArray['MESSAGE'] = 'Your shipment marked as remote area but we are unable to find remote area charges please put it manualy.';
                        }
                    }else{
                        if($invoiceDetailUpdate != NULL) {
                            $invoiceDetailUpdate->setRemoteAreaCharge(0);
                            $invoiceDetailUpdate->save();
                        }
                        $consignmentCon->savelog($user->getFullname() . ' has changed shipment as non remote area', '');
                        $responceArray['STATUS'] = 'SUCCESS';
                        $responceArray['MESSAGE'] = 'Your shipment has been successfully changed';
                    }
                    $consignmentCon->save();
                }
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }
            echo json_encode($responceArray);
            die;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'UPDATE_AGENT') {
            $responceArray = array();
            $consignmentId = $_POST['id'];
            $agentcode = $_POST['agentcode'];
            $consignmentCon = new Consignment($consignmentId);
            if (count($consignmentCon) > 0) {
                $consignmentCon->setAgentId($agentcode);
                $consignmentCon->savelog($user->getFullname() . ' has changed shipment agent Code to' . $agentcode, '');
                $consignmentCon->save();

                $responceArray['STATUS'] = 'SUCCESS';
                $responceArray['MESSAGE'] = 'Agent has been successfully changed';
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }
            echo json_encode($responceArray);
            die;
        }
        if (isset($_POST['action']) && $_POST['action'] == 'UPDATE_HAWB') {
            $responceArray = array();
            $consignmentId = $_POST['id'];
            echo $hawb = $_POST['hawb_new'];
            $consignmentCon = new Consignment($consignmentId);

            if (count($consignmentCon) > 0) {
                echo $old_hawb = $consignmentCon->getHawb();
                $consignmentCon->setHawb($hawb);
                $consignmentCon->savelog($user->getFullname() . ' has changed shipment HAWB from ' . $old_hawb . ' to ' . $hawb, '');
                $consignmentCon->save();

                $responceArray['STATUS'] = 'SUCCESS';
                $responceArray['MESSAGE'] = 'Hawb has been successfully changed';
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }
            echo json_encode($responceArray);
            die;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'UPDATE_TRACKING') {
            $responceArray = array();
            $consignmentId = $_POST['id'];
            $tracking = $_POST['tracking_new'];
            $consignmentCon = new Consignment($consignmentId);

            if (count($consignmentCon) > 0) {
                $old_awb = $consignmentCon->getAwb();
                $consignmentCon->setAwb($tracking);

                $parcels = $consignmentCon->getParcels();
//	echo count($parcels);
//	die;
                if (count($parcels) > 0) {
                    foreach ($parcels as $parcelsData) {
                        if (trim($parcelsData->getTrackingNumber()) == trim($old_awb)) {
                            $parcelsData->setTrackingNumber($tracking);
                            $parcelsData->save();
                        }
                    }
                }
                $consignmentCon->savelog($user->getFullname() . ' has changed shipment AWB from ' . $old_awb . ' to ' . $tracking, '');
                $consignmentCon->save();

                $responceArray['STATUS'] = 'SUCCESS';
                $responceArray['MESSAGE'] = 'Tracking Number has been successfully changed';
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }
            echo json_encode($responceArray);
            die;
        }



        if (isset($_POST['action']) && $_POST['action'] == 'UPDATE_AWB') {
            $responceArray = array();
            $consignmentId = $_POST['id'];
            $awb = $_POST['awb_new'];
            $consignmentCon = new Consignment($consignmentId);

            if (count($consignmentCon) > 0) {
                $old_awb = $consignmentCon->getAwb();
                $consignmentCon->setAwb($awb);
                $consignmentCon->savelog($user->getFullname() . ' has changed shipment HAWB from ' . $old_awb . ' to ' . $awb, '');
                $consignmentCon->save();

                $responceArray['STATUS'] = 'SUCCESS';
                $responceArray['MESSAGE'] = 'Hawb has been successfully changed';
            } else {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your shipment is not found.';
            }
            echo json_encode($responceArray);
            die;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'UPDATE_UPDATE_ACCOUNT') {
            $responceArray = array();

            $consignmentId = $_POST['id'];
            $newAccount = $_POST['newAccount'];
            $oldAccount = $_POST['oldAccount'];
            if (trim($newAccount) == '') {
                $responceArray['STATUS'] = 'ERROR';
                $responceArray['MESSAGE'] = 'Your new account is blank. Please try again';
            } else {

               $userCodeAccount =   new UserAccountFilter();
               $userCodeAccount->addFilter(" user_account = '".$newAccount."' and user_code <> 0 "); 
               $userCodeAccountData =   $userCodeAccount->getColumnList('user_account, user_code');
               if(count($userCodeAccountData)>0)
               {
                $consignmentCon = new Consignment($consignmentId);
                if (count($consignmentCon) > 0) {
                    $consignmentCon->setAccount($newAccount);
                    $consignmentCon->setUserCode($userCodeAccountData[0]->getUserCode());
                    $consignmentCon->savelog($user->getFullname() . ' has changed account ' . $oldAccount . ' to  ' . $newAccount, '');
                    $consignmentCon->save();

                    $responceArray['STATUS'] = 'SUCCESS';
                    $responceArray['MESSAGE'] = 'Your account has been successfully changed';
                } else {
                    $responceArray['STATUS'] = 'ERROR';
                    $responceArray['MESSAGE'] = 'Your shipment is not found.';
                }
               }
               else
               {
                    $responceArray['STATUS'] = 'ERROR';
                      $responceArray['MESSAGE'] = 'System does not find account number related to user account $newAccount. Please Contact to IT.';
               }
            }
            echo json_encode($responceArray);
            die;
        }


        if (isset($_POST['action']) && $_POST['action'] == 'SAVE_BILLING_HOLD_STATUS') {
            $consignmentId = $_POST['id'];
            $consignmentHold = $_POST['billing_hold'];
            $consignmentCon = new ConsignmentFilter();
            $consignmentCon->addFieldFilter('id', $consignmentId);
            $conList = $consignmentCon->getList(' * ');
            if (count($conList) > 0) {
                $consignmentConData = $conList[0];
//print_r($consignmentCon);
                $consignmentConData->setBillingHold($consignmentHold);
                $consignmentConData->save();
            }
            die;
        }
        if (isset($_POST['action']) && $_POST['action'] == 'GET_USER_DETAILS') {
            $accountDetails = array();
            $account = $_POST['account'];
            $filter = new UserAccountFilter();
            $filter->addUserAccountFilter($account);
            $filter->AddOrderByAccount();
            $userList = $filter->getColumnList("id, user_account, full_name, company, return_address, email, phone, logo, owner, billing_address, alternative_email, country");
            if (count($userList) > 0) {
                $userdata = $userList[0];
                $accountDetails['fullname'] = $userdata->getFirstName();
                $accountDetails['useraccount'] = $userdata->getUserAccount();
                $accountDetails['company'] = $userdata->getCompany();
                $accountDetails['returnaddress'] = $userdata->getReturnAddress();
                $accountDetails['email'] = $userdata->getEmail();
                $accountDetails['telephone'] = $userdata->getPhone();
                $accountDetails['logo'] = $userdata->getLogo();
                $accountDetails['billingaddress'] = $userdata->getBillingAddress();
                $accountDetails['alternativeemail'] = $userdata->getAlternativeEmail();
                $accountDetails['country'] = $userdata->getCountry();
            }
            echo json_encode($accountDetails);
            exit;
        }
        if (isset($_POST['func']) && $_POST['func'] == 'update_pod_status') {

            $emailPODStatus = "";
            $msg = "Status Updated successfully.";

            $tracking_id = $_POST["tracking_id"];
            $consignment_id = $_POST["consignment_id"];
            $tracking_number = $_POST["tracking_number"];
            $pod_status = $_POST["pod_status"];
            $pod_name = $_POST["pod_name"];
            $pod_date = $_POST["pod_date"];

            $pod_description = $_POST["pod_description"];
            $send_pod_status_mail = $_POST["send_pod_status_mail"];

            $pod_status = $pod_status == 'booked' ? 'In Transit' : $pod_status;

            if ($tracking_id != '') {
                $TrackingDataFilter = new TrackingDataFilter();
                $TrackingDataFilter->addIdFilter($tracking_id);
                $t_list = $TrackingDataFilter->getList();
                if (count($t_list) > 0)
                    $trackDataHistoryObj = $t_list[0];
                else
                    $trackDataHistoryObj = new TrackingData();
            } else
                $trackDataHistoryObj = new TrackingData();


            $trackDataHistoryObj->setConsignmentId($consignment_id);
            $trackDataHistoryObj->setTrackingNumber($tracking_number);
            $trackDataHistoryObj->setStatusCode($pod_status);
            $trackDataHistoryObj->setTrackPoint($pod_status);
            $trackDataHistoryObj->setDescription($pod_description);
            $trackDataHistoryObj->setDateCreated(date("Y-m-d h:i:s"));
            $trackDataHistoryObj->setAccount($_SESSION['admin']['user_name']);
            if ($pod_date == "")
                $pod_date = date("Y-m-d H:i");
            $trackDataHistoryObj->setPodDate(strtotime($pod_date . ":00"));
            if ($pod_status == 'delivered') {
                $emailPODStatus = 'Delivered';
                $trackDataHistoryObj->setSignature($pod_name);

                $imageName = "";
                if (isset($_FILES['pod_file']) && !empty($_FILES['pod_file']['name'])) {
                    $sourcePath = $_FILES['pod_file']['tmp_name'];
                    $imageName = $consignment_id . "_" . $_FILES['pod_file']['name'];
                    $targetPath = "../pod_images/" . $imageName;
                    if (move_uploaded_file($sourcePath, $targetPath)) {
                        $trackDataHistoryObj->setPodImage($imageName);
                    } else {
                        $msg .= "POD image uploading fails.";
                    }
                }
                $consignmentObj = new Consignment($consignment_id);
                $consignmentObj->setStatus($pod_status);
                $consignmentObj->save();
            }

            $trackDataHistoryObj->save();
            $parcelFilterdata = new ParcelFilter();
            $parcelFilterdata->addConsignmentIdFilterNew($consignment_id);
            $parcelList = $parcelFilterdata->getList();
            if (count($parcelList) > 0) {
                foreach ($parcelList as $plist) {
                    $plist->setCourierStatus($pod_status);
                    $plist->save();
                }
            }

            if ($send_pod_status_mail == 1) {
//send email to customer                            
                $emialContent = 'Dear Customer,<br /><br />Your shipment status details<br /><br />';
                $emialContent .= $pod_description . '<br /><br />';
                $emialContent .= '<table border="0" cellspacing="1" cellpadding="0" width="850" style="width:637.5pt">
                                        <tbody>
                                            <tr>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Hawb No</span></b></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Date of Hawb</span></b></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Your Ref No .</span></b></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Destination</span></b></p>
                                                </td>
                                                <td width="50" valign="top" style="width:37.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Pieces</span></b></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Customer Specified Weight (Kg)</span></b></p>
                                                </td>
                                                <td width="110" valign="top" style="width:82.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Description</span></b></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Value</span></b></p>
                                                </td>
                                                <td width="150" valign="top" style="width:112.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><b><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:maroon">Pod Status</span></b></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="9" style="padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <div class="MsoNormal" align="center" style="text-align:center">
                                                        <hr size="2" width="100%" align="center">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getAWB() . '</span></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getDateBooked() . '</span></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getReference() . '</span></p>
                                                </td>
                                                <td width="100" valign="top" style="width:75.0pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal">
                                                        <span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">
                                                            ' . $consignmentObj->getAddressLine1() . ($consignmentObj->getAddressLine2() != '' ? '<br />' . $consignmentObj->getAddressLine2() : '') . ($consignmentObj->getAddressLine3() != '' ? '<br />' . $consignmentObj->getAddressLine3() : '') . '<br />
                                                            ' . $consignmentObj->getCity() . '<br />' . $consignmentObj->getPostcode() . ' ' . $consignmentObj->getCountry() . '
                                                        </span>
                                                    </p>
                                                </td>
                                                <td width="50" valign="top" style="width:37.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getNumberPieces() . '</span></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getWeight() . '</span></p>
                                                </td>
                                                <td width="110" valign="top" style="width:82.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getDescription() . '</span></p>
                                                </td>
                                                <td width="70" valign="top" style="width:52.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $consignmentObj->getCurrency() . ' ' . $consignmentObj->getValue() . '</span></p>
                                                </td>
                                                <td width="150" valign="top" style="width:112.5pt;padding:1.5pt 1.5pt 1.5pt 1.5pt">
                                                    <p class="MsoNormal"><span style="font-size:9.0pt;font-family:&quot;Verdana&quot;,sans-serif;color:black">' . $emailPODStatus . '</span></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>';
                $emialContent .= '<br /><br />Thanks and regards,<br />One World Express';
                $email = $consignmentObj->getEmail();
                if ($email != "") {
                    $userObj = new UserAccountFilter();
                    $userObj->addUserNameFilter($consignmentObj->getAccount());
                    $users = $userObj->getColumnList('email,full_name');
                    if (count($users) > 0) {
                        $user = $users[0];
                        $email = $user->getEmail();
                    }
                }
                if ($email != "") {
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= 'From: One World Express <cs@oneworldexpress.com>' . "\r\n";
                    mail($email, "One World Shipping Status for HAWB No " . $consignmentObj->getHawb(), $emialContent, $headers);
                }
            }

            $ConsignmentLog = new ConsignmentLog();
            $ConsignmentLog->createlog("Update Status to " . $pod_status, $consignment_id);
            echo $msg;
            exit;
        }

        if (isset($_POST['func']) && $_POST['func'] == 'get_consignment_history') {
            $output = "";
            $consignment_id = $_POST['consignment_id'];
            $trackDataFilterObj = new TrackingDataFilter();
            $trackDataFilterObj->addConsignmentIDFilter($consignment_id);
            $trackingHistoryArr = $trackDataFilterObj->getColumnList('id, date_created,track_point,description,pod_date');
            if (count($trackingHistoryArr) > 0) {
                foreach ($trackingHistoryArr as $trackingHistory) {
                    $tracking_id = $trackingHistory->getId();
					$pod_date                =	date('Y-m-d H:i', $trackingHistory->getPodDate());
                    echo $pod_date = $trackingHistory->getPodDate();
					
                  $showdate = 	date('Y-m-d H:i', strtotime($trackingHistory->getDateCreated())); 
				if($pod_date != '' && $pod_date != '1970-01-01 01:00' && $pod_date != '1970-01-01 00:00' && 
				   $pod_date != '0000-00-00 00:00:00' && $trackingHistory->getPodDate() > 0)
					{
                        echo $showdate = date("Y-m-d H:i",$pod_date);
                    }
					
                    $output .= '<tr>';
                    $output .= '<td><a title="Edit" href="#" onClick="edit_pod(' . $tracking_id . ')" style="width:40px;"><span class="glyphicon glyphicon-pencil"></span></a></td> ';
                    $output .= '<td>' . $showdate . '</td>';
                    $output .= '<td>' . ucwords(strtolower($trackingHistory->getTrackPoint())) . '</td>';
                    $output .= '<td>' . $trackingHistory->getDescription() . '</td>';
                    $output .= '<td></td>';
                    $output .= '</tr>';
                }
            } else {
                $output .= '<tr>';
                $output .= '<td colspan="4">No History Data Found.</td>';
                $output .= '</tr>';
            }
            echo $output;
            exit;
        }

        /* ------------------------------------------------------------------------------ */
//get Audit Log
        if (isset($_POST['func']) && $_POST['func'] == 'get_audit_log') {
            $output = "";
            $consignment_id = $_POST['consignment_id'];


            $ConsignmentLogFilter = new ConsignmentLogFilter();
            $consignment_log = $ConsignmentLogFilter->getAuditLog($consignment_id);

            if (count($consignment_log) > 0) {
                foreach ($consignment_log as $clog) {
                    $output .= '<tr>';
                    $output .= '<td>' . $clog->getMessage() . '</td>';
                    $output .= '<td>' . strtoupper($clog->getIpaddress()) . '</td>';
                    $output .= '<td>' . formatDateTime(date("Y-m-d H:i:s", $clog->getLogDate())) . '</td>';
                    $output .= '</tr>';
                }
            } else {
                $output .= '<tr>';
                $output .= '<td colspan="4">No History Data Found.</td>';
                $output .= '</tr>';
            }
            echo $output;
            exit;
        }
//get Price Audit Log
        if (isset($_POST['func']) && $_POST['func'] == 'get_price_audit_log') {
            $output = "";
            $consignment_id = $_POST['consignment_id'];
            $InvoiceDetailFilter = new InvoiceDetailFilter();
            $InvoiceDetailFilter->addFieldFilter("consignment_id",$consignment_id);
            $InvoiceDetailData = $InvoiceDetailFilter->getColumnList("id");
            $InvoiceDetailData = $InvoiceDetailData[0];
            $InvoiceDetailID = $InvoiceDetailData->getId();
            $InvoiceDetailLogFilter = new InvoiceDetailLogFilter();
            $InvoiceDetail_log = $InvoiceDetailLogFilter->getAuditLog($InvoiceDetailID);
            if (count($InvoiceDetail_log) > 0) {
                foreach ($InvoiceDetail_log as $invoiceLog) {
                    $output .= '<tr>';
                    $output .= '<td>' . $invoiceLog->getMessage() . '</td>';
                    $output .= '<td>' . strtoupper($invoiceLog->getIpaddress()) . '</td>';
                    $output .= '<td>' . date("Y-m-d H:i:s", $invoiceLog->getLogDate()) . '</td>';
                    $output .= '<td class="details_view" style="cursor: pointer;" data-log-id="'.$invoiceLog->getId().'">Detail</td>';
                    $output .= '</tr>';
                }
            } else {
                $output .= '<tr>';
                $output .= '<td colspan="4">No History Data Found.</td>';
                $output .= '</tr>';
            }
            echo $output;
            exit;
        }
        if (isset($_POST['func']) && $_POST['func'] == 'get_price_audit_log_detail') {
            $log_id = "";
            $log_id = $_POST['log_id'];
            $InvoiceDetailLogData = new InvoiceDetailLog($log_id);
            $previous_data = unserialize($InvoiceDetailLogData->getPreviousData());
            $current_data = unserialize($InvoiceDetailLogData->getCurrentData());
            $output = "";
            $output .= '<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>Column Name</th>
								<th>Previous value</th>
								<th></th>
								<th>New Value</th>
							</tr>
						</thead>';
			$output .= $this->makeHtml($previous_data, $current_data);
			$output .= '</table>';
            echo $output;
            exit();
        }
        if (isset($_POST['func']) && $_POST['func'] == 'GetAncillaryChargesDetails') {
            $DetailsId = $_POST['DetailsId'];
            $InvoiceDetailLogData = new InvoiceDetailLog($DetailsId);
            $previous_data = unserialize($InvoiceDetailLogData->getPreviousData());
            $current_data = unserialize($InvoiceDetailLogData->getCurrentData());
            
            $oldData = json_decode($previous_data->getAncillaryChargesDetails());
            $NewData = json_decode($current_data->getAncillaryChargesDetails());
             $output = "";
            $output .= '<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>Column Name</th>
								<th>Previous value</th>
								<th></th>
								<th>New Value</th>
							</tr>
						</thead>';
			$output .= $this->makeHtmlAncillaryChargesDetails($oldData, $NewData);
			$output .= '</table>';
            echo $output;
           die; 
        }
        /* ------------------------------------------------------------------------------ */
// get vars
        $action = (isset($_REQUEST['action']) ? strip_tags($_REQUEST['action']) : '');
        $this->id = (isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : '');

        /* ------------------------------------------------------------------------------ */
// process form


        if (isset($_POST['btn_back'])) {
			if ($this->userSession->getUserType() == User::USER_TYPE_CUSTOMER_SERVICE)
				util_redirect("cs_bookings.php");
			else
	            util_redirect("bookings.php");
        }

        /* ------------------------------------------------------------------------------ */
// get customer
        $consignment = new Consignment($this->id);
        $this->tracking_number = $consignment->getAwb();
        $this->consignment_status = $consignment->getConsignmentStatus();

        /* $this->customerName = $customer->getFullname();
          $this->active = ($customer->getActive() == 0) ? "" : "Checked";
          // Set the title
          $this->setTitle("Admin - Customer details - " . $this->customerName);
         */
    }

    public function renderFooter() {
        ?>
          <link rel="stylesheet" href="../_assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-timepicker.min.css" />
        <script type="text/javascript" src="../_assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
        

        <script type="text/javascript">


            function updateExtra(extra_type) {
                var extras_total = 0;
                $("input[name='" + extra_type + "_extra_total[]']").each(function() {
                    extras_total += parseFloat($(this).val());
                });
                if (extra_type == 'agent') {
                    $("#agent_extra").val(extras_total);
                } else if (extra_type == 'customer') {
                    $("#extra").val(extras_total);
                }
                updateInvoiceTotal();
            }

            function loadConsignmentHistory(consignment_id) {
                $.post('booking_view.php', {func: 'get_consignment_history', consignment_id: consignment_id}, function(data) {
                    $("#history_content").html(data);
                });
            }
            function edit_pod(tracking_id)
            {

                $.post('ajaxbooking.php', {action: 'EDIT_POD', tracking_id: tracking_id}, function(data) {
                    var obj = JSON.parse(data)

                    document.getElementById('tracking_id').value = obj.tracking_id;
                    document.getElementById('consignment_id').value = obj.consignmentid;
                    document.getElementById('tracking_number').value = obj.tracking_number;
                    if (obj.pod_status == 'delivered')
                    {
                        $("#track_point_container").hide();
                        $(".pod_component_container").show();
                    } else {
                        $(".pod_component_container").hide();
                        $("#track_point_container").show();
                    }
                    document.getElementById('pod_status').value = obj.pod_status;
                    document.getElementById('pod_status').value = obj.TrackPoint;
                    document.getElementById('pod_description').value = obj.Description;
                    document.getElementById('pod_name').value = obj.Signature;
                    document.getElementById('pod_date').value = obj.PodDate;


                });


            }
            $(document).ready(function() {
                $("#btn_edit_extra_charge").hide();
                loadConsignmentHistory('<?php echo $this->id; ?>');
                /* $("#agent_id").select2({
                 placeholder: "Select Agent",
                 allowClear: true
                 });*/
                $(".form_datetime").datetimepicker({
                    autoclose: true
                });
                $(".pod_component_container").hide();

                $("#pod_status").change(function() {
                    var pod_status = $(this).val();
                    if (pod_status == 'delivered') {
                        $("#track_point_container").hide();
                        $(".pod_component_container").show();
                    } else {
                        $(".pod_component_container").hide();
                        $("#track_point_container").show();
                    }
                });
                $(".extras").click(function() {
                    $(".extras_container").hide();
                    var extra_type = $(this).data('type');
                    $("#hidden_extra_type").val(extra_type);
                    var title = extra_type == 'agent' ? 'Agent' : 'Customer';
                    $("#" + extra_type + "_extras_container").show();
                    $("#pricing-extra").find(".modal-title").html(title + ' Extra Charges');
                    var invoice_no = $("#update_pricing_details_frm #invoice_no").val();

                    if (extra_type == 'agent') {
                        $("#pricing-extra #description").prop("disabled", false);
                        $("#pricing-extra #extra_total").prop("disabled", false);
                    } else if (invoice_no != "") {
                        $("#pricing-extra #description").prop("disabled", true);
                        $("#pricing-extra #extra_total").prop("disabled", true);
                    }
                    $("#pricing-extra").modal('show');
                });
                $("#btn_edit_extra_charge").click(function() {
                    alert("Edit;")
                });
                $("#btn_add_extra_charge").click(function() {
                    var extra_type = $("#hidden_extra_type").val();
                    var agent_id = $("#agent_id").val();
                    var agent_name = $("#agent_id option:selected").text();
                    var charge_type_id = $("#charge_type").val();
                    var charge_type_text = $("#charge_type option:selected").text();
                    var description = $.trim($("#description").val());
                    var extra_total = $.trim($("#extra_total").val());

                    var totals_extras = $("#" + extra_type + "_totals_extras").val();
                    totals_extras++;

                    var tr = '';
                    if (agent_id == '') {
                        alert("Select agent.");
                        $("#agent_id").focus();
                        return false;
                    }
                    if (charge_type_id == '') {
                        alert("Select charge type.");
                        $("#charge_type").focus();
                        return false;
                    }
                    if (extra_total == '') {
                        alert("Enter extra total.");
                        $("#extra_total").focus();
                        return false;
                    }

                    tr += '<tr id="' + extra_type + '_row_' + totals_extras + '">';
                    tr += '<td>' + agent_name + '</td>';
                    tr += '<td>' + charge_type_text + '</td>';
                    tr += '<td>' + description + '</td>';
                    tr += '<td>' + extra_total + '</td>';
                    tr += '<td width="115px;">';
                    tr += '<input type="hidden" id="' + extra_type + '_agent_id_' + totals_extras + '" name="' + extra_type + '_agent_id[]" value="' + agent_id + '" />';
                    tr += '<input type="hidden" id="' + extra_type + '_charge_type_id_' + totals_extras + '" name="' + extra_type + '_charge_type_id[]" value="' + charge_type_id + '" />';
                    tr += '<input type="hidden" id="' + extra_type + '_description_' + totals_extras + '" name="' + extra_type + '_description[]" value="' + description + '" />';
                    tr += '<input type="hidden" id="' + extra_type + '_extra_total_' + totals_extras + '" name="' + extra_type + '_extra_total[]" value="' + extra_total + '" />';
                    //tr += '<button type="button" class="btn btn-primary btn-sm update_extra" data-type="'+extra_type+'" data-row_num="'+totals_extras+'">&nbsp;<i class="fa fa-edit"></i> Edit&nbsp;</button>';
                    tr += '<button type="button" class="btn btn-danger btn-sm remove_extra" data-type="' + extra_type + '" data-row="' + extra_type + '_row_' + totals_extras + '">&nbsp;<i class="fa fa-trash-o"></i> Del&nbsp;</button>';
                    tr += '</td>';
                    tr += '</tr>';

                    $("#" + extra_type + "_extras_body").append(tr);

                    $("#agent_id").val('');
                    $("#charge_type").val('');
                    $("#description").val('');
                    $("#extra_total").val('');
                    //$("#agent_id").select2('val', '');
                    $("#agent_id").val('');

                    $("#" + extra_type + "_totals_extras").val(totals_extras);
                    updateExtra(extra_type);
                });
                $(document).on('click', '#AncillaryChargesDetailsId', function() {
                    $("#audit_content_details_ancillary").html("<p>Please Wait...</p>");
                   $('#audit-log-details-ancillary').modal('show');
                   var DetailsId=""; 
                     DetailsId = $("#AncillaryChargesDetailsTxt").val();
                     $.ajax({
                        url: 'booking_view.php',
                        data: {DetailsId:DetailsId,func:"GetAncillaryChargesDetails"},
                        type: 'post',
                        success: function(response) {
                            $("#audit_content_details_ancillary").html(response);
                        }
                    });
                     
                });
                $(document).on('click', '.update_extra', function() {
                    var row_num = $(this).data("row_num");
                    var extra_type = $(this).data("type");
                    var _agent_id = $("#" + extra_type + "_agent_id_" + row_num).val();
                    var _charge_type_id = $("#" + extra_type + "_charge_type_id_" + row_num).val();
                    var _description = $("#" + extra_type + "_description_" + row_num).val();
                    var _extra_total = $("#" + extra_type + "_extra_total_" + row_num).val();

                    $("#agent_id").val(_agent_id);
                    $("#charge_type").val(_charge_type_id);
                    $("#description").val(_description);
                    $("#extra_total").val(_extra_total);
                    //$("#agent_id").select2('val', _agent_id);
                    $("#agent_id").val(_agent_id);
                    $("#btn_add_extra_charge").hide();
                    $("#btn_edit_extra_charge").show();

                });
                $(document).on('click', '.remove_extra', function() {
                    var row = $(this).data("row");
                    var extra_type = $(this).data("type");
                    if (confirm("Are you sure you want to remove?")) {
                        $("#" + row).remove();
                        updateExtra(extra_type);
                    }
                });
                $("#update_pod_status_btn").click(function() {
                    if ($.trim($('#pod_description').val()) == "") {
                        alert("Description is required.");
                        $('#pod_description').focus();
                        return false;
                    }

                    var file_data = $('#pod_image').prop('files')[0];
                    var form_data = new FormData();
                    var send_pod_status_mail = $("#send_pod_status_mail").is(":checked") ? 1 : 0;
                    form_data.append('pod_file', file_data);
                    form_data.append('func', 'update_pod_status');
                    form_data.append('tracking_id', $('#tracking_id').val());
                    form_data.append('consignment_id', $('#consignment_id').val());
                    form_data.append('tracking_number', $('#tracking_number').val());
                    form_data.append('pod_status', $('#pod_status').val());
                    form_data.append('pod_name', $('#pod_name').val());
                    form_data.append('pod_date', $('#pod_date').val());
                    form_data.append('send_pod_status_mail', send_pod_status_mail);
                    form_data.append('pod_description', $('#pod_description').val());
                    $.ajax({
                        url: 'booking_view.php',
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function(response) {
                            if (response.length > 150) {
                                window.location = 'login.php';
                            } else {
                                $("#pod_status_msg").html(response);
                                $("#pod_status_msg").show();
                                $('#pod_name').val('');
                                $('#pod_date').val('');
                                var checkbox = $('#send_pod_status_mail').prop('checked', false);
                                $.uniform.update(checkbox);
                                $('#pod_description').val('');
                               // $(".fileinput").fileinput('clear');
                                loadConsignmentHistory($('#consignment_id').val());
                            }
                        }
                    });
                });
                var heights = $(".inside").map(function() {
                    return $(this).height();
                }).get(),
                        maxHeight = Math.max.apply(null, heights);

                $(".inside").height(maxHeight);
                /*
                 $('.portlet').draggable({
                 handle: ".portlet-title"
                 });*/
                /* $('.portlet').draggable({
                 handle: ".portlet-body"
                 });*/
                $(".customer_charges,.agent_charges, #pricing-invoice #discount").blur(function() {
                    updateInvoiceTotal();
                });
            });
        </script>

        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
