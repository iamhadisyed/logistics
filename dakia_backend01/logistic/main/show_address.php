<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'address.class',
    'addressfilter.class']);
class Page extends BasePage {

    private $user = null;
    private $id = '';
    private $address_filter = '';

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'client_list.php' => Translation::GetCaption("ADDRESS"),
            Translation::GetCaption("LIST")
        );
        $this->user = $user = SessionManager::getUser();

        if (isset($_GET['action']) && $_GET['action'] == "address_ajax") {
            $addressFilter = new AddressFilter();
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

                $functionName = 'AddOrderBy' . $dataTableColumnName;
                $addressFilter->$functionName($orderFalse);
            }

            /*
             * Column filter
             * For search
             */

            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $searchCompany = $this->form_vars['search_company'];
                if (!empty($searchCompany))
                    $addressFilter->addFieldLikeFilter('company', $searchCompany);

                $searchContact = $this->form_vars['search_contact'];
                if (!empty($searchContact))
                    $addressFilter->addFieldLikeFilter('contact', $searchContact);

                $searchAddress = $this->form_vars['search_address'];
                if (!empty($searchAddress))
                    $addressFilter->addFilter("(address_line_1 LIKE '%".$searchAddress."%' OR address_line_2 LIKE '%".$searchAddress."%' OR address_line_3 LIKE '%".$searchAddress."%')");



                $searchCity = $this->form_vars['search_city'];
                if (!empty($searchCity))
                    $addressFilter->addFieldLikeFilter('city', $searchCity);

                $searchCountry = $this->form_vars['search_country'];
                if (!empty($searchCountry))
                    $addressFilter->addFieldLikeFilter('country', $searchCountry);

                $searchPostcode = $this->form_vars['search_postcode'];
                if (!empty($searchPostcode))
                    $addressFilter->addFieldLikeFilter('postcode', $searchPostcode);
            }
            $addressFilter->addFieldFilter("user_id", $this->user->getId());

            $iTotalRecords = $addressFilter->getPagingCount();
            //Paginatiopn code start here 
            $addressDataArr = array();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $addressFilter->setRowsPerPage($iDisplayLength);
            $addressFilter->setOffset($iDisplayStart);


            $addressObjs = $addressFilter->getPagingList(" a.phone_number, a.id, a.company, a.contact, a.address_line_1, a.address_line_2, a.address_line_3,a.city,a.country, c.name 'country_name',a.postcode");
            foreach ($addressObjs as $addressObj) {
                $addressArr = array();
                $addressArr['option'] .= "<a data-id =" . $addressObj->getId() . " class='btnedit btn btn-xs blue btn-outline' rel='tooltip' data-original-title='Edit'><span class='fa fa-pencil'></span> </a>";
                $addressArr['option'] .= "<a href='#' data-id =" . $addressObj->getId() . "  class='btndelete btn btn-xs red btn-outline' rel='tooltip' data-original-title='Delete'><span class='fa fa-times'></a>";
                $addressArr['company'] = $addressObj->getCompany();
                $addressArr['contact'] = $addressObj->getContact();
                $addressArr['address_line_1'] = $addressObj->getAddressLine1() . ' ' . $addressObj->getAddressLine2() . ' ' . $addressObj->getAddressLine3();
                $addressArr['city'] = $addressObj->getCity();
                $addressArr['country'] = $addressObj->getCountryName();
                $addressArr['country_iso'] = $addressObj->getCountry();
                $addressArr['postcode'] = $addressObj->getPostcode();
                $addressCurrArr = base64_encode(json_encode($addressArr));
                unset($addressArr['country_iso']);

                $addressDataArr [] = $addressArr;
            }

            $addressDataArrJson['data'] = $addressDataArr;
            $addressDataArrJson['draw'] = $sEcho;
            $addressDataArrJson['recordsTotal'] = $iTotalRecords;
            $addressDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($addressDataArrJson);
            die;
        }
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == "saveAddress") {
            $output = array();
            $company = $this->form_vars["company"];
            $contact = $this->form_vars["contact"];
            $addressLine1 = $this->form_vars["addressLine1"];
            $addressLine2 = $this->form_vars["addressLine2"];
            $addressLine3 = $this->form_vars["addressLine3"];
            $city = $this->form_vars["city"];
            $state = $this->form_vars["state"];
            $postcode = $this->form_vars["postcode"];
            $country = $this->form_vars["country"];
            $telephone = $this->form_vars["telephone"];


            $address2 = new Address(intval($this->form_vars["id"]));
            $address2->setAddressLine1($addressLine1);
            $address2->setAddressLine2($addressLine2);
            $address2->setAddressLine3($addressLine3);
            $address2->setCity($city);
            $address2->setCompany($company);
            $address2->setCountry($country);
            $address2->setContact($contact);
            $address2->setPhoneNumber(str_replace(" ", "", $telephone));
            $address2->setPostCode($postcode);
            $address2->setUserId($this->user->getId());
            $address2->save();
            $output["status"] = "success";
            $output["message"] = formatMessages(SUCCESS_ADDRESS_ADDED);

            echo json_encode($output);
            die;
        }
        else if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "editrecord") {

            $id = $this->form_vars['recordid'];
            $this->id = $id;
            $edit_array = array();
            $address = new Address($id);
            $edit_array['id'] = (int) $id;
            $edit_array['company'] = $address->getCompany();
            $edit_array['contact'] = $address->getContact();
            $edit_array['addressLine1'] = $address->getAddressLine1();
            $edit_array['addressLine2'] = $address->getAddressLine2();
            $edit_array['addressLine3'] = $address->getAddressLine3();
            $edit_array['city'] = $address->getCity();
            $edit_array['state'] = $address->getState();
            $edit_array['postcode'] = $address->getPostCode();
            $edit_array['country'] = $address->getCountry();
            $edit_array['telephone'] = $address->getPhoneNumber();
            echo json_encode($edit_array);
            die;
        }
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "deleterecord") {
            $delete_id = $_POST['recordid'];
            $address = new Address($delete_id);
            $address->delete();
            die;
        }
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == "exportList") {
            $addressFilter = new AddressFilter();
            $addressFilter->addUserFilter($this->user->getId());
            $searchCompany = $this->form_vars['search_company'];
            if (!empty($searchCompany))
                $addressFilter->addFieldLikeFilter('company', $searchCompany);

            $searchContact = $this->form_vars['search_contact'];
            if (!empty($searchContact))
                $addressFilter->addFieldLikeFilter('contact', $searchContact);

            $searchAddress = $this->form_vars['search_address'];
            if (!empty($searchAddress))
                $addressFilter->addFilter("(address_line_1 LIKE '%".$searchAddress."%' OR address_line_2 LIKE '%".$searchAddress."%' OR address_line_3 LIKE '%".$searchAddress."%')");


            $searchCity = $this->form_vars['search_city'];
            if (!empty($searchCity))
                $addressFilter->addFieldLikeFilter('city', $searchCity);

            $searchCountry = $this->form_vars['search_country'];
            if (!empty($searchCountry))
                $addressFilter->addFieldLikeFilter('country', $searchCountry);

            $searchPostcode = $this->form_vars['search_postcode'];
            if (!empty($searchPostcode))
                $addressFilter->addFieldLikeFilter('postcode', $searchPostcode);

            $addressFilter->addUserFilter($this->user->getId());
            $address_list = $addressFilter->getList("a.id, company, contact, address_line_1, address_line_2,address_line_3, city,state, c.name as country_name, postcode, phone_number");

            if (count($address_list) > 0) {
                $cr = "\r\n";
                $count = 1;
                $csv = "";
                $csvHeader = "company, contact, address_line_1, address_line_2, address_line_3, city,state, country, postcode, phone_number ";
                foreach ($address_list as $list) {
                    $csv .= cleanCsvCall($list->getCompany()) . ",";
                    $csv .= cleanCsvCall($list->getContact()) . ",";
                    $csv .= cleanCsvCall($list->getAddressLine1()) . ",";
                    $csv .= cleanCsvCall($list->getAddressLine2()) . ",";
                    $csv .= cleanCsvCall($list->getAddressLine3()) . ",";
                    $csv .= cleanCsvCall($list->getCity()) . ",";
                    $csv .= cleanCsvCall($list->getState()) . ",";
                    $csv .= cleanCsvCall($list->getCountryName()) . ",";
                    $csv .= cleanCsvCall($list->getPostcode()) . ",";
                    $csv .= cleanCsvCall($list->getPhoneNumber()) . ",";
                    $csv .= $cr;
                }

                $data = $csvHeader . $cr . $csv;
                $uniqueFileName = "export_addresses_" . date("YmdHis");
                $folder_path = "../_assets/csv";
                $file_path1 = $folder_path . "/" . $uniqueFileName . ".csv";
                $file_path = fopen($file_path1, 'w');
                fwrite($file_path, $data);
                fclose($file_path);
                $outputArray["status"] = "success";
                $outputArray["message"] = $file_path1;
            } else {
                $outputArray["status"] = "fail";
                $outputArray["message"] = "You cannot download empyt data file. No record found.";
            }
            echo json_encode($outputArray);
            die;
        }

        // FORM POSTED BACK?
        if (isset($this->form_vars["form_action"])) {
            // load session copies of class variables

            $this->address_filter = $_SESSION["address_filter"];
            $this->table_msg = $_SESSION["client_table_msg"];

            // take appropriate action
            switch ($this->form_vars["form_action"]) {


                case "show_invalid":
                    $delete_array = $_POST['deleteConsignments'];

                    for ($start = 0; $start <= count($delete_array) - 1; $start++) {
                        $deleteId = $delete_array[$start];

                        $address = new Address2($deleteId);

                        $_SESSION["deleteId"] = $deleteId;

                        //  util_redirect("consignment_edit.php?from=show_address&id= $address);
                        if ($user->getIsProduct() == 'YES')
                            util_redirect("product_consignment_edit.php?from=show_address");
                        else
                            util_redirect("consignment_edit.php?from=show_address");
                    }
                    break;



                // IMPORT
                case "import":
                    util_redirect("../main/client_file.php");
                    break;

                case "csv":
                    util_redirect("../main/address_csv.php");
                    break;

                // SEARCH
                case "search":
                    util_redirect("../main/searchaddress.php?from=client");
                    break;


                case "delete":



                    /* $this->address_filter = SessionManager::getAddressFilter();
                      $this->address_filter->addAccountFilter($_SESSION["user_account"]);
                      echo "delete";
                      die;
                      $consignment_array = $this->address_filter->getList(); */




                    $delete_array = $_POST['deleteConsignments'];

                    //print_r($delete_array);    
                    //die;
                    if (sizeof($delete_array) > 0) {
                        for ($start = 0; $start <= count($delete_array) - 1; $start++) {
                            $deleteId = $delete_array[$start];
                            $address = new Address2($deleteId);
                            $address->delete();
                        }
                    }

                    break;






                // unrecognised command
                default:
                    break;
            }  // switch()
        }


        // common initialisation for ths page
        $this->setTitle("Client Consignment List");
        // keep session copies of class variables
        $_SESSION["address_filter"] = $this->address_filter;
        $_SESSION["client_table_msg"] = $this->table_msg;
        // Check message
        if ($this->table_msg == "")
            $this->table_msg = "Consignments Listed";
    }

    public function renderHead() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />    
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderFooter() {
        ?>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        <script type="text/javascript">
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
                        [20, 50, 100, 150],
                        [20, 50, 100, 150] // change per page values here 
                    ],
                    "pageLength": 20, // default record count per page
                    "ajax": {
                        "url": "show_address.php?action=address_ajax", // ajax source
                        headers: {
                            
                        },
                    },
                    "bStateSave": true,
                    "columns": [
                        {"data": "option", "bSortable": false},
                        {"data": "company"},
                        {"data": "contact"},
                        {"data": "country"},
                        {"data": "address_line_1", "bSortable": false},
                        {"data": "city"},
                        {"data": "postcode"},
                    ],
                    "initComplete": function( settings, json ) {
                        $('a').tooltip();
                      },
                    "fnDrawCallback": function (oSettings) {
                        $('a').tooltip();
                    }
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
    
  
        var color_on = '#EEEEEE';
        var color_off='#FFFFFF';
        var count = 0;
        function checkedAll (group) 
        {
            if (count == 0) 
            {
                for (var i=0, len = group.length; i < len; i++) 
                {
                    group[i].checked = true;
                    count = 1;
                }
            }
            else 
            {
                for (var i=0, len = group.length; i < len; i++)
                {
                    group[i].checked = false;
                    count = 0;
                }
            }
        }
        function win(){
            window.opener.location.href="consignment_edit.php?from=show_address";
            self.close();
        }   
        function row_color(pTarget)
        {
            var pTR = pTarget.parentNode.parentNode;

            if(pTR.nodeName.toLowerCase() != 'tr')
            {
                return;
            }

            if(pTarget.checked == true)
            {
                color_off= pTR.style.backgroundColor;
                pTR.style.backgroundColor = color_on;

            }
            else
            {
                pTR.style.backgroundColor = color_off;
            }
        }

    $(document).ready(function(){
       $('button').tooltip();
       $('a').tooltip();
       $(document).on('click', '.btnedit', function () {
            var e = $(this);
            var recordid = e.data('id');
            $.ajax({
                method: "POST",
                url: "show_address.php",
                data: {recordid: recordid, action: "editrecord"}
            }).done(function (data) {
                //By using javasript json parse
                var t = JSON.parse(data);

                $("#btnSave").html("Update");
                $('#company').val(t.company);
                $('#contact').val(t.contact);
                $('#address_line_1').val(t.addressLine1);
                $('#address_line_2').val(t.addressLine2);
                $('#address_line_3').val(t.addressLine3);
                $('#city').val(t.city);
                $('#state').val(t.state);
                $('#postcode').val(t.postcode);
                $('#country').val(t.country);
                $('#telephone').val(t.telephone);
                $('#id').val(t.id);
                $('html, body').animate({scrollTop: '0px'}, 300);
                $('.filter-submit').click();
            });
        });
        $(document).on('click', '.btndelete', function () {
            var e = $(this);
            var recordid = e.data('id');
            if(confirm("Are you sure you want to delete?"))
            {
                $.ajax({
                    method: "POST",
                    url: "show_address.php",
                    data: {recordid: recordid, func: "deleterecord"}
                }).done(function (data) {
                    e.parents('tr').hide();
                    $('#error_message').hide(); 
                    $('#display_message').show();
                    $('#display_message').removeClass('alert-success').addClass('alert-danger');     
                    $('#display_message').html("Record deleted successfully.");
                    $('.filter-cancel').click();
                });
            }
            else
            {
                return false;
            }
        });
        
        $("#btnSave").click(function() {
            $('#adminForm').validator().on('submit',function (e) {
                if (e.isDefaultPrevented())
                {
                        return false;
                }
                else
                {
                    var id = $("#id").val();
                    var company = $("#company").val();
                    var contact = $("#contact").val();
                    var addressLine1 = $("#address_line_1").val();
                    var addressLine2 = $("#address_line_2").val();
                    var addressLine3 = $("#address_line_3").val();
                    var city = $("#city").val();
                    var state = $("#state").val();
                    var postcode = $("#postcode").val();
                    var country = $("#country").val();
                    var telephone = $("#telephone").val();


                    var form_data = new FormData();
                    form_data.append('id', id);
                    form_data.append('company', company);
                    form_data.append('contact', contact);
                    form_data.append('addressLine1', addressLine1);
                    form_data.append('addressLine2', addressLine2);
                    form_data.append('addressLine3', addressLine3);
                    form_data.append('city', city);
                    form_data.append('state', state);
                    form_data.append('postcode', postcode);
                    form_data.append('country', country);
                    form_data.append('telephone', telephone);
                    form_data.append('action', 'saveAddress');
                    $.ajax({
                        url: 'show_address.php',
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function(response) {
                            if(response.status == 'success'){
                               $("#adminForm").find("input[type=text], textarea, select").val("");
                               $('#error_message').show(); 
                               $('#error_message').removeClass('alert-danger').addClass('alert-success');     
                               $('#error_message').html(response.message);
                               $('.filter-cancel').click();
                            }else{
                                $('#error_message').show();
                                $('#error_message').removeClass('alert-success').addClass('alert-danger');     
                                $('#error_message').html(response.message);
                            }
                        }
                    });
                    return false;
                }
            });
                $("#adminForm").submit();
                
            });
        
        DataTableFun.init();
            
     });
    
    $('#btnExportData').click(function(){
        var form_data = new FormData();
        form_data.append('action', "exportList");

        $('.form-filter').each(function (index){
            form_data.append($(this).prop('name'), $(this).prop('value'));    
        });

        $.ajax({
            url: 'show_address.php',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(response) {
                if(response.status == 'success'){
                    $("#adminForm").find("input[type=text], textarea, select").val("");
                    $('#display_message').show(); 
                    $('#display_message').removeClass('alert-danger').addClass('alert-success');     
                    $('#display_message').html(
                            "You have successfully downloaded address data, Please "+
                            "<a class='btn btn-primary btn-xs' href='"+response.message+"' target='_blank'>click here</a> to download file."
                            );    
                }else{
                    $('#display_message').show();
                    $('#display_message').removeClass('alert-success').addClass('alert-danger');     
                    $('#display_message').html(response.message);
                }
            }
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

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                    <?php echo Translation::GetCaption("ADD_NEW_ADRESS"); ?>
                </div>
                <div class="actions"> 
                    <a href="client_list.php" class="btn blue">
                        <i class="fa fa-list"></i> <?php echo Translation::GetCaption("LIST"); ?></a>    
                    <a href="consignment_add.php" class="btn blue"  ><i class="fa fa-plus"></i> <?php echo Translation::GetCaption("ADD_NEW_SHIPMENT"); ?></a>

                </div>
            </div>
            <form method="post" enctype="multipart/form-data" id="adminForm" name="adminForm"  role="form">
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 alert alert-danger display-none"  id="error_message" ></div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6 red-18 text-right"><span class="caption-helper"><small class="text-danger"> <?php echo Translation::GetCaption("FIELDS_WITH_AN_ASTERISK_(*)_ARE_MANDATORY"); ?></small></span>  </div>
                        <div class="form-group col-md-3 retrunNotRequired">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                <div class="input-icon right">
                                    <i class="fa tooltips font-red" data-original-title="Company is mandatory">*</i>
                                    <input type="text" name="company" maxlength="30" id="company" value="<?php echo $company; ?>"  size="35"  placeholder="Company " class="form-control" rel="tooltip" title="Contact Person" required/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <div class="input-icon right">
                                        <i class="fa tooltips font-red" data-original-title="Contact is mandatory">*</i>
                                        <input name="contact" id="contact" value="" size="100" class="form-control"  maxlength="25" title="" placeholder="<?php echo Translation::GetCaption('CONTACT_PERSON'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('CONTACT_PERSON'); ?>" type="text" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <div class="input-icon right">
                                        <i class="fa tooltips font-red" data-original-title="Address Line 1 is mandatory">*</i>
                                        <input name="address_line_1" id="address_line_1" value="" size="100" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption('ADRESS_LINE_1_(STREET_&_NO)'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('ADRESS_LINE_1_(STREET_&_NO)'); ?>" type="text" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <input name="address_line_1" id="address_line_2" value="" size="100" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption('ADDRESS_LINE_2'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('ADDRESS_LINE_2'); ?>" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <input name="address_line_3" id="address_line_3" value="" size="100" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption('ADDRESS_LINE_3_COUNTRY_DIVISION_STATE'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('ADDRESS_LINE_3_COUNTRY_DIVISION_STATE'); ?>" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <div class="input-icon right">
                                        <i class="fa tooltips font-red" data-original-title="City is mandatory">*</i>
                                        <input name="city" id="city" value="" size="100" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption('CITY'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('CITY'); ?>" type="text" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <input name="state" id="state" value="" size="100" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption('STATE_REGION'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('STATE_REGION'); ?>" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <input name="postcode" id="postcode" value="" size="100" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption('POSTCODE'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('POSTCODE'); ?>" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
        <?php
        echo Ddl::generateCountryDDL('country', '', 'id');

        //echo  Country::getCountryDropDownList();
        ?>
                                    <span class="input-group-addon red-18">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                    <input name="telephone" id="telephone" value="" size="100" class="form-control"  maxlength="35" title="" placeholder="<?php echo Translation::GetCaption('TELEPHONE'); ?>" rel="tooltip" data-original-title="<?php echo Translation::GetCaption('TELEPHONE'); ?>" type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="clear:both"></div>
                    <div class="row " style="text-align:centre;" align="center">
                        <div class="col-md-12">
                            <a id="btnCancel" href="show_address.php" class="btn btn-default"><span></span><?php echo Translation::GetCaption("CANCEL"); ?></a>
                            <a id="btnSave" href="javascript:;" class="btn btn-primary"><span></span><?php echo Translation::GetCaption("SAVE"); ?></a>
                        </div>
                        <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                   <?php echo Translation::GetCaption("ADDRESS_BOOK"); ?>
                </div>
                <div class="actions"> 
                    <a href="javascript:;" class="btn blue btn-outline" id="btnExportData"> <i class="fa fa-download"></i> <?php echo Translation::GetCaption("EXPORT"); ?></a>    
                </div>
            </div>

            <div class="portlet-body">
                <!-- describe table filter -->
                <!-- CONSIGNMENT TABLE -->
                <div class="col-md-12 alert alert-danger display-none"  id="display_message" ></div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="75">Action</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Country</th>                                
                                <th>Address</th>
                                <th>City</th>                                
                                <th>Postcode</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs yellow filter-submit btn-outline margin-bottom-5" rel="tooltip" data-original-title="Search"><i class="fa fa-search"></i></button>
                                        <button  class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline" rel="tooltip" data-original-title="Cancel"><i class="fa fa-times"></i></button>
                                    </div>
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_company">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_contact">
                                </td>
                                <td>
                                <?php
                                echo Ddl::generateCountryDDL('search_country', '', 'id', ' class="form-filter bs-select form-filter form-control" required="" data-live-search="true" data-container="body" data-size="8"');
                                ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_address">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_city">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="search_postcode">
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>   
            </div>  <!-- table_container -->

          </div>
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
