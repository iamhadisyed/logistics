<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');

class Page extends BasePage {

    // source page for consignment edit
    private $source;
    private $readonly = array();
    private $error = array();

    /*     * *
     * Controller logic
     */

    protected function init() {
        // save source page info
        $this->source = @$_GET['from'];
        $user = SessionManager::getUser()->getUserAccount();



        // Get current user
        $user = SessionManager::getUser();


        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            $error_array = array();
            //echo '<pre>';
            //print_r($_POST);
            // take appropriate action



            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - validate consignment details - if OK, save and return to client list
                case "save":

                    if (trim($this->form_vars["user_account"]) == '' || $this->form_vars["user_account"] == '-- Select --') {
                        $this->error[] = 'Please select account.';
                    }

                    if (trim($this->form_vars['hawb']) == '' && trim($this->form_vars["awb"]) == '') {
                        $this->error[] = 'Please enter HAWB or Tracking number.';
                    }
                    if (empty($this->form_vars['address_line_1'])) {
                        $this->error[] = 'Please enter address line 1.';
                    }
                    if (trim($this->form_vars['contact']) == '' && trim($this->form_vars['company'])) {
                        $this->error[] = 'Please enter either contact or company name.';
                    }
                    if (trim($this->form_vars['contact']) == '' && trim($this->form_vars['company'])) {
                        $this->error[] = 'Please enter either contact or company name.';
                    }


                    /* if($this->form_vars['weight'] <= 0 || $this->form_vars['weight'] == '')
                      {
                      $this->error[] = 'Please enter weight and it should be greater than 0.';
                      } */

                    /* if(isset($this->form_vars['service_type']))
                      {
                      if($this->form_vars['service_type'] ==  'Please Select Service' || $this->form_vars['service_type'] == '')
                      {
                      $this->error[] = 'Please select service.';
                      }
                      } */

                    if ($this->form_vars['city'] == '') {
                        $this->error[] = 'Please enter city.';
                    }

                    if (count($this->error) == 0) {

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
        else {


            // Adding a new consignment
            $this->form_vars["new"] = "true";
        }

        // New readonly options
        /* if (@$this->form_vars["new"] == "true")
          {
          // If ware house user, then can entered any account number
          if ($user->hasPrivilege(User::PRIVILEGE_WAREHOUSE_LIST))
          {
          $this->readonly["account"] = "";
          }
          // new service, user has to enter HAWB
          //$this->readonly["hawb"] = "";
          } */


        // common initialisation for ths page
        $this->setTitle("Shipment Query");

        // save session variables
        $_SESSION["consignment_edit_source"] = $this->source;
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    private function showMessage() {
        if (isset($this->form_vars['hawb'])) {

            $str = '';
            if (count($this->error) > 0) {
                foreach ($this->error as $err) {
                    $str .= $err . '<br>';
                }

                echo '<script>$("#msg").addClass("error");</script>' . $str;
            } else {
                $str = 'Changes saved successfully';
                echo '<script>$("#msg").addClass("success");</script>' . $str;

                $this->form_vars["hawb"] = "";
            }
        }
    }

    protected function renderHead() {
        ?>

        <script type="text/javascript">

            function clear()
            {
                alert('jo');
                $("#company").text('');
                $("#contact").text('');
                $("#hawb").text('');
                $("#awb").text('');


                $('#address_line_1').text('');
                $('#address_line_2').text('');
                $('#address_line_3').text('');
                $('#city').text('');
                $('#country').text('');
                $('#postcode').text('');
                $('#telephone').text('');
                $('#weight').text('');
            }


            $(document).ready(function (e) {




                $("#btnSave").click(function () {
                    $("#form_action").val("save");
                    $("#adminForm").submit();
                })


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




                $('#hawb').keypress(function (e)
                {

                    var keyCode = e.which;
                    if (keyCode == 13)
                    {
                        $("#overlay").show();
                        var hawb_number = $.trim($('#hawb').val());
                        if (hawb_number == '')
                            alert('Please enter hawb/tracking number.');

                        //alert(hawb_number);
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {action: 'GetShipmentData', hawb: hawb_number},
                            success: function (data)
                            {
                                $("#overlay").hide();

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
                                    $('#country').val(obj.country);
                                    $('#postcode').val(obj.postcode);
                                    $('#telephone').val(obj.telephone);
                                    $('#weight').val(obj.weight);
                                    $('#oldService').val(obj.service);

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





            });






        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>

        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/show_address.php">Address</a></li>
            <li><a href="../main/services.php?id=<?php echo util_get_num("id"); ?>"><?php if (util_get_num("id") > 0) echo 'Edit';
        else echo 'Add'; ?></a></li>
        </ul>
        <?php
        $id = util_get_num("id");
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }

        // all fields read-only unless consignment is NEW, INVALID, VALID
        // - might change later  // switch
        ?>
        <div class="clear" ></div>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-action-undo"></i>
        <?php errorList::getItem()->render(); ?>RETURN Shipment</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <div id='msg'>
                        <? $this->showMessage(); ?>
                    </div>
        <?php errorList::getItem()->render(); ?>	
                    <div class="row">
                        <div class="col-md-12">    <h2 style="margin-left: 15px;">Shipment Detail</h2></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <select  name="user_account"  id="user_account" class="form-control">
                                        <option value="">-- Select -- </option>
        <?php
        $userAccountDb = new UserAccountFilter();
        $userAccountDb->addActiveFlagFilter();
        $userAccountDb->AddOrderByAccount(true);
        $userAccountDb->addIsDeletedFilter('NO');

        foreach ($userAccountDb->getList() as $userDataDb) {
            if ($userDataDb->getUserAccount() == $user_account)
                $selectedAccountNumber = 'selected="selected"';
            else
                $selectedAccountNumber = '';
            echo '<option value="' . $userDataDb->getUserAccount() . '" ' . $selectedAccountNumber . ' >' . $userDataDb->getUserAccount() . '</option>';
        }
        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type="text" id="hawb" name="hawb" size="20" placeholder="HAWB" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type="text" id="awb" name="awb" size="20" placeholder="AWB" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type="text" id="location" name="location" placeholder="LOCATION" size="20" maxlength="45" class="form-control" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12"><h2 style="margin-left: 15px;">Address Details</h2></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='company' id='company' placeholder="COMPANY" maxlength="30"  value="OneWorld Express Inc. Ltd" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='contact' placeholder="CONTACT" maxlength="30" id='contact'  value="OneWorld" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' maxlength="30" name='address_line_1' id='address_line_1' placeholder="ADDRESS LINE 1"  value="OneWorld Express" class="form-control" />
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' maxlength="30" name='address_line_2' id='address_line_2' placeholder="ADDRESS LINE 2"  value="Pump Lane" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' maxlength="30" name='address_line_3' value="Hayes" id='address_line_3' placeholder="ADDRESS LINE 3" size='100' class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='city' id='city' size='15' maxlength="30" placeholder="CITY" class="form-control" value="London" />
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='postcode' id='postcode' placeholder="POSTCODE" size='20' value='UB3 3NB'  class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                                    <select  name="country"  size="1" id="country" class="form-control">
        <?php
        $countriesList = new CountryFilter();
        $countryListData = $countriesList->getList();
        $selectedcountryName = "UNITED KINGDOM";
        foreach ($countryListData as $countryItem) {
            $countryName = strtoupper($countryItem->getName());

            if (strtolower(trim($selectedcountryName)) == strtolower(trim($countryItem->getName())))
                echo '<option value="' . $countryName . '" selected="selected">' . $countryName . '</option>';
            else
                echo '<option value="' . $countryName . '" >' . $countryName . '</option>';
        }
        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='weight' id='weight' placeholder="WEIGHT" size='20' class="form-control"  />
                                </div>
                            </div>
                        </div>



                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                        <?php
                                        if (isset($readonly_str) && $readonly_str == "") {
                                            ?>
                                        <select name='number_pieces' class="form-control" width='20px' id='number_pieces' >

                                            <?php
                                            for ($i = 1; $i < 40; $i++) {
                                                ?>
                                                <option value="<?php echo $i; ?>" <?php echo (@$number_pieces == $i) ? "selected" : "" ?>><?php echo $i; ?></option>
                <?php
            }
            ?>
                                        </select>
            <?php
        } else {
            ?>
                                        <input type='text' name='number_pieces' placeholder="NUMBER OF PIECES" id='number_pieces' class="form-control"
                                               value='<?php echo @$number_pieces; ?>' <?php if (isset($readonly_str)) echo $readonly_str ?> size='10'
                                               /></td>
            <?php
        }
        ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='length' id='length' placeholder="LENGTH" class="form-control"  size='5' />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='width' id='width' placeholder="WIDTH" class="form-control" size='5' />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>
                                    <input type='text' name='height' id='height' placeholder="HEIGHT" class="form-control" size='5' />
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="row" > 
                        <div class="col-md-12">
                            <a id="btnCancel" href="#" class="btn btn-danger btn_cancel"><span></span>Cancel</a>
                            <a id="btnSave" href="#" class="btn btn-primary btn_save"><span></span>Save</a>

                            <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                            <input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />
                            <input type="hidden" name="label_file_id" id="label_file_id" value="<?php echo @$label_file_id; ?>" />
                        </div>
                    </div>
                </div>

            </div>


        </div>
        <?php
    }

    public function saveFormDataToObject() {


        $option = '';
        $comments = '';

        $consignment = new Consignment();

        $carrierName = $this->form_vars['service_type'];

        if (isset($this->form_vars['comments']))
            $comments = $this->form_vars['comments'];

        /* if(isset($this->form_vars['code']))
          $handling = trim($this->form_vars['code']); */

        if (isset($this->form_vars['option'])) {
            $option = $this->form_vars['option'];
        }

        $account = trim($this->form_vars["user_account"]);
        $hawb = trim($this->form_vars['hawb']);
        $awb = trim($this->form_vars['awb']);
        $company = trim($this->form_vars['company']);
        $contact = trim($this->form_vars['contact']);
        $address_line_1 = trim($this->form_vars['address_line_1']);
        $address_line_2 = trim($this->form_vars['address_line_2']);
        $address_line_3 = trim($this->form_vars['address_line_3']);
        $city = trim($this->form_vars['city']);
        $country = trim($this->form_vars['country']);
        $postcode = trim($this->form_vars['postcode']);
        $country = trim($this->form_vars['country']);
        $telephone = trim($this->form_vars['telephone']);
        $weight = trim($this->form_vars['weight']);
        $number_pieces = trim($this->form_vars['number_pieces']);
        $length = trim($this->form_vars['length']);
        $width = trim($this->form_vars['width']);
        $height = trim($this->form_vars['height']);
        $comments = trim($this->form_vars["comments"]);
        $location = trim($this->form_vars["location"]);


        $countryFilter = new CountryFilter();
        $countryFilter->addNameArrayFilter($country);
        $country_list = $countryFilter->getColumnList('iso');
        $countryObj = '';

        if (count($country_list) > 0) {
            $countryObj = $country_list[0];
        }

        $country_iso_code = $countryObj->getIso();

        $consignment->setHawb($hawb);
        $consignment->setAwb($awb);
        $consignment->setAccount($account);
        $consignment->setDateSubmitted(time());
        $consignment->setService("returned");
        $consignment->setHandling("RTN");
        $consignment->setStatus(Consignment::STATUS_RETURNED);
        $consignment->setDateBooked(time());
        $consignment->setDateScanned(date("Y-m-d G:i:s"));

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
        $consignment->setNumberPieces($number_pieces);
        $consignment->setRoutingCode("S");
        $consignment->setLocation($location);
        $consignment->save();

        $parcel = new Parcel();
        $parcel->setConsignmentID($consignment->getId());
        $parcel->setLength($length);
        $parcel->setWidth($width);
        $parcel->setHeight($height);
        $parcel->save();


        $consignment_filter = new ConsignmentFilter();
        if ($awb != "" && $hawb != "") {
            /* $consignment_filter->addAwbAndHawbOrFilterNotRecycled($awb);
              $con_list  = $consignment_filter->getList(); */
            $label = new GlOrderPdfReturn();

            $filename = $label->buildPDFDocuments($consignment->getId());
            $returnStringParse = explode("||", $filename);
            //echo $returnStringParse[1];
            ?>
<iframe id="iFramePdf" style="display:none;" src="<? echo $returnStringParse[1]; ?>" style="width: 619px; height: 482px;"></iframe>
<script type="text/javascript">
    var getMyFrame = document.getElementById('iFramePdf');
    window.onload = function ()
    {
        try {
            getMyFrame.contentWindow.print();
        } catch (e) {
            alert("Exception thrown: " + e);
        }
    }


//window.onload = setTimeout("getMyFrame.contentWindow.print()", 1000);
    document.getMyFrame.printMe()


</script>

<?



/*echo "<script>window.open('".$returnStringParse[1]."','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');" ;
//echo " window.location = '../main/consignment_return.php'</script>";*/


//GlOrderPdfReturn::buildPDFDocuments($con_list);
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