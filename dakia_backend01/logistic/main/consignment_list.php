<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $consignment_filter = [];
    private $shipment_valid = 0;
    private $record = "";
    private $saccount = "";
    private $filtertype = "";
    private $uaccount = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Consignment List"
        );
        $this->user = SessionManager::getUser();
        /* get url parameters */
        $this->record = (($_GET['record']) != '') ? $_GET['record'] : '';
        $this->saccount = (int) (trim(util_get('saccount')) != '') ? base64_decode(util_get('saccount')) : '';
        $this->filtertype = base64_decode(util_get("filtertype"));
        $this->uaccount = (int) (trim(util_get('uaccount')) != '') ? base64_decode(util_get('uaccount')) : '';
        /* get valid shipnments */
        $conFilter = new ConsignmentFilter();
        $conFilter->addJoin("parcel pc", "pc.consignment_id = c.id");
        if($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
            $conFilter->addFilter("    c.user_id in ( select id from user where user_account_id = '" . $this->user->getUserAccountId() . "') and c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'", "filter");
        } else if($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            $conFilter->addFilter("    c.user_id = '" . $this->user->getId() . "' and c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'", "filter");
        } else if($this->user->getUserType() == User::USER_TYPE_ADMIN) {
            $conFilter->addFilter("    c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'", "filter");
        }
        $conFilter->addFilter("    c.is_customer_manifested <> 1", "filter");
        $conFilter->addGroupBy("    c.id");
        $shipments = $conFilter->getListNew('c.id');
        $this->shipment_valid = count($shipments);
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "consignment_list_ajax") {
            $this->consignment_filter = new ConsignmentFilter();
            /*  required filter check */
            $this->addRequiredFilter();
            /*
             * Column filter
             * For search
             */
            $filterAction = "filter_cancel";
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->addFilters($this->form_vars);
                $filterAction = "filter";
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
                $dataTableColumnName = strtolower($this->form_vars['columns'][$dataTableColumnId]['data']);
                $this->consignment_filter->AddOrderBy($dataTableColumnName, 'order_by', $orderFalse);
            } else {
                $this->consignment_filter->AddOrderBy(strtolower("c.id"), 'order_by', false);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $this->consignment_filter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->consignment_filter->setOffset($iDisplayStart);

            $this->consignment_filter->addGroupBy("c.id");
            $consignmentObjs = $this->consignment_filter->getShipmentPagingListOpt(false);
            $iTotalRecords = $this->consignment_filter->getShipmentPagingListOpt(false, true, false, true);
           // $iTotalRecords = $this->consignment_filter->getShipmentPagingCountNew(false);
            $consignmentDataArr = array();
            foreach ($consignmentObjs as $consignmentObj) {
                if (in_array($consignmentObj->getStatus(), [ Consignment::STATUS_HOLD, Consignment::STATUS_RECYCLED , Consignment::STATUS_READY_TO_PRINT, Consignment::STATUS_INVALID, Consignment::STATUS_LABEL_CREATED]) && $consignmentObj->getIsCustomerManifested() != '1') {
                    $consignmentArr['option'] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="consignments_ids[]"  value="' . $consignmentObj->getId() . '"  class="group-checkable consignmentsIds" /><span></span></label>';
                } else {
                    $consignmentArr['option'] = "&nbsp;";
                }
                $returnIcon = '';
                if ($consignmentObj->getConsignmentType() == 'return') {
                    $returnIcon = '<i class="font-red-mint fa fa-reply"></i>&nbsp;';
                }
                $consignmentArr['created_at'] = $returnIcon.date("d.m.Y", strtotime($consignmentObj->getDateCreated()));
                if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                    $consignmentArr['user_account_id'] = $consignmentObj->getUserAccount();
                }
                $externalUserData = ($this->uaccount > 0) ? "&uaccount=" . base64_encode($this->uaccount) : '';
                $consignmentArr['hawb'] = "";
                $consignmentArr['hawb'] .= '<a href="consignment_add.php?from=client&id=' . $consignmentObj->getId() . $externalUserData . '" id="hawb-' . $consignmentObj->getId() . '">';
                if ($consignmentObj->getReference() !== '') {
                    $consignmentArr['hawb'] .= $consignmentObj->getHawb() . ' / ' . $consignmentObj->getReference();
                } else {
                    $consignmentArr['hawb'] .= $consignmentObj->getHawb();
                }
                $consignmentArr['hawb'] .= '</a>';
                $consignmentArr['contact'] = ucwords(strtolower($consignmentObj->getContact()));
                $consignmentArr['country_id'] = (($consignmentObj->getCity() == "") ? "" : trim(ucwords(strtolower( utf8_encode( $consignmentObj->getCity())))) . " ") . "<br>" . (($consignmentObj->getCountryName() == "") ? "" : trim($consignmentObj->getCountryName()) . " ");
                $consignmentArr['weight'] = (($consignmentObj->getWeight() == "") ? "" : trim($consignmentObj->getWeight()) . " ") . "Kg";
                $status = $consignmentObj->getShipmentStatus();
                $c_status = Consignment::stateText(strtolower(trim($status)));
                if (strtolower($c_status) == "label created") {
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-blue-chambray bg-font-blue-chambray"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_LABEL_CREATED]) . '</span>';
                } else if (strtolower($c_status) == "booked") {
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-blue-dark bg-font-blue-dark"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_DISPATCHED]) . '</span>'; //= Translation::GetCaption("SHIPPED");
                } else if (Consignment::STATUS_RECYCLED == trim($status)) {
                    $consignmentArr['shipment_status'] = '<span class="label label-sm label-danger"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_RECYCLED]) . '</span>';
                } else if (Consignment::STATUS_DELIVERED == trim($status)) {
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-green-jungle bg-font-green-jungle"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_DELIVERED]) . '</span>';
                } else if (Consignment::STATUS_INVALID == trim($status)) {
                    $consignmentArr['shipment_status'] = '<span class="label label-sm label-warning"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) . '</span>';
                } else if (Consignment::STATUS_READY_TO_PRINT == trim($status)) {
                    $consignmentArr['shipment_status'] = '<span class="label label-sm label-info"> ' . Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_READY_TO_PRINT]) . '</span>';
                } else {
                    $consignmentArr['shipment_status'] = '<span class="label label-sm bg-default bg-font-default"> ' . Translation::GetCaption($c_status) . '</span>';
                }
                if (trim($consignmentObj->getProductName()) == '') {
                    $consignmentArr['service_id'] = ucwords(strtolower($consignmentObj->getServiceName()));
                } else {
                    $consignmentArr['service_id'] = ucwords(strtolower($consignmentObj->getProductName()));
                }
                $trackingLink = ($consignmentObj->getReturnAwb() != "") ? $consignmentObj->getAwb() . ' / ' . $consignmentObj->getReturnAwb() : $consignmentObj->getAwb();
                $consignmentArr['awb'] = '<a href="tracking.php?tracking_number=' . trim($consignmentObj->getAwb()) . ' "target="_blank">' . $trackingLink . '</a><br /> <a href="javascript:;" onclick="get_parcel_list(\''. $consignmentObj->getId() . '\',\''.$consignmentObj->getAwb().'\')" class="parcel_lists" data-tid="'.$consignmentObj->getAwb().'">Parcel List</a>';
                $errorString = $consignmentObj->getMessage();
                if (strlen($errorString) > 3) {
                    $errorStringMsg = '<p class="tooltipbutton" data-toggle="tooltip" data-placement="top" title=" ' . $errorString . '">' . substr($errorString, 0, 5) . "..." . "</p>";
                } else {
                    $errorStringMsg = $errorString;
                }
                $consignmentArr['reason'] = $errorStringMsg;
                $consignmentArr['label'] = '';
                if (trim($consignmentObj->getlabelFile()) != '' && file_exists($consignmentObj->getlabelFile())) {
                    if ($user->getFinalMileOverLabel() == "YES") {
                        $label = str_replace("/pdf", "/relabel", $consignmentObj->getlabelFile());
                        $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" href="' . $label . '" target="_blank" ><i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("VIEW_LABEL") . '"> </i> </a>';
                    } else {
                        $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" href="' . $consignmentObj->getlabelFile() . '" target="_blank" ><i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("VIEW_LABEL") . '"> </i></a>';
                    }
                } else if (in_array($consignmentObj->getStatus(), array(Consignment::STATUS_RECEIVED, Consignment::STATUS_PARTIAL_RECEIVED, Consignment::STATUS_DISPATCHED,Consignment::STATUS_PARTIAL_DISPATCHED,Consignment::STATUS_LABEL_CREATED,Consignment::STATUS_INTRANSIT,Consignment::STATUS_DELIVERED, Consignment::STATUS_PARTIAL_DELIVERED )))
                {
                    $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" onclick="return displayLabel(\'' . $consignmentObj->getId() . '\', \'' . $consignmentObj->getId() . '\')" id="generateLabelsLink-' . $consignmentObj->getId() . '" href="javascript:{};" ><i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("VIEW_LABEL") . '"> </i></a>';
                } else if ($consignmentObj->getStatus() == Consignment::STATUS_READY_TO_PRINT) {
                    $consignmentArr['label'] .= '<a style="cursor: pointer; cursor: hand;" onclick="javascript:generateLabels(\'' . $consignmentObj->getId() . '\')" id="generateLabelsLink-' . $consignmentObj->getId() . '"  ><i class="fa fa-file-o" data-toggle="tooltip" data-placement="top" title="' . Translation::GetCaption("GENERATE_LABEL") . '"> </i></a>';
                }
                $consignmentDataArr[] = $consignmentArr;
            }
            $setDataArrJson['data'] = $consignmentDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            $setDataArrJson['filterAction'] = $filterAction;
            echo json_encode($setDataArrJson, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }
        
        if(isset($this->form_vars['action']) && ($this->form_vars['action'] == "get_parcel_detail")) {
            $consignmentId = $this->form_vars['consignment_id'];
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addFieldFilter("consignment_id", $consignmentId);
            $parcelList = $parcelFilter->getList();
            $html = "";
            if(count($parcelList) > 0) {
                foreach($parcelList as $key => $parcel) {
                    $html .= "<tr>";
                            $html .= "<td> " . ($key + 1) . "</td>";
                            $html .= "<td>" . $parcel->getTrackingNumber() . "</td>";
                            $html .= "<td>" . $parcel->getLength()." x ".$parcel->getWidth()." x ".$parcel->getHeight() . "</td>";
                            $html .= "<td>" . $parcel->getWeight() . "</td>";
                            $html .= "<td>" . ucwords(Consignment::$database_status_array[$parcel->getParcelStatusCode()] ). "</td>";
                    $html .= "</tr>";
                }
            } else {
                $html .= "<tr>";
                    $html .= "<td> NO Parcel Found </td>";
                $html .= "</tr>";
            }
            echo $html;
            die;
        }

        if(isset($this->form_vars['form_action']) && ($this->form_vars['form_action'] == "export_data")) {
            $consignmentIds = [];
            if(isset($this->form_vars['consignments_ids']) && count($this->form_vars['consignments_ids']) > 0) {
                $consignmentIds = $this->form_vars['consignments_ids'];
            } else {
                $consignmentIds = $this->getAllFilterConsignments($this->form_vars);
            }
            
            if (count($consignmentIds) > 0) {
                header("Content-Type: application/csv");
                header("Content-Disposition: attachment; filename=exported_consignments.csv");
                $csv = "";
                $csv .= Consignment::exportHeader();
                foreach($consignmentIds as $consignmentId) {
                    $consignment = new Consignment($consignmentId);
                    $csv .= $consignment->exportRow();
                }
                $this->table_msg = formatMessages(SUCCESS_EXPORTED_FILE);
                echo $csv;
                die;
            } else {
                $this->table_msg = formatMessages(ERROR_DATA_EXPORT);
            }
        }
        
        if(isset($this->form_vars['form_action']) && ($this->form_vars['form_action'] == "delete_consignments")) {
            $consignmentIds = [];
            if(isset($this->form_vars['consignments_ids']) && count($this->form_vars['consignments_ids']) > 0) {
                $consignmentIds = $this->form_vars['consignments_ids'];
            }
            $output = [];
            if(count($consignmentIds) > 0) {
                $consignment = new Consignment();
                $consignmentRecycledResponse = $consignment->RecycledShipment($consignmentIds);
                if(isset($consignmentRecycledResponse['status']) && !empty($consignmentRecycledResponse['status'])) {
                    $consignmentRecycledResponse['status'] = strtolower($consignmentRecycledResponse['status']);
                }
                $output = $consignmentRecycledResponse;
            } else {
                $output["status"] = "error";
                $output["message"] = "No shipnment is selected for delete";
            }
            echo json_encode($output);
            die;
        }
        
        if(isset($this->form_vars['form_action']) && ($this->form_vars['form_action'] == "unhold_consignments")) {
            $consignmentIds = [];
            if(isset($this->form_vars['consignments_ids']) && count($this->form_vars['consignments_ids']) > 0) {
                $consignmentIds = $this->form_vars['consignments_ids'];
            }
            $output = [];
            if(count($consignmentIds) > 0) {
                $consignment = new Consignment();
                $consignmentUnholdResponse = $consignment->UnHoldShipment($consignmentIds);
                if(isset($consignmentUnholdResponse['status']) && !empty($consignmentUnholdResponse['status'])) {
                    $consignmentUnholdResponse['status'] = strtolower($consignmentUnholdResponse['status']);
                }
                $output = $consignmentUnholdResponse;
            } else {
                $output["status"] = "error";
                $output["message"] = "No shipnment is selected for unhold";
            }
            echo json_encode($output);
            die;
        }
        
        if(isset($this->form_vars['form_action']) && ($this->form_vars['form_action'] == "hold_consignments")) {
            $consignmentIds = [];
            if(isset($this->form_vars['consignments_ids']) && count($this->form_vars['consignments_ids']) > 0) {
                $consignmentIds = $this->form_vars['consignments_ids'];
            }
            $output = [];
            if(count($consignmentIds) > 0) {
                $consignmentHoldResponse = Consignment::HoldShipment($consignmentIds);
                if(isset($consignmentHoldResponse['status']) && !empty($consignmentHoldResponse['status'])) {
                    $consignmentHoldResponse['status'] = strtolower($consignmentHoldResponse['status']);
                }
                $output = $consignmentHoldResponse;
            } else {
                $output["status"] = "error";
                $output["message"] = "No shipnment is selected for hold";
            }
            echo json_encode($output);
            die;
        }
        
        if(isset($this->form_vars['form_action']) && ($this->form_vars['form_action'] == "restore_consignments")) {
            $consignmentIds = [];
            if(isset($this->form_vars['consignments_ids']) && count($this->form_vars['consignments_ids']) > 0) {
                $consignmentIds = $this->form_vars['consignments_ids'];
            }
            $output = [];
            if(count($consignmentIds) > 0) {
                $consignmentRestoreResponse = Consignment::RestoreShipment($consignmentIds);
                if(isset($consignmentRestoreResponse['status']) && !empty($consignmentRestoreResponse['status'])) {
                    $consignmentRestoreResponse['status'] = strtolower($consignmentRestoreResponse['status']);
                }
                $output = $consignmentRestoreResponse;
            } else {
                $output["status"] = "error";
                $output["message"] = "No shipnment is selected for restore";
            }
            echo json_encode($output);
            die;
        }
        
        if(isset($this->form_vars['form_manual_action']) && ($this->form_vars['form_manual_action'] == "create_manifest_selected")) {
            $consignmentIds = [];
            if(isset($this->form_vars['consignments_ids']) && count($this->form_vars['consignments_ids']) > 0) {
                $consignmentIds = $this->form_vars['consignments_ids'];
            }
            $output = [];
            $parcelDataFilter = new ConsignmentFilter();
            $parcelDataFilter->addJoin("parcel p", "p.consignment_id = c.id");
            $parcelDataFilter->addFilter("      c.id in ('".implode("','", $consignmentIds)."') AND c.awb <> '' ", "filter");
            $parcelDataFilterObjs = $parcelDataFilter->getListNew("p.id as parcel_id");
            if (count($parcelDataFilterObjs) > 0) {
                $parcelIds = [];
                foreach($parcelDataFilterObjs as $parcelDataFilterObj) {
                    $parcelIds[] = $parcelDataFilterObj->getParcelId();
                }
                $createManifestResponse = Manifest::manifestCreate($parcelIds);
                if(isset($createManifestResponse['STATUS']) && !empty($createManifestResponse['STATUS'])) {
                    $createManifestResponse['STATUS'] = strtolower($createManifestResponse['STATUS']);
                    $createManifestResponse['MESSAGE'] = strtolower($createManifestResponse['MESSAGE']);
                }
                $output = $createManifestResponse;
            } else {
                $output["STATUS"] = "error";
                $output["MESSAGE"] = Translation::GetCaption("MANIFEST_HAS_NOT_GENERATED");
            }
            echo json_encode($output);
            die;
        }
        
        if(isset($this->form_vars['form_manual_action']) && ($this->form_vars['form_manual_action'] == "create_manifest_all")) {
            $consignmentIds = $this->getAllFilterConsignments($this->form_vars);
            $output = [];
            $parcelDataFilter = new ConsignmentFilter();
            $parcelDataFilter->addJoin("parcel p", "p.consignment_id = c.id");
            $parcelDataFilter->addFilter("      c.id in ('".implode("','", $consignmentIds)."') AND c.awb <> '' ");
            $parcelDataFilterObjs = $parcelDataFilter->getListNew("p.id as parcel_id");
            if (count($parcelDataFilterObjs) > 0) {
                $parcelIds = [];
                foreach($parcelDataFilterObjs as $parcelDataFilterObj) {
                    $parcelIds[] = $parcelDataFilterObj->getParcelId();
                }
                $createManifestResponse = Manifest::manifestCreate($parcelIds);
                if(isset($createManifestResponse['status']) && !empty($createManifestResponse['status'])) {
                    $createManifestResponse['status'] = strtolower($createManifestResponse['status']);
                }
                $output = $createManifestResponse;
            } else {
                $output["STATUS"] = "error";
                $output["MESSAGE"] = Translation::GetCaption("MANIFEST_HAS_NOT_GENERATED");
            }
            echo json_encode($output);
            die;
        }
    }
    
    public function getAllFilterConsignments($formData) {
        $ids = [];
        $this->consignment_filter = new ConsignmentFilter();
        $this->addFilters($formData);
        $this->consignment_filter->addGroupBy("c.id");
        $consignmentObjs = $this->consignment_filter->getShipmentPagingListOpt(false,false,false);
        if(count($consignmentObjs) > 0) {
            foreach($consignmentObjs as $consignmentObj) {
                $ids[] = $consignmentObj->getId();
            }
        }
        return $ids;
    }
    
    public function addRequiredFilter() {
        if ($this->saccount > 0) {
            if(trim($this->filtertype) != '') {
                $conArrayStatus = array(
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_RECEIVED, Consignment::STATUS_PARTIAL_RECEIVED,
                    Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_PARTIAL_DELIVERED,
                    Consignment::STATUS_CLOSE,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_PROBLEM,
                    Consignment::STATUS_RELABLED,
                    Consignment::STATUS_DISCREPANCY,
                    Consignment::STATUS_AWATING_CLAIM);

                $this->consignment_filter->addFilter("      c.shipment_status in ('" . implode("','", $conArrayStatus) . "')", "consignmentfilter");
            }

            switch (trim($this->filtertype)) {
                case 'sub':
                    $this->consignment_filter->addFilter(" c.date_label_created > 0 ", 'consignmentfilter');
                    $userAccountArry = CustomerAccount::accountSubAccount($this->saccount);
                    $this->consignment_filter->addFilter("     u.user_account_id in ('" . implode("','", $userAccountArry) . "')", 'userfilter');
                    break;
                case 'all':
                    $this->consignment_filter->addFilter("    c.date_label_created > 0 ", 'consignmentfilter');
                    $userAccountArry = CustomerAccount::accountSubAccount($this->saccount, 0, true);
                    $this->consignment_filter->addFilter("     u.user_account_id in ('" . implode("','", $userAccountArry) . "')", 'userfilter');
                    break;
                case 'own':
                    $this->consignment_filter->addFilter("    c.date_label_created > 0 ", 'consignmentfilter');
                    $this->consignment_filter->addFilter('     u.user_account_id ="' . $this->saccount . '"', 'userfilter');
                    break;
                default:
                    $this->consignment_filter->addFilter('    u.user_account_id ="' . $this->saccount . '"', 'userfilter');
                    break;
            }
        } else if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
            $allouedAcccounts[] = $this->user->getUserAccountId();
            $this->consignment_filter->addFilter('     u.user_account_id IN ('.implode(",",$allouedAcccounts).')', 'userfilter');
        } else if($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            $this->consignment_filter->addAccountFilter($this->user->getId());
        } else if ($this->uaccount > 0) {
            $this->consignment_filter->addFilter('     c.user_id ="' . $this->uaccount . '"', 'consignmentfilter');
        }
        
        if ($this->record == "today") {
            $toDayDate = date("Y-m-d");
            $this->consignment_filter->addDateFilter($toDayDate, $toDayDate, 'submitted');
        }
        if ($this->record == "week") {
            $fromDate = date("Y-m-d");
            $toDate = date('Y-m-d', strtotime("+1 week"));
            $this->consignment_filter->addDateFilter($fromDate, $toDate, 'submitted');
        }
    }

    public function addFilters($formData) {
        $this->form_vars = $formData;
        /*
         * Column filter
         * For search
         */
        $searchDateFrom = $this->form_vars['search_Date_from'];
        $searchDateTo = $this->form_vars['search_Date_to'];

        if (!empty($searchDateFrom) || !empty($searchDateTo)) {
            $this->consignment_filter->addDateFilter($searchDateFrom, $searchDateTo, 'submitted');
        }
        $searchAccount = $this->form_vars['user_account_id'];
        if (!empty($searchAccount)) {
            $this->consignment_filter->addFilter("    ua.id ='" . trim($searchAccount) . "'", "userAccountFilter");
        }
        $searchHAWB = $this->form_vars['hawb'];
        if (!empty($searchHAWB)) {
            $this->consignment_filter->addFieldLikeFilter('hawb', trim($searchHAWB));
        }
        $searchName = $this->form_vars['contact'];
        if (!empty($searchName)) {
            $this->consignment_filter->addFieldLikeFilter('contact', trim($searchName));
        }
        $searchCountry = $this->form_vars['country_id'];
        if (!empty($searchCountry)) {
            $this->consignment_filter->addFieldEqualFilter('    country_id', '=', trim($searchCountry));
        }
        $searchWeight = $this->form_vars['weight'];
        if (!empty($searchWeight)) {
            $this->consignment_filter->addFieldLikeFilter('weight', trim($searchWeight));
        }
        $searchStatus = $this->form_vars['shipment_status'];
        if (!empty($searchStatus)) {
            if (trim($searchStatus) == Consignment::STATUS_INTRANSIT) {
                $this->consignment_filter->addFilter("      c.shipment_status in ( '" . Consignment::STATUS_INTRANSIT . "','" . Consignment::STATUS_PARTIAL_RECEIVED . "','" . Consignment::STATUS_RECEIVED . "','" . Consignment::STATUS_PARTIAL_DISPATCHED . "','" . Consignment::STATUS_DISPATCHED . "' )");
            } else if (trim($searchStatus) == Consignment::STATUS_DELIVERED) {
                $this->consignment_filter->addFilter("      c.shipment_status in ( '" . Consignment::STATUS_PARTIAL_DELIVERED . "','" . Consignment::STATUS_DELIVERED . "','" . Consignment::STATUS_CLOSE . "' )");
            } else {
                $this->consignment_filter->addFilter("      c.shipment_status='" . trim($searchStatus) . "'");
            }
        }
        $search_service = $this->form_vars['service_id'];
        if (!empty($search_service)) {
            $service = new Services($search_service);
            $isCustomized = $service->getIsCustomized();
            if ($isCustomized) {
                $this->consignment_filter->addFilter("    c.customized_service_id ='" . trim($search_service) . "'", "consignmentfilter");
            } else {
                $this->consignment_filter->addFilter("    c.service_id ='" . trim($search_service) . "'", "consignmentfilter");
            }
        }
        $searchTracking = $this->form_vars['awb'];
        if (!empty($searchTracking)) {
            $consignmentIds = array();
            $ParcelFilter = new ParcelFilter();
            $ParcelFilter->addFieldFilter("    p.tracking_number", trim($searchTracking));
            $ParcelData = $ParcelFilter->getColumnList("consignment_id");
            foreach ($ParcelData as $con_id) {
                $consignmentIds[] = $con_id->getConsignmentId();
            }
            $queryStr = "";
            if (count($consignmentIds) > 0) {
                $queryStr = " OR c.id IN ('" . implode("','", $consignmentIds) . "')";
            }
            $this->consignment_filter->addFilter("      (c.awb = '" . trim($searchTracking) . "'" . $queryStr . ")");
        }
        
        $manual_filter = $this->form_vars['manual_filter'];
        if($manual_filter == "manifest") {
            $this->consignment_filter->addManifestTableJoin();
            $this->consignment_filter->addFilter("     c.user_id = '" . $this->user->getId() . "'", 'consignmentfilter');
            $this->consignment_filter->addFilter('    c.is_customer_manifested <> 1');
        } else if($manual_filter == "label") {
            $this->consignment_filter->addFilter('    c.is_customer_manifested = 0');
            $this->consignment_filter->addFilter("      c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'");
        }
    }
    
    private function statusToUser() {
        $userType = $this->user->getUserType();
        $status_array = array();

        switch ($userType) {
            case "corporateclient":
                $status_array = array(
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_READY_TO_PRINT,
                    Consignment::STATUS_INVALID,
                    Consignment::STATUS_RECEIVED,
                    Consignment::STATUS_PARTIAL_RECEIVED,
                    Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_CLOSED
                );
                break;
            case "client":
            default:
                $status_array = array(
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_READY_TO_PRINT,
                    Consignment::STATUS_RECEIVED,
                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_INVALID,
                    Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_PROBLEM,
                    Consignment::STATUS_RETURNED
                );
                break;
            case "warehouse":
            case "admin":
            case "finance":
            case "customerservice":
            case "sales":
                $status_array = array(
                    Consignment::STATUS_RECYCLED,
                    Consignment::STATUS_INVALID,
                    Consignment::STATUS_READY_TO_PRINT,
                    Consignment::STATUS_LABEL_CREATED,
                    Consignment::STATUS_RECEIVED,
                    Consignment::STATUS_PARTIAL_RECEIVED,
                    Consignment::STATUS_DISPATCHED,
                    Consignment::STATUS_PARTIAL_DISPATCHED,
                    Consignment::STATUS_INTRANSIT,
                    Consignment::STATUS_HOLD,
                    Consignment::STATUS_PARTIAL_DELIVERED,
                    Consignment::STATUS_DELIVERED,
                    Consignment::STATUS_RETURNED,
                    Consignment::STATUS_CLOSE
                );
                break;
        }
        $this->form_vars["status_list"] = ''; // this line clears the current selected status
        $arrayForUsers = array();
        foreach ($status_array as $key => $status) {
            $c_status = Consignment::stateText($status);
            $arrayForUsers[$status] = Translation::GetCaption($c_status);
        }

        echo Ddl::generateArrayDDL('shipment_status', $arrayForUsers, $this->form_vars["status_list"], Translation::GetCaption("PLEASE_SELECT_STATUS"), 'class="form-control form-filter input-sm select2 searchbox"', "", 'consignment_status', Translation::GetCaption("PLEASE_SELECT_STATUS"));
    }

    /**
     * Page-specific buttons
     */
    
    public function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>
         <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/js-custom-forms/css/theme-minimal/jcf.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        
        <script src="../assets/global/plugins/js-custom-forms/js/jcf.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/js-custom-forms/js/jcf.scrollable.js" type="text/javascript"></script>
        <script src="../js/generate-bulk-labels.js" type="text/javascript"></script>

        <script type="text/javascript">
            var queryStr = "";
            <?php if (isset($_GET['saccount'])) { ?>
                queryStr = "&saccount=<?php echo $_GET['saccount']; ?>";
                <?php if (isset($_GET['filtertype'])) { ?>
                        queryStr += "&filtertype=<?php echo $_GET['filtertype']; ?>";
                <?php }  ?>
            <?php } ?>
            <?php if (isset($_GET['record'])) { ?>
                queryStr = "&record=<?php echo $_GET['record']; ?>";
            <?php } ?>
            <?php if (isset($_GET['uaccount'])) { ?>
                queryStr = "&uaccount=<?php echo $_GET['uaccount']; ?>";
            <?php } ?>    
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid, response) {
                            $('#manage-data-table_wrapper .custom-alerts.alert-danger').hide();
                            if(response.filterAction == "filter_cancel") {
                                $('.actions_btn').hide();
                            }
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
                                "url": "consignment_list.php?action=consignment_list_ajax" + queryStr, // ajax source
                                headers: {

                                }
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", 'sClass':'sorting_1', "bSortable": false},
                                {"data": "created_at", 'sClass':'date-created', "bSortable": false},
                                <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                                    {"data": "user_account_id", 'sClass':'account'},
                                <?php } ?>
                                {"data": "hawb", 'sClass':'hawb'},
                                {"data": "contact", 'sClass':'name'},
                                {"data": "country_id", 'sClass':'country'},
                                {"data": "weight", 'sClass':'weight'},
                                {"data": "shipment_status", 'sClass':'status'},
                                {"data": "service_id", 'sClass':'serviceType'},
                                {"data": "awb", 'sClass':'tracking'},
                                {"data": "reason", 'sClass':'reason', "bSortable": false},
                                {"data": "label", 'sClass':'label-td', "bSortable": false}
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
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
            });
            
            function getParcelList(connsignmentId, trackingNumber) {
                $.ajax({
                    type: "POST",
                    url: "consignment_list.php",
                    data: {
                        consignment_id: connsignmentId,
                        action: "get_parcel_detail"
                    },
                    success: function(data) {
                        $('#parcel_list_data').html("");
                        $('.tracking_number_heading').html("");
                        $('#parcel_list_data').html(data);
                        $('.tracking_number_heading').html(trackingNumber);
                        $('#parcel_list_modal').modal('show');
                    },
                    error: function() {
                        swal("Sorry", "Please try again later", "error");
                    }
                });
            }
            
            function other_option_action() {
                var ids = [];
                $('.consignmentsIds:checked').map(function () {
                    ids.push(this.value);
                }).get();
                var option_value = $('#other_options').val();
                if (option_value == "Export") {
                    $("#form_action").val("export_data");
                    $("#consignment_list_form").submit();
                } else if (option_value == "Delete") {
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                        swal({
                            title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THESE_SHIPMENT?") ?>",
                            text : "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                var form_data = $("#consignment_list_form").serializeArray();
                                form_data.push({name: "form_action", value: 'delete_consignments'});
                                $.ajax({
                                    type: "POST",
                                    url: "consignment_list.php",
                                    data: form_data,
                                    dataType: "json",
                                    success: function (data) {
                                        if(data.status == "success") {
                                            grid.getDataTable().ajax.reload();
                                            swal("Success!",  data.message, "success");
                                        } else {
                                            swal("Sorry!", data.message, "error");
                                        }
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });
                    } else {
                        swal("Sorry!", "<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>", "error");
                    }    
                } else if (option_value == "Unhold") {
                     if (typeof ids !== 'undefined' && ids.length > 0) {
                         swal({
                            title: '<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_UNHOLD_THESE_SHIPMENT") ?>',
                            text : "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                var form_data = $("#consignment_list_form").serializeArray();
                                form_data.push({name: "form_action", value: 'unhold_consignments'});
                                $.ajax({
                                    type: "POST",
                                    url: "consignment_list.php",
                                    data: form_data,
                                    dataType: "json",
                                    success: function (data) {
                                        if(data.status == "success") {
                                            grid.getDataTable().ajax.reload();
                                            swal("Success!",  data.message, "success");
                                        } else {
                                            swal("Sorry!", data.message, "error");
                                        }
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });
                    } else {
                        swal("Sorry!", "<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>", "error");
                    }
                } else if (option_value == "Hold") {
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                         swal({
                            title: '<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_HOLD_THESE_SHIPMENT") ?>',
                            text : "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                var form_data = $("#consignment_list_form").serializeArray();
                                form_data.push({name: "form_action", value: 'hold_consignments'});
                                $.ajax({
                                    type: "POST",
                                    url: "consignment_list.php",
                                    data: form_data,
                                    dataType: "json",
                                    success: function (data) {
                                        if(data.status == "success") {
                                            grid.getDataTable().ajax.reload();
                                            swal("Success!",  data.message, "success");
                                        } else {
                                            swal("Sorry!", data.message, "error");
                                        }
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });
                    } else {
                        swal("Sorry!", "<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>", "error");
                    }
                } else if (option_value == "Restore") {
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                         swal({
                            title: '<?php echo Translation::GetCaption("RESTORE_CONFIRM_MESSAGE") ?>',
                            text : "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                var form_data = $("#consignment_list_form").serializeArray();
                                form_data.push({name: "form_action", value: 'restore_consignments'});
                                $.ajax({
                                    type: "POST",
                                    url: "consignment_list.php",
                                    data: form_data,
                                    dataType: "json",
                                    success: function (data) {
                                        if(data.status == "success") {
                                            grid.getDataTable().ajax.reload();
                                            swal("Success!",  data.message, "success");
                                        } else {
                                            swal("Sorry!", data.message, "error");
                                        }
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });
                    } else {
                        swal("Sorry!", "<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>", "error");
                    }
                }
            }
            
            function generateLabels(consignmentid) {
                $('.loading-' + consignmentid).show();
                $.ajax({
                    url: "ajaxlabel.php",
                    data: {
                        consignmentid: consignmentid,
                        action: 'GENERATELABEL'
                    },
                    type: "POST",
                    dataType: "json",
                    async: true,
                })
                // Code to run if the request succeeds (is done);
                // The response is passed to the function
                .done(function(json) {
                    if (json.status == 'SUCCESS') {
                        var dataJson = json.DATA;
                        $.each(dataJson, function(arrayID, dataArray) {
                            if (dataArray.STATUS == 'SUCCESS') {
                                awbnumber = dataArray.AWB;
                                hawbnumber = dataArray.HAWB;
                                $('#generateLabelsLink-' + consignmentid).attr('onclick', 'displayLabel(\'' + dataArray.ID + '\')');
                                $('#generateLabelsLink-' + consignmentid).html('<i class="fa fa-file-pdf-o" data-toggle="tooltip" data-placement="top" title="<?php echo Translation::GetCaption("VIEW_LABEL");?>"> </i>');
                                $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm bg-blue-chambray bg-font-blue-chambray"><?php echo Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_LABEL_CREATED]) ?></span>');
                                $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.tracking').html('<a href="tracking.php?tracking_number=' + awbnumber + ' "target="_blank">' + awbnumber + '</a>');
                                $("[data-toggle='tooltip']").tooltip();
                            } else {
                                $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.reason').html(dataArray.MESSAGE);
                                $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm label-warning"><?php echo Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) ?></span>');
                            }
                        });
                    } else {
                        $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.reason').html(dataArray.MESSAGE);
                        $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm label-warning"><?php echo Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) ?></span>');
                    }
                    $('.loading-' + consignmentid).hide();
                })
                // Code to run if the request fails; the raw request and
                // status codes are passed to the function
                .fail(function(xhr, status, errorThrown) {
                    $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.reason').html("Error: " + errorThrown);
                    $('#generateLabelsLink-' + consignmentid).closest('tr').find('td.status').html('<span class="label label-sm label-warning"><?php echo Translation::GetCaption(Consignment::$status_array[Consignment::STATUS_INVALID]) ?></span>');
                    $('.loading-' + consignmentid).hide();
                })
                // Code to run regardless of success or failure;
                .always(function(xhr, status) {
                    // alert( "The request is complete!" );
                });
            }
            
            function displayLabel(consignmentid, recordid) {
                $.ajax({
                    url: "ajaxlabel.php",
                    data: {
                        consignmentid: consignmentid,
                        action: 'SHOWLABEL'
                    },
                    type: "POST",
                    dataType: "json",
                    async: false,
                })
                // Code to run if the request succeeds (is done);
                // The response is passed to the function
                .done(function(json) {
                    if (json.STATUS != "ERROR") {
                        var labelLink = json.LABEL;
                        var labels = labelLink.replace("\/", "/");
                        popupwindow(labels, 'Label View', 550, 400);
                    } else {
                        swal("", json.MESSAGE, "info");
                    }
                })
                .fail(function(xhr, status, errorThrown) {
                    swal("", "Please try again later", "info");
                    console.log("Error: " + errorThrown);
                    console.log("Status: " + status);
                    console.dir(xhr);
                });
            }
            function popupwindow(url, title, w, h) {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }
            
            function manual_filter(action) {
                $('.actions_btn').hide();
                if(action == "manifest") {
                    $('#ShowManifest').show();
                    $("#consignment_status").val('<?= Consignment::STATUS_LABEL_CREATED; ?>');
                    $("#consignment_status").change();
                    grid.setAjaxParam("manual_filter", "manifest");
                } else if(action == "label") {
                    $('#ShowLabel').show();
                    $("#consignment_status").val('<?= Consignment::STATUS_READY_TO_PRINT; ?>');
                    $("#consignment_status").change();
                    grid.setAjaxParam("manual_filter", "label");
                }
                grid.submitFilter();
            }
            
            function manual_action(action) {
                var ids = [];
                $('.consignmentsIds:checked').map(function () {
                    ids.push(this.value);
                }).get();
                if(action == "manifest_selected") {
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                        var form_data = $("#consignment_list_form").serializeArray();
                        form_data.push({name: "form_manual_action", value: 'create_manifest_selected'});
                        $.ajax({
                            type: "POST",
                            url: "consignment_list.php",
                            data: form_data,
                            dataType: "json",
                            success: function (data) {
                                if(data.status == "success") {
                                    grid.getDataTable().ajax.reload();
                                    swal("Success!",  data.message, "success");
                                } else {
                                    swal("Sorry!", data.message, "error");
                                }
                            },
                            error: function () {
                                //alert('error handing here');
                            }
                        });
                    } else {
                        swal("Sorry!", "<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>", "error");
                    }
                } else if(action == "manifest_all") {
                    var form_data = $("#consignment_list_form").serializeArray();
                    form_data.push({name: "form_manual_action", value: 'create_manifest_all'});
                    $.ajax({
                        type: "POST",
                        url: "consignment_list.php",
                        data: form_data,
                        dataType: "json",
                        success: function (data) {
                            if(data.status == "success") {
                                grid.getDataTable().ajax.reload();
                                swal("Success!",  data.message, "success");
                            } else {
                                swal("Sorry!", data.message, "error");
                            }
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                } else if(action == "print_selected") {
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                        $.ajax({
                            type: "POST",
                            url: "ajaxlabel.php",
                            data: { action: 'GET_SHIPMENT_WITHID_JSON', shipment_id: ids },
                            dataType: "json",
                            success: function (json) {
                                if (json.STATUS == 'SUCCESS') {
                                    var shipnmentIds = json.DATA;
                                    var valid_shipment = shipnmentIds.length;
                                    if (valid_shipment > 0) {
                                        $("#generate-labels").show();
                                        $('#print-labels-popup').modal('show');
                                        var message = '<?= Translation::GetCaption("BULK_LABEL_MESSAGE_1") ?> ' + valid_shipment + " <?= Translation::GetCaption("BULK_LABEL_MESSAGE_3") ?> <?= Translation::GetCaption("BULK_LABEL_MESSAGE_2") ?>";
                                        swal({
                                            title: message,
                                            text : "",
                                            type: "warning",
                                            showCancelButton: true,
                                            confirmButtonClass: "btn-danger",
                                            confirmButtonText: "Yes",
                                            cancelButtonText: "No",
                                            closeOnConfirm: true,
                                            closeOnCancel: true
                                        },
                                        function(isConfirm) {
                                            if (isConfirm) {
                                                var action = "";
                                                if ((typeof shipnmentIds !== 'undefined' && shipnmentIds.length > 0) || (shipnmentIds != "")) {
                                                    action = 'GET_SHIPMENT_WITHID_JSON_NEW';
                                                    valid_shipment = parseInt(shipnmentIds.length);
                                                } else {
                                                    action = 'GET_SHIPMENT_JSON';
                                                    valid_shipment = parseInt('<?php echo $this->shipmentValid; ?>');
                                                }
                                                var externalUserParam = '';
                                                var externalUser = '<?php echo util_get("uaccount"); ?>';
                                                if ($.trim(externalUser) != '') {
                                                    externalUserParam = "?uaccount=" + externalUser;
                                                }
                                                //send request to generate labels and merge together //all in one request
                                                var exporting = true,xhr;
                                                $("#export-terminal").css({"z-index": 9999, "visibility": "visible"}).fadeIn();
                                                $("#export-terminal-msgs").append("<li>Processing request...</li>");
                                                jcf.replaceAll();
                                                var AJAX_URL = "ajaxgeneratelabels.php" + externalUserParam;
                                                var AJAX_DATA = 'action='+action+'&shipment_id='+shipnmentIds;
                                                return callXHRRequest(AJAX_URL,AJAX_DATA);
                                            }
                                         });
                                    } else {
                                        var message = '<?php echo Translation::GetCaption("LABEL_ERROR_MESSAGE") ?>';
                                        swal('Sorry',message,'error');
                                    }
                                }
                            },
                            error: function () {
                                $(".modal-close").show();
                            }
                        });
                    } else {
                        swal("Sorry!", "<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>", "error");
                    }    
                } else if(action == "print_all") {
                    var valid_shipment = '<?php echo $this->shipment_valid; ?>';
                    var message = '<?php echo Translation::GetCaption("BULK_LABEL_MESSAGE_1") ?> ' + valid_shipment + " <?= Translation::GetCaption("BULK_LABEL_MESSAGE_3") ?> <?= Translation::GetCaption("BULK_LABEL_MESSAGE_2") ?>";
                    swal({
                       title: message,
                       text : "",
                       type: "warning",
                       showCancelButton: true,
                       confirmButtonClass: "btn-danger",
                       confirmButtonText: "Yes",
                       cancelButtonText: "No",
                       closeOnConfirm: true,
                       closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            var action = "";
                            var valid_shipment = "";
                            if ((typeof ids === 'undefined' && ids.length <= 0) || (ids == "")) {
                                action = 'GET_SHIPMENT_WITHID_JSON_NEW';
                                valid_shipment = parseInt(ids.length);
                            } else {
                                action = 'GET_SHIPMENT_JSON';
                                valid_shipment = parseInt('<?= $this->shipmentValid; ?>');
                            }
                            var externalUserParam = '';
                            var externalUser = '<?php echo util_get("uaccount"); ?>';
                            if ($.trim(externalUser) != '') {
                                externalUserParam = "?uaccount=" + externalUser;
                            }
                            //send request to generate labels and merge together //all in one request
                            var exporting = true,xhr;
                            $("#export-terminal").css({"z-index": 9999, "visibility": "visible"}).fadeIn();
                            $("#export-terminal-msgs").append("<li>Processing request...</li>");
                            jcf.replaceAll();
                            var AJAX_URL = "ajaxgeneratelabels.php" + externalUserParam;
                            var AJAX_DATA = 'action='+action+'&shipment_id='+ids;
                            return callXHRRequest(AJAX_URL,AJAX_DATA);
                        }
                    });
                }
            }
            
            function callXHRRequest(ajaxUrl, ajaxData) {
                $("body").css("overflow-y","hidden");
                var wait = document.getElementById("wait");
                if ( wait.innerHTML.length > 3 ) {
                    wait.innerHTML = "";
                } else {
                    wait.innerHTML += ".";
                }
                try {
                    if (window.XMLHttpRequest) {
                        // code for modern browsers
                        xhr=new XMLHttpRequest();
                    } else {
                        // code for old IE browsers
                        xhr=new ActiveXObject("Microsoft.XMLHTTP");
                    }
                    xhr.previous_text = '';
                    xhr.onload = function() {};
                    xhr.onerror = function() {
                        exporting = false;
                    };
                    xhr.onreadystatechange = function(response) {
                        try {
                            if (xhr.readyState > 2) {
                                var new_response = xhr.responseText.substring(xhr.previous_text.length);
                                xhr.previous_text = xhr.responseText;
                                if(new_response.indexOf("http") !== -1){
                                    if(new_response.indexOf("taskCompleted") !== -1){
                                        var errorRes = new_response.replace("abortConsoleExecution", "");
                                        var errorRes = new_response.replace("taskCompleted", "");
                                        $("#export-terminal-msgs").append('<br><li style="list-style: none" class="completed display-block">'+errorRes+"</li>");
                                        $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                        clearInterval(dots);
                                        document.getElementById("wait").innerHTML = "";
                                        setTimeout(function(){
                                            exporting = false;
                                            $("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
                                            $("#export-terminal-msgs").html("");
                                            $("body").css("overflow-y","auto");
                                        },5000);
                                    } else {
                                        exporting = false;
                                        clearInterval(dots);
                                        $("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
                                        $("#export-terminal-msgs").html("");
                                        $("body").css("overflow-y","auto");
                                    }
                                } else if(new_response.indexOf("abortConsoleExecution") !== -1) {
                                    var errorRes = new_response.replace("abortConsoleExecution", "");
                                    $("#export-terminal-msgs").append('<li class="errorconsole">'+errorRes+"</li>");
                                    $("#export-terminal-msgs").append('<li class="errorconsole">'+"Please fix all errors above and try again! "+"</li>");

                                    $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                    clearInterval(dots);
                                    document.getElementById("wait").innerHTML = "";
                                    setTimeout(function(){
                                        exporting = false;
                                        $("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
                                        $("#export-terminal-msgs").html("");
                                        $("body").css("overflow-y","auto");
                                    },5000);
                                } else if(new_response.indexOf("consoleWarning") !== -1) {
                                    var errorRes = new_response.replace("consoleWarning", "");
                                    $("#export-terminal-msgs").append('<li class="errorconsole">'+errorRes+"</li>");
                                } else if(new_response.indexOf("taskCompleted") !== -1) {
                                    var errorRes = new_response.replace("abortConsoleExecution", "");
                                    var errorRes = new_response.replace("taskCompleted", "");
                                    $("#export-terminal-msgs").append('<br><li style="list-style: none" class="completed display-block">'+errorRes+"</li>");
                                    $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                    clearInterval(dots);
                                    document.getElementById("wait").innerHTML = "";
                                    setTimeout(function(){
                                        exporting = false;
                                        $("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
                                        $("#export-terminal-msgs").html("");
                                        $("body").css("overflow-y","auto");
                                    },5000);
                                } else {
                                    if(new_response != "false" && new_response!="" && new_response.indexOf("http") === -1)
                                        var respstring= new_response.trim();
                                    if(respstring){
                                        if(respstring.length > 4){
                                            $("#export-terminal-msgs").append("<li class='display-block'>"+new_response+"</li>");
                                            $("#export-terminal").find('.jcf-scrollable').scrollTop($('#export-terminal-msgs').height());
                                        }
                                    }
                                }
                            }
                            $(".errorconsole").css({"background-color":"red"});
                            $(".completed").css({"background-color":"green"});
                        } catch (e) {
                            console.log("<b>[XHR] Exception: " + e + "</b>");
                        }
                    };
                    xhr.open("POST", ajaxUrl, true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
                    xhr.send(ajaxData);
                } catch (e) {
                    console.log(("<b>[XHR] Exception: " + e + "</b>"));
                }
            }
            
            function close_label_window() {
                $('.export-excel-msg').hide();
            }
            
            function merge_labels() {
                var ids = [];
                $('.consignmentsIds:checked').map(function () {
                    ids.push(this.value);
                }).get();
                var externalUserParam = '';
                var externalUser = '<?php echo util_get("uaccount"); ?>';
                if ($.trim(externalUser) != '') {
                    externalUserParam = "?uaccount=" + externalUser;
                }
                $("#generate-labels-new").hide();
                if ((typeof ids === 'undefined' && ids.length <= 0) || (ids == "")) {
                    swal("Sorry!", "<?php echo Translation::GetCaption("MERGE_LABEL_MESSAGE_1") ?>" , "error");
                    return false;
                } else {
                    $.ajax({
                        url: "ajaxlabel.php" + externalUserParam,
                        data: {
                            action:'GET_LABEL_WITHID_JSON',
                            shipment_id: ids,
                        },
                        type: "POST",
                        dataType : "json",
                    }).done(function(json) {
                        if (json.STATUS == 'SUCCESS') {
                            var shipmentsArray = json.DATA;
                            var labelsLinkArray = shipmentsArray;
                            var valid_shipment = shipmentsArray.length;
                            if (labelsLinkArray.length > 0) {
                                $(".modal-close").hide();
                                $.ajax({
                                    url: "ajaxlabel.php" + externalUserParam,
                                    data: {
                                    labels: labelsLinkArray,
                                        action: 'GENERATELABELMERGE'
                                    },
                                    type: "POST",
                                    dataType : "json",
                                    async: false,
                                })
                                // Code to run if the request succeeds (is done);
                                // The response is passed to the function
                                .done(function(json) {
                                    $('#print-labels-popup').modal('show');
                                    $('#generate-labels').hide();
                                    $('#print-labels-message').html(json.MESSAGE);
                                    $(".modal-close").show();
                                })
                                // Code to run if the request fails; the raw request and
                                // status codes are passed to the function
                                .fail(function(xhr, status, errorThrown) {
                                    // alert( "The request is complete!" );
                                    $(".modal-close").show();
                                })
                                // Code to run regardless of success or failure;
                                .always(function(xhr, status) {
                                    // alert( "The request is complete!" );
                                    $(".modal-close").show();
                                });
                            } else {
                                $('#print-labels-message').html('No label is available to merge');
                            }
                        } else {
                            swal("Status", json.DATA, "info");
                        }
                    })
                    .fail(function(xhr, status, errorThrown) {
                        $(".modal-close").show();
                    })
                    // Code to run regardless of success or failure;
                    .always(function(xhr, status) {
                        // alert( "The request is complete!" );
                    });
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Consignment List
                </div>
                <div class="actions">
                    <a href="consignment_add.php?option=new" class="btn blue">
                        <i class="fa fa-plus"></i> <?php echo Translation::GetCaption("ADD_NEW_SHIPMENT") ?>
                    </a>
                    <a href="import.php" class="btn blue">
                        <i class="fa fa-upload"></i> <?php echo Translation::GetCaption("IMPORT") ?>
                    </a>
                    <a href="show_address.php" class="btn blue">
                        <i class="fa fa-building-o"></i> Manage Address
                    </a>
                    <?php if (Permissions::checkFilePermission('coclient_user_endofday.php')): ?>
                        <a href="coclient_user_endofday.php" class="btn blue">
                            <i class="fa fa-building-o"></i> <?php echo Translation::GetCaption("LIST_OF_ALL_MANIFESTS"); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="export-excel-msg" id="export-terminal">
                            <div class="cancel" title="Close" onclick="close_label_window()">Close</div>
                            <div class="jcf-scrollable">
                                <ul id="export-terminal-msgs"></ul>
                                <span id="wait">.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <fieldset>
                            <ul class="nav nav-pills">
                                <?php if (Permissions::checkFilePermission('manifest')): ?>
                                    <li>
                                        <input class="btn btn-default margin-bottom-5" id="btnManifestShow" name="btnManifestShow" type="button" value="<?php echo Translation::GetCaption("MANIFEST"); ?>" onclick="manual_filter('manifest')" />
                                    </li>
                                <?php endif;?>
                                <?php   if ($_GET["show"] == "hold_parcels") {  ?>
                                    <li>
                                        <input class="btn btn-default margin-bottom-5" id="btnUnHold" name="btnUnHold" type="button" value="<?php echo Translation::GetCaption("UNHOLD"); ?>" />
                                    </li>
                                <?php }
                                    if ($_GET["show"] != "collection_selected") {
                                ?>
                                <?php if (Permissions::checkFilePermission('label')): ?>
                                    <li><input class="btn btn-default margin-bottom-5" id="btnLabelShow" name="btnLabelShow" type="button" value="<?php echo Translation::GetCaption("LABELS"); ?>" <?= ($this->shipment_valid <= 0) ? 'disabled' : '' ?> onclick="manual_filter('label')" /></li>
                                <?php endif; ?>
                                <?php  } else { ?>
                                    <input class="btn btn-default margin-bottom-5" id="btnCollectionSelected" name="btnCollectionSelected" type="button" value="<?php echo Translation::GetCaption('SELECT_SHIPMENT'); ?>"  />
                                    <input class="btn btn-default margin-bottom-5" id="btnCollectionAll" name="btnCollectionAll" type="button" value="<?php echo Translation::GetCaption("ALL_SHIPMENTS"); ?>"  />
                                <?php } ?>
                                <?php if (Permissions::checkFilePermission('merge_existing_label')): ?>
                                    <li class="<?php if ($_SESSION["status"] == array(Consignment::STATUS_LABEL_CREATED, Consignment::STATUS_HOLD)) echo "active"; ?>"><input class="btn btn-default margin-bottom-5" id="btnLabelMerge" name="btnLabelMerge" type="button" value="<?php echo Translation::GetCaption("MERGE_EXISTING_LABEL"); ?>" onclick="merge_labels()" /></li>
                                <?php endif; ?>
                            </ul>
                        </fieldset>
                    </div>
                    <div class="col-md-6 actions_btn" id="ShowLabel" style="display:none;">
                        <fieldset>
                            <ul class="nav nav-pills pull-right">
                            <?php if (Permissions::checkFilePermission('m_s_printed_selected')): ?>
                            <li class="">
                                <input class="btn btn-default margin-bottom-5" id="btnLabelCreate" name="btnLabelCreate" type="button" value="<?php echo Translation::GetCaption("PRINT_SELECTED"); ?>" onclick="manual_action('print_selected')" />
                            </li>
                            <?php endif; ?>
                            <?php if (Permissions::checkFilePermission('m_s_printed_all')): ?>
                                <li class=""><input  id="btnBatchSingleLabel" name="btnBatchSingleLabel" class="btn btn-default margin-bottom-5" type="button" value="<?php echo Translation::GetCaption("PRINT_ALL"); ?>" onclick="manual_action('print_all')" /></li>
                            <?php endif; ?>
                            </ul>
                        </fieldset>
                    </div>
                    <div class="col-md-6 actions_btn" id="ShowManifest" style="display: none;">
                        <fieldset>
                            <ul class="nav nav-pills pull-right">
                            <?php if (Permissions::checkFilePermission('m_s_manife_stselected')): ?>
                                <li>
                                    <input class="btn btn-default margin-bottom-5" id="btnManifestSelected" name="btnManifestSelected" type="button" value="<?php echo Translation::GetCaption("MANIFEST_SELECTEDS"); ?>" onclick="manual_action('manifest_selected')"  />
                                </li>
                            <?php endif; ?>
                            <?php if (Permissions::checkFilePermission('m_s_manife_all')): ?>
                                <li>
                                    <input class="btn btn-default margin-bottom-5" id="btnManifestAll" name="btnManifestAll" type="button"  value="<?php echo Translation::GetCaption("MANIFEST_ALL"); ?>" onclick="manual_action('manifest_all')" />
                                </li>
                            <?php endif; ?>
                            </ul>
                        </fieldset>
                    </div>
                    <div class="col-md-6" id="ShowPickup" style="<?php
                        if (isset($_GET['show']) && $_GET['show'] == 'collection_selected') {
                            echo '';
                        } else {
                            echo 'display:none;';
                        } ?>">
                        <fieldset>
                            <ul class="nav nav-pills">
                                <li>
                                    <input class="btn btn-default margin-bottom-5" id="btnCollectionHistory" name="btnCollectionHistory" type="button" value="<?php echo Translation::GetCaption("COLLECTION_HISTORY"); ?>"  />
                                    <input class="btn btn-default margin-bottom-5" id="btnPutOnHold" name="btnPutOnHold" type="button" value="<?php echo Translation::GetCaption("HOLD"); ?>"  />
                                </li>
                            </ul>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <span> </span>
                                <select class="table-group-action-input form-control input-inline input-small input-sm" id="other_options" name="other_options" style="width:150px !important;">
                                    <option value="">Select Action</option>
                                    <option value="<?php echo Translation::GetCaption("EXPORT"); ?>"><?php echo Translation::GetCaption("EXPORT"); ?></option>
                                    <option value="<?php echo Translation::GetCaption("DELETE"); ?>"><?php echo Translation::GetCaption("DELETE"); ?></option>
                                    <option value="<?php echo Translation::GetCaption("UNHOLD"); ?>"><?php echo Translation::GetCaption("UNHOLD"); ?></option>
                                    <option value="<?php echo Translation::GetCaption("HOLD"); ?>"><?php echo Translation::GetCaption("HOLD"); ?></option>
                                    <option value="<?php echo Translation::GetCaption("RESTORE"); ?>"><?php echo Translation::GetCaption("RESTORE"); ?></option>
                                </select>
                                <button class="btn btn-sm btn-default table-group-action-submit" type="button" onclick="other_option_action()">
                                    <i class="fa fa-check"></i> Submit</button>
                            </div>
                            <form  action="consignment_list.php" method="post" id="consignment_list_form">
                                <input type="hidden" name="form_action" id="form_action" value="" />
                                <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th>
                                                 <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                    <input type='checkbox' name='checkall' class="group-checkable"/>
                                                    <span></span>
                                                </label>
                                            </th>
                                            <th>Created</th>
                                            <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                                            <th>Accounts</th>
                                            <?php } ?>
                                            <th>HAWB</th>
                                            <th>Contact</th>
                                            <th>Country</th>
                                            <th>Weight</th>
                                            <th>Status</th>
                                            <th>Service</th>
                                            <th>Tracking No</th>
                                            <th>Reason</th>
                                            <th>Label</th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td>
                                                <div class="margin-bottom-5">
                                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
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
                                            <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN || $this->user->getUserType() == User::USER_TYPE_CORPORATE) { ?>
                                                <td>
                                                    <?php
                                                    $accountParentId = 0;
                                                    if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                                                        $accountParentId = $this->user->getUserAccountId();
                                                    }
                                                    $selectedAccount = "";
                                                    if (!empty($_GET['account']) && (int) trim($_GET['account']) > 0) {
                                                        $selectedAccount = (int) trim($_GET['account']);
                                                    }
                                                    $allowedLevel = 0;
                                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                                        $allowedLevel = 1;
                                                    }
                                                    echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel);
                                                    ?>
                                                </td>
                                            <?php } ?>
                                            <td>
                                                <input type="text" name="hawb" class="form-control form-filter input-sm" /> 
                                            </td>
                                            <td>
                                                <input type="text" name="contact" class="form-control form-filter input-sm" /> 
                                            </td>
                                            <td>
                                                <?php echo Ddl::generateCountryDDL('country_id', '', 'iso', ' class="form-filter bs-select form-control input-sm" data-live-search="true"'); ?>
                                            </td>
                                            <td>
                                                <input type="text" name="weight" class="form-control form-filter input-sm" /> 
                                            </td>
                                            <td>
                                                <?php $this->statusToUser(); ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $sql = "SELECT
                                                                service_id 'id', name 'name', IF(is_customized = 0, 'service','product' ) AS 'service_type'
                                                            FROM
                                                                user_services_routing usr
                                                                    INNER JOIN
                                                                services s ON usr.service_id = s.id
                                                                    AND user_account_id = '" . $this->user->getUserAccountId() . "'
                                                            GROUP BY service_id ";
                                                    echo Ddl::generateDDLFromSql($sql, 'service_id', 'name', 'id', '', ' class="form-filter form-control input-sm select2 searchbox"', 'Service', '', '', '', array('service_type' => 'service_type'));
                                                ?>
                                            </td>
                                            <td>
                                                <input type="text" name="awb" class="form-control form-filter input-sm" />
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="parcel_list_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Parcel List [ <span class="tracking_number_heading"></span> ]</h4>
                    </div>
                    <div class="modal-body">
                         <div class="table-scrollable">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Tracking Number </th>
                                        <th> Dims </th>
                                        <th> Weight </th>
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <tbody id="parcel_list_data"> 
                                    
                                </tbody>
                            </table>
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
        <div id="hidden_frm" style="display: none;">

        </div>
        <?php
    }
    
    protected function renderFooter() {
        ?>
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

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>