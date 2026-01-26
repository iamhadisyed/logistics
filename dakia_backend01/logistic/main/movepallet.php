<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings


require_once("../includes/settings/config.inc.php");


// set up local page class
class Page extends BasePage {

	private $id;
	private $name;
	private $action;
	private $location;
    /*     * *
     * Set the page header
     * @return void
     */

    public function getTitle() {
        return "Admin - Index";
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
		
		
		
		?>			
				
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->

        <div class="row">
            <div class="col-md-12">
                <!--<h3 class="page-title">Create Barcode</h3>-->
            </div>
           <!-- <div class="col-md-4"> <a href="reporting_services_fullscreen.php" target="_blank" class="btn btn-success pull-right">Show Full Screen</a> </div>-->
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Add Warehouse Location</div>
                
                <div class="tools"> <a href="javascript:;" class="collapse"> </a> 
                <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> 
                </div>
            </div>
			           
            <div class="portlet-body" style="padding-top:50px">
                <div class="scroller" data-rail-color="blue" data-handle-color="blue">                
                <div id="msg" style="padding-left:750px; display:none" class="alert alert-danger">

                 </div>
                    
                   
                    <form method="post">   
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                               <label style="font-size:25px;color:#000065; padding-left:395px; width:345">Select Action:</label>                             
                            </div>
                        </div>                        
                        <div class="col-md-6">
                            <div class="form-group">
                            <select id="action" name="action" style="font-size:25px;height:50px;width:354px !important;">
                            	<option value="--- Please Select ---">--- Please Select ---</option>
                                <option value="Movement">Movement</option>
                                <option value="Returns">Returns</option>
                                <option value="Dispatch">Dispatch</option>
                            </select>
                            </div>
                        </div>          
                    
                    </div>
                    <div id="divLocation" style="display:none" class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                               <label style="font-size:25px;color:#000065; padding-left:395px; width:345">Select Location:</label> 
                            
                            </div>
                        </div>                        
                        <div class="col-md-6">      
                        	<div class="form-group">                      
                            <? $this->locationDropdown($_POST['action']) ?>
                            </div>
                        </div>
                    </div>
                    <div id="divPallet" class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                               <label style="font-size:25px;color:#000065; padding-left:400px; width:345">Pallet Number / Tracking Number:</label> 
                            
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <input style="font-size:25px;height:50px;width:311px" maxlength="50" class="form-control" id="pallet_number" name="pallet_number" placeholder="Pallet/Bag/York" type="text" value="">
                        </div>
                    </div>    
                  </div>
                  <div id="divVehicle" class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                               <label style="font-size:25px;color:#000065; padding-left:400px; width:345">Vehicle Number:</label> 
                            
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <input style="font-size:25px;height:50px;width:311px" maxlength="50" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="Vehicle number" type="text" value="">
                        </div>
                    </div>    
                  </div>      
                      <div id="divSave" class="row">
                      		<div class="col-md-6">

                            </div>
                            <div class="col-md-6">
                            <input type="hidden" name="form_action" id="form_action"  />                         
                            <input type="button" style="width:350px; height:50px" name="btnSaveLoc" id="btnSaveLoc" type="submit" class="btn btn-primary" value="Save" />
                            <i style="display:none;" id="loader" class="fa fa-spinner fa-spin icon-large"></i>
                     
                              

                           		
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


                            </div>
                        </div>
                    
                </div>
                	</form>
            </div>
			<!--
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-users"></i>Result </div>
                    <div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
                </div>
                <div class="portlet-body">
                    <div class="scroller" style="min-height:200px;"  data-rail-color="blue" data-handle-color="blue" id="rpt_container">
                    	
                    
                          

                    </div>
                    </div>
                </div>
            </div>
			-->
            <!-- END PAGE CONTENT-->
                                    <?php
                                }

                                /**
                                 * Override to show the menu
                                 *
                                 */
                                public function renderMenu() {
                                    $menu = new Adminmenu(Adminmenu::DASHBOARD);
                                    $menu->render();
                                }

