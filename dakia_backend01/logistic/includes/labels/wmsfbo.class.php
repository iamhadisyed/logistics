<?php

include_classes([
    'skuorder.class',
    'skuorderfilter.class',
    'sku.class',
    'skufilter.class',
    'skuboxdetail.class',
    'skuboxdetailfilter.class',
]);

class WMSFBO implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $skuOrder = null;


    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
      
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {
        //print_r($consignment); die;
        $skuOrderFilter = new SkuOrderFilter();
        $skuOrderFilter->addFilter("consignment_id ='".$consignment->getId()."'");
        $skuOrderList = $skuOrderFilter->getList();
        //print_r($skuOrderList); die;
        
        if(count($skuOrderList > 0))
        {
            
            $this->skuOrder = $skuOrderList[0];
            
            $skuOrderId = $this->skuOrder->getId();
            
            $output = array();
            $user = new User($consignment->getUserId());
            $this->userAccount = new CustomerAccount($user->getUserAccountId());

            $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $this->pdf = $pdf;

            // Generate label for each parecel
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
    
            $skuOrder = new SkuOrder($skuOrderId);
            $processCode = $skuOrder->getShipmentReference();
         
            $skuboxDetailFilter = new SkuBoxDetailFilter();
            $skuboxDetailFilter->addFilter("sku_order_id ='".$skuOrder->getId()."'");
            $skuDetailOrder = $skuboxDetailFilter->getList();
            
            if(count($skuDetailOrder) > 0)
            {
                $count = 1;
                foreach($skuDetailOrder as $boxDetail)
                {
                    $this->addWayBill($processCode, $boxDetail, $count);
                    $count++;
                }         
            }
            $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $processCode . ".pdf", "F");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $skuOrderId . ".pdf";
            return $output;
        }        
    }
    
    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {      
    }

    public function sendData($tracking_numbers = array()) {      
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill($processCode, $boxDetail, $count) {
	
        $skuBoxMappingFilter = new SkuBoxMappingFilter();
        $skuBoxMappingFilter->addFilter("sku_box_detail_id = '".$boxDetail->getId()."'");
        $result = $skuBoxMappingFilter->getList('*');
        
        foreach($result as $skuId)
        {
           $sku = new sku($skuId->getSkuId());  
           $skuName[] = $sku->getSku();
        }
        $skuName = implode('|', $skuName);

        $warehouseObj = new warehouse($this->skuOrder->getWarehouseId());
        
        $userAccountName = $this->userAccount->getUserAccount();						
	$this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);
        $page_size = array(100, 150);
        $this->pdf->AddPage("P", $page_size);
        
        $output = array();
        $output["STATUS"] = "SUCCESS";
        $this->pdf->setFont("helvetica", "", 11);

        $style = array(
            'position' => '',
            'align' => 'L',
            'stretch' => false,
            'cellfitalign' => '',
            'border' => false,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'stretchtext' => 1
        );
        
        $this->pdf->line(0,30,100,30);
        $this->pdf->write1DBarcode($this->skuOrder->getShipmentReference(), 'C128', '3', '35', '', 12, 0.35, $style, 'N');
        $this->pdf->write1DBarcode($boxDetail->getBagNumber(), 'C128', '8.5', '85', '', 12, 0.4, $style, 'N');        
        
        $this->pdf->rect(70,35,22,12);
        $this->pdf->line(70,42,92,42);
        $this->pdf->setFont("helvetica", "B", 13);
        $this->pdf->Text(74, 35, "OWE");
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(74, 42.5, "Stock In");
        $this->pdf->Text(13, 109.5, $skuName);
        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(5, 109.5, "SKU:");
        $this->pdf->rect(6,58,85,18);
        $this->pdf->line(6,67,91,67);
        
        $this->pdf->line(25,58,25,76);
        $this->pdf->line(48,58,48,76);
        $this->pdf->line(70,58,70,76);
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(8, 60, "FROM");
        $this->pdf->Text(8, 69, "CUST");
        $this->pdf->Text(25, 69, $userAccountName);

        $this->pdf->Text(55, 60, "TO");
        $this->pdf->Text(75, 60, $warehouseObj->getWarehouseCode());
        $this->pdf->Text(50, 69, "PIECES");
        $this->pdf->Text(75, 69, "1 of " . $count);
        $this->pdf->line(0,108,100,108);
        $this->pdf->line(0,115,100,115);     
    }

    public function recycledShipment($consignment) {

        $parcel_list = $consignment->getParcels();
        /*
         *  Get Service  constants 
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['TOURLINE_API_AGENCY_CODE']) == '' || trim(@$this->constants['TOURLINE_API_CLIENT_CODE']) == '' || trim(@$this->constants['TOURLINE_COLLECTION_USERNAME']) == '' || trim(@$this->constants['TOURLINE_COLLECTION_PASSWORD']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $cancelCollectionMethod = "ManageShippingDelivery";
        foreach ($parcel_list as $parcel) {

            $getCancelRequest = array(
                'AgencyClientCode' => $this->constants['TOURLINE_API_AGENCY_CODE'],
                'ClientCode' => $this->constants['TOURLINE_API_CLIENT_CODE'],
                'ShippingCode' => $parcel->getDoTrackingNumber(),
                'isCancellation' => true
            );
            $getCancelResponse = $this->soapRequest($cancelCollectionMethod, $getCancelRequest, true);
            $consignment->setApiData(print_r($getCancelRequest, true), print_r($getCancelResponse, true), $cancelCollectionMethod);
            if (property_exists($getCancelResponse, 'ErrorCode')) {
                if (isset($getCancelResponse->ErrorCode)) {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "ErrorCode: {$getCancelResponse->ErrorCode} ; ErrorMessage: {$getCancelResponse->ErrorMessage}";
                } else {
                    $output["STATUS"] = "SUCCESS";
                }
            }
        }
        return $output;
    }

    public function setTrackingParams($serviceId, $agentId) {
        $this->trackingServiceId = $serviceId;
        $this->trackingAgentId = $agentId;
    }
}
