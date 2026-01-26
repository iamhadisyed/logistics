<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'products.class',
    'productsfilter.class',
    'brands.class',
    'brandsfilter.class',
    'productsattributemapping.class',
    'productsattributemappingfilter.class',
    'productscategorymapping.class',
    'productscategorymappingfilter.class',
    'productsoptionmapping.class',
    'productsoptionmappingfilter.class',
    'productsimagemapping.class',
    'productsimagemappingfilter.class'
]);

class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    public $error = array();
    public $message;
    private $user = null;
    private $productId = '';
    private $productObj = '';

    protected function init()
    {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Product'
        );

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $this->productId = $_GET['id'];
            $this->productObj = new Products($_GET['id']);
        }
        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "save") {
            $validate = true;
            $errorMsg = "Product not saved successfully";
            $imageFiles = $_FILES['product_image'];
            $productName = $this->form_vars["product_name"];
            $manufacturer = $this->form_vars["manufacturer"];
            $asin = $this->form_vars["asin"];
            $upc = $this->form_vars["upc"];
            $sku = $this->form_vars["sku"];
            $isbn = $this->form_vars["isbn"];
            $ean = $this->form_vars["ean"];
            //$hs_code = $this->form_vars["hs_code"];
            //$gross_weight = $this->form_vars["gross_weight"];
            $net_weight = $this->form_vars["net_weight"];
            $quantity = $this->form_vars["quantity"];
            $length = $this->form_vars["length"];
            $width = $this->form_vars["width"];
            $height = $this->form_vars["height"];
            $price = $this->form_vars["price"];
            $note = $this->form_vars["note"];
            $description = $this->form_vars["description"];
            // = $this->form_vars["category_id"];
            $brandId = $this->form_vars["brand_id"];
            $attrbuites = $this->form_vars["attrbuite"];
            $options = $this->form_vars["option"];
            $dateAdded = time();
            $addedBy = $this->user->getId();
            $dateUpdate = time();
            $updateBy = $this->user->getId();
            $id = $this->form_vars['id'];
            if(empty($brandId)) {
                $validate = false;
                $errorMsg = "Brand name is required";
            }
            if(empty($productName)) {
                $validate = false;
                $errorMsg = "Product name is required";
            }
            if(empty($manufacturer)) {
                $validate = false;
                $errorMsg = "Manufacturer is required";
            }
            if(empty($asin) && empty($upc) &&  empty($sku)) {
                $validate = false;
                $errorMsg = "Please enter ASIN, UPC or SKU.";
            }

            if ($validate) {
                $products = new Products();
                if ($id != "" && $id > 0) {
                    $products = new Products($id);
                }
                $products->setProductName($productName);
                $products->setManufacturer($manufacturer);
                $products->setAsin($asin);
                $products->setUpc($upc);
                $products->setSku($sku);
                $products->setIsbn($isbn);
                $products->setEan($ean);
                //$products->setHsCode($hs_code);
                //$products->setGrossWeight($gross_weight);
                $products->setNetWeight($net_weight);
                $products->setQuantity($quantity);
                $products->setLength($length);
                $products->setWidth($width);
                $products->setHeight($height);
                $products->setPrice($price);
                $products->setNote($note);
                $products->setDescription($description);
                $products->setDateAdded($dateAdded);
                $products->setAddedBy($addedBy);
                $products->setDateUpdated($dateUpdate);
                $products->setUpdatedBy($updateBy);
                $products->setBrandId($brandId);

                $products->save();
                $productId = $products->getId();
                if($productId > 0) {
                    ProductsCategoryMapping::deleteProductsCategoryMappingByProductsId($productId);
//                    if(count($category_ids)) {
//                        foreach($category_ids as $category_id) {
//                            $productsCategoryMapping = new ProductsCategoryMapping();
//                            $productsCategoryMapping->setProductId($productId);
//                            $productsCategoryMapping->setCategoryId($category_id);
//                            $productsCategoryMapping->save();
//                        }
//                    }
                    ProductsAttributeMapping::deleteProductsAttributeMappingByProductsId($productId);
                    if(count($attrbuites)) {
                        foreach($attrbuites as $attrbuite) {
                            $attrbuiteId = $attrbuite['id'];
                            $attrbuiteDescription = explode("<br />", nl2br($attrbuite['discription']));
                            if(count($attrbuiteDescription)) {
                                foreach($attrbuiteDescription as $description) {
                                    $description = trim($description);
                                    $productsAttributeMapping = new ProductsAttributeMapping();
                                    $productsAttributeMapping->setProductId($productId);
                                    $productsAttributeMapping->setAttributeId($attrbuiteId);
                                    $productsAttributeMapping->setValue($description);
                                    $productsAttributeMapping->save();
                                }
                            }
                        }
                    }
                    ProductsOptionMapping::deleteProductsOptionMappingByProductsId($productId);
                    if(count($options)) {
                        foreach($options as $option) {
                            $optionId = $option['id'];
                            $optionDescription = explode("<br />", nl2br($option['discription']));
                            if(count($optionDescription)) {
                                foreach($optionDescription as $description) {
                                    $description = trim($description);
                                    $productsOptionMapping = new productsOptionMapping();
                                    $productsOptionMapping->setProductId($productId);
                                    $productsOptionMapping->setOptionId($optionId);
                                    $productsOptionMapping->setValue($description);
                                    $productsOptionMapping->save();
                                }
                            }
                        }
                    }
                    if(isset($imageFiles['name'])) {
                        $imageFileNames = $imageFiles['name'];
                        foreach($imageFileNames as $k => $filename) {
                            $path_parts = pathinfo($filename);
                            $ext = strtolower($path_parts['extension']);
                            $basename = $path_parts['basename'];
                            if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg') {
                                $new_file_name = "product_" . $productId . "_" . microtime() . "." . $ext;
                                $relPath = '../_assets/product_files/'.$new_file_name;
                                if (!file_exists("../_assets/product_files/")){
                                    @mkdir("../_assets/product_files/", 0775);
                                }
                                move_uploaded_file($imageFiles['tmp_name'][$k], $relPath);
                                $productsImageMapping = new ProductsImageMapping();
                                $productsImageMapping->setProductId($productId);
                                $productsImageMapping->setImage($new_file_name);
                                $productsImageMapping->save();
                            }
                        }
                    }
                }
                
                $message = $this->wmsAddSkuApi($productId);    
                //echo $message; 
                if($message != '' && $message != 'true')
                {                
                    $output['status'] = "error";
                    $output['message'] = $message;
                }
                else
                {
                    $output["status"] = "success";
                    $output["message"] = "Product has been saved successfully";
                }
            } else {
                $output['status'] = "error";
                $output['message'] = $errorMsg;
            }
            
            //print_r($output); die;
            echo json_encode($output);
            die;
        }

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "delete") {
            $imgId = $this->form_vars['img_id'];
            $imgName = $this->form_vars['img_name'];
            $relPath = '../_assets/product_files/'.$imgName;
            unlink($relPath);
            $productsImageMappingFilter = new ProductsImageMappingFilter();
            $productsImageMappingFilter->where(['id' => $imgId]);
            $productsImageMappingFilter->delete();
            $return = [
                'status' => 'success',
                'message' => 'Product Image delete successfully'
            ];
            echo json_encode($return);
            die;
        }
    }

    protected function renderHead()
    {
        ?>
            <style type="text/css">
                .product_img {
                    height: 112px;
                    width: 100%;
                }
                .close_img {
                    width: 15px;
                    height: 15px;
                    position: absolute;
                    right: 5px;
                }
            </style>
        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
        <style type="text/css">
            .fieldset legend {
                font-size: 18px;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "attributes.php?action=attributes_list";
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid, response) {
                            $(".table-container .custom-alerts").hide();
                            if (response.recordsTotal > 0) {
                                $("#bluk_actions").show();
                                $(".button-download-records").show();
                            } else {
                                $("#bluk_actions").hide();
                                $(".button-download-records").hide();
                            }
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
                                "url": datatableurl, // ajax source
                                headers: {}
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "name", "bSortable": false},
                                {"data": "group", "bSortable": false},
                                {"data": "is_active", "bSortable": false}
                            ],
                            rowCallback: function (row, data, index) {
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
            $(document).ready(function (e) {

                var elindex = 0;
                if (jQuery('#elindex-hardcode-attribute').length > 0) {
                    elindex = jQuery('#elindex-hardcode-attribute').val();
                }
                $(document).on('click', '.add_more_attribute', function () {
                    elindex++;
                    var clone = $(this).parent().parent().clone();
                    var attributes = $(clone).find('.attrbuite_id .bs-select').attr('name');
                    var attributesId = $(clone).find('.attrbuite_id .bs-select').attr('id');
                    var attrbuiteDiscription = $(clone).find('.attrbuite_discription').attr('name');
                    var attrbuiteDiscriptionId = $(clone).find('.attrbuite_discription').attr('id');
                    $(this).remove();
                    $(clone).find('input').val('');
                    $(clone).find('select').val('');
                    $(clone).find('textarea').val('');
                    $(clone).find('.attrbuite_id .bs-select').attr('name', attributes.replace(/\d+/, elindex));
                    $(clone).find('.attrbuite_id .bs-select').attr('id', attributesId.replace(/\d+/, elindex));
                    $(clone).find('.attrbuite_discription').attr('name', attrbuiteDiscription.replace(/\d+/, elindex));
                    $(clone).find('.attrbuite_discription').attr('id', attrbuiteDiscriptionId.replace(/\d+/, elindex));

                    $(clone).find('.bootstrap-select.attrbuite_id').replaceWith(function () {
                        return $('#attrbuite_id_' + elindex, this);
                    });
                    $(clone).find('#attrbuite_id_' + elindex).selectpicker('refresh');

                    $(clone).find('button.remove_attribute').show();
                    $(clone).find('button.remove_attribute').removeClass('initial-button');
                    $(clone).appendTo($('.attribute_container'));
                });
                $(document).on('click', '.remove_attribute', function () {
                    var el = $(this);
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
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
                            if ($('.attribute_container .remove_attribute').length > 1) {
                                $(el).parent().parent().remove();
                                if ($('.add_more_attribute').length == 0) {
                                    var addMore = $(el).parent().find('.add_more_attribute').clone();
                                    $('.attribute_container .remove_attribute').last().parent().prepend(addMore);
                                }
                            } else {
                                $(el).parent().parent().find('input').val('');
                            }
                        }
                    });
                });

                var elindex_image = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex_image = jQuery('#elindex-hardcode').val();
                }
                $(document).on('click', '.add_more_image', function () {
                    elindex_image++;
                    var clone = $(this).parent().parent().clone();
                    var productImage = $(clone).find('.product_image').attr('name');
                    var productImageId = $(clone).find('.product_image').attr('id');
                    $(this).remove();
                    $(clone).find('.product_image').attr('name', productImage.replace(/\d+/, elindex_image));
                    $(clone).find('.product_image').attr('id', productImageId.replace(/\d+/, elindex_image));
                    $(clone).find('button.remove_image').show();
                    $(clone).find('button.remove_image').removeClass('initial-button');
                    $(clone).appendTo($('.image_container'));
                });
                $(document).on('click', '.remove_image', function () {
                    var el = $(this);
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            if ($('.image_container .remove_image').length > 1) {
                                $(el).parent().parent().remove();
                                if ($('.add_more_image').length == 0) {
                                    var addMore = $(el).parent().find('.add_more_image').clone();
                                    $('.image_container .remove_image').last().parent().prepend(addMore);
                                }
                            } else {
                                $(el).parent().parent().find('input').val('');
                            }
                        }
                    });
                });

                var elindex_option = 0;
                if (jQuery('#elindex-hardcode-option').length > 0) {
                    elindex_option = jQuery('#elindex-hardcode-option').val();
                }
                $(document).on('click', '.add_more_option', function () {
                    elindex_option++;
                    var clone = $(this).parent().parent().clone();
                    var options = $(clone).find('.option_id .bs-select').attr('name');
                    var optionsId = $(clone).find('.option_id .bs-select').attr('id');
                    var optionDiscription = $(clone).find('.option_discription').attr('name');
                    var optionDiscriptionId = $(clone).find('.option_discription').attr('id');
                    $(this).remove();
                    $(clone).find('input').val('');
                    $(clone).find('select').val('');
                    $(clone).find('textarea').val('');
                    $(clone).find('.option_id .bs-select').attr('name', options.replace(/\d+/, elindex_option));
                    $(clone).find('.option_id .bs-select').attr('id', optionsId.replace(/\d+/, elindex_option));
                    $(clone).find('.option_discription').attr('name', optionDiscription.replace(/\d+/, elindex_option));
                    $(clone).find('.option_discription').attr('id', optionDiscriptionId.replace(/\d+/, elindex_option));

                    $(clone).find('.bootstrap-select.option_id').replaceWith(function () {
                        return $('#option_id_' + elindex_option, this);
                    });
                    $(clone).find('#option_id_' + elindex_option).selectpicker('refresh');

                    $(clone).find('button.remove_option').show();
                    $(clone).find('button.remove_option').removeClass('initial-button');
                    $(clone).appendTo($('.option_container'));
                });
                $(document).on('click', '.remove_option', function () {
                    var el = $(this);
                    swal({
                            title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
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
                                if ($('.option_container .remove_option').length > 1) {
                                    $(el).parent().parent().remove();
                                    if ($('.add_more_attribute').length == 0) {
                                        var addMore = $(el).parent().find('.add_more_option').clone();
                                        $('.option_container .remove_option').last().parent().prepend(addMore);
                                    }
                                } else {
                                    $(el).parent().parent().find('input').val('');
                                }
                            }
                        });
                });

                $(document).on('click', '#btnSave', function () {
                    var product_id = '<?php echo $this->productId ?>';
                    var form = $('#product_form')[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);
                    console.log(formData);
                    //return false;
                    formData.append('action', 'save');
                    $.blockUI();
                    $.ajax({
                        url: 'product_add.php',
                        data: formData,
                        type: 'POST',
                        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                        processData: false, // NEEDED, DON'T OMIT THIS
                        dataType: "json",
                        success: function (response) {
                            if (response.status == "success") {
                                if(product_id != "" && product_id > 0) {
                                    window.location.href = window.location.href + "&status=success";
                                } else {
                                    window.location.href = window.location.href + "?status=success";
                                }
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                            $.unblockUI();
                        },
                        error: function () {
                            $.unblockUI();
                        }
                    });
                });
                DataTableFun.init();

                $(document).on('click', '.delete_img', function () {
                    var img_id = $(this).data('img_id');
                    var img_name = $(this).data('img_name');
                    swal({
                        title: "Are You Sure?",
                        text: "you want to delete image!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            $.blockUI();
                            $.ajax({
                                type: "POST",
                                url: "product_add.php",
                                data: {action: "delete", img_id: img_id, img_name: img_name},
                                dataType: "json",
                                success: function (data) {
                                    $.unblockUI();
                                    if (data.status == "success") {
                                        swal("Success!", data.message, "success");
                                    } else {
                                        swal("Sorry!", data.message, "error");
                                    }
                                },
                                error: function () {
                                    $.unblockUI();
                                    alert('error handing here');
                                }
                            });
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

    protected function renderBody()
    {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-users"></i>
                    Product
                </div>
                <div class="actions">
                    <a href="product_list.php" class="btn btn-sm blue" id="btn_product_list"  >
                        <span></span><i class="fa fa-list"></i>&nbsp;
                        Product list
                    </a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <?php if(isset($_GET['status']) && $_GET['status'] == 'success') { ?>
                <div class="row">
                    <div class="col-sm-12">
                        <p class="alert alert-success">Product is save successfully</p>
                    </div>
                </div>
                <?php } ?>
                <form method="post" class="product_form" id="product_form" >
                    <input type="hidden" name="id" id="id" value="<?php echo $this->productId ?>" />
                    <div class="tabbable-bordered">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_general" data-toggle="tab"> General </a>
                            </li>
                            <li>
                                <a href="#tab_attribute" data-toggle="tab"> Attribute </a>
                            </li>
                            <li>
                                <a href="#tab_option" data-toggle="tab"> Options </a>
                            </li>
                            <li>
                                <a href="#tab_images" data-toggle="tab"> Images </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_general">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                        <fieldset class="fieldset">
                                            <legend>Basic Information</legend>
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <?php
                                                        echo Ddl::generateDDL('brand_id','BrandsFilter', 'is_active=1', 'name', 'id',(!empty($this->productObj) ? $this->productObj->getBrandId() : ''), 'class="form-control select2 select form-filter', 'Select Brand', '');
                                                        ?>
                                                        <label>Brand <span class="required" aria-required="true"> * </span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="product_name" placeholder="Product Name" value="<?php echo !empty($this->productObj) ? $this->productObj->getProductName() : '' ?>" >
                                                        <label>Product Name <span class="required" aria-required="true"> * </span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="manufacturer" placeholder="Product Manufacturer" value="<?php echo !empty($this->productObj) ? $this->productObj->getManufacturer() : '' ?>" >
                                                        <label>Manufacturer <span class="required" aria-required="true"> * </span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="asin" placeholder="ASIN" value="<?php echo !empty($this->productObj) ? $this->productObj->getAsin() : '' ?>" >
                                                        <label>ASIN </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="sku" placeholder="SKU" value="<?php echo !empty($this->productObj) ? $this->productObj->getSku() : '' ?>" >
                                                        <label>SKU <span class="required" aria-required="true"> * </span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="upc" placeholder="UPC" value="<?php echo !empty($this->productObj) ? $this->productObj->getUpc() : '' ?>" >
                                                        <label>UPC </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="isbn" placeholder="ISBN" value="<?php echo !empty($this->productObj) ? $this->productObj->getIsbn() : '' ?>" >
                                                        <label>ISBN </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="ean" placeholder="EAN" value="<?php echo !empty($this->productObj) ? $this->productObj->getEan() : '' ?>" >
                                                        <label>EAN </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="note" placeholder="Notes" value="<?php echo !empty($this->productObj) ? $this->productObj->getNote() : '' ?>" >
                                                        <label>Notes </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 form-group">
                                                    <div class="has-float-label">
                                                        <textarea class="form-control" name="description"><?php echo !empty($this->productObj) ? $this->productObj->getDescription() : '' ?></textarea>
                                                        <label>Description </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>


                                        <fieldset class="fieldset">
                                            <legend>Shipping Information</legend>
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="net_weight" placeholder="Net Weight" value="<?php echo !empty($this->productObj) ? $this->productObj->getNetWeight() : '' ?>" >
                                                        <label>Net Weight </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="length" placeholder="Length" value="<?php echo !empty($this->productObj) ? $this->productObj->getLength() : '' ?>" >
                                                        <label>Length </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="width" placeholder="Width" value="<?php echo !empty($this->productObj) ? $this->productObj->getWidth() : '' ?>" >
                                                        <label>Width </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="height" placeholder="Height" value="<?php echo !empty($this->productObj) ? $this->productObj->getHeight() : '' ?>" >
                                                        <label>Height </label>
                                                    </div>
                                                </div>

                                            </div>
                                        </fieldset>
                                        <fieldset class="fieldset">
                                            <legend>Price Information</legend>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="quantity" placeholder="Quantity" value="<?php echo !empty($this->productObj) ? $this->productObj->getQuantity() : '' ?>" >
                                                        <label>Quantity </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <div class="has-float-label">
                                                        <input type="text" class="form-control" name="price" placeholder="Price" value="<?php echo !empty($this->productObj) ? $this->productObj->getPrice() : '' ?>" >
                                                        <label>Price </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_attribute">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th width="40%">Attribute</th>
                                                            <th width="40%">Test</th>
                                                            <th width="10%">&nbsp;</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="attribute_container">
                                                    <?php  if($this->productId > 0) { ?>
                                                        <?php
                                                            $productAttributeMappingFilter = new ProductsAttributeMappingFilter();
                                                            $productAttributeMappingFilter->where(['product_id' => $this->productId]);
                                                            $productAttributeMappingFilterObjs = $productAttributeMappingFilter->getList();
                                                        ?>
                                                        <input type="hidden" name="elindex-hardcode-attribute" id="elindex-hardcode-attribute" value="<?php echo count($productAttributeMappingFilterObjs); ?>" />
                                                        <?php
                                                            if(count($productAttributeMappingFilterObjs)) {
                                                                foreach($productAttributeMappingFilterObjs as $k => $productAttributeMappingFilterObj) {
                                                        ?>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <div class="has-float-label">
                                                                                    <?php
                                                                                    $selectedAttribute = $productAttributeMappingFilterObj->getAttributeId();
                                                                                    $sql = "select * from product_attributes where is_active = 1";
                                                                                    echo Ddl::generateDDLFromSql($sql, 'attrbuite['.$k.'][id]', 'attrbuite_name', 'id', $selectedAttribute, ' class="bs-select form-control attrbuite_id" placeholder="Attribute"  data-live-search="true"  data-container="body" data-size="8"  ', '', "", 'attrbuite_id_'.$k, "", "");
                                                                                    ?>
                                                                                    <label>Attribute <span class="required" aria-required="true"> * </span></label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <div class="has-float-label">
                                                                                    <textarea class="form-control attrbuite_discription"  name="attrbuite[<?php echo $k ?>][discription]" id="attrbuite_discription_<?php echo $k ?>" ><?php echo $productAttributeMappingFilterObj->getValue() ?></textarea>
                                                                                    <label>Value <span class="required" aria-required="true"> * </span></label>
                                                                                    <span class="help-block">Please add attribute value per line</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <?php if (count($productAttributeMappingFilterObjs) == ($k + 1)) { ?>
                                                                                <button type="button" class="btn btn-success add_more_attribute"><i class="fa fa-plus"></i></button>
                                                                                <button type="button" class="btn btn-danger remove_attribute"> <i class="fa fa-minus"></i></button>
                                                                            <?php } else { ?>
                                                                                <button type="button" class="btn btn-danger remove_attribute"> <i class="fa fa-minus"></i></button>
                                                                            <?php } ?>

                                                                        </td>
                                                                    </tr>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    <?php  } else { ?>
                                                        <tr>
                                                            <td>
                                                                <div class="form-group">
                                                                    <div class="has-float-label">
                                                                        <?php
                                                                        $selectedAttribute = '';
                                                                        $sql = "select * from product_attributes where is_active = 1";
                                                                        echo Ddl::generateDDLFromSql($sql, 'attrbuite[0][id]', 'attrbuite_name', 'id', $selectedAttribute, ' class="bs-select form-control attrbuite_id" placeholder="Attribute"  data-live-search="true" data-container="body" data-size="8" ', '', "", 'attrbuite_id_0', "", "");
                                                                        ?>
                                                                        <label>Attribute <span class="required" aria-required="true"> * </span></label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <div class="has-float-label">
                                                                        <textarea class="form-control attrbuite_discription"  name="attrbuite[0][discription]" id="attrbuite_discription_0" ></textarea>
                                                                        <label>Value <span class="required" aria-required="true"> * </span></label>
                                                                        <span class="help-block">Please add attribute value per line</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-success add_more_attribute"><i class="fa fa-plus"></i></button>
                                                                <button type="button" class="btn btn-danger remove_attribute"> <i class="fa fa-minus"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php  } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_option">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th width="40%">Option</th>
                                                        <th width="40%">Test</th>
                                                        <th width="10%">&nbsp;</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="option_container">
                                                    <?php  if($this->productId > 0) { ?>
                                                    <?php
                                                        $productOptionMappingFilter = new ProductsOptionMappingFilter();
                                                        $productOptionMappingFilter->where(['product_id' => $this->productId]);
                                                        $productOptionMappingFilterObjs = $productOptionMappingFilter->getList();
                                                    ?>
                                                    <input type="hidden" name="elindex-hardcode-option" id="elindex-hardcode-option" value="<?php echo count($productOptionMappingFilterObjs); ?>" />
                                                    <?php
                                                        if(count($productOptionMappingFilterObjs)) {
                                                            foreach ($productOptionMappingFilterObjs as $k => $productOptionMappingFilterObj) {
                                                    ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <div class="has-float-label">
                                                                                <?php
                                                                                $selectedOption = $productOptionMappingFilterObj->getOptionId();
                                                                                $sql = "select * from products_options where is_active = 1";
                                                                                echo Ddl::generateDDLFromSql($sql, 'option['.$k.'][id]', 'option_name', 'id', $selectedOption, ' class="bs-select form-control option_id" placeholder="Options"  data-live-search="true"  data-container="body" data-size="8" ', '', "", 'option_id_'.$k, "", "");
                                                                                ?>
                                                                                <label>Option <span class="required" aria-required="true"> * </span></label>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <div class="has-float-label">
                                                                                <textarea class="form-control option_discription"  name="option[<?php echo $k ?>][discription]" id="option_discription_<?php echo $k ?>" ><?php echo $productOptionMappingFilterObj->getValue() ?></textarea>
                                                                                <label>Value <span class="required" aria-required="true"> * </span></label>
                                                                                <span class="help-block">Please add option value per line</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <?php if (count($productOptionMappingFilterObjs) == ($k + 1)) { ?>
                                                                            <button type="button" class="btn btn-success add_more_option"><i class="fa fa-plus"></i></button>
                                                                            <button type="button" class="btn btn-danger remove_option"> <i class="fa fa-minus"></i></button>
                                                                        <?php } else { ?>
                                                                            <button type="button" class="btn btn-danger remove_option"> <i class="fa fa-minus"></i></button>
                                                                        <?php } ?>

                                                                    </td>
                                                                </tr>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                    <?php  } else { ?>
                                                        <tr>
                                                            <td>
                                                                <div class="form-group">
                                                                    <div class="has-float-label">
                                                                        <?php
                                                                        $selectedOption = '';
                                                                        $sql = "select * from products_options where is_active = 1";
                                                                        echo Ddl::generateDDLFromSql($sql, 'option[0][id]', 'option_name', 'id', $selectedOption, ' class="bs-select form-control option_id" placeholder="Options"  data-live-search="true" data-container="body" data-size="8" ', '', "", 'option_id_0', "", "");
                                                                        ?>
                                                                        <label>Option <span class="required" aria-required="true"> * </span></label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <div class="has-float-label">
                                                                        <textarea class="form-control option_discription"  name="option[0][discription]" id="option_discription_0" ></textarea>
                                                                        <label>Value <span class="required" aria-required="true"> * </span></label>
                                                                        <span class="help-block">Please add option value per line</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-success add_more_option"><i class="fa fa-plus"></i></button>
                                                                <button type="button" class="btn btn-danger remove_option"> <i class="fa fa-minus"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php  } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_images">
                                <div class="row">
                                    <?php  if($this->productId > 0) { ?>
                                    <?php
                                        $productsImageMappingFilter = new ProductsImageMappingFilter();
                                        $productsImageMappingFilter->where(['product_id' => $this->productId]);
                                        $productsImageMappingFilterObjs = $productsImageMappingFilter->getList();
                                    ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <?php if(count($productsImageMappingFilterObjs)) { ?>
                                            <?php foreach($productsImageMappingFilterObjs as $productsImageMappingFilterObj) { ?>
                                            <div class="col-md-2">
                                                <a href="javascript:;" class="delete_img" data-img_id="<?php echo $productsImageMappingFilterObj->getId() ?>" data-img_name="<?php echo $productsImageMappingFilterObj->getImage() ?>" >
                                                    <img src="<?php echo BASE_URL ?>_assets/product_files/close_icon.png" class="img img-responsive close_img" >
                                                </a>
                                                <img src="<?php echo BASE_URL ?>_assets/product_files/<?php echo $productsImageMappingFilterObj->getImage() ?>" class="img img-responsive product_img" >
                                            </div>
                                            <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th width="80%">Image</th>
                                                    <th width="20%">&nbsp;</th>
                                                </tr>
                                                </thead>
                                                <tbody class="image_container">
                                                <tr>
                                                    <td>
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="form-group">
                                                                <div class="input-group input-large">
                                                                    <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                                        <span class="fileinput-filename"> </span>
                                                                    </div>
                                                                    <span class="input-group-addon btn default btn-file">
                                                                        <span class="fileinput-new"> Select file </span>
                                                                        <span class="fileinput-exists"> Change </span>
                                                                        <input type="file" class="product_image" name="product_image[0]" id="product_image_0">
                                                                    </span>
                                                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-success add_more_image"><i class="fa fa-plus"></i></button>
                                                        <button type="button" class="btn btn-danger remove_image"> <i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" name="btnSave" id="btnSave" class="btn btn-primary btn_save">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }
    
    public function wmsAddSkuApi($productId)
    {
        
        //../_assets/product_files/
        $productsImageMappingFilter = new ProductsImageMappingFilter();
        $productsImageMappingFilter->addFilter('    product_id = "'.$productId.'"');
        $result = $productsImageMappingFilter->getList();
        $imageName = $result[0]->getImage();
        
        
        try 
            {
                $product = new Products($productId);
                $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
                $request = new stdClass();
                $request->SKU = $product->getSku();
                $request->CustomerID = 'BRANDS';//$product->getCustomerId(); // added by say pick kerna hai 
                $request->Active = '1';//$sku->getActive();
                $request->Description = $product->getDescription();
                $request->Name = $product->getProductName();
                $request->NetWeight = $product->getNetWeight();
                $request->Price = $product->getPrice();
                $request->Length = $product->getLength();
                $request->Width = $product->getWidth();
                $request->Height = $product->getHeight();              
                $request->HSCode = $product->getHSCode();
                $request->Image = '../_assets/product_files/'.$imageName;  // get from product image mapping
                $response = $client->CreateSku( array("request" => $request));
                if($response->CreateSkuResult->ErrorMsg == '') 
                {
                    $product->setSendWms('1');
                    $product->save();
                    return true;
                }
                else
                {
                    return $response->CreateSkuResult->ErrorMsg;
                }
            } 
            catch (Exception $e) 
            {
                return $e->getMessage();		
            }         
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