                                public function renderHead() 
								{
									 ?>
                                     <script type="text/javascript">
									 
									 function showLabel(url, title, w, h) 
									 {

										  var left = (screen.width/2)-(w/2);
										  var top = (screen.height/2)-(h/2);
										  return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', 			  height='+h+', top='+top+', left='+left);
									 }
									 
									 
									 
									$(document).ready(function()
                					{
										
										$("#divVehicle").css("display", "none");											
										
										$("#action").change(function(e)
										{
											var value = $("#action option:selected").val();
											
											
											
											if(value == 'Movement' || value == 'Returns')
											{												
												$("#divLocation").css("display", "block");
												$("#divPallet").css("display", "block");												
												$("#divVehicle").css("display", "none");	
												
												$.ajax({
													type: "POST",
													url: "pallet_movement_ajax.php",  // your php file name
													data: {action: 'loadlocationdropdown',  
													       locationtype: value
														   	 
														  },
													  success:function(data)
													  {
														   //alert(data);
														   $('#location option').remove();
														   $.each($.parseJSON(data), function(idx, obj) 
														   {
																
																$("#location").append("<option value = " + obj.id +                                                                    ">" + obj.name + "</option>");
																
														   });
															

																														
															//alert(data);
													  }
													  
													});													
												
											}
											else if(value == 'Dispatch')
											{
												$("#divVehicle").css("display", "block");												
												$("#divLocation").css("display", "none");
											}
											else
											{
												$("#divVehicle").css("display", "none");	
												$("#divLocation").css("display", "none");										
												/*$("#divLocation").css("display", "none");
												
												$("#divPallet").css("display", "none");																							
												$("#divSave").css("display", "none");																							                                                */
												
											}
											
										
											
										});
										
										document.getElementById('pallet_number').addEventListener('keypress', 
										function(event) 
										{
											if (event.keyCode == 13) 
											{
												document.getElementById('pallet_number').focus();
												event.preventDefault();
												$('#btnSaveLoc').click();
											}
										});
										
										$('#btnSaveLoc').click(function(e) 
										{
											
											e.preventDefault();	
											
											$("#loader").show();
											
											$("#msg").css("display", "none");						
											
											var moveType = $("#action option:selected").val();					
											
											var locationid = $("#location option:selected").val();	
											
											var pallet_number = $("#pallet_number").val();
											
											var vehicle_number = $("#vehicle_number").val();										
											
											if(action == '--- Please Select ---')
											{
												$("#msg").css("display", "block");
												$("#msg").html("<b>Error!</b> Please select action.");
												$("#loader").hide();
												return false;
											}											
											else if(action == 'Movement' && locationid == '--- Please Select ---')
											{
												$("#msg").css("display", "block");
												$("#msg").html("<b>Error!</b> Please select location.");
												$("#loader").hide();
												return false;
											}
											else if(pallet_number == '')
											{
												$("#msg").css("display", "block");
												$("#msg").html("<b>Error!</b> Please enter pallet/Bag/York number.");
												$("#loader").hide();
												return false;
											}
											
											$.ajax({
											  type: "POST",
											  url: "pallet_movement_ajax.php",  // your php file name
											  data: {action: 'movepallet',  
											  		 moveType:moveType,
													 pallet_number:pallet_number,
													 locationid:locationid,
													 vehicle_number:vehicle_number								 
													 
													},
											  success:function(data)
											  {											  
												  //alert(data);		  
												  $("#loader").hide();
												  
												  var obj = JSON.parse(data);		  		  
												  
												  //$("#msg").text('');
												
												  if(obj.result == 'error')
												  {
													 $("#msg").removeClass('alert-success'); 
													 $("#msg").addClass('alert-danger');				 
													 $("#msg").css('display','block'); 
													 $("#msg").html("<b>Error! </b>" + obj.message);													 
												  }
												  else
												  {
													    //alert(data);
														$("#msg").css('display','block'); 
													 	$("#msg").removeClass('alert-danger'); 
													 	$("#msg").addClass('alert-success'); 
													 	$("#msg").html("<b>Success! </b>" + obj.message);									 
														setTimeout(function()
														{														
															//$messageDiv.hide().html('');
															$("#pallet_number").val("");
															$('#pallet_number').focus();
															$("#msg").html("");
															$("#msg").css('display','none'); 
															
															
							
														}, 1000);
														
														//alert(obj.label);	
														
														if(obj.label != null)																  
													     	showLabel(obj.label, 'pallet label',400,470);
													  
													  
												  }
												  
											  }
											  
											});
											
											
											//$("#loader").show();
																	
											
											
											
										});				
					
					

                					});
									 	
