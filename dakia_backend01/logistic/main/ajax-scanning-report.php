<?php
// get settings
require_once("../includes/settings/config.inc.php");

@session_start();

/***
 * Page for editing a user
 */
class Page extends BasePage
{
	private $userServiceTypeNew;
	
	/***
	* Controller logic
	*/
	protected function init()
	{
		//if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] != USER::USER_TYPE_CORPORATE_CLIENT )
			Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
			$sessionUser = SessionManager::getUser();	
		//
		
		$mawbNumber		=	array();	
		$conMawbFilter	=	new	ConsignmentFilter();
		if(count($conMawbFilter->getTodayMawb()) <= count($_SESSION['MAWB_NUMBER']))
		{
			unset($_SESSION['MAWB_NUMBER']);
		}
		if(!isset($_SESSION['MAWB_NUMBER']))
		{
			$getConsignmentMawbData	= $conMawbFilter->getTodayMawb();
			if(count($getConsignmentMawbData)>0)
				$_SESSION['MAWB_NUMBER'][]	= $getConsignmentMawbData[0]->getMawb();
					
		}
		else
		{
			$getConsignmentMawbData	= $conMawbFilter->getTodayMawbWithSession($_SESSION['MAWB_NUMBER']);
			if(count($getConsignmentMawbData)>0)
				$_SESSION['MAWB_NUMBER'][]	= $getConsignmentMawbData[0]->getMawb();
		}
	}
	
	/*******************************/
	/***	defaultrouting        **/
	/*******************************/
/***
	 * Insert content in to HTML Head section
	 */

	
	protected function renderHead()
	{
	?>
	<script>
	function getMawbNumbers()
	{
		$('#ajax-mawblist').show();
		$('#ajax-current-mawb-summary').show();
		$('#ajax-user-summary').show();
		 $.post("ajax-scan-mawb-list.php", {},function(mawbContents,status){
			if(status == 'success')
			{
				$('#mawblist').html(mawbContents);
				$('#ajax-mawblist').hide();
 			}
            
        });
		
		$.post("ajax-scan-mawb-summary.php", {},function(mawbContents,status){
			if(status == 'success')
			{
				$('#current-mawb-summary').html(mawbContents);
				$('#ajax-current-mawb-summary').hide();
 			}
            
        });
		
		$.post("ajax-scan-mawb-user-summary.php", {},function(mawbContents,status){
			if(status == 'success')
			{
				$('#user-summary').html(mawbContents);
				$('#ajax-user-summary').hide();
 			}
            
        });
		setTimeout(function(){
					getMawbNumbers();
				}, 300000);
		
	}
	
	function getMawbHistory()
	{
		 
		
		
	}
	function getMawbUserHistory()
	{
		 
		
		
	}
	
	
	
	
	
	
	
	
	
	$(document).ready(function(e) {
        setTimeout(function(){
					getMawbNumbers()
				}, 3000);
				;
		//getMawbHistory();
		//getMawbUserHistory();
    });
    </script>
    
    <script type="text/javascript">
(function blink() { 
    $('.blink_me').fadeOut(500).fadeIn(500, blink); 
})();
</script>
	<?php
	}
	/***
	* Content View
	*/
	protected function renderBody()
	{
            
		//print_r(@$_SESSION['MAWB_NUMBER']);
		if (isset($_SESSION['MAWB_NUMBER']) && (count($_SESSION['MAWB_NUMBER']) > 0))
		{
			$mawbNumber	=	$_SESSION['MAWB_NUMBER'][(count($_SESSION['MAWB_NUMBER'])-1)];
		}
		else
			$mawbNumber	=	'';
	?>
    <br />
		<div class="main_formpage"> 
		<style>
		.summary{
			width:100%;
			color: hsl(239, 100%, 20%);
			font-family:Arial, Helvetica, sans-serif;
			font-size: 25px;
			font-weight: normal;
			padding: 5px;
	
	
		}
		td{
			padding-left:5px !important;
		}
		.summary .current-mawb{
			width:50%;
			
			
		}
		.summary .current-mawb-summary{
		}
		.current-mawb-summary .section50{
			border:#093 1px solid;
			width:50%;
			float:left;
			
		}
		
		.title-mawb{
			font-size:25px;
			font-weight:bold;
		}
		.main_container{
			max-width:95%;
		}
        </style>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <div class="portlet box blue">
            <div class="portlet-title">
            	<div class="caption"> <i class="icon-bar-chart"></i>
				<?php errorList::getItem()->render(); ?> Warehouse Scanning</div>
         		<div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
	       		</div>
                
            <div class="portlet-body">
         <div class="row">
	         <div class="col-md-12" style="text-align:center; color:#F00">Note: This report will be refreshed after 5 mins</div>
         </div>
         <div class="row">
              <div class="col-md-12">
            <label></label>
			 <div class="summary">
           <div style="position:relative;">
            <img id="ajax-mawblist" width="50" src="loading.gif" style="left: 300px; position: absolute;top: 65px;">
               <img id="ajax-current-mawb-summary" width="50" src="loading.gif" style="right: 300px; position: absolute;top: 65px;">
           </div>    
                <table width="100%" class="table table-striped table-bordered table-advance table-hover">
              <tr>
                <td class="current-mawb" valign="top" id="mawblist">
               
                </td>
                <td class="current-mawb-summary" id="current-mawb-summary">
					<?php /*?>/*<iframe src="scan-mawb-summary.php?mawbSelected=<?php echo $mawbNumber;?>"style="background: none repeat scroll 0 0 hsla(0, 0%, 0%, 0); border: medium none; height: 200px;width: 100%;" scrolling="no">
					</iframe>* /<?php */?>
                       
                    
                </td>
              </tr>
            </table>
	        </div>
            <div style="position:relative;">
            <img id="ajax-user-summary" width="50" src="loading.gif" style="position: absolute; top: 65px; left: 640px;">
            </div>
            <div class="summary" style="font-size:24px !important;" id="user-summary">
             <?php /*?> <iframe src="scan-mawb-user-summary.php?mawbSelected=<?php echo $mawbNumber;?>"style="background: none repeat scroll 0 0 hsla(0, 0%, 0%, 0); border: medium none; width: 100%; height:auto;" scrolling="no">
                         
              </iframe><?php */?>
            </div>
            
		<input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" /> 
     </div>
	
    </div>
    <div class="row" style="text-align:center;"> 
			
	 </div>
        </div>
		
    </div>
	</div>
	
        
			
        
		<?php
	//	$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		//header("Refresh: 60;url='".$actual_link."'");
	}
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