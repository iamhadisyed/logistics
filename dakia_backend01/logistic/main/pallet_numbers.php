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

    private $report_filter;
    private $carrier_list;
    private $services_list;
    private $carrier;
    private $service;
    private $date_scanned_from;
    private $date_scanned_to;
    private $box_number;
    private $pallet_number;
    private $include_scanned = 'N';
    private $include_notscanned = 'Y';
    //private $service;

    private $report_data;
    private $where = "";
    private $join = "";

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
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Genreate Barcode Panel </div>
                
                <div class="tools"> <a href="javascript:;" class="collapse"> </a> 
                <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> 
                </div>
            </div>
			           
            <div class="portlet-body" style="padding-top:200px">
                <div class="scroller" data-rail-color="blue" data-handle-color="blue">                
					<div id="errMessage" style="display:none" class="alert alert-danger">
							
					</div>
                    
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            <label style="font-size:25px;color:#000065; padding-left:344px">Select Type :</label>                             
                         	</div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                             <select id="barcodeType" style="font-size:25px;height:50px;width:354px !important;" name="barcodeType" class="form-control">
                             	<option value="S">Please Select</option>
                             	<option value="C">Carton</option>
                                <option value="P">Pallet</option>
                                <option value="B">Bag</option>
                                <option value="Y">York</option>
                                <option value="M">Magnum</option>
                             </select>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            <label style="font-size:25px;color:#000065; padding-left:344px">Select Service :</label>                             
                         	</div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">	
                            <? $this->loadServiceTypes(); ?>
                            </div>
                    	</div>                        
                    </div>                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                               <label style="font-size:25px;color:#000065; padding-left:395px; width:345">Quantity :</label> 
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-thumb-tack"></i> </span>
                               <input style="font-size:25px;height:50px;width:311px" maxlength="4" size="4" class="form-control" id="number_pieces" name="number_pieces" placeholder="Pieces" type="number" value="">
                            </div>
                        </div>
                    </div>    
                  </div>      
                      <div class="row">
                      		<div class="col-md-6">

                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="form_action" id="form_action"  />
                                <input type="button" style="width:350px; height:50px" name="btnSearchNew" id="btnSearchNew" type="submit" class="btn btn-primary" value="Generate Barcode" />
                              
                            		<i style="display:none;" id="loader" class="fa fa-spinner fa-spin icon-large"></i>
                           		
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

                                public function renderHead() {
                                    ?>
            <script type="text/javascript">

			
			function showLabel(url, title, w, h) {

			  var left = (screen.width/2)-(w/2);
			  var top = (screen.height/2)-(h/2);
			  return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', 			  height='+h+', top='+top+', left='+left);
}
			
			

                $(document).ready(function()
                {
										
					$('#btnSearchNew').click(function(e) 
					{
						
						e.preventDefault();						
						
						var number_pieces = $("#number_pieces").val();						
						
						var barcodeType = $("#barcodeType option:selected").val();					
						
						var service_type = $("#service_type option:selected").val();
						
						
						$("#loader").show();
												
						$.ajax({
						  type: "POST",
						  url: "pallet_numbers_ajax.php",  // your php file name
						  data: {action: 'getPalletLabel',  
						  		 number_pieces: number_pieces, 
								 barcodeType:barcodeType,
								 service_type:service_type
								  },
						  success:function(data)
						  {
							  
							  //alert(data);		  
							  $("#loader").hide();
							  
							  var obj = JSON.parse(data);		  
							  
							  
							  $("#errMessage").text('');
							
							  if(obj.result == 'error')
				 			  {
								 alert(obj.message);
								 $("#errMessage").css('display','block'); 
								 $("#errMessage").append(obj.message); 
							  }
							  else
							  {
								  showLabel(obj.label, 'pallet label',400,420);
								  $("#errMessage").css('display','none'); 
								  
							  }
							  
							  return false;
		
						  }
		
						});

						
						
					});
					
					
					
					

                });



            </script>
        <?
    }

    

    /*     * *
     * Controller logic goes here
     */
	 
	private function loadServiceTypes($id = '')
	{

			$serviceArr = array(
							'HUNGARY POST TRACKED',
							'HUNGARY POST UNTRACKED',
							'ROYAL MAIL T&S',							
							'ROYAL MAIL TRACKED 48',							
							'ROYAL MAIL UNTRACKED',
							'SWEDEN POST TRACKED',
							'SWEDEN POST UNTRACKED',
							'EUROB2C',
							'DHL',
							"HERMES",
							"YODEL",
							"DPD GERMANY",
							"DPD GERMANY DIRECT",
							"DPD NETHERLAND",							
							"CORREOS UNTRACKED",
							"CORREOS",
							"DAC",
							"WHISTL",
							"GLS",
							"CPOST"


							);

		//$conCarrierFilter  = new ServiceFilter();
		//$serviceData   = $conCarrierFilter->getDistinctList('carrier_name');
		
		if($id == '')
			echo '<select name="service_type" id="service_type" style="font-size:25px;height:50px;width:354px !important;" class="form-control">';
		else
			echo '<select name="'.$id.'" id="'.$id.'" onchange="setImage();" width="400px">';	
		//if (!isset($this->form_vars["service_type"]) || $this->form_vars["service_type"]=="Please Select a Value")
		//{
		echo '<option>Please Select Service</option>';
		//}

		foreach ($serviceArr as $service)
		{
			$selected = "";
			if (isset($this->form_vars["service_type"]) && $this->form_vars["service_type"] == $value['value'])
			{
				$selected = " SELECTED ";
			}
			echo '<option value="' . $service. '"' . $selected . '>';
			echo $service;
			echo '</option>';
		}
		echo '</select>';


	}

    public function init() 
	{				
		
			//$user = SessionManager::getUser();
			
     
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
 