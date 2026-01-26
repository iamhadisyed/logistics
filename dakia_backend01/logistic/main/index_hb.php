<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
include_once("../includes/autoload/convertxml2array.class.php");

// set up local page class
class Page extends BasePage {
    /*     * *
     * Set the page header
     * @return void
     */

    private $total_consignment_chart = array();
    private $daily_consignment_chart = array();
    private $top_countries_chart = array();
    private $top_countries_bar = array();
    private $top_services_chart = array();
    private $top_services_bar = array();
    private $total_hold_parcels = 0;
    private $total_consignment = 0;
    private $total_shipped_parcels = 0;
    private $total_delivered_parcels = 0;
    private $total_weight = 0;
    private $max_weight = 0;

    public function getTitle() {
        return "Admin - Index";
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
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
                                        <a href="javascript:;" class="collapse">
                                        </a>
                                        <!--<a href="#portlet-config" data-toggle="modal" class="config">
                                        </a>
                                        -->                                        <a href="javascript:;" class="reload">
                                        </a>
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
        <?    
        $Sessionuser = SessionManager::getUser();
        if($Sessionuser->getThemeId() != 1)
        {
        include("auto_dashboard_panel.php");
        }
        ?>


        <form name="frm_dashboard" action="client_list.php" id="frm_dashboard" method="POST">
            <input type="hidden" name="form_action" value=""  id="form_action"/>
        </form>
        <!-- END DASHBOARD STATS -->
        <div class="clearfix">
        </div>
        <?  
        $Sessionuser = SessionManager::getUser();
        if(isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
        $displayOption 	=	@$_SESSION['menu-option'];	
        else
        $displayOption 	=		$Sessionuser->getUserType() ;	

        switch($displayOption)
        {
        case User::USER_TYPE_WAREHOUSE :

        ?>
        <div class="row">
            <div class="col-md-12">

                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="visible-lg visible-md hidden-xs hidden-sm">
                            <div class="work-flow-1">
                                <div class="page-heading-box">
                                    <h3>OPERATION DASHBOARD</h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>

                                <h3 style="position: absolute;    top: 163px;    left: 110px; background:#004E87; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("WORKFLOW"); ?></h3>

                                <h3 style="position:absolute; top: 163px;    right: 120px;  background:#F58025; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;">Quality Control</h3>   

                                <h3 style="    position: absolute;
                                    top: 199px;
                                    left: 385px;
                                    text-align: center;     font-size: 17px; background:#00661A; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("TOOLBOX_SERVICE"); ?>
                                </h3>       

                                <a href="pre-alert-report.php">
                                    <div class="round-button btn-1">1</div>
                                    <h3 class="btn1-create"><?php echo Translation::GetCaption("MAWB_FLIGHT_STATUS"); ?></h3>
                                </a>
                                <a href="bagscan.php">
                                    <div class="round-button btn-2">2</div>
                                    <h3 class="btn2-create"><?php echo Translation::GetCaption("SCAN_CARTON_SINGLE_ITEM"); ?></h3>
                                </a>  
                                <a href="relabel_shipment.php">
                                    <div class="round-button btn-3">3</div>
                                    <h3 class="btn3-create"><?php echo Translation::GetCaption("RELABEL_SHIPMENT"); ?></h3>
                                </a>
                                <a href="endofday_summary.php">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo Translation::GetCaption("BOOKING"); ?></h3>
                                </a>
                                <a href="generate-manifest_m.php">
                                    <div class="round-button btn-5">5</div>
                                    <h3 class="btn5-create"><?php echo Translation::GetCaption("CREATE_MANIFESTS"); ?></h3>
                                </a>
                                <a href="search.php">
                                    <div class="round-button-orange btn-10">5</div>
                                    <h3 class="btn10-create"><?php echo Translation::GetCaption("SEARCH_DATABASE"); ?></h3>
                                </a>
                                <a href="consigmentreturn.php">
                                    <div class="round-button-orange btn-9">4</div>
                                    <h3 class="btn9-create"><?php echo Translation::GetCaption("RETURN_SHIPMENT"); ?></h3>
                                </a>
                                <a href="ajax-scanning-report.php">
                                    <div class="round-button-orange btn-8">3</div>
                                    <h3 class="btn8-create"><?php echo Translation::GetCaption("MAWB_SCANNING_REPORT"); ?></h3>
                                </a>
                                <a href="endofday_search.php">
                                    <div class="round-button-orange btn-7">2</div>
                                    <h3 class="btn7-create"><?php echo Translation::GetCaption("END_OF_DAY_SUMMARIES"); ?> <br /> <?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>
                                <a href="pallet_report.php">
                                    <div class="round-button-orange btn-6">1</div>
                                    <h3 class="btn6-create"><?php echo Translation::GetCaption("PALLET_DISPATCH_REPORT"); ?></h3>
                                </a>

                                <a href="support_center.php">
                                    <div class="round-button-black btn-11">
                                                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 
                                </a>
                                <a href="#">
                                    <div class="round-button-black btn-12">
                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>
                                </a>

                                <a href="changestatus.php">
                                    <div class="round-button-black btn-13">
                                        <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn13-create"><?php echo Translation::GetCaption("STOP_SHIPMENT"); ?></h3>
                                </a>

                            </div>
                        </div>



                        <div class="hidden-lg hidden-md visible-sm visible-xs">
                            <div class="workflow-mobile">
                                <div class="workflow-1-mobile-heading">
                                    <h3><?php echo Translation::GetCaption("OPERATION_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>
                                <a href="pre-alert-report.php">
                                    <div class="round-button btn-m-1">1</div>
                                    <h3 class="btn-m-1-create"><?php echo Translation::GetCaption("MAWB_FLIGHT_STATUS"); ?></h3>
                                </a>
                                <a href="bagscan.php">
                                    <div class="round-button btn-m-2">2</div>
                                    <h3 class="btn-m-2-create"><?php echo Translation::GetCaption("SCAN_CARTON_SINGLE_ITEM"); ?></h3>
                                </a>
                                <a href="relabel_shipment.php">
                                    <div class="round-button btn-m-3">3</div>
                                    <h3 class="btn-m-3-create"><?php echo Translation::GetCaption("RELABEL_SHIPMENT"); ?></h3>
                                </a>
                                <a href="endofday_summary.php">
                                    <div class="round-button btn-m-4">4</div>
                                    <h3 class="btn-m-4-create"><?php echo Translation::GetCaption("BOOKING"); ?></h3>
                                </a>
                                <a href="generate-manifest_m.php">
                                    <div class="round-button btn-m-5">5</div>
                                    <h3 class="btn-m-5-create"><?php echo Translation::GetCaption("CREATE_MANIFESTS"); ?></h3>
                                </a>
                                <a href="search.php">
                                    <div class="round-button-orange btn-m-10">5</div>
                                    <h3 class="btn-m-10-create"><?php echo Translation::GetCaption("SEARCH_DATABASE"); ?></h3>
                                </a>
                                <a href="consigmentreturn.php">
                                    <div class="round-button-orange btn-m-9">4</div>
                                    <h3 class="btn-m-9-create"><?php echo Translation::GetCaption("RETURN_SHIPMENT"); ?></h3>
                                </a>
                                <a href="ajax-scanning-report.php">
                                    <div class="round-button-orange btn-m-8">3</div>
                                    <h3 class="btn-m-8-create"><?php echo Translation::GetCaption("MAWB_SCANNING_REPORT"); ?></h3>
                                </a>
                                <a href="endofday_search.php">
                                    <div class="round-button-orange btn-m-7">2</div>
                                    <h3 class="btn-m-7-create"><?php echo Translation::GetCaption("END_OF_DAY_SUMMARIES"); ?> <br /><?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>
                                <a href="pallet_report.php">
                                    <div class="round-button-orange btn-m-6">1</div>
                                    <h3 class="btn-m-6-create"><?php echo Translation::GetCaption("PALLET_DISPATCH_REPORT"); ?></h3>
                                </a>
                                <div class="round-button-black btn-m-11">
                                    <a href="support_center.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 

                                <div class="round-button-black btn-m-12">
                                    <a href="#">
                                          <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>



                                <div class="round-button-black btn-m-13">
                                    <a href="#">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-13-create"><?php echo Translation::GetCaption("STOP_SHIPMENT"); ?></h3>
















                            </div>
                        </div>

                    </div> 
                </div>        
            </div>
        </div> 
        <?

        break;
        case User::USER_TYPE_CORPORATE_CLIENT :

        ?>
        <div class="row">
            <div class="col-md-12">

                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="visible-lg visible-md hidden-xs hidden-sm">
                            <div class="work-flow-1">
                                <div class="page-heading-box">
                                    <h3><?php echo Translation::GetCaption("CORPORATE_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>

                                <h3 style="position: absolute;    top: 163px;    left: 110px; background:#004E87; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("WORKFLOW"); ?></h3>

                                <h3 style="position:absolute; top: 163px;    right: 120px;  background:#F58025; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("QUALITY_CONTROL"); ?></h3>   

                                <h3 style="    position: absolute;
                                    top: 199px;
                                    left: 385px;
                                    text-align: center;     font-size: 17px; background:#00661A; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("TOOLBOX_SERVICE"); ?>
                                </h3>       

                                <a href="bagscan.php">
                                    <div class="round-button btn-1">1</div>
                                    <h3 class="btn1-create"><?php echo Translation::GetCaption("INBOUND_SCANNING"); ?></h3>
                                </a>
                                <a href="bagscan.php">
                                    <div class="round-button btn-2">2</div>
                                    <h3 class="btn2-create"><?php echo Translation::GetCaption("END_OF_DAY_MANIFEST"); ?></h3>
                                </a>  
                                <a href="bagscan.php">
                                    <div class="round-button btn-3">3</div>
                                    <h3 class="btn3-create"><?php echo Translation::GetCaption("SEND_PRE_ALERT"); ?></h3>
                                </a>
                                <a href="supplier_returns.php">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo Translation::GetCaption("RETURNS"); ?></h3>
                                </a>
                                <a href="customers.php">
                                    <div class="round-button btn-5">5</div>
                                    <h3 class="btn5-create"><?php echo Translation::GetCaption("MEMBERS"); ?></h3>
                                </a>



                                <a href="track_shipment.php">
                                    <div class="round-button-orange btn-10">5</div>
                                    <h3 class="btn10-create"><?php echo Translation::GetCaption("SCAN_REPORT"); ?></h3>
                                </a>
                                <a href="intransportation_report.php">
                                    <div class="round-button-orange btn-9">4</div>
                                    <h3 class="btn9-create"><?php echo Translation::GetCaption("HUB_REPORT"); ?></h3>
                                </a>
                                <a href="generate-manifest_m.php">
                                    <div class="round-button-orange btn-8">3</div>
                                    <h3 class="btn8-create"><?php echo Translation::GetCaption("OPERATION_MANIFEST"); ?></h3>
                                </a>
                                <a href="corporate_service_list_report.php">
                                    <div class="round-button-orange btn-7">2</div>
                                    <h3 class="btn7-create"><?php echo Translation::GetCaption("EXPORT_PARTNER_SERVICES"); ?></h3>
                                </a>
                                <a href="account_summary.php">
                                    <div class="round-button-orange btn-6">1</div>
                                    <h3 class="btn6-create"><?php echo Translation::GetCaption("ACCOUNT_SUMMARY_REPORTS"); ?></h3>
                                </a>

                                <a href="support_center.php">
                                    <div class="round-button-black btn-11">
                                                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 
                                </a>
                                <a href="../_assets/user_manuals/Corporateclient_updated.pdf">
                                    <div class="round-button-black btn-12">
                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>
                                </a>
                                <a href="relabel_shipment.php">
                                    <div class="round-button-black btn-13">
                                        <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn13-create"><?php echo Translation::GetCaption("RELABEL_SHIPMENT"); ?></h3>
                                </a>
                                <a href="changestatus.php">
                                    <div class="round-button-black btn-14">
                                        <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn14-create"><?php echo Translation::GetCaption("STOP_SHIPMENT"); ?></h3>
                                </a>


                            </div>
                        </div>



                        <div class="hidden-lg hidden-md visible-sm visible-xs">
                            <div class="workflow-mobile">
                                <div class="workflow-1-mobile-heading">
                                    <h3><?php echo Translation::GetCaption("CORPORATE_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>
                                <a href="bagscan.php">
                                    <div class="round-button btn-m-1">1</div>
                                    <h3 class="btn-m-1-create"><?php echo Translation::GetCaption("SCANNING_&_REPORTS"); ?></h3>
                                </a>
                                <a href="bagscan.php">
                                    <div class="round-button btn-m-2">2</div>
                                    <h3 class="btn-m-2-create"><?php echo Translation::GetCaption("END_OF_DAY_SIMPLE"); ?></h3>
                                </a>
                                <a href="manifest_search.php">
                                    <div class="round-button btn-m-3">3</div>
                                    <h3 class="btn-m-3-create"><?php echo Translation::GetCaption("MANIFEST"); ?></h3>
                                </a>
                                <a href="supplier_returns.php">
                                    <div class="round-button btn-m-4">4</div>
                                    <h3 class="btn-m-4-create"><?php echo Translation::GetCaption("RETURN"); ?></h3>
                                </a>
                                <a href="customers.php">
                                    <div class="round-button btn-m-4">5</div>
                                    <h3 class="btn-m-5-create"><?php echo Translation::GetCaption("MEMBERS"); ?></h3>
                                </a>

                                <a href="track_shipment.php">
                                    <div class="round-button-orange btn-m-10">5</div>
                                    <h3 class="btn-m-10-create"><?php echo Translation::GetCaption("SCAN_REPORT"); ?></h3>
                                </a>
                                <a href="intransportation_report.php">
                                    <div class="round-button-orange btn-m-9">4</div>
                                    <h3 class="btn-m-9-create"><?php echo Translation::GetCaption("HUB_REPORT"); ?></h3>
                                </a>
                                <a href="generate-manifest_m.php">
                                    <div class="round-button-orange btn-m-8">3</div>
                                    <h3 class="btn-m-8-create"><?php echo Translation::GetCaption("OPERATION_MANIFEST"); ?></h3>
                                </a>
                                <a href="corporate_service_list_report.php">
                                    <div class="round-button-orange btn-m-7">2</div>
                                    <h3 class="btn-m-7-create"><?php echo Translation::GetCaption("EXPORT_PARTNER_SERVICES"); ?></h3>
                                </a>
                                <a href="account_summary.php">
                                    <div class="round-button-orange btn-m-6">2</div>
                                    <h3 class="btn-m-6-create"><?php echo Translation::GetCaption("ACCOUNT_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <div class="round-button-black btn-m-11">
                                    <a href="support_center.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 

                                <div class="round-button-black btn-m-12">
                                    <a href="../_assets/user_manuals/Corporateclient_updated.pdf">
                                          <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>



                                <div class="round-button-black btn-m-13">
                                    <a href="relabel_shipment.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-13-create"><?php echo Translation::GetCaption("RELABEL_SHIPMENT"); ?></h3>

                                <div class="round-button-black btn-m-14">
                                    <a href="changestatus.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-14-create"><?php echo Translation::GetCaption("STOP_SHIPMENT"); ?></h3>
















                            </div>
                        </div>

                    </div> 
                </div>        
            </div>
        </div>
        <?

        break;
        case User::USER_TYPE_FINANCE :

        ?>
        <div class="row">
            <div class="col-md-12">

                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="visible-lg visible-md hidden-xs hidden-sm">
                            <div class="work-flow-1">
                                <div class="page-heading-box">
                                    <h3><?php echo Translation::GetCaption("FINANCE_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>

                                <h3 style="position: absolute;    top: 163px;    left: 110px; background:#004E87; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("WORKFLOW"); ?></h3>

                                <h3 style="position:absolute; top: 163px;    right: 120px;  background:#F58025; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("QUALITY_CONTROL"); ?></h3>   

                                <h3 style="    position: absolute;
                                    top: 199px;
                                    left: 385px;
                                    text-align: center;     font-size: 17px; background:#00661A; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("TOOLBOX_SERVICE"); ?>
                                </h3>       

                                <a href="customers.php">
                                    <div class="round-button btn-1">1</div>
                                    <h3 class="btn1-create"><?php echo Translation::GetCaption("MEMBERS"); ?></h3>
                                </a>
                                <a href="tariff_list.php">
                                    <div class="round-button btn-2">2</div>
                                    <h3 class="btn2-create"><?php echo Translation::GetCaption("TARIFF_LIST"); ?></h3>
                                </a>  
                                <a href="couriers.php">
                                    <div class="round-button btn-3">3</div>
                                    <h3 class="btn3-create"><?php echo Translation::GetCaption("CARRIERS"); ?></h3>
                                </a>
                                <a href="bookings.php">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo Translation::GetCaption("FIND_SHIPMENTS"); ?></h3>
                                </a>
                                <a href="mawb-report.php">
                                    <div class="round-button btn-5">5</div>
                                    <h3 class="btn5-create"><?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>
                                <a href="invoices.php">
                                    <div class="round-button-orange btn-10">5</div>
                                    <h3 class="btn10-create"><?php echo Translation::GetCaption("INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="manual_invoices.php">
                                    <div class="round-button-orange btn-9">4</div>
                                    <h3 class="btn9-create"><?php echo Translation::GetCaption("MANUAL_INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="credit_note.php">
                                    <div class="round-button-orange btn-8">3</div>
                                    <h3 class="btn8-create"><?php echo Translation::GetCaption("CREDIT_NOTE_LIST"); ?></h3>
                                </a>
                                <a href="day-summary-report.php">
                                    <div class="round-button-orange btn-7">2</div>
                                    <h3 class="btn7-create"><?php echo Translation::GetCaption("DAY_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <a href="account_summary.php">
                                    <div class="round-button-orange btn-6">1</div>
                                    <h3 class="btn6-create"><?php echo Translation::GetCaption("Account Summary Report"); ?></h3>
                                </a>

                                <a href="support_center.php">
                                    <div class="round-button-black btn-11">
                                                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 
                                </a>
                                <a href="../_assets/user_manuals/accounts_userguide.pdf">
                                    <div class="round-button-black btn-12">
                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>
                                </a>



                            </div>
                        </div>



                        <div class="hidden-lg hidden-md visible-sm visible-xs">
                            <div class="workflow-mobile">
                                <div class="workflow-1-mobile-heading">
                                    <h3><?php echo Translation::GetCaption("FINANCE_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>
                                <a href="customers.php">
                                    <div class="round-button btn-m-1">1</div>
                                    <h3 class="btn-m-1-create"><?php echo Translation::GetCaption("MEMBERS"); ?></h3>
                                </a>
                                <a href="tariff_list.php">
                                    <div class="round-button btn-m-2">2</div>
                                    <h3 class="btn-m-2-create"><?php echo Translation::GetCaption("TARIFF_LIST"); ?></h3>
                                </a>
                                <a href="couriers.php">
                                    <div class="round-button btn-m-3">3</div>
                                    <h3 class="btn-m-3-create"><?php echo Translation::GetCaption("CARRIERS"); ?></h3>
                                </a>
                                <a href="bookings.php">
                                    <div class="round-button btn-m-4">4</div>
                                    <h3 class="btn-m-4-create"><?php echo Translation::GetCaption("FIND_SHIPMENTS"); ?></h3>
                                </a>
                                <a href="mawb-report.php">
                                    <div class="round-button btn-m-4">5</div>
                                    <h3 class="btn-m-5-create"><?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>

                                <a href="invoices.php">
                                    <div class="round-button-orange btn-m-10">5</div>
                                    <h3 class="btn-m-10-create"><?php echo Translation::GetCaption("INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="manual_invoices.php">
                                    <div class="round-button-orange btn-m-9">4</div>
                                    <h3 class="btn-m-9-create"><?php echo Translation::GetCaption("MANUAL_INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="credit_note.php">
                                    <div class="round-button-orange btn-m-8">3</div>
                                    <h3 class="btn-m-8-create"><?php echo Translation::GetCaption("CREDIT_NOTE_LIST"); ?></h3>
                                </a>
                                <a href="day-summary-report.php">
                                    <div class="round-button-orange btn-m-7">2</div>
                                    <h3 class="btn-m-7-create"><?php echo Translation::GetCaption("DAY_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <a href="account_summary.php">
                                    <div class="round-button-orange btn-m-6">2</div>
                                    <h3 class="btn-m-6-create"><?php echo Translation::GetCaption("ACCOUNT_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <div class="round-button-black btn-m-11">
                                    <a href="support_center.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 

                                <div class="round-button-black btn-m-12">
                                    <a href="../_assets/user_manuals/accounts_userguide.pdf">
                                          <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>


                            </div>
                        </div>

                    </div> 
                </div>        
            </div>
        </div>
        <?

        break;




        case User::USER_TYPE_CUSTOMER_SERVICE :

        ?>
        <div class="row">
            <div class="col-md-12">

                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="visible-lg visible-md hidden-xs hidden-sm">
                            <div class="work-flow-1">
                                <div class="page-heading-box">
                                    <h3><?php echo Translation::GetCaption("CUSTOMER SERVICE DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>

                                <h3 style="position: absolute;    top: 163px;    left: 110px; background:#004E87; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("WORKFLOW"); ?></h3>

                                <h3 style="position:absolute; top: 163px;    right: 120px;  background:#F58025; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("QUALITY_CONTROL"); ?></h3>   

                                <h3 style="    position: absolute;
                                    top: 199px;
                                    left: 385px;
                                    text-align: center;     font-size: 17px; background:#00661A; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("TOOLBOX_SERVICE"); ?>
                                </h3>       

                                <a href="quotationlist.php">
                                    <div class="round-button btn-1">1</div>
                                    <h3 class="btn1-create"><?php echo Translation::GetCaption("QUOTATION"); ?></h3>
                                </a>
                                <a href="cs_bookings.php">
                                    <div class="round-button btn-2">2</div>
                                    <h3 class="btn2-create"><?php echo Translation::GetCaption("FIND_SHIPMENTS"); ?></h3>
                                </a>  
                                <a href="agent.php">
                                    <div class="round-button btn-3">3</div>
                                    <h3 class="btn3-create"><?php echo Translation::GetCaption("AGENT"); ?></h3>
                                </a>
                                <a href="multitracking.php">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo Translation::GetCaption("MULTI_TRACKING"); ?></h3>
                                </a>
                                <a href="#">
                                    <div class="round-button btn-5">5</div>
                                    <h3 class="btn5-create"></h3>
                                </a>
                                <a href="client_list.php">
                                    <div class="round-button-orange btn-10">5</div>
                                    <h3 class="btn10-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_SHIPMENT"); ?></h3>
                                </a>
                                <a href="endofday_search.php">
                                    <div class="round-button-orange btn-9">4</div>
                                    <h3 class="btn9-create"><?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>
                                <a href="find_collection.php">
                                    <div class="round-button-orange btn-8">3</div>
                                    <h3 class="btn8-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_COLLECTIONS"); ?></h3>
                                </a>
                                <a href="#">
                                    <div class="round-button-orange btn-7">2</div>
                                    <h3 class="btn7-create"></h3>
                                </a>
                                <a href="#">
                                    <div class="round-button-orange btn-6">1</div>
                                    <h3 class="btn6-create"></h3>
                                </a>

                                <a href="support_center.php">
                                    <div class="round-button-black btn-11">
                                                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 
                                </a>
                                <a href="#">
                                    <div class="round-button-black btn-12">
                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>
                                </a>



                            </div>
                        </div>



                        <div class="hidden-lg hidden-md visible-sm visible-xs">
                            <div class="workflow-mobile">
                                <div class="workflow-1-mobile-heading">
                                    <h3><?php echo Translation::GetCaption("FINANCE_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>
                                <a href="customers.php">
                                    <div class="round-button btn-m-1">1</div>
                                    <h3 class="btn-m-1-create"><?php echo Translation::GetCaption("MEMBERS"); ?></h3>
                                </a>
                                <a href="tariff_list.php">
                                    <div class="round-button btn-m-2">2</div>
                                    <h3 class="btn-m-2-create"><?php echo Translation::GetCaption("TARIFF_LIST"); ?></h3>
                                </a>
                                <a href="couriers.php">
                                    <div class="round-button btn-m-3">3</div>
                                    <h3 class="btn-m-3-create"><?php echo Translation::GetCaption("CARRIERS"); ?></h3>
                                </a>
                                <a href="bookings.php">
                                    <div class="round-button btn-m-4">4</div>
                                    <h3 class="btn-m-4-create"><?php echo Translation::GetCaption("FIND_SHIPMENTS"); ?></h3>
                                </a>
                                <a href="mawb-report.php">
                                    <div class="round-button btn-m-4">5</div>
                                    <h3 class="btn-m-5-create"><?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>

                                <a href="invoices.php">
                                    <div class="round-button-orange btn-m-10">5</div>
                                    <h3 class="btn-m-10-create"><?php echo Translation::GetCaption("INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="manual_invoices.php">
                                    <div class="round-button-orange btn-m-9">4</div>
                                    <h3 class="btn-m-9-create"><?php echo Translation::GetCaption("MANUAL_INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="credit_note.php">
                                    <div class="round-button-orange btn-m-8">3</div>
                                    <h3 class="btn-m-8-create"><?php echo Translation::GetCaption("CREDIT_NOTE_LIST"); ?></h3>
                                </a>
                                <a href="day-summary-report.php">
                                    <div class="round-button-orange btn-m-7">2</div>
                                    <h3 class="btn-m-7-create"><?php echo Translation::GetCaption("DAY_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <a href="account_summary.php">
                                    <div class="round-button-orange btn-m-6">2</div>
                                    <h3 class="btn-m-6-create"><?php echo Translation::GetCaption("ACCOUNT_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <div class="round-button-black btn-m-11">
                                    <a href="support_center.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 

                                <div class="round-button-black btn-m-12">
                                    <a href="#">
                                          <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>


                            </div>
                        </div>

                    </div> 
                </div>        
            </div>
        </div>
        <?

        break;



        case User::USER_TYPE_ADMIN :

        ?>
        <div class="row">
            <div class="col-md-12">

                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="visible-lg visible-md hidden-xs hidden-sm">
                            <div class="work-flow-1">
                                <div class="page-heading-box">
                                    <h3><?php echo ("DASHBOARD"); ?></h3>
                                    <p><?php echo "SMART TRACK"; ?></p>
                                </div>

                                <h3 style="position: absolute;    top: 163px;    left: 110px; background:#004E87; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo "MODULES" ?></h3>

                                <!--                                <h3 style="position:absolute; top: 163px;    right: 120px;  background:#F58025; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                                                    -moz-border-radius: 5px;
                                                                    border-radius: 5px;"><?php echo ""; ?></h3>   -->

                                <h3 style="    position: absolute;
                                    top: 199px;
                                    left: 385px;
                                    text-align: center;     font-size: 17px; background:#00661A; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("TOOLBOX_SERVICE"); ?>
                                </h3>      


                                <a href="index.php?menu-option=client">
                                    <div class="round-button btn-1">1</div>
                                    <h3 class="btn1-create"><?php echo "Customer" ?></h3>
                                </a>
                                <a href="index.php?menu-option=corporateclient">
                                    <div class="round-button btn-2">2</div>
                                    <h3 class="btn2-create"><?php echo "Corporate Customer" ?></h3>
                                </a>  
                                <a href="index.php?menu-option=finance">
                                    <div class="round-button btn-3">3</div>
                                    <h3 class="btn3-create"><?php echo "Finance" ?></h3>
                                </a>
                                <a href="index.php?menu-option=customerservice">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo "Customer Service" ?></h3>
                                </a>
                                <a href="index.php?menu-option=warehouse">
                                    <div class="round-button btn-5">5</div>
                                    <h3 class="btn5-create"><?php echo "Operation" ?></h3>
                                </a>
                                <a href="services_list.php">
                                    <div class="round-button-orange btn-10">5</div>
                                    <h3 class="btn10-create"><?php echo Translation::GetCaption("SERVICES"); ?></h3>
                                </a>
                                <a href="department_list.php">
                                    <div class="round-button-orange btn-9">4</div>
                                    <h3 class="btn9-create"><?php echo Translation::GetCaption("DEPARTMENT"); ?></h3>
                                </a>
                                <a href="languagekeys_list.php">
                                    <div class="round-button-orange btn-8">3</div>
                                    <h3 class="btn8-create"><?php echo Translation::GetCaption("TRANSLATION"); ?></h3>
                                </a>
                                <a href="market_places_list.php">
                                    <div class="round-button-orange btn-7">2</div>
                                    <h3 class="btn7-create"><?php echo Translation::GetCaption("MARKET_PLACES"); ?></h3>
                                </a>
                                <a href="shopping_platform.php">
                                    <div class="round-button-orange btn-6">1</div>
                                    <h3 class="btn6-create"><?php echo Translation::GetCaption("SHOPPING_PLATFORM"); ?></h3>
                                </a>


                                <a href="support_center.php">
                                    <div class="round-button-black btn-11">
                                                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 
                                </a>
                                <a href="#">
                                    <div class="round-button-black btn-12">
                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>
                                </a>



                            </div>
                        </div>

                    </div>
                </div>


                <div class="hidden-lg hidden-md visible-sm visible-xs">

                    <div class="workflow-mobile">
                        <div class="workflow-1-mobile-heading">
                            <h3><?php echo "DASHBOARD"; ?></h3>
                            <p><?php echo "SMART TRACK"; ?></p>
                        </div>
                        <a href="index.php?menu-option=client">

                            <div class="round-button btn-m-1">1</div>
                            <h3 class="btn-m-1-create"><?php echo "Customer" ?></h3>
                        </a>
                        <a href="index.php?menu-option=corporateclient">
                            <div class="round-button btn-m-2">2</div>
                            <h3 class="btn-m-2-create"><?php echo "Corporate Customer" ?></h3>
                        </a>
                        <a href="index.php?menu-option=finance">
                            <div class="round-button btn-m-3">3</div>
                            <h3 class="btn-m-3-create"><?php echo "Finance" ?></h3>
                        </a>
                        <a href="index.php?menu-option=customerservice">
                            <div class="round-button btn-m-4">4</div>
                            <h3 class="btn-m-4-create"><?php echo "Customer Service" ?></h3>
                        </a>
                        <a href="customers.php">
                            <div class="round-button btn-m-4">5</div>
                            <h3 class="btn-m-5-create"><?php echo "Operaion" ?></h3>
                        </a>














                    </div>
                </div>

            </div> 
        </div>        
        </div>
        </div>









        <?

        break;










        case User::USER_TYPE_CUSTOMER_SERVICE :

        ?>
        <div class="row">
            <div class="col-md-12">

                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="visible-lg visible-md hidden-xs hidden-sm">
                            <div class="work-flow-1">
                                <div class="page-heading-box">
                                    <h3><?php echo Translation::GetCaption("CUSTOMER SERVICE DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>

                                <h3 style="position: absolute;    top: 163px;    left: 110px; background:#004E87; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("WORKFLOW"); ?></h3>

                                <h3 style="position:absolute; top: 163px;    right: 120px;  background:#F58025; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("QUALITY_CONTROL"); ?></h3>   

                                <h3 style="    position: absolute;
                                    top: 199px;
                                    left: 385px;
                                    text-align: center;     font-size: 17px; background:#00661A; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("TOOLBOX_SERVICE"); ?>
                                </h3>       

                                <a href="quotationlist.php">
                                    <div class="round-button btn-1">1</div>
                                    <h3 class="btn1-create"><?php echo Translation::GetCaption("QUOTATION"); ?></h3>
                                </a>
                                <a href="cs_bookings.php">
                                    <div class="round-button btn-2">2</div>
                                    <h3 class="btn2-create"><?php echo Translation::GetCaption("FIND_SHIPMENTS"); ?></h3>
                                </a>  
                                <a href="agent.php">
                                    <div class="round-button btn-3">3</div>
                                    <h3 class="btn3-create"><?php echo Translation::GetCaption("AGENT"); ?></h3>
                                </a>
                                <a href="multitracking.php">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo Translation::GetCaption("MULTI_TRACKING"); ?></h3>
                                </a>
                                <a href="#">
                                    <div class="round-button btn-5">5</div>
                                    <h3 class="btn5-create"></h3>
                                </a>
                                <a href="client_list.php">
                                    <div class="round-button-orange btn-10">5</div>
                                    <h3 class="btn10-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_SHIPMENT"); ?></h3>
                                </a>
                                <a href="endofday_search.php">
                                    <div class="round-button-orange btn-9">4</div>
                                    <h3 class="btn9-create"><?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>
                                <a href="find_collection.php">
                                    <div class="round-button-orange btn-8">3</div>
                                    <h3 class="btn8-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_COLLECTIONS"); ?></h3>
                                </a>
                                <a href="#">
                                    <div class="round-button-orange btn-7">2</div>
                                    <h3 class="btn7-create"></h3>
                                </a>
                                <a href="#">
                                    <div class="round-button-orange btn-6">1</div>
                                    <h3 class="btn6-create"></h3>
                                </a>

                                <a href="support_center.php">
                                    <div class="round-button-black btn-11">
                                                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 
                                </a>
                                <a href="#">
                                    <div class="round-button-black btn-12">
                                            <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </div>
                                    <h3 class="btn12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>
                                </a>



                            </div>
                        </div>



                        <div class="hidden-lg hidden-md visible-sm visible-xs">
                            <div class="workflow-mobile">
                                <div class="workflow-1-mobile-heading">
                                    <h3><?php echo Translation::GetCaption("FINANCE_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>
                                <a href="customers.php">
                                    <div class="round-button btn-m-1">1</div>
                                    <h3 class="btn-m-1-create"><?php echo Translation::GetCaption("MEMBERS"); ?></h3>
                                </a>
                                <a href="tariff_list.php">
                                    <div class="round-button btn-m-2">2</div>
                                    <h3 class="btn-m-2-create"><?php echo Translation::GetCaption("TARIFF_LIST"); ?></h3>
                                </a>
                                <a href="couriers.php">
                                    <div class="round-button btn-m-3">3</div>
                                    <h3 class="btn-m-3-create"><?php echo Translation::GetCaption("CARRIERS"); ?></h3>
                                </a>
                                <a href="bookings.php">
                                    <div class="round-button btn-m-4">4</div>
                                    <h3 class="btn-m-4-create"><?php echo Translation::GetCaption("FIND_SHIPMENTS"); ?></h3>
                                </a>
                                <a href="mawb-report.php">
                                    <div class="round-button btn-m-4">5</div>
                                    <h3 class="btn-m-5-create"><?php echo Translation::GetCaption("MAWB_REPORT"); ?></h3>
                                </a>

                                <a href="invoices.php">
                                    <div class="round-button-orange btn-m-10">5</div>
                                    <h3 class="btn-m-10-create"><?php echo Translation::GetCaption("INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="manual_invoices.php">
                                    <div class="round-button-orange btn-m-9">4</div>
                                    <h3 class="btn-m-9-create"><?php echo Translation::GetCaption("MANUAL_INVOICE_LIST"); ?></h3>
                                </a>
                                <a href="credit_note.php">
                                    <div class="round-button-orange btn-m-8">3</div>
                                    <h3 class="btn-m-8-create"><?php echo Translation::GetCaption("CREDIT_NOTE_LIST"); ?></h3>
                                </a>
                                <a href="day-summary-report.php">
                                    <div class="round-button-orange btn-m-7">2</div>
                                    <h3 class="btn-m-7-create"><?php echo Translation::GetCaption("DAY_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <a href="account_summary.php">
                                    <div class="round-button-orange btn-m-6">2</div>
                                    <h3 class="btn-m-6-create"><?php echo Translation::GetCaption("ACCOUNT_SUMMARY_REPORT"); ?></h3>
                                </a>
                                <div class="round-button-black btn-m-11">
                                    <a href="support_center.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 

                                <div class="round-button-black btn-m-12">
                                    <a href="#">
                                          <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>


                            </div>
                        </div>

                    </div> 
                </div>        
            </div>
        </div>
        <?

        break;
        default:
        $Sessionuser = SessionManager::getUser();
        if($Sessionuser->getThemeId() == 1)
        {
        
        include("germany_dashboard.php");
        }
        else
        {

        ?>		 
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="visible-lg visible-md hidden-xs hidden-sm">
                            <div class="work-flow-1">
                                <div class="page-heading-box">
                                    <h3><?php echo Translation::GetCaption("APPLICATION_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>

                                <h3 style="position: absolute;    top: 163px;    left: 110px; background:#004E87; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;">                      <?php echo Translation::GetCaption("WORKFLOW"); ?></h3>

                                <h3 style="position:absolute; top: 163px;    right: 120px;  background:#F58025; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("SEARCH_DATABASES"); ?></h3>   

                                <h3 style="    position: absolute;
                                    top: 199px;
                                    left: 385px;
                                    text-align: center;     font-size: 17px; background:#00661A; color:#fff; padding:5px; -webkit-border-radius: 5px;
                                    -moz-border-radius: 5px;
                                    border-radius: 5px;"><?php echo Translation::GetCaption("TOOLBOX_SERVICE"); ?>
                                </h3>       
                                <? 
                                if($Sessionuser->getRetailCustomer() == "1")
                                {
                                $consignment_page = "booking_quote.php";
                                }
                                else
                                {
                                if($Sessionuser->getIsProduct() == "YES")
                                {
                                $consignment_page = "product_consignment_edit.php?option=new";
                                }
                                else
                                {
                                $consignment_page = "consignment_edit.php?option=new";
                                }
                                }


                                ?>           
                                <a href="<? echo $consignment_page; ?>">
                                    <div class="round-button btn-1">1</div>
                                    <h3 class="btn1-create"><?php echo Translation::GetCaption("CREATE_SHIPMENTS"); ?></h3>
                                </a>
                                <a href="client_list.php?show=valid_show">
                                    <div class="round-button btn-2">2</div>
                                    <h3 class="btn2-create"><?php echo Translation::GetCaption("CREATE_LABELS"); ?></h3>
                                </a>  
                                <a href="manifest_type.php?manifest=selected">
                                    <div class="round-button btn-3">3</div>
                                    <h3 class="btn3-create"><?php echo Translation::GetCaption("CREATE_MANIFESTS"); ?></h3>
                                </a>
                                <? if($Sessionuser->getBagging() == "YES")
                                { ?>
                                <a href="coclient_user_endofday_collection.php">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo Translation::GetCaption("REQUEST_COLLECTION"); ?></h3>
                                </a>
                                <a href="multitracking.php">
                                    <div class="round-button btn-5">5</div>
                                    <h3 class="btn5-create"><?php echo Translation::GetCaption("MULTI_TRACKING_SYSTEM"); ?></h3>
                                </a>
                                <?
                                }
                                else
                                {?>
                                <a href="multitracking.php">
                                    <div class="round-button btn-4">4</div>
                                    <h3 class="btn4-create"><?php echo Translation::GetCaption("MULTI_TRACKING_SYSTEM"); ?></h3>
                                </a>
                                <? } ?>
                                <a href="client_list.php?show=searchShipment">
                                    <div class="round-button-orange btn-10">5</div>
                                    <h3 class="btn10-create"><?php echo Translation::GetCaption("SEARCH_DATABASE"); ?></h3>
                                </a>
                                <a href="label_list.php">
                                    <div class="round-button-orange btn-9">4</div>
                                    <h3 class="btn9-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_LABELS"); ?></h3>
                                </a>
                                <a href="coclient_user_endofday.php">
                                    <div class="round-button-orange btn-8">3</div>
                                    <h3 class="btn8-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_MANIFESTS"); ?></h3>
                                </a>
                                <a href="find_collection.php">
                                    <div class="round-button-orange btn-7">2</div>
                                    <h3 class="btn7-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_COLLECTIONS"); ?></h3>
                                </a>
                                <!--   <a href="search.php">
                                       <div class="round-button-orange btn-6">1</div>
                                       <h3 class="btn6-create"><?php echo Translation::GetCaption("ADVANCED_SHIPMENT_SEARCH"); ?></h3>
                                   </a>-->
                                <a href="support_center.php">
                                    <div class="round-button-black btn-11">

                                    <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->

                                    </div>

                                    <h3 class="btn11-create">
                                        <?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?>
                                    </h3> 

                                </a>
                                <? if (SessionManager::getUser()->getIsProduct() == "YES") { ?>
                                <a href="../_assets/user_manuals/User Guide One World Tracked (Product User)1.pdf">
                                    <? } else{ ?>
                                    <a href="../_assets/user_manuals/User Guide One World Tracked (Standard_User).pdf">
                                        <? } ?>
                                        <div class="round-button-black btn-12">

                                      <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->

                                        </div>
                                        <h3 class="btn12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>
                                    </a>

                                    <a href="changestatus.php">
                                        <div class="round-button-black btn-13">

                                    <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->

                                        </div>
                                        <h3 class="btn13-create"><?php echo Translation::GetCaption("STOP_SHIPMENT"); ?></h3>

                                    </a>

                                    <a href="label_list_x.php">
                                        <div class="round-button-black btn-14">

                                   <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->

                                        </div>
                                        <h3 class="btn14-create"><?php echo Translation::GetCaption("RELABEL"); ?></h3> 
                                    </a>

                                    <a href="csv.php">
                                        <div class="round-button-black btn-15">

                                   <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->

                                        </div>
                                        <h3 class="btn15-create"><?php echo Translation::GetCaption("EXPORT_SHIPMENTS"); ?></h3> 
                                    </a>


                            </div>
                        </div>



                        <div class="hidden-lg hidden-md visible-sm visible-xs">
                            <div class="workflow-mobile">
                                <div class="workflow-1-mobile-heading">
                                    <h3><?php echo Translation::GetCaption("APPLICATION_DASHBOARD"); ?></h3>
                                    <p><?php echo Translation::GetCaption("SMART_TRACK"); ?></p>
                                </div>
                                <div class="round-button btn-m-1">1</div>
                                <h3 class="btn-m-1-create">Create single shipment</h3>
                                <div class="round-button btn-m-2">2</div>
                                <h3 class="btn-m-2-create"><?php echo Translation::GetCaption("CREATE_LABELS"); ?></h3>
                                <div class="round-button btn-m-3">3</div>
                                <h3 class="btn-m-3-create"><?php echo Translation::GetCaption("CREATE_MANIFESTS"); ?></h3>
                                <div class="round-button btn-m-4">4</div>
                                <h3 class="btn-m-4-create"><?php echo Translation::GetCaption("REQUEST_COLLECTION"); ?></h3>

                                <div class="round-button btn-m-5">5</div>
                                <h3 class="btn-m-5-create"><?php echo Translation::GetCaption("MULTI_TRACKING_SYSTEM"); ?></h3>


                                <div class="round-button-orange btn-m-10">5</div>
                                <h3 class="btn-m-10-create"><?php echo Translation::GetCaption("ADVANCED_SHIPMENT_SEARCH"); ?></h3>

                                <div class="round-button-orange btn-m-9">4</div>
                                <h3 class="btn-m-9-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_COLLECTIONS"); ?></h3>

                                <div class="round-button-orange btn-m-8">3</div>
                                <h3 class="btn-m-8-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_MANIFESTS"); ?></h3>

                                <div class="round-button-orange btn-m-7">2</div>
                                <h3 class="btn-m-7-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_LABELS"); ?></h3>

                                <div class="round-button-orange btn-m-6">1</div>
                                <h3 class="btn-m-6-create"><?php echo Translation::GetCaption("SHOW_LIST_OF_SHIPMENT"); ?></h3>




                                <div class="round-button-black btn-m-11">
                                    <a href="support_center.php">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-11-create"><?php echo Translation::GetCaption("CONTACT"); ?><br><?php echo Translation::GetCaption("HELPDESK"); ?></h3> 

                                <div class="round-button-black btn-m-12">
                                    <? if (SessionManager::getUser()->getIsProduct() == "YES") { ?>
                                    <a href="../_assets/user_manuals/User Guide One World Tracked (Product User)1.pdf">
                                        <? } else{ ?>
                                        <a href="../_assets/user_manuals/User Guide One World Tracked (Standard_User).pdf">
                                            <? } ?>
                                                  <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                        </a>
                                </div>
                                <h3 class="btn-m-12-create"><?php echo Translation::GetCaption("DOWNLOAD_MANUAL"); ?></h3>



                                <div class="round-button-black btn-m-13">
                                    <a href="#">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-13-create"><?php echo Translation::GetCaption("STOP_SHIPMENT"); ?></h3>


                                <div class="round-button-black btn-m-14">
                                    <a href="#">
                                       <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-14-create"><?php echo Translation::GetCaption("RELABEL"); ?></h3> 



                                <div class="round-button-black btn-m-15">
                                    <a href="#">
                                      <!--<i class="fa fa-arrow-down" aria-hidden="true"></i>-->
                                    </a>
                                </div>
                                <h3 class="btn-m-15-create"><?php echo Translation::GetCaption("EXPORT_SHIPMENTS"); ?></h3> 


                            </div>
                        </div>

                    </div> 
                </div>        
            </div>
        </div> 
        <?
        }
        break;


        }


        ?>
        <?php
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
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-bar-chart font-green-sharp hide"></i>
                                <span class="caption-subject font-green-sharp bold uppercase"><?php echo Translation::GetCaption("TICKET"); ?></span>
                                <span class="caption-helper"><?php echo Translation::GetCaption("NEED_RESPONSE"); ?></span>
                            </div>                        
                            <div class="actions">
                                <a class="btn btn-xs btn-arrow-link btn-default" href="support_center_list.php"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="display-none" style="display: block;">
                                <!-- CONSIGNMENT TABLE -->
                                <div id='table_container'  class="main_grid2">
                                    <div class="table-scrollable">
                                        <table class='table table-striped table-bordered table-advance table-hover'>
                                            <thead>
                                                <tr>
                                                    <th id="col1" class="bg-red"><?php echo Translation::GetCaption("TICKET_ID"); ?></th>
                                                    <th id="col1" class="bg-red"><?php echo Translation::GetCaption("SUBJECT"); ?></th>
                                                    <?php if ($sessionUser->getUserType() == "admin") { ?>
                                                        <th id="col3" class="bg-red"><?php echo Translation::GetCaption("TICKET_BY"); ?></th>
                                                    <?php } ?>
                                                    <th id="col3" class="bg-red"><?php echo Translation::GetCaption("PRIORITY"); ?></th>
                                                    <th id="col2" class="bg-red"><?php echo Translation::GetCaption("DATE"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($data_return as $value) {
                                                    ?>

                                                    <tr>
                                                        <td><a href="ticket_details.php?id=<?php echo md5($value->getId()); ?>" ><?php echo $value->getTicketCode(); ?></a></td>
                                                        <td><?php echo $value->getSubject(); ?></td>
                                                        <?php if ($sessionUser->getUserType() == "admin") { ?>
                                                            <td><?php echo $value->getAddedBy(); ?></td>
                                                        <?php } ?>
                                                        <td <?php
                                                        if ($value->getPriority() == "low") {
                                                            echo 'style="color:#8A8A8A"';
                                                        } elseif ($value->getPriority() == "medium") {
                                                            echo 'style="color:#000000"';
                                                        } elseif ($value->getPriority() == "high") {
                                                            echo 'style="color:#F07D18"';
                                                        } elseif ($value->getPriority() == "urgent") {
                                                            echo 'style="color:#E826C6"';
                                                        } elseif ($value->getPriority() == "critical") {
                                                            echo 'style="color:#FF0000"';
                                                        }
                                                        ?> ><?php echo ucwords($value->getPriority()); ?></td>
                                                        <td><?php
                                                            $date = new DateTime();
                                                            $date->setTimestamp($value->getAddedDate());
                                                            echo $date->format('Y-m-d H:i:s');
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>  <!-- table_container -->  
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <!-- BEGIN PORTLET-->
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bar-chart font-green-sharp hide"></i>
                            <span class="caption-subject font-green-sharp bold uppercase"><?php echo Translation::GetCaption("SHIPMENT"); ?></span>
                            <span class="caption-helper"><?php echo Translation::GetCaption("TOTAL_STATS"); ?></span>
                        </div>                        
                        <div class="actions">
                            <a class="btn btn-xs btn-arrow-link btn-default" href="client_list.php"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="consignment_statistics_loading">
                            <img src="../_assets/admin/layout/img/loading.gif" alt="loading"/>
                        </div>
                        <div id="consignment_statistics_content" class="display-none">
                            <div id="consignment_statistics" class="chart">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
            <div class="col-md-6 col-sm-12">
                <!-- BEGIN PORTLET-->
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-share font-red-sunglo hide"></i>
                            <span class="caption-subject font-red-sunglo bold uppercase"><?php echo Translation::GetCaption("SHIPMENT"); ?></span>
                            <span class="caption-helper"><?php echo Translation::GetCaption("DAILY_STATS"); ?></span>
                        </div>
                        <div class="actions">
                            <a class="btn btn-xs btn-arrow-link btn-default" id="Consignment_daily" href="#"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="daily_consignment_statistics_loading">
                            <img src="../_assets/admin/layout/img/loading.gif" alt="loading"/>
                        </div>
                        <div id="daily_consignment_statistics_content" class="display-none">
                            <div id="daily_consignment_statistics" class="chart">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- BEGIN CHART PORTLET-->
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bar-chart font-green-haze"></i>
                            <span class="caption-subject bold uppercase font-green-haze"><?php echo Translation::GetCaption("TOP_COUNTRIES"); ?></span>
                            <span class="caption-helper"><?php echo Translation::GetCaption("TOP_COUNTRIES_YOU_SHIP"); ?></span>
                        </div>
                        <div class="actions">
                            <a class="btn btn-xs btn-arrow-link btn-default" href="top_countries_list.php"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a>
                        </div>                        
                    </div>
                    <div class="portlet-body">
                        <div id="top_countries_chart" class="chart" style="height: 400px;"></div>
                        <?php
                        if (count($this->top_countries_bar) > 0) {
                            ?>
                            <div class="well margin-top-20">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <?php
                                        foreach ($this->top_countries_bar as $country_name => $country_total) {
                                            ?>
                                            <span class="margin-right-10">
                                                <small><strong><?php echo $country_name; ?>:</strong> <?php echo $country_total; ?></small>
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <!-- END CHART PORTLET-->
            </div>
            <? 
            if($Sessionuser->getIsProduct() == 'NO') { ?>
            <div class="col-md-6">
                <!-- BEGIN CHART PORTLET-->
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bar-chart font-green-haze"></i>
                            <span class="caption-subject bold uppercase font-green-haze"><?php echo Translation::GetCaption("TOP_SERVICES"); ?></span>
                            <span class="caption-helper"><?php echo Translation::GetCaption("TOP_SERVICES_YOU_USE"); ?></span>
                        </div>
                        <div class="actions">
                            <a class="btn btn-xs btn-arrow-link btn-default" href="top_services_list.php"><?php echo Translation::GetCaption("VIEW_MORE"); ?></a>
                        </div>                        
                    </div>
                    <div class="portlet-body">
                        <div id="top_services_chart" class="chart" style="height: 400px;"></div>
                        <?php
                        if (count($this->top_services_bar) > 0) {
                            ?>                        
                            <div class="well margin-top-20">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <?php
                                        foreach ($this->top_services_bar as $service_name => $service_total) {
                                            ?>
                                            <span class="margin-right-10">
                                                <small><strong><?php echo $service_name; ?>:</strong> <?php echo $service_total; ?></small>
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>  
                    </div>
                </div>
                <!-- END CHART PORTLET-->
            </div>
            <? } ?>
        </div>
        <div class="row">

            <!-- END PAGE CONTENT-->
            <?php
        }

        public function renderHead() {
            ?>
            <style type="text/css">
                .main-top-margin {    position: absolute;
                                      top: 83px;}

                .main-top-margin .fa		{color:#004E87}

                .tabbable-custom>.nav-tabs>li.active {
                    border-top: 3px solid #004E87;
                    margin-top: 0;
                    position: relative;
                    background:#004E87
                }
                .tabbable-custom>.nav-tabs>li.active>a:hover {background:#004E87 !important; color:#fff !important}

                .tabbable-custom>.nav-tabs>li.active>a {
                    background:#004E87 !important; color:#fff !important
                }

                .btn-orange {
                    color: #fff;
                    background-color: #F59231;
                    border-color: #F59231;
                }


                .stepwizard-step p {
                    margin-top: 10px;    
                }

                .process-row {
                    display: table-row;
                }

                .process {
                    display: table;     
                    width: 100%;
                    position: relative;
                }

                .process-step button[disabled] {
                    opacity: 1 !important;
                    filter: alpha(opacity=100) !important;
                }



                .process-step {    
                    display: table-cell;
                    text-align: center;
                    position: relative;
                }

                .process-step p {
                    margin-top:10px;

                }

                .btn-circle {
                    width: 100px;
                    height: 100px;
                    text-align: center;
                    padding: 6px 0;
                    font-size: 12px;
                    line-height: 1.428571429;
                    border-radius: 100px !important;
                }


                calc-border { border:1px solid #ccc; padding:5px; border-radius:5px; background:#fff;     margin-bottom: 25px;}
                .calc-border h4 { text-align:center
                }




                .blue-left-box {    background: rgba(0,136,204,.8); text-align:center !important;     border-bottom: 1px solid #15171e; padding:20px; margin-bottom:20px;  }
                .cal-screen { display: table; width:100%}
                .calc-border { border:1px solid #ccc; padding:10px}



                .cal-result h2 {
                    font-size: 65px;
                    float: right;
                    font-family: 'Orbitron', sans-serif;
                    font-weight: 500;
                    margin: 0;
                    color: #000;
                    margin-top:20px;

                }

                .cal-screen {
                    border: 10px solid #fff;
                    -webkit-border-radius: 10px 10px 10px 10px !important;
                    border-radius: 10px 10px 10px 10px !important;

                    padding:10px 10px 0;
                    margin-bottom:10px;

                    font-size: 20px;
                    color: #ccc;
                    font-family: 'Orbitron', sans-serif;
                    /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#ffffff+0,e5e5e5+100;White+3D */
                    background: #ffffff; /* Old browsers */
                    background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 100%); /* FF3.6-15 */
                    background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Chrome10-25,Safari5.1-6 */
                    background: linear-gradient(to bottom,  #ffffff 0%,#e5e5e5 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
                    border:1px solid #ccc


                }


                #radioBtn2 .notActive{   color: #3276b1;   background-color: #fff;}
                .cal-dimesnion { background: url(images/dimesion.png) no-repeat; max-width: 207px; height: 131px; position: relative; margin: 20px auto; clear:both; display:block }
                .dhead { position: absolute; top: 10px; left: 53px}
                .dheight { right: -85px;top: 46px;width: 82px; position: absolute; -webkit-transform: rotate(90deg);
                           -moz-transform: rotate(90deg);
                           -o-transform: rotate(90deg);}
                .dheight input { background: #99B3FF !important; color: #000 !important;       

                }

                .dlenght { left: 80px;top: 126px;width: 92px;position: absolute;margin-top: 15px;}
                .dlenght input { background: #FF9999 !important; color: #000 !important;}

                .dwidht{ left: -25px;top: 103px;position: absolute; 
                         -webkit-transform: rotate(39deg);
                         -moz-transform: rotate(39deg);
                         -o-transform: rotate(39deg);
                         writing-mode: lr-tb;

                }
                .dwidhtinput { background: #FFDC73 !important; color: #000 !important; width: 92px; left: -50px;top: 118px; position: absolute; -webkit-transform: rotate(40deg);
                               -moz-transform: rotate(40deg);}

                @media screen and (max-width: 400px) {
                    .dheight {    right: -15px;}
                    .dheight span {text-shadow: 1px 1px 1px #000000; background: #000}
                    .dwidht {    left: -25px;    top: 53px;text-shadow: 1px 1px 1px #000000; }

                    .dwidhtinput {width: 54px;   left: -20px; top: 92px;}


                }

                .control-label { float:left; }

                .col-xs-15,
                .col-sm-15,
                .col-md-15,
                .col-lg-15 {
                    position: relative;
                    min-height: 1px;
                    padding-right: 10px;
                    padding-left: 10px;
                }

                .col-xs-15 {
                    width: 20%;
                    float: left;
                }
                @media (min-width: 768px) {
                    .col-sm-15 {
                        width: 20%;
                        float: left;
                    }
                }
                @media (min-width: 992px) {
                    .col-md-15 {
                        width: 20%;
                        float: left;
                    }
                }
                @media (min-width: 1200px) {
                    .col-lg-15 {
                        width: 20%;
                        float: left;
                    }
                }


            </style>
            <style type="text/css">


                .btn-m-15 { position:absolute;     top: 1920px;    left: 58px;}
                .btn-m-15-create { position: absolute;    top: 1980px;       left: 0px;   text-align: center;     font-size: 16px;}

                .btn-m-14 { position:absolute;     top: 1820px;    left: 58px;}
                .btn-m-14-create { position: absolute;    top: 1880px;       left: 65px;   text-align: center;     font-size: 16px;}

                .btn-m-13 { position:absolute;     top: 1720px;    left: 58px;}
                .btn-m-13-create { position: absolute;    top: 1780px;       left: 42px;   text-align: center;     font-size: 16px;}



                .btn-m-12 { position:absolute;     top: 1620px;    left: 58px;}
                .btn-m-12-create { position: absolute;    top: 1680px;       left: 42px;   text-align: center;     font-size: 16px;}



                .btn-m-11 { position:absolute;     top: 1520px;    left: 58px;}
                .btn-m-11-create { position: absolute;    top: 1580px;       left: 42px;   text-align: center;     font-size: 16px;}




                .btn-m-10 { position:absolute;     top: 1400px;    left: 58px;}
                .btn-m-10-create { position: absolute;    top: 1460px;       left: 42px;   text-align: center;     font-size: 16px;}

                .btn-m-9 { position:absolute;     top: 1280px;    left: 58px;}
                .btn-m-9-create { position: absolute;    top: 1340px;        left: 42px;   text-align: center;     font-size: 16px;}


                .btn-m-8 { position:absolute;     top: 1140px;    left: 58px;}
                .btn-m-8-create { position: absolute;    top: 1200px;       left: 42px;   text-align: center;     font-size: 16px;}


                .btn-m-7 { position:absolute;     top: 1020px;    left: 58px;}
                .btn-m-7-create { position: absolute;    top: 1080px;       left: 42px;  text-align: center;     font-size: 16px;}


                .btn-m-6 { position:absolute;     top: 900px;    left: 58px;}
                .btn-m-6-create { position: absolute;    top: 960px;        left: 42px;   text-align: center;     font-size: 16px;}

                .btn-m-5 { position:absolute;     top: 750px;    left: 58px;}
                .btn-m-5-create { position: absolute;    top: 800px;        left: 42px;    text-align: center;     font-size: 16px;}


                .btn-m-4 { position:absolute;     top: 620px;    left: 58px;}
                .btn-m-4-create { position: absolute;    top: 680px;       left: 42px;    text-align: center;     font-size: 16px;}


                .btn-m-3 { position:absolute;     top: 500px;    left: 58px;}
                .btn-m-3-create { position: absolute;    top: 560px;       left: 42px;    text-align: center;     font-size: 16px;}




                .btn-m-2 { position:absolute;     top: 380px;    left: 58px;}
                .btn-m-2-create { position: absolute;    top: 440px;    left: 47px;    text-align: center;     font-size: 16px;}


                .btn-m-1 { position:absolute;     top: 240px;    left: 58px;}
                .btn-m-1-create { position: absolute;    top: 305px;    left: 0;    text-align: center;     font-size: 16px;}

                .workflow-1-mobile-heading {    position: absolute;    top: 80px;    width: 150px;    height: 150px;    left: 14px; color:#fff;}








                .workflow-mobile { background: url(../images/workflow1-mobile.png) no-repeat; width:248px; height:2050px; position:relative; margin:10px auto; text-align:center;  }

                .round-button-black .fa {    color: #fff;
                                             font-size: 35px;
                }

                .btn-15 { position:absolute; right: 405px;   top:690px;}
                .btn15-create { position:absolute;   right: 362px;
                                top: 744px;font-size: 16px; text-align:center; -webkit-box-shadow: 0 0 2px 2px #FFFFFF;
                                box-shadow: 0 0 2px 2px #FFFFFF;}



                .btn-14 { position:absolute; right: 405px;   top:590px;}
                .btn14-create { position:absolute;   right: 411px;
                                top: 644px;font-size: 16px; text-align:center; -webkit-box-shadow: 0 0 2px 2px #FFFFFF;
                                box-shadow: 0 0 2px 2px #FFFFFF;}



                .btn-13 { position:absolute; right: 405px;   top:490px;}
                .btn13-create { position:absolute;   right: 380px;
                                top: 544px;font-size: 16px; text-align:center; -webkit-box-shadow: 0 0 2px 2px #FFFFFF;
                                box-shadow: 0 0 2px 2px #FFFFFF;}





                .btn-12 { position:absolute; right: 405px;   top: 390px;}
                .btn12-create { position:absolute;   right: 381px;
                                top: 444px;font-size: 16px; text-align:center; -webkit-box-shadow: 0 0 2px 2px #FFFFFF;
                                box-shadow: 0 0 2px 2px #FFFFFF;}



                .btn-11 { position:absolute;     right: 405px;
                          top: 257px;}
                .btn11-create { position:absolute;       right: 405px;
                                top: 311px;font-size: 16px;-webkit-box-shadow: 0 0 2px 2px #FFFFFF;
                                box-shadow: 0 0 2px 2px #FFFFFF;}


                .btn-10 { position:absolute; right:0; top: 250px}
                .btn10-create { position:absolute; right:88px; top: 250px; font-size: 16px;}

                .btn-9 { position:absolute; right:0; top: 370px}
                .btn9-create { position:absolute; right:88px; top: 373px; font-size: 16px;}


                .btn-8 { position:absolute; right:0; top: 480px}
                .btn8-create { position:absolute; right:88px; top: 490px; font-size: 16px;}

                .btn-7 { position:absolute; right:0; top: 600px}
                .btn7-create { position:absolute; right:88px; top: 613px; font-size: 16px;}

                .btn-6 { position:absolute; right:0; top: 700px}
                .btn6-create { position:absolute; right:88px; top: 712px; font-size: 16px;}


                .btn-5 { position:absolute; left:0; top: 650px}
                .btn5-create { position:absolute; left:75px; top: 655px; font-size: 16px;}

                .btn-4 { position:absolute; left:0; top: 550px}
                .btn4-create { position:absolute; left:75px; top: 550px; font-size: 16px;}


                .btn-4 { position:absolute; left:0; top: 550px}
                .btn4-create { position:absolute; left:75px; top: 550px; font-size: 16px;}


                .btn-3 { position:absolute; left:0; top: 441px}
                .btn3-create { position:absolute; left:75px; top: 441px; font-size: 16px;}


                .btn-2 { position:absolute; left:0; top: 341px}
                .btn2-create { position:absolute; left:75px; top: 333px; font-size: 16px;}

                .btn-1 { position:absolute; left:0; top: 243px}
                .btn1-create { position:absolute; left:75px; top: 245px; font-size: 16px;}

                .page-heading-box {     width: 175px;    height: 150px;    position: absolute;    top: 66px;    left: 373px;    text-align: center; color:#fff;   }
                .work-flow-1 { background: url(../images/flow-image-01.png) no-repeat; width:900px; height:768px; position:relative; margin:0 auto 10px auto }

                .round-button {    width: 70px;    height: 70px;    border-radius: 50%;    border: 2px solid #59B4F7;    overflow: hidden;    background: #004E87;    box-shadow: 0 0 3px gray;  border-radius: 50% !important; padding: 18px;     text-align: center;
                                   font-size: 20px;
                                   color: #fff;}
                .round-button:hover {    background: #F58025;}
                .round-button img {   display: block;    width: 37px;          height: 37px;}

                .round-button-orange {    width: 70px;    height: 70px;    border-radius: 50%;    border: 2px solid #FEC715;    overflow: hidden;    background: #F58025;    box-shadow: 0 0 3px gray;  border-radius: 50% !important; padding: 18px; text-indent:-11111111px}
                .round-button-orange:hover {    background: #004E87;}
                .round-button-orange img {   display: block;    width: 37px;           height: 37px;}
                .round-button-black {    width: 70px;    height: 70px;    border-radius: 50%;    border: 2px solid #006600;    overflow: hidden;    background: #238C00;    box-shadow: 0 0 3px gray;  border-radius: 50% !important; padding: 18px;}
                .round-button-black:hover {    background: #00661A;}
                .round-button-black img {   display: block;    width: 37px;           height: 37px;}


                #boxes-issues * div  { display:block !important}     
                .enter-number{ background:url(../assets/images/textarea-bg.png) no-repeat; width:342px; height:341px; }
                .enter-number textarea{   width: 200px;  height: 239px;  background: transparent;  margin: 55px 0 0 40px; border:none;}

            </style>

            <script src="../_assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
            <script src="../_assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>

            <script type="text/javascript">
                $(document).ready(function () {
                    initCratTotalConsignment();
                    initCratDailyConsignment();
                    initChartTopCountries();
                    initChartTopServices();
                    getTotalShipments();
                    getTotalHoldShipment();
                    getTotalShippedShipment();
                    getTotalDeliveredShipment();


                    //MULTI TRACKING
                    $(".start-tracking").click(function () {

                        if ($.trim($("#resnums").val()) == '') {
                            alert("<?= Translation::GetCaption("MULTITRACKING_ERROR"); ?>");
                        } else {
                            var totalNumberValue = $("#resnums").val().split("\n");
                            //alert(totalNumberValue.length);
                            if (totalNumberValue.length > 50)
                                alert("You are not allowed to track more than " + record_limit + " number at one go.");
                            else
                                $("#resTrackfrm").submit();
                        }
                    });

                    // GET QUOTATIOn
                    $("#calculatePrice").click(function () {

                        var calc_shipping_from = $('#calc_shipping_from').val();
                        var calc_shipping_to = $('#calc_shipping_to').val();
                        var calc_weight = $('#calc_weight').val();
                        var calc_currency = $('#calc_currency').val();
                        var calc_length = $('#calc_length').val();
                        var calc_width = $('#calc_width').val();
                        var calc_height = $('#calc_height').val();

                        var user_type = "<? echo $_SESSION['menu-option']; ?>";
                        $.post("index_hb.php?menu-option=" + user_type, {func: "GET_TARRIF", calc_shipping_from: calc_shipping_from, calc_shipping_to: calc_shipping_to,
                            calc_weight: calc_weight, calc_currency: calc_currency, calc_length: calc_length, calc_width: calc_width, calc_height: calc_height, user_type: user_type}, function (response_data)
                        {
                            $('#screen_price').empty();
                            $('#screen_price').append(response_data);
                        });
                    });

                    // SHOW ALL
                    $("#show_all").click(function () {
                        $("#form_action").val("show_all_index");
                        $("#frm_dashboard").submit();
                    });

                    // SHOW HOLD PARCELS
                    $("#hold_parcel").click(function () {
                        $("#form_action").val("hold_parcels");
                        $("#frm_dashboard").submit();
                    });

                    // SHOW HOLD PARCELS
                    $("#delivered_parcel").click(function () {
                        $("#form_action").val("delivered_parcel");
                        $("#frm_dashboard").submit();
                    });

                    // SHOW HOLD PARCELS
                    $("#shipped_parcel").click(function () {
                        $("#form_action").val("shipped_parcel");
                        $("#frm_dashboard").submit();
                    });

                    // SHOW WEIGHT
                    $("#weight").click(function () {
                        $("#form_action").val("weight");
                        $("#frm_dashboard").submit();
                    });

                    // SHOW Consignment Daily
                    $("#Consignment_daily").click(function () {
                        $("#form_action").val("Consignment_daily");
                        $("#frm_dashboard").submit();
                    });

                    // SHOW top_countries
                    $("#top_countries").click(function () {
                        $("#form_action").val("top_countries");
                        $("#frm_dashboard").attr("action", "top_countries_list.php");
                        $("#frm_dashboard").submit();
                    });

                    // SHOW top_services
                    $("#top_services").click(function () {
                        $("#form_action").val("top_services");
                        $("#frm_dashboard").attr("action", "top_services_list.php");
                        $("#frm_dashboard").submit();
                    });

                });
                var initCratTotalConsignment = function () {
                    if ($('#consignment_statistics').size() != 0) {
                        $('#consignment_statistics_loading').hide();
                        $('#consignment_statistics_content').show();

                        var consignment = [
        <?php echo implode(",", $this->total_consignment_chart); ?>
                        ];

                        var plot_statistics = $.plot($("#consignment_statistics"),
                                [{
                                        data: consignment,
                                        lines: {
                                            fill: 0.6,
                                            lineWidth: 0
                                        },
                                        color: ['#f89f9f']
                                    }, {
                                        data: consignment,
                                        points: {
                                            show: true,
                                            fill: true,
                                            radius: 5,
                                            fillColor: "#f89f9f",
                                            lineWidth: 3
                                        },
                                        color: '#fff',
                                        shadowSize: 0
                                    }],
                                {
                                    xaxis: {
                                        tickLength: 0,
                                        tickDecimals: 0,
                                        mode: "categories",
                                        min: 0,
                                        font: {
                                            lineHeight: 14,
                                            style: "normal",
                                            variant: "small-caps",
                                            color: "#6F7B8A"
                                        }
                                    },
                                    yaxis: {
                                        ticks: 5,
                                        tickDecimals: 0,
                                        tickColor: "#eee",
                                        font: {
                                            lineHeight: 14,
                                            style: "normal",
                                            variant: "small-caps",
                                            color: "#6F7B8A"
                                        }
                                    },
                                    grid: {
                                        hoverable: true,
                                        clickable: true,
                                        tickColor: "#eee",
                                        borderColor: "#eee",
                                        borderWidth: 1
                                    }
                                });

                        var previousPoint = null;
                        $("#consignment_statistics").bind("plothover", function (event, pos, item) {
                            $("#x").text(pos.x.toFixed(2));
                            $("#y").text(pos.y.toFixed(2));
                            if (item) {
                                if (previousPoint != item.dataIndex) {
                                    previousPoint = item.dataIndex;
                                    $("#tooltip").remove();
                                    var x = item.datapoint[0].toFixed(2),
                                            y = item.datapoint[1].toFixed(2);
                                    showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' Consignments');
                                }
                            } else {
                                $("#tooltip").remove();
                                previousPoint = null;
                            }
                        });
                    }
                }
                var initCratDailyConsignment = function () {
                    if ($('#daily_consignment_statistics').size() != 0) {
                        $('#daily_consignment_statistics_loading').hide();
                        $('#daily_consignment_statistics_content').show();

                        var daily_consignment = [
        <?php echo implode(",", $this->daily_consignment_chart); ?>
                        ];
                        var daily_plot_statistics = $.plot($("#daily_consignment_statistics"),
                                [{
                                        data: daily_consignment,
                                        lines: {
                                            fill: 0.2,
                                            lineWidth: 0,
                                        },
                                        color: ['#BAD9F5']
                                    }, {
                                        data: daily_consignment,
                                        points: {
                                            show: true,
                                            fill: true,
                                            radius: 4,
                                            fillColor: "#9ACAE6",
                                            lineWidth: 2
                                        },
                                        color: '#9ACAE6',
                                        shadowSize: 1
                                    }, {
                                        data: daily_consignment,
                                        lines: {
                                            show: true,
                                            fill: false,
                                            lineWidth: 3
                                        },
                                        color: '#9ACAE6',
                                        shadowSize: 0
                                    }],
                                {
                                    xaxis: {
                                        tickLength: 0,
                                        tickDecimals: 0,
                                        mode: "categories",
                                        min: 0,
                                        font: {
                                            lineHeight: 18,
                                            style: "normal",
                                            variant: "small-caps",
                                            color: "#6F7B8A"
                                        }
                                    },
                                    yaxis: {
                                        ticks: 5,
                                        tickDecimals: 0,
                                        tickColor: "#eee",
                                        font: {
                                            lineHeight: 14,
                                            style: "normal",
                                            variant: "small-caps",
                                            color: "#6F7B8A"
                                        }
                                    },
                                    grid: {
                                        hoverable: true,
                                        clickable: true,
                                        tickColor: "#eee",
                                        borderColor: "#eee",
                                        borderWidth: 1
                                    }
                                });

                        var previousPoint = null;
                        $("#daily_consignment_statistics").bind("plothover", function (event, pos, item) {
                            $("#x").text(pos.x.toFixed(2));
                            $("#y").text(pos.y.toFixed(2));
                            if (item) {
                                if (previousPoint != item.dataIndex) {
                                    previousPoint = item.dataIndex;
                                    $("#tooltip").remove();
                                    var x = item.datapoint[0].toFixed(2),
                                            y = item.datapoint[1].toFixed(2);
                                    showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' Consignments');
                                }
                            } else {
                                $("#tooltip").remove();
                                previousPoint = null;
                            }
                        });
                    }
                }
                var initChartTopCountries = function () {
                    var chart = AmCharts.makeChart("top_countries_chart", {
                        "type": "pie",
                        "theme": "light",
                        "fontFamily": 'Open Sans',
                        "color": '#888',
                        "dataProvider": [<?php echo implode(",", $this->top_countries_chart); ?>],
                        "valueField": "value",
                        "titleField": "country",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "exportConfig": {
                            menuItems: [{
                                    icon: '/lib/3/images/export.png',
                                    format: 'png'
                                }]
                        }
                    });

                    jQuery('.top_countries_chart_input').off().on('input change', function () {
                        var property = jQuery(this).data('property');
                        var target = chart;
                        var value = Number(this.value);
                        chart.startDuration = 0;

                        if (property == 'innerRadius') {
                            value += "%";
                        }

                        target[property] = value;
                        chart.validateNow();
                    });

                    $('#top_countries_chart').closest('.portlet').find('.fullscreen').click(function () {
                        chart.invalidateSize();
                    });
                }
                //TOTAL SHIPMENTS
                var getTotalShipments = function () {

                    $('#ajax-total-shipment').show();
                    var user_type = "<? echo $_SESSION['menu-option']; ?>";
                    $.post("index.php?menu-option=" + user_type, {func: "GET_TOTAL_SHIPMENTS", user_type: user_type}, function (response_data)
                    {
                        document.getElementById('total_shipment').innerHTML = response_data;
                        $('#ajax-total-shipment').hide();
                    });

                }
                // TOTAL HOLD SHIPMENTS
                var getTotalHoldShipment = function () {
                    $('#ajax-total-hold-shipment').show();
                    var user_type = "<? echo $_SESSION['menu-option']; ?>";
                    $.post("index.php?menu-option=" + user_type, {func: "GET_HOLD_SHIPMENTS", user_type: user_type}, function (response_data)
                    {
                        document.getElementById('total_hold_shipment').innerHTML = response_data;
                        $('#ajax-total-hold-shipment').hide();
                    });

                }
                //TOTAL SHIPPED SHIPMENT
                var getTotalShippedShipment = function () {
                    $('#ajax-total-shipped-shipment').show();
                    var user_type = "<? echo $_SESSION['menu-option']; ?>";
                    $.post("index.php?menu-option=" + user_type, {func: "GET_SHIPPED_SHIPMENTS", user_type: user_type}, function (response_data)
                    {
                        document.getElementById('total_shipped_shipment').innerHTML = response_data;
                        $('#ajax-total-shipped-shipment').hide();
                    });

                }

                //TOTAL DELIVERED SHIPMENT
                var getTotalDeliveredShipment = function () {
                    $('#ajax-total-delivered-shipment').show();
                    var user_type = "<? echo $_SESSION['menu-option']; ?>";
                    $.post("index.php?menu-option=" + user_type, {func: "GET_DELIVERED_SHIPMENTS", user_type: user_type}, function (response_data)
                    {
                        document.getElementById('total_delivered_shipment').innerHTML = response_data;
                        $('#ajax-total-delivered-shipment').hide();
                    });

                }
                var initChartTopServices = function () {
                    var chart = AmCharts.makeChart("top_services_chart", {
                        "type": "pie",
                        "theme": "light",
                        "fontFamily": 'Open Sans',
                        "color": '#888',
                        "dataProvider": [<?php echo implode(",", $this->top_services_chart); ?>],
                        "valueField": "value",
                        "titleField": "service",
                        "outlineAlpha": 0.4,
                        "depth3D": 15,
                        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
                        "angle": 30,
                        "exportConfig": {
                            menuItems: [{
                                    icon: '/lib/3/images/export.png',
                                    format: 'png'
                                }]
                        }
                    });

                    jQuery('.top_services_chart_input').off().on('input change', function () {
                        var property = jQuery(this).data('property');
                        var target = chart;
                        var value = Number(this.value);
                        chart.startDuration = 0;

                        if (property == 'innerRadius') {
                            value += "%";
                        }

                        target[property] = value;
                        chart.validateNow();
                    });

                    $('#top_services_chart').closest('.portlet').find('.fullscreen').click(function () {
                        chart.invalidateSize();
                    });
                }
                function calculateChargeableWeight()
                {
                    var weight = $('#calc_weight').val();
                    var length = $('#calc_length').val();
                    var width = $('#calc_width').val();
                    var height = $('#calc_height').val();
                    $('#_calc_width').val(width);
                    $('#_calc_length').val(length);
                    $('#_calc_height').val(height);
                    var vol_weight = (length * width * height) / 5000;

                    if (vol_weight > weight)
                    {
                        $('#calc_chargeable_weight').val(vol_weight);
                    } else
                    {
                        $('#calc_chargeable_weight').val(weight);
                    }

                }

                function showChartTooltip(x, y, xValue, yValue) {
                    $('<div id="tooltip" class="chart-tooltip">' + yValue + '<\/div>').css({
                        position: 'absolute',
                        display: 'none',
                        top: y - 40,
                        left: x - 40,
                        border: '0px solid #ccc',
                        padding: '2px 6px',
                        'background-color': '#fff'
                    }).appendTo("body").fadeIn(200);
                }
            </script>        
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $userSection = SessionManager::getUser();
        if (!isset($_POST['func']) && $userSection->getDefaultLang() != 'en-GB' && $userSection->getDefaultLang() != '') {
            $lang_selected = util_get("lang");
            $lang_session = trim(@$_SESSION['lang']);
            $lang_default = trim($userSection->getDefaultLang());

            if (trim($lang_session) != '' && $lang_selected != '' && $lang_session != $lang_selected) {
                $_SESSION['lang'] = $lang_selected;
                util_redirect("../main/index.php?lang=" . trim($lang_selected));
            } else if (trim($lang_session) != '') {
                
            } else if (trim($lang_default) != '') {
                $_SESSION['lang'] = $lang_default;
                util_redirect("../main/index.php?lang=" . $lang_default);
            }
        }
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            $data = util_get("data"); //$_SESSION["user_account"];
            if (trim($data) && $data != '')
                util_redirect(SETTING_MAIN_URL . "main/login.php?data=" . $data);
            else
                util_redirect(SETTING_MAIN_URL . "main/login.php");
        }

        $redirectToDb = false;
        if ($userSection->getUserType() != USER::USER_TYPE_CLIENT && !isset($_SESSION['menu-option']))
            $redirectToDb = true;
        else
            $redirectToDb = false;

        if (util_get("menu-option") != '') {
            @$_SESSION['menu-option'] = util_get("menu-option");
        } else if (@$_SESSION['menu-option'] != util_get("menu-option"))
            @$_SESSION['menu-option'] = util_get("menu-option");
        else
            @$_SESSION['menu-option'] = $userSection->getUserType();
        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_TARRIF') {
            $Sessionuser = SessionManager::getUser();
            $calc_shipping_from = $_REQUEST["calc_shipping_from"];
            $calc_shipping_to = $_REQUEST["calc_shipping_to"];
            $calc_weight = $_REQUEST["calc_weight"];
            $calc_currency = $_REQUEST["calc_currency"];
            $calc_length = $_REQUEST["calc_length"];
            $calc_width = $_REQUEST["calc_width"];
            $calc_height = $_REQUEST["calc_height"];


            if ($calc_shipping_from != '' && $calc_shipping_to != '' && $calc_weight != '' && $calc_currency != '') {

                if ($calc_width > 0 && $calc_height > 0 && $calc_length > 0) {
                    $_calc_width = $calc_width;
                    $_calc_height = $calc_height;
                    $_calc_length = $calc_length;
                    $vol_weight = ($calc_width * $calc_height * $calc_length) / 5000;
                    if ($vol_weight > $calc_weight)
                        $chargeable_weight = $vol_weight;
                    else
                        $chargeable_weight = $calc_weight;
                }
                else {
                    $chargeable_weight = $calc_weight;
                }

                $infoarray = array();
                $infoarray['apiKey'] = $Sessionuser->getApiKey();
                $infoarray['apiSecert'] = $Sessionuser->getApiSecert();
                $infoarray['accountNumber'] = $Sessionuser->getUserAccount();
                $infoarray['fromCountryIso'] = $calc_shipping_from;
                $infoarray['toCountryIso'] = $calc_shipping_to;
                $infoarray['serviceCode'] = 'eCommerce Packet Plus';
                $infoarray['Weight'] = $chargeable_weight;
                $infoarray['Peices'] = '1';
                $infoarray['requestedCurrency'] = $calc_currency;
                $infoarray['postcode'] = '';
                $infoarray['platform'] = '';

                print_r($infoarray);
                //$information = "YPS||YPS.COM||YPS||".$calc_shipping_from."||".$calc_shipping_to."||YPS_ROUTING||".$calc_weight."||1";
                $client = new SoapClient(null, array(
                    'location' => "https://oneworldexpress.co.uk/remote/main/tariffapi.php?wsdl",
                    'uri' => "https://oneworldexpress.co.uk/remote/main/tariffapi.php?wsdl"));

                $resultas = $client->__soapCall('GetTariffCodeByProductName', array('information' => implode('||', $infoarray)));

                $response_array = ConvertXMLToArray::XML2Array($resultas);
                print_r($response_array);
                exit;
                $response = $response_array["Source"]["Response"];
                if (sizeof($response) > 0) {
                    $status = $response['Status'];
                    $response = $response["Tariffs"];

                    if ($status == "Success") {
                        echo $tarrif = $response["Tarrif"];
                    } else {
                        echo $tarrif = $response["Detail"];
                    }
                }
            }
            die;
        }
        // Total Consignment
        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_TOTAL_SHIPMENTS') {
            $user_type = $_REQUEST["user_type"];
            $consignmentFilteter = new ConsignmentFilter();
            if ($user_type != User::USER_TYPE_ADMIN)
                $consignmentFilteter->addFilter("user_code = '" . $userSection->getUserCode() . "'");
            //	//$consignmentFilteter->addAccountFilter($userSection->getUserAccount());
            $status_not_include = array('0', '', 'new', 'invalid', 'recycled', 'cancelled');
            $consignmentFilteter->addStatusFilterNotIn($status_not_include);
            // Total Consignment
            $dataTotalConsignment = $consignmentFilteter->getDashboardTotalConsignmentInfo();
            if (count($dataTotalConsignment) > 0) {
                $dataConsignment = $dataTotalConsignment[0];
                $total_consignment = $dataConsignment->getId();
            }
            echo $total_consignment;
            die;
            //   $this->total_weight = $dataConsignment->getWeight();
        }
        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_HOLD_SHIPMENTS') {
            $total_hold_parcels = 0;
            $user_type = $_REQUEST["user_type"];
            $consignmentFilteter = new ConsignmentFilter();
            if ($user_type != User::USER_TYPE_ADMIN)
                $consignmentFilteter->addFilter("user_code = '" . $userSection->getUserCode() . "'");
            //	//$consignmentFilteter->addAccountFilter($userSection->getUserAccount());
            $status_not_include = array('0', '', 'new', 'invalid', 'recycled', 'cancelled');
            $consignmentFilteter->addStatusFilterNotIn($status_not_include);
            // Total Hold Parcels
            $dataHoldValues = $consignmentFilteter->getDashboardTotalHoldConsignment();
            if (count($dataHoldValues) > 0) {
                $dataHold = $dataHoldValues[0];
                $total_hold_parcels = $dataHold->getId();
            }
            echo $total_hold_parcels;
            die;
        }
        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_SHIPPED_SHIPMENTS') {
            $total_shipped_parcels = 0;
            $user_type = $_REQUEST["user_type"];
            $consignmentFilteter = new ConsignmentFilter();
            if ($user_type != User::USER_TYPE_ADMIN)
                $consignmentFilteter->addFilter("user_code = '" . $userSection->getUserCode() . "'");
            //	$consignmentFilteter->addAccountFilter($userSection->getUserAccount());
            $status_not_include = array('0', '', 'new', 'invalid', 'recycled', 'cancelled');
            $consignmentFilteter->addStatusFilterNotIn($status_not_include);
            // Total Shipped Parcels
            $dataShippedValues = $consignmentFilteter->getDashboardTotalShippedConsignment();
            if (count($dataShippedValues) > 0) {
                $dataShipped = $dataShippedValues[0];
                $total_shipped_parcels = $dataShipped->getId();
            }
            echo $total_shipped_parcels;
            die;
        }
        if (isset($_REQUEST['func']) && $_REQUEST['func'] == 'GET_DELIVERED_SHIPMENTS') {
            $total_delivered_parcels = 0;
            $user_type = $_REQUEST["user_type"];
            $consignmentFilteter = new ConsignmentFilter();
            if ($user_type != User::USER_TYPE_ADMIN)
                $consignmentFilteter->addFilter("user_code = '" . $userSection->getUserCode() . "'");
            //	$consignmentFilteter->addAccountFilter($userSection->getUserAccount());
            $status_not_include = array('0', '', 'new', 'invalid', 'recycled', 'cancelled');
            $consignmentFilteter->addStatusFilterNotIn($status_not_include);
            // Total Delivered Parcels
            $dataDeliveredValues = $consignmentFilteter->getDashboardTotalDeliveredConsignment();
            if (count($dataDeliveredValues) > 0) {
                $dataDelivered = $dataDeliveredValues[0];
                $total_delivered_parcels = $dataDelivered->getId();
            }
            echo $total_delivered_parcels;
            die;
        }
        $consignmentFilteter = new ConsignmentFilter();
        //if($userSection->getUserType() != User::USER_TYPE_ADMIN)
        $consignmentFilteter->addFilter("user_code = '" . $userSection->getUserCode() . "'");
        //	$consignmentFilteter->addAccountFilter($userSection->getUserAccount());
        $status_not_include = array('0', '', 'new', 'invalid', 'recycled', 'cancelled');
        $consignmentFilteter->addStatusFilterNotIn($status_not_include);

        $dataValues = $consignmentFilteter->getDashboardReport_A();
        $transit_total = 0;
        $return_total = 0;
        if (count($dataValues) > 0) {
            foreach ($dataValues as $dataList) {
                $consignmentStatus = $dataList->getConsignmentStatus();
                if (trim($consignmentStatus) != '') {
                    @ $this->total_consignment_chart[] = "['" . ucwords(Consignment::$status_array[$consignmentStatus]) . "',   " . $dataList->getId() . "]";
                }
            }
        } else
            $this->total_consignment_chart[] = "['No Shipment Found',  0]";

        // Daily Consignment
        $dataDailyValues = $consignmentFilteter->getDashboardReport_Daily();
        if (count($dataDailyValues) > 0) {
            foreach ($dataDailyValues as $dataDailyList) {
                if (trim($dataDailyList->getConsignmentStatus()) != '') {
                    $consignmentStatus = $dataDailyList->getConsignmentStatus();
                    $this->daily_consignment_chart[] = "['" . ucwords(Consignment::$status_array[$dataDailyList->getConsignmentStatus()]) . "',   " . $dataDailyList->getId() . "]";
                }
            }
        } else
            $this->daily_consignment_chart[] = "['No Shipment Found',  0]";

        // Top Countries
        $dataCountryValues = $consignmentFilteter->getDashboardTopCoutry();
        if (count($dataCountryValues) > 0) {
            foreach ($dataCountryValues as $dataCountryList) {
                if (trim($dataCountryList->getCountry()) != '') {
                    $consignmentCountry = ucwords(strtolower($dataCountryList->getCountry()));
                    $this->top_countries_bar[$consignmentCountry] = $dataCountryList->getId();
                    $this->top_countries_chart[] = '{"country" : "' . $consignmentCountry . '", "value" : ' . $dataCountryList->getId() . '}';
                }
            }
        } else
            $this->top_countries_chart[] = '{"country" : "No Country Found", "value" : 0}';

        // Top Services
        $dataServicesValues = $consignmentFilteter->getDashboardTopServices();
        if (count($dataServicesValues) > 0) {
            foreach ($dataServicesValues as $dataServiceList) {
                if (trim($dataServiceList->getServiceType()) != '') {
                    $consignmentService = $dataServiceList->getServiceType();
                    $this->top_services_bar[ucwords($consignmentService)] = $dataServiceList->getId();
                    $this->top_services_chart[] = '{"service" : "' . ucwords($consignmentService) . '", "value" : ' . $dataServiceList->getId() . '}';
                }
            }
        } else
            $this->top_services_chart[] = '{"service" : "No Country Found", "value" : 0}';








        // Max  Weight
        $dataHeighestConsignment = $consignmentFilteter->getDashboardHighestConsignmentWeight();
        if (count($dataHeighestConsignment) > 0) {
            $dataHeighWeightConsignment = $dataHeighestConsignment[0];
            $this->max_weight = $dataHeighWeightConsignment->getWeight();
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
