<style type="text/css">

    /*#hawb 
    { 
        background: #FFF url(http://html-generator.weebly.com/files/theme/input-text-9.png) no-repeat 7px 5px;
            background-color: #EDEDE7;
        border: 1px solid #999; 
        outline: 0; 
        padding-left: 40px;
            font-size: 25px;
        height: 50px; 
        width: 397px; 
      } 
    input[type=text] 
    {     
            background-color: #EDEDE7;
        border: 1px solid #999; 
        outline: 0; 
        padding-left: 20px;
            font-size: 13px;
        height: 25px; 
        width: 275px; 
            border-radius : 10px;
      }  
      
      input[type=text]:focus, textarea:focus, #hawb:focus
      {
           background-color: #FFC;
           border: 1px solid #21286F;
      }*/

    .tableA
    {


        margin-right : 200px;
        display: inline-block;
    }

    .error
    {
        font-size: 18px;
        color: red;   
    }

    .success
    {
        font-size: 18px;
        color: green;   
    }

    .web_dialog_overlay
    {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        background: #000000;
        opacity: .5;
        filter: alpha(opacity=15);
        -moz-opacity: .15;
        z-index: 101;
        display: none;
    }
    .web_dialog_alert
    {
        position: fixed;
        top: 50%;
        left: 50%;

        padding: 0px;
        z-index: 102;
        font-family: Verdana;
        font-size: 10pt;
    }




</style> 




<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');



