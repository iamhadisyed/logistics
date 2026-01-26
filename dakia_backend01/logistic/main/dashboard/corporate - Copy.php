
<!-- END PAGE BREADCRUMB -->
<!-- BEGIN PAGE BASE CONTENT -->
<?php
$Sessionuser = SessionManager::getUser();
if ($Sessionuser->getThemeId() != 1) {
    include("auto_dashboard_panel.php");
}
?>
<div class="row">
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase font-dark">Shipment Statistics</span>
                    <!--<span class="caption-helper">distance stats...</span>-->
                </div>
                <div class="actions">                    
                    <!--<a class="btn btn-circle btn-icon-only btn-default" href="#">
                        <i class="icon-wrench"></i>
                    </a>-->                    
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="dashboard_shipment_stats_chart" class="CSSAnimationChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption ">
                    <span class="caption-subject font-dark bold uppercase">Top Countries</span>
                    <span class="caption-helper">Top countries shipments...</span>
                </div>
                <div class="actions">
                    <!--<a class="btn btn-circle btn-icon-only btn-default" href="#">
                        <i class="icon-wrench"></i>
                    </a>-->                    
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="dashboard_top_countries_chart" class="CSSAnimationChart"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
<?php
    $Sessionuser = SessionManager::getUser();
    if (isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
        $displayOption = @$_SESSION['menu-option'];
    else
        $displayOption = $Sessionuser->getUserType();
    Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
    $sessionUser = SessionManager::getUser();
    $data_return = "";
    $department_list = "";
    if ($sessionUser->getUserType() == "admin") {
        $UserDepartmentFilter = new UserDepartmentFilter();
        $UserDepartmentFilter->addByUserId($sessionUser->getId());
        $department_list = $UserDepartmentFilter->getList();
        $department_str = "";
        if (is_array($department_list) && !empty($department_list)) {
            foreach ($department_list as $value) {
                $department_str .= $value->getDepartmentId() . ",";
            }
            $department_str = rtrim($department_str, ',');
            $HelpDeskTicketFilter = new HelpDeskTicketFilter();
            $data_return = $HelpDeskTicketFilter->getUnReadMessageForAdmin($department_str);
        }
    } elseif ($sessionUser->getUserType() != "admin") {
        $HelpDeskTicketFilter = new HelpDeskTicketFilter();
        $data_return = $HelpDeskTicketFilter->getUnReadMessageForClient($sessionUser->getId());
    }
    if (is_array($data_return) && !empty($data_return)) {
        $data_return = array_slice($data_return, 0, 5);
    }
    if (!empty($data_return)) {
?>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-bubbles font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase"><?php echo Translation::GetCaption("TICKET"); ?></span>
                     <span class="caption-helper"><?php echo Translation::GetCaption("NEED_RESPONSE"); ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="portlet_comments_1">
                        <!-- BEGIN: Comments -->
                        <div class="mt-comments">
                            <?php foreach ($data_return as $value) {
                                $userObj = new CustomerAccount($value->getAddedBy());
                                $fullName = $userObj->getFirstName();
                                $profileImage = "../assets/pages/media/profile/".$userObj->getProfileImage();
                                if (!file_exists($profileImage)) {
                                    $profileImage = "../assets/pages/media/profile/avatar.png";
                                }
                            ?>
                                <div class="mt-comment">
                                <div class="mt-comment-img">
                                    <img src="<?php echo $profileImage;?>" /> 
                                </div>
                                <div class="mt-comment-body">
                                    <div class="mt-comment-info">
                                        <span class="mt-comment-author"><?php echo $fullName; ?></span>
                                        <span class="mt-comment-date">
                                        <?php
                                            echo $date = date("j M, g:i A", $value->getAddedDate());
                                        ?>
                                        </span>
                                    </div>
                                    <div class="mt-comment-text"> <?php echo $value->getSubject(); ?> </div>
                                    <div class="mt-comment-details">
                                        <span class="mt-comment-status mt-comment-status-pending"><?php echo ucwords($value->getPriority()); ?></span>
                                        <ul class="mt-comment-actions">
                                            <li>
                                                <a href="ticket_details.php?id=<?php echo md5($value->getId()); ?>" >View</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- END: Comments -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class=" icon-social-twitter font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase"><?php echo Translation::GetCaption("ESTIMATED_SHIPPING_CALCULATOR")?></span>
                </div>
            </div>
            <div class="portlet-body">
                
                
                
                
                
                
                
                <div class="calc-border">
                    <div class="col-sm-12">
                        <div class="cal-header">
                            <div class="cal-screen" style="">
                                <div class="col-md-2 col-sm-2 col-xs-2 text-center" id="screen_currency"></div>
                                <div class="col-md-2 col-sm-2 col-xs-2 text-center" id="screen_scale"></div>
                                <div class="col-md-4 col-sm-4 col-xs-4 text-center" id="screen_dim"></div>
                                <div class="col-md-4 col-sm-4 col-xs-4 text-center" id="screen_from_to"></div>
                                <div class="col-md-12">
                                    <span class="cal-result" id="screen_message" ></span>
                                    <span class="cal-result" id="screen_price" ><h2>-</h2></span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="cal-body">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <label class="control-label" for="textinput"><?php echo Translation::GetCaption("SHOPPING_FROM"); ?></label>
                                <select id="calc_shipping_from" name="calc_shipping_from" class="form-control calculate_price">
                                    <option value="GB" >United Kingdom</option>
                                    <option value="US" >United States</option>
                                    <option value="NL" >Netherlands</option>
                                    <option value="CN" >China</option>
                                    <option value="DE" selected>Germany</option>
                                </select>
                            </div>                                                                
                            <div class="col-sm-6">
                                <label class="control-label" for="textinput"><?php echo Translation::GetCaption("SHOPPING_TO"); ?></label>
                                <select data-live-search="true" data-live-search-style="startsWith" data-toggle="dropdown" name="calc_shipping_to"  id="calc_shipping_to"  rel="tooltip"   class="selectpicker form-control">
                                    <option value=""><?php echo Translation::GetCaption("SHOPPING_TO"); ?></option>
                                    <option  value='AF'>Afghanistan</option><option  value='AS'>American Samoa</option><option  value='AD'>Andorra</option><option  value='AO'>Angola</option><option  value='AI'>Anguilla</option><option  value='AQ'>Antarctica</option><option  value='AG'>Antigua and Barbuda</option><option  value='AR'>Argentina</option><option  value='AM'>Armenia</option><option  value='AW'>Aruba</option><option  value='AU'>Australia</option><option  value='AT'>Austria</option><option  value='AZ'>Azerbaijan</option><option  value='BS'>Bahamas</option><option  value='BH'>Bahrain</option><option  value='BD'>Bangladesh</option><option  value='BB'>Barbados</option><option  value='BY'>Belarus</option><option  value='BE'>Belgium</option><option  value='BZ'>Belize</option><option  value='BJ'>Benin</option><option  value='BM'>Bermuda</option><option  value='BT'>Bhutan</option><option  value='BO'>Bolivia</option><option  value='BA'>Bosnia and Herzegovina</option><option  value='BW'>Botswana</option><option  value='BV'>Bouvet Island</option><option  value='BR'>Brazil</option><option  value='IO'>British Indian Ocean Territory</option><option  value='BN'>Brunei Darussalam</option><option  value='BG'>Bulgaria</option><option  value='BF'>Burkina Faso</option><option  value='BI'>Burundi</option><option  value='KH'>Cambodia</option><option  value='CM'>Cameroon</option><option  value='CA'>Canada</option><option  value='CV'>Cape Verde</option><option  value='KY'>Cayman Islands</option><option  value='TD'>Chad</option><option  value='QQ'>Channel Islands</option><option  value='CL'>Chile</option><option  value='CN'>China</option><option  value='CX'>Christmas Island</option><option  value='CC'>Cocos (Keeling) Islands</option><option  value='CO'>Colombia</option><option  value='KM'>Comoros</option><option  value='CG'>Congo</option><option  value='CD'>Democratic Republic of the Congo</option><option  value='CK'>Cook Islands</option><option  value='CR'>Costa Rica</option><option  value='CI'>Cote D Ivoire</option><option  value='HR'>Croatia</option><option  value='CU'>Cuba</option><option  value='CY'>Cyprus</option><option  value='CZ'>Czech Republic</option><option  value='DK'>Denmark</option><option  value='DJ'>Djibouti</option><option  value='DM'>Dominica</option><option  value='DO'>Dominican Republic</option><option  value='EC'>Ecuador</option><option  value='EG'>Egypt</option><option  value='SV'>El Salvador</option><option  value='GQ'>Equatorial Guinea</option><option  value='ER'>Eritrea</option><option  value='EE'>Estonia</option><option  value='ET'>Ethiopia</option><option  value='FK'>Falkland Islands (Malvinas)</option><option  value='FO'>Faroe Islands</option><option  value='FJ'>Fiji</option><option  value='FI'>Finland</option><option  value='FR'>France</option><option  value='GF'>French Guiana</option><option  value='PF'>French Polynesia</option><option  value='TF'>French Southern Territories</option><option  value='GA'>Gabon</option><option  value='GM'>Gambia</option><option  value='GE'>Georgia</option><option  value='DE'>Germany</option><option  value='GH'>Ghana</option><option  value='GI'>Gibraltar</option><option  value='GR'>Greece</option><option  value='GL'>Greenland</option><option  value='GD'>Grenada</option><option  value='GP'>Guadeloupe</option><option  value='GU'>Guam</option><option  value='GT'>Guatemala</option><option  value='GN'>Guinea</option><option  value='GW'>Guinea-Bissau</option><option  value='GY'>Guyana</option><option  value='HT'>Haiti</option><option  value='HM'>Heard Island and Mcdonald Islands</option><option  value='VA'>Holy See (Vatican City State)</option><option  value='HN'>Honduras</option><option  value='HK'>Hong Kong</option><option  value='HU'>Hungary</option><option  value='IS'>Iceland</option><option  value='IN'>India</option><option  value='ID'>Indonesia</option><option  value='IR'>Iran</option><option  value='IQ'>Iraq</option><option  value='IE'>Ireland</option><option  value='IL'>Israel</option><option  value='IT'>Italy</option><option  value='JM'>Jamaica</option><option  value='JP'>Japan</option><option  value='JO'>Jordan</option><option  value='KZ'>Kazakhstan</option><option  value='KE'>Kenya</option><option  value='KI'>Kiribati</option><option  value='KP'>North Korea</option><option  value='KR'>South Korea</option><option  value='KW'>Kuwait</option><option  value='KG'>Kyrgyzstan</option><option  value='LA'>Lao People Democratic Republic</option><option  value='LV'>Latvia</option><option  value='LB'>Lebanon</option><option  value='LS'>Lesotho</option><option  value='LR'>Liberia</option><option  value='LY'>Libyan Arab Jamahiriya</option><option  value='LI'>Liechtenstein</option><option  value='LT'>Lithuania</option><option  value='LU'>Luxembourg</option><option  value='MO'>Macau</option><option  value='MK'>Macedonia</option><option  value='MG'>Madagascar</option><option  value='MW'>Malawi</option><option  value='MY'>Malaysia</option><option  value='MV'>Maldives</option><option  value='ML'>Mali</option><option  value='MT'>Malta</option><option  value='MH'>Marshall Islands</option><option  value='MQ'>Martinique</option><option  value='MR'>Mauritania</option><option  value='MU'>Mauritius</option><option  value='YT'>Mayotte</option><option  value='MX'>Mexico</option><option  value='FM'>Micronesia, Federated States of</option><option  value='MD'>Moldova, Republic of</option><option  value='MC'>Monaco</option><option  value='MN'>Mongolia</option><option  value='MS'>Montserrat</option><option  value='MA'>Morocco</option><option  value='MZ'>Mozambique</option><option  value='MM'>Myanmar</option><option  value='NA'>Namibia</option><option  value='NR'>Nauru</option><option  value='NP'>Nepal</option><option  value='NL'>Netherlands</option><option  value='AN'>Netherlands Antilles</option><option  value='NC'>New Caledonia</option><option  value='NZ'>New Zealand</option><option  value='NI'>Nicaragua</option><option  value='NE'>Niger</option><option  value='NG'>Nigeria</option><option  value='NU'>Niue</option><option  value='NF'>Norfolk Island</option><option  value='MP'>Northern Mariana Islands</option><option  value='NO'>Norway</option><option  value='OM'>Oman</option><option  value='PK'>Pakistan</option><option  value='PW'>Palau</option><option  value='PS'>Palestinian Territory, Occupied</option><option  value='PA'>Panama</option><option  value='PG'>Papua New Guinea</option><option  value='PY'>Paraguay</option><option  value='PE'>Peru</option><option  value='PH'>Philippines</option><option  value='PN'>Pitcairn</option><option  value='PL'>Poland</option><option  value='PT'>Portugal</option><option  value='PR'>Puerto Rico</option><option  value='QA'>Qatar</option><option  value='RE'>Reunion</option><option  value='RO'>Romania</option><option  value='RU'>Russian Federation</option><option  value='RW'>Rwanda</option><option  value='SH'>Saint Helena</option><option  value='KN'>Saint Kitts and Nevis</option><option  value='LC'>Saint Lucia</option><option  value='PM'>Saint Pierre and Miquelon</option><option  value='VC'>Saint Vincent and the Grenadines</option><option  value='WS'>Samoa</option><option  value='SM'>San Marino</option><option  value='ST'>Sao Tome and Principe</option><option  value='SA'>Saudi Arabia</option><option  value='SN'>Senegal</option><option  value='RS'>Serbia</option><option  value='SC'>Seychelles</option><option  value='SL'>Sierra Leone</option><option  value='SG'>Singapore</option><option  value='SK'>Slovakia</option><option  value='SI'>Slovenia</option><option  value='SB'>Solomon Islands</option><option  value='SO'>Somalia</option><option  value='ZA'>South Africa</option><option  value='GS'>South Georgia and the South Sandwich Islands</option><option  value='ES'>Spain</option><option  value='LK'>Sri Lanka</option><option  value='SD'>Sudan</option><option  value='SR'>Suriname</option><option  value='SJ'>Svalbard and Jan Mayen</option><option  value='SZ'>Swaziland</option><option  value='SE'>Sweden</option><option  value='CH'>Switzerland</option><option  value='SY'>Syrian Arab Republic</option><option  value='TW'>Taiwan</option><option  value='TJ'>Tajikistan</option><option  value='TZ'>Tanzania</option><option  value='TH'>Thailand</option><option  value='TL'>Timor-Leste</option><option  value='TG'>Togo</option><option  value='TK'>Tokelau</option><option  value='TO'>Tonga</option><option  value='TT'>Trinidad and Tobago</option><option  value='TN'>Tunisia</option><option  value='TR'>Turkey</option><option  value='TM'>Turkmenistan</option><option  value='TC'>Turks and Caicos Islands</option><option  value='TV'>Tuvalu</option><option  value='UG'>Uganda</option><option  value='UA'>Ukraine</option><option  value='AE'>United Arab Emirates</option><option  value='GB'>United Kingdom</option><option  value='US'>United States</option><option  value='UM'>United States Minor Outlying Islands</option><option  value='UY'>Uruguay</option><option  value='UZ'>Uzbekistan</option><option  value='VU'>Vanuatu</option><option  value='VE'>Venezuela</option><option  value='VG'>Virgin Islands, British</option><option  value='VI'>Virgin Islands (US)</option><option  value='WF'>Wallis and Futuna</option><option  value='EH'>Western Sahara</option><option  value='YE'>Yemen</option><option  value='ZM'>Zambia</option><option  value='ZW'>Zimbabwe</option><option  value='AL'>Albania</option><option  value='DZ'>Algeria</option><option  value='MF'>Saint Martin</option><option  value='CF'>Central African Republic</option><option  value='CW'>Curacao</option><option  value='IM'>Isle of Man</option><option  value='PYF'>Tahiti</option><option  value='VN'>Vietnam</option><option  value='WKU'>Wake Island</option><option  value='ZR'>Zaire</option><option  value='STA'>Spanish Terr N Africa</option><option  value='CS'>Serbia and Montenegro</option><option  value='XN'>Nevis</option><option  value='XY'>ST. Barthelemy</option><option  value='VAT'>Vatican</option><option  value='XK'>kosovo</option><option  value='IC'>Canary Islands</option><option  value='BQ'>Bonaire, Sint Eustatius and Saba</option><option  value='SS'>South Sudan</option><option  value='JE'>Jersey</option><option  value='BL'>Saint Barthelemy</option><option  value='ME'>Montenegro</option><option  value='GG'>Guernsey</option><option  value='RU'>RUSSIA</option><option  value='ND'>Northern Ireland</option><option  value='TLS'>East Timor</option><option  value='GUF'>French Guyana</option><option  value='GIN'>Guinea</option><option  value='XS'>Somaliland</option><option  value='XE'>St.Eustatius</option><option  value='SH'>St.Helena</option><option  value='LC'>St.Lucia</option><option  value='XM'>St.Maarten</option>                                                         </select>

                            </div>                                            
                        </div>
                        <div class="form-group" id="calc_total_weight_container">
                            <div class="col-sm-6">
                                <label class="control-label" for="calc_weight"><?php echo Translation::GetCaption("WEIGHT"); ?></label>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label font-green-soft" for="calc_chargeable_weight">Chargeable Weight</label>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="calc_weight" name="calc_weight" placeholder="" class="form-control calculate_price_input" type="number" onChange="calculateChargeableWeight();" />
                                    <span class="input-group-addon"> kg </span>
                                    <div id="calc_total_weight_error_container" class="help-block with-errors" style="display: none;"><?php echo Translation::GetCaption("PLEASE_ENTER_VALID_WEIGHT"); ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="calc_chargeable_weight" name="calc_chargeable_weight" placeholder="" class="form-control chargeable_weight" type="text" readonly="" >
                                    <span class="input-group-addon"> kg </span>
                                </div>
                            </div>
                            <!-- <div class="col-sm-4">
                                 <div id="radioBtn2" class="btn-group">
                                     <a class="btn btn-warning  btn-md active" data-toggle="calc_scale" data-title="kg"><?php echo Translation::GetCaption("KGS"); ?></a>
                                     <a class="btn btn-info  btn-md notActive" data-toggle="calc_scale" data-title="lb"><?php echo Translation::GetCaption("LBS"); ?></a>
                                 </div>
                                 <input name="calc_scale" class="calculate_price" value="kg" id="calc_scale" type="hidden">
                             </div>
                             <div class="col-sm-2"><i class="fa fa-refresh fa-lg fa-spin" id="calc_loading" style="display: none;"></i></div> -->                                           
                        </div>
                        <div class="form-group">
                            <!-- <div class="col-sm-6">
                                 <label class="control-label" for="textinput"></label>
                                 <select name="calc_currency" id="calc_currency" class="form-control calculate_price">
                                                                                                           
                                 </select>                                                    </div>-->

                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="control-label" for="calc_length"><? echo Translation::GetCaption("LENGTH"); ?></label>
                            </div>
                            <div class="col-sm-4">
                                <label class="control-label" for="calc_width"><? echo Translation::GetCaption("WIDTH"); ?></label>
                            </div>
                            <div class="col-sm-4">
                                <label class="control-label" for="calc_height"><? echo Translation::GetCaption("HEIGHT"); ?></label>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-expand"></i> </span>
                                    <input id="calc_length" name="calc_length" placeholder="0.0" class="form-control calculate_price_input" type="number" onChange="calculateChargeableWeight();">
                                    <span class="input-group-addon"> <? echo Translation::GetCaption("CM"); ?> </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-arrows-h"></i> </span>
                                    <input id="calc_width" name="calc_width" placeholder="0.0" class="form-control calculate_price_input" type="number" onChange="calculateChargeableWeight();">
                                    <span class="input-group-addon"> <? echo Translation::GetCaption("CM"); ?> </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-arrows-v"></i> </span>
                                    <input id="calc_height" name="calc_height" placeholder="0.0" class="form-control calculate_price_input" type="number" onChange="calculateChargeableWeight();">
                                    <span class="input-group-addon"> <? echo Translation::GetCaption("CM"); ?> </span>
                                </div>
                            </div>
                            <!-- <div class="col-sm-3">
                                 <label class="control-label" for="calc_dim_unit"></label>
                                 <select name="calc_dim_unit" id="calc_dim_unit" class="form-control calculate_price">
                                     <option value="cm"></option>
                                     <option value="in"></option>
                                 </select>
                             </div>-->
                        </div>   

                        <br clear="all"/> 
                        <div class="cal-dimesnion">
                            <span class="dhead"> Dimensions</span>
                            <span class="dheight">
                                <span>Height (<span class="calc_dim_label_unit"><? echo Translation::GetCaption("CM"); ?></span>) </span>
                                <br>
                                <input id="_calc_height" name="_calc_height" placeholder="0.0" class="form-control" type="text" readonly="">
                            </span> 
                            <span class="dlenght">
                                <input id="_calc_width" name="_calc_width" placeholder="0.0" class="form-control" type="text" readonly="">
                                Width (<span class="calc_dim_label_unit"><? echo Translation::GetCaption("CM"); ?></span>)
                            </span>
                            <span class="dwidht">Length (<span class="calc_dim_label_unit"><? echo Translation::GetCaption("CM"); ?></span>) </span>
                            <input id="_calc_length" name="_calc_length" placeholder="0.0" class="form-control dwidhtinput" type="text" readonly="">
                        </div>
                        <br clear="all"/> 

                        <br clear="all"/> 

                        <br />
                        <br />
                        <div class="row">
                            <div class="col-sm-12" align="center">
                                <button id="calculatePrice" name="calculatePrice"  class="btn btn-primary">Calculate</button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>                                            
                </div>
            </div>
        </div>
    </div>
</div>
<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo Translation::GetCaption("MODIFY"); ?> | <?php echo Translation::GetCaption("CHANGE_MY_EMAIL"); ?> | <?php echo Translation::GetCaption("CHANGE_MY_PASSWORD"); ?></h4>
            </div>
            <div class="modal-body">
                <div class="tab-pane" id="tab_1">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gift"></i><?php echo Translation::GetCaption("FORM_SAMPLE"); ?>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"></a>
                                <!--<a href="#portlet-config" data-toggle="modal" class="config">
                                </a>
                                -->
                                <a href="javascript:;" class="reload"></a>
                                <a href="javascript:;" class="remove">
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="#" class="horizontal-form">
                                <div class="form-body">
                                    <h3 class="form-section"><?php echo Translation::GetCaption("PERSON_INFO"); ?></h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo Translation::GetCaption("FIRST_NAME"); ?></label>
                                                <input type="text" id="firstName" class="form-control" placeholder="Chee Kin">
                                                <span class="help-block">
                                                    This is inline help </span>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group has-error">
                                                <label class="control-label">Last Name</label>
                                                <input type="text" id="lastName" class="form-control" placeholder="Lim">
                                                <span class="help-block">
                                                    This field has error. </span>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Gender</label>
                                                <select class="form-control">
                                                    <option value="">Male</option>
                                                    <option value="">Female</option>
                                                </select>
                                                <span class="help-block">
                                                    Select your gender </span>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Date of Birth</label>
                                                <input type="text" class="form-control" placeholder="dd/mm/yyyy">
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Category</label>
                                                <select class="select2_category form-control" data-placeholder="Choose a Category" tabindex="1">
                                                    <option value="Category 1">Category 1</option>
                                                    <option value="Category 2">Category 2</option>
                                                    <option value="Category 3">Category 5</option>
                                                    <option value="Category 4">Category 4</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Membership</label>
                                                <div class="radio-list">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked> Option 1 </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2"> Option 2 </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <h3 class="form-section">Address</h3>
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>Street</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>City</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>State</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Post Code</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <select class="form-control">
                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                </div>
                                <div class="form-actions right">
                                    <button type="button" class="btn default">Cancel</button>
                                    <button type="submit" class="btn blue"><i class="fa fa-check"></i> Save</button>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn blue">Save changes</button>
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- END STYLE CUSTOMIZER -->
<!-- BEGIN PAGE HEADER-->    
<!-- END DASHBOARD STATS -->