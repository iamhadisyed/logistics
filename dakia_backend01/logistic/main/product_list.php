<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'products.class',
    'productsfilter.class',
    'brands.class',
    'brandsfilter.class',
    'productscategorymapping.class',
    'productscategorymappingfilter.class',
    'usermarketplacesmappingfilter.class',
    'usermarketplacesmapping.class',
    'categories.class',
    'categoriesfilter.class',
    'marketplaces.class',
    'marketplacesfilter.class',
    'amazonproductapiresult.class',
    'amazonproductapiresultfilter.class',
    'productmarketplacemapping.class',
    'productmarketplacemappingfilter.class'
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
    private $categories = [];

    protected function init()
    {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Products'
        );

        if (isset($_GET['action']) && $_GET['action'] == "product_list") {
            
            $this->user = SessionManager::getUser();
            
            $productsFilter = new ProductsFilter();            
            $productsFilter->addFilter("    p.added_by = '".$this->user->getId()."'");
            
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') { 
                $name = $this->form_vars['product_name'];                
                if (!empty($name)) {
                    $productsFilter->whereLike(['product_name' => $name]);
                }
                $group = $this->form_vars['group'];
                if (!empty($group)) {
                    $productsFilter->whereLike(['group' => $group]);
                }
                $asin = $this->form_vars['asin'];                
                if (!empty($asin)) {
                    $productsFilter->addFilter("    asin ='".$asin."'");
                }
                
                $sku = $this->form_vars['sku'];                
                if (!empty($sku)) {
                    $productsFilter->addFilter("    sku ='".$sku."'");
                }

                $searchDateFrom = $this->form_vars['search_Date_from'];
                $searchDateTo = $this->form_vars['search_Date_to'];
                if (!empty($searchDateFrom) && !empty($searchDateTo)) {
                    $productsFilter->whereBetween('date_added', $searchDateFrom, $searchDateTo);
                }
                
                $searchStatus = $this->form_vars['search_Active'];
                $productsFilter->join("product_marketplace_mapping pmm", "pmm.product_id = p.id");
                if (trim($searchStatus) != '') {                    
                    
                    $productsFilter->addFilter('pmm.status = "'.$searchStatus.'"');
                }
                
                $searchMarketplace = $this->form_vars['search_marketplace'];
                if (trim($searchMarketplace) != '') {                    
                    $productsFilter->addFilter('pmm.marketplace_id = "'.$searchMarketplace.'"');
                }
                $productsFilter->orderBy('pmm.id','DESC');
            }
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = "ASC";
                if ($orderBy == 'desc') {
                    $orderFalse = 'DESC';
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if ($dataTableColumnName != "action") {
                    $productsFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $productsFilter->orderBy(strtolower("p.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $productsFilter->setRowsPerPage($iDisplayLength);
            $productsFilter->setOffset($iDisplayStart);
            $productsFilter->leftJoin("products_image_mapping pi", "pi.product_id = p.id");
            $productsFilterObjs = $productsFilter->getList("p.*, pi.image");
            $iTotalRecords = $productsFilter->getCount(false);
            $setDataArr = array();

            foreach ($productsFilterObjs as $productsFilterObj) {
                $ProductMarketPlaceMappingFilterObj = new ProductMarketPlaceMappingFilter();
                $ProductMarketPlaceMappingFilterObj->addFieldFilter('   product_id', $productsFilterObj->getId());
                $ProductMarketPlaceMappingFilterObj->addFieldFilter('   user_id', $this->user->getId());
                $ProductMarketPlaceMappingFilterObjs  = $ProductMarketPlaceMappingFilterObj->getList(false, 1);
                $currentArr['status_message'] = '';
                $dated = '';
                if(count($ProductMarketPlaceMappingFilterObjs) >0 )
                {
                    if($ProductMarketPlaceMappingFilterObjs[0]->getStatus() != '')
                    {
                        $currentArr['status_message'] = '<a class="popovers" data-trigger="hover" data-content="' . $ProductMarketPlaceMappingFilterObjs[0]->getMessage() . '" >' . $ProductMarketPlaceMappingFilterObjs[0]->getStatus() . '</a>';
                    }                   
                }
                $currentArr['product_name'] = $productsFilterObj->getProductName();
                $dated = date("Y-m-d H:i:s", $productsFilterObj->getDateAdded());
                $currentArr['created'] = $dated;
                $currentArr['asin'] = $productsFilterObj->getAsin();
                $currentArr['sku'] = $productsFilterObj->getSku();
                //$currentArr['hs_code'] = $productsFilterObj->getHsCode();
                $currentArr['quantity'] = $productsFilterObj->getQuantity();
                $currentArr['price'] = $productsFilterObj->getPrice();
                $action = '<div class="btn-group" data-container="body" >
                                <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" >';
                        $action .= '<li>
                                        <a title="Edit" href="product_add.php?id=' . $productsFilterObj->getId() . '" data-id="' . $productsFilterObj->getId() . '" class="edit">
                                            <span class="glyphicon glyphicon-pencil"></span> Edit
                                        </a>
                                    </li>';
                $action .= '<li>
                        <a type="button" class="" data-productid="'.$productsFilterObj->getId().'" data-toggle="modal" onClick="openModal($(this))"><span class="glyphicon glyphicon-pencil"></span> Publish</a>
                           </li>';
                    $action .= '</ul>';
                $action .= '</div>';
                $currentArr['actions'] = $action;
                $productImage = "../_assets/product_files/".$productsFilterObj->getImage();
                if (empty($productsFilterObj->getImage()) || !file_exists($productImage)) {
                    $productImage = '../images/thirdparty/No-image-found.png';
                }
                $currentArr['product_image'] = '<img src="'.$productImage.'" alt="No Image" class = "img-responsive" style = "max-width:30px">';
                $currentArr['marketplace'] = '';
                $setDataArr[] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "publish_products_to_market_place") {
            $product_id = $this->form_vars['product_id'];
            $publishArr = $this->form_vars['marketplace'];
            if (!empty($publishArr)){
                $user = SessionManager::getUser();
                $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
                $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
                foreach($publishArr as $publish){
                    $pkid = 0;
                    //echo "I am a product my id is: ".$product_id." my market id is: ".$publish['id']. " and my category id is: ".$publish['category_id'];
                    $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $publish['id']);
                    $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList();
                    if (count($userMarketPlaceMappingObj) > 0) {
                        $marketPlaces = new MarketPlaces($userMarketPlaceMappingObj[0]->getMarketPlacesId());
                        include_classes([
                            strtolower($marketPlaces->getClassName()) . '.class',
                        ]);
                        $className = ucwords($marketPlaces->getClassName());
                        //$authdata = json_decode($userMarketPlaceMappingObj[0]->getAuthData());
                        if (!empty($className)) {
                            $marketPlaceClassObj = new $className($userMarketPlaceMappingObj[0]);
                            if ($className == 'Amazon') {
                                $AmazonProductApiResultFilterObj = new AmazonProductApiResultFilter();
                                $AmazonProductApiResultFilterObj->addFieldFilter('    product_id', $product_id);
                                $AmazonProductApiResultFilterObj->addFieldFilter('user_id', $user->getId());  /// user account id filter
                                $AmazonProductApiResultFilterObj->AddOrderById(false);
                                $AmazonProductApiResultFilterObjs = $AmazonProductApiResultFilterObj->getList();
                                $AmazonProductApiResultFilterObj = $AmazonProductApiResultFilterObjs[0];
                                if (count($AmazonProductApiResultFilterObjs) == 0 || $AmazonProductApiResultFilterObjs[0]->getFeedMessage() == 'SUCCESS'){
                                    $result = $marketPlaceClassObj->productListingApi([$product_id], [$publish['category_id']]);
                                    $response = $this->saveAmazonMessage($pkid, $result, $product_id, $publish, 'Product');
                                }else {
                                    $pkid = $AmazonProductApiResultFilterObjs[0]->getId();
                                        $AmazonProductApiResultFilterObj = $AmazonProductApiResultFilterObjs[0];
                                        $feedMessage = $AmazonProductApiResultFilterObj->getFeedMessage();
                                        if($feedMessage != 'SUCCESS') {
                                            $feedType = $AmazonProductApiResultFilterObj->getFeedType();
                                            if($feedType == 'Product') {
                                                $result = $marketPlaceClassObj->productListingApi([$product_id], [$publish['category_id']]);
                                                $response = $this->saveAmazonMessage($pkid, $result, $product_id, $publish, 'Product');
                                            } else if($feedType == 'Inventory') {
                                                $result = $marketPlaceClassObj->inventoryListingApi([$product_id]);
                                                $response = $this->saveAmazonMessage($pkid, $result, $product_id, $publish, 'Inventory');
                                            } else if($feedType == 'Pricing') {
                                                $result = $marketPlaceClassObj->priceListingApi([$product_id]);
                                                $response = $this->saveAmazonMessage($pkid, $result, $product_id, $publish, 'Pricing');
                                            }
                                        }
                                    }
                                }
                            } else {
                                $response["status"] = "ERROR";
                                $response["message"] = "Market place is not configured.";
                            }

                        echo json_encode($response);
                        die;
                    }
                }
            }
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "product_market_place") {
          
            $productId = $this->form_vars["productId"];
           
            $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFilter("user_account_id = '".$this->user->getUserAccountId()."'");
            $activeMarketPlaces = $userMarketPlaceMappingFilter->getList();
            print_r($activeMarketPlaces); die;
            
            //$marketOrderDetails = new MarketPlaceOrderDetails($marketPlaceOrderDetailId);
           /* $marketOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
            $marketOrderDetailsFilter->addFieldFilter('marketplace_order_id', $marketPlaceOrderId);
            $orderItems = $marketOrderDetailsFilter->getColumnList('*');
            
            $marketPlaceOrder = new MarketPlaceOrder($marketPlaceOrderId);
            ///Getting marketplace logo
            $marketPlaces = new MarketPlaces($marketPlaceOrder->getMarketPlaceId());
            $marketPlaceLogo = "../images/thirdparty/" . strtolower($marketPlaces->getIntegrationLogo());

            ///////////////END/////////////////
            $countryId = $marketPlaceOrder->getReceiverCountryId();
            $countryObj = new Country($countryId);
            $country = $countryObj->getIso();
            
            // MarketPlace order Details Table
            //$title = $marketOrderDetails->getTitle();
            //$asin = $marketOrderDetails->getAsin();
            //$quantity = $marketOrderDetails->getQuantityPurchased();
            //$currency = $marketOrderDetails->getCurrency();
            //$itemId = $marketOrderDetails->getmarketplaceItemId();
           
            // MarketPlace order table
            $output = '';
            $amount = $marketPlaceOrder->getOrderTotal();
            $contact = $marketPlaceOrder->getReceiverName();
            $addressLine1 = $marketPlaceOrder->getReceiverAddressLine1();
            $addressLine2 = $marketPlaceOrder->getReceiverAddressLine2();
            $city = $marketPlaceOrder->getReceiverCity();
            $postcode = $marketPlaceOrder->getReceiverPostCode();
            $telephone = $marketPlaceOrder->getReceiverPhone();
            //$output .= '<div id= "testdiv"> <img src='.$marketPlaceLogo.'></img></div>';
            $output .= '<div id ="res_message_popup" class="row display-none">
                            <div class="col-md-12">
                            <div class="alert alert-success"></div>
                            </div>
                            </div>';
            $output .= '<div class="col-md-12">
                        <h4 class="text-primary"><strong>Order Detail</strong><span class="pull-right">';
            if($marketPlaceOrder->getOrderStatus() == 'Shipped') {
                $output .= '<small class="margin-right-10">Shipped On: ' . $marketPlaceOrder->getShippedDate() . '</small>';
            }
             $output .= $this->getStatus($marketPlaceOrder->getOrderStatus()) . '</span></h4>
                            <table  class="table table-striped table-bordered table-hover">
                            <tr>                            
                            <td><strong>Item ID</strong></td>
                            <td><strong>Title</strong></td>
                            <td><strong>Asin</strong></td>
                            <td><strong>SKU</strong></td>
                            <td><strong>Qty</strong></td>
                            <td><strong>Price</strong></td>
                            </tr>';
            
            foreach($orderItems as $itemDetail)
            {
                $output .= '<tr>
                            <td>' . $itemDetail->getMarketplaceItemId() . ' </td>                       
                            <td>' . $itemDetail->getTitle() . ' </td>                       
                            <td>' . $itemDetail->getAsin() . '</td>
                            <td>' . $itemDetail->getsku() . '</td>
                            <td>' . $itemDetail->getquantityPurchased() . '</td>
                            <td>' . $itemDetail->getItemPrice().' '.$itemDetail->getCurrency(). '</td>                        
                            </tr>';
                            
                            
            }
            $output .= '</table></div>';
            $output .= '<div class="col-md-12">
                            <h4 class="text-primary"><strong>Shipping Detail</strong></h4>
                            <table  class="table table-striped table-bordered table-hover">
                            <tr>
                            <td><strong>Receiver Name:</strong></td>
                            <td>' . $contact . '</td>
                            </tr>
                            
                            <tr>
                            <td><strong>Country:</strong></td>
                            <td>' . $country . '</td>
                            </tr>
                            <tr>
                            <td><strong>PostCode:</strong></td>
                            <td>' . $postcode . '</td>
                            </tr>
                            <tr>
                            <td>
                            <strong>Telephone:</strong>
                            </td>
                            <td>' . $telephone . '</td>                        
                            </tr>
                            <tr>
                            <td>
                            <strong>Address:</strong> 
                            <td> ' . $addressLine1 . ' ' . $addressLine2 . ' ' . $city . ' </td>                       
                            </tr>
                            </table>
                            </div>
                        </div>';
            $output .= '<div class="col-md-12"><h4 class="text-primary"><strong>OneWorld Service</strong></h4>';

            //if shipment is already in the system and label has been generated then don't show weight service dropdown etc.

            if ($consignmentId > 0) {
                $con = new Consignment($consignmentId);
                $weight = $con->getWeight();
                $numberPieces = $con->getNumberPieces();
                $serviceId = $con->getServiceId();
                $ser = new Services($serviceId);
                $serviceName = $ser->getName();

                $labelUrl = SETTING_URL . '_assets/pdf/' . $con->getLabelFile();
                $numberPieces = $con->getNumberPieces();

                $output .= '<input type="hidden" class="form-control form-filter" name="weight" id= "weight" value=' . $weight . '>
                                            <input type="hidden" class="form-control form-filter" name="numberPieces" id= "noofpieces" value = ' . $numberPieces . '>
                                            <input type="hidden" class="form-control form-filter" name="service" id= "service" value = ' . $serviceName . '>';

                $output .= '
                                             <div class="row">
                                             <div class="col-md-4">
                                             <strong>Weight:</strong>
                                             <span>' . $weight . '</span>
                                             </div>
                                             <div class="col-md-4">
                                             <strong>Service:</strong>
                                             <span>' . $serviceName . '</span>
                                             </div>
                                             <div class="col-md-4">
                                             <strong>No. of Pieces:</strong>
                                             <span>' . $numberPieces . '</span>
                                             </div>';
            } else {
                $output .= '<div class="row">	
                                <div class="col-md-4">

                                <strong>Select a Service:</strong>';
                $userAccountId = $this->user->getUserAccountId();
                $fromCountry = $this->user->getCountryId();
                $serviceFilter = new ServiceFilter();
                $servicesData = $serviceFilter->getUserAccountServices($userAccountId, $fromCountry, $countryId, 'D');
                $output .= '<select name="service" id="service" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Service" data-container="body" placeholder="Service">';
                $output .= '<option value="">Please Select</option>';
                /*
                 *   Standard base customer
                 */
             /*   $selectedStr = "";
                if (count($servicesData) > 0) {
                    foreach ($servicesData as $userServiceData) {
                        $selectedStr = ($userServiceData->getId() == $selected ? ' selected="selected"' : '');
                        $output .= '<option ' . $selectedStr . '" value="' . $userServiceData->getId() . '">' . $userServiceData->getName() . '</option>';
                    }
                }
                $output .= '</select>                                          
                                           </div>
                                           <div class="col-md-4">
                                           <strong>Weight:</strong>
                                           <input type="text" class="form-control form-filter" name="weight" id= "weight">
                                           </div>
                                           <div class="col-md-4">
                                           <strong>Number of Pieces:</strong>
                                           <input type="text" class="form-control form-filter" name="number_pieces" id= "number_pieces">
                                           </div>';
            }
            $output .= '<input type="hidden" name="marketplaceorderid" id="marketplaceorderid" value="' . $marketPlaceOrderId . '" class="form-control"/>
                                       <input type="hidden" name="marketplaceorderdetailid" id="marketplaceorderdetailid" value="' . $marketPlaceOrderDetailId . '" class="form-control"/>
                                       <input type="hidden" name="labelLink" id="labelLink" value="' . $labelUrl . '" class="form-control"/> 
                                       </div></div>'; */
            echo $output;
            exit; 
        } 

        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "delete") {
            $id = $this->form_vars['id'];
            $attributesFilter = new AttributesFilter();
            $attributesFilter->where(['id' => $id]);
            $attributesFilter->delete();
            $return = [
                'status' => 'success',
                'message' => 'Attribute delete successfully'
            ];
            echo json_encode($return);
            return json_encode($return);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_marketplace_categories"){
            $marketsCategories = [];
            $id = $this->form_vars['id'];
            $index_id = $this->form_vars['index_id'];
            $categoryParentId = '';
            $includeParent = true;
            $allowedLevel = 0;
            $catDdl = "";
            $catDdl =  Ddl::showTreeDropdown('marketplace['.$index_id.'][category_id]', 'categories', 'name', 'node_id', $categoryParentId, ["market_place_id = '".DbAccess3::escape($id)."' AND is_active = '1'"], $selectedCategory, "Select Category", 'class="bs-select input-sm form-control form-filter selectpicker pull-right category_id" placeholder="Category"  data-live-search="true" data-show-subtext="true"', "category_id_".$index_id, "", '', '', 'owe_16_', $includeParent, $allowedLevel, 'parent_id');
            $catDdl.='<label>Category <span class="required" aria-required="true"> * </span></label>';
            $marketsCategories = [
                'data'=>$catDdl,
                'status' => 'success'
            ];
            echo json_encode($marketsCategories);
            die;
        }
    }

    protected function renderHead()
    {
        ?>

        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
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
        <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.js" type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "product_list.php?action=product_list";
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
                                {"data": "status_message", "bSortable": false},
                                {"data": "product_image", "bSortable": false},
                                {"data": "asin", "bSortable": false},
                                {"data": "product_name", "bSortable": false},
                               
                                {"data": "sku", "bSortable": false},
                               // {"data": "hs_code", "bSortable": false},
                                {"data": "quantity", "bSortable": false},
                                {"data": "price", "bSortable": false},
                                {"data": "created", "bSortable": false},
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
                
                $('button.click')
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                DataTableFun.init();

                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                    $('#elindex-hardcode').val(1);
                }
                $(document).on('click', '.add-more-pieces-keys', function () {
                    elindex++;
                    $('#elindex-hardcode').val(elindex +1);
                    var clone = $(this).parent().parent().parent().clone();
                    var marketPlace = $(clone).find('select.market_place_id').attr('name');
                    var marketPlaceId = $(clone).find('select.market_place_id').attr('id');

                    var category = $(clone).find('select.category_id').attr('name');
                    var categoryId = $(clone).find('select.category_id').attr('id');
                    var selectbarId = $(clone).find('.selectbar').attr('id');

                    $(this).remove();
                    $(clone).find('.market_place_id').attr('name', marketPlace.replace(/\d+/, elindex));
                    $(clone).find('.market_place_id').attr('id', marketPlaceId.replace(/\d+/, elindex));
                     $(clone).find('.bootstrap-select.market_place_id').replaceWith(function () {
                        return $('#market_place_id_'+ elindex, this);
                    });
                    $(clone).find('#market_place_id_' + elindex).selectpicker('refresh');

                    $(clone).find('select.category_id.selectpicker').attr('name', category.replace(/\d+/, elindex));
                    $(clone).find('select.category_id.selectpicker').attr('id', categoryId.replace(/\d+/, elindex));
                    $(clone).find('.selectbar').attr('id', selectbarId.replace(/\d+/, elindex));

                    $(clone).find('.bootstrap-select.category_id').replaceWith(function () {
                         return $('#category_id_' + elindex, this);
                    });
                    $(clone).find('#category_id_' + elindex).selectpicker('refresh');
                    $(clone).find('.market_place_id').attr('index_id', elindex);
                    $(clone).find('button.remove-pieces-key').show();
                    $(clone).find('button.remove-pieces-key').removeClass('initial-button');
                    //console.log($(clone).html());

                    $(clone).appendTo($('.pieces-container'));
                });

                $(document).on('click', '.remove-pieces-key', function () {
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
                                if ($('.pieces-container .remove-pieces-key').length > 1) {
                                    $(el).parent().parent().remove();
                                    $('#elindex-hardcode').val(elindex);
                                    if ($('.add-more-pieces-keys').length == 0) {
                                        var addMore = $(el).parent().find('.add-more-pieces-keys').clone();
                                        $('.pieces-container .remove-pieces-key').last().parent().prepend(addMore);
                                    }
                                } else {
                                    $(el).parent().parent().find('input').val('');
                                }
                            }
                        });
                });
                $(document).on('click', '.publishMarketPlace', function () {
                    var e = $(this);
                    var productId = e.data('productid');
                    var productSku = e.data('sku');
                    $('#productSku').html(productSku);
                   // $('#productIdHeading').html(productId);
                   // $('#flag_marketplace').html("<img src=../images/" + marketPlaceLogo + "  height='40px' />");
                });

                 $('#btn_Save').click(function () {
                  var productId = $('#productId').html();
               // var marketPlaceId = $('#marketplaceorderid').val();

                $.ajax({
                    method: "POST",
                    url: "product_list.php", // your php file name
                    data: {
                        productId: productId,
                        func: 'publishProduct'},
                }).done(function (data) {
                    var rs = JSON.parse(data);
                    if (rs.STATUS === "SUCCESS") {
                        $("#res_message_popup div.alert").addClass('alert-success');
                        $("#res_message_popup div.alert").html(rs.MESSAGE);
                        $("#res_message_popup").show();
                        grid.getDataTable().ajax.reload();
                        $('#btn_ViewLabel').show();
                        $('#btn_DispatchLabel').hide();
                        $('#btn_GenerateLabel').hide();
                    } else if (rs.STATUS === "ERROR") {
                        $("#res_message_popup div.alert").addClass('alert-danger');
                        $("#res_message_popup div.alert").html(rs.MESSAGE);
                        $("#res_message_popup").show();
                        grid.getDataTable().ajax.reload();
                    }

                });
            });

                $(document).on('click', '.delete', function () {
                    var id = $(this).data('id');
                    swal({
                        title: "Are You Sure?",
                        text: "you want to delete category!",
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
                                url: "attributes.php",
                                data: {action: "delete", id: id},
                                dataType: "json",
                                success: function (data) {
                                    $.unblockUI();
                                    if (data.status == "success") {
                                        swal("Success!", data.message, "success");
                                        grid.getDataTable().ajax.reload();
                                    } else {
                                        swal("Sorry!", data.message, "error");
                                    }
                                },
                                error: function () {
                                    $.unblockUI();
                                    alert('error handling here');
                                }
                            });
                        }
                    });
                });
                /* form validation */
                $('#publish_product_form').validate ({
                    // validation rules for registration form
                    errorClass: "error-class",
                    validClass: "valid-class",
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        if(element.parent('.form-group').length) {
                            error.insertAfter(element.parent());
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    onError : function(){
                        $('.input-group.error-class').find('.help-block.form-error').each(function() {
                            $(this).closest('.form-group').addClass('error-class').append($(this));
                        });
                    },

                    rules: {
                        category_id: {
                            required: true
                        },
                        work_place: {
                            required: true
                        }
                    },

                    messages: {
                        category_id: {
                            required: "This field is required."
                        },
                        work_place: {
                            required: "This field is required."
                        },
                        highlight: function(element, errorClass) {
                            $(element).removeClass(errorClass);
                        }
                    },

                });
            });
            function savePublishment(sendemail=false) {
                var checkValid = $('#publish_product_form').valid();
                if(checkValid) {
                    var form_data = $("#publish_product_form").serializeArray();
                    form_data.push({name: "func", value: 'publish_products_to_market_place'});
                    $.ajax({
                        url: 'product_list.php',
                        data: form_data,
                        type: 'post',
                        dataType: "json",
                        success: function (response) {
                            $("#myModal").modal('hide');
                            if (response.status == "SUCCESS") {
                                swal({
                                    type: 'success',
                                    html: true,
                                    title: "Success!",
                                    text: response.message
                                });
                                //$("#publish_product_form")[0].reset();
                            } else {
                                //swal("Sorry!", "Some thing went wrong please contact to support", "error");
                                swal({
                                    type: 'error',
                                    html: true,
                                    title: "Error!",
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            }
            function loadCategories(that) {
                var index_val = that.attr('index_id');
                var marketPlaceId = that.val();
                $.ajax({
                    type: "POST",
                    url: "product_list.php",
                    data: {action: "get_marketplace_categories", id: marketPlaceId, index_id:index_val},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == "success") {
                            $("#selectbar_"+index_val).html(data.data);
                            $('#category_id_' + index_val).selectpicker('refresh');
                        } else {
                            swal("Sorry!", data.message, "error");
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            }
            function openModal(that)
            {
                var product_id = that.data('productid');
                $("#product_id").val(product_id);
                $("#myModal").modal('show');
            }
        </script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
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
                <div class="caption"><i class="icon-bar-chart"></i>
                    Product List
                </div>
                <div class="actions">
                    <a href="product_add.php" class="btn btn-sm blue" id="btn_product_add"  >
                        <span></span><i class="fa fa-plus"></i>&nbsp;
                        Product Add
                    </a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <form class="form-horizontal form-row-seperated">
                    <div class="table-container">
                        <div class="table-actions-wrapper"></div>
                        <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                            <thead>
                            <tr role="row" class="heading">
                                <th>Action</th>
                                <th>Published</th>
                                <th>Image</th>
                                <th>ASIN</th>
                                <th>Name</th>
                                
                                <th>SKU</th>
                              <!--  <th>HS Code</th> -->
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Created</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td width = "6%">
                                    <button class="btn btn-xs blue filter-submit btn-outline"><i class="fa fa-search"></i>
                                    </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline">
                                        <div><i class="fa fa-times"></i></div>
                                    </button>
                                </td>
                                <td width = "5%">
                                    <div class = "margin-bottom-5">
                                    <?php 
                                     echo Ddl::generateMarketPlaceDDLWithImage('search_marketplace', '', 'id', ' class="bs-select input-sm form-control form-filter" data-live-search="true"  data-show-subtext="true"');
                                    ?>                                        
                                    </div>
                                    
                                    <div class = "margin-bottom-5">
                                    <?php
                                     $arrayTypeValues = array('Active' => 'Active', 'In Progress' => 'In Progress', 'Error' => 'Error');
                                     echo Ddl::generateArrayDDL('search_Active', $arrayTypeValues, "", "Status", ' class="input-sm form-control form-filter bs-select"', "", 'search_Active', 'Status', '');
                                    ?>  
                                    </div>
                                </td>
                                <td>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter" name="asin"/>
                                </td> 
                                <td>
                                    <input type="text" class="form-control form-filter" name="product_name"/>
                                </td>                                
                                <td>
                                    <input type="text" class="form-control form-filter" name="sku"/>
                                </td>
                                <!--<td>
                                    <input type="text" class="form-control form-filter" name="hs_code"/>
                                </td> -->
                                <td>
                                    <input type="text" class="form-control form-filter" name="quantity"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter" name="price"/>
                                </td>
                                <td>
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="search_Date_from" placeholder="From" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="search_Date_to" placeholder="To" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Publish Product</h4>
                    </div>
                    <div class="modal-body">
                        <form action="product_list.php" id="publish_product_form"
                            <div class="pieces-container" id="pieces-container">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <div class="has-float-label">
                                            <?php
                                            echo Ddl::generateMarketPlaceDDLWithImage('marketplace[0][id]', '', 'id', ' class="bs-select input-sm form-control selectpicker form-filter pull-right market_place_id" index_id=0 onChange="loadCategories($(this));" data-live-search="true"  data-show-subtext="true"', 'market_place_id_0');
                                            ?>
                                            <label>Market Place <span class="required" aria-required="true"> * </span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <div class="has-float-label">
                                            <span class="selectbar" id="selectbar_0">
                                                <?php
                                                $categoryParentId = '';
                                                $selectedCategory = [];
                                                $includeParent = true;
                                                $allowedLevel = 0;
                                                //echo Ddl::showTreeDropdown('marketplace[0][category_id]', 'categories', 'name', 'id', $categoryParentId, array("is_active = '1'"), $selectedCategory, "Select Category", 'class="bs-select input-sm form-control form-filter selectpicker pull-right category_id" placeholder="Category"  data-live-search="true" data-show-subtext="true"', "category_id_0", "", '', '', 'owe_16_', $includeParent, $allowedLevel, 'parent_id');
                                                ?>
                                                <select id="category_id_0" name="marketplace[0][category_id]" class="bs-select input-sm form-control form-filter selectpicker pull-right category_id" placeholder="Category" data-live-search="true" data-show-subtext="true" data-container="body">
                                                </select>
                                            <label>Category <span class="required" aria-required="true"> * </span></label>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-success add-more-pieces-keys"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger remove-pieces-key initial-button"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                                <input type="hidden" name="product_id" id="product_id" value="" >
                                <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="0" />
                        </div>
                            <div class="modal-footer">
                                <button type="button" id="save_publishment" class="btn btn-primary" onclick="savePublishment()" >Publish Item</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                        </form>
                    </div>
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

    protected function saveAmazonMessage($pkid, $result, $product_id, $publish, $message_type)
    {
        $user = SessionManager::getUser();
        $addedAt = date("Y-m-d H:i:s", time());
        $AmazonProductApiResultObj = new AmazonProductApiResult();
        $AmazonProductApiResultObj->setProductId($product_id);
        $AmazonProductApiResultObj->setFeedType($message_type);
        $AmazonProductApiResultObj->setFeedId($result['MESSAGE']);
        if ($result['STATUS'] == 'SUCCESS') {
            $AmazonProductApiResultObj->setFeedMessage("Your product has been submitted and it may take up to 15 minutes to process.");
            $AmazonProductApiResultObj->setFeedStatus("SUBMITTED");
        }else {
            $AmazonProductApiResultObj->setFeedMessage($result['MESSAGE']);
            $AmazonProductApiResultObj->setFeedStatus("ERROR");
        }
        $AmazonProductApiResultObj->setUserId($user->getId());
        $AmazonProductApiResultObj->setAddedAt($addedAt);
        $AmazonProductApiResultObj->save();
        $response = $this->saveMarketplaceMessage($result, $product_id, $publish, $message_type);
        return $response;
    }

    protected function saveMarketplaceMessage($result, $product_id, $publish, $message_type)
    {
        $pkid = 0;
        $user = SessionManager::getUser();
        $productMarketPlaceMappingFilterObj = new ProductMarketPlaceMappingFilter();
        $productMarketPlaceMappingFilterObj->addFieldFilter('    product_id', $product_id);
        $productMarketPlaceMappingFilterObj->addFieldFilter('user_id', $user->getId());  /// user account id filter
        $productMarketPlaceMappingFilterObj->addFieldFilter('marketplace_id', $publish['id']);  /// user account id filter
        $productMarketPlaceMappingFilterObj->AddOrderById(false);
        $productMarketPlaceMappingFilterObjs = $productMarketPlaceMappingFilterObj->getList();

        if (!empty($productMarketPlaceMappingFilterObjs)){
            $pkid = $productMarketPlaceMappingFilterObjs[0]->getId();
            $ProductMarketPlaceMappingObj = new ProductMarketPlaceMapping($pkid);
        }else {
            $ProductMarketPlaceMappingObj = new ProductMarketPlaceMapping();
        }

        $ProductMarketPlaceMappingObj->setProductId($product_id);
        $ProductMarketPlaceMappingObj->setMarketPlaceId($publish['id']);
        $ProductMarketPlaceMappingObj->setCategoryId($publish['category_id']);
        if ($result['STATUS'] == 'SUCCESS') {
            $ProductMarketPlaceMappingObj->setStatus('In Progress');
            $ProductMarketPlaceMappingObj->setMessage("Your product has been submitted and it may take up to 15 minutes to process."); // it may take
        }else {
            $ProductMarketPlaceMappingObj->setStatus('Error');
            $ProductMarketPlaceMappingObj->setMessage($result['MESSAGE']);
        }
        
        $ProductMarketPlaceMappingObj->setUserId($user->getId());
        $ProductMarketPlaceMappingObj->save();
        if ($result['STATUS'] == 'SUCCESS') {
            $response["status"] = "SUCCESS";
            $response["message"] = "Your product has been submitted and it may take up to 15 minutes to process.";
        }else {
            $response["status"] = "ERROR";
            $response["message"] = $result['MESSAGE'];
        }
        return $response;
    }
}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();

