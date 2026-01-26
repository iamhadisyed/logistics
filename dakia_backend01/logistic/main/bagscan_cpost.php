<?php

//

// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage
{

	


	protected function init()
	{
		?>
			<script type="text/javascript" src="../js/json2.js"></script>
			<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
            <script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js">
            </script>


			<script type="text/javascript">	
	
	function getParameterByName(name) 
	{
		name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			results = regex.exec(location.search);
		return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	
	function checkKeyValue(e,tabnumber)
	{
		if (e.keyCode == 13) 
		{
			btnScanSingle();	
			return false;	
		}
				
		
	}
	
	$(document).ready(function()
	{
			// Set the date pickers
	        $('#pod_date').datepicker({dateFormat: "yy-mm-dd"});
	});



                 



	//$(document).ready(function() {
		
		function showTextBox()
		{
			var shipment_status = $('#status :selected').text();

			
			if(shipment_status == 'Delivered')
			{
				$("#trName").remove();
				var html = "<tr id='trName'><td><label style='font-size:20px'>Receiver Name Proof of Delivery</label></td><td><input id='name' name='name'                             style='width:337px;height:35px; font-size:24px' type='text' /></td></tr>";
									   
				$("#trAfter").after(html);
			}
			else if(shipment_status == 'On Hold')
			{
				$("#trName").remove();
				var html = "<tr id='trName'><td><label style='font-size:20px'>Hold Reason</label></td><td><input id='holdreason' name='holdreason' style='width:337px;height:35px; font-size:24px' type='text' /></td></tr>";
									   
				$("#trAfter").after(html);
			}
			else
			{
				$("#trName").remove();
			}
		}

		
		
		function btnScanSingle()
		{
			


			//if ( $("#box_number").val().length == 0 )
			//alert($("#box_number").val());
			if ($.trim($("#box_number").val()).length == 0 )
			{
				alert('Please scan tracking number.');
				$("#box_number").focus();
				return false;

			}		

			var messageDiv = $('#message');
			var box_number = $.trim($("#box_number").val());
			var shipment_status = $('#status :selected').text();
			var pod_name = $.trim($('#name').val());	
			
			var holdreason = $.trim($('#holdreason').val());	;
			
			
			var date = $.datepicker.formatDate("yy-mm-dd", $("#pod_date").datepicker("getDate"));
			
			//var date =  $("#pod_date").datepicker('getDate');
			
			var hours = $("#hours option:selected").val();
			var mins = $("#minute option:selected").val();
			
			
			
			
			var country = getParameterByName("country");			
			var account = getParameterByName("account");		
			
			
			
			$.ajax({
			type: "POST",
			url: "box_cpost_ajax.php",  // your php file name
			data: { action: 'single_shipment_scan',  boxnumber: box_number, 
					status : shipment_status, pod : pod_name, 
					country: country, account: account, 
					date:date, hours:hours, mins:mins, holdreason:holdreason },
			success:function(data)
			{

				 
				 var obj = JSON.parse(data);


				 if(obj.result === 'error')
				 {
				 	 //$('#box_number').select();
					 messageDiv.css('color','red');
					 messageDiv.show().html(obj.message);

					 //alert(obj.total);
					
					 //alert(obj.total);

					$("#box_number").css("background-color", "red");
					//var message = obj.message;
					//var message = message.split(' ').join('%2f');


					//$("#supplier_tracking_no").css("background-color", "red");

					setTimeout(function()
					{

						//messageDiv.hide().html('');
						$("#box_number").val("");
						$("#box_number").css("background-color", "white");
						$('#box_number').focus();

					}, 1000);

				 }
				 else
				 {
					 //$('#box_number').select();
					 messageDiv.css('color','green');									
					 messageDiv.show().html(obj.message);
					 $("#box_number").css("background-color", "green");
					



					setTimeout(function(){
					//messageDiv.hide().html('');
					//$('#box_number').val("");

					
					$('#box_number').css("background-color", "white");
					$('#box_number').val("");
					$('#box_number').focus();

					//$("#box_number").css("background-color", "white");


					}, 1000);

				 }



			  }

			});

			 return false;

		}//);

	</script>
    
    <?

		//Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
		//$user = SessionManager::getUser();

		if (isset($this->form_vars["form_action"]))
		{
			// take appropriate action
			switch ($this->form_vars["form_action"])
			{

				

			}
		}





		// common initialisation for ths page
		$this->setTitle("Box Scan");
		//$toolbar = Toolbar::getItem();

		// Check message
		//if ($this->table_msg == "") $this->table_msg = "";

	}

	/**
	* Head
	* - non-standard action buttons
	*/
	protected function renderHead()
	{
	?>

	<style type="text/css">

	.main_formpage h2
	{
		border-bottom:0px;
	}
	
	label
	{
		font-family:"Arial Black", Gadget, sans-serif;
		font-size:32px;
		font-weight:bold;
	}




	</style>

	


	<?php
	}

	/***
	* Content View
	*/

	
	private function GetPostOffices($countryname)
	{
		$host = YPS_HOST;
		$user = YPS_USER;
		$password = YPS_PASSWORD;
		$db = YPS_DB;	
		
		$postOfficeArr = array();
		
		$con = mysqli_connect($host, $user, $password);
		mysqli_select_db($con, $db);
		
		
		$query1 = "select distinct postoffice from country_post_office where isactive= 1 and isdeleted = 0 
				   and countryid = (select countryid from country where countryname = '$countryname')order by 			                   postoffice";
				   
				   
		$results = mysqli_query($con, $query1);
			
		while($data=mysqli_fetch_array($results))
		{
			$postOfficeArr[] = $data['postoffice'];				
		}
		
		return $postOfficeArr;
				   
				   
	}

	protected function renderBody()
	{
		
		$country = "CURACAO";
		$account = "CPOST";
		
		if(isset($_GET["country"]))
			$country = $_GET["country"];
		
		if(isset($_GET["account"]))
			$account = $_GET["account"];
			
			
		//$user = SessionManager::getUser();
		
		// transfer form variables into local values (form variables come from parent)
		//foreach ($this->form_vars as $key=>$val) {$$key = $val; }



		?>

	

        <div class="main_formpage">
       	<h1 class="heading"></h1>
        <div class="form_container">
        <div class="gray_container">

	    <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        
	    <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        
        
	  

        
                   <table>
                   <tr>
                   <td>
                   </td>
                   <td>
                   		
                   </td>
                   </tr>
                   
                   <tr>
                   <td>
                   	&nbsp;&nbsp;&nbsp;
                   </td>
                   </tr>
                   
                   <tr>
                   <td>
                   </td>
                   <td align="center">                    	
                        <h1>YPS SCANNING</h1>
                          <br />
                          <br />
                          <br />
                          <br />
                          <br />
                          <br />
                    </td>
                   </tr>
                   
				   <tr>
				   <td>
                   </td>
				   <td align="center" colspan="4">
				   
				   </td>
				   </tr>
                   
				   <tr>
				   <td>
				   </td>
				   <td colspan="4">
				   	<h1><span id="message"></span></h1>
				   </td>
				   </tr>
                  
                  <tr>
                  <td>
                  <label>Status :</label>
                  </td>                 
                  <td>                  
                  <select id='status' onchange="showTextBox();" name='status' class="form-control">
                  <option value="Received in <? echo $country ?>">Received in <? echo $country ?></option>
                  <? 
					  $postOfficeArray = $this->GetPostOffices($country);
					  //echo "<pre>";
					  //print_r($postOfficeArray);
					  if(count($postOfficeArray) > 0)
					  {
						  foreach($postOfficeArray as $postOffice)
						  {
							  echo "<option value=Received at ".$postOffice.">Received at ".$postOffice."</option>";
						  }
					  }
				  
				  ?>
                  <option value="Out for Delivery">Out for Delivery</option>
                  <option value="Delivered">Delivered</option>
                  <!--<option value="POD">POD</option>-->
                  <option value="On Hold">On Hold</option>
                  </select>
                  </td>
                  </tr>                  
                  <tr><td><label style="font-size:20px">POD Date : (Optional)</label></td>
				<td> <input placeholder="" type="text" name="pod_date" id="pod_date" 
                
                style="background-color:#FFC; width:230px;height:30px; float:left;" 
                class="form_field_col1" readonly 
                />
                </td>
                </tr>
                <tr>
                <td><label style="font-size:20px">Time: (Optional)</label></td>
                <td><select name="hours" id="hours">
                <?php
					for($i=0; $i<=24; $i++){
						
						if($i<='9')
						{
							echo '<option value=0' .$i . '>' . '0' .$i . '</option>';
						}
						else{
						echo '<option value=' . $i . '>' .$i . '</option>';
						}
					}
				?>

               		</select> <select name="minute" id="minute" >
                <?php
					for($j=0; $j<=60; $j++)
					{
						if($j<='9')
						{
							echo '<option value=0' .$j . '>' . '0' .$j . '</option>';
						}
						else
						{
							echo '<option value=' . $j . '>'  .$j . '</option>';
						}
					}
				?>

               		</select></td></tr>
   				
                  
                  <tr id="trAfter">
                  <td>&nbsp;                  	
                  	
                   
                  </td>
                  </tr>                                  
                  
                  
                  <tr>
                   	<td>&nbsp;
                    	
                    </td>
                  </tr>
                  <tr>
                   	<td>&nbsp;
                    	
                    </td>
                  </tr>
                  <tr>
                   	<td>&nbsp;
                    	
                    </td>
                  </tr>                 
                  
                  <tr>
                  
                  <td><label>Scan Bag/Shipment Tag :</label></td><td>
                      <input type="text" name="box_number" style="width:500px;height:100px;font-size:90px; background-color:#FFC;" id="box_number" value="" class="standard"  onkeypress="return checkKeyValue(event, 1);" />
                  </td>
                  
                  <tr>

                     <td colspan=2 align=center>
                        <br /><br />
                        <input type="submit" id="btnScanOrd" name="btnScanOrd" style="width:100px; display:none;"   value="scan" >

                           </td>
                           </tr>


                    </table>
                    <br />
                    <br />
                    
                </div>                

        </div>
        
		<?php
	}
	
	public function renderMenu() 
	{
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
	}

} // class

/*------------------------------------------------------------------------------*/
// create and render page
$page = new Page("noheader");
$page->show();