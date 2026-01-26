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

    /*     * *
     * Controller logic
     */

    protected function init() {

        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'add_sku.php' => 'Add SKUs'
        );
        $this->setTitle("SKU Order List");
        
        if ($this->form_vars["form_action"] == 'add_SKU') 
        {
            if(isset($this->form_vars["sku"]) && $this->form_vars["sku"] != '')
            {
                $sku = $this->form_vars["sku"];
            }
            else
            {
                $output['MESSAGE'][] = 'Please Enter SKU </br>';
            }
            
            if(isset($this->form_vars["sku_description"]))
            {
                $description = $this->form_vars["sku_description"];
            }
            
            if(isset($this->form_vars["sku_name"]))
            {
                $name = $this->form_vars["sku_name"];
            }
            
            if(isset($this->form_vars["sku_notes"]))
            {
                $notes = $this->form_vars["sku_notes"];
            }
            
            if(isset($this->form_vars["sku_hscode"]))
            {
                $hscode = $this->form_vars["sku_hscode"];
            }
            
            if(isset($this->form_vars["sku_net_weight"]) && $this->form_vars["sku_net_weight"] != '')
            {
                $netWeight = $this->form_vars["sku_net_weight"];
            }
            else
            {
                $output['MESSAGE'][] = 'Please Enter Net Weight </br>';
            }
           
            if(isset($this->form_vars["sku_price"]) && $this->form_vars["sku_price"] != '')
            {
                $price = $this->form_vars["sku_price"];
            }
            else
            {
                $output['MESSAGE'][] = 'Please Enter SKU Price </br>';
            }
            
            if(isset($this->form_vars["sku_currency"]) && $this->form_vars["sku_currency"] != '')
            {
                $currency = $this->form_vars["sku_currency"];
            }
            else
            {
                $output['MESSAGE'][] = 'Please Select Currency </br>';
            }
           
            if(isset($this->form_vars["sku_length"]) && $this->form_vars["sku_length"] != '')
            {
                $length = $this->form_vars["sku_length"];
            }
            else
            {
                $output['MESSAGE'][] = 'Please Enter SKU Length </br>';
            }
           
            if(isset($this->form_vars["sku_width"]) && $this->form_vars["sku_width"] != '')
            {
                $width = $this->form_vars["sku_width"];
            }
            else
            {
                $output['MESSAGE'][] = 'Please Enter SKU Width </br>';
            }
           
            if(isset($this->form_vars["sku_height"]) && $this->form_vars["sku_height"] != '')
            {
                $height = $this->form_vars["sku_height"];
            }
            else
            {
                $output['MESSAGE'][] = 'Please Enter SKU Height </br>';
            }
           
            
            if(isset($this->form_vars["active_flag"]))
            {
                $active = '1';
            }
            else
            {
                $active = '0';
            }
            
            $error_array = "";
            $sku_image = '';
          
            if (isset($_FILES["sku_image"]) && trim($_FILES["sku_image"]["name"]) != '') {
                $newPath = "../_assets/images/sku_images/";
                if (!file_exists($newPath))
                    @mkdir($newPath, 0775);
                $allowedExts = array("gif", "jpeg", "jpg", "png");
                $temp = explode(".", $_FILES["sku_image"]["name"]);
                $extension = end($temp);
                if ((($_FILES["sku_image"]["type"] == "image/gif") || ($_FILES["sku_image"]["type"] == "image/jpeg") || ($_FILES["sku_image"]["type"] == "image/jpg") || ($_FILES["sku_image"]["type"] == "image/pjpeg") || ($_FILES["sku_image"]["type"] == "image/x-png") || ($_FILES["sku_image"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                    if ($_FILES["sku_image"]["error"] > 0) {
                        $error_array[] = "Return Code: " . $_FILES["sku_image"]["error"] . "<br>";
                    } else {
                        $sku_image = str_replace(' ', '_', time() . $_FILES["sku_image"]["name"]);
                        move_uploaded_file($_FILES["sku_image"]["tmp_name"], "../_assets/images/sku_images/" . $sku_image);
                        $thumb = new easyphpthumbnail;
                        $thumb->Thumblocation = '../_assets/images/sku_images/thumbnails/';
                        $thumb->Thumbsaveas = $extension;
                        $thumb->Thumbfilename = $sku_image;
                        $thumb->Clipcorner = array(2, 15, 0, 0, 1, 1, 0);

                        $thumb->Thumbsize = 46;
                        $thumb->Createthumb("../_assets/images/sku_images/" . $sku_image, 'file');
                    }
                } else {
                    $output['MESSAGE'][] = formatMessages(ERROR_INVALID_FILE);
                }
            }
            if($output != '')
            {
                $output['STATUS'] = "ERROR";
                echo json_encode($output);
                die;
            }
         
            // Add new SKU to smartTrack 
            $skuFilter = New SkuFilter();
            $skuFilter->addFilter("sku = '".$sku."'");
            $result = $skuFilter->getList('*');
            
            if(count($result) <= 0)
            {
                $skuObj = new SKU();
            }
            else
            {
                $skuObj = new SKU($result[0]->getId());
            }
                $skuObj->setSku($sku);
                $skuObj->setCustomerId($this->user->getId());
                $skuObj->setDeclaredName($name);
                $skuObj->setDescription($description);
                $skuObj->setLength($length);
                $skuObj->setWidth($width);
                $skuObj->setHeight($height);
                $skuObj->setGrossWeight($grossWeight);
                $skuObj->setNetWeight($netWeight);
                $skuObj->setPrice($price);
                $skuObj->setCurrency($currency);
                $skuObj->setActive($active);
                $skuObj->setNotes($notes);
                $skuObj->setHscode($hscode);
                $skuObj->setUserId($this->user->getId());
                $skuObj->setDateCreated(strtotime(date('Y-m-d H:i:s')));
                //echo $sku_image; die;
                if (!empty($sku_image))
                   $skuObj->setImage('../_assets/images/sku_images/'.$sku_image);
                $skuObj->save();

                if($skuObj->getId() != '')
                {
                    $message = $this->wmsAddSkuApi($skuObj->getId());
                    if(!$message)
                    {
                        $output["MESSAGE"] = $message;
                        $output["STATUS"] = "ERROR";
                    }
                    else
                    {
                        $output["STATUS"] = "SUCCESS";
                        $output["MESSAGE"] = "SKU has been saved successfully";
                    }
                }
                
                else
                {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = 'Data has not been saved.';
                }
            echo json_encode($output);
            exit;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_csv_frm') {
                $csvObj = new SKU();
                $sql = "SELECT  * FROM `sku` limit 1";
                $res = $csvObj->getDataFromSql($sql);
                
                //if (count($res) > 0) {
                    $returnString = "SKU,Name,Description,Notes,Net Weight,Price,Currency,HS Code,Length,Width,Height,Active,Image";
                    $fileName = "sku_" . time();
                    $returnString .= "\r\n";
                    $returnString .=  ",";
                    $returnString .=   ",";
                    $returnString .=  ",";
                    $returnString .=  ",";
                    $returnString .=   ",";
                    $returnString .=  ",";
                    $returnString .=  ",";
                    $returnString .=  ",";
                    $returnString .=   ",";
                    $returnString .=  ",";
                    $returnString .= "";
                //}
                    header("Content-type: text/csv");
                    header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    echo $returnString;
                    die;
                }
           // }
        //}
        
        //Handle Import csv
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            
            $output = array();
            $output['status'] = 'success';
            $output['message'] = formatMessages(SUCCESS_UPLOADED);
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    //$user = SessionManager::getUser();
                    $userId = $this->user->getId();
                    
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $relPath = '../_assets/sku_csv/' . $new_file_name;
                    if (!file_exists("../_assets/sku_csv/"))
                        @mkdir("../_assets/sku_csv/", 0775);
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {

                        $row = 0;

                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = '';
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                if ($row < 1) {
                                    $row++;
                                    continue;
                                }
                                $sku = $data[0];
                                //$customerId = $data[1];
                                $skuName = $data[1];
                                $description = $data[2];
                                $notes = $data[3];
                                $netWeight = $data[4];
                                $price = $data[5];
                                $currency = $data[6];
                                $hsCode = $data[7];
                                $length = $data[8];
                                $height = $data[9];
                                $width = $data[10];
                                $active = $data[11]; 
                                $image = $data[12];
                                
                                $imagePath = "../_assets/images/sku_images/" . basename($image);
                                $imageContents = file_get_contents($image);
                                file_put_contents($imagePath, $imageContents);
                               
                                $thumb = new easyphpthumbnail;
                                $thumb->Thumblocation = '../_assets/images/sku_images/thumbnails/';
                                $thumb->Thumbsaveas = $extension;
                                $thumb->Thumbfilename = basename($image);
                                $thumb->Clipcorner = array(2, 15, 0, 0, 1, 1, 0);

                                $thumb->Thumbsize = 46;
                                $thumb->Createthumb($imagePath, 'file');
                        

                                $skuFilter = new SkuFilter();
                                $skuFilter->addFilter("     sku = '" . $sku . "'");
                                $skuFilterResult = $skuFilter->getList("id");

                                if (count($skuFilterResult) > 0) {
                                    $skuId = $skuFilterResult[0]->getId();
                                    $skuObj = new SKU($skuId);
                                }
                                else
                                {
                                    $skuObj = new SKU();
                                }

                                ////// Set values in SKU table
                                
                                if($userId != '')
                                {
                                    $skuObj->setCustomerId($userId);
                                }
                                
                                if($skuName != '')
                                {
                                    $skuObj->setDeclaredName($skuName);
                                }
                                
                                if($sku != '')
                                {
                                    $skuObj->setSku($sku);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row. ' row rejected. Please enter SKU for this record';
                                    continue;
                                }

                                if ($netWeight  != '') 
                                {
                                    $skuObj->setNetWeight($netWeight);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected.Please enter Net Weight';
                                    $continue;
                                }
                                
                                if ($description  != '') 
                                {
                                    $skuObj->setDescription($description);                                    
                                }
                                
                                if ($notes  != '') 
                                {
                                    $skuObj->setNotes($notes);                                    
                                }
                                
                                if ($price  != '') 
                                {
                                    $skuObj->setPrice($price);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Price';
                                    $continue;
                                }
                                
                                if ($currency  != '') 
                                {
                                    $skuObj->setCurrency($currency);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Currency';
                                    $continue;
                                }
                                
                                if ($hsCode  != '') 
                                {
                                    $skuObj->setHsCode($hsCode);                                    
                                }
                                
                                if ($length  != '') 
                                {
                                    $skuObj->setLength($length);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Length';
                                    $continue;
                                }
                                
                                if ($width  != '') 
                                {
                                    $skuObj->setWidth($width);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Width';
                                    $continue;
                                }
                                
                                if ($height  != '') 
                                {
                                    $skuObj->setHeight($height);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Height';
                                    $continue;
                                }
                                
                                if ($active  != '') 
                                {
                                    $skuObj->setActive($active);                                    
                                }
                                
                                if ($image  != '') 
                                {
                                    $skuObj->setImage($imagePath);                                    
                                }
                                $skuObj->setDateCreated(strtotime(date('Y-m-d H:i:s')));
                                $skuObj->save();
                 
                                $skuIds[] = $skuObj->getId();                  
                                $row++;
                            }
                        }
                        fclose($handle);
                        
                        if($skuIds != '')
                        {
                            foreach($skuIds as $skuId)
                            {
                                $message = $this->wmsAddSkuApi($skuId);
                            }
                        }
                    } else {
                        $output['message'] = formatMessages(ERROR_FILE_UPLOADED); //'File upload fail.';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = formatMessages(ERROR_INVALID_FILE);
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = formatMessages(ERROR_FILE_EMPTY);
                $output['status'] = 'fail';
            }
            if($errorMsg != '')
            {
                $output['status'] = 'Fail';
                $output['message'] = $errorMsg;
            }
            echo json_encode($output);
            exit;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == "skulist_ajax") {
            // Get current user
            $this->sku = new SkuFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $sku_id  = $this->form_vars['search_sku'];
                if (!empty($sku_id)) {
                    $this->sku->addFilter(" id = '".$sku_id."' ");
                }
                
                $name = $this->form_vars['name'];                
                if (!empty($name)) {
                    $this->sku->addFilter('  declared_name = "'.$name.'"');
                }
                
                $description = $this->form_vars['description'];                
                if (!empty($description)) {
                    $this->sku->addFilter('  description = "'.$description.'"');
                }
                
                $price = $this->form_vars['price'];                
                if (!empty($price)) {
                    $this->sku->addFilter('  price = "'.$price.'"');
                }
                
                $searchActive = $this->form_vars['search_Active'];
                if (trim($searchActive) != '') {
                    $this->sku->addFilter('active = "'.$searchActive.'"');
                }
                
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
                //print_r($dataTableColumnName); die;
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $this->sku->orderBy(strtolower($dataTableColumnName), $orderFalse);
            }

            $iTotalRecords = $this->sku->getCount(false, false);
            //echo $iTotalRecords; die;
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->sku->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->sku->setOffset($iDisplayStart);
            
            if ($dataTableColumnName != '') {
                $this->sku->orderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->sku->orderBy('id', desc);
            }
            $skyList = $this->sku->getList("id, sku, declared_name, description, price, currency, image,active");
            $rangeDataArr = array();
            foreach ($skyList as $skulist) {
                $rangeArr['actionss'] = '';
                $rangeArr['actionss'] .= "<a data-id =" . $skulist->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-pencil'></span> </a>";
                
                $rangeArr['sku'] = $skulist->getSku();
                $user = new User($skulist->getCustomerId());
                $rangeArr['sku_name'] = $skulist->getDeclaredName();
                $rangeArr['description'] = $skulist->getDescription();
                $rangeArr['price'] = $skulist->getPrice().' '.$skulist->getCurrency();
                $imageName = substr($skulist->getImage(), 29);
                
                if(!file_exists('../_assets/images/sku_images/thumbnails/'.$imageName) || $skulist->getImage() == '')
                {
                    $rangeArr['image'] = "<img src='../_assets/images/sku_images/thumbnails/no_image_found.jpg'/>";
                }
                else
                {
                    $rangeArr['image'] = "<img src='../_assets/images/sku_images/thumbnails/".$imageName."'/>";
                }
                $rangeArr['active_flag'] = '<div class="text-center">' . ($skulist->getActive() ? '<span class="label label-sm label-success">Yes</span>' : '<span class="label label-sm label-danger">No</span>') . '</div>';
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
            //echo $sku->getCurrency(); die;
            $edit_array['id'] = $sku_id;
            $edit_array['sku'] = $sku->getSKU();
       //     $edit_array['customer_id'] = $user->getUserName();
            $edit_array['declared_name'] = $sku->getDeclaredName();
            $edit_array['notes'] = $sku->getNotes();
            $edit_array['description'] = $sku->getDescription();
            $edit_array['length'] = $sku->getLength();
            $edit_array['width'] = $sku->getWidth();
            $edit_array['height'] = $sku->getHeight();
            $edit_array['price'] = $sku->getPrice();
            $edit_array['currency'] = $sku->getCurrency();
            $edit_array['net_weight'] = $sku->getNetWeight();
            $edit_array['hscode'] = $sku->getHSCode();
            $edit_array['active'] = $sku->getActive();
            $edit_array['image'] = $sku->getImage();
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
                                "url": "add_sku.php?action=skulist_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss", "bSortable": false},
                                {"data": "sku"},
                                {"data": "sku_name"},
                                {"data": "description"},
                                {"data": "price"},
                                {"data": "image"},
                                {"data": "active_flag"},
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
                 $(document).on('click', '#btnSubmitImport', function () {
                    $("#file_in").val("");
                    $("#csv_upload").modal('show');
                });
                $(document).on('click', '#upload_csv', function () {
                  //  $('#console_window').show();
                    //$('#console_window').html('');
                    //$('#console_window').html("Uploading CSV File....<br />");
                    var file_data = $('#file_in').prop('files')[0];
                    $('#csv_upload').modal('hide');
                    var form_data = new FormData();
                    form_data.append('csv_file', file_data);
                    form_data.append('func', 'upload_csv_file');
                    $.ajax({
                        url: "add_sku.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == 'success') {
                                $("#res_message div.alert").removeClass('alert-danger').addClass('alert-success');
                                $("#res_message div.alert").html(response.message);
                                $("#res_message").show();
                                grid.getDataTable().ajax.reload();
                            } else {
                                $("#res_message div.alert").removeClass('alert-success').addClass('alert-danger');
                                $("#res_message div.alert").html(response.message);
                                $("#res_message").show();
                                grid.getDataTable().ajax.reload();
                            }
                        }
                    });
                    return false;
                });
                
                $(document).on('click', '#download_csv', function () {
                     //var groupId = 'download_template';
                     //$("#option_value").val(groupId);
                     $("#hiddenForm").submit();
                });
                
                $('#btn_Save').click(function () { 
                    
                    var form = $('#AddSKUForm')[0]; // You need to use standard javascript object here
                    var formData = new FormData(form);
                    
                    $.ajax({
                    method: "POST",
                    url: "add_sku.php",
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData:false,
                    success:function(data)
                    {
                        var rs = data;
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
                            $("#res_message div.alert").removeClass('alert-success').addClass('alert-danger');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                        }    
                    }  
                });
               });  
               
                $(document).on('click', '.btnedit', function () {
                    //grid.getDataTable().ajax.reload();
                    $("#res_message").hide();
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
                        $('#sku_name').val(t.declared_name);
                        $('#sku_description').val(t.description);
                        $('#sku_price').val(t.price);
                        $('#sku_currency').val(t.currency);
                        $('#sku_length').val(t.length);
                        $('#sku_width').val(t.width);
                        $('#sku_height').val(t.height);
                        $('#sku_net_weight').val(t.net_weight);
                        $('#sku_hscode').val(t.hscode);
                        $('#sku_notes').val(t.notes);
                        $(".fileinput-preview img").attr('src', t.image);
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
                        <div class="caption"><i class="fa fa-plus"></i>
                                Add SKU
                        </div>
                        <div class="actions">
                            <a href="add_sku_order.php" class="btn blue"><span></span><i class="fa fa-plus"></i>&nbsp;Add SKU Order</a>
                            <a id="btnSubmitImport" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i>&nbsp;<?php echo "Import CSV"; ?></a>
                       </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row display-none" id="res_message">
                                <div class="col-md-12">
                                    <div class="alert alert-success"></div>
                                </div>
                        </div>
                            
                        <form name="AddSKUForm" id="AddSKUForm" action="add_SKU" method="POST" enctype="multipart/form-data">                            
                            <div class="row">
                            <div class="col-md-6">
                                <h4 class="modal-title text-primary"><strong>Add SKU: <span id="AddSKU"></span> </strong> </h4>                                
                            </div>                            
                        </div>
                   
                        <input type="hidden" name="form_action" id="form_action" value="add_SKU" />
                        <div class="col-md-12" id="errorMessage">
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">SKU</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku" name="sku" type="text" placeholder="SKU" value="" rel="tooltip" data-original-title="SKU">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="label-account">Name</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_name" name="sku_name" type="text" placeholder="Name" value="" rel="tooltip" data-original-title="Name">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="label-account">Price</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_price" name="sku_price" type="text" placeholder="SKU Price" value="" rel="tooltip" data-original-title="SKU Price">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Currency</label>
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                        <?php
                                        // Currency array
                                        $arrayCurrency = array('GBP' => 'GBP','USD' => 'USD','CAN' => 'CAN','DFL' => 'DFL','DKR' => 'DKR','EUR' => 'EUR','FFR' => 'FFR','HKG' => 'HKG','INR' => 'INR','JPY' => 'JPY','NKR' => 'NKR','NLG' => 'NLG','SGD' => 'SGD','SFR' => 'SFR','SKR' => 'SKR','YEN' => 'YEN','PLN' => 'PLN');
                                        echo Ddl::generateArrayDDL('sku_currency', $arrayCurrency, $sku_currency, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Currency" placeholder="Currency"');?>
                                       
                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Net Weight</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_net_weight" name="sku_net_weight" type="text" placeholder="SKU Net Weight" value="" rel="tooltip" data-original-title="SKU Net Weight">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="label-account">Length</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_length" name="sku_length" type="text" placeholder="Length" value="" rel="tooltip" data-original-title="Length">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Height</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_height" name="sku_height" type="text" placeholder="Height" value="" rel="tooltip" data-original-title="Height">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Width</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_width" name="sku_width" type="text" placeholder="WIDTH" value="" rel="tooltip" data-original-title="Width">
                                    </div>
                                </div>
                            </div>
                                                       
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Description</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_description" name="sku_description" type="text" placeholder="Description" value="" rel="tooltip" data-original-title="Description">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">HS Code</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_hscode" name="sku_hscode" type="text" placeholder="HS Code" value="" rel="tooltip" data-original-title="HS Code">
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <label class="label-account">Notes</label>
                                <div class="form-group">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                        <input class="form-control form-filter" id="sku_notes" name="sku_notes" type="text" placeholder="Notes" value="" rel="tooltip" data-original-title="Notes">
                                    </div>
                                </div>
                            </div>
                            
                            
                                  
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Active</label><br />
                                    <input id="active_flag" <?php echo ($active_flag = '1' ? 'checked="checked"' : ''); ?> name="active_flag" type="checkbox" class="make-switch"  data-on-text="Yes"  data-off-text="No"  data-on-color="primary" data-off-color="danger">
                                </div>
                            </div>
                        </div>
                        <div style="clear:both"></div> 
                        <br />
                        
                        <div class="row">
                        <div class="col-sm-6">
                            <label for="sku_image"> Upload SKU image</label>
                            <br clear="all">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                    <?php
                                    if (trim($sku_image) != '' && file_exists('../_assets/images/sku_images/' . $sku_image)) {
                                        echo '<img id="sku_img" src="../_assets/images/sku_images/' . $sku_image . '" title="" >';
                                    } else {
                                        echo '<img id="sku_img" src = "../images/No-image-found.jpg">';
                                    }
                                    ?>  

                                </div>
                                <div> <span class="btn default btn-file"> <span class="fileinput-new"> Select image </span> <span class="fileinput-exists"> Change </span>
                                        <input id="sku_image"  type="file" name="sku_image" >
                                    </span> <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a> </div>
                            </div>
                            <div class="clearfix margin-top-10"> <span class="label label-primary"><small>NOTE!</span> Recommended image dimensions (225 x 225) </small></div>
                            <div class="form-group">
                                <div class="input-group"> </div>
                            </div>
                        </div>
                    </div>
                        <div class="row " style="text-align:centre;" align="right">
                            <div class="col-md-12">
                                <input id="btn_Save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("Save"); ?>"/>
                                <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                            </div>
                        </div>
                        
                        
                </form>
                    </div>
                </div>
            
             <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="option_value" value="" id="option_value"/>
                <input type="hidden" name="action" value="download_csv_frm" />
            </form>
        <!--Model for CSV Upload-->
        <div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Import SKUs</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <input class="form-control" id="file_in" name="file_in" type="file" value="" />                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv" class="btn green">Upload</button>
                        <button type="button" id="download_csv" class="btn green">  <i class="fa fa-download"></i> Download Template </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>
                            SKU List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th><?php echo Translation::GetCaption("ACTION"); ?></th>
                                    <th>SKU</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>  
                                    <th>Image</th>
                                    <th>Active</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td width = "8%">
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td class="user_acccount_correct_button"  width = "15%">
                                       <?php                                       
                                       echo Ddl::generateDDL('search_sku', "skuFilter", ' active = "1"', "sku", "id", $search_sku, ' class="bs-select input-sm form-control form-filter" data-container="body" data-live-search="true"  data-show-subtext="true"','Select SKU','','search_sku'); 
                                       ?>
                                    </td>
                                    <td width = "25%">
                                        <div>
                                            <input type="text" class="form-control form-filter" name="name" />
                                        </div>
                                    </td>
                                    <td width = "25%">
                                        <div>
                                            <input type="text" class="form-control form-filter" name="description" />
                                        </div>
                                    </td>
                                    <td width = "10%">
                                        <div>
                                            <input type="text" class="form-control form-filter" name="price" />
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div></div>
                                    </td> 
                                    
                                    <td class="user_acccount_correct_button" width = "6%">
                                        <?php
                                        $arrayTypeValues = array('0' => 'No', '1' => 'Yes');
                                        echo Ddl::generateArrayDDL('search_Active', $arrayTypeValues, "", "Status", ' class="form-control form-filter select2"', "", 'search_Active', 'Select Status', '');
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
         //echo 'sadasd'; die;
        try 
            {
                $sku = new Sku($skuid);
                $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
                $request = new stdClass();
                $request->SKU = $sku->getSku();
                $request->CustomerID = 'BRANDS';//$sku->getCustomerId();
                $request->Active = $sku->getActive();
                $request->Description = $sku->getDescription();
                $request->Name = $sku->getDeclaredName();
                $request->NetWeight = $sku->getNetWeight();
                $request->Price = $sku->getPrice();
                $request->Length = $sku->getLength();
                $request->Width = $sku->getWidth();
                $request->Height = $sku->getHeight();              
                $request->HSCode = $sku->getHSCode();
                $request->Image = $sku->getImage();
                $response = $client->CreateSku( array("request" => $request));
                //echo $response->CreateSkuResult->ErrorMsg.'ssada'; die;
                if($response->CreateSkuResult->ErrorMsg == '') 
                {
                    $sku->setSendWms('1');
                    $sku->save();
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
