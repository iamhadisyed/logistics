<?php

////////////////////////////////////////////////////
//
// List of tariffs
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $page_vars;
	private $error;
	private $uploadMessage;
	private $debug = false;

	/***
	 * Controller logic goes here
	 */
	public function init()
	{
		//echo  $size = (int)$_SERVER['CONTENT_LENGTH'];
		t_on();

		$this->error = "";
		$load_all = false;
		
		
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		$this->setTitle("Upload Remote Area Postcodes");

		if (!isset($_POST['btn_upload']))
		{
			$this->page_vars = array();
		}
		else
		{
			$this->page_vars = util_getPostArray();
		}
	}


	private function uploadFile ($debug_flag)
	{
		if (!isset($_POST['btn_upload'])) return false;
	
		$FilObj = new Fileupload($_FILES["remote_area_file"]);

		if ($FilObj->uploaded)
		{
			$fileName = "";
		 	$file = "../_assets/remotearea/";
		 	$FilObj->process($file);

		 	if ($FilObj->processed)
		 	{
	 			$fileName = $FilObj->file_dst_pathname;
		 	}

		 	if ($fileName == "")
		 	{
		 		$this->error .= "Error uploading file<br />";
		 	}
		}
		else
		{
			$this->error .= "No upload file given.<br />";
		}

		//
		if ($this->error != "") return false;

		$tariffUpdate = new RemoteAreaUpload($fileName);
		$tariffUpdate->setDebugMode($debug_flag);
        $tariffUpdate->uploadAll();
        return true;
	}
	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		$p = $this->page_vars;

		if ($this->uploadFile($this->debug))
		{
			// Send to edit form
			$page = "remoteareas.php";

			if ($this->debug)
			{
				echo "<p><a href=\"" . $page . "\">Return to Remote Area</a></p>";
			}
			else
			{
				echo "<p>Upload complete.  Click <a href=\"" . $page . "\">here</a> to continue.</p>";
				//util_redirect ($page);
			}
		}
		else
		{
			?>
			<div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-gift"></i>Upload Remote Area
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
                <div class="scroller" style="min-height:200px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
								
				<?php
				if ($this->error != "")
				{
					?>
					<p class="error-block"><?php echo $this->error; ?></p>
					<?php
				}
				else
				{
					echo "<br />";
				}
				?>

				<div class="row" style="text-align:center; padding-left:10px;">
                     <div class="form-group col-sm-10 col-l-3">
                        <label>Please Upload Remote Area File:</label>
                        <input class="form-control" id="remote_area_file" name="remote_area_file" type="file" value="">
                      </div>
                </div>
				<div class="row" style="text-align:center; padding-left:10px;">
                             <button type="submit" class="btn btn-primary" id="btn_uploade" name="btn_uploade" value="Upload">
                                Upload
                             </button>
                   </div>
				<input type="hidden" name="action" id="action" value="save" />
				
					</div>
	            </div>
            </div>
			<?php
		}
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
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 