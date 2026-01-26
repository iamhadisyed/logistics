<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $product;
    private $id = NULL;
    private $breadcrumb = '';

    /*     * *
     * Controller logic
     */

    protected function init() {

        if (!Permissions::checkFilePermission('products.php'))
            util_redirect("index.php");
        $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("PRODUCT")
        );

        $this->product = new ProductFilter();
        // common initialisation for ths page
        $this->setTitle("Product List");
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "DELETEPRODUCT") {

            $output = array();
            $productid = $this->form_vars["productid"];
            if ($productid > 0) {
                $produtcObj = new Products($productid);
                $produtcObj->setStatus(2);
                $produtcObj->save();
                $output["status"] = "success";
                $output["message"] = "Record deleted successfully.";
                $productLog = new ProductLog();
                $productLog->createlog($user->getId(), '', $produtcObj->getId(), 'PRODUCT', $user->getUserName() . ' has delete ' . $produtcObj->getProductName(), '', $produtcObj);
            } else {
                $output["status"] = "fail";
                $output["message"] = "Unable to delete Product.";
            }
            echo json_encode($output);
            die;
        } else if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {

            $product_id = $this->form_vars["id"];
            $product_name = $this->form_vars["product_name"];
            $insurance = $this->form_vars["insurance"];
            $description = $this->form_vars["description"];
            $from_weight = $this->form_vars["from_weight"];
            $to_weight = $this->form_vars["to_weight"];
            $max_length = $this->form_vars["max_length"];
            $max_width = $this->form_vars["max_width"];
            $max_height = $this->form_vars["max_height"];
            $max_vol_weight = $this->form_vars["max_vol_weight"];
            $vol_denominator = $this->form_vars["vol_denominator"];
            $remotearea_charges = $this->form_vars["remotearea_charges"];
            $fuel_charges = $this->form_vars["fuel_charges"];

            $country_id = $this->form_vars["country_id"];
            $transit_time = $this->form_vars["transit_time"];



            $uploadName = "";
            if (isset($_FILES["logo"]) && trim($_FILES["logo"]["name"]) != '') {
                $allowedExts = array("gif", "jpeg", "jpg", "png");
                $temp = explode(".", $_FILES["logo"]["name"]);
                $extension = end($temp);

                if ((($_FILES["logo"]["type"] == "image/gif") || ($_FILES["logo"]["type"] == "image/jpeg") || ($_FILES["logo"]["type"] == "image/jpg") || ($_FILES["logo"]["type"] == "image/pjpeg") || ($_FILES["logo"]["type"] == "image/x-png") || ($_FILES["logo"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                    if ($_FILES["logo"]["error"] > 0) {
                        $error_array[] = "Return Code: " . $_FILES["logo"]["error"] . "<br>";
                    } else {
                        $folder_path = "../images/productlogo/";
                        if (!file_exists($folder_path)) {
                            mkdir($folder_path, 0777, true);
                        }
                        $uploadName = time() . "." . $extension;
                        move_uploaded_file($_FILES["logo"]["tmp_name"], "../images/productlogo/" . $uploadName);
                        $thumb = new easyphpthumbnail;

                        $thumbnailFolder = "../images/productlogo/thumbnail/";
                        if (!file_exists($folder_path)) {
                            mkdir($thumbnailFolder, 0777, true);
                        }
                        $thumb->Thumblocation = $thumbnailFolder;
                        $thumb->Thumbprefix = 'owe_';
                        $thumb->Thumbsaveas = 'png';
                        $thumb->Thumbfilename = $uploadName;
                        //$thumb -> Clipcorner = array(2,15,0,0,1,1,0);

                        $thumb->Thumbsize = 16;
                        $thumb->Thumbprefix = 'owe_16_';
                        $thumb->Createthumb("../images/productlogo/" . $uploadName, 'file');

                        $thumb->Thumbsize = 100;
                        $thumb->Thumbprefix = 'owe_100_';
                        $thumb->Createthumb("../images/productlogo/" . $uploadName, 'file');

                        $thumb->Thumbsize = 200;
                        $thumb->Thumbprefix = 'owe_200_';
                        $thumb->Createthumb("../images/productlogo/" . $uploadName, 'file');

                        $thumb->Thumbsize = 300;
                        $thumb->Thumbprefix = 'owe_300_';
                        $thumb->Createthumb("../images/productlogo/" . $uploadName, 'file');
                    }
                } else {
                    $error_array[] = "Invalid file";
                }
            }

            if (isset($this->form_vars["active"]))
                $isActive = 1;
            if (isset($this->form_vars["is_untrack"]))
                $is_untrack = 1;

            //$status = $this->form_vars["status"];
            $error_array = array();
            $headerMessage = '';
            $delete = 0;
            $productFilter = new ProductFilter();
            if ((int) $product_id > 0)
                $productFilter->addFilter(" p.id= '" . DbAccess3::escape($product_id) . "'");
            else
                $productFilter->addFilter("product_name = '" . $product_name . "'");
            $productResult = $productFilter->getList();


            if (count($productResult) > 0) {
                if ((int) $product_id > 0) {
                    $productObj = new Products(intval($productResult[0]->getId()));
                    $oldProductObj = new Products(intval($productResult[0]->getId()));
                    $productObj->setProductName($product_name);
                    $productObj->setInsurance($insurance);
                    $productObj->setDescription($description);
                    $productObj->setStatus($isActive);
                    $productObj->setFromWeight($from_weight);
                    $productObj->setToWeight($to_weight);
                    $productObj->setLength($max_length);
                    $productObj->setWidth($max_width);
                    $productObj->setHeight($max_height);
                    $productObj->setVolWeight($max_vol_weight);
                    $productObj->setVolDenominator($vol_denominator);
                    $productObj->setRemoteareaCharges($remotearea_charges);
                    $productObj->setFuelCharges($fuel_charges);
                    $productObj->setIsUntrack($is_untrack);
                    $productObj->setCountryId($country_id);
                    $productObj->setTransitTime($transit_time);


                    if ($uploadName != '')
                        $productObj->setLogo($uploadName);
                    $productObj->save();
                    $PartnerServicesRouting = PartnerServicesRouting::updateRoutineStatus($status, $productObj->getId());
                    $product_id_log = $productObj->getId();
                    $newProductObj = $productObj;
                }
            } else {
                $productObj = new Products();
                $oldProductObj = $productObj;
                $productObj->setProductName($product_name);
                $productObj->setInsurance($insurance);
                $productObj->setDescription($description);
                $productObj->setStatus($isActive);
                $productObj->setFromWeight($from_weight);
                $productObj->setToWeight($to_weight);
                if ($uploadName != '')
                    $productObj->setLogo($uploadName);

                $productObj->setLength($max_length);
                $productObj->setWidth($max_width);
                $productObj->setHeight($max_height);
                $productObj->setVolWeight($max_vol_weight);
                $productObj->setVolDenominator($vol_denominator);
                $productObj->setRemoteareaCharges($remotearea_charges);
                $productObj->setFuelCharges($fuel_charges);
                $productObj->setIsUntrack($is_untrack);
                $productObj->setCountryId($country_id);
                $productObj->setTransitTime($transit_time);


                $productObj->setAddedDate(date("Y-m-d H:i:s"));
                $productObj->setAddedBy($user->getId());
                $productObj->save();
                $product_id_log = $productObj->getId();
                $newProductObj = $productObj;
            }
            $productLog = new ProductLog();
            if ((int) $product_id <= 0) {
                $productLog->createlog($user->getId(), '', $product_id_log, 'PRODUCT', $user->getUserName() . ' has added new product ' . $product_name, '', $newProductData);
            } else {
                $oldProductData = serialize($oldProductObj);
                $newProductData = serialize($newProductObj);
                $productLog->createlog($user->getId(), '', $product_id_log, 'PRODUCT', $user->getUserName() . ' has updated ' . $product_name, $oldProductData, $newProductData);
            }
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $product_id = $_POST['recordid'];
            $this->id = $product_id;
            $edit_array = array();
            $product = new Products($product_id);

            $edit_array['id'] = $product_id;
            $edit_array['product_name'] = $product->getProductName();
            $edit_array['insurance'] = $product->getInsurance();
            $edit_array['description'] = $product->getDescription();
            $edit_array['from_weight'] = $product->getFromWeight();
            $edit_array['to_weight'] = $product->getToWeight();
            $edit_array['logo'] = $product->getLogo();
            $edit_array['max_length'] = $product->getLength();
            $edit_array['max_width'] = $product->getWidth();
            $edit_array['max_height'] = $product->getHeight();
            $edit_array['max_vol_weight'] = $product->getVolWeight();
            $edit_array['vol_denominator'] = $product->getVolDenominator();
            $edit_array['remotearea_charges'] = $product->getRemoteareaCharges();
            $edit_array['fuel_charges'] = $product->getFuelCharges();
            $edit_array['status'] = $product->getStatus();
            $edit_array['is_untrack'] = $product->getIsUntrack();
            $edit_array['country_id'] = $product->getCountryId();
            $edit_array['transit_time'] = $product->getTransitTime();
            echo json_encode($edit_array);
            die;
        } else if (isset($this->form_vars['func']) && $this->form_vars['func'] == "GET_SERVICE_COUNTRY") {
            $productid = $this->form_vars['productid'];
            if ($productid > 0) {
                $partnerserviceRoutingFilter = new PartnerServicesRoutingFilter();
                $partnerserviceRoutingFilter->addFieldFilter("product_id", $productid);
                $partnerserviceRoutingFilter->setGroup("country_id");
                $plist = $partnerserviceRoutingFilter->getColumnList("country_id");
                if (count($plist) > 0) {
                    $output = "";
                    $output .= '<table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="4">Countries List</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                    <tr>';
                    $i = 0;

                    foreach ($plist as $sercoun) {
                        $serCountryId = $sercoun->getCountryId();
                        $country = new Country($serCountryId);
                        $countryName = $country->getName();
                        $iso = $country->getIso();

                        if ($i % 4 == 0) {
                            $output .= '</tr>';
                            $output .= '<tr>';
                        }
                        $link = '<img src=\'../assets/global/img/flags/' . strtolower($iso) . '.png\' /> ' . $countryName;
                        $output .= '<td>' . $link . '</td>';
                        $i++;
                    }
                }
                echo $output;
            }
            exit;
        }

        if (isset($_GET['action']) && $_GET['action'] == "product_ajax") {
            // Get current user
            $this->product = new ProductFilter();
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
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                if ($dataTableColumnName == "weight")
                    $dataTableColumnName = "to_weight";
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {


                $searchDateFrom = $this->form_vars['search_Date_from'];
                $searchDateTo = $this->form_vars['search_Date_to'];

                if (!empty($searchDateFrom) || !empty($searchDateTo))
                    $this->product->addDateFilter($searchDateFrom, $searchDateTo, 'submitted');


                $searchproduct_name = $this->form_vars['search_product_name'];
                if (!empty($searchproduct_name)) {
                    $this->product->addFieldLikeFilter('product_name', $searchproduct_name);
                }
                $searchinsurance = $this->form_vars['search_insurance'];
                if (!empty($searchinsurance)) {
                    $this->product->addFieldLikeFilter('insurance', $searchinsurance);
                }
                $searchstatus = $this->form_vars['search_Active'];
                if (($searchstatus) != '') {
                    $this->product->addFieldFilter('status', $searchstatus);
                }
            }
            $iTotalRecords = $this->product->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->product->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->product->setOffset($iDisplayStart);

            if ($dataTableColumnName != '') {
                $this->product->AddOrderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->product->AddOrderBy('id', false);
            }
            $product_list = $this->product->getProductList("p.* , c.iso 'country_iso', c.name 'country_name'");
            $productDataArr = array();
            foreach ($product_list as $product) {
                $productArr['actionss'] = '';
                $productArr['actionss'] .= (Permissions::checkFilePermission('product_edit') ? "<a data-id =" . $product->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-pencil'></span> </a>" : '');
                $productArr['actionss'] .= (Permissions::checkFilePermission('product_delete') ? "<a href='#' data-id =" . $product->getId() . "  class='btndelete btn btn-xs red btn-outline'><span class='fa fa-trash'></span></a>" : "");
                //$productArr['actionss'] .= (Permissions::checkFilePermission('product_audit') ? "<a href='javascript:;' data-productid =" . $product->getId() . " data-product_name=" . $product->getProductName() . " data-action='GET_PRODUCT_LOGS' data-productload='products.php' class='btn btn-xs btndelete red mt-ladda-btn ladda-button btn-outline product-logs' title='logs' data-toggle='modal' data-target='#product-logs-popup' role='dialog' tabindex='-1'><span class='fa fa-list'></a>" : '');
                $productArr['actionss'] .= (Permissions::checkFilePermission('product_audit') ? '<a data-title="Product" data-table="product" data-container="audit_content" data-ajax_url="products.php" data-id="' . $product->getId() . '" 
                                                                                                id="btnAudit" href="javascript:;" class="btn btn-xs btn-default blue btn-outline pull-left margin-right-5 show_audit"  title="audit" data-target="#audit-log" data-toggle="modal" >
                                                                                                   <span class="fa fa-list"></span> 
                                                                                                </a>' : '');
                $productArr['actionss'] .= (Permissions::checkFilePermission('product_edit.php') ? "<a href='product_edit.php?customize_service_id=" . $product->getId() . "&country=GB' class='btn btn-xs blue mt-ladda-btn ladda-button btn-outline' title='View'><span class='fa fa-eye'></a>" : '');
                $productArr['added_date'] = $product->getAddedDate();
                $productArr['product_name'] = '<img src="../images/productlogo/thumbnail/owe_16_' . $product->getLogo() . '"> ' . $product->getProductName();
                $productArr['insurance'] = $product->getInsurance();
                $productArr['weight'] = round($product->getFromWeight()) . " - " . round($product->getToWeight());
                $productArr['origin_country'] = '<img src="../assets/global/img/flags/' . strtolower($product->getCountryIso()) . '.png"> ' . $product->getCountryName();
                $productArr['transit_time'] = $product->getTransitTime();

                $productArr['country'] = '<a class=" viewcountry margin-right-5" rel="tooltip" 
                                               data-toggle="modal" data-target="#view-country-popup" role="dialog" tabindex="-1" 
                                                data-action="GET_SERVICE_COUNTRY"
                                                data-productid="' . $product->getId() . '"
                                                data-productname="' . $product->getProductName() . '" data-carrierload="products.php"
                                                href="javascript:;">
                                                 View available countries
                                            </a>';
                //$productArr['country'] = $product->getInsurance();
                $productArr['status'] = ($product->getStatus() == 1 ? '<center><span class="label label-sm label-success">Yes</span></center>' : '<center><span class="label label-sm label-danger">No</span></center>');
                $productDataArr[] = $productArr;
            }
            $productDataarr['data'] = $productDataArr;
            $productDataarr['draw'] = $sEcho;
            $productDataarr['recordsTotal'] = $iTotalRecords;
            $productDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($productDataarr);
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
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />



        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <?php
    }

    protected function renderFooter() {
        ?>
        <script>
            $(document).ready(function () {
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "products.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $("#productForm")[0].reset();
                    });
                });
                $(document).on('click', '#btn_Save', function () {
                    $('#productForm').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else
                        {
                            var id = $('#id').val();
                            //alert('adsfasdfsadf');
                            $.ajax({
                                method: "POST",
                                url: "products.php",
                                data: $('#productForm').serialize()
                            }).done(function (data) {

                                $('#btn_Save').val("Save");
                                $('#success_msg').html(" ");
                                if (id == "") {
                                    $('#success_msg').html("Record has been Added Successfully");
                                } else {
                                    $('#success_msg').html("Record has been Updated Successfully");
                                }
                                $("#productForm")[0].reset();
                                $("#logo_hidden").val();
                                $("div").removeClass("hidden");
                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                $('#id').val("");
                                $('#manage-data-table').DataTable().ajax.reload();
                            });
                        }
                    });
                    $("#productForm").submit();
                });

                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var recordid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "products.php",
                        data: {recordid: recordid, func: "editrecord"}
                    }).done(function (data) {

                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");
                        $('#product_name').val(t.product_name);
                        $('#insurance').val(t.insurance);
                        $('#description').val(t.description);
                        $('#from_weight').val(t.from_weight);
                        $('#to_weight').val(t.to_weight);
                        $('#logo_hidden').val(t.logo);
                        $('#max_length').val(t.max_length);
                        $('#max_width').val(t.max_width);
                        $('#max_height').val(t.max_height);
                        $('#max_vol_weight').val(t.max_vol_weight);
                        $('#vol_denominator').val(t.vol_denominator);
                        $('#remotearea_charges').val(t.remotearea_charges);
                        $('#fuel_charges').val(t.fuel_charges);
                        $('#country_id').val(t.country_id).selectpicker('refresh');
                        $('#transit_time').val(t.transit_time);

                        if (t.is_untrack == 1) {
                            $('#is_untrack').attr('checked', true);
                            $('#is_untrack').bootstrapSwitch('state', true);
                        } else if (t.is_untrack == 0) {
                            $('#is_untrack').attr('checked', false);
                            $('#is_untrack').bootstrapSwitch('state', false);
                        }

                        if (t.status == 1) {
                            $('#active').attr('checked', true);
                            $('#active').bootstrapSwitch('state', true);
                        } else if (t.status == 0) {
                            $('#active').attr('checked', false);
                            $('#active').bootstrapSwitch('state', false);
                        }
                        //$('#active').val(t.status);
                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
                    });
                });

                $(document).on('click', '.btndelete', function () {
                    var e = $(this);
                    swal({
                        title: "Are you sure, you want to delete product?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                            function (isConfirm) {
                                if (isConfirm) {

                                    var productid = e.data('id');

                                    $.ajax({
                                        method: "POST",
                                        url: "products.php",
                                        data: {productid: productid, action: "DELETEPRODUCT"}
                                    }).done(function (data) {
                                        var t = JSON.parse(data);
                                        if (t.status == "success")
                                        {
                                            $("#successmsg").removeClass("hidden");
                                            $('#successmsg').show();
                                            $('#success_msg').addClass('alert alert-success');
                                            $('#success_msg').html(t.message);
                                            $('.filter-cancel').click();

                                        }
                                    });
                                } else
                                {
                                    return false;
                                }
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
                                "url": "products.php?action=product_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "added_date"},
                                {"data": "product_name"},
                                {"data": "origin_country"},
                                {"data": "transit_time"},
                                {"data": "insurance"},
                                {"data": "weight"},
                                {"data": "country", "bSortable": false},
                                {"data": "status"}
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

                $(document).on('click', '.viewcountry', function () {
                    var e = $(this);
                    var productname = e.data('productname');
                    var productid = e.data('productid');
                    var url = e.data('carrierload');
                    var action = e.data('action');

                    $("#service_name").html(productname);
                    $.post(url, {func: action, productname: productname, productid: productid}, function (d) {
                        $("#carrier-logs-display").html(d);
                    });
                });
            });
            function numbersonly(e)
            {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8)
                {
                    if (unicode == 46)
                    {
                    } else if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
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
        if (Permissions::checkFilePermission('product_add')) {
            ?>
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-plus"></i>
                            
            <?php
            echo Translation::GetCaption("ADD_UPDATE_PRODUCTS");
            ?> List
                            
                        </div>
                        <div class="actions">
            <?php if (Permissions::checkFilePermission('routing_manual.php')) { ?>
                                <a href="routing_manual.php" class="btn blue" id="manual_add_product" name="manual_add_product">
                                    <i class="fa fa-plus"></i> Add Routing</a>
            <?php
            }
            if (Permissions::checkFilePermission('routing_bulk.php')) {
                ?>
                                <a href="routing_bulk.php" class="btn blue" id="routing_bulk" name="routing_bulk">
                                    <i class="fa fa-upload"></i> Upload CSV</a>
            <?php } ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form name="productForm" id="productForm" action="" method="POST" enctype="multipart/form-data">           
                            <div class="row">
                                <div class="col-md-12 hidden" id="successmsg">
                                    <div class="alert alert-success" id="success_msg"> </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Product Name</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <i class="fa tooltips font-red" data-original-title="Product Name is mandatory">*</i>
                                            <input name="product_name" id="product_name" value="" size="50" class="form-control"  maxlength="35" title="" placeholder="<?= Translation::GetCaption("PLEASE_ENTER_PRODUCT_NAME"); ?>" rel="tooltip" data-original-title="<?= Translation::GetCaption("PLEASE_ENTER_PRODUCT_NAME"); ?>" type="text" required>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Insurance</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="insurance" id="insurance" value="" size="50" class="form-control" title="<?= Translation::GetCaption("INSURANCE"); ?>" maxlength="35" placeholder="<?= Translation::GetCaption("PLEASE_ENTER_INSURANCE"); ?>" rel="tooltip" data-original-title="<?= Translation::GetCaption("PLEASE_ENTER_INSURANCE"); ?>" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label><?= Translation::GetCaption("TRANSIT_TIME"); ?> in Day(s)</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="transit_time" id="transit_time" value="" size="50" class="form-control" title="<?= Translation::GetCaption("TRANSIT_TIME"); ?>" maxlength="35" placeholder="<?= Translation::GetCaption("NAV_ADD_TRANSIT_TIME"); ?>" rel="tooltip" data-original-title="<?= Translation::GetCaption("NAV_ADD_TRANSIT_TIME"); ?>" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label><?= Translation::GetCaption("ORIGIN_COUNTRY"); ?></label>
                                        <div class="input-group input-group-sm" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
            <?php echo Ddl::generateCountryDDL('country_id', '', 'id'); ?>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>From Weight</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                            <input name="from_weight" id="from_weight" value="" size="50" class="form-control" title="From Weight" maxlength="35" placeholder="From Weight" rel="tooltip" data-original-title="From Weight" onkeypress='return numbersonly(event)' type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>To Weight</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                            <input name="to_weight" id="to_weight" value="" size="50" class="form-control" title="To Weight" maxlength="35" placeholder="To Weight" rel="tooltip" data-original-title="To Weight" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Max Length</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                            <input name="max_length" id="max_length" value="" size="50" class="form-control" title="Max Length" maxlength="35" placeholder="Max Length" rel="tooltip" data-original-title="Max Length" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Max Width</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                            <input name="max_width" id="max_width" value="" size="50" class="form-control" title="Max Width" maxlength="35" placeholder="Max Width" rel="tooltip" data-original-title="Max Width" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Max Height</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-list"></i></span>
                                            <input name="max_height" id="max_height" value="" size="50" class="form-control" title="Max Height" maxlength="35" placeholder="Max Height" rel="tooltip" data-original-title="Max Height" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Max Vol Weight</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                            <input name="max_vol_weight" id="max_vol_weight" value="" size="50" class="form-control" title="Max Vol Weight" maxlength="35" placeholder="Max Vol Weight" rel="tooltip" data-original-title="Max Vol Weight" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Vol Denominator</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-balance-scale"></i></span>
                                            <input name="vol_denominator" id="vol_denominator" value="" size="50" class="form-control" title="Vol Denominator" maxlength="35" placeholder="Vol Denominator" rel="tooltip" data-original-title="Vol Denominator" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Remotearea Charges</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-filter"></i></span>
                                            <input name="remotearea_charges" id="remotearea_charges" value="" size="50" class="form-control" title="Remotearea Charges" maxlength="35" placeholder="Remotearea Charges" rel="tooltip" data-original-title="Remotearea Charges" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Fuel Charges</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-filter"></i></span>
                                            <input name="fuel_charges" id="fuel_charges" value="" size="50" class="form-control" title="Fuel Charges" maxlength="35" placeholder="Fuel Charges" rel="tooltip" data-original-title="Fuel Charges" onkeypress='return numbersonly(event)' type="text">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Description</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="description" id="description" value=""  class="form-control" title="<?= Translation::GetCaption("DESCRIPTION"); ?>" maxlength="35" placeholder="<?= Translation::GetCaption("DESCRIPTION"); ?>" rel="tooltip" data-original-title="<?= Translation::GetCaption("DESCRIPTION"); ?>" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label >Untrack</label>
                                        <div class="md-radio-sm md-radio-inline">
                                            <input <?php echo ($is_untrack == '1' ? 'checked="checked"' : ''); ?> name="is_untrack" id="is_untrack" type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No"  data-on-color="primary" data-off-color="danger" data-size="mini">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label >Active</label>

                                        <div class="md-radio-sm md-radio-inline">
                                            <input <?php echo ($active == '1' ? 'checked="checked"' : ''); ?> name="active" id="active" type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No"  data-on-color="primary" data-off-color="danger" data-size="mini">
                                        </div>
                                    </div>
                                </div>        
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" id="logo_hidden" name="logo_hidden" value="<?php echo $logo_hidden; ?>">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100px; height: 80px;">
            <?php
            if (trim($logo_hidden) != '') {
                echo '<img src="../images/agentlogo/thumbnail/owe_100_' . $logo_hidden . '" >';
            } else {
                echo '<img src = "../images/No-image-found.jpg">';
            }
            ?>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="form-control uneditable-input input-fixed input-xs" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Select file </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="logo" id="logo" accept="image/*" />
                                                </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists fileinput-exists-remove" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                            <div class="clearfix margin-top-10"> <span class="label label-primary label-sm"><small>NOTE!</span> Recommended logo dimensions (254 x 62) </small></div>
                                        </div>
                                    </div>

                                </div>   
                            </div> 
                            <div style="clear:both"></div> 
                            <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                            <input type="hidden" name="form_action" id="form_action" value="saverecord" />
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
                        
        <?= Translation::GetCaption("PRODUCT_DETAILS"); ?>
                        
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th><?php echo Translation::GetCaption("DATE_CREATED"); ?></th>
                                    <th><?php echo Translation::GetCaption("PRODUCT_NAME"); ?></th>
                                    <th><?php echo Translation::GetCaption("ORIGIN_COUNTRY"); ?></th>
                                    <th><?php echo Translation::GetCaption("TRANSIT_TIME"); ?></th>
                                    <th><?php echo Translation::GetCaption("INSURANCE"); ?></th>
                                    <th>Weight</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td>


                                        <!--  <div class="form-group input-group input-group-xs date date-picker" data-date-format="yyyy-mm-dd">
                                               <input type="text" class="form-control form-filter input-sm" readonly name="search_Date_from" placeholder="From">
                                               <span class="input-group-btn">
                                                   <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                               </span>
                                           </div>
                                           <div class="input-group  input-group-xs date date-picker" data-date-format="yyyy-mm-dd">
                                               <input type="text" class="form-control form-filter input-sm" readonly name="search_Date_to" placeholder="To">
                                               <span class="input-group-btn">
                                                   <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                               </span>
                                           </div> -->
                                    </td>
                                    <td>
                                        <div class="input-group  input-group-xs">
                                            <input type="text"  class="form-control form-filter input-sm" name="search_product_name">
                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="input-group  input-group-xs">
                                            <input type="text" class="form-control form-filter input-sm" name="search_insurance" onkeypress='return numbersonly(event)'>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
        <?php
        $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
        echo Ddl::generateArrayDDL('search_Active', $arrayTypeValues, "", "Select Active", ' class="form-control form-filter select2"', "", 'search_Active', 'Select Status', '');
        ?>
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
        <div class="modal fade" tabindex="-1" role="dialog" id="view-country-popup" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="service_name"></span> Countries List</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" >
                            <div class="col-md-12" id="carrier-logs-display">

                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
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
