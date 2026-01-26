<?php
// get settings
            require_once("../includes/settings/config.inc.php");
class Page extends BasePage
{
    // source page for consignment edit
    private $source;
    private $readonly = array();

    /***
    * Controller logic
    */
    protected function init()
    {
        // save source page info
                $this->source = @$_GET['from'];
			// $user = SessionManager::getUser()->getUserAccount();	
        // Get current user
        $user = SessionManager::getUser();

        // Default readonly status
        $this->readonly["account"] = "readonly";
        $this->readonly["hawb"] = "readonly"; // can only edit a hawb if label hasn't been printed
        $this->readonly["date_submitted"] = "readonly";

        // is this form being posted back?
        if (isset($this->form_vars["form_action"]))
        {
            $error_array = array();
            // take appropriate action
		
            switch ($this->form_vars["form_action"])
            {
                // SAVE
                // - validate consignment details - if OK, save and return to client list
                case "save":
                    // save new details

                    // CAN'T EDIT HAWB

                   
					
                    $address2 = new Address2(intval($this->form_vars["id"]));
                    $address2->setAddressLine1($this->form_vars["address_line_1"]);
                    $address2->setAddressLine2 ($this->form_vars["address_line_2"]);
					$address2->setAddressLine3 ($this->form_vars["address_line_3"]);
					
                    $address2->setCity ($this->form_vars["city"]);
                    $address2->setCompany ($this->form_vars["company"]);
                    $address2->setCountry ($this->form_vars["country"]);
                    $address2->setContact ($this->form_vars["contact"]);
					
                    $address2->setPhoneNumber(str_replace (" ", "", $this->form_vars["telephone"]));  
                    $address2->setPostCode($this->form_vars["postcode"]);
                    $address2->setAccount($user->getUserAccount());
					
                    $address2->save();
					//$address2->getPhoneNumber();


                    // If new, check HAWB and account
                    if ($this->form_vars["new"] == "true")
                    {
                      
                        
                        if ($address2->getCity() == "")
                        {
                            $error_array[] = Translation::GetCaption("PEASE_ENTER_CITY");
                        }
                        
                        if ($address2->getCompany() == "")
                        {
                            $error_array[] = Translation::GetCaption("PLEASE_ENTER_COMPANY");
                        }
                        
                        if ($address2->getAddressLine1()== "")
                        {
                            $error_array[] = Translation::GetCaption("PLEASE_ENTER_ADDRESS_LINE_1");
                        }
                        
                        
                    }

                    if (sizeof($error_array)>0)
                    {
                        // show errors
                        $visualErrorComponent = ErrorList::getItem();
                        $visualErrorComponent->setPrelistMessage("Address problems:");
                        $visualErrorComponent->addErrorList($error_array);
                    }
                    
                    else
                    {
                        util_redirect("show_address.php");
                    }
					//echo 	sizeof($error_array). 'sdfasd'.$this->form_vars["form_action"];
		//die;
                    break;

                case "service": // selected service is changed. (needed as package requirements change)
                    break;

                case "pieces":
                    break;

                case "delete":
                    // delete
                    $id = intval($this->form_vars["id"]);
                    if ($id > 0)
                    {
                        $consignment = new Consignment($id);
                        $consignment->delete();
                    }
                    $this->returnToSource(true);
                    break;

                // CANCEL
                // - return to source page
                case "cancel":
                default:
                    util_redirect("show_address.php");
                    break;
            }
        }

        // not post back - first time this form is shown
        else
        {
            //$from=$_GET["from"];
           

                
            // get consignment id passed
            $id = util_get_num("id");
            
            // Is this a new consignment
    

            //
            // get consignment values
            $address = new Address2($id);
            $this->form_vars["address_line_1"]=$address->getAddressLine1();
            $this->form_vars["address_line_2"]=$address->getAddressLine2();
            $this->form_vars["address_line_3"]=$address->getAddressLine3();
            $this->form_vars["city"]=$address->getCity();
            $this->form_vars["company"]=$address->getCompany();
            $this->form_vars["country"]=$address->getCountry();
            $this->form_vars["contact"]=$address->getContact();
            $this->form_vars["telephone"]=$address->getPhoneNumber();
            $this->form_vars["postcode"]=$address->getPostCode();
            /*

                // Add Consignment
                case "add_consignment":
                    $_SESSION["consignment_filter"] = null;
                    //
                    $consignment = new Consignment();
                    $consignment->setStatus(Consignment::STATUS_INVALID);
                    $consignment->setAccount($user->getUserAccount());
                    $consignment->setService(Consignment::SERVICE_DOMESTIC);
                    $consignment->setDateSubmitted (time());
                    $consignment->save();
                    //
                    $consignment->setHawb ("New-" . $consignment->getId());
                    $consignment->save();
                    util_redirect ("../main/consignment_edit.php?id=" . $consignment->getId());
                    break;
             */


            // Adding a new consignment
            $this->form_vars["new"] = "true";
            
        
        }

        // New readonly options
        if (@$this->form_vars["new"] == "true")
        {
            // If ware house user, then can entered any account number
            if ($user->hasPrivilege(User::PRIVILEGE_WAREHOUSE_LIST))
            {
                $this->readonly["account"] = "";
            }
            // new service, user has to enter HAWB
            $this->readonly["hawb"] = "";
        }


        // common initialisation for ths page
        $this->setTitle("Consignment Edit");

        // save session variables
        $_SESSION["consignment_edit_source"] = $this->source;
    }
  
