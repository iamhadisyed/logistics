<?php
// get settings
require_once("../includes/settings/config.inc.php");
/* * *
 * Page for editing a user
 */
 include_classes([   
                    'carrier.class',
                    'carrierfilter.class',
                    'carrierlog.class',
                    'carrierlogfilter.class',
                    'currency.class',
                    'currencyfilter.class',
                    'country.class',
                    'countryfilter.class',
                    'documenttype.class',
                    'documenttypefilter.class',
                    'remoteareasgroups.class',
                    'remoteareasgroupsfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'servicecountrytimefilter.class',
                    'servicecountrytime.class',
                    'remoteareachargesservices.class',
                    'remoteareachargesservicesfilter.class',
                    'remoteareachargescarrierfilter.class',
                    'remoteareachargescarrier.class',
                    'carrierdocument.class',
                    'carrierdocumentfilter.class',
                ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    protected function init() {
        if (!Permissions::checkFilePermission('carrier.php'))
            util_redirect("index.php");
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'carrier.php' => Translation::GetCaption("CARRIERS"),
            Translation::GetCaption("LIST")
        );
        $user = SessionManager::getUser();
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_WAREHOUSE_LIST);
        t_on(); // turn on trace for this page
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_carriers_excel') {

            if (PHP_SAPI == 'cli')
                die('This should only be run from a Web Browser');

            /** Include PHPExcel */
            require_once '../includes/library/PHPExcel-1.8/PHPExcel.php';
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            // Set document properties
            $objPHPExcel->getProperties()->setCreator(formatMessages(CREATOR_OF_DOCUMENT, FALSE))
                    ->setTitle("Carriers")
                    ->setSubject("Smart Track System Carriers")
                    ->setDescription("Smart Track System Carriers")
                    ->setKeywords("Shipping,Carriers")
                    ->setCategory("Shipping");

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');

            $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
            $activeSheet->mergeCells('A1:G1');
            $activeSheet->setCellValue('A1', "Smart Track Carriers");
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('size' => 16, 'bold' => true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);

            $activeSheet->setCellValue('A2', "Logo");
            $activeSheet->setCellValue('B2', "Name");
            $activeSheet->setCellValue('C2', "Display Name");
            $activeSheet->setCellValue('D2', "Country");
            $activeSheet->setCellValue('E2', "Cut Off Time");
            $activeSheet->setCellValue('F2', "Currency");
            $activeSheet->setCellValue('G2', "Active");

            $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray(array('font' => array('bold' => true), 'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);

            $carrierFilterObj = new CarrierFilter();
            $carrierFilterObj->addFilter("cl.status != '2'");
            $carriersData = $carrierFilterObj->getCarrierList("cl.logo,cl.carrier,cl.carrier_display_name,cl.currency_code,cl.cut_off_time,cl.status,cn.name AS country_id");
            if (count($carriersData) > 0) {
                $rowNum = 3;
                foreach ($carriersData as $carrierObj) {


                    $gdImage = imagecreatefrompng('../images/carrierlogo/thumbnail/owe_50_' . $carrierObj->getLogo());
                    // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setName('Sample image');
                    $objDrawing->setDescription('Sample image');
                    $objDrawing->setImageResource($gdImage);
                    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight(50);
                    $objDrawing->setCoordinates('A' . $rowNum);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                    $activeSheet->setCellValue('B' . $rowNum, $carrierObj->getCarrier());
                    $activeSheet->setCellValue('C' . $rowNum, $carrierObj->getCarrierDisplayName());
                    $activeSheet->setCellValue('D' . $rowNum, $carrierObj->getCountryId());
                    $activeSheet->setCellValue('E' . $rowNum, $carrierObj->getCutOffTime());
                    $activeSheet->setCellValue('F' . $rowNum, $carrierObj->getCurrencyCode());
                    $activeSheet->setCellValue('G' . $rowNum, ($carrierObj->getStatus() == 1 ? 'Yes' : 'No'));

                    $activeSheet->getRowDimension($rowNum)->setRowHeight(50);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $rowNum . ':G' . $rowNum)->applyFromArray(array('alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
                    $rowNum++;
                }
            }
            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Smart Track Carriers');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            foreach (range('B', 'G') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            $fileName = "carriers_" . time() . ".xlsx";

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'CHECK_GAZ_FILES') {
            $output = [];
            $carrierName    =   ucfirst(str_replace(" ", "_",trim($this->form_vars['carrier_name'])));
            
            $classFilename = strtolower($carrierName).'.class';
            
            include_classes([$classFilename],'labels');
             
            $carrierClass   =   new $carrierName();
            $outputResponse  =    $carrierClass->carrierCheckGazFiles();
            if(trim($outputResponse["status"]) == "SUCCESS")
            {
                $output["status"] = "SUCCESS";
                $output["FILES"] = $outputResponse['FILES'];
            }
            else
            {
                $output["status"] = "ERROR";
                $output["message"] = "Below files not found for GAZ, Please upload and try again.\r\n".implode(",\r\n",$outputResponse["FILES_NOT_FOUND"] ) ;
            }
            echo json_encode($output);
            die;
        }elseif (isset($this->form_vars['action']) && $this->form_vars['action'] == 'PROCESSING_GAZ_FILE') {
            
                ini_set('session.gc_maxlifetime', '-1');
                ini_set('MAX_EXECUTION_TIME', '-1');
                $output = [];
                $carrierName    =   ucfirst(str_replace(" ", "_",trim($this->form_vars['carrier_name'])));
                $classFilename = strtolower($carrierName).'.class';
                include_classes([$classFilename],'labels');
                
                $fileName       =   $this->form_vars['file_name'];
                
                $carrierClass   =   new $carrierName();
                $output  =    $carrierClass->carrierGazFilesProcess($fileName);

                echo json_encode($output);
                die;
        } elseif (isset($this->form_vars['action']) && $this->form_vars['action'] == 'GET_CARRIER_DOCUMENTS') {
             $carrierId = $this->form_vars["carrier_id"];
             $outputHtml = '';
             if ($carrierId > 0) {

                 $carrierDocumentFilter = new carrierDocumentFilter();
                 $carrierDocumentLists = $carrierDocumentFilter->getCarrierDoc($carrierId);
                 foreach ($carrierDocumentLists as $carrierDocumentList) {

                     $temp = explode(".", $carrierDocumentList->getDocumentName());
                     $extension = end($temp);
                     $fileFullPath = "../_assets/carrier_documents/" . $carrierId . "/" . $carrierDocumentList->getDocumentName();
                     if (!file_exists($fileFullPath) || $extension == "pdf") {
                         $fileFullPath = "../images/No-image-found.jpg";
                     }
                     if ($extension == "pdf") {
                         $fileFullPath = "../images/pdf.png";
                     }

                     $outputHtml .= '<div class="col-md-3" id="ser_doc_' . $carrierDocumentList->getId() . '">';
                     $outputHtml .= '    <div class="thumbnail">';
                     $outputHtml .= '        <div class="document-thumb">';
                     $outputHtml .= '            <img src="' . $fileFullPath . '" alt="' . $carrierDocumentList->getDocumentName() . '" data-src="' . $fileFullPath . '">';
                     $outputHtml .= '        </div>';
                     $outputHtml .= '        <div class="caption">';
                     $outputHtml .= '            <h3>' . $carrierDocumentList->getDocumentId() . '</h3>';
                     $outputHtml .= '            <a target="_blank" href="../_assets/carrier_documents/' . $carrierId . '/' . $carrierDocumentList->getDocumentName() . '" class="btn blue"> View </a>';
                     $outputHtml .= '            <a  class="btn red remove_doc" data-doc_id="' . $carrierDocumentList->getId() . '"> Remove </a>';
                     $outputHtml .= '            </p>';
                     $outputHtml .= '        </div>';
                     $outputHtml .= '    </div>';
                     $outputHtml .= '</div>';
                 }
             } else {

                 $outputHtml .= '<div class="col-md-3">';
                 $outputHtml .= '<div class="alert alert-danger">There is no document uploaded yet. </div>';
                 $outputHtml .= '</div>';
             }
             echo $outputHtml;
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'upload_carrier_doc') {
            $html = "";
            $carrierId = $this->form_vars["carrier_id"];
            $documentId = $this->form_vars["file_type"];
            $fileTypeText = $this->form_vars["file_type_text"];

            $addedBy = $user->getId();
            $path = "../_assets/carrier_documents/" . $carrierId . "/";
            if (!file_exists($path))
                @mkdir($path, 0775);
            if (isset($_FILES["carrier_doc_file"]) && trim($_FILES["carrier_doc_file"]["name"]) != '') {
                $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf");
                $temp = explode(".", $_FILES["carrier_doc_file"]["name"]);
                $extension = end($temp);
                if ((($_FILES["carrier_doc_file"]["type"] == "image/gif") || ($_FILES["carrier_doc_file"]["type"] == "image/jpeg") || ($_FILES["carrier_doc_file"]["type"] == "image/jpg") || ($_FILES["carrier_doc_file"]["type"] == "image/pjpeg") || ($_FILES["carrier_doc_file"]["type"] == "image/x-png") || ($_FILES["carrier_doc_file"]["type"] == "image/png" ) || ($_FILES["carrier_doc_file"]["type"] == "application/pdf" ) ) && in_array($extension, $allowedExts)) {
                    if ($_FILES["carrier_doc_file"]["error"] > 0) {
                        $return_msg = "Return Code: " . $_FILES["carrier_doc_file"]["error"] . "<br>";
                    } else {
                        $uploadUserDoc = str_replace(' ', '_', time() . $_FILES["carrier_doc_file"]["name"]);
                        move_uploaded_file($_FILES["carrier_doc_file"]["tmp_name"], $path . $uploadUserDoc);
                        $fileFullPath = $path . $uploadUserDoc;
                        if ($extension == "pdf") {
                            $fileFullPath = "../images/pdf.png";
                        }
                        //Save User document Data
                        $carrierDocument = new carrierDocument();
                        $carrierDocument->setCarrierId($carrierId);
                        $carrierDocument->setDocumentId($documentId);
                        $carrierDocument->setDocumentName($uploadUserDoc);
                        $carrierDocument->setAddedBy($addedBy);
                        $carrierDocument->setAddedDate(date('d-m-Y'));
                        $carrierDocument->save();

                        $html .= '<div class="col-md-3" id="ser_doc_' . $carrierDocument->getId() . '">';
                        $html .= '<div class="thumbnail">';
                        $html .= '<img src="' . $fileFullPath . '" alt="100%x200" style="max-width: 100%; max-height: 200px; display: block;" data-src="' . $path . $uploadUserDoc . '">';
                        $html .= '<div class="caption">';
                        $html .= '<h3>' . $fileTypeText . '</h3>';
                        $html .= '<a target="_blank" href="' . $path . $uploadUserDoc . '" class="btn blue"> View </a>&nbsp&nbsp';
                        $html .= '<a  class="btn red remove_doc" data-doc_id="' . $carrierDocument->getId() . '"> Remove </a>';
                        $html .= '</p>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        echo $html;
                    }
                } else {
                    echo $return_msg = "0";
                }
            }

            die;
        }
//          Handle remove User Document
        else if(isset($_GET['func']) && $_GET['func'] == 'UPLOAD_GAZZETIER_FILES'){
            
            $response    =   [];
            $folderName =   str_replace(" ","",$this->form_vars['gazetier_name']);
            $foldercarrierName =   strtolower(str_replace(" ","_",$this->form_vars['carrier_name_for_gaz']));
            if(trim($folderName)== '')
                $folderName =   date("Y_m_d");
            $fileToUpload   =  $_FILES;

            $filenameUploded    =   [];
            $fileError          =   [];
            $path   =    SETTING_DIR_REMOTE.'gazzetier/'.$foldercarrierName."/".$folderName;
            if (!file_exists($path   )) {
                mkdir($path   , 0777, true);
            }
            //echo "<pre>";
//print_r($_FILES['files']);
            if(count($_FILES['files']['name'])>0) {
                foreach ($_FILES['files']['name'] as $fileKey => $filename) {
                    $uploadfile = SETTING_DIR_REMOTE . "gazzetier/" .$foldercarrierName."/". $folderName . "/" . basename($_FILES['files']['name'][$fileKey]);
                    $uploadfileUrl = SETTING_URL . "gazzetier/" .$foldercarrierName."/". $folderName . "/" . basename($_FILES['files']['name'][$fileKey]);
                    $uploadedFileName = basename($_FILES['files']['name'][$fileKey]);
                    $uploadedFileSize = $_FILES['files']['size'][$fileKey];
                    $uploadedFileType = $_FILES['files']['type'][$fileKey];
                    if (move_uploaded_file($_FILES['files']['tmp_name'][$fileKey], $uploadfile)) {
                        $filenameUploded[] = $uploadfile;
                    } else {
                        $fileError [] = "Possible file upload attack!\n";
                    }
                }
                $response['files'] = [[
                    'url' => $uploadfileUrl,
                    'name' => $uploadedFileName,// $file->getBaseName(),
                    'type' => $uploadedFileType,// $file->getType(),
                    'size' => $uploadedFileSize, //$file->getSize(),
                    'delete_url' => $uploadfileUrl, //"http://url.to/delete /file/", // url ?
                    'delete_type' => "DELETE"
                ]];
            }
            echo json_encode($response);
            die;
        }
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'remove_carrier_doc') {
            $docId = $this->form_vars['doc_id'];
            $carrierId = $this->form_vars['carrier_id'];
            if ($carrierId > 0) {
                $carrierDocument = new carrierDocument($docId);
                @unlink("../_assets/carrier_documents/" . $carrierId . "/" . $carrierDocument->getDocumentName());
                $carrierDocument->deleteById($docId);
            }
            echo "1";
            die;
        }
        else if (trim($this->form_vars["func"]) == 'GET_SERVICES_COUNTRY') {
            $serviceId = (int) $this->form_vars["serviceid"];
            $servicename = $this->form_vars["servicename"];
            $selectCountryClass = new ServiceCountryTimeFilter();
            $selectCountryClass->addFilter(" id_service ='" . DbAccess3::escape($serviceId) . "'");
            $selectCountryList = $selectCountryClass->getCountryList("c.id, c.iso as iso, c.name as country_name");
            if (count($selectCountryList) > 0) {
                foreach ($selectCountryList as $countryData) {
                    echo '<div class="col-sm-6"><img src="../assets/global/img/flags/' . strtolower($countryData->getIso()) . '.png"> ' . $countryData->getCountryName() . '</div>';
                }
            } else {
                echo '<div class="col-sm-12"> No country available for ' . $servicename . '.</div>';
            }
            echo '<div style="clear:both;"></div>';
            die;
        }
        else if (trim($this->form_vars["func"]) == 'CHANGE_STATUS_CARRIER') {

            $output = array();
            $output["STATUS"] = 'SUCCESS';
            $output["MESSAGE"] = '';
            $courier = (int) $this->form_vars["carrier_id"];
            $carrier_name = strip_tags($this->form_vars["carrier_name"]);
            $status = (trim($this->form_vars["status"]));
            if ($status == 'active')
                $status = 1;
            else if ($status == 'inactive')
                $status = 0;
            else if ($status == 'delete')
                $status = 2;

            if (trim($status) != '') {
                $carrierClass = new Carrier($courier);
                $carrierObjectOld = new Carrier($courier);
                $oldCarrierData = serialize($carrierObjectOld);

                $carrierClass->setStatus($status);
                $carrierClass->save();
                $newCarrierData = serialize($carrierClass);
                $carrierLog = new CarrierLog();
                if ($carrierObjectOld != $newCarrierData)
                    $carrierLog->createlog($user->getId(), '', $courier, 'CARRIER', $user->getUserName() . ' has ' . trim($this->form_vars["status"]) . ' carrier ' . $carrier_name, $carrierObjectOld, $newCarrierData);

                if (trim($status) == '0' && $carrierClass->getId() > 0) {
                    $servicesFilter = new ServiceFilter();
                    $servicesFilter->addFilter("active <> '" . $status . "'");
                    $servicesFilter->addFilter("carrier_id = '" . $courier . "'");
                    $serviceList = $servicesFilter->getList();
                    if (count($serviceList) > 0) {
                        foreach ($serviceList as $serviceData) {
                            $oldServiceData = serialize($serviceData);
                            $serviceData->setActive($status);
                            $serviceData->save();
                            $newServiceData = serialize($serviceData);
                            $serviceLog = new ServiceLog();
                            if ($oldServiceData != $newServiceData)
                                $serviceLog->createlog($user->getId(), '', $serviceData->getId(), 'SERVICES', $user->getUserName() . ' has updated carrier, it effect services  ' . $serviceData->getName(), $oldServiceData, $newServiceData);
                        }
                    }
                }
                $output["STATUS"] = 'SUCCESS';
                $output["MESSAGE"] = formatMessages(SUCCESS_CHANGE_CARRIER_STATUS);
            }
            else {
                $output["STATUS"] = 'ERROR';
                $output["MESSAGE"] = formatMessages(SUCCESS_CHANGE_CARRIER_STATUS); //'';
            }
            echo json_encode($output);
            die;
        }
        else if (trim($this->form_vars["func"]) == "GET_ALL_SERVICES") {
            $htmlReturn = "";
            $courier = (int) $this->form_vars["courier"];
            $carrier_name = strip_tags($this->form_vars["carrier_name"]);
            $cut_of_time = strip_tags($this->form_vars["cut_of_time"]);
            $carrier_display_name = $this->form_vars["carrier_display_name"];
            $carrier_status = $this->form_vars["carrier_status"];
            if(preg_match('/^[a-zA-Z1-9 \d]+$/', $this->form_vars["carrier_country"])) {
                $carrier_country = $this->form_vars["carrier_country"];
            } else {
                $carrier_country = '';
            }
            $carrier_country_iso = $this->form_vars["carrier_country_iso"];
            $courierimage = $this->form_vars["courierimage"];
            $carrier_remotearea_check = $this->form_vars["carrier_remotearea_check"];
            $serviceTable = '';
            $serviceTable .= '<div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><img class="margin-right-5" src="../images/carrierlogo/thumbnail/owe_50_' . DbAccess3::escape($courierimage) . '" alt="' . DbAccess3::escape($carrier_name) . '" uib-popover="' . DbAccess3::escape($carrier_name) . '" popover-trigger="mouseenter">' . DbAccess3::escape($carrier_name) . ' Services</h4>
                    </div>
                    <!-- /.modal-content --> 
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div  id="console_window_service" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px; margin-bottom:15px;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <strong>Origin:</strong> ' . (trim($carrier_country) == '' ? '-' : ' <img src="../assets/global/img/flags/' . trim(strtolower(DbAccess3::escape($carrier_country_iso))) . '.png">  ' . trim($carrier_country) ) . '
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Active:</strong> ' . (trim($carrier_status) == '1' ? '<span class="label label-sm label-success"> Yes </span>' : '<span class="label label-sm label-danger"> No </span>') . '
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Cut Off:</strong> ' . (trim($cut_of_time) == '' ? '-' : trim(strip_tags($cut_of_time))) . '</div>
                                </div>
                            </div>';
            if ($carrier_remotearea_check == 's') {
                $serviceTable .= '<div class="col-md-4 text-right" id="service_model_csv">
                                <a href="#" id="export_remotearea_service" data-export_carrier_id="' . $courier . '" class="btn blue btn-sm pull-right margin-left-5"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</a>
                                <a  data-import_carrier_id="' . $courier . '" id="btnSubmitImport_service" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i>&nbsp;Import</a>
                                <div id="hidden_frm" style="display: none;">
                                    <form name="hidden_form_service" id="hidden_form_service" action="" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="carrier_id_hidden_service" value="" id="carrier_id_hidden_service"/>
                                        <input type="hidden" name="action" value="download_service" />
                                        <input type="file" name="import_csv_service" id="import_csv_service" />
                                    </form>
                                </div>
                            </div>';
            }
            $serviceTable .= '</div>
                        <div class="row">
                            <hr class="" />
                        </div>
                        <div class="row" id="message_download_csv_service" style="display: none;">
                            <div class="col-md-12">
                                    <div class="alert alert-danger"></div>
                            </div>
                        </div>';
            $carrierObj = new Carrier($courier);
            if ($courier > 0) {
                $serviceFilter = new ServiceFilter();
                $serviceFilter->addFieldFilter('carrier_id', $courier);
                $serviceFilter->addFieldFilter('deletedq', '0');
                $serviceFilter->addFilter(" code <> '' ");
                $serviceData = $serviceFilter->getColumnList('id,uploaded_currency, carrier_id, name, code, origin_country,type, wieght_type,is_remotearea, active ');
                if (count($serviceData) > 0) {
                    $serviceTable .= '<div class="row"><div class="col-md-12"><table class="table table-striped table-bordered table-advance table-hover">';
                    $serviceTable .= '<thead><tr>' .
                            '<th>Action</th>' .
                            '<th>Service</th>' .
                            '<th>Code</th>' .
                            '<th>Countries</th>' .
                            '<th>Package / Service Type</th>';
                    if ($carrierObj->getRemoteareaCheck() == "s") {
                        $serviceTable .= '<th>Remotearea</th>';
                    }
                    $serviceTable .= '<th>Status</th>' .
                        '</tr></thead><tbody>';
                    foreach ($serviceData as $servicesItem) {
                        $serviceId = $servicesItem->getId();
                        $carrierId = $servicesItem->getCarrierId();
                        $name = $servicesItem->getName();
                        $code = $servicesItem->getCode();
                        $serviceCountry = str_replace(',', ', ', $servicesItem->getServiceCountry());
                        $originCountry = $servicesItem->getOriginCountry();
                        $uploadedCurrency = $servicesItem->getUploadedCurrency();
                        $TypeRegion = $servicesItem->getType();
                        $weightType = $servicesItem->getWieghtType();
                        $isCustomized = $servicesItem->getIsCustomized();
                        $isRemotearea = $servicesItem->getIsRemotearea();
                        $checkZoneBase = $carrierObj->getZoneBase();
                        $IsserviceActive    = $servicesItem->getActive();
                        $serviceTable .= '<tr>' .
                                        '<td>' .'<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >
                                                <li>
                                                    <a href="services_detail.php?id=' . $serviceId . '" title="Edit">
                                                        <span class="fa fa-pencil"></span> Edit 
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="service_country_time.php?service_id=' . $serviceId . '&carrier_id=' . $carrierId . '" title="Transit Time">
                                                        <span class="fa fa-calendar-o"></span> Transit Time
                                                    </a>
                                                </li>';
                        if ($checkZoneBase == 0) {
                            $serviceTable .= '<li><a href="tariffs_list.php?carrier_id=' . $carrierId . '&service_id=' . $serviceId . '" title="Tariff list">
                                            <span class="fa fa-money"></span> Tariff list
                                        </a></li>
                                        <li><a href="carrier_zones.php?carrier_id=' . $carrierId . '&service_id=' . $serviceId . '" title="Add Carrier Zone">
                                            <span class="fa fa-dropbox"></span> Add Carrier Zone
                                        </a></li>';
                        }
                        if ($carrierObj->getRemoteareaCheck() == "s") {
                            $serviceTable .= '<li>
                                                        <a data-carrier_logo="../images/carrierlogo/thumbnail/owe_50_' . DbAccess3::escape($courierimage) . '" data-carrier_name="' . $carrier_name . '" data-service_name="' . $name . '" data-carrier-id="' . $carrierId . '" data-service-id="' . $serviceId . '" ' . (($isRemotearea == 'Y') ? '' : 'style="display:none;"') . ' class="remotearea_services_charges" title="Remotearea">
                                                            <span class="glyphicon glyphicon-screenshot"></span> Remotearea
                                                        </a>
                                                <li>';
                        }
                        $serviceTable .=  /*(
                            (Permissions::checkFilePermission('client_list.php') ) ?
                                '<li>
                                                    <a href="client_list.php?uaccount=" title="Manage Shipments">
                                                        <i class="fa fa-shopping-cart"></i> Manage Shipments
                                                    </a>
                                                </li>' : '') .*/
                            '</ul>
                                        </div>'. '</td>' .
                                '<td>' . $name . '</td>' .
                                '<td>' . $code . '</td>' .
                                '<td>
                                        <a class="btn btn-xs btn-default blue btn-outline pull-left margin-right-5 event-servicecountry" rel="tooltip" title="Services Country"
                                                data-target="#service-country-popup" role="dialog" tabindex="-1" 
                                                data-servicename="' . $name . '" data-action="GET_SERVICES_COUNTRY"
                                                data-serviceid="' . $serviceId . '" data-carrierload="carrier.php" >
                                                Show Countries
                                            </a>' .
                                '<td>'
                                . (($weightType == 1) ? 'Parcel' : 'Shipment') .
                                '<br>'
                                . (($isCustomized == 1) ? 'Product' : 'Service') .
                                
                                '</td>';
                        if ($carrierObj->getRemoteareaCheck() == "s") {
                            $serviceTable .= '<td>
                                                                <div class="form-group">
                                                                            <div class="md-radio-inline">
                                                                                <input data-current-service="' . $serviceId . '" name="is_remotearea[' . $serviceId . ']" data-servicename="' . $name . '" type="checkbox" class="make-switch is_remotearea"  data-on-text="YES"  ' . (($isRemotearea == 'Y') ? 'checked=checked' : '') . '  data-off-text="NO"  data-on-color="primary" data-off-color="danger" data-size="mini">
                                                                            </div>
                                                                </div>
                                                            </td>';
                        }
                        $serviceTable .= '<td>'. ($IsserviceActive == 1 ? '<center><span class="label label-sm label-success">Active</span></center>' : '<center><span class="label label-sm label-danger">Inactive</span></center>') . '</td>' .
                                '</tr>';
                    }
                    $serviceTable .= '</tbody></table></div></div>';
                } else {
                    $serviceTable .= '<div class="alert alert-danger text-center">We are unable to find service(s) for carrier ' . $carrier_name . '.</div>';
                }
            } else {
                $serviceTable .= '<div class="alert alert-danger text-center">You have provided invalid carrier. Please try again.</div>';
            }
            echo $serviceTable . '<div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div><script>$(".is_remotearea").bootstrapSwitch();
                   $(".is_remotearea").on("switchChange.bootstrapSwitch", function (event, state) {
                    var serviceId = $(this).attr("data-current-service");
                    var serviceName = $(this).attr("data-servicename");
                    serviceRemotearea(state,serviceId,serviceName);
                    });</script>';
            die;
        }
        if (isset($this->form_vars['carrier_id'])) {
            $carrier_id = $this->form_vars['carrier_id'];
        } else if (isset($this->form_vars['id'])) {
            $carrier_id = $this->form_vars['id'];
        }
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            $user = SessionManager::getUser();
//          take appropriate action
            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - save new address detailsvalidate new address details - if OK, save and return to booking list
                case "save":

                    $carrierId = $this->form_vars['id'];
                    $user = SessionManager::getUser();
                    if (isset($this->form_vars['remotearea'])) {
                        //Delete all carrier old entries
                        $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                        $remoteareasGroupsFilter->addFilter("AND carrier_id = " . $carrierId);
                        $remoteareasGroupsIds = $remoteareasGroupsFilter->getColumnList("id");
                        if (count($remoteareasGroupsIds) > 0) {
                            foreach ($remoteareasGroupsIds as $remoteareasGroupsId) {
                                $groupsId = $remoteareasGroupsId->getId();
                                RemoteareaChargesCarrier::deleteByGroupId($groupsId);
                            }
                        }
                        //Delete if its changed from service to carrier wise.
                        $serviceFilter = new ServiceFilter();
                        $serviceFilter->addFilter("    carrier_id=" . $carrierId);
                        $serviceIdsObj = $serviceFilter->getColumnList("id");
                        if (count($serviceIdsObj) > 0) {
                            foreach ($serviceIdsObj as $serviceIds) {
                                RemoteareaChargesServices::deleteByServiceId($serviceIds->getId());
                            }
                        }
                        parse_str($this->form_vars['group_serialize'], $groupSerialize);
                        parse_str($this->form_vars['charges_serialize'], $chargesSerialize);
                        foreach ($groupSerialize['group'] as $key => $value) {
                            $remoteareaChargesCarrier = new RemoteareaChargesCarrier();
                            $remoteareaChargesCarrier->setRemoteareaGroupId($value);
                            $remoteareaChargesCarrier->setRemoteareaCharges($chargesSerialize['charges'][$key]);
                            $remoteareaChargesCarrier->setAddedBy($user->getId());
                            $remoteareaChargesCarrier->setAddedDate(time());
                            $remoteareaChargesCarrier->setIsDeleted('N');
                            $remoteareaChargesCarrier->save();
                        }
                    } else {
//                            Copy all carrier remotearea charges valus
                        if ($this->form_vars['copy_carrier'] == "copy") {
                            $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                            $remoteareasGroupsFilter->addFilter("AND carrier_id = " . $carrierId);
                            $remoteareasGroupsIds = $remoteareasGroupsFilter->getColumnList("id");
                            if (count($remoteareasGroupsIds) > 0) {
                                foreach ($remoteareasGroupsIds as $remoteareasGroupsId) {
                                    $groupsId = $remoteareasGroupsId->getId();
                                    $remoteareaChargesCarrierFilter = new RemoteareaChargesCarrierFilter();
                                    $remoteareaChargesCarrierFilter->addFilter("    remotearea_group_id = " . $groupsId);
                                    $remoteareaChargesCarrierListCur = $remoteareaChargesCarrierFilter->getList();
                                    if (count($remoteareaChargesCarrierListCur) > 0)
                                        foreach ($remoteareaChargesCarrierListCur as $remoteareaChargesCarrierNew) {
                                            $remoteareaCharges = $remoteareaChargesCarrierNew->getRemoteareaCharges();
                                            $serviceFilter = new ServiceFilter();
                                            $serviceFilter->addFilter("    carrier_id=" . $carrierId);
                                            $serviceListObjs = $serviceFilter->getColumnList("id,from_weight,to_weight");
                                            foreach ($serviceListObjs as $serviceListObj) {
                                                $serviceId = $serviceListObj->getId();
                                                $fromWeight = $serviceListObj->getFromWeight();
                                                $toWeight = $serviceListObj->getToWeight();
                                                $services = new Services($serviceId);
                                                $services->setIsRemotearea("Y");
                                                $services->save();
                                                $remoteareaChargesService = new RemoteareaChargesServices();
                                                $remoteareaChargesService->setRemoteareaGroupId($groupsId);
                                                $remoteareaChargesService->setServiceId($serviceId);
                                                $remoteareaChargesService->setRemoteareaCharges($remoteareaCharges);
                                                $remoteareaChargesService->setFromWeight($fromWeight);
                                                $remoteareaChargesService->setToWeight($toWeight);
                                                $remoteareaChargesService->setAddedBy($user->getId());
                                                $remoteareaChargesService->setAddedDate(time());
                                                $remoteareaChargesService->setIsDeleted('N');
                                                $remoteareaChargesService->save();
                                            }
                                        }
                                }
                            }
                            //Delete all carrier old entries
                            $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                            $remoteareasGroupsFilter->addFilter("AND carrier_id = " . $carrierId);
                            $remoteareasGroupsIds = $remoteareasGroupsFilter->getColumnList("id");
                        }
                        //Delete all carrier remotearea charges valus
                    }
                   
                    $carrier_display_name = $this->form_vars["carrier_display_name"];
                    $status = (isset($this->form_vars["status"]) ? 1 : 0);
                    $carrier = $this->form_vars["carrier"];
                    $cut_off_time = $this->form_vars["cut_off_time"];
                    $parentid = $this->form_vars["parentid"];
                    $country = $this->form_vars["country"];
                    $currency_code = $this->form_vars["currency_code"];
                    $zone_base = (isset($this->form_vars["zone_base"]) ? 1 : 0);
                    $zone_type = (isset($this->form_vars["zone_type"]) ? 'country' : 'postcode');
                    $is_pallet = (isset($this->form_vars["is_pallet"]) ? 1 : 0);
                    $remotearea = (isset($this->form_vars["remotearea"]) ? 'c' : 's');
                    $is_gazetteer = (isset($this->form_vars["is_gazetteer"]) ? '1' : '0');
                    $is_reconcile = (isset($this->form_vars["is_reconcile"]) ? '1' : '0');
                    $on_contract = (isset($this->form_vars["on_contract"]) ? 1 : 0);

                    $uploadName = "";
                    // save new address details3

                    $error_array = array();
                    if (isset($_FILES["logo"]) && trim($_FILES["logo"]["name"]) != '' && isset($_POST["carrier"]) && trim($_POST["carrier"]) != '') {
                        $allowedExts = array("gif", "jpeg", "jpg", "png");
                        $temp = explode(".", $_FILES["logo"]["name"]);
                        $extension = end($temp);

                        if ((($_FILES["logo"]["type"] == "image/gif") || ($_FILES["logo"]["type"] == "image/jpeg") || ($_FILES["logo"]["type"] == "image/jpg") || ($_FILES["logo"]["type"] == "image/pjpeg") || ($_FILES["logo"]["type"] == "image/x-png") || ($_FILES["logo"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                            if ($_FILES["logo"]["error"] > 0) {
                                $error_array[] = "Return Code: " . $_FILES["logo"]["error"] . "<br>";
                            } else {
                                $uploadName = str_replace(' ', '-', strtolower($carrier)) . "_" . time() . "." . $extension;
                                move_uploaded_file($_FILES["logo"]["tmp_name"], "../images/carrierlogo/" . $uploadName);
                                $thumb = new easyphpthumbnail;

                                $thumb->Thumblocation = '../images/carrierlogo/thumbnail/';
                                $thumb->Thumbprefix = 'owe_';
                                $thumb->Thumbsaveas = 'png';
                                $thumb->Thumbfilename = $uploadName;
                                $thumb->Maketransparent = array(1, 0, '#FF0000', 0);
                                $thumb->Keeptransparency = true;
                                //$thumb -> Clipcorner = array(2,15,0,0,1,1,0);

                                $thumb->Thumbsize = 16;
                                $thumb->Thumbprefix = 'owe_16_';
                                $thumb->Createthumb("../images/carrierlogo/" . $uploadName, 'file');

                                $thumb->Thumbsize = 50;
                                $thumb->Thumbprefix = 'owe_50_';
                                $thumb->Createthumb("../images/carrierlogo/" . $uploadName, 'file');


                                $thumb->Thumbsize = 100;
                                $thumb->Thumbprefix = 'owe_100_';
                                $thumb->Createthumb("../images/carrierlogo/" . $uploadName, 'file');

                                $thumb->Thumbsize = 200;
                                $thumb->Thumbprefix = 'owe_200_';
                                $thumb->Createthumb("../images/carrierlogo/" . $uploadName, 'file');

                                $thumb->Thumbsize = 300;
                                $thumb->Thumbprefix = 'owe_300_';
                                $thumb->Createthumb("../images/carrierlogo/" . $uploadName, 'file');
                            }
                        } else {
                            $error_array[] = formatMessages(ERROR_INVALID_FILE); //"Invalid file";
                        }
                    } else if ((int) $carrier_id > 0) {
                        
                    } else if (!empty($this->form_vars['logo_hidden'])) {
                        $error_array[] = formatMessages(ERROR_FILE_EMPTY); //"Please select carrier and upload logo.";
                        $headerMessage = 'Error';
                    }

                    if (trim($parentid) > 0 && trim($country) == '') {
                        $error_array[] = formatMessages(ERROR_COUNTRY_EMPTY); //"Please select country for carreir.";
                        $headerMessage = 'Error';
                    }

                    if (count($error_array) <= 0) {
                        $carrierFilter = new CarrierFilter();
                        if ((int) $carrier_id > 0)
                            $carrierFilter->addFilter(" cl.id= '" . DbAccess3::escape($carrier_id) . "'");
                        else {
                            $carrierFilter->addCarrierFilter($carrier);
                            $carrierFilter->addFilter(" country_iso = '" . DbAccess3::escape($country) . "'");
                        }
                        $carrierResult = $carrierFilter->getList();

                        if (count($carrierResult) > 0) {

                            if ((int) $carrier_id > 0) {
                                $carrierLogo = new Carrier(intval($carrierResult[0]->getId()));
                                $carrierObjectOld = new Carrier(intval($carrierResult[0]->getId()));
                                $carrierLogo->setCarrier($carrier);
                                $carrierLogo->setCutOffTime($cut_off_time);
                                $carrierLogo->setCarrierDisplayName($carrier_display_name);
                                $carrierLogo->setStatus($status);
                                $carrierLogo->setCarrierId($parentid);
                                $carrierLogo->setCountryId($country);
                                $carrierLogo->setCurrencyCode($currency_code);
                                $carrierLogo->setRemoteareaCheck($remotearea);
                                $carrierLogo->setIsGazetteer($is_gazetteer);
                                $carrierLogo->setIsReconcile($is_reconcile);


                                $carrierLogo->setOnContract($on_contract);

                                $carrierLogo->setZoneBase($zone_base);
                                $carrierLogo->setZoneType($zone_type);


                                if ($uploadName != '')
                                    $carrierLogo->setLogo($uploadName);
                                $carrierLogo->save();
                                $carrier_id_Db = $carrierLogo->getId();
                                $carrierObjectNew = $carrierLogo;
                                $error_array[] = formatMessages(SUCCESS_CARRIER_UPDATE, FALSE); //"Carrier details has been updated successfully.";
                                $headerMessage = 'Success';
                            }
                            else {
                                $error_array[] = formatMessages(ERROR_CARRIER_DUPLICATE_ENTRY, FALSE); //"The carrier name (".$carrier."), you are using is already added.";
                                $headerMessage = 'Error';
                            }
                        } else {
                            $carrierLogo = new Carrier();
                            $carrierObjectOld = $carrierLogo;
                            $carrierLogo->setCarrier($carrier);
                            if ($uploadName != '')
                                $carrierLogo->setLogo($uploadName);
                            $carrierLogo->setCarrierDisplayName($carrier_display_name);
                            $carrierLogo->setStatus($status);
                            $carrierLogo->setCutOffTime($cut_off_time);
                            $carrierLogo->setCarrierId($parentid);
                            $carrierLogo->setCountryId($country);
                            $carrierLogo->setCurrencyCode($currency_code);
                            $carrierLogo->setRemoteareaCheck($remotearea);
                            $carrierLogo->setIsGazetteer($is_gazetteer);
                            $carrierLogo->setIsReconcile($is_reconcile);
                            $carrierLogo->setOnContract($on_contract);
                            $carrierLogo->setZoneBase($zone_base);
                            $carrierLogo->setZoneType($zone_type);

                            $carrierLogo->save();
                            $carrier_id_Db = $carrierLogo->getId();
                            $carrierObjectNew = $carrierLogo;
                            $error_array[] = formatMessages(SUCCESS_CARRIER_UPDATE, FALSE); //"Carrier details has been updated successfully.";
                            $headerMessage = 'Success';
                        }
                    }

                    $carrierLog = new CarrierLog();

                    if ((int) $carrier_id <= 0) {
                        //$carrierLog->createlog('Created new carrier', $carrier_id, 'CARRIER');
                        $carrierLog->createlog($user->getId(), '', $carrier_id_Db, 'CARRIER', $user->getUserName() . ' has added new carrier ' . $carrier, '', $newCarrierData);
                    } else {

                        $oldCarrierData = serialize($carrierObjectOld);
                        $newCarrierData = serialize($carrierObjectNew);
                        //createlog($user_id = '', $ipaddress, $Serviceid, $log_type, $message, $previous_data, $current_data)
                        $carrierLog->createlog($user->getId(), '', $carrier_id_Db, 'CARRIER', $user->getUserName() . ' has updated ' . $carrier, $oldCarrierData, $newCarrierData);
                    }
                    if (trim($headerMessage) == 'Success') {
                        $output["status"] = "success";
                        $output["message"] = Translation::GetCaption("CARRIER_ADD_SUCCESS_MESSAGE");
                        echo json_encode($output);
                        die;
                    } else {
                        $output["status"] = "error";
                        $output["message"] = $error_array;
                        echo json_encode($output);
                        die;
                    }
                    break;



                // CANCEL
                // - return to booking list
                case "cancel":
                default:
                    util_redirect("../main/carrier.php");
                    break;
            }
        }
//          not post back - first time this form is shown
        else {
            // get consignment id passed
//		/$id = util_get_num("id");
            $this->form_vars["id"] = $carrier_id;
            $carrierData = new Carrier($carrier_id);
            //
            $this->form_vars["carrier"] = $carrierData->getCarrier();
            $this->form_vars["cut_off_time"] = $carrierData->getCutOffTime();
            $this->form_vars["carrier_display_name"] = $carrierData->getCarrierDisplayName();
            $this->form_vars["status"] = $carrierData->getStatus();
            $this->form_vars["remotearea_check"] = $carrierData->getRemoteareaCheck();
            $this->form_vars["on_contract"] = $carrierData->getOnContract();

        }
//          Save Service remotearea
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_service_remotearea') {
            $serviceId = $this->form_vars['service_id'];
            $carreirId = $this->form_vars['carreir_id'];
            $user = SessionManager::getUser();
            //Save service remotearea check
            $services = new Services($serviceId);
            $services->setIsRemotearea("Y");
            $services->save();
            parse_str($this->form_vars['group'], $group);
            parse_str($this->form_vars['charges'], $charges);
            parse_str($this->form_vars['from_weight'], $fromWeight);
            parse_str($this->form_vars['to_weight'], $toWeight);
            parse_str($this->form_vars['formula'], $formula);
            RemoteareaChargesServices::deleteByServiceId($serviceId);
            foreach ($group['group'] as $key => $groupIds) {
                $remoteareaChargesServices = new RemoteareaChargesServices();
                $remoteareaChargesServices->setRemoteareaGroupId($groupIds);
                $remoteareaChargesServices->setServiceId($serviceId);
                $remoteareaChargesServices->setRemoteareaCharges($charges['charges'][$key]);
                $remoteareaChargesServices->setFromWeight($fromWeight['from_weight'][$key]);
                $remoteareaChargesServices->setToWeight($toWeight['to_weight'][$key]);
                $remoteareaChargesServices->setFormulla($formula['formula'][$key]);
                $remoteareaChargesServices->setAddedBy($user->getId());
                $remoteareaChargesServices->setAddedDate(time());
                $remoteareaChargesServices->setIsDeleted('N');
                $remoteareaChargesServices->save();
            }
            $output["status"] = "success";
            $output["message"] = formatMessages(SUCCESS_SERVICE_REMOTE_AREA_ADDED, FALSE); //"Service remotearea added sucessfully";
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars["get_all"]) && $this->form_vars["get_all"] == "carrier_list") {
            $carrierFilter = new CarrierFilter();
            $carrierFilter->AddOrderBy('carrier', true);
            $carrierFilter->addFilter(" status != '2' ");
            $carrierResult = $carrierFilter->getCarrierList("cl.*,cn.name 'country_name',cn.iso 'country_iso'");
            $carrierFilterCount = 0;
            $html = "";
            $count = 1;
            foreach ($carrierResult as $rowImage) {
                $carrierName = $rowImage->getCarrier();
                $carrierId = $rowImage->getId();
                $cutOffTime = 'Cut Off - ' . $rowImage->getCutOfftime();
                $displayName = $rowImage->getCarrierDisplayName();
                $status = $rowImage->getStatus();
                $countryIso = $rowImage->getCountryIso();
                $countryName = $rowImage->getCountryName();
                $currencyCode = $rowImage->getCurrencyCode();
                $zoneBase = $rowImage->getZoneBase();
                $zoneType = $rowImage->getZoneType();
                $onContract = (trim($rowImage->getOnContract()) == '1'?'Available':'Unavailable');
                $carrierBaseHtml = '';
                if ($zoneBase == 1) {
                    $carrierBaseHtml = (Permissions::checkFilePermission('carrier_tariff_list') ? '<a class="btn btn-xs btn-default blue btn-outline margin-right-5" href="tariffs_list.php?carrier_id=' . $rowImage->getId() . '" data-carrier_id = "' . $rowImage->getId() . '" rel="tooltip" title="Tariff List">
                                                 <span class="fa fa-money"></span> 
                                            </a>' : '')
                            . (Permissions::checkFilePermission('carrier_add_zone') ? '<a class="btn btn-xs btn-default blue btn-outline margin-right-5" href="carrier_zones.php?carrier_id=' . $rowImage->getId() . '" data-carrier_id = "' . $rowImage->getId() . '" rel="tooltip"  placeholder="Add Carrier Zones" title="Add Carrier Zones" >
                                                 <span class="fa fa-dropbox"></span> 
                                            </a>' : '');
                }
                $logoPath = '../images/carrierlogo/thumbnail/owe_100_' . $rowImage->getLogo();
                if (!file_exists($logoPath))
                    $logoPath = '../images/carrierlogo/thumbnail/owe_100_no_image.png';

                $html .= '<div data-name="' . $displayName . '" class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope search_carrier_custom">
                                    <div class="panel panel-default mt-element-ribbon">

                                 
                        ' . (trim($status) == '1' ? '<span class="ribbon ribbon-right ribbon-color-success uppercase carrier_status status_' . $carrierId . '" data-status="inactive" data-carrier_id="' . $carrierId . '"  data-carrier_name="' . $displayName . '">  ACTIVE </span>' :
                        '<span class="ribbon ribbon-right ribbon-color-danger uppercase carrier_status status_' . $carrierId . '"  data-status="active"  data-carrier_id="' . $carrierId . '" data-carrier_name="' . $displayName . '">  INACTIVE </span>') . '
                                
                        ' . (Permissions::checkFilePermission('carrier_edit') ? '<a  class="carrier_edit" data-carrier_id = "' . $rowImage->getId() . '" rel="tooltip" title="Edit">
                           <div class="ribbon ribbion-postion ribbon-border ribbon-color-primary uppercase">
                              EDIT </span>
                           </div>
                          </a>' : '') . '
                       
                        <div class="text-center bold carrier-heading-box">' . $displayName . '</div>

                            <div class="panel-body" title="' . $displayName . '-' . $cutOffTime . '">
                                <div class="img-wrapper clearfix">
                                    <img class="img-responsive center-block" src="' . $logoPath . '" alt="' . $rowImage->getCarrier() . '" uib-popover="' . $rowImage->getCarrier() . '" popover-trigger="mouseenter" >
                                </div> 
                            </div>
                            <div class="text-center" >
                                <ul class="list-unstyled task-list">
                                    <li class="clearfix"><img src="../assets/global/img/flags/' . strtolower($countryIso) . '.png"> ' . $countryName . '</li>
                                    <li class="clearfix">' . $cutOffTime . '</li>
                                    <li class="clearfix"> Currency: ' . $currencyCode . '</li>
                                    <li class="clearfix"> Customer Contract: ' . $onContract . '</li>
                                </ul>
                            </div>        
                                        <div class="panel-footer bg-primary clearfix text-center" >  
                                            ' . (Permissions::checkFilePermission('carrier_delete') ? '<a class="btn btn-xs btn-default red btn-outline delete_carrier margin-right-5" href="#" rel="tooltip" title="Delete" data-carrierId = "' . $rowImage->getId() . '"><span class="fa fa-trash"></span></a>' : '')
                        . (Permissions::checkFilePermission('carrier_audit') ?  "<a href='' class='btn btn-xs btn-default blue btn-outline margin-right-5' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $rowImage->getId() . "' data-log_name='carrier' data-toggle='modal'> <i class='fa fa-list'></i></a>": '')
                        . (Permissions::checkFilePermission('carrier_show_services') ? '<a class="btn btn-xs btn-default blue btn-outline margin-right-5 event-carrierload" 
                                                rel="tooltip" title="Services"
                                                data-toggle="modal" 
                                                data-target="#carrier-service-popup" 
                                                role="dialog" 
                                                tabindex="-1"
                                                
                                                data-carrier_name="' . $rowImage->getCarrier() . '" 
                                                data-action="GET_ALL_SERVICES"
                                                data-courierimage="' . $rowImage->getLogo() . '" 
                                                data-courierid="' . $rowImage->getId() . '" 
                                                data-carrierload="carrier.php"
                                                data-cut_of_time="' . $cutOffTime . '" 
                                                data-carrier_display_name="' . $displayName . '" 
                                                data-carrier_status="' . $status . '" 
                                                data-carrier_country="' . $countryName . '" 
                                                data-carrier_country_iso="' . $rowImage->getCountryIso() . '"
                                                data-carrier_remotearea_check="' . $rowImage->getRemoteareaCheck() . '"
                                                >
                                                <span class="fa fa-plug"></span>
                                            </a>' : '')
                        . (Permissions::checkFilePermission('carrier_transit_time') ? '<a class="btn btn-xs btn-default blue btn-outline margin-right-5" href="service_country_time.php?carrier_id=' . $rowImage->getId() . '" data-carrier_id = "' . $rowImage->getId() . '" rel="tooltip" title="Transit Time">
                                                 <span class="fa fa-calendar-o"></span> 
                                            </a>' : '')
                        . (Permissions::checkFilePermission('carrier_document_upload') ? ''
                        . ' <a '
                        . 'class="btn btn-xs btn-default blue btn-outline margin-right-5 carrier_document_upload" '
                        . ' '
                        . 'data-carrier_name="' . $rowImage->getCarrier() . '" '
                        //. 'data-toggle="modal" '
                        //. 'data-target="#carrier-documents-popup"  '
                        . 'data-action="GET_ALL_DOOCUMENTS" '
                        . 'data-courierimage="' . $rowImage->getLogo() . '" '
                        . 'data-courier_name="' . $rowImage->getCarrier() . '" '
                        . 'data-carrier_id = "' . $rowImage->getId() . '"
                                                
                                                rel="tooltip" title="Carrier Documents">
                                                 <span class="fa fa-file-o"></span> 
                                            </a>' : '')
                    
                        . ((Permissions::checkFilePermission('carrier_document_upload') && $rowImage->getIsGazetteer() == '1') ? ''
                        . ' <a '
                        . 'class="btn btn-xs btn-default blue btn-outline margin-right-5 carrier_gaz_document_upload" '
                        . ' '
                        . 'data-carrier_name="' . $rowImage->getCarrier() . '" '
                        //. 'data-toggle="modal" '
                        //. 'data-target="#carrier-documents-popup"  '
                        . 'data-action="GET_GAZ_DOOCUMENTS" '
                        . 'data-courierimage="' . $rowImage->getLogo() . '" '
                        . 'data-courier_name="' . $rowImage->getCarrier() . '" '
                        . 'data-carrier_id = "' . $rowImage->getId() . '"
                                rel="tooltip" title="Carrier  Gazetier">
                                 <span class="fa fa-sitemap"></span> 
                            </a>' : '')
                        . $carrierBaseHtml
                        . '</div>
                                    </div>
                                </div>';
            }
            $html .= '<div class="clearfix"></div>';
            echo $html;
            die;
        }
        if (isset($this->form_vars["edit_carrier"]) && $this->form_vars["edit_carrier"] == "editEvent") {

            $carrier_id = DbAccess3::escape(trim($this->form_vars["carrier_id"]));
            $carrierDataArr['remotearea_chares'] = Carrier::getRemoteareaContent($carrier_id);
            $carrierData = new Carrier($carrier_id);
            $carrierDataArr["carrier"] = $carrierData->getCarrier();
            $carrierDataArr["cut_off_time"] = $carrierData->getCutOffTime();
            $carrierDataArr["carrier_display_name"] = $carrierData->getCarrierDisplayName();
            $carrierDataArr["status"] = $carrierData->getStatus();
            $carrierDataArr["country"] = $carrierData->getCountryId();
            $carrierDataArr["currency_code"] = $carrierData->getCurrencyCode();
            $carrierDataArr["remotearea_check"] = $carrierData->getRemoteareaCheck();

            $carrierDataArr["is_gazetteer"] = $carrierData->getIsGazetteer();
            $carrierDataArr["is_reconcile"] = $carrierData->getIsReconcile();

            $carrierDataArr["on_contract"] = $carrierData->getOnContract();
            $carrierDataArr["carrier_id"] = $carrierData->getCarrierId();
            $carrierDataArr["zone_base"] = $carrierData->getZoneBase();
            $carrierDataArr["zone_type"] = $carrierData->getZoneType();
            $carrierDataArr["carrier_tbl_id"] = $carrierData->getId();
            $carrierDataArr["logo"] = '../images/carrierlogo/thumbnail/owe_100_' . $carrierData->getLogo();
            $countryObj = new Country($carrierData->getCountryId());
            $carrierDataArr["country_name"] = $countryObj->getName();
            $carrierDataArr["country_iso"] = strtolower($countryObj->getIso());

            echo json_encode($carrierDataArr);
            die;
        }
        if (isset($this->form_vars["action"]) && $this->form_vars["action"] == "get_remotearea_change") {
            $carrierId = (int) trim($this->form_vars["carrier_id"]);
            $modelType = trim($this->form_vars["model_type"]);
            $serviceId = trim($this->form_vars["service_id"]);
            if ($modelType == "carrier")
                $remoteareaDataArr['remotearea_chares'] = Carrier::getRemoteareaContent($carrierId, $modelType, $serviceId, false);
            else
                $remoteareaDataArr['remotearea_chares'] = Carrier::getRemoteareaContent($carrierId, $modelType, $serviceId, true);
            echo json_encode($remoteareaDataArr);
            die;
        }
//          Handle carrier service CSV            
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download_service') {
            $carrierId = $this->form_vars['carrier_id_hidden_service'];
            if ($carrierId > 0) {
                $carrierObj = new Carrier($carrierId);
                $csvObj = new RemoteareaChargesServices();
                $sql = "SELECT rg.id, rcs.remotearea_charges, rcs.from_weight,rcs.to_weight, rcs.to_weight, rcs.formulla, rg.group_name AS remotearea_group_id, s.name AS service_id FROM `remotearea_charges_services` rcs LEFT JOIN `remoteareas_groups` rg  ON rcs.remotearea_group_id = rg.id LEFT JOIN `services` s ON rcs.service_id = s.id WHERE s.`carrier_id` = '" . DbAccess3::escape($carrierId) . "' AND rg.is_deleted = 'N'";
                $res = $csvObj->getDataFromSql($sql);
                if (count($res) > 0) {
                    $returnString = "Service Name,Group Name,From Weight,To Weight,Charges,Fromula";
                    $carrierFileName = "";
                    foreach ($res as $resultData) {
                        $returnString .= "\r\n";
                        $carrierFileName = $carrierObj->getCarrier();
                        $returnString .= $resultData->getServiceId() . ",";
                        $returnString .= $resultData->getRemoteareaGroupId() . ",";
                        $returnString .= $resultData->getFromWeight() . ",";
                        $returnString .= $resultData->getToWeight() . ",";
                        $returnString .= $resultData->getRemoteareaCharges() . ",";
                        $returnString .= $resultData->getFormulla();
                    }
                    header("Content-type: text/csv");
                    header("Content-Disposition: attachment; filename=" . $carrierFileName . ".csv");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    echo $returnString;
                    die;
                }
            }
        }
//          Handle download Csv carrier
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download') {
            $carrierId = (int) $this->form_vars['carrier_id_hidden'];
            $downloadType = $this->form_vars['download_type'];
            if ($downloadType == "carrier") {
                if ($carrierId > 0) {
                    $carrierObj = new Carrier($carrierId);
                    $csvObj = new RemoteareaChargesCarrier();
                    if (trim($carrierId) != '') {
                        $sql = "SELECT  rcc.remotearea_charges, rg.group_name AS remotearea_group_id FROM `remotearea_charges_carrier` rcc  LEFT JOIN remoteareas_groups rg   ON rg.id = rcc.remotearea_group_id  WHERE rg.carrier_id = '" . DbAccess3::escape($carrierId) . "'  AND rg.is_deleted = 'N'  ORDER BY rcc.id ASC";
                        $res = $csvObj->getDataFromSql($sql);
                        if (count($res) > 0) {
                            $returnString = "Group Name, Charges";
                            $carrierFileName = "";
                            foreach ($res as $resultData) {
                                $returnString .= "\r\n";
                                $carrierFileName = $carrierObj->getCarrier();
                                $returnString .= $resultData->getRemoteareaGroupId() . ",";
                                $returnString .= $resultData->getRemoteareaCharges();
                            }
                            header("Content-type: text/csv");
                            header("Content-Disposition: attachment; filename=" . $carrierFileName . ".csv");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            echo $returnString;
                            die;
                        }
                    }
                }
            }
        }
//          Handle Impoert csv carrier
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            $carrierId = $this->form_vars['carrier_id'];

            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $relPath = '../_assets/remoteareas_csv/' . $new_file_name;
                    if (!file_exists("../_assets/remoteareas_csv/"))
                        @mkdir("../_assets/remoteareas_csv/", 0775);
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                        $row = 1;
                        $successRecords = 0;
                        $errorRecords = 0;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = '';
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                if ($row < 2) {
                                    $row++;
                                    continue;
                                }
                                $groupName = $data[0];
                                $charges = $data[1];
                                $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                                $remoteareasGroupsFilter->addFilter("AND group_name = '" . DbAccess3::escape($groupName) . "' AND carrier_id = '" . DbAccess3::escape($carrierId) . "'");
                                $remoteareasGroupsResult = $remoteareasGroupsFilter->getColumnList("id");
                                if (count($remoteareasGroupsResult) > 0) {
                                    $groupId = $remoteareasGroupsResult[0]->getId();
                                    $csvContent .= (!empty($csvContent) ? "\r\n" : "") . $groupId . "," . $charges;
                                    $successRecords++;
                                } else {
                                    $errorRecords++;
                                }
                                $row++;
                            }
                            fclose($handle);
                            $CurFileName = "remotearea" . time() . ".csv";
                            //Write File
                            $CurFileContent = "../_assets/remoteareas_csv/" . $CurFileName;
                            if (!empty($csvContent)) {
                                if (file_put_contents($CurFileContent, $csvContent)) {
                                    $output['file_name'] = $new_file_name;
                                    $newDate = date('Y-m-d H:i:s');
                                    $load_data_sql = "LOAD DATA LOCAL INFILE '../_assets/remoteareas_csv/" . $CurFileName . "' INTO TABLE `remotearea_charges_carrier` FIELDS ENCLOSED BY '\"'
                                            TERMINATED BY ',' LINES TERMINATED BY '\r\n' (
                                                              `remotearea_group_id`,`remotearea_charges`
                                                            ) SET is_deleted = 'N' , added_by =  '" . $user->getId() . "' , added_date='" . $newDate . "'";
                                    $output['sql'] = $load_data_sql;
                                    $res = DbAccess3::runQueryWithError($load_data_sql);
                                    if ($res === false) {
                                        $error = DbAccess3::$dbError;
                                        $output['message'] = $error[0];
                                        $output['status'] = 'fail';
                                    } else {
                                        @unlink('../_assets/remoteareas_csv/' . $CurFileName);
                                        $message = 'Uploaded successfully with';
                                        $message .= '<br /> ' . $successRecords . formatMessages(SUCCESS_IMPORTED_RECORD, FALSE);
                                        $message .= '<br /> ' . $errorRecords . formatMessages(ERROR_INVALID_RECORD, FALSE);
                                        $output['message'] = $message;
                                    }
                                }
                            } else {
                                $message = 'Uploaded successfully with';
                                $message .= '<br /> ' . $successRecords . formatMessages(SUCCESS_IMPORTED_RECORD, FALSE);
                                $message .= '<br /> ' . $errorRecords . formatMessages(ERROR_INVALID_RECORD, FALSE);
                                $output['message'] = $message;
                            }
                        }
                    } else {
                        $output['message'] = formatMessages(ERROR_FILE_UPLOADED, FALSE); // '';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = formatMessages(ERROR_INVALID_FILE, FALSE); //'Invalid CSV File.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = formatMessages(ERROR_FILE_EMPTY, FALSE); //'No file found to import data.';
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        }
//          Handle Import csv Service
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file_service') {
            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            $carrierId = $this->form_vars['carrier_id'];
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $relPath = '../_assets/remoteareas_csv/' . $new_file_name;
                    if (!file_exists("../_assets/remoteareas_csv/"))
                        @mkdir("../_assets/remoteareas_csv/", 0775);
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {

                        $row = 1;
                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = '';
                            $successRecords = 0;
                            $errorRecords = 0;
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                if ($row < 2) {
                                    $row++;
                                    continue;
                                }
                                $serviceName = $data[0];
                                $groupName = $data[1];
                                $fromWeight = $data[2];
                                $toWeight = $data[3];
                                $charges = $data[4];
                                $formula = $data[5];

                                $serviceFilter = new ServiceFilter();
                                $serviceFilter->addFilter("    name = '" . DbAccess3::escape($serviceName) . "'");
                                $serviceFilterResult = $serviceFilter->getColumnList("id");

                                $serviceId = 0;
                                if (count($serviceFilterResult) > 0) {
                                    $remoteareasGroupsFilter = new RemoteareasGroupsFilter();
                                    $remoteareasGroupsFilter->addFilter("AND group_name = '" . DbAccess3::escape($groupName) . "' AND carrier_id = '" . DbAccess3::escape($carrierId) . "'");
                                    $remoteareasGroupsResult = $remoteareasGroupsFilter->getColumnList("id");
                                    if (count($remoteareasGroupsResult) > 0) {
                                        $groupId = $remoteareasGroupsResult[0]->getId();
                                        $serviceId = $serviceFilterResult[0]->getId();
                                        $csvContent .= (!empty($csvContent) ? "\r\n" : "") . $serviceId . "," . $groupId . "," . $fromWeight . "," . $toWeight . "," . $charges . "," . $formula;
                                        $successRecords++;
                                    } else {
                                        $errorRecords++;
                                    }
                                } else {
                                    $errorRecords++;
                                }
                                //Update services table is_remotearea
                                if ($serviceId > 0) {
                                    $service = new Services($serviceId);
                                    $service->setIsRemotearea("Y");
                                    $service->save();
                                }
                                $row++;
                            }
                            fclose($handle);
                            $CurFileName = "remotearea_service" . time() . ".csv";
                            //Write File
                            $CurFileContent = "../_assets/remoteareas_csv/" . $CurFileName;
                            if (!empty($csvContent)) {
                                if (file_put_contents($CurFileContent, $csvContent)) {
                                    $output['file_name'] = $new_file_name;
                                    $newDate = date('Y-m-d H:i:s');
                                    $load_data_sql = "LOAD DATA LOCAL INFILE '../_assets/remoteareas_csv/" . $CurFileName . "' INTO TABLE `remotearea_charges_services` FIELDS ENCLOSED BY '\"'
                                            TERMINATED BY ',' LINES TERMINATED BY '\r\n' (
                                                              `service_id`,`remotearea_group_id`,`from_weight`,`to_weight`,`remotearea_charges`,`formulla`
                                                            ) SET is_deleted = 'N' , added_by =  '" . $user->getId() . "' , added_date='" . $newDate . "'";
                                    $output['sql'] = $load_data_sql;
                                    $res = DbAccess3::runQueryWithError($load_data_sql);
                                    if ($res === false) {
                                        $error = DbAccess3::$dbError;
                                        $output['message'] = $error[0];
                                        $output['status'] = 'fail';
                                    } else {
                                        @unlink('../_assets/remoteareas_csv/' . $CurFileName);
                                        $message = 'Uploaded successfully with';
                                        $message .= '<br /> ' . $successRecords . formatMessages(SUCCESS_IMPORTED_RECORD, FALSE);
                                        $message .= '<br /> ' . $errorRecords . formatMessages(ERROR_INVALID_RECORD, FALSE);
                                        $output['message'] = $message;
                                    }
                                }
                            } else {
                                $message = 'Uploaded successfully with';
                                $message .= '<br /> ' . $successRecords . formatMessages(SUCCESS_IMPORTED_RECORD, FALSE);
                                $message .= '<br /> ' . $errorRecords . formatMessages(ERROR_INVALID_RECORD, FALSE);
                                $output['message'] = $message;
                            }
                        }
                    } else {
                        $output['message'] = formatMessages(ERROR_FILE_UPLOADED, FALSE); // '';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = formatMessages(ERROR_INVALID_FILE, FALSE); //'Invalid CSV File.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = formatMessages(ERROR_FILE_EMPTY, FALSE); //'No file found to import data.';
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        }
//          Check download CSV count for carreir
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "check_download_remoteareas") {
            $csvObj = new RemoteareaChargesCarrier();
            $carrierId = 0;
            if (!empty($this->form_vars['carrier_id']))
                $carrierId = $this->form_vars['carrier_id'];
            $sql = "SELECT  COUNT(rcc.id) AS id FROM `remotearea_charges_carrier` rcc  LEFT JOIN remoteareas_groups rg   ON rg.id = rcc.remotearea_group_id  WHERE rg.carrier_id = '" . DbAccess3::escape($carrierId) . "'  AND rg.is_deleted = 'N'  ORDER BY rcc.id ASC";
            $res = $csvObj->getDataFromSql($sql);
            if ($res[0]->getId() > 0) {
                $output["status"] = "success";
                echo json_encode($output);
                die;
            } else {
                $output["status"] = "error";
                echo json_encode($output);
                die;
            }
            die;
        }
