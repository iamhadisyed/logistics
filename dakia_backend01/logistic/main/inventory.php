<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([ 'errorlist.class'
                ],'visualcomponents');
include_classes([
    'skuorder.class',
    'skuorderfilter.class',
    'sku.class',
    'skufilter.class',
    'servicerangemapping.class',
    'servicerangemappingfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'licenceplate.class',
    'licenceplatefilter.class']);
include_classes([
    'tcpdf',
        ], '3rdparty/tcpdf');

include_classes([
    'pdfmerger'], 'labels');
 
 
class Page extends BasePage {

    private $licencePlate;
    private $skuorder;
    private $id = NULL;
    private $breadcrumb = '';
    private $user = NULL;
    private $isCountry = 0;
    private $inventoryRequest;

    /*     * *
     * Controller logic
     */

    protected function init() {

        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'inventory.php' => 'Inventory List'
        );
        $this->setTitle("INVENTORY List");
        
        if (isset($_GET['action']) && $_GET['action'] == "inventorylist_ajax") {
            // Get current user
             $this->inventoryRequest = new stdClass();
            /*
             * Column filter
             * For search
             */
                              
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                
                $sku_search  = $this->form_vars['search_sku'];
                if (!empty($sku_search)) {
                     $this->inventoryRequest->Sku = $sku_search;
                }
                
                $customerCode  = $this->form_vars['warehouse_code'];
                if (!empty($customerCode)) {
                    $this->inventoryRequest->CustomerCode = $customerCode;
                }
                
                $location = $this->form_vars['location'];                
                if (!empty($location)) {
                    $this->inventoryRequest->Location = $location;
                }
                
                $bagNumber = $this->form_vars['container_code'];                
                if (!empty($bagNumber)) {
                    $this->inventoryRequest->TraceID = $bagNumber;
                }
                
                $searchDateFrom = $this->form_vars['search_Date_from'];
                $searchDateTo = $this->form_vars['search_Date_to'];
                if (!empty($searchDateFrom) || !empty($searchDateTo))
                {
                    $this->inventoryRequest->CreateTime->BeginDateTime = date('Y-m-d\TH:i:s',strtotime($searchDateFrom));
                    $this->inventoryRequest->CreateTime->EndDateTime = date('Y-m-d\TH:i:s',strtotime($searchDateTo));
                }
             //   print_r($this->inventoryRequest); die;
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                //print_r($this->form_vars['order']); die;
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
            }

            $inventoryResult = $this->GetInventory();
            $iTotalRecords = $inventoryResult->RecordCount;
         
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            
            $this->inventoryRequest->PageIndex = $iDisplayStart;
            $this->inventoryRequest->PageSize = $iDisplayLength;
            $inventoryResult = $this->GetInventory();
             
