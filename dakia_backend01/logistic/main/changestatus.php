<?php
/* echo "This file is temprary block by admin please contact to admin for this 
  <br>;
  ITsupport@oneworldexpress.com
  ";
  die; */
// get settings
require_once("../includes/settings/config.inc.php");
?>
<?php

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $error = array();
    public $message;
    private $status_array = array();
    private $track_point_array = array();
    private $hawbNotInOurSystem;
    private $duplicate_tracking_number = array();

    protected function init() {
        $sessionUser = SessionManager::getUser();
        $userAccount = $sessionUser->getUserAccount();
        t_on(); // turn on trace for this page
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            $this->error = '';
            switch ($this->form_vars["form_action"]) {
                case "save":
                    $trackingNumberArray = array();
                    $count = 0;
                    $barcodelist = @$this->form_vars['barcodelist'];
                    $statusCode = $this->form_vars['status'];
                    $comments = str_replace("'", "", trim($this->form_vars['comments']));
                    $bulkconsignments = preg_replace('`\A[ \t]*\r?\n|\r?\n[ \t]*\Z`', '', $barcodelist);
                    $bulkconsignments = str_replace("\r\n\r\n", "\r\n", trim($bulkconsignments));
                    $bulkconsignments = str_replace("\r\n", ",", trim($bulkconsignments));
                    if (!isset($statusCode) || $statusCode == 'Please Select a Value')
                        $this->error[] = Translation::GetCaption("PLEASE_SELECT_STATUS") . "<br>";
                    if ($comments == '')
                        $this->error[] = Translation::GetCaption("PLEASE_ENTER_COMMENTS") . "<br>";
                    if (!isset($barcodelist) or $barcodelist == '')
                        $this->error[] = Translation::GetCaption("PLEASE_ENTER_TRACKING_NUMBER") . "<br>";
                    if ($this->error != '')
                        return;
                    $bulkconsignment = explode(",", $bulkconsignments);
                    $defaultArraySubit = explode(",", $bulkconsignments);
                    $bulkconsignment = array_unique($bulkconsignment);
                    $NumberofUniqueCode = 0;
                    $trackingNumbers = array();
                    foreach ($bulkconsignment as $res) {
                        if (trim($res) == '')
                            continue;
                        $res = str_replace(' ', '', $res);
                        $res = str_replace('JJD', 'JD', $res);
                        $track_arr = str_split($res);
                        //in case of DPD germany ignore first 8 and last 7 characters
                        if ($track_arr[0] == '%')
                            $res = substr($res, 8, 22 - 8);
                        $trackingNumbers[] = DbAccess3::escape($res);
                    }
                    if (count($trackingNumbers) > 0) {
                        $strTrackingNumbers = implode("','", $trackingNumbers);
                        $con_filter = new ConsignmentFilter();
                        $con_filter->addAwbArrayFilter($strTrackingNumbers);
                        $con_filter->addFilter(" IsInvoiced <> 'Y'");
                        $con_list = $con_filter->getColumnList("awb, date_booked");
                        if (count($con_list) > 0) {
                            $trackDataHoldArr = array();
                            $holdCount = 0;
                            $bookedshipment = array();
                            foreach ($con_list as $con) {
                                if ($statusCode == "unhold") {
                                    if ($con->getDateBooked() == "" || $con->getDateBooked() == "1970-01-01")
                                        $foundConList[] = $con->getAwb();
                                    else
                                        $bookedshipment[] = $con->getAwb();
                                } else
                                    $foundConList[] = $con->getAwb();
                                $trackDataHoldArr[$holdCount]['userid'] = $sessionUser->getId();
                                $trackDataHoldArr[$holdCount]['comments'] = $comments;
                                $trackDataHoldArr[$holdCount]['tracking_number'] = $con->getAwb();
                                $trackDataHoldArr[$holdCount++]['date_created'] = date("Y-m-d H:i:s");
                            }
                            if (count($foundConList) > 0 || count($bookedshipment) > 0) {
                                if ($statusCode != 'missing' && $statusCode != 'damaged') {
                                    if ($statusCode == "unhold") {
                                        if (sizeof($bookedshipment) > 0) {
                                            $strbookedshipment = implode("','", $bookedshipment);
                                            $setColumns = "consignment_status = 'booked', message = '" . DbAccess3::escape($comments) . "'";
                                            /////////////// where condition ////////////////
                                            $where = " awb IN('$strbookedshipment') and awb != ''";
                                            Consignment::bulkUpdate($setColumns, $where);
                                        }
                                        if (sizeof($foundConList) > 0) {
                                            $strFoundConList = implode("','", $foundConList);
                                            $setColumns = "consignment_status = 'received', message = '" . DbAccess3::escape($comments) . "'";
                                            /////////////// where condition ////////////////
                                            $where = " awb IN('$strFoundConList') and awb != ''";
                                            Consignment::bulkUpdate($setColumns, $where);
                                        }
                                        $this->AddLog($bookedshipment, $statusCode);
                                    } else {
                                        $strFoundConList = implode("','", $foundConList);
                                        $setColumns = "consignment_status = '" . DbAccess3::escape($statusCode) . "', message = '" . DbAccess3::escape($comments) . "'";
                                        /////////////// where condition ////////////////
                                        $where = " awb IN('$strFoundConList') and awb != ''";
                                        Consignment::bulkUpdate($setColumns, $where);
                                        $this->AddLog($foundConList, $statusCode);
                                    }
                                    ConsignmentHold::bulkDataInsert($trackDataHoldArr);
                                } else {
                                    ConsignmentHold::bulkDataInsert($trackDataHoldArr);
                                    $this->SendEmail($trackingNumbers, $statusCode);
                                    $this->AddLog($trackDataHoldArr, $statusCode);
                                }
                            } else
                                $this->message = " No Tracking number found.";
                        }
                        if (count($foundConList) > 0)
                            $this->booking_not_exist = array_values(array_diff($trackingNumbers, $foundConList));
                        else
                            $this->booking_not_exist = $trackingNumbers;
                        if (count($defaultArraySubit) > 0) {
                            $duplicate_tracking_number = array_count_values($defaultArraySubit);
                            foreach ($duplicate_tracking_number as $key => $value) {
                                if ($value > 1)
                                    $this->duplicate_tracking_number[] = $key;
                            }
                        }
                    } else
                        $this->message = " No Tracking number found.";
                // end else
            }// end foreach
            if (count($foundConList) > 0)
                $this->message = count($foundConList) + count($bookedshipment) . " UNIQUE ENTRIES HAVE BEEN SAVED SUCCESSFULLY.";
            if (isset($booking_not_exist) && count($booking_not_exist) > 0)
                $this->message .= '<br />' . count($booking_not_exist) . " TRACKING NUMBERS DOES NOT EXISTS IN OUR SYSTEM";
        }// end switch
    }

