<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
], 'labels');
include_classes([
    'tcpdf'
], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
], 'general');

include_classes([
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'services.class',
    'servicefilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmentrelabel.class',
    'consignmentrelabelfilter.class',
    'carrier.class',
    'carrierfilter.class',

]);

class Page extends BasePage
{

// source page for consignment edit
    private $source;
    private $id = NULL;
    private $readonly = array();
    private $error = array();
    private $str;
    private $chkAutoPrint;
    private $userObj;

    /*     * *
     * Controller logic
     */

    protected function init()
    {
// save source page info
        $user = SessionManager::getUser();
        $this->userObj = $user;
//        if ($user->getUserType() == User::USER_TYPE_CLIENT) {
//            util_redirect("index.php");
//        }
        $this->source = @$_GET['from'];
//        $currentPage_title 
        if (util_get_num("id") > 0) {
            $currentPage_title = 'Edit';
        } else {
            $currentPage_title = 'Add';
        }
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            '#' => 'Relabel Shipment'
        );
// Get current user
        $user = SessionManager::getUser();
// is this form being posted back?
        if (isset($_POST["form_action"])) {
            $error_array = array();
            switch ($_POST["form_action"]) {
                case "open_print_dialog":

                    $fileName = $this->form_vars['filename'];

                    if ($fileName == '') {

                        $hawb = trim($this->form_vars['hawb']);
                        $consignment_filter = new ConsignmentFilter();
                        $consignment_filter->addAwbAndHawbOrFilterNotRecycled($hawb);
                        $con_list = $consignment_filter->getColumnList("hawb, 
                                                                        company,
									account,
                                                                        service,
                                                                        notes,
                                                                        reference,
                                                                        country_iso_code,
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
									service_type,
                                                                        currency,
                                                                        value,
                                                                        description,
                                                                        routing_code,
                                                                        single_label,
                                                                        length,
                                                                        width,
                                                                        height");
                        if (count($con_list) > 0) {
                            $con = $con_list[0];
//print_r($con);																									
                            $this->CreateLabel($con);
                            $fileName = $con->getSingleLabel();
                        }
                    } else {
                        ?>
                        <iframe id="iFramePdf" src="<?php echo $fileName; ?>"
                                style="display:none;width: 619px; height: 482px;"></iframe>
                        <script type="text/javascript">
                            var printFrame = document.getElementById('iFramePdf');

                            if (printFrame) {
                                printFrame.contentWindow.print();
                            } else {
                                PDFViewerApplication.pdfDocument.getData().then(function (res) {
                                    var src = URL.createObjectURL(new Blob([res], {type: 'application/pdf'}));
                                    printFrame = document.createElement('iframe');
                                    printFrame.id = 'print-frame';
                                    printFrame.style.display = 'none';
                                    printFrame.src = src;
                                    document.body.appendChild(printFrame);
                                    setTimeout(function () {
                                        printFrame.contentWindow.print();
                                    }, 0)
                                })
                            }
                            // alert(getMyFrame);
                            //  getMyFrame.contentWindow.print()
                            // window.onload = setTimeout("getMyFrame.contentWindow.print()", 1000);
                            // document.iFramePdf.printMe();					
                            $("#hawb").val('');
                            document.getElementById("tracking").focus();
                        </script>
                        <?php
                    }
                    if ($this->form_vars['chkAutoPrint'] == "on")
                        $this->chkAutoPrint = "checked";
                    else
                        $this->chkAutoPrint = "";

                    break;

                case "save":
                    $this->saveFormDataToObject();
                    break;
                default:
                    break;
            }
        } // not post back - first time this form is shown
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

    private function showMessage()
    {
        if (isset($_POST['hawb'])) {

            $str = '';
            if (count($this->error) > 0) {
                foreach ($this->error as $err) {
                    $this->str .= $err . '<br>';
                }

                echo "<script>$('#msg').addClass('alert-danger');</script>" . $this->str;
            } else {
//$str = 'Changes saved successfully';
                echo "<script>$('#msg').addClass('alert-success');</script>" . $this->str;
            }
        }
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <?php
    }

    protected function renderHead()
    {
        ?>
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

            .tableA {


                margin-right: 200px;
                display: inline-block;
            }

            .error {
                font-size: 18px;
                color: red;
            }

            .success {
                font-size: 18px;
                color: green;
            }

            .web_dialog_overlay {
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

            .web_dialog_alert {
                position: fixed;
                top: 50%;
                left: 50%;

                padding: 0px;
                z-index: 102;
                font-family: Verdana;
                font-size: 10pt;
            }

            .custom_textbox_style {
                margin-left: 16px;
                width: 94%;
            }
        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <?php
        $id = util_get_num("id");
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="icon-bar-chart"></i>
                    Relabel Shipment
                </div>
                <div class="actions">
                    <a href="relabel_consignment_list.php" class="btn blue"> Relabel Consignment List </a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="row" id="show_general_msg" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <form method="post" action="javascript:;" enctype="multipart/form-data" id="relabelShipmentForm"  name="relabelShipmentForm" role="form">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Only accecpt
                                    shipment tracking number</strong></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="caption margin-bottom-10 block">
                                <span class="caption-subject bold uppercase">Shipment Detail</span>
                            </div>
                        </div>
                    </div>
                    <!--NEW SECTION START-->
                    <div class="row">

                        <div class="col-md-3" id="hawb_form_div">

                            <div class="form-group">

                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-tags"></i>
                                    <i id="loader" name="loader" class="fa fa-spinner fa-spin icon-large hidden"></i>


                                    <input name="tracking" id="tracking" value="<?php echo @$tracking; ?>" size="50"
                                           class="form-control" title="" placeholder="Tracking" rel="tooltip"
                                           data-original-title="Tracking" type="text" required>

                                    <input type="hidden" id='option' name="option" value='Relabel'/> <label
                                        for="tracking">Tracking <i class="font-red"
                                                                   data-original-title="Tracking number is Mandatory">*</i></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">


                            <div class="form-group">

                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-ticket"></i>

                                    <input name="oldService" id="oldService" value="<?php echo @$oldService; ?>"
                                           size="80"
                                           class="form-control" title="" readonly placeholder="Selected Service"
                                           rel="tooltip"
                                           data-original-title="Selected Service" type="text"> <label for="oldService">Selected
                                        Service</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">


                            <div class="form-group">
                                <div class="has-float-label">


                                    <?php
                                    // Get user account services
                                    $userAccountId = $this->userObj->getUserAccountId();
                                    $userServicesRoutingFilter = new UserServicesRoutingFilter();
                                    $userServicesRoutingFilter->addFieldFilter("user_account_id", $userAccountId);
                                    //                                    $userServicesRoutingFilter->addFieldFilter("is_agreed", "1");
                                    $userServicesRoutingFilter->addFieldFilter("status", "1");
                                    $userServicesRoutingFilter->setGroup("service_id");
                                    $userServiceObj = $userServicesRoutingFilter->getColumnList("service_id");
                                    $userServiceArr = "";
                                    foreach ($userServiceObj as $userService) {
                                        $userServiceArr .= "'" . $userService->getServiceId() . "',";
                                    }
                                    $userServiceArr = rtrim($userServiceArr, ",");

                                    echo Ddl::generateServiceDDLWithImage('service_type', $selected_value, 'id', ' class="bs-select input-sm form-control form-filter" required="" data-live-search="true" data-show-subtext="true" ', '', '', 'name', 'Select Services'); ?>
                                    <input type="hidden" id="code" name='code'/>
                                    <input type="hidden" id="filename" name="filename"/> <label>New Service</label>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label><input type="checkbox" class="icheck" <?php echo $this->chkAutoPrint ?>
                                                      id="chkAutoPrint" name="chkAutoPrint"
                                                      data-checkbox="icheckbox_square-blue"></label> <label>Auto Print</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-top-10">
                        <div class="col-md-12">
                            <div class="caption margin-bottom-10 block">
                                <span class="caption-subject bold uppercase">Address Details</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">


                            <div class="form-group">
                                <div class="has-float-label input-icon right">

                                    <i class="fa fa-key"></i>


                                    <input name="company" id="company" value="<?php echo @$company; ?>" maxlength="30"
                                           size="100"
                                           class="form-control" title="" placeholder="Company" rel="tooltip"
                                           data-original-title="Company" type="text" required>
                                    <label for="company">Company <i class="font-red" data-original-title="Company is mandatory">*</i></label>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">


                            <div class="form-group">
                                <div class="has-float-label input-icon right">

                                    <i class="fa fa-book"></i>


                                    <input name="city" id="city"
                                           value="<?php echo(!empty($city) ? DbAccess3::escape($city) : ''); ?>" size="15"
                                           class="form-control" maxlength="30"
                                           title="" placeholder="City" rel="tooltip" data-original-title="City" type="text"
                                           required>
                                    <label for="city">City <i class="font-red"
                                                              data-original-title="City is Mandatory">*</i></label>


                                </div>
                            </div>
                        </div>


                        <div class="col-md-3">


                            <div class="form-group">
                                <div class="has-float-label">
                                    <select id='currency' class='select_dropdown_con form-control select2' name='currency'
                                            style='' <?php echo $readonly_str ?> >
                                        <?php
                                        $arrayCurrency = array('GBP', 'USD', 'CAN', 'DFL', 'DKR', 'EUR', 'FFR', 'HKG', 'INR', 'JPY', 'NKR', 'NLG', 'SGD', 'SFR', 'SKR', 'YEN', 'PLN');
                                        foreach ($arrayCurrency as $currency) {
                                            if (@$this->form_vars['currency'] == $currency)
                                                $selectedCurrency = 'selected="selected"';
                                            else
                                                $selectedCurrency = '';

                                            echo '<option value="' . $currency . '" ' . $selectedCurrency . '>' . $currency . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label>Currency</label>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="has-float-label">
                                    <select name='number_pieces' id='number_pieces'
                                            class="select_dropdown_con form-control select2" placeholder="Pieces" rel="tooltip"
                                            data-original-title="Pieces">

                                        <?php
                                        for ($i = 1; $i < 150; $i++) {
                                            ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <label>Pieces</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">


                            <div class="form-group">

                                <div class="has-float-label  input-icon right">

                                    <i class="fa fa-user"></i>


                                    <input name="contact" id="contact" maxlength="30" value="<?php echo @$contact; ?>"
                                           size="100"
                                           class="form-control" title="" placeholder="Contact Person" rel="tooltip"
                                           data-original-title="Contact Person" type="text" required>
                                    <label for="contact">Contact Person <i class="font-red"
                                                                           data-original-title="contact is mandatory">*</i></label>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">


                            <div class="form-group">

                                <div class="has-float-label  input-icon right">

                                    <i class="fa fa-qrcode"></i>

                                    <input name="postcode" id="postcode" value="<?php echo @$postcode; ?>" size="20"
                                           class="form-control"
                                           title="" placeholder="Postcode" rel="tooltip" data-original-title="Postcode"
                                           type="text">
                                    <label for="postcode">Postcode</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">


                            <div class="form-group">

                                <div class="has-float-label  input-icon right">


                                    <i class="fa fa-tags"></i>

                                    <input name="value" id="value" value="<?php echo @$value; ?>" size="50" class="form-control"
                                           title="" placeholder="Values" rel="tooltip" data-original-title="Values" type="text">
                                    <label for="value">Values</label>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">


                            <div class="form-group">

                                <div class="has-float-label">


                                    <select class='select_dropdown_con form-control select2' name="itemtype" id="itemtype">
                                        <?php
                                        $itemType = array("Normal", "Letter", "Packets", "Lithium Ion Battery", "Lithium Metal Battery", "Perfume", "Fire Extinguisher");
                                        foreach ($itemType as $itype)
                                            printf("<option value=\"%s\" %s>%s</option> ", $itype, ($selectedItemType == $itype ? "selected='selected'" : ""), $itype);
                                        ?>
                                    </select>
                                    <label>Item Type</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">


                            <div class="form-group">

                                <div class="has-float-label  input-icon right">
                                    <i class="fa fa-map-marker"></i>


                                    <input name="address_line_1" id="address_line_1" maxlength="30"
                                           value="<?php echo @$address_line_1; ?>"
                                           size="100" class="form-control" title="" placeholder="Address Line 1" rel="tooltip"
                                           data-original-title="Address Line 1" type="text" required>

                                    <label for="address_line_1">Address Line 1 <i class="font-red"
                                                                                  data-original-title="Address Line 1 is Mandatory">*</i></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">


                                <div class="has-float-label  input-icon right">
                                    <i class="fa fa-map-marker"></i>

                                    <input name="address_line_2" id="address_line_2" value="<?php echo @$address_line_2; ?>"
                                           size="100"
                                           class="form-control" title="" placeholder="Address Line 2" rel="tooltip"
                                           data-original-title="Address Line 2" type="text">
                                    <label for="address_line_2">Address Line 2</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="has-float-label  input-icon right">
                                    <i class="fa fa-map-marker"></i>
                                    <input name="address_line_3" id="address_line_3" value="<?php echo @$address_line_3; ?>"
                                           size="100"
                                           class="form-control" title="" placeholder="Address Line 3" rel="tooltip"
                                           data-original-title="Address Line 3" type="text">
                                    <label for="address_line_3">Address Line 3</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="has-float-label">

                                    <?php echo Ddl::generateCountryDDL('country_id_new', "", 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8"');
                                    ?>
                                    <label>Country</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!--                                <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Length</label>
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input name="length" id="length" value="<?php echo @$length; ?>" size="20" class="form-control" title="" placeholder="Length" rel="tooltip" data-original-title="Length" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Width</label>
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input name="width" id="width" value="<?php echo @$width; ?>" size="20" class="form-control" title="" placeholder="Width" rel="tooltip" data-original-title="Width" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Height</label>
                                                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                                                    <div class="input-icon right">
                                                                        <input name="height" id="height" value="<?php echo @$height; ?>" size="20" class="form-control" title="" placeholder="Height" rel="tooltip" data-original-title="Height" type="text">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>-->
                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="has-float-label  input-icon right"><i class="fa fa-phone"></i>
                                    <div class="input-icon right">
                                        <input name="telephone" id="telephone" value="<?php echo @$telephone; ?>" size="20"
                                               class="form-control"
                                               title="" placeholder="Telephone" rel="tooltip" data-original-title="Telephone"
                                               type="text"
                                               onkeypress="return numbersonly(event)">
                                        <label for="telephone">Telephone</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="has-float-label  input-icon right">
                                    <i class="fa fa-ticket"></i>

                                    <input name="description" id="description" value="<?php echo @$description; ?>" size="50"
                                           class="form-control"
                                           title="" placeholder="Description" rel="tooltip" data-original-title="Description"
                                           type="text">
                                    <label for="description">Description</label>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="has-float-label  input-icon right"><i class="fa fa-ticket"></i>
                                    <textarea name='comments' rows='1' cols='10' id='comments' class="form-control"
                                              placeholder="Comments" rel="tooltip" data-original-title="Comments"></textarea>
                                    <label for="comments">Comments</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">

                                <div class="has-float-label  input-icon right"><i class="fa fa-ticket"></i>

                                    <input name="weight" id="weight" value="<?php echo @$weight; ?>" size="20"
                                           class="form-control" title="" placeholder="Weight" rel="tooltip"
                                           data-original-title="Weight" type="text">
                                    <label for="weight">Weight</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--                        <div class="row">
                                                <div class="col-md-12">

                                                </div>
                                            </div>-->
                    <!--END NEW SECTION START-->
                    <div class="row" style="text-align:center;">
                        <input id="btnSave" type="button" class="btn btn-primary btn_save"
                               value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                        <input id="btnCancel" type="button" class="btn btn-default btn_cancel"
                               value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                        <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>"/>
                        <input type="hidden" name="parcel_id" id="parcel_id" value=""/>
                        <input type="hidden" name="form_action" id="form_action" value=""/>
                        <input type="hidden" name="old_data" id="old_data" value=""/>
                        <input type="hidden" name="label_file_id" id="label_file_id" value="<?php echo @$label_file_id; ?>"/>
                    </div>
                </form>
            </div><!--portlet-body-->
        </div>
        <?php
    }

    public function saveFormDataToObject()
    {
        $option = '';
        $comments = '';
        $carrierName = $_POST['service_type'];
        if (isset($_POST['comments']))
            $comments = $_POST['comments'];

        if (isset($_POST['option'])) {
            $option = $_POST['option'];
        }
        $oldJsonArr = [];
        $oldJsonDatasubmit = json_decode(trim($_POST['old_data']), true);
        $oldJsonArr['company'] = $oldJsonDatasubmit['company'];
        $oldJsonArr['city'] = $oldJsonDatasubmit['city'];
        $oldJsonArr['currency'] = $oldJsonDatasubmit['currency'];
        $oldJsonArr['number_pieces'] = $oldJsonDatasubmit['number_pieces'];
        $oldJsonArr['contact'] = $oldJsonDatasubmit['contact'];
        $oldJsonArr['postcode'] = $oldJsonDatasubmit['postcode'];
        $oldJsonArr['value'] = $oldJsonDatasubmit['value'];
//        $oldJsonArr['itemtype'] = "";//Not Found
        $oldJsonArr['address_line_1'] = $oldJsonDatasubmit['addressline1'];
        $oldJsonArr['country'] = $oldJsonDatasubmit['country_id_new'];
        $oldJsonArr['description'] = $oldJsonDatasubmit['description'];
        $oldJsonArr['address_line_2'] = $oldJsonDatasubmit['addressline2'];
        $oldJsonArr['telephone'] = $oldJsonDatasubmit['telephone'];
        $oldJsonArr['address_line_3'] = $oldJsonDatasubmit['addressline3'];
        $oldJsonArr['weight'] = $oldJsonDatasubmit['weight'];
        $oldJsonArr['length'] = $oldJsonDatasubmit['length'];
        $oldJsonArr['width'] = $oldJsonDatasubmit['width'];
        $oldJsonArr['height'] = $oldJsonDatasubmit['height'];
        $oldJson = json_encode($oldJsonArr);

        $company = trim($_POST['company']);
        $city = trim($_POST['city']);
        $currency = $_POST['currency'];
        $pieces = $_POST['number_pieces'];
        $contact = trim($_POST['contact']);
        $postcode = trim($_POST['postcode']);
        $value = $_POST['value'];
//        $selectedItemType = $_POST['itemtype'];
        $address_line_1 = trim($_POST['address_line_1']);
        $country = trim($_POST['country_id_new']);
        $description = $_POST['description'];
        $address_line_2 = trim($_POST['address_line_2']);
        $telephone = trim($_POST['telephone']);
        //Comments are above added to variable
        $address_line_3 = trim($_POST['address_line_3']);
        $weight = trim($_POST['weight']);
        $length = trim($_POST['length']);
        $width = trim($_POST['width']);
        $height = trim($_POST['height']);
        $consignmentId = trim($_POST['id']);
        $parcelId = trim($_POST['parcel_id']);

        //Save consignment
        $consignment = new Consignment($consignmentId);
        $consignment->setCompany($company);
        $consignment->setCity($city);
        $consignment->setCurrency($currency);
        $consignment->setNumberPieces($pieces);
        $consignment->setContact($contact);
        $consignment->setPostCode($postcode);
        $consignment->setValue($value);
//        $consignment->setItemType($selectedItemType);
        $consignment->setAddressLine1($address_line_1);
        $consignment->setCountryId($country);
        $consignment->setDescription($description);
        $consignment->setAddressLine2($address_line_2);
        $consignment->setTelephone($telephone);
        $consignment->setAddressLine3($address_line_3);
        $consignment->setWeight($weight);
        $consignment->save();
        if ($parcelId > 0) {
            //Save parcel Fields
            $parcel = new Parcel($parcelId);
            $parcel->setLength($length);
            $parcel->setWidth($width);
            $parcel->setHeight($height);
            $parcel->save();
        }
        $jsonDataArr = [];
        $jsonDataArr['company'] = $company;
        if (preg_match('/^[a-zA-Z1-9 \d]+$/', $city)) {
            $city = $city;
        } else {
            $city = '';
        }
        $jsonDataArr['city'] = $city;
        $jsonDataArr['currency'] = $currency;
        $jsonDataArr['number_pieces'] = $pieces;
        $jsonDataArr['contact'] = $contact;
        $jsonDataArr['postcode'] = $postcode;
        $jsonDataArr['value'] = $value;
//        $jsonDataArr['itemtype'] = $selectedItemType;
        $jsonDataArr['address_line_1'] = $address_line_1;
        $jsonDataArr['country'] = $country;
        $jsonDataArr['description'] = $description;
        $jsonDataArr['address_line_2'] = $address_line_2;
        $jsonDataArr['telephone'] = $telephone;
        $jsonDataArr['address_line_3'] = $address_line_3;
        $jsonDataArr['weight'] = $weight;
        $jsonData = json_encode($jsonDataArr);

//        addDataIntoConsignmentRelabel($oldJson, $jsonData, $consignment->getAwb(), $consignment->getAwb(), $consignmentId);
        echo "Consignment data saved successfully";
        die;
//        
//        
//        $consignment->setHawb($consignment->getHawb());
//        $consignment->setConsignmentStatus('valid'); // STATUS SHOULD BE VALID FOR RELABEL
//        $consignment->setHandling();
//        $consignment->setService($service->getType());
//        $consignment->setHandling($handling);
//        $consignment->setReference($consignment->getReference());
//        $consignment->setDateReceived($consignment->getDateReceived());
//        $consignment->setType($consignment->getType());
//        $consignment->setDateSubmitted(time());
//        $consignment->setDateImported(time());
//        $consignment->setDateScanned(date("Y-m-d H:i:s"));
////            $consignment->setCountry($country);
//        
//        
//        
//        
//        
////        $hawb = trim($_POST['hawb']);
//        $country_iso_code = $country;
//        $consignment_filter = new ConsignmentFilter();
//        $consignment_filter->addAwbAndHawbOrFilterNotRecycled($hawb);
//        $con_list = $consignment_filter->getList();
//
////////////////// CREATE A NEW LABEL WHEN THE NEW SERVICE IS SELECTED EITHER  ///////////
///// WITH NEW SHIPMENT INFO OR OLD BY THE USER /////////////////////
////echo $option . " " . $carrierName;
////die;		
//
//        if (count($con_list) > 0 && $option == 'Hold') {
////echo "saved";
//            $consignment = $con_list[0];
//            $consignment->setStatus('hold');
//            $consignment->setMessage($comments);
//            $consignment->save();
//            $this->showMessage();
//        } else if (count($con_list) > 0 && $carrierName !== '' && $option == 'Relabel') {
//
//            $serviceflr = new ServiceFilter();
//            $serviceflr->addCodeExactFilter($carrierName); /// SELECTED SERVICE BY THE USER
//            $serviceList = $serviceflr->getColumnList("name, code, carrier, carrier_name, type");
//
//            $service = $serviceList[0];
//            $carrier = $service->getCarrier();
//            $handling = $service->getCode();
//            $consignment = $con_list[0];
//            $con_relabel = new Consignment();
//            $con_relabel->setHawb($consignment->getHawb());
//            $con_relabel->setConsignmentStatus('valid'); // STATUS SHOULD BE VALID FOR RELABEL
//            $con_relabel->setHandling();
//            $con_relabel->setService($service->getType());
//            $con_relabel->setHandling($handling);
//            $con_relabel->setReference($consignment->getReference());
//            $con_relabel->setDateReceived($consignment->getDateReceived());
//            $con_relabel->setType($consignment->getType());
//
//            $con_relabel->setDateSubmitted(time());
//            $con_relabel->setDateImported(time());
//            $con_relabel->setDateScanned(date("Y-m-d H:i:s"));
//            $con_relabel->setCompany($company);
//            $con_relabel->setContact($contact);
//            $con_relabel->setAddressLine1($address_line_1);
//            $con_relabel->setAddressLine2($address_line_2);
//            $con_relabel->setAddressLine3($address_line_3);
//            $con_relabel->setCity($city);
////            $con_relabel->setCountry($country);
//            $con_relabel->setPostCode($postcode);
//            $con_relabel->setCountryIsoCode($country_iso_code);
//            $con_relabel->setTelephone($telephone);
//            $con_relabel->setNumberPieces($pieces);
//            $con_relabel->setWeight($weight);
//            $con_relabel->setItemType($selectedItemType);
////            print_r($con_relabel);
////            die;
//            if (trim($description) != '')
//                $con_relabel->setDescription($description);
//            else
//                $con_relabel->setDescription($consignment->getDescription());
//            if (trim($value) != '')
//                $con_relabel->setValue($value);
//            else
//                $con_relabel->setValue($consignment->getValue());
//            if (trim($currency) != '')
//                $con_relabel->setCurrency($currency);
//            else
//                $con_relabel->setCurrency($consignment->getCurrency());
//            $con_relabel->setNotes($consignment->getNotes());
//            $con_relabel->setRoutingCode($consignment->getRoutingCode());
//
//            $con_relabel->setServiceType($service->getName()); /// USER ENTERED SERVICE
//            $con_relabel->setMawb($consignment->getMawb());
//            $con_relabel->setBagNumber($consignment->getBagNumber());
//            $con_relabel->setBagWeight($consignment->getBagWeight());
//
//            $user = SessionManager::getUser()->getUserAccount();
//
//            /* if(trim($user) == 'OPERA')
//              {
//              $con_relabel->setAccount('OPERA');
//              $con_relabel->setWarehouseAccountRef($consignment->getAccount());
//              }
//              else
//              { */
//            $con_relabel->setAccount($consignment->getAccount());
//            if (trim($consignment->getWarehouseAccountRef()) == '')
//                $con_relabel->setWarehouseAccountRef($user);
//            else
//                $con_relabel->setWarehouseAccountRef($consignment->getWarehouseAccountRef());
////}
////$con_relabel->setRelabelShipment(1);
//            $consignment_validator = new ConsignmentValidator($con_relabel);
////$isValid = $consignment_validator->isValid();
//
//            if (!$consignment_validator->isValid()) {
//                foreach ($consignment_validator->getErrorList() as $error)
//                    $this->error[] = $error;
////print_r($this->error);
////die;
//                return;
//            }
//
//            $consignment->setDateBooked('');
//            $consignment->setStatus(Consignment::STATUS_RECYCLED);
//            $consignment->save();
//
//            $con_relabel->save();
////if($consignment_validator->isValid()
////echo "validation  " . $isValid;
////die;
//            $con_relabel = $this->CreateLabel($con_relabel);
//
//            $ConsignmentLog = new ConsignmentLog();
//            $ConsignmentLog->createlog("Shipment relabelled : Old Tracking Number " . $consignment->getAwb(), $con_relabel->getId());
//
//            $ConsignmentLog = new ConsignmentLog();
//            $ConsignmentLog->createlog("Shipment relabelled : New Tracking Number " . $con_relabel->getAwb(), $consignment->getId());
//
//            if (trim($comments) == '')
//                $consignment->setMessage('Please relabel the shipment ' . str_replace("..", SETTING_MAIN_URL, $con_relabel->getSingleLabel()));
//            else
//                $consignment->setMessage($comments . " " . str_replace("..", SETTING_MAIN_URL, $con_relabel->getSingleLabel()));
//
//            $consignment->save();
//
//            $user = SessionManager::getUser();
//
//            $consignmntRelabel = new ConsignmentRelabel();
//            $consignmntRelabel->setAccount($con_relabel->getAccount());
//            $consignmntRelabel->setOldTrackingNo($consignment->getAwb());
//            $consignmntRelabel->setNewTrackingNo($con_relabel->getAwb());
//            $consignmntRelabel->setDateCreated(time());
//            $consignmntRelabel->setUserId($user->getId());
//            $consignmntRelabel->save();
//
//            $this->TransferTrackPoints($consignment, $con_relabel);
//
//            TrackingData::AddVirtualTrackingToScanParcels(array($con_relabel->getAwb()), $user);
//        }
//        else if (count($con_list) > 0 && $option == 'Relabel') { //////////////// EXISTING SERVICE IS SELECTED AND CREATE A LABEL WITH NEW SHIPEMNT INFORMATION
////            echo "kazim2";
////            die;
//            $consignment = $con_list[0];
//            $oldTrackingNumber = $consignment->getAwb();
//
//            $consignment->setStatus('valid');
//            $consignment->setCompany($company);
//            $consignment->setContact($contact);
//            $consignment->setAddressLine1($address_line_1);
//            $consignment->setAddressLine2($address_line_2);
//            $consignment->setAddressLine3($address_line_3);
//            $consignment->setCity($city);
////            $consignment->setCountry($country);
//            $consignment->setPostCode($postcode);
//            $consignment->setCountryIsoCode($country_iso_code);
//            $consignment->setTelephone($telephone);
//            $consignment->setWeight($weight);
//            $consignment->setNumberPieces($pieces);
//            $consignment->setItemType($selectedItemType);
//
//            if (trim($description) != '')
//                $consignment->setDescription($description);
//            if (trim($value) != '')
//                $consignment->setValue($value);
//            if (trim($currency) != '')
//                $consignment->setCurrency($currency);
//
//            $consignment->setSingleLabel('');
////$consignment->setAwb(''); //// need to discuss with Kiran when we make one system then it should give us the same tracking number or send an
////// email to the customer
//            $consignment->save();
//            $con_relabel = $this->CreateLabel($consignment);
//
//            $user = SessionManager::getUser();
//
//            if (isset($con_relabel) && $con_relabel->getId() > 0) {
//
//                $consignmntRelabel = new ConsignmentRelabel();
//                $consignmntRelabel->setAccount($con_relabel->getAccount());
//                $consignmntRelabel->setOldTrackingNo($oldTrackingNumber);
//                $consignmntRelabel->setNewTrackingNo($con_relabel->getAwb());
//                $consignmntRelabel->setDateCreated(time());
//                $consignmntRelabel->setUserId($user->getId());
//                $consignmntRelabel->save();
//                $ConsignmentLog = new ConsignmentLog();
//                $ConsignmentLog->createlog("Shipment relabelled : Old Tracking Number " . $oldTrackingNumber . "New Tracking Number " . $con_relabel->getAwb(), $con_relabel->getId());
//
//                TrackingData::AddVirtualTrackingToScanParcels(array($con_relabel->getAwb()), $user);
//            }
//        }
    }

    public function TransferTrackPoints($old_consignment, $new_consignment)
    {
        $tracking_data_filter = new TrackingDataFilter();
        $tracking_data_filter->addTrackingNumberFilter($old_consignment->getAwb());
        $tracking_data_list = $tracking_data_filter->getList();

        foreach ($tracking_data_list as $track_data) {
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

        if (count($userList) > 0) {
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
        if ($returnconsignmentarray[0] == "SUCCESS") {

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

            $fileNameNew = "../_assets/pdf/" . date("Y_m_d") . "/" . $consignment->getId() . ".pdf";
// echo $fileNameNew;			 
// $fp			=	fopen($fileNameNew, 'wb+');
// fwrite( $fp, $pdfpage );
// fclose( $fp );
// exit;
//	 $pdf2->addPDF($fileNameNew, 'all');
// echo $labelFile->getId(); exit;
//echo $fileNameNew;
            $consignment->setPrintedFileId($labelFile->getId());

            if ($consignment->getDateReceived() == '')
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
            try {
//$pdf2->merge('file', $fileNameNew);
                echo $returnconsignmentarray[1];
                echo '<script language="javascript">';
                echo "window.open('" . $returnconsignmentarray[1] . "','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');";
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
                <?php
//$("#hawb").select();
                /* echo '<script language="javascript">';
                  echo "window.open('". str_replace("http", "https", $fileNameNew)."','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');" ;
                  echo '</script>'; */
            } catch (Exception $e) {
                $this->str = 'Caught exception: ' . $e->getMessage();
//echo 'Caught exception: ',  $e->getMessage(), "\n";
                $consignment->setStatus(Consignment::STATUS_INVALID);
            }
            return $consignment;
        }
        if ($returnconsignmentarray[0] == "ERROR") {
            $this->str = 'Error: ' . $returnconsignmentarray[1];
            $consignment->setStatus(Consignment::STATUS_INVALID);
//$con_relabel->setMessage($returnconsignmentarray[1]);
            echo "<script language='javascript'> alert('" . $returnconsignmentarray[1] . "'); </script>";
            $consignment->savelog('Single Failed At API Stage: ' . $returnconsignmentarray[1], 'A');
            $consignment->save();
//  echo $returnconsignmentarray[1]; exit;
            return $consignment;
        }
    }

    /*     * *
     * Gets the full path to the folder
     */

    public function getFullPath($fileName)
    {
        $path = "../_assets/pdf/" . date("Y_m_d") . "/";

        if (!file_exists($path))
            @mkdir($path, 0775);

        return $path . $fileName;
    }

    public function renderFooter()
    {
        ?>
        <script type="text/javascript">

            function showLabel(url, title, w, h) {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', 			  height=' + h + ', top=' + top + ', left=' + left);
            }

            $(document).ready(function (e) {
                var request;
                var active = false;
                document.getElementById("tracking").focus();
                $("#tracking").keypress(function (event) {
                    if (event.keyCode == 13) {
                        $("#loader").removeClass("hidden");
                        var hawb_number = $.trim($('#tracking').val());
                        var service_id_new = $.trim($('#service_type').val());
                        var autoprint = $("#chkAutoPrint").is(":checked");
                        if ($.trim($("#tracking").val()).length == 0) {
                            $('#msg').addClass("alert-danger");
                            $('#msg').show().html('Please Enter hawb/tracking Number.');
                            $("#hawb_form_div").addClass("has-error has-danger");
                            $('html, body').animate({
                                scrollTop: $("#response_message").offset().top
                            }, 1000);
                            $("#loader").addClass("hidden");
                            return false;
                        } else {
                            $("#hawb_form_div").removeClass("has-error has-danger");
                            $("#msg").removeClass("alert-danger");
                            $("#msg").html('');
                        }
                        $.ajax({
                            type: "POST",
                            url: "box_ajax.php", // your php file name
                            data: {
                                action: 'GetShipmentData',
                                tracking: hawb_number,
                                autoprint: autoprint,
                                service_id_new: service_id_new,
                                relabel_shipment: "relabel"
                            },
                            success: function (data) {
                                $("#loader").addClass("hidden");
                                var obj = JSON.parse(data);
                                if (obj.result == 'error') {
                                    show_res_msg("error", obj.message);
                                    $("#hawb_form_div").addClass("has-error has-danger");
                                } else {
                                    $('#msg').text('');
                                    $('#msg').removeClass('alert-danger');
                                    $('#tracking').css('background-color', 'springgreen');
                                    $('#old_data').val(data);
                                    $('#company').val(obj.company);
                                    $('#contact').val(obj.contact);
                                    $('#address_line_1').val(obj.addressline1);
                                    $('#address_line_2').val(obj.addressline2);
                                    $('#address_line_3').val(obj.addressline3);
                                    $('#city').val(obj.city);
                                    $('#id').val(obj.consignment_id);
                                    $('#country_id_new').val(obj.country);
                                    $('#country_id_new').selectpicker('refresh');
                                    $('#postcode').val(obj.postcode);
                                    $('#telephone').val(obj.telephone);
                                    $('#weight').val(obj.weight);
                                    $('#length').val(obj.length);
                                    $('#width').val(obj.width);
                                    $('#parcel_id').val(obj.parcel_id);
                                    $('#height').val(obj.height);
                                    $('#oldService').val(obj.service);
                                    $('#oldService').attr("data-service_id",obj.service_id);
                                    $('#number_pieces').val(obj.number_pieces);
                                    $('#currency').val(obj.currency);
                                    $('#value').val(obj.value);
                                    $('#description').val(obj.description);
                                    if (autoprint) {
                                        $("#hawb").focus();
                                        $("#form_action").val("open_print_dialog");
                                        $("#filename").val(obj.label);
                                        $("#adminForm").submit();
                                        showLabel(obj.label, 'Label', 500, 500);
                                    }
                                    setTimeout(function () {
                                        $('#hawb').css('background-color', '#EDEDE7');
                                    }, 3000);
                                }
                            }
                        });
                        return false;
                    }
                });

                $("#btnSave").click(function () {
                    $("#form_action").val("save");
                    var validation_check = 1;
                    var tracking = $('#tracking').val();
                    if (tracking.length == 0) {
                        validation_check = 0;
                        $('#tracking').css('border-color', 'red');
                    } else {
                        $('#tracking').css('border-color', '#c2cad8');
                    }
                    var company = $('#company').val();
                    if (company.length == 0) {
                        validation_check = 0;
                        $('#company').css('border-color', 'red');
                    } else {
                        $('#company').css('border-color', '#c2cad8');
                    }
                    var city = $('#city').val();
                    if (city.length == 0) {
                        validation_check = 0;
                        $('#city').css('border-color', 'red');
                    } else {
                        $('#city').css('border-color', '#c2cad8');
                    }
                    var contact = $('#contact').val();
                    if (contact.length == 0) {
                        validation_check = 0;
                        $('#contact').css('border-color', 'red');
                    } else {
                        $('#contact').css('border-color', '#c2cad8');
                    }
                    var address_line_1 = $('#address_line_1').val();
                    if (address_line_1.length == 0) {
                        validation_check = 0;
                        $('#address_line_1').css('border-color', 'red');
                    } else {
                        $('#address_line_1').css('border-color', '#c2cad8');
                    }
                    var weight = $('#weight').val();
                    if (weight.length == 0) {
                        validation_check = 0;
                        $('#weight').css('border-color', 'red');
                    } else {
                        $('#weight').css('border-color', '#c2cad8');
                    }
                    if (!$.isNumeric(weight)) {
                        validation_check = 0;
                        $('#weight').css('border-color', 'red');
                    } else {
                        $('#weight').css('border-color', '#c2cad8');
                    }
                    if (validation_check == 1) {
                        $.ajax({
                            method: "POST",
                            url: "relabel_shipment.php",
                            data: $('#relabelShipmentForm').serialize()
                        }).done(function (data) {
                            show_res_msg("success", data);
                            $.ajax({
                                type: "POST",
                                url: "box_ajax.php", // your php file name
                                data: {
                                    action: 'GetShipmentData',
                                    tracking: tracking,
                                    autoprint: true,
                                    service_id_new: $("#oldService").data("service_id"),
                                    relabel_shipment: "relabel"
                                },
                                success: function (data) {
                                    $("#loader").addClass("hidden");
                                    var obj = JSON.parse(data);
                                    if (obj.result == 'error') {
                                        show_res_msg("error", obj.message);
                                        $("#hawb_form_div").addClass("has-error has-danger");
                                    } else {
                                        $('#msg').text('');
                                        $('#msg').removeClass('alert-danger');
                                        $('#tracking').css('background-color', 'springgreen');
                                        $('#old_data').val(data);
                                        $('#company').val(obj.company);
                                        $('#contact').val(obj.contact);
                                        $('#address_line_1').val(obj.addressline1);
                                        $('#address_line_2').val(obj.addressline2);
                                        $('#address_line_3').val(obj.addressline3);
                                        $('#city').val(obj.city);
                                        $('#id').val(obj.consignment_id);
                                        $('#country_id_new').val(obj.country);
                                        $('#country_id_new').selectpicker('refresh');
                                        $('#postcode').val(obj.postcode);
                                        $('#telephone').val(obj.telephone);
                                        $('#weight').val(obj.weight);
                                        $('#length').val(obj.length);
                                        $('#width').val(obj.width);
                                        $('#parcel_id').val(obj.parcel_id);
                                        $('#height').val(obj.height);
                                        $('#oldService').val(obj.service);
                                        $('#number_pieces').val(obj.number_pieces);
                                        $('#currency').val(obj.currency);
                                        $('#value').val(obj.value);
                                        $('#description').val(obj.description);
                                        if (autoprint) {
                                            $("#hawb").focus();
                                            $("#form_action").val("open_print_dialog");
                                            $("#filename").val(obj.label);
                                            $("#adminForm").submit();
                                            showLabel(obj.label, 'Label', 500, 500);
                                        }
                                        setTimeout(function () {
                                            $('#hawb').css('background-color', '#EDEDE7');
                                        }, 3000);
                                    }
                                }
                            });
                        });
                    } else {
                        swal("Sorry!", "please fill the required fields", "error");
                    }
                    //                    $('#relabelShipmentForm').validator().on('submit', function (e) {
                    //                        if (e.isDefaultPrevented())
                    //                        {
                    //                            return false;
                    //                        } else {
                    //                            $("#form_action").val("save");
                    //                            if (active) {
                    //                                request.abort();
                    //                            }
                    //                            active = true;
                    //                            request = $.ajax({
                    //                                method: "POST",
                    //                                url: "relabel_shipment.php",
                    //                                data: $('#relabelShipmentForm').serialize()
                    //                            }).done(function (data) {
                    //                                active = false;
                    //                            });
                    //                        }
                    //                    });
                    //                    $("#relabelShipmentForm").submit();
                });
                $("#btnCancel").click(function () {
                    window.location.replace("relabel_shipment.php");
                });
            });

            function show_res_msg(type, msg) {
                $("#show_general_msg div.alert").html(" ");
                if (type == "success") {
                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                } else {
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                }
                $("#show_general_msg div.alert").html(msg);
                $("#show_general_msg").show();
                setTimeout(function () {
                    $("#show_general_msg").hide();
                }, 3000);
            }

            function numbersonly(e) {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8) {
                    if (unicode == 46) {
                    } else if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
                }
            }
        </script>
        <?php
    }

    /**
     * Return to source page
     * @param none
     */
    private function returnToSource()
    {
        $from_str = "?from=consignment_edit";
        switch ($this->source) {
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
//    default:
//    util_redirect("../main/client_list.php");
//    break;
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

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
