<?php

//CREATED BY ALEX 03/07/2014

ob_start();
class BoxLabel extends TCPDF
{
    const BORDER_OFF = 0;
    const BORDER_FRAME = 1;
    const END_POS_NEXT_LINE = 2;
    const END_POS_TO_RIGHT = 0;
    const FOOTER = "Corporate Address: Unit 28 Sheraton Business Centre, 20 Wadsworth Road, Greenford, Middlesex, UB6 7JB";

    private $image_folder;
    private $order_status;
    private $order_number;
    private $your_ref;
    private $bookings_collection_booking_id;
    private $bookings_collection_description;
    private $bookings_collection_time;
    private $insurance;
    private $price;
    private $total;
	public $typeheader;

    private $transaction_id;
    private $transaction_date;
    private $transaction_time;
    private $transaction_total;
    private $consignment_list;
    private $totalShipments=0;
    private $totalWeight=0;
    private $totalNumberOfPieces=0;
   // private $pageNumber=1;
	private $pdf;
	private $firstpager;
	private $font;
	private $service = '';


    private $show_main_header_flag = true;
	
	
	 public function Header() 
    { 
      
    } 

    public function Footer() 
    { 
       
    }
	
	public function SetService($service)
	{
		$this->service = $service;
	}

    /**
     * Generate full pdf
     */
    public static function buildPDFDocuments($box_number, $qty=0,$palletCarrierGroupName = '', $mawb = '', $hub = 'LON',$serviceName="")
    {
        $folder_path = "../_assets/bag_pdf";
        $page_format= array(100,200);
        $PdfObj = new BoxLabel('P','mm',$page_format);
        $PdfObj->SetPrintHeader(false);
        $PdfObj->SetPrintFooter(false);
        $PdfObj->createBookingDocs('', "../images/");
        $PdfObj->AddInformation($box_number,$qty, $palletCarrierGroupName, $mawb, $hub,$serviceName);
        $fileName = uniqid();
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
	$PdfObj->Output("../_assets/bag_pdf/".$fileName.".pdf", "F");
        return SETTING_MAIN_ASSETS . "bag_pdf/".$fileName.".pdf" ;
    }

 
  


    public function setIncludeHeaderFlag ($includeFlag)
    {
        $this->show_main_header_flag = $includeFlag;
    }



    public function AddInformation($box_number, $qty = 0, $palletCarrierGroupName='', $mawb ='', $hub,$serviceName)
    {
        $userLogo = User::getUserCompanyImages();
	$this->style = array('position' => 'C','align' => 'C','stretch' => false);
        $user = SessionManager::getUser();	
        //make sure the label is not going to new page for no reason.
        $this->SetAutoPageBreak(false, 0);	
        // set default font
        $this->SetFont('',$style = '',$size = 9,$this->font,$subset = 'default',$out = true);
        $yobjects = $this->getY();
        $xobjects = $this->getX() ;
        $this->SetXY ($xobjects - 9.75, $yobjects - 9.75);
        
	$this->Image($userLogo,$xobjects - 10,$yobjects - 10,$w = 50,$h = 20,$type = '',$link = '',$align = '',$resize = false,$dpi = 300,$palign = '',$ismask = false,$imgmask = false,$border = 0,$fitbox = false,$hidden = false,$fitonpage = false,$alt = false,$altimgs = array());		
	//$this->text($xobjects + 40,$yobjects,'RETURN YOUR PARCEL',$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
	//$this->write1DBarcode($consignment[0]->getAwb(), 'C128', $xobjects ,$yobjects + 15, 120, 20, 0.5	, $this->style, 'Y');
        $this->SetXY ($xobjects + 9.75, $yobjects);
	$this->MultiCell(80,60,'',$border='1', $align='L', $fill=0, $ln=0,$xobjects+1,$yobjects + 18, $reseth=true, $reseth=0, $ishtml=false, $autopadding=false,$maxh=0);
	
	//$this->text($xobjects,$yobjects + 35,$consignment[0]->getAwb(),$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = 'C',
	//$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
        $this->text($xobjects + 2,$yobjects + 20,'MAWB: ' . $mawb,$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
	$this->text($xobjects + 2,$yobjects + 30,'HUB: ' .$hub,$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
	$this->SetFont('Times','B',11);
        
        if(empty($palletCarrierGroupName) && !empty($serviceName)){
            $this->text($xobjects + 2,$yobjects + 40,'Service: ' . $serviceName,$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
        }else{
            $this->text($xobjects + 2,$yobjects + 40,'Carrier Group: ' . $palletCarrierGroupName,$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
        }
	$this->SetFont('Times','',10);
	if($palletCarrierGroupName != 'Hungary Post Untracked Mail EUR' && $palletCarrierGroupName != 'Hungary Post Untracked Mail International'){
            $this->text($xobjects + 2,$yobjects + 50,'QTY: ' . $qty,$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
        } else {
            $country_arr = array();
            $con_filter = new ConsignmentFilter();
            $country_list = $con_filter->getCountryFromBagNumber($box_number);		
            foreach($country_list as $country) {		
                $country_arr[] = $country->getCountry(); 
            }
            $this->text($xobjects + 2,$yobjects + 50,'Country: ' . implode($country_arr, ", "));	
	}
        
	$this->text($xobjects + 2,$yobjects + 60,'Date/Time: '. date("d-m-Y H:i:s"),$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
	$this->text($xobjects + 2,$yobjects + 70,'PROCESSED BY: '.$user->getUsername(),$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
	$this->write1DBarcode($box_number, 'C128', $xobjects + 10,$yobjects + 110, 120, 20, 0.5	, $this->style, 'Y');

	$this->text($xobjects,$yobjects + 130,$box_number,$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = 'C',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
        
	$this->SetFont('Times','B',18);
	if($palletCarrierGroupName != "") {
            $this->text($xobjects,$yobjects+150, $palletCarrierGroupName,$fstroke = false,$fclip = false,$ffill = true,$border = 0,$ln = 1,$align = 'C',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M',$rtloff = false);
	}
}
    public function createBookingDocs($consignment_list, $image_folder) {
        // get basket       
        $this->consignment_list=$consignment_list;
        $this->image_folder = $image_folder;
        $this->addpage();
    }
}