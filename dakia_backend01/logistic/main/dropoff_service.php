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
    'include_list',
    
    ], 'reamus');
include_classes([
    'ups.class'
    ], 'labels');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'currency.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'serviceconstantvaluefilter.class',
    'serviceconstantvalue.class',
    'servicerangemappingfilter.class',
    'servicerangemapping.class',
    'licenceplatefilter.class',
    'licenceplate.class',
    'warehousefilter.class',
    'warehouse.class',
    'trackingfilter.class',
    'tracking.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    
    
    
    
    
    ]);
//require_once("../includes/labels/parcelforyou.class.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $countryId = "";
    private $user = "";
    private $serviceId = "";
    private $serviceValue = "";
    private $carrierValue = "";
    private $carrierLogo = "";
    private $carrierName = "";
    private $location_array = array();

    protected function init() {

//        if(!Permissions::checkFilePermission('add_ranges.php')) 
//                    util_redirect ("index.php");
        $this->user = $user = SessionManager::getUser();
        $this->countryId = util_get("countryid");
        $this->serviceId = util_get("serviceid");

        if (!empty($this->serviceId) && $this->serviceId > 0) {
            $this->serviceValue = new services($this->serviceId);
            $carrierId = $this->serviceValue->getCarrierId();
            $this->carrierValue = new Carrier($carrierId);
            $this->carrierLogo = $this->carrierValue->getLogo();
            $this->carrierName = $this->carrierValue->getCarrier();
        }
        // common initialisation for ths page
        $this->setTitle("Drop Off List");

        if ($this->user->getId() > 0 && $this->serviceId > 0) {
            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->user->getId());
            $dropoffFilter->addFieldFilter("service_id", $this->serviceId);
            $dropoffFilter->orderBySort("date_created desc");
            $dropoffList = $dropoffFilter->getList();

            if (count($dropoffList) > 0) {
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
                    'index' => 0);
                $this->location_array = json_encode($locationarray);
            }
        }
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {


            $output = [];
            $countryId = $this->form_vars["countryId"];
            if($countryId > 0)
            {
                $country = new Country($countryId);
            }
            $postcode = str_replace(" ", "", $this->form_vars["postcode"]);
            $upslabel = new UPS();
            $dropofflocationresponse = $upslabel->getDropOffLocation($postcode, $country->getIso());
           
            if (sizeof($dropofflocationresponse) > 0) {
                $index = 1;
                foreach ($dropofflocationresponse as $dropoff)
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
                        'index' => $index);
                $index++;
            }

            echo json_encode($location);
            die;
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "DROPOFF_SELECTED_SERVICE") {
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


            $dropoffFilter = new DropoffUserLocationFilter();
            $dropoffFilter->addFieldFilter("user_id", $this->user->getId());
            $dropoffFilter->addFieldFilter("service_id", $serviceId);
            $dropoffFilter->addFieldFilter("companyname", $companyname);
            $dropoffList = $dropoffFilter->getList();
            if (count($dropoffList) == 0) {

                $dropUserLocation = new DropoffUserLocation();
                $dropUserLocation->setUserId($this->user->getId());
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
            } else {
                foreach ($dropoffList as $droppoint) {
                    $droppoint->setDateCreated(strtotime(date('Y-m-d H:i:s')));
                    $droppoint->save();
                }
            }



            $output .= '<div class="row">
                            <h4><span id="rangeName"></span><img src="../images/carrierlogo/thumbnail/owe_100_' . $carrierlogo . '" /> ' . $carriername . '</h4>
                            <div class="col-md-12">';
            $output .= '<label><h3><b>' . $companyname . '</b></h3></label><br />';
            $output .= '<label>' . $addressline1 . '</label><br />';
            $output .= '<label>' . $addressline2 . '</label><br />';
            $output .= '<label>' . $addressline3 . '</label><br />';
            $output .= '<label>' . $city . '</label><br />';
            $output .= '<label>' . $postcode . '</label><br />';
            $output .= '<label>' . $country . '</label><br />';
            $output .= '<label>' . $telephone . '</label><br />';
            $output .= '</div><div class="col-md-12">';
            $output .= '<label><h3><b>Opening Hours</b></h3></label></div>';
            $output .= '<div  class="col-md-2">Mon</div><div  class="col-md-10">' . $mon . '</div>';
            $output .= '<div  class="col-md-2">Tue</div><div  class="col-md-10">' . $tue . '</div>';
            $output .= '<div  class="col-md-2">Wed</div><div  class="col-md-10">' . $wed . '</div>';
            $output .= '<div  class="col-md-2">Thu</div><div  class="col-md-10">' . $thu . '</div>';
            $output .= '<div  class="col-md-2">Fri</div><div  class="col-md-10">' . $fri . '</div>';
            $output .= '<div  class="col-md-2">Sat</div><div  class="col-md-10">' . $sat . '</div>';
            $output .= '<div  class="col-md-2">Sun</div><div  class="col-md-10">' . $sun . '</div>';
            $storedetails = "companyname:" . $companyname . ",addressline1:" . $addressline1 . ",addressline2:" . $addressline2 . ",addressline3:" . $addressline3 . ",city:" . $city . ",postcode:" . $postcode . ",country:" . $country . ",telephone:" . $telephone;
            $base64storedetails = base64_encode($storedetails);
            $output .= '</div><div style="clear:both"><br /></div><div class="col-md-6">'
                    . '<a  data-store-detail="' . $base64storedetails . '" id="btnSendParcel" class="btn btn-sm blue">'
                    . '<span></span>&nbspSend Parcel</a></div>';
            $output .= '</div>';
            echo $output;
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
        </style>


        <?php
    }

    public function addPagelavelJs() {
        ?>




        <?php
    }

    protected function renderFooter() {
        ?>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1w02sqHWTNNRIf_RCJ4r1lymBQxHMPx0&region=IN"></script>
        <script type="text/javascript">
            var InforObj = [];
            var locations = '';
            var map;
            var previousmarker = null;
            var currentmarker = null;

            function initMap(countryIso='', postcode='')
            {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: {lat: 51.5, lng: -0.41}
                });
                
                var geocoder = new google.maps.Geocoder();
                geocodeAddress(geocoder, map, countryIso, postcode);        
                    

            }
            
            function geocodeAddress(geocoder, resultsMap, countryIso, postcode) {
                
                var address = postcode + "," + countryIso;
                geocoder.geocode({'address': address}, function(results, status) {
                  if (status === 'OK') {
                    resultsMap.setCenter(results[0].geometry.location);
//                    var marker = new google.maps.Marker({
//                      map: resultsMap,
//                      position: results[0].geometry.location
//                    });
                  } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                  }
                });
              }

            //Add marker on map
            function initMarker(locations)
            {
                
                $("#dropoff-content-display").html('');
                var iconBase = '../images/carrierlogo/thumbnail/owe_16_<?php echo $this->carrierLogo; ?>';
                var hoverIconBase = '../images/carrierlogo/thumbnail/owe_50_<?php echo $this->carrierLogo; ?>';
                var contentString = '<div id="content"><h5>' + locations.companyname + '</h5><br /> ' + locations.addressline1 + '<br /> ' + locations.addressline2 + '<br /> ' + locations.city + '<br /> ' + locations.postcode + '</div>';

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
                google.maps.event.addListener(marker, 'click', (function () {
                    return function () {
                        if (previousmarker !== null)
                        {
                            previousmarker.setAnimation(null);
                            previousmarker.setIcon(iconBase);
                        }
                        previousmarker = marker;
                        map.setZoom(12);
                        //map.setCenter(marker.getPosition());
                        marker.setIcon(null);
                        marker.setIcon(hoverIconBase);
                        toggleBounce(marker);
                        var url = "dropoff_service.php";
                        var action = "DROPOFF_SELECTED_SERVICE";
                        var companyname = locations.companyname;
                        var addressline1 = locations.addressline1;
                        var addressline2 = locations.addressline2;
                        var addressline3 = locations.addressline3;
                        var city = locations.city;
                        var postcode = locations.postcode;
                        var country = locations.country;
                        var telephone = locations.telephone;
                        var mon = locations.Mon;
                        var tue = locations.Tue;
                        var wed = locations.Wed;
                        var thu = locations.Thu;
                        var fri = locations.Fri;
                        var sat = locations.Sat;
                        var sun = locations.Sun;
                        var lat = locations.lat;
                        var lng = locations.lng;
                        var serviceId = <?= $this->serviceId; ?>;
                        var carrierlogo = '<?= $this->carrierLogo; ?>';
                        var carriername = '<?= $this->carrierName; ?>';


                        $.post(url, {func: action, companyname: companyname,
                            addressline1: addressline1, addressline2: addressline2,
                            addressline3: addressline3, city: city, postcode: postcode, country: country, telephone: telephone,
                            mon: mon, tue: tue, wed: wed, thu: thu, fri: fri, sat: sat, sun: sun, carrierLogo: carrierlogo, carrierName: carriername, lat: lat, lng: lng, serviceId: serviceId
                        }, function (d) {
                            //$('#add-dropoffform-popup').modal('show');
                            $("#dropoff-content-display").html(d);
                        });


                    }

                })(marker));
                currentmarker = marker;
            }


            $(document).ready(function () {
        <?php if (count($this->location_array) > 0) { ?>
                    $(window).bind("load", function () {
                        initMap();
                        initMarker(<?= ($this->location_array); ?>);
                        google.maps.event.trigger(currentmarker, 'click');
                        toggleBounce(currentmarker);
                    });

                    //  
        <?php } ?>

                $('#btn_Save').click(function () {
                    $.post("dropoff_service.php", $("#rangeForm").serialize(), function (response) {
                        
                        
                        locations = JSON.parse(response);
                        var countryIso = locations[0].country;
                        var postcode = document.getElementById('postcode').value;
                        initMap(countryIso, postcode);
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
                    var storedetails = $(this).data("store-detail");
                    window.location.href = "consignment_add.php?dropoffdetail=" + storedetails;

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
            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>
        <div class="main_formpage">
        <?php
        //if(Permissions::checkFilePermission('range_add'))
        {
            ?>
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"><img src='../images/carrierlogo/thumbnaicaption-subject bold uppercasel/owe_16_<?= $this->carrierLogo; ?>'  /> 
                            
                <?php echo $this->carrierName ?>   DROP OFF LOCATION
                            
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
                                <div class="col-md-3">
                                    <label>Country</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
            <?php
            if (empty($this->countryId))
                $this->countryId = "225";
            echo Ddl::generateCountryDDL('countryId', $this->countryId, 'id', ' class="bs-select input-sm form-control" data-live-search="true" data-show-subtext="true"');
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
                            <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
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

            <div class="col-md-9">
                <div id="map"></div>
            </div>
            <div class="col-md-3 portlet light" style="height: 600px; overflow: auto;">
                <div class="col-md-12" id="dropoff-content-display">

                </div>

            </div>
        </div>

        <form name="DropoffForm" id="DropoffForm" action="" method="POST">           
            <div class="modal fade " tabindex="-1" role="dialog" id="add-dropoffform-popup"  >
                <div class="modal-dialog modal-m">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="rangeName"></span><img src="../images/carrierlogo/thumbnail/owe_16_<?= $this->carrierLogo; ?>" /> <?php echo $this->carrierName ?> Selected Drop Off Location</h4>
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
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
