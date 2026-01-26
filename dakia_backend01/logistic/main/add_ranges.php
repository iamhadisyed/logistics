<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([ 'errorlist.class'
                ],'visualcomponents');
include_classes(['licenceplate.class','licenceplatefilter.class']);
include_classes([
    'country.class',
    'countryfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'services.class',
    'servicefilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'carrierfilter.class',
    'carrier.class',
    'servicerangemapping.class',
    'servicerangemappingfilter.class']);
 
 
class Page extends BasePage {

    private $licencePlate;
    private $id = NULL;
    private $breadcrumb = '';
    private $user = NULL;
    private $isCountry = 0;

    /*     * *
     * Controller logic
     */

    protected function init() {

//        if(!Permissions::checkFilePermission('add_ranges.php')) 
//                    util_redirect ("index.php");
        $this->user = $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'add_ranges.php' => 'Add Ranges'
        );
        $this->licencePlate = new LicencePlateFilter();
        $countryRange = 0;
        // common initialisation for ths page
        $this->setTitle("Ranges List");
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {
            $output = [];

            $range_id = $this->form_vars["id"];
            $start_range = $this->form_vars["start_range"];
            $range_name = $this->form_vars["range_name"];
            $end_range = $this->form_vars["end_range"];
            $prefix = $this->form_vars["prefix"];
            $sufix = $this->form_vars["suffix"];
            $reminder_limit = $this->form_vars["reminder_limit"];;
            
            $next_number = $this->form_vars["next_number"];
            $serviceId = $this->form_vars["service_id"];
            $agentId = $this->form_vars["agent_id"];

            $errors = [];
            if (empty($start_range)) {
                $errors[] = "Enter Start Range.";
            }
            if (empty($end_range)) {
                $errors[] = "Enter Start Range.";
            }
            if (empty($range_name)) {
                $errors[] = "Enter Range Name.";
            }
            if (empty($next_number)) {
                $errors[] = "Enter Next Number.";
            }
            if (empty($agentId)) {
                $errors[] = "Select Agent";
            }
            if (empty($serviceId)) {
                $errors[] = "Select Services";
            }

            if (!empty($errors)) {
                $errorStr = implode('<br />', $errors);
                $output['STATUS'] = 'error';
                $output['MESSAGE'] = $errorStr;
            } else {

                $licencePlateObj = new LicencePlate($range_id);

                if ($range_id > 0 && $licencePlateObj->getNextNumber() == $licencePlateObj->getRangeEnd()) {
                    $output['STATUS'] = 'error';
                    $output['MESSAGE'] = 'Tracking number range finish. Please check your ranges.';
                } else {
                    $licencePlateObj->setRangeName($range_name);
                    $licencePlateObj->setRangeStart($start_range);
                    $licencePlateObj->setRangeEnd($end_range);

                    $licencePlateObj->setNextNumber($next_number);

                    if (isset($this->form_vars["countryrange"])) {
                        $countryRange = 1;
                        $this->isCountry = $countryRange;
                        $licencePlateObj->setCountryRange($countryRange);
                        $zoneCountries = $this->form_vars['zone_countries'];
                        if (count($zoneCountries) > 0) {
                            $countryList = implode(',', $zoneCountries);
                            $licencePlateObj->setCountryList($countryList);
                        }
                    }

                    if ((int) $range_id <= 0) {
                        $licencePlateObj->setAddedBy($user->getId());
                        $licencePlateObj->setDateCreated(time());
                    }

                    $licencePlateObj->setPrefix($prefix);
                    $licencePlateObj->setSufix($sufix);
                    $licencePlateObj->setUpdatedBy($user->getId());
                    $licencePlateObj->setDateUpdated(time());

                    $licencePlateObj->save();

                    $licenceplate_id = $licencePlateObj->getId();
                    if ($licenceplate_id > 0) {
                        if (count($serviceId) > 0) {
                            foreach ($serviceId as $sid) {
                                $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
                                $serviceRangeMappingFilter->addFieldFilter("service_id", $sid);
                                $serviceRangeMappingFilter->addFieldFilter("agent_id", $agentId);
                                if($range_id > 0)
                                {
                                    $serviceRangeMappingFilter->addFieldFilter("licence_plate_id", $range_id);
                                }
                                $servicerangeList = $serviceRangeMappingFilter->getList();
                                if (count($servicerangeList) > 0) {
                                    
                                        foreach($servicerangeList as $srlist)
                                        {
                                            
                                         
                                            if($range_id > 0)
                                            {
                                                $srlist->setServiceId($sid);
                                                $srlist->setAgentId($agentId);
                                                $srlist->setLicencePlateId($licenceplate_id);
                                                $srlist->save();
                                            }
                                           else if($licenceplate_id != $srlist->getLicencePlateId() && $countryRange != "1")
                                            {
                                                $output['STATUS'] = 'error';
                                                $output['MESSAGE'] = 'Selected Service and Agent already have pre-assigned Ranges.';
                                                echo json_encode($output);
                                                die;
                                            }
                                            else
                                            {
                                                $serviceRangeMapping = new ServiceRangeMapping();
                                                $serviceRangeMapping->setServiceId($sid);
                                                $serviceRangeMapping->setAgentId($agentId);
                                                $serviceRangeMapping->setLicencePlateId($licenceplate_id);
                                                $serviceRangeMapping->save();
                                               
                                            } 
                                                
                                               
                                    }
                                    
                                } else {
                                    $serviceRangeMapping = new ServiceRangeMapping();
                                    $serviceRangeMapping->setServiceId($sid);
                                    $serviceRangeMapping->setAgentId($agentId);
                                    $serviceRangeMapping->setLicencePlateId($licenceplate_id);
                                    $serviceRangeMapping->save();
                                    
                                }
                            }
                           
                        }
                        if($range_id > 0)
                        {
                            $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
                            $serviceRangeMappingFilter->addFieldFilter("licence_plate_id", $range_id);
                            $servicerangeList = $serviceRangeMappingFilter->getList();
                           
                            if(count($servicerangeList) != count($serviceId))
                            {
                                $serviceInRange = array();
                                foreach($servicerangeList as $sList)
                                {
                                    $serviceInRange[] = $sList->getServiceId();
                                }
                                $deleteServiceList = array_diff($serviceInRange, $serviceId);
                                $sRangeMapping = new ServiceRangeMapping();
                                $sRangeMapping->deleteRange(" service_id = '".implode("','", $deleteServiceList)."' and licence_plate_id = '".$range_id."'");
                                
                            }
                        }
                        
                    }
                    $output['STATUS'] = 'success';
                    $output['MESSAGE'] = "Ranges added successfully.";
                }
            }
             echo json_encode($output);
            die;
        } 
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $licenceplate_id = (int) $_POST['recordid'];