            if ($dataTableColumnName != '') {
            //    $this->sku->orderBy($dataTableColumnName, $orderFalse);
            } else {
              //  $this->sku->orderBy('id', desc);
            }
            //print_r($inventoryResult->RecordData); die;
            $rangeDataArr = array();
            foreach ($inventoryResult->RecordData as $inventoryResultList) {
                
                
                $rangeArr['actionss'] = '';
                $rangeArr['sku'] = $inventoryResultList->SKU;
                $ImageFileName = $inventoryResultList->ImageAddress;
                
                $imageName = substr($ImageFileName, 29);
                
                if(!file_exists('../_assets/images/sku_images/thumbnails/'.$imageName))
                {                    
                    $rangeArr['Image'] =  "<img src='../_assets/images/sku_images/thumbnails/no_image_found.jpg'/>";
                }
                else
                {
                    $rangeArr['Image'] =  "<img src='../_assets/images/sku_images/thumbnails/".$imageName."'/>";
                }
                
                $rangeArr['warehouse_code'] = $inventoryResultList->WarehouseCode;
                $rangeArr['location'] = $inventoryResultList->Location;
                $rangeArr['container_code'] = $inventoryResultList->ContainerCode;
                $rangeArr['quantity'] = $inventoryResultList->Qty;
                $rangeArr['quantity_available'] = $inventoryResultList->QtyAvailable;
                $rangeArr['create_on'] = date('Y-m-d H:i:s', strtotime($inventoryResultList->CreateOn));
                $rangeDataArr[] = $rangeArr;
            }
            $rangeDataArr['data'] = $rangeDataArr;
            $rangeDataArr['draw'] = $sEcho;
            $rangeDataArr['recordsTotal'] = $iTotalRecords;
            $rangeDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($rangeDataArr);
            die;
        }
        
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {

            $sku_id = (int) $_POST['recordid'];

            $this->id = $sku_id;
            $edit_array = array();
            
            $sku = new Sku($sku_id);
            $user = new User($sku->getCustomerId());
            
            $edit_array['id'] = $sku_id;
            $edit_array['sku'] = $sku->getSKU();
            $edit_array['customer_id'] = $user->getUserName();
            $edit_array['declared_name'] = $sku->getDeclaredName();
            $edit_array['description'] = $sku->getDescription();
            $edit_array['length'] = $sku->getLength();
            $edit_array['width'] = $sku->getWidth();
            $edit_array['height'] = $sku->getHeight();
            $edit_array['price'] = $sku->getPrice();
            $edit_array['gross_weight'] = $sku->getGrossWeight();
            $edit_array['net_weight'] = $sku->getNetWeight();
            $edit_array['hscode'] = $sku->getHSCode();
            $edit_array['notes'] = $sku->getNotes();
            $edit_array['active'] = $sku->getActive();
            //print_r($edit_array); die;
            echo json_encode($edit_array);
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
                                "url": "inventory.php?action=inventorylist_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "sku"},
                                {"data": "Image"},
                                {"data": "warehouse_code"},
                                {"data": "location"},
                                {"data": "container_code"}, 
                                {"data": "quantity"}, 
                                {"data": "quantity_available"}, 
                                {"data": "create_on"}, 
                               
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
                $('button.click')
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                DataTableFun.init();
                
                $('#btn_Save').click(function () {
                   
                    $.ajax({
                    method: "POST",
                    url: "add_sku.php",
                    data: $('#AddSKUForm').serialize(),
                    success:function(data)
                    {
                        var rs = JSON.parse(data);
                        $("#AddSKUForm")[0].reset();
                        if(rs.STATUS == 'SUCCESS')
                        {
                            $("#res_message div.alert").removeClass('alert-danger').addClass('alert-success');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                        }
                        else
                        if(rs.STATUS == 'ERROR')
                        {
                            $("#res_message div.alert").removeClass('alert-danger').addClass('alert-success');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                        }    
                    }  
                });
               });  
               
                $(document).on('click', '.btnedit', function () {
                      $("#res_message div.alert").removeClass('alert-success');
                        $("#res_message div.alert").removeClass('alert-danger');
                    var e = $(this);
                    var recordid = e.data('id');
                    
                    $.ajax({
                        method: "POST",
                        url: "add_sku.php",
                        data: {recordid: recordid, func: "editrecord"}
                    }).done(function (data) {

                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");
                        $('#sku').val(t.sku);
                        $('#sku_description').val(t.description);
                        $('#sku_price').val(t.price);
                        $('#sku_length').val(t.length);
                        $('#sku_width').val(t.width);
                        $('#sku_height').val(t.height);
                        $('#sku_net_weight').val(t.net_weight);
                        $('#sku_hscode').val(t.hscode);
                        $('#sku_notes').val(t.notes);
                        var active = t.active;
                        if (active == '1')
                        {
                            $('#active_flag').attr('checked', true);
                            $('#active_flag').bootstrapSwitch('state', true);
                            
                        } else
                        {
                            $('#active_flag').attr('checked', false);
                            $('#active_flag').bootstrapSwitch('state', false);
                        }

                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
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
                
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>
                            Inventory List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>SKU</th>
                                    <th>Image</th>
                                    <th>Warehouse</th>
                                    <th>Location</th>
                                    <th>Bag Number</th>
                                    <th>Total Qty</th>
                                    <th>Available Qty</th>
                                    <th>Create On</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td width = "8%">
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td class="user_acccount_correct_button">
                                       <?php                                       
                                       //echo Ddl::generateDDL('search_sku', "skuFilter", ' active = "1"', "sku", "id", $search_sku, ' class="bs-select input-sm form-control form-filter" data-container="body" data-live-search="true"  data-show-subtext="true"','Select SKU','','search_sku'); 
                                       ?>
                                        <input type="text" class="form-control form-filter" name="search_sku" />
                                    </td>
                                    <td></td>

                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="warehouse_code" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="location" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="container_code" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="quantity" />
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input type="text" class="form-control form-filter" name="quantity_available" />
                                        </div>
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
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
                </div>
            </div>
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
    
     public function wmsAddSkuApi($skuid)
    {
        try 
            {
                $sku = new Sku($skuid);
                $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
                $request = new stdClass();
                $request->SKU = $sku->getSku();
                $request->CustomerCode = 'BRANDS';//$sku->getCustomerId();
                $request->WarehouseCode = "1";
                $request->SKU_Ref1 = '';
                $request->Hazard_Flag = '';
                $request->Active_Flag = $sku->getActive();
                $request->Descr_C = '';
                $request->Descr_E = $sku->getDescription();
                $request->DeclaredNameEN = $sku->getDeclaredName();
                $request->DeclaredNameCN = '';
                $request->GrossWeight = $sku->getGrossWeight();
                $request->NetWeight = $sku->getNetWeight();
                $request->Tare = "1";
                $request->Cube = "1.0";
                $request->Price = $sku->getPrice();
                $request->SKULength = $sku->getLength();
                $request->SKUWidth = $sku->getWidth();
                $request->SKUHeight = $sku->getHeight();
                 $request->ImageAddress = '';
                $request->HSCode = '';
                $request->FirstOP = '';
               // print_r($request);die;
                $response = $client->CreateSku( array("request" => $request));
           //     print_r($response);
             //   echo "REQUEST:\n" . $client->__getLastRequest() . "\n";        
            } 
            catch (Exception $e) 
            {
                return $e->getMessage();		
            }         
    }
    
    public function GetInventory()
    {
        //echo $this->user->getUserAccountId(); die;
        $userAccountId = new CustomerAccount($this->user->getUserAccountId());
        //echo $userAccountId; 
        $userAccountName = $userAccountId->getUserAccount();
        //echo $userAccountName; die;
        $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
       // $this->inventoryRequest = new stdClass();
        //echo $userAccountName; die;
        $this->inventoryRequest->CustomerCode = $userAccountName;
        //$this->inventoryRequest->PageIndex = 3;
        //$this->inventoryRequest->PageSize = 10;
       // print_r($this->inventoryRequest);
        //   die;
        $response = $client->GetInventory( array("request" => $this->inventoryRequest));
        if($response->GetInventoryResult->Success == '1')
        {
            $result = json_decode($response->GetInventoryResult->DataStr);
            
            return $result;
        }
    }      

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
