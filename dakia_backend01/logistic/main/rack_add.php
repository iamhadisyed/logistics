<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Warehouse Add/Update page
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
	'pdfmerger'
], 'labels');
include_classes([
	'tcpdf'
], '3rdparty/tcpdf');
include_classes([
	'consignment.class',
	'consignmentfilter.class',
	'parcel.class',
	'parcelfilter.class',
	'warehouse.class',
	'warehousefilter.class',
	'rack.class',
	'rackfilter.class',
	'rackshelf.class',
	'rackshelffilter.class',
	'lograckshelf.class',
	'lograckshelffilter.class',
	'rackshelfitem.class',
	'rackshelfitemfilter.class'
]);

// set up local page class
class Page extends BasePage {

    private $error_msg = "";
    private $id = NULL;
    private $warehouse_id = NULL;
    private $title = NULL;
    private $short_title = NULL;
    private $rack_rows = NULL;
    private $rack_cols = NULL;
    private $shelf_length = NULL;
    private $shelf_width = NULL;
    private $shelf_height = NULL;
    private $shelf_max_weight = NULL;
    private $is_active = 1;
    private $is_york = 0;
    private $warehouses = NULL;
    private $customer_list = NULL;

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Add Rack'
        );
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_item_detail') {
            $output = array();
            $log_shelf_id = $this->form_vars['log_shelf_id'];
            $logRackShelfObj = new LogRackShelf($log_shelf_id);
            $rack_shelf_id = $logRackShelfObj->getRackShelfId();
            $rack_shelf_item_id = $logRackShelfObj->getRackShelfItemId();
            $customer_id = $logRackShelfObj->getCustomerId();

            $rackShelfItemObj = new RackShelfItem($rack_shelf_item_id);
            $goods_name = $rackShelfItemObj->getGoodsName();
            $description = $rackShelfItemObj->getDescription();
            $weight = $rackShelfItemObj->getWeight();
            $dimension = $rackShelfItemObj->getDimension();

            list($shelf_length, $shelf_width, $shelf_height) = explode("x", $dimension);

            $output['rack_shelf_item_id'] = $rack_shelf_item_id;
            $output['customer_id'] = $customer_id;
            $output['goods_name'] = $goods_name;
            $output['description'] = $description;
            $output['weight'] = $weight;
            $output['dimension'] = $dimension;
            $output['length'] = $shelf_length;
            $output['width'] = $shelf_width;
            $output['height'] = $shelf_height;

            echo json_encode($output);
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_rack_shelf') {
            $rack_id = $this->form_vars['rack_id'];
            echo $this->getRackShelf($rack_id);
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'load_empty_shelfs') {
            $rack_id = $this->form_vars['rack_id'];
            $output = array();
            $RackShelfObj = new RackShelfFilter();
            $RackShelfObj->addFilter("rs.rack_id='" . $rack_id . "' AND rs.is_filled=0");
            $shelfs = $RackShelfObj->getList();
            if (count($shelfs) > 0) {
                foreach ($shelfs as $shelf) {
                    $shelfNo = $shelf->getShelfNo();
                    $output[$shelf->getId()] = $shelfNo + 1;
                }
            }
            echo json_encode($output);
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'shift_rack_item') {
            $old_rack_shelf_id = $this->form_vars['old_rack_shelf_id'];
            $log_rack_shelf_id = $this->form_vars['log_rack_shelf_id'];
            $rack_id = $this->form_vars['rack_id'];
            $new_rack_shelf_id = $this->form_vars['new_rack_shelf_id'];
            $remarks = $this->form_vars['remarks'];

            $LogRackShelfItemObj = new LogRackShelf($log_rack_shelf_id);
            $rackShelfItemId = $LogRackShelfItemObj->getRackShelfItemId();
            $rackShelfItemCustomerId = $LogRackShelfItemObj->getCustomerId();

            // out from current shelf 
            $logRackShelfObj = new LogRackShelf($log_rack_shelf_id);
            $logRackShelfObj->setOutDate(date("Y-m-d H:i:s"));
            $logRackShelfObj->setRemarks($remarks);
            $logRackShelfObj->setOutBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
            $logRackShelfObj->save(true);

            $rackShelfObj = new RackShelf($old_rack_shelf_id);
            $rackShelfObj->setIsFilled(0);
            $rackShelfObj->setUpdatedDate(date("Y-m-d H:i:s"));
            $rackShelfObj->setUpdatedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
            $rackShelfObj->save(true);
            // add item in new shelf

            $logFieldsVal = array('rack_shelf_id' => $new_rack_shelf_id,
                'rack_shelf_item_id' => $rackShelfItemId,
                'customer_id' => $rackShelfItemCustomerId,
                'in_date' => date("Y-m-d H:i:s"),
                'in_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
            );
            $LogShelfItem = new LogRackShelf($logFieldsVal);
            $LogShelfItem->save(true);
            $log_shelf_item_id = $LogShelfItem->getId();
            if (!empty($log_shelf_item_id) && $log_shelf_item_id > 0) {
                $rackShelfObj = new RackShelf($new_rack_shelf_id);
                $rackShelfObj->setIsFilled(1);
                $rackShelfObj->setUpdatedDate(date("Y-m-d H:i:s"));
                $rackShelfObj->setUpdatedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                $rackShelfObj->save(true);
            }
            // Label Info
            $labelInfo = $this->getLabelInfo($new_rack_shelf_id, 'shift');
            $label = new GlOrderPdfReturnRack();
            $label->AddLabelInfo($labelInfo);
            $filename = $label->buildPDFDocuments();
            echo $filename;
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'york_out_rack_item') {
            $rack_id = $this->form_vars['york_rack_id'];
            $rack_shelf_id = $this->form_vars['york_out_rack_shelf_id'];
            $remarks = $this->form_vars['remarks'];
            $tracking_no = trim($this->form_vars['tracking_no_remove']);

            $logRackShelfFilter = new LogRackShelfFilter();
            $logItemIds = $logRackShelfFilter->getLogItemsInShelf($rack_shelf_id);

            if ($tracking_no != '') {
                $rackShelfItemFilter = new RackShelfItemFilter();
                $rackShelfItemFilter->addTrackingNumberFilter($tracking_no);
                $rackShelfItemList = $rackShelfItemFilter->getList();

                if (count($rackShelfItemList) > 0) {
                    $rackShelfItemObj = $rackShelfItemList[0];
                    $logRackShelfObj = new LogRackShelf($id);
                    $logRackShelfObj->setRemarks($remarks);
                    $logRackShelfObj->setOutDate(date("Y-m-d H:i:s"));
                    $logRackShelfObj->setOutBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                    $logRackShelfObj->save(true);

                    $message = $tracking_no . " item have been removed from rack.";
                }
            } else {
                if (count($logItemIds) > 0) {
                    foreach ($logItemIds as $id) {

                        $logRackShelfObj = new LogRackShelf($id);
                        $logRackShelfObj->setRemarks($remarks);
                        $logRackShelfObj->setOutDate(date("Y-m-d H:i:s"));
                        $logRackShelfObj->setOutBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                        $logRackShelfObj->save(true);
                    }

                    $rackShelfObj = new RackShelf($rack_shelf_id);
                    $rackShelfObj->setIsFilled(0);
                    $rackShelfObj->setUpdatedDate(date("Y-m-d H:i:s"));
                    $rackShelfObj->setUpdatedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                    $rackShelfObj->save(true);
                }

                $message = count($logItemIds) . " items have been removed from rack.";
            }



            $out['message'] = $message;
            echo json_encode($out);
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'out_shelf_item') {

            $rack_shelf_id = $this->form_vars['shelf_id'];
            $log_shelf_id = $this->form_vars['log_rack_shelf_id'];
            $remarks = $this->form_vars['remarks'];

            $logRackShelfObj = new LogRackShelf($log_shelf_id);
            $logRackShelfObj->setRemarks($remarks);
            $logRackShelfObj->setOutDate(date("Y-m-d H:i:s"));
            $logRackShelfObj->setOutBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
            $logRackShelfObj->save(true);

            $rackShelfObj = new RackShelf($rack_shelf_id);
            $rackShelfObj->setIsFilled(0);
            $rackShelfObj->setUpdatedDate(date("Y-m-d H:i:s"));
            $rackShelfObj->setUpdatedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
            $rackShelfObj->save(true);

            // Label Info
            $labelInfo = $this->getLabelInfo($rack_shelf_id, 'out');
            $label = new GlOrderPdfReturnRack();
            $label->AddLabelInfo($labelInfo);
            $filename = $label->buildPDFDocuments();
            echo $filename;

            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'edit_rack_item') {

            $rack_shelf_item_id = $this->form_vars['rack_shelf_item_id'];
            $rack_shelf_id = $this->form_vars['rack_shelf_id'];
            $log_rack_shelf_id = $this->form_vars['log_rack_shelf_id'];
            $rack_id = $this->form_vars['rack_id'];
            $customer_id = $this->form_vars['customer_id'];
            $goods_name = $this->form_vars['goods_name'];
            $weight = $this->form_vars['weight'];
            $length = $this->form_vars['length'];
            $width = $this->form_vars['width'];
            $height = $this->form_vars['height'];
            $description = $this->form_vars['description'];
            $dimension = $length . "x" . $width . "x" . $height;

            $updated_date = date("Y-m-d H:i:s");
            $updated_by = (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');

            $rackShelfItemObj = new RackShelfItem($rack_shelf_item_id);
            $rackShelfItemObj->setGoodsName($goods_name);
            $rackShelfItemObj->setDescription($description);
            $rackShelfItemObj->setWeight($weight);
            $rackShelfItemObj->setDimension($dimension);
            $rackShelfItemObj->setUpdatedDate($updated_date);
            $rackShelfItemObj->setUpdatedBy($updated_by);
            $rackShelfItemObj->save(true);

            $LogShelfItemObj = new LogRackShelf($log_rack_shelf_id);
            $LogShelfItemObj->setCustomerId($customer_id);
            $LogShelfItemObj->save(true);
            // Label Info
            $labelInfo = $this->getLabelInfo($rack_shelf_id, 'edit');
            $label = new GlOrderPdfReturnRack();
            $label->AddLabelInfo($labelInfo);
            $filename = $label->buildPDFDocuments();
            echo $filename;
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'york_in_rack_item') {
            $out['status'] = 'success';

            $york_rack_shelf_id = $this->form_vars['york_rack_shelf_id'];

            $dimension = $this->form_vars['length'] . "x" . $this->form_vars['width'] . "x" . $this->form_vars['height'];
            $trackingNumberArray = $this->form_vars["tracking_number"];
            $message = "";
            //echo count($trackingNumberArray);			
            //die;			
            //$count = 0;

            for ($i = 0; $i < count($trackingNumberArray); $i++) {

                $fieldsVal = array(
                    'goods_name' => $this->form_vars['goods_name'][$i],
                    'description' => $this->form_vars['description'][$i],
                    'tracking_number' => $this->form_vars["tracking_number"][$i],
                    'weight' => $this->form_vars['weight'][$i],
                    'dimension' => $this->form_vars['dimension'][$i],
                    'added_date' => date("Y-m-d H:i:s"),
                    'added_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0'),
                    'updated_date' => date("Y-m-d H:i:s"),
                    'updated_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
                );

                $ShelfItemObj = new RackShelfItem($fieldsVal);
                //print_r($ShelfItemObj);
                $ShelfItemObj->save(true);
                $rack_shelf_item_id = $ShelfItemObj->getId();

                if (!empty($rack_shelf_item_id) && $rack_shelf_item_id > 0) {

                    $logFieldsVal = array('rack_shelf_id' => $york_rack_shelf_id,
                        'rack_shelf_item_id' => $rack_shelf_item_id,
                        'customer_id' => $this->form_vars['customer_id'],
                        'in_date' => date("Y-m-d H:i:s"),
                        'in_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
                    );
                    $LogShelfItem = new LogRackShelf($logFieldsVal);
                    $LogShelfItem->save(true);

                    $log_shelf_item_id = $LogShelfItem->getId();
                    if (!empty($log_shelf_item_id) && $log_shelf_item_id > 0) {
                        $rackShelfObj = new RackShelf($rack_shelf_id);
                        $rackShelfObj->setIsFilled(1);
                        $rackShelfObj->setUpdatedDate(date("Y-m-d H:i:s"));
                        $rackShelfObj->setUpdatedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                        $rackShelfObj->save(true);
                        // Label Info

                        /* $labelInfo = $this->getLabelInfo($rack_shelf_id, 'in');            
                          $label = new GlOrderPdfReturnRack();
                          $label->AddLabelInfo($labelInfo);
                          $filename = $label->buildPDFDocuments();
                          echo $filename;
                         */
                    }
                }



                //echo "Rack Shelf Item Id " . $rack_shelf_item_id;
            }

            $message = count($trackingNumberArray) . " items have been added to rack.";

            $out['message'] = $message;
            echo json_encode($out);
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'validate_york_in_rack_item') {
            $out = array();
            $message = '';
            $out['status'] = 'success';
            $york_tracking_no_post = $this->form_vars['york_tracking_no'];

            $york_tracking_no = preg_replace("/\r\n/", "<br />", $york_tracking_no_post);
            $york_tracking_noArr = array_filter(explode('<br />', $york_tracking_no));

            //print_r($york_tracking_noArr);
            //die;


            $license_plate = '';
            $parcelFilterRes = array();
            foreach ($york_tracking_noArr as $tracking_no) {
                $license_plate .= ($license_plate != '' ? ',' : '') . "'" . $tracking_no . "'";
            }
            if ($license_plate != '') {
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addLicensePlateFilterIn($license_plate);
                $parcelFilterRes = $parcelFilter->getColumnList('consignment_id, tracking_number, description, height, width, length, weight');
            }
            $foundTrackingNo = array();
            $notFoundTrackingNo = array();
            $itemInputs = '';

            if (!empty($parcelFilterRes)) {
                foreach ($parcelFilterRes as $index => $parcelObj) {
                    $consignment_id = $parcelObj->getConsignmentId();
                    $consignmentObj = new Consignment($consignment_id);
                    $foundTrackingNo[] = $parcelObj->getTrackingNumber();
                    $dimension = $parcelObj->getLength() . 'x' . $parcelObj->getWidth() . 'x' . $parcelObj->getHeight();
                    $itemInputs .= '<input type="hidden" name="consignment_id[' . $index . ']" value="' . $parcelObj->getConsignmentId() . '" />';
                    $itemInputs .= '<input type="hidden" name="goods_name[' . $index . ']" value="' . $consignmentObj->getDescription() . '" />';
                    $itemInputs .= '<input type="hidden" name="description[' . $index . ']" value="' . $consignmentObj->getDescription() . '" />';
                    $itemInputs .= '<input type="hidden" name="dimension[' . $index . ']" value="' . $dimension . '" />';
                    $itemInputs .= '<input type="hidden" name="weight[' . $index . ']" value="' . $consignmentObj->getWeight() . '" />';
                    $itemInputs .= '<input type="hidden" name="tracking_number[' . $index . ']" value="' . $parcelObj->getTrackingNumber() . '" />';
                }
            }
            $notFoundTrackingNo = array_diff($york_tracking_noArr, $foundTrackingNo);
            if (count($notFoundTrackingNo) > 0) {
                $out['status'] = 'fail';
                $message .= '<div class="alert alert-danger">Fillowing tracking numbers not found add these</div>';
                $message .= '<table class="table table-striped table-bordered table-hover">';
                foreach ($notFoundTrackingNo as $not_found_tracking_no) {
                    $message .= '<tr><td>' . $not_found_tracking_no . '<td><td><button type="button" data-tracking_no="' . $not_found_tracking_no . '" class="btn btn-danger btn-xs add-return-shippment">Add Shippment</button></td></tr>';
                }
                $message .= '</table>';
            } else if (count($foundTrackingNo) == count($york_tracking_noArr)) {
                $message .= $itemInputs;
            }
            $out['message'] = $message;
            echo json_encode($out);
            exit;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'in_rack_item') {

            $dimension = $this->form_vars['length'] . "x" . $this->form_vars['width'] . "x" . $this->form_vars['height'];
            $fieldsVal = array('goods_name' => $this->form_vars['goods_name'],
                'description' => $this->form_vars['description'],
                'weight' => $this->form_vars['weight'],
                'dimension' => $dimension,
                'added_date' => date("Y-m-d H:i:s"),
                'added_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0'),
                'updated_date' => date("Y-m-d H:i:s"),
                'updated_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
            );
            $ShelfItemObj = new RackShelfItem($fieldsVal);
            $ShelfItemObj->save(true);
            $rack_shelf_id = $this->form_vars['rack_shelf_id'];
            $rack_shelf_item_id = $ShelfItemObj->getId();
            if (!empty($rack_shelf_item_id) && $rack_shelf_item_id > 0) {
                $logFieldsVal = array('rack_shelf_id' => $rack_shelf_id,
                    'rack_shelf_item_id' => $rack_shelf_item_id,
                    'customer_id' => $this->form_vars['customer_id'],
                    'in_date' => date("Y-m-d H:i:s"),
                    'in_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
                );
                $LogShelfItem = new LogRackShelf($logFieldsVal);
                $LogShelfItem->save(true);
                $log_shelf_item_id = $LogShelfItem->getId();
                if (!empty($log_shelf_item_id) && $log_shelf_item_id > 0) {
                    $rackShelfObj = new RackShelf($rack_shelf_id);
                    $rackShelfObj->setIsFilled(1);
                    $rackShelfObj->setUpdatedDate(date("Y-m-d H:i:s"));
                    $rackShelfObj->setUpdatedBy(isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                    $rackShelfObj->save(true);
                    // Label Info
                    $labelInfo = $this->getLabelInfo($rack_shelf_id, 'in');
                    print_r($labelInfo);
                    $label = new GlOrderPdfReturnRack();
                    $label->AddLabelInfo($labelInfo);
                    $filename = $label->buildPDFDocuments();
                    echo $filename;
                }
            }
            exit;
        }
        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        $UObj = new UserAccountFilter();
        $this->customer_list = $UObj->getColumnList('full_name,user_account');
        $WObj = new WarehouseFilter();
        $this->warehouses = $WObj->getList();

        /* ------------------------------------------------------------------------------ */
        // get vars
        $this->id = (isset($_REQUEST['rack_id']) ? strip_tags($_REQUEST['rack_id']) : '0');

        if ($_SESSION['user_type'] == 'warehouse' && $this->id == 0) {
            util_redirect("racks.php");
        }

        if ($this->id != 0) {
            $RackObj = new Rack($this->id);
            $orig_id = $RackObj->getId();
            if ($orig_id == 0)
                util_redirect("racks.php");
        }


        /* ------------------------------------------------------------------------------ */
        // process form
        if (isset($this->form_vars['btn_save'])) {
            $this->warehouse_id = $this->form_vars['warehouse_id'];
            $this->title = $this->form_vars['title'];
            $this->short_title = $this->form_vars['short_title'];
            $this->rack_rows = $this->form_vars['rack_rows'];
            $this->rack_cols = $this->form_vars['rack_cols'];
            $this->shelf_length = $this->form_vars['shelf_length'];
            $this->shelf_width = $this->form_vars['shelf_width'];
            $this->shelf_height = $this->form_vars['shelf_height'];
            $this->shelf_max_weight = $this->form_vars['shelf_max_weight'];
            $this->is_york = isset($this->form_vars['is_york']) ? '1' : '0';
            $this->is_active = isset($this->form_vars['is_active']) ? '1' : '0';
            if ($this->validate_form()) {
                $fieldsVal = array('id' => $this->id,
                    'warehouse_id' => $this->warehouse_id,
                    'title' => $this->title,
                    'short_title' => $this->short_title,
                    'rack_rows' => $this->rack_rows,
                    'rack_cols' => $this->rack_cols,
                    'shelf_dimension' => $this->shelf_length . "x" . $this->shelf_width . "x" . $this->shelf_height,
                    'shelf_max_weight' => $this->shelf_max_weight,
                    'is_york' => $this->is_york,
                    'is_active' => $this->is_active,
                    'is_deleted' => 0,
                    'added_date' => date("Y-m-d H:i:s"),
                    'added_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
                );
                if ($this->id > 0) {
                    $fieldsVal['updated_date'] = date("Y-m-d H:i:s");
                    $fieldsVal['updated_by'] = (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0');
                }
                $RackObj = new Rack($fieldsVal);
                $RackObj->save(true);
                $new_rack_id = $RackObj->getId();
                if ($this->id == 0) {
                    $totalshelfs = $this->rack_rows * $this->rack_cols;
                    for ($s = 0; $s < $totalshelfs; $s++) {
                        $fieldVal = array('rack_id' => $new_rack_id,
                            'shelf_no' => $s,
                            'is_filled' => 0,
                            'updated_date' => date("Y-m-d H:i:s"),
                            'updated_by' => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : '0')
                        );
                        $RsObj = new RackShelf($fieldVal);
                        $RsObj->save(true);
                    }
                }
            }
        }
        /* ------------------------------------------------------------------------------ */
        // get warehouse
        //echo $this->id;
        $RObj = new Rack($this->id);
        $this->warehouse_id = $RObj->getWarehouseId();
        $this->title = $RObj->getTitle();
        $this->short_title = $RObj->getShortTitle();
        $this->rack_rows = $RObj->getRackRows();
        $this->rack_cols = $RObj->getRackCols();
        $shelf_dimension_tmp = $RObj->getShelfDimension();
        list($this->shelf_length, $this->shelf_width, $this->shelf_height) = explode("x", $shelf_dimension_tmp);
        $this->shelf_max_weight = $RObj->getShelfMaxWeight();
        $this->is_york = $RObj->getIsYork();
        $this->is_active = $RObj->getIsActive();

        /* ------------------------------------------------------------------------------ */
        $this->setTitle("Admin - Rack Details");
    }

    /*     * *
     * This page's content
     * @return void
     */

    protected function addPagelavelCss() {
        ?>
        <style type="text/css">

            td.table-green  { background:#060; color:#fff}
            .table-green .btn  { background:#060; color:#fff}

            td.table-red { background:#C00; color:#fff}   


            .table-red .btn { background:#C00; color:#fff}   
            .btn-gorup-table { display:block !important;}			
            #contextMenu {
                position: absolute;
                display:none;
                z-index: 999999999999;
            }
            .dropdown-menu li a.disabled{
                color: #aaa;
                cursor: default;
            }            
            .dropdown-menu li a.disabled:hover{
                background-color: #fff !important;
            }
            .dropdown-menu li a.disabled:active{
                background-color: #fff !important;
            }
            .dropdown-menu li a.disabled:focus{
                background-color: #fff !important;
            }
            .table .btn { margin-right:0 !important}
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script type="text/javascript">

            $(document).ready(function () {
                $('input[title]').tooltip({placement: 'bottom'});
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
        <?php
        if ($this->id > 0) {
            ?>
                    getRackShelf('<?php echo $this->id; ?>');
            <?php
        }
        ?>
                $(document).on('click', '.add-return-shippment', function () {
                    var tracking_no = $(this).data('tracking_no');
                    $("#york_rtn_tracking_no").val(tracking_no);
                    $("#york_shipment_add").modal('show');
                    return false;
                });

                $(document).on('click', '#btn_york_add_rtn_shipment', function () {
                    if ($.trim($("#york_rtn_tracking_no").val()) == "") {
                        alert("Enter Tracking number!");
                        $("#york_rtn_tracking_no").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Description").val()) == "") {
                        alert("Enter Description!");
                        $("#york_rtn_Description").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Company").val()) == "") {
                        alert("Enter Company!");
                        $("#york_rtn_Company").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Contact").val()) == "") {
                        alert("Enter Contact!");
                        $("#york_rtn_Contact").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Service").val()) == "") {
                        alert("Enter Service!");
                        $("#york_rtn_Service").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Handling").val()) == "") {
                        alert("Enter Handling!");
                        $("#york_rtn_Handling").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_AddressLine1").val()) == "") {
                        alert("Enter Address Line 1!");
                        $("#york_rtn_AddressLine1").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_City").val()) == "") {
                        alert("Enter City!");
                        $("#york_rtn_City").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Country").val()) == "") {
                        alert("Enter Country!");
                        $("#york_rtn_Country").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_PostCode").val()) == "") {
                        alert("Enter Post Code!");
                        $("#york_rtn_PostCode").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_CountryIsoCode").val()) == "") {
                        alert("Enter Country Iso Code!");
                        $("#york_rtn_CountryIsoCode").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Telephone").val()) == "") {
                        alert("Enter Telephone!");
                        $("#york_rtn_Telephone").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_NumberPieces").val()) == "") {
                        alert("Enter Number of Pieces!");
                        $("#york_rtn_NumberPieces").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_Weight").val()) == "") {
                        alert("Enter Weight!");
                        $("#york_rtn_Weight").focus();
                        return false;
                    }
                    if ($.trim($("#york_rtn_VolWeight").val()) == "") {
                        alert("Enter Vol. Weight!");
                        $("#york_rtn_VolWeight").focus();
                        return false;
                    }
                    return false;
                });

                $("#york_btn_in_item").on("click", function () {

                    /*
                     if ($.trim($("#york_customer_id").val()) == "") {
                     alert("Select Customer!");
                     $("#york_customer_id").focus();
                     return false;
                     }
                     */
                    if ($.trim($("#york_tracking_no").val()) == "") {
                        alert("Enter tracking numbers!");
                        $("#york_tracking_no").focus();
                        return false;
                    }

                    $.post('rack_add.php', $("#york_frm_item_in").serialize(), function (data) {
                        try {
                            var response = JSON.parse(data);
                            $("#york_validate_container").html(response.message);
                            if (response.status == 'success') {
                                $("#york_add_edit_item_func").val('york_in_rack_item');
                                $.post('rack_add.php', $("#york_frm_item_in").serialize(), function (data) {

                                    $("#york_add_edit_item_func").val('validate_york_in_rack_item');
                                    var response = JSON.parse(data);
                                    //alert(response.message);
                                    $("#york_frm_item_in_message").html(response.message);
                                    $("#york_frm_item_in_message").css("display", "block");
                                });
                            }
                        } catch (err) {

                        }
                    });
                    return false;
                });


                $("#york_btn_out_item").on("click", function () {

                    /*
                     if ($.trim($("#york_customer_id").val()) == "") {
                     alert("Select Customer!");
                     $("#york_customer_id").focus();
                     return false;
                     }
                     
                     if ($.trim($("#york_tracking_no").val()) == "") {
                     alert("Enter tracking numbers!");
                     $("#york_tracking_no").focus();
                     return false;
                     }
                     */



                    $.post('rack_add.php', $("#york_frm_item_out").serialize(), function (data)
                    {
                        try
                        {
                            var response = JSON.parse(data);
                            //$("#york_validate_container").html(response.message);
                            //if(response.status == 'success'){
                            $("#york_add_edit_item_func").val('york_out_rack_item');

                            $.post('rack_add.php', $("#york_frm_item_out").serialize(), function (data){
                                $("#york_add_edit_item_func").val('york_out_rack_item');
                                var response = JSON.parse(data);
                                alert(response.message);
                                $("#york_frm_item_out_message").html(response.message);
                                $("#york_frm_item_out_message").css("display", "block");
                            });
                        } catch (err) {

                        }
                    });
                    return false;
                });
                $("#btn_in_item").on("click", function () {
                    if ($.trim($("#customer_id").val()) == "") {
                        alert("Select Customer!");
                        $("#customer_id").focus();
                        return false;
                    }
                    if ($.trim($("#goods_name").val()) == "") {
                        alert("Enter Item Nasme!");
                        $("#goods_name").focus();
                        return false;
                    }
                    if ($.trim($("#weight").val()) == "") {
                        alert("Enter Item Weight!");
                        $("#weight").focus();
                        return false;
                    }
                    if (isNaN($("#weight").val())) {
                        alert("Enter Valid Item Weight!");
                        $("#weight").focus();
                        return false;
                    }
                    if ($.trim($("#length").val()) == "") {
                        alert("Enter Item Length!");
                        $("#length").focus();
                        return false;
                    }
                    if ($.trim($("#width").val()) == "") {
                        alert("Enter Item Width!");
                        $("#width").focus();
                        return false;
                    }
                    if ($.trim($("#height").val()) == "") {
                        alert("Enter Item Height!");
                        $("#height").focus();
                        return false;
                    }
                    $.post('rack_add.php', $("#frm_item_in").serialize(), function (data) {
                        $('#in_item_modal').modal('hide');
                        $('#frm_item_in').find('input:text').val('');
                        $('#frm_item_in').find('textarea').val('');
                        openLabelPopup(data);
                        getRackShelf('<?php echo (int) $this->id; ?>');
                    });
                    return false;
                });
                $("#btn_shift_item").click(function () {
                    $.post('rack_add.php', $("#frm_item_shift").serialize(), function (data) {
                        $('#shift_item_modal').modal('hide');
                        openLabelPopup(data);
                        getRackShelf('<?php echo (int) $this->id; ?>');
                    });
                });

                $("#btn_out_item").click(function () {
                    $.post('rack_add.php', $("#frm_item_out").serialize(), function (data) {
                        $('#out_item_modal').modal('hide');
                        $('#frm_item_out').find('textarea').val('');
                        openLabelPopup(data);
                        getRackShelf('<?php echo (int) $this->id; ?>');
                    });
                });

                $("#btn_york_out_item").click(function () {

                    $.post('rack_add.php', $("#york_frm_item_out").serialize(), function (data) {
                        $('#york_out_item_modal').modal('hide');
                        $('#frm_item_out').find('textarea').val('');
                        //openLabelPopup(data);
                        getRackShelf('<?php echo (int) $this->id; ?>');
                    });
                });


                var $contextMenu = $("#contextMenu");
                $("body").on("contextmenu", "table tr td", function (e) {

                    $(".contextmenu_menu_a").removeClass('disabled');

                    var is_filled = $(this).data('is_filled');
                    var shelf_id = $(this).data('shelf_id');
                    var shelf_title = $(this).data('shelf_title');

                    var type = $(this).data("type");

                    var log_shelf_id = null;



                    $("#contextmenu_menu_history").data('shelf_id', shelf_id);

                    if (is_filled == 0)
                    {

                        $("#contextmenu_menu_in").data('shelf_id', shelf_id);
                        $("#contextmenu_menu_in").data('shelf_title', shelf_title);


                        if (type == 0)
                        {
                            $("#contextmenu_menu_out").addClass("disabled");
                        } else
                        {
                            $("#contextmenu_menu_out").data('shelf_title', shelf_title);
                            $("#contextmenu_menu_out").data('shelf_id', shelf_id);
                        }

                        $("#contextmenu_menu_shift").addClass("disabled");
                        $("#contextmenu_menu_edit").addClass("disabled");
                    } else
                    {
                        log_shelf_id = $(this).data('log_shelf_id');

                        $("#contextmenu_menu_out").data('shelf_id', shelf_id);
                        $("#contextmenu_menu_out").data('log_shelf_id', log_shelf_id);
                        $("#contextmenu_menu_out").data('shelf_title', shelf_title);

                        $("#contextmenu_menu_shift").data('shelf_id', shelf_id);
                        $("#contextmenu_menu_shift").data('log_shelf_id', log_shelf_id);
                        $("#contextmenu_menu_shift").data('shelf_title', shelf_title);

                        $("#contextmenu_menu_edit").data('shelf_id', shelf_id);
                        $("#contextmenu_menu_edit").data('log_shelf_id', log_shelf_id);
                        $("#contextmenu_menu_edit").data('shelf_title', shelf_title);

                        $("#contextmenu_menu_in").addClass("disabled");
                    }
                    $contextMenu.css({
                        display: "block",
                        left: e.pageX,
                        top: e.pageY - 110
                    });
                    return false;
                });
                $("body").on('click', function () {
                    $contextMenu.hide();
                });
                $contextMenu.on("click", "a", function (e) {
                    if ($(this).hasClass('disabled'))
                        return false;

                    $contextMenu.hide();
                    var shelf_id = $(this).data('shelf_id');
                    var type = $(this).data("type");
                    var shelf_title = $(this).data('shelf_title');



                    switch (type) {
                        case 'york_in':
        //                            $("#customer_id").select2("val", "");
                            $("#customer_id").val();
                            $('#york_frm_item_in').find('input:text').val('');
                            $('#york_frm_item_in').find('textarea').val('');
                            $("#york_add_edit_item_func").val("validate_york_in_rack_item");
                            $("#york_add_item_modal_title_shelf_name").html(shelf_title);
                            $("#york_rack_shelf_id").val(shelf_id);
                            $("#york_frm_item_in_message").css("display", "none");
                            $('#york_in_item_modal').modal('show');
                            break;
                        case 'in':
        //                            $("#customer_id").select2("val", "");
                            $("#customer_id").val();
                            $('#frm_item_in').find('input:text').val('');
                            $('#frm_item_in').find('textarea').val('');
                            $("#add_edit_item_func").val("in_rack_item");
                            $("#add_item_modal_title_shelf_name").html(shelf_title);
                            $("#rack_shelf_id").val(shelf_id);
                            $('#in_item_modal').modal('show');
                            break;
                        case 'out':
                            var log_shelf_id = $(this).data('log_shelf_id');
                            if (confirm("Are you sure you want to out this item?")) {
                                $("#out_item_modal_title_shelf_name").html(shelf_title);
                                $("#out_shelf_id").val(shelf_id);
                                $("#out_log_rack_shelf_id").val(log_shelf_id);
                                $('#out_item_modal').modal('show');
                            }
                            break;
                        case 'york_out':
                            //var log_shelf_id = $(this).data('log_shelf_id');
                            //var york_rack_shelf_id = $(this).data('york_rack_shelf_id');			


                            if (confirm("Are you sure you want to remove items from york?"))
                            {

                                $("#york_remove_item_modal_title_shelf_name").html(shelf_title);
                                $("#york_out_rack_shelf_id").val(shelf_id);
                                $("#york_out_log_rack_shelf_id").val(log_shelf_id);
                                $("#york_out_add_edit_item_func").val("york_out_rack_item");
                                $('#york_out_item_modal').modal('show');
                            }
                            break;
                        case 'shift':
                            if (confirm("Are you sure you want to shift this item?")) {
                                var log_shelf_id = $(this).data('log_shelf_id');
                                $.post('rack_add.php', {func: 'load_empty_shelfs', rack_id: '<?php echo (int) $this->id; ?>'}, function (data) {
                                    var available_shelf = "";
                                    $.each(data, function (k, v) {
                                        available_shelf += '<option value="' + k + '"><?php echo $this->short_title; ?> ' + v + '</option>';
                                    });
                                    $("#old_rack_shelf_id").val(shelf_id);
                                    $("#old_log_rack_shelf_id").val(log_shelf_id);
                                    $("#new_rack_shelf_id").html(available_shelf);
                                    $("#shift_item_modal_title_shelf_name").html(shelf_title);
                                    $('#shift_item_modal').modal('show');
                                }, "json");
                            }
                            break;
                        case 'edit':
                            $("#add_edit_item_func").val("edit_rack_item");
                            var log_shelf_id = $(this).data('log_shelf_id');
                            $("#edit_log_rack_shelf_id").val(log_shelf_id);
                            $("#rack_shelf_id").val(shelf_id);
                            $("#add_item_modal_title_shelf_name").html(shelf_title);
                            $('#in_item_modal').modal('show');
                            $("#btn_in_item").prop("disabled", true);
                            $.post('rack_add.php', {func: 'get_item_detail', log_shelf_id: log_shelf_id}, function (data) {
                                $("#edit_rack_shelf_item_id").val(data.rack_shelf_item_id);
                                $("#customer_id").val(data.customer_id);
                                //$("#customer_id").select2("val", data.customer_id);
                                $("#goods_name").val(data.goods_name);
                                $("#weight").val(data.weight);
                                $("#length").val(data.length);
                                $("#width").val(data.width);
                                $("#height").val(data.height);
                                $("#description").val(data.description);
                                $("#btn_in_item").prop("disabled", false);
                            }, "json")
                            break;
                        case 'history':
                            window.location = 'report_rack_shelf.php?id=' + shelf_id;
                            break;
                    }
                });

            });
            function openLabelPopup(url) {
                if ($.trim(url)) {
                    window.open($.trim(url), '_blank', 'height=350,width=205');
                }
            }
            function getRackShelf(rack_id) {
                $.post('rack_add.php', {func: 'get_rack_shelf', rack_id: rack_id}, function (data) {
                    $("#rack_shelf_tabel").html(data);
                    $.fn.reverse = [].reverse;
                    $("#rack_shelf_tbl > tbody > tr").reverse().appendTo("#rack_shelf_tbl > tbody");
                    $(".popovers").popover();
                });
            }
        </script>
        <?php
    }

    public function renderBody() {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-cogs"></i>Rack Detail </div>
                <div class="tools">
                </div>
                <div class="actions">
                    <a href="racks.php" class="btn blue"><i class="fa fa-list"></i> Rack List</a>
                </div>
            </div>
            <div class="portlet-body">
                    <?php
                    if ($_SESSION['user_type'] == 'admin') {
                        ?>
                    <form method="post">
                        <?php
                        if ($this->error_msg != "") {
                            ?>
                            <div class="note note-success"><?php echo $this->error_msg ?></div>
                <?php
            }
            ?>
                        <div class="form">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-ioxhost"></i> </span>
                                                <?php
                                                if (count($this->warehouses) > 0) {
                                                    ?>
                                                <select name="warehouse_id" id="warehouse_id" class="form-control">
                                                    <option value="0">---Warehouse---</option>
                                                    <?php
                                                    foreach ($this->warehouses as $warehouse) {
                                                        ?>
                                                        <option value="<?php echo $warehouse->getId(); ?>"<?php echo ($warehouse->getId() == $this->warehouse_id ? ' selected="selected"' : ''); ?>><?php echo $warehouse->getWarehouseName(); ?></option>
                                                    <?php
                                                }
                                                ?>
                                                </select>
                <?php
            }
            ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-3 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-list"></i> </span>
                                            <input class="form-control" placeholder="Title" type="text" name="title" id="title" value="<?php echo $this->title; ?>"  rel="tooltip" title="Title" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-list"></i> </span>
                                            <input class="form-control" placeholder="Short Title" type="text" name="short_title" id="short_title" value="<?php echo $this->short_title; ?>" rel="tooltip" title="Short Title" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-bars"></i> </span>
                                            <select class="form-control" name="rack_rows" id="rack_rows"<?php echo ($this->id > 0 ? ' disabled="disabled"' : ''); ?>>
                                                <option value="">Levels</option>
                                            <?php for ($r = 1; $r <= 500; $r++) { ?>
                                                    <option value="<?php echo $r; ?>"<?php echo ($r == $this->rack_rows ? ' selected="selected"' : ''); ?>><?php echo $r; ?></option>
            <?php } ?>
                                            </select>
            <?php if ($this->id > 0) { ?>
                                                <input type="hidden" name="rack_rows" value="<?php echo $this->rack_rows; ?>" />
            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-bars fa-rotate-270"></i> </span>
                                            <select class="form-control" name="rack_cols" id="rack_cols"<?php echo ($this->id > 0 ? ' disabled="disabled"' : ''); ?>>
                                                <option value="">Shelves</option>
                                            <?php for ($c = 1; $c <= 500; $c++) { ?>
                                                    <option value="<?php echo $c; ?>"<?php echo ($c == $this->rack_cols ? ' selected="selected"' : ''); ?> rel="tooltip" title="Colums" ><?php echo $c; ?></option>
            <?php } ?>
                                            </select>
            <?php if ($this->id > 0) { ?>
                                                <input type="hidden" name="rack_cols" value="<?php echo $this->rack_cols; ?>" />
            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-table "></i> </span>
                                            <input class="form-control" placeholder="Shelf Length" type="text" name="shelf_length" id="shelf_length" value="<?php echo $this->shelf_length; ?>"  rel="tooltip" title="Shelf Length"/>
                                        </div>
                                    </div>
                                    <p class="help-block">Legth in cm</p>
                                </div>
                                <div class="col-md-2 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-table "></i> </span>
                                            <input class="form-control" placeholder="Shelf Length" type="text" name="shelf_width" id="shelf_width" value="<?php echo $this->shelf_width; ?>"  rel="tooltip" title="Shelf Width"/>
                                        </div>
                                    </div>
                                    <p class="help-block">Width in cm</p>
                                </div>
                                <div class="col-md-2 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-table "></i> </span>
                                            <input class="form-control" placeholder="Shelf Length" type="text" name="shelf_height" id="shelf_height" value="<?php echo $this->shelf_height; ?>" rel="tooltip" title="Shelf Height" />
                                        </div>
                                    </div>
                                    <p class="help-block">Height in cm</p>
                                </div>
                                <div class="col-md-2 form-group">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-tasks"></i> </span>
                                            <input class="form-control" placeholder="Shelf Max Weight" type="text" name="shelf_max_weight" id="shelf_max_weight" value="<?php echo $this->shelf_max_weight; ?>" rel="tooltip" title="Shelf Max Weight"/>
                                        </div>
                                    </div>
                                    <p class="help-block">Weight in Kg</p>
                                </div>
                                <div class="col-md-2 form-group">
                                    <br />
                                    <label>
                                        <input type="checkbox" name="is_york" id="is_york" value=""<?php echo ($this->is_york == 1 ? ' checked="checked"' : ''); ?> />
                                        York</label>
                                </div>
                                <div class="col-md-2 form-group">
                                    <br />
                                    <label>
                                        <input type="checkbox" name="is_active" id="is_active" value=""<?php echo ($this->is_active == 1 ? ' checked="checked"' : ''); ?> />
                                        Active</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary" id="btn_save" name="btn_save" value="Save Changes">Save</button>
                                    <a href="rack_add.php" id="btnCancel" class="btn_cancel btn btn btn-default" data-original-title="" title=""><span></span>Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
            <?php
        } else {
            ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>Warehouse</th>
                            <td colspan="3">
            <?php
            $wh = new Warehouse($this->warehouse_id);
            echo $wh->getWarehouseName();
            ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td><?php echo $this->title; ?></td>
                            <th>Short Title</th>
                            <td><?php echo $this->short_title; ?></td>
                        </tr>
                        <tr>
                            <th>Levels</th>
                            <td><?php echo $this->rack_rows; ?></td>
                            <th>Shelves</th>
                            <td><?php echo $this->rack_cols; ?></td>
                        </tr>
                        <tr>
                            <th>Shelf Max Weight</th>
                            <td><?php echo $this->shelf_max_weight; ?>Kg</td>
                            <th>Shelf Dimension</th>
                            <td><?php echo $this->shelf_length . "x" . $this->shelf_width . "x" . $this->shelf_height; ?></td>
                        </tr>                    
                    </table>
            <?php
        }
        ?>
            </div>
        </div>
        <?php
        if ($this->id > 0) {
            ?>
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-cogs"></i>Rack Shelves </div>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered" id="rack_shelf_tbl">
                            <tbody id="rack_shelf_tabel">
                                <tr>
                                    <td>Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="contextMenu" class="clearfix">
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:0px;">
                                <li><a tabindex="-1" class="contextmenu_menu_a" id="contextmenu_menu_in" 
                                       data-type="<?php echo (($this->is_york) ? 'york_' : ''); ?>in" data-shelf_id="" data-shelf_id="" data-shelf_title="" href="javascript:{};">Item In</a></li>
                                <li><a tabindex="-1" class="contextmenu_menu_a" id="contextmenu_menu_out" 
                                       data-type="<?php echo (($this->is_york) ? 'york_' : ''); ?>out" data-shelf_id="" data-log_shelf_id="" data-shelf_title="" href="javascript:{};">Item Out</a></li>
                                <li><a tabindex="-1" class="contextmenu_menu_a" id="contextmenu_menu_shift" data-type="shift" data-shelf_id="" data-log_shelf_id="" data-shelf_title="" href="javascript:{};">Item Shift</a></li>
                                <li><a tabindex="-1" class="contextmenu_menu_a" id="contextmenu_menu_edit" data-type="edit" data-shelf_id="" data-log_shelf_id="" data-shelf_title="" href="javascript:{};">Edit Item</a></li>
                                <li class="divider"></li>
                                <li><a tabindex="-1" id="contextmenu_menu_history" data-type="history" href="javascript:{};">View History</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>            
            <?php
        }
    }

    public function renderFooter() {
        ?>
        <div class="modal fade" id="york_in_item_modal" role="basic" aria-hidden="true">
            <div class="page-loading page-loading-boxed"> <span>&nbsp;&nbsp;Loading... </span> </div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add item in york shelf <span id="york_add_item_modal_title_shelf_name"></span></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form" name="york_frm_item_in" id="york_frm_item_in">
                            <div id="york_frm_item_in_message" style="display:none" class="alert alert-success">  									
                            </div>
                            <input type="hidden" name="york_rack_shelf_id" id="york_rack_shelf_id" value="" />
                            <input type="hidden" name="york_log_rack_shelf_id" id="york_edit_log_rack_shelf_id" value="" />
                            <input type="hidden" name="york_rack_shelf_item_id" id="york_edit_rack_shelf_item_id" value="" />
                            <input type="hidden" name="func" id="york_add_edit_item_func" value="validate_york_in_rack_item" />
                            <input type="hidden" name="york_rack_id" value="<?php echo (int) $this->id; ?>" />
                            <!--<div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Customer</label>
                                    <select class="form-control" name="york_customer_id" id="york_customer_id" data-toggle="dropdown">                                            
                            <?php
                            /* if (count($this->customer_list) > 0) {
                              foreach ($this->customer_list as $customer) {
                              ?>
                              <option value="<?php echo $customer->getId(); ?>"><?php echo $customer->getFirstName() . " [" . $customer->getUserAccount() . "]"; ?></option>
                              <?php
                              }
                              } */
                            ?>
                                    </select>
                                </div>
                            </div>                                
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Item Description</label>
                                    <textarea class="form-control" name="york_description" id="york_description" rows="3" cols="10"></textarea>
                                </div>
                            </div>-->
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Tracking Numbers</label>
                                    <textarea class="form-control" name="york_tracking_no" id="york_tracking_no" rows="3" cols="10"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="york_validate_container">

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="york_btn_in_item">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="in_item_modal" role="basic" aria-hidden="true">
            <div class="page-loading page-loading-boxed"> <span>&nbsp;&nbsp;Loading... </span> </div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add item in shelf <span id="add_item_modal_title_shelf_name"></span></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form" name="frm_item_in" id="frm_item_in">
                            <input type="hidden" name="rack_shelf_id" id="rack_shelf_id" value="" />
                            <input type="hidden" name="log_rack_shelf_id" id="edit_log_rack_shelf_id" value="" />
                            <input type="hidden" name="rack_shelf_item_id" id="edit_rack_shelf_item_id" value="" />
                            <input type="hidden" name="func" id="add_edit_item_func" value="in_rack_item" />
                            <input type="hidden" name="rack_id" value="<?php echo (int) $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Customer</label>
                                    <select class="form-control" name="customer_id" id="customer_id" data-toggle="dropdown">                                            
                                        <?php
                                        if (count($this->customer_list) > 0) {
                                            foreach ($this->customer_list as $customer) {
                                                ?>
                                                <option value="<?php echo $customer->getId(); ?>"><?php echo $customer->getFullName() . " [" . $customer->getUserAccount() . "]"; ?></option>
                <?php
            }
        }
        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Item Name</label>
                                    <input type="text" class="form-control" name="goods_name" id="goods_name" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label">Item Weight</label>
                                    <input type="text" class="form-control" name="weight" id="weight" />
                                    <p class="help-block">Weight in Kg</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Item Length</label>
                                    <input type="text" class="form-control" name="length" id="length" />
                                    <p class="help-block">Length in cm</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Item Width</label>
                                    <input type="text" class="form-control" name="width" id="width" />
                                    <p class="help-block">Width in cm</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Item Height</label>
                                    <input type="text" class="form-control" name="height" id="height" />
                                    <p class="help-block">Height in cm</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Item Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="3" cols="10"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="btn_in_item">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="shift_item_modal" role="basic" aria-hidden="true">
            <div class="page-loading page-loading-boxed"> <span>&nbsp;&nbsp;Loading... </span> </div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Shift item from shelf <span id="shift_item_modal_title_shelf_name"></span></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form" name="frm_item_shift" id="frm_item_shift">
                            <input type="hidden" name="old_rack_shelf_id" id="old_rack_shelf_id" value="" />
                            <input type="hidden" name="log_rack_shelf_id" id="old_log_rack_shelf_id" value="" />
                            <input type="hidden" name="func" value="shift_rack_item" />
                            <input type="hidden" name="rack_id" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Shelf to Shift</label>
                                    <select name="new_rack_shelf_id" id="new_rack_shelf_id" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="btn_shift_item">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="out_item_modal" role="basic" aria-hidden="true">
            <div class="page-loading page-loading-boxed"> <span>&nbsp;&nbsp;Loading... </span> </div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Out Shelf Item <span id="out_item_modal_title_shelf_name"></span></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form" name="frm_item_out" id="frm_item_out">
                            <input type="hidden" name="log_rack_shelf_id" id="out_log_rack_shelf_id" value="" />
                            <input type="hidden" name="shelf_id" id="out_shelf_id" value="" />
                            <input type="hidden" name="func" value="out_shelf_item" />
                            <input type="hidden" name="rack_id" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="btn_out_item">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="york_out_item_modal" role="basic" aria-hidden="true">
            <div class="page-loading page-loading-boxed"> <span>&nbsp;&nbsp;Loading... </span> </div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                            
                        <h4 class="modal-title">York Items Out <span id="york_remove_item_modal_title_shelf_name"></span></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form" name="york_frm_item_out" id="york_frm_item_out">
                            <input type="hidden" name="york_out_rack_shelf_id" id="york_out_rack_shelf_id" value="" />
                            <input type="hidden" name="york_out_log_rack_shelf_id" id="york_edit_log_rack_shelf_id" value="" />
                            <input type="hidden" name="york_out_rack_shelf_item_id" id="york_out_rack_shelf_item_id" value="" />
                            <input type="hidden" name="func" id="york_add_edit_item_func" value="york_out_rack_item" />
                            <input type="hidden" name="york_rack_id" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">Enter Tracking Number</label>
                                    <input id="tracking_no_remove" type="text" class="form-control" />
                                    <label class="control-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" cols="10" rows="3"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="york_btn_out_item">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="york_shipment_add" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add Return Shipment Details</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form" name="frm_york_add_return_shipment" id="frm_york_add_return_shipment">
                            <input type="hidden" name="rack_id" value="<?php echo $this->id; ?>" />
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label">Tracking No</label>
                                    <input type="text" name="york_rtn_tracking_no" id="york_rtn_tracking_no" class="form-control" readonly="true" />
                                </div>
                                <div class="col-md-8">
                                    <label class="control-label">Description</label>
                                    <input type="text" name="york_rtn_Description" id="york_rtn_Description" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label">Company</label>
                                    <input type="text" name="york_rtn_company" id="york_rtn_Company" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Contact</label>
                                    <input type="text" name="york_rtn_Contact" id="york_rtn_Contact" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Service</label>
                                    <input type="text" name="york_rtn_Service" id="york_rtn_Service" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Handling</label>
                                    <input type="text" name="york_rtn_Handling" id="york_rtn_Handling" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label">Address Line 1</label>
                                    <input type="text" name="york_rtn_AddressLine1" id="york_rtn_AddressLine1" class="form-control" />
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label">Address Line 2</label>
                                    <input type="text" name="york_rtn_AddressLine2" id="york_rtn_AddressLine2" class="form-control" />
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label">Address Line 3</label>
                                    <input type="text" name="york_rtn_AddressLine3" id="york_rtn_AddressLine3" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label">City</label>
                                    <input type="text" name="york_rtn_City" id="york_rtn_City" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Country</label>
                                    <input type="text" name="york_rtn_Country" id="york_rtn_Country" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Post Code</label>
                                    <input type="text" name="york_rtn_PostCode" id="york_rtn_PostCode" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Country Iso Code</label>
                                    <input type="text" name="york_rtn_CountryIsoCode" id="york_rtn_CountryIsoCode" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label">Telephone</label>
                                    <input type="text" name="york_rtn_Telephone" id="york_rtn_Telephone" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Number of Pieces</label>
                                    <input type="text" name="york_rtn_NumberPieces" id="york_rtn_NumberPieces" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Weight</label>
                                    <input type="text" name="york_rtn_Weight" id="york_rtn_Weight" class="form-control" />
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Vol. Weight</label>
                                    <input type="text" name="york_rtn_VolWeight" id="york_rtn_VolWeight" class="form-control" />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn blue" id="btn_york_add_rtn_shipment">Save changes</button>
                    </div>
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
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }

    public function renderHead() {
        ?>

        <?php
    }

    private function getRackShelf($rack_id) {
        $output = "";
        $RackObj = new Rack($rack_id);
        $rack_title = $RackObj->getTitle();
        $rack_short_title = $RackObj->getShortTitle();
        $rack_rows = $RackObj->getRackRows();
        $rack_cols = $RackObj->getRackCols();
        $rack_shelf_max_weight = $RackObj->getShelfMaxWeight();
        $rack_shelf_dimension = $RackObj->getShelfDimension();
        $is_york = $RackObj->getIsYork();

        $rackShelfObj = new RackShelfFilter();
        $rackShelfObj->addFilter("rs.rack_id = '" . $rack_id . "'");
        $rackshelfs = $rackShelfObj->getList();
//	echo count($rackshelfs);
//	die;
        if (count($rackshelfs) > 0) {
            $output .= "<tr>";
            foreach ($rackshelfs as $rackshelf) {
                $shelf_id = $rackshelf->getId();
                $shelf_no = $rackshelf->getShelfNo();
                $is_filled = $rackshelf->getIsFilled();
                $shelfTitle = $rack_short_title . " " . ($shelf_no + 1);
                $popoverTitle = "Shelf " . $shelfTitle;
                $popoverContent = "<table class='table table-bordered'>";
                $popoverContent .= "<tr><th>Max Weight</th><th>Dimension</th></tr>";
                $popoverContent .= "<tr><td>" . $rack_shelf_max_weight . "</td><td>" . $rack_shelf_dimension . "</td></tr>";
                $popoverContent .= "</table>";
                $currentShelfItem = NULL;
                $log_shelf_id = "";
                $shelfTitleFilled = "";
                if ($is_filled == 1) {
                    $rackShelfItemObj = new LogRackShelfFilter();
                    $currentShelfItem = $rackShelfItemObj->getCurrentShelfItem($shelf_id);
                    $log_shelf_id = $currentShelfItem['log_shelf_id'];
                    $inByObj = new CustomerAccount($currentShelfItem['in_by']);
                    $CustomerObj = new CustomerAccount($currentShelfItem['customer_id']);

                    $shelfTitleFilled = " / " . htmlentities($currentShelfItem['goods_name']);

                    $popoverContent = "<table class='table table-bordered'>";
                    $popoverContent .= "<tr><td><b>In Date</b></td><td>" . $currentShelfItem['in_date'] . "</td></tr>";
                    $popoverContent .= "<tr><td><b>In By User</b></td><td>" . $inByObj->getFullName() . " [" . $inByObj->getAccount() . "]</td></tr>";
                    $popoverContent .= "<tr><td><b>Customer</b></td><td>" . $CustomerObj->getFullName() . " [" . $CustomerObj->getAccount() . "]</td></tr>";
                    $popoverContent .= "<tr><td><b>Weight</b></td><td>" . $currentShelfItem['weight'] . "</td></tr>";
                    $popoverContent .= "<tr><td><b>Dimension</b></td><td>" . $currentShelfItem['dimension'] . "</td></tr>";
                    $popoverContent .= "<tr><td colspan='2'><b>Description</b></td></tr>";
                    $popoverContent .= "<tr><td colspan='2'>" . htmlentities($currentShelfItem['description']) . "</td></tr>";
                    $popoverContent .= "</table>";
                }
                if (fmod($shelf_no, $rack_cols) == 0) {
                    $output .= "</tr><tr>";
                }
                $output .= '<td class="table-' . ($is_filled == 1 ? 'red' : 'green') . ' popovers" data-html="true" data-trigger="hover" data-container="body" data-placement="top" data-content="' . $popoverContent . '" data-original-title="' . $popoverTitle . '" data-is_filled="' . $is_filled . '" data-log_shelf_id="' . $log_shelf_id . '" data-shelf_id="' . $shelf_id . '" data-shelf_title="' . $shelfTitle . '" data-type="' . $is_york . '">';
                $output .= '<button type="button" class="btn btn-fit-height">';
                $output .= $shelfTitle . $shelfTitleFilled;
                $output .= '</button>';
                $output .= '</td>';
            }
            $output .= "</tr>";
        }
        return $output;
    }

    private function getLabelInfo($shelf_id, $type) {
        $rackShelfItemObj = new LogRackShelfFilter();
        $currentShelfItem = $rackShelfItemObj->getShelfLabelInfo($shelf_id);

        $log_shelf_id = $currentShelfItem['log_shelf_id'];

        $shelf_no = $currentShelfItem['shelf_no'];
        $short_title = $currentShelfItem['short_title'];

        $inByObj = new CustomerAccount($currentShelfItem['in_by']);
        $CustomerObj = new CustomerAccount($currentShelfItem['customer_id']);
        $output = array();

        $shelfName = $short_title . " " . ($shelf_no + 1);
        $title = '';
        switch ($type) {
            case 'in':
                $title = 'Rack Location: ' . $shelfName;
                break;
            case 'out':
                $title = 'Rack Location: ' . $shelfName;
                break;
            case 'shift':
                $title = 'Rack Location: ' . $shelfName;
                break;
            case 'edit':
                $title = 'Rack Location: ' . $shelfName;
                break;
        }
        $output['Title'] = $title;
        $output['Barcode_Id'] = str_pad($shelf_id, 10, "0", STR_PAD_LEFT);

        $output['Customer'] = $CustomerObj->getFullName();
        $output['Item'] = $currentShelfItem['goods_name'];
        $output['Weight'] = $currentShelfItem['weight'] . "Kg";
        $output['Dimension'] = $currentShelfItem['dimension'];
        $output['Date'] = $currentShelfItem['in_date'];
        $output['Processed By'] = $inByObj->getFullName();

        return $output;
    }

    /**
     * Returns boolean to indicate if the form is valid
     *
     */
    private function validate_form() {
        // Check that the warehouse name is not blank
        if (trim($this->warehouse_id) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Warehouse cannot be blank.";
        }
        // Check the address line 1
        if (trim($this->title) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Title must be given.";
        }
        if (trim($this->short_title) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Short Title must be given.";
        }
        // Check the stateregion
        if (trim($this->rack_rows) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Levels must be given.";
        }
        if (trim($this->rack_cols) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Shelves must be given.";
        }
        if (trim($this->shelf_length) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Shelf Length must be given.";
        }
        if (trim($this->shelf_width) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Shelf Width must be given.";
        }
        if (trim($this->shelf_height) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Shelf Height must be given.";
        }
        if (trim($this->shelf_max_weight) == "") {
            if ($this->error_msg != "")
                $this->error_msg .= "<br />";
            $this->error_msg .= "Shelf Max Weight must be given.";
        }

        return ($this->error_msg == "");
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