//          Check download CSV count for service            
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "check_download_remoteareas_service") {
            $csvObj = new RemoteareaChargesServices();
            $carrierId = 0;
            if (!empty($this->form_vars['carrier_id']))
                $carrierId = $this->form_vars['carrier_id'];
            $sql = "SELECT COUNT(rg.id) AS id FROM `remotearea_charges_services` rcs LEFT JOIN `remoteareas_groups` rg  ON rcs.remotearea_group_id = rg.id LEFT JOIN `services` s ON rcs.service_id = s.id WHERE s.`carrier_id` = '" . DbAccess3::escape($carrierId) . "' AND rg.is_deleted = 'N'";
            $res = $csvObj->getDataFromSql($sql);
            if ($res[0]->getId() > 0) {
                $output["status"] = "success";
                echo json_encode($output);
                die;
            } else {
                $output["status"] = "error";
                echo json_encode($output);
                die;
            }
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete_services_remoteareas") {
            $serviceId = $this->form_vars['service_id'];
            if ($serviceId > 0) {
                RemoteareaChargesServices::deleteByServiceId($serviceId);
                $service = new Services($serviceId);
                $service->setIsRemotearea("N");
                $service->save();
                $output["status"] = "success";
                $output["message"] = Translation::GetCaption("RECORD_DELETED_SUCCESSFULLY");
                echo json_encode($output);
            } else {
                $output["status"] = "error";
                $output["message"] = formatMessages(ERROR_FAILED_REQUEST, FALSE);
                "Some error occur";
                echo json_encode($output);
            }
            die;
        }
        // common initialisation for ths page
        $this->setTitle("Carriers List");
    }

    public function buildCategory($parent, $category) {
        $html = "";
        if (isset($category['parent_cats'][$parent])) {
            $html .= "<ul>\n";
            foreach ($category['parent_cats'][$parent] as $cat_id) {
                if (!isset($category['parent_cats'][$cat_id])) {
                    $html .= "<li>\n  <a href='" . $category['categories'][$cat_id]['category_link'] . "'>" . $category['categories'][$cat_id]['category_name'] . "</a>\n</li> \n";
                }
                if (isset($category['parent_cats'][$cat_id])) {
                    $html .= "<li>\n  <a href='" . $category['categories'][$cat_id]['category_link'] . "'>" . $category['categories'][$cat_id]['category_name'] . "</a> \n";
                    $html .= $this->buildCategory($cat_id, $category);
                    $html .= "</li> \n";
                }
            }
            $html .= "</ul> \n";
        }
        return $html;
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        ?>

        <?php
    }

    protected function getCategories($parent, $category) {
        $html = "";
        if (isset($category['parent_cats'][$parent])) {
            $html .= "<ul>\n";
            foreach ($category['parent_cats'][$parent] as $cat_id) {
                if (!isset($category['parent_cats'][$cat_id])) {
                    $html .= "<li>" . $category['categories'][$cat_id]->getCarrier() . "</li> \n";
                }
                if (isset($category['parent_cats'][$cat_id])) {
                    $html .= "<li>" . $category['categories'][$cat_id]->getCarrier() . " \n";
                    $html .= $this->getCategories($cat_id, $category);
                    $html .= "</li> \n";
                }
            }
            $html .= "</ul> \n";
        }
        return $html;
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />


        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->

        <style>
           
        </style>


        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>



        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="../assets/pages/scripts/form-fileupload.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->


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
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1"><?= Translation::GetCaption("ADD_UPDATE_CARRIER"); ?></h4>
                        <div class="flex-shrink-0">
                            <a href="carrier_list.php" class="btn btn-primary">List Carrier</a>

                            <div style="display: none;" class="col-md-12"  id="id="res_message" >
                                <div class="alert alert-success" id="res_message_div"></div>
                            </div>
                            <!--<div class="col-md-12 alert alert-success display-none"  id="res_message" ></div>-->
                            <?php if (errorList::getItem()->getErrorCount() > 0) { ?>
                                <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
                            <?php } ?>
                            <?php
                            if (Permissions::checkFilePermission('carrier_download_excel') && 1==2) {
                                ?>
                                <a  id="export_carriers_excel" class="btn blue btn-sm margin-left-5" data-original-title="Download Carriers" title="Download Carriers"><span></span><i class="fa fa-download"></i>&nbsp;Download Excel</a>
                            <?php } ?>

                        </div>
                    </div><!-- end card header -->
                    <?php if (Permissions::checkFilePermission('carrier_add')) { ?>
                         <div class="card-body">
                            <div class="live-preview">
                                <form name="adminForm" id="adminForm" action="" method="POST"  >
                                    <div class="row gy-4">
                                        <div class="col-xxl-6 col-md-6">
                                            <div>
                                                <label for="carrier_display_name" class="form-label">Carrier Display Name</label>
                                                <input  autocomplete="off" type="text" class="form-control rounded-pill" name="carrier_display_name" id="carrier_display_name" value="<?php echo @$carrier_display_name; ?>" required="required">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-xxl-6 col-md-6">
                                            <div>
                                                <label for="cut_off_time" class="form-label">Cut Off Time</label>
                                                <input  autocomplete="off" type="text" class="form-control rounded-pill" name="cut_off_time" id="cut_off_time" value="<?php echo @$cut_off_time; ?>" required="required">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-xxl-6 col-md-6">
                                            <label for="parentid" class="form-label">Main Carrier</label>
                                            <?php
                                            echo Ddl::generateCarrierDDLWithImage('parentid', $parentid, 'id', ' class="form-control rounded-pill" required="" data-show-subtext="true"', 'parentid', '', " carrier_id = '0' AND country_id <= '0' ");
                                            ?>
                                        </div>
                                        <div class="col-xxl-6 col-md-6">
                                            <label for="currency_code" class="form-label">Currency</label>
                                            <?php echo Ddl::generateDDL('currency_code', 'CurrencyFilter', ' AND isactive = 1 ', 'rightsymbol', 'rightsymbol', $currency_code, ' class="form-control rounded-pill"  ', 'Select Currency', '', '', ''); ?>
                                        </div>
                                        <div class="col-xxl-6 col-md-6">
                                            <label for="dashboard" class="form-label">Origin Country</label>
                                            <?php
                                            echo Ddl::generateCountryDDL('country', $country, '');
                                            ?>
                                        </div>
                                        <div class="col-xxl-4 col-md-4">
                                            <label for="zone_base" class="form-label">Zone Base</label>
                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input <?php echo ($zone_base == 0 ? 'checked="checked"' : ''); ?> name="zone_base"  id="zone_base" type="checkbox" class="form-check-input"  >
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-md-4">
                                            <label for="zone_type" class="form-label">Zone Type</label>
                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input <?php echo ($zone_type == 'country' ? 'checked="checked"' : ''); echo (empty($zone_type)) ? 'checked="checked"' : ''; ?> name="zone_type"  id="zone_type" type="checkbox" class="form-check-input"  >
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-md-4">
                                            <label for="is_gazetteer" class="form-label">Gazetteer Available</label>
                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input <?php echo ($is_gazetteer == '1' ? 'checked="checked"' : ''); ?> name="is_gazetteer"  id="is_gazetteer" type="checkbox" class="form-check-input"  >
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-md-4">
                                            <label for="remotearea" class="form-label">Remote area</label>
                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input <?php echo ($is_gazetteer == '1' ? 'checked="checked"' : ''); ?> name="remotearea"  id="remotearea" type="checkbox" class="form-check-input"  >
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-md-4">
                                            <label for="status" class="form-label">Status</label>
                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input <?php echo ($status == '1' ? 'checked="checked"' : ''); ?> name="status"  id="on_contract" type="checkbox" class="form-check-input"  >
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-md-4">
                                            <label for="on_contract" class="form-label">Customer Own Contract</label>
                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input <?php echo ($on_contract == '1' ? 'checked="checked"' : ''); ?> name="on_contract"  id="on_contract" type="checkbox" class="form-check-input"  >
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-md-4">
                                            <label for="is_reconcile" class="form-label">Reconcilable</label>
                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input <?php echo ($on_contract == '1' ? 'checked="checked"' : ''); ?> name="is_reconcile"  id="is_reconcile" type="checkbox" class="form-check-input"  >
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-md-4" id="remotearea_charges_div" style="display: none;">
                                            <a data-carrier_id="" data-carrier_name="" data-carrier_logo="" data-country_name="" data-country_iso="" data-cut_off_time="" data-active="" id="remotearea_charges_link"  class="btn btn-sm btn-info margin-top-20">
                                                 Remotearea Charges
                                            </a>
                                        </div>
                                        <div class="col-xxl-4 col-md-4" >
                                            <input type="hidden" id="logo" name="logo" value="<?php echo $logo; ?>">
                                            <label for="your_logo"> Carrier Logo</label>
                                            <br clear="all">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;">
                                                    <?php
                                                    echo '<img src = "../images/No-image-found.jpg">';
                                                    ?>
                                                </div>
                                                <div> <span class="btn default btn-file"> <span class="fileinput-new"> Select image </span> <span class="fileinput-exists"> Change </span>
                                        <input id="logo"  type="file" name="logo"  accept="image/*"  required="required"  >
                                    </span> <a  class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a> </div>
                                            </div>
                                            <div class="clearfix margin-top-10"> <span class="label label-primary"><small>NOTE!</span> Recommended logo dimensions (254 x 62) </small></div>
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <a id="btnSave"    class="btn btn-success"><span></span>Save</a>
                                            <a href="carrier.php" id="btnCancel" class="btn_cancel btn btn btn-primary"><span></span>Cancel</a>
                                        </div>
                                    </div>
                                </div>
                             <input type="hidden" name="id" id="id" value="<?php echo @$this->form_vars["id"]; ?>" />
                             <input type="hidden" name="form_action" id="form_action" value="" />
                             <input type="hidden" name="group_serialize" id="group_serialize" value="" />
                             <input type="hidden" name="charges_serialize" id="charges_serialize" value="" />
                             <input type="hidden" name="copy_carrier" id="copy_carrier" value="" />
                            </form>
                            <!--end row-->
                        </div>
                         </div>
                    <?php }?>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->

        <div class="modal fade" tabindex="-1" role="dialog" id="carrier-service-popup" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="carrier-content-display">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="serviceCountryName"></span> Services</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" >
                            <div class="col-md-12"  id="carrier-content-display-wait">

                            </div>
                        </div>       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="service-country-popup" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><span id="serviceCountryName"></span> Countries</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row" >
                            <div class="col-md-12"  id="service-country-content-display">

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


        <!--Model for carrier details-->
        <div class="modal fade" role="dialog" id="carrier-documents-popup" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Carriers Documents Copy for <span id='doc-carrier-name'></span></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="carrier_id_for_doc" name="carrier_id_for_doc">
                        <div class="row" >
                            <div class="col-md-12"  id="model-cd-content-display">


                                <!--  ########################################################### -->
                                <?php ?>


                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <div class="first_form_col">
                                                <label>Document Type</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon"> <i class="fa fa-file-o"></i></div>
                                                    <!-- User Document -->
                                                    <?php
                                                    echo Ddl::generateDDL('document_name', 'DocumentTypeFilter', " is_active = '1' AND is_delete = '0' AND document_type = 'service_contract'", 'document_name', 'id', '', ' class="bs-select form-control" required="" data-show-subtext="true" data-toggle="tooltip"  title="User Document Type" data-original-title="User Document Type"', '', 'Select document type', 'document_name', 'User Document Type', '', '');
                                                    ?>
                                                    <span class="input-group-addon red-18">*</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Please Select File</label>
                                        <div class="input-group input-group-sm">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-group-sm">
                                                    <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                        <span class="fileinput-filename"> </span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="file_name" id="file_name"> </span>
                                                    <a  class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                    <a  class="input-group-addon btn blue" id="upload_file" data-original-title="" title="">Upload</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="append_service_doc">


                                    <!--  ########################################################### -->

                                </div>
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


        <!--Model for carrier details-->
        <div class="modal fade" tabindex="-1" role="dialog" id="carrier-detail-popup">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Carriers List</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" >
                            <div class="col-md-12"  id="model-carrier-content-display">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th> Carrier Name </th>
                                            <th> Carrier Display Name </th>
                                            <th> Cut of time </th>
                                            <th> Carrier Service </th>
                                            <th> Country </th>
                                            <th> Status </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
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
        <!--End Model for carrier details-->

        <div class="modal fade bs-modal-lg" id="remotearea_change_model" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title"><i class="glyphicon glyphicon-save"></i> Remotearea Carrier Charges</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div  id="console_window" style="display: none; clear:both;background-color: #000;color: #FFF; padding: 15px;margin-bottom: 15px;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger display-none"  id="res_message" ></div>
                            </div>
                        </div>
                        <div class="row carrier_model_csv">
                            <div class="col-md-8">
                                <div class="row hide">
                                    <div class="col-sm-4">
                                        <strong>Origin:</strong> <span id="carrier_rmta_origin_span"></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Active:</strong> <span id="carrier_rmta_active_span"></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Cut Off:</strong> <span id="carrier_rmta_cut_off_span"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="#" id="export_remotearea" data-export_carrier_id="" class="btn blue btn-sm pull-right margin-left-5"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</a>
                                <a  id="btnSubmitImport" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i>&nbsp;Import</a>
                            </div>
                            <div id="hidden_frm" style="display: none;">
                                <form name="hiddenForm" id="hiddenForm" action="" method="POST" enctype='multipart/form-data'>
                                    <input type="hidden" name="carrier_id_hidden" value="" id="carrier_id_hidden"/>
                                    <input type="hidden" name="action" value="download" />
                                    <input type="hidden" name="download_type" value="carrier" />
                                    <input type="file" name="import_csv" id="import_csv" />
                                </form>
                            </div>
                        </div>
                        <div class="row carrier_model_csv">
                            <hr class="" />
                        </div>
                        <div class="row" id="message_download_csv" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-danger"></div>
                            </div>
                        </div>
                        <div id="remotearea_change_html">Please wait data loading... 
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <a  class="btn btn-info add_more_btn">
                                    <i class="fa fa-plus"></i> Add More
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" class="btn green save_service_agent_data">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Model for CSV Upload Carrier-->
        <div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Select CSV File</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
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
                                                    <input type="file" name="file_in" id="file_in">
                                                </span>
                                                <a  class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv" class="btn green">Upload</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Model for CSV Upload Services-->
        <div class="modal fade" id="csv_upload_service" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Select CSV File</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
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
                                                    <input type="file" name="file_in_service" id="file_in_service">
                                                </span>
                                                <a  class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv_service" data-import_carrier_id="" class="btn green">Upload</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        
        
        
        <!--Model for carrier details-->
        <div class="modal fade" role="dialog" id="carrier-gaz-documents-popup" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Carrier  Gazetier for <span id='gaz-carrier-name'></span></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="carrier_id_for_gaz" name="carrier_id_for_gaz">

                        <div class="row" >
                            <div class="col-md-12"  id="model-gaz-content-display">
                                <!--  ########################################################### -->
                                <?php ?>
                                <div class="row">
                                    <form id="fileupload" action="carrier.php?func=UPLOAD_GAZZETIER_FILES" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" id="carrier_name_for_gaz" name="carrier_name_for_gaz">
                                        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->

                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="col-sm-3 hidden">
                                                    <div class="form-group">
                                                        <label>Gazetier Name</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-addon"> <i class="fa fa-key"></i></span>
                                                            <input type="text" class="form-control" name="gazetier_name" id="gazetier_name" value="<?php echo @$gazetier_name; ?>" size="" maxlength="35" rel="tooltip" data-original-title="Gazetier For Carrier" placeholder="Gazetier For Carrier" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="fileupload-buttonbar">
                                            <div class="col-sm-9 center">
                                                <!-- The fileinput-button span is used to style the file input field as button -->
                                                <span class="btn green fileinput-button">
                                            <i class="fa fa-plus"></i>
                                            <span> Add files... </span>
                                            <input type="file" name="files[]" multiple=""> </span>
                                                <button type="submit" class="btn blue start">
                                                    <i class="fa fa-upload"></i>
                                                    <span> Start upload </span>
                                                </button>
                                                <!--<button type="reset" class="btn warning cancel">
                                                    <i class="fa fa-ban-circle"></i>
                                                    <span> Cancel upload </span>
                                                </button>-->
                                                <button type="button" class="btn warning process-uploaded-files">
                                                    <i class="fa fa-ban-circle"></i>
                                                    <span> Process Uploaded Files </span>
                                                </button>
                                                <!--<button type="button" class="btn red delete">
                                                    <i class="fa fa-trash"></i>
                                                    <span> Delete </span>
                                                </button>
                                                <input type="checkbox" class="toggle">-->
                                                <!-- The global file processing state -->
                                                <span class="fileupload-process"> </span>
                                            </div>
                                            <!-- The global progress information -->
                                            <div class="col-lg-5 fileupload-progress fade">
                                                <!-- The global progress bar -->
                                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar progress-bar-success" style="width:0%;"> </div>
                                                </div>
                                                <!-- The extended global progress information -->
                                                <div class="progress-extended"> &nbsp; </div>
                                            </div>
                                        </div>
                                            </div>
                                        </div>
                                        <!-- The table listing the files available for upload/download -->
                                        <table role="presentation" class="table table-striped clearfix">
                                            <tbody class="files"> </tbody>
                                        </table>
                                    </form>
                                </div>
                                <div class="row">
                                    <div id="append_service_gaz">
                                    </div>
                                </div>
                            </div>
                        </div>       
                    </div>
                    <!--<div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>-->
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>




        <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <script id="template-upload" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-upload fade">
                            <td>
                                <span class="preview"></span>
                            </td>
                            <td>
                                <p class="name">{%=file.name%}</p>
                                <strong class="error label label-danger"></strong>
                            </td>
                            <td>
                                <p class="size">Processing...</p>
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                            </td>
                            <td> {% if (!i && !o.options.autoUpload) { %}
                                <button class="btn blue start" disabled>
                                    <i class="fa fa-upload"></i>
                                    <span>Start</span>
                                </button> {% } %} {% if (!i) { %}
                                <button class="btn red cancel">
                                    <i class="fa fa-ban"></i>
                                    <span>Cancel</span>
                                </button> {% } %} </td>
                        </tr> {% } %} </script>
        <!-- The template to display files available for download -->
        <script id="template-download" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-download fade">
                            <td>
                                <span class="preview"> {% if (file.thumbnailUrl) { %}
                                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery>
                                        <img src="{%=file.thumbnailUrl%}">
                                    </a> {% } %} </span>
                            </td>
                            <td>
                                <p class="name"> {% if (file.url) { %}
                                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl? 'data-gallery': ''%}>{%=file.name%}</a> {% } else { %}
                                    <span>{%=file.name%}</span> {% } %} </p> {% if (file.error) { %}
                                <div>
                                    <span class="label label-danger">Error</span> {%=file.error%}</div> {% } %} </td>
                            <td>
                                <span class="size">{%=o.formatFileSize(file.size)%}</span>
                            </td>
                            <td> {% if (file.deleteUrl) { %}
                                <button class="btn red delete btn-sm" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}" {% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}' {% } %}>
                                    <i class="fa fa-trash-o"></i>
                                    <span>Delete</span>
                                </button>
                                <input type="checkbox" name="delete" value="1" class="toggle"> {% } else { %}
                                <button class="btn yellow cancel btn-sm">
                                    <i class="fa fa-ban"></i>
                                    <span>Cancel</span>
                                </button> {% } %} </td>
                        </tr> {% } %} </script>
        <!-- END PAGE BASE CONTENT -->

        
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

    public function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                getAllCarrierList();
                $('input').tooltip();
                $('select').tooltip();
                $('a').tooltip();
                //$('.anchorTooltip').tooltip();

                $(document).on('click', '.carrier_log_detail_link', function () {
                    var e = $(this);
                    var cid = e.data('cid');
                    var url = e.data('poload');
                    $.post(url, {func: 'get_log_details', cid: cid}, function (d) {
                        $("#model-content-display").html(d);
                        $("#detail-log-popup").modal('show');

                    });
                });
                $("#btnSave").click(function () {
                    $("#form_action").val("save");
                    var group = $(".remotearea_group").serialize();
                    var charges = $(".remotearea_charges").serialize();
                    $("#charges_serialize").val(charges);
                    $("#group_serialize").val(group);
                    $("#remotearea_charges_link").attr("data-carrier_id", "");
                    $("#adminForm").submit();
                });
                $("#btnCancel").click(function () {
                    $("#form_action").val("cancel");
                    location.reload(true);
                    //$("#adminForm").submit();
                });
                $(document).on('click', '.delete_carrier', function () {
                    var e = $(this);
                    var status = 'delete';
                    var carrier_id = e.data('carrierid');
                    swal({
                        title: "<?php echo Translation::GetCaption("CARRIER_DELETE_MESSAGE") ?>",
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
                                    $.ajax({
                                        type: "POST",
                                        url: "carrier.php",
                                        data: {func: "CHANGE_STATUS_CARRIER", carrier_id: carrier_id, status: status},
                                        dataType: "json",
                                        success: function (data) {
                                            if (status == 'delete')
                                            {
                                                getAllCarrierList();
                                            }
                                            swal(
                                                    data.STATUS,
                                                    data.MESSAGE,
                                                    data.STATUS.toLowerCase()
                                                    )
                                        },
                                        error: function () {
                                            $('#res_message').removeClass('alert-success').addClass('alert-danger');

                                        }
                                    });
                                }
                            });
                });


                $(document).on('click', '.process-uploaded-files', function () {

                    var carrierName =   $('#carrier_name_for_gaz').val();
                    var return_status =   "";


                    $.ajax({
                        type: "POST",
                        url: "carrier.php",
                        data: {action: "CHECK_GAZ_FILES", carrier_name: carrierName},
                        dataType: "json",
                        success: function (data) {
                            if (data.status == 'SUCCESS')
                            {
                                $.each(data.FILES, function(file_index, file_name) {
                                        return_status += processGazFiles(file_name, carrierName)+" \r\n";
                                });
                            }

                                swal(
                                    "File Processed",
                                    return_status,
                                    'success'
                                );
                        },
                        error: function () {
                            $('#res_message').removeClass('alert-success').addClass('alert-danger');

                        }
                    });
                });

                function processGazFiles(file_name, carrierName)
                {
                    var returnmessga   =   '';
                    $.ajax({
                        type: "POST",
                        url: "carrier.php",
                        data: {action: "PROCESSING_GAZ_FILE", carrier_name: carrierName,file_name:file_name},
                        dataType: "json",
                        async:false,
                        success: function (data) {
                            returnmessga =  data.message;
                        },
                        error: function () {
                            swal(
                                "File Processed Error",
                                data.message,
                                'error'
                            );
                        }
                    });
                    return returnmessga;
                }

                $(document).on('click', '.carrier-logs', function () {
                    var e = $(this);
                    var carrier_name = e.data('carrier_name');
                    var courier = e.data('courierid');
                    var url = e.data('carrierload');
                    var action = e.data('action');

                    $("#carrier_name_logs").html(carrier_name);
                    $.post(url, {func: action, carrier_name: carrier_name, courier: courier}, function (d) {
                        $("#carrier-logs-display").html(d);
                    });
                });

                $(document).on('click', '.event-servicecountry', function () {
                    $("#service-country-content-display").html('Please wait, we are dealing with your request.');
                    var e = $(this);

                    var serviceid = e.data('serviceid');
                    var servicename = e.data('servicename');
                    var url = e.data('carrierload');
                    var action = e.data('action');

                    $("#serviceCountryName").html(servicename);
                    $.post(url, {func: action, serviceid: serviceid, servicename: servicename}, function (d) {
                        $("#service-country-content-display").html(d);
                        $("#service-country-popup").modal('show');
                    });
                });
                $(document).on('click', '.carrier_status', function () {

                    var e = $(this);
                    var status = e.data('status');
                    var carrier_name = e.data('carrier_name');
                    var carrier_id = e.data('carrier_id');
                    var confirmationMessage = "<?php echo Translation::GetCaption("CARRIER_CONFIRM_MESSAGES") ?>"
                    confirmationMessage = confirmationMessage.replace("#name#", carrier_name);
                    confirmationMessage = confirmationMessage.replace("#status#", status);
                    confirmationMessage = confirmationMessage.replace("#status#", status);
                    swal({
                        title: confirmationMessage,
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
                                if (isConfirm)
                                {
                                    $.ajax({
                                        type: "POST",
                                        url: "carrier.php",
                                        data: {func: "CHANGE_STATUS_CARRIER", carrier_name: carrier_name, carrier_id: carrier_id, status: status},
                                        dataType: "json",
                                        success: function (data) {
                                            if (status == 'active')
                                            {
                                                e.removeClass('ribbon-color-danger').addClass('ribbon-color-success');
                                                e.data('status', 'inactive')
                                                e.html('Active');
                                            } else if (status == 'inactive')
                                            {
                                                e.removeClass('ribbon-color-success').addClass('ribbon-color-danger');
                                                e.data('status', 'active')
                                                e.html('Inactive');
                                            }
                                            swal(
                                                    data.STATUS,
                                                    data.MESSAGE,
                                                    data.STATUS.toLowerCase()
                                                    )
                                        },
                                        error: function () {
                                            $('#res_message').removeClass('alert-success').addClass('alert-danger');

                                        }
                                    });

                                }

                            });

                });

                $(document).on('click', '.event-carrierload', function () {
                    $("#message_download_csv_service").hide();
                    var carrierId = $(this).attr("data-courierid");
                    var serviceDisplayName = $(this).data("carrier_display_name");
                    $("#carrier-content-display-wait").html('Please wait, we are dealing with your request.');
                    var e = $(this);

                    var carrier_name = e.data('carrier_name');
                    var courier = e.data('courierid');
                    var url = e.data('carrierload');
                    var action = e.data('action');
                    var courierimage = e.data('courierimage');


                    var cut_of_time = e.data('cut_of_time');
                    var carrier_display_name = e.data('carrier_display_name');
                    var carrier_status = e.data('carrier_status');
                    var carrier_country = e.data('carrier_country');
                    var carrier_country_iso = e.data('carrier_country_iso');
                    var carrier_remotearea_check = e.data('carrier_remotearea_check');

                    $("#carrier_name").html(carrier_name);
                    $.post(url, {func: action, carrier_name: carrier_name, courier: courier, courierimage: courierimage,
                        carrier_display_name: carrier_display_name, cut_of_time: cut_of_time, carrier_status: carrier_status, carrier_country_iso: carrier_country_iso, carrier_country: carrier_country, carrier_remotearea_check: carrier_remotearea_check
                    }, function (d) {
                        $("#carrier-content-display").html(d);
                        $("#export_remotearea_service").data("carrier_id", carrierId);
                        $("#console_window_service").hide();
                        $('a').tooltip();
                    });
                });
                $(document).on('click', '.carrier_details', function () {
                    $("#carrier-detail-popup").modal('show');
                    var carrierName = $(this).attr('data-carrier-name');
                    var cutOfTime = $(this).attr('data-cut_of_time');
                    var carrierDisplayName = $(this).attr('data-carrier_display_name');
                    var status = $(this).attr('data-carrier_status');
                    var statusHtml = '';
                    if (status == '1') {
                        statusHtml = '<span class="label label-sm label-success"> Approved </span>';

                    } else {
                        statusHtml = '<span class="label label-sm label-danger"> Blocked </span>';
                    }
                    var country = $(this).attr('data-carrier_country');
                    var carrierService = $(this).attr('data-carrier_service');
                    var html = '<tr><td rowspan="2"> ' + carrierName + ' </td><td> ' + carrierDisplayName + ' </td><td> ' + cutOfTime + ' </td><td> ' + carrierService + ' </td><td> ' + country + ' </td><td>' + statusHtml + '</td></tr>';
                    $('#model-carrier-content-display tbody').html(html);
                });
                //        Handle User File upload
                $(document).on('click', '#upload_file', function () {
                    var fileType = $("#document_name").val();
                    var fileTypeText = $("#document_name option:selected").text();
                    if ($.trim(fileType) == "") {
                        swal("", "Please Select File Type", "info");
                        return false;
                    }
                    var filename = $("#file_name").val();
                    if (filename == "") {
                        swal("", "Please Select File", "info");
                        return false;
                    } else {
                        var file_data = $('#file_name').prop('files')[0];
                        var form_data = new FormData();
                        var carrierId = $("#carrier_id_for_doc").val();
                        form_data.append('file_type', fileType);
                        form_data.append('file_name', filename);
                        form_data.append('carrier_id', carrierId);
                        form_data.append('file_type_text', fileTypeText);
                        form_data.append('action', "upload_carrier_doc");
                        form_data.append('carrier_doc_file', file_data);
                        $.ajax({
                            url: "carrier.php", // point to server-side PHP script
                            dataType: 'html', // what to expect back from the PHP script, if anything
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (php_script_response) {
                                if (php_script_response == "0") {
                                    swal("Invalid file type", "You can only upload gif,jpeg,jpg,png and pdf file", "error");
                                } else {
                                    $("#append_service_doc").append(php_script_response);
                                    $("#document_name").val($("#document_name option:first").val());
                                    $("#document_name").selectpicker('refresh');
                                    $("a.fileinput-exists").click();
                                }
                            }
                        });
                    }
                });
                //Handle Document Remove functioanlity
                $(document).on('click', '.remove_doc', function () {
                    var el = $(this);
                    $('#carrier-documents-popup').modal('hide');
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
                                    var serviceDocId = el.data("doc_id");
                                    var carrierId = $("#carrier_id_for_doc").val();
                                    $.ajax({
                                        url: 'carrier.php',
                                        type: 'POST',
                                        data: {action: 'remove_carrier_doc', doc_id: serviceDocId, carrier_id: carrierId},
                                        headers: {
                                        },
                                        success: function () {
                                            $("#ser_doc_" + serviceDocId).remove();
                                        },
                                        error: function (xhr, status, error) {

                                        }
                                    });

                                }
                                $('#carrier-documents-popup').modal('show');
                            });

                });
            });
            
            /*******************************************************/
            
            $(document).on('click', '.carrier_gaz_document_upload', function () {

                var carrier_id = $(this).attr('data-carrier_id');
                var carrier_name = $(this).attr('data-carrier_name');

                $('#carrier_name_for_gaz').val(carrier_name);
                $('#carrier_id_for_gaz').val(carrier_id);
                $('#carrier-gaz-documents-popup').modal('show');
                $('#gaz-carrier-name').html(carrier_name);
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {action: "GET_CARRIER_DOCUMENTS", carrier_id: carrier_id},

                    success: function (data) {
                        $("#carrier-gaz-documents-popup #append_service_gaz").html(data);
                    },
                    error: function () {
                        $('#carrier-gaz-documents-popup #res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#carrier-gaz-documents-popup #res_message').html("Some error occurred");
                        $('#carrier-gaz-documents-popup #res_message').show();
                        $('html, body').animate({scrollTop: 0}, 0);
                    }
                });
            });
            
            /*******************************************************/
            $(document).on('click', '.carrier_document_upload', function () {

                var carrier_id = $(this).attr('data-carrier_id');
                var carrier_name = $(this).attr('data-carrier_name');
                $('#carrier_id_for_doc').val(carrier_id);
                $('#carrier-documents-popup').modal('show');
                $('#doc-carrier-name').html(carrier_name);
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {action: "GET_CARRIER_DOCUMENTS", carrier_id: carrier_id},

                    success: function (data) {
                        $("#append_service_doc").html(data);
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                        $('html, body').animate({scrollTop: 0}, 0);
                    }
                });
            });
        //Edit carrier functionality        
            $(document).on('click', '.carrier_edit', function () {
                var carrier_id = $(this).attr('data-carrier_id');
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {edit_carrier: "editEvent", carrier_id: carrier_id},
                    dataType: "json",
                    success: function (data) {
                        $("#carrier").val(data.carrier);
                        $("#carrier_display_name").val(data.carrier_display_name);
                        $("#cut_off_time").val(data.cut_off_time);
                        $("#id").val(data.carrier_tbl_id);
                        var select2Parentid = $("#parentid");
                        select2Parentid.val(data.carrier_id).trigger('change');
                        $("#country").val(data.country).trigger('change');
                        $("#currency_code").val(data.currency_code).trigger('change');
                        if (data.status === "1") {
                            $('.carrier_chk').attr('checked', true);
                            $('.carrier_chk').bootstrapSwitch('state', true);
                        } else {
                            $('.carrier_chk').attr('checked', false);
                            $('.carrier_chk').bootstrapSwitch('state', false);
                        }

                        if (data.is_gazetteer === "1") {
                            $('#is_gazetteer').attr('checked', true);
                            $('#is_gazetteer').bootstrapSwitch('state', true);
                        } else {
                            $('#is_gazetteer').attr('checked', false);
                            $('#is_gazetteer').bootstrapSwitch('state', false);
                        }



                        if (data.on_contract === "1") {
                            $('#on_contract').attr('checked', true);
                            $('#on_contract').bootstrapSwitch('state', true);
                        } else {
                            $('#on_contract').attr('checked', false);
                            $('#on_contract').bootstrapSwitch('state', false);
                        }
                        if (data.zone_base === "1") {
                            $('#zone_base').attr('checked', true);
                            $('#zone_base').bootstrapSwitch('state', true);
                        } else {
                            $('#zone_base').attr('checked', false);
                            $('#zone_base').bootstrapSwitch('state', false);
                        }if (data.zone_type === "country") {
                            $('#zone_type').attr('checked', true);
                            $('#zone_type').bootstrapSwitch('state', true);
                        } else {
                            $('#zone_type').attr('checked', false);
                            $('#zone_type').bootstrapSwitch('state', false);
                        }
                        if (data.is_pallet === "1") {
                            $('#is_pallet').attr('checked', true);
                            $('#is_pallet').bootstrapSwitch('state', true);
                        } else {
                            $('#is_pallet').attr('checked', false);
                            $('#is_pallet').bootstrapSwitch('state', false);
                        }
                        
                        if (data.is_reconcile === "1") {
                            $('#is_reconcile').attr('checked', true);
                            $('#is_reconcile').bootstrapSwitch('state', true);
                        } else {
                            $('#is_reconcile').attr('checked', false);
                            $('#is_reconcile').bootstrapSwitch('state', false);
                        }
                        if (data.remotearea_check === "c") {
                            $('#remotearea').attr('checked', true);
                            $('#remotearea').bootstrapSwitch('state', true);
                            //$("#remotearea_charges_div a").attr("data-carrier_id",carrier_id);
                            $("#remotearea_charges_div a").attr("data-carrier_id", carrier_id);
                            $("#remotearea_charges_div a").attr("data-carrier_name", data.carrier);
                            $("#remotearea_charges_div a").attr("data-carrier_active", data.status);
                            $("#remotearea_charges_div a").attr("data-carrier_cut_off_time", data.cut_off_time);
                            $("#remotearea_charges_div a").attr("data-carrier_logo", data.logo);
                            $("#remotearea_charges_div a").attr("data-country_name", data.country_name);
                            $("#remotearea_charges_div a").attr("data-country_iso", data.country_iso);

                            $("#remotearea_charges_div").show();
                        } else {
                            $("#remotearea").attr("data-ajax_set", 'ok');
                            $('#remotearea').attr('checked', false);
                            $('#remotearea').bootstrapSwitch('state', false);
                            $("#remotearea_charges_div a").attr("data-carrier_id", "");
                            $("#remotearea_charges_div").hide();
                        }
                        $(".fileinput-preview img").attr('src', data.logo);
                        $('html, body').animate({scrollTop: 0}, 0);
                        $("#parent_clone").html(data.remotearea_chares);
                        $('.remotearea_group').select2();
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                        $('html, body').animate({scrollTop: 0}, 0);
                    }
                });

            });
        //Handle Model for Remotearea Charges
            $(document).on('click', '#remotearea_charges_link', function () {
                $(".save_service_agent_data").attr("data-is_carrier", 'Y');
                $("#console_window").hide();
                $("#message_download_csv").hide();
                $(".carrier_model_csv").show();
                var carrier_id = parseInt($(this).attr("data-carrier_id"));
                var carrier_name = $(this).attr("data-carrier_name");
                var carrier_logo = $(this).attr("data-carrier_logo");
                $("#export_remotearea").attr('data-export_carrier_id', carrier_id);

                var country_name = $(this).attr("data-country_name");
                var country_iso = $(this).attr("data-country_iso");
                var carrier_active = $(this).attr("data-carrier_active");
                var carrier_cut_off_time = $(this).attr("data-carrier_cut_off_time");
                var carrier_active_html = '<span class="label label-sm label-danger"> No </span>';
                if (carrier_active == 1)
                    carrier_active_html = '<span class="label label-sm label-success"> Yes </span>';

                $("#remotearea_change_model #carrier_rmta_origin_span").html('<img src="../assets/global/img/flags/' + country_iso + '.png"> ' + country_name);
                $("#remotearea_change_model #carrier_rmta_active_span").html(carrier_active_html);
                $("#remotearea_change_model #carrier_rmta_cut_off_span").html(carrier_cut_off_time);



                $("#remotearea_change_model .modal-header h4.modal-title").html('<img class="margin-right-5" src="' + carrier_logo + '" alt="' + carrier_name + '" popover-trigger="mouseenter" />' + carrier_name + " Remoteareas");




                $("#remotearea_change_model").modal("show");
                remoteareaModel(carrier_id, 'carrier');
            });
        //Handle service remotearea model
            $(document).on('click', '.remotearea_services_charges', function () {
                $(".save_service_agent_data").attr("data-is_carrier", 'N');
                $("#console_window").hide();
                $(".carrier_model_csv").hide();
                var carrierId = $(this).attr("data-carrier-id");
                var serviceId = $(this).attr("data-service-id");
                var carrier_name = $(this).attr("data-carrier_name");
                var carrier_logo = $(this).attr("data-carrier_logo");
                var service_name = $(this).attr("data-service_name");
                $("#remotearea_change_model .modal-header h4.modal-title").html('<img class="margin-right-5" src="' + carrier_logo + '" alt="' + carrier_name + '" popover-trigger="mouseenter" />' + carrier_name + " services [" + service_name + "] remoteareas");
                $("#remotearea_change_model").modal("show");
        //    $("#carrier_id_hidden").val(serviceId);
                $(".save_service_agent_data").attr("data-service-id", serviceId);
                $(".save_service_agent_data").attr("data-carrier-id", carrierId);

                remoteareaModel(carrierId, "service", serviceId);
            });
        //Handle remotearea export
            $(document).on('click', '#export_remotearea', function () {
                var carrier_id = parseInt($(this).attr('data-export_carrier_id'));
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {action: "check_download_remoteareas", carrier_id: carrier_id},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == "success") {
                            $("#carrier_id_hidden").val(carrier_id);
                            $("#hiddenForm").submit();
                            $('#remotearea_change_model').modal('hide');
                        } else {
                            $('#message_download_csv_service div.alert').addClass('alert-danger').removeClass('alert-success');
                            $("#message_download_csv div.alert").html("");
                            $("#message_download_csv div.alert").html("There is no record found to download CSV");
                            $("#message_download_csv").show();
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            });
        //Handle remotearea carrier import
            $(document).on('click', '#import_remotearea', function () {
                $("#import_csv").click();
            });
            $(document).on('click', '#btnSubmitImport', function () {
                //$("#file_in").val("");
                $("a.fileinput-exists").click();
                $("#csv_upload").modal('show');
            });
        //Handle import service model
            $(document).on('click', '#btnSubmitImport_service', function () {
                var carrier_id = $(this).data("import_carrier_id");
                $("#upload_csv_service").data("import_carrier_id", carrier_id);
                //$("#file_in_service").val("");
                $("a.fileinput-exists").click();
                $("#csv_upload_service").modal('show');
            });
        //handle services export
            $(document).on('click', '#export_remotearea_service', function () {
                var carrierId = $(this).data("carrier_id");
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {action: "check_download_remoteareas_service", carrier_id: carrierId},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == "success") {
                            $("#carrier_id_hidden_service").val(carrierId);
                            $("#hidden_form_service").submit();
                        } else {
                            $('#message_download_csv_service div.alert').addClass('alert-danger').removeClass('alert-success');
                            $("#message_download_csv_service div.alert").html("");
                            $("#message_download_csv_service div.alert").html("There is no record found to download CSV");
                            $("#message_download_csv_service").show();
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            });
        //Handle Import Csv carrier
            $(document).on('click', '#upload_csv', function () {
                $('#console_window').show();
                $('#console_window').html('');
                $('#console_window').html("Uploading CSV File....<br />");
                var file_data = $('#file_in').prop('files')[0];
                $('#csv_upload').modal('hide');
                var form_data = new FormData();
                var carrierId = "";
                carrierId = $("#export_remotearea").attr("data-export_carrier_id");
                form_data.append('csv_file', file_data);
                form_data.append('func', 'upload_csv_file');
                form_data.append('carrier_id', carrierId);
                $.ajax({
                    url: "carrier.php",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#console_window').append(response.message);
                            grid.getDataTable().ajax.reload();
                        } else {
                            $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                        }
                    }
                });
                return false;
            });
        //Handle Import Csv Service
            $(document).on('click', '#upload_csv_service', function () {
                $('#console_window_service').show();
                $('#console_window_service').html('');
                $('#console_window_service').html("Uploading CSV File....<br />");
                var file_data = $('#file_in_service').prop('files')[0];
                $('#csv_upload_service').modal('hide');
                var form_data = new FormData();
                var carrierId = $(this).data("import_carrier_id");
                form_data.append('csv_file', file_data);
                form_data.append('func', 'upload_csv_file_service');
                form_data.append('carrier_id', carrierId);
                $.ajax({
                    url: "carrier.php",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#console_window_service').append(response.message);
                            grid.getDataTable().ajax.reload();
                        } else {
                            $('#console_window_service').append('<span style="color:red;">' + response.message + '</span><br />');
                        }
                    }
                });
                return false;
            });
            function remoteareaModel(carrier_id, modelType = "carrier", serviceId) {
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {action: "get_remotearea_change", carrier_id: carrier_id, model_type: modelType, service_id: serviceId},
                    dataType: "json",
                    success: function (data) {
                        if (parseInt($(".save_service_agent_data").attr("data-loaded-id")) != parseInt(carrier_id)) {
                            $("#remotearea_change_html").html("");
                            $("#remotearea_change_html").html(data.remotearea_chares);
                            $(".remotearea_group").select2();
                        }
                    },
                    error: function () {

                    }
                });
            }
        // Handle carriers download excel
            $("#export_carriers_excel").click(function () {
                $("#frm_export_carriers_excel").submit();
            });


        //Handle remove button functionality for remotearea
            $(document).on('click', '.show_remove_btn', function () {
                var current_index = $(this).attr('data-index-of-remove');
                swal({
                    title: "Are you sure you want to remove this?",
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
                                $('.append_here [data-index-agent="' + current_index + '"]').remove();
                            }
                        });
            });

        //Handle Model for Remotearea Charges 
            $(document).on('click', '.add_more_btn', function () {
                var index_of_agent = $(".show_remove_btn").map(function () {
                    return $(this).data('index-of-remove');
                }).get();//get all data values in an array
                var highest_index_of_agent = Math.max.apply(Math, index_of_agent);//find the highest value from them
                highest_index_of_agent = parseInt(highest_index_of_agent) + 1;
                $(".clone_div").children().clone().appendTo(".append_here");
                $('.append_here .row').last().attr('data-index-agent', highest_index_of_agent);
                $('.append_here .row .show_remove_btn').last().attr('data-index-of-remove', highest_index_of_agent);
                $('.append_here .row .show_remove_btn').show();
                setInputFeilds(highest_index_of_agent);
            });
        //Handle input feilds of model for remotearea
            function setInputFeilds(id) {
                $('.append_here [data-index-agent="' + id + '"] .remotearea_group').next().remove();
                $('.append_here [data-index-agent="' + id + '"] :text').val("");
                $('.append_here [data-index-agent="' + id + '"] .remotearea_group').attr("name", 'group[]');
                var select2Parentid = $('.append_here [data-index-agent="' + id + '"] .remotearea_group').select2();
                select2Parentid.val("").trigger('change');
            }
        //Handle Save button for model for remotearea
            $(document).on('click', '.save_service_agent_data', function () {
                var $nonempty = $('.validate_check').filter(function () {
                    if (!$(this).val()) {
                        $(this).parents(".input-group").css('border', '1px solid red');
                    } else {
                        $(this).parents(".input-group").css('border', '0px');
                    }
                    return !$(this).val();
                });
                if ($nonempty.length == 0) {
                    var currentId = $("#remotearea_charges_link").attr("data-carrier_id");
                    $(this).attr("data-loaded-id", currentId);
                    if ($(".save_service_agent_data").attr('data-service-id') && $(".save_service_agent_data").attr('data-is_carrier') != "Y") {
                        saveServiceRemotearea();
                        $("#remotearea_change_model").modal("hide");
                    }
                    if ($(".save_service_agent_data").attr('data-is_carrier') == "Y") {
                        var check = checkit();
                        if (check == true) {
                            $("#remotearea_change_model").modal("hide");
                        }
                    }
                }
            });
            // check the zone base
            $('#zone_base').on('switchChange.bootstrapSwitch', function (event, state) {
                if ($(this).prop('checked') == true) {
                    $('#zone_type_box').show();
                } else {
                    $('#zone_type_box').hide();
                }
            });
        //Check if remoteareas assigned duplicate group values
            function checkit() {
                var checker = [];
                var is_ok = true;
                $(".remotearea_group").each(function () {
                    var selection = $(this).val();
                    if (checker[selection]) {
                        //if the property is defined, then we've already encountered this value
                        swal("", "Can't duplicate groups", "error");
                        is_ok = false;
                        return;
                    } else {
                        checker[selection] = true;
                    }
                });
                return is_ok;
            }
        //Save remotearea if services
            function saveServiceRemotearea() {
                var serviceId = $(".save_service_agent_data").attr("data-service-id");
                var carreirId = $(".save_service_agent_data").attr("data-carrier-id");
                var group = $(".remotearea_group").serialize();
                var charges = $(".remotearea_charges").serialize();
                var fromWeight = $(".remotearea_from_weight").serialize();
                var toWeight = $(".remotearea_to_weight").serialize();
                var formula = $(".remotearea_formula").serialize();
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {action: "save_service_remotearea", service_id: serviceId, carreir_id: carreirId, group: group, charges: charges, from_weight: fromWeight, to_weight: toWeight, formula: formula},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 'success') {
                            $('#message_download_csv_service div.alert').addClass('alert-success').removeClass('alert-danger');
                            $('#message_download_csv_service div.alert').html("");
                            $('#message_download_csv_service div.alert').html(data.message);
                            $('#message_download_csv_service').show();
                            $('html, body').animate({scrollTop: 0}, 0);
                            //$("#carrier-service-popup").modal('hide');
                        }
                    },
                    error: function () {

                    }
                });
            }
        //Handle remmotarea button hide show
            $('#remotearea').on('switchChange.bootstrapSwitch', function (event, state) {
                var chkAjax = $(this).attr("data-ajax_set");
                if (parseInt($("#id").val()) > 0) {
                    if (state == true) {
                        $("#remotearea_charges_div").show();
                    } else if (chkAjax != "ok") {
                        swal({
                            title: "Do you want to copy the remotearea charges of this carrier to all serives",
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
                                        $("#copy_carrier").val("copy");
                                    } else {
                                        $("#copy_carrier").val("");
                                    }
                                });
                        $("#remotearea_charges_div").hide();
                    }
                    $("#remotearea").attr("data-ajax_set", '');
                }

            });
        //Handle service remotearea charges functionality
            function serviceRemotearea(state, serviceId, serviceName) {
                if (state == true) {
                    $('*[data-service-id="' + serviceId + '"]').show();
                } else {
                    swal({
                        title: "Are you sure you want to delete all remoteareas for " + serviceName + " service",
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
                                    $.ajax({
                                        type: "POST",
                                        url: "carrier.php",
                                        data: {action: "delete_services_remoteareas", service_id: serviceId},
                                        dataType: "json",
                                        success: function (data) {
                                            if (data.status == "success") {
                                                $('#message_download_csv_service div.alert').addClass('alert-success').removeClass('alert-danger');
                                                $("#message_download_csv_service  div.alert").html("");
                                                $("#message_download_csv_service  div.alert").html(data.message);
                                                $("#message_download_csv_service").show();
                                            } else {
                                                $("#message_download_csv_service  div.alert").html(data.message);
                                                $("#message_download_csv_service").show();
                                            }
                                        },
                                        error: function () {
                                            alert('error handing here');
                                        }
                                    });
                                }
                            });
                    $('*[data-service-id="' + serviceId + '"]').hide();
                }
            }
        //Send ajax request to save Data
        //accept form Data
            function addCarrier(formData) {
                var carrier = $("#carrier").val();
                var carrier_display_name = $("#carrier_display_name").val();
                var cut_off_time = $("#cut_off_time").val("");
                var cut_off_time = $("#currency_code").val();
                var country = $("#country").val();
                if ($.trim(carrier) == '' || $.trim(carrier_display_name) == '') {
                    $('#res_message').removeClass('alert-success').addClass('alert-danger');
                    $('#res_message').html("<?php echo formatMessages(ERROR_REQUIRED_FILEDS_EMPTY); ?>");
                    $('#res_message').show();
                    $('html, body').animate({scrollTop: 0}, 0);
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: formData,
                    dataType: "json",
                    async: false,
                    success: function (data) {
                        $('#res_message').removeClass('alert-danger');
                        $('#res_message').removeClass('alert-success');
                        $('#res_message').removeClass('alert-info');
                        if (data.status == 'success') {
                            $('#res_message').addClass('alert-success');
                            $("#carrier").val("");
                            $("#id").val("");
                            $("#carrier_display_name").val("");
                            $("#cut_off_time").val("");
                            $("#currency_code").val("").trigger('change');
                            $("#parentid").val("").trigger('change');
                            //var select2Parentid = $("#parentid").select2();
                            // var select2Country = $("#country").select2();
                            //select2Parentid.val("").trigger('change');
                            $("#country").val("").trigger('change');
                            $(".fileinput-exists-remove").trigger('click');
                            $(".fileinput-preview img").attr('src', '../images/No-image-found.jpg');
                            $("#search_carrier").val("");
                            getAllCarrierList();
                        } else if (data.status == 'info') {
                            $('#res_message').addClass('alert-info');
                        } else {
                            $('#res_message').addClass('alert-danger');
                        }
                        $('#res_message').html(data.message);
                        $('#res_message').show();
                        $('html, body').animate({scrollTop: 0}, 0);
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                        $('html, body').animate({scrollTop: 0}, 0);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        //Handle form submit request        
            $("#adminForm").submit(function () {
                var formData = new FormData(this);
                addCarrier(formData);
                return false;
            });
        //Sreach functionality for Carrier Name        
            $("#search_carrier").on('keyup keypress', function (e) {
                $('.search_carrier_custom').each(function (e) {
                    var current = $.trim($(this).data('name')).toLowerCase();
                    var search_str = $.trim($("#search_carrier").val()).toLowerCase();
                    if (current.indexOf(search_str) >= 0) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        //Get All ajax based carrier List
            function getAllCarrierList() {
                $.ajax({
                    type: "POST",
                    url: "carrier.php",
                    data: {get_all: "carrier_list"},
                    success: function (data) {
                        $('#append_all_carrier_list').html("");
                        $('#append_all_carrier_list').html(data);
                        $('a').tooltip();
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }
        </script>
        <style>
            .panel-footer{
                padding: 10px 5px;
            }
        </style>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