            $this->id = $licenceplate_id;
            $edit_array = array();
            $licencePlate = new LicencePlate($licenceplate_id);

            $edit_array['id'] = $licenceplate_id;
            $edit_array['start_range'] = $licencePlate->getRangeStart();
            $edit_array['end_range'] = $licencePlate->getRangeEnd();
            $edit_array['prefix'] = $licencePlate->getPrefix();
            $edit_array['suffix'] = $licencePlate->getSufix();
            $edit_array['reminder_limit'] = $licencePlate->getRangeReminderLimit();
            
            $edit_array['range_name'] = $licencePlate->getRangeName();
            $edit_array['next_number'] = $licencePlate->getNextNumber();
            $edit_array['country_range'] = $licencePlate->getCountryRange();
            $zoneCountries = [];
            if($licencePlate->getCountryRange() == 1){
                $zoneCountries  = explode(",", $licencePlate->getCountryList());
               
            }
            $edit_array['zone_countries'] = $zoneCountries; 
            $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
            $serviceRangeMappingFilter->addFieldFilter("licence_plate_id", $licenceplate_id);
            $servicerangeList = $serviceRangeMappingFilter->getList();
            if(count($servicerangeList) > 0)
            {
                $serviceIds = [];
                foreach($servicerangeList as $srlist)
                {
                    $serviceIds[] = $srlist->getServiceId();
                    $edit_array['agent_id'] = $srlist->getAgentId();
                }
            }
            $edit_array['service_id'] = $serviceIds;

