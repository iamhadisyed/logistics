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
	private $name = NULL;
	private $printable_name = NULL;
	private $numcode = NULL;
	private $has_postcodeq = NULL;
	private $action = NULL;
	private $customs_flag;
	private $vat_flag;
	private $sort_priority = 2;
	private $export_flag = NULL;
	private $pagetitle = NULL;
	private $metakeywords = NULL;
	private $metadescription = NULL;
	private $currencyname = NULL;
	private $left_symbol = NULL;
	private $right_symbol = NULL;
	private $is_Default = NULL;
	private $currency_exch_rate = NULL;
	private $is_active = NULL;

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
                        <i class="glyphicon glyphicon-search"></i>Search Panel
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="scroller" style="min-height:200px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                    <div class="note note-error">
                        <p>	<?php	if ($this->error_msg != "") {	echo $this->error_msg;	} ?>	 </p>
                    </div>
                    
			<form method="post">
				<div class="row">
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>Currency Name: </label>
                                <input class="form-control" name="name" id="name" type="text" value="<?php echo $this->currencyname;?>">
                              </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Left Symbol:  </label>
                                <input class="form-control" name="left_symbol" id="left_symbol" type="text" value="<?php echo @$this->left_symbol;?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>Right Symbol: </label>
                                <input  class="form-control" type="text" id="right_symbol" name="right_symbol" value="<?php echo @$this->right_symbol;?>" />
                              </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Currency Exchange Rate: </label>
                                <input class="form-control" id="currency_exch_rate" name="currency_exch_rate" type="text" value="<?php echo @$this->currency_exch_rate;?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                             <div class="form-group">
                                <label>is Default: </label>
                                <input name="is_Default" type="checkbox" <?php if ($this->is_Default) {echo "checked";}?>>
                              </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>is Active:  </label>
                               <input name="is_active" type="checkbox" <?php if ($this->is_active) {echo "checked";}?>>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <a href="currency.php">Currency List</a>&nbsp;&nbsp;&nbsp;
                                <button type="submit" class="btn btn-primary" id="btn_save" name="btn_save" value="Save Changes">
                                	Save changes
                                </button>
                            </div>
                        </div>
                        
                    </div>
                
		
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
		$this->id = (isset($_REQUEST['currency_id']) ? strip_tags($_REQUEST['currency_id']) : '');
		$this->currencyname = (isset($_POST['name']) ? strtoupper(strip_tags($_POST['name'])) : '');
		$this->left_symbol = (isset($_POST['left_symbol']) ? strip_tags($_POST['left_symbol']) : '');
		$this->right_symbol = (isset($_POST['right_symbol']) ? strip_tags($_POST['right_symbol']) : '');
		$this->is_Default = (bool)(isset($_POST['is_Default']));
        $this->currency_exch_rate = (isset($_POST['currency_exch_rate']) ? strip_tags($_POST['currency_exch_rate']) : '');
		$this->is_active = (bool)(isset($_POST['is_active']));
		/*------------------------------------------------------------------------------*/
		// process form
		if (isset($_POST['btn_save']))
		{
			
			if ($this->validate_form())
			{
				// save
				//echo ("adfasfasf" . $this->id);
				
				$CouSavObj = new Currency($this->id);
				
				$CouSavObj->setCurrencyName($this->currencyname);
				$CouSavObj->setLeftSymbol($this->left_symbol);
				$CouSavObj->setRightSymbol($this->right_symbol);				
				$CouSavObj->setIsDefault($this->is_Default);				
				$CouSavObj->setCurrencyExchangeRate($this->currency_exch_rate);				
				$CouSavObj->setIsActive($this->is_active);
				$this->is_active;
				$CouSavObj->save();

				// go to courier list
				util_redirect("currency.php");
			}
		}

		/*------------------------------------------------------------------------------*/
		// process delete
	 
		if ($_REQUEST['action'] == "confirmed_delete")
		{
			
			// save times and details
			$CouDelObj = new Currency($this->id);
			$CouDelObj->delete();

			// go to courier list
			util_redirect("currency.php");
		}

		/*------------------------------------------------------------------------------*/
		// get country
		$CouObj = new Currency($this->id);
		if (!isset($_POST['name'])) { $this->currencyname = $CouObj->getCurrencyName(); }
		if (!isset($_POST['left_symbol'])) { $this->left_symbol = $CouObj->getLeftSymbol(); }
		if (!isset($_POST['right_symbol'])) { $this->right_symbol = $CouObj->getRightSymbol(); }
		
		if (!isset($_POST['is_Default'])) { $this->is_Default = $CouObj->getIsDefault(); }
		if (!isset($_POST['currency_exch_rate'])) { $this->currency_exch_rate = $CouObj->getCurrencyExchangeRate(); }
		if (!isset($_POST['is_Active'])) { $this->is_active = $CouObj->getIsActive(); }
	
		/*------------------------------------------------------------------------------*/
		$this->setTitle ("Admin - Currency details - " . $this->currencyname);
	}

	/**
	 * Returns boolean to indicate if the form is valid
	 *
	 */
	private function validate_form()
	{
		// Check that the country name is not blank
		if (trim($this->currencyname) == "")
		{
			if ($this->error_msg != "") $this->error_msg .= "<br />";
			$this->error_msg .= "Currency name cannot be blank.";
		}
	
		return ($this->error_msg == "");

	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