    /***
     * Insert content in to HTML Head section
     */
    protected function renderHead()
    {
        ?>
        <script type="text/javascript">
        <!--          
            
            
        $(document).ready(function(){

            // Service changes
            $("#service").change(function() {


                $("#form_action").val("service");
                $("#adminForm").submit();
            });


            // number of pieces changes
            $("#number_pieces").change(function() {
                // only matters for international
                if ($("#service").val() == "<?php echo Consignment::SERVICE_INTERNATIONAL; ?>")
                {
                    $("#form_action").val("pieces");
                    $("#adminForm").submit();
                }
            });
        });
		function numbersonly(e){
			var unicode=e.charCode? e.charCode : e.keyCode
			if (unicode!=8)
			{
				if(unicode==46)
				{}
				else if (unicode<48||unicode>57) //if not a number
				return false //disable key press
			}
		}
        //-->
        </script>
        <?php
    }

    /***
    * Content View
    */
    protected function renderBody()
    {
		?>
        <ul class="breadcrumb">
			<li><a href="../main/index.php"><? echo Translation::GetCaption("HOME") ?></a></li>
            <li><a href="../main/show_address.php"><? echo Translation::GetCaption("ADDRESS") ?></a></li>
			<li><a href="../main/services.php?id=<?php echo util_get_num("id");?>"><?php if(util_get_num("id") > 0) echo 'Edit'; else  echo 'Add';?></a></li>
		</ul>
		<?php
		 $id = util_get_num("id");
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key=>$val) {$$key = $val; }

        // all fields read-only unless consignment is NEW, INVALID, VALID
        // - might change later  // switch

        ?>
<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption"> <i class="glyphicon glyphicon-search"></i><? echo Translation::GetCaption("ADDRESS_DETAILS") ?></div>
    <div class="tools">  <a href="javascript:;" class="collapse"></a> </div>
  </div>
  <div class="portlet-body">
  