            echo json_encode($edit_array);
            die;
        } 
        //// NOT USING
        else if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "ASSIGN_RANGE") {
            $licenceplate_id = $this->form_vars["range_id"];
            $serviceId = $this->form_vars["service_id"];
            $services = explode(",", $serviceId);

            $agentId = $this->form_vars["agent_id"];
            $output = array();
            if (count($serviceId) > 0 && $agentId > 0) {
                foreach ($services as $sid) {
                    $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
                    $serviceRangeMappingFilter->addFieldFilter("service_id", $sid);
                    $serviceRangeMappingFilter->addFieldFilter("agent_id", $agentId);
                    $servicerangeList = $serviceRangeMappingFilter->getList();
                    if (count($servicerangeList) > 0) {
                        foreach ($servicerangeList as $srlist) {
                            $srlist->setLicencePlateId($licenceplate_id);
                            $srlist->save();
                        }
                    } else {
                        $serviceRangeMapping = new ServiceRangeMapping();
                        $serviceRangeMapping->setServiceId($sid);
                        $serviceRangeMapping->setAgentId($agentId);
                        $serviceRangeMapping->setLicencePlateId($licenceplate_id);
                        $serviceRangeMapping->save();
                    }
                }
                $output["status"] = "success";
                $output["message"] = '<div class="alert alert-success">' . formatMessages(SUCCESS_SERVICE_ADDED) . '</div>';
            } else {
                $output["status"] = "error";
                $output["message"] = '<div class="alert alert-danger">' . formatMessages(ERROR_SERVICE_AGENT_SELECT) . '</div>';
            }

            echo json_encode($output);
            die;
        } 
        
        /// NOT USING
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "VIEW_SERVICES") {
            $rangeid = $this->form_vars["rangeid"];
            $serviceRangeMapping = new ServiceRangeMappingFilter();
            $servicelist = $serviceRangeMapping->getServicesList($rangeid);
            $html = '<div class="row"><div class="col-md-12"><table class="table table-bordered table-hover"><thead><tr><th>Service Name</th><th>Agent Name</th></tr></thead><tbody>';
            foreach ($servicelist as $services) {
                $html .= '<tr><td><img src=\'../images/carrierlogo/thumbnail/owe_16_' . $services->getLogo() . '\' /> ' . $services->getServiceName() . '</td>'
                        . '<td>' . $services->getAgentName() . '</td>'
                        . '</tr>';
            }

            $html .= '</tbody></table></div></div>';
            echo $html;
            die;
        } 
        
        /// NOT USING
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "ADD_COUNTRYRANGE") {
            $rangeid = $this->form_vars["rangeid"];
            $rangeName = $this->form_vars["rangename"];
            
            $licenceplate = new LicencePlate($rangeid);
            $countryFilter = new CountryFilter();
            $countryFilter->addFilter(" id in (".$licenceplate->getCountryList().")");
            $zoneCountries = $countryFilter->getList();
            $output .= '<div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>';
            if(!empty($zoneCountries)){
                $i = 0;
                foreach($zoneCountries as $zoneCountry){
                    if ($i % 4 == 0) {
                        $output .= '</tr><tr>';
                    }
                    $output .= '<td>';
                    $output .= '<img src="/assets/global/img/flags/' . strtolower($zoneCountry->getIso()) . '.png" />&nbsp;&nbsp;'.$zoneCountry->getName();
                    $output .= '</td>';
                    $i++;
                }
            }else{
                $output .= '<tr><td>No country found.</td></tr>';
            }
            $output .= '                </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>';
            echo $output;
            exit;
        } 
        else if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "Save_Country_Range") {
            $countryId = $this->form_vars["country"];
            $endRange = $this->form_vars["end_range"];
            $licenceplate_id = $this->form_vars["licenceplate_id"];
            $prefix = $this->form_vars["prefix"];
            $rangename = $this->form_vars["range_name"];
            $start_range = $this->form_vars["start_range"];
            $suffix = $this->form_vars["suffix"];
            $reminder_limit = $this->form_vars["reminder_limit"];
            

            $output = array();
            if ($countryId > 0 && $licenceplate_id > 0 && $rangename != '' && $start_range != '' && $endRange != "") {
                $licencePlateCountryobj = new LicencePlateCountryFilter();
                $licencePlateCountryobj->addFilter(" country_id = '" . $countryId . "' and licence_plate_id = '" . $licenceplate_id . "'");
                $licenceplateCountryList = $licencePlateCountryobj->getList();
                if (count($licenceplateCountryList) > 0) {
                    $oldrangeStart = $licenceplateCountryList[0]->getRangeStart();
                    $oldrangeEnd = $licenceplateCountryList[0]->getRangeEnd();
                    $oldNextNumber = $licenceplateCountryList[0]->getNextNumber();
                    $oldRangeName = $licenceplateCountryList[0]->getRangeName();
                    $oldPrefix = $licenceplateCountryList[0]->getPrefix();
                    $oldSuffix = $licenceplateCountryList[0]->getSufix();

                    $licencePlateObj = $licenceplateCountryList[0];
                } else {
                    $licencePlateObj = new LicencePlateCountry();
                    $licencePlateObj->setDateCreated(time());
                    $licencePlateObj->setAddedBy($this->user->getId());
                }
                $licencePlateObj->setCountryId($countryId);
                $licencePlateObj->setLicencePlateId($licenceplate_id);
                $licencePlateObj->setRangeStart($start_range);
                $licencePlateObj->setRangeEnd($endRange);
                $licencePlateObj->setNextNumber($start_range);
                $licencePlateObj->setRangeName($rangename);
                $licencePlateObj->setPrefix($prefix);
                $licencePlateObj->setSufix($suffix);
                $licencePlateObj->setRangeReminderLimit($reminder_limit);
                
                $licencePlateObj->setDateUpdated(time());
                $licencePlateObj->setUpdatedBy($this->user->getId());
                $licencePlateObj->save();
                $output["status"] = "success";
                $output["message"] = '<div class="alert alert-success">' . formatMessages(SUCCESS_COUNTRY_RANGE_ADDED) . '</div>';
            } else {
                $output["status"] = "error";
                $output["message"] = '<div class="alert alert-danger">' . formatMessages(ERROR_REQUIRED_FILEDS_EMPTY) . '</div>';
            }
            echo json_encode($output);
            exit;
        }

        if (isset($_GET['action']) && $_GET['action'] == "licenceplate_ajax") {
            // Get current user
            $this->licencePlate = new LicencePlateFilter();
            $this->licencePlate->addServiceRangeMappingJoin();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                $service_id  = $this->form_vars['search_service'];
                if (!empty($service_id)) {
                    $this->licencePlate->addFieldFilter('s.service_id', $service_id);
                }
                $agent_id  = $this->form_vars['search_agent'];
                if (!empty($agent_id)) {
                    $this->licencePlate->addFieldFilter('s.agent_id', $agent_id);
                }
                $name = $this->form_vars['name'];
                if (!empty($name)) {
                    $this->licencePlate->addFieldLikeFilter('l.range_name', $name);
                }
                $start_range = $this->form_vars['start_range'];
                if (!empty($start_range)) {
                    $this->licencePlate->addFieldFilter('l.range_start', $start_range);
                }
                $end_range = $this->form_vars['end_range'];
                if (!empty($end_range)) {
                    $this->licencePlate->addFieldFilter('ls.range_end', $end_range);
                }
                $next_range = $this->form_vars['next_range'];
                if (!empty($next_range)) {
                    $this->licencePlate->addFieldFilter('l.next_number', $next_range);
                }
                $prefix = $this->form_vars['prefix'];
                if (!empty($prefix)) {
                    $this->licencePlate->addFieldFilter('l.prefix', $prefix);
                }
                $suffix = $this->form_vars['suffix'];
                if (!empty($suffix)) {
                    $this->licencePlate->addFieldFilter('l.sufix', $suffix);
                }
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $this->licencePlate->AddOrderBy(strtolower("l." . $dataTableColumnName), $orderFalse);
            }

            $iTotalRecords = $this->licencePlate->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->licencePlate->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->licencePlate->setOffset($iDisplayStart);

            if ($dataTableColumnName != '') {
                $this->licencePlate->AddOrderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->licencePlate->AddOrderBy('l.id', false);
            }
            $range_list = $this->licencePlate->getPagingList("l.*");
           
            $rangeDataArr = array();
            foreach ($range_list as $range) {
                $rangeArr['actionss'] = '';
                $rangeArr['actionss'] .= "<a data-id =" . $range->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-pencil'></span> </a>";
                $rangeArr['actionss'] .= "<a href='' class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $range->getId() . "' data-log_name='licence_plate' data-toggle='modal'> <span class='fa fa-list'></span> </a>";
//                $rangeArr['actionss'] .= '<a class=" assignranges btn-xs blue btn mt-ladda-btn ladda-button btn-outline" rel="tooltip" 
//                                                    data-toggle="modal" data-target="#view-ranges-popup" role="dialog" tabindex="-1" 
//                                                    data-rangeid="' . $range->getId() . '"
//                                                    data-rangename="' . $range->getRangeName() . '"
//                                                    href="javascript:;">
//                                                    <span class=" fa fa-retweet"></span>
//                                                </a>';
//                $rangeArr['actionss'] .= '<a class=" viewservices btn-xs blue btn mt-ladda-btn ladda-button btn-outline" rel="tooltip" 
//                                                    data-toggle="modal" data-target="#view-services-popup" role="dialog" tabindex="-1" 
//                                                    data-rangeid="' . $range->getId() . '"
//                                                    data-rangename="' . $range->getRangeName() . '"
//                                                    data-action = "VIEW_SERVICES"
//                                                    data-load = "add_ranges.php"
//                                                    href="javascript:;">
//                                                    <span class=" fa fa-eye"></span>
//                                                </a>';
                if ($range->getCountryRange() == 1) {
                    $rangeArr['actionss'] .= '<a class=" addCountryRanges btn-xs blue btn mt-ladda-btn ladda-button btn-outline" rel="tooltip" 
                                                    data-toggle="modal" data-target="#add-countryrange-popup" role="dialog" tabindex="-1" 
                                                    data-rangeid="' . $range->getId() . '"
                                                    data-rangename="' . $range->getRangeName() . '"
                                                    data-action = "ADD_COUNTRYRANGE"
                                                    data-load = "add_ranges.php"
                                                    href="javascript:;">
                                                    <span class=" fa fa-globe"></span>
                                                </a>';
                }
                $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
                $serviceRangeMappingList = $serviceRangeMappingFilter->getServicesList($range->getId());
                if(count($serviceRangeMappingList) > 0)
                {
                    $serviceName = array();
                    foreach($serviceRangeMappingList as $serviceList)
                    {
                        $serviceName[] = $serviceList->getServiceName();
                        $agentName = $serviceList->getAgentName();
                    }
                }
                $rangeArr['service_id'] = implode("<br />", $serviceName);
                $rangeArr['agent_id'] = $agentName;
                $rangeArr['range_name'] = $range->getRangeName();
                $rangeArr['range_start'] = $range->getRangeStart();
                $rangeArr['range_end'] = $range->getRangeEnd();
                $rangeArr['next_number'] = $range->getNextNumber();
                $rangeArr['prefix'] = $range->getPrefix();
                $rangeArr['sufix'] = $range->getSufix();
                $rangeDataArr[] = $rangeArr;
            }
            $rangeDataArr['data'] = $rangeDataArr;
            $rangeDataArr['draw'] = $sEcho;
            $rangeDataArr['recordsTotal'] = $iTotalRecords;
            $rangeDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($rangeDataArr);
            die;
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>

        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />

        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />



        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>


        <?php
    }

    protected function renderFooter() {
        ?>
        <script>
            $('#countryrange').on('switchChange.bootstrapSwitch', function (event, state) {
                if (state) {
                    $("#show_country_div").show();
                } else {
                    $('#show_country_div').hide();
                }
            });
        <?php if ($this->isCountry == '0') { ?>
                $("#show_country_div").hide();
        <?php } ?>
            $(document).ready(function () {

                $('.multiselect_drop_down').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='Type to search'>",
                    afterInit: function (ms) {
                        var that = this,
                                $selectableSearch = that.$selectableUl.prev(),
                                $selectionSearch = that.$selectionUl.prev(),
                                selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                                selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';
                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                                .on('keydown', function (e) {
                                    if (e.which === 40) {
                                        that.$selectableUl.focus();
                                        return false;
                                    }
                                });

                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                                .on('keydown', function (e) {
                                    if (e.which == 40) {
                                        that.$selectionUl.focus();
                                        return false;
                                    }
                                });
                    },
                    afterSelect: function (values) {
                        this.qs1.cache();
                        this.qs2.cache();
                    },
                    afterDeselect: function (values) {
                        this.qs1.cache();
                        this.qs2.cache();
                    }
                });

                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "add_ranges.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $("#rangeForm")[0].reset();
                    });
                });
                


                $(document).on('click', '#save_country_range', function () {
                    $.ajax({
                        method: "POST",
                        url: "add_ranges.php",
                        data: $('#CountryForm').serialize()

                    }).done(function (data) {
                        var t = JSON.parse(data);
                        $('#errorMessage').html(t.message);
                        $("#CountryForm")[0].reset();
                        $("div").removeClass("hidden");
                        $('#licenceplate_id').val("");
                    });
                    return false;

                });

                $(document).on('click', '#cancel_country', function () {
                    $("#CountryForm")[0].reset();
                    return true;
                });


                $(document).on('click', '.btnedit', function () {
                      $("#res_message div.alert").removeClass('alert-success');
                        $("#res_message div.alert").removeClass('alert-danger');
                    var e = $(this);
                    var recordid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "add_ranges.php",
                        data: {recordid: recordid, func: "editrecord"}
                    }).done(function (data) {

                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");
                        $('#range_name').val(t.range_name);
                        $('#start_range').val(t.start_range);
                        $('#end_range').val(t.end_range);
                        $('#next_number').val(t.next_number);
                        $('#prefix').val(t.prefix);
                        $('#suffix').val(t.suffix);
                        $('#reminder_limit').val(t.reminder_limit);
                        $('#zone_countries').multiSelect('select', t.zone_countries);
                        $('#service_id').val(t.service_id).trigger('change');
                        $('#agent_id').val(t.agent_id).trigger('change');
                        

                        var countryRange = t.country_range;
                        if (countryRange == '1')
                        {
                            $('#countryrange').attr('checked', true);
                            $('#countryrange').bootstrapSwitch('state', true);
                            
                        } else
                        {
                            $('#countryrange').attr('checked', false);
                            $('#countryrange').bootstrapSwitch('state', false);
                        }

                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
                    });
                });

            });
            
           
           var grid = "";
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
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
                                "url": "add_ranges.php?action=licenceplate_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "service_id"},
                                {"data": "agent_id"},
                                {"data": "range_name"},
                                {"data": "range_start"},
                                {"data": "range_end"},
                                {"data": "next_number"},
                                {"data": "prefix"},
                                {"data": "sufix"},
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
            
             $('#btn_Save').click(function () {
                    $.post("add_ranges.php", $("#rangeForm").serialize(), function (response) {
                       
                        $("#res_message div.alert").removeClass('alert-success');
                        $("#res_message div.alert").removeClass('alert-danger');
                        if (response.STATUS == "success") {
                            $.unblockUI();
                            $("#res_message div.alert").addClass('alert-success');
                            $("#res_message div.alert").html(response.MESSAGE);
                            $("#res_message").show();
                            $("#rangeForm")[0].reset();
                            $('#id').val("");
                            grid.getDataTable().ajax.reload();
                            $('#service_id').val('').trigger('change');
                            $('#agent_id').val('').trigger('change');
                            $('#zone_countries').val("");
                            $('#zone_countries').multiSelect('refresh');
                            $('#zone_countries').multiSelect('select', []);

                        } else
                        {
                            $("#res_message div.alert").addClass('alert-danger');
                            $("#res_message div.alert").html(response.MESSAGE);
                            $("#res_message").show();
                        }
                    }, "json");
                });

            $(document).ready(function () {
                DataTableFun.init();

                $(document).on('click', '.assignranges', function () {
                    var e = $(this);
                    var rangename = e.data('rangename');
                    var rangeid = e.data('rangeid');
                    $("#rangeName").html(rangename);
                    $('#range_id').val(rangeid);
                });

                $(document).on('click', '.viewservices', function () {
                    var e = $(this);
                    var rangename = e.data('rangename');
                    var rangeid = e.data('rangeid');
                    var action = e.data('action');
                    var url = e.data('load');
                    $("#rname").html(rangename);
                    $.post(url, {func: action, rangeid: rangeid
                    }, function (d) {
                        $("#range-content-display").html(d);
                    });
                });

                $(document).on('click', '.addCountryRanges', function () {
                    var e = $(this);
                    var rangename = e.data('rangename');
                    var rangeid = e.data('rangeid');
                    var action = e.data('action');
                    var url = e.data('load');
                    $("#rname").html(rangename);
                    $.post(url, {func: action, rangeid: rangeid
                    }, function (d) {
                        $("#countryrange-content-display").html(d);
                    });
                });
                $('#save_bulk').click(function () {
                    var range_id = $('#range_id').val();
                    var service_id = $('#services').val();

                    var agent_id = $('#agent_id').val();
                    var form_data = new FormData();
                    form_data.append('action', 'ASSIGN_RANGE');
                    form_data.append('service_id', service_id);
                    form_data.append('agent_id', agent_id);
                    form_data.append('range_id', range_id);

                    $.ajax({
                        url: "add_ranges.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == "success")
                            {
                                $('#display_error').html(response.message);
                                $('#services').val('');
                                $('#agent_id').val("");
                            } else
                            {
                                $('#display_error').html(response.message);
                                return false;
                            }
                        }
                    });

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
                        <div class="caption"><i class="fa fa-plus"></i>
                                Add Tracking Number Ranges
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
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        
                                      <div class="has-float-label input-icon right">
            <?php
            //echo Ddl::generateServiceDDLWithImage('service_id',$service_id,'id', ' class="bs-select input-sm form-control form-filter " required="" data-show-subtext="true"','service_id','','name','Select Service'); 
            echo Ddl::generateDDL('service_id[]', 'ServiceFilter', "AND active = '1'", 'name', 'id', $selected_value, ' class="form-control select2 " multiple required', "Please select Services", "", "service_id", "service_id");
            //    echo Ddl::generateDDL($serviceName, 'ServiceFilter', "AND active = '1'", 'name', 'id', $selected_value, ' class="form-control select2 service_select"', "Please select service", "", "service", "service");
            ?>
                                           <label >Services <span class="red-18">*</span> </label> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 

                                  
                                      <div class="has-float-label input-icon right">

                                         
            <?php
            echo Ddl::generateDDL('agent_id', 'AgentDataFilter', "( agent_type = 'carrier' OR agent_type = 'both')", 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select" required', "Please select agent", "", "agent_id", "Agent");
            ?>
                                                <label>Agent <span class="red-18">*</span> </label>  
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group"> 
                                         <div class="has-float-label input-icon right">
                                      
                                     
                                          <i class="fa fa-map-marker"></i> 
                                            <input name="range_name" id="range_name" value="" size="50" class="form-control" title="Range Name" maxlength="35" placeholder="Range Name" rel="tooltip" data-original-title="Range Name" type="text" required>
                                              <label for="range_name">Range Name</label>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <div class="has-float-label input-icon right">
                                       
                                    
                                            <i class="fa fa-map-marker"></i> 
                                            <input name="start_range" id="start_range" value="" size="50" class="form-control" title="Range Start" maxlength="35" placeholder="Range Start" rel="tooltip" data-original-title="Range Start" type="text" required>
                                             <label for="start_range">Range Start</label>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <div class="has-float-label input-icon right">
                                        
                                      
                                            <i class="fa fa-map-marker"></i> 
                                            <input name="end_range" id="end_range" value="" size="50" class="form-control" title="Range End" maxlength="35" placeholder="Range End" rel="tooltip" data-original-title="Range End" type="text" required>
                                            <label for="end_range">Range End</label>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                         <div class="has-float-label input-icon right">
                                        
                                        <i class="fa fa-map-marker"></i>
                                            <input name="next_number" id="next_number" value="" size="50" class="form-control" title="Next Range" maxlength="35" placeholder="Next Range" rel="tooltip" data-original-title="Next Range" type="text" required>
                                            <label for="next_number">Next Range</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <div class="has-float-label input-icon right">
                                        
                                      <i class="fa fa-map-marker"></i> 
                                            <input name="prefix" id="prefix" value="" size="50" class="form-control" title="Prefix" maxlength="35" placeholder="Prefix" rel="tooltip" data-original-title="Prefix" type="text">
                                            <label for="prefix">Prefix</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                       
                                       <div class="has-float-label input-icon right">
                                            <i class="fa fa-map-marker"></i> 
                                            <input name="suffix" id="suffix" value="" size="50" class="form-control" title="Suffix" maxlength="35" placeholder="Suffix" rel="tooltip" data-original-title="Suffix" type="text">
                                             <label for="suffix">Suffix</label>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                         
                                          <div class="has-float-label input-icon right">
                                        <i class="fa fa-map-marker"></i> 
                                              <input name="reminder_limit" id="reminder_limit" value="" size="50" class="form-control" title="Reminder Limit" maxlength="35" placeholder="Reminder Limit" rel="tooltip" data-original-title="Reminder Limit" type="text">
                                               <label for="reminder_limit">Reminder Limit</label>
                                          </div>
                                    </div>
                                  </div> 
                                <div class="col-sm-3 ">
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                        <label>Country wise Range</label>
                                        <div class="md-radio-inline">
                                            <input <?php echo ($countryrange == '1' ? 'checked="checked"' : ''); ?> name="countryrange" id="countryrange" type="checkbox" class="make-switch"  data-on-text="Yes" data-off-text="No" data-on-color="primary"  data-size="small">
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                            <div id="show_country_div">
                                <div class="row customized_cls" >
                                    <div class="col-md-12 full-width-multiselect">
                                        <label>Zone Countries</label>
                                        <?php
                                        $selected_countries = array();
                                        echo Ddl::generateDDL('zone_countries[]', 'CountryFilter', array('active' => '1', 'deletedq' => 'N'), 'name', 'id', $selected_countries, 'class="multi-select multiselect_drop_down" multiple="multiple"', '', '', 'zone_countries');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div style="clear:both"></div> 
                            <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                            <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                            <br />
                            <div class="row " style="text-align:centre;" align="center">
                                <div class="col-md-12">
                                    <input id="btn_Save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                                    <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                                </div>
                            </div>   
                        </form>
                    </div>
                </div>
        <?php } ?>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>
                            Ranges List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>Service</th>
                                    <th>Agent</th>
                                    <th>Name</th>
                                    <th>Start Range</th>
                                    <th>End Range</th>
                                    <th>Next Range</th>
                                    <th>Prefix</th>
                                    <th>Suffix</th>

                                </tr>
                                <tr role="row" class="filter">
                                    <td width = "6%">
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td class="user_acccount_correct_button">
                                       <?php
                                       echo Ddl::generateServiceDDLWithImage('search_service', $search_service, 'id', ' class="bs-select input-sm form-control form-filter" data-container="body" data-live-search="true"  data-show-subtext="true"','','',''); 
                                       ?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo Ddl::generateDDL('search_agent', 'AgentDataFilter', "agent_type = 'carrier'", 'agent_name', 'id', $selected_value, ' class="form-control select2 form-filter" ', "Please select agent", "", "search_agent", "Agent");
                                        ?>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="name" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="number" class="form-control form-filter" name="start_range" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="number" class="form-control form-filter" name="end_range" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="number" class="form-control form-filter" name="next_range" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="prefix" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="suffix" />
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                </div>
            </div>
        </div>
        <form name="adminForm" id="adminForm" action="" method="POST">           
            <div class="modal fade" tabindex="-1" role="dialog" id="view-ranges-popup" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="rangeName"></span> Assign Ranges</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" id="display_error">

                                </div>
                            </div>
                            <div class="row" >

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label >Services</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon select2-bootstrap-append"> <i class="fa fa-user"></i> </div>
        <?php
        //echo Ddl::generateServiceDDLWithImage('services',$services,'id', ' class="form-control select2 " multiple required="" data-show-subtext="true"'); 
        
        echo Ddl::generateDDL('services[]', 'ServiceFilter', "AND active = '1'", 'name', 'id', $selected_value, ' class="form-control select2 " multiple required', "Please select Services", "", "services", "services");
        ?>
                                            <span class="input-group-addon red-18">*</span> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group"> 
                                        <label>Agent</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
        <?php
        // Add filter according to new logic
//                                                echo Ddl::generateDDL('agent_id', 'AgentDataFilter', "agent_type = 'carrier'", 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select" required', "Please select agent", "", "agent_id", "Agent");
        echo Ddl::generateDDL('agent_id', 'AgentDataFilter', " ", 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select" required', "Please select agent", "", "agent_id", "Agent");
        ?>

                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="range_id" id="range_id" value="" />
                            <a id="save_bulk"   href="javascript:;" class="btn btn-primary"><span></span>Save</a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>
        <div class="modal fade" tabindex="-1" role="dialog" id="view-services-popup" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="rname"></span> Range Services List</h4>
                    </div>
                    <div class="modal-body" id="range-content-display">

                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="range_id" id="range_id" value="" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>

        <form name="CountryForm" id="CountryForm" action="" method="POST">           
            <div class="modal fade" tabindex="-1" role="dialog" id="add-countryrange-popup" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="rangeName"></span> Range Country</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12" id="errorMessage">

                                </div>
                            </div>
                            <div class="modal-body" id="countryrange-content-display">

                            </div>
                        </div>
                        <div class="modal-footer">                       
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
