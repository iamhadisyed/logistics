<?php

////////////////////////////////////////////////////
//
// List of courier services
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $_courier_id = 0;
	private $_services = array();

    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>

<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption"> <i class="glyphicon glyphicon-search"></i>Services for <?php echo $this->_courier_id;?></div>
    <div class="tools"> <a href="couriers.php" style="color:#fff;">Couriers</a> <a href="javascript:;" class="collapse"></a> </div>
  </div>
  <div class="portlet-body">
    <div class="scroller" style="min-height:300px; "  data-rail-color="blue" data-handle-color="blue">
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr class="red-back">
            <th>Ref.</th>
            <th>Private Name</th>
            <th>Service</th>
            <th align="center">Currency</th>
            <th align="center">Active</th>
            
            <th align="center">Remote Area</th>
            <th align="center">Rate Bands</th>
            <th align="center">Tariffs</th>
            <?php /*?><th align="center">Edit</th><?php */?>
          </tr>
        </thead>
        <tbody>
          <?php
			  
              if (count($this->_services) > 0)
			  {
			      foreach ($this->_services as $service)
				  {

				  	switch ($service->getClassCode())
				  	{
				  		case "dhlexp":
				  			$autobooking = "DHL Day";
				  			break;
				  		case "dhl":
				  			$autobooking = "DHL Time";
				  			break;
				  		default:
				  			$autobooking = "None";
				  	}
				  ?>
          <tr>
            <td class="valign-middle"><?php echo $service->getReference();?></td>
            <td class="valign-middle"><?php echo $service->getCode();?></td>
            <td class="valign-middle"><?php echo $service->getName();?></td>
            <td class="valign-middle"><?php echo $service->getUploadedCurrency();?></td>
            <td class="valign-middle" align="center" ><?php echo $service->getActive() ? "Yes":"NO";?></td>
            
            <td align="center" class=""><a class="btn btn-xs default blue-stripe " href="remotearea_services.php?service_id=<?php echo $service->getId();?>&courier_id=<?php echo $this->_courier_id; ?>">Remote Areas</a></td>
            <td align="center" class="">
                <!--
                ratebands.php?service_id=<?php echo $service->getId();?>&courier_id=<?php echo $this->_courier_id; ?>
                countries_zones.php?service_id=<?php echo $service->getId();?>&courier_id=<?php echo $this->_courier_id; ?>
                -->
            	<a class="btn btn-xs default blue-stripe event-ratebandload"  
                   data-toggle="modal" data-target="#rate-band-popup" role="dialog" tabindex="-1" 
                   data-serviceid="<?php echo $service->getId();?>" data-action="GET_ALL_RATEBANDS"
                   data-courierid="<?php echo $this->_courier_id; ?>" data-ratebandload="services.php"
                   data-servicename="<?php echo $service->getName();?>"
                   href="javascript:;">
                	Rate Bands
				</a>
			</td>
            <?php /*?><td align="center" class="clsButton">
            	<a class="btn btn-primary" href="countries_zones.php?service_id=<?php echo $service->getId();?>&courier_id=<?php echo $this->_courier_id; ?>">Countries</a>
            </td><?php */?>
            <td align="center" class="clsButton"><?php /*?><a href="tariffs.php?service_id=<?php echo $service->getId();?>&courier_id=<?php echo $this->_courier_id; ?>">Tariffs Codes</a>  ||<?php */?>
              <span onclick="showCostTariffList('service-cost-<?php echo $service->getId();?>','<?php echo $service->getId();?>', '<?php echo $this->_courier_id; ?>')"  class="btn btn-xs default blue-stripe ">Cost Tariff</span>
              <span onclick="showTariffList('service-<?php echo $service->getId();?>','<?php echo $service->getId();?>', '<?php echo $this->_courier_id; ?>')"  class="btn btn-xs default blue-stripe ">Chargeable Tariff</span>
              </td>
            <?php /*?><td align="center" class="clsButton"><a class="btn btn-primary" href="service_details.php?service_id=<?php echo $service->getId();?>&courier_id=<?php echo $this->_courier_id; ?>">Edit</a></td><?php */?>
            <?php /*?><td align="center" class="clsButton"><a href="service_details.php?service_id=<?php echo $service->getId();?>&courier_id=<?php echo $this->_courier_id; ?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Service?')">Delete</a></td><?php */?>
          </tr>
          <tr >
            <td colspan="11" id="service-<?php echo $service->getId();?>" style="display:none;"  class="clsButton" align="center"></td>
          </tr>
          <tr >
            <td colspan="11" id="service-cost-<?php echo $service->getId();?>" style="display:none;"  class="clsButton" align="center"></td>
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

<form id="download_tariff_frm" name="download_tariff_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="download_tariff" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Tariff Validity</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="download_tariff_msg"></div>
                            <input type="hidden" name="action" id="action" value="DOWNLOAD_TARIFF" />                            
                            <div class="row">
                                
                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Details</legend>
                                        <div class="row">
                                            <div class="col-md-12 download-tariff-service-area">
                                                
                                                <div class="col-md-3">
                                                    <div class="form-group ">
                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                                            <input class="form-control" name="tariff_name" id="id" type="hidden" readonly="readonly" disabled="disabled" />
                                                            <input class="form-control" name="tariff_name" id="tariff_name" type="text" readonly="readonly" disabled="disabled" placeholder="Tariff Name" value="" rel="tooltip" data-original-title="Tariff Name" data-placement="bottom"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group ">
                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                                            <input class="form-control" name="tariff_type" id="tariff_type" type="text"   readonly="readonly"  disabled="disabled" placeholder="Tariff Type" value=""    rel="tooltip" data-original-title="Tariff Type" data-placement="bottom" />
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
                                                
                                                <div class="col-md-3">
                                                    <div class="form-group ">
                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                                            <input class="form-control" name="date_start" id="date_start" type="text" placeholder="Date Start" value="" rel="tooltip" data-original-title="Date Start" data-placement="bottom"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group ">
                                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                                            <input class="form-control" name="date_to" id="date_to" type="text"  placeholder="Date To" value=""    rel="tooltip" data-original-title="Date End" data-placement="bottom" />
                                                         
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                                              
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="save-tariff-validity">Save</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
    
<div class="modal fade" tabindex="-1" role="dialog" id="rate-band-popup" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Rate Bands Details</h4>
            </div>
            <div class="modal-body">

                <div class="row" >
                    <div class="col-md-12"  id="rateband-content-display">

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
    
    
    <div class="modal fade" tabindex="-1" role="dialog" id="country-rateband-popup" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">List Country</h4>
                </div>
                <div class="modal-body">

                    <div class="row" >
                        <div class="col-md-12"  id="countryrateband-content-display">

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
    public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::COURIERS);
    	$menu->render();
    }


    /**
     * Override to show the menu
     *
     */
    public function renderHead()
    {
    	?>
         <link rel="stylesheet" href="../_assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-timepicker.min.css" />
        <script type="text/javascript" src="../_assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script>
		function showTariffList(dataId, serviceId, courier_name)
		{
			 $.post( 
			 	"ajaxTariffs.php",
				{action:'SHOW_ALL_TARIFF',service_id:serviceId, courier_name:courier_name,tariff_type:'chargeable'},
				function( data ) 
				{
					$("#"+dataId).html(data);
					$('.tariff-validitaty').click(function(e){
						 $("#download_tariff_msg").hide();
						$("#download_tariff").modal("show");
						$("#download_tariff_msg").hide();
						
						var elm = $(this);
						var tariff_name	= elm.data('tariff-name');
						var tariff_type	= elm.data('tariff-type');
						var action	= elm.data('action');
						$.post( 
							"ajaxTariffs.php",
							{action:action, tariff_name:tariff_name, tariff_type:tariff_type},
							function( data ){
								var obj = jQuery.parseJSON( data);
								if($.trim(obj.start_date) != '')
									$("#date_start").val(obj.start_date);
								
								 
								 
								if($.trim(obj.end_date) != '')
									$("#date_to").val(obj.end_date);		
								if($.trim(obj.tariff_type) != '')
									$("#tariff_type").val(obj.tariff_type);
								if($.trim(obj.tariff_name) != '')
									$("#tariff_name").val(obj.tariff_name);
								if($.trim(obj.id) != '')
									$("#id").val(obj.id);
								//$(".download-tariff-service-area").html(data);		
							});
					 
					 });
					 
				});
//			$('#'+dataId).html(servicename + 'this is test ');
			$('#service-cost-'+serviceId).hide();
			$('#'+dataId).toggle();
		}
		
		
		function showCostTariffList(dataId, serviceId, courier_name)
		{
			 $.post( 
			 	"ajaxTariffs.php",
				{action:'SHOW_ALL_TARIFF',service_id:serviceId, courier_name:courier_name,tariff_type:'cost'},
				function( data ) 
				{
					$("#"+dataId).html(data);
					$('.tariff-validitaty').click(function(e){
						 $("#download_tariff_msg").hide();
						$("#download_tariff").modal("show");
						$("#download_tariff_msg").hide();
						
						var elm = $(this);
						var tariff_name	= elm.data('tariff-name');
						var tariff_type	= elm.data('tariff-type');
						var action	= elm.data('action');
						$.post( 
							"ajaxTariffs.php",
							{action:action, tariff_name:tariff_name, tariff_type:tariff_type},
							function( data ){
								var obj = jQuery.parseJSON( data);
								if($.trim(obj.start_date) != '')
									$("#date_start").val(obj.start_date);
								
								 
								 
								if($.trim(obj.end_date) != '')
									$("#date_to").val(obj.end_date);		
								if($.trim(obj.tariff_type) != '')
									$("#tariff_type").val(obj.tariff_type);
								if($.trim(obj.tariff_name) != '')
									$("#tariff_name").val(obj.tariff_name);
								if($.trim(obj.id) != '')
									$("#id").val(obj.id);
								//$(".download-tariff-service-area").html(data);		
							});
					 
					 });
				});
			$('#service-'+serviceId).hide();
//			$('#'+dataId).html(servicename + 'this is test ');
			$('#'+dataId).toggle();
		}
		// Set date picker
            $(document).ready(function () {
				
		$('#date_start').datepicker({dateFormat: 'dd-mm-yy'});
        $("#date_to").datepicker({dateFormat: 'dd-mm-yy'});
		$("#save-tariff-validity").click(function(){

				$("#download_tariff_msg").hide();
			
				var form_data = new FormData();
                
				var  action 		=  'SAVE_TARIFF_EXPIRE';
				var  id 			=  $("#id").val();
				var  date_start 	=  $("#date_start").val();
				var  date_end  		=  $("#date_to").val();
				var  tariff_type 	=  $("#tariff_type").val();
				var  tariff_name  	=  $("#tariff_name").val();
				
				
				
			$.post( 
				"ajaxTariffs.php",
				{
					action : action, id:id,date_start:date_start,date_end:date_end,tariff_type:tariff_type,tariff_name:tariff_name
					},
				function( data ) 
				{
					var obj = jQuery.parseJSON( data);
					if($.trim(obj.start_date) != '')
						$("#date_start").val(obj.start_date);
					if($.trim(obj.date_end) != '')
						$("#date_to").val(obj.end_date);		
					if($.trim(obj.tariff_type) != '')
						$("#tariff_type").val(obj.tariff_type);
					if($.trim(obj.tariff_name) != '')
						$("#tariff_name").val(obj.tariff_name);
					if($.trim(obj.id) != '')
						$("#id").val(obj.id);
									
					$("#download_tariff_msg").html(obj.message);
					$("#download_tariff_msg").show();
				});						
								
									
									
		});
                
             //   ratebandload
                $(document).on('click', '.event-ratebandload', function () {
                    //console.log($(this));
                    $("#rateband-content-display").html('Please wait, we are dealing with your request.');
                    var e = $(this);
                    var service = e.data('serviceid');
                    var courier = e.data('courierid');
                    var url = e.data('ratebandload');
                    var servicename = e.data('servicename');
                    
                    var action = e.data('action');
                    $.post(url, {func: action , service: service,service_name:servicename , courier:courier }, function (d) {
                        $("#rateband-content-display").html(d);
                        //$("#detail-log-popup").modal('show');
                    });
                });
                
                  $(document).on('click', '.get_country_rateband_list', function () {
                    //console.log($(this));
                    $("#countryrateband-content-display").html('Please wait, we are dealing with your request.');
                    var e = $(this);
                    
                    var ratebandid = e.data('ratebandid');
                    var service = e.data('service');
                    var url = 'services.php';
                    var servicename = e.data('servicename');
                    var action = e.data('action');
                    $.post(url, {func: action , service: service,service_name:servicename , ratebandid:ratebandid }, function (d) {
                        $("#countryrateband-content-display").html(d);
                        $("#country-rateband-popup").modal('show');
                    });
                });
			});
		
                
                
                
		
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
        
        
        
        if (util_request("func") == 'GET_RATEBANDS_COUNTRY')
        {
            $serviceId      =   $this->form_vars['service'];
            $serviceName      =   $this->form_vars['service_name'];
            $ratebandid    =   $this->form_vars['ratebandid'];
            
            
            $Countryrateband    = new CountryFilter();
            $Countryrateband->addFilter(" id in ( select country_id from countries_link_ratebands where rateband_id = '".$ratebandid."') ");
            $countryList    =   $Countryrateband->getColumnList(' name ');
            if(count($countryList)>0)
            {
               foreach($countryList as $countryData)
               {
                  echo '<div class="col-sm-4">
                                '. $countryData->getName().'
                        </div>';
               }
                   
            }
            else
            {
                echo '<div class="col-sm-4">
                        No Country assigned yet.
                        </div>';
            }
            
            die;
        }
        
        if (util_request("func") == 'DOWNLOAD_ALL_RATEBANDS')
        {
            $serviceId      =   util_request('service_id');
            $serviceName    =   util_request('service_name');

            // output headers so that the file is downloaded rather than displayed
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$serviceName.'.csv');

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            // output the column headings
            fputcsv($output, array('Rateband Names'));
            
            //Call ratebands filter class to set filters and get data from database 
            $RbnObj             = new RatebandFilter();
            $RbnObj->addFieldFilter("courier_service_id",  $serviceId);
            $ratebandsFilter    = $RbnObj->getColumnList("name");
            // Check data get from the tables
            if(count($ratebandsFilter)>0)     
            {
                // loop over the rows, outputting them
                foreach($ratebandsFilter as $ratebands)
                    fputcsv($output, array($ratebands->getName()));
            }
            
            exit;
               
        }
        
        if (util_request("func") == 'DOWNLOAD_ALL_RATEBANDS_COUNTRIES')
        {
            $serviceId          =   util_request('service_id');
            $serviceName        =   util_request('service_name');
            $ratebandid         =   util_get_num('ratebandid');
            

            // output headers so that the file is downloaded rather than displayed
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=country-rateband-list-'.time().'.csv');

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            // output the column headings
            fputcsv($output, array('Rateband', 'Country', 'ISO'));
            
            //Call ratebands filter class to set filters and get data from database 
            $RbnObj             = new RatebandFilter();
            $RbnObj->addFieldFilter("courier_service_id",  $serviceId);
            if($ratebandid>0)
                $RbnObj->addFieldFilter("r.id",  $ratebandid);    
            $RbnObj->AddOrderBy('r.id');
            $ratebandsFilter    = $RbnObj->getCountryRatebandList("r.name 'name', c.name 'country', c.iso 'iso'");
            // Check data get from the tables
            if(count($ratebandsFilter)>0)     
            {
                // loop over the rows, outputting them
                foreach($ratebandsFilter as $ratebands)
                    fputcsv($output, array($ratebands->getName(), $ratebands->getCountry(),$ratebands->getIso()));
            }
            
            exit;
               
        }
        
        if (util_request("func") == 'GET_ALL_RATEBANDS')
        {
            $serviceId      =   $this->form_vars['service'];
            $serviceName      =   $this->form_vars['service_name'];
            $courierName    =   $this->form_vars['courier'];
            
            $downloadRateband   =   false;
            
            $RbnObj             = new RatebandFilter();
            $RbnObj->addFieldFilter("courier_service_id",  $serviceId);
            $ratebandsFilter    = $RbnObj->getColumnList("name");
            if(count($ratebandsFilter)>0)     
            {
                $downloadRateband=  true;
                foreach($ratebandsFilter as $ratebands)
                {
                    echo '<div class="col-sm-6 download-tariff-popup">
                                <span class="pull-left" title="Tariff Name">
                                <strong>'.$ratebands->getName().'</strong>
                                </span>
                                
                                <a href="services.php?func=DOWNLOAD_ALL_RATEBANDS_COUNTRIES&service_id='.$serviceId.'&ratebandid='.$ratebands->getId().'"  title="Download Countries">
                                    <span class="glyphicon glyphicon-download pull-right" ></span>
                                </a>    
                                <span class="pull-right">&nbsp;&nbsp;</span>                                
                                <a href="javascript:;" class="get_country_rateband_list" data-action="GET_RATEBANDS_COUNTRY"
                                data-ratebandid="'.$ratebands->getId().'" data-servicename="'.$serviceName.'" data-service="'.$serviceId.'" title="Show Countries">
                                    <span class="glyphicon glyphicon-flag pull-right rateband-countries" ></span>
                                </a>    
                            <span class="pull-right">&nbsp;&nbsp;</span>
                                
                                
                                <a href="ratebands_details.php?rateband_id='.$ratebands->getId().'" title="Edit Rate Band"><span class="glyphicon glyphicon-pencil pull-right rateband-countries" data-action="GET_RATEBANDS_COUNTRY"
                                data-ratebandid="'.$ratebands->getId().'" data-servicename="'.$serviceName.'" data-service="'.$serviceId.'" title="Show Countries"></span></a>
                        </div>';
                   
                }
               
            }
            else
            {
               echo '<div class="alert alert-danger">
                    No Rate band(s) found.
                        </div>';
            }
                
            echo '<br><br><br><div class="col-sm-12" style="text-align:center;">
                '.(($downloadRateband)=== true? '<a href="services.php?func=DOWNLOAD_ALL_RATEBANDS&service_id='.$serviceId.'&service_name='.$serviceName .'" class="btn btn-xs btn-primary" >Download  Rate Bands</a>' : '' ).'
                <a  href="ratebands_details.php?rateband_id=0&service_id='.$serviceId.'&courier_id='.$courierName .'" class="btn btn-xs btn-primary" >Add Rate Bands</a>
                '.(($downloadRateband)=== true? '<a  href="countries_zones.php?service_id='.$serviceId.'&courier_id='.$courierName .'"class="btn btn-xs btn-primary" >Assign Country</a>' : '' ).'
                '.(($downloadRateband)=== true? '<a  href="services.php?func=DOWNLOAD_ALL_RATEBANDS_COUNTRIES&service_id='.$serviceId.'&service_name='.$serviceName .'" class="btn btn-xs btn-primary"  >Download Assigned Country</a>' : '' ).'
                </div>'; 
            
            exit;
            //
               
        }
                
                
	    /*------------------------------------------------------------------------------*/
		// courier id passed to page
		$this->_courier_id = util_request("courier_id");
		if ($this->_courier_id < 0) util_redirect("couriers.php");

        // get courier details
		//$CorObj         = new Courier($this->_courier_id);
        //if ($CorObj->getId() != $this->_courier_id) util_redirect("couriers.php"); // check got correct courier

        // set the title
        $this->setTitle("Courier Services for " .$this->_courier_id );//$CorObj->getName());

        // get the services for this courier
        $CorObj         = new Courier();
		//$CorObj->setId('1');
		$CorObj->setName($this->_courier_id );
		//$this->_services = $CorObj->getServices("", false);
		
		$service			=	new ServiceFilter();
		$service->addCarrierFilter($this->_courier_id);
		$this->_services	=	$service->getList();
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 