									 </script>									 
									 
								  <?
            
    							}

    

    /*     * *
     * Controller logic goes here
     */
	 
   private function locationDropdown($action = '')
   {
	
	echo '<select id="location" name="location"  style="font-size:25px;height:50px;width:354px !important;">';
	echo '<option value="--- Please Select ---">--- Please Select ---</option>';				
	
	
	$user = Sessionmanager::getUser();
	
	$locationFilter = new LocationFilter();
	$locationFilter->AddActiveFilter("Y");			
	$locationFilter->AddWarehouseIdFilter($user->getWarehouseId());	
	
	if($action != 'Returns')
		$locationFilter->addReturnsLocationNameLikeFilter("not");	
	
	$locationListData = $locationFilter->getColumnList("id, name, active");
	
	
	
	//die;
	
	if(count($locationListData)>0)
	{
		foreach($locationListData as $locationItem)
		{
			if($locationItem->getName() == $this->location)
			echo '<option value="'.$locationItem->getName().'" selected="selected">'.$locationItem->getName().'</option>';
			else
			echo '<option value="'.$locationItem->getId().'" >'.$locationItem->getName().'</option>';			
		}
	}
	echo '</select>';
}
	 
	private function validate_form()
	{
		$validate = false;
		
		$palletNumber = trim($_POST["pallet_number"]);
		$this->action = $_POST["action"];
		$this->location = $_POST["location"];
		
		//echo $action .  " location " . $location;
		
		//die;
				
		if($this->action == '--- Please Select ---')
		{
			if ($this->error_msg != "") $this->error_msg .= "<br />";
			$this->error_msg .= "Please select Action.";
		}
		elseif($this->location == '--- Please Select ---')
		{
			if ($this->error_msg != "") $this->error_msg .= "<br />";
			$this->error_msg .= "Please select Location.";
		}
		elseif($palletNumber == '')
		{
			if ($this->error_msg != "") $this->error_msg .= "<br />";
			$this->error_msg .= "Pallet number should not be empty.";
		}
		elseif($palletNumber != '')
		{
			$palletFilter = new PalletFilter();
			$palletFilter->addPalletNumberFilter($palletNumber);
			$list = $palletFilter->getColumnList("id, palletno");
			if(count($list) == 0)
			{
				if ($this->error_msg != "") $this->error_msg .= "<br />";
				$this->error_msg .= "Pallet number not found.";
			}
			else
			{
				$validate = true;
			}
		}
		else
		{
			$validate = true;
		}
		
		return $validate;
	}

    public function init() 
	{
		$user = Sessionmanager::getUser();
		
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}
		
		$this->id = (isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : '');				
		$this->name = (isset($_POST['name']) ? strip_tags($_POST['name']) : '');
		
		$this->location =  $_POST['location'];
		$this->action = $_POST['action'];
		
		

		
		if (isset($_POST['btnSaveLoc']))
		{
			
			if ($this->validate_form())
			{
				/*$locObj = new PalletLocation($this->id);
				$locObj->setName($this->name);
				
				if($this->id == 0)
				{
					$locObj->setDateCreated(date("Y-m-d G:i:s"));
					$locObj->setCreatedBy($user->getId());	
					$locObj->setActive("Y");			
				}
				else
				{
					$locObj->setDateUpdated(date("Y-m-d G:i:s"));
					$locObj->setUpdatedBy($user->getId());				
				}
				//echo "<pre>";
				
				
				$locObj->save();
				util_redirect("viewlocations.php");*/
			}
		}
		else
		{
			$locObj = new Location($this->id);
			$this->name = $locObj->getName();
		}
		
			//$user = SessionManager::getUser();
			
     
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
