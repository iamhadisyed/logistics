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

	private $id = NULL;
	private $iso = NULL;
	private $name = NULL;
	private $printable_name = NULL;
	private $iso3 = NULL;
	private $numcode = NULL;
	private $has_postcodeq = NULL;
	private $action = NULL;
	private $customs_flag;
	private $vat_flag;
	private $sort_priority = 2;
	private $timezone_diff = NULL;
	private $export_flag = NULL;
	private $description = NULL;
	private $pagetitle = NULL;
	private $metakeywords = NULL;
	private $metadescription = NULL;
	private $CountryImage = NULL;
	private $countrybanner = NULL;

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
                    <i class="glyphicon glyphicon-gift"></i>Country Detail
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
				?>
				<fieldset id="fld_times_details">
                <div class="row">
                    <div class="col-md-3">
                         <div class="form-group">
                    		<label>Country Name: </label>
							<input class="form-control" id="name" name="name" type="text" value="<?php echo $this->name;?>" />
        	            </div>
                   </div>
                   <div class="col-md-3">
                         <div class="form-group">
                    		<label>ISO: </label>
							<input class="form-control" id="iso" name="iso" type="text" value="<?php echo $this->iso;?>" />
        	            </div>
                   </div>
                   <div class="col-md-3">
                         <div class="form-group">
                    		<label>Iso 3: </label>
							<input class="form-control" id="iso3" name="iso3" type="text" value="<?php echo $this->iso3;?>" />
        	            </div>
                   </div>
                   <div class="col-md-3">
                         <div class="form-group">
                    		<label>Region: </label>
                            <select  id="region" name="region" class="form-control" >
                                <option value="DBP" <?php  echo ($this->region == 'DBP') ? 'selected="selected"' : '';?>>United Kindom</option>
                                <option value="R1" <?php  echo ($this->region == 'R1') ? 'selected="selected"' : '';?>>Europe</option>
                                <option value="INT" <?php  echo ($this->region == 'INT') ? 'selected="selected"' : '';?>>International</option>
                            </select>
        	            </div>
                   </div>
               </div>
				<div class="row">
                    <div class="col-md-3">
                         <div class="form-group">
                    		<label>Type: </label>
                            <select id="type" name="type"  class="form-control" >
                                <option value="C" <?php  echo ($this->type == 'C') ? 'selected="selected"' : '';?> >Collecton</option>
                                <option value="" >Dispatch</option>
                            </select>
        	            </div>
                   </div>
				</div>
                <div class="row">
                    <div class="col-md-3">
                         <div class="form-group">
                    		<a href="countries.php" class="btn btn-primary"><i class="glyphicon glyphicon-chevron-left"></i>Country List</a>&nbsp;&nbsp;&nbsp;
							<button type="submit" class="btn btn-primary" id="btn_save" name="btn_save" value="Save Changes">
                                	Save changes
                            </button>
        	            </div>
                   </div>
				</div>
				</fieldset>
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
	
	
 public function UploadSingleFile($FormFileFieldName,$FileName,$DirPath,$SizeLimit)
 {
        if(  $_FILES[$FormFileFieldName]['name'] != '' and
             $_FILES[$FormFileFieldName]['size'] != 0  and
             $_FILES[$FormFileFieldName]['size'] <= $SizeLimit)
         {

               if(!file_exists($DirPath))
                   mkdir($DirPath,0777);

               $orig_name=$_FILES[$FormFileFieldName]['name'];

               //echo "$orig_name <BR>";

                if($FileName!="")
                {
                        list($fname,$ext)=explode(".",$orig_name);
                        $FilePath = $DirPath.$FileName.".".$ext;
                }
                else
                {
                        $FilePath = $DirPath.$orig_name;
                }
				//print $FilePath;
                
                move_uploaded_file($_FILES[$FormFileFieldName]['tmp_name'], $FilePath);
                @chmod($FilePath,0777);
                
                print_r($_FILES);

                return $FilePath;
         }

         return "";
 }
	
	
	public function init()
	{
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		
		/*------------------------------------------------------------------------------*/
		// get vars
		$this->id = (isset($_REQUEST['country_id']) ? strip_tags($_REQUEST['country_id']) : '');
		
		/*------------------------------------------------------------------------------*/
		// process form
		if (isset($_POST['btn_save']))
		{
			//print_r($_POST);
			$this->name		=	$_POST['name'];// => Afghanistan
			$this->iso		=	$_POST['iso'];// => AF
			$this->iso3		=	$_POST['iso3'];// => AFG
			$this->type		=	$_POST['type'];// => AFG
			$this->region	=	$_POST['region'];// => INT

	
			if ($this->validate_form())
			{

				$CouSavObj = new Country($this->id);
				
				$CouSavObj->setName($this->name);
				$CouSavObj->setIso($this->iso);
				$CouSavObj->setIso3($this->iso3);
				$CouSavObj->setType($this->type);
				$CouSavObj->setRegion($this->region);
				$CouSavObj->save();

				// go to courier list
				util_redirect("countries.php");
			}
		}

		/*------------------------------------------------------------------------------*/
		// process delete
		if ($this->action == "confirmed_delete")
		{
			// save times and details
			$CouDelObj = new Conutry($this->id);
			$CouDelObj->setDeletedq('Y');
			$CouDelObj->save();

			// go to courier list
			util_redirect("countries.php");
		}

		/*------------------------------------------------------------------------------*/
		// get country
		//echo $this->id;
		$CouObj 		= 	new Country($this->id);
		$this->iso		=	$CouObj->getIso();
		$this->iso3		=	$CouObj->getIso3();
		$this->name		=	$CouObj->getName();
		$this->type		=	$CouObj->getType();
		$this->region	=	$CouObj->getRegion();
		/*------------------------------------------------------------------------------*/
		$this->setTitle ("Admin - Country Details - " . $CouObj->getName());
		
	}

	/**
	 * Returns boolean to indicate if the form is valid
	 *
	 */
	private function validate_form()
	{
		// Check that the country name is not blank
		if (trim($this->name) == "")
		{
			if ($this->error_msg != "") $this->error_msg .= "<br />";
			$this->error_msg .= "Currency name cannot be blank.";
		}
		// Check the iso code
		if (trim($this->iso) == "")
		{
			if ($this->error_msg != "") $this->error_msg .= "<br />";
			$this->error_msg .= "An iso code for the country must be given.";
		}

		// Check that the iso is unique
		if ($this->error_msg == "")
		{
			$filter = new CountryFilter();
			$filter->addIsoFilter($this->iso);
			$country_array = $filter->getList();
			if (sizeof($country_array) > 0)
			{
				if ($country_array[0]->getId() != $this->id)
				{
					if ($this->error_msg != "") $this->error_msg .= "<br />";
					$this->error_msg = "The ISO code has to be unique.";
				}
			}
		}/**/

		return ($this->error_msg == "");

	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
