<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'apitestpanel.class',
    'country.class',
    'countryfilter.class'
]);

class Page extends BasePage {

    protected function init() {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'API Testing Panel'
        );
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <style>
            .select2 {
                width: 100% !important;
            }

            a.disabled {
                pointer-events: none;
            }

            .tablewrap {border-collapse:collapse; table-layout:fixed;  }
            .tablewrap td {  word-wrap:break-word;}
        </style>
        <?php
    }

    public function addPagelavelJs() {
        $sessionUser = SessionManager::getUser();
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
        type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
        type="text/javascript"></script>

        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $testApi = new ApiTestPanel();
        $selectedHandling = [];
        $handlingCodes = $testApi->getActiveHandlingCodes();
        $results = [];
        $site = '';
        $requests = ['accessToken' => 'Access Token Request',
            'addShipment' => 'Add Shipment ',
            'getLabel' => 'Get Label',
            'getTracking' => 'Get Tracking',
            'voidLabels' => 'Get Void Labels'
        ];
        $weight = 2.3;
        $length = 10;
        $width = 10;
        $height = 10;
        $client_id = "";
        $client_secret = "";
        $order_reference = "";
        $tracking_number = "";
        $receiver_iso = "";
        $receiver_postcode = "";
        $validateAddShipment = false;
        $validateTracking = false;
        $createdShipmentViaApi = false;
        $countryFilter = new CountryFilter();
        $countryFilterData = $countryFilter->getColumnList('iso,name');
        if (isset($_POST['apiBtn'])) {
            $site = $this->form_vars['site'];
            $selectedHandling = $this->form_vars['handling_codes'];
            $weight = $this->form_vars['weight'];
            $length = $this->form_vars['length'];
            $width = $this->form_vars['width'];
            $height = $this->form_vars['height'];
            $client_id = trim($this->form_vars['client_id']);
            $client_secret = trim($this->form_vars['client_secret']);
            $receiver_iso = trim($this->form_vars['receiver_iso']);
            $receiver_postcode = trim($this->form_vars['receiver_postcode']);
            $tracking_number = trim($this->form_vars['tracking_number']);
            $order_reference = trim($this->form_vars['order_reference']);
            $manualSiteUrl = rtrim(trim($this->form_vars['manual_site_url']),"/");
 
            $testApi->setWeightDimensions($weight, $length, $width, $height);
            $testApi->setApiCredentials($site, $client_id, $client_secret, $receiver_iso, $receiver_postcode,$manualSiteUrl);

            /////////////////ACCESS TOKEN REQUEST/////////////////
            $results['Token']['accessToken'] = $testApi->accessToken();
            if ($results['Token']['accessToken']['apiStatus'] == 'success') {
                $accessToken = json_decode($results['Token']['accessToken']['response']);
                if (isset($accessToken->access_token) && $accessToken->access_token != '') {
                    $results['Token']['accessToken']['methodStatus'] = "success";
                    $testApi->setAuthorizationToken($accessToken->access_token);


                    if (count($selectedHandling) > 0) {
                        foreach ($selectedHandling as $handling) {
                            $testApi->setSelectedHandlingCodes($handling);


                            /////////////////ADD SHIPMENT REQUEST/////////////////
                            if ($tracking_number == "") {
                                if ($order_reference == "") {
                                    $results[$handling]['addShipment'] = $testApi->addShipment();
                                    $addShipmentResponse = json_decode($results[$handling]['addShipment']['response']);
                                    $results[$handling]['addShipment']['methodStatus'] = $addShipmentResponse->status;
                                    if ($results[$handling]['addShipment']['methodStatus'] == 'success') {
                                        
                                    }
                                    $addShipmentResponseDetail = $addShipmentResponse->_embedded->shipmentResponse[0];
                                    if (isset($addShipmentResponseDetail->order_reference) && $addShipmentResponseDetail->order_reference != "") {
                                        $validateAddShipment = true; //shipment created
                                        $createdShipmentViaApi = true;
                                        $testApi->setOrderReference($addShipmentResponseDetail->order_reference);
                                    }
                                } else if ($order_reference != "") {
                                    $validateAddShipment = true; //used previous shipment id
                                    $testApi->setOrderReference($order_reference);
                                }
                            }

                            ///////////////// GET LABEL REQUEST/////////////////
                            if ($validateAddShipment) {

                                $results[$handling]['getLabel'] = $testApi->getLabel();
                                $getLabelResponse = json_decode($results[$handling]['getLabel']['response']);
                                $results[$handling]['getLabel']['methodStatus'] = $getLabelResponse->status;
                                $getLabelResponseDetail = $getLabelResponse->_embedded->labelResponse;

                                if (isset($getLabelResponseDetail->tracking_number[0])) {
                                    $validateTracking = true;
                                    $testApi->setTrackingNumber($getLabelResponseDetail->tracking_number[0]);
                                }
                            } else if ($tracking_number != "") {
                                $validateTracking = true; //used previous tracking number
                                $testApi->setTrackingNumber($tracking_number);
                            }



                            if ($validateTracking) {
                                ///////////////// GET TRACKING REQUEST/////////////////
                                $results[$handling]['getTracking'] = $testApi->getTracking();
                                $getTrackingResponse = json_decode($results[$handling]['getTracking']['response']);
                                $results[$handling]['getTracking']['methodStatus'] = $getTrackingResponse->status;
                            }


                            /////////////////GET VOID LABEL REQUEST/////////////////
                            if ($createdShipmentViaApi) {
                                $results[$handling]['voidLabels'] = $testApi->getVoidLabels();
                                $getvoidLabelsResponse = json_decode($results[$handling]['voidLabels']['response']);
                                $results[$handling]['voidLabels']['methodStatus'] = $getvoidLabelsResponse->status;
                            }
                        }//end foreach handling
                    }//check selected
                } else {
                    $results['accessToken']['methodStatus'] = "error";
                }
            }
        }
        ?>

        <div class="portlet light">
            <form name="adminForm" id="adminForm" action="" method="POST" enctype="multipart/form-data" autocomplete="off"  >  

                <div class="portlet-title">
                    <div class="caption label_new"><i class="fa fa-list"></i>
                        API Test Panel
                    </div>
                    <div class="actions">
        <!--                    <a href="customers.php" class="btn blue"><span></span><i class="fa fa-users"></i>&nbsp;List Accounts</a>-->
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <?php
                        $this->flashMsg->display();
                        ?>
                        <div class="col-md-12">
                            <div class="alert alert-danger display-none" id="response_message_alert"></div>
                        </div>
                    </div>
                    <div class="row">


                        <?php
                        if (count($handlingCodes) > 0) {
                            foreach ($handlingCodes as $hcode) {
                                $checked = false;
                                if (count($selectedHandling) > 0) {
                                    if (in_array($hcode['service_code'], $selectedHandling)) {
                                        $checked = true;
                                    }
                                }
                                ?>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label class="label-account">
                                                    <input id="match" name="handling_codes[]" type="checkbox" class="form-filter icheck" data-checkbox="icheckbox_flat-green" value="<?php echo $hcode['service_code']; ?>"  <?php if ($checked) { ?> checked="checked"<?php } ?>/>
                                                    <?php echo $hcode['service_code'] . " - " . $hcode['service_name']; ?> [<?php echo $hcode['sender_iso'] . '-' . $hcode['receiver_iso']; ?>]
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                            }
                        }
                        ?>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="font-red-thunderbird"><br>(if empty login credentials <b>owecorp or tahirfinance</b> account credentials will used)</div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-4">

                            <div class="form-group">
                                <label class="label-account label_new ">Testing Site </label>
                                <select id="site" name="site" class="form-control">
                                    <option value="">Manual Site URL</option>
                                    <option value="sandbox" <?php if ($site == "sandbox") { ?> selected="selected" <?php } ?>>http://sandbox.smarttrack.co</option>
                                    <option value="dev" <?php if ($site == "dev") { ?> selected="selected" <?php } ?>>http://developer.smarttrack.co</option>
                                     <option value="local" <?php if ($site == "local") { ?> selected="selected" <?php } ?>>http://local.oneworldexpress.co.uk</option>
                                    <option value="1" <?php if ($site == "1") { ?> selected="selected" <?php } ?>>http://www.smarttrack.co</option>
                                    <option value="2" <?php if ($site == "2") { ?> selected="selected" <?php } ?>>http://beta.smarttrack.co</option>
                                    <option value="3" <?php if ($site == "3") { ?> selected="selected" <?php } ?>>http://staging.smarttrack.co</option>
                                    <option value="4" <?php if ($site == "4") { ?> selected="selected" <?php } ?>>http://test.smarttrack.co</option>
                                </select>
                            </div>       

                             <div class="form-group">
                                <label class="label-account label_new">Manual Site Link </label>
                                <input name="manual_site_url" id="manual_site_url"  class="form-control form-filter"  value="<?php echo $manualSiteUrl; ?>"/>
                            </div> 
                            
                            <div class="form-group">
                                <label class="label-account label_new">Client ID </label>
                                <input name="client_id" id="client_id"  class="form-control form-filter"  value="<?php echo $client_id; ?>"/>
                            </div> 

                            <div class="form-group">
                                <label class="label-account label_new">Client Secret</label>
                                <input name="client_secret" id="client_secret"  class="form-control form-filter"  value="<?php echo $client_secret; ?>"/>
                            </div>    
                        </div>



                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="label-account label_new ">Receiver ISO</label>
                                <select id="receiver_iso" name="receiver_iso" class="form-control">
                                    <option value="">Default</option>
                                    <?php if (count($countryFilterData) > 0) {
                                        foreach ($countryFilterData as $iso) {
                                            ?>
                                            <option value="<?php echo $iso->getIso(); ?>" <?php if ($receiver_iso == $iso->getIso()) { ?> selected="selected" <?php } ?>> <?php echo $iso->getName() . ' -  ' . $iso->getIso(); ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>    

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="label-account label_new" >Weight</label>
                                        <input name="weight" id="weight"  class="form-control form-filter"  value="<?php echo $weight; ?>"/>
                                    </div>       
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="label-account label_new">Length</label>
                                        <input name="length" id="length"  class="form-control form-filter"  value="<?php echo $length; ?>"/>
                                    </div>       
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="label-account label_new">Width</label>
                                        <input name="width" id="width"  class="form-control form-filter"  value="<?php echo $width; ?>"/>
                                    </div>       
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="label-account label_new">Height</label>
                                        <input name="height" id="height"  class="form-control form-filter"  value="<?php echo $height; ?>"/>
                                    </div>       
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <div class="row">
                                 <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="label-account label_new">Order Reference </label>
                                <input name="order_reference" id="order_reference"  class="form-control form-filter"  value="<?php echo $order_reference; ?>"/>
                                <br> (Will not create shipment only test label and tracking)
                            </div>       
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="label-account label_new">Tracking Number </label>
                                <input name="tracking_number" id="tracking_number"  class="form-control form-filter"  value="<?php echo $tracking_number; ?>"/>
                                <br> (If not empty check tracking request only)
                            </div>       
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="label-account label_new">PostCode</label>
                                <input name="receiver_postcode" id="receiver_postcode"  class="form-control form-filter"  value="<?php echo $receiver_postcode; ?>"/>
                            </div>       
                        </div>

                    </div>
                            </div>
                        </div>
                    </div>


                   



                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit"  name="apiBtn"  id="apiBtn" class="btn btn-primary">Test API</button>       
                            </div>       
                        </div>
                    </div>

                </div>
                <input type="hidden" name="form_action" id="form_action" value="" />
            </form>
        </div>




        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-purple-plum">API TEST RESULTS</div>
            </div>
            <div class="portlet-body">


                <div>




                    <?php
                    //print_r($results); 
                    if (count($results) > 0) {
                        ?>



            <?php foreach ($results as $service => $rdata) { ?>
                            <div><h3><b><?php echo $service . " - " . $handlingCodes[$service]['carrier']; ?></b></h3></div>
                            <table class="table table-bordered table-hover tablewrap">

                                <thead>
                                    <tr>

                                        <th class="col-md-2" >API Method and URL</th>
                                        <th class="col-md-1" >Status</th>
                <!--                                        <th class="col-md-1" >Json Decode</th>-->
                                        <th class="col-md-3">Data Sent</th>
                                        <th class="col-md-2">Original Response</th>
                                        <th class="col-md-4">Filtered Response (removeBomUtf8)</th>
                                    </tr>
                                </thead>
                                <tbody>
                <?php foreach ($rdata as $key => $data) { ?>
                                        <tr>

                                            <td><b><?php echo $requests[$key]; ?></b><br><br> <?php echo "<b>URL: </b>" . $data['apiurl']; ?></td>




                                            <td> 
                                                <b>Request Status</b><br>
                                                <?php if ($data['apiStatus'] == 'success') { ?>
                                                    <i class="font-green-jungle font-lg fa fa-check"><?php echo $data['apiStatus']; ?></i>
                                                <?php } else { ?>
                                                    <i class="font-red-thunderbird font-lg fa fa-times"><?php echo $data['apiStatus']; ?></i>
                    <?php } ?>

                                                <br><br><b>Response Status</b><br>
                                                <?php if ($data['methodStatus'] == 'success') { ?>
                                                    <i class="font-green-jungle font-lg fa fa-check"><?php echo $data['methodStatus']; ?></i>
                                                <?php } else { ?>
                                                    <i class="font-red-thunderbird font-lg fa fa-times"><?php echo $data['methodStatus']; ?></i>
                    <?php } ?>


                                                <br><br><b>Json Decoding</b><br>
                                                <?php if ($data['jsonDecodeError'] == ' - No errors') { ?>
                                                    <i class="font-green-jungle font-lg fa fa-check">No Error</i>
                                                <?php } else { ?>
                                                    <i class="font-red-thunderbird font-lg fa fa-times"><?php echo $data['jsonDecodeError']; ?></i>
                    <?php } ?>     

                                            </td>
                    <!--                                            <td><?php // echo $data['jsonDecodeError'];  ?></td>-->
                                            <td><pre><?php if ($service != 'Token') {
                        if (is_array($data['dataSent'])) {
                            print_r($data['dataSent']);
                        } else {
                            echo $data['dataSent'];
                        }
                    } ?></pre></td>

                                            <td><?php
                                                    //echo $data['originalResponse'];

                                                    echo (strlen($data['originalResponse']) > 300) ? substr($data['originalResponse'], 0, 300) . "..." : $data['originalResponse'];
                                                    ?></td>
                                            <td>

                                                <div class="scroller" style="height:400px" data-rail-visible="1" data-rail-color="blue" data-handle-color="#a1b2bd">
                    <?php
                    echo "<pre>" . $data['response'] . "</pre>";
                    ?>

                                                </div>
                                            </td>

                                        </tr>

                            <?php } ?>
                                </tbody> 

                            </table>
                <?php
            }
            ?>

        <?php } ?>


                </div>
            </div>
        </div>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>