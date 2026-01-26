<?php

// get settings
require_once("../includes/settings/config.inc.php");

@ session_start();
class Page extends BasePage
{
	private $error_list 		=	array();
	private $msg 				=	"";
	public $labelSaveOption 	=	false;
	private $labelFile 			=	null;
	private $percentageBar 		=	0;
	private $pass 				=	'';
	private $account 			=	'';
	protected function init()
	{
		
		$left_to_print = 0;

		$user = SessionManager::getUser();
		$filter = SessionManager::getConsignmentFilter();	
		$filter->addStatusFilter(Consignment::STATUS_READY_TO_PRINT);
		$filter->addAccountFilter($user->getUserAccount());		
		$filter->AddOrderByAccount();	
		$filter->AddOrderByID();
		
		$consignment_array = $filter->getColumnList('id');
		$left_to_print = sizeof($consignment_array);
		
		if($left_to_print == 0)
		{
			util_redirect ("../main/client_list.php");
		}
		
		
		$this->setTitle("Generating Labels");
		$this->msg = '<div class="clear" ></div>
  		<div class="main_formpage">
    	<h1 class="heading">Labels generation</h1><div class="form_container">
        <div class="gray_container">';
		$this->msg .= "Generating labels for " . $this->form_vars["consignment_total"] . " consignments.";
	
		
	}// end of function

	/***
	 * Gets the full page to the folder
	 */
	public function getFullPath($fileName)
	{
		$path = "../_assets/pdf/" . date("Y_m_d", time()) . "/";

		if (!file_exists($path)) @mkdir($path, 0775);

		return $path . $fileName;
	}
	/***
	 * Head section
	 */
	protected function renderHead()
	{
		?>
       <style> 
        	.bootbox {
				background-clip: padding-box;
				background-color: #fff;
				border: 1px solid rgba(0, 0, 0, 0.3);
				border-radius: 6px;
				box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
				left: 50%;
				margin-left: -280px;
				outline: 0 none;
				position: fixed;
				top: 10%;
				width: 560px;
				min-height: 130px;
				max-height: 170px;
				
			}
			bootbox .modal-body  {
					max-height: 400px;
					overflow-y: auto;
					padding: 15px;
					position: relative;
				}
				
			
			.bootbox.modal.fade.in {
				top: 10%;
			}
			.bootbox.modal.fade {
				top: -25%;
				transition: opacity 0.3s linear 0s, top 0.3s ease-out 0s;
			}
			.bootbox.fade.in {
				opacity: 1;
			}
        </style>
         <script src="../assets/JS/bootbox.js"></script>
		<script type="text/javascript">
		var testarr = new Array;
		var	progressBarTotalCount	=	0;
		var	progressBarCompletion	=	0;

		$(document).ready(function()
		{		
			$.ajax ({
			  type: "POST",
			  url: "bulkAjaxLabel.php",  // your php file name
			  async: false,
			  data: {action:'query'}, 
			  success:function(save)
			  {
				 
				  obj = JSON.parse(save);
				  
				  var i = 0;
				 
				  if(obj.error == "")
				  {
					  if(obj.serviceId != null)
					  {
						  progressBarTotalCount	=	obj.serviceId.length;
						 // alert(progressBarTotalCount);
						  while(i<=obj.serviceId.length)
						  {		
								  if(i==obj.serviceId.length)
								  {
									  $.ajax ({
										  type: "POST",
										  url: "bulkAjaxLabel.php",  // your php file name
										  async: false,
										  data: {action:'singleQuery', id:'', i:(i)}, 
										  success: process
										});
										
										$("#merging-status").html("Please wait, label are merging now...");
								  }
								  else
								  {
								  $.ajax ({
								  type: "POST",
								  url: "bulkAjaxLabel.php",  // your php file name
								  async: false,
								  data: {action:'singleQuery', id:obj.serviceId[i].id, i:(i)}, 
								  success: process
									});
									progressBarCompletion	=	Math.ceil(((i+1)/progressBarTotalCount)*100);
									//alert(progressBarCompletion);
									$("#progress-bar-new").attr('style','width:'+progressBarCompletion+'%; display:block');
									$("#progress-bar-completion").html(progressBarCompletion);
									
								  }
								i++;
						  }	
					  }
				  }
				  else
				  {
					//  alert(obj.error);
					  window.location.replace("../main/label_list.php");
				  }
			  }
			});		
		});
		
		
		$("#progress-bar-new").attr('style','width:0px; display:block');
			
			function process(data) 
			{					
					//alert(data);					
					
					var fileLink = data.split('||');					
					
					if(fileLink[0]=='Link')
					{
						if(fileLink[1]!= 'equal' )
						{
							testarr.push(fileLink[1]);
						}
					
						else if(fileLink[1]=='equal')
						{
							  mergeLabelArray = testarr.join('||');
							  labelId = 	fileLink[2];												  
							  $.ajax ({
							  type: "POST",
							  url: "bulkAjaxLabel.php",  // your php file name
							  data: {action:'Merge', mergeLabelArray:mergeLabelArray, labelId:labelId}, 
							  success:function(save)
							  {
								 // alert(save);
									var responStr	=	save.split('||');
									if(responStr[0] == 'ERROR')
									{
										  alert(save);
									}
									else
									{
										
										bootbox.dialog("A PDF Which Contains Labels For "+ responStr[1] +" Selected Shipments Has Been Created. Open The Next TAB To View Or to Print.", [{
											"label" : "Yes",
											"class" : "btn-success",
											"callback": function() {
												 var filelink = responStr[0].replace("../", "https://oneworldexpress.co.uk/remote/");
												window.open(filelink);
												window.location.replace("../main/label_list.php");
											}
										}, {
											"label" : "Cancel",
											"class" : "btn-primary",
											"callback": function() {
												window.location.replace("../main/label_list.php");
												return;
											}
										}, ]);
										
							  		//window.location.replace("../main/client_list.php?show=btnReceived");
									//window.location.replace("../main/label_list.php");
									}
							  }
							});
							
							testarr = new Array;
						}
					}
					
					
				}
				
				
				
				
		
		$(function() {
			$(".meter > span").each(function() {
				$(this)
					.data("origWidth", $(this).width())
					.width(0)
					.animate({
						width: $(this).data("origWidth")
					}, 1200);
			});
		});
	</script>
		<?php
	}