// end if($this->form_vars["form_action"])
    /*     * *
     * Insert content in to HTML Head section
     */

    private function AddLog($bookedshipment, $statusCode) {
        $sessionUser = SessionManager::getUser();
        foreach ($bookedshipment as $bookAwb) {
            $bookAwb = trim($bookAwb);
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->AddAwbFilter($bookAwb);
            $consignmentFilter->addFieldNotFilter("awb", "");
            $listBooked = $consignmentFilter->getColumnList("id, awb");
            if (count($listBooked) > 0) {
                $con = $listBooked[0];
                $ConsignmentLog = new ConsignmentLog();
                $ConsignmentLog->createlog("Shipment Status change to  " . $statusCode, $con->getId());
            }
        }
    }

    public function SendEmail($trackingNumberArr, $status) {
        $headers = "From: itsupport@oneworldexpress.com \r\n";
        $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $strHoldShipment = implode("<BR>", $trackingNumberArr);
        $holdemailmessage = "Dear All,<BR><BR>Please find the " . ucwords($status) . " shipment(s). <BR><BR>" . $strHoldShipment;
        //$holdemailmessage = "Dear All,<BR><BR>Please find the shipment(s). <BR><BR>" . $strHoldShipment;					
        //mail("ops@oneworldexpress.com, ITSupport@oneworldexpress.com", "$status Shipments", $holdemailmessage, $headers);
        mail("itsupport@oneworldexpress.com", ucwords($status) . " Shipments", $holdemailmessage, $headers);
    }

    protected function renderHead() {
        //$sessionUser = SessionManager::getUser();
        //$userAccount = $sessionUser->getUserAccount();
        ?>
        <script type="text/javascript">
            function form_submit()
            {
                bookingForm.submit();
            }
            $(document).ready(function (e) {
                $('input').tooltip();
                $('select').tooltip();
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <div class="clear" ></div>
        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-bar-chart"></i>
                       
        <?php errorList::getItem()->render(); ?><? echo Translation::GetCaption("HOLD_RELABEL SHIPMENT_STATUS");  ?>               
                    </div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
        <?php
        if ($this->error != '') {
            echo '<div class="alert alert-danger">';
            foreach ($this->error as $err)
                echo $err;
            echo '</div>';
        } else if (trim($this->message) != '') {
            echo '<div class="alert alert-danger">';
            echo $this->message;
            echo '</div></div>';
        }
        ?>
                            <div class="row">
                                <h4><? echo Translation::GetCaption("HOLD_RELABEL HOLD_SHIPMENT"); ?></h4>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label  style="text-transform: capitalize;"><? echo Translation::GetCaption("HOLD_SHIPMENT_STATUS"); ?></label>
                                        <div class="input-group"> 
                                            <select name="status" id="status" class="selectpicker form-control">
                                                <option value="Please Select a Value"><? echo Translation::GetCaption("PLEASE_SELECT_STATUS"); ?></option>
                                                <option value='hold' <?php echo (@$status == 'hold' ? 'selected="selected"' : ''); ?> ><? echo Translation::GetCaption("HOLD"); ?></option>  
                                                <option value='unhold' <?php echo (@$status == 'unhold' ? 'selected="selected"' : ''); ?>><? echo Translation::GetCaption("UNHOLD"); ?></option>       
                                                <option value='missing' <?php echo (@$status == 'missing' ? 'selected="selected"' : ''); ?>><? echo Translation::GetCaption("MISSING_GOODS"); ?></option>
                                                <option value='damaged' <?php echo (@$status == 'damaged' ? 'selected="selected"' : ''); ?>><? echo Translation::GetCaption("DAMAGED"); ?></option>       
                                                <option value='received' <?php echo (@$status == 'received' ? 'selected="selected"' : ''); ?>><? echo Translation::GetCaption("RESOLVE"); ?></option>       
                                            </select>
                                            <span class="input-group-addon">  <input type='button' name='status_tooltip' id='status_tooltip' title="<? echo Translation::GetCaption("CHANGE_SHIPMENT_STATUS"); ?>" class="tooltipbutton"  placeholder="<? echo Translation::GetCaption("CHANGE_SHIPMENT_STATUS"); ?>"  rel="tooltip" value=" " data-trigger="hover" data-placement="right"  /></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label  style="text-transform: capitalize;"><? echo Translation::GetCaption("AWB"); ?></label>
                                        <textarea id="barcodelist" name="barcodelist" class="form-control"  ><?php echo @$barcodelist; ?></textarea>
                                    </div> 
                                    <div class="form-group">
                                        <label  style="text-transform: capitalize;"><? echo Translation::GetCaption("COMMENTS"); ?></label>
                                        <input type="text" id="comments" name="comments" value="<?= @$comments ?>" size="50" style="" class="form-control"  />
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div class="row" style="text-align:center;"> 
                                        <a id="btnSave"   href="#" class="btn blue  btn-outline btn-circle btn_save"><span></span><? echo Translation::GetCaption("SAVE"); ?></a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
        <?php
        //echo count($this->booking_not_exist);
        if (isset($this->booking_not_exist) && count($this->booking_not_exist) > 0) {
            echo '<label style="text-transform: capitalize;">' . Translation::GetCaption("NOT_FOUND") . '</label>';
            foreach ($this->booking_not_exist as $error_hawb)
                echo '<div class="col-md-12">' . $error_hawb . '</div>';
        }
        ?>
                                        </div>
                                        <div class="col-md-6">
        <?php
        //echo count($this->hawbNotInOurSystem);
        if (count($this->duplicate_tracking_number) > 0) {
            echo '<label style="text-transform: capitalize;">' . Translation::GetCaption("DUPLICATE_TRACKING_NUMBERS") . '</label>';
            foreach ($this->duplicate_tracking_number as $duplicate_hawb)
                echo '<div class="col-md-12">' . $duplicate_hawb . '</div>';
        }
        ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
