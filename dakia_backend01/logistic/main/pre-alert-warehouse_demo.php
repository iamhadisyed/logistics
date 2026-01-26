<?php
// get settings
require_once("../includes/settings/config.inc.php");
echo "<link type='text/css' href='../css/demo.css' rel='stylesheet' media='screen' />";
echo "<link type='text/css' href='../css/osx.css' rel='stylesheet' media='screen' />";

/***
 * Page for editing a user
 */
class Page extends BasePage
{
	/***
	* Controller logic
	*/
	protected function init()
	{		
		Sessionmanager::checkUserAccess(USER::PRIVILEGE_WAREHOUSE_LIST);
		// Tool bar
//		$toolbar = Toolbar::getItem();
	
        //$toolbar->showPrintOption($this->num_valid > 0);

//        $toolbar->showSearchOption();
//        $toolbar->showImportOption();
//        $toolbar->showLabelList();
//        $toolbar->showWarehousePage();
//        $toolbar->showAddConsignment();
//        $toolbar->showCSVOption();
//        $toolbar->showReleaseList();
//        $toolbar->showConsignmentList(true);
//        $toolbar->showSaveAddress();
//		$toolbar->showSearchOption();
//	    $toolbar->showAddUser();
//		$toolbar->showExportOption();
//		$toolbar->showEndOfDayOption();
//		$toolbar->showManifestList();
//		$toolbar->showLabelCreation();
//		$toolbar->showUserList();
		t_on(); // turn on trace for this page

		// is this form being posted back?
		
		if (isset($this->form_vars["form_action"]))
		{
			// take appropriate action
			switch ($this->form_vars["form_action"])
			{			
				default:
					$id = $this->form_vars["id"];
					$status = $this->form_vars["status_drop".$id];
					$flight_status = $this->form_vars["flight_status_drop".$id];
					$comments = $this->form_vars["commentstxt".$id];
					$clear = $this->form_vars["clear_drop".$id];
					$dateTime = $this->form_vars["dt".$id];
					$shipper  = $this->form_vars["shipper".$id];
					$mawb = $this->form_vars["mawbtxt".$id];
					$flightNumber = $this->form_vars["flightNumbertxt".$id];
					$pieces = $this->form_vars["piecestxt".$id];
					$weight = $this->form_vars["weighttxt".$id];
					$eta = $this->form_vars["eta".$id];
					$etd = $this->form_vars["etd".$id];
					$shed	= $this->form_vars["shedtxt".$id];
					//
					$preAlertFlr = new PreAlertFilter();
					$updateRow = $preAlertFlr->addIdFilter(@$id);
					
					if(count($updateRow)>0)
					{
						
						if($status=='ASSIGNED')
						{
						    $updateRow[0]->setCurrentStatus('COLLECTION IN PROGRESS');
							$status = 'M';
						}
						else
						if(trim($flight_status)!='')
							$updateRow[0]->setCurrentStatus(@$flight_status);
						
						
						if($status=='IN WAREHOUSE')
							$updateRow[0]->setDateTime(date('Y-m-d H:i'));	
						else
							$updateRow[0]->setDateTime(@$dateTime);
							
						if(trim($status)!='')
							$updateRow[0]->setStatus(@$status);	
						//if(trim($clear)!='')
						//	$updateRow[0]->setCleared(@$clear);						
						$updateRow[0]->setComments(@$comments);						
						$updateRow[0]->setAccount(@$shipper);
						$updateRow[0]->setMawb(@$mawb);
						$updateRow[0]->setFlightNumber(@$flightNumber);
						$updateRow[0]->setPieces(@$pieces);
						$updateRow[0]->setWeight(@$weight);
						$updateRow[0]->setEta(@$eta);
						$updateRow[0]->setEtd(@$etd);
						$updateRow[0]->setShed(@$shed);	
						//echo $shed; exit;					
						$updateRow[0]->save();
					}
					util_redirect ("../main/pre-alert-warehouse.php");
					break;
					
					case 'delete':
					$id = $this->form_vars["id"];
					if($id!='')
					{
						$preDelete = new PreAlert($id);
						$preDelete->setStatus('Recycled');
						$preDelete->save();
					}
					break;
					
					case 'cancel':
					util_redirect("http://oneworldexpress.co.uk/remote/main/pre-alert-warehouse.php");	
					break;
					
					
			}
		}
			// common initialisation for ths page
			$this->setTitle("Report");    
	}
/***
	 * Insert content in to HTML Head section
	 */
	protected function renderHead()
	{
	?>
   
	    <script src="../js/timepicker/jquery.js"></script>
        <script src="../js/timepicker/jquery.datetimepicker.js"></script>
    	<link rel="stylesheet" type="text/css" href="../js/timepicker/jquery.datetimepicker.css"/>
        
		<!--<script type='text/javascript' src='../js/jquery.js'></script>-->
		<script type='text/javascript' src='../js/jquery.simplemodal.js'></script>
        <script type='text/javascript' src='../js/osx.js'></script>
        <script type="text/javascript">
	    
		var id;
		              
	 
	$(document).ready(function(e) {       
	    $('.datd').click(function(){
				id = $(this).attr('id');
			}).datetimepicker({
			 	 format: "Y/m/d H:i",
				 showApplyButton : true ,
				 onClose: function(dp,input) {
					$("#id").val(id);	
					$("#dt"+id).val(input.val());
					//$('#bookingForm').submit();				
			    }
				});
				
				$('.etdc').click(function(){
				id = $(this).attr('id');
			}).datetimepicker({
			 	 format: "Y/m/d H:i",
				 showApplyButton : true ,
				 onClose: function(dp,input) {
					$("#id").val(id);	
					$("#etd"+id).val(input.val());
					//$('#bookingForm').submit();				
			    }
				});
				
				
				$('.etac').click(function(){
				id = $(this).attr('id');
			}).datetimepicker({
			 	 format: "Y/m/d H:i",
				 showApplyButton : true ,
				 onClose: function(dp,input) {
					$("#id").val(id);	
					$("#eta"+id).val(input.val());
					//$('#bookingForm').submit();				
			    }
				});
				
			
			
			$('#save_value').click(function(){
        var val = [];
        $(':checkbox:checked').each(function(i){
          val[i] = $(this).val();	
	//	  alert(val[i]);	  
        });
		
      var mawb_check = val.join("','");
	//  alert(mawb_check);
	  $.ajax ({
		  	  type: "POST",
			  url: "prealertajax.php",  // your php file name
			  data: { mawb_check:mawb_check, action:'ServicePopUp'}, 
			  success:function(data)
			  { 
			  $("#summary").html(data);		 // 		window.open('"../main/pre-alert-warehouse.php"','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');
			  }	
			});		
    	});
    }); 
	
	/*function showdropdown(id)
	{
	$('#status'+id).replaceWith('<td><select id="status_drop'+id+'" name ="status_drop'+id+'" style="height:45px; background-color:white; color:black; width:100px;"><option value="">Select a Status</option><option value="Not Assigned">Not Assigned</option><option value="In Warehouse">In Warehouse</option></select></td>');	
	
	$("#status_drop"+id).change(function() 
		{
		$("#id").val(id);
		$('#bookingForm').submit();
		});	
	 
	 $( "#status_drop"+id).mouseout(function()
	 {
		 $( "body").click(function()
		 {
		 	$('#bookingForm').submit();
		}); 
	 });
	}


	function flightstatusdropdown(id)
	{
	$('#flight'+id).replaceWith('<td><select id="flight_status_drop'+id+'" name ="flight_status_drop'+id+'" style="height:45px;  background-color:white; width:155px; color:black; width:100px;"><option value="">Select a Status</option><option value="In Transit">IN TRANSIT</option><option value="ARRIVED LHR">ARRIVED LHR</option><option value="UNDER CLEARANCE">UNDER CLEARANCE</option><option value="COLLECTION IN PROCESS">COLLECTION IN PROCESS</option></select></td>');	
	
	 $("#flight_status_drop"+id).change(function() 
		{
		$("#id").val(id);
		$('#bookingForm').submit();
		});	
	 
	 $("#flight_status_drop"+id).mouseout(function()
	 {
		 $( "body").click(function()
		 {
		 	$('#bookingForm').submit();
		}); 
	 });
	}
	
	
	function cleardropdown(id,value)
	{		
		
		$('#clear'+id).replaceWith('<td><select id="clear_drop'+id+'" name ="clear_drop'+id+'" style="height:45px; background-color:white; color:black; width:70px;"><option value="">Select a value</option><option value="Yes">YES</option><option value="No">NO</option></select></td>');	
	
	 $("#clear_drop"+id).change(function() 
		{
		$("#id").val(id);
		$('#bookingForm').submit();
		});	
	 
	 $("#clear_drop"+id).mouseout(function()
	 {
		 $( "body").click(function()
		 {
		 	$('#bookingForm').submit();
		}); 
	 });
	}
	
	function shipperText(id)
	{
		$('#shipper'+id).show();
		$('#shipper'+id).change(function()
		{
			$("#id").val(id);
		    $('#bookingForm').submit();
		});
		
		$('#shipper'+id).mouseout(function()
	 {
		 $( "body").click(function()
		 {
			$("#id").val(id); 
		 	$('#bookingForm').submit();
		}); 
	 });
		 
	}
	
	function mawbText(id,value)
	{	
	$('#mawb'+id).replaceWith('<td><input value='+value+' type="text" id="mawbtxt'+id+'" name="mawbtxt'+id+'" style="height:30px; width:85px; color:black"/></td>');
		$("#mawbtxt"+id).focus();
		$("#mawbtxt"+id).change(function() 
		{
			$("#id").val(id);
			$('#bookingForm').submit();
		});	
		
		$("#mawbtxt"+id).blur(function() 
		{
			$("#id").val(id);
			$('#bookingForm').submit();
		});		
    }
	
	
	
	function flightNumberText(id,value)
	{
		$('#flightNum'+id).replaceWith('<td><input type="text" value='+value+' id="flightNumbertxt'+id+'" name="flightNumbertxt'+id+'" style="height:30px; width:85px; color:black"/></td>');
		$('#flightNumbertxt'+id).focus();
		$("#flightNumbertxt"+id).change(function() 
		{
		$("#id").val(id);
		$('#bookingForm').submit();
		});			
		
		$("#flightNumbertxt"+id).blur(function() 
		{
			$("#id").val(id);
			$('#bookingForm').submit();
		});
	}
	
	function piecesText(id,value)
	{
		$('#pieces'+id).replaceWith('<td><input type="text" value='+value+' id="piecestxt'+id+'" name="piecestxt'+id+'" style="height:30px; width:85px; color:black"/></td>');
		$('#piecestxt'+id).focus();
		$("#piecestxt"+id).change(function() 
		{
		$("#id").val(id);
		$('#bookingForm').submit();
		});	
		
		$("#piecestxt"+id).blur(function() 
		{
			$("#id").val(id);
			$('#bookingForm').submit();
		});		
	}
	
	function weightText(id,value)
	{
		$('#weight'+id).replaceWith('<td><input type="text" value='+value+' id="weighttxt'+id+'" name="weighttxt'+id+'" style="height:30px; width:85px; color:black"/></td>');
		$('#weighttxt'+id).focus();
		$("#weighttxt"+id).change(function() 
		{
		$("#id").val(id);
		$('#bookingForm').submit();
		});		
		
		$("#weighttxt"+id).blur(function() 
		{
			$("#id").val(id);
			$('#bookingForm').submit();
		});
	}
	
	function commentText(id,value)
	{
		$('#comments'+id).replaceWith('<td><input type="text" value='+value+' id="commentstxt'+id+'" name="commentstxt'+id+'" style="height:30px; width:85px; color:black"/></td>');
		$('#commentstxt'+id).focus();
		$("#commentstxt"+id).change(function() 
		{
		$("#id").val(id);
		$('#bookingForm').submit();
		});		
		
		$("#commentstxt"+id).blur(function() 
		{
			$("#id").val(id);
			$('#bookingForm').submit();
		});
	}*/
	
	var pre_id = 0;
	function UpdateAll(id,mawb,flightNumber,pieces,weight,comments,account, flightStatus, oweStatus,shed)
	{
		$('#save'+id).show();
		$('#cancel'+id).show();
		$('#update'+id).hide();
		$('#delete'+id).hide();
		
		 $.ajax ({
			  type: "POST",
			  url: "prealertajax.php",  // your php file name
			  data: { account:account, id:id, action:'AccountPreAlert'}, 
			  success:function(data)
			  { 
				  $('#ship'+id).html(data);
			  }	
			  		  
			});	
		
		$.ajax ({
			  type: "POST",
			  url: "prealertajax.php",  // your php file name
			  data: { flightStatus:flightStatus, id:id, action:'CurrentStatus'}, 
			  success:function(data)
			  { 
				  $('#flight'+id).html(data);
			  }	
			  		  
			});
			
		$.ajax ({
			  type: "POST",
			  url: "prealertajax.php",  // your php file name
			  data: { oweStatus:oweStatus,id:id, action:'OWEStatus'}, 
			  success:function(data)
			  { 
				  $('#status'+id).html(data);
			  }	
			  		  
			});
			
/*		$.ajax ({
			  type: "POST",
			  url: "prealertajax.php",  // your php file name
			  data: { clear:clear, id:id, action:'Clear'}, 
			  success:function(data)
			  { 
				  $('#clear'+id).html(data);
			  }	
			  		  
			});
*/		
		$('#mawb'+id).replaceWith('<td><input value="'+mawb+'" type="text" id="mawbtxt'+id+'" name="mawbtxt'+id+'" style="height:15px; width:70px; color:black"/></td>');
		
		$('#flightNum'+id).replaceWith('<td><input type="text" value="'+flightNumber+'" id="flightNumbertxt'+id+'" name="flightNumbertxt'+id+'" style="height:15px; width:70px; color:black"/></td>');
		
		$('#pieces'+id).replaceWith('<td><input type="text" value="'+pieces+'" id="piecestxt'+id+'" name="piecestxt'+id+'" style="height:15px; width:70px; color:black"/></td>');
		
		$('#weight'+id).replaceWith('<td><input type="text" value="'+weight+'" id="weighttxt'+id+'" name="weighttxt'+id+'" style="height:15px; width:70px; color:black"/></td>');	
		
		$('#comments'+id).replaceWith('<td><input type="text" value="'+comments+'" id="commentstxt'+id+'" name="commentstxt'+id+'" style="height:15px; width:70px; color:black"/></td>');	
		
		$('#shed'+id).replaceWith('<td><input type="text" value="'+shed+'" id="shedtxt'+id+'" name="shedtxt'+id+'" style="height:15px; width:70px; color:black"/></td>');	
	    pre_id = id;			
		
	}
	
	function SaveAll(id)
	{
		$("#id").val(id);
			$('#bookingForm').submit();
	}
	
	function DeleteRow(id)
	{
		if(confirm("Are you sure you want to delete"))
		{
			$("#id").val(id);
			$("#form_action").val("delete");
			$('#bookingForm').submit();
		}
	}
	
	function Cancel(id)
	{
		//alert('12');
		$("#form_action").val("cancel");
		$('#bookingForm').submit();
	}
	
	function servicePopup(mawb)
	{
		$.ajax ({
			  type: "POST",
			  url: "prealertajax.php",  // your php file name
			  data: { mawb:mawb, action:'ServicePopUp'}, 
			  success:function(data)
			  { 
			  $("#summary").html(data);
		 // 		window.open('"../main/pre-alert-warehouse.php"','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');
			  }	
			});		
	}
	
	  
    </script>
	<?php
	}
	

