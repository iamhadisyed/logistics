<?php
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'carrier.class',
                    'carrierfilter.class',
                    'country.class',
                    'countryfilter.class',
                    'servicecountrytimefilter.class',
                    'servicecountrytime.class',
                    'services.class' ,
                    'servicefilter.class',
                    ]);
// set up local page class
class Page extends BasePage {

    var $ServiceCountryTime;
    private $countries;
    private $countryid = NULL;
    private $service;
    private $serviceid = NULL;
    private $transit_time;
    private $carrier;
    private $carrierid = NULL;
    private $id = NULL;

    public function init() {

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'service_country_time.php' => 'Service Country Time'
        );
        // check admin user is authenticated
        $this->source = @$_GET['from'];
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);

        $this->carrierid = $_GET['carrier_id'];

        $this->serviceid = $_GET['service_id'];

        // Load Services On the Basis of Carrier ID Start
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "getServiceList") {
            $carier_id = $this->form_vars["carrier"];
            $service_id = '';
            $service = new Services();
            $serviceList = $service->getServicesList($service_id, $carier_id);
            echo $serviceList;
            die;
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "deleterecord") {
            $delete_id = $_POST['recordid'];
            $ServiceCountryTime = new ServiceCountryTime($delete_id);
            $ServiceCountryTime->delete();
        }
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "cancelrecord") {
            $cancel_array = array();
//            getCountryDropDownList
            $Country = new Country();
            $service = new Services();
            $Carrier = new Carrier();
            $cancel_array['carrierList'] = $Carrier->dropdownCarrierBox('carrierid', 'carrierid');
            $cancel_array['countryList'] = $Country->getCountryDropDown('countryid', 'countryid', '', 'id');
            $cancel_array['serviceList'] = $service->getServiceDropDownList();
            echo json_encode($cancel_array);
            die;
        }

        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $id = $_POST['recordid'];
            $this->id = $id;
            $edit_array = array();
            $serviceCountry = new ServiceCountryTime($id);
            $edit_array['id'] = $id;
            $edit_array['countryid'] = $serviceCountry->getCountryId();
            $edit_array['serviceid'] = $serviceCountry->getServiceId();
            $edit_array['transit_time'] = $serviceCountry->getTransitTime();
            echo json_encode($edit_array);
            die;
        }
        // Load Services On the Basis of Carrier ID END
        //Form post function Start
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "saverecord") {
            $id = $this->form_vars["id"];
            $alreadyexist_array = array();
            $this->ServiceCountryTime = new ServiceCountryTimeFilter();
            // take appropriate action
            $Country = $this->form_vars["countryid"];
            $service = $this->form_vars["serviceid"];

            $this->ServiceCountryTime->addFieldFilter('id_country', $Country);
            $this->ServiceCountryTime->addFieldFilter('id_service', $service);
            $ServiceCountryTime_List = $this->ServiceCountryTime->getList();
            if (empty($ServiceCountryTime_List)) {
                $serviceCountry = new ServiceCountryTime();
                if ($id > 0) {
                    $serviceCountry->SetId($this->form_vars["id"]);
                }

                $serviceCountry->SetCountryId($this->form_vars["countryid"]);
                $serviceCountry->SetServiceId($this->form_vars["serviceid"]);
                $serviceCountry->SetTransitTime($this->form_vars["transit"]);
                $serviceCountry->save();
                $alreadyexist_array['message'] = "Record Added Successfully";
                $alreadyexist_array['success'] = "1";
                echo json_encode($alreadyexist_array);
                die;
            } else {
                foreach ($ServiceCountryTime_List as $serviceCountry) {
                    $serviceCountry->SetTransitTime($this->form_vars["transit"]);
                    $serviceCountry->save();
                }
                $alreadyexist_array['message'] = "Record Updated Successfully";
                $alreadyexist_array['success'] = "1";
                echo json_encode($alreadyexist_array);
                die;
            }
        }
        // Form Post function End 
        // get Service Country Time List
        $this->ServiceCountryTime = new ServiceCountryTimeFilter();
        $this->setTitle("Admin Service Country Time");

        if (isset($_GET['action']) && $_GET['action'] == "servicecountrytime_ajax") {
            // Get current user
            $sessionUser = SessionManager::getUser();
            /*
             * Set columns orders for sorting
             */
//            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != -1) {
//                $dataTableColumnId = $this->form_vars['order'][0]['column'];
//                $orderBy = $this->form_vars['order'][0]['dir'];
//                $orderFalse = TRUE;
//                if ($orderBy == 'desc') {
//                    $orderFalse = FALSE;
//                }
//                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
//                $this->ServiceCountryTime->AddOrderBy($dataTableColumnName, $orderFalse);
//            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->ServiceCountryTime = new ServiceCountryTimeFilter();

                $searchCountry = $this->form_vars['search_Country'];
                if (!empty($searchCountry)) {
                    $this->ServiceCountryTime->addFieldFilter('id_country', $searchCountry);
                }
                $searchName = $this->form_vars['search_Service'];
                if (!empty($searchName)) {
                    $this->ServiceCountryTime->addFieldFilter('id_service', $searchName);
                }
                $searchAccount = $this->form_vars['search_Transit'];
                if (!empty($searchAccount)) {
                    $this->ServiceCountryTime->addFieldLikeFilter('transit_time', $searchAccount);
                }
            }
            $this->ServiceCountryTime->addServiceTableJoin();
            if ($this->serviceid > 0)
                $this->ServiceCountryTime->addFilter(" id_service = '" . $this->serviceid . "'");
            else if ($this->carrierid > 0)
                $this->ServiceCountryTime->addFilter(" id_service in (select id from services where carrier_id = '" . DbAccess3::escape($this->carrierid) . "') ");



            $iTotalRecords = $this->ServiceCountryTime->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->ServiceCountryTime->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->ServiceCountryTime->setOffset($iDisplayStart);
            $ServiceCountryTime_List = $this->ServiceCountryTime->getList();
            $ServiceCountryTimeDataArr = array();
            foreach ($ServiceCountryTime_List as $ServiceCountryTime) {
                $carrier_id = $ServiceCountryTime->getCarrierId();
                if ($carrier_id > 0) {
                    $carrier = new Carrier($carrier_id);
                    $logo = $carrier->getLogo();
                }
                $ServiceCountryTimeArr['actionss'] = '';
                $ServiceCountryTimeArr['actionss'] .= "<a data-id =" . $ServiceCountryTime->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline'><span class='fa fa-pencil'></span> </a>";
                $ServiceCountryTimeArr['actionss'] .= "<a href='#' data-id =" . $ServiceCountryTime->getId() . " onclick='return confirm('Are you sure you want to delete?')' class='btn btn-xs btndelete red mt-ladda-btn ladda-button btn-outline'><span class='fa fa-times'></a>";
//                $warehouseArr['option'] = '<input type=checkbox id= "delete55" name="deletewarehouse[]" value="' . $warehouse->getId() . '" />';
                $ServiceCountryTimeArr['id_country'] = "<img src='../assets/global/img/flags/" . strtolower(Country::getIsoFromId($ServiceCountryTime->getCountryId())) . ".png' /> " . Country::nameCountry($ServiceCountryTime->getCountryId());
                $ServiceCountryTimeArr['id_service'] = "<img src='../images/carrierlogo/thumbnail/owe_16_$logo' />" . Services::nameService($ServiceCountryTime->getServiceId());
                $ServiceCountryTimeArr['transit_time'] = $ServiceCountryTime->getTransitTime();
                $ServiceCountryTimeDataArr[] = $ServiceCountryTimeArr;
            }
            $ServiceCountryTimeDataarr['data'] = $ServiceCountryTimeDataArr;
            $ServiceCountryTimeDataarr['draw'] = $sEcho;
            $ServiceCountryTimeDataarr['recordsTotal'] = $iTotalRecords;
            $ServiceCountryTimeDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($ServiceCountryTimeDataarr);
            die;
        }
        
        
        
           if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "download_csv_transit_template") {
            $carrierId = $this->form_vars['carrierId'];
            $carrierObj = new Carrier($carrierId);
            /*  Download Csv Code  */
            $fileName = "carrier_transit_time_import.csv";
            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            fputcsv($output, array('Service Code', 'Country ISO', 'Transit Time'));

            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
        }


        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "import_csv_upload") {
            $userAccount = SessionManager::getUser();
            $carrierId = trim($this->form_vars['carrierId']);
            $countryData = new CountryFilter();
            $countryArray = [];
            $serviceArray = [];
            $errorRows = [];
            $outmessage = '';


            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name = "transit_" . $carrierId . "_" . time() . "_" . $basename;


                    chdir('../');
                    chdir('_assets');
                    $currentDirecotryPath = str_replace('\\', '/', getcwd()) . "/";
                    $newDirectoryPath = $currentDirecotryPath . '/upload_csv/transits/';
                    if (!file_exists($newDirectoryPath)) {
                        @mkdir($newDirectoryPath, 0775, true);
                    }


                    $relPath = $newDirectoryPath . $new_file_name;
                    if (file_exists($newDirectoryPath)) {
                        if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {

                            $row = 1;
                            $successRecords = 0;
                            $errorRecords = 0;

                            if (($handle = fopen($relPath, "r")) !== FALSE) {
                                $csvContent = $outmessage = '';
                                $successRecords = 0;
                                $errorRecords = 0;

                                $countryDataList = $countryData->getColumnList('id,iso');
                                if (count($countryDataList) > 0) {
                                    foreach ($countryDataList as $country) {
                                        $countryArray[$country->getIso()] = $country->getId();
                                    }
                                }

                                $servicesObj = new ServiceFilter();
                                $servicesObj->addFieldFilter('carrier_id', $carrierId);
                                $serviceDataList = $servicesObj->getColumnList(' code ');
                                if (count($serviceDataList) > 0) {
                                    foreach ($serviceDataList as $services) {
                                        $serviceArray[$services->getCode()] = $services->getId();
                                    }
                                }


                                while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                    if ($row > 1) {
                                        $serviceCode = trim($data[0]);
                                        $countryIso = trim($data[1]);
                                        $transitTime = trim($data[2]);

                                        if ($serviceCode != "" && $countryIso != "" && $transitTime != "") {
                                            if (isset($countryArray[$countryIso])) {//check valid ISO and code uploaded
                                                if (isset($serviceArray[$serviceCode])) {
                                                    $serviceCountryTime = new ServiceCountryTimeFilter();
                                                    $serviceCountryTime->addFilter("    id_country = '" . $countryArray[$countryIso] . "' AND id_service='" . $serviceArray[$serviceCode] . "' ");

                                                    $serviceCountryTimeList = $serviceCountryTime->getList(false);
                                                    if (count($serviceCountryTimeList) > 0) {
                                                        //already exists
                                                        foreach ($serviceCountryTimeList as $serviceCountry) {
                                                            $serviceCountry->SetTransitTime($transitTime);
                                                            $serviceCountry->save();
                                                            $successRecords++;
                                                        }
                                                    } else {
                                                        //not exists
                                                        $serviceCountryTime = new ServiceCountryTime();
                                                        $serviceCountryTime->SetCountryId($countryArray[$countryIso]);
                                                        $serviceCountryTime->SetServiceId($serviceArray[$serviceCode]);
                                                        $serviceCountryTime->SetTransitTime($transitTime);
                                                        $serviceCountryTime->save();
                                                        $successRecords++;
                                                    }
                                                } else {
                                                    $errorRecords++;
                                                    $errorRows['invalid_service_code_for_carrier'][] = $row;
                                                }
                                            } else {
                                                $errorRecords++;
                                                $errorRows['invalid_country_iso'][] = $row;
                                            }
                                        } else {
                                            $errorRows['empty_rows'][] = $row;
                                            $errorRecords++;
                                        }
                                    }
                                    $row++;
                                }

                                $outmessage .= 'CSV Uploaded Successfully';
                                $outmessage .= '<br /> ' . $successRecords . ' Transit time updated successfully.';
                                $outmessage .= '<br /> ' . $errorRecords . '  Not imported due to invalid data in csv.';

                                if (count($errorRows) > 0) {
                                    foreach ($errorRows as $key => $errorRows) {
                                        $outmessage .= "<br><br><b>" . strtoupper($key). "</b>";
                                        $outmessage .= "<br>Row#: " . implode(', ', $errorRows) . "";
                                    }
                                }

                                $output['message'] = $outmessage;
                                $output["status"] = "success";
                            }
                        } else {
                            $output['message'] = 'File upload fail.';
                            $output['status'] = 'fail';
                        }
                    }
                } else {
                    $output['message'] = 'Invalid CSV File.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = 'No file found to import data.';
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        }


        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "import_csv_modal") {
            $carrierId = $this->form_vars['carrierId'];
            echo '  <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title"><b>Import Transit Time</b></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="message_download_csv" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-danger"></div>
                            </div>
                        </div>
                        <div class="row">
    
                            <div class="col-md-12">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                     
                                        <label>' . Translation::GetCaption("SELECT_FILE_TO_IMPORT") . '</label>
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input input-fixed input-large" data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                <span class="fileinput-filename"> </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput-new"> Select file </span>
                                                <span class="fileinput-exists"> Change </span>
                                                <input type="file" name="file" id="upload_csv_file"> </span>
                                            <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            <a href="javascript:;" class="input-group-addon btn blue" id="import_csv_btn" data-original-title="" data-carrierId ="' . $carrierId . '" title="">' . Translation::GetCaption("IMPORT") . '</a>
                                            <a href="javascript:;" class="input-group-addon btn danger" id="download_csv_template" data-original-title="" title=""><i class="fa fa-download"></i>' . Translation::GetCaption("TEMPLATE") . '</a>
                                                
                                        </div>
                                     
                                </div>
                            </div>
                        </div> 
                        
                        <div class="row">
                            <div class="col-md-12">
                               <br> <div  id="upload_console_window" style="  clear:both;background-color: #000;color: #FFF; padding: 15px; display:none;"></div>
                            </div>         
                            </div>
                  
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        
                    </div>
                 ';
            die;
        }
    }

    public function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <?php
    }

    public function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption" id="heading">
                    <i class="glyphicon glyphicon-List"></i>
                    <?php echo Translation::GetCaption("ADD_UPDATE_SERVICE_TIME") ?>
                </div>
                <div class="tools">
                    <input id="import_csv_option"  data-name="carrier" data-value="<?php echo $this->carrierid; ?>"    type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("Import csv"); ?>"/>
                </div>
            </div>
            <div class="portlet-body">
                <form method="post" action="" enctype="multipart/form-data" id="servicecountrytimeForm" name="servicecountrytimeForm"  role="form">
                    <div class="row">
                        <div class="col-md-12 hidden" id="successmsg">
                            <div class="alert alert-success" id="success_msg"> Record has been Added Successfully .</div>

                        </div>
                        <div class="col-md-12 hidden" id="failuremsg">
                            <div class="alert alert-danger" id="failure_msg"> Record Already Exist .</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Carrier</label>
                                <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <?php
                                    if ($this->carrierid != '')
                                        $disabled = "disabled";
                                    else
                                        $disabled = "";
                                    echo Ddl::generateCarrierDDLWithImage('carrierid', $this->carrierid, 'id', ' class="bs-select form-control ' . $disabled . '" data-live-search="true"');
                                    //echo Ddl::generateDDL('carrierid','CarrierFilter','status=1','carrier', 'id',$this->carrierid,'class="form-control select2"'. $disabled,'Select Carrier','','carrierid','Carrier');                                     
