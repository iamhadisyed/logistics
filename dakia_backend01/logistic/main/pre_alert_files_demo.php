<?php
require_once("../includes/settings/config.inc.php");
class Page extends BasePage
{
	
	
    private $page_vars;
	private $bob;
	private $blockedExts = array("exe", "com", "scr", "bat","php","html","htm","php5","htaccess");
	

	
    public function init()
    {
        
       $error_list = ErrorList::getItem();
		$error_list->setPrelistMessage("Unable to upload consignment file:");
	    SessionManager::checkUserAccess(User::PRIVILEGE_CLIENT);
        t_on();

        $this->setTitle("Upload Pre Alert File");
		$user = SessionManager::getUser();
		$dir = __DIR__;
		//check the user session and make folder if it does not exist.
		if (isset($user))
		{
			
			if (file_exists ($dir. '/../_assets/preadvice/' .$user->getUsername()) == false)
			{
			@mkdir($dir. '/../_assets/preadvice/' .$user->getUsername(), 0777);
			@chmod($dir. '/../_assets/preadvice/' .$user->getUsername(), 0777);
			}
		}
       
//        $toolbar = Toolbar::getItem();
//        //$toolbar->showPrintOption($this->num_valid > 0);
//        $toolbar->showSearchOption();
//        $toolbar->showImportOption();
//        $toolbar->showLabelList();
//        $toolbar->showWarehousePage();
//        $toolbar->showAddConsignment();
//        $toolbar->showCSVOption();
//        $toolbar->showReleaseList();
//        $toolbar->showConsignmentList();
//        $toolbar->showSaveAddress();
//		$toolbar->showSearchOption();
//	    $toolbar->showAddUser();
//		$toolbar->showExportOption();
//		$toolbar->showEndOfDayOption();
//		$toolbar->showManifestList();
//		$toolbar->showLabelCreation();
//		$toolbar->showUserList();
		$uploadlog = new consignmentlog();
		//delete function
		if(@$_GET['action'] && @$_GET['action'] == 'delete') 
		{
			//check if warehouse
		if (Sessionmanager::getUser()->getUserType() == 'warehouse')
		{
			unlink($dir. '/../_assets/preadvice/' .$_GET['folder'].'/'.$_GET['filename']);
			$uploadlog->createlog($_GET['filename']. ' Has been deleted from ' .$_GET['folder'].' by warehouse',0,'U');
       		header('Location:pre_alert_files.php?action=folder&foldername='.$_GET['folder']);
			exit();
		} else {
			//normal user delete
       		unlink($dir. '/../_assets/preadvice/' .$user->getUsername().'/'.$_GET['filename']);
			$uploadlog->createlog($_GET['filename']. ' Has been deleted by ' .$user->getUsername(),0,'U');
       		header("Location:pre_alert_files.php");
       		exit();
			
			}
  	 	}
			//folder view warehouse check
		if (@$_GET['action'] && @$_GET['action'] == 'folder' && Sessionmanager::getUser()->getUserType() == 'warehouse')
		{
			//print message what folder they are viewing.
			$this->bob = "Viewing " .$_GET['foldername']. " Folder"; 
		}
		
        // Set up filter conditions and pass to list
        if (isset($_POST["form_action"]))
        {
            //check if post upload file         
            if ($_POST["form_action"]=="ok" || $_POST["form_action"]=="save" )
            {
				//expload the name to check blocked extension
				$temp = explode(".", $_FILES["upload"]["name"]);
				$extension = end($temp);
				//if its not in the blocked array allow it.
				if (!in_array($extension, $this->blockedExts))
					{
				//try make the dir and show no errors.
               // @mkdir($dir. '/../_assets/preadvice/' .$user->getUsername(), 0777);
				//@chmod($dir. '/../_assets/preadvice/' .$user->getUsername(), 0777);
				//generate path
  				$target_path = $dir. '/../_assets/preadvice/' .$user->getUsername().'/'. $_FILES['upload']['name'];
				//chmod folder for uploading.
            	if(move_uploaded_file($_FILES['upload']['tmp_name'], $target_path)) {
					chmod($target_path, 0777);
   					 $this->bob = "The file ".  basename( $_FILES['upload']['name']). 
   						 " has been uploaded!";
						 
						 
					$uploadlog->createlog(basename($_FILES['upload']['name']),1,'U');
					} else {
						
					//failed upload.
   					 $this->bob = "There was an error uploading the file, please try again!";	
				}
				//illegal file.	
            } else {
				$blist = '';
				foreach($this->blockedExts as $bext)
				{
				$blist .= '.'.$bext.' ';	
				}
				$this->bob = "Illegal file extension do not use any of the following extensions: " .$blist;
				} 
			}
			
        }
		
		if (@$_GET['action'] && @$_GET['action'] == 'continue' && Sessionmanager::getUser()->getUserType() == 'warehouse')
		{
			//print message what folder they are viewing.
		
			$account_number = $user->getUserAccount();
			$foldername = $_GET['foldername'];
			
			$this->file_name =  Page::IMPORT_FILE_DIR . $foldername ."/". $_GET['filename'];
			$file_object = new InputFileCon($this->file_name);
			
			
			$this->import_status = Page::STATUS_IMPORTING;
			$this->import_num_accepted = 0;
			$this->import_num_checked = 0;
			$this->import_num_rejected = "";
			
			if ($file_object == null)
			{
				$error_list->addError("Can't open file " . $this->file_name);
			}
			else
			{	
				/*if($this->file_position == 1)
				{
					$this->file_position += 1;
				}*/
				$file_object->setPosition($this->file_position);
				$more_rows = true;
				
				for ($row_idx = 0; $more_rows && ($row_idx < Page::IMPORT_BATCH_MAX); $row_idx++ )
				{
					
					if ($file_object->nextRow())
					{	
						if($this->file_position == 0  && $row_idx == 0 )
						{
							continue;
						}
						$this->importBooking($file_object, $account_number);
						
					}
					else
					{
						$more_rows = false;
					}
					
				}
				if ($more_rows)
				{
					$this->file_position = $file_object->getPosition();
					$this->import_msg = $this->import_num_checked . " bookings read.  Reading next 5 bookings ...";
					//$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					//header("Refresh: 10;url='".$actual_link."'");
					util_redirect($actual_link);
				}

				// no more rows
				else
				{
					
					$this->import_msg = $this->import_num_accepted . " bookings imported.";
					$this->import_msg .= $this->import_num_rejected;
					$this->import_status = Page::STATUS_IMPORTED;
					//
				}

				// close file
				unset($file_object);
			}
		}
		
	
		
		
		
           
        //util_redirect("warehouse_list.php");
       
    }

  
    public function renderHead()
    {
   ?>
    <style type="text/css">
   
div.pager {
    text-align: center;
    margin: 1em 0;
	padding:5px;
	margin: 20px auto;
	border-radius: 5px;
	background:#f2f3f5;
	border:1px solid #e8e8e8;
	width:80%;
	height:27px;
}

div.pager span {

	
		position: relative;
		float: left;
		text-align:center;
		padding: 5px 10px;
		line-height: 1.42857143;
		text-decoration: none;
		border-radius:5px;
		-moz-border-radius:5px;
		-webkit-border-radius:5px;
		background:#fff;
		box-shadow:2px 2px 0 #eee;
		color:#000;
		margin:0 2px;
}

div.pager span.active {
     font-weight:bold;

}


  </style>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>  
 <script>
 function email(file) {
	 
	 if (confirm('Are you sure you want to email this?')) {
      $.ajax({
           type: "POST",
           url: 'emailaj.php',
           data:{action:''+file+''},
           success:function(html) {
             alert(html);
           }
	 
      });
 }
  }
  
  $(function(){
$('table.paginated').each(function() {
    var currentPage = 0;
    var numPerPage = 20;
    var $table = $(this);
    $table.bind('repaginate', function() {
        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
    });
    $table.trigger('repaginate');
    var numRows = $table.find('tbody tr').length;
    var numPages = Math.ceil(numRows / numPerPage);
    var $pager = $('<div class="pager"><b> Total Records: ' + numRows +'</b></div>');
    for (var page = 0; page < numPages; page++) {
		
        $('<span class="page-number"></span>').text(page + 1).bind('click', {
            newPage: page
        }, function(event) {
            currentPage = event.data['newPage'];
            $table.trigger('repaginate');
            $(this).addClass('active').siblings().removeClass('active');
        }).appendTo($pager).addClass('clickable');
    }
	
    $pager.insertBefore($table).find('span.page-number:first').addClass('active');
	document.getElementById("totalrecords").innerHTML = "Total Records: " + numRows;
	//document.write('<p>Total Records: ' + numRows  + '</p>');
});

});//]]>
  


     </script>
        <?php
    }


