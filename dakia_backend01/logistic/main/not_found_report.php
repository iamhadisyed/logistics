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

    private $list;
	private $date_scanned_from;
	private $date_scanned_to;
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
            <div class="col-md-8"> 
                <h3 class="page-title">Services Transaction Report</h3>
            </div>
            <div class="col-md-4"> 
                <a href="reporting_services_fullscreen.php" target="_blank" class="btn btn-success pull-right">Show Full Screen</a>
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Panel </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" data-rail-color="blue" data-handle-color="blue">
                    <div class="row">
                      
                        <div class="col-md-2">
                            <label class="control-label">Date From</label>
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_scanned_from" id="date_scanned_from" type="text" placeholder="Date Form" value="<?php echo $this->form_vars["date_scanned_from"]; ?>" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">Date To</label>
                            <div class="form-group  date-date-pic">
                                <input class="form-control" name="date_scanned_to" id="date_scanned_to" type="text" placeholder="Date To" value="<?php echo $this->form_vars["date_scanned_to"]; ?>" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">&nbsp;</label><br />
                            <input type="hidden" name="form_action" id="form_action"  />
                            <button type="button" name="btnSearch" id="btnSearch" class="btn btn-primary">Search</button>
                            <button type="button" name="btnExport" id="btnExport" class="btn btn-primary">Export</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-users"></i>Result
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
                    <div class="table-bordered">
                        <table class="table table-striped table-bordered table-advance table-hover">
                            <?php
							   if(count($this->list) > 0)
							   {
								  
                                ?>
                            <thead>
                                <tr>
                                    <th>Tracking Number  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Record Found: <?php echo count($this->list) ?></th>
                                    <th>Mawb</th>
                                    <th>Scanned By</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
							   foreach($this->list as $record)
								   {
									   ?>
                                       
                                        <tr class="carrier_services_tr carrier_services_<?php echo $code;?>">
                                            <td><?php echo $record->getTrackingNumber() ; ?></td>
                                            <td><?php echo $record->getMawb() ; ?></td>
                                            <td><?php echo $record->getScannedBy() ; ?></td>
                                          </tr>
                                 
                                    <?php
								   }
							    }else{
                                ?>
                                    <tr>
                                        <td colspan="4">No Record Found.</td>
                                    </tr>   
                                <?php 
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
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
        <style type="text/css">
            #counterWiseRptNoModal{z-index: 99999999}
        </style>
        <script type="text/javascript" src="../_assets/js/jquery.chained.min.js"></script>
        <script type="text/javascript">
          
            $(document).ready(function() {
                $("#date_scanned_from").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});
                $("#date_scanned_to").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: '../../app_theme/images/calendar.gif', buttonImageOnly: true});                
            
			$("#btnSearch").click(function()
			{
				
				$("#form_action").val("Search");
            	$("#adminForm").submit();
			});
			$("#btnExport").click(function()
			{
				
				$("#form_action").val("Export");
            	$("#adminForm").submit();
			});
			
			
			    
            });
        </script>
      
        <?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
		
		
        	if($_POST["form_action"]  == "Search")
			{
				$this->date_scanned_from  				= $_POST['date_scanned_from'];
				$this->date_scanned_to  					= $_POST['date_scanned_to'];
				$NotFoundRecordFilter = new NotFoundRecordFilter();
				$NotFoundRecordFilter->addDateCreatedFilter(date("Y-m-d",strtotime($this->date_scanned_from)) , date("Y-m-d",strtotime($this->date_scanned_to)));
				$this->list = $NotFoundRecordFilter->getColumnList('tracking_number, mawb, scanned_by');
				
				
			}
			else if($_POST["form_action"]  == "Export")
			{
			
				$this->date_scanned_from  				= $_POST['date_scanned_from'];
				$this->date_scanned_to  					= $_POST['date_scanned_to'];
				$NotFoundRecordFilter = new NotFoundRecordFilter();
				$NotFoundRecordFilter->addDateCreatedFilter(date("Y-m-d",strtotime($this->date_scanned_from)) , date("Y-m-d",strtotime($this->date_scanned_to)));
				$this->list = $NotFoundRecordFilter->getColumnList('tracking_number, mawb, scanned_by');
				if(count($this->list) > 0)
				{
					$csv = "";
					$cr = "\r\n";
					$count = 1;
					
					$csvHeader = "Tracking Number, mawb, scanned By\r\n";
					
					
					$NumberofUniqueCode = 0;
					
					foreach($this->list as $notFound)
					{
					
						
					
						$csv  .=  $count . ",";
						
						
						$csv  .=  "=\"". str_replace(",", " ",$notFound->getTrackingNumber())   . "\""    . ",";		
						$csv  .=  "=\"". str_replace(",", " ",$notFound->getMawb())   . "\""    . ",";		
						$csv  .=  "=\"". str_replace(",", " ",$notFound->getScannedBy())   . "\""    . ",";		
						
						$csv  .=  $cr;	
						$count +=1;		
					}
					
				
					$folder_path    = "../_assets/not_fount_report/";
					
					if (!file_exists($folder_path)) {
						mkdir($folder_path, 0777, true);
					}
					
					$uniqueFileName = uniqid();
						
					$file_path    = $folder_path ."/" . $uniqueFileName;
					
					$file_path = $file_path .  ".csv";
					$file_path = fopen($file_path, 'w');
			 
					fwrite($file_path, $csvHeader . $cr . $csv);
				  
			   // close file
					fclose($file_path);
					$data = $csvHeader . $cr . $csv;
					echo $data;
					
					header("Content-Type: application/csv");
	
					header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");
							
					
					exit;
				}
				
				
			}
       
        
    }
}
/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?> 