    	<div class="scroller" style="min-height:300px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
        	 <?php errorList::getItem()->render(); ?>
        <div>
        <div class="col-md-12 red-18" style="text-align:right; font-size:12px !important;"> <? echo Translation::GetCaption("FIELDS_WITH_AN_ASTERISK_(*)_ARE_MANDATORY") ?> </div>
        <div class="col-md-3">
            <label><? echo Translation::GetCaption("COMPANY"); ?></label>
            <input type='text' name='company' id='company' maxlength="30" value='<?php echo @$company; ?>' size='100' class='form-control' />
        </div>
        <div class="col-md-3">
            <label><? echo Translation::GetCaption("CONTACT_PERSON"); ?></label>
            <input type='text' name='contact' maxlength="30" id='contact' value='<?php echo @$contact; ?>' size='100'  class='form-control'/>
        </div>
        <div class="col-md-3">
            <label><? echo Translation::GetCaption("ADRESS_LINE_1_(STREET_&_NO)"); ?></label>
            <input type='text' maxlength="30" name='address_line_1' id='address_line_1' value='<?php echo @$address_line_1; ?>' size='100' class='form-control' />
        </div>
        <div class="col-md-3">
            <label><? echo Translation::GetCaption("ADDRESS_LINE_2"); ?></label>
            <input type='text' maxlength="30" name='address_line_2' id='address_line_2' value='<?php echo @$address_line_2; ?>'  size='100' class='form-control' />
        </div>
        <div class="col-md-3">
			<label><? echo Translation::GetCaption("ADDRESS_LINE_3_COUNTRY_DIVISION_STATE"); ?></label>
            <input type='text' maxlength="30" name='address_line_3' id='address_line_3' value='<?php echo @$address_line_3; ?>'  size='100' class='form-control' />
        </div>
		<div class="col-md-3">
			<label><? echo Translation::GetCaption("CITY"); ?> <span style="color:#F00;"> * </span></label>
            <input type='text' name='city' id='city' value='<?php echo @$city; ?>' size='15' maxlength="30" class='form-control' />
        </div>
                <div class="col-md-3">
			<label><? echo Translation::GetCaption("POSTCODE"); ?></label>
            <input type='text' name='postcode' id='postcode' value='<?php echo @$postcode; ?>' size='20'  class='form-control'>
        </div>
                <div class="col-md-3">
			<label><? echo Translation::GetCaption("COUNTRY"); ?> <span style="color:#F00;"> * </span></label>
            <select  name="country"  size="1" id="country" class="form-control">
                <?php 
						$countriesList		=	new CountryFilter();
						$countryListData	=	$countriesList->getList();
						foreach($countryListData as $countryItem)
						{
							if(strtolower(trim($country))== strtolower(trim($countryItem->getName())))
								echo '<option value="'.$countryItem->getName().'" selected="selected">'.$countryItem->getName().'</option>';
							else
								echo '<option value="'.$countryItem->getName().'" >'.$countryItem->getName().'</option>';
							
							
						}
					?>
                </select>
        </div>
        <div class="col-md-3">
			<label><? echo Translation::GetCaption("TELEPHONE"); ?></label>
            <input type='text' name='telephone' id='telephone' value='<?php echo @$telephone; ?>' size='20' class="form-control"  onkeypress="return numbersonly(event)"/>
        </div>
        </div>
        
        <div style="clear:both"></div>
        <br /><br />
        <div class="row " style="text-align:centre;" align="center">
        <div class="col-md-12">
           <a id="btnCancel" href="#" class="btn btn-danger"><span></span><? echo Translation::GetCaption("CANCEL"); ?></a>
            <a id="btnSave" href="#" class="btn btn-primary"><span></span><? echo Translation::GetCaption("SAVE"); ?></a>
          </div>
        </div>

        <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
        <input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />
        <input type="hidden" name="label_file_id" id="label_file_id" value="<?php echo @$label_file_id; ?>" />
      <input type="hidden" name="form_action" id="form_action" value="" />
	
      </div>
</div>    
</div>
        <?php
    }

    /**
    * Return to source page
    * @param none
    */
    private function returnToSource()
    {
        $from_str = "?from=consignment_edit";
        switch($this->source)
        {
            case "client":
            default:
                util_redirect("../main/client_list.php$from_str");
                break;
            case "warehouse":
                util_redirect("../main/warehouse_list.php$from_str");
                break;
            case "booking":
                util_redirect("../main/booking_list.php$from_str");
                break;
            case "export":
                util_redirect("../main/export_list.php$from_str");
                break;
            default:
                util_redirect ("../main/client_list.php");
                break;
        }
    }
/**
	* Override to show the menu
	*
	*/
	public function renderMenu()
	{
		$menu = new Adminmenu(Adminmenu::CUSTOMERS);
		$menu->render();
	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show(); 