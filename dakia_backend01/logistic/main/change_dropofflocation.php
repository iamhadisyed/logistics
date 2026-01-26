<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../includes/labels/ups.class.php");
require_once("../includes/labels/pudo.class.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $consignmentId = "";
    private $user = "";
    private $serviceId = "";
    private $serviceValue = "";
    private $carrierValue = "";
    private $carrierLogo = "";
    private $carrierName = "";
    private $location_array = array();
    private $consignment = "";
    
    
    protected function init() {

//        if(!Permissions::checkFilePermission('add_ranges.php')) 
//                    util_redirect ("index.php");
        
        $this->consignmentId = base64_decode(util_get("id"));
         if($this->consignmentId > 0)
        {
            $this->consignment = new Consignment($this->consignmentId);
            $this->serviceId = $this->consignment->getServiceId();
            $this->user = $this->consignment->getUserId();
        }
        
        
        if(!empty($this->serviceId) && $this->serviceId > 0){
            $this->serviceValue = new services($this->serviceId);
            $carrierId = $this->serviceValue->getCarrierId();
            $this->carrierValue = new Carrier($carrierId);
            $this->carrierLogo = $this->carrierValue->getLogo();
            $this->carrierName = $this->carrierValue->getCarrier();
        }
        // common initialisation for ths page
        $this->setTitle("Change Drop off Location");
       
        if($this->consignmentId > 0)
        {
           
            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->consignment->getUserId());
            $dropoffFilter->addFieldFilter("service_id", $this->serviceId);
            $dropoffFilter->addFieldFilter("companyname", $this->consignment->getSenderCompany()); 
            $dropoffList = $dropoffFilter->getList();
        
            if(count($dropoffList) > 0)
            {
                $dlist = $dropoffList[0];
                $locationarray = array('companyname' => $dlist->getCompanyName(), 
                                        'addressline1' => $dlist->getAddressLine1(), 
                                        'addressline2' => $dlist->getAddressLine2(), 
                                        'addressline3' => $dlist->getAddressLine3(), 
                                        'city' => $dlist->getCity(), 
                                        'postcode' => $dlist->getPostcode(), 
                                        'country' => $dlist->getCountry(), 
                                        'telephone' => $dlist->getTelephone(), 
                                        'lat' => $dlist->getLat(), 
                                        'lng' => $dlist->getLng(), 
                                        'Mon' => $dlist->getMon(),
                                        'Tue' => $dlist->getTue(),
                                        'Wed' => $dlist->getWed(),
                                        'Thu' => $dlist->getThu(),
                                        'Fri' => $dlist->getFri(),
                                        'Sat' => $dlist->getSat(),
                                        'Sun' => $dlist->getSun(),
                                        'consignmentid' => $this->consignmentId,
                                        'index' => 0);
                $this->location_array = json_encode($locationarray);
               
            }
        }
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {
            
           
            $output = [];
            
            $consignmentid = $this->form_vars["consignmentid"];
            $postcode = str_replace(" ", "", $this->form_vars["postcode"]);
            $upslabel = new UPS();
            $dropofflocationresponse  = $upslabel->getDropOffLocation($postcode);
         
            if (sizeof($dropofflocationresponse) > 0) {
                $index = 1;
                foreach($dropofflocationresponse as $dropoff)
                $location[] = array('companyname' => $dropoff['companyname'], 
                                    'addressline1' => $dropoff['addressline1'], 
                                    'addressline2' => $dropoff['addressline2'], 
                                    'addressline3' => $dropoff['addressline3'], 
                                    'city' => $dropoff['city'], 
                                    'postcode' => $dropoff['postcode'], 
                                    'country' => $dropoff['country'], 
                                    'telephone' => $dropoff['telephone'], 
                                    'lat' => $dropoff['lat'], 
                                    'lng' => $dropoff['lng'], 
                                    'Mon' => $dropoff['Mon'],
                                    'Tue' => $dropoff['Tue'],
                                    'Wed' => $dropoff['Wed'],
                                    'Thu' => $dropoff['Thu'],
                                    'Fri' => $dropoff['Fri'],
                                    'Sat' => $dropoff['Sat'],
                                    'Sun' => $dropoff['Sun'],
                                    'consignmentid' => $consignmentid,
                                    'index' => $index);
                $index++;
                
            }

            echo json_encode($location);
            die;
        }
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "DROPOFF_SELECTED_SERVICE") {
             $companyname = $this->form_vars["companyname"];
             $addressline1 = $this->form_vars["addressline1"];
             $addressline2 = $this->form_vars["addressline2"];
             $addressline3 = $this->form_vars["addressline3"];
             $city = $this->form_vars["city"];
             $postcode = $this->form_vars["postcode"];
             $country = $this->form_vars["country"];
             $telephone = $this->form_vars["telephone"];
             $mon = $this->form_vars["mon"];
             $tue = $this->form_vars["tue"];
             $wed = $this->form_vars["wed"];
             $thu = $this->form_vars["thu"];
             $fri = $this->form_vars["fri"];
             $sat = $this->form_vars["sat"];
             $sun = $this->form_vars["sun"];
             $carrierlogo = $this->form_vars["carrierLogo"];
             $carriername = $this->form_vars["carrierName"];
             $lat = $this->form_vars["lat"];
             $lng = $this->form_vars["lng"];
             $serviceId = $this->form_vars["serviceId"];
             $consignmentid = $this->form_vars["consignmentid"];
             $consignment = new Consignment($consignmentid);
             
           
             
             
              $output .= '<div class="row">
                            <h4><span id="rangeName"></span><img src="../images/carrierlogo/thumbnail/owe_100_'.$carrierlogo.'" /> '.$carriername  .'</h4>
                            <div class="col-md-12">';
              $output .= '<label><h3><b>'.$companyname.'</b></h3></label><br />';
              $output .= '<label>'.$addressline1.'</label><br />';
              $output .= '<label>'.$addressline2.'</label><br />';
              $output .= '<label>'.$addressline3.'</label><br />';
              $output .= '<label>'.$city.'</label><br />';
              $output .= '<label>'.$postcode.'</label><br />';
              $output .= '<label>'.$country.'</label><br />';
              $output .= '<label>'.$telephone.'</label><br />';
              $output .= '</div><div class="col-md-12">';
              $output .= '<label><h3><b>Opening Hours</b></h3></label></div>';
              $output .= '<div  class="col-md-2">Mon</div><div  class="col-md-10">'.$mon.'</div>';
              $output .= '<div  class="col-md-2">Tue</div><div  class="col-md-10">'.$tue.'</div>';
              $output .= '<div  class="col-md-2">Wed</div><div  class="col-md-10">'.$wed.'</div>';
              $output .= '<div  class="col-md-2">Thu</div><div  class="col-md-10">'.$thu.'</div>';
              $output .= '<div  class="col-md-2">Fri</div><div  class="col-md-10">'.$fri.'</div>';
              $output .= '<div  class="col-md-2">Sat</div><div  class="col-md-10">'.$sat.'</div>';
              $output .= '<div  class="col-md-2">Sun</div><div  class="col-md-10">'.$sun.'</div>';
             
              
              $output .= '</div><div style="clear:both"><br /></div><div class="col-md-6">'
                      . '<a   id="btnSendParcel"'
                      . ' data-cid="' . $consignmentid . '" data-companyname="' . $companyname . '"'
                      . ' data-addressline1="' . $addressline1 . '" data-addressline2="' . $addressline2 . '" '
                      . ' data-addressline3="' . $addressline3 . '" data-city="' . $city . '" data-postcode="' . $postcode . '" '
                      . ' data-country="' . $country . '" data-telephone="' . $telephone . '"  class="btn btn-sm blue">'
                      . '<span></span>&nbspChange Address</a></div>';
              $output .=      '</div>';
            echo $output;
            exit;
         }
         else if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "UPDATE_ADDRESS") {
           
            $output = array();
            $companyname = $this->form_vars["companyname"];            
            $addressline1 = $this->form_vars["addressline1"];
            $addressline2 = $this->form_vars["addressline2"];
            $addressline3 = $this->form_vars["addressline3"];
            $city = $this->form_vars["city"];
            $postcode = $this->form_vars["postcode"];
            $country = $this->form_vars["country"];
            $telephone = $this->form_vars["telephone"];
            $consignmentid = $this->form_vars["consignmentId"];
            $consignment = new Consignment($consignmentid);
            if($consignmentid > 0)
            {
                $sendercompany = trim($consignment->getSenderCompany());
                if($sendercompany == $companyname)
                {
                   $output["STATUS"] = "ERROR" ;
                   $output["MESSAGE"] = "Selected drop off location is same as current location. Please choose different location.";
                }
                else
                {
                
                $consignment->setSenderCompany($companyname);
                $consignment->setSenderAddressLine1($addressline1);
                $consignment->setSenderAddressLine2($addressline2);
                $consignment->setSenderAddressLine3($addressline3);
                $consignment->setSenderCity($city);
                $consignment->setSenderPostCode($postcode);
                $consignment->setSenderTelephone($telephone);
                $consignment->setShipmentStatus(Consignment::STATUS_HOLD);
                $consignment->setMessage("Please relabel shipment. Its Drop off location change.");
                $consignment->save();
                
                $selectedLocation = "<h4>Your Selected Drop Off Location:</h4><br />
                                    <label><b>".$companyname."</b></label><br />
                                    <label>".$addressline1."</label><br />
                                    <label>".$addressline2."</label><br />
                                    <label>".$addressline3."</label><br />
                                    <label>".$city."</label><br />
                                    <label>".$postcode."</label><br />
                                    <label>Tel: ".$telephone."</label><br /><br />";
                
                
                $dropoffFilter = new DropoffUserLocationFilter();
                $dropoffFilter->addFieldFilter("user_id", $consignment->getUserId());
                $dropoffFilter->addFieldFilter("service_id", $serviceId);
                $dropoffFilter->addFieldFilter("companyname", $companyname);
                $dropoffList = $dropoffFilter->getList();
                if(count($dropoffList) == 0)
                {

                    $dropUserLocation = new DropoffUserLocation();
                    $dropUserLocation->setUserId($consignment->getUserId());
                    $dropUserLocation->setServiceId($serviceId);
                    $dropUserLocation->setCompanyname($companyname);
                    $dropUserLocation->setAddressLine1($addressline1);
                    $dropUserLocation->setAddressLine2($addressline2);
                    $dropUserLocation->setAddressLine3($addressline3);
                    $dropUserLocation->setCity($city);
                    $dropUserLocation->setPostcode($postcode);
                    $dropUserLocation->setCountry($country);
                    $dropUserLocation->setTelephone($telephone);
                    $dropUserLocation->setMon($mon);
                    $dropUserLocation->setTue($tue);
                    $dropUserLocation->setWed($wed);
                    $dropUserLocation->setThu($thu);
                    $dropUserLocation->setFri($fri);
                    $dropUserLocation->setSat($sat);
                    $dropUserLocation->setSun($sun);
                    $dropUserLocation->setDateCreated(strtotime(date('Y-m-d H:i:s')));
                    $dropUserLocation->setLat($lat);
                    $dropUserLocation->setLng($lng);
                    $dropUserLocation->save();
                }
                else {
                    foreach($dropoffList as $droppoint)
                    {
                        $droppoint->setDateCreated(strtotime(date('Y-m-d H:i:s')));
                        $droppoint->save();
                    }
                 }
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = $selectedLocation;
                $pudo = new PUDO();
                $pudo->sendCustomerEmail($consignment, "1");
                }
            }
            echo json_encode($output);
            exit;
                    
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        
    }

    protected function addPagelavelCss() {
        
        ?>
       
        <style>
            #map{
                width:100%;
                height:600px;
            }
            
            .page-header.navbar .page-logo .logo-default {
                margin: 17px 10px 0 !important;
                max-width: 180px !important;
                max-height: 43px !important;
            }

            .page-sidebar-hide {
                margin-left: 0px !important;;
                padding-left: 0px !important;
                margin-top: 0px !important;
            }

            .dashboard-stat2 h3 {
                font-size: 24px !important;
            }
        </style>


        <?php
    }

    public function addPagelavelJs() {
        ?>

        <script src="../assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-knob-dials.min.js" type="text/javascript"></script>
         <link rel="stylesheet" href="../assets/layouts/layout4/css/custom.css">

           <?php
        if (!isset($_SESSION['admin'])) {
            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $(".page-content-wrapper > .page-content").addClass('page-sidebar-hide');
                    $(".sidebar-toggler").hide();
                })
            </script>
        <?php
        }

    }

    protected function renderFooter() {
        ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1w02sqHWTNNRIf_RCJ4r1lymBQxHMPx0&region=IN">
        </script>
        <script>
            var InforObj = [];
            var locations = '';
            var map;
            var previousmarker = null;
            var currentmarker = null;
            
            function initMap()
            {
                map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 10,
                            center: {lat: 51.5, lng: -0.41}
                });
                
            }
            
            //Add marker on map
            function initMarker(locations)
            {
                $("#dropoff-content-display").html('');
                var iconBase = '../images/carrierlogo/thumbnail/owe_16_<?php echo $this->carrierLogo;?>';
                var hoverIconBase = '../images/carrierlogo/thumbnail/owe_50_<?php echo $this->carrierLogo;?>';
                var contentString = '<div id="content"><h5>'+locations.companyname+'</h5><br /> '+locations.addressline1+'<br /> '+locations.addressline2+'<br /> '+locations.city+'<br /> '+locations.postcode+'</div>';
                            
                //Add Marker on map
               const marker = new google.maps.Marker({
                   position: {lat: parseFloat(locations.lat), lng: parseFloat(locations.lng)},
                   icon: iconBase,
                   map: map,
                   animation: google.maps.Animation.DROP
                   //zIndex: parseFloat(locations[i].index)
               });

               const infowindow = new google.maps.InfoWindow({
                   content: contentString,
                   maxWidth: 200
               });

                marker.addListener('mouseover', function () {
                   closeOtherInfo();
                   infowindow.open(marker.get('map'), marker);
                   InforObj[0] = infowindow;
               });
               marker.addListener('mouseout', function () {
                   closeOtherInfo();
                   infowindow.close();
                   InforObj[0] = infowindow;
               });
               
               

                // Add info window to marker    
               google.maps.event.addListener(marker, 'click', (function() {
                   return function() {
                       if(previousmarker !== null)
                       {
                           previousmarker.setAnimation(null);
                           previousmarker.setIcon(iconBase);
                       }
                       previousmarker = marker;
                       map.setZoom(12);
                       map.setCenter(marker.getPosition());
                       marker.setIcon(null); 
                       marker.setIcon(hoverIconBase); 
                                toggleBounce(marker);
                       var url = "change_dropofflocation.php";
                       var action = "DROPOFF_SELECTED_SERVICE";
                       var companyname  = locations.companyname;
                       var addressline1  = locations.addressline1;
                       var addressline2  = locations.addressline2;
                       var addressline3  = locations.addressline3;
                       var city  = locations.city;
                       var postcode  = locations.postcode;
                       var country  = locations.country;
                       var telephone  = locations.telephone;
                       var mon  = locations.Mon;
                       var tue  = locations.Tue;
                       var wed  = locations.Wed;
                       var thu  = locations.Thu;
                       var fri  = locations.Fri;
                       var sat  = locations.Sat;
                       var sun  = locations.Sun;
                       var lat  = locations.lat;
                       var lng  = locations.lng;
                       var consignmentid =locations.consignmentid;
                       var serviceId = <?=$this->serviceId;?>;
                       var carrierlogo = '<?= $this->carrierLogo; ?>';
                       var carriername = '<?= $this->carrierName; ?>';


                       $.post(url, {func: action, companyname: companyname,
                           addressline1: addressline1, addressline2: addressline2,
                           addressline3: addressline3, city: city, postcode: postcode, country: country, telephone: telephone,
                           mon:mon, tue:tue, wed:wed, thu:thu, fri:fri, sat:sat, sun:sun, carrierLogo: carrierlogo, 
                           carrierName: carriername, lat: lat, lng: lng, serviceId: serviceId, consignmentid: consignmentid
                           }, function (d) {
                               //$('#add-dropoffform-popup').modal('show');
                                $("#dropoff-content-display").html(d);
                                $("#dropoff-content-display").parent().animate({ width: 'show' });
                           });


                   }
                    
               })(marker));
               currentmarker = marker;
            }
              $(document).on('click', '#close_drop_off_detail', function (event, state) {
                $("#dropoff-content-display").parent().animate({ width: 'hide' });
            });
             
            $(document).ready(function () {
                <?php  if(count($this->location_array) > 0){   ?>
                        $(window).bind("load", function() {
                            initMap();
                            initMarker(<?= ($this->location_array);?>);
                            google.maps.event.trigger(currentmarker, 'click');
                            toggleBounce(currentmarker);
                        });
                        
                      //  
                <?php } ?>
              
            $('#btn_Save').click(function () {
                    $.post("change_dropofflocation.php", $("#rangeForm").serialize(), function (response) {
                        
                        initMap();
                        locations = JSON.parse(response);
                        var len = locations.length;
                        var i;
                        
                        
                        // Add multiple markers to map
                        for (i = 0; i < len; i++)
                        {
                            initMarker(locations[i]);   
                        }
                    });
            });
                
            $(document).on('click', '#btnSendParcel', function () {
                
                
                var e = $(this);
                var companyname = e.data('companyname');
                var addressline1 = e.data('addressline1');
                var addressline2 = e.data('addressline2');
                var addressline3 = e.data('addressline3');
                var city = e.data('city');
                var postcode = e.data('postcode');
                var country = e.data('country');
                var telephone = e.data('telephone');
                var consignmentId = e.data('cid');
                var form_data = new FormData();
                form_data.append('action', 'UPDATE_ADDRESS');
                form_data.append('companyname', companyname);
                form_data.append('addressline1', addressline1);
                form_data.append('addressline2', addressline2);
                form_data.append('addressline3', addressline3);
                form_data.append('city', city);
                form_data.append('postcode', postcode);
                form_data.append('country', country);
                form_data.append('telephone', telephone);
                form_data.append('consignmentId', consignmentId);
                $.ajax({
                        url: "change_dropofflocation.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.STATUS == "SUCCESS")
                            {
                                $('#selected_dropofflocation').html(response.MESSAGE);
                                $("#res_message div.alert").addClass('alert-danger');
                                $('#res_message').html('<div class="alert alert-success">Drop Off Location change Successfully. You will receive confirmation email shortly.</div>');
                                $("#res_message").show();
                            } 
                            else
                            {
                                $("#res_message div.alert").addClass('alert-danger');
                                $('#res_message').html('<div class="alert alert-danger">'+response.MESSAGE+'</div>');
                                $("#res_message").show();
                            }
                        }
                    });
                
            });
            });
            function toggleBounce(marker) {
                if (marker.getAnimation() !== null) {
                  marker.setAnimation(null);
                } else {
                  marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            }
            function closeOtherInfo() {
            if (InforObj.length > 0) {
                /* detach the info-window from the marker ... undocumented in the API docs */
                InforObj[0].set("marker", null);
                /* and close it */
                InforObj[0].close();
                /* blank the array */
                InforObj.length = 0;
            }
        }
       
           </script>
        <?php

    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        if (errorList::getItem()->getErrorCount() > 0) {
            ?>
            <div class="alert alert-info" ><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>
        <div >
            <?php
            //if(Permissions::checkFilePermission('range_add'))
            {
                
                ?>
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"><img src='../images/carrierlogo/thumbnail/owe_16_<?=$this->carrierLogo; ?>'  /> 
                            
                              <?php echo $this->carrierName?>  CURRENT DROP OFF LOCATION
                        </div>
                        <div class="actions">
                            
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form name="rangeForm" id="rangeForm" action="" method="POST">           
                            <div class="row display-none" id="res_message">
                                <div class="col-md-12">
                                    <div class="alert alert-success"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="selected_dropofflocation">
                                    <h4>Your Current Drop Off Location:</h4><br />
                                    <?php if(count($this->consignment) > 0)
                                    {
                                    ?>
                                    <label><b><?=$this->consignment->getSenderCompany()?></b></label><br />
                                    <label><?=$this->consignment->getSenderAddressLine1()?></label><br />
                                    <label><?=$this->consignment->getSenderAddressLine2()?></label><br />
                                    <label><?=$this->consignment->getSenderAddressLine3()?></label><br />
                                    <label><?=$this->consignment->getSenderCity()?></label><br />
                                    <label><?=$this->consignment->getSenderPostcode()?></label><br />
                                    <label>Tel: <?=$this->consignment->getSenderTelephone()?></label><br /><br />
                                    <?php
                                    }
                                    ?>
                                    <h4><b>Change Drop Off Location:</b></h4>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="col-md-3">
                                    <label>Country</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <?php
                                        if (empty($this->countryId))
                                             ($this->consignment->getCountryId() > 0) ? $this->countryId = $this->consignment->getCountryId() : "";
                                        echo Ddl::generateCountryDDL('countryId', $this->countryId, 'id', ' class="bs-select input-sm form-control " disabled = "disabled" data-live-search="true" data-show-subtext="true"');
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Post Code</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="postcode" id="postcode" value="" size="50" class="form-control" title="Post Code" maxlength="35" placeholder="Post Code" rel="tooltip" data-original-title="Post Code" type="text" required>

                                        </div>
                                    </div>
                                </div> 
                                
                            </div>


                            <div style="clear:both"></div> 
                            <input type="hidden" name="consignmentid" id="consignmentid" value="<?php echo $this->consignmentId; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                            <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                            <br />
                            <div class="row " style="text-align:centre;" align="center">
                                <div class="col-md-6">

                                    <input id="btn_Save" type="button"  class="btn btn-primary" value="FIND"/>
                                    <input id="btn_Cancel" type="button"  class="btn btn-default" value="RESET"/>
                                </div>
                            </div>   
                        </form>
                    </div>
                </div>
            <?php } ?>
            
            
            <div class="row">
                <div class="col-md-12" style="position:relative;">
                    <div id="map"></div>
                </div>
                <div class="col-md-3 portlet light pull-right"  style="display: none; right: 0px; position:absolute; height: 600px; overflow: auto;">
                    <button type="button" id="close_drop_off_detail" class="close pull-right"></button>
                    <div class="col-md-12" id="dropoff-content-display"></div>
                </div>
            </div>
            
        </div>
            
            <form name="DropoffForm" id="DropoffForm" action="" method="POST">           
            <div class="modal fade " tabindex="-1" role="dialog" id="add-dropoffform-popup"  >
                <div class="modal-dialog modal-m">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="rangeName"></span><img src="../images/carrierlogo/thumbnail/owe_16_<?=$this->carrierLogo;?>" /> <?php  echo $this->carrierName?> Selected Drop Off Location</h4>
                        </div>
                        <div class="modal-body" id="dropoff-content-display">

                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>

        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
      if (isset($_SESSION['admin'])) {
            $menu = new Adminmenu(Adminmenu::COURIERS);
            $menu->render();
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