	/***
	* Content View
	*/
	protected function renderBody()
	{
		
		// transfer form variables into local values (form variables come from parent)
		foreach ($this->form_vars as $key=>$val) {$$key = $val; }

		?>
    <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
         <div class="clear" ></div>
 		 <div class="main_formpage" >
    	  <style>
	   td
	   {
		   padding:3px;
		   font-weight:bold;
		   border: 1px solid black;
       }
	   
	   </style>
<div class="portlet box blue">
            <div class="portlet-title">
            	<div class="caption"> <i class="icon-bar-chart"></i>
				<?php errorList::getItem()->render(); ?> FLIGHT ARRIVALS</div>
         		<div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
	       		</div>
                
            <div class="portlet-body">
               <div class="row">
                   <div class="col-md-4">
                      <input type="button" id="save_value"  name='osx' value="Serv List" class='osx demo form-control'/> 
                   </div>
                       
               </div>  
         <div class="row">
             <div class="col-md-12" style="overflow: auto;">
            <label></label>
			
			<table class="table table-striped table-bordered table-advance table-hover" border=1 style="text-align:center; font-size:15px; color: black; margin-left:0px; ">
     <colgroup>
    	<col style="width: 4%" />
  	  	<col style="width: 5%" />
   	 	<col style="width: 4%" />
        <col style="width: 4%" />
        
        <col style="width: 2%" />
  	  	<col style="width: 2%" />
   	 	<col style="width: 4%" />
        <col style="width: 4%" />
  	  	<col style="width: 5%" />
   	 	<col style="width: 5%" />
 		<col style="width: 2%" /> 	  	
		<col style="width: 5%" />
   	 	<col style="width: 3%" />
        <col style="width: 2%" />
        
 	  </colgroup>
      <tr>
      <td>SHIPPER</td>
      <td>MAWB#</td>
      <td>FLIGHT#</td>
      <td>SERVICES</td>
      <td>NUMBER OF BAGS</td>
      <td>WEIGHT<br>(KG)</td>
      <td>ETD</td>
      <td>ETA</td>
      
      <td>CURRENT STATUS</td>
      <td>DATE & TIME</td>
      
   <!--   <td>CLEARED</td>-->
      <td>OWE STATUS</td>
      <td> SHED </td>
      <td>COMMENTS</td>
      <td>Edit</td>
      </tr>
      
      <?php
	  $pfilter = new PreAlertFilter();
	  $pVal = $pfilter->report(date('Y-m-d'));
	//  echo count($pVal);// exit;
	  
	  foreach($pVal as $val)
	  {
		$status 		= $val->getStatus();
		 if($status=='M')
		 {
			 $status = 'ASSIGNED';
		 }
		 $currentStatus = $val->getCurrentStatus();
		 
		 $mawb 			= $val->getMawb();
		 $flightNumber  = $val->getFlightNumber();
		 $pieces 		= $val->getPieces();
         $weight 		= $val->getWeight();
         $etd			= $val->getEtd();
         $eta			= $val->getEta();
		 $id			= $val->getId();
		 $dateTime 		= $val->getDateTime();
		 $shed			= $val->getShed();
		// echo $shed.'asdasd'; 
		 
		 //$con = new ConsignmentFilter();
		 //echo $mawb;
		 //$service_list = $con->getReportServiceList($mawb);
		 // print_r($service_list);// exit;
		 /*foreach($service_list as $ser_list)
		 {
		 	 $cleared .= ','.$ser_list;
		 }*/
		 //	echo $mawb;	
		 
		if(strtolower($val->getAccount()) == 'michelle')
			$preAccount = 'OWE China';
		else
			$preAccount = $val->getAccount();
		 	 
		// $cleared		= $val->getCleared();
		 $comments		= $val->getComments();
		 //$preAccount	= $val->getAccount();
		 
		 if(strtoupper($status)=="NOT ASSIGNED" && strtoupper($currentStatus)=="IN TRANSIT")
		 {
			 $color = "#FF3535";
			 $fcolor = "black";
		 }
		 else
		 if(strtoupper($status)=="IN WAREHOUSE")
		 {
			 $color = "#A3F46C";
			 $fcolor = "black";
		 }
		 else
		 {
		 	$color = "#FFB164";
			$fcolor = "black";
		 }
		?>
        
        <?php
        //$user = new UserFilter();
		//$account = $user->getColumnList('user_account');
		
        ?>
        <tr style = "color:<?php echo $fcolor;?>; background:<?php echo $color;?>">
        
        <td id="ship<?php echo $id?>"><?php echo $preAccount; ?></td>
        
        <td id = "mawb<?php echo $id?>" ><? echo $mawb;?></td>        
		<td id = "flightNum<?php echo $id?>" ><? echo $flightNumber;?></td>  
    
      
      
        <?php /*?><td id = "services<?php echo $id?>"><input type="checkbox" name='osx' value="Serv List" class='osx demo' onclick="servicePopup(<?php echo "'".$mawb."'" ?>)"></td><?php */?>    
        <td><input name="selector[]" id="checkbox<?php echo $id?>" class="ads_Checkbox" type="checkbox" value=<?php echo $mawb ?> /></td>  
        <td id = "pieces<?php echo $id?>" ><? echo $pieces;?></td>
        <td id = "weight<?php echo $id?>" ><? echo $weight;?></td>
        

        <td style="color:#000000 !important;">
        <input type='button' class="etdc"  id="<?php echo $id?>" value='<? echo $etd;?>'  />
		<input type='hidden' id="etd<?php echo $id?>" name="etd<?php echo $id?>" 
        value="<?php echo $etd;?>" />
        </td>

        <td style="color:#000000 !important;">
        <input type='button' class="etac"  id="<?php echo $id?>" value='<? echo $eta;?>'  />
		<input type='hidden' id="eta<?php echo $id?>" name="eta<?php echo $id?>" 
        value="<?php echo $eta;?>" />
        </td>
        
        <td id = "flight<?php echo $id?>"><? echo $currentStatus;?></td>
        
        <td style="color: #000000 !important;">
        <input type='button' class="datd"  id="<?php echo $id?>" value='<? echo $dateTime;?>' style='width:120px'  />
		<input type='hidden' id="dt<?php echo $id?>" name="dt<?php echo $id?>" 
        value="<?php echo $dateTime;?>" />
		</td>
        
        
       <?php /*?> <td id="clear<?php echo $id?>"><? echo $cleared;?></td><?php */?>
        
        <td id="status<?php echo $id?>"><? echo $status;?></td>
        <td id="shed<?php echo $id?>"><? echo $shed;?></td>
        <td id ="comments<?php echo $id?>"><? echo $comments;?></td> 
       <!-- ,'".$cleared."' -->
       <td><a href="#" id="update<?php echo $id;?>" title="Edit" onclick="UpdateAll(<?php echo "'".$id."','".$mawb."','".$flightNumber."','".$pieces."','".$weight."','".$comments."','".$preAccount."','".$currentStatus."','".$status."','".$shed."'" ?>)" class="glyphicon glyphicon-pencil"></a>
        
           <a href="#" id="delete<?php echo $id;?>" title="Delete" onclick="DeleteRow(<?php echo "'".$id."'" ?>)" class="glyphicon glyphicon-trash">
        </a>
        
           <a href="#" hidden="hidden" id="save<?php echo $id;?>" title="Save" onclick="SaveAll(<?php echo "'".$id."'" ?>)" class="glyphicon glyphicon-floppy-saved">
        </a>
        
        <a href="#" hidden="hidden" id="cancel<?php echo $id;?>" title="Cancel" onclick="Cancel()" class="glyphicon glyphicon-remove">
        </a>
       
        </td>     
        </tr>
        		  
	  <? }
	  ?>
      </table>
<br /> <br />
             <table id="report" style="font-size:20px;  text-align:center; width:90%; margin-left: 25px;
                    margin-right: 0px; float:left;" cellspacing="12" border="1" class="table table-striped table-bordered table-advance table-hover"></table>
			<br class="clear" />	  
     </div>
	
    </div>
    <div class="row" style="text-align:center;"> 
			<input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
	 </div>
        </div>
		
    </div>
	</div>
	
        
        
        <div id="osx-modal-content">
			<div id="osx-modal-title">SERVICE REPORT</div>
			<div class="close"><a href="#" class="simplemodal-close">x</a></div>
			<div id="osx-modal-data">
				 <table id = "summary">
                <!--	<tr>
                    	<td><label id="error" style="color:red; font-size:12px; margin-left:150px;" ></label></td>
                    </tr>
                    <tr>
                    	<td>
                        	<label style="margin-left:150px;">Password:</label>
                        	<input type="password" id="psw" name="psw" style="margin-left:15px; height:20px; width:150px;"  value="<?php echo @$psw; ?>" />
                           
                        </td>
                    </tr>
                    <tr>
                    <td>
                   	<br /><button id="deletebtn" style="margin-left:220px;" name="deletebtn"  onClick="servicePopup(<?php echo "'".$mawb."'" ?>);">Get Report</button>
                                 	
                     </td>
                    </tr>-->
                </table>
				
			</div>
		</div>
       
		<?php
		//header("Refresh: 30;url='http://sandbox.oneworldexpress.co.uk/remote/main/pre-alert-report.php'");
        }}
        /**
	* Return to source page
	* @param $filter_set
	*/
	public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::COURIERS);
    	$menu->render();
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();