<?php
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
    public $errormessage;

    protected function init() {


        $sessionUser = SessionManager::getUser();
        $userAccount = $sessionUser->getUserAccount();
        t_on(); // turn on trace for this page
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {


            $this->error = '';
            switch ($this->form_vars["form_action"]) {
                case "save":

                    $referencenumber = $this->form_vars["reference"];
                    $trackpoint = $this->form_vars["tracking"];

                    if ($trackpoint == "Other") {
                        $trackpoint = $this->form_vars["trackingstatus"];
                    }

                    if ($referencenumber != '') {
                        $c_filter = new ConsignmentFilter();
                        $c_filter->addOweDispatchNumber($referencenumber);
                        $list = $c_filter->getColumnListLimit('awb');
                        if (count($list) > 0) {
                            $trackDataArr = array();
                            $NumberofUniqueCode = 0;
                            foreach ($list as $con_res) {
                                $con_id = $con_res->getID();
                                $trackDataArr[$NumberofUniqueCode]['consignment_id'] = $con_id;
                                $trackDataArr[$NumberofUniqueCode]['tracking_number'] = $con_res->getAwb();
                                $trackDataArr[$NumberofUniqueCode]['status_code'] = $trackpoint;
                                $trackDataArr[$NumberofUniqueCode]['description'] = $trackpoint;
                                $trackDataArr [$NumberofUniqueCode]['track_point'] = $trackpoint;
                                $trackDataArr[$NumberofUniqueCode]['date_created'] = date("Y-m-d H:i:s");
                                $trackDataArr [$NumberofUniqueCode]['mawb'] = '';
                                $trackDataArr[$NumberofUniqueCode]['account'] = "ONEWORLD";
                                $NumberofUniqueCode++;
                            }

                            if (count($trackDataArr) > 0) {
                                //	echo "Count :" . count($trackDataArr);
                                $trackingDataFilter = new TrackingDataFilter();
                                $trackingDataFilter->bulkDataInsert($trackDataArr);
                                $this->form_vars["reference"] = "";
                                $this->message = "Tracking Added Successfully for " . count($trackDataArr) . " Number of Shipments.";
                            }
                        } else {
                            $this->error = "Could not find any record from this Reference Number.";
                        }
                    }



                    break;
            }// end switch
        }// end if($this->form_vars["form_action"])
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        $sessionUser = SessionManager::getUser();
        $userAccount = $sessionUser->getUserAccount();
        ?>       
        <script type="text/javascript">
            /*function form_submit()
             {
             bookingForm.submit();
             }*/

            $(document).ready(function ()
            {
                $('#tracking').change(function () {
                    if ($('#tracking').val() == 'Other')
                    {
                        $('#trackingstatus').show();
                        // $('trackingstatus').css('display', 'block'); 
                    } else
                    {
                        $('#trackingstatus').hide();
                        // $('trackingstatus').css('display', 'none');
                    }
                });

                $("#btnSave").click(function ()
                {

                    $("#form_action").val("save");
                    $("#adminForm").submit();

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

        <div class="clear" ></div>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-book-open"></i>
                        LINEHAUL TRACKING</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <div style="font-size:16px; font-weight:bold; color:#FF0000">
        <?php
        if (sizeof($this->error) > 0)
            echo $this->error;
        ?></div>
                    <div style="font-size:16px; font-weight:bold; color:#000099">
        <?php
        if (sizeof($this->message) > 0)
            echo $this->message;
        ?>
                    </div>
                    <div style="font-size:16px; font-weight:bold; color:red">

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>REFERENCE NUMBER</label>
                            <input  id='reference' type="text" name='reference'   class="form-control" value="<?php echo @$this->form_vars["reference"] ?>" />
                        </div>
                        <div class="col-md-4">
                            <label>TRACKING POINT</label>
                            <select id="tracking"  name="tracking" class="select_dropdown form-control"  style ="">
                                <option value="">Select Tracking Point</option>
                                <option value="Departed LHR">Departed LHR</option>
                                <option value="Arrived at Destination HUB">Arrived at Destination HUB</option>
                                <option value="Delayed by 24 Hours">Delayed by 24 Hours</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" id="trackingstatus" name='trackingstatus'  style=" display:none;" class="form_field_coll1" />
                        </div>
                    </div>
                    <div class="row" style="text-align:center;"> 
                        <br /><br />
                        <a id="btnSave" href="#" class="btn btn-primary btn_save" style=""><span></span>Save</a>  

                        <br /><br />
                    </div>
                </div>

            </div>

        </div>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />        
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