class Page extends BasePage
{
// source page for consignment edit
private $source;
private $readonly = array();
private $error = array();
private $str;
private $chkAutoPrint;

/* * *
 * Controller logic
 */
protected function init()
{
// save source page info

$user = SessionManager::getUser();
if ($user->getUserType() != "warehouse" && $user->getUserType() != "corporateclient" && $user->getUserType() != "admin")
{
util_redirect("index.php");
}
$this->source = @$_GET['from'];




// Tool bar
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
// Get current user
$user = SessionManager::getUser();

// Default readonly status
//$this->readonly["account"] = "readonly";
//$this->readonly["hawb"] = "readonly"; // can only edit a hawb if label hasn't been printed
//$this->readonly["date_submitted"] = "readonly";
// is this form being posted back?
if (isset($this->form_vars["form_action"]))
{
$error_array = array();
//echo '<pre>';
//print_r($_POST);
//die;
// take appropriate action



switch ($this->form_vars["form_action"])
{
// SAVE
// - validate consignment details - if OK, save and return to client list

case "open_print_dialog":

$fileName = $this->form_vars['filename'];

if($fileName == '')
{


$hawb = trim($this->form_vars['hawb']);
$consignment_filter = new ConsignmentFilter();
$consignment_filter->addAwbAndHawbOrFilterNotRecycled($hawb);
$con_list = $consignment_filter->getColumnList( "company,
																			 account, service, notes,reference, country_iso_code,   
																			contact, 
																			address_line_1,
																			address_line_2, 
																			address_line_3, 
																			city, 
																			country, 
																			postcode, 
																			telephone,
																			weight,
																			number_pieces,
																			handling, 
																			service_type,currency,value,description, routing_code,
																			single_label");

if(count($con_list) > 0)
{
$con = $con_list[0];
//print_r($con);																									
$this->CreateLabel($con);
$fileName = $con->getSingleLabel();
}


}
else
{
?>	
<iframe id="iFramePdf"  src="<?php echo $fileName; ?>" style="display:none;width: 619px; height: 482px;"></iframe>
<script type="text/javascript">
    var getMyFrame = document.getElementById('iFramePdf');
    window.onload = setTimeout("getMyFrame.contentWindow.print()", 1000);
    document.iFramePdf.printMe();
    $("#hawb").val('');
    document.getElementById("hawb").focus();
</script>
<?	


}

if($this->form_vars['chkAutoPrint'] == "on")
$this->chkAutoPrint = "checked";
else
$this->chkAutoPrint = "";	

//echo $this->chkAutoPrint;

//die;                        


break;

case "save":

if(trim($this->form_vars['hawb']) == '')
{
$this->error[] = 'Please enter HAWB or Tracking number.';
}
if(empty($this->form_vars['address_line_1']))
{

$this->error[] = 'Please enter address line 1.';
}
if(trim($this->form_vars['contact']) == '' && trim($this->form_vars['company']))
{
$this->error[] = 'Please enter either contact or company name.';
}
if(trim($this->form_vars['contact']) == '' && trim($this->form_vars['company']))
{
$this->error[] = 'Please enter either contact or company name.';
}

/*if($this->form_vars['weight'] <= 0 || $this->form_vars['weight'] == '')
{
$this->error[] = 'Please enter weight and it should be greater than 0.';
}*/

/*if(isset($this->form_vars['service_type']))
{					
if($this->form_vars['service_type'] ==  'Please Select Service' || $this->form_vars['service_type'] == '')
{
$this->error[] = 'Please select service.';
}
}*/

if($this->form_vars['city'] == '')
{
$this->error[] = 'Please enter city.';
}		

if(count($this->error) == 0)	
{

//echo '<pre>';
//print_r($_POST);		

$this->saveFormDataToObject();
}




break;

default:                    
break;
}
}

// not post back - first time this form is shown
else
{


// Adding a new consignment
$this->form_vars["new"] = "true";


}

// New readonly options
/*if (@$this->form_vars["new"] == "true")
{
// If ware house user, then can entered any account number
if ($user->hasPrivilege(User::PRIVILEGE_WAREHOUSE_LIST))
{
$this->readonly["account"] = "";
}
// new service, user has to enter HAWB
//$this->readonly["hawb"] = "";
}*/


// common initialisation for ths page
$this->setTitle("Shipment Query");

// save session variables
$_SESSION["consignment_edit_source"] = $this->source;
}

/***
* Insert content in to HTML Head section
*/

private function showMessage()
{
if(isset($this->form_vars['hawb']))
{

$str = '';
if(count($this->error) > 0)
{
foreach($this->error as $err)
{
$this->str .= $err  . '<br>';
}

echo '<script>$("#msg").addClass("error");</script>' . $this->str;
}
else
{
//$str = 'Changes saved successfully';
echo '<script>$("#msg").addClass("success");</script>' . $this->str;
}

}
}	
protected function renderHead()
{
?>
<link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
<script src="../js/bootstrap-select.min.js"></script>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css"
      href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" />
<script type="text/javascript">

    function showLabel(url, title, w, h)
    {
        var left = (screen.width / 2) - (w / 2);
        var top = (screen.height / 2) - (h / 2);
        window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', 			  height=' + h + ', top=' + top + ', left=' + left);
    }




    $(document).ready(function (e)
    {
        document.getElementById("hawb").focus();

        document.getElementById('hawb').addEventListener('keypress', function (event)
        {
            if (event.keyCode == 13)
            {
                $("#loader").css("display", "block");
                //$("#overlay").show();
                var hawb_number = $.trim($('#hawb').val());

                var autoprint = $("#chkAutoPrint").is(":checked");

                //alert(autoprint);

                if (hawb_number == '')
                    alert('Please enter hawb/tracking number.');

//					    alert(hawb_number);
                $.ajax({
                    type: "POST",
                    url: "box_ajax.php", // your php file name
                    data: {action: 'GetShipmentData', hawb: hawb_number, autoprint: autoprint},
                    success: function (data)
                    {
                        $("#loader").css("display", "none");

                        //alert(data);
                        var obj = JSON.parse(data);



                        //alert(obj.country);
                        if (obj.result == 'error')
                        {


                            $('#hawb').css('background-color', 'red');
                            $('#msg').css('color', 'red');
                            $('#msg').css('font-size', '14');
                            $('#msg').text(obj.message);



                            setTimeout(function ()
                            {
                                $('#hawb').css('background-color', '#EDEDE7');

                            }, 3000);

                        } else
                        {


                            $('#msg').text('');
                            $('#hawb').css('background-color', 'springgreen');
                            $('#company').val(obj.company);
                            $('#contact').val(obj.contact);
                            $('#address_line_1').val(obj.addressline1);
                            $('#address_line_2').val(obj.addressline2);
                            $('#address_line_3').val(obj.addressline3);
                            $('#city').val(obj.city);

                            //alert($('#country'));	

                            //$("#country option[value='" + obj.country + "']").prop('selected', true);		

                            $('.bootstrap-select .filter-option').text(obj.country);

                            //$('#country').text(obj.country);									

                            $('#postcode').val(obj.postcode);
                            $('#telephone').val(obj.telephone);
                            $('#weight').val(obj.weight);
                            $('#oldService').val(obj.service);
                            $('#number_pieces').val(obj.number_pieces);
                            $('#currency').val(obj.currency);
                            $('#value').val(obj.value);
                            $('#description').val(obj.description);

                            if (autoprint)
                            {
                                //$("#hawb").val('');
                                $("#hawb").focus();
                                $("#form_action").val("open_print_dialog");
                                $("#filename").val(obj.label);
                                $("#adminForm").submit();
                                //showLabel(obj.label, 'Label',500,500);

                            }

                            setTimeout(function ()
                            {
                                $('#hawb').css('background-color', '#EDEDE7');

                            }, 3000);
                        }



                    }

                });

                return false;

            }



        });






        //$('input').tooltip();
        //$('select').tooltip();
        //$('textarea').tooltip();
        $("#btnSave").click(function ()
        {

            $("#form_action").val("save");
            $("#adminForm").submit();
        });





        /*$.ajax({
         type: "POST",
         url : "box_ajax.php",  // your php file name
         data: {action: 'GetServiceList'},
         success:function(data)
         {							 
         var obj = JSON.parse(data);				 
         
         $( "#serviceList" ).autocomplete({
         minLength: 2,
         source: obj,
         focus: function( event, ui ) {
         $( "#serviceList" ).val( ui.item.label );
         return false;
         },
         select: function( event, ui ) {
         $( "#serviceList" ).val( ui.item.label );
         $("#code").val(ui.item.value);									 
         return false;
         }
         })
         
         }				
         
         
         });*/










    });






</script>
<?php
}

/* * *
 * Content View
 */
protected function renderBody()
{
?>

<input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
<ul class="breadcrumb">
    <li><a href="../main/index.php">Home</a></li>
    <li><a href="../main/show_address.php">Address</a></li>
    <li><a href="../main/services.php?id=<?php echo util_get_num("id"); ?>"><?php if(util_get_num("id") > 0) echo 'Edit';
else echo 'Add';
?></a></li>
</ul>
<?php
$id = util_get_num("id");
// transfer form variables into local values (form variables come from parent)
foreach ($this->form_vars as $key => $val) {$$key = $val;
}

// all fields read-only unless consignment is NEW, INVALID, VALID
// - might change later  // switch
?>
<div class="clear" ></div>
<div class="main_formpage">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"> <i class="icon-bar-chart"></i>
<?php errorList::getItem()->render(); ?>Relabel Shipment</div>
            <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
        </div>

        <div class="portlet-body">
            <div id='msg'>
                <? $this->showMessage(); ?>
            </div>
<?php errorList::getItem()->render(); ?>
            <h2 style="margin-left: 14px;" >Shipment Detail</h2>
            <!--NEW SECTION START-->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-3">         
                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> 
                                <i id="loader" name="loader" style="display:none" class="fa fa-spinner fa-spin icon-large"></i>
                            </span>
                            <input type="text" id="hawb" name="hawb" value='<?php echo @$hawb; ?>' size="50" class="form-control" placeholder="Tracking/HAWB NUMBER" rel="tooltip" data-original-title="Tracking/HAWB No."/> 


                            <input type="hidden" id='option' name="option" value='Relabel'/>               
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                            <input class='form-control'  type="text" id="oldService" name="oldService" value='<?php echo @$oldService; ?>' style='' size="80" readonly  placeholder="Selected Service" rel="tooltip" data-original-title="Selected Service"/>    
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="input-group"> 
                            <!--<span class="input-group-addon"> <i class="fa fa-tags"></i> </span>-->
                            <label class='form-control'>Change To</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                            <? $this->loadServiceTypes(); ?>
                            <input type="hidden" id="code" name='code' /> 
                            <input type="hidden" id="filename" name="filename" />
                        </div>
                    </div>

                </div>        




                <div class="col-md-6">

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-3">
                        <div class="input-group"> 
                            <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                            <input type="checkbox" <? echo $this->chkAutoPrint ?> id="chkAutoPrint" name="chkAutoPrint" /> Auto Print

                        </div>
                    </div>    
                </div>       
            </div>

            <h2 style="margin-left: 14px;">Address Details</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group col-md-6">
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                <input type='text' name='company' id='company' maxlength="30" value='<?php echo @$company; ?>' size='100' class='form-control' placeholder="Company"rel="tooltip" data-original-title="Company"/>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                <input type='text' name='contact' maxlength="30" id='contact' value='<?php echo @$contact; ?>' size='100' class='form-control' rel="tooltip" data-original-title="Contact Person" placeholder="Contact Person"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-book"></i> </span>
                                <input type='text' name='city' id='city' value='<?php echo @$city; ?>' size='15' maxlength="30" class='form-control' placeholder="City" rel="tooltip" data-original-title="City"/>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-qrcode"></i> </span>
                                <input type='text' name='postcode' id='postcode' value='<?php echo @$postcode; ?>' size='20'  class='form-control' placeholder="Postcode" rel="tooltip" data-original-title="Postcode">
                            </div>
                        </div>
                    </div> 
                    <div class="form-group col-md-6" style="margin-top: -3%">
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                <input type='text' maxlength="30" name='address_line_1' id='address_line_1' value='<?php echo @$address_line_1; ?>' size='100' class='form-control' rel="tooltip" data-original-title="Address Line 1" placeholder="Address Line 1"/>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                <input type='text' maxlength="30" name='address_line_2' id='address_line_2' value='<?php echo @$address_line_2; ?>'  size='100' class='form-control' rel="tooltip" data-original-title="Address Line 2" placeholder="Address Line 2"/>
                            </div>
                        </div>

                    </div>
                    <div class="form-group col-md-6" style="margin-top: -3%">
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                                <select  name="country"  size="1" id="country" class="selectpicker" data-live-search="true" rel="tooltip" data-original-title="Country" placeholder="Country">
                                    <?php
                                    $countriesList = new CountryFilter();
                                    $countryListData = $countriesList->getList();
                                    foreach($countryListData as $countryItem)
                                    {
                                    $countryName = strtoupper($countryItem->getName());

                                    if(strtoupper(trim($country)) == strtoupper(trim($countryItem->getName())))
                                    echo '<option value="'.$countryName.'" selected>'.$countryName.'</option>';
                                    else
                                    echo '<option value="'.$countryName.'" >'.$countryName.'</option>';


                                    }
                                    ?>
                                </select>
                            </div>
                        </div>      
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-phone"></i> </span>
                                <input type='text' name='telephone' id='telephone' value='<?php echo @$telephone; ?>' size='20' class="form-control"  onkeypress="return numbersonly(event)" rel="tooltip" data-original-title="Telephone" placeholder="Telephone"/>  
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6"></div>
                    <div class="form-group col-md-6" >
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                <input type='text' maxlength="30" name='address_line_3' id='address_line_3' value='<?php echo @$address_line_3; ?>'  size='100' class='form-control' rel="tooltip" data-original-title="Address Line 3" placeholder="Address Line 3"/>
                            </div>
                        </div> 
                    </div>
                    <div class="form-group col-md-6" style="top: -25%">
                        <div class="form-group col-md-12" >
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                <input type='text' name='weight' id='weight' value='<?php echo @$weight; ?>' size='20' class="form-control" placeholder="Weight" rel="tooltip" data-original-title="Weight"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group col-md-6">
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                <input type='text' name='description' id='description' value='<?php echo @$description; ?>' size='50' class="form-control"  placeholder="Description" rel="tooltip" data-original-title="Description"/>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                <input type='text' name='value' id='value' value='<?php echo @$value; ?>' size='50' class="form-control" style='' placeholder="Values"  rel="tooltip" data-original-title="Values"/>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="input-group">
                                <select id='currency' class='select_dropdown_con form-control' name='currency' style='' <?php echo $readonly_str ?> >
                                    <?php
// Currency array 
                                    $arrayCurrency = array('GBP', 'USD', 'CAN', 'DFL', 'DKR', 'EUR', 'FFR', 'HKG', 'INR', 'JPY', 'NKR', 'NLG', 'SGD', 'SFR', 'SKR', 'YEN', 'PLN');
                                    foreach($arrayCurrency as $currency){
                                    if (@$this->form_vars['currency'] == $currency)
                                    $selectedCurrency = 'selected="selected"';
                                    else
                                    $selectedCurrency = '';

                                    echo '<option value="'.$currency.'" '.$selectedCurrency.'>'.$currency.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                <select class='select_dropdown_con form-control' name="itemtype" id="itemtype">	
                                    <?  $itemType=array ("Normal","Letter","Packets","Lithium Ion Battery", "Lithium Metal Battery", "Perfume","Fire Extinguisher");
                                    foreach($itemType as $itype)
                                    printf("<option value=\"%s\" %s>%s</option> ",$itype,($selectedItemType==$itype? "selected='selected'" : ""),$itype);
                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group col-md-6">
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                <select name='number_pieces' id='number_pieces' class="select_dropdown_con form-control" placeholder="Pieces" rel="tooltip" data-original-title="Pieces">

                                    <?php
                                    for ($i = 1;
                                    $i<150;
                                    $i++)
                                    {
                                    ?>
                                    <option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                <textarea name='comments' rows='4' cols='10' id='comments' class="form-control" placeholder="Comments" rel="tooltip" data-original-title="Comments"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--END NEW SECTION START-->



            <div class="row" style="text-align:center;"> 
                <a id="btnCancel" href="#" class="btn btn-danger btn_cancel"><span></span>Cancel</a>
                <a id="btnSave" href="#" class="btn btn-primary btn_save"><span></span>Save</a>
                <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                <input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />
                <input type="hidden" name="label_file_id" id="label_file_id" value="<?php echo @$label_file_id; ?>" />
            </div>
        </div><!--portlet-body-->
    </div>
</div>
<?php
}


public function saveFormDataToObject()
{


$option = '';
$comments = '';

$carrierName = $this->form_vars['service_type'];

if(isset($this->form_vars['comments']))
$comments = $this->form_vars['comments'];

/* if(isset($this->form_vars['code']))
  $handling = trim($this->form_vars['code']); */

if(isset($this->form_vars['option']))
{
$option = $this->form_vars['option'];
}


//echo "test".$option;


$hawb = trim($this->form_vars['hawb']);
$company = trim($this->form_vars['company']);
$contact = trim($this->form_vars['contact']);
$address_line_1 = trim($this->form_vars['address_line_1']);
$address_line_2 = trim($this->form_vars['address_line_2']);
$address_line_3 = trim($this->form_vars['address_line_3']);
$city = trim($this->form_vars['city']);
//$country = trim($this->form_vars['country']);
$postcode = trim($this->form_vars['postcode']);
$country = trim($this->form_vars['country']);
$telephone = trim($this->form_vars['telephone']);
$weight = trim($this->form_vars['weight']);
$pieces = $this->form_vars['number_pieces'];
$value = $this->form_vars['value'];
$currency = $this->form_vars['currency'];
$description = $this->form_vars['description'];
$selectedItemType = $this->form_vars['itemtype'];

$countryFilter = new CountryFilter();
$countryFilter->addNameArrayFilter($country);
$country_list = $countryFilter->getColumnList('iso');

$countryObj = '';

if(count($country_list) > 0)
{
$countryObj = $country_list[0];
}

$country_iso_code = $countryObj->getIso();


$consignment_filter = new ConsignmentFilter();
$consignment_filter->addAwbAndHawbOrFilterNotRecycled($hawb);
$con_list = $consignment_filter->getList();

//////////////// CREATE A NEW LABEL WHEN THE NEW SERVICE IS SELECTED EITHER  ///////////
/// WITH NEW SHIPMENT INFO OR OLD BY THE USER /////////////////////
//echo $option . " " . $carrierName;
//die;		



if(count($con_list) > 0 && $option == 'Hold')
{
//echo "saved";
$consignment = $con_list[0];
$consignment->setStatus('hold');
$consignment->setMessage($comments);
$consignment->save();
$this->showMessage();

}
else if(count($con_list) > 0 && $carrierName !== '' && $option == 'Relabel')
{


$serviceflr = new ServiceFilter();
$serviceflr->addCodeExactFilter($carrierName); /// SELECTED SERVICE BY THE USER
$serviceList = $serviceflr->getColumnList("name, code, carrier, carrier_name, type");

//echo count($serviceList);
//echo $carrierName;	
//die;	

$service = $serviceList[0];
$carrier = $service->getCarrier();
$handling = $service->getCode();


/* $serviceflr = new ServiceFilter();
  $serviceflr->addSCodeFilter($handling); /// SELECTED SERVICE BY THE USER
  $serviceList = $serviceflr->getColumnList("name, code, carrier, type");
  $service = $serviceList[0];
  $carrier = $service->getCarrier(); */


$consignment = $con_list[0];


//print_r($consignment);
//die;

$con_relabel = new Consignment();
$con_relabel->setHawb($consignment->getHawb());

$con_relabel->setConsignmentStatus('valid'); // STATUS SHOULD BE VALID FOR RELABEL
$con_relabel->setHandling();

$con_relabel->setService($service->getType());
$con_relabel->setHandling($handling);
$con_relabel->setReference($consignment->getReference());
$con_relabel->setDateReceived($consignment->getDateReceived());
$con_relabel->setType($consignment->getType());

$con_relabel->setDateSubmitted(time());
$con_relabel->setDateImported(time());
$con_relabel->setDateScanned(date("Y-m-d H:i:s"));
$con_relabel->setCompany($company);
$con_relabel->setContact($contact);
$con_relabel->setAddressLine1($address_line_1);
$con_relabel->setAddressLine2($address_line_2);
$con_relabel->setAddressLine3($address_line_3);
$con_relabel->setCity($city);
$con_relabel->setCountry($country);
$con_relabel->setPostCode($postcode);
$con_relabel->setCountryIsoCode($country_iso_code);
$con_relabel->setTelephone($telephone);
$con_relabel->setNumberPieces($pieces);
$con_relabel->setWeight($weight);
$con_relabel->setItemType($selectedItemType);

if(trim($description) != '')
$con_relabel->setDescription($description);
else
$con_relabel->setDescription($consignment->getDescription());
if(trim($value) != '')
$con_relabel->setValue($value);
else
$con_relabel->setValue($consignment->getValue());
if(trim($currency) != '')
$con_relabel->setCurrency($currency);
else
$con_relabel->setCurrency($consignment->getCurrency());
$con_relabel->setNotes($consignment->getNotes());
$con_relabel->setRoutingCode($consignment->getRoutingCode());

$con_relabel->setServiceType($service->getName()); /// USER ENTERED SERVICE
$con_relabel->setMawb($consignment->getMawb());
$con_relabel->setBagNumber($consignment->getBagNumber());
$con_relabel->setBagWeight($consignment->getBagWeight());

$user = SessionManager::getUser()->getUserAccount();

/* if(trim($user) == 'OPERA')
  {
  $con_relabel->setAccount('OPERA');
  $con_relabel->setWarehouseAccountRef($consignment->getAccount());
  }
  else
  { */
$con_relabel->setAccount($consignment->getAccount());
if(trim($consignment->getWarehouseAccountRef()) == '')
$con_relabel->setWarehouseAccountRef($user);
else
$con_relabel->setWarehouseAccountRef($consignment->getWarehouseAccountRef());
//}
//$con_relabel->setRelabelShipment(1);


$consignment_validator = new ConsignmentValidator($con_relabel);

//$isValid = $consignment_validator->isValid();

if (!$consignment_validator->isValid())
{
foreach($consignment_validator->getErrorList() as $error)
$this->error[] = $error;

//print_r($this->error);
//die;

return;
}


$consignment->setDateBooked('');
$consignment->setStatus(Consignment::STATUS_RECYCLED);
$consignment->save();

$con_relabel->save();



//if($consignment_validator->isValid()
//echo "validation  " . $isValid;
//die;



$con_relabel = $this->CreateLabel($con_relabel);

$ConsignmentLog = new ConsignmentLog();
$ConsignmentLog->createlog("Shipment relabelled : Old Tracking Number " . $consignment->getAwb(), $con_relabel->getId());

$ConsignmentLog = new ConsignmentLog();
$ConsignmentLog->createlog("Shipment relabelled : New Tracking Number " . $con_relabel->getAwb(), $consignment->getId());



if(trim($comments) == '')
$consignment->setMessage('Please relabel the shipment ' . str_replace("..", SETTING_MAIN_URL, $con_relabel->getSingleLabel()));
else
$consignment->setMessage($comments . " " . str_replace("..", SETTING_MAIN_URL, $con_relabel->getSingleLabel()));

$consignment->save();


$user = SessionManager::getUser();


$consignmntRelabel = new ConsignmentRelabel();
$consignmntRelabel->setAccount($con_relabel->getAccount());
$consignmntRelabel->setOldTrackingNo($consignment->getAwb());
$consignmntRelabel->setNewTrackingNo($con_relabel->getAwb());
$consignmntRelabel->setDateCreated(time());
$consignmntRelabel->setUserId($user->getId());
$consignmntRelabel->save();



$this->TransferTrackPoints($consignment, $con_relabel);

TrackingData::AddVirtualTrackingToScanParcels(array($con_relabel->getAwb()), $user);

}
else if(count($con_list) > 0 && $option == 'Relabel') //////////////// EXISTING SERVICE IS SELECTED AND CREATE A LABEL WITH NEW SHIPEMNT INFORMATION
{
//echo "kazim2";
//die;


$consignment = $con_list[0];

$oldTrackingNumber = $consignment->getAwb();

$consignment->setStatus('valid');
$consignment->setCompany($company);
$consignment->setContact($contact);
$consignment->setAddressLine1($address_line_1);
$consignment->setAddressLine2($address_line_2);
$consignment->setAddressLine3($address_line_3);
$consignment->setCity($city);
$consignment->setCountry($country);
$consignment->setPostCode($postcode);
$consignment->setCountryIsoCode($country_iso_code);
$consignment->setTelephone($telephone);
$consignment->setWeight($weight);
$consignment->setNumberPieces($pieces);
$consignment->setItemType($selectedItemType);


if(trim($description) != '')
$consignment->setDescription($description);
if(trim($value) != '')
$consignment->setValue($value);

if(trim($currency) != '')
$consignment->setCurrency($currency);



$consignment->setSingleLabel('');
//$consignment->setAwb(''); //// need to discuss with Kiran when we make one system then it should give us the same tracking number or send an
//// email to the customer
$consignment->save();
$con_relabel = $this->CreateLabel($consignment);

$user = SessionManager::getUser();

if(isset($con_relabel) && $con_relabel->getId() > 0)
{

$consignmntRelabel = new ConsignmentRelabel();
$consignmntRelabel->setAccount($con_relabel->getAccount());
$consignmntRelabel->setOldTrackingNo($oldTrackingNumber);
$consignmntRelabel->setNewTrackingNo($con_relabel->getAwb());
$consignmntRelabel->setDateCreated(time());
$consignmntRelabel->setUserId($user->getId());
$consignmntRelabel->save();


$ConsignmentLog = new ConsignmentLog();
$ConsignmentLog->createlog("Shipment relabelled : Old Tracking Number " . $oldTrackingNumber . "New Tracking Number " . $con_relabel->getAwb(), $con_relabel->getId());





TrackingData::AddVirtualTrackingToScanParcels(array($con_relabel->getAwb()), $user);
}

}

}

public function TransferTrackPoints($old_consignment, $new_consignment)
{
$tracking_data_filter = new TrackingDataFilter();
$tracking_data_filter->addTrackingNumberFilter($old_consignment->getAwb());
$tracking_data_list = $tracking_data_filter->getList();

foreach($tracking_data_list as $track_data)
{
$tracking_data = new TrackingData();
$tracking_data->setTrackingNumber($new_consignment->getAwb());
$tracking_data->setConsignmentID($new_consignment->getID());
$tracking_data->setDateCreated($track_data->getDateCreated());
$tracking_data->setStatusCode($track_data->getStatusCode());
$tracking_data->setDescription($track_data->getDescription());
$tracking_data->setTrackPoint($track_data->getTrackPoint());
$tracking_data->setAccount($track_data->getAccount());
$tracking_data->save();

}

}

public function CreateLabel($consignment)
{


$pdf2 = new PDFMerger();
$labelFile = new LabelFile();
$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetX(1.0);
//
$first_page = true;
$page_count = 0;
$consignment_str = "";
$account = "";
$complete = true;

$serviceflr = new ServiceFilter();
$serviceflr->addSCodeFilter($consignment->getHandling()); /// SELECTED SERVICE BY THE USER
$serviceList = $serviceflr->getColumnList("name, code, carrier, type");
$service = $serviceList[0];
$carrier = $service->getCarrier();



$userflr = new UserAccountFilter();
$userflr->addUserAccountFilter($consignment->getAccount());
$userList = $userflr->getColumnList("user_pass");

if(count($userList) > 0)
{
$user = $userList[0];
$pass = $user->getUserPass();

}

$mixedCreator['consignment'] = $consignment;
$mixedCreator['password'] = $pass;
$mixedCreator['carrier'] = "2";

$apiValue = new Apis($mixedCreator); //  Calling the constructor
$results = $apiValue->label();
$returnconsignmentarray = explode("||", $results);

//echo $returnconsignmentarray[0];
//die;



if($returnconsignmentarray[0] == "SUCCESS" )
{

$labelFile = new LabelFile();
$consignment_str = $consignment->getHawb();
$account = $consignment->getAccount();
$labelFile->setHawbList($consignment_str);
$labelFile->setAccountNumber($account);
$labelFile->save();

$consignment->setAwb($returnconsignmentarray[2]);
$uklink = $returnconsignmentarray[1];

$this->str = "Label has generated.";

/* if(substr($uklink,0,10)!='../_assets')
  {
  if(  strpos(strtolower($uklink), 'http') === false)
  $uklink 	=	'http://'.$uklink ;
  } */


/* if(substr($uklink,0,10)!='../_assets')
  {
  if(strpos(strtolower($uklink), 'http') === false)
  $uklink 	=	'http://'.$uklink ;
  }

  if(strpos(strtolower($uklink), 'http') === false)
  {

  } */
//$uklink 	=	'http://'.$uklink ;
//$uklink;
//$pdfpage = file_get_contents($uklink);

$fileNameNew = "../_assets/pdf/".date("Y_m_d")."/".$consignment->getId().".pdf";
// echo $fileNameNew;			 
// $fp			=	fopen($fileNameNew, 'wb+');
// fwrite( $fp, $pdfpage );
// fclose( $fp );
// exit;
//	 $pdf2->addPDF($fileNameNew, 'all');
// echo $labelFile->getId(); exit;
//echo $fileNameNew;
$consignment->setPrintedFileId($labelFile->getId());

if($consignment->getDateReceived() == '')
$consignment->setDateReceived(time());

$consignment->setDateBooked(time());
$consignment->setStatus(Consignment::STATUS_RELABEL);
$consignment->setSingleLabel($fileNameNew);
$consignment->savelog('Single Created/Printed', 'L');
$consignment->save();


//echo $fileNameNew;
//$outFile = $labelFile->getFullPath();
// echo $outFile;
//$pdf->Output($fileNameNew, "F");
// exit;
try
{
//$pdf2->merge('file', $fileNameNew);
echo $returnconsignmentarray[1];
echo '<script language="javascript">';
echo "window.open('" . $returnconsignmentarray[1]. "','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');";
echo '</script>';
?>
<!--
                        <iframe id="iFramePdf"  src="<?php echo $fileNameNew; ?>" style="display:none;width: 619px; height: 482px;"></iframe>
                            <script type="text/javascript">
                            var getMyFrame = document.getElementById('iFramePdf');
                            window.open($fileNameNew)
                            window.onload = setTimeout("getMyFrame.contentWindow.print()", 1000);
                            document.iFramePdf.printMe();					
                            $("#hawb").val('');
                            document.getElementById("hawb").focus();
                            
                            </script>-->

<?

//$("#hawb").select();


/*echo '<script language="javascript">';
echo "window.open('". str_replace("http", "https", $fileNameNew)."','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');" ;
echo '</script>';*/
}
catch(Exception $e)
{	
$this->str = 'Caught exception: ' .  $e->getMessage();
//echo 'Caught exception: ',  $e->getMessage(), "\n";
$consignment->setStatus(Consignment::STATUS_INVALID);
} 							 

return $consignment;
} 
if($returnconsignmentarray[0] == "ERROR")
{
$this->str = 'Error: ' .  $returnconsignmentarray[1];
$consignment->setStatus(Consignment::STATUS_INVALID);
//$con_relabel->setMessage($returnconsignmentarray[1]);
echo "<script language='javascript'> alert('". $returnconsignmentarray[1]."'); </script>" ;
$consignment->savelog('Single Failed At API Stage: ' .$returnconsignmentarray[1], 'A');
$consignment->save();
//  echo $returnconsignmentarray[1]; exit;
return $consignment;
}
}

/***
* Gets the full path to the folder
*/
public function getFullPath($fileName)
{
$path = "../_assets/pdf/" . date("Y_m_d") . "/";

if (!file_exists($path)) @mkdir($path, 0775);

return $path . $fileName;
}

private function loadServiceTypes()
{

$serviceFilter = new ServiceFilter();
$serviceArr = $serviceFilter->getColumnList("name, code, carrier");

//foreach($list as $service)
//{
//}


/*$serviceArr = array(
'Hungary Post Registered Mail EUR',
'Hungary Post Registered Mail International',								
'Sweden Post Registered Mail EUR',
'Sweden Post Registered Mail International',												
"WHISTL",
'E-EURO CORREOS',
'Royal Mail 2nd Untracked',
'Royal Mail Tracked 48',
'Royal Mail Tracked 48 with Sign',
'Hermes @2 Day',
'Hermes @2 Day Signed',
'Yodel Mini Pack',
'Yodel @Home 72',
'Yodel @Home 24',								
'DHL Europe Air Express (ECX)',
'DHL INT Express NDX (WPX)',
'DHL INT Express DOCS (DOX)',
'DHL Europe Road Economy (ESU)',
'Cacesa Express',
'DHL INT Express NDX (WPX - DDP)'				


);*/

//$conCarrierFilter  = new ServiceFilter();
//$serviceData   = $conCarrierFilter->getDistinctList('carrier_name');

//echo "kazim..." . $this->form_vars["service_type"];

echo '<select name="service_type" id="service_type" width="" class="selectpicker" placeholder="Service Type" rel="tooltip" title="Service Type" data-live-search="true" >';
//if (!isset($this->form_vars["service_type"]) || $this->form_vars["service_type"]=="Please Select a Value")
//{
echo '<option value=""> Select Service</option>';
//}



//die;

foreach ($serviceArr as $service)
{
$selected = "";
if (isset($this->form_vars["service_type"]) && $this->form_vars["service_type"] == $service->getCode())
{
$selected = " selected=\"SELECTED\"";
}
else
$selected = '';

echo '<option value="' . $service->getCode(). '"' . $selected . '>';
echo $service->getName();
echo '</option>';
}
echo '</select>';


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