	/***
	* Content
	*/
	protected function renderBody()
	{
		// transfer form variables into local values (form variables come from parent)
		foreach ($this->form_vars as $key=>$val) {$$key = $val; }

		?>
		 <div class="clear" ></div>
         
          <div class="row">
        	<div class="col-md-12">  
	        	<img src="../images/1.png" alt="create shipment" style="padding-left:15px; width:900px;"  />
                </div>
        </div>
		<div class="main_formpage">
    	<h1 class="heading">
        </h1>
    	<div class="portlet box blue">
            <div class="portlet-title">
            	<div class="caption"> <i class="icon-docs"></i>
				<?php errorList::getItem()->render(); ?>
					Label Creation				</div>
         		<div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
	       	</div>
             <div class="portlet-body">
             	 <div class="row">
                 <div class="col-md-12">
                 	<p><?php echo $this->msg; ?></p>
                 </div>
                 </div>
                 <div class="row">
                 <div class="col-md-12">
                    <h2>please wait...</h2>
                     <div class="progress">
                          <div class="progress-bar" role="progressbar" aria-valuenow="100"
                          aria-valuemin="0" aria-valuemax="100" style="width:100%">
                            <span id="merging-status">Label Processing... (<span id="progress-bar-completion">0</span>%)</span>
                          </div>
                        </div>
                    </div>
                 </div>
                <div style="clear:both;"></div><br />
			    <div  class="row"  style="text-align:center;">  
                <input type="hidden" name="consignment_total" value="<?php echo @$consignment_total; ?>" />
                <input type="hidden" name="labelSaveOption" value="<?php echo @$labelSaveOption; ?>" />
                <input type="hidden" name="pass" value="<?php echo @$pass; ?>" />
                <input type="hidden" name="account" value="<?php echo @$account; ?>" />
        
                <input type="submit" name="btnNext" id="btnNext" style="display:none" />
    	        </div>
         </div>
         </div>
         
                
		


		

		

		
		<?php
		
	}
	public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::COURIERS);
    	$menu->render();
    }	

}// end class
/*------------------------------------------------------------------------------*/
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();