//                                    echo Carrier::dropdownCarrierBox('carrierid', 'carrierid', $this->carrierid, $disabled);
                                    ?>
                                    <span class="input-group-addon red-18"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Services</label>
                            <div class="form-group">
                                <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>

                                    <?php
                                    echo Ddl::generateServiceDDLWithImage("serviceid", $this->serviceid, 'id', ' class="bs-select input-sm form-control form-filter " required="" data-live-search="true" data-show-subtext="true" data-live-search="true"', '', '', 'name', 'Select Services', $this->carrierid);
                                    //   echo Services::getServicesList($this->serviceid, $this->carrierid);
                                    ?> 
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Country</label>
                                <div class="input-group input-group-sm"> 
                                    <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                                    <?php echo Ddl::generateCountryDDL('countryid', $countryid, 'id'); ?>
                                    <?php //echo Ddl::generateDDL('countryid','CountryFilter','','name', 'id','','class="form-control select2"','Select Country','','countryid','Country');  ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Transit Time</label>
                                <div class="input-group input-group-sm input-icon right"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>

                                    <i class="fa tooltips font-red" data-original-title="Transit Time is mandatory">*</i>
                                    <input name="transit_time" id="transit_time" value="" size="50" class="form-control"  maxlength="35" title=""
                                           placeholder="<?php echo Translation::GetCaption('TRANSIT_TIME'); ?> in days"
                                           rel="tooltip" onkeypress='return numbersonly(event)' data-original-title="<?php echo Translation::GetCaption("TRANSIT_TIME"); ?>" type="text" required>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both"></div>
                    <div class="row " style="text-align:centre;" align="center">
                        <div class="col-md-12">
                            <input id="btn_Save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                                   <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>
                    <input type="hidden" name="form_action" id="form_action" value="" />
                </form>
            </div>
        </div> 
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-List"></i>
                    <?php echo Translation::GetCaption("SERVICE_COUNTRY_LIST") ?>
                </div>
                <div class="tools">
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th>Actions</th>
                                <th>Country</th>
                                <th>Service</th>
                                <th>Transit Time (Days) </th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn-xs filter-submit margin-bottom btn btn-default mt-ladda-btn ladda-button btn-outline "><i class="fa fa-search"></i></button>
                                        <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    echo Ddl::generateCountryDDL('search_Country', $search_Country, 'id', ' class="select2 form-control input-sm" required="" data-live-search="true" data-size="8"');
                                    ?>
                                </td>
                                <td>
                                    <?php echo Ddl::generateServiceDDLWithImage("search_Service", $search_Service, 'id', ' class="select2 input-sm form-control form-filter " required="" data-live-search="true" data-show-subtext="true" data-live-search="true"', '', '', 'name', 'Select Services'); ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_Transit" onkeypress='return numbersonly(event)'>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        
             <!--Model for services remotearea-->
    <div class="modal fade bs-modal-lg" id="modal_upload_csv"   aria-hidden="true" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" id="modal-data">

                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
            </div>
             
             
       <form name="hiddenForm" id="download_template_form" action="service_country_time.php" method="POST">
     
                <input type="hidden" name="carrierId" id="download_csv_carrierId" value="<?php echo $this->carrierid;?>" />
         
                <input type="hidden" name="func" value="download_csv_transit_template" />
            </form>  
        <?php
    }

    public function renderFooter() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script> 
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        <script src="../js/bootstrap-select.min.js"></script>
         <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript">
                    $(document).ready(function () {
                        
                        
                        $(document).on('click', '#download_csv_template', function () {
                            $('#download_template_form').submit();
                        });
                        
              
                            $(document).on('click', '#import_csv_option', function () {
                            $.ajax({
                                    url: 'service_country_time.php',
                                    type: 'POST',
                                    dataType: "html",
                                    data: {func: 'import_csv_modal',  carrierId: $(this).data("value") },
                                    headers: {
                                    },
                                    success: function (data) {
                                   $("#modal-data").html("");
                                   $("#modal-data").html(data);
                                    },
                                    error: function (xhr, status, error) {
                                    }
                                });
                                
                                $('#modal_upload_csv').modal('show');
                        });
                        
                        
                        $(document).on('click', '#import_csv_btn', function () {
                        var file_data = $('#upload_csv_file').prop('files')[0];
                        var form_data = new FormData();
                        form_data.append('carrierId',$(this).data("carrierid"));
                        form_data.append('func','import_csv_upload');
                        form_data.append('csv_file', file_data);

                        $.ajax({
                            url: 'service_country_time.php',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (data) {

                                  if (data.status == 'success') {
                                    $("#upload_console_window").addClass('alert-success').removeClass('alert-danger');
                                    $("#upload_console_window").html("");
                                    $("#upload_console_window").html(data.message);
                                    $("#upload_console_window").show();

                                     swal("Success!", "Transit Time Updated Successfully", "success");
                                }else{
                                    swal("Error!", "Something went wrong! Transit Time not saved \n\r "+data.message, "error");
                                     $("#upload_console_window").html("");
                                     $("#upload_console_window").html(data.message);
                                     $("#upload_console_window").show();
                                }

                            }
                        });
        });
                
                $('#search_Country').addClass('form-filter');
                var xhr;
                var active=false;
                $('#successmsg').hide();
                // Service changes
                $("#carrierid").change(function () {
                    var carrier_value = $('#carrierid').val();
                    $.ajax({
                        method: "POST",
                        url: "service_country_time.php",
                        data: {carrier: carrier_value, func: "getServiceList"}
                    }).done(function (data) {
                        $('#serviceid').html(data);
                        $('#serviceid').selectpicker("refresh");
                    });
                });

                $(document).on('click', '#btn_Cancel', function () {
                    //                    $(".remove").removeClass("has-error has-danger");
                    window.location = 'services_list.php';

                    $.ajax({
                        method: "POST",
                        url: "service_country_time.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#countryid').html(" ");
                        $('#serviceid').html(" ");
                        $('#carrierid').html(" ");
                        $('#transit_time').val(" ");
                        $('select').removeAttr('disabled');
                        $('#carrierid').html(t.carrierList);
                        $('#countryid').html(t.countryList);
                        $('#serviceid').html(t.serviceList);
                    });
                });

                $(document).on('click', '#btn_Save', function () {

                    $('#servicecountrytimeForm').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else
                        {
                            
                            $('#btn_Save').prop('disabled', true);
                            //                            $('#btn_Save').attr('disabled','disabled');
                            var transit_value = $('#transit_time').val();
                            var serviceid = $('#serviceid').val();
                            var countryid = $('#countryid').val();
                            var id = $('#id').val();
                            if(active) {xhr.abort();}
                             active=true;
                            xhr = $.ajax({
                                method: "POST",
                                url: "service_country_time.php",
                                data: {id: id, transit: transit_value, serviceid: serviceid, countryid: countryid, func: "saverecord"}
                            }).done(function (data) {
                                var t = $.parseJSON(data);
                                if (t.success == '1') {
                                    $('#btn_Save').val("Save");
                                    $('#success_msg').html(" ");
                                    if (id == "") {
                                        $('#success_msg').html("Record has been Added Successfully");
                                    } else {
                                        $('#success_msg').html("Record has been Updated Successfully");
                                    }
                                   // $('#btn_Cancel').click();
                                    $("div").removeClass("hidden");
                                    $('#failuremsg').hide();
                                    $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                    $('#id').val("");
                                    $('#manage-data-table').DataTable().ajax.reload();
                                     active=false;
                                } else {
                                    $('#failure_msg').html("");
                                    $('#failure_msg').html(t.message);
                                    $("div").removeClass("hidden");
                                    $('#successmsg').hide();
                                    $('#failuremsg').show();
                                     active=false;
                                }
                                
                            }, 'json');
                            $('#btn_Save').removeAttr('disabled');
                        }
                    });
                    $("#servicecountrytimeForm").submit();
                });

                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var recordid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "service_country_time.php",
                        data: {recordid: recordid, func: "editrecord"}
                    }).done(function (data) {
                        //By using javasript json parse
                        var t = JSON.parse(data);

                        $('#btn_Save').val("Update");
                        $('#transit_time').val(t.transit_time);
                        $('#serviceid').val(t.serviceid).change();
                        $('#countryid').val(t.countryid).change();
                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
                    });
                });
                $(document).on('click', '.btndelete', function () {
                    var e = $(this);
                    var recordid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "service_country_time.php",
                        data: {recordid: recordid, func: "deleterecord"}
                    }).done(function (data) {
                        e.parents('tr').hide();
                        $('#manage-data-table').DataTable().ajax.reload();
                    });
                });
            });
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error  
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options 
                            "lengthMenu": [
                                [10, 20, 50, 100],
                                [10, 20, 50, 100] // change per page values here 
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": "service_country_time.php?action=servicecountrytime_ajax<?php echo isset($_GET['carrier_id'])?'&carrier_id='.DbAccess3::escape((int)$_GET['carrier_id']):'';?>", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "id_country"},
                                {"data": "id_service"},
                                {"data": "transit_time"},
                                        //                                {"data": "actions", "bSortable": false},
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable();
                    }
                };
            }();

            $(document).ready(function () {
                DataTableFun.init();
            });
            
             function numbersonly(e)
        {
            var unicode=e.charCode? e.charCode : e.keyCode
            if (unicode!=8)
            {
                if(unicode==46)
                {}
                else if (unicode<48||unicode>57) //if not a number
                return false //disable key press
        }
    }
        </script>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COUNTRIES);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
