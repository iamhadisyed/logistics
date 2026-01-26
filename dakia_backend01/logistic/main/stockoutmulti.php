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
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Remove Items from Stock</div>
                
                <div class="tools"> <a href="javascript:;" class="collapse"> </a> 
                <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> 
                </div>
            </div>
			           
            <div class="portlet-body" style="padding-top:50px">
                <div class="scroller" data-rail-color="blue" data-handle-color="blue">                
                <div id="msg" style="padding-left:750px; display:none" class="alert alert-danger">

                 </div>
                    
                    <div class="col-md-12">
                    <form method="post">  
                    
                     <div class="row">
                     	<div id="errMsg" style="display:none">
						</div>
                        <div id="divNotFoundNumbers" style="display:none">  							 
                        </div>

                     </div>                      
                     <div class="row">
                       <div class="col-md-4">
                       		Select Process
                       </div>	
                       <div class="col-md-4">
                    	<select id="processList" name="processList" class="form-control" onchange="onchangeprocess();">
                           <option value="0">-- Select Process --</option>	
                          <option value="DEST">Destroyed</option>
                          <option value="RTC">Return To Customer</option>
                          <option value="WAREHOUSE">Return To Warehouse</option>
                       </select>
                       </div>
                    </div>  
                    <br /><br />                                  
                    <div class="row">
                       <div class="col-md-4">
                       		Select Warehouse
                       </div>	
                       <div class="col-md-4">
                    	<select id="warehouseList" name="warehouseList" class="form-control">
                           <option value="0">-- Select Warehouse --</option>	
                       </select>
                       </div>
                    </div>
                    <br /><br />
                    <div id="divPallet" class="row">
                    	<div class="col-md-4">
                       		Enter Tracking Numbers
                       </div>
                                           
                        <div class="col-md-4">
                           <textarea style="font-size:25px;height:300px;width:289px;" rows="4" cols="5" class="form-control" id="barcodelist" name="barcodelist" placeholder="Tracking Numbers" type="texarea" value=""></textarea>                    
                        </div>
                    </div>
                    
                    <br /><br />                   
                   
                    <div class="row"> 
                    	<div class="col-md-4">
                        </div>
                    	<div class="col-md-4">	   
                     		<button class="btn btn-primary btn_save" type="submit" id="btn_scan_multiple" 
                     			onClick="return RemoveItemsFromStock();" name="btn_scan_multiple">Process    
                                <i style="display:none;" id="loader" class="fa fa-spinner fa-spin icon-large"></i> 
                                </button>
                                 
                        </div>                            
                    
                   </div>   
                    
                    
                        
                  </div>                 
                  
                                
                 
              
                </div>
                	</form>
                    
                    </div>
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
									 
									 function onchangeprocess()
									 {
										 var selectedValue = $("#processList option:selected").val();			 									 
										 
										 var dropDown = $("#warehouseList");
										 	
										 dropDown.empty();	  									 										 
										     
										 
										 	 //alert(selectedValue);
										 
										 if(selectedValue == 'WAREHOUSE')
										 {
											 
											 
											 
											 										 
											  $.ajax({
													  type: "POST",
													  url: "box_ajax.php",  // your php file name
													  data: {action: 'WarehouseList'},
													  success:function(data)
													  {
														  
														   $("#loader").css("display", "none");
														  
														   var obj = JSON.parse(data);
														   
														   var warehouse = obj.warehouse;
														   
														   //alert(warehouse[0].hub);
														   
														   if(obj.msg == 'success')
														   {												   	 
																 
															   	 for(i=0;i<=warehouse.length;i++)
																 {				 			  																		
																																																									
																 		dropDown.append
																		(
																			$('<option></option>').val(warehouse[i].id).html(warehouse[i].hub)
																		);
																
																 }
																
														   }
													  }
		
												});
											
										 
										 }
										 else
										 {										 
											 dropDown.append($('<option></option>').val(0).html('-- Select Warehouse --'));
										 }
										 
									 }
									 
									 
									function RemoveItemsFromStock()
								    {
											
											var strNotFoundNumbers = "";
											
											$("#loader").css("display", "block");	
										
											$("#errMsg").text("");
											
											var arr = $("#barcodelist").val().split("\n").filter(Boolean);
											
											var status = $("#processList :selected").text();
											
											var value = $("#processList :selected").val();
											
											if(value == 0)
											{
												alert("Please select process");
												return false;
											}				 
											
											
											
											var warehouseid = $("#warehouseList option:selected").val();
											
											var arrDistinctBarcodes = new Array();
											 $(arr).each(function(index, item) {
												 if ($.inArray(item, arrDistinctBarcodes) == -1)
															arrDistinctBarcodes.push(item);
											 });
											 
											 if(arrDistinctBarcodes.length == 0)
											 {
												 alert("Please enter tracking numbers");
												 return false;
											 }
											 
											 var json_arrDistinctBarcodes = JSON.stringify(arrDistinctBarcodes);
											
											 $.ajax({
												  type: "POST",
												  url: "box_ajax.php",  // your php file name
												  data: {action: 'RemoveItemsFromStock',  trackingnumbers:  json_arrDistinctBarcodes, status:status, warehouseid: warehouseid },
												  success:function(data)
												  {
													  
													    $("#loader").css("display", "none");
																												
												   		var obj = jQuery.parseJSON(data);
																												
														
														if(obj.result == 'success')
														{
															
															if(obj.not_found.length > 0)
															{
																$("#divNotFoundNumbers").append("<b>Not Found Numbers List:</b><br><br>");
																															
																for(var i=0;i< obj.not_found.length;i++)
																{																	
																	$("#divNotFoundNumbers").append(obj.not_found[i] + "<br>" );	
																}
																
																$("#divNotFoundNumbers").css("display", "block");
																$("#divNotFoundNumbers").addClass("alert alert-danger");															
																
															}
															
															$("#errMsg").css("display", "block");
															$("#errMsg").addClass("alert alert-success");
															$("#errMsg").html("Success!!! Manifest Number : " + 
																obj.manifestid + " pdf File : <a target='_blank' href='" + obj.pdf + "'>PDF Link" + "</a>                                                                                   <br>Pallet Label : <a target='_blank' href='" + obj.label + "'>Label</a>"
																);
																
															showLabel(obj.label, 'Pallet Label', 400, 400);
														}
														else
														{
																
																$("#divNotFoundNumbers").css("display", "block");
																$("#divNotFoundNumbers").addClass("alert alert-danger");
																
																$("#divNotFoundNumbers").append("<b>Not Found Numbers List:</b><br><br>");
																
																for(var i=0; i< obj.not_found.length; i++)
																{																																	
																	$("#divNotFoundNumbers").append(obj.not_found[i] + "<br>" );	
																}
																
																
														}
															
															
														
															
														
												  }
	
											});
											
											
											return false;
										
										
									} 
									 
									 
									 

									$(document).ready(function()
                					{
										
										
										
										
									});
									 	
									 </script>	
                                      <script language="javascript" src="includes/3rdparty/calendar/calendar.js"></script>								 
									 
								  <?php
            
    							}

  
    /*     * *
     * Controller logic goes here
     */
	 
	

	 


    public function init() 
	{
		$user = Sessionmanager::getUser();
		
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}
		//$user = SessionManager::getUser();			
     
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
