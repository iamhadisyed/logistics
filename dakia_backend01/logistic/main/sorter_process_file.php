<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Country details page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $error_msg = "";
	private $matchfile = array();

	/***
	* This page's content
	* @return void
	*/
	public function renderBody()
	{
		?>
		<div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-gift"></i>Send Data To Sorter
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                
			<form method="post">
				<?php
				if ($this->error_msg != "")
				{
					?>
					<div class="note note-success"><?php echo $this->error_msg ?></div>
					<?php
				}
				  if(sizeof($this->matchfile) > 0)
				  {
				?>
            	  <fieldset id="fld_times_details">
                      
                  <div class="row">
            <?
						foreach($this->matchfile as $sorter_file)
						{	  
				 
				?>
          
                   
                            <div class="col-md-3">
                               <div class="form-group">
                                  <div class="input-group"> 
                                    <? echo $sorter_file; ?>
                                  </div>
                                </div>
                              </div>
                 
                   
            <? 
						}
				?>
            		 </div>
            	   <div class="row">
                          <div class="col-md-3">
                               <div class="form-group">
                           <button type="submit" class="btn btn-primary" id="btn_save" name="btn_save" value="Save Changes">
                                       Send Data
                                  </button>
                           </div>
                         </div>
                  </div>
                  </fieldset>
            <?
				 } ?>
			</form>
				</div>
        	</div>
        </div>
        
		<?php
	}

	/**
	* Override to show the menu
	*
	*/
	public function renderMenu()
	{
		$menu = new Adminmenu(Adminmenu::CURRENCY);
		$menu->render();
	}

	/***
	* Controller logic goes here
	*/
	
	
 
	
	public function init()
	{
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		
		/*------------------------------------------------------------------------------*/
		// get all file list
		$ftp_server = "164.39.218.210";
				

		// set up basic connection
		$conn_id = ftp_connect($ftp_server, 2100);
		if(!$conn_id)
		{
			echo "connection failed";
		}
		else
		{
			//echo "success";
			// login with username and password
			$login_result = ftp_login($conn_id, "optimus","Optimus123@");	
			
			ftp_pasv($conn_id, true);
		// get contents of the current directory
			$arrfile =  ftp_nlist($conn_id, "Outbox/Error");
			
			if(sizeof($arrfile) > 0)
			{
				foreach($arrfile as $filename)
				{
					$file_start = "SEPI";
					$bag_file_start = "SECC";
					if(strpos($filename, $file_start) !== false || strpos($filename, $bag_file_start) !== false)
					{
						if(strpos($filename, "OK") !== false)
						{
							$filename = str_replace("Outbox/Error", "", $filename);
							$this->matchfile[] = str_replace(".OK","",$filename);
						}
					}
				
				}
			}
		}
		
			
		
		/*------------------------------------------------------------------------------*/
		// process form
		if (isset($_POST['btn_save']))
		{
			
		}

		

		
		
	}


}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 