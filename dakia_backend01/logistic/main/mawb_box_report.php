  <?php
  ////////////////////////////////////////////////////
  //
  // Controller for Admin - Index page
  //
  ////////////////////////////////////////////////////
  // get settings
  //Accounts Report
  //Operation Reports
  //Aging Statistic Report
  
  require_once("../includes/settings/config.inc.php");
  
  // set up local page class
  class Page extends BasePage {
  
	  private $errorList;
	  public $mawb_data;
	  private $carrier_list;
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
	<!--<div class="col-md-8">
	  <h3 class="page-title">Sorter Error Report</h3>
	</div>-->
	<div class="col-md-4"> 
	  <!--<a href="reporting_user_fullscreen.php" target="_blank" class="btn btn-success pull-right">Show Full Screen</a>--> 
	</div>
  </div>
  <div class="portlet box blue">
	  <div class="portlet-title">
	   <div class="caption"> <i class="glyphicon glyphicon-search"></i>Exception Report </div>
	   <div class="tools"> <a href="" class="fullscreen"> </a> <a href="javascript:;" class="collapse"></a> </div>
	  </div>
	<div class="portlet-body">
		 <div class="row">
		  <div class="col-md-3" style="padding-left:15px;">
			 
              <?php
              	if(count($this->mawb_data) > 0)
				{
					$month = date("M",strtotime("-1 month"));
					$thismonth = date("M");
					if($month != $thismonth)
						$mawbmonth = $month . "-" . $thismonth;
					else
						$mawbmonth = $thismonth;
					?>
                    <table class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                    	<th> MASTER NUMBER </th>
                        <th align="right"><? echo $mawbmonth; ?></th>
                    </thead>
                     <tbody>
					<?
					foreach($this->mawb_data as $mawb)
					{
					?>
                    	<tr>
                            <td colspan="2">
                            <a href="#" onclick="loadBoxdetails('<? echo $mawb->getMawb(); ?>')"  class="table table-striped table-bordered table-advance table-hover" ><? echo $mawb->getMawb(); ?>(<? echo $mawb->getId() ?>)</a>
                            </td>
                        </tr>
					<?
					}
					?>
                    </tbody>
                    </table>
                    <?
				}
			  ?>
		  </div>
		  <div class="col-md-9" >
			<div class="scroller" data-rail-color="blue" data-handle-color="blue" style="border-style: solid;    border-width: 1px;">
			<div class="row" style="padding:15px;">
		  	<div class="col-md-3">
                <div class="form-group">
                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                     <select name="carrier" id="carrier" class="form-control">
                      <option value="">Carrier Name</option>
                      <?php
                                            if (count($this->carrier_list) > 0) {
                                                foreach ($this->carrier_list as $carrierObj) {
                                                    $carrier = trim($carrierObj->getCarrier());
                                                    echo '<option value="' . $carrier . '"' . ($carrier == $this->carrier ? ' selected="selected"' : '') . '>' . $carrier . '</option>';
                                                }
                                            }
                                            ?>
                    </select>
                    </div>
                </div>
			</div>
			<div class="col-md-3">
                <div class="form-group">
                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                      <select id = "box_type" name="box_type" class="form-control">
                          <option value="">Box Type</option>
                          <option value="normal">normal</option>
                          <option value="mix">mix</option>
                     </select>
                    </div>
                </div>
		  </div>
		  <div class="col-md-3">
			<div class="form-group">
			  <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
			  <select id = "searchreason" name="searchreason" class="form-control">
				  <option value="">Reason</option>
				  <option value="too late">Too late</option>
				  <option value="mix carton">mix carton</option>
				  <option value="weekend">weekend</option>
				  <option value="this channel should be handled tomorrow">this channel should be handled tomorrow</option>
				  <option value="other reason">other reason</option>
			 </select>
			  </div>    
		  </div>
		  </div>
		  </div>
		  <div class="col-md-3">
			<input type="hidden" name="form_action" id="form_action"  />
			<button type="button" name="btnSearch" id="btnSearch" class="btn btn-primary btn-blcok" style="margin-bottom: 5px;">Search</button>
			<!--<button type="button" name="btnExport" id="btnExport" class="btn btn-default btn-blcok">Export</button>-->
            <input type="hidden" id = 'mawbno' value="" />
          
		  </div>
          </div>
           <div class="portlet box blue">
              <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>Result </div>
                <div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
              </div>
              <div class="portlet-body">
                <div class="scroller" style="min-height:600px;"  data-rail-color="blue" data-handle-color="blue" id="rpt_container">
                  <div class="table-bordered" id="rpt_tbl_container">
                   
                  </div>
                 
                </div>
                
              </div>
            </div>
		 </div>
		 </div>
	</div>
  </div>
   <div class="modal fade" tabindex="-1" role="dialog" id="reason-log" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Reason</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="consignment_id" id="consignment_id" value="<?php echo $this->id; ?>" />
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover" >
                                    <tbody>
                                    	<tr>
                                        	<td> 
                                                <select id = "reason" name="reason" class="form-control" onChange="setEnterData(this.value);">
                                                  <option value="">Reason</option>
                                                  <option value="too late">Too late</option>
                                                  <option value="mix carton">mix carton</option>
                                                  <option value="weekend">weekend</option>
                                                  <option value="this channel should be handled tomorrow">this channel should be handled tomorrow</option>
                                                  <option value="other reason">other reason</option>
                                                 </select>
                                             </td>
                                        </tr>
                                        <tr>
                                        	<td>
                                                <div id="otherreason" >
                                                 <label>Enter Reason :</label>
                                                    <input id="other" type="text"   name="other"  value='<?php echo @$other; ?>' class="form_field_coll1">
                                                        
                                                </div>
                                            </td>
                                        </tr>
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                    	<button type="button" name="btnadd" id="btnadd" class="btn btn-primary btn-blcok" style="margin-bottom: 5px;" onClick="saveReason();">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="btnclose">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
  
  
  <!-- END PAGE CONTENT-->
  <?php
	  }
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
		  
		  	 $(document).ready(function() {
				if(typeof($("#reason")) !== 'undefined' &&  $("#reason").val() == "other reason")
					$('#otherreason').show();
				else
					$('#otherreason').hide();
				
                $("#btnSearch").click(function(){
					
                     mawb = document.getElementById('mawbno').value;
					 carrier = document.getElementById('carrier').value;
					 boxtype = document.getElementById('box_type').value;
					 reason = document.getElementById('searchreason').value;
					 
					 $.ajax({
						url: "ajaxmawbreport.php",
						type: "POST",
						async: false,
						data: {
							action: 'GETBOXSEARCH',
							mawb: mawb,
							carrier: carrier,
							boxtype: boxtype,
							reason: reason,
						},
						success: function(data) {
							$('#rpt_tbl_container').html(data);
						}
					});
                });
			 });
		  	function loadBoxdetails(mawb)
			{
				document.getElementById('mawbno').value = mawb;
				 $.ajax({
                    url: "ajaxmawbreport.php",
                    type: "POST",
                    async: false,
                    data: {
                        action: 'GETBOXDETAILS',
                        mawb: mawb,
                    },
                    success: function(data) {
						$('#rpt_tbl_container').html(data);
					}
                });
				
			}
			function saveReason()
			{
				var addreason	= document.getElementsByName("addreason[]");
				 mawb = document.getElementById('mawbno').value;
				var txt = "";
				var txt1 = "";
				var value = "";
				for (i = 0; i < addreason.length; i++) {
				  if (addreason[i].checked) {
					value = addreason[i].value;  
					 var bag_numebr_handlin = value.split("||");
					txt = txt + bag_numebr_handlin[0] + ",";
					txt1 = txt1 + bag_numebr_handlin[1] + ",";
				  }
				}
				bagnumber = txt.replace(/,\s*$/, "");
				service = txt1.replace(/,\s*$/, "");
				
				reason = document.getElementById('reason').value;
				if(reason == 'other reason')
				{
					reason =  document.getElementById('other').value;
				}
				 $.ajax({
                    url: "ajaxmawbreport.php",
                    type: "POST",
                    async: false,
                    data: {
                        action: 'SAVEREASON',
                        reason: reason,
						bagnumber: bagnumber,
						service: service,
                    },
                    success: function(data) {
						$("#btnclose").click();
						loadBoxdetails(mawb);
					}
                });
				
			}
			
			function setEnterData(rea)
			{
				if(rea == 'other reason')
				{
					$('#otherreason').show();
				}
				else
				{
					$('#otherreason').hide();
				}
			}
		  </script>
		  
  
  <?php
	  }
  
	  /*     * *
	   * Controller logic goes here
	   */
  
	  public function init() {
		  
		$servicesFilterObj = new ServiceFilter();
        $servicesFilterObj->getUniqueCarrierfilter();
        $this->carrier_list = $servicesFilterObj->getColumnList('carrier'); 
		 	
		$consignmentFilter = new ConsignmentFilter();
		$this->mawb_data = $consignmentFilter->getTodaysMawb();	  
	 }
	  
  }
  /* ------------------------------------------------------------------------------ */
  // create and render page
  $PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
  $PageObj->show();
  ?>