    /***
     * Render the made body of page.
     */
    public function renderBody()
    {      
		?>
     <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <ul class="breadcrumb">
			<li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/pre_alert_files.php">Pre Alert</a></li>
			<li><a href="">Upload</a></li>
		</ul>
		
       <!-- <form action="report.php" method="post">-->
         <div class="clear" ></div>
 <div class="portlet box blue">
            <div class="portlet-title">
            	<div class="caption"> <i class="icon-users"></i>
				<?php errorList::getItem()->render(); ?> Upload Pre Alert Files	</div>
         		<div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
	       		</div>
                
            <div class="portlet-body">
         <div class="row">
              <div class="col-md-4">
            <label>Here you can upload your pre-alert files and email them to the Warehouse team.</label>
			 <? if(Sessionmanager::getUser()->getUserType() != 'warehouse') 
	{
	  ?>
        <label for="upload">Upload File:</label>
        <input type="file" name="upload" id="upload" class="form-control" />
        <br>
        
        
        
        
        <br>
        <? } ?>
        <input type="hidden" name="form_action" value="ok" />
        <? echo "<font color='red'>" .$this->bob. '</font><br>'; ?>  
	 
     </div>
	 
    </div>
	<!-- second portion -->
    <div class="row" style="text-align:center;">  
       <?		
		
		if(Sessionmanager::getUser()->getUserType() != 'warehouse') 
	{
	  ?>
      <div class="form_buttons"> 
      <a id="btnSave" href="#"  class="btn btn-primary btn_save"><span></span>Upload</a> 
   
     </div><br /><br /><br /><br />
   
		<table id='consignment_list' class="consignment_list_tbl" >
	
			<!-- table head -->
			 <thead>
							<tr>
								<th align="center">Filename</th>
								<th align="center">File Size</th>
                                <th align="center">Date/Time Created</th>
								<th align="center">Options</th>
							
							</tr>
			  </thead>

		   <? } else { 
		   //get username folder list if warehouse.
if (@$_GET['action'] && @$_GET['action'] == 'folder'  && Sessionmanager::getUser()->getUserType() == 'warehouse')
		{ ?>
        	<!-- table body -->
  
<table id='consignment_list' class="consignment_list_tbl">
	
			<!-- table head -->
			 <thead>
							<tr>
								<th align="center">Filename</th>
								<th align="center">File Size</th>
                                <th align="center">Date/Time Created</th>
								<th align="center">Options</th>
							
							</tr>
			  </thead>
        
         
        <? } else { ?>
			<!-- table body -->

  <table class="paginated" id='consignment_list'>
<!--<table  class="consignment_list_tbl">
	
			<!-- table head -->
			 <thead>
							<tr>
								<th align="center">Folder</th>
								<th align="center">Options</th>
							
							</tr>
			  </thead>
            
<? 
		}	
} ?>
	
     
        
       	<tbody >
            	<tr style="display: table-row;">
                
                
                   <? 
		
		$user = SessionManager::getUser();
		$dir = __DIR__;
		
		if(Sessionmanager::getUser()->getUserType() == 'warehouse' && @$_GET['action'] != 'folder' )
		{
			//check the dir for files uploaded.
		 	$dir = $dir. "/../_assets/preadvice/" .$_GET['foldername'];
			$dh  = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				$files[] = $filename;
			}
			asort($files);
			
			//go through the array of each file
			foreach ($files as $key => $val)
			{
				if(($val != '.') && ($val != '..'))
				 echo '<td>'.$val.'</td><td><a class="ebayButton" href="pre_alert_files.php?action=folder&foldername=' .$val. '">View Folder</a></td>
				</tr>';
			}
			
			/*for ($i = 0;$i < count($files);$i++)
			{
				if(($files[$i] != '.') && ($files[$i] != '..'))
					echo '<td>'.$files[$i].'</td><td><a class="ebayButton" href="pre_alert_files.php?action=folder&foldername=' .$files[$i]. '">View Folder</a></td>
				</tr>';
			}
			*/
		}
		else 
		{
			//viewing files of user as a warehouse user.
			if (@$_GET['action'] && @$_GET['action'] == 'folder'  && Sessionmanager::getUser()->getUserType() == 'warehouse')
			{
				$dir = $dir. "/../_assets/preadvice/" .$_GET['foldername'];
				$dh  = opendir($dir);
				while (false !== ($filename = readdir($dh))) {
   					$files[] = $filename;
				}
				
				asort($files);
				foreach ($files as $key => $val)
				{
					if(($val != '.') && ($val != '..'))
					{
						$file = "'".trim($val)."'";
						$userf = new userfilter();
						$userf->addUserNameFilter($_GET['foldername']);	
						$u = $userf->getList();
					
						$lfilter = new consignmentlogfilter();
						$lfilter->addUserIdFilter($u[0]->getID());
						$lfilter->addFilterUpload();
						$lfilter->addCidFilter(1);
						$lfilter->addMessageFilterExact($val);
						$fileinformation = $lfilter->getLogList();
						
						//if(count($fileinformation) > 0)
						{
							//<tr style="display: table-row;">
							echo '
							
								<td>' .$val. '</td>  
								<td>'.filesize($dir.'/'.$val).' Bytes</td> ';
							if(count($fileinformation )>0)
								echo '<td>'.date("d-m-Y H:i:s",@$fileinformation[0]->getLogDate()).'</td>';
							else
								echo '<td> </td>';
							echo '<td>
									<a class="ebayButton" href="pre_alert_files.php?action=delete&filename='.$val.'&folder='.$_GET['foldername'].'">Delete</a>
									<a class="ebayButton" href="./../_assets/preadvice/'.$_GET['foldername'].'/'.$val.'" target=_blank>Download</a> <a class="ebayButton" onclick="email('.$file.')" href="#">Email</a>
									<a id="btnSubmit" href="pre_alert_upload.php?action=continue&filename='.$val.'&foldername='.$_GET['foldername'].'" class="ebayButton"><span></span> Upload</a>
								</td>
							</tr>';
						}
					}
				}
			
	
			}
			else
			{
				//check the dir for files uploaded. as a normal user
				$dir = $dir. "/../_assets/preadvice/" .$user->getUsername();
				$dh  = opendir($dir);
				while (false !== ($filename = readdir($dh)))
				{
					$files[] = $filename;
				}
				
				asort($files);
				foreach ($files as $key => $val)
				{
					if(($val != '.') && ($val != '..'))
					{
						$lfilter = new consignmentlogfilter();
						$lfilter->addUserIdFilter($user->getID());
						$lfilter->addCidFilter(1);
						$lfilter->addFilterUpload();
						$lfilter->addMessageFilterExact($val);
						$fileinformation = $lfilter->getLogList();		
						
						if(count($fileinformation) > 0)
						{
							$file = "'".trim($val)."'";
							echo '<td>' .$val. '</td>  <td>'.filesize($dir.'/'.$val).' Bytes</td><td>'.date("d-m-Y H:i:s",$fileinformation[0]->getLogDate()).'</td>
										<td><a class="ebayButton" href="pre_alert_files.php?action=delete&filename='.$val[$i].'">Delete</a> 
										<a class="ebayButton" href="./../_assets/preadvice/'.$user->getUsername().'/'.$val[$i].'" target=_blank>Download</a>
										 <a class="ebayButton" onclick="email('.$file.')" href="#">Email</a></td>
									</tr>';
						}
					}
							
				}
	

		}
		
		}
		
?>
                    
                  
			</tbody> 
        	</table>
        <? if(Sessionmanager::getUser()->getUserType() == 'warehouse') { ?>
        <div class="form_buttons">    
         <a id="btnCancel" href="pre_alert_files.php" class="btn btn-danger btn_cancel"><span></span>Go Back</a>	 <a id="btnSearch" href="logs3.php" class="btn_label"><span></span>Search</a>	
         </div><? } ?> 
    </div>
		
    </div>
	</div>
	
        </form>
        <?php
    
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
// create and show page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
