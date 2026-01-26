<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Tariffs list page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $tariffs; // array of tariff objects
	private $service_id = 0;
	private $courier_id = 0;

	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		?>
        <div style="text-align:right;">
	        <a href="tariffs_cost_full_upload.php?service_id=<?php echo $this->service_id; echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>"  class="btn btn-primary">Upload Tariff List</a>
                        &nbsp;&nbsp;&nbsp;<a  class="btn btn-primary" href="tariffs_cost_details.php?service_id=<?php echo $this->service_id; echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>">Add new tariff</a>
                        &nbsp;&nbsp;&nbsp;<a  class="btn btn-primary" href="services.php?courier_id=<?php echo $this->courier_id ?>">Services</a>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    BULK COST TARIFF: <?php echo $this->customerName; ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                    <a href="" class="fullscreen">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                </div>
                
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:100px;"  data-rail-color="blue" data-handle-color="blue">
                <table class="table table-striped table-bordered table-advance table-hover">
				<thead>
                    <tr>
                        <th colspan="2" style="text-align:center;">
                            Manual Edit Section 
                        </th>
                        <th colspan="2" style="text-align:center;">
                            CSV Download Section
                        </th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="text-align:center;">
                    	<a  class="btn btn-primary" href="tariffs_cost_bulk_update.php?tariff=tariff_cost&service_id=<?php echo $this->service_id; echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>">Cost Price</a>
                	</td>
                    <td style="text-align:center;">
                    <a  class="btn btn-primary" href="tariffs_cost_bulk_update.php?tariff=unit_cost&service_id=<?php echo $this->service_id; echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>">Cost Unit Price</a>
                    </td>
                    <td style="text-align:center;"><a  class="btn btn-primary" href="tariffs_cost_bulk_download.php?tariff=tariff_cost&service_id=<?php echo $this->service_id; echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>"><span class="glyphicon glyphicon-download-alt"></span> Cost Price</a>
                </td>
                    <td style="text-align:center;"><a  class="btn btn-primary" href="tariffs_cost_bulk_download.php?tariff=unit_cost&service_id=<?php echo $this->service_id; echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>"><span class="glyphicon glyphicon-download-alt"></span> Cost Unit Price</a>
                    </td>
                </tr>
				</tbody>
			</table>
                
                </div>
			</div>
          </div>
			<div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    NORMAIL TARIFF SECTION: <?php echo $this->customerName; ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                    <a href="" class="fullscreen">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                </div>
                
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px;"  data-rail-color="blue" data-handle-color="blue">
                
			 <table class="table table-striped table-bordered table-advance table-hover">
				<thead>
				<tr>
					<th rowspan="2" align="center" style="vertical-align:middle; text-align:center;">Action</th>
                    <th style="text-align:center;">From Zone</th>
					<th style="text-align:center;">To Zone</th>
					<th align="center" style="vertical-align:middle; text-align:center;">Option</th>
					
				</tr>
				<?php /*?><tr>
					<th></th>
<?php /  *?>					<th>Postcode Group</th>
<?php * /?>					<th>&nbsp;</th>
					<th></th>
<?php / *?>					<th>Postcode Group</th>
<?php * /?>				</tr><?php */?>
				</thead>
				<tbody>
			 <?php
			 if (count($this->tariffs) > 0)
			 {
				 foreach ($this->tariffs as $tariff)
				 {
				 ?>
					<tr>
						<td align="center" class="clsButton"><a href="tariffs_cost_details.php?service_id=<?php echo $tariff->getCourierServiceId();?>&collection_rateband_id=<?php echo $tariff->getCollectionRatebandId();?>&destination_rateband_id=<?php echo $tariff->getDestinationRatebandId();?>&collection_postcode_group_id=<?php echo $tariff->getCollectionPostcodeGroupId();?>&destination_postcode_group_id=<?php echo $tariff->getDestinationPostcodeGroupId(); echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                        &nbsp;&nbsp;&nbsp;<a href="tariffs_cost_details.php?service_id=<?php echo $tariff->getCourierServiceId();?>&collection_rateband_id=<?php echo $tariff->getCollectionRatebandId();?>&destination_rateband_id=<?php echo $tariff->getDestinationRatebandId();?>&collection_postcode_group_id=<?php echo $tariff->getCollectionPostcodeGroupId();?>&destination_postcode_group_id=<?php echo $tariff->getDestinationPostcodeGroupId(); echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Tariff?');"><span class="glyphicon glyphicon-remove"></span></a></td>
                        <td><?php echo $tariff->getCollectionRateband();?></td>
						<td><?php echo $tariff->getDestinationRateband();?></td>
							<td align="center" class="clsButton">
                        <a   class="btn btn-primary" href="tariffs_cost_update.php?service_id=<?php echo $tariff->getCourierServiceId();?>&collection_rateband_id=<?php echo $tariff->getCollectionRatebandId();?>&destination_rateband_id=<?php echo $tariff->getDestinationRatebandId();?>&collection_postcode_group_id=<?php echo $tariff->getCollectionPostcodeGroupId();?>&destination_postcode_group_id=<?php echo $tariff->getDestinationPostcodeGroupId(); echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>">
                       <span class="glyphicon glyphicon-open"></span>
                        Update Tariff Cost</a>
                        &nbsp;&nbsp;
                        <a  class="btn btn-primary" href="tariffs_cost_download.php?service_id=<?php echo $tariff->getCourierServiceId();?>&collection_rateband_id=<?php echo $tariff->getCollectionRatebandId();?>&destination_rateband_id=<?php echo $tariff->getDestinationRatebandId();?>&collection_postcode_group_id=<?php echo $tariff->getCollectionPostcodeGroupId();?>&destination_postcode_group_id=<?php echo $tariff->getDestinationPostcodeGroupId(); echo (isset($_REQUEST['tariff_name']))? '&tariff_name='.htmlspecialchars($_REQUEST['tariff_name']) :''; ?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Cost Price</a> 
                        &nbsp;&nbsp;<a  class="btn btn-primary" data-toggle="modal" data-target="#pricing-invoice" title="Pricing" href="#" onclick="getEstimatedDataValue('<?php echo $tariff->getCourierServiceId();?>','<?php echo $tariff->getCollectionRatebandId();?>','<?php echo $tariff->getDestinationRatebandId();?>')">
                        <span class="glyphicon glyphicon-dashboard"></span>Delivery Time</a>
                        </td>
						
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
        </form>
	<form id="estimated_delivery_time" name="estimated_delivery_time" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="pricing-invoice" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Estimated Delivery Time</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="pricing_details_msg">Updated successfully</div>
                            <input type="hidden" name="action" id="action" value="UPDATE_ESTIMATE_DELIVERY_TIMING" />
                            <input type="hidden" name="id" id="id" value="0" />
                            <div class="row">
                                <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">Service Name</label><br /><span id="service_name"></span>
                                                <input type="hidden" name="estimated_service[service_id]" id="service_id" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                            <div class="form-group col-md-6">
                                              <div class="form-group col-md-12">
                                                 <label class="control-label font-green-soft">Delivery Time</label>
                                              </div>
                                              <div class="form-group col-md-4">
                                              <select name="estimated_service[from_timing]" id="from_timing"class="form-control input-sm customer_charges col-md-4">
                                                	<option value="">From Day</option>
                                                    <?php for($j=1; $j<=60; $j++)
													{
													?>
	                                                    <option value="<?=$j ?>"><?=$j ?></option>
                                                    <?php
													}?>
                                                    
                                                </select>
                                              </div> 
                                              <div class="form-group col-md-4">
                                               <select name="estimated_service[to_timing]" id="to_timing"class="form-control input-sm customer_charges  col-md-4">
                                                	<option value="">To Day</option>
                                                    <?php for($j=1; $j<=60; $j++)
													{
													?>
	                                                    <option value="<?=$j ?>"><?=$j ?></option>
                                                    <?php
													}?>
                                                    
                                                </select>
                                              </div> 
                                              <div class="form-group col-md-4">
                                              Days
                                              <input type="hidden" name="estimated_service[delivery_timing]" id="delivery_timing" value="" class="form-control input-sm customer_charges" />
                                              </div>  
                                               
                                                
                                                
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">From Rateband</label><br /><span id="from_rateband_name"></span>
                                                <input type="hidden" name="estimated_service[from_rateband]" id="from_rateband" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label font-green-soft">To Rateband</label><br /><span id="to_rateband_name"></span>
                                                <input type="hidden" name="estimated_service[to_rateband]" id="to_rateband" value="" class="form-control input-sm customer_charges" />
                                            </div>
                                        </div>    
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update_pricing_details_btn" onclick="updatePricingDetails();">Save changes</button>
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
	public function renderMenu()
	{
		$menu = new Adminmenu(Adminmenu::COURIERS);
		$menu->render();
	}

public function  renderHead()
{
	?>
	<script>
	
	 function updatePricingDetails() 
	{
		var form_data = $("#estimated_delivery_time").serialize();
		//alert('updatePricingDetails'+form_data);
		
		if ($("#pricing_details_msg").hasClass("alert-success"))
			$("#pricing_details_msg").removeClass("alert-success");
		$("#pricing_details_msg").addClass("alert-info");
		$("#pricing_details_msg").html("please wait we are saving...");
		$("#pricing_details_msg").show();
		$.post("tariffs_cost.php", form_data, function(data) {
			if ($("#pricing_details_msg").hasClass("alert-info"))
				$("#pricing_details_msg").removeClass("alert-info");
			$("#pricing_details_msg").addClass("alert-success");
			$("#pricing_details_msg").html("Updated successfully.");
			$("#pricing_details_msg").show();
			var obj = jQuery.parseJSON(data);//$('#service').html('<option value="">Select Service</option>' + data);
			$("#id").val(obj.id);
		});
	}



	function getEstimatedDataValue(serviceId, fromRateBand, toRateBand)
		{
			 $("#pricing_details_msg").hide();
			//alert(serviceId+'-'+fromRateBand+'-'+toRateBand);
			$.ajax({
					url: "tariffs_cost.php",
					type: "POST",
					async: false,
					data: {
						action			: 'GET_ESTIMATE_DELIVERY_TIME',
						service_id		: serviceId,
						from_rateband	: fromRateBand,
						to_rateband		: toRateBand,
						
					},
					success: function(data) 
					{
						var obj = jQuery.parseJSON(data);//$('#service').html('<option value="">Select Service</option>' + data);
						
						$("#delivery_timing").val(obj.delivery_timing);
						$("#from_timing").val(obj.from_timing);
						//alert(obj.from_timing);
						$("#to_timing").val(obj.to_timing);
						$("#service_id").val(obj.service_id);
						$("#from_rateband").val(obj.from_rateband);
						$("#to_rateband").val(obj.to_rateband);
						$("#service_name").html(obj.service_name);
						
						$("#from_rateband_name").html(obj.from_rateband_name);
						$("#to_rateband_name").html(obj.to_rateband_name);
						$("#id").val(obj.id);
					
					}
               	});
		}
    </script>
	<?php
}
	/***
	 * Controller logic goes here
	 */
	public function init()
	{
		// check admin user is authenticated
			
		$user = SessionManager::getUser();
		if ($user->getUserType() != "finance" && $user->getUserType() != "admin")
		{
			util_redirect("index.php");
		}
		//
		$this->setTitle("Admin - Cost Tariffs");
		
		
		if (isset($_GET['tariff_name']) ) 
		{
			$this->customerName	=	$_GET['tariff_name'];
		}
		
		if (isset($_POST['action']) && $_POST['action']== 'UPDATE_ESTIMATE_DELIVERY_TIMING') 
		{
				extract($_POST);
				$estimateTimingDetails	=	'';
				$fromTiming				=	$_POST['estimated_service']['from_timing'];
				$toTiming				=	$_POST['estimated_service']['to_timing'];
				if(trim($fromTiming	) == '')
				{
					$estimateTimingDetails	=	' ';
				}
				else
				{
					$estimateTimingDetails	=	$fromTiming;
				}
				
				if(trim($toTiming) == '')
				{
					$estimateTimingDetails	.=	' ';
				}
				else
				{
					$estimateTimingDetails	.=	" - ".$toTiming ;
				}
				$estimateTimingDetails	.=	' Days';
					if($_POST['id']> 0)
			{
				
				
				
				$estimateDeliveryTime	=	new EstimateDeliveryTimingFilter($_POST['id']);
				$estimateDeliveryTime->addFieldFilter('id',$_POST['id']);
				$columnList			=		$estimateDeliveryTime->getColumnLIst('id, service_id, from_rateband, to_rateband, created_by ');
				if(count($columnList)>0)
				{
					foreach($columnList as $columnListData){
						$columnListData->getDeliveryTiming();
						$columnListData->getServiceId();
						$columnListData->setDeliveryTiming($estimateTimingDetails);
						$columnListData->save();
						$recordId	=	$columnListData->getId();
						break;
						}
				}
			}
			else
			{
				$estimateDeliveryTime	=	new EstimateDeliveryTiming();
				$estimateDeliveryTime->setDeliveryTiming($estimateTimingDetails);
				$estimateDeliveryTime->setFromRateband($_POST['estimated_service']['from_rateband']);	
				$estimateDeliveryTime->setToRateband($_POST['estimated_service']['to_rateband']);	
				$estimateDeliveryTime->setServiceId($_POST['estimated_service']['service_id']);
				$estimateDeliveryTime->setStatus('ACTIVE');
				$estimateDeliveryTime->setDateCreated(date('Y-m-d h:i:s'));
				$estimateDeliveryTime->setCreatedBy('admin');
				$estimateDeliveryTime->save();	
				$recordId	=	$estimateDeliveryTime->getId();
			}
			
			$arrau['id']	=	$recordId;
			echo json_encode($arrau);
			
			//print_r($_POST);
			exit;
		}
		
		if (isset($_POST['action']) && $_POST['action']== 'GET_ESTIMATE_DELIVERY_TIME') 
		{
			$service_id		=	$_POST['service_id'];
			$from_rateband	=	$_POST['from_rateband'];
			$to_rateband	=	$_POST['to_rateband'];
			$responceData	=	array();
			
			// get Servicename and ratebandname 
			
			$ratebandNames			=	array();
			$serviceName			=		new RatebandFilter();
			$serviceNameList		=		$serviceName->getServiceRatebandNameLIst($service_id, $from_rateband, $to_rateband);
			if(count($serviceNameList) > 0)
			{
				foreach($serviceNameList as $serviceData)
				{
					$serviceNameIn	=	$serviceData->getChangedBy();
					$ratebandNames[$serviceData->getId()]	= $serviceData->getName();	
				}
			}
			
			
			$estimateDeliveryTime	=	new EstimateDeliveryTimingFilter();
			$estimateDeliveryTime->addFieldFilter('service_id',$service_id);
			$estimateDeliveryTime->addFieldFilter('from_rateband',$from_rateband);
			$estimateDeliveryTime->addFieldFilter('to_rateband',$to_rateband);
			//$estimateDeliveryTime->addFieldFilter('status','ACTIVE');
			$columnList			=		$estimateDeliveryTime->getColumnLIst('id, service_id, from_rateband, to_rateband, delivery_timing ');
			if(count($columnList) > 0	)
			{
				$columnListData = $columnList[0];
				$responceData['delivery_timing']	=	$columnListData->getDeliveryTiming();
				$delivery_timing					=	$responceData['delivery_timing'];
				$delivery_timing	=	str_replace( 'Days','',$delivery_timing);
				$delivery_timing	=	str_replace( ' ','',$delivery_timing);
				$estimateDelieveryTimeparts	=	explode('-',$delivery_timing);
				$responceData['from_timing']		=	@$estimateDelieveryTimeparts[0]	;
				$responceData['to_timing']			=	@$estimateDelieveryTimeparts[1]	;
				$responceData['service_id']			=	$columnListData->getServiceId();
				$responceData['from_rateband']		=	$columnListData->getFromRateband();
				$responceData['to_rateband']		=	$columnListData->getToRateband();
				$responceData['id']					=	$columnListData->getId();
				
				$responceData['service_name']			=	$serviceNameIn;
				$responceData['from_rateband_name']		=	$ratebandNames[$from_rateband];
				$responceData['to_rateband_name']		=	$ratebandNames[$to_rateband];
			}
			else
			{
					$columnListData = $columnList[0];
					$responceData['delivery_timing']		=	'';
					$responceData['from_timing']			=	'';
					$responceData['to_timing']				=	'';
					$responceData['service_id']				=	$service_id	;
					$responceData['from_rateband']			=	$from_rateband;
					$responceData['to_rateband']			=	$to_rateband;
					$responceData['service_name']			=	$serviceNameIn;
					$responceData['from_rateband_name']		=	$ratebandNames[$from_rateband];
					$responceData['to_rateband_name']		=	$ratebandNames[$to_rateband];
					$responceData['id']						=	'0';
			}
			echo json_encode($responceData);
			exit;
			
		}


		/*------------------------------------------------------------------------------*/
		// get vars
		(int)$this->service_id		= (isset($_REQUEST['service_id'])			 ? strip_tags($_REQUEST['service_id']) : '');
		

		$tariff_name	=	(isset($_REQUEST['tariff_name'])			 ? strip_tags(htmlspecialchars($_REQUEST['tariff_name'])) : '');

		// determine the courier id
		$serviceObject = new Services($this->service_id);
		$this->courier_id = $serviceObject->getCarrier();

		/*------------------------------------------------------------------------------*/
		// get tariffs
		
		$TarObj		 = new CostTariffsFilter();
		$TarObj->addFieldFilter('courier_service_id',$this->service_id);
		$tariff_name	=	(isset($_REQUEST['tariff_name'])			 ? strip_tags(htmlspecialchars($_REQUEST['tariff_name'])) : '');
		if(trim($tariff_name) != '')
		{
			$TarObj->addFieldFilter('tariff_name',$tariff_name);
		}
		$this->tariffs = $TarObj->getTarifList();
	}
	
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 